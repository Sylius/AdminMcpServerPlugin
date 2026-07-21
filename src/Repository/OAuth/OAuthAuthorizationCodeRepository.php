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
use League\Bundle\OAuth2ServerBundle\Entity\AuthCode as AuthCodeEntity;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthAuthorizationCode;

final class OAuthAuthorizationCodeRepository implements AuthCodeRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        return new AuthCodeEntity();
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        if ($this->entityManager->find(OAuthAuthorizationCode::class, $authCodeEntity->getIdentifier()) !== null) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $entity = new OAuthAuthorizationCode(
            $authCodeEntity->getIdentifier(),
            $authCodeEntity->getExpiryDateTime(),
        );

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function revokeAuthCode(string $codeId): void
    {
        $entity = $this->entityManager->find(OAuthAuthorizationCode::class, $codeId);

        if ($entity === null) {
            return;
        }

        $entity->revoke();
        $this->entityManager->flush();
    }

    public function isAuthCodeRevoked(string $codeId): bool
    {
        $entity = $this->entityManager->find(OAuthAuthorizationCode::class, $codeId);

        if ($entity === null) {
            return true;
        }

        return $entity->isRevoked();
    }
}
