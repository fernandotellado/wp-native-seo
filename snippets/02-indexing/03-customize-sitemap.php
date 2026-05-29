<?php
/**
 * Section 3 — Customise the native WordPress sitemap
 *
 * WordPress ships its own XML sitemap since 5.5 at /wp-sitemap.xml.
 * These filters let you exclude taxonomies, post types and specific posts
 * without any plugin.
 *
 * Workshop: WCEU 2026 — Do you really need an SEO/GEO plugin for WordPress?
 *
 * @package WCEU2026NativeSEO
 */

defined( 'ABSPATH' ) || exit;

/**
 * Exclude taxonomies from the sitemap.
 *
 * Tags often produce thin archives with one or two posts. Drop them here.
 */
add_filter(
	'wp_sitemaps_taxonomies',
	function ( $taxonomies ) {
		unset( $taxonomies['post_tag'] );
		// To exclude categories too: unset( $taxonomies['category'] );.
		return $taxonomies;
	}
);

/**
 * Exclude post types from the sitemap.
 *
 * Useful for landing pages, custom post types that shouldn't be indexed,
 * or attachments (already excluded by WordPress by default).
 */
add_filter(
	'wp_sitemaps_post_types',
	function ( $post_types ) {
		// Example: exclude a custom post type called 'landing'.
		unset( $post_types['landing'] );
		return $post_types;
	}
);

/**
 * Exclude individual posts by ID from the sitemap.
 *
 * Adds a NOT IN clause to the query that builds the sitemap.
 */
add_filter(
	'wp_sitemaps_posts_query_args',
	function ( $args, $post_type ) {
		if ( 'post' !== $post_type ) {
			return $args;
		}

		// IDs to exclude from the sitemap.
		$excluded_ids = array( 0, 0 ); // Replace with real post IDs.

		$args['post__not_in'] = $excluded_ids;
		return $args;
	},
	10,
	2
);

/**
 * Exclude the author archives sub-sitemap if you redirect authors anyway.
 */
add_filter(
	'wp_sitemaps_add_provider',
	function ( $provider, $name ) {
		if ( 'users' === $name ) {
			return false;
		}
		return $provider;
	},
	10,
	2
);
