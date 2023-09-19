<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;
use Shipping_Simulator\Admin\Settings;

class Logger {
	public function __start () {
		add_filter( h::prefix( 'get_logger' ), [ $this, 'get_logger' ] );
	}

	public function get_logger () {
		return $this;
	}

	public function debug ( $message ) {
		if ( ! Settings::debug_enabled() ) return;
		wc_get_logger()->debug( $message, [ 'source' => h::config_get( 'SLUG' ) ] );
	}

	public function info ( $message ) {
		if ( ! Settings::debug_enabled() ) return;
		wc_get_logger()->info( $message, [ 'source' => h::config_get( 'SLUG' ) ] );
	}

	public function error ( $message ) {
		if ( ! Settings::debug_enabled() ) return;
		wc_get_logger()->error( $message, [ 'source' => h::config_get( 'SLUG' ) ] );
	}
}
