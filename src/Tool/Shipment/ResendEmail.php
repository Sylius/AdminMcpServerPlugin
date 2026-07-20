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

namespace Sylius\AdminMcpServerPlugin\Tool\Shipment;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'resend_shipment_email',
    description: 'resend_shipment_email(id) → Resends the shipment confirmation email to the customer. The shipment must already be in "shipped" state (use ship_shipment first if needed). Returns empty string on success. Use get_shipment(id) to check the current state.',
)]
final readonly class ResendEmail
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    public function __invoke(int $id): string
    {
        return $this->client->post(sprintf('shipments/%d/resend-confirmation-email', $id));
    }
}
