<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductReview;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_review',
    description: 'update_product_review(id, title?, rating?, comment?) → Updates a product review content. Only provided fields are changed; omitted fields keep their current values. Rating must be 1–5 if provided. To approve or reject a review use accept_product_review or reject_product_review instead.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(
        int $id,
        string $title = '',
        ?int $rating = null,
        string $comment = '',
    ): string {
        $existing = json_decode($this->client->get(sprintf('product-reviews/%d', $id)), true);

        return $this->client->put(sprintf('product-reviews/%d', $id), [
            'title'   => $title !== '' ? $title : ($existing['title'] ?? ''),
            'rating'  => $rating ?? ($existing['rating'] ?? 5),
            'comment' => $comment !== '' ? $comment : ($existing['comment'] ?? ''),
        ]);
    }
}
