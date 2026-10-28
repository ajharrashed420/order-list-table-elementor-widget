<?php
/**
 * WooCommerce My Account hooks and frontend assets.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Integrate with existing account endpoints without rewrite rules. */
class Oltew_My_Account {
	/** Register frontend hooks after WooCommerce has initialized. */
	public function __construct() {
		add_action( 'wp', array( $this, 'hooks' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
	}

	/**
	 * Whether a logged-in account page needs this integration.
	 *
	 * @return bool
	 */
	private function is_active() {
		$options = Oltew_Settings::get();
		return 'yes' === $options['account_enabled'] && is_user_logged_in() && function_exists( 'is_account_page' ) && is_account_page();
	}

	/** Wire the configured placement and opt out of private-page caching early. */
	public function hooks() {
		if ( ! $this->is_active() ) {
			return;
		}
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Standard cache-plugin opt-out constant.
			define( 'DONOTCACHEPAGE', true );
		}
		nocache_headers();
		$options   = Oltew_Settings::get();
		$locations = $options['account_location'];
		if ( in_array( 'orders', $locations, true ) ) {
			remove_action( 'woocommerce_account_orders_endpoint', 'woocommerce_account_orders' );
			add_action( 'woocommerce_account_orders_endpoint', array( $this, 'render' ) );
		}
		if ( in_array( 'dashboard_before', $locations, true ) && ! is_wc_endpoint_url() ) {
			add_action( 'woocommerce_account_content', array( $this, 'render' ), 5 );
		}
		if ( in_array( 'dashboard_after', $locations, true ) ) {
			add_action( 'woocommerce_account_dashboard', array( $this, 'render' ) );
		}
	}

	/** Render the shared table in an account-scoped wrapper. */
	public function render() {
		if ( ! $this->is_active() ) {
			return;
		}
		echo '<div class="oltew-account">';
		$table = new Oltew_Account_Table();
		$table->render();
		echo '</div>';
	}

	/** Enqueue the shared assets and validated, account-only color variables. */
	public function assets() {
		if ( ! $this->is_active() ) {
			return;
		}
		wp_enqueue_style( 'order-list-table' );
		wp_enqueue_style( 'oltew-account', OLTEW_URL . 'assets/css/account.css', array( 'order-list-table' ), OLTEW_VERSION );
		wp_enqueue_script( 'oltew-order-list' );
		wp_enqueue_script( 'oltew-table-pagination' );
		$options = Oltew_Settings::get();
		$css     = '.oltew-account .oltew-orders{';
		foreach ( Oltew_Settings::fields() as $key => $field ) {
			if ( 'color' !== $field['type'] ) {
				continue;
			}
			$color = sanitize_hex_color( $options[ $key ] );
			if ( $color ) {
				$css .= '--oltew-' . substr( $key, 6 ) . ':' . $color . ';';
			}
		}
		wp_add_inline_style( 'oltew-account', $css . '}' );
	}
}
