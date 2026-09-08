<?php
/**
 * Provider interface contract.
 *
 * @package Pinterest_Downloader
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface PD_Provider_Interface
 *
 * All extraction providers must implement this contract to ensure
 * modularity and clean pluggability for future extraction sources.
 */
interface PD_Provider_Interface {

	/**
	 * Returns the unique identifier of the provider.
	 *
	 * @return string
	 */
	public function get_id();

	/**
	 * Returns the human-readable name of the provider.
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Checks if this provider can handle the given URL.
	 *
	 * @param string $url Normalized Pinterest URL.
	 * @return bool
	 */
	public function supports( $url );

	/**
	 * Extracts media information from the given URL.
	 *
	 * @param string $url            Normalized Pinterest URL.
	 * @param string $requested_type 'video' or 'image'.
	 * @return PD_Provider_Result
	 */
	public function extract( $url, $requested_type = 'video' );
}
