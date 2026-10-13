#!/usr/bin/env node
/**
 * provision_app_home_schema.cjs
 *
 * Reads { layout, cfg } JSON from stdin.
 * Writes the complete App Home SDUI schema JSON to stdout.
 *
 * Called by App\Libraries\AppHomeSchemaBuilder::build() via proc_open.
 * DO NOT write anything to stdout except the final JSON.
 */

'use strict';

// â”€â”€â”€ helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function safeStr(v, fallback = '') {
  return (typeof v === 'string' && v.trim() !== '') ? v.trim() : fallback;
}
function safeInt(v, fallback = 0) {
  const n = parseInt(v, 10);
  return Number.isFinite(n) ? n : fallback;
}
function safeColor(v, fallback = '#6366f1') {
  return (typeof v === 'string' && /^#[0-9a-fA-F]{3,8}$/.test(v.trim())) ? v.trim() : fallback;
}

/** Strip replacement chars / broken emoji so Flutter never shows "???? Welcome back". */
function safeGreeting(v, fallback = 'Welcome back') {
  let s = typeof v === 'string' ? v.trim() : '';
  if (!s) return fallback;
  // Drop Unicode replacement char and lone ? runs left by latin1/mojibake emoji loss.
  s = s.replace(/\uFFFD+/g, '').replace(/^\?{2,}\s*/g, '').trim();
  if (!s || /^Welcome back$/i.test(s)) return fallback;
  // Keep emoji when valid UTF-8 survived; otherwise prefer clean ASCII default.
  if (/^[\u0000-\u001F\u007F-\u009F]+/.test(s)) return fallback;
  return s;
}

/**
 * Fix duplicated /v8/... path joins from misconfigured base_url / APP_HOME_API_URL.
 * e.g. .../api_creation.php/v8/api_creation.php â†’ .../v8/api_creation.php
 */
function normalizePublicUrl(raw, fallback) {
  let url = safeStr(raw, '');
  if (!url) return safeStr(fallback, '');
  url = url.replace(/\/+$/, '');

  // Collapse ".../api_creation.php/v8/api_creation.php" (and query).
  url = url.replace(
    /\/(?:v8\/)?api_creation\.php\/+(?:v8\/)?api_creation\.php/gi,
    '/v8/api_creation.php'
  );
  // Collapse ".../api_creation.php/v8/iot_api_creation.php"
  url = url.replace(
    /\/(?:v8\/)?api_creation\.php\/+(?:v8\/)?iot_api_creation\.php/gi,
    '/v8/iot_api_creation.php'
  );
  // Collapse repeated iot endpoint joins.
  url = url.replace(
    /\/(?:v8\/)?iot_api_creation\.php\/+(?:v8\/)?iot_api_creation\.php/gi,
    '/v8/iot_api_creation.php'
  );

  // If someone passed schema base + relative iot path wrongly ending as api_creation.php only,
  // leave as-is after collapse above.
  return url;
}

function normalizeSchemaApiUrl(raw, userId) {
  const uid = safeInt(userId, 0) || 0;
  let url = normalizePublicUrl(raw, 'https://efactor.tech/v8/api_creation.php');
  // Ensure endpoint file is api_creation.php (not iot).
  if (/iot_api_creation\.php/i.test(url)) {
    url = url.replace(/iot_api_creation\.php/i, 'api_creation.php');
  }
  if (!/api_creation\.php/i.test(url)) {
    url = 'https://efactor.tech/v8/api_creation.php';
  }
  // Strip existing query, then rebuild canonical get_schema query.
  const base = url.split('?')[0].replace(/\/+$/, '');
  return base + '?action=get_schema&user_id=' + Math.max(1, uid);
}

function normalizeIotApiUrl(raw) {
  let url = normalizePublicUrl(raw, 'https://efactor.tech/v8/iot_api_creation.php');
  if (/api_creation\.php/i.test(url) && !/iot_api_creation\.php/i.test(url)) {
    // Wrong endpoint (schema API used as IoT) â€” force IoT script.
    url = url.replace(/api_creation\.php/i, 'iot_api_creation.php');
  }
  if (!/iot_api_creation\.php/i.test(url)) {
    url = 'https://efactor.tech/v8/iot_api_creation.php';
  }
  return url.split('?')[0].replace(/\/+$/, '');
}

// â”€â”€â”€ responsive contract â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function buildResponsiveContract() {
  return {
    version: '1.0',
    unit: 'logicalPx',
    dimensionPolicy: 'logical',
    scaleLegacyDimensions: false,
    safeArea: true,
    breakpoints: {
      compact:  { minimumWidth: 0,   maximumWidth: 599 },
      medium:   { minimumWidth: 600, maximumWidth: 839 },
      expanded: { minimumWidth: 840 },
    },
    layouts: {
      narrow:   { columns: 2, gridGap: 10, pagePadding: 12, maxContentWidth: null },
      compact:  { columns: 3, gridGap: 12, pagePadding: 16, maxContentWidth: null },
      medium:   { columns: 4, gridGap: 14, pagePadding: 24, maxContentWidth: 720 },
      expanded: { columns: 5, gridGap: 16, pagePadding: 32, maxContentWidth: 1024 },
    },
  };
}

// â”€â”€â”€ card face builder â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function buildFaces(card) {
  const faces = Array.isArray(card.faces) ? card.faces : [];
  const result = [];
  for (let i = 0; i < 4; i++) {
    const src = (faces[i] && typeof faces[i] === 'object') ? faces[i] : {};
    result.push({
      faceIndex: i,
      title:    safeStr(src.title || card.title || card.label, ''),
      subtitle: safeStr(src.subtitle || card.subtitle, ''),
      icon:     safeStr(src.icon    || card.icon, 'fa-cube'),
      color:    safeColor(src.color || card.color, '#6366f1'),
      value:    safeStr(src.value   || card.value, ''),
      label:    safeStr(src.label   || card.label, ''),
      image:    safeStr(src.image   || card.image, ''),
    });
  }
  return result;
}

// â”€â”€â”€ single card spec â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function buildCardSpec(card, theme, forcedGridArea) {
  const type     = safeStr(card.type, 'link');
  const colSpan  = safeInt(card.colSpan, 1);
  const rowSpan  = safeInt(card.rowSpan, 1);
  const radius   = safeInt(card.borderRadius, safeInt(theme.cardBorderRadius, 20));
  const faces    = buildFaces(card);
  const id       = safeStr(card.id, 'card-' + Date.now());
  // Always unique â€” never reuse another card's gridArea (Flutter layout breaks on duplicates).
  const gridArea = safeStr(forcedGridArea || id, id);

  return {
    id,
    type,
    gridArea,
    title:         safeStr(card.title || card.label, ''),
    label:         safeStr(card.label || card.title, ''),
    subtitle:      safeStr(card.subtitle, ''),
    value:         safeStr(card.value, ''),
    icon:          safeStr(card.icon, 'fa-cube'),
    color:         safeColor(card.color, '#6366f1'),
    textColor:     safeColor(card.textColor, '#ffffff'),
    image:         safeStr(card.image, ''),
    imageFit:      safeStr(card.imageFit, 'cover'),
    imagePosition: safeStr(card.imagePosition, 'center'),
    tag:           safeStr(card.tag, ''),
    colSpan,
    rowSpan,
    tall:          rowSpan > 1,
    borderRadius:  radius,
    defaultFace:   safeInt(card.defaultFace, 0),
    rotationDir:   safeStr(card.rotationDir, 'ltr'),
    faces,
    // Flip animation â€” 3-D card rotation on tap, matches UI reference video.
    flip: {
      enabled:          true,
      axis:             'y',
      durationMs:       380,
      curve:            'easeInOutCubic',
      perspectiveDepth: 1200,
      trigger:          'onTap',
      faceCount:        4,
      currentFace:      safeInt(card.defaultFace, 0),
    },
    // Responsive card size overrides.
    responsiveLayout: {
      narrow:   { colSpan: Math.min(colSpan, 2), rowSpan },
      compact:  { colSpan, rowSpan },
      medium:   { colSpan, rowSpan },
      expanded: { colSpan, rowSpan },
    },
  };
}

// â”€â”€â”€ grid component â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function buildGridComponent(cards, theme) {
  const cols = safeInt(theme.gridColumns, 3);
  const gap  = safeInt(theme.gridGap, 12);
  const rh   = safeInt(theme.gridRowHeight, 96);
  const usedAreas = Object.create(null);

  return {
    type:       'cardGrid',
    id:         'home-grid',
    columns:    cols,
    gap,
    rowHeight:  rh,
    // Responsive columns / gap per breakpoint.
    responsiveLayout: {
      narrow:   { columns: 2, gap: 10, rowHeight: rh },
      compact:  { columns: cols,  gap,      rowHeight: rh },
      medium:   { columns: cols + 1, gap: 14, rowHeight: rh, maxContentWidth: 720 },
      expanded: { columns: cols + 2, gap: 16, rowHeight: rh, maxContentWidth: 1024 },
    },
    cards: cards.map((c, index) => {
      const id = safeStr(c && c.id, 'card-' + index);
      let area = id;
      if (usedAreas[area]) {
        area = id + '-' + index;
      }
      usedAreas[area] = true;
      return buildCardSpec(c, theme, area);
    }),
  };
}

// â”€â”€â”€ welcome banner â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function buildWelcomeBanner(welcome, theme) {
  return {
    type:        'welcomeBanner',
    id:          'welcome-banner',
    visible:     true,
    greeting:    safeGreeting(welcome.greeting, 'Welcome back'),
    email:       safeStr(welcome.email, ''),
    caption:     safeStr(welcome.caption, 'Manage your ecosystem'),
    background: {
      type:   'linearGradient',
      start:  safeColor(theme.welcomeStart, '#7c3aed'),
      end:    safeColor(theme.welcomeEnd,   '#4f46e5'),
      angle:  135,
    },
    textColor:   safeColor(theme.welcomeTextColor, '#ffffff'),
    borderRadius: 20,
    padding:     { top: 18, right: 18, bottom: 18, left: 18 },
    // Responsive padding.
    responsiveLayout: {
      narrow:   { padding: { top: 14, right: 14, bottom: 14, left: 14 } },
      compact:  { padding: { top: 18, right: 18, bottom: 18, left: 18 } },
      medium:   { padding: { top: 20, right: 24, bottom: 20, left: 24 } },
      expanded: { padding: { top: 24, right: 32, bottom: 24, left: 32 } },
    },
    avatarPlaceholder: {
      type:         'initial',
      resolveFrom:  'email',
      size:         42,
      borderRadius: 21,
      background:   'rgba(255,255,255,0.22)',
    },
  };
}

// â”€â”€â”€ alert strip â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function buildAlertStrip(alerts, theme) {
  return {
    type:        'alertStrip',
    id:          'alert-strip',
    visible:     true,
    count:       safeInt(alerts.count, 0),
    newUsers:    safeInt(alerts.newUsers, 0),
    badgeText:   safeStr(alerts.badgeText, 'real-time'),
    showBadge:   alerts.showBadge !== false,
    background:  safeColor(theme.alertBg, '#151b2e'),
    textColor:   safeColor(theme.textSecondary, '#e2e8f0'),
    badgeColor:  safeColor(theme.accentGreen, '#22c55e'),
    padding:     { top: 10, right: 16, bottom: 10, left: 16 },
    borderRadius: 14,
    liveIndicator: { color: '#22c55e', pulseAnimation: true },
  };
}

// â”€â”€â”€ bottom nav â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function buildBottomNav(nav, theme) {
  const navBg = safeColor(theme.navBg, '#000000');
  const isLightNav = isLightColor(navBg);
  return {
    type:        'bottomNav',
    id:          'bottom-nav',
    visible:     true,
    background:  navBg,
    activeColor: safeColor(theme.navActiveColor, '#3b82f6'),
    inactiveColor: isLightNav ? 'rgba(15,23,42,0.45)' : 'rgba(255,255,255,0.38)',
    height:      60,
    safeArea:    true,
    items: Array.isArray(nav) ? nav.map(item => ({
      id:     safeStr(item.id, ''),
      icon:   safeStr(item.icon, 'fa-circle'),
      label:  safeStr(item.label, ''),
      active: item.active === true,
    })) : [],
  };
}

function isLightColor(hex) {
  const h = String(hex || '').replace('#', '');
  if (!/^[0-9a-fA-F]{6}$/.test(h)) return false;
  const r = parseInt(h.slice(0, 2), 16);
  const g = parseInt(h.slice(2, 4), 16);
  const b = parseInt(h.slice(4, 6), 16);
  // Relative luminance threshold
  return ((0.299 * r) + (0.587 * g) + (0.114 * b)) > 180;
}

function applyStaticUiTheme(theme, staticKit) {
  const accent = safeColor(staticKit.navActive, '#db2777');
  theme.phoneBg = staticKit.phoneBg || '#ffffff';
  theme.navBg = staticKit.navBg || '#ffffff';
  theme.navActiveColor = accent;
  theme.accentColor = accent;
  theme.accentGreen = safeColor(theme.accentGreen, '#16a34a');
  theme.bgStyle = 'light';
  theme.surfaceColor = '#ffffff';
  theme.textPrimary = '#111827';
  theme.textSecondary = '#374151';
  theme.textMuted = '#6b7280';
  theme.alertBg = '#ffffff';
  theme.staticUiTemplate = staticKit.id;
  return theme;
}

// â”€â”€â”€ page header â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function buildPageHeader(header, cfg) {
  return {
    type:       'pageHeader',
    id:         'page-header',
    visible:    true,
    title:      safeStr(header.title, 'eFactor Â· IoE'),
    subtitle:   safeStr(header.subtitle, 'Smart insights & tools'),
    logoUrl:    safeStr(cfg.logoUrl, ''),
    actions: [
      { id: 'notif', icon: 'fa-bell',     actionType: 'navigate', target: 'alerts'   },
      { id: 'qr',    icon: 'fa-qrcode',   actionType: 'navigate', target: 'addDevice' },
      { id: 'avatar',icon: 'fa-user',     actionType: 'navigate', target: 'profile'  },
    ],
  };
}

// â”€â”€â”€ IoT summary card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function buildIotSummaryCard(cfg) {
  return {
    type:        'iotSummaryCard',
    id:          'iot-summary',
    visible:     true,
    dataSource:  'iotDevices',
    apiUrl:      normalizeIotApiUrl(cfg.iotApiUrl),
    label:       'Internet of Things',
    borderRadius: 18,
    background:  'linear-gradient(135deg,#1e3a5f,#0f2040)',
    textColor:   '#ffffff',
    iconColor:   '#38bdf8',
    action:      { type: 'navigate', target: 'iotDashboard' },
    // Responsive sizing.
    responsiveLayout: {
      narrow:   { colSpan: 2, padding: { top: 12, right: 12, bottom: 12, left: 12 } },
      compact:  { colSpan: 2, padding: { top: 14, right: 14, bottom: 14, left: 14 } },
      medium:   { colSpan: 2, padding: { top: 16, right: 18, bottom: 16, left: 18 } },
      expanded: { colSpan: 2, padding: { top: 18, right: 22, bottom: 18, left: 22 } },
    },
  };
}

// â”€â”€â”€ Static UI kits (Settings â†’ UI Templates) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function loadAhpStaticUi() {
  try {
    const fs = require('fs');
    const path = require('path');
    const vm = require('vm');
    const candidates = [
      path.join(__dirname, 'app_home_static_ui.js'),
      path.join(__dirname, '..', '..', '..', 'assets', 'js', 'app_home_static_ui.js'),
      path.join(__dirname, '..', '..', 'assets', 'js', 'app_home_static_ui.js'),
    ];
    for (const file of candidates) {
      if (!fs.existsSync(file)) continue;
      const ctx = { console, globalThis: {}, window: {} };
      ctx.window = ctx.globalThis;
      vm.createContext(ctx);
      vm.runInContext(fs.readFileSync(file, 'utf8'), ctx);
      if (ctx.globalThis.AhpStaticUi && typeof ctx.globalThis.AhpStaticUi.toSchema === 'function') {
        return ctx.globalThis.AhpStaticUi;
      }
    }
  } catch (e) {
    process.stderr.write('provision_app_home_schema: AhpStaticUi load skipped: ' + e.message + '\n');
  }
  return null;
}

const AhpStaticUi = loadAhpStaticUi();

function resolveStaticUiTemplateId(layout) {
  const theme = (layout && layout.theme && typeof layout.theme === 'object') ? layout.theme : {};
  return safeStr(layout.staticUiTemplate || theme.staticUiTemplate, '');
}

function resolveHomeTemplateId(layout) {
  const theme = (layout && layout.theme && typeof layout.theme === 'object') ? layout.theme : {};
  return safeStr(theme.homeTemplate || layout.homeTemplate, 'default');
}

function buildStaticHomeUiComponent(templateId, cfg) {
  if (!templateId || !AhpStaticUi) return null;
  const kit = AhpStaticUi.toSchema(templateId, cfg || {});
  if (!kit) return null;
  return Object.assign({
    type: 'staticHomeUi',
    id: 'static-home-ui',
    visible: true,
  }, kit);
}

// â”€â”€â”€ main schema builder â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function buildSchema(layout, cfg) {
  cfg = cfg || {};
  layout = layout || {};
  const themeIn  = (layout.theme    && typeof layout.theme    === 'object') ? layout.theme    : {};
  const welcome  = (layout.welcome  && typeof layout.welcome  === 'object') ? layout.welcome  : {};
  const alerts   = (layout.alerts   && typeof layout.alerts   === 'object') ? layout.alerts   : {};
  const headerIn = (layout.header   && typeof layout.header   === 'object') ? layout.header   : {};
  let nav        = Array.isArray(layout.bottomNav) ? layout.bottomNav : [];
  const cards    = Array.isArray(layout.cards)     ? layout.cards     : [];

  const staticUiTemplateId = resolveStaticUiTemplateId(layout);
  const homeTemplateId = resolveHomeTemplateId(layout);
  const staticKit = staticUiTemplateId && AhpStaticUi ? AhpStaticUi.get(staticUiTemplateId) : null;
  const staticHomeUi = buildStaticHomeUiComponent(staticUiTemplateId, cfg);
  const staticActive = !!(staticHomeUi && staticKit);

  // Merge static kit appearance into theme (overwrite dark leftovers so Flutter matches preview).
  const theme = Object.assign({}, themeIn);
  if (staticActive) {
    applyStaticUiTheme(theme, staticKit);
    if (Array.isArray(staticKit.bottomNav) && staticKit.bottomNav.length) {
      nav = JSON.parse(JSON.stringify(staticKit.bottomNav));
    }
  }
  theme.homeTemplate = homeTemplateId;
  if (staticUiTemplateId) theme.staticUiTemplate = staticUiTemplateId;

  const header = Object.assign({}, headerIn);
  if (staticActive) {
    // Match studio: brand is inside staticHomeUi, not the IoE cube header.
    header.title = 'eFactor';
    header.subtitle = staticKit.category
      ? (staticKit.label + ' Â· ' + staticKit.category)
      : (staticKit.label || 'Smart ecosystem');
  }

  const schemaVersion = '3.0.0';
  const generatedAt   = new Date().toISOString();
  const userId = safeInt(cfg.userId, 0) || null;
  const logoUrl = safeStr(cfg.logoUrl, 'https://efactor.tech/files/home/Logo_new.png');

  const pageHeader = buildPageHeader(header, Object.assign({}, cfg, { logoUrl: logoUrl }));
  const welcomeBanner = buildWelcomeBanner(welcome, theme);
  const alertStrip = buildAlertStrip(alerts, theme);
  const iotSummaryCard = buildIotSummaryCard(cfg);
  const grid = buildGridComponent(cards, theme);
  const bottomNav = buildBottomNav(nav, theme);

  // When a UI template is selected, hide cube-home strips â€” Flutter must render staticHomeUi only.
  let renderOrder = ['pageHeader', 'welcomeBanner', 'alertStrip', 'iotSummaryCard', 'grid'];
  if (staticActive) {
    pageHeader.visible = false;
    welcomeBanner.visible = false;
    alertStrip.visible = false;
    iotSummaryCard.visible = false;
    grid.visible = false;
    // Studio preview does not show pageHeader; only staticHomeUi (+ bottom nav outside page).
    renderOrder = ['staticHomeUi'];
  }

  const components = {
    pageHeader:     pageHeader,
    welcomeBanner:  welcomeBanner,
    alertStrip:     alertStrip,
    iotSummaryCard: iotSummaryCard,
    grid:           grid,
    bottomNav:      bottomNav,
  };
  if (staticHomeUi) {
    components.staticHomeUi = staticHomeUi;
  }

  return {
    schemaVersion,
    kind:          'efactor.app-home.sdui',
    generatedAt,
    generatedBy:   'provision_app_home_schema.cjs',
    userId,
    schemaApiUrl:  normalizeSchemaApiUrl(cfg.schemaApiUrl, userId),

    // â”€â”€ Root responsive contract â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    responsive: buildResponsiveContract(),

    // â”€â”€ App-level config â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    app: {
      name:              'eFactor',
      initialPageId:     safeStr(layout.activePage, 'home'),
      safeArea:          true,
      orientation:       'portrait|landscape',
      keyboardAvoidance: true,
      bgStyle:           safeStr(theme.bgStyle, 'dark'),
      background:        safeColor(theme.phoneBg, '#0b0f1a'),
      homeTemplate:      homeTemplateId,
      staticUiTemplate:  staticUiTemplateId || null,
      uiMode:            staticActive ? 'staticHomeUi' : 'cubeHome',
    },

    // â”€â”€ Theme tokens â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    theme: {
      accentColor:        safeColor(theme.accentColor,       '#7c3aed'),
      accentGreen:        safeColor(theme.accentGreen,       '#22c55e'),
      surfaceColor:       safeColor(theme.surfaceColor,      '#151b2e'),
      phoneBg:            safeColor(theme.phoneBg,           '#0b0f1a'),
      textPrimary:        safeColor(theme.textPrimary,       '#ffffff'),
      textSecondary:      safeColor(theme.textSecondary,     '#e2e8f0'),
      textMuted:          safeColor(theme.textMuted,         '#64748b'),
      navActiveColor:     safeColor(theme.navActiveColor,    '#3b82f6'),
      navBg:              safeColor(theme.navBg,             '#000000'),
      cardBorderRadius:   safeInt(theme.cardBorderRadius,     20),
      gridGap:            safeInt(theme.gridGap,              12),
      gridColumns:        safeInt(theme.gridColumns,           3),
      gridRows:           safeInt(theme.gridRows,              4),
      gridRowHeight:      safeInt(theme.gridRowHeight,        96),
      homeTemplate:       homeTemplateId,
      staticUiTemplate:   staticUiTemplateId || null,
      // Card flip animation tokens â€” global defaults.
      cardFlip: {
        enabled:          !staticActive,
        axis:             'y',
        durationMs:       380,
        curve:            'easeInOutCubic',
        perspectiveDepth: 1200,
        faceCount:        4,
      },
    },

    // â”€â”€ Components â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    components: components,

    // â”€â”€ Page layout â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    page: {
      id:              safeStr(layout.activePage, 'home'),
      scrollable:      true,
      safeArea:        true,
      keyboardAvoidance: false,
      background:      safeColor(theme.phoneBg, '#0b0f1a'),
      padding:         { top: 0, right: 0, bottom: 80, left: 0 },
      renderOrder:     renderOrder,
      homeTemplate:    homeTemplateId,
      staticUiTemplate: staticUiTemplateId || null,
      uiMode:          staticActive ? 'staticHomeUi' : 'cubeHome',
      // Responsive page padding per breakpoint.
      responsiveLayout: {
        narrow:   { padding: { top: 0, right: 0, bottom: 80, left: 0 } },
        compact:  { padding: { top: 0, right: 0, bottom: 80, left: 0 } },
        medium:   { padding: { top: 0, right: 24, bottom: 80, left: 24 }, maxContentWidth: 720 },
        expanded: { padding: { top: 0, right: 48, bottom: 80, left: 48 }, maxContentWidth: 1024 },
      },
    },

    // â”€â”€ Navigation state â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    navigation: {
      activePage:  safeStr(layout.activePage, 'home'),
      bottomNav:   bottomNav,
    },

    // â”€â”€ Profile page â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    profilePage: layout.profilePage || {},

    // â”€â”€ Devices page â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    devicesPage: layout.devicesPage || {},
  };
}

// â”€â”€â”€ entrypoint â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
let raw = '';
process.stdin.setEncoding('utf8');
process.stdin.on('data', chunk => { raw += chunk; });
process.stdin.on('end', () => {
  let input;
  try {
    input = JSON.parse(raw);
  } catch (e) {
    process.stderr.write('provision_app_home_schema: invalid JSON on stdin: ' + e.message + '\n');
    process.exit(1);
  }

  const layout = (input && typeof input.layout === 'object' && input.layout) ? input.layout : {};
  const cfg    = (input && typeof input.cfg    === 'object' && input.cfg)    ? input.cfg    : {};

  let schema;
  try {
    schema = buildSchema(layout, cfg);
  } catch (e) {
    process.stderr.write('provision_app_home_schema: schema build error: ' + e.message + '\n');
    process.exit(1);
  }

  process.stdout.write(JSON.stringify(schema, null, 0));
  process.exit(0);
});

