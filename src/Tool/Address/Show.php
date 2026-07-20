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

namespace Sylius\AdminMcpServerPlugin\Tool\Address;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_address',
    description: 'get_address(id) → Full details of an address: firstName, lastName, company, street, city, postcode, countryCode, provinceCode, provinceName, phoneNumber. Get the address id from list_customer_addresses(customerId) or from an order\'s shippingAddress/billingAddress fields.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Address ID.
     */
    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('addresses/%d', $id));
    }
}
