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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\AdminMcpServerPlugin\Tool\Customer\Create;
use Sylius\AdminMcpServerPlugin\Tool\Customer\DeleteUser;
use Sylius\AdminMcpServerPlugin\Tool\Customer\GetStatistics;
use Sylius\AdminMcpServerPlugin\Tool\Customer\Index;
use Sylius\AdminMcpServerPlugin\Tool\Customer\Show;
use Sylius\AdminMcpServerPlugin\Tool\Customer\Update;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $api = service('sylius_admin_mcp_server.client.api');

    $services->set(Index::class)->args([$api])->tag('mcp.tool');
    $services->set(Show::class)->args([$api])->tag('mcp.tool');
    $services->set(Create::class)->args([$api])->tag('mcp.tool');
    $services->set(Update::class)->args([$api])->tag('mcp.tool');
    $services->set(GetStatistics::class)->args([$api])->tag('mcp.tool');
    $services->set(DeleteUser::class)->args([$api])->tag('mcp.tool');
};
