/**
 * Grid SDUI renderer for App Home — Grid tab preview.
 * Renders UI from get_grid schema (grid_api_creation.php), same pattern as IoT SDUI.
 */
(function (global) {
  'use strict';

  var runtimeConfig = {
    gridApiUrl: '',
    userId: 0,
    sessionApiBase: '',
    onAction: null,
    onRefresh: null
  };

  function esc(v) {
    return String(v == null ? '' : v)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function mergeRuntime(runtime) {
    if (!runtime || typeof runtime !== 'object') return runtimeConfig;
    runtimeConfig = {
      gridApiUrl: String(runtime.gridApiUrl || runtimeConfig.gridApiUrl || '').replace(/\/+$/, ''),
      userId: parseInt(runtime.userId || runtimeConfig.userId || 0, 10) || 0,
      sessionApiBase: String(runtime.sessionApiBase || runtime.appApiBase || runtimeConfig.sessionApiBase || '').replace(/\/+$/, ''),
      onAction: typeof runtime.onAction === 'function' ? runtime.onAction : runtimeConfig.onAction,
      onRefresh: typeof runtime.onRefresh === 'function' ? runtime.onRefresh : runtimeConfig.onRefresh
    };
    return runtimeConfig;
  }

  function iconClass(token) {
    var map = {
      user: 'fa-user',
      users: 'fa-users',
      search: 'fa-search',
      quote: 'fa-quote-left',
      grid: 'fa-border-all',
      profile: 'fa-user',
      'chevron-right': 'fa-chevron-right'
    };
    return map[String(token || '').toLowerCase()] || 'fa-circle';
  }

  function iconSvg(token) {
    var t = String(token || '').toLowerCase();
    var paths = {
      user: '<path d="M12 12.2A4.2 4.2 0 1 0 7.8 8 4.2 4.2 0 0 0 12 12.2zm0 1.8c-3.86 0-8.5 1.94-8.5 4.85V21h17v-2.15C20.5 15.94 15.86 14 12 14z"/>',
      users: '<path d="M8.8 11.4A3.4 3.4 0 1 0 5.4 8a3.4 3.4 0 0 0 3.4 3.4zm7.1-.2a3 3 0 1 0-3-3 3 3 0 0 0 3 3zM8.8 13.2c-3.08 0-7.3 1.54-7.3 4.6V21h12.4v-3.2c0-3.06-4.22-4.6-7.3-4.6zm7.1.5c-.5 0-1.04.05-1.6.14 1.55.94 2.6 2.24 2.6 4.06V21H22v-2.7c0-2.52-3.32-4.26-6.1-4.26z"/>',
      search: '<path fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" d="M10.75 18.25a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/><path fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" d="M16.6 16.6 21 21"/>',
      quote: '<path d="M10.2 18.4H4.6c0-3.55 1.7-6.35 5.1-8.4l1.15 1.7c-2.05 1.15-3.15 2.55-3.3 4.2h2.65zm9.2 0h-5.6c0-3.55 1.7-6.35 5.1-8.4l1.15 1.7c-2.05 1.15-3.15 2.55-3.3 4.2H19.4z"/>',
      grid: '<path d="M4 4h7v7H4zm9 0h7v7h-7zM4 13h7v7H4zm9 0h7v7h-7z"/>',
      profile: '<path d="M12 12.2A4.2 4.2 0 1 0 7.8 8 4.2 4.2 0 0 0 12 12.2zm0 1.8c-3.86 0-8.5 1.94-8.5 4.85V21h17v-2.15C20.5 15.94 15.86 14 12 14z"/>',
      'chevron-right': '<path fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/>',
      share: '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7M16 6l-4-4-4 4M12 2v14"/>'
    };
    var inner = paths[t] || paths.user;
    return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">' + inner + '</svg>';
  }

  function extractSchema(payload) {
    if (!payload || typeof payload !== 'object') return null;
    if (payload.schema && typeof payload.schema === 'object') return payload.schema;
    if (payload.components || payload.rootComponentId) return payload;
    return null;
  }

  function extractContent(payload, schema) {
    if (payload && payload.content && typeof payload.content === 'object') return payload.content;
    if (schema && schema.content && typeof schema.content === 'object') return schema.content;
    return {};
  }

  function renderFromSchema(schema, content) {
    var components = (schema && schema.components) || {};
    var header = components['grid-page-header'] || {};
    var statsComp = components['grid-stats-row'] || {};
    var shareQr = components['grid-share-qr'] || {};
    var exploreSection = components['grid-explore-section'] || {};
    var quoteComp = components['grid-quote-block'] || {};
    var featureRow = components['grid-feature-row'] || {};
    var exploreRow = components['grid-explore-row'] || {};

    var featureIds = Array.isArray(featureRow.children) ? featureRow.children : [];
    var exploreIds = Array.isArray(exploreRow.children) ? exploreRow.children : [];
    var stats = Array.isArray(statsComp.items) ? statsComp.items : ((content.stats) || []);
    var trailing = exploreSection.trailing || {};
    var headerActions = Array.isArray(header.actions) ? header.actions : [];
    var avatar = headerActions.find(function (a) { return a && a.type === 'avatarButton'; }) || {};
    var searchAction = headerActions.find(function (a) { return a && a.iconToken === 'search'; }) || headerActions[0] || {};
    var logoUrl = header.logoUrl || header.image || (content.header && content.header.logoUrl) || content.logoUrl
      || 'https://efactor.tech/assets/images/GRID_Logo.png';

    var featuresHtml = featureIds.map(function (id) {
      var card = components[id] || {};
      var contentCards = Array.isArray(content.featureCards) ? content.featureCards : [];
      var contentCard = contentCards.find(function (c) {
        if (!c || typeof c !== 'object') return false;
        var cid = 'grid-feature-' + String(c.id || '');
        return cid === id || String(c.actionId || '') === String(card.actionId || '') || String(c.id || '') === String(id).replace(/^grid-feature-/, '');
      }) || {};
      var cardUrl = card.url || card.route || contentCard.url || contentCard.route || '';
      var subtitle = card.subtitle || contentCard.subtitle || '';
      var iconHtml = iconSvg(card.iconToken || contentCard.iconToken || 'user');
      return '<button type="button" class="ahp-gn-feature" data-action="' + esc(card.actionId || contentCard.actionId || '') + '"'
        + (cardUrl ? ' data-url="' + esc(cardUrl) + '"' : '')
        + ' data-open-in="inAppWebView"'
        + ' style="--ahp-gn-start:' + esc(card.gradientStart || card.color || contentCard.gradientStart || '#dce4ff')
        + ';--ahp-gn-end:' + esc(card.gradientEnd || card.color || contentCard.gradientEnd || '#b7c4fb')
        + ';--ahp-gn-icon:' + esc(card.color || contentCard.color || '#5b6cff')
        + ';--ahp-gn-text:' + esc(card.textColor || contentCard.textColor || '#312e81')
        + ';color:' + esc(card.textColor || contentCard.textColor || '#312e81') + '">'
        + '<span class="ahp-gn-feature-icon">' + iconHtml + '</span>'
        + '<strong>' + esc(card.title || contentCard.title || '') + '</strong>'
        + (subtitle ? '<small class="ahp-gn-feature-sub">' + esc(subtitle) + '</small>' : '')
        + '<span class="ahp-gn-feature-go" aria-hidden="true">' + iconSvg('chevron-right') + '</span>'
        + '</button>';
    }).join('');

    var statsHtml = stats.map(function (stat, index) {
      var value = (stat && typeof stat.value === 'object' && stat.value !== null)
        ? (stat.value.fallback != null ? stat.value.fallback : (stat.value.value != null ? stat.value.value : 0))
        : (stat && stat.value != null ? stat.value : 0);
      return '<div class="ahp-gn-stat">'
        + '<strong>' + esc(String(value)) + '</strong>'
        + '<span>' + esc((stat && stat.label) || '') + '</span>'
        + '</div>'
        + (index < stats.length - 1 ? '<span class="ahp-gn-stat-divider" aria-hidden="true"></span>' : '');
    }).join('');

    var modes = Array.isArray(shareQr.modes) ? shareQr.modes : [];
    var selectedMode = String(shareQr.selectedMode || 'professional');
    var modesHtml = modes.map(function (mode) {
      var id = String((mode && mode.id) || '');
      var selected = mode && (mode.selected === true || id === selectedMode);
      return '<button type="button" class="ahp-gn-qr-mode' + (selected ? ' is-active' : '') + '"'
        + ' data-action="' + esc((mode && mode.actionId) || 'grid-select-qr-mode') + '"'
        + ' data-mode="' + esc(id) + '">' + esc((mode && mode.label) || id) + '</button>';
    }).join('');
    var qrByMode = (shareQr.qrImageUrlsByMode && typeof shareQr.qrImageUrlsByMode === 'object')
      ? shareQr.qrImageUrlsByMode : {};
    var qrImage = shareQr.qrImageUrl || shareQr.image || qrByMode[selectedMode] || '';
    if (!qrImage && shareQr.qrImageUrlTemplate && shareQr.qrPayload) {
      qrImage = String(shareQr.qrImageUrlTemplate)
        .replace('{data}', encodeURIComponent(String(shareQr.qrPayload)))
        .replace('{payload}', encodeURIComponent(String(shareQr.qrPayload)))
        .replace('{mode}', encodeURIComponent(selectedMode));
    }
    var shareQrHtml = '<section class="ahp-gn-qr">'
      + '<div class="ahp-gn-qr-copy">'
      + '<span class="ahp-gn-qr-icon">' + iconSvg(shareQr.iconToken || 'grid') + '</span>'
      + '<div class="ahp-gn-qr-text">'
      + '<strong>' + esc(shareQr.title || 'Share Profile QR') + '</strong>'
      + '<small>' + esc(shareQr.subtitle || 'Share your professional or circle profile with anyone.') + '</small>'
      + '</div>'
      + '<div class="ahp-gn-qr-modes" role="tablist">' + modesHtml + '</div>'
      + '</div>'
      + '<div class="ahp-gn-qr-media">'
      + '<div class="ahp-gn-qr-frame"'
      + ' data-qr-professional="' + esc(qrByMode.professional || '') + '"'
      + ' data-qr-circle="' + esc(qrByMode.circle || '') + '">'
      + (qrImage
        ? '<img src="' + esc(qrImage) + '" alt="Profile QR code" width="120" height="120" loading="lazy">'
        : '<div class="ahp-gn-qr-fallback" aria-hidden="true"></div>')
      + '</div>'
      + '<span class="ahp-gn-qr-caption">' + esc(shareQr.qrCaption || 'Scan to connect') + '</span>'
      + '<button type="button" class="ahp-gn-qr-share" data-action="' + esc(shareQr.shareActionId || shareQr.actionId || 'grid-share-qr') + '" aria-label="Share QR">'
      + iconSvg('share') + '</button>'
      + '</div></section>';

    var exploreHtml = exploreIds.map(function (id) {
      var item = components[id] || {};
      return '<button type="button" class="ahp-gn-explore" data-action="' + esc(item.actionId || '') + '">'
        + '<span class="ahp-gn-explore-icon" style="--ahp-gn-icon:' + esc(item.color || '#8b5cf6') + '">'
        + iconSvg(item.iconToken) + '</span>'
        + '<span class="ahp-gn-explore-copy"><strong>' + esc(item.title || '') + '</strong>'
        + '<small>' + esc(item.subtitle || '') + '</small></span>'
        + '<span class="ahp-gn-explore-go" aria-hidden="true">' + iconSvg('chevron-right') + '</span>'
        + '</button>';
    }).join('');

    var initial = String(avatar.label || (content.header && content.header.avatarInitial) || 'S').charAt(0).toUpperCase();
    var quoteText = quoteComp.title || 'Connections turn potential into progress.';
    var quoteHtml = esc(quoteText).replace(/^Connections\b/, '<span class="ahp-gn-quote-accent">Connections</span>');

    return '<div class="ahp-gn-page" data-grid-sdui="1">'
      + '<header class="ahp-gn-header">'
      + '<div class="ahp-gn-header-text">'
      + '<div class="ahp-gn-brand">'
      + '<img class="ahp-gn-logo" src="' + esc(logoUrl) + '" alt="GRID" width="120" height="36">'
      + '</div>'
      + '<p>' + esc(header.subtitle || 'Your professional network space') + '</p>'
      + '</div>'
      + '<div class="ahp-gn-header-actions">'
      + '<button type="button" class="ahp-gn-icon-btn" data-action="' + esc(searchAction.actionId || 'grid-open-search') + '" aria-label="Search">'
      + iconSvg(searchAction.iconToken || 'search') + '</button>'
      + '<button type="button" class="ahp-gn-avatar" data-action="' + esc(avatar.actionId || 'navigate-profile') + '"'
      + ' style="background:' + esc(avatar.color || '#7c3aed') + '" aria-label="Profile">' + esc(initial) + '</button>'
      + '</div></header>'
      + '<div class="ahp-gn-feature-row">' + featuresHtml + '</div>'
      + '<div class="ahp-gn-stats">' + statsHtml + '</div>'
      + shareQrHtml
      + '<div class="ahp-gn-section-head">'
      + '<h2>' + esc(exploreSection.title || 'Continue Exploring') + '</h2>'
      + '<button type="button" class="ahp-gn-see-all" data-action="' + esc(trailing.actionId || 'grid-see-all-exploring') + '">'
      + esc(trailing.label || 'See all') + '</button>'
      + '</div>'
      + '<div class="ahp-gn-explore-row">' + exploreHtml + '</div>'
      + '<section class="ahp-gn-quote">'
      + iconSvg(quoteComp.iconToken || 'quote')
      + '<p>' + quoteHtml + '</p>'
      + '</section></div>';
  }

  function bindActions(root, runtime) {
    if (!root) return;
    root.querySelectorAll('[data-action]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var actionId = btn.getAttribute('data-action') || '';
        var mode = btn.getAttribute('data-mode') || '';
        var url = btn.getAttribute('data-url') || '';
        if (actionId === 'grid-select-qr-mode' && mode) {
          root.querySelectorAll('.ahp-gn-qr-mode').forEach(function (el) {
            el.classList.toggle('is-active', el.getAttribute('data-mode') === mode);
          });
          var frame = root.querySelector('.ahp-gn-qr-frame');
          var img = frame ? frame.querySelector('img') : null;
          var nextSrc = frame ? (frame.getAttribute('data-qr-' + mode) || '') : '';
          if (img && nextSrc) img.src = nextSrc;
        }
        if (typeof runtime.onAction === 'function') {
          runtime.onAction(actionId, {
            source: 'grid-sdui',
            mode: mode || undefined,
            url: url || undefined,
            openUrl: !!url
          });
          return;
        }
        if (actionId === 'refresh-grid' && typeof runtime.onRefresh === 'function') {
          runtime.onRefresh();
        }
      });
    });
  }

  function render(payload, mount, runtime) {
    runtime = mergeRuntime(runtime || {});
    var el = typeof mount === 'string' ? document.querySelector(mount) : mount;
    if (!el) return { ok: false, error: 'Grid mount not found' };

    var schema = extractSchema(payload);
    if (!schema || !schema.components || !schema.components['grid-page']) {
      el.innerHTML = '<div class="ahp-dev-empty"><i class="fas fa-border-all"></i>'
        + '<strong>Grid schema missing</strong><span>get_grid did not return grid-page components.</span></div>';
      return { ok: false, error: 'Invalid grid schema' };
    }

    var content = extractContent(payload, schema);
    el.innerHTML = renderFromSchema(schema, content);
    bindActions(el, runtime);
    return { ok: true, schema: schema, content: content };
  }

  function buildUrl(runtime) {
    runtime = mergeRuntime(runtime || {});
    var base = String(runtime.gridApiUrl || '').trim();
    if (!base) {
      base = 'https://efactor.tech/v8/grid_api_creation.php?action=get_grid';
    }
    if (!/[?&]action=/.test(base)) {
      base += (base.indexOf('?') >= 0 ? '&' : '?') + 'action=get_grid';
    }
    if (runtime.userId > 0 && !/[?&]user_id=/.test(base)) {
      base += '&user_id=' + encodeURIComponent(String(runtime.userId));
    }
    return base;
  }

  function fetchSchema(runtime) {
    runtime = mergeRuntime(runtime || {});
    var url = buildUrl(runtime);
    var sessionFallback = String(runtime.sessionApiBase || '').replace(/\/+$/, '');

    function viaProxy() {
      if (!sessionFallback) {
        return Promise.reject(new Error('Grid API unreachable'));
      }
      return fetch(sessionFallback + '/grid', {
        method: 'GET',
        credentials: 'same-origin',
        headers: { Accept: 'application/json' }
      }).then(function (res) {
        return res.json().then(function (data) {
          if (!res.ok || !data || data.success === false) {
            throw new Error((data && data.message) || ('Grid proxy HTTP ' + res.status));
          }
          return data;
        });
      });
    }

    return fetch(url, {
      method: 'GET',
      credentials: 'omit',
      headers: { Accept: 'application/json' }
    }).then(function (res) {
      return res.json().then(function (data) {
        if (!res.ok || !data || data.success === false) {
          throw new Error((data && data.message) || ('Grid API HTTP ' + res.status));
        }
        return data;
      });
    }).catch(function () {
      return viaProxy();
    });
  }

  global.AHP_GRID_SDUI = {
    render: render,
    fetchSchema: fetchSchema,
    buildUrl: buildUrl,
    extractSchema: extractSchema
  };
})(typeof window !== 'undefined' ? window : globalThis);
