# Available MCP Tools

The plugin provides 171 tools organized by resource. All tools require a valid OAuth 2.0 Bearer token.

> The plugin also exposes a Sylius guidelines document as an MCP resource (URI: `sylius://guidelines`), accessible via `resources/list` and `resources/read` — not as a tool.

## Administrators (5)
`list_administrators`, `get_administrator`, `create_administrator`, `update_administrator`, `delete_administrator`

## Products (5)
`list_products`, `get_product`, `create_product`, `update_product`, `delete_product`

## Product Variants (5)
`list_product_variants`, `get_product_variant`, `create_product_variant`, `update_product_variant`, `delete_product_variant`

## Product Attributes (7)
`list_product_attributes`, `get_product_attribute`, `create_product_attribute`, `update_product_attribute`, `delete_product_attribute`, `set_product_attribute_value`, `remove_product_attribute_value`

## Product Options (6)
`list_product_options`, `get_product_option`, `create_product_option`, `update_product_option`, `delete_product_option`, `add_product_option_value`

## Product Images (4)
`list_product_images`, `get_product_image`, `update_product_image`, `delete_product_image`

## Product Reviews (6)
`list_product_reviews`, `get_product_review`, `update_product_review`, `delete_product_review`, `accept_product_review`, `reject_product_review`

## Product Associations (5)
`list_product_associations`, `get_product_association`, `create_product_association`, `update_product_association`, `delete_product_association`

## Product Association Types (5)
`list_product_association_types`, `get_product_association_type`, `create_product_association_type`, `update_product_association_type`, `delete_product_association_type`

## Taxons / Categories (5)
`list_taxons`, `get_taxon`, `create_taxon`, `update_taxon`, `delete_taxon`

## Product Taxons / Category Assignments (5)
`list_product_taxons`, `get_product_taxon`, `create_product_taxon`, `update_product_taxon`, `delete_product_taxon`

## Taxon Images (4)
`list_taxon_images`, `get_taxon_image`, `update_taxon_image`, `delete_taxon_image`

## Customers (6)
`list_customers`, `get_customer`, `create_customer`, `update_customer`, `delete_customer_user`, `get_customer_statistics`

## Customer Groups (5)
`list_customer_groups`, `get_customer_group`, `create_customer_group`, `update_customer_group`, `delete_customer_group`

## Addresses (3)
`list_customer_addresses`, `get_address`, `update_address`

## Orders (8)
`list_orders`, `get_order`, `list_order_items`, `get_order_item`, `list_order_payments`, `list_order_shipments`, `cancel_order`, `resend_order_email`

## Payments (4)
`list_payments`, `get_payment`, `complete_payment`, `refund_payment`

## Shipments (4)
`list_shipments`, `get_shipment`, `ship_shipment`, `resend_shipment_email`

## Channels (5)
`list_channels`, `get_channel`, `create_channel`, `update_channel`, `delete_channel`

## Promotions (7)
`list_promotions`, `get_promotion`, `create_promotion`, `update_promotion`, `delete_promotion`, `archive_promotion`, `restore_promotion`

## Coupons (6)
`list_coupons`, `get_coupon`, `create_coupon`, `update_coupon`, `delete_coupon`, `generate_coupons`

## Catalog Promotions (5)
`list_catalog_promotions`, `get_catalog_promotion`, `create_catalog_promotion`, `update_catalog_promotion`, `delete_catalog_promotion`

## Shipping Methods (7)
`list_shipping_methods`, `get_shipping_method`, `create_shipping_method`, `update_shipping_method`, `delete_shipping_method`, `archive_shipping_method`, `restore_shipping_method`

## Shipping Categories (5)
`list_shipping_categories`, `get_shipping_category`, `create_shipping_category`, `update_shipping_category`, `delete_shipping_category`

## Zones (5)
`list_zones`, `get_zone`, `create_zone`, `update_zone`, `delete_zone`

## Zone Members (3)
`list_zone_members`, `add_zone_member`, `remove_zone_member`

## Countries (4)
`list_countries`, `get_country`, `create_country`, `update_country`

## Provinces (5)
`list_provinces`, `get_province`, `create_province`, `update_province`, `delete_province`

## Currencies (3)
`list_currencies`, `get_currency`, `create_currency`

## Exchange Rates (5)
`list_exchange_rates`, `get_exchange_rate`, `create_exchange_rate`, `update_exchange_rate`, `delete_exchange_rate`

## Locales (4)
`list_locales`, `get_locale`, `create_locale`, `delete_locale`

## Payment Methods (5)
`list_payment_methods`, `get_payment_method`, `create_payment_method`, `update_payment_method`, `delete_payment_method`

## Tax Categories (5)
`list_tax_categories`, `get_tax_category`, `create_tax_category`, `update_tax_category`, `delete_tax_category`

## Tax Rates (5)
`list_tax_rates`, `get_tax_rate`, `create_tax_rate`, `update_tax_rate`, `delete_tax_rate`
