<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingCategory;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_shipping_category',
    description: 'update_shipping_category(code, name?, description?) → JSON object of the updated shipping category. Only provided fields are changed; omitted fields keep their current values.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code, string $name = '', string $description = ''): string
    {
        $existing = json_decode($this->client->get(sprintf('shipping-categories/%s', $code)), true);

        $body = ['name' => $name !== '' ? $name : ($existing['name'] ?? $code)];

        if ($description !== '') {
            $body['description'] = $description;
        } elseif (isset($existing['description']) && $existing['description'] !== null) {
            $body['description'] = $existing['description'];
        }

        return $this->client->put(sprintf('shipping-categories/%s', $code), $body);
    }
}
