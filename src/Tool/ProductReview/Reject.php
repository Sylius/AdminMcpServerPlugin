<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductReview;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

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
