<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Address;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_address',
    description: <<<'DESC'
update_address — Updates an address. Only the fields you provide are changed; everything else stays the same (the tool fetches the current address first and merges your changes).

REQUIRED: id (address numeric ID — get it from list_customer_addresses).
OPTIONAL: firstName, lastName, street, city, postcode, countryCode (ISO 2-letter, e.g. "US", "FR", "DE"), company, phoneNumber, provinceCode (e.g. "US-NY"), provinceName (e.g. "New York").

Use list_customer_addresses(customerId) to find the address id before calling this tool.
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(
        int $id,
        string $firstName = '',
        string $lastName = '',
        string $street = '',
        string $city = '',
        string $postcode = '',
        string $countryCode = '',
        string $company = '',
        string $phoneNumber = '',
        string $provinceCode = '',
        string $provinceName = '',
    ): string {
        // GET the existing address so we can do a smart merge (PUT requires all required fields)
        $existing = json_decode($this->client->get(sprintf('addresses/%d', $id)), true);

        $body = [
            'firstName'   => $firstName !== '' ? $firstName : ($existing['firstName'] ?? ''),
            'lastName'    => $lastName !== '' ? $lastName : ($existing['lastName'] ?? ''),
            'street'      => $street !== '' ? $street : ($existing['street'] ?? ''),
            'city'        => $city !== '' ? $city : ($existing['city'] ?? ''),
            'postcode'    => $postcode !== '' ? $postcode : ($existing['postcode'] ?? ''),
            'countryCode' => $countryCode !== '' ? $countryCode : ($existing['countryCode'] ?? ''),
        ];

        $body['company']      = $company !== '' ? $company : ($existing['company'] ?? null);
        $body['phoneNumber']  = $phoneNumber !== '' ? $phoneNumber : ($existing['phoneNumber'] ?? null);
        $body['provinceCode'] = $provinceCode !== '' ? $provinceCode : ($existing['provinceCode'] ?? null);
        $body['provinceName'] = $provinceName !== '' ? $provinceName : ($existing['provinceName'] ?? null);

        // Remove null optional fields to avoid overwriting existing data with null
        $body = array_filter($body, static fn ($v) => $v !== null);

        return $this->client->put(sprintf('addresses/%d', $id), $body);
    }
}
