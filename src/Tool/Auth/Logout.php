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

namespace Sylius\AdminMcpServerPlugin\Tool\Auth;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Session\TokenStorageInterface;

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
