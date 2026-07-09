<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Provider;

use Sylius\AdminMcpServerPlugin\Exception\NotAuthenticatedException;
use Sylius\AdminMcpServerPlugin\Session\TokenStorageInterface;

final readonly class SessionTokenProvider implements TokenProviderInterface
{
    public function __construct(
        private TokenStorageInterface $storage,
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

        throw new NotAuthenticatedException(
            'Not authenticated. Use the "login" tool with a Sylius admin email and password first.',
        );
    }
}
