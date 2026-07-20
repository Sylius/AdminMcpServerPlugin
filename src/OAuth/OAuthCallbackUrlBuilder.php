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

final readonly class OAuthCallbackUrlBuilder
{
    public function buildSuccessUrl(string $redirectUri, string $code, string $state): string
    {
        return $redirectUri . '?' . http_build_query(array_filter(
            ['code' => $code, 'state' => $state],
            static fn (string $v): bool => $v !== '',
        ));
    }

    public function buildErrorUrl(string $redirectUri, string $state, string $error, string $description): string
    {
        return $redirectUri . '?' . http_build_query(array_filter(
            ['error' => $error, 'error_description' => $description, 'state' => $state],
            static fn (string $v): bool => $v !== '',
        ));
    }
}
