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
    name: 'delete_customer_user',
    description: 'delete_customer_user(id) → Removes the login account (shop user) from a customer, preventing them from logging into the shop. The customer profile, order history and addresses are all preserved. Returns 404 if the customer never registered a login. Use get_customer(id) to check if a customer has a user account (look at the "user" field).',
)]
final readonly class DeleteUser
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Customer ID whose shop user account should be deleted.
     */
    public function __invoke(int $id): string
    {
        return $this->client->delete(sprintf('customers/%d/user', $id));
    }
}
