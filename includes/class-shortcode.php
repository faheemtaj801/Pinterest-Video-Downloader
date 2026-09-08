<?php
/**
 * Shortcode registration and rendering.
 *
 * @package Pinterest_Downloader
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PD_Shortcode
 *
 * Registers [pinterest_downloader] and delegates rendering to template files.
 */
class PD_Shortcode {

	/**
	 * Reference to the asset manager.
	 *
	 * @var PD_Assets
	 */
	private $assets;

	/**
	 * Constructor — registers the shortcode.
	 *
	 * @param PD_Assets $assets Asset manager instance.
	 */
	public function __construct( PD_Assets $assets ) {
		$this->assets = $assets;
		add_shortcode( 'pinterest_downloader', array( $this, 'render' ) );
	}

	/**
	 * Renders the downloader shortcode output.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Enclosed content (unused).
	 * @return string         HTML output.
	 */
	public function render( $atts, $content = '' ) {
		$atts = shortcode_atts(
			array(
				'type'   => 'video', // Default to video mode.
				'layout' => 'full',  // 'full' (complete landing page) or 'tool' (just hero & download card)
			),
			$atts,
			'pinterest_downloader'
		);

		// Sanitise and validate the type attribute.
		$type = sanitize_key( $atts['type'] );
		if ( ! in_array( $type, array( 'video', 'image', 'gif' ), true ) ) {
			$type = 'video';
		}

		$layout = sanitize_key( $atts['layout'] );
		if ( ! in_array( $layout, array( 'full', 'tool' ), true ) ) {
			$layout = 'full';
		}

		// Signal to the asset manager that this shortcode is present.
		$this->assets->set_shortcode_found( $type );

		// Capture template output.
		ob_start();
		$this->load_template( 'downloader', array( 'type' => $type, 'layout' => $layout ) );
		return ob_get_clean();
	}

	/**
	 * Loads a template file with a given context.
	 *
	 * @param string $template Template name (without .php).
	 * @param array  $args     Variables to extract into the template scope.
	 */
	public function load_template( $template, $args = array() ) {
		$template_file = PD_PLUGIN_DIR . 'templates/' . $template . '.php';

		if ( ! file_exists( $template_file ) ) {
			return;
		}

		// Expose $args keys as local variables inside the template.
		if ( ! empty( $args ) ) {
			// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			extract( $args, EXTR_SKIP );
		}

		include $template_file;
	}
}
