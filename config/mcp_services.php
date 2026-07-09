<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Acme\SyliusExamplePlugin\Mcp\Resource\SyliusGuidelinesResource;
use Acme\SyliusExamplePlugin\Tool\Address\Show as AddressShow;
use Acme\SyliusExamplePlugin\Tool\Address\Update as AddressUpdate;
use Acme\SyliusExamplePlugin\Tool\Administrator\Create as AdministratorCreate;
use Acme\SyliusExamplePlugin\Tool\Administrator\Delete as AdministratorDelete;
use Acme\SyliusExamplePlugin\Tool\Administrator\Index as AdministratorIndex;
use Acme\SyliusExamplePlugin\Tool\Administrator\Show as AdministratorShow;
use Acme\SyliusExamplePlugin\Tool\Administrator\Update as AdministratorUpdate;
use Acme\SyliusExamplePlugin\Tool\Auth\Login;
use Acme\SyliusExamplePlugin\Tool\Auth\Logout;
use Acme\SyliusExamplePlugin\Tool\Channel\Create as ChannelCreate;
use Acme\SyliusExamplePlugin\Tool\Channel\Delete as ChannelDelete;
use Acme\SyliusExamplePlugin\Tool\Channel\Index as ChannelIndex;
use Acme\SyliusExamplePlugin\Tool\Channel\Show as ChannelShow;
use Acme\SyliusExamplePlugin\Tool\Channel\Update as ChannelUpdate;
use Acme\SyliusExamplePlugin\Tool\Country\Create as CountryCreate;
use Acme\SyliusExamplePlugin\Tool\Country\Index as CountryIndex;
use Acme\SyliusExamplePlugin\Tool\Country\Show as CountryShow;
use Acme\SyliusExamplePlugin\Tool\Country\Update as CountryUpdate;
use Acme\SyliusExamplePlugin\Tool\Currency\Create as CurrencyCreate;
use Acme\SyliusExamplePlugin\Tool\Currency\Index as CurrencyIndex;
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
use Acme\SyliusExamplePlugin\Tool\ExchangeRate\Create as ExchangeRateCreate;
use Acme\SyliusExamplePlugin\Tool\ExchangeRate\Index as ExchangeRateIndex;
use Acme\SyliusExamplePlugin\Tool\ExchangeRate\Update as ExchangeRateUpdate;
use Acme\SyliusExamplePlugin\Tool\Locale\Create as LocaleCreate;
use Acme\SyliusExamplePlugin\Tool\Locale\Delete as LocaleDelete;
use Acme\SyliusExamplePlugin\Tool\Locale\Index as LocaleIndex;
use Acme\SyliusExamplePlugin\Tool\PaymentMethod\Create as PaymentMethodCreate;
use Acme\SyliusExamplePlugin\Tool\PaymentMethod\Index as PaymentMethodIndex;
use Acme\SyliusExamplePlugin\Tool\PaymentMethod\Show as PaymentMethodShow;
use Acme\SyliusExamplePlugin\Tool\PaymentMethod\Update as PaymentMethodUpdate;
use Acme\SyliusExamplePlugin\Tool\Product\Create as ProductCreate;
use Acme\SyliusExamplePlugin\Tool\Product\Delete as ProductDelete;
use Acme\SyliusExamplePlugin\Tool\Product\Index as ProductIndex;
use Acme\SyliusExamplePlugin\Tool\Product\Show as ProductShow;
use Acme\SyliusExamplePlugin\Tool\Product\Update as ProductUpdate;
use Acme\SyliusExamplePlugin\Tool\ProductAssociation\Create as ProductAssociationCreate;
use Acme\SyliusExamplePlugin\Tool\ProductAssociation\Delete as ProductAssociationDelete;
use Acme\SyliusExamplePlugin\Tool\ProductAssociation\Index as ProductAssociationIndex;
use Acme\SyliusExamplePlugin\Tool\ProductAssociationType\Create as ProductAssociationTypeCreate;
use Acme\SyliusExamplePlugin\Tool\ProductAssociationType\Index as ProductAssociationTypeIndex;
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
use Acme\SyliusExamplePlugin\Tool\ProductTaxon\Create as ProductTaxonCreate;
use Acme\SyliusExamplePlugin\Tool\ProductTaxon\Delete as ProductTaxonDelete;
use Acme\SyliusExamplePlugin\Tool\ProductTaxon\Index as ProductTaxonIndex;
use Acme\SyliusExamplePlugin\Tool\ProductTaxon\Update as ProductTaxonUpdate;
use Acme\SyliusExamplePlugin\Tool\ProductVariant\Create as ProductVariantCreate;
use Acme\SyliusExamplePlugin\Tool\ProductVariant\Delete as ProductVariantDelete;
use Acme\SyliusExamplePlugin\Tool\ProductVariant\Index as ProductVariantIndex;
use Acme\SyliusExamplePlugin\Tool\ProductVariant\Show as ProductVariantShow;
use Acme\SyliusExamplePlugin\Tool\ProductVariant\Update as ProductVariantUpdate;
use Acme\SyliusExamplePlugin\Tool\TaxCategory\Create as TaxCategoryCreate;
use Acme\SyliusExamplePlugin\Tool\TaxCategory\Index as TaxCategoryIndex;
use Acme\SyliusExamplePlugin\Tool\TaxCategory\Show as TaxCategoryShow;
use Acme\SyliusExamplePlugin\Tool\TaxCategory\Update as TaxCategoryUpdate;
use Acme\SyliusExamplePlugin\Tool\TaxRate\Create as TaxRateCreate;
use Acme\SyliusExamplePlugin\Tool\TaxRate\Index as TaxRateIndex;
use Acme\SyliusExamplePlugin\Tool\TaxRate\Show as TaxRateShow;
use Acme\SyliusExamplePlugin\Tool\TaxRate\Update as TaxRateUpdate;
use Acme\SyliusExamplePlugin\Tool\Taxon\Create as TaxonCreate;
use Acme\SyliusExamplePlugin\Tool\Taxon\Delete as TaxonDelete;
use Acme\SyliusExamplePlugin\Tool\Taxon\Index as TaxonIndex;
use Acme\SyliusExamplePlugin\Tool\Taxon\Show as TaxonShow;
use Acme\SyliusExamplePlugin\Tool\Taxon\Update as TaxonUpdate;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(SyliusGuidelinesResource::class)
        ->tag('mcp.resource');

    $services->set(Login::class)
        ->args([
            service('sylius_admin_mcp_server.authenticator'),
            service('sylius_admin_mcp_server.token_storage'),
        ])
        ->tag('mcp.tool');

    $services->set(Logout::class)
        ->args([service('sylius_admin_mcp_server.token_storage')])
        ->tag('mcp.tool');

    $services->set(AdministratorIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(AdministratorShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(AdministratorCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(AdministratorUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(AdministratorDelete::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductDelete::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductVariantIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductVariantShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductVariantCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductVariantUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductVariantDelete::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductAttributeIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductAttributeShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductAttributeCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductAttributeUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductOptionIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductOptionShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductOptionCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductOptionUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductReviewIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductReviewShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductReviewUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductReviewAccept::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductReviewReject::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductReviewDelete::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductAssociationIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');


    $services->set(ProductAssociationCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductAssociationDelete::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductAssociationTypeIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');


    $services->set(ProductAssociationTypeCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(TaxonIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(TaxonShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(TaxonCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(TaxonUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(TaxonDelete::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductTaxonIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductTaxonCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductTaxonUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ProductTaxonDelete::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CustomerIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CustomerShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CustomerCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CustomerUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CustomerGetStatistics::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CustomerDeleteUser::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CustomerGroupIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CustomerGroupShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CustomerGroupCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CustomerGroupUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(AddressShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(AddressUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(TaxCategoryIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(TaxCategoryShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(TaxCategoryCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(TaxCategoryUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(TaxRateIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(TaxRateShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(TaxRateCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(TaxRateUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(PaymentMethodIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(PaymentMethodShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(PaymentMethodCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(PaymentMethodUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ChannelIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ChannelShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ChannelCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ChannelUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ChannelDelete::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CurrencyIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CurrencyCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ExchangeRateIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ExchangeRateCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ExchangeRateUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(LocaleIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(LocaleCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(LocaleDelete::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CountryIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CountryShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CountryCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CountryUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');
};
