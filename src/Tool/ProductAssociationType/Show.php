<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAssociationType;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_product_association_type',
    description: 'get_product_association_type(code) → JSON object of a single product association type. Returns: id, code, name, createdAt, updatedAt, translations (name per locale).',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('product-association-types/%s', $code));
    }
}
