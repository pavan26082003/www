<?php
helper('url');
$apiBase = rtrim((string) ($apiBase ?? site_url('app-home-page/api')), '/');
$logoUrlLight = trim((string) ($logoUrlLight ?? ($logoUrl ?? 'https://efactor.tech/assets/images/efactor-logo.png')));
$logoUrlDark = trim((string) ($logoUrlDark ?? 'https://efactor.tech/assets/images/efactor-logo_light.png'));
$appearanceMode = strtolower(trim((string) ($appearanceMode ?? 'system')));
if (!in_array($appearanceMode, array('light', 'dark', 'system'), true)) {
    $appearanceMode = 'system';
}
$accentColor = trim((string) ($accentColor ?? '#7c3aed'));
if (!preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $accentColor)) {
    $accentColor = '#7c3aed';
}
if ($logoUrlLight === '') {
    $logoUrlLight = 'https://efactor.tech/assets/images/efactor-logo.png';
}
if ($logoUrlDark === '') {
    $logoUrlDark = 'https://efactor.tech/assets/images/efactor-logo_light.png';
}
?>
<!DOCTYPE html>
<html lang="en" data-appearance="<?= esc($appearanceMode) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="color-scheme" content="light dark">
  <title>Rating — eFactor</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --accent: <?= esc($accentColor) ?>;
      --star-on: #f5b942;
    }
    /* Light palette (Profile → Light, or System when OS is light) */
    html[data-theme="light"] {
      --bg: #f3f4f6;
      --bg-glow: rgba(124, 58, 237, 0.10);
      --bg-grad-a: #f8fafc;
      --bg-grad-b: #eef2ff;
      --bg-grad-c: #f1f5f9;
      --surface: #ffffff;
      --text: #0f172a;
      --muted: #64748b;
      --star-off: rgba(100, 116, 139, 0.45);
      --line: rgba(15, 23, 42, 0.10);
      --toast-bg: rgba(255, 255, 255, 0.96);
      --focus: color-mix(in srgb, var(--accent, #7c3aed) 45%, transparent);
    }
    /* Dark palette (Profile → Dark, or System when OS is dark) */
    html[data-theme="dark"] {
      --bg: #0b1220;
      --bg-glow: rgba(124, 58, 237, 0.18);
      --bg-grad-a: #0b1220;
      --bg-grad-b: #111827;
      --bg-grad-c: #07111f;
      --surface: #151b2e;
      --text: #f8fafc;
      --muted: #94a3b8;
      --star-off: rgba(148, 163, 184, 0.45);
      --line: rgba(255, 255, 255, 0.12);
      --toast-bg: rgba(15, 23, 42, 0.95);
      --focus: color-mix(in srgb, var(--accent, #7c3aed) 45%, transparent);
    }
    * { box-sizing: border-box; }
    html, body {
      margin: 0;
      min-height: 100%;
      font-family: Sora, Inter, system-ui, -apple-system, sans-serif;
      background: radial-gradient(circle at 12% 18%, var(--bg-glow), transparent 30%),
                  linear-gradient(160deg, var(--bg-grad-a), var(--bg-grad-b) 55%, var(--bg-grad-c));
      color: var(--text);
    }
    body { padding: 20px 18px calc(28px + env(safe-area-inset-bottom)); }
    .wrap { max-width: 420px; margin: 0 auto; }
    .brandbar {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 0 0 18px;
    }
    .brandbar img {
      width: auto;
      height: 44px;
      max-width: 180px;
      object-fit: contain;
      border-radius: 0;
      background: transparent;
    }
    h1 { margin: 8px 0 4px; font-size: 28px; font-weight: 800; letter-spacing: -0.02em; }
    .sub { margin: 0 0 22px; color: var(--muted); font-size: 13px; line-height: 1.4; }
    .stars {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin: 8px 0 22px;
    }
    .star {
      width: 52px;
      height: 52px;
      border: 0;
      border-radius: 14px;
      background: transparent;
      color: var(--star-off);
      font-size: 30px;
      cursor: pointer;
      padding: 0;
    }
    .star.is-on { color: var(--star-on); }
    label.field { display: block; text-align: left; margin: 0 0 16px; }
    label.field span {
      display: block;
      font-size: 13px;
      font-weight: 700;
      margin-bottom: 8px;
    }
    textarea {
      width: 100%;
      min-height: 120px;
      resize: vertical;
      border-radius: 14px;
      border: 1px solid var(--line);
      background: var(--surface);
      color: var(--text);
      padding: 12px;
      font: inherit;
      font-size: 14px;
      line-height: 1.4;
    }
    textarea::placeholder { color: var(--muted); opacity: 0.9; }
    textarea:focus { outline: 2px solid var(--focus); outline-offset: 1px; }
    .send {
      width: 100%;
      border: 0;
      border-radius: 16px;
      min-height: 52px;
      background: var(--accent);
      color: #fff;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .send:disabled { opacity: 0.55; cursor: default; }
    .toast {
      position: fixed;
      left: 50%;
      bottom: 24px;
      transform: translateX(-50%);
      background: var(--toast-bg);
      border: 1px solid var(--line);
      color: var(--text);
      padding: 10px 14px;
      border-radius: 999px;
      font-size: 13px;
      opacity: 0;
      pointer-events: none;
      transition: opacity .2s ease;
      max-width: 90vw;
      z-index: 20;
    }
    .toast.show { opacity: 1; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="brandbar">
      <img
        id="ratingLogo"
        src="<?= esc($logoUrlLight) ?>"
        alt="eFactor"
        data-logo-light="<?= esc($logoUrlLight) ?>"
        data-logo-dark="<?= esc($logoUrlDark) ?>"
      >
    </div>
    <h1>Rating</h1>
    <p class="sub">Select a star, then write what went wrong</p>
    <div class="stars" id="starRow" role="group" aria-label="Star rating"></div>
    <label class="field">
      <span>Comment</span>
      <textarea id="comment" maxlength="2000" placeholder="Describe the issue so it can be fixed"></textarea>
    </label>
    <button type="button" class="send" id="sendBtn"><i class="fas fa-paper-plane"></i> Send rating</button>
  </div>
  <div class="toast" id="toast" role="status"></div>
  <script>
    (function () {
      var apiBase = <?= json_encode($apiBase) ?>;
      var appearanceMode = <?= json_encode($appearanceMode) ?>;
      var stars = 0;
      var row = document.getElementById('starRow');
      var commentEl = document.getElementById('comment');
      var sendBtn = document.getElementById('sendBtn');
      var toast = document.getElementById('toast');
      var logoEl = document.getElementById('ratingLogo');
      var root = document.documentElement;

      function systemPrefersDark() {
        try {
          return !!(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        } catch (e) {
          return false;
        }
      }

      function resolveTheme() {
        var mode = String(appearanceMode || 'system').toLowerCase();
        if (mode === 'light' || mode === 'dark') return mode;
        return systemPrefersDark() ? 'dark' : 'light';
      }

      function applyAppearance() {
        var theme = resolveTheme();
        root.setAttribute('data-theme', theme);
        root.style.colorScheme = theme;
        if (!logoEl) return;
        var lightUrl = logoEl.getAttribute('data-logo-light') || 'https://efactor.tech/assets/images/efactor-logo.png';
        var darkUrl = logoEl.getAttribute('data-logo-dark') || 'https://efactor.tech/assets/images/efactor-logo_light.png';
        var next = theme === 'dark' ? darkUrl : lightUrl;
        if (next && logoEl.getAttribute('src') !== next) {
          logoEl.setAttribute('src', next);
        }
      }

      function returnToProfile() {
        // Same close pattern as grid/help scanners — closes in-app WebView back to Profile.
        try {
          var bridge = window.flutter_inappwebview;
          if (bridge && typeof bridge.callHandler === 'function') {
            bridge.callHandler('closeWebView');
            return;
          }
        } catch (e) {}
        try {
          if (window.history.length > 1) {
            window.history.back();
            return;
          }
        } catch (e2) {}
        try {
          window.close();
        } catch (e3) {}
      }

      function showToast(msg) {
        toast.textContent = msg;
        toast.classList.add('show');
        setTimeout(function () { toast.classList.remove('show'); }, 2500);
      }

      function renderStars() {
        var html = '';
        var i;
        for (i = 1; i <= 5; i++) {
          html += '<button type="button" class="star' + (i <= stars ? ' is-on' : '') + '" data-stars="' + i + '" aria-label="' + i + (i === 1 ? ' star' : ' stars') + '"><i class="fas fa-star"></i></button>';
        }
        row.innerHTML = html;
        row.querySelectorAll('[data-stars]').forEach(function (btn) {
          btn.addEventListener('click', function () {
            stars = parseInt(btn.getAttribute('data-stars'), 10) || 0;
            renderStars();
          });
        });
      }

      sendBtn.addEventListener('click', function () {
        var comment = (commentEl.value || '').trim();
        if (!stars) {
          showToast('Select a star rating.');
          return;
        }
        if (!comment) {
          showToast('Write a comment about the issue.');
          return;
        }
        sendBtn.disabled = true;
        fetch(apiBase + '/rating-feedback', {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ rating: stars, comment: comment })
        })
          .then(function (r) { return r.json().catch(function () { return { success: false, message: 'Could not send rating.' }; }); })
          .then(function (res) {
            showToast((res && res.message) || (res && res.success ? 'Thanks. Your rating was sent.' : 'Could not send rating.'));
            if (res && res.success) {
              stars = 0;
              commentEl.value = '';
              renderStars();
              setTimeout(returnToProfile, 700);
              return;
            }
            sendBtn.disabled = false;
          })
          .catch(function () {
            showToast('Could not send rating. Please try again.');
            sendBtn.disabled = false;
          });
      });

      applyAppearance();
      try {
        var mq = window.matchMedia('(prefers-color-scheme: dark)');
        var onOsChange = function () {
          if (String(appearanceMode || 'system').toLowerCase() === 'system') {
            applyAppearance();
          }
        };
        if (mq && mq.addEventListener) mq.addEventListener('change', onOsChange);
        else if (mq && mq.addListener) mq.addListener(onOsChange);
      } catch (e) {}

      renderStars();
    })();
  </script>
</body>
</html>
