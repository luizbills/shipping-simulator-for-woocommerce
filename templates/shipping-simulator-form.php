<section id="wc-shipping-sim">
	<form id="wc-shipping-sim-form" action="<?= esc_url( $ajax_url ); ?>" data-ajax-action="<?= esc_attr( $ajax_action ) ?>" data-product-id="<?= esc_attr( $product_id ); ?>" data-product-type="<?= esc_attr( $product_type ); ?>">
		<div id="wc-shipping-sim-before">
			<strong>Consulte o frete e prazo de entrega:</strong>
		</div>
		<div>
			<input type="text" name="postcode" placeholder="Digite seu CEP" title="Digite seu CEP" class="input-text" data-mask="<?= esc_attr( $input_mask ); ?>" required>
			<button type="submit" class="button submit">Consultar</button>
			<?= $nonce ?>
		</div>
		<div id="wc-shipping-sim-after">
			<a href="https://buscacepinter.correios.com.br/app/endereco/index.php">Não sei meu cep</a>
		</div>
	</form>

	<div id="wc-shipping-sim-results"></div>

</section>
