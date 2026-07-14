<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Customer;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_customer',
    description: <<<'DESC'
get_customer(id) → Full details of a customer. Returns: id, email, firstName, lastName, gender, phoneNumber, birthday, group (IRI — last segment is the code, e.g. "retail"), subscribedToNewsletter, user (login account status: enabled/verified), createdAt.

To see customer order history use list_orders. To see their saved addresses use list_customer_addresses(id). To see purchase statistics use get_customer_statistics(id).
DESC,
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
