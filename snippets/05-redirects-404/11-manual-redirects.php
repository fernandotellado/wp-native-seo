<?php
/**
 * Section 10 — Manual 301 redirects via template_redirect
 *
 * For a small number of mappings, this is faster than installing a plugin.
 * For hundreds of redirects, use Redirection by John Godley or htaccess rules.
 *
 * Each rule maps a source URI to a destination. Sources are matched against
 * REQUEST_URI exactly (with and without trailing slash).
 *
 * Workshop: WCEU 2026 — Do you really need an SEO/GEO plugin for WordPress?
 *
 * @package WCEU2026NativeSEO
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'template_redirect',
	function () {
		// Only run on the frontend.
		if ( is_admin() ) {
			return;
		}

		// Map of old URI → new URL or path.
		$redirects = array(
			'/old-page/'          => '/new-page/',
			'/services/seo/'      => '/seo-services/',
			'/blog/2024/welcome/' => '/welcome/',
		);

		// Get current request URI without query string, normalised.
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$request_uri = strtok( $request_uri, '?' );

		// Normalise with trailing slash.
		$request_uri_with_slash = trailingslashit( $request_uri );

		foreach ( $redirects as $source => $destination ) {
			if ( $request_uri === $source || $request_uri_with_slash === trailingslashit( $source ) ) {
				$target = ( false !== strpos( $destination, 'http' ) )
					? $destination
					: home_url( $destination );

				wp_safe_redirect( $target, 301 );
				exit;
			}
		}
	},
	1
);
