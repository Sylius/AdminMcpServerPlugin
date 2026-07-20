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

namespace Sylius\AdminMcpServerPlugin\Entity\OAuth;

use Doctrine\ORM\Mapping as ORM;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAccessTokenRepository;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OAuthAccessTokenRepository::class)]
#[ORM\Table(name: 'sylius_admin_mcp_oauth_access_tokens')]
class OAuthAccessToken
{
    private const int TTL_SECONDS = 3600;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(length: 64, unique: true)]
    private string $tokenHash;

    #[ORM\ManyToOne(targetEntity: OAuthClient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private OAuthClient $client;

    #[ORM\ManyToOne(targetEntity: 'Sylius\Component\Core\Model\AdminUser')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private AdminUserInterface $adminUser;

    #[ORM\Column(length: 500)]
    private string $scopes;

    #[ORM\Column]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $revokedAt = null;

    private function __construct(
        string $tokenHash,
        OAuthClient $client,
        AdminUserInterface $adminUser,
        string $scopes,
    ) {
        $this->id = Uuid::v7();
        $this->tokenHash = $tokenHash;
        $this->client = $client;
        $this->adminUser = $adminUser;
        $this->scopes = $scopes;
        $this->expiresAt = new \DateTimeImmutable('+' . self::TTL_SECONDS . ' seconds');
    }

    public static function issue(
        OAuthClient $client,
        AdminUserInterface $adminUser,
        string $scopes,
        #[\SensitiveParameter]
        string $plainToken,
    ): self {
        return new self(hash('sha256', $plainToken), $client, $adminUser, $scopes);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTokenHash(): string
    {
        return $this->tokenHash;
    }

    public function getClient(): OAuthClient
    {
        return $this->client;
    }

    public function getAdminUser(): AdminUserInterface
    {
        return $this->adminUser;
    }

    public function getScopes(): string
    {
        return $this->scopes;
    }

    public function getExpiresIn(): int
    {
        return max(0, $this->expiresAt->getTimestamp() - (new \DateTimeImmutable())->getTimestamp());
    }

    public function isExpired(): bool
    {
        return new \DateTimeImmutable() > $this->expiresAt;
    }

    public function isRevoked(): bool
    {
        return $this->revokedAt !== null;
    }

    public function revoke(): void
    {
        $this->revokedAt = new \DateTimeImmutable();
    }
}
