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
    description: 'list_products(page?, itemsPerPage?, code?, name?, enabled?, orderBy?, orderDir?) → JSON-LD/Hydra collection of Sylius products. Each product has: code (string — the identifier for get_product, update_product, delete_product), enabled, channels, mainTaxon, translations (name, slug, description per locale), variants, createdAt, updatedAt. The @id field is the JSON-LD IRI of the product. To get recently added products use orderBy=createdAt orderDir=desc.',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(
        int $page = 1,
        int $itemsPerPage = 30,
        string $code = '',
        string $name = '',
        ?bool $enabled = null,
        string $orderBy = '',
        string $orderDir = 'asc',
    ): string {
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

        if ($orderBy !== '') {
            $params['order[' . $orderBy . ']'] = $orderDir;
        }

        return $this->client->get('products', $params);
    }
}
