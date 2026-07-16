<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Address;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_address',
    description: <<<'DESC'
update_address(id, body) → JSON of the updated address.

IMPORTANT: First call get_address to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body.

id: numeric address ID (from list_customer_addresses).
body (JSON string): full modified address JSON.
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(int $id, string $body): string
    {
        return $this->client->put(sprintf('addresses/%d', $id), json_decode($body, true) ?? []);
    }
}
