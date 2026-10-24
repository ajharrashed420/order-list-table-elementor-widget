<?php
/**
 * Localized relative dates using UTC timestamps.
 *
 * @package OrderListTableElementorWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Format a date without relying on the server timezone.
 *
 * @param DateTimeInterface|int|string $timestamp Order date or timestamp.
 * @return string
 */
function oltew_ago_woo_list_table( $timestamp ) {
	if ( $timestamp instanceof DateTimeInterface ) {
		$timestamp = $timestamp->getTimestamp();
	} elseif ( ! is_numeric( $timestamp ) ) {
		try {
			$timestamp = ( new DateTimeImmutable( (string) $timestamp, wp_timezone() ) )->getTimestamp();
		} catch ( Exception $exception ) {
			return '';
		}
	}
	$timestamp = (int) $timestamp;
	$now       = time();
	if ( $timestamp <= 0 ) {
		return '';
	}
	if ( $timestamp >= $now - MINUTE_IN_SECONDS ) {
		return __( 'Just now', 'order-list-table-elementor-widget' );
	}
	/* translators: %s: Human-readable elapsed time. */
	return sprintf( __( '%s ago', 'order-list-table-elementor-widget' ), human_time_diff( $timestamp, $now ) );
}
