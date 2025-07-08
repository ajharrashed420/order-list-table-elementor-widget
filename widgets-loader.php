<?php
namespace OltewOrderListTableEle;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class Plugin {

	const VERSION = '3.0.0';
	const MINIMUM_ELEMENTOR_VERSION = '3.7.0';
	const MINIMUM_PHP_VERSION = '7.4';

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		// Fallback only for older WordPress versions without plugin dependencies
		if ( ! function_exists( 'wp_get_active_and_valid_plugins' ) ) {
			add_action( 'admin_init', [ $this, 'maybe_deactivate' ] );
			return;
		}

		// If compatible, hook into Elementor init
		if ( $this->is_compatible() ) {
			add_action( 'elementor/init', [ $this, 'init' ] );
		}
	}

	private function is_compatible() {

		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_missing_elementor' ] );
			return false;
		}

		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_missing_woocommerce' ] );
			return false;
		}

		if ( version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_minimum_elementor' ] );
			return false;
		}

		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_minimum_php' ] );
			return false;
		}

		return true;
	}

	// ❌ Fallback: Deactivate if dependencies not met (for old WP versions)
	public function maybe_deactivate() {
		if ( ! is_admin() || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$deactivate = false;

		if ( ! class_exists( 'WooCommerce' ) || ! did_action( 'elementor/loaded' ) ) {
			$deactivate = true;
		}

		if ( $deactivate ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			add_action( 'admin_notices', [ $this, 'notice_dependency_missing_deactivated' ] );
		}
	}

	public function init() {
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}

	public function register_widgets( $widgets_manager ) {
		require_once __DIR__ . '/order-list-table-register.php';
		$widgets_manager->register( new \Oltew_Order_List_table_Ele_Widget() );
	}

	// Admin Notices

	public function notice_missing_elementor() {
		$this->print_notice(
			sprintf(
				esc_html__( '"%1$s" requires %2$s to be installed and activated.', 'order-list-table-elementor-widget' ),
				'<strong>' . esc_html__( 'Woocommerce Order List Table', 'order-list-table-elementor-widget' ) . '</strong>',
				'<strong>' . esc_html__( 'Elementor', 'order-list-table-elementor-widget' ) . '</strong>'
			)
		);
	}

	public function notice_missing_woocommerce() {
		$this->print_notice(
			sprintf(
				esc_html__( '"%1$s" requires %2$s to be installed and activated.', 'order-list-table-elementor-widget' ),
				'<strong>' . esc_html__( 'Woocommerce Order List Table', 'order-list-table-elementor-widget' ) . '</strong>',
				'<strong>' . esc_html__( 'WooCommerce', 'order-list-table-elementor-widget' ) . '</strong>'
			)
		);
	}

	public function notice_minimum_elementor() {
		$this->print_notice(
			sprintf(
				esc_html__( '"%1$s" requires %2$s version %3$s or greater.', 'order-list-table-elementor-widget' ),
				'<strong>' . esc_html__( 'Woocommerce Order List Table', 'order-list-table-elementor-widget' ) . '</strong>',
				'<strong>' . esc_html__( 'Elementor', 'order-list-table-elementor-widget' ) . '</strong>',
				self::MINIMUM_ELEMENTOR_VERSION
			)
		);
	}

	public function notice_minimum_php() {
		$this->print_notice(
			sprintf(
				esc_html__( '"%1$s" requires %2$s version %3$s or greater.', 'order-list-table-elementor-widget' ),
				'<strong>' . esc_html__( 'Woocommerce Order List Table', 'order-list-table-elementor-widget' ) . '</strong>',
				'<strong>' . esc_html__( 'PHP', 'order-list-table-elementor-widget' ) . '</strong>',
				self::MINIMUM_PHP_VERSION
			)
		);
	}

	public function notice_dependency_missing_deactivated() {
		$this->print_notice(
			esc_html__( 'Plugin deactivated because required dependencies are missing.', 'order-list-table-elementor-widget' )
		);
	}

	private function print_notice( $message ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		printf(
			'<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>',
			wp_kses_post( $message )
		);
	}
}
