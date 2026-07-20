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
