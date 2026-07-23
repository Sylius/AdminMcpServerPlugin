# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Setup
```bash
# Install dependencies (requires PHP 8.2+, Composer)
composer install

# Build frontend assets (requires Node.js 20+, Yarn)
yarn install && yarn build
vendor/bin/console assets:install

# Database setup
vendor/bin/console doctrine:database:create
vendor/bin/console doctrine:migrations:migrate -n
vendor/bin/console sylius:fixtures:load -n

# Generate JWT keypair (if not already done)
vendor/bin/console lexik:jwt:generate-keypair

# Start development server
symfony server:start -d
```

### Testing
```bash
# PHPUnit tests
vendor/bin/phpunit

# Behat tests (non-JS)
vendor/bin/behat --strict --tags="~@javascript&&~@mink:chromedriver"
```

### Code Quality
```bash
# PHPStan (level max)
vendor/bin/phpstan analyse -c phpstan.neon -l max src/

# Coding standards
vendor/bin/ecs check src/ config/
vendor/bin/ecs check src/ config/ --fix
```

### Composer Scripts
```bash
# Database reset with fixtures
composer run database-reset

# Frontend rebuild
composer run frontend-clear

# Complete initialization
composer run test-app-init
```

## Architecture

This is the **Sylius Admin MCP Server Plugin** — it exposes the Sylius Admin API as an MCP (Model Context Protocol) server with OAuth 2.0 PKCE authentication.

### Core Structure

```
src/
├── Api/
│   ├── ApiClientInterface.php          # HTTP client interface (GET/POST/PUT/PATCH/DELETE)
│   ├── HttpApiClient.php               # Implementation: JWT auth + request execution
│   ├── HttpAuthenticator.php           # Login, token refresh
│   └── AuthenticatorInterface.php
├── Controller/OAuth/
│   ├── AuthorizationController.php     # GET/POST /admin/mcp/oauth/authorize (consent page)
│   ├── TokenController.php             # POST /_mcp/oauth/token
│   ├── RegistrationController.php      # POST /_mcp/oauth/register (dynamic client reg)
│   └── WellKnownController.php         # GET /.well-known/oauth-authorization-server
├── DependencyInjection/
│   ├── Configuration.php               # sylius_admin_mcp_server.tools.* config
│   └── SyliusAdminMcpServerExtension.php  # Extension: loads services, prepends league config
├── Entity/OAuth/
│   └── OAuthClient.php                 # Doctrine entity: registered OAuth clients
├── Migrations/
│   └── Version20260721000001.php       # Creates oauth_clients, auth_codes, refresh_tokens tables
├── Provider/
│   ├── OAuthJwtTokenProvider.php       # Reads Bearer user → creates Sylius API JWT
│   └── CredentialsTokenProvider.php    # Login with email/password → JWT
├── Repository/OAuth/
│   ├── OAuthAccessTokenRepository.php
│   ├── OAuthAuthorizationCodeRepository.php
│   ├── OAuthClientRepository.php
│   └── OAuthRefreshTokenRepository.php
├── Security/Mcp/
│   └── McpBearerAuthListener.php       # kernel.request priority 10: validates Bearer tokens
├── Session/
│   └── SessionTokenStorage.php         # Stores OAuth auth request between GET/POST
├── Tool/                               # 171 MCP tools organized by resource
│   ├── Administrator/
│   ├── CatalogPromotion/
│   ├── Channel/
│   ├── Country/
│   ├── Coupon/
│   ├── Currency/
│   ├── Customer/
│   ├── ExchangeRate/
│   ├── Locale/
│   ├── Order/
│   ├── Payment/
│   ├── PaymentMethod/
│   ├── Product/
│   ├── ProductAssociation/
│   ├── ProductAssociationType/
│   ├── ProductAttribute/
│   ├── ProductImage/
│   ├── ProductOption/
│   ├── ProductReview/
│   ├── ProductTaxon/
│   ├── ProductVariant/
│   ├── Promotion/
│   ├── Province/
│   ├── Shipment/
│   ├── ShippingCategory/
│   ├── ShippingMethod/
│   ├── TaxCategory/
│   ├── TaxRate/
│   ├── Taxon/
│   ├── TaxonImage/
│   ├── Zone/
│   └── ZoneMember/
└── SyliusAdminMcpServerPlugin.php      # Main plugin class
```

### Tool Pattern

Each tool is a `final readonly class` with:
- `#[McpTool(name: '...', description: '...')]` attribute
- `__invoke(string $param, ...)` method returning JSON string
- Injected `ApiClientInterface $client`

Tools are registered via `config/mcp_services.php` with `mcp.tool` tag.

### OAuth Authentication Flow

1. Client registers via `POST /_mcp/oauth/register` → stored in `sylius_admin_mcp_oauth_clients`
2. User visits `GET /admin/mcp/oauth/authorize?...` → must be logged in admin with `ROLE_API_ACCESS`
3. User approves consent form → `POST /admin/mcp/oauth/authorize`
4. App gets authorization code → exchanges via `POST /_mcp/oauth/token`
5. App uses Bearer token for `POST /_mcp` requests
6. `McpBearerAuthListener` validates token → sets `_mcp_oauth_admin_user` on request attributes
7. `OAuthJwtTokenProvider` reads user → creates Sylius JWT for API calls

### Database

Three tables created by the plugin migration:
- `sylius_admin_mcp_oauth_clients` — registered OAuth clients
- `sylius_admin_mcp_oauth_authorization_codes` — auth codes (short-lived)
- `sylius_admin_mcp_oauth_refresh_tokens` — refresh tokens (30 days)

Access tokens are JWT (stateless), not persisted to DB.

### Test Application

The test application lives at `tests/TestApplication/` and uses `sylius/test-application` as a base. Environment config is in `tests/TestApplication/.env`.

Key env vars for local development:
```
DATABASE_URL=mysql://root:root@127.0.0.1/sylius_mcp_dev
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your-passphrase
SYLIUS_ADMIN_MCP_SERVER_API_URL=https://127.0.0.1:8003/api/v2/admin/
SYLIUS_ADMIN_MCP_SERVER_API_EMAIL=api@example.com
SYLIUS_ADMIN_MCP_SERVER_API_PASSWORD=sylius-api
SYLIUS_ADMIN_MCP_SERVER_VERIFY_SSL=false
SYLIUS_ADMIN_MCP_SERVER_OAUTH_ENCRYPTION_KEY=<32-byte-hex>
```
