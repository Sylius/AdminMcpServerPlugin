<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Auth;

use Sylius\AdminMcpServerPlugin\Session\TokenStorageInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'logout',
    description: 'logout() → clears the stored Sylius admin token for this session. Administrator tools will require login again afterwards.',
)]
final readonly class Logout
{
    public function __construct(
        private TokenStorageInterface $storage,
    ) {
    }

    public function __invoke(): string
    {
        $this->storage->clear();

        return 'Logged out. The stored admin token has been cleared.';
    }
}
