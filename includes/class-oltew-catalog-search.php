<?php
/**
 * Authorized, bounded catalog search for Elementor selectors.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Load catalog choices on demand instead of embedding the entire store. */
class Oltew_Catalog_Search {
	/** Register editor assets and the authenticated search action. */
	public function __construct() {
		add_action( 'wp_ajax_oltew_catalog_search', array( $this, 'search' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_editor' ) );
	}

	/** Enqueue the selector view only in the Elementor editor. */
	public function enqueue_editor() {
		wp_enqueue_script( 'oltew-catalog-editor', plugins_url( '../assets/js/catalog-editor.js', __FILE__ ), array( 'jquery', 'elementor-editor' ), OLTEW_VERSION, true );
		wp_localize_script(
			'oltew-catalog-editor',
			'oltewCatalog',
			array(
				'url'         => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'oltew_catalog_search' ),
				'placeholder' => __( 'Type to search…', 'order-list-table-elementor-widget' ),
			)
		);
	}

	/** Search public products/categories or resolve saved selected labels. */
	public function search() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			wp_send_json_error( array( 'message' => __( 'You cannot search this catalog.', 'order-list-table-elementor-widget' ) ), 403 );
		}
		check_ajax_referer( 'oltew_catalog_search', 'nonce' );
		$type = isset( $_POST['catalog_type'] ) && is_string( $_POST['catalog_type'] ) ? sanitize_key( wp_unslash( $_POST['catalog_type'] ) ) : '';
		if ( ! in_array( $type, array( 'products', 'categories' ), true ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid catalog type.', 'order-list-table-elementor-widget' ) ), 400 );
		}
		$term    = isset( $_POST['q'] ) && is_string( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : '';
		$term    = wp_html_excerpt( $term, 120, '' );
		$page    = isset( $_POST['page'] ) && is_scalar( $_POST['page'] ) ? max( 1, min( 1000, absint( $_POST['page'] ) ) ) : 1;
		$ids     = isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ? map_deep( wp_unslash( $_POST['ids'] ), 'sanitize_text_field' ) : array();
		$ids     = array_slice( $ids, 0, 100 );
		$results = array();
		$count   = 0;
		if ( 'categories' === $type ) {
			$args = array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
				'number'     => 20,
				'offset'     => ( $page - 1 ) * 20,
				'search'     => $term,
			);
			if ( $ids ) {
				$args['slug'] = array_map( 'sanitize_title', array_filter( $ids, 'is_scalar' ) );
				if ( ! array_filter( $args['slug'] ) ) {
					wp_send_json_success(
						array(
							'results'    => array(),
							'pagination' => array( 'more' => false ),
						)
					);
				}
				$args['number'] = 100;
				$args['offset'] = 0;
				unset( $args['search'] );
			}
			$terms = get_terms( $args );
			if ( ! is_wp_error( $terms ) ) {
				$count = count( $terms );
				foreach ( $terms as $category ) {
					$results[] = array(
						'id'   => $category->slug,
						'text' => wp_strip_all_tags( $category->name ),
					);
				}
			}
		} elseif ( function_exists( 'wc_get_products' ) ) {
			$args = array(
				'status'     => 'publish',
				'visibility' => 'catalog',
				'limit'      => 20,
				'page'       => $page,
				'orderby'    => 'title',
				'order'      => 'ASC',
				'return'     => 'objects',
			);
			if ( $ids ) {
				$args['include'] = array_values( array_filter( array_map( 'absint', array_filter( $ids, 'is_scalar' ) ) ) );
				if ( ! $args['include'] ) {
					wp_send_json_success(
						array(
							'results'    => array(),
							'pagination' => array( 'more' => false ),
						)
					);
				}
				$args['limit'] = 100;
				$args['page']  = 1;
			} elseif ( '' !== $term ) {
				$args['s'] = $term;
			}
			$products = wc_get_products( $args );
			$count    = count( $products );
			foreach ( $products as $product ) {
				if ( ! $product instanceof \WC_Product || $product->is_type( 'variation' ) || 'publish' !== $product->get_status() || ! in_array( $product->get_catalog_visibility(), array( 'visible', 'catalog' ), true ) || ! is_post_publicly_viewable( $product->get_id() ) ) {
					continue;
				}
				$results[] = array(
					'id'   => (string) $product->get_id(),
					'text' => wp_strip_all_tags( $product->get_name() ) . ' (#' . $product->get_id() . ')',
				);
			}
		}
		wp_send_json_success(
			array(
				'results'    => $results,
				'pagination' => array( 'more' => ! $ids && 20 === $count ),
			)
		);
	}
}
