<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Customer;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_customers',
    description: <<<'DESC'
list_customers(page?, itemsPerPage?, email?, firstName?, lastName?) → Lists shop customers. Each customer has: id (numeric — needed for all other customer operations), email, firstName, lastName, gender (m/f/u), phoneNumber, birthday, group (IRI — last segment is the group code), subscribedToNewsletter, createdAt.

Filter by email (exact match), firstName, or lastName to find a specific customer. The numeric id is needed for get_customer, update_customer, get_customer_statistics, list_customer_addresses.
DESC,
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int    $page         Page number (1-based). Default = 1.
     * @param int    $itemsPerPage Items per page. Default = 30.
     * @param string $email        Filter by exact email address. Leave empty to skip.
     * @param string $firstName    Filter by first name (partial match). Leave empty to skip.
     * @param string $lastName     Filter by last name (partial match). Leave empty to skip.
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30, string $email = '', string $firstName = '', string $lastName = ''): string
    {
        $query = [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ];

        if ($email !== '') {
            $query['email'] = $email;
        }
        if ($firstName !== '') {
            $query['firstName'] = $firstName;
        }
        if ($lastName !== '') {
            $query['lastName'] = $lastName;
        }

        return $this->client->get('customers', $query);
    }
}
