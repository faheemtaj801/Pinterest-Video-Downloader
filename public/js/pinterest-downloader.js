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
  const resultThumbnail    = wrapper.querySelector( '#pd-result-thumbnail' );
  const resultDuration     = wrapper.querySelector( '#pd-result-duration' );
  const resultTitle        = wrapper.querySelector( '#pd-result-title' );
  const btnMp4             = wrapper.querySelector( '#pd-btn-mp4' );
  const btnHd              = wrapper.querySelector( '#pd-btn-hd' );
  const btnImg             = wrapper.querySelector( '#pd-btn-img' );
  const mp4Title           = wrapper.querySelector( '#pd-mp4-title' );
  const imgTitle           = wrapper.querySelector( '#pd-img-title' );

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

    // 2. Duration Tag
    if ( resultDuration ) {
      if ( isVideo && data.duration ) {
        resultDuration.textContent = data.duration;
        resultDuration.hidden = false;
      } else {
        resultDuration.hidden = true;
      }
    }

    // 3. Title
    if ( resultTitle ) {
      resultTitle.textContent = data.title || ( isVideo ? 'Pinterest Video' : ( isGif ? 'Pinterest GIF' : 'Pinterest Image' ) );
    }

    // 4. Filename base
    const cleanTitle = data.title ? data.title.replace( /[^a-z0-9_-]/gi, '_' ).toLowerCase().substring( 0, 40 ) : 'pinterest-download';

    // 5. Format Buttons Configuration
    if ( isVideo ) {
      // Find 720p HD variant if present, or use primary video URL
      let hdUrl = data.media_url;
      if ( Array.isArray( data.variants ) && data.variants.length > 0 ) {
        const hdVariant = data.variants.find( function ( v ) {
          return v.quality === '720p' || ( v.label && v.label.includes( '720' ) );
        } );
        if ( hdVariant && hdVariant.url ) {
          hdUrl = hdVariant.url;
        }
      }

      // Configure MP4 button
      if ( btnMp4 ) {
        btnMp4.style.display = 'flex';
        btnMp4.href = buildStreamerUrl( data.media_url, cleanTitle + '.mp4' );
        btnMp4.setAttribute( 'download', cleanTitle + '.mp4' );
      }

      // Configure HD button
      if ( btnHd ) {
        btnHd.style.display = 'flex';
        btnHd.href = buildStreamerUrl( hdUrl, cleanTitle + '-hd.mp4' );
        btnHd.setAttribute( 'download', cleanTitle + '-hd.mp4' );
      }

      // Configure Cover Image button
      if ( btnImg ) {
        btnImg.style.display = 'flex';
        if ( imgTitle ) {
          imgTitle.textContent = 'Download Cover Image';
        }
        const imgUrl = data.thumbnail_url || data.media_url;
        btnImg.href = buildStreamerUrl( imgUrl, cleanTitle + '-cover.jpg' );
        btnImg.setAttribute( 'download', cleanTitle + '-cover.jpg' );
      }

    } else {
      // Image or GIF mode
      if ( btnMp4 ) {
        btnMp4.style.display = 'none';
      }
      if ( btnHd ) {
        btnHd.style.display = 'none';
      }
      if ( btnImg ) {
        btnImg.style.display = 'flex';
        if ( imgTitle ) {
          imgTitle.textContent = isGif ? 'Download Animated GIF' : 'Download Original Image';
        }
        const ext = isGif ? '.gif' : '.jpg';
        btnImg.href = buildStreamerUrl( data.media_url, cleanTitle + ext );
        btnImg.setAttribute( 'download', cleanTitle + ext );
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
