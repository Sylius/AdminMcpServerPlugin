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
