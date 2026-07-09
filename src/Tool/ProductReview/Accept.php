<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductReview;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'accept_product_review',
    description: 'accept_product_review(id) → Accepts a Sylius product review (transitions status from "new" to "accepted"). Returns JSON of the updated review.',
)]
final readonly class Accept
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Product review ID to accept. Must be in "new" status.
     */
    public function __invoke(int $id): string
    {
        return $this->client->patch(sprintf('product-reviews/%d/accept', $id), []);
    }
}
