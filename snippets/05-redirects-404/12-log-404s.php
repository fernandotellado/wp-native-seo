<?php
/**
 * Section 11 — Log 404 errors to wp-content/404-log.txt
 *
 * Catches every 404 and appends a line with the timestamp, requested URI,
 * referrer and user agent to a dedicated text file at wp-content/404-log.txt.
 * Use it to build a list of broken inbound links worth redirecting, without
 * mixing them with the PHP error log.
 *
 * Skips obvious vulnerability probes (anything with wp-config, .env,
 * /vendor/, etc.) to avoid filling the file with garbage.
 *
 * Workshop: WCEU 2026 — Do you really need an SEO/GEO plugin for WordPress?
 *
 * @package WCEU2026NativeSEO
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'template_redirect',
	function () {
		if ( ! is_404() ) {
			return;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$referrer    = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '-';
		$user_agent  = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '-';

		// Skip obvious vulnerability probes — no need to log thousands of these.
		$noise = array( 'wp-config', '.env', '/vendor/', '/.git/', '/phpunit/', '/wp-includes/wlwmanifest' );
		foreach ( $noise as $needle ) {
			if ( false !== stripos( $request_uri, $needle ) ) {
				return;
			}
		}

		$line = sprintf(
			"[%s] URI: %s | Referrer: %s | UA: %s\n",
			gmdate( 'Y-m-d H:i:s' ),
			$request_uri,
			$referrer,
			$user_agent
		);

		// Write to wp-content/404-log.txt with the PHP error_log "append to file" mode.
		error_log( $line, 3, WP_CONTENT_DIR . '/404-log.txt' );
	}
);
