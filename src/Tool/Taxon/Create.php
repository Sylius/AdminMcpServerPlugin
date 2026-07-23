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

namespace Sylius\AdminMcpServerPlugin\Tool\Taxon;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_taxon',
    description: <<<'DESC'
create_taxon — Creates a product category (called "taxon" in Sylius). Categories are used to organize products in the store. They can be nested (subcategories).

REQUIRED: code (unique ID, no spaces, e.g. "T_SHIRTS"), name (display name, e.g. "T-Shirts").
OPTIONAL: parent (parent category IRI to create a subcategory — the format is always "/api/v2/admin/taxons/{CODE}", so if you know the parent code you can construct it directly without calling list_taxons, e.g. "/api/v2/admin/taxons/CLOTHING"), slug (URL path, auto-generated from name if omitted), description, localeCode (default "en_US").

Example: to create "Men's T-Shirts" under "Men's Clothing": code="MENS_TSHIRTS", name="Men's T-Shirts", parent="/api/v2/admin/taxons/MENS_CLOTHING". Use list_taxons to find existing category IRIs.
DESC,
)]
final readonly class Create
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    public function __invoke(
        string $code,
        string $name,
        string $localeCode = 'en_US',
        string $slug = '',
        string $description = '',
        string $parent = '',
    ): string {
        $translation = ['name' => $name, 'locale' => $localeCode];
        if ($slug !== '') {
            $translation['slug'] = $slug;
        }
        if ($description !== '') {
            $translation['description'] = $description;
        }

        $body = [
            'code' => $code,
            'translations' => [$localeCode => $translation],
        ];

        if ($parent !== '') {
            $body['parent'] = $parent;
        }

        return $this->client->post('taxons', $body);
    }
}
