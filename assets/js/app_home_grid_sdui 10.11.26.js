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
      qrcode: 'fa-qrcode',
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
      scanner: '<path fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" d="M7 4H5.5A1.5 1.5 0 0 0 4 5.5V7M17 4h1.5A1.5 1.5 0 0 1 20 5.5V7M20 17v1.5a1.5 1.5 0 0 1-1.5 1.5H17M7 20H5.5A1.5 1.5 0 0 1 4 18.5V17"/><rect x="8" y="8" width="3.2" height="3.2" rx=".4"/><rect x="12.8" y="8" width="3.2" height="3.2" rx=".4"/><rect x="8" y="12.8" width="3.2" height="3.2" rx=".4"/><path d="M13 13h1.2v1.2H13zm2.3 0H17v1.2h-1.7zm-2.3 2.3H14V17h-1zm2.3 0H17V17h-1.7z"/>',
      qrcode: '<path d="M3 3h8v8H3zm2 2v4h4V5zm8-2h8v8h-8zm2 2v4h4V5zM3 13h8v8H3zm2 2v4h4v-4zm10-2h2v2h-2zm3 0h3v2h-3zm-3 3h2v2h-2zm3 0h2v2h-2zm2 0h1v5h-3v-2h2zm-5 3h2v2h-2zm3 2h2v2h-2zm-3 0h2v2h-2z"/>',
      quote: '<path d="M10.2 18.4H4.6c0-3.55 1.7-6.35 5.1-8.4l1.15 1.7c-2.05 1.15-3.15 2.55-3.3 4.2h2.65zm9.2 0h-5.6c0-3.55 1.7-6.35 5.1-8.4l1.15 1.7c-2.05 1.15-3.15 2.55-3.3 4.2H19.4z"/>',
      grid: '<path d="M4 4h7v7H4zm9 0h7v7h-7zM4 13h7v7H4zm9 0h7v7h-7z"/>',
      profile: '<path d="M12 12.2A4.2 4.2 0 1 0 7.8 8 4.2 4.2 0 0 0 12 12.2zm0 1.8c-3.86 0-8.5 1.94-8.5 4.85V21h17v-2.15C20.5 15.94 15.86 14 12 14z"/>',
      message: '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3h11A2.5 2.5 0 0 1 20 5.5v8A2.5 2.5 0 0 1 17.5 16H9l-4.2 3.2A.8.8 0 0 1 3.5 18.5V5.5z"/>',
      phone: '<path d="M7.1 3.6c.4-.4 1-.5 1.5-.2l2 1.2c.5.3.7.9.6 1.4l-.5 1.8a1.2 1.2 0 0 1-.7.8l-1 .4a9.6 9.6 0 0 0 4.8 4.8l.4-1c.1-.5.5-.9.9-.7l1.8.5c.5.1 1.1.1 1.4.6l1.2 2c.3.5.2 1.1-.2 1.5l-1.1 1.1c-.4.4-1 .6-1.6.5C11.2 18.6 5.4 12.8 4.1 6.3c-.1-.6.1-1.2.5-1.6z"/>',
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
    var shareQr = components['grid-share-qr'] || {};
    var exploreSection = components['grid-explore-section'] || {};
    var quoteComp = components['grid-quote-block'] || {};
    var featureRow = components['grid-feature-row'] || {};
    var exploreRow = components['grid-explore-row'] || {};

    var featureIds = Array.isArray(featureRow.children) ? featureRow.children : [];
    var exploreIds = Array.isArray(exploreRow.children) ? exploreRow.children : [];
    var trailing = exploreSection.trailing || {};
    var headerActions = Array.isArray(header.actions) ? header.actions : [];
    var avatar = headerActions.find(function (a) { return a && a.type === 'avatarButton'; }) || {};
    var scanAction = headerActions.find(function (a) {
      return a && a.type !== 'avatarButton';
    }) || {};
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
    var qrImage = shareQr.imageUrl || shareQr.qrImageUrl || shareQr.image || qrByMode[selectedMode] || '';
    if (!qrImage && shareQr.qrImageUrlTemplate && shareQr.qrPayload) {
      qrImage = String(shareQr.qrImageUrlTemplate)
        .replace('{data}', encodeURIComponent(String(shareQr.qrPayload)))
        .replace('{payload}', encodeURIComponent(String(shareQr.qrPayload)))
        .replace('{mode}', encodeURIComponent(selectedMode));
    }
    var shareQrHtml = '<section class="ahp-gn-qr">'
      + '<div class="ahp-gn-qr-copy">'
      + '<span class="ahp-gn-qr-icon">' + iconSvg(shareQr.iconToken || 'qrcode') + '</span>'
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
        ? '<img src="' + esc(qrImage) + '" alt="Profile QR code" width="128" height="128" loading="lazy">'
        : '<div class="ahp-gn-qr-fallback" aria-hidden="true"></div>')
      + '</div>'
      + '<span class="ahp-gn-qr-caption">' + esc(shareQr.qrCaption || 'Scan to connect') + '</span>'
      + '</div></section>';

    var schemaActions = (schema && schema.actions) || {};
    function actionUrl(actionId) {
      var action = schemaActions[actionId] || {};
      return action.url || (action.destination && action.destination.url) || action.route || '';
    }
    var filters = Array.isArray(exploreSection.filters) ? exploreSection.filters : [];
    var filtersHtml = filters.map(function (filter) {
      var id = String((filter && filter.id) || 'all');
      var count = filter && filter.count ? ' ' + filter.count : '';
      return '<button type="button" class="ahp-gn-filter' + (filter && filter.selected ? ' is-active' : '') + '"'
        + ' data-recent-filter="' + esc(id) + '">'
        + esc((filter && filter.label) || id) + esc(count) + '</button>';
    }).join('');
    var exploreHtml = exploreIds.map(function (id) {
      var item = components[id] || {};
      var itemUrl = item.url || item.route || actionUrl(item.actionId || '');
      var initials = String(item.initials || '').trim();
      var badge = String(item.badge || '').trim();
      var time = String(item.time || '').trim();
      var preview = String(item.preview || item.subtitle || '');
      var space = String(item.space || 'professional');
      var tint = item.backgroundColor || (space === 'circle' ? '#fff4ec' : '#eef2ff');
      var avatar = item.avatarColor || item.color || '#5b6cff';
      return '<button type="button" class="ahp-gn-activity" data-space="' + esc(space) + '"'
        + ' data-action="' + esc(item.actionId || '') + '"'
        + (itemUrl ? ' data-url="' + esc(itemUrl) + '"' : '')
        + ' style="--ahp-gn-tint:' + esc(tint) + '">'
        + '<span class="ahp-gn-activity-avatar" style="background:' + esc(avatar) + '">' + esc(initials || '•') + '</span>'
        + '<span class="ahp-gn-activity-main">'
        + '<span class="ahp-gn-activity-top">'
        + '<strong>' + esc(item.title || '') + '</strong>'
        + (badge ? '<em>' + esc(badge) + '</em>' : '')
        + (time ? '<time>' + esc(time) + '</time>' : '')
        + '</span>'
        + (preview ? '<small>' + esc(preview) + '</small>' : '')
        + '</span></button>';
    }).join('');

    var initial = String(avatar.label || (content.header && content.header.avatarInitial) || 'S').charAt(0).toUpperCase();
    var avatarUrl = avatar.imageUrl || avatar.avatarUrl || avatar.image || (content.header && content.header.avatarUrl) || '';
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
      + '<button type="button" class="ahp-gn-icon-btn" data-action="' + esc(scanAction.actionId || 'grid-open-scanner') + '" aria-label="Scan QR code">'
      + iconSvg(scanAction.iconToken || 'scanner') + '</button>'
      + '<button type="button" class="ahp-gn-avatar" data-action="' + esc(avatar.actionId || 'navigate-profile') + '"'
      + (actionUrl(avatar.actionId || 'navigate-profile') ? ' data-url="' + esc(actionUrl(avatar.actionId || 'navigate-profile')) + '"' : '')
      + ' style="background:' + esc(avatar.color || '#7c3aed') + '" aria-label="Profile">'
      + (avatarUrl ? '<img src="' + esc(avatarUrl) + '" alt="" width="40" height="40">' : esc(initial))
      + '</button>'
      + '</div></header>'
      + '<div class="ahp-gn-feature-row">' + featuresHtml + '</div>'
      + shareQrHtml
      + '<div class="ahp-gn-section-head">'
      + '<div class="ahp-gn-recent-title">'
      + '<h2>' + esc(exploreSection.title || 'Recent Activity') + '</h2>'
      + (exploreSection.badge ? '<span class="ahp-gn-recent-new">' + esc(exploreSection.badge) + '</span>' : '')
      + '</div>'
      + '<button type="button" class="ahp-gn-see-all" data-action="' + esc(trailing.actionId || 'grid-see-all-exploring') + '"'
      + (actionUrl(trailing.actionId || 'grid-see-all-exploring') ? ' data-url="' + esc(actionUrl(trailing.actionId || 'grid-see-all-exploring')) + '"' : '')
      + '>'
      + esc(trailing.label || 'See all') + '</button>'
      + '</div>'
      + (filtersHtml ? '<div class="ahp-gn-filters">' + filtersHtml + '</div>' : '')
      + '<div class="ahp-gn-explore-row">' + exploreHtml + '</div>'
      + '<section class="ahp-gn-quote">'
      + iconSvg(quoteComp.iconToken || 'quote')
      + '<p>' + quoteHtml + '</p>'
      + '</section></div>';
  }

  function bindActions(root, runtime) {
    if (!root) return;
    root.querySelectorAll('[data-recent-filter]').forEach(function (chip) {
      chip.addEventListener('click', function () {
        var filter = chip.getAttribute('data-recent-filter') || 'all';
        root.querySelectorAll('[data-recent-filter]').forEach(function (el) {
          el.classList.toggle('is-active', el === chip);
        });
        root.querySelectorAll('.ahp-gn-activity').forEach(function (row) {
          var space = row.getAttribute('data-space') || '';
          row.hidden = filter !== 'all' && space !== filter;
        });
      });
    });
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
