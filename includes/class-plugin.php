<?php
/**
 * Core plugin orchestrator.
 *
 * @package Pinterest_Downloader
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PD_Plugin
 *
 * Singleton that wires together all sub-systems.
 */
class PD_Plugin {

	/**
	 * Single instance.
	 *
	 * @var PD_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Asset manager.
	 *
	 * @var PD_Assets
	 */
	public $assets;

	/**
	 * Shortcode handler.
	 *
	 * @var PD_Shortcode
	 */
	public $shortcode;

	/**
	 * Automated SEO and Schema manager.
	 *
	 * @var PD_SEO
	 */
	public $seo;

	/**
	 * Plugin settings manager.
	 *
	 * @var PD_Settings
	 */
	public $settings;

	/**
	 * Downloader engine.
	 *
	 * @var PD_Downloader_Engine
	 */
	public $engine;

	/**
	 * AJAX handler.
	 *
	 * @var PD_Ajax
	 */
	public $ajax;

	/**
	 * Returns the single instance of the plugin.
	 *
	 * @return PD_Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor — registers all hooks.
	 */
	private function __construct() {
		$this->settings  = new PD_Settings();
		$this->engine    = new PD_Downloader_Engine();
		$this->ajax      = new PD_Ajax( $this->engine );
		$this->assets    = new PD_Assets();
		$this->shortcode = new PD_Shortcode( $this->assets );
		$this->seo       = new PD_SEO();

		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'wp_head', array( $this, 'inject_google_analytics' ), 1 );
		add_filter( 'pd_rate_limit_max', array( $this, 'get_rate_limit' ) );
	}

	/**
	 * Load plugin text domain for translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'pinterest-downloader',
			false,
			dirname( PD_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Injects Google Analytics tracking code if GA ID is set.
	 */
	public function inject_google_analytics() {
		$ga_id = PD_Settings::get( 'google_analytics' );
		if ( empty( $ga_id ) ) {
			return;
		}
		$ga_id = sanitize_text_field( $ga_id );
		echo "\n<!-- PinDownloady Google Analytics -->\n";
		echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $ga_id ) . '"></script>' . "\n";
		echo '<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag(\'js\',new Date());gtag(\'config\',' . wp_json_encode( $ga_id ) . ');</script>' . "\n";
		echo "<!-- /PinDownloady Google Analytics -->\n\n";
	}

	/**
	 * Returns rate limit value from settings.
	 *
	 * @return int
	 */
	public function get_rate_limit() {
		return (int) PD_Settings::get( 'rate_limit' );
	}
}
