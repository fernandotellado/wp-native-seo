<?php
/**
 * Section 7b — Add social URL fields to the user profile
 *
 * WordPress profiles by default only have one URL field: "Website".
 * That's enough for Person.url in JSON-LD, but not for Person.sameAs.
 *
 * The user_contactmethods filter has been native since WP 2.9. It adds
 * extra URL fields to the user profile screen and stores them as user
 * meta. They are then read by Section 7 (the JSON-LD snippet) to feed
 * Person.sameAs.
 *
 * IMPORTANT: This snippet alone does NOT print anything in the page
 * source. It only adds form fields to the user profile. You must also
 * enable Section 7 (07-article-breadcrumb-jsonld.php) so the JSON-LD
 * output actually reads these fields and renders Person.sameAs.
 *
 * The slugs ('wporg', 'github', 'x', 'youtube', 'linkedin', 'mastodon')
 * must match the keys read in Section 7.
 *
 * Workshop: WCEU 2026 — Do you really need an SEO/GEO plugin for WordPress?
 *
 * @package WCEU2026NativeSEO
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'user_contactmethods',
	function ( $methods ) {
		$methods['wporg']    = 'WordPress.org profile URL';
		$methods['github']   = 'GitHub URL';
		$methods['x']        = 'X / Twitter URL';
		$methods['youtube']  = 'YouTube URL';
		$methods['linkedin'] = 'LinkedIn URL';
		$methods['mastodon'] = 'Mastodon URL';

		return $methods;
	}
);

/**
 * Read the values stored by the contact methods filter.
 *
 * Use this helper from the JSON-LD snippet (section 7) to build
 * Person.sameAs as an array of URLs.
 *
 * @param int $user_id User ID.
 * @return array
 */
function wceu_get_user_social_urls( $user_id ) {
	$keys = array( 'user_url', 'wporg', 'github', 'x', 'youtube', 'linkedin', 'mastodon' );
	$urls = array();

	foreach ( $keys as $key ) {
		$value = '';

		if ( 'user_url' === $key ) {
			// user_url is a column on the users table, not in user_meta.
			$user = get_user_by( 'id', $user_id );
			if ( $user && $user->user_url ) {
				$value = $user->user_url;
			}
		} else {
			$value = get_user_meta( $user_id, $key, true );
		}

		if ( $value && filter_var( $value, FILTER_VALIDATE_URL ) ) {
			$urls[] = esc_url_raw( $value );
		}
	}

	return $urls;
}
