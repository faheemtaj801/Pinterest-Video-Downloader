<?php
/**
 * Main downloader wrapper template — Exact TikSav.app Design.
 *
 * @package Pinterest_Downloader
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$is_video = ( 'video' === $type );
$is_gif   = ( 'gif'   === $type );

if ( $is_gif ) {
	$hero_title    = esc_html__( 'Pinterest GIF Downloader', 'pinterest-downloader' );
	$hero_subtitle = esc_html__( 'Download Pinterest GIFs in original animated format', 'pinterest-downloader' );
} elseif ( $is_video ) {
	$hero_title    = esc_html__( 'Pinterest Video Downloader', 'pinterest-downloader' );
	$hero_subtitle = esc_html__( 'Download Pinterest videos without watermark in HD MP4', 'pinterest-downloader' );
} else {
	$hero_title    = esc_html__( 'Pinterest Image Downloader', 'pinterest-downloader' );
	$hero_subtitle = esc_html__( 'Download Pinterest images in full original resolution', 'pinterest-downloader' );
}
?>
<div class="pd-app" data-pd-type="<?php echo esc_attr( $type ); ?>">

	<!-- ── Sticky Nav ─────────────────────────────────────────────── -->
	<header class="pd-nav" role="banner">
		<nav class="pd-nav__inner" role="navigation" aria-label="<?php esc_attr_e( 'Main navigation', 'pinterest-downloader' ); ?>">

			<a href="#" class="pd-nav__brand" aria-label="<?php esc_attr_e( 'Pinterest Downloader home', 'pinterest-downloader' ); ?>">
				<div class="pd-nav__logo-box" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
					     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
						<path stroke-linecap="round" stroke-linejoin="round"
						      d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 11l5 5 5-5M12 4v12"/>
					</svg>
				</div>
				<span class="pd-nav__brand-name">
					Pinterest<span><?php esc_html_e( 'Down', 'pinterest-downloader' ); ?></span>
				</span>
			</a>

			<div class="pd-nav__links">
				<a href="#pd-how-it-works" class="pd-nav__link">
					<?php esc_html_e( 'How it works', 'pinterest-downloader' ); ?>
				</a>
				<a href="#pd-faq" class="pd-nav__link">
					<?php esc_html_e( 'FAQ', 'pinterest-downloader' ); ?>
				</a>
			</div>

		</nav>
	</header>

	<main>

		<!-- ── Hero ───────────────────────────────────────────────── -->
		<section class="pd-hero" aria-labelledby="pd-hero-heading">
			<div class="pd-hero__container">

				<h1 class="pd-hero__title" id="pd-hero-heading">
					<?php echo $hero_title; ?>
				</h1>

				<p class="pd-hero__subtitle">
					<?php echo $hero_subtitle; ?>
				</p>

				<!-- States live inside the hero (input / loading) -->
				<div id="pd-hero-states">

					<!-- State: Input -->
					<div class="pd-state pd-state--input is-active" id="pd-state-input">
						<?php include PD_PLUGIN_DIR . 'templates/input.php'; ?>
					</div>

					<!-- State: Loading -->
					<div class="pd-state pd-state--loading" id="pd-state-loading"
					     aria-live="polite" aria-hidden="true">
						<?php include PD_PLUGIN_DIR . 'templates/loading.php'; ?>
					</div>

				</div>

			</div>
		</section>

		<!-- ── Result (below hero, white bg) ─────────────────────── -->
		<div class="pd-result-outer">
			<div class="pd-state pd-state--result" id="pd-state-result"
			     aria-live="polite" aria-hidden="true">
				<?php include PD_PLUGIN_DIR . 'templates/result.php'; ?>
			</div>
		</div>

		<!-- ── Error ──────────────────────────────────────────────── -->
		<div class="pd-error-outer">
			<div class="pd-state pd-state--error" id="pd-state-error"
			     aria-live="assertive" aria-hidden="true">
				<?php include PD_PLUGIN_DIR . 'templates/error.php'; ?>
			</div>
		</div>

		<!-- ── How It Works ───────────────────────────────────────── -->
		<section class="pd-section-how" id="pd-how-it-works" aria-labelledby="pd-how-heading">
			<div class="pd-section__container">

				<div class="pd-section__header">
					<h2 id="pd-how-heading" class="pd-section-title">
						<?php esc_html_e( 'Download in 3 Easy Steps', 'pinterest-downloader' ); ?>
					</h2>
				</div>

				<ol class="pd-steps-grid">

					<li class="pd-step-card">
						<div class="pd-step-num" aria-hidden="true">1</div>
						<h3 class="pd-step-title"><?php esc_html_e( 'Copy the Pinterest Link', 'pinterest-downloader' ); ?></h3>
						<p class="pd-step-desc">
							<?php esc_html_e( 'Open Pinterest, tap Share → Copy Link on any public Pin you want to save.', 'pinterest-downloader' ); ?>
						</p>
					</li>

					<li class="pd-step-card">
						<div class="pd-step-num" aria-hidden="true">2</div>
						<h3 class="pd-step-title"><?php esc_html_e( 'Paste & Click Download', 'pinterest-downloader' ); ?></h3>
						<p class="pd-step-desc">
							<?php esc_html_e( 'Paste the Pinterest URL into the downloader above and press Download. All available formats are fetched instantly.', 'pinterest-downloader' ); ?>
						</p>
					</li>

					<li class="pd-step-card">
						<div class="pd-step-num" aria-hidden="true">3</div>
						<h3 class="pd-step-title"><?php esc_html_e( 'Save Without Watermark', 'pinterest-downloader' ); ?></h3>
						<p class="pd-step-desc">
							<?php esc_html_e( 'Choose HD MP4 without watermark or original image. The file saves directly to your device.', 'pinterest-downloader' ); ?>
						</p>
					</li>

				</ol>
			</div>
		</section>

		<!-- ── Features ───────────────────────────────────────────── -->
		<section class="pd-section-features" aria-labelledby="pd-feat-heading">
			<div class="pd-section__container">

				<div class="pd-section__header">
					<span class="pd-section-label"><?php esc_html_e( 'Benefits', 'pinterest-downloader' ); ?></span>
					<h2 id="pd-feat-heading" class="pd-section-title">
						<?php esc_html_e( 'Why Choose Our Downloader?', 'pinterest-downloader' ); ?>
					</h2>
				</div>

				<div class="pd-feat-grid">

					<div class="pd-feat-card">
						<div class="pd-feat-icon" aria-hidden="true">
							<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
							</svg>
						</div>
						<h3 class="pd-feat-title"><?php esc_html_e( 'Lightning Fast', 'pinterest-downloader' ); ?></h3>
						<p class="pd-feat-desc"><?php esc_html_e( 'Direct CDN extraction ensures high-speed conversion and instant saving in seconds.', 'pinterest-downloader' ); ?></p>
					</div>

					<div class="pd-feat-card">
						<div class="pd-feat-icon" aria-hidden="true">
							<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
							</svg>
						</div>
						<h3 class="pd-feat-title"><?php esc_html_e( 'HD Quality', 'pinterest-downloader' ); ?></h3>
						<p class="pd-feat-desc"><?php esc_html_e( 'Download videos in highest available 720p HD MP4 and images in original full resolution.', 'pinterest-downloader' ); ?></p>
					</div>

					<div class="pd-feat-card">
						<div class="pd-feat-icon" aria-hidden="true">
							<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
							</svg>
						</div>
						<h3 class="pd-feat-title"><?php esc_html_e( '100% Free & Secure', 'pinterest-downloader' ); ?></h3>
						<p class="pd-feat-desc"><?php esc_html_e( 'No sign-up, registration, or software installation needed. Completely safe and free.', 'pinterest-downloader' ); ?></p>
					</div>

					<div class="pd-feat-card">
						<div class="pd-feat-icon" aria-hidden="true">
							<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
								<line x1="12" y1="18" x2="12.01" y2="18"/>
							</svg>
						</div>
						<h3 class="pd-feat-title"><?php esc_html_e( 'Works on All Devices', 'pinterest-downloader' ); ?></h3>
						<p class="pd-feat-desc"><?php esc_html_e( 'Fully responsive and compatible with Android, iPhone, Windows, and Mac.', 'pinterest-downloader' ); ?></p>
					</div>

				</div>
			</div>
		</section>

		<!-- ── FAQ ────────────────────────────────────────────────── -->
		<section class="pd-section-faq" id="pd-faq" aria-labelledby="pd-faq-heading">
			<div class="pd-section__container pd-section__container--narrow">

				<div class="pd-section__header">
					<p class="pd-section-label"><?php esc_html_e( 'Got Questions?', 'pinterest-downloader' ); ?></p>
					<h2 id="pd-faq-heading" class="pd-section-title">
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
							'a' => __( 'On mobile (Android & iPhone), files save to your default "Downloads" folder or Camera Roll / Gallery. On desktop, files are saved in your browser\'s default Downloads directory.', 'pinterest-downloader' ),
						),
						array(
							'q' => __( 'Can I download Pinterest images at original resolution?', 'pinterest-downloader' ),
							'a' => __( 'Yes! Our tool automatically detects the highest original quality available on Pinterest CDN and provides a direct full-size download.', 'pinterest-downloader' ),
						),
						array(
							'q' => __( 'Do I need an account or software installation?', 'pinterest-downloader' ),
							'a' => __( 'No. You do not need to register, provide an email, or install browser extensions. It works directly in any modern browser.', 'pinterest-downloader' ),
						),
						array(
							'q' => __( 'Is this Pinterest downloader free to use?', 'pinterest-downloader' ),
							'a' => __( 'Yes, it is 100% free with unlimited downloads. No premium tier, no hidden costs.', 'pinterest-downloader' ),
						),
					);

					foreach ( $faqs as $i => $faq ) :
						$btn_id   = 'pd-faq-btn-' . ( $i + 1 );
						$panel_id = 'pd-faq-panel-' . ( $i + 1 );
					?>
					<div class="pd-faq-item">
						<button
							type="button"
							class="pd-faq-toggle"
							id="<?php echo esc_attr( $btn_id ); ?>"
							aria-expanded="false"
							aria-controls="<?php echo esc_attr( $panel_id ); ?>"
						>
							<span class="pd-faq-question"><?php echo esc_html( $faq['q'] ); ?></span>
							<span class="pd-faq-icon" aria-hidden="true">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
								     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
								</svg>
							</span>
						</button>
						<div
							class="pd-faq-answer"
							id="<?php echo esc_attr( $panel_id ); ?>"
							role="region"
							aria-labelledby="<?php echo esc_attr( $btn_id ); ?>"
						>
							<p><?php echo esc_html( $faq['a'] ); ?></p>
						</div>
					</div>
					<?php endforeach; ?>
				</div>

			</div>
		</section>

	</main>

	<!-- ── Disclaimer Banner ──────────────────────────────────────── -->
	<div class="pd-disclaimer">
		<div class="pd-disclaimer__inner">
			<p>
				<?php esc_html_e( 'Pinterest Downloader is an independent tool built to help users save Pinterest content for personal use. We have no relationship with Pinterest Inc. or any of their subsidiaries. All trademarks, logos, and brand names belong to their respective owners. Only download content you have the right to download.', 'pinterest-downloader' ); ?>
			</p>
		</div>
	</div>

	<!-- ── Footer ─────────────────────────────────────────────────── -->
	<footer class="pd-footer" role="contentinfo">
		<div class="pd-footer__top">

			<div>
				<a href="#" class="pd-footer__brand" aria-label="<?php esc_attr_e( 'Pinterest Downloader home', 'pinterest-downloader' ); ?>">
					<div class="pd-footer__logo-box" aria-hidden="true">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
						     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
							<path stroke-linecap="round" stroke-linejoin="round"
							      d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 11l5 5 5-5M12 4v12"/>
						</svg>
					</div>
					<span class="pd-footer__brand-name">
						Pinterest<span><?php esc_html_e( 'Down', 'pinterest-downloader' ); ?></span>
					</span>
				</a>
				<p class="pd-footer__tagline">
					<?php esc_html_e( 'The fastest free Pinterest downloader. No watermark, no account, no limits.', 'pinterest-downloader' ); ?>
				</p>
			</div>

			<div>
				<h3 class="pd-footer__col-title"><?php esc_html_e( 'Quick Links', 'pinterest-downloader' ); ?></h3>
				<ul class="pd-footer__links">
					<li><a href="#pd-how-it-works"><?php esc_html_e( 'How It Works', 'pinterest-downloader' ); ?></a></li>
					<li><a href="#pd-faq"><?php esc_html_e( 'FAQ', 'pinterest-downloader' ); ?></a></li>
				</ul>
			</div>

			<div>
				<h3 class="pd-footer__col-title"><?php esc_html_e( 'Download Types', 'pinterest-downloader' ); ?></h3>
				<ul class="pd-footer__links">
					<li><a href="#"><?php esc_html_e( 'Pinterest Video Downloader', 'pinterest-downloader' ); ?></a></li>
					<li><a href="#"><?php esc_html_e( 'Pinterest Image Downloader', 'pinterest-downloader' ); ?></a></li>
					<li><a href="#"><?php esc_html_e( 'Pinterest GIF Downloader', 'pinterest-downloader' ); ?></a></li>
				</ul>
			</div>

		</div>

		<div class="pd-footer__bottom">
			<div class="pd-footer__bottom-inner">
				<p class="pd-footer__copy">
					&copy; <span id="pd-footer-year"></span>
					<?php esc_html_e( 'Pinterest Downloader. All rights reserved.', 'pinterest-downloader' ); ?>
				</p>
			</div>
		</div>

		<script>
			var y = document.getElementById('pd-footer-year');
			if (y) y.textContent = new Date().getFullYear();
		</script>
	</footer>

</div><!-- .pd-app -->
