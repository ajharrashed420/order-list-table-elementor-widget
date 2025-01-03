<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor List Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Oltew_Order_List_table_Ele_Widget extends \Elementor\Widget_Base
{

	public function get_name()
	{
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
	public function get_title()
	{
		return esc_html__('Order List Table', 'oltew-order-list-table-ele');
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
	public function get_icon()
	{
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
	public function get_custom_help_url()
	{
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
	public function get_categories()
	{
		return ['general'];
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
	public function get_keywords()
	{
		return ['order', 'order table', 'woocommerce recent order', 'order list', 'woocommerce order list'];
	}


	/**
	 * Register list widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls()
	{

		/////////////Table Content Tab//////////////////

		//Table Settings Section
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__('Table Settings', 'oltew-order-list-table-ele'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);


		$this->add_control(
			'select_status',
			[
				'label' => esc_html__('Select Status', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'wc-completed' => esc_html__('Completed', 'oltew-order-list-table-ele'),
					'wc-processing' => esc_html__('Processing', 'oltew-order-list-table-ele'),
					'wc-on-hold' => esc_html__('On hold', 'oltew-order-list-table-ele'),
					'wc-failed' => esc_html__('Failed', 'oltew-order-list-table-ele'),
					'wc-cancelled' => esc_html__('Cancelled', 'oltew-order-list-table-ele'),

				],
				'default' => ['wc-completed', 'wc-processing', 'wc-on-hold', 'wc-failed', 'wc-cancelled'],
			]
		);

		$this->add_control(
			'list_per_page',
			[
				'label' => esc_html__('List Per Page', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => '10',
			]
		);



		$this->add_control(
			'order_time_format',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => esc_html__('Select Order Time Format', 'oltew-order-list-table-ele'),
				'options' => [
					'ago' => esc_html__('Ago', 'oltew-order-list-table-ele'),
					'date' => esc_html__('Date/Month/YR TIME', 'oltew-order-list-table-ele'),
				],
				'default' => 'ago',
			]
		);
		
		$this->add_control(
			'order_by',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => esc_html__('Order', 'oltew-order-list-table-ele'),
				'options' => [
					'asc' => esc_html__('ASC', 'oltew-order-list-table-ele'),
					'desc' => esc_html__('DESC', 'oltew-order-list-table-ele'),
				],
				'default' => 'desc',
			]
		);


		$this->add_control(
			'order_list_mobile_style',
			[
				'label' => esc_html__('Responsive for Mobile View', 'oltew-order-list-table-ele'),
				'description' => esc_html__('Enable/Disable Responsive Mobile View Table Style. Default full width table like dasktop mode', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no'
			]
		);


		$this->end_controls_section();


		// Table Header Title & Icon Section//
		////////////////////////////////////
		$this->start_controls_section(
			'table_header',
			[
				'label' => esc_html__('Table Header Title and Icons', 'oltew-order-list-table-ele'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		//Customer Name

		$this->add_control(
			'customer_name_heading',
			[
				'label' => esc_html__('Customer Name and Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);
		$this->add_control(
			'customer_name',
			[
				'label' => esc_html__('Customer Name', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Customer',
			]
		);

		$this->add_control(
			'customer_th_icon',
			[
				'label' => esc_html__('Customer Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-users',
					'library' => 'solid',
				],
			]
		);

		//Sell Time
		$this->add_control(
			'sell_time_heading',
			[
				'label' => esc_html__('Sell Time and Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'sell_time',
			[
				'label' => esc_html__('Sell Time', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Sell Time',
			]
		);

		$this->add_control(
			'sell_time_th_icon',
			[
				'label' => esc_html__('Sell Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-history',
					'library' => 'solid',
				],
			]
		);

		//Order Status
		$this->add_control(
			'order_status_heading',
			[
				'label' => esc_html__('Order Status and Icons', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);
		$this->add_control(
			'order_status',
			[
				'label' => esc_html__('Order Status', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Status',
			]
		);

		$this->add_control(
			'order_status_th_icon',
			[
				'label' => esc_html__('Order Status Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-fire',
					'library' => 'solid',
				],
			]
		);


		//Customer Phone
		$this->add_control(
			'customer_phone_heading',
			[
				'label' => esc_html__('Customer Phone', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);
		$this->add_control(
			'customer_phone',
			[
				'label' => esc_html__('Phone', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Phone',
			]
		);

		$this->add_control(
			'customer_phone_th_icon',
			[
				'label' => esc_html__('Customer Phone Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-phone',
					'library' => 'solid',
				],
			]
		);

		//Amount
		$this->add_control(
			'sell_amount_heading',
			[
				'label' => esc_html__('Sell Amount and Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);
		$this->add_control(
			'sell_amount',
			[
				'label' => esc_html__('Sell Amount', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Amount',
			]
		);

		$this->add_control(
			'sell_amount_th_icon',
			[
				'label' => esc_html__('Amount Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-dollar',
					'library' => 'solid',
				],
			]
		);

		$this->end_controls_section();




		//Data List Icons Section////
		/////////////////////////////
		$this->start_controls_section(
			'data_icons',
			[
				'label' => esc_html__('Data List Text & Icons', 'oltew-order-list-table-ele'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		//Customer List Icon
		$this->add_control(
			'customer_list_icon',
			[
				'label' => esc_html__('Customer Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-user',
					'library' => 'solid',
				],
			]
		);

		//Sell List Icon
		$this->add_control(
			'sell_list_time_icon',
			[
				'label' => esc_html__('Sell Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-clock-o',
					'library' => 'solid',
				],
			]
		);
		

		//Customer Phone List Icon
		$this->add_control(
			'customer_phone_list_icon',
			[
				'label' => esc_html__('Phone Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-phone',
					'library' => 'solid',
				],
			]
		);

		
		
		
		//Start Order Status icons//
		///////////////////////////
		$this->add_control(
			'order_status_icon_heading',
			[
				'label' => esc_html__( 'Order Status Icons', 'oltew-order-list-table-ele' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);


		//Status Complted Icon
		$this->add_control(
			'set_status_completed_icon',
			[
				'label' => esc_html__('Status Completed Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-check-circle',
					'library' => 'solid',
				],
			]
		);


		//Status Processing Icon
		$this->add_control(
			'set_status_processing_icon',
			[
				'label' => esc_html__('Status Processing Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-hourglass',
					'library' => 'solid',
				],
			]
		);
		

		//Status On hold Icon
		$this->add_control(
			'set_status_onhold_icon',
			[
				'label' => esc_html__('Status On hold Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-pause',
					'library' => 'solid',
				],
			]
		);


		//Status Faild or Canelled Icon
		$this->add_control(
			'set_status_faild_icon',
			[
				'label' => esc_html__('Status Fail or Canelled Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-times',
					'library' => 'solid',
				],
			]
		);
		
		
		
		$this->end_controls_section();



		///Prodcut Section////////////
		/////////////////////////////
		$this->start_controls_section(
			'product_section',
			[
				'label' => esc_html__('Product Section', 'oltew-order-list-table-ele'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'hide_product_name',
			[
				'label' => esc_html__('Hide/Show', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no'
			]
		);


		//Prodcut Heading Section//
		$this->add_control(
			'product_heading',
			[
				'label' => esc_html__('Product Heading', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);


		//Product Name
		$this->add_control(
			'product_name',
			[
				'label' => esc_html__('Products', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Products',
			]
		);



		//Product Heading Icon
		$this->add_control(
			'product_name_th_icon',
			[
				'label' => esc_html__('Product Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-shopping-cart',
					'library' => 'solid',
				],
			]
		);


		///Product Body Section///

		$this->add_control(
			'product_body_section',
			[
				'label' => esc_html__('Product List Section', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		//Product Link Word Limit
		$this->add_control(
			'product_link_words',
			[
				'label' => esc_html__('Link Word Limit', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 5,
			]
		);

		$this->add_control(
			'product_list_icon',
			[
				'label' => esc_html__('Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-shopping-bag',
					'library' => 'solid',
				],
			]
		);
		


		$this->end_controls_section();






		///Buy Now Button Section////
		/////////////////////////////
		$this->start_controls_section(
			'buy_now_button_section',
			[
				'label' => esc_html__('Buy Now Button', 'oltew-order-list-table-ele'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'hide_buy_now',
			[
				'label' => esc_html__('Hide/Show', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no'
			]
		);


		//Buy Now Heading Section///
		$this->add_control(
			'buy_now_heading',
			[
				'label' => esc_html__('Buy Now Table Heading', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);
		$this->add_control(
			'buy_now',
			[
				'label' => esc_html__('Text', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Action',
			]
		);

		$this->add_control(
			'buy_now_th_icon',
			[
				'label' => esc_html__('Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-cart-plus',
					'library' => 'solid',
				],
			]
		);


		///Buy Now Body Section///
		//Buy Now button Section
		$this->add_control(
			'buy_now_button_heading',
			[
				'label' => esc_html__('Buy Now List', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);
		
		//Buy Now button text
		$this->add_control(
			'buy_now_button_text',
			[
				'label' => esc_html__('Text', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Buy Now',
			]
		);
		
		
		//Buy Now List Icon
		$this->add_control(
			'buy_now_list_icon',
			[
				'label' => esc_html__('Icon', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'buy_now_list_icon_size',
			[
				'label' => esc_html__( 'Size', 'oltew-order-list-table-ele' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table td .buy_now_icon' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_section();




		//////////////Table Column Hide & Show Section/////////////	
		///////////////////////////////////////////////////////////
		$this->start_controls_section(
			'table_column_hide_show',
			[
				'label' => esc_html__('Table Item Hide/Show', 'oltew-order-list-table-ele'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'hide_order_sl',
			[
				'label' => esc_html__('Hide Order SL', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);
		$this->add_control(
			'hide_customer_title',
			[
				'label' => esc_html__('Hide Customer Column', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no'
			]
		);

		$this->add_control(
			'hide_sell_time',
			[
				'label' => esc_html__('Hide Sell Time', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no'
			]
		);

		$this->add_control(
			'hide_status',
			[
				'label' => esc_html__('Hide Order Status', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no'
			]
		);

		

		$this->add_control(
			'hide_customer_phone',
			[
				'label' => esc_html__('Hide Customer Phone', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'hide_sell_amount',
			[
				'label' => esc_html__('Hide Sell Amount', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no'
			]
		);


		$this->end_controls_section();
		
		///////Table Alignment Section//////
		$this->start_controls_section(
			'oltew_table_alignment_settings',
			[
				'label' => esc_html__('Table Alignment', 'oltew-order-list-table-ele'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'oltew_table_th_alignment',
			[
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'label' => esc_html__( 'Table Header Title', 'oltew-order-list-table-ele' ),
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'oltew-order-list-table-ele' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'oltew-order-list-table-ele' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'oltew-order-list-table-ele' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
			]
		);
		
		$this->add_control(
			'oltew_table_td_alignment',
			[
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'label' => esc_html__( 'Table Body Text', 'oltew-order-list-table-ele' ),
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'oltew-order-list-table-ele' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'oltew-order-list-table-ele' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'oltew-order-list-table-ele' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
			]
		);

		$this->end_controls_section();


		/////////////////Table Style Tab////////////////
		///////////////////////////////////////////////

		$this->start_controls_section(
			'table_style',
			[
				'label' => esc_html__('Table Style', 'oltew-order-list-table-ele'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		//Table Border//
		$this->add_control(
			'table_order_heading',
			[
				'label' => esc_html__('Table Style', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'table_border_style',
				'selector' => '{{WRAPPER}} .oltew-order-list-table table',
			]
		);

		//Table Padding
		$this->add_responsive_control(
			'woltw_table_padding',
			[
				'label' => esc_html__('Table Padding', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table,{{WRAPPER}} .oltew-order-list-table table th, {{WRAPPER}} .oltew-order-list-table table td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]

			]
		);

		$this->add_control(
			'table_heading_style',
			[
				'label' => esc_html__('Table Header Style', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'table_heading_title_color',
			[
				'label' => esc_html__('Title Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table th' => 'color: {{VALUE}}'
				]
			]
		);
		
		$this->add_control(
			'table_heading_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'oltew-order-list-table-ele' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table th .order_list_icon' => 'width: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}} .oltew-order-list-table table th .order_list_icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'table_heading_icon_color',
			[
				'label' => esc_html__('Icon Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table th .order_list_icon, {{WRAPPER}} .oltew-order-list-table th .order_list_icon svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .oltew-order-list-table th .order_list_icon svg path' => 'fill: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'table_heading_background_color',
			[
				'label' => esc_html__('Background Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffd000',
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table thead' => 'background: {{VALUE}}'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'table_heading_typography',
				'label' => esc_html__('Typography', 'oltew-order-list-table-ele'),
				'selector' => '{{WRAPPER}} .oltew-order-list-table th',
			]
		);


		//Table Th Border
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'table_th_border_style',
				'selector' => '{{WRAPPER}} .oltew-order-list-table table th',
			]
		);




		//Table Body Style
		$this->add_control(
			'table_body_style',
			[
				'label' => esc_html__('Table Body Style', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);
		$this->add_control(
			'table_body_text_color',
			[
				'label' => esc_html__('Text color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table td' => 'color: {{VALUE}}'
				]
			]
		);

		
		$this->add_control(
			'table_body_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'oltew-order-list-table-ele' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table td .order_list_icon, {{WRAPPER}} .oltew-order-list-table table td .order_list_icon svg' => 'width: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}} .oltew-order-list-table table td .order_list_icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'table_body_icon_color',
			[
				'label' => esc_html__('Icon Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table td .order_list_icon, {{WRAPPER}} .oltew-order-list-table table td .order_list_icon svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .oltew-order-list-table table td .order_list_icon svg path' => 'fill: {{VALUE}}',
				]
			]
		);
		$this->add_control(
			'table_background_color',
			[
				'label' => esc_html__('Background Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table' => 'background: {{VALUE}}'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'table_body_typography',
				'label' => esc_html__('Typography', 'oltew-order-list-table-ele'),
				'selector' => '{{WRAPPER}} .oltew-order-list-table table td',
			]
		);

		$this->add_control(
			'tr_nth_child_even',
			[
				'label' => esc_html__('<tr> Background (even)', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table tr:nth-child(even)' => 'background-color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'tr_nth_child_odd',
			[
				'label' => esc_html__('<tr> Background (odd)', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table tr:nth-child(odd)' => 'background-color: {{VALUE}}',
				]
			]
		);


		//Table Tr Border
		$this->add_control(
			'table_tr_border',
			[	
				'label' => esc_html__('Table tr style', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'table_tr_border_style',
				'selector' => '{{WRAPPER}} .oltew-order-list-table table tr',
			]
		);


		//Table Td Border
		$this->add_control(
			'table_td_border',
			[	
				'label' => esc_html__('Table td style', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'table_td_border_style',
				'selector' => '{{WRAPPER}} .oltew-order-list-table table td',
			]
		);

		

		$this->end_controls_section();


		//Product Link Style Section
		$this->start_controls_section(
			'product_link_style_section',
			[
				'label' => esc_html__('Product List Link', 'oltew-order-list-table-ele'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		//Product Link Style Tabs//
		/////////////////////////
		$this->add_control(
			'product_link_style_heading',
			[
				'label' => esc_html__('Product Link Style', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->start_controls_tabs(
			'product_link_style_tabs'
		);

		//Product Link Normal
		$this->start_controls_tab(
			'product_links_tyle_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'oltew-order-list-table-ele' ),
			]
		);


		//Product Link Color Normal
		$this->add_control(
			'product_body_link_color',
			[
				'label' => esc_html__('Product link color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table .product_list a' => 'color: {{VALUE}}'
				]
			]
		);


		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'product_link_typography',
				'label' => esc_html__('Product Link Typography', 'oltew-order-list-table-ele'),
				'selector' => '{{WRAPPER}} .oltew-order-list-table table .product_list',
			]
		);



		$this->end_controls_tab();


		//Product Link Hover
		$this->start_controls_tab(
			'product_link_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'oltew-order-list-table-ele' ),
			]
		);

		//Product Link Color Hover
		$this->add_control(
			'product_body_link_hover',
			[
				'label' => esc_html__('Product link color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table .product_list a:hover' => 'color: {{VALUE}}'
				]
			]
		);

		$this->end_controls_tab();




		$this->end_controls_tabs();




		$this->end_controls_section();




		////Buy now Button section Style//
		/////////////////////////////////
		$this->start_controls_section(
			'buy_now_button_style',
			[
				'label' => esc_html__('Buy Now Button', 'oltew-order-list-table-ele'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);



		$this->start_controls_tabs(
			'buy_now_style_tabs'
		);

		//Buy Now Link Normal
		$this->start_controls_tab(
			'buy_now_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'oltew-order-list-table-ele' ),
			]
		);


		//Buy Now Link Color Normal
		$this->add_control(
			'buy_now_link_color',
			[
				'label' => esc_html__('Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table .buy_now a' => 'color: {{VALUE}}',
				]
			]
		);

		//Buy Now Icon Color Normal
		$this->add_control(
			'buy_now_link_icon_color',
			[
				'label' => esc_html__('Icon Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table .buy_now_icon, {{WRAPPER}} .oltew-order-list-table table .buy_now_icon svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .oltew-order-list-table table .buy_now_icon svg path' => 'fill: {{VALUE}}',
				]
			]
		);

		//Buy Now Background Color Normal
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'buy_now_background_color',
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .oltew-order-list-table table .buy_now a',
				
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'buy_now_link_typography',
				'label' => esc_html__('Typography', 'oltew-order-list-table-ele'),
				'selector' => '{{WRAPPER}} .oltew-order-list-table table .buy_now',
			]
		);


		$this->add_responsive_control(
			'buy_now_padding',
			[
				'label' => esc_html__('Padding', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default' => [
					'top' => 5,
					'right' => 10,
					'bottom' => 5,
					'left' => 10,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table .buy_now a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

			]
		);


		$this->add_responsive_control(
			'buy_now_margin',
			[
				'label' => esc_html__('Margin', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default' => [
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table .buy_now a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

			]
		);


		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'buy_now_border_color',
				'selector' => '{{WRAPPER}} .oltew-order-list-table table .buy_now a',
			]
		);


		$this->add_responsive_control(
			'buy_now_border_radius',
			[
				'label' => esc_html__('Border radius', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default' => [
					'top' => 5,
					'right' => 5,
					'bottom' => 5,
					'left' => 5,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table .buy_now a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

			]
		);



		$this->end_controls_tab();


		//Buy Now Link Hover//
		//////////////////////
		$this->start_controls_tab(
			'buy_now_link_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'oltew-order-list-table-ele' ),
			]
		);

		//Buy Now Link Color Hover
		$this->add_control(
			'buy_now_link_hover',
			[
				'label' => esc_html__('Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table .buy_now a:hover' => 'color: {{VALUE}}'
				]
			]
		);

		$this->end_controls_tab();




		$this->end_controls_tabs();






		$this->end_controls_section();

		/////////////Order Status Section///////////////
		///////////////////////////////////////////////
		$this->start_controls_section(
			'order_status_style',
			[
				'label' => esc_html__('Order Status Style', 'oltew-order-list-table-ele'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		//Table Status Icon Size

		$this->add_control(
			'table_status_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'oltew-order-list-table-ele' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table td .status_icon, {{WRAPPER}} .oltew-order-list-table table td .status_icon i' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		//completed style
		$this->add_control(
			'table_status_completed_style',
			[
				'label' => esc_html__('Status Completed Style', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);
		$this->add_control(
			'table_status_list_title_color',
			[
				'label' => esc_html__('Title Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st_completed' => 'color: {{VALUE}}'
				]
			]
		);

		$this->add_control(
			'complete_status_list_icon_color',
			[
				'label' => esc_html__('Icon Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table td .st_completed .status_icon, {{WRAPPER}} .oltew-order-list-table table td .st_completed .status_icon svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .oltew-order-list-table td .st_completed .status_icon svg path' => 'fill: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'complete_status_list_background_color',
			[
				'label' => esc_html__('Background Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st_completed' => 'background: {{VALUE}}'
				]
			]
		);

		$this->add_responsive_control(
			'completed_padding',
			[
				'label' => esc_html__('Padding', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .st_completed' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

			]
		);


		//Processing style
		$this->add_control(
			'table_status_processing_style',
			[
				'label' => esc_html__('Status Processing Style', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);
		$this->add_control(
			'processing_status_list_title_color',
			[
				'label' => esc_html__('Title Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st_processing' => 'color: {{VALUE}}'
				]
			]
		);

		$this->add_control(
			'processing_status_list_icon_color',
			[
				'label' => esc_html__('Icon Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table td .st_processing .status_icon, {{WRAPPER}} .oltew-order-list-table td .st_processing .status_icon svg, {{WRAPPER}} .oltew-order-list-table td .st_processing .status_icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .oltew-order-list-table td .st_processing .status_icon svg path' => 'fill: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'processing_status_list_background_color',
			[
				'label' => esc_html__('Background Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st_processing' => 'background: {{VALUE}}'
				]
			]
		);
		$this->add_responsive_control(
			'processing_padding',
			[
				'label' => esc_html__('Padding', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .st_processing' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

			]
		);



		//On Hold style
		$this->add_control(
			'table_status_on_hold_style',
			[
				'label' => esc_html__('Status On Hold', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);
		$this->add_control(
			'on_hold_status_list_title_color',
			[
				'label' => esc_html__('Title Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st_on-hold' => 'color: {{VALUE}}'
				]
			]
		);

		$this->add_control(
			'on_hold_status_list_icon_color',
			[
				'label' => esc_html__('Icon Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table td .st_on-hold .status_icon, {{WRAPPER}} .oltew-order-list-table table td .st_on-hold .status_icon svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .oltew-order-list-table td .st_on-hold .status_icon svg path' => 'fill: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'on_hold_status_list_background_color',
			[
				'label' => esc_html__('Background Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st_on-hold' => 'background: {{VALUE}}'
				]
			]
		);
		$this->add_responsive_control(
			'on_hold_padding',
			[
				'label' => esc_html__('Padding', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .st_on-hold' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]

			]
		);


		//Status Failed Style
		$this->add_control(
			'table_status_failed_style',
			[
				'label' => esc_html__('Status Failed & Cancelled', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);
		$this->add_control(
			'failed_status_list_title_color',
			[
				'label' => esc_html__('Title Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st_cancelled' => 'color: {{VALUE}}'
				]
			]
		);

		$this->add_control(
			'failed_status_list_icon_color',
			[
				'label' => esc_html__('Icon Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .oltew-order-list-table table td .st_cancelled .status_icon, {{WRAPPER}} .oltew-order-list-table table td .st_cancelled .status_icon svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .oltew-order-list-table td .st_cancelled .status_icon svg path' => 'fill: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'failed_status_list_background_color',
			[
				'label' => esc_html__('Background Color', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .st_cancelled' => 'background: {{VALUE}}'
				]
			]
		);
		$this->add_responsive_control(
			'failed_padding',
			[
				'label' => esc_html__('Padding', 'oltew-order-list-table-ele'),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .st_cancelled' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

			]
		);


		$this->end_controls_section();
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

		// Table Settings Section
		$settings = $this->get_settings_for_display();
		$select_status = $settings['select_status'];
		$list_per_page = $settings['list_per_page'];
		$order_time_format = $settings['order_time_format'];
		$order_by = $settings['order_by'];

		$order_list_mobile_style = $settings['order_list_mobile_style'];

		//Prodcut Section
		$product_name = $settings['product_name'];
		$product_link_words = $settings['product_link_words'];
		$hide_product_name = $settings['hide_product_name'];
		$product_name_th_icon = $settings['product_name_th_icon']; 
		$product_list_icon = $settings['product_list_icon'];


		// Table Header Title & Icon Section
		$customer_name = $settings['customer_name'];
		$sell_time = $settings['sell_time'];
		$order_status = $settings['order_status'];
		$buy_now = $settings['buy_now'];
		$customer_phone = $settings['customer_phone'];
		$sell_amount = $settings['sell_amount'];
		
		//Heading  Icons
		$customer_th_icon = $settings['customer_th_icon'];
		$sell_time_th_icon = $settings['sell_time_th_icon'];
		$order_status_th_icon = $settings['order_status_th_icon'];
		$buy_now_th_icon = $settings['buy_now_th_icon']; 
		$customer_phone_th_icon = $settings['customer_phone_th_icon'];
		$sell_amount_th_icon = $settings['sell_amount_th_icon'];
		
		
		//Table Body Icons
		$customer_list_icon = $settings['customer_list_icon'];
		$sell_list_time_icon = $settings['sell_list_time_icon'];
		$buy_now_button_text = $settings['buy_now_button_text'];
		$buy_now_list_icon = $settings['buy_now_list_icon'];
		$customer_phone_list_icon = $settings['customer_phone_list_icon'];

		//Order Status Icons
		$set_status_completed_icon = $settings['set_status_completed_icon'];
		$set_status_processing_icon = $settings['set_status_processing_icon'];
		$set_status_onhold_icon = $settings['set_status_onhold_icon'];
		$set_status_faild_icon = $settings['set_status_faild_icon'];

		
		// Hide Column
		$hide_order_sl = $settings['hide_order_sl'];
		$hide_customer_title = $settings['hide_customer_title'];
		$hide_sell_time = $settings['hide_sell_time'];
		$hide_status = $settings['hide_status'];
		
		$hide_buy_now  = $settings['hide_buy_now'];
		$hide_customer_phone = $settings['hide_customer_phone'];
		$hide_sell_amount = $settings['hide_sell_amount'];
		// Table Text Align
		$oltew_table_th_alignment = $settings['oltew_table_th_alignment'];
		$oltew_table_td_alignment = $settings['oltew_table_td_alignment'];

		// Check WooCommerce Plugin Active or Not
		if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) :
			// Check if HPOS is enabled
			$is_hpos_enabled = class_exists('Automattic\WooCommerce\Utilities\OrderUtil') &&
							Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();

			// Fetch orders based on storage method
			$customer_orders = $is_hpos_enabled 
				? wc_get_orders(array(
					'status'    => $select_status,
					'orderby'   => 'date',
					'order'     => $order_by,
					'limit'     => $list_per_page,
				))
				: get_posts(apply_filters('woocommerce_my_account_my_orders_query', array(
					'post_type'      => 'shop_order',
					'post_status'    => $select_status,
					'order'          => $order_by,
					'posts_per_page' => $list_per_page,
				)));

			// Check if orders are available
			if ($customer_orders) : ?>


				<!-- Responsive for Mobile Style -->
				 <?php if($order_list_mobile_style == 'yes'): ?>
				 <style>
					@media only screen and (max-width: 760px) {
						.oltew-order-list-table table,
						.oltew-order-list-table thead,
						.oltew-order-list-table tbody,
						.oltew-order-list-table th,
						.oltew-order-list-table td,
						.oltew-order-list-table tr {
							display: block;
						}

						.oltew-order-list-table table, .oltew-order-list-table table th {
							border: none;
						}


						.oltew-order-list-table thead tr {
							position: absolute;
							top: -9999px;
							left: -9999px;
						}

						.oltew-order-list-table table tr {
							margin-bottom: 15px;
							border: 1px solid #e4e4e4;
						}

						.oltew-order-list-table td {
							position: relative;
							padding-left: 30%;
							border: 1px solid #e4e4e4;
						}

						.oltew-order-list-table td:before {
							position: absolute;
							left: 6px;
							width: 25%;
							padding-right: 10px;
							white-space: nowrap;
							content: attr(data-column);
							font-weight: bold;
						}
					}
				</style>
				<?php endif; ?>

				<div class="oltew-order-list-table" style="overflow-x:auto;">
					<table>
						<thead>
							<tr style="text-align:<?php echo esc_html($oltew_table_th_alignment); ?>;">
								<?php if ($hide_order_sl !== 'yes') { ?><th>SL</th><?php }; ?>
								
								<?php if ($hide_customer_title !== 'yes') { ?>
								<th class="customer_name_woor">
									<div class="heading_content">
										<span class="order_list_icon"><?php \Elementor\Icons_Manager::render_icon($customer_th_icon, ['aria-hidden' => 'true']); ?></span> <?php echo esc_html($customer_name); ?>
									</div>
								</th>
								<?php }; ?>
								
								<?php if ($hide_sell_time !== 'yes') { ?>
								<th class="sell_time_woor">
									<div class="heading_content">
										<span class="order_list_icon"><?php \Elementor\Icons_Manager::render_icon($sell_time_th_icon, ['aria-hidden' => 'true']); ?></span> <?php echo esc_html($sell_time); ?>
									</div>
								</th>
								<?php }; ?>
								
								<?php if ($hide_status !== 'yes') { ?>
									<th class="order_status_woor">
										<div class="heading_content">
											<span class="order_list_icon"><?php \Elementor\Icons_Manager::render_icon($order_status_th_icon, ['aria-hidden' => 'true']); ?></span> <?php echo esc_html($order_status); ?>
										</div>
									</th>
								<?php }; ?>
								
								<?php if ($hide_product_name !== 'yes') { ?>
									<th class="product_name_woor">
										<div class="heading_content"><span class="order_list_icon"><?php \Elementor\Icons_Manager::render_icon($product_name_th_icon, ['aria-hidden' => 'true']); ?></span> <?php echo esc_html($product_name); ?>
										</div>
									</th>
								<?php }; ?>

								<?php if ($hide_customer_phone !== 'yes') { ?>
									<th class="customer_phone_woor">
										<div class="heading_content"><span class="order_list_icon"><?php \Elementor\Icons_Manager::render_icon($customer_phone_th_icon, ['aria-hidden' => 'true']); ?></span> <?php echo esc_html($customer_phone); ?>
										</div>
									</th>
								<?php }; ?>
								
								
								<?php if ($hide_sell_amount !== 'yes') { ?>
									<th class="sell_amount_woor">
										<div class="heading_content">
											<span class="order_list_icon"><?php \Elementor\Icons_Manager::render_icon($sell_amount_th_icon, ['aria-hidden' => 'true']); ?></span> <?php echo esc_html($sell_amount); ?>
										</div>
									</th>
								<?php }; ?>


								<?php if ($hide_buy_now !== 'yes') { ?>
									<th class="buy_now_woor">
										<div class="heading_content"><span class="order_list_icon"><?php \Elementor\Icons_Manager::render_icon($buy_now_th_icon, ['aria-hidden' => 'true']); ?></span> <?php echo esc_html($buy_now); ?>
										</div>
									</th>
								<?php }; ?>
							</tr>
						</thead>
						<tbody>
							<?php
							$sl = 1;
							foreach ($customer_orders as $customer_order) {
								$order = $is_hpos_enabled ? $customer_order : wc_get_order($customer_order->ID);
								if (!$order) continue;
								$order_date = $order->get_date_created();
							?>
								<tr style="text-align:<?php echo esc_html($oltew_table_td_alignment); ?>;">
									
									<!-- Serial Number -->
									<?php if ($hide_order_sl !== 'yes') { ?>
										<td class="order_sl" data-column="SL"><?php echo esc_html($sl++); ?></td>
									<?php }; ?>
									
									<!-- Customer Name -->
									<?php if ($hide_customer_title !== 'yes') { ?>
										<td class="customer_name"  data-column="<?php echo esc_html($customer_name); ?>">
											<div class="body_content">
												<div class="order_list_icon"><?php \Elementor\Icons_Manager::render_icon($customer_list_icon, ['aria-hidden' => 'true']); ?></div>
												<?php echo esc_html($order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ?: 'Guest'); ?>
											</div>
										</td>
									<?php }; ?>

									<!-- Sell Time -->
									<?php if ($hide_sell_time !== 'yes') { ?>
										<td class="sell_time"  data-column="<?php echo esc_html($sell_time); ?>">
											<div class="body_content">
												<div class="order_list_icon"><?php \Elementor\Icons_Manager::render_icon($sell_list_time_icon, ['aria-hidden' => 'true']); ?></div>
												<?php
												if ($order_time_format == 'ago') {
													if (function_exists('oltew_ago_woo_list_table')) {
														echo esc_html(oltew_ago_woo_list_table($order_date));
													}
												}
												if ($order_time_format == 'date') {
													echo esc_html(date('d/M/y h:i A', strtotime($order_date)));
												}
												?>
											</div>
										</td>
									<?php }; ?>

									<!-- Order Status -->
									<?php if ($hide_status !== 'yes') { ?>
									<td  data-column="<?php echo esc_html($order_status); ?>">
										<?php
											$order_status = wc_get_order_status_name($order->get_status());
	
											if ($order_status == 'Completed') { ?>
												<span class="st_completed status_odd"><span class="status_icon"><?php \Elementor\Icons_Manager::render_icon($set_status_completed_icon, ['aria-hidden' => 'true']); ?></span> Completed</span>
											<?php }

											if ($order_status == 'Processing')  { ?>
												<span class="st_processing status_odd"><span class="status_icon"><?php \Elementor\Icons_Manager::render_icon($set_status_processing_icon, ['aria-hidden' => 'true'])?></span> Processing</span>
											<?php }

											if ($order_status == 'On hold') { ?>
												<span class="st_on-hold status_odd"><span class="status_icon"><?php \Elementor\Icons_Manager::render_icon($set_status_onhold_icon, ['aria-hidden' => 'true'])?></span> On Hold</span>
											<?php }

											if ($order_status == 'Cancelled') { ?>
												<span class="st_cancelled status_odd"><span class="status_icon"><?php \Elementor\Icons_Manager::render_icon($set_status_faild_icon, ['aria-hidden' => 'true'])?></span> Cancelled</span>
											<?php }

											if ($order_status == 'Failed') { ?>
												<span class="st_cancelled status_odd"><span class="status_icon"><?php \Elementor\Icons_Manager::render_icon($set_status_faild_icon, ['aria-hidden' => 'true'])?></span> Failed</span>
											<?php }
										?>
									</td>
									
									
									<?php }; ?>


								<!-- Product Name -->
									<?php if ($hide_product_name !== 'yes') { ?>
										<td class="product_list" data-column="<?php echo esc_html($product_name); ?>">
											<div class="product_list_content">
												<?php if (!empty($product_list_icon['value'])): ?>
													<div class="order_list_icon">
														<?php \Elementor\Icons_Manager::render_icon($product_list_icon, ['aria-hidden' => 'true']); ?>
													</div>
												<?php endif; ?>

												<?php
												$word_limit = $product_link_words; // Limit product titles to 5 words
												$product_links = []; // Initialize array to hold product titles with links

												foreach ($order->get_items() as $item) {
													// Get the product ID
													$product_id = $item->get_product_id();

													// Get the product permalink
													$product_url = get_permalink($product_id);

													// Get the product title
													$full_title = esc_html($item->get_name());
													$truncated_title = implode(' ', array_slice(explode(' ', $full_title), 0, $word_limit));

													// Append the product link to the array
													$product_links[] = '<a href="' . esc_url($product_url) . '">' . esc_html($truncated_title) . '...</a>';
												}

												// Display product titles as links separated by commas
												echo implode(', ', $product_links);
												?>
											</div>
										</td>
									<?php } ?>


									


									
									<!-- Customer Phone -->
									<?php if ($hide_customer_phone !== 'yes') { ?>
										<td class="customer_phone"  data-column="<?php echo esc_html($customer_phone); ?>">
												<?php if (!empty($customer_phone_list_icon['value'])): ?>
													<div class="order_list_icon">
													<?php
													\Elementor\Icons_Manager::render_icon($customer_phone_list_icon, ['aria-hidden' => 'true']);?>
													</div>
												<?php endif; ?>
												<?php
												$customer_phone_woo = $order->get_billing_phone();
												echo esc_html(' ' . substr($customer_phone_woo, 0, -4) . 'XXXX');
												?>
											</td>
									<?php }; ?>

									<!-- Sell Amount -->
									<?php if ($hide_sell_amount !== 'yes') { ?>
										<td class="sell_amount"  data-column="<?php echo esc_html($sell_amount); ?>">
											<?php
											// Get the order's total and currency
											$order_total = $order->get_total();
											$order_currency = $order->get_currency(); // Get currency for this order

											// Format the price using the order's currency
											echo wc_price($order_total, array('currency' => $order_currency));
											?>
										</td>
									<?php }; ?>


									<!-- Buy Now -->
									<?php if ($hide_buy_now !== 'yes') { ?>
									<td class="buy_now"  data-column="<?php echo esc_html($buy_now); ?>">
										<?php
										foreach ($order->get_items() as $item) {
											// Get the product ID
											$product_id = $item->get_product_id();

											// Load the WooCommerce product object
											$product = wc_get_product($product_id);

											// Determine the URL for the "Buy Now" button
											if ($product->is_type('simple')) {
												// Redirect to checkout with the product in the cart
												$buy_now_url = wc_get_checkout_url() . '?add-to-cart=' . $product_id;
											} elseif ($product->is_type('variable')) {
												// Redirect to the product's single product page
												$buy_now_url = get_permalink($product_id);
											} else {
												// Default to the product's page for other product types
												$buy_now_url = get_permalink($product_id);
											}

											// Display the "Buy Now" button 
											?>
											<a href="<?php echo esc_url($buy_now_url); ?>" class="buy-now-button">
												<?php if (!empty($buy_now_list_icon['value'])): ?>
												<span class="buy_now_icon">
													<?php \Elementor\Icons_Manager::render_icon($buy_now_list_icon, ['aria-hidden' => 'true']);?>
												</span>
												<?php endif; ?>
												
												<?php echo esc_html($buy_now_button_text); ?>
											</a>
											<?php
										}
										?>
									</td>
									<?php }; ?>


								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			<?php endif;
		endif;
	}


}
