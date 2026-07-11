<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\AdminMcpServerPlugin\Mcp\Resource\SyliusGuidelinesResource;
use Sylius\AdminMcpServerPlugin\Tool\CatalogPromotion\Create as CatalogPromotionCreate;
use Sylius\AdminMcpServerPlugin\Tool\CatalogPromotion\Index as CatalogPromotionIndex;
use Sylius\AdminMcpServerPlugin\Tool\CatalogPromotion\Show as CatalogPromotionShow;
use Sylius\AdminMcpServerPlugin\Tool\CatalogPromotion\Update as CatalogPromotionUpdate;
use Sylius\AdminMcpServerPlugin\Tool\Coupon\Create as CouponCreate;
use Sylius\AdminMcpServerPlugin\Tool\Coupon\Delete as CouponDelete;
use Sylius\AdminMcpServerPlugin\Tool\Coupon\Generate as CouponGenerate;
use Sylius\AdminMcpServerPlugin\Tool\Coupon\Index as CouponIndex;
use Sylius\AdminMcpServerPlugin\Tool\Coupon\Update as CouponUpdate;
use Sylius\AdminMcpServerPlugin\Tool\Promotion\Archive as PromotionArchive;
use Sylius\AdminMcpServerPlugin\Tool\Promotion\Create as PromotionCreate;
use Sylius\AdminMcpServerPlugin\Tool\Promotion\Delete as PromotionDelete;
use Sylius\AdminMcpServerPlugin\Tool\Promotion\Index as PromotionIndex;
use Sylius\AdminMcpServerPlugin\Tool\Promotion\Restore as PromotionRestore;
use Sylius\AdminMcpServerPlugin\Tool\Promotion\Show as PromotionShow;
use Sylius\AdminMcpServerPlugin\Tool\Promotion\Update as PromotionUpdate;
use Sylius\AdminMcpServerPlugin\Tool\Address\Show as AddressShow;
use Sylius\AdminMcpServerPlugin\Tool\ShippingCategory\Index as ShippingCategoryIndex;
use Sylius\AdminMcpServerPlugin\Tool\ShippingMethod\Archive as ShippingMethodArchive;
use Sylius\AdminMcpServerPlugin\Tool\ShippingMethod\Create as ShippingMethodCreate;
use Sylius\AdminMcpServerPlugin\Tool\ShippingMethod\Delete as ShippingMethodDelete;
use Sylius\AdminMcpServerPlugin\Tool\ShippingMethod\Index as ShippingMethodIndex;
use Sylius\AdminMcpServerPlugin\Tool\ShippingMethod\Restore as ShippingMethodRestore;
use Sylius\AdminMcpServerPlugin\Tool\ShippingMethod\Show as ShippingMethodShow;
use Sylius\AdminMcpServerPlugin\Tool\ShippingMethod\Update as ShippingMethodUpdate;
use Sylius\AdminMcpServerPlugin\Tool\Zone\Create as ZoneCreate;
use Sylius\AdminMcpServerPlugin\Tool\Zone\Index as ZoneIndex;
use Sylius\AdminMcpServerPlugin\Tool\Zone\Show as ZoneShow;
use Sylius\AdminMcpServerPlugin\Tool\Zone\Update as ZoneUpdate;
use Sylius\AdminMcpServerPlugin\Tool\Address\Update as AddressUpdate;
use Sylius\AdminMcpServerPlugin\Tool\Administrator\Create as AdministratorCreate;
use Sylius\AdminMcpServerPlugin\Tool\Administrator\Delete as AdministratorDelete;
use Sylius\AdminMcpServerPlugin\Tool\Administrator\Index as AdministratorIndex;
use Sylius\AdminMcpServerPlugin\Tool\Administrator\Show as AdministratorShow;
use Sylius\AdminMcpServerPlugin\Tool\Administrator\Update as AdministratorUpdate;
use Sylius\AdminMcpServerPlugin\Tool\Auth\Login;
use Sylius\AdminMcpServerPlugin\Tool\Auth\Logout;
use Sylius\AdminMcpServerPlugin\Tool\Channel\Create as ChannelCreate;
use Sylius\AdminMcpServerPlugin\Tool\Channel\Delete as ChannelDelete;
use Sylius\AdminMcpServerPlugin\Tool\Channel\Index as ChannelIndex;
use Sylius\AdminMcpServerPlugin\Tool\Channel\Show as ChannelShow;
use Sylius\AdminMcpServerPlugin\Tool\Channel\Update as ChannelUpdate;
use Sylius\AdminMcpServerPlugin\Tool\Country\Create as CountryCreate;
use Sylius\AdminMcpServerPlugin\Tool\Country\Index as CountryIndex;
use Sylius\AdminMcpServerPlugin\Tool\Country\Show as CountryShow;
use Sylius\AdminMcpServerPlugin\Tool\Country\Update as CountryUpdate;
use Sylius\AdminMcpServerPlugin\Tool\Currency\Create as CurrencyCreate;
use Sylius\AdminMcpServerPlugin\Tool\Currency\Index as CurrencyIndex;
use Sylius\AdminMcpServerPlugin\Tool\Customer\Create as CustomerCreate;
use Sylius\AdminMcpServerPlugin\Tool\Customer\DeleteUser as CustomerDeleteUser;
use Sylius\AdminMcpServerPlugin\Tool\Customer\GetStatistics as CustomerGetStatistics;
use Sylius\AdminMcpServerPlugin\Tool\Customer\Index as CustomerIndex;
use Sylius\AdminMcpServerPlugin\Tool\Customer\Show as CustomerShow;
use Sylius\AdminMcpServerPlugin\Tool\Customer\Update as CustomerUpdate;
use Sylius\AdminMcpServerPlugin\Tool\CustomerGroup\Create as CustomerGroupCreate;
use Sylius\AdminMcpServerPlugin\Tool\CustomerGroup\Index as CustomerGroupIndex;
use Sylius\AdminMcpServerPlugin\Tool\CustomerGroup\Show as CustomerGroupShow;
use Sylius\AdminMcpServerPlugin\Tool\CustomerGroup\Update as CustomerGroupUpdate;
use Sylius\AdminMcpServerPlugin\Tool\ExchangeRate\Create as ExchangeRateCreate;
use Sylius\AdminMcpServerPlugin\Tool\ExchangeRate\Index as ExchangeRateIndex;
use Sylius\AdminMcpServerPlugin\Tool\ExchangeRate\Update as ExchangeRateUpdate;
use Sylius\AdminMcpServerPlugin\Tool\Locale\Create as LocaleCreate;
use Sylius\AdminMcpServerPlugin\Tool\Locale\Delete as LocaleDelete;
use Sylius\AdminMcpServerPlugin\Tool\Locale\Index as LocaleIndex;
use Sylius\AdminMcpServerPlugin\Tool\PaymentMethod\Create as PaymentMethodCreate;
use Sylius\AdminMcpServerPlugin\Tool\PaymentMethod\Index as PaymentMethodIndex;
use Sylius\AdminMcpServerPlugin\Tool\PaymentMethod\Show as PaymentMethodShow;
use Sylius\AdminMcpServerPlugin\Tool\PaymentMethod\Update as PaymentMethodUpdate;
use Sylius\AdminMcpServerPlugin\Tool\Product\Create as ProductCreate;
use Sylius\AdminMcpServerPlugin\Tool\Product\Delete as ProductDelete;
use Sylius\AdminMcpServerPlugin\Tool\Product\Index as ProductIndex;
use Sylius\AdminMcpServerPlugin\Tool\Product\Show as ProductShow;
use Sylius\AdminMcpServerPlugin\Tool\Product\Update as ProductUpdate;
use Sylius\AdminMcpServerPlugin\Tool\ProductAssociation\Create as ProductAssociationCreate;
use Sylius\AdminMcpServerPlugin\Tool\ProductAssociation\Delete as ProductAssociationDelete;
use Sylius\AdminMcpServerPlugin\Tool\ProductAssociation\Index as ProductAssociationIndex;
use Sylius\AdminMcpServerPlugin\Tool\ProductAssociationType\Create as ProductAssociationTypeCreate;
use Sylius\AdminMcpServerPlugin\Tool\ProductAssociationType\Index as ProductAssociationTypeIndex;
use Sylius\AdminMcpServerPlugin\Tool\ProductAttribute\Create as ProductAttributeCreate;
use Sylius\AdminMcpServerPlugin\Tool\ProductAttribute\Index as ProductAttributeIndex;
use Sylius\AdminMcpServerPlugin\Tool\ProductAttribute\Show as ProductAttributeShow;
use Sylius\AdminMcpServerPlugin\Tool\ProductAttribute\Update as ProductAttributeUpdate;
use Sylius\AdminMcpServerPlugin\Tool\ProductOption\Create as ProductOptionCreate;
use Sylius\AdminMcpServerPlugin\Tool\ProductOption\Index as ProductOptionIndex;
use Sylius\AdminMcpServerPlugin\Tool\ProductOption\Show as ProductOptionShow;
use Sylius\AdminMcpServerPlugin\Tool\ProductOption\Update as ProductOptionUpdate;
use Sylius\AdminMcpServerPlugin\Tool\ProductReview\Accept as ProductReviewAccept;
use Sylius\AdminMcpServerPlugin\Tool\ProductReview\Delete as ProductReviewDelete;
use Sylius\AdminMcpServerPlugin\Tool\ProductReview\Index as ProductReviewIndex;
use Sylius\AdminMcpServerPlugin\Tool\ProductReview\Reject as ProductReviewReject;
use Sylius\AdminMcpServerPlugin\Tool\ProductReview\Show as ProductReviewShow;
use Sylius\AdminMcpServerPlugin\Tool\ProductReview\Update as ProductReviewUpdate;
use Sylius\AdminMcpServerPlugin\Tool\ProductTaxon\Create as ProductTaxonCreate;
use Sylius\AdminMcpServerPlugin\Tool\ProductTaxon\Delete as ProductTaxonDelete;
use Sylius\AdminMcpServerPlugin\Tool\ProductTaxon\Index as ProductTaxonIndex;
use Sylius\AdminMcpServerPlugin\Tool\ProductTaxon\Update as ProductTaxonUpdate;
use Sylius\AdminMcpServerPlugin\Tool\ProductVariant\Create as ProductVariantCreate;
use Sylius\AdminMcpServerPlugin\Tool\ProductVariant\Delete as ProductVariantDelete;
use Sylius\AdminMcpServerPlugin\Tool\ProductVariant\Index as ProductVariantIndex;
use Sylius\AdminMcpServerPlugin\Tool\ProductVariant\Show as ProductVariantShow;
use Sylius\AdminMcpServerPlugin\Tool\ProductVariant\Update as ProductVariantUpdate;
use Sylius\AdminMcpServerPlugin\Tool\TaxCategory\Create as TaxCategoryCreate;
use Sylius\AdminMcpServerPlugin\Tool\TaxCategory\Index as TaxCategoryIndex;
use Sylius\AdminMcpServerPlugin\Tool\TaxCategory\Show as TaxCategoryShow;
use Sylius\AdminMcpServerPlugin\Tool\TaxCategory\Update as TaxCategoryUpdate;
use Sylius\AdminMcpServerPlugin\Tool\TaxRate\Create as TaxRateCreate;
use Sylius\AdminMcpServerPlugin\Tool\TaxRate\Index as TaxRateIndex;
use Sylius\AdminMcpServerPlugin\Tool\TaxRate\Show as TaxRateShow;
use Sylius\AdminMcpServerPlugin\Tool\TaxRate\Update as TaxRateUpdate;
use Sylius\AdminMcpServerPlugin\Tool\Taxon\Create as TaxonCreate;
use Sylius\AdminMcpServerPlugin\Tool\Taxon\Delete as TaxonDelete;
use Sylius\AdminMcpServerPlugin\Tool\Taxon\Index as TaxonIndex;
use Sylius\AdminMcpServerPlugin\Tool\Taxon\Show as TaxonShow;
use Sylius\AdminMcpServerPlugin\Tool\Taxon\Update as TaxonUpdate;

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

    $services->set(ShippingMethodIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ShippingMethodShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ShippingMethodCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ShippingMethodUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ShippingMethodDelete::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ShippingMethodArchive::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ShippingMethodRestore::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ShippingCategoryIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ZoneIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ZoneShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ZoneCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(ZoneUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(PromotionIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(PromotionShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(PromotionCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(PromotionUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(PromotionDelete::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(PromotionArchive::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(PromotionRestore::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CouponIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CouponCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CouponUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CouponDelete::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CouponGenerate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CatalogPromotionIndex::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CatalogPromotionShow::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CatalogPromotionCreate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');

    $services->set(CatalogPromotionUpdate::class)
        ->args([service('sylius_admin_mcp_server.client.api')])
        ->tag('mcp.tool');
};
