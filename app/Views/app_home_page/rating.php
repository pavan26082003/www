<?php
helper('url');
$apiBase = rtrim((string) ($apiBase ?? site_url('app-home-page/api')), '/');
$logoUrl = trim((string) ($logoUrl ?? 'https://efactor.tech/files/home/Logo_new.png'));
if ($logoUrl === '') {
    $logoUrl = 'https://efactor.tech/files/home/Logo_new.png';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Rating — eFactor</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --bg: #0b1220;
      --surface: #151b2e;
      --text: #f8fafc;
      --muted: #94a3b8;
      --accent: #7c3aed;
      --star-off: rgba(148, 163, 184, 0.45);
      --star-on: #f5b942;
      --line: rgba(255, 255, 255, 0.12);
    }
    * { box-sizing: border-box; }
    html, body {
      margin: 0;
      min-height: 100%;
      font-family: Sora, Inter, system-ui, -apple-system, sans-serif;
      background: radial-gradient(circle at 12% 18%, rgba(124, 58, 237, 0.18), transparent 30%),
                  linear-gradient(160deg, #0b1220, #111827 55%, #07111f);
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
      width: 28px;
      height: 28px;
      object-fit: contain;
      border-radius: 6px;
    }
    .brandbar strong {
      font-size: 16px;
      font-weight: 800;
      letter-spacing: -0.02em;
    }
    .brandbar span {
      margin-left: auto;
      color: var(--muted);
      font-size: 13px;
      font-weight: 600;
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
    textarea:focus { outline: 2px solid rgba(124, 58, 237, 0.45); outline-offset: 1px; }
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
      background: rgba(15, 23, 42, 0.95);
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
      <img src="<?= esc($logoUrl) ?>" alt="eFactor">
      <strong>eFactor</strong>
      <span>Account</span>
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
      var stars = 0;
      var row = document.getElementById('starRow');
      var commentEl = document.getElementById('comment');
      var sendBtn = document.getElementById('sendBtn');
      var toast = document.getElementById('toast');

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
              setTimeout(function () {
                if (window.history.length > 1) window.history.back();
              }, 900);
              return;
            }
            sendBtn.disabled = false;
          })
          .catch(function () {
            showToast('Could not send rating. Please try again.');
            sendBtn.disabled = false;
          });
      });

      renderStars();
    })();
  </script>
</body>
</html>
