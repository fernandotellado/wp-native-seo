<?php
/**
 * Section 11 — Log 404 errors to the PHP error log
 *
 * Catches every 404 and writes the requested URI, the referrer and the user
 * agent to the PHP error log. Use it to build a list of broken inbound
 * links worth redirecting.
 *
 * Skips bot referrers and obvious vulnerability probes (anything with
 * wp-config, .env, /vendor/, etc.) to avoid filling the log with garbage.
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

		error_log(
			sprintf(
				'[404] URI: %s | Referrer: %s | UA: %s',
				$request_uri,
				$referrer,
				$user_agent
			)
		);
	}
);
