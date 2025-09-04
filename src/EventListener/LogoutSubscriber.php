<?php
//
//namespace App\EventListener;
//
//use Symfony\Component\EventDispatcher\EventSubscriberInterface;
//use Symfony\Component\HttpFoundation\RequestStack;
//use Symfony\Component\Security\Http\Event\LogoutEvent;
//
//class LogoutSubscriber implements EventSubscriberInterface
//{
//    private $requestStack;
//
//    public function __construct(RequestStack $requestStack)
//    {
//        $this->requestStack = $requestStack;
//    }
//
//    public static function getSubscribedEvents()
//    {
//        return [
//            LogoutEvent::class => 'onLogout',
//        ];
//    }
//
//    public function onLogout(LogoutEvent $event)
//    {
//        $request = $this->requestStack->getCurrentRequest();
//
//        if ($request && $request->hasSession()) {
//            $session = $request->getSession();
//
//            // Your logout logic here
//            $session->invalidate();
//
//            // Optional: Add flash message or other cleanup
//            // $session->getFlashBag()->add('success', 'You have been logged out successfully.');
//        }
//    }
//}


// src/EventListener/LogoutSubscriber.php
namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Psr\Log\LoggerInterface;

class LogoutSubscriber implements EventSubscriberInterface
{
    private $requestStack;
    private $logger;

    public function __construct(RequestStack $requestStack, LoggerInterface $logger = null)
    {
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onLogout(LogoutEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request && $request->hasSession()) {
            $session = $request->getSession();

            // Get user info before session is cleared
            $token = $event->getToken();
            $username = $token ? $token->getUserIdentifier() : 'unknown';

            // Add custom logout logic here
            $session->getFlashBag()->add('success', 'You have been logged out successfully.');

            // Clear custom session data
            $this->clearCustomSessionData($session);

            // Log the logout activity
            if ($this->logger) {
                $this->logger->info('User logged out', [
                    'username' => $username,
                    'session_id' => $session->getId(),
                    'session_name' => $session->getName()
                ]);
            }
        }
    }

    private function clearCustomSessionData($session): void
    {
        // Clear any custom session variables you might have set
        $customSessionKeys = [
            'last_activity',
            'user_preferences',
            'cart_items',
            'search_filters',
            // Add any other custom session keys you use
        ];

        foreach ($customSessionKeys as $key) {
            if ($session->has($key)) {
                $session->remove($key);
            }
        }
    }
}
