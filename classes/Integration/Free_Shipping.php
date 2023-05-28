<?php

namespace Shipping_Simulator\Integration;

use Shipping_Simulator\Helpers as h;
use WC_Shipping_Free_Shipping;

final class Free_Shipping {
	protected static $instance = null;

	public static function instance () {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __start () {
		add_action( 'wc_shipping_simulator_load_integrations', [ $this, 'add_hooks' ], 10 );
	}

	public function is_enabled () {
		return apply_filters(
			'wc_shipping_simulator_integration_free_shipping_enabled',
			true
		);
	}

	public function add_hooks () {
		if ( $this->is_enabled() ) {
			add_filter( 'wc_shipping_simulator_package_rates', [ $this, 'shipping_package_rates' ], 10, 2 );
			add_filter( 'woocommerce_shipping_free_shipping_is_available', [ $this, 'always_avaliable_in_simulator' ], 20, 2 );
		}
	}

	public function shipping_package_rates ( $rates, $package ) {
		if ( count( $rates ) > 0 ) {
			$found = 0;
			foreach ( $rates as $key => $rate ) {
				if ( 'free_shipping' === $rate->get_method_id() ) {
					$found++;
					$method = new WC_Shipping_Free_Shipping( $rate->get_instance_id() );
					$requires_min_amount = in_array( $method->requires, [ 'min_amount', 'either' ] );
					$requires_coupon = in_array( $method->requires, [ 'coupon', 'both' ] );
					if ( $requires_coupon || $requires_min_amount && $package['contents_cost'] < $method->min_amount ) {
						unset( $rates[ $key ] );
						$found--;
					}
				}
			}
			$package['HAS_FREE_SHIPPING'] = $found >= 1;
		}
		return $rates;
	}

	public function always_avaliable_in_simulator ( $is_available, $package ) {
		return h::get( $package['DOING_SHIPPING_SIMULATION'] ) || $is_available;
	}
}
