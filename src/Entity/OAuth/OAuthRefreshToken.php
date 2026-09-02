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

#[ORM\Entity]
#[ORM\Table(name: 'sylius_admin_mcp_oauth_refresh_tokens')]
class OAuthRefreshToken
{
    #[ORM\Column(options: ['default' => false])]
    private bool $revoked = false;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column]
        private readonly string $identifier,
        #[ORM\Column]
        private readonly \DateTimeImmutable $expiry,
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getExpiry(): \DateTimeImmutable
    {
        return $this->expiry;
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function revoke(): void
    {
        $this->revoked = true;
    }
}
