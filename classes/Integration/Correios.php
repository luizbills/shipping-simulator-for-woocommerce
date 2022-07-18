<?php

namespace Shipping_Simulator\Integration;

use Shipping_Simulator\Helpers as h;
use WC_Correios_Autofill_Addresses;

final class Correios {
	protected static $instace = null;

	public static function instance () {
		return self::$instace;
	}

	public function __start () {
		self::$instace = $this;
		add_action( 'wc_shipping_simulator_load_integrations', [ $this, 'add_hooks' ] );
	}

	public function is_enabled () {
		return apply_filters(
			'wc_shipping_simulator_integration_correios_enabled',
			class_exists( 'WC_Correios' )
		);
	}

	public function add_hooks () {
		if ( $this->is_enabled() ) {
			add_filter( 'wc_shipping_simulator_shipping_package_rates', [ $this, 'shipping_package_rates' ] );
			add_action( 'wc_shipping_simulator_request_results_html', [ $this, 'request_results_html' ], 5, 3 );
		}
	}

	public function shipping_package_rates ( $rates ) {
		if ( count( $rates ) > 0 ) {
			foreach ( $rates as $rate ) {
				if ( h::str_starts_with( $rate->get_method_id(), 'correios-' ) ) {
					$metadata = $rate->get_meta_data();
					$delivery = intval( h::get( $metadata['_delivery_forecast'] ) );
					if ( $delivery > 0 ) {
						$label = wc_correios_get_estimating_delivery( $rate->get_label(), $delivery );
						$rate->set_label( $label );
					}
				}
			}
		}
		return $rates;
	}

	public function request_results_html ( $html, $rates, $posted ) {
		if ( count( $rates ) > 0 ) {
			$postcode = h::get( $posted['postcode'] );
			$result = WC_Correios_Autofill_Addresses::get_address( $postcode );
			$address = $postcode;

			if ( $result ) {
				$parts = [
					h::get( $result->address ),
					h::get( $result->city ),
					h::get( $result->state )
				];
				$address = apply_filters(
					'wc_shipping_simulator_integration_correios_results_address',
					implode( ', ', array_filter( $parts ) ),
					$result
				);
			}

			$text = sprintf(
				esc_html__( 'Shipping options for %s', 'wc-shipping-simulator' ),
				'<strong>' . $address . '</strong>'
			);

			$html = str_replace(
				'<table',
				'<div id="wc-shipping-sim-results-address">' . $text . '</div><table',
				$html
			);
		}
		return $html;
	}
}
