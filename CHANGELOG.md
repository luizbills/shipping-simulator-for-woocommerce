# Changelog

All notable changes to this project will be documented in this file.

## 1.6.0 - 2022-10-28

[Source code changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.5.0...1.6.0)

-   Feature: Improve simulator/frontend accessibility. #36
-   New action hook: `wc_shipping_simulator_form_before_button`. #42

## 1.5.0 - 2022-10-25

[Source code changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.4.0...1.5.0)

-   Fix: Shipping simulator appearing for external and grouped products. #39

## 1.4.0 - 2022-09-28

[Source code changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.3.3...1.4.0)

-   Feature: automatically disables built-in shipping simulator of "Melhor Envio" plugin.

## 1.3.3 - 2022-08-02

[Source code changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.3.2...1.3.3)

-   Fix: delete plugin database options on uninstallation.
-   New filter hook: `wc_shipping_simulator_get_template_full_path` can be used to override the path of a template file.

## 1.3.2 - 2022-08-01

[Source code changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.3.1...1.3.2)

-   Fix: conflict with jquery-mask library (present on many sites).

## 1.3.1 - 2022-07-26

[Source code changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.3.0...1.3.1)

-   Feature: Correios integration now fill the package address with city, street and neighborhood. Useful to some shipping methods like Lalamove.
-   Fix: better message when missing a variation.
-   Fix: free shipping was unavailable for non logged in users.
-   Fix: PHP v8+ deprecation notice.
-   i18n: Frontend unexpected error and timeout is now translatable.

## 1.3.0 - 2022-07-25

[Source code changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.2.0...1.3.0)

-   New settings option: Update customer address (disabled by default). Now, the customer address can be updated when a shipping simulation returns shipping options.
-   New filter hook: `wc_shipping_simulator_settings_field`
-   Fix: don't display the simulator in products out of stock.

## 1.2.0 - 2022-07-24

[Source code changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.1.1...1.2.0)

-   Feature: automatically simulates shipping when the customer finishes typing the postcode.
-   Fix: simulator no longer crashes on timeout error.

## 1.1.1 - 2022-07-20

[Source code changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.1.0...1.1.1)

-   Remove `Update URI: false` from header.

## 1.1.0 - 2022-07-20

[Source code changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.0.2...1.1.0)

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

## 1.0.2 - 2022-07-19

[Source code changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.0.1...1.0.2)

-   Improve frontend javascript

## 1.0.1 - 2022-07-19

[Source code changes](https://github.com/luizbills/shipping-simulator-for-woocommerce/compare/1.0.0...1.0.1)

-   Move support and donation links from plugin actions to plugin meta

## 1.0.0 - 2022-07-18

-   Initial release
