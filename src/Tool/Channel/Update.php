<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Channel;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_channel',
    description: <<<'DESC'
update_channel(code, body) → JSON of the updated channel.

IMPORTANT: First call get_channel to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body.

Convenience shorthands: locale (IRI string) sets both defaultLocale and locales; currency (IRI string) sets both baseCurrency and currencies.
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code, string $body): string
    {
        $b = json_decode($body, true) ?? [];
        if (isset($b['locale'])) {
            $b['defaultLocale'] = $b['locale'];
            $b['locales'] = [$b['locale']];
            unset($b['locale']);
        }
        if (isset($b['currency'])) {
            $b['baseCurrency'] = $b['currency'];
            $b['currencies'] = [$b['currency']];
            unset($b['currency']);
        }
        return $this->client->put(sprintf('channels/%s', $code), $b);
    }
}
