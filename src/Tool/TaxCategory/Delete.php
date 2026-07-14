<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\TaxCategory;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_tax_category',
    description: 'delete_tax_category(code) → empty string on success (HTTP 204). Permanently deletes the Sylius tax category with the given code. Cannot delete a category that has tax rates assigned to it.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Tax category code to delete.
     */
    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('tax-categories/%s', $code));
    }
}
