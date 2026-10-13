<?php
$iotDevices = isset($devices) && is_array($devices) ? $devices : array();
$savedSchema = isset($schema) && is_array($schema) ? $schema : array();
$savedAccessToken = isset($accessToken) ? trim((string) $accessToken) : '';
$v8ApiBase = isset($v8ApiPath) ? rtrim((string) $v8ApiPath, '/') : '';
$v8DeviceApiBase = isset($v8DeviceApiPath) ? rtrim((string) $v8DeviceApiPath, '/') : 'https://efactor.tech/v8/iot_api_creation.php';
$tokenSuffix = $savedAccessToken !== '' ? '&access_token=' . rawurlencode($savedAccessToken) : '';
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800;900&display=swap');
/* ── design tokens ──────────────────────────────────────────── */
:root{
  --iot-blue:#2563eb;
  --iot-green:#16a34a;
  --iot-green-light:#dcfce7;
  --iot-green-mid:#bbf7d0;
  --iot-gray:#94a3b8;
  --iot-red:#dc2626;
  --iot-ink:#1f2a44;
  --iot-muted:#71819a;
  --iot-line:rgba(148,163,184,.26);
}

/* ── shell ───────────────────────────────────────────────────── */
.iot-shell{
  height:calc(100vh - 72px);
  display:flex;flex-direction:column;
  background:linear-gradient(120deg,#d6f8f1,#eef4fb 48%,#d9e7ff);
  font-family:Inter,Arial,sans-serif;
  color:var(--iot-ink);overflow:hidden;
}

/* ── toolbar ─────────────────────────────────────────────────── */
.iot-toolbar{
  min-height:60px;padding:10px 20px;
  display:flex;align-items:center;gap:12px;
  background:rgba(255,255,255,.92);
  border-bottom:1px solid var(--iot-line);
  flex-shrink:0;
}
.iot-title-block{flex:1}
.iot-title-block span,.iot-panel-label,.iot-eyebrow{
  display:block;color:var(--iot-blue);
  font-size:11px;font-weight:900;
  text-transform:uppercase;letter-spacing:.05em;
}
.iot-title-block h1{margin:2px 0;font-size:20px}
.iot-title-block p,.iot-detail-panel p,.iot-phone-header p{
  margin:0;color:var(--iot-muted);font-size:12px;
}
.iot-icon-btn{
  min-width:38px;height:38px;padding:0 12px;
  border:1px solid var(--iot-line);border-radius:10px;
  background:#fff;color:#24304a;font-size:12px;font-weight:700;cursor:pointer;
}
.iot-icon-btn:hover{background:#f8fafc}

/* ── 3-col workspace ─────────────────────────────────────────── */
.iot-workspace{
  flex:1;min-height:0;
  display:grid;
  grid-template-columns:minmax(210px,240px) minmax(420px,1fr) minmax(320px,480px);
  gap:18px;padding:18px 20px;overflow:hidden;
}
.iot-left-panel,.iot-side-panel{min-height:0;overflow:auto;padding:4px}
.iot-left-panel{display:flex;flex-direction:column;gap:18px}

/* ── schema btn ──────────────────────────────────────────────── */
.iot-schema-btn{
  width:100%;min-height:44px;border:0;border-radius:12px;
  background:linear-gradient(135deg,#0f8b8d,#2563eb);
  color:#fff;font-size:13px;font-weight:900;cursor:pointer;
}
.iot-schema-btn:disabled{opacity:.6;cursor:wait}

/* ── access token field ──────────────────────────────────────── */
.iot-token-field{
  display:flex;flex-direction:column;gap:6px;
}
.iot-token-field label{
  font-size:10px;font-weight:900;color:var(--iot-blue);
  text-transform:uppercase;letter-spacing:.05em;
}
.iot-token-input{
  width:100%;min-height:38px;padding:8px 10px;
  border:1px solid var(--iot-line);border-radius:10px;
  background:#fff;color:var(--iot-ink);
  font-size:12px;font-family:Consolas,monospace;
  overflow-x:auto;white-space:nowrap;
}
.iot-token-input:focus{
  outline:2px solid rgba(37,99,235,.25);
  border-color:rgba(37,99,235,.45);
}
.iot-token-hint{
  margin:0;font-size:10px;color:var(--iot-muted);line-height:1.4;
}

/* ── glass panels ────────────────────────────────────────────── */
.glass-panel,.iot-detail-panel,.iot-panel-card{
  background:rgba(255,255,255,.82);
  border:1px solid rgba(255,255,255,.7);
  box-shadow:0 14px 38px rgba(31,42,68,.09);
  backdrop-filter:blur(18px);
}
.iot-detail-panel,.iot-panel-card{padding:14px;border-radius:14px}
.iot-detail-panel strong{display:block;font-size:15px;margin-bottom:6px}

/* ── phone chrome ────────────────────────────────────────────── */
.iot-phone-wrap{
  min-height:0;overflow:auto;
  display:flex;justify-content:center;align-items:flex-start;
  padding:6px 0 24px;
}
.iot-phone{
  position:relative;width:375px;
  border:11px solid #111827;border-radius:44px;
  background:#111827;
  box-shadow:0 26px 68px rgba(15,23,42,.34),inset 0 0 0 1px rgba(255,255,255,.06);
  overflow:hidden;flex-shrink:0;
}
.iot-phone::before{
  content:"";position:absolute;z-index:10;
  top:8px;left:50%;transform:translateX(-50%);
  width:86px;height:20px;border-radius:999px;background:#111827;
}
.iot-phone-status{
  position:relative;z-index:11;height:34px;padding:0 20px;
  display:flex;align-items:center;justify-content:space-between;
  color:#fff;font-size:12px;font-weight:700;
}
.iot-phone-screen{
  height:680px;overflow-y:auto;overflow-x:hidden;
  padding:14px 12px 28px;
  background:#ececec;
  overscroll-behavior:contain;
  font-family:'Inter',system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
  -webkit-font-smoothing:antialiased;
}
.iot-phone-header{
  display:flex;align-items:flex-start;justify-content:space-between;gap:10px;
  padding:4px 2px 12px;margin-bottom:4px;
  background:transparent;box-shadow:none;
}
.iot-phone-header h2{margin:4px 0 2px;font-size:22px;font-weight:900;line-height:1.08;color:#111827}
.iot-phone-header .iot-eyebrow{color:#9ca3af;font-size:12px;font-weight:600;text-transform:none;letter-spacing:0}
.iot-phone-header p{margin:0;color:#9ca3af;font-size:12px}
.iot-header-actions{display:flex;align-items:flex-start;gap:8px;flex-shrink:0}
.iot-device-search{
  display:none;align-items:center;gap:8px;
  margin:0 0 12px;padding:0 12px;height:42px;border-radius:14px;
  background:#fff;box-shadow:0 8px 24px rgba(0,0,0,.06);
}
.iot-device-search.is-open{display:flex}
.iot-device-search svg{
  width:16px;height:16px;flex-shrink:0;color:#9ca3af;
  stroke:currentColor;fill:none;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round;
}
.iot-device-search input{
  flex:1;min-width:0;border:0;outline:none;background:transparent;
  font:600 13px/1.2 'Inter',system-ui,sans-serif;color:#111827;
}
.iot-device-search input::placeholder{color:#9ca3af;font-weight:600}
.iot-device-search input::-webkit-search-cancel-button{cursor:pointer}
.iot-header-avatar{
  width:42px;height:42px;border-radius:14px;flex-shrink:0;
  background:linear-gradient(145deg,#111827,#374151);
  box-shadow:0 8px 20px rgba(17,24,39,.18);
  display:flex;align-items:center;justify-content:center;
  color:#fff;
}
.iot-header-add-btn{
  width:42px;height:42px;border-radius:14px;flex-shrink:0;border:0;padding:0;
  background:linear-gradient(145deg,#2563eb,#1d4ed8);
  box-shadow:0 8px 20px rgba(37,99,235,.28);
  display:flex;align-items:center;justify-content:center;
  color:#fff;cursor:pointer;transition:transform .15s ease, box-shadow .15s ease;
}
.iot-header-add-btn:hover{transform:scale(1.04);box-shadow:0 10px 24px rgba(37,99,235,.34)}
.iot-header-add-btn:active{transform:scale(.96)}
.iot-header-search-btn{
  width:42px;height:42px;border-radius:14px;flex-shrink:0;border:0;padding:0;
  background:#fff;box-shadow:0 8px 20px rgba(17,24,39,.1);
  display:flex;align-items:center;justify-content:center;
  color:#111827;cursor:pointer;transition:transform .15s ease, box-shadow .15s ease, background .15s ease, color .15s ease;
}
.iot-header-search-btn:hover{transform:scale(1.04);box-shadow:0 10px 24px rgba(17,24,39,.14)}
.iot-header-search-btn:active{transform:scale(.96)}
.iot-header-search-btn.is-open{background:#111827;color:#fff}
.iot-header-add-btn svg,
.iot-header-search-btn svg{
  width:22px;height:22px;stroke:currentColor;fill:none;
  stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round;
}
.iot-qr-modal{
  position:fixed;inset:0;z-index:9999;display:none;
  align-items:flex-end;justify-content:center;padding:0;
  background:rgba(15,23,42,.62);backdrop-filter:blur(6px);
}
@media (min-width:520px){
  .iot-qr-modal{align-items:center;padding:16px}
}
.iot-qr-modal.is-open{display:flex}
.iot-qr-sheet{
  position:relative;width:min(420px,100%);max-height:min(92vh,720px);
  background:#fff;border-radius:24px 24px 0 0;overflow:hidden;
  display:flex;flex-direction:column;
  box-shadow:0 -8px 40px rgba(15,23,42,.16),0 24px 60px rgba(15,23,42,.28);
  animation:iot-qr-sheet-in .35s cubic-bezier(.22,.9,.28,1);
}
@media (min-width:520px){
  .iot-qr-sheet{border-radius:24px}
}
@keyframes iot-qr-sheet-in{
  from{transform:translateY(24px);opacity:0}
  to{transform:none;opacity:1}
}
.iot-qr-sheet-header{
  display:grid;grid-template-columns:40px minmax(0,1fr) auto;align-items:center;gap:8px;
  padding:14px 16px 10px;border-bottom:1px solid #f1f5f9;
}
.iot-qr-sheet-title{
  text-align:center;font-size:15px;font-weight:900;color:#111827;letter-spacing:-.02em;
}
.iot-qr-header-spacer{width:36px;height:36px}
.iot-qr-icon-btn{
  width:36px;height:36px;border:0;border-radius:12px;background:#f1f5f9;color:#475569;
  cursor:pointer;display:inline-flex;align-items:center;justify-content:center;
  transition:background .15s ease,transform .15s ease;
}
.iot-qr-icon-btn:hover{background:#e2e8f0}
.iot-qr-icon-btn:active{transform:scale(.96)}
.iot-qr-icon-btn svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0}
.iot-qr-icon-btn.is-qr{color:#2563eb}
.iot-qr-icon-btn.is-qr.has-label{width:auto;min-height:36px;padding:6px 10px;gap:6px;justify-content:flex-start}
.iot-qr-manual-label-text{font-size:11px;font-weight:800;line-height:1.15;color:#2563eb;white-space:nowrap;letter-spacing:-.01em}
@media (max-width:360px){.iot-qr-manual-label-text{font-size:10px}}
.iot-qr-sheet-body{padding:12px 16px 16px;overflow-y:auto;flex:1 1 auto}
.iot-qr-hero{
  display:flex;gap:12px;align-items:center;margin-bottom:14px;padding:12px 14px;
  border-radius:16px;background:linear-gradient(135deg,#eff6ff,#f8fafc);border:1px solid #dbeafe;
}
.iot-qr-hero-icon{
  width:44px;height:44px;border-radius:14px;background:#2563eb;color:#fff;
  display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;
  box-shadow:0 10px 24px rgba(37,99,235,.28);
}
.iot-qr-hero-icon svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.iot-qr-kicker{
  display:block;font-size:10px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#2563eb;
}
.iot-qr-subtitle{margin:4px 0 0;font-size:12px;font-weight:600;color:#64748b;line-height:1.45}
.iot-qr-scanner-stage{
  position:relative;min-height:340px;border-radius:24px;overflow:hidden;background:#05070c;margin-bottom:14px;
}
.iot-qr-reader-wrap{position:absolute;inset:0;z-index:0}
.iot-qr-reader-wrap video{
  width:100%!important;height:100%!important;object-fit:cover!important;border-radius:0!important;
}
.iot-qr-reader-wrap #qr-shaded-region{border:none!important;box-shadow:none!important}
.iot-qr-reader-wrap img[alt="Info icon"]{display:none!important}
.iot-qr-scan-overlay{
  position:absolute;inset:0;z-index:2;display:flex;align-items:center;justify-content:center;
  pointer-events:none;
  background:linear-gradient(180deg,rgba(5,7,12,.35) 0%,transparent 18%,transparent 62%,rgba(5,7,12,.75) 100%);
}
.iot-qr-scan-frame{
  position:relative;width:min(72%,240px);aspect-ratio:1;border-radius:28px;
  box-shadow:0 0 0 9999px rgba(5,7,12,.42);
}
.iot-qr-scan-frame span{
  position:absolute;width:28px;height:28px;border:3px solid #3b82f6;
}
.iot-qr-scan-frame span:nth-child(1){top:0;left:0;border-right:0;border-bottom:0;border-top-left-radius:18px}
.iot-qr-scan-frame span:nth-child(2){top:0;right:0;border-left:0;border-bottom:0;border-top-right-radius:18px}
.iot-qr-scan-frame span:nth-child(3){bottom:0;left:0;border-right:0;border-top:0;border-bottom-left-radius:18px}
.iot-qr-scan-frame span:nth-child(4){bottom:0;right:0;border-left:0;border-top:0;border-bottom-right-radius:18px}
.iot-qr-scan-line{
  position:absolute;left:8%;right:8%;height:2px;top:12%;
  background:linear-gradient(90deg,transparent,#60a5fa,transparent);
  animation:iot-qr-scan-sweep 2.2s ease-in-out infinite;
}
@keyframes iot-qr-scan-sweep{
  0%,100%{top:12%;opacity:.35}
  50%{top:88%;opacity:1}
}
.iot-qr-steps{display:flex;gap:8px;justify-content:center;margin-bottom:12px}
.iot-qr-step{
  flex:1;text-align:center;padding:8px 6px;border-radius:12px;
  font-size:11px;font-weight:800;color:#94a3b8;background:#f1f5f9;transition:all .2s ease;
}
.iot-qr-step.is-active{color:#fff;background:#2563eb;box-shadow:0 8px 20px rgba(37,99,235,.24)}
.iot-qr-step.is-done{color:#16a34a;background:#dcfce7}
.iot-qr-status-pill{
  display:flex;align-items:center;gap:10px;padding:12px 14px;border-radius:14px;
  background:#f8fafc;border:1px solid #e2e8f0;font-size:12px;font-weight:700;color:#334155;min-height:44px;
}
.iot-qr-status-pill.is-error{background:#fef2f2;border-color:#fecaca;color:#dc2626}
.iot-qr-status-pill.is-warning{background:#fffbeb;border-color:#fde68a;color:#b45309}
.iot-qr-status-pill.is-success{background:#f0fdf4;border-color:#bbf7d0;color:#16a34a}
.iot-qr-status-dot{
  width:8px;height:8px;border-radius:50%;background:#2563eb;flex-shrink:0;
  animation:iot-qr-pulse 1.4s ease infinite;
}
.iot-qr-status-pill.is-error .iot-qr-status-dot,.iot-qr-status-pill.is-success .iot-qr-status-dot{animation:none}
.iot-qr-status-pill.is-error .iot-qr-status-dot{background:#dc2626}
.iot-qr-status-pill.is-warning .iot-qr-status-dot{background:#f59e0b}
.iot-qr-status-pill.is-success .iot-qr-status-dot{background:#16a34a}
@keyframes iot-qr-pulse{
  0%,100%{opacity:.45;transform:scale(.85)}
  50%{opacity:1;transform:scale(1)}
}
.iot-qr-sheet-footer{
  padding:12px 16px calc(12px + env(safe-area-inset-bottom,0));border-top:1px solid #f1f5f9;
  display:flex;justify-content:center;
}
.iot-qr-btn{
  border:0;border-radius:12px;padding:12px 16px;font-size:13px;font-weight:800;cursor:pointer;
  transition:background .15s ease,transform .15s ease;
}
.iot-qr-btn.ghost{width:100%;background:#fff;color:#64748b;border:1px solid #e2e8f0}
.iot-qr-btn.ghost:hover{background:#f8fafc}
.iot-qr-btn.ghost:active{transform:scale(.98)}
.iot-qr-btn.primary{background:#2563eb;color:#fff;box-shadow:0 8px 20px rgba(37,99,235,.24)}
.iot-qr-btn.primary:hover{background:#1d4ed8}
.iot-qr-btn.primary:active{transform:scale(.98)}
.iot-qr-manual-modal{
  position:absolute;inset:0;z-index:6;display:none;
  align-items:center;justify-content:center;padding:20px;
  background:rgba(15,23,42,.45);backdrop-filter:blur(4px);
}
.iot-qr-manual-modal.is-open{display:flex}
.iot-qr-manual-card{
  width:100%;background:#fff;border-radius:18px;padding:18px 16px 16px;
  box-shadow:0 16px 40px rgba(15,23,42,.22);
}
.iot-qr-manual-title{margin:0 0 4px;font-size:15px;font-weight:900;color:#111827;text-align:center;letter-spacing:-.02em}
.iot-qr-manual-hint{margin:0 0 14px;font-size:12px;font-weight:600;color:#64748b;text-align:center;line-height:1.45}
.iot-qr-manual-label{display:block;font-size:11px;font-weight:800;color:#475569;margin-bottom:6px;letter-spacing:.04em;text-transform:uppercase}
.iot-qr-manual-input{
  width:100%;box-sizing:border-box;border:1.5px solid #e2e8f0;border-radius:12px;
  padding:12px 14px;font-size:14px;font-weight:700;color:#111827;outline:none;background:#fff;
}
.iot-qr-manual-input:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.15)}
.iot-qr-manual-input.is-error{border-color:#dc2626;box-shadow:0 0 0 3px rgba(220,38,38,.12)}
.iot-qr-manual-error{
  display:none;margin:6px 0 0;font-size:12px;font-weight:700;color:#dc2626;line-height:1.35;
}
.iot-qr-manual-error.is-visible{display:block}
.iot-qr-manual-actions{display:flex;gap:8px;margin-top:14px}
.iot-qr-manual-actions .iot-qr-btn{flex:1;width:auto}
.iot-header-avatar svg{
  width:22px;height:22px;stroke:currentColor;fill:none;
  stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;
}
.iot-stats-card{
  display:flex;align-items:center;justify-content:space-between;gap:12px;
  padding:14px 16px;margin-bottom:14px;border-radius:24px;
  background:#fff;box-shadow:0 8px 24px rgba(0,0,0,.06);
}
.iot-stats-card-left{display:flex;flex-direction:column;gap:2px}
.iot-stats-card-label{font-size:11px;font-weight:700;color:#9ca3af}
.iot-stats-card-title{font-size:13px;font-weight:800;color:#111827}
.iot-stats-card-value{font-size:28px;font-weight:900;color:#84cc16;line-height:1.05;margin-top:2px}
.iot-stats-card-value span{font-size:14px;font-weight:700;color:#84cc16}
.iot-stats-card-art{
  width:58px;height:58px;border-radius:18px;flex-shrink:0;
  background:linear-gradient(145deg,#ecfccb,#d9f99d);
  box-shadow:0 6px 18px rgba(132,204,22,.16);
  display:flex;align-items:center;justify-content:center;
  color:#65a30d;
}
.iot-stats-card-art svg{
  width:28px;height:28px;stroke:currentColor;fill:none;
  stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;
}
.iot-live-dot{
  width:10px;height:10px;border-radius:50%;
  background:#84cc16;
  box-shadow:0 0 0 4px rgba(132,204,22,.18);
  flex-shrink:0;animation:iot-pulse 2s ease-in-out infinite;
}
@keyframes iot-pulse{
  0%,100%{box-shadow:0 0 0 4px rgba(132,204,22,.22)}
  50%{box-shadow:0 0 0 8px rgba(132,204,22,.08)}
}

/* ══ device grid ════════════════════════════════════════════════ */
.iot-device-grid{
  display:grid;
  grid-template-columns:repeat(2,minmax(0,1fr));
  gap:12px;
}

/* ══ CARD — SmartHome reference style ═══════════════════════════ */
.iot-device-card{
  position:relative;display:flex;flex-direction:column;
  min-height:168px;padding:14px 12px 12px;
  border-radius:24px;border:0;overflow:hidden;
  cursor:pointer;outline:none;
  transition:transform .18s ease,box-shadow .18s ease;
}
.iot-device-card::before{
  content:"";position:absolute;inset:0;pointer-events:none;
  background:radial-gradient(circle at 88% 18%,rgba(255,255,255,.08) 0%,transparent 42%);
}
.iot-device-card:focus-visible{outline:3px solid rgba(132,204,22,.45);outline-offset:2px}
.iot-device-card.is-selected{outline:3px solid rgba(132,204,22,.55);outline-offset:2px}

/* ONLINE — dark card */
.iot-device-card.is-online{
  background:#1c1c1e;color:#fff;
  box-shadow:0 12px 28px rgba(0,0,0,.18);
}
.iot-device-card.is-online:hover{
  transform:translateY(-3px);
  box-shadow:0 16px 34px rgba(0,0,0,.24);
}

/* OFFLINE — light card with clear muted styling */
.iot-device-card.is-offline{
  background:#fff;color:#111827;
  box-shadow:0 8px 24px rgba(0,0,0,.07);
  border:1.5px solid #e5e7eb;
}
.iot-device-card.is-offline::before{
  background:radial-gradient(circle at 88% 18%,rgba(148,163,184,.08) 0%,transparent 42%);
}
.iot-device-card.is-offline .iot-product-image{opacity:.72;filter:grayscale(.35)}
.iot-device-card.is-offline .iot-card-signal{opacity:.28}
.iot-device-card.is-offline:hover{
  transform:translateY(-2px);
  box-shadow:0 12px 28px rgba(0,0,0,.1);
}

/* top row: icon + signal */
.iot-card-top{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:10px;position:relative;z-index:1;
}
.iot-card-icon{
  width:38px;height:38px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;
  position:relative;
}
.is-online .iot-card-icon{background:#fff}
.is-offline .iot-card-icon{background:#111827}
.iot-card-icon .iot-logo{
  width:100%;height:100%;border:0;border-radius:50%;object-fit:cover;
  position:relative;z-index:2;
}
.iot-card-icon .iot-icon-fallback{
  position:absolute;inset:0;display:flex;align-items:center;justify-content:center;z-index:1;
}
.iot-card-icon .iot-icon-fallback.is-initial{font-size:14px;font-weight:900}
.iot-brand-placeholder{
  width:38px;height:38px;min-width:38px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:14px;font-weight:900;flex-shrink:0;
}
.is-online .iot-brand-placeholder{background:#fff;color:#111827}
.is-offline .iot-brand-placeholder{background:#111827;color:#fff}
.iot-card-icon svg{width:20px;height:20px;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;fill:none}
.is-online .iot-card-icon svg{stroke:#111827}
.is-offline .iot-card-icon svg{stroke:#fff}
.iot-card-signal{
  width:18px;height:18px;opacity:.55;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
}
.iot-card-signal svg{width:16px;height:16px;stroke-width:2;stroke-linecap:round;fill:none}
.is-online .iot-card-signal svg{stroke:#fff}
.is-offline .iot-card-signal svg{stroke:#111827}

/* titles */
.iot-card-body{padding:0;margin-bottom:6px;position:relative;z-index:1}
.iot-device-name{
  margin:0 0 2px;font-size:13.5px;font-weight:800;line-height:1.28;letter-spacing:-.01em;
  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
}
.is-online .iot-device-name{color:#fff}
.is-offline .iot-device-name{color:#111827}
.iot-brand-name{
  margin:0;font-size:11px;font-weight:600;line-height:1.25;letter-spacing:.01em;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.is-online .iot-brand-name{color:rgba(255,255,255,.62)}
.is-offline .iot-brand-name{color:#9ca3af}
.iot-device-type{
  margin:2px 0 0;font-size:10px;font-weight:600;line-height:1.2;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.is-online .iot-device-type{color:rgba(255,255,255,.42)}
.is-offline .iot-device-type{color:#cbd5e1}

/* product image */
.iot-product-wrap{
  padding:4px 0 6px;display:flex;align-items:center;justify-content:flex-start;
  position:relative;z-index:1;min-height:48px;
}
.iot-product-image{width:auto;max-width:100%;height:48px;object-fit:contain;border-radius:8px;display:block}
.iot-product-icon,.iot-product-fallback{
  width:48px;height:48px;display:flex;align-items:center;justify-content:center;
  border-radius:14px;background:rgba(255,255,255,.1);
}
.is-offline .iot-product-icon,.is-offline .iot-product-fallback{background:#f3f4f6;border:1px dashed #e5e7eb}
.iot-product-icon svg,.iot-product-fallback svg{width:24px;height:24px;stroke-width:1.6}
.is-online .iot-product-icon svg,.is-online .iot-product-fallback svg{stroke:#a3e635}
.is-offline .iot-product-icon svg,.is-offline .iot-product-fallback svg{stroke:#9ca3af}
.iot-product-fallback{display:none}
.iot-product-wrap.is-fallback .iot-product-image{display:none}
.iot-product-wrap.is-fallback .iot-product-fallback{display:flex}

/* status line */
.iot-status-line{
  display:inline-flex;align-items:center;gap:5px;
  font-size:10.5px;font-weight:800;margin-bottom:8px;
  position:relative;z-index:1;
}
.iot-status-line::before{
  content:"";width:6px;height:6px;border-radius:50%;flex-shrink:0;
}
.is-online .iot-status-line{color:#a3e635}
.is-online .iot-status-line::before{background:#84cc16;box-shadow:0 0 0 3px rgba(132,204,22,.22)}
.is-offline .iot-status-line{color:#94a3b8}
.is-offline .iot-status-line::before{background:#cbd5e1}

/* footer: On/Off + toggle */
.iot-card-footer{
  display:flex;align-items:center;justify-content:space-between;gap:8px;
  margin-top:auto;padding-top:2px;position:relative;z-index:1;
}
.iot-power-label{font-size:12px;font-weight:800}
.is-online .iot-power-label{color:#fff}
.is-offline .iot-power-label{color:#111827}

/* toggle switch */
.iot-toggle{
  width:44px;height:26px;padding:0;border:0;border-radius:999px;
  position:relative;cursor:pointer;flex-shrink:0;
  transition:background .18s ease;appearance:none;
}
.iot-toggle::after{
  content:"";position:absolute;top:3px;left:3px;
  width:20px;height:20px;border-radius:50%;
  background:#fff;box-shadow:0 2px 6px rgba(0,0,0,.18);
  transition:transform .18s ease;
}
.iot-toggle.is-on{background:#fff}
.iot-toggle.is-on::after{transform:translateX(18px);background:#1c1c1e}
.is-online .iot-toggle.is-on{background:rgba(255,255,255,.9)}
.is-offline .iot-toggle{background:#111827}
.is-offline .iot-toggle::after{background:#fff}
.is-offline .iot-toggle.is-on::after{transform:translateX(18px);background:#fff}
.iot-toggle:disabled{cursor:not-allowed;opacity:.55}
.iot-toggle:not(:disabled):active{transform:scale(.96)}
.iot-toggle.is-on:not(:disabled){background:rgba(255,255,255,.92)}
.is-offline .iot-toggle:not(.is-on){background:#e5e7eb}
.is-offline .iot-toggle:not(.is-on)::after{background:#fff}

/* ── accessibility: reduced motion ───────────────────────────── */
@media(prefers-reduced-motion:reduce){
  .iot-live-dot{animation:none!important}
  .iot-device-card,.iot-toggle{transition:none!important}
}

/* ── detail panel ────────────────────────────────────────────── */
.iot-detail-brand-row{
  display:flex;align-items:center;gap:8px;
  margin-bottom:9px;padding-bottom:7px;border-bottom:1px solid var(--iot-line);
}
.iot-detail-brand-logo{width:28px;height:28px;object-fit:contain;border-radius:6px;border:1px solid rgba(203,213,225,.5)}
.iot-detail-brand-name{font-size:11px;font-weight:800;color:var(--iot-muted);text-transform:uppercase;letter-spacing:.04em}
.iot-detail-product-img{width:100%;max-height:100px;object-fit:contain;border-radius:9px;margin-bottom:8px;border:1px solid rgba(203,213,225,.35);background:#f8fbff}
.iot-detail-status{
  display:inline-flex;align-items:center;gap:5px;
  padding:3px 10px;border-radius:999px;
  font-size:11px;font-weight:800;margin-bottom:7px;
}
.iot-detail-status.online{background:rgba(132,204,22,.15);color:#65a30d;border:1px solid rgba(132,204,22,.25)}
.iot-detail-status.offline{background:#f1f5f9;color:#64748b;border:1px solid rgba(148,163,184,.25)}
.iot-detail-status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0}
.iot-detail-row{display:flex;justify-content:space-between;gap:8px;font-size:11px;margin-bottom:4px}
.iot-detail-row dt{color:var(--iot-muted);font-weight:700;flex-shrink:0}
.iot-detail-row dd{color:var(--iot-ink);font-weight:800;margin:0;text-align:right;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}

/* ── right panel ─────────────────────────────────────────────── */
.iot-side-panel{display:flex;flex-direction:column;gap:10px}
.iot-api-head{
  display:flex;align-items:center;justify-content:space-between;gap:8px;
  margin-bottom:6px;
}
.iot-api-head .iot-panel-label{margin:0}
.iot-copy-btn{
  min-width:68px;height:28px;padding:0 10px;border:1px solid var(--iot-line);
  border-radius:8px;background:#fff;color:#24304a;
  font-size:10px;font-weight:800;cursor:pointer;flex-shrink:0;
  transition:background .15s ease,color .15s ease,border-color .15s ease;
}
.iot-copy-btn:hover{background:#f8fafc;border-color:rgba(37,99,235,.35)}
.iot-copy-btn.is-copied{
  background:rgba(132,204,22,.12);border-color:rgba(132,204,22,.35);color:#65a30d;
}
.iot-panel-card code{display:block;word-break:break-all;color:#12345f;font-size:10.5px;margin-top:0}
.iot-schema-output{
  flex:1;min-height:380px;resize:none;padding:14px;border:0;border-radius:12px;
  background:#0f172a;color:#dbeafe;font:11px/1.65 Consolas,monospace;
}

/* ── empty state ─────────────────────────────────────────────── */
.iot-empty-state{
  grid-column:1/-1;padding:22px;
  border:1.5px dashed rgba(148,163,184,.42);border-radius:14px;
  text-align:center;color:var(--iot-muted);font-size:12px;
}

/* ── responsive ──────────────────────────────────────────────── */
@media(max-width:1180px){
  .iot-workspace{grid-template-columns:220px minmax(360px,1fr)}
  .iot-side-panel{grid-column:1/-1;max-height:380px}
}
@media(max-width:820px){
  .iot-shell{height:auto;overflow:visible}
  .iot-workspace{display:flex;flex-direction:column;overflow:visible;padding:12px}
  .iot-left-panel,.iot-side-panel,.iot-phone-wrap{overflow:visible}
  .iot-phone{width:min(375px,100%)}
  .iot-phone-screen{height:auto;min-height:600px}
  .iot-side-panel{max-height:none}
  .iot-toolbar{padding:10px 14px}
}
</style>

<div id="iot-dashboard-root"
     data-api-base="<?= htmlspecialchars($apiBase ?? '', ENT_QUOTES, 'UTF-8') ?>"
     data-dashboard-url="<?= htmlspecialchars($dashboardUrl ?? '', ENT_QUOTES, 'UTF-8') ?>"
     data-v8-api-path="<?= htmlspecialchars($v8ApiBase, ENT_QUOTES, 'UTF-8') ?>"
     data-v8-device-api-path="<?= htmlspecialchars($v8DeviceApiBase, ENT_QUOTES, 'UTF-8') ?>"
     data-profile-token="<?= htmlspecialchars($savedAccessToken, ENT_QUOTES, 'UTF-8') ?>"
     data-user-id="<?= (int) ($userId ?? 0) ?>"
     data-csrf-name="<?= htmlspecialchars($csrfTokenName ?? '', ENT_QUOTES, 'UTF-8') ?>"
     data-csrf-hash="<?= htmlspecialchars($csrfHash ?? '', ENT_QUOTES, 'UTF-8') ?>">
  <section class="iot-shell">

    <div class="iot-toolbar">
      <button type="button" class="iot-icon-btn" id="iotBackBtn">&#8592; Back</button>
      <div class="iot-title-block">
        <span>Device Experience</span>
        <h1>IoT Dashboard</h1>
        <p>Live profile devices with production mobile schema.</p>
      </div>
      <button type="button" class="iot-icon-btn" id="iotRefreshBtn">&#8635; Refresh</button>
    </div>

    <div class="iot-workspace">

      <div class="iot-left-panel">
        <div class="iot-token-field glass-panel" style="padding:12px;border-radius:14px">
          <label for="iotAccessTokenInput">Access Token</label>
          <input
            type="text"
            id="iotAccessTokenInput"
            class="iot-token-input"
            placeholder="Auto-loaded from efactorproduct — edit if needed"
            value="<?= htmlspecialchars($savedAccessToken, ENT_QUOTES, 'UTF-8') ?>"
            title="<?= htmlspecialchars($savedAccessToken, ENT_QUOTES, 'UTF-8') ?>"
            autocomplete="off"
            spellcheck="false"
          >
          <p class="iot-token-hint">Auto-loaded from <strong>efactorproduct</strong>. You can edit this token; API URLs update as you type.</p>
        </div>
        <button type="button" class="iot-schema-btn" id="iotGenerateSchemaBtn">Skema Generate</button>
        <div class="iot-detail-panel glass-panel" id="iotDetailPanel">
          <span class="iot-panel-label">Selected Device</span>
          <strong>Tap any device card</strong>
          <p>Device details will appear here for app developer API wiring.</p>
        </div>
      </div>

      <div class="iot-phone-wrap">
        <div class="iot-phone">
          <div class="iot-phone-status"><span>9:41</span><span>LTE&nbsp;&nbsp;100%</span></div>
          <div class="iot-phone-screen">
            <div class="iot-phone-header">
              <div>
                <span class="iot-eyebrow">Smart Home</span>
                <h2>My Devices</h2>
                <p id="iotSummaryText"><?= count($iotDevices) ?> products ready</p>
              </div>
              <div class="iot-header-actions">
                <button type="button" class="iot-header-search-btn" id="iotSearchToggleBtn" aria-label="Search devices" aria-expanded="false" aria-controls="iotDeviceSearchBar" title="Search devices">
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="M20 20l-3.5-3.5"/>
                  </svg>
                </button>
                <button type="button" class="iot-header-add-btn" id="iotAddDeviceBtn" aria-label="Add device" title="Scan QR to add device">
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 5v14"/>
                    <path d="M5 12h14"/>
                  </svg>
                </button>
              </div>
            </div>
            <label class="iot-device-search" id="iotDeviceSearchBar" for="iotDeviceSearch">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/>
                <path d="M20 20l-3.5-3.5"/>
              </svg>
              <input type="search" id="iotDeviceSearch" placeholder="Search devices" aria-label="Search devices by name" autocomplete="off" spellcheck="false">
            </label>
            <div class="iot-stats-card" id="iotStatsCard">
              <div class="iot-stats-card-left">
                <span class="iot-stats-card-label" id="iotStatsDate"></span>
                <span class="iot-stats-card-title">Connected Devices</span>
                <div class="iot-stats-card-value" id="iotStatsOnline">0 <span>online</span></div>
              </div>
              <div class="iot-stats-card-art" aria-hidden="true">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
                  <path d="M1.42 9a16 16 0 0 1 21.16 0"/>
                  <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                  <circle cx="12" cy="20" r="1.5" fill="currentColor" stroke="none"/>
                </svg>
              </div>
            </div>
            <div class="iot-device-grid" id="iotDeviceGrid" role="list"></div>
          </div>
        </div>
      </div>

      <aside class="iot-side-panel">
        <div class="iot-panel-card glass-panel">
          <div class="iot-api-head">
            <span class="iot-panel-label">Schema API</span>
            <button type="button" class="iot-copy-btn" id="iotCopySchemaApiBtn" data-copy-target="iotSchemaApiUrl">Copy API</button>
          </div>
          <code id="iotSchemaApiUrl"><?= htmlspecialchars($v8ApiBase, ENT_QUOTES, 'UTF-8') ?>?action=get_iot_schema<?= htmlspecialchars($tokenSuffix, ENT_QUOTES, 'UTF-8') ?></code>
        </div>
        <div class="iot-panel-card glass-panel">
          <div class="iot-api-head">
            <span class="iot-panel-label">Device API</span>
            <button type="button" class="iot-copy-btn" id="iotCopyDeviceApiBtn" data-copy-target="iotDeviceApiUrl">Copy API</button>
          </div>
          <code id="iotDeviceApiUrl"><?= htmlspecialchars($v8ApiBase, ENT_QUOTES, 'UTF-8') ?>?action=get_iot_devices<?= htmlspecialchars($tokenSuffix, ENT_QUOTES, 'UTF-8') ?></code>
        </div>
        <textarea id="iotSchemaOutput" class="iot-schema-output" readonly spellcheck="false"><?= htmlspecialchars(
          !empty($savedSchema) ? json_encode($savedSchema, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) : '',
          ENT_QUOTES, 'UTF-8'
        ) ?></textarea>
      </aside>

    </div>
  </section>
</div>

<div class="iot-qr-modal" id="iotQrModal" aria-hidden="true">
  <div class="iot-qr-sheet" role="dialog" aria-modal="true" aria-labelledby="iotQrTitle">
    <header class="iot-qr-sheet-header">
      <button type="button" class="iot-qr-icon-btn" id="iotQrCloseBtn" aria-label="Close scanner">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
      </button>
      <span class="iot-qr-sheet-title" id="iotQrTitle">Scan Device</span>
      <button type="button" class="iot-qr-icon-btn is-qr has-label" id="iotQrManualBtn" data-iot-control="manualNodeIdButton" aria-label="I don't have QR" title="I don't have QR — enter node ID">
        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3z"/><path d="M19 14h2v2h-2z"/><path d="M14 19h2v2h-2z"/><path d="M18 18h3v3h-3z"/></svg>
        <span class="iot-qr-manual-label-text">I don't have QR</span>
      </button>
    </header>
    <div class="iot-qr-sheet-body">
      <div class="iot-qr-hero">
        <span class="iot-qr-hero-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><path d="M7 12h10"/></svg>
        </span>
        <div>
          <span class="iot-qr-kicker">Add WiFi Node</span>
          <p class="iot-qr-subtitle" id="iotQrSubtitle">Align the QR code on your device inside the frame. We verify it, then register the node.</p>
        </div>
      </div>
      <div class="iot-qr-scanner-stage">
        <div class="iot-qr-reader-wrap" id="iotQrReader"></div>
        <div class="iot-qr-scan-overlay" aria-hidden="true">
          <div class="iot-qr-scan-frame">
            <span></span><span></span><span></span><span></span>
            <div class="iot-qr-scan-line"></div>
          </div>
        </div>
      </div>
      <div class="iot-qr-steps" id="iotQrSteps">
        <div class="iot-qr-step is-active" data-step="scan">Scan</div>
        <div class="iot-qr-step" data-step="checkDevice">Verify</div>
        <div class="iot-qr-step" data-step="addWifiNode">Add</div>
      </div>
      <div class="iot-qr-status-pill" id="iotQrStatus">
        <span class="iot-qr-status-dot" aria-hidden="true"></span>
        <span class="iot-qr-status-text">Point your camera at the device QR code…</span>
      </div>
    </div>
    <footer class="iot-qr-sheet-footer">
      <button type="button" class="iot-qr-btn ghost" id="iotQrCancelBtn">Cancel</button>
    </footer>
    <div class="iot-qr-manual-modal" id="iotQrManualModal" aria-hidden="true">
      <div class="iot-qr-manual-card" role="dialog" aria-modal="true" aria-labelledby="iotQrManualTitle">
        <h3 class="iot-qr-manual-title" id="iotQrManualTitle">Enter Node ID</h3>
        <p class="iot-qr-manual-hint">Type the device node ID to connect. We verify it, then register the node — same as scanning the QR code.</p>
        <label class="iot-qr-manual-label" for="iotQrNodeIdInput">Node ID</label>
        <input type="text" class="iot-qr-manual-input" id="iotQrNodeIdInput" name="node_id" autocomplete="off" autocapitalize="off" spellcheck="false" placeholder="Enter node ID" required>
        <p class="iot-qr-manual-error" id="iotQrNodeIdError" hidden>Please enter a Node ID.</p>
        <div class="iot-qr-manual-actions">
          <button type="button" class="iot-qr-btn ghost" id="iotQrManualCancelBtn">Cancel</button>
          <button type="button" class="iot-qr-btn primary" id="iotQrManualConnectBtn">Connect</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
window.IOT_DASHBOARD_BOOT = {
  devices: <?= json_encode($iotDevices, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) ?>,
  schema:  <?= json_encode($savedSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) ?>,
  accessToken: <?= json_encode($savedAccessToken, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) ?>,
  accessTokenSource: 'efactorproduct'
};

(function () {
  'use strict';

  var root        = document.getElementById('iot-dashboard-root');
  if (!root) return;
  var apiBase     = root.getAttribute('data-api-base') || '';
  var v8ApiPath   = root.getAttribute('data-v8-api-path') || '';
  var v8DeviceApiPath = root.getAttribute('data-v8-device-api-path') || 'https://efactor.tech/v8/iot_api_creation.php';
  var grid        = document.getElementById('iotDeviceGrid');
  var searchInput = document.getElementById('iotDeviceSearch');
  var searchToggle = document.getElementById('iotSearchToggleBtn');
  var searchBar = document.getElementById('iotDeviceSearchBar');
  var searchQuery = '';
  var output      = document.getElementById('iotSchemaOutput');
  var summary     = document.getElementById('iotSummaryText');
  var detailPanel = document.getElementById('iotDetailPanel');
  var tokenInput  = document.getElementById('iotAccessTokenInput');
  var schemaApiEl = document.getElementById('iotSchemaApiUrl');
  var deviceApiEl = document.getElementById('iotDeviceApiUrl');
  var csrfName    = root.getAttribute('data-csrf-name') || (window.AppHelper && AppHelper.csrfTokenName) || '';
  var csrfHash    = root.getAttribute('data-csrf-hash') || (window.AppHelper && AppHelper.csrfHash) || '';

  var boot    = window.IOT_DASHBOARD_BOOT || {};
  var devices = Array.isArray(boot.devices) ? boot.devices : [];

  /* ── sort: online devices first ──────────────────────────── */
  function sortDevicesOnlineFirst(list) {
    return list.slice().sort(function (a, b) {
      var ao = a.online ? 1 : 0;
      var bo = b.online ? 1 : 0;
      if (ao !== bo) return bo - ao;
      var an = String(a.product_name || a.productName || a.name || '').toLowerCase();
      var bn = String(b.product_name || b.productName || b.name || '').toLowerCase();
      return an.localeCompare(bn);
    });
  }

  devices = sortDevicesOnlineFirst(devices);

  /* ── escape ──────────────────────────────────────────────── */
  function esc(v) {
    return String(v == null ? '' : v)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;')
      .replace(/'/g,'&#039;');
  }

  /* ── parse device fields ─────────────────────────────────── */
  function isPowerOn(value) {
    if (value === true || value === 1) return true;
    if (value === false || value === 0 || value == null) return false;
    var s = String(value).toLowerCase().trim();
    return s === '1' || s === '01' || s === 'true' || s === 'yes' || s === 'on';
  }

  function isOnlineState(d) {
    if (d.online === true || d.online === 1) return true;
    if (d.online === false || d.online === 0) return false;
    var s = String(d.status || d.online || '').toLowerCase().trim();
    return s === 'online' || s === '1' || s === 'true' || s === 'yes' || s === 'connected';
  }

  function parse(d) {
    var online = isOnlineState(d);
    var powerOn = isPowerOn(d.power) || isPowerOn(d.powerOn);
    return {
      productName  : d.product_name  || d.productName  || d.name        || 'Device',
      deviceType   : d.devicetype    || d.device_type  || d.productName || '',
      brandName    : d.brandName     || d.brand_name   || d.vendor_name || 'Efactor',
      brandLogo    : d.brandLogo     || d.brand_logo   || '',
      productImage : d.productImage  || d.product_image || '',
      nodeId       : d.node_id       || d.id            || '',
      status       : d.status        || (online ? 'Online' : 'Offline'),
      online       : online,
      powerOn      : online ? powerOn : false,
      modelName    : d.modelName     || d.model_name    || '',
      modelNumber  : d.modelNumber   || d.model_number  || '',
    };
  }

  function syncCardPowerState(card, device, powerOn) {
    if (!card || !device) return;
    // Keep online/offline independent from power on/off.
    var online = !!device.online;
    var nextPower = !!powerOn;
    device.power = nextPower;
    device.powerOn = nextPower;
    var btn = card.querySelector('.iot-toggle');
    var label = card.querySelector('.iot-power-label');
    if (btn) {
      btn.classList.toggle('is-on', nextPower);
      btn.setAttribute('aria-pressed', nextPower ? 'true' : 'false');
      btn.disabled = !online;
      btn.setAttribute('aria-disabled', online ? 'false' : 'true');
    }
    if (label) label.textContent = nextPower ? 'On' : 'Off';
  }

  /* ── brand logo (detail panel) ───────────────────────────── */
  function brandLogoEl(logo, name) {
    var initial = esc((name || 'E').charAt(0).toUpperCase());
    if (logo) {
      return '<img class="iot-detail-brand-logo" src="' + esc(logo) + '" alt="' + esc(name) + '" loading="lazy"'
           + ' onerror="this.style.display=\'none\'">';
    }
    return '<span class="iot-detail-brand-logo iot-brand-placeholder" style="width:28px;height:28px;font-size:12px;border-radius:6px">' + initial + '</span>';
  }

  /* ── card icon circle ────────────────────────────────────── */
  function cardIconEl(logo, brandName, deviceType) {
    var initial = esc((brandName || 'E').charAt(0).toUpperCase());
    var fallback = logo
      ? '<span class="iot-icon-fallback is-initial">' + initial + '</span>'
      : '<span class="iot-icon-fallback">' + deviceIconSvg(deviceType || brandName) + '</span>';
    var img = logo
      ? '<img class="iot-logo" src="' + esc(logo) + '" alt="' + esc(brandName) + '" loading="lazy" onerror="this.style.display=\'none\'">'
      : '';
    return '<div class="iot-card-icon">' + fallback + img + '</div>';
  }

  function productIconFallback(deviceType, productName) {
    return '<div class="iot-product-fallback" aria-hidden="true">' + deviceIconSvg(deviceType || productName) + '</div>';
  }

  /* ── product image (icon fallback when no URL or load error) ─ */
  function productImgEl(url, name, deviceType) {
    var iconFallback = productIconFallback(deviceType, name);
    if (url) {
      return '<div class="iot-product-wrap">'
           + '<img class="iot-product-image" src="' + esc(url) + '" alt="' + esc(name) + '" loading="lazy"'
           + ' onerror="this.closest(\'.iot-product-wrap\').classList.add(\'is-fallback\')">'
           + iconFallback
           + '</div>';
    }
    return '<div class="iot-product-wrap is-fallback">'
         + iconFallback
         + '</div>';
  }

  var signalSvg = '<svg viewBox="0 0 24 24" aria-hidden="true">'
    + '<path d="M5 12.55a11 11 0 0 1 14.08 0"/>'
    + '<path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>'
    + '<circle cx="12" cy="20" r="1" fill="currentColor" stroke="none"/>'
    + '</svg>';

  /* ── device-type → SVG icon map ─────────────────────────── */
  function deviceIconSvg(type) {
    var t = (type || '').toLowerCase();

    /* fan / air */
    if (/fan|air|ventil|exhaust/.test(t))
      return '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/>'
           + '<path d="M12 9c0-3-2.5-6-5-5.5C4 4 3 7 5 9c1 1 4 1 4 1s-1 3 1 5c1.5 2 4.5 2 5.5.5C17 14 15 11 12 9z"/>'
           + '<path d="M12 15c0 3 2.5 6 5 5.5C20 20 21 17 19 15c-1-1-4-1-4-1s1-3-1-5c-1.5-2-4.5-2-5.5-.5C7 10 9 13 12 15z"/></svg>';

    /* light / lamp / bulb */
    if (/light|lamp|bulb|led|tube/.test(t))
      return '<svg viewBox="0 0 24 24"><path d="M9 21h6M12 3a6 6 0 0 1 4.24 10.24C15.5 14 15 15 15 16H9c0-1-.5-2-1.24-2.76A6 6 0 0 1 12 3z"/><path d="M9 16v1a3 3 0 0 0 6 0v-1"/></svg>';

    /* plug / socket / switch */
    if (/plug|socket|switch|outlet|relay/.test(t))
      return '<svg viewBox="0 0 24 24"><rect x="6" y="3" width="12" height="13" rx="2"/>'
           + '<path d="M9 7v3M15 7v3"/>'
           + '<path d="M12 16v5M8 21h8"/></svg>';

    /* camera / security */
    if (/camera|cam|cctv|security|monitor/.test(t))
      return '<svg viewBox="0 0 24 24"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3z"/>'
           + '<circle cx="12" cy="13" r="3"/></svg>';

    /* lock / door */
    if (/lock|door|gate|access/.test(t))
      return '<svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/>'
           + '<path d="M7 11V7a5 5 0 0 1 10 0v4"/>'
           + '<circle cx="12" cy="16" r="1.5" fill="currentColor" stroke="none"/></svg>';

    /* thermostat / temp / heat / ac */
    if (/thermo|temp|heat|cool|hvac|ac|climate/.test(t))
      return '<svg viewBox="0 0 24 24"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/></svg>';

    /* sensor / motion / pir */
    if (/sensor|motion|pir|detect/.test(t))
      return '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="2"/>'
           + '<path d="M16.24 7.76a6 6 0 0 1 0 8.49M7.76 16.24a6 6 0 0 1 0-8.49"/>'
           + '<path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 19.07a10 10 0 0 1 0-14.14"/></svg>';

    /* speaker / audio */
    if (/speaker|audio|sound|music|alarm/.test(t))
      return '<svg viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2"/>'
           + '<circle cx="12" cy="14" r="4"/><circle cx="12" cy="14" r="1.5" fill="currentColor" stroke="none"/>'
           + '<circle cx="12" cy="6" r="1"/></svg>';

    /* TV / screen / display */
    if (/tv|tele|screen|display|projector/.test(t))
      return '<svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/>'
           + '<path d="M8 21h8M12 17v4"/></svg>';

    /* cooker / rice / oven / microwave / kitchen */
    if (/cook|rice|oven|micro|kitchen|grill|toaster|kettle|coffee/.test(t))
      return '<svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/>'
           + '<path d="M7 5V3M12 5V3M17 5V3"/>'
           + '<circle cx="8" cy="12" r="2.5"/>'
           + '<path d="M13 10h5M13 14h5"/></svg>';

    /* washer / dryer / machine */
    if (/wash|dryer|laundry|machine/.test(t))
      return '<svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/>'
           + '<circle cx="12" cy="13" r="4"/>'
           + '<path d="M8 6h.01M11 6h.01M14 6h.01"/></svg>';

    /* router / gateway / hub / wifi */
    if (/router|gateway|hub|wifi|network|bridge/.test(t))
      return '<svg viewBox="0 0 24 24"><path d="M5 12.55a11 11 0 0 1 14.08 0"/>'
           + '<path d="M1.42 9a16 16 0 0 1 21.16 0"/>'
           + '<path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>'
           + '<circle cx="12" cy="20" r="1.5" fill="currentColor" stroke="none"/></svg>';

    /* scanner / barcode / qr */
    if (/scan|barcode|qr/.test(t))
      return '<svg viewBox="0 0 24 24"><path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2"/>'
           + '<path d="M7 8v8M12 8v8M17 8v8"/></svg>';

    /* battery / energy / power meter */
    if (/battery|energy|meter|power|watt/.test(t))
      return '<svg viewBox="0 0 24 24"><rect x="2" y="7" width="16" height="10" rx="2"/>'
           + '<path d="M22 11v2"/>'
           + '<path d="M7 11v2M11 11v2"/></svg>';

    /* default — generic device/chip */
    return '<svg viewBox="0 0 24 24"><rect x="7" y="7" width="10" height="10" rx="1"/>'
         + '<path d="M9 3v2M12 3v2M15 3v2M9 19v2M12 19v2M15 19v2M3 9h2M3 12h2M3 15h2M19 9h2M19 12h2M19 15h2"/></svg>';
  }

  /* ── build one card ──────────────────────────────────────── */
  function buildCard(d, i) {
    var p      = parse(d);
    var cls    = p.online ? ' is-online' : ' is-offline';
    var stateLabel = p.online ? 'Online' : 'Offline';
    var powerLabel = p.online ? (p.powerOn ? 'On' : 'Off') : 'Off';
    var toggleCls  = p.powerOn ? ' is-on' : '';
    var typeHtml = (p.deviceType && p.deviceType !== p.productName)
      ? '<p class="iot-device-type">' + esc(p.deviceType) + '</p>'
      : '';

    return (
      '<article class="iot-device-card' + cls + '" tabindex="0" role="listitem"'
    + ' data-device-index="' + i + '" data-node-id="' + esc(p.nodeId) + '"'
    + ' aria-label="' + esc(p.productName) + ', ' + esc(p.brandName) + ', ' + stateLabel + '">'

    + '<div class="iot-card-top">'
    +   cardIconEl(p.brandLogo, p.brandName, p.deviceType || p.productName)
    +   '<span class="iot-card-signal">' + signalSvg + '</span>'
    + '</div>'

    + '<div class="iot-card-body">'
    +   '<h3 class="iot-device-name">' + esc(p.productName) + '</h3>'
    +   '<p class="iot-brand-name">' + esc(p.brandName) + '</p>'
    +   typeHtml
    + '</div>'

    + productImgEl(p.productImage, p.productName, p.deviceType || p.productName)

    + '<div class="iot-status-line">' + esc(stateLabel) + '</div>'

    + '<div class="iot-card-footer">'
    +   '<span class="iot-power-label">' + esc(powerLabel) + '</span>'
    +   '<button type="button" class="iot-toggle' + toggleCls + '"'
    +     (p.online ? '' : ' disabled aria-disabled="true"')
    +     ' aria-pressed="' + (p.powerOn ? 'true' : 'false') + '"'
    +     ' aria-label="' + esc(p.online ? 'Toggle power for ' + p.productName : p.productName + ' is offline') + '"></button>'
    + '</div>'

    + '</article>'
    );
  }

  function updateStats() {
    var onlineCount = devices.filter(function (d) { return !!d.online; }).length;
    if (summary) summary.textContent = devices.length + ' products · ' + onlineCount + ' online';
    var statsOnline = document.getElementById('iotStatsOnline');
    if (statsOnline) statsOnline.innerHTML = onlineCount + ' <span>online</span>';
    var statsDate = document.getElementById('iotStatsDate');
    if (statsDate) {
      var now = new Date();
      statsDate.textContent = now.toLocaleDateString(undefined, { day: 'numeric', month: 'long', year: 'numeric' })
        + ' · ' + devices.length + ' total';
    }
  }

  function postPowerToggle(nodeId, turningOn) {
    var url = buildApiUrl('device_power_toggle');
    var token = getAccessToken();
    var headers = apiHeaders();
    headers['Content-Type'] = 'application/json';
    if (token) headers['X-IOT-Access-Token'] = token;
    return fetch(url, {
      method: 'POST',
      headers: headers,
      body: JSON.stringify({
        node_id: nodeId,
        command: turningOn ? 'on' : 'off',
        power: turningOn
      })
    }).then(parseApiResponse);
  }

  function toggleDevicePower(card, device) {
    if (!card || !device || !device.online) return;
    var btn = card.querySelector('.iot-toggle');
    if (!btn || btn.disabled) return;

    var nodeId = card.getAttribute('data-node-id') || device.node_id || '';
    var turningOn = !btn.classList.contains('is-on');
    syncCardPowerState(card, device, turningOn);
    btn.disabled = true;

    postPowerToggle(nodeId, turningOn)
      .then(function (data) {
        var nextPower = turningOn;
        if (data && typeof data.power !== 'undefined') {
          nextPower = isPowerOn(data.power);
        }
        syncCardPowerState(card, device, nextPower);
        document.dispatchEvent(new CustomEvent('iot:power-click', {
          detail: {
            node_id: nodeId,
            power: nextPower ? 'on' : 'off',
            command: (data && data.command) ? data.command : (nextPower ? 'on' : 'off')
          }
        }));
      })
      .catch(function () {
        syncCardPowerState(card, device, !turningOn);
      })
      .finally(function () {
        if (device.online) btn.disabled = false;
      });
  }

  function deviceMatchesQuery(d, q) {
    if (!q) return true;
    var p = parse(d);
    var hay = [
      p.productName, p.brandName, p.deviceType, p.nodeId, p.modelName, p.modelNumber, p.status
    ].join(' ').toLowerCase();
    return hay.indexOf(q) !== -1;
  }

  /* ── render grid ─────────────────────────────────────────── */
  function render() {
    if (!grid) return;
    devices = sortDevicesOnlineFirst(devices);
    if (!devices.length) {
      grid.innerHTML = '<div class="iot-empty-state"><strong>No devices found</strong><br>Devices will appear here when this profile has records in efactorproduct.</div>';
      updateStats();
      return;
    }
    var q = searchQuery.trim().toLowerCase();
    var html = '';
    devices.forEach(function (d, i) {
      if (deviceMatchesQuery(d, q)) html += buildCard(d, i);
    });
    if (!html) {
      grid.innerHTML = '<div class="iot-empty-state"><strong>No matching devices</strong><br>Nothing matches “' + esc(searchQuery.trim()) + '”.</div>';
      updateStats();
      return;
    }
    grid.innerHTML = html;
    updateStats();
  }

  /* ── detail panel ────────────────────────────────────────── */
  function showDetail(d) {
    if (!detailPanel || !d) return;
    var p = parse(d);
    var logoHtml = p.brandLogo
      ? '<img class="iot-detail-brand-logo" src="' + esc(p.brandLogo) + '" alt="' + esc(p.brandName) + '" loading="lazy">'
      : '<span class="iot-detail-brand-logo" style="width:28px;height:28px;border-radius:6px;background:#111827;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:900">' + esc(p.brandName.charAt(0).toUpperCase()) + '</span>';
    var imgHtml = p.productImage
      ? '<img class="iot-detail-product-img" src="' + esc(p.productImage) + '" alt="' + esc(p.productName) + '" loading="lazy">'
      : '';
    detailPanel.innerHTML =
      '<span class="iot-panel-label">Selected Device</span>'
    + '<div class="iot-detail-brand-row">' + logoHtml + '<span class="iot-detail-brand-name">' + esc(p.brandName) + '</span></div>'
    + imgHtml
    + '<strong>' + esc(p.productName) + '</strong>'
    + '<span class="iot-detail-status ' + (p.online ? 'online' : 'offline') + '">' + esc(p.status) + '</span>'
    + '<dl style="margin:6px 0 0">'
    + (p.nodeId                             ? '<div class="iot-detail-row"><dt>Node</dt><dd>'    + esc(p.nodeId)      + '</dd></div>' : '')
    + (p.deviceType && p.deviceType !== p.productName
                                            ? '<div class="iot-detail-row"><dt>Type</dt><dd>'    + esc(p.deviceType)  + '</dd></div>' : '')
    + (p.modelName                          ? '<div class="iot-detail-row"><dt>Model</dt><dd>'   + esc(p.modelName)   + '</dd></div>' : '')
    + (p.modelNumber                        ? '<div class="iot-detail-row"><dt>Model #</dt><dd>' + esc(p.modelNumber) + '</dd></div>' : '')
    + '</dl>';
  }

  /* ── refresh ─────────────────────────────────────────────── */
  function refresh() {
    if (!apiBase) {
      console.warn('[IoT] refresh() skipped — apiBase not set.');
      return;
    }
    console.log('[IoT] refresh() → GET', apiBase + '/devices');
    fetch(apiBase + '/devices', { credentials: 'same-origin' })
      .then(function(r){ return r.json(); })
      .then(function(data){
        if (data && data.access_token) {
          applyProfileAccessToken(data.access_token);
        }
        if (data && data.success && Array.isArray(data.devices)) {
          devices = sortDevicesOnlineFirst(data.devices);
          console.log('[IoT] refresh() → got', devices.length, 'devices. online:', devices.filter(function(d){ return d.online; }).length);
          render();
        } else {
          console.warn('[IoT] refresh() → unexpected response:', data);
        }
      })
      .catch(function(err){
        console.error('[IoT] refresh() failed:', err && err.message ? err.message : err);
      });
  }

  /* ── access token → API URLs ─────────────────────────────── */
  function applyProfileAccessToken(token) {
    token = String(token || '').trim();
    if (!token || !tokenInput) return;
    tokenInput.value = token;
    tokenInput.title = token;
    updateApiUrls();
  }

  function getAccessToken() {
    if (tokenInput) {
      var value = String(tokenInput.value || '').trim();
      if (value !== '') return value;
    }
    return String(root.getAttribute('data-profile-token') || boot.accessToken || '').trim();
  }

  function buildApiUrl(action) {
    var token = getAccessToken();
    if (action === 'get_iot_schema' && !token && apiBase) {
      return apiBase.replace(/\/+$/, '') + '/schema';
    }
    var base = v8ApiPath.replace(/\/+$/, '');
    var url = base + '?action=' + encodeURIComponent(action);
    // Include access_token in the display/copy URL so it works when opened directly in a browser.
    if (token) url += '&access_token=' + encodeURIComponent(token);
    return url;
  }

  function apiHeaders() {
    var headers = {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    };
    if (csrfHash) {
      headers['X-CSRF-TOKEN'] = csrfHash;
    }
    return headers;
  }

  function apiJsonBody(payload) {
    var body = payload || {};
    if (csrfName && csrfHash && body[csrfName] === undefined) {
      body[csrfName] = csrfHash;
    }
    return JSON.stringify(body);
  }

  function parseApiResponse(r) {
    return r.text().then(function (text) {
      var data = null;
      try {
        data = text ? JSON.parse(text) : null;
      } catch (e) {
        data = null;
      }
      if (data && data.success !== true && (data.status === 'success' || data.prod_category != null)) {
        data.success = true;
      }
      if (!r.ok) {
        if (data && data.success === true) {
          data.httpStatus = r.status;
          data.warning = data.warning || ('Server returned HTTP ' + r.status + ', but the operation completed successfully.');
          return data;
        }
        var msg = (data && (data.message || data.description || data.error))
          ? (data.message || data.description || data.error)
          : 'Request failed.';
        throw new Error('Failed (HTTP ' + r.status + '): ' + msg);
      }
      return data || {};
    });
  }

  function updateApiUrls() {
    if (schemaApiEl) schemaApiEl.textContent = buildApiUrl('get_iot_schema');
    if (deviceApiEl) deviceApiEl.textContent = buildApiUrl('get_iot_devices');
  }

  function loadSavedAccessToken() {
    if (!apiBase || !tokenInput) return;
    if (boot.accessToken) {
      applyProfileAccessToken(boot.accessToken);
    }
    fetch(apiBase + '/access-token', { credentials: 'same-origin', headers: apiHeaders() })
      .then(parseApiResponse)
      .then(function (data) {
        if (data && data.access_token) {
          applyProfileAccessToken(data.access_token);
          if (data.source) {
            var hint = document.querySelector('.iot-token-hint');
            if (hint) {
              hint.innerHTML = 'Auto-loaded from <strong>' + esc(data.source) + '</strong> for this profile.';
            }
          }
        }
      })
      .catch(function () { /* keep server-rendered value */ });
  }

  function copyApiUrl(targetId, btn) {
    var el = document.getElementById(targetId);
    var text = el ? String(el.textContent || '').trim() : '';
    if (!text) {
      alert('No API URL available to copy.');
      return;
    }

    function markCopied() {
      if (!btn) return;
      var prev = btn.textContent;
      btn.classList.add('is-copied');
      btn.textContent = 'Copied!';
      setTimeout(function () {
        btn.classList.remove('is-copied');
        btn.textContent = prev || 'Copy API';
      }, 1600);
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(markCopied).catch(function () {
        fallbackCopy(text);
        markCopied();
      });
      return;
    }

    fallbackCopy(text);
    markCopied();
  }

  function fallbackCopy(text) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.setAttribute('readonly', '');
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); } catch (e) { /* ignore */ }
    document.body.removeChild(ta);
  }

  /* ── generate schema ─────────────────────────────────────── */
  function generateSchema() {
    var btn = document.getElementById('iotGenerateSchemaBtn');
    if (!apiBase || (btn && btn.disabled)) return;

    var accessToken = getAccessToken();

    if (btn) btn.disabled = true;
    fetch(apiBase + '/generate-schema', {
      method: 'POST',
      credentials: 'same-origin',
      headers: apiHeaders(),
      body: apiJsonBody(accessToken ? { access_token: accessToken } : {})
    })
      .then(parseApiResponse)
      .then(function(data){
        if (data && data.schema && output) output.value = JSON.stringify(data.schema, null, 2);
        if (data && data.access_token && tokenInput) {
          tokenInput.value = data.access_token;
          updateApiUrls();
        }
        if (data && data.success === false) {
          alert(data.message || 'Could not save schema.');
          return;
        }
        alert((data && data.message) || 'Schema generated.');
      })
      .catch(function(err){
        alert(err && err.message ? err.message : 'Could not generate schema.');
      })
      .finally(function(){ if (btn) btn.disabled = false; });
  }

  /* ── add device via QR ───────────────────────────────────── */
  var qrModal = document.getElementById('iotQrModal');
  var qrReaderEl = document.getElementById('iotQrReader');
  var qrStatusEl = document.getElementById('iotQrStatus');
  var qrCloseBtn = document.getElementById('iotQrCloseBtn');
  var qrCancelBtn = document.getElementById('iotQrCancelBtn');
  var qrManualBtn = document.getElementById('iotQrManualBtn');
  var qrManualModal = document.getElementById('iotQrManualModal');
  var qrManualCancelBtn = document.getElementById('iotQrManualCancelBtn');
  var qrManualConnectBtn = document.getElementById('iotQrManualConnectBtn');
  var qrNodeIdInput = document.getElementById('iotQrNodeIdInput');
  var qrNodeIdError = document.getElementById('iotQrNodeIdError');
  var qrStepsEl = document.getElementById('iotQrSteps');
  var addDeviceBtn = document.getElementById('iotAddDeviceBtn');
  var qrScanner = null;
  var qrBusy = false;

  function setQrStep(stepName) {
    if (!qrStepsEl) return;
    var order = ['scan', 'checkDevice', 'addWifiNode'];
    var activeIndex = order.indexOf(stepName);
    qrStepsEl.querySelectorAll('.iot-qr-step').forEach(function (el) {
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
    if (!qrStatusEl) return;
    var textEl = qrStatusEl.querySelector('.iot-qr-status-text');
    if (textEl) textEl.textContent = message || '';
    else qrStatusEl.textContent = message || '';
    qrStatusEl.classList.remove('is-error', 'is-success', 'is-warning');
    if (type === 'error') qrStatusEl.classList.add('is-error');
    if (type === 'success') qrStatusEl.classList.add('is-success');
    if (type === 'warning') qrStatusEl.classList.add('is-warning');
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
        if (fromQuery) return normalizeQrNodeId(fromQuery);
        var parts = url.pathname.split('/').filter(Boolean);
        if (parts.length) return normalizeQrNodeId(parts[parts.length - 1]);
      } catch (e2) { /* keep raw */ }
    }
    var uidMatch = value.match(/(?:^|\b)UID\s*:\s*([A-Za-z0-9_-]{16,64})/i);
    if (uidMatch && uidMatch[1]) return uidMatch[1].trim();
    var nodeMatch = value.match(/(?:node_id|nodeId|node)\s*[:=]\s*([A-Za-z0-9_-]{16,64})/i);
    if (nodeMatch && nodeMatch[1]) return nodeMatch[1].trim();
    var compactMatch = value.match(/\b([A-Fa-f0-9]{20,64})\b/);
    if (compactMatch && compactMatch[1]) return compactMatch[1].trim();
    if (value.indexOf('socket_') === 0) return value.slice(7).trim();
    return value.replace(/[^A-Za-z0-9_-]/g, '').trim();
  }

  function getCurrentLocation() {
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
        { enableHighAccuracy: true, timeout: 12000, maximumAge: 60000 }
      );
    });
  }

  function postDashboardApi(path, payload) {
    var body = payload || {};
    return fetch(apiBase.replace(/\/+$/, '') + '/' + path, {
      method: 'POST',
      credentials: 'same-origin',
      headers: apiHeaders(),
      body: apiJsonBody(body)
    }).then(parseApiResponse);
  }

  function loadFreshAccessToken() {
    if (!apiBase) return Promise.resolve(getAccessToken());
    console.log('[IoT] loadFreshAccessToken() → GET', apiBase + '/access-token');
    return fetch(apiBase.replace(/\/+$/, '') + '/access-token', {
      credentials: 'same-origin',
      headers: apiHeaders()
    }).then(parseApiResponse).then(function (data) {
      if (data && data.access_token) {
        applyProfileAccessToken(data.access_token);
        console.log('[IoT] loadFreshAccessToken() → token refreshed, source:', data.source || 'unknown');
      } else {
        console.warn('[IoT] loadFreshAccessToken() → no token in response:', data);
      }
      return getAccessToken();
    }).catch(function (err) {
      console.error('[IoT] loadFreshAccessToken() failed:', err && err.message ? err.message : err);
      return getAccessToken();
    });
  }

  function callV8DeviceApi(action, nodeId, loc) {
    var token = getAccessToken();
    // Only action goes in the URL query string (and node_id for add_wifi_node per API contract).
    // Token, latitude, longitude must NOT appear in the URL — token belongs in the
    // Authorization header; lat/lon belong in the JSON body.
    var url = v8DeviceApiPath.replace(/\/+$/, '') + '?action=' + encodeURIComponent(action);
    if (action === 'add_wifi_node') url += '&node_id=' + encodeURIComponent(nodeId);

    var latitude = (loc && loc.latitude) ? loc.latitude : '';
    var longitude = (loc && loc.longitude) ? loc.longitude : '';

    var headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    };
    if (token) {
      // Authorization header must use "Bearer <token>" format.
      headers['Authorization'] = 'Bearer ' + token;
    }

    var body = {
      node_id: nodeId,
      latitude: latitude,
      longitude: longitude
    };

    console.log('[IoT] callV8DeviceApi →', action, '| node_id:', nodeId, '| lat:', latitude, '| lon:', longitude, '| token present:', !!token);
    console.log('[IoT] callV8DeviceApi → URL:', url);

    return fetch(url, {
      method: 'POST',
      headers: headers,
      credentials: 'omit',
      body: JSON.stringify(body)
    }).then(function(r) {
      console.log('[IoT] callV8DeviceApi ←', action, '| HTTP', r.status);
      return parseApiResponse(r);
    }).then(function(data) {
      console.log('[IoT] callV8DeviceApi ←', action, '| response:', JSON.stringify(data));
      return data;
    }).catch(function(err) {
      console.error('[IoT] callV8DeviceApi ←', action, '| ERROR:', err && err.message ? err.message : err);
      throw err;
    });
  }

  function setManualNodeIdError(show) {
    var message = 'Please enter a Node ID.';
    var schemaInput = boot && boot.schema && boot.schema.overlays
      && boot.schema.overlays.qrScannerModal
      && boot.schema.overlays.qrScannerModal.manualEntry
      && boot.schema.overlays.qrScannerModal.manualEntry.input;
    if (schemaInput && schemaInput.errorMessage) {
      message = String(schemaInput.errorMessage);
    }
    if (qrNodeIdInput) {
      qrNodeIdInput.classList.toggle('is-error', !!show);
    }
    if (qrNodeIdError) {
      qrNodeIdError.textContent = message;
      qrNodeIdError.hidden = !show;
      qrNodeIdError.classList.toggle('is-visible', !!show);
    }
  }

  function closeManualPrompt() {
    if (qrManualModal) {
      qrManualModal.classList.remove('is-open');
      qrManualModal.setAttribute('aria-hidden', 'true');
    }
    if (qrNodeIdInput) {
      qrNodeIdInput.value = '';
    }
    setManualNodeIdError(false);
  }

  function openManualPrompt() {
    if (!qrManualModal) return;
    qrManualModal.classList.add('is-open');
    qrManualModal.setAttribute('aria-hidden', 'false');
    if (qrNodeIdInput) {
      qrNodeIdInput.value = '';
      setManualNodeIdError(false);
      setTimeout(function () { qrNodeIdInput.focus(); }, 30);
    }
  }

  function submitManualNodeId() {
    var raw = qrNodeIdInput ? String(qrNodeIdInput.value || '').trim() : '';
    if (!raw) {
      setManualNodeIdError(true);
      if (qrNodeIdInput) qrNodeIdInput.focus();
      return;
    }
    setManualNodeIdError(false);
    closeManualPrompt();
    handleQrScan(raw, 'manual');
  }

  function stopQrScanner() {
    if (!qrScanner) return Promise.resolve();
    var instance = qrScanner;
    qrScanner = null;
    return instance.stop().catch(function () { return instance.clear(); }).then(function () {
      if (qrReaderEl) qrReaderEl.innerHTML = '';
    });
  }

  function closeQrModal() {
    if (!qrModal) return;
    closeManualPrompt();
    qrModal.classList.remove('is-open');
    qrModal.setAttribute('aria-hidden', 'true');
    qrBusy = false;
    stopQrScanner();
    setQrStep('scan');
    setQrStatus('Point your camera at the device QR code…', '');
  }

  function openQrModal() {
    if (!qrModal || !qrReaderEl || typeof Html5Qrcode !== 'function') {
      alert('QR scanner is unavailable in this browser.');
      return;
    }
    qrModal.classList.add('is-open');
    qrModal.setAttribute('aria-hidden', 'false');
    setQrStep('scan');
    setQrStatus('Point your camera at the device QR code…', '');

    qrScanner = new Html5Qrcode('iotQrReader');
    qrScanner.start(
      { facingMode: 'environment' },
      { fps: 10, qrbox: { width: 250, height: 250 } },
      function (decodedText) { handleQrScan(decodedText); },
      function () { /* ignore scan noise */ }
    ).catch(function (err) {
      setQrStatus((err && err.message) ? err.message : 'Could not start camera.', 'error');
    });
  }

  function handleQrScan(rawValue, source) {
    if (qrBusy) return;
    var nodeId = normalizeQrNodeId(rawValue);
    if (!nodeId) {
      setQrStatus(source === 'manual' ? 'Enter a valid node ID.' : 'Invalid QR code.', 'error');
      return;
    }

    console.log('[IoT] handleQrScan → raw:', rawValue, '| normalised node_id:', nodeId, '| source:', source || 'scan');
    qrBusy = true;
    setQrStep('checkDevice');
    setQrStatus(source === 'manual' ? 'Node ID entered. Verifying device…' : 'QR scanned. Verifying device…', '');
    stopQrScanner();

    getCurrentLocation().then(function (loc) {
      console.log('[IoT] location →', loc.latitude || 'unavailable', loc.longitude || 'unavailable');
      return loadFreshAccessToken().then(function (token) {
        if (!token) {
          throw new Error('No access token found. Sign in again, then retry.');
        }
        console.log('[IoT] step 1 → checkDevice');
        return callV8DeviceApi('check_device', nodeId, loc).then(function (checkResult) {
          if (!checkResult || checkResult.success === false || checkResult.status === 'failure') {
            var checkMsg = (checkResult && (checkResult.message || checkResult.description))
              ? (checkResult.message || checkResult.description)
              : 'Device verification failed.';
            console.error('[IoT] checkDevice failed:', checkMsg, checkResult);
            throw new Error(checkMsg);
          }
          console.log('[IoT] step 1 → checkDevice OK:', checkResult);
          setQrStep('addWifiNode');
          setQrStatus('Verified. Adding WiFi node…', '');
          console.log('[IoT] step 2 → add_wifi_node');
          return callV8DeviceApi('add_wifi_node', nodeId, loc);
        }).then(function (addResult) {
          if (!addResult || (addResult.status !== 'success' && addResult.success !== true)) {
            var addMsg = (addResult && (addResult.message || addResult.description || addResult.error))
              ? (addResult.message || addResult.description || addResult.error)
              : 'Could not add device.';
            console.error('[IoT] add_wifi_node failed:', addMsg, addResult);
            throw new Error(addMsg);
          }
          console.log('[IoT] step 2 → add_wifi_node OK:', addResult);
          if (addResult.warning) {
            setQrStatus('Success with warning: ' + addResult.warning, 'warning');
          } else {
            setQrStatus('Success: Device added successfully.', 'success');
          }
          // If the add response already contains a refreshed device list, apply it immediately
          // so My Devices updates before the separate refresh() call completes.
          if (addResult.devices && Array.isArray(addResult.devices) && addResult.devices.length > 0) {
            console.log('[IoT] applying', addResult.devices.length, 'devices from add response');
            devices = sortDevicesOnlineFirst(addResult.devices);
            render();
          }
          refresh();
          setTimeout(closeQrModal, 900);
        });
      });
    }).catch(function (err) {
      var msg = err && err.message ? err.message : 'Add device failed.';
      console.error('[IoT] handleQrScan failed:', msg);
      setQrStatus(/^Failed \(HTTP/.test(msg) ? msg : ('Failed: ' + msg), 'error');
      qrBusy = false;
    });
  }

  /* ── events ──────────────────────────────────────────────── */
  document.getElementById('iotCopySchemaApiBtn')?.addEventListener('click', function () {
    copyApiUrl('iotSchemaApiUrl', this);
  });
  document.getElementById('iotCopyDeviceApiBtn')?.addEventListener('click', function () {
    copyApiUrl('iotDeviceApiUrl', this);
  });
  document.getElementById('iotGenerateSchemaBtn')?.addEventListener('click', generateSchema);
  document.getElementById('iotRefreshBtn')?.addEventListener('click', refresh);
  addDeviceBtn?.addEventListener('click', openQrModal);
  qrCloseBtn?.addEventListener('click', closeQrModal);
  qrCancelBtn?.addEventListener('click', closeQrModal);
  qrManualBtn?.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    openManualPrompt();
  });
  qrManualCancelBtn?.addEventListener('click', closeManualPrompt);
  qrManualConnectBtn?.addEventListener('click', submitManualNodeId);
  qrNodeIdInput?.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      submitManualNodeId();
    }
  });
  qrNodeIdInput?.addEventListener('input', function () {
    if (qrNodeIdInput && String(qrNodeIdInput.value || '').trim() !== '') {
      setManualNodeIdError(false);
    }
  });
  qrManualModal?.addEventListener('click', function (e) {
    if (e.target === qrManualModal) closeManualPrompt();
  });
  qrModal?.addEventListener('click', function (e) {
    if (e.target === qrModal) closeQrModal();
  });
  function setSearchOpen(open) {
    if (!searchBar || !searchToggle) return;
    searchBar.classList.toggle('is-open', open);
    searchToggle.classList.toggle('is-open', open);
    searchToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) {
      if (searchInput) searchInput.focus();
      return;
    }
    if (searchInput && searchInput.value) {
      searchInput.value = '';
      searchQuery = '';
      render();
    }
  }
  searchToggle?.addEventListener('click', function () {
    setSearchOpen(!searchBar.classList.contains('is-open'));
  });
  searchInput?.addEventListener('input', function () {
    searchQuery = searchInput.value || '';
    render();
  });
  searchInput?.addEventListener('search', function () {
    searchQuery = searchInput.value || '';
    render();
  });
  searchInput?.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') setSearchOpen(false);
  });
  tokenInput?.addEventListener('input', updateApiUrls);
  tokenInput?.addEventListener('change', updateApiUrls);
  document.getElementById('iotBackBtn')?.addEventListener('click', function(){
    window.location.href = root.getAttribute('data-dashboard-url') || './';
  });

  if (grid) {
    grid.addEventListener('click', function(e) {
      var toggleBtn = e.target.closest('.iot-toggle');
      var card     = e.target.closest('.iot-device-card');
      if (!card) return;
      var d = devices[Number(card.getAttribute('data-device-index'))] || null;
      if (toggleBtn) {
        e.stopPropagation();
        e.preventDefault();
        toggleDevicePower(card, d);
        return;
      }
      grid.querySelectorAll('.iot-device-card').forEach(function(c){ c.classList.remove('is-selected'); });
      card.classList.add('is-selected');
      showDetail(d);
      document.dispatchEvent(new CustomEvent('iot:device-card-click', { detail:{ node_id: card.getAttribute('data-node-id')||'' } }));
    });

    grid.addEventListener('keydown', function(e){
      if (e.key !== 'Enter' && e.key !== ' ') return;
      var card = e.target.closest('.iot-device-card');
      if (card) { e.preventDefault(); card.click(); }
    });
  }

  /* ── pointer scroll ──────────────────────────────────────── */
  root.addEventListener('wheel', function(e) {
    var target = e.target.closest('.iot-phone-screen, .iot-schema-output');
    if (!target || !root.contains(target)) return;
    var max = target.scrollHeight - target.clientHeight;
    if (max <= 1) return;
    if ((e.deltaY < 0 && target.scrollTop <= 0) || (e.deltaY > 0 && target.scrollTop >= max - 1)) return;
    target.scrollTop += e.deltaY;
    e.preventDefault();
  }, { passive:false, capture:true });

  render();
  loadSavedAccessToken();
  updateApiUrls();

  var IOT_STATUS_POLL_MS = 12000;
  var iotStatusPollTimer = null;
  function startIotStatusPoll() {
    if (iotStatusPollTimer) clearInterval(iotStatusPollTimer);
    iotStatusPollTimer = setInterval(function () {
      if (!document.hidden) refresh();
    }, IOT_STATUS_POLL_MS);
  }
  function stopIotStatusPoll() {
    if (iotStatusPollTimer) {
      clearInterval(iotStatusPollTimer);
      iotStatusPollTimer = null;
    }
  }
  document.addEventListener('visibilitychange', function () {
    if (document.hidden) stopIotStatusPoll();
    else {
      refresh();
      startIotStatusPoll();
    }
  });
  startIotStatusPoll();
})();
</script>
