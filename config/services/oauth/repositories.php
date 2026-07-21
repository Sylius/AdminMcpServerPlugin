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

    $services->set('sylius_admin_mcp_server.repository.oauth.client', OAuthClientRepository::class)
        ->args([service('doctrine')]);
    $services->alias(OAuthClientRepository::class, 'sylius_admin_mcp_server.repository.oauth.client');

    $services->set('sylius_admin_mcp_server.repository.oauth.access_token', OAuthAccessTokenRepository::class)
        ->args([service('doctrine')]);
    $services->alias(OAuthAccessTokenRepository::class, 'sylius_admin_mcp_server.repository.oauth.access_token');

    $services->set('sylius_admin_mcp_server.repository.oauth.authorization_code', OAuthAuthorizationCodeRepository::class)
        ->args([service('doctrine')]);
    $services->alias(OAuthAuthorizationCodeRepository::class, 'sylius_admin_mcp_server.repository.oauth.authorization_code');

    $services->set('sylius_admin_mcp_server.repository.oauth.refresh_token', OAuthRefreshTokenRepository::class)
        ->args([service('doctrine')]);
    $services->alias(OAuthRefreshTokenRepository::class, 'sylius_admin_mcp_server.repository.oauth.refresh_token');
};
