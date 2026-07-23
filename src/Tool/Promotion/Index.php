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
    name: 'list_promotions',
    description: 'list_promotions(page?, itemsPerPage?, orderBy?, orderDir?) → Lists cart promotions (discounts applied at checkout). Each has: code, name, couponBased (true=requires coupon code / false=automatic), usageLimit, used (times applied), startsAt/endsAt (validity window), rules (conditions), actions (what discount). Use get_promotion(code) for full details including rules and actions. Use orderBy/orderDir to sort (e.g. orderBy=createdAt orderDir=desc).',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(
        int $page = 1,
        int $itemsPerPage = 30,
        string $orderBy = '',
        string $orderDir = 'asc',
    ): string {
        $params = [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ];
        if ($orderBy !== '') {
            $params['order[' . $orderBy . ']'] = $orderDir;
        }

        return $this->client->get('promotions', $params);
    }
}
