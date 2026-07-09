<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductReview;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_product_reviews',
    description: 'list_product_reviews(page?, itemsPerPage?, status?, productCode?) → JSON Hydra collection of Sylius product reviews. Each review has: id, title, rating, comment, author (customer IRI), status (new/accepted/rejected), reviewSubject (product IRI).',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int    $page         Page number (1-based). Default = 1.
     * @param int    $itemsPerPage Items per page. Default = 30.
     * @param string $status       Filter by status: new, accepted, rejected. Default = "" (all).
     * @param string $productCode  Filter by product code. Default = "" (all).
     */
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
