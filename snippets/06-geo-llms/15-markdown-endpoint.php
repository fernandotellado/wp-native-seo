<?php
/**
 * Section 12b — Serve any post as Markdown via ?format=md
 *
 * AI models parse Markdown more efficiently than HTML. With a single query
 * argument, every post on the site is also reachable as a clean Markdown
 * document.
 *
 * Example: visit /your-post/?format=md and you get plain Markdown.
 *
 * Workshop: WCEU 2026 — Do you really need an SEO/GEO plugin for WordPress?
 *
 * @package WCEU2026NativeSEO
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'template_redirect',
	function () {
		if ( ! is_singular() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only public endpoint.
		$format = isset( $_GET['format'] ) ? sanitize_key( wp_unslash( $_GET['format'] ) ) : '';

		if ( 'md' !== $format ) {
			return;
		}

		$post = get_queried_object();
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		header( 'Content-Type: text/markdown; charset=utf-8' );
		header( 'X-Robots-Tag: noindex' );

		// Title.
		echo '# ' . wp_strip_all_tags( get_the_title( $post ) ) . "\n\n";

		// Meta block.
		$author = get_user_by( 'id', $post->post_author );
		if ( $author ) {
			echo "Author: {$author->display_name}\n";
		}
		echo 'Published: ' . mysql2date( 'Y-m-d', $post->post_date_gmt ) . "\n";
		echo 'Updated: ' . mysql2date( 'Y-m-d', $post->post_modified_gmt ) . "\n";
		echo 'URL: ' . get_permalink( $post ) . "\n\n";

		// Excerpt if available.
		if ( ! empty( $post->post_excerpt ) ) {
			echo '> ' . wp_strip_all_tags( $post->post_excerpt ) . "\n\n";
		}

		// Body: render blocks, strip HTML to plain text + line breaks.
		$content = apply_filters( 'the_content', $post->post_content );

		// Naive but workable HTML → Markdown conversion.
		$content = preg_replace( '#<h2[^>]*>(.*?)</h2>#is', "\n\n## $1\n\n", $content );
		$content = preg_replace( '#<h3[^>]*>(.*?)</h3>#is', "\n\n### $1\n\n", $content );
		$content = preg_replace( '#<h4[^>]*>(.*?)</h4>#is', "\n\n#### $1\n\n", $content );
		$content = preg_replace( '#<p[^>]*>(.*?)</p>#is', "$1\n\n", $content );
		$content = preg_replace( '#<li[^>]*>(.*?)</li>#is', "- $1\n", $content );
		$content = preg_replace( '#<strong[^>]*>(.*?)</strong>#is', '**$1**', $content );
		$content = preg_replace( '#<em[^>]*>(.*?)</em>#is', '*$1*', $content );
		$content = preg_replace( '#<a [^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)</a>#is', '[$2]($1)', $content );
		$content = preg_replace( '#<img [^>]*alt=["\']([^"\']*)["\'][^>]*src=["\']([^"\']+)["\'][^>]*>#is', '![$1]($2)', $content );
		$content = wp_strip_all_tags( $content );
		$content = html_entity_decode( $content, ENT_QUOTES, 'UTF-8' );

		echo trim( $content ) . "\n";

		exit;
	},
	1
);
