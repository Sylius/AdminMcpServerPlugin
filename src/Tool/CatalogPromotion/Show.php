<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\CatalogPromotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_catalog_promotion',
    description: 'get_catalog_promotion(code) → JSON object of a single Sylius catalog promotion. Returns: id, code, name, enabled, exclusive, priority, startDate, endDate, channels, scopes, actions, translations, state.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Catalog promotion code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('catalog-promotions/%s', $code));
    }
}
