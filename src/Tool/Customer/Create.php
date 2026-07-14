<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Customer;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_customer',
    description: <<<'DESC'
create_customer — Creates a new customer account. Does NOT create a login (user account) — only stores customer data.

REQUIRED: email (must be unique), firstName, lastName.
OPTIONAL: phoneNumber (e.g. "+1 555 123 4567"), birthday (format "YYYY-MM-DD", e.g. "1990-05-15"), gender ("m" for male, "f" for female, "u" for unknown/unspecified), subscribedToNewsletter (true/false, default false), customerGroupCode (use list_customer_groups to find codes).

Returns the created customer with their numeric ID (use this ID for update_customer, get_address, etc.).
DESC,
)]
final readonly class Create
{
    public function __construct(private ApiClientInterface $client) {}

    /**
     * @param string $email                  Customer email address (must be unique).
     * @param string $firstName              First name.
     * @param string $lastName               Last name.
     * @param string $phoneNumber            Phone number (e.g. "+1 555 123 4567"). Default = "" (none).
     * @param string $birthday               Date of birth in YYYY-MM-DD format (e.g. "1990-05-15"). Default = "" (none).
     * @param string $gender                 Gender: "m" (male), "f" (female), "u" (unspecified). Default = "u".
     * @param bool   $subscribedToNewsletter Whether the customer opts in to newsletter. Default = false.
     * @param string $customerGroupCode      Customer group code (use list_customer_groups). Default = "" (none).
     */
    public function __invoke(
        string $email,
        string $firstName,
        string $lastName,
        string $phoneNumber = '',
        string $birthday = '',
        string $gender = 'u',
        bool $subscribedToNewsletter = false,
        string $customerGroupCode = '',
    ): string {
        $body = [
            'email'                   => $email,
            'firstName'               => $firstName,
            'lastName'                => $lastName,
            'gender'                  => $gender,
            'subscribedToNewsletter'  => $subscribedToNewsletter,
        ];

        if ($phoneNumber !== '') { $body['phoneNumber'] = $phoneNumber; }
        if ($birthday !== '') { $body['birthday'] = $birthday; }
        if ($customerGroupCode !== '') {
            $body['group'] = $this->client->iri(sprintf('customer-groups/%s', $customerGroupCode));
        }

        return $this->client->post('customers', $body);
    }
}
