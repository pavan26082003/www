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
  html, body { margin: 0; height: 100%; background: #05070c; color: #fff; font-family: Inter, Arial, sans-serif; }
  body { display: flex; flex-direction: column; }
  header { display: flex; align-items: center; gap: 10px; padding: 14px 16px; }
  header button { border: 0; border-radius: 10px; background: #1e293b; color: #fff; font-weight: 700; padding: 8px 12px; }
  header strong { font-size: 16px; }
  #reader { flex: 1; min-height: 0; }
  p { margin: 0; padding: 12px 16px 20px; text-align: center; color: #cbd5e1; font-size: 14px; }
</style>
</head>
<body>
<header>
  <button type="button" id="backBtn" aria-label="Back">Back</button>
  <strong>Scan QR</strong>
</header>
<div id="reader"></div>
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
        try { bridge.callHandler('goBack'); } catch (e) {}
        try { bridge.callHandler('close'); } catch (e) {}
        try { bridge.callHandler('back'); } catch (e) {}
      }
      if (window.history.length > 1) {
        window.history.back();
        return;
      }
      window.history.back();
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

  function openScanned(raw) {
    var value = String(raw || '').trim().replace(/^["']|["']$/g, '');
    if (!/^https?:\/\//i.test(value)) {
      say('That QR is not a connect link');
      busy = false;
      return;
    }
    try {
      var url = new URL(value);
      var host = url.hostname.toLowerCase();
      var allowed = host === ownHost || host === 'efactor.tech' || host === 'www.efactor.tech' || host === 'localhost' || host === '127.0.0.1';
      if (allowed && sso && !url.searchParams.get('grid_sso')) {
        url.searchParams.set('grid_sso', sso);
      }
      window.location.replace(url.toString());
    } catch (e) {
      say('That QR is not a connect link');
      busy = false;
    }
  }

  if (typeof Html5Qrcode !== 'function') {
    say('QR scanner is unavailable in this browser');
    return;
  }
  scanner = new Html5Qrcode('reader');
  scanner.start(
    { facingMode: 'environment' },
    { fps: 10, qrbox: { width: 240, height: 240 } },
    function (decoded) {
      if (busy) return;
      busy = true;
      scanner.stop().catch(function () {}).then(function () { openScanned(decoded); });
    },
    function () {}
  ).catch(function (err) {
    say((err && err.message) ? err.message : 'Could not start the camera');
  });
})();
</script>
</body>
</html>
