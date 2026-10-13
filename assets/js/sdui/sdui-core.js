/**
 * Efactor Server-Driven UI core — schema builder, semantic validator, legacy migrator.
 * Browser: window.EfactorSdui
 * Node: module.exports
 */
(function (root, factory) {
  if (typeof module === 'object' && module.exports) {
    module.exports = factory();
  } else {
    root.EfactorSdui = factory();
  }
})(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

  var SCHEMA_VERSION = '1.0.0';
  var DOCUMENT_TYPE = 'serverDrivenUi';
  var ENGINE = 'efactor-sdui';

  var CUSTOM_COMPONENTS = {
    radialGauge: {
      implementation: 'EfactorRadialGauge',
      version: '1.0.0',
      valueType: 'number',
      supportedProperties: ['valueBinding', 'minimum', 'maximum', 'step', 'interactive', 'startAngle', 'sweepAngle', 'activeColor', 'inactiveColor'],
      supportedEvents: ['onChanged', 'onChangeEnd']
    },
    needleGauge: {
      implementation: 'EfactorNeedleGauge',
      version: '1.0.0',
      valueType: 'number',
      supportedProperties: ['valueBinding', 'minimum', 'maximum', 'step', 'interactive', 'startAngle', 'sweepAngle', 'activeColor', 'inactiveColor'],
      supportedEvents: ['onChanged', 'onChangeEnd']
    },
    linearGauge: {
      implementation: 'EfactorLinearGauge',
      version: '1.0.0',
      valueType: 'number',
      supportedProperties: ['valueBinding', 'minimum', 'maximum', 'interactive', 'activeColor', 'inactiveColor'],
      supportedEvents: []
    },
    fireControl: {
      implementation: 'EfactorFireControl',
      version: '1.0.0',
      valueType: 'string',
      supportedProperties: ['valueBinding', 'commands'],
      supportedEvents: ['onCommand']
    }
  };

  var CAPABILITIES = [
    'layout.flex.v1',
    'layout.stack.v1',
    'state.typed.v1',
    'binding.path.v1',
    'action.pipeline.v1',
    'network.rest.v1',
    'component.gauge.radial.v1',
    'component.gauge.needle.v1',
    'component.gauge.linear.v1',
    'component.fire.v1',
    'component.buttonGroup.v1'
  ];

  function isFiniteNumber(value) {
    return typeof value === 'number' && isFinite(value);
  }

  function num(value, fallback) {
    var parsed = typeof value === 'number' ? value : parseFloat(value);
    return isFinite(parsed) ? parsed : fallback;
  }

  function bool(value) {
    return !(value === false || value === 0 || value === '0' || value == null);
  }

  function slug(value, fallback) {
    var id = String(value || '')
      .normalize('NFKD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '');
    return id || fallback || 'device';
  }

  function camelKey(value, fallback) {
    var parts = String(value || '')
      .replace(/[^a-zA-Z0-9]+/g, ' ')
      .trim()
      .split(/\s+/)
      .filter(Boolean);
    if (!parts.length) return fallback || 'parameter';
    return parts.map(function (part, index) {
      var lower = part.toLowerCase();
      if (index === 0) return lower;
      return lower.charAt(0).toUpperCase() + lower.slice(1);
    }).join('');
  }

  function actionIdForControl(controlId) {
    return 'update_' + String(controlId || 'control').replace(/[^a-zA-Z0-9]+/g, '_');
  }

  function toAbsoluteUrl(url, origin) {
    if (!url) return '';
    if (/^https?:\/\//i.test(url)) return url;
    try {
      return new URL(url, origin || 'http://localhost').href;
    } catch (e) {
      return String(url);
    }
  }

  function splitUrl(url, origin) {
    var absolute = toAbsoluteUrl(url, origin);
    try {
      var parsed = new URL(absolute);
      return { baseUrl: parsed.origin, path: parsed.pathname + parsed.search };
    } catch (e) {
      return { baseUrl: origin || 'http://localhost', path: absolute || '/' };
    }
  }

  /** Convert CSS rgba/rgb/hex to #RRGGBB or #AARRGGBB. */
  function normalizeColor(value, fallback) {
    if (value == null || value === '') return fallback || '#000000';
    var raw = String(value).trim();
    if (/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/.test(raw)) {
      if (raw.length === 4) {
        return ('#' + raw[1] + raw[1] + raw[2] + raw[2] + raw[3] + raw[3]).toUpperCase();
      }
      return raw.toUpperCase();
    }
    var rgba = raw.match(/^rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)(?:\s*,\s*([\d.]+))?\s*\)$/i);
    if (rgba) {
      var r = Math.max(0, Math.min(255, Math.round(parseFloat(rgba[1]))));
      var g = Math.max(0, Math.min(255, Math.round(parseFloat(rgba[2]))));
      var b = Math.max(0, Math.min(255, Math.round(parseFloat(rgba[3]))));
      var a = rgba[4] != null ? Math.max(0, Math.min(1, parseFloat(rgba[4]))) : 1;
      var hex = function (n) {
        return ('0' + n.toString(16)).slice(-2).toUpperCase();
      };
      if (a < 1) {
        return '#' + hex(Math.round(a * 255)) + hex(r) + hex(g) + hex(b);
      }
      return '#' + hex(r) + hex(g) + hex(b);
    }
    return fallback || '#000000';
  }

  function isDarkColor(value) {
    var hex = normalizeColor(value, '#FFFFFF').replace('#', '');
    if (hex.length === 8) hex = hex.slice(2);
    if (hex.length !== 6) return false;
    var r = parseInt(hex.slice(0, 2), 16) / 255;
    var g = parseInt(hex.slice(2, 4), 16) / 255;
    var b = parseInt(hex.slice(4, 6), 16) / 255;
    return ((0.2126 * r) + (0.7152 * g) + (0.0722 * b)) < 0.45;
  }

  function themeRef(path) {
    return '{theme.' + path + '}';
  }

  function stateValuePath(key) {
    return 'state.parameters.' + key + '.value';
  }

  function binding(path, mode) {
    return { path: path, mode: mode || 'oneWay' };
  }

  function normalizeOptions(options) {
    var list = Array.isArray(options) ? options : [];
    var seen = {};
    var out = [];
    list.forEach(function (option) {
      if (option == null) return;
      var value = typeof option === 'object' ? option.value : option;
      var label = typeof option === 'object' ? (option.label != null ? option.label : value) : option;
      if (value == null || value === '') return;
      var key = String(value);
      if (seen[key]) return;
      seen[key] = true;
      out.push({ label: String(label), value: key });
    });
    return out;
  }

  function coerceEnumValue(value, options) {
    var opts = normalizeOptions(options);
    if (!opts.length) {
      return { options: [], value: value == null ? '' : String(value), valid: false };
    }
    var asString = value == null || value === '' ? '' : String(value);
    var match = opts.some(function (o) { return o.value === asString; });
    if (!match) {
      asString = opts[0].value;
    }
    return { options: opts, value: asString, valid: true };
  }

  function parseToggle(value) {
    if (typeof value === 'boolean') return value;
    var text = String(value == null ? '' : value).trim().toLowerCase();
    return text === '1' || text === '01' || text === 'true' || text === 'on' || text === 'yes';
  }

  function isRegisteredLike(type, registeredTypes) {
    if (!type) return false;
    if (/fire/i.test(type) || type === 'FireControlPanel' || type === 'fireControl') return true;
    if (type === 'buttonGroup' || type === 'ButtonGroup') return true;
    var map = registeredTypes || {};
    return !!map[type];
  }

  function isFireType(type) {
    return /fire/i.test(String(type || '')) || type === 'FireControlPanel' || type === 'fireControl';
  }

  function isReadOnlyType(type, control) {
    if (control && (control.readOnly === true || (control.component && control.component.readOnly === true))) {
      return true;
    }
    return type === 'value' || type === 'linearGauge';
  }

  function isInteractiveGauge(type, control) {
    if (isReadOnlyType(type, control)) return false;
    if (control && control.interactive === false) return false;
    return type === 'powerGauge' || type === 'needleGauge' || type === 'radialGauge';
  }

  function numericBounds(constraints, defaults) {
    var c = constraints || {};
    var minimum = isFiniteNumber(c.minimum) ? c.minimum : (defaults.minimum != null ? defaults.minimum : 0);
    var maximum = isFiniteNumber(c.maximum) ? c.maximum : (defaults.maximum != null ? defaults.maximum : 100);
    var step = isFiniteNumber(c.step) && c.step > 0 ? c.step : (defaults.step != null ? defaults.step : 1);
    if (maximum < minimum) {
      var swap = minimum;
      minimum = maximum;
      maximum = swap;
    }
    return { minimum: minimum, maximum: maximum, step: step };
  }

  function clampNumber(value, bounds) {
    var v = isFiniteNumber(value) ? value : bounds.minimum;
    if (v < bounds.minimum) v = bounds.minimum;
    if (v > bounds.maximum) v = bounds.maximum;
    return v;
  }

  function buildStateParameter(control, key) {
    var type = control.componentType || control.controlType || 'value';
    var constraints = control.constraints || {};
    var options = normalizeOptions(control.options);
    var initial = control.initialValue;
    if (initial == null && control.component && control.component.initialValue != null) {
      initial = control.component.initialValue;
    }
    if (initial == null) initial = control.value;

    var entry = {
      type: 'string',
      value: initial == null ? '' : String(initial),
      readOnly: isReadOnlyType(type, control)
    };

    if (type === 'switch') {
      entry.type = 'boolean';
      entry.value = parseToggle(initial);
      entry.readOnly = false;
      entry.transport = { encode: { 'false': '00', 'true': '01' } };
    } else if (type === 'button') {
      entry.type = 'boolean';
      entry.value = parseToggle(initial);
      entry.readOnly = false;
    } else if (type === 'slider' || type === 'powerGauge' || type === 'needleGauge' || type === 'radialGauge' || type === 'linearGauge') {
      entry.type = 'number';
      var defaults = type === 'slider' ? { minimum: 0, maximum: 255, step: 1 } : { minimum: 0, maximum: 100, step: 1 };
      var bounds = numericBounds(constraints, defaults);
      var numeric = typeof initial === 'number' ? initial : parseFloat(String(initial == null ? '' : initial).replace(/[^\d.-]/g, ''));
      entry.value = clampNumber(Number.isFinite(numeric) ? numeric : bounds.minimum, bounds);
      entry.constraints = bounds;
      entry.readOnly = !isInteractiveGauge(type, control) && (type === 'linearGauge' || type === 'value' || isReadOnlyType(type, control));
      if (type === 'linearGauge') entry.readOnly = true;
      if (isInteractiveGauge(type, control)) entry.readOnly = false;
    } else if (type === 'dropdown' || type === 'segmented' || type === 'buttonGroup' || (options.length && !isFireType(type))) {
      var coerced = coerceEnumValue(initial, options.length ? options : [{ label: 'Option', value: 'option' }]);
      entry.type = 'enum';
      entry.value = coerced.value;
      entry.enum = coerced.options.map(function (o) { return o.value; });
      entry.options = coerced.options;
      entry.readOnly = false;
    } else if (type === 'textField') {
      entry.type = 'string';
      entry.value = initial == null ? '' : String(initial);
      entry.constraints = {
        minLength: isFiniteNumber(constraints.minLength) ? constraints.minLength : 0,
        maxLength: isFiniteNumber(constraints.maxLength) ? constraints.maxLength : 120
      };
      entry.readOnly = false;
    } else if (isFireType(type)) {
      var fireOpts = options.length ? options : [
        { label: 'START', value: 'START' },
        { label: 'STOP', value: 'STOP' }
      ];
      var fireCoerced = coerceEnumValue(initial, fireOpts);
      entry.type = 'enum';
      entry.value = fireCoerced.value;
      entry.enum = fireCoerced.options.map(function (o) { return o.value; });
      entry.options = fireCoerced.options;
      entry.readOnly = false;
    } else if (type === 'elevatedButton') {
      entry.type = 'string';
      entry.value = initial == null ? 'EXECUTE' : String(initial);
      entry.readOnly = false;
    } else {
      entry.type = 'string';
      entry.value = initial == null ? '' : String(initial);
      entry.readOnly = true;
    }

    var meta = control.meta || {};
    entry.meta = {
      controlId: control.id,
      label: control.label,
      parameterName: control.parameterName || control.parameter || control.label,
      parameterKey: control.parameterKey || key,
      componentType: type,
      pageNo: control.pageNo,
      icon: control.icon || 'gauge',
      actionId: actionIdForControl(control.id),
      commandId: meta.command_id || meta.commandId || control.commandId || null,
      functionCode: meta.function_code || meta.functionCode || control.functionCode || null
    };
    return entry;
  }

  function setStateStep(path, value) {
    return { type: 'setState', path: path, value: value };
  }

  function invokeAction(action, args) {
    return { type: 'invoke', action: action, args: args || {} };
  }

  function writeSteps(actionId, key, valueExpr) {
    return [
      setStateStep(stateValuePath(key), valueExpr),
      invokeAction(actionId, { parameter: key, value: valueExpr })
    ];
  }

  function buildParameterCard(control, key, stateEntry, theme, constants) {
    var type = control.componentType || control.controlType || 'value';
    var valuePath = stateValuePath(key);
    var styles = control.styles || {};
    var actionId = stateEntry.meta.actionId;
    var options = stateEntry.options || normalizeOptions(control.options);
    var bounds = stateEntry.constraints || { minimum: 0, maximum: 100, step: 1 };
    var accent = normalizeColor(styles.accentColor || theme.accentColor, '#2563EB');
    var card = {
      type: 'parameterCard',
      id: 'card_' + key,
      label: control.label,
      icon: control.icon || theme.parameterIcon || 'gauge',
      componentType: type,
      stateKey: key,
      action: actionId,
      visible: control.visible !== false,
      style: {
        backgroundColor: normalizeColor(styles.cardColor || theme.cardColor, '#FFFFFF'),
        borderColor: normalizeColor(styles.borderColor || theme.borderColor, '#BFDBFE'),
        borderWidth: num(theme.borderSize, 1),
        radius: num(theme.borderRadius, 16),
        padding: {
          top: num(theme.cardPadding, 14),
          right: num(theme.cardPadding, 14),
          bottom: num(theme.cardPadding, 14),
          left: num(theme.cardPadding, 14)
        },
        shadow: themeRef('shadows.card'),
        accentColor: accent,
        textColor: normalizeColor(styles.textColor || theme.textColor, '#0F172A'),
        mutedTextColor: normalizeColor(styles.mutedTextColor || theme.mutedTextColor, '#64748B'),
        iconColor: normalizeColor(styles.iconColor || theme.iconColor || theme.accentColor, '#2563EB'),
        statusColor: normalizeColor(styles.statusColor || theme.accentColor, '#2563EB'),
        minHeight: 66
      }
    };

    if (type === 'switch') {
      card.valueText = { type: 'expression', op: 'if', args: [valuePath, 'ON', 'OFF'] };
      card.accessory = {
        type: 'switch',
        valueBinding: binding(valuePath, 'twoWay'),
        activeColor: normalizeColor(styles.accentColor || theme.toggleOnColor || theme.accentColor, '#2563EB'),
        inactiveColor: normalizeColor(theme.toggleOffColor, '#CBD5E1'),
        onChanged: writeSteps(actionId, key, '{event.value}')
      };
      return card;
    }

    if (type === 'slider') {
      var isTimer = String(control.catalogComponentType || '').toLowerCase() === 'autotriggertimer'
        || /auto trigger/.test(String((control.meta && control.meta.ui_type) || ''));
      var sliderBounds = numericBounds(control.constraints || bounds, { minimum: 0, maximum: 255, step: 1 });
      if (isTimer && sliderBounds.maximum <= 1) sliderBounds.maximum = 255;
      card.valueText = { type: 'expression', op: 'formatNumber', args: [valuePath, 0] };
      card.content = {
        type: 'slider',
        valueBinding: binding(valuePath, 'twoWay'),
        minimum: sliderBounds.minimum,
        maximum: sliderBounds.maximum,
        step: sliderBounds.step,
        activeColor: accent,
        inactiveColor: normalizeColor(theme.toggleOffColor || theme.borderColor, '#CBD5E1'),
        onChanged: [setStateStep(valuePath, '{event.value}')],
        onChangeEnd: [invokeAction(actionId, { parameter: key, value: '{event.value}' })]
      };
      if (isTimer) {
        card.content.variant = 'timer';
        card.content.valueSuffix = 's';
        card.content.kicker = 'TIMER';
      }
      return card;
    }

    if (type === 'powerGauge' || type === 'radialGauge') {
      var interactive = isInteractiveGauge(type, control);
      card.valueText = { type: 'expression', op: 'formatNumber', args: [valuePath, 0] };
      card.content = {
        type: 'radialGauge',
        implementation: 'EfactorRadialGauge@1.0.0',
        valueBinding: binding(valuePath, interactive ? 'twoWay' : 'oneWay'),
        minimum: bounds.minimum,
        maximum: bounds.maximum,
        step: bounds.step,
        startAngle: constants.arcStart,
        sweepAngle: constants.arcSweep,
        interactive: interactive,
        activeColor: accent,
        inactiveColor: normalizeColor(theme.toggleOffColor || theme.borderColor, '#CBD5E1')
      };
      if (interactive) {
        card.content.onChanged = [setStateStep(valuePath, '{event.value}')];
        card.content.onChangeEnd = [invokeAction(actionId, { parameter: key, value: '{event.value}' })];
      }
      return card;
    }

    if (type === 'needleGauge') {
      var needleInteractive = isInteractiveGauge(type, control);
      card.valueText = { type: 'expression', op: 'formatNumber', args: [valuePath, 0] };
      card.content = {
        type: 'needleGauge',
        implementation: 'EfactorNeedleGauge@1.0.0',
        valueBinding: binding(valuePath, needleInteractive ? 'twoWay' : 'oneWay'),
        minimum: bounds.minimum,
        maximum: bounds.maximum,
        step: bounds.step,
        startAngle: constants.arcStart,
        sweepAngle: constants.arcSweep,
        interactive: needleInteractive,
        activeColor: accent,
        inactiveColor: normalizeColor(theme.toggleOffColor || theme.borderColor, '#CBD5E1')
      };
      if (needleInteractive) {
        card.content.onChanged = [setStateStep(valuePath, '{event.value}')];
        card.content.onChangeEnd = [invokeAction(actionId, { parameter: key, value: '{event.value}' })];
      }
      return card;
    }

    if (type === 'linearGauge') {
      card.valueText = { type: 'expression', op: 'formatNumber', args: [valuePath, 0] };
      card.content = {
        type: 'linearGauge',
        implementation: 'EfactorLinearGauge@1.0.0',
        valueBinding: binding(valuePath, 'oneWay'),
        minimum: bounds.minimum,
        maximum: bounds.maximum,
        interactive: false,
        activeColor: accent,
        inactiveColor: normalizeColor(theme.toggleOffColor || theme.borderColor, '#CBD5E1')
      };
      return card;
    }

    if (type === 'dropdown') {
      card.content = {
        type: 'dropdown',
        valueBinding: binding(valuePath, 'twoWay'),
        options: options,
        onChanged: writeSteps(actionId, key, '{event.value}')
      };
      return card;
    }

    if (type === 'segmented') {
      var segmentOptions = options.length ? options : normalizeOptions([
        { label: 'Low', value: 'Low' },
        { label: 'Mid', value: 'Mid' },
        { label: 'High', value: 'High' }
      ]);
      card.content = {
        type: 'segmentedControl',
        valueBinding: binding(valuePath, 'twoWay'),
        options: segmentOptions,
        selectedColor: accent,
        onChanged: writeSteps(actionId, key, '{event.value}')
      };
      return card;
    }

    if (type === 'textField') {
      card.content = {
        type: 'textField',
        textBinding: binding(valuePath, 'twoWay'),
        placeholder: control.parameterName || control.label || 'Enter value',
        sendButton: { visible: true, label: 'Send' },
        onChanged: [setStateStep(valuePath, '{event.value}')],
        onSubmitted: [invokeAction(actionId, { parameter: key, value: '{event.value}' })]
      };
      return card;
    }

    if (type === 'button') {
      card.valueText = { type: 'expression', op: 'if', args: [valuePath, 'ON', 'OFF'] };
      card.accessory = {
        type: 'button',
        text: { type: 'expression', op: 'if', args: [valuePath, 'ON', 'OFF'] },
        variant: 'primary',
        backgroundColor: normalizeColor(styles.accentColor || theme.buttonBgColor || theme.accentColor, '#2563EB'),
        foregroundColor: normalizeColor(theme.buttonTextColor, '#FFFFFF'),
        onTap: [
          setStateStep(valuePath, { type: 'expression', op: 'not', args: [valuePath] }),
          invokeAction(actionId, { parameter: key, value: '{event.value}' })
        ]
      };
      return card;
    }

    if (type === 'elevatedButton') {
      card.accessory = {
        type: 'button',
        text: String(control.value || control.label || 'EXECUTE'),
        variant: 'primary',
        backgroundColor: normalizeColor(styles.accentColor || theme.buttonBgColor || theme.accentColor, '#2563EB'),
        foregroundColor: normalizeColor(theme.buttonTextColor, '#FFFFFF'),
        onTap: [invokeAction(actionId, { parameter: key, value: '{event.value}', operation: 'execute' })]
      };
      return card;
    }

    if (isFireType(type)) {
      var commands = (options.length ? options : [
        { label: 'START', value: 'START' },
        { label: 'STOP', value: 'STOP' }
      ]).map(function (option) {
        return {
          label: option.label,
          value: option.value,
          variant: /stop/i.test(option.value) ? 'danger' : 'success',
          onTap: [
            setStateStep(valuePath, option.value),
            invokeAction(actionId, { parameter: key, value: option.value, operation: 'execute' })
          ]
        };
      });
      card.content = {
        type: 'fireControl',
        implementation: 'EfactorFireControl@1.0.0',
        valueBinding: binding(valuePath, 'twoWay'),
        commands: commands,
        onCommand: writeSteps(actionId, key, '{event.value}').concat([
          { type: 'setArgs', args: { operation: 'execute' } }
        ])
      };
      // Simplify onCommand to explicit event value + execute
      card.content.onCommand = [
        setStateStep(valuePath, '{event.value}'),
        invokeAction(actionId, { parameter: key, value: '{event.value}', operation: 'execute' })
      ];
      return card;
    }

    if (isRegisteredLike(type) || type === 'buttonGroup') {
      var groupOptions = options.length ? options : [{ label: 'A', value: 'A' }];
      card.content = {
        type: 'buttonGroup',
        valueBinding: binding(valuePath, 'twoWay'),
        options: groupOptions.map(function (option) {
          return {
            label: option.label,
            value: option.value,
            onTap: [
              setStateStep(valuePath, option.value),
              invokeAction(actionId, { parameter: key, value: option.value })
            ]
          };
        }),
        onSelected: writeSteps(actionId, key, '{event.value}')
      };
      return card;
    }

    card.valueText = { type: 'binding', path: valuePath, mode: 'oneWay' };
    return card;
  }

  /**
   * Build a production SDUI document from a studio DTO.
   * @param {object} input
   */
  function buildDocument(input) {
    input = input || {};
    var theme = input.theme || {};
    var controls = (input.controls || []).filter(function (c) { return c && c.visible !== false; });
    var pagesMeta = input.pages || [];
    var origin = input.origin || 'http://localhost';
    var modelNo = input.modelNo || 'Device';
    var modalId = input.modalId || 0;
    var activeIndex = Math.max(0, num(input.activeIndex, 0));
    var locale = input.locale || 'en-IN';
    var fontFamily = theme.fontFamily || 'Inter';
    var fontAsset = input.fontAsset || ('assets/fonts/' + String(fontFamily).replace(/\s+/g, '') + '-Regular.ttf');
    var schemaDocsUrl = input.schemaDocsUrl || (origin + '/app/Sdui/schema/server-driven-ui.schema.json');
    var constants = {
      arcStart: input.arcStart != null ? input.arcStart : -210,
      arcSweep: input.arcSweep != null ? input.arcSweep : 240
    };
    var headerBg = normalizeColor(theme.topbarColor, '#FFFFFF');
    var headerDark = isDarkColor(headerBg);
    var headerTitleColor = headerDark ? '#FFFFFF' : '#0F172A';
    var headerMutedColor = headerDark ? '#FFFFFFAD' : '#64748B';
    var headerIconColor = normalizeColor(theme.iconColor || theme.accentColor, '#2563EB');

    var pageCount = Math.max(1, pagesMeta.length || 1);
    var maxPageIndex = Math.max(0, pageCount - 1);
    activeIndex = Math.min(activeIndex, maxPageIndex);

    var uniqueKeys = {};
    var controlKeyMap = {};
    controls.forEach(function (control, index) {
      var preferred = String(control.parameterKey || '').trim();
      if (!preferred) {
        preferred = String(control.id || control.parameterName || control.parameter || control.label || '')
          .replace(/[^a-zA-Z0-9]+/g, '_')
          .toLowerCase()
          .replace(/^_+|_+$/g, '');
      }
      if (!preferred) preferred = 'parameter' + (index + 1);
      var key = preferred;
      var suffix = 2;
      while (uniqueKeys[key] && uniqueKeys[key] !== control.id) {
        key = preferred + '_' + suffix;
        suffix += 1;
      }
      uniqueKeys[key] = control.id;
      controlKeyMap[control.id] = key;
    });

    var stateParameters = {};
    controls.forEach(function (control) {
      var key = controlKeyMap[control.id];
      stateParameters[key] = buildStateParameter(control, key);
    });

    var commandUrl = splitUrl(input.parametersApiUrl || input.schemaApiUrl || '', origin);
    var saveUrl = splitUrl(input.saveThemeUrl || '', origin);
    var infoUrl = splitUrl(input.deviceInfoUrl || '', origin);

    var actions = {
      previousPage: {
        type: 'pager',
        operation: 'previous',
        path: 'state.activePage.value',
        progressPath: 'state.pageProgress.value'
      },
      nextPage: {
        type: 'pager',
        operation: 'next',
        path: 'state.activePage.value',
        progressPath: 'state.pageProgress.value'
      },
      selectPage: {
        type: 'pager',
        operation: 'select',
        path: 'state.activePage.value',
        progressPath: 'state.pageProgress.value',
        index: '{args.index}'
      },
      writeParameter: {
        type: 'sequence',
        steps: [
          { type: 'validate', path: 'state.parameters.{args.parameter}' },
          setStateStep('state.error.value', null),
          setStateStep('state.saving.value', true),
          {
            type: 'request',
            dataSource: 'deviceCommand',
            args: {
              parameter: '{args.parameter}',
              operation: '{args.operation|write}',
              value: '{args.value}',
              commandId: '{state.parameters.{args.parameter}.meta.commandId}',
              functionCode: '{state.parameters.{args.parameter}.meta.functionCode}'
            },
            resultAs: 'commandResult'
          }
        ],
        finally: [setStateStep('state.saving.value', false)],
        onSuccess: [],
        onError: [
          setStateStep('state.error.value', '{error.message}'),
          { type: 'toast', variant: 'error', message: '{error.message}' }
        ]
      },
      executeCommand: {
        type: 'sequence',
        steps: [
          setStateStep('state.saving.value', true),
          {
            type: 'request',
            dataSource: 'deviceCommand',
            args: {
              parameter: '{args.parameter}',
              operation: 'execute',
              value: '{args.value}',
              commandId: '{state.parameters.{args.parameter}.meta.commandId}',
              functionCode: '{state.parameters.{args.parameter}.meta.functionCode}'
            },
            resultAs: 'commandResult'
          }
        ],
        finally: [setStateStep('state.saving.value', false)],
        onSuccess: [{ type: 'toast', variant: 'success', message: 'Command sent' }],
        onError: [
          setStateStep('state.error.value', '{error.message}'),
          { type: 'toast', variant: 'error', message: '{error.message}' }
        ]
      },
      openDeviceInfo: { type: 'navigate', route: 'deviceInfo' },
      openSettings: { type: 'navigate', route: 'settings' }
    };

    // Per-control executable actions (explicitly invoked from events)
    Object.keys(stateParameters).forEach(function (key) {
      var param = stateParameters[key];
      var actionId = param.meta.actionId;
      var readOnly = !!param.readOnly;
      if (readOnly) return;
      var operationDefault = (param.meta.componentType === 'elevatedButton' || isFireType(param.meta.componentType))
        ? 'execute'
        : 'write';
      actions[actionId] = {
        type: 'sequence',
        description: 'Write or execute ' + key,
        steps: [
          {
            type: 'invoke',
            action: operationDefault === 'execute' ? 'executeCommand' : 'writeParameter',
            args: {
              parameter: key,
              value: '{args.value}',
              operation: '{args.operation|' + operationDefault + '}'
            }
          }
        ]
      };
    });

    var usedIcons = {};
    (input.usedIconNames || []).forEach(function (name) { usedIcons[name] = true; });
    controls.forEach(function (c) {
      if (c.icon) usedIcons[c.icon] = true;
    });
    ['gauge', 'info', 'settings', 'chevron-left', 'chevron-right'].forEach(function (name) {
      usedIcons[name] = true;
    });
    var iconAssets = {};
    Object.keys(usedIcons).forEach(function (name) {
      iconAssets[name] = { type: 'asset', source: 'assets/icons/' + name + '.svg' };
    });

    function buildPageHero(page, pageIndex, pageControls) {
      var pageTitle = (theme.headerText && String(theme.headerText).trim())
        ? String(theme.headerText).trim()
        : (page.title || ('Page ' + page.pageNo));
      return {
        type: 'heroCard',
        id: 'hero_' + (page.id || pageIndex),
        visible: bool(theme.heroVisible),
        style: {
          minHeight: 112,
          padding: { top: 18, right: 18, bottom: 18, left: 18 },
          margin: { top: 0, right: 0, bottom: 14, left: 0 },
          borderRadius: num(theme.borderRadius, 16) + 6,
          backgroundColor: normalizeColor(theme.topbarColor, '#FFFFFF'),
          gradient: {
            type: 'linear',
            angle: 145,
            colors: [
              normalizeColor(theme.topbarColor, '#FFFFFF'),
              normalizeColor(theme.secondaryColor, '#94A3B8')
            ]
          },
          shadow: themeRef('shadows.card')
        },
        kicker: { icon: theme.iconStyle || theme.parameterIcon || 'gauge', text: 'Live Control Page', color: '#FFFFFFB8' },
        title: { text: pageTitle, color: '#FFFFFF', style: themeRef('typography.title') },
        subtitle: {
          text: pageControls.length + ' controls linked to Page ' + page.pageNo,
          color: '#FFFFFFAD'
        },
        badge: { value: String(pageIndex + 1), label: 'Page', backgroundColor: '#FFFFFF1F', color: '#FFFFFF' }
      };
    }

    function buildNameCard(page, pageIndex) {
      return {
        type: 'infoCard',
        id: 'name_card_' + (page.id || pageIndex),
        visible: bool(theme.nameCardVisible),
        style: {
          backgroundColor: normalizeColor(theme.cardColor, '#FFFFFF'),
          borderColor: normalizeColor(theme.borderColor, '#BFDBFE'),
          borderWidth: num(theme.borderSize, 1),
          radius: num(theme.borderRadius, 16),
          padding: {
            top: num(theme.cardPadding, 14),
            right: num(theme.cardPadding, 14),
            bottom: num(theme.cardPadding, 14),
            left: num(theme.cardPadding, 14)
          },
          shadow: themeRef('shadows.card'),
          margin: { top: 0, right: 0, bottom: num(theme.cardSpacing, 12), left: 0 }
        },
        label: { text: 'name', color: normalizeColor(theme.mutedTextColor, '#64748B'), style: themeRef('typography.label') },
        value: {
          text: page.title || ('Page ' + page.pageNo),
          color: normalizeColor(theme.textColor, '#0F172A'),
          style: themeRef('typography.value')
        },
        action: {
          type: 'button',
          text: 'Edit',
          variant: 'text',
          color: normalizeColor(theme.accentColor, '#2563EB'),
          onTap: [{ type: 'invoke', action: 'openSettings' }]
        }
      };
    }

    var routePages = (pagesMeta.length ? pagesMeta : [{ id: 'page-1', pageNo: '1', title: 'Controls' }]).map(function (page, pageIndex) {
      var pageControls = controls.filter(function (control) {
        return String(control.pageNo) === String(page.pageNo);
      });
      var children = [];
      if (bool(theme.heroVisible)) children.push(buildPageHero(page, pageIndex, pageControls));
      if (bool(theme.nameCardVisible)) children.push(buildNameCard(page, pageIndex));
      pageControls.forEach(function (control) {
        var key = controlKeyMap[control.id];
        children.push(buildParameterCard(control, key, stateParameters[key], theme, constants));
      });
      if (!children.length) {
        children.push({
          type: 'text',
          text: 'No UI parameters on this page.',
          style: themeRef('typography.body'),
          color: themeRef('colors.muted')
        });
      }
      return {
        id: page.id || ('page-' + (page.pageNo || pageIndex + 1)),
        pageNo: page.pageNo,
        title: page.title,
        type: 'scrollView',
        padding: {
          top: num(theme.pagePadding, 16),
          right: num(theme.pagePadding, 16),
          bottom: num(theme.pagePadding, 16),
          left: num(theme.pagePadding, 16)
        },
        gap: num(theme.cardSpacing, 12),
        children: children
      };
    });

    var firstPage = pagesMeta[activeIndex] || pagesMeta[0] || routePages[0];

    var document = {
      $schema: schemaDocsUrl,
      schemaVersion: SCHEMA_VERSION,
      documentType: DOCUMENT_TYPE,
      id: slug(modelNo, 'efactor') + '-control',
      revision: Math.floor(Date.now() / 1000),
      generatedAt: new Date().toISOString(),
      rendererContract: {
        engine: ENGINE,
        minimumVersion: '1.0.0',
        strict: true,
        unknownComponentPolicy: 'errorPlaceholder',
        unknownPropertyPolicy: 'rejectDocument',
        capabilities: CAPABILITIES.slice(),
        customComponents: JSON.parse(JSON.stringify(CUSTOM_COMPONENTS))
      },
      app: {
        title: modelNo + ' Control',
        modelNo: modelNo,
        modalId: modalId,
        locale: locale,
        initialRoute: 'device'
      },
      theme: {
        mode: theme.themeMode || 'light',
        backgroundType: theme.backgroundType || 'gradient',
        fontFamilies: { body: { family: fontFamily, asset: fontAsset } },
        colors: {
          background: normalizeColor(theme.backgroundColor, '#EFF6FF'),
          backgroundEnd: normalizeColor(theme.gradientColor || theme.shellColor, '#DBEAFE'),
          surface: normalizeColor(theme.cardColor, '#FFFFFF'),
          shell: normalizeColor(theme.shellColor || theme.cardColor, '#FFFFFF'),
          text: normalizeColor(theme.textColor, '#0F172A'),
          muted: normalizeColor(theme.mutedTextColor, '#64748B'),
          primary: normalizeColor(theme.accentColor, '#2563EB'),
          primaryDark: normalizeColor(theme.topbarColor, '#FFFFFF'),
          secondary: normalizeColor(theme.secondaryColor, '#94A3B8'),
          border: normalizeColor(theme.borderColor, '#BFDBFE'),
          success: normalizeColor(theme.successColor, '#10B981'),
          warning: normalizeColor(theme.warningColor, '#F59E0B'),
          danger: normalizeColor(theme.errorColor, '#DC2626'),
          disabled: normalizeColor(theme.toggleOffColor, '#CBD5E1'),
          onPrimary: normalizeColor(theme.buttonTextColor, '#FFFFFF'),
          toggleOn: normalizeColor(theme.toggleOnColor || theme.accentColor, '#2563EB'),
          toggleOff: normalizeColor(theme.toggleOffColor, '#CBD5E1'),
          icon: normalizeColor(theme.iconColor || theme.accentColor, '#2563EB'),
          buttonBg: normalizeColor(theme.buttonBgColor || theme.accentColor, '#2563EB'),
          buttonText: normalizeColor(theme.buttonTextColor, '#FFFFFF')
        },
        spacing: {
          xs: 4, sm: 8, md: 12,
          lg: num(theme.cardPadding, 16),
          xl: num(theme.pagePadding, 24),
          pagePadding: num(theme.pagePadding, 16),
          cardPadding: num(theme.cardPadding, 14),
          cardGap: num(theme.cardSpacing, 12),
          cardMargin: num(theme.cardMargin, 0)
        },
        radii: {
          sm: 8, md: 12,
          lg: num(theme.borderRadius, 16),
          pill: 999,
          button: num(theme.buttonRadius, 14)
        },
        typography: {
          body: {
            fontFamily: 'body',
            fontSize: num(theme.fontSize, 15),
            fontWeight: num(theme.fontWeight, 500),
            letterSpacing: num(theme.letterSpacing, -0.2),
            lineHeight: num(theme.lineHeight, 1.5),
            textAlign: theme.textAlign || 'left'
          },
          label: { fontFamily: 'body', fontSize: 11, fontWeight: 800, lineHeight: 1.2 },
          value: { fontFamily: 'body', fontSize: 16, fontWeight: 700, lineHeight: 1.25 },
          title: { fontFamily: 'body', fontSize: 20, fontWeight: 800, lineHeight: 1.2 }
        },
        shadows: {
          card: {
            color: '#14123246',
            offsetX: 0,
            offsetY: 12,
            blur: 28,
            spread: 0,
            strength: num(theme.shadowStrength, 42)
          }
        },
        effects: { cardBlur: num(theme.cardBlur, 0) },
        visibility: {
          brandVisible: bool(theme.brandVisible),
          heroVisible: bool(theme.heroVisible),
          nameCardVisible: bool(theme.nameCardVisible),
          pageNavigatorVisible: bool(theme.pageNavigatorVisible),
          showHeaderIcons: bool(theme.showHeaderIcons),
          showStatus: bool(theme.showStatus)
        },
        layout: {
          headerHeight: num(theme.headerHeight, 72),
          headerAlignment: theme.headerAlignment || 'left',
          headerText: theme.headerText || ''
        }
      },
      assets: { icons: iconAssets },
      state: {
        activePage: {
          type: 'integer',
          value: activeIndex,
          constraints: { minimum: 0, maximum: maxPageIndex }
        },
        activePageTitle: {
          type: 'string',
          value: theme.headerText || (firstPage ? firstPage.title : modelNo)
        },
        activePageSubtitle: {
          type: 'string',
          value: firstPage ? ('Page ' + firstPage.pageNo + '.Operation') : 'No pages'
        },
        pageProgress: {
          type: 'number',
          value: pageCount ? Math.round(((activeIndex + 1) / pageCount) * 10000) / 100 : 0,
          constraints: { minimum: 0, maximum: 100 }
        },
        online: { type: 'boolean', value: true },
        saving: { type: 'boolean', value: false },
        error: { type: 'null', value: null },
        parameters: stateParameters
      },
      dataSources: {
        deviceCommand: {
          type: 'rest',
          baseUrl: commandUrl.baseUrl,
          path: commandUrl.path || ('/api/device-ui-schema/' + encodeURIComponent(modelNo) + '/parameters'),
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: 'Bearer {secure.authToken}'
          },
          timeoutMs: 10000,
          body: {
            modelNo: '{app.modelNo}',
            modalId: '{app.modalId}',
            parameter: '{args.parameter}',
            operation: '{args.operation}',
            value: '{args.value}',
            commandId: '{args.commandId}',
            functionCode: '{args.functionCode}'
          },
          response: {
            successWhen: { type: 'expression', op: 'and', args: [
              { type: 'expression', op: 'gte', args: ['response.status', 200] },
              { type: 'expression', op: 'lt', args: ['response.status', 300] }
            ]},
            dataPath: 'response.body.data',
            errorPath: 'response.body.message'
          },
          retry: {
            maximumAttempts: 2,
            backoffMs: 500,
            retryOnStatus: [408, 429, 500, 502, 503, 504]
          }
        },
        saveConfiguration: {
          type: 'rest',
          baseUrl: saveUrl.baseUrl,
          path: saveUrl.path || '/items/save_view_page_ui_theme',
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: 'Bearer {secure.authToken}'
          },
          timeoutMs: 10000,
          body: {
            modalId: '{app.modalId}',
            modelNo: '{app.modelNo}',
            parameters: '{state.parameters}',
            theme: '{theme}'
          },
          response: {
            successWhen: { type: 'expression', op: 'and', args: [
              { type: 'expression', op: 'gte', args: ['response.status', 200] },
              { type: 'expression', op: 'lt', args: ['response.status', 300] }
            ]},
            errorPath: 'response.body.message'
          },
          retry: {
            maximumAttempts: 2,
            backoffMs: 500,
            retryOnStatus: [408, 429, 500, 502, 503, 504]
          }
        },
        deviceInfo: {
          type: 'rest',
          baseUrl: infoUrl.baseUrl,
          path: infoUrl.path || ('/api/device-ui-info/' + encodeURIComponent(modelNo)),
          method: 'GET',
          headers: { Authorization: 'Bearer {secure.authToken}' },
          timeoutMs: 10000,
          response: {
            successWhen: { type: 'expression', op: 'and', args: [
              { type: 'expression', op: 'gte', args: ['response.status', 200] },
              { type: 'expression', op: 'lt', args: ['response.status', 300] }
            ]},
            dataPath: 'response.body',
            errorPath: 'response.body.message'
          },
          retry: {
            maximumAttempts: 2,
            backoffMs: 500,
            retryOnStatus: [408, 429, 500, 502, 503, 504]
          }
        }
      },
      actions: actions,
      routes: {
        device: {
          screen: {
            type: 'scaffold',
            id: 'device-screen',
            background: (theme.backgroundType === 'gradient')
              ? {
                  type: 'linearGradient',
                  angle: 180,
                  colors: [themeRef('colors.background'), themeRef('colors.backgroundEnd')]
                }
              : themeRef('colors.background'),
            safeArea: true,
            header: {
              type: 'appBar',
              height: num(theme.headerHeight, 72),
              backgroundColor: headerBg,
              gradient: {
                type: 'linear',
                angle: 180,
                colors: [headerBg, headerBg]
              },
              alignment: theme.headerAlignment || 'left',
              title: {
                type: 'text',
                textBinding: binding('state.activePageTitle.value', 'oneWay'),
                text: theme.headerText || (firstPage ? firstPage.title : modelNo),
                style: themeRef('typography.title'),
                color: headerTitleColor
              },
              subtitle: {
                type: 'column',
                children: [
                  { type: 'text', text: modelNo, color: headerMutedColor },
                  {
                    type: 'text',
                    textBinding: binding('state.activePageSubtitle.value', 'oneWay'),
                    text: firstPage ? ('Page ' + firstPage.pageNo + '.Operation') : 'No pages',
                    color: headerMutedColor
                  }
                ]
              },
              leading: {
                type: 'iconButton',
                icon: 'chevron-left',
                semanticLabel: 'Back',
                backgroundColor: '#FFFFFF',
                color: '#0F172A',
                onTap: [{ type: 'navigateBack' }]
              },
              actions: bool(theme.showHeaderIcons) ? [
                {
                  type: 'iconButton',
                  icon: 'info',
                  semanticLabel: 'Device information',
                  backgroundColor: '#00000000',
                  color: headerIconColor,
                  onTap: [{ type: 'invoke', action: 'openDeviceInfo' }]
                },
                {
                  type: 'iconButton',
                  icon: 'settings',
                  semanticLabel: 'Settings',
                  backgroundColor: '#00000000',
                  color: headerIconColor,
                  onTap: [{ type: 'invoke', action: 'openSettings' }]
                }
              ] : []
            },
            body: {
              type: 'column',
              children: [
                bool(theme.showStatus) ? {
                  type: 'statusBadge',
                  text: { type: 'expression', op: 'if', args: ['state.online.value', 'Online', 'Offline'] },
                  color: { type: 'expression', op: 'if', args: ['state.online.value', themeRef('colors.success'), themeRef('colors.danger')] }
                } : null,
                {
                  type: 'pageView',
                  indexBinding: binding('state.activePage.value', 'twoWay'),
                  physics: 'page',
                  children: routePages
                },
                {
                  type: 'pageIndicator',
                  visible: bool(theme.pageNavigatorVisible),
                  count: pageCount,
                  indexBinding: binding('state.activePage.value', 'oneWay'),
                  activeColor: themeRef('colors.primary'),
                  inactiveColor: themeRef('colors.disabled'),
                  onSelect: [{ type: 'invoke', action: 'selectPage', args: { index: '{event.index}' } }]
                },
                {
                  type: 'row',
                  visible: bool(theme.pageNavigatorVisible),
                  padding: { top: 16, right: 16, bottom: 16, left: 16 },
                  gap: 12,
                  mainAxisAlignment: 'spaceBetween',
                  children: [
                    {
                      type: 'iconButton',
                      icon: 'chevron-left',
                      backgroundColor: normalizeColor(theme.topbarColor, '#FFFFFF'),
                      color: '#FFFFFF',
                      enabled: { type: 'expression', op: 'gt', args: ['state.activePage.value', 0] },
                      onTap: [{ type: 'invoke', action: 'previousPage' }]
                    },
                    {
                      type: 'iconButton',
                      icon: 'chevron-right',
                      backgroundColor: normalizeColor(theme.topbarColor, '#FFFFFF'),
                      color: '#FFFFFF',
                      enabled: {
                        type: 'expression',
                        op: 'lt',
                        args: ['state.activePage.value', maxPageIndex]
                      },
                      onTap: [{ type: 'invoke', action: 'nextPage' }]
                    }
                  ]
                }
              ].filter(Boolean)
            }
          }
        },
        deviceInfo: {
          screen: {
            type: 'scaffold',
            safeArea: true,
            header: {
              type: 'appBar',
              title: { type: 'text', text: 'Device information' },
              leading: {
                type: 'iconButton',
                icon: 'chevron-left',
                onTap: [{ type: 'navigateBack' }]
              }
            },
            body: {
              type: 'scrollView',
              padding: { top: 16, right: 16, bottom: 16, left: 16 },
              children: [
                {
                  type: 'asyncContent',
                  dataSource: 'deviceInfo',
                  loading: { type: 'loader' },
                  error: { type: 'errorPlaceholder', message: '{error.message}' },
                  builder: {
                    type: 'column',
                    gap: 12,
                    children: [
                      { type: 'text', text: '{result.name}', style: themeRef('typography.title') },
                      { type: 'text', text: '{result.model}', style: themeRef('typography.body') },
                      { type: 'text', text: '{result.description}', style: themeRef('typography.body'), color: themeRef('colors.muted') }
                    ]
                  }
                }
              ]
            }
          }
        },
        settings: {
          screen: {
            type: 'scaffold',
            safeArea: true,
            header: {
              type: 'appBar',
              title: { type: 'text', text: 'Settings' },
              leading: {
                type: 'iconButton',
                icon: 'chevron-left',
                onTap: [{ type: 'navigateBack' }]
              }
            },
            body: {
              type: 'scrollView',
              padding: { top: 16, right: 16, bottom: 16, left: 16 },
              children: [
                { type: 'text', text: 'Theme and control order are managed in Design Studio.', style: themeRef('typography.body') }
              ]
            }
          }
        }
      },
      observability: {
        analytics: [{ event: 'screen_view', route: '{runtime.route}' }],
        redactPaths: ['secure.authToken'],
        logLevel: 'error'
      }
    };

    return document;
  }

  function collectComponentIds(node, ids, errors) {
    if (!node || typeof node !== 'object') return;
    if (Array.isArray(node)) {
      node.forEach(function (child) { collectComponentIds(child, ids, errors); });
      return;
    }
    if (node.id) {
      if (ids[node.id]) {
        errors.push('Duplicate component id: ' + node.id);
      } else {
        ids[node.id] = true;
      }
    }
    Object.keys(node).forEach(function (key) {
      if (key === 'id') return;
      var value = node[key];
      if (value && typeof value === 'object') collectComponentIds(value, ids, errors);
    });
  }

  function walkInvokes(node, actions, errors, stack) {
    if (!node || typeof node !== 'object') return;
    if (Array.isArray(node)) {
      node.forEach(function (child) { walkInvokes(child, actions, errors, stack); });
      return;
    }
    if (node.type === 'invoke' && node.action) {
      if (!actions[node.action]) {
        errors.push('Invoked action does not exist: ' + node.action);
      } else if (stack.indexOf(node.action) !== -1) {
        errors.push('Circular action invocation: ' + stack.concat([node.action]).join(' -> '));
      } else {
        var next = stack.concat([node.action]);
        var def = actions[node.action];
        if (def && def.steps) walkInvokes(def.steps, actions, errors, next);
        if (def && def.type === 'invoke') walkInvokes(def, actions, errors, next);
      }
    }
    Object.keys(node).forEach(function (key) {
      if (key === 'action' && node.type === 'invoke') return;
      var value = node[key];
      if (value && typeof value === 'object') walkInvokes(value, actions, errors, stack);
    });
  }

  function pathExists(doc, path) {
    if (!path || typeof path !== 'string') return false;
    if (path.indexOf('{') !== -1) return true; // dynamic
    var parts = path.replace(/^state\./, 'state.').split('.');
    var cursor = doc;
    // Allow state.* absolute from document root
    for (var i = 0; i < parts.length; i++) {
      if (cursor == null || typeof cursor !== 'object') return false;
      if (!Object.prototype.hasOwnProperty.call(cursor, parts[i])) return false;
      cursor = cursor[parts[i]];
    }
    return true;
  }

  function collectBindings(node, bindings) {
    if (!node || typeof node !== 'object') return;
    if (Array.isArray(node)) {
      node.forEach(function (child) { collectBindings(child, bindings); });
      return;
    }
    if (node.path && (node.mode || node.valueBinding || node.textBinding || node.indexBinding)) {
      bindings.push(node);
    }
    ['valueBinding', 'textBinding', 'indexBinding'].forEach(function (key) {
      if (node[key] && node[key].path) bindings.push(node[key]);
    });
    Object.keys(node).forEach(function (key) {
      var value = node[key];
      if (value && typeof value === 'object') collectBindings(value, bindings);
    });
  }

  function validateSemantic(doc, options) {
    options = options || {};
    var errors = [];
    var warnings = [];
    if (!doc || typeof doc !== 'object') {
      return { valid: false, errors: ['Document is not an object'], warnings: [] };
    }

    var required = ['schemaVersion', 'documentType', 'app', 'theme', 'state', 'dataSources', 'actions', 'routes', 'rendererContract'];
    required.forEach(function (key) {
      if (doc[key] == null) errors.push('Missing required top-level property: ' + key);
    });

    if (doc.documentType !== DOCUMENT_TYPE) {
      errors.push('documentType must be serverDrivenUi');
    }

    if (!doc.rendererContract || doc.rendererContract.engine !== ENGINE) {
      errors.push('rendererContract.engine must be efactor-sdui');
    }

    var registered = options.registeredImplementations || {};
    var custom = (doc.rendererContract && doc.rendererContract.customComponents) || {};
    Object.keys(custom).forEach(function (type) {
      var impl = custom[type].implementation;
      var version = custom[type].version;
      if (!impl || !version) {
        errors.push('customComponents.' + type + ' requires implementation and version');
      }
      if (options.requireNativeRegistration && !registered[impl]) {
        errors.push('Custom component implementation not registered: ' + impl);
      }
    });

    var params = (doc.state && doc.state.parameters) || {};
    Object.keys(params).forEach(function (key) {
      var param = params[key];
      if (!param || !param.type) {
        errors.push('state.parameters.' + key + ' missing type');
        return;
      }
      if (param.type === 'enum' || param.enum) {
        var enumValues = param.enum || (param.options || []).map(function (o) { return o.value; });
        if (!enumValues || !enumValues.length) {
          errors.push('state.parameters.' + key + ' enum has empty options');
        } else if (enumValues.indexOf(String(param.value)) === -1) {
          errors.push('state.parameters.' + key + ' value not in enum options');
        }
        var seen = {};
        enumValues.forEach(function (v) {
          if (seen[v]) errors.push('state.parameters.' + key + ' duplicate option value: ' + v);
          seen[v] = true;
        });
      }
      if (param.type === 'number' || param.type === 'integer') {
        var c = param.constraints || {};
        if (!isFiniteNumber(c.minimum) || !isFiniteNumber(c.maximum) || !isFiniteNumber(c.step)) {
          errors.push('state.parameters.' + key + ' numeric constraints incomplete');
        } else {
          if (param.value < c.minimum || param.value > c.maximum) {
            errors.push('state.parameters.' + key + ' value outside min/max');
          }
        }
      }
      if (param.type === 'boolean' && param.constraints) {
        errors.push('state.parameters.' + key + ' boolean must not have numeric constraints');
      }
      if (param.readOnly) {
        // checked later against write events in routes
      }
    });

    var bindings = [];
    collectBindings(doc.routes, bindings);
    bindings.forEach(function (b) {
      if (!b.path || String(b.path).indexOf('state.') !== 0) {
        errors.push('Binding path must start with state.*: ' + (b.path || ''));
      } else if (!pathExists(doc, b.path) && b.path.indexOf('{') === -1) {
        // pathExists for .value leaves: state.parameters.x.value — walk to value key
        var parentPath = b.path.replace(/\.value$/, '');
        if (!pathExists(doc, parentPath) && !pathExists(doc, b.path)) {
          errors.push('Binding path does not exist: ' + b.path);
        }
      }
      if (['oneWay', 'twoWay', 'event'].indexOf(b.mode) === -1) {
        errors.push('Invalid binding mode: ' + b.mode);
      }
    });

    var dataSources = doc.dataSources || {};
    function checkDataSourceRefs(node) {
      if (!node || typeof node !== 'object') return;
      if (Array.isArray(node)) {
        node.forEach(checkDataSourceRefs);
        return;
      }
      if (node.dataSource && !dataSources[node.dataSource]) {
        errors.push('Referenced dataSource missing: ' + node.dataSource);
      }
      Object.keys(node).forEach(function (k) {
        if (node[k] && typeof node[k] === 'object') checkDataSourceRefs(node[k]);
      });
    }
    checkDataSourceRefs(doc.actions);
    checkDataSourceRefs(doc.routes);

    Object.keys(dataSources).forEach(function (name) {
      var ds = dataSources[name];
      if (!ds.type || !ds.method || !ds.path) {
        errors.push('dataSources.' + name + ' incomplete (type/method/path)');
      }
      if (!ds.response || !ds.retry) {
        warnings.push('dataSources.' + name + ' should define response and retry');
      }
    });

    var ids = {};
    collectComponentIds(doc.routes, ids, errors);
    walkInvokes(doc.routes, doc.actions || {}, errors, []);
    walkInvokes(doc.actions || {}, doc.actions || {}, errors, []);

    // Assets
    var icons = (doc.assets && doc.assets.icons) || {};
    function checkIcons(node) {
      if (!node || typeof node !== 'object') return;
      if (Array.isArray(node)) {
        node.forEach(checkIcons);
        return;
      }
      if (typeof node.icon === 'string' && !icons[node.icon]) {
        warnings.push('Asset icon missing: ' + node.icon);
      }
      Object.keys(node).forEach(function (k) {
        if (node[k] && typeof node[k] === 'object') checkIcons(node[k]);
      });
    }
    checkIcons(doc.routes);

    // Read-only parameters must not have write events
    function findWriteOnReadOnly(node) {
      if (!node || typeof node !== 'object') return;
      if (Array.isArray(node)) {
        node.forEach(findWriteOnReadOnly);
        return;
      }
      if (node.type === 'parameterCard' && node.stateKey) {
        var param = params[node.stateKey];
        if (param && param.readOnly) {
          var content = node.content || node.accessory || {};
          ['onChanged', 'onChangeEnd', 'onTap', 'onSubmitted', 'onSelected', 'onCommand'].forEach(function (evt) {
            if (content[evt]) {
              var hasWrite = JSON.stringify(content[evt]).indexOf('writeParameter') !== -1
                || JSON.stringify(content[evt]).indexOf('executeCommand') !== -1
                || JSON.stringify(content[evt]).indexOf('update_') !== -1;
              if (hasWrite && content.interactive !== true) {
                errors.push('Read-only parameter ' + node.stateKey + ' has write event ' + evt);
              }
            }
          });
          if (content.interactive === true) {
            errors.push('Read-only gauge ' + node.stateKey + ' marked interactive');
          }
        }
      }
      Object.keys(node).forEach(function (k) {
        if (node[k] && typeof node[k] === 'object') findWriteOnReadOnly(node[k]);
      });
    }
    findWriteOnReadOnly(doc.routes);

    // Forbidden legacy sections
    ['controls', 'ui', 'extensions', 'registerComponents', 'schemaControlList', 'catalog', 'studioDesign'].forEach(function (legacy) {
      if (doc[legacy] != null) {
        errors.push('Legacy section must not be present: ' + legacy);
      }
    });

    return { valid: errors.length === 0, errors: errors, warnings: warnings };
  }

  function migrateLegacy(legacy) {
    legacy = legacy || {};
    if (legacy.documentType === DOCUMENT_TYPE && legacy.state && legacy.routes) {
      // Already SDUI — strip legacy duplicate sections
      var cleaned = JSON.parse(JSON.stringify(legacy));
      delete cleaned.controls;
      delete cleaned.ui;
      delete cleaned.extensions;
      delete cleaned.registerComponents;
      delete cleaned.pages;
      delete cleaned.catalog;
      delete cleaned.studioDesign;
      delete cleaned.settings;
      delete cleaned.schemaControlList;
      return cleaned;
    }

    var themeSource = legacy.theme || (legacy.settings && legacy.settings.theme) || {};
    var controls = legacy.controls || legacy.parameters || [];
    if (!controls.length && legacy.state && legacy.state.device && legacy.state.device.parameters) {
      controls = Object.keys(legacy.state.device.parameters).map(function (key) {
        return {
          id: key,
          parameterKey: key,
          parameterName: key,
          label: key,
          componentType: 'value',
          initialValue: legacy.state.device.parameters[key],
          pageNo: '1',
          visible: true
        };
      });
    }

    var pages = legacy.pages || legacy.navigation && legacy.navigation.pages || [
      { id: 'page-1', pageNo: '1', title: 'Controls' }
    ];

    var modelNo = (legacy.app && legacy.app.modelNo) || legacy.modelNo || 'Device';
    var links = legacy.links || {};

    return buildDocument({
      theme: Object.assign({}, themeSource, {
        accentColor: themeSource.colors && themeSource.colors.primary,
        cardColor: themeSource.colors && themeSource.colors.surface,
        textColor: themeSource.colors && themeSource.colors.text,
        backgroundColor: themeSource.colors && themeSource.colors.background
      }),
      controls: controls.map(function (control, index) {
        return {
          id: control.id || ('control_' + index),
          parameterKey: control.parameterKey || control.binding || control.id,
          parameterName: control.parameterName || control.parameter || control.label,
          label: control.label || control.parameterName || control.id,
          componentType: control.componentType || control.controlType || control.type || 'value',
          initialValue: control.initialValue != null ? control.initialValue : control.value,
          options: control.options || [],
          constraints: control.constraints || {},
          pageNo: control.pageNo || '1',
          icon: control.icon || 'gauge',
          visible: control.visible !== false,
          styles: control.styles || {},
          meta: control.meta || {},
          readOnly: control.readOnly,
          interactive: control.interactive
        };
      }),
      pages: pages.map(function (page, index) {
        return {
          id: page.id || ('page-' + (index + 1)),
          pageNo: page.pageNo || String(index + 1),
          title: page.title || page.pageName || ('Page ' + (index + 1))
        };
      }),
      modelNo: modelNo,
      modalId: (legacy.app && legacy.app.modalId) || legacy.modalId || 0,
      origin: 'http://localhost',
      parametersApiUrl: links.parameters || '',
      saveThemeUrl: links.save || '',
      deviceInfoUrl: links.deviceInfo || '',
      schemaApiUrl: links.schema || '',
      schemaDocsUrl: 'http://localhost/app/Sdui/schema/server-driven-ui.schema.json',
      activeIndex: 0,
      locale: 'en-IN'
    });
  }

  var LEGACY_FIELDS_TO_REMOVE = [
    'controls',
    'ui',
    'ui.widgetTree',
    'extensions',
    'extensions.universalUi',
    'registerComponents',
    'pages',
    'navigation',
    'settings.componentOverrides',
    'settings.componentTypes',
    'schemaControlList',
    'catalog',
    'studioDesign',
    'state.device',
    'schemaType',
    'rendering.stac',
    'component.widgetTree (per-control expanded trees)',
    'binding roots: device.parameters.*, $device.*, parameters.parameter-*.value'
  ];

  return {
    SCHEMA_VERSION: SCHEMA_VERSION,
    DOCUMENT_TYPE: DOCUMENT_TYPE,
    CUSTOM_COMPONENTS: CUSTOM_COMPONENTS,
    buildDocument: buildDocument,
    validateSemantic: validateSemantic,
    migrateLegacy: migrateLegacy,
    normalizeColor: normalizeColor,
    normalizeOptions: normalizeOptions,
    coerceEnumValue: coerceEnumValue,
    LEGACY_FIELDS_TO_REMOVE: LEGACY_FIELDS_TO_REMOVE
  };
});
