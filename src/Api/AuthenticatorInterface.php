<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Api;

use Acme\SyliusExamplePlugin\Exception\AuthenticationFailedException;

interface AuthenticatorInterface
{
    /**
     * @throws AuthenticationFailedException
     */
    public function requestToken(string $email, string $password): string;
}
