<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Provider;

use Acme\SyliusExamplePlugin\Exception\AuthenticationFailedException;
use Acme\SyliusExamplePlugin\Exception\NotAuthenticatedException;

interface TokenProviderInterface
{
    /**
     * @throws NotAuthenticatedException
     * @throws AuthenticationFailedException
     */
    public function getToken(bool $forceRefresh = false): string;
}
