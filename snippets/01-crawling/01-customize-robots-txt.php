<?php
/**
 * Section 1 — Customise the virtual robots.txt
 *
 * Appends extra rules to the robots.txt that WordPress generates on the fly.
 * No physical file needed. The Reading setting "Discourage search engines"
 * still wins over this filter.
 *
 * Workshop: WCEU 2026 — Do you really need an SEO/GEO plugin for WordPress?
 *
 * @package WCEU2026NativeSEO
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'robots_txt',
	function ( $output, $public ) {
		// If the site is set to private from Settings → Reading, respect it.
		if ( '0' === (string) $public ) {
			return $output;
		}

		$extra  = "\n";
		$extra .= "# Custom rules added natively via the robots_txt filter.\n";
		$extra .= "User-agent: *\n";
		$extra .= "Disallow: /wp-content/plugins/\n";
		$extra .= "Disallow: /wp-content/cache/\n";
		$extra .= "Disallow: /trackback/\n";
		$extra .= "Disallow: /xmlrpc.php\n";
		$extra .= "Disallow: /?s=\n";
		$extra .= "Disallow: /search/\n";

		return $output . $extra;
	},
	10,
	2
);
