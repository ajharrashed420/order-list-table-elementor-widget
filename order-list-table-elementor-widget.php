<?php
/**
 * Plugin Name: WooCommerce Order List Table for Elementor
 * Description: To show Woocommerce recent order list on a table, just use this Elementor Widget/Addon. 
 * Plugin URI:  https://wpmethods.com/order-list-table-elementor-widget
 * Version:     2.0.1
 * Author:      WP Methods
 * Author URI:  https://wpmethods.com/
 * Text Domain: oltew-order-list-table-ele
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Elementor tested up to: 3.29.2
 * Elementor Pro tested up to: 3.29.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* adds stylesheet file to the end of the queue */
function oltew_order_list_table_enq_style(){
    $dir = plugin_dir_url(__FILE__);
    wp_enqueue_style('order-list-table', $dir . '/css/custom-style.css', array(), '2.0.1', 'all');
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
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
        $links[] = '<a href="https://buymeacoffee.com/ajharrashed" target="_blank" style="color: #0073aa;">' . __('🎁Donate', 'oltew-order-list-table') . '</a>';
    }
    return $links;
}



