<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\DependencyInjection;

use Sylius\Bundle\CoreBundle\DependencyInjection\PrependDoctrineMigrationsTrait;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class SyliusAdminMcpServerExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    /**
     * Maps config key (under `tools`) to the corresponding file in config/tools/.
     */
    private const TOOL_FILE_MAP = [
        'auth'                    => 'auth',
        'administrators'          => 'administrator',
        'products'                => 'product',
        'product_variants'        => 'product_variant',
        'product_attributes'      => 'product_attribute',
        'product_options'         => 'product_option',
        'product_reviews'         => 'product_review',
        'product_taxons'          => 'product_taxon',
        'product_associations'    => 'product_association',
        'product_association_types' => 'product_association_type',
        'taxons'                  => 'taxon',
        'customers'               => 'customer',
        'customer_groups'         => 'customer_group',
        'addresses'               => 'address',
        'channels'                => 'channel',
        'currencies'              => 'currency',
        'exchange_rates'          => 'exchange_rate',
        'locales'                 => 'locale',
        'countries'               => 'country',
        'payment_methods'         => 'payment_method',
        'tax_categories'          => 'tax_category',
        'tax_rates'               => 'tax_rate',
        'shipping_methods'        => 'shipping_method',
        'shipping_categories'     => 'shipping_category',
        'zones'                   => 'zone',
        'zone_members'            => 'zone_member',
        'promotions'              => 'promotion',
        'coupons'                 => 'coupon',
        'catalog_promotions'      => 'catalog_promotion',
        'shipments'               => 'shipment',
        'payments'                => 'payment',
        'orders'                  => 'order',
        'provinces'               => 'province',
        'product_images'          => 'product_image',
        'taxon_images'            => 'taxon_image',
        'mcp_resources'           => 'mcp_resources',
    ];

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.php');

        $toolsLoader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config/tools'));

        foreach (self::TOOL_FILE_MAP as $configKey => $fileName) {
            if ($config['tools'][$configKey]) {
                $toolsLoader->load($fileName . '.php');
            }
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDoctrineMigrations($container);
        $container->prependExtensionConfig('api_platform', [
            'mapping' => [
                'paths' => [__DIR__ . '/../../config/api_platform'],
            ],
        ]);
    }

    protected function getMigrationsNamespace(): string
    {
        return 'DoctrineMigrations';
    }

    protected function getMigrationsDirectory(): string
    {
        return '@SyliusAdminMcpServerPlugin/src/Migrations';
    }

    protected function getNamespacesOfMigrationsExecutedBefore(): array
    {
        return [
            'Sylius\Bundle\CoreBundle\Migrations',
        ];
    }
}
