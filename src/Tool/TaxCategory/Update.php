<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\TaxCategory;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_tax_category',
    description: 'update_tax_category(code, name?, description?) → JSON object of the updated Sylius tax category. Only provided fields are changed; omitted fields keep their current values.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code        Tax category code to update.
     * @param string $name        New display name (omit to keep current).
     * @param string $description New description (omit to keep current).
     */
    public function __invoke(string $code, string $name = '', string $description = ''): string
    {
        $existing = json_decode($this->client->get(sprintf('tax-categories/%s', $code)), true);

        $body = ['name' => $name !== '' ? $name : ($existing['name'] ?? $code)];
        if ($description !== '') {
            $body['description'] = $description;
        } elseif (isset($existing['description']) && $existing['description'] !== null) {
            $body['description'] = $existing['description'];
        }

        return $this->client->put(sprintf('tax-categories/%s', $code), $body);
    }
}
