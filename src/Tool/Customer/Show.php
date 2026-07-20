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
