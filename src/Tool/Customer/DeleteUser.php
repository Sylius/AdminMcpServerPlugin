<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Customer;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_customer_user',
    description: 'delete_customer_user(id) → empty string on success (HTTP 204). Deletes the shop user account associated with the customer, preventing login. The customer record itself is preserved. Returns 404 if the customer has no user account.',
)]
final readonly class DeleteUser
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Customer ID whose shop user account should be deleted.
     */
    public function __invoke(int $id): string
    {
        return $this->client->delete(sprintf('customers/%d/user', $id));
    }
}
