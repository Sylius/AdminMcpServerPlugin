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

use Sylius\AdminMcpServerPlugin\OAuth\Authorization\AuthorizationConsentProcessor;
use Sylius\AdminMcpServerPlugin\OAuth\Exception\OAuthException;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final readonly class AuthorizationController
{
    public function __construct(
        private AuthorizationConsentProcessor $processor,
        private Security $security,
        private Environment $twig,
        private UrlGeneratorInterface $urlGenerator,
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

        try {
            $client = $this->processor->resolveClient($responseType, $clientId, $redirectUri);
        } catch (OAuthException $e) {
            return new Response($e->getDescription(), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->processor->validatePkce($codeChallenge, $codeChallengeMethod);
        } catch (OAuthException $e) {
            return $this->renderError(
                'sylius_admin_mcp_server.oauth.error.generic',
                $this->processor->buildErrorUrl($redirectUri, $state, $e->getError(), $e->getDescription()),
            );
        }

        $user = $this->security->getUser();
        if (!$user instanceof AdminUserInterface) {
            return new RedirectResponse(
                $this->urlGenerator->generate('sylius_admin_security_login', ['_target_path' => $request->getUri()]),
            );
        }

        if (!$this->processor->hasApiAccess($user)) {
            return $this->renderError(
                'sylius_admin_mcp_server.oauth.error.no_api_access',
                $this->processor->buildErrorUrl($redirectUri, $state, 'access_denied', 'User does not have API access'),
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
                $this->processor->buildErrorUrl($redirectUri, $state, 'access_denied', 'User denied the request'),
            );
        }

        try {
            $client = $this->processor->resolveClient('code', $clientId, $redirectUri);
        } catch (OAuthException $e) {
            return new Response('Invalid request', Response::HTTP_BAD_REQUEST);
        }

        $user = $this->security->getUser();
        if (!$user instanceof AdminUserInterface) {
            return new RedirectResponse($this->urlGenerator->generate('sylius_admin_security_login'));
        }

        if (!$this->processor->hasApiAccess($user)) {
            return $this->renderError(
                'sylius_admin_mcp_server.oauth.error.no_api_access',
                $this->processor->buildErrorUrl($redirectUri, $state, 'access_denied', 'User does not have API access'),
            );
        }

        $successUrl = $this->processor->grantConsent(
            $client,
            $user,
            $redirectUri,
            $scope,
            $state,
            $codeChallenge,
            $codeChallengeMethod,
        );

        return $this->renderSuccess($successUrl);
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
