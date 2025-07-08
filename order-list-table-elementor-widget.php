<?php
/**
 * Plugin Name: Order List Table Elementor Widget for Woocommerce
 * Description: Order List Table use for to show Woocommerce recent order list on a table, just use this Elementor Widget/Addon. 
 * Plugin URI:  https://wpmethods.com/order-list-table-elementor-widget
 * Version:     3.0.0
 * Author:      WP Methods
 * Author URI:  https://wpmethods.com/
 * Text Domain: order-list-table-elementor-widget
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * Tested up to: 6.9
 * Requires Plugins: elementor, woocommerce
 * Elementor tested up to: 3.30.1
 * Elementor Pro tested up to: 3.30.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* adds stylesheet file to the end of the queue */
function oltew_order_list_table_enq_style(){
    $dir = plugin_dir_url(__FILE__);
    wp_enqueue_style('order-list-table', $dir . '/css/custom-style.css', array(), '2.0.2', 'all');
    wp_enqueue_style(
        'font-awesome',
        $dir . '/css/font-awesome.min.css',
        array(),
        '5.15.4'
    );
}
add_action('wp_enqueue_scripts', 'oltew_order_list_table_enq_style');

function oltew_order_list_table() {

    // Load plugin file
    require_once( __DIR__ . '/widgets-loader.php' );
    require_once( __DIR__ . '/date-ago-function.php' );

    // Run the plugin
    \OltewOrderListTableEle\Plugin::instance();

}
add_action( 'plugins_loaded', 'oltew_order_list_table' );




//Donation Link
add_filter('plugin_row_meta', 'oltew_donation_link', 10, 2);

function oltew_donation_link($links, $file) {
    // Replace 'your-plugin/your-plugin.php' with the actual path to your plugin file
    if ($file == plugin_basename(__FILE__)) {
        $links[] = '<a href="https://buymeacoffee.com/ajharrashed" target="_blank" style="color: #0073aa;">' . __('🎁Donate', 'order-list-table-elementor-widget') . '</a>';
    }
    return $links;
}



