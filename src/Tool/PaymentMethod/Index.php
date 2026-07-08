<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\PaymentMethod;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_payment_methods',
    description: 'list_payment_methods(page?, itemsPerPage?) → JSON Hydra collection of Sylius payment methods. Each method has: id, code, enabled, position, gatewayConfig (factoryName, gatewayName), channels, translations (name, description, instructions per locale).',
)]
final readonly class Index
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param int $page         Page number (1-based). Default = 1.
     * @param int $itemsPerPage Items per page. Default = 30.
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30): string
    {
        return $this->client->get('payment-methods', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}
