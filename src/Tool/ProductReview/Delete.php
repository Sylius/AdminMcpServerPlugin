<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductReview;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_product_review',
    description: 'delete_product_review(id) → Deletes the Sylius product review with the given ID. Returns empty response on success (HTTP 204).',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Product review ID to delete.
     */
    public function __invoke(int $id): string
    {
        return $this->client->delete(sprintf('product-reviews/%d', $id));
    }
}
