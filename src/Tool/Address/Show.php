<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Address;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_address',
    description: 'get_address(id) → Full details of an address: firstName, lastName, company, street, city, postcode, countryCode, provinceCode, provinceName, phoneNumber. Get the address id from list_customer_addresses(customerId) or from an order\'s shippingAddress/billingAddress fields.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Address ID.
     */
    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('addresses/%d', $id));
    }
}
