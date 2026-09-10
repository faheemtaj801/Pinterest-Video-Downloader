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
    font-size: clamp(26px, 5vw, 42px) !important;
    font-weight: 800 !important;
    line-height: 1.15 !important;
    letter-spacing: -0.025em !important;
    margin: 0 0 16px 0 !important;
    text-align: center !important;
  }

  .pd-app .pd-hero__subtitle {
    color: rgba(255, 255, 255, 0.88) !important;
    font-family: 'Inter', sans-serif !important;
    font-size: clamp(14px, 2.2vw, 17px) !important;
    font-weight: 400 !important;
    line-height: 1.65 !important;
    max-width: 600px !important;
    margin: 0 auto 28px auto !important;
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

  /* Mobile button hidden by default on desktop */
  .pd-app .pd-mobile-download-btn {
    display: none !important;
  }

  /* ── MOBILE RESPONSIVE (TikSav style) ─────────────────────────── */
  @media (max-width: 640px) {

    /* Hero: tighter padding on mobile */
    .pd-app .pd-hero {
      padding: 36px 20px 32px !important;
    }

    /* Input container full width */
    .pd-app .pd-input-container {
      max-width: 100% !important;
    }

    /* Input icon hidden on mobile to save space */
    .pd-app .pd-input-icon {
      display: none !important;
    }

    /* Input field smaller text */
    .pd-app .pd-input-field {
      font-size: 15px !important;
      padding: 12px 8px !important;
    }

    /* Paste button smaller */
    .pd-app .pd-paste-btn {
      padding: 11px 12px !important;
      font-size: 13px !important;
    }

    /* HIDE desktop Download button inside input row */
    .pd-app .pd-input-box .pd-btn-primary {
      display: none !important;
    }

    /* SHOW full-width Download button below input (TikSav style) */
    .pd-app .pd-mobile-download-btn {
      display: flex !important;
      width: 100% !important;
      justify-content: center !important;
      align-items: center !important;
      gap: 10px !important;
      background: #2563eb !important;
      color: #ffffff !important;
      border: none !important;
      border-radius: 14px !important;
      padding: 16px !important;
      font-size: 17px !important;
      font-weight: 700 !important;
      cursor: pointer !important;
      margin-top: 10px !important;
      box-shadow: 0 4px 14px rgba(37,99,235,0.4) !important;
      font-family: 'Inter', sans-serif !important;
      transition: background 0.18s !important;
    }
    .pd-app .pd-mobile-download-btn:active {
      background: #1d4ed8 !important;
    }
  }

  /* ── Mobile Form Layout ────────────────────────────────────────── */
  @media (max-width: 600px) {
    .pd-app .pd-input-box {
      flex-wrap: wrap !important;
      gap: 10px !important;
      padding: 12px !important;
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

		<?php if ( 'tool' !== $layout ) : ?>
		<!-- ── Quick Navigation Pills Bar ──────────────────────────── -->
		<div class="pd-nav-pills">
			<a href="#pd-how-it-works" class="pd-pill-link">&#8595; How It Works</a>
			<a href="#pd-device-guide" class="pd-pill-link">&#8595; Device Guide</a>
			<a href="#pd-troubleshooting" class="pd-pill-link">&#8595; Troubleshooting</a>
			<a href="#pd-faq" class="pd-pill-link">&#8595; FAQ</a>
			<a href="/pinterest-image-downloader/" class="pd-pill-link">&#128444; Images</a>
			<a href="/pinterest-gif-downloader/" class="pd-pill-link">&#127916; GIFs</a>
		</div>
		<?php endif; ?>

	</main>

</div><!-- .pd-app -->

