<?php

namespace Shipping_Simulator\Integration;

use Shipping_Simulator\Helpers as h;
use WC_Correios_Autofill_Addresses;

final class Correios {
	protected static $instance = null;
	protected $address_cache = null;

	public static function instance () {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __start () {
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
			add_filter( 'wc_shipping_simulator_package_data', [ $this, 'fill_package_destination' ] );
			add_filter( 'wc_shipping_simulator_package_rates', [ $this, 'package_rates' ] );
			add_filter( 'wc_shipping_simulator_results_title_address', [ $this, 'results_title_address' ], 10, 2 );
		}
	}

	public function fill_package_destination ( $package ) {
		$dest = $package['destination'];
		if ( 'BR' === h::get( $dest['country'] ) ) {
			$postcode = h::get( $dest['postcode'] );
			$address = $this->get_address( $postcode );
			if ( null !== $address ) {
				$dest = [
					'postcode' => $postcode,
					'country' => 'BR',
					'state' => $address->state,
					'city' => $address->city,
					'address' => implode(
						', ',
						array_filter( [
							h::get( $address->address ),
							h::get( $address->neighborhood ),
						] )
					),
					'address_1' => h::get( $address->address, '' ),
					'address_2' => '',
				];
				$package['destination'] = $dest;
			}
		}
		return $package;
	}

	public function package_rates ( $rates ) {
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

	public function results_title_address ( $address_string, $data ) {
		$postcode = h::get( $data['postcode'] );
		$address = $this->get_address( $postcode );
		if ( null !== $address ) {
			$parts = [
				h::get( $address->address ),
				h::get( $address->city ),
				h::get( $address->state )
			];
			$address_string = '<strong>' . apply_filters(
				'wc_shipping_simulator_integration_correios_results_address',
				implode( ', ', array_filter( $parts ) ),
				$address
			) . '</strong>';
		}
		return $address_string;
	}

	public function get_address ( $postcode ) {
		$address = null;
		if ( $this->address_cache && $postcode === $this->address_cache->postcode ) {
			$address = $this->address_cache;
		} else {
			$address = $postcode ? WC_Correios_Autofill_Addresses::get_address( $postcode ) : null;
			if ( h::filled( $address ) ) {
				$address->postcode = $postcode;
				$this->address_cache = $address; // cache
			}
		}
		return $address;
	}
}
