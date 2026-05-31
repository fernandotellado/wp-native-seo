<?php
/**
 * Section 7c — ProfilePage + Person JSON-LD on author archives
 *
 * Section 7 outputs schema on single posts only. Author archive pages
 * (/author/{slug}/) are profile pages about a person — they deserve
 * their own schema. This snippet emits a ProfilePage with a Person as
 * mainEntity whenever WordPress is rendering an author archive.
 *
 * The Person object reuses the same data sources as Section 7:
 *
 *  - display_name              → Person.name
 *  - biographical info (bio)   → Person.description
 *  - author posts URL          → Person.url
 *  - Gravatar URL              → Person.image
 *  - Website field (user_url)  → Person.sameAs[0]
 *  - Mastodon, X, LinkedIn, GitHub, YouTube (via Section 7b filter)
 *                              → Person.sameAs[1..n]
 *
 * Without Section 7b, sameAs only carries the Website URL (one entry).
 * With Section 7b plus social URLs filled in, sameAs lists all of them.
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

		if ( ! is_author() ) {
			return;
		}

		$author = get_queried_object();
		if ( ! $author instanceof WP_User ) {
			return;
		}

		$person = array(
			'@type'       => 'Person',
			'name'        => $author->display_name,
			'description' => $author->description,
			'url'         => get_author_posts_url( $author->ID ),
			'image'       => get_avatar_url( $author->ID, array( 'size' => 256 ) ),
		);

		// Build sameAs from native Website URL + Section 7b extras.
		$same_as_keys = array( 'wporg', 'github', 'x', 'youtube', 'linkedin', 'mastodon' );
		$same_as      = array();

		if ( $author->user_url && filter_var( $author->user_url, FILTER_VALIDATE_URL ) ) {
			$same_as[] = esc_url_raw( $author->user_url );
		}

		foreach ( $same_as_keys as $key ) {
			$url = get_user_meta( $author->ID, $key, true );
			if ( $url && filter_var( $url, FILTER_VALIDATE_URL ) ) {
				$same_as[] = esc_url_raw( $url );
			}
		}

		if ( ! empty( $same_as ) ) {
			$person['sameAs'] = array_values( array_unique( $same_as ) );
		}

		$profile_page = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'ProfilePage',
			'mainEntity' => $person,
		);

		echo "\n" . '<script type="application/ld+json">' . "\n";
		echo wp_json_encode( $profile_page, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		echo "\n" . '</script>' . "\n";
	},
	5
);
