<?php

namespace App\Security;

use App\Entity\ApiKey;
use App\Entity\ApiSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function supports(Request $request): ?bool
    {
        $path = $request->getPathInfo();

        // Skip authentication for public routes
        if (in_array($path, ['/api/register', '/api/login'])) {
            return false;
        }

        return $request->headers->has('X-API-KEY')
            || $request->headers->has('Authorization')
            || ($request->headers->has('X-CLIENT-ID') && $request->headers->has('X-SECRET-KEY'));
    }

    public function authenticate(Request $request): Passport
    {
        /** METHOD 1 — API Key only **/
        $apiKeyHeader = $request->headers->get('X-API-KEY');
        if ($apiKeyHeader) {
            $apiKey = $this->em->getRepository(ApiKey::class)
                ->findOneBy(['secretKey' => $apiKeyHeader, 'isActive' => true]);

            if (!$apiKey) {
                throw new CustomUserMessageAuthenticationException('Invalid API key.');
            }

            return new SelfValidatingPassport(new UserBadge($apiKey->getUser()->getUserIdentifier()));
        }

        /** METHOD 2 — Bearer Token **/
        $authHeader = $request->headers->get('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
            $session = $this->em->getRepository(ApiSession::class)
                ->findOneBy(['token' => $token, 'isActive' => true]);

            if (!$session) {
                throw new CustomUserMessageAuthenticationException('Invalid session token.');
            }

            if (new \DateTime() > $session->getExpiresAt()) {
                $session->setIsActive(false);
                $this->em->flush();
                throw new CustomUserMessageAuthenticationException('Session token expired.');
            }

            return new SelfValidatingPassport(new UserBadge($session->getUser()->getUserIdentifier()));
        }

        /** METHOD 3 — Client ID + Secret Key **/
        $clientId = $request->headers->get('X-CLIENT-ID');
        $secretKey = $request->headers->get('X-SECRET-KEY');

        if ($clientId && $secretKey) {
            $apiKeyEntity = $this->em->getRepository(ApiKey::class)
                ->findOneBy(['clientId' => $clientId, 'secretKey' => $secretKey, 'isActive' => true]);

            if (!$apiKeyEntity) {
                throw new CustomUserMessageAuthenticationException('Invalid client credentials.');
            }

            return new SelfValidatingPassport(new UserBadge($apiKeyEntity->getUser()->getUserIdentifier()));
        }

        throw new CustomUserMessageAuthenticationException('No authentication provided.');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // Allow the request to continue
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            ['error' => $exception->getMessage()],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
