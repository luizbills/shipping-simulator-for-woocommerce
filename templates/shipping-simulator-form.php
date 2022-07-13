<section id="wc-shipping-sim">
	<form id="wc-shipping-sim-form" action="<?= esc_url( $ajax_url ); ?>" data-ajax-action="<?= esc_attr( $ajax_action ) ?>" data-product-id="<?= esc_attr( $product_id ); ?>" data-product-type="<?= esc_attr( $product_type ); ?>">
		<?php do_action( 'wc_shipping_simulator_form_start' ) ?>

		<div id="wc-shipping-sim-form-fields">
			<input type="text" name="postcode" placeholder="Digite seu CEP" title="Digite seu CEP" class="input-text" data-mask="<?= esc_attr( $input_mask ); ?>" required>
			<button type="submit" class="button submit">Consultar</button>
			<?= $nonce ?>
		</div>

		<?php do_action( 'wc_shipping_simulator_form_end' ) ?>
	</form>

	<div id="wc-shipping-sim-results"></div>

</section>
