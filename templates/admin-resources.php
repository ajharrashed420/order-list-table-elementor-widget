<?php
/**
 * Optional WP Methods resources for the plugin settings sidebar.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$oltew_products        = array(
	array(
		'name'        => 'Refmora',
		'url'         => 'https://wordpress.org/plugins/refmora/',
		'image'       => 'refmora.gif',
		'description' => __( 'Add a referral and affiliate program to WooCommerce.', 'order-list-table-elementor-widget' ),
		'type'        => __( 'Free plugin', 'order-list-table-elementor-widget' ),
		'source'      => 'WordPress.org',
	),
	array(
		'name'        => 'Social Chat Floating Icons',
		'url'         => 'https://wordpress.org/plugins/wpmethods-social-chat-floating-icons/',
		'image'       => 'social-chat.gif',
		'description' => __( 'Help visitors contact you through their favorite chat apps.', 'order-list-table-elementor-widget' ),
		'type'        => __( 'Free plugin', 'order-list-table-elementor-widget' ),
		'source'      => 'WordPress.org',
	),
	array(
		'name'        => 'Click Shop',
		'url'         => 'https://wpmethods.com/product/click-shop-wordpress-landing-page-type-ecommerce-theme/',
		'image'       => 'click-shop.webp',
		'description' => __( 'Create an ecommerce storefront with WooCommerce.', 'order-list-table-elementor-widget' ),
		'type'        => __( 'Paid theme', 'order-list-table-elementor-widget' ),
		'source'      => 'WPMethods.com',
	),
);
$oltew_community_links = array(
	array(
		'url'   => 'https://buymeacoffee.com/ajharrashed',
		'icon'  => 'coffee',
		'label' => __( 'Buy me a coffee', 'order-list-table-elementor-widget' ),
		'class' => 'oltew-coffee',
	),
	array(
		'url'   => 'https://www.youtube.com/@wpmethods',
		'icon'  => 'video-alt3',
		'label' => __( 'YouTube · WP Methods', 'order-list-table-elementor-widget' ),
		'class' => 'oltew-community-link',
	),
	array(
		'url'   => 'https://wpmethods.com',
		'icon'  => 'admin-home',
		'label' => __( 'Visit WP Methods', 'order-list-table-elementor-widget' ),
		'class' => 'oltew-community-link',
	),
);
?>
<section class="oltew-sidebar-card oltew-community" aria-labelledby="oltew-community-heading">
	<p class="oltew-sidebar-eyebrow"><?php esc_html_e( 'MADE WITH CARE', 'order-list-table-elementor-widget' ); ?></p>
	<h2 id="oltew-community-heading"><?php esc_html_e( 'Support & connect', 'order-list-table-elementor-widget' ); ?></h2>
	<p><?php esc_html_e( 'Enjoying the plugin? Support the creator or explore more tutorials and tools.', 'order-list-table-elementor-widget' ); ?></p>
	<?php foreach ( $oltew_community_links as $oltew_link ) : ?>
		<a class="<?php echo esc_attr( $oltew_link['class'] ); ?>" href="<?php echo esc_url( $oltew_link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
			<span class="dashicons dashicons-<?php echo esc_attr( $oltew_link['icon'] ); ?>" aria-hidden="true"></span>
			<span><?php echo esc_html( $oltew_link['label'] ); ?></span>
			<span class="dashicons dashicons-external oltew-link-arrow" aria-hidden="true"></span>
			<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'order-list-table-elementor-widget' ); ?></span>
		</a>
	<?php endforeach; ?>
</section>
<section class="oltew-sidebar-card oltew-products" aria-labelledby="oltew-products-heading">
	<h2 id="oltew-products-heading"><?php esc_html_e( 'Other products', 'order-list-table-elementor-widget' ); ?></h2>
	<p><?php esc_html_e( 'Optional tools from WP Methods.', 'order-list-table-elementor-widget' ); ?></p>
	<ul class="oltew-product-list">
		<?php foreach ( $oltew_products as $oltew_product ) : ?>
			<li>
				<a class="oltew-product-link" href="<?php echo esc_url( $oltew_product['url'] ); ?>" target="_blank" rel="noopener noreferrer">
					<?php if ( $oltew_product['image'] ) : ?>
						<img class="oltew-product-image" src="<?php echo esc_url( OLTEW_URL . 'assets/img/' . $oltew_product['image'] ); ?>" width="44" height="44" alt="" loading="lazy" decoding="async">
					<?php else : ?>
						<span class="oltew-product-image oltew-chat-icon dashicons dashicons-format-chat" aria-hidden="true"></span>
					<?php endif; ?>
					<span class="oltew-product-copy">
						<strong><?php echo esc_html( $oltew_product['name'] ); ?></strong>
						<span class="oltew-product-description"><?php echo esc_html( $oltew_product['description'] ); ?></span>
						<span class="oltew-product-meta"><span class="oltew-product-type"><?php echo esc_html( $oltew_product['type'] ); ?></span><span><?php echo esc_html( $oltew_product['source'] ); ?></span></span>
					</span>
					<span class="dashicons dashicons-external oltew-link-arrow" aria-hidden="true"></span>
					<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'order-list-table-elementor-widget' ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
