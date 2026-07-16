<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Provider;

use Sylius\AdminMcpServerPlugin\Exception\NotAuthenticatedException;

interface TokenProviderInterface
{
    /**
     * @throws NotAuthenticatedException
     */
    public function getToken(): string;
}
