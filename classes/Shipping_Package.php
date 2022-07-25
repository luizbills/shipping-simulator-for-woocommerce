<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;
use Shipping_Simulator\Admin\Settings;

final class Shipping_Package {
	public $ready = false;

	protected $contents;
	protected $destination;

	public function __construct () {
		$this->contents = [];
		$this->destination = apply_filters(
			'wc_shipping_simulator_destination_fields',
			[
				'postcode' => '',
				'country' => '',
				'state' => '',
				'city' => '',
				'address' => '',
				'address_1' => '',
				'address_2' => '',
			]
		);
	}

	public function set_destination ( $args ) {
		foreach ( $args as $key => $value ) {
			if ( isset( $this->destination[ $key ] ) ) {
				$this->destination[ $key ] = $value;
			}
		}
	}

	public function add_product ( $product_id, $quantity, $variation_id = null ) {
		$product_id = absint( $product_id );
		$variation_id = $variation_id ? absint( $variation_id ) : 0;
		$variation = $variation_id ? wc_get_product_variation_attributes( $variation_id ) : [];
		$quantity = max( $quantity, 1 );
		$product = wc_get_product( $variation_id ? $variation_id : $product_id );
		$price = $product->get_price();
		$is_virtual = $product->is_virtual();

		if ( apply_filters( 'wc_shipping_simulator_package_validate_virtual_product', true ) ) {
			h::throw_if(
				$is_virtual,
				esc_attr__( 'This product is virtual and can not shippable.', 'wc-shipping-simulator' )
			);
		}

		h::throw_if(
			'yes' === Settings::get_option( 'requires_variation' ) && 0 === $variation_id && $product->is_type( 'variable' ),
			esc_attr__( 'Please select some product options first.', 'wc-shipping-simulator' )
		);

		$price_total = $price * $quantity;
		$this->contents[] = apply_filters(
			'wc_shipping_simulator_package_item',
			[
				'product_id' => $product_id,
				'variation_id' => $variation_id,
				'variation' => $variation,
				'quantity' => $quantity,
				'data' => $product,
				'line_tax' => 0,
				'line_tax_data' => [ 'subtotal' => [], 'total' => [] ],
				'line_subtotal' => $price_total,
				'line_subtotal_tax' => 0,
				'line_total' => $price_total,
			]
		);

		return true;
	}

	public function get_package () {
		$contents_total = 0;
		$has_variations = false;

		foreach ( $this->contents as $item ) {
			$contents_total += h::get( $item['line_total'], 0 );
			if ( ! $has_variations ) {
				$has_variations = 0 !== $item['variation_id'];
			}
		}

		$package = apply_filters(
			'wc_shipping_simulator_package_data',
			[
				'contents' => $this->contents,
				'destination' => $this->destination,
				'applied_coupons' => [],
				'user' => [ 'ID' => get_current_user_id() ],
				'contents_cost' => $contents_total,
				'cart_subtotal' => $contents_total,
				'has_variations' => $has_variations
			]
		);
		$package['DOING_SHIPPING_SIMULATION'] = true;

		return $package;
	}

	public function calculate_shipping () {
		$wc_shipping = \WC_Shipping::instance();

		// save the current WC_Shipping->packages
		$original_packages = $wc_shipping->packages;

		// calculate
		$package = $this->get_package();
		h::logger()->info( 'Calculating shipping rates for ...' );
		$this->log_package( $package );
		$result = $wc_shipping->calculate_shipping( [ $package ] );

		// restore the WC_Shipping->packages
		$wc_shipping->packages = $original_packages;

		$rates = h::get( $result[0]['rates'], [] );
		h::logger()->info( 'Result: ' . wp_json_encode( array_keys( $rates ) ) );

		if ( count( $rates ) > 1 ) {
			uasort( $rates, function ( $a, $b ) {
				return $a->get_cost() <=> $b->get_cost();
			} );
		}

		$rates = apply_filters(
			'wc_shipping_simulator_package_rates',
			$rates,
			$package
		);

		h::throw_if(
			$package['has_variations'] && 0 === count( $rates ),
			esc_attr__( 'Please select some product options before adding this product to your cart.', 'wc-shipping-simulator' )
		);

		return $rates;
	}

	protected function log_package ( $package ) {
		if ( Settings::debug_enabled() ) {
			$i = 0;
			foreach ( $package['contents'] as $item ) {
				$i++;
				$item_data = [
					'name' => $item['data']->get_name(),
					'product_id' => $item['product_id'],
					'quantity' => $item['quantity'],
					'weight' => $item['data']->get_weight(),
					'variation_id' => $item['variation_id'],
					'variation' => $item['variation'],
				];
				h::logger()->info( "Package item #$i: " . wp_json_encode( $item_data ) );
			}
			h::logger()->info( 'Package destination: ' . wp_json_encode( $package['destination'] ) );
			h::logger()->info( 'Package total: ' . wp_json_encode( $package['contents_cost'] ) );
		}
	}
}
