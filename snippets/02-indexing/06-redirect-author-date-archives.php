<?php
/**
 * Section 6 — Redirect author and date archives to the homepage
 *
 * On single-author sites, author archives duplicate the blog. Date archives
 * almost always do too. A 301 is cleaner than a noindex because it consolidates
 * link equity into the homepage instead of leaving dead-end URLs.
 *
 * If your site has multiple authors and you want to keep author archives,
 * comment out the first redirect.
 *
 * Workshop: WCEU 2026 — Do you really need an SEO/GEO plugin for WordPress?
 *
 * @package WCEU2026NativeSEO
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'template_redirect',
	function () {
		if ( is_admin() ) {
			return;
		}

		// Author archives → homepage (only for single-author sites).
		if ( is_author() ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}

		// Date archives → homepage.
		if ( is_date() ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	}
);

/**
 * Also remove the author and date base from the URL rewrites so old crawlers
 * don't keep hitting these URLs.
 *
 * Not strictly required, the redirect above already handles it, but it makes
 * the site cleaner.
 */
add_filter(
	'author_link',
	function ( $link ) {
		return home_url( '/' );
	}
);
