<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Address;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_address',
    description: <<<'DESC'
update_address(id, body) → JSON of the updated address. Only fields in body are changed; everything else stays the same (the tool fetches the current address first and merges your changes).

id: numeric address ID (from list_customer_addresses).
body (JSON string) — fields: firstName, lastName, street, city, postcode, countryCode (ISO 2-letter, e.g. "US"), company, phoneNumber, provinceCode (e.g. "US-NY"), provinceName (e.g. "New York").
Example: '{"firstName":"Jan","city":"Warsaw","countryCode":"PL"}'
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
        $existing = json_decode($this->client->get(sprintf('addresses/%d', $id)), true);
        $b = json_decode($body, true) ?? [];

        $merged = [
            'firstName'   => $b['firstName']   ?? ($existing['firstName'] ?? ''),
            'lastName'    => $b['lastName']    ?? ($existing['lastName'] ?? ''),
            'street'      => $b['street']      ?? ($existing['street'] ?? ''),
            'city'        => $b['city']        ?? ($existing['city'] ?? ''),
            'postcode'    => $b['postcode']    ?? ($existing['postcode'] ?? ''),
            'countryCode' => $b['countryCode'] ?? ($existing['countryCode'] ?? ''),
        ];

        foreach (['company', 'phoneNumber', 'provinceCode', 'provinceName'] as $opt) {
            $val = $b[$opt] ?? ($existing[$opt] ?? null);
            if ($val !== null) {
                $merged[$opt] = $val;
            }
        }

        return $this->client->put(sprintf('addresses/%d', $id), $merged);
    }
}
