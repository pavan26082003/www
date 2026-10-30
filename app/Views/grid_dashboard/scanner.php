<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
  <title>QR Scanner</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous">
  <style>
    * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
    html, body {
      margin: 0; padding: 0;
      width: 100%; height: 100%;
      background: #090d16; color: #f8fafc;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      overflow: hidden;
    }
    .scanner-shell {
      position: relative;
      width: 100%; height: 100%;
      display: flex; flex-direction: column;
    }
    .scanner-header {
      position: absolute; top: 0; left: 0; right: 0; z-index: 30;
      display: flex; align-items: center; justify-content: space-between;
      padding: env(safe-area-inset-top, 16px) 16px 14px;
      background: linear-gradient(180deg, rgba(9,13,22,.9) 0%, rgba(9,13,22,0) 100%);
    }
    .back-btn {
      width: 40px; height: 40px; border-radius: 50%;
      background: rgba(255,255,255,.15); backdrop-filter: blur(8px);
      border: 0; color: #fff; font-size: 16px;
      display: grid; place-items: center; cursor: pointer;
    }
    .scanner-title {
      font-size: 16px; font-weight: 700; color: #fff;
      display: flex; align-items: center; gap: 8px;
    }
    .scanner-viewport {
      position: relative; flex: 1;
      display: flex; align-items: center; justify-content: center;
      background: #000; overflow: hidden;
    }
    #reader {
      width: 100% !important; height: 100% !important;
      border: 0 !important;
    }
    #reader video {
      width: 100% !important; height: 100% !important;
      object-fit: cover !important;
    }
    /* Library overlay fights our viewfinder and hurts detection — hide it. */
    #qr-shaded-region { display: none !important; }
    .viewfinder-box {
      position: absolute;
      width: 260px; height: 260px;
      pointer-events: none; z-index: 20;
    }
    .corner {
      position: absolute; width: 28px; height: 28px;
      border-color: #8b5cf6; border-style: solid; border-width: 0;
    }
    .corner.tl { top: 0; left: 0; border-top-width: 4px; border-left-width: 4px; border-top-left-radius: 12px; }
    .corner.tr { top: 0; right: 0; border-top-width: 4px; border-right-width: 4px; border-top-right-radius: 12px; }
    .corner.bl { bottom: 0; left: 0; border-bottom-width: 4px; border-left-width: 4px; border-bottom-left-radius: 12px; }
    .corner.br { bottom: 0; right: 0; border-bottom-width: 4px; border-right-width: 4px; border-bottom-right-radius: 12px; }
    .laser-line {
      position: absolute; left: 12px; right: 12px; height: 3px;
      background: linear-gradient(90deg, transparent, #c084fc, #8b5cf6, #c084fc, transparent);
      box-shadow: 0 0 14px #8b5cf6, 0 0 6px #c084fc;
      animation: laserMove 2.2s ease-in-out infinite alternate;
    }
    @keyframes laserMove { 0% { top: 12px; } 100% { top: 244px; } }
    .scanner-footer {
      position: absolute; bottom: 0; left: 0; right: 0; z-index: 30;
      padding: 16px 20px env(safe-area-inset-bottom, 24px);
      background: linear-gradient(0deg, rgba(9,13,22,.95) 0%, rgba(9,13,22,0) 100%);
      display: flex; flex-direction: column; align-items: center; gap: 14px;
    }
    .scanner-tip {
      font-size: 13px; color: #cbd5e1; font-weight: 500; text-align: center;
    }
    .scanner-controls {
      display: flex; gap: 16px; align-items: center; justify-content: center;
    }
    .ctrl-btn {
      min-width: 44px; height: 44px; padding: 0 16px; border-radius: 22px;
      background: rgba(255,255,255,.15); backdrop-filter: blur(8px);
      border: 1px solid rgba(255,255,255,.2); color: #fff;
      font-size: 13px; font-weight: 600; cursor: pointer;
      display: inline-flex; align-items: center; gap: 8px;
    }
    .ctrl-btn:active { transform: scale(.96); }
    .status-toast {
      position: absolute; top: 70px; left: 50%; transform: translateX(-50%);
      padding: 8px 18px; border-radius: 20px; background: rgba(124,58,237,.95);
      color: #fff; font-size: 12px; font-weight: 600; z-index: 40;
      display: none; box-shadow: 0 4px 14px rgba(0,0,0,.3);
    }
  </style>
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body>
  <div class="scanner-shell">
    <header class="scanner-header">
      <button type="button" class="back-btn" id="backBtn" aria-label="Back">
        <i class="fas fa-arrow-left"></i>
      </button>
      <div class="scanner-title">
        <i class="fas fa-qrcode"></i>
        <span>Scan QR Code</span>
      </div>
      <div style="width:40px"></div>
    </header>

    <div class="status-toast" id="toastEl"></div>

    <div class="scanner-viewport">
      <div id="reader"></div>
      <div class="viewfinder-box">
        <div class="corner tl"></div>
        <div class="corner tr"></div>
        <div class="corner bl"></div>
        <div class="corner br"></div>
        <div class="laser-line"></div>
      </div>
    </div>

    <footer class="scanner-footer">
      <div class="scanner-tip">Align any GRID or profile QR code inside the box</div>
      <div class="scanner-controls">
        <button type="button" class="ctrl-btn" id="flipBtn">
          <i class="fas fa-camera-rotate"></i>
          <span>Flip</span>
        </button>
        <label class="ctrl-btn" style="cursor:pointer">
          <i class="fas fa-image"></i>
          <span>Gallery</span>
          <input type="file" id="fileInput" accept="image/*" style="display:none">
        </label>
      </div>
    </footer>
  </div>

  <script>
    (function() {
      var html5QrCode = null;
      var currentFacingMode = 'environment';
      var isProcessing = false;
      var pageSso = <?= json_encode(trim((string) ($_GET['grid_sso'] ?? '')), JSON_UNESCAPED_SLASHES) ?>;
      var mintUrl = <?= json_encode(site_url('grid-dashboard/api/mint-sso'), JSON_UNESCAPED_SLASHES) ?>;

      function toast(msg) {
        var el = document.getElementById('toastEl');
        if (!el) return;
        el.textContent = msg;
        el.style.display = 'block';
        clearTimeout(toast._t);
        toast._t = setTimeout(function() { el.style.display = 'none'; }, 2200);
      }

      function mintFreshSso() {
        // Prefer session mint (dashboard). Fall back to re-issue from page token.
        var fromSession = fetch(mintUrl, {
          method: 'GET',
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (res) { return res.json(); }).then(function (data) {
          return (data && data.success && data.grid_sso) ? String(data.grid_sso) : '';
        }).catch(function () { return ''; });

        return fromSession.then(function (token) {
          if (token) return token;
          if (!pageSso) return '';
          return fetch('/v8/grid_sso_mint.php?grid_sso=' + encodeURIComponent(pageSso), {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
          }).then(function (res) { return res.json(); }).then(function (data) {
            return (data && data.success && data.grid_sso) ? String(data.grid_sso) : '';
          }).catch(function () { return ''; });
        });
      }

      function buildConnectDestination(raw, freshToken) {
        var targetUrl = String(raw || '').trim();
        if (/^efactor:\/\/(?:navigate\?url=)?(.*)$/i.test(targetUrl)) {
          var deep = targetUrl.match(/^efactor:\/\/(?:navigate\?url=)?(.*)$/i);
          if (deep && deep[1]) targetUrl = decodeURIComponent(deep[1]);
        } else if (/S\.target_url=/i.test(targetUrl)) {
          var intent = targetUrl.match(/S\.target_url=([^;]+)/i);
          if (intent && intent[1]) targetUrl = decodeURIComponent(intent[1]);
        }
        if (!/^https?:\/\//i.test(targetUrl) && targetUrl.indexOf('/') !== 0) return '';
        if (/grid-dashboard\/scanner|\/grid\/scanner|\/grid\/qr(?:\?|$)/i.test(targetUrl)) return '';

        var parsed;
        try { parsed = new URL(targetUrl, window.location.origin); } catch (e) { return ''; }
        var path = parsed.pathname;
        var mode = (parsed.searchParams.get('mode') || '').toLowerCase();
        var isCircle = /grid-chat\/connect|grid_chat\/connect/i.test(path) || mode === 'circle';
        var isProfessional = /\/grid\/connect/i.test(path) || (mode === 'professional' && !isCircle);
        var token = String(freshToken || '').trim();
        parsed.searchParams.delete('grid_sso');

        // Stay on /grid/connect — GRID gate() consumes grid_sso and redirects to profile.
        if (isCircle || isProfessional) {
          var connectPath = isCircle ? '/index.php/grid-chat/connect' : '/index.php/grid/connect';
          var next = new URL(window.location.origin + connectPath);
          next.searchParams.set('mode', isCircle ? 'circle' : 'professional');
          var gridId = parsed.searchParams.get('grid_id') || parsed.searchParams.get('gridId') || '';
          var user = parsed.searchParams.get('user_id') || parsed.searchParams.get('userId') || '';
          if (gridId) next.searchParams.set('grid_id', gridId);
          if (user) next.searchParams.set('user_id', user);
          if (token) next.searchParams.set('grid_sso', token);
          return next.toString();
        }

        if (token) parsed.searchParams.set('grid_sso', token);
        if (window.location.hostname === 'localhost' && (parsed.hostname === 'efactor.tech' || parsed.hostname === 'www.efactor.tech')) {
          parsed.protocol = window.location.protocol;
          parsed.host = window.location.host;
        }
        return parsed.toString();
      }

      function handleScanSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;
        toast('QR Code Scanned!');

        try {
          if (navigator.vibrate) navigator.vibrate(100);
        } catch (e) {}

        if (html5QrCode) {
          try { html5QrCode.stop(); } catch (e) {}
        }

        mintFreshSso().then(function (token) {
          var targetUrl = buildConnectDestination(decodedText, token || pageSso);
          if (!targetUrl) {
            toast('That QR is not a connect link');
            isProcessing = false;
            startScanner(currentFacingMode);
            return;
          }
          if (!(token || pageSso)) {
            toast('Could not mint a login token. Open scanner from Grid again.');
            isProcessing = false;
            startScanner(currentFacingMode);
            return;
          }
          try { sessionStorage.setItem('gdScanDone', String(Date.now())); } catch (e) {}
          window.location.replace(targetUrl);
        }).catch(function () {
          toast('Could not open that QR');
          isProcessing = false;
          startScanner(currentFacingMode);
        });
      }

      function stopScanner() {
        if (!html5QrCode || typeof html5QrCode.stop !== 'function') {
          return Promise.resolve();
        }
        return html5QrCode.stop().catch(function () {}).then(function () {
          try { html5QrCode.clear(); } catch (e) {}
        });
      }

      function startScanner(facing) {
        stopScanner().then(function () {
          initScanner(facing || currentFacingMode);
        });
      }

      function initScanner(facing) {
        var mount = document.getElementById('reader');
        if (!mount || typeof Html5Qrcode !== 'function') {
          toast('QR scanner is unavailable in this browser');
          return;
        }
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
          toast('Camera unavailable');
          return;
        }
        currentFacingMode = facing || 'environment';
        // Fresh mount so Html5Qrcode does not reuse a broken video node.
        mount.innerHTML = '';
        html5QrCode = new Html5Qrcode('reader', {
          verbose: false,
          experimentalFeatures: { useBarCodeDetectorIfSupported: true }
        });
        // No qrbox: scan the full frame; CSS viewfinder is visual-only.
        html5QrCode.start(
          { facingMode: currentFacingMode },
          { fps: 15, disableFlip: false },
          function (decodedText) {
            if (isProcessing) return;
            handleScanSuccess(decodedText);
          },
          function () {}
        ).catch(function (err) {
          if (currentFacingMode === 'environment') {
            // Many desktop / emulator WebViews have no rear camera.
            initScanner('user');
            return;
          }
          toast('Camera unavailable: ' + ((err && err.message) || 'permission error'));
        });
      }

      document.getElementById('backBtn').addEventListener('click', function() {
        if (html5QrCode) {
          try { html5QrCode.stop(); } catch (e) {}
        }
        var bridge = window.flutter_inappwebview;
        if (bridge && typeof bridge.callHandler === 'function') {
          try { bridge.callHandler('closeWebView'); } catch (e) {}
          try { bridge.callHandler('goBack'); } catch (e) {}
          try { bridge.callHandler('close'); } catch (e) {}
          try { bridge.callHandler('back'); } catch (e) {}
        }
        if (window.history.length > 1) {
          window.history.back();
        }
      });

      document.getElementById('flipBtn').addEventListener('click', function() {
        currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
        startScanner(currentFacingMode);
      });

      document.getElementById('fileInput').addEventListener('change', function(e) {
        var file = e.target.files && e.target.files[0];
        e.target.value = '';
        if (!file || isProcessing) return;
        stopScanner().then(function () {
          var hidden = document.getElementById('scanFileDecoder');
          if (!hidden) {
            hidden = document.createElement('div');
            hidden.id = 'scanFileDecoder';
            hidden.hidden = true;
            document.body.appendChild(hidden);
          }
          var decoder = new Html5Qrcode('scanFileDecoder');
          decoder.scanFile(file, true).then(function (decodedText) {
            handleScanSuccess(decodedText);
          }).catch(function () {
            toast('No QR code detected in image');
            startScanner(currentFacingMode);
          });
        });
      });

      window.addEventListener('DOMContentLoaded', function() {
        var doneAt = 0;
        try { doneAt = parseInt(sessionStorage.getItem('gdScanDone') || '0', 10); } catch (e) {}
        if (doneAt && (Date.now() - doneAt) < 4000) {
          toast('QR already opened');
          return;
        }
        startScanner(currentFacingMode);
      });
    })();
  </script>
</body>
</html>
