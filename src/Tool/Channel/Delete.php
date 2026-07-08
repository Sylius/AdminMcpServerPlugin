<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Channel;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_channel',
    description: 'delete_channel(code) → empty string on success (HTTP 204). Permanently deletes the Sylius channel with the given code.',
)]
final readonly class Delete
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code Channel code to delete.
     */
    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('channels/%s', $code));
    }
}
