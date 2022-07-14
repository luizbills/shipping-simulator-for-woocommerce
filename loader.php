<?php

namespace Shipping_Simulator;

use Shipping_Simulator\Helpers as h;

defined( 'WPINC' ) || exit( 1 );

return [
	Ajax::class,
	Shortcode::class,
	Tweaks::class,
	Integrations::class,
	Integration\Brazil::class,
	Integration\Correios::class,
	Integration\Free_Shipping::class,
];
