<?php
/**
 * Section 2.5 — wp-config.php constants that affect SEO
 *
 * Copy these three lines into your wp-config.php, ABOVE the line that says:
 *
 *   /* That's all, stop editing! Happy publishing. *\/
 *
 * Leave them commented out at first, uncomment one by one during the demo.
 *
 * Workshop: WCEU 2026 — Do you really need an SEO/GEO plugin for WordPress?
 *
 * @package WCEU2026NativeSEO
 */

// Cap post revisions at 5 per post (default: unlimited).
define( 'WP_POST_REVISIONS', 5 );

// Empty the trash after 7 days (default: 30 days).
define( 'EMPTY_TRASH_DAYS', 7 );

// Disable the theme/plugin file editor in the dashboard.
// If an admin account is compromised, attackers can't inject code through the UI.
define( 'DISALLOW_FILE_EDIT', true );

/*
 * Bonus constants worth knowing about (not used live during the workshop):
 *
 * define( 'AUTOSAVE_INTERVAL', 120 );
 *     // WordPress autosaves every 60 seconds by default. 120 is plenty.
 *
 * define( 'DISABLE_WP_CRON', true );
 *     // Disable the pseudo-cron that runs on every page load.
 *     // Replace with a real server cron pointing to wp-cron.php.
 *
 * define( 'WP_AUTO_UPDATE_CORE', 'minor' );
 *     // Auto-update only minor releases. Safer than 'true' or false.
 *
 * define( 'FORCE_SSL_ADMIN', true );
 *     // Force HTTPS for admin area cookies and logins.
 */
