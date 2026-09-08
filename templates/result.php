<?php
/**
 * Result state template — exact TikSav.app result card design.
 *
 * Populated dynamically by JavaScript.
 *
 * @package Pinterest_Downloader
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="pd-result-container">

	<!-- TikSav Result Card -->
	<div class="pd-result-card" id="pd-result-card">

		<!-- Top: thumbnail + details -->
		<div class="pd-result-top">
			<div class="pd-result-flex">

				<!-- Thumbnail with duration badge -->
				<div class="pd-result-thumb-box">
					<img
						id="pd-result-thumbnail"
						class="pd-result-cover"
						src=""
						alt="<?php esc_attr_e( 'Pinterest media preview', 'pinterest-downloader' ); ?>"
						loading="lazy"
					/>
					<span id="pd-result-duration" class="pd-result-duration" hidden></span>
				</div>

				<!-- Details -->
				<div class="pd-result-details">
					<div class="pd-result-author-row">
						<span id="pd-result-author" class="pd-result-author-name">Pinterest</span>
					</div>

					<h2 id="pd-result-title" class="pd-result-title">
						<!-- Populated by JS -->
					</h2>

					<div class="pd-result-badges">
						<span class="pd-badge pd-badge--nowm">
							<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none"
							     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
							</svg>
							<?php esc_html_e( 'No Watermark', 'pinterest-downloader' ); ?>
						</span>
						<span class="pd-badge pd-badge--hd">
							<?php esc_html_e( 'HD Available', 'pinterest-downloader' ); ?>
						</span>
					</div>
				</div>

			</div>
		</div>

		<!-- Divider -->
		<div class="pd-result-divider"></div>

		<!-- Format Buttons -->
		<div class="pd-result-formats">

			<p class="pd-formats-label"><?php esc_html_e( 'Choose Format', 'pinterest-downloader' ); ?></p>

			<!-- 1. MP4 — royal blue gradient (TikSav dl-btn-mp4) -->
			<a
				id="pd-btn-mp4"
				href="#"
				class="pd-dl-btn pd-dl-btn--mp4"
				target="_blank"
				rel="noopener noreferrer"
				download
			>
				<div class="pd-dl-icon" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
					     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
						<path stroke-linecap="round" stroke-linejoin="round"
						      d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.889L15 14M4 6h8a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z"/>
					</svg>
				</div>
				<div class="pd-dl-text">
					<div class="pd-dl-title" id="pd-mp4-title">
						<?php esc_html_e( 'Download MP4', 'pinterest-downloader' ); ?>
					</div>
					<div class="pd-dl-subtitle">
						<?php esc_html_e( 'No watermark · Best quality', 'pinterest-downloader' ); ?>
					</div>
				</div>
				<svg class="pd-dl-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
				     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round"
					      d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 11l5 5 5-5M12 4v12"/>
				</svg>
			</a>

			<!-- 2. HD — white with royal border (TikSav dl-btn-hd) -->
			<a
				id="pd-btn-hd"
				href="#"
				class="pd-dl-btn pd-dl-btn--hd"
				target="_blank"
				rel="noopener noreferrer"
				download
			>
				<div class="pd-dl-icon" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
					     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
						<path stroke-linecap="round" stroke-linejoin="round"
						      d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
					</svg>
				</div>
				<div class="pd-dl-text">
					<div class="pd-dl-title">
						<?php esc_html_e( 'HD Version', 'pinterest-downloader' ); ?>
					</div>
					<div class="pd-dl-subtitle">
						<?php esc_html_e( 'Highest resolution available', 'pinterest-downloader' ); ?>
					</div>
				</div>
				<svg class="pd-dl-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
				     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round"
					      d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 11l5 5 5-5M12 4v12"/>
				</svg>
			</a>

			<!-- 3. Cover Image — sky blue (TikSav dl-btn-mp3 style) -->
			<a
				id="pd-btn-img"
				href="#"
				class="pd-dl-btn pd-dl-btn--img"
				target="_blank"
				rel="noopener noreferrer"
				download
			>
				<div class="pd-dl-icon" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
					     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
						<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
						<circle cx="8.5" cy="8.5" r="1.5"/>
						<polyline points="21 15 16 10 5 21"/>
					</svg>
				</div>
				<div class="pd-dl-text">
					<div class="pd-dl-title" id="pd-img-title">
						<?php esc_html_e( 'Download Cover Image', 'pinterest-downloader' ); ?>
					</div>
					<div class="pd-dl-subtitle">
						<?php esc_html_e( 'Full resolution · Original JPG', 'pinterest-downloader' ); ?>
					</div>
				</div>
				<svg class="pd-dl-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
				     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round"
					      d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 11l5 5 5-5M12 4v12"/>
				</svg>
			</a>

		</div>

		<p class="pd-result-expire-note">
			<?php esc_html_e( 'Links expire shortly. Re-paste the Pinterest URL if a download stops working.', 'pinterest-downloader' ); ?>
		</p>

	</div><!-- .pd-result-card -->

	<!-- Reset button (TikSav style — royal blue text, no bg) -->
	<div class="pd-reset-wrap">
		<button type="button" class="pd-reset-btn" data-pd-reset="true">
			<span>↩ <?php esc_html_e( 'Download another video', 'pinterest-downloader' ); ?></span>
		</button>
	</div>

</div><!-- .pd-result-container -->
