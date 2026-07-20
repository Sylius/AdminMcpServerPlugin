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
    name: 'create_province',
    description: 'create_province(countryCode, code, name, abbreviation?) → JSON of the newly created province. code must be exactly XX-XX format: 2 uppercase country letters + dash + 2 uppercase region letters (e.g. "US-CA" for California, "CA-ON" for Ontario, "PL-MZ" for Mazovia). countryCode is the 2-letter ISO code (e.g. "US").',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $countryCode  2-letter ISO country code (e.g. "US").
     * @param string $code         Full province code with country prefix (e.g. "US-CA").
     * @param string $name         Province full name (e.g. "California").
     * @param string $abbreviation Optional short abbreviation (e.g. "CA"). Default = "".
     */
    public function __invoke(
        string $countryCode,
        string $code,
        string $name,
        string $abbreviation = '',
    ): string {
        $country = json_decode($this->client->get(sprintf('countries/%s', $countryCode)), true);
        $provinces = $country['provinces'] ?? [];

        $newProvince = ['code' => $code, 'name' => $name];
        if ($abbreviation !== '') {
            $newProvince['abbreviation'] = $abbreviation;
        }
        $provinces[] = $newProvince;

        $this->client->put(sprintf('countries/%s', $countryCode), ['provinces' => $provinces]);

        return $this->client->get(sprintf('countries/%s/provinces/%s', $countryCode, $code));
    }
}
