<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\TaxCategory;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_tax_category',
    description: 'update_tax_category(code, name, description?) → JSON object of the updated Sylius tax category.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code        Tax category code to update.
     * @param string $name        New display name.
     * @param string $description New description. Default = "".
     */
    public function __invoke(string $code, string $name, string $description = ''): string
    {
        $body = ['name' => $name];
        if ($description !== '') {
            $body['description'] = $description;
        }

        return $this->client->put(sprintf('tax-categories/%s', $code), $body);
    }
}
