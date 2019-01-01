<?php
/**
 * \dimadin\WP\Plugin\commonWP\WC\NthOrderDiscount\Coupon class.
 *
 * @package NthOrderDiscount
 * @since 1.0.0
 */

namespace dimadin\WP\Plugin\WC\NthOrderDiscount;

use dimadin\WP\Plugin\WC\NthOrderDiscount\Settings\Get;
use WC_Coupon;
use Exception;

/**
 * Class that adds or removes automatic discount.
 *
 * @since 1.0.0
 */
class Coupon {
	/**
	 * Add discount to the cart if it doesn't exist or not previously removed.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Cart $cart WooCommerce cart object.
	 */
	public static function maybe_add_discount( $cart ) {
		// Check if user is logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Check if cart already used but removed discount.
		if ( WC()->session->get( 'nth_order_coupon_removed' ) ) {
			return;
		}

		// Check if cart already has discount.
		if ( static::cart_used_discount( $cart ) ) {
			return;
		}

		// Check if there are items in cart.
		if ( $cart->is_empty() ) {
			return;
		}

		// Check if recent orders used coupon.
		if ( static::recent_orders_used_discount( $cart ) ) {
			return;
		}

		// Add discount to cart.
		static::add_discount( $cart );
	}

	/**
	 * Check if any of recent orders by customer used automatic discount.
	 *
	 * @todo Use actual customer, regardless if it's registered or not
	 * @todo Use order date of the last order with discount when checking incomplete orders.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Cart $cart WooCommerce cart object.
	 * @return bool Whether recent orders of a customer used automatic discount.
	 */
	public static function recent_orders_used_discount( $cart ) {
		$required_completed = absint( Get::after() );

		// Get recent completed orders of customer.
		$orders = wc_get_orders(
			[
				'customer' => get_current_user_id(),
				'status'   => 'completed',
				'limit'    => $required_completed,
			]
		);

		// Is number of recent orders same as requested.
		if ( count( $orders ) !== $required_completed ) {
			return true;
		}

		// Is discount used in any of recent orders.
		foreach ( $orders as $order ) {
			if ( static::order_used_discount( $order ) ) {
				return true;
			}
		}

		// Get incomplete orders of customer since last order.
		$orders = wc_get_orders(
			[
				'customer'     => get_current_user_id(),
				'status'       => [
					'on-hold',
					'pending',
					'processing',
				],
				'date_created' => '>=',
			]
		);

		// Is discount used in any of recent incomplete orders.
		foreach ( $orders as $order ) {
			if ( static::order_used_discount( $order ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if order used automatic discount.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Order $order WooCommerce order object.
	 * @return bool Whether order used automatic discount.
	 */
	public static function order_used_discount( $order ) {
		return static::coupons_have_discount( $order->get_used_coupons() );
	}

	/**
	 * Check if cart used automatic discount.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Cart $cart WooCommerce cart object.
	 * @return bool Whether cart used automatic discount.
	 */
	public static function cart_used_discount( $cart ) {
		return static::coupons_have_discount( $cart->get_applied_coupons() );
	}

	/**
	 * Check if any coupon is discount.
	 *
	 * @since 1.0.0
	 *
	 * @param array $coupon_codes An array of coupon codes.
	 * @return bool Whether any of coupon is automatic discount.
	 */
	public static function coupons_have_discount( $coupon_codes ) {
		foreach ( $coupon_codes as $coupon_code ) {
			// Discount was used.
			if ( static::is_coupon_for_discount( $coupon_code ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if coupon is automatic discount.
	 *
	 * @since 1.0.0
	 *
	 * @param string $coupon_code Code of coupon.
	 * @return bool Whether coupon is automatic discount.
	 */
	public static function is_coupon_for_discount( $coupon_code ) {
		$coupon = new WC_Coupon( $coupon_code );

		if ( $coupon->get_meta( 'for_nth_order' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add automatic discount to the cart.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Cart $cart WooCommerce cart object.
	 */
	public static function add_discount( $cart ) {
		// Add filter to add discount no matter if coupons are allowed.
		add_filter( 'woocommerce_coupons_enabled', '__return_true', 975 );

		try {
			$coupon = static::create_coupon( $cart );
			$cart->add_discount( $coupon->get_code() );
		} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Do not do anything, for now at least.
		}

		remove_filter( 'woocommerce_coupons_enabled', '__return_true', 975 );
	}

	/**
	 * Create coupon for discount.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If coupon can't be added.
	 *
	 * @param \WC_Cart $cart WooCommerce cart object.
	 * @return \WC_Coupon $coupon WooCommerce coupon object.
	 */
	public static function create_coupon( $cart ) {
		// Get settings.
		$completed_number = Get::after();
		$discount_type    = Get::discount_type();
		$amount           = Get::amount();

		// Check that all settings are set.
		if ( ! $completed_number || ! $discount_type || ! $amount ) {
			throw new Exception( __( 'Cannot add discount.', 'nth-order-discount-for-woocommerce' ) );
		}

		if ( 1 === (int) $completed_number ) {
			/* translators: 2. current date */
			$string = __( 'Discount applied to every second order. (%2$s)', 'nth-order-discount-for-woocommerce' );
		} else {
			/* translators: 1. number of orders (always plural) 2. current date */
			$string = _n(
				'Discount applied to order after every %1$d completed orders. (%2$s)',
				'Discount applied to order after every %1$d completed orders. (%2$s)',
				$completed_number,
				'nth-order-discount-for-woocommerce'
			);
		}

		$coupon_code = sprintf( $string, $completed_number, date_i18n( 'Y-m-d-H-i-s' ) );
		$description = __( 'Automatically applied discount for regular customers.', 'nth-order-discount-for-woocommerce' );

		$coupon = new WC_Coupon();
		$coupon->set_code( $coupon_code );
		$coupon->set_description( $description );
		$coupon->set_discount_type( $discount_type );
		$coupon->set_amount( $amount );
		$coupon->set_usage_limit( 1 );
		$coupon->set_usage_limit_per_user( 1 );
		$coupon->add_meta_data( 'for_nth_order', true );
		$coupon->add_meta_data( 'customer_id', get_current_user_id() );
		$coupon->save();

		return $coupon;
	}

	/**
	 * Change coupon action messages if coupon is for the discount.
	 *
	 * @todo Also filter message via AJAX
	 *
	 * @since 1.0.0
	 *
	 * @param string     $msg      Original message.
	 * @param int        $msg_code Code of message type.
	 * @param \WC_Coupon $coupon   WooCommerce coupon object.
	 * @return string $msg
	 */
	public static function filter_coupon_message( $msg, $msg_code, $coupon ) {
		// Check if coupon is for the discount.
		if ( ! static::is_coupon_for_discount( $coupon ) ) {
			return $msg;
		}

		switch ( $msg_code ) {
			case $coupon::WC_COUPON_SUCCESS:
				$msg = __( 'Discount added.', 'nth-order-discount-for-woocommerce' );
				break;
			case $coupon::WC_COUPON_REMOVED:
				$msg = __( 'Discount removed.', 'nth-order-discount-for-woocommerce' );
				break;
		}

		return $msg;
	}

	/**
	 * Store that cart used discount but removed it.
	 *
	 * @since 1.0.0
	 *
	 * @param string $coupon_code Code of the coupon to remove.
	 */
	public static function removed_coupon( $coupon_code ) {
		// Check if coupon is for the discount.
		if ( ! static::is_coupon_for_discount( $coupon_code ) ) {
			return;
		}

		WC()->session->set( 'nth_order_coupon_removed', true );
	}

	/**
	 * Remove session data when cart is emptied.
	 *
	 * @since 1.0.0
	 */
	public static function emptied_cart() {
		unset( WC()->session->nth_order_coupon_removed );
	}

	/**
	 * Check that coupon is for current user.
	 *
	 * @since 1.0.0
	 */
	public static function validate_customer() {
		foreach ( WC()->cart->get_applied_coupons() as $code ) {
			$coupon = new WC_Coupon( $code );

			if ( $coupon->is_valid() && $coupon->get_meta( 'customer_id' ) != get_current_user_id() ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				$coupon->add_coupon_message( WC_Coupon::E_WC_COUPON_NOT_YOURS_REMOVED );
				WC()->cart->remove_coupon( $code );
			}
		}
	}
}
