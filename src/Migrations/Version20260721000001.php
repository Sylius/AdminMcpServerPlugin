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

namespace Sylius\AdminMcpServerPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260721000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create MCP OAuth tables (clients, authorization_codes, refresh_tokens)';
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
        $clients->addUniqueIndex(['clientId'], 'UNIQ_2D062973EA1CE9BE');

        $authCodes = $schema->createTable('sylius_admin_mcp_oauth_authorization_codes');
        $authCodes->addColumn('identifier', 'string', ['length' => 255]);
        $authCodes->addColumn('expiry', 'datetime_immutable');
        $authCodes->addColumn('revoked', 'boolean', ['default' => false]);
        $authCodes->setPrimaryKey(['identifier']);

        $refreshTokens = $schema->createTable('sylius_admin_mcp_oauth_refresh_tokens');
        $refreshTokens->addColumn('identifier', 'string', ['length' => 255]);
        $refreshTokens->addColumn('expiry', 'datetime_immutable');
        $refreshTokens->addColumn('revoked', 'boolean', ['default' => false]);
        $refreshTokens->setPrimaryKey(['identifier']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('sylius_admin_mcp_oauth_refresh_tokens');
        $schema->dropTable('sylius_admin_mcp_oauth_authorization_codes');
        $schema->dropTable('sylius_admin_mcp_oauth_clients');
    }
}
