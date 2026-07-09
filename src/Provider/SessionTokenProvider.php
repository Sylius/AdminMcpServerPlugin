<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Provider;

use Acme\SyliusExamplePlugin\Exception\NotAuthenticatedException;
use Acme\SyliusExamplePlugin\Session\TokenStorageInterface;

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
