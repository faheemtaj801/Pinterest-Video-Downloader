<?php
/**
 * Loading state template — exact TikSav.app design (white dots on blue hero).
 *
 * @package Pinterest_Downloader
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="pd-loading-container" role="status" aria-live="polite"
     aria-label="<?php esc_attr_e( 'Loading media info', 'pinterest-downloader' ); ?>">

	<!-- White pulsing dots (TikSav style) -->
	<div class="pd-dot-loader" aria-hidden="true">
		<span style="background:#fff;"></span>
		<span style="background:#fff;"></span>
		<span style="background:#fff;"></span>
	</div>

	<p class="pd-loading-text">
		<?php esc_html_e( 'Fetching media info…', 'pinterest-downloader' ); ?>
	</p>

</div>
