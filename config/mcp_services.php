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
use Acme\SyliusExamplePlugin\Tool\Product\Create as ProductCreate;
use Acme\SyliusExamplePlugin\Tool\Product\Delete as ProductDelete;
use Acme\SyliusExamplePlugin\Tool\Product\Index as ProductIndex;
use Acme\SyliusExamplePlugin\Tool\Product\Show as ProductShow;
use Acme\SyliusExamplePlugin\Tool\Product\Update as ProductUpdate;
use Acme\SyliusExamplePlugin\Tool\Taxon\Create as TaxonCreate;
use Acme\SyliusExamplePlugin\Tool\Taxon\Delete as TaxonDelete;
use Acme\SyliusExamplePlugin\Tool\Taxon\Index as TaxonIndex;
use Acme\SyliusExamplePlugin\Tool\Taxon\Show as TaxonShow;
use Acme\SyliusExamplePlugin\Tool\Taxon\Update as TaxonUpdate;
use Acme\SyliusExamplePlugin\Tool\ProductTaxon\Create as ProductTaxonCreate;
use Acme\SyliusExamplePlugin\Tool\ProductTaxon\Delete as ProductTaxonDelete;
use Acme\SyliusExamplePlugin\Tool\ProductTaxon\Index as ProductTaxonIndex;
use Acme\SyliusExamplePlugin\Tool\ProductTaxon\Update as ProductTaxonUpdate;

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

    $services->set(ProductIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductShow::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductCreate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductDelete::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(TaxonIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(TaxonShow::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(TaxonCreate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(TaxonUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(TaxonDelete::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductTaxonIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductTaxonCreate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductTaxonUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductTaxonDelete::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');
};
