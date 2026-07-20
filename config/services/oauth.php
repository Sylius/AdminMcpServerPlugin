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

use Sylius\AdminMcpServerPlugin\Controller\OAuth\AuthorizationController;
use Sylius\AdminMcpServerPlugin\Controller\OAuth\RegistrationController;
use Sylius\AdminMcpServerPlugin\Controller\OAuth\TokenController;
use Sylius\AdminMcpServerPlugin\Controller\OAuth\WellKnownController;
use Sylius\AdminMcpServerPlugin\Form\Extension\AdminUserTypeExtension;
use Sylius\AdminMcpServerPlugin\OAuth\AuthorizationCodeIssuer;
use Sylius\AdminMcpServerPlugin\OAuth\OAuthCallbackUrlBuilder;
use Sylius\AdminMcpServerPlugin\OAuth\TokenIssuer;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAccessTokenRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAuthorizationCodeRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthClientRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthRefreshTokenRepository;
use Sylius\AdminMcpServerPlugin\Security\McpAccessTokenHandler;
use Sylius\AdminMcpServerPlugin\Security\McpBearerAuthListener;
use Sylius\AdminMcpServerPlugin\Security\PkceVerifier;
use Sylius\AdminMcpServerPlugin\Security\RedirectUriValidator;
use Sylius\AdminMcpServerPlugin\Security\TokenHasher;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\KernelEvents;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Repositories
    $services->set(OAuthClientRepository::class)
        ->args([service('doctrine')]);

    $services->set(OAuthAccessTokenRepository::class)
        ->args([service('doctrine')]);

    $services->set(OAuthAuthorizationCodeRepository::class)
        ->args([service('doctrine')]);

    $services->set(OAuthRefreshTokenRepository::class)
        ->args([service('doctrine')]);

    // Security helpers
    $services->set(TokenHasher::class);

    $services->set(PkceVerifier::class);

    $services->set(RedirectUriValidator::class);

    // OAuth domain services
    $services->set(OAuthCallbackUrlBuilder::class);

    $services->set(AuthorizationCodeIssuer::class)
        ->args([
            service(OAuthAuthorizationCodeRepository::class),
        ]);

    $services->set(TokenIssuer::class)
        ->args([
            service(OAuthAccessTokenRepository::class),
            service(OAuthRefreshTokenRepository::class),
            service('doctrine.orm.entity_manager'),
        ]);

    // Security infrastructure
    $services->set('sylius_admin_mcp_server.security.mcp_access_token_handler', McpAccessTokenHandler::class)
        ->args([
            service(OAuthAccessTokenRepository::class),
            service(TokenHasher::class),
        ]);

    $services->set(McpBearerAuthListener::class)
        ->args([
            service(OAuthAccessTokenRepository::class),
            service(TokenHasher::class),
        ])
        ->tag('kernel.event_listener', ['event' => KernelEvents::REQUEST, 'method' => '__invoke', 'priority' => 10]);

    // Controllers
    $services->set(WellKnownController::class)
        ->args([service('router')])
        ->tag('controller.service_arguments');

    $services->set(RegistrationController::class)
        ->args([
            service(OAuthClientRepository::class),
            service(RedirectUriValidator::class),
        ])
        ->tag('controller.service_arguments');

    $services->set(AuthorizationController::class)
        ->args([
            service(OAuthClientRepository::class),
            service(AuthorizationCodeIssuer::class),
            service(OAuthCallbackUrlBuilder::class),
            service(Security::class),
            service('twig'),
        ])
        ->tag('controller.service_arguments');

    $services->set(TokenController::class)
        ->args([
            service(OAuthClientRepository::class),
            service(OAuthAuthorizationCodeRepository::class),
            service(OAuthRefreshTokenRepository::class),
            service(PkceVerifier::class),
            service(TokenIssuer::class),
            service(TokenHasher::class),
        ])
        ->tag('controller.service_arguments');

    // Form extensions
    $services->set(AdminUserTypeExtension::class)
        ->tag('form.type_extension');
};
