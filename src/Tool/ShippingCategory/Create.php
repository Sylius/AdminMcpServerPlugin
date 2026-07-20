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

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingCategory;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_shipping_category',
    description: 'create_shipping_category(code, name, description?) → JSON object of the newly created Sylius shipping category. Shipping categories let you restrict which shipping methods apply to specific products (assign the category to both the product variant and the shipping method).',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code, string $name, string $description = ''): string
    {
        $body = ['code' => $code, 'name' => $name];
        if ($description !== '') {
            $body['description'] = $description;
        }

        return $this->client->post('shipping-categories', $body);
    }
}
