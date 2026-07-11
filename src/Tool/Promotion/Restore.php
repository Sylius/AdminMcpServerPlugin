<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Promotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'restore_promotion',
    description: 'restore_promotion(code) → Restores a previously archived Sylius cart promotion, making it active again in the shop. Returns JSON of the restored promotion.',
)]
final readonly class Restore
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Promotion code to restore.
     */
    public function __invoke(string $code): string
    {
        return $this->client->patch(sprintf('promotions/%s/restore', $code), []);
    }
}
