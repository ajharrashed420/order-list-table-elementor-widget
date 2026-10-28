<?php
/**
 * Shared order query and rendering for Elementor and WooCommerce My Account.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Oltew_Order_Table_Renderer {
	/**
	 * Define canonical fields with the original setting identifiers.
	 *
	 * @return array
	 */
	private function oltew_fields() {
		$fields = array(
			'serial'   => array(
				'label' => esc_html__( 'SL', 'order-list-table-elementor-widget' ),
				'hide'  => 'hide_order_sl',
				'class' => 'order_sl',
			),
			'customer' => array(
				'label'   => esc_html__( 'Customer', 'order-list-table-elementor-widget' ),
				'setting' => 'customer_name',
				'hide'    => 'hide_customer_title',
				'icon'    => 'customer_th_icon',
				'class'   => 'customer_name',
			),
			'time'     => array(
				'label'   => esc_html__( 'Sell Time', 'order-list-table-elementor-widget' ),
				'setting' => 'sell_time',
				'hide'    => 'hide_sell_time',
				'icon'    => 'sell_time_th_icon',
				'class'   => 'sell_time',
			),
			'status'   => array(
				'label'   => esc_html__( 'Status', 'order-list-table-elementor-widget' ),
				'setting' => 'order_status',
				'hide'    => 'hide_status',
				'icon'    => 'order_status_th_icon',
				'class'   => 'order_status',
			),
			'products' => array(
				'label'   => esc_html__( 'Products', 'order-list-table-elementor-widget' ),
				'setting' => 'product_name',
				'hide'    => 'hide_product_name',
				'icon'    => 'product_name_th_icon',
				'class'   => 'product_list',
			),
			'phone'    => array(
				'label'   => esc_html__( 'Phone', 'order-list-table-elementor-widget' ),
				'setting' => 'customer_phone',
				'hide'    => 'hide_customer_phone',
				'icon'    => 'customer_phone_th_icon',
				'class'   => 'customer_phone',
			),
			'amount'   => array(
				'label'   => esc_html__( 'Amount', 'order-list-table-elementor-widget' ),
				'setting' => 'sell_amount',
				'hide'    => 'hide_sell_amount',
				'icon'    => 'sell_amount_th_icon',
				'class'   => 'sell_amount',
			),
			'action'   => array(
				'label'   => esc_html__( 'Action', 'order-list-table-elementor-widget' ),
				'setting' => 'buy_now',
				'hide'    => 'hide_buy_now',
				'icon'    => 'buy_now_th_icon',
				'class'   => 'buy_now',
			),
		);
		if ( 'oltew_account_orders' === $this->get_name() ) {
			$fields['order'] = array(
				'label' => __( 'Order', 'order-list-table-elementor-widget' ),
				'hide'  => 'hide_order_number',
				'class' => 'order_number',
			);
		}
		return $fields;
	}

	/**
	 * Normalize, deduplicate and filter the ordered fields.
	 *
	 * @param array $settings Elementor settings.
	 * @return array
	 */
	private function oltew_visible_fields( $settings ) {
		$fields = $this->oltew_fields();
		$rows   = isset( $settings['oltew_columns'] ) && is_array( $settings['oltew_columns'] ) ? $settings['oltew_columns'] : array_map(
			function ( $key ) {
				return array(
					'field'   => $key,
					'enabled' => 'yes',
				);
			},
			array_keys( $fields )
		);
		$result = array();
		foreach ( $rows as $row ) {
			$key = isset( $row['field'] ) && is_string( $row['field'] ) ? $row['field'] : '';
			if ( ! isset( $fields[ $key ] ) || isset( $result[ $key ] ) || 'yes' !== ( $row['enabled'] ?? 'yes' ) || 'yes' === ( $settings[ $fields[ $key ]['hide'] ] ?? '' ) ) {
				continue;
			}
			$field          = $fields[ $key ];
			$field['label'] = ! empty( $row['label'] ) ? $row['label'] : ( $settings[ $field['setting'] ?? '' ] ?? $field['label'] );
			$result[ $key ] = $field;
		}
		return $result;
	}

	/** Render tables using one secure order query and field renderer. */
	protected function render_modern_orders() {
		if ( ! function_exists( 'wc_get_orders' ) || ( 'oltew_account_orders' !== $this->get_name() && ! Oltew_Settings::widget_enabled( $this->get_name() ) ) ) {
			return;
		}
		$settings = $this->get_settings_for_display();
		$scope    = $settings['oltew_scope'] ?? 'public';
		if ( 'public' !== $scope ) {
			if ( ! defined( 'DONOTCACHEPAGE' ) ) {
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Standard cache-plugin opt-out constant.
				define( 'DONOTCACHEPAGE', true );
			}
			if ( ! headers_sent() ) {
				nocache_headers();
			}
		}
		if ( ! in_array( $scope, array( 'public', 'own', 'managers' ), true ) || ( 'own' === $scope && ! is_user_logged_in() ) || ( 'managers' === $scope && ! current_user_can( 'manage_woocommerce' ) ) ) {
			echo '<div class="oltew-orders oltew-empty" role="status">' . esc_html__( 'Please sign in with an authorized account to view orders.', 'order-list-table-elementor-widget' ) . '</div>';
			return;
		}
		$fields  = $this->oltew_visible_fields( $settings );
		$limit   = max( 1, min( 100, absint( $settings['list_per_page'] ?? 10 ) ) );
		$orders  = $this->oltew_page_result( $fields ? Oltew_Order_Query::get( $settings, $this->oltew_seen_ids() ) : array(), 'orders', $limit );
		$layout  = $this->get_layout();
		$classes = array( 'oltew-orders', 'oltew-order-list-table', 'oltew-layout-' . $layout );
		foreach ( array(
			'oltew_hover'             => 'oltew-hover',
			'oltew_shadow'            => 'oltew-shadow',
			'oltew_processing_spin'   => 'oltew-processing-spin',
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
		$toggle_field = isset( $fields['products'] ) ? 'products' : 'action';
		if ( $this->oltew_is_append() ) {
			$this->oltew_render_order_rows( $orders, $fields, $settings, $toggle_field );
			return;
		}
		$animation = $settings['oltew_animation'] ?? 'none';
		if ( in_array( $animation, array( 'fade', 'rise', 'scale' ), true ) ) {
			$classes[] = 'oltew-animate-' . $animation;
		}
		$head_align = in_array( $settings['oltew_table_th_alignment'] ?? '', array( 'left', 'center', 'right', 'justify' ), true ) ? $settings['oltew_table_th_alignment'] : 'left';
		$body_align = in_array( $settings['oltew_table_td_alignment'] ?? '', array( 'left', 'center', 'right', 'justify' ), true ) ? $settings['oltew_table_td_alignment'] : 'left';
		include OLTEW_PATH . 'templates/order-table.php';
	}

	/**
	 * Render a batch of rows for initial and Load More requests.
	 *
	 * @param array  $orders Order objects.
	 * @param array  $fields Visible fields.
	 * @param array  $settings Widget settings.
	 * @param string $toggle_field Expansion field.
	 */
	private function oltew_render_order_rows( $orders, $fields, $settings, $toggle_field ) {
		?>
							<?php foreach ( $orders as $index => $order ) : ?>
								<?php $row_id = wp_unique_id( 'oltew-order-' . $this->get_id() . '-' . $this->oltew_serial_offset() . '-' ); ?>
								<tr id="<?php echo esc_attr( $row_id ); ?>" class="oltew-item" style="--oltew-index: <?php echo absint( 'yes' === ( $settings['oltew_stagger'] ?? '' ) ? min( $index, 10 ) : 0 ); ?>;">
									<?php
									foreach ( $fields as $key => $field ) :
										?>
										<td class="<?php echo esc_attr( $field['class'] ); ?>" data-column="<?php echo esc_attr( $field['label'] ); ?>"><?php $this->oltew_render_value( $key, $order, $this->oltew_serial_offset() + $index, $settings, $row_id, $toggle_field ); ?></td><?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
		<?php
	}

	/**
	 * Render a field heading.
	 *
	 * @param array $field Field definition.
	 * @param array $settings Settings.
	 */
	private function oltew_render_heading( $field, $settings ) {
		echo '<span class="heading_content">';
		if ( 'yes' === ( $settings['oltew_heading_icons'] ?? 'yes' ) && isset( $field['icon'] ) ) {
			$this->oltew_icon( $settings[ $field['icon'] ] ?? array() );
		}
		echo '<span>' . esc_html( $field['label'] ) . '</span></span>';
	}

	/**
	 * Render an icon with Elementor's renderer.
	 *
	 * @param array  $icon Icon settings.
	 * @param string $wrapper_class Wrapper class.
	 */
	private function oltew_icon( $icon, $wrapper_class = 'order_list_icon' ) {
		if ( ! class_exists( '\Elementor\Icons_Manager' ) || ! is_array( $icon ) || empty( $icon['value'] ) ) {
			return;
		}
		// Normalize the original widget's Font Awesome 4 defaults in saved pages.
		if ( 'solid' === ( $icon['library'] ?? '' ) && is_string( $icon['value'] ) ) {
			$icon = \Elementor\Icons_Manager::fa4_to_fa5_value_migration( $icon['value'] );
		}
		if ( in_array( $icon['library'] ?? '', array( 'fa-solid', 'fa-regular', 'fa-brands' ), true ) ) {
			wp_enqueue_style( 'elementor-icons-' . $icon['library'] );
		}
		echo '<span class="' . esc_attr( $wrapper_class ) . '" aria-hidden="true">';
		\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
		echo '</span>';
	}

	/**
	 * Render a field with output escaping and privacy checks.
	 *
	 * @param string    $key Field identifier.
	 * @param \WC_Order $order Order object.
	 * @param int       $index Position.
	 * @param array     $settings Settings.
	 * @param string    $row_id Unique row identifier.
	 * @param string    $toggle_field Field that owns the expand button.
	 */
	private function oltew_render_value( $key, $order, $index, $settings, $row_id, $toggle_field ) {
		$icons = array(
			'customer' => 'customer_list_icon',
			'time'     => 'sell_list_time_icon',
			'phone'    => 'customer_phone_list_icon',
		);
		echo '<span class="body_content">';
		if ( 'yes' === ( $settings['oltew_body_icons'] ?? 'yes' ) && isset( $icons[ $key ] ) ) {
			$this->oltew_icon( $settings[ $icons[ $key ] ] ?? array() );
		}
		switch ( $key ) {
			case 'order':
				echo '<a href="' . esc_url( $order->get_view_order_url() ) . '">#' . esc_html( $order->get_order_number() ) . '</a>';
				break;
			case 'serial':
				echo esc_html( number_format_i18n( $index + 1 ) );
				break;
			case 'customer':
				$name = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
				$mode = $settings['oltew_customer_name'] ?? 'masked';
				if ( ! $name || 'anonymous' === $mode ) {
					$name = __( 'Customer', 'order-list-table-elementor-widget' );
				} elseif ( 'full' !== $mode || 'public' === ( $settings['oltew_scope'] ?? 'public' ) ) {
					$parts = preg_split( '/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY );
					$name  = implode(
						' ',
						array_map(
							function ( $part ) {
								return ( function_exists( 'mb_substr' ) ? mb_substr( $part, 0, 1 ) : wp_html_excerpt( $part, 1, '' ) ) . '.';
							},
							$parts
						)
					);
				}
				echo esc_html( $name );
				break;
			case 'phone':
				if ( 'oltew_account_orders' === $this->get_name() ) {
					echo esc_html( $order->get_billing_phone() );
					break;
				}
				$phone = preg_replace( '/\D+/', '', $order->get_billing_phone() );
				echo esc_html( strlen( $phone ) > 4 ? substr( $phone, 0, -4 ) . 'XXXX' : ( $phone ? 'XXXX' : '—' ) );
				break;
			case 'time':
				$date = $order->get_date_created();
				if ( $date ) {
					echo '<time datetime="' . esc_attr( $date->date( DATE_ATOM ) ) . '">';
					echo esc_html( oltew_format_order_date( $date, $settings['order_time_format'] ?? 'ago' ) );
					echo '</time>';
				} else {
					echo '—';
				}
				break;
			case 'amount':
				echo wp_kses_post( wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ) );
				break;
			case 'status':
				$status       = $order->get_status();
				$class        = in_array( $status, array( 'failed', 'cancelled' ), true ) ? 'cancelled' : $status;
				$status_icons = array(
					'completed'  => 'set_status_completed_icon',
					'processing' => 'set_status_processing_icon',
					'on-hold'    => 'set_status_onhold_icon',
					'failed'     => 'set_status_faild_icon',
					'cancelled'  => 'set_status_faild_icon',
					'refunded'   => 'set_status_refunded_icon',
				);
				echo '<span class="status_odd st_' . esc_attr( sanitize_html_class( $class ) ) . '">';
				if ( 'yes' === ( $settings['oltew_body_icons'] ?? 'yes' ) ) {
					$icon = $settings[ $status_icons[ $status ] ?? '' ] ?? array();
					// Replace the previous default waiting icon in existing saved widgets.
					if ( 'processing' === $status && is_array( $icon ) && in_array( $icon['value'] ?? '', array( 'fa fa-hourglass', 'fas fa-hourglass' ), true ) ) {
						$icon = array(
							'value'   => 'fas fa-circle-notch',
							'library' => 'fa-solid',
						);
					}
					$this->oltew_icon( $icon, 'status_icon' );
				}
				echo esc_html( wc_get_order_status_name( $status ) ) . '</span>';
				if ( 'refunded' !== $status && $order->get_total_refunded() > 0 ) {
					echo '<span class="status_odd st_refunded">' . esc_html__( 'Partially refunded', 'order-list-table-elementor-widget' ) . '</span>';
				}
				break;
			case 'action':
				if ( 'oltew_account_orders' === $this->get_name() ) {
					foreach ( wc_get_account_orders_actions( $order ) as $action ) {
						echo '<a class="button oltew-account-action" href="' . esc_url( $action['url'] ) . '">' . esc_html( $action['name'] ) . '</a> ';
					}
					break;
				}
				// Fall through to the product purchase actions.
			case 'products':
				$this->oltew_render_products( $key, $order, $settings, $row_id, $toggle_field );
				break;
		}
		echo '</span>';
	}

	/**
	 * Keep product and action positions identical, excluding private products.
	 *
	 * @param \WC_Order $order Order object.
	 * @return array
	 */
	private function oltew_display_items( $order ) {
		$items = array();
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();
			if ( $product && ! is_post_publicly_viewable( $product->get_parent_id() ? $product->get_parent_id() : $product->get_id() ) ) {
				if ( 'oltew_account_orders' !== $this->get_name() ) {
					continue;
				}
				$product = false;
			}
			$items[] = array(
				'item'    => $item,
				'product' => $product,
			);
		}
		return $items;
	}

	/**
	 * Render matched product/action lists, initially showing only the first item.
	 *
	 * @param string    $key Products or action field.
	 * @param \WC_Order $order Order object.
	 * @param array     $settings Widget settings.
	 * @param string    $row_id Unique row identifier.
	 * @param string    $toggle_field Field containing the row's single toggle.
	 */
	private function oltew_render_products( $key, $order, $settings, $row_id, $toggle_field ) {
		$items     = $this->oltew_display_items( $order );
		$collapsed = 'yes' === ( $settings['oltew_expand_products'] ?? 'yes' ) && count( $items ) > 1;
		echo '<span class="oltew-product-stack">';
		foreach ( $items as $position => $entry ) {
			$item    = $entry['item'];
			$product = $entry['product'];
			echo '<span class="oltew-product-entry"';
			if ( $collapsed && $position > 0 ) {
				echo ' data-oltew-extra hidden';
			}
			echo '>';
			if ( 'products' === $key ) {
				$visual = $settings['oltew_product_visual'] ?? 'icon';
				if ( 'image' === $visual && $product ) {
					echo wp_kses_post(
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
					);
				} elseif ( in_array( $visual, array( 'icon', 'image' ), true ) && 'yes' === ( $settings['oltew_body_icons'] ?? 'yes' ) ) {
					$this->oltew_icon( $settings['product_list_icon'] ?? array() );
				}
				if ( $product ) {
					$title = wp_trim_words( $item->get_name(), max( 1, min( 50, absint( $settings['product_link_words'] ?? 5 ) ) ) );
					echo '<a href="' . esc_url( $product->get_permalink() ) . '">' . esc_html( $title ) . '</a>';
				} else {
					echo '<span>' . esc_html( 'oltew_account_orders' === $this->get_name() ? $item->get_name() : __( 'Product unavailable', 'order-list-table-elementor-widget' ) ) . '</span>';
				}
			} elseif ( $product && ! $product->is_in_stock() ) {
				if ( 'yes' === ( $settings['oltew_show_stock_out'] ?? 'yes' ) ) {
					echo '<span class="status_odd oltew-order-stock-out">' . esc_html( $settings['oltew_stock_out_text'] ?? __( 'Out of stock', 'order-list-table-elementor-widget' ) ) . '</span>';
				}
			} elseif ( $product && $product->is_purchasable() ) {
				$url = $product->is_type( 'simple' ) ? add_query_arg( 'add-to-cart', $product->get_id(), wc_get_checkout_url() ) : $product->get_permalink();
				echo '<a class="buy-now-button" href="' . esc_url( $url ) . '" aria-label="' . esc_attr( ( $settings['buy_now_button_text'] ?? __( 'Buy Now', 'order-list-table-elementor-widget' ) ) . ': ' . $product->get_name() ) . '">';
				if ( 'yes' === ( $settings['oltew_body_icons'] ?? 'yes' ) ) {
					$this->oltew_icon( $settings['buy_now_list_icon'] ?? array(), 'buy_now_icon' );
				}
				echo esc_html( $settings['buy_now_button_text'] ?? __( 'Buy Now', 'order-list-table-elementor-widget' ) ) . '</a>';
			}
			echo '</span>';
		}
		echo '</span>';
		if ( $collapsed && $key === $toggle_field ) {
			echo '<button type="button" class="oltew-products-toggle" aria-expanded="false" aria-controls="' . esc_attr( $row_id ) . '"><span class="oltew-expand-label">';
			/* translators: 1: Expand button label, 2: Number of additional product lines. */
			echo esc_html( sprintf( __( '%1$s (%2$s)', 'order-list-table-elementor-widget' ), $settings['oltew_expand_text'] ?? __( 'Show more', 'order-list-table-elementor-widget' ), number_format_i18n( count( $items ) - 1 ) ) );
			echo '</span><span class="oltew-collapse-label" hidden>' . esc_html( $settings['oltew_collapse_text'] ?? __( 'Show less', 'order-list-table-elementor-widget' ) ) . '</span><span class="oltew-toggle-chevron" aria-hidden="true"></span></button>';
		}
	}
}
