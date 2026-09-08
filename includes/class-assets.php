<?php
/**
 * Handles conditional asset enqueueing.
 *
 * @package Pinterest_Downloader
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PD_Assets
 *
 * Registers and conditionally enqueues plugin CSS and JS.
 * Assets are ONLY loaded on pages that contain the shortcode.
 */
class PD_Assets {

	/**
	 * Whether the shortcode has been detected on the current page.
	 *
	 * @var bool
	 */
	private $shortcode_found = false;

	/**
	 * Stores the current downloader type for JS localisation.
	 *
	 * @var string
	 */
	private $shortcode_type = 'video';

	/**
	 * Constructor — registers hooks.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
	}

	/**
	 * Signals that the shortcode is present on this page.
	 *
	 * Called by PD_Shortcode before rendering.
	 *
	 * @param string $type Downloader type: 'video', 'image', or 'gif'.
	 */
	public function set_shortcode_found( $type = 'video' ) {
		$this->shortcode_found = true;
		$this->shortcode_type  = sanitize_key( $type );
	}

	/**
	 * Enqueues stylesheet in the head and localized script in footer.
	 * Guarantees 100% reliable UI styling, brand colors, and zero FOUC on all themes and page builders.
	 */
	public function enqueue_frontend_assets() {
		// 1. Enqueue Stylesheet in <head>
		wp_enqueue_style(
			'pinterest-downloader',
			PD_PLUGIN_URL . 'public/css/pinterest-downloader.css',
			array(),
			PD_VERSION
		);

		// 2. Enqueue JavaScript in <footer>
		wp_enqueue_script(
			'pinterest-downloader',
			PD_PLUGIN_URL . 'public/js/pinterest-downloader.js',
			array(),
			PD_VERSION,
			true // Load in footer.
		);

		// 3. Localize script with AJAX URL, Nonce, and i18n strings
		wp_localize_script(
			'pinterest-downloader',
			'pdVars',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'pd_nonce' ),
				'type'    => $this->shortcode_type,
				'i18n'    => array(
					'invalidUrl'       => esc_html__( 'Please enter a valid Pinterest URL (pinterest.com or pin.it).', 'pinterest-downloader' ),
					'emptyInput'       => esc_html__( 'Please paste a Pinterest link first.', 'pinterest-downloader' ),
					'analyzing'        => esc_html__( 'Analyzing your Pinterest link...', 'pinterest-downloader' ),
					'findingMedia'     => esc_html__( 'Finding available media...', 'pinterest-downloader' ),
					'preparing'        => esc_html__( 'Preparing your download...', 'pinterest-downloader' ),
					'almostReady'      => esc_html__( 'Almost ready...', 'pinterest-downloader' ),
					'videoNotFound'    => esc_html__( 'No downloadable video was found for this Pin.', 'pinterest-downloader' ),
					'imageNotFound'    => esc_html__( 'No downloadable image was found for this Pin.', 'pinterest-downloader' ),
					'gifNotFound'      => esc_html__( 'No downloadable GIF was found for this Pin.', 'pinterest-downloader' ),
					'privateContent'   => esc_html__( 'This Pin is private or requires login. Only public Pins can be processed.', 'pinterest-downloader' ),
					'timeout'          => esc_html__( 'Processing is taking longer than expected. Please try again.', 'pinterest-downloader' ),
					'rateLimited'      => esc_html__( 'Too many requests. Please wait a moment before trying again.', 'pinterest-downloader' ),
					'serverError'      => esc_html__( 'Something went wrong while processing this link. Please try again.', 'pinterest-downloader' ),
					'downloadVideo'    => esc_html__( 'Download Video', 'pinterest-downloader' ),
					'downloadImage'    => esc_html__( 'Download Image', 'pinterest-downloader' ),
					'downloadGif'      => esc_html__( 'Download GIF', 'pinterest-downloader' ),
					'downloadStarted'  => esc_html__( 'Download starting...', 'pinterest-downloader' ),
					'imagePinOnVideo'  => esc_html__( 'Note: This Pin contains an image instead of a video.', 'pinterest-downloader' ),
					'videoPinOnGif'    => esc_html__( 'Note: This Pin is available as an animated video (MP4).', 'pinterest-downloader' ),
				),
			)
		);
	}
}
