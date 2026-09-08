<?php
/**
 * Input state template — 100% Unified Responsive Input Form.
 * Fully compatible with all devices (Mobile, Tablet, Desktop) without duplicate elements.
 *
 * @package Pinterest_Downloader
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$placeholder = esc_attr__( 'Paste Pinterest link here...', 'pinterest-downloader' );
$btn_label   = esc_html__( 'Download', 'pinterest-downloader' );
if ( 'gif' === $type ) {
	$btn_label = esc_html__( 'Download GIF', 'pinterest-downloader' );
} elseif ( 'image' === $type ) {
	$btn_label = esc_html__( 'Download Image', 'pinterest-downloader' );
}
?>
<div class="pd-input-container">
	<form id="pd-download-form" role="search" aria-label="<?php esc_attr_e( 'Pinterest downloader', 'pinterest-downloader' ); ?>" novalidate>

		<div class="pd-input-box">
			<div class="pd-input-icon" aria-hidden="true">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
					<path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
				</svg>
			</div>

			<input
				type="url"
				id="pd-url-input"
				name="pd_url"
				class="pd-input-field"
				placeholder="<?php echo $placeholder; ?>"
				autocomplete="off"
				autocorrect="off"
				autocapitalize="none"
				spellcheck="false"
				inputmode="url"
				required
			/>

			<div class="pd-input-actions">
				<button type="button" id="pd-paste-btn" class="pd-paste-btn" aria-label="<?php esc_attr_e( 'Paste from clipboard', 'pinterest-downloader' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
					</svg>
					<span><?php esc_html_e( 'Paste', 'pinterest-downloader' ); ?></span>
				</button>

				<button type="submit" id="pd-download-btn" class="pd-btn-primary" aria-label="<?php echo esc_attr( $btn_label ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 11l5 5 5-5M12 4v12"/>
					</svg>
					<span><?php echo $btn_label; ?></span>
				</button>
			</div>
		</div>

		<p class="pd-terms-note">
			<?php esc_html_e( 'By using our service you accept our', 'pinterest-downloader' ); ?>
			<a href="/terms-of-service/"><?php esc_html_e( 'Terms of Service', 'pinterest-downloader' ); ?></a>.
		</p>

	</form>
</div>
