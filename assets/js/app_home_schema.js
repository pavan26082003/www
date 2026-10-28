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
    'fa-video': 'video',
    'fa-video-camera': 'video',
    'fa-lock': 'lock',
    'fa-plug': 'devices',
    'fa-water': 'humidity',
    'fa-fan': 'devices',
    'fa-wind': 'devices',
    'fa-door-open': 'devices',
    'fa-solar-panel': 'bolt',
    'fa-utensils': 'utensils',
    'fa-receipt': 'receipt',
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
    'fa-user': 'user',
    'fa-user-circle': 'user',
    'fa-user-pen': 'profile',
    'fa-user-edit': 'profile',
    'fa-signal': 'devices',
    'fa-broadcast-tower': 'devices',
    'fa-tower-broadcast': 'devices',
    'fa-braille': 'grid',
    'fa-quote-left': 'quote',
    'fa-quote-right': 'quote',
    'fa-magnifying-glass': 'search',
    'fa-chevron-right': 'chevron-right',
    'fa-moon': 'appearance',
    'fa-shield-halved': 'shield',
    'fa-palette': 'appearance'
  };

  var FLUTTER_ICON_NAMES = {
    search: 'magnifyingGlass',
    quote: 'quoteLeft',
    user: 'user',
    users: 'users',
    grid: 'borderAll',
    home: 'house',
    devices: 'tableCellsLarge',
    video: 'video',
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
    scanner: 'scanner',
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
    appearance: 'moon',
    category: 'list'
  };

  var WEB_ICON_NAMES = {
    search: 'magnifying-glass',
    quote: 'quote-left',
    user: 'user',
    users: 'users',
    grid: 'border-all',
    home: 'house',
    devices: 'table-cells-large',
    video: 'video',
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
    scanner: 'scanner',
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
    appearance: 'moon',
    category: 'list'
  };

  var BOTTOM_NAV_CONTRACTS = {
    home: { routeId: 'home', actionId: 'navigate-home' },
    devices: { routeId: 'devices', actionId: 'navigate-devices' },
    category: { routeId: 'category', actionId: 'navigate-category' },
    grid: { routeId: 'grid', actionId: 'navigate-grid' },
    alerts: { routeId: 'alerts', actionId: 'navigate-alerts' },
    reports: { routeId: 'reports', actionId: 'navigate-reports' },
    explore: { routeId: 'reports', actionId: 'navigate-reports' },
    rooms: { routeId: 'rooms', actionId: 'navigate-rooms' },
    profile: { routeId: 'profile', actionId: 'navigate-profile' },
    settings: { routeId: 'profile', actionId: 'navigate-profile' }
  };

  /**
   * The bottom navigation is the same four slots for every user: Home, IoT, Grid, Profile.
   * Slot 3 is not configurable. Older saved layouts that stored Alerts (or Reports, Rooms, …)
   * in that slot are ignored.
   */
  var BOTTOM_NAV_SLOTS = [
    { slot: 'home', fixed: true, id: 'home', label: 'Home', iconToken: 'home' },
    { slot: 'iot', fixed: true, id: 'devices', label: 'IoT', iconToken: 'devices' },
    { slot: 'grid', fixed: true, id: 'grid', label: 'Grid', iconToken: 'grid' },
    { slot: 'profile', fixed: true, id: 'profile', label: 'Profile', iconToken: 'profile' }
  ];

  /** Third tab is always Grid. */
  var BOTTOM_NAV_CATEGORY_DEFAULT = 'grid';

  /**
   * Legacy destination labels. The published bottom nav does not read this map;
   * slot 3 is always Grid.
   */
  var BOTTOM_NAV_CATEGORY_TARGETS = {
    grid: { label: 'Grid', iconToken: 'grid' },
    category: { label: 'Category', iconToken: 'category' },
    alerts: { label: 'Alerts', iconToken: 'alerts' },
    reports: { label: 'Reports', iconToken: 'reports' },
    explore: { label: 'Explore', iconToken: 'reports' },
    rooms: { label: 'Rooms', iconToken: 'rooms' }
  };

  /** Third slot is always Grid. Saved per-user targets such as "alerts" are ignored. */
  function resolveCategorySlotTarget() {
    return BOTTOM_NAV_CATEGORY_DEFAULT;
  }

  /** Write Home / IoT / Grid / Profile onto the layout so snapshots match the nav contract. */
  function applySharedBottomNav(layout) {
    layout = layout || {};
    var active = String(layout.activePage || 'home').trim().toLowerCase();
    if (active === 'settings') active = 'profile';
    if (active === 'category') active = 'grid';
    layout.bottomNavCategoryTarget = BOTTOM_NAV_CATEGORY_DEFAULT;
    layout.bottomNav = [
      { id: 'home', icon: 'fa-home', label: 'Home', active: active === 'home' },
      { id: 'devices', icon: 'fa-th', label: 'IoT', active: active === 'devices' },
      { id: 'grid', icon: 'fa-border-all', label: 'Grid', active: active === 'grid' },
      { id: 'profile', icon: 'fa-user', label: 'Profile', active: active === 'profile' }
    ];
    return layout;
  }

  /** @deprecated Use BOTTOM_NAV_CONTRACTS */
  var NAV_ACTIONS = {
    home: 'navigate-home',
    devices: 'navigate-devices',
    alerts: 'navigate-alerts',
    reports: 'navigate-reports',
    explore: 'navigate-reports',
    rooms: 'navigate-rooms',
    profile: 'navigate-profile'
  };

  /**
   * Build the canonical four-slot bottom navigation. Home, IoT, Grid and Profile are
   * fixed for every user. Labels and icons come from the slot contract, not from a
   * saved per-user bottomNav, so every template publishes the same navigation.
   */
  function buildBottomNavigationItems(layout, usedIcons, actionRegistry, routes, pages) {
    layout = layout || {};
    var categoryTarget = resolveCategorySlotTarget(layout);
    var items = [];

    BOTTOM_NAV_SLOTS.forEach(function (slot) {
      var id = slot.fixed ? slot.id : categoryTarget;
      var contract = BOTTOM_NAV_CONTRACTS[id];
      var defaults = slot.fixed ? slot : (BOTTOM_NAV_CATEGORY_TARGETS[id] || slot);
      // Only the configurable slot may fall back; a fixed slot keeps its canonical contract.
      if (!contract || !actionRegistry[contract.actionId] || !routes[contract.routeId] || !pages[contract.routeId]) {
        if (slot.fixed) return;
        id = BOTTOM_NAV_CATEGORY_DEFAULT;
        contract = BOTTOM_NAV_CONTRACTS[id];
        defaults = BOTTOM_NAV_CATEGORY_TARGETS[id];
        if (!contract || !routes[contract.routeId] || !pages[contract.routeId]) return;
      }

      usedIcons[defaults.iconToken] = true;
      items.push({
        id: 'nav-' + id,
        slot: slot.slot,
        fixed: !!slot.fixed,
        label: defaults.label,
        iconToken: defaults.iconToken,
        routeId: contract.routeId,
        actionId: contract.actionId
      });
    });

    return items;
  }

  function filterValidNavigationItems(items, actionRegistry, routes, pages) {
    return (items || []).filter(function (item) {
      if (!item || !item.actionId || !item.routeId) return false;
      return !!(actionRegistry[item.actionId] && routes[item.routeId] && pages[item.routeId]);
    });
  }

  function slugToken(name) {
    return String(name || 'cube')
      .replace(/^fa-/, '')
      .replace(/[^a-z0-9]+/gi, '-')
      .replace(/^-|-$/g, '')
      .toLowerCase() || 'cube';
  }

  function faToToken(faIcon) {
    var v = String(faIcon || 'fa-cube').replace(/^(fas|far|fab|fa-solid|fa-regular|fa-brands)\s+/, '').trim();
    if (FLUTTER_ICON_NAMES[v]) return v;
    if (v.indexOf('fa-') !== 0) v = 'fa-' + v.replace(/^fa-?/, '');
    var mapped = FA_TOKEN_MAP[v] || slugToken(v);
    return FLUTTER_ICON_NAMES[mapped] ? mapped : 'cube';
  }

  function phaseOffset(delaySec, durationSec) {
    var d = Number(delaySec) || 0;
    var dur = Number(durationSec) || 12;
    if (dur <= 0) dur = 12;
    var normalized = d % dur;
    if (normalized < 0) normalized += dur;
    return Math.round((normalized / dur) * 1000) / 1000;
  }

  function buildPhaseOffsetFields(phaseOffsetSec, durationSec) {
    var sec = Number(phaseOffsetSec) || 0;
    var dur = Number(durationSec) || 12;
    return {
      phaseOffsetSec: sec,
      phaseOffsetMs: Math.round(sec * 1000),
      phaseOffsetNormalized: phaseOffset(sec, dur),
      phaseOffsetSemantics: sec < 0 ? 'css-animation-delay-stagger' : 'elapsed-phase-shift'
    };
  }

  function signedFlipStepExpression(stepDeg) {
    var n = Number(stepDeg);
    if (!n) return '+0';
    return n > 0 ? ('+' + n) : String(n);
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
          name: FLUTTER_ICON_NAMES[token] || FLUTTER_ICON_NAMES.cube
        }
      };
    });
    return registry;
  }

  function cloneJson(obj) {
    return JSON.parse(JSON.stringify(obj || {}));
  }

  var CUBE_HOLD_RATIO = 0.84;
  var CUBE_FACE_COUNT = 4;
  var FACE_GEOMETRY = ['front', 'right', 'back', 'left'];

  function resolveCardRotationDirection(card) {
    var isLtr = card && card.rotationDir === 'ltr';
    return {
      rotationDir: isLtr ? 'ltr' : 'rtl',
      rotationDirection: isLtr ? 'left-to-right' : 'right-to-left',
      direction: isLtr ? 'clockwise' : 'counterclockwise',
      degreesPerFace: isLtr ? 90 : -90
    };
  }

  function buildCardFaceSequence() {
    return FACE_GEOMETRY.map(function (name, faceIndex) {
      return {
        faceIndex: faceIndex,
        geometryFace: name,
        contentBinding: 'faces[' + faceIndex + ']',
        label: ['Front', 'Right', 'Back', 'Left'][faceIndex] || ('Face ' + (faceIndex + 1))
      };
    });
  }
  var DEFAULT_HOME_GRID_COLUMNS = 3;
  var GREETING_WAVE = '';
  var DEFAULT_HOME_GRID_ROW_HEIGHT = 96;
  /** Metro mosaic default — thin uniform gutters like Windows Live Tiles. */
  var DEFAULT_GRID_GAP = 4;

  function resolveGridGap(theme) {
    theme = theme || {};
    if (theme.gridGap == null || theme.gridGap === '') {
      return DEFAULT_GRID_GAP;
    }
    var n = Number(theme.gridGap);
    if (isNaN(n)) return DEFAULT_GRID_GAP;
    return Math.max(0, Math.min(24, n));
  }

  /**
   * Home Settings expose only three block kinds. Older layouts used
   * stat/link/add — normalize them so Flutter still gets one contract.
   */
  function normalizeHomeBlockType(type) {
    var t = String(type || '').trim().toLowerCase();
    if (t === 'image') return 'image';
    if (t === 'mixed') return 'mixed';
    if (t === 'stack' || t === 'stat' || t === 'link' || t === 'add' || t === 'action') return 'stack';
    return 'stack';
  }

  function normalizeFaceContentType(face, card) {
    face = face || {};
    card = card || {};
    var explicit = String(face.type || '').trim().toLowerCase();
    if (explicit === 'mixed') return 'mixed';
    if (explicit === 'image') return 'image';
    if (explicit === 'stack' || explicit === 'stat' || explicit === 'link' || explicit === 'action' || explicit === 'add') {
      return 'stack';
    }
    var block = normalizeHomeBlockType(card.type);
    if (block === 'mixed') {
      var hasImg = !!(face.image || card.image);
      var hasText = !!(face.title || face.label || face.subtitle || face.value || face.icon);
      return hasImg && hasText ? 'mixed' : (hasImg ? 'image' : 'stack');
    }
    if (block === 'image') {
      if (face.image || card.image) return 'image';
      return 'stack';
    }
    return 'stack';
  }

  /** Stable per-tile stagger so API builds do not rotate every tile in sync. */
  function stableCardAnimOffsetSec(cardId, index, totalCount, cycleSec) {
    var speed = Math.max(8, Number(cycleSec) || 16);
    var count = Math.max(1, totalCount || 1);
    var slot = Math.max(0, index || 0);
    var hash = String(cardId || '').split('').reduce(function (sum, ch) {
      return sum + ch.charCodeAt(0);
    }, 0);
    var segment = speed / CUBE_FACE_COUNT;
    var indexSpread = (slot / count) * speed;
    var hashSpread = ((hash % 997) / 997) * speed * 0.88;
    var microSpread = ((hash % 17) / 17) * segment * 0.95;
    var saltSpread = ((hash % 43) / 43) * segment * 0.35;
    return -((indexSpread + hashSpread + microSpread + saltSpread) % speed);
  }

  function extractUrlOrigin(url) {
    var raw = String(url || '').trim();
    if (!raw) return '';
    try {
      var base = (typeof window !== 'undefined' && window.location && window.location.href)
        ? window.location.href
        : 'https://efactor.tech/';
      return new URL(raw, base).origin;
    } catch (e) {
      return '';
    }
  }

  function resolveApiOrigin(cfg) {
    cfg = cfg || {};
    var fromApiBase = extractUrlOrigin(cfg.apiBase);
    if (fromApiBase) return fromApiBase;
    var fromSchema = extractUrlOrigin(cfg.schemaApiUrl);
    if (fromSchema) return fromSchema;
    var fromDashboard = extractUrlOrigin(cfg.dashboardUrl);
    if (fromDashboard) return fromDashboard;
    if (typeof window !== 'undefined' && window.location && window.location.origin) {
      return window.location.origin;
    }
    return 'https://efactor.tech';
  }

  function resolveDashboardApiEndpoint(cfg) {
    cfg = cfg || {};
    var apiBase = String(cfg.apiBase || '').replace(/\/+$/, '');
    if (apiBase) return apiBase + '/layout';
    return resolveApiOrigin(cfg) + '/index.php/app-home-page/api/layout';
  }

  function buildDashboardDataSource(cfg) {
    cfg = cfg || {};
    return {
      type: 'rest',
      endpoint: resolveDashboardApiEndpoint(cfg),
      method: 'GET',
      headers: { Accept: 'application/json' },
      responseSchema: { '$ref': '#/models/DashboardResponse' },
      responseMapping: {
        alerts: '$.layout.alerts',
        cards: '$.layout.cards',
        summary: '$.layout.alerts'
      },
      cachePolicy: 'network-first',
      cacheTtlSec: 60,
      timeoutMs: 15000,
      retry: { enabled: true, maximumAttempts: 2, backoffMs: 800 },
      errorPolicy: {
        useCachedData: true,
        componentId: 'home-error'
      },
      requires: ['dashboard.read'],
      published: true
    };
  }

  function buildApiContract(cfg) {
    cfg = cfg || {};
    var origin = resolveApiOrigin(cfg);
    return {
      environments: {
        development: { baseUrl: origin },
        staging: { baseUrl: origin },
        production: { baseUrl: origin }
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
      return Math.max(1, Math.min(12, Number(theme.gridColumns)));
    }
    return DEFAULT_HOME_GRID_COLUMNS;
  }

  function resolveHomeGridRows(theme) {
    theme = theme || {};
    if (theme.gridRows != null && !isNaN(Number(theme.gridRows))) {
      return Math.max(1, Math.min(16, Number(theme.gridRows)));
    }
    return 4;
  }

  function resolveHomeGridRowHeight(theme) {
    theme = theme || {};
    if (theme.gridRowHeight != null && !isNaN(Number(theme.gridRowHeight))) {
      return Math.max(40, Math.min(160, Number(theme.gridRowHeight)));
    }
    return DEFAULT_HOME_GRID_ROW_HEIGHT;
  }

  var RESPONSIVE_TEXT_OVERFLOW = {
    greeting: { maxLines: 1, overflow: 'ellipsis', softWrap: true },
    title: { maxLines: 2, overflow: 'ellipsis', softWrap: true },
    subtitle: { maxLines: 2, overflow: 'ellipsis', softWrap: true },
    cardTitle: { maxLines: 3, overflow: 'clip', softWrap: true },
    cardSubtitle: { maxLines: 2, overflow: 'clip', softWrap: true },
    statValue: { maxLines: 1, overflow: 'ellipsis', softWrap: false },
    statLabel: { maxLines: 2, overflow: 'ellipsis', softWrap: true }
  };

  /**
   * rowHeight and minRowHeight are always dp numbers. The app rejects a
   * non-numeric rowHeight (it becomes null and the home grid is not drawn).
   */
  function buildResponsiveGridLayouts(theme) {
    theme = theme || {};
    var homeGridColumns = resolveHomeGridColumns(theme);
    var gridGap = resolveGridGap(theme);
    var homeGridRowHeight = resolveHomeGridRowHeight(theme);
    var narrowColumns = Math.min(2, homeGridColumns);
    function track(numericHeight) {
      var n = Number(numericHeight);
      if (!isFinite(n)) n = homeGridRowHeight;
      return n;
    }
    return {
      narrow: {
        columns: narrowColumns,
        columnGap: gridGap,
        rowGap: gridGap,
        rowHeight: track(homeGridRowHeight),
        minRowHeight: track(homeGridRowHeight),
        maximumContentWidth: 359,
        minimumCellWidthDp: 96
      },
      compact: {
        columns: homeGridColumns,
        columnGap: gridGap,
        rowGap: gridGap,
        rowHeight: track(homeGridRowHeight),
        minRowHeight: track(homeGridRowHeight),
        minimumCellWidthDp: 96
      },
      medium: {
        columns: Math.max(homeGridColumns, 4),
        columnGap: gridGap,
        rowGap: gridGap,
        rowHeight: track(Math.max(88, homeGridRowHeight - 8)),
        minRowHeight: track(Math.max(88, homeGridRowHeight - 8)),
        maximumContentWidth: 720
      },
      expanded: {
        columns: Math.max(homeGridColumns, 6),
        columnGap: gridGap,
        rowGap: gridGap,
        rowHeight: track(Math.max(96, homeGridRowHeight)),
        minRowHeight: track(Math.max(96, homeGridRowHeight)),
        maximumContentWidth: 1200
      }
    };
  }

  function homeGridLayouts(theme) {
    return buildResponsiveGridLayouts(theme);
  }

  function clampColumnSpan(columnSpan, maxColumns) {
    return Math.max(1, Math.min(parseInt(columnSpan, 10) || 1, Math.max(1, maxColumns || 1)));
  }

  function buildCardResponsiveLayout(card, theme) {
    theme = theme || {};
    var layouts = buildResponsiveGridLayouts(theme);
    var col = clampColumnSpan(card && card.colSpan, resolveHomeGridColumns(theme));
    var row = Math.max(1, Math.min(parseInt(card && card.rowSpan, 10) || 1, resolveHomeGridRows(theme)));
    return {
      narrow: { columnSpan: clampColumnSpan(col, layouts.narrow.columns), rowSpan: row },
      compact: { columnSpan: clampColumnSpan(col, layouts.compact.columns), rowSpan: row },
      medium: { columnSpan: clampColumnSpan(col, layouts.medium.columns), rowSpan: row },
      expanded: { columnSpan: clampColumnSpan(col, layouts.expanded.columns), rowSpan: row }
    };
  }

  function buildFaceContentLayout(faceType) {
    faceType = normalizeHomeBlockType(faceType === 'stat' || faceType === 'link' || faceType === 'action' || faceType === 'add'
      ? 'stack'
      : faceType) || 'stack';
    if (faceType === 'stat') faceType = 'stack';
    if (faceType === 'stack') {
      return {
        mode: 'stack',
        direction: 'column',
        align: 'stretch',
        justify: 'space-between',
        primaryField: 'title',
        secondaryField: 'subtitle',
        valueField: 'value',
        primaryRole: 'title',
        secondaryRole: 'subtitle',
        iconPosition: 'center',
        captionPosition: 'bottom-left',
        valuePosition: 'center',
        labelPosition: 'bottom-left',
        overflow: 'clip',
        titleMaxLines: RESPONSIVE_TEXT_OVERFLOW.statLabel.maxLines,
        subtitleMaxLines: RESPONSIVE_TEXT_OVERFLOW.statValue.maxLines,
        padding: { horizontal: 10, vertical: 8 },
        gap: 0,
        iconGap: 4,
        alignText: 'left',
        style: 'startTile'
      };
    }
    if (faceType === 'mixed') {
      return {
        mode: 'mixed',
        direction: 'column',
        align: 'stretch',
        justify: 'space-between',
        primaryField: 'title',
        secondaryField: 'subtitle',
        imageField: 'image',
        primaryRole: 'title',
        secondaryRole: 'subtitle',
        iconPosition: 'center',
        captionPosition: 'bottom-left',
        labelPosition: 'bottom-left',
        overflow: 'clip',
        titleMaxLines: RESPONSIVE_TEXT_OVERFLOW.cardTitle.maxLines,
        subtitleMaxLines: RESPONSIVE_TEXT_OVERFLOW.cardSubtitle.maxLines,
        padding: { horizontal: 10, vertical: 8 },
        gap: 0,
        alignText: 'left',
        style: 'startTile'
      };
    }
    if (faceType === 'image') {
      return {
        direction: 'column',
        align: 'stretch',
        justify: 'flex-end',
        primaryField: 'title',
        secondaryField: 'subtitle',
        primaryRole: 'overlayTitle',
        secondaryRole: 'overlaySubtitle',
        imageField: 'image',
        mode: 'image',
        overflow: 'clip',
        titleMaxLines: RESPONSIVE_TEXT_OVERFLOW.cardTitle.maxLines,
        subtitleMaxLines: RESPONSIVE_TEXT_OVERFLOW.cardSubtitle.maxLines,
        padding: { horizontal: 10, vertical: 8 },
        gap: 2,
        alignText: 'left',
        captionPosition: 'bottom-left',
        labelPosition: 'bottom-left'
      };
    }
    return {
      mode: 'stack',
      direction: 'column',
      align: 'center',
      justify: 'center',
      primaryField: 'title',
      secondaryField: 'subtitle',
      primaryRole: 'title',
      secondaryRole: 'subtitle',
      iconPosition: 'top',
      overflow: 'clip',
      titleMaxLines: RESPONSIVE_TEXT_OVERFLOW.cardTitle.maxLines,
      subtitleMaxLines: RESPONSIVE_TEXT_OVERFLOW.cardSubtitle.maxLines,
      padding: { horizontal: 8, vertical: 10 },
      gap: 5,
      iconGap: 4,
      alignText: 'center'
    };
  }

  function resolveCardFaceSec(card, theme, helpers) {
    // Prefer card.faceSec / theme when helper is the broken "always 16" stub.
    if (card && card.faceSec != null && card.faceSec !== '' && !isNaN(parseFloat(card.faceSec))) {
      return Math.max(2, Math.min(8, parseFloat(card.faceSec)));
    }
    if (helpers && typeof helpers.getCardFaceSec === 'function') {
      var fromHelper = helpers.getCardFaceSec(card);
      if (fromHelper != null && !isNaN(parseFloat(fromHelper))) {
        var helperSec = parseFloat(fromHelper);
        // Treat full-cycle seconds mistakenly returned as faceSec.
        if (helperSec > 8) helperSec = helperSec / CUBE_FACE_COUNT;
        return Math.max(2, Math.min(8, helperSec));
      }
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

  var DEFAULT_FACE_TYPOGRAPHY = {
    stack: {
      iconSize: 20, titleFontSize: 12, subtitleFontSize: 22,
      titleFontWeight: 800, subtitleFontWeight: 800,
      titleLineHeight: 1.15, subtitleLineHeight: 1.05,
      contentGap: 4, iconGap: 4, textAlign: 'center',
      titleMaxLines: 2, subtitleMaxLines: 1, overflow: 'clip'
    },
    mixed: {
      iconSize: 16, titleFontSize: 12, subtitleFontSize: 10,
      titleFontWeight: 800, subtitleFontWeight: 700,
      titleLineHeight: 1.1, subtitleLineHeight: 1.15,
      contentGap: 2, iconGap: 4, textAlign: 'left',
      titleMaxLines: 2, subtitleMaxLines: 1, overflow: 'clip'
    },
    stat: {
      iconSize: 20, titleFontSize: 9, subtitleFontSize: 24,
      titleFontWeight: 800, subtitleFontWeight: 900,
      titleLineHeight: 1.2, subtitleLineHeight: 1,
      contentGap: 6, iconGap: 4, textAlign: 'center',
      titleMaxLines: 2, subtitleMaxLines: 1, overflow: 'ellipsis'
    },
    link: {
      iconSize: 20, titleFontSize: 11, subtitleFontSize: 8,
      titleFontWeight: 800, subtitleFontWeight: 700,
      titleLineHeight: 1.15, subtitleLineHeight: 1.15,
      contentGap: 5, iconGap: 4, textAlign: 'center',
      titleMaxLines: 3, subtitleMaxLines: 2, overflow: 'clip'
    },
    image: {
      iconSize: 18, titleFontSize: 13, subtitleFontSize: 10,
      titleFontWeight: 800, subtitleFontWeight: 700,
      titleLineHeight: 1.1, subtitleLineHeight: 1.15,
      contentGap: 2, textAlign: 'center',
      titleMaxLines: 1, subtitleMaxLines: 1, overflow: 'clip'
    },
    action: {
      iconSize: 20, titleFontSize: 11, subtitleFontSize: 8,
      titleFontWeight: 800, subtitleFontWeight: 700,
      titleLineHeight: 1.15, subtitleLineHeight: 1.15,
      contentGap: 5, iconGap: 4, textAlign: 'center',
      titleMaxLines: 3, subtitleMaxLines: 1, overflow: 'clip'
    },
    text: {
      iconSize: 20, titleFontSize: 11, subtitleFontSize: 8,
      titleFontWeight: 800, subtitleFontWeight: 700,
      titleLineHeight: 1.15, subtitleLineHeight: 1.15,
      contentGap: 5, textAlign: 'center',
      titleMaxLines: 3, subtitleMaxLines: 2, overflow: 'clip'
    },
    device: {
      iconSize: 28, titleFontSize: 10, subtitleFontSize: 10, valueFontSize: 20, locationFontSize: 8,
      titleFontWeight: 600, subtitleFontWeight: 700,
      titleMaxLines: 2, subtitleMaxLines: 1, overflow: 'ellipsis'
    }
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
      titleFontWeight: face.titleFontWeight != null ? Number(face.titleFontWeight) : (defaults.titleFontWeight || 800),
      subtitleFontWeight: face.subtitleFontWeight != null ? Number(face.subtitleFontWeight) : (defaults.subtitleFontWeight || 700),
      titleLineHeight: face.titleLineHeight != null ? Number(face.titleLineHeight) : (defaults.titleLineHeight || 1.12),
      subtitleLineHeight: face.subtitleLineHeight != null ? Number(face.subtitleLineHeight) : (defaults.subtitleLineHeight || 1.1),
      contentGap: face.contentGap != null ? Number(face.contentGap) : (defaults.contentGap != null ? defaults.contentGap : 4),
      iconGap: face.iconGap != null ? Number(face.iconGap) : (defaults.iconGap != null ? defaults.iconGap : 4),
      textAlign: face.textAlign || defaults.textAlign || 'center',
      titleFontFamily: titleFontFamily,
      subtitleFontFamily: subtitleFontFamily,
      titleMaxLines: face.titleMaxLines != null ? Number(face.titleMaxLines) : defaults.titleMaxLines,
      subtitleMaxLines: face.subtitleMaxLines != null ? Number(face.subtitleMaxLines) : defaults.subtitleMaxLines,
      overflow: face.overflow || defaults.overflow || 'ellipsis',
      softWrap: face.softWrap != null ? !!face.softWrap : true
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
      titleTextColor: face.titleTextColor || face.textColor || parent.textColor || '#ffffff',
      subtitleTextColor: face.subTextColor || face.textColor || parent.textColor || '#ffffff',
      iconColor: face.iconColor || parent.iconColor || '#ffffff'
    };
  }

  function buildCubeGeometryContract(directionMeta) {
    directionMeta = directionMeta || resolveCardRotationDirection(null);
    var stepDeg = directionMeta.degreesPerFace != null
      ? directionMeta.degreesPerFace
      : ((directionMeta.rotationDir || 'rtl') === 'ltr' ? 90 : -90);
    return {
      model: 'fourFaceBoxRotation',
      rendererKey: 'fourFaceBoxRotation',
      axis: 'y',
      degreesPerFace: stepDeg,
      direction: directionMeta.direction || 'counterclockwise',
      rotationDir: directionMeta.rotationDir || 'rtl',
      rotationDirection: directionMeta.rotationDirection || 'right-to-left',
      faceSequence: buildCardFaceSequence(),
      transformStyle: 'preserve-3d',
      perspective: 'host',
      perspectivePx: 800,
      faceStacking: 'four-face-box',
      visibleFacePolicy: 'front-face-by-angle',
      activeFaceBinding: 'faceIndex',
      activeFaceClass: 'ahp-cube-face-front',
      // Real box: depth equals face width so left/right sides are true faces.
      depthFormula: 'slotWidth',
      sizeFormula: 'slotWidth',
      halfDepthFormula: 'slotWidth / 2',
      parentCenteringTransform: 'translateZ(-halfDepth)',
      backfaceVisibility: 'hidden',
      forbiddenInterpretations: ['flatFlipCard', 'edgeFlipSwap', 'carousel', 'horizontalSlider', 'stackedCards', 'opacityFaceSwap'],
      facePlacement: FACE_GEOMETRY.map(function (name, i) {
        return {
          faceIndex: i,
          geometryFace: name,
          cssClass: 'face-' + i,
          baseTransform: 'rotateY(' + (i * 90) + 'deg) translateZ(halfDepth)',
          rotateYDeg: i * 90,
          translateZ: 'halfDepth'
        };
      }),
      note: 'Four-sided box: faces on front/right/back/left. Parent uses translateZ(-half) rotateY(angle). Dwell then turn 90deg. NOT a flat flip card.'
    };
  }

  function buildCubeAnimationContract(durationMs, phaseOffsetSec, directionMeta) {
    directionMeta = directionMeta || resolveCardRotationDirection(null);
    var segmentMs = rotationSegmentMs(durationMs);
    var stepDeg = directionMeta.degreesPerFace != null
      ? directionMeta.degreesPerFace
      : ((directionMeta.rotationDir || 'rtl') === 'ltr' ? 90 : -90);
    var stepExpr = signedFlipStepExpression(stepDeg);
    var phaseFields = buildPhaseOffsetFields(phaseOffsetSec, durationMs / 1000);
    return {
      driver: 'procedural-rAF',
      algorithm: 'dwell-then-rotateY',
      model: 'fourFaceBoxRotation',
      axis: 'y',
      direction: directionMeta.direction || 'counterclockwise',
      rotationDir: directionMeta.rotationDir || 'rtl',
      rotationDirection: directionMeta.rotationDirection || 'right-to-left',
      degreesPerFace: stepDeg,
      faceSequence: buildCardFaceSequence(),
      faceCount: CUBE_FACE_COUNT,
      durationMs: durationMs,
      faceDurationMs: Math.round(segmentMs),
      phaseOffsetSec: phaseFields.phaseOffsetSec,
      phaseOffsetMs: phaseFields.phaseOffsetMs,
      phaseOffsetNormalized: phaseFields.phaseOffsetNormalized,
      phaseOffsetSemantics: phaseFields.phaseOffsetSemantics,
      holdRatio: CUBE_HOLD_RATIO,
      transitionRatio: 1 - CUBE_HOLD_RATIO,
      holdMs: rotationDwellMs(durationMs),
      transitionMs: rotationTransitionMs(durationMs),
      faceSwapAtFlipProgress: null,
      stepExpression: stepExpr,
      rotateSequence: [
        { phase: 'dwell', rotateParentY: 'hold at segmentBaseAngle' },
        { phase: 'rotate', rotateParentY: 'ease from segmentBaseAngle toward segmentBaseAngle ' + stepExpr },
        { phase: 'complete', rotateParentY: 'segmentBaseAngle ' + stepExpr }
      ],
      easing: {
        name: 'ease-in-out-cubic',
        implementation: 't < 0.5 ? 4*t*t*t : 1 - Math.pow(-2*t + 2, 3) / 2'
      },
      angleCalculation: {
        segmentIndex: 'floor(elapsed / segmentSec) % 4',
        baseAngleDeg: 'segmentIndex * ' + stepDeg,
        rotateProgress: 'max(0, (elapsedInSegment - holdSec) / transitionSec)',
        currentAngleDeg: 'baseAngle + (nextAngle - baseAngle) * ease(rotateProgress)',
        activeFaceIndex: 'round(currentAngleDeg / degreesPerFace) % 4',
        parentTransform: 'translateZ(-halfDepth) rotateY(currentAngleDeg)'
      },
      stagger: { enabled: true, source: 'card-index-hash-offset' },
      note: 'Hold on each face of the box, then rotateY 90deg to the next side. Independent per-tile phaseOffset.'
    };
  }

  function buildCardRotationBlock(card, index, theme, helpers, totalCards) {
    var durationMs = resolveCardDurationMs(card, theme, helpers);
    var cardSpeedSec = durationMs / 1000;
    var faceDurationMs = Math.round(durationMs / CUBE_FACE_COUNT);
    var dwellMs = rotationDwellMs(durationMs);
    var transitionMs = rotationTransitionMs(durationMs);
    var phaseOffsetSec;
    if (card && card.startDelayMs != null && card.startDelayMs !== '' && !isNaN(Number(card.startDelayMs))) {
      phaseOffsetSec = -(Math.max(0, Number(card.startDelayMs)) / 1000);
    } else if (helpers && typeof helpers.getCardAnimOffset === 'function') {
      phaseOffsetSec = helpers.getCardAnimOffset(card && card.id, index, totalCards || 1, cardSpeedSec);
    } else {
      phaseOffsetSec = stableCardAnimOffsetSec(card && card.id, index, totalCards || 1, cardSpeedSec);
    }
    var directionMeta = resolveCardRotationDirection(card);
    var phaseFields = buildPhaseOffsetFields(phaseOffsetSec, cardSpeedSec);
    var rotationEnabled = card && card.rotationEnabled === false ? false : true;
    return {
      enabled: rotationEnabled,
      presetId: 'tile-face-rotation',
      model: 'fourFaceBoxRotation',
      rendererKey: 'fourFaceBoxRotation',
      axis: 'y',
      loop: true,
      // Additive aliases for Metro contract (existing names kept below).
      faceDuration: faceDurationMs,
      transitionDuration: transitionMs,
      startDelay: Math.abs(phaseFields.phaseOffsetMs || 0),
      rotationDir: directionMeta.rotationDir,
      rotationDirection: directionMeta.rotationDirection,
      direction: directionMeta.direction,
      degreesPerFace: directionMeta.degreesPerFace,
      faceSequence: buildCardFaceSequence(),
      phaseOffset: phaseFields.phaseOffsetNormalized,
      phaseOffsetSec: phaseFields.phaseOffsetSec,
      phaseOffsetMs: phaseFields.phaseOffsetMs,
      phaseOffsetSemantics: phaseFields.phaseOffsetSemantics,
      durationMs: durationMs,
      faceCount: CUBE_FACE_COUNT,
      faceDurationMs: faceDurationMs,
      dwellMs: dwellMs,
      transitionMs: transitionMs,
      dwellRatio: CUBE_HOLD_RATIO,
      transitionRatio: 1 - CUBE_HOLD_RATIO,
      geometry: buildCubeGeometryContract(directionMeta),
      animation: buildCubeAnimationContract(durationMs, phaseOffsetSec, directionMeta),
      cssDelayVar: '--ahp-cube-delay',
      cssDelayValueSec: phaseOffsetSec
    };
  }

  function buildUiRenderContract(theme) {
    theme = theme || {};
    var cycleMs = theme.cardSpeed != null ? Number(theme.cardSpeed) * 1000 : 16000;
    var isLight = theme.bgStyle === 'light';
    var logout = resolveLogoutButtonColors(theme);
    var defaultRotation = resolveCardRotationDirection(null);
    var responsiveLayouts = homeGridLayouts(theme);
    var gridGap = resolveGridGap(theme);
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
        columnGap: gridGap,
        rowGap: gridGap,
        gap: gridGap,
        rowHeightDp: resolveHomeGridRowHeight(theme),
        minCardRowHeightDp: resolveHomeGridRowHeight(theme),
        sizeFormula: 'slotWidth',
        cardWidthFormula: '(cellWidth * columnSpan) + ((columnSpan - 1) * columnGap)',
        cardHeightFormula: '(rowHeight * rowSpan) + ((rowSpan - 1) * rowGap)',
        responsiveLayouts: responsiveLayouts
      },
      responsive: {
        enabled: true,
        defaultBreakpoint: 'compact',
        breakpoints: ['narrow', 'compact', 'medium', 'expanded'],
        layouts: responsiveLayouts,
        breakpointColumns: {
          narrow: responsiveLayouts.narrow.columns,
          compact: responsiveLayouts.compact.columns,
          medium: responsiveLayouts.medium.columns,
          expanded: responsiveLayouts.expanded.columns
        }
      },
      cubeGeometry: buildCubeGeometryContract(defaultRotation),
      cubeAnimation: buildCubeAnimationContract(cycleMs, 0, defaultRotation),
      tileRotation: {
        model: 'fourFaceBoxRotation',
        geometry: buildCubeGeometryContract(defaultRotation),
        animation: buildCubeAnimationContract(cycleMs, 0, defaultRotation)
      },
      themeCssVariables: {
        '--ahp-phone-bg': theme.phoneBg || '#f3f4f6',
        '--ahp-surface': theme.surfaceColor || theme.alertBg || '#ffffff',
        '--ahp-grid-gap': resolveGridGap(theme) + 'px',
        '--ahp-grid-columns': String(resolveHomeGridColumns(theme)),
        '--ahp-grid-row-height': resolveHomeGridRowHeight(theme) + 'px',
        '--ahp-card-speed': (theme.cardSpeed != null ? Number(theme.cardSpeed) : 16) + 's',
        '--ahp-card-shadow': isLight
          ? '0 18px 40px rgba(15,23,42,.08)'
          : '0 18px 40px rgba(0,0,0,.35)',
        '--ahp-logout-bg': logout.background,
        '--ahp-logout-text': logout.text
      },
      faceTypographyDefaults: DEFAULT_FACE_TYPOGRAPHY,
      homeHeader: {
        greetingFontSize: 13,
        greetingFontWeight: 600,
        titleFontSize: 18,
        titleFontWeight: 800,
        titleLineHeight: 1.15,
        titleLetterSpacing: 0,
        subtitleFontSize: 11,
        logoWidth: 96,
        logoHeight: 28,
        logoMaxWidth: 96,
        logoMaxHeight: 28,
        avatarSize: 46,
        avatarFontSize: 15,
        avatarFontWeight: 800,
        layoutGap: 10,
        marginBottom: 12,
        textColumnMinWidth: 0,
        textOverflow: 'ellipsis',
        titleMaxLines: 2,
        subtitleMaxLines: 2,
        greetingMaxLines: 1
      },
      bottomNavigation: buildBottomNavAppearance(theme),
      profilePage: buildProfileAppearanceContract(theme)
    };
  }

  function mediaOrigin(cfg) {
    var filesBase = cfg && cfg.filesBase ? String(cfg.filesBase) : '';
    var match = filesBase.match(/^(https?:\/\/[^/]+)/i);
    if (match) return match[1];
    var schemaApi = cfg && cfg.schemaApiUrl ? String(cfg.schemaApiUrl) : '';
    match = schemaApi.match(/^(https?:\/\/[^/]+)/i);
    if (match) return match[1];
    if (typeof window !== 'undefined' && window.location && window.location.origin) return window.location.origin;
    return '';
  }

  function resolveMediaUrl(url, cfg) {
    var src = String(url || '').trim();
    if (!src) return '';
    if (/^https?:\/\//i.test(src)) return src;
    if (src.charAt(0) === '/') {
      var origin = mediaOrigin(cfg);
      return origin ? origin + src : src;
    }
    if (src.indexOf('files/') === 0 || src.indexOf('assets/') === 0 || src.indexOf('uploads/') === 0) {
      var root = mediaOrigin(cfg);
      return root ? root + '/' + src : '/' + src;
    }
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
        imageFit: f.imageFit || c.imageFit || 'cover',
        imagePosition: f.imagePosition || c.imagePosition || 'center',
        imageScale: f.imageScale != null ? f.imageScale : (c.imageScale != null ? c.imageScale : 100),
        route: f.route || (fi === 0 ? c.pageRoute : '') || ''
      };
    });
    return c;
  }

  function resolveStaticUiTemplateId(layout) {
    layout = layout || {};
    var theme = layout.theme || {};
    return String(layout.staticUiTemplate || theme.staticUiTemplate || '').trim() || null;
  }

  function buildStaticUiSnapshot(layout, cfg) {
    layout = layout || {};
    cfg = cfg || {};
    var templateId = resolveStaticUiTemplateId(layout);
    if (!templateId) return null;
    var api = global.AhpStaticUi;
    if (api && typeof api.toSchema === 'function') {
      return api.toSchema(templateId, {
        logoUrl: (cfg && cfg.logoUrl) || (api.logoUrl) || 'https://efactor.tech/files/home/Logo_new.png'
      });
    }
    return {
      enabled: true,
      mode: 'staticHomeUi',
      templateId: templateId,
      brand: {
        name: 'eFactor',
        logoUrl: (cfg && cfg.logoUrl) || 'https://efactor.tech/files/home/Logo_new.png'
      },
      rendererKey: 'staticHomeUi',
      note: 'Static home UI kit selected. Load AhpStaticUi for full kit metadata.'
    };
  }

  function applyStaticUiAppearanceToTheme(theme, staticUi) {
    theme = theme || {};
    if (!staticUi || !staticUi.enabled) return theme;
    var appearance = staticUi.appearance || {};
    var accent = appearance.accentColor || appearance.navActiveColor || theme.accentColor || '#ef4444';
    theme.phoneBg = appearance.phoneBg || '#ffffff';
    theme.navBg = appearance.navBg || '#ffffff';
    theme.navActiveColor = appearance.navActiveColor || accent;
    theme.accentColor = accent;
    theme.surfaceColor = appearance.surfaceColor || '#ffffff';
    theme.textPrimary = appearance.textPrimary || '#111827';
    theme.textSecondary = appearance.textSecondary || '#374151';
    theme.textMuted = appearance.textMuted || '#6b7280';
    theme.alertBg = appearance.surfaceColor || '#ffffff';
    theme.bgStyle = appearance.bgStyle || 'light';
    theme.welcomeStart = appearance.welcomeStart || accent;
    theme.welcomeEnd = appearance.welcomeEnd || accent;
    theme.showWelcome = false;
    theme.showAlerts = false;
    theme.staticUiTemplate = staticUi.templateId || theme.staticUiTemplate;
    return theme;
  }

  function buildStaticHomeUiComponent(staticUi) {
    if (!staticUi || !staticUi.enabled) return null;
    return Object.assign({
      id: 'static-home-ui',
      type: 'staticHomeUi',
      visible: true
    }, staticUi);
  }

  function applyStaticHomeUiToComponents(components, layout, cfg, theme) {
    components = components || {};
    var staticUi = buildStaticUiSnapshot(layout, cfg);
    if (!staticUi || !staticUi.enabled) return null;
    var staticComp = buildStaticHomeUiComponent(staticUi);
    if (!staticComp) return null;
    components['static-home-ui'] = staticComp;
    if (components['home-page']) {
      var priorHomeLayout = components['home-page'].layout || {};
      var priorPad = priorHomeLayout.padding && typeof priorHomeLayout.padding === 'object'
        ? priorHomeLayout.padding
        : {};
      components['home-page'] = Object.assign({}, components['home-page'], {
        children: ['static-home-ui'],
        layout: Object.assign({}, priorHomeLayout, {
          padding: {
            left: 0,
            right: 0,
            top: 0,
            bottom: Math.max(Number(priorPad.bottom) || 0, NAV_CHROME_BOTTOM)
          },
          gap: 0,
          reservedBottomInset: NAV_CHROME_BOTTOM,
          contentAvoidsBottomNavigation: true,
          overflowX: 'hidden',
          overflowY: 'auto'
        }),
        uiMode: 'staticHomeUi',
        staticUiTemplate: staticUi.templateId
      });
    }
    if (components['main-bottom-navigation']) {
      components['main-bottom-navigation'] = Object.assign({}, components['main-bottom-navigation'], {
        appearance: Object.assign(
          {},
          components['main-bottom-navigation'].appearance || {},
          buildBottomNavAppearance(theme || (layout && layout.theme) || {})
        )
      });
    }
    ['home-header', 'welcome-banner', 'alerts-summary', 'home-grid'].forEach(function (id) {
      if (!components[id]) return;
      components[id] = Object.assign({}, components[id], {
        visible: false,
        visibleWhen: { equals: [true, false] }
      });
    });
    return staticUi;
  }

  var LIGHT_HOME_KITS = {
    'efactor-quickmart': true,
    'efactor-market': true,
    'efactor-deals': true,
    'efactor-shop': true,
    'efactor-style': true
  };

  function publishedCardRadius(value) {
    if (value == null || value === '') return 16;
    var n = Number(value);
    if (!isFinite(n) || n < 0) return 16;
    if (n === 0) return 0;
    if (n > 72) return 72;
    return Math.round(n);
  }

  function themeIsDarkCanvas(theme) {
    theme = theme || {};
    if (String(theme.bgStyle || '').toLowerCase() === 'dark') return true;
    var bg = String(theme.phoneBg || '').trim().toLowerCase();
    return bg === '#0b0f1a' || bg === '#0b1220' || bg === '#111827' || bg === '#151b2e' || bg === '#000000';
  }

  /**
   * Studio live preview is the dark bento. A leftover light kit id makes
   * the app draw QuickMart instead of that canvas. Radius above 16 is a
   * pill on a ~105px column; the studio canvas reads as 14–16.
   */
  function calibratePublishedHomeCanvas(layout) {
    layout = layout || {};
    var theme = layout.theme || {};
    layout.theme = theme;
    var staticUi = String(layout.staticUiTemplate || theme.staticUiTemplate || '').trim();
    var kit = String(theme.homeTemplate || layout.homeTemplate || '').toLowerCase();
    if (!staticUi && themeIsDarkCanvas(theme) && LIGHT_HOME_KITS[kit]) {
      layout.homeTemplate = 'default';
      theme.homeTemplate = 'default';
      theme.showWelcome = true;
    }
    if (!staticUi && (theme.showWelcome == null)) theme.showWelcome = true;
    var explicitRadii = [];
    if (Array.isArray(layout.cards)) {
      layout.cards.forEach(function (card) {
        if (!card || card.borderRadius == null || card.borderRadius === '') return;
        card.borderRadius = publishedCardRadius(card.borderRadius);
        explicitRadii.push(card.borderRadius);
      });
    }
    // A tile radius of 0 is square. Do not leave radius.card at 16 when every tile is 0.
    if (explicitRadii.length && explicitRadii.every(function (n) { return n === 0; })) {
      theme.cardBorderRadius = 0;
    } else {
      theme.cardBorderRadius = publishedCardRadius(theme.cardBorderRadius);
    }
    if (!staticUi) layout.homeTemplate = theme.homeTemplate || layout.homeTemplate || 'default';
    return layout;
  }

  function buildLayoutSnapshot(layout, cfg) {
    layout = calibratePublishedHomeCanvas(layout || {});
    cfg = cfg || {};
    var theme = layout.theme || {};
    var homeTemplate = theme.homeTemplate || layout.homeTemplate || 'default';
    var staticUiTemplate = layout.staticUiTemplate || theme.staticUiTemplate || null;
    var staticUi = buildStaticUiSnapshot(layout, cfg);
    return {
      version: 1,
      activePage: layout.activePage || 'home',
      homeTemplate: homeTemplate,
      staticUiTemplate: staticUiTemplate,
      staticUi: staticUi,
      theme: Object.assign({}, cloneJson(theme), {
        gridGap: resolveGridGap(theme),
        homeTemplate: homeTemplate,
        staticUiTemplate: staticUiTemplate,
        showBottomNav: theme.showBottomNav !== false,
        showWelcome: theme.showWelcome !== false,
        cardBorderRadius: publishedCardRadius(theme.cardBorderRadius)
      }),
      header: cloneJson(layout.header),
      welcome: cloneJson(layout.welcome),
      alerts: cloneJson(layout.alerts),
      cards: (layout.cards || []).map(function (card) {
        return normalizeLayoutSnapshotCard(card, theme, cfg);
      }),
      devicesPage: cloneJson(layout.devicesPage),
      profilePage: cloneJson(mergeLayoutProfile(layout, cfg)),
      bottomNav: cloneJson(layout.bottomNav),
      applicationCube: cloneJson(layout.applicationCube),
      navigation: {
        bottomNavFixed: true,
        bottomNavOutsideScroll: true,
        showBottomNav: theme.showBottomNav !== false,
        items: cloneJson(layout.bottomNav)
      },
      grid: {
        rows: resolveHomeGridRows(theme),
        columns: resolveHomeGridColumns(theme),
        columnGap: resolveGridGap(theme),
        rowGap: resolveGridGap(theme),
        gap: resolveGridGap(theme),
        rowHeightDp: resolveHomeGridRowHeight(theme),
        minCardRowHeightDp: resolveHomeGridRowHeight(theme),
        sizeFormula: 'slotWidth',
        defaultFaceSec: resolveCardFaceSec(null, theme, {}),
        defaultCycleDurationMs: (theme.cardSpeed != null ? Number(theme.cardSpeed) : 16) * 1000,
        responsiveLayouts: homeGridLayouts(theme)
      },
      animation: {
        holdRatio: CUBE_HOLD_RATIO,
        transitionRatio: 1 - CUBE_HOLD_RATIO,
        globalFaceSec: resolveCardFaceSec(null, theme, {})
      }
    };
  }

  function resolveNonNegativeRadius(value, fallback) {
    if (value == null || value === '') return fallback;
    var n = Number(value);
    if (!isFinite(n)) return fallback;
    return n < 0 ? 0 : n;
  }

  function resolveTileBorderRadius(card, theme) {
    var fromCard = resolveNonNegativeRadius(card && card.borderRadius, null);
    if (fromCard != null) return fromCard;
    var fromTheme = resolveNonNegativeRadius(theme && theme.cardBorderRadius, null);
    if (fromTheme != null) return fromTheme;
    return 0;
  }

  function buildDesignTokens(theme) {
    theme = theme || {};
    var isLight = theme.bgStyle === 'light';
    var logout = resolveLogoutButtonColors(theme);
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
        logoutBackground: logout.background,
        logoutText: logout.text,
        border: isLight ? 'rgba(17,24,39,.08)' : 'rgba(255,255,255,.10)',
        online: '#22c55e',
        offline: '#94a3b8',
        danger: '#ef4444',
        warning: '#f59e0b'
      },
      spacing: { xxs: 2, xs: 4, sm: 8, md: 12, lg: 16, xl: 24, gridGap: resolveGridGap(theme) },
      typography: {
        brandTitle: { fontFamily: 'system', fontSize: 18, fontWeight: 800, lineHeight: 1.15, colorToken: 'colors.textPrimary', maxLines: 2, overflow: 'clip', softWrap: true },
        brandSubtitle: { fontFamily: 'system', fontSize: 11, fontWeight: 400, lineHeight: 1.25, colorToken: 'colors.textMuted', maxLines: 2, overflow: 'ellipsis' },
        welcomeGreeting: { fontFamily: 'system', fontSize: 15, fontWeight: 700, lineHeight: 1.2, colorToken: 'colors.welcomeText', maxLines: 2, overflow: 'clip', softWrap: true },
        welcomeEmail: { fontFamily: 'system', fontSize: 13, fontWeight: 600, lineHeight: 1.25, colorToken: 'colors.welcomeText', maxLines: 1, overflow: 'ellipsis' },
        welcomeCaption: { fontFamily: 'system', fontSize: 11, fontWeight: 500, lineHeight: 1.2, colorToken: 'colors.welcomeText', maxLines: 2, overflow: 'clip', softWrap: true },
        homeGreeting: { fontSize: 13, fontWeight: 600, lineHeight: 1.2, colorToken: 'colors.textMuted', maxLines: 1, overflow: 'ellipsis' },
        homeTitle: { fontSize: 26, fontWeight: 800, lineHeight: 1.08, letterSpacing: -0.03, colorToken: 'colors.textPrimary', maxLines: 2, overflow: 'ellipsis' },
        homeSubtitle: { fontSize: 12, fontWeight: 400, lineHeight: 1.3, colorToken: 'colors.textMuted', maxLines: 2, overflow: 'ellipsis' },
        pageTitle: { fontFamily: 'system', fontSize: 22, fontWeight: 700, lineHeight: 1.2, maxLines: 1, overflow: 'ellipsis' },
        pageSubtitle: { fontFamily: 'system', fontSize: 13, fontWeight: 400, lineHeight: 1.3, maxLines: 2, overflow: 'ellipsis' },
        cardTitle: { fontFamily: 'system', fontSize: 11, fontWeight: 800, lineHeight: 1.15, maxLines: 3, overflow: 'clip', softWrap: true },
        cardSubtitle: { fontFamily: 'system', fontSize: 8, fontWeight: 700, lineHeight: 1.15, maxLines: 2, overflow: 'clip', softWrap: true },
        statValue: { fontFamily: 'system', fontSize: 24, fontWeight: 900, lineHeight: 1, letterSpacing: -0.02 },
        statLabel: { fontFamily: 'system', fontSize: 9, fontWeight: 800, lineHeight: 1.2, maxLines: 2, overflow: 'ellipsis' },
        imageOverlayTitle: { fontFamily: 'system', fontSize: 13, fontWeight: 800, lineHeight: 1.1, maxLines: 1, overflow: 'clip' },
        imageOverlaySubtitle: { fontFamily: 'system', fontSize: 10, fontWeight: 700, lineHeight: 1.15, maxLines: 1, overflow: 'clip' },
        deviceTitle: { fontFamily: 'system', fontSize: 10, fontWeight: 600, lineHeight: 1.15, maxLines: 2, overflow: 'ellipsis' },
        deviceValue: { fontFamily: 'system', fontSize: 20, fontWeight: 700, lineHeight: 1 },
        byFaceType: DEFAULT_FACE_TYPOGRAPHY
      },
      radius: {
        small: 8,
        card: publishedCardRadius(theme.cardBorderRadius),
        device: 16,
        large: 24,
        pill: 999
      },
      shadows: {
        card: isLight
          ? { color: '#140f172a', blur: 40, spread: 0, offsetX: 0, offsetY: 18, css: '0 18px 40px rgba(15,23,42,.08)' }
          : { color: '#59000000', blur: 40, spread: 0, offsetX: 0, offsetY: 18, css: '0 18px 40px rgba(0,0,0,.35)' }
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
          tileRotationModel: 'fourFaceBoxRotation',
          transformStyle: 'preserve-3d',
          faceSwapAtFlipProgress: null
        };
      })()
    };
  }

  function faceType(cardType, face, fi, card) {
    return normalizeFaceContentType(face, Object.assign({}, card || {}, { type: cardType || (card && card.type) }));
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
    appearance.iconPosition = 'center';
    appearance.titlePosition = 'bottomLeft';
    appearance.style = appearance.style || 'startTile';
    return appearance;
  }

  function resolveFaceImageFitSettings(face, card) {
    face = face || {};
    card = card || {};
    var fit = String(face.imageFit || card.imageFit || 'contain').toLowerCase();
    if (fit !== 'cover' && fit !== 'fill') fit = 'contain';
    var pos = String(face.imagePosition || card.imagePosition || 'center').toLowerCase();
    if (['center', 'top', 'bottom', 'left', 'right'].indexOf(pos) === -1) pos = 'center';
    var scale = parseInt(face.imageScale != null ? face.imageScale : card.imageScale, 10);
    if (isNaN(scale)) scale = 100;
    scale = Math.max(50, Math.min(200, scale));
    return { fit: fit, position: pos, scale: scale };
  }

  function isSelfLabeledBanner(url) {
    var u = String(url || '').toLowerCase();
    return u.indexOf('hrms-human-resource-management-system') !== -1 || u.indexOf('2283105665') !== -1;
  }

  function buildHomeFace(face, card, fi, usedIcons, theme, cfg) {
    var cardType = card.type || 'link';
    var type = faceType(cardType, face, fi, card);
    var iconToken = faToToken(face.icon || card.icon);
    usedIcons[iconToken] = true;
    var appearance = buildFaceAppearance(face, card, type, theme);
    var faceRoute = String(face.route || (fi === 0 ? card.pageRoute : '') || '').trim();
    var out = {
      id: (card.id || 'card') + '-face-' + fi,
      type: type,
      faceIndex: fi,
      geometryFace: FACE_GEOMETRY[fi] || ('face-' + fi),
      iconToken: iconToken,
      showIcon: face.hideIcon !== true,
      appearance: appearance,
      typography: resolveFaceTypography(face, card, type, theme),
      contentLayout: buildFaceContentLayout(type)
    };
    if (faceRoute) {
      out.route = faceRoute;
      out.openIn = isGridHomeCard(card, face, fi)
        ? 'inApp'
        : (/^https?:\/\//i.test(faceRoute) ? 'inAppWebView' : 'inApp');
    }

    if (type === 'stack' || type === 'stat' || type === 'link' || type === 'action') {
      out.type = 'stack';
      out.title = face.title || face.label || card.title || card.label || '';
      var stackValue = face.value != null ? face.value : (type === 'stat' || face.value != null ? card.value : undefined);
      if (stackValue != null && stackValue !== '') {
        out.value = bind('dashboard.cards.' + card.id + '.faces.' + fi + '.value', stackValue);
      }
      if (face.subtitle != null || card.subtitle) {
        out.subtitle = face.subtitle != null ? staticVal(face.subtitle) : staticVal(card.subtitle);
      }
      if (face.badge != null) out.badge = face.badge;
      if (face.status != null) out.status = face.status;
    } else if (type === 'image') {
      var imgSrc = face.image || (fi === 0 ? card.image : '') || '';
      if (imgSrc) {
        var imageDisplay = resolveFaceImageFitSettings(face, card);
        var overlayTitle = String(face.title || face.label || '').trim();
        if (overlayTitle && ['new block', 'face 2', 'face 3', 'face 4'].indexOf(overlayTitle.toLowerCase()) >= 0) {
          overlayTitle = '';
        }
        var overlaySub = String(face.subtitle || '').trim();
        if (!overlaySub && fi === 0) overlaySub = String(card.subtitle || '').trim();
        out.title = overlayTitle;
        if (overlaySub) out.subtitle = staticVal(overlaySub);
        out.overlayTypography = {
          title: {
            fontSize: appearance.titleFontSize || 13,
            fontWeight: appearance.titleFontWeight || 800,
            lineHeight: appearance.titleLineHeight || 1.1,
            maxLines: 1,
            overflow: 'clip',
            textAlign: 'center'
          },
          subtitle: {
            fontSize: appearance.subtitleFontSize || 10,
            fontWeight: appearance.subtitleFontWeight || 700,
            lineHeight: appearance.subtitleLineHeight || 1.15,
            maxLines: 1,
            overflow: 'clip',
            textAlign: 'center'
          }
        };
        out.image = resolveMediaUrl(imgSrc, cfg || {});
        out.assetId = 'asset-' + card.id + '-face-' + fi;
        out.imageFit = imageDisplay.fit;
        out.imagePosition = imageDisplay.position;
        out.imageScale = imageDisplay.scale;
        out.imageAlignment = imageDisplay.position;
        out.appearance = Object.assign({}, out.appearance || {}, {
          imageFit: imageDisplay.fit,
          imagePosition: imageDisplay.position,
          imageScale: imageDisplay.scale,
          objectFit: imageDisplay.fit,
          objectPosition: imageDisplay.position
        });
        if (isSelfLabeledBanner(imgSrc)) {
          out.title = '';
          delete out.subtitle;
          out.showIcon = false;
          out.imageFit = 'contain';
          out.imageScale = 100;
          out.appearance = Object.assign({}, out.appearance, {
            backgroundColor: '#ffffff',
            hideIcon: true,
            showIcon: false,
            imageFit: 'contain',
            objectFit: 'contain',
            imageScale: 100
          });
        }
      } else {
        out.type = 'stack';
        out.title = face.title || face.label || card.title || card.label || '';
        out.subtitle = face.subtitle != null ? staticVal(face.subtitle) : staticVal(card.subtitle);
      }
    } else if (type === 'mixed') {
      out.type = 'mixed';
      var mixedImg = face.image || (fi === 0 ? card.image : '') || '';
      var mixedFit = resolveFaceImageFitSettings(face, card);
      out.mixedLayout = face.mixedLayout || card.mixedLayout || 'startTile';
      out.title = face.title || face.label || card.title || card.label || '';
      if (face.subtitle != null || card.subtitle) {
        out.subtitle = face.subtitle != null ? staticVal(face.subtitle) : staticVal(card.subtitle);
      }
      if (face.value != null && face.value !== '') {
        out.value = bind('dashboard.cards.' + card.id + '.faces.' + fi + '.value', face.value);
      }
      if (mixedImg) {
        out.image = resolveMediaUrl(mixedImg, cfg || {});
        out.assetId = 'asset-' + card.id + '-face-' + fi;
        out.imageFit = mixedFit.fit;
        out.imagePosition = mixedFit.position;
        out.imageScale = mixedFit.scale;
        out.appearance = Object.assign({}, out.appearance || {}, {
          imageFit: mixedFit.fit,
          imagePosition: mixedFit.position,
          imageScale: mixedFit.scale,
          objectFit: mixedFit.fit,
          objectPosition: mixedFit.position
        });
      }
    } else {
      out.type = 'stack';
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
        sizeFormula: 'slotWidth'
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
    card.colSpan = Math.max(1, Math.min(12, parseInt(card.colSpan, 10) || 1));
    card.rowSpan = Math.max(1, Math.min(16, parseInt(card.rowSpan, 10) || 1));
  }

  function resolvePublicAvatarUrl(url) {
    var src = String(url || '').trim();
    if (!src || /^(data:|blob:)/i.test(src)) return '';
    if (/\/assets\/images\/avatar\.jpg(\?|$)/i.test(src)) return '';
    if (/^https?:\/\//i.test(src)) {
      if (/^http:\/\//i.test(src) && !/localhost|127\.0\.0\.1|::1/i.test(src)) {
        src = 'https://' + src.slice(7);
      }
      return src;
    }
    if (src.charAt(0) !== '/') {
      src = src.indexOf('/') === -1 ? ('/files/profile_images/' + src) : ('/' + src);
    }
    return 'https://efactor.tech' + src;
  }

  function getProfileAvatarUrl(profile, cfg) {
    profile = profile || {};
    cfg = cfg || {};
    var fromProfile = resolvePublicAvatarUrl(profile.avatarUrl);
    var fromLive = resolvePublicAvatarUrl(cfg.loginUserProfile && cfg.loginUserProfile.avatarUrl);
    return fromLive || fromProfile;
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

  function isGridHomeCard(card, face, fi) {
    if (!card) return false;
    if (String(card.id || '').toLowerCase() === 'grid') return true;
    var route = String((face && face.route) || ((fi == null || fi === 0) ? (card.pageRoute || '') : '') || '').trim();
    if (!route) return false;
    var path = route.replace(/^https?:\/\/[^/?#]+/i, '').replace(/^\//, '').toLowerCase();
    path = path.replace(/[?#].*$/, '');
    return path === 'grid' || route.toLowerCase() === 'grid' || route.toLowerCase() === '/grid';
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

    if (isGridHomeCard(card, face, fi)) return 'open-grid-module';

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
      if (['home', 'profile', 'alerts', 'reports', 'add-device', 'grid', 'category', 'rooms'].indexOf(normalized) >= 0) {
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

  var NAV_CHROME_BOTTOM = 120;
  var TAB_ROUTE_IDS = ['home', 'devices', 'grid', 'profile', 'category', 'alerts', 'reports', 'rooms'];

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
      position: 'fixed',
      overlayContent: false,
      persistAcrossRoutes: true,
      iconSize: 16,
      labelFontSize: 9,
      labelFontWeight: 700,
      shadowToken: 'shadows.card'
    };
  }

  function withNavSafeScrollLayout(layout) {
    layout = layout || {};
    var padding = layout.padding;
    if (typeof padding === 'number') {
      padding = {
        left: padding,
        right: padding,
        top: padding,
        bottom: Math.max(padding, NAV_CHROME_BOTTOM)
      };
    } else if (padding && typeof padding === 'object') {
      padding = Object.assign({}, padding, {
        bottom: Math.max(Number(padding.bottom) || 0, NAV_CHROME_BOTTOM)
      });
    } else {
      padding = { left: 10, right: 10, top: 8, bottom: NAV_CHROME_BOTTOM };
    }
    return Object.assign({}, layout, {
      padding: padding,
      reservedBottomInset: NAV_CHROME_BOTTOM,
      contentAvoidsBottomNavigation: true,
      overflowX: 'hidden',
      overflowY: 'auto',
      clipHorizontalOverflow: true
    });
  }

  function attachPersistentShell(page) {
    if (!page || typeof page !== 'object') return page;
    return Object.assign({}, page, {
      usesAppShell: true,
      keepBottomNavigation: true,
      bottomNavigationVisible: true,
      hideBottomNavigation: false,
      bottomNavigationComponentId: 'main-bottom-navigation',
      chrome: 'appShell'
    });
  }

  function decorateTabPages(pages) {
    if (!pages || typeof pages !== 'object') return pages;
    Object.keys(pages).forEach(function (id) {
      if (id === 'login') return;
      pages[id] = attachPersistentShell(pages[id]);
    });
    return pages;
  }

  function buildPersistentAppShell(theme) {
    theme = theme || {};
    return {
      type: 'appShell',
      persistent: true,
      persistsAcrossRoutes: true,
      persistOnRouteChange: true,
      replaceOnNestedSchema: false,
      bodySlot: 'active-page',
      bottomNavigationComponentId: theme.showBottomNav !== false ? 'main-bottom-navigation' : null,
      floatingActionComponentId: null,
      bottomNavigationOutsidePageScroll: true,
      bottomNavigationFixed: true,
      bottomNavigationPosition: 'fixed',
      bottomNavigationOverlay: false,
      contentAvoidsBottomNavigation: true,
      reservedBottomInset: NAV_CHROME_BOTTOM,
      visibleOnRouteIds: TAB_ROUTE_IDS.slice(),
      hideOnRouteIds: ['login']
    };
  }

  function isUnwantedHomeHeaderText(value) {
    var text = String(value || '').trim();
    if (!text) return false;
    if (/quick\s*mart/i.test(text)) return true;
    return /\b\d+\s*minutes?\b/i.test(text) && /nearby/i.test(text);
  }

  function resolveHomeHeaderTitle(layout) {
    var header = (layout && layout.header) || {};
    var title = String(header.title || '').trim();
    // Same fallback for every template/account — never emit empty required title.
    if (isUnwantedHomeHeaderText(title)) title = '';
    return title || 'eFactor · IoE';
  }

  function resolveHomeHeaderSubtitle(layout) {
    var header = (layout && layout.header) || {};
    var subtitle = String(header.subtitle || '').trim();
    if (isUnwantedHomeHeaderText(subtitle)) subtitle = '';
    return subtitle || 'Smart insights & tools';
  }

  function buildHomeHeaderTextBlock(text, i18nKey, typographyToken, typography, overflow) {
    overflow = overflow || RESPONSIVE_TEXT_OVERFLOW.subtitle;
    return {
      text: text,
      i18nKey: i18nKey,
      '$i18n': i18nKey,
      typographyToken: typographyToken,
      typography: Object.assign({}, typography || {}, overflow),
      maxLines: overflow.maxLines,
      overflow: overflow.overflow,
      softWrap: overflow.softWrap !== false
    };
  }

  function buildHomeHeaderGreetingBlock(greetingName) {
    return buildHomeHeaderTextBlock(
      formatHomeGreeting(greetingName),
      'home.greeting',
      'typography.homeGreeting',
      {
        fontSize: 13,
        fontWeight: 600,
        lineHeight: 1.2,
        colorToken: 'colors.textMuted'
      },
      RESPONSIVE_TEXT_OVERFLOW.greeting
    );
  }

  function mergeLayoutProfile(layout, cfg) {
    var page = (layout && layout.profilePage) || {};
    var live = (cfg && cfg.loginUserProfile) || {};
    var merged = Object.assign({}, page, live);
    merged.avatarUrl = resolvePublicAvatarUrl(live.avatarUrl) || resolvePublicAvatarUrl(page.avatarUrl);
    return merged;
  }

  function buildHomeHeaderComponent(layout, cfg, theme, usedIcons) {
    usedIcons = usedIcons || {};
    theme = theme || {};
    layout = layout || {};
    cfg = cfg || {};
    var titleText = resolveHomeHeaderTitle(layout);
    var subtitleText = resolveHomeHeaderSubtitle(layout);
    var titleColor = theme.textPrimary || (theme.bgStyle === 'light' ? '#0f172a' : '#f8fafc');
    var subtitleColor = theme.textMuted || (theme.bgStyle === 'light' ? '#64748b' : '#94a3b8');
    var logoUrl = (cfg && cfg.logoUrl) || 'https://efactor.tech/files/home/Logo_new.png';
    var profile = mergeLayoutProfile(layout, cfg);
    var greetingName = getProfileGreetingName(profile, cfg);
    var avatarUrl = getProfileAvatarUrl(profile, cfg);
    var header = {
      id: 'home-header',
      type: 'homeHeader',
      visible: true,
      overflowPolicy: 'ellipsis',
      logo: {
        imageUrl: logoUrl,
        width: 96,
        height: 28,
        maxWidth: 96,
        maxHeight: 28,
        borderRadius: 0,
        objectFit: 'contain',
        objectPosition: 'left center',
        backgroundColor: 'transparent'
      },
      title: buildHomeHeaderTextBlock(
        titleText,
        'home.brandTitle',
        'typography.brandTitle',
        {
          fontSize: 18,
          fontWeight: 800,
          lineHeight: 1.15,
          colorToken: 'colors.textPrimary',
          color: titleColor
        },
        RESPONSIVE_TEXT_OVERFLOW.title
      ),
      subtitle: buildHomeHeaderTextBlock(
        subtitleText,
        'home.brandSubtitle',
        'typography.brandSubtitle',
        {
          fontSize: 11,
          fontWeight: 400,
          lineHeight: 1.25,
          colorToken: 'colors.textMuted',
          color: subtitleColor
        },
        RESPONSIVE_TEXT_OVERFLOW.subtitle
      ),
      layout: {
        direction: 'row',
        align: 'center',
        justify: 'space-between',
        gap: 10,
        marginBottom: 12
      },
      textColumn: {
        flex: 1,
        direction: 'column',
        align: 'flex-start',
        gap: 2,
        minWidth: 0,
        shrink: 1,
        overflow: 'hidden'
      },
      appearance: {
        titleFontSize: 18,
        titleFontWeight: 800,
        titleLineHeight: 1.15,
        titleColor: titleColor,
        subtitleFontSize: 11,
        subtitleFontWeight: 400,
        subtitleLineHeight: 1.25,
        subtitleColor: subtitleColor,
        logoWidth: 96,
        logoHeight: 28,
        layoutGap: 10,
        marginBottom: 12,
        backgroundColor: 'transparent'
      }
    };
    // Market hides the welcome banner. Keep the same header row the app already
    // knows (greeting + avatar are optional homeHeader props) so the top bar still renders.
    if (theme.showWelcome === false) {
      usedIcons.profile = true;
      header.greeting = buildHomeHeaderGreetingBlock(greetingName);
      if (header.greeting && header.greeting.typography) {
        header.greeting.typography.color = subtitleColor;
      }
      header.avatar = {
        initials: getProfileInitials(profile, cfg),
        initialsBinding: {
          '$bind': 'session.user.initials',
          fallback: getProfileInitials(profile, cfg)
        },
        imageUrl: avatarUrl,
        imageUrlBinding: avatarUrl ? {
          '$bind': 'session.user.avatarUrl',
          fallback: avatarUrl
        } : undefined,
        appearance: {
          width: 46,
          height: 46,
          borderRadius: 999,
          gradientStart: theme.welcomeStart || '#22c55e',
          gradientEnd: theme.welcomeEnd || '#0ea5e9',
          fontSize: 15,
          fontWeight: 800,
          textColor: '#ffffff'
        },
        events: { tap: { actionId: 'navigate-profile' } },
        accessibilityLabel: 'Open profile'
      };
    }
    return header;
  }

  function buildProfileAppearanceContract(theme) {
    theme = theme || {};
    var isLight = theme.bgStyle === 'light';
    var logout = resolveLogoutButtonColors(theme);
    return {
      pagePadding: 16,
      pageGap: 12,
      cardPadding: 20,
      cardBorderRadius: 18,
      cardShadowToken: 'shadows.card',
      cardBorderColor: isLight ? 'rgba(17,24,39,.06)' : 'rgba(255,255,255,.10)',
      avatarSize: 64,
      avatarFontSize: 22,
      avatarFontWeight: 800,
      avatarGradientStart: theme.welcomeStart || (isLight ? '#fbbf24' : '#7c3aed'),
      avatarGradientEnd: theme.welcomeEnd || (isLight ? '#f97316' : '#4f46e5'),
      avatarTextColor: theme.welcomeTextColor || '#ffffff',
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
      rowBorderColor: isLight ? 'rgba(17,24,39,.06)' : 'rgba(255,255,255,.10)',
      rowBackgroundColorToken: 'colors.surface',
      rowTitleColorToken: 'colors.textPrimary',
      rowSubtitleColorToken: 'colors.textMuted',
      rowIconBackgroundColorToken: 'colors.accent',
      rowIconColorToken: 'colors.accent',
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
      logoutFontWeight: 800,
      logoutBackgroundColor: logout.background,
      logoutTextColor: logout.text,
      logoutIconColor: logout.text,
      pageBackgroundColorToken: 'colors.pageBackground',
      cardBackgroundColorToken: 'colors.surface'
    };
  }

  /**
   * Full profile screen (header + menu rows + logout). Never publish the
   * pageHeader-only stub (profile-page-header) — Flutter needs these children.
   */
  function applyProfilePageComponents(components, layout, cfg, theme, usedIcons) {
    components = components || {};
    layout = layout || {};
    cfg = cfg || {};
    theme = theme || {};
    usedIcons = usedIcons || {};
    var profile = mergeLayoutProfile(layout, cfg);
    var profileAppearance = buildProfileAppearanceContract(theme);
    var profileInitials = getProfileInitials(profile, cfg);
    var profileAvatarUrl = getProfileAvatarUrl(profile, cfg);
    var notifOn = profile.notificationsEnabled !== false;
    var themeModeFallback = (theme.bgStyle === 'dark') ? 'dark' : 'light';
    var displayName = profile.name || 'Profile';
    var displayEmail = profile.email || (layout.welcome && layout.welcome.email) || cfg.userEmail || '';
    if (!displayEmail) displayEmail = 'user@example.com';

    usedIcons.profile = true;
    usedIcons.alerts = true;
    usedIcons.appearance = true;
    usedIcons.shield = true;
    usedIcons.logout = true;
    usedIcons['chevron-right'] = true;

    delete components['profile-page-header'];

    var profilePad = typeof profileAppearance.pagePadding === 'number' ? profileAppearance.pagePadding : 16;
    var profileBottomPad = Math.max(profilePad + 24, NAV_CHROME_BOTTOM + 24);
    var phoneValue = String(profile.phone || '').trim();
    var memberIdValue = String(profile.memberId || '').trim();

    components['profile-page'] = {
      id: 'profile-page',
      type: 'scrollPage',
      layout: withNavSafeScrollLayout({
        safeArea: true,
        padding: {
          left: profilePad,
          right: profilePad,
          top: profilePad,
          bottom: profileBottomPad
        },
        gap: profileAppearance.pageGap,
        overflowX: 'hidden',
        overflowY: 'auto',
        footerChildId: 'profile-logout-button',
        footerSticky: false,
        contentAvoidsBottomNavigation: true,
        reservedBottomInset: profileBottomPad,
        minHeight: '100%'
      }),
      children: ['profile-header', 'profile-menu', 'profile-logout-button']
    };

    // Studio identity layout: avatar → memberId (eyebrow) → name (title) → email → phone chip.
    // Keep legacy name/email/phone/memberId keys; add roles so Flutter does not promote phone to title.
    components['profile-header'] = {
      id: 'profile-header',
      type: 'profileHeader',
      name: displayName,
      title: displayName,
      fullName: displayName,
      displayName: displayName,
      email: displayEmail,
      phone: phoneValue,
      memberId: memberIdValue,
      eyebrow: memberIdValue,
      meta: memberIdValue,
      chip: phoneValue,
      phoneChip: phoneValue,
      avatarText: profileInitials,
      avatarUrl: profileAvatarUrl,
      imageUrl: profileAvatarUrl,
      nameBinding: { '$state': 'session.user.displayName', fallback: displayName },
      titleBinding: { '$state': 'session.user.displayName', fallback: displayName },
      emailBinding: { '$state': 'session.user.email', fallback: displayEmail },
      phoneBinding: { '$state': 'session.user.phone', fallback: phoneValue },
      memberIdBinding: { '$state': 'session.user.memberId', fallback: memberIdValue },
      eyebrowBinding: { '$state': 'session.user.memberId', fallback: memberIdValue },
      avatarUrlBinding: { '$bind': 'session.user.avatarUrl', fallback: profileAvatarUrl || '' },
      imageUrlBinding: { '$bind': 'session.user.avatarUrl', fallback: profileAvatarUrl || '' },
      avatarTextBinding: { '$state': 'session.user.initials', fallback: profileInitials },
      avatar: {
        initials: profileInitials,
        imageUrl: profileAvatarUrl,
        imageUrlBinding: { '$bind': 'session.user.avatarUrl', fallback: profileAvatarUrl || '' },
        appearance: {
          width: profileAppearance.avatarSize || 64,
          height: profileAppearance.avatarSize || 64,
          borderRadius: 999,
          objectFit: 'cover',
          fontSize: profileAppearance.avatarFontSize || 22,
          fontWeight: profileAppearance.avatarFontWeight || 800,
          textColor: '#ffffff'
        }
      },
      fields: {
        memberId: { role: 'eyebrow', value: memberIdValue, binding: { '$state': 'session.user.memberId', fallback: memberIdValue } },
        name: { role: 'title', value: displayName, binding: { '$state': 'session.user.displayName', fallback: displayName } },
        email: { role: 'subtitle', value: displayEmail, binding: { '$state': 'session.user.email', fallback: displayEmail } },
        phone: { role: 'chip', value: phoneValue, binding: { '$state': 'session.user.phone', fallback: phoneValue } }
      },
      layout: {
        variant: 'studioIdentity',
        order: ['avatar', 'memberId', 'name', 'email', 'phone'],
        roles: {
          avatar: 'avatar',
          memberId: 'eyebrow',
          name: 'title',
          email: 'subtitle',
          phone: 'chip'
        },
        showMemberIdAsEyebrow: true,
        showPhoneAsChip: true,
        neverPromotePhoneToTitle: true,
        hideNameFallbackToPhone: true
      },
      appearance: Object.assign({}, profileAppearance, {
        titleField: 'name',
        eyebrowField: 'memberId',
        subtitleField: 'email',
        chipField: 'phone',
        chipFields: ['phone'],
        fieldOrder: ['avatar', 'memberId', 'name', 'email', 'phone'],
        layoutVariant: 'studioIdentity',
        neverPromotePhoneToTitle: true
      })
    };

    components['profile-menu'] = {
      id: 'profile-menu',
      type: 'profileMenu',
      appearance: profileAppearance,
      items: [
        {
          id: 'edit-profile',
          iconToken: 'profile',
          label: { '$i18n': 'profile.edit' },
          subtitle: { '$i18n': 'profile.editSubtitle' },
          trailingIconToken: 'chevron-right',
          actionId: 'profile-edit'
        },
        {
          id: 'notifications',
          iconToken: 'alerts',
          label: { '$i18n': 'profile.notifications' },
          subtitle: notifOn
            ? { '$i18n': 'profile.notificationsSubtitle' }
            : 'Alerts and reminders disabled',
          trailingIconToken: 'chevron-right',
          actionId: 'profile-notifications'
        },
        {
          id: 'appearance',
          iconToken: 'appearance',
          label: { '$i18n': 'profile.appearance' },
          subtitle: (themeModeFallback === 'dark' ? 'Dark' : 'Light') + ' mode',
          subtitleBinding: { '$state': 'global.themeMode' },
          subtitleFormat: '{value} mode',
          subtitleTransform: 'capitalizeFirst',
          trailingIconToken: 'chevron-right',
          actionId: 'profile-appearance'
        },
        {
          id: 'privacy-policy',
          iconToken: 'shield',
          label: { '$i18n': 'profile.privacy' },
          subtitle: { '$i18n': 'profile.privacySubtitle' },
          trailingIconToken: 'chevron-right',
          actionId: 'profile-privacy'
        }
      ]
    };

    components['profile-logout-button'] = {
      id: 'profile-logout-button',
      type: 'actionButton',
      label: { '$i18n': 'profile.logout' },
      iconToken: 'logout',
      actionId: 'logout',
      layout: {
        width: 'fill',
        alignSelf: 'stretch',
        flexShrink: 0,
        marginTop: 8,
        marginBottom: 8
      },
      appearance: Object.assign({}, profileAppearance, {
        variant: 'primary',
        destructive: false,
        width: '100%',
        minHeight: profileAppearance.logoutMinHeight,
        borderRadius: profileAppearance.logoutBorderRadius,
        backgroundColor: profileAppearance.logoutBackgroundColor,
        textColor: profileAppearance.logoutTextColor,
        iconColor: profileAppearance.logoutIconColor,
        stickyBottom: false
      })
    };

    return components;
  }

  function resolveLogoutButtonColors(theme) {
    theme = theme || {};
    var isLight = theme.bgStyle === 'light';
    return {
      background: isLight
        ? (theme.textPrimary || '#111827')
        : (theme.accentColor || theme.navActiveColor || '#3b82f6'),
      text: '#ffffff'
    };
  }

  function ensureSchemaIconTokens(usedIcons) {
    ['language', 'shield', 'logout', 'appearance', 'chevron-right', 'profile', 'home', 'devices', 'category', 'alerts', 'reports', 'rooms'].forEach(function (token) {
      usedIcons[token] = true;
    });
  }

  function cardTapActionId(card) {
    if (card.type === 'add') return 'navigate-add-device';
    if (isGridHomeCard(card)) return 'open-grid-module';
    if (card.pageRoute) {
      var raw = String(card.pageRoute).trim();
      if (/^https?:\/\//i.test(raw)) {
        return 'external-nav-card-' + safeRoutePart(card.id, 'card');
      }
      var pr = raw.toLowerCase().replace(/^\//, '');
      if (pr === 'devices' || pr === 'iot') return 'open-iot-app';
      if (['home', 'profile', 'alerts', 'reports', 'add-device', 'grid', 'category', 'rooms'].indexOf(pr) >= 0) {
        return 'navigate-' + (pr === 'iot' ? 'devices' : pr);
      }
      var r = raw.replace(/^\//, '').replace(/\//g, '-');
      return 'navigate-card-' + (card.id || r);
    }
    if (isIotHomeCard(card, null, 0)) return 'open-iot-app';
    if (card.id === 'grid') return 'open-grid-module';
    if (card.id === 'hrms') return 'open-hrms-module';
    return null;
  }

  /**
   * In-app routeId for a face. HTTP(S) faces omit routeId so the host uses
   * face.route + openIn / external-navigate instead of the stub face page.
   */
  function resolveFaceRouteId(card, face, fi) {
    var route = String((face && face.route) || (fi === 0 ? (card && card.pageRoute) : '') || '').trim();
    if (!route) return cardFaceRouteId(card, fi);
    if (/^https?:\/\//i.test(route)) return null;
    var normalized = route.replace(/^\//, '').toLowerCase();
    if (normalized === 'iot') return 'devices';
    if (['home', 'devices', 'profile', 'alerts', 'reports', 'add-device', 'grid', 'category', 'rooms'].indexOf(normalized) >= 0) {
      return normalized;
    }
    return normalized.replace(/[^a-z0-9]+/gi, '-').toLowerCase() || cardFaceRouteId(card, fi);
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

  function getDefaultGridPageContent() {
    return {
      id: 'gridPage',
      routeId: 'grid',
      header: {
        title: 'Grid',
        subtitle: 'Your professional network space',
        searchIconToken: 'scanner',
        avatarInitial: 'S',
        avatarColor: '#7c3aed'
      },
      featureCards: [
        {
          id: 'professional-space',
          title: 'Professional Space',
          subtitle: 'Discover people, opportunities and grow your career.',
          iconToken: 'user',
          color: '#5b6cff',
          gradientStart: '#dbeafe',
          gradientEnd: '#ede9fe',
          textColor: '#312e81',
          actionId: 'grid-open-professional-space'
        },
        {
          id: 'circle-connect',
          title: 'Circle Connect',
          subtitle: 'Meaningful conversations. Stronger circles.',
          iconToken: 'users',
          color: '#f97316',
          gradientStart: '#ffedd5',
          gradientEnd: '#fff7ed',
          textColor: '#9a3412',
          actionId: 'grid-open-circle-connect'
        }
      ],
      stats: [
        { id: 'professionals', label: 'Professionals', value: 248 },
        { id: 'opportunities', label: 'Opportunities', value: 36 },
        { id: 'active-circles', label: 'Active Circles', value: 12 }
      ],
      exploring: {
        title: 'Continue Exploring',
        seeAllLabel: 'See all',
        seeAllActionId: 'grid-see-all-exploring',
        items: [
          {
            id: 'find-talent',
            title: 'Find Talent',
            subtitle: 'People with skills you need',
            iconToken: 'user',
            color: '#8b5cf6',
            actionId: 'grid-open-find-talent'
          },
          {
            id: 'join-circles',
            title: 'Join Circles',
            subtitle: "Communities you'll love",
            iconToken: 'users',
            color: '#14b8a6',
            actionId: 'grid-open-join-circles'
          }
        ]
      },
      quote: {
        text: 'Connections turn potential into progress.',
        iconToken: 'quote'
      }
    };
  }

  function resolveGridPageContent(layout) {
    var defaults = getDefaultGridPageContent();
    var page = (layout && layout.gridPage && typeof layout.gridPage === 'object') ? layout.gridPage : {};
    return {
      id: page.id || defaults.id,
      routeId: page.routeId || defaults.routeId,
      header: Object.assign({}, defaults.header, page.header || {}),
      featureCards: Array.isArray(page.featureCards) && page.featureCards.length ? page.featureCards : defaults.featureCards,
      stats: Array.isArray(page.stats) && page.stats.length ? page.stats : defaults.stats,
      exploring: Object.assign({}, defaults.exploring, page.exploring || {}, {
        items: (page.exploring && Array.isArray(page.exploring.items) && page.exploring.items.length)
          ? page.exploring.items
          : defaults.exploring.items
      }),
      quote: Object.assign({}, defaults.quote, page.quote || {})
    };
  }

  function resolveCircleConnectUrl(cfg) {
    cfg = cfg || {};
    var url = String(cfg.circleConnectUrl || '').trim();
    if (!url || !/grid[-_]chat/i.test(url)) {
      url = 'https://efactor.tech/index.php/grid-chat';
    }
    return url;
  }

  function gridOpenUrlAction(url) {
    return {
      type: 'open-url',
      url: url,
      route: url,
      title: '',
      openIn: 'inAppWebView',
      webView: {
        javaScriptEnabled: true,
        sharedCookies: true,
        clearCache: false,
        showAppBar: false,
        showToolbar: false,
        hideAppBar: true,
        displayMode: 'fullscreen'
      },
      destination: {
        url: url,
        openIn: 'inAppWebView',
        title: '',
        sharedCookies: true,
        showAppBar: false,
        showToolbar: false,
        hideAppBar: true
      }
    };
  }

  function buildGridPageActions(cfg) {
    var circleUrl = resolveCircleConnectUrl(cfg);
    return {
      'navigate-grid': { type: 'navigate', destination: { routeId: 'grid' } },
      'refresh-grid': {
        type: 'sequence',
        actions: [
          { type: 'set-state', path: 'refreshing', value: true },
          { type: 'refresh-data-source', dataSourceId: 'grid' },
          { type: 'set-state', path: 'refreshing', value: false }
        ],
        errorPresentation: { type: 'snackbar', message: 'Unable to refresh Grid' }
      },
      'grid-open-scanner': {
        type: 'open-url',
        url: 'https://efactor.tech/v8/grid_scan.php',
        route: 'https://efactor.tech/v8/grid_scan.php',
        title: '',
        openIn: 'inAppWebView'
      },
      'grid-open-professional-space': { type: 'host-action', actionId: 'open-grid-professional-space' },
      'grid-open-circle-connect': {
        type: 'open-url',
        url: circleUrl,
        route: circleUrl,
        title: 'Circle Connect',
        openIn: 'inAppWebView',
        webView: { javaScriptEnabled: true, sharedCookies: true, clearCache: false },
        destination: {
          url: circleUrl,
          openIn: 'inAppWebView',
          title: 'Circle Connect',
          sharedCookies: true
        }
      },
      'grid-open-find-talent': gridOpenUrlAction('https://efactor.tech/index.php/grid/network'),
      'grid-open-join-circles': gridOpenUrlAction('https://efactor.tech/index.php/grid-chat'),
      'grid-see-all-exploring': gridOpenUrlAction('https://efactor.tech/index.php/grid/network')
    };
  }

  function resolveGridApiUrl(cfg) {
    cfg = cfg || {};
    if (cfg.gridApiUrl) return String(cfg.gridApiUrl).replace(/\/+$/, '');
    var schemaUrl = String(cfg.schemaApiUrl || cfg.apiUrl || '').replace(/\/+$/, '');
    if (schemaUrl) {
      // Prefer sibling grid_api_creation.php next to api_creation.php / v8 host.
      var gridBase = schemaUrl
        .replace(/api_creation\.php.*/i, 'grid_api_creation.php')
        .replace(/[?&].*$/, '');
      if (/grid_api_creation\.php/i.test(gridBase)) {
        return gridBase + '?action=get_grid';
      }
    }
    return 'https://efactor.tech/v8/grid_api_creation.php?action=get_grid';
  }

  function buildGridDataSource(cfg, layout) {
    var endpoint = resolveGridApiUrl(cfg);
    var userId = (cfg && cfg.userId) || (layout && layout.userId) || null;
    return {
      type: 'rest',
      endpoint: endpoint,
      method: 'GET',
      queryParameters: userId ? { user_id: userId, schema_rev: 'scanner-qr-7' } : { schema_rev: 'scanner-qr-7' },
      headers: { Accept: 'application/json' },
      responseMapping: {
        schema: '$.schema',
        page: '$.schema.page',
        components: '$.schema.components',
        actions: '$.schema.actions',
        iconRegistry: '$.schema.iconRegistry',
        content: '$.schema.content',
        stats: '$.schema.content.stats'
      },
      cachePolicy: 'network-first',
      cacheTtlSec: 60,
      timeoutMs: 30000,
      published: true,
      errorPolicy: {
        useCachedData: true,
        componentId: 'grid-error',
        clearHostSession: false,
        navigateToLogin: false
      }
    };
  }

  function applyGridNetworkPageComponents(components, layout, usedIcons, cfg, theme) {
    var page = resolveGridPageContent(layout);
    var header = page.header || {};
    var exploring = page.exploring || {};
    var quote = page.quote || {};
    var featureCards = page.featureCards || [];
    var exploreItems = exploring.items || [];

    usedIcons.grid = true;
    usedIcons.scanner = true;
    usedIcons.user = true;
    usedIcons.users = true;
    usedIcons.quote = true;
    usedIcons['chevron-right'] = true;

    var featureChildIds = featureCards.map(function (card, index) {
      var id = 'grid-feature-' + (card.id || index);
      usedIcons[card.iconToken || 'user'] = true;
      components[id] = {
        id: id,
        type: 'featureCard',
        title: card.title || '',
        subtitle: card.subtitle || '',
        iconToken: card.iconToken || 'user',
        color: card.color || '#5b6cff',
        gradientStart: card.gradientStart || '#dbeafe',
        gradientEnd: card.gradientEnd || '#ede9fe',
        textColor: card.textColor || '#312e81',
        actionId: card.actionId || null,
        events: card.actionId ? { tap: { actionId: card.actionId } } : undefined,
        layout: { columnSpan: 1, rowSpan: 1 },
        appearance: {
          borderRadius: 22,
          backgroundGradient: {
            start: card.gradientStart || '#dbeafe',
            end: card.gradientEnd || '#ede9fe'
          }
        }
      };
      return id;
    });

    var exploreChildIds = exploreItems.map(function (item, index) {
      var id = 'grid-explore-' + (item.id || index);
      usedIcons[item.iconToken || 'user'] = true;
      components[id] = {
        id: id,
        type: 'exploreCard',
        title: item.title || '',
        subtitle: item.subtitle || '',
        iconToken: item.iconToken || 'user',
        color: item.color || '#8b5cf6',
        actionId: item.actionId || null,
        events: item.actionId ? { tap: { actionId: item.actionId } } : undefined,
        layout: { columnSpan: 1, rowSpan: 1 },
        appearance: { borderRadius: 18, backgroundColor: '#ffffff' }
      };
      return id;
    });

    components['grid-page'] = {
      id: 'grid-page',
      type: 'scrollPage',
      layout: {
        direction: 'vertical',
        safeArea: true,
        padding: { left: 20, right: 20, top: 12, bottom: 28 },
        gap: 18
      },
      style: 'gridNetwork',
      children: [
        'grid-page-header',
        'grid-feature-row',
        'grid-explore-section',
        'grid-quote-block'
      ]
    };

    components['grid-page-header'] = {
      id: 'grid-page-header',
      type: 'pageHeader',
      title: header.title || 'Grid',
      subtitle: header.subtitle || 'Your professional network space',
      typography: { titleToken: 'typography.pageTitle', subtitleToken: 'typography.pageSubtitle' },
      actions: [
        {
          id: 'grid-header-scanner',
          iconToken: 'scanner',
          actionId: 'grid-open-scanner'
        },
        {
          id: 'grid-header-avatar',
          type: 'avatarButton',
          label: header.avatarInitial || 'S',
          initial: header.avatarInitial || 'S',
          color: header.avatarColor || '#7c3aed',
          actionId: 'navigate-profile',
          imageUrl: header.avatarUrl || '',
          avatarUrl: header.avatarUrl || '',
          image: header.avatarUrl || ''
        }
      ]
    };

    components['grid-feature-row'] = {
      id: 'grid-feature-row',
      type: 'responsiveGrid',
      layout: { columns: 2, columnGap: 12, rowGap: 12, independentScroll: false },
      overflowPolicy: 'reflow',
      preserveChildOrder: true,
      children: featureChildIds
    };

    components['grid-explore-section'] = {
      id: 'grid-explore-section',
      type: 'section',
      title: exploring.title || 'Continue Exploring',
      trailing: {
        label: exploring.seeAllLabel || 'See all',
        actionId: exploring.seeAllActionId || 'grid-see-all-exploring'
      },
      children: ['grid-explore-row']
    };

    components['grid-explore-row'] = {
      id: 'grid-explore-row',
      type: 'responsiveGrid',
      layout: { columns: 2, columnGap: 12, rowGap: 12, independentScroll: false },
      overflowPolicy: 'reflow',
      preserveChildOrder: true,
      children: exploreChildIds
    };

    components['grid-quote-block'] = {
      id: 'grid-quote-block',
      type: 'quoteBlock',
      title: quote.text || 'Connections turn potential into progress.',
      iconToken: quote.iconToken || 'quote',
      appearance: {
        backgroundColor: '#f5f3ff',
        textColor: '#312e81',
        borderRadius: 20
      }
    };

    components['grid-loading'] = { id: 'grid-loading', type: 'skeleton', variant: 'home-dashboard' };
    components['grid-empty'] = {
      id: 'grid-empty',
      type: 'emptyState',
      iconToken: 'grid',
      title: 'Grid is empty',
      subtitle: 'Your network space will appear here.'
    };
    components['grid-error'] = {
      id: 'grid-error',
      type: 'errorState',
      title: 'Unable to load Grid',
      retryActionId: 'refresh-grid'
    };
  }

  function extractGridPageSchema(fullSchema, layout, cfg) {
    fullSchema = fullSchema || {};
    var components = fullSchema.components || {};
    var gridComponents = {};
    Object.keys(components).forEach(function (key) {
      if (key.indexOf('grid-') === 0) gridComponents[key] = components[key];
    });
    if (!gridComponents['grid-page']) {
      var usedIcons = {};
      applyGridNetworkPageComponents(gridComponents, layout, usedIcons, cfg || {}, {});
    }
    var actions = fullSchema.actions || fullSchema.actionRegistry || {};
    var gridActions = buildGridPageActions(cfg);
    Object.keys(actions).forEach(function (key) {
      if (key.indexOf('grid-') === 0 || key === 'navigate-grid' || key === 'refresh-grid' || key === 'navigate-profile') {
        gridActions[key] = actions[key];
      }
    });
    return {
      schemaType: fullSchema.schemaType || 'server-driven-mobile-app',
      schemaVersion: fullSchema.schemaVersion || '3.0.0',
      pageId: 'grid',
      routeId: 'grid',
      rootComponentId: 'grid-page',
      route: (fullSchema.routes && fullSchema.routes.grid) || { path: '/grid', pageId: 'grid' },
      page: (fullSchema.pages && fullSchema.pages.grid) || {
        id: 'grid',
        routeId: 'grid',
        rootComponentId: 'grid-page',
        presentationStates: {
          loadingComponentId: 'grid-loading',
          emptyComponentId: 'grid-empty',
          errorComponentId: 'grid-error',
          offlineComponentId: 'offline-banner'
        }
      },
      components: gridComponents,
      actions: gridActions,
      dataSources: {
        grid: (fullSchema.dataSources && fullSchema.dataSources.grid) || buildGridDataSource(cfg, layout)
      },
      content: resolveGridPageContent(layout),
      iconRegistry: fullSchema.iconRegistry || buildIconRegistry(['grid', 'scanner', 'user', 'users', 'quote', 'chevron-right', 'profile'])
    };
  }

  function buildSchema(layout, cfg, helpers) {
    helpers = helpers || {};
    var getCardAnimOffset = helpers.getCardAnimOffset || function () { return 0; };
    var getDeviceAnimOffset = helpers.getDeviceAnimOffset || function () { return 0; };
    var getDefaultFace = helpers.getDefaultFace || function () { return 0; };

    layout = calibratePublishedHomeCanvas(layout || {});
    cfg = cfg || {};
    var theme = layout.theme || {};
    layout.theme = theme;
    applySharedBottomNav(layout);
    var staticUiEarly = buildStaticUiSnapshot(layout, cfg);
    if (staticUiEarly && staticUiEarly.enabled) {
      applyStaticUiAppearanceToTheme(theme, staticUiEarly);
      layout.staticUiTemplate = staticUiEarly.templateId;
      theme.staticUiTemplate = staticUiEarly.templateId;
    }
    applySharedBottomNav(layout);
    var userId = cfg.userId || 0;
    var userEmail = cfg.userEmail || (layout.welcome && layout.welcome.email) || '';
    var cardSpeed = theme.cardSpeed != null ? Number(theme.cardSpeed) : 16;
    var gridGap = resolveGridGap(theme);
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
          var imageDisplay = resolveFaceImageFitSettings(face, card);
          assets['asset-' + card.id + '-face-' + fi] = {
            type: 'network-image',
            source: resolvedImg,
            fit: imageDisplay.fit,
            alignment: imageDisplay.position,
            scale: imageDisplay.scale,
            objectFit: imageDisplay.fit,
            objectPosition: imageDisplay.position,
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
      'navigate-devices': { type: 'host-action', actionId: 'open-iot' },
      'navigate-category': { type: 'navigate', destination: { routeId: 'category' } },
      'navigate-grid': { type: 'navigate', destination: { routeId: 'grid' } },
      'navigate-alerts': { type: 'navigate', destination: { routeId: 'alerts' } },
      'navigate-reports': { type: 'navigate', destination: { routeId: 'reports' } },
      'navigate-rooms': { type: 'navigate', destination: { routeId: 'rooms' } },
      'navigate-profile': { type: 'navigate', destination: { routeId: 'profile' } },
      'navigate-add-device': { type: 'navigate', destination: { routeId: 'add-device' } },
      'open-grid': { type: 'host-action', actionId: 'open-grid' },
      'open-grid-module': { type: 'navigate', destination: { routeId: 'grid' } },
      'open-hrms-module': { type: 'navigate', destination: { routeId: 'hrms' } },
      'refresh-grid': {
        type: 'sequence',
        actions: [
          { type: 'set-state', path: 'refreshing', value: true },
          { type: 'refresh-data-source', dataSourceId: 'grid' },
          { type: 'set-state', path: 'refreshing', value: false }
        ],
        errorPresentation: { type: 'snackbar', message: 'Unable to refresh Grid' }
      },
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
    // Grid feature/host actions live in get_grid (grid_api_creation.php), not home schema.

    cards.forEach(function (card) {
      var tapId = cardTapActionId(card);
      if (tapId && !actionRegistry[tapId] && card.pageRoute) {
        var rawRoute = String(card.pageRoute).trim();
        if (isGridHomeCard(card)) {
          actionRegistry[tapId] = { type: 'navigate', destination: { routeId: 'grid' } };
        } else if (/^https?:\/\//i.test(rawRoute)) {
          actionRegistry[tapId] = { type: 'external-navigate', url: rawRoute };
        } else {
          var routeSlug = rawRoute.replace(/^\//, '').replace(/[^a-z0-9]+/gi, '-').toLowerCase();
          var known = routeSlug === 'iot' ? 'devices' : routeSlug;
          if (['home', 'devices', 'profile', 'alerts', 'reports', 'add-device', 'grid', 'category', 'rooms'].indexOf(known) >= 0) {
            actionRegistry[tapId] = {
              type: 'navigate',
              destination: { routeId: known }
            };
          } else {
            actionRegistry[tapId] = {
              type: 'navigate',
              destination: { routeId: routeSlug || ('page-' + card.id), parameters: { path: rawRoute } }
            };
          }
        }
      }
    });

    var components = {};
    var homeGridChildren = [];

    cards.forEach(function (card, index) {
      var compId = card.id + '-card';
      homeGridChildren.push(compId);
      var tapAction = cardTapActionId(card);
      var events = { longPress: { actionId: 'show-card-faces' } };
      // Prefer face-level tap when faces exist so URL / route on the active face wins.
      var facesHaveOwnTap = Array.isArray(card.faces) && card.faces.length > 0;
      if (tapAction && (!facesHaveOwnTap || card.type === 'add')) {
        events.tap = { actionId: tapAction };
      }

      var faces = (card.faces || []).map(function (face, fi) {
        var builtFace = buildHomeFace(face, card, fi, usedIcons, theme, cfg);
        var tapActionId = resolveFaceTapActionId(card, face, fi, actionRegistry);
        builtFace.events = { tap: { actionId: tapActionId } };
        var faceRouteId = resolveFaceRouteId(card, face, fi);
        if (faceRouteId) {
          builtFace.routeId = faceRouteId;
        } else {
          delete builtFace.routeId;
        }
        return builtFace;
      });

      var faceSec = resolveCardFaceSec(card, theme, helpers);
      var cardBorderRadius = resolveTileBorderRadius(card, theme);
      var cardCol = Math.max(1, Math.min(12, Number(card.colSpan != null ? card.colSpan : 1) || 1));
      var cardRow = Math.max(1, Math.min(16, Number(card.rowSpan != null ? card.rowSpan : 1) || 1));
      // Market Festival Specials must sit beside Daily Needs (not full-width).
      if (card.id === 'ef-m-special' && cardCol >= homeGridColumns) {
        cardCol = Math.min(2, Math.max(1, homeGridColumns - 1));
      }
      if (card.id === 'ef-m-special') {
        cardCol = Math.min(cardCol, 2);
        cardRow = Math.max(cardRow, 2);
      }
      var blockType = normalizeHomeBlockType(card.type);
      var orientation = card.orientation || (
        cardCol > 1 && cardRow === 1 ? 'wide' :
        cardRow > 1 && cardCol === 1 ? 'tall' :
        cardCol > 1 && cardRow > 1 ? 'large' : 'square'
      );

      components[compId] = {
        id: compId,
        presetId: 'home-tile',
        cardType: blockType,
        sortOrder: index,
        gridArea: compId,
        orientation: orientation,
        iconToken: faToToken(card.icon),
        layout: (function () {
          var lay = { columnSpan: cardCol, rowSpan: cardRow };
          if (card.col != null && card.col !== '' && !isNaN(Number(card.col))) lay.columnStart = Number(card.col);
          if (card.row != null && card.row !== '' && !isNaN(Number(card.row))) lay.rowStart = Number(card.row);
          return lay;
        })(),
        responsiveLayout: buildCardResponsiveLayout(card, theme),
        overflowPolicy: 'clip',
        dimensions: {
          columnSpan: cardCol,
          rowSpan: cardRow,
          borderRadius: cardBorderRadius,
          minRowHeightDp: homeGridRowHeight,
          sizeFormula: 'slotWidth'
        },
        appearance: {
          backgroundColor: card.color || card.bgColor || '#334155',
          borderRadius: cardBorderRadius,
          textColor: card.textColor || '#ffffff',
          iconColor: card.iconColor || '#ffffff',
          shadowToken: 'shadows.tile',
          style: 'metroTile',
          iconPosition: 'center',
          titlePosition: 'bottomLeft',
          sameFaceColor: card.sameFaceColor === true,
          sameFaceTextColor: card.sameFaceTextColor === true
        },
        timing: {
          faceSec: faceSec,
          cycleDurationMs: resolveCardDurationMs(card, theme, helpers),
          usesGlobalDefault: card.faceSec == null || card.faceSec === '',
          startDelayMs: card.startDelayMs != null && card.startDelayMs !== '' ? Number(card.startDelayMs) : null
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
      persistAcrossRoutes: true,
      position: 'fixed',
      outsideScroll: true,
      appearance: buildBottomNavAppearance(theme)
    };

    components['home-page'] = {
      id: 'home-page',
      type: 'scrollPage',
      layout: withNavSafeScrollLayout({
        direction: 'vertical',
        safeArea: true,
        padding: { left: 10, right: 10, top: 8, bottom: NAV_CHROME_BOTTOM },
        gap: gridGap
      }),
      children: ['home-header', 'welcome-banner', 'alerts-summary', 'home-grid']
    };

    components['home-header'] = buildHomeHeaderComponent(layout, cfg, theme, usedIcons);

    components['welcome-banner'] = (function () {
      var profile = mergeLayoutProfile(layout, cfg);
      var avatarUrl = getProfileAvatarUrl(profile, cfg);
      var welcome = layout.welcome || {};
      var greetingText = String(welcome.greeting || '').trim() || '👋 Welcome back';
      var captionText = String(welcome.caption || '').trim() || 'Manage your ecosystem';
      return {
        id: 'welcome-banner',
        type: 'banner',
        visibleWhen: { '$state': 'preferences.showWelcome', equals: true },
        content: {
          greeting: greetingText,
          supportingText: {
            '$state': 'session.user.email',
            fallback: userEmail || (profile && profile.email) || ''
          },
          caption: captionText
        },
        typography: {
          greeting: {
            fontSize: 15,
            fontWeight: 700,
            lineHeight: 1.2,
            colorToken: 'colors.welcomeText',
            maxLines: 2,
            overflow: 'clip',
            softWrap: true
          },
          supportingText: {
            fontSize: 13,
            fontWeight: 600,
            lineHeight: 1.25,
            colorToken: 'colors.welcomeText',
            maxLines: 1,
            overflow: 'ellipsis'
          },
          caption: {
            fontSize: 11,
            fontWeight: 500,
            lineHeight: 1.2,
            colorToken: 'colors.welcomeText',
            opacity: 0.88,
            maxLines: 2,
            overflow: 'clip',
            softWrap: true
          }
        },
        avatar: {
          initials: getProfileInitials(profile, cfg),
          imageUrl: avatarUrl,
          imageUrlBinding: avatarUrl ? {
            '$bind': 'session.user.avatarUrl',
            fallback: avatarUrl
          } : undefined,
          appearance: {
            width: 52,
            height: 52,
            borderRadius: 999,
            borderWidth: 2,
            borderColor: 'rgba(255,255,255,.28)',
            fontSize: 18,
            fontWeight: 800,
            textColor: '#ffffff',
            objectFit: 'cover'
          },
          events: { tap: { actionId: 'navigate-profile' } },
          accessibilityLabel: 'Open profile'
        },
        layout: {
          direction: 'row',
          align: 'center',
          justify: 'space-between',
          gap: 12,
          marginBottom: 12
        },
        textColumn: {
          flex: 1,
          direction: 'column',
          align: 'flex-start',
          gap: 3,
          minWidth: 0
        },
        appearance: {
          background: {
            type: 'linear-gradient',
            startColor: theme.welcomeStart || '#7c3aed',
            endColor: theme.welcomeEnd || '#4f46e5',
            direction: '135deg'
          },
          textColorToken: 'colors.welcomeText',
          radius: 20,
          padding: 16,
          shadow: '0 18px 40px rgba(76,29,149,.28)'
        }
      };
    })();

    components['alerts-summary'] = {
      id: 'alerts-summary',
      type: 'summaryBanner',
      visibleWhen: { '$state': 'preferences.showAlerts', equals: true },
      iconToken: 'alerts',
      content: {
        title: 'Live Alerts',
        value: bind('dashboard.alerts.count', (layout.alerts && layout.alerts.count) != null ? layout.alerts.count : 0),
        badge: (layout.alerts && layout.alerts.badgeText) || 'real-time',
        // Match web Live Alerts chrome (bell + date + new users + status pill).
        newUsers: bind(
          'dashboard.alerts.newUsers',
          (layout.alerts && layout.alerts.newUsers) != null ? layout.alerts.newUsers : 0
        ),
        meta: (function () {
          var d = new Date();
          var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
          var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
          var dateLabel = days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
          var users = (layout.alerts && layout.alerts.newUsers) != null ? Number(layout.alerts.newUsers) : 0;
          return dateLabel + ' · ' + users + ' new users';
        })(),
        pill: (layout.alerts && layout.alerts.showBadge === false) ? null : 'now'
      },
      appearance: {
        backgroundColorToken: 'colors.surface',
        textColorToken: 'colors.textPrimary',
        radiusToken: 'radius.card',
        iconToken: 'alerts',
        showIcon: true,
        showMeta: true,
        showPill: (layout.alerts && layout.alerts.showBadge) !== false
      }
    };

    components['home-grid'] = {
      id: 'home-grid',
      type: 'responsiveGrid',
      layout: Object.assign({
        type: 'metroGrid',
        columns: resolveHomeGridColumns(theme),
        gap: resolveGridGap(theme),
        independentScroll: false,
        width: '100%',
        maxWidth: '100%',
        sizeFormula: 'slotWidth'
      }, homeGridLayouts(theme).compact),
      layouts: homeGridLayouts(theme),
      overflowPolicy: 'reflow',
      preventHorizontalOverflow: true,
      preserveChildOrder: true,
      clampChildColumnSpan: true,
      style: 'metroMosaic',
      children: homeGridChildren
    };

    var appliedStaticUi = applyStaticHomeUiToComponents(components, layout, cfg, theme);

    var devPage = layout.devicesPage || {};
    var devHeader = devPage.header || {};
    components['devices-page'] = {
      id: 'devices-page',
      type: 'scrollPage',
      layout: withNavSafeScrollLayout({
        direction: 'vertical',
        safeArea: true,
        padding: { left: 10, right: 10, top: 8, bottom: NAV_CHROME_BOTTOM },
        gap: gridGap
      }),
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
      layout: Object.assign({}, buildResponsiveGridLayouts(theme).compact, {
        independentScroll: false
      }),
      layouts: buildResponsiveGridLayouts(theme),
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

    ['reports', 'rooms', 'add-device', 'grid-details', 'hrms'].forEach(function (pageKey) {
      var titles = {
        reports: ['Reports', 'Usage, performance and activity reports'],
        rooms: ['Rooms', 'Organize and control devices by room'],
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

    // Profile is a full screen (header + menu + logout), not a pageHeader stub.
    applyProfilePageComponents(components, layout, cfg, theme, usedIcons);

    // Category destination for the configurable nav slot. It must render safely with no
    // category data at all, so the grid starts empty and the page declares an empty state.
    components['category-page'] = {
      id: 'category-page',
      type: 'scrollPage',
      layout: { safeArea: true, padding: 16 },
      children: ['category-page-header', 'category-grid']
    };
    components['category-page-header'] = {
      id: 'category-page-header',
      type: 'pageHeader',
      title: 'Category',
      subtitle: 'Browse by category'
    };
    components['category-grid'] = {
      id: 'category-grid',
      type: 'responsiveGrid',
      layout: Object.assign({}, buildResponsiveGridLayouts(theme).compact, { independentScroll: false }),
      layouts: buildResponsiveGridLayouts(theme),
      overflowPolicy: 'reflow',
      preserveChildOrder: true,
      clampChildColumnSpan: true,
      children: []
    };
    components['category-loading'] = { id: 'category-loading', type: 'skeleton', variant: 'device-grid' };
    components['category-empty'] = {
      id: 'category-empty', type: 'emptyState', iconToken: 'category',
      title: 'No categories yet', subtitle: 'Categories will appear here once they are available.'
    };
    components['category-error'] = { id: 'category-error', type: 'errorState', title: 'Unable to load categories', retryActionId: 'refresh-dashboard' };

    // Grid network page is a separate SDUI API (grid_api_creation.php), like IoT.
    // Home schema only keeps a route stub + dataSources.grid pointer — not the full UI tree.
    components['grid-loading'] = { id: 'grid-loading', type: 'skeleton', variant: 'home-dashboard' };
    components['grid-empty'] = {
      id: 'grid-empty',
      type: 'emptyState',
      iconToken: 'grid',
      title: 'Grid is empty',
      subtitle: 'Your network space will appear here.'
    };
    components['grid-error'] = {
      id: 'grid-error',
      type: 'errorState',
      title: 'Unable to load Grid',
      retryActionId: 'refresh-grid'
    };
    components['grid-page'] = {
      id: 'grid-page',
      type: 'scrollPage',
      style: 'gridNetwork',
      dataSourceId: 'grid',
      layout: withNavSafeScrollLayout({ safeArea: true, padding: 16 }),
      children: []
    };
    usedIcons.grid = true;

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

    var routes = {
      home: { path: '/home', pageId: 'home' },
      devices: { path: '/devices', pageId: 'devices' },
      category: { path: '/category', pageId: 'category' },
      grid: { path: '/grid', pageId: 'grid' },
      alerts: { path: '/alerts', pageId: 'alerts' },
      reports: { path: '/reports', pageId: 'reports' },
      rooms: { path: '/rooms', pageId: 'rooms' },
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
      category: {
        id: 'category',
        routeId: 'category',
        rootComponentId: 'category-page',
        presentationStates: {
          loadingComponentId: 'category-loading',
          emptyComponentId: 'category-empty',
          errorComponentId: 'category-error',
          offlineComponentId: 'offline-banner'
        }
      },
      grid: {
        id: 'grid',
        routeId: 'grid',
        rootComponentId: 'grid-page',
        presentationStates: {
          loadingComponentId: 'grid-loading',
          emptyComponentId: 'grid-empty',
          errorComponentId: 'grid-error',
          offlineComponentId: 'offline-banner'
        },
        dataSourceId: 'grid'
      },
      alerts: { id: 'alerts', routeId: 'alerts', rootComponentId: 'alerts-page' },
      reports: { id: 'reports', routeId: 'reports', rootComponentId: 'reports-page' },
      rooms: { id: 'rooms', routeId: 'rooms', rootComponentId: 'rooms-page' },
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

    decorateTabPages(pages);
    var navItems = buildBottomNavigationItems(layout, usedIcons, actionRegistry, routes, pages);

    usedIcons.refresh = true;
    usedIcons.home = true;
    usedIcons.devices = true;
    usedIcons.alerts = true;
    usedIcons.reports = true;
    usedIcons.rooms = true;
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
        title: (layout.header && layout.header.title) || 'Welcome to Efactor!',
        description: (layout.header && layout.header.subtitle) || 'Smart ecosystem dashboard',
        defaultRouteId: 'home',
        defaultLocale: ts.locale,
        timezone: ts.timezone,
        generatedAtIst: ts.local,
        generatedAtUtc: ts.utc,
        themePreset: theme.bgStyle || 'light',
        themeMode: theme.bgStyle === 'light' ? 'light' : 'dark',
        layoutSnapshot: buildLayoutSnapshot(layout, cfg),
        staticUiTemplate: (layout.staticUiTemplate || theme.staticUiTemplate || null),
        staticUi: appliedStaticUi || buildStaticUiSnapshot(layout, cfg),
        uiMode: (appliedStaticUi && appliedStaticUi.enabled) ? 'staticHomeUi' : 'cubeHome'
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
        overflowPolicy: {
          text: 'ellipsis',
          header: 'ellipsis',
          grid: 'reflow',
          cubeFaces: 'clip',
          images: 'contain',
          pageScroll: 'scroll',
          horizontal: 'hidden'
        },
        pageScroll: {
          direction: 'vertical',
          singlePrimaryScrollContainer: true,
          nestedScrollableGrids: false,
          overflowX: 'hidden',
          bottomNavigationOutsideScroll: true,
          bottomNavigationFixed: true,
          reservedBottomInset: NAV_CHROME_BOTTOM,
          contentAvoidsBottomNavigation: true
        },
        responsiveGrid: {
          breakpoints: ['narrow', 'compact', 'medium', 'expanded'],
          defaultBreakpoint: 'compact',
          clampColumnSpanToColumns: true,
          minimumCellWidthDp: 96,
          narrowScreenThresholdDp: 360,
          narrowScreenMaxColumns: 2,
          preserveMetroColumnsOnNarrow: false,
          recalculateOnOrientationChange: true,
          layouts: homeGridLayouts(theme),
          breakpointColumns: {
            narrow: Math.min(2, homeGridColumns),
            compact: homeGridColumns,
            medium: Math.max(homeGridColumns, 4),
            expanded: 6
          }
        },
        gridCalculation: {
          mode: 'available-width',
          formula: '(availableWidth - ((columns - 1) * columnGap)) / columns',
          cardWidthFormula: '(cellWidth * columnSpan) + ((columnSpan - 1) * columnGap)',
          cardHeightFormula: '(rowHeight * rowSpan) + ((rowSpan - 1) * rowGap)',
          homeGridColumns: homeGridColumns,
          columnGap: gridGap,
          rowGap: gridGap,
          sizeFormula: 'slotWidth',
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

      cardRotationContract: {
        version: '1.2.0',
        componentTypes: ['rotatingFaceCard', 'rotatingDeviceCard'],
        model: 'fourFaceBoxRotation',
        rendererKey: 'fourFaceBoxRotation',
        axis: 'y',
        faceCount: CUBE_FACE_COUNT,
        faceOrder: FACE_GEOMETRY.slice(),
        faceIndexMap: { '0': 'front', '1': 'right', '2': 'back', '3': 'left' },
        algorithm: 'dwell-then-rotateY',
        transformStyle: 'preserve-3d',
        faceStacking: 'four-face-box',
        visibleFacePolicy: 'front-face-by-angle',
        faceSwapAtFlipProgress: null,
        holdRatio: CUBE_HOLD_RATIO,
        transitionRatio: 1 - CUBE_HOLD_RATIO,
        defaultDirection: 'counterclockwise',
        defaultRotationDir: 'rtl',
        directionBinding: 'component.rotation.direction',
        faceContentBinding: 'component.faces[faceIndex]',
        geometryBinding: 'component.rotation.geometry',
        animationBinding: 'component.rotation.animation',
        presetIds: {
          homeTile: 'tile-face-rotation',
          deviceTile: 'device-face-rotation'
        },
        excludedPresets: ['applicationCubeRotation'],
        applicationCubeRotationScope: 'AppCube3D-only',
        note: 'Flutter must use fourFaceBoxRotation: 4 faces on a real box (depth=slotWidth), parent translateZ(-half) rotateY. Do NOT flatFlipCard / mid-flip face swap. Do not use applicationCubeRotation for home/device tiles.'
      },

      designTokens: buildDesignTokens(theme),
      themePreset: theme.bgStyle || 'light',
      iconRegistry: buildIconRegistry(Object.keys(usedIcons)),
      assets: assets,

      dataSources: {
        dashboard: buildDashboardDataSource(cfg),
        devices: {
          type: 'rest',
          endpoint: String((cfg && cfg.iotApiUrl) || 'https://efactor.tech/v8/iot_api_creation.php').replace(/\/+$/, ''),
          method: 'GET',
          queryParameters: {
            action: 'get_iot_schema',
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
            schema: '$.schema',
            items: '$.schema.devices',
            summary: '$.schema.page.summary',
            fallbackItems: '$.devices'
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
        },
        grid: buildGridDataSource(cfg, layout)
      },

      state: {
        activeRouteId: layout.activePage || 'home',
        refreshing: false,
        selectedComponentId: null,
        animationsEnabled: true,
        preferences: {
          showWelcome: theme.showWelcome !== false,
          showAlerts: theme.showAlerts !== false,
          showBottomNav: theme.showBottomNav !== false
        },
        devices: {}
      },

      animationPresets: (function () {
        var defaultRotation = resolveCardRotationDirection(null);
        return {
        'tile-face-rotation': {
          type: 'four-face-box-cycle',
          model: 'fourFaceBoxRotation',
          rendererKey: 'fourFaceBoxRotation',
          transformStyle: 'preserve-3d',
          axis: 'y',
          direction: defaultRotation.direction,
          rotationDir: defaultRotation.rotationDir,
          rotationDirection: defaultRotation.rotationDirection,
          degreesPerFace: defaultRotation.degreesPerFace,
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
          faceSwapAtFlipProgress: null,
          geometry: buildCubeGeometryContract(defaultRotation),
          animation: buildCubeAnimationContract(durationMs, 0, defaultRotation)
        },
        'device-face-rotation': {
          type: 'four-face-box-cycle',
          model: 'fourFaceBoxRotation',
          rendererKey: 'fourFaceBoxRotation',
          transformStyle: 'preserve-3d',
          axis: 'y',
          direction: defaultRotation.direction,
          rotationDir: defaultRotation.rotationDir,
          rotationDirection: defaultRotation.rotationDirection,
          degreesPerFace: defaultRotation.degreesPerFace,
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
          faceSwapAtFlipProgress: null,
          geometry: buildCubeGeometryContract(defaultRotation),
          animation: buildCubeAnimationContract(durationMs, 0, defaultRotation)
        }
        };
      })(),

      componentPresets: (function () {
        var defaultRotation = resolveCardRotationDirection(null);
        return {
        'home-tile': {
          type: 'rotatingFaceCard',
          appearance: {
            textColorToken: 'colors.textPrimary',
            radiusToken: 'radius.card',
            shadowToken: 'shadows.card'
          },
          overflowPolicy: 'clip',
          contentOverflow: 'clip',
          rotation: {
            enabled: true,
            presetId: 'tile-face-rotation',
            geometry: buildCubeGeometryContract(defaultRotation),
            animation: buildCubeAnimationContract(durationMs, 0, defaultRotation)
          },
          dimensions: {
            minRowHeightDp: homeGridRowHeight,
            sizeFormula: 'slotWidth'
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
            geometry: buildCubeGeometryContract(defaultRotation),
            animation: buildCubeAnimationContract(durationMs, 0, defaultRotation)
          },
          dimensions: {
            minRowHeightDp: 96,
            sizeFormula: 'slotWidth'
          }
        }
        };
      })(),

      actionRegistry: actionRegistry,
      actions: actionRegistry,

      routes: routes,

      appShell: buildPersistentAppShell(theme),

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
          'faceSelector', 'deviceControlPanel', 'staticHomeUi', 'homeHeader',
          'profileHeader', 'profileMenu', 'actionButton', 'themeModeSelector', 'loginForm'
        ],
        allowedActionTypes: [
          'navigate', 'sequence', 'set-state', 'refresh-data-source', 'request',
          'show-bottom-sheet', 'show-dialog', 'show-snackbar', 'host-action', 'external-navigate', 'open-url'
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
    resolveHomeGridRows: resolveHomeGridRows,
    resolveHomeGridRowHeight: resolveHomeGridRowHeight,
    resolveGridGap: resolveGridGap,
    normalizeHomeBlockType: normalizeHomeBlockType,
    normalizeFaceContentType: normalizeFaceContentType,
    stableCardAnimOffsetSec: stableCardAnimOffsetSec,
    resolveApiOrigin: resolveApiOrigin,
    resolveDashboardApiEndpoint: resolveDashboardApiEndpoint,
    buildDashboardDataSource: buildDashboardDataSource,
    buildApiContract: buildApiContract,
    resolveGridApiUrl: resolveGridApiUrl,
    getDefaultGridPageContent: getDefaultGridPageContent,
    resolveGridPageContent: resolveGridPageContent,
    extractGridPageSchema: extractGridPageSchema,
    buildResponsiveGridLayouts: buildResponsiveGridLayouts,
    buildCardResponsiveLayout: buildCardResponsiveLayout,
    buildFaceContentLayout: buildFaceContentLayout,
    CUBE_HOLD_RATIO: CUBE_HOLD_RATIO,
    getProfileInitials: getProfileInitials,
    getProfileAvatarUrl: getProfileAvatarUrl,
    getProfileGreetingName: getProfileGreetingName,
    getThemeModeLabel: getThemeModeLabel,
    buildHomeHeaderComponent: buildHomeHeaderComponent,
    resolveHomeHeaderTitle: resolveHomeHeaderTitle,
    resolveHomeHeaderSubtitle: resolveHomeHeaderSubtitle,
    mergeLayoutProfile: mergeLayoutProfile,
    buildCardRotationBlock: buildCardRotationBlock,
    buildBottomNavAppearance: buildBottomNavAppearance,
    withNavSafeScrollLayout: withNavSafeScrollLayout,
    NAV_CHROME_BOTTOM: NAV_CHROME_BOTTOM,
    buildProfileAppearanceContract: buildProfileAppearanceContract,
    applyProfilePageComponents: applyProfilePageComponents,
    resolveLogoutButtonColors: resolveLogoutButtonColors,
    formatHomeGreeting: formatHomeGreeting,
    sanitizeWelcomeGreeting: sanitizeWelcomeGreeting,
    isIotHomeCard: isIotHomeCard,
    resolveFaceTapActionId: resolveFaceTapActionId,
    resolveFaceRouteId: resolveFaceRouteId,
    resolveMediaUrl: resolveMediaUrl,
    buildHomeFace: buildHomeFace,
    resolveFaceImageFitSettings: resolveFaceImageFitSettings,
    buildBottomNavigationItems: buildBottomNavigationItems,
    filterValidNavigationItems: filterValidNavigationItems,
    BOTTOM_NAV_CONTRACTS: BOTTOM_NAV_CONTRACTS,
    buildStaticUiSnapshot: buildStaticUiSnapshot,
    applyStaticUiAppearanceToTheme: applyStaticUiAppearanceToTheme,
    applyStaticHomeUiToComponents: applyStaticHomeUiToComponents,
    resolveStaticUiTemplateId: resolveStaticUiTemplateId
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
      if (schema.animationPresets && schema.animationPresets.applicationCubeRotation) {
        delete schema.animationPresets.applicationCubeRotation;
      }
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

  function resolveNonNegativeRadius(value, fallback) {
    if (value == null || value === '') return fallback;
    var n = Number(value);
    if (!isFinite(n)) return fallback;
    return n < 0 ? 0 : n;
  }

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
        optionalProps: [
          'bottomNavigationComponentId',
          'floatingActionComponentId',
          'bottomNavigationOutsidePageScroll',
          'bottomNavigationFixed',
          'persistsAcrossRoutes',
          'replaceOnNestedSchema',
          'reservedBottomInset'
        ],
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
        // The emitted header carries title/logo; greeting and avatar are optional
        // variants, so declaring them required contradicted every published schema.
        requiredProps: ['title'],
        optionalProps: ['subtitle', 'greeting', 'avatar', 'logo', 'layout', 'appearance', 'textColumn', 'overflowPolicy'],
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
        optionalProps: ['visibleWhen', 'appearance', 'iconToken', 'enabledWhen'],
        propsSchema: {
          iconToken: { type: 'string' },
          content: {
            type: 'object',
            properties: {
              title: { type: ['string', 'object'] },
              value: { type: ['number', 'string', 'object'] },
              badge: { type: ['string', 'object'] },
              newUsers: { type: ['number', 'string', 'object'] },
              meta: { type: ['string', 'object'] },
              pill: { type: ['string', 'object', 'null'] }
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
        optionalProps: ['children', 'childrenBinding', 'layout', 'layouts', 'overflowPolicy', 'preserveChildOrder', 'clampChildColumnSpan'],
        propsSchema: {
          children: { type: 'array', minItems: 0 },
          layouts: { type: 'object' },
          overflowPolicy: { type: 'string', enum: ['reflow', 'scroll', 'clip'], default: 'reflow' },
          preserveChildOrder: { type: 'boolean', default: true },
          clampChildColumnSpan: { type: 'boolean', default: true }
        },
        slots: ['child'],
        variants: ['narrow', 'compact', 'medium', 'expanded'],
        supportedLayouts: ['narrow', 'compact', 'medium', 'expanded'],
        bindingCompatibility: ['$bind']
      }),
      RotatingFaceCard: componentDef('rotatingFaceCard', {
        requiredProps: ['faces'],
        optionalProps: ['appearance', 'events', 'responsiveLayout', 'presetId', 'layout', 'rotation', 'defaultFaceIndex', 'overflowPolicy', 'contentLayout'],
        propsSchema: {
          faces: { type: 'array', minItems: 1, maxItems: 4 },
          defaultFaceIndex: { type: 'integer', minimum: 0, maximum: 3, default: 0 },
          layout: {
            type: 'object',
            properties: {
              columnSpan: { type: 'integer', minimum: 1, maximum: 12, default: 1 },
              rowSpan: { type: 'integer', minimum: 1, maximum: 16, default: 1 }
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
      StaticHomeUi: componentDef('staticHomeUi', {
        requiredProps: ['templateId', 'sections'],
        optionalProps: ['brand', 'appearance', 'bottomNav', 'previewImages', 'label', 'category', 'mode', 'rendererKey', 'replacesCubeHome', 'note', 'visible'],
        propsSchema: {
          templateId: { type: 'string' },
          sections: { type: 'array', minItems: 1 },
          brand: { type: 'object' },
          appearance: { type: 'object' },
          bottomNav: { type: 'array' }
        },
        slots: ['section'],
        variants: ['ui-style', 'ui-grocery', 'ui-market', 'ui-deals', 'ui-food', 'ui-quick'],
        supportedEvents: ['tap'],
        bindingCompatibility: ['$bind', '$state', '$i18n']
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
        optionalProps: [
          'phone', 'memberId', 'avatarText', 'avatarUrl', 'imageUrl', 'avatar', 'appearance', 'layout', 'fields',
          'title', 'fullName', 'displayName', 'eyebrow', 'meta', 'chip', 'phoneChip',
          'nameBinding', 'titleBinding', 'emailBinding', 'phoneBinding', 'memberIdBinding',
          'eyebrowBinding', 'avatarUrlBinding', 'imageUrlBinding', 'avatarTextBinding'
        ],
        propsSchema: {
          name: { type: ['string', 'object'] },
          title: { type: ['string', 'object'] },
          email: { type: ['string', 'object'] },
          phone: { type: ['string', 'object'] },
          memberId: { type: ['string', 'object'] },
          eyebrow: { type: ['string', 'object'] },
          avatarUrl: { type: 'string' },
          imageUrl: { type: 'string' },
          avatar: { type: 'object' },
          layout: { type: 'object' },
          fields: { type: 'object' },
          appearance: { type: 'object' }
        },
        supportedEvents: ['tap'],
        bindingCompatibility: ['$bind', '$state', '$i18n']
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

  function buildResponsive(theme) {
    theme = theme || {};
    var responsiveLayouts = (global.AhpSchemaV2 && global.AhpSchemaV2.buildResponsiveGridLayouts)
      ? global.AhpSchemaV2.buildResponsiveGridLayouts(theme)
      : {
        narrow: { columns: 2, columnGap: 12, rowGap: 12, rowHeight: 96, minRowHeight: 96 },
        compact: { columns: 3, columnGap: 12, rowGap: 12, rowHeight: 96, minRowHeight: 96 },
        medium: { columns: 4, columnGap: 12, rowGap: 12, rowHeight: 88, minRowHeight: 88 },
        expanded: { columns: 6, columnGap: 12, rowGap: 12, rowHeight: 96, minRowHeight: 96 }
      };
    return {
      enabled: true,
      defaultBreakpoint: 'compact',
      breakpoints: {
        narrow: { minimumWidth: 0, maximumWidth: 359, description: 'Small phones' },
        compact: { minimumWidth: 360, maximumWidth: 599, description: 'Phones' },
        medium: { minimumWidth: 600, maximumWidth: 1023, description: 'Tablets' },
        expanded: { minimumWidth: 1024, description: 'Desktop / large tablets' }
      },
      orientation: {
        portrait: { useConfiguredBreakpoint: true },
        landscape: { preferMediumLayout: true, clampColumnSpan: true }
      },
      overflowPolicy: {
        default: 'ellipsis',
        headerText: 'ellipsis',
        grid: 'reflow',
        cubeFaces: 'clip',
        images: 'clip',
        pageScroll: 'scroll'
      },
      textScale: {
        policy: 'system-clamped',
        minimum: 0.9,
        maximum: 1.3
      },
      grid: {
        minimumCellWidthDp: 96,
        narrowScreenThresholdDp: 360,
        narrowScreenMaxColumns: 2,
        clampColumnSpanToColumns: true,
        preserveMetroColumnsOnNarrow: false,
        recalculateOnOrientationChange: true,
        preserveChildOrder: true,
        layouts: responsiveLayouts,
        breakpointColumns: {
          narrow: responsiveLayouts.narrow.columns,
          compact: responsiveLayouts.compact.columns,
          medium: responsiveLayouts.medium.columns,
          expanded: responsiveLayouts.expanded.columns
        }
      },
      layoutSelection: {
        homeGrid: ['narrow', 'compact', 'medium', 'expanded'],
        devicesGrid: ['narrow', 'compact', 'medium', 'expanded']
      }
    };
  }

  function buildLocalization(layout) {
    var header = layout.header || {};
    var welcome = layout.welcome || {};
    var v2 = global.AhpSchemaV2 || {};
    var title = v2.resolveHomeHeaderTitle ? v2.resolveHomeHeaderTitle(layout) : (header.title || 'Welcome to Efactor!');
    var subtitle = v2.resolveHomeHeaderSubtitle ? v2.resolveHomeHeaderSubtitle(layout) : (header.subtitle || 'Control your connected home');
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
          'home.brandTitle': title,
          'home.brandSubtitle': subtitle,
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
          'navigation.rooms': 'Rooms',
          'navigation.profile': 'Profile',
          'alerts.title': 'Alerts',
          'alerts.subtitle': 'Recent system and device alerts',
          'alerts.emptyTitle': 'No alerts',
          'alerts.emptySubtitle': 'Everything looks good.',
          'reports.title': 'Reports',
          'reports.subtitle': 'Usage, performance and activity reports',
          'profile.title': 'Profile',
          'profile.subtitle': 'Profile, privacy, and logout',
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
          'home.brandTitle': title,
          'home.brandSubtitle': subtitle,
          'home.greeting': 'Hey, {{name}}',
          'home.welcome': greeting,
          'home.manageEcosystem': caption,
          'home.liveAlerts': 'Live Alerts',
          'navigation.home': 'Home',
          'navigation.devices': 'Devices',
          'navigation.alerts': 'Alerts',
          'navigation.reports': 'Reports',
          'navigation.rooms': 'Rooms',
          'navigation.profile': 'Profile',
          'login.title': 'Sign in',
          'login.subtitle': 'Continue to your eFactor account',
          'devices.detailsTitle': 'Device details',
          'devices.detailsSubtitle': 'Monitor and control this device'
        },
        'hi-IN': {
          'home.title': title,
          'home.subtitle': 'à¤¸à¥à¤®à¤¾à¤°à¥à¤Ÿ à¤œà¤¾à¤¨à¤•à¤¾à¤°à¥€ à¤”à¤° à¤‰à¤ªà¤•à¤°à¤£',
          'home.brandTitle': title,
          'home.brandSubtitle': 'à¤¸à¥à¤®à¤¾à¤°à¥à¤Ÿ à¤œà¤¾à¤¨à¤•à¤¾à¤°à¥€ à¤”à¤° à¤‰à¤ªà¤•à¤°à¤£',
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
          homeGreeting: { fontSize: 13, fontWeight: 600, lineHeight: 1.2, maxLines: 1, overflow: 'ellipsis' },
          homeTitle: { fontSize: 26, fontWeight: 800, lineHeight: 1.08, letterSpacing: -0.03, maxLines: 2, overflow: 'ellipsis' },
          homeSubtitle: { fontSize: 12, fontWeight: 400, lineHeight: 1.3, maxLines: 2, overflow: 'ellipsis' }
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
    var resolveLogout = (global.AhpSchemaV2 && global.AhpSchemaV2.resolveLogoutButtonColors)
      ? global.AhpSchemaV2.resolveLogoutButtonColors
      : function (t) {
        t = t || {};
        var light = t.bgStyle === 'light';
        return {
          background: light
            ? (t.textPrimary || '#111827')
            : (t.accentColor || t.navActiveColor || '#3b82f6'),
          text: '#ffffff'
        };
      };
    var logout = resolveLogout(theme);
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
        navActive: theme.navActiveColor || (isLight ? '#111827' : '#2dd4bf'),
        logoutBackground: logout.background,
        logoutText: logout.text
      },
      typography: {
        homeGreeting: { fontSize: 13, fontWeight: 600, lineHeight: 1.2, colorToken: 'colors.textMuted', maxLines: 1, overflow: 'ellipsis' },
        homeTitle: { fontSize: 26, fontWeight: 800, lineHeight: 1.08, letterSpacing: -0.03, colorToken: 'colors.textPrimary', maxLines: 2, overflow: 'ellipsis' },
        homeSubtitle: { fontSize: 12, fontWeight: 400, lineHeight: 1.3, colorToken: 'colors.textMuted', maxLines: 2, overflow: 'ellipsis' }
      }
    };
  }

  function resolveDefaultIotApiUrl(cfg) {
    cfg = cfg || {};
    var base = String(cfg.iotApiUrl || '').replace(/\/+$/, '');
    if (base) return base;
    if (typeof window !== 'undefined' && window.location && window.location.origin) {
      var origin = window.location.origin;
      if (/localhost|127\.0\.0\.1/i.test(origin)) {
        return origin + '/iot_api_creation.php';
      }
    }
    return 'https://efactor.tech/v8/iot_api_creation.php';
  }

  function sanitizePublishedSchema(schema) {
    if (!schema || typeof schema !== 'object') return schema;
    var out = JSON.parse(JSON.stringify(schema));
    if (out.state && out.state.session) {
      out.state.session.iotAccessToken = null;
    }
    if (out.session) {
      out.session.iotAccessToken = null;
    }
    if (out.components && out.components['home-header'] && out.components['home-header'].type === 'brandHeader') {
      out.components['home-header'].type = 'homeHeader';
    }
    var hasAppCube = !!(out.components && out.components['application-cube-3d']);
    if (!hasAppCube && out.animationPresets && out.animationPresets.applicationCubeRotation) {
      delete out.animationPresets.applicationCubeRotation;
    }
    function scrubRotationDimensions(node) {
      if (!node || typeof node !== 'object') return;
      if (Array.isArray(node)) {
        node.forEach(scrubRotationDimensions);
        return;
      }
      var rotationModel = (node.rotation && node.rotation.geometry && node.rotation.geometry.model)
        || (node.rotation && node.rotation.animation && node.rotation.animation.model)
        || (node.geometry && node.geometry.model)
        || (node.animation && node.animation.model)
        || node.model;
      // Legacy flatFlipCard had no depth; fourFaceBoxRotation keeps full box depth (= slotWidth).
      var isLegacyFlatFlip = rotationModel === 'flatFlipCard';
      if (isLegacyFlatFlip && node.dimensions) {
        delete node.dimensions.cubeDepthFormula;
        delete node.dimensions.perspectiveDp;
      }
      Object.keys(node).forEach(function (key) {
        scrubRotationDimensions(node[key]);
      });
    }
    scrubRotationDimensions(out.components);
    scrubRotationDimensions(out.componentPresets);
    scrubRotationDimensions(out.animationPresets);
    function scrubNode(node) {
      if (!node || typeof node !== 'object') return;
      if (Array.isArray(node)) {
        node.forEach(scrubNode);
        return;
      }
      Object.keys(node).forEach(function (key) {
        var val = node[key];
        if (typeof val === 'string') {
          if (/access_token=eyJ/i.test(val)) {
            node[key] = val
              .replace(/([?&])access_token=[^&]*/g, '$1access_token={{$secureState.session.iotAccessToken}}')
              .replace(/\?$/, '');
          }
        } else if (val && typeof val === 'object') {
          scrubNode(val);
        }
      });
    }
    scrubNode(out.dataSources);
    scrubNode(out.api);
    return out;
  }

  function buildIotDevicesDataSource(cfg) {
    cfg = cfg || {};
    var endpoint = String(cfg.iotApiUrl || resolveDefaultIotApiUrl(cfg)).replace(/\/+$/, '');
    var tokenBinding = { '$secureState': 'session.iotAccessToken' };
    return {
      type: 'rest',
      endpoint: endpoint,
      method: 'GET',
      queryParameters: {
        action: 'get_iot_schema',
        access_token: tokenBinding
      },
      headers: {
        Accept: 'application/json',
        'X-IOT-Access-Token': tokenBinding
      },
      authentication: {
        type: 'iot-access-token',
        tokenBinding: tokenBinding,
        refreshActionId: 'refresh-iot-access-token'
      },
      responseSchema: { '$ref': '#/models/DevicesResponse' },
      responseMapping: {
        schema: '$.schema',
        items: '$.schema.devices',
        summary: '$.schema.page.summary',
        fallbackItems: '$.devices',
        fallbackSummary: '$.summary'
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
    { routeId: 'profile-appearance', titleKey: 'profile.appearance', subtitleKey: 'profile.appearanceSubtitle' },
    { routeId: 'profile-privacy', titleKey: 'profile.privacy', subtitleKey: 'profile.privacySubtitle' }
  ];

  function buildScopedState(layout, cfg) {
    var theme = layout.theme || {};
    var bgStyle = theme.bgStyle || 'light';
    cfg = cfg || {};
    var v2 = global.AhpSchemaV2 || {};
    var profile = Object.assign({}, layout.profilePage || {}, cfg.loginUserProfile || {});
    var greetingName = v2.getProfileGreetingName ? v2.getProfileGreetingName(profile, cfg) : 'Guest';
    var initials = v2.getProfileInitials ? v2.getProfileInitials(profile, cfg) : 'U';
    var avatarUrl = v2.getProfileAvatarUrl ? v2.getProfileAvatarUrl(profile, cfg) : '';
    var layoutProfile = layout.profilePage || {};

    return {
      global: {
        themeMode: bgStyle === 'light' ? 'light' : 'dark',
        themePreset: bgStyle,
        themeServerControlled: true,
        locale: 'en-IN',
        networkStatus: 'online',
        apiEnvironment: 'production',
        iotApiUrl: cfg.iotApiUrl || resolveDefaultIotApiUrl(cfg),
        apiBase: cfg.apiBase || ''
      },
      session: {
        authenticated: true,
        userId: cfg.userId || 0,
        roles: ['user'],
        iotAccessToken: null,
        iotAccessTokenBinding: { '$secureState': 'session.iotAccessToken' },
        user: {
          email: profile.email || layoutProfile.email || cfg.userEmail || (layout.welcome && layout.welcome.email) || '',
          displayName: profile.name || layoutProfile.name || profile.email || cfg.userEmail || '',
          greetingName: greetingName,
          firstName: greetingName,
          initials: initials,
          avatarUrl: avatarUrl,
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
        profile: {
          loggingOut: false,
          notificationsEnabled: layoutProfile.notificationsEnabled !== false,
          language: layoutProfile.language || profile.language || 'English (India)'
        },
        login: {
          submitting: false
        }
      },
      preferences: {
        showWelcome: theme.showWelcome !== false,
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
    return buildCardResponsiveLayout(card, theme);
  }

  function routePermissionsFor(key) {
    if (key === 'login') return [];
    if (key === 'alerts') return ['alerts.read'];
    if (key === 'reports') return ['reports.read'];
    if (key === 'rooms') return ['dashboard.read'];
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
        requiredPermissions: isLogin ? [] : (r.requiredPermissions && key !== 'alerts' && key !== 'reports' && key !== 'rooms' && key !== 'profile' && key !== 'add-device' && key !== 'device-details'
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

  function enrichActions(actionRegistry, cfg, layout) {
    cfg = cfg || {};
    layout = layout || {};
    var out = Object.assign({}, actionRegistry || {});

    Object.keys(out).forEach(function (id) {
      out[id] = fixErrorPresentation(out[id]);
    });

    out['open-iot-app'] = { type: 'host-action', actionId: 'open-iot' };
    out['navigate-devices'] = { type: 'host-action', actionId: 'open-iot' };
    out['open-grid'] = { type: 'host-action', actionId: 'open-grid' };
    out['open-grid-module'] = { type: 'navigate', destination: { routeId: 'grid' } };
    out['navigate-grid'] = { type: 'navigate', destination: { routeId: 'grid' } };

    out['profile-edit'] = { type: 'navigate', destination: { routeId: 'profile-edit' } };
    out['profile-notifications'] = { type: 'navigate', destination: { routeId: 'profile-notifications' } };
    out['profile-appearance'] = { type: 'navigate', destination: { routeId: 'profile-appearance' } };
    out['profile-privacy'] = { type: 'navigate', destination: { routeId: 'profile-privacy' } };

    out['profile-edit-open'] = {
      type: 'host-action',
      actionId: 'open-edit-profile',
      parameters: {
        fields: ['name', 'email', 'phone']
      }
    };

    var prefsApiBase = (cfg.apiBase ? String(cfg.apiBase).replace(/\/+$/, '') : '/index.php/app-home-page/api');
    out['profile-notifications-enable'] = {
      type: 'sequence',
      actions: [
        { type: 'set-state', path: 'pages.profile.notificationsEnabled', value: true },
        {
          type: 'request',
          request: {
            method: 'POST',
            endpoint: prefsApiBase + '/profile-preferences',
            headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
            body: { notificationsEnabled: true }
          },
          onSuccess: { type: 'show-snackbar', message: 'Notifications enabled' },
          onError: { type: 'show-snackbar', message: 'Could not save notification preference' }
        },
        { type: 'host-action', actionId: 'save-profile-preferences', parameters: { notificationsEnabled: true } }
      ]
    };
    out['profile-notifications-disable'] = {
      type: 'sequence',
      actions: [
        { type: 'set-state', path: 'pages.profile.notificationsEnabled', value: false },
        {
          type: 'request',
          request: {
            method: 'POST',
            endpoint: prefsApiBase + '/profile-preferences',
            headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
            body: { notificationsEnabled: false }
          },
          onSuccess: { type: 'show-snackbar', message: 'Notifications disabled' },
          onError: { type: 'show-snackbar', message: 'Could not save notification preference' }
        },
        { type: 'host-action', actionId: 'save-profile-preferences', parameters: { notificationsEnabled: false } }
      ]
    };

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

  var DEFAULT_ASSET_DOMAINS = [
    'images.unsplash.com',
    'plus.unsplash.com',
    'encrypted-tbn0.gstatic.com',
    'cdn.pixabay.com',
    'm-cdn.phonearena.com',
    'images.news18.com',
    'www.techtarget.com',
    'media.wired.com',
    'efactor.tech',
    'www.efactor.tech',
    'localhost',
    '127.0.0.1'
  ];

  function extractAssetHost(url, cfg) {
    var value = String(url || '').trim();
    if (!value) return '';
    if (value.indexOf('//') === 0) value = 'https:' + value;
    if (value.charAt(0) === '/' && value.charAt(1) !== '/') {
      if (cfg && cfg.siteOrigin) return extractAssetHost(cfg.siteOrigin, cfg);
      if (typeof window !== 'undefined' && window.location && window.location.hostname) {
        return String(window.location.hostname).toLowerCase().replace(/:\d+$/, '');
      }
      return '';
    }
    var match = value.match(/^https?:\/\/([^/?#]+)/i);
    return match ? String(match[1]).toLowerCase().replace(/:\d+$/, '') : '';
  }

  function buildAllowedAssetDomains(assets, cfg) {
    var hosts = {};
    DEFAULT_ASSET_DOMAINS.forEach(function (host) { hosts[host] = true; });
    if (cfg && cfg.siteOrigin) {
      var originHost = extractAssetHost(cfg.siteOrigin, cfg);
      if (originHost) hosts[originHost] = true;
    }
    if (cfg && cfg.apiBase) {
      var apiHost = extractAssetHost(cfg.apiBase, cfg);
      if (apiHost) hosts[apiHost] = true;
    }
    if (cfg && cfg.filesBase) {
      var filesHost = extractAssetHost(cfg.filesBase, cfg);
      if (filesHost) hosts[filesHost] = true;
    }
    Object.keys(assets || {}).forEach(function (key) {
      var asset = assets[key];
      if (!asset || !asset.source) return;
      var host = extractAssetHost(asset.source, cfg);
      if (host) hosts[host] = true;
    });
    return Object.keys(hosts).sort();
  }

  function resolveMediaUrlForAssets(url, cfg) {
    if (global.AhpSchemaV2 && typeof global.AhpSchemaV2.resolveMediaUrl === 'function') {
      return global.AhpSchemaV2.resolveMediaUrl(url, cfg);
    }
    var src = String(url || '').trim();
    if (!src) return '';
    if (/^https?:\/\//i.test(src) || src.indexOf('/') === 0 || src.indexOf('files/') === 0) return src;
    var base = (cfg && cfg.filesBase) ? String(cfg.filesBase).replace(/\/+$/, '') : '/files';
    return base + '/brand_logo/' + encodeURIComponent(src);
  }

  function ensureSchemaAssets(assets, layout, cfg) {
    assets = Object.assign({}, assets || {});
    resolveLayoutCardsForSchema(layout).forEach(function (card) {
      (card.faces || []).forEach(function (face, fi) {
        var img = face.image || (fi === 0 ? card.image : '');
        if (!img) return;
        var key = 'asset-' + card.id + '-face-' + fi;
        var imageDisplay = (global.AhpSchemaV2 && global.AhpSchemaV2.resolveFaceImageFitSettings)
          ? global.AhpSchemaV2.resolveFaceImageFitSettings(face, card)
          : { fit: 'cover', position: 'center', scale: 100 };
        if (!assets[key]) {
          assets[key] = {
            type: 'network-image',
            source: resolveMediaUrlForAssets(img, cfg),
            fit: imageDisplay.fit,
            alignment: imageDisplay.position,
            scale: imageDisplay.scale,
            fitPolicy: imageDisplay.fit,
            objectFit: imageDisplay.fit,
            objectPosition: imageDisplay.position,
            cache: true,
            timeoutMs: 10000,
            placeholderIconToken: 'image-placeholder',
            errorIconToken: 'image-placeholder'
          };
        }
      });
    });
    return assets;
  }

  function mergeCanonicalHomeFaces(freshTile, card, cfg) {
    var v2 = global.AhpSchemaV2 || {};
    var usedIcons = {};
    var theme = (card && card.theme) || {};
    var faces = (freshTile && Array.isArray(freshTile.faces) && freshTile.faces.length)
      ? freshTile.faces.slice()
      : [];
    if (v2.buildHomeFace && Array.isArray(card.faces)) {
      return card.faces.map(function (face, fi) {
        var built = v2.buildHomeFace(face || {}, card, fi, usedIcons, theme, cfg || {});
        var previous = faces[fi] || {};
        var imgSrc = (face && face.image) || (fi === 0 ? card.image : '') || '';
        var merged = Object.assign({}, previous, built, {
          type: built.type || previous.type,
          assetId: built.assetId || previous.assetId || (imgSrc ? ('asset-' + card.id + '-face-' + fi) : undefined),
          image: built.image || previous.image || (imgSrc ? resolveMediaUrlForAssets(imgSrc, cfg) : undefined),
          imageFit: built.imageFit || previous.imageFit || face.imageFit || card.imageFit || 'cover',
          imagePosition: built.imagePosition || previous.imagePosition || face.imagePosition || card.imagePosition || 'center',
          imageScale: built.imageScale != null ? built.imageScale : (previous.imageScale != null ? previous.imageScale : (face.imageScale != null ? face.imageScale : (card.imageScale != null ? card.imageScale : 100))),
          imageAlignment: built.imagePosition || previous.imagePosition || face.imagePosition || card.imagePosition || 'center',
          route: built.route != null ? built.route : previous.route,
          openIn: built.openIn != null ? built.openIn : previous.openIn,
          events: previous.events || built.events
        });
        if (typeof v2.resolveFaceRouteId === 'function') {
          var rid = v2.resolveFaceRouteId(card, face || {}, fi);
          if (rid) merged.routeId = rid;
          else delete merged.routeId;
        } else if (built.route && /^https?:\/\//i.test(String(built.route))) {
          delete merged.routeId;
        }
        return merged;
      });
    }
    return faces;
  }

  function resolveLayoutCardsForSchema(layout) {
    var cards = (layout && Array.isArray(layout.cards)) ? layout.cards.slice() : [];
    if (!cards.length && layout && layout.meta && layout.meta.layoutSnapshot &&
        Array.isArray(layout.meta.layoutSnapshot.cards)) {
      cards = layout.meta.layoutSnapshot.cards.slice();
    }
    return cards.filter(function (card) {
      return card && card.id;
    });
  }

  function cardFaceRouteIdFromCard(card, faceIndex) {
    var part = String(card.id || card.title || 'card')
      .replace(/[^a-z0-9]+/gi, '-')
      .replace(/^-+|-+$/g, '')
      .toLowerCase() || 'card';
    return 'card-' + part + '-face-' + faceIndex;
  }

  /**
   * Keep components.home-grid.children and components.<cardId>-card aligned with layout.cards /
   * meta.layoutSnapshot.cards so Flutter renders the same grid as the web preview.
   */
  function reconcileHomeGridComponents(out, layout, cfg, freshComponents) {
    out = out || {};
    freshComponents = freshComponents || {};
    cfg = cfg || {};
    var cards = resolveLayoutCardsForSchema(layout);
    if (!cards.length) return out;

    var cardIdSet = {};
    var expectedChildIds = [];

    cards.forEach(function (card, index) {
      if (!card || !card.id) return;
      cardIdSet[card.id] = true;
      var compId = card.id + '-card';
      expectedChildIds.push(compId);

      var freshTile = freshComponents[compId] || null;
      var existing = out[compId] || {};
      var colSpan = Math.max(1, Math.min(12, Number(
        card.colSpan != null ? card.colSpan : (card.columnSpan != null ? card.columnSpan : 1)
      ) || 1));
      var rowSpan = Math.max(1, Math.min(16, Number(card.rowSpan != null ? card.rowSpan : 1) || 1));
      var borderRadius = card.borderRadius != null && card.borderRadius !== ''
        ? resolveNonNegativeRadius(card.borderRadius, 0)
        : (
          (existing.dimensions && existing.dimensions.borderRadius != null && existing.dimensions.borderRadius !== '')
            ? resolveNonNegativeRadius(existing.dimensions.borderRadius, 0)
            : ((freshTile && freshTile.dimensions && freshTile.dimensions.borderRadius != null && freshTile.dimensions.borderRadius !== '')
              ? resolveNonNegativeRadius(freshTile.dimensions.borderRadius, 0)
              : resolveNonNegativeRadius((layout && layout.theme && layout.theme.cardBorderRadius), 0))
        );

      if (freshTile) {
        out[compId] = Object.assign({}, freshTile, existing, {
          id: compId,
          presetId: 'home-tile',
          sortOrder: index,
          cardType: (global.AhpSchemaV2 && global.AhpSchemaV2.normalizeHomeBlockType
            ? global.AhpSchemaV2.normalizeHomeBlockType(card.type || existing.cardType || freshTile.cardType || 'stack')
            : String(card.type || existing.cardType || freshTile.cardType || 'stack')),
          gridArea: compId,
          layout: Object.assign({}, freshTile.layout || {}, existing.layout || {}, {
            columnSpan: colSpan,
            rowSpan: rowSpan
          }),
          dimensions: Object.assign({}, freshTile.dimensions || {}, existing.dimensions || {}, {
            columnSpan: colSpan,
            rowSpan: rowSpan,
            borderRadius: borderRadius
          }),
          appearance: Object.assign({}, freshTile.appearance || {}, existing.appearance || {}, {
            backgroundColor: card.color || card.bgColor || (freshTile.appearance || {}).backgroundColor || '#334155',
            borderRadius: borderRadius,
            textColor: card.textColor || (freshTile.appearance || {}).textColor,
            iconColor: card.iconColor || (freshTile.appearance || {}).iconColor
          }),
          faces: mergeCanonicalHomeFaces(freshTile, card, cfg),
          // Prefer freshly built rotation/timing/events from layout cards (studio preview).
          rotation: freshTile.rotation || existing.rotation,
          timing: Object.assign({}, existing.timing || {}, freshTile.timing || {}),
          events: Object.assign({}, existing.events || {}, freshTile.events || {})
        });
        // Re-apply face tap + routeId from fresh tile so URL navigation is not stale.
        if (Array.isArray(out[compId].faces) && Array.isArray(freshTile.faces)) {
          out[compId].faces = out[compId].faces.map(function (face, fi) {
            var freshFace = freshTile.faces[fi] || {};
            var next = Object.assign({}, face, {
              events: freshFace.events || face.events,
              route: freshFace.route != null ? freshFace.route : face.route,
              openIn: freshFace.openIn != null ? freshFace.openIn : face.openIn
            });
            if (Object.prototype.hasOwnProperty.call(freshFace, 'routeId')) {
              if (freshFace.routeId) next.routeId = freshFace.routeId;
              else delete next.routeId;
            }
            return next;
          });
        }
      } else if (existing.id) {
        var rebuiltRotation = null;
        var rebuiltTiming = null;
        var v2Rotate = global.AhpSchemaV2 || {};
        if (typeof v2Rotate.buildCardRotationBlock === 'function') {
          var themeForRotate = (layout && layout.theme) || {};
          rebuiltRotation = v2Rotate.buildCardRotationBlock(card, index, themeForRotate, {}, cards.length);
          var faceSecRebuild = (card.faceSec != null && card.faceSec !== '' && !isNaN(parseFloat(card.faceSec)))
            ? Math.max(2, Math.min(8, parseFloat(card.faceSec)))
            : null;
          rebuiltTiming = {
            faceSec: faceSecRebuild,
            cycleDurationMs: rebuiltRotation.durationMs,
            usesGlobalDefault: faceSecRebuild == null,
            startDelayMs: card.startDelayMs != null && card.startDelayMs !== '' ? Number(card.startDelayMs) : null
          };
        }
        out[compId] = Object.assign({}, existing, {
          sortOrder: index,
          presetId: 'home-tile',
          gridArea: compId,
          layout: Object.assign({}, existing.layout || {}, { columnSpan: colSpan, rowSpan: rowSpan }),
          dimensions: Object.assign({}, existing.dimensions || {}, {
            columnSpan: colSpan,
            rowSpan: rowSpan,
            borderRadius: borderRadius
          }),
          appearance: Object.assign({}, existing.appearance || {}, {
            backgroundColor: card.color || card.bgColor || (existing.appearance || {}).backgroundColor,
            borderRadius: borderRadius
          }),
          rotation: rebuiltRotation || existing.rotation,
          timing: Object.assign({}, existing.timing || {}, rebuiltTiming || {})
        });
      }

      for (var fi = 0; fi < 4; fi++) {
        ['-page', '-header', '-summary'].forEach(function (suffix) {
          var faceCompId = cardFaceRouteIdFromCard(card, fi) + suffix;
          if (freshComponents[faceCompId]) {
            out[faceCompId] = freshComponents[faceCompId];
          }
        });
      }
    });

    Object.keys(out).forEach(function (id) {
      if (!out[id] || out[id].presetId !== 'home-tile') return;
      var cardId = id.replace(/-card$/, '');
      if (!cardIdSet[cardId]) delete out[id];
    });

    if (out['home-grid']) {
      out['home-grid'] = Object.assign({}, out['home-grid'], {
        children: expectedChildIds.filter(function (id) { return out[id]; })
      });
    }

    return out;
  }

  function upgradeComponents(components, layout, cfg, freshComponents) {
    var out = Object.assign({}, components || {});
    freshComponents = freshComponents || components || {};
    var theme = (layout && layout.theme) || {};
    cfg = cfg || {};
    var v2 = global.AhpSchemaV2 || {};
    var gridGap = v2.resolveGridGap ? v2.resolveGridGap(theme) : 12;
    var homeGridColumns = v2.resolveHomeGridColumns
      ? v2.resolveHomeGridColumns(theme)
      : (theme.gridColumns != null && !isNaN(Number(theme.gridColumns))
        ? Math.max(1, Math.min(12, Number(theme.gridColumns)))
        : 3);
    var homeGridRowHeight = v2.resolveHomeGridRowHeight
      ? v2.resolveHomeGridRowHeight(theme)
      : (theme.gridRowHeight != null && !isNaN(Number(theme.gridRowHeight))
        ? Math.max(40, Math.min(160, Number(theme.gridRowHeight)))
        : 96);
    var cardsById = {};
    (layout.cards || []).forEach(function (card) {
      if (card && card.id) cardsById[card.id] = card;
    });

    if (out['home-header']) {
      out['home-header'] = v2.buildHomeHeaderComponent
        ? v2.buildHomeHeaderComponent(layout, cfg, theme, {})
        : out['home-header'];
      if (out['home-header']) out['home-header'].type = 'homeHeader';
    }

    if (out['welcome-banner'] && v2.buildHomeHeaderComponent) {
      var freshWelcome = (function () {
        var profile = v2.mergeLayoutProfile ? v2.mergeLayoutProfile(layout, cfg) : {};
        var avatarUrl = v2.getProfileAvatarUrl ? v2.getProfileAvatarUrl(profile, cfg) : '';
        var welcome = layout.welcome || {};
        return Object.assign({}, out['welcome-banner'], {
          visibleWhen: exprAnd([
            exprEquals(stateRef('session.authenticated'), true),
            exprEquals(stateRef('preferences.showWelcome'), true)
          ]),
          content: {
            greeting: i18n('home.welcome'),
            supportingText: {
              '$state': 'session.user.email',
              fallback: (profile && profile.email) || (layout.welcome && layout.welcome.email) || ''
            },
            caption: i18n('home.manageEcosystem')
          },
          avatar: Object.assign({}, out['welcome-banner'].avatar || {}, {
            initials: v2.getProfileInitials ? v2.getProfileInitials(profile, cfg) : '',
            imageUrl: avatarUrl,
            imageUrlBinding: avatarUrl ? {
              '$bind': 'session.user.avatarUrl',
              fallback: avatarUrl
            } : undefined
          })
        });
      })();
      out['welcome-banner'] = freshWelcome;
    } else if (out['welcome-banner']) {
      out['welcome-banner'] = Object.assign({}, out['welcome-banner'], {
        visibleWhen: exprAnd([
          exprEquals(stateRef('session.authenticated'), true),
          exprEquals(stateRef('preferences.showWelcome'), true)
        ])
      });
    }

    if (out['alerts-summary']) {
      var alerts = (layout && layout.alerts) || {};
      var alertDate = (function () {
        var d = new Date();
        var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
      })();
      var newUsers = alerts.newUsers != null ? Number(alerts.newUsers) : 0;
      out['alerts-summary'] = Object.assign({}, out['alerts-summary'], {
        visibleWhen: exprAnd([
          exprEquals(stateRef('session.authenticated'), true),
          exprEquals(stateRef('preferences.showAlerts'), true)
        ]),
        enabledWhen: exprEquals(stateRef('session.authenticated'), true),
        iconToken: 'alerts',
        content: Object.assign({}, (out['alerts-summary'].content || {}), {
          title: i18n('home.liveAlerts'),
          newUsers: bind('dashboard.alerts.newUsers', newUsers),
          meta: alertDate + ' · ' + newUsers + ' new users',
          pill: alerts.showBadge === false ? null : 'now',
          badge: alerts.badgeText || (out['alerts-summary'].content && out['alerts-summary'].content.badge) || 'real-time'
        }),
        appearance: Object.assign({}, (out['alerts-summary'].appearance || {}), {
          iconToken: 'alerts',
          showIcon: true,
          showMeta: true,
          showPill: alerts.showBadge !== false
        })
      });
    }

    if (out['home-grid']) {
      var layoutCardIds = resolveLayoutCardsForSchema(layout).map(function (card) {
        return card.id + '-card';
      }).filter(function (id) { return out[id]; });
      var homeTileIds = layoutCardIds.length ? layoutCardIds : Object.keys(out).filter(function (id) {
        return out[id] && out[id].presetId === 'home-tile';
      }).sort(function (a, b) {
        var sa = out[a].sortOrder != null ? out[a].sortOrder : 999;
        var sb = out[b].sortOrder != null ? out[b].sortOrder : 999;
        return sa - sb;
      });
      var gridLayouts = v2.buildResponsiveGridLayouts
        ? v2.buildResponsiveGridLayouts(theme)
        : (out['home-grid'].layouts || {});
      out['home-grid'] = Object.assign({}, out['home-grid'], {
        type: 'responsiveGrid',
        style: 'metroMosaic',
        children: homeTileIds.length ? homeTileIds : (out['home-grid'].children || []),
        layout: Object.assign({}, gridLayouts.compact || out['home-grid'].layout || {}, {
          type: 'metroGrid',
          columns: (gridLayouts.compact && gridLayouts.compact.columns) || 4,
          gap: (v2.resolveGridGap ? v2.resolveGridGap(theme) : 8),
          independentScroll: false,
          sizeFormula: 'slotWidth'
        }),
        layouts: gridLayouts,
        overflowPolicy: 'reflow',
        preserveChildOrder: true,
        clampChildColumnSpan: true
      });
    }

    if (out['devices-grid']) {
      var gridBinding = Object.assign({}, out['devices-grid'].childrenBinding || {}, {
        '$bind': 'devices.items',
        itemTemplateId: (out['devices-grid'].childrenBinding && out['devices-grid'].childrenBinding.itemTemplateId) || 'device-card-template',
        itemIdPath: (out['devices-grid'].childrenBinding && out['devices-grid'].childrenBinding.itemIdPath) || 'id'
      });
      delete gridBinding.fallback;
      var deviceGridLayouts = v2.buildResponsiveGridLayouts
        ? v2.buildResponsiveGridLayouts(theme)
        : (out['devices-grid'].layouts || {});
      out['devices-grid'] = Object.assign({}, out['devices-grid'], {
        type: 'responsiveGrid',
        layout: Object.assign({}, deviceGridLayouts.compact || {}, { independentScroll: false }),
        layouts: deviceGridLayouts,
        overflowPolicy: 'reflow',
        preserveChildOrder: true,
        clampChildColumnSpan: true,
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
        persistAcrossRoutes: true,
        position: 'fixed',
        outsideScroll: true,
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
      var responsiveLayout = v2.buildCardResponsiveLayout
        ? v2.buildCardResponsiveLayout(src || { colSpan: col, rowSpan: row }, theme)
        : cardResponsiveLayout(src || { colSpan: col, rowSpan: row }, theme);
      var enrich = {
        responsiveLayout: responsiveLayout,
        overflowPolicy: 'clip'
      };
      if (src) {
        enrich.cardType = src.type || c.cardType || 'link';
        enrich.layout = Object.assign({}, c.layout || {}, { columnSpan: col, rowSpan: row });
        enrich.dimensions = Object.assign({}, c.dimensions || {}, {
          columnSpan: col,
          rowSpan: row,
          borderRadius: src.borderRadius != null && src.borderRadius !== ''
            ? (Number(src.borderRadius) < 0 || !isFinite(Number(src.borderRadius)) ? 0 : Number(src.borderRadius))
            : ((c.dimensions && c.dimensions.borderRadius != null && c.dimensions.borderRadius !== '')
              ? (Number(c.dimensions.borderRadius) < 0 || !isFinite(Number(c.dimensions.borderRadius)) ? 0 : Number(c.dimensions.borderRadius))
              : 0),
          minRowHeightDp: homeGridRowHeight,
          sizeFormula: 'slotWidth'
        });
        enrich.appearance = Object.assign({}, c.appearance || {}, {
          borderRadius: src.borderRadius != null && src.borderRadius !== ''
            ? (Number(src.borderRadius) < 0 || !isFinite(Number(src.borderRadius)) ? 0 : Number(src.borderRadius))
            : ((c.appearance && c.appearance.borderRadius != null && c.appearance.borderRadius !== '')
              ? (Number(c.appearance.borderRadius) < 0 || !isFinite(Number(c.appearance.borderRadius)) ? 0 : Number(c.appearance.borderRadius))
              : 0)
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
        enrich.gridArea = id;
      } else {
        enrich.responsiveLayout = responsiveLayout;
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

    // Always publish full profile children (never leave the V2 pageHeader stub).
    if (v2.applyProfilePageComponents) {
      v2.applyProfilePageComponents(out, layout, cfg, theme, {});
    }
    var profileAppearance = (out['profile-header'] && out['profile-header'].appearance)
      || (v2.buildProfileAppearanceContract ? v2.buildProfileAppearanceContract(theme) : { pagePadding: 16, pageGap: 12 });
    var themeModeFallback = (theme.bgStyle === 'dark') ? 'dark' : 'light';
    delete out['profile-page-header'];

    var profile = (global.AhpSchemaV2 && global.AhpSchemaV2.mergeLayoutProfile)
      ? global.AhpSchemaV2.mergeLayoutProfile(layout, cfg)
      : Object.assign({}, (layout && layout.profilePage) || {}, (cfg && cfg.loginUserProfile) || {});
    var privacyText = String(profile.privacyPolicyText || profile.privacyPolicy || '').trim()
      || 'We collect account details (name, email, phone) to provide IoE device control, Grid networking, and support.\n\n'
        + 'Your data is used to authenticate sessions, sync preferences (notifications, appearance), and improve reliability.\n\n'
        + 'You can update profile details and notification preferences any time from this Profile tab.\n\n'
        + 'For questions about security or data controls, contact support@efactor.tech.';
    var subPad = profileAppearance.pagePadding || 16;
    var subGap = profileAppearance.pageGap || 12;
    var navChromeBottom = (typeof v2.NAV_CHROME_BOTTOM === 'number' && v2.NAV_CHROME_BOTTOM > 0)
      ? v2.NAV_CHROME_BOTTOM
      : 120;
    var makeNavSafeLayout = (typeof v2.withNavSafeScrollLayout === 'function')
      ? v2.withNavSafeScrollLayout
      : function (layout) {
          layout = layout || {};
          var padding = layout.padding;
          if (typeof padding === 'number') {
            padding = {
              left: padding,
              right: padding,
              top: padding,
              bottom: Math.max(padding, navChromeBottom)
            };
          } else if (padding && typeof padding === 'object') {
            padding = Object.assign({}, padding, {
              bottom: Math.max(Number(padding.bottom) || 0, navChromeBottom)
            });
          } else {
            padding = { left: 10, right: 10, top: 8, bottom: navChromeBottom };
          }
          return Object.assign({}, layout, {
            padding: padding,
            reservedBottomInset: navChromeBottom,
            contentAvoidsBottomNavigation: true,
            overflowX: 'hidden',
            overflowY: 'auto',
            clipHorizontalOverflow: true
          });
        };
    var subLayout = makeNavSafeLayout({
      safeArea: true,
      padding: { left: subPad, right: subPad, top: subPad, bottom: Math.max(subPad + 24, navChromeBottom + 24) },
      gap: subGap
    });

    // Edit profile — live account values; host opens editor, values stay binding-driven.
    out['profile-edit-page'] = {
      id: 'profile-edit-page',
      type: 'scrollPage',
      layout: subLayout,
      children: ['profile-edit-header', 'profile-edit-menu']
    };
    out['profile-edit-header'] = {
      id: 'profile-edit-header',
      type: 'pageHeader',
      title: i18n('profile.edit'),
      subtitle: i18n('profile.editSubtitle')
    };
    out['profile-edit-menu'] = {
      id: 'profile-edit-menu',
      type: 'profileMenu',
      appearance: profileAppearance,
      items: [
        {
          id: 'edit-name',
          iconToken: 'profile',
          label: 'Name',
          subtitle: profile.name || '',
          subtitleBinding: { '$state': 'session.user.displayName', fallback: profile.name || '' },
          trailingIconToken: 'chevron-right',
          actionId: 'profile-edit-open'
        },
        {
          id: 'edit-email',
          iconToken: 'alerts',
          label: 'Email',
          subtitle: profile.email || '',
          subtitleBinding: { '$state': 'session.user.email', fallback: profile.email || '' },
          trailingIconToken: 'chevron-right',
          actionId: 'profile-edit-open'
        },
        {
          id: 'edit-phone',
          iconToken: 'profile',
          label: 'Phone',
          subtitle: profile.phone || '',
          subtitleBinding: { '$state': 'session.user.phone', fallback: profile.phone || '' },
          trailingIconToken: 'chevron-right',
          actionId: 'profile-edit-open'
        },
        {
          id: 'edit-member',
          iconToken: 'shield',
          label: 'Member ID',
          subtitle: profile.memberId || '',
          subtitleBinding: { '$state': 'session.user.memberId', fallback: profile.memberId || '' },
          trailingIconToken: 'chevron-right',
          actionId: 'profile-edit-open'
        }
      ]
    };

    // Notifications — toggle enabled/disabled dynamically.
    out['profile-notifications-page'] = {
      id: 'profile-notifications-page',
      type: 'scrollPage',
      layout: subLayout,
      children: ['profile-notifications-header', 'profile-notifications-menu']
    };
    out['profile-notifications-header'] = {
      id: 'profile-notifications-header',
      type: 'pageHeader',
      title: i18n('profile.notifications'),
      subtitle: i18n('profile.notificationsSubtitle')
    };
    out['profile-notifications-menu'] = {
      id: 'profile-notifications-menu',
      type: 'profileMenu',
      appearance: profileAppearance,
      items: [
        {
          id: 'notifications-on',
          iconToken: 'alerts',
          label: 'Enable alerts',
          subtitle: 'Alerts and reminders enabled',
          trailingIconToken: 'chevron-right',
          actionId: 'profile-notifications-enable',
          selectedWhen: { equals: [{ '$state': 'pages.profile.notificationsEnabled' }, true] }
        },
        {
          id: 'notifications-off',
          iconToken: 'alerts',
          label: 'Disable alerts',
          subtitle: 'Alerts and reminders disabled',
          trailingIconToken: 'chevron-right',
          actionId: 'profile-notifications-disable',
          selectedWhen: { equals: [{ '$state': 'pages.profile.notificationsEnabled' }, false] }
        }
      ]
    };

    // Appearance — light/dark selector (dynamic via global.themeMode).
    out['profile-appearance-page'] = {
      id: 'profile-appearance-page',
      type: 'scrollPage',
      layout: subLayout,
      children: ['profile-appearance-header', 'profile-appearance-selector']
    };
    out['profile-appearance-header'] = {
      id: 'profile-appearance-header',
      type: 'pageHeader',
      title: i18n('profile.appearance'),
      subtitle: dynamicThemeModeSubtitle(themeModeFallback)
    };
    out['profile-appearance-selector'] = {
      id: 'profile-appearance-selector',
      type: 'themeModeSelector',
      selectedModeBinding: { '$state': 'global.themeMode' },
      selectedPresetBinding: { '$state': 'global.themePreset' },
      appearance: profileAppearance,
      options: [
        {
          id: 'appearance-light',
          preset: 'light',
          mode: 'light',
          label: i18n('appearance.lightMode'),
          subtitle: i18n('appearance.lightModeHint'),
          actionId: 'apply-theme-light'
        },
        {
          id: 'appearance-dark',
          preset: 'dark',
          mode: 'dark',
          label: i18n('appearance.darkMode'),
          subtitle: i18n('appearance.darkModeHint'),
          actionId: 'apply-theme-dark'
        }
      ]
    };

    // Privacy policy — readable dynamic content from profilePage.privacyPolicyText.
    out['profile-privacy-page'] = {
      id: 'profile-privacy-page',
      type: 'scrollPage',
      layout: subLayout,
      children: ['profile-privacy-header', 'profile-privacy-body']
    };
    out['profile-privacy-header'] = {
      id: 'profile-privacy-header',
      type: 'pageHeader',
      title: i18n('profile.privacy'),
      subtitle: i18n('profile.privacySubtitle')
    };
    out['profile-privacy-body'] = {
      id: 'profile-privacy-body',
      type: 'emptyState',
      iconToken: 'shield',
      title: 'Privacy & data controls',
      subtitle: privacyText
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

    reconcileHomeGridComponents(out, layout, cfg, freshComponents);
    if (v2.applyStaticHomeUiToComponents) {
      v2.applyStaticHomeUiToComponents(out, layout, cfg, theme);
    }
    return out;
  }

  function enrichNavigationItems(items) {
    return (items || []).map(function (item) {
      var routeId = item.routeId || item.id;
      var key = 'navigation.' + (routeId || 'home');
      var customLabel = (item && typeof item.label === 'string') ? item.label.trim() : '';
      return Object.assign({}, item, {
        label: customLabel || i18n(key)
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
      'request', 'show-bottom-sheet', 'show-dialog', 'show-snackbar', 'external-navigate', 'host-action', 'open-url'
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
      'profileHeader', 'profileMenu', 'actionButton', 'themeModeSelector', 'staticHomeUi',
      // Emitted as 'application-cube-3d'; unknownComponentPolicy is 'reject', so the
      // type has to be allowlisted or the renderer drops the cube.
      'AppCube3D'
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
    var staticUiActive = !!(layout.staticUiTemplate || (layout.theme && layout.theme.staticUiTemplate));
    if (staticUiActive) {
      themePreset = (layout.theme && layout.theme.bgStyle) || 'light';
    }
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

    var components = upgradeComponents(base.components || {}, layout, cfg, base.components || {});
    var actionRegistry = enrichActions(base.actionRegistry || {}, cfg, layout);
    var routes = enrichRoutes(base.routes || {});

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
    Object.keys(pages).forEach(function (id) {
      if (id === 'login' || !pages[id]) return;
      pages[id] = Object.assign({}, pages[id], {
        usesAppShell: true,
        keepBottomNavigation: true,
        bottomNavigationVisible: true,
        hideBottomNavigation: false,
        bottomNavigationComponentId: 'main-bottom-navigation',
        chrome: 'appShell'
      });
    });

    var navItems = (global.AhpSchemaV2 && global.AhpSchemaV2.filterValidNavigationItems)
      ? global.AhpSchemaV2.filterValidNavigationItems(
        enrichNavigationItems((base.navigation && base.navigation.items) || []),
        actionRegistry,
        routes,
        pages
      )
      : enrichNavigationItems((base.navigation && base.navigation.items) || []);

    var dataSources = {
      dashboard: (global.AhpSchemaV2 && global.AhpSchemaV2.buildDashboardDataSource)
        ? global.AhpSchemaV2.buildDashboardDataSource(cfg)
        : (base.dataSources && base.dataSources.dashboard) || {},
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

    actionRegistry['navigate-devices'] = { type: 'host-action', actionId: 'open-iot' };
    actionRegistry['open-iot-app'] = { type: 'host-action', actionId: 'open-iot' };

    var assets = ensureSchemaAssets(base.assets || {}, layout, cfg);
    var allowedAssetDomains = buildAllowedAssetDomains(assets, cfg);
    var mergedProfile = (global.AhpSchemaV2 && global.AhpSchemaV2.mergeLayoutProfile)
      ? global.AhpSchemaV2.mergeLayoutProfile(layout, cfg)
      : Object.assign({}, (layout && layout.profilePage) || {}, (cfg && cfg.loginUserProfile) || {});

    return sanitizePublishedSchema({
      '$schema': 'https://schemas.efactor.app/sdui/app-v3.schema.json',
      schemaVersion: '3.0.0',
      schemaType: 'server-driven-mobile-app',
      schemaId: 'efactor://app-home/' + userId,
      revision: 3,
      generatedAt: ts.utc,
      generatedAtIst: ts.local,
      // Flat aliases for hosts that still read top-level profile (nested pages/components remain canonical).
      profilePage: mergedProfile,
      profile: {
        pageId: 'profile',
        routeId: 'profile',
        rootComponentId: 'profile-page',
        data: mergedProfile
      },
      signature: {
        algorithm: 'Ed25519',
        keyId: 'efactor-ui-key-2026-01',
        value: null,
        pendingServerSignature: true
      },

      meta: Object.assign({}, base.meta || {}, {
        schemaProfile: 'homepage-canonical-v3',
        title: (layout.header && layout.header.title) || 'Welcome to Efactor!',
        description: (layout.header && layout.header.subtitle) || 'Smart ecosystem dashboard',
        defaultRouteId: 'home',
        defaultLocale: ts.locale,
        timezone: ts.timezone,
        generatedAtIst: ts.local,
        generatedAtUtc: ts.utc,
        themePreset: themePreset,
        themeMode: themePreset === 'light' ? 'light' : 'dark',
        uiMode: (base.meta && base.meta.uiMode) || (staticUiActive ? 'staticHomeUi' : 'cubeHome'),
        staticUiTemplate: layout.staticUiTemplate || (layout.theme && layout.theme.staticUiTemplate) || (base.meta && base.meta.staticUiTemplate) || null,
        staticUi: (base.meta && base.meta.staticUi) || null,
        layoutSnapshot: global.AhpSchemaV2.buildLayoutSnapshot
          ? global.AhpSchemaV2.buildLayoutSnapshot(layout, cfg)
          : ((base.meta && base.meta.layoutSnapshot) || null)
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
      responsive: buildResponsive(layout.theme || {}),
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
        activeModeBinding: stateRef('global.themeMode'),
        serverControlled: true,
        userSelectable: false,
        appearance: buildThemeAppearanceBlock(layout.theme || {}),
        presets: buildThemePresetsCatalog(layout.theme || {})
      },
      iconRegistry: base.iconRegistry || {},
      assets: assets,

      componentDefinitions: buildComponentDefinitions(),
      componentPresets: base.componentPresets || {},
      components: components,

      models: buildModels(),
      dataSources: dataSources,
      api: (global.AhpSchemaV2 && global.AhpSchemaV2.buildApiContract)
        ? global.AhpSchemaV2.buildApiContract(cfg)
        : (base.api || {}),

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
        persistent: true,
        persistsAcrossRoutes: true,
        persistOnRouteChange: true,
        replaceOnNestedSchema: false,
        bottomNavigationOutsidePageScroll: true,
        bottomNavigationFixed: true,
        bottomNavigationPosition: 'fixed',
        bottomNavigationOverlay: false,
        contentAvoidsBottomNavigation: true,
        reservedBottomInset: 108,
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
        allowedAssetDomains: allowedAssetDomains
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
    });
  }

  global.AhpSchemaV3 = {
    build: function (layout, cfg, helpers) {
      return sanitizePublishedSchema(buildSchema(layout, cfg, helpers));
    },
    sanitizePublishedSchema: sanitizePublishedSchema,
    reconcileHomeGridComponents: reconcileHomeGridComponents,
    resolveLayoutCardsForSchema: resolveLayoutCardsForSchema,
    resolveDefaultIotApiUrl: resolveDefaultIotApiUrl,
    resolveGridApiUrl: function (cfg) {
      return (global.AhpSchemaV2 && global.AhpSchemaV2.resolveGridApiUrl)
        ? global.AhpSchemaV2.resolveGridApiUrl(cfg)
        : '';
    },
    getDefaultGridPageContent: function () {
      return (global.AhpSchemaV2 && global.AhpSchemaV2.getDefaultGridPageContent)
        ? global.AhpSchemaV2.getDefaultGridPageContent()
        : {};
    },
    resolveGridPageContent: function (layout) {
      return (global.AhpSchemaV2 && global.AhpSchemaV2.resolveGridPageContent)
        ? global.AhpSchemaV2.resolveGridPageContent(layout)
        : {};
    },
    extractGridPageSchema: function (fullSchema, layout, cfg) {
      return (global.AhpSchemaV2 && global.AhpSchemaV2.extractGridPageSchema)
        ? global.AhpSchemaV2.extractGridPageSchema(fullSchema, layout, cfg)
        : {};
    },
    buildGridPageSchema: function (layout, cfg, helpers) {
      var full = sanitizePublishedSchema(buildSchema(layout, cfg, helpers));
      return (global.AhpSchemaV2 && global.AhpSchemaV2.extractGridPageSchema)
        ? global.AhpSchemaV2.extractGridPageSchema(full, layout, cfg)
        : {};
    },
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
    var cubeEnriched = cube.enabled && cardCount === 0;
    // The cube component is emitted by the base builder regardless of enrichment, so its
    // definition must be published whenever the component exists or the renderer rejects it.
    if (!cubeEnriched && !(schema.components && schema.components['application-cube-3d'])) {
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
    if (!cubeEnriched) {
      return schema;
    }
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
    return (global.AhpSchemaV3 && global.AhpSchemaV3.sanitizePublishedSchema)
      ? global.AhpSchemaV3.sanitizePublishedSchema(schema)
      : schema;
  };
})(typeof window !== 'undefined' ? window : globalThis);

/* Canonical contract validation — every template must publish a schema the default
   Home renderer can already draw, so drift is caught at generation time. */
(function (global) {
  'use strict';
  if (!global.AhpSchemaV3 || typeof global.AhpSchemaV3.build !== 'function') return;
  var baseBuild = global.AhpSchemaV3.build;

  function validateCanonicalContract(schema) {
    var errors = [];
    var warnings = [];
    var allowed = (schema.security && schema.security.allowedComponentTypes) || [];
    var defsByType = {};
    Object.keys(schema.componentDefinitions || {}).forEach(function (name) {
      var def = schema.componentDefinitions[name] || {};
      var type = def.componentType || name.charAt(0).toLowerCase() + name.slice(1);
      defsByType[type] = def;
    });

    var components = schema.components || {};
    Object.keys(components).forEach(function (id) {
      var component = components[id];
      if (!component || !component.type) return;
      if (allowed.length && allowed.indexOf(component.type) === -1) {
        errors.push(id + ': component type "' + component.type + '" is not in security.allowedComponentTypes.');
      }
      var def = defsByType[component.type];
      if (!def) {
        errors.push(id + ': component type "' + component.type + '" has no componentDefinitions entry.');
        return;
      }
      // Most components carry props at the top level; AppCube3D nests them under `props`.
      var nested = component.props || {};
      (def.requiredProps || []).forEach(function (prop) {
        var present = component[prop] !== undefined && component[prop] !== null;
        var presentNested = nested[prop] !== undefined && nested[prop] !== null;
        if (!present && !presentNested) {
          errors.push(id + ': missing required prop "' + prop + '" for ' + component.type + '.');
        }
      });
    });

    // Navigation must use the canonical destination contract, not template-local names.
    ((schema.navigation && schema.navigation.items) || []).forEach(function (item) {
      ['routeId', 'actionId', 'iconToken'].forEach(function (prop) {
        if (!item[prop]) {
          errors.push('navigation item "' + (item.id || '?') + '" is missing canonical "' + prop + '".');
        }
      });
      if (item.routeId && !(schema.routes || {})[item.routeId]) {
        errors.push('navigation item "' + (item.id || '?') + '" routeId "' + item.routeId + '" has no route.');
      }
      if (item.actionId && !(schema.actionRegistry || {})[item.actionId]) {
        errors.push('navigation item "' + (item.id || '?') + '" actionId "' + item.actionId + '" is unregistered.');
      }
    });

    // Sections that no existing component can draw are renderer requirements, not errors.
    var requiresNewComponent = [];
    Object.keys(components).forEach(function (id) {
      var component = components[id];
      if (!component || component.type !== 'staticHomeUi') return;
      var req = component.rendererRequirements || {};
      (req.requiresNewComponent || []).forEach(function (type) {
        if (requiresNewComponent.indexOf(type) === -1) requiresNewComponent.push(type);
      });
      (component.sections || []).forEach(function (section) {
        if (section && section.type && !section.canonicalComponent && requiresNewComponent.indexOf(section.type) === -1) {
          requiresNewComponent.push(section.type);
        }
      });
    });
    requiresNewComponent.forEach(function (type) {
      warnings.push('section "' + type + '" has no canonical component and needs renderer support.');
    });

    return {
      contractVersion: '1.0',
      valid: errors.length === 0,
      errors: errors,
      warnings: warnings,
      requiresNewComponent: requiresNewComponent
    };
  }

  global.AhpSchemaV3.validateCanonicalContract = validateCanonicalContract;
  global.AhpSchemaV3.build = function (layout, cfg, helpers) {
    var schema = baseBuild(layout, cfg, helpers);
    schema.meta = Object.assign({}, schema.meta || {});
    schema.meta.contractValidation = validateCanonicalContract(schema);
    return schema;
  };
})(typeof window !== 'undefined' ? window : globalThis);