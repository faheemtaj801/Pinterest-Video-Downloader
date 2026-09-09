<?php
/**
 * Pinterest extraction provider implementation.
 *
 * Uses Pinterest's internal resource API (used by their own frontend) to
 * retrieve structured pin data reliably, since Pinterest is a fully JS-rendered
 * SPA and no useful data appears in the raw HTML response.
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
 * Extracts public media metadata from Pinterest URLs using a three-tier approach:
 *   1. Pinterest's internal resource API (/resource/PinResource/get/)
 *   2. oEmbed endpoint (title + thumbnail fallback)
 *   3. Raw HTML meta tag scan (og:image last resort)
 */
class PD_Pinterest_Provider implements PD_Provider_Interface {

	/**
	 * Provider ID.
	 *
	 * @var string
	 */
	const ID = 'pinterest_public';

	/**
	 * Standard desktop Chrome User-Agent string.
	 *
	 * @var string
	 */
	const UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';

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

		// 1. Resolve short URLs (pin.it) to canonical Pinterest URL.
		$resolved_url = $this->resolve_redirects( $url );
		if ( ! $resolved_url ) {
			$resolved_url = $url;
		}

		// 2. Extract Pin ID from the resolved URL.
		$pin_id = $this->extract_pin_id( $resolved_url );

		// 3. Try Pinterest internal resource API first (most reliable for videos).
		if ( $pin_id ) {
			$result = $this->fetch_via_resource_api( $pin_id, $resolved_url, $requested_type );
			if ( $result && $result->success ) {
				return $result;
			}
		}

		// 4. Fallback: Try oEmbed endpoint (gives title + thumbnail).
		$oembed_data = $this->fetch_via_oembed( $resolved_url );

		// 5. Fallback: Fetch raw HTML and scan meta tags.
		$html_data = $this->fetch_via_html( $resolved_url );

		// 6. Merge fallback data.
		$merged = $this->merge_fallback_data( $oembed_data, $html_data );

		if ( empty( $merged['video_url'] ) && empty( $merged['image_url'] ) ) {
			return PD_Provider_Result::error(
				'MEDIA_NOT_FOUND',
				esc_html__( "We couldn't find downloadable media on this Pin. It may be private, removed, or not a video/image pin.", 'pinterest-downloader' )
			);
		}

		// 7. Build result from merged fallback data.
		return $this->build_result( $merged, $resolved_url, $requested_type );
	}

	// ─── Core Extraction Methods ───────────────────────────────────────────────

	/**
	 * Attempts to fetch pin data using Pinterest's internal resource API.
	 *
	 * Pinterest's own frontend calls this endpoint to render pin pages.
	 * It returns structured JSON with video_list, images, title, etc.
	 *
	 * @param string $pin_id       Pin ID.
	 * @param string $source_url   Canonical pin URL.
	 * @param string $requested_type 'video' or 'image'.
	 * @return PD_Provider_Result|null
	 */
	private function fetch_via_resource_api( $pin_id, $source_url, $requested_type ) {
		$options = wp_json_encode(
			array(
				'options' => array(
					'id'            => $pin_id,
					'field_set_key' => 'detailed',
				),
				'context' => array(),
			)
		);

		$api_url = add_query_arg(
			array(
				'source_url'     => urlencode( $source_url ),
				'data'           => $options,
				'_'              => time(),
			),
			'https://www.pinterest.com/resource/PinResource/get/'
		);

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout'     => 18,
				'redirection' => 3,
				'headers'     => array(
					'User-Agent'      => self::UA,
					'Accept'          => 'application/json, text/javascript, */*; q=0.01',
					'Accept-Language' => 'en-US,en;q=0.9',
					'Referer'         => $source_url,
					'X-Requested-With' => 'XMLHttpRequest',
					'X-APP-VERSION'   => 'cb6ca04',
					'X-Pinterest-AppState' => 'active',
				),
				'sslverify'   => true,
			)
		);

		if ( is_wp_error( $response ) ) {
			return null;
		}

		$status = wp_remote_retrieve_response_code( $response );
		if ( 200 !== (int) $status ) {
			return null;
		}

		$body = wp_remote_retrieve_body( $response );
		if ( empty( $body ) ) {
			return null;
		}

		$json = json_decode( $body, true );
		if ( ! is_array( $json ) ) {
			return null;
		}

		// Navigate to the pin resource data.
		$pin_data = null;
		if ( ! empty( $json['resource_response']['data'] ) ) {
			$pin_data = $json['resource_response']['data'];
		} elseif ( ! empty( $json['data'] ) ) {
			$pin_data = $json['data'];
		}

		if ( ! is_array( $pin_data ) ) {
			return null;
		}

		// Extract from the pin resource data.
		$extracted = $this->parse_pin_resource_data( $pin_data );

		if ( empty( $extracted['video_url'] ) && empty( $extracted['image_url'] ) ) {
			return null;
		}

		return $this->build_result( $extracted, $source_url, $requested_type );
	}

	/**
	 * Parses Pinterest's internal pin resource data structure.
	 *
	 * @param array $pin_data Raw pin resource data array.
	 * @return array Extracted media data.
	 */
	private function parse_pin_resource_data( array $pin_data ) {
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

		// Title.
		if ( ! empty( $pin_data['title'] ) ) {
			$extracted['title'] = $pin_data['title'];
		} elseif ( ! empty( $pin_data['grid_title'] ) ) {
			$extracted['title'] = $pin_data['grid_title'];
		} elseif ( ! empty( $pin_data['description'] ) ) {
			$extracted['title'] = substr( $pin_data['description'], 0, 80 );
		}

		// Duration.
		if ( ! empty( $pin_data['duration'] ) ) {
			$extracted['duration'] = $this->format_duration( $pin_data['duration'] );
		}

		// Videos from video_list.
		if ( ! empty( $pin_data['videos']['video_list'] ) && is_array( $pin_data['videos']['video_list'] ) ) {
			$this->parse_video_list( $pin_data['videos']['video_list'], $extracted );
		} elseif ( ! empty( $pin_data['video_list'] ) && is_array( $pin_data['video_list'] ) ) {
			$this->parse_video_list( $pin_data['video_list'], $extracted );
		}

		// Story pin videos.
		if ( empty( $extracted['video_url'] ) && ! empty( $pin_data['story_pin_data']['pages'] ) ) {
			foreach ( $pin_data['story_pin_data']['pages'] as $page ) {
				if ( ! empty( $page['blocks'] ) ) {
					foreach ( $page['blocks'] as $block ) {
						if ( ! empty( $block['video']['video_list'] ) ) {
							$this->parse_video_list( $block['video']['video_list'], $extracted );
							if ( ! empty( $extracted['video_url'] ) ) break 2;
						}
					}
				}
			}
		}

		// Images.
		if ( ! empty( $pin_data['images'] ) && is_array( $pin_data['images'] ) ) {
			$images = $pin_data['images'];
			// Prefer original size.
			$size_priority = array( 'orig', '1200x', '736x', '564x', '474x', '236x' );
			foreach ( $size_priority as $size ) {
				if ( ! empty( $images[ $size ]['url'] ) ) {
					$extracted['image_url']     = $images[ $size ]['url'];
					$extracted['thumbnail_url'] = $images[ $size ]['url'];
					if ( empty( $extracted['width'] ) && ! empty( $images[ $size ]['width'] ) ) {
						$extracted['width'] = intval( $images[ $size ]['width'] );
					}
					if ( empty( $extracted['height'] ) && ! empty( $images[ $size ]['height'] ) ) {
						$extracted['height'] = intval( $images[ $size ]['height'] );
					}
					break;
				}
			}
		}

		// image_signature fallback.
		if ( empty( $extracted['image_url'] ) && ! empty( $pin_data['image_signature'] ) ) {
			$extracted['image_url'] = 'https://i.pinimg.com/originals/' . $pin_data['image_signature'];
		}

		// Upgrade thumbnail.
		if ( ! empty( $extracted['image_url'] ) ) {
			$extracted['image_url'] = $this->upgrade_pinimg_url( $extracted['image_url'] );
		}
		if ( ! empty( $extracted['thumbnail_url'] ) ) {
			$extracted['thumbnail_url'] = $this->upgrade_pinimg_url( $extracted['thumbnail_url'] );
		}

		// Clean title.
		if ( ! empty( $extracted['title'] ) ) {
			$extracted['title'] = $this->clean_title( $extracted['title'] );
		}

		return $extracted;
	}

	/**
	 * Parses a video_list array into extracted video data.
	 *
	 * @param array $video_list  The video_list array from Pinterest API.
	 * @param array $extracted   Reference to extracted data.
	 */
	private function parse_video_list( array $video_list, array &$extracted ) {
		$best_url  = '';
		$max_width = 0;
		$variants  = array();

		foreach ( $video_list as $quality_key => $stream ) {
			if ( empty( $stream['url'] ) ) {
				continue;
			}

			$raw_url = $stream['url'];
			$is_mp4  = ( false !== stripos( $raw_url, '.mp4' ) );
			$is_m3u8 = ( false !== stripos( $raw_url, '.m3u8' ) );

			// Skip HLS playlists if we can get MP4s.
			$final_url = $raw_url;
			if ( $is_m3u8 ) {
				// Convert HLS manifest to 720p MP4 equivalent path.
				$converted = preg_replace( '#/hls/#i', '/720p/', $raw_url );
				$converted = preg_replace( '#\.m3u8(\?.*)?$#i', '.mp4$1', $converted );
				if ( $converted ) {
					$final_url = $converted;
				}
			}

			$w = ! empty( $stream['width'] ) ? intval( $stream['width'] ) : 0;
			$h = ! empty( $stream['height'] ) ? intval( $stream['height'] ) : 0;

			// Derive quality label.
			if ( ! empty( $stream['quality'] ) ) {
				$label = esc_html( $stream['quality'] );
			} elseif ( $w ) {
				$label = "{$w}p MP4";
			} else {
				$label = strtoupper( (string) $quality_key ) . ' MP4';
			}

			$variants[] = array(
				'label'   => $label,
				'url'     => esc_url_raw( $final_url ),
				'width'   => $w,
				'height'  => $h,
				'quality' => $w ? "{$w}p" : 'HD',
				'format'  => 'mp4',
			);

			// Pick the highest resolution as the primary URL.
			if ( $w > $max_width || ( empty( $best_url ) && ( $is_mp4 || $is_m3u8 ) ) ) {
				if ( $is_mp4 || $is_m3u8 ) {
					$max_width          = $w;
					$best_url           = esc_url_raw( $final_url );
					$extracted['width'] = $w ?: ( $extracted['width'] ?? null );
					$extracted['height']= $h ?: ( $extracted['height'] ?? null );
				}
			}
		}

		if ( ! empty( $best_url ) ) {
			$extracted['video_url'] = $best_url;
			// Sort variants by resolution descending.
			usort( $variants, function( $a, $b ) {
				return $b['width'] - $a['width'];
			} );
			$extracted['variants'] = $variants;
		}
	}

	/**
	 * Attempts to fetch pin metadata via Pinterest's oEmbed endpoint.
	 *
	 * Returns title and thumbnail URL for images; no video data available via this endpoint.
	 *
	 * @param string $url Canonical pin URL.
	 * @return array Partial data array.
	 */
	private function fetch_via_oembed( $url ) {
		$data = array(
			'title'         => '',
			'image_url'     => '',
			'thumbnail_url' => '',
		);

		$oembed_url = add_query_arg(
			array(
				'url'    => urlencode( $url ),
				'format' => 'json',
			),
			'https://www.pinterest.com/oembed.json'
		);

		$response = wp_remote_get(
			$oembed_url,
			array(
				'timeout'   => 10,
				'headers'   => array(
					'User-Agent'      => self::UA,
					'Accept'          => 'application/json',
					'Accept-Language' => 'en-US,en;q=0.9',
				),
				'sslverify' => true,
			)
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return $data;
		}

		$body = wp_remote_retrieve_body( $response );
		$json = json_decode( $body, true );
		if ( ! is_array( $json ) ) {
			return $data;
		}

		if ( ! empty( $json['title'] ) ) {
			$data['title'] = $this->clean_title( $json['title'] );
		}
		if ( ! empty( $json['thumbnail_url'] ) ) {
			$data['image_url']     = $this->upgrade_pinimg_url( $json['thumbnail_url'] );
			$data['thumbnail_url'] = $data['image_url'];
		}

		return $data;
	}

	/**
	 * Fetches the raw HTML of a pin page and extracts meta tags.
	 *
	 * This is a last-resort fallback. Pinterest's SPA delivers very little useful data
	 * in the initial HTML, but og:image and og:title are sometimes present.
	 *
	 * @param string $url Canonical pin URL.
	 * @return array Partial data array.
	 */
	private function fetch_via_html( $url ) {
		$data = array(
			'title'         => '',
			'video_url'     => '',
			'image_url'     => '',
			'thumbnail_url' => '',
			'variants'      => array(),
		);

		$response = wp_safe_remote_get(
			$url,
			array(
				'timeout'     => 15,
				'redirection' => 5,
				'headers'     => array(
					'User-Agent'      => self::UA,
					'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
					'Accept-Language' => 'en-US,en;q=0.9',
					'Cache-Control'   => 'no-cache',
				),
				'sslverify'   => true,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $data;
		}

		$status = wp_remote_retrieve_response_code( $response );
		if ( $status < 200 || $status >= 400 ) {
			return $data;
		}

		$html = wp_remote_retrieve_body( $response );
		if ( empty( $html ) ) {
			return $data;
		}

		// Unescape slashes for regex scanning.
		$clean = str_replace( '\/', '/', $html );

		// og:image.
		if ( preg_match( '/<meta[^>]*property=["\']og:image["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $m ) ||
			 preg_match( '/<meta[^>]*content=["\']([^"\']+)["\'][^>]*property=["\']og:image["\']/i', $html, $m ) ) {
			$data['image_url']     = $this->upgrade_pinimg_url( $m[1] );
			$data['thumbnail_url'] = $data['image_url'];
		}

		// og:title / <title>.
		if ( empty( $data['title'] ) ) {
			if ( preg_match( '/<meta[^>]*property=["\']og:title["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $m ) ) {
				$data['title'] = $this->clean_title( html_entity_decode( $m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
			} elseif ( preg_match( '/<title[^>]*>(.*?)<\/title>/is', $html, $m ) ) {
				$data['title'] = $this->clean_title( html_entity_decode( $m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
			}
		}

		// Direct MP4 scan (v.pinimg.com videos).
		if ( preg_match_all( '#https?://v(?:\d+)?\.pinimg\.com/videos/[^\s"\'<>\\\\]+?\.mp4#i', $clean, $mp4_m ) ) {
			foreach ( $mp4_m[0] as $mp4_url ) {
				$mp4_url = esc_url_raw( trim( $mp4_url ) );
				if ( empty( $data['video_url'] ) ) {
					$data['video_url'] = $mp4_url;
				}
				$label = ( false !== stripos( $mp4_url, '720p' ) ) ? '720p HD MP4' : 'MP4 Video';
				$data['variants'][] = array(
					'label'   => $label,
					'url'     => $mp4_url,
					'quality' => ( false !== stripos( $mp4_url, '720p' ) ) ? '720p' : 'HD',
					'format'  => 'mp4',
				);
			}
		}

		// HLS m3u8 scan + conversion.
		if ( empty( $data['video_url'] ) && preg_match_all( '#https?://v(?:\d+)?\.pinimg\.com/videos/[^\s"\'<>\\\\]+?\.m3u8#i', $clean, $m3u8_m ) ) {
			foreach ( $m3u8_m[0] as $m3u8_url ) {
				$converted = preg_replace( '#/hls/#i', '/720p/', $m3u8_url );
				$converted = preg_replace( '#\.m3u8(\?.*)?$#i', '.mp4$1', $converted );
				$converted = esc_url_raw( trim( $converted ) );
				if ( empty( $data['video_url'] ) ) {
					$data['video_url'] = $converted;
				}
				$data['variants'][] = array(
					'label'   => '720p HD MP4',
					'url'     => $converted,
					'quality' => '720p',
					'format'  => 'mp4',
				);
			}
		}

		// pinimg.com image fallback.
		if ( empty( $data['image_url'] ) && preg_match_all( '#https?://i\.pinimg\.com/(?:originals|\d+x)/[^\s"\'<>\\\\]+?\.(?:jpg|jpeg|png|webp)#i', $clean, $img_m ) ) {
			$data['image_url']     = $this->upgrade_pinimg_url( esc_url_raw( $img_m[0][0] ) );
			$data['thumbnail_url'] = $data['image_url'];
		}

		return $data;
	}

	// ─── Helpers ───────────────────────────────────────────────────────────────

	/**
	 * Merges oEmbed and raw-HTML fallback data, giving priority to whichever has more info.
	 *
	 * @param array $oembed Partial data from oEmbed.
	 * @param array $html   Partial data from HTML.
	 * @return array Merged data.
	 */
	private function merge_fallback_data( array $oembed, array $html ) {
		$merged = array(
			'title'         => $oembed['title'] ?: $html['title'],
			'video_url'     => $html['video_url'] ?? '',
			'image_url'     => $oembed['image_url'] ?: ( $html['image_url'] ?? '' ),
			'thumbnail_url' => $oembed['thumbnail_url'] ?: ( $html['thumbnail_url'] ?? '' ),
			'width'         => null,
			'height'        => null,
			'duration'      => null,
			'variants'      => $html['variants'] ?? array(),
		);
		return $merged;
	}

	/**
	 * Builds a PD_Provider_Result from extracted media data.
	 *
	 * @param array  $data         Extracted media data.
	 * @param string $source_url   Canonical source URL.
	 * @param string $requested_type 'video' or 'image'.
	 * @return PD_Provider_Result
	 */
	private function build_result( array $data, $source_url, $requested_type ) {
		$is_video = ! empty( $data['video_url'] );

		if ( $is_video ) {
			$variants = ! empty( $data['variants'] ) ? $data['variants'] : array();

			// Ensure the primary video is in variants list.
			$primary_in_variants = false;
			foreach ( $variants as $v ) {
				if ( ! empty( $v['url'] ) && $v['url'] === $data['video_url'] ) {
					$primary_in_variants = true;
					break;
				}
			}
			if ( ! $primary_in_variants ) {
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

			// Add cover image variant.
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
					'thumbnail_url' => ! empty( $data['thumbnail_url'] ) ? $data['thumbnail_url'] : ( $data['image_url'] ?? '' ),
					'title'         => ! empty( $data['title'] ) ? $data['title'] : esc_html__( 'Pinterest Video', 'pinterest-downloader' ),
					'width'         => ! empty( $data['width'] ) ? $data['width'] : 720,
					'height'        => ! empty( $data['height'] ) ? $data['height'] : 1280,
					'duration'      => ! empty( $data['duration'] ) ? $data['duration'] : null,
					'source_url'    => $source_url,
					'variants'      => $variants,
				)
			);
		}

		// Video was requested but not found.
		if ( 'video' === $requested_type ) {
			return PD_Provider_Result::error(
				'VIDEO_NOT_FOUND',
				esc_html__( 'No downloadable video was found for this Pin. Please verify that this Pin actually contains a video.', 'pinterest-downloader' )
			);
		}

		// Image / GIF result.
		$img_url    = $data['image_url'];
		$img_ext    = strtolower( pathinfo( wp_parse_url( $img_url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
		$is_gif     = ( 'gif' === $img_ext ) || ( ! empty( $data['format'] ) && 'gif' === strtolower( $data['format'] ) );
		$media_type = $is_gif ? 'gif' : 'image';
		$format     = $is_gif ? 'gif' : ( $img_ext ?: 'jpg' );

		$image_variants = array(
			array(
				'label'   => $is_gif ? esc_html__( 'Download GIF', 'pinterest-downloader' ) : esc_html__( 'Original Resolution', 'pinterest-downloader' ),
				'quality' => 'HD',
				'url'     => $img_url,
				'format'  => $format,
			),
		);

		return PD_Provider_Result::success(
			array(
				'media_type'    => $media_type,
				'format'        => $format,
				'media_url'     => $img_url,
				'thumbnail_url' => $img_url,
				'title'         => ! empty( $data['title'] ) ? $data['title'] : ( $is_gif ? esc_html__( 'Pinterest GIF', 'pinterest-downloader' ) : esc_html__( 'Pinterest Image', 'pinterest-downloader' ) ),
				'width'         => ! empty( $data['width'] ) ? $data['width'] : null,
				'height'        => ! empty( $data['height'] ) ? $data['height'] : null,
				'duration'      => null,
				'source_url'    => $source_url,
				'variants'      => $image_variants,
			)
		);
	}

	/**
	 * Extracts the numeric Pin ID from a Pinterest URL.
	 *
	 * Handles URLs like:
	 *   - https://www.pinterest.com/pin/123456789/
	 *   - https://pinterest.com/pin/123456789012345678/
	 *   - https://www.pinterest.co.uk/pin/987654321/
	 *
	 * @param string $url Canonical Pinterest URL.
	 * @return string|null Pin ID or null if not found.
	 */
	private function extract_pin_id( $url ) {
		if ( preg_match( '#/pin/(\d+)/?(?:[?#]|$)#i', $url, $m ) ) {
			return $m[1];
		}
		return null;
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
				'redirection' => 5,
				'headers'     => array(
					'User-Agent' => self::UA,
				),
			)
		);

		if ( ! is_wp_error( $head ) ) {
			// WordPress follows redirects up to the 'redirection' limit;
			// the final URL is in the 'x-final-location' pseudo-header or we can
			// look at the response object's URL.
			$headers = wp_remote_retrieve_headers( $head );

			// If redirection was NOT followed (redirection=0), use Location header.
			if ( ! empty( $headers['location'] ) ) {
				$location = $headers['location'];
				// Ensure absolute URL.
				if ( 0 === strpos( $location, '/' ) ) {
					$location = 'https://www.pinterest.com' . $location;
				}
				return esc_url_raw( $location );
			}
		}

		// Follow redirect chain manually with GET.
		$get = wp_safe_remote_get(
			$url,
			array(
				'timeout'     => 10,
				'redirection' => 5,
				'headers'     => array(
					'User-Agent' => self::UA,
				),
				'sslverify'   => true,
			)
		);

		if ( ! is_wp_error( $get ) ) {
			// WordPress reports the final URL in the response headers as 'x-final-location'
			// or we can try to parse <link rel="canonical"> from the body.
			$body = wp_remote_retrieve_body( $get );
			if ( preg_match( '/<link[^>]*rel=["\']canonical["\'][^>]*href=["\']([^"\']+)["\']/i', $body, $m ) ) {
				return esc_url_raw( $m[1] );
			}
		}

		return $url;
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

		if ( false !== stripos( $url, 'pinimg.com' ) ) {
			$upgraded = preg_replace( '/\/(236x|474x|564x|736x|1200x|60x60)\//i', '/originals/', $url );
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
			$mins     = floor( $secs / 60 );
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
}
