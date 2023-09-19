<?php

use Shipping_Simulator\Shortcode;
use Shipping_Simulator\Admin\Settings;

$prefix = Settings::get_prefix();
$shortcode = Shortcode::get_tag();

return [
	[
		'id' => $prefix . 'settings',
		'type' => 'title',
		'name' => esc_html__( 'Shipping Simulator Settings', 'wc-shipping-simulator' ),
		'desc' => esc_html__( 'The following options are used to configure the Shipping Simulator.', 'wc-shipping-simulator' ),
	],
	[
		'id'       => $prefix . 'auto_insert',
		'type'     => 'checkbox',
		'name'     => esc_html__( 'Enable auto-insert', 'wc-shipping-simulator' ),
		'desc'     => esc_html__( 'Enable', 'wc-shipping-simulator' ),
		'desc_tip' => sprintf(
			// translators: %s is a shortcode tag
			esc_html__( 'Display automatically the shipping simulator in product pages. Alternatively you can manually insert the shipping simulator using the %s shortcode.', 'wc-shipping-simulator' ),
			"<code>[$shortcode]</code>"
		),
		'default'  => 'yes'
	],
	[
		'id'       => $prefix . 'requires_variation',
		'type'     => 'checkbox',
		'name'     => esc_html__( 'Product variation is required', 'wc-shipping-simulator' ),
		'desc'     => esc_html__( 'Enable', 'wc-shipping-simulator' ),
		'desc_tip' => esc_html__( 'Disable this option to allow customers simulate shipping rates even when a variation is not selected on variable products. However, always make sure that the variable product has a defined weight.', 'wc-shipping-simulator' ),
		'default'  => 'yes'
	],
	[
		'id'       => $prefix . 'autofill_addresses',
		'type'     => 'checkbox',
		'name'     => esc_html__( 'Display full address', 'wc-shipping-simulator' ),
		'desc'     => esc_html__( 'Enable', 'wc-shipping-simulator' ),
		'desc_tip' => esc_html__( 'When this option is activated, the street, neighborhood and city will be displayed in the shipping simulator.', 'wc-shipping-simulator' ),
		'default'  => 'yes'
	],
	[
		'id'       => $prefix . 'update_address',
		'type'     => 'radio',
		'name'     => esc_html__( 'Update customer address', 'wc-shipping-simulator' ),
		'options'  => [
			'0' => esc_html__( "Don't update", 'wc-shipping-simulator' ),
			'1' => esc_html__( "Update only shipping address", 'wc-shipping-simulator' ),
			'2' => esc_html__( "Update billing and shipping address", 'wc-shipping-simulator' ),
		],
		'desc_tip' => esc_html__( 'The customer address can be updated when a shipping simulation returns shipping options.', 'wc-shipping-simulator' ),
		'default'  => '0'
	],
	[
		'id'       => $prefix . 'form_title',
		'type'     => 'text',
		'name'     => esc_html__( 'Title', 'wc-shipping-simulator' ),
		'desc'     => esc_html__( 'Text that appears before the simulator fields.', 'wc-shipping-simulator' ),
		'default'  => __( 'Check shipping cost and delivery time:', 'wc-shipping-simulator' ),
	],
	[
		'id'       => $prefix . 'input_placeholder',
		'type'     => 'text',
		'name'     => esc_html__( 'Input placeholder', 'wc-shipping-simulator' ),
		'desc'     => esc_html__( 'Text that appears when the postcode field is empty.', 'wc-shipping-simulator' ),
		'default'  => __( 'Type your postcode', 'wc-shipping-simulator' ),
	],
	[
		'id'       => $prefix . 'submit_label',
		'type'     => 'text',
		'name'     => esc_html__( 'Button Text', 'wc-shipping-simulator' ),
		'desc'     => esc_html__( 'Text that appears on the shipping simulator button.', 'wc-shipping-simulator' ),
		'default'  => __( 'Apply', 'wc-shipping-simulator' ),
	],
	[
		'id'       => $prefix . 'after_results',
		'type'     => 'textarea',
		'name'     => esc_html__( 'Text after results.', 'wc-shipping-simulator' ),
		'default'  => __( 'Delivery times start from the confirmation of payment.', 'wc-shipping-simulator' ),
		'css' => 'height: 6rem',
	],
	[
		'id'       => $prefix . 'no_results',
		'type'     => 'textarea',
		'name'     => esc_html__( 'Text when there are no results.', 'wc-shipping-simulator' ),
		'default'  => __( 'Unfortunately at this moment this product cannot be delivered to the specified region.', 'wc-shipping-simulator' ),
		'css' => 'height: 6rem;',
	],
	[
		'id' => $prefix . 'settings',
		'type' => 'sectionend',
	],
	[
		'id' => $prefix . 'settings_debug',
		'type' => 'title',
		'name' => esc_html__( 'Debug', 'wc-shipping-simulator' ),
	],
	[
		'id'       => $prefix . 'debug_mode',
		'type'     => 'checkbox',
		'name'     => esc_html__( 'Debug mode', 'wc-shipping-simulator' ),
		'desc'     => esc_html__( 'Enable', 'wc-shipping-simulator' ),
		'desc_tip' => __( 'Enable debug mode to log your shipping simulations and display helpful informations in product page.', 'wc-shipping-simulator' ),
		'default'  => 'no'
	],
	[
		'id' => $prefix . 'settings',
		'type' => 'sectionend',
	],
];
