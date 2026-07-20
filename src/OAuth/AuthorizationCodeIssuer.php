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

namespace Sylius\AdminMcpServerPlugin\OAuth;

use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthAuthorizationCode;
use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthClient;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAuthorizationCodeRepository;
use Sylius\Component\Core\Model\AdminUserInterface;

final readonly class AuthorizationCodeIssuer
{
    public function __construct(
        private OAuthAuthorizationCodeRepository $codeRepository,
    ) {
    }

    public function issue(
        OAuthClient $client,
        AdminUserInterface $user,
        string $redirectUri,
        string $scope,
        string $codeChallenge,
        string $codeChallengeMethod,
    ): string {
        $plainCode = bin2hex(random_bytes(32));

        $authCode = OAuthAuthorizationCode::issue(
            $client,
            $user,
            $redirectUri,
            $scope,
            $codeChallenge,
            $codeChallengeMethod,
            $plainCode,
        );

        $this->codeRepository->save($authCode);

        return $plainCode;
    }
}
