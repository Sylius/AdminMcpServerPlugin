<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Api;

interface ApiClientInterface
{
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
     * PUT with Content-Type: application/ld+json — required when the body contains embedded
     * sub-resources referenced by @id (e.g. channelPricings, translations in product variants).
     *
     * @param array<string, mixed> $body
     */
    public function putLd(string $path, array $body = []): string;

    /**
     * @param array<string, mixed> $body
     */
    public function patch(string $path, array $body = []): string;

    public function delete(string $path): string;
}
