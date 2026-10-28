<?php
/**
 * Plugin Name: Order List Table Elementor Widget for WooCommerce
 * Description: Display WooCommerce orders and products with customizable, responsive table widgets for Elementor.
 * Plugin URI:  https://wpmethods.com/order-list-table-elementor-widget
 * Version:     3.2.0
 * Author:      WP Methods
 * Author URI:  https://wpmethods.com/
 * Text Domain: order-list-table-elementor-widget
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * Requires Plugins: woocommerce
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
define( 'OLTEW_VERSION', '3.2.0' );
define( 'OLTEW_FILE', __FILE__ );
define( 'OLTEW_PATH', plugin_dir_path( __FILE__ ) );
define( 'OLTEW_URL', plugin_dir_url( __FILE__ ) );

/** Register assets; Elementor loads them only when a widget needs them. */
function oltew_order_list_table_enq_style() {
	wp_register_style( 'order-list-table', plugins_url( 'assets/css/custom-style.css', __FILE__ ), array(), OLTEW_VERSION );
	wp_register_script( 'oltew-order-list', plugins_url( 'assets/js/order-list.js', __FILE__ ), array(), OLTEW_VERSION, true );
	wp_register_script( 'oltew-table-pagination', plugins_url( 'assets/js/load-more.js', __FILE__ ), array(), OLTEW_VERSION, true );
}
add_action( 'init', 'oltew_order_list_table_enq_style' );

/** Declare support for WooCommerce's order storage API. */
function oltew_declare_compatibility() {
	if ( class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
}
add_action( 'before_woocommerce_init', 'oltew_declare_compatibility' );

/** Initialize the plugin after dependencies have loaded. */
function oltew_order_list_table() {

	// Load plugin files.
	require_once __DIR__ . '/widgets-loader.php';
	require_once __DIR__ . '/date-ago-function.php';

	// Run the plugin.
	\OltewOrderListTableEle\Plugin::instance();
}
add_action( 'plugins_loaded', 'oltew_order_list_table' );




// Donation link.
add_filter( 'plugin_row_meta', 'oltew_donation_link', 10, 2 );

/**
 * Add the optional donation link to plugin metadata.
 *
 * @param array  $links Existing links.
 * @param string $file Plugin filename.
 * @return array
 */
function oltew_donation_link( $links, $file ) {
	if ( plugin_basename( __FILE__ ) === $file ) {
		$links[] = '<a href="https://buymeacoffee.com/ajharrashed" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Donate', 'order-list-table-elementor-widget' ) . '</a>';
	}
	return $links;
}
