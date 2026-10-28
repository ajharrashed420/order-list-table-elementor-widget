<?php
/**
 * Shared order table view. Variables are prepared by Oltew_Order_Table_Renderer.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$oltew_has_title    = ! empty( $settings['oltew_title'] ) && 'yes' === ( $settings['oltew_show_title'] ?? 'yes' );
$oltew_has_subtitle = ! empty( $settings['oltew_subtitle'] ) && 'yes' === ( $settings['oltew_show_subtitle'] ?? 'yes' );
$oltew_has_count    = 'yes' === ( $settings['oltew_show_count'] ?? '' );
?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="--oltew-head-align: <?php echo esc_attr( $head_align ); ?>; --oltew-body-align: <?php echo esc_attr( $body_align ); ?>;">
			<?php if ( 'yes' === ( $settings['oltew_show_heading'] ?? '' ) && ( $oltew_has_title || $oltew_has_subtitle || $oltew_has_count ) ) : ?>
				<div class="oltew-toolbar">
					<?php if ( $oltew_has_title || $oltew_has_subtitle ) : ?>
					<div>
						<?php
						if ( $oltew_has_title ) :
							?>
							<h3 class="oltew-title"><?php echo esc_html( $settings['oltew_title'] ); ?></h3><?php endif; ?>
						<?php
						if ( $oltew_has_subtitle ) :
							?>
							<p class="oltew-subtitle"><?php echo esc_html( $settings['oltew_subtitle'] ); ?></p><?php endif; ?>
					</div>
					<?php endif; ?>
					<?php
					if ( $oltew_has_count ) :
						?>
						<span class="oltew-count">
						<?php
						/* translators: %s: Number of orders displayed in this widget. */
						echo esc_html( sprintf( _n( '%s order', '%s orders', count( $orders ), 'order-list-table-elementor-widget' ), number_format_i18n( count( $orders ) ) ) );
						?>
					</span><?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( ! $orders && ! $this->oltew_has_more() ) : ?>
				<div class="oltew-empty oltew-surface" role="status"><?php echo esc_html( $settings['oltew_empty_text'] ?? __( 'No orders to display yet.', 'order-list-table-elementor-widget' ) ); ?></div>
			<?php else : ?>
				<div class="oltew-scroll oltew-surface" role="region" aria-label="<?php esc_attr_e( 'Recent orders table', 'order-list-table-elementor-widget' ); ?>" tabindex="0">
					<table class="oltew-table">
						<caption class="oltew-screen-reader"><?php echo esc_html( $settings['oltew_title'] ?? __( 'Recent orders', 'order-list-table-elementor-widget' ) ); ?></caption>
						<thead><tr>
						<?php
						foreach ( $fields as $oltew_field ) :
							?>
							<th scope="col"><?php $this->oltew_render_heading( $oltew_field, $settings ); ?></th><?php endforeach; ?></tr></thead>
						<tbody>
						<?php $this->oltew_render_order_rows( $orders, $fields, $settings, $toggle_field ); ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
			<?php $this->oltew_render_load_more( $settings ); ?>
		</div>
