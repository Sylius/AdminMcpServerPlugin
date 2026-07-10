<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductReview;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_product_review',
    description: 'get_product_review(id) → JSON object of a single Sylius product review. Returns: id, title, rating, comment, author, status, reviewSubject, createdAt, updatedAt.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Product review ID.
     */
    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('product-reviews/%d', $id));
    }
}
