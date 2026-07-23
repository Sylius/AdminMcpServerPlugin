<p align="center">
    <a href="https://sylius.com" target="_blank">
        <picture>
          <source media="(prefers-color-scheme: dark)" srcset="https://media.sylius.com/sylius-logo-800-dark.png">
          <source media="(prefers-color-scheme: light)" srcset="https://media.sylius.com/sylius-logo-800.png">
          <img alt="Sylius Logo." src="https://media.sylius.com/sylius-logo-800.png">
        </picture>
    </a>
</p>

<h1 align="center">Sylius Admin MCP Server Plugin</h1>

<p align="center">Exposes Sylius Admin API as a Model Context Protocol (MCP) server with OAuth 2.0 PKCE authentication.</p>

---

This plugin turns your Sylius store into an MCP server, allowing AI assistants (Claude, Cursor, etc.) to manage your store through natural language. It provides 171 tools covering all major Sylius resources — products, orders, customers, promotions, shipping, and more — secured by a full OAuth 2.0 Authorization Code + PKCE flow.

## Requirements

- PHP 8.2+
- Sylius 2.1 or 2.2
- Symfony 7.4
- MySQL or PostgreSQL
- Node.js 20+ and Yarn (for frontend assets)

## Installation

### Step 1 — Add the plugin via Composer

```bash
composer require sylius/admin-mcp-server-plugin
```

> **Note on `symfony/type-info` patch** (Sylius 2.2.x): A bug in `symfony/type-info v7.4.x` causes a cache warmup error: `Cannot create union with both "object" and class type`. Apply the patch that ships with the plugin:
>
> ```bash
> patch vendor/symfony/type-info/TypeContext/TypeContextFactory.php \
>   vendor/sylius/admin-mcp-server-plugin/patches/symfony-type-info-union-fix.patch
> ```
>
> To re-apply this patch automatically after every `composer update`, you can use [cweagans/composer-patches](https://github.com/cweagans/composer-patches):
>
> 1. `composer require --dev cweagans/composer-patches`
> 2. Copy the patch to your project: `mkdir -p patches && cp vendor/sylius/admin-mcp-server-plugin/patches/symfony-type-info-union-fix.patch patches/`
> 3. Add to your `composer.json`:
>    ```json
>    "config": { "allow-plugins": { "cweagans/composer-patches": true } },
>    "extra": {
>        "patches": {
>            "symfony/type-info": {
>                "Fix: Cannot create union with both object and class type": "patches/symfony-type-info-union-fix.patch"
>            }
>        }
>    }
>    ```
> 4. Run `composer install`

### Step 2 — Register bundles

Add to your `config/bundles.php`:

```php
return [
    // ... existing bundles ...
    League\Bundle\OAuth2ServerBundle\LeagueOAuth2ServerBundle::class => ['all' => true],
    Symfony\AI\McpBundle\McpBundle::class => ['all' => true],
    Sylius\AdminMcpServerPlugin\SyliusAdminMcpServerPlugin::class => ['all' => true],
];
```

> **Note**: The Symfony Flex recipe that runs automatically with `composer require` registers these bundles automatically. Verify your `config/bundles.php` contains all three. However, the Flex recipe does **not** create the plugin config file (Step 3), the corrected OAuth config (Step 4), or the routes file (Step 5) — those must be created manually.

### Step 3 — Import plugin configuration

Create `config/packages/sylius_admin_mcp_server.yaml`:

```yaml
imports:
    - { resource: "@SyliusAdminMcpServerPlugin/config/config.yaml" }
```

### Step 4 — Configure OAuth2 server

The Flex recipe creates `config/packages/league_oauth2_server.yaml` with generic settings. **Replace its contents** with the following to use your existing JWT keys:

```yaml
# config/packages/league_oauth2_server.yaml
league_oauth2_server:
    authorization_server:
        private_key: '%env(resolve:JWT_SECRET_KEY)%'
        private_key_passphrase: '%env(JWT_PASSPHRASE)%'
        encryption_key: '%env(SYLIUS_ADMIN_MCP_SERVER_OAUTH_ENCRYPTION_KEY)%'
        encryption_key_type: 'plain'
        enable_client_credentials_grant: false
        enable_password_grant: false
        enable_implicit_grant: false
        persist_access_token: false

    resource_server:
        public_key: '%env(resolve:JWT_PUBLIC_KEY)%'

    scopes:
        available:
            - admin_api
        default:
            - admin_api

    persistence:
        in_memory: ~
```

### Step 5 — Import routes

Create `config/routes/sylius_admin_mcp_server.yaml`:

```yaml
sylius_admin_mcp_server:
    resource: "@SyliusAdminMcpServerPlugin/config/routes.yaml"
    type: yaml
```

### Step 6 — Configure environment variables

Add to your `.env` (or `.env.local`):

```dotenv
###> sylius/admin-mcp-server-plugin ###
# URL of this application's Admin API (used by MCP tools to call the API)
SYLIUS_ADMIN_MCP_SERVER_API_URL=https://your-domain.com/api/v2/admin/
# Admin API user credentials — only required if you use the credentials-based token provider.
# By default the plugin reuses the OAuth Bearer token of the logged-in admin user.
# To switch to credential-based login, alias the service in your config/services.php:
#   ->alias('sylius_admin_mcp_server.provider.token', 'sylius_admin_mcp_server.provider.token.credentials');
SYLIUS_ADMIN_MCP_SERVER_API_EMAIL=api@example.com
SYLIUS_ADMIN_MCP_SERVER_API_PASSWORD=your-api-password
# Set to false to disable SSL verification (useful for local HTTPS)
SYLIUS_ADMIN_MCP_SERVER_VERIFY_SSL=true
# Random hex string for OAuth token encryption — generate with: openssl rand -hex 32
SYLIUS_ADMIN_MCP_SERVER_OAUTH_ENCRYPTION_KEY=your-32-byte-hex-key-here
###< sylius/admin-mcp-server-plugin ###
```

Generate the encryption key:

```bash
openssl rand -hex 32
```

### Step 7 — Generate JWT keypair (if not already done)

Sylius ships with `lexik/jwt-authentication-bundle`. If your JWT keys don't exist yet:

```bash
php bin/console lexik:jwt:generate-keypair
```

### Step 8 — Run database migrations

```bash
php bin/console doctrine:migrations:migrate -n
```

This creates three OAuth tables: `sylius_admin_mcp_oauth_clients`, `sylius_admin_mcp_oauth_authorization_codes`, `sylius_admin_mcp_oauth_refresh_tokens`.

### Step 9 — Clear cache

```bash
php bin/console cache:clear
php bin/console cache:warmup
```

### Step 10 — Build frontend assets

The admin authorization page requires Sylius admin panel assets. If you haven't already:

```bash
yarn install
yarn build
php bin/console assets:install
```

### Step 11 — Grant API access to an admin user

Only admin users with `ROLE_API_ACCESS` can authorize via the OAuth consent page. Grant it to an existing user via SQL:

```sql
UPDATE sylius_admin_user
SET roles = JSON_ARRAY_APPEND(roles, '$', 'ROLE_API_ACCESS')
WHERE email = 'your@admin.com';
```

Or create a dedicated API user in your fixtures.

---

## Verification

After installation, verify everything is working:

```bash
# 1. Check services registered
php bin/console debug:container --tag=mcp.tool | grep "Sylius"

# 2. Check routes registered
php bin/console debug:router | grep mcp

# 3. Check OAuth discovery endpoint
curl -sk https://your-domain.com/.well-known/oauth-authorization-server | python3 -m json.tool
```

Expected routes: `/.well-known/oauth-authorization-server`, `/_mcp/oauth/register`, `/admin/mcp/oauth/authorize`, `/_mcp/oauth/token`, `/_mcp`.

---

## Security

- All `/_mcp` requests require a valid OAuth 2.0 Bearer token (returns `401` otherwise)
- Authorization is limited to admin users with `ROLE_API_ACCESS`
- Tokens expire after 1 hour; refresh tokens are valid for 30 days
- PKCE (Proof Key for Code Exchange) prevents authorization code interception attacks

---

## Documentation

- [Authentication Flow (OAuth 2.0 PKCE)](docs/authentication.md)
- [Available MCP Tools](docs/tools.md) — 171 tools across 34 resource groups
- [Configuration Reference](docs/configuration.md) — selectively disabling tool groups
- [Troubleshooting](docs/troubleshooting.md)

---

## License

This plugin is released under the [MIT License](LICENSE).
