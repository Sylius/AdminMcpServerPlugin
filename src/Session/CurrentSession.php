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

use Mcp\Server\Session\SessionInterface;

final class CurrentSession
{
    private ?SessionInterface $session = null;

    public function bind(SessionInterface $session): void
    {
        $this->session = $session;
    }

    public function get(): ?SessionInterface
    {
        return $this->session;
    }
}
