<?php

namespace Shipping_Simulator\Core\Traits;

use Shipping_Simulator\Core\Config;

trait Config_Helpers {

	/**
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 * @throws \Exception
	 */
	public static function config_get ( $key, $default = null ) {
		return Config::get( $key, $default );
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return mixed The value
	 * @throws \Exception
	 */
	public static function config_set ( $key, $value ) {
		return Config::set( $key, $value );
	}
}
