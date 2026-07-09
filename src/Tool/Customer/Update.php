<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Customer;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_customer',
    description: 'update_customer(id, email?, firstName?, lastName?, gender?, phoneNumber?, birthday?, subscribedToNewsletter?, groupCode?) → JSON object of the updated Sylius customer. Only provided fields are changed. gender: "m", "f", "u". groupCode: "retail" or "wholesale".',
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
            $body['group'] = sprintf('/api/v2/admin/customer-groups/%s', $groupCode);
        }

        return $this->client->put(sprintf('customers/%d', $id), $body);
    }
}
