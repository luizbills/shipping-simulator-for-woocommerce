<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;

defined( 'WPINC' ) || exit( 1 );

// register_activation_hook( h::config_get( 'FILE' ), function () {
// 	h::log( 'plugin activated' );
// } );

return [
	[ Integration\Brazil::class, 10 ], // 10 is priority
	[ Ajax::class, 10 ], // 10 is priority
	[ Shortcode::class, 10 ], // 10 is priority
];
