<?php

use Shipping_Simulator\Helpers as h;

defined( 'WPINC' ) || exit( 1 );

return [
	'woocommerce' => [
		'check' => 'function:WC',
		'message' => sprintf(
			/* translators: %s is replaced with a required plugin name */
			__( 'Install and activate the %s plugin.', 'wc-shipping-simulator' ),
			'<strong>WooCommerce</strong>'
		),
	],

	'woocommerce-shipping' => [
		'check' => function () {
			return 'disabled' !== get_option( 'woocommerce_ship_to_countries' );
		},
		'message' => sprintf(
			/* translators: %s is replaced with a required option */
			__( 'The WooCommerce option %s is disabled.', 'wc-shipping-simulator' ),
			'<strong>' . esc_html__( 'Shipping location(s)', 'wc-shipping-simulator' ) . '</strong>'
		),
	]
];
