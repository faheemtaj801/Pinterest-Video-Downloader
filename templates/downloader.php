<?php
/**
 * Main downloader wrapper template (TikSav Design).
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

$is_video = ( 'video' === $type );
$is_gif   = ( 'gif' === $type );

if ( $is_gif ) {
	$heading = esc_html__( 'Pinterest GIF Downloader', 'pinterest-downloader' );
	$subtext = esc_html__( 'Download Pinterest GIFs in animated MP4 or GIF format', 'pinterest-downloader' );
} elseif ( $is_video ) {
	$heading = esc_html__( 'Pinterest Video Downloader', 'pinterest-downloader' );
	$subtext = esc_html__( 'Download Pinterest videos without watermark in HD MP4 or 720p', 'pinterest-downloader' );
} else {
	$heading = esc_html__( 'Pinterest Image Downloader', 'pinterest-downloader' );
	$subtext = esc_html__( 'Download Pinterest images in full original resolution', 'pinterest-downloader' );
}
?>
<div class="pd-app" data-pd-type="<?php echo esc_attr( $type ); ?>">

	<!-- =========================================================
		HERO SECTION (TikSav Royal Blue Gradient)
	========================================================== -->
	<section class="pd-hero" aria-labelledby="pd-hero-heading">
		<div class="pd-hero__container">

			<!-- Brand pill tag -->
			<div class="pd-hero__badge">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 11l5 5 5-5M12 4v12"/>
				</svg>
				<span><?php esc_html_e( 'Free & Fast Pinterest Downloader', 'pinterest-downloader' ); ?></span>
			</div>

			<h1 class="pd-hero__title" id="pd-hero-heading">
				<?php echo $heading; ?>
			</h1>

			<p class="pd-hero__subtitle">
				<?php echo $subtext; ?>
			</p>

			<!-- =========================================================
				STATE CONTAINER (Input / Loading / Result / Error)
			========================================================== -->
			<div class="pd-hero__states">

				<!-- State 1: Input -->
				<div class="pd-state pd-state--input is-active" id="pd-state-input">
					<?php include PD_PLUGIN_DIR . 'templates/input.php'; ?>
				</div>

				<!-- State 2: Loading -->
				<div class="pd-state pd-state--loading" id="pd-state-loading" aria-live="polite" aria-hidden="true">
					<?php include PD_PLUGIN_DIR . 'templates/loading.php'; ?>
				</div>

				<!-- State 3: Result -->
				<div class="pd-state pd-state--result" id="pd-state-result" aria-live="polite" aria-hidden="true">
					<?php include PD_PLUGIN_DIR . 'templates/result.php'; ?>
				</div>

				<!-- State 4: Error -->
				<div class="pd-state pd-state--error" id="pd-state-error" aria-live="assertive" aria-hidden="true">
					<?php include PD_PLUGIN_DIR . 'templates/error.php'; ?>
				</div>

			</div><!-- .pd-hero__states -->

		</div><!-- .pd-hero__container -->
	</section><!-- .pd-hero -->

	<!-- =========================================================
		HOW IT WORKS (TikSav 3 Easy Steps)
	========================================================== -->
	<section class="pd-section pd-section--white" id="pd-how-heading" aria-labelledby="pd-how-title">
		<div class="pd-section__container">

			<div class="pd-section__header">
				<span class="pd-section__label"><?php esc_html_e( 'Simple Process', 'pinterest-downloader' ); ?></span>
				<h2 class="pd-section__title" id="pd-how-title">
					<?php esc_html_e( 'Download in 3 Easy Steps', 'pinterest-downloader' ); ?>
				</h2>
			</div>

			<div class="pd-steps-grid">

				<!-- Step 1 -->
				<div class="pd-step-card">
					<div class="pd-step-num" aria-hidden="true">1</div>
					<h3 class="pd-step-title"><?php esc_html_e( 'Copy the Pinterest Link', 'pinterest-downloader' ); ?></h3>
					<p class="pd-step-desc">
						<?php esc_html_e( 'Open Pinterest, tap Share → Copy Link on any public video or image you want to save.', 'pinterest-downloader' ); ?>
					</p>
				</div>

				<!-- Step 2 -->
				<div class="pd-step-card">
					<div class="pd-step-num" aria-hidden="true">2</div>
					<h3 class="pd-step-title"><?php esc_html_e( 'Paste & Click Download', 'pinterest-downloader' ); ?></h3>
					<p class="pd-step-desc">
						<?php esc_html_e( 'Paste the link into the box above and click Download. All available formats are fetched instantly.', 'pinterest-downloader' ); ?>
					</p>
				</div>

				<!-- Step 3 -->
				<div class="pd-step-card">
					<div class="pd-step-num" aria-hidden="true">3</div>
					<h3 class="pd-step-title"><?php esc_html_e( 'Save Without Watermark', 'pinterest-downloader' ); ?></h3>
					<p class="pd-step-desc">
						<?php esc_html_e( 'Choose 720p HD MP4 without watermark or original image. The file saves directly to your device.', 'pinterest-downloader' ); ?>
					</p>
				</div>

			</div><!-- .pd-steps-grid -->

		</div>
	</section>

	<!-- =========================================================
		FEATURES SECTION (TikSav Style)
	========================================================== -->
	<section class="pd-section pd-section--snow" aria-labelledby="pd-feat-title">
		<div class="pd-section__container">

			<div class="pd-section__header">
				<span class="pd-section__label"><?php esc_html_e( 'Benefits', 'pinterest-downloader' ); ?></span>
				<h2 class="pd-section__title" id="pd-feat-title">
					<?php esc_html_e( 'Why Choose Our Downloader?', 'pinterest-downloader' ); ?>
				</h2>
			</div>

			<div class="pd-feat-grid">

				<div class="pd-feat-card">
					<div class="pd-feat-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="28" height="28">
							<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
						</svg>
					</div>
					<h3 class="pd-feat-title"><?php esc_html_e( 'Lightning Fast', 'pinterest-downloader' ); ?></h3>
					<p class="pd-feat-desc"><?php esc_html_e( 'Direct CDN extraction ensures high-speed conversion and instant saving in seconds.', 'pinterest-downloader' ); ?></p>
				</div>

				<div class="pd-feat-card">
					<div class="pd-feat-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="28" height="28">
							<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
						</svg>
					</div>
					<h3 class="pd-feat-title"><?php esc_html_e( 'HD & Original Quality', 'pinterest-downloader' ); ?></h3>
					<p class="pd-feat-desc"><?php esc_html_e( 'Download videos in highest available 720p/1080p MP4 and images in original full resolution.', 'pinterest-downloader' ); ?></p>
				</div>

				<div class="pd-feat-card">
					<div class="pd-feat-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="28" height="28">
							<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
						</svg>
					</div>
					<h3 class="pd-feat-title"><?php esc_html_e( '100% Free & Secure', 'pinterest-downloader' ); ?></h3>
					<p class="pd-feat-desc"><?php esc_html_e( 'No sign-up, registration, or software installation needed. Completely safe and free.', 'pinterest-downloader' ); ?></p>
				</div>

				<div class="pd-feat-card">
					<div class="pd-feat-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="28" height="28">
							<rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
							<line x1="12" y1="18" x2="12.01" y2="18"/>
						</svg>
					</div>
					<h3 class="pd-feat-title"><?php esc_html_e( 'Works on All Devices', 'pinterest-downloader' ); ?></h3>
					<p class="pd-feat-desc"><?php esc_html_e( 'Fully responsive and compatible with Android, iPhone, iPad, Windows, and Mac.', 'pinterest-downloader' ); ?></p>
				</div>

			</div><!-- .pd-feat-grid -->

		</div>
	</section>

	<!-- =========================================================
		FAQ ACCORDION (TikSav Style)
	========================================================== -->
	<section class="pd-section pd-section--white" aria-labelledby="pd-faq-title">
		<div class="pd-section__container pd-section__container--narrow">

			<div class="pd-section__header">
				<span class="pd-section__label"><?php esc_html_e( 'Got Questions?', 'pinterest-downloader' ); ?></span>
				<h2 class="pd-section__title" id="pd-faq-title">
					<?php esc_html_e( 'Frequently Asked Questions', 'pinterest-downloader' ); ?>
				</h2>
			</div>

			<div class="pd-faq-list">

				<?php
				$faqs = array(
					array(
						'q' => __( 'How do I download a Pinterest video without watermark?', 'pinterest-downloader' ),
						'a' => __( 'Copy the link of the Pinterest pin from the Pinterest app or browser, paste it into the box above, and click Download. You will see direct download options for 720p HD MP4 without watermarks.', 'pinterest-downloader' ),
					),
					array(
						'q' => __( 'Where are downloaded videos saved on my phone or PC?', 'pinterest-downloader' ),
						'a' => __( 'On mobile (Android & iPhone), files save to your default "Downloads" folder or Camera Roll / Gallery. On desktop, files are saved in your browser’s default Downloads directory.', 'pinterest-downloader' ),
					),
					array(
						'q' => __( 'Can I download Pinterest images at original resolution?', 'pinterest-downloader' ),
						'a' => __( 'Yes! Our tool automatically detects the highest original quality (/originals/) available on Pinterest CDN and provides a direct full-size download.', 'pinterest-downloader' ),
					),
					array(
						'q' => __( 'Do I need an account or software installation?', 'pinterest-downloader' ),
						'a' => __( 'No. You do not need to register, provide an email, or install browser extensions. It works directly in any modern browser.', 'pinterest-downloader' ),
					),
					array(
						'q' => __( 'Is this Pinterest downloader free to use?', 'pinterest-downloader' ),
						'a' => __( 'Yes, it is 100% free with unlimited downloads.', 'pinterest-downloader' ),
					),
				);

				foreach ( $faqs as $index => $faq ) :
					$item_id  = 'pd-faq-item-' . ( $index + 1 );
					$panel_id = 'pd-faq-panel-' . ( $index + 1 );
					?>
					<div class="pd-faq-item">
						<button
							type="button"
							class="pd-faq-toggle"
							aria-expanded="false"
							aria-controls="<?php echo esc_attr( $panel_id ); ?>"
							id="<?php echo esc_attr( $item_id ); ?>"
						>
							<span class="pd-faq-question"><?php echo esc_html( $faq['q'] ); ?></span>
							<span class="pd-faq-icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
									<line x1="12" y1="5" x2="12" y2="19"/>
									<line x1="5" y1="12" x2="19" y2="12"/>
								</svg>
							</span>
						</button>
						<div
							class="pd-faq-answer"
							id="<?php echo esc_attr( $panel_id ); ?>"
							role="region"
							aria-labelledby="<?php echo esc_attr( $item_id ); ?>"
							hidden
						>
							<p><?php echo esc_html( $faq['a'] ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>

			</div><!-- .pd-faq-list -->

		</div>
	</section>

</div><!-- .pd-app -->

