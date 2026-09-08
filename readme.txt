=== Pinterest Downloader ===

Contributors: yourname
Tags: pinterest, downloader, video downloader, image downloader, pinterest video
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A modern Pinterest media downloader tool supporting video and image download workflows.

== Description ==

Pinterest Downloader is a professional WordPress plugin that provides a beautiful, fast downloader interface for Pinterest media.

**Features:**

* Pinterest video downloader shortcode
* Pinterest image downloader shortcode
* Clean, mobile-first responsive design
* Accessible UI with ARIA support
* Inline URL validation
* Loading state with animated spinner
* Beautiful result & error cards
* FAQ accordion component
* How It Works section
* Features section
* Lightweight — no jQuery, no frameworks
* Conditional asset loading (CSS/JS only on pages that use the shortcode)

**Shortcodes:**

`[pinterest_downloader]` — defaults to video mode
`[pinterest_downloader type="video"]` — video downloader
`[pinterest_downloader type="image"]` — image downloader

**Usage:**

1. Install and activate the plugin.
2. Create a WordPress page.
3. Add the shortcode `[pinterest_downloader type="video"]` to the page content.
4. Publish the page.

The plugin provides the downloader tool only. You write your own SEO content above and below the shortcode.

== Installation ==

1. Download the plugin ZIP file.
2. Go to **WordPress Dashboard → Plugins → Add New → Upload Plugin**.
3. Upload the ZIP file and click **Install Now**.
4. Click **Activate Plugin**.
5. Add the shortcode to any page or post.

**Manual Installation:**

1. Upload the `pinterest-downloader` folder to `/wp-content/plugins/`.
2. Activate the plugin from the Plugins menu.

== Frequently Asked Questions ==

= How do I add the downloader to my page? =

Use the shortcode `[pinterest_downloader type="video"]` for video mode, or `[pinterest_downloader type="image"]` for image mode.

= Does this plugin require any API keys? =

No. Phase 1 is UI-only. API integration is handled in Phase 2.

= Will this conflict with my theme? =

The plugin uses scoped CSS selectors (`.pd-*`) and does not modify theme styles.

= Does it work on mobile? =

Yes. The plugin is fully responsive and optimised for all screen sizes from 320px and up.

== Changelog ==

= 1.0.0 =
* Initial release — Phase 1 UI foundation.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
