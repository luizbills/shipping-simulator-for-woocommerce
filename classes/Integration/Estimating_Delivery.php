<?php

namespace Shipping_Simulator\Integration;

use Shipping_Simulator\Helpers as h;

final class Estimating_Delivery {
	protected $state_list = null;
	protected static $instance = null;

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
			'wc_shipping_simulator_integration_estimating_delivery_enabled',
			true
		);
	}

	public function add_hooks () {
		if ( ! $this->is_enabled() ) return;

		\add_filter( 'wc_shipping_simulator_package_rates', [ $this, 'package_rates' ] );
	}

	/**
	 * @param \WC_Shipping_Rate[] $rates
	 * @return \WC_Shipping_Rate[]
	 */
	public function package_rates ( $rates ) {
		if ( count( $rates ) === 0 ) return $rates;

		foreach ( $rates as $rate ) {
			$days = $this->get_delivery_days( $rate );
			if ( $days < 1 ) continue;

			$label = $rate->get_label();
			$delivery = h::get_estimating_delivery( $days );
			$rate->set_label( "$label ($delivery)" );
		}

		return $rates;
	}

	/**
	 * @param \WC_Shipping_Rate $rate
	 * @return integer
	 */
	protected function get_delivery_days ( $rate ) {
		$key = \apply_filters(
			'wc_shipping_simulator_integration_estimating_delivery_metadata',
			'_delivery_forecast',
			$rate
		);
		$value = 0;
		if ( $key ) {
			$metadata = $rate->get_meta_data();
			$value = $metadata[ $key ] ?? 0;
		}
		return intval( \apply_filters(
			'wc_shipping_simulator_integration_estimating_delivery_days',
			$value,
			$rate
		) );
	}
}
