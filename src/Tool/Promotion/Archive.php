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

namespace Sylius\AdminMcpServerPlugin\Tool\Promotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'archive_promotion',
    description: 'archive_promotion(code) → Archives (soft-deletes) a Sylius cart promotion. The promotion is hidden from the shop but preserved in the database. Calling this on an already-archived promotion updates the archivedAt timestamp (Sylius allows it). Use restore_promotion to undo. Returns JSON of the archived promotion.',
)]
final readonly class Archive
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Promotion code to archive.
     */
    public function __invoke(string $code): string
    {
        return $this->client->patch(sprintf('promotions/%s/archive', $code), []);
    }
}
