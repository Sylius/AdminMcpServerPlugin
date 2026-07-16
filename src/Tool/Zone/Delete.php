<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Zone;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'delete_zone',
    description: 'delete_zone(code) → Permanently deletes a zone. Returns empty string on success (HTTP 204). WARNING: Do not delete zones that are assigned to shipping methods or tax rates.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Zone code to delete (e.g. "EU").
     */
    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('zones/%s', $code));
    }
}
