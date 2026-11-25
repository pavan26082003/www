<?php
helper('url');
$apiBase = rtrim((string) ($apiBase ?? site_url('app-home-page/api')), '/');
$intent = strtolower(trim((string) ($intent ?? 'change')));
if (!in_array($intent, array('change', 'upload', 'remove'), true)) {
    $intent = 'change';
}
$avatarUrl = trim((string) ($avatarUrl ?? ''));
$appearanceMode = strtolower(trim((string) ($appearanceMode ?? 'system')));
if (!in_array($appearanceMode, array('light', 'dark', 'system'), true)) {
    $appearanceMode = 'system';
}
$hasPhoto = $avatarUrl !== '';
$logoUrlLight = trim((string) ($logoUrlLight ?? ($logoUrl ?? 'https://efactor.tech/assets/images/efactor-logo.png')));
$logoUrlDark = trim((string) ($logoUrlDark ?? 'https://efactor.tech/assets/images/efactor-logo_light.png'));
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
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, user-scalable=no">
  <meta name="color-scheme" content="light dark">
  <title>Profile picture — eFactor</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script>
    // Resolve theme before first paint (avoids a flash); full logic runs again at end of body.
    (function () {
      var mode = <?= json_encode($appearanceMode) ?>;
      var dark = mode === 'dark';
      if (mode !== 'light' && mode !== 'dark') {
        try { dark = !!(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches); } catch (e) {}
      }
      document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
      document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
    })();
  </script>
  <style>
    /* Dark palette (Profile → Dark, or System when OS is dark). Also the pre-JS fallback. */
    :root,
    html[data-theme="dark"] {
      --bg: #0b141a;
      --surface: #1b242b;
      --surface-alt: #111b21;
      --chip: #1b242b;
      --text: #e9edef;
      --muted: #8696a0;
      --line: rgba(233, 237, 239, 0.12);
      --line-strong: #8696a0;
      --link: #53bdeb;
      --press: rgba(255, 255, 255, 0.06);
      --handle: rgba(233, 237, 239, 0.28);
      --toast-bg: rgba(17, 27, 33, 0.96);
      --shadow-sheet: 0 -10px 40px rgba(0, 0, 0, 0.45);
      --shadow-edit: 0 6px 16px rgba(0, 0, 0, 0.35);
      --shutter: #e9edef;
      --shutter-ring: rgba(233, 237, 239, 0.28);
      --focus: rgba(83, 189, 235, 0.55);
    }
    /* Light palette (Profile → Light, or System when OS is light) */
    html[data-theme="light"] {
      --bg: #f3f4f6;
      --surface: #ffffff;
      --surface-alt: #f8fafc;
      --chip: #e2e8f0;
      --text: #0f172a;
      --muted: #556274;
      --line: rgba(15, 23, 42, 0.10);
      --line-strong: #556274;
      --link: #0369a1;
      --press: rgba(15, 23, 42, 0.06);
      --handle: rgba(15, 23, 42, 0.22);
      --toast-bg: rgba(255, 255, 255, 0.96);
      --shadow-sheet: 0 -10px 40px rgba(15, 23, 42, 0.12);
      --shadow-edit: 0 6px 16px rgba(15, 23, 42, 0.18);
      --shutter: #0f172a;
      --shutter-ring: rgba(15, 23, 42, 0.20);
      --focus: rgba(3, 105, 161, 0.45);
    }
    * { box-sizing: border-box; }
    html, body {
      margin: 0;
      min-height: 100%;
      font-family: Sora, Inter, system-ui, -apple-system, sans-serif;
      background: var(--bg);
      color: var(--text);
      touch-action: manipulation;
    }
    button:focus-visible { outline: 2px solid var(--focus); outline-offset: 2px; }
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: 0;
    }
    .stage {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      padding: 28px 18px 18px;
      background: var(--bg);
      position: relative;
    }
    .brandbar {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
    }
    .brandbar img {
      width: auto;
      height: 40px;
      max-width: 180px;
      object-fit: contain;
      border-radius: 0;
      background: transparent;
      display: block;
    }
    .photo {
      width: 168px;
      height: 168px;
      border-radius: 999px;
      overflow: visible;
      background: var(--chip);
      border: 0;
      position: relative;
      display: grid;
      place-items: center;
      color: var(--muted);
      font-size: 48px;
      margin-top: auto;
      margin-bottom: auto;
    }
    .photo-clip {
      width: 100%;
      height: 100%;
      border-radius: 999px;
      overflow: hidden;
      display: grid;
      place-items: center;
      background: var(--chip);
      border: 1px solid var(--line);
    }
    .photo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      display: block;
    }
    .edit {
      position: absolute;
      right: 8px;
      bottom: 8px;
      width: 42px;
      height: 42px;
      border-radius: 999px;
      background: var(--chip);
      color: var(--muted);
      display: grid;
      place-items: center;
      border: 2px solid var(--line-strong);
      box-shadow: var(--shadow-edit);
      font-size: 15px;
    }
    .edit i { color: var(--muted); }
    .sheet {
      background: var(--surface);
      border-radius: 18px 18px 0 0;
      border-top: 1px solid var(--line);
      padding: 8px 0 calc(14px + env(safe-area-inset-bottom));
      box-shadow: var(--shadow-sheet);
    }
    .handle {
      width: 42px;
      height: 4px;
      border-radius: 999px;
      background: var(--handle);
      margin: 2px auto 8px;
    }
    .header {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 42px;
      padding: 0 48px;
      margin-bottom: 4px;
    }
    .close {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      width: 36px;
      height: 36px;
      border: 0;
      border-radius: 999px;
      background: transparent;
      color: var(--muted);
      display: grid;
      place-items: center;
      cursor: pointer;
      padding: 0;
    }
    .title {
      font-size: 17px;
      font-weight: 600;
      color: var(--text);
    }
    .list { display: flex; flex-direction: column; padding: 4px 0 8px; }
    .option {
      width: 100%;
      border: 0;
      background: transparent;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 18px;
      min-height: 52px;
      padding: 10px 22px;
      text-align: left;
      cursor: pointer;
      font: inherit;
    }
    .option:active { background: var(--press); }
    .option i {
      width: 28px;
      text-align: center;
      font-size: 20px;
      color: var(--muted);
    }
    .option span { font-size: 16px; color: var(--text); }
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
      z-index: 60;
    }
    .toast.show { opacity: 1; }
    input[type="file"] { display: none; }
    .cam-root, .crop-root {
      position: fixed;
      inset: 0;
      z-index: 40;
      background: var(--bg);
      display: flex;
      flex-direction: column;
    }
    .cam-video {
      flex: 1;
      width: 100%;
      object-fit: cover;
      background: #000;
      min-height: 0;
    }
    .cam-actions, .crop-actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 14px 18px calc(14px + env(safe-area-inset-bottom));
      background: var(--surface);
      border-top: 1px solid var(--line);
    }
    .cam-cancel, .crop-btn {
      border: 0;
      background: transparent;
      color: var(--muted);
      font: inherit;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      padding: 8px 4px;
    }
    .crop-btn.is-done { color: var(--link); }
    .cam-shutter {
      width: 58px;
      height: 58px;
      border: 0;
      border-radius: 999px;
      background: var(--shutter);
      display: grid;
      place-items: center;
      cursor: pointer;
      box-shadow: 0 0 0 3px var(--shutter-ring);
      padding: 0;
    }
    .cam-shutter span {
      width: 46px;
      height: 46px;
      border-radius: 999px;
      border: 3px solid var(--surface);
      display: block;
    }
    .crop-stage {
      position: relative;
      flex: 1;
      min-height: 0;
      overflow: hidden;
      background: #000;
      touch-action: none;
      cursor: grab;
    }
    .crop-stage.is-dragging { cursor: grabbing; }
    .crop-img {
      position: absolute;
      left: 50%;
      top: 50%;
      transform-origin: center center;
      will-change: transform;
      user-select: none;
      -webkit-user-drag: none;
      pointer-events: none;
      max-width: none;
    }
    .crop-mask {
      position: absolute;
      inset: 0;
      pointer-events: none;
      background: transparent;
    }
    .crop-hole {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      border-radius: 999px;
      box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.62);
      border: 2px solid rgba(233, 237, 239, 0.92);
    }
    .crop-hint {
      position: absolute;
      left: 0;
      right: 0;
      bottom: 18px;
      text-align: center;
      color: #e9edef;
      font-size: 13px;
      font-weight: 500;
      text-shadow: 0 1px 4px rgba(0,0,0,.6);
      pointer-events: none;
    }
    .crop-zoom {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 14px;
      padding: 10px 18px;
      background: var(--surface-alt);
      border-top: 1px solid var(--line);
    }
    .crop-zoom button {
      width: 36px;
      height: 36px;
      border: 1px solid var(--line);
      border-radius: 999px;
      background: var(--chip);
      color: var(--muted);
      display: grid;
      place-items: center;
      cursor: pointer;
      font-size: 14px;
    }
    .crop-zoom input[type="range"] {
      flex: 1;
      max-width: 220px;
      accent-color: var(--link);
    }
  </style>
</head>
<body>
  <div class="stage">
    <div class="brandbar">
      <img
        id="profileLogo"
        src="<?= esc($logoUrlLight) ?>"
        alt="eFactor"
        data-logo-light="<?= esc($logoUrlLight) ?>"
        data-logo-dark="<?= esc($logoUrlDark) ?>"
      >
    </div>
    <div class="photo" id="photo">
      <div class="photo-clip" id="photoClip">
        <?php if ($hasPhoto): ?>
          <img id="photoImg" src="<?= esc($avatarUrl) ?>" alt="Profile photo">
        <?php else: ?>
          <i class="fas fa-user"></i>
        <?php endif; ?>
      </div>
      <span class="edit" aria-hidden="true"><i class="fas fa-pen"></i></span>
    </div>
  </div>
  <div class="sheet" role="dialog" aria-label="Profile picture">
    <div class="handle" aria-hidden="true"></div>
    <div class="header">
      <button type="button" class="close" id="closeBtn" aria-label="Close"><i class="fas fa-times"></i></button>
      <div class="title">Profile picture</div>
    </div>
    <div class="list">
      <button type="button" class="option" data-action="change"><i class="fas fa-camera"></i><span>Camera</span></button>
      <button type="button" class="option" data-action="upload"><i class="far fa-image"></i><span>Gallery</span></button>
      <?php if ($hasPhoto): ?>
        <button type="button" class="option" data-action="remove"><i class="far fa-trash-alt"></i><span>Remove</span></button>
      <?php endif; ?>
    </div>
  </div>
  <input id="cameraFile" type="file" accept="image/*" capture="user">
  <input id="galleryFile" type="file" accept="image/jpeg,image/png,image/webp,image/gif">
  <div class="toast" id="toast" role="status"></div>
  <script>
    (function () {
      var apiBase = <?= json_encode($apiBase) ?>;
      var appearanceMode = <?= json_encode($appearanceMode) ?>;
      var logoEl = document.getElementById('profileLogo');
      var root = document.documentElement;
      var toast = document.getElementById('toast');
      var closeBtn = document.getElementById('closeBtn');
      var cameraFile = document.getElementById('cameraFile');
      var galleryFile = document.getElementById('galleryFile');
      var stream = null;
      var cropState = null;
      var OUTPUT_SIZE = 640;

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

      function showToast(msg) {
        toast.textContent = msg;
        toast.classList.add('show');
        setTimeout(function () { toast.classList.remove('show'); }, 2200);
      }

      function returnToProfile() {
        stopCamera();
        closeCropper();
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
        try { window.close(); } catch (e3) {}
      }

      function showPhoto(url) {
        var clip = document.getElementById('photoClip');
        if (!clip) return;
        if (!url) {
          clip.innerHTML = '<i class="fas fa-user"></i>';
          return;
        }
        clip.innerHTML = '<img id="photoImg" alt="Profile photo" src="' + url + '">';
      }

      function stopCamera() {
        if (!stream) return;
        try {
          stream.getTracks().forEach(function (t) { try { t.stop(); } catch (e) {} });
        } catch (e2) {}
        stream = null;
        var root = document.getElementById('camRoot');
        if (root) root.remove();
      }

      function closeCropper() {
        if (cropState && cropState.objectUrl) {
          try { URL.revokeObjectURL(cropState.objectUrl); } catch (e) {}
        }
        cropState = null;
        var root = document.getElementById('cropRoot');
        if (root) root.remove();
      }

      function postForm(body) {
        fetch(apiBase + '/profile-photo', {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          body: body
        })
          .then(function (r) { return r.json().catch(function () { return { success: false, message: 'Could not update the profile photo.' }; }); })
          .then(function (res) {
            showToast((res && res.message) || (res && res.success ? 'Profile photo updated.' : 'Could not update the profile photo.'));
            if (res && res.success) {
              showPhoto(res.avatarUrl || '');
              setTimeout(returnToProfile, 700);
            }
          })
          .catch(function () {
            showToast('Could not update the profile photo. Please try again.');
          });
      }

      function uploadBlobOrFile(file, intent) {
        var body = new FormData();
        body.append('intent', intent);
        body.append('profile_image_file', file);
        postForm(body);
      }

      function clamp(n, min, max) {
        return Math.max(min, Math.min(max, n));
      }

      function cropDiameter(stage) {
        var w = stage.clientWidth || window.innerWidth || 320;
        var h = stage.clientHeight || window.innerHeight || 480;
        return Math.floor(Math.min(w, h) * 0.78);
      }

      function applyCropTransform() {
        if (!cropState || !cropState.img || !cropState.stage) return;
        var img = cropState.img;
        var scale = cropState.scale;
        img.style.width = (cropState.nw * scale) + 'px';
        img.style.height = (cropState.nh * scale) + 'px';
        img.style.transform = 'translate(calc(-50% + ' + cropState.ox + 'px), calc(-50% + ' + cropState.oy + 'px))';
        if (cropState.zoomRange) {
          cropState.zoomRange.value = String(scale);
        }
      }

      function fitCropImage() {
        if (!cropState || !cropState.stage) return;
        var dia = cropDiameter(cropState.stage);
        if (cropState.hole) {
          cropState.hole.style.width = dia + 'px';
          cropState.hole.style.height = dia + 'px';
        }
        var minScale = Math.max(dia / cropState.nw, dia / cropState.nh);
        cropState.minScale = minScale;
        cropState.maxScale = Math.max(minScale * 4, minScale + 0.01);
        if (!cropState.scale || cropState.scale < minScale) {
          cropState.scale = minScale;
        }
        cropState.scale = clamp(cropState.scale, cropState.minScale, cropState.maxScale);
        cropState.ox = 0;
        cropState.oy = 0;
        if (cropState.zoomRange) {
          cropState.zoomRange.min = String(cropState.minScale);
          cropState.zoomRange.max = String(cropState.maxScale);
          cropState.zoomRange.step = String((cropState.maxScale - cropState.minScale) / 100 || 0.01);
        }
        constrainCropOffset();
        applyCropTransform();
      }

      function constrainCropOffset() {
        if (!cropState) return;
        var dia = cropDiameter(cropState.stage);
        var half = dia / 2;
        var halfW = (cropState.nw * cropState.scale) / 2;
        var halfH = (cropState.nh * cropState.scale) / 2;
        var maxX = Math.max(0, halfW - half);
        var maxY = Math.max(0, halfH - half);
        cropState.ox = clamp(cropState.ox, -maxX, maxX);
        cropState.oy = clamp(cropState.oy, -maxY, maxY);
      }

      function exportCroppedSquare(done) {
        if (!cropState || !cropState.img) {
          done(null);
          return;
        }
        var stage = cropState.stage;
        var dia = cropDiameter(stage);
        var scale = cropState.scale;
        var drawW = cropState.nw * scale;
        var drawH = cropState.nh * scale;
        var vw = stage.clientWidth;
        var vh = stage.clientHeight;
        var drawX = (vw / 2) + cropState.ox - (drawW / 2);
        var drawY = (vh / 2) + cropState.oy - (drawH / 2);
        var viewLeft = (vw / 2) - (dia / 2);
        var viewTop = (vh / 2) - (dia / 2);
        var sx = (viewLeft - drawX) / scale;
        var sy = (viewTop - drawY) / scale;
        var sSize = dia / scale;
        var canvas = document.createElement('canvas');
        canvas.width = OUTPUT_SIZE;
        canvas.height = OUTPUT_SIZE;
        var ctx = canvas.getContext('2d');
        if (!ctx) {
          done(null);
          return;
        }
        ctx.fillStyle = '#000';
        ctx.fillRect(0, 0, OUTPUT_SIZE, OUTPUT_SIZE);
        try {
          ctx.drawImage(cropState.img, sx, sy, sSize, sSize, 0, 0, OUTPUT_SIZE, OUTPUT_SIZE);
        } catch (err) {
          done(null);
          return;
        }
        canvas.toBlob(function (blob) {
          done(blob || null);
        }, 'image/jpeg', 0.92);
      }

      function openCropper(sourceUrl, intent, revokeUrl) {
        closeCropper();
        stopCamera();
        var root = document.createElement('div');
        root.id = 'cropRoot';
        root.className = 'crop-root';
        root.innerHTML =
          '<div class="crop-stage" id="cropStage">' +
            '<img class="crop-img" id="cropImg" alt="Crop photo" draggable="false">' +
            '<div class="crop-mask"><div class="crop-hole" id="cropHole"></div></div>' +
            '<div class="crop-hint">Move and zoom to fit the circle</div>' +
          '</div>' +
          '<div class="crop-zoom">' +
            '<button type="button" id="cropZoomOut" aria-label="Zoom out"><i class="fas fa-minus"></i></button>' +
            '<input type="range" id="cropZoomRange" aria-label="Zoom">' +
            '<button type="button" id="cropZoomIn" aria-label="Zoom in"><i class="fas fa-plus"></i></button>' +
          '</div>' +
          '<div class="crop-actions">' +
            '<button type="button" class="crop-btn" id="cropCancel">Cancel</button>' +
            '<button type="button" class="crop-btn is-done" id="cropDone">Done</button>' +
          '</div>';
        document.body.appendChild(root);

        var stage = document.getElementById('cropStage');
        var img = document.getElementById('cropImg');
        var hole = document.getElementById('cropHole');
        var zoomRange = document.getElementById('cropZoomRange');
        cropState = {
          intent: intent || 'upload',
          objectUrl: revokeUrl || null,
          stage: stage,
          img: img,
          hole: hole,
          zoomRange: zoomRange,
          nw: 1,
          nh: 1,
          scale: 1,
          minScale: 1,
          maxScale: 4,
          ox: 0,
          oy: 0,
          dragging: false,
          lastX: 0,
          lastY: 0,
          pinchStartDist: 0,
          pinchStartScale: 1
        };

        img.onload = function () {
          cropState.nw = img.naturalWidth || img.width || 1;
          cropState.nh = img.naturalHeight || img.height || 1;
          fitCropImage();
        };
        img.onerror = function () {
          showToast('Could not load the photo for cropping.');
          closeCropper();
        };
        img.src = sourceUrl;

        document.getElementById('cropCancel').addEventListener('click', function () {
          closeCropper();
        });
        document.getElementById('cropDone').addEventListener('click', function () {
          exportCroppedSquare(function (blob) {
            if (!blob) {
              showToast('Could not crop the photo.');
              return;
            }
            var file = new File([blob], 'profile-crop.jpg', { type: 'image/jpeg' });
            var intentOut = cropState.intent || 'upload';
            closeCropper();
            showToast('Updating photo…');
            uploadBlobOrFile(file, intentOut);
          });
        });
        document.getElementById('cropZoomIn').addEventListener('click', function () {
          cropState.scale = clamp(cropState.scale * 1.12, cropState.minScale, cropState.maxScale);
          constrainCropOffset();
          applyCropTransform();
        });
        document.getElementById('cropZoomOut').addEventListener('click', function () {
          cropState.scale = clamp(cropState.scale / 1.12, cropState.minScale, cropState.maxScale);
          constrainCropOffset();
          applyCropTransform();
        });
        zoomRange.addEventListener('input', function () {
          cropState.scale = clamp(parseFloat(zoomRange.value) || cropState.minScale, cropState.minScale, cropState.maxScale);
          constrainCropOffset();
          applyCropTransform();
        });

        stage.addEventListener('pointerdown', function (e) {
          if (e.pointerType === 'touch' && e.isPrimary === false) return;
          cropState.dragging = true;
          cropState.lastX = e.clientX;
          cropState.lastY = e.clientY;
          stage.classList.add('is-dragging');
          try { stage.setPointerCapture(e.pointerId); } catch (err) {}
        });
        stage.addEventListener('pointermove', function (e) {
          if (!cropState.dragging) return;
          var dx = e.clientX - cropState.lastX;
          var dy = e.clientY - cropState.lastY;
          cropState.lastX = e.clientX;
          cropState.lastY = e.clientY;
          cropState.ox += dx;
          cropState.oy += dy;
          constrainCropOffset();
          applyCropTransform();
        });
        function endDrag(e) {
          cropState.dragging = false;
          stage.classList.remove('is-dragging');
          try { stage.releasePointerCapture(e.pointerId); } catch (err) {}
        }
        stage.addEventListener('pointerup', endDrag);
        stage.addEventListener('pointercancel', endDrag);
        stage.addEventListener('wheel', function (e) {
          e.preventDefault();
          var factor = e.deltaY > 0 ? (1 / 1.08) : 1.08;
          cropState.scale = clamp(cropState.scale * factor, cropState.minScale, cropState.maxScale);
          constrainCropOffset();
          applyCropTransform();
        }, { passive: false });

        var pointers = {};
        stage.addEventListener('touchstart', function (e) {
          for (var i = 0; i < e.changedTouches.length; i++) {
            var t = e.changedTouches[i];
            pointers[t.identifier] = { x: t.clientX, y: t.clientY };
          }
          var ids = Object.keys(pointers);
          if (ids.length === 2) {
            var a = pointers[ids[0]];
            var b = pointers[ids[1]];
            cropState.pinchStartDist = Math.hypot(a.x - b.x, a.y - b.y) || 1;
            cropState.pinchStartScale = cropState.scale;
            cropState.dragging = false;
          }
        }, { passive: true });
        stage.addEventListener('touchmove', function (e) {
          for (var i = 0; i < e.changedTouches.length; i++) {
            var t = e.changedTouches[i];
            pointers[t.identifier] = { x: t.clientX, y: t.clientY };
          }
          var ids = Object.keys(pointers);
          if (ids.length === 2) {
            e.preventDefault();
            var a = pointers[ids[0]];
            var b = pointers[ids[1]];
            var dist = Math.hypot(a.x - b.x, a.y - b.y) || 1;
            cropState.scale = clamp(
              cropState.pinchStartScale * (dist / (cropState.pinchStartDist || 1)),
              cropState.minScale,
              cropState.maxScale
            );
            constrainCropOffset();
            applyCropTransform();
          }
        }, { passive: false });
        stage.addEventListener('touchend', function (e) {
          for (var i = 0; i < e.changedTouches.length; i++) {
            delete pointers[e.changedTouches[i].identifier];
          }
        }, { passive: true });
        stage.addEventListener('touchcancel', function (e) {
          for (var i = 0; i < e.changedTouches.length; i++) {
            delete pointers[e.changedTouches[i].identifier];
          }
        }, { passive: true });

        window.addEventListener('resize', fitCropImage);
      }

      function beginCropFromBlob(blob, intent) {
        if (!blob) {
          showToast('Could not load the photo.');
          return;
        }
        var url = URL.createObjectURL(blob);
        openCropper(url, intent, url);
      }

      function beginCropFromFile(file, intent) {
        if (!file) return;
        beginCropFromBlob(file, intent);
      }

      function bindInput(input, intent) {
        input.addEventListener('change', function () {
          var file = input.files && input.files[0];
          input.value = '';
          if (!file) return;
          beginCropFromFile(file, intent);
        });
      }

      function openGallery() {
        galleryFile.click();
      }

      function openCameraFallback() {
        cameraFile.click();
      }

      function openCamera() {
        if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
          openCameraFallback();
          return;
        }
        stopCamera();
        closeCropper();
        var root = document.createElement('div');
        root.id = 'camRoot';
        root.className = 'cam-root';
        root.innerHTML =
          '<video id="camVideo" class="cam-video" playsinline autoplay muted></video>' +
          '<div class="cam-actions">' +
            '<button type="button" class="cam-cancel" id="camCancel">Cancel</button>' +
            '<button type="button" class="cam-shutter" id="camCapture" aria-label="Take photo"><span></span></button>' +
            '<span style="width:64px"></span>' +
          '</div>';
        document.body.appendChild(root);
        var video = document.getElementById('camVideo');
        navigator.mediaDevices.getUserMedia({
          audio: false,
          video: { facingMode: { ideal: 'user' }, width: { ideal: 1280 }, height: { ideal: 1280 } }
        }).then(function (mediaStream) {
          stream = mediaStream;
          video.srcObject = mediaStream;
          var p = video.play();
          if (p && typeof p.catch === 'function') p.catch(function () {});
        }).catch(function () {
          stopCamera();
          openCameraFallback();
        });
        document.getElementById('camCancel').addEventListener('click', function () {
          stopCamera();
        });
        document.getElementById('camCapture').addEventListener('click', function () {
          try {
            var canvas = document.createElement('canvas');
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 640;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            canvas.toBlob(function (blob) {
              stopCamera();
              if (!blob) {
                showToast('Could not capture the photo.');
                return;
              }
              beginCropFromBlob(blob, 'change');
            }, 'image/jpeg', 0.92);
          } catch (e) {
            stopCamera();
            openCameraFallback();
          }
        });
      }

      bindInput(cameraFile, 'change');
      bindInput(galleryFile, 'upload');

      document.querySelectorAll('[data-action]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var action = btn.getAttribute('data-action') || '';
          if (action === 'remove') {
            if (!window.confirm('Remove the current profile photo?')) return;
            var body = new FormData();
            body.append('intent', 'remove');
            postForm(body);
            return;
          }
          if (action === 'change') {
            openCamera();
            return;
          }
          if (action === 'upload') {
            openGallery();
          }
        });
      });

      closeBtn.addEventListener('click', returnToProfile);

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
    })();
  </script>
</body>
</html>
