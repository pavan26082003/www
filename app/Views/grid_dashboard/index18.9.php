<?php
$schemaJson = json_encode(isset($schema) && is_array($schema) ? $schema : array(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$apiBase = isset($apiBase) ? rtrim((string) $apiBase, '/') : site_url('grid-dashboard/api');
$dashboardUrl = isset($dashboardUrl) ? (string) $dashboardUrl : site_url('dashboard');
$gridApiUrl = isset($gridApiUrl) ? (string) $gridApiUrl : '';
$proUrl = isset($professionalSpaceUrl) ? (string) $professionalSpaceUrl : '';
$circleUrl = isset($circleConnectUrl) ? (string) $circleConnectUrl : '';
$csrfName = isset($csrfTokenName) ? (string) $csrfTokenName : '';
$csrfHash = isset($csrfHash) ? (string) $csrfHash : '';
$content = isset($content) && is_array($content) ? $content : array();
$header = is_array($content['header'] ?? null) ? $content['header'] : array();
$featureCards = is_array($content['featureCards'] ?? null) ? $content['featureCards'] : array();
$stats = is_array($content['stats'] ?? null) ? $content['stats'] : array();
$logoUrl = (string) ($header['logoUrl'] ?? $content['logoUrl'] ?? 'https://efactor.tech/assets/images/GRID_Logo.png');
$avatarInitial = (string) ($header['avatarInitial'] ?? 'S');
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800;900&display=swap');
.grid-shell{height:calc(100vh - 72px);display:flex;flex-direction:column;background:linear-gradient(120deg,#efe9ff,#eef4fb 48%,#e4ecff);font-family:Inter,Arial,sans-serif;color:#1f2a44;overflow:hidden}
.grid-toolbar{min-height:60px;padding:10px 20px;display:flex;align-items:center;gap:12px;background:rgba(255,255,255,.92);border-bottom:1px solid rgba(148,163,184,.26);flex-shrink:0}
.grid-title-block{flex:1}
.grid-title-block span{display:block;color:#5b6cff;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.05em}
.grid-title-row{display:flex;align-items:center;gap:8px}
.grid-title-row img{height:22px}
.grid-title-block h1{margin:2px 0;font-size:20px}
.grid-title-block p{margin:0;color:#71819a;font-size:12px}
.grid-icon-btn{min-width:38px;height:38px;padding:0 12px;border:1px solid rgba(148,163,184,.26);border-radius:10px;background:#fff;color:#24304a;font-size:12px;font-weight:700;cursor:pointer}
.grid-workspace{flex:1;min-height:0;display:grid;grid-template-columns:minmax(230px,260px) minmax(380px,1fr) minmax(320px,440px);gap:18px;padding:18px 20px;overflow:hidden}
.grid-left,.grid-side{min-height:0;overflow:auto;padding:4px}
.grid-left{display:flex;flex-direction:column;gap:16px}
.grid-schema-btn{width:100%;min-height:46px;border:0;border-radius:14px;background:linear-gradient(135deg,#7c5cff,#6d28d9);color:#fff;font-size:14px;font-weight:900;cursor:pointer}
.grid-schema-btn:disabled{opacity:.6;cursor:wait}
.grid-panel{background:rgba(255,255,255,.86);border:1px solid rgba(255,255,255,.7);box-shadow:0 14px 38px rgba(31,42,68,.08);border-radius:16px;padding:16px}
.grid-panel h3,.grid-panel-label{margin:0 0 10px;font-size:12px;font-weight:900;letter-spacing:.04em;text-transform:uppercase;color:#5b6cff}
.grid-panel p{margin:0;color:#64748b;font-size:12px;line-height:1.45}
.grid-panel label{display:block;margin:12px 0 6px;font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#64748b}
.grid-panel input{width:100%;border:1px solid #dbe3ef;border-radius:10px;padding:10px 12px;font-size:12px;font-family:Consolas,monospace}
.grid-status{margin-top:10px;font-size:12px;color:#0f766e;min-height:16px}
.grid-phone-wrap{min-height:0;overflow:auto;display:flex;justify-content:center;padding:6px 0 24px}
.grid-phone{width:375px;border:11px solid #111827;border-radius:44px;background:#111827;box-shadow:0 26px 68px rgba(15,23,42,.34);overflow:hidden;flex-shrink:0}
.grid-phone-status{height:34px;padding:0 20px;display:flex;align-items:center;justify-content:space-between;color:#fff;font-size:12px;font-weight:700}
.grid-phone-screen{height:680px;overflow:auto;padding:8px 16px 28px;background:#f7f8fc}
.grid-phone-header{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:8px}
.grid-phone-header img{height:28px}
.grid-phone-actions{display:flex;gap:8px}
.grid-phone-chip{width:36px;height:36px;border-radius:999px;display:flex;align-items:center;justify-content:center;font-weight:800}
.grid-phone-sub{margin:0 0 14px;color:#6b7280;font-size:13px}
.grid-feature-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.grid-feature{min-height:210px;border-radius:24px;padding:16px;position:relative;color:#312e81;text-decoration:none;display:block}
.grid-feature h4{margin:48px 0 8px;font-size:18px}
.grid-feature p{margin:0;font-size:12px;line-height:1.4;opacity:.85}
.grid-feature .arrow{position:absolute;right:14px;bottom:14px;width:28px;height:28px;border-radius:999px;background:rgba(255,255,255,.55);display:flex;align-items:center;justify-content:center;font-size:14px}
.grid-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:16px;text-align:center}
.grid-stats strong{display:block;font-size:22px;color:#2e1065}
.grid-stats span{font-size:11px;color:#6b7280}
.grid-api-head{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px}
.grid-copy-btn{border:0;background:#eef2ff;color:#4338ca;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:800;cursor:pointer}
.grid-side code{display:block;font-size:11px;color:#334155;word-break:break-all}
.grid-side pre{margin:0;max-height:52vh;overflow:auto;font-size:12px;background:#0f172a;color:#e2e8f0;padding:14px;border-radius:12px}
@media (max-width:1100px){.grid-workspace{grid-template-columns:1fr;overflow:auto}.grid-shell{height:auto}}
</style>

<div id="grid-dashboard-root"
     data-api-base="<?= esc($apiBase) ?>"
     data-dashboard-url="<?= esc($dashboardUrl) ?>">
  <section class="grid-shell">
    <div class="grid-toolbar">
      <button type="button" class="grid-icon-btn" id="gridBackBtn">&larr; Back</button>
      <div class="grid-title-block">
        <span>Network Experience</span>
        <div class="grid-title-row">
          <img src="<?= esc($logoUrl) ?>" alt="GRID">
          <h1>GRID Dashboard</h1>
        </div>
        <p>Generate Grid SDUI schema and preview the mobile UI.</p>
      </div>
      <button type="button" class="grid-icon-btn" id="gridRefreshBtn">Refresh</button>
    </div>

    <div class="grid-workspace">
      <div class="grid-left">
        <button type="button" class="grid-schema-btn" id="gridGenerateBtn">Schema Generate</button>
        <div class="grid-panel">
          <h3>Feature Cards</h3>
          <p style="font-weight:800;color:#312e81;margin-bottom:8px">Open URL (in phone)</p>
          <label>Professional Space URL</label>
          <input id="gridProUrl" type="text" value="<?= esc($proUrl) ?>" placeholder="<?= esc(site_url('grid')) ?>">
          <label>Circle Connect URL</label>
          <input id="gridCircleUrl" type="text" value="<?= esc($circleUrl) ?>" placeholder="<?= esc(site_url('grid-chat')) ?>">
          <p style="margin-top:12px">Icons are built in (no image URLs). Paste a page URL, then click Schema Generate. Tap opens the URL inside the phone.</p>
          <div class="grid-status" id="gridStatus"></div>
        </div>
        <div class="grid-panel">
          <h3>Grid Studio</h3>
          <p><strong>Professional network page</strong><br>Click Schema Generate to rebuild the Grid schema from this profile. The app opens /grid and /grid-chat in the same session — no second login.</p>
        </div>
      </div>

      <div class="grid-phone-wrap">
        <div class="grid-phone">
          <div class="grid-phone-status"><span>9:41</span><span>LTE&nbsp;&nbsp;100%</span></div>
          <div class="grid-phone-screen" id="gridPhonePreview">
            <div class="grid-phone-header">
              <img src="<?= esc($logoUrl) ?>" alt="GRID">
              <div class="grid-phone-actions">
                <span class="grid-phone-chip" style="background:#eef2ff;color:#4338ca">Q</span>
                <span class="grid-phone-chip" style="background:<?= esc($header['avatarColor'] ?? '#7c3aed') ?>;color:#fff"><?= esc($avatarInitial) ?></span>
              </div>
            </div>
            <p class="grid-phone-sub"><?= esc($header['subtitle'] ?? 'Your professional network space') ?></p>
            <div class="grid-feature-row" id="gridFeaturePreview">
              <?php foreach ($featureCards as $card): if (!is_array($card)) continue; ?>
                <a class="grid-feature" href="<?= esc($card['url'] ?? $card['route'] ?? '#') ?>" target="_blank" rel="noopener"
                   style="background:linear-gradient(180deg,<?= esc($card['gradientStart'] ?? '#dce4ff') ?>,<?= esc($card['gradientEnd'] ?? '#b7c4fb') ?>);color:<?= esc($card['textColor'] ?? '#312e81') ?>">
                  <h4><?= esc($card['title'] ?? '') ?></h4>
                  <p><?= esc($card['subtitle'] ?? '') ?></p>
                  <span class="arrow">&rsaquo;</span>
                </a>
              <?php endforeach; ?>
            </div>
            <div class="grid-stats">
              <?php foreach ($stats as $stat): if (!is_array($stat)) continue; ?>
                <div>
                  <strong><?= esc($stat['value'] ?? 0) ?></strong>
                  <span><?= esc($stat['label'] ?? '') ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <aside class="grid-side">
        <div class="grid-panel">
          <div class="grid-api-head">
            <span class="grid-panel-label" style="margin:0">Schema API</span>
            <button type="button" class="grid-copy-btn" id="gridCopyApiBtn">Copy API</button>
          </div>
          <code id="gridSchemaApiUrl"><?= esc($gridApiUrl !== '' ? $gridApiUrl : $apiBase . '/schema') ?></code>
        </div>
        <div class="grid-panel" style="margin-top:14px">
          <div class="grid-api-head">
            <span class="grid-panel-label" style="margin:0">Schema JSON</span>
            <button type="button" class="grid-copy-btn" id="gridCopyJsonBtn">Copy JSON</button>
          </div>
          <pre id="gridSchemaOut"><?= esc($schemaJson) ?></pre>
        </div>
      </aside>
    </div>
  </section>
</div>
<script>
(function() {
    var btn = document.getElementById('gridGenerateBtn');
    var statusEl = document.getElementById('gridStatus');
    var out = document.getElementById('gridSchemaOut');
    var back = document.getElementById('gridBackBtn');
    var refresh = document.getElementById('gridRefreshBtn');
    var apiBase = <?= json_encode($apiBase) ?>;
    var dashboardUrl = <?= json_encode($dashboardUrl) ?>;
    var csrfName = <?= json_encode($csrfName) ?>;
    var csrfHash = <?= json_encode($csrfHash) ?>;

    if (back) back.addEventListener('click', function() { window.location.href = dashboardUrl; });
    if (refresh) refresh.addEventListener('click', function() { window.location.reload(); });

    function copyText(value, button) {
        if (!value) return;
        navigator.clipboard.writeText(value).then(function() {
            var prev = button.textContent;
            button.textContent = 'Copied';
            setTimeout(function() { button.textContent = prev; }, 1200);
        }).catch(function() {});
    }
    var copyApi = document.getElementById('gridCopyApiBtn');
    var copyJson = document.getElementById('gridCopyJsonBtn');
    if (copyApi) copyApi.addEventListener('click', function() {
        copyText((document.getElementById('gridSchemaApiUrl') || {}).textContent || '', copyApi);
    });
    if (copyJson) copyJson.addEventListener('click', function() {
        copyText((out && out.textContent) || '', copyJson);
    });

    function renderPreview(schema) {
        var content = (schema && schema.content) || {};
        var cards = Array.isArray(content.featureCards) ? content.featureCards : [];
        var row = document.getElementById('gridFeaturePreview');
        if (!row) return;
        row.innerHTML = cards.map(function(card) {
            var url = card.url || card.route || '#';
            var start = card.gradientStart || '#dce4ff';
            var end = card.gradientEnd || '#b7c4fb';
            var color = card.textColor || '#312e81';
            return '<a class="grid-feature" href="' + String(url).replace(/"/g, '&quot;') + '" target="_blank" rel="noopener" style="background:linear-gradient(180deg,' + start + ',' + end + ');color:' + color + '">' +
                '<h4>' + (card.title || '') + '</h4><p>' + (card.subtitle || '') + '</p><span class="arrow">&rsaquo;</span></a>';
        }).join('');
    }

    btn.addEventListener('click', async function() {
        btn.disabled = true;
        statusEl.textContent = 'Generating...';
        try {
            var payload = {
                professionalSpaceUrl: document.getElementById('gridProUrl').value,
                circleConnectUrl: document.getElementById('gridCircleUrl').value
            };
            if (csrfName && csrfHash) payload[csrfName] = csrfHash;
            var res = await fetch(apiBase + '/generate-schema', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            });
            var data = await res.json();
            out.textContent = JSON.stringify(data.schema || data, null, 2);
            if (data.professionalSpaceUrl) document.getElementById('gridProUrl').value = data.professionalSpaceUrl;
            if (data.circleConnectUrl) document.getElementById('gridCircleUrl').value = data.circleConnectUrl;
            renderPreview(data.schema || {});
            statusEl.textContent = data.message || (data.success ? 'Saved' : 'Failed');
        } catch (err) {
            statusEl.textContent = err.message || 'Generate failed';
        } finally {
            btn.disabled = false;
        }
    });
})();
</script>
