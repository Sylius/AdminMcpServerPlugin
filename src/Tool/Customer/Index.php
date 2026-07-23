<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Customer;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_customers',
    description: <<<'DESC'
list_customers(page?, itemsPerPage?, email?, firstName?, lastName?, orderBy?, orderDir?) → Lists shop customers. Each customer has: id (numeric — needed for all other customer operations), email, firstName, lastName, gender (m/f/u), phoneNumber, birthday, group (IRI — last segment is the group code), subscribedToNewsletter, createdAt.

Filter by email (exact match), firstName, or lastName to find a specific customer. The numeric id is needed for get_customer, update_customer, get_customer_statistics, list_customer_addresses. Use orderBy/orderDir to sort (e.g. orderBy=createdAt orderDir=desc).
DESC,
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(
        int $page = 1,
        int $itemsPerPage = 30,
        string $email = '',
        string $firstName = '',
        string $lastName = '',
        string $orderBy = '',
        string $orderDir = 'asc',
    ): string {
        $params = ['page' => $page, 'itemsPerPage' => $itemsPerPage];

        if ($email !== '') {
            $params['email'] = $email;
        }
        if ($firstName !== '') {
            $params['firstName'] = $firstName;
        }
        if ($lastName !== '') {
            $params['lastName'] = $lastName;
        }
        if ($orderBy !== '') {
            $params['order[' . $orderBy . ']'] = $orderDir;
        }

        return $this->client->get('customers', $params);
    }
}
