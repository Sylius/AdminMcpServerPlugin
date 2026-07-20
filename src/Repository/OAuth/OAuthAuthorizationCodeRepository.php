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
use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthAuthorizationCode;

/** @extends ServiceEntityRepository<OAuthAuthorizationCode> */
class OAuthAuthorizationCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OAuthAuthorizationCode::class);
    }

    public function findActiveByCodeHash(string $hash): ?OAuthAuthorizationCode
    {
        /** @var OAuthAuthorizationCode|null $code */
        $code = $this->createQueryBuilder('c')
            ->where('c.codeHash = :hash')
            ->andWhere('c.usedAt IS NULL')
            ->andWhere('c.expiresAt > :now')
            ->setParameter('hash', $hash)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();

        return $code;
    }

    public function save(OAuthAuthorizationCode $code): void
    {
        $this->getEntityManager()->persist($code);
        $this->getEntityManager()->flush();
    }
}
