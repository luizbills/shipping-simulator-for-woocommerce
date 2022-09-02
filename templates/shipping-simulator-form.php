<?php extract( $args ); ?>
<section id="wc-shipping-sim">
	<?php do_action( 'wc_shipping_simulator_wrapper_start' ) ?>

	<form id="wc-shipping-sim-form" action="<?php echo esc_url( $ajax_url ); ?>" data-ajax-action="<?php echo esc_attr( $ajax_action ) ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-product-type="<?php echo esc_attr( $product_type ); ?>">
		<?php do_action( 'wc_shipping_simulator_form_start' ) ?>

		<div id="wc-shipping-sim-form-fields">
			<?php do_action( 'wc_shipping_simulator_form_fields_start' ) ?>

			<input name="postcode" type="<?php echo esc_attr( $input_type ); ?>" value="<?php echo esc_attr( $input_value ) ?>" placeholder="<?php echo esc_attr( $input_placeholder ); ?>" aria-label="<?php echo esc_attr( $input_placeholder ); ?>" class="input-text input-postcode" required>

			<?php echo $nonce ?>

			<button type="submit" class="button submit" aria-label="<?php esc_attr_e( 'Calculate shipping', 'wc-shipping-simulator' ); ?>"><?php echo esc_html( $submit_label ); ?></button>

			<?php do_action( 'wc_shipping_simulator_form_fields_end' ) ?>
		</div>

		<?php do_action( 'wc_shipping_simulator_form_end' ) ?>
	</form>

	<?php do_action( 'wc_shipping_simulator_form_after' ) ?>

	<div id="wc-shipping-sim-results"></div>

	<?php do_action( 'wc_shipping_simulator_wrapper_end' ) ?>
</section>
