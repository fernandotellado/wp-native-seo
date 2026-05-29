# Native WordPress SEO/GEO — WordCamp Europe 2026

Companion repository for the WordCamp Europe 2026 workshop:

> **Do you really need an SEO/GEO plugin for WordPress?**
> Fernando Tellado · AyudaWP.com · Kraków, June 2026.

Every native trick, snippet and reference shown live during the workshop, organised by what each SEO job actually does.

## How to read this repo

The workshop walks through six SEO jobs. Each folder under `snippets/` matches one of those jobs and contains the code we uncommented live on stage.

```
.
├── README.md                   ← you are here
├── demo-seo-tweaks.php         ← drop-in mu-plugin with every snippet
├── wp-config-additions.php     ← three SEO-relevant constants
├── htaccess-additions.txt      ← server-level rules
└── snippets/
    ├── 01-crawling/
    ├── 02-indexing/
    ├── 03-structured-data/
    ├── 04-social-sharing/
    ├── 05-redirects-404/
    └── 06-geo-llms/
```

## Quick start

1. Download `demo-seo-tweaks.php`.
2. Copy it into `wp-content/mu-plugins/` (create the folder if it doesn't exist).
3. Open the file. Every section is **commented out** by default.
4. Uncomment the sections you want active. Save the file. Reload your site.

That's it. The mu-plugin folder doesn't appear in the plugins screen and the file loads on every request, no activation needed.

> Important: most snippets check for Yoast SEO, Rank Math and AIOSEO and stay silent when one of those plugins is active. Use the snippets OR an SEO plugin, not both. Mixing both means duplicate meta tags.

## What each section covers

### Section 1 — Crawling

Who is allowed in.

- Customise the virtual `robots.txt` natively.
- Block AI training and answer-engine bots (GPTBot, ClaudeBot, Google-Extended, PerplexityBot, and others).
- When a physical `robots.txt` makes sense.
- htaccess rules for User-Agent blocking at the server level.

### Section 2 — Indexing

What enters the index and how it looks.

- Customise the native `wp-sitemap.xml`: exclude taxonomies, post types or specific IDs.
- Auto meta description from existing WordPress fields (tagline, excerpt, term description, biographical info).
- noindex thin and low-value pages (search results, paginated archives, tags with few posts).
- 301-redirect author and date archives.
- `wp-config.php` constants worth knowing: `WP_POST_REVISIONS`, `EMPTY_TRASH_DAYS`, `DISALLOW_FILE_EDIT`.

### Section 3 — Structured data

Telling machines what your content means.

- Minimal `Article` + `BreadcrumbList` JSON-LD built from existing WordPress fields.
- E-E-A-T inputs: user biographical info, social URLs, category descriptions.

### Section 4 — Social sharing

When somebody shares your post.

- Native Open Graph tags (`og:title`, `og:description`, `og:image`, `og:type`, `og:url`).
- Native Twitter Card tags.
- The block editor fields that feed image SEO (alt text, caption, featured image).

### Section 5 — Redirects and 404 management

When URLs change. When pages disappear.

- The unsung hero: `wp_old_slug_redirect()`, native and automatic since the early days.
- Manual 301s via `template_redirect`.
- 404 logging to the PHP error log to build a real list of broken inbound links.
- htaccess redirect blocks for large maps.

### Section 6 — The GEO layer

Speaking the language of AI.

- Serve `/llms.txt` natively (Markdown directory of your site for AI crawlers).
- Per-post Markdown endpoint with `?format=md`.
- Callback to AI bot rules in Section 1.

## What WordPress still can't do natively

The honest gaps where a focused plugin earns its place:

- Visual per-post override of meta title, meta description and canonical.
- Redirect manager with a UI.
- Advanced schema types (FAQ, HowTo, Product, Review).
- GEO stack with a UI: `llms.txt` builder, Markdown endpoint, AI bot analytics, Cloudflare AI Content Signals.

If you want clicks instead of code for any of these, see the plugin list below.

## Free plugins by AyudaWP that cover these gaps

All on WordPress.org. All free. Zero database tables. They coexist with Yoast and Rank Math.

| Job | Plugin |
|---|---|
| AI bots and Cloudflare AI Content Signals | [AI Content Signals](https://wordpress.org/plugins/ai-content-signals/) |
| Visual sitemap exclusion | [Native Sitemap Customizer](https://wordpress.org/plugins/native-sitemap-customizer/) |
| Per-post noindex with UI | [NoIndexer](https://wordpress.org/plugins/noindexer/) |
| Meta description + Open Graph + Twitter Card + JSON-LD with UI | [Native SEO Meta Tags](https://wordpress.org/plugins/native-seo-meta-tags/) |
| Per-post visibility control | [Post Visibility Control](https://wordpress.org/plugins/post-visibility-control/) |
| Share to ChatGPT, Claude, Perplexity | [AI Share & Summarize](https://wordpress.org/plugins/ai-share-summarize/) |
| Full GEO stack: llms.txt, Markdown endpoint, AI bot analytics | [VigIA](https://wordpress.org/plugins/vigia/) |

## What's new in WordPress 7

Native AI in the editor, via the canonical [AI Experiments](https://wordpress.org/plugins/?s=ai+experiments) plugin and supporting building blocks. Already generates alt text, excerpts, meta descriptions and titles natively. Install it on a test site and play with it.

## Reference SEO plugins mentioned during the talk

We compared the native approach against the two most installed SEO plugins:

- [Yoast SEO](https://wordpress.org/plugins/wordpress-seo/) — 13M+ installs.
- [Rank Math](https://wordpress.org/plugins/seo-by-rank-math/) — 3M+ installs.

Both are excellent products. The workshop is not against them. The point is that you don't *need* them for every SEO job — and now you know which jobs WordPress already handles for you.

## Support

Questions about anything in the repo? Open an issue. Pull requests welcome.

- Official website: [AyudaWP Servicios](https://servicios.ayudawp.com)
- Tutorials and articles: [ayudawp.com](https://ayudawp.com)
- YouTube: [AyudaWordPressES](https://www.youtube.com/AyudaWordPressES)

## About AyudaWP.com

We specialise in WordPress security, SEO, AI and performance optimization plugins. We create tools that solve real problems for WordPress site owners while maintaining the highest coding standards and accessibility requirements.

## Licence

All snippets and code in this repository are released under the GPL v2 or later, same licence as WordPress core.
