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

namespace Sylius\AdminMcpServerPlugin\Tool\TaxonImage;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_taxon_images',
    description: 'list_taxon_images(taxonCode) → JSON Hydra collection of images for a Sylius taxon. Each image has: id (use for delete_taxon_image), type, path (URL path to the image file), owner.',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $taxonCode Taxon code.
     */
    public function __invoke(string $taxonCode): string
    {
        return $this->client->get(sprintf('taxons/%s/images', $taxonCode));
    }
}
