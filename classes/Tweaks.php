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
		$tag = Shortcode::get_tag();
		$id = $product ? $product->get_id() : 0;
		echo do_shortcode( "[$tag product=\"$id\"]" );
	}

	public function results_after () {
		// TODO: get this text from a settings field
		$text = __( 'Delivery times start from the confirmation of payment.', 'wc-shipping-simulator' );
		if ( ! $text ) return;
		?>
		<div id="wc-shipping-sim-results-after"><?= h::safe_html( $text ) ?></div>
		<?php
	}
}
