<?php
/**
 * Loading state template (TikSav Design).
 *
 * @package Pinterest_Downloader
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="pd-loading-container" role="status" aria-live="polite" aria-label="<?php esc_attr_e( 'Loading media info', 'pinterest-downloader' ); ?>">

	<!-- TikSav Pulsing Dot Loader -->
	<div class="pd-dot-loader" aria-hidden="true">
		<span></span>
		<span></span>
		<span></span>
	</div>

	<!-- Status message -->
	<p class="pd-loading-text" id="pd-loading-message">
		<?php esc_html_e( 'Fetching media info…', 'pinterest-downloader' ); ?>
	</p>

</div>

