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

namespace Sylius\AdminMcpServerPlugin\Security;

final class AdminUserRole
{
    public const ADMINISTRATION_ACCESS = 'ROLE_ADMINISTRATION_ACCESS';

    public const API_ACCESS = 'ROLE_API_ACCESS';

    private function __construct()
    {
    }
}
