<?php
/**
 * \dimadin\WP\Plugin\commonWP\WC\NthOrderDiscount\Singleton trait.
 *
 * @package NthOrderDiscount
 * @since 1.0.0
 */

namespace dimadin\WP\Plugin\WC\NthOrderDiscount;

/**
 * Singleton pattern.
 *
 * @since 1.0.0
 *
 * @link http://www.sitepoint.com/using-traits-in-php-5-4/
 */
trait Singleton {
	/**
	 * Instantiate called class.
	 *
	 * @return object $instance Instance of called class.
	 */
	public static function get_instance() {
		static $instance = false;

		if ( false === $instance ) {
			$instance = new static();
		}

		return $instance;
	}
}
