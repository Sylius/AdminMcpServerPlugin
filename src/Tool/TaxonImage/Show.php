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
    name: 'get_taxon_image',
    description: 'get_taxon_image(taxonCode, imageId) → JSON object of a single taxon image. Returns: id, type, path (URL to the image), owner. Use list_taxon_images to find the imageId.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $taxonCode, int $imageId): string
    {
        return $this->client->get(sprintf('taxons/%s/images/%d', $taxonCode, $imageId));
    }
}
