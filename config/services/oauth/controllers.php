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
use Sylius\AdminMcpServerPlugin\OAuth\AuthorizationCodeIssuer;
use Sylius\AdminMcpServerPlugin\OAuth\OAuthCallbackUrlBuilder;
use Sylius\AdminMcpServerPlugin\OAuth\TokenIssuer;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAuthorizationCodeRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthClientRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthRefreshTokenRepository;
use Sylius\AdminMcpServerPlugin\Security\PkceVerifier;
use Sylius\AdminMcpServerPlugin\Security\RedirectUriValidator;
use Sylius\AdminMcpServerPlugin\Security\TokenHasher;
use Symfony\Bundle\SecurityBundle\Security;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

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
};
