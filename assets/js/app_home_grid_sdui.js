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
      sun: '<circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
      moon: '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 14.5A8.5 8.5 0 1 1 9.5 3a7 7 0 0 0 11.5 11.5z"/>',
      qrcode: '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 3H5a2 2 0 0 0-2 2v2m0 10v2a2 2 0 0 0 2 2h2m10 0h2a2 2 0 0 0 2-2v-2m0-10V5a2 2 0 0 0-2-2h-2"/><path d="M7 7h3v3H7zm7 0h3v3h-3zM7 14h3v3H7zm7 2h1v1h-1zm2-2h1v3h-3v-1h2z"/>',
      quote: '<path d="M10.2 18.4H4.6c0-3.55 1.7-6.35 5.1-8.4l1.15 1.7c-2.05 1.15-3.15 2.55-3.3 4.2h2.65zm9.2 0h-5.6c0-3.55 1.7-6.35 5.1-8.4l1.15 1.7c-2.05 1.15-3.15 2.55-3.3 4.2H19.4z"/>',
      grid: '<path d="M4 4h7v7H4zm9 0h7v7h-7zM4 13h7v7H4zm9 0h7v7h-7z"/>',
      profile: '<path d="M12 12.2A4.2 4.2 0 1 0 7.8 8 4.2 4.2 0 0 0 12 12.2zm0 1.8c-3.86 0-8.5 1.94-8.5 4.85V21h17v-2.15C20.5 15.94 15.86 14 12 14z"/>',
      message: '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3h11A2.5 2.5 0 0 1 20 5.5v8A2.5 2.5 0 0 1 17.5 16H9l-4.2 3.2A.8.8 0 0 1 3.5 18.5V5.5z"/>',
      phone: '<path d="M7.1 3.6c.4-.4 1-.5 1.5-.2l2 1.2c.5.3.7.9.6 1.4l-.5 1.8a1.2 1.2 0 0 1-.7.8l-1 .4a9.6 9.6 0 0 0 4.8 4.8l.4-1c.1-.5.5-.9.9-.7l1.8.5c.5.1 1.1.1 1.4.6l1.2 2c.3.5.2 1.1-.2 1.5l-1.1 1.1c-.4.4-1 .6-1.6.5C11.2 18.6 5.4 12.8 4.1 6.3c-.1-.6.1-1.2.5-1.6z"/>',
      'chevron-right': '<path fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/>',
      share: '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7M16 6l-4-4-4 4M12 2v14"/>',
      bell: '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 17H9m9 0a2.5 2.5 0 0 0 .7-1.8c0-2.4.5-4.2 1.3-5.5.7-1.2.5-2.8-.6-3.7A6 6 0 0 0 6.6 5.9c-1.1.9-1.3 2.5-.6 3.7.8 1.3 1.3 3.1 1.3 5.5A2.5 2.5 0 0 0 8 17"/><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M10 17a2 2 0 0 0 4 0"/>',
      briefcase: '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 7V5.5A1.5 1.5 0 0 1 9.5 4h5A1.5 1.5 0 0 1 16 5.5V7M4 10h16v8.5A1.5 1.5 0 0 1 18.5 20h-13A1.5 1.5 0 0 1 4 18.5V10zm0 0V8.5A1.5 1.5 0 0 1 5.5 7h13A1.5 1.5 0 0 1 20 8.5V10"/>'
    };
    var inner = paths[t] || paths.user;
    return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">' + inner + '</svg>';
  }

  // Self-contained static globe shown whenever the animated external SVG
  // can't load (missing/undeployed asset, 404, blocked). No network, no script.
  function orbitGlobeInlineSvg() {
    var dots = [
      [70,72,2,.9],[64,84,1.8,.75],[74,92,2.1,.9],[61,100,1.7,.7],[79,106,2,.85],
      [70,116,2.2,.9],[82,126,1.8,.75],[72,136,2,.8],[88,96,1.6,.6],
      [106,66,2,.85],[116,78,2.2,.9],[108,90,1.8,.75],[120,100,2.1,.9],[112,112,2,.85],
      [123,123,1.9,.8],[113,134,2,.8],[103,126,1.7,.65],[128,88,1.6,.6],
      [140,74,2,.85],[151,86,2.1,.9],[145,98,1.8,.75],[156,106,2,.85],[147,118,1.9,.8],
      [158,128,1.8,.7],[137,104,1.7,.65],[150,140,1.7,.7]
    ];
    var dotsHtml = dots.map(function (d) {
      return '<circle cx="' + d[0] + '" cy="' + d[1] + '" r="' + d[2] + '" fill-opacity="' + d[3] + '"/>';
    }).join('');
    return '<svg class="ahp-gn-orbit-globe-fallback" viewBox="0 0 220 220" width="220" height="220" '
      + 'xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false">'
      + '<defs>'
      + '<radialGradient id="gnGlobeSphere" cx="42%" cy="36%" r="68%">'
      + '<stop offset="0" stop-color="#2b3140"/><stop offset=".68" stop-color="#191c24"/>'
      + '<stop offset="1" stop-color="#0b0d13"/></radialGradient>'
      + '<radialGradient id="gnGlobeRim" cx="50%" cy="50%" r="50%">'
      + '<stop offset=".82" stop-color="#60a5fa" stop-opacity="0"/>'
      + '<stop offset=".96" stop-color="#93c5fd" stop-opacity=".28"/>'
      + '<stop offset="1" stop-color="#bfdbfe" stop-opacity=".85"/></radialGradient>'
      + '<clipPath id="gnGlobeClip"><circle cx="110" cy="110" r="84"/></clipPath>'
      + '</defs>'
      + '<circle cx="110" cy="110" r="94" fill="url(#gnGlobeRim)"/>'
      + '<circle cx="110" cy="110" r="84" fill="url(#gnGlobeSphere)"/>'
      + '<g clip-path="url(#gnGlobeClip)" fill="none" stroke="#6b7a96" stroke-opacity=".32" stroke-width="1">'
      + '<ellipse cx="110" cy="110" rx="84" ry="30"/><ellipse cx="110" cy="110" rx="84" ry="58"/>'
      + '<ellipse cx="110" cy="110" rx="30" ry="84"/><ellipse cx="110" cy="110" rx="58" ry="84"/>'
      + '<line x1="26" y1="110" x2="194" y2="110"/></g>'
      + '<g clip-path="url(#gnGlobeClip)" fill="#e8efff">' + dotsHtml + '</g>'
      + '<circle cx="96" cy="84" r="2.6" fill="#bfdbfe"/><circle cx="96" cy="84" r="5" fill="#60a5fa" fill-opacity=".25"/>'
      + '<circle cx="150" cy="118" r="2.6" fill="#bfdbfe"/><circle cx="150" cy="118" r="5" fill="#60a5fa" fill-opacity=".25"/>'
      + '<path d="M96 84 Q130 70 150 118" fill="none" stroke="#93c5fd" stroke-opacity=".55" stroke-width="1.1"/>'
      + '<circle cx="110" cy="110" r="84" fill="none" stroke="#9cc3fb" stroke-opacity=".5" stroke-width="1.2"/>'
      + '</svg>';
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
    var logoUrlDark = header.logoUrlDark
      || (header.appearanceDark && header.appearanceDark.logoUrlDark)
      || 'https://efactor.tech/assets/images/dark_grid.png';

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
        + '<span class="ahp-gn-feature-cta" aria-hidden="true"><em>' + esc(card.ctaLabel || contentCard.ctaLabel || 'Explore Now') + '</em> ' + iconSvg('chevron-right') + '</span>'
        + '<span class="ahp-gn-feature-go" aria-hidden="true">' + iconSvg('chevron-right') + '</span>'
        + '</button>';
    }).join('');

    var modes = Array.isArray(shareQr.modes) ? shareQr.modes : [];
    var selectedMode = String(shareQr.selectedMode || 'professional');
    var modesHtml = modes.map(function (mode) {
      var id = String((mode && mode.id) || '');
      var selected = mode && (mode.selected === true || id === selectedMode);
      var modeIcon = String((mode && mode.iconToken) || (id === 'circle' ? 'users' : 'user'));
      return '<button type="button" class="ahp-gn-qr-mode' + (selected ? ' is-active' : '') + '"'
        + ' data-action="' + esc((mode && mode.actionId) || 'grid-select-qr-mode') + '"'
        + ' data-mode="' + esc(id) + '">'
        + '<span class="ahp-gn-qr-mode-icon" aria-hidden="true">' + iconSvg(modeIcon) + '</span>'
        + '<span class="ahp-gn-qr-mode-label">' + esc((mode && mode.label) || id) + '</span>'
        + '</button>';
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
    // Preview-only layout: copy + QR on top, mode pills full-width at bottom.
    // QR image URLs / mode actions unchanged. Header icon prefers qrcode/scanner (not grid).
    var shareIconToken = String(shareQr.iconToken || 'qrcode').toLowerCase();
    if (!shareIconToken || shareIconToken === 'grid' || shareIconToken === 'border-all') {
      shareIconToken = 'qrcode';
    }
    var shareQrHtml = '<section class="ahp-gn-qr">'
      + '<div class="ahp-gn-qr-top">'
      + '<div class="ahp-gn-qr-copy">'
      + '<span class="ahp-gn-qr-icon">' + iconSvg(shareIconToken) + '</span>'
      + '<div class="ahp-gn-qr-text">'
      + '<strong>' + esc(shareQr.title || 'Share Profile QR') + '</strong>'
      + '<small>' + esc(shareQr.subtitle || 'Share your professional or circle profile with anyone.') + '</small>'
      + '</div></div>'
      + '<div class="ahp-gn-qr-media">'
      + '<div class="ahp-gn-qr-frame"'
      + ' data-qr-professional="' + esc(qrByMode.professional || '') + '"'
      + ' data-qr-circle="' + esc(qrByMode.circle || '') + '">'
      + (qrImage
        ? '<img src="' + esc(qrImage) + '" alt="Profile QR code" width="140" height="140" loading="lazy">'
        : '<div class="ahp-gn-qr-fallback" aria-hidden="true"></div>')
      + '</div>'
      + '<span class="ahp-gn-qr-caption">' + esc(shareQr.qrCaption || 'Scan to connect') + '</span>'
      + '</div></div>'
      + '<div class="ahp-gn-qr-modes" role="tablist">' + modesHtml + '</div>'
      + '</section>';

    var schemaActions = (schema && schema.actions) || {};
    function actionUrl(actionId) {
      var action = schemaActions[actionId] || {};
      return action.url || (action.destination && action.destination.url) || action.route || '';
    }
    var filters = Array.isArray(exploreSection.filters) ? exploreSection.filters : [];
    // Drop legacy "All" chip so UI matches Professional / Chat tabs.
    filters = filters.filter(function (filter) {
      return String((filter && filter.id) || '') !== 'all';
    });
    if (!filters.length) {
      filters = [
        { id: 'professional', label: 'Professional', iconToken: 'briefcase', selected: true },
        { id: 'circle', label: 'Chat', iconToken: 'message', selected: false }
      ];
    }
    var selectedFilter = 'professional';
    filters.forEach(function (filter) {
      if (filter && filter.selected) selectedFilter = String(filter.id || 'professional');
    });
    var filtersHtml = filters.map(function (filter) {
      var id = String((filter && filter.id) || 'professional');
      var label = String((filter && filter.label) || id);
      if (id === 'circle' && /circle/i.test(label)) label = 'Chat';
      var iconTok = String((filter && filter.iconToken) || (id === 'circle' ? 'message' : 'briefcase'));
      return '<button type="button" class="ahp-gn-filter' + (filter && filter.selected ? ' is-active' : '') + '"'
        + ' data-recent-filter="' + esc(id) + '">'
        + '<span class="ahp-gn-filter-icon" aria-hidden="true">' + iconSvg(iconTok) + '</span>'
        + '<span class="ahp-gn-filter-label">' + esc(label) + '</span>'
        + '</button>';
    }).join('');
    var exploreHtml = exploreIds.map(function (id) {
      var item = components[id] || {};
      var itemUrl = item.url || item.route || actionUrl(item.actionId || '');
      var initials = String(item.initials || '').trim();
      var time = String(item.time || '').trim();
      var preview = String(item.preview || item.subtitle || '');
      // subtitle in schema may already include " · time" — prefer preview only.
      if (preview.indexOf(' · ') > -1 && time && preview.slice(-time.length) === time) {
        preview = preview.replace(/\s·\s[^\·]+$/, '');
      }
      var space = String(item.space || 'professional');
      var tint = item.backgroundColor || (space === 'circle' ? '#fff4ec' : '#eef2ff');
      var avatar = item.avatarColor || item.color || '#5b6cff';
      var unread = item.unread === true || item.unread === 1 || item.unread === '1';
      var avatarImg = item.imageUrl || item.image || (item.leading && item.leading.imageUrl) || '';
      var iconTok = String(item.iconToken || (item.leading && item.leading.iconToken) || '').trim();
      // Prefer photo; else iconToken (briefcase/building/users) to match mock rows.
      var avatarInner = avatarImg
        ? '<img src="' + esc(avatarImg) + '" alt="" width="42" height="42">'
        : (iconTok ? iconSvg(iconTok) : esc(initials || '•'));
      var hidden = selectedFilter !== 'all' && space !== selectedFilter;
      return '<button type="button" class="ahp-gn-activity' + (unread ? ' is-unread' : '') + '" data-space="' + esc(space) + '"'
        + (hidden ? ' hidden' : '')
        + ' data-action="' + esc(item.actionId || '') + '"'
        + (itemUrl ? ' data-url="' + esc(itemUrl) + '"' : '')
        + ' style="--ahp-gn-tint:' + esc(tint) + '">'
        + '<span class="ahp-gn-activity-avatar" style="background:' + esc(avatar) + '">'
        + avatarInner
        + '</span>'
        + '<span class="ahp-gn-activity-main">'
        + '<span class="ahp-gn-activity-top">'
        + '<strong>' + esc(item.title || '') + '</strong>'
        + '</span>'
        + (preview ? '<small>' + esc(preview) + '</small>' : '')
        + '</span>'
        + '<span class="ahp-gn-activity-meta">'
        + (time ? '<time>' + esc(time) + '</time>' : '')
        + '<span class="ahp-gn-activity-meta-row">'
        + (unread ? '<span class="ahp-gn-activity-unread" aria-label="Unread"></span>' : '')
        + '<span class="ahp-gn-activity-chevron" aria-hidden="true">' + iconSvg('chevron-right') + '</span>'
        + '</span></span>'
        + '</button>';
    }).join('');

    var initial = String(avatar.label || (content.header && content.header.avatarInitial) || 'S').charAt(0).toUpperCase();
    var avatarUrl = avatar.imageUrl || avatar.avatarUrl || avatar.image || (content.header && content.header.avatarUrl) || '';
    // Hero copy — prefer schema values, fall back to defaults (keeps web + Flutter in sync).
    var heroTitle = header.subtitle || header.title || 'Your professional network space';
    var heroEyebrow = header.eyebrow || (content.header && content.header.eyebrow) || 'Welcome to GRID';
    var heroTagline = header.tagline || (content.header && content.header.tagline) || 'Connect. Collaborate. Grow Together.';
    // Orbit globe asset from schema (content / feature-row / circle-connect card).
    var circleFeature = components['grid-feature-circle-connect'] || {};
    var orbitGlobeUrl = String(
      header.orbitGlobeUrl
      || content.orbitGlobeUrl
      || featureRow.orbitGlobeUrl
      || circleFeature.orbitGlobeUrl
      || ''
    ).trim();
    if (!orbitGlobeUrl) {
      orbitGlobeUrl = (window.location.origin || '') + '/assets/grid-orbit-globe.svg';
    }
    // Animated SVG is progressive enhancement; if it can't load (missing asset,
    // 404, blocked), the <object> renders the inline static globe fallback instead.
    var orbitGlobeHtml = '<div class="ahp-gn-orbit-globe-wrap" aria-hidden="true">'
      + '<object class="ahp-gn-orbit-globe" type="image/svg+xml" data="' + esc(orbitGlobeUrl) + '" tabindex="-1">'
      + orbitGlobeInlineSvg()
      + '</object>'
      + '</div>';

    return '<div class="ahp-gn-page font-sans antialiased" data-grid-sdui="1">'
      + '<header class="ahp-gn-header">'
      + '<div class="ahp-gn-brand">'
      + '<img class="ahp-gn-logo" src="' + esc(logoUrl) + '" alt="GRID" width="160" height="48"'
      + ' data-logo-light="' + esc(logoUrl) + '" data-logo-dark="' + esc(logoUrlDark) + '">'
      + '</div>'
      + '<div class="ahp-gn-header-actions">'
      // GRID has no theme toggle. Appearance follows the account's Appearance
      // preference (schema.theme.defaultMode), applied in render() via
      // applyAppearance(). Header actions are scanner + profile avatar only.
      + '<button type="button" class="ahp-gn-icon-btn" data-action="' + esc(scanAction.actionId || 'grid-open-scanner') + '" aria-label="Scan QR code">'
      + iconSvg(scanAction.iconToken || 'scanner') + '</button>'
      + '<button type="button" class="ahp-gn-avatar" data-action="' + esc(avatar.actionId || 'navigate-profile') + '"'
      + (actionUrl(avatar.actionId || 'navigate-profile') ? ' data-url="' + esc(actionUrl(avatar.actionId || 'navigate-profile')) + '"' : '')
      + ' style="background:' + esc(avatar.color || '#7c3aed') + '" aria-label="Profile">'
      + (avatarUrl ? '<img src="' + esc(avatarUrl) + '" alt="" width="40" height="40">' : esc(initial))
      + '<span class="ahp-gn-avatar-status" aria-hidden="true"></span>'
      + '</button>'
      + '</div></header>'
      + '<section class="ahp-gn-hero">'
      + '<div class="ahp-gn-hero-copy">'
      + '<p class="ahp-gn-hero-eyebrow">' + esc(heroEyebrow) + '</p>'
      + '<h1 class="ahp-gn-hero-title">' + esc(heroTitle) + '</h1>'
      + '<p class="ahp-gn-hero-tagline">' + esc(heroTagline) + '</p>'
      + '</div>'
      + orbitGlobeHtml
      + '</section>'
      + '<div class="ahp-gn-feature-stage">'
      + '<div class="ahp-gn-feature-row">' + featuresHtml + '</div>'
      + '</div>'
      + shareQrHtml
      + '<section class="ahp-gn-updates">'
      + '<div class="ahp-gn-section-head">'
      + '<div class="ahp-gn-recent-title">'
      + '<span class="ahp-gn-updates-bell" aria-hidden="true">' + iconSvg(exploreSection.iconToken || 'bell') + '</span>'
      + '<h2>' + esc(exploreSection.title || 'Updates') + '</h2>'
      + (exploreSection.badge ? '<span class="ahp-gn-recent-new">' + esc(exploreSection.badge) + '</span>' : '')
      + '</div>'
      + '<button type="button" class="ahp-gn-see-all" data-action="' + esc(trailing.actionId || 'grid-see-all-exploring') + '"'
      + (actionUrl(trailing.actionId || 'grid-see-all-exploring') ? ' data-url="' + esc(actionUrl(trailing.actionId || 'grid-see-all-exploring')) + '"' : '')
      + '>'
      + esc(trailing.label || 'View All') + ' ' + iconSvg('chevron-right') + '</button>'
      + '</div>'
      + (filtersHtml ? '<div class="ahp-gn-filters" role="tablist">' + filtersHtml + '</div>' : '')
      + '<div class="ahp-gn-explore-row">' + exploreHtml + '</div>'
      + '<button type="button" class="ahp-gn-view-all-updates" data-action="' + esc((exploreSection.footer && exploreSection.footer.actionId) || trailing.actionId || 'grid-see-all-exploring') + '"'
      + (actionUrl((exploreSection.footer && exploreSection.footer.actionId) || trailing.actionId || 'grid-see-all-exploring') ? ' data-url="' + esc(actionUrl((exploreSection.footer && exploreSection.footer.actionId) || trailing.actionId || 'grid-see-all-exploring')) + '"' : '')
      + '>' + esc((exploreSection.footer && exploreSection.footer.label) || 'View All Updates') + ' ' + iconSvg('chevron-right') + '</button>'
      + '</section>'
      + '</div>';
  }

  function phoneThemeHost(root) {
    if (!root || !root.closest) return null;
    return root.closest('.gd-phone-screen, .ahp-phone-screen') || root.parentElement;
  }

  function currentPhoneTheme(host) {
    if (!host) return 'light';
    var forced = host.getAttribute('data-theme');
    if (forced === 'dark' || forced === 'light') return forced;
    try {
      return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    } catch (e) {
      return 'light';
    }
  }

  function applyPhoneTheme(host, theme) {
    if (!host) return;
    var resolved = theme === 'dark' ? 'dark' : 'light';
    host.setAttribute('data-theme', resolved);
    host.classList.toggle('dark', resolved === 'dark');
    var logos = host.querySelectorAll ? host.querySelectorAll('.ahp-gn-logo') : [];
    for (var i = 0; i < logos.length; i++) {
      var img = logos[i];
      var next = resolved === 'dark'
        ? (img.getAttribute('data-logo-dark') || '')
        : (img.getAttribute('data-logo-light') || '');
      if (next && img.getAttribute('src') !== next) img.setAttribute('src', next);
    }
    try {
      host.dispatchEvent(new CustomEvent('grid-theme', { bubbles: true, detail: { theme: resolved } }));
    } catch (e) {}
  }

  // GRID has no theme toggle. Appearance is driven entirely by the account's
  // preference, delivered in schema.theme.defaultMode ('light' | 'dark' |
  // 'system'). 'light'/'dark' force that mode; 'system' follows the OS/browser
  // and keeps following live changes. The page's dark styles are applied via
  // [data-theme="dark"] only (there is no page-level prefers-color-scheme CSS),
  // so 'system' must be resolved to an explicit data-theme here in JS.
  function applyAppearance(host, mode) {
    if (!host) return;
    // Detach any previous system-mode watcher first, so a changed account
    // preference (e.g. System -> Dark, or switching accounts) is not later
    // overridden by a stale OS-change listener from an earlier render.
    if (host.__gridAppearanceCleanup) {
      try { host.__gridAppearanceCleanup(); } catch (e) {}
      host.__gridAppearanceCleanup = null;
    }
    mode = String(mode || 'system').toLowerCase();
    if (mode === 'light' || mode === 'dark') {
      applyPhoneTheme(host, mode);
      return;
    }
    var mq = null;
    try { mq = window.matchMedia('(prefers-color-scheme: dark)'); } catch (e) {}
    var sync = function () { applyPhoneTheme(host, (mq && mq.matches) ? 'dark' : 'light'); };
    sync();
    if (mq) {
      try {
        if (mq.addEventListener) {
          mq.addEventListener('change', sync);
          host.__gridAppearanceCleanup = function () { mq.removeEventListener('change', sync); };
        } else if (mq.addListener) {
          mq.addListener(sync);
          host.__gridAppearanceCleanup = function () { mq.removeListener(sync); };
        }
      } catch (e) {}
    }
  }

  function bindActions(root, runtime) {
    if (!root) return;
    // No theme-toggle binding: GRID appearance is set from the account
    // preference in render() via applyAppearance(), not a header button.
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
    var requested = runtime || {};
    runtime = mergeRuntime(requested);
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
    // Studio Dark/Light passes webThemeOverride. Mobile schema defaultMode is unchanged.
    var forced = String(requested.webThemeOverride || '').toLowerCase();
    var appearanceMode = (forced === 'dark' || forced === 'light')
      ? forced
      : ((schema.theme && schema.theme.defaultMode) || 'system');
    applyAppearance(phoneThemeHost(el), appearanceMode);
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
    extractSchema: extractSchema,
    applyAppearance: applyAppearance
  };
})(typeof window !== 'undefined' ? window : globalThis);
