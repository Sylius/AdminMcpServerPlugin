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
use Acme\SyliusExamplePlugin\Tool\ProductVariant\Create as ProductVariantCreate;
use Acme\SyliusExamplePlugin\Tool\ProductVariant\Delete as ProductVariantDelete;
use Acme\SyliusExamplePlugin\Tool\ProductVariant\Index as ProductVariantIndex;
use Acme\SyliusExamplePlugin\Tool\ProductVariant\Show as ProductVariantShow;
use Acme\SyliusExamplePlugin\Tool\ProductVariant\Update as ProductVariantUpdate;
use Acme\SyliusExamplePlugin\Tool\ProductAttribute\Create as ProductAttributeCreate;
use Acme\SyliusExamplePlugin\Tool\ProductAttribute\Index as ProductAttributeIndex;
use Acme\SyliusExamplePlugin\Tool\ProductAttribute\Show as ProductAttributeShow;
use Acme\SyliusExamplePlugin\Tool\ProductAttribute\Update as ProductAttributeUpdate;
use Acme\SyliusExamplePlugin\Tool\ProductOption\Create as ProductOptionCreate;
use Acme\SyliusExamplePlugin\Tool\ProductOption\Index as ProductOptionIndex;
use Acme\SyliusExamplePlugin\Tool\ProductOption\Show as ProductOptionShow;
use Acme\SyliusExamplePlugin\Tool\ProductOption\Update as ProductOptionUpdate;
use Acme\SyliusExamplePlugin\Tool\ProductReview\Accept as ProductReviewAccept;
use Acme\SyliusExamplePlugin\Tool\ProductReview\Delete as ProductReviewDelete;
use Acme\SyliusExamplePlugin\Tool\ProductReview\Index as ProductReviewIndex;
use Acme\SyliusExamplePlugin\Tool\ProductReview\Reject as ProductReviewReject;
use Acme\SyliusExamplePlugin\Tool\ProductReview\Show as ProductReviewShow;
use Acme\SyliusExamplePlugin\Tool\ProductReview\Update as ProductReviewUpdate;
use Acme\SyliusExamplePlugin\Tool\ProductAssociation\Create as ProductAssociationCreate;
use Acme\SyliusExamplePlugin\Tool\ProductAssociation\Delete as ProductAssociationDelete;
use Acme\SyliusExamplePlugin\Tool\ProductAssociation\Index as ProductAssociationIndex;
use Acme\SyliusExamplePlugin\Tool\ProductAssociation\Update as ProductAssociationUpdate;
use Acme\SyliusExamplePlugin\Tool\ProductAssociationType\Create as ProductAssociationTypeCreate;
use Acme\SyliusExamplePlugin\Tool\ProductAssociationType\Index as ProductAssociationTypeIndex;
use Acme\SyliusExamplePlugin\Tool\ProductAssociationType\Update as ProductAssociationTypeUpdate;

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

    $services->set(ProductVariantIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductVariantShow::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductVariantCreate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductVariantUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductVariantDelete::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductAttributeIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductAttributeShow::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductAttributeCreate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductAttributeUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductOptionIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductOptionShow::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductOptionCreate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductOptionUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductReviewIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductReviewShow::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductReviewUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductReviewDelete::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductReviewAccept::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductReviewReject::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductAssociationIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductAssociationCreate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductAssociationUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductAssociationDelete::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductAssociationTypeIndex::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductAssociationTypeCreate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');

    $services->set(ProductAssociationTypeUpdate::class)
        ->args([service(AdminApiClient::class)])
        ->tag('mcp.tool');
};
