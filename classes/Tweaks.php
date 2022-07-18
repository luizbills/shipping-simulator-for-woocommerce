<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;
use Shipping_Simulator\Shortcode;
use Shipping_Simulator\Admin\Settings;

final class Tweaks {
	protected static $instace = null;

	public static function instance () {
		return self::$instace;
	}

	public function __start () {
		self::$instace = $this;
		add_action( 'woocommerce_single_product_summary', [ $this, 'auto_inser_shortcode' ], 35 );
		add_action( 'wc_shipping_simulator_form_start', [ $this, 'form_start' ] );
		add_action( 'wc_shipping_simulator_results_before', [ $this, 'results_before' ] );
		add_action( 'wc_shipping_simulator_results_after', [ $this, 'results_after' ] );
	}

	public function auto_inser_shortcode () {
		if ( 'yes' !== Settings::get_option( 'auto_insert' ) ) return;
		global $product;
		if ( $product ) {
			$id = $product->get_id();
			$tag = Shortcode::get_tag();
			echo do_shortcode( "[$tag]" );
		}
	}

	public function form_start () {
		$option = Settings::get_option( 'form_title' );
		$title = apply_filters(
			'wc_shipping_simulator_form_title',
			$option ? "<strong>$option</strong>" : ''
		);
		if ( $title ) {
			?>
			<div id="wc-shipping-sim-form-title"><?php echo h::safe_html( $title ) ?></div>
			<?php
		}
	}

	public function results_before ( $data ) {
		$title = apply_filters(
			'wc_shipping_simulator_results_title',
			__( 'Shipping options for', 'wc-shipping-simulator' )
		);
		if ( $title ) {
			$title .= ' ' . apply_filters(
				'wc_shipping_simulator_results_title_address',
				'<strong>' . $data['postcode'] . '</strong>',
				$data
			);
			?>
			<div id="wc-shipping-sim-results-title"><?php echo h::safe_html( $title ) ?></div>
			<?php
		}
	}

	public function results_after () {
		$option = Settings::get_option( 'after_results' );
		$text = apply_filters(
			'wc_shipping_simulator_text_after_results',
			$option
		);
		if ( $text ) {
			?>
			<div id="wc-shipping-sim-results-after"><?php echo h::safe_html( $text ) ?></div>
			<?php
		}
	}
}
