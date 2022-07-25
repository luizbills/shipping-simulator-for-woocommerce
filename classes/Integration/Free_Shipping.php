<?php

namespace Shipping_Simulator\Integration;

use Shipping_Simulator\Helpers as h;
use WC_Shipping_Free_Shipping;

final class Free_Shipping {
	protected static $instace = null;

	public static function instance () {
		return self::$instace;
	}

	public function __start () {
		self::$instace = $this;
		add_action( 'wc_shipping_simulator_load_integrations', [ $this, 'add_hooks' ], 10, 2 );
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
		}
	}

	public function shipping_package_rates ( $rates, $package ) {
		if ( count( $rates ) > 0 ) {
			$found = false;
			foreach ( $rates as $key => $rate ) {
				if ( 'free_shipping' === $rate->get_method_id() ) {
					$method = new WC_Shipping_Free_Shipping( $rate->get_instance_id() );
					if ( in_array( $method->requires, [ 'min_amount', 'either' ] ) && $package['contents_cost'] < $method->min_amount ) {
						unset( $rates[ $key ] );
						$found = true;
					}
				}
			}
			$package['HAS_FREE_SHIPPING'] = $found;
		}
		return $rates;
	}
}
