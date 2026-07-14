<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Api;

final readonly class AdminApiIriConverter implements IriConverterInterface
{
    private string $baseApiPath;

    public function __construct(string $baseUri)
    {
        $this->baseApiPath = rtrim((string) parse_url($baseUri, \PHP_URL_PATH), '/') ?: '/api/v2/admin';
    }

    public function iri(string $path): string
    {
        return $this->baseApiPath . '/' . ltrim($path, '/');
    }

    public function normalizeCode(string $iriOrCode): string
    {
        return str_contains($iriOrCode, '/') ? basename($iriOrCode) : $iriOrCode;
    }
}
