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

namespace Sylius\AdminMcpServerPlugin\OAuth;

use League\OAuth2\Server\Entities\UserEntityInterface;

final readonly class UserEntity implements UserEntityInterface
{
    public function __construct(private string $email)
    {
    }

    public function getIdentifier(): string
    {
        return $this->email;
    }
}
