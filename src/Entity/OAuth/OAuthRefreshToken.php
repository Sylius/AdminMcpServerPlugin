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
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthRefreshTokenRepository;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OAuthRefreshTokenRepository::class)]
#[ORM\Table(name: 'sylius_admin_mcp_oauth_refresh_tokens')]
class OAuthRefreshToken
{
    private const int TTL_SECONDS = 2_592_000; // 30 days

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(length: 64, unique: true)]
    private string $tokenHash;

    #[ORM\ManyToOne(targetEntity: OAuthAccessToken::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private OAuthAccessToken $accessToken;

    #[ORM\Column]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $revokedAt = null;

    private function __construct(string $tokenHash, OAuthAccessToken $accessToken)
    {
        $this->id = Uuid::v7();
        $this->tokenHash = $tokenHash;
        $this->accessToken = $accessToken;
        $this->expiresAt = new \DateTimeImmutable('+' . self::TTL_SECONDS . ' seconds');
    }

    public static function issue(
        OAuthAccessToken $accessToken,
        #[\SensitiveParameter]
        string $plainToken,
    ): self {
        return new self(hash('sha256', $plainToken), $accessToken);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTokenHash(): string
    {
        return $this->tokenHash;
    }

    public function getAccessToken(): OAuthAccessToken
    {
        return $this->accessToken;
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
