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
}
