<?php
/**
 * \dimadin\WP\Plugin\commonWP\WC\NthOrderDiscount\Admin\Notices class.
 *
 * @package NthOrderDiscount
 * @since 1.0.0
 */

namespace dimadin\WP\Plugin\WC\NthOrderDiscount\Admin;

use WC_Admin_Notices;

/**
 * Class that adds notices to admin screens.
 *
 * @since 1.0.0
 */
class Notices {
	/**
	 * Add notice if requirements are not met.
	 *
	 * @since 1.0.0
	 */
	public static function add_requirements() {
		if ( ! defined( 'WC_VERSION' ) ) {
			add_action( 'admin_notices', [ __NAMESPACE__ . '\Notices', 'add_no_wc' ] );
		} else {
			add_action( 'admin_notices', [ __NAMESPACE__ . '\Notices', 'add_no_required_wc' ] );
		}
	}

	/**
	 * Add notice if WooCommerce is not active.
	 *
	 * @since 1.0.0
	 */
	public static function add_no_wc() {
		$message = __( 'Nth Order Discount for WooCommerce requires WooCommerce to be installed and active.', 'nth-order-discount-for-woocommerce' );

		echo '<div class="error"><p>' . $message . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Add notice if not running required version of WooCommerce.
	 *
	 * @since 1.0.0
	 */
	public static function add_no_required_wc() {
		/* translators: WooCommerce version */
		$message = sprintf( __( 'The minimum WooCommerce version required for Nth Order Discount for WooCommerce is %s.', 'nth-order-discount-for-woocommerce' ), '3.1' );

		echo '<div class="error"><p>' . $message . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
