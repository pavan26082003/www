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
function buildCardSpec(card, theme) {
  const type     = safeStr(card.type, 'link');
  const colSpan  = safeInt(card.colSpan, 1);
  const rowSpan  = safeInt(card.rowSpan, 1);
  const radius   = safeInt(card.borderRadius, safeInt(theme.cardBorderRadius, 20));
  const faces    = buildFaces(card);

  return {
    id:            safeStr(card.id, 'card-' + Date.now()),
    type,
    gridArea:      safeStr(card.gridArea || card.id, ''),
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
    cards: cards.map(c => buildCardSpec(c, theme)),
  };
}

// â”€â”€â”€ welcome banner â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function buildWelcomeBanner(welcome, theme) {
  return {
    type:        'welcomeBanner',
    id:          'welcome-banner',
    visible:     true,
    greeting:    safeStr(welcome.greeting, 'ðŸ‘‹ Welcome back'),
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
  return {
    type:        'bottomNav',
    id:          'bottom-nav',
    visible:     true,
    background:  safeColor(theme.navBg, '#000000'),
    activeColor: safeColor(theme.navActiveColor, '#3b82f6'),
    inactiveColor: 'rgba(255,255,255,0.38)',
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

// â”€â”€â”€ page header â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function buildPageHeader(header, cfg) {
  return {
    type:       'pageHeader',
    id:         'page-header',
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
    apiUrl:      safeStr(cfg.iotApiUrl, 'https://efactor.tech/v8/iot_api_creation.php'),
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

// â”€â”€â”€ main schema builder â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function buildSchema(layout, cfg) {
  const theme    = (layout.theme    && typeof layout.theme    === 'object') ? layout.theme    : {};
  const welcome  = (layout.welcome  && typeof layout.welcome  === 'object') ? layout.welcome  : {};
  const alerts   = (layout.alerts   && typeof layout.alerts   === 'object') ? layout.alerts   : {};
  const header   = (layout.header   && typeof layout.header   === 'object') ? layout.header   : {};
  const nav      = Array.isArray(layout.bottomNav) ? layout.bottomNav : [];
  const cards    = Array.isArray(layout.cards)     ? layout.cards     : [];

  const schemaVersion = '3.0.0';
  const generatedAt   = new Date().toISOString();

  return {
    schemaVersion,
    kind:          'efactor.app-home.sdui',
    generatedAt,
    generatedBy:   'provision_app_home_schema.cjs',
    userId:        safeInt((cfg || {}).userId, 0) || null,
    schemaApiUrl:  safeStr((cfg || {}).schemaApiUrl, ''),

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
      // Card flip animation tokens â€” global defaults.
      cardFlip: {
        enabled:          true,
        axis:             'y',
        durationMs:       380,
        curve:            'easeInOutCubic',
        perspectiveDepth: 1200,
        faceCount:        4,
      },
    },

    // â”€â”€ Components â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    components: {
      pageHeader:     buildPageHeader(header, cfg || {}),
      welcomeBanner:  buildWelcomeBanner(welcome, theme),
      alertStrip:     buildAlertStrip(alerts, theme),
      iotSummaryCard: buildIotSummaryCard(cfg || {}),
      grid:           buildGridComponent(cards, theme),
      bottomNav:      buildBottomNav(nav, theme),
    },

    // â”€â”€ Page layout â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    page: {
      id:              safeStr(layout.activePage, 'home'),
      scrollable:      true,
      safeArea:        true,
      keyboardAvoidance: false,
      background:      safeColor(theme.phoneBg, '#0b0f1a'),
      padding:         { top: 0, right: 0, bottom: 80, left: 0 },  // 80 = bottom nav height
      renderOrder:     ['pageHeader', 'welcomeBanner', 'alertStrip', 'iotSummaryCard', 'grid'],
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
      bottomNav:   buildBottomNav(nav, theme),
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

