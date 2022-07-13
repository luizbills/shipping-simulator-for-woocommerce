<?php

namespace Shipping_Simulator;

final class Integrations {
	public function __start () {
		add_action( 'init', [ $this, 'load_integrations' ] );
	}

	public function load_integrations () {
		do_action( 'wc_shipping_simulator_load_integrations' );
	}
}
