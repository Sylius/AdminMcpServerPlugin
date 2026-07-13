<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Product;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_products',
    description: 'list_products(page?, itemsPerPage?, code?, name?, enabled?) → JSON Hydra collection of Sylius products. Each product has: id, code, enabled, channels, mainTaxon, translations (name, slug, description per locale), variants, createdAt, updatedAt.',
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
     * @param string $code         Filter by exact product code.
     * @param string $name         Filter by product name (partial match).
     * @param bool|null $enabled   Filter by enabled status (null = all).
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30, string $code = '', string $name = '', ?bool $enabled = null): string
    {
        $params = [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ];

        if ($code !== '') {
            $params['code'] = $code;
        }

        if ($name !== '') {
            $params['translations.name'] = $name;
        }

        if ($enabled !== null) {
            $params['enabled'] = $enabled ? 'true' : 'false';
        }

        return $this->client->get('products', $params);
    }
}
