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

use Sylius\AdminMcpServerPlugin\OAuth\Metadata\OAuthServerMetadataProvider;
use Sylius\AdminMcpServerPlugin\OAuth\Registration\ClientRegistrar;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.oauth.client_registrar', ClientRegistrar::class)
        ->args([
            service('sylius_admin_mcp_server.repository.oauth.client'),
        ]);

    $services->set('sylius_admin_mcp_server.oauth.metadata_provider', OAuthServerMetadataProvider::class)
        ->args([service('router')]);
};
