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

use Sylius\AdminMcpServerPlugin\OAuth\Authorization\AuthorizationConsentProcessor;
use Sylius\AdminMcpServerPlugin\OAuth\AuthorizationCodeIssuer;
use Sylius\AdminMcpServerPlugin\OAuth\Grant\AuthorizationCodeGrantHandler;
use Sylius\AdminMcpServerPlugin\OAuth\Grant\RefreshTokenGrantHandler;
use Sylius\AdminMcpServerPlugin\OAuth\Metadata\OAuthServerMetadataProvider;
use Sylius\AdminMcpServerPlugin\OAuth\OAuthCallbackUrlBuilder;
use Sylius\AdminMcpServerPlugin\OAuth\Registration\ClientRegistrar;
use Sylius\AdminMcpServerPlugin\OAuth\TokenIssuer;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.oauth.callback_url_builder', OAuthCallbackUrlBuilder::class);

    $services->set('sylius_admin_mcp_server.oauth.authorization_code_issuer', AuthorizationCodeIssuer::class)
        ->args([
            service('sylius_admin_mcp_server.repository.oauth.authorization_code'),
        ]);

    $services->set('sylius_admin_mcp_server.oauth.token_issuer', TokenIssuer::class)
        ->args([
            service('sylius_admin_mcp_server.repository.oauth.access_token'),
            service('sylius_admin_mcp_server.repository.oauth.refresh_token'),
            service('doctrine.orm.entity_manager'),
        ]);

    $services->set('sylius_admin_mcp_server.oauth.grant.authorization_code_handler', AuthorizationCodeGrantHandler::class)
        ->args([
            service('sylius_admin_mcp_server.repository.oauth.client'),
            service('sylius_admin_mcp_server.repository.oauth.authorization_code'),
            service('sylius_admin_mcp_server.security.oauth.pkce_verifier'),
            service('sylius_admin_mcp_server.oauth.token_issuer'),
            service('sylius_admin_mcp_server.security.oauth.token_hasher'),
        ]);

    $services->set('sylius_admin_mcp_server.oauth.grant.refresh_token_handler', RefreshTokenGrantHandler::class)
        ->args([
            service('sylius_admin_mcp_server.repository.oauth.client'),
            service('sylius_admin_mcp_server.repository.oauth.refresh_token'),
            service('sylius_admin_mcp_server.oauth.token_issuer'),
            service('sylius_admin_mcp_server.security.oauth.token_hasher'),
        ]);

    $services->set('sylius_admin_mcp_server.oauth.consent_processor', AuthorizationConsentProcessor::class)
        ->args([
            service('sylius_admin_mcp_server.repository.oauth.client'),
            service('sylius_admin_mcp_server.oauth.authorization_code_issuer'),
            service('sylius_admin_mcp_server.oauth.callback_url_builder'),
        ]);

    $services->set('sylius_admin_mcp_server.oauth.client_registrar', ClientRegistrar::class)
        ->args([
            service('sylius_admin_mcp_server.repository.oauth.client'),
            service('sylius_admin_mcp_server.security.oauth.redirect_uri_validator'),
        ]);

    $services->set('sylius_admin_mcp_server.oauth.metadata_provider', OAuthServerMetadataProvider::class)
        ->args([service('router')]);
};
