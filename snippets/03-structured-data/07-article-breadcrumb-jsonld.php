<?php
/**
 * Section 7 — Minimal JSON-LD: Article + BreadcrumbList
 *
 * Outputs structured data on single posts using fields WordPress already has:
 *
 *  - headline       → the post title.
 *  - author         → the user's display name and biographical info.
 *  - datePublished  → post_date_gmt in ISO 8601.
 *  - dateModified   → post_modified_gmt in ISO 8601.
 *  - image          → the featured image.
 *  - publisher      → Site Title + Site Icon.
 *  - breadcrumbs    → built from the post's main category hierarchy.
 *
 * Extend with FAQPage, HowTo, Product, Review as needed.
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

		if ( ! is_singular( 'post' ) ) {
			return;
		}

		$post   = get_queried_object();
		$author = get_user_by( 'id', $post->post_author );

		// Featured image, falls back to nothing if missing.
		$image_url = get_the_post_thumbnail_url( $post, 'full' );

		// Site logo: try Site Icon first.
		$logo_url = get_site_icon_url( 512 );

		$article = array(
			'@context'      => 'https://schema.org',
			'@type'         => 'Article',
			'headline'      => get_the_title( $post ),
			'datePublished' => mysql2date( 'c', $post->post_date_gmt, false ),
			'dateModified'  => mysql2date( 'c', $post->post_modified_gmt, false ),
			'mainEntityOfPage' => array(
				'@type' => 'WebPage',
				'@id'   => get_permalink( $post ),
			),
		);

		if ( $author ) {
			$article['author'] = array(
				'@type'       => 'Person',
				'name'        => $author->display_name,
				'description' => $author->description,
				'url'         => get_author_posts_url( $author->ID ),
			);

			// Person.sameAs — reads the URL fields from the user profile.
			// The Website field (user_url) is native. The rest only exist if
			// section 7b is also uncommented (user_contactmethods filter).
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
				$article['author']['sameAs'] = array_values( array_unique( $same_as ) );
			}
		}

		if ( $image_url ) {
			$article['image'] = $image_url;
		}

		$article['publisher'] = array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
		);

		if ( $logo_url ) {
			$article['publisher']['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => $logo_url,
			);
		}

		// BreadcrumbList from the primary category.
		$breadcrumb_items = array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => 'Home',
				'item'     => home_url( '/' ),
			),
		);

		$categories = get_the_category( $post );
		if ( ! empty( $categories ) ) {
			$primary  = $categories[0];
			$breadcrumb_items[] = array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => $primary->name,
				'item'     => get_category_link( $primary ),
			);

			$breadcrumb_items[] = array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title( $post ),
				'item'     => get_permalink( $post ),
			);
		} else {
			$breadcrumb_items[] = array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => get_the_title( $post ),
				'item'     => get_permalink( $post ),
			);
		}

		$breadcrumb = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $breadcrumb_items,
		);

		echo "\n" . '<script type="application/ld+json">' . "\n";
		echo wp_json_encode( $article, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		echo "\n" . '</script>' . "\n";

		echo '<script type="application/ld+json">' . "\n";
		echo wp_json_encode( $breadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		echo "\n" . '</script>' . "\n";
	},
	5
);
