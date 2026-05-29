<?php
/**
 * Plugin Name: WCEU 2026 — Demo SEO Tweaks
 * Description: Companion mu-plugin for the WordCamp Europe 2026 workshop "Do you really need an SEO/GEO plugin for WordPress?". Every section is COMMENTED OUT by default. Uncomment them live during the demo.
 * Version: 1.0.0
 * Author: Fernando Tellado
 * Author URI: https://ayudawp.com
 * License: GPL-2.0-or-later
 *
 * Place this file in wp-content/mu-plugins/. It is auto-loaded by WordPress
 * and cannot be deactivated from the dashboard.
 *
 * @package WCEU2026NativeSEO
 */

defined( 'ABSPATH' ) || exit;

/* =====================================================================
 * SECTION 1 — Crawling: customise the virtual robots.txt
 * ===================================================================== */
/*
add_filter(
	'robots_txt',
	function ( $output, $public ) {
		if ( '0' === (string) $public ) {
			return $output;
		}

		$extra  = "\n";
		$extra .= "# Custom rules added natively via the robots_txt filter.\n";
		$extra .= "User-agent: *\n";
		$extra .= "Disallow: /wp-content/plugins/\n";
		$extra .= "Disallow: /wp-content/cache/\n";
		$extra .= "Disallow: /trackback/\n";
		$extra .= "Disallow: /xmlrpc.php\n";
		$extra .= "Disallow: /?s=\n";
		$extra .= "Disallow: /search/\n";

		return $output . $extra;
	},
	10,
	2
);
*/

/* =====================================================================
 * SECTION 2 — Crawling: block AI bots
 * ===================================================================== */
/*
add_filter(
	'robots_txt',
	function ( $output, $public ) {
		if ( '0' === (string) $public ) {
			return $output;
		}

		$ai_bots = array(
			'GPTBot', 'ChatGPT-User', 'OAI-SearchBot',
			'ClaudeBot', 'anthropic-ai',
			'Google-Extended', 'PerplexityBot', 'CCBot',
			'Bytespider', 'Amazonbot', 'cohere-ai',
			'FacebookBot', 'Applebot-Extended',
		);

		$rules = "\n# Block AI training and answer-engine bots.\n";
		foreach ( $ai_bots as $bot ) {
			$rules .= "User-agent: {$bot}\nDisallow: /\n\n";
		}

		return $output . $rules;
	},
	20,
	2
);
*/

/* =====================================================================
 * SECTION 3 — Indexing: exclude taxonomies and post types from sitemap
 * ===================================================================== */
/*
add_filter(
	'wp_sitemaps_taxonomies',
	function ( $taxonomies ) {
		unset( $taxonomies['post_tag'] );
		return $taxonomies;
	}
);
*/

/* =====================================================================
 * SECTION 4 — Indexing: auto meta description
 * ===================================================================== */
/*
add_action(
	'wp_head',
	function () {
		if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
			return;
		}

		$description = '';

		if ( is_front_page() || is_home() ) {
			$description = get_bloginfo( 'description' );
		} elseif ( is_singular() ) {
			$post = get_queried_object();
			$description = ! empty( $post->post_excerpt )
				? $post->post_excerpt
				: wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '…' );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term && ! empty( $term->description ) ) {
				$description = wp_strip_all_tags( $term->description );
			}
		} elseif ( is_author() ) {
			$author = get_queried_object();
			$bio    = $author ? get_the_author_meta( 'description', $author->ID ) : '';
			$description = $bio ? $bio : sprintf( 'Posts by %s', $author->display_name );
		} elseif ( is_404() ) {
			$description = get_bloginfo( 'description' );
		}

		$description = trim( $description );
		if ( '' === $description ) {
			return;
		}
		if ( mb_strlen( $description ) > 160 ) {
			$description = mb_substr( $description, 0, 157 ) . '…';
		}

		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $description ) );
	},
	1
);
*/

/* =====================================================================
 * SECTION 5 — Indexing: noindex thin and low-value pages
 * ===================================================================== */
/*
add_action(
	'wp_head',
	function () {
		if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
			return;
		}

		$should_noindex = false;

		if ( is_search() )    { $should_noindex = true; }
		if ( is_paged() )     { $should_noindex = true; }
		if ( is_404() )       { $should_noindex = true; }

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
*/

/* =====================================================================
 * SECTION 6 — Indexing: redirect author and date archives
 * ===================================================================== */
/*
add_action(
	'template_redirect',
	function () {
		if ( is_admin() ) {
			return;
		}

		if ( is_author() ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}

		if ( is_date() ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	}
);
*/

/* =====================================================================
 * SECTION 7 — Structured data: Article + BreadcrumbList JSON-LD
 * ===================================================================== */
/*
add_action(
	'wp_head',
	function () {
		if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
			return;
		}
		if ( ! is_singular( 'post' ) ) {
			return;
		}

		$post   = get_queried_object();
		$author = get_user_by( 'id', $post->post_author );

		$image_url = get_the_post_thumbnail_url( $post, 'full' );
		$logo_url  = get_site_icon_url( 512 );

		$article = array(
			'@context'         => 'https://schema.org',
			'@type'            => 'Article',
			'headline'         => get_the_title( $post ),
			'datePublished'    => mysql2date( 'c', $post->post_date_gmt, false ),
			'dateModified'     => mysql2date( 'c', $post->post_modified_gmt, false ),
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
*/

/* =====================================================================
 * SECTION 8 — Social: Open Graph tags
 * ===================================================================== */
/*
add_action(
	'wp_head',
	function () {
		if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
			return;
		}

		$title = $description = $url = $image = '';
		$type  = 'website';

		if ( is_front_page() || is_home() ) {
			$title       = get_bloginfo( 'name' );
			$description = get_bloginfo( 'description' );
			$url         = home_url( '/' );
			$image       = get_site_icon_url( 512 );
		} elseif ( is_singular() ) {
			$post  = get_queried_object();
			$title = get_the_title( $post );

			$description = ! empty( $post->post_excerpt )
				? $post->post_excerpt
				: wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '…' );

			$url   = get_permalink( $post );
			$image = get_the_post_thumbnail_url( $post, 'full' );
			if ( ! $image ) {
				$image = get_site_icon_url( 512 );
			}
			$type = 'article';
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

		if ( 'article' === $type && is_singular( 'post' ) ) {
			$post = get_queried_object();
			printf( '<meta property="article:published_time" content="%s" />' . "\n", esc_attr( mysql2date( 'c', $post->post_date_gmt, false ) ) );
			printf( '<meta property="article:modified_time" content="%s" />' . "\n", esc_attr( mysql2date( 'c', $post->post_modified_gmt, false ) ) );
		}
	},
	2
);
*/

/* =====================================================================
 * SECTION 9 — Social: Twitter Card tags
 * ===================================================================== */
/*
add_action(
	'wp_head',
	function () {
		if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
			return;
		}

		$twitter_handle = '@ayudawp';

		$title = $description = $image = '';

		if ( is_front_page() || is_home() ) {
			$title       = get_bloginfo( 'name' );
			$description = get_bloginfo( 'description' );
			$image       = get_site_icon_url( 512 );
		} elseif ( is_singular() ) {
			$post  = get_queried_object();
			$title = get_the_title( $post );
			$description = ! empty( $post->post_excerpt )
				? $post->post_excerpt
				: wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '…' );
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
*/

/* =====================================================================
 * SECTION 10 — Redirects: manual 301 with template_redirect
 * ===================================================================== */
/*
add_action(
	'template_redirect',
	function () {
		if ( is_admin() ) {
			return;
		}

		$redirects = array(
			'/old-page/'          => '/new-page/',
			'/services/seo/'      => '/seo-services/',
			'/blog/2024/welcome/' => '/welcome/',
		);

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$request_uri = strtok( $request_uri, '?' );
		$request_uri_with_slash = trailingslashit( $request_uri );

		foreach ( $redirects as $source => $destination ) {
			if ( $request_uri === $source || $request_uri_with_slash === trailingslashit( $source ) ) {
				$target = ( false !== strpos( $destination, 'http' ) ) ? $destination : home_url( $destination );
				wp_safe_redirect( $target, 301 );
				exit;
			}
		}
	},
	1
);
*/

/* =====================================================================
 * SECTION 11 — Redirects: log 404s to error_log
 * ===================================================================== */
/*
add_action(
	'template_redirect',
	function () {
		if ( ! is_404() ) {
			return;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$referrer    = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '-';
		$user_agent  = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '-';

		$noise = array( 'wp-config', '.env', '/vendor/', '/.git/', '/phpunit/', '/wp-includes/wlwmanifest' );
		foreach ( $noise as $needle ) {
			if ( false !== stripos( $request_uri, $needle ) ) {
				return;
			}
		}

		error_log( sprintf( '[404] URI: %s | Referrer: %s | UA: %s', $request_uri, $referrer, $user_agent ) );
	}
);
*/

/* =====================================================================
 * SECTION 12 — GEO: serve /llms.txt and per-post Markdown endpoint
 * ===================================================================== */
/*
// 12a — /llms.txt endpoint.
add_action(
	'init',
	function () {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		if ( '/llms.txt' !== strtok( $request_uri, '?' ) ) {
			return;
		}

		$cache_key = 'wceu_llms_txt_output';
		$cached    = get_transient( $cache_key );

		if ( false === $cached ) {
			$site_name = get_bloginfo( 'name' );
			$site_desc = get_bloginfo( 'description' );
			$home      = home_url( '/' );

			$out  = "# {$site_name}\n\n> {$site_desc}\n\nSite: {$home}\nLast updated: " . gmdate( 'c' ) . "\n\n## Recent posts\n\n";

			$posts = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 50, 'no_found_rows' => true ) );

			foreach ( $posts as $post ) {
				$title   = wp_strip_all_tags( get_the_title( $post ) );
				$url     = get_permalink( $post );
				$excerpt = $post->post_excerpt ? wp_strip_all_tags( $post->post_excerpt ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 25, '…' );
				$out    .= "- [{$title}]({$url}): {$excerpt}\n";
			}

			$cached = $out;
			set_transient( $cache_key, $cached, HOUR_IN_SECONDS );
		}

		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'X-Robots-Tag: noindex' );
		echo $cached;
		exit;
	},
	1
);

// 12b — ?format=md endpoint.
add_action(
	'template_redirect',
	function () {
		if ( ! is_singular() ) {
			return;
		}

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

		echo '# ' . wp_strip_all_tags( get_the_title( $post ) ) . "\n\n";

		$author = get_user_by( 'id', $post->post_author );
		if ( $author ) {
			echo "Author: {$author->display_name}\n";
		}
		echo 'Published: ' . mysql2date( 'Y-m-d', $post->post_date_gmt ) . "\n";
		echo 'Updated: '   . mysql2date( 'Y-m-d', $post->post_modified_gmt ) . "\n";
		echo 'URL: '       . get_permalink( $post ) . "\n\n";

		if ( ! empty( $post->post_excerpt ) ) {
			echo '> ' . wp_strip_all_tags( $post->post_excerpt ) . "\n\n";
		}

		$content = apply_filters( 'the_content', $post->post_content );
		$content = preg_replace( '#<h2[^>]*>(.*?)</h2>#is', "\n\n## $1\n\n", $content );
		$content = preg_replace( '#<h3[^>]*>(.*?)</h3>#is', "\n\n### $1\n\n", $content );
		$content = preg_replace( '#<p[^>]*>(.*?)</p>#is', "$1\n\n", $content );
		$content = preg_replace( '#<li[^>]*>(.*?)</li>#is', "- $1\n", $content );
		$content = preg_replace( '#<strong[^>]*>(.*?)</strong>#is', '**$1**', $content );
		$content = preg_replace( '#<em[^>]*>(.*?)</em>#is', '*$1*', $content );
		$content = preg_replace( '#<a [^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)</a>#is', '[$2]($1)', $content );
		$content = wp_strip_all_tags( $content );
		$content = html_entity_decode( $content, ENT_QUOTES, 'UTF-8' );

		echo trim( $content ) . "\n";
		exit;
	},
	1
);
*/

/* =====================================================================
 * End of demo-seo-tweaks.php
 * ===================================================================== */
