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

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthScope;

final class OAuthScopeRepository implements ScopeRepositoryInterface
{
    private const array AVAILABLE_SCOPES = ['admin_api'];

    public function getScopeEntityByIdentifier(string $identifier): ?ScopeEntityInterface
    {
        if (!\in_array($identifier, self::AVAILABLE_SCOPES, true)) {
            return null;
        }

        return new OAuthScope($identifier);
    }

    /**
     * @param ScopeEntityInterface[] $scopes
     *
     * @return list<ScopeEntityInterface>
     */
    public function finalizeScopes(
        array $scopes,
        string $grantType,
        ClientEntityInterface $clientEntity,
        ?string $userIdentifier = null,
        ?string $authCodeId = null,
    ): array {
        return array_values(array_filter(
            $scopes,
            static fn (ScopeEntityInterface $s): bool => \in_array($s->getIdentifier(), self::AVAILABLE_SCOPES, true),
        ));
    }
}
