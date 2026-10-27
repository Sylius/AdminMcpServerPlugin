<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Tests\Sylius\AdminMcpServerPlugin\Behat\Context\Ui\Shop\WelcomeContext;
use Tests\Sylius\AdminMcpServerPlugin\Behat\Page\Shop\DynamicWelcomePage;
use Tests\Sylius\AdminMcpServerPlugin\Behat\Page\Shop\StaticWelcomePage;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Put your Behat services here

    $services
        ->set('sylius_admin_mcp_server.context.ui.shop.welcome', WelcomeContext::class)
        ->public()
        ->args([
            service('sylius_admin_mcp_server.page.shop.static_welcome'),
            service('sylius_admin_mcp_server.page.shop.dynamic_welcome'),
        ])
    ;

    $services
        ->set('sylius_admin_mcp_server.page.shop.static_welcome', StaticWelcomePage::class)
        ->parent('sylius.behat.symfony_page')
    ;

    $services
        ->set('sylius_admin_mcp_server.page.shop.dynamic_welcome', DynamicWelcomePage::class)
        ->parent('sylius.behat.symfony_page')
    ;
};
