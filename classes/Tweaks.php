<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;
use Shipping_Simulator\Shortcode;

final class Tweaks {
	protected static $instace = null;

	public static function instance () {
		return self::$instace;
	}

	public function __start () {
		self::$instace = $this;
		add_action( 'wc_shipping_simulator_form_start', [ $this, 'form_start' ] );
		add_action( 'wc_shipping_simulator_results_after', [ $this, 'results_after' ] );
		add_action( 'woocommerce_single_product_summary', [ $this, 'include_shortcode' ], 35 );
	}

	public function form_start () {
		// TODO: get this text from a settings field
		$text = '<strong>' . __( 'Check shipping cost and delivery time:', 'wc-shipping-simulator' ) . '</strong>';
		if ( ! $text ) return;
		?>
		<div id="wc-shipping-sim-before"><?= h::safe_html( $text ) ?></div>
		<?php
	}

	public function include_shortcode () {
		global $product;
		if ( $product && $this->product_needs_shipping( $product ) ) {
			$id = $product->get_id();
			$tag = Shortcode::get_tag();
			echo do_shortcode( "[$tag product=\"$id\"]" );
		}
	}

	public function results_after () {
		// TODO: get this text from a settings field
		$text = __( 'Delivery times start from the confirmation of payment.', 'wc-shipping-simulator' );
		if ( ! $text ) return;
		?>
		<div id="wc-shipping-sim-results-after"><?= h::safe_html( $text ) ?></div>
		<?php
	}

	protected function product_needs_shipping ( $product ) {
		$result = false;
		$type = $product->get_type();
		if ( in_array( $type, [ 'simple', 'variation' ] ) ) {
			$result = $product->needs_shipping();
		}
		elseif ( 'variable' === $product->get_type() ) {
			$variations = $product->get_available_variations();
			foreach ( $variations as $variation ) {
				if ( ! $variation['is_virtual'] ) {
					$result = true;
					break;
				}
			}
		}
		return apply_filters( 'wc_shipping_simulator_product_needs_shipping', $result, $product );
	}
}
