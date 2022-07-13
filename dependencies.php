<?php

use Shipping_Simulator\Helpers as h;

defined( 'WPINC' ) || exit( 1 );

return [
	'php' => [
		'message' => function () {
			$req_version = h::config_get( 'REQUIRED_PHP_VERSION', false );
			return sprintf(
				__( "Update your %s version to $req_version or later.", 'wc-shipping-simulator' ),
				'<strong>PHP</strong>'
			);
		},
		'check' => function () {
			$req_version = h::config_get( 'REQUIRED_PHP_VERSION', false );
			$serv_version = \preg_replace( '/[^0-9\.]/', '', PHP_VERSION );
			return $req_version && $serv_version ? \version_compare( $serv_version, $req_version, '>=' ) : true;
		}
	],

	'woocommerce' => [
		'message' => sprintf(
			__( 'Install and activate the %s plugin.', 'wc-shipping-simulator' ),
			'<strong>WooCommerce</strong>'
		),
		'check' => function () {
			return \function_exists( 'WC' );
		}
	],
];
