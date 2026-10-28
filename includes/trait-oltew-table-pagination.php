<?php
/**
 * Shared pagination controls and rendering state.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Paginate catalog and order tables without exposing editable query arguments. */
trait Oltew_Table_Pagination {
	/**
	 * Verified incoming cursor.
	 *
	 * @var array
	 */
	private $oltew_cursor = array(
		'page'      => 1,
		'displayed' => 0,
		'seen'      => array(),
	);

	/**
	 * State for the next request.
	 *
	 * @var array
	 */
	private $oltew_next = array(
		'has_more' => false,
		'state'    => array(
			'page'      => 2,
			'displayed' => 0,
			'seen'      => array(),
		),
	);

	/**
	 * Whether to output rows without the surrounding widget.
	 *
	 * @var bool
	 */
	private $oltew_append_only = false;

	/**
	 * Normalize WooCommerce pagination metadata and track consumed IDs.
	 *
	 * @param array|object $result WooCommerce query result.
	 * @param string       $kind Collection key.
	 * @param int          $limit Batch size.
	 * @param callable     $visible Optional visibility check.
	 * @return array
	 */
	protected function oltew_page_result( $result, $kind, $limit, $visible = null ) {
		$records = is_object( $result ) ? (array) ( $result->$kind ?? array() ) : (array) $result;
		$total   = is_object( $result ) ? (int) ( $result->total ?? count( $records ) ) : count( $records );
		$records = array_slice( array_values( $records ), 0, $limit );
		$ids     = array();
		foreach ( $records as $record ) {
			if ( is_object( $record ) && method_exists( $record, 'get_id' ) && $record->get_id() > 0 ) {
				$ids[] = $record->get_id();
			}
		}
		$items            = $visible ? array_values( array_filter( $records, $visible ) ) : $records;
		$this->oltew_next = array(
			'has_more' => count( $records ) > 0 && $total > count( $records ),
			'state'    => array(
				'page'      => $this->oltew_cursor['page'] + 1,
				'displayed' => $this->oltew_cursor['displayed'] + count( $items ),
				'seen'      => array_values( array_unique( array_merge( $this->oltew_cursor['seen'], $ids ) ) ),
			),
		);
		return $items;
	}

	/**
	 * Get already-consumed IDs to avoid repeating rows when ranking changes.
	 *
	 * @return array
	 */
	protected function oltew_seen_ids() {
		return $this->oltew_cursor['seen'];
	}

	/**
	 * Get the serial offset for appended rows.
	 *
	 * @return int
	 */
	protected function oltew_serial_offset() {
		return $this->oltew_cursor['displayed'];
	}

	/**
	 * Get whether unseen rows are available.
	 *
	 * @return bool
	 */
	protected function oltew_has_more() {
		return $this->oltew_next['has_more'];
	}

	/**
	 * Get whether this render is an append request.
	 *
	 * @return bool
	 */
	protected function oltew_is_append() {
		return $this->oltew_append_only;
	}

	/**
	 * Render the next batch using a verified cursor.
	 *
	 * @param array $cursor Verified pagination context.
	 * @return array
	 */
	public function oltew_render_next( $cursor ) {
		$this->oltew_cursor      = array(
			'page'      => max( 2, absint( $cursor['page'] ?? 2 ) ),
			'displayed' => absint( $cursor['displayed'] ?? 0 ),
			'seen'      => array_values( array_filter( array_map( 'absint', (array) ( $cursor['seen'] ?? array() ) ) ) ),
		);
		$this->oltew_append_only = true;
		ob_start();
		try {
			$this->render();
			$rows = ob_get_contents();
		} finally {
			ob_end_clean();
		}
		return array_merge( $this->oltew_next, array( 'rows' => $rows ) );
	}

	/**
	 * Format the cumulative displayed count in the site's language.
	 *
	 * @param int $count Displayed rows.
	 * @return string
	 */
	public function oltew_count_label( $count ) {
		if ( 'oltew_product_list_table' === $this->get_name() ) {
			/* translators: %s: Number of products displayed. */
			return sprintf( _n( '%s product', '%s products', $count, 'order-list-table-elementor-widget' ), number_format_i18n( $count ) );
		}
		/* translators: %s: Number of orders displayed. */
		return sprintf( _n( '%s order', '%s orders', $count, 'order-list-table-elementor-widget' ), number_format_i18n( $count ) );
	}

	/**
	 * Output a configurable button only when another batch exists.
	 *
	 * @param array $settings Widget settings.
	 */
	protected function oltew_render_load_more( $settings ) {
		if ( 'yes' !== ( $settings['oltew_load_more'] ?? 'yes' ) || ! $this->oltew_has_more() ) {
			return;
		}
		?>
		<div class="oltew-load-more-wrap">
			<button type="button" class="oltew-load-more" data-oltew-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-oltew-token="<?php echo esc_attr( Oltew_Load_More::token( $this, $settings, $this->oltew_next['state'] ) ); ?>" data-oltew-nonce="<?php echo esc_attr( wp_create_nonce( 'oltew_load_more' ) ); ?>" data-oltew-loading="<?php echo esc_attr( $settings['oltew_load_more_loading'] ?? __( 'Loading…', 'order-list-table-elementor-widget' ) ); ?>" data-oltew-error="<?php echo esc_attr( $settings['oltew_load_more_error'] ?? __( 'Unable to load rows. Please try again or reload the page.', 'order-list-table-elementor-widget' ) ); ?>"><?php echo esc_html( $settings['oltew_load_more_text'] ?? __( 'Load More', 'order-list-table-elementor-widget' ) ); ?></button>
			<p class="oltew-load-more-status" role="status" aria-live="polite"></p>
		</div>
		<?php
	}
}
