/**
 * Pinterest Downloader — Public JavaScript (TikSav Design)
 *
 * Vanilla JS state machine connected to real WordPress backend extraction.
 * Zero external dependencies. Zero jQuery.
 *
 * States: idle → loading → result | error
 *
 * @package Pinterest_Downloader
 */

/* global pdVars */

( function () {
  'use strict';

  // ─── Safety guard ────────────────────────────────────────────────────────
  if ( typeof pdVars === 'undefined' ) {
    console.warn( '[PinterestDownloader] pdVars not defined. Plugin JS loaded outside shortcode context.' );
    return;
  }

  // ─── Constants ───────────────────────────────────────────────────────────

  /** Accepted Pinterest URL patterns */
  const PINTEREST_PATTERNS = [
    /^https?:\/\/(www\.)?pinterest\.(com|co\.uk|ca|au|fr|de|es|it|jp|br|ru|se|dk|nz|at|be|ch|cl|co|co\.kr|com\.au|com\.mx|fi|hu|ie|in|mx|nl|no|nz|ph|pt|ro|ru|se|sk|th|vn)\/.*$/i,
    /^https?:\/\/pin\.it\/.+$/i,
    /^https?:\/\/www\.pinterest\.com\/.+$/i,
  ];

  /** Error type mapping for friendly user messages */
  const ERROR_TYPES = {
    INVALID_URL: {
      title: 'Invalid Pinterest Link',
      desc: pdVars.i18n.invalidUrl || 'Please enter a valid Pinterest link (pinterest.com or pin.it).',
    },
    UNSUPPORTED_URL: {
      title: 'Unsupported Content',
      desc: 'This link format is not supported. Please paste a direct Pinterest Pin link.',
    },
    MEDIA_NOT_FOUND: {
      title: "We couldn't find downloadable media.",
      desc: 'The Pin may have been removed, deleted, or contains no downloadable media.',
    },
    VIDEO_NOT_FOUND: {
      title: 'No Video Found',
      desc: pdVars.i18n.videoNotFound || 'No downloadable video was found for this Pin. Please ensure this link contains a video.',
    },
    IMAGE_NOT_FOUND: {
      title: 'No Image Found',
      desc: pdVars.i18n.imageNotFound || 'No downloadable image was found for this Pin.',
    },
    GIF_NOT_FOUND: {
      title: 'No GIF Found',
      desc: pdVars.i18n.gifNotFound || 'No downloadable GIF was found for this Pin.',
    },
    PRIVATE_CONTENT: {
      title: 'This Pin is Private',
      desc: pdVars.i18n.privateContent || 'Private or secret boards cannot be accessed. Please try a public Pin.',
    },
    TIMEOUT: {
      title: 'Connection Timed Out',
      desc: pdVars.i18n.timeout || 'Processing is taking longer than expected. Please try again in a moment.',
    },
    RATE_LIMITED: {
      title: 'Too Many Requests',
      desc: pdVars.i18n.rateLimited || 'Too many requests. Please wait a moment before trying again.',
    },
    PROVIDER_ERROR: {
      title: "Couldn't Process Link",
      desc: pdVars.i18n.serverError || 'Something went wrong while processing this link. Please try again.',
    },
    UNKNOWN_ERROR: {
      title: "We couldn't process this link.",
      desc: 'Please check the Pinterest URL and try again.',
    },
  };

  // ─── State ────────────────────────────────────────────────────────────────

  /**
   * Current UI state.
   * @type {'idle'|'loading'|'result'|'error'}
   */
  let currentState = 'idle';

  /** Active AbortController for fetch requests */
  let activeAbortController = null;

  // ─── Element References ───────────────────────────────────────────────────

  const wrapper = document.querySelector( '.pd-app, .pd-wrapper' );

  if ( ! wrapper ) return; // Not on a page with the shortcode.

  const form             = wrapper.querySelector( '#pd-download-form' );
  const urlInputDesktop  = wrapper.querySelector( '#pd-url-input' );
  const urlInputMobile   = wrapper.querySelector( '#pd-url-input-mobile' );
  const downloadBtnDesk  = wrapper.querySelector( '#pd-download-btn' );
  const downloadBtnMob   = wrapper.querySelector( '#pd-mobile-download-btn' );
  const pasteBtnDesk     = wrapper.querySelector( '#pd-paste-btn' );
  const pasteBtnMob      = wrapper.querySelector( '#pd-paste-btn-mobile' );
  const inputError       = wrapper.querySelector( '#pd-input-error' );
  const errorText        = wrapper.querySelector( '#pd-error-text' );

  // State containers (input is NOT in this list — it's always visible)
  const stateLoading = wrapper.querySelector( '#pd-state-loading' );
  const stateResult  = wrapper.querySelector( '#pd-state-result' );
  const stateError   = wrapper.querySelector( '#pd-state-error' );

  // Input form elements (always in DOM, never hidden)
  const inputSection = wrapper.querySelector( '#pd-input-section' );

  // Result card elements (TikSav Style)
  const resultThumbnail     = wrapper.querySelector( '#pd-result-thumbnail' );
  const resultDuration      = wrapper.querySelector( '#pd-result-duration' );
  const resultDurationBadge = wrapper.querySelector( '#pd-result-duration-badge' );
  const resultDurationText  = wrapper.querySelector( '#pd-result-duration-text' );
  const resultTitle         = wrapper.querySelector( '#pd-result-title' );
  const btnMp4              = wrapper.querySelector( '#pd-btn-mp4' );
  const btnHd               = wrapper.querySelector( '#pd-btn-hd' );
  const btnImg              = wrapper.querySelector( '#pd-btn-img' );
  const mp4Title            = wrapper.querySelector( '#pd-mp4-title' );
  const hdTitle             = wrapper.querySelector( '#pd-hd-title' );
  const imgTitle            = wrapper.querySelector( '#pd-img-title' );
  const mp4Subtitle         = wrapper.querySelector( '#pd-mp4-subtitle' );
  const hdSubtitle          = wrapper.querySelector( '#pd-hd-subtitle' );
  const imgSubtitle         = wrapper.querySelector( '#pd-img-subtitle' );
  const resetBtnText        = wrapper.querySelector( '#pd-reset-btn-text' );

  // Error card elements
  const errorTitle = wrapper.querySelector( '#pd-error-title' );
  const errorDesc  = wrapper.querySelector( '#pd-error-desc' );

  // Detect mode from shortcode data attribute
  const downloaderType = wrapper.dataset.pdType || pdVars.type || 'video';

  // ─── Helper: Get Current Input Value ──────────────────────────────────────

  function getInputValue() {
    if ( urlInputDesktop && urlInputDesktop.value.trim() ) {
      return urlInputDesktop.value.trim();
    }
    if ( urlInputMobile && urlInputMobile.value.trim() ) {
      return urlInputMobile.value.trim();
    }
    return '';
  }

  function setInputValue( val ) {
    if ( urlInputDesktop ) urlInputDesktop.value = val;
    if ( urlInputMobile ) urlInputMobile.value = val;
    syncButtonState();
  }

  // ─── State Machine ────────────────────────────────────────────────────────

  /**
   * Transitions the UI to the given state.
   * Input box is ALWAYS visible — only loading/result/error toggle.
   *
   * @param {'idle'|'loading'|'result'|'error'} state
   */
  function setState( state ) {
    currentState = state;

    const isLoading = ( 'loading' === state );

    // Toggle state panels (input stays visible always)
    [ stateLoading, stateResult, stateError ].forEach( function ( el ) {
      if ( ! el ) return;
      el.classList.remove( 'is-active' );
      el.setAttribute( 'aria-hidden', 'true' );
    } );

    const map = {
      loading: stateLoading,
      result:  stateResult,
      error:   stateError,
    };

    const target = map[ state ];
    if ( target ) {
      target.classList.add( 'is-active' );
      target.removeAttribute( 'aria-hidden' );
    }

    // Disable / enable form inputs during loading
    const urlInput = wrapper.querySelector( '#pd-url-input' );
    const dlBtn    = wrapper.querySelector( '#pd-download-btn' );
    const pasteBtn = wrapper.querySelector( '#pd-paste-btn' );

    if ( urlInput ) urlInput.disabled  = isLoading;
    if ( dlBtn )    dlBtn.disabled     = isLoading || ! getInputValue();
    if ( pasteBtn ) pasteBtn.disabled  = isLoading;

    // On idle: clear old result/error, clear input
    if ( 'idle' === state ) {
      clearInput();
    }

    // Scroll result into view when it appears
    if ( 'result' === state && stateResult ) {
      setTimeout( function () {
        stateResult.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
      }, 120 );
    }

    // Scroll error into view when it appears
    if ( 'error' === state && stateError ) {
      setTimeout( function () {
        stateError.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
      }, 80 );
    }
  }

  // ─── Validation ───────────────────────────────────────────────────────────

  function isPinterestUrl( url ) {
    if ( ! url || typeof url !== 'string' ) return false;
    const trimmed = url.trim();
    return PINTEREST_PATTERNS.some( function ( pattern ) {
      return pattern.test( trimmed );
    } );
  }

  function showInputError( message ) {
    if ( ! inputError || ! errorText ) return;
    errorText.textContent = message;
    inputError.hidden = false;
    urlInputDesktop && urlInputDesktop.setAttribute( 'aria-invalid', 'true' );
    urlInputMobile && urlInputMobile.setAttribute( 'aria-invalid', 'true' );
  }

  function hideInputError() {
    if ( ! inputError ) return;
    inputError.hidden = true;
    urlInputDesktop && urlInputDesktop.removeAttribute( 'aria-invalid' );
    urlInputMobile && urlInputMobile.removeAttribute( 'aria-invalid' );
  }

  // ─── Input Helpers ────────────────────────────────────────────────────────

  function syncButtonState() {
    const val      = getInputValue();
    const hasValue = val.length > 0;
    const loading  = ( 'loading' === currentState );

    const dlBtn = wrapper.querySelector( '#pd-download-btn' );
    if ( dlBtn ) {
      dlBtn.disabled = loading || ! hasValue;
      dlBtn.setAttribute( 'aria-disabled', ( loading || ! hasValue ) ? 'true' : 'false' );
    }
  }

  function clearInput() {
    if ( urlInputDesktop ) urlInputDesktop.value = '';
    if ( urlInputMobile ) urlInputMobile.value = '';
    hideInputError();
    syncButtonState();
  }

  // ─── Secure Download URL Builder ─────────────────────────────────────────

  function buildStreamerUrl( mediaUrl, filename ) {
    if ( ! mediaUrl ) return '#';
    if ( mediaUrl.includes( 'pinimg.com' ) || mediaUrl.includes( 'pinterest.com' ) ) {
      return pdVars.ajaxUrl +
        '?action=pd_file_download' +
        '&nonce=' + encodeURIComponent( pdVars.nonce ) +
        '&media_url=' + encodeURIComponent( mediaUrl ) +
        '&filename=' + encodeURIComponent( filename );
    }
    return mediaUrl;
  }

  // ─── Duration Formatter (Fix 4) ───────────────────────────────────────────

  /**
   * Formats raw duration (seconds, ms, ISO string, or formatted string) into M:SS (e.g. 0:45, 1:23).
   *
   * @param {string|number} raw
   * @return {string} Formatted duration string e.g. "0:45"
   */
  function formatDuration( raw ) {
    if ( ! raw ) return '';

    // Handle ISO 8601 string (e.g. PT45S, PT1M15S, PT0M32S)
    if ( typeof raw === 'string' && /^PT/i.test( raw ) ) {
      const m = raw.match( /PT(?:(\d+)M)?(?:(\d+(?:\.\d+)?)S)?/i );
      if ( m ) {
        const mins = parseInt( m[1] || '0', 10 );
        const secs = Math.round( parseFloat( m[2] || '0' ) );
        return mins + ':' + ( secs < 10 ? '0' : '' ) + secs;
      }
    }

    // Handle string format like "00:45" or "0:45" or "01:23"
    if ( typeof raw === 'string' && raw.includes( ':' ) ) {
      const parts = raw.split( ':' );
      if ( parts.length === 2 ) {
        const mins = parseInt( parts[0], 10 );
        const secs = parseInt( parts[1], 10 );
        return ( isNaN( mins ) ? 0 : mins ) + ':' + ( secs < 10 ? '0' : '' ) + ( isNaN( secs ) ? '00' : secs );
      }
      return raw.trim();
    }

    // Handle numeric values (seconds or milliseconds)
    const num = parseFloat( raw );
    if ( ! isNaN( num ) && num > 0 ) {
      // If greater than 1000, value is almost certainly in milliseconds
      const totalSecs = num > 1000 ? Math.round( num / 1000 ) : Math.round( num );
      const mins      = Math.floor( totalSecs / 60 );
      const secs      = totalSecs % 60;
      return mins + ':' + ( secs < 10 ? '0' : '' ) + secs;
    }

    return String( raw );
  }

  // ─── Result Population (TikSav Signature Card) ────────────────────────────

  /**
   * Populates the result card with real extracted media data.
   *
   * @param {Object} data  Media data object returned from backend.
   */
  function populateResult( data ) {
    const isVideo = ( 'video' === data.media_type );
    const isGif   = ( 'gif' === data.media_type );
    const format  = ( data.format || ( isVideo ? 'mp4' : ( isGif ? 'gif' : 'jpg' ) ) ).toUpperCase();

    // 1. Thumbnail Preview
    if ( resultThumbnail ) {
      resultThumbnail.src = data.thumbnail_url || data.media_url || '';
      resultThumbnail.alt = data.title || 'Pinterest media preview';
    }

    // 2. Video Duration (Fix 4: M:SS format on thumbnail badge and metadata badge)
    const rawDuration = data.duration || data.duration_ms || data.duration_seconds || data.video_duration || null;
    let formattedDuration = formatDuration( rawDuration );

    if ( isVideo ) {
      if ( formattedDuration ) {
        if ( resultDuration ) {
          resultDuration.textContent = formattedDuration;
          resultDuration.hidden = false;
        }
        if ( resultDurationBadge && resultDurationText ) {
          resultDurationText.textContent = 'Duration: ' + formattedDuration;
          resultDurationBadge.hidden = false;
        }
      } else {
        // Duration not in API response: attempt to probe duration from HTML5 video metadata
        if ( resultDuration ) resultDuration.hidden = true;
        if ( resultDurationBadge ) resultDurationBadge.hidden = true;

        if ( data.media_url && typeof Audio !== 'undefined' ) {
          try {
            const probeVideo = document.createElement( 'video' );
            probeVideo.preload = 'metadata';
            probeVideo.src = data.media_url;
            probeVideo.onloadedmetadata = function () {
              if ( probeVideo.duration && ! isNaN( probeVideo.duration ) && probeVideo.duration > 0 ) {
                const detected = formatDuration( probeVideo.duration );
                if ( resultDuration ) {
                  resultDuration.textContent = detected;
                  resultDuration.hidden = false;
                }
                if ( resultDurationBadge && resultDurationText ) {
                  resultDurationText.textContent = 'Duration: ' + detected;
                  resultDurationBadge.hidden = false;
                }
              }
            };
          } catch ( e ) {
            // Silently ignore probing errors
          }
        }
      }
    } else {
      // Non-video media (Images / GIFs) don't have video duration badges
      if ( resultDuration ) resultDuration.hidden = true;
      if ( resultDurationBadge ) resultDurationBadge.hidden = true;
    }

    // 3. Title
    if ( resultTitle ) {
      resultTitle.textContent = data.title || ( isVideo ? 'Pinterest Video' : ( isGif ? 'Pinterest GIF' : 'Pinterest Image' ) );
    }

    // 4. Filename base
    const cleanTitle = data.title ? data.title.replace( /[^a-z0-9_-]/gi, '_' ).toLowerCase().substring( 0, 40 ) : 'pinterest-download';

    // 5. HD Variant Extraction
    let hdUrl = data.media_url;
    if ( Array.isArray( data.variants ) && data.variants.length > 0 ) {
      const hdVariant = data.variants.find( function ( v ) {
        return v.quality === '720p' || ( v.label && v.label.includes( '720' ) ) || v.quality === 'orig' || v.quality === 'HD';
      } );
      if ( hdVariant && hdVariant.url ) {
        hdUrl = hdVariant.url;
      }
    }

    // 6. Format Buttons Configuration (Fix 3: Contextual button labels per page type)
    const isImagePage = ( 'image' === downloaderType );
    const isGifPage   = ( 'gif' === downloaderType );

    if ( isImagePage || ( ! isVideo && ! isGif && ! isGifPage ) ) {
      // ─── IMAGE DOWNLOADER PAGE ──────────────────────────────────────────────
      // Button 1: Download Image — Full resolution · Original quality
      if ( btnImg ) {
        btnImg.style.display = 'flex';
        if ( imgTitle ) imgTitle.textContent = 'Download Image';
        if ( imgSubtitle ) imgSubtitle.textContent = 'Full resolution · Original quality';
        btnImg.href = buildStreamerUrl( data.media_url, cleanTitle + '.jpg' );
        btnImg.setAttribute( 'download', cleanTitle + '.jpg' );
      }

      // Button 2: HD Version — Highest resolution available
      if ( btnHd ) {
        btnHd.style.display = 'flex';
        if ( hdTitle ) hdTitle.textContent = 'HD Version';
        if ( hdSubtitle ) hdSubtitle.textContent = 'Highest resolution available';
        btnHd.href = buildStreamerUrl( hdUrl || data.media_url, cleanTitle + '-hd.jpg' );
        btnHd.setAttribute( 'download', cleanTitle + '-hd.jpg' );
      }

      // Remove or hide the MP4 option (Images don't download as MP4)
      if ( btnMp4 ) {
        btnMp4.style.display = 'none';
      }

      if ( resetBtnText ) {
        resetBtnText.textContent = 'Download another image';
      }

    } else if ( isGifPage || isGif ) {
      // ─── GIF DOWNLOADER PAGE ────────────────────────────────────────────────
      const gifMediaUrl = ( isGif && data.media_url ) ? data.media_url : ( data.thumbnail_url || data.media_url );
      const loopMediaUrl = data.video_url || ( isVideo ? data.media_url : '' ) || data.media_url;

      // Button 1: Download GIF — Animated · Original format
      if ( btnImg ) {
        btnImg.style.display = 'flex';
        if ( imgTitle ) imgTitle.textContent = 'Download GIF';
        if ( imgSubtitle ) imgSubtitle.textContent = 'Animated · Original format';
        btnImg.href = buildStreamerUrl( gifMediaUrl, cleanTitle + '.gif' );
        btnImg.setAttribute( 'download', cleanTitle + '.gif' );
      }

      // Button 2: Download MP4 Loop — Smaller file · Smoother playback
      if ( btnMp4 ) {
        btnMp4.style.display = 'flex';
        if ( mp4Title ) mp4Title.textContent = 'Download MP4 Loop';
        if ( mp4Subtitle ) mp4Subtitle.textContent = 'Smaller file · Smoother playback';
        btnMp4.href = buildStreamerUrl( loopMediaUrl, cleanTitle + '-loop.mp4' );
        btnMp4.setAttribute( 'download', cleanTitle + '-loop.mp4' );
      }

      // Remove or hide Cover Image option (Not relevant for GIFs)
      if ( btnHd ) {
        btnHd.style.display = 'none';
      }

      if ( resetBtnText ) {
        resetBtnText.textContent = 'Download another GIF';
      }

    } else {
      // ─── VIDEO DOWNLOADER PAGE (Default) ────────────────────────────────────
      // Button 1: Download MP4 — No watermark · Best quality
      if ( btnMp4 ) {
        btnMp4.style.display = 'flex';
        if ( mp4Title ) mp4Title.textContent = 'Download MP4';
        if ( mp4Subtitle ) mp4Subtitle.textContent = 'No watermark · Best quality';
        btnMp4.href = buildStreamerUrl( data.media_url, cleanTitle + '.mp4' );
        btnMp4.setAttribute( 'download', cleanTitle + '.mp4' );
      }

      // Button 2: HD Version — Highest resolution available
      if ( btnHd ) {
        btnHd.style.display = 'flex';
        if ( hdTitle ) hdTitle.textContent = 'HD Version';
        if ( hdSubtitle ) hdSubtitle.textContent = 'Highest resolution available';
        btnHd.href = buildStreamerUrl( hdUrl, cleanTitle + '-hd.mp4' );
        btnHd.setAttribute( 'download', cleanTitle + '-hd.mp4' );
      }

      // Button 3: Download Cover Image — Full resolution · Original JPG
      if ( btnImg ) {
        btnImg.style.display = 'flex';
        if ( imgTitle ) imgTitle.textContent = 'Download Cover Image';
        if ( imgSubtitle ) imgSubtitle.textContent = 'Full resolution · Original JPG';
        const imgUrl = data.thumbnail_url || data.media_url;
        btnImg.href = buildStreamerUrl( imgUrl, cleanTitle + '-cover.jpg' );
        btnImg.setAttribute( 'download', cleanTitle + '-cover.jpg' );
      }

      if ( resetBtnText ) {
        resetBtnText.textContent = 'Download another video';
      }
    }
  }

  // ─── Error Population ─────────────────────────────────────────────────────

  function populateError( errorCode, customMessage ) {
    const code = ( errorCode || '' ).toUpperCase();
    const def  = ERROR_TYPES[ code ] || ERROR_TYPES.UNKNOWN_ERROR;

    if ( errorTitle ) {
      errorTitle.textContent = def.title;
    }
    if ( errorDesc ) {
      errorDesc.textContent = customMessage || def.desc;
    }
  }

  // ─── Backend Extraction Request ───────────────────────────────────────────

  function executeDownloadRequest( url ) {
    setState( 'loading' );

    // Cancel any previous in-flight request
    if ( activeAbortController ) {
      activeAbortController.abort();
    }
    activeAbortController = new AbortController();

    const formData = new URLSearchParams();
    formData.append( 'action', 'pd_download' );
    formData.append( 'nonce', pdVars.nonce );
    formData.append( 'url', url );
    formData.append( 'type', downloaderType );

    // 20-second client-side timeout
    const timeoutId = setTimeout( function () {
      if ( activeAbortController ) {
        activeAbortController.abort();
      }
    }, 20000 );

    fetch( pdVars.ajaxUrl, {
      method: 'POST',
      body: formData,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
      },
      signal: activeAbortController.signal,
    } )
      .then( function ( response ) {
        clearTimeout( timeoutId );
        if ( ! response.ok ) {
          throw new Error( 'HTTP_' + response.status );
        }
        return response.json();
      } )
      .then( function ( res ) {
        if ( res && res.success && res.data ) {
          populateResult( res.data );
          setState( 'result' );
        } else {
          const errCode = ( res && res.data && res.data.error_code ) ? res.data.error_code : 'PROVIDER_ERROR';
          const errMsg  = ( res && res.data && res.data.message ) ? res.data.message : null;
          populateError( errCode, errMsg );
          setState( 'error' );
        }
      } )
      .catch( function ( err ) {
        clearTimeout( timeoutId );
        if ( err.name === 'AbortError' ) {
          populateError( 'TIMEOUT', pdVars.i18n.timeout );
        } else {
          populateError( 'PROVIDER_ERROR', pdVars.i18n.serverError );
        }
        setState( 'error' );
      } );
  }

  // ─── Form Submit ──────────────────────────────────────────────────────────

  if ( form ) {
    form.addEventListener( 'submit', function ( e ) {
      e.preventDefault();

      const url = getInputValue();

      if ( ! url ) {
        showInputError( pdVars.i18n.emptyInput || 'Please paste a Pinterest link first.' );
        return;
      }

      if ( ! isPinterestUrl( url ) ) {
        showInputError( pdVars.i18n.invalidUrl || 'Please enter a valid Pinterest link.' );
        return;
      }

      hideInputError();
      executeDownloadRequest( url );
    } );
  }

  // ─── Input Events & Two-Way Sync ──────────────────────────────────────────

  [ urlInputDesktop, urlInputMobile ].forEach( function ( input ) {
    if ( ! input ) return;

    input.addEventListener( 'input', function () {
      const val = input.value;
      if ( input === urlInputDesktop && urlInputMobile ) {
        urlInputMobile.value = val;
      } else if ( input === urlInputMobile && urlInputDesktop ) {
        urlInputDesktop.value = val;
      }
      syncButtonState();
      if ( val.trim().length > 0 ) {
        hideInputError();
      }
    } );

    input.addEventListener( 'keydown', function ( e ) {
      if ( e.key === 'Enter' ) {
        e.preventDefault();
        form && form.dispatchEvent( new Event( 'submit', { cancelable: true } ) );
      }
    } );
  } );

  // ─── Clipboard Paste Buttons ──────────────────────────────────────────────

  function handlePaste() {
    if ( ! navigator.clipboard || ! navigator.clipboard.readText ) {
      urlInputDesktop && urlInputDesktop.focus();
      return;
    }

    navigator.clipboard.readText()
      .then( function ( text ) {
        const trimmed = ( text || '' ).trim();
        setInputValue( trimmed );
        urlInputDesktop && urlInputDesktop.focus();

        if ( trimmed && ! isPinterestUrl( trimmed ) ) {
          showInputError( pdVars.i18n.invalidUrl || 'Please enter a valid Pinterest link.' );
        } else {
          hideInputError();
        }
      } )
      .catch( function () {
        urlInputDesktop && urlInputDesktop.focus();
      } );
  }

  if ( pasteBtnDesk ) {
    pasteBtnDesk.addEventListener( 'click', handlePaste );
  }
  if ( pasteBtnMob ) {
    pasteBtnMob.addEventListener( 'click', handlePaste );
  }

  // ─── Try Again / Reset Buttons ────────────────────────────────────────────────────

  wrapper.addEventListener( 'click', function ( e ) {
    const btn = e.target.closest( '[data-pd-reset]' );
    if ( btn ) {
      if ( activeAbortController ) {
        activeAbortController.abort();
      }
      // Hide result/error panels only — input stays visible
      [ stateResult, stateError, stateLoading ].forEach( function ( el ) {
        if ( ! el ) return;
        el.classList.remove( 'is-active' );
        el.setAttribute( 'aria-hidden', 'true' );
      } );
      clearInput();
      currentState = 'idle';
    }
  } );

  // ─── FAQ Accordion (TikSav Style) ─────────────────────────────────────────

  // ── FAQ Accordion (exact TikSav: max-height animation, is-open class) ──
  const faqItems = document.querySelectorAll( '.pd-faq-item' );

  faqItems.forEach( function ( item ) {
    const toggle = item.querySelector( '.pd-faq-toggle' );
    if ( ! toggle ) return;

    toggle.addEventListener( 'click', function () {
      const isOpen = item.classList.contains( 'is-open' );

      // Close all
      faqItems.forEach( function ( other ) {
        other.classList.remove( 'is-open' );
        const t = other.querySelector( '.pd-faq-toggle' );
        if ( t ) t.setAttribute( 'aria-expanded', 'false' );
      } );

      // Open clicked
      if ( ! isOpen ) {
        item.classList.add( 'is-open' );
        toggle.setAttribute( 'aria-expanded', 'true' );
      }
    } );
  } );

  // ─── Initialise ─────────────────────────────────────────────────────────

  // Input is always visible — just sync button state on load
  syncButtonState();
  currentState = 'idle';

} )();
