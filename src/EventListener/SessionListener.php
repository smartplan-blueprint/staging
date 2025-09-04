<?php
//
//// src/EventListener/SessionListener.php
//namespace App\EventListener;
//
//use Symfony\Component\EventDispatcher\EventSubscriberInterface;
//use Symfony\Component\HttpKernel\Event\RequestEvent;
//use Symfony\Component\HttpKernel\KernelEvents;
//use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
//use Symfony\Component\HttpFoundation\RequestStack;
//
//class SessionListener implements EventSubscriberInterface
//{
//    private $tokenStorage;
//    private $requestStack;
//
//    public function __construct(TokenStorageInterface $tokenStorage, RequestStack $requestStack)
//    {
//        $this->tokenStorage = $tokenStorage;
//        $this->requestStack = $requestStack;
//    }
//
//    public static function getSubscribedEvents(): array
//    {
//        return [
//            KernelEvents::REQUEST => ['onKernelRequest', 8],
//        ];
//    }
//
//    public function onKernelRequest(RequestEvent $event): void
//    {
//        if (!$event->isMainRequest()) {
//            return;
//        }
//
//        $request = $this->requestStack->getCurrentRequest();
//        if (!$request || !$request->hasSession()) {
//            return;
//        }
//
//        $session = $request->getSession();
//        $token = $this->tokenStorage->getToken();
//
//        if ($token && is_object($user = $token->getUser())) {
//            $sessionUserId = $session->get('_security_user_id');
//
//            // Check if session user ID doesn't match current user ID
//            if ($sessionUserId && $sessionUserId !== $user->getId()) {
//                // Session mixing detected - destroy and restart
//                $session->invalidate();
//                $session->start();
//                $session->set('_security_user_id', $user->getId());
//
//                // Optional: add flash message
//                $session->getFlashBag()->add('warning', 'Session was reset due to security reasons.');
//            } else {
//                // Store current user ID in session for verification
//                $session->set('_security_user_id', $user->getId());
//            }
//        }
//    }
//}

// src/EventListener/SessionListener.php
namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionListener implements EventSubscriberInterface
{
    private $tokenStorage;
    private $requestStack;
    private $logger;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        LoggerInterface $logger = null
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 8],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (!$request || !$request->hasSession()) {
            return;
        }

        $session = $request->getSession();
        $token = $this->tokenStorage->getToken();

        // Ensure session is started only when needed
        if (!$session->isStarted() && $this->shouldStartSession($request)) {
            $session->start();
        }

        if ($session->isStarted()) {
            // Update last activity timestamp
            $session->set('last_activity', time());

            // Check for session fixation/mixing
            $this->validateSession($session, $token);
        }
    }

    private function shouldStartSession($request): bool
    {
        // Only start session for authenticated users or specific routes
        $route = $request->attributes->get('_route');
        $publicRoutes = ['app_login', 'app_register', 'public_page'];

        return !in_array($route, $publicRoutes, true) ||
            $this->tokenStorage->getToken() !== null;
    }

    private function validateSession(SessionInterface $session, $token): void
    {
        $sessionId = $session->getId();

        if ($token && is_object($user = $token->getUser())) {
            $currentUserId = $user->getId();
            $sessionUserId = $session->get('_security_user_id');
            $sessionUserHash = $session->get('_security_user_hash');

            // Create a hash of user ID + session ID for stronger validation
            $expectedHash = hash('sha256', $currentUserId . $sessionId);

            // If session has user data but doesn't match current user
            if ($sessionUserId && $sessionUserId !== $currentUserId) {
                $this->handleSessionMixing($session, $currentUserId, $sessionId, $expectedHash);
                return;
            }

            // If hash doesn't match (session fixation attempt)
            if ($sessionUserHash && $sessionUserHash !== $expectedHash) {
                $this->handleSessionFixation($session, $currentUserId, $sessionId, $expectedHash);
                return;
            }

            // Store validation data in session
            $session->set('_security_user_id', $currentUserId);
            $session->set('_security_user_hash', $expectedHash);
            $session->set('_security_session_id', $sessionId);

        } elseif ($session->has('_security_user_id')) {
            // User is not authenticated but session has user data - clear it
            $this->clearStaleSessionData($session);
        }
    }

    private function handleSessionMixing(SessionInterface $session, $currentUserId, $sessionId, $expectedHash): void
    {
        if ($this->logger) {
            $this->logger->warning('Session mixing detected', [
                'current_user_id' => $currentUserId,
                'session_user_id' => $session->get('_security_user_id'),
                'session_id' => $sessionId
            ]);
        }

        // Regenerate session ID and clear old data
        $session->migrate(true);
        $session->clear();

        // Set new session data
        $session->set('_security_user_id', $currentUserId);
        $session->set('_security_user_hash', $expectedHash);
        $session->set('_security_session_id', $session->getId());

        // Add flash message
        $session->getFlashBag()->add('warning', 'Your session was reset for security reasons.');
    }

    private function handleSessionFixation(SessionInterface $session, $currentUserId, $sessionId, $expectedHash): void
    {
        if ($this->logger) {
            $this->logger->alert('Session fixation attempt detected', [
                'current_user_id' => $currentUserId,
                'session_id' => $sessionId
            ]);
        }

        // Complete session regeneration
        $session->invalidate();
        $session->start();

        $session->set('_security_user_id', $currentUserId);
        $session->set('_security_user_hash', $expectedHash);
        $session->set('_security_session_id', $session->getId());

        $session->getFlashBag()->add('error', 'Security issue detected. Please log in again.');
    }

    private function clearStaleSessionData(SessionInterface $session): void
    {
        $securityKeys = [
            '_security_user_id',
            '_security_user_hash',
            '_security_session_id',
            '_security_last_username',
        ];

        foreach ($securityKeys as $key) {
            if ($session->has($key)) {
                $session->remove($key);
            }
        }
    }
}
