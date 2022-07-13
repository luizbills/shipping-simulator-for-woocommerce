<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;

final class Tweaks {
	public function __start () {
		add_action( 'wc_shipping_simulator_form_start', [ $this, 'form_start' ] );
	}

	public function form_start () {
		$text = '<strong>Consulte o frete e prazo de entrega:</strong>';
		if ( ! $text ) return;
		?>
		<div id="wc-shipping-sim-before"><?= h::safe_html( $text ) ?></div>
		<?php
	}
}
