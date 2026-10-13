/**
 * App Home Page â€” SDUI Schema Builder (canonical bundle)
 * Merges app_home_schema_v2.js (core) + app_home_schema_v3.js (enrichments).
 * Load before app_home_page.js.
 */

/**
 * App Home Page â€” Server-Driven UI Schema v2.0.0 builder
 * Platform-independent semantic schema for Flutter / Web / Android / iOS
 */
(function (global) {
  'use strict';

  var FA_TOKEN_MAP = {
    'fa-home': 'home',
    'fa-th': 'devices',
    'fa-th-large': 'devices',
    'fa-border-all': 'grid',
    'fa-users': 'users',
    'fa-user-tie': 'users',
    'fa-user-friends': 'users',
    'fa-bolt': 'bolt',
    'fa-tint': 'humidity',
    'fa-plus': 'plus',
    'fa-sync': 'refresh',
    'fa-sync-alt': 'refresh',
    'fa-bell': 'alerts',
    'fa-chart-bar': 'reports',
    'fa-chart-line': 'reports',
    'fa-cog': 'settings',
    'fa-crosshairs': 'cube',
    'fa-cube': 'cube',
    'fa-image': 'image-placeholder',
    'fa-images': 'image-placeholder',
    'fa-key': 'key',
    'fa-fingerprint': 'fingerprint',
    'fa-link': 'link',
    'fa-qrcode': 'qrcode',
    'fa-wifi': 'devices',
    'fa-bluetooth-b': 'devices',
    'fa-video': 'devices',
    'fa-lock': 'lock',
    'fa-plug': 'devices',
    'fa-water': 'humidity',
    'fa-fan': 'devices',
    'fa-wind': 'devices',
    'fa-door-open': 'devices',
    'fa-solar-panel': 'bolt',
    'fa-utensils': 'cube',
    'fa-running': 'devices',
    'fa-money-bill': 'money',
    'fa-calendar': 'clock',
    'fa-fire': 'bolt',
    'fa-check': 'check',
    'fa-phone': 'phone',
    'fa-share-alt': 'grid',
    'fa-project-diagram': 'grid',
    'fa-mobile-alt': 'mobile',
    'fa-id-card': 'id-card',
    'fa-shield-alt': 'shield',
    'fa-list': 'list',
    'fa-clipboard-list': 'reports',
    'fa-hourglass-half': 'clock',
    'fa-clock': 'clock',
    'fa-compass': 'compass',
    'fa-ellipsis-h': 'menu',
    'fa-ellipsis-v': 'menu',
    'fa-heart': 'heart',
    'fa-star': 'star',
    'fa-thumbs-up': 'check',
    'fa-map-marker-alt': 'compass',
    'fa-map': 'compass',
    'fa-sitemap': 'grid',
    'fa-network-wired': 'grid',
    'fa-microchip': 'devices',
    'fa-robot': 'devices',
    'fa-puzzle-piece': 'grid',
    'fa-cubes': 'grid',
    'fa-layer-group': 'grid',
    'fa-bullseye': 'cube',
    'fa-leaf': 'bolt',
    'fa-sun': 'bolt',
    'fa-moon': 'alerts',
    'fa-lightbulb': 'bolt',
    'fa-envelope': 'alerts',
    'fa-comment': 'alerts',
    'fa-comments': 'alerts',
    'fa-sms': 'alerts',
    'fa-paper-plane': 'grid',
    'fa-search': 'grid',
    'fa-filter': 'grid',
    'fa-sort': 'grid',
    'fa-tasks': 'reports',
    'fa-camera': 'devices',
    'fa-music': 'cube',
    'fa-file': 'reports',
    'fa-file-alt': 'reports',
    'fa-folder': 'grid',
    'fa-language': 'language',
    'fa-sign-out-alt': 'logout',
    'fa-right-from-bracket': 'logout',
    'fa-user-pen': 'profile',
    'fa-user-edit': 'profile',
    'fa-chevron-right': 'chevron-right',
    'fa-moon': 'appearance',
    'fa-shield-halved': 'shield',
    'fa-palette': 'appearance'
  };

  var FLUTTER_ICON_NAMES = {
    home: 'house',
    devices: 'tableCellsLarge',
    grid: 'borderAll',
    users: 'users',
    bolt: 'bolt',
    humidity: 'droplet',
    plus: 'plus',
    refresh: 'rotate',
    alerts: 'bell',
    reports: 'chartColumn',
    settings: 'gear',
    cube: 'cube',
    'image-placeholder': 'image',
    clock: 'clock',
    compass: 'compass',
    menu: 'ellipsis',
    heart: 'heart',
    star: 'star',
    fingerprint: 'fingerprint',
    qrcode: 'qrcode',
    'id-card': 'idCard',
    shield: 'shieldHalved',
    money: 'moneyBill',
    mobile: 'mobileScreen',
    th: 'tableCells',
    link: 'link',
    check: 'check',
    phone: 'phone',
    list: 'list',
    key: 'key',
    lock: 'lock',
    language: 'language',
    logout: 'rightFromBracket',
    profile: 'userPen',
    'chevron-right': 'chevronRight',
    appearance: 'moon'
  };

  var WEB_ICON_NAMES = {
    home: 'house',
    devices: 'table-cells-large',
    grid: 'border-all',
    users: 'users',
    bolt: 'bolt',
    humidity: 'droplet',
    plus: 'plus',
    refresh: 'rotate',
    alerts: 'bell',
    reports: 'chart-column',
    settings: 'gear',
    cube: 'cube',
    'image-placeholder': 'image',
    clock: 'clock',
    compass: 'compass',
    menu: 'ellipsis',
    heart: 'heart',
    star: 'star',
    fingerprint: 'fingerprint',
    qrcode: 'qrcode',
    'id-card': 'id-card',
    shield: 'shield-halved',
    money: 'money-bill',
    mobile: 'mobile-screen',
    th: 'table-cells',
    link: 'link',
    check: 'check',
    phone: 'phone',
    list: 'list',
    key: 'key',
    lock: 'lock',
    language: 'language',
    logout: 'right-from-bracket',
    profile: 'user-pen',
    'chevron-right': 'chevron-right',
    appearance: 'moon'
  };

  var NAV_ACTIONS = {
    home: 'navigate-home',
    devices: 'navigate-devices',
    alerts: 'navigate-alerts',
    reports: 'navigate-reports',
    profile: 'navigate-profile'
  };

  function slugToken(name) {
    return String(name || 'cube')
      .replace(/^fa-/, '')
      .replace(/[^a-z0-9]+/gi, '-')
      .replace(/^-|-$/g, '')
      .toLowerCase() || 'cube';
  }

  function faToToken(faIcon) {
    var v = String(faIcon || 'fa-cube').replace(/^fas\s+/, '').trim();
    if (v.indexOf('fa-') !== 0) v = 'fa-' + v.replace(/^fa-?/, '');
    return FA_TOKEN_MAP[v] || slugToken(v);
  }

  function phaseOffset(delaySec, durationSec) {
    var d = Math.abs(Number(delaySec) || 0);
    var dur = Number(durationSec) || 12;
    if (dur <= 0) dur = 12;
    var p = (d / dur) % 1;
    return Math.round(p * 1000) / 1000;
  }

  function bind(path, fallback) {
    var b = { '$bind': path };
    if (fallback !== undefined && fallback !== null && fallback !== '') {
      b.fallback = String(fallback);
    }
    return b;
  }

  function staticVal(value) {
    if (value === undefined || value === null) return '';
    return String(value);
  }

  function buildIconRegistry(usedTokens) {
    var registry = {};
    var tokens = {};
    usedTokens.forEach(function (t) { tokens[t] = true; });
    tokens.cube = true;
    tokens['image-placeholder'] = true;
    Object.keys(tokens).forEach(function (token) {
      registry[token] = {
        web: {
          pack: 'font-awesome-6',
          name: WEB_ICON_NAMES[token] || token.replace(/-/g, '-')
        },
        flutter: {
          pack: 'font_awesome_flutter',
          name: FLUTTER_ICON_NAMES[token] || token.replace(/-([a-z])/g, function (_, c) { return c.toUpperCase(); })
        }
      };
    });
    return registry;
  }

  function cloneJson(obj) {
    return JSON.parse(JSON.stringify(obj || {}));
  }

  var CUBE_HOLD_RATIO = 0.78;
  var CUBE_FACE_COUNT = 4;
  var DEFAULT_HOME_GRID_COLUMNS = 3;
  var GREETING_WAVE = '';
  var DEFAULT_HOME_GRID_ROW_HEIGHT = 96;

  function formatHomeGreeting(name) {
    return 'Hey, ' + (name || 'Guest');
  }

  function sanitizeWelcomeGreeting(raw) {
    var greeting = String(raw || '').trim();
    if (!greeting || /^\?+$/.test(greeting) || greeting.indexOf('\uFFFD') !== -1) {
      return 'Welcome back';
    }
    if (/welcome back/i.test(greeting)) {
      return 'Welcome back';
    }
    return greeting.replace(/\?{2,}/g, '').replace(/\uFFFD/g, '').replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, '').trim() || 'Welcome back';
  }

  function formatIndianTimestamp(date) {
    date = date || new Date();
    var utc = date.toISOString();
    try {
      var fmt = new Intl.DateTimeFormat('en-IN', {
        timeZone: 'Asia/Kolkata',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
      });
      var parts = fmt.formatToParts(date);
      var get = function (type) {
        var part = parts.find(function (p) { return p.type === type; });
        return part ? part.value : '';
      };
      var local = get('year') + '-' + get('month') + '-' + get('day') + 'T' +
        get('hour') + ':' + get('minute') + ':' + get('second') + '+05:30';
      return { utc: utc, local: local, timezone: 'Asia/Kolkata', locale: 'en-IN' };
    } catch (e) {
      var ist = new Date(date.getTime() + (5.5 * 60 * 60 * 1000));
      var pad = function (n) { return String(n).padStart(2, '0'); };
      var local = ist.getUTCFullYear() + '-' + pad(ist.getUTCMonth() + 1) + '-' + pad(ist.getUTCDate()) + 'T' +
        pad(ist.getUTCHours()) + ':' + pad(ist.getUTCMinutes()) + ':' + pad(ist.getUTCSeconds()) + '+05:30';
      return { utc: utc, local: local, timezone: 'Asia/Kolkata', locale: 'en-IN' };
    }
  }

  function resolveHomeGridColumns(theme) {
    theme = theme || {};
    if (theme.gridColumns != null && !isNaN(Number(theme.gridColumns))) {
      return Math.max(1, Math.min(6, Number(theme.gridColumns)));
    }
    return DEFAULT_HOME_GRID_COLUMNS;
  }

  function resolveHomeGridRowHeight(theme) {
    theme = theme || {};
    if (theme.gridRowHeight != null && !isNaN(Number(theme.gridRowHeight))) {
      return Math.max(72, Math.min(140, Number(theme.gridRowHeight)));
    }
    return DEFAULT_HOME_GRID_ROW_HEIGHT;
  }

  function resolveCardFaceSec(card, theme, helpers) {
    if (helpers.getCardFaceSec && card) {
      return helpers.getCardFaceSec(card);
    }
    if (card && card.faceSec != null && card.faceSec !== '' && !isNaN(parseFloat(card.faceSec))) {
      return Math.max(2, Math.min(8, parseFloat(card.faceSec)));
    }
    var speed = theme && theme.cardSpeed != null ? Number(theme.cardSpeed) : 16;
    return Math.max(2, Math.min(8, speed / CUBE_FACE_COUNT));
  }

  function resolveCardDurationMs(card, theme, helpers) {
    return Math.round(resolveCardFaceSec(card, theme, helpers) * CUBE_FACE_COUNT * 1000);
  }

  function rotationSegmentMs(durationMs) {
    return durationMs / CUBE_FACE_COUNT;
  }

  function rotationDwellMs(durationMs) {
    return Math.round(rotationSegmentMs(durationMs) * CUBE_HOLD_RATIO);
  }

  function rotationTransitionMs(durationMs) {
    return Math.round(rotationSegmentMs(durationMs) * (1 - CUBE_HOLD_RATIO));
  }

  var FACE_GEOMETRY = ['front', 'right', 'back', 'left'];

  var DEFAULT_FACE_TYPOGRAPHY = {
    stat: { iconSize: 18, titleFontSize: 9, subtitleFontSize: 22 },
    link: { iconSize: 20, titleFontSize: 12, subtitleFontSize: 9 },
    image: { iconSize: 20, titleFontSize: 12, subtitleFontSize: 9 },
    action: { iconSize: 20, titleFontSize: 12, subtitleFontSize: 9 },
    text: { iconSize: 20, titleFontSize: 12, subtitleFontSize: 9 },
    device: { iconSize: 28, titleFontSize: 10, subtitleFontSize: 10, valueFontSize: 20, locationFontSize: 8 }
  };

  function resolveFaceTypography(face, parent, faceType, theme) {
    face = face || {};
    parent = parent || {};
    theme = theme || {};
    var defaults = DEFAULT_FACE_TYPOGRAPHY[faceType] || DEFAULT_FACE_TYPOGRAPHY.link;
    var iconSize = face.iconSize != null && face.iconSize !== '' ? Number(face.iconSize)
      : (parent.iconSize != null && parent.iconSize !== '' ? Number(parent.iconSize) : defaults.iconSize);
    var titleFontSize = face.titleFontSize != null && face.titleFontSize !== ''
      ? Number(face.titleFontSize)
      : (parent.titleFontSize != null && parent.titleFontSize !== '' ? Number(parent.titleFontSize) : defaults.titleFontSize);
    var subtitleFontSize = face.subFontSize != null && face.subFontSize !== ''
      ? Number(face.subFontSize)
      : (parent.subFontSize != null && parent.subFontSize !== '' ? Number(parent.subFontSize) : defaults.subtitleFontSize);
    var titleFontFamily = face.titleFontFamily || parent.titleFontFamily || 'system';
    var subtitleFontFamily = face.subFontFamily || parent.subFontFamily || 'system';
    var out = {
      iconSize: iconSize,
      titleFontSize: titleFontSize,
      subtitleFontSize: subtitleFontSize,
      titleFontFamily: titleFontFamily,
      subtitleFontFamily: subtitleFontFamily
    };
    if (faceType === 'stat') {
      out.statValueFontSize = subtitleFontSize;
      out.statLabelFontSize = titleFontSize;
    }
    if (faceType === 'device') {
      out.valueFontSize = face.valueFontSize != null ? Number(face.valueFontSize) : (defaults.valueFontSize || 20);
      out.locationFontSize = face.locFontSize != null ? Number(face.locFontSize) : (defaults.locationFontSize || 8);
    }
    return out;
  }

  function resolveFaceColors(face, parent, theme) {
    face = face || {};
    parent = parent || {};
    theme = theme || {};
    return {
      backgroundColor: face.color || face.bgColor || parent.color || parent.bgColor || '#334155',
      titleTextColor: face.titleTextColor || face.textColor || parent.textColor || theme.textPrimary || '#ffffff',
      subtitleTextColor: face.subTextColor || face.textColor || parent.textColor || theme.textPrimary || '#ffffff',
      iconColor: face.iconColor || parent.iconColor || theme.accentColor || '#ffffff'
    };
  }

  function buildCubeGeometryContract() {
    return {
      model: 'fourFaceVerticalCube',
      axis: 'y',
      degreesPerFace: 90,
      direction: 'clockwise',
      depthFormula: 'slotWidth / 2',
      sizeFormula: 'slotWidth',
      halfDepthFormula: 'slotWidth / 2',
      perspectiveDp: 700,
      transformStyle: 'preserve-3d',
      backfaceVisibility: 'hidden',
      facePlacement: FACE_GEOMETRY.map(function (name, i) {
        return {
          faceIndex: i,
          geometryFace: name,
          cssClass: 'face-' + i,
          baseTransform: 'rotateY(' + (i * 90) + 'deg) translateZ({halfDepth})'
        };
      })
    };
  }

  function buildCubeAnimationContract(durationMs, phaseOffsetSec) {
    var segmentMs = rotationSegmentMs(durationMs);
    return {
      driver: 'procedural-rAF',
      algorithm: 'segmented-hold-then-ease-flip',
      axis: 'y',
      direction: 'clockwise',
      faceCount: CUBE_FACE_COUNT,
      durationMs: durationMs,
      faceDurationMs: Math.round(segmentMs),
      phaseOffsetSec: phaseOffsetSec || 0,
      holdRatio: CUBE_HOLD_RATIO,
      transitionRatio: 1 - CUBE_HOLD_RATIO,
      holdMs: rotationDwellMs(durationMs),
      transitionMs: rotationTransitionMs(durationMs),
      easing: {
        name: 'ease-in-out-cubic',
        implementation: 't < 0.5 ? 4*t*t*t : 1 - Math.pow(-2*t + 2, 3) / 2'
      },
      angleCalculation: {
        baseAngleDeg: '-floor(elapsed / segmentSec) % 4 * 90',
        flipProgress: 'max(0, (elapsedInSegment - holdSec) / flipSec)',
        currentAngleDeg: 'baseAngle - ease(flipProgress) * 90'
      },
      stagger: { enabled: true, source: 'card-index-hash-offset' }
    };
  }

  function buildCardRotationBlock(card, index, theme, helpers, totalCards) {
    var durationMs = resolveCardDurationMs(card, theme, helpers);
    var cardSpeedSec = durationMs / 1000;
    var phaseOffsetSec = helpers.getCardAnimOffset
      ? helpers.getCardAnimOffset(card.id, index)
      : 0;
    return {
      enabled: true,
      presetId: 'tile-face-rotation',
      rotationDir: card.rotationDir === 'ltr' ? 'ltr' : 'rtl',
      rotationDirection: card.rotationDir === 'ltr' ? 'left-to-right' : 'right-to-left',
      phaseOffset: phaseOffset(phaseOffsetSec, cardSpeedSec),
      phaseOffsetSec: phaseOffsetSec,
      phaseOffsetMs: Math.round(Math.abs(phaseOffsetSec) * 1000),
      durationMs: durationMs,
      faceCount: CUBE_FACE_COUNT,
      faceDurationMs: Math.round(durationMs / CUBE_FACE_COUNT),
      dwellMs: rotationDwellMs(durationMs),
      transitionMs: rotationTransitionMs(durationMs),
      dwellRatio: CUBE_HOLD_RATIO,
      transitionRatio: 1 - CUBE_HOLD_RATIO,
      geometry: buildCubeGeometryContract(),
      animation: buildCubeAnimationContract(durationMs, phaseOffsetSec),
      cssDelayVar: '--ahp-cube-delay',
      cssDelayValueSec: phaseOffsetSec
    };
  }

  function buildUiRenderContract(theme) {
    theme = theme || {};
    var cycleMs = theme.cardSpeed != null ? Number(theme.cardSpeed) * 1000 : 16000;
    var isLight = theme.bgStyle === 'light';
    return {
      version: '1.0.0',
      reference: 'app_home_page.js',
      phone: {
        designWidth: 375,
        designHeight: 812,
        safeArea: true
      },
      grid: {
        columns: resolveHomeGridColumns(theme),
        gap: theme.gridGap != null ? Number(theme.gridGap) : 12,
        rowHeightDp: resolveHomeGridRowHeight(theme),
        minCardRowHeightDp: resolveHomeGridRowHeight(theme),
        cardWidthFormula: '(cellWidth * columnSpan) + ((columnSpan - 1) * gap)',
        cardHeightFormula: '(rowHeight * rowSpan) + ((rowSpan - 1) * gap)'
      },
      cubeGeometry: buildCubeGeometryContract(),
      cubeAnimation: buildCubeAnimationContract(cycleMs, 0),
      themeCssVariables: {
        '--ahp-phone-bg': theme.phoneBg || '#f3f4f6',
        '--ahp-surface': theme.surfaceColor || theme.alertBg || '#ffffff',
        '--ahp-grid-gap': (theme.gridGap != null ? Number(theme.gridGap) : 12) + 'px',
        '--ahp-grid-columns': String(resolveHomeGridColumns(theme)),
        '--ahp-grid-row-height': resolveHomeGridRowHeight(theme) + 'px',
        '--ahp-card-speed': (theme.cardSpeed != null ? Number(theme.cardSpeed) : 16) + 's',
        '--ahp-card-shadow': isLight
          ? '0 18px 40px rgba(15,23,42,.08)'
          : '0 18px 40px rgba(0,0,0,.35)'
      },
      faceTypographyDefaults: DEFAULT_FACE_TYPOGRAPHY,
      homeHeader: {
        greetingFontSize: 13,
        greetingFontWeight: 600,
        titleFontSize: 26,
        titleFontWeight: 800,
        titleLineHeight: 1.08,
        titleLetterSpacing: -0.03,
        subtitleFontSize: 12,
        avatarSize: 46,
        avatarFontSize: 15,
        avatarFontWeight: 800,
        layoutGap: 12,
        marginBottom: 14
      },
      bottomNavigation: buildBottomNavAppearance(theme),
      profilePage: buildProfileAppearanceContract(theme)
    };
  }

  function resolveMediaUrl(url, cfg) {
    var src = String(url || '').trim();
    if (!src) return '';
    if (/^https?:\/\//i.test(src) || src.indexOf('/') === 0 || src.indexOf('files/') === 0) return src;
    var base = (cfg && cfg.filesBase) ? String(cfg.filesBase).replace(/\/+$/, '') : '/files';
    return base + '/brand_logo/' + encodeURIComponent(src);
  }

  function normalizeLayoutSnapshotCard(card, theme, cfg) {
    var c = cloneJson(card || {});
    ensureCard(c);
    c.columnSpan = c.colSpan;
    c.rowSpan = c.rowSpan;
    c.color = c.color || c.bgColor || '#334155';
    c.image = resolveMediaUrl(c.image || '', cfg);
    c.faces = (c.faces || []).map(function (face, fi) {
      var f = cloneJson(face || {});
      if (!f.color && c.color) f.color = c.color;
      if (!f.icon && c.icon) f.icon = c.icon;
      f.image = resolveMediaUrl(f.image || (fi === 0 ? c.image : ''), cfg);
      return {
        title: f.title || f.label || '',
        subtitle: f.subtitle || f.value || '',
        type: f.type || '',
        icon: f.icon || c.icon || '',
        color: f.color || c.color || '',
        image: f.image || '',
        route: f.route || (fi === 0 ? c.pageRoute : '') || ''
      };
    });
    return c;
  }

  function buildLayoutSnapshot(layout, cfg) {
    layout = layout || {};
    cfg = cfg || {};
    var theme = layout.theme || {};
    return {
      version: 1,
      activePage: layout.activePage || 'home',
      theme: cloneJson(theme),
      header: cloneJson(layout.header),
      welcome: cloneJson(layout.welcome),
      alerts: cloneJson(layout.alerts),
      cards: (layout.cards || []).map(function (card) {
        return normalizeLayoutSnapshotCard(card, theme, cfg);
      }),
      devicesPage: cloneJson(layout.devicesPage),
      profilePage: cloneJson(layout.profilePage),
      bottomNav: cloneJson(layout.bottomNav),
      applicationCube: cloneJson(layout.applicationCube),
      grid: {
        rows: theme.gridRows || 3,
        columns: resolveHomeGridColumns(theme),
        gap: theme.gridGap != null ? Number(theme.gridGap) : 12,
        rowHeightDp: resolveHomeGridRowHeight(theme),
        minCardRowHeightDp: resolveHomeGridRowHeight(theme),
        defaultFaceSec: resolveCardFaceSec(null, theme, {}),
        defaultCycleDurationMs: (theme.cardSpeed != null ? Number(theme.cardSpeed) : 16) * 1000
      },
      animation: {
        holdRatio: CUBE_HOLD_RATIO,
        transitionRatio: 1 - CUBE_HOLD_RATIO,
        globalFaceSec: resolveCardFaceSec(null, theme, {})
      }
    };
  }

  function buildDesignTokens(theme) {
    theme = theme || {};
    var isLight = theme.bgStyle === 'light';
    return {
      colors: {
        pageBackground: theme.phoneBg || (isLight ? '#f3f4f6' : '#0b0f1a'),
        surface: theme.surfaceColor || theme.alertBg || (isLight ? '#ffffff' : '#151b2e'),
        surfaceColor: theme.surfaceColor || theme.alertBg || (isLight ? '#ffffff' : '#151b2e'),
        surfaceElevated: theme.surfaceColor || theme.alertBg || (isLight ? '#ffffff' : '#151b2e'),
        accent: theme.accentColor || (isLight ? '#14b8a6' : '#7c3aed'),
        accentGreen: theme.accentGreen || '#22c55e',
        textPrimary: theme.textPrimary || (isLight ? '#111827' : '#ffffff'),
        textSecondary: theme.textSecondary || (isLight ? '#374151' : '#dbe4ef'),
        textMuted: theme.textMuted || (isLight ? '#6b7280' : '#94a3b8'),
        welcomeGradientStart: theme.welcomeStart || '#7c3aed',
        welcomeGradientEnd: theme.welcomeEnd || '#4f46e5',
        avatarGradientStart: theme.welcomeStart || '#7c3aed',
        avatarGradientEnd: theme.welcomeEnd || '#4f46e5',
        navBackground: theme.navBg || '#000000',
        navActive: theme.navActiveColor || '#3b82f6',
        welcomeText: theme.welcomeTextColor || '#ffffff',
        border: isLight ? 'rgba(17,24,39,.08)' : 'rgba(255,255,255,.10)',
        online: '#22c55e',
        offline: '#94a3b8',
        danger: '#ef4444',
        warning: '#f59e0b'
      },
      spacing: { xxs: 2, xs: 4, sm: 8, md: 12, lg: 16, xl: 24, gridGap: theme.gridGap != null ? Number(theme.gridGap) : 12 },
      typography: {
        homeGreeting: { fontSize: 13, fontWeight: 600, lineHeight: 1.2, colorToken: 'colors.textMuted' },
        homeTitle: { fontSize: 26, fontWeight: 800, lineHeight: 1.08, letterSpacing: -0.03, colorToken: 'colors.textPrimary' },
        homeSubtitle: { fontSize: 12, fontWeight: 400, lineHeight: 1.3, colorToken: 'colors.textMuted' }
      },
      radius: { small: 8, card: 12, device: 16, large: 24, pill: 999 },
      shadows: {
        card: isLight
          ? { color: '#140f172a', blur: 40, spread: 0, offsetX: 0, offsetY: 18, css: '0 18px 40px rgba(15,23,42,.08)' }
          : { color: '#59000000', blur: 40, spread: 0, offsetX: 0, offsetY: 18, css: '0 18px 40px rgba(0,0,0,.35)' }
      },
      typography: {
        pageTitle: { fontFamily: 'system', fontSize: 22, fontWeight: 700, lineHeight: 1.2, maxLines: 1, overflow: 'ellipsis' },
        pageSubtitle: { fontFamily: 'system', fontSize: 13, fontWeight: 400, lineHeight: 1.3, maxLines: 2, overflow: 'ellipsis' },
        cardTitle: { fontFamily: 'system', fontSize: 12, fontWeight: 600, lineHeight: 1.15, maxLines: 2, overflow: 'ellipsis' },
        cardSubtitle: { fontFamily: 'system', fontSize: 9, fontWeight: 400, lineHeight: 1.2, maxLines: 2, overflow: 'ellipsis' },
        statValue: { fontFamily: 'system', fontSize: 22, fontWeight: 700, lineHeight: 1 },
        statLabel: { fontFamily: 'system', fontSize: 9, fontWeight: 600, lineHeight: 1.15, maxLines: 2, overflow: 'ellipsis' },
        deviceTitle: { fontFamily: 'system', fontSize: 10, fontWeight: 600, lineHeight: 1.15, maxLines: 2, overflow: 'ellipsis' },
        deviceValue: { fontFamily: 'system', fontSize: 20, fontWeight: 700, lineHeight: 1 },
        byFaceType: DEFAULT_FACE_TYPOGRAPHY
      },
      animation: (function () {
        var cycleMs = theme.cardSpeed != null ? Number(theme.cardSpeed) * 1000 : 16000;
        return {
          cardRotationDurationMs: cycleMs,
          cardRotationFaceCount: CUBE_FACE_COUNT,
          cardRotationFaceDurationMs: Math.round(cycleMs / CUBE_FACE_COUNT),
          cardRotationDwellRatio: CUBE_HOLD_RATIO,
          cardRotationTransitionRatio: 1 - CUBE_HOLD_RATIO,
          cardRotationDwellMs: rotationDwellMs(cycleMs),
          cardRotationTransitionMs: rotationTransitionMs(cycleMs),
          cubeDepthFormula: 'cellWidth / 2',
          perspectiveDp: 700
        };
      })()
    };
  }

  function faceType(cardType, face, fi) {
    if (face.type) return face.type;
    if (cardType === 'stat') return 'stat';
    if (cardType === 'image') return fi === 0 && (face.image || cardType === 'image') ? 'image' : 'link';
    if (cardType === 'add') return 'action';
    return 'link';
  }

  var FONT_FAMILY_CSS = {
    system: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
    inter: 'Inter, system-ui, sans-serif',
    roboto: 'Roboto, Arial, sans-serif',
    poppins: 'Poppins, sans-serif',
    montserrat: 'Montserrat, sans-serif',
    'open-sans': '"Open Sans", sans-serif',
    lato: 'Lato, sans-serif',
    nunito: 'Nunito, sans-serif',
    merriweather: 'Merriweather, Georgia, serif',
    mono: 'ui-monospace, SFMono-Regular, Menlo, Consolas, monospace'
  };

  function resolveFontFamily(id) {
    return FONT_FAMILY_CSS[id] || FONT_FAMILY_CSS.system;
  }

  function buildFaceAppearance(face, parent, faceType, theme) {
    parent = parent || {};
    faceType = faceType || 'link';
    theme = theme || {};
    var colors = resolveFaceColors(face, parent, theme);
    var typography = resolveFaceTypography(face, parent, faceType, theme);
    var borderRadius = face.borderRadius != null && face.borderRadius !== ''
      ? Number(face.borderRadius)
      : (parent.borderRadius != null && parent.borderRadius !== '' ? Number(parent.borderRadius) : undefined);
    var appearance = Object.assign({}, colors, typography);
    if (borderRadius != null && !isNaN(borderRadius)) appearance.borderRadius = borderRadius;
    appearance.titleFontFamilyCss = resolveFontFamily(typography.titleFontFamily);
    appearance.subtitleFontFamilyCss = resolveFontFamily(typography.subtitleFontFamily);
    if (face.hideIcon === true) appearance.hideIcon = true;
    else appearance.showIcon = face.showIcon !== false;
    return appearance;
  }

  function buildHomeFace(face, card, fi, usedIcons, theme, cfg) {
    var cardType = card.type || 'link';
    var type = faceType(cardType, face, fi);
    var iconToken = faToToken(face.icon || card.icon);
    usedIcons[iconToken] = true;
    var appearance = buildFaceAppearance(face, card, type, theme);
    var out = {
      id: (card.id || 'card') + '-face-' + fi,
      type: type,
      faceIndex: fi,
      geometryFace: FACE_GEOMETRY[fi] || ('face-' + fi),
      iconToken: iconToken,
      showIcon: face.hideIcon !== true,
      appearance: appearance,
      typography: resolveFaceTypography(face, card, type, theme),
      contentLayout: type === 'stat'
        ? { primaryField: 'value', secondaryField: 'title', primaryRole: 'statValue', secondaryRole: 'statLabel' }
        : { primaryField: 'title', secondaryField: 'subtitle', primaryRole: 'title', secondaryRole: 'subtitle' }
    };

    if (type === 'stat') {
      out.title = face.label || face.title || card.label || card.title || '';
      out.value = bind('dashboard.cards.' + card.id + '.faces.' + fi + '.value', face.value != null ? face.value : card.value);
    } else if (type === 'image' && (face.image || card.image)) {
      out.title = face.title || card.title || '';
      out.image = resolveMediaUrl(face.image || card.image || '', cfg || {});
      out.assetId = 'asset-' + card.id + '-face-' + fi;
      out.imageFit = 'cover';
    } else if (type === 'action') {
      out.title = face.title || card.title || 'Action';
    } else {
      out.title = face.title || face.label || card.title || card.label || '';
      out.subtitle = face.subtitle != null ? staticVal(face.subtitle) : staticVal(card.subtitle);
    }
    return out;
  }

  function buildDeviceFace(face, device, fi, usedIcons, theme) {
    var iconToken = faToToken(face.icon || device.icon);
    usedIcons[iconToken] = true;
    var name = face.name || face.label || (fi === 0 ? device.name : ('Face ' + (fi + 1)));
    var value = face.value != null ? face.value : (fi === 0 ? device.value : '');
    var unit = face.unit != null ? face.unit : (fi === 0 ? device.unit : '');
    var location = face.location != null ? face.location : (fi === 0 ? device.location : '');
    var faceTypeName = value !== '' || unit !== '' ? 'device' : (location ? 'text' : 'status');
    var out = {
      id: (device.id || 'dev') + '-face-' + fi,
      type: faceTypeName,
      faceIndex: fi,
      geometryFace: FACE_GEOMETRY[fi] || ('face-' + fi),
      iconToken: iconToken,
      title: name,
      showIcon: face.showIcon !== false && face.hideIcon !== true,
      appearance: buildFaceAppearance(face, device, 'device', theme),
      typography: resolveFaceTypography(face, device, 'device', theme)
    };
    if (value !== '' || unit !== '') {
      out.value = staticVal(value);
      out.unit = staticVal(unit);
    }
    if (location) out.subtitle = staticVal(location);
    return out;
  }

  function normalizeDeviceItem(device, index, durationSec, getDeviceAnimOffset, usedIcons, theme) {
    ensureDevice(device);
    var delay = getDeviceAnimOffset(device.id, index);
    usedIcons = usedIcons || {};
    theme = theme || {};
    var iconToken = faToToken(device.icon);
    usedIcons[iconToken] = true;
    var durationMs = Math.round(Number(durationSec || 16) * 1000);
    var colSpan = Math.max(1, Math.min(3, parseInt(device.colSpan, 10) || 1));
    var rowSpan = Math.max(1, Math.min(3, parseInt(device.rowSpan, 10) || 1));
    var borderRadius = device.borderRadius != null ? Number(device.borderRadius) : 16;
    return {
      id: device.id,
      name: device.name || '',
      value: device.value != null ? String(device.value) : '',
      unit: device.unit || '',
      iconToken: iconToken,
      color: device.color || '#334155',
      textColor: device.textColor || theme.textPrimary || '#ffffff',
      location: device.location || '',
      online: device.online !== false,
      power: device.power !== false,
      sortOrder: index,
      animationPhase: phaseOffset(delay, durationSec),
      defaultFaceIndex: device.defaultFace != null ? device.defaultFace : 0,
      colSpan: colSpan,
      rowSpan: rowSpan,
      dimensions: {
        columnSpan: colSpan,
        rowSpan: rowSpan,
        borderRadius: borderRadius,
        minRowHeightDp: 96,
        cubeDepthFormula: 'cellWidth / 2',
        perspectiveDp: 650
      },
      timing: {
        usesGlobalDefault: true,
        faceSec: Math.max(2, Math.min(8, Number(durationSec || 16) / CUBE_FACE_COUNT)),
        cycleDurationMs: durationMs
      },
      rotation: Object.assign({}, buildCardRotationBlock({ id: device.id, faceSec: null }, index, theme, {
        getCardAnimOffset: function () { return delay; }
      }, 1), {
        presetId: 'device-face-rotation',
        phaseOffsetSec: delay,
        cssDelayVar: '--ahp-cube-delay',
        cssDelayValueSec: delay
      }),
      faces: (device.faces || []).slice(0, 4).map(function (f, fi) {
        return buildDeviceFace(f, device, fi, usedIcons, theme);
      })
    };
  }

  function ensureDevice(device) {
    if (!device.faces || !device.faces.length) {
      device.faces = [{ name: device.name, value: device.value, unit: device.unit, icon: device.icon, color: device.color }];
    }
    while (device.faces.length < 4) {
      device.faces.push({ name: device.name || 'Face', icon: device.icon || 'fa-cube', color: device.color });
    }
    device.faces = device.faces.slice(0, 4);
  }

  function ensureCard(card) {
    if (!card.faces || !card.faces.length) {
      card.faces = [{ value: card.value, label: card.label || card.title, icon: card.icon, color: card.color }];
    }
    while (card.faces.length < 4) card.faces.push({ title: 'Face ' + (card.faces.length + 1), icon: card.icon || 'fa-cube', color: card.color });
    card.faces = card.faces.slice(0, 4);
    /* Inherit card-level color/icon when face doesn't override */
    card.faces.forEach(function (face) {
      if (!face.color && card.color) face.color = card.color;
      if (!face.icon && card.icon) face.icon = card.icon;
    });
    card.colSpan = Math.max(1, Math.min(6, parseInt(card.colSpan, 10) || 1));
    card.rowSpan = Math.max(1, Math.min(6, parseInt(card.rowSpan, 10) || 1));
  }

  function getProfileInitials(profile, cfg) {
    profile = profile || {};
    cfg = cfg || {};
    var source = String(profile.name || profile.email || cfg.userEmail || 'User').trim();
    var parts = source.split(/\s+/).filter(Boolean);
    if (!parts.length) return 'U';
    if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
    return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
  }

  function getProfileGreetingName(profile, cfg) {
    profile = profile || {};
    cfg = cfg || {};
    var name = String(profile.name || '').trim();
    if (name && name.toLowerCase() !== 'profile') return name.split(/\s+/)[0];
    var email = String(profile.email || cfg.userEmail || '').trim();
    if (email.indexOf('@') > 0) return email.split('@')[0];
    return 'Guest';
  }

  function getThemeModeLabel(theme) {
    theme = theme || {};
    var map = {
      light: 'Light', dark: 'Dark', midnight: 'Midnight', slate: 'Slate', ocean: 'Ocean',
      purple: 'Purple', forest: 'Forest', rose: 'Rose', aurora: 'Aurora', ember: 'Ember',
      graphite: 'Graphite', glass: 'Glass'
    };
    return map[theme.bgStyle || 'light'] || 'Light';
  }

  function isIotHomeCard(card, face, fi) {
    if (!card) return false;
    var blob = [
      card.label, card.title, card.subtitle, card.id,
      face && face.title, face && face.label, face && face.value, face && face.subtitle
    ].map(function (v) { return String(v || '').toLowerCase(); }).join(' ');
    if (/\biot\b|internet of things|devices Â· iot|devices - iot|devicesÂ·iot/.test(blob)) return true;
    var route = String((face && face.route) || (fi === 0 ? card.pageRoute : '') || '').trim().toLowerCase().replace(/^\//, '');
    if (route === 'devices' || route === 'iot') return true;
    if (card.id === 'iot' || card.id === 'devices') return true;
    return false;
  }

  function resolveFaceTapActionId(card, face, fi, actionRegistry) {
    actionRegistry = actionRegistry || {};
    var route = String((face && face.route) || '').trim();
    if (!route && fi === 0) route = String(card.pageRoute || '').trim();

    if (route) {
      if (/^https?:\/\//i.test(route)) {
        var extId = 'external-nav-' + safeRoutePart(card.id, 'card') + '-face-' + fi;
        if (!actionRegistry[extId]) {
          actionRegistry[extId] = { type: 'external-navigate', url: route };
        }
        return extId;
      }
      var normalized = route.replace(/^\//, '').toLowerCase();
      if (normalized === 'devices' || normalized === 'iot') return 'open-iot-app';
      if (['home', 'profile', 'alerts', 'reports', 'add-device'].indexOf(normalized) >= 0) {
        return 'navigate-' + normalized;
      }
      var slug = normalized.replace(/[^a-z0-9]+/gi, '-').toLowerCase() || ('page-' + card.id);
      var customId = 'navigate-custom-' + safeRoutePart(card.id, 'card') + '-face-' + fi;
      if (!actionRegistry[customId]) {
        actionRegistry[customId] = {
          type: 'navigate',
          destination: { routeId: slug, parameters: { path: route } }
        };
      }
      return customId;
    }

    if (isIotHomeCard(card, face, fi)) return 'open-iot-app';

    var defaultId = cardFaceActionId(card, fi);
    if (!actionRegistry[defaultId]) {
      actionRegistry[defaultId] = {
        type: 'navigate',
        destination: {
          routeId: cardFaceRouteId(card, fi),
          parameters: {
            cardId: card.id || '',
            faceIndex: fi,
            faceTitle: (face && (face.title || face.label)) || card.title || card.label || ''
          }
        }
      };
    }
    return defaultId;
  }

  function buildBottomNavAppearance(theme) {
    theme = theme || {};
    return {
      backgroundColor: theme.navBg || '#ffffff',
      activeColorToken: 'colors.navActive',
      inactiveColorToken: 'colors.textMuted',
      showLabels: true,
      height: 62,
      marginHorizontal: 16,
      marginBottom: 12,
      borderRadius: 999,
      borderWidth: 1,
      borderColor: theme.bgStyle === 'light' ? 'rgba(17,24,39,.08)' : 'rgba(255,255,255,.10)',
      paddingHorizontal: 10,
      paddingVertical: 8,
      safeAreaInsetBottom: true,
      iconSize: 16,
      labelFontSize: 9,
      labelFontWeight: 700,
      shadowToken: 'shadows.card'
    };
  }

  function buildHomeHeaderComponent(layout, cfg, theme, usedIcons) {
    usedIcons = usedIcons || {};
    usedIcons.profile = true;
    theme = theme || {};
    layout = layout || {};
    cfg = cfg || {};
    var profile = layout.profilePage || {};
    var header = layout.header || {};
    var greetingName = getProfileGreetingName(profile, cfg);
    return {
      id: 'home-header',
      type: 'homeHeader',
      greeting: {
        text: formatHomeGreeting(greetingName),
        i18nKey: 'home.greeting',
        i18nParams: { name: greetingName },
        typography: {
          fontSize: 13,
          fontWeight: 600,
          lineHeight: 1.2,
          colorToken: 'colors.textMuted'
        }
      },
      title: {
        text: header.title || 'Welcome to SmartHome!',
        i18nKey: 'home.title',
        typography: {
          fontSize: 26,
          fontWeight: 800,
          lineHeight: 1.08,
          letterSpacing: -0.03,
          colorToken: 'colors.textPrimary'
        }
      },
      subtitle: {
        text: header.subtitle || '',
        i18nKey: 'home.subtitle',
        typography: {
          fontSize: 12,
          fontWeight: 400,
          lineHeight: 1.3,
          colorToken: 'colors.textMuted'
        }
      },
      avatar: {
        initials: getProfileInitials(profile, cfg),
        initialsBinding: {
          '$bind': 'session.user.initials',
          fallback: getProfileInitials(profile, cfg)
        },
        appearance: {
          width: 46,
          height: 46,
          borderRadius: 999,
          gradientStart: theme.welcomeStart || '#fbbf24',
          gradientEnd: theme.welcomeEnd || '#f97316',
          fontSize: 15,
          fontWeight: 800,
          textColor: '#ffffff',
          shadow: '0 10px 24px rgba(249,115,22,.28)'
        },
        events: { tap: { actionId: 'navigate-profile' } },
        accessibilityLabel: 'Open profile'
      },
      layout: {
        direction: 'row',
        align: 'flex-start',
        justify: 'space-between',
        gap: 12,
        marginBottom: 14
      }
    };
  }

  function buildProfileAppearanceContract(theme) {
    theme = theme || {};
    return {
      pagePadding: 16,
      pageGap: 12,
      cardPadding: 20,
      cardBorderRadius: 18,
      cardShadowToken: 'shadows.card',
      cardBorderColor: theme.bgStyle === 'light' ? 'rgba(17,24,39,.06)' : 'rgba(255,255,255,.10)',
      avatarSize: 64,
      avatarFontSize: 22,
      avatarFontWeight: 800,
      nameFontSize: 20,
      nameFontWeight: 800,
      emailFontSize: 13,
      metaFontSize: 12,
      listGap: 10,
      rowMinHeight: 56,
      rowPaddingVertical: 14,
      rowPaddingHorizontal: 14,
      rowBorderRadius: 14,
      rowBorderWidth: 1,
      rowBorderColor: theme.bgStyle === 'light' ? 'rgba(17,24,39,.06)' : 'rgba(255,255,255,.10)',
      rowShadowToken: 'shadows.card',
      iconBoxSize: 36,
      iconBoxBorderRadius: 10,
      iconSize: 16,
      rowTitleFontSize: 14,
      rowTitleFontWeight: 700,
      rowSubtitleFontSize: 11,
      rowSubtitleFontWeight: 500,
      chevronSize: 12,
      chevronToken: 'chevron-right',
      logoutMinHeight: 48,
      logoutBorderRadius: 14,
      logoutFontSize: 14,
      logoutFontWeight: 800
    };
  }

  function ensureSchemaIconTokens(usedIcons) {
    ['language', 'shield', 'logout', 'appearance', 'chevron-right', 'profile', 'home', 'devices', 'alerts', 'reports'].forEach(function (token) {
      usedIcons[token] = true;
    });
  }

  function cardTapActionId(card) {
    if (card.type === 'add') return 'navigate-add-device';
    if (card.pageRoute) {
      var pr = String(card.pageRoute).trim().toLowerCase().replace(/^\//, '');
      if (pr === 'devices' || pr === 'iot') return 'open-iot-app';
      var r = String(card.pageRoute).replace(/^\//, '').replace(/\//g, '-');
      return 'navigate-card-' + (card.id || r);
    }
    if (isIotHomeCard(card, null, 0)) return 'open-iot-app';
    if (card.id === 'grid') return 'open-grid-module';
    if (card.id === 'hrms') return 'open-hrms-module';
    return null;
  }


  function safeRoutePart(value, fallback) {
    return String(value || fallback || 'item').replace(/[^a-z0-9]+/gi, '-').replace(/^-+|-+$/g, '').toLowerCase() || fallback || 'item';
  }

  function cardFaceRouteId(card, faceIndex) {
    return 'card-' + safeRoutePart(card.id || card.title, 'card') + '-face-' + faceIndex;
  }

  function cardFaceActionId(card, faceIndex) {
    return 'open-' + cardFaceRouteId(card, faceIndex);
  }
  function buildSchema(layout, cfg, helpers) {
    helpers = helpers || {};
    var getCardAnimOffset = helpers.getCardAnimOffset || function () { return 0; };
    var getDeviceAnimOffset = helpers.getDeviceAnimOffset || function () { return 0; };
    var getDefaultFace = helpers.getDefaultFace || function () { return 0; };

    layout = layout || {};
    cfg = cfg || {};
    var theme = layout.theme || {};
    var userId = cfg.userId || 0;
    var userEmail = cfg.userEmail || (layout.welcome && layout.welcome.email) || '';
    var cardSpeed = theme.cardSpeed != null ? Number(theme.cardSpeed) : 16;
    var gridGap = theme.gridGap != null ? Number(theme.gridGap) : 12;
    var durationMs = cardSpeed * 1000;
    var homeGridColumns = resolveHomeGridColumns(theme);
    var homeGridRowHeight = resolveHomeGridRowHeight(theme);
    var usedIcons = {};

    var cards = (layout.cards || []).slice();
    cards.forEach(ensureCard);
    var seenIotHomeCard = false;
    cards = cards.filter(function (card) {
      if (!isIotHomeCard(card, null, 0)) return true;
      if (seenIotHomeCard) return false;
      seenIotHomeCard = true;
      return true;
    });
    var usedGridAreas = {};

    var devicesRaw = (layout.devicesPage && layout.devicesPage.devices) || [];
    var deviceItems = devicesRaw.map(function (d, i) {
      return normalizeDeviceItem(d, i, cardSpeed, getDeviceAnimOffset, usedIcons, theme);
    });

    var assets = {};
    cards.forEach(function (card) {
      (card.faces || []).forEach(function (face, fi) {
        var img = face.image || (fi === 0 ? card.image : '');
        if (img) {
          var resolvedImg = resolveMediaUrl(img, cfg);
          assets['asset-' + card.id + '-face-' + fi] = {
            type: 'network-image',
            source: resolvedImg,
            fit: 'cover',
            alignment: 'center',
            cache: true,
            timeoutMs: 10000,
            placeholderIconToken: 'image-placeholder',
            errorIconToken: 'image-placeholder'
          };
        }
      });
    });

    var actionRegistry = {
      'navigate-home': { type: 'navigate', destination: { routeId: 'home' } },
      'navigate-devices': { type: 'navigate', destination: { routeId: 'devices' } },
      'navigate-alerts': { type: 'navigate', destination: { routeId: 'alerts' } },
      'navigate-reports': { type: 'navigate', destination: { routeId: 'reports' } },
      'navigate-profile': { type: 'navigate', destination: { routeId: 'profile' } },
      'navigate-add-device': { type: 'navigate', destination: { routeId: 'add-device' } },
      'open-grid-module': { type: 'navigate', destination: { routeId: 'grid-details' } },
      'open-hrms-module': { type: 'navigate', destination: { routeId: 'hrms' } },
      'open-iot-app': { type: 'host-action', actionId: 'open-iot' },
      'refresh-dashboard': {
        type: 'sequence',
        actions: [
          { type: 'set-state', path: 'refreshing', value: true },
          { type: 'refresh-data-source', dataSourceId: 'dashboard' },
          { type: 'refresh-data-source', dataSourceId: 'devices' },
          { type: 'set-state', path: 'refreshing', value: false }
        ],
        errorPresentation: { type: 'snackbar', message: 'Unable to refresh data' }
      },
      'show-card-faces': { type: 'show-bottom-sheet', componentId: 'face-selector-sheet' },
      'control-device': {
        type: 'show-bottom-sheet',
        componentId: 'device-control-sheet',
        parameters: { deviceId: { '$event': 'componentId' } }
      },
      'toggle-device-power': {
        type: 'request',
        request: {
          method: 'POST',
          endpoint: '/api/devices/{deviceId}/power',
          pathParameters: { deviceId: { '$event': 'componentId' } },
          body: { enabled: { '$toggleState': 'devices.{deviceId}.power' } }
        },
        optimisticUpdate: true,
        loadingStatePath: 'devices.{deviceId}.updating',
        errorPresentation: { type: 'snackbar', message: 'Unable to update device' }
      },
      'show-route-error': {
        type: 'show-dialog',
        title: 'Page unavailable',
        message: 'This page has not been configured.'
      }
    };

    cards.forEach(function (card) {
      var tapId = cardTapActionId(card);
      if (tapId && !actionRegistry[tapId] && card.pageRoute) {
        var routeSlug = String(card.pageRoute).replace(/^\//, '').replace(/[^a-z0-9]+/gi, '-').toLowerCase();
        actionRegistry[tapId] = {
          type: 'navigate',
          destination: { routeId: routeSlug || ('page-' + card.id) }
        };
      }
    });

    var components = {};
    var homeGridChildren = [];

    cards.forEach(function (card, index) {
      var compId = card.id + '-card';
      homeGridChildren.push(compId);
      var tapAction = cardTapActionId(card);
      var events = { longPress: { actionId: 'show-card-faces' } };
      if (tapAction) events.tap = { actionId: tapAction };

      var faces = (card.faces || []).map(function (face, fi) {
        var builtFace = buildHomeFace(face, card, fi, usedIcons, theme, cfg);
        var tapActionId = resolveFaceTapActionId(card, face, fi, actionRegistry);
        builtFace.events = { tap: { actionId: tapActionId } };
        builtFace.routeId = cardFaceRouteId(card, fi);
        return builtFace;
      });

      var faceSec = resolveCardFaceSec(card, theme, helpers);
      var cardBorderRadius = card.borderRadius != null ? Number(card.borderRadius) : 0;
      var cardCol = card.colSpan || 1;
      var cardRow = card.rowSpan || 1;
      var orientation = card.orientation || (
        cardCol > 1 && cardRow === 1 ? 'wide' :
        cardRow > 1 && cardCol === 1 ? 'tall' :
        cardCol > 1 && cardRow > 1 ? 'large' : 'compact'
      );

      var gridArea = String(card.gridArea || card.id || compId);
      if (usedGridAreas[gridArea]) {
        gridArea = compId;
      }
      usedGridAreas[gridArea] = true;

      components[compId] = {
        id: compId,
        presetId: 'home-tile',
        cardType: card.type || 'link',
        sortOrder: index,
        gridArea: gridArea,
        orientation: orientation,
        iconToken: faToToken(card.icon),
        layout: {
          columnSpan: cardCol,
          rowSpan: cardRow
        },
        dimensions: {
          columnSpan: cardCol,
          rowSpan: cardRow,
          borderRadius: cardBorderRadius,
          minRowHeightDp: resolveHomeGridRowHeight(theme),
          cubeDepthFormula: 'slotWidth / 2',
          halfDepthFormula: 'slotWidth / 2',
          sizeFormula: 'slotWidth',
          perspectiveDp: 700
        },
        appearance: {
          backgroundColor: card.color || card.bgColor || '#334155',
          borderRadius: cardBorderRadius,
          textColor: card.textColor || theme.textPrimary || '#ffffff',
          iconColor: card.iconColor || theme.accentColor || '#ffffff',
          shadowToken: 'shadows.card'
        },
        timing: {
          faceSec: faceSec,
          cycleDurationMs: resolveCardDurationMs(card, theme, helpers),
          usesGlobalDefault: card.faceSec == null || card.faceSec === ''
        },
        rotation: buildCardRotationBlock(card, index, theme, helpers, cards.length),
        defaultFaceIndex: getDefaultFace(card),
        events: events,
        faces: faces
      };

      (card.faces || []).forEach(function (face, fi) {
        var routeId = cardFaceRouteId(card, fi);
        var headerId = routeId + '-header';
        var summaryId = routeId + '-summary';
        components[routeId + '-page'] = {
          id: routeId + '-page',
          type: 'scrollPage',
          layout: { safeArea: true, padding: 16, gap: 12 },
          parameters: { cardId: card.id || '', faceIndex: fi },
          children: [headerId, summaryId]
        };
        components[headerId] = {
          id: headerId,
          type: 'pageHeader',
          title: face.title || face.label || card.title || card.label || 'Card face',
          subtitle: face.subtitle || face.value || card.subtitle || (['Front', 'Right', 'Back', 'Left'][fi] || ('Face ' + (fi + 1))),
          iconToken: faToToken(face.icon || card.icon || 'fa-cube')
        };
        components[summaryId] = {
          id: summaryId,
          type: 'banner',
          content: {
            title: face.title || face.label || card.title || card.label || 'Card face',
            subtitle: 'Generated detail page for ' + (['Front', 'Right', 'Back', 'Left'][fi] || ('Face ' + (fi + 1))),
            supportingText: face.subtitle || face.value || card.subtitle || ''
          },
          appearance: { backgroundColor: face.color || card.color || '#202940' }
        };
      });
    });

    var headerIcon = faToToken(theme.logoIcon || 'fa-crosshairs');
    usedIcons[headerIcon] = true;

    components['main-bottom-navigation'] = {
      id: 'main-bottom-navigation',
      type: 'bottomNavigation',
      itemsSource: { '$reference': 'navigation.items' },
      activeRouteBinding: { '$state': 'activeRouteId' },
      appearance: buildBottomNavAppearance(theme)
    };

    components['home-page'] = {
      id: 'home-page',
      type: 'scrollPage',
      layout: {
        direction: 'vertical',
        safeArea: true,
        padding: { left: 10, right: 10, top: 8, bottom: 16 },
        gap: gridGap
      },
      children: ['home-header', 'welcome-banner', 'alerts-summary', 'home-grid']
    };

    components['home-header'] = buildHomeHeaderComponent(layout, cfg, theme, usedIcons);

    components['welcome-banner'] = {
      id: 'welcome-banner',
      type: 'banner',
      visibleWhen: { '$state': 'preferences.showWelcome', equals: true },
      content: {
        title: (layout.welcome && layout.welcome.greeting) || 'Welcome back',
        subtitle: (layout.welcome && layout.welcome.caption) || '',
        supportingText: userEmail
      },
      appearance: {
        background: {
          type: 'linear-gradient',
          startColor: theme.welcomeStart || '#7c3aed',
          endColor: theme.welcomeEnd || '#4f46e5',
          direction: 'left-to-right'
        },
        textColorToken: 'colors.textPrimary',
        radiusToken: 'radius.large',
        padding: 16
      }
    };

    components['alerts-summary'] = {
      id: 'alerts-summary',
      type: 'summaryBanner',
      visibleWhen: { '$state': 'preferences.showAlerts', equals: true },
      content: {
        title: 'Live Alerts',
        value: bind('dashboard.alerts.count', (layout.alerts && layout.alerts.count) != null ? layout.alerts.count : 0),
        badge: (layout.alerts && layout.alerts.badgeText) || 'real-time'
      },
      appearance: {
        backgroundColorToken: 'colors.surface',
        textColorToken: 'colors.textPrimary',
        radiusToken: 'radius.card'
      }
    };

    components['home-grid'] = {
      id: 'home-grid',
      type: 'responsiveGrid',
      layout: {
        columns: homeGridColumns,
        columnGap: gridGap,
        rowGap: gridGap,
        rowHeight: homeGridRowHeight,
        minRowHeight: homeGridRowHeight,
        independentScroll: false
      },
      children: homeGridChildren
    };

    var devPage = layout.devicesPage || {};
    var devHeader = devPage.header || {};
    components['devices-page'] = {
      id: 'devices-page',
      type: 'scrollPage',
      layout: {
        direction: 'vertical',
        safeArea: true,
        padding: { left: 10, right: 10, top: 8, bottom: 16 },
        gap: 12
      },
      children: ['devices-header', 'devices-status', 'devices-grid']
    };

    components['devices-header'] = {
      id: 'devices-header',
      type: 'pageHeader',
      title: devHeader.title || 'devices - iot',
      subtitle: devHeader.subtitle || 'Tap a cube to control.',
      typography: { titleToken: 'typography.pageTitle', subtitleToken: 'typography.pageSubtitle' }
    };

    components['devices-status'] = {
      id: 'devices-status',
      type: 'statusSummary',
      items: [
        {
          label: 'Online',
          value: bind('devices.summary.online', (devPage.status && devPage.status.online) != null ? devPage.status.online : 0),
          colorToken: 'colors.online'
        },
        {
          label: 'Offline',
          value: bind('devices.summary.offline', (devPage.status && devPage.status.offline) != null ? devPage.status.offline : 0),
          colorToken: 'colors.offline'
        }
      ]
    };

    components['devices-grid'] = {
      id: 'devices-grid',
      type: 'responsiveGrid',
      layout: {
        columns: 3,
        columnGap: 10,
        rowGap: 10,
        rowHeight: 96,
        independentScroll: false
      },
      childrenBinding: {
        '$bind': 'devices.items',
        itemTemplateId: 'device-card-template',
        itemIdPath: 'id',
        fallback: deviceItems
      }
    };

    components['device-card-template'] = {
      id: 'device-card-template',
      presetId: 'device-tile',
      layout: { columnSpan: 1, rowSpan: 1 },
      appearance: { backgroundColor: { '$item': 'color', fallback: '#334155' } },
      rotation: { phaseOffset: { '$item': 'animationPhase', fallback: 0 } },
      defaultFaceIndex: { '$item': 'defaultFaceIndex', fallback: 0 },
      status: {
        online: { '$item': 'online', fallback: false },
        showStatusDot: true,
        onlineColorToken: 'colors.online',
        offlineColorToken: 'colors.offline'
      },
      powerControl: {
        visible: true,
        enabledWhen: { '$item': 'online', equals: true },
        powerBinding: { '$item': 'power', fallback: false }
      },
      events: {
        tap: { actionId: 'control-device' },
        powerTap: { actionId: 'toggle-device-power' },
        longPress: { actionId: 'show-card-faces' }
      },
      facesBinding: { '$item': 'faces', fallback: [] }
    };

    components['alerts-page'] = {
      id: 'alerts-page',
      type: 'scrollPage',
      layout: { safeArea: true, padding: 16 },
      children: ['alerts-page-header', 'alerts-list']
    };
    components['alerts-page-header'] = {
      id: 'alerts-page-header',
      type: 'pageHeader',
      title: 'Alerts',
      subtitle: 'Recent system and device alerts'
    };
    components['alerts-list'] = {
      id: 'alerts-list',
      type: 'list',
      itemsBinding: { '$bind': 'dashboard.alerts.items', fallback: [] },
      emptyStateComponentId: 'alerts-empty'
    };

    ['reports', 'profile', 'add-device', 'grid-details', 'hrms'].forEach(function (pageKey) {
      var titles = {
        reports: ['Reports', 'Usage, performance and activity reports'],
        profile: ['Profile', 'Profile information, language, privacy, and logout'],
        'add-device': ['Add Device', 'Connect a device using QR, Wi-Fi or Bluetooth'],
        'grid-details': ['eFactor Grid', 'Share and connect'],
        hrms: ['HRMS', 'Employees, attendance and payroll']
      };
      var t = titles[pageKey] || [pageKey, ''];
      components[pageKey + '-page'] = {
        id: pageKey + '-page',
        type: 'scrollPage',
        layout: { safeArea: true, padding: 16 },
        children: [pageKey + '-page-header']
      };
      components[pageKey + '-page-header'] = {
        id: pageKey + '-page-header',
        type: 'pageHeader',
        title: t[0],
        subtitle: t[1]
      };
    });

    components['offline-banner'] = {
      id: 'offline-banner',
      type: 'banner',
      content: { title: 'You are offline', subtitle: 'Showing the latest cached information' },
      appearance: { backgroundColorToken: 'colors.warning', textColorToken: 'colors.textPrimary' }
    };
    components['home-loading'] = { id: 'home-loading', type: 'skeleton', variant: 'home-dashboard' };
    components['home-error'] = { id: 'home-error', type: 'errorState', title: 'Unable to load dashboard', retryActionId: 'refresh-dashboard' };
    components['devices-loading'] = { id: 'devices-loading', type: 'skeleton', variant: 'device-grid' };
    components['devices-empty'] = {
      id: 'devices-empty', type: 'emptyState', iconToken: 'devices',
      title: 'No devices found', subtitle: 'Add your first device to get started.',
      primaryActionId: 'navigate-add-device'
    };
    components['devices-error'] = { id: 'devices-error', type: 'errorState', title: 'Unable to load devices', retryActionId: 'refresh-dashboard' };
    components['alerts-empty'] = { id: 'alerts-empty', type: 'emptyState', iconToken: 'alerts', title: 'No alerts', subtitle: 'Everything looks good.' };
    components['face-selector-sheet'] = { id: 'face-selector-sheet', type: 'faceSelector' };
    components['device-control-sheet'] = {
      id: 'device-control-sheet',
      type: 'deviceControlPanel',
      deviceIdBinding: { '$parameter': 'deviceId' }
    };

    var navItems = (layout.bottomNav || []).map(function (item, i) {
      var id = item.id || ('nav-' + i);
      var iconToken = faToToken(item.icon);
      usedIcons[iconToken] = true;
      return {
        id: 'nav-' + id,
        label: item.label || id,
        iconToken: iconToken,
        routeId: id,
        actionId: NAV_ACTIONS[id] || ('navigate-' + id)
      };
    });

    var routes = {
      home: { path: '/home', pageId: 'home' },
      devices: { path: '/devices', pageId: 'devices' },
      alerts: { path: '/alerts', pageId: 'alerts' },
      reports: { path: '/reports', pageId: 'reports' },
      profile: { path: '/profile', pageId: 'profile' },
      'add-device': { path: '/devices/add', pageId: 'add-device' },
      'grid-details': { path: '/modules/grid', pageId: 'grid-details' },
      hrms: { path: '/modules/hrms', pageId: 'hrms' }
    };

    cards.forEach(function (card) {
      if (card.pageRoute && !routes[card.id]) {
        var slug = String(card.pageRoute).replace(/^\//, '').replace(/[^a-z0-9]+/gi, '-').toLowerCase() || ('page-' + card.id);
        if (!routes[slug]) {
          routes[slug] = { path: card.pageRoute, pageId: slug };
        }
      }
    });

    cards.forEach(function (card) {
      (card.faces || []).forEach(function (face, fi) {
        var routeId = cardFaceRouteId(card, fi);
        var customPath = String(face.route || '').trim();
        routes[routeId] = {
          path: customPath || ('/cards/' + safeRoutePart(card.id || card.title, 'card') + '/faces/' + fi),
          pageId: routeId,
          parameters: {
            cardId: { type: 'string', required: true },
            faceIndex: { type: 'integer', required: true }
          }
        };
        if (customPath && !routes[customPath.replace(/^\//, '').replace(/[^a-z0-9]+/gi, '-').toLowerCase()]) {
          var slug = customPath.replace(/^\//, '').replace(/[^a-z0-9]+/gi, '-').toLowerCase() || routeId;
          routes[slug] = { path: customPath, pageId: slug };
        }
      });
    });
    var pages = {
      home: { id: 'home', routeId: 'home', rootComponentId: 'home-page', presentationStates: { loadingComponentId: 'home-loading', errorComponentId: 'home-error', offlineComponentId: 'offline-banner' } },
      devices: { id: 'devices', routeId: 'devices', rootComponentId: 'devices-page', presentationStates: { loadingComponentId: 'devices-loading', emptyComponentId: 'devices-empty', errorComponentId: 'devices-error', offlineComponentId: 'offline-banner' } },
      alerts: { id: 'alerts', routeId: 'alerts', rootComponentId: 'alerts-page' },
      reports: { id: 'reports', routeId: 'reports', rootComponentId: 'reports-page' },
      profile: { id: 'profile', routeId: 'profile', rootComponentId: 'profile-page' },
      'add-device': { id: 'add-device', routeId: 'add-device', rootComponentId: 'add-device-page' },
      'grid-details': { id: 'grid-details', routeId: 'grid-details', rootComponentId: 'grid-details-page' },
      hrms: { id: 'hrms', routeId: 'hrms', rootComponentId: 'hrms-page' }
    };

    cards.forEach(function (card) {
      (card.faces || []).forEach(function (face, fi) {
        var routeId = cardFaceRouteId(card, fi);
        pages[routeId] = {
          id: routeId,
          routeId: routeId,
          rootComponentId: routeId + '-page',
          presentationStates: { offlineComponentId: 'offline-banner' }
        };
      });
    });

    usedIcons.refresh = true;
    usedIcons.home = true;
    usedIcons.devices = true;
    usedIcons.alerts = true;
    usedIcons.reports = true;
    usedIcons.profile = true;
    ensureSchemaIconTokens(usedIcons);

    var ts = formatIndianTimestamp(new Date());

    return {
      '$schema': 'https://schemas.efactor.app/sdui/app-v2.schema.json',
      schemaVersion: '2.0.0',
      schemaType: 'server-driven-mobile-app',
      schemaId: 'efactor://app-home/' + userId,
      revision: 1,
      generatedAt: ts.utc,
      generatedAtIst: ts.local,

      meta: {
        appId: 'efactor-ioe',
        userId: userId,
        title: (layout.header && layout.header.title) || 'Welcome to SmartHome!',
        description: (layout.header && layout.header.subtitle) || 'Smart ecosystem dashboard',
        defaultRouteId: 'home',
        defaultLocale: ts.locale,
        timezone: ts.timezone,
        generatedAtIst: ts.local,
        generatedAtUtc: ts.utc,
        themePreset: theme.bgStyle || 'light',
        themeMode: theme.bgStyle === 'light' ? 'light' : 'dark',
        layoutSnapshot: buildLayoutSnapshot(layout, cfg)
      },

      rendererRequirements: {
        engine: 'efactor-sdui',
        minimumVersion: '2.0.0',
        maximumTestedVersion: '2.x',
        strict: true,
        unknownComponentPolicy: 'reject',
        unknownActionPolicy: 'reject',
        layoutEngine: 'constraint-based',
        platformIndependent: true,
        requiredCapabilities: [
          'responsive-grid', 'vertical-page-scroll', 'fixed-bottom-navigation',
          'data-binding', 'local-state', 'remote-actions', 'timed-face-rotation',
          'image-caching', 'loading-states', 'error-states'
        ],
        fallbacks: {
          unsupported3dAnimation: 'cross-fade',
          unsupportedIcon: 'cube',
          missingImage: 'image-placeholder',
          missingRoute: 'show-route-error',
          missingData: 'use-binding-fallback'
        }
      },

      layoutContract: {
        designWidth: cfg.phoneWidth || 375,
        designHeight: cfg.phoneHeight || 812,
        responsive: true,
        respectSafeArea: true,
        textScalePolicy: 'system-clamped',
        minimumTextScale: 0.9,
        maximumTextScale: 1.3,
        pageScroll: {
          direction: 'vertical',
          singlePrimaryScrollContainer: true,
          nestedScrollableGrids: false,
          bottomNavigationOutsideScroll: true
        },
        gridCalculation: {
          mode: 'available-width',
          formula: '(availableWidth - ((columns - 1) * columnGap)) / columns',
          cardWidthFormula: '(cellWidth * columnSpan) + ((columnSpan - 1) * columnGap)',
          cardHeightFormula: '(rowHeight * rowSpan) + ((rowSpan - 1) * rowGap)',
          homeGridColumns: homeGridColumns,
          defaultRowHeightDp: homeGridRowHeight,
          minCardRowHeightDp: homeGridRowHeight
        }
      },

      uiRenderContract: buildUiRenderContract(theme),

      motionPolicy: {
        respectReducedMotion: true,
        reducedMotionBehavior: 'show-default-face',
        pauseWhenAppInactive: true,
        pauseWhenPageInactive: true,
        pauseWhenOffscreen: true,
        doNotBindAnimationToScroll: true,
        doNotBindAnimationToDrag: true,
        doNotBindAnimationToPan: true
      },

      designTokens: buildDesignTokens(theme),
      themePreset: theme.bgStyle || 'dark',
      iconRegistry: buildIconRegistry(Object.keys(usedIcons)),
      assets: assets,

      dataSources: {
        dashboard: {
          type: 'rest',
          endpoint: '/api/dashboard/' + userId,
          method: 'GET',
          headers: { Accept: 'application/json' },
          cachePolicy: 'network-first',
          cacheTtlSec: 60,
          timeoutMs: 15000,
          retry: { enabled: true, maximumAttempts: 2, backoffMs: 800 }
        },
        devices: {
          type: 'rest',
          endpoint: String((cfg && cfg.iotApiUrl) || 'https://efactor.tech/v8/iot_api_creation.php').replace(/\/+$/, ''),
          method: 'GET',
          queryParameters: {
            action: 'get_iot_devices',
            access_token: { '$secureState': 'session.iotAccessToken' }
          },
          headers: {
            Accept: 'application/json',
            'X-IOT-Access-Token': { '$secureState': 'session.iotAccessToken' }
          },
          authentication: {
            type: 'iot-access-token',
            tokenBinding: { '$secureState': 'session.iotAccessToken' },
            refreshActionId: 'refresh-iot-access-token'
          },
          responseMapping: {
            items: '$.devices',
            summary: '$.summary',
            fallbackItems: '$.data.items'
          },
          itemContract: 'deviceGrid',
          cachePolicy: 'network-first',
          cacheTtlSec: 30,
          timeoutMs: 45000,
          published: true,
          errorPolicy: {
            useCachedData: true,
            componentId: 'devices-error',
            clearHostSession: false,
            navigateToLogin: false
          }
        }
      },

      state: {
        activeRouteId: layout.activePage || 'home',
        refreshing: false,
        selectedComponentId: null,
        animationsEnabled: true,
        preferences: {
          showWelcome: theme.showWelcome === true,
          showAlerts: theme.showAlerts !== false,
          showBottomNav: theme.showBottomNav !== false
        },
        devices: {}
      },

      animationPresets: {
        'tile-face-rotation': {
          type: 'quarter-turn-face-cycle',
          axis: 'y',
          direction: 'clockwise',
          durationMs: durationMs,
          faceCount: CUBE_FACE_COUNT,
          repeat: true,
          curve: 'ease-in-out-cubic',
          dwellMs: rotationDwellMs(durationMs),
          transitionMs: rotationTransitionMs(durationMs),
          dwellRatio: CUBE_HOLD_RATIO,
          transitionRatio: 1 - CUBE_HOLD_RATIO,
          speedLabel: cardSpeed <= 12 ? 'fast' : cardSpeed >= 20 ? 'slow' : 'natural',
          pauseWhenOffscreen: true,
          pauseWhenAppInactive: true,
          reducedMotionBehavior: 'default-face',
          geometry: buildCubeGeometryContract(),
          animation: buildCubeAnimationContract(durationMs, 0)
        },
        'device-face-rotation': {
          type: 'quarter-turn-face-cycle',
          axis: 'y',
          direction: 'clockwise',
          durationMs: durationMs,
          faceCount: CUBE_FACE_COUNT,
          repeat: true,
          curve: 'ease-in-out-cubic',
          dwellMs: rotationDwellMs(durationMs),
          transitionMs: rotationTransitionMs(durationMs),
          dwellRatio: CUBE_HOLD_RATIO,
          transitionRatio: 1 - CUBE_HOLD_RATIO,
          speedLabel: cardSpeed <= 12 ? 'fast' : cardSpeed >= 20 ? 'slow' : 'natural',
          pauseWhenOffscreen: true,
          pauseWhenAppInactive: true,
          reducedMotionBehavior: 'default-face',
          geometry: buildCubeGeometryContract(),
          animation: buildCubeAnimationContract(durationMs, 0)
        }
      },

      componentPresets: {
        'home-tile': {
          type: 'rotatingFaceCard',
          appearance: {
            textColorToken: 'colors.textPrimary',
            radiusToken: 'radius.card',
            shadowToken: 'shadows.card'
          },
          rotation: {
            enabled: true,
            presetId: 'tile-face-rotation',
            geometry: buildCubeGeometryContract(),
            animation: buildCubeAnimationContract(durationMs, 0)
          },
          dimensions: {
            minRowHeightDp: homeGridRowHeight,
            cubeDepthFormula: 'slotWidth / 2'
          }
        },
        'device-tile': {
          type: 'rotatingDeviceCard',
          appearance: {
            textColorToken: 'colors.textPrimary',
            radiusToken: 'radius.device',
            shadowToken: 'shadows.card'
          },
          rotation: {
            enabled: true,
            presetId: 'device-face-rotation',
            geometry: Object.assign({}, buildCubeGeometryContract(), { perspectiveDp: 650 }),
            animation: buildCubeAnimationContract(durationMs, 0)
          },
          dimensions: {
            minRowHeightDp: 96,
            cubeDepthFormula: 'slotWidth / 2'
          }
        }
      },

      actionRegistry: actionRegistry,
      actions: actionRegistry,

      routes: routes,

      appShell: {
        type: 'appShell',
        bodySlot: 'active-page',
        bottomNavigationComponentId: theme.showBottomNav !== false ? 'main-bottom-navigation' : null,
        floatingActionComponentId: null,
        bottomNavigationOutsidePageScroll: true
      },

      navigation: {
        activeRouteBinding: { '$state': 'activeRouteId' },
        items: navItems
      },

      routes: routes,
      pages: pages,
      components: components,

      localization: {
        defaultLocale: 'en-IN',
        fallbackLocale: 'en',
        supportedLocales: ['en-IN', 'en'],
        dateTime: {
          locale: 'en-IN',
          timeZone: 'Asia/Kolkata',
          calendar: 'gregory',
          hourCycle: 'h12'
        }
      },

      accessibility: {
        minimumTapTargetDp: 44,
        minimumContrastRatio: 4.5,
        announceRouteChanges: true,
        announceDeviceStateChanges: true,
        decorativeImagesExcludedFromSemantics: true
      },

      security: {
        requireHttps: true,
        requireSchemaSignature: false,
        rejectExpiredSchema: false,
        maximumSchemaSizeKb: 512,
        maximumComponentDepth: 20,
        maximumComponents: 500,
        allowedComponentTypes: [
          'appShell', 'scrollPage', 'pageHeader', 'banner', 'summaryBanner', 'statusSummary',
          'responsiveGrid', 'rotatingFaceCard', 'rotatingDeviceCard', 'bottomNavigation',
          'floatingActionButton', 'list', 'skeleton', 'emptyState', 'errorState',
          'faceSelector', 'deviceControlPanel'
        ],
        allowedActionTypes: [
          'navigate', 'sequence', 'set-state', 'refresh-data-source', 'request',
          'show-bottom-sheet', 'show-dialog', 'show-snackbar', 'host-action', 'external-navigate'
        ],
        allowedAssetDomains: ['images.unsplash.com']
      },

      telemetry: {
        enabled: true,
        events: {
          routeView: true,
          componentTap: true,
          actionSuccess: true,
          actionFailure: true,
          schemaRenderFailure: true
        },
        excludePersonalData: true
      },

      testing: {
        deterministicModeSupported: true,
        goldenTest: {
          viewportWidth: cfg.phoneWidth || 375,
          viewportHeight: cfg.phoneHeight || 812,
          devicePixelRatio: 3,
          animationProgress: 0,
          locale: 'en-IN',
          textScaleFactor: 1
        },
        visualDifferenceThreshold: 0.02
      }
    };
  }

  global.AhpSchemaV2 = {
    build: buildSchema,
    faToToken: faToToken,
    phaseOffset: phaseOffset,
    buildLayoutSnapshot: buildLayoutSnapshot,
    formatIndianTimestamp: formatIndianTimestamp,
    buildUiRenderContract: buildUiRenderContract,
    resolveHomeGridColumns: resolveHomeGridColumns,
    resolveHomeGridRowHeight: resolveHomeGridRowHeight,
    CUBE_HOLD_RATIO: CUBE_HOLD_RATIO,
    getProfileInitials: getProfileInitials,
    getProfileGreetingName: getProfileGreetingName,
    getThemeModeLabel: getThemeModeLabel,
    buildHomeHeaderComponent: buildHomeHeaderComponent,
    buildBottomNavAppearance: buildBottomNavAppearance,
    buildProfileAppearanceContract: buildProfileAppearanceContract,
    formatHomeGreeting: formatHomeGreeting,
    sanitizeWelcomeGreeting: sanitizeWelcomeGreeting,
    isIotHomeCard: isIotHomeCard,
    resolveFaceTapActionId: resolveFaceTapActionId
  };
})(typeof window !== 'undefined' ? window : globalThis);

/* Application Cube 3D schema contract extension */
(function (global) {
  'use strict';
  if (!global.AhpSchemaV2 || typeof global.AhpSchemaV2.build !== 'function') return;
  var baseBuild = global.AhpSchemaV2.build;
  function normalizeCube(layout) {
    var src = (layout && layout.applicationCube) || {};
    var fallback = [
      { id: 'appid', name: 'AppID', subtitle: 'Generation', icon: 'fa-key', route: '/appid', applicationId: 'appid', gradientStart: '#2563eb', gradientEnd: '#14b8a6', textColor: '#ffffff', badge: 'Secure' },
      { id: 'hrms', name: 'HRMS', subtitle: 'People Ops', icon: 'fa-users', route: '/hrms', applicationId: 'hrms', gradientStart: '#7c3aed', gradientEnd: '#ec4899', textColor: '#ffffff', badge: 'Live' },
      { id: 'estore', name: 'eStore', subtitle: 'Commerce', icon: 'fa-shopping-cart', route: '/estore', applicationId: 'estore', gradientStart: '#f97316', gradientEnd: '#facc15', textColor: '#ffffff', badge: 'Open' },
      { id: 'grip', name: 'GRIP', subtitle: 'Insights', icon: 'fa-chart-line', route: '/grip', applicationId: 'grip', gradientStart: '#0f766e', gradientEnd: '#22c55e', textColor: '#ffffff', badge: 'New' }
    ];
    var faces = Array.isArray(src.faces) && src.faces.length ? src.faces.slice(0, 4) : fallback;
    while (faces.length < 4) faces.push(fallback[faces.length]);
    faces = faces.slice(0, 4).map(function (face, i) {
      face = Object.assign({}, fallback[i], face || {});
      return {
        id: face.id || face.applicationId || fallback[i].id,
        name: face.name || fallback[i].name,
        subtitle: face.subtitle || '',
        icon: face.icon || fallback[i].icon,
        image: face.image || '',
        route: face.route || fallback[i].route,
        applicationId: face.applicationId || face.id || fallback[i].id,
        gradientStart: face.gradientStart || fallback[i].gradientStart,
        gradientEnd: face.gradientEnd || fallback[i].gradientEnd,
        textColor: face.textColor || '#ffffff',
        badge: face.badge || '',
        faceIndex: i,
        geometryFace: ['front', 'right', 'back', 'left'][i]
      };
    });
    return {
      enabled: src.enabled !== false,
      faceCount: 4,
      autoRotate: src.autoRotate !== false,
      rotationAxis: 'y',
      durationMs: parseInt(src.durationMs, 10) || 16000,
      perspective: parseInt(src.perspective, 10) || 900,
      size: parseInt(src.size, 10) || 150,
      pauseOnInteraction: src.pauseOnInteraction !== false,
      rotateInPlace: true,
      geometry: 'fourVerticalFaces',
      faces: faces
    };
  }
  function actionId(face) {
    return 'open-application-cube-' + String(face.applicationId || face.id || face.name || 'app').replace(/[^a-z0-9]+/ig, '-').toLowerCase();
  }
  global.AhpSchemaV2.build = function (layout, cfg, helpers) {
    var schema = baseBuild(layout, cfg, helpers);
    var cube = normalizeCube(layout || {});
    var cardCount = ((layout && layout.cards) || []).length;
    var useCubeAsHomeGrid = cube.enabled && cardCount === 0;
    schema.applicationCube = cube;
    schema.assets = Object.assign({}, schema.assets || {});
    cube.faces.forEach(function (face) {
      if (face.image) {
        schema.assets['asset-application-cube-face-' + face.faceIndex] = {
          type: 'network-image', source: face.image, fit: 'contain', alignment: 'center', cache: true
        };
      }
    });
    schema.actionRegistry = Object.assign({}, schema.actionRegistry || {});
    cube.faces.forEach(function (face) {
      schema.actionRegistry[actionId(face)] = {
        type: 'navigate',
        destination: {
          routeId: String(face.route || face.applicationId || face.id || '').replace(/^\//, '').replace(/[^a-z0-9]+/ig, '-').toLowerCase() || face.id,
          path: face.route || '',
          parameters: { applicationId: face.applicationId || face.id || '', faceIndex: face.faceIndex }
        }
      };
    });
    if (!useCubeAsHomeGrid) {
      schema.animationPresets = Object.assign({}, schema.animationPresets || {}, {
        applicationCubeRotation: {
          type: 'keyframed3DRotation',
          axis: 'y',
          faceCount: 4,
          degreesPerFace: 90,
          repeat: 'infinite',
          durationMs: cube.durationMs,
          rotateInPlace: true,
          easing: 'cubic-bezier(.72,0,.18,1)',
          dwellMs: Math.round(cube.durationMs / 4),
          geometry: 'realFourFaceCube'
        }
      });
      return schema;
    }
    schema.components = Object.assign({}, schema.components || {});
    Object.keys(schema.components).forEach(function (id) {
      var component = schema.components[id];
      if (!component) return;
      if (component.presetId === 'home-tile' || /^card-.*-face-\d+-(page|header|summary)$/.test(id)) {
        delete schema.components[id];
      }
    });
    schema.routes = Object.assign({}, schema.routes || {});
    Object.keys(schema.routes).forEach(function (id) {
      if (/^card-.*-face-\d+$/.test(id)) delete schema.routes[id];
    });
    Object.keys(schema.actionRegistry).forEach(function (id) {
      if (/^(open-card-|navigate-card-)/.test(id) || id === 'show-card-faces') delete schema.actionRegistry[id];
    });
    schema.components['application-cube-3d'] = {
      id: 'application-cube-3d',
      type: 'AppCube3D',
      rendererKey: 'applicationCube3D',
      renderer: {
        key: 'applicationCube3D',
        geometry: 'fourVerticalFaces',
        faceCount: 4,
        rotateInPlace: true,
        rotationAxis: 'y',
        real3D: true,
        notCarousel: true,
        notFlipCard: true,
        cssReference: {
          transformStyle: 'preserve-3d',
          perspective: cube.perspective,
          faceDepthFormula: 'translateZ(size / 2)',
          rotations: ['rotateY(0deg)', 'rotateY(90deg)', 'rotateY(180deg)', 'rotateY(270deg)']
        }
      },
      props: {
        faceCount: 4,
        rotationAxis: 'y',
        autoRotate: cube.autoRotate,
        durationMs: cube.durationMs,
        rotateInPlace: true,
        perspective: cube.perspective,
        size: cube.size,
        pauseOnInteraction: cube.pauseOnInteraction,
        faces: cube.faces.map(function (face) {
          return Object.assign({}, face, { actionId: actionId(face) });
        })
      },
      animationRef: 'applicationCubeRotation'
    };
    if (schema.components['home-grid']) {
      schema.components['home-grid'] = Object.assign({}, schema.components['home-grid'], {
        children: ['application-cube-3d'],
        layout: Object.assign({}, schema.components['home-grid'].layout || {}, { columns: 1, rowHeight: Math.max(190, cube.size + 52) })
      });
    }
    schema.animationPresets = Object.assign({}, schema.animationPresets || {}, {
      applicationCubeRotation: {
        type: 'keyframed3DRotation',
        axis: 'y',
        faceCount: 4,
        degreesPerFace: 90,
        repeat: 'infinite',
        durationMs: cube.durationMs,
        rotateInPlace: true,
        easing: 'cubic-bezier(.72,0,.18,1)',
        dwellMs: Math.round(cube.durationMs / 4),
        geometry: 'realFourFaceCube'
      }
    });
    schema.renderer = Object.assign({}, schema.renderer || {}, {
      key: 'applicationCube3D',
      geometry: 'fourVerticalFaces',
      faceCount: 4,
      rotateInPlace: true,
      rotationAxis: 'y',
      statement: 'THIS IS A REAL FOUR-FACE 3D CUBE.'
    });
    return schema;
  };
})(window);

/**
 * App Home Page â€” Server-Driven UI Schema v3.0.0
 * Upgrades the v2 semantic schema with:
 * componentDefinitions, scoped state, permissions, validation,
 * localization.resources, responsive breakpoints, full API contracts,
 * navigation guards, security signing policy, generalized expressions.
 */
(function (global) {
  'use strict';

  var GREETING_WAVE = '';

  function sanitizeWelcomeGreeting(raw) {
    if (global.AhpSchemaV2 && typeof global.AhpSchemaV2.sanitizeWelcomeGreeting === 'function') {
      return global.AhpSchemaV2.sanitizeWelcomeGreeting(raw);
    }
    var greeting = String(raw || '').trim();
    if (!greeting || /^\?+$/.test(greeting) || greeting.indexOf('\uFFFD') !== -1) {
      return 'Welcome back';
    }
    return greeting;
  }

  function i18n(key) {
    return { '$i18n': key };
  }

  function stateRef(path) {
    return { '$state': path };
  }

  function bind(path, fallback) {
    var b = { '$bind': path };
    if (fallback !== undefined && fallback !== null && fallback !== '') {
      b.fallback = fallback;
    }
    return b;
  }

  function dynamicThemeModeSubtitle(fallbackMode) {
    return {
      '$format': '{mode} mode',
      '$bindings': {
        mode: {
          '$state': 'global.themeMode',
          transform: 'capitalizeFirst',
          fallback: fallbackMode || 'light'
        }
      }
    };
  }

  function buildApplyThemeAction(preset, mode) {
    return {
      type: 'sequence',
      actions: [
        { type: 'set-state', path: 'global.themeMode', value: mode },
        { type: 'set-state', path: 'global.themePreset', value: preset },
        { type: 'host-action', actionId: 'apply-theme-preset', parameters: { preset: preset, mode: mode } }
      ]
    };
  }

  function exprEquals(left, right) {
    return { equals: [left, right] };
  }

  function exprAnd(parts) {
    return { and: parts };
  }

  function exprOr(parts) {
    return { or: parts };
  }

  function exprGreaterThan(left, right) {
    return { greaterThan: [left, right] };
  }

  function exprIncludes(left, right) {
    return { includes: [left, right] };
  }

  function exprExists(path) {
    return { exists: [path] };
  }

  function buildExpressionsCatalog() {
    return {
      operators: [
        'equals', 'notEquals',
        'greaterThan', 'greaterThanOrEqual',
        'lessThan', 'lessThanOrEqual',
        'and', 'or', 'not',
        'includes', 'exists', 'isEmpty', 'matches'
      ],
      conditionFields: [
        'visibleWhen', 'enabledWhen', 'renderWhen',
        'readonlyWhen', 'actionWhen', 'styleWhen'
      ],
      valueSources: ['$state', '$bind', '$item', '$event', '$parameter', '$i18n', '$secureState']
    };
  }

  function componentDef(componentType, opts) {
    return Object.assign({
      version: '1.0.0',
      componentType: componentType,
      requiredProps: [],
      optionalProps: [],
      propsSchema: {},
      slots: [],
      variants: ['standard'],
      supportedEvents: [],
      bindingCompatibility: ['$state', '$bind', '$i18n'],
      minimumRendererVersion: '3.0.0'
    }, opts || {});
  }

  function buildComponentDefinitions() {
    return {
      AppShell: componentDef('appShell', {
        requiredProps: ['bodySlot'],
        optionalProps: ['bottomNavigationComponentId', 'floatingActionComponentId', 'bottomNavigationOutsidePageScroll'],
        propsSchema: {
          bodySlot: { type: 'string' },
          bottomNavigationComponentId: { type: ['string', 'null'] },
          floatingActionComponentId: { type: ['string', 'null'] }
        },
        slots: ['body', 'bottomNavigation', 'floatingAction']
      }),
      ScrollPage: componentDef('scrollPage', {
        optionalProps: ['layout', 'children', 'parameters'],
        propsSchema: {
          layout: { type: 'object' },
          children: { type: 'array' }
        },
        slots: ['child']
      }),
      PageHeader: componentDef('pageHeader', {
        requiredProps: ['title'],
        optionalProps: ['subtitle', 'iconToken', 'typography'],
        propsSchema: {
          title: { type: ['string', 'object'] },
          subtitle: { type: ['string', 'object'] },
          iconToken: { type: 'string' }
        },
        bindingCompatibility: ['$i18n', '$bind', '$state']
      }),
      HomeHeader: componentDef('homeHeader', {
        requiredProps: ['title', 'greeting', 'avatar'],
        optionalProps: ['subtitle', 'layout', 'appearance'],
        propsSchema: {
          greeting: { type: 'object' },
          title: { type: ['string', 'object'] },
          subtitle: { type: ['string', 'object'] },
          avatar: { type: 'object' }
        },
        supportedEvents: ['tap'],
        bindingCompatibility: ['$i18n', '$bind', '$state']
      }),
      Banner: componentDef('banner', {
        requiredProps: ['content'],
        optionalProps: ['visibleWhen', 'appearance', 'enabledWhen'],
        propsSchema: {
          content: {
            type: 'object',
            properties: {
              title: { type: ['string', 'object'] },
              subtitle: { type: ['string', 'object'] },
              supportingText: { type: ['string', 'object'] }
            }
          }
        },
        slots: ['content'],
        variants: ['gradient', 'solid', 'warning'],
        supportedEvents: ['tap'],
        bindingCompatibility: ['$i18n', '$bind', '$state']
      }),
      SummaryBanner: componentDef('summaryBanner', {
        requiredProps: ['content'],
        optionalProps: ['visibleWhen', 'appearance'],
        propsSchema: {
          content: {
            type: 'object',
            properties: {
              title: { type: ['string', 'object'] },
              value: { type: ['number', 'string', 'object'] },
              badge: { type: ['string', 'object'] }
            }
          }
        },
        supportedEvents: ['tap'],
        bindingCompatibility: ['$i18n', '$bind', '$state']
      }),
      StatusSummary: componentDef('statusSummary', {
        requiredProps: ['items'],
        optionalProps: ['appearance'],
        propsSchema: {
          items: { type: 'array', minItems: 1 }
        },
        slots: ['item'],
        bindingCompatibility: ['$bind', '$state', '$i18n']
      }),
      ResponsiveGrid: componentDef('responsiveGrid', {
        optionalProps: ['children', 'childrenBinding', 'layout', 'layouts', 'overflowPolicy', 'preserveChildOrder'],
        propsSchema: {
          children: { type: 'array', minItems: 0 },
          layouts: { type: 'object' },
          overflowPolicy: { type: 'string', enum: ['reflow', 'scroll', 'clip'], default: 'reflow' },
          preserveChildOrder: { type: 'boolean', default: true }
        },
        slots: ['child'],
        variants: ['compact', 'medium', 'expanded'],
        supportedLayouts: ['compact', 'medium', 'expanded'],
        bindingCompatibility: ['$bind']
      }),
      RotatingFaceCard: componentDef('rotatingFaceCard', {
        requiredProps: ['faces'],
        optionalProps: ['appearance', 'events', 'responsiveLayout', 'presetId', 'layout', 'rotation', 'defaultFaceIndex'],
        propsSchema: {
          faces: { type: 'array', minItems: 1, maxItems: 4 },
          defaultFaceIndex: { type: 'integer', minimum: 0, maximum: 3, default: 0 },
          layout: {
            type: 'object',
            properties: {
              columnSpan: { type: 'integer', minimum: 1, maximum: 6, default: 1 },
              rowSpan: { type: 'integer', minimum: 1, maximum: 6, default: 1 }
            }
          },
          rotation: {
            type: 'object',
            properties: {
              enabled: { type: 'boolean', default: true },
              phaseOffset: { type: 'number', minimum: 0, maximum: 1, default: 0 }
            }
          }
        },
        slots: ['face'],
        variants: ['standard', 'compact', 'featured'],
        supportedEvents: ['tap', 'longPress'],
        bindingCompatibility: ['$bind', '$item', '$state', '$i18n']
      }),
      RotatingDeviceCard: componentDef('rotatingDeviceCard', {
        requiredProps: ['faces'],
        optionalProps: ['appearance', 'events', 'responsiveLayout', 'presetId', 'status', 'powerControl', 'facesBinding'],
        propsSchema: {
          faces: { type: 'array', minItems: 1, maxItems: 4 },
          defaultFaceIndex: { type: 'integer', minimum: 0, maximum: 3, default: 0 },
          status: {
            type: 'object',
            properties: {
              online: { type: 'boolean' },
              showStatusDot: { type: 'boolean', default: true }
            }
          },
          powerControl: {
            type: 'object',
            properties: {
              visible: { type: 'boolean', default: true },
              powerBinding: { type: 'object' }
            }
          }
        },
        slots: ['face', 'status', 'power'],
        variants: ['standard', 'stacked'],
        supportedEvents: ['tap', 'longPress', 'powerTap'],
        bindingCompatibility: ['$bind', '$item', '$state']
      }),
      BottomNavigation: componentDef('bottomNavigation', {
        requiredProps: ['itemsSource'],
        optionalProps: ['appearance', 'visibleWhen', 'activeRouteBinding'],
        propsSchema: {
          itemsSource: { type: 'object' },
          activeRouteBinding: { type: 'object' }
        },
        slots: ['item'],
        variants: ['labeled', 'icon-only'],
        supportedEvents: ['tap'],
        bindingCompatibility: ['$reference', '$state']
      }),
      FloatingActionButton: componentDef('floatingActionButton', {
        requiredProps: ['actionId'],
        optionalProps: ['iconToken', 'appearance', 'visibleWhen'],
        propsSchema: {
          actionId: { type: 'string' },
          iconToken: { type: 'string' }
        },
        supportedEvents: ['tap'],
        bindingCompatibility: ['$state', '$i18n']
      }),
      ProfileHeader: componentDef('profileHeader', {
        requiredProps: ['name', 'email'],
        optionalProps: ['phone', 'memberId', 'avatarText'],
        propsSchema: {
          name: { type: ['string', 'object'] },
          email: { type: ['string', 'object'] },
          phone: { type: ['string', 'object'] },
          memberId: { type: ['string', 'object'] }
        },
        supportedEvents: ['tap'],
        bindingCompatibility: ['', '', '']
      }),
      ProfileMenu: componentDef('profileMenu', {
        requiredProps: ['items'],
        optionalProps: ['appearance', 'selectionMode', 'selectedIdBinding'],
        propsSchema: {
          items: {
            type: 'array',
            minItems: 1,
            items: {
              type: 'object',
              properties: {
                id: { type: 'string' },
                iconToken: { type: 'string' },
                label: { type: ['string', 'object'] },
                subtitle: { type: ['string', 'object'] },
                subtitleBinding: { type: 'object' },
                subtitleFormat: { type: 'string' },
                trailingIconToken: { type: 'string' },
                actionId: { type: 'string' },
                selectedWhen: { type: 'object' }
              }
            }
          }
        },
        supportedEvents: ['tap'],
        bindingCompatibility: ['$bind', '$state', '$format', '$i18n']
      }),
      ThemeModeSelector: componentDef('themeModeSelector', {
        requiredProps: ['options'],
        optionalProps: ['selectedPresetBinding', 'selectedModeBinding', 'appearance'],
        propsSchema: {
          options: {
            type: 'array',
            minItems: 1,
            items: {
              type: 'object',
              required: ['id', 'preset', 'mode', 'label', 'actionId'],
              properties: {
                id: { type: 'string' },
                preset: { type: 'string' },
                mode: { type: 'string', enum: ['light', 'dark'] },
                label: { type: ['string', 'object'] },
                subtitle: { type: ['string', 'object'] },
                actionId: { type: 'string' }
              }
            }
          },
          selectedPresetBinding: { type: 'object' },
          selectedModeBinding: { type: 'object' }
        },
        supportedEvents: ['tap'],
        bindingCompatibility: ['$state', '$bind', '$i18n']
      }),
      ActionButton: componentDef('actionButton', {
        requiredProps: ['label', 'actionId'],
        optionalProps: ['iconToken', 'appearance'],
        propsSchema: {
          label: { type: ['string', 'object'] },
          actionId: { type: 'string' },
          iconToken: { type: 'string' }
        },
        supportedEvents: ['tap'],
        bindingCompatibility: ['', '']
      }),
      List: componentDef('list', {
        optionalProps: ['itemsBinding', 'emptyStateComponentId', 'itemTemplateId'],
        propsSchema: {
          itemsBinding: { type: 'object' },
          emptyStateComponentId: { type: 'string' }
        },
        slots: ['item'],
        bindingCompatibility: ['$bind', '$item']
      }),
      Skeleton: componentDef('skeleton', {
        optionalProps: ['variant'],
        propsSchema: {
          variant: { type: 'string' }
        },
        variants: ['home-dashboard', 'device-grid', 'standard']
      }),
      EmptyState: componentDef('emptyState', {
        requiredProps: ['title'],
        optionalProps: ['subtitle', 'iconToken', 'primaryActionId'],
        propsSchema: {
          title: { type: ['string', 'object'] },
          subtitle: { type: ['string', 'object'] },
          iconToken: { type: 'string' },
          primaryActionId: { type: 'string' }
        },
        bindingCompatibility: ['$i18n', '$state']
      }),
      ErrorState: componentDef('errorState', {
        requiredProps: ['title'],
        optionalProps: ['subtitle', 'retryActionId'],
        propsSchema: {
          title: { type: ['string', 'object'] },
          subtitle: { type: ['string', 'object'] },
          retryActionId: { type: 'string' }
        },
        bindingCompatibility: ['$i18n', '$state']
      }),
      FaceSelector: componentDef('faceSelector', {
        optionalProps: ['facesBinding', 'selectedIndexBinding'],
        propsSchema: {
          facesBinding: { type: 'object' },
          selectedIndexBinding: { type: 'object' }
        },
        supportedEvents: ['select'],
        bindingCompatibility: ['$bind', '$state', '$item']
      }),
      DeviceControlPanel: componentDef('deviceControlPanel', {
        optionalProps: ['deviceIdBinding', 'controls'],
        propsSchema: {
          deviceIdBinding: { type: 'object' }
        },
        supportedEvents: ['powerTap'],
        bindingCompatibility: ['$parameter', '$state', '$bind']
      }),
      DeviceDetailsHeader: componentDef('deviceDetailsHeader', {
        requiredProps: ['deviceIdBinding'],
        optionalProps: ['titleBinding', 'statusBinding', 'appearance'],
        propsSchema: {
          deviceIdBinding: { type: 'object' },
          titleBinding: { type: 'object' },
          statusBinding: { type: 'object' }
        },
        bindingCompatibility: ['$parameter', '$state', '$bind', '$i18n']
      }),
      LoginForm: componentDef('loginForm', {
        optionalProps: ['submitActionId', 'emailBinding', 'passwordBinding', 'title', 'subtitle'],
        propsSchema: {
          submitActionId: { type: 'string' },
          emailBinding: { type: 'object' },
          passwordBinding: { type: 'object' },
          title: { type: ['string', 'object'] },
          subtitle: { type: ['string', 'object'] }
        },
        supportedEvents: ['submit'],
        bindingCompatibility: ['$state', '$i18n', '$secureState']
      })
    };
  }

  function buildPermissions() {
    return {
      roles: {
        guest: ['dashboard.read'],
        user: [
          'dashboard.read',
          'devices.read',
          'devices.control',
          'alerts.read',
          'reports.read',
          'profile.read'
        ],
        admin: ['*']
      },
      native: {
        camera: {
          requiredForActions: ['scan-qr-device', 'navigate-add-device'],
          requestMode: 'when-needed',
          deniedActionId: 'show-camera-permission-help'
        },
        bluetooth: {
          requiredForActions: ['pair-bluetooth-device'],
          requestMode: 'when-needed',
          deniedActionId: 'show-bluetooth-permission-help'
        },
        notifications: {
          requiredForActions: ['enable-device-alerts'],
          requestMode: 'optional'
        },
        location: {
          requiredForActions: ['discover-wifi-devices'],
          requestMode: 'when-needed',
          deniedActionId: 'show-location-permission-help'
        }
      }
    };
  }

  function buildValidation() {
    return {
      document: {
        schemaUri: 'https://schemas.efactor.app/sdui/app-v3.schema.json',
        strict: true,
        rejectUnknownTopLevelFields: true,
        rejectDuplicateIds: true,
        validateReferences: true
      },
      components: {
        validateProps: true,
        validateEvents: true,
        validateChildren: true,
        maximumDepth: 20
      },
      bindings: {
        strictTypes: true,
        missingPathPolicy: 'use-fallback',
        typeMismatchPolicy: 'render-error-boundary'
      },
      api: {
        validateRequest: true,
        validateResponse: true,
        rejectUnexpectedResponseFields: false
      }
    };
  }

  function buildResponsive() {
    return {
      breakpoints: {
        compact: { minimumWidth: 0, maximumWidth: 599 },
        medium: { minimumWidth: 600, maximumWidth: 1023 },
        expanded: { minimumWidth: 1024 }
      }
    };
  }

  function buildLocalization(layout) {
    var header = layout.header || {};
    var welcome = layout.welcome || {};
    var title = header.title || 'Welcome to SmartHome!';
    var subtitle = header.subtitle || 'Control your connected home';
    var themeModeLabel = (global.AhpSchemaV2 && global.AhpSchemaV2.getThemeModeLabel)
      ? global.AhpSchemaV2.getThemeModeLabel(layout.theme)
      : 'Light';
    var notifOn = !layout.profilePage || layout.profilePage.notificationsEnabled !== false;
    var greeting = sanitizeWelcomeGreeting(welcome.greeting);
    var caption = welcome.caption || 'Manage your ecosystem';

    return {
      defaultLocale: 'en-IN',
      fallbackLocale: 'en',
      supportedLocales: ['en-IN', 'en', 'hi-IN'],
      dateTime: {
        locale: 'en-IN',
        timeZone: 'Asia/Kolkata',
        calendar: 'gregory',
        hourCycle: 'h12',
        formats: {
          shortDate: 'dd MMM yyyy',
          shortTime: 'hh:mm a',
          dateTime: 'dd MMM yyyy, hh:mm a'
        }
      },
      resources: {
        en: {
          'home.title': title,
          'home.subtitle': subtitle,
          'home.greeting': 'Hey, {{name}}',
          'home.welcome': greeting,
          'home.manageEcosystem': caption,
          'home.liveAlerts': 'Live Alerts',
          'home.offlineTitle': 'You are offline',
          'home.offlineSubtitle': 'Showing the latest cached information',
          'devices.title': (layout.devicesPage && layout.devicesPage.header && layout.devicesPage.header.title) || 'devices - iot',
          'devices.subtitle': (layout.devicesPage && layout.devicesPage.header && layout.devicesPage.header.subtitle) || 'Tap a cube to control.',
          'devices.online': 'Online',
          'devices.offline': 'Offline',
          'devices.emptyTitle': 'No devices found',
          'devices.emptySubtitle': 'Add your first device to get started.',
          'devices.detailsTitle': 'Device details',
          'devices.detailsSubtitle': 'Monitor and control this device',
          'navigation.home': 'Home',
          'navigation.devices': 'Devices',
          'navigation.alerts': 'Alerts',
          'navigation.reports': 'Reports',
          'navigation.profile': 'Profile',
          'alerts.title': 'Alerts',
          'alerts.subtitle': 'Recent system and device alerts',
          'alerts.emptyTitle': 'No alerts',
          'alerts.emptySubtitle': 'Everything looks good.',
          'reports.title': 'Reports',
          'reports.subtitle': 'Usage, performance and activity reports',
          'profile.title': 'Profile',
          'profile.subtitle': 'Profile, language, privacy, and logout',
          'profile.edit': 'Edit profile',
          'profile.editSubtitle': 'Manage account details',
          'profile.notifications': 'Notifications',
          'profile.notificationsSubtitle': notifOn ? 'Alerts and reminders enabled' : 'Alerts and reminders disabled',
          'profile.language': 'Language',
          'profile.languageSubtitle': (layout.profilePage && layout.profilePage.language) || 'English (India)',
          'profile.appearance': 'Appearance',
          'profile.appearanceSubtitle': '{{mode}} mode',
          'profile.appearanceSubtitleDynamic': '{{mode}} mode',
          'appearance.pageSubtitle': 'Choose light or dark mode for the app',
          'appearance.lightMode': 'Light mode',
          'appearance.lightModeHint': 'Bright backgrounds and dark text',
          'appearance.darkMode': 'Dark mode',
          'appearance.darkModeHint': 'Dark backgrounds and light text',
          'profile.privacy': 'Privacy Policy',
          'profile.privacySubtitle': 'Security and data controls',
          'profile.logout': 'Logout',
          'login.title': 'Sign in',
          'login.subtitle': 'Continue to your eFactor account',
          'errors.pageUnavailable': 'Page unavailable',
          'errors.pageUnavailableMessage': 'This page has not been configured.',
          'errors.dashboardLoad': 'Unable to load dashboard',
          'errors.devicesLoad': 'Unable to load devices',
          'errors.devicesAuth': 'IoT access token is missing or expired. Refresh the token in IoT Dashboard.',
          'errors.refreshFailed': 'Unable to refresh data',
          'errors.deviceUpdateFailed': 'Unable to update device',
          'errors.accessDenied': 'Access denied',
          'errors.sessionExpired': 'Session expired',
          'permissions.cameraHelp': 'Camera access is required to scan device QR codes.',
          'permissions.bluetoothHelp': 'Bluetooth access is required to pair nearby devices.',
          'permissions.locationHelp': 'Location access is required for Wi-Fi device discovery.'
        },
        'en-IN': {
          'home.title': title,
          'home.subtitle': subtitle,
          'home.greeting': 'Hey, {{name}}',
          'home.welcome': greeting,
          'home.manageEcosystem': caption,
          'home.liveAlerts': 'Live Alerts',
          'navigation.home': 'Home',
          'navigation.devices': 'Devices',
          'navigation.alerts': 'Alerts',
          'navigation.reports': 'Reports',
          'navigation.profile': 'Profile',
          'login.title': 'Sign in',
          'login.subtitle': 'Continue to your eFactor account',
          'devices.detailsTitle': 'Device details',
          'devices.detailsSubtitle': 'Monitor and control this device'
        },
        'hi-IN': {
          'home.title': title,
          'home.subtitle': 'à¤¸à¥à¤®à¤¾à¤°à¥à¤Ÿ à¤œà¤¾à¤¨à¤•à¤¾à¤°à¥€ à¤”à¤° à¤‰à¤ªà¤•à¤°à¤£',
          'home.welcome': 'ðŸ‘‹ à¤µà¤¾à¤ªà¤¸à¥€ à¤ªà¤° à¤¸à¥à¤µà¤¾à¤—à¤¤ à¤¹à¥ˆ',
          'home.manageEcosystem': 'à¤…à¤ªà¤¨à¥‡ à¤‡à¤•à¥‹à¤¸à¤¿à¤¸à¥à¤Ÿà¤® à¤•à¥‹ à¤ªà¥à¤°à¤¬à¤‚à¤§à¤¿à¤¤ à¤•à¤°à¥‡à¤‚',
          'home.liveAlerts': 'à¤²à¤¾à¤‡à¤µ à¤…à¤²à¤°à¥à¤Ÿ',
          'navigation.home': 'à¤¹à¥‹à¤®',
          'navigation.devices': 'à¤¡à¤¿à¤µà¤¾à¤‡à¤¸',
          'navigation.alerts': 'à¤…à¤²à¤°à¥à¤Ÿ',
          'navigation.reports': 'à¤°à¤¿à¤ªà¥‹à¤°à¥à¤Ÿ',
          'navigation.settings': 'à¤¸à¥‡à¤Ÿà¤¿à¤‚à¤—à¥à¤¸',
          'devices.online': 'à¤‘à¤¨à¤²à¤¾à¤‡à¤¨',
          'devices.offline': 'à¤‘à¤«à¤²à¤¾à¤‡à¤¨',
          'login.title': 'à¤¸à¤¾à¤‡à¤¨ à¤‡à¤¨',
          'login.subtitle': 'à¤…à¤ªà¤¨à¥‡ eFactor à¤–à¤¾à¤¤à¥‡ à¤®à¥‡à¤‚ à¤œà¤¾à¤°à¥€ à¤°à¤–à¥‡à¤‚'
        }
      }
    };
  }

  function buildApiContract(userId) {
    return {
      environments: {
        development: { baseUrl: 'https://dev-api.efactor.app' },
        staging: { baseUrl: 'https://staging-api.efactor.app' },
        production: { baseUrl: 'https://api.efactor.app' }
      },
      authentication: {
        type: 'bearer',
        tokenBinding: { '$secureState': 'session.accessToken' },
        refreshActionId: 'refresh-access-token'
      },
      defaultHeaders: {
        Accept: 'application/json',
        'Content-Type': 'application/json'
      },
      standardErrors: {
        '401': { actionId: 'session-expired', scope: 'host-authenticated-api-only', clearHostSession: true },
        '403': { actionId: 'show-access-denied', clearHostSession: false },
        '404': { actionId: 'show-not-found', clearHostSession: false },
        '429': { actionId: 'show-rate-limit', clearHostSession: false },
        '500': { actionId: 'show-server-error', clearHostSession: false }
      },
      dataSourceErrors: {
        '401': { actionId: 'show-data-source-auth-error', clearHostSession: false, navigateToLogin: false },
        '403': { actionId: 'show-access-denied', clearHostSession: false, navigateToLogin: false }
      },
      currentEnvironmentBinding: { '$state': 'global.apiEnvironment', fallback: 'production' }
    };
  }

  function buildModels() {
    var deviceItem = {
      type: 'object',
      required: ['id', 'name'],
      properties: {
        id: { type: 'string' },
        name: { type: 'string' },
        value: { type: ['string', 'number'] },
        unit: { type: 'string' },
        iconToken: { type: 'string' },
        color: { type: 'string' },
        location: { type: 'string' },
        online: { type: 'boolean' },
        power: { type: 'boolean' },
        updating: { type: 'boolean' },
        faces: { type: 'array', maxItems: 4 }
      }
    };

    return {
      DeviceItem: deviceItem,
      DashboardResponse: {
        type: 'object',
        required: ['data'],
        properties: {
          data: {
            type: 'object',
            required: ['alerts', 'cards'],
            properties: {
              alerts: {
                type: 'object',
                properties: {
                  count: { type: 'integer' },
                  items: { type: 'array' }
                }
              },
              cards: { type: 'object' },
              summary: { type: 'object' }
            }
          }
        }
      },
      DevicesResponse: {
        type: 'object',
        required: ['data'],
        properties: {
          data: {
            type: 'object',
            required: ['items'],
            properties: {
              items: { type: 'array', items: { '$ref': '#/models/DeviceItem' } },
              summary: {
                type: 'object',
                properties: {
                  online: { type: 'integer' },
                  offline: { type: 'integer' }
                }
              }
            }
          }
        }
      },
      DeviceDetailsResponse: {
        type: 'object',
        required: ['data'],
        properties: {
          data: {
            type: 'object',
            required: ['device'],
            properties: {
              device: { '$ref': '#/models/DeviceItem' },
              controls: { type: 'array' },
              telemetry: { type: 'object' }
            }
          }
        }
      },
      ApiError: {
        type: 'object',
        required: ['code', 'message'],
        properties: {
          code: { type: ['string', 'integer'] },
          message: { type: 'string' },
          details: { type: 'object' },
          retryable: { type: 'boolean' }
        }
      }
    };
  }

  function themePresetColorBundle(presetKey) {
    var bundles = {
      light: {
        mode: 'light',
        colors: {
          pageBackground: '#f3f4f6',
          surface: '#ffffff',
          surfaceElevated: '#ffffff',
          textPrimary: '#111827',
          textSecondary: '#374151',
          textMuted: '#6b7280',
          accent: '#14b8a6',
          accentGreen: '#22c55e',
          welcomeGradientStart: '#fbbf24',
          welcomeGradientEnd: '#f97316',
          navBackground: '#ffffff',
          navActive: '#111827',
          border: 'rgba(17,24,39,.08)',
          online: '#22c55e',
          offline: '#94a3b8'
        }
      },
      dark: {
        mode: 'dark',
        colors: {
          pageBackground: '#0b0f1a',
          surface: '#151b2e',
          surfaceElevated: '#1c2438',
          textPrimary: '#ffffff',
          textSecondary: '#dbe4ef',
          textMuted: '#94a3b8',
          accent: '#7c3aed',
          accentGreen: '#22c55e',
          welcomeGradientStart: '#7c3aed',
          welcomeGradientEnd: '#4f46e5',
          navBackground: '#000000',
          navActive: '#3b82f6',
          border: 'rgba(255,255,255,.10)',
          online: '#22c55e',
          offline: '#64748b'
        }
      },
      midnight: { mode: 'dark', colors: { pageBackground: '#050816', surface: '#111827', textPrimary: '#f8fafc', textMuted: '#94a3b8', accent: '#6366f1', navBackground: '#030712', navActive: '#818cf8' } },
      slate: { mode: 'dark', colors: { pageBackground: '#0f172a', surface: '#1e293b', textPrimary: '#f1f5f9', textMuted: '#94a3b8', accent: '#38bdf8', navBackground: '#0b1220', navActive: '#38bdf8' } },
      ocean: { mode: 'dark', colors: { pageBackground: '#031225', surface: '#0b2545', textPrimary: '#ecfeff', textMuted: '#7dd3fc', accent: '#06b6d4', navBackground: '#02101f', navActive: '#22d3ee' } },
      purple: { mode: 'dark', colors: { pageBackground: '#140b2d', surface: '#241447', textPrimary: '#faf5ff', textMuted: '#c4b5fd', accent: '#a855f7', navBackground: '#0f0720', navActive: '#c084fc' } },
      forest: { mode: 'dark', colors: { pageBackground: '#071a12', surface: '#103123', textPrimary: '#ecfdf5', textMuted: '#86efac', accent: '#22c55e', navBackground: '#04140d', navActive: '#4ade80' } },
      rose: { mode: 'dark', colors: { pageBackground: '#2a0612', surface: '#431225', textPrimary: '#fff1f2', textMuted: '#fda4af', accent: '#f43f5e', navBackground: '#1f040d', navActive: '#fb7185' } },
      aurora: { mode: 'dark', colors: { pageBackground: '#07131f', surface: '#102338', textPrimary: '#ecfeff', textMuted: '#99f6e4', accent: '#2dd4bf', navBackground: '#041018', navActive: '#5eead4' } },
      ember: { mode: 'dark', colors: { pageBackground: '#1a0b05', surface: '#30150b', textPrimary: '#fff7ed', textMuted: '#fdba74', accent: '#f97316', navBackground: '#120803', navActive: '#fb923c' } },
      graphite: { mode: 'dark', colors: { pageBackground: '#111111', surface: '#1f1f1f', textPrimary: '#f5f5f5', textMuted: '#a3a3a3', accent: '#737373', navBackground: '#0a0a0a', navActive: '#d4d4d4' } },
      glass: { mode: 'light', colors: { pageBackground: '#eef2ff', surface: 'rgba(255,255,255,.72)', textPrimary: '#0f172a', textMuted: '#64748b', accent: '#6366f1', navBackground: 'rgba(255,255,255,.88)', navActive: '#4f46e5' } }
    };
    return bundles[presetKey] || bundles.dark;
  }

  function buildThemePresetsCatalog(theme) {
    theme = theme || {};
    var active = theme.bgStyle || 'light';
    var keys = ['light', 'dark', 'midnight', 'slate', 'ocean', 'purple', 'forest', 'rose', 'aurora', 'ember', 'graphite', 'glass'];
    var presets = {};
    keys.forEach(function (key) {
      var bundle = themePresetColorBundle(key);
      presets[key] = {
        id: key,
        mode: bundle.mode,
        pageBackgroundToken: 'colors.pageBackground',
        colors: Object.assign({}, bundle.colors),
        typography: {
          homeGreeting: { fontSize: 13, fontWeight: 600, lineHeight: 1.2 },
          homeTitle: { fontSize: 26, fontWeight: 800, lineHeight: 1.08, letterSpacing: -0.03 },
          homeSubtitle: { fontSize: 12, fontWeight: 400, lineHeight: 1.3 }
        }
      };
    });
    if (presets[active]) {
      presets[active] = Object.assign({}, presets[active], {
        id: active,
        mode: active === 'light' || active === 'glass' ? 'light' : 'dark',
        colors: Object.assign({}, presets[active].colors, {
          pageBackground: theme.phoneBg || presets[active].colors.pageBackground,
          surface: theme.surfaceColor || theme.alertBg || presets[active].colors.surface,
          textPrimary: theme.textPrimary || presets[active].colors.textPrimary,
          textMuted: theme.textMuted || presets[active].colors.textMuted,
          accent: theme.accentColor || presets[active].colors.accent,
          accentGreen: theme.accentGreen || presets[active].colors.accentGreen,
          welcomeGradientStart: theme.welcomeStart || presets[active].colors.welcomeGradientStart,
          welcomeGradientEnd: theme.welcomeEnd || presets[active].colors.welcomeGradientEnd,
          navBackground: theme.navBg || presets[active].colors.navBackground,
          navActive: theme.navActiveColor || presets[active].colors.navActive
        })
      });
    }
    return presets;
  }

  function buildThemeAppearanceBlock(theme) {
    theme = theme || {};
    var preset = theme.bgStyle || 'light';
    var isLight = preset === 'light';
    return {
      preset: preset,
      mode: isLight ? 'light' : 'dark',
      colors: {
        pageBackground: theme.phoneBg || (isLight ? '#f3f4f6' : '#0b0f1a'),
        surface: theme.surfaceColor || theme.alertBg || (isLight ? '#ffffff' : '#151b2e'),
        textPrimary: theme.textPrimary || (isLight ? '#111827' : '#ffffff'),
        textMuted: theme.textMuted || (isLight ? '#6b7280' : '#94a3b8'),
        accent: theme.accentColor || '#14b8a6',
        welcomeGradientStart: theme.welcomeStart || '#fbbf24',
        welcomeGradientEnd: theme.welcomeEnd || '#f97316',
        navBackground: theme.navBg || (isLight ? '#ffffff' : '#000000'),
        navActive: theme.navActiveColor || (isLight ? '#111827' : '#2dd4bf')
      },
      typography: {
        homeGreeting: { fontSize: 13, fontWeight: 600, lineHeight: 1.2, colorToken: 'colors.textMuted' },
        homeTitle: { fontSize: 26, fontWeight: 800, lineHeight: 1.08, letterSpacing: -0.03, colorToken: 'colors.textPrimary' },
        homeSubtitle: { fontSize: 12, fontWeight: 400, lineHeight: 1.3, colorToken: 'colors.textMuted' }
      }
    };
  }

  function buildIotDevicesDataSource(cfg) {
    cfg = cfg || {};
    var endpoint = String(cfg.iotApiUrl || 'https://efactor.tech/v8/iot_api_creation.php').replace(/\/+$/, '');
    return {
      type: 'rest',
      endpoint: endpoint,
      method: 'GET',
      queryParameters: {
        action: 'get_iot_devices',
        access_token: { '$secureState': 'session.iotAccessToken' }
      },
      headers: {
        Accept: 'application/json',
        'X-IOT-Access-Token': { '$secureState': 'session.iotAccessToken' }
      },
      authentication: {
        type: 'iot-access-token',
        tokenBinding: { '$secureState': 'session.iotAccessToken' },
        refreshActionId: 'refresh-iot-access-token'
      },
      responseSchema: { '$ref': '#/models/DevicesResponse' },
      responseMapping: {
        items: '$.devices',
        summary: '$.summary',
        fallbackItems: '$.data.items',
        fallbackSummary: '$.data.summary'
      },
      itemContract: 'deviceGrid',
      cachePolicy: 'stale-while-revalidate',
      cacheTtlSec: 30,
      timeoutMs: 45000,
      retry: { enabled: true, maximumAttempts: 2, backoffMs: 800 },
      errorPolicy: {
        useCachedData: true,
        componentId: 'devices-error',
        clearHostSession: false,
        navigateToLogin: false,
        httpStatusHandlers: {
          '401': { actionId: 'show-devices-auth-error', clearHostSession: false },
          '403': { actionId: 'show-access-denied', clearHostSession: false }
        },
        unauthorizedActionId: 'refresh-iot-access-token'
      },
      requires: ['devices.read'],
      published: true
    };
  }

  var PROFILE_SUB_ROUTES = [
    { routeId: 'profile-edit', titleKey: 'profile.edit', subtitleKey: 'profile.editSubtitle' },
    { routeId: 'profile-notifications', titleKey: 'profile.notifications', subtitleKey: 'profile.notificationsSubtitle' },
    { routeId: 'profile-language', titleKey: 'profile.language', subtitleKey: 'profile.languageSubtitle' },
    { routeId: 'profile-appearance', titleKey: 'profile.appearance', subtitleKey: 'profile.appearanceSubtitle' },
    { routeId: 'profile-privacy', titleKey: 'profile.privacy', subtitleKey: 'profile.privacySubtitle' }
  ];

  function buildScopedState(layout, cfg) {
    var theme = layout.theme || {};
    var bgStyle = theme.bgStyle || 'light';
    cfg = cfg || {};
    var profile = cfg.loginUserProfile || {};
    var layoutProfile = layout.profilePage || {};

    return {
      global: {
        themeMode: bgStyle === 'light' ? 'light' : 'dark',
        themePreset: bgStyle,
        locale: 'en-IN',
        networkStatus: 'online',
        apiEnvironment: 'production',
        iotApiUrl: cfg.iotApiUrl || 'https://efactor.tech/v8/iot_api_creation.php',
        apiBase: cfg.apiBase || ''
      },
      session: {
        authenticated: true,
        userId: cfg.userId || 0,
        roles: ['user'],
        iotAccessToken: cfg.iotAccessToken || '',
        user: {
          email: profile.email || layoutProfile.email || cfg.userEmail || (layout.welcome && layout.welcome.email) || '',
          displayName: profile.name || layoutProfile.name || profile.email || cfg.userEmail || '',
          phone: profile.phone != null ? profile.phone : (layoutProfile.phone || ''),
          memberId: profile.memberId || layoutProfile.memberId || ''
        }
      },
      navigation: {
        activeRouteId: layout.activePage || 'home',
        history: []
      },
      pages: {
        home: {
          refreshing: false,
          selectedCardId: null
        },
        devices: {
          refreshing: false,
          selectedDeviceId: null
        },
        'device-details': {
          refreshing: false,
          selectedDeviceId: null
        },
        login: {
          submitting: false
        }
      },
      preferences: {
        showWelcome: theme.showWelcome === true,
        showAlerts: theme.showAlerts !== false,
        showBottomNav: theme.showBottomNav !== false,
        animationsEnabled: true
      },
      entities: {
        devices: {
          byId: {},
          allIds: []
        }
      },
      persistence: {
        preferences: 'local',
        navigation: 'memory',
        'entities.devices': 'cache'
      }
    };
  }

  function buildCapabilities() {
    return {
      required: [
        'responsive-grid',
        'vertical-page-scroll',
        'fixed-bottom-navigation',
        'data-binding',
        'scoped-state',
        'remote-actions',
        'timed-face-rotation',
        'image-caching',
        'loading-states',
        'error-states',
        'i18n',
        'permissions',
        'expression-engine',
        'schema-signature-verify'
      ],
      optional: [
        'haptic-feedback',
        'push-notifications'
      ]
    };
  }

  function cardResponsiveLayout(card, theme) {
    theme = theme || {};
    var maxSpan = Math.max(1, resolveHomeGridColumns(theme));
    var col = Math.max(1, Math.min(maxSpan, parseInt(card.colSpan, 10) || 1));
    var row = Math.max(1, Math.min(maxSpan, parseInt(card.rowSpan, 10) || 1));
    return {
      compact: { columnSpan: col, rowSpan: row },
      medium: { columnSpan: Math.min(4, col + (col === 1 ? 0 : 0)), rowSpan: row },
      expanded: { columnSpan: Math.min(6, Math.max(col, col > 1 ? col : 1)), rowSpan: row }
    };
  }

  function routePermissionsFor(key) {
    if (key === 'login') return [];
    if (key === 'alerts') return ['alerts.read'];
    if (key === 'reports') return ['reports.read'];
    if (key === 'settings' || key === 'profile') return ['profile.read'];
    if (key === 'add-device') return ['devices.control'];
    if (key === 'device-details' || key === 'devices') return ['devices.read'];
    if (key === 'home') return ['dashboard.read'];
    if (key === 'grid-details' || key === 'hrms') return ['dashboard.read'];
    if (key.indexOf('device') === 0) return ['devices.read'];
    if (key.indexOf('profile-') === 0) return ['profile.read'];
    return ['dashboard.read'];
  }

  function enrichRoutes(routes) {
    var out = {};
    Object.keys(routes || {}).forEach(function (key) {
      var r = routes[key] || {};
      var isLogin = key === 'login';
      out[key] = Object.assign({}, r, {
        path: r.path || ('/' + key),
        pageId: r.pageId || key,
        public: isLogin ? true : (key === 'home' ? false : !!r.public),
        requiresAuthentication: isLogin ? false : (r.requiresAuthentication !== false),
        requiredPermissions: isLogin ? [] : (r.requiredPermissions && key !== 'alerts' && key !== 'reports' && key !== 'profile' && key !== 'add-device' && key !== 'device-details'
          ? r.requiredPermissions
          : routePermissionsFor(key)),
        deepLinks: r.deepLinks || ['efactor://' + key],
        transition: r.transition || 'fade',
        restoreState: r.restoreState !== false
      });
      // Always apply canonical route permissions from the repair rules
      if (!isLogin) {
        out[key].requiredPermissions = routePermissionsFor(key);
      }
    });

    if (!out['device-details']) {
      out['device-details'] = {
        path: '/devices/:deviceId',
        pageId: 'device-details',
        public: false,
        requiresAuthentication: true,
        requiredPermissions: ['devices.read'],
        deepLinks: ['efactor://devices/:deviceId'],
        transition: 'slide',
        restoreState: true,
        parameters: {
          deviceId: { type: 'string', required: true }
        }
      };
    } else {
      out['device-details'] = Object.assign({}, out['device-details'], {
        requiredPermissions: ['devices.read'],
        parameters: out['device-details'].parameters || {
          deviceId: { type: 'string', required: true }
        }
      });
    }

    if (!out.login) {
      out.login = {
        path: '/login',
        pageId: 'login',
        public: true,
        requiresAuthentication: false,
        requiredPermissions: [],
        deepLinks: ['efactor://login'],
        transition: 'fade',
        restoreState: false
      };
    }

    PROFILE_SUB_ROUTES.forEach(function (def) {
      if (!out[def.routeId]) {
        out[def.routeId] = {
          path: '/profile/' + def.routeId.replace(/^profile-/, ''),
          pageId: def.routeId,
          public: false,
          requiresAuthentication: true,
          requiredPermissions: ['profile.read'],
          deepLinks: ['efactor://' + def.routeId],
          transition: 'slide',
          restoreState: true
        };
      }
    });

    return out;
  }

  function fixErrorPresentation(action) {
    if (!action || !action.errorPresentation) return action;
    var ep = Object.assign({}, action.errorPresentation);
    if (ep.type === 'snackbar') ep.type = 'show-snackbar';
    return Object.assign({}, action, { errorPresentation: ep });
  }

  function enrichActions(actionRegistry, cfg) {
    cfg = cfg || {};
    var out = Object.assign({}, actionRegistry || {});

    Object.keys(out).forEach(function (id) {
      out[id] = fixErrorPresentation(out[id]);
    });

    out['open-iot-app'] = { type: 'host-action', actionId: 'open-iot' };

    out['profile-edit'] = { type: 'navigate', destination: { routeId: 'profile-edit' } };
    out['profile-notifications'] = { type: 'navigate', destination: { routeId: 'profile-notifications' } };
    out['profile-language'] = { type: 'navigate', destination: { routeId: 'profile-language' } };
    out['profile-appearance'] = { type: 'navigate', destination: { routeId: 'profile-appearance' } };
    out['profile-privacy'] = { type: 'navigate', destination: { routeId: 'profile-privacy' } };
    out['apply-theme-light'] = buildApplyThemeAction('light', 'light');
    out['apply-theme-dark'] = buildApplyThemeAction('dark', 'dark');

    out['show-devices-auth-error'] = {
      type: 'sequence',
      actions: [
        { type: 'show-snackbar', message: { '$i18n': 'errors.devicesAuth' } },
        { type: 'refresh-data-source', dataSourceId: 'devices', clearHostSession: false }
      ]
    };

    out['show-data-source-auth-error'] = {
      type: 'show-snackbar',
      message: { '$i18n': 'errors.devicesAuth' }
    };

    out['refresh-devices-data-source'] = {
      type: 'refresh-data-source',
      dataSourceId: 'devices',
      clearHostSession: false
    };

    if (out['refresh-dashboard']) {
      out['refresh-dashboard'] = Object.assign({}, fixErrorPresentation(out['refresh-dashboard']), {
        requires: ['dashboard.read'],
        actions: (out['refresh-dashboard'].actions || []).map(function (a) {
          if (a.type === 'set-state' && a.path === 'refreshing') {
            return Object.assign({}, a, { path: 'pages.home.refreshing' });
          }
          return a;
        }),
        errorPresentation: {
          type: 'show-snackbar',
          message: (out['refresh-dashboard'].errorPresentation && out['refresh-dashboard'].errorPresentation.message) || { '$i18n': 'errors.refreshFailed' }
        }
      });
    }

    if (out['toggle-device-power']) {
      var toggleReq = Object.assign({}, (out['toggle-device-power'].request || {}), {
        method: 'POST',
        endpoint: '/api/devices/{deviceId}/power',
        pathParameters: { deviceId: { '$event': 'componentId' } },
        body: { enabled: { '$toggleState': 'entities.devices.byId.{deviceId}.power' } }
      });
      out['toggle-device-power'] = Object.assign({}, fixErrorPresentation(out['toggle-device-power']), {
        requires: ['devices.control'],
        request: toggleReq,
        optimisticUpdate: true,
        loadingStatePath: 'entities.devices.byId.{deviceId}.updating',
        errorPresentation: {
          type: 'show-snackbar',
          message: { '$i18n': 'errors.deviceUpdateFailed' }
        }
      });
    }

    out['control-device'] = {
      type: 'navigate',
      destination: {
        routeId: 'device-details',
        parameters: { deviceId: { '$event': 'componentId' } }
      },
      requires: ['devices.read']
    };

    if (out['navigate-add-device']) {
      out['navigate-add-device'] = Object.assign({}, out['navigate-add-device'], {
        requires: ['devices.control'],
        requiredNativePermissions: ['camera']
      });
    }

    out['refresh-iot-access-token'] = {
      type: 'request',
      request: {
        method: 'GET',
        endpoint: (cfg.apiBase ? String(cfg.apiBase).replace(/\/+$/, '') : '/index.php/app-home-page/api') + '/iot-access-token',
        headers: { Accept: 'application/json' }
      },
      onSuccess: {
        type: 'set-secure-state',
        path: 'session.iotAccessToken',
        value: { '$response': 'access_token' }
      }
    };

    out['refresh-access-token'] = {
      type: 'request',
      request: {
        method: 'POST',
        endpoint: '/api/auth/refresh',
        body: { refreshToken: { '$secureState': 'session.refreshToken' } }
      },
      onSuccess: {
        type: 'set-secure-state',
        path: 'session.accessToken',
        value: { '$response': 'accessToken' }
      }
    };

    out['session-expired'] = {
      type: 'sequence',
      scope: 'host-authenticated-api-only',
      actions: [
        { type: 'set-state', path: 'session.authenticated', value: false },
        { type: 'set-secure-state', path: 'session.accessToken', value: null },
        { type: 'navigate', destination: { routeId: 'login' } },
        { type: 'show-snackbar', message: { '$i18n': 'errors.sessionExpired' } }
      ]
    };

    out['show-access-denied'] = {
      type: 'show-dialog',
      title: { '$i18n': 'errors.accessDenied' },
      message: { '$i18n': 'errors.pageUnavailableMessage' }
    };

    out['show-not-found'] = {
      type: 'show-dialog',
      title: { '$i18n': 'errors.pageUnavailable' },
      message: { '$i18n': 'errors.pageUnavailableMessage' }
    };

    out['show-rate-limit'] = {
      type: 'show-snackbar',
      message: 'Too many requests. Please try again shortly.'
    };

    out['show-server-error'] = {
      type: 'show-snackbar',
      message: { '$i18n': 'errors.refreshFailed' }
    };

    out['show-camera-permission-help'] = {
      type: 'show-dialog',
      title: 'Camera permission',
      message: { '$i18n': 'permissions.cameraHelp' }
    };

    out['show-bluetooth-permission-help'] = {
      type: 'show-dialog',
      title: 'Bluetooth permission',
      message: { '$i18n': 'permissions.bluetoothHelp' }
    };

    out['show-location-permission-help'] = {
      type: 'show-dialog',
      title: 'Location permission',
      message: { '$i18n': 'permissions.locationHelp' }
    };

    out['scan-qr-device'] = {
      type: 'sequence',
      requires: ['devices.control'],
      requiredNativePermissions: ['camera'],
      actions: [
        { type: 'navigate', destination: { routeId: 'add-device' } }
      ]
    };

    out['pair-bluetooth-device'] = {
      type: 'sequence',
      requires: ['devices.control'],
      requiredNativePermissions: ['bluetooth'],
      actions: [
        { type: 'navigate', destination: { routeId: 'add-device' } }
      ]
    };

    out['enable-device-alerts'] = {
      type: 'set-state',
      path: 'preferences.showAlerts',
      value: true,
      requiredNativePermissions: ['notifications']
    };

    out['discover-wifi-devices'] = {
      type: 'sequence',
      requires: ['devices.control'],
      requiredNativePermissions: ['location'],
      actions: [
        { type: 'navigate', destination: { routeId: 'add-device' } }
      ]
    };

    out['submit-login'] = {
      type: 'request',
      request: {
        method: 'POST',
        endpoint: '/api/auth/login',
        body: {
          email: { '$state': 'pages.login.email' },
          password: { '$secureState': 'pages.login.password' }
        }
      },
      onSuccess: {
        type: 'sequence',
        actions: [
          { type: 'set-secure-state', path: 'session.accessToken', value: { '$response': 'accessToken' } },
          { type: 'set-state', path: 'session.authenticated', value: true },
          { type: 'navigate', destination: { routeId: 'home' } }
        ]
      },
      errorPresentation: {
        type: 'show-snackbar',
        message: { '$i18n': 'errors.accessDenied' }
      }
    };

    out['logout'] = {
      type: 'sequence',
      requires: ['profile.read'],
      actions: [
        { type: 'set-state', path: 'pages.profile.loggingOut', value: true },
        {
          type: 'request',
          request: {
            method: 'POST',
            endpoint: '/sd-logout',
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          }
        },
        { type: 'set-state', path: 'session.authenticated', value: false },
        { type: 'set-secure-state', path: 'session.accessToken', value: null },
        { type: 'external-navigate', url: '/welcome' }
      ],
      errorPresentation: {
        type: 'show-snackbar',
        message: 'Unable to logout. Please try again.'
      }
    };
    if (out['show-route-error']) {
      out['show-route-error'] = Object.assign({}, out['show-route-error'], {
        title: i18n('errors.pageUnavailable'),
        message: i18n('errors.pageUnavailableMessage')
      });
    }

    return out;
  }

  function upgradeComponents(components, layout, cfg) {
    var out = Object.assign({}, components || {});
    var theme = (layout && layout.theme) || {};
    cfg = cfg || {};
    var v2 = global.AhpSchemaV2 || {};
    var gridGap = theme.gridGap != null ? Number(theme.gridGap) : 12;
    var homeGridColumns = v2.resolveHomeGridColumns
      ? v2.resolveHomeGridColumns(theme)
      : (theme.gridColumns != null && !isNaN(Number(theme.gridColumns))
        ? Math.max(1, Math.min(6, Number(theme.gridColumns)))
        : 3);
    var homeGridRowHeight = v2.resolveHomeGridRowHeight
      ? v2.resolveHomeGridRowHeight(theme)
      : (theme.gridRowHeight != null && !isNaN(Number(theme.gridRowHeight))
        ? Math.max(72, Math.min(140, Number(theme.gridRowHeight)))
        : 96);
    var cardsById = {};
    (layout.cards || []).forEach(function (card) {
      if (card && card.id) cardsById[card.id] = card;
    });

    if (out['home-header']) {
      var rebuiltHeader = v2.buildHomeHeaderComponent
        ? v2.buildHomeHeaderComponent(layout, cfg, theme, {})
        : out['home-header'];
      out['home-header'] = Object.assign({}, rebuiltHeader, out['home-header'], {
        type: 'homeHeader',
        greeting: Object.assign({}, rebuiltHeader.greeting || {}, {
          text: v2.formatHomeGreeting
            ? v2.formatHomeGreeting(v2.getProfileGreetingName ? v2.getProfileGreetingName(layout.profilePage, cfg) : 'Guest')
            : ('Hey, ' + (v2.getProfileGreetingName ? v2.getProfileGreetingName(layout.profilePage, cfg) : 'Guest')),
          i18nKey: 'home.greeting',
          typography: Object.assign({}, (rebuiltHeader.greeting && rebuiltHeader.greeting.typography) || {}, {
            fontSize: 13,
            fontWeight: 600,
            lineHeight: 1.2,
            colorToken: 'colors.textMuted'
          })
        }),
        title: Object.assign({}, rebuiltHeader.title || {}, {
          text: (layout.header && layout.header.title) || i18n('home.title'),
          i18nKey: 'home.title',
          typography: Object.assign({}, (rebuiltHeader.title && rebuiltHeader.title.typography) || {}, {
            fontSize: 26,
            fontWeight: 800,
            lineHeight: 1.08,
            letterSpacing: -0.03,
            colorToken: 'colors.textPrimary'
          })
        }),
        subtitle: Object.assign({}, rebuiltHeader.subtitle || {}, {
          text: (layout.header && layout.header.subtitle) || i18n('home.subtitle'),
          i18nKey: 'home.subtitle',
          typography: Object.assign({}, (rebuiltHeader.subtitle && rebuiltHeader.subtitle.typography) || {}, {
            fontSize: 12,
            fontWeight: 400,
            lineHeight: 1.3,
            colorToken: 'colors.textMuted'
          })
        }),
        avatar: Object.assign({}, rebuiltHeader.avatar || {}, {
          initials: v2.getProfileInitials ? v2.getProfileInitials(layout.profilePage, cfg) : 'U',
          initialsBinding: {
            '$bind': 'session.user.initials',
            fallback: v2.getProfileInitials ? v2.getProfileInitials(layout.profilePage, cfg) : 'U'
          },
          events: { tap: { actionId: 'navigate-profile' } }
        })
      });
    }

    if (out['welcome-banner']) {
      out['welcome-banner'] = Object.assign({}, out['welcome-banner'], {
        visibleWhen: exprAnd([
          exprEquals(stateRef('session.authenticated'), true),
          exprEquals(stateRef('preferences.showWelcome'), true)
        ]),
        content: {
          title: i18n('home.welcome'),
          subtitle: i18n('home.manageEcosystem'),
          supportingText: {
            '$state': 'session.user.email',
            fallback: ''
          }
        }
      });
    }

    if (out['alerts-summary']) {
      out['alerts-summary'] = Object.assign({}, out['alerts-summary'], {
        visibleWhen: exprAnd([
          exprEquals(stateRef('session.authenticated'), true),
          exprEquals(stateRef('preferences.showAlerts'), true)
        ]),
        enabledWhen: exprEquals(stateRef('session.authenticated'), true),
        content: Object.assign({}, (out['alerts-summary'].content || {}), {
          title: i18n('home.liveAlerts')
        })
      });
    }

    if (out['home-grid']) {
      var homeTileIds = Object.keys(out).filter(function (id) {
        return out[id] && out[id].presetId === 'home-tile';
      }).sort(function (a, b) {
        var sa = out[a].sortOrder != null ? out[a].sortOrder : 999;
        var sb = out[b].sortOrder != null ? out[b].sortOrder : 999;
        return sa - sb;
      });
      out['home-grid'] = Object.assign({}, out['home-grid'], {
        type: 'responsiveGrid',
        children: homeTileIds.length ? homeTileIds : (out['home-grid'].children || []),
        layouts: {
          compact: {
            columns: homeGridColumns,
            columnGap: Math.max(8, gridGap - 2),
            rowGap: Math.max(8, gridGap - 2),
            rowHeight: homeGridRowHeight,
            minRowHeight: homeGridRowHeight
          },
          medium: {
            columns: 4,
            columnGap: gridGap,
            rowGap: gridGap,
            rowHeight: 88,
            maximumContentWidth: 720
          },
          expanded: {
            columns: 6,
            columnGap: Math.max(gridGap, 16),
            rowGap: Math.max(gridGap, 16),
            rowHeight: 96,
            maximumContentWidth: 1200
          }
        },
        overflowPolicy: 'reflow',
        preserveChildOrder: true
      });
    }

    if (out['devices-grid']) {
      var gridBinding = Object.assign({}, out['devices-grid'].childrenBinding || {}, {
        '$bind': 'devices.items',
        itemTemplateId: (out['devices-grid'].childrenBinding && out['devices-grid'].childrenBinding.itemTemplateId) || 'device-card-template',
        itemIdPath: (out['devices-grid'].childrenBinding && out['devices-grid'].childrenBinding.itemIdPath) || 'id'
      });
      delete gridBinding.fallback;
      out['devices-grid'] = Object.assign({}, out['devices-grid'], {
        type: 'responsiveGrid',
        layouts: {
          compact: { columns: 3, columnGap: 10, rowGap: 10, rowHeight: 96 },
          medium: { columns: 4, columnGap: 12, rowGap: 12, rowHeight: 104, maximumContentWidth: 720 },
          expanded: { columns: 6, columnGap: 16, rowGap: 16, rowHeight: 112, maximumContentWidth: 1200 }
        },
        overflowPolicy: 'reflow',
        preserveChildOrder: true,
        childrenBinding: gridBinding
      });
    }

    if (out['devices-header']) {
      out['devices-header'] = Object.assign({}, out['devices-header'], {
        title: i18n('devices.title'),
        subtitle: i18n('devices.subtitle')
      });
    }

    if (out['devices-status'] && Array.isArray(out['devices-status'].items)) {
      out['devices-status'] = Object.assign({}, out['devices-status'], {
        items: [
          Object.assign({}, out['devices-status'].items[0] || {}, { label: i18n('devices.online') }),
          Object.assign({}, out['devices-status'].items[1] || {}, { label: i18n('devices.offline') })
        ]
      });
    }

    if (out['main-bottom-navigation']) {
      var navAppearance = v2.buildBottomNavAppearance
        ? v2.buildBottomNavAppearance(theme)
        : (out['main-bottom-navigation'].appearance || {});
      out['main-bottom-navigation'] = Object.assign({}, out['main-bottom-navigation'], {
        activeRouteBinding: stateRef('navigation.activeRouteId'),
        visibleWhen: exprEquals(stateRef('preferences.showBottomNav'), true),
        appearance: Object.assign({}, navAppearance, out['main-bottom-navigation'].appearance || {})
      });
    }

    if (out['offline-banner']) {
      out['offline-banner'] = Object.assign({}, out['offline-banner'], {
        visibleWhen: exprEquals(stateRef('global.networkStatus'), 'offline'),
        content: {
          title: i18n('home.offlineTitle'),
          subtitle: i18n('home.offlineSubtitle')
        }
      });
    }

    Object.keys(out).forEach(function (id) {
      var c = out[id];
      if (!c || c.presetId !== 'home-tile') return;
      var cardId = id.replace(/-card$/, '');
      var src = cardsById[cardId];
      var col = (c.layout && c.layout.columnSpan) || (src && src.colSpan) || 1;
      var row = (c.layout && c.layout.rowSpan) || (src && src.rowSpan) || 1;
      var enrich = {
        responsiveLayout: {
          compact: { columnSpan: col, rowSpan: row },
          medium: { columnSpan: col, rowSpan: row },
          expanded: { columnSpan: Math.min(6, Math.max(col, col === 1 ? 1 : col)), rowSpan: row }
        }
      };
      if (src) {
        enrich.cardType = src.type || c.cardType || 'link';
        enrich.layout = Object.assign({}, c.layout || {}, { columnSpan: col, rowSpan: row });
        enrich.dimensions = Object.assign({}, c.dimensions || {}, {
          columnSpan: col,
          rowSpan: row,
          borderRadius: src.borderRadius != null ? Number(src.borderRadius) : ((c.dimensions && c.dimensions.borderRadius) || 10),
          minRowHeightDp: homeGridRowHeight,
          cubeDepthFormula: 'cellWidth / 2',
          perspectiveDp: 700
        });
        enrich.appearance = Object.assign({}, c.appearance || {}, {
          borderRadius: src.borderRadius != null ? Number(src.borderRadius) : ((c.appearance && c.appearance.borderRadius) || 10)
        });
        enrich.timing = Object.assign({}, c.timing || {}, {
          faceSec: (c.timing && c.timing.faceSec) || (src.faceSec != null && src.faceSec !== '' ? Math.max(2, Math.min(8, parseFloat(src.faceSec))) : undefined),
          usesGlobalDefault: src.faceSec == null || src.faceSec === ''
        });
        enrich.orientation = src.orientation || c.orientation || (
          col > 1 && row === 1 ? 'wide' :
          row > 1 && col === 1 ? 'tall' :
          col > 1 && row > 1 ? 'large' : 'compact'
        );
        enrich.sortOrder = c.sortOrder != null ? c.sortOrder : undefined;
        enrich.gridArea = src.gridArea || c.gridArea || cardId;
      } else {
        enrich.responsiveLayout = {
          compact: { columnSpan: col, rowSpan: row },
          medium: { columnSpan: col, rowSpan: row },
          expanded: { columnSpan: Math.min(6, Math.max(col, col === 1 ? 1 : col)), rowSpan: row }
        };
      }
      out[id] = Object.assign({}, c, enrich);
    });

    if (out['device-card-template']) {
      out['device-card-template'] = Object.assign({}, out['device-card-template'], {
        responsiveLayout: {
          compact: { columnSpan: 1, rowSpan: 1 },
          medium: { columnSpan: 1, rowSpan: 1 },
          expanded: { columnSpan: 1, rowSpan: 1 }
        },
        powerControl: Object.assign({}, out['device-card-template'].powerControl || {}, {
          enabledWhen: exprAnd([
            exprEquals({ '$item': 'online' }, true),
            exprIncludes(stateRef('session.roles'), 'user')
          ])
        })
      });
    }

    out['device-details-page'] = {
      id: 'device-details-page',
      type: 'scrollPage',
      layout: { safeArea: true, padding: 16 },
      parameters: {
        deviceId: { '$parameter': 'deviceId' }
      },
      children: ['device-details-header', 'device-details-controls']
    };

    out['device-details-header'] = {
      id: 'device-details-header',
      type: 'deviceDetailsHeader',
      deviceIdBinding: { '$parameter': 'deviceId' },
      titleBinding: { '$bind': 'deviceDetails.device.name', fallback: i18n('devices.detailsTitle') },
      statusBinding: { '$bind': 'deviceDetails.device.online', fallback: false }
    };

    out['device-details-controls'] = {
      id: 'device-details-controls',
      type: 'deviceControlPanel',
      deviceIdBinding: { '$parameter': 'deviceId' }
    };

    var profile = (layout && layout.profilePage) || {};
    var profileAppearance = v2.buildProfileAppearanceContract
      ? v2.buildProfileAppearanceContract(theme)
      : { pagePadding: 16, pageGap: 12 };
    var profileInitials = v2.getProfileInitials ? v2.getProfileInitials(profile, cfg) : 'U';
    var notifOn = profile.notificationsEnabled !== false;
    var themeModeFallback = (theme.bgStyle === 'dark') ? 'dark' : 'light';

    out['profile-page'] = {
      id: 'profile-page',
      type: 'scrollPage',
      layout: { safeArea: true, padding: profileAppearance.pagePadding, gap: profileAppearance.pageGap },
      children: ['profile-header', 'profile-menu', 'profile-logout-button']
    };

    out['profile-header'] = {
      id: 'profile-header',
      type: 'profileHeader',
      name: profile.name || 'Profile',
      email: profile.email || (layout.welcome && layout.welcome.email) || '',
      phone: profile.phone || '',
      memberId: profile.memberId || '',
      avatarText: profileInitials,
      appearance: profileAppearance
    };

    out['profile-menu'] = {
      id: 'profile-menu',
      type: 'profileMenu',
      appearance: profileAppearance,
      items: [
        {
          id: 'edit-profile',
          iconToken: 'profile',
          label: i18n('profile.edit'),
          subtitle: i18n('profile.editSubtitle'),
          trailingIconToken: 'chevron-right',
          actionId: 'profile-edit'
        },
        {
          id: 'notifications',
          iconToken: 'alerts',
          label: i18n('profile.notifications'),
          subtitle: notifOn ? i18n('profile.notificationsSubtitle') : 'Alerts and reminders disabled',
          trailingIconToken: 'chevron-right',
          actionId: 'profile-notifications'
        },
        {
          id: 'language',
          iconToken: 'language',
          label: i18n('profile.language'),
          subtitle: profile.language || i18n('profile.languageSubtitle'),
          trailingIconToken: 'chevron-right',
          actionId: 'profile-language'
        },
        {
          id: 'appearance',
          iconToken: 'appearance',
          label: i18n('profile.appearance'),
          subtitle: dynamicThemeModeSubtitle(themeModeFallback),
          subtitleBinding: stateRef('global.themeMode'),
          subtitleFormat: '{value} mode',
          subtitleTransform: 'capitalizeFirst',
          trailingIconToken: 'chevron-right',
          actionId: 'profile-appearance'
        },
        {
          id: 'privacy-policy',
          iconToken: 'shield',
          label: i18n('profile.privacy'),
          subtitle: i18n('profile.privacySubtitle'),
          trailingIconToken: 'chevron-right',
          actionId: 'profile-privacy'
        }
      ]
    };

    out['profile-logout-button'] = {
      id: 'profile-logout-button',
      type: 'actionButton',
      label: i18n('profile.logout'),
      iconToken: 'logout',
      actionId: 'logout',
      appearance: Object.assign({}, profileAppearance, { variant: 'primary', destructive: false, minHeight: profileAppearance.logoutMinHeight, borderRadius: profileAppearance.logoutBorderRadius })
    };

    PROFILE_SUB_ROUTES.forEach(function (def) {
      if (def.routeId === 'profile-appearance') return;
      var pageId = def.routeId + '-page';
      var headerId = def.routeId + '-header';
      out[pageId] = {
        id: pageId,
        type: 'scrollPage',
        layout: { safeArea: true, padding: profileAppearance.pagePadding || 16, gap: profileAppearance.pageGap || 12 },
        children: [headerId]
      };
      out[headerId] = {
        id: headerId,
        type: 'pageHeader',
        title: i18n(def.titleKey),
        subtitle: i18n(def.subtitleKey)
      };
    });

    out['profile-appearance-page'] = {
      id: 'profile-appearance-page',
      type: 'scrollPage',
      layout: { safeArea: true, padding: profileAppearance.pagePadding || 16, gap: profileAppearance.pageGap || 12 },
      children: ['profile-appearance-header', 'profile-appearance-theme-selector']
    };
    out['profile-appearance-header'] = {
      id: 'profile-appearance-header',
      type: 'pageHeader',
      title: i18n('profile.appearance'),
      subtitle: i18n('appearance.pageSubtitle')
    };
    out['profile-appearance-theme-selector'] = {
      id: 'profile-appearance-theme-selector',
      type: 'themeModeSelector',
      appearance: profileAppearance,
      selectedPresetBinding: stateRef('global.themePreset'),
      selectedModeBinding: stateRef('global.themeMode'),
      options: [
        {
          id: 'light',
          preset: 'light',
          mode: 'light',
          label: i18n('appearance.lightMode'),
          subtitle: i18n('appearance.lightModeHint'),
          actionId: 'apply-theme-light',
          selectedWhen: exprEquals(stateRef('global.themePreset'), 'light')
        },
        {
          id: 'dark',
          preset: 'dark',
          mode: 'dark',
          label: i18n('appearance.darkMode'),
          subtitle: i18n('appearance.darkModeHint'),
          actionId: 'apply-theme-dark',
          selectedWhen: exprEquals(stateRef('global.themePreset'), 'dark')
        }
      ]
    };

    out['login-page'] = {
      id: 'login-page',
      type: 'scrollPage',
      layout: { safeArea: true, padding: 16 },
      children: ['login-page-header', 'login-form']
    };

    out['login-page-header'] = {
      id: 'login-page-header',
      type: 'pageHeader',
      title: i18n('login.title'),
      subtitle: i18n('login.subtitle')
    };

    out['login-form'] = {
      id: 'login-form',
      type: 'loginForm',
      title: i18n('login.title'),
      subtitle: i18n('login.subtitle'),
      submitActionId: 'submit-login',
      emailBinding: { '$state': 'pages.login.email' },
      passwordBinding: { '$secureState': 'pages.login.password' }
    };

    return out;
  }

  function enrichNavigationItems(items) {
    return (items || []).map(function (item) {
      var routeId = item.routeId || item.id;
      var key = 'navigation.' + (routeId || 'home');
      return Object.assign({}, item, {
        label: i18n(key)
      });
    });
  }

  function buildSecurity() {
    var issued = new Date();
    var expires = new Date(issued.getTime() + (7 * 24 * 60 * 60 * 1000));
    var ts = (global.AhpSchemaV2 && global.AhpSchemaV2.formatIndianTimestamp)
      ? global.AhpSchemaV2.formatIndianTimestamp(issued)
      : { utc: issued.toISOString(), local: issued.toISOString(), timezone: 'Asia/Kolkata', locale: 'en-IN' };
    var expTs = (global.AhpSchemaV2 && global.AhpSchemaV2.formatIndianTimestamp)
      ? global.AhpSchemaV2.formatIndianTimestamp(expires)
      : { utc: expires.toISOString(), local: expires.toISOString(), timezone: 'Asia/Kolkata', locale: 'en-IN' };
    return {
      requireHttps: true,
      requireSchemaSignature: true,
      signatureAlgorithm: 'Ed25519',
      signatureKeyId: 'efactor-ui-key-2026-01',
      rejectExpiredSchema: true,
      issuedAt: ts.utc,
      issuedAtIst: ts.local,
      expiresAt: expTs.utc,
      expiresAtIst: expTs.local,
      timezone: ts.timezone,
      maximumSchemaSizeKb: 512,
      maximumComponentDepth: 20,
      maximumComponents: 500,
      note: 'Signature is attached by the server at publish time. Private signing keys must never appear in this JSON.'
    };
  }

  function uniqueActionTypes(baseTypes) {
    var required = [
      'navigate', 'sequence', 'set-state', 'set-secure-state', 'refresh-data-source',
      'request', 'show-bottom-sheet', 'show-dialog', 'show-snackbar', 'external-navigate', 'host-action'
    ];
    var seen = {};
    var out = [];
    (baseTypes || []).concat(required).forEach(function (t) {
      if (!t || seen[t]) return;
      seen[t] = true;
      out.push(t === 'snackbar' ? 'show-snackbar' : t);
    });
    if (seen.snackbar && !seen['show-snackbar']) {
      out.push('show-snackbar');
    }
    return out.filter(function (t) { return t !== 'snackbar'; });
  }

  function uniqueComponentTypes(baseTypes) {
    var required = [
      'appShell', 'scrollPage', 'pageHeader', 'homeHeader', 'banner', 'summaryBanner', 'statusSummary',
      'responsiveGrid', 'rotatingFaceCard', 'rotatingDeviceCard', 'bottomNavigation',
      'floatingActionButton', 'list', 'skeleton', 'emptyState', 'errorState',
      'faceSelector', 'deviceControlPanel', 'deviceDetailsHeader', 'loginForm',
      'profileHeader', 'profileMenu', 'actionButton', 'themeModeSelector'
    ];
    var seen = {};
    var out = [];
    (baseTypes || []).concat(required).forEach(function (t) {
      if (!t || seen[t]) return;
      seen[t] = true;
      out.push(t);
    });
    return out;
  }

  function buildSchema(layout, cfg, helpers) {
    if (!global.AhpSchemaV2 || typeof global.AhpSchemaV2.build !== 'function') {
      throw new Error('AhpSchemaV2 builder is required for Schema v3');
    }

    layout = layout || {};
    if (layout.activePage === 'settings') layout.activePage = 'profile';
    var base = global.AhpSchemaV2.build(layout, cfg, helpers);
    var themePreset = (layout.theme && layout.theme.bgStyle) || 'light';
    layout = layout || {};
    cfg = cfg || {};
    var userId = cfg.userId || 0;
    var previewDevices = ((base.components && base.components['devices-grid'] &&
        base.components['devices-grid'].childrenBinding &&
        Array.isArray(base.components['devices-grid'].childrenBinding.fallback))
      ? base.components['devices-grid'].childrenBinding.fallback
      : []) || [];

    if (!previewDevices.length && layout.devicesPage && Array.isArray(layout.devicesPage.devices)) {
      previewDevices = layout.devicesPage.devices;
    }

    var components = upgradeComponents(base.components || {}, layout, cfg);
    var actionRegistry = enrichActions(base.actionRegistry || {}, cfg);
    var routes = enrichRoutes(base.routes || {});
    var navItems = enrichNavigationItems((base.navigation && base.navigation.items) || []);

    var pages = Object.assign({}, base.pages || {});
    pages['device-details'] = {
      id: 'device-details',
      routeId: 'device-details',
      rootComponentId: 'device-details-page',
      presentationStates: {
        loadingComponentId: 'devices-loading',
        errorComponentId: 'devices-error',
        offlineComponentId: 'offline-banner'
      }
    };
    pages.login = {
      id: 'login',
      routeId: 'login',
      rootComponentId: 'login-page',
      presentationStates: {
        errorComponentId: 'home-error',
        offlineComponentId: 'offline-banner'
      }
    };
    PROFILE_SUB_ROUTES.forEach(function (def) {
      pages[def.routeId] = {
        id: def.routeId,
        routeId: def.routeId,
        rootComponentId: def.routeId + '-page',
        presentationStates: { offlineComponentId: 'offline-banner' }
      };
    });
    if (pages.devices) {
      pages.devices = Object.assign({}, pages.devices, {
        onEnterActions: [{ actionId: 'refresh-devices-data-source' }],
        dataSources: ['devices']
      });
    }

    var dataSources = {
      dashboard: {
        type: 'rest',
        endpoint: '/api/dashboard/{userId}',
        method: 'GET',
        pathParameters: {
          userId: stateRef('session.userId')
        },
        responseSchema: { '$ref': '#/models/DashboardResponse' },
        responseMapping: {
          alerts: '$.data.alerts',
          cards: '$.data.cards',
          summary: '$.data.summary'
        },
        cachePolicy: 'stale-while-revalidate',
        cacheTtlSec: 60,
        timeoutMs: 15000,
        retry: { enabled: true, maximumAttempts: 2, backoffMs: 800 },
        errorPolicy: {
          useCachedData: true,
          componentId: 'home-error'
        },
        requires: ['dashboard.read'],
        published: true
      },
      devices: buildIotDevicesDataSource(cfg),
      deviceDetails: {
        type: 'rest',
        endpoint: '/api/devices/{userId}/{deviceId}',
        method: 'GET',
        pathParameters: {
          userId: stateRef('session.userId'),
          deviceId: { '$parameter': 'deviceId' }
        },
        responseSchema: { '$ref': '#/models/DeviceDetailsResponse' },
        responseMapping: {
          device: '$.data.device',
          controls: '$.data.controls',
          telemetry: '$.data.telemetry'
        },
        cachePolicy: 'network-first',
        cacheTtlSec: 15,
        timeoutMs: 15000,
        retry: { enabled: true, maximumAttempts: 2, backoffMs: 600 },
        errorPolicy: {
          useCachedData: true,
          componentId: 'devices-error'
        },
        requires: ['devices.read']
      }
    };

    var motionPolicy = Object.assign({}, base.motionPolicy || {}, {
      animationPresets: base.animationPresets || (base.motionPolicy && base.motionPolicy.animationPresets) || {}
    });

    var ts = (global.AhpSchemaV2 && global.AhpSchemaV2.formatIndianTimestamp)
      ? global.AhpSchemaV2.formatIndianTimestamp(new Date())
      : { utc: new Date().toISOString(), local: new Date().toISOString(), timezone: 'Asia/Kolkata', locale: 'en-IN' };

    return {
      '$schema': 'https://schemas.efactor.app/sdui/app-v3.schema.json',
      schemaVersion: '3.0.0',
      schemaType: 'server-driven-mobile-app',
      schemaId: 'efactor://app-home/' + userId,
      revision: 3,
      generatedAt: ts.utc,
      generatedAtIst: ts.local,
      signature: {
        algorithm: 'Ed25519',
        keyId: 'efactor-ui-key-2026-01',
        value: null,
        pendingServerSignature: true
      },

      meta: Object.assign({}, base.meta || {}, {
        schemaProfile: 'homepage-canonical-v3',
        title: (layout.header && layout.header.title) || 'Welcome to SmartHome!',
        description: (layout.header && layout.header.subtitle) || 'Smart ecosystem dashboard',
        defaultRouteId: 'home',
        defaultLocale: ts.locale,
        timezone: ts.timezone,
        generatedAtIst: ts.local,
        generatedAtUtc: ts.utc,
        themePreset: themePreset,
        themeMode: themePreset === 'light' ? 'light' : 'dark',
        layoutSnapshot: (base.meta && base.meta.layoutSnapshot) || (global.AhpSchemaV2.buildLayoutSnapshot ? global.AhpSchemaV2.buildLayoutSnapshot(layout, cfg) : null)
      }),

      rendererRequirements: Object.assign({}, base.rendererRequirements || {}, {
        minimumVersion: '3.0.0',
        maximumTestedVersion: '3.x',
        requiredCapabilities: buildCapabilities().required
      }),

      capabilities: buildCapabilities(),

      layoutContract: base.layoutContract || {},
      uiRenderContract: base.uiRenderContract || (global.AhpSchemaV2 && global.AhpSchemaV2.buildUiRenderContract
        ? global.AhpSchemaV2.buildUiRenderContract((layout && layout.theme) || {})
        : {}),
      responsive: buildResponsive(),
      motionPolicy: motionPolicy,
      animationPresets: motionPolicy.animationPresets || base.animationPresets || {},

      designTokens: Object.assign({}, (global.AhpSchemaV2 && global.AhpSchemaV2.buildDesignTokens)
        ? global.AhpSchemaV2.buildDesignTokens(layout.theme || {})
        : (base.designTokens || {}), {
        themePreset: themePreset,
        themeMode: themePreset === 'light' ? 'light' : 'dark',
        appearance: buildThemeAppearanceBlock(layout.theme || {})
      }),
      themes: {
        activePreset: themePreset,
        activePresetBinding: stateRef('global.themePreset'),
        activeMode: themePreset === 'light' ? 'light' : 'dark',
        appearance: buildThemeAppearanceBlock(layout.theme || {}),
        presets: buildThemePresetsCatalog(layout.theme || {})
      },
      iconRegistry: base.iconRegistry || {},
      assets: base.assets || {},

      componentDefinitions: buildComponentDefinitions(),
      componentPresets: base.componentPresets || {},
      components: components,

      models: buildModels(),
      dataSources: dataSources,
      api: buildApiContract(userId),

      state: buildScopedState(layout, cfg),
      bindings: {
        strictTypes: true,
        missingPathPolicy: 'use-fallback',
        typeMismatchPolicy: 'render-error-boundary'
      },
      expressions: buildExpressionsCatalog(),
      validation: buildValidation(),
      permissions: buildPermissions(),

      actionRegistry: actionRegistry,
      routes: routes,
      appShell: Object.assign({}, base.appShell || {}, {
        bottomNavigationVisibleWhen: exprEquals(stateRef('preferences.showBottomNav'), true)
      }),
      navigation: {
        initialRouteId: 'home',
        unknownRouteActionId: 'show-route-error',
        backBehavior: 'history',
        preservePageState: true,
        maximumHistoryEntries: 20,
        activeRouteBinding: stateRef('navigation.activeRouteId'),
        items: navItems
      },
      pages: pages,

      localization: buildLocalization(layout),
      accessibility: base.accessibility || {},
      security: Object.assign({}, base.security || {}, buildSecurity(), {
        allowedComponentTypes: uniqueComponentTypes(base.security && base.security.allowedComponentTypes),
        allowedActionTypes: uniqueActionTypes(base.security && base.security.allowedActionTypes),
        allowedAssetDomains: (base.security && base.security.allowedAssetDomains) || ['images.unsplash.com']
      }),
      telemetry: base.telemetry || {},
      rendererFeatureStatus: {
      rotatingFaces3d: 'required',
      cardFaceTapNavigation: 'required',
      faceSelectorSheet: 'disabled',
      deviceControlPanel: 'disabled',
      exampleRemoteDataSources: 'published',
      refreshButton: 'removed'
    },
    auth: {
      otpEndpoint: '/sd-send-otp',
      socialProviders: ['google', 'apple', 'facebook'],
      login: { otpSendActionId: 'send-login-otp' },
      signup: { otpSendActionId: 'send-signup-otp' },
      forgotPassword: { otpSendActionId: 'send-reset-otp' },
      logout: { endpoint: '/sd-logout', method: 'POST', redirectUrl: '/welcome', actionId: 'logout' }
    },    testing: Object.assign({}, base.testing || {}, {
        schemaVersion: '3.0.0',
        previewData: {
          devices: previewDevices
        }
      })
    };
  }

  global.AhpSchemaV3 = {
    build: buildSchema,
    i18n: i18n,
    exprEquals: exprEquals,
    exprAnd: exprAnd
  };
})(typeof window !== 'undefined' ? window : globalThis);

/* Application Cube 3D schema v3 enrichment */
(function (global) {
  'use strict';
  if (!global.AhpSchemaV3 || typeof global.AhpSchemaV3.build !== 'function') return;
  var baseBuild = global.AhpSchemaV3.build;
  function addUnique(list, value) {
    list = Array.isArray(list) ? list.slice() : [];
    if (list.indexOf(value) === -1) list.push(value);
    return list;
  }
  global.AhpSchemaV3.build = function (layout, cfg, helpers) {
    var schema = baseBuild(layout, cfg, helpers);
    var cube = schema.applicationCube || ((layout && layout.applicationCube) || {});
    var cardCount = ((layout && layout.cards) || []).length;
    if (!cube.enabled || cardCount > 0) {
      return schema;
    }
    schema.componentDefinitions = Object.assign({}, schema.componentDefinitions || {}, {
      AppCube3D: {
        version: '1.0.0',
        componentType: 'AppCube3D',
        requiredProps: ['faces', 'faceCount', 'rotationAxis', 'rotateInPlace'],
        optionalProps: ['autoRotate', 'durationMs', 'perspective', 'size', 'pauseOnInteraction', 'renderer'],
        propsSchema: {
          faceCount: { type: 'integer', const: 4 },
          rotationAxis: { type: 'string', enum: ['y'] },
          rotateInPlace: { type: 'boolean', const: true },
          faces: { type: 'array', minItems: 4, maxItems: 4 },
          durationMs: { type: 'integer', enum: [12000, 16000, 20000, 28000] },
          perspective: { type: 'integer', minimum: 650, maximum: 1400 },
          size: { type: 'integer', minimum: 124, maximum: 190 }
        },
        rendererContract: {
          key: 'applicationCube3D',
          geometry: 'fourVerticalFaces',
          real3D: true,
          forbiddenInterpretations: ['flatCards', 'carousel', 'horizontalSlider', 'stackedCards', 'flipCard'],
          transformRequirements: ['transform-style: preserve-3d', 'perspective', 'translateZ(size / 2)', 'rotateY']
        },
        supportedEvents: ['tap', 'pressStart', 'pressEnd', 'hoverStart', 'hoverEnd'],
        minimumRendererVersion: '3.0.0'
      }
    });
    if (schema.components && schema.components['application-cube-3d']) {
      schema.components['application-cube-3d'] = Object.assign({}, schema.components['application-cube-3d'], {
        type: 'AppCube3D',
        rendererKey: 'applicationCube3D',
        renderer: Object.assign({}, schema.components['application-cube-3d'].renderer || {}, {
          key: 'applicationCube3D',
          geometry: 'fourVerticalFaces',
          faceCount: 4,
          rotateInPlace: true,
          rotationAxis: 'y',
          statement: 'THIS IS A REAL FOUR-FACE 3D CUBE.'
        })
      });
    }
    schema.rendererRequirements = Object.assign({}, schema.rendererRequirements || {});
    schema.rendererRequirements.requiredCapabilities = addUnique(schema.rendererRequirements.requiredCapabilities, 'real-four-face-3d-application-cube');
    schema.rendererRequirements.requiredCapabilities = addUnique(schema.rendererRequirements.requiredCapabilities, 'css-3d-preserve-3d');
    schema.rendererRequirements.forbiddenApplicationCubeFallbacks = ['flatCards', 'carousel', 'horizontalSlider', 'stackedCards', 'flipCard'];
    schema.capabilities = Object.assign({}, schema.capabilities || {});
    schema.capabilities.required = addUnique(schema.capabilities.required, 'real-four-face-3d-application-cube');
    schema.renderer = Object.assign({}, schema.renderer || {}, {
      key: 'applicationCube3D',
      geometry: 'fourVerticalFaces',
      faceCount: 4,
      rotateInPlace: true,
      rotationAxis: 'y'
    });
    schema.animationPresets = Object.assign({}, schema.animationPresets || {}, {
      applicationCubeRotation: Object.assign({
        type: 'keyframed3DRotation',
        axis: 'y',
        faceCount: 4,
        degreesPerFace: 90,
        repeat: 'infinite',
        durationMs: parseInt(cube.durationMs, 10) || 16000,
        rotateInPlace: true
      }, (schema.animationPresets && schema.animationPresets.applicationCubeRotation) || {})
    });
    return schema;
  };
})(window);