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

use Sylius\AdminMcpServerPlugin\Form\Extension\AdminUserTypeExtension;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(AdminUserTypeExtension::class)
        ->tag('form.type_extension');
};
