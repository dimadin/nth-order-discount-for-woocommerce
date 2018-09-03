<?php
/**
 * \dimadin\WP\Plugin\commonWP\WC\NthOrderDiscount\Settings\Get class.
 *
 * @package NthOrderDiscount
 * @since 1.0.0
 */

namespace dimadin\WP\Plugin\WC\NthOrderDiscount\Settings;

/**
 * Class for retrieving settings values.
 *
 * @since 1.0.0
 */
class Get {
	/**
	 * Get requested setting value.
	 *
	 * @param string $method    Name of the method being called.
	 * @param array  $arguments Enumerated array containing the parameters passed to the method.
	 * @return mixed $value
	 */
	public static function __callStatic( $method, $arguments ) {
		// Get option value by suffixing requested method name to base.
		$value = get_option( 'nth_order_discount_' . $method );

		/**
		 * Filters the value of setting.
		 *
		 * The dynamic portion of the hook name, `$method`, refers to the setting name.
		 *
		 * @since 1.0.0
		 *
		 * @param mixed  $value  Value of setting.
		 * @param string $method Name of the method being called.
		 */
		return apply_filters( "nth_order_discount_{$method}_setting", $value, $method );
	}
}
