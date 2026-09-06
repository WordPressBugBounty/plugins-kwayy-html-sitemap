=== Kwayy HTML Sitemap ===
Contributors: bimaljr
Donate link: https://pbminfotech.com/
Tags: sitemap, html sitemap, custom post type, taxonomy, woocommerce
Requires at least: 5.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Generate a clean, SEO-friendly HTML sitemap for WordPress. Supports Posts, Pages, Custom Post Types, Taxonomies, and WooCommerce.

== Description ==

Generate a clean and SEO-friendly **HTML Sitemap** for your WordPress website.

Kwayy HTML Sitemap helps you create a complete sitemap page that improves website navigation for both visitors and search engines. Unlike XML sitemaps, this plugin creates a fully readable HTML sitemap page directly on your website.

The plugin supports:

* Pages
* Posts
* Custom Post Types
* Taxonomies (Categories, Tags, and Custom CPT Taxonomies)
* WooCommerce Products
* Any registered public post type

Simply add the shortcode below to any page and your sitemap will be generated automatically:

`[kwayy-sitemap]`

= Features =

* Easy-to-use settings panel (Settings -> Kwayy HTML Sitemap)
* Supports Custom Post Types and Taxonomies (Categories, Tags)
* WooCommerce product & product taxonomy support
* SEO-friendly HTML output with clean hierarchy
* Enable or disable specific post types and taxonomies
* Drag & Drop section reordering
* Custom display titles for post types and taxonomies
* Option to include CPT page / archive link as the first item
* Search and select specific posts, pages, or taxonomy terms to exclude
* Lightweight, fast performance, and responsive design

= Why Use HTML Sitemap? =

An HTML sitemap helps:

* Improve website navigation for visitors
* Increase internal linking and page crawlability
* Help search engines discover all pages, CPTs, and taxonomy archives
* Enhance overall user experience and SEO rankings

== Installation ==

1. Upload `kwayy-html-sitemap` to the `/wp-content/plugins/` directory, or install directly through the WordPress Plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Configure your post types, taxonomies, and exclusion options under **Settings -> Kwayy HTML Sitemap**.
4. Create a new page (e.g. "Sitemap") and insert shortcode `[kwayy-sitemap]`.
5. Publish the page and your HTML sitemap is live!

== Frequently Asked Questions ==

= How can I display the sitemap on my website? =
Simply create a new WordPress page (e.g., "Sitemap") and add the shortcode `[kwayy-sitemap]` to the page content.

= Can I choose which post types and taxonomies to show? =
Yes! In **Settings -> Kwayy HTML Sitemap**, you can check/uncheck each post type and taxonomy, and drag them to arrange your preferred display order.

= Can I exclude specific posts or categories? =
Yes, use the "Exclude Posts, Pages & Terms" multi-select box on the options page to search and exclude any specific post, page, or taxonomy term.

= How do I add a link to the main CPT archive page? =
In the settings table, check the "Add CPT Link" option next to any post type. The first link in that section will lead directly to the post type's archive page.

= Where can I get support? =
Visit our official support desk at [https://pbminfotech.support](https://pbminfotech.support).

== Screenshots ==

1. Settings -> Kwayy HTML Sitemap options page with drag & drop sorting, icons, and options.
2. Renaming custom display titles for post types and taxonomies.
3. Frontend HTML sitemap output generated with shortcode `[kwayy-sitemap]`.

== Changelog ==

= 5.1 =
* Security Fix: Added nonce verification (`wp_nonce_field` and `check_admin_referer`) to prevent Cross-Site Request Forgery (CSRF) on settings save (CVE-2026-65539).
* Security Fix: Added capability check (`manage_options`) and nonce verification (`check_ajax_referer`) to admin notice dismissal endpoint.
* Security Fix: Added direct file access protection (`defined( 'ABSPATH' ) || exit;`).
* Security Fix: Removed undefined role function in multisite check.
* Security Fix: Hardened input sanitization with `wp_unslash()`, strict ID integer filtering, and sort order whitelist validation.
* Security Fix: Enhanced output escaping with `esc_url()` for permalinks and `esc_attr()` for form input values.
* Security Fix: Hardened Select2 rendering and popover title rename against DOM XSS.
* Performance & Compatibility: Scoped Select2 and admin script enqueues strictly to the plugin settings page.

= 5.0 =
* Added support for Taxonomies (Categories, Tags, Custom CPT Taxonomies).
* Added Drag & Drop reordering for both CPTs and Taxonomies.
* Added option to include CPT archive/page URL as the first link in each CPT section.
* Added ability to exclude specific taxonomy terms alongside posts and pages.
* Added Select2 search and multi-select for easily excluding posts and terms with type icons.
* Redesigned options page with responsive layout, type icons, and tooltips.
* Cleaned up empty post types and empty taxonomy headings when no visible items exist.
* Added welcome admin notice directing users to Settings -> Kwayy HTML Sitemap.
* Compatibility tested up to WordPress 7.1 and PHP 8.2.

= 4.0 =
* Security enhancements.

= 3.1 =
* Small changes and added "Support Us" block on option page.

= 3.0 =
* Added support to customize display titles of custom post types.

= 2.0 =
* Added support to exclude posts.
* Added CSS classes to sitemap lists for custom design.
* Wrapped sitemap content in a container div.
* Redesigned options page.

= 1.0.8 =
* Replaced wrong shortcode description with correct details.

= 1.0.7 =
* Added sort order support to list all posts on sitemap page.

= 1.0.6 =
* Bug fix for links.

= 1.0.5 =
* Updated documentation and support details.

= 1.0.4 =
* Updated code to show posts in tree view.

= 1.0.3 =
* Bug fixes.

= 1.0.2 =
* Bug fixes.

= 1.0.1 =
* Initial release.

== Upgrade Notice ==

= 5.0 =
Major update featuring full taxonomy support, CPT archive link options, term exclusions, and modern admin UI.
