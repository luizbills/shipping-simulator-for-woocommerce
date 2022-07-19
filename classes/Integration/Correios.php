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
			add_filter( 'wc_shipping_simulator_results_title_address', [ $this, 'results_title_address' ], 10, 2 );
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

	public function results_title_address ( $address, $data ) {
		$result = WC_Correios_Autofill_Addresses::get_address( $data['postcode'] );
		if ( h::filled( $result ) ) {
			$parts = [
				h::get( $result->address ),
				h::get( $result->city ),
				h::get( $result->state )
			];
			$address = '<strong>' . apply_filters(
				'wc_shipping_simulator_integration_correios_results_address',
				implode( ', ', array_filter( $parts ) ),
				$result
			) . '</strong>';
		}
		return $address;
	}
}
