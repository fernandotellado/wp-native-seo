<?php
/**
 * Section 5 — noindex thin or low-value archive pages
 *
 * Adds <meta name="robots" content="noindex, follow"> to:
 *
 *  - Search results pages.
 *  - Paginated archives from page 2 onwards.
 *  - Tag archives with fewer than 3 posts.
 *  - 404 pages (WordPress already does this, made explicit).
 *
 * Skips when an SEO plugin is doing the same job, to avoid duplicate tags.
 *
 * Workshop: WCEU 2026 — Do you really need an SEO/GEO plugin for WordPress?
 *
 * @package WCEU2026NativeSEO
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_head',
	function () {
		if (
			defined( 'WPSEO_VERSION' )
			|| defined( 'RANK_MATH_VERSION' )
			|| defined( 'AIOSEO_VERSION' )
		) {
			return;
		}

		$should_noindex = false;

		if ( is_search() ) {
			$should_noindex = true;
		}

		if ( is_paged() ) {
			$should_noindex = true;
		}

		if ( is_404() ) {
			$should_noindex = true;
		}

		if ( is_tag() ) {
			$term = get_queried_object();
			if ( $term && $term->count < 3 ) {
				$should_noindex = true;
			}
		}

		if ( ! $should_noindex ) {
			return;
		}

		echo '<meta name="robots" content="noindex, follow" />' . "\n";
	},
	1
);
