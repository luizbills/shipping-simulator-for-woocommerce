<section id="wc-shipping-sim">
	<form id="wc-shipping-sim-form" action="<?= esc_url( $ajax_url ); ?>" data-ajax-action="<?= esc_attr( $ajax_action ) ?>" data-product-id="<?= esc_attr( $product_id ); ?>" data-product-type="<?= esc_attr( $product_type ); ?>">
		<?php do_action( 'wc_shipping_simulator_form_start' ) ?>

		<div id="wc-shipping-sim-form-fields">
			<?php do_action( 'wc_shipping_simulator_form_fields_start' ) ?>

			<input name="postcode" type="<?= esc_attr( $input_type ); ?>" value="<?= esc_attr( $input_value ) ?>" placeholder="<?= esc_attr( $input_placeholder ); ?>" title="<?= esc_attr( $input_placeholder ); ?>" class="input-text input-postcode" data-mask="<?= esc_attr( $input_mask ); ?>" required maxlength="20">

			<?= $nonce ?>

			<button type="submit" class="button submit"><?= esc_html( $submit_label ); ?></button>

			<?php do_action( 'wc_shipping_simulator_form_fields_end' ) ?>
		</div>

		<?php do_action( 'wc_shipping_simulator_form_end' ) ?>
	</form>

	<div id="wc-shipping-sim-results"></div>
</section>
