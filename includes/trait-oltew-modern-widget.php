<?php
/**
 * Shared controls and secure rendering for order widgets.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Shared implementation for the two table layouts. */
trait Oltew_Modern_Widget {
	use Oltew_Table_Design;

	/**
	 * Get the presentation variant.
	 *
	 * @return string
	 */
	protected function get_layout() {
		return 'table';
	}

	/**
	 * Load styles only for pages containing a widget.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'order-list-table' );
	}

	/**
	 * Load the delegated product expansion handler for order widgets.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'oltew-order-list', 'oltew-table-pagination' );
	}

	/**
	 * Prevent Elementor element caching of customer-specific orders.
	 *
	 * @return bool
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Define canonical fields with the original setting identifiers.
	 *
	 * @return array
	 */
	private function oltew_fields() {
		return array(
			'serial'   => array(
				'label' => esc_html__( 'SL', 'order-list-table-elementor-widget' ),
				'hide'  => 'hide_order_sl',
				'class' => 'order_sl',
			),
			'customer' => array(
				'label'   => esc_html__( 'Customer', 'order-list-table-elementor-widget' ),
				'setting' => 'customer_name',
				'hide'    => 'hide_customer_title',
				'icon'    => 'customer_th_icon',
				'class'   => 'customer_name',
			),
			'time'     => array(
				'label'   => esc_html__( 'Sell Time', 'order-list-table-elementor-widget' ),
				'setting' => 'sell_time',
				'hide'    => 'hide_sell_time',
				'icon'    => 'sell_time_th_icon',
				'class'   => 'sell_time',
			),
			'status'   => array(
				'label'   => esc_html__( 'Status', 'order-list-table-elementor-widget' ),
				'setting' => 'order_status',
				'hide'    => 'hide_status',
				'icon'    => 'order_status_th_icon',
				'class'   => 'order_status',
			),
			'products' => array(
				'label'   => esc_html__( 'Products', 'order-list-table-elementor-widget' ),
				'setting' => 'product_name',
				'hide'    => 'hide_product_name',
				'icon'    => 'product_name_th_icon',
				'class'   => 'product_list',
			),
			'phone'    => array(
				'label'   => esc_html__( 'Phone', 'order-list-table-elementor-widget' ),
				'setting' => 'customer_phone',
				'hide'    => 'hide_customer_phone',
				'icon'    => 'customer_phone_th_icon',
				'class'   => 'customer_phone',
			),
			'amount'   => array(
				'label'   => esc_html__( 'Amount', 'order-list-table-elementor-widget' ),
				'setting' => 'sell_amount',
				'hide'    => 'hide_sell_amount',
				'icon'    => 'sell_amount_th_icon',
				'class'   => 'sell_amount',
			),
			'action'   => array(
				'label'   => esc_html__( 'Action', 'order-list-table-elementor-widget' ),
				'setting' => 'buy_now',
				'hide'    => 'hide_buy_now',
				'icon'    => 'buy_now_th_icon',
				'class'   => 'buy_now',
			),
		);
	}

	/** Add layout, privacy, ordering and design controls. */
	protected function register_modern_controls() {
		$this->start_controls_section( 'oltew_presentation', array( 'label' => esc_html__( 'Layout & Effects', 'order-list-table-elementor-widget' ) ) );
		$this->add_control(
			'oltew_title',
			array(
				'label'   => esc_html__( 'Widget title', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Recent orders', 'order-list-table-elementor-widget' ),
			)
		);
		$this->add_control(
			'oltew_subtitle',
			array(
				'label'   => esc_html__( 'Subtitle', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'The latest activity from our store', 'order-list-table-elementor-widget' ),
			)
		);
		foreach ( array(
			'oltew_show_heading'  => esc_html__( 'Show title & subtitle', 'order-list-table-elementor-widget' ),
			'oltew_show_count'    => esc_html__( 'Show displayed order count', 'order-list-table-elementor-widget' ),
			'oltew_heading_icons' => esc_html__( 'Show heading icons', 'order-list-table-elementor-widget' ),
			'oltew_body_icons'    => esc_html__( 'Show content icons', 'order-list-table-elementor-widget' ),
			'oltew_hover'         => esc_html__( 'Enable hover effect', 'order-list-table-elementor-widget' ),
			'oltew_shadow'        => esc_html__( 'Enable shadow', 'order-list-table-elementor-widget' ),
			'oltew_dividers'      => esc_html__( 'Show field dividers', 'order-list-table-elementor-widget' ),
		) as $name => $label ) {
			$this->add_control(
				$name,
				array(
					'label'   => $label,
					'type'    => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'yes',
				)
			);
		}
		$this->add_control(
			'oltew_processing_spin',
			array(
				'label'   => esc_html__( 'Rotate processing icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);
		$this->add_control(
			'oltew_spin_speed',
			array(
				'label'     => esc_html__( 'Rotation duration (ms)', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 500,
				'max'       => 5000,
				'default'   => 1800,
				'selectors' => array( '{{WRAPPER}} .oltew-orders' => '--oltew-spin-duration: {{VALUE}}ms;' ),
				'condition' => array( 'oltew_processing_spin' => 'yes' ),
			)
		);
		$this->add_control(
			'oltew_animation',
			array(
				'label'   => esc_html__( 'Entrance animation', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'rise',
				'options' => array(
					'none'  => esc_html__( 'None', 'order-list-table-elementor-widget' ),
					'fade'  => esc_html__( 'Fade in', 'order-list-table-elementor-widget' ),
					'rise'  => esc_html__( 'Slide up', 'order-list-table-elementor-widget' ),
					'scale' => esc_html__( 'Soft zoom', 'order-list-table-elementor-widget' ),
				),
			)
		);
		$this->add_control(
			'oltew_duration',
			array(
				'label'     => esc_html__( 'Animation duration (ms)', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 100,
				'max'       => 2000,
				'default'   => 500,
				'selectors' => array( '{{WRAPPER}} .oltew-orders' => '--oltew-duration: {{VALUE}}ms;' ),
				'condition' => array( 'oltew_animation!' => 'none' ),
			)
		);
		$this->add_control(
			'oltew_stagger',
			array(
				'label'     => esc_html__( 'Stagger rows', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array( 'oltew_animation!' => 'none' ),
			)
		);
		$this->add_control(
			'oltew_stripes',
			array(
				'label'   => esc_html__( 'Alternating row backgrounds', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
			)
		);
		$this->add_control(
			'oltew_sticky_header',
			array(
				'label'   => esc_html__( 'Sticky table header', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
			)
		);
		$this->add_control(
			'oltew_table_height',
			array(
				'label'     => esc_html__( 'Table scroll height (px)', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 160,
				'max'       => 1600,
				'default'   => 480,
				'selectors' => array( '{{WRAPPER}} .oltew-sticky .oltew-scroll' => 'max-height: {{VALUE}}px;' ),
				'condition' => array( 'oltew_sticky_header' => 'yes' ),
			)
		);
		$this->add_control(
			'oltew_empty_text',
			array(
				'label'   => esc_html__( 'Empty state text', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'No orders to display yet.', 'order-list-table-elementor-widget' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'oltew_column_settings', array( 'label' => esc_html__( 'Drag & Drop Field Order', 'order-list-table-elementor-widget' ) ) );
		$repeater = new \Elementor\Repeater();
		$options  = array();
		$defaults = array();
		foreach ( $this->oltew_fields() as $key => $field ) {
			$options[ $key ] = $field['label'];
			$defaults[]      = array(
				'field'   => $key,
				'label'   => '',
				'enabled' => 'yes',
			);
		}
		$repeater->add_control(
			'field',
			array(
				'label'   => esc_html__( 'Field', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $options,
				'default' => 'customer',
			)
		);
		$repeater->add_control(
			'label',
			array(
				'label'       => esc_html__( 'Custom heading', 'order-list-table-elementor-widget' ),
				'description' => esc_html__( 'Leave empty to use the existing heading setting.', 'order-list-table-elementor-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'enabled',
			array(
				'label'   => esc_html__( 'Show field', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);
		$this->add_control(
			'oltew_columns',
			array(
				'label'         => esc_html__( 'Drag items to reorder', 'order-list-table-elementor-widget' ),
				'description'   => esc_html__( 'Drag row handles to reorder table columns. Existing Hide/Show settings also apply. Removed fields stay hidden; duplicate fields are ignored.', 'order-list-table-elementor-widget' ),
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => $defaults,
				'title_field'   => '{{{ field }}}',
				'prevent_empty' => false,
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'oltew_privacy', array( 'label' => esc_html__( 'Order Visibility & Privacy', 'order-list-table-elementor-widget' ) ) );
		$this->add_control(
			'oltew_scope',
			array(
				'label'       => esc_html__( 'Who can see orders?', 'order-list-table-elementor-widget' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'public',
				'options'     => array(
					'public'   => esc_html__( 'Public store activity (masked names)', 'order-list-table-elementor-widget' ),
					'own'      => esc_html__( 'Logged-in customer: own orders', 'order-list-table-elementor-widget' ),
					'managers' => esc_html__( 'Store managers only', 'order-list-table-elementor-widget' ),
				),
				'description' => esc_html__( 'Public activity includes enabled product, date, status and total fields. Names and phone numbers are masked. Exclude pages with customer-specific visibility from full-page caching.', 'order-list-table-elementor-widget' ),
			)
		);
		$this->add_control(
			'oltew_customer_name',
			array(
				'label'   => esc_html__( 'Customer name display', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'masked',
				'options' => array(
					'masked'    => esc_html__( 'Initials only', 'order-list-table-elementor-widget' ),
					'full'      => esc_html__( 'Full name (private views only)', 'order-list-table-elementor-widget' ),
					'anonymous' => esc_html__( 'Anonymous customer', 'order-list-table-elementor-widget' ),
				),
			)
		);
		$this->end_controls_section();

		$this->register_table_design_controls();
		$this->register_stock_badge_controls();
		$this->register_load_more_controls();
	}

	/**
	 * Normalize, deduplicate and filter the ordered fields.
	 *
	 * @param array $settings Elementor settings.
	 * @return array
	 */
	private function oltew_visible_fields( $settings ) {
		$fields = $this->oltew_fields();
		$rows   = isset( $settings['oltew_columns'] ) && is_array( $settings['oltew_columns'] ) ? $settings['oltew_columns'] : array_map(
			function ( $key ) {
				return array(
					'field'   => $key,
					'enabled' => 'yes',
				);
			},
			array_keys( $fields )
		);
		$result = array();
		foreach ( $rows as $row ) {
			$key = isset( $row['field'] ) && is_string( $row['field'] ) ? $row['field'] : '';
			if ( ! isset( $fields[ $key ] ) || isset( $result[ $key ] ) || 'yes' !== ( $row['enabled'] ?? 'yes' ) || 'yes' === ( $settings[ $fields[ $key ]['hide'] ] ?? '' ) ) {
				continue;
			}
			$field          = $fields[ $key ];
			$field['label'] = ! empty( $row['label'] ) ? $row['label'] : ( $settings[ $field['setting'] ?? '' ] ?? $field['label'] );
			$result[ $key ] = $field;
		}
		return $result;
	}

	/** Render tables using one secure order query and field renderer. */
	protected function render_modern_orders() {
		if ( ! function_exists( 'wc_get_orders' ) ) {
			return;
		}
		$settings = $this->get_settings_for_display();
		$scope    = $settings['oltew_scope'] ?? 'public';
		if ( 'public' !== $scope ) {
			if ( ! defined( 'DONOTCACHEPAGE' ) ) {
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Standard cache-plugin opt-out constant.
				define( 'DONOTCACHEPAGE', true );
			}
			if ( ! headers_sent() ) {
				nocache_headers();
			}
		}
		if ( ! in_array( $scope, array( 'public', 'own', 'managers' ), true ) || ( 'own' === $scope && ! is_user_logged_in() ) || ( 'managers' === $scope && ! current_user_can( 'manage_woocommerce' ) ) ) {
			echo '<div class="oltew-orders oltew-empty" role="status">' . esc_html__( 'Please sign in with an authorized account to view orders.', 'order-list-table-elementor-widget' ) . '</div>';
			return;
		}
		$fields   = $this->oltew_visible_fields( $settings );
		$statuses = array_intersect( (array) ( $settings['select_status'] ?? array( 'wc-completed', 'wc-processing' ) ), array_keys( wc_get_order_statuses() ) );
		$args     = array(
			'type'     => 'shop_order',
			'status'   => array_values( $statuses ),
			'orderby'  => 'date',
			'order'    => 'asc' === ( $settings['order_by'] ?? 'desc' ) ? 'ASC' : 'DESC',
			'limit'    => max( 1, min( 100, absint( $settings['list_per_page'] ?? 10 ) ) ),
			'return'   => 'objects',
			'paginate' => 'yes' === ( $settings['oltew_load_more'] ?? 'yes' ),
			// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Signed, size-bounded consumed IDs prevent duplicate rows as live orders change.
			'exclude'  => $this->oltew_seen_ids(),
		);
		if ( 'own' === $scope ) {
			$args['customer_id'] = get_current_user_id();
		}
		$orders  = $this->oltew_page_result( $statuses && $fields ? wc_get_orders( $args ) : array(), 'orders', $args['limit'] );
		$layout  = $this->get_layout();
		$classes = array( 'oltew-orders', 'oltew-order-list-table', 'oltew-layout-' . $layout );
		foreach ( array(
			'oltew_hover'             => 'oltew-hover',
			'oltew_shadow'            => 'oltew-shadow',
			'oltew_processing_spin'   => 'oltew-processing-spin',
			'oltew_stripes'           => 'oltew-striped',
			'oltew_sticky_header'     => 'oltew-sticky',
			'order_list_mobile_style' => 'oltew-mobile-cards',
		) as $setting => $class ) {
			if ( 'yes' === ( $settings[ $setting ] ?? '' ) ) {
				$classes[] = $class;
			}
		}
		if ( 'yes' !== ( $settings['oltew_dividers'] ?? 'yes' ) ) {
			$classes[] = 'oltew-no-dividers';
		}
		$toggle_field = isset( $fields['products'] ) ? 'products' : 'action';
		if ( $this->oltew_is_append() ) {
			$this->oltew_render_order_rows( $orders, $fields, $settings, $toggle_field );
			return;
		}
		$animation = $settings['oltew_animation'] ?? 'none';
		if ( in_array( $animation, array( 'fade', 'rise', 'scale' ), true ) ) {
			$classes[] = 'oltew-animate-' . $animation;
		}
		$head_align = in_array( $settings['oltew_table_th_alignment'] ?? '', array( 'left', 'center', 'right', 'justify' ), true ) ? $settings['oltew_table_th_alignment'] : 'left';
		$body_align = in_array( $settings['oltew_table_td_alignment'] ?? '', array( 'left', 'center', 'right', 'justify' ), true ) ? $settings['oltew_table_td_alignment'] : 'left';
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="--oltew-head-align: <?php echo esc_attr( $head_align ); ?>; --oltew-body-align: <?php echo esc_attr( $body_align ); ?>;">
			<?php if ( 'yes' === ( $settings['oltew_show_heading'] ?? '' ) ) : ?>
				<div class="oltew-toolbar">
					<div>
						<?php
						if ( ! empty( $settings['oltew_title'] ) ) :
							?>
							<h3 class="oltew-title"><?php echo esc_html( $settings['oltew_title'] ); ?></h3><?php endif; ?>
						<?php
						if ( ! empty( $settings['oltew_subtitle'] ) ) :
							?>
							<p class="oltew-subtitle"><?php echo esc_html( $settings['oltew_subtitle'] ); ?></p><?php endif; ?>
					</div>
					<?php
					if ( 'yes' === ( $settings['oltew_show_count'] ?? '' ) ) :
						?>
						<span class="oltew-count">
						<?php
						/* translators: %s: Number of orders displayed in this widget. */
						echo esc_html( sprintf( _n( '%s order', '%s orders', count( $orders ), 'order-list-table-elementor-widget' ), number_format_i18n( count( $orders ) ) ) );
						?>
					</span><?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( ! $orders && ! $this->oltew_has_more() ) : ?>
				<div class="oltew-empty oltew-surface" role="status"><?php echo esc_html( $settings['oltew_empty_text'] ?? __( 'No orders to display yet.', 'order-list-table-elementor-widget' ) ); ?></div>
			<?php else : ?>
				<div class="oltew-scroll oltew-surface" role="region" aria-label="<?php esc_attr_e( 'Recent orders table', 'order-list-table-elementor-widget' ); ?>" tabindex="0">
					<table class="oltew-table">
						<caption class="oltew-screen-reader"><?php echo esc_html( $settings['oltew_title'] ?? __( 'Recent orders', 'order-list-table-elementor-widget' ) ); ?></caption>
						<thead><tr>
						<?php
						foreach ( $fields as $field ) :
							?>
							<th scope="col"><?php $this->oltew_render_heading( $field, $settings ); ?></th><?php endforeach; ?></tr></thead>
						<tbody>
						<?php $this->oltew_render_order_rows( $orders, $fields, $settings, $toggle_field ); ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
			<?php $this->oltew_render_load_more( $settings ); ?>
		</div>
		<?php
	}

	/**
	 * Render a batch of rows for initial and Load More requests.
	 *
	 * @param array  $orders Order objects.
	 * @param array  $fields Visible fields.
	 * @param array  $settings Widget settings.
	 * @param string $toggle_field Expansion field.
	 */
	private function oltew_render_order_rows( $orders, $fields, $settings, $toggle_field ) {
		?>
							<?php foreach ( $orders as $index => $order ) : ?>
								<?php $row_id = wp_unique_id( 'oltew-order-' . $this->get_id() . '-' . $this->oltew_serial_offset() . '-' ); ?>
								<tr id="<?php echo esc_attr( $row_id ); ?>" class="oltew-item" style="--oltew-index: <?php echo absint( 'yes' === ( $settings['oltew_stagger'] ?? '' ) ? min( $index, 10 ) : 0 ); ?>;">
									<?php
									foreach ( $fields as $key => $field ) :
										?>
										<td class="<?php echo esc_attr( $field['class'] ); ?>" data-column="<?php echo esc_attr( $field['label'] ); ?>"><?php $this->oltew_render_value( $key, $order, $this->oltew_serial_offset() + $index, $settings, $row_id, $toggle_field ); ?></td><?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
		<?php
	}

	/**
	 * Render a field heading.
	 *
	 * @param array $field Field definition.
	 * @param array $settings Settings.
	 */
	private function oltew_render_heading( $field, $settings ) {
		echo '<span class="heading_content">';
		if ( 'yes' === ( $settings['oltew_heading_icons'] ?? 'yes' ) && isset( $field['icon'] ) ) {
			$this->oltew_icon( $settings[ $field['icon'] ] ?? array() );
		}
		echo '<span>' . esc_html( $field['label'] ) . '</span></span>';
	}

	/**
	 * Render an icon with Elementor's renderer.
	 *
	 * @param array  $icon Icon settings.
	 * @param string $wrapper_class Wrapper class.
	 */
	private function oltew_icon( $icon, $wrapper_class = 'order_list_icon' ) {
		if ( ! is_array( $icon ) || empty( $icon['value'] ) ) {
			return;
		}
		// Normalize the original widget's Font Awesome 4 defaults in saved pages.
		if ( 'solid' === ( $icon['library'] ?? '' ) && is_string( $icon['value'] ) ) {
			$icon = \Elementor\Icons_Manager::fa4_to_fa5_value_migration( $icon['value'] );
		}
		if ( in_array( $icon['library'] ?? '', array( 'fa-solid', 'fa-regular', 'fa-brands' ), true ) ) {
			wp_enqueue_style( 'elementor-icons-' . $icon['library'] );
		}
		echo '<span class="' . esc_attr( $wrapper_class ) . '" aria-hidden="true">';
		\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
		echo '</span>';
	}

	/**
	 * Render a field with output escaping and privacy checks.
	 *
	 * @param string    $key Field identifier.
	 * @param \WC_Order $order Order object.
	 * @param int       $index Position.
	 * @param array     $settings Settings.
	 * @param string    $row_id Unique row identifier.
	 * @param string    $toggle_field Field that owns the expand button.
	 */
	private function oltew_render_value( $key, $order, $index, $settings, $row_id, $toggle_field ) {
		$icons = array(
			'customer' => 'customer_list_icon',
			'time'     => 'sell_list_time_icon',
			'phone'    => 'customer_phone_list_icon',
		);
		echo '<span class="body_content">';
		if ( 'yes' === ( $settings['oltew_body_icons'] ?? 'yes' ) && isset( $icons[ $key ] ) ) {
			$this->oltew_icon( $settings[ $icons[ $key ] ] ?? array() );
		}
		switch ( $key ) {
			case 'serial':
				echo esc_html( number_format_i18n( $index + 1 ) );
				break;
			case 'customer':
				$name = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
				$mode = $settings['oltew_customer_name'] ?? 'masked';
				if ( ! $name || 'anonymous' === $mode ) {
					$name = __( 'Customer', 'order-list-table-elementor-widget' );
				} elseif ( 'full' !== $mode || 'public' === ( $settings['oltew_scope'] ?? 'public' ) ) {
					$parts = preg_split( '/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY );
					$name  = implode(
						' ',
						array_map(
							function ( $part ) {
								return ( function_exists( 'mb_substr' ) ? mb_substr( $part, 0, 1 ) : wp_html_excerpt( $part, 1, '' ) ) . '.';
							},
							$parts
						)
					);
				}
				echo esc_html( $name );
				break;
			case 'phone':
				$phone = preg_replace( '/\D+/', '', $order->get_billing_phone() );
				echo esc_html( strlen( $phone ) > 4 ? substr( $phone, 0, -4 ) . 'XXXX' : ( $phone ? 'XXXX' : '—' ) );
				break;
			case 'time':
				$date = $order->get_date_created();
				if ( $date ) {
					echo '<time datetime="' . esc_attr( $date->date( DATE_ATOM ) ) . '">';
					echo esc_html( 'date' === ( $settings['order_time_format'] ?? 'ago' ) ? wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $date->getTimestamp() ) : oltew_ago_woo_list_table( $date ) );
					echo '</time>';
				} else {
					echo '—';
				}
				break;
			case 'amount':
				echo wp_kses_post( wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ) );
				break;
			case 'status':
				$status       = $order->get_status();
				$class        = in_array( $status, array( 'failed', 'cancelled' ), true ) ? 'cancelled' : $status;
				$status_icons = array(
					'completed'  => 'set_status_completed_icon',
					'processing' => 'set_status_processing_icon',
					'on-hold'    => 'set_status_onhold_icon',
					'failed'     => 'set_status_faild_icon',
					'cancelled'  => 'set_status_faild_icon',
					'refunded'   => 'set_status_refunded_icon',
				);
				echo '<span class="status_odd st_' . esc_attr( sanitize_html_class( $class ) ) . '">';
				if ( 'yes' === ( $settings['oltew_body_icons'] ?? 'yes' ) ) {
					$icon = $settings[ $status_icons[ $status ] ?? '' ] ?? array();
					// Replace the previous default waiting icon in existing saved widgets.
					if ( 'processing' === $status && is_array( $icon ) && in_array( $icon['value'] ?? '', array( 'fa fa-hourglass', 'fas fa-hourglass' ), true ) ) {
						$icon = array(
							'value'   => 'fas fa-circle-notch',
							'library' => 'fa-solid',
						);
					}
					$this->oltew_icon( $icon, 'status_icon' );
				}
				echo esc_html( wc_get_order_status_name( $status ) ) . '</span>';
				if ( 'refunded' !== $status && $order->get_total_refunded() > 0 ) {
					echo '<span class="status_odd st_refunded">' . esc_html__( 'Partially refunded', 'order-list-table-elementor-widget' ) . '</span>';
				}
				break;
			case 'products':
			case 'action':
				$this->oltew_render_products( $key, $order, $settings, $row_id, $toggle_field );
				break;
		}
		echo '</span>';
	}

	/** Add stock-out visibility, label and appearance controls to both order tables. */
	private function register_stock_badge_controls() {
		$this->start_controls_section( 'oltew_stock_badge', array( 'label' => esc_html__( 'Stock Out Badge', 'order-list-table-elementor-widget' ) ) );
		$this->add_control(
			'oltew_show_stock_out',
			array(
				'label'   => esc_html__( 'Show out-of-stock badge', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);
		$this->add_control(
			'oltew_stock_out_text',
			array(
				'label'     => esc_html__( 'Badge text', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Out of stock', 'order-list-table-elementor-widget' ),
				'condition' => array( 'oltew_show_stock_out' => 'yes' ),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'oltew_stock_badge_style',
			array(
				'label' => esc_html__( 'Stock Out Badge', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		foreach ( array(
			'oltew_stock_out_color'      => array( esc_html__( 'Text color', 'order-list-table-elementor-widget' ), 'color' ),
			'oltew_stock_out_background' => array( esc_html__( 'Background', 'order-list-table-elementor-widget' ), 'background' ),
		) as $key => $style ) {
			$this->add_control(
				$key,
				array(
					'label'     => $style[0],
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .oltew-orders .oltew-order-stock-out' => $style[1] . ': {{VALUE}};' ),
				)
			);
		}
		$this->add_responsive_control(
			'oltew_stock_out_radius',
			array(
				'label'       => esc_html__( 'Border radius', 'order-list-table-elementor-widget' ),
				'description' => esc_html__( 'Leave empty to use the Buy Now button radius.', 'order-list-table-elementor-widget' ),
				'type'        => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units'  => array( 'px', '%', 'em', 'rem' ),
				'selectors'   => array(
					'{{WRAPPER}} .oltew-orders .oltew-order-stock-out' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Keep product and action positions identical, excluding private products.
	 *
	 * @param \WC_Order $order Order object.
	 * @return array
	 */
	private function oltew_display_items( $order ) {
		$items = array();
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();
			if ( $product && ! is_post_publicly_viewable( $product->get_parent_id() ? $product->get_parent_id() : $product->get_id() ) ) {
				continue;
			}
			$items[] = array(
				'item'    => $item,
				'product' => $product,
			);
		}
		return $items;
	}

	/**
	 * Render matched product/action lists, initially showing only the first item.
	 *
	 * @param string    $key Products or action field.
	 * @param \WC_Order $order Order object.
	 * @param array     $settings Widget settings.
	 * @param string    $row_id Unique row identifier.
	 * @param string    $toggle_field Field containing the row's single toggle.
	 */
	private function oltew_render_products( $key, $order, $settings, $row_id, $toggle_field ) {
		$items     = $this->oltew_display_items( $order );
		$collapsed = 'yes' === ( $settings['oltew_expand_products'] ?? 'yes' ) && count( $items ) > 1;
		echo '<span class="oltew-product-stack">';
		foreach ( $items as $position => $entry ) {
			$item    = $entry['item'];
			$product = $entry['product'];
			echo '<span class="oltew-product-entry"';
			if ( $collapsed && $position > 0 ) {
				echo ' data-oltew-extra hidden';
			}
			echo '>';
			if ( 'products' === $key ) {
				$visual = $settings['oltew_product_visual'] ?? 'icon';
				if ( 'image' === $visual && $product ) {
					echo wp_kses_post(
						$product->get_image(
							'woocommerce_thumbnail',
							array(
								'class'           => 'oltew-product-thumb',
								'alt'             => '',
								'loading'         => 'lazy',
								// Keep native lazyload; theme noscript fallbacks are stripped by KSES.
								'data-cs-no-lazy' => '1',
							)
						)
					);
				} elseif ( in_array( $visual, array( 'icon', 'image' ), true ) && 'yes' === ( $settings['oltew_body_icons'] ?? 'yes' ) ) {
					$this->oltew_icon( $settings['product_list_icon'] ?? array() );
				}
				if ( $product ) {
					$title = wp_trim_words( $item->get_name(), max( 1, min( 50, absint( $settings['product_link_words'] ?? 5 ) ) ) );
					echo '<a href="' . esc_url( $product->get_permalink() ) . '">' . esc_html( $title ) . '</a>';
				} else {
					echo '<span>' . esc_html__( 'Product unavailable', 'order-list-table-elementor-widget' ) . '</span>';
				}
			} elseif ( $product && ! $product->is_in_stock() ) {
				if ( 'yes' === ( $settings['oltew_show_stock_out'] ?? 'yes' ) ) {
					echo '<span class="status_odd oltew-order-stock-out">' . esc_html( $settings['oltew_stock_out_text'] ?? __( 'Out of stock', 'order-list-table-elementor-widget' ) ) . '</span>';
				}
			} elseif ( $product && $product->is_purchasable() ) {
				$url = $product->is_type( 'simple' ) ? add_query_arg( 'add-to-cart', $product->get_id(), wc_get_checkout_url() ) : $product->get_permalink();
				echo '<a class="buy-now-button" href="' . esc_url( $url ) . '" aria-label="' . esc_attr( ( $settings['buy_now_button_text'] ?? __( 'Buy Now', 'order-list-table-elementor-widget' ) ) . ': ' . $product->get_name() ) . '">';
				if ( 'yes' === ( $settings['oltew_body_icons'] ?? 'yes' ) ) {
					$this->oltew_icon( $settings['buy_now_list_icon'] ?? array(), 'buy_now_icon' );
				}
				echo esc_html( $settings['buy_now_button_text'] ?? __( 'Buy Now', 'order-list-table-elementor-widget' ) ) . '</a>';
			}
			echo '</span>';
		}
		echo '</span>';
		if ( $collapsed && $key === $toggle_field ) {
			echo '<button type="button" class="oltew-products-toggle" aria-expanded="false" aria-controls="' . esc_attr( $row_id ) . '"><span class="oltew-expand-label">';
			/* translators: 1: Expand button label, 2: Number of additional product lines. */
			echo esc_html( sprintf( __( '%1$s (%2$s)', 'order-list-table-elementor-widget' ), $settings['oltew_expand_text'] ?? __( 'Show more', 'order-list-table-elementor-widget' ), number_format_i18n( count( $items ) - 1 ) ) );
			echo '</span><span class="oltew-collapse-label" hidden>' . esc_html( $settings['oltew_collapse_text'] ?? __( 'Show less', 'order-list-table-elementor-widget' ) ) . '</span><span class="oltew-toggle-chevron" aria-hidden="true"></span></button>';
		}
	}
}
