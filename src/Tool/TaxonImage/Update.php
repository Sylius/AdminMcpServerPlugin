<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\TaxonImage;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_taxon_image',
    description: <<<'DESC'
update_taxon_image(taxonCode, imageId, body) → JSON of the updated taxon image. Only fields in body are changed.

body (JSON string) — fields: type (string label, e.g. "main"/"banner").
Example: '{"type":"banner"}'
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $taxonCode, int $imageId, string $body): string
    {
        $existing = json_decode(
            $this->client->get(sprintf('taxons/%s/images/%d', $taxonCode, $imageId)),
            true,
        );
        $b = json_decode($body, true) ?? [];

        return $this->client->put(
            sprintf('taxons/%s/images/%d', $taxonCode, $imageId),
            ['type' => $b['type'] ?? ($existing['type'] ?? null)],
        );
    }
}
