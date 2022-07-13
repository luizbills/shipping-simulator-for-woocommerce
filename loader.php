<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;

defined( 'WPINC' ) || exit( 1 );

// register_activation_hook( h::config_get( 'FILE' ), function () {
// 	h::log( 'plugin activated' );
// } );

return [
	Settings::class,
	Integration\Brazil::class,
	Ajax::class,
	Shortcode::class,
];
