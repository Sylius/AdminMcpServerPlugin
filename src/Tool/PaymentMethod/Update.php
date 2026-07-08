<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\PaymentMethod;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_payment_method',
    description: 'update_payment_method(code, name, localeCode?, description?, instructions?, enabled?, channelCodes?) → JSON object of the updated Sylius payment method. Uses PUT with translation @id.',
)]
final readonly class Update
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string    $code         Payment method code to update.
     * @param string    $name         Display name for the given locale.
     * @param string    $localeCode   Locale for the translation. Default = "en_US".
     * @param string    $description  Description text. Default = "".
     * @param string    $instructions Payment instructions. Default = "".
     * @param bool|null $enabled      Set enabled status. Null = do not change.
     * @param string[]  $channelCodes New list of channel codes (replaces existing). Empty = do not change.
     */
    public function __invoke(
        string $code,
        string $name,
        string $localeCode = 'en_US',
        string $description = '',
        string $instructions = '',
        ?bool $enabled = null,
        array $channelCodes = [],
    ): string {
        $translation = [
            '@id' => sprintf('/api/v2/admin/payment-methods/%s/translations/%s', $code, $localeCode),
            'locale' => $localeCode,
            'name' => $name,
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
        if ($channelCodes !== []) {
            $body['channels'] = array_map(
                static fn (string $c) => sprintf('/api/v2/admin/channels/%s', $c),
                $channelCodes,
            );
        }

        return $this->client->put(sprintf('payment-methods/%s', $code), $body);
    }
}
