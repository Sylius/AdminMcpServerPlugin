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
use Sylius\AdminMcpServerPlugin\Security\McpAccessTokenHandler;
use Sylius\AdminMcpServerPlugin\Security\McpBearerAuthListener;
use Sylius\AdminMcpServerPlugin\Security\PkceVerifier;
use Sylius\AdminMcpServerPlugin\Security\RedirectUriValidator;
use Sylius\AdminMcpServerPlugin\Security\TokenHasher;
use Symfony\Component\HttpKernel\KernelEvents;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(TokenHasher::class);

    $services->set(PkceVerifier::class);

    $services->set(RedirectUriValidator::class);

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
};
