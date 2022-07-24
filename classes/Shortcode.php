<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;
use Shipping_Simulator\Ajax;
use Shipping_Simulator\Admin\Settings;

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

		$atts['product'] = absint( $atts['product'] );
		$prod = null;
		if ( 0 === $atts['product'] ) {
			global $product;
			$prod = $product;
		} else {
			$prod = wc_get_product( $atts['product'] );
		}

		if ( $prod && $this->product_needs_shipping( $prod ) ) {
			$this->enqueue_scripts();
			return h::get_template( 'shipping-simulator-form', [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_action' => Ajax::get_ajax_action(),
				'nonce' => Ajax::get_nonce_field(),
				'product_type' => $prod->get_type(),
				'product_id' => $prod->get_id(),

				// customizable template variables
				'input_mask' => apply_filters(
					'wc_shipping_simulator_form_input_mask',
					'' // no input mask by default
				),
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
		wp_localize_script(
			h::prefix( 'form' ),
			'wc_shipping_simulator_params',
			apply_filters( 'wc_shipping_simulator_form_js_params', [
				'requires_variation' => 'yes' === Settings::get_option( 'requires_variation' ),
				'auto_submit' => true,
			] )
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
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$billing_postcode = get_user_meta( $user_id, 'billing_postcode', true );
			$postcode = $billing_postcode ? $billing_postcode : get_user_meta( $user_id, 'shipping_postcode', true );
			return h::sanitize_postcode( $postcode );
		}
		return '';
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
