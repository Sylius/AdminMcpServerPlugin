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
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthClientRepository;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OAuthClientRepository::class)]
#[ORM\Table(name: 'sylius_admin_mcp_oauth_clients')]
class OAuthClient implements ClientEntityInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(length: 80, unique: true)]
    private string $clientId;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $clientSecretHash;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $redirectUris;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $grantTypes;

    #[ORM\Column(length: 20)]
    private string $tokenEndpointAuthMethod;

    #[ORM\Column(length: 255)]
    private string $clientName;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    private function __construct(
        string $clientId,
        ?string $clientSecretHash,
        array $redirectUris,
        string $clientName,
        string $tokenEndpointAuthMethod,
        array $grantTypes,
    ) {
        $this->id = Uuid::v7();
        $this->clientId = $clientId;
        $this->clientSecretHash = $clientSecretHash;
        $this->redirectUris = $redirectUris;
        $this->clientName = $clientName;
        $this->tokenEndpointAuthMethod = $tokenEndpointAuthMethod;
        $this->grantTypes = $grantTypes;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function register(
        array $redirectUris,
        string $clientName,
        string $tokenEndpointAuthMethod = 'client_secret_post',
        array $grantTypes = ['authorization_code', 'refresh_token'],
        ?string $plainSecret = null,
    ): self {
        return new self(
            clientId: bin2hex(random_bytes(20)),
            clientSecretHash: $plainSecret !== null ? hash('sha256', $plainSecret) : null,
            redirectUris: $redirectUris,
            clientName: $clientName,
            tokenEndpointAuthMethod: $tokenEndpointAuthMethod,
            grantTypes: $grantTypes,
        );
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    /** ClientEntityInterface */
    public function getIdentifier(): string
    {
        return $this->clientId;
    }

    public function getName(): string
    {
        return $this->clientName;
    }

    /** @return list<string> */
    public function getRedirectUri(): array
    {
        return $this->redirectUris;
    }

    public function isConfidential(): bool
    {
        return $this->tokenEndpointAuthMethod !== 'none';
    }

    /** @return list<string> */
    public function getRedirectUris(): array
    {
        return $this->redirectUris;
    }

    /** @return list<string> */
    public function getGrantTypes(): array
    {
        return $this->grantTypes;
    }

    public function getTokenEndpointAuthMethod(): string
    {
        return $this->tokenEndpointAuthMethod;
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function verifySecret(#[\SensitiveParameter] string $plainSecret): bool
    {
        if ($this->clientSecretHash === null) {
            return false;
        }

        return hash_equals($this->clientSecretHash, hash('sha256', $plainSecret));
    }

    public function matchesRedirectUri(string $uri): bool
    {
        return \in_array($uri, $this->redirectUris, true);
    }
}
