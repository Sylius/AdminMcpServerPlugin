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

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthRefreshToken;

/** @extends ServiceEntityRepository<OAuthRefreshToken> */
class OAuthRefreshTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OAuthRefreshToken::class);
    }

    public function findActiveByTokenHash(string $hash): ?OAuthRefreshToken
    {
        /** @var OAuthRefreshToken|null $token */
        $token = $this->createQueryBuilder('t')
            ->where('t.tokenHash = :hash')
            ->andWhere('t.revokedAt IS NULL')
            ->andWhere('t.expiresAt > :now')
            ->setParameter('hash', $hash)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();

        return $token;
    }

    public function save(OAuthRefreshToken $token): void
    {
        $this->getEntityManager()->persist($token);
    }
}
