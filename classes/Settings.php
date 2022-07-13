<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;

final class Settings {
	public function __start () {
		add_action( 'init', [ $this, 'load_integrations' ] );
	}

	public function load_integrations () {
		do_action( 'wc_shipping_simulator_load_integrations' );
	}
}
