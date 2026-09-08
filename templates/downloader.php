<?php
/**
 * Main downloader wrapper template — PinDownloady (Exact TikSav.app Design).
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
	$hero_subtitle = esc_html__( 'Download supported Pinterest videos from a Pin URL — no software, no account login. Paste the link, preview the available video, and save it to your device.', 'pinterest-downloader' );
} else {
	$hero_title    = esc_html__( 'Pinterest Image Downloader', 'pinterest-downloader' );
	$hero_subtitle = esc_html__( 'Download Pinterest images in full original resolution', 'pinterest-downloader' );
}
?>
<div class="pd-app" data-pd-type="<?php echo esc_attr( $type ); ?>">

	<!-- ── Sticky Nav ─────────────────────────────────────────────── -->
	<header class="pd-nav" role="banner">
		<nav class="pd-nav__inner" role="navigation" aria-label="<?php esc_attr_e( 'Main navigation', 'pinterest-downloader' ); ?>">

			<a href="/" class="pd-nav__brand" aria-label="PinDownloady Home">
				<div class="pd-nav__logo-box" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
					     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
						<path stroke-linecap="round" stroke-linejoin="round"
						      d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 11l5 5 5-5M12 4v12"/>
					</svg>
				</div>
				<span class="pd-nav__brand-name">
					Pin<span>Downloady</span>
				</span>
			</a>

			<div class="pd-nav__links">
				<a href="#pd-how-it-works" class="pd-nav__link"><?php esc_html_e( 'How It Works', 'pinterest-downloader' ); ?></a>
				<a href="#pd-device-guide" class="pd-nav__link"><?php esc_html_e( 'Devices', 'pinterest-downloader' ); ?></a>
				<a href="#pd-faq" class="pd-nav__link"><?php esc_html_e( 'FAQ', 'pinterest-downloader' ); ?></a>
				<a href="/pinterest-image-downloader/" class="pd-nav__link"><?php esc_html_e( 'Images', 'pinterest-downloader' ); ?></a>
				<a href="/pinterest-gif-downloader/" class="pd-nav__link"><?php esc_html_e( 'GIFs', 'pinterest-downloader' ); ?></a>
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

		<!-- ── Editorial Meta & Intro Callout ──────────────────────── -->
		<section class="pd-section-content" style="padding-top: 32px; padding-bottom: 24px;">
			<div class="pd-section__container pd-section__container--narrow">
				<div class="pd-editorial-meta">
					<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
					</svg>
					<span>Last updated: September 2026 &middot; Reviewed by the PinDownloady Editorial Team</span>
				</div>

				<div class="pd-prose">
					<p>This tool works entirely in your browser on phones, tablets, and computers. It does not require you to install an app or sign in to Pinterest.</p>
					
					<div class="pd-callout pd-callout--info">
						<strong>Important note:</strong> If a Pin is private, deleted, or does not contain video media that Pinterest allows to be accessed, the tool will not return a file &mdash; no downloader can retrieve content that isn't publicly available.
					</div>
				</div>
			</div>
		</section>

		<!-- ── How It Works (5 Steps) ──────────────────────────────── -->
		<section class="pd-section-how" id="pd-how-it-works" aria-labelledby="pd-how-heading">
			<div class="pd-section__container">

				<div class="pd-section__header">
					<span class="pd-section-label"><?php esc_html_e( 'Simple Guide', 'pinterest-downloader' ); ?></span>
					<h2 id="pd-how-heading" class="pd-section-title">
						<?php esc_html_e( 'How to Download a Pinterest Video', 'pinterest-downloader' ); ?>
					</h2>
					<p style="color: #64748b; margin-top: 8px; font-size: 1.05rem;">
						You need one thing: the URL of the specific Pin. Here's the full process.
					</p>
				</div>

				<ol class="pd-steps-grid--5">

					<li class="pd-step-card">
						<div class="pd-step-num" aria-hidden="true">1</div>
						<h3 class="pd-step-title">Copy the Pin URL</h3>
						<p class="pd-step-desc">
							Open the video Pin on Pinterest (app or browser) and tap <strong>Share &rarr; Copy Link</strong>. On desktop, copy directly from your browser's address bar. (Use the individual Pin link &mdash; not a board or profile link).
						</p>
					</li>

					<li class="pd-step-card">
						<div class="pd-step-num" aria-hidden="true">2</div>
						<h3 class="pd-step-title">Paste It Into the Downloader</h3>
						<p class="pd-step-desc">
							Paste the copied link into the box above and select <strong>Download</strong>. The tool checks the link and looks for video media attached to that Pin.
						</p>
					</li>

					<li class="pd-step-card">
						<div class="pd-step-num" aria-hidden="true">3</div>
						<h3 class="pd-step-title">Preview the Result</h3>
						<p class="pd-step-desc">
							Once processed, a preview appears so you can confirm it's the right video before saving it. Where available, you'll also see the resolution and file format.
						</p>
					</li>

					<li class="pd-step-card">
						<div class="pd-step-num" aria-hidden="true">4</div>
						<h3 class="pd-step-title">Pick a Quality</h3>
						<p class="pd-step-desc">
							If the Pin has more than one available resolution, choose the one you want. We only show quality levels that genuinely exist in the source &mdash; we don't upscale or fake a higher resolution.
						</p>
					</li>

					<li class="pd-step-card">
						<div class="pd-step-num" aria-hidden="true">5</div>
						<h3 class="pd-step-title">Download</h3>
						<p class="pd-step-desc">
							Select <strong>Download</strong>. Your browser handles the save based on its own settings &mdash; some browsers ask where to save, others save automatically to a default folder.
						</p>
					</li>

				</ol>
			</div>
		</section>

		<!-- ── Device-Specific Instructions Table ──────────────────── -->
		<section class="pd-section-content--alt" id="pd-device-guide">
			<div class="pd-section__container pd-section__container--narrow">
				<div class="pd-section__header">
					<span class="pd-section-label">Compatibility</span>
					<h2 class="pd-section-title">Device-Specific Instructions</h2>
				</div>

				<div class="pd-table-wrap">
					<table class="pd-table">
						<thead>
							<tr>
								<th>Device</th>
								<th>Browser to Use</th>
								<th>Where the File Usually Saves</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><strong>iPhone / iPad</strong></td>
								<td>Safari</td>
								<td>Files app or browser download folder</td>
							</tr>
							<tr>
								<td><strong>Android</strong></td>
								<td>Chrome</td>
								<td>Downloads folder or file manager</td>
							</tr>
							<tr>
								<td><strong>Windows</strong></td>
								<td>Chrome, Edge, or Firefox</td>
								<td>Downloads folder</td>
							</tr>
							<tr>
								<td><strong>Mac</strong></td>
								<td>Safari, Chrome, or Firefox</td>
								<td>Downloads folder</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="pd-prose">
					<p><strong>iPhone:</strong> Copy the Pin link from the Pinterest app, open this tool in Safari, paste, and download. Check the Files app if you don't see it in Photos.</p>
					<p><strong>Android:</strong> Copy the link from the Pinterest app, open this tool in Chrome, paste, and download. Files typically land in your Downloads folder.</p>
					<p><strong>Computer:</strong> Copy the Pin URL from your browser's address bar or the Pin's share menu, paste it here, and download. Files save to your browser's configured Downloads location.</p>
				</div>
			</div>
		</section>

		<!-- ── What Is & What You Can Download ────────────────────── -->
		<section class="pd-section-content">
			<div class="pd-section__container pd-section__container--narrow">
				
				<div class="pd-prose">
					<h2>What Is a Pinterest Video Downloader?</h2>
					<p>A Pinterest video downloader is a browser-based tool that reads a public Pin URL, checks what video media is attached to it, and lets you save that file to your device &mdash; without installing software.</p>
					<p>It's a separate process from Pinterest's own download option. According to <a href="https://help.pinterest.com/" target="_blank" rel="noopener">Pinterest's Help Center</a>, Pinterest allows creators to enable downloads for certain full-screen (9:16) video Pins directly through the app, and files downloaded that way can carry the creator's username as a watermark. Third-party tools like this one work independently of that setting and depend on what's technically accessible from the Pin itself.</p>

					<hr>

					<h2>What You Can Download</h2>
					<p><strong>Videos</strong> &mdash; the focus of this tool. Standard video Pins and most full-screen (9:16) video Pins are supported when the media is publicly accessible.</p>
					<p><strong>Images and photos</strong> &mdash; use our <a href="/pinterest-image-downloader/">Pinterest Image Downloader</a> instead. Pinterest also has a native image-download option for eligible Pins, which doesn't apply to every Pin.</p>
					<p><strong>GIFs and animated content</strong> &mdash; use our <a href="/pinterest-gif-downloader/">Pinterest GIF Downloader</a>. Note that content that looks like a GIF on Pinterest is sometimes delivered as a short video file rather than a true .gif; the final format depends on the source.</p>

					<!-- Tools cards -->
					<div class="pd-tools-grid">
						<a href="/pinterest-image-downloader/" class="pd-tool-card">
							<div class="pd-tool-card__icon">
								<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
									<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
									<circle cx="8.5" cy="8.5" r="1.5"/>
									<polyline points="21 15 16 10 5 21"/>
								</svg>
							</div>
							<div>
								<div class="pd-tool-card__title">Pinterest Image Downloader</div>
								<div class="pd-tool-card__desc">Download photos, pins, and boards in full original quality</div>
							</div>
						</a>

						<a href="/pinterest-gif-downloader/" class="pd-tool-card">
							<div class="pd-tool-card__icon">
								<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
									<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
								</svg>
							</div>
							<div>
								<div class="pd-tool-card__title">Pinterest GIF Downloader</div>
								<div class="pd-tool-card__desc">Extract and save animated loops and GIFs with one click</div>
							</div>
						</a>
					</div>

					<hr>

					<h2>What Works and What Doesn't</h2>
					<p>A failed attempt usually means one of the conditions below &mdash; not a broken tool:</p>
				</div>

				<div class="pd-table-wrap">
					<table class="pd-table">
						<thead>
							<tr>
								<th>Link or Content Type</th>
								<th>Result</th>
							</tr>
						</thead>
						<tbody>
							<tr><td><strong>Public video Pin</strong></td><td><span style="color:#16a34a;font-weight:700;">&check; Downloadable</span> when video media is accessible</td></tr>
							<tr><td><strong>pin.it short link</strong></td><td><span style="color:#16a34a;font-weight:700;">&check; Works</span> if it resolves to a valid Pin</td></tr>
							<tr><td><strong>Board or profile URL</strong></td><td><span style="color:#dc2626;font-weight:700;">&cross; Not supported</span> &mdash; use the individual Pin URL</td></tr>
							<tr><td><strong>Deleted Pin</strong></td><td><span style="color:#dc2626;font-weight:700;">&cross; No file available</span> &mdash; content no longer exists</td></tr>
							<tr><td><strong>Private or restricted Pin</strong></td><td><span style="color:#dc2626;font-weight:700;">&cross; Not available by design</span></td></tr>
							<tr><td><strong>Pin with no video attached</strong></td><td><span style="color:#eab308;font-weight:700;">&excl; No video result</span>, even though visible on Pinterest</td></tr>
						</tbody>
					</table>
				</div>

				<div class="pd-prose">
					<hr>

					<h2 id="pd-quality">Video Quality: HD, 1080p, 4K, and MP4</h2>
					<p><strong>Is HD available?</strong> Yes, when the source Pin has an HD version. Resolution varies by Pin &mdash; some creators upload in HD, some don't.</p>
					<p><strong>Is 4K available?</strong> Only if the original upload was 4K. No downloader &mdash; ours included &mdash; can produce genuine 4K detail from a lower-resolution source; anything claiming otherwise is upscaling, not recovering real quality.</p>
					<p><strong>Does downloading reduce quality?</strong> It can. The version Pinterest serves for download may be compressed differently than what you see while scrolling the feed.</p>
					<p><strong>What format do I get?</strong> MP4, when available, which covers most supported video Pins.</p>
					<p><strong>Why is there no sound on my downloaded video?</strong> Usually one of two reasons: the original Pin was uploaded without an audio track (common for aesthetic loops, text overlays, and slow-motion b-roll), or the audio was muted/restricted by the creator or platform. Check the speaker icon on the Pin itself in Pinterest before downloading &mdash; if it shows muted there, the file will be silent everywhere.</p>

					<hr>

					<h2>Is This Safe to Use?</h2>
					<p>You never need to enter your Pinterest password to use this tool. It only requires a public Pin URL &mdash; nothing else. If any Pinterest downloader asks for your login credentials, that's a red flag; close the tab.</p>
					<p><strong>What happens to the link you submit?</strong> We process the URL to locate the video, and we don't require or store Pinterest account credentials. Full details on data handling are in our <a href="/privacy-policy/">Privacy Policy</a>.</p>
					<p><strong>Public content only.</strong> This tool is built to work with publicly accessible Pins. It won't bypass private Pins or account restrictions &mdash; Pinterest's <a href="https://policy.pinterest.com/en/terms-of-service" target="_blank" rel="noopener">Terms of Service</a> explicitly prohibit circumventing access controls, and we don't attempt to.</p>

					<hr>

					<h2>Copyright and Responsible Use</h2>
					<p>Downloading a file doesn't transfer ownership of it. Per <a href="https://policy.pinterest.com/en/terms-of-service" target="_blank" rel="noopener">Pinterest's Terms of Service</a>, the person who posted a Pin is responsible for having the rights to that content &mdash; but that doesn't automatically mean you have the right to reuse it.</p>
					<p>Saving a video for personal reference is different from re-uploading it to your own channel, editing it into new content, or using it commercially. If you plan to do any of that, check with the original creator or confirm the licensing first. Pinterest maintains a <a href="https://policy.pinterest.com/en/copyright" target="_blank" rel="noopener">copyright reporting process</a> for rights holders if content is misused.</p>

					<hr>

					<h2>Pinterest's Save Feature vs. This Downloader</h2>
					<p>These solve different problems. <strong>Save</strong> adds a Pin to one of your Pinterest boards so you can find it again inside Pinterest &mdash; the content stays on the platform and needs an internet connection to view. <strong>Downloading</strong> creates an actual file on your device that works offline, can be edited, and stays even if the original Pin is deleted.</p>
					<p>Use Save when you're organizing ideas inside Pinterest. Use this tool when you need the actual video file.</p>

					<hr>

					<h2>Do You Need an App or Extension?</h2>
					<p>No. This tool runs in your browser, so there's nothing to install for the basic download workflow.</p>
					<p>If you're offered a Pinterest downloader <strong>APK</strong> from a site outside the Google Play Store, be cautious &mdash; sideloaded APKs from unknown sources are a common vector for malware. A browser-based tool avoids that risk entirely for occasional use.</p>

					<hr>

					<h2 id="pd-troubleshooting">Troubleshooting: Download Not Working?</h2>
				</div>

				<div class="pd-table-wrap">
					<table class="pd-table">
						<thead>
							<tr>
								<th>Problem</th>
								<th>Try This</th>
							</tr>
						</thead>
						<tbody>
							<tr><td>&ldquo;Invalid link&rdquo; error</td><td>Re-copy the URL directly from the Pin's Share menu</td></tr>
							<tr><td>No result returned</td><td>Confirm the Pin is still public and hasn't been deleted</td></tr>
							<tr><td>Video missing from result</td><td>The Pin may only contain an image &mdash; check on Pinterest directly</td></tr>
							<tr><td>Download won't start</td><td>Check your browser's download permissions/settings</td></tr>
							<tr><td>Page won't load</td><td>Refresh, or try Chrome/Safari/Firefox/Edge (current version)</td></tr>
							<tr><td>Can't find the saved file</td><td>Check your Downloads folder, or the Files app on iPhone</td></tr>
						</tbody>
					</table>
				</div>

				<div class="pd-prose">
					<p>If you've worked through this list and it still isn't working, <a href="/contact/">let us know</a> what happened &mdash; that helps us catch broken cases quickly.</p>

					<hr>

					<h2>Understanding Pinterest URLs</h2>
					<ul>
						<li><strong>Pin URL</strong> (<code>pinterest.com/pin/...</code>) &mdash; points to one specific piece of content. This is what you should paste here.</li>
						<li><strong>pin.it link</strong> &mdash; Pinterest's shortened share link. It needs to resolve correctly before we can process it; if it fails, grab the full Pin URL instead.</li>
						<li><strong>Board URL</strong> &mdash; a collection of multiple Pins. Not usable for a single video download.</li>
						<li><strong>Profile URL</strong> &mdash; a user's full Pinterest activity. Also not usable for a single video download.</li>
					</ul>
				</div>

			</div>
		</section>

		<!-- ── FAQ (All 12 User Questions) ────────────────────────── -->
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
							'q' => 'Is this Pinterest downloader free?',
							'a' => 'Yes, downloading videos through this tool is free, with no login required.',
						),
						array(
							'q' => 'Do I need a Pinterest account to use this?',
							'a' => 'No. You only need the public URL of the Pin.',
						),
						array(
							'q' => 'Can I download Pinterest videos on iPhone?',
							'a' => 'Yes — use Safari, paste the Pin link, and download. Files save to the Files app or your browser\'s download location.',
						),
						array(
							'q' => 'Can I download Pinterest videos on Android?',
							'a' => 'Yes — use Chrome (or another current browser), paste the link, and download to your Downloads folder.',
						),
						array(
							'q' => 'Can I download in HD?',
							'a' => 'Yes, when the source Pin has an HD version available.',
						),
						array(
							'q' => 'Can I download in 4K?',
							'a' => 'Only if the original video was uploaded in 4K. We can\'t create 4K quality that doesn\'t exist in the source.',
						),
						array(
							'q' => 'What file format will I get?',
							'a' => 'MP4, for the large majority of supported video Pins.',
						),
						array(
							'q' => 'Can I download videos with sound?',
							'a' => 'Yes, as long as the original Pin has an active audio track. If the source video is silent, the download will be silent too.',
						),
						array(
							'q' => 'Can I download a private Pin?',
							'a' => 'No. Private and restricted Pins are intentionally excluded — this isn\'t a limitation we plan to remove.',
						),
						array(
							'q' => 'Can I download a pin.it link?',
							'a' => 'Yes, as long as it resolves to a valid, public Pin.',
						),
						array(
							'q' => 'Can I reuse a downloaded video?',
							'a' => 'Not automatically. Downloading gives you a file, not usage rights. Check with the creator or review Pinterest\'s copyright policy before reusing content commercially.',
						),
						array(
							'q' => 'Is it safe?',
							'a' => 'Yes — we never ask for your Pinterest password, and we don\'t require an account to process a public Pin URL.',
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

		<!-- ── About PinDownloady & Legal Summary ─────────────────── -->
		<section class="pd-section-content--alt">
			<div class="pd-section__container pd-section__container--narrow">
				<div class="pd-prose">
					<h2>About This Tool</h2>
					<p><strong>PinDownloady</strong> is an independent, browser-based utility for downloading publicly available Pinterest video content. We are not affiliated with, endorsed by, or sponsored by Pinterest, Inc.</p>
					<p>This page is maintained by the PinDownloady editorial team and is reviewed whenever Pinterest changes its download behavior, Terms of Service, or content policies. Questions or a broken link to report? <a href="/contact/">Contact us</a> &mdash; we read every submission.</p>
					<p>
						<strong>Last reviewed:</strong> September 2026<br>
						<strong>Privacy Policy:</strong> <a href="/privacy-policy/">Read our full data handling practices</a><br>
						<strong>Terms of Use:</strong> <a href="/terms-of-service/">Read the terms for using this tool</a>
					</p>
				</div>

				<!-- Feedback Widget -->
				<div class="pd-feedback-card">
					<h3>Was this helpful?</h3>
					<div class="pd-feedback-actions" id="pd-feedback-actions">
						<button type="button" class="pd-feedback-btn" onclick="handleFeedback(true)">
							<span>👍</span> Yes
						</button>
						<button type="button" class="pd-feedback-btn" onclick="handleFeedback(false)">
							<span>👎</span> No
						</button>
					</div>
					<div class="pd-feedback-msg" id="pd-feedback-msg">
						Thank you for your feedback! It helps us improve.
					</div>
					<p style="font-size:0.875rem; color:#64748b; margin-top:12px;">
						If something didn't work, <a href="/contact/" style="color:#2563eb;font-weight:600;">tell us what happened</a> &mdash; it helps us fix broken links faster.
					</p>
				</div>

				<!-- Bottom Quick Downloader CTA -->
				<div class="pd-bottom-hero">
					<h3>Ready to Download?</h3>
					<p>Paste any Pinterest URL above to grab HD MP4 videos without watermarks.</p>
					<a href="#pd-hero-heading" class="pd-btn-scroll" onclick="window.scrollTo({top:0,behavior:'smooth'});document.getElementById('pd-url-input').focus();return false;">
						<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
						</svg>
						<span>Back to Downloader</span>
					</a>
				</div>

			</div>
		</section>

	</main>

	<!-- ── Disclaimer Banner ──────────────────────────────────────── -->
	<div class="pd-disclaimer">
		<div class="pd-disclaimer__inner">
			<p>
				PinDownloady is an independent tool built to help users save Pinterest content for personal use. We have no relationship with Pinterest Inc. or any of their subsidiaries. All trademarks, logos, and brand names belong to their respective owners. Only download content you have the right to download.
			</p>
		</div>
	</div>

	<!-- ── Footer ─────────────────────────────────────────────────── -->
	<footer class="pd-footer" role="contentinfo">
		<div class="pd-footer__top">

			<div>
				<a href="/" class="pd-footer__brand" aria-label="PinDownloady Home">
					<div class="pd-footer__logo-box" aria-hidden="true">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
						     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
							<path stroke-linecap="round" stroke-linejoin="round"
							      d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 11l5 5 5-5M12 4v12"/>
						</svg>
					</div>
					<span class="pd-footer__brand-name">
						Pin<span>Downloady</span>
					</span>
				</a>
				<p class="pd-footer__tagline">
					The fastest free Pinterest downloader. No watermark, no account, no limits.
				</p>
			</div>

			<div>
				<h3 class="pd-footer__col-title"><?php esc_html_e( 'Quick Links', 'pinterest-downloader' ); ?></h3>
				<ul class="pd-footer__links">
					<li><a href="#pd-how-it-works"><?php esc_html_e( 'How It Works', 'pinterest-downloader' ); ?></a></li>
					<li><a href="#pd-device-guide"><?php esc_html_e( 'Device Guide', 'pinterest-downloader' ); ?></a></li>
					<li><a href="#pd-troubleshooting"><?php esc_html_e( 'Troubleshooting', 'pinterest-downloader' ); ?></a></li>
					<li><a href="#pd-faq"><?php esc_html_e( 'FAQ', 'pinterest-downloader' ); ?></a></li>
					<li><a href="/privacy-policy/"><?php esc_html_e( 'Privacy Policy', 'pinterest-downloader' ); ?></a></li>
					<li><a href="/terms-of-service/"><?php esc_html_e( 'Terms of Service', 'pinterest-downloader' ); ?></a></li>
					<li><a href="/contact/"><?php esc_html_e( 'Contact Us', 'pinterest-downloader' ); ?></a></li>
				</ul>
			</div>

			<div>
				<h3 class="pd-footer__col-title"><?php esc_html_e( 'Download Types', 'pinterest-downloader' ); ?></h3>
				<ul class="pd-footer__links">
					<li><a href="/"><?php esc_html_e( 'Pinterest Video Downloader', 'pinterest-downloader' ); ?></a></li>
					<li><a href="/pinterest-image-downloader/"><?php esc_html_e( 'Pinterest Image Downloader', 'pinterest-downloader' ); ?></a></li>
					<li><a href="/pinterest-gif-downloader/"><?php esc_html_e( 'Pinterest GIF Downloader', 'pinterest-downloader' ); ?></a></li>
				</ul>
			</div>

		</div>

		<div class="pd-footer__bottom">
			<div class="pd-footer__bottom-inner">
				<p class="pd-footer__copy">
					&copy; <span id="pd-footer-year"></span> PinDownloady. All rights reserved.
				</p>
			</div>
		</div>

		<script>
			var y = document.getElementById('pd-footer-year');
			if (y) y.textContent = new Date().getFullYear();
			function handleFeedback(isYes){
				var acts = document.getElementById('pd-feedback-actions');
				var msg = document.getElementById('pd-feedback-msg');
				if(acts) acts.style.display = 'none';
				if(msg) msg.style.display = 'block';
			}
		</script>
	</footer>

</div><!-- .pd-app -->
