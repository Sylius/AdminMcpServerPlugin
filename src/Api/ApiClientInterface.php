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

interface ApiClientInterface
{
    /**
     * @param array<string, mixed> $query
     */
    public function get(string $path, array $query = []): string;

    /**
     * @param array<string, mixed> $body
     */
    public function post(string $path, array $body = []): string;

    /**
     * @param array<string, mixed> $body
     */
    public function put(string $path, array $body = []): string;

    /**
     * @param array<string, mixed> $body
     */
    public function patch(string $path, array $body = []): string;

    public function delete(string $path): string;
}
