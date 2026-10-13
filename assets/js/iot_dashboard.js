(function () {
  'use strict';

  var root = document.getElementById('iot-dashboard-root');
  if (!root) return;

  var apiBase     = root.getAttribute('data-api-base') || '';
  var grid        = document.getElementById('iotDeviceGrid');
  var output      = document.getElementById('iotSchemaOutput');
  var summary     = document.getElementById('iotSummaryText');
  var detailPanel = document.getElementById('iotDetailPanel');

  var boot    = window.IOT_DASHBOARD_BOOT || {};
  var devices = Array.isArray(boot.devices) ? boot.devices : [];

  /* ─── tiny HTML escape ─────────────────────────────────────── */
  function esc(v) {
    return String(v == null ? '' : v)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;')
      .replace(/'/g,'&#039;');
  }

  /* ─── normalise one device object ─────────────────────────── */
  function parse(d) {
    return {
      productName  : d.product_name  || d.productName  || d.name         || 'Device',
      deviceType   : d.devicetype    || d.device_type  || d.productName  || '',
      brandName    : d.brandName     || d.brand_name   || d.vendor_name  || 'Efactor',
      brandLogo    : d.brandLogo     || d.brand_logo   || '',
      productImage : d.productImage  || d.product_image || '',
      nodeId       : d.node_id       || d.id            || '',
      status       : d.status        || (d.online ? 'Online' : 'Offline'),
      online       : !!d.online,
      modelName    : d.modelName     || d.model_name    || '',
      modelNumber  : d.modelNumber   || d.model_number  || '',
    };
  }

  /* ─── brand logo element ───────────────────────────────────── */
  function brandLogoEl(logo, name) {
    var initial = esc((name || 'E').charAt(0).toUpperCase());
    if (logo) {
      return (
        '<img class="iot-logo" src="' + esc(logo) + '"'
      + ' alt="' + esc(name) + ' logo" loading="lazy"'
      + ' onerror="this.outerHTML=\'<span class=\\\"iot-brand-placeholder\\\">' + initial + '</span>\'">'
      );
    }
    return '<span class="iot-brand-placeholder">' + initial + '</span>';
  }

  /* ─── product image element ────────────────────────────────── */
  function productImgEl(url, name) {
    var placeholder =
      '<div class="iot-product-empty">'
    +   '<svg width="26" height="26" fill="none" viewBox="0 0 24 24" aria-hidden="true">'
    +     '<rect x="3" y="5" width="18" height="14" rx="2" stroke="#94a3b8" stroke-width="1.5"/>'
    +     '<circle cx="8.5" cy="10.5" r="1.5" fill="#94a3b8"/>'
    +     '<path d="m5 17 4-4 3 3 2-2 5 5" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round"/>'
    +   '</svg>'
    +   'No Image'
    + '</div>';

    var inner = url
      ? '<img class="iot-product-image" src="' + esc(url) + '"'
      +   ' alt="' + esc(name) + '" loading="lazy"'
      +   ' onerror="this.outerHTML=\'' + placeholder.replace(/'/g, "\\'") + '\'">'
      : placeholder;

    return '<div class="iot-product-wrap">' + inner + '</div>';
  }

  /* ─── build one card ───────────────────────────────────────── */
  function buildCard(d, i) {
    var p   = parse(d);
    var cls = p.online ? ' is-online' : ' is-offline';

    // show deviceType line only if it adds information
    var typeLine = (p.deviceType && p.deviceType !== p.productName)
      ? '<span>' + esc(p.deviceType) + '</span>'
      : '';

    return (
      '<article class="iot-device-card' + cls + '" tabindex="0"'
    + ' data-device-index="' + i + '"'
    + ' data-node-id="' + esc(p.nodeId) + '"'
    + ' role="button"'
    + ' aria-label="' + esc(p.productName + ' — ' + p.status) + '">'

    /* 1 ── brand strip */
    + '<div class="iot-brand-row">'
    +   brandLogoEl(p.brandLogo, p.brandName)
    +   '<span class="iot-brand-name">' + esc(p.brandName) + '</span>'
    + '</div>'

    /* 2 ── product image */
    + productImgEl(p.productImage, p.productName)

    /* 3 ── name + meta (no node ID — too long/ugly in card) */
    + '<div class="iot-card-body">'
    +   '<h3 class="iot-device-name">' + esc(p.productName) + '</h3>'
    +   (typeLine ? '<div class="iot-device-meta">' + typeLine + '</div>' : '')
    + '</div>'

    /* 4 ── footer */
    + '<div class="iot-card-footer">'
    +   '<button type="button" class="iot-power"'
    +     (p.online ? '' : ' disabled')
    +     ' title="' + esc(p.online ? 'Power control' : 'Device offline') + '"'
    +     ' aria-label="' + esc(p.online ? 'Toggle power – ' + p.productName : p.productName + ' is offline') + '">'
    +   '</button>'
    +   '<span class="iot-status-pill">' + esc(p.status) + '</span>'
    + '</div>'

    + '</article>'
    );
  }

  /* ─── render whole grid ────────────────────────────────────── */
  function render() {
    if (!grid) return;

    if (!devices.length) {
      grid.innerHTML =
        '<div class="iot-empty-state">'
      + '<strong>No devices found</strong><br>'
      + 'Devices will appear here when this profile has records in efactorproduct.'
      + '</div>';
      if (summary) summary.textContent = '0 products ready';
      return;
    }

    var onlineCount = devices.filter(function (d) { return !!d.online; }).length;
    if (summary) summary.textContent = devices.length + ' products, ' + onlineCount + ' online';

    grid.innerHTML = devices.map(buildCard).join('');
  }

  /* ─── detail panel on card select ─────────────────────────── */
  function showDetail(d) {
    if (!detailPanel || !d) return;
    var p = parse(d);

    var logoHtml = p.brandLogo
      ? '<img class="iot-detail-brand-logo" src="' + esc(p.brandLogo) + '" alt="' + esc(p.brandName) + ' logo" loading="lazy">'
      : '<span class="iot-detail-brand-logo iot-brand-placeholder">' + esc(p.brandName.charAt(0).toUpperCase()) + '</span>';

    var imgHtml = p.productImage
      ? '<img class="iot-detail-product-img" src="' + esc(p.productImage) + '" alt="' + esc(p.productName) + '" loading="lazy">'
      : '';

    detailPanel.innerHTML =
      '<span class="iot-panel-label">Selected Device</span>'
    + '<div class="iot-detail-brand-row">' + logoHtml + '<span class="iot-detail-brand-name">' + esc(p.brandName) + '</span></div>'
    + imgHtml
    + '<strong>' + esc(p.productName) + '</strong>'
    + '<span class="iot-detail-status ' + (p.online ? 'online' : 'offline') + '">' + esc(p.status) + '</span>'
    + '<dl style="margin:8px 0 0">'
    + (p.nodeId     ? '<div class="iot-detail-row"><dt>Node ID</dt><dd>' + esc(p.nodeId)     + '</dd></div>' : '')
    + (p.deviceType && p.deviceType !== p.productName
                    ? '<div class="iot-detail-row"><dt>Type</dt><dd>'    + esc(p.deviceType) + '</dd></div>' : '')
    + (p.modelName  ? '<div class="iot-detail-row"><dt>Model</dt><dd>'  + esc(p.modelName)  + '</dd></div>' : '')
    + (p.modelNumber? '<div class="iot-detail-row"><dt>Model #</dt><dd>'+ esc(p.modelNumber)+ '</dd></div>' : '')
    + '</dl>';
  }

  /* ─── refresh ──────────────────────────────────────────────── */
  function refresh() {
    if (!apiBase) return;
    fetch(apiBase + '/devices', { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data && data.success && Array.isArray(data.devices)) {
          devices = data.devices;
          render();
        }
      });
  }

  /* ─── generate schema ──────────────────────────────────────── */
  function generateSchema() {
    var btn = document.getElementById('iotGenerateSchemaBtn');
    if (!apiBase || (btn && btn.disabled)) return;
    if (btn) btn.disabled = true;
    fetch(apiBase + '/generate-schema', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({})
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data && data.schema && output) output.value = JSON.stringify(data.schema, null, 2);
        alert((data && data.message) || 'Schema generated.');
      })
      .catch(function () { alert('Could not generate schema.'); })
      .finally(function () { if (btn) btn.disabled = false; });
  }

  /* ─── wire events ──────────────────────────────────────────── */
  var schemaBtn  = document.getElementById('iotGenerateSchemaBtn');
  var refreshBtn = document.getElementById('iotRefreshBtn');
  var backBtn    = document.getElementById('iotBackBtn');

  if (schemaBtn)  schemaBtn.addEventListener('click', generateSchema);
  if (refreshBtn) refreshBtn.addEventListener('click', refresh);
  if (backBtn) backBtn.addEventListener('click', function () {
    window.location.href = root.getAttribute('data-dashboard-url') || './';
  });

  /* card clicks */
  if (grid) {
    grid.addEventListener('click', function (e) {
      var powerBtn = e.target.closest('.iot-power');
      var card     = e.target.closest('.iot-device-card');
      if (!card) return;

      var d = devices[Number(card.getAttribute('data-device-index'))] || null;

      if (powerBtn) {
        e.stopPropagation();
        if (!d || !d.online) return;
        document.dispatchEvent(new CustomEvent('iot:power-click', {
          detail: { node_id: card.getAttribute('data-node-id') || '' }
        }));
        return;
      }

      grid.querySelectorAll('.iot-device-card').forEach(function (c) {
        c.classList.remove('is-selected');
      });
      card.classList.add('is-selected');
      showDetail(d);

      document.dispatchEvent(new CustomEvent('iot:device-card-click', {
        detail: { node_id: card.getAttribute('data-node-id') || '' }
      }));
    });

    grid.addEventListener('keydown', function (e) {
      if (e.key !== 'Enter' && e.key !== ' ') return;
      var card = e.target.closest('.iot-device-card');
      if (card) { e.preventDefault(); card.click(); }
    });
  }

  /* ─── pointer scroll for phone screen & schema output ─────── */
  root.addEventListener('wheel', function (e) {
    var target = e.target.closest('.iot-phone-screen, .iot-schema-output');
    if (!target || !root.contains(target)) return;
    var max = target.scrollHeight - target.clientHeight;
    if (max <= 1) return;
    var atTop    = target.scrollTop <= 0;
    var atBottom = target.scrollTop >= max - 1;
    if ((e.deltaY < 0 && atTop) || (e.deltaY > 0 && atBottom)) return;
    target.scrollTop += e.deltaY;
    e.preventDefault();
  }, { passive: false, capture: true });

  /* ─── go ───────────────────────────────────────────────────── */
  render();

})();
