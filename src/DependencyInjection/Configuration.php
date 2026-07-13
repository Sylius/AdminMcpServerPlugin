<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_admin_mcp_server');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('tools')
                    ->addDefaultsIfNotSet()
                    ->info('Enable or disable individual tool groups. All groups are enabled by default.')
                    ->children()
                        ->booleanNode('auth')->defaultTrue()->info('Login / Logout tools.')->end()
                        ->booleanNode('administrators')->defaultTrue()->info('Administrator CRUD tools.')->end()
                        ->booleanNode('products')->defaultTrue()->info('Product CRUD tools.')->end()
                        ->booleanNode('product_variants')->defaultTrue()->info('Product variant CRUD tools.')->end()
                        ->booleanNode('product_attributes')->defaultTrue()->info('Product attribute tools.')->end()
                        ->booleanNode('product_options')->defaultTrue()->info('Product option tools.')->end()
                        ->booleanNode('product_reviews')->defaultTrue()->info('Product review tools.')->end()
                        ->booleanNode('product_taxons')->defaultTrue()->info('Product–taxon assignment tools.')->end()
                        ->booleanNode('product_associations')->defaultTrue()->info('Product association tools.')->end()
                        ->booleanNode('product_association_types')->defaultTrue()->info('Product association type tools.')->end()
                        ->booleanNode('taxons')->defaultTrue()->info('Taxon CRUD tools.')->end()
                        ->booleanNode('customers')->defaultTrue()->info('Customer tools.')->end()
                        ->booleanNode('customer_groups')->defaultTrue()->info('Customer group tools.')->end()
                        ->booleanNode('addresses')->defaultTrue()->info('Customer address tools.')->end()
                        ->booleanNode('channels')->defaultTrue()->info('Channel CRUD tools.')->end()
                        ->booleanNode('currencies')->defaultTrue()->info('Currency tools.')->end()
                        ->booleanNode('exchange_rates')->defaultTrue()->info('Exchange rate tools.')->end()
                        ->booleanNode('locales')->defaultTrue()->info('Locale tools.')->end()
                        ->booleanNode('countries')->defaultTrue()->info('Country tools.')->end()
                        ->booleanNode('payment_methods')->defaultTrue()->info('Payment method tools.')->end()
                        ->booleanNode('tax_categories')->defaultTrue()->info('Tax category tools.')->end()
                        ->booleanNode('tax_rates')->defaultTrue()->info('Tax rate tools.')->end()
                        ->booleanNode('shipping_methods')->defaultTrue()->info('Shipping method CRUD and archive/restore tools.')->end()
                        ->booleanNode('shipping_categories')->defaultTrue()->info('Shipping category tools.')->end()
                        ->booleanNode('zones')->defaultTrue()->info('Zone CRUD tools.')->end()
                        ->booleanNode('zone_members')->defaultTrue()->info('Zone member tools.')->end()
                        ->booleanNode('promotions')->defaultTrue()->info('Cart promotion CRUD and archive/restore tools.')->end()
                        ->booleanNode('coupons')->defaultTrue()->info('Promotion coupon tools.')->end()
                        ->booleanNode('catalog_promotions')->defaultTrue()->info('Catalog promotion CRUD tools.')->end()
                        ->booleanNode('shipments')->defaultTrue()->info('Shipment management tools (global list/get/ship/resend-email).')->end()
                        ->booleanNode('payments')->defaultTrue()->info('Payment management tools (global list/get).')->end()
                        ->booleanNode('orders')->defaultTrue()->info('Order management tools.')->end()
                        ->booleanNode('provinces')->defaultTrue()->info('Province CRUD tools.')->end()
                        ->booleanNode('product_images')->defaultTrue()->info('Product image tools.')->end()
                        ->booleanNode('taxon_images')->defaultTrue()->info('Taxon image tools.')->end()
                        ->booleanNode('mcp_resources')->defaultTrue()->info('MCP resources (e.g. Sylius guidelines).')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
