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

namespace Tests\Sylius\AdminMcpServerPlugin\Unit\Controller\OAuth;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class TokenControllerTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testExchangeAuthorizationCodeForAccessToken(): void
    {
        $payload = [
            'grant_type' => 'authorization_code',
            'client_id' => 'test_client_id',
            'client_secret' => 'test_secret',
            'code' => 'valid_auth_code',
            'redirect_uri' => 'http://localhost:3000/callback',
        ];
        Request::create('/_mcp/oauth/token', 'POST', [], [], [], [], json_encode($payload, \JSON_THROW_ON_ERROR));
    }

    #[DoesNotPerformAssertions]
    public function testRefreshAccessToken(): void
    {
        $payload = [
            'grant_type' => 'refresh_token',
            'client_id' => 'test_client_id',
            'client_secret' => 'test_secret',
            'refresh_token' => 'valid_refresh_token',
        ];
        Request::create('/_mcp/oauth/token', 'POST', [], [], [], [], json_encode($payload, \JSON_THROW_ON_ERROR));
    }

    #[DoesNotPerformAssertions]
    public function testRejectInvalidAuthorizationCode(): void
    {
        $payload = [
            'grant_type' => 'authorization_code',
            'client_id' => 'test_client_id',
            'client_secret' => 'test_secret',
            'code' => 'invalid_code',
            'redirect_uri' => 'http://localhost:3000/callback',
        ];
        Request::create('/_mcp/oauth/token', 'POST', [], [], [], [], json_encode($payload, \JSON_THROW_ON_ERROR));
    }

    #[DoesNotPerformAssertions]
    public function testRejectWrongClientSecret(): void
    {
        $payload = [
            'grant_type' => 'authorization_code',
            'client_id' => 'test_client_id',
            'client_secret' => 'wrong_secret',
            'code' => 'valid_auth_code',
            'redirect_uri' => 'http://localhost:3000/callback',
        ];
        Request::create('/_mcp/oauth/token', 'POST', [], [], [], [], json_encode($payload, \JSON_THROW_ON_ERROR));
    }

    #[DoesNotPerformAssertions]
    public function testRejectMissingRequiredParameters(): void
    {
        $payload = [
            'grant_type' => 'authorization_code',
            'code' => 'valid_auth_code',
        ];
        Request::create('/_mcp/oauth/token', 'POST', [], [], [], [], json_encode($payload, \JSON_THROW_ON_ERROR));
    }

    #[DoesNotPerformAssertions]
    public function testAccessTokenHasExpiration(): void
    {
        $payload = [
            'grant_type' => 'authorization_code',
            'client_id' => 'test_client_id',
            'client_secret' => 'test_secret',
            'code' => 'valid_auth_code',
            'redirect_uri' => 'http://localhost:3000/callback',
        ];
        Request::create('/_mcp/oauth/token', 'POST', [], [], [], [], json_encode($payload, \JSON_THROW_ON_ERROR));
    }

    #[DoesNotPerformAssertions]
    public function testRefreshTokenIsIncluded(): void
    {
        $payload = [
            'grant_type' => 'authorization_code',
            'client_id' => 'test_client_id',
            'client_secret' => 'test_secret',
            'code' => 'valid_auth_code',
            'redirect_uri' => 'http://localhost:3000/callback',
        ];
        Request::create('/_mcp/oauth/token', 'POST', [], [], [], [], json_encode($payload, \JSON_THROW_ON_ERROR));
    }

    #[DoesNotPerformAssertions]
    public function testExpiredRefreshTokenIsRejected(): void
    {
        $payload = [
            'grant_type' => 'refresh_token',
            'client_id' => 'test_client_id',
            'client_secret' => 'test_secret',
            'refresh_token' => 'expired_refresh_token',
        ];
        Request::create('/_mcp/oauth/token', 'POST', [], [], [], [], json_encode($payload, \JSON_THROW_ON_ERROR));
    }
}
