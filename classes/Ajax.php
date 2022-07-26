<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;
use Shipping_Simulator\Shipping_Package;
use Shipping_Simulator\Admin\Settings;

final class Ajax {
	public function __start () {
		$action = self::get_ajax_action();
		add_action( "wp_ajax_$action", [ $this, 'handle_request' ] );
		add_action( "wp_ajax_nopriv_$action", [ $this, 'handle_request' ] );
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
		$status = 200;

		if ( ! check_ajax_referer( self::get_nonce_action(), self::get_nonce_arg(), false ) ) {
			$response['success'] = false;
			$status = 403;
		} else {
			try {
				h::logger()->info( 'Request raw data: ' . wp_json_encode( $posted ) );

				$posted = $this->prepare_request_data( $posted );
				$this->validate_request_data( $posted );

				h::logger()->info( 'Request sanitized data: ' . wp_json_encode( $posted ) );

				$package = new Shipping_Package();
				$package = apply_filters( 'wc_shipping_simulator_request_update_package', $package, $posted );

				if ( ! $package->ready ) {
					$package->add_product( $posted['product'], $posted['quantity'], $posted['variation'] );
					$package->set_destination( [
						'postcode' => $posted['postcode']
					] );
				}

				$rates = $package->calculate_shipping();
				$response['results_html'] = apply_filters(
					'wc_shipping_simulator_request_results_html',
					h::get_template( 'shipping-simulator-results', [
						'rates' => $rates,
						'no_results_notice' => apply_filters(
							'wc_shipping_simulator_no_results_notice',
							Settings::get_option( 'no_results' )
						),
						'data' => $posted,
					] ),
					$rates,
					$posted
				);
			} catch ( Error $e ) {
				$response['success'] = false;
				$response['error'] = $e->getMessage();
				$status = 400;
			} catch ( \Throwable $e ) {
				throw $e;
			}
		}

		$status = apply_filters(
			'wc_shipping_simulator_request_response_status',
			$status,
			$posted,
			$response
		);

		$response = apply_filters(
			'wc_shipping_simulator_request_response',
			$response,
			$posted,
			$status
		);

		if ( ! Settings::debug_enabled() ) {
			$cache_max_age = (int) apply_filters(
				'wc_shipping_simulator_request_cache_max_age',
				600 // 10 minutes
			);
			if ( $cache_max_age > 0 ) {
				add_filter( 'nocache_headers', '__return_empty_array', 999 );
				header( "Cache-Control: max-age=$cache_max_age, must-revalidate" );
			}
		}

		wp_send_json( $response, $status );
	}

	protected function prepare_request_data ( $posted ) {
		$data = [
			'postcode' => h::sanitize_postcode( h::get( $posted['postcode'], '' ) ),
			'product' => absint( h::get( $posted['product'], 0 ) ),
			'variation' => absint( h::get( $posted['variation'], 0 ) ),
			'quantity' => absint( h::get( $posted['quantity'], 1 ) ),
		];
		return apply_filters(
			'wc_shipping_simulator_prepare_request_data',
			$data,
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
			$product = wc_get_product( $posted['variation'] ? $posted['variation'] : $posted['product'] );
			h::throw_if(
				! $product,
				esc_html__( 'Invalid product.', 'wc-shipping-simulator' )
			);
			h::throw_if(
				$posted['quantity'] < 1,
				esc_html__( 'The quantity must be greater than zero.', 'wc-shipping-simulator' )
			);
		}
	}
}
