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
    name: 'update_taxon_image',
    description: <<<'DESC'
update_taxon_image(taxonCode, imageId, body) → JSON of the updated taxon image.

IMPORTANT: First call get_taxon_image to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body.
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
        return $this->client->put(
            sprintf('taxons/%s/images/%d', $taxonCode, $imageId),
            json_decode($body, true) ?? [],
        );
    }
}
