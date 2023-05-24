<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;
use Shipping_Simulator\Request;
use Shipping_Simulator\Admin\Settings;

final class Shortcode {
	public function __start () {
		add_action( 'wc_shipping_simulator_form_after', [ $this, 'display_results_wrapper' ] );
		add_shortcode( self::get_tag(), [ $this, 'render_shortcode' ] );
	}

	public function display_results_wrapper () {
		$wrapper = h::get_template( 'shipping-simulator-results-wrapper' );
		echo apply_filters( 'wc_shipping_simulator_results_wrapper', $wrapper );
	}

	public static function get_tag () {
		return 'wc_shipping_simulator';
	}

	public function render_shortcode ( $atts ) {
		$atts = shortcode_atts( [
			'product' => 0,
		], $atts, self::get_tag() );

		$atts['product'] = absint( $atts['product'] );
		$product = null;
		if ( $atts['product'] !== 0 ) {
			$product = wc_get_product( $atts['product'] );
		} else {
			$product = $GLOBALS['product'] ?? null;
		}

		if ( is_object( $product ) && h::product_needs_shipping( $product ) ) {
			do_action( 'wc_shipping_simulator_shortcode_included', $atts );

			$this->enqueue_scripts();

			return h::get_template( 'shipping-simulator-form', [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_action' => Request::get_ajax_action(),
				'product_type' => $product->get_type(),
				'product_id' => $product->get_id(),

				// customizable template variables
				'input_placeholder' => apply_filters(
					'wc_shipping_simulator_form_input_placeholder',
					Settings::get_option( 'input_placeholder' )
				),
				'input_type' => apply_filters(
					'wc_shipping_simulator_form_input_type',
					'tel'
				),
				'input_value' => apply_filters(
					'wc_shipping_simulator_form_input_value',
					$this->get_customer_postcode()
				),
				'submit_label' => apply_filters(
					'wc_shipping_simulator_form_submit_label',
					Settings::get_option( 'submit_label' )
				),
			] );
		}
		return '';
	}

	protected function enqueue_scripts () {
		$suffix = h::get_defined( 'SCRIPT_DEBUG' ) ? '' : '.min';
		$plugin_version = h::config_get( 'VERSION' );
		wp_enqueue_script(
			h::prefix( 'form' ),
			h::plugin_url( "assets/js/form$suffix.js" ),
			[],
			$plugin_version,
			true
		);

		$params = apply_filters( 'wc_shipping_simulator_form_js_params', [
			'requires_variation' => 'yes' === Settings::get_option( 'requires_variation' ),
			'auto_submit' => true,
			'timeout' => 60000, // 1 minute in milliseconds
			'ajax_url' => \admin_url( '/admin-ajax.php' ),
			'errors' => [
				'timeout' => esc_html__( 'The server took too long to respond. Please try again.', 'wc-shipping-simulator' ),
				'unexpected' => esc_html__( 'An unexpected error occurred. Please refresh the page and try again.', 'wc-shipping-simulator' ),
			],
			'postcode_mask' => apply_filters(
				'wc_shipping_simulator_form_input_mask',
				'' // no input mask by default
			)
		] );
		wp_localize_script(
			h::prefix( 'form' ),
			'wc_shipping_simulator_params',
			$params
		);

		wp_enqueue_style(
			h::prefix( 'form' ),
			h::plugin_url( "assets/css/form$suffix.css" ),
			[],
			$plugin_version
		);

		do_action( 'wc_shipping_simulator_shortcode_enqueue_scripts' );
	}

	protected function get_customer_postcode () {
		$billing_postcode = WC()->customer->get_billing_postcode();
		$postcode = $billing_postcode ? $billing_postcode : WC()->customer->get_shipping_postcode();
		return h::sanitize_postcode( $postcode );
	}
}
