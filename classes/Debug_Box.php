<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;
use Shipping_Simulator\Admin\Settings;

final class Debug_Box {
	public function __start () {
		if ( Settings::debug_enabled() ) {
			add_action( 'wc_shipping_simulator_wrapper_end', [ $this, 'form_before' ] );
		}
	}

	public function form_before () {
		if ( ! current_user_can( 'manage_woocommerce' ) ) return;

		global $product;

		$yes = esc_html__( 'Yes', 'wc-shipping-simulator' );
		$no = esc_html__( 'No', 'wc-shipping-simulator' );

		$lines = [
			__( 'Product type:', 'wc-shipping-simulator' ) . ' ' . $product->get_type(),
			__( 'Product ID:', 'wc-shipping-simulator' ) . ' ' . '#' . $product->get_id(),
		];

		if ( $product->is_type( 'simple' ) ) {
			$lines = array_merge(
				$lines,
				[
					__( 'Has weight?', 'wc-shipping-simulator' ) . ' ' . ( $product->has_weight() ? $yes : $no ),
					__( 'Has dimensions?', 'wc-shipping-simulator' ) . ' ' . ( $product->has_dimensions() ? $yes : $no ),
				]
			);
		} elseif ( $product->is_type( 'variable' ) ) {
			$lines = array_merge(
				$lines,
				[
					__( 'Has weight?', 'wc-shipping-simulator' ) . ' ' . ( $product->get_weight() ? $yes : $no ),
					__( 'Has dimensions?', 'wc-shipping-simulator' ) . ' ' . ( $product->get_length() || $product->get_height() || $product->get_width() ? $yes : $no ),
				]
			);

			foreach ( $product->get_visible_children() as $id ) {
				$child = wc_get_product( $id );
				$lines = array_merge(
					$lines,
					[
						'<strong class="line-variation">' . __( 'VARIATION', 'wc-shipping-simulator' ) . " #$id</strong>",
						__( 'Attributes:', 'wc-shipping-simulator' )  . ' ' . esc_html( wp_json_encode( $child->get_attributes() ) ),
						__( 'Has weight?', 'wc-shipping-simulator' )  . ' ' . ( $child->has_weight() ? $yes : $no ),
						__( 'Has dimensions?', 'wc-shipping-simulator' ) . ' ' . ( $child->has_dimensions() ? $yes : $no ),
						__( 'Is virtual?', 'wc-shipping-simulator' ) . ' ' .  ( $child->is_virtual() ? $yes : $no ),
					]
				);
			}
		}

		$lines = apply_filters( 'wc_shipping_simulator_debug_box_lines', $lines, $product );

		?>
		<style>
			#wc-shipping-sim-debug-box {
				background-color: #fff4e6;
				padding: 1em;
			}

			#wc-shipping-sim-debug-box h4 {
				margin: 0!important;
			}
		</style>
		<div id="wc-shipping-sim-debug-box">
			<h4><?php esc_html_e( 'DEBUG MODE ENABLED', 'wc-shipping-simulator' ) ?></h4>

			<?php foreach ( $lines as $text ) : ?>
				<section><?php echo h::safe_html( $text ) ?></section>
			<?php endforeach ?>

			<section><em><?php esc_html_e( 'This box not appears for your customers.', 'wc-shipping-simulator' ) ?></em></section>
		</div>
		<?php
	}
}
