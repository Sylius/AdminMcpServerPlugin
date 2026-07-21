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

namespace Sylius\AdminMcpServerPlugin\Repository\OAuth;

use Doctrine\ORM\EntityManagerInterface;
use League\Bundle\OAuth2ServerBundle\Entity\RefreshToken as RefreshTokenEntity;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthRefreshToken;

final class OAuthRefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function getNewRefreshToken(): ?RefreshTokenEntityInterface
    {
        return new RefreshTokenEntity();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        if ($this->entityManager->find(OAuthRefreshToken::class, $refreshTokenEntity->getIdentifier()) !== null) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $entity = new OAuthRefreshToken(
            $refreshTokenEntity->getIdentifier(),
            $refreshTokenEntity->getExpiryDateTime(),
        );

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function revokeRefreshToken(string $tokenId): void
    {
        $entity = $this->entityManager->find(OAuthRefreshToken::class, $tokenId);

        if ($entity === null) {
            return;
        }

        $entity->revoke();
        $this->entityManager->flush();
    }

    public function isRefreshTokenRevoked(string $tokenId): bool
    {
        $entity = $this->entityManager->find(OAuthRefreshToken::class, $tokenId);

        if ($entity === null) {
            return true;
        }

        return $entity->isRevoked();
    }
}
