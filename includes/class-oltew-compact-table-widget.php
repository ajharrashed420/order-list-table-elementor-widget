<?php
/**
 * Compact order table widget.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** A second table layout with separated rows. */
class Oltew_Compact_Table_Widget extends Oltew_Order_List_Table_Ele_Widget {
	/**
	 * Get widget identifier.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'oltew_compact_orders_table';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Order List Compact Table', 'order-list-table-elementor-widget' );
	}

	/**
	 * Get layout identifier.
	 *
	 * @return string
	 */
	protected function get_layout() {
		return 'compact';
	}
}
