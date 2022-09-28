<?php

namespace Shipping_Simulator;

defined( 'WPINC' ) || exit( 1 );

return [
	Ajax::class,
	Shortcode::class,
	Tweaks::class,
	Logger::class,
	Integrations::class,
	Debug_Box::class,
	Integration\Brazil::class,
	Integration\Correios::class,
	Integration\Free_Shipping::class,
	Integration\Melhor_Envio::instance(),
	Admin\Settings::class,
	Admin\Plugin_Meta::class,
	Admin\Notices::class,
];
