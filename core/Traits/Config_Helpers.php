<?php

namespace Shipping_Simulator\Core\Traits;

use Shipping_Simulator\Core\Config;

trait Config_Helpers {
	public static function config_get ( $key, $default = null ) {
		return Config::get( $key, $default );
	}

	public static function config_set ( $key, $value ) {
		return Config::set( $key, $value );
	}
}
