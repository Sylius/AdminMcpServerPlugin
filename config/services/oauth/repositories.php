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

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAccessTokenRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAuthorizationCodeRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthClientRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthRefreshTokenRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthScopeRepository;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.repository.oauth.client', OAuthClientRepository::class)
        ->args([service('doctrine')]);
    $services->alias(OAuthClientRepository::class, 'sylius_admin_mcp_server.repository.oauth.client');
    $services->alias(ClientRepositoryInterface::class, 'sylius_admin_mcp_server.repository.oauth.client');

    $services->set('sylius_admin_mcp_server.repository.oauth.access_token', OAuthAccessTokenRepository::class);
    $services->alias(OAuthAccessTokenRepository::class, 'sylius_admin_mcp_server.repository.oauth.access_token');
    $services->alias(AccessTokenRepositoryInterface::class, 'sylius_admin_mcp_server.repository.oauth.access_token');

    $services->set('sylius_admin_mcp_server.repository.oauth.authorization_code', OAuthAuthorizationCodeRepository::class)
        ->args([service('doctrine.orm.entity_manager')]);
    $services->alias(OAuthAuthorizationCodeRepository::class, 'sylius_admin_mcp_server.repository.oauth.authorization_code');
    $services->alias(AuthCodeRepositoryInterface::class, 'sylius_admin_mcp_server.repository.oauth.authorization_code');

    $services->set('sylius_admin_mcp_server.repository.oauth.refresh_token', OAuthRefreshTokenRepository::class)
        ->args([service('doctrine.orm.entity_manager')]);
    $services->alias(OAuthRefreshTokenRepository::class, 'sylius_admin_mcp_server.repository.oauth.refresh_token');
    $services->alias(RefreshTokenRepositoryInterface::class, 'sylius_admin_mcp_server.repository.oauth.refresh_token');

    $services->set('sylius_admin_mcp_server.repository.oauth.scope', OAuthScopeRepository::class);
    $services->alias(OAuthScopeRepository::class, 'sylius_admin_mcp_server.repository.oauth.scope');
    $services->alias(ScopeRepositoryInterface::class, 'sylius_admin_mcp_server.repository.oauth.scope');
};
