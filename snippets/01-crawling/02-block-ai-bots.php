<?php
/**
 * Section 2 — Block AI bots in robots.txt
 *
 * Adds Disallow rules for the most common AI training crawlers.
 * Well-behaved bots respect these rules. Aggressive ones don't — for
 * those, see the htaccess User-Agent block in 04-htaccess-block-bots.txt.
 *
 * Workshop: WCEU 2026 — Do you really need an SEO/GEO plugin for WordPress?
 *
 * @package WCEU2026NativeSEO
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'robots_txt',
	function ( $output, $public ) {
		if ( '0' === (string) $public ) {
			return $output;
		}

		$ai_bots = array(
			'GPTBot',           // OpenAI.
			'ChatGPT-User',     // OpenAI on-demand fetch.
			'OAI-SearchBot',    // OpenAI search.
			'ClaudeBot',        // Anthropic.
			'anthropic-ai',     // Anthropic legacy.
			'Google-Extended',  // Google Bard / Gemini training opt-out.
			'PerplexityBot',    // Perplexity.
			'CCBot',            // Common Crawl.
			'Bytespider',       // ByteDance / TikTok.
			'Amazonbot',        // Amazon AI.
			'cohere-ai',        // Cohere.
			'FacebookBot',      // Meta AI.
			'Applebot-Extended', // Apple Intelligence training opt-out.
		);

		$rules = "\n# Block AI training and answer-engine bots.\n";

		foreach ( $ai_bots as $bot ) {
			$rules .= "User-agent: {$bot}\n";
			$rules .= "Disallow: /\n\n";
		}

		return $output . $rules;
	},
	20,
	2
);
