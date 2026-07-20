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

use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAccessTokenRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAuthorizationCodeRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthClientRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthRefreshTokenRepository;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(OAuthClientRepository::class)
        ->args([service('doctrine')]);

    $services->set(OAuthAccessTokenRepository::class)
        ->args([service('doctrine')]);

    $services->set(OAuthAuthorizationCodeRepository::class)
        ->args([service('doctrine')]);

    $services->set(OAuthRefreshTokenRepository::class)
        ->args([service('doctrine')]);
};
