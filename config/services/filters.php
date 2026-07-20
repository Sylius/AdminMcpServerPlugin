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

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius_api.search_filter.admin.taxon')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['parent' => 'exact', 'code' => 'partial']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.address')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['customer.id' => 'exact']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.zone_member')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['belongsTo.code' => 'exact']])
        ->tag('api_platform.filter')
    ;
};
