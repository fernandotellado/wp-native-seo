<?php
/**
 * Section 12a — Serve /llms.txt natively
 *
 * llms.txt is a Markdown-based directory of a site's content, written for
 * language models. It works like robots.txt + sitemap.xml, but designed for
 * AI crawlers and answer engines.
 *
 * This snippet listens for requests to /llms.txt, generates a Markdown
 * document on the fly with the site identity and a list of recent posts,
 * and serves it as text/plain.
 *
 * Spec: https://llmstxt.org/
 *
 * Workshop: WCEU 2026 — Do you really need an SEO/GEO plugin for WordPress?
 *
 * @package WCEU2026NativeSEO
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		// Match /llms.txt at the root, with or without query string.
		if ( '/llms.txt' !== strtok( $request_uri, '?' ) ) {
			return;
		}

		// Cache key — refresh once an hour.
		$cache_key = 'wceu_llms_txt_output';
		$cached    = get_transient( $cache_key );

		if ( false === $cached ) {
			$cached = wceu_build_llms_txt();
			set_transient( $cache_key, $cached, HOUR_IN_SECONDS );
		}

		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'X-Robots-Tag: noindex' );

		echo $cached; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markdown body.
		exit;
	},
	1
);

/**
 * Build the Markdown content for /llms.txt.
 *
 * @return string
 */
function wceu_build_llms_txt() {
	$site_name = get_bloginfo( 'name' );
	$site_desc = get_bloginfo( 'description' );
	$home      = home_url( '/' );

	$out  = "# {$site_name}\n\n";
	$out .= "> {$site_desc}\n\n";
	$out .= "Site: {$home}\n";
	$out .= 'Last updated: ' . gmdate( 'c' ) . "\n\n";

	$out .= "## Recent posts\n\n";

	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'no_found_rows'  => true,
		)
	);

	foreach ( $posts as $post ) {
		$title   = wp_strip_all_tags( get_the_title( $post ) );
		$url     = get_permalink( $post );
		$excerpt = $post->post_excerpt
			? wp_strip_all_tags( $post->post_excerpt )
			: wp_trim_words( wp_strip_all_tags( $post->post_content ), 25, '…' );

		$out .= "- [{$title}]({$url}): {$excerpt}\n";
	}

	$out .= "\n## Pages\n\n";

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	foreach ( $pages as $page ) {
		$title = wp_strip_all_tags( get_the_title( $page ) );
		$url   = get_permalink( $page );
		$out  .= "- [{$title}]({$url})\n";
	}

	return $out;
}

/**
 * Flush the llms.txt cache whenever a post is saved.
 */
add_action(
	'save_post',
	function () {
		delete_transient( 'wceu_llms_txt_output' );
	}
);
