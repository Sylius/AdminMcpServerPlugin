<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Provider;

use Sylius\AdminMcpServerPlugin\Api\AuthenticatorInterface;
use Sylius\AdminMcpServerPlugin\Session\TokenStorageInterface;

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
