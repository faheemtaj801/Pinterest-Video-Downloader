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
		$this->engine    = new PD_Downloader_Engine();
		$this->ajax      = new PD_Ajax( $this->engine );
		$this->assets    = new PD_Assets();
		$this->shortcode = new PD_Shortcode( $this->assets );
		$this->seo       = new PD_SEO();

		add_action( 'init', array( $this, 'load_textdomain' ) );
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
}
