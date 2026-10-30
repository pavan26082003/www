(function () {
  'use strict';

  var cfg = window.AHP_CONFIG || {};
  var layout = JSON.parse(JSON.stringify(cfg.layout || {}));
  var editMode = false;
  var activeMenuKey = 'layout';
  var selectedCardId = null;
  var selectedPageCardId = null;
  var sortable = null;
  var deviceSortable = null;
  var facePreviewSortable = null;
  var cardAnimOffsets = {};
  var deviceAnimOffsets = {};

  var ZOOM_MIN = 0.35;
  var ZOOM_MAX = 2.5;
  var ZOOM_STEP = 0.12;
  var previewZoom = { autoFit: true, manualScale: 0.8 };
  var activeFaceIndex = 0;
  var slotActionsBound = false;
  var deviceSlotActionsBound = false;
  var cardPickerBound = false;
  var cardEditorCardId = null;
  var selectedDeviceId = null;
  var activeDeviceFaceIndex = 0;
  var deviceEditorDeviceId = null;
  var deviceEditorBound = false;
  var navClickBound = false;

  /* Bento grid slot order — card index maps to visual position */
  var GRID_AREAS = [
    'user-modules', 'grid', 'hrms', 'id-card', 'appid',
    'leads', 'recipe', 'efactor-grid', 'add-device'
  ];
  /* Present studio mosaic — default for new users and Reset */
  var STUDIO_CARD_POSITIONS = {
    'user-modules': { col: 1, row: 1, colSpan: 1, rowSpan: 2 },
    'grid': { col: 2, row: 1, colSpan: 1, rowSpan: 1 },
    'appid': { col: 3, row: 1, colSpan: 1, rowSpan: 1 },
    'recipe': { col: 2, row: 2, colSpan: 2, rowSpan: 1 },
    'leads': { col: 1, row: 3, colSpan: 1, rowSpan: 1 },
    'id-card': { col: 2, row: 3, colSpan: 1, rowSpan: 1 },
    'hrms': { col: 3, row: 3, colSpan: 1, rowSpan: 1 },
    'efactor-grid': { col: 1, row: 4, colSpan: 3, rowSpan: 1 },
    'wifi': { col: 1, row: 5, colSpan: 1, rowSpan: 2 },
    'biryani-banner': { col: 2, row: 5, colSpan: 2, rowSpan: 2 }
  };
  var selectedNavEditId = null;

  function restoreStudioMosaicPositions() {
    // Never overwrite a layout the user already saved — that undoes Block size and placement on refresh.
    if (hasSavedLayout) return false;
    if (!layout.cards || !layout.cards.length) return false;
    var byId = {};
    layout.cards.forEach(function (card) {
      if (card && card.id) byId[card.id] = card;
    });
    var present = 0;
    var mismatched = 0;
    Object.keys(STUDIO_CARD_POSITIONS).forEach(function (id) {
      var card = byId[id];
      if (!card) return;
      present++;
      var pos = STUDIO_CARD_POSITIONS[id];
      if (
        Number(card.col) !== pos.col ||
        Number(card.row) !== pos.row ||
        Number(card.colSpan || 1) !== pos.colSpan ||
        Number(card.rowSpan || 1) !== pos.rowSpan
      ) {
        mismatched++;
      }
    });
    if (present < 5 || mismatched === 0) return false;
    layout.cards.forEach(function (card) {
      if (!card || !card.id) return;
      var pos = STUDIO_CARD_POSITIONS[card.id];
      if (!pos) return;
      card.col = pos.col;
      card.row = pos.row;
      card.colSpan = pos.colSpan;
      card.rowSpan = pos.rowSpan;
      if (card.id === 'user-modules') card.tall = true;
    });
    if ((getTheme().gridRows || 0) < 6) {
      getTheme().gridRows = 6;
    }
    assignGridAreas();
    return true;
  }

  /* Use single quotes in CSS stacks — double quotes break HTML style="..." attributes. */
  var FONT_FAMILIES = [
    { id: 'system', label: 'System Default', css: "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" },
    { id: 'inter', label: 'Inter', css: 'Inter, system-ui, sans-serif' },
    { id: 'roboto', label: 'Roboto', css: 'Roboto, Arial, sans-serif' },
    { id: 'poppins', label: 'Poppins', css: 'Poppins, sans-serif' },
    { id: 'montserrat', label: 'Montserrat', css: 'Montserrat, sans-serif' },
    { id: 'open-sans', label: 'Open Sans', css: "'Open Sans', sans-serif" },
    { id: 'lato', label: 'Lato', css: 'Lato, sans-serif' },
    { id: 'nunito', label: 'Nunito', css: 'Nunito, sans-serif' },
    { id: 'merriweather', label: 'Merriweather (Serif)', css: 'Merriweather, Georgia, serif' },
    { id: 'mono', label: 'Monospace', css: 'ui-monospace, SFMono-Regular, Menlo, Consolas, monospace' }
  ];

  /* Common Font Awesome icons for picker (show visuals, not just class text) */
  var QUICK_ICONS = [
    'fa-home', 'fa-th', 'fa-users', 'fa-user', 'fa-cog', 'fa-bell', 'fa-star', 'fa-heart',
    'fa-bolt', 'fa-cloud', 'fa-key', 'fa-link', 'fa-plus', 'fa-chart-bar', 'fa-chart-line', 'fa-mobile-alt'
  ];
  var ICON_LIBRARY = [
    'fa-home', 'fa-th', 'fa-th-large', 'fa-border-all', 'fa-users', 'fa-user', 'fa-user-tie', 'fa-user-friends',
    'fa-cog', 'fa-cogs', 'fa-sliders-h', 'fa-bell', 'fa-bell-slash', 'fa-star', 'fa-heart', 'fa-bolt',
    'fa-cloud', 'fa-wifi', 'fa-bluetooth-b', 'fa-key', 'fa-lock', 'fa-unlock', 'fa-link', 'fa-unlink',
    'fa-plus', 'fa-minus', 'fa-check', 'fa-times', 'fa-edit', 'fa-trash', 'fa-save', 'fa-sync',
    'fa-chart-bar', 'fa-chart-line', 'fa-chart-pie', 'fa-chart-area', 'fa-mobile-alt', 'fa-desktop', 'fa-laptop', 'fa-tablet-alt',
    'fa-fingerprint', 'fa-id-card', 'fa-qrcode', 'fa-barcode', 'fa-shield-alt', 'fa-lock-open', 'fa-eye', 'fa-eye-slash',
    'fa-calendar', 'fa-clock', 'fa-hourglass-half', 'fa-history', 'fa-map-marker-alt', 'fa-compass', 'fa-globe', 'fa-building',
    'fa-briefcase', 'fa-money-bill', 'fa-credit-card', 'fa-shopping-cart', 'fa-box', 'fa-truck', 'fa-warehouse', 'fa-industry',
    'fa-image', 'fa-images', 'fa-camera', 'fa-video', 'fa-music', 'fa-file', 'fa-file-alt', 'fa-folder',
    'fa-envelope', 'fa-comment', 'fa-comments', 'fa-phone', 'fa-sms', 'fa-paper-plane', 'fa-share-alt', 'fa-search',
    'fa-filter', 'fa-sort', 'fa-list', 'fa-clipboard-list', 'fa-tasks', 'fa-project-diagram', 'fa-sitemap', 'fa-network-wired',
    'fa-database', 'fa-server', 'fa-microchip', 'fa-memory', 'fa-robot', 'fa-puzzle-piece', 'fa-cube', 'fa-cubes',
    'fa-layer-group', 'fa-crosshairs', 'fa-bullseye', 'fa-fire', 'fa-leaf', 'fa-sun', 'fa-moon', 'fa-lightbulb'
  ];

  var BG_PRESETS = {
    dark: {
      phoneBg: '#0b0f1a',
      surfaceColor: '#151b2e',
      alertBg: '#151b2e',
      textPrimary: '#ffffff',
      textSecondary: '#e2e8f0',
      textMuted: '#64748b',
      accentColor: '#7c3aed',
      accentGreen: '#22c55e',
      welcomeStart: '#7c3aed',
      welcomeEnd: '#4f46e5',
      welcomeTextColor: '#ffffff',
      navActiveColor: '#3b82f6',
      navBg: '#000000'
    },
    light: {
      phoneBg: '#f3f4f6',
      surfaceColor: '#ffffff',
      alertBg: '#ffffff',
      textPrimary: '#111827',
      textSecondary: '#374151',
      textMuted: '#6b7280',
      accentColor: '#111827',
      accentGreen: '#22c55e',
      welcomeStart: '#fbbf24',
      welcomeEnd: '#f97316',
      welcomeTextColor: '#111827',
      navActiveColor: '#111827',
      navBg: '#ffffff'
    },
    midnight: {
      phoneBg: '#070b14',
      surfaceColor: '#0f1520',
      alertBg: '#0f1520',
      textPrimary: '#f1f5f9',
      textSecondary: '#cbd5e1',
      textMuted: '#64748b',
      accentColor: '#6366f1',
      accentGreen: '#22c55e',
      welcomeStart: '#4338ca',
      welcomeEnd: '#312e81',
      welcomeTextColor: '#ffffff',
      navActiveColor: '#818cf8',
      navBg: '#030712'
    },
    slate: {
      phoneBg: '#1e293b',
      surfaceColor: '#334155',
      alertBg: '#334155',
      textPrimary: '#f8fafc',
      textSecondary: '#e2e8f0',
      textMuted: '#94a3b8',
      accentColor: '#38bdf8',
      accentGreen: '#22c55e',
      welcomeStart: '#0284c7',
      welcomeEnd: '#0369a1',
      welcomeTextColor: '#ffffff',
      navActiveColor: '#38bdf8',
      navBg: '#0f172a'
    },
    ocean: {
      phoneBg: '#0c1929',
      surfaceColor: '#132337',
      alertBg: '#132337',
      textPrimary: '#ecfeff',
      textSecondary: '#cffafe',
      textMuted: '#67e8f9',
      accentColor: '#06b6d4',
      accentGreen: '#22c55e',
      welcomeStart: '#0891b2',
      welcomeEnd: '#0e7490',
      welcomeTextColor: '#ffffff',
      navActiveColor: '#22d3ee',
      navBg: '#041018'
    },
    purple: {
      phoneBg: '#1a1025',
      surfaceColor: '#251535',
      alertBg: '#251535',
      textPrimary: '#faf5ff',
      textSecondary: '#ede9fe',
      textMuted: '#a78bfa',
      accentColor: '#a855f7',
      accentGreen: '#22c55e',
      welcomeStart: '#9333ea',
      welcomeEnd: '#7e22ce',
      welcomeTextColor: '#ffffff',
      navActiveColor: '#c084fc',
      navBg: '#0f0818'
    },
    forest: {
      phoneBg: '#0f1a14',
      surfaceColor: '#162820',
      alertBg: '#162820',
      textPrimary: '#ecfdf5',
      textSecondary: '#d1fae5',
      textMuted: '#6ee7b7',
      accentColor: '#10b981',
      accentGreen: '#34d399',
      welcomeStart: '#059669',
      welcomeEnd: '#047857',
      welcomeTextColor: '#ffffff',
      navActiveColor: '#34d399',
      navBg: '#071510'
    },
    rose: {
      phoneBg: '#1a1014',
      surfaceColor: '#261820',
      alertBg: '#261820',
      textPrimary: '#fff1f2',
      textSecondary: '#ffe4e6',
      textMuted: '#fda4af',
      accentColor: '#f43f5e',
      accentGreen: '#fb7185',
      welcomeStart: '#e11d48',
      welcomeEnd: '#be123c',
      welcomeTextColor: '#ffffff',
      navActiveColor: '#fb7185',
      navBg: '#120a0d'
    },
    aurora: {
      phoneBg: '#07131f',
      surfaceColor: '#102132',
      alertBg: '#102132',
      textPrimary: '#f8fafc',
      textSecondary: '#dbeafe',
      textMuted: '#93c5fd',
      accentColor: '#22d3ee',
      accentGreen: '#2dd4bf',
      welcomeStart: '#14b8a6',
      welcomeEnd: '#8b5cf6',
      welcomeTextColor: '#ffffff',
      navActiveColor: '#67e8f9',
      navBg: '#06111c'
    },
    ember: {
      phoneBg: '#17120d',
      surfaceColor: '#281a11',
      alertBg: '#281a11',
      textPrimary: '#fff7ed',
      textSecondary: '#fed7aa',
      textMuted: '#fdba74',
      accentColor: '#f97316',
      accentGreen: '#fb923c',
      welcomeStart: '#ea580c',
      welcomeEnd: '#dc2626',
      welcomeTextColor: '#ffffff',
      navActiveColor: '#fb923c',
      navBg: '#100905'
    },
    graphite: {
      phoneBg: '#111827',
      surfaceColor: '#1f2937',
      alertBg: '#1f2937',
      textPrimary: '#f9fafb',
      textSecondary: '#d1d5db',
      textMuted: '#9ca3af',
      accentColor: '#14b8a6',
      accentGreen: '#2dd4bf',
      welcomeStart: '#334155',
      welcomeEnd: '#0f766e',
      welcomeTextColor: '#ffffff',
      navActiveColor: '#2dd4bf',
      navBg: '#030712'
    },
    glass: {
      phoneBg: '#0a1420',
      surfaceColor: '#132235',
      alertBg: '#132235',
      textPrimary: '#f8fafc',
      textSecondary: '#e0f2fe',
      textMuted: '#7dd3fc',
      accentColor: '#38bdf8',
      accentGreen: '#22d3ee',
      welcomeStart: '#06b6d4',
      welcomeEnd: '#2563eb',
      welcomeTextColor: '#ffffff',
      navActiveColor: '#7dd3fc',
      navBg: '#07111f'
    }
  };

  var THEME_META = {
    light: { label: 'Light SmartHome', note: 'Clean white surfaces', swatches: ['#f3f4f6', '#ffffff', '#111827', '#fbbf24'] },
    dark: { label: 'Dark', note: 'Deep purple accents', swatches: ['#0b0f1a', '#151b2e', '#7c3aed', '#3b82f6'] },
    midnight: { label: 'Midnight', note: 'Indigo night mode', swatches: ['#070b14', '#0f1520', '#6366f1', '#818cf8'] },
    slate: { label: 'Slate', note: 'Cool gray-blue', swatches: ['#1e293b', '#334155', '#38bdf8', '#0f172a'] },
    ocean: { label: 'Ocean', note: 'Cyan deep sea', swatches: ['#0c1929', '#132337', '#06b6d4', '#22d3ee'] },
    purple: { label: 'Purple', note: 'Violet glow', swatches: ['#1a1025', '#251535', '#a855f7', '#c084fc'] },
    forest: { label: 'Forest', note: 'Emerald nature', swatches: ['#0f1a14', '#162820', '#10b981', '#34d399'] },
    rose: { label: 'Rose', note: 'Warm pink tones', swatches: ['#1a1014', '#261820', '#f43f5e', '#fb7185'] },
    aurora: { label: 'Aurora', note: 'Teal to violet', swatches: ['#07131f', '#102132', '#14b8a6', '#8b5cf6'] },
    ember: { label: 'Ember', note: 'Orange fire', swatches: ['#17120d', '#281a11', '#f97316', '#dc2626'] },
    graphite: { label: 'Graphite', note: 'Neutral dark', swatches: ['#111827', '#1f2937', '#14b8a6', '#2dd4bf'] },
    glass: { label: 'Glass', note: 'Frosted blue', swatches: ['#0a1420', '#132235', '#38bdf8', '#2563eb'] }
  };

  /* Home page layout templates (Efactor-branded). Apply via Settings → Templates. */
  var HOME_TEMPLATE_IMAGES = {
    food: 'https://images.unsplash.com/photo-1563379091339-03246963d96a?w=600&h=400&fit=crop',
    fashion: 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=400&h=500&fit=crop',
    grocery: 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&h=400&fit=crop',
    home: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=400&fit=crop',
    beauty: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=400&fit=crop',
    delivery: 'https://images.unsplash.com/photo-1526367790999-0150786686a2?w=400&h=400&fit=crop',
    gadget: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=400&fit=crop',
    lifestyle: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=500&fit=crop'
  };

  // Distinct secondary-face accents (purple / navy / teal / green) so rotation is visible.
  var TEMPLATE_FACE_COLORS = ['#7c3aed', '#1e3a8a', '#0d9488', '#16a34a'];
  var TEMPLATE_FACE_ICONS = ['fa-cube', 'fa-star', 'fa-bolt', 'fa-heart'];

  function buildTemplateCard(spec) {
    var type = spec.type || 'link';
    var color = spec.color || '#334155';
    var icon = spec.icon || 'fa-cube';
    var title = spec.title || 'Efactor';
    var subtitle = spec.subtitle || '';
    var faces = (spec.faces || []).slice(0, 4);
    // Never clone the same title/subtitle/image onto every side — that makes the cube look static.
    while (faces.length < 4) {
      var fi = faces.length;
      var faceColor = fi === 0 ? color : (TEMPLATE_FACE_COLORS[fi] || color);
      var faceIcon = fi === 0 ? icon : (TEMPLATE_FACE_ICONS[fi] || 'fa-cube');
      if (fi === 0) {
        if (type === 'stat') {
          faces.push({ value: spec.value || '0', label: title, icon: icon, color: color, textColor: '#ffffff', iconColor: '#ffffff' });
        } else if (type === 'image') {
          faces.push({
            title: title,
            subtitle: subtitle,
            icon: icon,
            color: color,
            image: spec.image || '',
            textColor: spec.textColor || '#ffffff',
            iconColor: '#ffffff'
          });
        } else {
          faces.push({ title: title, subtitle: subtitle, icon: icon, color: color, textColor: '#ffffff', iconColor: '#ffffff' });
        }
      } else if (type === 'stat') {
        faces.push({ title: 'Face ' + (fi + 1), icon: faceIcon, color: faceColor, textColor: '#ffffff', iconColor: '#ffffff' });
      } else if (type === 'image') {
        faces.push({ title: 'Face ' + (fi + 1), icon: faceIcon, color: faceColor, textColor: '#ffffff', iconColor: '#ffffff' });
      } else {
        faces.push({ title: 'Face ' + (fi + 1), subtitle: '', icon: faceIcon, color: faceColor, textColor: '#ffffff', iconColor: '#ffffff' });
      }
    }
    return {
      id: spec.id,
      type: type,
      gridArea: spec.id,
      title: title,
      label: title,
      subtitle: subtitle,
      value: spec.value,
      icon: icon,
      color: color,
      textColor: spec.textColor || '#ffffff',
      image: spec.image || '',
      colSpan: Math.max(1, Math.min(12, parseInt(spec.colSpan, 10) || 1)),
      rowSpan: Math.max(1, Math.min(16, parseInt(spec.rowSpan, 10) || 1)),
      borderRadius: spec.borderRadius != null ? Number(spec.borderRadius) : 18,
      defaultFace: 0,
      rotationDir: spec.rotationDir || 'rtl',
      sameFaceColor: false,
      faces: faces
    };
  }

  function defaultTemplateBottomNav() {
    return [
      { id: 'home', icon: 'fa-home', label: 'Home', active: true },
      { id: 'devices', icon: 'fa-th', label: 'IoT', active: false },
      { id: 'grid', icon: 'fa-border-all', label: 'Grid', active: false },
      { id: 'profile', icon: 'fa-user', label: 'Profile', active: false }
    ];
  }

  var HOME_TEMPLATES = {
    'efactor-deals': {
      id: 'efactor-deals',
      label: 'Efactor Deals',
      note: 'Promo banner + 2-column lifestyle grid',
      swatches: ['#f7f5ff', '#ffffff', '#6d28d9', '#ef4444'],
      header: { title: 'Efactor Deals', subtitle: 'Offers near you' },
      welcome: { greeting: 'Efactor picks for you', email: '', caption: 'Save more every day' },
      alerts: { count: 3, newUsers: 0, badgeText: 'hot', showBadge: true },
      theme: {
        homeTemplate: 'efactor-deals',
        bgStyle: 'light',
        phoneBg: '#f7f5ff',
        surfaceColor: '#ffffff',
        alertBg: '#ffffff',
        textPrimary: '#111827',
        textSecondary: '#374151',
        textMuted: '#6b7280',
        accentColor: '#6d28d9',
        accentGreen: '#16a34a',
        welcomeStart: '#fde68a',
        welcomeEnd: '#f9a8d4',
        welcomeTextColor: '#111827',
        navActiveColor: '#6d28d9',
        navBg: '#ffffff',
        gridColumns: 2,
        gridRows: 4,
        gridGap: 12,
        gridRowHeight: 110,
        cardBorderRadius: 20,
        cardSpeed: 16,
        showWelcome: true,
        showAlerts: false,
        showBottomNav: true
      },
      cards: [
        { id: 'ef-promo', type: 'image', title: 'Biryani at ₹99', subtitle: 'Order now', icon: 'fa-utensils', color: '#fef3c7', image: HOME_TEMPLATE_IMAGES.food, colSpan: 2, rowSpan: 2, borderRadius: 22 },
        { id: 'ef-fashion', type: 'link', title: 'Fashion', subtitle: 'Going out', icon: 'fa-shoe-prints', color: '#ec4899', colSpan: 1, rowSpan: 2 },
        { id: 'ef-food', type: 'link', title: 'Food Delivery', subtitle: 'Get 85% off', icon: 'fa-motorcycle', color: '#f97316', colSpan: 1, rowSpan: 2 },
        { id: 'ef-dining', type: 'link', title: 'Budget Dining', subtitle: 'Near you', icon: 'fa-utensils', color: '#14b8a6', colSpan: 1, rowSpan: 1 },
        { id: 'ef-vouchers', type: 'link', title: 'Online Vouchers', subtitle: 'Efactor Pay', icon: 'fa-gift', color: '#6366f1', colSpan: 1, rowSpan: 1 },
        { id: 'ef-fine', type: 'link', title: 'Fine Dining', subtitle: 'Reserve', icon: 'fa-wine-glass', color: '#0f766e', colSpan: 1, rowSpan: 1 },
        { id: 'ef-shop101', type: 'link', title: '₹101 Shop', subtitle: 'Magic deals', icon: 'fa-store', color: '#7c3aed', colSpan: 1, rowSpan: 1 }
      ]
    },
    'efactor-quickmart': {
      id: 'efactor-quickmart',
      label: 'Efactor QuickMart',
      note: 'Fast delivery + category + buy-again rail',
      swatches: ['#ecfeff', '#ffffff', '#0891b2', '#22c55e'],
      header: { title: 'eFactor · IoE', subtitle: 'Smart insights & tools' },
      welcome: { greeting: 'Delivering in 8 min', email: '', caption: 'Fresh picks for your home' },
      alerts: { count: 0, newUsers: 0, badgeText: 'live', showBadge: false },
      theme: {
        homeTemplate: 'efactor-quickmart',
        bgStyle: 'light',
        phoneBg: '#ecfeff',
        surfaceColor: '#ffffff',
        alertBg: '#ffffff',
        textPrimary: '#0f172a',
        textSecondary: '#334155',
        textMuted: '#64748b',
        accentColor: '#0891b2',
        accentGreen: '#16a34a',
        welcomeStart: '#22d3ee',
        welcomeEnd: '#2563eb',
        welcomeTextColor: '#ffffff',
        navActiveColor: '#0891b2',
        navBg: '#ffffff',
        gridColumns: 3,
        gridRows: 5,
        gridGap: 10,
        gridRowHeight: 96,
        cardBorderRadius: 16,
        cardSpeed: 16,
        showWelcome: true,
        showAlerts: false,
        showBottomNav: true
      },
      cards: [
        { id: 'ef-qm-poshak', type: 'image', title: 'Festive Picks', subtitle: 'From ₹159', icon: 'fa-star', color: '#cffafe', image: HOME_TEMPLATE_IMAGES.lifestyle, colSpan: 1, rowSpan: 2 },
        { id: 'ef-qm-prasad', type: 'link', title: 'Prasad Corner', subtitle: 'Ready packs', icon: 'fa-box', color: '#0ea5e9', colSpan: 1, rowSpan: 1 },
        { id: 'ef-qm-kids', type: 'link', title: 'Kids Corner', subtitle: 'Costumes', icon: 'fa-child', color: '#f59e0b', colSpan: 1, rowSpan: 1 },
        { id: 'ef-qm-fasting', type: 'link', title: 'Fasting Essentials', subtitle: 'Stock up', icon: 'fa-leaf', color: '#10b981', colSpan: 1, rowSpan: 1 },
        { id: 'ef-qm-decor', type: 'link', title: 'Home Decor', subtitle: 'Temple ready', icon: 'fa-spa', color: '#8b5cf6', colSpan: 1, rowSpan: 1 },
        { id: 'ef-qm-buy1', type: 'image', title: 'Previously bought', subtitle: 'Add again', icon: 'fa-rotate', color: '#ffffff', image: HOME_TEMPLATE_IMAGES.beauty, colSpan: 1, rowSpan: 2, textColor: '#0f172a' },
        { id: 'ef-qm-buy2', type: 'image', title: 'Daily needs', subtitle: '20% OFF', icon: 'fa-shopping-basket', color: '#ffffff', image: HOME_TEMPLATE_IMAGES.grocery, colSpan: 1, rowSpan: 2, textColor: '#0f172a' },
        { id: 'ef-qm-buy3', type: 'stat', title: 'Fresh deals', value: '₹5', subtitle: 'Pick any 2', icon: 'fa-bolt', color: '#eab308', colSpan: 1, rowSpan: 2 }
      ]
    },
    'efactor-market': {
      id: 'efactor-market',
      label: 'Efactor Market',
      note: 'Search-style feed + specials + 2-col grid',
      swatches: ['#ffffff', '#f3f4f6', '#111827', '#16a34a'],
      header: { title: 'Efactor Market', subtitle: 'Search or ask anything' },
      welcome: { greeting: 'Welcome to Efactor', email: '', caption: 'Shop everyday essentials' },
      alerts: { count: 2, newUsers: 0, badgeText: 'new', showBadge: true },
      theme: {
        homeTemplate: 'efactor-market',
        bgStyle: 'light',
        phoneBg: '#ffffff',
        surfaceColor: '#f8fafc',
        alertBg: '#f1f5f9',
        textPrimary: '#0f172a',
        textSecondary: '#334155',
        textMuted: '#64748b',
        accentColor: '#111827',
        accentGreen: '#16a34a',
        welcomeStart: '#22c55e',
        welcomeEnd: '#0ea5e9',
        welcomeTextColor: '#ffffff',
        navActiveColor: '#111827',
        navBg: '#ffffff',
        gridColumns: 4,
        gridRows: 6,
        gridGap: 10,
        gridRowHeight: 88,
        cardBorderRadius: 14,
        cardSpeed: 16,
        showWelcome: false,
        showAlerts: true,
        showBottomNav: true
      },
      cards: [
        { id: 'ef-m-prime', type: 'link', title: 'Prime', subtitle: 'Members', icon: 'fa-crown', color: '#0ea5e9', colSpan: 1, rowSpan: 2, col: 1, row: 1 },
        { id: 'ef-m-live', type: 'link', title: 'Shop Live', subtitle: 'Watch', icon: 'fa-video', color: '#ef4444', colSpan: 1, rowSpan: 1, col: 2, row: 1 },
        { id: 'ef-m-deals', type: 'link', title: 'Deals', subtitle: 'Today', icon: 'fa-percent', color: '#ec4899', colSpan: 1, rowSpan: 1, col: 3, row: 1 },
        { id: 'ef-m-mobile', type: 'link', title: 'Mobiles', subtitle: 'Phones', icon: 'fa-mobile-alt', color: '#6366f1', colSpan: 1, rowSpan: 1, col: 4, row: 1 },
        { id: 'ef-m-elec', type: 'link', title: 'Electronics', subtitle: 'Gadgets', icon: 'fa-laptop', color: '#14b8a6', colSpan: 1, rowSpan: 1, col: 2, row: 2 },
        { id: 'ef-m-rewards', type: 'link', title: 'Rewards', subtitle: 'Points', icon: 'fa-coins', color: '#f59e0b', colSpan: 1, rowSpan: 1, col: 3, row: 2 },
        { id: 'ef-m-bed', type: 'image', title: 'From ₹299', subtitle: 'Bedsheets', icon: 'fa-bed', color: '#1e3a8a', image: HOME_TEMPLATE_IMAGES.home, colSpan: 2, rowSpan: 2, col: 1, row: 3 },
        { id: 'ef-m-home', type: 'link', title: 'Home', subtitle: 'Living', icon: 'fa-home', color: '#8b5cf6', colSpan: 1, rowSpan: 1, col: 3, row: 3 },
        { id: 'ef-m-special', type: 'image', title: 'Festival Specials', subtitle: 'Shop now', icon: 'fa-gifts', color: '#14532d', image: HOME_TEMPLATE_IMAGES.home, colSpan: 2, rowSpan: 2, col: 1, row: 5 },
        { id: 'ef-m-daily', type: 'link', title: 'Daily Needs', subtitle: 'Essentials', icon: 'fa-shopping-cart', color: '#22c55e', colSpan: 1, rowSpan: 2, col: 3, row: 5 }
      ]
    },
    'efactor-shop': {
      id: 'efactor-shop',
      label: 'Efactor Shop',
      note: 'Bold orange header + 3-column super savers',
      swatches: ['#ff5722', '#ffd600', '#e91e63', '#ffffff'],
      header: { title: 'Efactor Shop', subtitle: 'Search on Efactor' },
      welcome: { greeting: 'Daily Wear Sarees', email: '', caption: 'From ₹189 · Shop now' },
      alerts: { count: 3, newUsers: 0, badgeText: 'cart', showBadge: true },
      theme: {
        homeTemplate: 'efactor-shop',
        bgStyle: 'light',
        phoneBg: '#ffd600',
        surfaceColor: '#ffffff',
        alertBg: '#ffffff',
        textPrimary: '#111827',
        textSecondary: '#374151',
        textMuted: '#6b7280',
        accentColor: '#ff5722',
        accentGreen: '#16a34a',
        welcomeStart: '#2563eb',
        welcomeEnd: '#1d4ed8',
        welcomeTextColor: '#ffffff',
        navActiveColor: '#ff5722',
        navBg: '#ffffff',
        gridColumns: 3,
        gridRows: 4,
        gridGap: 8,
        gridRowHeight: 108,
        cardBorderRadius: 16,
        cardSpeed: 14,
        showWelcome: true,
        showAlerts: false,
        showBottomNav: true
      },
      cards: [
        { id: 'ef-s-hero', type: 'image', title: 'Daily Wear', subtitle: 'From ₹189', icon: 'fa-shirt', color: '#1d4ed8', image: HOME_TEMPLATE_IMAGES.fashion, colSpan: 3, rowSpan: 2 },
        { id: 'ef-s-1', type: 'link', title: 'Bedsheets', subtitle: 'From ₹149', icon: 'fa-bed', color: '#db2777', colSpan: 1, rowSpan: 2 },
        { id: 'ef-s-2', type: 'link', title: 'Kurtis', subtitle: 'From ₹199', icon: 'fa-female', color: '#7c3aed', colSpan: 1, rowSpan: 2 },
        { id: 'ef-s-3', type: 'link', title: 'Home Appliances', subtitle: 'From ₹499', icon: 'fa-blender', color: '#0d9488', colSpan: 1, rowSpan: 2 },
        { id: 'ef-s-4', type: 'stat', title: 'Super Saver', value: '70%', subtitle: 'OFF', icon: 'fa-bolt', color: '#e11d48', colSpan: 1, rowSpan: 1 },
        { id: 'ef-s-5', type: 'link', title: 'Play & Earn', subtitle: 'Win coins', icon: 'fa-gamepad', color: '#2563eb', colSpan: 2, rowSpan: 1 }
      ]
    },
    'efactor-style': {
      id: 'efactor-style',
      label: 'Efactor Style',
      note: 'Fashion lookbook + category rail + product grid',
      swatches: ['#ffffff', '#fdf2f8', '#db2777', '#111827'],
      header: { title: 'Efactor Style', subtitle: 'Deliver to your door' },
      welcome: { greeting: 'Lowest price in 30 days', email: '', caption: 'Curated brands on Efactor' },
      alerts: { count: 1, newUsers: 0, badgeText: 'sale', showBadge: true },
      theme: {
        homeTemplate: 'efactor-style',
        bgStyle: 'light',
        phoneBg: '#ffffff',
        surfaceColor: '#ffffff',
        alertBg: '#fdf2f8',
        textPrimary: '#111827',
        textSecondary: '#374151',
        textMuted: '#6b7280',
        accentColor: '#db2777',
        accentGreen: '#16a34a',
        welcomeStart: '#f472b6',
        welcomeEnd: '#db2777',
        welcomeTextColor: '#ffffff',
        navActiveColor: '#db2777',
        navBg: '#ffffff',
        gridColumns: 2,
        gridRows: 5,
        gridGap: 12,
        gridRowHeight: 120,
        cardBorderRadius: 18,
        cardSpeed: 16,
        showWelcome: true,
        showAlerts: false,
        showBottomNav: true
      },
      cards: [
        { id: 'ef-st-fashion', type: 'link', title: 'Fashion', subtitle: 'Selected', icon: 'fa-shirt', color: '#db2777', colSpan: 1, rowSpan: 1 },
        { id: 'ef-st-beauty', type: 'link', title: 'Beauty', subtitle: 'Glow', icon: 'fa-spa', color: '#ec4899', colSpan: 1, rowSpan: 1 },
        { id: 'ef-st-look1', type: 'image', title: 'Min 35% off', subtitle: 'Efactor Edit', icon: 'fa-tags', color: '#111827', image: HOME_TEMPLATE_IMAGES.fashion, colSpan: 1, rowSpan: 3 },
        { id: 'ef-st-look2', type: 'image', title: 'New drop', subtitle: 'Streetwear', icon: 'fa-fire', color: '#1f2937', image: HOME_TEMPLATE_IMAGES.lifestyle, colSpan: 1, rowSpan: 3 },
        { id: 'ef-st-p1', type: 'image', title: 'Accessories', subtitle: 'Efactor Luxe', icon: 'fa-gem', color: '#ffffff', image: HOME_TEMPLATE_IMAGES.beauty, colSpan: 1, rowSpan: 2, textColor: '#111827' },
        { id: 'ef-st-p2', type: 'image', title: 'Loungewear', subtitle: 'Everyday soft', icon: 'fa-shirt', color: '#ffffff', image: HOME_TEMPLATE_IMAGES.fashion, colSpan: 1, rowSpan: 2, textColor: '#111827' }
      ]
    }
  };

  var el = {
    viewport: document.getElementById('ahpViewport'),
    btnSetting: document.getElementById('ahpBtnSetting'),
    btnUndo: document.getElementById('ahpBtnUndo'),
    btnRedo: document.getElementById('ahpBtnRedo'),
    btnDone: document.getElementById('ahpBtnDone'),
    btnSave: document.getElementById('ahpBtnSave'),
    btnSchema: document.getElementById('ahpBtnSchema'),
    btnStudioTheme: document.getElementById('ahpBtnStudioTheme'),
    shell: document.getElementById('ahpShell'),
    btnBack: document.getElementById('ahpBtnBack'),
    btnZoomIn: document.getElementById('ahpZoomIn'),
    btnZoomOut: document.getElementById('ahpZoomOut'),
    btnZoomFit: document.getElementById('ahpZoomFit'),
    zoomLabel: document.getElementById('ahpZoomLabel'),
    previewMount: document.getElementById('ahpPreviewMount'),
    previewFrame: document.getElementById('ahpPreviewFrame'),
    phone: document.getElementById('ahpPhone'),
    grid: document.getElementById('ahpGrid'),
    toast: document.getElementById('ahpToast'),
    cardPicker: document.getElementById('ahpCardSelect'),
    pageCardPicker: null,
    schemaApiUrlInput: document.getElementById('ahpSchemaApiUrl'),
    schemaOutput: document.getElementById('ahpSchemaOutput'),
    btnCopySchema: document.getElementById('ahpCopySchemaBtn'),
    btnCopyApiUrl: document.getElementById('ahpCopyApiUrlBtn'),
    menuItems: document.querySelectorAll('.ahp-menu-item'),
    toolSections: document.querySelectorAll('.ahp-tool-section')
  };

  function cleanText(s) {
    return String(s == null ? '' : s)
      .replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, '')
      .replace(/[\u0080-\u00FF]{2,}/g, '')
      .replace(/Ã‚Â·/g, ' - ')
      .replace(/Ãƒâ€”/g, 'x')
      .replace(/Ã¢â€ â€™/g, '->')
      .replace(/Ã¢â‚¬Â¦/g, '...')
      .replace(/ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â/g, '-')
      .replace(/ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¦/g, '...')
      .replace(/Ã‚/g, '')
      .replace(/Ãƒ/g, '')
      .replace(/Ã¢/g, '')
      .replace(/\s+-\s+-\s+/g, ' - ')
      .trim();
  }

  function esc(s) {
    var d = document.createElement('div');
    d.textContent = cleanText(s);
    return d.innerHTML;
  }

  function showToast(msg) {
    if (!el.toast) return;
    el.toast.textContent = cleanText(msg);
    el.toast.classList.add('show');
    setTimeout(function () { el.toast.classList.remove('show'); }, 2500);
  }

  function formatDate() {
    return new Date().toLocaleDateString('en-GB', {
      weekday: 'short', day: 'numeric', month: 'short', year: 'numeric'
    });
  }

  var layoutInitialized = false;
  var hasSavedLayout = !!(cfg.hasSavedLayout || (cfg.settingsMeta && cfg.settingsMeta.has_layout));
  var layoutDirty = false;
  var saveDebounceTimer = null;
  var layoutSaveEpoch = 0;
  var saveInFlight = false;
  var cubeGeometryObserver = null;
  var layoutReady = false;
  var layoutHistory = [];
  var layoutHistoryIndex = -1;
  var layoutHistoryApplying = false;
  var layoutHistorySuppress = 0;
  var layoutHistoryTimer = null;
  var LAYOUT_HISTORY_MAX = 50;
  var LAYOUT_HISTORY_DEBOUNCE_MS = 350;

  function canRecordLayoutHistory() {
    return layoutReady && !layoutHistoryApplying && layoutHistorySuppress <= 0;
  }

  function beginLayoutHistorySuppress() {
    layoutHistorySuppress += 1;
  }

  function endLayoutHistorySuppress() {
    layoutHistorySuppress = Math.max(0, layoutHistorySuppress - 1);
  }

  function cancelPendingLayoutHistory() {
    if (layoutHistoryTimer) {
      clearTimeout(layoutHistoryTimer);
      layoutHistoryTimer = null;
    }
  }

  function cancelPendingAutoSave() {
    if (saveDebounceTimer) {
      clearTimeout(saveDebounceTimer);
      saveDebounceTimer = null;
    }
  }

  function cancelPendingHistoryAndSave() {
    cancelPendingLayoutHistory();
    cancelPendingAutoSave();
  }

  function captureLayoutSnapshot() {
    if (editMode && typeof flushThemeFromTools === 'function') {
      flushThemeFromTools(true);
    }
    return JSON.stringify(layout);
  }

  function resetLayoutHistory(options) {
    options = options || {};
    layoutHistory = [];
    layoutHistoryIndex = -1;
    if (options.skipSeed) {
      updateHistoryButtons();
      return;
    }
    pushLayoutHistorySnapshot(true);
  }

  function pushLayoutHistorySnapshot(force) {
    if (!canRecordLayoutHistory()) return;
    var snap = captureLayoutSnapshot();
    if (layoutHistoryIndex >= 0 && layoutHistory[layoutHistoryIndex] === snap) return;
    if (layoutHistoryIndex >= 0 && layoutHistoryIndex < layoutHistory.length - 1) {
      layoutHistory = layoutHistory.slice(0, layoutHistoryIndex + 1);
    }
    layoutHistory.push(snap);
    if (layoutHistory.length > LAYOUT_HISTORY_MAX) {
      layoutHistory.shift();
    }
    layoutHistoryIndex = layoutHistory.length - 1;
    updateHistoryButtons();
  }

  function scheduleLayoutHistorySnapshot() {
    if (!canRecordLayoutHistory()) return;
    cancelPendingLayoutHistory();
    layoutHistoryTimer = setTimeout(function () {
      layoutHistoryTimer = null;
      pushLayoutHistorySnapshot(false);
    }, LAYOUT_HISTORY_DEBOUNCE_MS);
  }

  function flushLayoutHistoryPending() {
    cancelPendingLayoutHistory();
    if (canRecordLayoutHistory()) {
      pushLayoutHistorySnapshot(false);
    }
  }

  function recordLayoutHistoryNow() {
    if (!canRecordLayoutHistory()) return;
    cancelPendingLayoutHistory();
    pushLayoutHistorySnapshot(true);
  }

  function commitLayoutHistoryBeforeChange() {
    if (!canRecordLayoutHistory()) return;
    cancelPendingLayoutHistory();
    var snap = captureLayoutSnapshot();
    if (layoutHistoryIndex >= 0 && layoutHistory[layoutHistoryIndex] === snap) return;
    pushLayoutHistorySnapshot(true);
  }

  function restoreLayoutSnapshot(snapshot) {
    if (!snapshot) return;
    cancelPendingHistoryAndSave();
    beginLayoutHistorySuppress();
    layoutHistoryApplying = true;
    try {
      var parsed = JSON.parse(snapshot);
      applyLayoutFromServer(parsed, { allowEmptyCards: true });
      layout.activePage = parsed.activePage || layout.activePage || 'home';
      if (layout.activePage === 'settings') layout.activePage = 'profile';
      if (Array.isArray(layout.bottomNav)) {
        layout.bottomNav.forEach(function (item) {
          if (item) item.active = item.id === layout.activePage;
        });
      }
      if (selectedCardId && !(layout.cards || []).some(function (c) { return c && c.id === selectedCardId; })) {
        selectedCardId = null;
        cardEditorCardId = null;
      }
      if (selectedPageCardId && !(layout.cards || []).some(function (c) { return c && c.id === selectedPageCardId; })) {
        selectedPageCardId = null;
      }
      if (selectedDeviceId) {
        var devicesPage = getDevicesPage();
        if (!(devicesPage.devices || []).some(function (d) { return d && d.id === selectedDeviceId; })) {
          selectedDeviceId = null;
        }
      }
      activeFaceIndex = 0;
      cardAnimOffsets = {};
      deviceAnimOffsets = {};
      refreshPreviewAfterSave({ fromHistory: true });
      syncToolFields();
      layoutDirty = true;
    } finally {
      layoutHistoryApplying = false;
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          endLayoutHistorySuppress();
          updateHistoryButtons();
        });
      });
    }
  }

  function undoLayoutChange() {
    if (layoutHistoryIndex <= 0) return;
    cancelPendingHistoryAndSave();
    layoutHistoryIndex -= 1;
    restoreLayoutSnapshot(layoutHistory[layoutHistoryIndex]);
    showToast('Undone');
  }

  function redoLayoutChange() {
    if (layoutHistoryIndex >= layoutHistory.length - 1) return;
    cancelPendingHistoryAndSave();
    layoutHistoryIndex += 1;
    restoreLayoutSnapshot(layoutHistory[layoutHistoryIndex]);
    showToast('Redone');
  }

  function updateHistoryButtons() {
    var canUndo = layoutHistoryIndex > 0;
    var canRedo = layoutHistoryIndex >= 0 && layoutHistoryIndex < layoutHistory.length - 1;
    if (el.btnUndo) el.btnUndo.disabled = !canUndo;
    if (el.btnRedo) el.btnRedo.disabled = !canRedo;
  }

  function ensureLayoutStructure() {
    if (layoutInitialized) {
      if (!layout.header) layout.header = {};
      if (!layout.welcome) layout.welcome = {};
      if (!layout.alerts) layout.alerts = {};
      if (!layout.theme || typeof layout.theme !== 'object') layout.theme = {};
      layout.theme.squareTiles = true;
      syncLayoutThemeFromPublishedSchema();
      normalizeWelcomeBannerPreference();
      normalizeAlertsBannerPreference();
      if (!Array.isArray(layout.cards)) layout.cards = [];
      if (!Array.isArray(layout.bottomNav)) layout.bottomNav = [];
      normalizeBottomNav();
      if (!layout.profilePage) layout.profilePage = {};
      if (layout.staticUiTemplate == null && layout.theme && layout.theme.staticUiTemplate) {
        layout.staticUiTemplate = layout.theme.staticUiTemplate;
      }
      if (layout.staticUiTemplate && layout.theme && !layout.theme.staticUiTemplate) {
        layout.theme.staticUiTemplate = layout.staticUiTemplate;
      }
      return;
    }
    var def = cfg.defaultLayout || {};
    if (!layout.header) layout.header = Object.assign({}, def.header || {});
    if (!layout.welcome) layout.welcome = Object.assign({}, def.welcome || {});
    if (!layout.alerts) layout.alerts = Object.assign({}, def.alerts || {});
    if (!layout.theme) layout.theme = {};
    layout.theme = Object.assign({}, def.theme || {}, layout.theme);
    syncLayoutThemeFromPublishedSchema();
    normalizeWelcomeBannerPreference();
    normalizeAlertsBannerPreference();
    if (layout.theme.gridColumns == null || isNaN(parseInt(layout.theme.gridColumns, 10))) {
      layout.theme.gridColumns = 3;
    }
    if (layout.theme.gridRowHeight == null || isNaN(parseInt(layout.theme.gridRowHeight, 10))) {
      layout.theme.gridRowHeight = 117;
    }
    // Every account: 1x1 cells stay square. Radius slider only changes corner roundness.
    layout.theme.squareTiles = true;
    if (layout.theme.cardBorderRadius == null || layout.theme.cardBorderRadius === '') {
      layout.theme.cardBorderRadius = 16;
    }
    if (layout.theme.gridRows == null || isNaN(parseInt(layout.theme.gridRows, 10))) {
      layout.theme.gridRows = 6;
    }
    if (!layout.theme.homeTemplate) {
      layout.theme.homeTemplate = layout.homeTemplate || 'default';
    }
    if (layout.theme.showBottomNav == null) {
      layout.theme.showBottomNav = true;
    }
    layout.homeTemplate = layout.theme.homeTemplate || layout.homeTemplate || 'default';
    if (layout.staticUiTemplate == null && layout.theme.staticUiTemplate) {
      layout.staticUiTemplate = layout.theme.staticUiTemplate;
    }
    if (layout.staticUiTemplate && !layout.theme.staticUiTemplate) {
      layout.theme.staticUiTemplate = layout.staticUiTemplate;
    }
    if (!Array.isArray(layout.cards)) layout.cards = [];
    if (!layout.bottomNav) layout.bottomNav = JSON.parse(JSON.stringify(def.bottomNav || []));
    normalizeBottomNav();
    if (!layout.profilePage) layout.profilePage = JSON.parse(JSON.stringify(def.profilePage || {}));
    if (!layout.devicesPage) {
      layout.devicesPage = JSON.parse(JSON.stringify(def.devicesPage || getDefaultDevicesPage()));
    } else {
      if (!layout.devicesPage.header) layout.devicesPage.header = Object.assign({}, (def.devicesPage && def.devicesPage.header) || {});
      if (!layout.devicesPage.status) layout.devicesPage.status = Object.assign({}, (def.devicesPage && def.devicesPage.status) || { online: 0, offline: 0 });
    }
    layout.devicesPage.devices = [];
    if (!layout.activePage) layout.activePage = 'home';
    if (layout.activePage === 'settings') layout.activePage = 'profile';
    if (
      layout.cards.length === 0 &&
      Array.isArray(def.cards) &&
      def.cards.length
    ) {
      layout.cards = JSON.parse(JSON.stringify(def.cards));
    }
    ensureSavedCardsStructure();
    normalizeCardOrder();
    assignGridAreas();
    ensureAllCardPositions(false);
    restoreStudioMosaicPositions();
    applyLoginUserProfileToLayout();
    layoutInitialized = true;
  }

  function getTheme() {
    if (!layout.theme || typeof layout.theme !== 'object') {
      layout.theme = {};
    }
    // Migrate legacy wide gutters (10–12) to Metro Start tight gap once.
    if (!layout.theme._gapUserSet) {
      var g = parseInt(layout.theme.gridGap, 10);
      if (isNaN(g) || g >= 10) layout.theme.gridGap = 4;
    }
    return layout.theme;
  }

  function promotePhotoImageFitToCover(card) {
    if (!card || card._imageFitUserSet) return;
    var id = String(card.id || '');
    // Logo-on-solid tiles keep Contain. Photo tiles upgrade legacy Contain → Cover (no blank bars).
    var logoTileIds = { grid: 1, hrms: 1, appid: 1, 'id-card': 1, 'efactor-grid': 1 };
    if (logoTileIds[id]) return;
    var faceTypeHint = normalizeHomeBlockType(card.type);
    if (faceTypeHint !== 'image' && faceTypeHint !== 'mixed' && id !== 'recipe' && id !== 'biryani-banner') return;
    var hasImageFace = false;
    (card.faces || []).forEach(function (face) {
      if (!face) return;
      var ft = String(face.type || card.type || '').toLowerCase();
      if ((ft === 'image' || ft === 'mixed') && String(face.image || (face === (card.faces && card.faces[0]) ? card.image : '') || '').trim()) {
        hasImageFace = true;
      }
    });
    if (!hasImageFace && id !== 'recipe' && id !== 'biryani-banner') return;
    if (!card.imageFit || card.imageFit === 'contain') card.imageFit = 'cover';
    (card.faces || []).forEach(function (face) {
      if (!face) return;
      var ft = String(face.type || card.type || '').toLowerCase();
      if (ft !== 'image' && ft !== 'mixed') return;
      if (!face.imageFit || face.imageFit === 'contain') face.imageFit = 'cover';
    });
  }

  function ensureSavedCardsStructure() {
    if (!Array.isArray(layout.cards)) layout.cards = [];
    layout.cards.forEach(function (card) {
      if (!card || typeof card !== 'object') return;
      ensureFaces(card, { skipLegacyUpgrade: true });
      promotePhotoImageFitToCover(card);
    });
  }

  function repairCardsFromDefaults() {
    ensureSavedCardsStructure();
  }

  /**
   * Third tab is always Grid. Saved per-user targets (alerts, reports, …) are ignored
   * so ahp-bottom-nav matches the schema for every user.
   */
  var NAV_CATEGORY_TARGETS = {
    grid: { icon: 'fa-border-all', label: 'Grid' }
  };

  function resolveNavCategoryTarget() {
    return 'grid';
  }

  /**
   * Keep the fixed 4 tabs (Home, IoT, Grid, Profile) but preserve studio label/icon edits.
   */
  function normalizeBottomNav() {
    var target = resolveNavCategoryTarget();
    var targetMeta = NAV_CATEGORY_TARGETS[target] || NAV_CATEGORY_TARGETS.grid;
    var activeId = String(layout.activePage || 'home').toLowerCase();
    if (activeId === 'settings') activeId = 'profile';
    if (activeId === 'category') activeId = 'grid';

    var byId = {};
    (layout.bottomNav || []).forEach(function (item) {
      if (item && item.id) byId[item.id] = item;
    });

    layout.bottomNavCategoryTarget = target;
    layout.bottomNav = [
      { id: 'home', icon: 'fa-home', label: 'Home' },
      { id: 'devices', icon: 'fa-th', label: 'IoT' },
      { id: target, icon: targetMeta.icon, label: targetMeta.label },
      { id: 'profile', icon: 'fa-user', label: 'Profile' }
    ].map(function (item) {
      var prev = byId[item.id];
      if (prev) {
        if (prev.label != null && String(prev.label).trim() !== '') item.label = String(prev.label).trim();
        if (prev.icon != null && String(prev.icon).trim() !== '') item.icon = normalizeFaIcon(prev.icon);
      }
      return Object.assign({}, item, { active: item.id === activeId });
    });
  }

  function getDefaultDevicesPage() {
    return {
      header: { title: 'devices - iot', subtitle: 'Tap a cube to control.', leftIcon: 'fa-qrcode', rightIcon: 'fa-ellipsis-h' },
      status: { online: 0, offline: 0 },
      devices: []
    };
  }

  var IOT_DEVICE_COLORS = ['#ea580c', '#7c3aed', '#2563eb', '#0d9488', '#334155', '#14b8a6', '#3b82f6', '#84cc16', '#8b5cf6', '#38bdf8', '#f97316'];
  var _iotDevicesPayload = null;
  var _iotDevicesLoading = false;
  var _iotStatusPollTimer = null;
  var IOT_STATUS_POLL_MS = 12000;

  function resolveIotApiBase() {
    var base = String(cfg.iotApiUrl || '').replace(/\/+$/, '');
    if (base) return base;
    if (window.AhpSchemaV3 && typeof window.AhpSchemaV3.resolveDefaultIotApiUrl === 'function') {
      return window.AhpSchemaV3.resolveDefaultIotApiUrl(cfg);
    }
    var origin = (window.location && window.location.origin) ? window.location.origin : '';
    if (/localhost|127\.0\.0\.1/i.test(origin)) {
      return origin + '/iot_api_creation.php';
    }
    return 'https://efactor.tech/v8/iot_api_creation.php';
  }

  function resolveIotAccessToken() {
    return String(cfg.iotAccessToken || '').trim();
  }

  function buildIotApiUrl(action) {
    var base = resolveIotApiBase();
    var url = base + '?action=' + encodeURIComponent(action);
    var token = resolveIotAccessToken();
    if (token) url += '&access_token=' + encodeURIComponent(token);
    return url;
  }

  function iotSchemaProxyUrl() {
    var apiBase = String(cfg.apiBase || '').replace(/\/+$/, '');
    return apiBase ? apiBase + '/iot-schema' : '';
  }

  function iotDevicesProxyUrl() {
    var apiBase = String(cfg.apiBase || '').replace(/\/+$/, '');
    return apiBase ? apiBase + '/iot-devices' : '';
  }

  function fetchIotSchemaPayload() {
    var token = resolveIotAccessToken();
    var headers = { 'Accept': 'application/json' };
    if (token) headers['X-IOT-Access-Token'] = token;

    var directFetch = function () {
      return fetch(buildIotApiUrl('get_iot_schema'), {
        method: 'GET',
        credentials: 'omit',
        headers: headers
      }).then(parseIotApiJson);
    };

    var proxyUrl = iotSchemaProxyUrl();
    if (!proxyUrl) return directFetch();

    return directFetch().catch(function () {
      return fetch(proxyUrl, {
        method: 'GET',
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' }
      }).then(parseIotApiJson);
    });
  }

  function fetchIotDevicesPayload() {
    var proxyUrl = iotDevicesProxyUrl();
    var token = resolveIotAccessToken();
    var headers = { 'Accept': 'application/json' };
    if (token) headers['X-IOT-Access-Token'] = token;

    var directFetch = function () {
      return fetch(buildIotApiUrl('get_iot_devices'), {
        method: 'GET',
        credentials: 'omit',
        headers: headers
      }).then(parseIotApiJson);
    };

    if (!proxyUrl) return directFetch();

    return fetch(proxyUrl, {
      method: 'GET',
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json' }
    })
      .then(parseIotApiJson)
      .catch(function () {
        return directFetch();
      });
  }

  function normalizeIotSchemaPayload(data) {
    if (!data || typeof data !== 'object') return null;
    if (data.schema && typeof data.schema === 'object') return data.schema;
    if (data.success !== false && (data.devices || data.page || data.layout || data.components)) return data;
    return null;
  }

  function resolveIotPayloadFromSchemaResponse(data) {
    if (!data || typeof data !== 'object') return null;
    if (data.success === false) {
      throw new Error(data.message || 'IoT schema API returned an error.');
    }
    var schema = normalizeIotSchemaPayload(data);
    if (!schema) return null;
    var devices = extractIotDevicesFromSchema(schema);
    if (!devices.length && data.schema && Array.isArray(data.schema.devices)) {
      schema = Object.assign({}, schema, { devices: data.schema.devices.slice() });
      devices = schema.devices;
    }
    if (!devices.length && Array.isArray(data.devices)) {
      schema = mergeIotDevicesIntoSchema(schema, { devices: data.devices });
    } else if (devices.length) {
      schema = mergeIotDevicesIntoSchema(schema, { devices: devices });
    }
    return schema;
  }

  function fetchIotDevicesBundle(forceFresh) {
    forceFresh = !!forceFresh;
    return refreshIotAccessTokenFromProfile()
      .then(function (token) {
        if (!token) {
          throw new Error('No IoT access token found for this profile. Save a token in IoT Dashboard first.');
        }
        cfg.iotAccessToken = token;
        return fetchIotSchemaPayload();
      })
      .then(function (schemaData) {
        if (schemaData && schemaData.iotApiUrl) cfg.iotApiUrl = schemaData.iotApiUrl;
        var merged = resolveIotPayloadFromSchemaResponse(schemaData);
        if (!forceFresh && merged && extractIotDevicesFromSchema(merged).length) {
          return merged;
        }
        return fetchIotDevicesPayload()
          .then(function (devicesData) {
            if (devicesData && devicesData.iotApiUrl) cfg.iotApiUrl = devicesData.iotApiUrl;
            var schema = merged || normalizeIotSchemaPayload(schemaData) || {
              page: { header: { title: 'My Devices', eyebrow: 'Smart Home' }, summary: {} },
              layout: { grid: { columns: 2, gap: 12 }, card: { borderRadius: 24, minHeight: 168 } },
              theme: { background: '#ececec' }
            };
            schema = mergeIotDevicesIntoSchema(schema, devicesData);
            return schema;
          });
      })
      .then(function (schema) {
        if (!schema || !extractIotDevicesFromSchema(schema).length) {
          throw new Error('No devices returned from IoT API for this access token.');
        }
        return schema;
      });
  }

  function preloadIotDevices() {
    if (_iotDevicesLoading || _iotDevicesPayload) return;
    _iotDevicesLoading = true;
    fetchIotDevicesBundle()
      .then(function (schema) {
        _iotDevicesPayload = schema;
        if (layout.activePage === 'devices') {
          applyIotDevicesPayload(schema);
        }
      })
      .catch(function () {
        /* silent preload — Devices tab will show error if user opens it */
      })
      .finally(function () {
        _iotDevicesLoading = false;
      });
  }

  function extractDevicesFromApiPayload(data) {
    if (!data || typeof data !== 'object') return [];
    if (Array.isArray(data.devices)) return data.devices;
    if (data.data && Array.isArray(data.data.devices)) return data.data.devices;
    if (data.data && Array.isArray(data.data.items)) return data.data.items;
    return [];
  }

  function mergeIotDevicesIntoSchema(schema, devicesPayload) {
    schema = schema && typeof schema === 'object' ? JSON.parse(JSON.stringify(schema)) : {};
    var devices = extractDevicesFromApiPayload(devicesPayload);
    if (!devices.length) {
      devices = extractIotDevicesFromSchema(schema);
    }
    if (!devices.length) return schema;

    schema.devices = devices;
    var online = devices.filter(function (d) { return d && d.online !== false; }).length;
    var total = devices.length;
    schema.deviceCount = total;
    schema.page = schema.page || {};
    schema.page.summary = Object.assign({}, schema.page.summary || {}, {
      total: total,
      online: online,
      offline: Math.max(0, total - online)
    });
    schema.summary = Object.assign({}, schema.summary || {}, {
      total: total,
      online: online,
      offline: Math.max(0, total - online)
    });
    if (schema.page.header && typeof schema.page.header === 'object') {
      schema.page.header.subtitle = total + ' products · ' + online + ' online';
    } else {
      schema.page.header = {
        eyebrow: 'Smart Home',
        title: 'My Devices',
        subtitle: total + ' products · ' + online + ' online'
      };
    }
    schema.generatedDate = schema.generatedDate || schema.savedDate || new Date().toLocaleDateString('en-IN', { day: 'numeric', month: 'long', year: 'numeric' });
    return schema;
  }

  function refreshIotAccessTokenFromProfile() {
    var apiBase = cfg.apiBase || '';
    if (!apiBase) return Promise.resolve(resolveIotAccessToken());
    return fetch(apiBase + '/iot-access-token', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data && data.access_token) cfg.iotAccessToken = data.access_token;
        if (data && data.iotApiUrl) cfg.iotApiUrl = data.iotApiUrl;
        return resolveIotAccessToken();
      })
      .catch(function () { return resolveIotAccessToken(); });
  }

  function parseIotApiJson(response) {
    return response.text().then(function (text) {
      var data = null;
      try { data = text ? JSON.parse(text) : null; } catch (e) { data = null; }
      if (!data) {
        throw new Error('Invalid JSON from IoT API (HTTP ' + response.status + ').');
      }
      if (data.success === false) {
        throw new Error(data.message || 'IoT API request failed.');
      }
      if (!response.ok) {
        var msg = data.message ? data.message : ('Request failed (' + response.status + ').');
        throw new Error(msg);
      }
      return data;
    });
  }

  function extractIotDevicesFromSchema(schema) {
    if (!schema || typeof schema !== 'object') return [];
    if (Array.isArray(schema.devices) && schema.devices.length) return schema.devices;
    if (schema.data && Array.isArray(schema.data.devices) && schema.data.devices.length) return schema.data.devices;
    if (schema.data && Array.isArray(schema.data.items) && schema.data.items.length) return schema.data.items;
    var page = schema.page || null;
    if (page && Array.isArray(page.devices) && page.devices.length) return page.devices;
    if (page && Array.isArray(page.components)) {
      for (var i = 0; i < page.components.length; i++) {
        var comp = page.components[i];
        if (comp && Array.isArray(comp.items) && comp.items.length) return comp.items;
        if (comp && Array.isArray(comp.devices) && comp.devices.length) return comp.devices;
      }
    }
    if (Array.isArray(schema.components)) {
      for (var j = 0; j < schema.components.length; j++) {
        var sc = schema.components[j];
        if (sc && Array.isArray(sc.items) && sc.items.length) return sc.items;
        if (sc && Array.isArray(sc.devices) && sc.devices.length) return sc.devices;
      }
    }
    if (schema.components && typeof schema.components === 'object') {
      var keys = Object.keys(schema.components);
      for (var k = 0; k < keys.length; k++) {
        var c = schema.components[keys[k]];
        if (c && Array.isArray(c.items) && c.items.length) return c.items;
        if (c && Array.isArray(c.devices) && c.devices.length) return c.devices;
      }
    }
    return [];
  }

  function extractIotSummaryFromSchema(schema, devices) {
    var summary = (schema && schema.page && schema.page.summary)
      || (schema && schema.meta && schema.meta.summary)
      || (schema && schema.summary)
      || null;
    if (summary) {
      return {
        online: summary.online != null ? summary.online : 0,
        offline: summary.offline != null ? summary.offline : 0
      };
    }
    devices = devices || [];
    var online = devices.filter(function (d) { return d.online !== false; }).length;
    return { online: online, offline: Math.max(0, devices.length - online) };
  }

  function iotIconForDevice(d) {
    var hint = String(d.device_type || d.devicetype || d.product_name || d.productName || d.name || '').toLowerCase();
    if (hint.indexOf('power') >= 0 || hint.indexOf('bolt') >= 0 || hint.indexOf('energy') >= 0) return 'fa-bolt';
    if (hint.indexOf('humid') >= 0 || hint.indexOf('tint') >= 0 || hint.indexOf('moist') >= 0) return 'fa-tint';
    if (hint.indexOf('camera') >= 0 || hint.indexOf('video') >= 0) return 'fa-video';
    if (hint.indexOf('lock') >= 0) return 'fa-lock';
    if (hint.indexOf('motion') >= 0 || hint.indexOf('sensor') >= 0 || hint.indexOf('running') >= 0) return 'fa-running';
    if (hint.indexOf('plug') >= 0 || hint.indexOf('socket') >= 0) return 'fa-plug';
    if (hint.indexOf('water') >= 0 || hint.indexOf('pump') >= 0) return 'fa-water';
    if (hint.indexOf('solar') >= 0) return 'fa-solar-panel';
    if (hint.indexOf('air') >= 0 || hint.indexOf('purifier') >= 0 || hint.indexOf('wind') >= 0) return 'fa-wind';
    if (hint.indexOf('door') >= 0 || hint.indexOf('garage') >= 0) return 'fa-door-open';
    if (hint.indexOf('fan') >= 0) return 'fa-fan';
    if (hint.indexOf('light') >= 0 || hint.indexOf('lamp') >= 0) return 'fa-lightbulb';
    if (hint.indexOf('temp') >= 0 || hint.indexOf('thermo') >= 0) return 'fa-thermometer-half';
    return 'fa-microchip';
  }

  function mapIotDeviceToCube(d, index) {
    var id = String(d.node_id || d.id || ('iot-' + index));
    var displayName = d.product_name || d.name || d.device_type || d.devicetype || 'Device';
    var brand = d.brand_name || d.brandName || d.vendor_name || '';
    var online = d.online !== false && String(d.status || '').toLowerCase() !== 'offline';
    var color = IOT_DEVICE_COLORS[index % IOT_DEVICE_COLORS.length];
    return {
      id: id,
      name: displayName,
      value: brand || (online ? 'Online' : 'Offline'),
      unit: '',
      icon: iotIconForDevice(d),
      color: color,
      location: brand,
      online: online,
      power: d.power !== false,
      stacked: false,
      colSpan: 1,
      rowSpan: 1,
      borderRadius: defaultCardRadius(),
      defaultFace: 0,
      iotNodeId: d.node_id || d.id || '',
      brandLogo: d.brandLogo || d.brand_logo || '',
      productImage: d.productImage || d.product_image || ''
    };
  }

  function getIotDevicesMount() {
    return document.getElementById('ahpIotSduiRoot');
  }

  function refreshIotDevicesStatusSilent() {
    if (layout.activePage !== 'devices') return;
    if (document.hidden) return;
    if (_iotDevicesLoading) return;

    fetchIotDevicesPayload()
      .then(function (devicesData) {
        if (!devicesData || devicesData.success === false) return;
        var devices = extractDevicesFromApiPayload(devicesData);
        if (!devices.length) return;

        var base = _iotDevicesPayload;
        if (!base || typeof base !== 'object') {
          base = {
            page: { header: { title: 'My Devices', eyebrow: 'Smart Home' }, summary: {} },
            layout: { grid: { columns: 2, gap: 12 }, card: { borderRadius: 24, minHeight: 168 } },
            theme: { background: '#ececec' }
          };
        } else {
          base = JSON.parse(JSON.stringify(base));
        }
        var schema = mergeIotDevicesIntoSchema(base, devicesData);
        _iotDevicesPayload = schema;
        applyIotDevicesPayload(schema);
      })
      .catch(function () {
        /* silent background refresh */
      });
  }

  function startIotStatusPoll() {
    stopIotStatusPoll();
    if (layout.activePage !== 'devices') return;
    _iotStatusPollTimer = setInterval(refreshIotDevicesStatusSilent, IOT_STATUS_POLL_MS);
  }

  function stopIotStatusPoll() {
    if (_iotStatusPollTimer) {
      clearInterval(_iotStatusPollTimer);
      _iotStatusPollTimer = null;
    }
  }

  function applyIotDevicesPayload(schema) {
    var mount = getIotDevicesMount();
    if (!mount) return;
    if (window.AHP_IOT_SDUI && typeof window.AHP_IOT_SDUI.render === 'function') {
      window.AHP_IOT_SDUI.render(schema, mount, {
        iotApiUrl: resolveIotApiBase(),
        accessToken: resolveIotAccessToken(),
        sessionApiBase: String(cfg.apiBase || '').replace(/\/+$/, ''),
        onRefresh: function (detail) {
          if (detail && detail.addedNodeId) {
            return refreshIotDevicesAfterAdd(detail);
          }
          // Power toggle only — patch cache in place; never reload list (avoids online/offline flip).
          if (detail && detail.powerOnly) {
            patchIotDevicePowerInCache(detail.nodeId, detail.power);
            return Promise.resolve(_iotDevicesPayload);
          }
          return loadDevicesFromIotApi(true);
        }
      });
      syncDevicePicker();
      return;
    }
    showDevicesPageError(
      'IoT schema renderer failed to load. Upload assets/js/app_home_iot_sdui.js to the server and hard-refresh (Ctrl+Shift+R).'
    );
  }

  function showDevicesPageLoading() {
    var mount = getIotDevicesMount();
    if (!mount) return;
    mount.innerHTML = '<div class="ahp-dev-loading"><i class="fas fa-spinner fa-spin"></i><span>Loading devices from IoT API...</span></div>';
  }

  function showDevicesPageError(message) {
    var mount = getIotDevicesMount();
    if (!mount) return;
    mount.innerHTML = '<div class="ahp-dev-empty"><i class="fas fa-plug"></i><strong>No devices loaded</strong><span>' + esc(message || 'Could not load devices.') + '</span></div>';
  }

  function loadDevicesFromIotApi(force) {
    if (_iotDevicesLoading) {
      return Promise.resolve(_iotDevicesPayload);
    }
    if (!force && _iotDevicesPayload) {
      applyIotDevicesPayload(_iotDevicesPayload);
      return Promise.resolve(_iotDevicesPayload);
    }
    _iotDevicesLoading = true;
    if (layout.activePage === 'devices') {
      showDevicesPageLoading();
    }
    return fetchIotDevicesBundle(!!force)
      .then(function (schema) {
        _iotDevicesPayload = schema;
        if (layout.activePage === 'devices') {
          applyIotDevicesPayload(schema);
        }
        return schema;
      })
      .catch(function (err) {
        if (layout.activePage === 'devices') {
          showDevicesPageError(err && err.message ? err.message : 'Could not load IoT devices.');
        }
        showToast(err && err.message ? err.message : 'Could not load IoT devices.');
        throw err;
      })
      .finally(function () {
        _iotDevicesLoading = false;
      });
  }

  function normalizeNodeIdNeedle(value) {
    return String(value || '').trim().replace(/^socket_/i, '').toLowerCase();
  }

  function buildAddedNodeNeedles(detail) {
    detail = detail || {};
    var needles = [];
    [detail.addedNodeId, detail.scannedNodeId].concat(detail.nodeIdAliases || []).forEach(function (id) {
      var needle = normalizeNodeIdNeedle(id);
      if (needle && needles.indexOf(needle) < 0) needles.push(needle);
    });
    return needles;
  }

  function buildNodeIdAliases(nodeId) {
    var raw = String(nodeId || '').trim();
    if (!raw) return [];
    var plain = raw.replace(/^socket_/i, '');
    var aliases = [raw];
    if (plain && plain !== raw) {
      aliases.push(plain, 'socket_' + plain);
    }
    return aliases;
  }

  function deviceMatchesNeedles(device, needles) {
    if (!device || !needles.length) return false;
    var ids = [
      device.node_id,
      device.nodeId,
      device.node_id_plain,
      device.node && device.node.node_id
    ].concat(Array.isArray(device.node_id_aliases) ? device.node_id_aliases : []);
    for (var i = 0; i < ids.length; i++) {
      var needle = normalizeNodeIdNeedle(ids[i]);
      if (needle && needles.indexOf(needle) >= 0) return true;
    }
    return false;
  }

  function refreshIotDevicesAfterAdd(detail) {
    detail = detail || {};
    var needles = buildAddedNodeNeedles(detail);

    if (detail.devices && Array.isArray(detail.devices) && detail.devices.length) {
      if (!needles.length || detail.devices.some(function (d) { return deviceMatchesNeedles(d, needles); })) {
        var baseFromDetail = _iotDevicesPayload && typeof _iotDevicesPayload === 'object'
          ? JSON.parse(JSON.stringify(_iotDevicesPayload))
          : {
            page: { header: { title: 'My Devices', eyebrow: 'Smart Home' }, summary: {} },
            layout: { grid: { columns: 2, gap: 12 }, card: { borderRadius: 24, minHeight: 168 } },
            theme: { background: '#ececec' }
          };
        var schemaFromDetail = mergeIotDevicesIntoSchema(baseFromDetail, { devices: detail.devices, success: true });
        _iotDevicesPayload = schemaFromDetail;
        applyIotDevicesPayload(schemaFromDetail);
        return Promise.resolve(schemaFromDetail);
      }
    }

    _iotDevicesPayload = null;
    return refreshIotAccessTokenFromProfile()
      .then(function () {
        var proxyUrl = iotDevicesProxyUrl();
        if (proxyUrl) {
          return fetch(proxyUrl, {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
          }).then(parseIotApiJson);
        }
        return fetchIotDevicesPayload();
      })
      .then(function (devicesData) {
        return fetchIotSchemaPayload().catch(function () { return null; }).then(function (schemaData) {
          var schema = resolveIotPayloadFromSchemaResponse(schemaData)
            || normalizeIotSchemaPayload(schemaData)
            || {
              page: { header: { title: 'My Devices', eyebrow: 'Smart Home' }, summary: {} },
              layout: { grid: { columns: 2, gap: 12 }, card: { borderRadius: 24, minHeight: 168 } },
              theme: { background: '#ececec' }
            };
          schema = mergeIotDevicesIntoSchema(schema, devicesData || {});
          var devices = extractIotDevicesFromSchema(schema);
          if (needles.length && !devices.some(function (d) { return deviceMatchesNeedles(d, needles); })) {
            throw new Error('The add-device API completed, but the refreshed device datasource did not return the added device.');
          }
          _iotDevicesPayload = schema;
          applyIotDevicesPayload(schema);
          return schema;
        });
      })
      .catch(function (err) {
        if (_iotDevicesPayload) {
          applyIotDevicesPayload(_iotDevicesPayload);
          return _iotDevicesPayload;
        }
        throw err;
      });
  }

  function patchIotDevicePowerInCache(nodeId, powerOn) {
    if (!_iotDevicesPayload || !nodeId) return;
    var needle = String(nodeId).trim().replace(/^socket_/i, '').toLowerCase();
    var devices = extractIotDevicesFromSchema(_iotDevicesPayload);
    devices.forEach(function (device) {
      if (!device) return;
      var id = String(device.node_id || device.nodeId || '').trim().replace(/^socket_/i, '').toLowerCase();
      if (id !== needle) return;
      device.power = !!powerOn;
      device.powerOn = !!powerOn;
    });
    _iotDevicesPayload.devices = devices;
  }

  function getDevicesPage() {
    if (!layout.devicesPage) {
      var def = cfg.defaultLayout || {};
      layout.devicesPage = JSON.parse(JSON.stringify(def.devicesPage || getDefaultDevicesPage()));
    }
    return layout.devicesPage;
  }

  function assignGridAreas() {
    if (!layout.cards) return;
    var used = Object.create(null);
    layout.cards.forEach(function (card, i) {
      ensureCardSize(card);
      var id = card.id || ('slot-' + i);
      // Always unique per card — never keep a copied gridArea from another card.
      var area = id;
      if (used[area]) {
        area = id + '-' + i;
      }
      used[area] = true;
      card.gridArea = area;
    });
  }

  function ensureCardSize(card) {
    var maxCol = getGridColumns();
    var maxRow = getGridRows();
    card.colSpan = Math.max(1, Math.min(maxCol, parseInt(card.colSpan, 10) || 1));
    card.rowSpan = Math.max(1, Math.min(maxRow, parseInt(card.rowSpan, 10) || (card.tall ? 2 : 1)));
    if (card.orientation !== 'custom') {
      card.orientation = getCardOrientation(card);
    }
  }

  function getCardCol(card) {
    var n = parseInt(card && card.col, 10);
    return isNaN(n) ? 0 : n;
  }

  function getCardRow(card) {
    var n = parseInt(card && card.row, 10);
    return isNaN(n) ? 0 : n;
  }

  function cardFootprintCells(col, row, colSpan, rowSpan) {
    var cells = [];
    var c;
    var r;
    for (r = row; r < row + rowSpan; r++) {
      for (c = col; c < col + colSpan; c++) {
        cells.push(c + ':' + r);
      }
    }
    return cells;
  }

  function buildOccupiedMap(excludeCardId) {
    var map = Object.create(null);
    (layout.cards || []).forEach(function (card) {
      if (!card || card.id === excludeCardId) return;
      ensureCardSize(card);
      var col = getCardCol(card);
      var row = getCardRow(card);
      if (col < 1 || row < 1) return;
      cardFootprintCells(col, row, card.colSpan, card.rowSpan).forEach(function (key) {
        map[key] = card.id;
      });
    });
    return map;
  }

  function cardFitsAt(card, col, row, occupied, maxRows) {
    var cols = getGridColumns();
    var rows = maxRows != null ? maxRows : getGridRows();
    ensureCardSize(card);
    if (col < 1 || row < 1) return false;
    if (col + card.colSpan - 1 > cols) return false;
    if (row + card.rowSpan - 1 > rows) return false;
    var cells = cardFootprintCells(col, row, card.colSpan, card.rowSpan);
    var i;
    for (i = 0; i < cells.length; i++) {
      if (occupied[cells[i]]) return false;
    }
    return true;
  }

  function growGridRowsTo(minRows) {
    var next = normalizeGridRows(Math.max(getGridRows(), minRows || 1), getGridRows());
    if (next === getGridRows()) return next;
    getTheme().gridRows = next;
    var rowsNode = document.getElementById('toolGridRows');
    if (rowsNode) {
      rowsNode.value = next;
      updateRangeUi(rowsNode, '');
    }
    syncGridLayoutVars();
    return next;
  }

  function findFirstFreeSlot(card, occupied) {
    var cols = getGridColumns();
    var rows = getGridRows();
    var r;
    var c;
    var attemptRows;
    ensureCardSize(card);
    // Search current board, then grow rows until a free seat exists (never overlap).
    for (attemptRows = rows; attemptRows <= 64; attemptRows++) {
      for (r = 1; r <= attemptRows - card.rowSpan + 1; r++) {
        for (c = 1; c <= cols - card.colSpan + 1; c++) {
          if (cardFitsAt(card, c, r, occupied, attemptRows)) {
            if (attemptRows > getGridRows()) growGridRowsTo(r + card.rowSpan - 1);
            return { col: c, row: r };
          }
        }
      }
    }
    // Last resort: append below everything (still non-overlapping).
    var maxUsedRow = 0;
    Object.keys(occupied || {}).forEach(function (key) {
      var parts = String(key).split(':');
      var usedRow = parseInt(parts[1], 10);
      if (!isNaN(usedRow)) maxUsedRow = Math.max(maxUsedRow, usedRow);
    });
    var fallbackRow = maxUsedRow + 1;
    growGridRowsTo(fallbackRow + card.rowSpan - 1);
    return { col: 1, row: fallbackRow };
  }

  function ensureAllCardPositions(forceRepack) {
    if (!layout.cards) return;
    var occupied = Object.create(null);
    var pending = [];
    layout.cards.forEach(function (card) {
      ensureCardSize(card);
      var col = getCardCol(card);
      var row = getCardRow(card);
      var cols = getGridColumns();
      var rows = getGridRows();
      var valid = !forceRepack && col >= 1 && row >= 1 &&
        col + card.colSpan - 1 <= cols &&
        row + card.rowSpan - 1 <= rows &&
        cardFitsAt(card, col, row, occupied, rows);
      if (valid) {
        card.col = col;
        card.row = row;
        cardFootprintCells(col, row, card.colSpan, card.rowSpan).forEach(function (key) {
          occupied[key] = card.id;
        });
      } else {
        pending.push(card);
      }
    });
    pending.forEach(function (card) {
      var spot = findFirstFreeSlot(card, occupied);
      card.col = spot.col;
      card.row = spot.row;
      cardFootprintCells(card.col, card.row, card.colSpan, card.rowSpan).forEach(function (key) {
        occupied[key] = card.id;
      });
    });
  }

  function placeCardInFirstFreeSlot(card) {
    if (!card) return;
    ensureCardSize(card);
    delete card.col;
    delete card.row;
    var occupied = buildOccupiedMap(card.id);
    var spot = findFirstFreeSlot(card, occupied);
    card.col = spot.col;
    card.row = spot.row;
  }

  function clampCardPosition(card) {
    ensureCardSize(card);
    var cols = getGridColumns();
    var rows = getGridRows();
    var col = getCardCol(card) || 1;
    var row = getCardRow(card) || 1;
    card.col = Math.max(1, Math.min(col, cols - card.colSpan + 1));
    card.row = Math.max(1, Math.min(row, rows - card.rowSpan + 1));
  }

  function findOverlappingCards(card, col, row) {
    var cells = cardFootprintCells(col, row, card.colSpan, card.rowSpan);
    var hit = Object.create(null);
    var out = [];
    (layout.cards || []).forEach(function (other) {
      if (!other || other.id === card.id) return;
      ensureCardSize(other);
      var oc = getCardCol(other);
      var orow = getCardRow(other);
      if (oc < 1 || orow < 1) return;
      var otherCells = cardFootprintCells(oc, orow, other.colSpan, other.rowSpan);
      var i;
      for (i = 0; i < cells.length; i++) {
        if (otherCells.indexOf(cells[i]) >= 0) {
          if (!hit[other.id]) {
            hit[other.id] = true;
            out.push(other);
          }
          break;
        }
      }
    });
    return out;
  }

  function placeCardAt(card, col, row) {
    if (!card) return false;
    ensureCardSize(card);
    var cols = getGridColumns();
    var rows = getGridRows();
    col = Math.max(1, Math.min(parseInt(col, 10) || 1, cols - card.colSpan + 1));
    row = Math.max(1, Math.min(parseInt(row, 10) || 1, rows - card.rowSpan + 1));
    var oldCol = getCardCol(card) || 1;
    var oldRow = getCardRow(card) || 1;
    if (oldCol === col && oldRow === row) return false;

    var overlapping = findOverlappingCards(card, col, row);
    card.col = col;
    card.row = row;

    overlapping.forEach(function (other) {
      var occupied = buildOccupiedMap(other.id);
      // Prefer swap into the dragged card's old seat when it fits.
      if (cardFitsAt(other, oldCol, oldRow, occupied)) {
        other.col = oldCol;
        other.row = oldRow;
        return;
      }
      var spot = findFirstFreeSlot(other, occupied);
      other.col = spot.col;
      other.row = spot.row;
    });
    return true;
  }

  function clientPointToGridCell(clientX, clientY) {
    if (!el.grid) return null;
    var gridRect = el.grid.getBoundingClientRect();
    if (!gridRect.width || !gridRect.height) return null;
    if (
      clientX < gridRect.left - 12 ||
      clientY < gridRect.top - 12 ||
      clientX > gridRect.right + 12 ||
      clientY > gridRect.bottom + 12
    ) {
      return null;
    }

    var cells = el.grid.querySelectorAll('.ahp-grid-drop-cell');
    var i;
    var cell;
    var rect;
    var best = null;
    var bestDist = Infinity;
    for (i = 0; i < cells.length; i++) {
      cell = cells[i];
      rect = cell.getBoundingClientRect();
      if (!rect.width || !rect.height) continue;
      if (clientX >= rect.left && clientX <= rect.right && clientY >= rect.top && clientY <= rect.bottom) {
        return {
          col: parseInt(cell.getAttribute('data-col'), 10) || 1,
          row: parseInt(cell.getAttribute('data-row'), 10) || 1
        };
      }
      var dx = clientX - ((rect.left + rect.right) / 2);
      var dy = clientY - ((rect.top + rect.bottom) / 2);
      var dist = (dx * dx) + (dy * dy);
      if (dist < bestDist) {
        bestDist = dist;
        best = cell;
      }
    }
    if (best) {
      return {
        col: parseInt(best.getAttribute('data-col'), 10) || 1,
        row: parseInt(best.getAttribute('data-row'), 10) || 1
      };
    }

    var cols = getGridColumns();
    var rows = getGridRows();
    var gap = normalizeGridGap(getTheme().gridGap, 4);
    var colW = (gridRect.width - gap * Math.max(0, cols - 1)) / cols;
    var rowH = (gridRect.height - gap * Math.max(0, rows - 1)) / rows;
    if (colW <= 0 || rowH <= 0) return null;
    var x = Math.max(0, Math.min(gridRect.width - 0.1, clientX - gridRect.left));
    var y = Math.max(0, Math.min(gridRect.height - 0.1, clientY - gridRect.top));
    return {
      col: Math.max(1, Math.min(cols, Math.floor(x / (colW + gap)) + 1)),
      row: Math.max(1, Math.min(rows, Math.floor(y / (rowH + gap)) + 1))
    };
  }

  function clearGridDropHighlights() {
    if (!el.grid) return;
    el.grid.querySelectorAll('.ahp-grid-drop-cell.is-drop-target').forEach(function (node) {
      node.classList.remove('is-drop-target');
    });
  }

  function highlightGridDropFootprint(col, row, colSpan, rowSpan) {
    if (!el.grid) return;
    var wanted = Object.create(null);
    var c;
    var r;
    for (r = row; r < row + rowSpan; r++) {
      for (c = col; c < col + colSpan; c++) {
        wanted[c + ':' + r] = true;
      }
    }
    el.grid.querySelectorAll('.ahp-grid-drop-cell').forEach(function (cell) {
      var key = cell.getAttribute('data-col') + ':' + cell.getAttribute('data-row');
      var on = !!wanted[key];
      if (on !== cell.classList.contains('is-drop-target')) {
        cell.classList.toggle('is-drop-target', on);
      }
    });
  }

  function renderGridDropCellsHtml() {
    if (!editMode) return '';
    var cols = getGridColumns();
    var rows = getGridRows();
    var html = '';
    var r;
    var c;
    for (r = 1; r <= rows; r++) {
      for (c = 1; c <= cols; c++) {
        html += '<div class="ahp-grid-drop-cell" data-drop-cell="1" data-col="' + c + '" data-row="' + r + '" style="grid-column:' + c + ';grid-row:' + r + '" aria-hidden="true"></div>';
      }
    }
    return html;
  }

  function getCardShapeSelectValue(card) {
    if (card && card.orientation === 'custom') return 'custom';
    return getCardOrientation(card);
  }

  function getCardOrientation(card) {
    var maxCol = getGridColumns();
    var maxRow = getGridRows();
    var c = parseInt(card.colSpan, 10) || 1;
    var r = parseInt(card.rowSpan, 10) || 1;
    if (c === 1 && r === 1) return 'square';
    if (c === Math.min(2, maxCol) && r === 1 && maxCol >= 2) return 'horizontal';
    if (c === 1 && r === Math.min(2, maxRow) && maxRow >= 2) return 'vertical';
    if (c === Math.min(2, maxCol) && r === Math.min(2, maxRow) && maxCol >= 2 && maxRow >= 2) return 'large';
    if (c === Math.min(4, maxCol) && r === Math.min(2, maxRow) && maxCol >= 4 && maxRow >= 2) return 'wide4';
    if (c === maxCol && r === maxRow && maxCol >= 2 && maxRow >= 2) return 'full';
    if (c === Math.min(3, maxCol) && r === 1 && maxCol >= 3) return 'wide3';
    if (c === 1 && r === Math.min(3, maxRow) && maxRow >= 3) return 'tall3';
    return 'custom';
  }

  function applyCardOrientation(card, orientation) {
    var maxCol = getGridColumns();
    var maxRow = getGridRows();
    if (orientation === 'custom') {
      card.orientation = 'custom';
      return;
    }
    if (orientation === 'horizontal') {
      card.colSpan = Math.min(2, maxCol);
      card.rowSpan = 1;
    } else if (orientation === 'vertical') {
      card.colSpan = 1;
      card.rowSpan = Math.min(2, maxRow);
    } else if (orientation === 'large') {
      card.colSpan = Math.min(2, maxCol);
      card.rowSpan = Math.min(2, maxRow);
    } else if (orientation === 'wide3') {
      card.colSpan = Math.min(3, maxCol);
      card.rowSpan = 1;
    } else if (orientation === 'wide4') {
      card.colSpan = Math.min(4, maxCol);
      card.rowSpan = Math.min(2, maxRow);
    } else if (orientation === 'tall3') {
      card.colSpan = 1;
      card.rowSpan = Math.min(3, maxRow);
    } else if (orientation === 'full') {
      card.colSpan = maxCol;
      card.rowSpan = maxRow;
    } else if (orientation === 'square') {
      card.colSpan = 1;
      card.rowSpan = 1;
    }
    card.orientation = getCardOrientation(card);
  }

  function cardShapePresetOptions(selected) {
    var maxCol = getGridColumns();
    var maxRow = getGridRows();
    var opts = [
      { value: 'square', label: 'Small / Square (1 x 1)' },
      { value: 'horizontal', label: 'Wide (2 x 1)' },
      { value: 'vertical', label: 'Tall (1 x 2)' },
      { value: 'large', label: 'Medium (2 x 2)' }
    ];
    if (maxCol >= 3) {
      opts.push({ value: 'wide3', label: 'Wide (' + Math.min(3, maxCol) + ' x 1)' });
    }
    if (maxCol >= 4 && maxRow >= 2) {
      opts.push({ value: 'wide4', label: 'Banner (' + Math.min(4, maxCol) + ' x 2)' });
    }
    if (maxRow >= 3) {
      opts.push({ value: 'tall3', label: 'Tall (1 x ' + Math.min(3, maxRow) + ')' });
    }
    if (maxCol >= 2 && maxRow >= 2) {
      opts.push({ value: 'full', label: 'Full grid (' + maxCol + ' x ' + maxRow + ')' });
    }
    opts.push({ value: 'custom', label: 'Custom (use width/height sliders)' });
    return opts.map(function (opt) {
      return '<option value="' + opt.value + '"' + (selected === opt.value ? ' selected' : '') + '>' + esc(opt.label) + '</option>';
    }).join('');
  }

  function normalizeCardOrder() {
    if (!layout.cards || layout.cards.length < 2) return;
    layout.cards.sort(function (a, b) {
      var ia = GRID_AREAS.indexOf(a.gridArea || '');
      var ib = GRID_AREAS.indexOf(b.gridArea || '');
      if (ia < 0) ia = GRID_AREAS.length;
      if (ib < 0) ib = GRID_AREAS.length;
      return ia - ib;
    });
  }

  var CUBE_FACE_COUNT = 4;
  var CUBE_HOLD_RATIO = 0.84;

  function easeInOutCubic(t) {
    if (t <= 0) return 0;
    if (t >= 1) return 1;
    return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
  }

  function getCubeTiming(speed) {
    speed = Math.max(4, Number(speed || 16));
    var segment = speed / CUBE_FACE_COUNT;
    var rotate = segment * (1 - CUBE_HOLD_RATIO);
    return {
      speed: speed,
      segment: segment,
      hold: segment * CUBE_HOLD_RATIO,
      // Alias kept for older callers; prefer .rotate for dwell-then-rotateY.
      flip: rotate,
      rotate: rotate
    };
  }

  function getCubeElapsedSeconds(cardId, index, isDevice, deviceId) {
    var speed = isDevice
      ? Math.max(8, Number(getTheme().cardSpeed || 16))
      : getCardRotationSpeed(cardId);
    var delay = isDevice
      ? getDeviceAnimOffset(deviceId, index)
      : getCardAnimOffset(cardId || '', index);
    var elapsed = ((Date.now() / 1000) - delay) % speed;
    if (elapsed < 0) elapsed += speed;
    return elapsed;
  }

  function getCubeRotationFromElapsed(elapsed, speed, rotationMul) {
    var mul = rotationMul == null ? -1 : rotationMul;
    var t = getCubeTiming(speed);
    var segmentIndex = Math.floor(elapsed / t.segment) % CUBE_FACE_COUNT;
    var tInSegment = elapsed % t.segment;
    var step = cubeSpinStep(mul);
    var baseAngle = segmentIndex * step;
    if (tInSegment <= t.hold) return baseAngle;
    var rotateProgress = Math.min(1, (tInSegment - t.hold) / Math.max(0.001, t.rotate));
    var nextAngle = (segmentIndex + 1) * step;
    // Continuous Y rotation — no mid-turn face swap (not flatFlipCard).
    return baseAngle + (nextAngle - baseAngle) * easeInOutCubic(rotateProgress);
  }

  function getCubeFaceIndexFromElapsed(elapsed, speed, rotationMul) {
    var mul = rotationMul == null ? -1 : rotationMul;
    var t = getCubeTiming(speed);
    var segmentIndex = Math.floor(elapsed / t.segment) % CUBE_FACE_COUNT;
    var tInSegment = elapsed % t.segment;
    // Front face follows the completed/current segment angle, not a mid-rotation swap.
    if (tInSegment > t.hold) {
      var rotateProgress = (tInSegment - t.hold) / Math.max(0.001, t.rotate);
      if (rotateProgress >= 1) {
        return mapSegmentToDisplayFace((segmentIndex + 1) % CUBE_FACE_COUNT, mul);
      }
    }
    return mapSegmentToDisplayFace(segmentIndex, mul);
  }

  function getCubeInteractionFace(elapsed, speed, rotationMul) {
    var mul = rotationMul == null ? -1 : rotationMul;
    var t = getCubeTiming(speed);
    var segmentIndex = Math.floor(elapsed / t.segment) % CUBE_FACE_COUNT;
    var tInSegment = elapsed % t.segment;
    if (tInSegment <= t.hold) {
      return { face: mapSegmentToDisplayFace(segmentIndex, mul), interactive: true };
    }
    var rotateProgress = (tInSegment - t.hold) / Math.max(0.001, t.rotate);
    if (rotateProgress >= 1) {
      return {
        face: mapSegmentToDisplayFace((segmentIndex + 1) % CUBE_FACE_COUNT, mul),
        interactive: true
      };
    }
    // During Y turn, keep previous face interactive binding; geometry shows both sides.
    return { face: mapSegmentToDisplayFace(segmentIndex, mul), interactive: false };
  }

  function syncCubeFacePointerState(cube, faceIndex, interactive) {
    cube.querySelectorAll('.ahp-cube-face[data-face-index]').forEach(function (faceEl) {
      var fi = parseInt(faceEl.getAttribute('data-face-index'), 10) || 0;
      var isFront = interactive && fi === faceIndex;
      faceEl.classList.toggle('ahp-cube-face-front', isFront);
      faceEl.style.pointerEvents = isFront ? 'auto' : 'none';
    });
  }

  function faceSecondsFromCardSpeed(speed) {
    speed = Math.max(8, Number(speed || 16));
    return Math.max(2, Math.min(8, speed / CUBE_FACE_COUNT));
  }

  function cardSpeedFromFaceSeconds(faceSec) {
    var sec = Math.max(2, Math.min(8, parseFloat(faceSec) || 4));
    return Math.round(sec * CUBE_FACE_COUNT);
  }

  function getCardById(cardId) {
    return (layout.cards || []).find(function (c) { return c.id === cardId; });
  }

  function getCardFaceSec(card) {
    if (card && card.faceSec != null && card.faceSec !== '' && !isNaN(parseFloat(card.faceSec))) {
      return Math.max(2, Math.min(8, parseFloat(card.faceSec)));
    }
    return faceSecondsFromCardSpeed(getTheme().cardSpeed || 16);
  }

  function getCardRotationSpeed(cardOrId) {
    var card = typeof cardOrId === 'string' ? getCardById(cardOrId) : cardOrId;
    return cardSpeedFromFaceSeconds(getCardFaceSec(card));
  }

  function invalidateCardAnimOffset(cardId) {
    delete cardAnimOffsets[cardId];
    delete cardAnimOffsets[cardId + '__speed'];
  }

  function computeCubeAnimOffset(id, index, totalCount, speed) {
    speed = Math.max(8, Number(speed || getTheme().cardSpeed || 16));
    var count = Math.max(1, totalCount || 1);
    var slot = Math.max(0, index || 0);
    var hash = String(id || '').split('').reduce(function (sum, ch) {
      return sum + ch.charCodeAt(0);
    }, 0);
    var segment = speed / CUBE_FACE_COUNT;
    var indexSpread = (slot / count) * speed;
    var hashSpread = ((hash % 997) / 997) * speed * 0.88;
    var microSpread = ((hash % 17) / 17) * segment * 0.95;
    var saltSpread = ((hash % 43) / 43) * segment * 0.35;
    return -((indexSpread + hashSpread + microSpread + saltSpread) % speed);
  }

  function getCardRotationMul(cardOrId) {
    var card = typeof cardOrId === 'string' ? getCardById(cardOrId) : cardOrId;
    if (card && card.rotationDir === 'ltr') return 1;
    return -1;
  }

  function mapSegmentToDisplayFace(segmentIndex, rotationMul) {
    var seg = Math.max(0, Math.min(3, parseInt(segmentIndex, 10) || 0));
    var mul = rotationMul == null ? -1 : rotationMul;
    if (mul < 0) return seg;
    return (CUBE_FACE_COUNT - seg) % CUBE_FACE_COUNT;
  }

  function cubeAngleForFace(faceIndex, rotationMul) {
    var fi = Math.max(0, Math.min(3, parseInt(faceIndex, 10) || 0));
    return -fi * 90;
  }

  function cubeSpinStep(rotationMul) {
    return rotationMul >= 0 ? 90 : -90;
  }

  function nextFaceInSpin(faceIndex, rotationMul) {
    var fi = Math.max(0, Math.min(3, parseInt(faceIndex, 10) || 0));
    if (rotationMul >= 0) return (fi + 3) % CUBE_FACE_COUNT;
    return (fi + 1) % CUBE_FACE_COUNT;
  }

  function forwardStepsBetween(fromFace, toFace, rotationMul) {
    fromFace = Math.max(0, Math.min(3, parseInt(fromFace, 10) || 0));
    toFace = Math.max(0, Math.min(3, parseInt(toFace, 10) || 0));
    if (fromFace === toFace) return 0;
    var steps = 0;
    var cursor = fromFace;
    while (cursor !== toFace && steps < CUBE_FACE_COUNT) {
      cursor = nextFaceInSpin(cursor, rotationMul);
      steps++;
    }
    return steps;
  }

  function targetAngleForFaceTransition(fromAngle, fromFace, toFace, rotationMul) {
    var steps = forwardStepsBetween(fromFace, toFace, rotationMul);
    if (!steps) return fromAngle;
    return fromAngle + steps * cubeSpinStep(rotationMul);
  }

  function shortestAngleDelta(fromAngle, toAngle) {
    var delta = toAngle - fromAngle;
    while (delta > 180) delta -= 360;
    while (delta < -180) delta += 360;
    return delta;
  }

  function syncSelectedCardCubeFace(options) {
    options = options || {};
    if (!selectedCardId || !el.grid) return;
    var slot = el.grid.querySelector('[data-card-id="' + selectedCardId + '"]');
    if (!slot) return;
    var cube = slot.querySelector('.ahp-cube:not(.ahp-application-cube)');
    if (!cube) {
      updateCardSlot(selectedCardId);
      scheduleCubeGeometry(el.grid);
      syncCubeRotation(el.grid);
      return;
    }

    var faceIndex = Math.max(0, Math.min(3, activeFaceIndex));
    var prevFace = options.fromFace != null ? Math.max(0, Math.min(3, options.fromFace)) : faceIndex;
    var rotationMul = getCardRotationMul(selectedCardId);
    var currentAngle = cube.dataset.ahpCubeAngle != null
      ? parseFloat(cube.dataset.ahpCubeAngle)
      : cubeAngleForFace(prevFace, rotationMul);
    if (isNaN(currentAngle)) currentAngle = cubeAngleForFace(prevFace, rotationMul);
    var targetAngle = options.animate && prevFace !== faceIndex
      ? targetAngleForFaceTransition(currentAngle, prevFace, faceIndex, rotationMul)
      : cubeAngleForFace(faceIndex, rotationMul);

    cube.classList.add('paused', 'ahp-show-face');
    cube.style.setProperty('--ahp-face-index', faceIndex);

    if (options.animate && prevFace !== faceIndex) {
      var animTarget = targetAngle;
      cube.classList.remove('ahp-face-animating');
      cube.style.transition = 'none';
      cube.style.transform = cubeParentTransform(currentAngle, cube);
      cube.offsetHeight;
      cube.classList.add('ahp-face-animating');
      cube.style.transform = cubeParentTransform(animTarget, cube);
      targetAngle = animTarget;
      var clearAnim = function (evt) {
        if (evt && evt.propertyName && evt.propertyName !== 'transform') return;
        cube.classList.remove('ahp-face-animating');
        cube.removeEventListener('transitionend', clearAnim);
      };
      cube.addEventListener('transitionend', clearAnim);
    } else {
      cube.classList.remove('ahp-face-animating');
      cube.style.transition = 'none';
      cube.style.transform = cubeParentTransform(targetAngle, cube);
    }

    cube.dataset.ahpCubeAngle = String(targetAngle);
    scheduleCubeGeometry(el.grid);
  }

  function getCardAnimOffset(cardId, index) {
    var card = getCardById(cardId);
    var speed = getCardRotationSpeed(card);
    if (cardAnimOffsets[cardId] == null || cardAnimOffsets[cardId + '__speed'] !== speed) {
      cardAnimOffsets[cardId] = computeCubeAnimOffset(cardId, index, (layout.cards || []).length, speed);
      cardAnimOffsets[cardId + '__speed'] = speed;
    }
    return cardAnimOffsets[cardId];
  }

  function ensureFaces(card, options) {
    options = options || {};
    card.type = normalizeHomeBlockType(card.type);
    if (!card.faces || !card.faces.length) {
      card.faces = [
        { type: card.type, value: card.value || '', label: card.label || card.title || '', icon: card.icon || 'fa-cube', color: card.color },
        { type: card.type, title: 'Face 2', icon: 'fa-star', color: TEMPLATE_FACE_COLORS[1] },
        { type: card.type, title: 'Face 3', icon: 'fa-bolt', color: TEMPLATE_FACE_COLORS[2] },
        { type: card.type, title: 'Face 4', icon: 'fa-heart', color: TEMPLATE_FACE_COLORS[3] }
      ];
    }
    while (card.faces.length < 4) {
      var padFi = card.faces.length;
      card.faces.push({
        type: card.type,
        title: 'Face ' + (padFi + 1),
        icon: TEMPLATE_FACE_ICONS[padFi] || 'fa-cube',
        color: TEMPLATE_FACE_COLORS[padFi] || card.color
      });
    }
    card.faces = card.faces.slice(0, 4);
    diversifyDuplicateCardFaces(card);
    card.faces.forEach(function (face, fi) {
      if (!face || typeof face !== 'object') return;
      // Only seed Face 1 from card.color — never overwrite other faces' colors.
      if (fi === 0 && !face.color && card.color) face.color = card.color;
      if (fi === 0 && !face.textColor && card.textColor) face.textColor = card.textColor;
      if (fi === 0 && !face.iconColor && card.iconColor) face.iconColor = card.iconColor;
      if (fi > 0 && !face.color && card.sameFaceColor !== true) {
        face.color = TEMPLATE_FACE_COLORS[fi] || card.color;
      }
      if (!face.iconColor) face.iconColor = '#ffffff';
      if (!face.textColor && !face.titleTextColor) face.textColor = '#ffffff';
      face.type = normalizeFaceContentType(face, card);
    });
    if (!options.skipLegacyUpgrade) {
      /* manual CRUD only */
    }
    if (card.borderRadius == null) card.borderRadius = defaultCardRadius();
    if (!card.rotationDir) card.rotationDir = 'rtl';
    card.defaultFace = 0;
    return card;
  }

  /** Fix template/legacy tiles that cloned the same title/subtitle onto every cube face. */
  function diversifyDuplicateCardFaces(card) {
    if (!card || !Array.isArray(card.faces) || card.faces.length < 2) return card;
    var primary = card.faces[0] || {};
    var primaryKey = [
      String(primary.title || primary.label || '').trim().toLowerCase(),
      String(primary.subtitle != null ? primary.subtitle : '').trim().toLowerCase(),
      String(primary.value != null ? primary.value : '').trim().toLowerCase(),
      String(primary.image || '').trim()
    ].join('|');
    for (var fi = 1; fi < card.faces.length; fi++) {
      var face = card.faces[fi];
      if (!face || typeof face !== 'object') {
        card.faces[fi] = {
          type: card.type,
          title: 'Face ' + (fi + 1),
          icon: TEMPLATE_FACE_ICONS[fi] || 'fa-cube',
          color: TEMPLATE_FACE_COLORS[fi] || card.color,
          textColor: '#ffffff',
          iconColor: '#ffffff'
        };
        continue;
      }
      var key = [
        String(face.title || face.label || '').trim().toLowerCase(),
        String(face.subtitle != null ? face.subtitle : '').trim().toLowerCase(),
        String(face.value != null ? face.value : '').trim().toLowerCase(),
        String(face.image || '').trim()
      ].join('|');
      var empty = !String(face.title || face.label || '').trim()
        && !String(face.subtitle != null ? face.subtitle : '').trim()
        && (face.value == null || String(face.value).trim() === '')
        && !String(face.image || '').trim();
      if (key === primaryKey || empty) {
        card.faces[fi] = {
          type: face.type || card.type,
          title: 'Face ' + (fi + 1),
          label: 'Face ' + (fi + 1),
          subtitle: '',
          icon: TEMPLATE_FACE_ICONS[fi] || face.icon || 'fa-cube',
          color: card.sameFaceColor === true
            ? (face.color || card.color || TEMPLATE_FACE_COLORS[fi])
            : (TEMPLATE_FACE_COLORS[fi] || face.color || card.color),
          textColor: face.textColor || card.textColor || '#ffffff',
          iconColor: face.iconColor || card.iconColor || '#ffffff'
        };
      }
    }
    return card;
  }

  function defaultCardRadius() {
    var t = getTheme();
    if (t && t.cardBorderRadius != null && t.cardBorderRadius !== '') {
      var n = parseInt(t.cardBorderRadius, 10);
      if (!isNaN(n)) return Math.max(0, Math.min(72, n));
    }
    return 0;
  }

  /** Label input = primary line on card; Subtitle input = secondary line. */
  var IMAGE_PLACEHOLDER_OVERLAY_TITLES = {
    'new block': true,
    'face 2': true,
    'face 3': true,
    'face 4': true
  };

  function readImageOverlayLabel(face) {
    face = face || {};
    var raw = String(face.title || face.label || '').trim();
    if (!raw) return '';
    if (IMAGE_PLACEHOLDER_OVERLAY_TITLES[raw.toLowerCase()]) return '';
    return raw;
  }

  function readImageOverlaySubtitle(face, card) {
    face = face || {};
    card = card || {};
    return String(face.subtitle != null && face.subtitle !== '' ? face.subtitle : (card.subtitle || '')).trim();
  }

  function readFaceImageUrlForEditor(card, face, faceIndex) {
    card = card || {};
    face = face || {};
    if (faceIndex === 0) {
      if (face.image != null && String(face.image).trim() !== '') return String(face.image).trim();
      if (card.image != null && String(card.image).trim() !== '') return String(card.image).trim();
      return '';
    }
    return face.image != null ? String(face.image).trim() : '';
  }

  function readFaceEditorText(card, face) {
    var type = card.type || 'link';
    if (type === 'stat') {
      return {
        label: face.value != null && face.value !== '' ? String(face.value) : (card.value != null && card.value !== '' ? String(card.value) : ''),
        subtitle: face.label || card.label || card.title || ''
      };
    }
    if (type === 'image') {
      return {
        label: readImageOverlayLabel(face),
        subtitle: readImageOverlaySubtitle(face, card)
      };
    }
    return {
      label: face.title || face.label || card.title || card.label || '',
      subtitle: face.subtitle || card.subtitle || ''
    };
  }

  function writeFaceEditorText(face, type, labelVal, subVal) {
    if (type === 'stat') {
      face.value = labelVal;
      face.label = subVal;
    } else {
      face.title = labelVal;
      face.subtitle = subVal;
      face.label = labelVal;
    }
  }

  function syncCardTextFromFace0(card, type, labelVal, subVal) {
    if (type === 'stat') {
      card.value = labelVal;
      card.label = subVal;
      card.title = subVal;
    } else {
      card.label = labelVal;
      card.title = labelVal;
      card.subtitle = subVal;
    }
  }

  function getDefaultFace(card) {
    if (card) card.defaultFace = 0;
    return 0;
  }

  function ensureDeviceFaces(device) {
    if (!device) return device;
    if (!device.faces || !device.faces.length) {
      var baseName = device.name || 'Device';
      var baseIcon = device.icon || 'fa-cube';
      var baseColor = device.color || '#334155';
      device.faces = [
        {
          name: baseName,
          value: device.value != null ? device.value : '',
          unit: device.unit || '',
          icon: baseIcon,
          color: baseColor,
          location: device.location || '',
          showIcon: true,
          iconSize: 28
        },
        { name: baseName + ' - B', icon: 'fa-star', color: baseColor, showIcon: true, iconSize: 28 },
        { name: baseName + ' - C', icon: 'fa-bolt', color: baseColor, showIcon: true, iconSize: 28 },
        { name: baseName + ' - D', icon: 'fa-heart', color: baseColor, showIcon: true, iconSize: 28 }
      ];
    }
    while (device.faces.length < 4) {
      device.faces.push({
        name: 'Face ' + (device.faces.length + 1),
        icon: device.icon || 'fa-cube',
        color: device.color || '#334155',
        showIcon: true,
        iconSize: 28
      });
    }
    device.faces = device.faces.slice(0, 4);
    device.faces.forEach(function (face) {
      if (!face.icon) face.icon = device.icon || 'fa-cube';
      if (!face.color) face.color = device.color || '#334155';
      if (face.showIcon == null) face.showIcon = true;
      if (face.iconSize == null) face.iconSize = 28;
    });
    device.defaultFace = getDeviceDefaultFace(device);
    if (device.defaultFace == null || isNaN(device.defaultFace)) device.defaultFace = 0;
    ensureDeviceSize(device);
    if (device.borderRadius == null) device.borderRadius = 16;
    if (!device.textColor) device.textColor = '#ffffff';
    if (device.showIcon == null) device.showIcon = true;
    if (device.iconSize == null) device.iconSize = 28;
    return device;
  }

  function ensureDeviceSize(device) {
    device.colSpan = Math.max(1, Math.min(3, parseInt(device.colSpan, 10) || 1));
    device.rowSpan = Math.max(1, Math.min(3, parseInt(device.rowSpan, 10) || 1));
    device.orientation = getCardOrientation(device);
  }

  function getDeviceDefaultFace(device) {
    var n = parseInt(device && device.defaultFace, 10);
    if (isNaN(n) || n < 0 || n > 3) return 0;
    return n;
  }

  function getDeviceAnimOffset(deviceId, index) {
    if (deviceAnimOffsets[deviceId] == null) {
      var page = getDevicesPage();
      deviceAnimOffsets[deviceId] = computeCubeAnimOffset(deviceId, index, (page.devices || []).length);
    }
    return deviceAnimOffsets[deviceId];
  }

  function deviceFaceBg(face, device) {
    return face.color || device.color || '#334155';
  }

  function deviceFaceRadius(face, device) {
    if (face.borderRadius != null && face.borderRadius !== '') {
      var faceRadiusValue = parseInt(face.borderRadius, 10);
      if (!isNaN(faceRadiusValue)) return Math.max(0, faceRadiusValue);
    }
    if (device.borderRadius != null && device.borderRadius !== '') {
      var deviceRadiusValue = parseInt(device.borderRadius, 10);
      if (!isNaN(deviceRadiusValue)) return Math.max(0, deviceRadiusValue);
    }
    return 16;
  }

  function deviceFaceTextColor(face, device) {
    if (face.textColor) return face.textColor;
    if (device.textColor) return device.textColor;
    return getTheme().textPrimary || '#ffffff';
  }

  function deviceFaceIconColor(face, device) {
    if (face.iconColor) return face.iconColor;
    if (device.iconColor) return device.iconColor;
    return getTheme().accentColor || '#ffffff';
  }

  function deviceFaceIconSize(face, device) {
    if (face.iconSize != null && face.iconSize !== '') return parseInt(face.iconSize, 10);
    if (device.iconSize != null && device.iconSize !== '') return parseInt(device.iconSize, 10);
    return 28;
  }

  function deviceFaceNameSize(face, device) {
    if (face.titleFontSize != null && face.titleFontSize !== '') return parseInt(face.titleFontSize, 10);
    if (device.titleFontSize != null && device.titleFontSize !== '') return parseInt(device.titleFontSize, 10);
    return 9;
  }

  function deviceFaceValueSize(face, device) {
    if (face.subFontSize != null && face.subFontSize !== '') return parseInt(face.subFontSize, 10);
    if (device.subFontSize != null && device.subFontSize !== '') return parseInt(device.subFontSize, 10);
    return 18;
  }

  function deviceFaceLocSize(face, device) {
    if (face.locFontSize != null && face.locFontSize !== '') return parseInt(face.locFontSize, 10);
    return 8;
  }

  function syncDeviceFromFace0(device) {
    if (!device || !device.faces || !device.faces[0]) return;
    var f0 = device.faces[0];
    device.name = f0.name != null ? f0.name : device.name;
    device.value = f0.value != null ? f0.value : device.value;
    device.unit = f0.unit != null ? f0.unit : device.unit;
    device.location = f0.location != null ? f0.location : device.location;
    device.icon = f0.icon || device.icon;
    device.color = f0.color || device.color;
    if (f0.textColor) device.textColor = f0.textColor;
    if (f0.iconColor) device.iconColor = f0.iconColor;
  }

  function renderDeviceFace(device, face, idx) {
    face = face || {};
    var bg = deviceFaceBg(face, device);
    var radius = deviceFaceRadius(face, device);
    var textColor = deviceFaceTextColor(face, device);
    var iconColor = deviceFaceIconColor(face, device);
    var shell = styleAttr({
      background: bg,
      'border-radius': radius + 'px',
      color: textColor
    });
    var icon = normalizeFaIcon(face.icon || device.icon || 'fa-cube');
    var iconSz = deviceFaceIconSize(face, device);
    var iconStyle = styleAttr({ 'font-size': iconSz + 'px', color: iconColor });
    var value = face.value !== undefined && face.value !== null
      ? face.value
      : (idx === 0 && device.value != null ? device.value : '');
    var unit = face.unit !== undefined && face.unit !== null
      ? face.unit
      : (idx === 0 ? (device.unit || '') : '');
    var valueHtml = '';
    if (value !== '' && value != null) {
      var valueStyle = styleAttr({
        'font-size': deviceFaceValueSize(face, device) + 'px',
        'font-family': resolveFontFamily(face.subFontFamily || device.subFontFamily),
        color: textColor
      });
      valueHtml = '<div class="ahp-dev-value" style="' + valueStyle + '">' + esc(value) +
        (unit ? '<small>' + esc(unit) + '</small>' : '') + '</div>';
    }
    var name = (face.name != null && face.name !== '')
      ? face.name
      : (face.label || (idx === 0 ? (device.name || 'Device') : ('Face ' + (idx + 1))));
    var nameStyle = styleAttr({
      'font-size': deviceFaceNameSize(face, device) + 'px',
      'font-family': resolveFontFamily(face.titleFontFamily || device.titleFontFamily),
      color: textColor
    });
    var loc = face.location !== undefined && face.location !== null
      ? face.location
      : (idx === 0 ? (device.location || '') : '');
    var locHtml = '';
    if (loc) {
      var locStyle = styleAttr({
        'font-size': deviceFaceLocSize(face, device) + 'px',
        'font-family': resolveFontFamily(face.locFontFamily || device.locFontFamily),
        color: textColor
      });
      locHtml = '<div class="ahp-dev-loc" style="' + locStyle + '">' + esc(loc) + '</div>';
    }
    return '<div class="ahp-cube-face face-' + idx + ' ahp-dev-face" data-face-index="' + idx + '" style="' + shell + '">' +
      '<div class="ahp-dev-body"><i class="fas ' + esc(icon) + '" style="' + iconStyle + '"></i>' + valueHtml +
      '<div class="ahp-dev-name" style="' + nameStyle + '">' + esc(name) + '</div>' + locHtml + '</div></div>';
  }

  function resolveFontFamily(id) {
    if (!id || id === 'system') return FONT_FAMILIES[0].css;
    var found = FONT_FAMILIES.find(function (f) { return f.id === id; });
    return found ? found.css : FONT_FAMILIES[0].css;
  }

  function shiftHexColor(hex, amount) {
    var h = String(hex || '').replace('#', '').trim();
    if (h.length === 3) {
      h = h.charAt(0) + h.charAt(0) + h.charAt(1) + h.charAt(1) + h.charAt(2) + h.charAt(2);
    }
    if (h.length !== 6 || !/^[0-9a-f]+$/i.test(h)) return hex || '#334155';
    var num = parseInt(h, 16);
    var r = Math.max(0, Math.min(255, ((num >> 16) & 255) + amount));
    var g = Math.max(0, Math.min(255, ((num >> 8) & 255) + amount));
    var b = Math.max(0, Math.min(255, (num & 255) + amount));
    return '#' + [r, g, b].map(function (v) {
      return v.toString(16).padStart(2, '0');
    }).join('');
  }

  function faceBg(face, card) {
    var raw = String((face && face.color) || (card && card.color) || '#334155').trim();
    if (!raw) raw = '#334155';
    // Metro / Start tiles: flat solid fill so preview matches the color picker exactly.
    if (/gradient/i.test(raw)) return raw;
    return raw;
  }

  function faceRadius(face, card) {
    if (face.borderRadius != null && face.borderRadius !== '') return parseInt(face.borderRadius, 10) || 0;
    return cardRadius(card);
  }

  function cardRadius(card) {
    if (card && card.borderRadius != null && card.borderRadius !== '') {
      var n = parseInt(card.borderRadius, 10);
      if (!isNaN(n)) return Math.max(0, n);
    }
    return defaultCardRadius();
  }

  function faceTitleSize(face, card, type) {
    if (face.titleFontSize != null && face.titleFontSize !== '') return parseInt(face.titleFontSize, 10);
    if (card.titleFontSize != null && card.titleFontSize !== '') return parseInt(card.titleFontSize, 10);
    return null;
  }

  function faceSubSize(face, card, type) {
    if (face.subFontSize != null && face.subFontSize !== '') return parseInt(face.subFontSize, 10);
    if (card.subFontSize != null && card.subFontSize !== '') return parseInt(card.subFontSize, 10);
    return null;
  }

  function faceIconSize(face, card, type) {
    if (face.iconSize != null && face.iconSize !== '') return parseInt(face.iconSize, 10);
    if (card.iconSize != null && card.iconSize !== '') return parseInt(card.iconSize, 10);
    return null;
  }

  function defaultFaceIconSize(type) {
    if (type === 'stat') return 20;
    if (type === 'image') return 18;
    return 28;
  }

  function defaultFaceTitleSize(type) {
    if (type === 'stat') return 9;
    if (type === 'image') return 13;
    return 14;
  }

  function defaultFaceSubSize(type) {
    if (type === 'stat') return 24;
    if (type === 'image') return 10;
    return 10;
  }

  function clampFontWeight(value, fallback) {
    var n = parseInt(value, 10);
    if (isNaN(n)) return fallback;
    if (n < 100) return 100;
    if (n > 900) return 900;
    return Math.round(n / 100) * 100;
  }

  function faceTitleWeight(face, card, type) {
    if (face && face.titleFontWeight != null && face.titleFontWeight !== '') {
      return clampFontWeight(face.titleFontWeight, 800);
    }
    if (card && card.titleFontWeight != null && card.titleFontWeight !== '') {
      return clampFontWeight(card.titleFontWeight, 800);
    }
    return type === 'stat' ? 800 : 800;
  }

  function faceSubWeight(face, card, type) {
    var raw = null;
    if (face) {
      if (face.subFontWeight != null && face.subFontWeight !== '') raw = face.subFontWeight;
      else if (face.subtitleFontWeight != null && face.subtitleFontWeight !== '') raw = face.subtitleFontWeight;
    }
    if (raw == null && card) {
      if (card.subFontWeight != null && card.subFontWeight !== '') raw = card.subFontWeight;
      else if (card.subtitleFontWeight != null && card.subtitleFontWeight !== '') raw = card.subtitleFontWeight;
    }
    if (raw != null) return clampFontWeight(raw, 500);
    return type === 'stat' ? 900 : 500;
  }

  function faceIconSizeDisplay(face, card, type) {
    var n = faceIconSize(face, card, type);
    return n != null && !isNaN(n) ? n : defaultFaceIconSize(type);
  }

  function faceTitleSizeDisplay(face, card, type) {
    var n = faceTitleSize(face, card, type);
    return n != null && !isNaN(n) ? n : defaultFaceTitleSize(type);
  }

  function faceSubSizeDisplay(face, card, type) {
    var n = faceSubSize(face, card, type);
    return n != null && !isNaN(n) ? n : defaultFaceSubSize(type);
  }

  function faceTypographyVars(face, card, type) {
    var vars = {};
    var iconSz = faceIconSizeDisplay(face, card, type);
    var titleSz = faceTitleSizeDisplay(face, card, type);
    var subSz = faceSubSizeDisplay(face, card, type);
    var titleWt = faceTitleWeight(face, card, type);
    var subWt = faceSubWeight(face, card, type);
    vars['--ahp-face-icon-size'] = iconSz + 'px';
    if (type === 'stat') {
      vars['--ahp-face-label-size'] = titleSz + 'px';
      vars['--ahp-face-value-size'] = subSz + 'px';
    } else {
      vars['--ahp-face-title-size'] = titleSz + 'px';
      vars['--ahp-face-sub-size'] = subSz + 'px';
    }
    vars['--ahp-face-title-weight'] = String(titleWt);
    vars['--ahp-face-sub-weight'] = String(subWt);
    var titleFont = face.titleFontFamily || card.titleFontFamily || 'system';
    var subFont = face.subFontFamily || card.subFontFamily || 'system';
    vars['--ahp-face-title-font'] = resolveFontFamily(titleFont);
    vars['--ahp-face-sub-font'] = resolveFontFamily(subFont);
    return vars;
  }

  function resolveMediaUrl(url) {
    var src = String(url || '').trim();
    if (!src) return '';
    if (/^https?:\/\//i.test(src) || /^data:/i.test(src)) return src;
    if (src.indexOf('//') === 0) return window.location.protocol + src;
    var base = (cfg.filesBase || '').replace(/\/$/, '') || (window.location.origin + '/files');
    if (src.charAt(0) === '/') return window.location.origin + src;
    if (src.indexOf('files/') === 0) return window.location.origin + '/' + src.replace(/^\/+/, '');
    if (src.indexOf('/') !== -1) return window.location.origin + '/' + src.replace(/^\/+/, '');
    return base + '/brand_logo/' + encodeURIComponent(src);
  }

  function cssUrl(url) {
    var resolved = resolveMediaUrl(url);
    if (!resolved) return '';
    return String(resolved).replace(/\\/g, '\\\\').replace(/'/g, '%27');
  }

  var IMAGE_FIT_OPTIONS = [
    { value: 'cover', label: 'Cover (fill tile, no blank edges)' },
    { value: 'contain', label: 'Contain (show full image, may letterbox)' },
    { value: 'fill', label: 'Fill (stretch to block)' }
  ];

  var IMAGE_POSITION_OPTIONS = [
    { value: 'center', label: 'Center' },
    { value: 'top', label: 'Top' },
    { value: 'bottom', label: 'Bottom' },
    { value: 'left', label: 'Left' },
    { value: 'right', label: 'Right' }
  ];

  function normalizeImageFit(value) {
    var fit = String(value || 'cover').toLowerCase();
    if (fit === 'contain' || fit === 'fill') return fit;
    return 'cover';
  }

  function normalizeImagePosition(value) {
    var pos = String(value || 'center').toLowerCase();
    return IMAGE_POSITION_OPTIONS.some(function (opt) { return opt.value === pos; }) ? pos : 'center';
  }

  function normalizeImageScale(value) {
    var scale = parseInt(value, 10);
    if (isNaN(scale)) scale = 100;
    return Math.max(50, Math.min(200, scale));
  }

  function ensureFaceImageDisplayDefaults(face, card) {
    if (!face || typeof face !== 'object') return face;
    if (!face.imageFit) face.imageFit = (card && card.imageFit) || 'cover';
    if (!face.imagePosition) face.imagePosition = (card && card.imagePosition) || 'center';
    if (face.imageScale == null || face.imageScale === '') {
      face.imageScale = (card && card.imageScale != null) ? card.imageScale : 100;
    }
    face.imageFit = normalizeImageFit(face.imageFit);
    face.imagePosition = normalizeImagePosition(face.imagePosition);
    face.imageScale = normalizeImageScale(face.imageScale);
    return face;
  }

  function resolveFaceImageDisplay(face, card) {
    face = face || {};
    card = card || {};
    var fit = normalizeImageFit(face.imageFit || card.imageFit);
    var pos = normalizeImagePosition(face.imagePosition || card.imagePosition);
    var scale = normalizeImageScale(face.imageScale != null ? face.imageScale : card.imageScale);
    var positionMap = {
      center: 'center center',
      top: 'center top',
      bottom: 'center bottom',
      left: 'left center',
      right: 'right center'
    };
    var backgroundPosition = positionMap[pos] || 'center center';
    var backgroundSize;
    if (fit === 'fill') {
      backgroundSize = '100% 100%';
    } else if (fit === 'contain') {
      backgroundSize = scale !== 100 ? scale + '% auto' : 'contain';
    } else if (scale !== 100) {
      backgroundSize = scale + '%';
    } else {
      backgroundSize = 'cover';
    }
    return {
      fit: fit,
      scale: scale,
      backgroundSize: backgroundSize,
      backgroundPosition: backgroundPosition,
      objectPosition: backgroundPosition
    };
  }

  function renderFaceImageMedia(img, extraClass, face, card) {
    var src = resolveMediaUrl(img);
    if (!src) return '';
    var display = resolveFaceImageDisplay(face || {}, card || {});
    var cls = 'ahp-face-photo ahp-face-photo-img' + (extraClass ? ' ' + extraClass : '');
    var scale = display.scale !== 100 ? 'scale(' + (display.scale / 100) + ')' : 'none';
    return '<img class="' + cls + '" src="' + esc(src) + '" alt="" loading="lazy" decoding="async" draggable="false"'
      + ' style="object-fit:' + esc(display.fit) + ';object-position:' + esc(display.objectPosition)
      + ';transform:' + scale + ';transform-origin:' + esc(display.objectPosition) + ';"'
      + ' onerror="this.classList.add(\'is-broken\');">';
  }

  function renderStartTileMedia(face, card, img, iconHtml, showIcon) {
    if (img) {
      return '<div class="ahp-start-tile-media">' +
        renderFaceImageMedia(img, 'ahp-start-tile-logo', face, card) +
        '</div>';
    }
    if (showIcon && iconHtml) {
      return '<div class="ahp-start-tile-media ahp-start-tile-media-icon">' + iconHtml + '</div>';
    }
    return '<div class="ahp-start-tile-media ahp-start-tile-media-empty" aria-hidden="true"></div>';
  }

  function faceTextInlineStyle(kind, face, card, type, color) {
    // !important so these beat stylesheet rules; font-family uses single quotes only.
    var parts = ['color:' + esc(color || '#ffffff')];
    if (kind === 'title') {
      parts.push('font-size:' + faceTitleSizeDisplay(face, card, type) + 'px !important');
      parts.push('font-weight:' + faceTitleWeight(face, card, type) + ' !important');
      parts.push('font-family:' + resolveFontFamily(face.titleFontFamily || card.titleFontFamily || 'system'));
    } else {
      parts.push('font-size:' + faceSubSizeDisplay(face, card, type) + 'px !important');
      parts.push('font-weight:' + faceSubWeight(face, card, type) + ' !important');
      parts.push('font-family:' + resolveFontFamily(face.subFontFamily || card.subFontFamily || 'system'));
    }
    return parts.join(';') + ';';
  }

  function applyFaceTypographyToElement(faceEl, face, card, type) {
    if (!faceEl) return;
    face = face || {};
    card = card || {};
    type = type || 'stack';
    var vars = faceTypographyVars(face, card, type);
    Object.keys(vars).forEach(function (key) {
      faceEl.style.setProperty(key, vars[key]);
    });
    var titleEl = faceEl.querySelector('.face-title, .face-label, .face-value');
    var subEl = faceEl.querySelector('.face-sub, .face-label');
    if (type === 'stat') {
      titleEl = faceEl.querySelector('.face-value') || titleEl;
      subEl = faceEl.querySelector('.face-label') || subEl;
      if (titleEl) {
        titleEl.style.setProperty('font-size', faceSubSizeDisplay(face, card, type) + 'px', 'important');
        titleEl.style.setProperty('font-weight', String(faceSubWeight(face, card, type)), 'important');
      }
      if (subEl && subEl !== titleEl) {
        subEl.style.setProperty('font-size', faceTitleSizeDisplay(face, card, type) + 'px', 'important');
        subEl.style.setProperty('font-weight', String(faceTitleWeight(face, card, type)), 'important');
      }
      return;
    }
    if (titleEl) {
      titleEl.style.setProperty('font-size', faceTitleSizeDisplay(face, card, type) + 'px', 'important');
      titleEl.style.setProperty('font-weight', String(faceTitleWeight(face, card, type)), 'important');
      titleEl.style.setProperty('font-family', resolveFontFamily(face.titleFontFamily || card.titleFontFamily || 'system'));
    }
    if (subEl && subEl !== titleEl) {
      subEl.style.setProperty('font-size', faceSubSizeDisplay(face, card, type) + 'px', 'important');
      subEl.style.setProperty('font-weight', String(faceSubWeight(face, card, type)), 'important');
      subEl.style.setProperty('font-family', resolveFontFamily(face.subFontFamily || card.subFontFamily || 'system'));
    }
    var iconEl = faceEl.querySelector('.face-icon');
    if (iconEl) {
      iconEl.style.setProperty('font-size', faceIconSizeDisplay(face, card, type) + 'px', 'important');
    }
  }

  function syncCardTypographyDom(card, root) {
    if (!card) return;
    card = ensureFaces(card);
    var scope = root || document;
    var nodes = [];
    if (scope.querySelectorAll) {
      scope.querySelectorAll('.ahp-cube-face[data-face-index], .ahp-face-preview[data-face]').forEach(function (node) {
        nodes.push(node);
      });
    }
    nodes.forEach(function (node) {
      var idx = parseInt(node.getAttribute('data-face-index') || node.getAttribute('data-face'), 10);
      if (isNaN(idx)) return;
      var face = card.faces[idx] || {};
      var type = normalizeFaceContentType(face, card) || 'stack';
      applyFaceTypographyToElement(node, face, card, type === 'mixed' ? 'mixed' : (type === 'image' ? 'image' : 'stack'));
    });
  }

  function renderStartTileCaption(title, titleColor, subtitle, subColor, face, card, type) {
    title = String(title || '').trim();
    subtitle = String(subtitle || '').trim();
    if (!title && !subtitle) return '';
    face = face || {};
    card = card || {};
    type = type || 'stack';
    return '<div class="ahp-start-tile-caption">' +
      (title ? '<div class="face-title" style="' + faceTextInlineStyle('title', face, card, type, titleColor) + '">' + esc(title) + '</div>' : '') +
      (subtitle ? '<div class="face-sub" style="' + faceTextInlineStyle('sub', face, card, type, subColor || titleColor) + '">' + esc(subtitle) + '</div>' : '') +
      '</div>';
  }

  function applyEditorImagePreviewStyles() {
    var preview = document.getElementById('ceImagePreview');
    var previewImg = document.getElementById('ceImagePreviewImg');
    if (!previewImg) return;
    var fitNode = document.getElementById('ceFaceImageFit');
    var posNode = document.getElementById('ceFaceImagePosition');
    var scaleNode = document.getElementById('ceFaceImageScale');
    var display = resolveFaceImageDisplay({
      imageFit: fitNode ? fitNode.value : 'cover',
      imagePosition: posNode ? posNode.value : 'center',
      imageScale: scaleNode ? scaleNode.value : 100
    }, {});
    if (preview) {
      preview.style.setProperty('--ahp-image-object-fit', display.fit);
      preview.style.setProperty('--ahp-image-position', display.objectPosition);
    }
    previewImg.style.objectFit = display.fit;
    previewImg.style.objectPosition = display.objectPosition;
    previewImg.style.width = '100%';
    previewImg.style.height = '100%';
    previewImg.style.transform = display.scale !== 100 ? 'scale(' + (display.scale / 100) + ')' : '';
    previewImg.style.transformOrigin = display.objectPosition;
  }

  function faceImageBackground(img, face, card) {
    if (img) return '#0b1220';
    return faceBg(face, card);
  }

  function imageFaceShellStyle(face, card, type, img) {
    var bg = faceImageBackground(img, face, card);
    var display = resolveFaceImageDisplay(face || {}, card || {});
    return styleAttr(Object.assign({
      background: bg,
      '--ahp-face-bg': bg,
      '--ahp-image-object-fit': display.fit,
      '--ahp-image-bg-size': display.backgroundSize || display.fit,
      '--ahp-image-position': display.objectPosition,
      'border-radius': faceRadius(face, card) + 'px',
      '--ahp-face-title-color': faceTitleColor(face, card),
      '--ahp-face-sub-color': faceSubColor(face, card),
      '--ahp-face-icon-color': faceIconColor(face, card)
    }, faceTypographyVars(face, card, type)));
  }

  function clearImagePlaceholderOverlayTitles(card) {
    if (!card || card.type !== 'image') return card;
    ensureFaces(card);
    card.faces.forEach(function (face) {
      if (!readImageOverlayLabel(face) && (face.title || face.label)) {
        delete face.title;
        delete face.label;
      }
    });
    var cardTitle = String(card.title || card.label || '').trim().toLowerCase();
    if (IMAGE_PLACEHOLDER_OVERLAY_TITLES[cardTitle]) {
      card.title = '';
      card.label = '';
    }
    return card;
  }

  function ensureImageBlockDefaults(card) {
    if (!card || card.type !== 'image') return card;
    ensureFaces(card);
    // Photo tiles default to cover (edge-to-edge). Explicit contain stays for logos.
    if (!card.imageFit) card.imageFit = 'cover';
    if (!card.imagePosition) card.imagePosition = 'center';
    if (card.imageScale == null || card.imageScale === '') card.imageScale = 100;
    card.faces.forEach(function (face) {
      if (!face.imageFit) face.imageFit = card.imageFit || 'cover';
      ensureFaceImageDisplayDefaults(face, card);
    });
    clearImagePlaceholderOverlayTitles(card);
    return card;
  }

  function uploadFaceImage(file, meta) {
    meta = meta || {};
    if (!file || !cfg.apiBase) return Promise.reject(new Error('Upload unavailable'));
    var fd = new FormData();
    fd.append('image', file);
    if (meta.cardId) fd.append('card_id', meta.cardId);
    if (meta.faceIndex != null) fd.append('face_index', String(meta.faceIndex));
    if (cfg.csrfName && cfg.csrfHash) fd.append(cfg.csrfName, cfg.csrfHash);
    return fetch(cfg.apiBase + '/upload-face-image', {
      method: 'POST',
      body: fd,
      credentials: 'same-origin'
    }).then(function (res) {
      return res.text().then(function (text) {
        var data = null;
        try { data = text ? JSON.parse(text) : null; } catch (e) { /* non-json */ }
        if (!res.ok) {
          var msg = (data && data.message) || ('Upload failed (HTTP ' + res.status + ')');
          return { success: false, message: msg };
        }
        return data || { success: false, message: 'Empty server response' };
      });
    });
  }

  function bindFaceImageUpload(panel) {
    if (!panel) return;
    var fileInput = document.getElementById('ceFaceImageUpload');
    if (!fileInput || fileInput.dataset.bound === '1') return;
    fileInput.dataset.bound = '1';
    fileInput.addEventListener('change', function () {
      var file = fileInput.files && fileInput.files[0];
      if (!file) return;
      var btn = document.getElementById('ceFaceImageUploadBtn');
      if (btn) {
        btn.disabled = true;
        btn.classList.add('is-uploading');
      }
      uploadFaceImage(file, {
        cardId: selectedCardId,
        faceIndex: activeFaceIndex
      }).then(function (res) {
        if (cfg.csrfName && res && res.csrfHash) cfg.csrfHash = res.csrfHash;
        if (!res || !res.success || !res.url) {
          showToast((res && res.message) || 'Image upload failed');
          return;
        }
        var urlInput = document.getElementById('ceFaceImage');
        if (urlInput) urlInput.value = res.url;
        var preview = document.getElementById('ceImagePreview');
        var previewImg = document.getElementById('ceImagePreviewImg');
        if (preview) preview.classList.add('has-image');
        if (previewImg) {
          previewImg.hidden = false;
          previewImg.src = res.url;
        }
        applyEditorImagePreviewStyles();
        applyCardEditor();
        showToast('Image uploaded - shown on cube face ' + (activeFaceIndex + 1));
      }).catch(function () {
        showToast('Image upload failed');
      }).finally(function () {
        fileInput.value = '';
        if (btn) {
          btn.disabled = false;
          btn.classList.remove('is-uploading');
        }
      });
    });
  }

  function faceShellStyle(face, card, type) {
    var bg = faceBg(face, card);
    return styleAttr(Object.assign({
      background: bg,
      '--ahp-face-bg': bg,
      'border-radius': faceRadius(face, card) + 'px',
      '--ahp-face-title-color': faceTitleColor(face, card),
      '--ahp-face-sub-color': faceSubColor(face, card),
      '--ahp-face-icon-color': faceIconColor(face, card)
    }, faceTypographyVars(face, card, type)));
  }

  function faceTitleColor(face, card) {
    if (face.titleTextColor) return face.titleTextColor;
    if (face.textColor) return face.textColor;
    if (card.textColor) return card.textColor;
    return '#ffffff';
  }

  function faceSubColor(face, card) {
    if (face.subTextColor) return face.subTextColor;
    if (face.textColor) return face.textColor;
    if (card.textColor) return card.textColor;
    return '#ffffff';
  }

  function faceIconColor(face, card) {
    if (face.iconColor) return face.iconColor;
    if (card.iconColor) return card.iconColor;
    return '#ffffff';
  }

  function cssStyleValue(val) {
    if (val == null || val === '') return '';
    var s = String(val);
    if (/^span \d+$/i.test(s)) return s;
    // Inline HTML style="..." attributes — never wrap values in double quotes;
    // quoting breaks gradients (background:"linear-gradient(...)" truncates the attr).
    return s;
  }

  function styleAttr(obj) {
    var parts = [];
    Object.keys(obj).forEach(function (k) {
      if (obj[k] != null && obj[k] !== '') {
        parts.push(k + ':' + cssStyleValue(obj[k]));
      }
    });
    return parts.join(';') + (parts.length ? ';' : '');
  }

  function getGridColumns() {
    var t = getTheme();
    return normalizeGridColumns(t.gridColumns, 3);
  }

  function getGridRows() {
    var t = getTheme();
    return normalizeGridRows(t.gridRows, 3);
  }

  function normalizeGridColumns(value, fallback) {
    fallback = fallback == null ? 3 : fallback;
    var n = parseInt(value, 10);
    if (isNaN(n)) n = fallback;
    return Math.max(1, Math.min(12, n));
  }

  function normalizeGridRows(value, fallback) {
    fallback = fallback == null ? 3 : fallback;
    var n = parseInt(value, 10);
    if (isNaN(n)) n = fallback;
    return Math.max(1, Math.min(16, n));
  }

  function normalizeGridRowHeight(value, fallback) {
    fallback = fallback == null ? 96 : fallback;
    var n = parseInt(value, 10);
    if (isNaN(n)) n = fallback;
    return Math.max(40, Math.min(160, n));
  }

  function defaultGridRowHeight() {
    var t = getTheme();
    return normalizeGridRowHeight(t.gridRowHeight, 96);
  }

  function clampAllCardSpans() {
    var maxCol = getGridColumns();
    var maxRow = getGridRows();
    (layout.cards || []).forEach(function (card) {
      ensureCardSize(card);
      if ((parseInt(card.colSpan, 10) || 1) > maxCol) card.colSpan = maxCol;
      if ((parseInt(card.rowSpan, 10) || 1) > maxRow) card.rowSpan = maxRow;
      clampCardPosition(card);
    });
    ensureAllCardPositions(false);
  }

  function normalizeGridGap(value, fallback) {
    if (window.AhpSchemaV2 && typeof window.AhpSchemaV2.resolveGridGap === 'function') {
      return window.AhpSchemaV2.resolveGridGap({ gridGap: value });
    }
    fallback = fallback == null ? 4 : fallback;
    var n = parseInt(value, 10);
    if (isNaN(n)) n = fallback;
    return Math.max(0, Math.min(24, n));
  }

  function resolveSquareGridRowHeight() {
    var t = getTheme();
    var cols = getGridColumns();
    var gap = normalizeGridGap(t.gridGap, 4);
    var grid = el.grid || document.getElementById('ahpGrid');
    // Prefer a real 1x1 card width — matches painted columns, not the phone chrome.
    var probe = grid && grid.querySelector('.ahp-card[data-col-span="1"], .ahp-card[data-col-span="1"]');
    if (probe && probe.offsetWidth > 20) {
      return normalizeGridRowHeight(probe.offsetWidth, 96);
    }
    var width = grid && (grid.clientWidth || grid.offsetWidth) ? (grid.clientWidth || grid.offsetWidth) : 0;
    if (width < 60 && el.phone) {
      var screen = el.phone.querySelector('.ahp-phone-scroll, .ahp-home-page, .ahp-phone-screen') || el.phone;
      width = screen.clientWidth || screen.offsetWidth || 0;
      if (width > 32) width -= 32;
    }
    if (width >= 60 && cols > 0) {
      var cell = Math.floor((width - gap * Math.max(0, cols - 1)) / cols);
      return normalizeGridRowHeight(cell, 96);
    }
    return defaultGridRowHeight();
  }

  function syncGridLayoutVars() {
    var t = getTheme();
    // Always square 1x1 cells for every account (row track = column width).
    t.squareTiles = true;
    t.gridRowHeight = resolveSquareGridRowHeight();
    var gapPx = normalizeGridGap(t.gridGap, 4) + 'px';
    var rowHeight = defaultGridRowHeight();
    var rowHeightPx = rowHeight + 'px';
    var cols = String(getGridColumns());
    var nodes = [
      el.phone,
      document.getElementById('ahpPhone'),
      document.querySelector('.ahp-phone-screen'),
      document.getElementById('ahpHomePage'),
      el.grid,
      document.getElementById('ahpGrid')
    ];
    nodes.forEach(function (node) {
      if (!node) return;
      node.style.setProperty('--ahp-grid-gap', gapPx);
      node.style.setProperty('--ahp-grid-columns', cols);
      node.style.setProperty('--ahp-grid-rows', String(getGridRows()));
      node.style.setProperty('--ahp-grid-row-height', rowHeightPx);
      node.style.setProperty('--ahp-cell-size', rowHeightPx);
    });
    if (el.grid) {
      el.grid.style.gap = gapPx;
      el.grid.style.columnGap = gapPx;
      el.grid.style.rowGap = gapPx;
      el.grid.style.gridAutoRows = rowHeightPx;
      el.grid.style.gridTemplateRows = 'repeat(' + getGridRows() + ', ' + rowHeightPx + ')';
    }
    var rowHeightInput = document.getElementById('toolGridRowHeight');
    if (rowHeightInput) {
      rowHeightInput.value = String(rowHeight);
      if (typeof updateRangeUi === 'function') updateRangeUi(rowHeightInput, 'px');
    }
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

  function applyTheme() {
    var t = getTheme();
    var root = el.phone || document.getElementById('ahpPhone');
    if (!root) return;

    var textPrimary = t.textPrimary || '#111827';
    var textSecondary = t.textSecondary || '#374151';
    var textMuted = t.textMuted || '#6b7280';
    var phoneBg = t.phoneBg || '#f3f4f6';
    var alertBg = t.alertBg || '#ffffff';
    var accent = t.accentColor || '#111827';
    var navActive = t.navActiveColor || '#111827';
    var navBg = t.navBg || '#ffffff';
    var welcomeText = t.welcomeTextColor || '#111827';
    var isLight = t.bgStyle === 'light';
    var surface = t.surfaceColor || t.alertBg || (isLight ? '#ffffff' : '#151b2e');
    var accentGreen = t.accentGreen || '#22c55e';
    var borderColor = isLight ? 'rgba(17,24,39,.08)' : 'rgba(255,255,255,.10)';
    var cardShadow = isLight
      ? '0 18px 40px rgba(15,23,42,.08)'
      : '0 18px 40px rgba(0,0,0,.35)';
    var logoutColors = resolveLogoutButtonColors(t);

    var themeVars = {
      '--ahp-phone-bg': phoneBg,
      '--ahp-surface': surface,
      '--ahp-border': borderColor,
      '--ahp-card-shadow': cardShadow,
      '--ahp-welcome-start': t.welcomeStart || '#fbbf24',
      '--ahp-welcome-end': t.welcomeEnd || '#f97316',
      '--ahp-alert-bg': alertBg,
      '--ahp-accent': accent,
      '--ahp-accent-green': accentGreen,
      '--ahp-nav-active': navActive,
      '--ahp-nav-bg': navBg,
      '--ahp-text-primary': textPrimary,
      '--ahp-text-secondary': textSecondary,
      '--ahp-text-muted': textMuted,
      '--ahp-welcome-text': welcomeText,
      '--ahp-logout-bg': logoutColors.background,
      '--ahp-logout-text': logoutColors.text,
      '--ahp-grid-gap': normalizeGridGap(t.gridGap, 4) + 'px',
      '--ahp-grid-row-height': defaultGridRowHeight() + 'px',
      '--ahp-grid-columns': String(getGridColumns()),
      '--ahp-card-speed': (t.cardSpeed || 16) + 's'
    };

    [root, root.querySelector('.ahp-phone-screen')].concat(
      Array.prototype.slice.call(root.querySelectorAll('.ahp-mobile'))
    ).forEach(function (node) {
      if (!node) return;
      Object.keys(themeVars).forEach(function (key) {
        node.style.setProperty(key, themeVars[key]);
      });
    });

    syncGridLayoutVars();

    root.style.background = phoneBg;
    var screen = root.querySelector('.ahp-phone-screen');
    if (screen) {
      screen.style.background = phoneBg;
      screen.setAttribute('data-theme', isLight ? 'light' : 'dark');
    }
    root.querySelectorAll('.ahp-mobile').forEach(function (page) { page.style.background = 'transparent'; page.style.color = textPrimary; });

    var logoEl = document.getElementById('ahpLogoIcon');
    if (logoEl) {
      var logoUrl = cfg.logoUrl || 'https://efactor.tech/files/home/Logo_new.png';
      logoEl.classList.add('ahp-m-logo-img');
      logoEl.innerHTML = '<img src="' + esc(logoUrl) + '" alt="eFactor">';
    }

    var alerts = document.getElementById('ahpAlerts');
    var welcome = document.getElementById('ahpWelcome');
    var bottomNav = document.getElementById('ahpBottomNav');
    if (welcome) {
      welcome.classList.toggle('ahp-is-hidden', !shouldShowWelcomeBanner());
    }
    if (alerts) {
      alerts.classList.toggle('ahp-is-hidden', !shouldShowAlertsStrip());
      alerts.style.background = alertBg;
    }
    if (bottomNav) {
      bottomNav.classList.toggle('ahp-is-hidden', t.showBottomNav === false);
      bottomNav.style.background = navBg;
    }

    var avatarBtn = document.getElementById('ahpHomeAvatar');
    if (avatarBtn) {
      avatarBtn.style.background = 'linear-gradient(135deg,' + (t.welcomeStart || '#fbbf24') + ',' + (t.welcomeEnd || '#f97316') + ')';
    }

    var badge = document.getElementById('ahpAlertsBadge');
    if (badge) {
      var showBadge = layout.alerts.showBadge !== false;
      badge.classList.toggle('ahp-is-hidden', !showBadge);
    }

    var badgeText = document.getElementById('ahpBadgeText');
    if (badgeText) badgeText.textContent = layout.alerts.badgeText || 'real-time';

    renderHeader();
    renderBottomNav();
    refreshThemedPreview();
    refreshCubePlayback(root);
  }

  function refreshLegacyCardThemedPreview() {
    if (layout.activePage === 'home' && el.grid && layout.cards && layout.cards.length) {
      layout.cards.forEach(function (card) {
        updateCardSlot(card.id);
      });
    } else if (layout.activePage === 'devices') {
      loadDevicesFromIotApi(false);
    }
    if (selectedCardId && cardEditorCardId === selectedCardId) {
      var card = layout.cards.find(function (c) { return c.id === selectedCardId; });
      if (card) updateCardEditorPreview(card);
    }
  }

  function renderCubeFace(card, face, idx) {
    var blockType = normalizeHomeBlockType(card.type);
    var type = normalizeFaceContentType(face, card) || blockType;
    var shell = faceShellStyle(face, card, type === 'stack' ? 'link' : type);
    var routeAttr = ' data-route="' + esc(face.route || card.pageRoute || '') + '"';
    var titleColor = faceTitleColor(face, card);
    var subColor = faceSubColor(face, card);
    var iconColor = faceIconColor(face, card);
    var showIcon = face.hideIcon !== true;
    var iconClass = 'fas ' + esc(normalizeFaIcon(face.icon || card.icon || (type === 'stack' ? 'fa-layer-group' : type === 'mixed' ? 'fa-th-large' : 'fa-image')));
    var iconType = type === 'image' ? 'image' : (type === 'mixed' ? 'mixed' : 'stack');
    var iconHtml = showIcon
      ? '<i class="' + iconClass + ' face-icon" style="color:' + esc(iconColor || '#ffffff') + ';font-size:' + faceIconSizeDisplay(face, card, iconType) + 'px"></i>'
      : '';
    var faceText = readFaceEditorText(card, face);
    var title = faceText.label || face.title || face.label || card.title || card.label || '';
    var subtitle = faceText.subtitle || face.subtitle || card.subtitle || '';
    var value = face.value != null && face.value !== '' ? face.value : (card.value || '');
    var faceImg = String(face.image || ((idx === 0) ? card.image : '') || '').trim();
    var imageFit = normalizeImageFit(face.imageFit || card.imageFit || 'cover');

    // Image block: full-bleed photo background (cover) so the tile has no blank bars. Title bottom-left.
    if (type === 'image') {
      if (!faceImg) {
        return '<div class="ahp-cube-face face-' + idx + ' ahp-start-tile ahp-face-image" data-face-index="' + idx + '"' + routeAttr + ' style="' + shell + '">' +
          renderStartTileMedia(face, card, '', iconHtml, showIcon) +
          renderStartTileCaption(title || 'Add image', '#ffffff', subtitle, '#ffffff', face, card, 'image') +
          '</div>';
      }
      var displayFace = Object.assign({}, face, {
        imageFit: imageFit,
        imagePosition: face.imagePosition || card.imagePosition || 'center',
        imageScale: face.imageScale != null ? face.imageScale : (card.imageScale != null ? card.imageScale : 100)
      });
      var imgDisplay = resolveFaceImageDisplay(displayFace, card);
      // Neutral underlay only — the photo itself is the tile background (cover = no blank bars).
      var imageShell = styleAttr(Object.assign({
        background: '#0b1220',
        '--ahp-face-bg': '#0b1220',
        '--ahp-image-object-fit': imgDisplay.fit,
        '--ahp-image-bg-size': imgDisplay.backgroundSize || imgDisplay.fit,
        '--ahp-image-position': imgDisplay.objectPosition,
        'border-radius': faceRadius(face, card) + 'px',
        '--ahp-face-title-color': '#ffffff',
        '--ahp-face-sub-color': '#ffffff',
        color: '#ffffff'
      }, faceTypographyVars(face, card, 'image')));
      return '<div class="ahp-cube-face face-' + idx + ' ahp-cube-image ahp-face-image ahp-image-fit-' + esc(imgDisplay.fit) + '" data-face-index="' + idx + '"' + routeAttr + ' style="' + imageShell + '">' +
        renderFaceImageMedia(faceImg, 'ahp-cube-face-img ahp-face-fill-img', displayFace, card) +
        renderStartTileCaption(title, '#ffffff', subtitle, '#ffffff', face, card, 'image') +
        '</div>';
    }

    // Mixed / Stack: centered vertical stack — icon/logo → title → subtitle
    if (value !== '' && value != null && String(title).trim() === String(value).trim()) {
      title = face.label || card.label || card.title || '';
    }
    if ((!title || !String(title).trim()) && value !== '' && value != null) {
      title = String(value);
    }
    var stackType = type === 'mixed' ? 'mixed' : 'stack';
    var startShell = styleAttr(Object.assign({
      background: faceBg(face, card),
      '--ahp-face-bg': faceBg(face, card),
      'border-radius': faceRadius(face, card) + 'px',
      '--ahp-face-title-color': titleColor || '#ffffff',
      '--ahp-face-sub-color': subColor || '#ffffff',
      '--ahp-face-icon-color': iconColor || '#ffffff',
      color: '#ffffff'
    }, faceTypographyVars(face, card, stackType)));
    var logoFace = Object.assign({}, face, {
      imageFit: 'contain',
      imagePosition: 'center',
      imageScale: face.imageScale != null ? face.imageScale : 100
    });
    return '<div class="ahp-cube-face face-' + idx + ' ahp-start-tile ahp-face-' + esc(type) + (faceImg ? ' ahp-start-tile-has-logo' : '') + '" data-face-index="' + idx + '"' + routeAttr + ' style="' + startShell + '">' +
      renderStartTileMedia(logoFace, card, faceImg, iconHtml, !faceImg && showIcon) +
      renderStartTileCaption(title, titleColor || '#ffffff', subtitle, subColor || '#ffffff', face, card, stackType) +
      '</div>';
  }

  function renderEditFaceBoard(card) {
    card = ensureFaces(card);
    var radius = cardRadius(card);
    return '<div class="ahp-edit-face-board" data-card-id="' + esc(card.id) + '" style="--ahp-card-radius:' + radius + 'px">' +
      card.faces.map(function (face, i) {
        var bg = faceBg(face, card);
        var titleColor = faceTitleColor(face, card);
        var subColor = faceSubColor(face, card);
        var iconColor = faceIconColor(face, card);
        var text = readFaceEditorText(card, face);
        var label = text.label || ('Face ' + (i + 1));
        var sub = text.subtitle || '';
        return '<button type="button" class="ahp-edit-face-card' + (i === activeFaceIndex ? ' active' : '') + '" data-face-index="' + i + '" style="background:' + esc(bg) + ';color:' + esc(titleColor) + ';border-radius:' + faceRadius(face, card) + 'px">' +
          '<span class="ahp-edit-face-num">Face ' + (i + 1) + '</span>' +
          '<i class="fas ' + esc(face.icon || card.icon || 'fa-cube') + '" style="color:' + esc(iconColor) + '"></i>' +
          '<strong style="color:' + esc(titleColor) + '">' + esc(label) + '</strong>' +
          (sub ? '<small style="color:' + esc(subColor) + '">' + esc(sub) + '</small>' : '') +
        '</button>';
      }).join('') + '</div>';
  }

  function renderCubeCard(card, index) {
    card = ensureFaces(card);
    var freeze = editMode && activeMenuKey === 'cards' && selectedCardId === card.id && cardEditorCardId === card.id;
    var offset = getCardAnimOffset(card.id, index);
    var facesHtml = card.faces.map(function (f, i) { return renderCubeFace(card, f, i); }).join('');
    var defFace = getDefaultFace(card);
    var faceIndex = freeze ? activeFaceIndex : defFace;
    var radius = cardRadius(card);
    return '<div class="ahp-cube' + (freeze ? ' paused ahp-show-face' : '') + '" style="--ahp-cube-delay:' + offset + 's;--ahp-card-radius:' + radius + 'px;--ahp-face-index:' + faceIndex + '">' + facesHtml + '</div>';
  }

  function renderFacePreview(card, face, idx) {
    var type = normalizeFaceContentType(face, card) || normalizeHomeBlockType(card.type) || 'stack';
    var text = readFaceEditorText(card, face);
    var label = text.label || ('Face ' + (idx + 1));
    var sub = text.subtitle || '';
    var shell = faceShellStyle(face, card, type);
    var showIcon = face.hideIcon !== true;
    var previewClass = 'ahp-face-preview' + (idx === activeFaceIndex ? ' active' : '') +
      (type === 'stat' ? ' ahp-face-preview-stat' : '') +
      (type === 'image' ? ' ahp-face-preview-image' : '');
    var iconSize = faceIconSizeDisplay(face, card, type);
    var iconHtml = showIcon
      ? '<i class="fas ' + esc(normalizeFaIcon(face.icon || card.icon || 'fa-cube')) + ' face-icon" style="font-size:' + iconSize + 'px;color:' + esc(faceIconColor(face, card)) + '"></i>'
      : '';
    if (type === 'image') {
      var img = String(face.image || card.image || '').trim();
      var imageShell = imageFaceShellStyle(face, card, type, img);
      return '<button type="button" class="' + previewClass + '" data-face="' + idx + '" style="' + imageShell + '">' +
        renderFaceImageMedia(img, 'ahp-face-preview-img', face, card) +
        '<span class="ahp-face-drag-handle" title="Drag to reorder" aria-label="Drag to reorder"><i class="fas fa-grip-lines"></i></span>' +
        (img ? '' : iconHtml) +
        '<strong class="face-title" style="' + faceTextInlineStyle('title', face, card, 'image', faceTitleColor(face, card)) + '">' + esc(label) + '</strong>' +
        (sub ? '<span class="face-sub" style="' + faceTextInlineStyle('sub', face, card, 'image', faceSubColor(face, card)) + '">' + esc(sub) + '</span>' : '') +
        (!img ? '<span class="ahp-face-image-hint">No image URL</span>' : '') +
        '</button>';
    }
    if (type === 'stat') {
      return '<button type="button" class="' + previewClass + '" data-face="' + idx + '" style="' + shell + '">' +
        '<span class="ahp-face-drag-handle" title="Drag to reorder" aria-label="Drag to reorder"><i class="fas fa-grip-lines"></i></span>' +
        iconHtml +
        '<strong class="face-value" style="' + faceTextInlineStyle('sub', face, card, 'stat', faceSubColor(face, card)) + '">' + esc(label) + '</strong>' +
        (sub ? '<span class="face-label" style="' + faceTextInlineStyle('title', face, card, 'stat', faceTitleColor(face, card)) + '">' + esc(sub) + '</span>' : '') +
        '</button>';
    }
    return '<button type="button" class="' + previewClass + '" data-face="' + idx + '" style="' + shell + '">' +
      '<span class="ahp-face-drag-handle" title="Drag to reorder" aria-label="Drag to reorder"><i class="fas fa-grip-lines"></i></span>' +
      iconHtml +
      '<strong class="face-title" style="' + faceTextInlineStyle('title', face, card, type, faceTitleColor(face, card)) + '">' + esc(label) + '</strong>' +
      (sub ? '<span class="face-sub" style="' + faceTextInlineStyle('sub', face, card, type, faceSubColor(face, card)) + '">' + esc(sub) + '</span>' : '') +
      '</button>';
  }

  function renderCardPreviewStrip(card) {
    card = ensureFaces(card);
    return '<div class="ahp-card-face-preview-wrap">' +
      '<div class="ahp-card-face-preview-head"><span>Face preview</span><small>Click to edit - drag to reorder</small></div>' +
      '<div class="ahp-card-face-preview-grid">' +
      card.faces.map(function (face, i) { return renderFacePreview(card, face, i); }).join('') +
      '</div></div>';
  }

  function refreshMobileCardFacePreview() {
    if (!selectedCardId) return;
    updateCardSlot(selectedCardId);
    refreshCubePlayback(el.grid);
  }

  function selectActiveFace(faceIndex, options) {
    options = options || {};
    faceIndex = Math.max(0, Math.min(3, parseInt(faceIndex, 10) || 0));
    var prevFace = activeFaceIndex;
    // Persist the face we are leaving — never write the new face's picker into the old face.
    if (selectedCardId && prevFace !== faceIndex) {
      applyCardEditor({ faceIndex: prevFace, skipColorFieldResync: true });
    } else if (selectedCardId) {
      applyCardEditor({ faceIndex: prevFace, skipColorFieldResync: true });
    }
    activeFaceIndex = faceIndex;
    if (options.rebuildEditor !== false) {
      syncCardEditor(true);
    } else {
      var panel = document.getElementById('ahpCardEditor');
      if (panel) {
        panel.querySelectorAll('.ahp-face-preview').forEach(function (node) {
          var fi = parseInt(node.getAttribute('data-face'), 10) || 0;
          node.classList.toggle('active', fi === activeFaceIndex);
        });
        var sectionTitle = panel.querySelector('.ahp-face-editor .ahp-style-section-title');
        if (sectionTitle) sectionTitle.textContent = 'Face ' + (activeFaceIndex + 1) + ' of 4';
        syncCardEditorColorFields(layout.cards.find(function (c) { return c.id === selectedCardId; }));
      }
    }
    updateSelectedCardHighlight();
    if (selectedCardId) {
      var slot = el.grid && el.grid.querySelector('[data-card-id="' + selectedCardId + '"]');
      if (slot) {
        var cube = slot.querySelector('.ahp-cube:not(.ahp-application-cube)');
        if (cube) {
          cube.style.setProperty('--ahp-face-index', activeFaceIndex);
        }
      }
    }
    syncSelectedCardCubeFace({ fromFace: prevFace, animate: prevFace !== faceIndex });
    refreshCubePlayback(el.grid);
  }

  function destroyFacePreviewSortable() {
    if (facePreviewSortable) {
      facePreviewSortable.destroy();
      facePreviewSortable = null;
    }
  }

  function initFacePreviewSortable(panel) {
    destroyFacePreviewSortable();
    if (!panel || typeof Sortable === 'undefined') return;
    var grid = panel.querySelector('.ahp-card-face-preview-grid');
    if (!grid) return;
    facePreviewSortable = Sortable.create(grid, {
      animation: 150,
      handle: '.ahp-face-drag-handle',
      draggable: '.ahp-face-preview',
      onStart: function () {
        commitLayoutHistoryBeforeChange();
      },
      onEnd: function (evt) {
        if (evt.oldIndex === evt.newIndex) return;
        var card = layout.cards.find(function (c) { return c.id === selectedCardId; });
        if (!card) return;
        ensureFaces(card);
        var faces = card.faces.slice();
        var moved = faces.splice(evt.oldIndex, 1)[0];
        faces.splice(evt.newIndex, 0, moved);
        card.faces = faces;
        card.defaultFace = 0;
        if (activeFaceIndex === evt.oldIndex) activeFaceIndex = evt.newIndex;
        else if (evt.oldIndex < activeFaceIndex && evt.newIndex >= activeFaceIndex) activeFaceIndex--;
        else if (evt.oldIndex > activeFaceIndex && evt.newIndex <= activeFaceIndex) activeFaceIndex++;
        applyCardEditor();
        updateCardEditorPreview(card);
        initFacePreviewSortable(panel);
        bindFacePreviewClicks(panel);
        refreshMobileCardFacePreview();
        markLayoutDirty({ historyNow: true });
      }
    });
  }

  function bindFacePreviewClicks(panel) {
    if (!panel) return;
    panel.querySelectorAll('.ahp-face-preview').forEach(function (node) {
      node.addEventListener('click', function (e) {
        if (e.target.closest('.ahp-face-drag-handle')) return;
        selectActiveFace(parseInt(node.getAttribute('data-face'), 10) || 0, { rebuildEditor: true });
      });
    });
  }

  function updateCardEditorPreview(card) {
    var panel = document.getElementById('ahpCardEditor');
    if (!panel || !card) return;
    card = ensureFaces(card);
    var grid = panel.querySelector('.ahp-card-face-preview-grid');
    if (grid) {
      grid.innerHTML = card.faces.map(function (face, i) {
        return renderFacePreview(card, face, i);
      }).join('');
      syncCardTypographyDom(card, grid);
      bindFacePreviewClicks(panel);
      initFacePreviewSortable(panel);
      return;
    }
    var old = panel.querySelector('.ahp-card-face-preview-wrap');
    if (!old) return;
    var tmp = document.createElement('div');
    tmp.innerHTML = renderCardPreviewStrip(card);
    var fresh = tmp.querySelector('.ahp-card-face-preview-wrap') || tmp.firstElementChild;
    if (fresh) old.replaceWith(fresh);
    syncCardTypographyDom(card, panel);
    bindFacePreviewClicks(panel);
    initFacePreviewSortable(panel);
  }

  function applyColorToAllFaces(card, color) {
    if (!card || !color) return;
    ensureFaces(card);
    card.color = color;
    card.sameFaceColor = true;
    (card.faces || []).forEach(function (face) {
      if (face && typeof face === 'object') face.color = color;
    });
  }

  function applyTextIconColorToAllFaces(card, textColor, iconColor) {
    if (!card) return;
    ensureFaces(card);
    card.sameFaceTextColor = true;
    if (textColor) card.textColor = textColor;
    if (iconColor) card.iconColor = iconColor;
    (card.faces || []).forEach(function (face) {
      if (!face || typeof face !== 'object') return;
      if (textColor) {
        face.textColor = textColor;
        face.titleTextColor = textColor;
        face.subTextColor = textColor;
      }
      if (iconColor) face.iconColor = iconColor;
    });
  }

  function facesShareSameColor(card) {
    if (!card || !Array.isArray(card.faces) || !card.faces.length) return !!card.sameFaceColor;
    if (card.sameFaceColor === true) return true;
    var first = String((card.faces[0] && card.faces[0].color) || card.color || '').trim().toLowerCase();
    if (!first) return false;
    return card.faces.every(function (face) {
      return String((face && face.color) || '').trim().toLowerCase() === first;
    });
  }

  function syncCardEditorColorFields(card) {
    if (!card) return;
    card = ensureFaces(card);
    var face = card.faces[activeFaceIndex] || {};
    var faceBgColor = face.color || card.color || '#7c3aed';
    var textColor = face.textColor || face.titleTextColor || card.textColor || '#ffffff';
    var iconColor = face.iconColor || card.iconColor || '#ffffff';
    setBackgroundColorField('ceFaceColor', faceBgColor);
    setBackgroundColorField('ceFaceTextColor', textColor);
    setBackgroundColorField('ceFaceIconColor', iconColor);
    var allFacesChk = document.getElementById('ceFaceColorAllFaces');
    if (allFacesChk) allFacesChk.checked = card.sameFaceColor === true;
    var allTextChk = document.getElementById('ceFaceTextColorAllFaces');
    if (allTextChk) allTextChk.checked = card.sameFaceTextColor === true;
  }

  function renderSlotHtml(card, index) {
    var area = card.gridArea || GRID_AREAS[index] || card.id;
    var radius = cardRadius(card);
    ensureCardSize(card);
    clampCardPosition(card);
    var slotStyle = styleAttr({
      '--ahp-card-radius': radius + 'px',
      'border-radius': radius + 'px',
      'grid-column': card.col + ' / span ' + card.colSpan,
      'grid-row': card.row + ' / span ' + card.rowSpan
    });
    return '<div class="ahp-card" data-area="' + esc(area) + '" data-card-id="' + esc(card.id) + '" data-card-index="' + index + '" data-col="' + card.col + '" data-row="' + card.row + '" data-col-span="' + card.colSpan + '" data-row-span="' + card.rowSpan + '" style="' + slotStyle + '">' +
      '<button type="button" class="ahp-slot-btn ahp-drag-handle ahp-drag-edge" title="Drag to place anywhere" aria-label="Drag to place anywhere"><i class="fas fa-grip-vertical"></i></button>' +
      '<div class="ahp-slot-actions ahp-slot-actions-tools">' +
      '<button type="button" class="ahp-slot-btn" data-action="edit" title="Edit"><i class="fas fa-pen"></i></button>' +
      '<button type="button" class="ahp-slot-btn ahp-slot-btn-danger" data-action="delete" title="Delete card"><i class="fas fa-trash"></i></button>' +
      '</div>' +
      renderCubeCard(card, index) + '</div>';
  }

  function updateCardSlot(cardId) {
    if (!el.grid) return;
    var card = layout.cards.find(function (c) { return c.id === cardId; });
    var slot = el.grid.querySelector('[data-card-id="' + cardId + '"]');
    if (!card || !slot) return;
    ensureCardSize(card);
    clampCardPosition(card);
    var idx = layout.cards.findIndex(function (c) { return c.id === cardId; });
    var radius = cardRadius(card);
    slot.style.setProperty('--ahp-card-radius', radius + 'px');
    slot.style.borderRadius = radius + 'px';
    slot.style.gridColumn = card.col + ' / span ' + card.colSpan;
    slot.style.gridRow = card.row + ' / span ' + card.rowSpan;
    slot.setAttribute('data-col', String(card.col));
    slot.setAttribute('data-row', String(card.row));
    slot.setAttribute('data-col-span', String(card.colSpan));
    slot.setAttribute('data-row-span', String(card.rowSpan));
    var cubeTarget = slot.querySelector('.ahp-cube:not(.ahp-application-cube), .ahp-edit-face-board');
    if (cubeTarget) cubeTarget.outerHTML = renderCubeCard(card, idx);
    else slot.insertAdjacentHTML('beforeend', renderCubeCard(card, idx));
    syncCardTypographyDom(card, slot);
    lockSelectedCardCubeFace(slot, cardId);
    scheduleCubeGeometry(el.grid);
    refreshCubePlayback(el.grid);
  }

  function lockSelectedCardCubeFace(slot, cardId) {
    if (!editMode || selectedCardId !== cardId || cardEditorCardId !== cardId || !slot) return;
    var cube = slot.querySelector('.ahp-cube:not(.ahp-application-cube)');
    if (!cube) return;
    var angle = cubeAngleForFace(activeFaceIndex, getCardRotationMul(cardId));
    cube.classList.add('paused', 'ahp-show-face');
    cube.style.setProperty('--ahp-face-index', activeFaceIndex);
    cube.classList.remove('ahp-face-animating');
    cube.style.transition = 'none';
    cube.style.transform = cubeParentTransform(angle, cube);
    cube.dataset.ahpCubeAngle = String(angle);
  }

  function renderGridAddPlaceholder() {
    var radius = defaultCardRadius();
    return '<div class="ahp-grid-add-slot" data-add-placeholder="1" style="--ahp-card-radius:' + radius + 'px;border-radius:' + radius + 'px">' +
      '<button type="button" class="ahp-grid-add-trigger" aria-expanded="false" aria-label="Add block">' +
      '<i class="fas fa-plus"></i><span>Add block</span></button>' +
      '<div class="ahp-grid-add-picker" hidden>' +
      '<button type="button" class="ahp-grid-add-option" data-add-type="stack"><i class="fas fa-layer-group"></i> Stack</button>' +
      '<button type="button" class="ahp-grid-add-option" data-add-type="image"><i class="fas fa-image"></i> Image</button>' +
      '<button type="button" class="ahp-grid-add-option" data-add-type="mixed"><i class="fas fa-th-large"></i> Mixed</button>' +
      '</div></div>';
  }

  function cardsHaveOverlap() {
    var occupied = Object.create(null);
    var overlap = false;
    (layout.cards || []).forEach(function (card) {
      if (!card) return;
      ensureCardSize(card);
      var col = getCardCol(card);
      var row = getCardRow(card);
      if (col < 1 || row < 1) {
        overlap = true;
        return;
      }
      cardFootprintCells(col, row, card.colSpan, card.rowSpan).forEach(function (key) {
        if (occupied[key]) overlap = true;
        occupied[key] = card.id;
      });
    });
    return overlap;
  }

  function renderLegacyCardGrid() {
    if (!el.grid || !layout.cards) return;
    assignGridAreas();
    ensureAllCardPositions(false);
    if (cardsHaveOverlap()) ensureAllCardPositions(true);
    if (!layout.cards.length) {
      el.grid.innerHTML = renderGridAddPlaceholder();
      el.grid.classList.remove('edit-sortable');
      destroySortable();
      bindGridAddPlaceholder();
      renderCardPicker();
      updateBlockCount();
      return;
    }
    el.grid.innerHTML = renderGridDropCellsHtml() + layout.cards.map(function (card, i) { return renderSlotHtml(card, i); }).join('');
    if (editMode) {
      el.grid.classList.add('edit-sortable');
      initSortable();
    } else {
      el.grid.classList.remove('edit-sortable');
      destroySortable();
    }
    renderCardPicker();
    renderPagePicker();
    updateBlockCount();
    updateSelectedCardHighlight();
    /* Sync card-speed on the phone element so animations start at the right speed */
    var phone = el.phone || document.getElementById('ahpPhone');
    if (phone) phone.style.setProperty('--ahp-card-speed', (getTheme().cardSpeed || 16) + 's');
    refreshCubePlayback(el.grid);
    scheduleCubeGeometry(el.grid);
    syncCubeRotation(el.grid);
    syncGridLayoutVars();
    requestAnimationFrame(applyPreviewZoom);
  }

  function updateLegacyBlockCount() {
    var node = document.getElementById('ahpBlockCount');
    var n = layout.cards ? layout.cards.length : 0;
    if (node) node.textContent = n + (n === 1 ? ' block' : ' blocks') + ' - scrollable';
  }

  function getProfileDisplayName() {
    var p = getProfilePage();
    var name = String(p.name || '').trim();
    if (name && name.toLowerCase() !== 'profile') {
      return name.split(/\s+/)[0];
    }
    var email = String(p.email || layout.welcome && layout.welcome.email || cfg.userEmail || '').trim();
    if (email.indexOf('@') > 0) return email.split('@')[0];
    return 'Guest';
  }

  var previewFlagsSyncedFromSchema = false;

  function getPublishedSchemaPreferences() {
    var schema = cfg.schema;
    if (!schema || !schema.state || !schema.state.preferences) return null;
    return schema.state.preferences;
  }

  function publishedSchemaHasWelcomeBanner() {
    var schema = cfg.schema;
    if (!schema || !schema.components) return false;
    var home = schema.components['home-page'];
    if (home && Array.isArray(home.children) && home.children.indexOf('welcome-banner') >= 0) return true;
    return !!schema.components['welcome-banner'];
  }

  function publishedSchemaHasAlertsBanner() {
    var schema = cfg.schema;
    if (!schema || !schema.components) return false;
    var home = schema.components['home-page'];
    if (home && Array.isArray(home.children) && home.children.indexOf('alerts-summary') >= 0) return true;
    return !!schema.components['alerts-summary'];
  }

  function syncLayoutThemeFromPublishedSchema() {
    if (previewFlagsSyncedFromSchema) return;
    previewFlagsSyncedFromSchema = true;
    if (!layout.theme) layout.theme = {};
    var prefs = getPublishedSchemaPreferences();
    if (prefs && prefs.showWelcome === false) {
      layout.theme.showWelcome = false;
    } else if (prefs && prefs.showWelcome !== false) {
      layout.theme.showWelcome = true;
    } else if (publishedSchemaHasWelcomeBanner()) {
      layout.theme.showWelcome = true;
    }
    if (prefs && prefs.showAlerts === false && !publishedSchemaHasAlertsBanner()) {
      layout.theme.showAlerts = false;
    } else if (publishedSchemaHasAlertsBanner()) {
      layout.theme.showAlerts = true;
    } else if (prefs && prefs.showAlerts !== false) {
      layout.theme.showAlerts = true;
    } else if (layout.theme.showAlerts == null) {
      layout.theme.showAlerts = true;
    }
  }

  function normalizeWelcomeBannerPreference() {
    var t = getTheme();
    if (t.showWelcome == null) t.showWelcome = true;
    migrateLegacyWelcomeDefault();
  }

  function normalizeAlertsBannerPreference() {
    var t = getTheme();
    if (t.showAlerts == null) t.showAlerts = true;
    migrateLegacyAlertsDefault();
  }

  function migrateLegacyWelcomeDefault() {
    var t = getTheme();
    if (t.showWelcome !== false) return;
    var prefs = getPublishedSchemaPreferences();
    // Respect explicit false from layout / published preferences (e.g. Market template).
    if (prefs && prefs.showWelcome === false) return;
    if (t.showWelcome === false && (t.homeTemplate === 'efactor-market' || layout.homeTemplate === 'efactor-market')) {
      return;
    }
    if (publishedSchemaHasWelcomeBanner() && !(prefs && prefs.showWelcome === false)) {
      // Only revive when the published schema actually wants welcome visible.
      if (prefs && prefs.showWelcome === true) t.showWelcome = true;
      return;
    }
    if (t.showAlerts === false && t.showWelcome == null) {
      t.showWelcome = true;
    }
  }

  function migrateLegacyAlertsDefault() {
    var t = getTheme();
    if (t.showAlerts !== false) return;
    if (publishedSchemaHasAlertsBanner()) {
      t.showAlerts = true;
      return;
    }
    t.showAlerts = true;
  }

  function shouldShowWelcomeBanner() {
    return getTheme().showWelcome !== false;
  }

  function shouldShowAlertsStrip() {
    return getTheme().showAlerts !== false;
  }

  function resolveWelcomeGreetingText(raw) {
    var greeting = String(raw || '').trim();
    if (!greeting || /^\?+$/.test(greeting) || greeting.indexOf('\uFFFD') !== -1) {
      return '\uD83D\uDC4B Welcome back';
    }
    if (/^hey there$/i.test(greeting)) {
      return '\uD83D\uDC4B Welcome back';
    }
    return greeting;
  }

  function renderWelcomeBanner() {
    var w = layout.welcome || {};
    var t = getTheme();
    var banner = document.getElementById('ahpWelcome');
    if (!banner) return;

    var greetEl = document.getElementById('ahpWelcomeGreet');
    var emailEl = document.getElementById('ahpWelcomeEmail');
    var captionEl = document.getElementById('ahpWelcomeCaption');
    var profile = getProfilePage();
    var greeting = resolveWelcomeGreetingText(w.greeting || getVal('toolWelcomeGreet'));
    var email = String(w.email || profile.email || cfg.userEmail || getVal('toolWelcomeEmail') || '').trim();
    var caption = String(w.caption || getVal('toolWelcomeCaption') || 'Manage your ecosystem').trim();

    if (greetEl) greetEl.textContent = greeting;
    if (emailEl) emailEl.textContent = email;
    if (captionEl) captionEl.textContent = caption;
    renderWelcomeAvatar();

    banner.classList.toggle('ahp-is-hidden', !shouldShowWelcomeBanner());
  }

  function renderWelcomeAvatar() {
    var btn = document.getElementById('ahpWelcomeAvatar');
    if (!btn) return;
    var profile = getProfilePage();
    var url = getProfileAvatarUrl(profile);
    var initials = getProfileInitials(profile);
    var img = btn.querySelector('img.ahp-avatar-photo');
    var initialsEl = document.getElementById('ahpWelcomeAvatarInitials');
    if (url) {
      if (!img) {
        img = document.createElement('img');
        img.className = 'ahp-avatar-photo';
        img.id = 'ahpWelcomeAvatarImg';
        img.alt = '';
        btn.insertBefore(img, btn.firstChild);
      }
      img.onerror = function () { showAvatarInitials(btn, img, initialsEl, initials); };
      img.src = url;
      if (initialsEl) {
        initialsEl.textContent = initials;
        initialsEl.classList.add('ahp-is-hidden');
      }
      btn.classList.add('ahp-has-photo');
      return;
    }
    if (img) img.remove();
    btn.classList.remove('ahp-has-photo');
    if (initialsEl) {
      initialsEl.textContent = initials;
      initialsEl.classList.remove('ahp-is-hidden');
    }
  }

  function formatAlertsDate() {
    var d = new Date();
    var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
  }

  function renderHeader() {
    var h = layout.header || {};
    var a = layout.alerts || {};

    var titleEl = document.getElementById('ahpHeaderTitle');
    var subEl = document.getElementById('ahpHeaderSub');
    if (titleEl) titleEl.textContent = h.title || 'eFactor · IoE';
    if (subEl) subEl.textContent = h.subtitle || 'Smart insights & tools';

    var alertCountEl = document.getElementById('ahpAlertCount');
    var alertMeta = document.getElementById('ahpAlertMeta');
    var count = a.count != null ? a.count : 0;
    if (alertCountEl) alertCountEl.textContent = 'Live Alerts ' + count;
    if (alertMeta) alertMeta.textContent = formatAlertsDate() + ' · ' + (a.newUsers || 0) + ' new users';

    var badgeEl = document.getElementById('ahpAlertsCountBadge');
    if (badgeEl) {
      var badgeNum = parseInt(count, 10);
      if (isNaN(badgeNum)) badgeNum = 0;
      badgeEl.textContent = badgeNum > 99 ? '99+' : String(badgeNum);
      badgeEl.classList.toggle('ahp-is-hidden', badgeNum <= 0);
    }

    renderWelcomeBanner();
    syncHeaderAvatarPlacement();
  }

  function syncHeaderAvatarPlacement() {
    var header = document.querySelector('#ahpHomePage .ahp-m-header');
    var welcome = document.getElementById('ahpWelcome');
    var avatar = document.getElementById('ahpWelcomeAvatar');
    var more = header ? header.querySelector('.ahp-m-more') : null;
    if (!header || !welcome || !avatar) return;
    if (!shouldShowWelcomeBanner()) {
      header.appendChild(avatar);
      if (more) more.classList.add('ahp-is-hidden');
    } else if (avatar.parentElement !== welcome) {
      welcome.appendChild(avatar);
      if (more) more.classList.remove('ahp-is-hidden');
    } else if (more) {
      more.classList.remove('ahp-is-hidden');
    }
  }

  function renderBottomNav() {
    normalizeBottomNav();
    var nav = document.getElementById('ahpBottomNav');
    if (!nav || !layout.bottomNav) return;
    var t = getTheme();
    var activeId = layout.activePage || 'home';
    nav.style.setProperty('--ahp-nav-active', t.navActiveColor || '#3b82f6');
    nav.innerHTML = layout.bottomNav.map(function (item) {
      var icon = normalizeFaIcon(item.icon);
      var isActive = item.id === activeId || (!!item.active && activeId === item.id);
      return '<button type="button" class="ahp-nav-item' + (isActive ? ' active' : '') + '" data-nav="' + esc(item.id) + '">' +
        '<i class="fas ' + esc(icon) + '"></i><span>' + esc(item.label) + '</span></button>';
    }).join('');
    bindNavClicks();
  }

  function bindNavClicks() {
    var nav = document.getElementById('ahpBottomNav');
    if (!nav || navClickBound) return;
    navClickBound = true;
    nav.addEventListener('click', function (e) {
      var item = e.target.closest('[data-nav]');
      if (!item) return;
      e.preventDefault();
      switchPage(item.getAttribute('data-nav'));
    });
  }

  function currentAnimatedFaceIndex(slot) {
    var cube = slot && slot.querySelector('.ahp-cube');
    var cardId = slot ? slot.getAttribute('data-card-id') : '';
    var cardIndex = parseInt(slot ? slot.getAttribute('data-card-index') : '0', 10) || 0;
    if (cube && cube.classList.contains('ahp-show-face')) {
      return Math.max(0, Math.min(3, parseInt(cube.style.getPropertyValue('--ahp-face-index'), 10) || 0));
    }
    var elapsed = getCubeElapsedSeconds(cardId, cardIndex, false, null);
    return getCubeFaceIndexFromElapsed(elapsed, getCardRotationSpeed(cardId), getCardRotationMul(cardId));
  }

  function normalizePreviewRoute(route) {
    return String(route || '').trim();
  }

  function resolvePreviewPageId(route) {
    var raw = normalizePreviewRoute(route);
    if (!raw) return null;
    var normalized = raw.replace(/^\//, '').toLowerCase();
    var known = ['home', 'devices', 'profile', 'settings', 'alerts', 'reports', 'face-detail', 'add-device'];
    if (known.indexOf(normalized) >= 0) return normalized;
    var navItem = (layout.bottomNav || []).find(function (n) {
      return n && String(n.id || '').toLowerCase() === normalized;
    });
    return navItem ? navItem.id : null;
  }

  function closeInAppBrowser() {
    var browser = document.getElementById('ahpInAppBrowser');
    var frame = document.getElementById('ahpInAppBrowserFrame');
    if (browser) {
      browser.classList.remove('is-open');
      browser.setAttribute('aria-hidden', 'true');
    }
    if (frame) frame.src = 'about:blank';
  }

  function openInAppBrowser(url, title) {
    url = String(url || '').trim();
    if (!url) return false;
    if (!/^https?:\/\//i.test(url)) url = 'https://' + url;
    var browser = document.getElementById('ahpInAppBrowser');
    var frame = document.getElementById('ahpInAppBrowserFrame');
    var titleEl = document.getElementById('ahpInAppBrowserTitle');
    if (!browser || !frame) return false;
    if (titleEl) titleEl.textContent = title || url;
    browser.classList.add('is-open');
    browser.setAttribute('aria-hidden', 'false');
    frame.src = 'about:blank';
    setTimeout(function () { frame.src = url; }, 30);
    return true;
  }

  function bindInAppBrowser() {
    var back = document.getElementById('ahpInAppBrowserBackBtn');
    if (back && !back._ahpBound) {
      back._ahpBound = true;
      back.addEventListener('click', function (e) {
        e.preventDefault();
        closeInAppBrowser();
      });
    }
  }

  function navigateFaceRoute(route, meta) {
    meta = meta || {};
    var raw = normalizePreviewRoute(route);
    if (!raw) {
      if (meta.fallbackFacePage) {
        layout.activeFacePage = meta.fallbackFacePage;
        switchPage('face-detail');
      }
      return;
    }
    if (/^https?:\/\//i.test(raw)) {
      if (!openInAppBrowser(raw, meta.title || raw)) {
        showToast('In-app browser unavailable');
      }
      return;
    }
    var pageId = resolvePreviewPageId(raw);
    if (pageId) {
      switchPage(pageId);
      return;
    }
    layout.activeFacePage = {
      cardId: meta.cardId || '',
      faceIndex: meta.faceIndex != null ? meta.faceIndex : 0,
      title: meta.title || raw,
      subtitle: meta.subtitle || raw,
      icon: meta.icon || 'fa-link',
      color: meta.color || '#202940',
      side: meta.side || '',
      actionText: 'Open ' + raw,
      route: raw
    };
    switchPage('face-detail');
  }

  function openCardFacePage(cardId, faceIndex) {
    var card = (layout.cards || []).find(function (c) { return c.id === cardId; });
    if (!card) return;
    ensureFaces(card);
    faceIndex = Math.max(0, Math.min(3, parseInt(faceIndex, 10) || 0));
    var face = card.faces[faceIndex] || {};
    var side = ['Front', 'Right', 'Back', 'Left'][faceIndex] || ('Face ' + (faceIndex + 1));
    var fallbackFacePage = {
      cardId: card.id,
      faceIndex: faceIndex,
      title: face.title || face.label || card.title || card.label || 'Card face',
      subtitle: face.subtitle || face.value || card.subtitle || '',
      icon: face.icon || card.icon || 'fa-cube',
      color: face.color || card.color || '#202940',
      side: side,
      actionText: 'Open ' + (face.title || face.label || card.title || card.label || 'details')
    };
    navigateFaceRoute(face.route || card.pageRoute || '', {
      cardId: card.id,
      faceIndex: faceIndex,
      title: fallbackFacePage.title,
      subtitle: fallbackFacePage.subtitle,
      icon: fallbackFacePage.icon,
      color: fallbackFacePage.color,
      side: side,
      fallbackFacePage: fallbackFacePage
    });
  }

  function switchPage(pageId, fromMenu) {
    if (!pageId) pageId = 'home';
    layout.activePage = pageId;
    setNavActive(pageId, true);
    renderActivePage();
  }

  function renderActivePage() {
    var pageId = layout.activePage || 'home';
    var home = document.getElementById('ahpHomePage');
    var devices = document.getElementById('ahpDevicesPage');
    var gridPage = document.getElementById('ahpGridPage');
    var placeholder = document.getElementById('ahpPlaceholderPage');
    if (home) home.classList.toggle('ahp-is-hidden', pageId !== 'home');
    if (devices) devices.classList.toggle('ahp-is-hidden', pageId !== 'devices');
    if (gridPage) gridPage.classList.toggle('ahp-is-hidden', pageId !== 'grid');
    if (pageId !== 'grid') closeGridInAppBrowser();
    if (placeholder) {
      placeholder.classList.toggle(
        'ahp-is-hidden',
        pageId === 'home' || pageId === 'devices' || pageId === 'grid'
      );
    }

    if (pageId === 'home') {
      stopIotStatusPoll();
      renderHeader();
      renderGrid();
      syncStaticHomeView();
    } else if (pageId === 'devices') {
      loadDevicesFromIotApi(true);
      startIotStatusPoll();
    } else if (pageId === 'grid') {
      stopIotStatusPoll();
      renderGridNetworkPage();
    } else {
      stopIotStatusPoll();
      renderPlaceholderPage(pageId);
    }
    requestAnimationFrame(applyPreviewZoom);
  }

  function getActiveStaticUiId() {
    return layout.staticUiTemplate || null;
  }

  function syncStaticHomeView() {
    var home = document.getElementById('ahpHomePage');
    var root = document.getElementById('ahpStaticUiRoot');
    var id = getActiveStaticUiId();
    var api = window.AhpStaticUi;
    if (!home || !root) return;
    if (id && api && api.get(id)) {
      home.classList.add('ahp-home-static-active');
      api.renderTo(root, id);
      var tpl = api.get(id);
      if (tpl && tpl.phoneBg && el.phone) {
        var screen = el.phone.querySelector('.ahp-phone-screen');
        if (screen) screen.style.background = tpl.phoneBg;
        home.style.setProperty('--ahp-phone-bg', tpl.phoneBg);
      }
    } else {
      home.classList.remove('ahp-home-static-active');
      root.innerHTML = '';
      root.classList.add('ahp-is-hidden');
      root.removeAttribute('data-static-ui');
    }
  }

  function applyStaticUiTemplate(templateId, options) {
    options = options || {};
    var api = window.AhpStaticUi;
    if (!api || !api.get(templateId)) {
      showToast('UI template not found');
      return false;
    }
    var tpl = api.get(templateId);
    if (!options.skipConfirm && getActiveStaticUiId() && getActiveStaticUiId() !== templateId) {
      if (!confirm('Switch to "' + tpl.label + '" static UI? Your cube home stays where you left it.')) {
        return false;
      }
    }
    if (!getActiveStaticUiId()) captureHomeTemplateDraft(getActiveHomeTemplateId());
    layout.staticUiTemplate = tpl.id;
    layout.activePage = 'home';
    layout.bottomNavCategoryTarget = 'grid';
    if (Array.isArray(tpl.bottomNav) && tpl.bottomNav.length) {
      layout.bottomNav = JSON.parse(JSON.stringify(tpl.bottomNav));
    }
    normalizeBottomNav();
    if (!layout.theme) layout.theme = {};
    layout.theme.staticUiTemplate = tpl.id;
    layout.theme.showBottomNav = true;
    if (tpl.navBg) layout.theme.navBg = tpl.navBg;
    if (tpl.navActive) layout.theme.navActiveColor = tpl.navActive;
    if (tpl.phoneBg) layout.theme.phoneBg = tpl.phoneBg;
    renderBottomNav();
    applyTheme();
    syncStaticHomeView();
    renderStaticUiTemplates();
    markLayoutDirty({ historyNow: true });
    showToast(tpl.label + ' applied (static) — Save to keep selection');
    return true;
  }

  function clearStaticUiTemplate() {
    if (!layout.staticUiTemplate) {
      showToast('No static UI active');
      return;
    }
    layout.staticUiTemplate = null;
    if (layout.theme) layout.theme.staticUiTemplate = null;
    syncStaticHomeView();
    applyTheme();
    renderGrid();
    renderStaticUiTemplates();
    markLayoutDirty({ historyNow: true });
    showToast('Static UI cleared — default home restored');
  }

  function renderStaticUiTemplates() {
    var wrap = document.getElementById('ahpStaticUiTemplates');
    if (!wrap) return;
    var api = window.AhpStaticUi;
    if (!api) {
      wrap.innerHTML = '<p class="ahp-tool-hint">Static UI module failed to load.</p>';
      return;
    }
    var current = getActiveStaticUiId();
    var logoUrl = (api.logoUrl || cfg.logoUrl || 'https://efactor.tech/files/home/Logo_new.png');
    wrap.innerHTML = api.list().map(function (tpl) {
      var imgs = (tpl.previewImages || []).slice(0, 4);
      while (imgs.length < 4) imgs.push(imgs[imgs.length - 1] || logoUrl);
      var mosaic = imgs.map(function (src, i) {
        return '<span class="ahp-static-template-tile t' + i + '" style="background-image:url(\'' + esc(src) + '\')"></span>';
      }).join('');
      return '<button type="button" class="ahp-static-template' + (tpl.id === current ? ' active' : '') + '" data-static-ui="' + esc(tpl.id) + '">' +
        '<div class="ahp-static-template-preview" aria-hidden="true">' +
          '<div class="ahp-static-template-mosaic">' + mosaic + '</div>' +
          '<div class="ahp-static-template-brand"><img src="' + esc(logoUrl) + '" alt=""><span>' + esc(tpl.category || 'eFactor') + '</span></div>' +
        '</div>' +
        '<strong>' + esc(tpl.label) + '</strong>' +
        '<em class="ahp-static-template-cat">' + esc(tpl.category || 'Template') + '</em>' +
        '<small>' + esc(tpl.note) + '</small></button>';
    }).join('');
    wrap.querySelectorAll('[data-static-ui]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        applyStaticUiTemplate(btn.getAttribute('data-static-ui'));
      });
    });
  }

  function brandBarHtml(subtitle) {
    var logoUrl = (window.AhpStaticUi && window.AhpStaticUi.logoUrl) || cfg.logoUrl || 'https://efactor.tech/files/home/Logo_new.png';
    return '<div class="ahp-page-brandbar">' +
      '<img src="' + esc(logoUrl) + '" alt="eFactor">' +
      '<strong>eFactor</strong>' +
      (subtitle ? '<span>' + esc(subtitle) + '</span>' : '') +
    '</div>';
  }

  var _gridSchemaPayload = null;
  var _gridSchemaLoading = false;

  function getGridMount() {
    return document.getElementById('ahpGridRoot');
  }

  function showGridPageLoading() {
    var mount = getGridMount();
    if (!mount) return;
    mount.innerHTML = '<div class="ahp-dev-loading"><i class="fas fa-spinner fa-spin"></i><span>Loading Grid from API...</span></div>';
  }

  function showGridPageError(message) {
    var mount = getGridMount();
    if (!mount) return;
    mount.innerHTML = '<div class="ahp-dev-empty"><i class="fas fa-border-all"></i><strong>Grid unavailable</strong><span>' + esc(message || 'Could not load Grid schema.') + '</span>'
      + '<button type="button" class="ahp-tool-btn" id="ahpGridRetryBtn" style="margin-top:12px">Retry</button></div>';
    var retry = document.getElementById('ahpGridRetryBtn');
    if (retry) {
      retry.addEventListener('click', function () { loadGridFromApi(true); });
    }
  }

  function applyGridSchemaPayload(payload) {
    var mount = getGridMount();
    var api = window.AHP_GRID_SDUI;
    if (!mount) return;
    if (!api || typeof api.render !== 'function') {
      showGridPageError('Grid schema renderer failed to load. Upload assets/js/app_home_grid_sdui.js and hard-refresh.');
      return;
    }
    api.render(payload, mount, {
      gridApiUrl: cfg.gridApiUrl || '',
      userId: cfg.userId || 0,
      sessionApiBase: cfg.apiBase || '',
      onRefresh: function () { loadGridFromApi(true); },
      onAction: function (actionId, meta) {
        meta = meta || {};
        if (actionId === 'navigate-profile') {
          switchPage('profile');
          return;
        }
        if (actionId === 'refresh-grid') {
          loadGridFromApi(true);
          return;
        }
        var url = meta.url || '';
        if (url && (meta.openUrl || actionId === 'grid-open-professional-space' || actionId === 'grid-open-circle-connect')) {
          openGridInAppBrowser(url, actionId === 'grid-open-circle-connect' ? 'Circle Connect' : 'Professional Space');
          return;
        }
        if (actionId) showToast('Grid action: ' + String(actionId).replace(/^grid-/, '').replace(/-/g, ' '));
      }
    });
  }

  function closeGridInAppBrowser() {
    var browser = document.getElementById('ahpGridBrowser');
    var frame = document.getElementById('ahpGridBrowserFrame');
    if (browser) {
      browser.classList.remove('is-open');
      browser.setAttribute('aria-hidden', 'true');
    }
    if (frame) frame.src = 'about:blank';
  }

  function openGridInAppBrowser(url, title) {
    url = String(url || '').trim();
    if (!url) {
      showToast('No URL attached to this card');
      return;
    }
    if (!/^https?:\/\//i.test(url)) url = 'https://' + url;
    var browser = document.getElementById('ahpGridBrowser');
    var frame = document.getElementById('ahpGridBrowserFrame');
    var titleEl = document.getElementById('ahpGridBrowserTitle');
    if (!browser || !frame) {
      showToast('In-app browser unavailable');
      return;
    }
    if (titleEl) titleEl.textContent = title || url;
    browser.classList.add('is-open');
    browser.setAttribute('aria-hidden', 'false');
    frame.src = 'about:blank';
    setTimeout(function () { frame.src = url; }, 30);
  }

  function bindGridInAppBrowser() {
    var back = document.getElementById('ahpGridBrowserBackBtn');
    if (back && !back._ahpBound) {
      back._ahpBound = true;
      back.addEventListener('click', function (e) {
        e.preventDefault();
        closeGridInAppBrowser();
      });
    }
  }

  function fetchGridSchemaPayload() {
    var api = window.AHP_GRID_SDUI;
    if (api && typeof api.fetchSchema === 'function') {
      return api.fetchSchema({
        gridApiUrl: cfg.gridApiUrl || '',
        userId: cfg.userId || 0,
        sessionApiBase: cfg.apiBase || ''
      });
    }
    var url = String(cfg.gridApiUrl || '').trim();
    if (!url) url = (cfg.apiBase || '') + '/grid';
    return fetch(url, {
      method: 'GET',
      credentials: url.indexOf(cfg.apiBase || '___') === 0 ? 'same-origin' : 'omit',
      headers: { Accept: 'application/json' }
    }).then(function (res) {
      return res.json().then(function (data) {
        if (!res.ok || !data || data.success === false) {
          throw new Error((data && data.message) || ('Grid API HTTP ' + res.status));
        }
        return data;
      });
    });
  }

  function loadGridFromApi(force) {
    if (_gridSchemaLoading) {
      return Promise.resolve(_gridSchemaPayload);
    }
    if (!force && _gridSchemaPayload) {
      applyGridSchemaPayload(_gridSchemaPayload);
      return Promise.resolve(_gridSchemaPayload);
    }
    _gridSchemaLoading = true;
    if (layout.activePage === 'grid') {
      showGridPageLoading();
    }
    return fetchGridSchemaPayload()
      .then(function (payload) {
        if (payload && payload.gridApiUrl) cfg.gridApiUrl = payload.gridApiUrl;
        _gridSchemaPayload = payload;
        if (payload && payload.content && typeof payload.content === 'object') {
          layout.gridPage = payload.content;
        }
        if (layout.activePage === 'grid') {
          applyGridSchemaPayload(payload);
        }
        return payload;
      })
      .catch(function (err) {
        if (layout.activePage === 'grid') {
          showGridPageError(err && err.message ? err.message : 'Could not load Grid schema.');
        }
        showToast(err && err.message ? err.message : 'Could not load Grid schema.');
        throw err;
      })
      .finally(function () {
        _gridSchemaLoading = false;
      });
  }

  function renderGridNetworkPage() {
    loadGridFromApi(false);
  }

  function renderPlaceholderPage(pageId) {
    var navItem = (layout.bottomNav || []).find(function (n) { return n.id === pageId; });
    var title = navItem ? navItem.label : 'Page';
    var icon = navItem ? normalizeFaIcon(navItem.icon) : 'fa-layer-group';
    var hint = 'This page is ready for app content.';
    var color = getTheme().accentColor || '#202940';

    if (pageId === 'profile' || pageId === 'settings') {
      renderProfilePage(color);
      return;
    }

    if (pageId === 'face-detail' && layout.activeFacePage) {
      title = layout.activeFacePage.title;
      icon = normalizeFaIcon(layout.activeFacePage.icon);
      color = layout.activeFacePage.color || color;
      hint = layout.activeFacePage.route
        ? ('Preview for route ' + layout.activeFacePage.route + '. This URL/page will open in the mobile app.')
        : ('Dummy page for ' + title + '. This is where the ' + title + ' related content will open in the app.');
    } else if (pageId === 'reports') {
      hint = 'Dummy reports page with usage, activity, and performance summary.';
    } else if (pageId === 'alerts') {
      hint = 'Dummy alerts page with notifications and service updates.';
    }

    var titleEl = document.getElementById('ahpPlaceholderTitle');
    var iconEl = document.getElementById('ahpPlaceholderIcon');
    var hintEl = document.getElementById('ahpPlaceholderHint');
    var box = document.querySelector('#ahpPlaceholderPage .ahp-page-placeholder');
    if (box) {
      box.classList.remove('ahp-profile-page');
      box.classList.remove('ahp-face-detail-page');
    }
    if (titleEl) titleEl.textContent = title;
    if (iconEl) iconEl.className = 'fas ' + icon;
    if (iconEl) iconEl.style.color = color;
    if (hintEl) {
      hintEl.innerHTML = brandBarHtml(title) + '<p class="ahp-placeholder-copy">' + esc(hint) + '</p>';
    }
    if (box) box.style.setProperty('--ahp-detail-color', color);
  }


  function renderFaceDetailPage(fallbackColor) {
    var data = layout.activeFacePage || {};
    var titleEl = document.getElementById('ahpPlaceholderTitle');
    var iconEl = document.getElementById('ahpPlaceholderIcon');
    var hintEl = document.getElementById('ahpPlaceholderHint');
    var box = document.querySelector('#ahpPlaceholderPage .ahp-page-placeholder');
    var color = data.color || fallbackColor || '#202940';
    if (titleEl) titleEl.textContent = '';
    if (iconEl) iconEl.className = 'fas fa-cube ahp-is-hidden';
    if (!hintEl || !box) return;
    box.classList.remove('ahp-profile-page');
    box.classList.add('ahp-face-detail-page');
    box.style.setProperty('--ahp-detail-color', color);
    hintEl.innerHTML =
      '<div class="ahp-face-detail-hero" style="--ahp-detail-color:' + esc(color) + '">' +
        '<button type="button" class="ahp-face-detail-back" data-face-back><i class="fas fa-arrow-left"></i></button>' +
        '<div class="ahp-face-detail-orb"><i class="fas ' + esc(normalizeFaIcon(data.icon || 'fa-cube')) + '"></i></div>' +
        '<span>' + esc(data.side || 'Face') + '</span>' +
        '<h2>' + esc(data.title || 'Face detail') + '</h2>' +
        '<p>' + esc(data.subtitle || 'Related app content opens here.') + '</p>' +
      '</div>' +
      '<div class="ahp-face-detail-grid">' +
        '<button type="button"><i class="fas fa-layer-group"></i><span>Overview</span></button>' +
        '<button type="button"><i class="fas fa-chart-line"></i><span>Insights</span></button>' +
        '<button type="button"><i class="fas fa-bolt"></i><span>Actions</span></button>' +
      '</div>' +
      '<button type="button" class="ahp-face-detail-primary">' + esc(data.actionText || 'Open details') + '</button>';
    var back = hintEl.querySelector('[data-face-back]');
    if (back) back.addEventListener('click', function () { switchPage('home'); });
    var primaryBtn = hintEl.querySelector('.ahp-face-detail-primary');
    if (primaryBtn && data.route) {
      primaryBtn.addEventListener('click', function () {
        navigateFaceRoute(data.route, { fallbackFacePage: data });
      });
    }
  }
  function getProfileInitials(profile) {
    var source = (profile.name || profile.email || cfg.userEmail || 'User').trim();
    var parts = source.split(/\s+/).filter(Boolean);
    if (!parts.length) return 'U';
    if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
    return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
  }

  function resolveStudioAvatarUrl(url) {
    var src = String(url || '').trim();
    if (!src || /^(data:|blob:)/i.test(src)) return '';
    if (/\/assets\/images\/avatar\.jpg(\?|$)/i.test(src) || /(^|\/)avatar\.jpg(\?|$)/i.test(src)) return '';
    var localStudio = window.location && /^(localhost|127\.0\.0\.1)$/i.test(window.location.hostname || '');
    if (/^https?:\/\//i.test(src)) {
      // The file is stored on this machine. efactor.tech does not have it, so the preview 404s.
      if (localStudio && /\/files\/profile_images\//i.test(src)) {
        return window.location.origin.replace(/\/+$/, '') + src.replace(/^https?:\/\/[^/]+/i, '');
      }
      return src;
    }
    if (src.charAt(0) !== '/') {
      src = src.indexOf('/') === -1 ? ('/files/profile_images/' + src) : ('/' + src);
    }
    var origin = localStudio
      ? window.location.origin
      : ((window.location && window.location.origin) || 'https://efactor.tech');
    return origin.replace(/\/+$/, '') + src;
  }

  function getProfileAvatarUrl(profile) {
    var page = profile || getProfilePage();
    var live = getLoginUserProfile();
    return resolveStudioAvatarUrl(live.avatarUrl) || resolveStudioAvatarUrl(page.avatarUrl);
  }

  function renderHomeAvatar() {
    var btn = document.getElementById('ahpHomeAvatar');
    if (!btn) return;
    var profile = getProfilePage();
    var url = getProfileAvatarUrl(profile);
    var initials = getProfileInitials(profile);
    var img = btn.querySelector('img.ahp-avatar-photo');
    var initialsEl = document.getElementById('ahpHomeAvatarInitials');
    if (url) {
      if (!img) {
        img = document.createElement('img');
        img.className = 'ahp-avatar-photo';
        img.alt = '';
        btn.insertBefore(img, btn.firstChild);
      }
      img.onerror = function () { showAvatarInitials(btn, img, initialsEl, initials); };
      img.src = url;
      if (initialsEl) {
        initialsEl.textContent = initials;
        initialsEl.classList.add('ahp-is-hidden');
      }
      btn.classList.add('ahp-has-photo');
      return;
    }
    if (img) img.remove();
    btn.classList.remove('ahp-has-photo');
    if (initialsEl) {
      initialsEl.textContent = initials;
      initialsEl.classList.remove('ahp-is-hidden');
    }
  }

  function showAvatarInitials(parent, img, initialsEl, initials) {
    if (img) img.remove();
    if (parent) parent.classList.remove('ahp-has-photo');
    if (initialsEl) {
      initialsEl.textContent = initials || initialsEl.textContent;
      initialsEl.classList.remove('ahp-is-hidden');
    }
  }

  function buildProfileAvatarHtml(profile) {
    var url = getProfileAvatarUrl(profile);
    var initials = esc(getProfileInitials(profile));
    if (url) {
      return '<img class="ahp-avatar-photo" src="' + esc(url) + '" alt="" onerror="var box=this.parentNode;this.remove();if(box){box.classList.remove(\'ahp-has-photo\');var n=box.querySelector(\'.ahp-avatar-initials\');if(n)n.classList.remove(\'ahp-is-hidden\');}">' +
        '<span class="ahp-avatar-initials ahp-is-hidden">' + initials + '</span>';
    }
    return initials;
  }

  function getProfilePage() {
    if (!layout.profilePage) layout.profilePage = {};
    return layout.profilePage;
  }

  function getLoginUserProfile() {
    return cfg.loginUserProfile || {};
  }

  function applyLoginUserProfileToLayout(profile) {
    var live = profile || getLoginUserProfile();
    if (!live || typeof live !== 'object') return;
    var page = getProfilePage();
    if (live.name) page.name = live.name;
    if (live.email) {
      page.email = live.email;
      if (!layout.welcome) layout.welcome = {};
      layout.welcome.email = live.email;
      renderWelcomeBanner();
    }
    if (live.phone != null) page.phone = live.phone;
    if (live.memberId) page.memberId = live.memberId;
    if (live.avatarUrl != null) {
      page.avatarUrl = live.avatarUrl;
      renderWelcomeAvatar();
    }
  }

  function refreshLoginUserProfileFromApi(callback) {
    fetch(cfg.apiBase + '/profile', {
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(function (res) {
        if (res && res.success && res.profile) {
          cfg.loginUserProfile = res.profile;
          applyLoginUserProfileToLayout(res.profile);
          syncProfileTools();
          renderHeader();
          if (layout.activePage === 'profile' || layout.activePage === 'settings') {
            renderProfilePage(getTheme().accentColor || '#202940');
          }
        }
        if (callback) callback();
      })
      .catch(function () {
        if (callback) callback();
      });
  }

  function syncProfileTools() {
    applyLoginUserProfileToLayout();
    var live = getLoginUserProfile();
    var p = getProfilePage();
    setVal('toolProfileName', live.name || p.name || 'Profile');
    setVal('toolProfileEmail', live.email || p.email || cfg.userEmail || '');
    setVal('toolProfilePhone', live.phone != null ? live.phone : (p.phone || ''));
    setVal('toolProfileMemberId', live.memberId || p.memberId || ('EF-' + (cfg.userId || '0')));
    setCheck('toolProfileNotifications', p.notificationsEnabled !== false);
  }

  function getThemeModeLabel() {
    return getTheme().bgStyle === 'light' ? 'Light' : 'Dark';
  }

  var ratingDraft = { stars: 0, comment: '' };

  function profileRowHtml(icon, title, subtitle, action, selected) {
    return '<button type="button" class="ahp-profile-row' + (selected ? ' is-selected' : '') + '" data-profile-action="' + esc(action) + '">' +
      '<i class="fas ' + icon + '"></i>' +
      '<span><strong>' + esc(title) + '</strong><small>' + esc(subtitle) + '</small></span>' +
      '<i class="fas fa-chevron-right"></i>' +
    '</button>';
  }

  function profileSectionHtml(title, subtitle) {
    return '<div class="ahp-profile-section"><strong>' + esc(title) + '</strong><small>' + esc(subtitle) + '</small></div>';
  }

  function mountProfileHtml(color, innerHtml) {
    var titleEl = document.getElementById('ahpPlaceholderTitle');
    var iconEl = document.getElementById('ahpPlaceholderIcon');
    var hintEl = document.getElementById('ahpPlaceholderHint');
    var box = document.querySelector('#ahpPlaceholderPage .ahp-page-placeholder');
    if (titleEl) titleEl.textContent = '';
    if (iconEl) iconEl.className = 'fas fa-user ahp-is-hidden';
    if (!hintEl || !box) return;
    box.classList.add('ahp-profile-page');
    box.style.setProperty('--ahp-detail-color', color || '#202940');
    hintEl.innerHTML = innerHtml;
    bindProfileInteractions();
  }

  function bindProfileInteractions() {
    var root = document.querySelector('#ahpPlaceholderPage .ahp-page-placeholder');
    if (!root) return;
    root.querySelectorAll('[data-profile-action]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var action = btn.getAttribute('data-profile-action') || '';
        var color = getTheme().accentColor || '#202940';
        if (action === 'edit') {
          setEditMode(true);
          setActiveMenu('profile');
          showToast('Edit profile details in the Profile panel');
          return;
        }
        if (action === 'notifications') {
          var p = getProfilePage();
          p.notificationsEnabled = !p.notificationsEnabled;
          setCheck('toolProfileNotifications', p.notificationsEnabled);
          showToast(p.notificationsEnabled ? 'Notifications enabled' : 'Notifications disabled');
          renderProfilePage(color);
          return;
        }
        if (action === 'appearance') {
          setEditMode(true);
          setActiveMenu('theme');
          showToast('Theme is set in the studio Theme panel. Save to update the app.');
          return;
        }
        if (action === 'privacy') {
          showToast('Privacy policy opens in the mobile app');
          return;
        }
        if (action === 'rating') {
          ratingDraft = { stars: 0, comment: '' };
          renderRatingPage(color);
          return;
        }
        if (action.indexOf('rate-star:') === 0) {
          readRatingComment();
          ratingDraft.stars = parseInt(action.slice('rate-star:'.length), 10) || 0;
          renderRatingPage(color);
          return;
        }
        if (action === 'rating-send') {
          submitRating(color);
        }
      });
    });
  }

  function readRatingComment() {
    var box = document.getElementById('ahpRatingComment');
    if (box) ratingDraft.comment = box.value || '';
  }

  function starRowHtml() {
    var html = '<div class="ahp-star-row" role="group" aria-label="Star rating">';
    var i;
    for (i = 1; i <= 5; i++) {
      html += '<button type="button" class="ahp-star' + (i <= ratingDraft.stars ? ' is-on' : '') + '" data-profile-action="rate-star:' + i + '" aria-label="' + i + (i === 1 ? ' star' : ' stars') + '"><i class="fas fa-star"></i></button>';
    }
    html += '</div>';
    return html;
  }

  function submitRating(color) {
    readRatingComment();
    var comment = (ratingDraft.comment || '').trim();
    if (!ratingDraft.stars) {
      showToast('Select a star rating.');
      return;
    }
    if (!comment) {
      showToast('Write a comment about the issue.');
      return;
    }
    var sendBtn = document.querySelector('[data-profile-action="rating-send"]');
    if (sendBtn) sendBtn.disabled = true;
    fetch((cfg.apiBase || '/index.php/app-home-page/api') + '/rating-feedback', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        rating: ratingDraft.stars,
        comment: comment
      })
    })
      .then(function (r) { return r.json().catch(function () { return { success: false, message: 'Could not send rating.' }; }); })
      .then(function (res) {
        showToast((res && res.message) || (res && res.success ? 'Thanks. Your rating was sent.' : 'Could not send rating.'));
        if (res && res.success) {
          ratingDraft = { stars: 0, comment: '' };
          renderProfilePage(color);
          return;
        }
        if (sendBtn) sendBtn.disabled = false;
      })
      .catch(function () {
        showToast('Could not send rating. Please try again.');
        if (sendBtn) sendBtn.disabled = false;
      });
  }

  function renderRatingPage(color) {
    mountProfileHtml(color,
      brandBarHtml('Account') +
      profileSectionHtml('Rating', 'Select a star, then write what went wrong') +
      starRowHtml() +
      '<label class="ahp-rating-comment"><span>Comment</span>' +
        '<textarea id="ahpRatingComment" maxlength="2000" placeholder="Describe the issue so it can be fixed">' + esc(ratingDraft.comment) + '</textarea>' +
      '</label>' +
      '<button type="button" class="ahp-profile-logout" data-profile-action="rating-send"><i class="fas fa-paper-plane"></i> Send rating</button>'
    );
  }

  function renderProfilePage(color) {
    var p = getProfilePage();
    var name = p.name || 'Profile';
    var email = p.email || cfg.userEmail || 'person@efactor.app';
    var phone = p.phone || '+91 123 456 7890';
    var memberId = p.memberId || ('EF-' + (cfg.userId || '177'));
    var notifOn = p.notificationsEnabled !== false;
    var csrfInput = cfg.csrfName && cfg.csrfHash
      ? '<input type="hidden" name="' + esc(cfg.csrfName) + '" value="' + esc(cfg.csrfHash) + '">'
      : '';
    var rows = [
      ['fa-user-pen', 'Edit profile', 'Manage account details', 'edit'],
      ['fa-bell', 'Notifications', notifOn ? 'Alerts and reminders enabled' : 'Alerts and reminders disabled', 'notifications'],
      ['fa-moon', 'Appearance', getThemeModeLabel() + ' mode', 'appearance'],
      ['fa-shield-halved', 'Privacy Policy', 'Security and data controls', 'privacy'],
      ['fa-star', 'Rating', 'Rate with stars and describe the issue', 'rating']
    ];
    var titleEl = document.getElementById('ahpPlaceholderTitle');
    var iconEl = document.getElementById('ahpPlaceholderIcon');
    var hintEl = document.getElementById('ahpPlaceholderHint');
    var box = document.querySelector('#ahpPlaceholderPage .ahp-page-placeholder');
    if (titleEl) titleEl.textContent = '';
    if (iconEl) iconEl.className = 'fas fa-user ahp-is-hidden';
    if (!hintEl || !box) return;
    box.classList.add('ahp-profile-page');
    box.style.setProperty('--ahp-detail-color', color || '#202940');
    hintEl.innerHTML =
      brandBarHtml('Account') +
      '<div class="ahp-profile-card">' +
        '<div class="ahp-profile-avatar' + (getProfileAvatarUrl(p) ? ' ahp-has-photo' : '') + '">' + buildProfileAvatarHtml(p) + '</div>' +
        (memberId ? '<p class="ahp-profile-gridid">' + esc(memberId) + '</p>' : '') +
        '<h2>' + esc(name) + '</h2>' +
        '<p>' + esc(email) + '</p>' +
        '<div class="ahp-profile-meta"><span>' + esc(phone) + '</span></div>' +
      '</div>' +
      '<div class="ahp-profile-list">' + rows.map(function (row) {
        return profileRowHtml(row[0], row[1], row[2], row[3], false);
      }).join('') + '</div>' +
      '<form class="ahp-profile-logout-form" method="post" action="' + esc(cfg.logoutUrl || '/index.php/sd-logout') + '">' +
        csrfInput +
        '<button type="submit" class="ahp-profile-logout"><i class="fas fa-sign-out-alt"></i> Logout</button>' +
      '</form>';
    bindProfileInteractions();
  }
  /* ── FOUR-FACE BOX ENGINE ──────────────────────────────────────────────
     Each tile is a real 3D box: front / right / back / left.
     Parent: translateZ(-half) rotateY(angle). Faces: rotateY(n*90) translateZ(half).
     half = slotWidth/2 (closed solid cube — demo.mp4 clone). No flat flip.
     ──────────────────────────────────────────────────────────────────── */
  var _faceSwapRaf = null;
  var _faceSwapActive = false;

  function getFaceIndexAtTime(cardId, cardIndex, isDevice) {
    var speed = isDevice
      ? Math.max(8, Number(getTheme().cardSpeed || 16))
      : getCardRotationSpeed(cardId);
    var deviceId = isDevice ? cardId : null;
    var elapsed = getCubeElapsedSeconds(isDevice ? null : cardId, cardIndex, isDevice, isDevice ? cardId : null);
    var rotationMul = isDevice ? -1 : getCardRotationMul(cardId);
    return getCubeFaceIndexFromElapsed(elapsed, speed, rotationMul);
  }

  function applyActiveFaceInEditMode(cube) {
    var faceIndex = parseInt(cube.style.getPropertyValue('--ahp-face-index') || '0', 10) || 0;
    cube.style.setProperty('--ahp-face-index', faceIndex);
  }

  function runFaceSwapLoop() {
    if (el.phone) syncCubeRotation(el.phone);
    if (el.grid) syncCubeRotation(el.grid);
    var devicesGrid = document.getElementById('ahpDevicesGrid');
    if (devicesGrid) syncCubeRotation(devicesGrid);
    _faceSwapRaf = requestAnimationFrame(runFaceSwapLoop);
  }

  function startFaceSwapLoop() {
    if (_faceSwapActive) return;
    _faceSwapActive = true;
    _faceSwapRaf = requestAnimationFrame(runFaceSwapLoop);
  }

  function stopFaceSwapLoop() {
    _faceSwapActive = false;
    if (_faceSwapRaf) { cancelAnimationFrame(_faceSwapRaf); _faceSwapRaf = null; }
  }

  function resolveCubeFaceGapPx() {
    var t = getTheme() || {};
    if (window.AhpSchemaV2 && typeof window.AhpSchemaV2.resolveCubeFaceGap === 'function') {
      return window.AhpSchemaV2.resolveCubeFaceGap(t);
    }
    if (t.cubeFaceGap != null && t.cubeFaceGap !== '') {
      var custom = parseInt(t.cubeFaceGap, 10);
      if (!isNaN(custom)) return Math.max(0, Math.min(24, custom));
    }
    return 0;
  }

  function syncCubeGeometry(scope) {
    var root = scope || el.phone || document;
    var faceGap = resolveCubeFaceGapPx();
    root.querySelectorAll('.ahp-card[data-card-id], .ahp-cube-wrap, .ahp-dev-cube-wrap').forEach(function (host) {
      if (host.querySelector('.ahp-application-cube')) return;
      var cube = host.querySelector('.ahp-cube:not(.ahp-application-cube)');
      if (!cube) return;
      // Use layout size (offsetWidth), NOT getBoundingClientRect().
      // Preview zoom scales the phone with transform; rect width is wrong and
      // makes translateZ too small → mid-turn looks like a crushed flat strip.
      var width = host.offsetWidth || host.clientWidth || 0;
      var height = host.offsetHeight || host.clientHeight || 0;
      if (!width || !height) return;
      // Closed solid cube (demo.mp4): halfDepth = slotWidth/2 (+ optional faceGap override).
      var half = Math.max(4, width / 2) + faceGap;
      var depth = half * 2;
      var vars = {
        '--ahp-cube-width': width + 'px',
        '--ahp-cube-height': height + 'px',
        '--ahp-cube-half': half + 'px',
        '--ahp-cube-face-z': half + 'px',
        '--ahp-cube-face-gap': faceGap + 'px',
        '--ahp-cube-size': depth + 'px'
      };
      Object.keys(vars).forEach(function (key) {
        host.style.setProperty(key, vars[key]);
        cube.style.setProperty(key, vars[key]);
      });
    });
  }

  function bindCubeGeometryObserver() {
    if (cubeGeometryObserver || typeof ResizeObserver === 'undefined') return;
    var phone = el.phone || document.getElementById('ahpPhone');
    if (!phone) return;
    cubeGeometryObserver = new ResizeObserver(function () {
      scheduleCubeGeometry(phone);
    });
    cubeGeometryObserver.observe(phone);
    if (el.grid) cubeGeometryObserver.observe(el.grid);
  }

  function cubeParentTransform(angleDeg, cube) {
    var half = 0;
    if (cube) {
      var raw = cube.style.getPropertyValue('--ahp-cube-half') || '';
      half = parseFloat(raw);
      if (isNaN(half) || half <= 0) {
        var slot = cube.closest('[data-card-id],[data-device-id],.ahp-cube-wrap,.ahp-dev-cube-wrap');
        if (slot) {
          // offsetWidth = layout px (ignores preview scale transform)
          var w = slot.offsetWidth || slot.clientWidth || 0;
          half = w > 0 ? (w / 2) + resolveCubeFaceGapPx() : 0; // faceGap defaults 0 (closed cube)
        }
      }
    }
    if (!(half > 0)) half = 0;
    return 'translateZ(' + (-half) + 'px) rotateY(' + angleDeg + 'deg)';
  }

  function syncCubeRotation(scope) {
    var root = scope || el.phone || document;
    root.querySelectorAll('.ahp-cube:not(.ahp-application-cube)').forEach(function (cube) {
      var slot = cube.closest('[data-card-id],[data-device-id]');
      var cardId = slot ? slot.getAttribute('data-card-id') : null;
      if (cube.classList.contains('ahp-show-face')) {
        var faceIndex = parseInt(cube.style.getPropertyValue('--ahp-face-index') || '0', 10) || 0;
        var angle = cubeAngleForFace(faceIndex, cardId ? getCardRotationMul(cardId) : -1);
        if (!cube.classList.contains('ahp-face-animating')) {
          cube.style.transform = cubeParentTransform(angle, cube);
          cube.dataset.ahpCubeAngle = String(angle);
        }
        syncCubeFacePointerState(cube, faceIndex, !editMode);
        return;
      }
      cube.classList.remove('ahp-face-animating');
      if (cube.classList.contains('paused')) {
        cube.style.removeProperty('transform');
        delete cube.dataset.ahpCubeAngle;
        syncCubeFacePointerState(cube, 0, false);
        return;
      }
      if (!slot) return;
      cardId = slot.getAttribute('data-card-id');
      var deviceId = slot.getAttribute('data-device-id');
      var index = parseInt(slot.getAttribute('data-card-index'), 10);
      if (isNaN(index)) index = 0;
      if (deviceId) {
        var page = getDevicesPage();
        index = (page.devices || []).findIndex(function (d) { return d.id === deviceId; });
        if (index < 0) index = 0;
      }
      var elapsed = getCubeElapsedSeconds(cardId || deviceId, index, !!deviceId, deviceId);
      var speed = deviceId
        ? Math.max(8, Number(getTheme().cardSpeed || 16))
        : getCardRotationSpeed(cardId);
      var rotationMul = cardId ? getCardRotationMul(cardId) : -1;
      var angle = getCubeRotationFromElapsed(elapsed, speed, rotationMul);
      var interaction = getCubeInteractionFace(elapsed, speed, rotationMul);
      cube.style.transition = 'none';
      cube.style.transform = cubeParentTransform(angle, cube);
      cube.dataset.ahpCubeAngle = String(angle);
      syncCubeFacePointerState(cube, interaction.face, interaction.interactive && !editMode);
    });
  }

  function scheduleCubeGeometry(scope) {
    requestAnimationFrame(function () {
      syncCubeGeometry(scope);
      syncCubeRotation(scope);
      requestAnimationFrame(function () {
        syncCubeGeometry(scope);
        syncCubeRotation(scope);
      });
    });
  }

  function renderDeviceCube(device, index) {
    device = ensureDeviceFaces(device);
    ensureDeviceSize(device);
    var offset = getDeviceAnimOffset(device.id, index || 0);
    var freeze = editMode && activeMenuKey === 'devices' && selectedDeviceId === device.id;
    var defFace = getDeviceDefaultFace(device);
    var faceIndex = freeze ? activeDeviceFaceIndex : defFace;
    var pause = freeze ? ' paused' : '';
    var faceClass = freeze ? ' ahp-show-face' : '';
    var color = device.color || '#334155';
    var radius = parseInt(device.borderRadius, 10);
    if (isNaN(radius)) radius = 16;
    var facesHtml = device.faces.map(function (f, i) { return renderDeviceFace(device, f, i); }).join('');
    var powerHtml = device.power !== false
      ? '<button type="button" class="ahp-dev-power" data-dev-power="' + esc(device.id) + '" title="Power"><i class="fas fa-power-off"></i></button>'
      : '';
    var selected = selectedDeviceId === device.id ? ' selected' : '';
    var stacked = device.stacked ? ' stacked' : '';
    var stackAttr = device.stacked ? ' data-stack-label="' + esc(device.stackLabel || 'More') + '"' : '';
    var cubeHtml = '<div class="ahp-dev-cube-inner"><div class="ahp-cube-wrap ahp-dev-cube-wrap" style="border-radius:' + radius + 'px">' +
      '<div class="ahp-cube' + pause + faceClass + '" style="--ahp-cube-delay:' + offset + 's;--ahp-face-index:' + faceIndex + ';--ahp-card-radius:' + radius + 'px">' +
      facesHtml + '</div></div></div>';
    var actionsLeft = '<div class="ahp-slot-actions ahp-slot-actions-left">' +
      '<button type="button" class="ahp-slot-btn ahp-drag-handle" title="Drag to reorder" aria-label="Drag to reorder"><i class="fas fa-grip-vertical"></i></button>' +
      '<button type="button" class="ahp-slot-btn" data-action="edit" title="Edit"><i class="fas fa-pen"></i></button>' +
      '</div>';
    var actionsRight = '<div class="ahp-slot-actions ahp-slot-actions-right">' +
      '<button type="button" class="ahp-slot-btn ahp-slot-btn-default" data-action="default-face" title="Default face: Face ' + (getDeviceDefaultFace(device) + 1) + '">' +
      '<i class="fas fa-star"></i><span class="ahp-slot-face-num">' + (getDeviceDefaultFace(device) + 1) + '</span></button>' +
      '</div>';
    var slotStyle = styleAttr({
      '--ahp-dev-color': color,
      '--ahp-card-radius': radius + 'px',
      'border-radius': radius + 'px',
      'grid-column': 'span ' + device.colSpan,
      'grid-row': 'span ' + device.rowSpan
    });
    return '<div class="ahp-dev-cube' + selected + stacked + '" data-device-id="' + esc(device.id) + '" style="' + slotStyle + '"' + stackAttr + '>' +
      actionsLeft + actionsRight +
      '<span class="ahp-dev-status-dot' + (device.online === false ? ' offline' : '') + '"></span>' +
      powerHtml + cubeHtml + '</div>';
  }

  function updateSelectedDeviceHighlight() {
    var grid = document.getElementById('ahpDevicesGrid');
    if (!grid) return;
    grid.querySelectorAll('.ahp-dev-cube').forEach(function (slot) {
      var id = slot.getAttribute('data-device-id');
      var isSelected = editMode && activeMenuKey === 'devices' && !!selectedDeviceId && id === selectedDeviceId;
      slot.classList.toggle('selected', isSelected || (!editMode && selectedDeviceId === id));
    });
  }

  function updateDeviceSlot(deviceId) {
    var grid = document.getElementById('ahpDevicesGrid');
    if (!grid) return;
    var page = getDevicesPage();
    var idx = (page.devices || []).findIndex(function (d) { return d.id === deviceId; });
    if (idx < 0) return;
    var device = page.devices[idx];
    var slot = grid.querySelector('[data-device-id="' + deviceId + '"]');
    if (!slot || !device) return;
    var wasSortable = !!deviceSortable;
    if (wasSortable) destroyDeviceSortable();
    slot.outerHTML = renderDeviceCube(device, idx);
    updateSelectedDeviceHighlight();
    scheduleCubeGeometry(grid);
    if (editMode && wasSortable) initDeviceSortable();
    refreshCubePlayback(grid);
  }

  function cycleDeviceDefaultFace(deviceId) {
    var page = getDevicesPage();
    var device = (page.devices || []).find(function (d) { return d.id === deviceId; });
    if (!device) return;
    ensureDeviceFaces(device);
    device.defaultFace = (getDeviceDefaultFace(device) + 1) % 4;
    if (selectedDeviceId === deviceId) {
      activeDeviceFaceIndex = device.defaultFace;
      syncDeviceEditor(true);
    }
    updateDeviceSlot(deviceId);
    showToast('Default face set to Face ' + (device.defaultFace + 1));
  }

  function renderDevicesPage(opts) {
    opts = opts || {};
    if (_iotDevicesPayload) {
      applyIotDevicesPayload(_iotDevicesPayload);
    } else {
      loadDevicesFromIotApi(true);
    }
    if (!opts.skipEditor) {
      syncDevicePicker();
      syncDeviceEditor();
    } else {
      syncDevicePicker();
    }
  }

  function selectDevice(deviceId, opts) {
    opts = opts || {};
    var same = selectedDeviceId === deviceId;
    selectedDeviceId = deviceId;
    if (!same || opts.resetFace) {
      activeDeviceFaceIndex = opts.faceIndex != null ? opts.faceIndex : 0;
      deviceEditorDeviceId = null;
    }
    if (layout.activePage !== 'devices') {
      switchPage('devices');
    } else {
      renderDevicesPage({ skipEditor: true });
      if (!same || opts.forceEditor) syncDeviceEditor(true);
      else updateSelectedDeviceHighlight();
    }
    refreshCubePlayback(document.getElementById('ahpDevicesGrid'));
  }

  function syncDevicePicker() {
    var picker = document.getElementById('ahpDevicePicker');
    var count = document.getElementById('ahpDeviceCount');
    var list = [];
    if (_iotDevicesPayload && window.AHP_IOT_SDUI && typeof window.AHP_IOT_SDUI.extractDevices === 'function') {
      list = window.AHP_IOT_SDUI.extractDevices(_iotDevicesPayload);
    } else {
      list = (getDevicesPage().devices || []);
    }
    if (count) {
      var n = list.length;
      count.textContent = n + (n === 1 ? ' device' : ' devices') + ' (from IoT API)';
    }
    if (!picker) return;
    if (_iotDevicesPayload) {
      picker.innerHTML = '<p class="ahp-tool-hint">Devices are loaded dynamically from the IoT schema API. Generate or refresh schema in IoT Dashboard.</p>';
      return;
    }
    picker.innerHTML = list.map(function (d, i) {
      var sel = d.id === selectedDeviceId ? ' selected' : '';
      return '<button type="button" class="ahp-card-pick' + sel + '" data-pick-device="' + esc(d.id) + '">' +
        '<strong>' + esc(d.name || ('Device ' + (i + 1))) + '</strong>' +
        '<span class="ahp-card-pick-meta">' + esc(d.location || d.value || d.id) + (d.online === false ? ' - offline' : ' - online') + '</span></button>';
    }).join('') || '<p class="ahp-tool-hint">No devices yet. Add one below.</p>';
    picker.querySelectorAll('[data-pick-device]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        selectDevice(btn.getAttribute('data-pick-device'));
      });
    });
  }

  function syncDeviceEditor(forceRebuild) {
    var panel = document.getElementById('ahpDeviceEditor');
    if (!panel) return;
    var page = getDevicesPage();
    var device = (page.devices || []).find(function (d) { return d.id === selectedDeviceId; });
    if (!device) {
      panel.innerHTML = '<p class="ahp-tool-hint">Select a device from the list or tap one in the preview.</p>';
      deviceEditorDeviceId = null;
      return;
    }
    ensureDeviceFaces(device);
    ensureDeviceSize(device);
    if (activeDeviceFaceIndex > 3) activeDeviceFaceIndex = 0;
    if (!forceRebuild && deviceEditorDeviceId === device.id && panel.querySelector('#deFaceName')) return;
    deviceEditorDeviceId = device.id;

    var face = device.faces[activeDeviceFaceIndex] || {};
    var orientation = getCardOrientation(device);
    var cardRadiusVal = device.borderRadius != null ? device.borderRadius : 16;
    var faceRadiusVal = face.borderRadius != null && face.borderRadius !== '' ? face.borderRadius : '';
    var faceTabs = [0, 1, 2, 3].map(function (i) {
      return '<button type="button" class="ahp-face-tab' + (i === activeDeviceFaceIndex ? ' active' : '') + '" data-dev-face="' + i + '">Face ' + (i + 1) + '</button>';
    }).join('');

    panel.innerHTML =
      '<div class="ahp-card-editor-actions ahp-card-editor-actions-grid">' +
      '<button type="button" class="ahp-tool-btn" id="ahpDuplicateDeviceBtn"><i class="fas fa-copy"></i> Duplicate</button>' +
      '<button type="button" class="ahp-tool-btn" id="ahpResetDeviceFaceBtn"><i class="fas fa-undo"></i> Reset face</button>' +
      '<button type="button" class="ahp-tool-btn ahp-tool-btn-danger" id="ahpDeleteDeviceBtn"><i class="fas fa-trash"></i> Delete</button>' +
      '</div>' +
      '<p class="ahp-save-hint"><i class="fas fa-info-circle"></i> Same styling options as Home cards. Click <strong>Save</strong> to keep.</p>' +

      '<div class="ahp-style-section"><div class="ahp-style-section-title">Device card</div>' +
      '<div class="ahp-field"><label for="deOrientation">Card shape</label>' +
      '<select id="deOrientation">' +
      '<option value="square"' + (orientation === 'square' ? ' selected' : '') + '>Square (1 x 1)</option>' +
      '<option value="horizontal"' + (orientation === 'horizontal' ? ' selected' : '') + '>Horizontal / Wide (2 x 1)</option>' +
      '<option value="vertical"' + (orientation === 'vertical' ? ' selected' : '') + '>Vertical / Tall (1 x 2)</option>' +
      '<option value="large"' + (orientation === 'large' ? ' selected' : '') + '>Large (2 x 2)</option>' +
      '<option value="custom"' + (orientation === 'custom' ? ' selected' : '') + '>Custom (use width/height)</option>' +
      '</select></div>' +
      rangeFieldHtml('deCardWidth', 'Card width (columns)', device.colSpan || 1, 1, 3, '') +
      rangeFieldHtml('deCardHeight', 'Card height (rows)', device.rowSpan || 1, 1, 3, '') +
      rangeFieldHtml('deCardRadius', 'Card border radius', cardRadiusVal, 0, 48, 'px') +
      '<div class="ahp-field"><label for="deDefaultFace">Default face</label>' +
      '<select id="deDefaultFace">' +
      [0, 1, 2, 3].map(function (i) {
        return '<option value="' + i + '"' + (getDeviceDefaultFace(device) === i ? ' selected' : '') + '>Face ' + (i + 1) + '</option>';
      }).join('') +
      '</select></div>' +
      colorFieldHtml('deColor', 'Default face color', device.color || '#334155') +
      colorFieldHtml('deTextColor', 'Default text color', device.textColor || '#ffffff') +
      '</div>' +

      '<div class="ahp-style-section ahp-face-editor">' +
      '<div class="ahp-style-section-title">Face ' + (activeDeviceFaceIndex + 1) + ' of 4</div>' +
      '<label class="ahp-face-editor-label">Select face to style (each rotates independently)</label>' +
      '<div class="ahp-face-tabs" role="tablist">' + faceTabs + '</div>' +
      colorFieldHtml('deFaceColor', 'Face background color', deviceFaceBg(face, device)) +
      rangeFieldHtml('deFaceRadius', 'Face border radius', faceRadiusVal !== '' ? faceRadiusVal : cardRadiusVal, 0, 48, 'px') +
      colorFieldHtml('deFaceTextColor', 'Text color', deviceFaceTextColor(face, device)) +
      colorFieldHtml('deFaceIconColor', 'Icon color', deviceFaceIconColor(face, device)) +
      iconPickerHtml('deFaceIcon', 'Icon', face.icon || device.icon || 'fa-cube') +
      rangeFieldHtml('deFaceIconSize', 'Icon size', deviceFaceIconSize(face, device), 10, 36, 'px') +
      '<div class="ahp-field"><label for="deFaceName">Name / title</label><input id="deFaceName" value="' + esc(face.name || device.name || '') + '"></div>' +
      rangeFieldHtml('deFaceNameSize', 'Name font size', deviceFaceNameSize(face, device), 6, 22, 'px') +
      fontFamilySelectHtml('deFaceNameFont', 'Name font family', face.titleFontFamily || device.titleFontFamily || 'system') +
      '<div class="ahp-field"><label for="deFaceValue">Value</label><input id="deFaceValue" value="' + esc(face.value != null ? face.value : '') + '" placeholder="e.g. 12"></div>' +
      '<div class="ahp-field"><label for="deFaceUnit">Unit</label><input id="deFaceUnit" value="' + esc(face.unit != null ? face.unit : '') + '" placeholder="W, %"></div>' +
      rangeFieldHtml('deFaceValueSize', 'Value font size', deviceFaceValueSize(face, device), 8, 36, 'px') +
      fontFamilySelectHtml('deFaceValueFont', 'Value font family', face.subFontFamily || device.subFontFamily || 'system') +
      '<div class="ahp-field"><label for="deFaceLocation">Location</label><input id="deFaceLocation" value="' + esc(face.location != null ? face.location : '') + '"></div>' +
      rangeFieldHtml('deFaceLocSize', 'Location font size', deviceFaceLocSize(face, device), 6, 16, 'px') +
      fontFamilySelectHtml('deFaceLocFont', 'Location font family', face.locFontFamily || device.locFontFamily || 'system') +
      '</div>' +

      '<div class="ahp-style-section"><div class="ahp-style-section-title">Device options</div>' +
      '<div class="ahp-field ahp-check-field"><label><input type="checkbox" id="deOnline"' + (device.online !== false ? ' checked' : '') + '> Online</label></div>' +
      '<div class="ahp-field ahp-check-field"><label><input type="checkbox" id="dePower"' + (device.power !== false ? ' checked' : '') + '> Show power button</label></div>' +
      '<div class="ahp-field ahp-check-field"><label><input type="checkbox" id="deStacked"' + (device.stacked ? ' checked' : '') + '> Stacked card (peek)</label></div>' +
      '<div class="ahp-field"><label for="deStackLabel">Stack label</label><input id="deStackLabel" value="' + esc(device.stackLabel || '') + '"></div>' +
      '</div>';

    ['deFaceName', 'deFaceValue', 'deFaceUnit', 'deFaceLocation', 'deStackLabel'].forEach(function (id) {
      var node = document.getElementById(id);
      if (node) node.addEventListener('input', applyDeviceEditor);
    });
    ['deOnline', 'dePower', 'deStacked'].forEach(function (id) {
      var node = document.getElementById(id);
      if (node) node.addEventListener('change', applyDeviceEditor);
    });

    var rangeIds = ['deCardWidth', 'deCardHeight', 'deCardRadius', 'deFaceRadius', 'deFaceIconSize', 'deFaceNameSize', 'deFaceValueSize', 'deFaceLocSize'];
    rangeIds.forEach(function (id) {
      var node = document.getElementById(id);
      if (!node) return;
      syncRangeThumb(node);
      var updateLabel = function () {
        var unit = (id === 'deCardWidth' || id === 'deCardHeight') ? '' : 'px';
        updateRangeUi(node, unit);
        if (id === 'deCardWidth' || id === 'deCardHeight') {
          var orient = document.getElementById('deOrientation');
          if (orient) orient.value = 'custom';
        }
        applyDeviceEditor();
      };
      node.addEventListener('input', updateLabel);
      node.addEventListener('change', updateLabel);
    });

    ['deFaceNameFont', 'deFaceValueFont', 'deFaceLocFont', 'deOrientation', 'deDefaultFace'].forEach(function (id) {
      var node = document.getElementById(id);
      if (node) node.addEventListener('change', function () {
        applyDeviceEditor();
        if (id === 'deOrientation') syncDeviceEditor(true);
        if (id === 'deDefaultFace') {
          activeDeviceFaceIndex = getDeviceDefaultFace(device);
          syncDeviceEditor(true);
          updateDeviceSlot(selectedDeviceId);
        }
      });
    });

    bindColorField('deColor', applyDeviceEditor);
    bindColorField('deTextColor', applyDeviceEditor);
    bindColorField('deFaceColor', applyDeviceEditor);
    bindColorField('deFaceTextColor', applyDeviceEditor);
    bindColorField('deFaceIconColor', applyDeviceEditor);
    bindIconPicker(panel);

    panel.querySelectorAll('[data-dev-face]').forEach(function (tab) {
      tab.addEventListener('click', function () {
        applyDeviceEditor();
        activeDeviceFaceIndex = parseInt(tab.getAttribute('data-dev-face'), 10) || 0;
        syncDeviceEditor(true);
        updateDeviceSlot(selectedDeviceId);
        refreshCubePlayback(document.getElementById('ahpDevicesGrid'));
      });
    });

    var del = document.getElementById('ahpDeleteDeviceBtn');
    if (del) del.addEventListener('click', function () {
      if (!confirm('Delete this device?')) return;
      delete deviceAnimOffsets[selectedDeviceId];
      page.devices = (page.devices || []).filter(function (d) { return d.id !== selectedDeviceId; });
      selectedDeviceId = null;
      deviceEditorDeviceId = null;
      renderDevicesPage();
      markLayoutDirty({ historyNow: true });
      showToast('Device removed');
    });
    var dup = document.getElementById('ahpDuplicateDeviceBtn');
    if (dup) dup.addEventListener('click', duplicateDevice);
    var resetFace = document.getElementById('ahpResetDeviceFaceBtn');
    if (resetFace) resetFace.addEventListener('click', resetSelectedDeviceFace);
  }

  function applyDeviceEditor(opts) {
    opts = opts || {};
    var page = getDevicesPage();
    var device = (page.devices || []).find(function (d) { return d.id === selectedDeviceId; });
    if (!device) return;
    ensureDeviceFaces(device);

    if (document.getElementById('deColor')) device.color = getVal('deColor') || device.color || '#334155';
    if (document.getElementById('deTextColor')) device.textColor = getVal('deTextColor') || '#ffffff';

    var sizeDirty = false;
    var orientationNode = document.getElementById('deOrientation');
    var cardWidthNode = document.getElementById('deCardWidth');
    var cardHeightNode = document.getElementById('deCardHeight');
    var prevCol = device.colSpan;
    var prevRow = device.rowSpan;
    if (orientationNode && orientationNode.value && orientationNode.value !== 'custom') {
      applyCardOrientation(device, orientationNode.value);
      if (cardWidthNode) {
        cardWidthNode.value = device.colSpan;
        updateRangeUi(cardWidthNode, '');
      }
      if (cardHeightNode) {
        cardHeightNode.value = device.rowSpan;
        updateRangeUi(cardHeightNode, '');
      }
    } else {
      if (cardWidthNode) device.colSpan = Math.max(1, Math.min(3, parseInt(cardWidthNode.value, 10) || 1));
      if (cardHeightNode) device.rowSpan = Math.max(1, Math.min(3, parseInt(cardHeightNode.value, 10) || 1));
      device.orientation = getCardOrientation(device);
      if (orientationNode) orientationNode.value = device.orientation;
    }
    ensureDeviceSize(device);
    if (device.colSpan !== prevCol || device.rowSpan !== prevRow) sizeDirty = true;

    var cardRadiusNode = document.getElementById('deCardRadius');
    if (cardRadiusNode) device.borderRadius = parseInt(cardRadiusNode.value, 10);

    var defaultFaceNode = document.getElementById('deDefaultFace');
    var prevDefault = getDeviceDefaultFace(device);
    if (defaultFaceNode) {
      device.defaultFace = Math.max(0, Math.min(3, parseInt(defaultFaceNode.value, 10) || 0));
    }

    var face = device.faces[activeDeviceFaceIndex];
    if (face) {
      if (document.getElementById('deFaceName')) face.name = getVal('deFaceName');
      if (document.getElementById('deFaceValue')) face.value = getVal('deFaceValue');
      if (document.getElementById('deFaceUnit')) face.unit = getVal('deFaceUnit');
      if (document.getElementById('deFaceLocation')) face.location = getVal('deFaceLocation');
      if (document.getElementById('deFaceColor')) face.color = getVal('deFaceColor') || device.color || '#334155';
      if (document.getElementById('deFaceTextColor')) face.textColor = getVal('deFaceTextColor') || '#ffffff';
      if (document.getElementById('deFaceIconColor')) face.iconColor = getVal('deFaceIconColor') || '#ffffff';
      if (document.getElementById('deFaceIcon')) {
        face.icon = normalizeFaIcon(getVal('deFaceIcon') || device.icon || 'fa-cube');
      }

      var faceRadiusNode = document.getElementById('deFaceRadius');
      if (faceRadiusNode) {
        var cardR = parseInt(device.borderRadius, 10);
        if (isNaN(cardR)) cardR = 16;
        var fr = parseInt(faceRadiusNode.value, 10);
        face.borderRadius = fr !== cardR ? fr : null;
      }
      var iconSizeNode = document.getElementById('deFaceIconSize');
      if (iconSizeNode) face.iconSize = parseInt(iconSizeNode.value, 10);
      var nameSizeNode = document.getElementById('deFaceNameSize');
      if (nameSizeNode) face.titleFontSize = parseInt(nameSizeNode.value, 10);
      var valueSizeNode = document.getElementById('deFaceValueSize');
      if (valueSizeNode) face.subFontSize = parseInt(valueSizeNode.value, 10);
      var locSizeNode = document.getElementById('deFaceLocSize');
      if (locSizeNode) face.locFontSize = parseInt(locSizeNode.value, 10);
      var nameFontNode = document.getElementById('deFaceNameFont');
      if (nameFontNode) face.titleFontFamily = nameFontNode.value;
      var valueFontNode = document.getElementById('deFaceValueFont');
      if (valueFontNode) face.subFontFamily = valueFontNode.value;
      var locFontNode = document.getElementById('deFaceLocFont');
      if (locFontNode) face.locFontFamily = locFontNode.value;

      if (activeDeviceFaceIndex === 0) syncDeviceFromFace0(device);
    }

    if (document.getElementById('deOnline')) device.online = getCheck('deOnline');
    if (document.getElementById('dePower')) device.power = getCheck('dePower');
    if (document.getElementById('deStacked')) device.stacked = getCheck('deStacked');
    if (document.getElementById('deStackLabel')) device.stackLabel = getVal('deStackLabel');

    /* Size changes need full grid reflow; style-only can patch slot */
    if (sizeDirty) {
      renderDevicesPage({ skipEditor: true });
    } else {
      updateDeviceSlot(selectedDeviceId);
    }
    syncDevicePicker();
    if (defaultFaceNode && getDeviceDefaultFace(device) !== prevDefault) {
      activeDeviceFaceIndex = device.defaultFace;
    }
    if (!opts.skipDirty) markLayoutDirty();
  }

  function duplicateDevice() {
    var page = getDevicesPage();
    var source = (page.devices || []).find(function (d) { return d.id === selectedDeviceId; });
    if (!source) return;
    var copy = JSON.parse(JSON.stringify(source));
    copy.id = 'dev-' + Date.now();
    copy.name = (copy.name || 'Device') + ' Copy';
    if (copy.faces && copy.faces[0]) copy.faces[0].name = copy.name;
    page.devices.push(copy);
    selectedDeviceId = copy.id;
    activeDeviceFaceIndex = 0;
    deviceEditorDeviceId = null;
    renderDevicesPage();
    markLayoutDirty({ historyNow: true });
    showToast('Device duplicated');
  }

  function resetSelectedDeviceFace() {
    var page = getDevicesPage();
    var device = (page.devices || []).find(function (d) { return d.id === selectedDeviceId; });
    if (!device) return;
    ensureDeviceFaces(device);
    var label = device.name || ('Face ' + (activeDeviceFaceIndex + 1));
    device.faces[activeDeviceFaceIndex] = {
      name: activeDeviceFaceIndex === 0 ? label : (label + ' - ' + String.fromCharCode(65 + activeDeviceFaceIndex)),
      value: activeDeviceFaceIndex === 0 ? (device.value || '') : '',
      unit: activeDeviceFaceIndex === 0 ? (device.unit || '') : '',
      location: activeDeviceFaceIndex === 0 ? (device.location || '') : '',
      icon: activeDeviceFaceIndex === 0 ? (device.icon || 'fa-cube') : 'fa-cube',
      color: device.color || '#334155',
      textColor: device.textColor || '#ffffff'
    };
    syncDeviceEditor(true);
    updateDeviceSlot(selectedDeviceId);
    markLayoutDirty();
    showToast('Face reset');
  }

  function addDevice() {
    var page = getDevicesPage();
    if (!page.devices) page.devices = [];
    var id = 'dev-' + Date.now();
    var device = {
      id: id,
      name: 'New Device',
      value: '',
      unit: '',
      icon: 'fa-cube',
      color: '#6366f1',
      textColor: '#ffffff',
      location: '',
      online: true,
      power: true,
      stacked: false,
      stackLabel: '',
      defaultFace: 0,
      borderRadius: 16,
      colSpan: 1,
      rowSpan: 1
    };
    page.devices.push(device);
    ensureDeviceFaces(device);
    selectedDeviceId = id;
    activeDeviceFaceIndex = 0;
    deviceEditorDeviceId = null;
    switchPage('devices');
    markLayoutDirty({ historyNow: true });
    showToast('Device added');
  }

  function syncDevicesToolFields() {
    var page = getDevicesPage();
    var h = page.header || {};
    var s = page.status || {};
    setVal('toolDevTitle', h.title || '');
    setVal('toolDevSub', h.subtitle || '');
    setVal('toolDevOnline', s.online != null ? s.online : 0);
    setVal('toolDevOffline', s.offline != null ? s.offline : 0);
  }

  function renderAll() {
    ensureLayoutStructure();
    renderBottomNav();
    renderActivePage();
    applyTheme();
    syncStaticHomeView();
    syncToolFields();
    renderThemePresets();
    renderHomeTemplates();
    renderStaticUiTemplates();
    initAllRangeSliders();
    if (selectedCardId) syncCardEditor(true);
    if (selectedDeviceId) syncDeviceEditor(true);
  }

  function selectLegacyCard(cardId) {
    selectedCardId = cardId;
    activeFaceIndex = 0;
    renderCardPicker();
    syncCardEditor(true);
    setActiveMenu('cards');
    updateSelectedCardHighlight();
    refreshCubePlayback(el.grid);
  }

  function updateLegacySelectedCardHighlight() {
    if (!el.grid) return;
    el.grid.querySelectorAll('.ahp-card').forEach(function (slot) {
      var id = slot.getAttribute('data-card-id');
      var isSelected = editMode && !!selectedCardId && id === selectedCardId && cardEditorCardId === selectedCardId;
      slot.classList.toggle('ahp-card-selected', isSelected);
      slot.setAttribute('aria-selected', isSelected ? 'true' : 'false');
      var badge = slot.querySelector('.ahp-slot-selected-badge');
      if (isSelected) {
        if (!badge) {
          badge = document.createElement('span');
          badge.className = 'ahp-slot-selected-badge';
          slot.appendChild(badge);
        }
        badge.textContent = 'Editing - Face ' + (activeFaceIndex + 1);
      } else if (badge) {
        badge.remove();
      }
    });
  }

  function renderLegacyCardPicker() {
    var sel = document.getElementById('ahpCardSelect');
    if (!sel) return;
    var current = selectedCardId || sel.value;
    sel.innerHTML = '<option value="">Choose a card to edit...</option>' +
      (layout.cards || []).map(function (c) {
        var label = (c.label || c.title || c.id) + ' (' + (c.type || 'link') + ')';
        return '<option value="' + esc(c.id) + '"' + (c.id === current ? ' selected' : '') + '>' + esc(label) + '</option>';
      }).join('');
    if (!cardPickerBound) {
      cardPickerBound = true;
      sel.addEventListener('change', function () {
        applyCardEditor();
        var id = sel.value;
        if (id) selectCard(id);
        else {
          selectedCardId = null;
          cardEditorCardId = null;
          syncCardEditor(true);
          updateSelectedCardHighlight();
        }
      });
    }
  }

  function getColorVal(id) {
    var hexIn = document.getElementById(id + 'Hex');
    var colorIn = document.getElementById(id);
    if (hexIn && hexIn.value) {
      var raw = hexIn.value.trim();
      if (isGradientColor(raw) || /^rgb/i.test(raw)) return raw;
      var fromHex = normalizeHex(raw);
      if (fromHex) return fromHex;
      return raw;
    }
    if (colorIn) {
      var stored = colorIn.getAttribute('data-bg-value');
      if (stored) return stored;
      return normalizeHex(colorIn.value) || colorIn.value || '';
    }
    return '';
  }

  function flushThemeFromTools(silent) {
    var t = getTheme();
    t.logoIcon = getVal('toolLogoIcon');
    t.gridGap = normalizeGridGap(getVal('toolGridGap'), 4);
    t.gridColumns = normalizeGridColumns(getVal('toolGridColumns'), t.gridColumns || 3);
    t.gridRows = normalizeGridRows(getVal('toolGridRows'), t.gridRows || 3);
    t.gridRowHeight = normalizeGridRowHeight(getVal('toolGridRowHeight'), t.gridRowHeight || 96);
    // Square cells for every account; radius alone changes corner style.
    t.squareTiles = true;
    t.cardBorderRadius = parseInt(getVal('toolCardBorderRadius'), 10);
    if (isNaN(t.cardBorderRadius)) t.cardBorderRadius = 0;
    t.cardBorderRadius = Math.max(0, Math.min(72, t.cardBorderRadius));
    t.cardSpeed = cardSpeedFromFaceSeconds(getVal('toolCardFaceSec') || faceSecondsFromCardSpeed(t.cardSpeed));
    t.showAlerts = getCheck('toolShowAlerts');
    t.showBottomNav = getCheck('toolShowBottomNav');
    t.bgStyle = getVal('toolBgStyle') || t.bgStyle || 'light';
    if (document.getElementById('toolShowWelcome')) {
      t.showWelcome = getCheck('toolShowWelcome');
    }

    var colorKeys = [
      ['toolAccentColor', 'accentColor'],
      ['toolAccentGreen', 'accentGreen'],
      ['toolSurfaceColor', 'surfaceColor'],
      ['toolWelcomeStart', 'welcomeStart'],
      ['toolWelcomeEnd', 'welcomeEnd'],
      ['toolPhoneBg', 'phoneBg'],
      ['toolAlertBg', 'alertBg'],
      ['toolNavActiveColor', 'navActiveColor'],
      ['toolTextPrimary', 'textPrimary'],
      ['toolTextMuted', 'textMuted'],
      ['toolNavBg', 'navBg']
    ];
    colorKeys.forEach(function (pair) {
      var val = getBackgroundColorVal(pair[0]) || getColorVal(pair[0]);
      if (val) t[pair[1]] = val;
    });
    if (!silent) markLayoutDirty();
  }

  function flushAllCardsBeforeSave() {
    applyCardEditor({ skipDirty: true });
    (layout.cards || []).forEach(function (card) {
      ensureFaces(card);
    });
  }

  function updateCardSelectOption(card) {
    var sel = document.getElementById('ahpCardSelect');
    if (!sel || !card) return;
    for (var i = 0; i < sel.options.length; i++) {
      if (sel.options[i].value === card.id) {
        var timing = card.faceSec != null ? (getCardFaceSec(card) + 's face') : 'layout default';
        sel.options[i].textContent = (card.label || card.title || card.id) + ' (' + (card.type || 'link') + ' - ' + timing + ')';
        break;
      }
    }
  }

  function renderThemePresets() {
    var wrap = document.getElementById('ahpThemePresets');
    if (!wrap) return;
    var current = getTheme().bgStyle || 'light';
    wrap.innerHTML = Object.keys(THEME_META).map(function (key) {
      var meta = THEME_META[key];
      var preset = BG_PRESETS[key] || {};
      var colors = meta.swatches || [
        preset.phoneBg,
        preset.surfaceColor || preset.alertBg,
        preset.accentColor,
        preset.navActiveColor
      ];
      var swatches = colors.map(function (c) {
        return '<span style="background:' + esc(c || '#334155') + '"></span>';
      }).join('');
      return '<button type="button" class="ahp-theme-preset' + (key === current ? ' active' : '') + '" data-theme="' + esc(key) + '">' +
        '<div class="ahp-theme-preset-swatches">' + swatches + '</div>' +
        '<strong>' + esc(meta.label) + '</strong>' +
        '<small>' + esc(meta.note) + '</small></button>';
    }).join('');
    wrap.querySelectorAll('.ahp-theme-preset').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var key = btn.getAttribute('data-theme');
        setVal('toolBgStyle', key);
        applyBgPreset(key);
        renderThemePresets();
      });
    });
  }

  function renderPagePicker() {
    /* Pages panel removed â€” routes are edited per face in Cards */
  }

  function syncPageFields() {
    var card = layout.cards.find(function (c) { return c.id === selectedPageCardId; });
    setVal('toolPageRoute', card ? (card.pageRoute || '') : '');
    setVal('toolPageLabel', card ? (card.pageLabel || '') : '');
  }

  function applyPageToCard() {
    if (!selectedPageCardId) return;
    var card = layout.cards.find(function (c) { return c.id === selectedPageCardId; });
    if (!card) return;
    card.pageRoute = getVal('toolPageRoute').trim();
    card.pageLabel = getVal('toolPageLabel');
    if (selectedCardId === card.id) syncCardEditor(true);
    else updateCardSlot(card.id);
    renderPagePicker();
    showToast('Page link updated');
  }

  function syncToolFields() {
    var h = layout.header || {};
    var w = layout.welcome || {};
    var a = layout.alerts || {};
    var t = getTheme();

    setVal('toolHeaderTitle', h.title);
    setVal('toolHeaderSub', h.subtitle);
    setVal('toolWelcomeGreet', w.greeting);
    setVal('toolWelcomeEmail', w.email);
    setVal('toolWelcomeCaption', w.caption || 'Manage your ecosystem');
    setVal('toolAlertCount', a.count);
    setVal('toolAlertUsers', a.newUsers);
    setVal('toolAlertBadge', a.badgeText);
    setCheck('toolShowAlertBadge', a.showBadge !== false);

    setVal('toolLogoIcon', t.logoIcon);
    setVal('toolGridGap', t.gridGap);
    setVal('toolGridColumns', getGridColumns());
    setVal('toolGridRows', getGridRows());
    setVal('toolGridRowHeight', defaultGridRowHeight());
    setVal('toolCardBorderRadius', t.cardBorderRadius != null ? t.cardBorderRadius : 0);
    var faceSec = faceSecondsFromCardSpeed(t.cardSpeed || 16);
    setVal('toolCardFaceSec', faceSec);
    setCheck('toolShowWelcome', shouldShowWelcomeBanner());
    setCheck('toolShowAlerts', shouldShowAlertsStrip());
    setCheck('toolShowBottomNav', t.showBottomNav !== false);

    setVal('toolBgStyle', t.bgStyle);
    setBackgroundColorField('toolAccentColor', t.accentColor);
    setBackgroundColorField('toolAccentGreen', t.accentGreen);
    setBackgroundColorField('toolSurfaceColor', t.surfaceColor);
    setBackgroundColorField('toolWelcomeStart', t.welcomeStart);
    setBackgroundColorField('toolWelcomeEnd', t.welcomeEnd);
    setBackgroundColorField('toolPhoneBg', t.phoneBg);
    setBackgroundColorField('toolAlertBg', t.alertBg);
    setBackgroundColorField('toolNavActiveColor', t.navActiveColor);
    setBackgroundColorField('toolTextPrimary', t.textPrimary);
    setBackgroundColorField('toolTextMuted', t.textMuted);
    setBackgroundColorField('toolNavBg', t.navBg);

    var gapNode = document.getElementById('toolGridGap');
    if (gapNode) {
      gapNode.value = normalizeGridGap(t.gridGap, 4);
      updateRangeUi(gapNode, 'px');
    }
    var gridColsNode = document.getElementById('toolGridColumns');
    if (gridColsNode) {
      gridColsNode.value = getGridColumns();
      updateRangeUi(gridColsNode, '');
    }
    var gridRowsNode = document.getElementById('toolGridRows');
    if (gridRowsNode) {
      gridRowsNode.value = getGridRows();
      updateRangeUi(gridRowsNode, '');
    }
    var gridRowNode = document.getElementById('toolGridRowHeight');
    if (gridRowNode) {
      gridRowNode.value = defaultGridRowHeight();
      updateRangeUi(gridRowNode, 'px');
    }
    var cardRadiusNode = document.getElementById('toolCardBorderRadius');
    if (cardRadiusNode) {
      cardRadiusNode.value = t.cardBorderRadius != null ? t.cardBorderRadius : 0;
      updateRangeUi(cardRadiusNode, 'px');
    }
    var faceSecNode = document.getElementById('toolCardFaceSec');
    if (faceSecNode) {
      faceSecNode.value = faceSecondsFromCardSpeed(t.cardSpeed || 16);
      updateRangeUi(faceSecNode, ' sec');
    }
    syncNavEditor();
    syncPageFields();
  }

  function setVal(id, v) {
    var node = document.getElementById(id);
    if (node) node.value = v != null ? v : '';
  }

  function setCheck(id, checked) {
    var node = document.getElementById(id);
    if (node) node.checked = !!checked;
  }

  function getVal(id) {
    var node = document.getElementById(id);
    return node ? node.value : '';
  }

  function getCheck(id) {
    var node = document.getElementById(id);
    return node ? node.checked : false;
  }

  function normalizeHex(val) {
    if (!val) return null;
    var s = String(val).trim();
    if (s.charAt(0) !== '#') s = '#' + s;
    return /^#[0-9A-Fa-f]{6}$/.test(s) ? s.toLowerCase() : null;
  }

  function isGradientColor(val) {
    return val && String(val).indexOf('gradient') !== -1;
  }

  function colorToPickerHex(value) {
    if (!value) return '#000000';
    var solid = normalizeHex(value);
    if (solid) return solid;
    var match = String(value).match(/#[0-9A-Fa-f]{6}/);
    return match ? match[0].toLowerCase() : '#000000';
  }

  function getBackgroundColorVal(id) {
    var hexIn = document.getElementById(id + 'Hex');
    var colorIn = document.getElementById(id);
    if (hexIn && hexIn.value) {
      var raw = hexIn.value.trim();
      if (isGradientColor(raw) || /^rgb/i.test(raw)) return raw;
      var fromHex = normalizeHex(raw);
      if (fromHex) return fromHex;
    }
    if (colorIn) {
      var stored = colorIn.getAttribute('data-bg-value');
      if (stored) return stored;
      return normalizeHex(colorIn.value) || colorIn.value || '';
    }
    return '';
  }

  function setBackgroundColorField(id, cssValue) {
    var val = cssValue || '#000000';
    var colorIn = document.getElementById(id);
    var hexIn = document.getElementById(id + 'Hex');
    var preview = document.getElementById(id + 'Preview');
    var pickerHex = colorToPickerHex(val);
    if (colorIn) {
      colorIn.value = pickerHex;
      colorIn.setAttribute('data-bg-value', val);
    }
    if (hexIn) hexIn.value = isGradientColor(val) ? val : (normalizeHex(val) || val);
    if (preview) preview.style.background = val;
  }

  function updateColorUi(colorId) {
    var colorIn = document.getElementById(colorId);
    if (!colorIn) return;
    var hex = colorIn.value;
    var hexIn = document.getElementById(colorId + 'Hex');
    var preview = document.getElementById(colorId + 'Preview');
    var css = normalizeHex(hex) || hex;
    colorIn.setAttribute('data-bg-value', css);
    if (hexIn && document.activeElement !== hexIn) hexIn.value = css;
    if (preview) preview.style.background = css;
  }

  function updateBackgroundColorUi(colorId) {
    var colorIn = document.getElementById(colorId);
    if (!colorIn) return;
    var css = colorIn.getAttribute('data-bg-value') || colorIn.value;
    var hexIn = document.getElementById(colorId + 'Hex');
    var preview = document.getElementById(colorId + 'Preview');
    if (preview) preview.style.background = css;
    if (hexIn && document.activeElement !== hexIn) {
      hexIn.value = isGradientColor(css) ? css : (normalizeHex(css) || css);
    }
  }

  function setColorVal(id, v) {
    var val = v || '#000000';
    setVal(id, val);
    updateColorUi(id);
  }

  function bindColorField(colorId, onChange) {
    var colorIn = document.getElementById(colorId);
    if (!colorIn) return;
    colorIn.addEventListener('input', function () {
      var n = normalizeHex(colorIn.value) || colorIn.value;
      colorIn.setAttribute('data-bg-value', n);
      var hexIn = document.getElementById(colorId + 'Hex');
      var preview = document.getElementById(colorId + 'Preview');
      if (hexIn) hexIn.value = n;
      if (preview) preview.style.background = n;
      if (onChange) onChange(n);
    });
    colorIn.addEventListener('change', function () {
      var n = normalizeHex(colorIn.value) || colorIn.value;
      colorIn.setAttribute('data-bg-value', n);
      var hexIn = document.getElementById(colorId + 'Hex');
      var preview = document.getElementById(colorId + 'Preview');
      if (hexIn) hexIn.value = n;
      if (preview) preview.style.background = n;
      if (onChange) onChange(n);
    });
    var hexIn = document.getElementById(colorId + 'Hex');
    if (hexIn) {
      hexIn.addEventListener('input', function () {
        var raw = hexIn.value.trim();
        var preview = document.getElementById(colorId + 'Preview');
        if (isGradientColor(raw) || /^rgb/i.test(raw)) {
          colorIn.setAttribute('data-bg-value', raw);
          if (preview) preview.style.background = raw;
          if (onChange) onChange(raw);
          return;
        }
        var n = normalizeHex(raw);
        if (n) {
          colorIn.value = n;
          colorIn.setAttribute('data-bg-value', n);
          var preview = document.getElementById(colorId + 'Preview');
          if (preview) preview.style.background = n;
          if (onChange) onChange(n);
        }
      });
      hexIn.addEventListener('change', function () {
        var raw = hexIn.value.trim();
        if (isGradientColor(raw) || /^rgb/i.test(raw)) return;
        var n = normalizeHex(raw);
        hexIn.value = n || colorIn.value;
      });
    }
    if (colorIn.getAttribute('data-bg-value')) {
      updateBackgroundColorUi(colorId);
    } else {
      updateColorUi(colorId);
    }
  }

  function fontFamilySelectHtml(id, label, value) {
    var v = value || 'system';
    var opts = FONT_FAMILIES.map(function (f) {
      return '<option value="' + f.id + '"' + (v === f.id ? ' selected' : '') + '>' + esc(f.label) + '</option>';
    }).join('');
    return '<div class="ahp-field"><label for="' + id + '">' + esc(label) + '</label>' +
      '<select id="' + id + '">' + opts + '</select></div>';
  }

  function rangeFieldHtml(id, label, value, min, max, unit) {
    var v = value != null && value !== '' ? value : min;
    var suffix = unit == null ? 'px' : unit;
    var pct = rangePercent(v, min, max);
    var showScale = (Number(max) - Number(min)) <= 80;
    return '<div class="ahp-field ahp-range-field">' +
      '<div class="ahp-range-head">' +
      '<label for="' + id + '">' + esc(label) + '</label>' +
      '<span class="ahp-range-val" id="' + id + 'Val">' +
      '<span class="ahp-range-val-num">' + v + '</span>' +
      (suffix ? '<span class="ahp-range-val-unit">' + esc(suffix) + '</span>' : '') +
      '</span></div>' +
      '<div class="ahp-range-control">' +
      '<input type="range" id="' + id + '" min="' + min + '" max="' + max + '" step="1" value="' + v + '" ' +
      'style="--ahp-range-pct:' + pct + '%" aria-valuemin="' + min + '" aria-valuemax="' + max + '" aria-valuenow="' + v + '">' +
      '</div>' +
      (showScale
        ? '<div class="ahp-range-scale"><span>' + min + esc(suffix) + '</span><span>' + max + esc(suffix) + '</span></div>'
        : '') +
      '</div>';
  }

  function cardRadiusFieldHtml(id, label, value, min, max) {
    var v = value != null && value !== '' ? value : min;
    var pct = rangePercent(v, min, max);
    return '<div class="ahp-field ahp-range-field ahp-radius-field">' +
      '<div class="ahp-range-head">' +
      '<label for="' + id + '">' + esc(label) + '</label>' +
      '<span class="ahp-range-val" id="' + id + 'Val">' +
      '<span class="ahp-range-val-num">' + v + '</span><span class="ahp-range-val-unit">px</span></span></div>' +
      '<div class="ahp-radius-field-body">' +
      '<div class="ahp-range-control">' +
      '<input type="range" id="' + id + '" min="' + min + '" max="' + max + '" step="1" value="' + v + '" ' +
      'style="--ahp-range-pct:' + pct + '%" aria-valuemin="' + min + '" aria-valuemax="' + max + '" aria-valuenow="' + v + '">' +
      '</div>' +
      '<div class="ahp-radius-live-preview" id="' + id + 'LivePreview" aria-hidden="true">' +
      '<div class="ahp-radius-live-box" style="border-radius:' + v + 'px"></div>' +
      '<small>Preview</small></div></div>' +
      '<div class="ahp-range-scale"><span>' + min + 'px</span><span>' + max + 'px</span></div></div>';
  }

  function colorPairRowHtml(id1, label1, val1, id2, label2, val2) {
    return '<div class="ahp-color-row-2">' +
      colorFieldHtml(id1, label1, val1) +
      colorFieldHtml(id2, label2, val2) +
      '</div>';
  }

  function rangePercent(value, min, max) {
    var lo = Number(min);
    var hi = Number(max);
    var val = Number(value);
    if (!isFinite(lo) || !isFinite(hi) || hi === lo) return 0;
    return Math.max(0, Math.min(100, ((val - lo) / (hi - lo)) * 100));
  }

  function syncRangeThumb(node) {
    if (!node || node.type !== 'range') return;
    node.style.setProperty('--ahp-range-pct', rangePercent(node.value, node.min, node.max) + '%');
    node.setAttribute('aria-valuenow', node.value);
  }

  function updateRangeUi(node, unit) {
    if (!node || node.type !== 'range') return;
    syncRangeThumb(node);
    var valWrap = document.getElementById(node.id + 'Val');
    if (!valWrap) return;
    var suffix = unit === '' ? '' : (unit == null ? 'px' : unit);
    var numEl = valWrap.querySelector('.ahp-range-val-num');
    var unitEl = valWrap.querySelector('.ahp-range-val-unit');
    if (numEl) {
      numEl.textContent = node.value;
      if (unitEl) unitEl.textContent = suffix;
      else if (suffix) valWrap.innerHTML = '<span class="ahp-range-val-num">' + esc(node.value) + '</span><span class="ahp-range-val-unit">' + esc(suffix) + '</span>';
    } else {
      valWrap.textContent = node.value + suffix;
    }
  }

  function initAllRangeSliders() {
    document.querySelectorAll('.ahp-range-control input[type="range"]').forEach(function (node) {
      updateRangeUi(node, rangeUnitFor(node.id));
    });
  }

  function colorFieldHtml(id, label, value) {
    var v = value || '#7c3aed';
    var pickerHex = colorToPickerHex(v);
    var hexValue = isGradientColor(v) ? v : (normalizeHex(v) || pickerHex);
    return '<div class="ahp-field ahp-color-field">' +
      '<label for="' + id + '">' + esc(label) + '</label>' +
      '<div class="ahp-color-control">' +
      '<span class="ahp-color-preview" id="' + id + 'Preview" style="background:' + esc(v) + '" aria-hidden="true"></span>' +
      '<input type="color" id="' + id + '" value="' + esc(pickerHex) + '" data-bg-value="' + esc(v) + '" aria-label="' + esc(label) + ' picker">' +
      '<input type="text" class="ahp-color-hex" id="' + id + 'Hex" value="' + esc(hexValue) + '" maxlength="128" spellcheck="false" ' +
      'aria-label="' + esc(label) + ' hex code" placeholder="#000000" autocomplete="off">' +
      '</div></div>';
  }

  function iconPickerHtml(inputId, label, value) {
    var v = (value || 'fa-cube').replace(/^fas\s+/, '');
    if (v.indexOf('fa-') !== 0) v = 'fa-' + v.replace(/^fa-?/, '');
    var quick = QUICK_ICONS.map(function (icon) {
      return '<button type="button" class="ahp-icon-option' + (icon === v ? ' selected' : '') + '" data-icon="' + icon + '" title="' + icon.replace('fa-', '') + '" aria-label="' + icon.replace('fa-', '') + '">' +
        '<i class="fas ' + icon + '"></i></button>';
    }).join('');
    return '<div class="ahp-field ahp-icon-picker-field" data-icon-input="' + inputId + '">' +
      '<label>' + esc(label) + '</label>' +
      '<div class="ahp-icon-selected-row">' +
      '<span class="ahp-icon-current" id="' + inputId + 'Current"><i class="fas ' + esc(v) + '"></i></span>' +
      '<div class="ahp-icon-selected-meta">' +
      '<strong id="' + inputId + 'Name">' + esc(v.replace('fa-', '')) + '</strong>' +
      '<small>Click an icon below or open more icons</small></div>' +
      '<button type="button" class="ahp-tool-btn ahp-icon-more-btn" data-icon-target="' + inputId + '"><i class="fas fa-th"></i> More icons</button>' +
      '</div>' +
      '<div class="ahp-icon-quick">' + quick + '</div>' +
      '<input type="hidden" id="' + inputId + '" value="' + esc(v) + '">' +
      '</div>';
  }

  function ensureIconModal() {
    var modal = document.getElementById('ahpIconModal');
    if (modal) return modal;
    modal = document.createElement('div');
    modal.id = 'ahpIconModal';
    modal.className = 'ahp-icon-modal';
    modal.hidden = true;
    modal.innerHTML =
      '<div class="ahp-icon-modal-backdrop" data-close="1"></div>' +
      '<div class="ahp-icon-modal-panel" role="dialog" aria-modal="true" aria-label="Choose icon">' +
      '<div class="ahp-icon-modal-head">' +
      '<strong>Choose an icon</strong>' +
      '<button type="button" class="ahp-icon-modal-close" data-close="1" aria-label="Close"><i class="fas fa-times"></i></button>' +
      '</div>' +
      '<input type="search" id="ahpIconSearch" class="ahp-icon-search" placeholder="Search icons (home, user, bell...)" autocomplete="off">' +
      '<div class="ahp-icon-modal-grid" id="ahpIconModalGrid"></div>' +
      '</div>';
    document.body.appendChild(modal);

    modal.addEventListener('click', function (e) {
      if (e.target.getAttribute('data-close') === '1' || e.target.closest('[data-close="1"]')) {
        closeIconModal();
        return;
      }
      var opt = e.target.closest('.ahp-icon-option');
      if (!opt || !modal._targetInputId) return;
      var targetId = modal._targetInputId;
      setIconValue(targetId, opt.getAttribute('data-icon'));
      closeIconModal();
      notifyIconChange(targetId || '');
    });

    var search = modal.querySelector('#ahpIconSearch');
    if (search) {
      search.addEventListener('input', function () {
        renderIconModalGrid(search.value);
      });
    }
    return modal;
  }

  function renderIconModalGrid(query) {
    var grid = document.getElementById('ahpIconModalGrid');
    if (!grid) return;
    var q = String(query || '').trim().toLowerCase().replace(/^fa-/, '');
    var list = ICON_LIBRARY.filter(function (icon) {
      if (!q) return true;
      return icon.replace('fa-', '').indexOf(q) !== -1;
    });
    grid.innerHTML = list.map(function (icon) {
      return '<button type="button" class="ahp-icon-option" data-icon="' + icon + '" title="' + icon.replace('fa-', '') + '">' +
        '<i class="fas ' + icon + '"></i><span>' + icon.replace('fa-', '') + '</span></button>';
    }).join('') || '<p class="ahp-tool-hint">No icons found</p>';
  }

  function openIconModal(inputId) {
    var modal = ensureIconModal();
    modal._targetInputId = inputId;
    modal.hidden = false;
    document.body.classList.add('ahp-icon-modal-open');
    renderIconModalGrid('');
    var search = document.getElementById('ahpIconSearch');
    if (search) {
      search.value = '';
      setTimeout(function () { search.focus(); }, 30);
    }
  }

  function closeIconModal() {
    var modal = document.getElementById('ahpIconModal');
    if (!modal) return;
    modal.hidden = true;
    modal._targetInputId = null;
    document.body.classList.remove('ahp-icon-modal-open');
  }

  function setIconValue(inputId, icon) {
    var input = document.getElementById(inputId);
    if (input) input.value = icon;
    var current = document.getElementById(inputId + 'Current');
    if (current) current.innerHTML = '<i class="fas ' + esc(icon) + '"></i>';
    var name = document.getElementById(inputId + 'Name');
    if (name) name.textContent = icon.replace('fa-', '');
    var field = document.querySelector('[data-icon-input="' + inputId + '"]');
    if (field) {
      field.querySelectorAll('.ahp-icon-option').forEach(function (btn) {
        btn.classList.toggle('selected', btn.getAttribute('data-icon') === icon);
      });
    }
  }

  function normalizeFaIcon(icon) {
    var v = String(icon || 'fa-circle').replace(/^fas\s+/, '').trim();
    if (v.indexOf('fa-') !== 0) v = 'fa-' + v.replace(/^fa-?/, '');
    return v;
  }

  function notifyIconChange(inputId) {
    var id = String(inputId || '');
    if (id === 'navIcon') {
      var input = document.getElementById('navIcon');
      if (input) applyNavIcon(input.value);
      return;
    }
    if (id === 'deIcon' || id === 'deFaceIcon') {
      applyDeviceEditor();
      return;
    }
    if (id === 'ceFaceIcon') {
      var showIcon = document.getElementById('ceFaceShowIcon');
      if (showIcon) showIcon.checked = true;
    }
    applyCardEditor();
  }

  function bindIconPicker(panel) {
    if (!panel) return;
    panel.querySelectorAll('.ahp-icon-quick .ahp-icon-option').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var field = btn.closest('[data-icon-input]');
        if (!field) return;
        var inputId = field.getAttribute('data-icon-input');
        setIconValue(inputId, btn.getAttribute('data-icon'));
        notifyIconChange(inputId);
      });
    });
    panel.querySelectorAll('.ahp-icon-more-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        openIconModal(btn.getAttribute('data-icon-target'));
      });
    });
  }

  function syncLegacyCardEditor(forceRebuild) {
    var panel = document.getElementById('ahpCardEditor');
    if (!panel) return;
    var card = layout.cards.find(function (c) { return c.id === selectedCardId; });
    if (!card) {
      cardEditorCardId = null;
      destroyFacePreviewSortable();
      panel.innerHTML = '<p class="ahp-tool-hint">No card selected. Add a block from <strong>Blocks</strong>, or click a card in the preview to edit. Every card supports create, edit, duplicate, and delete.</p>';
      return;
    }
    if (!forceRebuild && cardEditorCardId === card.id && panel.querySelector('#ceLabel')) {
      return;
    }
    cardEditorCardId = card.id;
    card = ensureFaces(card);
    if (activeFaceIndex > 3) activeFaceIndex = 0;

    var face = card.faces[activeFaceIndex] || {};
    var type = card.type || 'link';
    if (type === 'image') {
      ensureImageBlockDefaults(card);
      ensureFaceImageDisplayDefaults(face, card);
    }
    var faceText = readFaceEditorText(card, face);
    var labelVal = faceText.label;
    var subtitleVal = faceText.subtitle;
    var faceBgColor = face.color || card.color || '#7c3aed';
    var iconSizeDefault = face.iconSize != null ? face.iconSize : (card.iconSize != null ? card.iconSize : (type === 'stat' ? 18 : 28));
    var titleSizeDefault = face.titleFontSize != null ? face.titleFontSize : (card.titleFontSize != null ? card.titleFontSize : (type === 'stat' ? 9 : 14));
    var subSizeDefault = face.subFontSize != null ? face.subFontSize : (card.subFontSize != null ? card.subFontSize : (type === 'stat' ? 22 : 10));
    var titleWeightDefault = faceTitleWeight(face, card, type);
    var subWeightDefault = faceSubWeight(face, card, type);
    var cardRadiusVal = card.borderRadius != null ? card.borderRadius : defaultCardRadius();
    var rotationDir = card.rotationDir === 'ltr' ? 'ltr' : 'rtl';
    var globalFaceSec = faceSecondsFromCardSpeed(getTheme().cardSpeed || 16);
    var useGlobalFaceSec = card.faceSec == null || card.faceSec === '';
    var faceSecVal = useGlobalFaceSec ? globalFaceSec : getCardFaceSec(card);
    var faceImageFit = normalizeImageFit(face.imageFit || card.imageFit);
    var faceImagePosition = normalizeImagePosition(face.imagePosition || card.imagePosition);
    var faceImageScale = normalizeImageScale(face.imageScale != null ? face.imageScale : card.imageScale);
    var faceContentType = normalizeFaceContentType(face, card);
    var faceImageUrl = (faceContentType === 'image' || faceContentType === 'mixed')
      ? readFaceImageUrlForEditor(card, face, activeFaceIndex)
      : '';
    ensureCardSize(card);
    var orientation = getCardShapeSelectValue(card);

    var html = '<div class="ahp-card-editor-actions ahp-card-editor-actions-grid">' +
      '<button type="button" class="ahp-tool-btn" id="ahpDuplicateCardBtn"><i class="fas fa-copy"></i> Duplicate</button>' +
      '<button type="button" class="ahp-tool-btn" id="ahpResetFaceBtn"><i class="fas fa-undo"></i> Reset face</button>' +
      '<button type="button" class="ahp-tool-btn ahp-tool-btn-danger" id="ahpDeleteCardBtn"><i class="fas fa-trash"></i> Delete card</button>' +
      '</div>' +
      '<p class="ahp-save-hint"><i class="fas fa-info-circle"></i> Changes preview live. Click <strong>Save</strong> (top left) to keep settings.</p>' +
      renderCardPreviewStrip(card) +
      '<div class="ahp-style-section"><div class="ahp-style-section-title">Card</div>' +
      '<div class="ahp-field"><label for="ceType">Block type</label>' +
      '<select id="ceType">' +
      '<option value="stack"' + (normalizeHomeBlockType(type) === 'stack' ? ' selected' : '') + '>Stack</option>' +
      '<option value="image"' + (normalizeHomeBlockType(type) === 'image' ? ' selected' : '') + '>Image</option>' +
      '<option value="mixed"' + (normalizeHomeBlockType(type) === 'mixed' ? ' selected' : '') + '>Mixed</option>' +
      '</select></div>' +
      '<div class="ahp-field"><label for="ceOrientation">Block size</label>' +
      '<select id="ceOrientation">' +
      cardShapePresetOptions(orientation) +
      '</select></div>' +
      rangeFieldHtml('ceCardWidth', 'Tile width (columns)', card.colSpan || 1, 1, getGridColumns(), '') +
      rangeFieldHtml('ceCardHeight', 'Tile height (rows)', card.rowSpan || 1, 1, getGridRows(), '') +
      '<p class="ahp-tool-hint" style="margin-top:-6px">Use 1×1 for the smallest tile. Lower <strong>Cell height</strong> in Layout to shrink all cards.</p>' +
      cardRadiusFieldHtml('ceCardRadius', 'Tile corner radius', cardRadiusVal, 0, 72) +
      '<div class="ahp-field"><label for="ceRotationDir">Rotation direction</label>' +
      '<select id="ceRotationDir">' +
      '<option value="rtl"' + (rotationDir === 'rtl' ? ' selected' : '') + '>Right to left</option>' +
      '<option value="ltr"' + (rotationDir === 'ltr' ? ' selected' : '') + '>Left to right</option>' +
      '</select></div>' +
      '<div class="ahp-field ahp-check-field"><label for="ceRotationEnabled"><input type="checkbox" id="ceRotationEnabled"' + (card.rotationEnabled === false ? '' : ' checked') + '> Rotation enabled</label></div>' +
      rangeFieldHtml('ceFaceSec', 'Face hold time', faceSecVal, 2, 8, ' sec') +
      '<div class="ahp-field ahp-check-field"><label for="ceFaceSecGlobal"><input type="checkbox" id="ceFaceSecGlobal"' + (useGlobalFaceSec ? ' checked' : '') + '> Use layout default (' + globalFaceSec + ' sec)</label></div>' +
      rangeFieldHtml('ceStartDelay', 'Start delay', card.startDelayMs != null ? Math.round(Number(card.startDelayMs) / 100) / 10 : 0, 0, 8, ' sec') +
      '<p class="ahp-tool-hint" style="margin-top:-6px">Independent stagger so tiles do not rotate together. 0 = auto stagger from tile id.</p>' +
      '<div class="ahp-field"><label for="cePageRoute">Default navigation URL</label>' +
      '<input type="text" id="cePageRoute" value="' + esc(card.pageRoute || '') + '" placeholder="/devices or https://example.com">' +
      '<p class="ahp-tool-hint" style="margin-top:6px">Fallback when a face has no URL. App tabs: <code>/home</code>, <code>/devices</code>, <code>/profile</code>, <code>/alerts</code>, <code>/reports</code>.</p></div>' +
      '</div>' +
      '<div class="ahp-style-section ahp-face-editor">' +
      '<div class="ahp-style-section-title">Face ' + (activeFaceIndex + 1) + ' of 4</div>' +
      '<p class="ahp-tool-hint ahp-face-editor-hint">Each face can be Stack, Image, or Mixed independently.</p>' +
      '<div class="ahp-field"><label for="ceFaceType">Face type</label>' +
      '<select id="ceFaceType">' +
      '<option value="stack"' + (normalizeFaceContentType(face, card) === 'stack' ? ' selected' : '') + '>Stack</option>' +
      '<option value="image"' + (normalizeFaceContentType(face, card) === 'image' ? ' selected' : '') + '>Image</option>' +
      '<option value="mixed"' + (normalizeFaceContentType(face, card) === 'mixed' ? ' selected' : '') + '>Mixed</option>' +
      '</select></div>' +
      '<div class="ahp-field"><label for="ceFaceRoute">Navigation URL</label>' +
      '<input type="text" id="ceFaceRoute" value="' + esc(face.route || '') + '" placeholder="/devices or https://example.com">' +
      '<p class="ahp-tool-hint" style="margin-top:6px">When this face is tapped in preview, the app navigates here. Leave empty to use the card default URL above.</p></div>' +
      (normalizeFaceContentType(face, card) === 'image' || normalizeFaceContentType(face, card) === 'mixed'
        ? '<div class="ahp-field"><label for="ceFaceImage">Image URL</label>' +
          '<input type="url" id="ceFaceImage" value="' + esc(faceImageUrl || String(face.image || card.image || '').trim()) + '" placeholder="Paste or upload an image">' +
          '<div class="ahp-image-upload-row">' +
          '<input type="file" id="ceFaceImageUpload" accept="image/jpeg,image/png,image/webp,image/gif" hidden>' +
          '<button type="button" class="ahp-tool-btn ahp-image-upload-btn" id="ceFaceImageUploadBtn"><i class="fas fa-upload"></i> Upload image</button>' +
          '<span class="ahp-tool-hint">Saved to your account - shown on face ' + (activeFaceIndex + 1) + '</span>' +
          '</div>' +
          '<div class="ahp-editor-image-preview' + ((faceImageUrl || face.image || card.image) ? ' has-image' : '') + '" id="ceImagePreview">' +
          ((faceImageUrl || face.image || card.image)
            ? '<img id="ceImagePreviewImg" src="' + esc(resolveMediaUrl(faceImageUrl || face.image || card.image)) + '" alt="">'
            : '<img id="ceImagePreviewImg" alt="" hidden>') +
          '</div>' +
          '<div class="ahp-image-fit-row">' +
          '<div class="ahp-field"><label for="ceFaceImageFit">Image fit</label>' +
          '<select id="ceFaceImageFit">' +
          IMAGE_FIT_OPTIONS.map(function (opt) {
            return '<option value="' + opt.value + '"' + (faceImageFit === opt.value ? ' selected' : '') + '>' + esc(opt.label) + '</option>';
          }).join('') +
          '</select></div>' +
          '<div class="ahp-field"><label for="ceFaceImagePosition">Image focus</label>' +
          '<select id="ceFaceImagePosition">' +
          IMAGE_POSITION_OPTIONS.map(function (opt) {
            return '<option value="' + opt.value + '"' + (faceImagePosition === opt.value ? ' selected' : '') + '>' + esc(opt.label) + '</option>';
          }).join('') +
          '</select></div>' +
          '</div>' +
          rangeFieldHtml('ceFaceImageScale', 'Image zoom', faceImageScale, 50, 200, '%') +
          (normalizeFaceContentType(face, card) === 'mixed'
            ? '<div class="ahp-field"><label for="ceMixedLayout">Mixed layout</label><select id="ceMixedLayout">' +
              [
                { value: 'centeredStack', label: 'Centered stack (icon → title → subtitle)' },
                { value: 'startTile', label: 'Start tile (logo center, title bottom)' },
                { value: 'imageTop', label: 'imageTop' },
                { value: 'imageBottom', label: 'imageBottom' },
                { value: 'imageLeft', label: 'imageLeft' },
                { value: 'imageRight', label: 'imageRight' },
                { value: 'imageBackground', label: 'imageBackground' }
              ].map(function (opt) {
                var cur = face.mixedLayout || card.mixedLayout || 'centeredStack';
                return '<option value="' + opt.value + '"' + (cur === opt.value ? ' selected' : '') + '>' + esc(opt.label) + '</option>';
              }).join('') + '</select></div>'
            : '') +
          '<p class="ahp-tool-hint" style="margin-top:6px">Centered stack matches the home mosaic. Use Cover with no title for full-bleed photos.</p></div>'
        : '') +
      '<div class="ahp-face-text-row">' +
      '<div class="ahp-field"><label for="ceLabel">' + (normalizeFaceContentType(face, card) === 'image' ? 'Overlay label' : 'Title') + '</label><input id="ceLabel" value="' + esc(labelVal) + '" placeholder="' + (normalizeFaceContentType(face, card) === 'image' ? 'Title on image' : 'Main text') + '"></div>' +
      (normalizeFaceContentType(face, card) === 'image'
        ? '<div class="ahp-field"><label for="ceSubtitle">Overlay sub label</label><input id="ceSubtitle" value="' + esc(subtitleVal) + '" placeholder="Secondary text on image"></div>'
        : '<div class="ahp-field"><label for="ceSubtitle">Subtitle</label><input id="ceSubtitle" value="' + esc(subtitleVal) + '" placeholder="Secondary text"></div>') +
      '</div>' +
      '<div class="ahp-face-row">' +
      colorFieldHtml('ceFaceColor', 'Face background color', faceBgColor) +
      '<div class="ahp-field ahp-check-field"><label for="ceFaceColorAllFaces"><input type="checkbox" id="ceFaceColorAllFaces"' + (card.sameFaceColor === true ? ' checked' : '') + '> Apply this color to all 4 faces</label></div>' +
      '<button type="button" class="ahp-tool-btn" id="ceApplyColorAllFacesBtn" style="margin-bottom:10px"><i class="fas fa-clone"></i> Set all faces to this color now</button>' +
      colorPairRowHtml(
        'ceFaceTextColor', 'Text color', face.textColor || face.titleTextColor || card.textColor || '#ffffff',
        'ceFaceIconColor', 'Icon color', face.iconColor || card.iconColor || '#ffffff'
      ) +
      '<div class="ahp-field ahp-check-field"><label for="ceFaceTextColorAllFaces"><input type="checkbox" id="ceFaceTextColorAllFaces"' + (card.sameFaceTextColor === true ? ' checked' : '') + '> Same text + icon color on all faces</label></div>' +
      iconPickerHtml('ceFaceIcon', 'Icon', face.icon || card.icon || 'fa-cube') +
      '<div class="ahp-field ahp-check-field"><label for="ceFaceShowIcon"><input type="checkbox" id="ceFaceShowIcon"' + (face.hideIcon === true ? '' : ' checked') + '> Show icon on this face</label></div>' +
      rangeFieldHtml('ceFaceIconSize', 'Icon / logo size', iconSizeDefault, 6, 48, 'px') +
      rangeFieldHtml('ceFaceTitleSize', 'Title font size', titleSizeDefault, 5, 36, 'px') +
      rangeFieldHtml('ceFaceTitleWeight', 'Title boldness', titleWeightDefault, 300, 900, '') +
      fontFamilySelectHtml('ceFaceTitleFont', 'Title font family', face.titleFontFamily || card.titleFontFamily || 'system') +
      rangeFieldHtml('ceFaceSubSize', 'Subtitle font size', subSizeDefault, 6, 32, 'px') +
      rangeFieldHtml('ceFaceSubWeight', 'Subtitle boldness', subWeightDefault, 300, 900, '') +
      fontFamilySelectHtml('ceFaceSubFont', 'Subtitle font family', face.subFontFamily || card.subFontFamily || 'system') +
      '</div></div>';

    panel.innerHTML = html;
    if (type === 'image') applyEditorImagePreviewStyles();
    syncCardTypographyDom(card, panel);
    if (selectedCardId) {
      var liveSlot = el.grid && el.grid.querySelector('[data-card-id="' + selectedCardId + '"]');
      if (liveSlot) syncCardTypographyDom(card, liveSlot);
    }

    var delBtn = document.getElementById('ahpDeleteCardBtn');
    if (delBtn) delBtn.addEventListener('click', function () { deleteCard(selectedCardId); });
    var dupBtn = document.getElementById('ahpDuplicateCardBtn');
    if (dupBtn) dupBtn.addEventListener('click', function () { duplicateCard(selectedCardId); });
    var resetFaceBtn = document.getElementById('ahpResetFaceBtn');
    if (resetFaceBtn) resetFaceBtn.addEventListener('click', resetSelectedFace);

    bindFacePreviewClicks(panel);
    initFacePreviewSortable(panel);
    bindIconPicker(panel);
    bindFaceImageUpload(panel);

    var uploadBtn = document.getElementById('ceFaceImageUploadBtn');
    if (uploadBtn) {
      uploadBtn.addEventListener('click', function () {
        var fileInput = document.getElementById('ceFaceImageUpload');
        if (fileInput) fileInput.click();
      });
    }

    var inputIds = ['ceLabel', 'ceSubtitle', 'ceFaceRoute', 'cePageRoute', 'ceFaceImage'];
    inputIds.forEach(function (id) {
      var node = document.getElementById(id);
      if (node) node.addEventListener('input', function () {
        if (id === 'ceFaceImage') {
          var preview = document.getElementById('ceImagePreview');
          var previewImg = document.getElementById('ceImagePreviewImg');
          var url = resolveMediaUrl(node.value.trim());
          if (preview) preview.classList.toggle('has-image', !!url);
          if (previewImg) {
            if (url) {
              previewImg.hidden = false;
              previewImg.src = url;
            } else {
              previewImg.hidden = true;
              previewImg.removeAttribute('src');
            }
          }
          applyEditorImagePreviewStyles();
        }
        applyCardEditor();
      });
    });
    ['ceFaceImageFit', 'ceFaceImagePosition'].forEach(function (id) {
      var node = document.getElementById(id);
      if (!node) return;
      node.addEventListener('change', function () {
        applyEditorImagePreviewStyles();
        applyCardEditor();
      });
    });
    var rangeIds = ['ceCardWidth', 'ceCardHeight', 'ceCardRadius', 'ceFaceSec', 'ceStartDelay', 'ceFaceIconSize', 'ceFaceTitleSize', 'ceFaceTitleWeight', 'ceFaceSubSize', 'ceFaceSubWeight', 'ceFaceImageScale'];
    rangeIds.forEach(function (id) {
      var node = document.getElementById(id);
      if (!node) return;
      syncRangeThumb(node);
      var updateLabel = function () {
        var unit = (id === 'ceCardWidth' || id === 'ceCardHeight' || id === 'ceFaceTitleWeight' || id === 'ceFaceSubWeight')
          ? ''
          : (id === 'ceFaceImageScale' ? '%' : (id === 'ceFaceSec' || id === 'ceStartDelay' ? ' sec' : 'px'));
        updateRangeUi(node, unit);
        if (id === 'ceCardWidth' || id === 'ceCardHeight') {
          var orient = document.getElementById('ceOrientation');
          if (orient) orient.value = 'custom';
        }
        if (id === 'ceCardRadius') {
          var liveBox = document.querySelector('#ceCardRadiusLivePreview .ahp-radius-live-box');
          if (liveBox) liveBox.style.borderRadius = node.value + 'px';
        }
        if (id === 'ceFaceSec') {
          var globalFace = document.getElementById('ceFaceSecGlobal');
          if (globalFace && globalFace.checked) globalFace.checked = false;
          syncCardFaceSecUi();
        }
        if (id === 'ceFaceImageScale') applyEditorImagePreviewStyles();
        applyCardEditor();
      };
      node.addEventListener('input', updateLabel);
      node.addEventListener('change', updateLabel);
    });
    ['ceFaceTitleFont', 'ceFaceSubFont', 'ceOrientation', 'ceFaceShowIcon', 'ceFaceSecGlobal', 'ceMixedLayout'].forEach(function (id) {
      var node = document.getElementById(id);
      if (node) node.addEventListener('change', function () {
        if (id === 'ceFaceSecGlobal') syncCardFaceSecUi();
        applyCardEditor();
        if (id === 'ceOrientation' && node.value !== 'custom') syncCardEditor(true);
        if (id === 'ceMixedLayout') syncCardEditor(true);
      });
    });
    syncCardFaceSecUi();
    bindColorField('ceFaceColor', applyCardEditor);
    bindColorField('ceFaceTextColor', applyCardEditor);
    bindColorField('ceFaceIconColor', applyCardEditor);
    var allFacesColorChk = document.getElementById('ceFaceColorAllFaces');
    if (allFacesColorChk) {
      allFacesColorChk.addEventListener('change', function () {
        var card = layout.cards.find(function (c) { return c.id === selectedCardId; });
        if (!card) return;
        card.sameFaceColor = !!allFacesColorChk.checked;
        if (allFacesColorChk.checked) {
          var color = getBackgroundColorVal('ceFaceColor') || (card.faces[activeFaceIndex] && card.faces[activeFaceIndex].color) || card.color;
          if (color) applyColorToAllFaces(card, color);
          applyCardEditor();
          showToast('All 4 faces use the same background color');
        } else {
          applyCardEditor();
        }
      });
    }
    var applyAllColorBtn = document.getElementById('ceApplyColorAllFacesBtn');
    if (applyAllColorBtn) {
      applyAllColorBtn.addEventListener('click', function () {
        var card = layout.cards.find(function (c) { return c.id === selectedCardId; });
        if (!card) return;
        var color = getBackgroundColorVal('ceFaceColor') || (card.faces[activeFaceIndex] && card.faces[activeFaceIndex].color) || card.color;
        if (!color) {
          showToast('Pick a face background color first');
          return;
        }
        applyColorToAllFaces(card, color);
        var chk = document.getElementById('ceFaceColorAllFaces');
        if (chk) chk.checked = true;
        applyCardEditor();
        showToast('Background color applied to all faces');
      });
    }
    var allTextChk = document.getElementById('ceFaceTextColorAllFaces');
    if (allTextChk) {
      allTextChk.addEventListener('change', function () {
        var card = layout.cards.find(function (c) { return c.id === selectedCardId; });
        if (!card) return;
        card.sameFaceTextColor = !!allTextChk.checked;
        if (allTextChk.checked) {
          applyTextIconColorToAllFaces(
            card,
            getColorVal('ceFaceTextColor') || '#ffffff',
            getColorVal('ceFaceIconColor') || '#ffffff'
          );
          applyCardEditor();
          showToast('Text and icon colors applied to all faces');
        } else {
          applyCardEditor();
        }
      });
    }
    var typeNode = document.getElementById('ceType');
    if (typeNode) typeNode.addEventListener('change', function () {
      var card = layout.cards.find(function (c) { return c.id === selectedCardId; });
      if (card && typeNode.value === 'image') ensureImageBlockDefaults(card);
      applyCardEditor();
      syncCardEditor(true);
    });
    var faceTypeNode = document.getElementById('ceFaceType');
    if (faceTypeNode) faceTypeNode.addEventListener('change', function () {
      applyCardEditor();
      syncCardEditor(true);
    });
    var rotationDirNode = document.getElementById('ceRotationDir');
    if (rotationDirNode) rotationDirNode.addEventListener('change', applyCardEditor);
    var rotationEnabledNode = document.getElementById('ceRotationEnabled');
    if (rotationEnabledNode) rotationEnabledNode.addEventListener('change', applyCardEditor);
  }

  function syncCardFaceSecUi() {
    var globalNode = document.getElementById('ceFaceSecGlobal');
    var faceSecNode = document.getElementById('ceFaceSec');
    if (!faceSecNode) return;
    var useGlobal = globalNode ? globalNode.checked : false;
    faceSecNode.disabled = useGlobal;
    var wrap = faceSecNode.closest('.ahp-range-field');
    if (wrap) wrap.style.opacity = useGlobal ? '0.55' : '1';
    if (useGlobal) {
      faceSecNode.value = faceSecondsFromCardSpeed(getTheme().cardSpeed || 16);
      updateRangeUi(faceSecNode, ' sec');
    }
  }

  function applyLegacyCardEditor(opts) {
    opts = opts || {};
    var card = layout.cards.find(function (c) { return c.id === selectedCardId; });
    if (!card) return;
    if (!document.getElementById('ceLabel')) return;

    var typeNode = document.getElementById('ceType');
    if (typeNode) card.type = normalizeHomeBlockType(typeNode.value);
    var type = normalizeHomeBlockType(card.type);

    var rotationEnabledNode = document.getElementById('ceRotationEnabled');
    if (rotationEnabledNode) card.rotationEnabled = !!rotationEnabledNode.checked;

    var startDelayNode = document.getElementById('ceStartDelay');
    if (startDelayNode) {
      var delaySec = Math.max(0, Math.min(8, parseFloat(startDelayNode.value) || 0));
      if (delaySec > 0) card.startDelayMs = Math.round(delaySec * 1000);
      else delete card.startDelayMs;
      invalidateCardAnimOffset(card.id);
    }

    var orientationNode = document.getElementById('ceOrientation');
    var cardWidthNode = document.getElementById('ceCardWidth');
    var cardHeightNode = document.getElementById('ceCardHeight');
    var prevCol = card.colSpan;
    var prevRow = card.rowSpan;

    if (orientationNode && orientationNode.value && orientationNode.value !== 'custom') {
      applyCardOrientation(card, orientationNode.value);
      if (cardWidthNode) {
        cardWidthNode.value = card.colSpan;
        cardWidthNode.max = String(getGridColumns());
        updateRangeUi(cardWidthNode, '');
      }
      if (cardHeightNode) {
        cardHeightNode.value = card.rowSpan;
        cardHeightNode.max = String(getGridRows());
        updateRangeUi(cardHeightNode, '');
      }
    } else {
      if (cardWidthNode) card.colSpan = Math.max(1, Math.min(getGridColumns(), parseInt(cardWidthNode.value, 10) || 1));
      if (cardHeightNode) card.rowSpan = Math.max(1, Math.min(getGridRows(), parseInt(cardHeightNode.value, 10) || 1));
      card.orientation = 'custom';
      if (orientationNode) orientationNode.value = 'custom';
    }
    ensureCardSize(card);
    var sizeDirty = card.colSpan !== prevCol || card.rowSpan !== prevRow;

    var cardRadiusNode = document.getElementById('ceCardRadius');
    if (cardRadiusNode) card.borderRadius = parseInt(cardRadiusNode.value, 10);

    var rotationDirNode = document.getElementById('ceRotationDir');
    if (rotationDirNode) card.rotationDir = rotationDirNode.value === 'ltr' ? 'ltr' : 'rtl';

    var pageRouteNode = document.getElementById('cePageRoute');
    if (pageRouteNode) card.pageRoute = pageRouteNode.value.trim();

    var globalFaceSecNode = document.getElementById('ceFaceSecGlobal');
    var faceSecNode = document.getElementById('ceFaceSec');
    var prevFaceSec = card.faceSec;
    if (globalFaceSecNode && globalFaceSecNode.checked) {
      delete card.faceSec;
    } else if (faceSecNode) {
      card.faceSec = Math.max(2, Math.min(8, parseFloat(faceSecNode.value) || getCardFaceSec(card)));
    }
    if (prevFaceSec !== card.faceSec) {
      invalidateCardAnimOffset(card.id);
    }

    ensureFaces(card);
    var targetFaceIndex = opts.faceIndex != null
      ? Math.max(0, Math.min(3, parseInt(opts.faceIndex, 10) || 0))
      : activeFaceIndex;
    var face = card.faces[targetFaceIndex];

    var faceTypeNode = document.getElementById('ceFaceType');
    if (face && faceTypeNode) face.type = normalizeHomeBlockType(faceTypeNode.value);
    var faceType = face ? normalizeFaceContentType(face, card) : type;

    var labelVal = getVal('ceLabel');
    var subVal = getVal('ceSubtitle');

    if (face) {
      writeFaceEditorText(face, faceType === 'image' ? 'image' : (faceType === 'mixed' ? 'link' : (face.value != null ? 'stat' : 'link')), labelVal, subVal);
      face.icon = normalizeFaIcon(getVal('ceFaceIcon') || card.icon || 'fa-cube');
      // Do not overwrite other faces' icons onto the card when editing face 2–4
      if (targetFaceIndex === 0) card.icon = face.icon;
      var showIconNode = document.getElementById('ceFaceShowIcon');
      face.hideIcon = showIconNode ? !showIconNode.checked : false;

      if (document.getElementById('ceFaceColor')) {
        var nextFaceColor = getBackgroundColorVal('ceFaceColor');
        var applyAllFacesColor = !!(document.getElementById('ceFaceColorAllFaces') && document.getElementById('ceFaceColorAllFaces').checked);
        card.sameFaceColor = applyAllFacesColor;
        if (nextFaceColor) {
          if (applyAllFacesColor) {
            applyColorToAllFaces(card, nextFaceColor);
          } else {
            face.color = nextFaceColor;
            if (targetFaceIndex === 0) card.color = nextFaceColor;
          }
        }
      }

      var faceTextVal = getColorVal('ceFaceTextColor');
      var faceIconVal = getColorVal('ceFaceIconColor');
      var applyAllTextIcon = !!(document.getElementById('ceFaceTextColorAllFaces') && document.getElementById('ceFaceTextColorAllFaces').checked);
      card.sameFaceTextColor = applyAllTextIcon;
      if (applyAllTextIcon && (faceTextVal || faceIconVal)) {
        applyTextIconColorToAllFaces(card, faceTextVal || face.textColor || '#ffffff', faceIconVal || face.iconColor || '#ffffff');
      } else {
        if (faceTextVal) {
          face.textColor = faceTextVal;
          face.titleTextColor = faceTextVal;
          face.subTextColor = faceTextVal;
          if (targetFaceIndex === 0) card.textColor = faceTextVal;
        }
        if (faceIconVal) {
          face.iconColor = faceIconVal;
          if (targetFaceIndex === 0) card.iconColor = faceIconVal;
        }
      }

      var iconSizeNode = document.getElementById('ceFaceIconSize');
      if (iconSizeNode) {
        var iconSizeVal = parseInt(iconSizeNode.value, 10);
        if (!isNaN(iconSizeVal)) {
          face.iconSize = iconSizeVal;
          if (targetFaceIndex === 0) card.iconSize = iconSizeVal;
        }
      }

      var titleSizeNode = document.getElementById('ceFaceTitleSize');
      if (titleSizeNode) {
        var titleSizeVal = parseInt(titleSizeNode.value, 10);
        if (!isNaN(titleSizeVal)) {
          face.titleFontSize = titleSizeVal;
          if (targetFaceIndex === 0) card.titleFontSize = titleSizeVal;
        }
      }

      var titleWeightNode = document.getElementById('ceFaceTitleWeight');
      if (titleWeightNode) {
        var titleWeightVal = clampFontWeight(titleWeightNode.value, 800);
        face.titleFontWeight = titleWeightVal;
        if (targetFaceIndex === 0) card.titleFontWeight = titleWeightVal;
      }

      var subSizeNode = document.getElementById('ceFaceSubSize');
      if (subSizeNode) {
        var subSizeVal = parseInt(subSizeNode.value, 10);
        if (!isNaN(subSizeVal)) {
          face.subFontSize = subSizeVal;
          if (targetFaceIndex === 0) card.subFontSize = subSizeVal;
        }
      }

      var subWeightNode = document.getElementById('ceFaceSubWeight');
      if (subWeightNode) {
        var subWeightVal = clampFontWeight(subWeightNode.value, 500);
        face.subFontWeight = subWeightVal;
        face.subtitleFontWeight = subWeightVal;
        if (targetFaceIndex === 0) {
          card.subFontWeight = subWeightVal;
          card.subtitleFontWeight = subWeightVal;
        }
      }

      var titleFontNode = document.getElementById('ceFaceTitleFont');
      if (titleFontNode) {
        face.titleFontFamily = titleFontNode.value;
        if (targetFaceIndex === 0) card.titleFontFamily = titleFontNode.value;
      }

      var subFontNode = document.getElementById('ceFaceSubFont');
      if (subFontNode) {
        face.subFontFamily = subFontNode.value;
        if (targetFaceIndex === 0) card.subFontFamily = subFontNode.value;
      }

      var faceRouteNode = document.getElementById('ceFaceRoute');
      if (faceRouteNode) {
        var routeVal = faceRouteNode.value.trim();
        if (routeVal) face.route = routeVal;
        else delete face.route;
      }

      var faceImageNode = document.getElementById('ceFaceImage');
      if (faceImageNode && (faceType === 'image' || faceType === 'mixed')) {
        var imageVal = faceImageNode.value.trim();
        if (imageVal) face.image = imageVal;
        else delete face.image;
        if (targetFaceIndex === 0) card.image = imageVal;

        var fitNode = document.getElementById('ceFaceImageFit');
        if (fitNode) {
          face.imageFit = normalizeImageFit(fitNode.value);
          card._imageFitUserSet = true;
        }
        var posNode = document.getElementById('ceFaceImagePosition');
        if (posNode) face.imagePosition = normalizeImagePosition(posNode.value);
        var mixedLayoutNode = document.getElementById('ceMixedLayout');
        if (mixedLayoutNode && faceType === 'mixed') {
          face.mixedLayout = mixedLayoutNode.value || 'centeredStack';
          card.mixedLayout = face.mixedLayout;
        }
        var scaleNode = document.getElementById('ceFaceImageScale');
        if (scaleNode) face.imageScale = normalizeImageScale(scaleNode.value);
        if (activeFaceIndex === 0 || targetFaceIndex === 0) {
          card.imageFit = face.imageFit;
          card.imagePosition = face.imagePosition;
          card.imageScale = face.imageScale;
        }
      }
    }

    if (targetFaceIndex === 0) {
      syncCardTextFromFace0(card, type, labelVal, subVal);
    }

    if (sizeDirty) {
      renderGrid();
    } else {
      updateCardSlot(selectedCardId);
    }
    updateCardEditorPreview(card);
    syncCardFaceSecUi();
    if (!opts.skipColorFieldResync) syncCardEditorColorFields(card);
    updateCardSelectOption(card);
    updateSelectedCardHighlight();
    refreshCubePlayback(el.grid);
    syncCubeRotation(el.grid);
    if (!opts.skipDirty) markLayoutDirty();
  }

  function deleteCard(cardId) {
    if (!cardId) return;
    if (!confirm('Remove this card from the home page?')) return;
    delete cardAnimOffsets[cardId];
    layout.cards = layout.cards.filter(function (c) { return c.id !== cardId; });
    if (selectedCardId === cardId) {
      selectedCardId = null;
      cardEditorCardId = null;
    }
    if (selectedPageCardId === cardId) selectedPageCardId = null;
    assignGridAreas();
    renderGrid();
    syncCardEditor(true);
    markLayoutDirty();
    showToast('Card removed');
  }

  function clearAllCards() {
    if (!layout.cards || !layout.cards.length) {
      showToast('No cards to remove');
      return;
    }
    if (!confirm('Remove all cards from the home page? This cannot be undone until you save.')) return;
    layout.cards = [];
    selectedCardId = null;
    cardEditorCardId = null;
    selectedPageCardId = null;
    cardAnimOffsets = {};
    assignGridAreas();
    renderGrid();
    syncCardEditor(true);
    markLayoutDirty();
    showToast('All cards removed - add new ones from Blocks');
  }

  function duplicateCard(cardId) {
    if (!cardId) return;
    if (!layout.cards) layout.cards = [];
    var source = layout.cards.find(function (c) { return c.id === cardId; });
    if (!source) return;
    var copy = JSON.parse(JSON.stringify(source));
    copy.id = 'card-' + Date.now();
    copy.label = (copy.label || copy.title || 'Card') + ' Copy';
    copy.title = copy.label;
    delete copy.col;
    delete copy.row;
    layout.cards.push(copy);
    placeCardInFirstFreeSlot(copy);
    assignGridAreas();
    renderGrid();
    selectCard(copy.id);
    markLayoutDirty({ historyNow: true });
    showToast('Card duplicated');
  }

  function resetSelectedFace() {
    var card = layout.cards.find(function (c) { return c.id === selectedCardId; });
    if (!card) return;
    ensureFaces(card);
    var face = card.faces[activeFaceIndex];
    if (!face) return;
    var label = card.label || card.title || ('Face ' + (activeFaceIndex + 1));
    if (card.type === 'stat') {
      card.faces[activeFaceIndex] = {
        value: card.value || '0',
        label: label,
        icon: card.icon || 'fa-chart-bar'
      };
    } else if (card.type === 'image') {
      card.faces[activeFaceIndex] = {
        icon: card.icon || 'fa-cube',
        image: activeFaceIndex === 0 ? (card.image || '') : (face.image || '')
      };
    } else {
      card.faces[activeFaceIndex] = {
        title: label,
        subtitle: card.subtitle || '',
        icon: card.icon || 'fa-cube',
        image: card.type === 'image' ? (card.image || '') : ''
      };
    }
    updateCardSlot(selectedCardId);
    syncCardEditor(true);
    updateSelectedCardHighlight();
    markLayoutDirty();
    showToast('Selected face reset');
  }

  var navEditorBound = false;

  function getNavEditItem() {
    if (!layout.bottomNav || !layout.bottomNav.length) return null;
    var item = layout.bottomNav.find(function (n) { return n.id === selectedNavEditId; });
    if (!item) {
      item = layout.bottomNav[0];
      selectedNavEditId = item.id;
    }
    return item;
  }

  function syncNavEditor() {
    var list = document.getElementById('ahpNavEditor');
    var activeSelect = document.getElementById('toolNavActive');
    var editSelect = document.getElementById('toolNavEditTab');
    if (!layout.bottomNav) return;

    layout.bottomNav.forEach(function (item) {
      item.icon = normalizeFaIcon(item.icon);
    });

    if (!selectedNavEditId || !layout.bottomNav.some(function (n) { return n.id === selectedNavEditId; })) {
      selectedNavEditId = layout.bottomNav[0] ? layout.bottomNav[0].id : null;
    }

    if (activeSelect) {
      activeSelect.innerHTML = layout.bottomNav.map(function (item) {
        return '<option value="' + esc(item.id) + '"' + (item.active ? ' selected' : '') + '>' + esc(item.label) + '</option>';
      }).join('');
    }

    if (editSelect) {
      editSelect.innerHTML = layout.bottomNav.map(function (item) {
        return '<option value="' + esc(item.id) + '"' + (item.id === selectedNavEditId ? ' selected' : '') + '>' + esc(item.label) + '</option>';
      }).join('');
    }

    if (!list) return;
    var item = getNavEditItem();
    if (!item) {
      list.innerHTML = '<p class="ahp-tool-hint">No navigation tabs.</p>';
      return;
    }

    var icon = normalizeFaIcon(item.icon);
    var quick = QUICK_ICONS.map(function (ic) {
      return '<button type="button" class="ahp-icon-option' + (ic === icon ? ' selected' : '') + '" data-nav-icon="' + ic + '" title="' + ic.replace('fa-', '') + '">' +
        '<i class="fas ' + ic + '"></i></button>';
    }).join('');

    list.innerHTML =
      '<div class="ahp-nav-simple">' +
      '<div class="ahp-field">' +
      '<label for="toolNavLabel">Tab label</label>' +
      '<input type="text" id="toolNavLabel" value="' + esc(item.label || '') + '">' +
      '</div>' +
      '<div class="ahp-field ahp-icon-picker-field" data-icon-input="navIcon">' +
      '<label>Tab icon</label>' +
      '<div class="ahp-icon-selected-row">' +
      '<span class="ahp-icon-current" id="navIconCurrent"><i class="fas ' + esc(icon) + '"></i></span>' +
      '<div class="ahp-icon-selected-meta">' +
      '<strong id="navIconName">' + esc(icon.replace('fa-', '')) + '</strong>' +
      '<small>Pick an icon below</small></div>' +
      '<button type="button" class="ahp-tool-btn ahp-icon-more-btn" data-icon-target="navIcon"><i class="fas fa-th"></i> More</button>' +
      '</div>' +
      '<div class="ahp-icon-quick ahp-nav-icon-quick">' + quick + '</div>' +
      '<input type="hidden" id="navIcon" value="' + esc(icon) + '">' +
      '</div></div>';

    bindIconPicker(list);

    list.querySelectorAll('[data-nav-icon]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var ic = btn.getAttribute('data-nav-icon');
        setIconValue('navIcon', ic);
        applyNavIcon(ic);
      });
    });

    if (!navEditorBound) {
      navEditorBound = true;
      if (editSelect) {
        editSelect.addEventListener('change', function () {
          selectedNavEditId = editSelect.value;
          syncNavEditor();
        });
      }
      list.addEventListener('input', function (e) {
        if (e.target && e.target.id === 'toolNavLabel') {
          var navItem = getNavEditItem();
          if (!navItem) return;
          navItem.label = e.target.value;
          renderBottomNav();
          syncNavSelectLabels();
        }
      });
    }
  }

  function syncNavSelectLabels() {
    var activeSelect = document.getElementById('toolNavActive');
    var editSelect = document.getElementById('toolNavEditTab');
    (layout.bottomNav || []).forEach(function (item) {
      if (activeSelect) {
        var a = activeSelect.querySelector('option[value="' + item.id + '"]');
        if (a) a.textContent = item.label;
      }
      if (editSelect) {
        var e = editSelect.querySelector('option[value="' + item.id + '"]');
        if (e) e.textContent = item.label;
      }
    });
  }

  function applyNavIcon(icon) {
    var navItem = getNavEditItem();
    if (!navItem) return;
    navItem.icon = normalizeFaIcon(icon);
    renderBottomNav();
    var list = document.getElementById('ahpNavEditor');
    if (list) {
      list.querySelectorAll('[data-nav-icon]').forEach(function (btn) {
        btn.classList.toggle('selected', btn.getAttribute('data-nav-icon') === navItem.icon);
      });
    }
    markLayoutDirty();
  }

  function setNavActive(navId, skipRenderPage) {
    layout.bottomNav.forEach(function (item) {
      item.active = item.id === navId;
    });
    layout.activePage = navId;
    renderBottomNav();
    if (!skipRenderPage) renderActivePage();
    var activeSelect = document.getElementById('toolNavActive');
    if (activeSelect) activeSelect.value = navId;
  }

  function applyBgPreset(style) {
    var preset = BG_PRESETS[style];
    if (!preset) return;
    var t = getTheme();
    t.bgStyle = style;
    Object.keys(preset).forEach(function (key) {
      t[key] = preset[key];
    });
    setBackgroundColorField('toolAccentColor', t.accentColor);
    setBackgroundColorField('toolAccentGreen', t.accentGreen);
    setBackgroundColorField('toolSurfaceColor', t.surfaceColor);
    setBackgroundColorField('toolWelcomeStart', t.welcomeStart);
    setBackgroundColorField('toolWelcomeEnd', t.welcomeEnd);
    setBackgroundColorField('toolPhoneBg', t.phoneBg);
    setBackgroundColorField('toolAlertBg', t.alertBg);
    setBackgroundColorField('toolNavActiveColor', t.navActiveColor);
    setBackgroundColorField('toolTextPrimary', t.textPrimary);
    setBackgroundColorField('toolTextMuted', t.textMuted);
    setBackgroundColorField('toolNavBg', t.navBg);
    applyTheme();
    renderThemePresets();
  }

  function getActiveHomeTemplateId() {
    var t = getTheme();
    return (t && t.homeTemplate) || layout.homeTemplate || 'default';
  }

  function cloneTemplateValue(value) {
    return JSON.parse(JSON.stringify(value == null ? null : value));
  }

  function ensureHomeTemplateDrafts() {
    if (!layout.homeTemplateDrafts || typeof layout.homeTemplateDrafts !== 'object' || Array.isArray(layout.homeTemplateDrafts)) {
      layout.homeTemplateDrafts = {};
    }
    return layout.homeTemplateDrafts;
  }

  function captureHomeTemplateDraft(templateId) {
    var id = templateId || getActiveHomeTemplateId() || 'default';
    if (!id || getActiveStaticUiId()) return;
    var store = ensureHomeTemplateDrafts();
    var theme = cloneTemplateValue(getTheme()) || {};
    delete theme.staticUiTemplate;
    store[id] = {
      header: cloneTemplateValue(layout.header || {}),
      welcome: cloneTemplateValue(layout.welcome || {}),
      alerts: cloneTemplateValue(layout.alerts || {}),
      theme: theme,
      cards: cloneTemplateValue(layout.cards || []),
      bottomNavCategoryTarget: layout.bottomNavCategoryTarget || 'grid'
    };
  }

  function homeTemplateDraft(templateId) {
    var store = layout.homeTemplateDrafts;
    if (!store || typeof store !== 'object') return null;
    var draft = store[templateId];
    if (!draft || !Array.isArray(draft.cards) || !draft.cards.length) return null;
    return draft;
  }

  function finishHomeTemplateSwitch(templateId, resumed) {
    layout.homeTemplate = templateId;
    layout.staticUiTemplate = null;
    if (!layout.theme) layout.theme = {};
    layout.theme.homeTemplate = templateId;
    layout.theme.staticUiTemplate = null;
    layout.theme.showBottomNav = true;
    layout.bottomNavCategoryTarget = 'grid';
    layout.activePage = 'home';
    normalizeBottomNav();
    layout.bottomNav.forEach(function (item) {
      if (item) item.active = item.id === 'home';
    });
    selectedCardId = null;
    cardEditorCardId = null;
    selectedPageCardId = null;
    activeFaceIndex = 0;
    ensureLayoutStructure();
    assignGridAreas();
    ensureAllCardPositions(false);
    renderAll();
    renderHomeTemplates();
    if (typeof renderStaticUiTemplates === 'function') renderStaticUiTemplates();
    markLayoutDirty({ historyNow: true });
    showToast(resumed
      ? 'Restored where you left off — click Save to publish'
      : 'Template applied — click Save to publish');
  }

  function applyHomeTemplate(templateId, options) {
    options = options || {};
    var tpl = HOME_TEMPLATES[templateId];
    if (!tpl) {
      showToast('Template not found');
      return false;
    }
    var currentId = getActiveHomeTemplateId();
    var switching = currentId !== templateId || !!getActiveStaticUiId();
    if (!switching && !options.force) {
      showToast(tpl.label + ' is already at your latest edits');
      return true;
    }
    if (!options.skipConfirm && layout.cards && layout.cards.length && switching) {
      if (!confirm('Switch to "' + tpl.label + '"? This layout keeps the work you already did on it.')) {
        return false;
      }
    }

    if (!getActiveStaticUiId() && currentId && currentId !== templateId) {
      captureHomeTemplateDraft(currentId);
    }

    var draft = options.useFactory ? null : homeTemplateDraft(templateId);
    if (draft) {
      var keptEmail = (layout.welcome && layout.welcome.email) || cfg.userEmail || '';
      layout.header = cloneTemplateValue(draft.header || {});
      layout.welcome = Object.assign({}, cloneTemplateValue(draft.welcome || {}), {
        email: (draft.welcome && draft.welcome.email) || keptEmail
      });
      layout.alerts = cloneTemplateValue(draft.alerts || {});
      layout.theme = Object.assign({}, getTheme(), draft.theme || {}, {
        homeTemplate: templateId,
        staticUiTemplate: null,
        showBottomNav: true
      });
      layout.cards = cloneTemplateValue(draft.cards);
      finishHomeTemplateSwitch(templateId, true);
      return true;
    }

    layout.header = Object.assign({}, layout.header || {}, tpl.header || {});
    layout.welcome = Object.assign({}, layout.welcome || {}, tpl.welcome || {}, {
      email: (layout.welcome && layout.welcome.email) || cfg.userEmail || ''
    });
    layout.alerts = Object.assign({}, layout.alerts || {}, tpl.alerts || {});
    layout.theme = Object.assign({}, getTheme(), tpl.theme || {}, {
      homeTemplate: tpl.id,
      staticUiTemplate: null,
      showBottomNav: true
    });
    layout.cards = (tpl.cards || []).map(function (spec) {
      return buildTemplateCard(spec);
    });
    cardAnimOffsets = {};
    finishHomeTemplateSwitch(tpl.id, false);
    return true;
  }

  function renderHomeTemplates() {
    var wrap = document.getElementById('ahpHomeTemplates');
    if (!wrap) return;
    var current = getActiveHomeTemplateId();
    wrap.innerHTML = Object.keys(HOME_TEMPLATES).map(function (key) {
      var tpl = HOME_TEMPLATES[key];
      var swatches = (tpl.swatches || []).map(function (c) {
        return '<span style="background:' + esc(c || '#334155') + '"></span>';
      }).join('');
      return '<button type="button" class="ahp-home-template' + (key === current ? ' active' : '') + '" data-template="' + esc(key) + '">' +
        '<div class="ahp-home-template-swatches">' + swatches + '</div>' +
        '<strong>' + esc(tpl.label) + '</strong>' +
        '<small>' + esc(tpl.note) + '</small></button>';
    }).join('');
    wrap.querySelectorAll('.ahp-home-template').forEach(function (btn) {
      btn.addEventListener('click', function () {
        applyHomeTemplate(btn.getAttribute('data-template'));
      });
    });
  }

  function setActiveMenu(key) {
    activeMenuKey = key || 'layout';
    el.menuItems.forEach(function (m) {
      m.classList.toggle('active', m.getAttribute('data-menu') === key);
    });
    el.toolSections.forEach(function (s) {
      s.classList.toggle('active', s.getAttribute('data-tool') === key);
    });
    if (key === 'layout') {
      if (layout.activePage !== 'home') switchPage('home', true);
    }
    if (key === 'navigation') syncNavEditor();
    if (key === 'profile') {
      if (layout.activePage !== 'profile' && layout.activePage !== 'settings') {
        switchPage('profile', true);
      }
      refreshLoginUserProfileFromApi(function () {
        syncProfileTools();
      });
    }
    if (key === 'theme') renderThemePresets();
    if (key === 'templates') {
      if (layout.activePage !== 'home') switchPage('home', true);
      renderHomeTemplates();
    }
    if (key === 'ui-templates') {
      if (layout.activePage !== 'home') switchPage('home', true);
      renderStaticUiTemplates();
    }
    if (key === 'schema') refreshSchemaPreview();
    refreshCubePlayback(el.phone);
  }

  function bindDeviceSlotActions() {
    var grid = document.getElementById('ahpDevicesGrid');
    if (!grid || deviceSlotActionsBound) return;
    deviceSlotActionsBound = true;
    grid.addEventListener('click', function (e) {
      var powerBtn = e.target.closest('[data-dev-power]');
      if (powerBtn) {
        e.preventDefault();
        e.stopPropagation();
        var page = getDevicesPage();
        var powerId = powerBtn.getAttribute('data-dev-power');
        var powerDevice = (page.devices || []).find(function (d) { return d.id === powerId; });
        if (powerDevice) {
          powerDevice.online = powerDevice.online === false;
          renderDevicesPage({ skipEditor: true });
          showToast((powerDevice.name || 'Device') + (powerDevice.online === false ? ' turned off' : ' turned on'));
        }
        return;
      }
      if (!editMode) {
        var cube = e.target.closest('[data-device-id]');
        if (cube) selectDevice(cube.getAttribute('data-device-id'));
        return;
      }
      var btn = e.target.closest('.ahp-slot-btn[data-action]');
      if (btn && !btn.classList.contains('ahp-drag-handle')) {
        e.preventDefault();
        e.stopPropagation();
        var slot = btn.closest('[data-device-id]');
        if (!slot) return;
        var deviceId = slot.getAttribute('data-device-id');
        var action = btn.getAttribute('data-action');
        if (action === 'edit') {
          selectDevice(deviceId);
        } else if (action === 'default-face') {
          cycleDeviceDefaultFace(deviceId);
        }
        return;
      }
      if (!e.target.closest('.ahp-slot-btn')) {
        var pickSlot = e.target.closest('[data-device-id]');
        if (pickSlot) selectDevice(pickSlot.getAttribute('data-device-id'));
      }
    });
  }

  function initDeviceSortable() {
    var devGrid = document.getElementById('ahpDevicesGrid');
    if (typeof Sortable === 'undefined' || !devGrid) return;
    destroyDeviceSortable();
    function clearDragSizing() {
      devGrid.classList.remove('ahp-is-dragging');
      devGrid.querySelectorAll('.ahp-dev-cube').forEach(function (slot) {
        slot.style.width = '';
        slot.style.height = '';
        slot.style.transform = '';
      });
      document.body.classList.remove('ahp-dragging-card');
    }
    deviceSortable = Sortable.create(devGrid, {
      animation: 160,
      handle: '.ahp-drag-handle',
      draggable: '.ahp-dev-cube',
      filter: '.ahp-slot-btn:not(.ahp-drag-handle)',
      preventOnFilter: true,
      forceFallback: true,
      fallbackOnBody: true,
      fallbackTolerance: 6,
      fallbackClass: 'ahp-sortable-fallback',
      ghostClass: 'sortable-ghost',
      chosenClass: 'sortable-chosen',
      dragClass: 'sortable-drag',
      swapThreshold: 0.65,
      scroll: true,
      bubbleScroll: true,
      scrollSensitivity: 40,
      scrollSpeed: 12,
      onStart: function (evt) {
        commitLayoutHistoryBeforeChange();
        document.body.classList.add('ahp-dragging-card');
        devGrid.classList.add('ahp-is-dragging');
        if (!evt || !evt.item) return;
        var rect = evt.item.getBoundingClientRect();
        evt.item.style.width = rect.width + 'px';
        evt.item.style.height = rect.height + 'px';
      },
      onEnd: function () {
        var page = getDevicesPage();
        var devices = page.devices || [];
        var newOrder = [];
        devGrid.querySelectorAll('.ahp-dev-cube').forEach(function (slot) {
          var id = slot.getAttribute('data-device-id');
          var device = devices.find(function (d) { return d.id === id; });
          if (device) newOrder.push(device);
        });
        clearDragSizing();
        if (newOrder.length !== devices.length) {
          renderDevicesPage({ skipEditor: true });
          return;
        }
        var orderChanged = newOrder.some(function (device, i) {
          return devices[i] !== device;
        });
        if (!orderChanged) return;
        page.devices = newOrder;
        renderDevicesPage({ skipEditor: true });
        markLayoutDirty({ historyNow: true });
        showToast('Device order updated');
      },
      onCancel: clearDragSizing
    });
  }

  function destroyDeviceSortable() {
    if (deviceSortable) { deviceSortable.destroy(); deviceSortable = null; }
  }

  function bindGridAddPlaceholder() {
    if (!el.grid) return;
    el.grid.querySelectorAll('[data-add-placeholder]').forEach(function (slot) {
      if (slot.dataset.ahpAddBound === '1') return;
      slot.dataset.ahpAddBound = '1';
      var trigger = slot.querySelector('.ahp-grid-add-trigger');
      var picker = slot.querySelector('.ahp-grid-add-picker');
      if (trigger && picker) {
        trigger.addEventListener('click', function (e) {
          e.stopPropagation();
          var open = !picker.hidden;
          picker.hidden = open;
          trigger.setAttribute('aria-expanded', open ? 'false' : 'true');
          slot.classList.toggle('is-open', !open);
        });
      }
      slot.querySelectorAll('.ahp-grid-add-option[data-add-type]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.stopPropagation();
          addBlock(btn.getAttribute('data-add-type'));
        });
      });
    });
  }

  function bindLegacySlotActions() {
    if (!el.grid || slotActionsBound) return;
    slotActionsBound = true;
    el.grid.addEventListener('click', function (e) {
      if (!editMode) {
        var face = e.target.closest('.ahp-cube-face[data-face-index]');
        var openSlot = e.target.closest('.ahp-card');
        if (openSlot) {
          e.preventDefault();
          var faceIndex = face ? parseInt(face.getAttribute('data-face-index'), 10) : currentAnimatedFaceIndex(openSlot);
          openCardFacePage(openSlot.getAttribute('data-card-id'), isNaN(faceIndex) ? currentAnimatedFaceIndex(openSlot) : faceIndex);
        }
        return;
      }
      var btn = e.target.closest('.ahp-slot-btn[data-action]');
      if (btn && !btn.classList.contains('ahp-drag-handle')) {
        e.preventDefault();
        e.stopPropagation();
        var slot = btn.closest('.ahp-card');
        if (!slot) return;
        var cardId = slot.getAttribute('data-card-id');
        var action = btn.getAttribute('data-action');
        if (action === 'edit') {
          selectCard(cardId);
        } else if (action === 'delete') {
          deleteCard(cardId);
        }
        return;
      }
      var facePick = e.target.closest('.ahp-edit-face-card[data-face-index]');
      if (facePick) {
        e.preventDefault();
        e.stopPropagation();
        activeFaceIndex = Math.max(0, Math.min(3, parseInt(facePick.getAttribute('data-face-index'), 10) || 0));
        var faceSlot = facePick.closest('.ahp-card');
        if (faceSlot) selectedCardId = faceSlot.getAttribute('data-card-id');
        selectActiveFace(activeFaceIndex, { rebuildEditor: true });
        return;
      }
      if (!e.target.closest('.ahp-slot-btn')) {
        var pickSlot = e.target.closest('.ahp-card');
        if (pickSlot) selectCard(pickSlot.getAttribute('data-card-id'));
      }
    });
  }

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
    if (block === 'image') return (face.image || card.image) ? 'image' : 'stack';
    return 'stack';
  }

  function addBlock(type) {
    if (!layout.cards) layout.cards = [];
    var id = 'card-' + Date.now();
    type = normalizeHomeBlockType(type);
    var typeColors = {
      stack: '#2563eb',
      image: '#0f172a',
      mixed: '#7c3aed'
    };
    var typeIcons = { stack: 'fa-layer-group', image: 'fa-image', mixed: 'fa-th-large' };
    var color = typeColors[type] || '#2563eb';
    var icon = typeIcons[type] || 'fa-cube';
    var block = {
      id: id,
      type: type,
      color: color,
      icon: icon,
      textColor: '#ffffff',
      iconColor: '#ffffff',
      title: type === 'stack' ? 'New Stack' : type === 'mixed' ? 'New Mixed' : '',
      label: type === 'stack' ? 'New Stack' : type === 'mixed' ? 'New Mixed' : '',
      value: type === 'stack' ? '0' : '',
      subtitle: 'Tap to configure',
      defaultFace: 0,
      borderRadius: defaultCardRadius(),
      rotationDir: 'rtl',
      rotationEnabled: true,
      colSpan: 1,
      rowSpan: 1,
      orientation: 'square',
      tall: false,
      faces: [
        { type: type, title: type === 'image' ? '' : (type === 'mixed' ? 'New Mixed' : 'New Stack'), icon: icon, color: color, textColor: '#ffffff', iconColor: '#ffffff' },
        { type: type, title: 'Face 2', icon: 'fa-star', color: TEMPLATE_FACE_COLORS[1], textColor: '#ffffff', iconColor: '#ffffff' },
        { type: type, title: 'Face 3', icon: 'fa-bolt', color: TEMPLATE_FACE_COLORS[2], textColor: '#ffffff', iconColor: '#ffffff' },
        { type: type, title: 'Face 4', icon: 'fa-heart', color: TEMPLATE_FACE_COLORS[3], textColor: '#ffffff', iconColor: '#ffffff' }
      ]
    };
    if (type === 'stack') {
      block.faces[0] = { type: 'stack', value: '0', label: 'New Stack', title: 'New Stack', icon: icon, color: color };
    } else if (type === 'image') {
      block.title = '';
      block.label = '';
      block.subtitle = '';
      block.image = '';
      block.faces.forEach(function (face) {
        face.type = 'image';
        delete face.title;
        delete face.label;
        delete face.image;
        delete face.subtitle;
      });
      ensureImageBlockDefaults(block);
    } else if (type === 'mixed') {
      block.mixedLayout = 'centeredStack';
      block.image = '';
      block.imageFit = 'contain';
      block.imagePosition = 'center';
      block.faces.forEach(function (face) {
        face.type = 'mixed';
        face.mixedLayout = 'centeredStack';
        face.imageFit = 'contain';
        face.imagePosition = 'center';
      });
    }
    layout.cards.push(block);
    placeCardInFirstFreeSlot(block);
    assignGridAreas();
    if (layout.activePage !== 'home') {
      layout.activePage = 'home';
      renderActivePage();
    }
    renderGrid();
    selectCard(id);
    markLayoutDirty();
    showToast('Block added - drag the grip to place anywhere');
  }

  function initSortable() {
    if (typeof Sortable === 'undefined' || !el.grid) return;
    destroySortable();
    var dragState = {
      cardId: null,
      moved: false,
      point: { x: 0, y: 0 },
      grab: { x: 0, y: 0 },
      onMove: null,
      onRelease: null,
      previewRaf: 0,
      lastPreviewKey: '',
      fallback: null,
      active: false
    };
    function ensureDragStatus() {
      var node = document.getElementById('ahpDragStatus');
      if (node) return node;
      node = document.createElement('div');
      node.id = 'ahpDragStatus';
      node.className = 'ahp-drag-status';
      node.setAttribute('aria-live', 'polite');
      node.innerHTML = '<i class="fas fa-hand-rock"></i><span>Dragging card — drop on any cell</span>';
      document.body.appendChild(node);
      return node;
    }
    function setDragStatus(active, label) {
      var node = ensureDragStatus();
      var text = node.querySelector('span');
      if (text && label && text.textContent !== label) text.textContent = label;
      node.style.setProperty('display', active ? 'inline-flex' : 'none', 'important');
    }
    function pinFallbackToCursor(clientX, clientY) {
      var node = dragState.fallback || document.querySelector('.ahp-sortable-fallback');
      if (!node) return;
      dragState.fallback = node;
      node.style.position = 'fixed';
      node.style.margin = '0';
      node.style.right = 'auto';
      node.style.bottom = 'auto';
      node.style.transform = 'none';
      node.style.left = Math.round(clientX - dragState.grab.x) + 'px';
      node.style.top = Math.round(clientY - dragState.grab.y) + 'px';
    }
    function clearDragSizing() {
      if (dragState.previewRaf) {
        cancelAnimationFrame(dragState.previewRaf);
        dragState.previewRaf = 0;
      }
      if (el.grid) el.grid.classList.remove('ahp-is-dragging');
      if (el.grid) {
        el.grid.querySelectorAll('.ahp-card').forEach(function (slot) {
          slot.style.width = '';
          slot.style.height = '';
          slot.style.willChange = '';
        });
      }
      document.querySelectorAll('.ahp-sortable-fallback').forEach(function (node) {
        if (node.parentNode) node.parentNode.removeChild(node);
      });
      document.body.classList.remove('ahp-dragging-card');
      setDragStatus(false);
      clearGridDropHighlights();
      dragState.lastPreviewKey = '';
      dragState.active = false;
      dragState.fallback = null;
      if (dragState.onMove) {
        window.removeEventListener('pointermove', dragState.onMove);
        window.removeEventListener('touchmove', dragState.onMove);
        dragState.onMove = null;
      }
      if (dragState.onRelease) {
        window.removeEventListener('pointerup', dragState.onRelease);
        window.removeEventListener('pointercancel', dragState.onRelease);
        window.removeEventListener('mouseup', dragState.onRelease);
        window.removeEventListener('touchend', dragState.onRelease);
        dragState.onRelease = null;
      }
    }
    function syncCardPositionsInDom() {
      if (!el.grid) return;
      var rows = getGridRows();
      var cols = getGridColumns();
      var existingCells = el.grid.querySelectorAll('.ahp-grid-drop-cell').length;
      if (existingCells !== rows * cols) {
        renderGrid();
        return;
      }
      (layout.cards || []).forEach(function (card) {
        if (!card || !card.id) return;
        clampCardPosition(card);
        var slot = el.grid.querySelector('[data-card-id="' + card.id + '"]');
        if (!slot) return;
        slot.style.gridColumn = card.col + ' / span ' + card.colSpan;
        slot.style.gridRow = card.row + ' / span ' + card.rowSpan;
        slot.setAttribute('data-col', String(card.col));
        slot.setAttribute('data-row', String(card.row));
        slot.setAttribute('data-col-span', String(card.colSpan));
        slot.setAttribute('data-row-span', String(card.rowSpan));
      });
      syncGridLayoutVars();
    }
    function updateDropPreview(clientX, clientY) {
      var card = layout.cards.find(function (c) { return c.id === dragState.cardId; });
      if (!card) return;
      ensureCardSize(card);
      var cell = clientPointToGridCell(clientX, clientY);
      if (!cell) {
        if (dragState.lastPreviewKey !== 'none') {
          clearGridDropHighlights();
          setDragStatus(true, 'Dragging — move over the grid to place');
          dragState.lastPreviewKey = 'none';
        }
        return;
      }
      var col = Math.max(1, Math.min(cell.col, getGridColumns() - card.colSpan + 1));
      var row = Math.max(1, Math.min(cell.row, getGridRows() - card.rowSpan + 1));
      var key = col + ':' + row + ':' + card.colSpan + ':' + card.rowSpan;
      if (key === dragState.lastPreviewKey) return;
      dragState.lastPreviewKey = key;
      highlightGridDropFootprint(col, row, card.colSpan, card.rowSpan);
      setDragStatus(true, 'Drop at column ' + col + ', row ' + row);
    }
    function scheduleDropPreview(clientX, clientY) {
      dragState.point.x = clientX;
      dragState.point.y = clientY;
      dragState.moved = true;
      if (dragState.previewRaf) return;
      dragState.previewRaf = requestAnimationFrame(function () {
        dragState.previewRaf = 0;
        updateDropPreview(dragState.point.x, dragState.point.y);
      });
    }
    sortable = Sortable.create(el.grid, {
      animation: 140,
      easing: 'cubic-bezier(0.2, 0, 0, 1)',
      sort: false,
      draggable: '.ahp-card',
      filter: '.ahp-slot-actions-tools, .ahp-slot-actions-tools *, .ahp-slot-btn[data-action], .ahp-grid-drop-cell, [data-add-placeholder]',
      preventOnFilter: true,
      forceFallback: true,
      fallbackOnBody: true,
      fallbackTolerance: 5,
      fallbackClass: 'ahp-sortable-fallback',
      ghostClass: 'sortable-ghost',
      chosenClass: 'sortable-chosen',
      dragClass: 'sortable-drag',
      delay: 0,
      delayOnTouchOnly: true,
      touchStartThreshold: 3,
      emptyInsertThreshold: 20,
      swapThreshold: 0.45,
      scroll: true,
      bubbleScroll: true,
      scrollSensitivity: 64,
      scrollSpeed: 22,
      onStart: function (evt) {
        commitLayoutHistoryBeforeChange();
        document.body.classList.add('ahp-dragging-card');
        el.grid.classList.add('ahp-is-dragging');
        setDragStatus(true, 'Dragging card — drop on any cell');
        dragState.cardId = evt && evt.item ? evt.item.getAttribute('data-card-id') : null;
        dragState.moved = false;
        dragState.active = true;
        dragState.lastPreviewKey = '';
        dragState.fallback = null;
        if (!evt || !evt.item) return;
        var rect = evt.item.getBoundingClientRect();
        var oe = evt.originalEvent || {};
        var startX = oe.clientX != null ? oe.clientX : (rect.left + rect.width / 2);
        var startY = oe.clientY != null ? oe.clientY : (rect.top + rect.height / 2);
        dragState.point.x = startX;
        dragState.point.y = startY;
        dragState.grab.x = startX - rect.left;
        dragState.grab.y = startY - rect.top;
        requestAnimationFrame(function () {
          pinFallbackToCursor(dragState.point.x, dragState.point.y);
        });
        dragState.onMove = function (e) {
          var cx = e.clientX != null ? e.clientX : (e.touches && e.touches[0] && e.touches[0].clientX);
          var cy = e.clientY != null ? e.clientY : (e.touches && e.touches[0] && e.touches[0].clientY);
          if (cx == null || cy == null) return;
          pinFallbackToCursor(cx, cy);
          scheduleDropPreview(cx, cy);
        };
        dragState.onRelease = function () {
          if (!dragState.active) return;
          setTimeout(function () {
            if (dragState.active) clearDragSizing();
          }, 80);
        };
        window.addEventListener('pointermove', dragState.onMove, { passive: true });
        window.addEventListener('touchmove', dragState.onMove, { passive: true });
        window.addEventListener('pointerup', dragState.onRelease, { passive: true });
        window.addEventListener('pointercancel', dragState.onRelease, { passive: true });
        window.addEventListener('mouseup', dragState.onRelease, { passive: true });
        window.addEventListener('touchend', dragState.onRelease, { passive: true });
      },
      onEnd: function (evt) {
        var cardId = dragState.cardId || (evt && evt.item && evt.item.getAttribute('data-card-id'));
        var pointX = dragState.point.x;
        var pointY = dragState.point.y;
        var didMove = dragState.moved;
        var previewKey = dragState.lastPreviewKey;
        clearDragSizing();
        if (!cardId) {
          syncCardPositionsInDom();
          return;
        }
        if (!didMove && evt && evt.originalEvent) {
          var oe2 = evt.originalEvent;
          if (oe2.clientX != null) {
            pointX = oe2.clientX;
            pointY = oe2.clientY;
            didMove = true;
          }
        }
        if (!didMove) {
          syncCardPositionsInDom();
          return;
        }
        var card = layout.cards.find(function (c) { return c.id === cardId; });
        var cell = clientPointToGridCell(pointX, pointY);
        if ((!cell || !cell.col) && previewKey && previewKey !== 'none') {
          var parts = String(previewKey).split(':');
          if (parts.length >= 2) {
            cell = { col: parseInt(parts[0], 10), row: parseInt(parts[1], 10) };
          }
        }
        if (!card || !cell || isNaN(cell.col) || isNaN(cell.row)) {
          syncCardPositionsInDom();
          return;
        }
        var changed = placeCardAt(card, cell.col, cell.row);
        layout.cards.sort(function (a, b) {
          var ar = getCardRow(a) || 999;
          var br = getCardRow(b) || 999;
          if (ar !== br) return ar - br;
          return (getCardCol(a) || 999) - (getCardCol(b) || 999);
        });
        assignGridAreas();
        syncCardPositionsInDom();
        if (changed) {
          markLayoutDirty({ historyNow: true });
          showToast('Card placed');
        }
      },
      onCancel: clearDragSizing
    });
  }

  function destroySortable() {
    if (sortable) { sortable.destroy(); sortable = null; }
  }

  var STUDIO_THEME_KEY = 'ahpStudioTheme';

  function getStudioThemeMode() {
    try {
      return localStorage.getItem(STUDIO_THEME_KEY) === 'light' ? 'light' : 'dark';
    } catch (e) {
      return 'dark';
    }
  }

  function applyStudioThemeMode(mode) {
    mode = mode === 'light' ? 'light' : 'dark';
    if (el.shell) {
      el.shell.classList.toggle('ahp-studio-light', mode === 'light');
      el.shell.classList.toggle('ahp-studio-dark', mode === 'dark');
    }
    var icon = document.getElementById('ahpStudioThemeIcon');
    var label = document.getElementById('ahpStudioThemeLabel');
    if (icon) {
      icon.className = mode === 'light' ? 'fas fa-moon' : 'fas fa-sun';
    }
    if (label) {
      label.textContent = mode === 'light' ? 'Dark' : 'Light';
    }
    if (el.btnStudioTheme) {
      el.btnStudioTheme.setAttribute('aria-pressed', mode === 'light' ? 'true' : 'false');
      el.btnStudioTheme.title = mode === 'light'
        ? 'Switch studio panels to dark mode'
        : 'Switch studio panels to light mode';
    }
    try {
      localStorage.setItem(STUDIO_THEME_KEY, mode);
    } catch (e) { /* ignore */ }
  }

  function toggleStudioThemeMode() {
    applyStudioThemeMode(getStudioThemeMode() === 'light' ? 'dark' : 'light');
  }

  function setEditMode(on) {
    editMode = on;
    el.viewport.classList.toggle('edit-mode', on);
    el.viewport.classList.toggle('view-mode', !on);
    if (el.btnSetting) el.btnSetting.style.display = on ? 'none' : 'inline-flex';
    if (el.btnDone) el.btnDone.style.display = on ? 'inline-flex' : 'none';
    if (el.btnSave) el.btnSave.style.display = on ? 'inline-flex' : 'none';
    if (el.btnSchema) el.btnSchema.style.display = on ? 'inline-flex' : 'none';
    if (el.btnStudioTheme) el.btnStudioTheme.style.display = on ? 'inline-flex' : 'none';

    if (el.grid) {
      if (on) {
        el.grid.classList.add('edit-sortable');
        initSortable();
      } else {
        el.grid.classList.remove('edit-sortable');
        destroySortable();
      }
    }

    var devGrid = document.getElementById('ahpDevicesGrid');
    if (devGrid) {
      if (on) {
        devGrid.classList.add('edit-sortable');
        if (layout.activePage === 'devices') {
          renderDevicesPage({ skipEditor: true });
        } else {
          initDeviceSortable();
        }
      } else {
        devGrid.classList.remove('edit-sortable');
        destroyDeviceSortable();
        if (layout.activePage === 'devices') {
          renderDevicesPage({ skipEditor: true });
        }
      }
    }

    updateSelectedCardHighlight();
    updateSelectedDeviceHighlight();
    refreshCubePlayback(el.phone);
    scheduleCubeGeometry(el.phone);
    syncCubeRotation(el.phone);

    requestAnimationFrame(function () {
      applyPreviewZoom();
      requestAnimationFrame(applyPreviewZoom);
    });
  }

  function getFitScale() {
    return 0.8;
  }

  function updateZoomUi(scale) {
    if (el.zoomLabel) el.zoomLabel.textContent = Math.round(scale * 100) + '%';
    if (el.btnZoomOut) el.btnZoomOut.disabled = !previewZoom.autoFit && scale <= ZOOM_MIN + 0.001;
    if (el.btnZoomIn) el.btnZoomIn.disabled = !previewZoom.autoFit && scale >= ZOOM_MAX - 0.001;
    if (el.btnZoomFit) el.btnZoomFit.classList.toggle('is-active', previewZoom.autoFit);
  }

  function applyPreviewZoom() {
    if (!el.previewMount || !el.previewFrame) return;
    var scale = previewZoom.autoFit ? getFitScale() : previewZoom.manualScale;
    scale = Math.max(ZOOM_MIN, Math.min(ZOOM_MAX, scale));
    if (!previewZoom.autoFit) previewZoom.manualScale = scale;
    var frameW = el.previewFrame.offsetWidth || 375;
    var frameH = el.previewFrame.offsetHeight || 812;
    el.previewFrame.style.transform = 'translateX(-50%) scale(' + scale + ')';
    el.previewFrame.style.transformStyle = 'preserve-3d';
    el.previewFrame.style.webkitTransformStyle = 'preserve-3d';
    el.previewFrame.style.marginBottom = Math.abs(scale - 1) > 0.001 ? ((frameH * scale) - frameH) + 'px' : '';
    el.previewMount.style.setProperty('--ahp-preview-scaled-width', (frameW * scale) + 'px');
    el.previewMount.style.setProperty('--ahp-preview-scaled-height', (frameH * scale) + 'px');
    if (el.viewport) el.viewport.scrollLeft = 0;
    updateZoomUi(scale);
    scheduleCubeGeometry(el.phone);
    syncCubeRotation(el.phone);
  }

  function zoomBy(delta) {
    if (previewZoom.autoFit) {
      previewZoom.manualScale = getFitScale();
      previewZoom.autoFit = false;
    }
    previewZoom.manualScale = Math.max(ZOOM_MIN, Math.min(ZOOM_MAX, previewZoom.manualScale + delta));
    applyPreviewZoom();
  }

  function resetZoomFit() {
    previewZoom.autoFit = true;
    applyPreviewZoom();
  }

  function buildSchema() {
    assignGridAreas();
    var schemaHelpers = {
      getCardAnimOffset: getCardAnimOffset,
      getDeviceAnimOffset: getDeviceAnimOffset,
      getDefaultFace: getDefaultFace,
      getDeviceDefaultFace: getDeviceDefaultFace,
      getCardFaceSec: getCardFaceSec,
      getCardRotationSpeed: getCardRotationSpeed
    };
    if (typeof window.AhpSchemaV3 !== 'undefined' && window.AhpSchemaV3.build) {
      var built = window.AhpSchemaV3.build(layout, cfg, schemaHelpers);
      if (window.AhpSchemaV3.sanitizePublishedSchema) {
        return window.AhpSchemaV3.sanitizePublishedSchema(built);
      }
      return built;
    }
    if (typeof window.AhpSchemaV2 === 'undefined' || !window.AhpSchemaV2.build) {
      throw new Error('app_home_schema.js not loaded (AhpSchemaV3 / AhpSchemaV2 missing)');
    }
    return window.AhpSchemaV2.build(layout, cfg, schemaHelpers);
  }

  function generateSchema() {
    try {
      return buildSchema();
    } catch (err) {
      console.error('Schema generation failed:', err);
      var detail = (err && err.message) ? String(err.message).slice(0, 140) : 'Unknown error';
      showToast('Schema generation failed: ' + detail);
      return null;
    }
  }

  function copyText(text, okMsg) {
    var done = function () { showToast(okMsg || 'Copied'); };
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text).then(done).catch(function () {
        fallbackCopy(text, done);
      });
    } else {
      fallbackCopy(text, done);
    }
  }

  function fallbackCopy(text, done) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.setAttribute('readonly', '');
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    document.body.removeChild(ta);
    if (done) done();
  }

  function syncSchemaApiUrl(url) {
    url = String(url || cfg.schemaApiUrl || '').trim();
    if (!url) return;
    cfg.schemaApiUrl = url;
    if (el.schemaApiUrlInput) el.schemaApiUrlInput.value = url;
    var openBtn = document.getElementById('ahpOpenApiBtn');
    if (openBtn) openBtn.href = url;
  }

  function copySchemaApiUrl() {
    var url = (el.schemaApiUrlInput && el.schemaApiUrlInput.value) || cfg.schemaApiUrl || '';
    if (!url) {
      showToast('API URL not available');
      return;
    }
    copyText(url, 'API URL copied');
  }

  function refreshSchemaPreview() {
    var schema = generateSchema();
    if (el.schemaOutput) {
      el.schemaOutput.value = schema ? JSON.stringify(schema, null, 2) : '';
    }
    if (el.schemaApiUrlInput && !el.schemaApiUrlInput.value) {
      syncSchemaApiUrl(cfg.schemaApiUrl || '');
    }
    return schema;
  }

  function copySchemaJson() {
    var schema = refreshSchemaPreview();
    if (!schema) return;
    copyText(JSON.stringify(schema, null, 2), 'Schema copied');
  }

  function openSchemaPanel() {
    setEditMode(true);
    setActiveMenu('schema');
    refreshSchemaPreview();
  }

  function flushLayoutBeforeSave() {
    flushAllCardsBeforeSave();
    applyDeviceEditor({ skipDirty: true });

    if (!layout.header) layout.header = {};
    layout.header.title = getVal('toolHeaderTitle');
    layout.header.subtitle = getVal('toolHeaderSub');

    if (!layout.welcome) layout.welcome = {};
    layout.welcome.greeting = getVal('toolWelcomeGreet');
    layout.welcome.email = getVal('toolWelcomeEmail');
    layout.welcome.caption = getVal('toolWelcomeCaption') || 'Manage your ecosystem';

    if (!layout.alerts) layout.alerts = {};
    var raw = getVal('toolAlertCount');
    layout.alerts.count = raw === '' ? 0 : (parseFloat(raw) || 0);
    layout.alerts.newUsers = parseInt(getVal('toolAlertUsers'), 10) || 0;
    layout.alerts.badgeText = getVal('toolAlertBadge');
    layout.alerts.showBadge = getCheck('toolShowAlertBadge');

    flushThemeFromTools(true);

    var profile = getProfilePage();
    applyLoginUserProfileToLayout();
    profile.notificationsEnabled = getCheck('toolProfileNotifications');

    var devicesPage = getDevicesPage();
    if (!devicesPage.header) devicesPage.header = {};
    if (!devicesPage.status) devicesPage.status = {};

    if (selectedPageCardId) {
      var pageCard = (layout.cards || []).find(function (c) { return c.id === selectedPageCardId; });
      if (pageCard) {
        pageCard.pageRoute = getVal('toolPageRoute').trim();
        pageCard.pageLabel = getVal('toolPageLabel');
      }
    }

    layout.activePage = 'home';
    delete layout.activeFacePage;
    if (Array.isArray(layout.bottomNav)) {
      layout.bottomNav.forEach(function (item) {
        if (item) item.active = item.id === 'home';
      });
    }

    assignGridAreas();
  }

  function applyLayoutFromServer(serverLayout, options) {
    options = options || {};
    if (!serverLayout || typeof serverLayout !== 'object') return;
    var localCards = Array.isArray(layout.cards) ? layout.cards : [];
    layout = JSON.parse(JSON.stringify(serverLayout));
    if (!options.allowEmptyCards && (!Array.isArray(layout.cards) || !layout.cards.length)) {
      layout.cards = JSON.parse(JSON.stringify(localCards));
    } else if (!Array.isArray(layout.cards)) {
      layout.cards = [];
    }
    layout.activePage = layout.activePage || 'home';
    if (layout.activePage === 'settings') layout.activePage = 'profile';
    if (layout.activePage === 'category') layout.activePage = 'grid';
    normalizeBottomNav();
    ensureSavedCardsStructure();
    assignGridAreas();
  }

  function refreshPreviewAfterSave(options) {
    options = options || {};
    layout.activePage = layout.activePage || 'home';
    if (layout.activePage === 'settings') layout.activePage = 'profile';
    ensureSavedCardsStructure();
    assignGridAreas();
    applyTheme();
    renderActivePage();
    renderHeader();
    renderBottomNav();
    syncStaticHomeView();
    scheduleCubeGeometry(el.phone);
    refreshCubePlayback(el.phone);
    requestAnimationFrame(function () {
      scheduleCubeGeometry(el.phone);
      syncCubeRotation(el.phone);
      applyPreviewZoom();
      requestAnimationFrame(function () {
        scheduleCubeGeometry(el.phone);
        syncCubeRotation(el.phone);
        updateSelectedCardHighlight();
        if (!options.skipEditor) {
          refreshSchemaPreview();
          syncToolFields();
          renderThemePresets();
          renderStaticUiTemplates();
          if (selectedCardId) syncCardEditor(true);
          if (selectedDeviceId) syncDeviceEditor(true);
        }
      });
    });
  }

  function markLayoutDirty(options) {
    if (options && typeof options === 'object' && typeof options.type === 'string') {
      options = {};
    }
    options = options || {};
    if (!layoutReady) return;
    layoutDirty = true;
    layoutSaveEpoch += 1;
    if (canRecordLayoutHistory()) {
      if (options.historyNow) {
        recordLayoutHistoryNow();
      } else {
        scheduleLayoutHistorySnapshot();
      }
    }
    scheduleAutoSave();
  }

  function scheduleAutoSave() {
    if (saveInFlight) return;
    if (saveDebounceTimer) clearTimeout(saveDebounceTimer);
    saveDebounceTimer = setTimeout(function () {
      if (layoutDirty) saveLayout({ auto: true });
    }, 2500);
  }

  function saveLayout(options) {
    options = options || {};
    if (saveInFlight) {
      layoutDirty = true;
      return;
    }
    if (saveDebounceTimer) {
      clearTimeout(saveDebounceTimer);
      saveDebounceTimer = null;
    }
    flushLayoutBeforeSave();
    var epochAtStart = layoutSaveEpoch;
    var layoutToSave = JSON.parse(JSON.stringify(layout));
    var schema = generateSchema();
    if (!schema) {
      showToast('Cannot save - schema generation failed');
      return;
    }

    var payload = { layout: layoutToSave, schema: schema };
    if (cfg.csrfName && cfg.csrfHash) {
      payload[cfg.csrfName] = cfg.csrfHash;
    }

    saveInFlight = true;
    fetch(cfg.apiBase + '/layout', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    })
      .then(function (r) {
        if (!r.ok) {
          return r.text().then(function (text) {
            throw new Error('HTTP ' + r.status + (text ? ': ' + text.slice(0, 120) : ''));
          });
        }
        return r.json();
      })
      .then(function (res) {
        if (res.csrfHash && cfg.csrfName) {
          cfg.csrfHash = res.csrfHash;
        }
        if (!res.success) {
          showToast(res.message || 'Save failed');
          return;
        }
        hasSavedLayout = true;
        cfg.hasSavedLayout = true;
        if (res.schemaApiUrl) {
          syncSchemaApiUrl(res.schemaApiUrl);
        }
        if (res.schema && typeof res.schema === 'object') {
          cfg.schema = res.schema;
        }
        // Newer edits landed while this save was in flight — keep local layout and save again.
        if (epochAtStart !== layoutSaveEpoch) {
          layoutDirty = true;
          if (!options.auto) showToast('Layout and settings saved');
          return;
        }
        layoutDirty = false;
        if (res.layout && typeof res.layout === 'object') {
          applyLayoutFromServer(res.layout);
          cfg.layout = JSON.parse(JSON.stringify(layout));
        } else {
          layout = layoutToSave;
          cfg.layout = JSON.parse(JSON.stringify(layoutToSave));
          ensureSavedCardsStructure();
          assignGridAreas();
        }
        if (res.profile && typeof res.profile === 'object') {
          cfg.loginUserProfile = res.profile;
          applyLoginUserProfileToLayout(res.profile);
        }
        if (!options.auto) {
          showToast('Layout and settings saved');
        }
        refreshPreviewAfterSave({ skipEditor: !!options.auto });
        if (options.auto) {
          var savedSnap = captureLayoutSnapshot();
          if (layoutHistoryIndex >= 0 && layoutHistory[layoutHistoryIndex] !== savedSnap) {
            pushLayoutHistorySnapshot(true);
          }
        } else {
          resetLayoutHistory();
        }
      })
      .catch(function (err) {
        console.error('Save failed:', err);
        showToast('Save failed - check connection or sign in again');
      })
      .then(function () {
        saveInFlight = false;
        if (layoutDirty) scheduleAutoSave();
      });
  }

  function rangeUnitFor(id) {
    if (id === 'toolGridColumns' || id === 'toolGridRows' || id === 'ceCardWidth' || id === 'ceCardHeight' || id === 'deCardWidth' || id === 'deCardHeight') return '';
    if (id === 'toolCardFaceSec' || id === 'ceFaceSec') return ' sec';
    return 'px';
  }

  function bindField(id, handler) {
    var node = document.getElementById(id);
    if (!node) return;
    if (node.type === 'range') {
      var unit = rangeUnitFor(id);
      updateRangeUi(node, unit);
      node.addEventListener('input', function () {
        updateRangeUi(node, unit);
        handler();
      });
      return;
    }
    var evt = node.type === 'checkbox' || node.tagName === 'SELECT' ? 'change' : 'input';
    node.addEventListener(evt, handler);
  }

  function bindTools() {
    bindField('toolHeaderTitle', function () {
      layout.header.title = getVal('toolHeaderTitle');
      renderHeader();
    });
    bindField('toolHeaderSub', function () {
      layout.header.subtitle = getVal('toolHeaderSub');
      renderHeader();
    });
    bindField('toolWelcomeGreet', function () {
      layout.welcome.greeting = getVal('toolWelcomeGreet');
      renderWelcomeBanner();
    });
    bindField('toolWelcomeEmail', function () {
      layout.welcome.email = getVal('toolWelcomeEmail');
      renderWelcomeBanner();
    });
    bindField('toolWelcomeCaption', function () {
      if (!layout.welcome) layout.welcome = {};
      layout.welcome.caption = getVal('toolWelcomeCaption');
      renderWelcomeBanner();
    });
    bindField('toolAlertCount', function () {
      var raw = getVal('toolAlertCount');
      layout.alerts.count = raw === '' ? 0 : (parseFloat(raw) || 0);
      renderHeader();
    });
    bindField('toolAlertUsers', function () {
      layout.alerts.newUsers = parseInt(getVal('toolAlertUsers'), 10) || 0;
      renderHeader();
    });
    bindField('toolAlertBadge', function () {
      layout.alerts.badgeText = getVal('toolAlertBadge');
      applyTheme();
    });
    bindField('toolShowAlertBadge', function () {
      layout.alerts.showBadge = getCheck('toolShowAlertBadge');
      applyTheme();
    });

    bindField('toolLogoIcon', function () {
      getTheme().logoIcon = getVal('toolLogoIcon');
      renderWelcomeBanner();
      applyTheme();
    });
    bindField('toolGridGap', function () {
      var gapNode = document.getElementById('toolGridGap');
      var gap = gapNode ? normalizeGridGap(gapNode.value, 4) : 4;
      getTheme().gridGap = gap;
      getTheme()._gapUserSet = true;
      if (gapNode) updateRangeUi(gapNode, 'px');
      syncGridLayoutVars();
      applyTheme();
    });
    bindField('toolGridColumns', function () {
      var colsNode = document.getElementById('toolGridColumns');
      var cols = colsNode ? normalizeGridColumns(colsNode.value, 3) : 3;
      getTheme().gridColumns = cols;
      clampAllCardSpans();
      syncGridLayoutVars();
      applyTheme();
      renderGrid();
      if (selectedCardId) syncCardEditor(true);
      markLayoutDirty();
    });
    bindField('toolGridRows', function () {
      var rowsNode = document.getElementById('toolGridRows');
      var rows = rowsNode ? normalizeGridRows(rowsNode.value, 3) : 3;
      getTheme().gridRows = rows;
      clampAllCardSpans();
      renderGrid();
      if (selectedCardId) syncCardEditor(true);
      markLayoutDirty();
    });
    bindField('toolGridRowHeight', function () {
      // Cell height is derived from column width so 1x1 tiles stay square.
      // Slider is informational — syncGridLayoutVars restores the square size.
      getTheme().squareTiles = true;
      syncGridLayoutVars();
      applyTheme();
      renderGrid();
      markLayoutDirty();
    });
    bindField('toolCardBorderRadius', function () {
      var radiusNode = document.getElementById('toolCardBorderRadius');
      var radius = radiusNode ? parseInt(radiusNode.value, 10) : 0;
      if (isNaN(radius)) radius = 0;
      radius = Math.max(0, Math.min(72, radius));
      getTheme().cardBorderRadius = radius;
      if (radiusNode) updateRangeUi(radiusNode, 'px');
      (layout.cards || []).forEach(function (card) {
        if (!card) return;
        card.borderRadius = radius;
      });
      var cardRadiusNode = document.getElementById('ceCardRadius');
      if (cardRadiusNode) {
        cardRadiusNode.value = String(radius);
        updateRangeUi(cardRadiusNode, 'px');
        var liveBox = document.querySelector('#ceCardRadiusLivePreview .ahp-radius-live-box');
        if (liveBox) liveBox.style.borderRadius = radius + 'px';
      }
      if (el.grid && layout.cards) renderGrid();
      markLayoutDirty();
    });
    bindField('toolCardFaceSec', function () {
      var faceNode = document.getElementById('toolCardFaceSec');
      var faceSec = faceNode ? parseFloat(faceNode.value) || 4 : 4;
      getTheme().cardSpeed = cardSpeedFromFaceSeconds(faceSec);
      if (faceNode) updateRangeUi(faceNode, ' sec');
      if (el.phone) el.phone.style.setProperty('--ahp-card-speed', getTheme().cardSpeed + 's');
      cardAnimOffsets = {};
      deviceAnimOffsets = {};
      applyTheme();
      refreshCubePlayback(el.phone);
    });
    bindField('toolShowWelcome', function () {
      getTheme().showWelcome = getCheck('toolShowWelcome');
      applyTheme();
    });
    bindField('toolShowAlerts', function () {
      getTheme().showAlerts = getCheck('toolShowAlerts');
      applyTheme();
    });
    bindField('toolShowBottomNav', function () {
      getTheme().showBottomNav = getCheck('toolShowBottomNav');
      applyTheme();
    });

    bindField('toolBgStyle', function () {
      applyBgPreset(getVal('toolBgStyle'));
      renderThemePresets();
      if (layout.activePage === 'profile' || layout.activePage === 'settings') {
        renderProfilePage(getTheme().accentColor || '#202940');
      }
    });
    bindColorField('toolAccentColor', function () {
      getTheme().accentColor = getBackgroundColorVal('toolAccentColor') || getColorVal('toolAccentColor');
      applyTheme();
    });
    bindColorField('toolAccentGreen', function () {
      getTheme().accentGreen = getBackgroundColorVal('toolAccentGreen') || getColorVal('toolAccentGreen');
      applyTheme();
    });
    bindColorField('toolSurfaceColor', function () {
      getTheme().surfaceColor = getBackgroundColorVal('toolSurfaceColor') || getColorVal('toolSurfaceColor');
      applyTheme();
    });
    bindColorField('toolWelcomeStart', function () {
      getTheme().welcomeStart = getBackgroundColorVal('toolWelcomeStart') || getColorVal('toolWelcomeStart');
      applyTheme();
    });
    bindColorField('toolWelcomeEnd', function () {
      getTheme().welcomeEnd = getBackgroundColorVal('toolWelcomeEnd') || getColorVal('toolWelcomeEnd');
      applyTheme();
    });
    bindColorField('toolPhoneBg', function () {
      getTheme().phoneBg = getBackgroundColorVal('toolPhoneBg') || getColorVal('toolPhoneBg');
      applyTheme();
    });
    bindColorField('toolAlertBg', function () {
      getTheme().alertBg = getBackgroundColorVal('toolAlertBg') || getColorVal('toolAlertBg');
      applyTheme();
    });
    bindColorField('toolNavActiveColor', function () {
      getTheme().navActiveColor = getBackgroundColorVal('toolNavActiveColor') || getColorVal('toolNavActiveColor');
      applyTheme();
    });
    bindColorField('toolTextPrimary', function () {
      getTheme().textPrimary = getBackgroundColorVal('toolTextPrimary') || getColorVal('toolTextPrimary');
      applyTheme();
    });
    bindColorField('toolTextMuted', function () {
      getTheme().textMuted = getBackgroundColorVal('toolTextMuted') || getColorVal('toolTextMuted');
      applyTheme();
    });
    bindColorField('toolNavBg', function () {
      getTheme().navBg = getBackgroundColorVal('toolNavBg') || getColorVal('toolNavBg');
      applyTheme();
    });

    bindField('toolNavActive', function () {
      setNavActive(getVal('toolNavActive'));
    });

    bindField('toolProfileNotifications', function () {
      getProfilePage().notificationsEnabled = getCheck('toolProfileNotifications');
      if (layout.activePage === 'profile' || layout.activePage === 'settings') {
        renderProfilePage(getTheme().accentColor || '#202940');
      }
    });
    syncProfileTools();

    var addDevBtn = document.getElementById('ahpAddDeviceBtn');
    if (addDevBtn) addDevBtn.addEventListener('click', addDevice);

    document.querySelectorAll('.ahp-block-item').forEach(function (node) {
      node.addEventListener('click', function () {
        addBlock(node.getAttribute('data-type'));
      });
    });
    var clearCardsBtn = document.getElementById('ahpClearAllCardsBtn');
    if (clearCardsBtn) clearCardsBtn.addEventListener('click', clearAllCards);

    var resetBtn = document.getElementById('ahpResetLayout');
    var menuResetBtn = document.getElementById('ahpMenuResetBtn');
    function resetLayoutToDefaults() {
      if (!confirm('Reset to default layout? Unsaved changes will be lost.')) return;
      fetch(cfg.apiBase + '/reset', { method: 'POST' })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res.layout) {
            layout = JSON.parse(JSON.stringify(res.layout));
            cardAnimOffsets = {};
            selectedCardId = null;
            selectedPageCardId = null;
            cardEditorCardId = null;
            selectedDeviceId = null;
            activeFaceIndex = 0;
            selectedNavEditId = null;
            layoutInitialized = false;
            ensureLayoutStructure();
            ensureAllCardPositions(false);
            renderAll();
            resetLayoutHistory();
            layoutDirty = false;
            showToast('Layout reset to defaults');
          }
        })
        .catch(function () { showToast('Reset failed - check connection'); });
    }
    if (resetBtn) resetBtn.addEventListener('click', resetLayoutToDefaults);
    if (menuResetBtn) menuResetBtn.addEventListener('click', resetLayoutToDefaults);

    var clearStaticBtn = document.getElementById('ahpBtnClearStaticUi');
    if (clearStaticBtn) clearStaticBtn.addEventListener('click', clearStaticUiTemplate);

    var toolsPanel = document.querySelector('.ahp-tools');
    if (toolsPanel && !toolsPanel._dirtyBound) {
      toolsPanel._dirtyBound = true;
      toolsPanel.addEventListener('input', function () { markLayoutDirty(); });
      toolsPanel.addEventListener('change', function () { markLayoutDirty(); });
    }
  }


  function closestScrollPane(target) {
    if (!target) return null;
    return target.closest('.ahp-menu-column, .ahp-tools-scroll, .ahp-phone-scroll, .ahp-preview-column');
  }

  function routeWheelToLocalPane(e) {
    if (e.ctrlKey) return;
    var pane = closestScrollPane(e.target);
    if (!pane) pane = el.previewMount ? el.previewMount.closest('.ahp-preview-column') : null;
    if (!pane || pane === el.viewport || pane.classList.contains('ahp-viewport')) return;
    var maxScroll = pane.scrollHeight - pane.clientHeight;
    if (maxScroll <= 1) return;
    var goingUp = e.deltaY < 0;
    var goingDown = e.deltaY > 0;
    var atTop = pane.scrollTop <= 0;
    var atBottom = pane.scrollTop >= maxScroll - 1;
    // Only block parent scrolling while the local pane can still scroll.
    // Keep native scrolling (no manual scrollTop) so trackpads stay smooth.
    if ((goingUp && !atTop) || (goingDown && !atBottom)) {
      e.stopPropagation();
    }
  }
  function bindZoom() {
    if (el.btnZoomIn) el.btnZoomIn.addEventListener('click', function () { zoomBy(ZOOM_STEP); });
    if (el.btnZoomOut) el.btnZoomOut.addEventListener('click', function () { zoomBy(-ZOOM_STEP); });
    if (el.btnZoomFit) el.btnZoomFit.addEventListener('click', resetZoomFit);
    // Use bubble (not capture) so the pane receives native wheel first.
    if (el.viewport) el.viewport.addEventListener('wheel', routeWheelToLocalPane, { passive: true, capture: false });
    if (el.previewMount) {
      el.previewMount.addEventListener('wheel', function (e) {
        if (!e.ctrlKey) return;
        e.preventDefault();
        zoomBy(e.deltaY > 0 ? -ZOOM_STEP : ZOOM_STEP);
      }, { passive: false });
    }
    window.addEventListener('resize', function () { requestAnimationFrame(applyPreviewZoom); });
  }


  function cleanHexColor(value, fallback) {
    var v = String(value || '').trim();
    return /^#[0-9a-f]{6}$/i.test(v) ? v : fallback;
  }

  function applicationCubeFallbackFaces() {
    var defaults = [
      { id: 'appid', name: 'AppID', subtitle: 'Generation', icon: 'fa-key', route: '/appid', gradientStart: '#2563eb', gradientEnd: '#14b8a6', textColor: '#ffffff', badge: 'Secure' },
      { id: 'hrms', name: 'HRMS', subtitle: 'People Ops', icon: 'fa-users', route: '/hrms', gradientStart: '#7c3aed', gradientEnd: '#ec4899', textColor: '#ffffff', badge: 'Live' },
      { id: 'estore', name: 'eStore', subtitle: 'Commerce', icon: 'fa-shopping-cart', route: '/estore', gradientStart: '#f97316', gradientEnd: '#facc15', textColor: '#ffffff', badge: 'Open' },
      { id: 'grip', name: 'GRIP', subtitle: 'Insights', icon: 'fa-chart-line', route: '/grip', gradientStart: '#0f766e', gradientEnd: '#22c55e', textColor: '#ffffff', badge: 'New' }
    ];
    var source = (layout.cards || []).slice(0, 4);
    return defaults.map(function (fallback, i) {
      var card = source[i] || {};
      ensureFaces(card);
      var face = (card.faces && card.faces[getDefaultFace(card)]) || (card.faces && card.faces[0]) || {};
      var bg = face.color || card.color || '';
      var parts = String(bg).match(/#[0-9a-f]{6}/ig) || [];
      return {
        id: card.id || fallback.id,
        name: face.title || face.label || card.title || card.label || fallback.name,
        subtitle: face.subtitle || face.value || card.subtitle || fallback.subtitle,
        icon: normalizeFaIcon(face.icon || card.icon || fallback.icon),
        image: face.image || card.image || fallback.image || '',
        route: face.route || card.pageRoute || card.route || fallback.route,
        applicationId: face.applicationId || card.applicationId || card.id || fallback.id,
        gradientStart: cleanHexColor(face.gradientStart || parts[0], fallback.gradientStart),
        gradientEnd: cleanHexColor(face.gradientEnd || parts[1], fallback.gradientEnd),
        textColor: cleanHexColor(face.textColor || face.titleTextColor || card.textColor, fallback.textColor),
        badge: face.badge || card.badge || fallback.badge
      };
    });
  }

  function ensureApplicationCube() {
    var existing = layout.applicationCube || {};
    var faces = Array.isArray(existing.faces) && existing.faces.length ? existing.faces.slice(0, 4) : applicationCubeFallbackFaces();
    var fallback = applicationCubeFallbackFaces();
    while (faces.length < 4) faces.push(fallback[faces.length]);
    faces = faces.slice(0, 4).map(function (face, i) {
      var f = Object.assign({}, fallback[i], face || {});
      f.id = f.id || fallback[i].id;
      f.name = f.name || fallback[i].name;
      f.subtitle = f.subtitle || fallback[i].subtitle;
      f.icon = normalizeFaIcon(f.icon || fallback[i].icon);
      f.image = f.image || '';
      f.route = f.route || fallback[i].route;
      f.applicationId = f.applicationId || f.id;
      f.gradientStart = cleanHexColor(f.gradientStart, fallback[i].gradientStart);
      f.gradientEnd = cleanHexColor(f.gradientEnd, fallback[i].gradientEnd);
      f.textColor = cleanHexColor(f.textColor, '#ffffff');
      f.badge = f.badge || '';
      return f;
    });
    var duration = parseInt(existing.durationMs, 10) || ((parseInt(getTheme().cardSpeed, 10) || 16) * 1000);
    if ([12000, 16000, 20000, 28000].indexOf(duration) === -1) duration = 16000;
    layout.applicationCube = Object.assign({}, existing, {
      enabled: existing.enabled !== false,
      faceCount: 4,
      autoRotate: existing.autoRotate !== false,
      rotationAxis: 'y',
      durationMs: duration,
      perspective: Math.max(650, Math.min(1400, parseInt(existing.perspective, 10) || 900)),
      size: Math.max(124, Math.min(190, parseInt(existing.size, 10) || 150)),
      pauseOnInteraction: existing.pauseOnInteraction !== false,
      faces: faces
    });
    getTheme().cardSpeed = Math.round(layout.applicationCube.durationMs / 1000);
    return layout.applicationCube;
  }

  function applicationCubeCurrentFace(slot) {
    var cube = ensureApplicationCube();
    if (!cube.autoRotate) return 0;
    var duration = Math.max(1000, parseInt(cube.durationMs, 10) || 16000);
    var elapsed = Date.now() % duration;
    return Math.min(3, Math.floor(elapsed / (duration / 4)));
  }

  function renderApplicationCubeFace(face, index) {
    var background = face.image
      ? 'linear-gradient(145deg, rgba(6,11,22,.30), rgba(6,11,22,.66)), url(\'' + cssUrl(face.image) + '\') center/cover'
      : 'linear-gradient(145deg, ' + esc(face.gradientStart) + ', ' + esc(face.gradientEnd) + ')';
    return '<button type="button" class="ahp-cube-face ahp-app-cube-face face-' + index + '" data-face-index="' + index + '" data-route="' + esc(face.route || '') + '" data-application-id="' + esc(face.applicationId || face.id || '') + '" style="background:' + background + ';color:' + esc(face.textColor || '#ffffff') + ';--ahp-face-title-color:' + esc(face.textColor || '#ffffff') + ';--ahp-face-sub-color:' + esc(face.textColor || '#ffffff') + '">' +
      '<span class="ahp-app-cube-shine" aria-hidden="true"></span>' +
      '<span class="ahp-app-cube-icon"><i class="fas ' + esc(normalizeFaIcon(face.icon || 'fa-cube')) + '"></i></span>' +
      '<span class="ahp-app-cube-title">' + esc(face.name || ('Application ' + (index + 1))) + '</span>' +
      '<span class="ahp-app-cube-subtitle">' + esc(face.subtitle || '') + '</span>' +
      (face.badge ? '<span class="ahp-app-cube-badge">' + esc(face.badge) + '</span>' : '<span class="ahp-app-cube-badge ahp-app-cube-badge-empty">Open</span>') +
      '</button>';
  }

  function renderApplicationCube() {
    var cube = ensureApplicationCube();
    var facesHtml = cube.faces.map(renderApplicationCubeFace).join('');
    var active = editMode && activeMenuKey === 'cards';
    var classes = 'ahp-cube ahp-application-cube' + (cube.autoRotate ? '' : ' paused ahp-show-face') + (active ? ' ahp-show-face' : '');
    var faceIndex = active ? activeFaceIndex : 0;
    return '<div class="ahp-app-cube-shell' + (cube.pauseOnInteraction ? ' pause-enabled' : '') + '" data-application-cube="1" style="--ahp-app-cube-size:' + cube.size + 'px;--ahp-app-cube-duration:' + cube.durationMs + 'ms;--ahp-card-speed:' + (cube.durationMs / 1000) + 's;perspective:' + cube.perspective + 'px">' +
      '<div class="ahp-app-cube-glow" aria-hidden="true"></div>' +
      '<div class="' + classes + '" style="--ahp-face-index:' + faceIndex + ';--ahp-card-radius:24px">' + facesHtml + '</div>' +
      '<div class="ahp-app-cube-floor" aria-hidden="true"></div>' +
      '</div>';
  }

  function renderGrid() {
    if (el.grid) el.grid.classList.remove('ahp-application-grid');
    renderLegacyCardGrid();
  }

  function refreshThemedPreview() {
    if (layout.activePage === 'home' && el.grid) {
      refreshLegacyCardThemedPreview();
    } else if (layout.activePage === 'devices') {
      loadDevicesFromIotApi(false);
    } else if (layout.activePage === 'profile' || layout.activePage === 'settings') {
      renderProfilePage(getTheme().accentColor || '#202940');
    }
  }

  function refreshCubePlayback(scope) {
    var phone = el.phone || document.getElementById('ahpPhone');
    var cardSpeed = (getTheme().cardSpeed || 16) + 's';
    /* Apply speed to all cubes via the phone root CSS variable */
    if (phone) phone.style.setProperty('--ahp-card-speed', cardSpeed);

    var root = scope || phone || document;
    root.querySelectorAll('.ahp-cube').forEach(function (cube) {
      var slot = cube.closest('[data-card-id],[data-device-id]');
      if (!slot) return;
      var cardId = slot.getAttribute('data-card-id');
      var deviceId = slot.getAttribute('data-device-id');
      var freeze = false;
      var faceIndex = 0;
      if (editMode && cardId && selectedCardId === cardId && cardEditorCardId === cardId) {
        freeze = true;
        faceIndex = activeFaceIndex;
      } else if (editMode && activeMenuKey === 'devices' && deviceId && selectedDeviceId === deviceId) {
        freeze = true;
        faceIndex = activeDeviceFaceIndex;
      }
      cube.classList.toggle('paused', freeze);
      cube.classList.toggle('ahp-show-face', freeze);
      if (freeze) {
        cube.style.setProperty('--ahp-face-index', faceIndex);
      } else {
        cube.style.removeProperty('--ahp-face-index');
      }
    });
    syncCubeRotation(root);
  }

  function renderCardPicker() {
    renderLegacyCardPicker();
  }

  function updateBlockCount() {
    updateLegacyBlockCount();
  }

  function syncCardEditor(forceRebuild) {
    syncLegacyCardEditor(forceRebuild);
  }

  function applyCardEditor(opts) {
    applyLegacyCardEditor(opts || {});
  }

  function openApplicationCubeFace(faceIndex) {
    var cube = ensureApplicationCube();
    faceIndex = Math.max(0, Math.min(3, parseInt(faceIndex, 10) || 0));
    var face = cube.faces[faceIndex] || cube.faces[0];
    if (!face) return;
    navigateFaceRoute(face.route || '', {
      cardId: 'application-cube',
      faceIndex: faceIndex,
      title: face.name || 'Application',
      subtitle: face.subtitle || face.route || '',
      icon: face.icon || 'fa-cube',
      color: face.gradientStart || '#202940',
      side: ['Face 1', 'Face 2', 'Face 3', 'Face 4'][faceIndex],
      fallbackFacePage: {
        cardId: 'application-cube',
        faceIndex: faceIndex,
        title: face.name || 'Application',
        subtitle: face.subtitle || face.route || '',
        icon: face.icon || 'fa-cube',
        color: face.gradientStart || '#202940',
        side: ['Face 1', 'Face 2', 'Face 3', 'Face 4'][faceIndex],
        actionText: face.route ? ('Open ' + face.route) : ('Open ' + (face.name || 'application'))
      }
    });
  }

  function bindSlotActions() {
    bindLegacySlotActions();
  }

  function updateSelectedCardHighlight() {
    updateLegacySelectedCardHighlight();
  }

  function selectCard(cardId) {
    if (selectedCardId && selectedCardId !== cardId) {
      applyCardEditor();
    }
    selectedCardId = cardId;
    activeFaceIndex = 0;
    renderCardPicker();
    var sel = document.getElementById('ahpCardSelect');
    if (sel) sel.value = cardId || '';
    syncCardEditor(true);
    setActiveMenu('cards');
    updateSelectedCardHighlight();
    if (cardId) {
      syncSelectedCardCubeFace({ animate: false });
    }
  }
  function bindHomeChrome() {
    ['ahpHomeAvatar', 'ahpWelcomeAvatar'].forEach(function (id) {
      var avatar = document.getElementById(id);
      if (avatar && !avatar._ahpBound) {
        avatar._ahpBound = true;
        avatar.addEventListener('click', function () {
          switchPage('profile');
        });
      }
    });
  }

  function bindUI() {
    layout.activePage = 'home';
    bindGridInAppBrowser();
    bindInAppBrowser();
    if (Array.isArray(layout.bottomNav)) {
      layout.bottomNav.forEach(function (item) {
        if (item) item.active = item.id === 'home';
      });
    }

    if (el.btnSetting) el.btnSetting.addEventListener('click', function () { setEditMode(true); });
    if (el.btnUndo) el.btnUndo.addEventListener('click', undoLayoutChange);
    if (el.btnRedo) el.btnRedo.addEventListener('click', redoLayoutChange);
    document.addEventListener('keydown', function (e) {
      if (!layoutReady) return;
      var tag = (e.target && e.target.tagName) || '';
      if (/^(INPUT|TEXTAREA|SELECT)$/.test(tag)) return;
      if (e.ctrlKey || e.metaKey) {
        if (e.key === 'z' && !e.shiftKey) {
          e.preventDefault();
          undoLayoutChange();
        } else if (e.key === 'y' || (e.key === 'z' && e.shiftKey)) {
          e.preventDefault();
          redoLayoutChange();
        }
      }
    });
    if (el.btnDone) el.btnDone.addEventListener('click', function () {
      if (layoutDirty) {
        saveLayout();
      }
      setEditMode(false);
    });
    if (el.btnSave) el.btnSave.addEventListener('click', saveLayout);
    if (el.btnSchema) el.btnSchema.addEventListener('click', openSchemaPanel);
    if (el.btnStudioTheme) el.btnStudioTheme.addEventListener('click', toggleStudioThemeMode);
    applyStudioThemeMode(getStudioThemeMode());
    if (el.btnBack) el.btnBack.addEventListener('click', function () { window.location.href = cfg.dashboardUrl || '/'; });
    if (el.btnCopyApiUrl) el.btnCopyApiUrl.addEventListener('click', copySchemaApiUrl);
    if (el.btnCopySchema) el.btnCopySchema.addEventListener('click', copySchemaJson);

    if (cfg.schema && cfg.schema.schemaVersion && cfg.schema.schemaVersion !== '3.0.0') {
      setTimeout(function () {
        showToast('Schema outdated (' + cfg.schema.schemaVersion + '). Click Save to publish v3.0.0 SDUI schema.');
      }, 800);
    }

    el.menuItems.forEach(function (m) {
      m.addEventListener('click', function () {
        setActiveMenu(m.getAttribute('data-menu'));
      });
    });

    bindSlotActions();
    bindDeviceSlotActions();
    bindHomeChrome();
    bindTools();
    bindZoom();
    renderAll();
    bindCubeGeometryObserver();
    setEditMode(false);
    startFaceSwapLoop();
    refreshSchemaPreview();
    syncSchemaApiUrl(cfg.schemaApiUrl || '');
    /* Force cube animation start after layout paint */
    requestAnimationFrame(function () {
      refreshCubePlayback(el.phone);
      scheduleCubeGeometry(el.phone);
      syncCubeRotation(el.phone);
      requestAnimationFrame(function () {
        refreshCubePlayback(el.phone);
        syncCubeRotation(el.phone);
      });
    });

    /* Initial zoom + speed sync after first paint */
    requestAnimationFrame(function () {
      applyPreviewZoom();
      syncGridLayoutVars();
      var phone = el.phone || document.getElementById('ahpPhone');
      if (phone) phone.style.setProperty('--ahp-card-speed', (getTheme().cardSpeed || 16) + 's');
      requestAnimationFrame(function () {
        syncGridLayoutVars();
        applyPreviewZoom();
        if (layout.activePage === 'home') renderGrid();
      });
    });

    window.addEventListener('resize', function () {
      requestAnimationFrame(function () {
        syncGridLayoutVars();
        applyPreviewZoom();
        scheduleCubeGeometry(el.phone);
        if (layout.activePage === 'home' && getTheme().squareTiles !== false) renderGrid();
      });
    });

    layoutReady = true;
    resetLayoutHistory();
    preloadIotDevices();

    try {
      var bootPage = String(new URLSearchParams(window.location.search).get('page') || '').trim().toLowerCase();
      if (bootPage === 'category') bootPage = 'grid';
      if (bootPage === 'grid' || bootPage === 'devices' || bootPage === 'profile' || bootPage === 'home') {
        switchPage(bootPage);
      }
    } catch (e) { /* ignore */ }

    document.addEventListener('iot:power-error', function (e) {
      var msg = e && e.detail && e.detail.message ? e.detail.message : 'Power toggle failed.';
      showToast(msg);
    });
    document.addEventListener('iot:power-click', function (e) {
      var detail = e && e.detail ? e.detail : {};
      if (detail.power) {
        showToast('Power ' + detail.power);
      }
    });

    document.addEventListener('visibilitychange', function () {
      if (document.hidden) {
        stopIotStatusPoll();
        return;
      }
      if (layout.activePage === 'devices') {
        refreshIotDevicesStatusSilent();
        startIotStatusPoll();
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindUI);
  } else {
    bindUI();
  }
})();