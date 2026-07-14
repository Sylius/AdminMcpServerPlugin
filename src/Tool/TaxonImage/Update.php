<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\TaxonImage;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_taxon_image',
    description: 'update_taxon_image(taxonCode, imageId, type?) → Updates the type label of a taxon image (e.g. "main", "banner"). Returns JSON of the updated image. Use list_taxon_images to find the imageId.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $taxonCode, int $imageId, string $type = ''): string
    {
        $existing = json_decode(
            $this->client->get(sprintf('taxons/%s/images/%d', $taxonCode, $imageId)),
            true,
        );

        return $this->client->put(
            sprintf('taxons/%s/images/%d', $taxonCode, $imageId),
            ['type' => $type !== '' ? $type : ($existing['type'] ?? null)],
        );
    }
}
