<?php

namespace Shipping_Simulator\Admin;

use Shipping_Simulator\Helpers as h;

final class Notices {
	protected static $fields = null;

	public function __start () {
		add_action( 'admin_notices', [ $this, 'donation_notice' ] );
	}

	public function donation_notice () {
		if ( ! h::user_is_admin() ) return;

		global $pagenow;
		$in_plugins = 'plugins.php' === $pagenow;
		$in_settings = 'admin.php' === $pagenow && 'wc-settings' === h::get( $_GET['page'] ) && 'wc-shipping-simulator' === h::get( $_GET['section'] );

		if ( ! $in_plugins && ! $in_settings ) return;

		$cookie = 'wc_shipping_simulator_donation_notice_closed';
		if ( ! $in_settings && 'yes' === h::get( $_COOKIE[ $cookie ] ) ) return;

		$class = 'notice ' . ( $in_settings ? 'woocommerce-message updated' : 'notice-info is-dismissible' );

		echo h::get_template( 'notice-donation', [
			'class' => $class,
			'is_dismissible' => ! $in_settings,
			'cookie' => $cookie,
			'cookie_max_age' => 3 * MONTH_IN_SECONDS,
		] );
	}
}