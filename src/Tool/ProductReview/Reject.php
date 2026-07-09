<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductReview;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'reject_product_review',
    description: 'reject_product_review(id) → Rejects a Sylius product review (transitions status from "new" to "rejected"). Returns JSON of the updated review.',
)]
final readonly class Reject
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Product review ID to reject. Must be in "new" status.
     */
    public function __invoke(int $id): string
    {
        return $this->client->patch(sprintf('product-reviews/%d/reject', $id), []);
    }
}
