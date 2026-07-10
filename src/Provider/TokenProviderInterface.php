<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Provider;

use Sylius\AdminMcpServerPlugin\Exception\AuthenticationFailedException;
use Sylius\AdminMcpServerPlugin\Exception\NotAuthenticatedException;

interface TokenProviderInterface
{
    /**
     * @throws NotAuthenticatedException
     * @throws AuthenticationFailedException
     */
    public function getToken(bool $forceRefresh = false): string;
}
