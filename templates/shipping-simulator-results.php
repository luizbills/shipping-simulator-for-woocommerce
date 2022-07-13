<style>
	#wc-shipping-sim-results table {
		border-collapse: collapse;
	}
	#wc-shipping-sim-results tr {
		border: 1px solid #f0f0f0;
	}
	#wc-shipping-sim-results .col-cost {
		width: 30%;
		text-align: right;
	}
</style>
<table>
	<?php foreach ( $rates as $rate ) : ?>
		<tr class="method-<?= esc_attr( $rate->get_method_id() ) ?>">
			<th class="col-label"><?= $rate->get_label(); ?></th>
			<td  class="col-cost"><?= wc_price( $rate->get_cost() ); ?></td>
		</tr>
	<?php endforeach; ?>
</table>
