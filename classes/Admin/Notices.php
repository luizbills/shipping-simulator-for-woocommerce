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
		if ( $in_plugins || $in_settings ) {
			$class = 'notice ' . ( $in_settings ? 'woocommerce-message updated' : 'notice-info is-dismissible' );
			$cookie = 'wc_shipping_simulator_donation_notice_closed';
			echo h::get_template( 'notice-donation', [
				'class' => $class,
				'in_settings' => $in_settings,
				'cookie' => $cookie,
				'cookie_max_age' => 3 * MONTH_IN_SECONDS,
				'display' => $in_settings || 'yes' !== h::get( $_COOKIE[ $cookie ] ),
			] );
		}
	}
}