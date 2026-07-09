<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Session;

final readonly class SessionTokenStorage implements TokenStorageInterface
{
    private const KEY = 'sylius_admin_token';

    public function __construct(
        private CurrentSession $currentSession,
    ) {
    }

    public function get(): ?string
    {
        $token = $this->currentSession->get()?->get(self::KEY);

        return \is_string($token) && '' !== $token ? $token : null;
    }

    public function store(string $token): void
    {
        $this->currentSession->get()?->set(self::KEY, $token);
    }

    public function clear(): void
    {
        $this->currentSession->get()?->forget(self::KEY);
    }
}
