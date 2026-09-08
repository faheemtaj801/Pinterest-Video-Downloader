<?php
/**
 * Error state template — TikSav style.
 *
 * @package Pinterest_Downloader
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="pd-error-container">
	<div class="pd-error-card">

		<div class="pd-error-icon-wrap" aria-hidden="true">
			<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none"
			     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
				<path stroke-linecap="round" stroke-linejoin="round"
				      d="M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
			</svg>
		</div>

		<h3 class="pd-error-title" id="pd-error-title">
			<?php esc_html_e( "We couldn't process this link.", 'pinterest-downloader' ); ?>
		</h3>

		<p class="pd-error-desc" id="pd-error-desc">
			<?php esc_html_e( 'Please check the Pinterest link and try again.', 'pinterest-downloader' ); ?>
		</p>

		<div class="pd-error-actions">
			<button type="button" class="pd-btn-primary" data-pd-reset="true">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
				     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
					<polyline points="1 4 1 10 7 10"/>
					<path d="M3.51 15a9 9 0 1 0 .49-4"/>
				</svg>
				<?php esc_html_e( 'Try Again', 'pinterest-downloader' ); ?>
			</button>
		</div>

	</div>
</div>
