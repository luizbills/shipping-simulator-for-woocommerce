# Changelog

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 1.1.1 - 2022-07-20 - [Source changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.1.0...1.1.1)

-   Remove `Update URI: false` from header.

## 1.1.0 - 2022-07-20 - [Source changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.0.2...1.1.0)

-   Fix correios integration
-   Fix shipping simulations with product variations
-   New settings option: Debug mode (disabled by default)
-   New settings option: Product variation is required (enabled by default). Disable this option to allow customers simulate shipping rates even when a variation is not selected on variable products.
-   Now is possible log the shipping simulations with debug mode enabled
-   Now a box with helpful informations appears on product page with debug mode enabled
-   New filter hook: `wc_shipping_simulator_package_validate_virtual_product`
-   Renamed filter hook: from `wc_shipping_simulator_shipping_package_item` to `wc_shipping_simulator_package_item`
-   Renamed filter hook: from `wc_shipping_simulator_shipping_package_rates` to `wc_shipping_simulator_package_rates`
-   Renamed filter hook: from `wc_shipping_simulator_shipping_package_data` to `wc_shipping_simulator_package_data`

## 1.0.2 - 2022-07-19 - [Source changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.0.1...1.0.2)

-   Improve frontend javascript

## 1.0.1 - 2022-07-19 - [Source changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.0.0...1.0.1)

-   Move support and donation links from plugin actions to plugin meta

## 1.0.0 - 2022-07-18

-   Initial release
