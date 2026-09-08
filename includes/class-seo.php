<?php
/**
 * Built-in Automated SEO & Schema Markup.
 *
 * Automatically handles meta titles, descriptions, OpenGraph tags,
 * and JSON-LD schema without requiring Yoast SEO or Rank Math.
 *
 * @package Pinterest_Downloader
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PD_SEO
 */
class PD_SEO {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Only run if Yoast or Rank Math is not already managing SEO.
		add_filter( 'pre_get_document_title', array( $this, 'filter_document_title' ), 20 );
		add_action( 'wp_head', array( $this, 'inject_meta_tags' ), 2 );
		add_action( 'wp_head', array( $this, 'inject_schema_markup' ), 3 );
	}

	/**
	 * Checks if the current post/page contains the plugin shortcode.
	 *
	 * @return bool|string False if not found, or shortcode type ('video', 'image', 'gif').
	 */
	private function get_page_tool_type() {
		if ( ! is_singular() ) {
			return false;
		}

		global $post;
		if ( ! $post || empty( $post->post_content ) ) {
			return false;
		}

		if ( has_shortcode( $post->post_content, 'pinterest_downloader' ) ) {
			if ( preg_match( '/\[pinterest_downloader[^\]]*type=["\'](image|gif)["\']/i', $post->post_content, $matches ) ) {
				return $matches[1];
			}
			return 'video';
		}

		return false;
	}

	/**
	 * Automatically sets a high-ranking SEO title if Yoast / Rank Math is not present.
	 *
	 * @param string $title Default document title.
	 * @return string Filtered title.
	 */
	public function filter_document_title( $title ) {
		if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
			return $title;
		}

		$type = $this->get_page_tool_type();
		if ( ! $type ) {
			return $title;
		}

		if ( 'image' === $type ) {
			return 'Pinterest Image Downloader — Download High-Res Photos | PinDownloady';
		} elseif ( 'gif' === $type ) {
			return 'Pinterest GIF Downloader — Save Animated GIFs & Loops | PinDownloady';
		}

		return 'Pinterest Video Downloader — Save HD Videos Without Watermark | PinDownloady';
	}

	/**
	 * Injects SEO Meta description and OpenGraph tags into wp_head.
	 */
	public function inject_meta_tags() {
		if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
			return;
		}

		$type = $this->get_page_tool_type();
		if ( ! $type ) {
			return;
		}

		$canonical_url = esc_url( get_permalink() );

		if ( 'image' === $type ) {
			$title = 'Pinterest Image Downloader — PinDownloady';
			$desc  = 'Download Pinterest photos, wallpapers, and images in full original HD quality. 100% free, fast, and no registration required.';
		} elseif ( 'gif' === $type ) {
			$title = 'Pinterest GIF Downloader — PinDownloady';
			$desc  = 'Download Pinterest animated GIFs and video loops in high resolution. Fast, free, and works on all devices.';
		} else {
			$title = 'Pinterest Video Downloader — PinDownloady';
			$desc  = 'Download Pinterest videos without watermark in 720p HD MP4. Fast, secure, and 100% free browser-based tool for all devices.';
		}

		echo "\n<!-- PinDownloady Automatic SEO Meta Tags -->\n";
		echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
		echo '<link rel="canonical" href="' . $canonical_url . '" />' . "\n";
		echo '<meta property="og:locale" content="en_US" />' . "\n";
		echo '<meta property="og:type" content="website" />' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '" />' . "\n";
		echo '<meta property="og:url" content="' . $canonical_url . '" />' . "\n";
		echo '<meta property="og:site_name" content="PinDownloady" />' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '" />' . "\n";
		echo "<!-- /PinDownloady Automatic SEO Meta Tags -->\n\n";
	}

	/**
	 * Injects Google JSON-LD Schema (WebApplication) for Rich Snippets.
	 */
	public function inject_schema_markup() {
		$type = $this->get_page_tool_type();
		if ( ! $type ) {
			return;
		}

		$page_url = esc_url( get_permalink() );

		$schema = array(
			'@context'            => 'https://schema.org',
			'@type'               => 'WebApplication',
			'name'                => 'PinDownloady',
			'url'                 => $page_url,
			'applicationCategory' => 'MultimediaApplication',
			'operatingSystem'     => 'All (iOS, Android, Windows, macOS, Linux)',
			'browserRequirements' => 'Requires JavaScript. Requires HTML5.',
			'offers'              => array(
				'@type'         => 'Offer',
				'price'         => '0',
				'priceCurrency' => 'USD',
			),
			'description'         => 'Free online Pinterest video and media downloader without watermarks in HD quality.',
		);

		echo "\n<!-- PinDownloady Structured Data (Schema.org) -->\n";
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
		echo "<!-- /PinDownloady Structured Data -->\n\n";
	}
}
