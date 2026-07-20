<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\PaymentMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_payment_method',
    description: 'create_payment_method(code, name, gatewayFactoryName, gatewayName, channels, localeCode?, description?, instructions?, enabled?) → JSON object of the newly created Sylius payment method. channels: array of channel IRIs from list_channels @id. gatewayFactoryName e.g. "offline", "stripe", "paypal_express_checkout".',
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
     * @param string[] $channels           Array of channel IRIs (from list_channels @id).
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
        array $channels,
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
            'channels' => $channels,
            'translations' => [$localeCode => $translation],
        ]);
    }
}
