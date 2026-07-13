<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\AdminMcpServerPlugin\Mcp\Resource\SyliusGuidelinesResource;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(SyliusGuidelinesResource::class)
        ->tag('mcp.resource');
};
