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
    name: 'update_shipping_category',
    description: <<<'DESC'
update_shipping_category(code, body) → JSON of the updated shipping category.

IMPORTANT: First call get_shipping_category to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body.
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code, string $body): string
    {
        return $this->client->put(sprintf('shipping-categories/%s', $code), json_decode($body, true) ?? []);
    }
}
