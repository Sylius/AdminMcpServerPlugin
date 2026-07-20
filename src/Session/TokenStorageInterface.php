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

namespace Sylius\AdminMcpServerPlugin\Session;

interface TokenStorageInterface
{
    public function get(): ?string;

    public function store(string $token): void;

    public function clear(): void;
}
