<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;

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
		$product = wc_get_product( $product_id );
		$variation_id = $variation_id ? absint( $variation_id ) : 0;
		$variation = [];
		$quantity = max( $quantity, 1 );
		$price = $product->get_price();
		$is_virtual = $product->is_virtual();

		if ( 'variable' === $product->get_type() ) {
			$product_variation = wc_get_product( $variation_id );

			h::throw_if(
				! $product_variation || ! $product_variation->variation_is_visible(),
				esc_attr__( 'Please select some product options before adding this product to your cart.', 'wc-shipping-simulator' )
			);

			$atts = $product_variation->get_attributes();

			foreach ( $atts as $key => $value ) {
				$variation[ 'attribute_' . $key ] = $value;
			}

			$price = $product_variation->get_price();
			$is_virtual = $product_variation->is_virtual();
		}

		h::throw_if(
			$is_virtual,
			esc_attr__( 'This product is virtual and can not shippable.', 'wc-shipping-simulator' )
		);

		$total = $price * $quantity;
		$this->contents[] = apply_filters(
			'wc_shipping_simulator_shipping_package_item',
			[
				'product_id' => $product_id,
				'variation_id' => $variation_id,
				'variation' => $variation,
				'quantity' => $quantity,
				'data' => $product,
				'line_tax' => 0,
				'line_tax_data' => [ 'subtotal' => [], 'total' => [] ],
				'line_subtotal' => $total,
				'line_subtotal_tax' => 0,
				'line_total' => $total,
			]
		);
	}

	public function get_package () {
		$contents_total = 0;

		foreach ( $this->contents as $item ) {
			$contents_total += h::get( $item['line_total'], 0 );
		}

		$package = apply_filters(
			'wc_shipping_simulator_shipping_package_data',
			[
				'contents' => $this->contents,
				'destination' => $this->destination,
				'applied_coupons' => [],
				'user' => [ 'ID' => get_current_user_id() ],
				'contents_cost' => $contents_total,
				'cart_subtotal' => $contents_total
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
		$result = $wc_shipping->calculate_shipping( [ $package ] );

		// restore the WC_Shipping->packages
		$wc_shipping->packages = $original_packages;

		$rates = h::get( $result[0]['rates'], [] );

		if ( count( $rates ) > 1 ) {
			uasort( $rates, function ( $a, $b ) {
				return $a->get_cost() <=> $b->get_cost();
			} );
		}

		$rates = apply_filters(
			'wc_shipping_simulator_shipping_package_rates',
			$rates,
			$package
		);

		return $rates;
	}
}
