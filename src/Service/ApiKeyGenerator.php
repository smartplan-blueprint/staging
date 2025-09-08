<?php

namespace App\Service;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class ApiKeyGenerator
{
    public function generateApiKey(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function validateApiKey(?string $apiKey): bool
    {
        if (null === $apiKey) {
            throw new CustomUserMessageAuthenticationException('API key is required');
        }

        if (!preg_match('/^[a-f0-9]{64}$/', $apiKey)) {
            throw new CustomUserMessageAuthenticationException('Invalid API key format');
        }

        return true;
    }
}