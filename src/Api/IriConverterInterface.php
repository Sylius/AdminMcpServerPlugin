<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Api;

interface IriConverterInterface
{
    /**
     * Builds a full admin API IRI path from a relative resource path.
     * E.g. iri('channels/WEB') → '/api/v2/admin/channels/WEB'
     */
    public function iri(string $path): string;

    /**
     * Extracts the bare code from an IRI or returns the value as-is if it is already a code.
     * E.g. normalizeCode('/api/v2/admin/channels/WEB') → 'WEB'
     *      normalizeCode('WEB') → 'WEB'
     */
    public function normalizeCode(string $iriOrCode): string;
}
