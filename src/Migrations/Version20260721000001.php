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
        return 'Migrate OAuth tokens/codes to league/oauth2-server thin entities (identifier+expiry+revoked)';
    }

    public function up(Schema $schema): void
    {
        // Old tokens are incompatible with the new league-based format — truncate before restructure
        $this->addSql('TRUNCATE TABLE sylius_admin_mcp_oauth_refresh_tokens');
        $this->addSql('TRUNCATE TABLE sylius_admin_mcp_oauth_access_tokens');
        $this->addSql('TRUNCATE TABLE sylius_admin_mcp_oauth_authorization_codes');

        // access_tokens has no FKs — just restructure columns
        $this->addSql('DROP INDEX `primary` ON sylius_admin_mcp_oauth_access_tokens');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_access_tokens ADD identifier VARCHAR(255) NOT NULL, ADD revoked TINYINT(1) DEFAULT 0 NOT NULL, DROP id, DROP client_id, DROP adminUser_id, DROP tokenHash, DROP scopes, DROP revokedAt, CHANGE expiresAt expiry DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_access_tokens ADD PRIMARY KEY (identifier)');

        // refresh_tokens has no FKs — just restructure columns
        $this->addSql('DROP INDEX `primary` ON sylius_admin_mcp_oauth_refresh_tokens');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_refresh_tokens ADD identifier VARCHAR(255) NOT NULL, ADD revoked TINYINT(1) DEFAULT 0 NOT NULL, DROP id, DROP accessToken_id, DROP tokenHash, DROP revokedAt, CHANGE expiresAt expiry DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_refresh_tokens ADD PRIMARY KEY (identifier)');

        // authorization_codes has FK + unique constraints
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_authorization_codes DROP FOREIGN KEY FK_MCP_CODE_CLIENT');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_authorization_codes DROP FOREIGN KEY FK_MCP_CODE_USER');
        $this->addSql('DROP INDEX IDX_MCP_CODE_CLIENT ON sylius_admin_mcp_oauth_authorization_codes');
        $this->addSql('DROP INDEX IDX_MCP_CODE_USER ON sylius_admin_mcp_oauth_authorization_codes');
        $this->addSql('DROP INDEX UNIQ_MCP_AUTH_CODE_HASH ON sylius_admin_mcp_oauth_authorization_codes');
        $this->addSql('DROP INDEX `primary` ON sylius_admin_mcp_oauth_authorization_codes');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_authorization_codes ADD identifier VARCHAR(255) NOT NULL, ADD revoked TINYINT(1) DEFAULT 0 NOT NULL, DROP id, DROP client_id, DROP adminUser_id, DROP codeHash, DROP redirectUri, DROP scopes, DROP codeChallenge, DROP codeChallengeMethod, DROP usedAt, CHANGE expiresAt expiry DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_authorization_codes ADD PRIMARY KEY (identifier)');

        // clients: rename unique index to Doctrine-generated name
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_clients RENAME INDEX uniq_mcp_client_id TO UNIQ_2D062973EA1CE9BE');
    }

    public function down(Schema $schema): void
    {
        // access_tokens: restore UUID schema
        $this->addSql('DROP INDEX `PRIMARY` ON sylius_admin_mcp_oauth_access_tokens');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_access_tokens ADD id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD client_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD adminUser_id INT NOT NULL, ADD tokenHash VARCHAR(64) NOT NULL, ADD scopes VARCHAR(500) NOT NULL, ADD revokedAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP identifier, DROP revoked, CHANGE expiry expiresAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_access_tokens ADD PRIMARY KEY (id)');

        // refresh_tokens: restore UUID schema
        $this->addSql('DROP INDEX `PRIMARY` ON sylius_admin_mcp_oauth_refresh_tokens');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_refresh_tokens ADD id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD accessToken_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD tokenHash VARCHAR(64) NOT NULL, ADD revokedAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP identifier, DROP revoked, CHANGE expiry expiresAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_refresh_tokens ADD PRIMARY KEY (id)');

        // authorization_codes: restore UUID/FK schema
        $this->addSql('DROP INDEX `PRIMARY` ON sylius_admin_mcp_oauth_authorization_codes');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_authorization_codes ADD id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD client_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD adminUser_id INT NOT NULL, ADD codeHash VARCHAR(64) NOT NULL, ADD redirectUri VARCHAR(500) NOT NULL, ADD scopes VARCHAR(500) NOT NULL, ADD codeChallenge VARCHAR(128) NOT NULL, ADD codeChallengeMethod VARCHAR(10) NOT NULL, ADD usedAt DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP identifier, DROP revoked, CHANGE expiry expiresAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_authorization_codes ADD CONSTRAINT FK_MCP_CODE_CLIENT FOREIGN KEY (client_id) REFERENCES sylius_admin_mcp_oauth_clients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_authorization_codes ADD CONSTRAINT FK_MCP_CODE_USER FOREIGN KEY (adminUser_id) REFERENCES sylius_admin_user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_MCP_CODE_CLIENT ON sylius_admin_mcp_oauth_authorization_codes (client_id)');
        $this->addSql('CREATE INDEX IDX_MCP_CODE_USER ON sylius_admin_mcp_oauth_authorization_codes (adminUser_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_MCP_AUTH_CODE_HASH ON sylius_admin_mcp_oauth_authorization_codes (codeHash)');
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_authorization_codes ADD PRIMARY KEY (id)');

        // clients: restore index name
        $this->addSql('ALTER TABLE sylius_admin_mcp_oauth_clients RENAME INDEX uniq_2d062973ea1ce9be TO UNIQ_MCP_CLIENT_ID');
    }
}
