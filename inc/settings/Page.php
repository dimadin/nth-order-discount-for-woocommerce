<?php
/**
 * \dimadin\WP\Plugin\commonWP\WC\NthOrderDiscount\Settings\Page class.
 *
 * @package NthOrderDiscount
 * @since 1.0.0
 */

namespace dimadin\WP\Plugin\WC\NthOrderDiscount\Settings;

/**
 * Class for displaying admin settings.
 *
 * @since 1.0.0
 */
class Page {
	/**
	 * Get settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array $settings
	 */
	public static function get_settings() {
		$settings = [
			[
				'id'    => 'nth_order_discount_title',
				'title' => __( 'Automatic discount', 'nth-order-discount-for-woocommerce' ),
				'type'  => 'title',
			],
			[
				'id'    => 'nth_order_discount_after',
				'title' => __( 'Completed orders before discount', 'nth-order-discount-for-woocommerce' ),
				'desc'  => __( 'Apply automatic discount after how many completed orders', 'nth-order-discount-for-woocommerce' ),
				'type'  => 'number',
			],
			[
				'id'    => 'nth_order_discount_amount',
				'title' => __( 'Discount amount', 'nth-order-discount-for-woocommerce' ),
				'type'  => 'number',
			],
			[
				'id'      => 'nth_order_discount_discount_type',
				'title'   => __( 'Discount type', 'nth-order-discount-for-woocommerce' ),
				'type'    => 'select',
				'options' => wc_get_coupon_types(),
			],
			[
				'id'   => 'nth_order_discount_sectionend',
				'type' => 'sectionend',
			],
		];

		return $settings;
	}

	/**
	 * Add setting fields to existing settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings An array settings fields.
	 * @return array $settings
	 */
	public static function add_settings( $settings ) {
		$settings = array_merge( $settings, static::get_settings() );

		return $settings;
	}
}
