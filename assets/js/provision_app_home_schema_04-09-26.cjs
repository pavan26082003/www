#!/usr/bin/env node
'use strict';

/**
 * Build App Home SDUI schema from layout + cfg JSON (stdin or file paths).
 * Used by PHP AppHomeSchemaBuilder for automatic new-user provisioning.
 *
 * Usage:
 *   node provision_app_home_schema.cjs --layout layout.json --cfg cfg.json
 *   echo '{"layout":{...},"cfg":{...}}' | node provision_app_home_schema.cjs
 */

const fs = require('fs');
const path = require('path');
const vm = require('vm');

function readJsonFile(filePath) {
  return JSON.parse(fs.readFileSync(filePath, 'utf8'));
}

function readStdinJson() {
  const raw = fs.readFileSync(0, 'utf8').trim();
  if (!raw) {
    throw new Error('Empty stdin payload');
  }
  return JSON.parse(raw);
}

function parseArgs(argv) {
  const out = { layout: null, cfg: null };
  for (let i = 2; i < argv.length; i++) {
    if (argv[i] === '--layout' && argv[i + 1]) {
      out.layout = readJsonFile(argv[++i]);
    } else if (argv[i] === '--cfg' && argv[i + 1]) {
      out.cfg = readJsonFile(argv[++i]);
    }
  }
  return out;
}

function loadSchemaBuilder() {
  const schemaPath = path.join(__dirname, 'app_home_schema.js');
  const schemaJs = fs.readFileSync(schemaPath, 'utf8');
  const ctx = { console, globalThis: {}, window: {} };
  ctx.window = ctx.globalThis;
  vm.createContext(ctx);
  vm.runInContext(schemaJs, ctx);
  if (!ctx.globalThis.AhpSchemaV3 || typeof ctx.globalThis.AhpSchemaV3.build !== 'function') {
    throw new Error('AhpSchemaV3.build is not available after loading app_home_schema.js');
  }
  return ctx.globalThis.AhpSchemaV3;
}

function defaultHelpers() {
  return {
    getCardAnimOffset: function () { return 0; },
    getDeviceAnimOffset: function () { return 0; },
    getDefaultFace: function () { return 0; },
    getDeviceDefaultFace: function () { return 0; },
    getCardFaceSec: function () { return 16; },
    getCardRotationSpeed: function () { return 16; }
  };
}

function main() {
  let layout;
  let cfg;

  const args = parseArgs(process.argv);
  if (args.layout) {
    layout = args.layout;
    cfg = args.cfg || {};
  } else {
    const payload = readStdinJson();
    layout = payload.layout || payload;
    cfg = payload.cfg || {};
  }

  if (!layout || typeof layout !== 'object') {
    throw new Error('layout object is required');
  }

  const builder = loadSchemaBuilder();
  const schema = builder.build(layout, cfg, defaultHelpers());
  process.stdout.write(JSON.stringify(schema));
}

try {
  main();
} catch (err) {
  process.stderr.write((err && err.stack) ? err.stack : String(err));
  process.exit(1);
}
