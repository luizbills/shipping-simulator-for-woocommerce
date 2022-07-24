<?php
/*
Plugin Name: Shipping Simulator for WooCommerce
Version: 1.2.0
Description: Allows your customers to calculate the shipping rates on the product page
Author: Luiz Bills
Author URI: https://github.com/luizbills
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
	// check composer autoload
	$composer_autoload = __DIR__ . '/vendor/autoload.php';
	if ( ! file_exists( $composer_autoload ) ) {
		throw new \Error( $composer_autoload . ' does not exist' );
	}
	include_once $composer_autoload;
} catch ( Throwable $e ) {
	return add_action( 'admin_notices', function () use ( $e ) {
		if ( ! current_user_can( 'install_plugins' ) ) return;
		list( $plugin_name ) = get_file_data( __FILE__, [ 'plugin name' ] );
		$message = sprintf(
			esc_html__( 'Error on plugin %s activation: %s', 'wc-shipping-simulator' ),
			'<strong>' . esc_html( $plugin_name ) . '</strong>',
			'<br><code>' . esc_html( $e->getMessage() ) . '</code>'
		);
		echo "<div class='notice notice-error'><p>$message</p></div>";
	} );
}

// run the plugin
\Shipping_Simulator\Core\Main::start_plugin( __FILE__ );
