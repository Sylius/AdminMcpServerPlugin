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
OPTIONAL: email, firstName, lastName, gender ("m"=male, "f"=female, "u"=unknown), phoneNumber, birthday (format "YYYY-MM-DD", e.g. "1990-05-15"), subscribedToNewsletter (true/false), groupCode (customer segment — use list_customer_groups to find codes).

NOTE: This does not affect the customer's login account. To block login use delete_customer_user(id). To change a customer's group use groupCode with a code from list_customer_groups.
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
     * @param string      $groupCode               New customer group code (e.g. "retail", "wholesale").
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
        string $groupCode = '',
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
        if ($groupCode !== '') {
            $body['group'] = $this->client->iri(sprintf('customer-groups/%s', $groupCode));
        }

        return $this->client->put(sprintf('customers/%d', $id), $body);
    }
}
