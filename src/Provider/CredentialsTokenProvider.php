<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Provider;

use Acme\SyliusExamplePlugin\Api\AuthenticatorInterface;
use Acme\SyliusExamplePlugin\Session\TokenStorageInterface;

final readonly class CredentialsTokenProvider implements TokenProviderInterface
{
    public function __construct(
        private TokenStorageInterface $storage,
        private AuthenticatorInterface $authenticator,
        private string $email,
        private string $password,
    ) {
    }

    public function getToken(bool $forceRefresh = false): string
    {
        if (!$forceRefresh) {
            $token = $this->storage->get();
            if (null !== $token) {
                return $token;
            }
        }

        $token = $this->authenticator->requestToken($this->email, $this->password);
        $this->storage->store($token);

        return $token;
    }
}
