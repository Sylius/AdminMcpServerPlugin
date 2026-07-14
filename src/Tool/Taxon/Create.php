<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Taxon;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_taxon',
    description: <<<'DESC'
create_taxon — Creates a product category (called "taxon" in Sylius). Categories are used to organize products in the store. They can be nested (subcategories).

REQUIRED: code (unique ID, no spaces, e.g. "T_SHIRTS"), name (display name, e.g. "T-Shirts").
OPTIONAL: parentCode (parent category code to create a subcategory, e.g. "CLOTHING"), slug (URL path, auto-generated from name if omitted), description, localeCode (default "en_US").

Example: to create "Men's T-Shirts" under "Men's Clothing": code="MENS_TSHIRTS", name="Men's T-Shirts", parentCode="MENS_CLOTHING". Use list_taxons to find existing category codes.
DESC,
)]
final readonly class Create
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(
        string $code,
        string $name,
        string $localeCode = 'en_US',
        string $slug = '',
        string $description = '',
        string $parentCode = '',
    ): string {
        $translation = ['name' => $name, 'locale' => $localeCode];
        if ($slug !== '') { $translation['slug'] = $slug; }
        if ($description !== '') { $translation['description'] = $description; }

        $body = [
            'code'         => $code,
            'translations' => [$localeCode => $translation],
        ];

        if ($parentCode !== '') {
            $body['parent'] = sprintf('/api/v2/admin/taxons/%s', $parentCode);
        }

        return $this->client->post('taxons', $body);
    }
}
