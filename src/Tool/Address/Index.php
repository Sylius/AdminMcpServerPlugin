<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Address;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_customer_addresses',
    description: <<<'DESC'
list_customer_addresses(customerId) → Returns all saved addresses for a customer by collecting unique addresses from their orders (Sylius does not expose addresses as a standalone list).

Each address has: id (use for get_address or update_address), firstName, lastName, street, city, postcode, countryCode, phoneNumber, company.

REQUIRED: customerId (numeric customer ID — get it from list_customers or get_customer). Returns empty list if the customer has no orders yet.
DESC,
)]
final readonly class Index
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(int $customerId): string
    {
        // Sylius has no /addresses collection endpoint.
        // We collect addresses from the customer's orders instead.
        $orders = json_decode(
            $this->client->get('orders', ['customer.id' => $customerId, 'itemsPerPage' => 100, 'pagination' => false]),
            true,
        );

        $seen = [];
        $addresses = [];

        foreach ($orders['hydra:member'] ?? [] as $order) {
            foreach (['shippingAddress', 'billingAddress'] as $field) {
                $addr = $order[$field] ?? null;
                if (!is_array($addr)) {
                    continue;
                }
                $id = (int) basename($addr['@id'] ?? '');
                if ($id && !isset($seen[$id])) {
                    $seen[$id] = true;
                    $addresses[] = [
                        'id' => $id,
                        'firstName' => $addr['firstName'] ?? '',
                        'lastName' => $addr['lastName'] ?? '',
                        'street' => $addr['street'] ?? '',
                        'city' => $addr['city'] ?? '',
                        'postcode' => $addr['postcode'] ?? '',
                        'countryCode' => $addr['countryCode'] ?? '',
                        'phoneNumber' => $addr['phoneNumber'] ?? null,
                        'company' => $addr['company'] ?? null,
                    ];
                }
            }
        }

        return (string) json_encode([
            'customerId' => $customerId,
            'total' => count($addresses),
            'addresses' => $addresses,
        ]);
    }
}
