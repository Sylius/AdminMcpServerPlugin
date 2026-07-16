<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductReview;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_product_reviews',
    description: 'list_product_reviews(page?, itemsPerPage?, status?, productCode?) → JSON collection of product reviews. Filter by status ("new"=pending moderation, "accepted"=published, "rejected"=hidden) or by productCode (returns all reviews for that product). Each review has: id, title, rating (1-5), comment, status. Use accept_product_review(id) or reject_product_review(id) to moderate pending reviews.',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(int $page = 1, int $itemsPerPage = 30, string $status = '', string $productCode = ''): string
    {
        $params = ['page' => $page, 'itemsPerPage' => $itemsPerPage];
        if ($status !== '') {
            $params['status'] = $status;
        }
        if ($productCode !== '') {
            $params['reviewSubject'] = sprintf('/api/v2/admin/products/%s', $productCode);
        }

        return $this->client->get('product-reviews', $params);
    }
}
