<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\CustomerGroup;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_customer_group',
    description: 'update_customer_group(code, name) → JSON object of the updated Sylius customer group.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Customer group code to update.
     * @param string $name New display name for the customer group.
     */
    public function __invoke(string $code, string $name): string
    {
        return $this->client->put(sprintf('customer-groups/%s', $code), [
            'name' => $name,
        ]);
    }
}
