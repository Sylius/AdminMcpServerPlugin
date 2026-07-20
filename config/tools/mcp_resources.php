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

use Sylius\AdminMcpServerPlugin\Mcp\Resource\SyliusGuidelinesResource;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(SyliusGuidelinesResource::class)
        ->tag('mcp.resource');
};
