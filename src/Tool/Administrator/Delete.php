<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Administrator;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_administrator',
    description: 'delete_administrator(id) → empty string on success (HTTP 204). Permanently deletes the Sylius administrator with the given ID. Cannot delete your own account.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Administrator ID to delete.
     */
    public function __invoke(int $id): string
    {
        return $this->client->delete(sprintf('administrators/%d', $id));
    }
}
