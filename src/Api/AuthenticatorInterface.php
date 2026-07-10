<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Api;

use Sylius\AdminMcpServerPlugin\Exception\AuthenticationFailedException;

interface AuthenticatorInterface
{
    /**
     * @throws AuthenticationFailedException
     */
    public function requestToken(string $email, string $password): string;
}
