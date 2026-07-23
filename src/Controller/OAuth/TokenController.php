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

namespace Sylius\AdminMcpServerPlugin\Controller\OAuth;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Nyholm\Psr7\Response as Psr7Response;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class TokenController
{
    public function __construct(
        private AuthorizationServer $authorizationServer,
        private PsrHttpFactory $psrHttpFactory,
        private HttpFoundationFactory $httpFoundationFactory,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $psrRequest = $this->psrHttpFactory->createRequest($request);
        $psrResponse = new Psr7Response();

        try {
            $psrResponse = $this->authorizationServer->respondToAccessTokenRequest($psrRequest, $psrResponse);
        } catch (OAuthServerException $exception) {
            $psrResponse = $exception->generateHttpResponse($psrResponse);
        }

        return $this->httpFoundationFactory->createResponse($psrResponse);
    }
}
