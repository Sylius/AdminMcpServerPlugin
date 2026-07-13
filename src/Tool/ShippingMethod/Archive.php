<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'archive_shipping_method',
    description: 'archive_shipping_method(code) → Archives a Sylius shipping method (soft-delete). The method is hidden from the shop but preserved in the database. Use restore_shipping_method to undo. Returns JSON of the archived method.',
)]
final readonly class Archive
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Shipping method code to archive.
     */
    public function __invoke(string $code): string
    {
        return $this->client->patch(sprintf('shipping-methods/%s/archive', $code), []);
    }
}
