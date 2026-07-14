<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Province;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'delete_province',
    description: 'delete_province(countryCode, provinceCode) → Permanently deletes a province from a country. Returns empty string on success. provinceCode is the full code including country prefix (e.g. "US-CA").',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $countryCode  2-letter ISO country code (e.g. "US").
     * @param string $provinceCode Full province code (e.g. "US-CA").
     */
    public function __invoke(string $countryCode, string $provinceCode): string
    {
        $country = json_decode($this->client->get(sprintf('countries/%s', $countryCode)), true);
        $provinces = $country['provinces'] ?? [];

        $targetIri = $this->client->iri(sprintf('countries/%s/provinces/%s', $countryCode, $provinceCode));
        $provinces = array_values(array_filter($provinces, static fn (string $iri) => $iri !== $targetIri));

        $this->client->put(sprintf('countries/%s', $countryCode), ['provinces' => $provinces]);

        return '';
    }
}
