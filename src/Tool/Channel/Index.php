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
    name: 'list_channels',
    description: 'list_channels(page?, itemsPerPage?) → JSON Hydra collection of Sylius channels. Each channel has: id, code, name, hostname, color, enabled, defaultLocale, baseCurrency, locales, currencies, countries, taxZone, themeName, contactEmail, shippingAddressInCheckoutRequired.',
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
        return $this->client->get('channels', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}
