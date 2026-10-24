<?php
/**
 * Order table and shared legacy Elementor controls.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor List Widget.
 *
 * Elementor widget that displays WooCommerce order activity as a responsive table.
 *
 * @since 1.0.0
 */
class Oltew_Order_List_Table_Ele_Widget extends \Elementor\Widget_Base {

	use Oltew_Modern_Widget;

	/**
	 * Get the original widget identifier for saved-page compatibility.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'woo_recent_orders_table';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve list widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Order List Table (Modern)', 'order-list-table-elementor-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve list widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-table';
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget help URL.
	 */
	public function get_custom_help_url() {
		return 'https://wpmethods.com/contact';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the list widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'oltew-order-list-table' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the list widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'order', 'order table', 'woocommerce recent order', 'order list', 'woocommerce order list' );
	}


	/**
	 * Register list widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		// Table Content Tab.

		// Table Settings Section.
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Table Settings', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'select_status',
			array(
				'label'       => esc_html__( 'Select Status', 'order-list-table-elementor-widget' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => array(
					'wc-completed'  => esc_html__( 'Completed', 'order-list-table-elementor-widget' ),
					'wc-processing' => esc_html__( 'Processing', 'order-list-table-elementor-widget' ),
					'wc-on-hold'    => esc_html__( 'On hold', 'order-list-table-elementor-widget' ),
					'wc-failed'     => esc_html__( 'Failed', 'order-list-table-elementor-widget' ),
					'wc-cancelled'  => esc_html__( 'Cancelled', 'order-list-table-elementor-widget' ),
					'wc-refunded'   => esc_html__( 'Refunded', 'order-list-table-elementor-widget' ),
					'wc-pending'    => esc_html__( 'Pending payment', 'order-list-table-elementor-widget' ),

				),
				'default'     => array( 'wc-completed', 'wc-processing', 'wc-on-hold', 'wc-failed', 'wc-cancelled' ),
			)
		);

		$this->add_control(
			'list_per_page',
			array(
				'label'   => esc_html__( 'List Per Page', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 10,
				'min'     => 1,
				'max'     => 100,
			)
		);

		$this->add_control(
			'order_time_format',
			array(
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Select Order Time Format', 'order-list-table-elementor-widget' ),
				'options' => array(
					'ago'  => esc_html__( 'Ago', 'order-list-table-elementor-widget' ),
					'date' => esc_html__( 'Date/Month/YR TIME', 'order-list-table-elementor-widget' ),
				),
				'default' => 'ago',
			)
		);

		$this->add_control(
			'order_by',
			array(
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Order', 'order-list-table-elementor-widget' ),
				'options' => array(
					'asc'  => esc_html__( 'ASC', 'order-list-table-elementor-widget' ),
					'desc' => esc_html__( 'DESC', 'order-list-table-elementor-widget' ),
				),
				'default' => 'desc',
			)
		);

		$this->add_control(
			'order_list_mobile_style',
			array(
				'label'       => esc_html__( 'Responsive for Mobile View', 'order-list-table-elementor-widget' ),
				'description' => esc_html__( 'Display stacked cards on mobile. Disable to keep a horizontally scrollable table.', 'order-list-table-elementor-widget' ),
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);

		$this->end_controls_section();
		$this->register_modern_controls();

		// Table Header Title & Icon Section.

		$this->start_controls_section(
			'table_header',
			array(
				'label' => esc_html__( 'Table Header Title and Icons', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		// Customer Name.

		$this->add_control(
			'customer_name_heading',
			array(
				'label'     => esc_html__( 'Customer Name and Icon', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'customer_name',
			array(
				'label'   => esc_html__( 'Customer Name', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Customer',
			)
		);

		$this->add_control(
			'customer_th_icon',
			array(
				'label'   => esc_html__( 'Customer Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-users',
					'library' => 'fa-solid',
				),
			)
		);

		// Sell Time.
		$this->add_control(
			'sell_time_heading',
			array(
				'label'     => esc_html__( 'Sell Time and Icon', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'sell_time',
			array(
				'label'   => esc_html__( 'Sell Time', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Sell Time',
			)
		);

		$this->add_control(
			'sell_time_th_icon',
			array(
				'label'   => esc_html__( 'Sell Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-history',
					'library' => 'fa-solid',
				),
			)
		);

		// Order Status.
		$this->add_control(
			'order_status_heading',
			array(
				'label'     => esc_html__( 'Order Status and Icons', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'order_status',
			array(
				'label'   => esc_html__( 'Order Status', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Status',
			)
		);

		$this->add_control(
			'order_status_th_icon',
			array(
				'label'   => esc_html__( 'Order Status Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-fire',
					'library' => 'fa-solid',
				),
			)
		);

		// Customer Phone.
		$this->add_control(
			'customer_phone_heading',
			array(
				'label'     => esc_html__( 'Customer Phone', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'customer_phone',
			array(
				'label'   => esc_html__( 'Phone', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Phone',
			)
		);

		$this->add_control(
			'customer_phone_th_icon',
			array(
				'label'   => esc_html__( 'Customer Phone Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-phone',
					'library' => 'fa-solid',
				),
			)
		);

		// Amount.
		$this->add_control(
			'sell_amount_heading',
			array(
				'label'     => esc_html__( 'Sell Amount and Icon', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'sell_amount',
			array(
				'label'   => esc_html__( 'Sell Amount', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Amount',
			)
		);

		$this->add_control(
			'sell_amount_th_icon',
			array(
				'label'   => esc_html__( 'Amount Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-dollar-sign',
					'library' => 'fa-solid',
				),
			)
		);

		$this->end_controls_section();

		// Data List Icons Section.

		$this->start_controls_section(
			'data_icons',
			array(
				'label' => esc_html__( 'Data List Text & Icons', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		// Customer List Icon.
		$this->add_control(
			'customer_list_icon',
			array(
				'label'   => esc_html__( 'Customer Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-user',
					'library' => 'fa-solid',
				),
			)
		);

		// Sell List Icon.
		$this->add_control(
			'sell_list_time_icon',
			array(
				'label'   => esc_html__( 'Sell Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-clock',
					'library' => 'fa-solid',
				),
			)
		);

		// Customer Phone List Icon.
		$this->add_control(
			'customer_phone_list_icon',
			array(
				'label'   => esc_html__( 'Phone Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-phone',
					'library' => 'fa-solid',
				),
			)
		);

		// Start Order Status icons.

		$this->add_control(
			'order_status_icon_heading',
			array(
				'label'     => esc_html__( 'Order Status Icons', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		// Status Complted Icon.
		$this->add_control(
			'set_status_completed_icon',
			array(
				'label'   => esc_html__( 'Status Completed Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-check-circle',
					'library' => 'fa-solid',
				),
			)
		);

		// Status Processing Icon.
		$this->add_control(
			'set_status_processing_icon',
			array(
				'label'   => esc_html__( 'Status Processing Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-circle-notch',
					'library' => 'fa-solid',
				),
			)
		);

		// Status On hold Icon.
		$this->add_control(
			'set_status_onhold_icon',
			array(
				'label'   => esc_html__( 'Status On hold Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-pause',
					'library' => 'fa-solid',
				),
			)
		);

		// Status Faild or Canelled Icon.
		$this->add_control(
			'set_status_faild_icon',
			array(
				'label'   => esc_html__( 'Status Fail or Canelled Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-times',
					'library' => 'fa-solid',
				),
			)
		);

		// Status Refunded Icon.
		$this->add_control(
			'set_status_refunded_icon',
			array(
				'label'   => esc_html__( 'Status Refunded Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-undo',
					'library' => 'fa-solid',
				),
			)
		);

		$this->end_controls_section();

		// Prodcut Section.

		$this->start_controls_section(
			'product_section',
			array(
				'label' => esc_html__( 'Product Section', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'hide_product_name',
			array(
				'label'   => esc_html__( 'Hide/Show', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no',
			)
		);

		// Prodcut Heading Section.
		$this->add_control(
			'product_heading',
			array(
				'label'     => esc_html__( 'Product Heading', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		// Product Name.
		$this->add_control(
			'product_name',
			array(
				'label'   => esc_html__( 'Products', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Products',
			)
		);

		// Product Heading Icon.
		$this->add_control(
			'product_name_th_icon',
			array(
				'label'   => esc_html__( 'Product Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-shopping-cart',
					'library' => 'fa-solid',
				),
			)
		);

		// Product Body Section.

		$this->add_control(
			'product_body_section',
			array(
				'label'     => esc_html__( 'Product List Section', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		// Product Link Word Limit.
		$this->add_control(
			'product_link_words',
			array(
				'label'   => esc_html__( 'Link Word Limit', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 5,
				'min'     => 1,
				'max'     => 50,
			)
		);

		$this->add_control(
			'oltew_product_visual',
			array(
				'label'   => esc_html__( 'Product visual', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => array(
					'icon'  => esc_html__( 'Icon', 'order-list-table-elementor-widget' ),
					'image' => esc_html__( 'Product image', 'order-list-table-elementor-widget' ),
					'none'  => esc_html__( 'None', 'order-list-table-elementor-widget' ),
				),
			)
		);
		$this->add_control(
			'oltew_expand_products',
			array(
				'label'       => esc_html__( 'Collapse multiple products', 'order-list-table-elementor-widget' ),
				'description' => esc_html__( 'Show the first product and its action initially. Expand to reveal the remaining products and actions together. Disable to always show all products.', 'order-list-table-elementor-widget' ),
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);
		$this->add_control(
			'oltew_expand_text',
			array(
				'label'     => esc_html__( 'Expand label', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Show more', 'order-list-table-elementor-widget' ),
				'condition' => array( 'oltew_expand_products' => 'yes' ),
			)
		);
		$this->add_control(
			'oltew_collapse_text',
			array(
				'label'     => esc_html__( 'Collapse label', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Show less', 'order-list-table-elementor-widget' ),
				'condition' => array( 'oltew_expand_products' => 'yes' ),
			)
		);
		$this->add_control(
			'product_list_icon',
			array(
				'label'   => esc_html__( 'Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-shopping-bag',
					'library' => 'fa-solid',
				),
			)
		);

		$this->end_controls_section();

		// Buy Now Button Section.

		$this->start_controls_section(
			'buy_now_button_section',
			array(
				'label' => esc_html__( 'Buy Now Button', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'hide_buy_now',
			array(
				'label'   => esc_html__( 'Hide/Show', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no',
			)
		);

		// Buy Now Heading Section.
		$this->add_control(
			'buy_now_heading',
			array(
				'label'     => esc_html__( 'Buy Now Table Heading', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'buy_now',
			array(
				'label'   => esc_html__( 'Text', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Action',
			)
		);

		$this->add_control(
			'buy_now_th_icon',
			array(
				'label'   => esc_html__( 'Icon', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-cart-plus',
					'library' => 'fa-solid',
				),
			)
		);

		// Buy Now Body Section.
		// Buy Now button Section.
		$this->add_control(
			'buy_now_button_heading',
			array(
				'label'     => esc_html__( 'Buy Now List', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		// Buy Now button text.
		$this->add_control(
			'buy_now_button_text',
			array(
				'label'   => esc_html__( 'Text', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Buy Now',
			)
		);

		// Buy Now List Icon.
		$this->add_control(
			'buy_now_list_icon',
			array(
				'label' => esc_html__( 'Icon', 'order-list-table-elementor-widget' ),
				'type'  => \Elementor\Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'buy_now_list_icon_size',
			array(
				'label'      => esc_html__( 'Size', 'order-list-table-elementor-widget' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 15,
				),
				'selectors'  => array(
					'{{WRAPPER}} .oltew-order-list-table table td .buy_now_icon' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Table Column Hide & Show Section.

		$this->start_controls_section(
			'table_column_hide_show',
			array(
				'label' => esc_html__( 'Table Item Hide/Show', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'hide_order_sl',
			array(
				'label'   => esc_html__( 'Hide Order SL', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no',
			)
		);
		$this->add_control(
			'hide_customer_title',
			array(
				'label'   => esc_html__( 'Hide Customer Column', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no',
			)
		);

		$this->add_control(
			'hide_sell_time',
			array(
				'label'   => esc_html__( 'Hide Sell Time', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no',
			)
		);

		$this->add_control(
			'hide_status',
			array(
				'label'   => esc_html__( 'Hide Order Status', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no',
			)
		);

		$this->add_control(
			'hide_customer_phone',
			array(
				'label'   => esc_html__( 'Hide Customer Phone', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no',
			)
		);

		$this->add_control(
			'hide_sell_amount',
			array(
				'label'   => esc_html__( 'Hide Sell Amount', 'order-list-table-elementor-widget' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no',
			)
		);

		$this->end_controls_section();

		// Table Alignment Section.
		$this->start_controls_section(
			'oltew_table_alignment_settings',
			array(
				'label' => esc_html__( 'Table Alignment', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'oltew_table_th_alignment',
			array(
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'label'   => esc_html__( 'Table Header Title', 'order-list-table-elementor-widget' ),
				'options' => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'order-list-table-elementor-widget' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'order-list-table-elementor-widget' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'order-list-table-elementor-widget' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default' => 'left',
			)
		);

		$this->add_control(
			'oltew_table_td_alignment',
			array(
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'label'   => esc_html__( 'Table Body Text', 'order-list-table-elementor-widget' ),
				'options' => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'order-list-table-elementor-widget' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'order-list-table-elementor-widget' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'order-list-table-elementor-widget' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default' => 'left',
			)
		);

		$this->end_controls_section();

		// Table Style Tab.

		$this->start_controls_section(
			'table_style',
			array(
				'label' => esc_html__( 'Table Style', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		// Table Border.
		$this->add_control(
			'table_order_heading',
			array(
				'label'     => esc_html__( 'Table Style', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'table_border_style',
				'selector' => '{{WRAPPER}} .oltew-order-list-table table',
			)
		);

		// Table Padding.
		$this->add_responsive_control(
			'woltw_table_padding',
			array(
				'label'     => esc_html__( 'Table Padding', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table table,{{WRAPPER}} .oltew-order-list-table table th, {{WRAPPER}} .oltew-order-list-table table td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),

			)
		);

		$this->add_control(
			'table_heading_style',
			array(
				'label'     => esc_html__( 'Table Header Style', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'table_heading_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table th' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'table_heading_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'order-list-table-elementor-widget' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 15,
				),
				'selectors'  => array(
					'{{WRAPPER}} .oltew-order-list-table table th .order_list_icon' => 'width: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}} .oltew-order-list-table table th .order_list_icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'table_heading_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table th .order_list_icon, {{WRAPPER}} .oltew-order-list-table th .order_list_icon svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .oltew-order-list-table th .order_list_icon svg path' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'table_heading_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table thead' => 'background: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'table_heading_typography',
				'label'    => esc_html__( 'Typography', 'order-list-table-elementor-widget' ),
				'selector' => '{{WRAPPER}} .oltew-order-list-table th',
			)
		);

		// Table Th Border.
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'table_th_border_style',
				'selector' => '{{WRAPPER}} .oltew-order-list-table table th',
			)
		);

		// Table Body Style.
		$this->add_control(
			'table_body_style',
			array(
				'label'     => esc_html__( 'Table Body Style', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'table_body_text_color',
			array(
				'label'     => esc_html__( 'Text color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#334155',
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table table td' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'table_body_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'order-list-table-elementor-widget' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 15,
				),
				'selectors'  => array(
					'{{WRAPPER}} .oltew-order-list-table table td .order_list_icon, {{WRAPPER}} .oltew-order-list-table table td .order_list_icon svg' => 'width: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}} .oltew-order-list-table table td .order_list_icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'table_body_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table table td .order_list_icon, {{WRAPPER}} .oltew-order-list-table table td .order_list_icon svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .oltew-order-list-table table td .order_list_icon svg path' => 'fill: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'table_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table table' => 'background: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'table_body_typography',
				'label'    => esc_html__( 'Typography', 'order-list-table-elementor-widget' ),
				'selector' => '{{WRAPPER}} .oltew-order-list-table table td',
			)
		);

		$this->add_control(
			'tr_nth_child_even',
			array(
				'label'     => esc_html__( '<tr> Background (even)', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table table tr:nth-child(even)' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'tr_nth_child_odd',
			array(
				'label'     => esc_html__( '<tr> Background (odd)', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table table tr:nth-child(odd)' => 'background-color: {{VALUE}}',
				),
			)
		);

		// Table Tr Border.
		$this->add_control(
			'table_tr_border',
			array(
				'label'     => esc_html__( 'Table tr style', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'table_tr_border_style',
				'selector' => '{{WRAPPER}} .oltew-order-list-table table tr',
			)
		);

		// Table Td Border.
		$this->add_control(
			'table_td_border',
			array(
				'label'     => esc_html__( 'Table td style', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'table_td_border_style',
				'selector' => '{{WRAPPER}} .oltew-order-list-table table td',
			)
		);

		$this->end_controls_section();

		// Product Link Style Section.
		$this->start_controls_section(
			'product_link_style_section',
			array(
				'label' => esc_html__( 'Product List Link', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		// Product Link Style Tabs.

		$this->add_control(
			'product_link_style_heading',
			array(
				'label'     => esc_html__( 'Product Link Style', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs(
			'product_link_style_tabs'
		);

		// Product Link Normal.
		$this->start_controls_tab(
			'product_links_tyle_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'order-list-table-elementor-widget' ),
			)
		);

		// Product Link Color Normal.
		$this->add_control(
			'product_body_link_color',
			array(
				'label'     => esc_html__( 'Product link color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table table .product_list a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'product_link_typography',
				'label'    => esc_html__( 'Product Link Typography', 'order-list-table-elementor-widget' ),
				'selector' => '{{WRAPPER}} .oltew-order-list-table table .product_list',
			)
		);

		$this->end_controls_tab();

		// Product Link Hover.
		$this->start_controls_tab(
			'product_link_style_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'order-list-table-elementor-widget' ),
			)
		);

		// Product Link Color Hover.
		$this->add_control(
			'product_body_link_hover',
			array(
				'label'     => esc_html__( 'Product link color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table table .product_list a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// Buy now Button section Style.

		$this->start_controls_section(
			'buy_now_button_style',
			array(
				'label' => esc_html__( 'Buy Now Button', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs(
			'buy_now_style_tabs'
		);

		// Buy Now Link Normal.
		$this->start_controls_tab(
			'buy_now_style_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'order-list-table-elementor-widget' ),
			)
		);

		// Buy Now Link Color Normal.
		$this->add_control(
			'buy_now_link_color',
			array(
				'label'     => esc_html__( 'Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table table .buy_now a' => 'color: {{VALUE}}',
				),
			)
		);

		// Buy Now Icon Color Normal.
		$this->add_control(
			'buy_now_link_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table table .buy_now_icon, {{WRAPPER}} .oltew-order-list-table table .buy_now_icon svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .oltew-order-list-table table .buy_now_icon svg path' => 'fill: {{VALUE}}',
				),
			)
		);

		// Buy Now Background Color Normal.
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'buy_now_background_color',
				'types'    => array( 'classic', 'gradient', 'video' ),
				'selector' => '{{WRAPPER}} .oltew-order-list-table table .buy_now a',

			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'buy_now_link_typography',
				'label'    => esc_html__( 'Typography', 'order-list-table-elementor-widget' ),
				'selector' => '{{WRAPPER}} .oltew-order-list-table table .buy_now',
			)
		);

		$this->add_responsive_control(
			'buy_now_padding',
			array(
				'label'      => esc_html__( 'Padding', 'order-list-table-elementor-widget' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'default'    => array(
					'top'    => 5,
					'right'  => 10,
					'bottom' => 5,
					'left'   => 10,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .oltew-order-list-table table .buy_now a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),

			)
		);

		$this->add_responsive_control(
			'buy_now_margin',
			array(
				'label'      => esc_html__( 'Margin', 'order-list-table-elementor-widget' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'default'    => array(
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .oltew-order-list-table table .buy_now a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),

			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'buy_now_border_color',
				'selector' => '{{WRAPPER}} .oltew-order-list-table table .buy_now a',
			)
		);

		$this->add_responsive_control(
			'buy_now_border_radius',
			array(
				'label'      => esc_html__( 'Border radius', 'order-list-table-elementor-widget' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .oltew-order-list-table table .buy_now a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .oltew-orders' => '--oltew-buy-now-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),

			)
		);

		$this->end_controls_tab();

		// Buy Now Link Hover.

		$this->start_controls_tab(
			'buy_now_link_style_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'order-list-table-elementor-widget' ),
			)
		);

		// Buy Now Link Color Hover.
		$this->add_control(
			'buy_now_link_hover',
			array(
				'label'     => esc_html__( 'Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table table .buy_now a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'buy_now_background_hover_color',
				'types'    => array( 'classic', 'gradient', 'video' ),
				'selector' => '{{WRAPPER}} .oltew-order-list-table table .buy_now a:hover',

			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// Order Status Section.

		$this->start_controls_section(
			'order_status_style',
			array(
				'label' => esc_html__( 'Order Status Style', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		// Table Status Icon Size.

		$this->add_control(
			'table_status_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'order-list-table-elementor-widget' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 15,
				),
				'selectors'  => array(
					'{{WRAPPER}} .oltew-order-list-table table td .status_icon, {{WRAPPER}} .oltew-order-list-table table td .status_icon i' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		// completed style.
		$this->add_control(
			'table_status_completed_style',
			array(
				'label'     => esc_html__( 'Status Completed Style', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'table_status_list_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .st_completed' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'complete_status_list_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table table td .st_completed .status_icon, {{WRAPPER}} .oltew-order-list-table table td .st_completed .status_icon svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .oltew-order-list-table td .st_completed .status_icon svg path' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'complete_status_list_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .st_completed' => 'background: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'completed_padding',
			array(
				'label'     => esc_html__( 'Padding', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .st_completed' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),

			)
		);

		// Processing style.
		$this->add_control(
			'table_status_processing_style',
			array(
				'label'     => esc_html__( 'Status Processing Style', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'processing_status_list_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .st_processing' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'processing_status_list_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table td .st_processing .status_icon, {{WRAPPER}} .oltew-order-list-table td .st_processing .status_icon svg, {{WRAPPER}} .oltew-order-list-table td .st_processing .status_icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .oltew-order-list-table td .st_processing .status_icon svg path' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'processing_status_list_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .st_processing' => 'background: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'processing_padding',
			array(
				'label'     => esc_html__( 'Padding', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .st_processing' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),

			)
		);

		// On Hold style.
		$this->add_control(
			'table_status_on_hold_style',
			array(
				'label'     => esc_html__( 'Status On Hold', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'on_hold_status_list_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .st_on-hold' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'on_hold_status_list_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table table td .st_on-hold .status_icon, {{WRAPPER}} .oltew-order-list-table table td .st_on-hold .status_icon svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .oltew-order-list-table td .st_on-hold .status_icon svg path' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'on_hold_status_list_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .st_on-hold' => 'background: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'on_hold_padding',
			array(
				'label'     => esc_html__( 'Padding', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .st_on-hold' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),

			)
		);

		// Status Failed Style.
		$this->add_control(
			'table_status_failed_style',
			array(
				'label'     => esc_html__( 'Status Failed & Cancelled', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'failed_status_list_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .st_cancelled' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'failed_status_list_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .oltew-order-list-table table td .st_cancelled .status_icon, {{WRAPPER}} .oltew-order-list-table table td .st_cancelled .status_icon svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .oltew-order-list-table td .st_cancelled .status_icon svg path' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'failed_status_list_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .st_cancelled' => 'background: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'failed_padding',
			array(
				'label'     => esc_html__( 'Padding', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .st_cancelled' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),

			)
		);

		$this->end_controls_section();
		$this->register_load_more_style_controls();
	}


	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$this->render_modern_orders();
	}
}
