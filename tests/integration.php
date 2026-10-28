<?php
/**
 * Read-only integration checks against a local WordPress/WooCommerce installation.
 * Run with the site's PHP configuration. No settings, users or orders are saved.
 *
 * @package OrderListTableElementorWidget
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}

$_SERVER['HTTP_HOST']   = 'cs-dev.local';
$_SERVER['REQUEST_URI'] = '/';
define( 'DISABLE_WP_CRON', true );

// Optional second bootstrap verifies the account integration without Elementor.
if ( in_array( '--without-elementor', $argv, true ) ) {
	require_once dirname( __DIR__, 4 ) . '/wp-includes/plugin.php';
	add_filter( 'option_active_plugins', static function ( $plugins ) {
		return array_values( array_filter( $plugins, static function ( $plugin ) {
			return ! in_array( $plugin, array( 'elementor/elementor.php', 'elementor-pro/elementor-pro.php' ), true );
		} ) );
	} );
}

require dirname( __DIR__, 4 ) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/template.php';

$oltew_checks = 0;
$oltew_assert = static function ( $condition, $message ) use ( &$oltew_checks ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
	++$oltew_checks;
};
$oltew_options = wp_list_pluck( Oltew_Settings::fields(), 'default' );
add_filter( 'pre_option_oltew_settings', static function () use ( &$oltew_options ) {
	return $oltew_options;
} );
$oltew_assert( Oltew_Settings::widget_enabled( 'woo_recent_orders_table' ), 'Modern enabled by default.' );
$oltew_assert( Oltew_Settings::widget_enabled( 'oltew_compact_orders_table' ), 'Compact enabled by default.' );
$oltew_assert( Oltew_Settings::widget_enabled( 'oltew_product_list_table' ), 'Catalog enabled by default.' );
$oltew_settings = new Oltew_Settings();
$oltew_clean = $oltew_settings->sanitize( array(
	'account_enabled' => array( 'yes' ), 'account_per_page' => 9999,
	'account_location' => '../../escape', 'color_accent' => '#fff;}body{display:none}',
	'account_title' => '<script>alert(1)</script><b>Orders</b>',
	'account_statuses' => array( 'wc-completed', 'wc-unknown', array( 'wc-failed' ) ),
	'account_columns' => array(), 'unexpected' => 'discard me',
) );
$oltew_assert( 'no' === $oltew_clean['account_enabled'], 'Malformed toggles rejected.' );
$oltew_assert( 100 === $oltew_clean['account_per_page'], 'Batch size bounded.' );
$oltew_assert( array( 'orders' ) === $oltew_clean['account_location'], 'Location allowlist enforced.' );
$oltew_assert( '#4f46e5' === $oltew_clean['color_accent'], 'CSS injection rejected.' );
$oltew_assert( false === strpos( $oltew_clean['account_title'], '<' ), 'Settings HTML stripped.' );
$oltew_assert( array( 'wc-completed' ) === $oltew_clean['account_statuses'], 'Status allowlist enforced.' );
$oltew_assert( ! empty( $oltew_clean['account_columns'] ) && ! isset( $oltew_clean['unexpected'] ), 'Unknown keys removed; empty columns restored.' );
$oltew_multiclean = $oltew_settings->sanitize( array_merge( $oltew_options, array( 'account_location' => array( 'orders', 'orders', 'dashboard_after', '../invalid', array( 'dashboard_before' ) ) ) ) );
$oltew_assert( array( 'orders', 'dashboard_after' ) === $oltew_multiclean['account_location'], 'Multiple locations are allowlisted and deduplicated.' );
$oltew_emptyclean = $oltew_settings->sanitize( array_merge( $oltew_options, array( 'account_location' => array() ) ) );
$oltew_assert( array( 'orders' ) === $oltew_emptyclean['account_location'], 'Empty locations fall back to the Orders tab.' );
$oltew_assert( '' === oltew_format_order_date( 'not-a-date' ) && '' === oltew_format_order_date( '' ), 'Invalid dates are empty.' );
$oltew_date = new DateTimeImmutable( '2026-01-13 12:00:00', new DateTimeZone( 'UTC' ) );
$oltew_assert( oltew_format_order_date( $oltew_date ) === wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $oltew_date->getTimestamp() ), 'Site date and timezone respected.' );
$oltew_assert( '' !== oltew_format_order_date( time() - 7200, 'ago' ) && '' !== oltew_format_order_date( time() + 7200, 'ago' ), 'Past and future relative dates supported.' );

$oltew_options['account_enabled']  = 'yes';
$oltew_options['account_per_page'] = 2;
$oltew_table = new Oltew_Account_Table();
$oltew_second_table = new Oltew_Account_Table();
$oltew_assert( $oltew_table->get_id() !== $oltew_second_table->get_id(), 'Simultaneous account tables have distinct instance IDs.' );
$oltew_resumed_table = new Oltew_Account_Table( $oltew_table->get_id() );
$oltew_assert( $oltew_resumed_table->get_id() === $oltew_table->get_id(), 'Load More preserves the signed table instance ID.' );
wp_set_current_user( 0 );
$oltew_assert( array() === Oltew_Order_Query::get( $oltew_table->get_settings_for_display() ), 'Guest cannot query own orders.' );
ob_start();
$oltew_table->render();
$oltew_assert( '' === ob_get_clean(), 'Guest account render is empty.' );

// Use existing data only, and report no customer details.
$oltew_orders = wc_get_orders( array( 'type' => 'shop_order', 'limit' => 200, 'return' => 'objects', 'status' => array_keys( wc_get_order_statuses() ) ) );
$oltew_owners = array();
foreach ( $oltew_orders as $oltew_order ) {
	$oltew_owner = $oltew_order->get_customer_id();
	if ( $oltew_owner && get_user_by( 'id', $oltew_owner ) ) {
		$oltew_owners[ $oltew_owner ] = ( $oltew_owners[ $oltew_owner ] ?? 0 ) + 1;
	}
}
arsort( $oltew_owners );
$oltew_assert( ! empty( $oltew_owners ), 'An existing customer with orders is required.' );
$oltew_owner = (int) array_key_first( $oltew_owners );
wp_set_current_user( $oltew_owner );
$oltew_config = $oltew_table->get_settings_for_display();
$oltew_config['customer_id'] = 999999; // Ignored: ownership comes from the session.
$oltew_page = Oltew_Order_Query::get( $oltew_config );
$oltew_config = $oltew_table->get_settings_for_display();
$oltew_assert( count( $oltew_page->orders ) <= 2 && count( $oltew_page->orders ) > 0, 'Customer batch returned.' );
foreach ( $oltew_page->orders as $oltew_order ) {
	$oltew_assert( $oltew_owner === $oltew_order->get_customer_id(), 'Order belongs to current customer.' );
}
ob_start();
$oltew_table->render();
$oltew_html = ob_get_clean();
$oltew_assert( false !== strpos( $oltew_html, '<table' ) && false !== strpos( $oltew_html, 'view-order' ) && false !== strpos( $oltew_html, 'status_odd' ), 'Shared account table, details and statuses rendered.' );
$oltew_assert( false !== strpos( $oltew_html, 'oltew-mobile-cards' ), 'Mobile cards enabled.' );

$oltew_cursor = array( 'page' => 2, 'displayed' => count( $oltew_page->orders ), 'seen' => array() );
$oltew_cursor['seen'] = array_map( static function ( $order ) { return $order->get_id(); }, $oltew_page->orders );
$oltew_token = Oltew_Load_More::token( $oltew_table, $oltew_config, $oltew_cursor );
$oltew_assert( ! is_wp_error( Oltew_Load_More::validate( $oltew_token ) ), 'Own signed cursor accepted.' );
$oltew_assert( is_wp_error( Oltew_Load_More::validate( $oltew_token . 'x' ) ), 'Tampered cursor rejected.' );
$oltew_expired = json_decode( hex2bin( explode( '.', $oltew_token )[0] ), true );
$oltew_expired['expires'] = time() - 10;
$oltew_encoded = bin2hex( wp_json_encode( $oltew_expired ) );
$oltew_assert( is_wp_error( Oltew_Load_More::validate( $oltew_encoded . '.' . hash_hmac( 'sha256', $oltew_encoded, wp_salt( 'auth' ) ) ) ), 'Expired signed cursor rejected.' );
$oltew_options['account_per_page'] = 3;
$oltew_assert( is_wp_error( Oltew_Load_More::validate( $oltew_token ) ), 'Changed settings require a page reload.' );
$oltew_options['account_per_page'] = 2;
$oltew_batch = $oltew_table->oltew_render_next( $oltew_cursor );
$oltew_assert( false === strpos( $oltew_batch['rows'], '<table' ), 'Load More returns rows only.' );
foreach ( $oltew_cursor['seen'] as $oltew_seen ) {
	$oltew_assert( false === strpos( $oltew_batch['rows'], '/view-order/' . $oltew_seen . '/' ), 'No repeated order in next batch.' );
}

// Verify each supported placement without changing the site's stored settings.
$wp_query->is_page = true;
$wp_query->queried_object = get_post( wc_get_page_id( 'myaccount' ) );
$wp_query->queried_object_id = wc_get_page_id( 'myaccount' );
$wp->query_vars = array();
$oltew_account = new Oltew_My_Account();
$oltew_placements = array(
	array( 'orders', array( 'orders' ) ),
	array( 'dashboard_before', array( 'dashboard_before' ) ),
	array( 'dashboard_after', array( 'dashboard_after' ) ),
	array( 'both_before', array( 'orders', 'dashboard_before' ) ),
	array( 'both_after', array( 'orders', 'dashboard_after' ) ),
	array( array( 'orders' ), array( 'orders' ) ),
	array( array( 'dashboard_before' ), array( 'dashboard_before' ) ),
	array( array( 'dashboard_after' ), array( 'dashboard_after' ) ),
	array( array( 'orders', 'dashboard_before' ), array( 'orders', 'dashboard_before' ) ),
	array( array( 'orders', 'dashboard_after' ), array( 'orders', 'dashboard_after' ) ),
	array( array( 'dashboard_before', 'dashboard_after' ), array( 'dashboard_before', 'dashboard_after' ) ),
	array( array( 'orders', 'dashboard_before', 'dashboard_after' ), array( 'orders', 'dashboard_before', 'dashboard_after' ) ),
);
foreach ( $oltew_placements as $oltew_placement ) {
	$oltew_options['account_location'] = $oltew_placement[0];
	$oltew_expected = $oltew_placement[1];
	$oltew_assert( $oltew_expected === Oltew_Settings::get()['account_location'], 'Saved location normalized without losing placements.' );
	$oltew_account->hooks();
	$oltew_assert( ( false !== has_action( 'woocommerce_account_orders_endpoint', array( $oltew_account, 'render' ) ) ) === in_array( 'orders', $oltew_expected, true ), 'Orders endpoint placement matches settings.' );
	$oltew_assert( ( false !== has_action( 'woocommerce_account_content', array( $oltew_account, 'render' ) ) ) === in_array( 'dashboard_before', $oltew_expected, true ), 'Before-dashboard placement matches settings.' );
	$oltew_assert( ( false !== has_action( 'woocommerce_account_dashboard', array( $oltew_account, 'render' ) ) ) === in_array( 'dashboard_after', $oltew_expected, true ), 'After-dashboard placement matches settings.' );
	remove_action( 'woocommerce_account_orders_endpoint', array( $oltew_account, 'render' ) );
	remove_action( 'woocommerce_account_content', array( $oltew_account, 'render' ), 5 );
	remove_action( 'woocommerce_account_dashboard', array( $oltew_account, 'render' ) );
}
$oltew_options['color_accent'] = '#cc2266';
$oltew_account->assets();
$oltew_inline_styles = wp_styles()->get_data( 'oltew-account', 'after' );
$oltew_assert( false !== strpos( implode( '', $oltew_inline_styles ), '--oltew-accent:#cc2266;' ), 'Selected account accent is emitted as a scoped CSS variable.' );
$oltew_assert( false !== strpos( $oltew_html, 'button oltew-account-action' ), 'Account actions expose their own styling class.' );
$oltew_options['color_accent'] = '#4f46e5';
$oltew_options['account_location'] = 'orders';
$oltew_admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
wp_set_current_user( (int) $oltew_admins[0] );
add_settings_error( 'general', 'oltew_notice_regression', 'OLTEW_NOTICE_REGRESSION', 'success' );
ob_start();
// Top-level plugin pages render their own Settings API notices once.
$oltew_settings->render();
$oltew_admin_html = ob_get_clean();
$oltew_assert( 1 === substr_count( $oltew_admin_html, 'OLTEW_NOTICE_REGRESSION' ), 'Settings notices are printed once during the real header and page lifecycle.' );
$oltew_assert( strpos( $oltew_admin_html, '</header>' ) < strpos( $oltew_admin_html, 'class="wp-header-end"' ) && strpos( $oltew_admin_html, 'class="wp-header-end"' ) < strpos( $oltew_admin_html, '<form' ), 'WordPress notice relocation target sits outside the branded header and before the settings form.' );
$oltew_assert( 3 === substr_count( $oltew_admin_html, 'name="oltew_settings[account_location][]"' ), 'Settings show three independent placement checkboxes.' );
$oltew_assert( false !== strpos( $oltew_admin_html, 'name="_wpnonce"' ) && false !== strpos( $oltew_admin_html, 'action="options.php"' ), 'Settings form includes the WordPress save action and nonce.' );
wp_set_current_user( 0 );
ob_start();
$oltew_settings->render();
$oltew_assert( '' === ob_get_clean(), 'Settings page rejects unauthorized users.' );
wp_set_current_user( $oltew_owner );

// Exercise the real AJAX handler and nonce checks without making HTTP mutations.
define( 'DOING_AJAX', true );
add_filter( 'wp_die_ajax_handler', static function () {
	return static function () { throw new RuntimeException( 'oltew-test-json-end' ); };
} );
$_POST['token'] = $oltew_token;
$_REQUEST['nonce'] = 'invalid';
ob_start();
try {
	( new Oltew_Load_More() )->ajax();
} catch ( RuntimeException $oltew_exception ) {
	$oltew_assert( 'oltew-test-json-end' === $oltew_exception->getMessage(), 'Missing nonce was rejected.' );
}
$oltew_assert( '' === ob_get_clean(), 'Invalid nonce did not expose rows.' );
$_REQUEST['nonce'] = wp_create_nonce( 'oltew_load_more' );
ob_start();
try {
	( new Oltew_Load_More() )->ajax();
} catch ( RuntimeException $oltew_exception ) {
	$oltew_assert( 'oltew-test-json-end' === $oltew_exception->getMessage(), 'JSON response completed.' );
}
$oltew_json = json_decode( ob_get_clean(), true );
$oltew_assert( true === $oltew_json['success'] && isset( $oltew_json['data']['rows'], $oltew_json['data']['nonce'] ), 'Authenticated AJAX returns rows and a refreshed nonce.' );
wp_set_current_user( 0 );
$oltew_assert( is_wp_error( Oltew_Load_More::validate( $oltew_token ) ), 'Logged-out cursor rejected.' );
$oltew_other = get_users( array( 'number' => 1, 'exclude' => array( $oltew_owner ), 'fields' => 'ID' ) );
wp_set_current_user( (int) $oltew_other[0] );
$oltew_assert( is_wp_error( Oltew_Load_More::validate( $oltew_token ) ), 'Another user cannot reuse the cursor.' );
wp_set_current_user( $oltew_owner );
$oltew_options['account_enabled'] = 'no';
$oltew_assert( is_wp_error( Oltew_Load_More::validate( $oltew_token ) ), 'Disabling account integration revokes old cursors.' );
$oltew_options['woo_recent_orders_table'] = 'no';
$oltew_assert( ! Oltew_Settings::widget_enabled( 'woo_recent_orders_table' ), 'Widget toggle disabled.' );

if ( in_array( '--without-elementor', $argv, true ) ) {
	$oltew_assert( ! class_exists( '\Elementor\Plugin' ), 'Elementor was absent during account rendering.' );
} else {
	$oltew_widget_manager = \Elementor\Plugin::instance()->widgets_manager;
	$oltew_assert( ! $oltew_widget_manager->get_widget_types( 'woo_recent_orders_table' ), 'Disabled widget is not registered.' );
	$oltew_assert( (bool) $oltew_widget_manager->get_widget_types( 'oltew_product_list_table' ), 'Enabled product widget is registered.' );
}

printf( "PASS: %d integration assertions. No persistent settings or order changes.\n", $oltew_checks );
