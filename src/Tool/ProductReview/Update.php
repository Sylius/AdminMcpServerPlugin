<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductReview;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_review',
    description: 'update_product_review(id, title, rating, comment, status?) → JSON object of the updated Sylius product review. title, rating and comment are required by the API. status must be: new, accepted, or rejected.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int    $id      Product review ID.
     * @param string $title   Review title (required).
     * @param int    $rating  Rating 1–5 (required).
     * @param string $comment Review comment text (required).
     * @param string $status  Review status: new, accepted, rejected. Default = "new".
     */
    public function __invoke(int $id, string $title, int $rating, string $comment, string $status = 'new'): string
    {
        return $this->client->put(sprintf('product-reviews/%d', $id), [
            'title' => $title,
            'rating' => $rating,
            'comment' => $comment,
            'status' => $status,
        ]);
    }
}
