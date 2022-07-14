<?php

namespace Shipping_Simulator\Integration;

use Shipping_Simulator\Helpers as h;

final class Correios {
	protected static $instace = null;

	public static function instance () {
		return self::$instace;
	}

	public function __start () {
		self::$instace = $this;
		if ( class_exists( 'WC_Correios' ) ) {
			add_action( 'wc_shipping_simulator_load_integrations', [ $this, 'add_hooks' ] );
		}
	}

	public function add_hooks () {
		$enabled = apply_filters(
			'wc_shipping_simulator_integration_correios_enabled',
			true
		);
		if ( $enabled ) {
			add_filter( 'wc_shipping_simulator_shipping_package_rates', [ $this, 'shipping_package_rates' ] );
		}
	}

	public function shipping_package_rates ( $rates ) {
		if ( count( $rates ) > 0 ) {
			foreach ( $rates as $rate ) {
				if ( h::str_starts_with( $rate->get_method_id(), 'correios-' ) ) {
					$metadata = $rate->get_meta_data();
					$delivery = absint( h::get( $metadata['_delivery_forecast'] ) );
					if ( $delivery > 0 ) {
						$time = absint( $delivery ) > 1 ? "Entrega em até $delivery dias úteis" : 'Entrega em 1 dia útil';
						$label_suffix = apply_filters(
							'wc_shipping_simulator_integration_correios_rate_label_suffix',
							" ($time)",
							$rate
						);
						if ( $label_suffix ) {
							$rate->set_label( $rate->get_label() . $label_suffix );
						}
					}
				}
			}
		}
		return $rates;
	}

}
