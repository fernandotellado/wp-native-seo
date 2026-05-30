<?php
/**
 * Section 8 — Open Graph tags
 *
 * Outputs og:title, og:description, og:image, og:url, og:type, og:site_name.
 * Sources match what we already use for meta description and JSON-LD:
 *
 *  - og:title       → post title / site title.
 *  - og:description → excerpt / tagline / term description / bio.
 *  - og:image       → featured image / site icon fallback.
 *  - og:url         → permalink.
 *  - og:type        → article on singles, website everywhere else.
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

		$title       = '';
		$description = '';
		$url         = '';
		$image       = '';
		$type        = 'website';

		if ( is_front_page() || is_home() ) {
			$title       = get_bloginfo( 'name' );
			$description = get_bloginfo( 'description' );
			$url         = home_url( '/' );
			$image       = get_site_icon_url( 512 );
		} elseif ( is_singular() ) {
			$post  = get_queried_object();
			$title = get_the_title( $post );

			if ( ! empty( $post->post_excerpt ) ) {
				$description = $post->post_excerpt;
			} else {
				$description = wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '…' );
			}

			$url   = get_permalink( $post );
			$image = get_the_post_thumbnail_url( $post, 'full' );
			if ( ! $image ) {
				$image = get_site_icon_url( 512 );
			}

			$type = 'article';
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term        = get_queried_object();
			$title       = single_term_title( '', false );
			$description = $term ? wp_strip_all_tags( $term->description ) : '';
			$url         = get_term_link( $term );
			$image       = get_site_icon_url( 512 );
		} elseif ( is_author() ) {
			$author      = get_queried_object();
			$title       = $author->display_name;
			$description = get_the_author_meta( 'description', $author->ID );
			$url         = get_author_posts_url( $author->ID );
			$image       = get_avatar_url( $author->ID, array( 'size' => 256 ) );
			$type        = 'profile';
		}

		if ( $description && mb_strlen( $description ) > 200 ) {
			$description = mb_substr( $description, 0, 197 ) . '…';
		}

		printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( '<meta property="og:type" content="%s" />' . "\n", esc_attr( $type ) );
		printf( '<meta property="og:locale" content="%s" />' . "\n", esc_attr( str_replace( '-', '_', get_locale() ) ) );

		if ( $title ) {
			printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
		}
		if ( $description ) {
			printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $description ) );
		}
		if ( $url ) {
			printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ) );
		}
		if ( $image ) {
			printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $image ) );
		}

		// Article-specific extras.
		if ( 'article' === $type && is_singular( 'post' ) ) {
			$post = get_queried_object();
			printf( '<meta property="article:published_time" content="%s" />' . "\n", esc_attr( mysql2date( 'c', $post->post_date_gmt, false ) ) );
			printf( '<meta property="article:modified_time" content="%s" />' . "\n", esc_attr( mysql2date( 'c', $post->post_modified_gmt, false ) ) );

			$categories = get_the_category( $post );
			if ( ! empty( $categories ) ) {
				printf( '<meta property="article:section" content="%s" />' . "\n", esc_attr( $categories[0]->name ) );
			}
		}
	},
	2
);
