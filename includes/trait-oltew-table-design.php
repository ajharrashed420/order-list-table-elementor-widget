<?php
/**
 * Shared visual controls for order and product tables.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Keep the table design controls consistent across widgets. */
trait Oltew_Table_Design {
	use Oltew_Table_Pagination;
	use Oltew_Pagination_Controls;

	/**
	 * Register common design controls.
	 *
	 * @param bool $order_status Include order-specific badge colors.
	 */
	protected function register_table_design_controls( $order_status = true ) {
		$this->start_controls_section(
			'oltew_modern_style',
			array(
				'label' => esc_html__( 'Modern Design', 'order-list-table-elementor-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		foreach ( array(
			'oltew_accent'       => array( esc_html__( 'Accent color', 'order-list-table-elementor-widget' ), '--oltew-accent' ),
			'oltew_surface'      => array( esc_html__( 'Surface color', 'order-list-table-elementor-widget' ), '--oltew-surface' ),
			'oltew_border_color' => array( esc_html__( 'Divider / border color', 'order-list-table-elementor-widget' ), '--oltew-border' ),
			'oltew_muted'        => array( esc_html__( 'Subtitle / field label color', 'order-list-table-elementor-widget' ), '--oltew-muted' ),
			'oltew_hover_color'  => array( esc_html__( 'Hover background', 'order-list-table-elementor-widget' ), '--oltew-hover-bg' ),
			'oltew_stripe_color' => array( esc_html__( 'Alternating row color', 'order-list-table-elementor-widget' ), '--oltew-stripe' ),
		) as $name => $control ) {
			$this->add_control(
				$name,
				array(
					'label'     => $control[0],
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .oltew-orders' => $control[1] . ': {{VALUE}};' ),
				)
			);
		}
		foreach ( array(
			'oltew_radius'        => array( esc_html__( 'Corner radius', 'order-list-table-elementor-widget' ), '--oltew-radius', 18, 60 ),
			'oltew_gap'           => array( esc_html__( 'Row gap', 'order-list-table-elementor-widget' ), '--oltew-gap', 20, 80 ),
			'oltew_cell_padding'  => array( esc_html__( 'Cell / field spacing', 'order-list-table-elementor-widget' ), '--oltew-padding', 18, 60 ),
			'oltew_badge_radius'  => array( esc_html__( 'Badge radius', 'order-list-table-elementor-widget' ), '--oltew-badge-radius', 50, 60 ),
			'oltew_button_radius' => array( esc_html__( 'Button radius', 'order-list-table-elementor-widget' ), '--oltew-button-radius', 10, 60 ),
			'oltew_thumb_size'    => array( esc_html__( 'Product thumbnail size', 'order-list-table-elementor-widget' ), '--oltew-thumb-size', 36, 100 ),
			'oltew_thumb_radius'  => array( esc_html__( 'Product thumbnail radius', 'order-list-table-elementor-widget' ), '--oltew-thumb-radius', 8, 50 ),
		) as $name => $control ) {
			$this->add_responsive_control(
				$name,
				array(
					'label'      => $control[0],
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => $control[3],
						),
					),
					'default'    => array(
						'size' => $control[2],
						'unit' => 'px',
					),
					'selectors'  => array( '{{WRAPPER}} .oltew-orders' => $control[1] . ': {{SIZE}}{{UNIT}};' ),
				)
			);
		}
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'oltew_custom_shadow',
				'selector' => '{{WRAPPER}} .oltew-shadow .oltew-surface',
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'oltew_title_type',
				'label'    => esc_html__( 'Title typography', 'order-list-table-elementor-widget' ),
				'selector' => '{{WRAPPER}} .oltew-title',
			)
		);
		$this->add_control(
			'oltew_title_color',
			array(
				'label'     => esc_html__( 'Title color', 'order-list-table-elementor-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .oltew-title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'oltew_subtitle_type',
				'label'    => esc_html__( 'Subtitle typography', 'order-list-table-elementor-widget' ),
				'selector' => '{{WRAPPER}} .oltew-subtitle',
			)
		);
		if ( $order_status ) {
			foreach ( array(
				'refunded' => esc_html__( 'Refunded', 'order-list-table-elementor-widget' ),
				'pending'  => esc_html__( 'Pending payment', 'order-list-table-elementor-widget' ),
			) as $status => $label ) {
				$this->add_control(
					'oltew_' . $status . '_color',
					array(
						'label'     => $label,
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => array( '{{WRAPPER}} .st_' . $status => 'color: {{VALUE}};' ),
					)
				);
				$this->add_control(
					'oltew_' . $status . '_background',
					array(
						'label'     => $label . ' ' . esc_html__( 'background', 'order-list-table-elementor-widget' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => array( '{{WRAPPER}} .st_' . $status => 'background: {{VALUE}};' ),
					)
				);
			}
		}
		$this->end_controls_section();
	}
}
