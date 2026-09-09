<?php
/**
 * Main downloader wrapper template — PinDownloady (Optimized for WordPress & All Devices).
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

<!-- ── Critical WordPress & Theme Compatibility Styles (Inlined for 100% Instant Rendering) ── -->
<style id="pd-critical-inline-css">
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

  .pd-app, .pd-app *, .entry-content .pd-app, .entry-content .pd-app * {
    box-sizing: border-box !important;
  }

  .pd-app {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    color: #1a1f36 !important;
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 auto !important;
    line-height: 1.6 !important;
    -webkit-font-smoothing: antialiased !important;
  }

  /* Pull plugin upward to cancel WordPress theme's content-area top padding */
  .entry-content .pd-app,
  .wp-block-post-content .pd-app,
  .site-content .pd-app,
  article .pd-app {
    margin-top: -2em !important;
  }

  /* ── Hero Box ──────────────────────────────────────────────────── */
  .pd-app .pd-hero {
    background: linear-gradient(160deg, #1a3fd4 0%, #2152e8 40%, #1e45d6 70%, #1535b8 100%) !important;
    border-radius: 0 !important;
    padding: clamp(40px, 6vw, 72px) clamp(16px, 5vw, 80px) !important;
    text-align: center !important;
    color: #ffffff !important;
    width: 100vw !important;
    position: relative !important;
    left: 50% !important;
    right: 50% !important;
    margin-left: -50vw !important;
    margin-right: -50vw !important;
    margin-bottom: 32px !important;
  }

  /* Center the content inside the full-width hero */
  .pd-app .pd-hero__container {
    max-width: 860px !important;
    margin: 0 auto !important;
    padding: 0 20px !important;
  }

  /* ── Logo Badge (TikSav style) ─────────────────────────────── */
  .pd-app .pd-logo-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
    margin-bottom: 22px !important;
    text-decoration: none !important;
  }

  .pd-app .pd-logo-icon {
    width: 40px !important;
    height: 40px !important;
    border-radius: 10px !important;
    object-fit: cover !important;
    display: block !important;
    flex-shrink: 0 !important;
  }

  .pd-app .pd-logo-name {
    font-size: 20px !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    font-family: 'Inter', sans-serif !important;
    letter-spacing: -0.02em !important;
    line-height: 1 !important;
  }

  .pd-app .pd-hero__title {
    color: #ffffff !important;
    font-family: 'Inter', sans-serif !important;
    font-size: clamp(22px, 4.5vw, 38px) !important;
    font-weight: 800 !important;
    line-height: 1.2 !important;
    letter-spacing: -0.025em !important;
    margin: 0 0 16px 0 !important;
    text-align: center !important;
  }

  .pd-app .pd-hero__subtitle {
    color: rgba(255, 255, 255, 0.88) !important;
    font-family: 'Inter', sans-serif !important;
    font-size: clamp(15px, 2.2vw, 17px) !important;
    font-weight: 400 !important;
    line-height: 1.6 !important;
    max-width: 620px !important;
    margin: 0 auto 30px auto !important;
    text-align: center !important;
  }

  /* ── Unified Input Form ────────────────────────────────────────── */
  .pd-app .pd-input-container {
    width: 100% !important;
    max-width: 680px !important;
    margin: 0 auto !important;
  }

  .pd-app .pd-input-box {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    background: #ffffff !important;
    border-radius: 16px !important;
    padding: 6px !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.22) !important;
    border: 2px solid transparent !important;
    transition: box-shadow 0.2s, border-color 0.2s !important;
  }

  .pd-app .pd-input-box:focus-within {
    border-color: #93c5fd !important;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.32) !important;
  }

  .pd-app .pd-input-icon {
    padding: 0 8px 0 14px !important;
    color: #94a3b8 !important;
    display: flex !important;
    align-items: center !important;
    flex-shrink: 0 !important;
  }

  .pd-app .pd-input-field {
    flex: 1 1 auto !important;
    border: none !important;
    outline: none !important;
    background: transparent !important;
    padding: 12px 8px !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 16px !important;
    color: #0f172a !important;
    min-width: 0 !important;
    box-shadow: none !important;
  }

  .pd-app .pd-input-actions {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-shrink: 0 !important;
  }

  .pd-app .pd-paste-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    background: #f1f5f9 !important;
    border: none !important;
    border-radius: 10px !important;
    color: #475569 !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    padding: 11px 16px !important;
    cursor: pointer !important;
    transition: background 0.15s, color 0.15s !important;
    text-decoration: none !important;
  }

  .pd-app .pd-paste-btn:hover {
    background: #e2e8f0 !important;
    color: #2563eb !important;
  }

  .pd-app .pd-btn-primary {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    background: #2563eb !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 13px 26px !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 16px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.40) !important;
    white-space: nowrap !important;
    text-decoration: none !important;
    transition: transform 0.18s, background 0.18s, box-shadow 0.18s !important;
  }

  .pd-app .pd-btn-primary:hover {
    background: #1d4ed8 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50) !important;
  }

  .pd-app .pd-terms-note {
    margin-top: 14px !important;
    font-size: 13px !important;
    color: rgba(255, 255, 255, 0.65) !important;
    text-align: center !important;
  }

  .pd-app .pd-terms-note a {
    color: rgba(255, 255, 255, 0.85) !important;
    text-decoration: underline !important;
  }

  /* ── Mobile Form Layout ────────────────────────────────────────── */
  @media (max-width: 600px) {
    .pd-app .pd-input-box {
      flex-wrap: wrap !important;
      gap: 10px !important;
      padding: 12px !important;
    }
    .pd-app .pd-input-icon {
      display: none !important;
    }
    .pd-app .pd-input-field {
      width: 100% !important;
      padding: 8px 4px !important;
      border-bottom: 1px solid #e2e8f0 !important;
      font-size: 15px !important;
    }
    .pd-app .pd-input-actions {
      width: 100% !important;
      gap: 8px !important;
    }
    .pd-app .pd-paste-btn {
      flex: 1 !important;
      justify-content: center !important;
      padding: 12px 10px !important;
    }
    .pd-app .pd-btn-primary {
      flex: 2 !important;
      justify-content: center !important;
      padding: 12px 16px !important;
    }
  }

  /* ── States ─────────────────────────────────────────────────────── */
  /* Input is always visible — only result/error/loading are state-driven */
  .pd-app .pd-state {
    display: none !important;
  }
  .pd-app .pd-state.is-active {
    display: block !important;
  }

  /* Loading bar — shown below input, not replacing it */
  .pd-app .pd-loading-bar {
    margin-top: 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 10px !important;
    padding: 10px 0 0 0 !important;
  }

  .pd-app .pd-loading-bar .pd-dot-loader {
    display: flex !important;
    gap: 7px !important;
  }

  .pd-app .pd-loading-bar .pd-dot-loader span {
    width: 9px !important;
    height: 9px !important;
    border-radius: 50% !important;
    background: rgba(255,255,255,0.85) !important;
    animation: pdPulse 1.3s ease-in-out infinite !important;
    display: inline-block !important;
  }
  .pd-app .pd-loading-bar .pd-dot-loader span:nth-child(2) { animation-delay: 0.18s !important; }
  .pd-app .pd-loading-bar .pd-dot-loader span:nth-child(3) { animation-delay: 0.36s !important; }

  .pd-app .pd-loading-bar .pd-loading-text {
    color: rgba(255,255,255,0.8) !important;
    font-size: 13px !important;
    margin: 0 !important;
  }

  /* Input disabled state during loading */
  .pd-app .pd-input-field:disabled {
    opacity: 0.65 !important;
    cursor: not-allowed !important;
  }
  .pd-app .pd-btn-primary:disabled {
    opacity: 0.65 !important;
    cursor: not-allowed !important;
    transform: none !important;
  }

  /* Loading State */
  .pd-app .pd-loading-container {
    padding: 32px 16px !important;
    text-align: center !important;
  }

  .pd-app .pd-loading-dots {
    display: flex !important;
    justify-content: center !important;
    gap: 8px !important;
    margin-bottom: 12px !important;
  }

  .pd-app .pd-loading-dots span {
    width: 10px !important;
    height: 10px !important;
    border-radius: 50% !important;
    background: #ffffff !important;
    animation: pdPulse 1.4s ease-in-out infinite !important;
  }
  .pd-app .pd-loading-dots span:nth-child(2) { animation-delay: 0.2s !important; }
  .pd-app .pd-loading-dots span:nth-child(3) { animation-delay: 0.4s !important; }

  @keyframes pdPulse {
    0%, 80%, 100% { transform: scale(0.65); opacity: 0.4; }
    40% { transform: scale(1.15); opacity: 1; }
  }

  .pd-app .pd-loading-text {
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 14px !important;
    margin: 0 !important;
  }

  /* ── Result Card ───────────────────────────────────────────────── */
  .pd-app .pd-result-outer {
    max-width: 680px !important;
    margin: 0 auto 40px auto !important;
  }

  .pd-app .pd-result-card {
    background: #ffffff !important;
    border-radius: 20px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.08) !important;
    overflow: hidden !important;
  }

  .pd-app .pd-result-top {
    padding: 20px !important;
  }

  .pd-app .pd-result-flex {
    display: flex !important;
    gap: 16px !important;
    align-items: flex-start !important;
  }

  .pd-app .pd-thumb-wrap {
    width: 90px !important;
    height: 120px !important;
    border-radius: 12px !important;
    overflow: hidden !important;
    background: #f1f5f9 !important;
    flex-shrink: 0 !important;
    position: relative !important;
  }

  .pd-app .pd-thumb-img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    display: block !important;
  }

  .pd-app .pd-result-info {
    flex: 1 1 auto !important;
    min-width: 0 !important;
  }

  .pd-app .pd-result-author {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #2563eb !important;
    margin-bottom: 6px !important;
    display: block !important;
  }

  .pd-app .pd-result-title {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.4 !important;
    margin: 0 0 10px 0 !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
    overflow: hidden !important;
  }

  .pd-app .pd-badges {
    display: flex !important;
    gap: 6px !important;
    flex-wrap: wrap !important;
  }

  .pd-app .pd-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    padding: 3px 8px !important;
    border-radius: 9999px !important;
    background: #eff6ff !important;
    color: #1d4ed8 !important;
    border: 1px solid #bfdbfe !important;
  }

  .pd-app .pd-divider {
    height: 1px !important;
    background: #f1f5f9 !important;
  }

  .pd-app .pd-formats {
    padding: 20px !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 10px !important;
  }

  .pd-app .pd-dl-btn {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    padding: 14px 18px !important;
    border-radius: 12px !important;
    font-family: 'Inter', sans-serif !important;
    font-weight: 700 !important;
    font-size: 15px !important;
    text-decoration: none !important;
    cursor: pointer !important;
    transition: transform 0.17s, box-shadow 0.17s !important;
  }

  .pd-app .pd-dl-btn:hover {
    transform: translateY(-2px) !important;
  }

  .pd-app .pd-dl-btn-mp4 {
    background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35) !important;
  }

  .pd-app .pd-dl-btn-hd {
    background: #ffffff !important;
    color: #1d4ed8 !important;
    border: 1.5px solid #bfdbfe !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.08) !important;
  }

  .pd-app .pd-dl-btn-img {
    background: #ffffff !important;
    color: #0369a1 !important;
    border: 1.5px solid #bae6fd !important;
    box-shadow: 0 2px 8px rgba(3, 105, 161, 0.08) !important;
  }

  .pd-app .pd-reset-wrap {
    text-align: center !important;
    margin-top: 14px !important;
  }

  .pd-app .pd-reset-btn {
    background: transparent !important;
    border: none !important;
    color: #2563eb !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    text-decoration: none !important;
  }

  /* ── Error State ───────────────────────────────────────────────── */
  .pd-app .pd-error-card {
    background: #ffffff !important;
    border-radius: 20px !important;
    border: 1px solid #fee2e2 !important;
    padding: 28px 20px !important;
    text-align: center !important;
    max-width: 540px !important;
    margin: 0 auto 32px auto !important;
    box-shadow: 0 8px 24px rgba(239, 68, 68, 0.08) !important;
  }

  .pd-app .pd-error-icon {
    width: 56px !important;
    height: 56px !important;
    border-radius: 50% !important;
    background: #fee2e2 !important;
    color: #dc2626 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 auto 14px auto !important;
  }

  .pd-app .pd-error-title {
    font-size: 18px !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    margin: 0 0 8px 0 !important;
  }

  .pd-app .pd-error-desc {
    font-size: 14px !important;
    color: #475569 !important;
    margin: 0 0 20px 0 !important;
    line-height: 1.5 !important;
  }

  /* ── Quick Navigation Pills Bar ────────────────────────────────── */
  .pd-app .pd-nav-pills {
    display: flex !important;
    justify-content: center !important;
    flex-wrap: wrap !important;
    gap: 8px !important;
    margin: 0 auto 32px auto !important;
  }

  .pd-app .pd-pill-link {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 8px 16px !important;
    border-radius: 9999px !important;
    background: #f1f5f9 !important;
    border: 1px solid #e2e8f0 !important;
    color: #334155 !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
  }

  .pd-app .pd-pill-link:hover {
    background: #eff6ff !important;
    border-color: #bfdbfe !important;
    color: #2563eb !important;
  }

  /* ── Content & Typography Formatting ───────────────────────────── */
  .pd-app .pd-editorial-meta {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    background: #eff6ff !important;
    border: 1px solid #dbeafe !important;
    color: #1e40af !important;
    padding: 8px 16px !important;
    border-radius: 9999px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    width: fit-content !important;
    margin: 0 auto 24px auto !important;
  }

  .pd-app .pd-section-title {
    font-size: clamp(20px, 3.5vw, 28px) !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    line-height: 1.3 !important;
    margin: 36px 0 16px 0 !important;
    text-align: left !important;
  }

  .pd-app .pd-prose p {
    font-size: 16px !important;
    line-height: 1.7 !important;
    color: #334155 !important;
    margin: 0 0 16px 0 !important;
  }

  .pd-app .pd-prose a {
    color: #2563eb !important;
    font-weight: 600 !important;
    text-decoration: underline !important;
  }

  /* ── Tables ────────────────────────────────────────────────────── */
  .pd-app .pd-table-wrap {
    width: 100% !important;
    overflow-x: auto !important;
    border-radius: 14px !important;
    border: 1.5px solid #e2e8f0 !important;
    background: #ffffff !important;
    margin: 20px 0 28px 0 !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03) !important;
  }

  .pd-app .pd-table {
    width: 100% !important;
    border-collapse: collapse !important;
    text-align: left !important;
    font-size: 14px !important;
    margin: 0 !important;
  }

  .pd-app .pd-table th {
    background: #f8fafc !important;
    color: #0f172a !important;
    font-weight: 700 !important;
    padding: 12px 16px !important;
    border-bottom: 1.5px solid #cbd5e1 !important;
  }

  .pd-app .pd-table td {
    padding: 12px 16px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    color: #334155 !important;
    line-height: 1.5 !important;
  }

  .pd-app .pd-table tbody tr:last-child td {
    border-bottom: none !important;
  }

  /* ── Steps 5 Grid ──────────────────────────────────────────────── */
  .pd-app .pd-steps-grid {
    display: grid !important;
    grid-template-columns: 1fr !important;
    gap: 16px !important;
    list-style: none !important;
    padding: 0 !important;
    margin: 20px 0 32px 0 !important;
  }

  @media (min-width: 600px) {
    .pd-app .pd-steps-grid {
      grid-template-columns: repeat(2, 1fr) !important;
    }
  }
  @media (min-width: 900px) {
    .pd-app .pd-steps-grid {
      grid-template-columns: repeat(3, 1fr) !important;
    }
  }

  .pd-app .pd-step-card {
    background: #ffffff !important;
    border: 1.5px solid #f1f5f9 !important;
    border-radius: 16px !important;
    padding: 20px !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.04) !important;
  }

  .pd-app .pd-step-num {
    width: 40px !important;
    height: 40px !important;
    border-radius: 10px !important;
    background: #2563eb !important;
    color: #ffffff !important;
    font-size: 18px !important;
    font-weight: 800 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-bottom: 12px !important;
  }

  .pd-app .pd-step-title {
    font-size: 16px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 0 6px 0 !important;
  }

  .pd-app .pd-step-desc {
    font-size: 14px !important;
    color: #64748b !important;
    line-height: 1.5 !important;
    margin: 0 !important;
  }

  /* ── FAQ Accordion ─────────────────────────────────────────────── */
  .pd-app .pd-faq-list {
    display: flex !important;
    flex-direction: column !important;
    gap: 10px !important;
    margin: 20px 0 32px 0 !important;
  }

  .pd-app .pd-faq-item {
    background: #ffffff !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 14px !important;
    overflow: hidden !important;
  }

  .pd-app .pd-faq-toggle {
    width: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 14px !important;
    padding: 16px 20px !important;
    background: transparent !important;
    border: none !important;
    cursor: pointer !important;
    text-align: left !important;
    box-shadow: none !important;
  }

  .pd-app .pd-faq-question {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    flex: 1 !important;
  }

  .pd-app .pd-faq-icon {
    width: 26px !important;
    height: 26px !important;
    border-radius: 6px !important;
    background: #eff6ff !important;
    color: #2563eb !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    transition: transform 0.2s, background 0.2s !important;
  }

  .pd-app .pd-faq-item.is-open .pd-faq-icon {
    background: #2563eb !important;
    color: #ffffff !important;
    transform: rotate(45deg) !important;
  }

  .pd-app .pd-faq-answer {
    max-height: 0 !important;
    overflow: hidden !important;
    padding: 0 20px !important;
    font-size: 14px !important;
    color: #475569 !important;
    line-height: 1.6 !important;
    transition: max-height 0.3s ease, padding 0.2s ease !important;
  }

  .pd-app .pd-faq-item.is-open .pd-faq-answer {
    max-height: 350px !important;
    padding-bottom: 16px !important;
  }

  /* ── Feedback Box ──────────────────────────────────────────────── */
  .pd-app .pd-feedback-card {
    background: #f8fafc !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 16px !important;
    padding: 24px 16px !important;
    text-align: center !important;
    max-width: 500px !important;
    margin: 32px auto !important;
  }

  .pd-app .pd-feedback-card h3 {
    font-size: 16px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 0 12px 0 !important;
  }

  .pd-app .pd-feedback-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 10px 20px !important;
    border-radius: 10px !important;
    border: 1.5px solid #cbd5e1 !important;
    background: #ffffff !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    cursor: pointer !important;
    margin: 0 6px !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04) !important;
  }

  .pd-app .pd-feedback-btn:hover {
    background: #eff6ff !important;
    border-color: #2563eb !important;
    color: #2563eb !important;
  }
</style>

<div class="pd-app" data-pd-type="<?php echo esc_attr( $type ); ?>" data-pd-layout="<?php echo esc_attr( $layout ); ?>">

	<main>

		<!-- ── Hero Section (Styled for WordPress) ───────────────────── -->
		<section class="pd-hero" aria-labelledby="pd-hero-heading">
			<div class="pd-hero__container">

				<!-- Logo Badge — TikSav style icon + site name -->
				<div class="pd-logo-badge">
					<img
						src="<?php echo esc_url( PD_PLUGIN_URL . 'public/images/logo.png' ); ?>"
						alt="PinDownloady logo"
						class="pd-logo-icon"
						width="40"
						height="40"
					/>
					<span class="pd-logo-name">PinDownloady</span>
				</div>

				<h1 class="pd-hero__title" id="pd-hero-heading">
					<?php echo $hero_title; ?>
				</h1>

				<p class="pd-hero__subtitle">
					<?php echo $hero_subtitle; ?>
				</p>

				<!-- Input form — ALWAYS VISIBLE, never hidden by state machine -->
				<div id="pd-input-section">
					<?php include PD_PLUGIN_DIR . 'templates/input.php'; ?>
				</div>

				<!-- Loading indicator — compact, shown below input during fetch -->
				<div id="pd-state-loading" class="pd-state" role="status" aria-live="polite" aria-hidden="true">
					<div class="pd-loading-bar">
						<div class="pd-dot-loader" aria-hidden="true">
							<span></span><span></span><span></span>
						</div>
						<p class="pd-loading-text"><?php esc_html_e( 'Fetching media info…', 'pinterest-downloader' ); ?></p>
					</div>
				</div>

			</div>
		</section>

		<!-- ── Result Card — shows below hero, persists for new downloads ── -->
		<div class="pd-result-outer">
			<div class="pd-state pd-state--result" id="pd-state-result" aria-live="polite" aria-hidden="true">
				<?php include PD_PLUGIN_DIR . 'templates/result.php'; ?>
			</div>
		</div>

		<!-- ── Error Card — shows below hero ────────────────────────────── -->
		<div class="pd-error-outer">
			<div class="pd-state pd-state--error" id="pd-state-error" aria-live="assertive" aria-hidden="true">
				<?php include PD_PLUGIN_DIR . 'templates/error.php'; ?>
			</div>
		</div>

		<?php if ( 'tool' === $layout ) : ?>
	</main>
		<?php else : ?>

		<!-- ── Quick Navigation Pills Bar ──────────────────────────── -->
		<div class="pd-nav-pills">
			<a href="#pd-how-it-works" class="pd-pill-link">&#8595; How It Works</a>
			<a href="#pd-device-guide" class="pd-pill-link">&#8595; Device Guide</a>
			<a href="#pd-troubleshooting" class="pd-pill-link">&#8595; Troubleshooting</a>
			<a href="#pd-faq" class="pd-pill-link">&#8595; FAQ</a>
			<a href="/pinterest-image-downloader/" class="pd-pill-link">&#128444; Images</a>
			<a href="/pinterest-gif-downloader/" class="pd-pill-link">&#127916; GIFs</a>
		</div>

		<!-- ── Editorial Meta & Intro Callout ──────────────────────── -->
		<div class="pd-editorial-meta">
			<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
				<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
			</svg>
			<span>Last updated: September 2026 &middot; Reviewed by the PinDownloady Editorial Team</span>
		</div>

		<div class="pd-prose">
			<p>This tool works entirely in your browser on phones, tablets, and computers. It does not require you to install an app or sign in to Pinterest.</p>
			<p><em>If a Pin is private, deleted, or does not contain video media that Pinterest allows to be accessed, the tool will not return a file &mdash; no downloader can retrieve content that isn't publicly available.</em></p>
			<hr style="border:none; height:1px; background:#e2e8f0; margin: 32px 0;">
		</div>

		<!-- ── How to Download (5 Steps) ───────────────────────────── -->
		<section id="pd-how-it-works">
			<h2 class="pd-section-title">How to Download a Pinterest Video</h2>
			<div class="pd-prose">
				<p>You need one thing: the URL of the specific Pin. Here's the full process.</p>
			</div>

			<ol class="pd-steps-grid">
				<li class="pd-step-card">
					<div class="pd-step-num">1</div>
					<h3 class="pd-step-title">Copy the Pin URL</h3>
					<p class="pd-step-desc">Open the video Pin on Pinterest (app or browser) and tap <strong>Share &rarr; Copy Link</strong>. On desktop, copy directly from your browser's address bar. (Use the individual Pin link &mdash; not a board or profile link).</p>
				</li>
				<li class="pd-step-card">
					<div class="pd-step-num">2</div>
					<h3 class="pd-step-title">Paste It Into the Downloader</h3>
					<p class="pd-step-desc">Paste the copied link into the box above and select <strong>Download</strong>. The tool checks the link and looks for video media attached to that Pin.</p>
				</li>
				<li class="pd-step-card">
					<div class="pd-step-num">3</div>
					<h3 class="pd-step-title">Preview the Result</h3>
					<p class="pd-step-desc">Once processed, a preview appears so you can confirm it's the right video before saving it. Where available, you'll also see the resolution and file format.</p>
				</li>
				<li class="pd-step-card">
					<div class="pd-step-num">4</div>
					<h3 class="pd-step-title">Pick a Quality</h3>
					<p class="pd-step-desc">If the Pin has more than one available resolution, choose the one you want. We only show quality levels that genuinely exist in the source &mdash; we don't upscale or fake a higher resolution.</p>
				</li>
				<li class="pd-step-card">
					<div class="pd-step-num">5</div>
					<h3 class="pd-step-title">Download</h3>
					<p class="pd-step-desc">Select <strong>Download</strong>. Your browser handles the save based on its own settings &mdash; some browsers ask where to save, others save automatically to a default folder.</p>
				</li>
			</ol>
		</section>

		<!-- ── Device-Specific Instructions Table ──────────────────── -->
		<section id="pd-device-guide">
			<h2 class="pd-section-title">Device-Specific Instructions</h2>

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
				<hr style="border:none; height:1px; background:#e2e8f0; margin: 32px 0;">
			</div>
		</section>

		<!-- ── What Is & What You Can Download ────────────────────── -->
		<section>
			<div class="pd-prose">
				<h2 class="pd-section-title">What Is a Pinterest Video Downloader?</h2>
				<p>A Pinterest video downloader is a browser-based tool that reads a public Pin URL, checks what video media is attached to it, and lets you save that file to your device &mdash; without installing software.</p>
				<p>It's a separate process from Pinterest's own download option. According to <a href="https://help.pinterest.com/" target="_blank" rel="noopener">Pinterest's Help Center</a>, Pinterest allows creators to enable downloads for certain full-screen (9:16) video Pins directly through the app, and files downloaded that way can carry the creator's username as a watermark. Third-party tools like this one work independently of that setting and depend on what's technically accessible from the Pin itself.</p>

				<h2 class="pd-section-title">What You Can Download</h2>
				<p><strong>Videos</strong> &mdash; the focus of this tool. Standard video Pins and most full-screen (9:16) video Pins are supported when the media is publicly accessible.</p>
				<p><strong>Images and photos</strong> &mdash; use our <a href="/pinterest-image-downloader/">Pinterest Image Downloader</a> instead. Pinterest also has a native image-download option for eligible Pins, which doesn't apply to every Pin.</p>
				<p><strong>GIFs and animated content</strong> &mdash; use our <a href="/pinterest-gif-downloader/">Pinterest GIF Downloader</a>. Note that content that looks like a GIF on Pinterest is sometimes delivered as a short video file rather than a true .gif; the final format depends on the source.</p>

				<h2 class="pd-section-title">What Works and What Doesn't</h2>
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
				<h2 class="pd-section-title" id="pd-quality">Video Quality: HD, 1080p, 4K, and MP4</h2>
				<p><strong>Is HD available?</strong> Yes, when the source Pin has an HD version. Resolution varies by Pin &mdash; some creators upload in HD, some don't.</p>
				<p><strong>Is 4K available?</strong> Only if the original upload was 4K. No downloader &mdash; ours included &mdash; can produce genuine 4K detail from a lower-resolution source; anything claiming otherwise is upscaling, not recovering real quality.</p>
				<p><strong>Does downloading reduce quality?</strong> It can. The version Pinterest serves for download may be compressed differently than what you see while scrolling the feed.</p>
				<p><strong>What format do I get?</strong> MP4, when available, which covers most supported video Pins.</p>
				<p><strong>Why is there no sound on my downloaded video?</strong> Usually one of two reasons: the original Pin was uploaded without an audio track (common for aesthetic loops, text overlays, and slow-motion b-roll), or the audio was muted/restricted by the creator or platform. Check the speaker icon on the Pin itself in Pinterest before downloading &mdash; if it shows muted there, the file will be silent everywhere.</p>

				<h2 class="pd-section-title">Is This Safe to Use?</h2>
				<p>You never need to enter your Pinterest password to use this tool. It only requires a public Pin URL &mdash; nothing else. If any Pinterest downloader asks for your login credentials, that's a red flag; close the tab.</p>
				<p><strong>What happens to the link you submit?</strong> We process the URL to locate the video, and we don't require or store Pinterest account credentials. Full details on data handling are in our <a href="/privacy-policy/">Privacy Policy</a>.</p>
				<p><strong>Public content only.</strong> This tool is built to work with publicly accessible Pins. It won't bypass private Pins or account restrictions &mdash; Pinterest's <a href="https://policy.pinterest.com/en/terms-of-service" target="_blank" rel="noopener">Terms of Service</a> explicitly prohibit circumventing access controls, and we don't attempt to.</p>

				<h2 class="pd-section-title">Copyright and Responsible Use</h2>
				<p>Downloading a file doesn't transfer ownership of it. Per <a href="https://policy.pinterest.com/en/terms-of-service" target="_blank" rel="noopener">Pinterest's Terms of Service</a>, the person who posted a Pin is responsible for having the rights to that content &mdash; but that doesn't automatically mean you have the right to reuse it.</p>
				<p>Saving a video for personal reference is different from re-uploading it to your own channel, editing it into new content, or using it commercially. If you plan to do any of that, check with the original creator or confirm the licensing first.</p>

				<h2 class="pd-section-title">Pinterest's Save Feature vs. This Downloader</h2>
				<p>These solve different problems. <strong>Save</strong> adds a Pin to one of your Pinterest boards so you can find it again inside Pinterest &mdash; the content stays on the platform and needs an internet connection to view. <strong>Downloading</strong> creates an actual file on your device that works offline, can be edited, and stays even if the original Pin is deleted.</p>

				<h2 class="pd-section-title">Do You Need an App or Extension?</h2>
				<p>No. This tool runs in your browser, so there's nothing to install for the basic download workflow. If you're offered a Pinterest downloader APK from a site outside the Google Play Store, be cautious &mdash; sideloaded APKs from unknown sources are a common vector for malware.</p>

				<h2 class="pd-section-title" id="pd-troubleshooting">Troubleshooting: Download Not Working?</h2>
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
				<h2 class="pd-section-title">Understanding Pinterest URLs</h2>
				<ul>
					<li><strong>Pin URL</strong> (<code>pinterest.com/pin/...</code>) &mdash; points to one specific piece of content. This is what you should paste here.</li>
					<li><strong>pin.it link</strong> &mdash; Pinterest's shortened share link. It needs to resolve correctly before we can process it; if it fails, grab the full Pin URL instead.</li>
					<li><strong>Board URL</strong> &mdash; a collection of multiple Pins. Not usable for a single video download.</li>
					<li><strong>Profile URL</strong> &mdash; a user's full Pinterest activity. Also not usable for a single video download.</li>
				</ul>
				<hr style="border:none; height:1px; background:#e2e8f0; margin: 32px 0;">
			</div>
		</section>

		<!-- ── FAQ (All 12 User Questions) ────────────────────────── -->
		<section id="pd-faq">
			<h2 class="pd-section-title">Frequently Asked Questions</h2>

			<div class="pd-faq-list">
				<?php
				$faqs = array(
					array( 'q' => 'Is this Pinterest downloader free?', 'a' => 'Yes, downloading videos through this tool is free, with no login required.' ),
					array( 'q' => 'Do I need a Pinterest account to use this?', 'a' => 'No. You only need the public URL of the Pin.' ),
					array( 'q' => 'Can I download Pinterest videos on iPhone?', 'a' => 'Yes — use Safari, paste the Pin link, and download. Files save to the Files app or your browser\'s download location.' ),
					array( 'q' => 'Can I download Pinterest videos on Android?', 'a' => 'Yes — use Chrome (or another current browser), paste the link, and download to your Downloads folder.' ),
					array( 'q' => 'Can I download in HD?', 'a' => 'Yes, when the source Pin has an HD version available.' ),
					array( 'q' => 'Can I download in 4K?', 'a' => 'Only if the original video was uploaded in 4K. We can\'t create 4K quality that doesn\'t exist in the source.' ),
					array( 'q' => 'What file format will I get?', 'a' => 'MP4, for the large majority of supported video Pins.' ),
					array( 'q' => 'Can I download videos with sound?', 'a' => 'Yes, as long as the original Pin has an active audio track. If the source video is silent, the download will be silent too.' ),
					array( 'q' => 'Can I download a private Pin?', 'a' => 'No. Private and restricted Pins are intentionally excluded — this isn\'t a limitation we plan to remove.' ),
					array( 'q' => 'Can I download a pin.it link?', 'a' => 'Yes, as long as it resolves to a valid, public Pin.' ),
					array( 'q' => 'Can I reuse a downloaded video?', 'a' => 'Not automatically. Downloading gives you a file, not usage rights. Check with the creator or review Pinterest\'s copyright policy before reusing content commercially.' ),
					array( 'q' => 'Is it safe?', 'a' => 'Yes — we never ask for your Pinterest password, and we don\'t require an account to process a public Pin URL.' ),
				);

				foreach ( $faqs as $i => $faq ) :
					$btn_id   = 'pd-faq-btn-' . ( $i + 1 );
					$panel_id = 'pd-faq-panel-' . ( $i + 1 );
				?>
				<div class="pd-faq-item">
					<button type="button" class="pd-faq-toggle" id="<?php echo esc_attr( $btn_id ); ?>" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>">
						<span class="pd-faq-question"><?php echo esc_html( $faq['q'] ); ?></span>
						<span class="pd-faq-icon" aria-hidden="true">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
								<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
							</svg>
						</span>
					</button>
					<div class="pd-faq-answer" id="<?php echo esc_attr( $panel_id ); ?>" role="region" aria-labelledby="<?php echo esc_attr( $btn_id ); ?>">
						<p><?php echo esc_html( $faq['a'] ); ?></p>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</section>

		<!-- ── About PinDownloady & Feedback ──────────────────────── -->
		<section>
			<div class="pd-prose">
				<h2 class="pd-section-title">About This Tool</h2>
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
				<div id="pd-feedback-actions">
					<button type="button" class="pd-feedback-btn" onclick="document.getElementById('pd-feedback-actions').style.display='none';document.getElementById('pd-feedback-msg').style.display='block';">
						&#128077; Yes
					</button>
					<button type="button" class="pd-feedback-btn" onclick="document.getElementById('pd-feedback-actions').style.display='none';document.getElementById('pd-feedback-msg').style.display='block';">
						&#128078; No
					</button>
				</div>
				<div id="pd-feedback-msg" style="display:none; color:#16a34a; font-weight:700; font-size:14px; margin-top:8px;">
					Thank you for your feedback! It helps us improve.
				</div>
			</div>
		</section>

	</main>
	<?php endif; ?>

</div><!-- .pd-app -->

<!-- Accordion Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
	var faqToggles = document.querySelectorAll('.pd-faq-toggle');
	faqToggles.forEach(function(btn) {
		btn.addEventListener('click', function() {
			var item = btn.closest('.pd-faq-item');
			var isOpen = item.classList.contains('is-open');
			document.querySelectorAll('.pd-faq-item').forEach(function(i) { i.classList.remove('is-open'); });
			if (!isOpen) { item.classList.add('is-open'); }
		});
	});
});
</script>
