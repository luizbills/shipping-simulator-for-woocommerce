<?php

namespace Shipping_Simulator\Core\Traits;

use Shipping_Simulator\Core\Config;

trait Debug_Helpers {

	/**
	 * Dump and die
	 *
	 * @param mixed ...$values
	 * @return void|never
	 */
	public static function dd ( ...$values ) {
		if ( ! WP_DEBUG ) return;
		foreach ( $values as $v ) {
			echo '<pre>';
			var_dump( $v );
			echo '</pre>';
		}
		die;
	}
}
