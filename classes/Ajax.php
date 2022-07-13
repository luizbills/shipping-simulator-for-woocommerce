<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;
use Shipping_Simulator\Shipping_Package;

final class Ajax {
	public function __start () {
		$action = self::get_ajax_action();
		add_action( "wp_ajax_$action", [ $this, 'handle_request' ] );
		add_action( "wp_ajax_nopriv_$action", [ $this, 'handle_request' ] );

		// add_action( 'wp_head', function () { h::dd( WC()->cart ); });
	}

	public static function get_ajax_action () {
		return 'wc_shipping_simulator';
	}

	public static function get_nonce_action () {
		return 'wc_shipping_simulator';
	}

	public static function get_nonce_arg () {
		return 'nonce';
	}

	public static function get_nonce_field ( $referer = false, $echo = false ) {
		return wp_nonce_field( self::get_nonce_action(), self::get_nonce_arg(), $referer, $echo );
	}

	public function handle_request ( $posted = null ) {
		$response = [ 'success' => true ];
		$posted = $posted ? $posted : $_GET;

		if ( ! check_ajax_referer( self::get_nonce_action(), self::get_nonce_arg(), false ) ) {
			$response['success'] = false;
			wp_send_json( $response, 403 );
		} else {
			try {
				do_action( 'wc_shipping_simulator_load_integrations' );

				$posted = $this->sanitize_request_data( $posted );
				$this->validate_request_data( $posted );

				$package = new Shipping_Package();
				$package = apply_filters( 'wc_shipping_simulator_request_update_package', $package, $posted );

				if ( ! $package->ready ) {
					$package->add_product( $posted['product'], $posted['quantity'] );
					$package->set_destination( [
						'postcode' => $posted['postcode']
					] );
				}

				$rates = $package->calculate_shipping();
				$response['results_html'] = apply_filters(
					'wc_shipping_simulator_request_results_html',
					h::get_template( 'shipping-simulator-results', [
						'rates' => $rates,
						'no_rates_notice' => 'No momento este produto não pode ser entregue na região informada.'
					] ),
					$rates,
					$posted
				);
			} catch ( Error $e ) {
				$response['success'] = false;
				$response['error'] = $e->getMessage();
			} catch ( \Throwable $e ) {
				$response['success'] = false;
				$response['error'] = esc_html__( 'Something went wrong. Please try again.' );
				h::log( __METHOD__ . ' error: ' . $e->getMessage() );
			}
		}

		wp_send_json(
			apply_filters(
				'wc_shipping_simulator_request_response',
				$response,
				$posted
			)
		);
	}

	protected function sanitize_request_data ( $posted ) {
		$sanitized = [
			'postcode' => preg_replace( '/[^0-9a-z]/i', '', h::get( $posted['postcode'] ) ),
			'product' => absint( h::get( $posted['product'] ) ),
			'quantity' => absint( h::get( $posted['quantity'] ) ),
		];
		return apply_filters(
			'wc_shipping_simulator_sanitize_request_data',
			$sanitized,
			$posted
		);
	}

	protected function validate_request_data ( $posted ) {
		do_action( 'wc_shipping_simulator_validate_request_data', $posted );

		if ( apply_filters( 'wc_shipping_simulator_use_default_validations', true, $posted ) ) {
			h::throw_if(
				! $posted['postcode'],
				esc_html__( 'The postcode is required.', 'wc-shipping-simulator' )
			);
			h::throw_if(
				0 === $posted['product'],
				esc_html__( 'Invalid product ID.', 'wc-shipping-simulator' )
			);
			h::throw_if(
				$posted['quantity'] < 1,
				esc_html__( 'The quantity must be greater than zero.', 'wc-shipping-simulator' )
			);
		}
	}
}
