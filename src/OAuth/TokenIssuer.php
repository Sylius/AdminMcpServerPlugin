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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthAccessToken;
use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthClient;
use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthRefreshToken;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAccessTokenRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthRefreshTokenRepository;
use Sylius\Component\Core\Model\AdminUserInterface;

final readonly class TokenIssuer
{
    public function __construct(
        private OAuthAccessTokenRepository $accessTokenRepository,
        private OAuthRefreshTokenRepository $refreshTokenRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function issue(OAuthClient $client, AdminUserInterface $user, string $scope): IssuedTokenPair
    {
        $plainAccessToken = bin2hex(random_bytes(32));
        $accessToken = OAuthAccessToken::issue($client, $user, $scope, $plainAccessToken);
        $this->accessTokenRepository->save($accessToken);

        $plainRefreshToken = bin2hex(random_bytes(32));
        $refreshToken = OAuthRefreshToken::issue($accessToken, $plainRefreshToken);
        $this->refreshTokenRepository->save($refreshToken);

        $this->entityManager->flush();

        return new IssuedTokenPair($plainAccessToken, $plainRefreshToken, $accessToken->getExpiresIn(), $scope);
    }
}
