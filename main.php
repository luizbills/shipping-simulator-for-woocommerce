<?php
/*
Plugin Name: Shipping Simulator for WooCommerce
Plugin URI: https://github.com/luizbills/shipping-simulator-for-woocommerce
Description: Allows your customers to calculate the shipping rates on the product page
Version: 2.3.3
Requires at least: 4.9
Requires PHP: 7.4
Author: Luiz Bills
Author URI: https://luizpb.com
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: wc-shipping-simulator
Domain Path: /languages

Shipping Simulator for WooCommerce is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, version 3 of the License.

Shipping Simulator for WooCommerce is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

You should have received a copy of the GNU General Public License
along with Shipping Simulator for WooCommerce. If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

// prevents your PHP files from being executed via direct browser access
defined( 'WPINC' ) || exit( 1 );

load_plugin_textdomain( 'wc-shipping-simulator', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

try {
	// Check PHP Version
	$php_expected = '7.4';
	$php_current = PHP_VERSION;
	if ( version_compare( $php_current, $php_expected, '<' ) ) {
		throw new Error(
			sprintf(
				// translators: the %s are PHP versions
				esc_html__( "This plugin requires PHP version %s or later (your server PHP version is %s)", 'wc-shipping-simulator' ),
				$php_expected, esc_html( $php_current )
			)
		);
	}

	// check composer autoload
	$composer_autoload = __DIR__ . '/vendor/autoload.php';
	if ( ! file_exists( $composer_autoload ) ) {
		throw new Error( $composer_autoload . ' does not exist' );
	}
	include_once $composer_autoload;
} catch ( Throwable $e ) {
	return add_action( 'admin_notices', function () use ( $e ) {
		if ( ! current_user_can( 'install_plugins' ) ) return;
		list( $plugin_name ) = get_file_data( __FILE__, [ 'plugin name' ] );
		$message = sprintf(
			/* translators: %1$s is replaced with plugin name and %2$s with an error message */
			esc_html__( 'Error on %1$s plugin activation: %2$s', 'wc-shipping-simulator' ),
			'<strong>' . esc_html( $plugin_name ) . '</strong>',
			'<br><code>' . esc_html( $e->getMessage() ) . '</code>'
		);
		echo "<div class='notice notice-error'><p>$message</p></div>";
	} );
}

// run the plugin
\Shipping_Simulator\Core\Main::start_plugin( __FILE__ );
