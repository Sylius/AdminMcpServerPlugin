<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Address;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_customer_addresses',
    description: <<<'DESC'
list_customer_addresses(customerId) → JSON Hydra collection of addresses for a customer.

Each address has: id, firstName, lastName, street, city, postcode, countryCode, phoneNumber, company.
Use id (from @id) with get_address or update_address.

REQUIRED: customerId (numeric customer ID — get it from list_customers or get_customer).
DESC,
)]
final readonly class Index
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(int $customerId): string
    {
        return $this->client->get('addresses', ['customer.id' => $customerId]);
    }
}
