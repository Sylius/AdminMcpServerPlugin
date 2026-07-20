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
use Mcp\Exception\ToolCallException;
use Sylius\AdminMcpServerPlugin\Api\AuthenticatorInterface;
use Sylius\AdminMcpServerPlugin\Exception\AuthenticationFailedException;
use Sylius\AdminMcpServerPlugin\Session\TokenStorageInterface;

#[McpTool(
    name: 'login',
    description: 'login(email, password) → authenticates against the Sylius admin API and stores the token for this session. Call this before using any administrator tool.',
)]
final readonly class Login
{
    public function __construct(
        private AuthenticatorInterface $authenticator,
        private TokenStorageInterface $storage,
    ) {
    }

    /**
     * @param string $email    Sylius admin email address.
     * @param string $password Sylius admin password.
     */
    public function __invoke(string $email, string $password): string
    {
        try {
            $token = $this->authenticator->requestToken($email, $password);
        } catch (AuthenticationFailedException $e) {
            throw new ToolCallException($e->getMessage(), 0, $e);
        }

        $this->storage->store($token);

        return sprintf('Logged in as %s. The token is stored for this session.', $email);
    }
}
