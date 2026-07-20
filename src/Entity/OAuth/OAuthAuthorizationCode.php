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
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAuthorizationCodeRepository;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OAuthAuthorizationCodeRepository::class)]
#[ORM\Table(name: 'sylius_admin_mcp_oauth_authorization_codes')]
class OAuthAuthorizationCode
{
    private const int TTL_SECONDS = 60;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(length: 64, unique: true)]
    private string $codeHash;

    #[ORM\ManyToOne(targetEntity: OAuthClient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private OAuthClient $client;

    #[ORM\ManyToOne(targetEntity: 'Sylius\Component\Core\Model\AdminUser')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private AdminUserInterface $adminUser;

    #[ORM\Column(length: 500)]
    private string $redirectUri;

    #[ORM\Column(length: 500)]
    private string $scopes;

    #[ORM\Column(length: 128)]
    private string $codeChallenge;

    #[ORM\Column(length: 10)]
    private string $codeChallengeMethod;

    #[ORM\Column]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $usedAt = null;

    private function __construct(
        string $codeHash,
        OAuthClient $client,
        AdminUserInterface $adminUser,
        string $redirectUri,
        string $scopes,
        string $codeChallenge,
        string $codeChallengeMethod,
    ) {
        $this->id = Uuid::v7();
        $this->codeHash = $codeHash;
        $this->client = $client;
        $this->adminUser = $adminUser;
        $this->redirectUri = $redirectUri;
        $this->scopes = $scopes;
        $this->codeChallenge = $codeChallenge;
        $this->codeChallengeMethod = $codeChallengeMethod;
        $this->expiresAt = new \DateTimeImmutable('+' . self::TTL_SECONDS . ' seconds');
    }

    public static function issue(
        OAuthClient $client,
        AdminUserInterface $adminUser,
        string $redirectUri,
        string $scopes,
        string $codeChallenge,
        string $codeChallengeMethod,
        #[\SensitiveParameter]
        string $plainCode,
    ): self {
        return new self(
            hash('sha256', $plainCode),
            $client,
            $adminUser,
            $redirectUri,
            $scopes,
            $codeChallenge,
            $codeChallengeMethod,
        );
    }

    public function getCodeHash(): string
    {
        return $this->codeHash;
    }

    public function getClient(): OAuthClient
    {
        return $this->client;
    }

    public function getAdminUser(): AdminUserInterface
    {
        return $this->adminUser;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    public function getScopes(): string
    {
        return $this->scopes;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCodeChallenge(): string
    {
        return $this->codeChallenge;
    }

    public function getCodeChallengeMethod(): string
    {
        return $this->codeChallengeMethod;
    }

    public function isExpired(): bool
    {
        return new \DateTimeImmutable() > $this->expiresAt;
    }

    public function isUsed(): bool
    {
        return $this->usedAt !== null;
    }

    public function markUsed(): void
    {
        $this->usedAt = new \DateTimeImmutable();
    }
}
