<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Session;

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
