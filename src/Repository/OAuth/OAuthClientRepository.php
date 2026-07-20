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
use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthClient;

/** @extends ServiceEntityRepository<OAuthClient> */
class OAuthClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OAuthClient::class);
    }

    public function findByClientId(string $clientId): ?OAuthClient
    {
        return $this->findOneBy(['clientId' => $clientId]);
    }

    public function save(OAuthClient $client): void
    {
        $this->getEntityManager()->persist($client);
        $this->getEntityManager()->flush();
    }
}
