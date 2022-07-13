<?php

namespace Shipping_Simulator\Core;

use Shipping_Simulator\Core\Config;

abstract class Main {
	protected static $classes_to_load = [];
	protected static $dependencies = [];

	// == CORE HELPERS ==
	public static function start_plugin ( $main_file ) {
		if ( ! file_exists( $main_file ) ) {
			throw new \Error( 'Invalid plugin main file path in ' . __CLASS__ );
		}

		Config::init( $main_file );
		Dependencies::init( $main_file );
		Loader::init( $main_file );
	}
}
