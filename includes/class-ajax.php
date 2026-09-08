<?php
/**
 * AJAX Request Handler.
 *
 * @package Pinterest_Downloader
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PD_Ajax
 *
 * Handles AJAX endpoints for media extraction and safe file downloading.
 */
class PD_Ajax {

	/**
	 * Downloader engine instance.
	 *
	 * @var PD_Downloader_Engine
	 */
	private $engine;

	/**
	 * Constructor.
	 *
	 * @param PD_Downloader_Engine $engine Downloader engine instance.
	 */
	public function __construct( PD_Downloader_Engine $engine ) {
		$this->engine = $engine;

		// Media extraction AJAX endpoints.
		add_action( 'wp_ajax_pd_download', array( $this, 'handle_extraction' ) );
		add_action( 'wp_ajax_nopriv_pd_download', array( $this, 'handle_extraction' ) );

		// Safe download redirect / streamer endpoint.
		add_action( 'wp_ajax_pd_file_download', array( $this, 'handle_file_download' ) );
		add_action( 'wp_ajax_nopriv_pd_file_download', array( $this, 'handle_file_download' ) );
	}

	/**
	 * Handles AJAX request to extract Pinterest media.
	 */
	public function handle_extraction() {
		// 1. Verify Nonce.
		check_ajax_referer( 'pd_nonce', 'nonce' );

		// 2. Validate input parameters.
		$url  = isset( $_POST['url'] ) ? sanitize_text_field( wp_unslash( $_POST['url'] ) ) : '';
		$type = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : 'video';

		if ( empty( $url ) ) {
			wp_send_json_error(
				array(
					'error_code' => 'EMPTY_URL',
					'message'    => esc_html__( 'Please provide a Pinterest URL.', 'pinterest-downloader' ),
				)
			);
		}

		// 3. Process with Downloader Engine.
		$result = $this->engine->process( $url, $type );

		// 4. Send response.
		if ( $result->success ) {
			wp_send_json_success( $result->to_array() );
		} else {
			wp_send_json_error( $result->to_array() );
		}
	}

	/**
	 * Handles safe media downloading / force-download headers for pinimg CDN media.
	 * Strictly limited to validated Pinterest media domains (no open proxy).
	 */
	public function handle_file_download() {
		check_ajax_referer( 'pd_nonce', 'nonce' );

		$media_url = isset( $_GET['media_url'] ) ? esc_url_raw( wp_unslash( $_GET['media_url'] ) ) : '';
		$filename  = isset( $_GET['filename'] ) ? sanitize_file_name( wp_unslash( $_GET['filename'] ) ) : 'pinterest-download';

		if ( empty( $media_url ) ) {
			wp_die( esc_html__( 'Invalid media URL.', 'pinterest-downloader' ), 400 );
		}

		// Verify that the media URL is strictly from Pinterest's official media CDNs.
		$host = wp_parse_url( $media_url, PHP_URL_HOST );
		if ( ! $host || ! preg_match( '/(^|\.)(pinimg\.com|pinterest\.com)$/i', $host ) ) {
			wp_die( esc_html__( 'Unauthorized media domain.', 'pinterest-downloader' ), 403 );
		}

		// Stream the file via a temp file with Content-Disposition headers to prevent PHP memory exhaustion.
		$tmp_file = wp_tempnam();
		$response = wp_safe_remote_get(
			$media_url,
			array(
				'timeout'     => 60,
				'stream'      => true,
				'filename'    => $tmp_file,
				'headers'     => array(
					'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
				),
				'sslverify'   => true,
			)
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			if ( file_exists( $tmp_file ) ) {
				@unlink( $tmp_file );
			}
			wp_die( esc_html__( 'Failed to retrieve media file from Pinterest.', 'pinterest-downloader' ), 500 );
		}

		$content_type = wp_remote_retrieve_header( $response, 'content-type' );
		$ext          = strtolower( pathinfo( wp_parse_url( $media_url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );

		if ( 'mp4' === $ext || false !== stripos( $media_url, '.mp4' ) ) {
			$content_type = 'video/mp4';
			if ( ! preg_match( '/\.mp4$/i', $filename ) ) {
				$filename .= '.mp4';
			}
		} elseif ( in_array( $ext, array( 'jpg', 'jpeg' ), true ) || false !== stripos( $content_type, 'jpeg' ) ) {
			$content_type = 'image/jpeg';
			if ( ! preg_match( '/\.jpe?g$/i', $filename ) ) {
				$filename .= '.jpg';
			}
		} elseif ( 'png' === $ext || false !== stripos( $content_type, 'png' ) ) {
			$content_type = 'image/png';
			if ( ! preg_match( '/\.png$/i', $filename ) ) {
				$filename .= '.png';
			}
		} elseif ( 'gif' === $ext || false !== stripos( $content_type, 'gif' ) ) {
			$content_type = 'image/gif';
			if ( ! preg_match( '/\.gif$/i', $filename ) ) {
				$filename .= '.gif';
			}
		} elseif ( empty( $content_type ) ) {
			$content_type = 'application/octet-stream';
		}

		$file_size = file_exists( $tmp_file ) ? filesize( $tmp_file ) : 0;

		// Clean all output buffers.
		while ( ob_get_level() ) {
			ob_end_clean();
		}

		nocache_headers();
		header( 'Content-Type: ' . $content_type );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		if ( $file_size > 0 ) {
			header( 'Content-Length: ' . $file_size );
		}

		if ( file_exists( $tmp_file ) ) {
			readfile( $tmp_file );
			@unlink( $tmp_file );
		}
		exit;
	}
}
