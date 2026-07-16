<p align="center">
    <a href="https://sylius.com" target="_blank">
        <picture>
          <source media="(prefers-color-scheme: dark)" srcset="https://media.sylius.com/sylius-logo-800-dark.png">
          <source media="(prefers-color-scheme: light)" srcset="https://media.sylius.com/sylius-logo-800.png">
          <img alt="Sylius Logo." src="https://media.sylius.com/sylius-logo-800.png">
        </picture>
    </a>
</p>

<h1 align="center">Plugin Skeleton</h1>

<p align="center">Skeleton for starting Sylius plugins.</p>

## Documentation

For a comprehensive guide on Sylius Plugins development please go to Sylius documentation,
there you will find the <a href="https://docs.sylius.com/plugins-development-guide/how-to-create-a-plugin-for-sylius">Plugin Development Guide</a> - it's a great place to start.

For more information about the **Test Application** included in the skeleton, please refer to the [Sylius documentation](https://docs.sylius.com/plugins-development-guide/test-application).

## MCP Authorization

The MCP server never asks the LLM for credentials. Instead it exposes an OAuth 2.1
(authorization code + PKCE) authorization server, so MCP clients (Claude Desktop,
claude.ai, ...) connect through a browser login flow. The administrator signs in with
their Sylius admin e-mail and password on a dedicated form (`/oauth/authorize`); the
account must hold `ROLE_API_ACCESS`. The access token handed to the client is a regular
Sylius Admin API JWT, and a refresh token keeps the connection alive for up to
`refresh_token_max_lifetime` (default 30 days from the first login, non-sliding) before a
new browser login is required.

### Required environment variable

```dotenv
# Encryption key for OAuth authorization codes / refresh tokens (keep secret).
SYLIUS_ADMIN_MCP_SERVER_OAUTH_ENCRYPTION_KEY=<a-random-secret-string>
```

The OAuth server reuses the existing Sylius `JWT_SECRET_KEY` / `JWT_PUBLIC_KEY` /
`JWT_PASSPHRASE` keys for signing.

### Required `security.yaml`

Symfony does not allow a bundle to inject firewalls, so the host application must add the
MCP firewalls to `config/packages/security.yaml`. They must be declared **before** the
`shop` firewall:

```yaml
security:
    firewalls:
        sylius_admin_mcp_server_oauth:
            pattern: "^/(oauth|\\.well-known)"
            security: false
        sylius_admin_mcp_server_mcp:
            pattern: ^/_mcp
            provider: sylius_api_admin_user_provider
            stateless: true
            jwt: true
        # ... existing admin / api_admin / shop firewalls ...

    access_control:
        - { path: "^/(oauth|\\.well-known)", role: PUBLIC_ACCESS }
        - { path: ^/_mcp, role: ROLE_API_ACCESS }
        # ... existing access control rules ...
```

## Quickstart Installation

Run `composer create-project sylius/plugin-skeleton ProjectName`.

### Traditional

1. From the plugin skeleton root directory, run the following commands:

    ```bash
    (cd vendor/sylius/test-application && yarn install)
    (cd vendor/sylius/test-application && yarn build)
    vendor/bin/console assets:install
   
    vendor/bin/console doctrine:database:create
    vendor/bin/console doctrine:migrations:migrate -n
    # Optionally load data fixtures
    vendor/bin/console sylius:fixtures:load -n
    ```

To be able to set up a plugin's database, remember to configure your database credentials in `tests/TestApplication/.env` and `tests/TestApplication/.env.test`.

2. Run your local server:

      ```bash
      symfony server:ca:install
      symfony server:start -d
      ```

3. Open your browser and navigate to `https://localhost:8000`.

### Docker

1. Execute `make init` to initialize the container and install the dependencies.

2. Execute `make database-init` to create the database and run migrations.

3. (Optional) Execute `make load-fixtures` to load the fixtures.

4. Your app is available at `http://localhost`.

## Usage

### Running plugin tests

  - PHPUnit

    ```bash
    vendor/bin/phpunit
    ```

  - Behat (non-JS scenarios)

    ```bash
    vendor/bin/behat --strict --tags="~@javascript&&~@mink:chromedriver"
    ```

  - Behat (JS scenarios)
 
    1. [Install Symfony CLI command](https://symfony.com/download).
 
    2. Start Headless Chrome:
    
      ```bash
      google-chrome-stable --enable-automation --disable-background-networking --no-default-browser-check --no-first-run --disable-popup-blocking --disable-default-apps --allow-insecure-localhost --disable-translate --disable-extensions --no-sandbox --enable-features=Metal --headless --remote-debugging-port=9222 --window-size=2880,1800 --proxy-server='direct://' --proxy-bypass-list='*' http://127.0.0.1
      ```
    
    3. Install SSL certificates (only once needed) and run test application's webserver on `127.0.0.1:8080`:
    
      ```bash
      symfony server:ca:install
      APP_ENV=test symfony server:start --port=8080 --daemon
      ```
    
    4. Run Behat:
    
      ```bash
      vendor/bin/behat --strict --tags="@javascript,@mink:chromedriver"
      ```
    
  - Static Analysis
      
    - PHPStan
    
      ```bash
      vendor/bin/phpstan analyse -c phpstan.neon -l max src/  
      ```

  - Coding Standard
  
    ```bash
    vendor/bin/ecs check
    ```

### Opening Sylius with your plugin

- Using `test` environment:

    ```bash
    APP_ENV=test vendor/bin/console sylius:fixtures:load -n
    APP_ENV=test symfony server:start -d
    ```
    
- Using `dev` environment:

    ```bash
    vendor/bin/console sylius:fixtures:load -n
    symfony server:start -d
    ```
