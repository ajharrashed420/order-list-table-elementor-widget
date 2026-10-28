<?php
/**
 * Administrator settings view.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$oltew_sections      = array(
	'widgets'    => array( __( 'Elementor widgets', 'order-list-table-elementor-widget' ), __( 'Choose the widgets available in Elementor. Disabling a widget also hides its existing tables and stops their Load More requests.', 'order-list-table-elementor-widget' ) ),
	'account'    => array( __( 'WooCommerce My Account', 'order-list-table-elementor-widget' ), __( 'Show each signed-in customer their own orders, with the same table layouts used by the Elementor widgets.', 'order-list-table-elementor-widget' ) ),
	'appearance' => array( __( 'Account table appearance', 'order-list-table-elementor-widget' ), __( 'Customize My Account colors. Elementor tables keep their individual design controls.', 'order-list-table-elementor-widget' ) ),
);
$oltew_section_icons = array(
	'widgets'    => 'screenoptions',
	'account'    => 'admin-users',
	'appearance' => 'art',
);
?>
<div class="wrap oltew-admin">
	<header class="oltew-admin-header">
		<div><p class="oltew-admin-eyebrow"><?php esc_html_e( 'WP METHODS / WOOCOMMERCE', 'order-list-table-elementor-widget' ); ?></p>
		<h1><?php esc_html_e( 'Order List Table', 'order-list-table-elementor-widget' ); ?></h1>
		<p><?php esc_html_e( 'Your tables. Your customer experience.', 'order-list-table-elementor-widget' ); ?></p></div>
		<span class="oltew-version"><?php echo esc_html( OLTEW_VERSION ); ?></span>
	</header>
	<hr class="wp-header-end">
	<?php settings_errors(); ?>
	<form action="options.php" method="post">
		<?php settings_fields( 'oltew_settings_group' ); ?>
		<div class="oltew-admin-grid">
			<aside class="oltew-admin-sidebar" aria-label="<?php esc_attr_e( 'Settings navigation and WP Methods resources', 'order-list-table-elementor-widget' ); ?>">
				<nav class="oltew-admin-nav" aria-label="<?php esc_attr_e( 'Settings sections', 'order-list-table-elementor-widget' ); ?>">
					<?php foreach ( $oltew_sections as $oltew_section => $oltew_content ) : ?>
						<a class="oltew-menu-item<?php echo 'widgets' === $oltew_section ? ' is-active' : ''; ?>" href="#oltew-<?php echo esc_attr( $oltew_section ); ?>" 
						<?php
						if ( 'widgets' === $oltew_section ) :
							?>
							aria-current="location"<?php endif; ?>>
							<span class="dashicons dashicons-<?php echo esc_attr( $oltew_section_icons[ $oltew_section ] ); ?>" aria-hidden="true"></span>
							<span><?php echo esc_html( $oltew_content[0] ); ?></span>
							<span class="dashicons dashicons-arrow-right-alt2 oltew-menu-arrow" aria-hidden="true"></span>
						</a>
					<?php endforeach; ?>
					<p class="oltew-account-privacy"><span class="dashicons dashicons-lock" aria-hidden="true"></span><?php esc_html_e( 'Account tables always show only the current customer’s orders.', 'order-list-table-elementor-widget' ); ?></p>
				</nav>
				<?php require OLTEW_PATH . 'templates/admin-resources.php'; ?>
			</aside>
			<div>
				<?php foreach ( $oltew_sections as $oltew_section => $oltew_content ) : ?>
					<section class="oltew-settings-card" id="oltew-<?php echo esc_attr( $oltew_section ); ?>">
						<h2><?php echo esc_html( $oltew_content[0] ); ?></h2><p class="oltew-section-description"><?php echo esc_html( $oltew_content[1] ); ?></p>
						<?php foreach ( $oltew_fields as $oltew_key => $oltew_field ) : ?>
							<?php
							if ( $oltew_section !== $oltew_field['section'] ) {
								continue;
							}
							$oltew_value    = $oltew_settings[ $oltew_key ];
							$oltew_name     = Oltew_Settings::OPTION . '[' . $oltew_key . ']';
							$oltew_field_id = 'oltew-setting-' . $oltew_key;
							?>
							<div class="oltew-setting-row">
								<?php if ( 'checkboxes' === $oltew_field['type'] ) : ?>
									<fieldset><legend><?php echo esc_html( $oltew_field['label'] ); ?></legend><div class="oltew-checkbox-grid">
									<?php foreach ( $oltew_field['options'] as $oltew_option => $oltew_label ) : ?>
										<label><input type="checkbox" name="<?php echo esc_attr( $oltew_name ); ?>[]" value="<?php echo esc_attr( $oltew_option ); ?>" <?php checked( in_array( $oltew_option, (array) $oltew_value, true ) ); ?>> <?php echo esc_html( $oltew_label ); ?></label>
									<?php endforeach; ?>
									</div>
									<?php
									if ( isset( $oltew_field['description'] ) ) :
										?>
										<p class="description"><?php echo esc_html( $oltew_field['description'] ); ?></p><?php endif; ?></fieldset>
								<?php else : ?>
									<label class="oltew-setting-label" for="<?php echo esc_attr( $oltew_field_id ); ?>"><?php echo esc_html( $oltew_field['label'] ); ?></label>
									<?php if ( 'toggle' === $oltew_field['type'] ) : ?>
										<span class="oltew-switch"><input type="checkbox" role="switch" id="<?php echo esc_attr( $oltew_field_id ); ?>" name="<?php echo esc_attr( $oltew_name ); ?>" value="yes" <?php checked( $oltew_value, 'yes' ); ?>><span aria-hidden="true"></span></span>
									<?php elseif ( 'select' === $oltew_field['type'] ) : ?>
										<select id="<?php echo esc_attr( $oltew_field_id ); ?>" name="<?php echo esc_attr( $oltew_name ); ?>">
										<?php
										foreach ( $oltew_field['options'] as $oltew_option => $oltew_label ) :
											?>
											<option value="<?php echo esc_attr( $oltew_option ); ?>" <?php selected( $oltew_value, $oltew_option ); ?>><?php echo esc_html( $oltew_label ); ?></option><?php endforeach; ?>
										</select>
									<?php else : ?>
										<input type="<?php echo 'number' === $oltew_field['type'] ? 'number' : 'text'; ?>" class="<?php echo 'color' === $oltew_field['type'] ? 'oltew-color' : 'regular-text'; ?>" id="<?php echo esc_attr( $oltew_field_id ); ?>" name="<?php echo esc_attr( $oltew_name ); ?>" value="<?php echo esc_attr( $oltew_value ); ?>" 
										<?php
										if ( 'number' === $oltew_field['type'] ) :
											?>
											min="1" max="100" step="1"<?php endif; ?>
											<?php
											if ( 'color' === $oltew_field['type'] ) :
												?>
											data-default-color="<?php echo esc_attr( $oltew_field['default'] ); ?>"<?php endif; ?>>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</section>
				<?php endforeach; ?>
				<div class="oltew-save-bar"><?php submit_button( __( 'Save settings', 'order-list-table-elementor-widget' ), 'primary', 'submit', false ); ?></div>
			</div>
		</div>
	</form>
</div>
