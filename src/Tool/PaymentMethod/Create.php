<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\PaymentMethod;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_payment_method',
    description: 'create_payment_method(code, name, gatewayFactoryName, gatewayName, channelCodes, localeCode?, description?, instructions?, enabled?) → JSON object of the newly created Sylius payment method. gatewayFactoryName e.g. "offline", "stripe", "paypal_express_checkout".',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $code               Unique payment method code (e.g. "bank_transfer").
     * @param string   $name               Display name for the given locale.
     * @param string   $gatewayFactoryName Gateway factory name (e.g. "offline", "stripe").
     * @param string   $gatewayName        Human-readable gateway label (e.g. "Offline").
     * @param string[] $channelCodes       List of channel codes this method is available in.
     * @param string   $localeCode         Locale for the name/description translation. Default = "en_US".
     * @param string   $description        Optional description text. Default = "".
     * @param string   $instructions       Optional payment instructions. Default = "".
     * @param bool     $enabled            Whether the method is enabled. Default = true.
     */
    public function __invoke(
        string $code,
        string $name,
        string $gatewayFactoryName,
        string $gatewayName,
        array $channelCodes,
        string $localeCode = 'en_US',
        string $description = '',
        string $instructions = '',
        bool $enabled = true,
    ): string {
        $translation = ['locale' => $localeCode, 'name' => $name];
        if ($description !== '') {
            $translation['description'] = $description;
        }
        if ($instructions !== '') {
            $translation['instructions'] = $instructions;
        }

        return $this->client->post('payment-methods', [
            'code' => $code,
            'enabled' => $enabled,
            'gatewayConfig' => [
                'factoryName' => $gatewayFactoryName,
                'gatewayName' => $gatewayName,
            ],
            'channels' => array_map(
                static fn (string $c) => sprintf('/api/v2/admin/channels/%s', $c),
                $channelCodes,
            ),
            'translations' => [$localeCode => $translation],
        ]);
    }
}
