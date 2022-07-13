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

	public function add_product ( $product_id, $quantity ) {
		$product = wc_get_product( $product_id );
		$variation_id = null;
		$variation = [];
		$quantity = max( $quantity, 1 );

		if ( in_array( $product->get_type(), [ 'variable', 'variation' ] ) ) {
			// TODO: support to variable products
		}

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
				'line_subtotal' => $product->get_price() * $quantity,
				'line_subtotal_tax' => 0,
				'line_total' => $product->get_price() * $quantity,
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
		$result = $wc_shipping->calculate_shipping( [ $this->get_package() ] );

		// restore the WC_Shipping->packages
		$wc_shipping->packages = $original_packages;

		$rates = apply_filters(
			'wc_shipping_simulator_shipping_package_rates',
			h::get( $result[0]['rates'], [] )
		);

		return $rates;
	}
}
