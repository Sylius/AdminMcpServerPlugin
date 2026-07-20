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
    name: 'delete_taxon_image',
    description: 'delete_taxon_image(taxonCode, imageId) → Permanently deletes a taxon image. Returns empty string on success (HTTP 204). Use list_taxon_images to find the imageId.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $taxonCode Taxon code.
     * @param int    $imageId   Image numeric ID to delete.
     */
    public function __invoke(string $taxonCode, int $imageId): string
    {
        return $this->client->delete(sprintf('taxons/%s/images/%d', $taxonCode, $imageId));
    }
}
