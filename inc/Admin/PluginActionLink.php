<?php
/**
 * \dimadin\WP\Plugin\commonWP\WC\NthOrderDiscount\Admin\PluginActionLink class.
 *
 * @package NthOrderDiscount
 * @since 1.0.0
 */

namespace dimadin\WP\Plugin\WC\NthOrderDiscount\Admin;

/**
 * Class that adds notices to admin screens.
 *
 * @since 1.0.0
 */
class PluginActionLink {
	/**
	 * Show action links on the plugin screen.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $actions     An array of plugin action links.
	 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
	 * @param array  $plugin_data An array of plugin data. See `get_plugin_data()`.
	 * @return array $actions
	 */
	public static function add( $actions, $plugin_file, $plugin_data ) {
		if ( array_key_exists( 'TextDomain', $plugin_data ) && 'nth-order-discount-for-woocommerce' === $plugin_data['TextDomain'] ) {
			$actions['settings'] = '<a href="' . admin_url( 'admin.php?page=wc-settings' ) . '">' . esc_html__( 'Settings', 'nth-order-discount-for-woocommerce' ) . '</a>';
		}

		return $actions;
	}
}
