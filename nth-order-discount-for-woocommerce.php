<?php
/**
 * Plugin Name: Nth Order Discount for WooCommerce
 * Description: Automatic discounts after every nth order
 * Author:      Milan Dinić
 * Author URI:  https://milandinic.com/
 * Version:     1.0.0-beta-4
 * Text Domain: nth-order-discount-for-woocommerce
 * Domain Path: /languages/
 * License:     GPL
 *
 * @package NthOrderDiscount
 * @since 1.0.0
 */

// Check minimum required PHP version.
if ( version_compare( phpversion(), '5.4.0', '<' ) ) {
	return;
}

// Load dependencies.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

/*
 * Initialize a plugin.
 *
 * Load class when all plugins are loaded
 * so that other plugins can overwrite it.
 */
add_action( 'plugins_loaded', [ 'dimadin\WP\Plugin\WC\NthOrderDiscount\Main', 'get_instance' ], 10 );
