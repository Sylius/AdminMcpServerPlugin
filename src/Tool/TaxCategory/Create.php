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

namespace Sylius\AdminMcpServerPlugin\Tool\TaxCategory;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_tax_category',
    description: 'create_tax_category(code, name, description?) → JSON object of the newly created Sylius tax category. code must be unique.',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code        Unique tax category code (e.g. "reduced_vat").
     * @param string $name        Display name (e.g. "Reduced VAT").
     * @param string $description Optional description. Default = "".
     */
    public function __invoke(string $code, string $name, string $description = ''): string
    {
        $body = ['code' => $code, 'name' => $name];
        if ($description !== '') {
            $body['description'] = $description;
        }

        return $this->client->post('tax-categories', $body);
    }
}
