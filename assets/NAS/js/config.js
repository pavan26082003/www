// assets/NAS/js/config.js
// Central NAS frontend configuration for the CodeIgniter-mounted File Manager.
//
// Preferred values are injected by the NAS PHP views before this file loads:
//   window._basePath   = site_url('NAS')
//   window._apiBase    = site_url('NAS/api/v1')
//   window.NAS_ASSETS  = base_url('assets/NAS')
//
// Safe browser-derived fallbacks are provided so there are no hardcoded
// standalone-project or machine-specific paths.

(function () {
    'use strict';

    function trimTrailingSlash(value) {
        return String(value || '').replace(/\/+$/, '');
    }

    function deriveNasBaseFromLocation() {
        const path = window.location.pathname || '';
        const marker = '/NAS';
        const markerIndex = path.indexOf(marker);

        if (markerIndex >= 0) {
            return trimTrailingSlash(
                path.substring(0, markerIndex) + marker
            );
        }

        return '/NAS';
    }

    function deriveAssetsBaseFromCurrentScript() {
        const script = document.currentScript;

        if (script && script.src) {
            try {
                const url = new URL(script.src, window.location.origin);
                const suffix = '/js/config.js';

                if (url.pathname.endsWith(suffix)) {
                    return trimTrailingSlash(
                        url.pathname.substring(
                            0,
                            url.pathname.length - suffix.length
                        )
                    );
                }
            } catch (error) {
                console.warn(
                    'NAS config: unable to derive asset URL from current script.',
                    error
                );
            }
        }

        return '/assets/NAS';
    }

    // ---------------------------------------------------------------------
    // NAS base URL
    // ---------------------------------------------------------------------
    if (
        typeof window._basePath !== 'string' ||
        window._basePath.trim() === ''
    ) {
        window._basePath = deriveNasBaseFromLocation();
    }

    window._basePath = trimTrailingSlash(window._basePath);
    window.NAS_BASE = window._basePath;

    // ---------------------------------------------------------------------
    // API base URL
    // ---------------------------------------------------------------------
    if (
        typeof window._apiBase !== 'string' ||
        window._apiBase.trim() === ''
    ) {
        window._apiBase = window._basePath + '/api/v1';
    }

    window._apiBase = trimTrailingSlash(window._apiBase);
    window.API_BASE = window._apiBase;

    // ---------------------------------------------------------------------
    // Asset base URL
    // ---------------------------------------------------------------------
    if (
        typeof window.NAS_ASSETS !== 'string' ||
        window.NAS_ASSETS.trim() === ''
    ) {
        window.NAS_ASSETS = deriveAssetsBaseFromCurrentScript();
    }

    window.NAS_ASSETS = trimTrailingSlash(window.NAS_ASSETS);

    // ---------------------------------------------------------------------
    // Authenticated API / CSRF request helper
    // ---------------------------------------------------------------------
    // CodeIgniter redirects a failed CSRF request back to the dashboard in
    // production. fetch() follows that redirect and the caller then receives
    // HTML instead of JSON. Resolve the current token for every unsafe request
    // so JSON writes work with CSRF enabled as well as disabled.
    function readCookie(name) {
        const prefix = encodeURIComponent(name) + '=';
        const cookies = String(document.cookie || '').split(';');

        for (const entry of cookies) {
            const cookie = entry.trim();
            if (cookie.indexOf(prefix) === 0) {
                try {
                    return decodeURIComponent(cookie.substring(prefix.length));
                } catch (error) {
                    return cookie.substring(prefix.length);
                }
            }
        }

        return '';
    }

    window.getNasCsrfToken = function () {
        const configured = window.NAS_CSRF || {};
        const cookieName = configured.cookieName || 'rise_csrf_cookie';

        return {
            tokenName: configured.tokenName || 'rise_csrf_token',
            headerName: configured.headerName || 'X-CSRF-TOKEN',
            hash: readCookie(cookieName) || configured.hash || ''
        };
    };

    window.withNasCsrf = function (options) {
        const request = Object.assign({}, options || {});
        const method = String(request.method || 'GET').toUpperCase();
        const headers = new Headers(request.headers || {});

        request.credentials = request.credentials || 'include';
        headers.set('X-Requested-With', 'XMLHttpRequest');

        if (!['GET', 'HEAD', 'OPTIONS'].includes(method)) {
            const csrf = window.getNasCsrfToken();
            if (csrf.hash) {
                headers.set(csrf.headerName, csrf.hash);

                if (
                    request.body instanceof FormData
                    && !request.body.has(csrf.tokenName)
                ) {
                    request.body.append(csrf.tokenName, csrf.hash);
                }
            }
        }

        request.headers = headers;
        return request;
    };

    window.nasFetch = function (url, options) {
        return window.fetch(url, window.withNasCsrf(options));
    };

    // ---------------------------------------------------------------------
    // WebSocket URL
    // ---------------------------------------------------------------------
    // A CodeIgniter HTTP route is not a WebSocket server. Enable real-time
    // updates only when production explicitly injects a working WS endpoint.
    const configuredWsUrl =
        typeof window.NAS_WS_URL === 'string'
            ? window.NAS_WS_URL.trim()
            : '';
    const webSocketEnabled = configuredWsUrl !== '';

    window.CONFIG = Object.assign({}, window.CONFIG || {}, {
        API_BASE: window.API_BASE,
        ASSETS_URL: window.NAS_ASSETS,
        WS_URL: configuredWsUrl,
        WS_ENABLED: webSocketEnabled
    });

    console.log('✅ NAS Config loaded', {
        basePath: window._basePath,
        apiBase: window.API_BASE,
        assets: window.NAS_ASSETS,
        webSocket: webSocketEnabled ? window.CONFIG.WS_URL : 'disabled'
    });
})();
