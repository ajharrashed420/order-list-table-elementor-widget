<?php
/**
 * Signed, read-only pagination requests for the three table widgets.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Keep table configuration and private order scope under server control. */
class Oltew_Load_More {
	/** Register public and authenticated pagination handlers. */
	public function __construct() {
		add_action( 'wp_ajax_oltew_load_more', array( $this, 'ajax' ) );
		add_action( 'wp_ajax_nopriv_oltew_load_more', array( $this, 'ajax' ) );
	}

	/**
	 * Sign the next page's configuration; no client-supplied settings are trusted.
	 *
	 * @param object $widget Table widget or account adapter.
	 * @param array  $settings Rendered settings.
	 * @param array  $state Next-page cursor.
	 * @return string
	 */
	public static function token( $widget, $settings, $state ) {
		$private = 'oltew_product_list_table' !== $widget->get_name() && 'public' !== ( $settings['oltew_scope'] ?? 'public' );
		$data    = array(
			'widget'   => $widget->get_name(),
			'id'       => $widget->get_id(),
			'settings' => $settings,
			'state'    => $state,
			'user'     => $private ? get_current_user_id() : 0,
			'expires'  => time() + DAY_IN_SECONDS,
		);
		// Hex keeps the signed JSON safe in HTML attributes and form fields.
		$encoded = bin2hex( wp_json_encode( $data ) );
		return $encoded . '.' . hash_hmac( 'sha256', $encoded, wp_salt( 'auth' ) );
	}

	/**
	 * Verify integrity, expiry, supported widget and the current user's scope.
	 *
	 * @param string $token Signed cursor.
	 * @return array|WP_Error
	 */
	public static function validate( $token ) {
		if ( ! is_string( $token ) || strlen( $token ) > 262144 ) {
			return new WP_Error( 'invalid_cursor', __( 'Invalid pagination request.', 'order-list-table-elementor-widget' ) );
		}
		$parts = explode( '.', $token );
		if ( 2 !== count( $parts ) || ! ctype_xdigit( $parts[0] ) || 0 !== strlen( $parts[0] ) % 2 || ! hash_equals( hash_hmac( 'sha256', $parts[0], wp_salt( 'auth' ) ), $parts[1] ) ) {
			return new WP_Error( 'invalid_cursor', __( 'Invalid pagination request.', 'order-list-table-elementor-widget' ) );
		}
		$data = json_decode( hex2bin( $parts[0] ), true );
		if ( ! is_array( $data ) || ! is_array( $data['settings'] ?? null ) || ! is_array( $data['state'] ?? null ) || ! is_string( $data['id'] ?? null ) || ! preg_match( '/^[a-zA-Z0-9_-]{1,80}$/', $data['id'] ) || ! in_array( $data['widget'] ?? '', array( 'woo_recent_orders_table', 'oltew_compact_orders_table', 'oltew_product_list_table', 'oltew_account_orders' ), true ) ) {
			return new WP_Error( 'invalid_cursor', __( 'Invalid pagination request.', 'order-list-table-elementor-widget' ) );
		}
		if ( (int) ( $data['expires'] ?? 0 ) < time() ) {
			return new WP_Error( 'expired_cursor', __( 'Please reload this page before loading more rows.', 'order-list-table-elementor-widget' ) );
		}
		if ( 'oltew_account_orders' === $data['widget'] ) {
			$options = Oltew_Settings::get();
			if ( 'yes' !== $options['account_enabled'] || 'own' !== ( $data['settings']['oltew_scope'] ?? '' ) ) {
				return new WP_Error( 'disabled_table', __( 'This account table is unavailable.', 'order-list-table-elementor-widget' ) );
			}
			$table = new Oltew_Account_Table();
			if ( $data['settings'] !== $table->get_settings_for_display() ) {
				return new WP_Error( 'changed_settings', __( 'Table settings have changed. Please reload this page.', 'order-list-table-elementor-widget' ) );
			}
		} elseif ( ! Oltew_Settings::widget_enabled( $data['widget'] ) ) {
			return new WP_Error( 'disabled_table', __( 'This widget has been disabled.', 'order-list-table-elementor-widget' ) );
		}
		$scope = 'oltew_product_list_table' === $data['widget'] ? 'public' : ( $data['settings']['oltew_scope'] ?? 'public' );
		if ( ! in_array( $scope, array( 'public', 'own', 'managers' ), true ) || ( 'public' !== $scope && ( ! is_user_logged_in() || get_current_user_id() !== (int) ( $data['user'] ?? 0 ) ) ) || ( 'managers' === $scope && ! current_user_can( 'manage_woocommerce' ) ) ) {
			return new WP_Error( 'forbidden_cursor', __( 'You cannot load these orders.', 'order-list-table-elementor-widget' ) );
		}
		return $data;
	}

	/** Return only table rows, the next signed cursor and updated count text. */
	public function ajax() {
		check_ajax_referer( 'oltew_load_more', 'nonce' );
		$token = isset( $_POST['token'] ) && is_string( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : '';
		$data  = self::validate( $token );
		if ( is_wp_error( $data ) ) {
			wp_send_json_error( array( 'message' => $data->get_error_message() ), 403 );
		}
		if ( 'oltew_account_orders' === $data['widget'] ) {
			$widget           = new Oltew_Account_Table( $data['id'] );
			$data['settings'] = $widget->get_settings_for_display();
		} elseif ( class_exists( '\Elementor\Plugin' ) ) {
			$widget = \Elementor\Plugin::instance()->elements_manager->create_element_instance(
				array(
					'id'         => $data['id'],
					'elType'     => 'widget',
					'widgetType' => $data['widget'],
					'settings'   => $data['settings'],
				)
			);
		} else {
			$widget = null;
		}
		if ( ! $widget || ! method_exists( $widget, 'oltew_render_next' ) || 'yes' !== ( $data['settings']['oltew_load_more'] ?? 'yes' ) ) {
			wp_send_json_error( array( 'message' => __( 'This table cannot load more rows.', 'order-list-table-elementor-widget' ) ), 400 );
		}
		$result                = $widget->oltew_render_next( $data['state'] );
		$result['token']       = $result['has_more'] ? self::token( $widget, $data['settings'], $result['state'] ) : '';
		$result['nonce']       = wp_create_nonce( 'oltew_load_more' );
		$result['count_label'] = $widget->oltew_count_label( $result['state']['displayed'] );
		unset( $result['state'] );
		nocache_headers();
		wp_send_json_success( $result );
	}
}
