<?php
/**
 * Elementor integration and dependency checks.
 *
 * @package OrderListTableElementorWidget
 */

namespace OltewOrderListTableEle;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Load widgets only when dependencies are available. */
final class Plugin {
	const VERSION                   = '3.2.4';
	const MINIMUM_ELEMENTOR_VERSION = '3.7.0';
	const MINIMUM_PHP_VERSION       = '7.4';

	/**
	 * Shared plugin instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get the shared instance.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/** Initialize the integration. */
	public function __construct() {
		require_once __DIR__ . '/class-oltew-settings.php';
		new \Oltew_Settings();
		if ( class_exists( 'WooCommerce' ) ) {
			require_once __DIR__ . '/class-oltew-order-query.php';
			require_once __DIR__ . '/trait-oltew-table-pagination.php';
			require_once __DIR__ . '/trait-oltew-order-table-renderer.php';
			require_once __DIR__ . '/class-oltew-load-more.php';
			require_once __DIR__ . '/class-oltew-account-table.php';
			require_once __DIR__ . '/class-oltew-my-account.php';
			new \Oltew_Load_More();
			new \Oltew_My_Account();
		}
		if ( $this->is_compatible() ) {
			add_action( 'elementor/init', array( $this, 'init' ) );
		}
	}

	/**
	 * Verify dependencies without changing activation state.
	 *
	 * @return bool
	 */
	private function is_compatible() {
		if ( ! did_action( 'elementor/loaded' ) || ! class_exists( 'WooCommerce' ) || ! defined( 'ELEMENTOR_VERSION' ) || version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '<' ) || version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'dependency_notice' ) );
			return false;
		}
		return true;
	}

	/** Register the Elementor hooks. */
	public function init() {
		require_once __DIR__ . '/class-oltew-catalog-search.php';
		new \Oltew_Catalog_Search();
		add_action( 'elementor/controls/register', array( $this, 'register_catalog_control' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	/**
	 * Register the remote catalog selector.
	 *
	 * @param \Elementor\Controls_Manager $controls_manager Control registry.
	 */
	public function register_catalog_control( $controls_manager ) {
		require_once __DIR__ . '/class-oltew-catalog-select-control.php';
		$controls_manager->register( new \Oltew_Catalog_Select_Control() );
	}

	/**
	 * Group both order table widgets in their own Elementor category.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elementor category registry.
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'oltew-order-list-table',
			array(
				'title' => esc_html__( 'Order List Table', 'order-list-table-elementor-widget' ),
				'icon'  => 'eicon-table',
			)
		);
	}

	/**
	 * Register the order and product table widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widget registry.
	 */
	public function register_widgets( $widgets_manager ) {
		require_once __DIR__ . '/trait-oltew-table-pagination.php';
		require_once __DIR__ . '/trait-oltew-pagination-controls.php';
		require_once __DIR__ . '/trait-oltew-table-design.php';
		require_once __DIR__ . '/trait-oltew-modern-widget.php';
		require_once __DIR__ . '/class-oltew-order-list-table-ele-widget.php';
		require_once __DIR__ . '/class-oltew-compact-table-widget.php';
		require_once __DIR__ . '/class-oltew-product-list-table-widget.php';
		$widgets = array(
			'woo_recent_orders_table'    => '\\Oltew_Order_List_Table_Ele_Widget',
			'oltew_compact_orders_table' => '\\Oltew_Compact_Table_Widget',
			'oltew_product_list_table'   => '\\Oltew_Product_List_Table_Widget',
		);
		foreach ( $widgets as $name => $class ) {
			if ( \Oltew_Settings::widget_enabled( $name ) ) {
				$widgets_manager->register( new $class() );
			}
		}
	}

	/** Display an escaped notice describing minimum dependencies. */
	public function dependency_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			esc_html(
				sprintf(
					/* translators: 1: Minimum Elementor version, 2: Minimum PHP version. */
					__( 'Order List Table: WooCommerce and PHP %2$s or newer are required. Elementor %1$s or newer is required only for the Elementor widgets. My Account tables work without Elementor.', 'order-list-table-elementor-widget' ),
					self::MINIMUM_ELEMENTOR_VERSION,
					self::MINIMUM_PHP_VERSION
				)
			)
		);
	}
}
