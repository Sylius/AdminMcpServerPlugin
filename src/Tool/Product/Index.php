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

namespace Sylius\AdminMcpServerPlugin\Tool\Product;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_products',
    description: 'list_products(page?, itemsPerPage?, code?, name?, enabled?) → JSON-LD/Hydra collection of Sylius products. Each product has: code (string — the identifier for get_product, update_product, delete_product), enabled, channels, mainTaxon, translations (name, slug, description per locale), variants, createdAt, updatedAt. The @id field is the JSON-LD IRI of the product.',
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
