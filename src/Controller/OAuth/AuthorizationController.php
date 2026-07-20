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

use Sylius\AdminMcpServerPlugin\OAuth\AuthorizationCodeIssuer;
use Sylius\AdminMcpServerPlugin\OAuth\OAuthCallbackUrlBuilder;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthClientRepository;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final readonly class AuthorizationController
{
    public function __construct(
        private OAuthClientRepository $clientRepository,
        private AuthorizationCodeIssuer $codeIssuer,
        private OAuthCallbackUrlBuilder $urlBuilder,
        private Security $security,
        private Environment $twig,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        return $request->isMethod('POST') ? $this->handleConsent($request) : $this->showConsent($request);
    }

    private function showConsent(Request $request): Response
    {
        $clientId = $request->query->getString('client_id');
        $redirectUri = $request->query->getString('redirect_uri');
        $state = $request->query->getString('state');
        $codeChallenge = $request->query->getString('code_challenge');
        $codeChallengeMethod = $request->query->getString('code_challenge_method');
        $scope = $request->query->getString('scope', 'mcp');
        $responseType = $request->query->getString('response_type');

        if ($responseType !== 'code') {
            return new Response('Unsupported response_type', Response::HTTP_BAD_REQUEST);
        }

        $client = $this->clientRepository->findByClientId($clientId);
        if ($client === null) {
            return new Response('Invalid client_id', Response::HTTP_BAD_REQUEST);
        }

        if (!$client->matchesRedirectUri($redirectUri)) {
            return new Response('Invalid redirect_uri', Response::HTTP_BAD_REQUEST);
        }

        if ($codeChallenge === '' || $codeChallengeMethod !== 'S256') {
            return $this->renderError(
                'sylius_admin_mcp_server.oauth.error.generic',
                $this->urlBuilder->buildErrorUrl($redirectUri, $state, 'invalid_request', 'PKCE with S256 is required'),
            );
        }

        $user = $this->security->getUser();
        if (!$user instanceof AdminUserInterface) {
            return new RedirectResponse('/admin/login?_target_path=' . urlencode($request->getUri()));
        }

        if (!$this->hasApiAccess($user)) {
            return $this->renderError(
                'sylius_admin_mcp_server.oauth.error.no_api_access',
                $this->urlBuilder->buildErrorUrl($redirectUri, $state, 'access_denied', 'User does not have API access'),
            );
        }

        return new Response(
            $this->twig->render('@SyliusAdminMcpServer/oauth/authorize.html.twig', [
                'client_name' => $client->getClientName(),
                'scope' => $scope,
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'state' => $state,
                'code_challenge' => $codeChallenge,
                'code_challenge_method' => $codeChallengeMethod,
                'response_type' => $responseType,
            ]),
        );
    }

    private function handleConsent(Request $request): Response
    {
        $approve = $request->request->getString('approve');
        $clientId = $request->request->getString('client_id');
        $redirectUri = $request->request->getString('redirect_uri');
        $state = $request->request->getString('state');
        $codeChallenge = $request->request->getString('code_challenge');
        $codeChallengeMethod = $request->request->getString('code_challenge_method');
        $scope = $request->request->getString('scope', 'mcp');

        if ($approve !== '1') {
            return $this->renderError(
                'sylius_admin_mcp_server.oauth.error.denied_by_user',
                $this->urlBuilder->buildErrorUrl($redirectUri, $state, 'access_denied', 'User denied the request'),
            );
        }

        $client = $this->clientRepository->findByClientId($clientId);
        if ($client === null || !$client->matchesRedirectUri($redirectUri)) {
            return new Response('Invalid request', Response::HTTP_BAD_REQUEST);
        }

        $adminUser = $this->security->getUser();
        if (!$adminUser instanceof AdminUserInterface) {
            return new RedirectResponse('/admin/login');
        }

        if (!$this->hasApiAccess($adminUser)) {
            return $this->renderError(
                'sylius_admin_mcp_server.oauth.error.no_api_access',
                $this->urlBuilder->buildErrorUrl($redirectUri, $state, 'access_denied', 'User does not have API access'),
            );
        }

        $plainCode = $this->codeIssuer->issue(
            $client,
            $adminUser,
            $redirectUri,
            $scope,
            $codeChallenge,
            $codeChallengeMethod,
        );

        return $this->renderSuccess($this->urlBuilder->buildSuccessUrl($redirectUri, $plainCode, $state));
    }

    private function hasApiAccess(AdminUserInterface $user): bool
    {
        return \in_array('ROLE_API_ACCESS', $user->getRoles(), true);
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
