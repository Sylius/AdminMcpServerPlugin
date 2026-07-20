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

namespace Sylius\AdminMcpServerPlugin\Tool\Channel;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_channel',
    description: 'get_channel(code) → JSON object of a single Sylius channel. Returns: id, code, name, hostname, color, enabled, defaultLocale, baseCurrency, locales, currencies, countries, taxZone, themeName, contactEmail.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Channel code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('channels/%s', $code));
    }
}
