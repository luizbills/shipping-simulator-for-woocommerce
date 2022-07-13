<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;
use Shipping_Simulator\Ajax;

final class Shortcode {
	public function __start () {
		add_shortcode( self::get_tag(), [ $this, 'render_shortcode' ] );
	}

	public static function get_tag () {
		return 'wc_shipping_simulator';
	}

	public function render_shortcode ( $atts ) {
		$atts = shortcode_atts( [
			'product' => 0,
		], $atts, self::get_tag() );

		$this->enqueue_scripts();

		$product = wc_get_product( $atts['product'] );

		return h::get_template( 'shipping-simulator-form', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'ajax_action' => Ajax::get_ajax_action(),
			'nonce' => Ajax::get_nonce_field(),
			'product_type' => $product->get_type(),
			'product_id' => $product->get_id(),

			// customizable template variables
			'input_mask' => apply_filters(
				'wc_shipping_simulator_form_input_mask',
				'' // no input mask by default
			),
			'input_placeholder' => apply_filters(
				'wc_shipping_simulator_form_input_placeholder',
				__( 'Type your postcode', 'wc-shipping-simulator' )
			),
			'input_type' => apply_filters(
				'wc_shipping_simulator_form_input_type',
				'tel'
			),
			'submit_label' => apply_filters(
				'wc_shipping_simulator_form_submit_label',
				'Consultar'
			),
		] );
	}

	protected function enqueue_scripts () {
		wp_enqueue_script(
			h::prefix( 'form' ),
			h::plugin_url( 'assets/js/form.js' ),
			[],
			h::config_get( 'VERSION' ),
			true
		);
		wp_enqueue_style(
			h::prefix( 'form' ),
			h::plugin_url( 'assets/css/form.css' ),
			[],
			h::config_get( 'VERSION' )
		);
	}
}
