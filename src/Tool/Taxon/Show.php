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

namespace Sylius\AdminMcpServerPlugin\Tool\Taxon;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_taxon',
    description: 'get_taxon(code) → Returns full details of a category. Includes: code, enabled, parent (IRI — last segment is the parent code), children (IRIs of direct subcategories — last segments are codes), position (display order), translations (name and slug per locale), images. Use this to inspect a category before editing it.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Taxon code (e.g. "t_shirts", "category").
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('taxons/%s', $code));
    }
}
