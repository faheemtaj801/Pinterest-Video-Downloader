<?php
/**
 * Plugin Settings Page — PinDownloady.
 *
 * Provides a simple WordPress admin settings page.
 * No Yoast or Rank Math required — all SEO is handled automatically.
 *
 * @package Pinterest_Downloader
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PD_Settings
 */
class PD_Settings {

	/**
	 * Option group / page slug.
	 */
	const OPTION_GROUP = 'pd_settings_group';
	const OPTION_NAME  = 'pd_settings';
	const PAGE_SLUG    = 'pd-settings';

	/**
	 * Constructor — register hooks.
	 */
	public function __construct() {
		add_action( 'admin_menu',    array( $this, 'add_menu_page' ) );
		add_action( 'admin_init',    array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
	}

	/**
	 * Returns saved settings with defaults.
	 *
	 * @return array
	 */
	public static function get( $key = null ) {
		$defaults = array(
			'site_name'         => 'PinDownloady',
			'tagline'           => 'Free Pinterest Video Downloader',
			'terms_url'         => '/terms-of-service/',
			'privacy_url'       => '/privacy-policy/',
			'contact_url'       => '/contact/',
			'video_page_url'    => '/pinterest-video-downloader/',
			'image_page_url'    => '/pinterest-image-downloader/',
			'gif_page_url'      => '/pinterest-gif-downloader/',
			'google_analytics'  => '',
			'rate_limit'        => 30,
			'show_ads'          => '0',
		);

		$saved = get_option( self::OPTION_NAME, array() );
		$opts  = wp_parse_args( $saved, $defaults );

		if ( $key ) {
			return isset( $opts[ $key ] ) ? $opts[ $key ] : null;
		}
		return $opts;
	}

	/**
	 * Adds the plugin menu page under "Settings" in WP Admin.
	 */
	public function add_menu_page() {
		add_options_page(
			'PinDownloady Settings',
			'PinDownloady',
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Enqueues admin-only styles.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_styles( $hook ) {
		if ( 'settings_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}
		// Inline admin styles for the settings page.
		wp_add_inline_style(
			'wp-admin',
			'
			.pd-admin-wrap { max-width: 820px; }
			.pd-admin-wrap h1 { display:flex; align-items:center; gap:12px; margin-bottom:24px; }
			.pd-admin-wrap h1 img { width:36px; height:36px; border-radius:8px; }
			.pd-section { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:24px 28px; margin-bottom:24px; }
			.pd-section h2 { margin:0 0 4px 0; font-size:16px; color:#111827; display:flex; align-items:center; gap:8px; }
			.pd-section .pd-section-desc { color:#6b7280; font-size:13px; margin:0 0 20px 0; }
			.pd-field-row { display:grid; grid-template-columns:200px 1fr; gap:12px 20px; align-items:center; margin-bottom:14px; }
			.pd-field-row label { font-weight:600; font-size:14px; color:#374151; }
			.pd-field-row input[type=text],
			.pd-field-row input[type=url],
			.pd-field-row input[type=number],
			.pd-field-row select { width:100%; border:1.5px solid #d1d5db; border-radius:8px; padding:8px 12px; font-size:14px; }
			.pd-field-row input:focus, .pd-field-row select:focus { border-color:#2563eb; outline:none; box-shadow:0 0 0 3px rgba(37,99,235,0.12); }
			.pd-field-hint { font-size:12px; color:#9ca3af; grid-column:2; margin-top:-8px; margin-bottom:6px; }
			.pd-seo-auto-badge { display:inline-flex; align-items:center; gap:6px; background:#d1fae5; color:#065f46; border:1px solid #6ee7b7; border-radius:9999px; padding:4px 12px; font-size:12px; font-weight:700; }
			.pd-seo-auto-badge svg { width:14px; height:14px; }
			.pd-save-btn { background:#2563eb; color:#fff; border:none; border-radius:8px; padding:10px 28px; font-size:15px; font-weight:700; cursor:pointer; }
			.pd-save-btn:hover { background:#1d4ed8; }
			@media (max-width:600px) { .pd-field-row { grid-template-columns:1fr; } .pd-field-hint { grid-column:1; } }
			'
		);
	}

	/**
	 * Registers WordPress settings.
	 */
	public function register_settings() {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);
	}

	/**
	 * Sanitizes and validates settings before saving.
	 *
	 * @param array $input Raw POST values.
	 * @return array Sanitized values.
	 */
	public function sanitize_settings( $input ) {
		$clean = array();

		$clean['site_name']        = sanitize_text_field( $input['site_name'] ?? 'PinDownloady' );
		$clean['tagline']          = sanitize_text_field( $input['tagline'] ?? '' );
		$clean['terms_url']        = sanitize_text_field( $input['terms_url'] ?? '/terms-of-service/' );
		$clean['privacy_url']      = sanitize_text_field( $input['privacy_url'] ?? '/privacy-policy/' );
		$clean['contact_url']      = sanitize_text_field( $input['contact_url'] ?? '/contact/' );
		$clean['video_page_url']   = sanitize_text_field( $input['video_page_url'] ?? '' );
		$clean['image_page_url']   = sanitize_text_field( $input['image_page_url'] ?? '' );
		$clean['gif_page_url']     = sanitize_text_field( $input['gif_page_url'] ?? '' );
		$clean['google_analytics'] = sanitize_text_field( $input['google_analytics'] ?? '' );
		$clean['rate_limit']       = min( 100, max( 5, intval( $input['rate_limit'] ?? 30 ) ) );

		return $clean;
	}

	/**
	 * Renders the full settings page HTML.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$opts     = self::get();
		$logo_url = PD_PLUGIN_URL . 'public/images/logo.png';

		?>
		<div class="wrap pd-admin-wrap">

			<h1>
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="PinDownloady" />
				PinDownloady — Settings
			</h1>

			<form method="post" action="options.php">
				<?php settings_fields( self::OPTION_GROUP ); ?>

				<!-- ── Section 1: Brand Settings ── -->
				<div class="pd-section">
					<h2>🏷️ Brand Settings</h2>
					<p class="pd-section-desc">Your site name and tagline. These appear in the plugin hero and SEO meta tags.</p>

					<div class="pd-field-row">
						<label for="pd_site_name">Site Name</label>
						<input type="text" id="pd_site_name" name="<?php echo self::OPTION_NAME; ?>[site_name]"
							value="<?php echo esc_attr( $opts['site_name'] ); ?>" placeholder="PinDownloady" />
					</div>

					<div class="pd-field-row">
						<label for="pd_tagline">Tagline</label>
						<input type="text" id="pd_tagline" name="<?php echo self::OPTION_NAME; ?>[tagline]"
							value="<?php echo esc_attr( $opts['tagline'] ); ?>" placeholder="Free Pinterest Video Downloader" />
					</div>
					<p class="pd-field-hint">Used in browser title and meta description.</p>
				</div>

				<!-- ── Section 2: Page URLs ── -->
				<div class="pd-section">
					<h2>🔗 Page URLs</h2>
					<p class="pd-section-desc">Enter the full URL or relative path of each downloader page. Used for internal navigation links.</p>

					<div class="pd-field-row">
						<label for="pd_video_url">Video Downloader Page</label>
						<input type="text" id="pd_video_url" name="<?php echo self::OPTION_NAME; ?>[video_page_url]"
							value="<?php echo esc_attr( $opts['video_page_url'] ); ?>" placeholder="/pinterest-video-downloader/" />
					</div>

					<div class="pd-field-row">
						<label for="pd_image_url">Image Downloader Page</label>
						<input type="text" id="pd_image_url" name="<?php echo self::OPTION_NAME; ?>[image_page_url]"
							value="<?php echo esc_attr( $opts['image_page_url'] ); ?>" placeholder="/pinterest-image-downloader/" />
					</div>

					<div class="pd-field-row">
						<label for="pd_gif_url">GIF Downloader Page</label>
						<input type="text" id="pd_gif_url" name="<?php echo self::OPTION_NAME; ?>[gif_page_url]"
							value="<?php echo esc_attr( $opts['gif_page_url'] ); ?>" placeholder="/pinterest-gif-downloader/" />
					</div>
				</div>

				<!-- ── Section 3: Legal Pages ── -->
				<div class="pd-section">
					<h2>📄 Legal Page Links</h2>
					<p class="pd-section-desc">Links shown in the "Terms of Service" note below the download box. Enter full URL or relative path.</p>

					<div class="pd-field-row">
						<label for="pd_terms">Terms of Service URL</label>
						<input type="text" id="pd_terms" name="<?php echo self::OPTION_NAME; ?>[terms_url]"
							value="<?php echo esc_attr( $opts['terms_url'] ); ?>" placeholder="/terms-of-service/" />
					</div>

					<div class="pd-field-row">
						<label for="pd_privacy">Privacy Policy URL</label>
						<input type="text" id="pd_privacy" name="<?php echo self::OPTION_NAME; ?>[privacy_url]"
							value="<?php echo esc_attr( $opts['privacy_url'] ); ?>" placeholder="/privacy-policy/" />
					</div>

					<div class="pd-field-row">
						<label for="pd_contact">Contact Page URL</label>
						<input type="text" id="pd_contact" name="<?php echo self::OPTION_NAME; ?>[contact_url]"
							value="<?php echo esc_attr( $opts['contact_url'] ); ?>" placeholder="/contact/" />
					</div>
				</div>

				<!-- ── Section 4: SEO (Automatic) ── -->
				<div class="pd-section">
					<h2>
						🔍 SEO Settings
						<span class="pd-seo-auto-badge">
							<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
							Fully Automatic — No Yoast or Rank Math Needed
						</span>
					</h2>
					<p class="pd-section-desc">
						✅ <strong>SEO is already working automatically.</strong> The plugin auto-generates:
					</p>
					<ul style="color:#374151; font-size:14px; margin:0 0 16px 24px; line-height:1.8;">
						<li>📝 Meta Title &amp; Description for each page</li>
						<li>📱 Open Graph tags (Facebook, WhatsApp preview)</li>
						<li>🐦 Twitter Card tags</li>
						<li>🔗 Canonical URL (prevents duplicate content)</li>
						<li>📊 Schema.org JSON-LD markup (Google Rich Results)</li>
					</ul>
					<p style="background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:12px 16px; font-size:13px; color:#92400e; margin:0;">
						⚠️ <strong>Note:</strong> If you have Yoast SEO or Rank Math installed and active, their settings will take priority. You can safely delete/deactivate them — this plugin handles everything.
					</p>
				</div>

				<!-- ── Section 5: Analytics (Optional) ── -->
				<div class="pd-section">
					<h2>📈 Google Analytics (Optional)</h2>
					<p class="pd-section-desc">Paste your Google Analytics Measurement ID to track visitors. Leave blank to skip.</p>

					<div class="pd-field-row">
						<label for="pd_ga">Measurement ID</label>
						<input type="text" id="pd_ga" name="<?php echo self::OPTION_NAME; ?>[google_analytics]"
							value="<?php echo esc_attr( $opts['google_analytics'] ); ?>" placeholder="G-XXXXXXXXXX" />
					</div>
					<p class="pd-field-hint">Format: G-XXXXXXXXXX (from analytics.google.com)</p>
				</div>

				<!-- ── Section 6: Rate Limiting ── -->
				<div class="pd-section">
					<h2>⚡ Rate Limiting</h2>
					<p class="pd-section-desc">Maximum number of download requests per visitor per minute. Prevents abuse. Default: 30.</p>

					<div class="pd-field-row">
						<label for="pd_rate">Max Requests / Minute</label>
						<input type="number" id="pd_rate" name="<?php echo self::OPTION_NAME; ?>[rate_limit]"
							value="<?php echo esc_attr( $opts['rate_limit'] ); ?>" min="5" max="100" />
					</div>
				</div>

				<!-- ── Shortcode Guide ── -->
				<div class="pd-section">
					<h2>📋 How to Use Shortcodes</h2>
					<p class="pd-section-desc">Copy and paste these shortcodes into any WordPress page or post.</p>

					<table style="width:100%; border-collapse:collapse; font-size:14px;">
						<thead>
							<tr style="background:#f9fafb; border-bottom:1.5px solid #e5e7eb;">
								<th style="padding:10px 14px; text-align:left; color:#374151;">Page</th>
								<th style="padding:10px 14px; text-align:left; color:#374151;">Shortcode</th>
							</tr>
						</thead>
						<tbody>
							<tr style="border-bottom:1px solid #f3f4f6;">
								<td style="padding:10px 14px; color:#374151;">📹 Video Downloader</td>
								<td style="padding:10px 14px;"><code style="background:#f3f4f6; padding:3px 8px; border-radius:5px; font-size:13px;">[pinterest_downloader type="video"]</code></td>
							</tr>
							<tr style="border-bottom:1px solid #f3f4f6;">
								<td style="padding:10px 14px; color:#374151;">🖼️ Image Downloader</td>
								<td style="padding:10px 14px;"><code style="background:#f3f4f6; padding:3px 8px; border-radius:5px; font-size:13px;">[pinterest_downloader type="image"]</code></td>
							</tr>
							<tr>
								<td style="padding:10px 14px; color:#374151;">🎞️ GIF Downloader</td>
								<td style="padding:10px 14px;"><code style="background:#f3f4f6; padding:3px 8px; border-radius:5px; font-size:13px;">[pinterest_downloader type="gif"]</code></td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- Save Button -->
				<p>
					<button type="submit" class="pd-save-btn">💾 Save Settings</button>
				</p>

			</form>
		</div>
		<?php
	}
}
