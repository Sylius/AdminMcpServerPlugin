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
# Admin API user credentials (must have ROLE_API_ACCESS)
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

This creates three OAuth tables:
- `sylius_admin_mcp_oauth_clients`
- `sylius_admin_mcp_oauth_authorization_codes`
- `sylius_admin_mcp_oauth_refresh_tokens`

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

> **Important**: Only admin users with `ROLE_API_ACCESS` can use the OAuth authorization flow. Users without this role will see "Authorization Failed" on the consent page.

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

Expected routes:
- `GET /.well-known/oauth-authorization-server`
- `GET /.well-known/oauth-protected-resource`
- `POST /_mcp/oauth/register`
- `GET|POST /admin/mcp/oauth/authorize`
- `POST /_mcp/oauth/token`
- `GET|POST|DELETE|OPTIONS /_mcp`

---

## Authentication Flow (OAuth 2.0 PKCE)

The plugin implements OAuth 2.0 Authorization Code flow with PKCE. Here is the complete flow:

### Step 1 — Register an OAuth client

```bash
curl -X POST "https://your-domain.com/_mcp/oauth/register" \
  -H "Content-Type: application/json" \
  -d '{
    "client_name": "My MCP Client",
    "redirect_uris": ["http://localhost:3000/callback"],
    "grant_types": ["authorization_code"],
    "token_endpoint_auth_method": "none"
  }'
```

Response:
```json
{
  "client_id": "7c668d1d25b54fa65bc2bdc5a31f8b7e5701034c",
  "client_id_issued_at": 1784722394,
  "redirect_uris": ["http://localhost:3000/callback"],
  "grant_types": ["authorization_code"],
  "token_endpoint_auth_method": "none",
  "client_name": "My MCP Client"
}
```

### Step 2 — Generate PKCE code verifier and challenge

```python
import os, base64, hashlib
verifier = base64.urlsafe_b64encode(os.urandom(32)).rstrip(b'=').decode()
challenge = base64.urlsafe_b64encode(hashlib.sha256(verifier.encode()).digest()).rstrip(b'=').decode()
print(f"Verifier: {verifier}")
print(f"Challenge: {challenge}")
```

### Step 3 — Redirect user to authorization page

```
GET /admin/mcp/oauth/authorize
  ?response_type=code
  &client_id=YOUR_CLIENT_ID
  &redirect_uri=YOUR_REDIRECT_URI
  &code_challenge=YOUR_CHALLENGE
  &code_challenge_method=S256
  &state=RANDOM_STATE
```

The user must log in with an admin account that has `ROLE_API_ACCESS`. They see a consent page and can approve or deny the request.

### Step 4 — Exchange authorization code for access token

After approval, the user is redirected to your `redirect_uri` with a `code` parameter. Exchange it:

```bash
curl -X POST "https://your-domain.com/_mcp/oauth/token" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "grant_type=authorization_code" \
  -d "client_id=YOUR_CLIENT_ID" \
  -d "redirect_uri=YOUR_REDIRECT_URI" \
  -d "code=THE_AUTH_CODE" \
  -d "code_verifier=YOUR_VERIFIER"
```

Response:
```json
{
  "token_type": "Bearer",
  "expires_in": 3600,
  "access_token": "eyJ0eXAiOi...",
  "refresh_token": "def50200..."
}
```

### Step 5 — Use the access token with MCP

```bash
# Initialize MCP session
SESSION_ID=$(curl -s -D - -X POST "https://your-domain.com/_mcp" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test","version":"1"}}}' \
  | grep -i "mcp-session-id:" | tr -d '\r' | awk '{print $2}')

# Call an MCP tool
curl -X POST "https://your-domain.com/_mcp" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Mcp-Session-Id: $SESSION_ID" \
  -d '{"jsonrpc":"2.0","id":2,"method":"tools/call","params":{"name":"list_channels","arguments":{}}}'
```

---

## Available MCP Tools

The plugin provides 171 tools organized by resource:

### Administrators (5)
`list_administrators`, `get_administrator`, `create_administrator`, `update_administrator`, `delete_administrator`

### Products (5)
`list_products`, `get_product`, `create_product`, `update_product`, `delete_product`

### Product Variants (5)
`list_product_variants`, `get_product_variant`, `create_product_variant`, `update_product_variant`, `delete_product_variant`

### Product Attributes (7)
`list_product_attributes`, `get_product_attribute`, `create_product_attribute`, `update_product_attribute`, `delete_product_attribute`, `set_product_attribute_value`, `remove_product_attribute_value`

### Product Options (6)
`list_product_options`, `get_product_option`, `create_product_option`, `update_product_option`, `delete_product_option`, `add_product_option_value`

### Product Images (4)
`list_product_images`, `get_product_image`, `update_product_image`, `delete_product_image`

### Product Reviews (6)
`list_product_reviews`, `get_product_review`, `update_product_review`, `delete_product_review`, `accept_product_review`, `reject_product_review`

### Product Associations (5)
`list_product_associations`, `get_product_association`, `create_product_association`, `update_product_association`, `delete_product_association`

### Product Association Types (5)
`list_product_association_types`, `get_product_association_type`, `create_product_association_type`, `update_product_association_type`, `delete_product_association_type`

### Taxons / Categories (5)
`list_taxons`, `get_taxon`, `create_taxon`, `update_taxon`, `delete_taxon`

### Product Taxons / Category Assignments (5)
`list_product_taxons`, `get_product_taxon`, `create_product_taxon`, `update_product_taxon`, `delete_product_taxon`

### Taxon Images (4)
`list_taxon_images`, `get_taxon_image`, `update_taxon_image`, `delete_taxon_image`

### Customers (6)
`list_customers`, `get_customer`, `create_customer`, `update_customer`, `delete_customer_user`, `get_customer_statistics`

### Customer Groups (5)
`list_customer_groups`, `get_customer_group`, `create_customer_group`, `update_customer_group`, `delete_customer_group`

### Addresses (3)
`list_customer_addresses`, `get_address`, `update_address`

### Orders (8)
`list_orders`, `get_order`, `list_order_items`, `get_order_item`, `list_order_payments`, `list_order_shipments`, `cancel_order`, `resend_order_email`

### Payments (4)
`list_payments`, `get_payment`, `complete_payment`, `refund_payment`

### Shipments (4)
`list_shipments`, `get_shipment`, `ship_shipment`, `resend_shipment_email`

### Channels (5)
`list_channels`, `get_channel`, `create_channel`, `update_channel`, `delete_channel`

### Promotions (7)
`list_promotions`, `get_promotion`, `create_promotion`, `update_promotion`, `delete_promotion`, `archive_promotion`, `restore_promotion`

### Coupons (6)
`list_coupons`, `get_coupon`, `create_coupon`, `update_coupon`, `delete_coupon`, `generate_coupons`

### Catalog Promotions (5)
`list_catalog_promotions`, `get_catalog_promotion`, `create_catalog_promotion`, `update_catalog_promotion`, `delete_catalog_promotion`

### Shipping Methods (7)
`list_shipping_methods`, `get_shipping_method`, `create_shipping_method`, `update_shipping_method`, `delete_shipping_method`, `archive_shipping_method`, `restore_shipping_method`

### Shipping Categories (5)
`list_shipping_categories`, `get_shipping_category`, `create_shipping_category`, `update_shipping_category`, `delete_shipping_category`

### Zones (5)
`list_zones`, `get_zone`, `create_zone`, `update_zone`, `delete_zone`

### Zone Members (3)
`list_zone_members`, `add_zone_member`, `remove_zone_member`

### Countries (4)
`list_countries`, `get_country`, `create_country`, `update_country`

### Provinces (5)
`list_provinces`, `get_province`, `create_province`, `update_province`, `delete_province`

### Currencies (3)
`list_currencies`, `get_currency`, `create_currency`

### Exchange Rates (5)
`list_exchange_rates`, `get_exchange_rate`, `create_exchange_rate`, `update_exchange_rate`, `delete_exchange_rate`

### Locales (4)
`list_locales`, `get_locale`, `create_locale`, `delete_locale`

### Payment Methods (5)
`list_payment_methods`, `get_payment_method`, `create_payment_method`, `update_payment_method`, `delete_payment_method`

### Tax Categories (5)
`list_tax_categories`, `get_tax_category`, `create_tax_category`, `update_tax_category`, `delete_tax_category`

### Tax Rates (5)
`list_tax_rates`, `get_tax_rate`, `create_tax_rate`, `update_tax_rate`, `delete_tax_rate`

> **Note**: The plugin also exposes a Sylius guidelines document as an MCP protocol resource (URI: `sylius://guidelines`), accessible via `resources/list` and `resources/read` in the MCP protocol — not as a tool.

---

## Configuration Reference

You can selectively disable tool groups in your application configuration:

```yaml
# config/packages/sylius_admin_mcp_server.yaml
imports:
    - { resource: "@SyliusAdminMcpServerPlugin/config/config.yaml" }

sylius_admin_mcp_server:
    tools:
        administrators: true
        products: true
        product_variants: true
        product_attributes: true
        product_options: true
        product_reviews: true
        product_taxons: true
        product_associations: true
        product_association_types: true
        taxons: true
        customers: true
        customer_groups: true
        addresses: true
        channels: true
        currencies: true
        exchange_rates: true
        locales: true
        countries: true
        payment_methods: true
        tax_categories: true
        tax_rates: true
        shipping_methods: true
        shipping_categories: true
        zones: true
        zone_members: true
        promotions: true
        coupons: true
        catalog_promotions: true
        shipments: true
        payments: true
        orders: true
        provinces: true
        product_images: true
        taxon_images: true
        mcp_resources: true
```

---

## Troubleshooting

### "Invalid key supplied" on OAuth endpoints

The JWT private/public keys are missing. Run:

```bash
php bin/console lexik:jwt:generate-keypair
```

### "Could not find the entrypoints file from Webpack"

Frontend assets are not built. Run:

```bash
yarn install
yarn build
php bin/console assets:install
```

Requires Node.js 20+. If using nvm: `nvm use 20`.

### "Cannot create union with both object and class type" during cache warmup

The `symfony/type-info` patch is not applied. See Step 1 of Installation for the patch instructions.

### Plugin migration not running automatically

**Root cause**: If using an older version of the plugin (`< 1.1`), the migration used the generic `DoctrineMigrations` namespace which conflicts with the host application's migration path. This is fixed in current versions — the plugin now uses the `Sylius\AdminMcpServerPlugin\Migrations` namespace.

If you have a conflict, verify your installed version's migration namespace matches the path in your `doctrine_migrations` configuration:

```bash
php bin/console debug:config doctrine_migrations | grep -A5 "migrations_paths"
php bin/console doctrine:migrations:list | grep AdminMcpServer
```

### "Authorization Failed" — "User does not have API access"

The logged-in admin user lacks `ROLE_API_ACCESS`. Grant it:

```sql
UPDATE sylius_admin_user
SET roles = JSON_ARRAY_APPEND(roles, '$', 'ROLE_API_ACCESS')
WHERE email = 'api@example.com';
```

### MCP endpoint returns 401

All requests to `/_mcp` require a valid Bearer token. Obtain one via the OAuth flow (Steps 1-5 of Authentication Flow above).

### The flex recipe creates wrong `league_oauth2_server.yaml`

The `league/oauth2-server-bundle` flex recipe generates a config with `OAUTH_PRIVATE_KEY` / `OAUTH_PASSPHRASE` / `OAUTH_ENCRYPTION_KEY` variables. The plugin uses `JWT_SECRET_KEY` / `JWT_PASSPHRASE` / `SYLIUS_ADMIN_MCP_SERVER_OAUTH_ENCRYPTION_KEY` (reusing the existing Sylius JWT keys). Always replace the recipe-generated file with the one shown in Step 4 of Installation.

---

## Usage Examples

### List all products

```bash
curl -X POST "https://your-domain.com/_mcp" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Mcp-Session-Id: $SESSION_ID" \
  -d '{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"list_products","arguments":{}}}'
```

### Create a product

```bash
curl -X POST "https://your-domain.com/_mcp" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Mcp-Session-Id: $SESSION_ID" \
  -d '{
    "jsonrpc":"2.0","id":2,"method":"tools/call",
    "params":{
      "name":"create_product",
      "arguments":{
        "code":"BLUE_MUG_001",
        "name":"Blue Mug",
        "channels":["/api/v2/admin/channels/FASHION_WEB"],
        "translations":"{\"en_US\":{\"name\":\"Blue Mug\",\"description\":\"A lovely blue mug.\"}}"
      }
    }
  }'
```

### Create a promotion

```bash
curl -X POST "https://your-domain.com/_mcp" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Mcp-Session-Id: $SESSION_ID" \
  -d '{
    "jsonrpc":"2.0","id":3,"method":"tools/call",
    "params":{
      "name":"create_promotion",
      "arguments":{
        "code":"SUMMER_SALE",
        "name":"Summer Sale 2026",
        "channels":["/api/v2/admin/channels/FASHION_WEB"]
      }
    }
  }'
```

---

## MCP Client Configuration (Claude Desktop / Cursor)

To use this plugin with Claude Desktop or Cursor, configure the MCP client to connect to your Sylius store. The plugin follows the MCP HTTP transport specification with OAuth 2.0 PKCE for authentication.

The plugin's discovery endpoints enable automatic configuration:
- `/.well-known/oauth-authorization-server` — OAuth server metadata
- `/.well-known/oauth-protected-resource` — Resource server metadata

---

## Security

- All MCP tool calls require a valid OAuth 2.0 Bearer token
- Authorization is limited to admin users with `ROLE_API_ACCESS`
- Tokens expire after 1 hour; use the refresh token to obtain a new access token
- PKCE (Proof Key for Code Exchange) prevents authorization code interception attacks
- The plugin stores OAuth clients in the database (`sylius_admin_mcp_oauth_clients`)
- Authorization codes and refresh tokens use in-memory persistence by default (stateless)

---

## License

This plugin is released under the [MIT License](LICENSE).
