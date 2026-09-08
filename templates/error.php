<?php
/**
 * Error state template — TikSav Style.
 *
 * @package Pinterest_Downloader
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="pd-error-container" id="pd-error-card">

	<div class="pd-error-card">
		<!-- Error warning icon -->
		<div class="pd-error-icon-wrap" aria-hidden="true">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="36" height="36">
				<path d="M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
			</svg>
		</div>

		<!-- Error heading -->
		<h3 class="pd-error-title" id="pd-error-title">
			<?php esc_html_e( "We couldn't process this link.", 'pinterest-downloader' ); ?>
		</h3>

		<!-- Error description -->
		<p class="pd-error-desc" id="pd-error-desc">
			<?php esc_html_e( 'Please check the Pinterest link and try again.', 'pinterest-downloader' ); ?>
		</p>

		<!-- Actions -->
		<div class="pd-error-actions">
			<button
				type="button"
				class="pd-btn-primary pd-try-again"
				data-pd-reset="true"
			>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" aria-hidden="true">
					<polyline points="1 4 1 10 7 10"/>
					<path d="M3.51 15a9 9 0 1 0 .49-4"/>
				</svg>
				<span><?php esc_html_e( 'Try Again', 'pinterest-downloader' ); ?></span>
			</button>
		</div>
	</div>

</div>

