<?php

namespace Shipping_Simulator;

defined( 'WPINC' ) || exit( 1 );

return [
	Admin\Settings::class,
	Admin\Plugin_Meta::class,
	Admin\Notices::class,
	Integration\Brazil::instance(),
	Integration\Correios::instance(),
	Integration\Free_Shipping::instance(),
	Integration\Melhor_Envio::instance(),
	Request::class,
	Shortcode::class,
	Tweaks::class,
	Logger::class,
	Integrations::class,
	Debug_Box::class,
];
