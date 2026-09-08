<?php
/**
 * Downloader Engine orchestrator.
 *
 * @package Pinterest_Downloader
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PD_Downloader_Engine
 *
 * Coordinates URL validation, rate limiting, provider selection,
 * and media type detection across shortcode modes.
 */
class PD_Downloader_Engine {

	/**
	 * Registered extraction providers.
	 *
	 * @var PD_Provider_Interface[]
	 */
	private $providers = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Register default Pinterest provider.
		$this->register_provider( new PD_Pinterest_Provider() );

		// Allow plugins/add-ons to register additional providers via hook.
		do_action( 'pd_register_providers', $this );
	}

	/**
	 * Registers an extraction provider.
	 *
	 * @param PD_Provider_Interface $provider Provider instance.
	 */
	public function register_provider( PD_Provider_Interface $provider ) {
		$this->providers[ $provider->get_id() ] = $provider;
	}

	/**
	 * Main processing entry point.
	 *
	 * @param string $url            User-submitted URL.
	 * @param string $requested_type Requested mode: 'video' or 'image'.
	 * @return PD_Provider_Result
	 */
	public function process( $url, $requested_type = 'video' ) {
		// 1. Sanitize and validate input URL.
		$normalized_url = $this->normalize_url( $url );
		if ( ! $normalized_url ) {
			return PD_Provider_Result::error(
				'INVALID_URL',
				esc_html__( 'Please enter a valid Pinterest link (e.g. pinterest.com or pin.it).', 'pinterest-downloader' )
			);
		}

		// 2. Lightweight Rate Limiting (transient-based per IP).
		if ( ! $this->check_rate_limit() ) {
			return PD_Provider_Result::error(
				'RATE_LIMITED',
				esc_html__( 'Too many requests. Please wait a moment before trying again.', 'pinterest-downloader' )
			);
		}

		// 3. Find a supporting provider.
		$provider = $this->get_supporting_provider( $normalized_url );
		if ( ! $provider ) {
			return PD_Provider_Result::error(
				'UNSUPPORTED_URL',
				esc_html__( 'This URL is not supported. Please provide a standard public Pinterest Pin link.', 'pinterest-downloader' )
			);
		}

		// 4. Execute extraction.
		$result = $provider->extract( $normalized_url, $requested_type );

		// 5. Handle mode-specific semantics.
		if ( $result->success ) {
			$result = $this->apply_mode_context( $result, $requested_type );
		}

		// 6. Debug logging if enabled.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && ! $result->success ) {
			error_log( sprintf( '[Pinterest Downloader] Extraction failed for URL (%s): [%s] %s', esc_url_raw( $normalized_url ), $result->error_code, $result->error_message ) );
		}

		return $result;
	}

	/**
	 * Normalizes and strictly validates a Pinterest URL to prevent SSRF or malformed input.
	 *
	 * @param string $url Input URL.
	 * @return string|false Sanitized URL or false on invalid format.
	 */
	public function normalize_url( $url ) {
		if ( empty( $url ) || ! is_string( $url ) ) {
			return false;
		}

		$url = trim( $url );

		// Add protocol if missing.
		if ( ! preg_match( '#^https?://#i', $url ) ) {
			$url = 'https://' . $url;
		}

		// Validate URL format.
		$parsed = wp_parse_url( $url );
		if ( ! $parsed || empty( $parsed['host'] ) ) {
			return false;
		}

		$host = strtolower( $parsed['host'] );

		// Strict whitelist of allowed Pinterest hosts.
		$is_valid_host = (
			'pin.it' === $host ||
			'www.pin.it' === $host ||
			'pinterest.com' === $host ||
			'www.pinterest.com' === $host ||
			preg_match( '/(^|\.)pinterest\.[a-z]{2,3}(\.[a-z]{2})?$/i', $host )
		);

		if ( ! $is_valid_host ) {
			return false;
		}

		// Reject private IP / loopback / SSRF attempts.
		$ip = gethostbyname( $host );
		if ( $ip && ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
			return false;
		}

		return esc_url_raw( $url );
	}

	/**
	 * Finds the first registered provider that supports the URL.
	 *
	 * @param string $url Normalized URL.
	 * @return PD_Provider_Interface|null
	 */
	private function get_supporting_provider( $url ) {
		foreach ( $this->providers as $provider ) {
			if ( $provider->supports( $url ) ) {
				return $provider;
			}
		}
		return null;
	}

	/**
	 * Applies requested shortcode mode nuances to the normalized result.
	 *
	 * @param PD_Provider_Result $result         Extracted result.
	 * @param string             $requested_type 'video' or 'image'.
	 * @return PD_Provider_Result
	 */
	private function apply_mode_context( PD_Provider_Result $result, $requested_type ) {
		// Contextual adjustments can be added here if needed without modifying provider core.
		return $result;
	}

	/**
	 * Lightweight rate limiting check (30 requests per minute per IP).
	 *
	 * @return bool True if within limits, false if rate limited.
	 */
	private function check_rate_limit() {
		// Allow filtering / disabling rate limits for testing or custom needs.
		if ( apply_filters( 'pd_disable_rate_limiting', false ) ) {
			return true;
		}

		$ip = ! empty( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '127.0.0.1';
		$transient_key = 'pd_rate_' . md5( $ip );
		$count         = get_transient( $transient_key );

		if ( false === $count ) {
			set_transient( $transient_key, 1, 60 ); // 1 minute window.
			return true;
		}

		$max_requests = apply_filters( 'pd_rate_limit_max', 30 );

		if ( intval( $count ) >= $max_requests ) {
			return false;
		}

		set_transient( $transient_key, intval( $count ) + 1, 60 );
		return true;
	}
}
