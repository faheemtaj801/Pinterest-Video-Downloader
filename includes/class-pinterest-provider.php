<?php
/**
 * Pinterest extraction provider implementation.
 *
 * @package Pinterest_Downloader
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PD_Pinterest_Provider
 *
 * Extracts public media metadata directly from Pinterest URLs using
 * structured JSON-LD, OpenGraph tags, and public initial data blocks.
 */
class PD_Pinterest_Provider implements PD_Provider_Interface {

	/**
	 * Provider ID.
	 *
	 * @var string
	 */
	const ID = 'pinterest_public';

	/**
	 * Returns provider ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return self::ID;
	}

	/**
	 * Returns provider display name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'Pinterest Public Extractor';
	}

	/**
	 * Checks if URL is supported by this provider.
	 *
	 * @param string $url Target URL.
	 * @return bool
	 */
	public function supports( $url ) {
		if ( empty( $url ) || ! is_string( $url ) ) {
			return false;
		}

		$host = wp_parse_url( $url, PHP_URL_HOST );
		if ( ! $host ) {
			return false;
		}

		$host = strtolower( $host );
		return (
			'pin.it' === $host ||
			'www.pin.it' === $host ||
			'pinterest.com' === $host ||
			'www.pinterest.com' === $host ||
			preg_match( '/(^|\.)pinterest\.[a-z]{2,3}(\.[a-z]{2})?$/i', $host )
		);
	}

	/**
	 * Extracts media details from a Pinterest URL.
	 *
	 * @param string $url            Target URL.
	 * @param string $requested_type 'video' or 'image'.
	 * @return PD_Provider_Result
	 */
	public function extract( $url, $requested_type = 'video' ) {
		// 1. Resolve short URLs (e.g. pin.it) if necessary.
		$resolved_url = $this->resolve_redirects( $url );
		if ( ! $resolved_url ) {
			$resolved_url = $url;
		}

		// 2. Fetch page HTML safely.
		$response = wp_safe_remote_get(
			$resolved_url,
			array(
				'timeout'     => 15,
				'redirection' => 5,
				'headers'     => array(
					'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
					'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
					'Accept-Language' => 'en-US,en;q=0.9',
					'Cache-Control'   => 'no-cache',
				),
				'sslverify'   => true,
			)
		);

		// 3. Handle network / HTTP errors.
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			if ( false !== stripos( $error_message, 'timed out' ) || false !== stripos( $error_message, 'timeout' ) ) {
				return PD_Provider_Result::error(
					'TIMEOUT',
					esc_html__( 'Processing took longer than expected. Please try again in a moment.', 'pinterest-downloader' )
				);
			}

			return PD_Provider_Result::error(
				'PROVIDER_ERROR',
				esc_html__( 'Unable to connect to Pinterest. Please check the link and try again.', 'pinterest-downloader' )
			);
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		if ( 404 === $status_code ) {
			return PD_Provider_Result::error(
				'MEDIA_NOT_FOUND',
				esc_html__( 'This Pin was not found. It may have been deleted or the link is incorrect.', 'pinterest-downloader' )
			);
		}

		if ( 401 === $status_code || 403 === $status_code ) {
			return PD_Provider_Result::error(
				'PRIVATE_CONTENT',
				esc_html__( 'This Pin is private or requires login. Only public Pins can be processed.', 'pinterest-downloader' )
			);
		}

		if ( 429 === $status_code ) {
			return PD_Provider_Result::error(
				'RATE_LIMITED',
				esc_html__( 'Too many requests. Please wait a few seconds before trying again.', 'pinterest-downloader' )
			);
		}

		if ( $status_code < 200 || $status_code >= 400 ) {
			return PD_Provider_Result::error(
				'PROVIDER_ERROR',
				esc_html__( 'Pinterest returned an unexpected response code (' . intval( $status_code ) . ').', 'pinterest-downloader' )
			);
		}

		$html = wp_remote_retrieve_body( $response );
		if ( empty( $html ) ) {
			return PD_Provider_Result::error(
				'MEDIA_NOT_FOUND',
				esc_html__( 'No content received from this Pin link.', 'pinterest-downloader' )
			);
		}

		// 4. Extract data using multiple complementary strategies.
		$data = $this->parse_pinterest_html( $html, $resolved_url );

		if ( ! $data || ( empty( $data['video_url'] ) && empty( $data['image_url'] ) ) ) {
			return PD_Provider_Result::error(
				'MEDIA_NOT_FOUND',
				esc_html__( "We couldn't find downloadable media on this Pin. Please verify it is a valid public Pin.", 'pinterest-downloader' )
			);
		}

		// 5. Determine media type and construct normalized result.
		$is_video = ! empty( $data['video_url'] );

		// If video was found
		if ( $is_video ) {
			$variants = ! empty( $data['variants'] ) ? $data['variants'] : array();

			// Ensure primary MP4 is in variants
			$has_primary = false;
			foreach ( $variants as $v ) {
				if ( ! empty( $v['url'] ) && $v['url'] === $data['video_url'] ) {
					$has_primary = true;
					break;
				}
			}
			if ( ! $has_primary ) {
				array_unshift(
					$variants,
					array(
						'label'   => esc_html__( 'Download MP4 (720p HD)', 'pinterest-downloader' ),
						'quality' => '720p',
						'url'     => $data['video_url'],
						'width'   => ! empty( $data['width'] ) ? $data['width'] : 720,
						'height'  => ! empty( $data['height'] ) ? $data['height'] : 1280,
						'format'  => 'mp4',
					)
				);
			}

			// Add cover image to variants
			if ( ! empty( $data['image_url'] ) ) {
				$variants[] = array(
					'label'   => esc_html__( 'Cover Image (HD)', 'pinterest-downloader' ),
					'quality' => 'Image',
					'url'     => $data['image_url'],
					'width'   => null,
					'height'  => null,
					'format'  => 'jpg',
				);
			}

			return PD_Provider_Result::success(
				array(
					'media_type'    => 'video',
					'format'        => 'mp4',
					'media_url'     => $data['video_url'],
					'thumbnail_url' => ! empty( $data['thumbnail_url'] ) ? $data['thumbnail_url'] : $data['image_url'],
					'title'         => ! empty( $data['title'] ) ? $data['title'] : esc_html__( 'Pinterest Video', 'pinterest-downloader' ),
					'width'         => ! empty( $data['width'] ) ? $data['width'] : 720,
					'height'        => ! empty( $data['height'] ) ? $data['height'] : 1280,
					'duration'      => ! empty( $data['duration'] ) ? $data['duration'] : null,
					'source_url'    => $resolved_url,
					'variants'      => $variants,
				)
			);
		}

		// If user explicitly requested a video but no video stream was found, do not silently downgrade to image!
		if ( 'video' === $requested_type ) {
			return PD_Provider_Result::error(
				'VIDEO_NOT_FOUND',
				esc_html__( 'No downloadable video was found for this Pin. Please verify that this Pin actually contains a video.', 'pinterest-downloader' )
			);
		}

		// Detect if image is a true GIF.
		$img_url       = $data['image_url'];
		$img_ext       = strtolower( pathinfo( wp_parse_url( $img_url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
		$is_gif        = ( 'gif' === $img_ext ) || ( ! empty( $data['format'] ) && 'gif' === strtolower( $data['format'] ) );
		$media_type    = $is_gif ? 'gif' : 'image';
		$format        = $is_gif ? 'gif' : ( $img_ext ? $img_ext : 'jpg' );
		$default_title = $is_gif ? esc_html__( 'Pinterest GIF', 'pinterest-downloader' ) : esc_html__( 'Pinterest Image', 'pinterest-downloader' );

		$image_variants = array(
			array(
				'label'   => $is_gif ? esc_html__( 'Download GIF', 'pinterest-downloader' ) : esc_html__( 'Original Resolution', 'pinterest-downloader' ),
				'quality' => 'HD',
				'url'     => $img_url,
				'format'  => $format,
			),
		);

		// Image or GIF result.
		return PD_Provider_Result::success(
			array(
				'media_type'    => $media_type,
				'format'        => $format,
				'media_url'     => $img_url,
				'thumbnail_url' => $img_url,
				'title'         => ! empty( $data['title'] ) ? $data['title'] : $default_title,
				'width'         => ! empty( $data['width'] ) ? $data['width'] : null,
				'height'        => ! empty( $data['height'] ) ? $data['height'] : null,
				'duration'      => null,
				'source_url'    => $resolved_url,
				'variants'      => $image_variants,
			)
		);
	}

	/**
	 * Resolves short links (such as pin.it) to their canonical Pinterest URL.
	 *
	 * @param string $url Source URL.
	 * @return string|null Resolved URL or null on failure.
	 */
	private function resolve_redirects( $url ) {
		$host = wp_parse_url( $url, PHP_URL_HOST );
		if ( 'pin.it' !== $host && 'www.pin.it' !== $host ) {
			return $url;
		}

		$head = wp_safe_remote_head(
			$url,
			array(
				'timeout'     => 10,
				'redirection' => 0,
				'headers'     => array(
					'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
				),
			)
		);

		if ( ! is_wp_error( $head ) ) {
			$headers = wp_remote_retrieve_headers( $head );
			if ( ! empty( $headers['location'] ) ) {
				return esc_url_raw( $headers['location'] );
			}
		}

		return $url;
	}

	/**
	 * Parses HTML using JSON-LD, embedded __PWS_DATA__ scripts, and OpenGraph meta tags.
	 *
	 * @param string $html HTML string.
	 * @param string $url  Canonical source URL.
	 * @return array
	 */
	private function parse_pinterest_html( $html, $url ) {
		$extracted = array(
			'title'         => '',
			'video_url'     => '',
			'image_url'     => '',
			'thumbnail_url' => '',
			'width'         => null,
			'height'        => null,
			'duration'      => null,
			'variants'      => array(),
		);

		// Strategy 1: Parse __PWS_DATA__ or __INITIAL_DATA__ JSON script blocks (richest data).
		if ( preg_match( '/<script[^>]*id="__PWS_DATA__"[^>]*>(.*?)<\/script>/is', $html, $matches ) ||
			 preg_match( '/<script[^>]*data-relay-response="true"[^>]*>(.*?)<\/script>/is', $html, $matches ) ||
			 preg_match( '/window\.__INITIAL_DATA__\s*=\s*(\{.*?\});?\s*<\/script>/is', $html, $matches ) ) {

			$json_data = json_decode( trim( $matches[1] ), true );
			if ( is_array( $json_data ) ) {
				$this->extract_from_pws_data( $json_data, $extracted );
			}
		}

		// Strategy 2: Schema.org JSON-LD (<script type="application/ld+json">).
		if ( preg_match_all( '/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $ld_matches ) ) {
			foreach ( $ld_matches[1] as $ld_str ) {
				$ld_data = json_decode( trim( $ld_str ), true );
				if ( is_array( $ld_data ) ) {
					$this->extract_from_json_ld( $ld_data, $extracted );
				}
			}
		}

		// Strategy 3: OpenGraph & Twitter Meta Tags (reliable fallback).
		$this->extract_from_meta_tags( $html, $extracted );

		// Strategy 4: Deep Regex Scanner across raw & unescaped HTML (for modern Pinterest scripts & HLS conversion).
		$this->extract_from_raw_html( $html, $extracted );

		// Clean title if present.
		if ( ! empty( $extracted['title'] ) ) {
			$extracted['title'] = $this->clean_title( $extracted['title'] );
		}

		// Upgrade image URL to original full-resolution if it's a pinimg URL.
		if ( ! empty( $extracted['image_url'] ) ) {
			$extracted['image_url'] = $this->upgrade_pinimg_url( $extracted['image_url'] );
		}
		if ( ! empty( $extracted['thumbnail_url'] ) ) {
			$extracted['thumbnail_url'] = $this->upgrade_pinimg_url( $extracted['thumbnail_url'] );
		}

		return $extracted;
	}

	/**
	 * Deep regex scanner across the raw and unescaped HTML.
	 *
	 * Scans for direct MP4 and HLS m3u8 streams on v.pinimg.com and converts m3u8 to 720p MP4.
	 *
	 * @param string $html      Raw HTML.
	 * @param array  $extracted Reference to extracted array.
	 */
	private function extract_from_raw_html( $html, array &$extracted ) {
		// Clean and unescape slashes
		$clean = str_replace( '\/', '/', $html );

		// 1. Direct MP4 scan
		if ( preg_match_all( '#https?://(?:v\d?|v)\.pinimg\.com/videos/[^\s"\'<>\\]+?\.mp4#i', $clean, $mp4_matches ) ) {
			foreach ( $mp4_matches[0] as $url ) {
				$url = esc_url_raw( trim( $url ) );
				if ( empty( $extracted['video_url'] ) ) {
					$extracted['video_url'] = $url;
				}
				$label = ( false !== stripos( $url, '720p' ) ) ? '720p (HD MP4)' : 'MP4 Video';
				$extracted['variants'][] = array(
					'label'   => $label,
					'quality' => ( false !== stripos( $url, '720p' ) ) ? '720p' : 'HD',
					'url'     => $url,
					'format'  => 'mp4',
				);
			}
		}

		// 2. HLS m3u8 stream scan & conversion to 720p MP4
		if ( preg_match_all( '#https?://(?:v\d?|v)\.pinimg\.com/videos/[^\s"\'<>\\]+?\.m3u8#i', $clean, $m3u8_matches ) ) {
			foreach ( $m3u8_matches[0] as $m3u8_url ) {
				$m3u8_url = trim( $m3u8_url );
				$converted_720p = preg_replace( '#/hls/#i', '/720p/', $m3u8_url );
				$converted_720p = preg_replace( '#\.m3u8(\?.*)?$#i', '.mp4$1', $converted_720p );
				$converted_720p = esc_url_raw( $converted_720p );

				if ( empty( $extracted['video_url'] ) ) {
					$extracted['video_url'] = $converted_720p;
				}

				$extracted['variants'][] = array(
					'label'   => esc_html__( 'Download MP4 (720p HD)', 'pinterest-downloader' ),
					'quality' => '720p',
					'url'     => $converted_720p,
					'format'  => 'mp4',
				);
			}
		}

		// 3. Fallback image scan if image_url is still empty
		if ( empty( $extracted['image_url'] ) && preg_match_all( '#https?://i\.pinimg\.com/(?:originals|\d+x)/[^\s"\'<>\\]+?\.(?:jpg|jpeg|png|webp)#i', $clean, $img_matches ) ) {
			$first_img = esc_url_raw( $img_matches[0][0] );
			$extracted['image_url']     = $this->upgrade_pinimg_url( $first_img );
			$extracted['thumbnail_url'] = $extracted['image_url'];
		}
	}

	/**
	 * Extracts data recursively from Pinterest's internal state JSON.
	 *
	 * @param array $data      State data array.
	 * @param array $extracted Reference to extracted data array.
	 */
	private function extract_from_pws_data( array $data, array &$extracted ) {
		// Look for video_list in any node.
		$video_list = $this->find_nested_key( $data, 'video_list' );
		if ( is_array( $video_list ) ) {
			// Find best MP4 stream.
			$best_url  = '';
			$max_width = 0;
			$variants  = array();

			foreach ( $video_list as $quality_key => $stream ) {
				if ( ! empty( $stream['url'] ) ) {
					$raw_stream_url = $stream['url'];
					$is_mp4         = ( false !== stripos( $raw_stream_url, '.mp4' ) );
					$is_m3u8        = ( false !== stripos( $raw_stream_url, '.m3u8' ) );

					$final_url = $raw_stream_url;
					if ( $is_m3u8 ) {
						// Auto-convert HLS playlist to 720p MP4
						$final_url = preg_replace( '#/hls/#i', '/720p/', $raw_stream_url );
						$final_url = preg_replace( '#\.m3u8(\?.*)?$#i', '.mp4$1', $final_url );
					}

					$w = ! empty( $stream['width'] ) ? intval( $stream['width'] ) : ( $is_m3u8 ? 720 : 0 );
					$h = ! empty( $stream['height'] ) ? intval( $stream['height'] ) : ( $is_m3u8 ? 1280 : 0 );
					$label = ! empty( $stream['quality'] ) ? $stream['quality'] : ( $w ? "{$w}p" : $quality_key );

					$variants[] = array(
						'label'   => $label,
						'url'     => $final_url,
						'width'   => $w,
						'height'  => $h,
						'quality' => $w ? "{$w}p" : 'HD',
						'format'  => 'mp4',
					);

					if ( $w > $max_width || empty( $best_url ) ) {
						$max_width          = $w;
						$best_url           = $final_url;
						$extracted['width'] = $w;
						$extracted['height']= $h;
					}
				}
			}

			if ( $best_url ) {
				$extracted['video_url'] = $best_url;
				if ( ! empty( $variants ) ) {
					$extracted['variants'] = $variants;
				}
			}
		}

		// Look for images in pins.
		$images = $this->find_nested_key( $data, 'images' );
		if ( is_array( $images ) ) {
			if ( ! empty( $images['orig']['url'] ) ) {
				$extracted['image_url']     = $images['orig']['url'];
				$extracted['thumbnail_url'] = $images['orig']['url'];
				if ( empty( $extracted['width'] ) && ! empty( $images['orig']['width'] ) ) {
					$extracted['width'] = intval( $images['orig']['width'] );
				}
				if ( empty( $extracted['height'] ) && ! empty( $images['orig']['height'] ) ) {
					$extracted['height'] = intval( $images['orig']['height'] );
				}
			} elseif ( ! empty( $images['736x']['url'] ) && empty( $extracted['image_url'] ) ) {
				$extracted['image_url']     = $images['736x']['url'];
				$extracted['thumbnail_url'] = $images['736x']['url'];
			}
		}

		// Look for pin title/description.
		if ( empty( $extracted['title'] ) ) {
			$title = $this->find_nested_key( $data, 'title' );
			if ( is_string( $title ) && ! empty( $title ) ) {
				$extracted['title'] = $title;
			} else {
				$grid_title = $this->find_nested_key( $data, 'grid_title' );
				if ( is_string( $grid_title ) && ! empty( $grid_title ) ) {
					$extracted['title'] = $grid_title;
				}
			}
		}

		// Look for duration.
		if ( empty( $extracted['duration'] ) ) {
			$duration = $this->find_nested_key( $data, 'duration' );
			if ( $duration ) {
				$extracted['duration'] = $this->format_duration( $duration );
			}
		}
	}

	/**
	 * Extracts data from Schema.org JSON-LD data.
	 *
	 * @param array $ld        JSON-LD object.
	 * @param array $extracted Reference to extracted data array.
	 */
	private function extract_from_json_ld( array $ld, array &$extracted ) {
		// Handle Graph arrays.
		if ( isset( $ld['@graph'] ) && is_array( $ld['@graph'] ) ) {
			foreach ( $ld['@graph'] as $item ) {
				if ( is_array( $item ) ) {
					$this->extract_from_json_ld( $item, $extracted );
				}
			}
			return;
		}

		$type = isset( $ld['@type'] ) ? $ld['@type'] : '';

		// VideoObject.
		if ( 'VideoObject' === $type ) {
			if ( ! empty( $ld['contentUrl'] ) && empty( $extracted['video_url'] ) ) {
				$extracted['video_url'] = $ld['contentUrl'];
			}
			if ( ! empty( $ld['thumbnailUrl'] ) && empty( $extracted['thumbnail_url'] ) ) {
				$extracted['thumbnail_url'] = is_array( $ld['thumbnailUrl'] ) ? reset( $ld['thumbnailUrl'] ) : $ld['thumbnailUrl'];
			}
			if ( ! empty( $ld['name'] ) && empty( $extracted['title'] ) ) {
				$extracted['title'] = $ld['name'];
			}
			if ( ! empty( $ld['duration'] ) && empty( $extracted['duration'] ) ) {
				$extracted['duration'] = $this->format_duration( $ld['duration'] );
			}
		}

		// ImageObject or general pin item.
		if ( 'ImageObject' === $type || 'SocialMediaPosting' === $type || 'Article' === $type ) {
			if ( ! empty( $ld['contentUrl'] ) && empty( $extracted['image_url'] ) ) {
				$extracted['image_url'] = $ld['contentUrl'];
			}
			if ( ! empty( $ld['image'] ) && empty( $extracted['image_url'] ) ) {
				$extracted['image_url'] = is_array( $ld['image'] ) ? ( isset( $ld['image']['url'] ) ? $ld['image']['url'] : reset( $ld['image'] ) ) : $ld['image'];
			}
			if ( ! empty( $ld['headline'] ) && empty( $extracted['title'] ) ) {
				$extracted['title'] = $ld['headline'];
			}
			if ( ! empty( $ld['name'] ) && empty( $extracted['title'] ) ) {
				$extracted['title'] = $ld['name'];
			}
		}
	}

	/**
	 * Extracts data from OpenGraph, Twitter, and canonical HTML meta tags.
	 *
	 * @param string $html      HTML string.
	 * @param array  $extracted Reference to extracted data array.
	 */
	private function extract_from_meta_tags( $html, array &$extracted ) {
		// og:video or og:video:secure_url.
		if ( empty( $extracted['video_url'] ) ) {
			if ( preg_match( '/<meta[^>]*property=["\']og:video(?::secure_url)?["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $m ) ||
				 preg_match( '/<meta[^>]*content=["\']([^"\']+)["\'][^>]*property=["\']og:video(?::secure_url)?["\']/i', $html, $m ) ) {
				$extracted['video_url'] = $m[1];
			}
		}

		// og:image.
		if ( empty( $extracted['image_url'] ) ) {
			if ( preg_match( '/<meta[^>]*property=["\']og:image["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $m ) ||
				 preg_match( '/<meta[^>]*content=["\']([^"\']+)["\'][^>]*property=["\']og:image["\']/i', $html, $m ) ) {
				$extracted['image_url'] = $m[1];
			}
		}

		// Title: og:title or <title>.
		if ( empty( $extracted['title'] ) ) {
			if ( preg_match( '/<meta[^>]*property=["\']og:title["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $m ) ||
				 preg_match( '/<meta[^>]*name=["\']twitter:title["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $m ) ) {
				$extracted['title'] = $m[1];
			} elseif ( preg_match( '/<title[^>]*>(.*?)<\/title>/is', $html, $m ) ) {
				$extracted['title'] = $m[1];
			}
		}

		// Video direct HTML tag fallback.
		if ( empty( $extracted['video_url'] ) && preg_match( '/<video[^>]*src=["\']([^"\']+)["\']/i', $html, $m ) ) {
			$extracted['video_url'] = $m[1];
		}
	}

	/**
	 * Upgrades a Pinterest image thumbnail URL to its maximum resolution (originals).
	 *
	 * e.g. https://i.pinimg.com/236x/ab/cd/ef/... -> https://i.pinimg.com/originals/ab/cd/ef/...
	 *
	 * @param string $url Image URL.
	 * @return string
	 */
	private function upgrade_pinimg_url( $url ) {
		if ( empty( $url ) || ! is_string( $url ) ) {
			return $url;
		}

		// Replace standard thumbnail size segments with originals if hosted on pinimg.com.
		if ( false !== stripos( $url, 'pinimg.com' ) ) {
			$upgraded = preg_replace( '/\/(236x|474x|564x|736x|1200x)\//i', '/originals/', $url );
			if ( $upgraded ) {
				return $upgraded;
			}
		}

		return $url;
	}

	/**
	 * Formats ISO 8601 duration (e.g. PT15S) or numeric seconds into MM:SS.
	 *
	 * @param string|int|float $duration Raw duration.
	 * @return string Formatted string e.g. "00:15".
	 */
	private function format_duration( $duration ) {
		if ( empty( $duration ) ) {
			return '';
		}

		// ISO 8601 duration e.g. PT0M15S or PT15S or PT1M30S.
		if ( is_string( $duration ) && preg_match( '/PT(?:(\d+)M)?(?:(\d+(?:\.\d+)?)S)?/i', $duration, $m ) ) {
			$mins = ! empty( $m[1] ) ? intval( $m[1] ) : 0;
			$secs = ! empty( $m[2] ) ? intval( $m[2] ) : 0;
			return sprintf( '%02d:%02d', $mins, $secs );
		}

		// Numeric seconds or milliseconds.
		if ( is_numeric( $duration ) ) {
			$secs = floatval( $duration );
			if ( $secs > 1000 ) {
				// Likely milliseconds.
				$secs = round( $secs / 1000 );
			}
			$mins = floor( $secs / 60 );
			$rem_secs = $secs % 60;
			return sprintf( '%02d:%02d', $mins, $rem_secs );
		}

		return sanitize_text_field( (string) $duration );
	}

	/**
	 * Cleans Pinterest boilerplate from titles.
	 *
	 * @param string $title Raw title.
	 * @return string
	 */
	private function clean_title( $title ) {
		$title = html_entity_decode( $title, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$title = preg_replace( '/\s*[\-\|•]\s*Pinterest\s*$/i', '', $title );
		$title = preg_replace( '/^\s*Pinterest\s*[\-\|•]\s*/i', '', $title );
		return trim( $title );
	}

	/**
	 * Recursively searches a nested array for the first instance of a key.
	 *
	 * @param array  $array Array to search.
	 * @param string $key   Key to locate.
	 * @return mixed|null
	 */
	private function find_nested_key( array $array, $key ) {
		if ( array_key_exists( $key, $array ) ) {
			return $array[ $key ];
		}

		foreach ( $array as $val ) {
			if ( is_array( $val ) ) {
				$found = $this->find_nested_key( $val, $key );
				if ( null !== $found ) {
					return $found;
				}
			}
		}

		return null;
	}
}
