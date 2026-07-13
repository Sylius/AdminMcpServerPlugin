<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Promotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_promotion',
    description: 'get_promotion(code) → JSON object of a single Sylius cart promotion. Returns: id, code, name, description, priority, exclusive, usageLimit, used, couponBased, channels, startsAt, endsAt, rules (type + configuration per channel), actions (type + configuration per channel).',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Promotion code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('promotions/%s', $code));
    }
}
