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

use Sylius\AdminMcpServerPlugin\OAuth\Metadata\OAuthServerMetadataProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class WellKnownController
{
    public function __construct(
        private OAuthServerMetadataProvider $metadataProvider,
    ) {
    }

    public function authorizationServer(Request $request): JsonResponse
    {
        return new JsonResponse($this->metadataProvider->authorizationServer($request->getSchemeAndHttpHost()));
    }

    public function protectedResource(Request $request): JsonResponse
    {
        return new JsonResponse($this->metadataProvider->protectedResource($request->getSchemeAndHttpHost()));
    }
}
