<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductReview;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_review',
    description: <<<'DESC'
update_product_review(id, body) → JSON of the updated product review. Only fields in body are changed. To approve or reject a review use accept_product_review or reject_product_review instead.

body (JSON string) — fields: title (string), rating (1–5), comment (string).
Example: '{"title":"Great product","rating":5,"comment":"Excellent quality!"}'
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(int $id, string $body): string
    {
        $existing = json_decode($this->client->get(sprintf('product-reviews/%d', $id)), true);
        $b = json_decode($body, true) ?? [];

        return $this->client->put(sprintf('product-reviews/%d', $id), [
            'title'   => $b['title']   ?? ($existing['title'] ?? ''),
            'rating'  => $b['rating']  ?? ($existing['rating'] ?? 5),
            'comment' => $b['comment'] ?? ($existing['comment'] ?? ''),
        ]);
    }
}
