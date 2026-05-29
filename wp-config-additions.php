<?php
/**
 * wp-config.php additions — WCEU 2026 workshop
 *
 * Copy these constants into your real wp-config.php file. Place them ABOVE
 * the line that reads:
 *
 *   /* That's all, stop editing! Happy publishing. *\/
 *
 * Leave them commented out at first. Uncomment one by one during the demo
 * to walk through what each one does.
 *
 * Workshop: WCEU 2026 — Do you really need an SEO/GEO plugin for WordPress?
 */

// Cap post revisions at 5 per post (default: unlimited).
// define( 'WP_POST_REVISIONS', 5 );

// Empty the trash after 7 days (default: 30).
// define( 'EMPTY_TRASH_DAYS', 7 );

// Disable the theme/plugin file editor in the dashboard.
// If an admin account is compromised, attackers can't inject code through the UI.
// define( 'DISALLOW_FILE_EDIT', true );

/*
 * Bonus constants worth knowing about (not part of the live demo):
 *
 * define( 'AUTOSAVE_INTERVAL', 120 );
 *     WordPress autosaves every 60 seconds by default. 120 is plenty.
 *
 * define( 'DISABLE_WP_CRON', true );
 *     Disable the pseudo-cron that fires on every page load.
 *     Replace with a real server cron pointing to wp-cron.php every 5 minutes.
 *
 * define( 'WP_AUTO_UPDATE_CORE', 'minor' );
 *     Auto-update only minor releases. Safer than true or false.
 *
 * define( 'FORCE_SSL_ADMIN', true );
 *     Force HTTPS for admin area cookies and logins.
 *
 * define( 'WP_DEBUG', true );
 * define( 'WP_DEBUG_LOG', true );
 * define( 'WP_DEBUG_DISPLAY', false );
 *     Useful for development. Never leave WP_DEBUG_DISPLAY true in production.
 */
