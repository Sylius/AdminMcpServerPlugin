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
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthClient;

/** @extends ServiceEntityRepository<OAuthClient> */
final class OAuthClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
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

    public function getClientEntity(string $clientIdentifier): ?ClientEntityInterface
    {
        return $this->findByClientId($clientIdentifier);
    }

    public function validateClient(string $clientIdentifier, #[\SensitiveParameter] ?string $clientSecret, ?string $grantType): bool
    {
        $client = $this->findByClientId($clientIdentifier);

        if ($client === null) {
            return false;
        }

        if ($grantType !== null && !\in_array($grantType, $client->getGrantTypes(), true)) {
            return false;
        }

        if (!$client->isConfidential()) {
            return true;
        }

        if ($clientSecret === null) {
            return false;
        }

        return $client->verifySecret($clientSecret);
    }
}
