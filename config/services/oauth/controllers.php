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

use League\OAuth2\Server\AuthorizationServer;
use Sylius\AdminMcpServerPlugin\Controller\OAuth\AuthorizationController;
use Sylius\AdminMcpServerPlugin\Controller\OAuth\RegistrationController;
use Sylius\AdminMcpServerPlugin\Controller\OAuth\TokenController;
use Sylius\AdminMcpServerPlugin\Controller\OAuth\WellKnownController;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.controller.oauth.well_known', WellKnownController::class)
        ->args([service('sylius_admin_mcp_server.oauth.metadata_provider')])
        ->tag('controller.service_arguments');
    $services->alias(WellKnownController::class, 'sylius_admin_mcp_server.controller.oauth.well_known')
        ->public();

    $services->set('sylius_admin_mcp_server.controller.oauth.registration', RegistrationController::class)
        ->args([service('sylius_admin_mcp_server.oauth.client_registrar')])
        ->tag('controller.service_arguments');
    $services->alias(RegistrationController::class, 'sylius_admin_mcp_server.controller.oauth.registration')
        ->public();

    $services->set('sylius_admin_mcp_server.controller.oauth.authorization', AuthorizationController::class)
        ->args([
            service(AuthorizationServer::class),
            service('security.helper'),
            service('twig'),
            service('router'),
            service('league.oauth2_server.factory.psr_http'),
        ])
        ->tag('controller.service_arguments');
    $services->alias(AuthorizationController::class, 'sylius_admin_mcp_server.controller.oauth.authorization')
        ->public();

    $services->set('sylius_admin_mcp_server.controller.oauth.token', TokenController::class)
        ->args([
            service(AuthorizationServer::class),
            service('league.oauth2_server.factory.psr_http'),
            service('league.oauth2_server.factory.http_foundation'),
        ])
        ->tag('controller.service_arguments');
    $services->alias(TokenController::class, 'sylius_admin_mcp_server.controller.oauth.token')
        ->public();
};
