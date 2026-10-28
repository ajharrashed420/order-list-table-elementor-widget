<?php
/**
 * Storage-independent order queries with server-enforced visibility.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Keep customer ownership out of client-supplied query arguments. */
class Oltew_Order_Query {
	/**
	 * Query a bounded batch of orders using the WooCommerce CRUD API.
	 *
	 * @param array $settings Table settings.
	 * @param array $seen Already displayed order IDs.
	 * @return array|object
	 */
	public static function get( $settings, $seen = array() ) {
		$scope = $settings['oltew_scope'] ?? 'public';
		if ( ! in_array( $scope, array( 'public', 'own', 'managers' ), true ) || ( 'own' === $scope && ! is_user_logged_in() ) || ( 'managers' === $scope && ! current_user_can( 'manage_woocommerce' ) ) ) {
			return array();
		}
		$statuses = array_intersect( (array) ( $settings['select_status'] ?? array( 'wc-completed', 'wc-processing' ) ), array_keys( wc_get_order_statuses() ) );
		if ( ! $statuses ) {
			return array();
		}
		$args = array(
			'type'     => 'shop_order',
			'status'   => array_values( $statuses ),
			'orderby'  => 'date',
			'order'    => 'asc' === ( $settings['order_by'] ?? 'desc' ) ? 'ASC' : 'DESC',
			'limit'    => max( 1, min( 100, absint( $settings['list_per_page'] ?? 10 ) ) ),
			'return'   => 'objects',
			'paginate' => 'yes' === ( $settings['oltew_load_more'] ?? 'yes' ),
			// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Signed consumed IDs prevent duplicates in live tables.
			'exclude'  => array_map( 'absint', $seen ),
		);
		if ( 'own' === $scope ) {
			$args['customer_id'] = get_current_user_id();
		}
		return wc_get_orders( $args );
	}
}
