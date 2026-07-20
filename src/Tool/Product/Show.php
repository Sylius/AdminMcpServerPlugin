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

namespace Sylius\AdminMcpServerPlugin\Tool\Product;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_product',
    description: 'get_product(code) → JSON object of a single Sylius product. Returns: id, code, enabled, channels, mainTaxon, translations (name, slug, description, shortDescription per locale), variants, attributes, options, images, createdAt, updatedAt.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Product code (e.g. "MUG_BLUE").
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('products/%s', $code));
    }
}
