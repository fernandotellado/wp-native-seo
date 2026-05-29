<?php
/**
 * Section 4 — Auto meta description from WordPress fields
 *
 * Outputs <meta name="description"> on every page, pulling the text from
 * fields WordPress already has:
 *
 *  - Homepage  → site tagline (Settings → General).
 *  - Single    → manual excerpt, fallback to auto excerpt from content.
 *  - Archive   → term description (categories, tags, custom taxonomies).
 *  - Author    → biographical info from the user profile.
 *  - 404       → site tagline as last resort.
 *
 * Skips output if another SEO plugin already prints a meta description.
 *
 * Workshop: WCEU 2026 — Do you really need an SEO/GEO plugin for WordPress?
 *
 * @package WCEU2026NativeSEO
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_head',
	function () {
		// Do not output if a known SEO plugin is active and handling this.
		if (
			defined( 'WPSEO_VERSION' )     // Yoast.
			|| defined( 'RANK_MATH_VERSION' ) // Rank Math.
			|| defined( 'AIOSEO_VERSION' )    // AIOSEO.
		) {
			return;
		}

		$description = '';

		if ( is_front_page() || is_home() ) {
			$description = get_bloginfo( 'description' );
		} elseif ( is_singular() ) {
			$post = get_queried_object();
			if ( ! empty( $post->post_excerpt ) ) {
				$description = $post->post_excerpt;
			} else {
				$description = wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '…' );
			}
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term && ! empty( $term->description ) ) {
				$description = wp_strip_all_tags( $term->description );
			}
		} elseif ( is_author() ) {
			$author      = get_queried_object();
			$bio         = $author ? get_the_author_meta( 'description', $author->ID ) : '';
			$description = $bio ? $bio : sprintf( 'Posts by %s', $author->display_name );
		} elseif ( is_404() ) {
			$description = get_bloginfo( 'description' );
		}

		$description = trim( $description );

		if ( '' === $description ) {
			return;
		}

		// Hard cap at 160 chars, the practical limit for Google snippets.
		if ( mb_strlen( $description ) > 160 ) {
			$description = mb_substr( $description, 0, 157 ) . '…';
		}

		printf(
			'<meta name="description" content="%s" />' . "\n",
			esc_attr( $description )
		);
	},
	1
);
