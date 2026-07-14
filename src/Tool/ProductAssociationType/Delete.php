<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAssociationType;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_product_association_type',
    description: 'delete_product_association_type(code) → empty string on success (HTTP 204). Permanently deletes the Sylius product association type with the given code. Also deletes all product associations of this type.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Product association type code to delete.
     */
    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('product-association-types/%s', $code));
    }
}
