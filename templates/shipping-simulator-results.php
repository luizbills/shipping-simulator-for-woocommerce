<?php do_action( 'wc_shipping_simulator_results_before' ) ?>

<table>
	<?php foreach ( $rates as $rate ) : ?>
		<tr class="shipping-rate-method-<?= esc_attr( $rate->get_method_id() ) ?>">
			<th class="col-label">
				<span class="shipping-rate-label"><?= esc_html( $rate->get_label() ); ?></span>
				<?php do_action( 'wc_shipping_simulator_results_col_label', $rate ) ?>
			</th>
			<td  class="col-cost">
				<?= wc_price( $rate->get_cost() ); ?>
				<?php do_action( 'wc_shipping_simulator_results_col_cost', $rate ) ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>

<?php do_action( 'wc_shipping_simulator_results_after' ) ?>
