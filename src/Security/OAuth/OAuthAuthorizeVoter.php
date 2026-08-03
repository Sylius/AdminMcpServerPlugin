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

namespace Sylius\AdminMcpServerPlugin\Security\OAuth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Grants ROLE_ADMINISTRATION_ACCESS on the OAuth consent screen for users who have
 * ROLE_API_ACCESS. This allows users without full admin panel access to still
 * authorize MCP clients.
 *
 * @extends Voter<string, Request>
 */
final class OAuthAuthorizeVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === 'ROLE_ADMINISTRATION_ACCESS' &&
            $subject instanceof Request &&
            $subject->attributes->get('_route') === 'sylius_admin_mcp_server_oauth_authorize';
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, mixed $vote = null): bool
    {
        return in_array('ROLE_API_ACCESS', $token->getRoleNames(), true);
    }
}
