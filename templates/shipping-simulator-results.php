<table>
	<?php foreach ( $rates as $rate ) : ?>
		<tr class="method-<?= esc_attr( $rate->get_method_id() ) ?>">
			<th class="col-label"><?= $rate->get_label(); ?></th>
			<td  class="col-cost"><?= wc_price( $rate->get_cost() ); ?></td>
		</tr>
	<?php endforeach; ?>
</table>
