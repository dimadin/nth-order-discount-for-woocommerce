<?php
/**
 * \dimadin\WP\Plugin\commonWP\WC\NthOrderDiscount\Init class.
 *
 * @package NthOrderDiscount
 * @since 1.0.0
 */

namespace dimadin\WP\Plugin\WC\NthOrderDiscount;

use dimadin\WP\Plugin\WC\NthOrderDiscount\Singleton;
use dimadin\WP\Plugin\WC\NthOrderDiscount\Admin\Notices;

/**
 * Class with methods that initialize Nth Order Discount.
 *
 * This class hooks other parts of Nth Order Discount, and
 * other methods that are important for functioning
 * of Nth Order Discount.
 *
 * @since 1.0.0
 */
class Main {
	use Singleton;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		static::hook();
	}

	/**
	 * Hook everything.
	 *
	 * @since 1.0.0
	 */
	public static function hook() {
		// phpcs:disable PEAR.Functions.FunctionCallSignature.SpaceBeforeCloseBracket, Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma, WordPress.Arrays.CommaAfterArrayItem.SpaceAfterComma, WordPress.Arrays.ArrayDeclarationSpacing.SpaceBeforeArrayCloser, Generic.Functions.FunctionCallArgumentSpacing.SpaceBeforeComma

		// If requirements aren't met, don't load plugin.
		if ( ! static::check_requirements() ) {
			return Notices::add_requirements();
		}

		// Show action links on the plugin screen.
		add_filter( 'plugin_action_links',                 [ __NAMESPACE__ . '\Admin\PluginActionLink', 'add'                   ], 10, 3 );

		// Add discount if it doesn't exist to the cart each time totals are calculated.
		add_action( 'woocommerce_before_calculate_totals', [ __NAMESPACE__ . '\Coupon',                 'maybe_add_discount'    ]        );

		// Check that coupon is for current user.
		add_filter( 'woocommerce_check_cart_items',        [ __NAMESPACE__ . '\Coupon',                 'validate_customer'     ]        );

		// Add settings fields to WooCommerce/Settings/General screen.
		add_filter( 'woocommerce_general_settings',        [ __NAMESPACE__ . '\Settings\Page',          'add_settings'          ]        );

		// Add filter to change default messages for coupon actions.
		add_filter( 'woocommerce_coupon_message',          [ __NAMESPACE__ . '\Coupon',                 'filter_coupon_message' ], 10, 3 );

		// Add data to the session when discount is manually removed from cart.
		add_action( 'woocommerce_removed_coupon',          [ __NAMESPACE__ . '\Coupon',                 'removed_coupon'        ]        );

		// Remove session data when cart is emptied.
		add_action( 'woocommerce_cart_emptied',            [ __NAMESPACE__ . '\Coupon',                 'emptied_cart'          ]        );

		// phpcs:enable
	}

	/**
	 * Check if all requirements are met.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether requirements are met.
	 */
	public static function check_requirements() {
		// WooCommerce must be activated.
		if ( ! defined( 'WC_VERSION' ) ) {
			return false;
		}

		// WooCommerce version must be at least 3.1.0.
		if ( version_compare( WC_VERSION, '3.1.0', '<' ) ) {
			return false;
		}

		return true;
	}
}
