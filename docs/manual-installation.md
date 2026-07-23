# Manual Installation (without Symfony Flex)

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

## Step 1 - Add the plugin via Composer

```bash
composer require sylius/admin-mcp-server-plugin
```

## Step 2 - Register bundles

Add to your `config/bundles.php`:

```php
return [
    // ... existing bundles ...
    League\Bundle\OAuth2ServerBundle\LeagueOAuth2ServerBundle::class => ['all' => true],
    Symfony\AI\McpBundle\McpBundle::class => ['all' => true],
    Sylius\AdminMcpServerPlugin\SyliusAdminMcpServerPlugin::class => ['all' => true],
];
```

## Step 3 - Import plugin configuration

Create `config/packages/sylius_admin_mcp_server.yaml`:

```yaml
imports:
    - { resource: "@SyliusAdminMcpServerPlugin/config/config.yaml" }
```

> **Note on `league_oauth2_server.yaml`**: importing the plugin configuration also loads the plugin's own `config/packages/league_oauth2_server.yaml`, which configures the OAuth2 server using `SYLIUS_ADMIN_MCP_SERVER_OAUTH_*` environment variables. If your application already has a `config/packages/league_oauth2_server.yaml` (e.g. created by the `league/oauth2-server-bundle` Flex recipe), the plugin's configuration takes precedence and overrides it.

## Step 4 - Import routes

Create `config/routes/sylius_admin_mcp_server.yaml`:

```yaml
sylius_admin_mcp_server:
    resource: "@SyliusAdminMcpServerPlugin/config/routes.yaml"
    type: yaml
```

## Step 5 - Generate OAuth keypair

The plugin uses a dedicated RSA keypair (separate from Lexik JWT) for signing OAuth tokens:

```bash
mkdir -p config/jwt
openssl genrsa -out config/jwt/mcp_private.pem 4096
openssl rsa -in config/jwt/mcp_private.pem -pubout -out config/jwt/mcp_public.pem
```

## Step 6 - Configure environment variables

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
# OAuth RSA keypair generated in Step 5 (separate from Lexik JWT keys)
SYLIUS_ADMIN_MCP_SERVER_OAUTH_PRIVATE_KEY=%kernel.project_dir%/config/jwt/mcp_private.pem
SYLIUS_ADMIN_MCP_SERVER_OAUTH_PUBLIC_KEY=%kernel.project_dir%/config/jwt/mcp_public.pem
SYLIUS_ADMIN_MCP_SERVER_OAUTH_PASSPHRASE=
###< sylius/admin-mcp-server-plugin ###
```

Generate the encryption key:

```bash
openssl rand -hex 32
```

## Step 7 - Run database migrations

```bash
php bin/console doctrine:migrations:migrate -n
```

This creates three OAuth tables: `sylius_admin_mcp_oauth_clients`, `sylius_admin_mcp_oauth_authorization_codes`, `sylius_admin_mcp_oauth_refresh_tokens`.

## Step 8 - Clear cache

```bash
php bin/console cache:clear
php bin/console cache:warmup
```

## Step 9 - Build frontend assets

The admin authorization page requires Sylius admin panel assets. If you haven't already:

```bash
yarn install
yarn build
php bin/console assets:install
```

## Step 10 - Grant API access to an admin user

Only admin users with `ROLE_API_ACCESS` can authorize via the OAuth consent page. Grant it to an existing user via SQL:

```sql
UPDATE sylius_admin_user
SET roles = JSON_ARRAY_APPEND(roles, '$', 'ROLE_API_ACCESS')
WHERE email = 'your@admin.com';
```

Or create a dedicated API user in your fixtures.
