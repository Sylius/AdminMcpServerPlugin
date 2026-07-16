<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\PaymentMethod;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_payment_method',
    description: <<<'DESC'
update_payment_method(code, body) → JSON of the updated payment method. Only fields in body are changed.

body (JSON string) — fields: enabled (bool), channels (array of channel IRIs from list_channels @id), translations (map of locale → {name?, description?, instructions?}).
Example: '{"enabled":true,"translations":{"en_US":{"name":"Bank Transfer","description":"Pay via bank transfer"},"pl_PL":{"name":"Przelew bankowy"}}}'
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
        $result = [];

        if (isset($b['translations'])) {
            $existing = json_decode($this->client->get(sprintf('payment-methods/%s', $code)), true);
            $merged = $existing['translations'] ?? [];
            foreach ($b['translations'] as $locale => $fields) {
                if (!isset($merged[$locale])) {
                    $merged[$locale] = [];
                }
                foreach ($fields as $key => $value) {
                    $merged[$locale][$key] = $value;
                }
            }
            $result['translations'] = $merged;
        }

        if (isset($b['enabled'])) {
            $result['enabled'] = $b['enabled'];
        }
        if (isset($b['channels'])) {
            $result['channels'] = $b['channels'];
        }

        return $this->client->put(sprintf('payment-methods/%s', $code), $result);
    }
}
