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
        // When filtering by product, fetch review IRIs from the product response
        // (the /product-reviews collection endpoint does not support filtering by product)
        if ($productCode !== '') {
            $product = json_decode($this->client->get(sprintf('products/%s', $productCode)), true);
            $reviewIris = $product['reviews'] ?? [];

            $reviews = [];
            foreach ($reviewIris as $iri) {
                $id = basename($iri);
                $review = json_decode($this->client->get(sprintf('product-reviews/%s', $id)), true);
                if ($status === '' || ($review['status'] ?? '') === $status) {
                    $reviews[] = $review;
                }
            }

            return (string) json_encode([
                '@context' => '/api/v2/contexts/ProductReview',
                '@type' => 'hydra:Collection',
                'hydra:totalItems' => count($reviews),
                'hydra:member' => $reviews,
            ]);
        }

        $params = ['page' => $page, 'itemsPerPage' => $itemsPerPage];
        if ($status !== '') {
            $params['status'] = $status;
        }

        return $this->client->get('product-reviews', $params);
    }
}
