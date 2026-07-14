<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingCategory;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_shipping_category',
    description: 'delete_shipping_category(code) → Permanently deletes a shipping category. Returns empty response on success (204). NOTE: Do not delete a category that is still assigned to shipping methods — unassign it first.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('shipping-categories/%s', $code));
    }
}
