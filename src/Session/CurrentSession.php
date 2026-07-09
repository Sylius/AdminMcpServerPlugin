<?php

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
