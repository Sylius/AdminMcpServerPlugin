<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Zone;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_zone',
    description: 'get_zone(code) → JSON object of a single Sylius zone. Returns: id, code, name, type, scope, members.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Zone code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('zones/%s', $code));
    }
}
