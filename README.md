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

This plugin turns your Sylius store into an MCP server, allowing AI assistants (Claude, Cursor, etc.) to manage your store through natural language. It provides 171 tools covering all major Sylius resources - products, orders, customers, promotions, shipping, and more - secured by a full OAuth 2.0 Authorization Code + PKCE flow.

## Requirements

- PHP 8.2+
- Sylius 2.1 or 2.2
- Symfony 7.4
- MySQL or PostgreSQL
- Node.js 20+ and Yarn (for frontend assets)

## Installation

### Installation with Symfony Flex recipe

When Symfony Flex is active, `composer require sylius/admin-mcp-server-plugin` automatically registers the bundles, imports the plugin configuration, and imports the routes. Continue from Step 2 below.

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

#### Step 1 - Add the plugin via Composer

```bash
composer require sylius/admin-mcp-server-plugin
```

#### Step 2 - Generate OAuth keypair

The plugin uses a dedicated RSA keypair (separate from Lexik JWT) for signing OAuth tokens:

```bash
mkdir -p config/jwt
openssl genrsa -out config/jwt/mcp_private.pem 4096
openssl rsa -in config/jwt/mcp_private.pem -pubout -out config/jwt/mcp_public.pem
```

The keys default to `config/jwt/mcp_private.pem` and `config/jwt/mcp_public.pem`. Override via env vars if you want a different location.

#### Step 3 - Configure environment variables

Add to your `.env` (or `.env.local`):

```dotenv
###> sylius/admin-mcp-server-plugin ###
# URL of this application's Admin API (used by MCP tools to call the API)
SYLIUS_ADMIN_MCP_SERVER_API_URL=https://your-domain.com/api/v2/admin/
# Admin API user credentials - only required if you use the credentials-based token provider.
# By default the plugin reuses the OAuth Bearer token of the logged-in admin user.
# To switch to credential-based login, alias the service in your config/services.php:
#   ->alias('sylius_admin_mcp_server.provider.token', 'sylius_admin_mcp_server.provider.token.credentials');
SYLIUS_ADMIN_MCP_SERVER_API_EMAIL=api@example.com
SYLIUS_ADMIN_MCP_SERVER_API_PASSWORD=your-api-password
# Set to false to disable SSL verification (useful for local HTTPS)
SYLIUS_ADMIN_MCP_SERVER_VERIFY_SSL=true
# Random hex string for OAuth token encryption - generate with: openssl rand -hex 32
SYLIUS_ADMIN_MCP_SERVER_OAUTH_ENCRYPTION_KEY=your-32-byte-hex-key-here
###< sylius/admin-mcp-server-plugin ###
```

Generate the encryption key:

```bash
openssl rand -hex 32
```

#### Step 4 - Run database migrations

```bash
php bin/console doctrine:migrations:migrate -n
```

This creates three OAuth tables: `sylius_admin_mcp_oauth_clients`, `sylius_admin_mcp_oauth_authorization_codes`, `sylius_admin_mcp_oauth_refresh_tokens`.

#### Step 5 - Clear cache

```bash
php bin/console cache:clear
php bin/console cache:warmup
```

#### Step 6 - Build frontend assets

The admin authorization page requires Sylius admin panel assets. If you haven't already:

```bash
yarn install
yarn build
php bin/console assets:install
```

#### Step 7 - Grant API access to an admin user

Only admin users with `ROLE_API_ACCESS` can authorize via the OAuth consent page. Grant it to an existing user via SQL:

```sql
UPDATE sylius_admin_user
SET roles = JSON_ARRAY_APPEND(roles, '$', 'ROLE_API_ACCESS')
WHERE email = 'your@admin.com';
```

Or create a dedicated API user in your fixtures.

---

### Manual Installation (without Symfony Flex)

> **Note on `symfony/type-info` patch** (Sylius 2.2.x): see the note above in the Flex section.

#### Step 1 - Add the plugin via Composer

```bash
composer require sylius/admin-mcp-server-plugin
```

#### Step 2 - Register bundles

Add to your `config/bundles.php`:

```php
return [
    // ... existing bundles ...
    League\Bundle\OAuth2ServerBundle\LeagueOAuth2ServerBundle::class => ['all' => true],
    Symfony\AI\McpBundle\McpBundle::class => ['all' => true],
    Sylius\AdminMcpServerPlugin\SyliusAdminMcpServerPlugin::class => ['all' => true],
];
```

#### Step 3 - Import plugin configuration

Create `config/packages/sylius_admin_mcp_server.yaml`:

```yaml
imports:
    - { resource: "@SyliusAdminMcpServerPlugin/config/config.yaml" }
```

#### Step 4 - Import routes

Create `config/routes/sylius_admin_mcp_server.yaml`:

```yaml
sylius_admin_mcp_server:
    resource: "@SyliusAdminMcpServerPlugin/config/routes.yaml"
    type: yaml
```

#### Step 5 - Generate OAuth keypair

The plugin uses a dedicated RSA keypair (separate from Lexik JWT) for signing OAuth tokens:

```bash
mkdir -p config/jwt
openssl genrsa -out config/jwt/mcp_private.pem 4096
openssl rsa -in config/jwt/mcp_private.pem -pubout -out config/jwt/mcp_public.pem
```

#### Step 6 - Configure environment variables

Add to your `.env` (or `.env.local`):

```dotenv
###> sylius/admin-mcp-server-plugin ###
# URL of this application's Admin API (used by MCP tools to call the API)
SYLIUS_ADMIN_MCP_SERVER_API_URL=https://your-domain.com/api/v2/admin/
# Admin API user credentials - only required if you use the credentials-based token provider.
# By default the plugin reuses the OAuth Bearer token of the logged-in admin user.
# To switch to credential-based login, alias the service in your config/services.php:
#   ->alias('sylius_admin_mcp_server.provider.token', 'sylius_admin_mcp_server.provider.token.credentials');
SYLIUS_ADMIN_MCP_SERVER_API_EMAIL=api@example.com
SYLIUS_ADMIN_MCP_SERVER_API_PASSWORD=your-api-password
# Set to false to disable SSL verification (useful for local HTTPS)
SYLIUS_ADMIN_MCP_SERVER_VERIFY_SSL=true
# Random hex string for OAuth token encryption - generate with: openssl rand -hex 32
SYLIUS_ADMIN_MCP_SERVER_OAUTH_ENCRYPTION_KEY=your-32-byte-hex-key-here
###< sylius/admin-mcp-server-plugin ###
```

Generate the encryption key:

```bash
openssl rand -hex 32
```

#### Step 7 - Run database migrations

```bash
php bin/console doctrine:migrations:migrate -n
```

This creates three OAuth tables: `sylius_admin_mcp_oauth_clients`, `sylius_admin_mcp_oauth_authorization_codes`, `sylius_admin_mcp_oauth_refresh_tokens`.

#### Step 8 - Clear cache

```bash
php bin/console cache:clear
php bin/console cache:warmup
```

#### Step 9 - Build frontend assets

The admin authorization page requires Sylius admin panel assets. If you haven't already:

```bash
yarn install
yarn build
php bin/console assets:install
```

#### Step 10 - Grant API access to an admin user

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

## Connecting MCP Clients

Once the plugin is installed and running, connect an AI assistant using one of the options below.

### Option A - Claude Desktop (local or remote)

Claude Desktop supports both local and remote MCP servers and works out of the box with any HTTPS URL - including `localhost` with a self-signed certificate.

Edit your Claude Desktop configuration file:

- **macOS**: `~/Library/Application Support/Claude/claude_desktop_config.json`
- **Windows**: `%APPDATA%\Claude\claude_desktop_config.json`

```json
{
  "mcpServers": {
    "sylius-admin": {
      "type": "http",
      "url": "https://your-domain.com/_mcp"
    }
  }
}
```

Claude Desktop reads the OAuth discovery endpoint (`/.well-known/oauth-authorization-server`) automatically and opens the consent page in your default browser when you first use the server. No manual token management is required.

> **Local development**: Use `"url": "https://127.0.0.1:8003/_mcp"`. Claude Desktop accepts self-signed certificates, so no tunnelling is needed.

### Option B - Claude.ai in the browser (Custom Connectors)

> **Requirements**: Claude.ai **Pro or Max** plan. The server must be publicly accessible over HTTPS with a **valid (non-self-signed) SSL certificate**. `localhost` does not work - Anthropic's servers call your endpoint from their own IP ranges.

1. Open [claude.ai](https://claude.ai) → click your avatar → **Customize Claude** → **Connectors** → **+** → **Add custom connector**
2. Paste your server URL: `https://your-domain.com/_mcp`
3. Click **Connect**. Claude.ai reads the `/.well-known/oauth-authorization-server` discovery document automatically and redirects you to your Sylius admin consent page.
4. Log in as an admin user with `ROLE_API_ACCESS` and click **Allow**.

Claude.ai uses `https://claude.ai/api/mcp/auth_callback` as its redirect URI, which the plugin's dynamic client registration endpoint accepts automatically.

> **Local development without a public server**: Tunnel your local server through a public HTTPS URL using [ngrok](https://ngrok.com), [Cloudflare Tunnel](https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/), or [LocalCan](https://www.localcan.com/):
> ```bash
> # Example with ngrok:
> ngrok http https://127.0.0.1:8003
> # Then use the generated https://xxxx.ngrok-free.app/_mcp URL in claude.ai
> ```

### Option C - Claude Code (CLI)

```bash
claude mcp add --transport http sylius-admin https://your-domain.com/_mcp
```

Claude Code handles the full OAuth PKCE flow automatically on first use (opens a browser window for the consent step).

### Option D - Cursor

In Cursor settings → **MCP** → **Add server**:

```json
{
  "sylius-admin": {
    "url": "https://your-domain.com/_mcp",
    "type": "http"
  }
}
```

---

## Security

- All `/_mcp` requests require a valid OAuth 2.0 Bearer token (returns `401` otherwise)
- Authorization is limited to admin users with `ROLE_API_ACCESS`
- Tokens expire after 1 hour; refresh tokens are valid for 30 days
- PKCE (Proof Key for Code Exchange) prevents authorization code interception attacks

---

## Documentation

- [Authentication Flow (OAuth 2.0 PKCE)](docs/authentication.md)
- [Available MCP Tools](docs/tools.md) - 171 tools across 34 resource groups
- [Configuration Reference](docs/configuration.md) - selectively disabling tool groups
- [Troubleshooting](docs/troubleshooting.md)

---

## License

This plugin is released under the [MIT License](LICENSE).
