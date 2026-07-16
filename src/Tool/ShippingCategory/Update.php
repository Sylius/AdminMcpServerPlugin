<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingCategory;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_shipping_category',
    description: <<<'DESC'
update_shipping_category(code, body) → JSON of the updated shipping category. Only fields in body are changed.

body (JSON string) — fields: name (string), description (string).
Example: '{"name":"Heavy Goods","description":"Items over 20kg"}'
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code, string $body): string
    {
        $existing = json_decode($this->client->get(sprintf('shipping-categories/%s', $code)), true);
        $b = json_decode($body, true) ?? [];

        $merged = ['name' => $b['name'] ?? ($existing['name'] ?? $code)];

        $description = $b['description'] ?? ($existing['description'] ?? null);
        if ($description !== null) {
            $merged['description'] = $description;
        }

        return $this->client->put(sprintf('shipping-categories/%s', $code), $merged);
    }
}
