<?php
/**
 * Section 9 — Twitter Card tags
 *
 * Outputs twitter:card, twitter:title, twitter:description, twitter:image.
 * Reuses the same logic as Open Graph; twitter:* tags act as a fallback when
 * a platform doesn't read og:* tags.
 *
 * If your site has a default Twitter / X account, set TWITTER_SITE_HANDLE
 * below (with the @).
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

		$twitter_handle = '@ayudawp'; // Change to your handle, or leave empty.

		$title       = '';
		$description = '';
		$image       = '';

		if ( is_front_page() || is_home() ) {
			$title       = get_bloginfo( 'name' );
			$description = get_bloginfo( 'description' );
			$image       = get_site_icon_url( 512 );
		} elseif ( is_singular() ) {
			$post  = get_queried_object();
			$title = get_the_title( $post );

			if ( ! empty( $post->post_excerpt ) ) {
				$description = $post->post_excerpt;
			} else {
				$description = wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '…' );
			}

			$image = get_the_post_thumbnail_url( $post, 'full' );
			if ( ! $image ) {
				$image = get_site_icon_url( 512 );
			}
		}

		if ( $description && mb_strlen( $description ) > 200 ) {
			$description = mb_substr( $description, 0, 197 ) . '…';
		}

		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";

		if ( $twitter_handle ) {
			printf( '<meta name="twitter:site" content="%s" />' . "\n", esc_attr( $twitter_handle ) );
		}

		if ( $title ) {
			printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );
		}
		if ( $description ) {
			printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $description ) );
		}
		if ( $image ) {
			printf( '<meta name="twitter:image" content="%s" />' . "\n", esc_url( $image ) );
		}
	},
	3
);
