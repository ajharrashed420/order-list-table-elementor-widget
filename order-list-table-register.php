<?php
/**
 * Compatibility entry point for the original widget file.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once __DIR__ . '/includes/class-oltew-load-more.php';
require_once __DIR__ . '/includes/trait-oltew-table-pagination.php';
require_once __DIR__ . '/includes/trait-oltew-table-design.php';
require_once __DIR__ . '/includes/trait-oltew-modern-widget.php';
require_once __DIR__ . '/includes/class-oltew-order-list-table-ele-widget.php';
