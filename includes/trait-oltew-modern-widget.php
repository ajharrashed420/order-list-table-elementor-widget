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
	use Oltew_Order_Table_Renderer;

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
}
