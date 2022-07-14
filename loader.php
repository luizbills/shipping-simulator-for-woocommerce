<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;

defined( 'WPINC' ) || exit( 1 );

// register_activation_hook( h::config_get( 'FILE' ), function () {
// 	h::log( 'plugin activated' );
// } );

return [
	Ajax::class,
	Shortcode::class,
	Tweaks::class,
	Integrations::class,
	Integration\Brazil::class,
	Integration\Correios::class,
];
