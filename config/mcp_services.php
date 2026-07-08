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
use Acme\SyliusExamplePlugin\Tool\Address\Show as AddressShow;
use Acme\SyliusExamplePlugin\Tool\Address\Update as AddressUpdate;
use Acme\SyliusExamplePlugin\Tool\TaxCategory\Create as TaxCategoryCreate;
use Acme\SyliusExamplePlugin\Tool\TaxCategory\Index as TaxCategoryIndex;
use Acme\SyliusExamplePlugin\Tool\TaxCategory\Show as TaxCategoryShow;
use Acme\SyliusExamplePlugin\Tool\TaxCategory\Update as TaxCategoryUpdate;
use Acme\SyliusExamplePlugin\Tool\TaxRate\Create as TaxRateCreate;
use Acme\SyliusExamplePlugin\Tool\TaxRate\Index as TaxRateIndex;
use Acme\SyliusExamplePlugin\Tool\TaxRate\Show as TaxRateShow;
use Acme\SyliusExamplePlugin\Tool\TaxRate\Update as TaxRateUpdate;
use Acme\SyliusExamplePlugin\Tool\PaymentMethod\Create as PaymentMethodCreate;
use Acme\SyliusExamplePlugin\Tool\PaymentMethod\Index as PaymentMethodIndex;
use Acme\SyliusExamplePlugin\Tool\PaymentMethod\Show as PaymentMethodShow;
use Acme\SyliusExamplePlugin\Tool\PaymentMethod\Update as PaymentMethodUpdate;
use Acme\SyliusExamplePlugin\Tool\Customer\Create as CustomerCreate;
use Acme\SyliusExamplePlugin\Tool\Customer\DeleteUser as CustomerDeleteUser;
use Acme\SyliusExamplePlugin\Tool\Customer\GetStatistics as CustomerGetStatistics;
use Acme\SyliusExamplePlugin\Tool\Customer\Index as CustomerIndex;
use Acme\SyliusExamplePlugin\Tool\Customer\Show as CustomerShow;
use Acme\SyliusExamplePlugin\Tool\Customer\Update as CustomerUpdate;
use Acme\SyliusExamplePlugin\Tool\CustomerGroup\Create as CustomerGroupCreate;
use Acme\SyliusExamplePlugin\Tool\CustomerGroup\Index as CustomerGroupIndex;
use Acme\SyliusExamplePlugin\Tool\CustomerGroup\Show as CustomerGroupShow;
use Acme\SyliusExamplePlugin\Tool\CustomerGroup\Update as CustomerGroupUpdate;
use Acme\SyliusExamplePlugin\Tool\Taxon\Create as TaxonCreate;
use Acme\SyliusExamplePlugin\Tool\Taxon\Delete as TaxonDelete;
use Acme\SyliusExamplePlugin\Tool\Taxon\Index as TaxonIndex;
use Acme\SyliusExamplePlugin\Tool\Taxon\Show as TaxonShow;
use Acme\SyliusExamplePlugin\Tool\Taxon\Update as TaxonUpdate;

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

    $services->set(CustomerIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(CustomerShow::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(CustomerCreate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(CustomerUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(CustomerGetStatistics::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(CustomerDeleteUser::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(CustomerGroupIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(CustomerGroupShow::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(CustomerGroupCreate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(CustomerGroupUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(AddressShow::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(AddressUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(TaxCategoryIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(TaxCategoryShow::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(TaxCategoryCreate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(TaxCategoryUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(TaxRateIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(TaxRateShow::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(TaxRateCreate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(TaxRateUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(PaymentMethodIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(PaymentMethodShow::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(PaymentMethodCreate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(PaymentMethodUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');
};
