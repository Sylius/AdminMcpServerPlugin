<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\CustomerGroup;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_customer_group',
    description: 'get_customer_group(code) → JSON object of a single Sylius customer group. Returns: id, code, name.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Customer group code (e.g. "retail", "wholesale").
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('customer-groups/%s', $code));
    }
}
