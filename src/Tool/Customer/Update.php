<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Customer;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_customer',
    description: <<<'DESC'
update_customer — Updates customer information. Only the fields you provide are changed.

REQUIRED: id (numeric customer id from list_customers).
OPTIONAL: email, firstName, lastName, gender ("m"=male, "f"=female, "u"=unknown), phoneNumber, birthday (format "YYYY-MM-DD", e.g. "1990-05-15"), subscribedToNewsletter (true/false), group (customer segment IRI from list_customer_groups @id).

NOTE: This does not affect the customer's login account. To block login use delete_customer_user(id). To change a customer's group use group with an IRI from list_customer_groups.
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
    public function __invoke(
        int $id,
        string $email = '',
        string $firstName = '',
        string $lastName = '',
        string $gender = '',
        string $phoneNumber = '',
        string $birthday = '',
        ?bool $subscribedToNewsletter = null,
        string $group = '',
    ): string {
        $body = [];

        if ($email !== '') {
            $body['email'] = $email;
        }
        if ($firstName !== '') {
            $body['firstName'] = $firstName;
        }
        if ($lastName !== '') {
            $body['lastName'] = $lastName;
        }
        if ($gender !== '') {
            $body['gender'] = $gender;
        }
        if ($phoneNumber !== '') {
            $body['phoneNumber'] = $phoneNumber;
        }
        if ($birthday !== '') {
            $body['birthday'] = $birthday;
        }
        if ($subscribedToNewsletter !== null) {
            $body['subscribedToNewsletter'] = $subscribedToNewsletter;
        }
        if ($group !== '') {
            $body['group'] = $group;
        }

        return $this->client->put(sprintf('customers/%d', $id), $body);
    }
}
