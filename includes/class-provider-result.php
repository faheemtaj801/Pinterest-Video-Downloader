<?php
/**
 * Normalized provider result object.
 *
 * @package Pinterest_Downloader
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PD_Provider_Result
 *
 * Encapsulates the normalized extraction response from any provider.
 */
class PD_Provider_Result {

	/**
	 * Whether extraction was successful.
	 *
	 * @var bool
	 */
	public $success = false;

	/**
	 * Media type: 'video', 'image', 'gif', or 'unsupported'.
	 *
	 * @var string
	 */
	public $media_type = 'unsupported';

	/**
	 * File format: 'mp4', 'jpg', 'png', 'gif', 'webp', etc.
	 *
	 * @var string
	 */
	public $format = '';

	/**
	 * Primary downloadable media URL.
	 *
	 * @var string
	 */
	public $media_url = '';

	/**
	 * Thumbnail / poster image URL.
	 *
	 * @var string
	 */
	public $thumbnail_url = '';

	/**
	 * Title of the Pin / media.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Width in pixels.
	 *
	 * @var int|null
	 */
	public $width = null;

	/**
	 * Height in pixels.
	 *
	 * @var int|null
	 */
	public $height = null;

	/**
	 * Formatted or numeric duration (for videos).
	 *
	 * @var string|int|null
	 */
	public $duration = null;

	/**
	 * Normalized source URL.
	 *
	 * @var string
	 */
	public $source_url = '';

	/**
	 * Error code if failed.
	 *
	 * @var string|null
	 */
	public $error_code = null;

	/**
	 * Human-readable error message.
	 *
	 * @var string|null
	 */
	public $error_message = null;

	/**
	 * Additional media variants or quality options.
	 *
	 * @var array
	 */
	public $variants = array();

	/**
	 * Factory helper for success response.
	 *
	 * @param array $args Result parameters.
	 * @return self
	 */
	public static function success( array $args = array() ) {
		$result               = new self();
		$result->success      = true;
		$result->media_type   = isset( $args['media_type'] ) ? sanitize_key( $args['media_type'] ) : 'image';
		$result->format       = isset( $args['format'] ) ? sanitize_key( $args['format'] ) : '';
		$result->media_url    = isset( $args['media_url'] ) ? esc_url_raw( $args['media_url'] ) : '';
		$result->thumbnail_url= isset( $args['thumbnail_url'] ) ? esc_url_raw( $args['thumbnail_url'] ) : '';
		$result->title        = isset( $args['title'] ) ? sanitize_text_field( $args['title'] ) : '';
		$result->width        = isset( $args['width'] ) ? intval( $args['width'] ) : null;
		$result->height       = isset( $args['height'] ) ? intval( $args['height'] ) : null;
		$result->duration     = isset( $args['duration'] ) ? sanitize_text_field( (string) $args['duration'] ) : null;
		$result->source_url   = isset( $args['source_url'] ) ? esc_url_raw( $args['source_url'] ) : '';
		$result->variants     = isset( $args['variants'] ) && is_array( $args['variants'] ) ? $args['variants'] : array();

		// Auto-infer format if not explicitly passed.
		if ( empty( $result->format ) && ! empty( $result->media_url ) ) {
			$ext = pathinfo( wp_parse_url( $result->media_url, PHP_URL_PATH ), PATHINFO_EXTENSION );
			$result->format = strtolower( sanitize_key( $ext ) );
		}

		return $result;
	}

	/**
	 * Factory helper for error response.
	 *
	 * @param string $error_code    Internal normalized error code.
	 * @param string $error_message User-friendly error message.
	 * @return self
	 */
	public static function error( $error_code, $error_message = '' ) {
		$result                = new self();
		$result->success       = false;
		$result->error_code    = sanitize_key( $error_code );
		$result->error_message = sanitize_text_field( $error_message );

		return $result;
	}

	/**
	 * Converts the result to an array for JSON response.
	 *
	 * @return array
	 */
	public function to_array() {
		if ( $this->success ) {
			return array(
				'media_type'    => $this->media_type,
				'format'        => $this->format,
				'media_url'     => $this->media_url,
				'thumbnail_url' => $this->thumbnail_url,
				'title'         => $this->title,
				'width'         => $this->width,
				'height'        => $this->height,
				'duration'      => $this->duration,
				'source_url'    => $this->source_url,
				'variants'      => $this->variants,
			);
		}

		return array(
			'error_code' => $this->error_code ? $this->error_code : 'UNKNOWN_ERROR',
			'message'    => $this->error_message ? $this->error_message : esc_html__( 'Unable to process this Pinterest link.', 'pinterest-downloader' ),
		);
	}
}
