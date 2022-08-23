<?php

use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Brain\Monkey;
use Shipping_Simulator\Tweaks;

class Tweaks_Test extends TestCase {
	// Adds Mockery expectations to the PHPUnit assertions count.
	use MockeryPHPUnitIntegration;

	public function test_hooks () {
		$instance = new Tweaks();
		$instance->__start();

		self::assertNotFalse( has_action( 'woocommerce_single_product_summary' ) );
	}

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}
}