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

namespace Sylius\AdminMcpServerPlugin\Tool\Country;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_country',
    description: 'create_country(code, enabled?) → Adds a country to the store. code must be ISO 3166-1 alpha-2 (e.g. "US", "PL", "DE", "FR"). Returns JSON with the country name. Note: countries cannot be deleted via API — use update_country(code, enabled=false) to hide one instead.',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code    ISO 3166-1 alpha-2 country code (e.g. "PL", "DE").
     * @param bool   $enabled Whether the country is enabled. Default = true.
     */
    public function __invoke(string $code, bool $enabled = true): string
    {
        return $this->client->post('countries', [
            'code' => $code,
            'enabled' => $enabled,
        ]);
    }
}
