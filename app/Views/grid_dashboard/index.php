<?php
$savedSchema = isset($schema) && is_array($schema) ? $schema : array();
$savedContent = isset($content) && is_array($content) ? $content : array();
$proUrlValue = isset($professionalSpaceUrl) ? trim((string) $professionalSpaceUrl) : '';
$circleUrlValue = isset($circleConnectUrl) ? trim((string) $circleConnectUrl) : '';
if (
    ($proUrlValue === '' || $circleUrlValue === '')
    && !empty($savedContent['featureCards'])
    && is_array($savedContent['featureCards'])
) {
    foreach ($savedContent['featureCards'] as $card) {
        if (!is_array($card)) {
            continue;
        }
        $cardId = (string) ($card['id'] ?? '');
        $cardUrl = trim((string) ($card['url'] ?? $card['route'] ?? ''));
        if ($cardId === 'professional-space' && $proUrlValue === '') {
            $proUrlValue = $cardUrl;
        }
        if ($cardId === 'circle-connect' && $circleUrlValue === '') {
            $circleUrlValue = $cardUrl;
        }
    }
}
if (class_exists('\App\Libraries\GridWebSso')) {
    $proUrlValue = \App\Libraries\GridWebSso::resolveCardUrl('professional-space', $proUrlValue);
    $circleUrlValue = \App\Libraries\GridWebSso::resolveCardUrl('circle-connect', $circleUrlValue);
}
$gridApi = isset($gridApiUrl) ? (string) $gridApiUrl : '';
$gridApiBase = isset($gridApiBase) ? rtrim((string) $gridApiBase, '/') : '';
if ($gridApi === '' && $gridApiBase !== '') {
    $gridApi = $gridApiBase . '?action=get_grid&user_id=' . (int) ($userId ?? 0);
}
$schemaJson = !empty($savedSchema)
    ? json_encode($savedSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    : '';
$ahpCss = base_url('assets/css/app_home_page.css');
$gridSduiJs = base_url('assets/js/app_home_grid_sdui.js');
$wwwRoot = rtrim(str_replace('\\', '/', defined('FCPATH') ? FCPATH : dirname(__DIR__, 3)), '/');
$cssPath = $wwwRoot . '/assets/css/app_home_page.css';
$jsPath = $wwwRoot . '/assets/js/app_home_grid_sdui.js';
if (is_readable($cssPath)) {
    $ahpCss .= '?v=' . filemtime($cssPath);
}
if (is_readable($jsPath)) {
    $gridSduiJs .= '?v=' . filemtime($jsPath);
}
?>
<link rel="stylesheet" href="<?= htmlspecialchars($ahpCss, ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800;900&display=swap');
:root{
  --gd-ink:#1f2a44;
  --gd-muted:#71819a;
  --gd-line:rgba(148,163,184,.26);
  --gd-accent:#7c3aed;
  --gd-accent-2:#5b6cff;
}
.gd-shell{
  height:calc(100vh - 72px);
  display:flex;flex-direction:column;
  background:linear-gradient(120deg,#f3e8ff,#eef4fb 48%,#dbeafe);
  font-family:Inter,Arial,sans-serif;
  color:var(--gd-ink);overflow:hidden;
}
.gd-toolbar{
  min-height:60px;padding:10px 20px;
  display:flex;align-items:center;gap:12px;
  background:rgba(255,255,255,.92);
  border-bottom:1px solid var(--gd-line);
  flex-shrink:0;
}
.gd-title-block{flex:1}
.gd-title-block span,.gd-panel-label{
  display:block;color:var(--gd-accent);
  font-size:11px;font-weight:900;
  text-transform:uppercase;letter-spacing:.05em;
}
.gd-title-block h1{margin:2px 0;font-size:20px}
.gd-title-block p{margin:0;color:var(--gd-muted);font-size:12px}
.gd-icon-btn{
  min-width:38px;height:38px;padding:0 12px;
  border:1px solid var(--gd-line);border-radius:10px;
  background:#fff;color:#24304a;font-size:12px;font-weight:700;cursor:pointer;
}
.gd-icon-btn:hover{background:#f8fafc}
.gd-workspace{
  flex:1;min-height:0;
  display:grid;
  grid-template-columns:minmax(210px,240px) minmax(420px,1fr) minmax(320px,480px);
  gap:18px;padding:18px 20px;overflow:hidden;
}
.gd-left-panel,.gd-side-panel{min-height:0;overflow:auto;padding:4px}
.gd-left-panel{display:flex;flex-direction:column;gap:18px}
.gd-schema-btn{
  width:100%;min-height:44px;border:0;border-radius:12px;
  background:linear-gradient(135deg,#7c3aed,#5b6cff);
  color:#fff;font-size:13px;font-weight:900;cursor:pointer;
}
.gd-schema-btn:disabled{opacity:.6;cursor:wait}
.gd-glass{
  background:rgba(255,255,255,.82);
  border:1px solid rgba(255,255,255,.7);
  box-shadow:0 14px 38px rgba(31,42,68,.09);
  backdrop-filter:blur(18px);
  padding:14px;border-radius:14px;
}
.gd-glass strong{display:block;font-size:15px;margin:6px 0}
.gd-glass p{margin:0;color:var(--gd-muted);font-size:12px;line-height:1.45}
.gd-phone-wrap{
  min-height:0;overflow:auto;
  display:flex;justify-content:center;align-items:flex-start;
  padding:6px 0 24px;
}
.gd-phone{
  position:relative;width:375px;
  border:11px solid #111827;border-radius:44px;
  background:#111827;
  box-shadow:0 26px 68px rgba(15,23,42,.34),inset 0 0 0 1px rgba(255,255,255,.06);
  overflow:hidden;flex-shrink:0;
}
.gd-phone::before{
  content:"";position:absolute;z-index:10;
  top:8px;left:50%;transform:translateX(-50%);
  width:86px;height:20px;border-radius:999px;background:#111827;
}
.gd-phone-status{
  position:relative;z-index:11;height:34px;padding:0 20px;
  display:flex;align-items:center;justify-content:space-between;
  color:#fff;font-size:12px;font-weight:700;
}
.gd-phone-screen{
  position:relative;
  height:680px;overflow-y:auto;overflow-x:hidden;
  background:#f7f8fc;
  overscroll-behavior:contain;
}
.gd-side-panel{display:flex;flex-direction:column;gap:12px}
.gd-api-head{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px}
.gd-copy-btn{
  border:1px solid var(--gd-line);border-radius:8px;background:#fff;
  color:#334155;font-size:11px;font-weight:700;padding:4px 8px;cursor:pointer;
}
.gd-side-panel code{
  display:block;font-size:11px;line-height:1.4;word-break:break-all;
  color:#334155;background:#f8fafc;border-radius:8px;padding:8px;
  border:1px solid var(--gd-line);
}
.gd-schema-output{
  flex:1;min-height:280px;width:100%;
  border:1px solid var(--gd-line);border-radius:14px;
  background:#0f172a;color:#e2e8f0;
  font-family:Consolas,Monaco,monospace;font-size:11px;line-height:1.45;
  padding:12px;resize:vertical;
}
.gd-toast{
  position:fixed;right:18px;bottom:18px;z-index:9999;
  background:#111827;color:#fff;padding:10px 14px;border-radius:10px;
  font-size:12px;font-weight:700;display:none;
  box-shadow:0 12px 30px rgba(15,23,42,.28);
}
.gd-toast.is-on{display:block}
.gd-field{display:flex;flex-direction:column;gap:6px;margin-top:10px}
.gd-field label{font-size:10px;font-weight:900;color:var(--gd-accent);text-transform:uppercase;letter-spacing:.05em}
.gd-field input{
  width:100%;min-height:38px;padding:8px 10px;
  border:1px solid var(--gd-line);border-radius:10px;
  background:#fff;color:var(--gd-ink);font-size:12px;
}
.gd-field input:focus{outline:2px solid rgba(124,58,237,.25);border-color:rgba(124,58,237,.45)}
.gd-field small{font-size:10px;color:var(--gd-muted);line-height:1.35}
.gd-phone{position:relative}
.gd-phone-browser{
  position:absolute;inset:0;z-index:20;
  display:none;flex-direction:column;background:#fff;
}
.gd-phone-browser.is-open{display:flex}
.gd-phone-browser-bar{
  flex:0 0 auto;display:flex;align-items:center;gap:8px;
  padding:8px 10px;background:#f8fafc;border-bottom:1px solid #e2e8f0;
}
.gd-phone-browser-bar button{
  border:0;border-radius:8px;background:#e2e8f0;color:#1e293b;
  font-size:11px;font-weight:800;padding:6px 8px;cursor:pointer;
}
.gd-phone-browser-title{
  flex:1;min-width:0;font-size:11px;font-weight:700;color:#475569;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.gd-phone-browser iframe{
  flex:1 1 auto;width:100%;min-height:0;border:0;background:#fff;display:block;
}
.gd-phone-browser-fallback{display:none!important}
@media (max-width:1100px){
  .gd-workspace{grid-template-columns:1fr;overflow:auto}
  .gd-phone-wrap{order:1}
  .gd-left-panel{order:0}
  .gd-side-panel{order:2;min-height:360px}
}
</style>

<div id="grid-dashboard-root"
     data-api-base="<?= htmlspecialchars($apiBase ?? '', ENT_QUOTES, 'UTF-8') ?>"
     data-dashboard-url="<?= htmlspecialchars($dashboardUrl ?? '', ENT_QUOTES, 'UTF-8') ?>"
     data-grid-api-url="<?= htmlspecialchars($gridApi, ENT_QUOTES, 'UTF-8') ?>"
     data-user-id="<?= (int) ($userId ?? 0) ?>"
     data-csrf-name="<?= htmlspecialchars($csrfTokenName ?? '', ENT_QUOTES, 'UTF-8') ?>"
     data-csrf-hash="<?= htmlspecialchars($csrfHash ?? '', ENT_QUOTES, 'UTF-8') ?>">
  <section class="gd-shell">
    <div class="gd-toolbar">
      <button type="button" class="gd-icon-btn" id="gdBackBtn">&#8592; Back</button>
      <div class="gd-title-block">
        <span>Network Experience</span>
        <h1 style="display:flex;align-items:center;gap:10px;margin:2px 0">
          <img src="https://efactor.tech/assets/images/GRID_Logo.png" alt="GRID" style="height:28px;width:auto;display:block">
          <span>Grid Dashboard</span>
        </h1>
        <p>Generate Grid SDUI schema and preview the mobile UI.</p>
      </div>
      <button type="button" class="gd-icon-btn" id="gdRefreshBtn">&#8635; Refresh</button>
    </div>

    <div class="gd-workspace">
      <div class="gd-left-panel">
        <button type="button" class="gd-schema-btn" id="gdGenerateSchemaBtn">Schema Generate</button>
        <div class="gd-glass">
          <span class="gd-panel-label">Feature cards</span>
          <strong>Open URL (in phone)</strong>
          <div class="gd-field">
            <label for="gdProfessionalSpaceUrl">Professional Space URL</label>
            <input type="url" id="gdProfessionalSpaceUrl" placeholder="<?= htmlspecialchars(site_url('grid'), ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($proUrlValue, ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" spellcheck="false">
          </div>
          <div class="gd-field">
            <label for="gdCircleConnectUrl">Circle Connect URL</label>
            <input type="url" id="gdCircleConnectUrl" placeholder="<?= htmlspecialchars(site_url('grid-chat'), ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($circleUrlValue, ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" spellcheck="false">
          </div>
          <small>Icons are built in (no image URLs). These open /grid and /grid-chat in the same app session (no second login). Click Schema Generate, then tap a card inside the phone.</small>
        </div>
        <div class="gd-glass" id="gdInfoPanel">
          <span class="gd-panel-label">Grid Studio</span>
          <strong>Professional network page</strong>
          <p>Click <b>Schema Generate</b> to rebuild the Grid schema from this profile. The phone preview and schema panel update together. Taps open /grid and /grid-chat without asking login again.</p>
        </div>
        <div class="gd-glass">
          <span class="gd-panel-label">Public API</span>
          <strong>get_grid</strong>
          <p>Flutter / app should fetch the Grid schema the same way IoT uses <code>get_iot_schema</code>.</p>
        </div>
      </div>

      <div class="gd-phone-wrap">
        <div class="gd-phone">
          <div class="gd-phone-status"><span>9:41</span><span>LTE&nbsp;&nbsp;100%</span></div>
          <div class="gd-phone-screen">
            <div id="gdPreviewRoot" class="ahp-grid-network-root" aria-live="polite"></div>
            <div class="gd-phone-browser" id="gdPhoneBrowser" aria-hidden="true">
              <div class="gd-phone-browser-bar">
                <button type="button" id="gdBrowserBackBtn">&#8592; Back</button>
                <span class="gd-phone-browser-title" id="gdBrowserTitle">Loading…</span>
              </div>
              <iframe id="gdBrowserFrame" title="In-app browser" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>

      <aside class="gd-side-panel">
        <div class="gd-glass">
          <div class="gd-api-head">
            <span class="gd-panel-label">Schema API</span>
            <button type="button" class="gd-copy-btn" id="gdCopySchemaApiBtn">Copy API</button>
          </div>
          <code id="gdSchemaApiUrl"><?= htmlspecialchars($gridApi, ENT_QUOTES, 'UTF-8') ?></code>
        </div>
        <div class="gd-glass" style="flex:1;display:flex;flex-direction:column;min-height:0">
          <div class="gd-api-head">
            <span class="gd-panel-label">Schema JSON</span>
            <button type="button" class="gd-copy-btn" id="gdCopySchemaJsonBtn">Copy JSON</button>
          </div>
          <textarea id="gdSchemaOutput" class="gd-schema-output" readonly spellcheck="false"><?= htmlspecialchars($schemaJson, ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>
      </aside>
    </div>
  </section>
</div>
<div class="gd-toast" id="gdToast" role="status"></div>

<script>
window.GRID_DASHBOARD_BOOT = {
  schema: <?= json_encode($savedSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  content: <?= json_encode($savedContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  gridApiUrl: <?= json_encode($gridApi, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  professionalSpaceUrl: <?= json_encode($proUrlValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  circleConnectUrl: <?= json_encode($circleUrlValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  userId: <?= (int) ($userId ?? 0) ?>
};
</script>
<script src="<?= htmlspecialchars($gridSduiJs, ENT_QUOTES, 'UTF-8') ?>"></script>
<script>
(function () {
  'use strict';
  var root = document.getElementById('grid-dashboard-root');
  if (!root) return;

  var apiBase = String(root.getAttribute('data-api-base') || '').replace(/\/+$/, '');
  var dashboardUrl = String(root.getAttribute('data-dashboard-url') || '/');
  var gridApiUrl = String(root.getAttribute('data-grid-api-url') || '');
  var userId = parseInt(root.getAttribute('data-user-id') || '0', 10) || 0;
  var csrfName = String(root.getAttribute('data-csrf-name') || '');
  var csrfHash = String(root.getAttribute('data-csrf-hash') || '');
  var boot = window.GRID_DASHBOARD_BOOT || {};
  var currentSchema = boot.schema || null;
  var currentBrowserUrl = '';

  function toast(msg) {
    var el = document.getElementById('gdToast');
    if (!el) return;
    el.textContent = msg;
    el.classList.add('is-on');
    clearTimeout(toast._t);
    toast._t = setTimeout(function () { el.classList.remove('is-on'); }, 2200);
  }

  function apiHeaders() {
    var h = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    };
    if (csrfHash) h['X-CSRF-TOKEN'] = csrfHash;
    return h;
  }

  function apiJsonBody(extra) {
    var body = extra && typeof extra === 'object' ? Object.assign({}, extra) : {};
    if (csrfName && csrfHash) body[csrfName] = csrfHash;
    return JSON.stringify(body);
  }

  function getUrlInputs() {
    return {
      professionalSpaceUrl: String((document.getElementById('gdProfessionalSpaceUrl') || {}).value || '').trim(),
      circleConnectUrl: String((document.getElementById('gdCircleConnectUrl') || {}).value || '').trim()
    };
  }

  function stripSso(url) {
    url = String(url || '').trim();
    if (!url) return '';
    try {
      var parsed = new URL(url, window.location.origin);
      parsed.searchParams.delete('grid_sso');
      return parsed.toString();
    } catch (e) {
      return url.replace(/([?&])grid_sso=[^&]*&?/g, '$1').replace(/[?&]$/, '');
    }
  }

  function setUrlInputs(proUrl, circleUrl) {
    var pro = document.getElementById('gdProfessionalSpaceUrl');
    var circle = document.getElementById('gdCircleConnectUrl');
    if (pro && proUrl != null) pro.value = stripSso(proUrl);
    if (circle && circleUrl != null) circle.value = stripSso(circleUrl);
  }

  function setSchemaOutput(schema) {
    var out = document.getElementById('gdSchemaOutput');
    if (!out) return;
    out.value = schema ? JSON.stringify(schema, null, 2) : '';
  }

  function normalizeUrl(url) {
    url = String(url || '').trim();
    if (!url) return '';
    if (!/^https?:\/\//i.test(url)) url = 'https://' + url;
    return url;
  }

  function closeInPhoneBrowser() {
    var browser = document.getElementById('gdPhoneBrowser');
    var frame = document.getElementById('gdBrowserFrame');
    if (browser) {
      browser.classList.remove('is-open');
      browser.setAttribute('aria-hidden', 'true');
    }
    if (frame) frame.src = 'about:blank';
    currentBrowserUrl = '';
  }

  function openInPhoneBrowser(url, title) {
    url = normalizeUrl(url);
    if (!url) {
      toast('Set a URL first, then Schema Generate');
      return;
    }
    currentBrowserUrl = url;
    var browser = document.getElementById('gdPhoneBrowser');
    var frame = document.getElementById('gdBrowserFrame');
    var titleEl = document.getElementById('gdBrowserTitle');
    if (!browser || !frame) {
      toast('In-app preview unavailable');
      return;
    }
    if (titleEl) titleEl.textContent = title || url;
    browser.classList.add('is-open');
    browser.setAttribute('aria-hidden', 'false');
    frame.style.display = 'block';
    frame.src = 'about:blank';
    setTimeout(function () {
      frame.src = url;
    }, 30);
  }

  function resolveFeatureUrl(actionId, schema) {
    var schemaUrl = resolveSchemaActionUrl(schema, actionId);
    var urls = getUrlInputs();
    if (actionId === 'grid-open-professional-space') {
      return schemaUrl || urls.professionalSpaceUrl;
    }
    if (actionId === 'grid-open-circle-connect') {
      return schemaUrl || urls.circleConnectUrl;
    }
    return schemaUrl;
  }

  function resolveSchemaActionUrl(schema, actionId) {
    var actions = (schema && schema.actions) || {};
    var action = actions[actionId] || {};
    return action.url || (action.destination && action.destination.url) || action.route || '';
  }

  function renderPreview(payload) {
    var mount = document.getElementById('gdPreviewRoot');
    var api = window.AHP_GRID_SDUI;
    if (!mount) return;
    if (!api || typeof api.render !== 'function') {
      mount.innerHTML = '<div style="padding:24px;color:#6b7280;font:600 13px Inter,sans-serif">Grid preview renderer missing. Deploy assets/js/app_home_grid_sdui.js</div>';
      return;
    }
    var data = payload && payload.schema
      ? payload
      : { success: true, schema: payload, content: (payload && payload.content) || boot.content || {} };
    api.render(data, mount, {
      gridApiUrl: gridApiUrl,
      userId: userId,
      onAction: function (actionId, meta) {
        meta = meta || {};
        var url = meta.url || resolveFeatureUrl(actionId, currentSchema);
        if (url && (actionId === 'grid-open-professional-space' || actionId === 'grid-open-circle-connect' || meta.openUrl)) {
          openInPhoneBrowser(url, actionId === 'grid-open-circle-connect' ? 'Circle Connect' : 'Professional Space');
          return;
        }
        if (actionId) toast('Action: ' + String(actionId).replace(/^grid-/, '').replace(/-/g, ' '));
      }
    });
  }

  function applyPayload(payload) {
    if (!payload || !payload.schema) return;
    currentSchema = payload.schema;
    if (payload.gridApiUrl) {
      gridApiUrl = payload.gridApiUrl;
      var urlEl = document.getElementById('gdSchemaApiUrl');
      if (urlEl) urlEl.textContent = gridApiUrl;
      root.setAttribute('data-grid-api-url', gridApiUrl);
    }
    if (payload.professionalSpaceUrl != null || payload.circleConnectUrl != null) {
      setUrlInputs(
        payload.professionalSpaceUrl || '',
        payload.circleConnectUrl || ''
      );
    }
    setSchemaOutput(currentSchema);
    closeInPhoneBrowser();
    renderPreview(payload);
  }

  function generateSchema() {
    var btn = document.getElementById('gdGenerateSchemaBtn');
    var urls = getUrlInputs();
    if (btn) {
      btn.disabled = true;
      btn.textContent = 'Generating…';
    }
    return fetch(apiBase + '/generate-schema', {
      method: 'POST',
      credentials: 'same-origin',
      headers: apiHeaders(),
      body: apiJsonBody(urls)
    }).then(function (res) {
      return res.json().then(function (data) {
        if (!res.ok || !data || data.success === false) {
          throw new Error((data && data.message) || ('HTTP ' + res.status));
        }
        return data;
      });
    }).then(function (data) {
      applyPayload(data);
      toast(data.message || 'Grid schema generated');
    }).catch(function (err) {
      toast(err && err.message ? err.message : 'Generate failed');
    }).finally(function () {
      if (btn) {
        btn.disabled = false;
        btn.textContent = 'Schema Generate';
      }
    });
  }

  function refreshSchema() {
    return fetch(apiBase + '/schema', {
      method: 'GET',
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    }).then(function (res) {
      return res.json().then(function (data) {
        if (!res.ok || !data || data.success === false) {
          throw new Error((data && data.message) || ('HTTP ' + res.status));
        }
        return data;
      });
    }).then(function (data) {
      applyPayload(data);
      toast('Schema refreshed');
    }).catch(function (err) {
      toast(err && err.message ? err.message : 'Refresh failed');
    });
  }

  function copyText(text, okMsg) {
    if (!text) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(function () { toast(okMsg || 'Copied'); });
      return;
    }
    var ta = document.createElement('textarea');
    ta.value = text;
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); toast(okMsg || 'Copied'); } catch (e) {}
    document.body.removeChild(ta);
  }

  var backBtn = document.getElementById('gdBackBtn');
  if (backBtn) backBtn.addEventListener('click', function () { window.location.href = dashboardUrl; });
  var refreshBtn = document.getElementById('gdRefreshBtn');
  if (refreshBtn) refreshBtn.addEventListener('click', refreshSchema);
  var genBtn = document.getElementById('gdGenerateSchemaBtn');
  if (genBtn) genBtn.addEventListener('click', generateSchema);
  var copyApi = document.getElementById('gdCopySchemaApiBtn');
  if (copyApi) copyApi.addEventListener('click', function () {
    copyText(document.getElementById('gdSchemaApiUrl').textContent, 'API URL copied');
  });
  var copyJson = document.getElementById('gdCopySchemaJsonBtn');
  if (copyJson) copyJson.addEventListener('click', function () {
    copyText(document.getElementById('gdSchemaOutput').value, 'Schema JSON copied');
  });
  var browserBack = document.getElementById('gdBrowserBackBtn');
  if (browserBack) browserBack.addEventListener('click', closeInPhoneBrowser);

  if (currentSchema) {
    applyPayload({
      success: true,
      schema: currentSchema,
      content: boot.content || currentSchema.content || {},
      gridApiUrl: gridApiUrl,
      professionalSpaceUrl: boot.professionalSpaceUrl || '',
      circleConnectUrl: boot.circleConnectUrl || ''
    });
  } else {
    generateSchema();
  }
})();
</script>
