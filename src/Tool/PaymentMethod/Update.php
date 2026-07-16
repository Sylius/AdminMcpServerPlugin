<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\PaymentMethod;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_payment_method',
    description: <<<'DESC'
update_payment_method(code, translations?, enabled?, channels?) → JSON object of the updated Sylius payment method. Only provided fields are changed.

translations (JSON string) — map of locale → translation fields (name, description, instructions). Pass multiple locales at once:
'{"en_US": {"name": "Bank Transfer", "description": "..."}, "pl_PL": {"name": "Przelew bankowy"}}'

channels: array of channel IRIs from list_channels @id (replaces existing). Omit to keep current.
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string    $code         Payment method code to update.
     * @param string    $translations JSON map of locale → {name?, description?, instructions?}.
     * @param bool|null $enabled      Set enabled status. Null = do not change.
     * @param string[]  $channels     Array of channel IRIs (from list_channels @id) — replaces existing. Empty = do not change.
     */
    public function __invoke(
        string $code,
        string $translations = '{}',
        ?bool $enabled = null,
        array $channels = [],
    ): string {
        $existing = json_decode($this->client->get(sprintf('payment-methods/%s', $code)), true);

        $body = [];

        $incoming = json_decode($translations, true);
        if (!empty($incoming)) {
            $merged = $existing['translations'] ?? [];
            foreach ($incoming as $locale => $fields) {
                if (!isset($merged[$locale])) {
                    $merged[$locale] = [];
                }
                foreach ($fields as $key => $value) {
                    $merged[$locale][$key] = $value;
                }
            }
            $body['translations'] = $merged;
        }

        if ($enabled !== null) {
            $body['enabled'] = $enabled;
        }
        if ($channels !== []) {
            $body['channels'] = $channels;
        }

        return $this->client->put(sprintf('payment-methods/%s', $code), $body);
    }
}
