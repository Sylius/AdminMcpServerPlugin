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

namespace Sylius\AdminMcpServerPlugin\Api;

use Sylius\AdminMcpServerPlugin\Exception\AuthenticationFailedException;

interface AuthenticatorInterface
{
    /**
     * @throws AuthenticationFailedException
     */
    public function requestToken(string $email, string $password): string;
}
