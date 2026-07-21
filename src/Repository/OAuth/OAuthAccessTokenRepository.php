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
use League\Bundle\OAuth2ServerBundle\Entity\AccessToken as AccessTokenEntity;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthAccessToken;

class OAuthAccessTokenRepository implements AccessTokenRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param ScopeEntityInterface[] $scopes
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, ?string $userIdentifier = null): AccessTokenEntityInterface
    {
        $token = new AccessTokenEntity();
        $token->setClient($clientEntity);

        if ($userIdentifier !== null && $userIdentifier !== '') {
            $token->setUserIdentifier($userIdentifier);
        }

        foreach ($scopes as $scope) {
            $token->addScope($scope);
        }

        return $token;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        if ($this->entityManager->find(OAuthAccessToken::class, $accessTokenEntity->getIdentifier()) !== null) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $entity = new OAuthAccessToken(
            $accessTokenEntity->getIdentifier(),
            $accessTokenEntity->getExpiryDateTime(),
        );

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function revokeAccessToken(string $tokenId): void
    {
        $entity = $this->entityManager->find(OAuthAccessToken::class, $tokenId);

        if ($entity === null) {
            return;
        }

        $entity->revoke();
        $this->entityManager->flush();
    }

    public function isAccessTokenRevoked(string $tokenId): bool
    {
        $entity = $this->entityManager->find(OAuthAccessToken::class, $tokenId);

        if ($entity === null) {
            return true;
        }

        return $entity->isRevoked();
    }
}
