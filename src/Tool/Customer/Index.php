<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Customer;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_customers',
    description: 'list_customers(page?, itemsPerPage?, email?) → JSON Hydra collection of Sylius customers. Each customer has: id, email, firstName, lastName, gender, phoneNumber, birthday, group, subscribedToNewsletter, createdAt. Optionally filter by exact email address.',
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
     * @param string $email        Filter by exact email address. Leave empty to list all.
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30, string $email = ''): string
    {
        $query = [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ];

        if ($email !== '') {
            $query['email'] = $email;
        }

        return $this->client->get('customers', $query);
    }
}
