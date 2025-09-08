<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ApiKey;
use App\Entity\ApiSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'])) {
            return $this->json(['error' => 'Email and password required'], 400);
        }

        if ($em->getRepository(User::class)->findOneBy(['email' => $data['email']])) {
            return $this->json(['error' => 'User already exists'], 400);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($hasher->hashPassword($user, $data['password']));
        $em->persist($user);
        $em->flush();

        // Create API key
        $apiKey = new ApiKey();
        $apiKey->setUser($user);
        $apiKey->setClientId('client_'.$user->getId());
        $apiKey->setSecretKey(bin2hex(random_bytes(20)));
        $apiKey->setIsActive(true);
        $em->persist($apiKey);
        $em->flush();

        return $this->json([
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'client_id' => $apiKey->getClientId(),
            'api_key' => $apiKey->getSecretKey()
        ], 201);
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'])) {
            return $this->json(['error'=>'Email and password required'],400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email'=>$data['email']]);
        if (!$user || !$hasher->isPasswordValid($user,$data['password'])) {
            return $this->json(['error'=>'Invalid credentials'],401);
        }

        $token = bin2hex(random_bytes(16));
        $expiresAt = (new \DateTimeImmutable())->modify('+5 minutes');

        $session = new ApiSession();
        $session->setUser($user);
        $session->setToken($token);
        $session->setExpiresAt($expiresAt);
        $session->setIsActive(true);
        $em->persist($session);
        $em->flush();

        return $this->json([
            'user_id'=>$user->getId(),
            'email'=>$user->getEmail(),
            'token'=>$token,
            'expires_at'=>$expiresAt->format('Y-m-d H:i:s')
        ]);
    }

   #[Route('/api/dashboard', name: 'api_dashboard', methods: ['GET'])]
public function dashboard(Request $request, EntityManagerInterface $em): JsonResponse
{
    $user = null;

    // --- 1. Check Authorization header ---
    $authHeader = $request->headers->get('Authorization');
    if ($authHeader) {
        // Bearer token
        if (str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
            $session = $em->getRepository(ApiSession::class)->findOneBy([
                'token' => $token,
                'isActive' => true
            ]);

            if ($session && new \DateTimeImmutable() <= $session->getExpiresAt()) {
                $user = $session->getUser();
            } else {
                if ($session) { $session->setIsActive(false); $em->flush(); }
                return $this->json(['error' => 'Token expired'], 401);
            }
        }

        // API key in Authorization header
        elseif (str_starts_with($authHeader, 'Client ')) {
            $clientData = substr($authHeader, 7);
            if (str_contains($clientData, ':')) {
                [$clientId, $secretKey] = explode(':', $clientData, 2);
                $apiKey = $em->getRepository(ApiKey::class)->findOneBy([
                    'clientId' => $clientId,
                    'isActive' => true
                ]);

                if ($apiKey && hash_equals($apiKey->getSecretKey(), $secretKey)) {
                    $user = $apiKey->getUser();
                } else {
                    return $this->json(['error' => 'Invalid API key'], 401);
                }
            } else {
                return $this->json(['error' => 'Invalid API key format'], 400);
            }
        }
    }

    // --- 2. Check X-CLIENT-ID / X-SECRET-KEY headers as fallback ---
    if (!$user) {
        $clientId = $request->headers->get('X-CLIENT-ID');
        $secretKey = $request->headers->get('X-SECRET-KEY');

        if ($clientId && $secretKey) {
            $apiKey = $em->getRepository(ApiKey::class)->findOneBy([
                'clientId' => $clientId,
                'isActive' => true
            ]);

            if ($apiKey && hash_equals($apiKey->getSecretKey(), $secretKey)) {
                $user = $apiKey->getUser();
            } else {
                return $this->json(['error' => 'Invalid client credentials.'], 401);
            }
        }
    }

    if (!$user) {
        return $this->json(['error' => 'No authentication provided.'], 401);
    }

    // Mask API key for display
    $apiKey = $em->getRepository(ApiKey::class)->findOneBy(['user' => $user]);
    $maskedKey = null;
    if ($apiKey) {
        $key = $apiKey->getSecretKey();
        $maskedKey = substr($key, 0, 4) . str_repeat('*', strlen($key)-8) . substr($key, -4);
    }

    return $this->json([
        'message' => 'Welcome to your dashboard',
        'email' => $user->getEmail(),
        'client_id' => $apiKey?->getClientId(),
        'api_key' => $maskedKey,
        'roles' => $user->getRoles()
    ]);

}

}