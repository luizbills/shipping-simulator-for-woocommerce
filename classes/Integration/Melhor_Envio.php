<?php

namespace Shipping_Simulator\Integration;

final class Melhor_Envio {
	protected static $instance = null;

	public static function instance () {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __start () {
		add_action( 'wc_shipping_simulator_load_integrations', [ $this, 'add_hooks' ], 10 );
	}

	public function is_enabled () {
		return apply_filters(
			'wc_shipping_simulator_integration_melhor_envio_enabled',
			is_plugin_active( 'melhor-envio-cotacao/melhor-envio-beta.php' )
		);
	}

	public function add_hooks () {
		if ( $this->is_enabled() ) {
			add_filter( 'pre_option_melhor_envio_option_where_show_calculator', [ $this, 'return_none_string' ] );
		}
	}

	public function return_none_string () {
		return 'none';
	}
}
