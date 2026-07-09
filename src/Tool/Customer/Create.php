<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Customer;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_customer',
    description: 'create_customer(email, firstName?, lastName?, gender?, phoneNumber?, birthday?, subscribedToNewsletter?, groupCode?) → JSON object of the newly created Sylius customer. gender: "m" (male), "f" (female), "u" (unknown). groupCode: "retail" or "wholesale".',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $email                   Customer email address (unique).
     * @param string $firstName               First name. Default = "".
     * @param string $lastName                Last name. Default = "".
     * @param string $gender                  Gender: "m", "f", or "u" (unknown). Default = "u".
     * @param string $phoneNumber             Phone number. Default = "".
     * @param string $birthday                Birthday in "YYYY-MM-DD" format. Default = "".
     * @param bool   $subscribedToNewsletter  Subscribe to newsletter. Default = false.
     * @param string $groupCode               Customer group code (e.g. "retail", "wholesale"). Default = "".
     */
    public function __invoke(
        string $email,
        string $firstName = '',
        string $lastName = '',
        string $gender = 'u',
        string $phoneNumber = '',
        string $birthday = '',
        bool $subscribedToNewsletter = false,
        string $groupCode = '',
    ): string {
        $body = [
            'email' => $email,
            'gender' => $gender,
            'subscribedToNewsletter' => $subscribedToNewsletter,
        ];

        if ($firstName !== '') {
            $body['firstName'] = $firstName;
        }
        if ($lastName !== '') {
            $body['lastName'] = $lastName;
        }
        if ($phoneNumber !== '') {
            $body['phoneNumber'] = $phoneNumber;
        }
        if ($birthday !== '') {
            $body['birthday'] = $birthday;
        }
        if ($groupCode !== '') {
            $body['group'] = sprintf('/api/v2/admin/customer-groups/%s', $groupCode);
        }

        return $this->client->post('customers', $body);
    }
}
