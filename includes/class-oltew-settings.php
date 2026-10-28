<?php
/**
 * Settings API registration, schema and sanitization.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Store validated integration settings independently of Elementor. */
class Oltew_Settings {
	const OPTION = 'oltew_settings';
	const PAGE   = 'oltew-settings';

	/** Register administrator-only settings and assets. */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( OLTEW_FILE ), array( $this, 'action_links' ) );
	}

	/**
	 * Define enabled widgets using stable saved Elementor identifiers.
	 *
	 * @return array
	 */
	public static function widgets() {
		return array(
			'woo_recent_orders_table'    => __( 'Order List Table (Modern)', 'order-list-table-elementor-widget' ),
			'oltew_compact_orders_table' => __( 'Order List Compact Table', 'order-list-table-elementor-widget' ),
			'oltew_product_list_table'   => __( 'Product List Table', 'order-list-table-elementor-widget' ),
		);
	}

	/**
	 * Define account columns in display order.
	 *
	 * @return array
	 */
	public static function columns() {
		return array(
			'order'    => __( 'Order number', 'order-list-table-elementor-widget' ),
			'time'     => __( 'Date', 'order-list-table-elementor-widget' ),
			'status'   => __( 'Status', 'order-list-table-elementor-widget' ),
			'products' => __( 'Products', 'order-list-table-elementor-widget' ),
			'amount'   => __( 'Total', 'order-list-table-elementor-widget' ),
			'action'   => __( 'Order actions', 'order-list-table-elementor-widget' ),
			'customer' => __( 'Customer', 'order-list-table-elementor-widget' ),
			'phone'    => __( 'Phone', 'order-list-table-elementor-widget' ),
		);
	}

	/**
	 * Describe each setting once for validation and the settings view.
	 *
	 * @return array
	 */
	public static function fields() {
		$fields = array();
		foreach ( self::widgets() as $name => $label ) {
			$fields[ $name ] = array(
				'type'    => 'toggle',
				'label'   => $label,
				'default' => 'yes',
				'section' => 'widgets',
			);
		}
		$fields += array(
			'account_enabled'       => array(
				'type'    => 'toggle',
				'label'   => __( 'Enable My Account order tables', 'order-list-table-elementor-widget' ),
				'default' => 'no',
				'section' => 'account',
			),
			'account_location'      => array(
				'type'        => 'checkboxes',
				'label'       => __( 'Display locations', 'order-list-table-elementor-widget' ),
				'default'     => array( 'orders' ),
				'section'     => 'account',
				'description' => __( 'Select one or more locations. You can show the table in all three locations.', 'order-list-table-elementor-widget' ),
				'options'     => array(
					'orders'           => __( 'Replace the Orders tab', 'order-list-table-elementor-widget' ),
					'dashboard_before' => __( 'Before the Dashboard content', 'order-list-table-elementor-widget' ),
					'dashboard_after'  => __( 'After the Dashboard content', 'order-list-table-elementor-widget' ),
				),
			),
			'account_layout'        => array(
				'type'    => 'select',
				'label'   => __( 'Table layout', 'order-list-table-elementor-widget' ),
				'default' => 'table',
				'section' => 'account',
				'options' => array(
					'table'   => __( 'Modern', 'order-list-table-elementor-widget' ),
					'compact' => __( 'Compact', 'order-list-table-elementor-widget' ),
				),
			),
			'account_show_title'    => array(
				'type'    => 'toggle',
				'label'   => __( 'Show title', 'order-list-table-elementor-widget' ),
				'default' => 'yes',
				'section' => 'account',
			),
			'account_title'         => array(
				'type'    => 'text',
				'label'   => __( 'Title', 'order-list-table-elementor-widget' ),
				'default' => __( 'My orders', 'order-list-table-elementor-widget' ),
				'section' => 'account',
			),
			'account_show_subtitle' => array(
				'type'    => 'toggle',
				'label'   => __( 'Show subtitle', 'order-list-table-elementor-widget' ),
				'default' => 'yes',
				'section' => 'account',
			),
			'account_subtitle'      => array(
				'type'    => 'text',
				'label'   => __( 'Subtitle', 'order-list-table-elementor-widget' ),
				'default' => __( 'Track your purchases and view order details.', 'order-list-table-elementor-widget' ),
				'section' => 'account',
			),
			'account_show_count'    => array(
				'type'    => 'toggle',
				'label'   => __( 'Show order count badge', 'order-list-table-elementor-widget' ),
				'default' => 'yes',
				'section' => 'account',
			),
			'account_empty'         => array(
				'type'    => 'text',
				'label'   => __( 'Empty state text', 'order-list-table-elementor-widget' ),
				'default' => __( 'You have not placed any orders yet.', 'order-list-table-elementor-widget' ),
				'section' => 'account',
			),
			'account_per_page'      => array(
				'type'    => 'number',
				'label'   => __( 'Orders per batch', 'order-list-table-elementor-widget' ),
				'default' => 10,
				'section' => 'account',
			),
			'account_date_format'   => array(
				'type'    => 'select',
				'label'   => __( 'Date and time format', 'order-list-table-elementor-widget' ),
				'default' => 'date',
				'section' => 'account',
				'options' => array(
					'date' => __( 'Site date and time', 'order-list-table-elementor-widget' ),
					'ago'  => __( 'Relative time (2 hours ago)', 'order-list-table-elementor-widget' ),
				),
			),
			'account_statuses'      => array(
				'type'        => 'checkboxes',
				'label'       => __( 'Order statuses', 'order-list-table-elementor-widget' ),
				'default'     => array(),
				'section'     => 'account',
				'options'     => function_exists( 'wc_get_order_statuses' ) ? wc_get_order_statuses() : array(),
				'description' => __( 'Leave all unchecked to show every registered order status.', 'order-list-table-elementor-widget' ),
			),
			'account_columns'       => array(
				'type'    => 'checkboxes',
				'label'   => __( 'Visible columns', 'order-list-table-elementor-widget' ),
				'default' => array( 'order', 'time', 'status', 'products', 'amount', 'action' ),
				'section' => 'account',
				'options' => self::columns(),
			),
			'account_mobile'        => array(
				'type'    => 'toggle',
				'label'   => __( 'Use cards on mobile', 'order-list-table-elementor-widget' ),
				'default' => 'yes',
				'section' => 'account',
			),
			'account_expand'        => array(
				'type'    => 'toggle',
				'label'   => __( 'Expand additional products on demand', 'order-list-table-elementor-widget' ),
				'default' => 'yes',
				'section' => 'account',
			),
		);
		$colors  = array(
			'accent'     => array( __( 'Accent and buttons', 'order-list-table-elementor-widget' ), '#4f46e5' ),
			'text'       => array( __( 'Body text', 'order-list-table-elementor-widget' ), '#334155' ),
			'heading'    => array( __( 'Title and column headings', 'order-list-table-elementor-widget' ), '#0f172a' ),
			'surface'    => array( __( 'Table background', 'order-list-table-elementor-widget' ), '#ffffff' ),
			'border'     => array( __( 'Borders', 'order-list-table-elementor-widget' ), '#e8edf5' ),
			'muted'      => array( __( 'Secondary text', 'order-list-table-elementor-widget' ), '#64748b' ),
			'completed'  => array( __( 'Completed status text', 'order-list-table-elementor-widget' ), '#047857' ),
			'processing' => array( __( 'Processing status text', 'order-list-table-elementor-widget' ), '#1d4ed8' ),
			'pending'    => array( __( 'Pending and on-hold status text', 'order-list-table-elementor-widget' ), '#92400e' ),
			'failed'     => array( __( 'Failed and cancelled status text', 'order-list-table-elementor-widget' ), '#b91c1c' ),
			'refunded'   => array( __( 'Refunded status text', 'order-list-table-elementor-widget' ), '#6d28d9' ),
		);
		foreach ( $colors as $key => $color ) {
			$fields[ 'color_' . $key ] = array(
				'type'    => 'color',
				'label'   => $color[0],
				'default' => $color[1],
				'section' => 'appearance',
			);
		}
		return $fields;
	}

	/**
	 * Read settings with backward-compatible defaults.
	 *
	 * @return array
	 */
	public static function get() {
		$defaults                     = wp_list_pluck( self::fields(), 'default' );
		$saved                        = get_option( self::OPTION, array() );
		$settings                     = wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
		$settings['account_location'] = self::normalize_account_locations( $settings['account_location'] );
		return $settings;
	}

	/**
	 * Normalize multiple placements and preserve previously saved single choices.
	 *
	 * @param mixed $locations Submitted or saved locations.
	 * @return array
	 */
	public static function normalize_account_locations( $locations ) {
		if ( is_string( $locations ) ) {
			$legacy    = array(
				'both_before' => array( 'orders', 'dashboard_before' ),
				'both_after'  => array( 'orders', 'dashboard_after' ),
			);
			$locations = $legacy[ $locations ] ?? array( $locations );
		}
		$allowed   = array( 'orders', 'dashboard_before', 'dashboard_after' );
		$locations = is_array( $locations ) ? array_values( array_intersect( $allowed, array_filter( $locations, 'is_string' ) ) ) : array();
		return $locations ? $locations : array( 'orders' );
	}

	/**
	 * Whether an Elementor widget is enabled.
	 *
	 * @param string $name Stable widget name.
	 * @return bool
	 */
	public static function widget_enabled( $name ) {
		$settings = self::get();
		return isset( self::widgets()[ $name ] ) && 'yes' === $settings[ $name ];
	}

	/** Register the option; options.php verifies the capability and nonce. */
	public function register() {
		register_setting(
			'oltew_settings_group',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => array(),
				'show_in_rest'      => false,
			)
		);
	}

	/**
	 * Accept only schema keys and their allowed values.
	 *
	 * @param mixed $input Submitted settings.
	 * @return array
	 */
	public function sanitize( $input ) {
		$input  = is_array( $input ) ? $input : array();
		$result = array();
		foreach ( self::fields() as $key => $field ) {
			$value = $input[ $key ] ?? null;
			if ( 'account_location' === $key ) {
				$result[ $key ] = self::normalize_account_locations( $value );
				continue;
			}
			switch ( $field['type'] ) {
				case 'toggle':
					$result[ $key ] = 'yes' === $value ? 'yes' : 'no';
					break;
				case 'checkboxes':
					$result[ $key ] = is_array( $value ) ? array_values( array_intersect( array_keys( $field['options'] ), array_filter( $value, 'is_string' ) ) ) : array();
					break;
				case 'select':
					$result[ $key ] = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default'];
					break;
				case 'color':
					$result[ $key ] = is_string( $value ) && sanitize_hex_color( $value ) ? sanitize_hex_color( $value ) : $field['default'];
					break;
				case 'number':
					$result[ $key ] = is_scalar( $value ) ? max( 1, min( 100, absint( $value ) ) ) : $field['default'];
					break;
				default:
					$result[ $key ] = is_string( $value ) ? sanitize_text_field( $value ) : $field['default'];
			}
		}
		if ( empty( $result['account_columns'] ) ) {
			$result['account_columns'] = array( 'order', 'status', 'amount', 'action' );
			add_settings_error( self::OPTION, 'oltew_columns', __( 'At least one column is required. The essential order columns have been restored.', 'order-list-table-elementor-widget' ), 'warning' );
		}
		return $result;
	}

	/** Add a top-level settings menu, also available without dependencies. */
	public function menu() {
		add_menu_page( __( 'Order List Table', 'order-list-table-elementor-widget' ), __( 'Order List Table', 'order-list-table-elementor-widget' ), 'manage_options', self::PAGE, array( $this, 'render' ), 'dashicons-list-view', 56 );
	}

	/**
	 * Load presentation assets only on this plugin's settings screen.
	 *
	 * @param string $hook Current admin screen.
	 */
	public function assets( $hook ) {
		if ( 'toplevel_page_' . self::PAGE !== $hook ) {
			return;
		}
		wp_enqueue_style( 'oltew-admin', OLTEW_URL . 'assets/css/admin.css', array( 'wp-color-picker', 'dashicons' ), OLTEW_VERSION );
		wp_enqueue_script( 'oltew-admin', OLTEW_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), OLTEW_VERSION, true );
	}

	/**
	 * Link directly from the plugin list.
	 *
	 * @param array $links Plugin action links.
	 * @return array
	 */
	public function action_links( $links ) {
		if ( current_user_can( 'manage_options' ) ) {
			array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php?page=' . self::PAGE ) ) . '">' . esc_html__( 'Settings', 'order-list-table-elementor-widget' ) . '</a>' );
		}
		return $links;
	}

	/** Render the administrator-only view. */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$oltew_settings = self::get();
		$oltew_fields   = self::fields();
		include OLTEW_PATH . 'templates/admin-settings.php';
	}
}
