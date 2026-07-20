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

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAssociationType;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_product_association_type',
    description: 'create_product_association_type(code, name, localeCode?) → JSON object of the newly created Sylius product association type. code must be unique.',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code       Unique association type code (e.g. "cross_sell").
     * @param string $name       Display name for the given locale.
     * @param string $localeCode Locale for the name translation. Default = "en_US".
     */
    public function __invoke(string $code, string $name, string $localeCode = 'en_US'): string
    {
        return $this->client->post('product-association-types', [
            'code' => $code,
            'translations' => [
                $localeCode => [
                    'locale' => $localeCode,
                    'name' => $name,
                ],
            ],
        ]);
    }
}
