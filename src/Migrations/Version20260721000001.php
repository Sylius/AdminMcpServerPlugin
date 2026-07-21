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

final class Version20260721000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create MCP OAuth tables (clients, authorization_codes, refresh_tokens)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE sylius_admin_mcp_oauth_clients (
                id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)',
                clientId VARCHAR(80) NOT NULL,
                clientSecretHash VARCHAR(64) DEFAULT NULL,
                redirectUris JSON NOT NULL,
                grantTypes JSON NOT NULL,
                tokenEndpointAuthMethod VARCHAR(20) NOT NULL,
                clientName VARCHAR(255) NOT NULL,
                createdAt DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id),
                UNIQUE INDEX UNIQ_2D062973EA1CE9BE (clientId)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE sylius_admin_mcp_oauth_authorization_codes (
                identifier VARCHAR(255) NOT NULL,
                expiry DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                revoked TINYINT(1) DEFAULT 0 NOT NULL,
                PRIMARY KEY (identifier)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE sylius_admin_mcp_oauth_refresh_tokens (
                identifier VARCHAR(255) NOT NULL,
                expiry DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                revoked TINYINT(1) DEFAULT 0 NOT NULL,
                PRIMARY KEY (identifier)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS sylius_admin_mcp_oauth_refresh_tokens');
        $this->addSql('DROP TABLE IF EXISTS sylius_admin_mcp_oauth_authorization_codes');
        $this->addSql('DROP TABLE IF EXISTS sylius_admin_mcp_oauth_clients');
    }
}
