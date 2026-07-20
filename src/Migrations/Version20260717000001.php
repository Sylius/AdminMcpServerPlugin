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

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260717000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create OAuth 2.0 tables for MCP authentication (clients, access tokens, authorization codes, refresh tokens)';
    }

    public function up(Schema $schema): void
    {
        $clients = $schema->createTable('sylius_admin_mcp_oauth_clients');
        $clients->addColumn('id', 'uuid');
        $clients->addColumn('clientId', 'string', ['length' => 80]);
        $clients->addColumn('clientSecretHash', 'string', ['length' => 64, 'notnull' => false]);
        $clients->addColumn('redirectUris', 'json');
        $clients->addColumn('grantTypes', 'json');
        $clients->addColumn('tokenEndpointAuthMethod', 'string', ['length' => 20]);
        $clients->addColumn('clientName', 'string', ['length' => 255]);
        $clients->addColumn('createdAt', 'datetime_immutable');
        $clients->setPrimaryKey(['id']);
        $clients->addUniqueIndex(['clientId'], 'UNIQ_MCP_CLIENT_ID');

        $accessTokens = $schema->createTable('sylius_admin_mcp_oauth_access_tokens');
        $accessTokens->addColumn('id', 'uuid');
        $accessTokens->addColumn('client_id', 'uuid');
        $accessTokens->addColumn('adminUser_id', 'integer');
        $accessTokens->addColumn('tokenHash', 'string', ['length' => 64]);
        $accessTokens->addColumn('scopes', 'string', ['length' => 500]);
        $accessTokens->addColumn('expiresAt', 'datetime_immutable');
        $accessTokens->addColumn('revokedAt', 'datetime_immutable', ['notnull' => false]);
        $accessTokens->setPrimaryKey(['id']);
        $accessTokens->addUniqueIndex(['tokenHash'], 'UNIQ_MCP_ACCESS_TOKEN_HASH');
        $accessTokens->addIndex(['client_id'], 'IDX_MCP_ACCESS_CLIENT');
        $accessTokens->addIndex(['adminUser_id'], 'IDX_MCP_ACCESS_USER');
        $accessTokens->addForeignKeyConstraint(
            'sylius_admin_mcp_oauth_clients',
            ['client_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'FK_MCP_ACCESS_CLIENT',
        );
        $accessTokens->addForeignKeyConstraint(
            'sylius_admin_user',
            ['adminUser_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'FK_MCP_ACCESS_USER',
        );

        $authCodes = $schema->createTable('sylius_admin_mcp_oauth_authorization_codes');
        $authCodes->addColumn('id', 'uuid');
        $authCodes->addColumn('client_id', 'uuid');
        $authCodes->addColumn('adminUser_id', 'integer');
        $authCodes->addColumn('codeHash', 'string', ['length' => 64]);
        $authCodes->addColumn('redirectUri', 'string', ['length' => 500]);
        $authCodes->addColumn('scopes', 'string', ['length' => 500]);
        $authCodes->addColumn('codeChallenge', 'string', ['length' => 128]);
        $authCodes->addColumn('codeChallengeMethod', 'string', ['length' => 10]);
        $authCodes->addColumn('expiresAt', 'datetime_immutable');
        $authCodes->addColumn('usedAt', 'datetime_immutable', ['notnull' => false]);
        $authCodes->setPrimaryKey(['id']);
        $authCodes->addUniqueIndex(['codeHash'], 'UNIQ_MCP_AUTH_CODE_HASH');
        $authCodes->addIndex(['client_id'], 'IDX_MCP_CODE_CLIENT');
        $authCodes->addIndex(['adminUser_id'], 'IDX_MCP_CODE_USER');
        $authCodes->addForeignKeyConstraint(
            'sylius_admin_mcp_oauth_clients',
            ['client_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'FK_MCP_CODE_CLIENT',
        );
        $authCodes->addForeignKeyConstraint(
            'sylius_admin_user',
            ['adminUser_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'FK_MCP_CODE_USER',
        );

        $refreshTokens = $schema->createTable('sylius_admin_mcp_oauth_refresh_tokens');
        $refreshTokens->addColumn('id', 'uuid');
        $refreshTokens->addColumn('accessToken_id', 'uuid');
        $refreshTokens->addColumn('tokenHash', 'string', ['length' => 64]);
        $refreshTokens->addColumn('expiresAt', 'datetime_immutable');
        $refreshTokens->addColumn('revokedAt', 'datetime_immutable', ['notnull' => false]);
        $refreshTokens->setPrimaryKey(['id']);
        $refreshTokens->addUniqueIndex(['tokenHash'], 'UNIQ_MCP_REFRESH_TOKEN_HASH');
        $refreshTokens->addIndex(['accessToken_id'], 'IDX_MCP_REFRESH_ACCESS');
        $refreshTokens->addForeignKeyConstraint(
            'sylius_admin_mcp_oauth_access_tokens',
            ['accessToken_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'FK_MCP_REFRESH_ACCESS',
        );
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('sylius_admin_mcp_oauth_refresh_tokens');
        $schema->dropTable('sylius_admin_mcp_oauth_authorization_codes');
        $schema->dropTable('sylius_admin_mcp_oauth_access_tokens');
        $schema->dropTable('sylius_admin_mcp_oauth_clients');
    }
}
