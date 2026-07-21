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

namespace Sylius\AdminMcpServerPlugin\Security\OAuth;

final readonly class RedirectUriValidator
{
    public function isValid(mixed $uri): bool
    {
        return \is_string($uri) && (
            str_starts_with($uri, 'https://') ||
            str_starts_with($uri, 'http://localhost') ||
            str_starts_with($uri, 'http://127.0.0.1')
        );
    }
}
