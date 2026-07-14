<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Api;

interface ApiClientInterface
{
    /**
     * Builds a full IRI path (e.g. /api/v2/admin/channels/WEB) from a relative resource path.
     * Use instead of hardcoding /api/v2/admin/ in tool classes.
     */
    public function iri(string $path): string;

    /**
     * @param array<string, mixed> $query
     */
    public function get(string $path, array $query = []): string;

    /**
     * @param array<string, mixed> $body
     */
    public function post(string $path, array $body = []): string;

    /**
     * @param array<string, mixed> $body
     */
    public function put(string $path, array $body = []): string;

    /**
     * @param array<string, mixed> $body
     */
    public function patch(string $path, array $body = []): string;

    public function delete(string $path): string;
}
