<?php

declare(strict_types=1);

namespace Tests\Sylius\AdminMcpServerPlugin\Unit\Api;

use Sylius\AdminMcpServerPlugin\Api\AdminApiIriConverter;
use PHPUnit\Framework\TestCase;

final class AdminApiIriConverterTest extends TestCase
{
    public function testBuildsIriFromRelativePath(): void
    {
        $converter = new AdminApiIriConverter('https://localhost/api/v2/admin/');

        self::assertSame('/api/v2/admin/channels/WEB', $converter->iri('channels/WEB'));
        self::assertSame('/api/v2/admin/taxons/caps', $converter->iri('taxons/caps'));
    }

    public function testStripsLeadingSlashFromPath(): void
    {
        $converter = new AdminApiIriConverter('https://localhost/api/v2/admin/');

        self::assertSame('/api/v2/admin/products/MUG', $converter->iri('/products/MUG'));
    }

    public function testFallsBackToDefaultPathWhenBaseUriHasNoPath(): void
    {
        $converter = new AdminApiIriConverter('https://localhost');

        self::assertSame('/api/v2/admin/channels/WEB', $converter->iri('channels/WEB'));
    }
}
