<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Acme\SyliusExamplePlugin\Mcp\Loader\PluginDiscoveryLoader;
use Acme\SyliusExamplePlugin\Tool\Administrator\Create as AdministratorCreate;
use Acme\SyliusExamplePlugin\Tool\Administrator\Delete as AdministratorDelete;
use Acme\SyliusExamplePlugin\Tool\Administrator\Index as AdministratorIndex;
use Acme\SyliusExamplePlugin\Tool\Administrator\Show as AdministratorShow;
use Acme\SyliusExamplePlugin\Tool\Administrator\Update as AdministratorUpdate;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(PluginDiscoveryLoader::class)
        ->args([service('logger')])
        ->tag('monolog.logger', ['channel' => 'mcp'])
        ->tag('mcp.loader');

    $services->set(AdminApiClient::class)
        ->args([
            service('acme_admin_mcp.http_client'),
            param('acme_admin_mcp.api.base_uri'),
            param('acme_admin_mcp.api.email'),
            param('acme_admin_mcp.api.password'),
        ]);

    $services->set(AdministratorIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(AdministratorShow::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(AdministratorCreate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(AdministratorUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(AdministratorDelete::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');
};
