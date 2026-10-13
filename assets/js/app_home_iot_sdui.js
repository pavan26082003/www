/**
 * IoT SDUI renderer for App Home Page — devices tab preview.
 * Renders UI from get_iot_schema and wires iot-toggle to dataSources.powerToggle.
 */
(function (global) {
  'use strict';

  var ASSET_HOST = 'https://efactor.tech';
  var runtimeConfig = {
    iotApiUrl: '',
    accessToken: '',
    sessionApiBase: '',
    onRefresh: null
  };

  var SIGNAL_SVG = '<svg viewBox="0 0 24 24" aria-hidden="true">'
    + '<path d="M5 12.55a11 11 0 0 1 14.08 0"/>'
    + '<path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>'
    + '<circle cx="12" cy="20" r="1" fill="currentColor" stroke="none"/>'
    + '</svg>';

  var HEADER_HOME_SVG = '<svg viewBox="0 0 24 24" aria-hidden="true">'
    + '<path d="M4 10.5 12 4l8 6.5"/>'
    + '<path d="M6 9.5V20h12V9.5"/>'
    + '<path d="M10 20v-5h4v5"/>'
    + '<path d="M12 4v2"/>'
    + '<circle cx="17" cy="7" r="1.2" fill="currentColor" stroke="none"/>'
    + '<path d="M16 7h2"/>'
    + '</svg>';

  var CONNECTION_ART_SVG = '<svg viewBox="0 0 24 24" aria-hidden="true">'
    + '<path d="M5 12.55a11 11 0 0 1 14.08 0"/>'
    + '<path d="M1.42 9a16 16 0 0 1 21.16 0"/>'
    + '<path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>'
    + '<circle cx="12" cy="20" r="1.5" fill="currentColor" stroke="none"/>'
    + '</svg>';

  function esc(v) {
    return String(v == null ? '' : v)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function isPowerOn(value) {
    if (value === true || value === 1) return true;
    if (value === false || value === 0 || value == null || value === '') return false;
    var normalized = String(value).toLowerCase().trim();
    return normalized === '1' || normalized === '01' || normalized === 'true'
      || normalized === 'yes' || normalized === 'on';
  }

  function resolveAssetUrl(path) {
    var value = String(path || '').trim();
    if (!value) return '';
    if (/^https?:\/\//i.test(value)) return value;
    if (value.indexOf('//') === 0) return window.location.protocol + value;
    var origin = (typeof window !== 'undefined' && window.location && window.location.origin)
      ? window.location.origin
      : ASSET_HOST;
    if (value.charAt(0) === '/') return origin + value;
    if (value.indexOf('/') !== -1) return origin + '/' + value.replace(/^\/+/, '');
    return origin + '/files/brand_logo/' + encodeURIComponent(value);
  }

  function mergeRuntime(runtime) {
    if (!runtime || typeof runtime !== 'object') return runtimeConfig;
    runtimeConfig = {
      iotApiUrl: String(runtime.iotApiUrl || runtimeConfig.iotApiUrl || '').replace(/\/+$/, ''),
      accessToken: String(runtime.accessToken || runtimeConfig.accessToken || '').trim(),
      sessionApiBase: String(runtime.sessionApiBase || runtime.appApiBase || runtimeConfig.sessionApiBase || '').replace(/\/+$/, ''),
      onRefresh: typeof runtime.onRefresh === 'function' ? runtime.onRefresh : runtimeConfig.onRefresh
    };
    return runtimeConfig;
  }

  function extractApiError(data, fallback) {
    if (!data || typeof data !== 'object') return fallback || 'Request failed.';
    return data.message || data.description || data.error || fallback || 'Request failed.';
  }

  function refreshRuntimeAccessToken(runtime) {
    runtime = mergeRuntime(runtime);
    var base = String(runtime.sessionApiBase || '').replace(/\/+$/, '');
    if (!base) return Promise.resolve(runtime);
    return fetch(base + '/iot-access-token', {
      method: 'GET',
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json' }
    }).then(function (res) {
      return res.json().catch(function () { return {}; });
    }).then(function (data) {
      if (data && data.access_token) {
        runtime.accessToken = String(data.access_token).trim();
      }
      if (data && data.iotApiUrl) {
        runtime.iotApiUrl = String(data.iotApiUrl).replace(/\/+$/, '');
      }
      mergeRuntime(runtime);
      return runtime;
    }).catch(function () {
      return runtime;
    });
  }

  function isAddWifiNodeSource(dataSource, url) {
    if (dataSource && dataSource.id === 'addWifiNode') return true;
    return String(url || '').indexOf('action=add_wifi_node') >= 0;
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

  function resolveSessionProxyUrl(dataSource, runtime) {
    runtime = mergeRuntime(runtime);
    var proxy = dataSource && dataSource.webProxy;
    if (!proxy || !proxy.path) return '';
    var base = runtime.sessionApiBase || '';
    if (!base && proxy.appHomeBase) base = String(proxy.appHomeBase).replace(/\/+$/, '');
    if (!base) return '';
    return base + '/' + String(proxy.path).replace(/^\/+/, '');
  }

  function resolvePowerToggleSource(schema) {
    if (schema && schema.dataSources && schema.dataSources.powerToggle) {
      return schema.dataSources.powerToggle;
    }
    return null;
  }

  function buildPowerToggleUrl(schema, runtime) {
    runtime = mergeRuntime(runtime);
    var source = resolvePowerToggleSource(schema);
    var sessionBase = String(runtime.sessionApiBase || '').replace(/\/+$/, '');
    if (sessionBase) {
      return {
        url: sessionBase + '/device-power-toggle',
        source: source,
        useSession: true
      };
    }
    var base = runtime.iotApiUrl || '';
    if (source && source.url) {
      base = String(source.url).replace(/\/+$/, '');
    }
    if (!base && typeof window !== 'undefined' && window.location && window.location.origin) {
      if (/localhost|127\.0\.0\.1/i.test(window.location.origin)) {
        base = window.location.origin + '/iot_api_creation.php';
      } else {
        base = ASSET_HOST + '/v8/iot_api_creation.php';
      }
    }
    var action = (source && source.query && source.query.action) ? source.query.action : 'device_power_toggle';
    var url = base + (base.indexOf('?') >= 0 ? '&' : '?') + 'action=' + encodeURIComponent(action);
    if (runtime.accessToken && url.indexOf('access_token=') < 0) {
      url += '&access_token=' + encodeURIComponent(runtime.accessToken);
    }
    return { url: url, source: source, useSession: false };
  }

  function normalizePowerNodeId(device) {
    // Prefer the DB node_id as stored on the device card (may include socket_).
    var raw = String(device.node_id || device.nodeId || '').trim();
    if (raw) return raw;
    return normalizeQrNodeId(String(device.node && device.node.node_id || '').trim());
  }

  function postPowerToggle(schema, device, turningOn, runtime) {
    runtime = mergeRuntime(runtime);
    var nodeId = normalizePowerNodeId(device);
    if (!nodeId) {
      return Promise.reject(new Error('Missing node_id for power toggle.'));
    }

    var endpoint = buildPowerToggleUrl(schema, runtime);
    var perDevice = device.actions && device.actions.onPowerToggle;
    var url = (perDevice && perDevice.url && !endpoint.useSession) ? String(perDevice.url) : endpoint.url;
    if (!endpoint.useSession && runtime.accessToken && url.indexOf('access_token=') < 0) {
      url += (url.indexOf('?') >= 0 ? '&' : '?') + 'access_token=' + encodeURIComponent(runtime.accessToken);
    }

    var headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    };
    if (!endpoint.useSession) {
      headers['X-IOT-Access-Token'] = runtime.accessToken || '';
    }
    if (perDevice && perDevice.headers && typeof perDevice.headers === 'object' && !endpoint.useSession) {
      Object.keys(perDevice.headers).forEach(function (key) {
        var val = perDevice.headers[key];
        if (val === '{{auth.access_token}}') val = runtime.accessToken;
        if (val != null && String(val).indexOf('$secureState') < 0) {
          headers[key] = String(val);
        }
      });
    }

    var body = {
      node_id: nodeId,
      command: turningOn ? 'on' : 'off',
      power: turningOn
    };

    return fetch(url, {
      method: 'POST',
      headers: headers,
      credentials: endpoint.useSession ? 'same-origin' : 'omit',
      body: JSON.stringify(body)
    }).then(function (res) {
      return res.json().catch(function () { return {}; }).then(function (data) {
        if (!res.ok || data.success === false) {
          throw new Error(extractApiError(data, 'IoT API "powerToggle" failed with HTTP ' + res.status + '.'));
        }
        if (data.status === 'success' || typeof data.power !== 'undefined') {
          data.success = true;
        }
        return data;
      });
    });
  }

  function syncCardPowerState(card, device, turningOn) {
    if (!card || !device) return;
    var toggle = card.querySelector('.iot-toggle');
    var label = card.querySelector('.iot-power-label');
    // Online/offline is independent of power — never flip card online class here.
    var online = device.online !== false && String(device.status || '').toLowerCase() !== 'offline';
    var powerOn = !!turningOn;
    if (toggle) {
      toggle.classList.toggle('is-on', powerOn);
      toggle.setAttribute('aria-pressed', powerOn ? 'true' : 'false');
      toggle.disabled = !online;
      toggle.setAttribute('aria-disabled', online ? 'false' : 'true');
    }
    if (label) label.textContent = powerOn ? 'On' : 'Off';
    device.power = powerOn;
    device.powerOn = powerOn;
  }

  function extractDevices(schema) {
    if (!schema || typeof schema !== 'object') return [];
    if (Array.isArray(schema.devices) && schema.devices.length) return schema.devices.slice();
    if (schema.data && Array.isArray(schema.data.devices) && schema.data.devices.length) return schema.data.devices.slice();
    if (schema.data && Array.isArray(schema.data.items) && schema.data.items.length) return schema.data.items.slice();
    var page = schema.page || null;
    if (page && Array.isArray(page.devices) && page.devices.length) return page.devices.slice();
    if (page && Array.isArray(page.components)) {
      for (var i = 0; i < page.components.length; i++) {
        var comp = page.components[i];
        if (comp && Array.isArray(comp.items) && comp.items.length) return comp.items.slice();
        if (comp && Array.isArray(comp.devices) && comp.devices.length) return comp.devices.slice();
      }
    }
    if (Array.isArray(schema.components)) {
      for (var j = 0; j < schema.components.length; j++) {
        var c = schema.components[j];
        if (c && Array.isArray(c.items) && c.items.length) return c.items.slice();
        if (c && Array.isArray(c.devices) && c.devices.length) return c.devices.slice();
      }
    }
    if (schema.components && typeof schema.components === 'object') {
      var keys = Object.keys(schema.components);
      for (var k = 0; k < keys.length; k++) {
        var node = schema.components[keys[k]];
        if (node && Array.isArray(node.items) && node.items.length) return node.items.slice();
        if (node && Array.isArray(node.devices) && node.devices.length) return node.devices.slice();
      }
    }
    return [];
  }

  function resolveLayout(schema) {
    schema = schema || {};
    var layout = schema.layout || {};
    var grid = layout.grid || {};
    var card = layout.card || {};
    var page = schema.page || {};
    var pagePadding = layout.pagePadding || page.padding || {};
    var summary = page.summary || layout.summary || schema.summary || schema.metrics || {};
    var header = page.header || layout.header || {};

    if ((!grid.columns && !grid.gap) && Array.isArray(page.components)) {
      for (var ci = 0; ci < page.components.length; ci++) {
        var comp = page.components[ci];
        if (comp && (comp.type === 'deviceGrid' || comp.id === 'iot-device-grid')) {
          if (comp.columns != null) grid.columns = comp.columns;
          if (comp.gap != null) grid.gap = comp.gap;
          if (comp.card && typeof comp.card === 'object') {
            card = Object.assign({}, card, comp.card);
          }
          break;
        }
      }
    }
    if ((!grid.columns && !grid.gap) && Array.isArray(schema.components)) {
      for (var sj = 0; sj < schema.components.length; sj++) {
        var sc = schema.components[sj];
        if (sc && (sc.type === 'deviceGrid' || sc.id === 'iot-device-grid')) {
          if (sc.columns != null) grid.columns = sc.columns;
          if (sc.gap != null) grid.gap = sc.gap;
          if (sc.card && typeof sc.card === 'object') {
            card = Object.assign({}, card, sc.card);
          }
          break;
        }
      }
    }

    var columns = parseInt(grid.columns, 10);
    if (isNaN(columns) || columns < 1) columns = 2;
    var gap = parseInt(grid.gap, 10);
    if (isNaN(gap)) gap = 12;
    var borderRadius = card.borderRadius != null ? parseInt(card.borderRadius, 10) : 24;
    if (isNaN(borderRadius)) borderRadius = 24;
    var minHeight = card.minHeight != null ? parseInt(card.minHeight, 10) : 168;
    if (isNaN(minHeight)) minHeight = 168;
    return {
      columns: Math.max(1, Math.min(4, columns)),
      gap: Math.max(4, gap),
      cardBorderRadius: Math.max(0, borderRadius),
      cardMinHeight: Math.max(120, minHeight),
      paddingH: parseInt(pagePadding.horizontal, 10) || 12,
      paddingTop: parseInt(pagePadding.top, 10) || 14,
      paddingBottom: parseInt(pagePadding.bottom, 10) || 28,
      header: header,
      summary: summary,
      theme: schema.theme || {},
      generatedDate: schema.generatedDate || schema.savedDate || '',
      generatedAt: schema.generatedAt || schema.savedAt || ''
    };
  }

  function sortDevicesOnlineFirst(list) {
    return list.slice().sort(function (a, b) {
      var ao = a && a.online !== false ? 1 : 0;
      var bo = b && b.online !== false ? 1 : 0;
      if (ao !== bo) return bo - ao;
      var an = String(a.product_name || a.productName || a.name || '').toLowerCase();
      var bn = String(b.product_name || b.productName || b.name || '').toLowerCase();
      return an.localeCompare(bn);
    });
  }

  function parseDevice(d) {
    var online = d.online !== false && String(d.status || '').toLowerCase() !== 'offline';
    var powerRaw = d.power != null ? d.power : d.powerOn;
    var powerOn = isPowerOn(powerRaw);
    var productName = d.product_name || d.productName || d.name || d.modelname || d.model_name || 'Device';
    var brandName = d.brandName || d.brand_name || d.vendor_name || d.vendorName || 'Efactor';
    var brandLogo = resolveAssetUrl(d.brandLogo || d.brand_logo || d.brandlogo || d.brandLogoUrl || '');
    var productImage = resolveAssetUrl(d.productImage || d.product_image || d.Product_image || d.img_path || '');
    return {
      productName: productName,
      deviceType: d.devicetype || d.device_type || d.vendor_type || d.productName || '',
      brandName: brandName,
      brandLogo: brandLogo,
      productImage: productImage,
      nodeId: d.node_id || d.id || '',
      status: d.status || (online ? 'Online' : 'Offline'),
      online: online,
      powerOn: online && powerOn,
      resolvedIconSvg: d.resolvedIconSvg || '',
      modelName: d.modelName || d.model_name || d.modelname || '',
      modelNumber: d.modelNumber || d.model_number || d.model_no || ''
    };
  }

  function deviceIconSvg(type) {
    var t = String(type || '').toLowerCase();
    if (/fan|air|ventil|exhaust/.test(t)) {
      return '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 9c0-3-2.5-6-5-5.5C4 4 3 7 5 9c1 1 4 1 4 1s-1 3 1 5c1.5 2 4.5 2 5.5.5C17 14 15 11 12 9z"/><path d="M12 15c0 3 2.5 6 5 5.5C20 20 21 17 19 15c-1-1-4-1-4-1s1-3-1-5c-1.5-2-4.5-2-5.5-.5C7 10 9 13 12 15z"/></svg>';
    }
    if (/light|lamp|bulb|led|tube/.test(t)) {
      return '<svg viewBox="0 0 24 24"><path d="M9 21h6M12 3a6 6 0 0 1 4.24 10.24C15.5 14 15 15 15 16H9c0-1-.5-2-1.24-2.76A6 6 0 0 1 12 3z"/><path d="M9 16v1a3 3 0 0 0 6 0v-1"/></svg>';
    }
    if (/plug|socket|switch|outlet|relay/.test(t)) {
      return '<svg viewBox="0 0 24 24"><rect x="6" y="3" width="12" height="13" rx="2"/><path d="M9 7v3M15 7v3"/><path d="M12 16v5M8 21h8"/></svg>';
    }
    if (/camera|cam|cctv|security|monitor/.test(t)) {
      return '<svg viewBox="0 0 24 24"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3z"/><circle cx="12" cy="13" r="3"/></svg>';
    }
    if (/lock|door|gate|access/.test(t)) {
      return '<svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1.5" fill="currentColor" stroke="none"/></svg>';
    }
    if (/thermo|temp|heat|cool|hvac|ac|climate/.test(t)) {
      return '<svg viewBox="0 0 24 24"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/></svg>';
    }
    if (/sensor|motion|pir|detect/.test(t)) {
      return '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="2"/><path d="M16.24 7.76a6 6 0 0 1 0 8.49M7.76 16.24a6 6 0 0 1 0-8.49"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 19.07a10 10 0 0 1 0-14.14"/></svg>';
    }
    return '<svg viewBox="0 0 24 24"><rect x="7" y="7" width="10" height="10" rx="1"/><path d="M9 3v2M12 3v2M15 3v2M9 19v2M12 19v2M15 19v2M3 9h2M3 12h2M3 15h2M19 9h2M19 12h2M19 15h2"/></svg>';
  }

  function cardIconEl(logo, brandName, deviceType, resolvedSvg) {
    var initial = esc((brandName || 'E').charAt(0).toUpperCase());
    var svg = resolvedSvg || deviceIconSvg(deviceType || brandName);
    var fallback = logo
      ? '<span class="iot-icon-fallback is-initial">' + initial + '</span>'
      : '<span class="iot-icon-fallback">' + svg + '</span>';
    var img = logo
      ? '<img class="iot-logo" src="' + esc(logo) + '" alt="' + esc(brandName) + '" loading="lazy" onerror="this.style.display=\'none\'">'
      : '';
    return '<div class="iot-card-icon">' + fallback + img + '</div>';
  }

  function productImgEl(url, name, deviceType, resolvedSvg) {
    var iconFallback = '<div class="iot-product-fallback" aria-hidden="true">' + (resolvedSvg || deviceIconSvg(deviceType || name)) + '</div>';
    if (url) {
      return '<div class="iot-product-wrap">'
        + '<img class="iot-product-image" src="' + esc(url) + '" alt="' + esc(name) + '" loading="lazy"'
        + ' onerror="this.closest(\'.iot-product-wrap\').classList.add(\'is-fallback\')">'
        + iconFallback
        + '</div>';
    }
    return '<div class="iot-product-wrap is-fallback">' + iconFallback + '</div>';
  }

  function buildCard(d, i, cardStyle) {
    var p = parseDevice(d);
    var cls = p.online ? ' is-online' : ' is-offline';
    var stateLabel = p.online ? 'Online' : 'Offline';
    var powerLabel = p.online ? (p.powerOn ? 'On' : 'Off') : 'Off';
    var toggleCls = p.powerOn ? ' is-on' : '';
    var typeHtml = (p.deviceType && p.deviceType !== p.productName)
      ? '<p class="iot-device-type">' + esc(p.deviceType) + '</p>'
      : '';
    var modelNo = p.modelNumber || p.modelName || '';
    var modelHtml = modelNo
      ? '<p class="iot-device-type iot-model-no">' + esc(modelNo) + '</p>'
      : '';
    var radius = cardStyle.borderRadius != null ? cardStyle.borderRadius : 24;
    var minH = cardStyle.minHeight != null ? cardStyle.minHeight : 168;

    return (
      '<article class="iot-device-card' + cls + '" tabindex="0" role="listitem"'
      + ' style="border-radius:' + radius + 'px;min-height:' + minH + 'px"'
      + ' data-device-index="' + i + '" data-node-id="' + esc(p.nodeId) + '"'
      + ' aria-label="' + esc(p.productName) + ', ' + esc(p.brandName) + ', ' + stateLabel + '">'
      + '<div class="iot-card-top">'
      + cardIconEl(p.brandLogo, p.brandName, p.deviceType || p.productName, p.resolvedIconSvg)
      + '<span class="iot-card-signal">' + SIGNAL_SVG + '</span>'
      + '</div>'
      + '<div class="iot-card-body">'
      + '<h3 class="iot-device-name">' + esc(p.productName) + '</h3>'
      + '<p class="iot-brand-name">' + esc(p.brandName) + '</p>'
      + modelHtml
      + typeHtml
      + '</div>'
      + productImgEl(p.productImage, p.productName, p.deviceType || p.productName, p.resolvedIconSvg)
      + '<div class="iot-status-line">' + esc(stateLabel) + '</div>'
      + '<div class="iot-card-footer">'
      + '<span class="iot-power-label">' + esc(powerLabel) + '</span>'
      + '<button type="button" class="iot-toggle' + toggleCls + '"'
      + ' data-iot-control="powerApiToggle"'
      + ' data-action-source="powerToggle"'
      + ' data-bind="device.power"'
      + (p.online ? '' : ' disabled aria-disabled="true"')
      + ' aria-pressed="' + (p.powerOn ? 'true' : 'false') + '"'
      + ' aria-label="' + esc(p.online ? 'Toggle power for ' + p.productName : p.productName + ' is offline') + '"></button>'
      + '</div>'
      + '</article>'
    );
  }

  function formatSummaryDate(schema) {
    if (schema.generatedDate) return schema.generatedDate;
    if (schema.generatedAt) {
      try {
        return new Date(schema.generatedAt).toLocaleDateString(undefined, { day: 'numeric', month: 'long', year: 'numeric' });
      } catch (e) { /* ignore */ }
    }
    return new Date().toLocaleDateString(undefined, { day: 'numeric', month: 'long', year: 'numeric' });
  }

  var ADD_DEVICE_SVG = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14"/><path d="M5 12h14"/></svg>';
  var qrScanner = null;
  var qrBusy = false;
  var activeSchema = null;
  var activeRuntime = null;

  function resolveHeaderAction(schema) {
    if (!schema || typeof schema !== 'object') return null;
    var uiActions = schema.ui && Array.isArray(schema.ui.headerActions) ? schema.ui.headerActions : [];
    if (uiActions.length) return uiActions[0];
    var pageAvatar = schema.page && schema.page.header ? schema.page.header.avatar : null;
    if (pageAvatar && (pageAvatar.control === 'addDeviceButton' || pageAvatar.icon === 'addDevice')) {
      return Object.assign({}, pageAvatar, {
        id: 'addDevice',
        type: 'iconButton',
        control: 'addDeviceButton',
        ariaLabel: pageAvatar.ariaLabel || 'Scan QR to add device'
      });
    }
    return null;
  }

  function resolveOverlay(schema, overlayId) {
    if (!schema || !schema.overlays) return null;
    return schema.overlays[overlayId] || null;
  }

  function buildDataSourceUrl(dataSource, runtime, extraQuery) {
    runtime = mergeRuntime(runtime);
    if (!dataSource || !dataSource.url) return '';
    var url = String(dataSource.url).replace(/\/+$/, '');
    var query = dataSource.query || {};
    var parts = [];
    Object.keys(query).forEach(function (key) {
      var val = query[key];
      if (val && typeof val === 'object' && val.$secureState) {
        val = runtime.accessToken;
      }
      if (extraQuery && extraQuery[key] != null && extraQuery[key] !== '') {
        val = extraQuery[key];
      }
      if (val != null && String(val) !== '') {
        parts.push(encodeURIComponent(key) + '=' + encodeURIComponent(String(val)));
      }
    });
    if (parts.length) {
      url += (url.indexOf('?') >= 0 ? '&' : '?') + parts.join('&');
    }
    return url;
  }

  function callDataSource(dataSource, runtime, body, extraQuery) {
    runtime = mergeRuntime(runtime);
    var proxyUrl = resolveSessionProxyUrl(dataSource, runtime);
    var method = proxyUrl
      ? String((dataSource.webProxy && dataSource.webProxy.method) || dataSource.method || 'POST').toUpperCase()
      : String((dataSource && dataSource.method) || 'POST').toUpperCase();
    var url = proxyUrl || buildDataSourceUrl(dataSource, runtime, extraQuery);
    var headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    };
    if (!proxyUrl) {
      headers['X-IOT-Access-Token'] = runtime.accessToken || '';
    }
    var options = {
      method: method,
      headers: headers,
      credentials: proxyUrl ? 'same-origin' : 'omit'
    };
    if (method === 'POST') {
      options.body = JSON.stringify(body || {});
    }
    return fetch(url, options).then(function (res) {
      return res.json().catch(function () { return {}; }).then(function (data) {
        if (isAddWifiNodeSource(dataSource, url)) {
          if (data && data.status === 'success') {
            data.success = true;
          } else if (!data || data.status !== 'success') {
            throw new Error(extractApiError(data, 'Could not add device.'));
          }
        } else if (data && (data.status === 'success' || data.prod_category != null)) {
          data.success = true;
        }
        if (!res.ok || data.success === false || data.status === 'failure') {
          throw new Error(extractApiError(data, 'Request failed (' + res.status + ').'));
        }
        return data;
      });
    });
  }

  function normalizeQrNodeId(raw) {
    var value = String(raw || '').trim();
    if (!value) return '';
    value = value.replace(/^["']+|["']+$/g, '').trim();
    try {
      if (/%[0-9A-Fa-f]{2}/.test(value)) {
        value = decodeURIComponent(value);
      }
    } catch (e0) { /* keep raw */ }
    if (value.charAt(0) === '{') {
      try {
        var parsed = JSON.parse(value);
        if (parsed) {
          if (parsed.node_id) return String(parsed.node_id).trim();
          if (parsed.nodeId) return String(parsed.nodeId).trim();
          if (parsed.NODE_ID) return String(parsed.NODE_ID).trim();
          if (parsed.id) return String(parsed.id).trim();
        }
      } catch (e) { /* keep raw */ }
    }
    if (/^https?:\/\//i.test(value)) {
      try {
        var url = new URL(value);
        var fromQuery = url.searchParams.get('node_id') || url.searchParams.get('nodeId') || url.searchParams.get('id');
        if (fromQuery) return String(fromQuery).trim();
        var parts = url.pathname.split('/').filter(Boolean);
        if (parts.length) return String(parts[parts.length - 1]).trim();
      } catch (e2) { /* keep raw */ }
    }
    // Keep socket_ prefix — server matches both forms; legacy v8 uses QR value as-is.
    return value.trim();
  }

  function getCurrentLocation(options) {
    options = options || {};
    return new Promise(function (resolve) {
      if (!navigator.geolocation) {
        resolve({ latitude: '', longitude: '' });
        return;
      }
      navigator.geolocation.getCurrentPosition(
        function (pos) {
          resolve({
            latitude: String(pos.coords.latitude),
            longitude: String(pos.coords.longitude)
          });
        },
        function () { resolve({ latitude: '', longitude: '' }); },
        options
      );
    });
  }

  function ensureQrLibrary(overlay) {
    var libUrl = (overlay && overlay.scanner && overlay.scanner.libraryUrl)
      ? overlay.scanner.libraryUrl
      : 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js';
    if (typeof Html5Qrcode === 'function') return Promise.resolve();
    return new Promise(function (resolve, reject) {
      var existing = document.querySelector('script[data-iot-qr-lib="1"]');
      if (existing) {
        existing.addEventListener('load', function () { resolve(); });
        existing.addEventListener('error', reject);
        return;
      }
      var script = document.createElement('script');
      script.src = libUrl;
      script.async = true;
      script.setAttribute('data-iot-qr-lib', '1');
      script.onload = function () { resolve(); };
      script.onerror = reject;
      document.head.appendChild(script);
    });
  }

  var QR_CLOSE_ICON = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>';
  var QR_HERO_ICON = '<svg viewBox="0 0 24 24"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><path d="M7 12h10"/></svg>';
  var QR_CODE_ICON = '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3z"/><path d="M19 14h2v2h-2z"/><path d="M14 19h2v2h-2z"/><path d="M18 18h3v3h-3z"/></svg>';

  function resolveQrSteps(overlay) {
    if (overlay && Array.isArray(overlay.steps) && overlay.steps.length) {
      return overlay.steps;
    }
    if (overlay && Array.isArray(overlay.flow)) {
      return overlay.flow.map(function (step) {
        return { id: step.step, label: step.label || step.step };
      });
    }
    return [
      { id: 'scan', label: 'Scan' },
      { id: 'checkDevice', label: 'Verify' },
      { id: 'addWifiNode', label: 'Add' }
    ];
  }

  function buildQrModalHtml(overlay) {
    overlay = overlay || {};
    var header = overlay.header || {};
    var hero = overlay.hero || {};
    var steps = resolveQrSteps(overlay);
    var cancelLabel = 'Cancel';
    if (Array.isArray(overlay.actions)) {
      overlay.actions.forEach(function (action) {
        if (action && (action.id === 'cancel' || action.id === 'close')) {
          cancelLabel = action.label || cancelLabel;
        }
      });
    }
    var stepsHtml = steps.map(function (step, index) {
      var cls = index === 0 ? ' iot-qr-step is-active' : ' iot-qr-step';
      return '<div class="' + cls.trim() + '" data-step="' + esc(step.id || '') + '">' + esc(step.label || step.id || '') + '</div>';
    }).join('');
    var idleMessage = (overlay.flow && overlay.flow[0] && overlay.flow[0].statusMessage)
      ? overlay.flow[0].statusMessage
      : 'Point your camera at the device QR code…';
    var rightAction = header.rightAction || {};
    var manual = overlay.manualEntry || {};
    var manualInput = manual.input || {};
    var rightAria = rightAction.ariaLabel || rightAction.title || rightAction.label || 'Enter node ID';
    var cancelManual = 'Cancel';
    var connectManual = 'Connect';
    if (Array.isArray(manual.actions)) {
      manual.actions.forEach(function (action) {
        if (!action) return;
        if (action.id === 'cancel') cancelManual = action.label || cancelManual;
        if (action.id === 'connect') connectManual = action.label || connectManual;
      });
    }
    return (
      '<div class="iot-qr-sheet" role="dialog" aria-modal="true" aria-labelledby="ahpIotQrTitle">'
      + '<header class="iot-qr-sheet-header">'
      + '<button type="button" class="iot-qr-icon-btn" id="ahpIotQrCloseBtn" aria-label="Close scanner">' + QR_CLOSE_ICON + '</button>'
      + '<span class="iot-qr-sheet-title" id="ahpIotQrTitle">' + esc(header.title || overlay.title || 'Scan Device') + '</span>'
      + '<button type="button" class="iot-qr-icon-btn is-qr" id="ahpIotQrManualBtn" data-iot-control="manualNodeIdButton" aria-label="' + esc(rightAria) + '" title="' + esc(rightAria) + '">' + QR_CODE_ICON + '</button>'
      + '</header>'
      + '<div class="iot-qr-sheet-body">'
      + '<div class="iot-qr-hero">'
      + '<span class="iot-qr-hero-icon" aria-hidden="true">' + QR_HERO_ICON + '</span>'
      + '<div><span class="iot-qr-kicker">' + esc(hero.kicker || 'Add WiFi Node') + '</span>'
      + '<p class="iot-qr-subtitle" id="ahpIotQrSubtitle">' + esc(hero.subtitle || overlay.subtitle || 'Align the QR code on your device inside the frame.') + '</p></div>'
      + '</div>'
      + '<div class="iot-qr-scanner-stage">'
      + '<div class="iot-qr-reader-wrap" id="ahpIotQrReader"></div>'
      + '<div class="iot-qr-scan-overlay" aria-hidden="true">'
      + '<div class="iot-qr-scan-frame"><span></span><span></span><span></span><span></span><div class="iot-qr-scan-line"></div></div>'
      + '</div></div>'
      + '<div class="iot-qr-steps" id="ahpIotQrSteps">' + stepsHtml + '</div>'
      + '<div class="iot-qr-status-pill" id="ahpIotQrStatus"><span class="iot-qr-status-dot" aria-hidden="true"></span>'
      + '<span class="iot-qr-status-text">' + esc(idleMessage) + '</span></div>'
      + '</div>'
      + '<footer class="iot-qr-sheet-footer"><button type="button" class="iot-qr-btn ghost" id="ahpIotQrCancelBtn">' + esc(cancelLabel) + '</button></footer>'
      + '<div class="iot-qr-manual-modal" id="ahpIotQrManualModal" aria-hidden="true">'
      + '<div class="iot-qr-manual-card" role="dialog" aria-modal="true" aria-labelledby="ahpIotQrManualTitle">'
      + '<h3 class="iot-qr-manual-title" id="ahpIotQrManualTitle">' + esc(manual.title || 'Enter Node ID') + '</h3>'
      + '<p class="iot-qr-manual-hint" id="ahpIotQrManualHint">' + esc(manual.subtitle || 'Type the device node ID to connect. We verify it, then register the node — same as scanning the QR code.') + '</p>'
      + '<label class="iot-qr-manual-label" for="ahpIotQrNodeIdInput">' + esc(manualInput.label || 'Node ID') + '</label>'
      + '<input type="text" class="iot-qr-manual-input" id="ahpIotQrNodeIdInput" name="node_id" autocomplete="off" autocapitalize="off" spellcheck="false" placeholder="' + esc(manualInput.placeholder || 'Enter node ID') + '">'
      + '<div class="iot-qr-manual-actions">'
      + '<button type="button" class="iot-qr-btn ghost" id="ahpIotQrManualCancelBtn">' + esc(cancelManual) + '</button>'
      + '<button type="button" class="iot-qr-btn primary" id="ahpIotQrManualConnectBtn">' + esc(connectManual) + '</button>'
      + '</div></div></div>'
      + '</div>'
    );
  }

  function applyQrOverlayCopy(modal, overlay) {
    if (!modal || !overlay) return;
    var titleEl = modal.querySelector('#ahpIotQrTitle');
    var subtitleEl = modal.querySelector('#ahpIotQrSubtitle');
    var kickerEl = modal.querySelector('.iot-qr-kicker');
    var header = overlay.header || {};
    var hero = overlay.hero || {};
    var rightAction = header.rightAction || {};
    var manual = overlay.manualEntry || {};
    var manualInput = manual.input || {};
    var manualBtn = modal.querySelector('#ahpIotQrManualBtn');
    var manualTitle = modal.querySelector('#ahpIotQrManualTitle');
    var manualHint = modal.querySelector('#ahpIotQrManualHint');
    var manualInputEl = modal.querySelector('#ahpIotQrNodeIdInput');
    var rightAria = rightAction.ariaLabel || rightAction.title || rightAction.label || 'Enter node ID';
    if (titleEl) titleEl.textContent = header.title || overlay.title || 'Scan Device';
    if (kickerEl) kickerEl.textContent = hero.kicker || 'Add WiFi Node';
    if (subtitleEl) subtitleEl.textContent = hero.subtitle || overlay.subtitle || subtitleEl.textContent;
    if (manualBtn) {
      manualBtn.setAttribute('aria-label', rightAria);
      manualBtn.setAttribute('title', rightAria);
    }
    if (manualTitle) manualTitle.textContent = manual.title || 'Enter Node ID';
    if (manualHint) manualHint.textContent = manual.subtitle || manualHint.textContent;
    if (manualInputEl) {
      if (manualInput.placeholder) manualInputEl.setAttribute('placeholder', manualInput.placeholder);
      var labelEl = modal.querySelector('.iot-qr-manual-label');
      if (labelEl && manualInput.label) labelEl.textContent = manualInput.label;
    }
  }

  function closeManualPrompt() {
    var prompt = document.getElementById('ahpIotQrManualModal');
    var input = document.getElementById('ahpIotQrNodeIdInput');
    if (prompt) {
      prompt.classList.remove('is-open');
      prompt.setAttribute('aria-hidden', 'true');
    }
    if (input) {
      input.classList.remove('is-error');
      input.value = '';
    }
  }

  function openManualPrompt() {
    var prompt = document.getElementById('ahpIotQrManualModal');
    var input = document.getElementById('ahpIotQrNodeIdInput');
    if (!prompt) return;
    prompt.classList.add('is-open');
    prompt.setAttribute('aria-hidden', 'false');
    if (input) {
      input.classList.remove('is-error');
      input.value = '';
      setTimeout(function () { input.focus(); }, 30);
    }
  }

  function submitManualNodeId() {
    var input = document.getElementById('ahpIotQrNodeIdInput');
    var overlay = activeSchema ? resolveOverlay(activeSchema, 'qrScannerModal') : {};
    var raw = input ? String(input.value || '').trim() : '';
    if (!raw) {
      if (input) {
        input.classList.add('is-error');
        input.focus();
      }
      return;
    }
    closeManualPrompt();
    handleQrScan(raw, activeSchema, activeRuntime, overlay || {}, 'manual');
  }

  function ensureQrModal(overlay) {
    overlay = overlay || {};
    var modal = document.getElementById('ahpIotQrModal');
    if (modal && (!modal.querySelector('.iot-qr-sheet') || !modal.querySelector('#ahpIotQrManualBtn'))) {
      modal.remove();
      modal = null;
    }
    if (modal) {
      applyQrOverlayCopy(modal, overlay);
      return modal;
    }
    modal = document.createElement('div');
    modal.id = 'ahpIotQrModal';
    modal.className = 'iot-qr-modal';
    modal.setAttribute('aria-hidden', 'true');
    modal.innerHTML = buildQrModalHtml(overlay);
    document.body.appendChild(modal);
    modal.addEventListener('click', function (e) {
      if (e.target === modal) closeQrModal();
    });
    var closeBtn = document.getElementById('ahpIotQrCloseBtn');
    var cancelBtn = document.getElementById('ahpIotQrCancelBtn');
    var manualBtn = document.getElementById('ahpIotQrManualBtn');
    var manualModal = document.getElementById('ahpIotQrManualModal');
    var manualCancel = document.getElementById('ahpIotQrManualCancelBtn');
    var manualConnect = document.getElementById('ahpIotQrManualConnectBtn');
    var manualInput = document.getElementById('ahpIotQrNodeIdInput');
    if (closeBtn) closeBtn.addEventListener('click', closeQrModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeQrModal);
    if (manualBtn) manualBtn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      openManualPrompt();
    });
    if (manualModal) {
      manualModal.addEventListener('click', function (e) {
        if (e.target === manualModal) closeManualPrompt();
      });
    }
    if (manualCancel) manualCancel.addEventListener('click', closeManualPrompt);
    if (manualConnect) manualConnect.addEventListener('click', submitManualNodeId);
    if (manualInput) {
      manualInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          submitManualNodeId();
        }
      });
    }
    return modal;
  }

  function setQrStep(stepName) {
    var stepsEl = document.getElementById('ahpIotQrSteps');
    if (!stepsEl) return;
    var order = [];
    stepsEl.querySelectorAll('.iot-qr-step').forEach(function (el) {
      order.push(el.getAttribute('data-step') || '');
    });
    var activeIndex = order.indexOf(stepName);
    stepsEl.querySelectorAll('.iot-qr-step').forEach(function (el) {
      var step = el.getAttribute('data-step') || '';
      var idx = order.indexOf(step);
      el.classList.remove('is-active', 'is-done');
      if (idx >= 0 && activeIndex >= 0) {
        if (idx < activeIndex) el.classList.add('is-done');
        if (idx === activeIndex) el.classList.add('is-active');
      }
    });
  }

  function setQrStatus(message, type) {
    var el = document.getElementById('ahpIotQrStatus');
    if (!el) return;
    var textEl = el.querySelector('.iot-qr-status-text');
    if (textEl) textEl.textContent = message || '';
    else el.textContent = message || '';
    el.classList.remove('is-error', 'is-success');
    if (type === 'error') el.classList.add('is-error');
    if (type === 'success') el.classList.add('is-success');
  }

  function stopQrScanner() {
    if (!qrScanner) return Promise.resolve();
    var instance = qrScanner;
    qrScanner = null;
    return instance.stop().catch(function () { return instance.clear(); }).then(function () {
      var reader = document.getElementById('ahpIotQrReader');
      if (reader) reader.innerHTML = '';
    });
  }

  function closeQrModal() {
    var modal = document.getElementById('ahpIotQrModal');
    if (!modal) return;
    closeManualPrompt();
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    qrBusy = false;
    stopQrScanner();
    setQrStep('scan');
    var overlay = activeSchema ? resolveOverlay(activeSchema, 'qrScannerModal') : null;
    var idleMessage = (overlay && overlay.flow && overlay.flow[0] && overlay.flow[0].statusMessage)
      ? overlay.flow[0].statusMessage
      : 'Point your camera at the device QR code…';
    setQrStatus(idleMessage, '');
  }

  function openQrModal(schema, runtime) {
    activeSchema = schema;
    activeRuntime = mergeRuntime(runtime);
    runtime = activeRuntime;
    var overlay = resolveOverlay(schema, 'qrScannerModal') || {};
    var modal = ensureQrModal(overlay);
    ensureQrLibrary(overlay).then(function () {
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      setQrStep('scan');
      setQrStatus((overlay.flow && overlay.flow[0] && overlay.flow[0].statusMessage) || 'Point your camera at the device QR code…', '');
      var reader = document.getElementById('ahpIotQrReader');
      if (!reader || typeof Html5Qrcode !== 'function') {
        setQrStatus('QR scanner is unavailable in this browser.', 'error');
        return;
      }
      reader.innerHTML = '';
      qrScanner = new Html5Qrcode('ahpIotQrReader');
      var scannerCfg = overlay.scanner || {};
      qrScanner.start(
        { facingMode: scannerCfg.facingMode || 'environment' },
        {
          fps: scannerCfg.fps || 10,
          qrbox: scannerCfg.qrbox || { width: 250, height: 250 }
        },
        function (decodedText) { handleQrScan(decodedText, schema, runtime, overlay); },
        function () { /* ignore scan noise */ }
      ).catch(function (err) {
        setQrStatus((err && err.message) ? err.message : 'Could not start camera.', 'error');
      });
    }).catch(function () {
      setQrStatus('Could not load QR scanner library.', 'error');
    });
  }

  function handleQrScan(rawValue, schema, runtime, overlay, source) {
    if (qrBusy) return;
    var nodeId = normalizeQrNodeId(rawValue);
    if (!nodeId) {
      setQrStatus(source === 'manual' ? 'Enter a valid node ID.' : 'Invalid QR code.', 'error');
      return;
    }
    qrBusy = true;
    setQrStep('checkDevice');
    var verifyMessage = 'QR scanned. Verifying device…';
    if (source === 'manual') {
      verifyMessage = (overlay && overlay.manualEntry && overlay.manualEntry.statusMessage)
        ? overlay.manualEntry.statusMessage
        : 'Node ID entered. Verifying device…';
    } else if (overlay && overlay.flow && overlay.flow[1] && overlay.flow[1].statusMessage) {
      verifyMessage = overlay.flow[1].statusMessage;
    }
    setQrStatus(verifyMessage, '');
    stopQrScanner();

    var locOptions = (overlay.location && overlay.location.options) ? overlay.location.options : {
      enableHighAccuracy: true, timeout: 12000, maximumAge: 60000
    };

    getCurrentLocation(locOptions).then(function (loc) {
      return refreshRuntimeAccessToken(runtime).then(function (activeRuntime) {
        if (!activeRuntime.accessToken) {
          throw new Error('No access token found. Sign in again, then retry.');
        }
        var checkDs = schema.dataSources && schema.dataSources.checkDevice;
        var addDs = schema.dataSources && schema.dataSources.addWifiNode;
        var body = {
          node_id: nodeId,
          latitude: loc.latitude,
          longitude: loc.longitude
        };
        return callDataSource(checkDs, activeRuntime, body).then(function (checkResult) {
          if (checkResult && (checkResult.status === 'failure' || checkResult.success === false)) {
            throw new Error(extractApiError(checkResult, 'Device verification failed.'));
          }
          setQrStep('addWifiNode');
          setQrStatus('Verified. Adding WiFi node…', '');
          return callDataSource(addDs, activeRuntime, body, {
            node_id: nodeId,
            latitude: loc.latitude,
            longitude: loc.longitude
          }).then(function (addResult) {
            return {
              scannedNodeId: nodeId,
              addedNodeId: (addResult && addResult.node_id) ? String(addResult.node_id).trim() : nodeId,
              nodeIdAliases: buildNodeIdAliases(nodeId),
              devices: (addResult && Array.isArray(addResult.devices)) ? addResult.devices : null
            };
          });
        });
      }).then(function (result) {
        setQrStatus('Device added successfully.', 'success');
        var closeDelay = 900;
        if (overlay.flow) {
          overlay.flow.forEach(function (step) {
            if (step.step === 'addWifiNode' && step.closeDelayMs) closeDelay = step.closeDelayMs;
          });
        }
        var refreshPromise = (typeof runtime.onRefresh === 'function')
          ? Promise.resolve(runtime.onRefresh({
            addedNodeId: result.addedNodeId || result.scannedNodeId,
            scannedNodeId: result.scannedNodeId,
            nodeIdAliases: result.nodeIdAliases || buildNodeIdAliases(nodeId),
            devices: result.devices,
            force: true
          }))
          : Promise.resolve();
        return refreshPromise.then(function () {
          setTimeout(closeQrModal, closeDelay);
        }).catch(function (refreshErr) {
          setQrStatus(refreshErr && refreshErr.message
            ? refreshErr.message
            : 'Device added. Refreshing list…', 'error');
          qrBusy = false;
        });
      });
    }).catch(function (err) {
      setQrStatus(err && err.message ? err.message : 'Add device failed.', 'error');
      qrBusy = false;
    });
  }

  function bindOnboarding(root, schema, runtime) {
    if (!root || !schema) return;
    var action = resolveHeaderAction(schema);
    if (!action) return;
    var btn = root.querySelector('[data-iot-control="addDeviceButton"]');
    if (!btn) return;
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      openQrModal(schema, runtime);
    });
  }

  function deviceMatchesQuery(device, query) {
    if (!query) return true;
    var p = parseDevice(device);
    var hay = [
      p.productName, p.brandName, p.deviceType, p.nodeId, p.modelName, p.modelNumber, p.status
    ].join(' ').toLowerCase();
    return hay.indexOf(query) !== -1;
  }

  function renderHeader(cfg, deviceCount, onlineCount, schema, searchState) {
    searchState = searchState || {};
    var h = cfg.header || {};
    var eyebrow = h.eyebrow || 'Smart Home';
    var title = h.title || 'My Devices';
    var subtitle = h.subtitle || (deviceCount + ' products · ' + onlineCount + ' online');
    var action = resolveHeaderAction(schema || {});
    var actionHtml = action
      ? '<button type="button" class="iot-header-add-btn" data-iot-control="addDeviceButton" aria-label="' + esc(action.ariaLabel || 'Add device') + '" title="' + esc(action.title || action.ariaLabel || 'Add device') + '">' + ADD_DEVICE_SVG + '</button>'
      : '<span class="iot-header-avatar" aria-hidden="true">' + HEADER_HOME_SVG + '</span>';
    var search = (h.search && typeof h.search === 'object') ? h.search : {};
    var searchValue = searchState.query || '';
    var searchOpen = !!searchState.open;
    return (
      '<header class="iot-phone-header">'
      + '<div class="iot-phone-header-copy">'
      + '<span class="iot-eyebrow">' + esc(eyebrow) + '</span>'
      + '<h2>' + esc(title) + '</h2>'
      + '<p>' + esc(subtitle) + '</p>'
      + '</div>'
      + '<div class="iot-header-actions">'
      + '<button type="button" class="iot-search-btn' + (searchOpen ? ' is-open' : '') + '" data-iot-search-toggle aria-expanded="' + (searchOpen ? 'true' : 'false') + '" aria-label="' + esc(search.ariaLabel || 'Search devices') + '" title="Search devices">'
      + '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>'
      + '</button>'
      + actionHtml
      + '</div>'
      + '</header>'
      + '<form class="iot-device-search' + (searchOpen ? ' is-open' : '') + '" data-iot-search-form role="search">'
      + '<input type="search" data-iot-search-input placeholder="' + esc(search.placeholder || 'Search devices') + '" value="' + esc(searchValue) + '" aria-label="' + esc(search.ariaLabel || 'Search devices by name') + '" autocomplete="off">'
      + '</form>'
    );
  }

  function renderSummaryCard(cfg, schema, deviceCount, onlineCount) {
    var summaryRadius = 24;
    if (schema.components && Array.isArray(schema.components)) {
      schema.components.forEach(function (comp) {
        if (comp && comp.type === 'summaryCard' && comp.style && comp.style.borderRadius != null) {
          summaryRadius = parseInt(comp.style.borderRadius, 10) || summaryRadius;
        }
      });
    }
    var dateLabel = formatSummaryDate(schema);
    var totalLine = deviceCount + ' total';
    return (
      '<div class="iot-stats-card" style="border-radius:' + summaryRadius + 'px">'
      + '<div class="iot-stats-card-left">'
      + '<span class="iot-stats-card-label">' + esc(dateLabel) + ' · ' + esc(totalLine) + '</span>'
      + '<span class="iot-stats-card-title">Connected Devices</span>'
      + '<div class="iot-stats-card-value">' + esc(String(onlineCount)) + ' <span>online</span></div>'
      + '</div>'
      + '<div class="iot-stats-card-art" aria-hidden="true">' + CONNECTION_ART_SVG + '</div>'
      + '</div>'
    );
  }

  function bindCardEvents(root, devices, schema, runtime) {
    if (!root) return;
    runtime = mergeRuntime(runtime);
    root.querySelectorAll('.iot-device-card').forEach(function (card) {
      card.addEventListener('click', function (e) {
        if (e.target.closest('.iot-toggle')) return;
        root.querySelectorAll('.iot-device-card.is-selected').forEach(function (el) {
          el.classList.remove('is-selected');
        });
        card.classList.add('is-selected');
      });
      var toggle = card.querySelector('.iot-toggle');
      if (!toggle) return;
      toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        var idx = parseInt(card.getAttribute('data-device-index'), 10) || 0;
        var device = devices[idx];
        if (!device || device.online === false) return;
        if (toggle.disabled) return;

        var turningOn = !toggle.classList.contains('is-on');
        syncCardPowerState(card, device, turningOn);
        toggle.disabled = true;

        postPowerToggle(schema, device, turningOn, runtime)
          .then(function (data) {
            var nextPower = turningOn;
            if (data && typeof data.power !== 'undefined') {
              nextPower = isPowerOn(data.power);
            }
            syncCardPowerState(card, device, nextPower);
            // Power-only update — do NOT full-refresh devices (that was flipping online/offline).
            if (typeof runtime.onRefresh === 'function') {
              runtime.onRefresh({
                nodeId: device.node_id || device.nodeId,
                power: nextPower,
                powerOnly: true
              });
            }
            if (typeof document !== 'undefined' && document.dispatchEvent) {
              document.dispatchEvent(new CustomEvent('iot:power-click', {
                detail: {
                  node_id: device.node_id || device.nodeId || '',
                  power: nextPower ? 'on' : 'off',
                  command: (data && data.command) ? data.command : (nextPower ? 'on' : 'off')
                }
              }));
            }
          })
          .catch(function (err) {
            syncCardPowerState(card, device, !turningOn);
            if (typeof document !== 'undefined' && document.dispatchEvent) {
              document.dispatchEvent(new CustomEvent('iot:power-error', {
                detail: { message: err && err.message ? err.message : 'Power toggle failed.' }
              }));
            }
          })
          .finally(function () {
            if (device.online !== false) toggle.disabled = false;
          });
      });
    });
  }

  function render(schema, mountEl, runtime) {
    if (!mountEl) return false;
    mergeRuntime(runtime);
    var layout = resolveLayout(schema);
    var allDevices = sortDevicesOnlineFirst(extractDevices(schema));
    var searchQuery = String((mountEl.dataset && mountEl.dataset.iotSearch) || '').trim();
    var searchOpen = searchQuery !== '' || (mountEl.dataset && mountEl.dataset.iotSearchOpen === '1');
    var needle = searchQuery.toLowerCase();
    var devices = needle
      ? allDevices.filter(function (d) { return deviceMatchesQuery(d, needle); })
      : allDevices;
    var searchState = { query: searchQuery, open: searchOpen };
    var summary = layout.summary || {};
    var onlineCount = summary.online != null
      ? summary.online
      : allDevices.filter(function (d) { return d.online !== false; }).length;
    var offlineCount = summary.offline != null
      ? summary.offline
      : Math.max(0, allDevices.length - onlineCount);

    if (layout.theme && layout.theme.background) {
      mountEl.style.background = layout.theme.background;
    } else {
      mountEl.style.background = '#ececec';
    }

    if (!allDevices.length || !devices.length) {
      var emptyTitle = searchQuery ? 'No matching devices' : 'No devices found';
      var emptyCopy = searchQuery
        ? 'Nothing matches that name.'
        : 'No devices returned from the IoT API for this access token.';
      mountEl.innerHTML = (
        '<div class="ahp-iot-sdui-page" style="padding:' + layout.paddingTop + 'px ' + layout.paddingH + 'px ' + layout.paddingBottom + 'px">'
        + renderHeader(layout, allDevices.length, onlineCount, schema, searchState)
        + '<div class="iot-empty-state"><strong>' + emptyTitle + '</strong><br>' + esc(emptyCopy) + '</div>'
        + '</div>'
      );
      mountEl.dataset.iotOnline = String(onlineCount);
      mountEl.dataset.iotOffline = String(offlineCount);
      bindDeviceSearch(mountEl, schema, runtime);
      bindOnboarding(mountEl, schema, runtimeConfig);
      return true;
    }

    var cardStyle = {
      borderRadius: layout.cardBorderRadius,
      minHeight: layout.cardMinHeight
    };

    mountEl.innerHTML = (
      '<div class="ahp-iot-sdui-page" style="padding:' + layout.paddingTop + 'px ' + layout.paddingH + 'px ' + layout.paddingBottom + 'px">'
      + renderHeader(layout, allDevices.length, onlineCount, schema, searchState)
      + renderSummaryCard(layout, schema, devices.length, onlineCount)
      + '<div class="iot-device-grid" role="list" style="grid-template-columns:repeat(' + layout.columns + ',minmax(0,1fr));gap:' + layout.gap + 'px">'
      + devices.map(function (d, i) { return buildCard(d, i, cardStyle); }).join('')
      + '</div>'
      + '</div>'
    );

    mountEl.dataset.iotOnline = String(onlineCount);
    mountEl.dataset.iotOffline = String(offlineCount);
    mountEl.dataset.iotColumns = String(layout.columns);
    bindCardEvents(mountEl, devices, schema, runtimeConfig);
    bindOnboarding(mountEl, schema, runtimeConfig);
    bindDeviceSearch(mountEl, schema, runtime);
    return true;
  }

  function bindDeviceSearch(mountEl, schema, runtime) {
    if (!mountEl) return;
    var toggle = mountEl.querySelector('[data-iot-search-toggle]');
    var form = mountEl.querySelector('[data-iot-search-form]');
    var input = mountEl.querySelector('[data-iot-search-input]');
    if (toggle && form) {
      toggle.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var opening = !form.classList.contains('is-open');
        mountEl.dataset.iotSearchOpen = opening ? '1' : '0';
        if (!opening) mountEl.dataset.iotSearch = '';
        render(schema, mountEl, runtime);
        if (opening) {
          var next = mountEl.querySelector('[data-iot-search-input]');
          if (next) next.focus();
        }
      });
    }
    if (!input) return;
    input.addEventListener('input', function () {
      mountEl.dataset.iotSearch = input.value || '';
      mountEl.dataset.iotSearchOpen = '1';
      var caret = input.selectionStart;
      render(schema, mountEl, runtime);
      var next = mountEl.querySelector('[data-iot-search-input]');
      if (next) {
        next.focus();
        if (caret != null && next.setSelectionRange) next.setSelectionRange(caret, caret);
      }
    });
    input.addEventListener('keydown', function (e) {
      if (e.key !== 'Escape') return;
      mountEl.dataset.iotSearch = '';
      mountEl.dataset.iotSearchOpen = '0';
      render(schema, mountEl, runtime);
    });
  }

  global.AHP_IOT_SDUI = {
    render: render,
    extractDevices: extractDevices,
    resolveLayout: resolveLayout,
    resolveAssetUrl: resolveAssetUrl,
    postPowerToggle: postPowerToggle,
    isPowerOn: isPowerOn
  };
})(typeof window !== 'undefined' ? window : this);
