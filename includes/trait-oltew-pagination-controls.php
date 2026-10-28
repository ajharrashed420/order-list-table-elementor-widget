<?php
/**
 * Elementor pagination controls.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Oltew_Pagination_Controls {
	/** Register text and visibility controls. */
	protected function register_load_more_controls() {
		$this->start_controls_section( 'oltew_pagination', array( 'label' => esc_html__( 'Load More', 'order-list-table-elementor-widget' ) ) );
		$this->add_control(
			'oltew_load_more',
			array(
				'label'       => esc_html__( 'Show Load More button', 'order-list-table-elementor-widget' ),
				'description' => esc_html__( 'Use the table items-per-page setting for each batch. The button appears only when more matching items are available.', 'order-list-table-elementor-widget' ),
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);
		foreach ( array(
			'oltew_load_more_text'    => array( esc_html__( 'Button text', 'order-list-table-elementor-widget' ), esc_html__( 'Load More', 'order-list-table-elementor-widget' ) ),
			'oltew_load_more_loading' => array( esc_html__( 'Loading text', 'order-list-table-elementor-widget' ), esc_html__( 'Loading…', 'order-list-table-elementor-widget' ) ),
			'oltew_load_more_error'   => array( esc_html__( 'Error text', 'order-list-table-elementor-widget' ), esc_html__( 'Unable to load rows. Please try again or reload the page.', 'order-list-table-elementor-widget' ) ),
		) as $name => $text ) {
			$this->add_control(
				$name,
				array(
					'label'     => $text[0],
					'type'      => \Elementor\Controls_Manager::TEXT,
					'default'   => $text[1],
					'condition' => array( 'oltew_load_more' => 'yes' ),
				)
			);
		}
		$this->end_controls_section();
	}

	/** Register appearance controls after the widget's other style sections. */
	protected function register_load_more_style_controls() {
		$this->start_controls_section(
			'oltew_pagination_style',
			array(
				'label' => esc_html__( 'Load More Button', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		foreach ( array(
			'oltew_more_color'            => array( esc_html__( 'Text color', 'order-list-table-elementor-widget' ), '.oltew-load-more', 'color' ),
			'oltew_more_background'       => array( esc_html__( 'Background', 'order-list-table-elementor-widget' ), '.oltew-load-more', 'background' ),
			'oltew_more_hover_color'      => array( esc_html__( 'Hover text color', 'order-list-table-elementor-widget' ), '.oltew-load-more:hover', 'color' ),
			'oltew_more_hover_background' => array( esc_html__( 'Hover background', 'order-list-table-elementor-widget' ), '.oltew-load-more:hover', 'background' ),
		) as $key => $style ) {
			$this->add_control(
				$key,
				array(
					'label'     => $style[0],
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} ' . $style[1] => $style[2] . ': {{VALUE}};' ),
				)
			);
		}
		$this->add_responsive_control(
			'oltew_more_align',
			array(
				'label'     => esc_html__( 'Alignment', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => array(
					'left'   => esc_html__( 'Left', 'order-list-table-elementor-widget' ),
					'center' => esc_html__( 'Center', 'order-list-table-elementor-widget' ),
					'right'  => esc_html__( 'Right', 'order-list-table-elementor-widget' ),
				),
				'selectors' => array( '{{WRAPPER}} .oltew-load-more-wrap' => 'text-align: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'oltew_more_type',
				'selector' => '{{WRAPPER}} .oltew-load-more',
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'oltew_more_border',
				'selector' => '{{WRAPPER}} .oltew-load-more',
			)
		);
		$this->add_responsive_control(
			'oltew_more_padding',
			array(
				'label'     => esc_html__( 'Padding', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => array( '{{WRAPPER}} .oltew-load-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'oltew_more_radius',
			array(
				'label'      => esc_html__( 'Corner radius', 'order-list-table-elementor-widget' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors'  => array( '{{WRAPPER}} .oltew-load-more' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}
}
