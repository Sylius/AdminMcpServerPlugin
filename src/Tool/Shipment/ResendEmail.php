<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Shipment;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'resend_shipment_email',
    description: 'resend_shipment_email(id) → Resends the shipment confirmation email to the customer. The shipment must be in "shipped" state. Returns empty string on success (HTTP 202).',
)]
final readonly class ResendEmail
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Numeric shipment ID. Shipment must be in "shipped" state.
     */
    public function __invoke(int $id): string
    {
        return $this->client->post(sprintf('shipments/%d/resend-confirmation-email', $id));
    }
}
