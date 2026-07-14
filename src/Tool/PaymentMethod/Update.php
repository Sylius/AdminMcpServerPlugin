<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\PaymentMethod;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_payment_method',
    description: 'update_payment_method(code, name?, localeCode?, description?, instructions?, enabled?, channels?) → JSON object of the updated Sylius payment method. channels: array of channel IRIs from list_channels @id (replaces existing). Only provided fields are changed; omitted fields keep their current values.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string    $code         Payment method code to update.
     * @param string    $name         Display name for the given locale (omit to keep current).
     * @param string    $localeCode   Locale for the translation. Default = "en_US".
     * @param string    $description  Description text.
     * @param string    $instructions Payment instructions.
     * @param bool|null $enabled      Set enabled status. Null = do not change.
     * @param string[]  $channels Array of channel IRIs (from list_channels @id) — replaces existing. Empty = do not change.
     */
    public function __invoke(
        string $code,
        string $name = '',
        string $localeCode = 'en_US',
        string $description = '',
        string $instructions = '',
        ?bool $enabled = null,
        array $channels = [],
    ): string {
        $existing = json_decode($this->client->get(sprintf('payment-methods/%s', $code)), true);

        $resolvedName = $name !== '' ? $name : ($existing['translations'][$localeCode]['name'] ?? $code);

        $translation = [
            '@id'    => $this->client->iri(sprintf('payment-methods/%s/translations/%s', $code, $localeCode)),
            'locale' => $localeCode,
            'name'   => $resolvedName,
        ];
        if ($description !== '') {
            $translation['description'] = $description;
        }
        if ($instructions !== '') {
            $translation['instructions'] = $instructions;
        }

        $body = ['translations' => [$localeCode => $translation]];

        if ($enabled !== null) {
            $body['enabled'] = $enabled;
        }
        if ($channels !== []) {
            $body['channels'] = $channels;
        }

        return $this->client->put(sprintf('payment-methods/%s', $code), $body);
    }
}
