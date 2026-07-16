<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Customer;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_customer',
    description: <<<'DESC'
update_customer(id, body) — Updates customer information. Only the fields included in body are changed.

REQUIRED: id (numeric customer id from list_customers).
body (JSON string) — fields: email, firstName, lastName, gender ("m"/"f"/"u"), phoneNumber, birthday ("YYYY-MM-DD"), subscribedToNewsletter (bool), group (IRI from list_customer_groups @id).
Example: '{"firstName": "Jan", "lastName": "Kowalski", "subscribedToNewsletter": true}'

NOTE: Does not affect the customer's login account. To block login use delete_customer_user(id).
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int         $id                      Customer ID.
     * @param string      $email                   New email address.
     * @param string      $firstName               New first name.
     * @param string      $lastName                New last name.
     * @param string      $gender                  New gender: "m", "f", or "u".
     * @param string      $phoneNumber             New phone number.
     * @param string      $birthday                New birthday in "YYYY-MM-DD" format.
     * @param bool|null   $subscribedToNewsletter  New newsletter subscription status.
     * @param string      $group                   New customer group IRI from list_customer_groups @id.
     */
    public function __invoke(int $id, string $body): string
    {
        return $this->client->put(sprintf('customers/%d', $id), json_decode($body, true) ?? []);
    }
}
