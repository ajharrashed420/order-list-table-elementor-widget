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
	$timestamp = oltew_date_timestamp( $timestamp );
	$now       = time();
	if ( $timestamp <= 0 ) {
		return '';
	}
	if ( $timestamp > $now + MINUTE_IN_SECONDS ) {
		/* translators: %s: Human-readable time until the date. */
		return sprintf( __( 'In %s', 'order-list-table-elementor-widget' ), human_time_diff( $now, $timestamp ) );
	}
	if ( $timestamp >= $now - MINUTE_IN_SECONDS ) {
		return __( 'Just now', 'order-list-table-elementor-widget' );
	}
	/* translators: %s: Human-readable elapsed time. */
	return sprintf( __( '%s ago', 'order-list-table-elementor-widget' ), human_time_diff( $timestamp, $now ) );
}

/**
 * Convert a date to a UTC timestamp while respecting the WordPress timezone.
 *
 * @param DateTimeInterface|int|string $date Date object, timestamp or date string.
 * @return int
 */
function oltew_date_timestamp( $date ) {
	if ( $date instanceof DateTimeInterface ) {
		return max( 0, $date->getTimestamp() );
	}
	if ( is_numeric( $date ) ) {
		return max( 0, (int) $date );
	}
	if ( ! is_string( $date ) || '' === trim( $date ) ) {
		return 0;
	}
	try {
		return max( 0, ( new DateTimeImmutable( $date, wp_timezone() ) )->getTimestamp() );
	} catch ( Exception $exception ) {
		return 0;
	}
}

/**
 * Format order dates consistently across Elementor and My Account.
 *
 * @param DateTimeInterface|int|string $date Order date.
 * @param string                       $format Relative (ago) or site date (date).
 * @return string
 */
function oltew_format_order_date( $date, $format = 'date' ) {
	$timestamp = oltew_date_timestamp( $date );
	if ( ! $timestamp ) {
		return '';
	}
	return 'ago' === $format ? oltew_ago_woo_list_table( $timestamp ) : wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timestamp );
}
