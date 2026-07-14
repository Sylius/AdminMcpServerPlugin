<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Zone;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_zone',
    description: 'get_zone(code) → Full details of a zone. Returns: code, name, type (country/zone/province — what kind of things are members), scope (shipping/tax/all — what it\'s used for), members (array of member codes, e.g. country ISO codes like "US", "DE"). Use add_zone_member / remove_zone_member to change members.',
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
