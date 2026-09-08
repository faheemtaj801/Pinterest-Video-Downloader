<?php
/**
 * Input state template — URL input + download button (TikSav Design).
 *
 * Available variables:
 *   $type  string  'video', 'image', or 'gif'
 *
 * @package Pinterest_Downloader
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$placeholder = esc_attr__( 'Paste Pinterest link here...', 'pinterest-downloader' );
if ( 'gif' === $type ) {
	$btn_label = esc_html__( 'Download GIF', 'pinterest-downloader' );
} elseif ( 'image' === $type ) {
	$btn_label = esc_html__( 'Download Image', 'pinterest-downloader' );
} else {
	$btn_label = esc_html__( 'Download', 'pinterest-downloader' );
}
?>
<div class="pd-input-container">

	<form class="pd-form" id="pd-download-form" novalidate>

		<!-- Desktop & Tablet Inline Pill Wrap -->
		<div class="pd-input-wrap pd-input-wrap--desktop">
			<!-- Link Icon -->
			<div class="pd-input-icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
					<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
					<path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
				</svg>
			</div>

			<label for="pd-url-input" class="pd-visually-hidden">
				<?php esc_html_e( 'Paste Pinterest link here', 'pinterest-downloader' ); ?>
			</label>

			<input
				type="url"
				id="pd-url-input"
				class="pd-input-field"
				placeholder="<?php echo $placeholder; ?>"
				autocomplete="off"
				autocorrect="off"
				autocapitalize="none"
				spellcheck="false"
				aria-required="true"
				aria-describedby="pd-input-error"
			/>

			<!-- Paste button -->
			<button
				type="button"
				class="pd-paste-btn"
				id="pd-paste-btn"
				aria-label="<?php esc_attr_e( 'Paste from clipboard', 'pinterest-downloader' ); ?>"
				title="<?php esc_attr_e( 'Paste from clipboard', 'pinterest-downloader' ); ?>"
			>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" aria-hidden="true">
					<rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
					<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
				</svg>
				<span><?php esc_html_e( 'Paste', 'pinterest-downloader' ); ?></span>
			</button>

			<!-- Submit button -->
			<button
				type="submit"
				class="pd-btn-primary"
				id="pd-download-btn"
				aria-label="<?php echo esc_attr( $btn_label ); ?>"
			>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true">
					<path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 11l5 5 5-5M12 4v12"/>
				</svg>
				<span><?php echo $btn_label; ?></span>
			</button>
		</div>

		<!-- Mobile Stacked Wrap -->
		<div class="pd-input-wrap--mobile">
			<div class="pd-input-wrap">
				<div class="pd-input-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
						<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
						<path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
					</svg>
				</div>
				<input
					type="url"
					id="pd-url-input-mobile"
					class="pd-input-field"
					placeholder="<?php echo $placeholder; ?>"
					autocomplete="off"
					autocorrect="off"
					autocapitalize="none"
					spellcheck="false"
				/>
				<button
					type="button"
					class="pd-paste-btn"
					id="pd-paste-btn-mobile"
					aria-label="<?php esc_attr_e( 'Paste from clipboard', 'pinterest-downloader' ); ?>"
				>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" aria-hidden="true">
						<rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
						<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
					</svg>
					<span><?php esc_html_e( 'Paste', 'pinterest-downloader' ); ?></span>
				</button>
			</div>

			<button
				type="submit"
				class="pd-btn-primary pd-btn-primary--mobile"
				id="pd-download-btn-mobile"
			>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="20" height="20" aria-hidden="true">
					<path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 11l5 5 5-5M12 4v12"/>
				</svg>
				<span><?php echo $btn_label; ?></span>
			</button>
		</div>

		<!-- Terms & conditions note -->
		<p class="pd-terms-note">
			<span><?php esc_html_e( 'By using our service you accept our', 'pinterest-downloader' ); ?></span>
			<a href="#pd-how-heading"><?php esc_html_e( 'Terms of Service', 'pinterest-downloader' ); ?></a>.
		</p>

		<!-- Inline validation error alert -->
		<div
			class="pd-inline-error"
			id="pd-input-error"
			role="alert"
			aria-live="polite"
			hidden
		>
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" aria-hidden="true">
				<circle cx="12" cy="12" r="10"/>
				<line x1="12" y1="8" x2="12" y2="12"/>
				<line x1="12" y1="16" x2="12.01" y2="16"/>
			</svg>
			<span id="pd-error-text"></span>
		</div>

	</form>

</div>
