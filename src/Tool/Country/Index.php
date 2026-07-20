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
    name: 'list_countries',
    description: 'list_countries(page?, itemsPerPage?) → Lists all countries registered in the store (both enabled and disabled). Each has: code (2-letter ISO, e.g. "US", "DE", "PL"), name (full country name), enabled, provinces (list of province IRIs — use list_provinces(countryCode) to see them). Use create_country to add a new country.',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $page         Page number (1-based). Default = 1.
     * @param int $itemsPerPage Items per page. Default = 30.
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30): string
    {
        return $this->client->get('countries', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}
