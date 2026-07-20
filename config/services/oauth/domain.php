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

use Sylius\AdminMcpServerPlugin\OAuth\AuthorizationCodeIssuer;
use Sylius\AdminMcpServerPlugin\OAuth\OAuthCallbackUrlBuilder;
use Sylius\AdminMcpServerPlugin\OAuth\TokenIssuer;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAccessTokenRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAuthorizationCodeRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthRefreshTokenRepository;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

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
};
