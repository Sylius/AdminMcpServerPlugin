<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Api;

use Sylius\AdminMcpServerPlugin\Exception\AuthenticationFailedException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class HttpAuthenticator implements AuthenticatorInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    public function requestToken(string $email, string $password): string
    {
        try {
            $data = $this->httpClient->request('POST', 'administrators/token', [
                'json' => [
                    'email' => $email,
                    'password' => $password,
                ],
            ])->toArray();
        } catch (HttpClientExceptionInterface $e) {
            throw new AuthenticationFailedException(
                'Authentication failed. Verify the email/password and that the Sylius admin API is reachable.',
                0,
                $e,
            );
        }

        $token = $data['token'] ?? null;
        if (!\is_string($token) || '' === $token) {
            throw new AuthenticationFailedException('Authentication succeeded but the admin API returned no token.');
        }

        return $token;
    }
}
