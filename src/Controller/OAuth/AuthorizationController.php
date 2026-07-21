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
use Nyholm\Psr7\ServerRequest;
use Sylius\AdminMcpServerPlugin\OAuth\UserEntity;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final readonly class AuthorizationController
{
    public function __construct(
        private AuthorizationServer $authorizationServer,
        private Security $security,
        private Environment $twig,
        private UrlGeneratorInterface $urlGenerator,
        private PsrHttpFactory $psrHttpFactory,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        return $request->isMethod('POST') ? $this->handleConsent($request) : $this->showConsent($request);
    }

    private function showConsent(Request $request): Response
    {
        $psrRequest = $this->psrHttpFactory->createRequest($request);

        try {
            $authRequest = $this->authorizationServer->validateAuthorizationRequest($psrRequest);
        } catch (OAuthServerException $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $user = $this->security->getUser();
        if (!$user instanceof AdminUserInterface) {
            return new RedirectResponse(
                $this->urlGenerator->generate('sylius_admin_security_login', ['_target_path' => $request->getUri()]),
            );
        }

        if (!\in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return $this->renderError(
                'sylius_admin_mcp_server.oauth.error.no_api_access',
                $authRequest->getRedirectUri() . '?error=access_denied&error_description=User+does+not+have+API+access',
            );
        }

        $scopeIdentifiers = implode(' ', array_map(
            static fn ($s): string => $s->getIdentifier(),
            $authRequest->getScopes(),
        ));

        return new Response(
            $this->twig->render('@SyliusAdminMcpServer/oauth/authorize.html.twig', [
                'client_name' => $authRequest->getClient()->getName(),
                'scope' => $scopeIdentifiers,
                'client_id' => $request->query->getString('client_id'),
                'redirect_uri' => $request->query->getString('redirect_uri'),
                'state' => $request->query->getString('state'),
                'code_challenge' => $request->query->getString('code_challenge'),
                'code_challenge_method' => $request->query->getString('code_challenge_method'),
                'response_type' => $request->query->getString('response_type', 'code'),
            ]),
        );
    }

    private function handleConsent(Request $request): Response
    {
        $approve = $request->request->getString('approve');

        $queryParams = array_filter([
            'response_type' => $request->request->getString('response_type', 'code'),
            'client_id' => $request->request->getString('client_id'),
            'redirect_uri' => $request->request->getString('redirect_uri'),
            'scope' => $request->request->getString('scope', 'admin_api'),
            'state' => $request->request->getString('state'),
            'code_challenge' => $request->request->getString('code_challenge'),
            'code_challenge_method' => $request->request->getString('code_challenge_method'),
        ], static fn (string $v): bool => $v !== '');

        $psrRequest = (new ServerRequest('GET', '/_mcp/oauth/authorize'))->withQueryParams($queryParams);

        try {
            $authRequest = $this->authorizationServer->validateAuthorizationRequest($psrRequest);
        } catch (OAuthServerException $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $user = $this->security->getUser();
        if (!$user instanceof AdminUserInterface) {
            return new RedirectResponse($this->urlGenerator->generate('sylius_admin_security_login'));
        }

        if (!\in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return $this->renderError(
                'sylius_admin_mcp_server.oauth.error.no_api_access',
                $authRequest->getRedirectUri() . '?error=access_denied&error_description=User+does+not+have+API+access',
            );
        }

        $authRequest->setUser(new UserEntity($user->getEmail() ?? ''));
        $authRequest->setAuthorizationApproved($approve === '1');

        try {
            $psrResponse = $this->authorizationServer->completeAuthorizationRequest($authRequest, new Psr7Response());
        } catch (OAuthServerException $e) {
            $redirectUrl = $e->generateHttpResponse(new Psr7Response())->getHeaderLine('Location');

            return $this->renderError('sylius_admin_mcp_server.oauth.error.denied_by_user', $redirectUrl);
        }

        $redirectUrl = $psrResponse->getHeaderLine('Location');

        return $this->renderSuccess($redirectUrl);
    }

    private function renderSuccess(string $redirectUrl): Response
    {
        return new Response(
            $this->twig->render('@SyliusAdminMcpServer/oauth/success.html.twig', [
                'redirect_url' => $redirectUrl,
            ]),
        );
    }

    private function renderError(string $messageKey, string $redirectUrl): Response
    {
        return new Response(
            $this->twig->render('@SyliusAdminMcpServer/oauth/error.html.twig', [
                'message' => $messageKey,
                'redirect_url' => $redirectUrl,
            ]),
        );
    }
}
