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

namespace Sylius\AdminMcpServerPlugin\Tool\Province;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_province',
    description: 'get_province(countryCode, provinceCode) → JSON object of a single province. Returns: id, code (e.g. "US-NY"), name, abbreviation, country. provinceCode must include the country prefix (e.g. "US-NY", "PL-MA").',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $countryCode, string $provinceCode): string
    {
        return $this->client->get(sprintf('countries/%s/provinces/%s', $countryCode, $provinceCode));
    }
}
