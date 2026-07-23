# Configuration Reference

## Disabling tool groups

You can selectively disable tool groups in `config/packages/sylius_admin_mcp_server.yaml`:

```yaml
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

Set any group to `false` to exclude its tools from the MCP server.

## Allowed hosts (ngrok / reverse proxy)

`symfony/mcp-bundle` validates the `Host` header of every HTTP request against an allowlist. When using a tunnel (ngrok, Cloudflare Tunnel, LocalCan) or a custom domain, add the public hostname to the `mcp.http.allowed_hosts` list:

```yaml
# config/packages/sylius_admin_mcp_server.yaml
imports:
    - { resource: "@SyliusAdminMcpServerPlugin/config/config.yaml" }

mcp:
    http:
        allowed_hosts:
            - 'your-tunnel.ngrok-free.dev'
            - 'your-domain.com'
            - 'localhost'
            - '127.0.0.1'
```

Without this, Claude Code (and other MCP clients) will complete the OAuth flow successfully but fail on the first `initialize` request and report "Got new credentials, but sylius rejected them on reconnect."
