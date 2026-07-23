# Troubleshooting

## "Invalid key supplied" on OAuth endpoints

The JWT private/public keys are missing. Run:

```bash
php bin/console lexik:jwt:generate-keypair
```

## "Could not find the entrypoints file from Webpack"

Frontend assets are not built. Run:

```bash
yarn install
yarn build
php bin/console assets:install
```

Requires Node.js 20+. If using nvm: `nvm use 20`.

## "Cannot create union with both object and class type" during cache warmup

The `symfony/type-info` patch is not applied. See [Installation Step 1](../README.md#step-1--add-the-plugin-via-composer) for patch instructions.

## Plugin migration not running / not visible

The plugin uses the `Sylius\AdminMcpServerPlugin\Migrations` namespace. If it does not appear in `doctrine:migrations:list`, check that your `doctrine_migrations.yaml` includes the plugin's path:

```bash
php bin/console debug:config doctrine_migrations | grep -A5 "migrations_paths"
php bin/console doctrine:migrations:list | grep AdminMcpServer
```

## "Authorization Failed" — "User does not have API access"

The logged-in admin user lacks `ROLE_API_ACCESS`. Grant it:

```sql
UPDATE sylius_admin_user
SET roles = JSON_ARRAY_APPEND(roles, '$', 'ROLE_API_ACCESS')
WHERE email = 'api@example.com';
```

## MCP endpoint returns 401

All requests to `/_mcp` require a valid Bearer token. See [Authentication Flow](authentication.md).

## Flex recipe creates wrong `league_oauth2_server.yaml`

The `league/oauth2-server-bundle` Flex recipe generates a config with `OAUTH_PRIVATE_KEY` / `OAUTH_PASSPHRASE` / `OAUTH_ENCRYPTION_KEY`. This plugin uses `JWT_SECRET_KEY` / `JWT_PASSPHRASE` / `SYLIUS_ADMIN_MCP_SERVER_OAUTH_ENCRYPTION_KEY` (reusing the existing Sylius JWT keys). Always replace the recipe-generated file with the content shown in [Installation Step 4](../README.md#step-4--configure-oauth2-server).
