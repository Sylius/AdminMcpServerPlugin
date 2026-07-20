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

namespace Sylius\AdminMcpServerPlugin\Tool\Locale;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'delete_locale',
    description: 'delete_locale(code) → empty string on success (HTTP 204). Permanently deletes the Sylius locale with the given code. Cannot delete the base locale used by a channel.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Locale code to delete (e.g. "pl_PL").
     */
    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('locales/%s', $code));
    }
}
