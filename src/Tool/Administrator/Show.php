<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Administrator;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_administrator',
    description: 'get_administrator(id) → JSON object of a single Sylius administrator. Returns: id, username, email, firstName, lastName, localeCode, enabled, lastLogin, createdAt, updatedAt.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Administrator ID.
     */
    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('administrators/%d', $id));
    }
}
