<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Mcp\Resource;

use Mcp\Capability\Attribute\McpResource;
use Mcp\Schema\Content\TextResourceContents;

#[McpResource(
    uri: 'sylius://guidelines',
    name: 'Sylius Admin API Guidelines',
    description: 'Critical rules and patterns for correctly using Sylius Admin MCP tools. Read this before performing any operations.',
    mimeType: 'text/markdown',
)]
final class SyliusGuidelinesResource
{
    public function __invoke(): TextResourceContents
    {
        return new TextResourceContents(
            uri: 'sylius://guidelines',
            text: $this->getContent(),
            mimeType: 'text/markdown',
        );
    }

    private function getContent(): string
    {
        return <<<'MARKDOWN'
        # Sylius Admin MCP — Guidelines

        ## Authentication
        - **Always call `login` first** before any other tool (except `logout`).
        - Use admin credentials (email + password).
        - The token is stored for the session — no need to re-login on every call.

        ## Translations — CRITICAL RULES

        ### Never overwrite other locale translations
        When updating a product, taxon, payment method, etc. that has multiple locale translations
        (e.g. `en_US` and `de_DE`), updating only `en_US` via `update_product` or `update_taxon`
        will **preserve all other locale translations automatically** — the tools handle this internally.

        ### Translation locale codes
        Use BCP 47 locale codes: `en_US`, `pl_PL`, `de_DE`, `fr_FR`, `it_IT`, etc.

        ### To add/update a translation for a new locale
        Simply call `update_product(code, name="...", localeCode="de_DE")` — the tool will fetch
        existing translations, merge the new locale in, and PUT the full set.

        ## Products

        ### Creating a product
        1. Create the product with `create_product` (code, name, channel codes are required).
        2. Create at least one variant with `create_product_variant` (code, productCode, channelCode, price).
        3. Optionally assign to a taxon with `create_product_taxon`.

        ### Product variants
        - `price` is in the **smallest currency unit** (cents/grosze): `4999` = $49.99 / 49,99 PLN.
        - `channelPricings` key = channel code (e.g. `FASHION_WEB`).

        ### Channels in product
        - Pass channel codes as an array: `["FASHION_WEB"]`.
        - The tool converts them to IRIs internally.

        ## Taxons

        ### Taxon update — slug uniqueness
        Slugs must be unique per locale. If you get a 422 on slug, try a different slug value.

        ### Taxon hierarchy
        Use `parentCode` when creating child taxons. Leave empty for root taxons.

        ## Channels

        ### Required fields for create_channel / update_channel
        - `code`, `name`, `localeCode`, `currencyCode`, `taxCalculationStrategy`
        - `taxCalculationStrategy` values: `"order_items_based"` or `"order_item_units_based"` (default)

        ## Tax Rates

        ### Amount format
        `amount` is a **decimal fraction**: `0.23` = 23%, `0.08` = 8%.

        ### Required IRIs
        - `categoryCode` → resolved to `/api/v2/admin/tax-categories/{code}` internally
        - `zoneCode` → resolved to `/api/v2/admin/zones/{code}` internally

        ## Exchange Rates
        - `ratio`: `0.92` means 1 sourceCurrency = 0.92 targetCurrency.
        - Both currencies must exist before creating an exchange rate.

        ## Customers
        - Customers are identified by **numeric ID**, not code.
        - `delete_customer_user` deletes only the ShopUser login account, not the Customer record itself.
        - There is no DELETE endpoint for Customer — use `delete_customer_user` instead.

        ## Addresses
        - Identified by **numeric ID**.
        - `update_address` uses PUT — provide all main fields (firstName, lastName, street, city, postcode, countryCode).

        ## Error handling
        - `422 Unprocessable Entity` → validation error, check the `violations` array in the response.
        - `404 Not Found` → resource does not exist or wrong identifier.
        - `405 Method Not Allowed` → operation not supported for this resource.
        MARKDOWN;
    }
}
