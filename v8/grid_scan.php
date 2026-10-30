<?php
/**
 * In-app Grid QR scanner.
 * Opened by the Grid header scanner action (open-url).
 * A scanned connect link opens that person. grid_sso on this page is the
 * scanner's own login token and is forwarded only to this host.
 */
header('Cache-Control: private, no-cache');
header('X-Frame-Options: SAMEORIGIN');
$sso = trim((string) ($_GET['grid_sso'] ?? ''));
$host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? 'efactor.tech'));
$host = preg_replace('/:\d+$/', '', $host) ?: 'efactor.tech';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Scan QR</title>
<style>
  * { box-sizing: border-box; }
  html, body { margin: 0; height: 100%; background: #020617; color: #fff; font-family: Inter, Arial, sans-serif; }
  body { display: flex; flex-direction: column; }
  header { display: flex; align-items: center; gap: 10px; padding: 12px 16px; background: linear-gradient(135deg,#111827,#172554); border-bottom: 1px solid rgba(255,255,255,.1); }
  header button, header label { border: 1px solid rgba(255,255,255,.14); border-radius: 10px; background: rgba(255,255,255,.1); color: #fff; font-weight: 800; padding: 8px 12px; cursor: pointer; }
  header strong { font-size: 16px; }
  header label { margin-left: auto; font-size: 12px; }
  header button:hover, header label:hover { background: rgba(255,255,255,.18); }
  header input { display: none; }
  .scan-stage { flex: 1; min-height: 0; position: relative; overflow: hidden; background: #020617; }
  #reader { position: absolute; inset: 0; width: 100%; height: 100%; }
  #reader video { width: 100% !important; height: 100% !important; object-fit: cover !important; }
  #qr-shaded-region { display: none !important; }
  .scan-overlay { position: absolute; inset: 0; z-index: 3; display: grid; place-items: center; pointer-events: none; background: radial-gradient(circle at center,transparent 0 29%,rgba(2,6,23,.18) 42%,rgba(2,6,23,.58) 100%); }
  .scan-frame { position: relative; width: min(72vw,280px); aspect-ratio: 1; border: 1px solid rgba(255,255,255,.22); border-radius: 28px; box-shadow: 0 0 0 999px rgba(2,6,23,.13),0 20px 60px rgba(0,0,0,.3),inset 0 0 34px rgba(34,211,238,.06); }
  .scan-frame::before, .scan-frame::after { content: ""; position: absolute; inset: -3px; border-radius: 29px; background: linear-gradient(#67e8f9,#67e8f9) left top/42px 4px no-repeat,linear-gradient(#67e8f9,#67e8f9) left top/4px 42px no-repeat,linear-gradient(#67e8f9,#67e8f9) right top/42px 4px no-repeat,linear-gradient(#67e8f9,#67e8f9) right top/4px 42px no-repeat; }
  .scan-frame::after { transform: rotate(180deg); filter: drop-shadow(0 0 8px rgba(34,211,238,.85)); }
  .scan-line { position: absolute; left: 12px; right: 12px; top: 12px; height: 2px; border-radius: 999px; background: linear-gradient(90deg,transparent,#22d3ee 12%,#fff 50%,#22d3ee 88%,transparent); box-shadow: 0 0 9px #22d3ee,0 0 20px rgba(34,211,238,.8); animation: scanSweep 2.4s ease-in-out infinite alternate; }
  .scan-line::after { content: ""; position: absolute; inset: -12px 0; background: linear-gradient(180deg,transparent,rgba(34,211,238,.14),transparent); }
  @keyframes scanSweep { from { top: 12px; } to { top: calc(100% - 14px); } }
  @media (prefers-reduced-motion: reduce) { .scan-line { animation: none; top: 50%; } }
  p { margin: 0; padding: 14px 16px 20px; text-align: center; color: #cbd5e1; background: linear-gradient(180deg,#020617,#0f172a); font-size: 13px; font-weight: 700; }
</style>
</head>
<body>
<header>
  <button type="button" id="backBtn" aria-label="Back">Back</button>
  <strong>Scan QR</strong>
  <label for="qrFile">Upload QR<input type="file" id="qrFile" accept="image/*"></label>
</header>
<div class="scan-stage">
  <div id="reader"></div>
  <div class="scan-overlay" aria-hidden="true">
    <div class="scan-frame"><span class="scan-line"></span></div>
  </div>
</div>
<p id="status">Align the QR code inside the frame</p>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function () {
  var statusEl = document.getElementById('status');
  var busy = false;
  var sso = <?= json_encode($sso, JSON_UNESCAPED_SLASHES) ?>;
  var ownHost = <?= json_encode($host, JSON_UNESCAPED_SLASHES) ?>;

  var scanner = null;

  function leaveScanner() {
    var finish = function () {
      var bridge = window.flutter_inappwebview;
      if (bridge && typeof bridge.callHandler === 'function') {
        try { bridge.callHandler('closeWebView'); } catch (e) {}
        try { bridge.callHandler('goBack'); } catch (e) {}
        try { bridge.callHandler('close'); } catch (e) {}
        try { bridge.callHandler('back'); } catch (e) {}
      }
      try {
        if (window.parent && window.parent !== window) {
          window.parent.postMessage({ type: 'grid-scanner-close' }, '*');
        }
      } catch (e) {}
      if (window.history.length > 1) {
        window.history.back();
        return;
      }
      try { window.close(); } catch (e) {}
      var fallback = <?= json_encode('/index.php/grid' . ($sso !== '' ? ('?grid_sso=' . rawurlencode($sso)) : ''), JSON_UNESCAPED_SLASHES) ?>;
      window.setTimeout(function () { window.location.replace(fallback); }, 250);
    };
    if (scanner && typeof scanner.stop === 'function') {
      scanner.stop().then(finish).catch(finish);
      scanner = null;
      return;
    }
    finish();
  }

  document.getElementById('backBtn').addEventListener('click', function () {
    leaveScanner();
  });

  function say(text) { statusEl.textContent = text; }

  function mintFreshSso() {
    if (!sso) return Promise.resolve('');
    var mintUrl = '/v8/grid_sso_mint.php?grid_sso=' + encodeURIComponent(sso);
    return fetch(mintUrl, { method: 'GET', credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
      .then(function (res) { return res.json(); })
      .then(function (data) { return (data && data.success && data.grid_sso) ? String(data.grid_sso) : ''; })
      .catch(function () { return ''; });
  }

  function buildConnectDestination(raw, freshToken) {
    var value = String(raw || '').trim().replace(/^["']|["']$/g, '');
    if (!/^https?:\/\//i.test(value)) return '';
    var url;
    try { url = new URL(value); } catch (e) { return ''; }
    var host = url.hostname.toLowerCase();
    var allowed = host === ownHost || host === 'efactor.tech' || host === 'www.efactor.tech' || host === 'localhost' || host === '127.0.0.1';
    if (!allowed) return '';

    var path = url.pathname;
    var mode = (url.searchParams.get('mode') || '').toLowerCase();
    var isCircle = /grid-chat\/connect|grid_chat\/connect/i.test(path) || mode === 'circle';
    var isProfessional = /\/grid\/connect/i.test(path) || (mode === 'professional' && !isCircle);
    var token = String(freshToken || '').trim();
    url.searchParams.delete('grid_sso');

    // Keep /grid/connect (GRID gate). Same host as this scanner so the mint verifies.
    if (isCircle || isProfessional) {
      var connectPath = isCircle ? '/index.php/grid-chat/connect' : '/index.php/grid/connect';
      var next = new URL(window.location.origin + connectPath);
      next.searchParams.set('mode', isCircle ? 'circle' : 'professional');
      var gridId = url.searchParams.get('grid_id') || url.searchParams.get('gridId') || '';
      var user = url.searchParams.get('user_id') || url.searchParams.get('userId') || '';
      if (gridId) next.searchParams.set('grid_id', gridId);
      if (!isCircle && user) next.searchParams.set('user_id', user);
      if (token) next.searchParams.set('grid_sso', token);
      return next.toString();
    }

    if (token) url.searchParams.set('grid_sso', token);
    if (host !== ownHost && (host === 'efactor.tech' || host === 'www.efactor.tech')) {
      url.protocol = window.location.protocol;
      url.host = window.location.host;
    }
    return url.toString();
  }

  function openScanned(raw) {
    mintFreshSso().then(function (token) {
      var dest = buildConnectDestination(raw, token || sso);
      if (!dest) {
        say('That QR is not a connect link');
        busy = false;
        return;
      }
      if (!(token || sso)) {
        say('Login expired. Open the scanner again from Grid.');
        busy = false;
        return;
      }
      window.location.replace(dest);
    });
  }

  if (typeof Html5Qrcode !== 'function') {
    say('QR scanner is unavailable in this browser');
    return;
  }
  function startCamera(facing) {
    if (typeof Html5Qrcode !== 'function') {
      say('QR scanner is unavailable in this browser');
      return;
    }
    var mount = document.getElementById('reader');
    if (mount) mount.innerHTML = '';
    scanner = new Html5Qrcode('reader', {
      verbose: false,
      experimentalFeatures: { useBarCodeDetectorIfSupported: true }
    });
    // No qrbox — full-frame decode; CSS frame is decoration only.
    scanner.start(
      { facingMode: facing || 'environment' },
      { fps: 15, disableFlip: false },
      function (decoded) {
        if (busy) return;
        busy = true;
        scanner.stop().catch(function () {}).then(function () { openScanned(decoded); });
      },
      function () {}
    ).catch(function (err) {
      if ((facing || 'environment') === 'environment') {
        startCamera('user');
        return;
      }
      say((err && err.message) ? err.message : 'Could not start the camera');
    });
  }

  document.getElementById('qrFile').addEventListener('change', function (event) {
    var file = event.target.files && event.target.files[0];
    event.target.value = '';
    if (!file || busy) return;
    busy = true;
    var decode = function () {
      var hidden = document.getElementById('scanFileDecoder');
      if (!hidden) {
        hidden = document.createElement('div');
        hidden.id = 'scanFileDecoder';
        hidden.hidden = true;
        document.body.appendChild(hidden);
      }
      var decoder = new Html5Qrcode('scanFileDecoder');
      decoder.scanFile(file, true).then(openScanned).catch(function () {
        say('No QR code was found in that image');
        busy = false;
        startCamera('environment');
      });
    };
    if (scanner && typeof scanner.stop === 'function') {
      scanner.stop().then(decode).catch(decode);
    } else {
      decode();
    }
  });
  startCamera('environment');
})();
</script>
</body>
</html>
