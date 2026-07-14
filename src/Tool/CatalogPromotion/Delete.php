<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\CatalogPromotion;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_catalog_promotion',
    description: 'delete_catalog_promotion(code) → Permanently deletes a catalog promotion. Returns empty response on success (204). NOTE: Catalog promotions are processed asynchronously — if the promotion was just created or recently modified, wait a moment before deleting (deletion of a "processing" promotion may fail).',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('catalog-promotions/%s', $code));
    }
}
