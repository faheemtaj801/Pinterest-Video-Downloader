<?php
/**
 * Input state template — exact TikSav.app design.
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

		<!-- Desktop inline pill -->
		<div class="pd-input-wrap--desktop">
			<div class="pd-input-icon" aria-hidden="true">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
				     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
					<path stroke-linecap="round" stroke-linejoin="round"
					      d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
				</svg>
			</div>

			<label for="pd-url-input" class="pd-sr-only">
				<?php esc_html_e( 'Paste Pinterest URL', 'pinterest-downloader' ); ?>
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
				inputmode="url"
				aria-required="true"
				aria-describedby="pd-input-error"
			/>

			<button type="button" id="pd-paste-btn"
			        class="pd-paste-btn"
			        aria-label="<?php esc_attr_e( 'Paste from clipboard', 'pinterest-downloader' ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
				     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round"
					      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
				</svg>
				<?php esc_html_e( 'Paste', 'pinterest-downloader' ); ?>
			</button>

			<button type="submit" id="pd-download-btn"
			        class="pd-btn-primary"
			        aria-label="<?php echo esc_attr( $btn_label ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
				     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round"
					      d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 11l5 5 5-5M12 4v12"/>
				</svg>
				<?php echo $btn_label; ?>
			</button>
		</div>

		<!-- Mobile stacked -->
		<div class="pd-input-wrap--mobile">
			<div class="pd-input-row">
				<div class="pd-input-icon" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
					     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
						<path stroke-linecap="round" stroke-linejoin="round"
						      d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
					</svg>
				</div>
				<label for="pd-url-input-mobile" class="pd-sr-only">
					<?php esc_html_e( 'Paste Pinterest URL', 'pinterest-downloader' ); ?>
				</label>
				<input
					type="url"
					id="pd-url-input-mobile"
					class="pd-input-field"
					placeholder="<?php echo $placeholder; ?>"
					autocomplete="off"
					autocorrect="off"
					autocapitalize="none"
					spellcheck="false"
					inputmode="url"
				/>
				<button type="button" id="pd-paste-btn-mobile"
				        class="pd-paste-btn"
				        aria-label="<?php esc_attr_e( 'Paste from clipboard', 'pinterest-downloader' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
					     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round"
						      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
					</svg>
					<?php esc_html_e( 'Paste', 'pinterest-downloader' ); ?>
				</button>
			</div>

			<button type="submit" id="pd-download-btn-mobile"
			        class="pd-btn-primary pd-btn-primary--full">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
				     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round"
					      d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 11l5 5 5-5M12 4v12"/>
				</svg>
				<?php echo $btn_label; ?>
			</button>
		</div>

		<!-- Terms note -->
		<p class="pd-terms-note">
			<span><?php esc_html_e( 'By using our service you accept our', 'pinterest-downloader' ); ?></span>
			<a href="#pd-faq"><?php esc_html_e( 'Terms of Service', 'pinterest-downloader' ); ?></a>.
		</p>

		<!-- Inline error -->
		<div id="pd-input-error" class="pd-inline-error" role="alert" aria-live="polite" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
			     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round"
				      d="M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
			</svg>
			<span id="pd-error-text"></span>
		</div>

	</form>
</div>
