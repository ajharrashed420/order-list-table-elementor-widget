<?php
/**
 * WooCommerce settings adapter for the shared order renderer.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Render customer tables without creating an Elementor widget. */
class Oltew_Account_Table {
	use Oltew_Table_Pagination;
	use Oltew_Order_Table_Renderer;

	/**
	 * Unique instance ID, preserved by signed Load More requests.
	 *
	 * @var string
	 */
	private $table_id;

	/**
	 * Keep rows unique when several account placements appear on one page.
	 *
	 * @param string $table_id Previously signed table ID, or empty for a new table.
	 */
	public function __construct( $table_id = '' ) {
		$this->table_id = $table_id ? sanitize_key( $table_id ) : wp_unique_id( 'account-orders-' );
	}

	/**
	 * Map account settings to the common table configuration.
	 *
	 * @return array
	 */
	public function get_settings_for_display() {
		$options  = Oltew_Settings::get();
		$columns  = array();
		$headings = array(
			'time'   => __( 'Date', 'order-list-table-elementor-widget' ),
			'amount' => __( 'Total', 'order-list-table-elementor-widget' ),
			'action' => __( 'Actions', 'order-list-table-elementor-widget' ),
		);
		foreach ( $options['account_columns'] as $field ) {
			$columns[] = array(
				'field'   => $field,
				'enabled' => 'yes',
				'label'   => $headings[ $field ] ?? '',
			);
		}
		return array(
			'oltew_scope'             => 'own',
			'oltew_customer_name'     => 'full',
			'select_status'           => $options['account_statuses'] ? $options['account_statuses'] : array_keys( wc_get_order_statuses() ),
			'list_per_page'           => $options['account_per_page'],
			'order_by'                => 'desc',
			'order_time_format'       => $options['account_date_format'],
			'oltew_title'             => $options['account_title'],
			'oltew_subtitle'          => $options['account_subtitle'],
			'oltew_empty_text'        => $options['account_empty'],
			'oltew_show_heading'      => 'yes',
			'oltew_show_title'        => $options['account_show_title'],
			'oltew_show_subtitle'     => $options['account_show_subtitle'],
			'oltew_show_count'        => $options['account_show_count'],
			'oltew_heading_icons'     => 'no',
			'oltew_body_icons'        => 'no',
			'oltew_columns'           => $columns,
			'oltew_load_more'         => 'yes',
			'oltew_hover'             => 'yes',
			'oltew_dividers'          => 'yes',
			'order_list_mobile_style' => $options['account_mobile'],
			'oltew_expand_products'   => $options['account_expand'],
			'oltew_product_visual'    => 'image',
			'product_link_words'      => 50,
		);
	}

	/**
	 * Identify the independent account table for signed pagination.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'oltew_account_orders';
	}

	/**
	 * Provide a stable identifier for this table instance.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->table_id;
	}

	/**
	 * Select the configured shared layout.
	 *
	 * @return string
	 */
	protected function get_layout() {
		$options = Oltew_Settings::get();
		return 'compact' === $options['account_layout'] ? 'compact' : 'table';
	}

	/** Render only for a signed-in customer while the integration is enabled. */
	public function render() {
		$options = Oltew_Settings::get();
		if ( is_user_logged_in() && 'yes' === $options['account_enabled'] ) {
			$this->render_modern_orders();
		}
	}
}
