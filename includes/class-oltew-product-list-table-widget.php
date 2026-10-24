<?php
/**
 * WooCommerce product catalog table for Elementor.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Display public catalog products using the Modern Table design. */
class Oltew_Product_List_Table_Widget extends \Elementor\Widget_Base {
	use Oltew_Table_Design;

	/**
	 * Get widget identifier.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'oltew_product_list_table';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Product List Table', 'order-list-table-elementor-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-products';
	}

	/**
	 * Get the shared Elementor category.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'oltew-order-list-table' );
	}

	/**
	 * Get search keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'product', 'woocommerce', 'catalog', 'table', 'stock', 'sales' );
	}

	/**
	 * Load shared styles only on pages using the widget.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'order-list-table' );
	}

	/**
	 * Load the shared table pagination handler.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'oltew-table-pagination' );
	}

	/**
	 * Keep price, stock and sales values outside Elementor element caches.
	 *
	 * @return bool
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Define available columns and their default labels.
	 *
	 * @return array
	 */
	private function product_fields() {
		return array(
			'serial'     => esc_html__( 'SL', 'order-list-table-elementor-widget' ),
			'image'      => esc_html__( 'Image', 'order-list-table-elementor-widget' ),
			'title'      => esc_html__( 'Product', 'order-list-table-elementor-widget' ),
			'price'      => esc_html__( 'Price', 'order-list-table-elementor-widget' ),
			'categories' => esc_html__( 'Category', 'order-list-table-elementor-widget' ),
			'sales'      => esc_html__( 'Total Sales', 'order-list-table-elementor-widget' ),
			'stock'      => esc_html__( 'Stock', 'order-list-table-elementor-widget' ),
			'sku'        => esc_html__( 'SKU', 'order-list-table-elementor-widget' ),
			'rating'     => esc_html__( 'Rating', 'order-list-table-elementor-widget' ),
			'action'     => esc_html__( 'Action', 'order-list-table-elementor-widget' ),
		);
	}

	/** Register product-specific content and common design controls. */
	protected function register_controls() {
		$this->register_product_layout_controls();
		$this->start_controls_section( 'oltew_product_query', array( 'label' => esc_html__( 'Product Settings', 'order-list-table-elementor-widget' ) ) );
		$this->add_control(
			'products_limit',
			array(
				'label'   => esc_html__( 'Products per page', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 100,
				'default' => 10,
			)
		);
		$this->add_control(
			'products_source',
			array(
				'label'   => esc_html__( 'Product collection', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'all',
				'options' => array(
					'all'           => esc_html__( 'All products / custom sort', 'order-list-table-elementor-widget' ),
					'featured'      => esc_html__( 'Featured products', 'order-list-table-elementor-widget' ),
					'best_selling'  => esc_html__( 'Best selling', 'order-list-table-elementor-widget' ),
					'recent'        => esc_html__( 'Recent products', 'order-list-table-elementor-widget' ),
					'best_reviewed' => esc_html__( 'Best reviewed', 'order-list-table-elementor-widget' ),
				),
			)
		);
		$this->add_control(
			'products_orderby',
			array(
				'label'     => esc_html__( 'Sort by', 'order-list-table-elementor-widget' ),
				'condition' => array( 'products_source' => array( 'all', 'featured' ) ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => array(
					'date'       => esc_html__( 'Date', 'order-list-table-elementor-widget' ),
					'title'      => esc_html__( 'Title', 'order-list-table-elementor-widget' ),
					'ID'         => esc_html__( 'Product ID', 'order-list-table-elementor-widget' ),
					'menu_order' => esc_html__( 'Menu order', 'order-list-table-elementor-widget' ),
				),
			)
		);
		$this->add_control(
			'products_order',
			array(
				'label'     => esc_html__( 'Sort direction', 'order-list-table-elementor-widget' ),
				'condition' => array( 'products_source' => array( 'all', 'featured' ) ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => array(
					'ASC'  => esc_html__( 'Ascending', 'order-list-table-elementor-widget' ),
					'DESC' => esc_html__( 'Descending', 'order-list-table-elementor-widget' ),
				),
			)
		);
		$this->add_control(
			'products_categories',
			array(
				'label'        => esc_html__( 'Categories', 'order-list-table-elementor-widget' ),
				'description'  => esc_html__( 'Search and select multiple categories. Products from any selected category are eligible. Leave empty for all categories.', 'order-list-table-elementor-widget' ),
				'type'         => 'oltew_catalog_select',
				'catalog_type' => 'categories',
				'multiple'     => true,
				'label_block'  => true,
				'default'      => array(),
			)
		);
		$this->add_control(
			'products_ids',
			array(
				'label'        => esc_html__( 'Specific products', 'order-list-table-elementor-widget' ),
				'description'  => esc_html__( 'Search products by name and select one or more. These selections also respect the selected categories, collection and stock filters. Leave empty to use all matching products.', 'order-list-table-elementor-widget' ),
				'type'         => 'oltew_catalog_select',
				'catalog_type' => 'products',
				'multiple'     => true,
				'label_block'  => true,
				'default'      => array(),
			)
		);
		$this->add_control(
			'products_type',
			array(
				'label'   => esc_html__( 'Product type', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''         => esc_html__( 'All catalog product types', 'order-list-table-elementor-widget' ),
					'simple'   => esc_html__( 'Simple', 'order-list-table-elementor-widget' ),
					'variable' => esc_html__( 'Variable', 'order-list-table-elementor-widget' ),
					'grouped'  => esc_html__( 'Grouped', 'order-list-table-elementor-widget' ),
					'external' => esc_html__( 'External', 'order-list-table-elementor-widget' ),
				),
			)
		);
		$this->add_control(
			'products_stock',
			array(
				'label'       => esc_html__( 'Stock filter', 'order-list-table-elementor-widget' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''            => esc_html__( 'All stock statuses', 'order-list-table-elementor-widget' ),
					'instock'     => esc_html__( 'In stock', 'order-list-table-elementor-widget' ),
					'outofstock'  => esc_html__( 'Out of stock', 'order-list-table-elementor-widget' ),
					'onbackorder' => esc_html__( 'On backorder', 'order-list-table-elementor-widget' ),
				),
				'description' => esc_html__( 'WooCommerce catalog visibility and its hide-out-of-stock setting are respected.', 'order-list-table-elementor-widget' ),
			)
		);
		$this->add_control(
			'products_show_serial',
			array(
				'label'       => esc_html__( 'Show SL column', 'order-list-table-elementor-widget' ),
				'description' => esc_html__( 'Adds SL to older table configurations. Reorder it in Drag & Drop Columns, or disable it here.', 'order-list-table-elementor-widget' ),
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);
		$this->add_control(
			'products_sales_fire',
			array(
				'label'   => esc_html__( 'Show sales fire icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);
		$this->add_control(
			'products_animate_fire',
			array(
				'label'     => esc_html__( 'Animate fire', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array( 'products_sales_fire' => 'yes' ),
			)
		);
		$this->add_control(
			'products_fire_speed',
			array(
				'label'     => esc_html__( 'Fire animation duration (ms)', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 500,
				'max'       => 5000,
				'default'   => 1200,
				'selectors' => array( '{{WRAPPER}} .oltew-products' => '--oltew-fire-duration: {{VALUE}}ms;' ),
				'condition' => array(
					'products_animate_fire' => 'yes',
					'products_sales_fire'   => 'yes',
				),
			)
		);
		$this->add_control(
			'products_category_images',
			array(
				'label'   => esc_html__( 'Show category images', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);
		$this->add_control(
			'products_stock_quantity',
			array(
				'label'   => esc_html__( 'Show available stock quantity', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
			)
		);
		$this->add_control(
			'products_title_words',
			array(
				'label'       => esc_html__( 'Product title word limit', 'order-list-table-elementor-widget' ),
				'description' => esc_html__( 'Set 0 to show the full product title.', 'order-list-table-elementor-widget' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 100,
				'default'     => 5,
			)
		);
		$this->add_control(
			'products_buy_text',
			array(
				'label'   => esc_html__( 'Buy Now label', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Buy Now', 'order-list-table-elementor-widget' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'oltew_product_columns', array( 'label' => esc_html__( 'Drag & Drop Columns', 'order-list-table-elementor-widget' ) ) );
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'field',
			array(
				'label'   => esc_html__( 'Column', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->product_fields(),
				'default' => 'title',
			)
		);
		$repeater->add_control(
			'label',
			array(
				'label' => esc_html__( 'Custom heading', 'order-list-table-elementor-widget' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'enabled',
			array(
				'label'   => esc_html__( 'Show column', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);
		$defaults = array();
		foreach ( array_keys( $this->product_fields() ) as $field ) {
			$defaults[] = array(
				'field'   => $field,
				'label'   => '',
				'enabled' => in_array( $field, array( 'sku', 'rating' ), true ) ? '' : 'yes',
			);
		}
		$this->add_control(
			'products_columns',
			array(
				'label'         => esc_html__( 'Drag items to reorder', 'order-list-table-elementor-widget' ),
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => $defaults,
				'title_field'   => '{{{ field }}}',
				'prevent_empty' => false,
			)
		);
		$this->end_controls_section();

		$this->register_load_more_controls();
		$this->register_table_design_controls( false );
		$this->register_product_styles();
		$this->register_load_more_style_controls();
	}

	/** Register the first Content section for product table layout and effects. */
	private function register_product_layout_controls() {
		$this->start_controls_section( 'oltew_product_layout', array( 'label' => esc_html__( 'Layout & Effects', 'order-list-table-elementor-widget' ) ) );
		$this->add_control(
			'oltew_title',
			array(
				'label'   => esc_html__( 'Widget title', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Our products', 'order-list-table-elementor-widget' ),
			)
		);
		$this->add_control(
			'oltew_subtitle',
			array(
				'label'   => esc_html__( 'Subtitle', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Discover your next favorite', 'order-list-table-elementor-widget' ),
			)
		);
		foreach ( array(
			'oltew_show_heading'      => esc_html__( 'Show title & subtitle', 'order-list-table-elementor-widget' ),
			'oltew_show_count'        => esc_html__( 'Show displayed product count', 'order-list-table-elementor-widget' ),
			'oltew_hover'             => esc_html__( 'Enable hover effect', 'order-list-table-elementor-widget' ),
			'oltew_shadow'            => esc_html__( 'Enable shadow', 'order-list-table-elementor-widget' ),
			'oltew_dividers'          => esc_html__( 'Show dividers', 'order-list-table-elementor-widget' ),
			'order_list_mobile_style' => esc_html__( 'Stack rows on mobile', 'order-list-table-elementor-widget' ),
		) as $key => $label ) {
			$this->add_control(
				$key,
				array(
					'label'   => $label,
					'type'    => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'yes',
				)
			);
		}
		foreach ( array(
			'oltew_stripes'       => esc_html__( 'Alternating row backgrounds', 'order-list-table-elementor-widget' ),
			'oltew_sticky_header' => esc_html__( 'Sticky table header', 'order-list-table-elementor-widget' ),
		) as $key => $label ) {
			$this->add_control(
				$key,
				array(
					'label' => $label,
					'type'  => \Elementor\Controls_Manager::SWITCHER,
				)
			);
		}
		$this->add_control(
			'oltew_table_height',
			array(
				'label'     => esc_html__( 'Scroll height (px)', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 160,
				'max'       => 1600,
				'default'   => 480,
				'selectors' => array( '{{WRAPPER}} .oltew-sticky .oltew-scroll' => 'max-height: {{VALUE}}px;' ),
				'condition' => array( 'oltew_sticky_header' => 'yes' ),
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
			)
		);
		$this->add_control(
			'oltew_stagger',
			array(
				'label'   => esc_html__( 'Stagger rows', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);
		$this->add_control(
			'products_empty_text',
			array(
				'label'   => esc_html__( 'Empty state text', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'No products to display.', 'order-list-table-elementor-widget' ),
			)
		);
		$this->end_controls_section();
	}

	/** Add table, category, stock and purchase-button styling. */
	private function register_product_styles() {
		$this->start_controls_section(
			'product_table_styles',
			array(
				'label' => esc_html__( 'Table, Stock & Button', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		foreach ( array(
			'products_header_alignment' => array( esc_html__( 'Heading alignment', 'order-list-table-elementor-widget' ), 'th' ),
			'products_cell_alignment'   => array( esc_html__( 'Cell alignment', 'order-list-table-elementor-widget' ), 'td' ),
		) as $key => $alignment ) {
			$this->add_responsive_control(
				$key,
				array(
					'label'     => $alignment[0],
					'type'      => \Elementor\Controls_Manager::SELECT,
					'default'   => 'left',
					'options'   => array(
						'left'   => esc_html__( 'Left', 'order-list-table-elementor-widget' ),
						'center' => esc_html__( 'Center', 'order-list-table-elementor-widget' ),
						'right'  => esc_html__( 'Right', 'order-list-table-elementor-widget' ),
					),
					'selectors' => array( '{{WRAPPER}} .oltew-products .oltew-table ' . $alignment[1] => 'text-align: {{VALUE}};' ),
				)
			);
		}
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'products_table_border',
				'selector' => '{{WRAPPER}} .oltew-products .oltew-scroll',
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'products_button_border',
				'selector' => '{{WRAPPER}} .oltew-products .buy_now a',
			)
		);
		foreach ( array(
			'product_head_color'            => array( esc_html__( 'Heading color', 'order-list-table-elementor-widget' ), '.oltew-table th', 'color' ),
			'product_head_background'       => array( esc_html__( 'Heading background', 'order-list-table-elementor-widget' ), '.oltew-table thead', 'background' ),
			'product_body_color'            => array( esc_html__( 'Body color', 'order-list-table-elementor-widget' ), '.oltew-table td', 'color' ),
			'product_button_color'          => array( esc_html__( 'Button text', 'order-list-table-elementor-widget' ), '.buy_now a', 'color' ),
			'product_button_background'     => array( esc_html__( 'Button background', 'order-list-table-elementor-widget' ), '.buy_now a', 'background' ),
			'product_button_hover'          => array( esc_html__( 'Button hover background', 'order-list-table-elementor-widget' ), '.buy_now a:hover', 'background' ),
			'product_instock_color'         => array( esc_html__( 'In stock text', 'order-list-table-elementor-widget' ), '.oltew-stock-instock', 'color' ),
			'product_instock_background'    => array( esc_html__( 'In stock background', 'order-list-table-elementor-widget' ), '.oltew-stock-instock', 'background' ),
			'product_outofstock_color'      => array( esc_html__( 'Out of stock text', 'order-list-table-elementor-widget' ), '.oltew-stock-outofstock', 'color' ),
			'product_outofstock_background' => array( esc_html__( 'Out of stock background', 'order-list-table-elementor-widget' ), '.oltew-stock-outofstock', 'background' ),
			'product_backorder_color'       => array( esc_html__( 'Backorder text', 'order-list-table-elementor-widget' ), '.oltew-stock-onbackorder', 'color' ),
			'product_backorder_background'  => array( esc_html__( 'Backorder background', 'order-list-table-elementor-widget' ), '.oltew-stock-onbackorder', 'background' ),
			'product_sales_color'           => array( esc_html__( 'Sales count color', 'order-list-table-elementor-widget' ), '.oltew-sales-meta', 'color' ),
			'product_sales_background'      => array( esc_html__( 'Sales badge background', 'order-list-table-elementor-widget' ), '.oltew-sales-meta', 'background' ),
		) as $key => $style ) {
			$selectors = array( '{{WRAPPER}} .oltew-products ' . $style[1] => $style[2] . ': {{VALUE}};' );
			if ( 'product_body_color' === $key ) {
				$selectors['{{WRAPPER}} .oltew-products'] = '--oltew-product-link-color: {{VALUE}};';
			}
			$this->add_control(
				$key,
				array(
					'label'     => $style[0],
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => $selectors,
				)
			);
		}
		foreach ( array(
			'product_price_color'         => array( esc_html__( 'Product price color', 'order-list-table-elementor-widget' ), '--oltew-price-color' ),
			'product_regular_price_color' => array( esc_html__( 'Del Color', 'order-list-table-elementor-widget' ), '--oltew-regular-price-color' ),
		) as $key => $style ) {
			$this->add_control(
				$key,
				array(
					'label'     => $style[0],
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .oltew-products .oltew-product-price' => $style[1] . ': {{VALUE}};' ),
				)
			);
		}
		foreach ( array(
			'product_heading_type' => array( esc_html__( 'Heading typography', 'order-list-table-elementor-widget' ), 'th' ),
			'product_body_type'    => array( esc_html__( 'Body typography', 'order-list-table-elementor-widget' ), 'td' ),
			'product_button_type'  => array( esc_html__( 'Button typography', 'order-list-table-elementor-widget' ), '.buy_now a' ),
		) as $key => $style ) {
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => $key,
					'label'    => $style[0],
					'selector' => '{{WRAPPER}} .oltew-products ' . $style[1],
				)
			);
		}
		$this->add_control(
			'products_fire_color',
			array(
				'label'     => esc_html__( 'Fire color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .oltew-products' => '--oltew-fire-color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'products_fire_size',
			array(
				'label'      => esc_html__( 'Fire icon size', 'order-list-table-elementor-widget' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 40,
					),
				),
				'default'    => array(
					'size' => 17,
					'unit' => 'px',
				),
				'selectors'  => array( '{{WRAPPER}} .oltew-products' => '--oltew-fire-size: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'products_category_size',
			array(
				'label'      => esc_html__( 'Category image size', 'order-list-table-elementor-widget' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 16,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 28,
					'unit' => 'px',
				),
				'selectors'  => array( '{{WRAPPER}} .oltew-products' => '--oltew-category-size: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'product_title_styles',
			array(
				'label' => esc_html__( 'Product & Category Titles', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		foreach ( array(
			'product_title_color'          => array( esc_html__( 'Product title color', 'order-list-table-elementor-widget' ), '.oltew-product-title a' ),
			'product_title_hover_color'    => array( esc_html__( 'Product title hover color', 'order-list-table-elementor-widget' ), '.oltew-product-title a:hover, {{WRAPPER}} .oltew-products .oltew-product-title a:focus-visible' ),
			'product_category_color'       => array( esc_html__( 'Category title color', 'order-list-table-elementor-widget' ), '.oltew-category-link' ),
			'product_category_hover_color' => array( esc_html__( 'Category title hover color', 'order-list-table-elementor-widget' ), '.oltew-category-link:hover, {{WRAPPER}} .oltew-products .oltew-category-link:focus-visible' ),
		) as $key => $style ) {
			$this->add_control(
				$key,
				array(
					'label'     => $style[0],
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .oltew-products ' . $style[1] => 'color: {{VALUE}};' ),
				)
			);
		}
		$this->end_controls_section();
	}

	/**
	 * Validate, deduplicate and order product columns.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	private function visible_columns( $settings ) {
		$available  = $this->product_fields();
		$columns    = array();
		$has_serial = false;
		foreach ( (array) ( $settings['products_columns'] ?? array() ) as $row ) {
			$key = isset( $row['field'] ) && is_string( $row['field'] ) ? $row['field'] : '';
			if ( 'serial' === $key ) {
				$has_serial = true;
			}
			if ( ! isset( $available[ $key ] ) || isset( $columns[ $key ] ) || 'yes' !== ( $row['enabled'] ?? 'yes' ) ) {
				continue;
			}
			$columns[ $key ] = ! empty( $row['label'] ) ? $row['label'] : $available[ $key ];
		}
		if ( 'yes' !== ( $settings['products_show_serial'] ?? 'yes' ) ) {
			unset( $columns['serial'] );
		} elseif ( ! $has_serial && $columns ) {
			$columns = array( 'serial' => $available['serial'] ) + $columns;
		}
		return $columns;
	}

	/**
	 * Use WooCommerce lookup-table sorting only for this product query.
	 *
	 * @param array  $args Product query arguments.
	 * @param string $source Collection preset.
	 * @return array|object
	 */
	private function query_products( $args, $source ) {
		if ( ! in_array( $source, array( 'best_selling', 'best_reviewed' ), true ) ) {
			return wc_get_products( $args );
		}
		$token                      = wp_unique_id( 'oltew-sort-' );
		$args['oltew_catalog_sort'] = $token;
		$mark_query                 = function ( $wp_args, $query_vars ) use ( $token ) {
			if ( ( $query_vars['oltew_catalog_sort'] ?? '' ) === $token ) {
				$wp_args['oltew_catalog_sort'] = $token;
			}
			return $wp_args;
		};
		$sort_query                 = function ( $clauses, $query ) use ( $token, $source ) {
			if ( $token !== $query->get( 'oltew_catalog_sort' ) ) {
				return $clauses;
			}
			return 'best_selling' === $source ? WC()->query->order_by_popularity_post_clauses( $clauses ) : WC()->query->order_by_rating_post_clauses( $clauses );
		};
		add_filter( 'woocommerce_product_data_store_cpt_get_products_query', $mark_query, 20, 2 );
		add_filter( 'posts_clauses', $sort_query, 20, 2 );
		try {
			return wc_get_products( $args );
		} finally {
			remove_filter( 'woocommerce_product_data_store_cpt_get_products_query', $mark_query, 20 );
			remove_filter( 'posts_clauses', $sort_query, 20 );
		}
	}

	/** Render only published, visible WooCommerce catalog products. */
	protected function render() {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return;
		}
		$settings = $this->get_settings_for_display();
		$columns  = $this->visible_columns( $settings );
		$source   = $settings['products_source'] ?? 'all';
		$args     = array(
			'status'     => 'publish',
			'visibility' => 'catalog',
			'limit'      => max( 1, min( 100, absint( $settings['products_limit'] ?? 10 ) ) ),
			'orderby'    => in_array( $settings['products_orderby'] ?? '', array( 'date', 'title', 'ID', 'menu_order' ), true ) ? $settings['products_orderby'] : 'date',
			'order'      => 'ASC' === ( $settings['products_order'] ?? '' ) ? 'ASC' : 'DESC',
			'return'     => 'objects',
			'paginate'   => 'yes' === ( $settings['oltew_load_more'] ?? 'yes' ),
			// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Signed, size-bounded consumed IDs prevent duplicates when live sales/rating ranks change.
			'exclude'    => $this->oltew_seen_ids(),
		);
		if ( ! empty( $settings['products_categories'] ) ) {
			$categories       = is_array( $settings['products_categories'] ) ? $settings['products_categories'] : explode( ',', (string) $settings['products_categories'] );
			$args['category'] = array_values( array_unique( array_filter( array_map( 'sanitize_title', array_filter( $categories, 'is_scalar' ) ) ) ) );
		}
		if ( ! empty( $settings['products_ids'] ) ) {
			$selected        = is_array( $settings['products_ids'] ) ? $settings['products_ids'] : explode( ',', (string) $settings['products_ids'] );
			$args['include'] = array_slice( array_values( array_unique( array_filter( array_map( 'absint', array_filter( $selected, 'is_scalar' ) ) ) ) ), 0, 100 );
			if ( ! $args['include'] ) {
				$args['include'] = array( 0 );
			}
		}
		if ( 'featured' === $source ) {
			$args['featured'] = true;
		} elseif ( in_array( $source, array( 'best_selling', 'best_reviewed', 'recent' ), true ) ) {
			$args['orderby'] = 'recent' === $source ? 'date' : 'ID';
			$args['order']   = 'DESC';
		}
		if ( in_array( $settings['products_type'] ?? '', array( 'simple', 'variable', 'grouped', 'external' ), true ) ) {
			$args['type'] = $settings['products_type'];
		}
		if ( in_array( $settings['products_stock'] ?? '', array( 'instock', 'outofstock', 'onbackorder' ), true ) ) {
			$args['stock_status'] = $settings['products_stock'];
		} elseif ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$args['stock_status'] = array( 'instock', 'onbackorder' );
		}
		$products = $this->oltew_page_result(
			$columns ? $this->query_products( $args, $source ) : array(),
			'products',
			$args['limit'],
			function ( $product ) {
				return $product instanceof \WC_Product && ! $product->is_type( 'variation' ) && 'publish' === $product->get_status() && in_array( $product->get_catalog_visibility(), array( 'visible', 'catalog' ), true ) && is_post_publicly_viewable( $product->get_id() ) && $product->is_visible();
			}
		);
		if ( $this->oltew_is_append() ) {
			$this->render_product_rows( $products, $columns, $settings );
			return;
		}
		$classes = array( 'oltew-orders', 'oltew-products', 'oltew-order-list-table', 'oltew-layout-table' );
		foreach ( array(
			'oltew_hover'             => 'oltew-hover',
			'oltew_shadow'            => 'oltew-shadow',
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
		if ( in_array( $settings['oltew_animation'] ?? '', array( 'fade', 'rise', 'scale' ), true ) ) {
			$classes[] = 'oltew-animate-' . $settings['oltew_animation'];
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php if ( 'yes' === ( $settings['oltew_show_heading'] ?? '' ) ) : ?>
				<div class="oltew-toolbar"><div><h3 class="oltew-title"><?php echo esc_html( $settings['oltew_title'] ?? '' ); ?></h3>
				<?php
				if ( ! empty( $settings['oltew_subtitle'] ) ) :
					?>
					<p class="oltew-subtitle"><?php echo esc_html( $settings['oltew_subtitle'] ); ?></p><?php endif; ?></div>
				<?php
				if ( 'yes' === ( $settings['oltew_show_count'] ?? '' ) ) :
					?>
					<span class="oltew-count">
					<?php
					/* translators: %s: Number of products displayed. */
					echo esc_html( sprintf( _n( '%s product', '%s products', count( $products ), 'order-list-table-elementor-widget' ), number_format_i18n( count( $products ) ) ) );
					?>
				</span><?php endif; ?></div>
			<?php endif; ?>
			<?php if ( ! $products && ! $this->oltew_has_more() ) : ?>
				<div class="oltew-empty oltew-surface" role="status"><?php echo esc_html( $settings['products_empty_text'] ?? __( 'No products to display.', 'order-list-table-elementor-widget' ) ); ?></div>
			<?php else : ?>
				<div class="oltew-scroll oltew-surface" role="region" tabindex="0" aria-label="<?php esc_attr_e( 'Product list table', 'order-list-table-elementor-widget' ); ?>">
					<table class="oltew-table"><caption class="oltew-screen-reader"><?php echo esc_html( $settings['oltew_title'] ?? __( 'Products', 'order-list-table-elementor-widget' ) ); ?></caption><thead><tr>
						<?php
						foreach ( $columns as $label ) :
							?>
							<th scope="col"><?php echo esc_html( $label ); ?></th><?php endforeach; ?>
					</tr></thead><tbody>
						<?php $this->render_product_rows( $products, $columns, $settings ); ?>
					</tbody></table>
				</div>
			<?php endif; ?>
			<?php $this->oltew_render_load_more( $settings ); ?>
		</div>
		<?php
	}

	/**
	 * Render a batch of rows for initial and Load More requests.
	 *
	 * @param array $products Product objects.
	 * @param array $columns Visible columns.
	 * @param array $settings Widget settings.
	 */
	private function render_product_rows( $products, $columns, $settings ) {
		?>
						<?php foreach ( $products as $index => $product ) : ?>
							<tr class="oltew-item" style="--oltew-index: <?php echo absint( 'yes' === ( $settings['oltew_stagger'] ?? '' ) ? min( $index, 10 ) : 0 ); ?>;">
								<?php
								foreach ( $columns as $key => $label ) :
									?>
									<td class="<?php echo esc_attr( 'action' === $key ? 'buy_now' : 'oltew-product-' . $key ); ?>" data-column="<?php echo esc_attr( $label ); ?>"><span class="oltew-product-cell-value"><?php $this->render_product_value( $key, $product, $settings, $this->oltew_serial_offset() + $index ); ?></span></td><?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
		<?php
	}

	/**
	 * Render a catalog field, escaping at its output boundary.
	 *
	 * @param string      $key Column key.
	 * @param \WC_Product $product Product object.
	 * @param array       $settings Widget settings.
	 * @param int         $index Displayed product position.
	 */
	private function render_product_value( $key, $product, $settings, $index ) {
		switch ( $key ) {
			case 'serial':
				echo esc_html( number_format_i18n( $index + 1 ) );
				break;
			case 'image':
				echo '<a href="' . esc_url( $product->get_permalink() ) . '" aria-label="' . esc_attr( $product->get_name() ) . '">' . wp_kses_post(
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
				) . '</a>';
				break;
			case 'title':
				$full_title = wp_strip_all_tags( $product->get_name() );
				$word_limit = max( 0, min( 100, (int) ( $settings['products_title_words'] ?? 5 ) ) );
				$title      = $word_limit ? wp_trim_words( $full_title, $word_limit, '…' ) : $full_title;
				echo '<a href="' . esc_url( $product->get_permalink() ) . '" title="' . esc_attr( $full_title ) . '" aria-label="' . esc_attr( $full_title ) . '">' . esc_html( $title ) . '</a>';
				break;
			case 'price':
				echo '<span class="oltew-price-value' . ( $product->is_type( 'variable' ) ? ' oltew-variable-price' : '' ) . '">' . wp_kses_post( $product->get_price_html() ) . '</span>';
				break;
			case 'categories':
				$this->render_product_categories( $product, $settings );
				break;
			case 'sales':
				echo '<span class="oltew-sales-meta">';
				if ( 'yes' === ( $settings['products_sales_fire'] ?? 'yes' ) ) {
					echo '<span class="oltew-sales-fire' . ( 'yes' === ( $settings['products_animate_fire'] ?? 'yes' ) ? ' oltew-fire-animated' : '' ) . '" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false" fill="currentColor"><path d="M13.4 2c.5 3.7-2.8 5.2-2.2 8.1-1.6-.5-2.5-1.8-2.8-3.2C5.7 9.2 4 12 4 15a8 8 0 0 0 16 0c0-4.6-3-9.2-6.6-13ZM12 21c-2 0-3.5-1.4-3.5-3.2 0-1.5.9-2.8 2.2-4 .1 1.1.6 1.9 1.4 2.3-.3-1.7 1-2.7 1.2-4.3 1.6 1.9 2.2 4 2.2 6 0 1.8-1.5 3.2-3.5 3.2Z"/></svg></span>';
				}
				echo '<span class="oltew-sales-count">' . esc_html( number_format_i18n( max( 0, (int) $product->get_total_sales() ) ) ) . '</span></span>';
				break;
			case 'stock':
				$stock  = $product->get_stock_status();
				$labels = array(
					'instock'     => __( 'In stock', 'order-list-table-elementor-widget' ),
					'outofstock'  => __( 'Out of stock', 'order-list-table-elementor-widget' ),
					'onbackorder' => __( 'On backorder', 'order-list-table-elementor-widget' ),
				);
				echo '<span class="status_odd oltew-stock-' . esc_attr( sanitize_html_class( $stock ) ) . '">' . esc_html( $labels[ $stock ] ?? $stock );
				if ( 'yes' === ( $settings['products_stock_quantity'] ?? '' ) && $product->managing_stock() && null !== $product->get_stock_quantity() ) {
					echo ' <span class="oltew-stock-quantity">' . esc_html( number_format_i18n( max( 0, $product->get_stock_quantity() ) ) ) . '</span>';
				}
				echo '</span>';
				break;
			case 'sku':
				echo esc_html( $product->get_sku() ? $product->get_sku() : '—' );
				break;
			case 'rating':
				/* translators: %s: Average rating out of five. */
				echo esc_html( sprintf( __( '%s / 5', 'order-list-table-elementor-widget' ), number_format_i18n( $product->get_average_rating(), 1 ) ) );
				break;
			case 'action':
				if ( ! $product->is_in_stock() || ( $product->is_type( 'simple' ) && ! $product->is_purchasable() ) ) {
					echo '<span class="oltew-unavailable">' . esc_html__( 'Unavailable', 'order-list-table-elementor-widget' ) . '</span>';
					break;
				}
				$url   = $product->is_type( 'simple' ) ? add_query_arg(
					array(
						'add-to-cart' => $product->get_id(),
						'quantity'    => 1,
					),
					wc_get_checkout_url()
				) : $product->get_permalink();
				$label = $product->is_type( array( 'simple', 'variable' ) ) ? ( $settings['products_buy_text'] ?? __( 'Buy Now', 'order-list-table-elementor-widget' ) ) : __( 'View product', 'order-list-table-elementor-widget' );
				echo '<a class="buy-now-button" href="' . esc_url( $url ) . '" aria-label="' . esc_attr( $label . ': ' . $product->get_name() ) . '">' . esc_html( $label ) . '</a>';
				break;
		}
	}

	/**
	 * Render category thumbnails and names linking to their archive pages.
	 *
	 * @param \WC_Product $product Product object.
	 * @param array       $settings Widget settings.
	 */
	private function render_product_categories( $product, $settings ) {
		$rendered = false;
		echo '<span class="oltew-category-list">';
		foreach ( $product->get_category_ids() as $term_id ) {
			$term = get_term( $term_id, 'product_cat' );
			if ( ! $term || is_wp_error( $term ) ) {
				continue;
			}
			$url = get_term_link( $term );
			if ( is_wp_error( $url ) ) {
				continue;
			}
			echo '<a class="oltew-category-link" href="' . esc_url( $url ) . '">';
			if ( 'yes' === ( $settings['products_category_images'] ?? 'yes' ) ) {
				$image = wp_get_attachment_image(
					absint( get_term_meta( $term_id, 'thumbnail_id', true ) ),
					'thumbnail',
					false,
					array(
						'class'           => 'oltew-category-thumb',
						'alt'             => '',
						'loading'         => 'lazy',
						'data-cs-no-lazy' => '1',
					)
				);
				if ( $image ) {
					echo wp_kses_post( $image );
				} else {
					echo '<img class="oltew-category-thumb" src="' . esc_url( wc_placeholder_img_src( 'woocommerce_thumbnail' ) ) . '" alt="" width="28" height="28" loading="lazy" data-cs-no-lazy="1">';
				}
			}
			echo '<span>' . esc_html( $term->name ) . '</span></a>';
			$rendered = true;
		}
		if ( ! $rendered ) {
			echo '—';
		}
		echo '</span>';
	}
}
