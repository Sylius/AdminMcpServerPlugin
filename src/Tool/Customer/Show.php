<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Customer;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_customer',
    description: 'get_customer(id) → JSON object of a single Sylius customer. Returns: id, email, firstName, lastName, gender, phoneNumber, birthday, group, subscribedToNewsletter, defaultAddress, user (enabled, verified), createdAt, fullName.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Customer ID.
     */
    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('customers/%d', $id));
    }
}
