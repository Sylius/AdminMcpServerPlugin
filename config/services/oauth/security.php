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

use League\OAuth2\Server\ResourceServer;
use Sylius\AdminMcpServerPlugin\Security\Mcp\McpBearerAuthListener;
use Sylius\AdminMcpServerPlugin\Security\OAuth\OAuthAuthorizeVoter;
use Symfony\Component\HttpKernel\KernelEvents;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.security.mcp.bearer_auth_listener', McpBearerAuthListener::class)
        ->args([
            service(ResourceServer::class),
            service('league.oauth2_server.factory.psr_http'),
            service('sylius.repository.admin_user'),
        ])
        ->tag('kernel.event_listener', ['event' => KernelEvents::REQUEST, 'method' => '__invoke', 'priority' => 10]);

    $services->set('sylius_admin_mcp_server.security.oauth.authorize_voter', OAuthAuthorizeVoter::class)
        ->tag('security.voter');
};
