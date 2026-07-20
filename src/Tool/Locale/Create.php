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

namespace Sylius\AdminMcpServerPlugin\Tool\Locale;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_locale',
    description: 'create_locale(code) → JSON object of the newly created Sylius locale. code must be a valid locale string (e.g. "en_US", "pl_PL", "de_DE", "fr_FR").',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Locale code (e.g. "pl_PL", "de_DE").
     */
    public function __invoke(string $code): string
    {
        return $this->client->post('locales', ['code' => $code]);
    }
}
