<div id="page-content" class="page-wrapper clearfix api-list-page">
  <div class="api-list-shell">
    <div class="api-list-header">
      <div>
        <p class="api-list-kicker">API Library</p>
        <h1>View APIs</h1>
      </div>
      <div class="api-list-header-actions">
        <?php if (!empty($modelno)): ?>
          <a href="<?php echo get_uri('items/modal_pages/' . (int) ($itemid ?? 0) . '?modelno=' . rawurlencode($modelno)); ?>" class="api-list-next">Add Pages</a>
        <?php endif; ?>
        <a href="<?php echo get_uri('items/modal_api/' . (int) ($itemid ?? 0)); ?>" class="api-list-next">Add API/Product</a>
        <a href="#" class="api-list-back" id="view-api-back">Back</a>
      </div>
    </div>

    <div class="api-list-card">
      <div class="api-list-card-title">
        <div>
          <h2>Saved API Configurations</h2>
          <p>Select a row to inspect its complete command, response, and error details.</p>
        </div>
      </div>
      <div class="table-responsive api-list-table-wrap">
        <table id="item-table" class="display" cellspacing="0" width="100%"></table>
      </div>
    </div>
  </div>
</div>

<!-- Full-Screen Modal -->
<div id="rowModal">
  <div class="api-preview-modal">
    <div class="api-preview-modal-header">
      <h3>FULL API DATA PREVIEW</h3>
      <button type="button" class="close-btn" aria-label="Close">&times;</button>
    </div>
    <div class="api-preview-modal-body" tabindex="-1">
      <div id="rowDetailsTable"></div>
    </div>
    <div class="api-preview-actions">
      <button type="button" class="api-update-btn api-preview-update">Update</button>
      <button type="button" class="api-update-btn api-preview-close">Close</button>
    </div>
  </div>
</div>

<style>
:root {
  --api-bg: #F8F5F2;
  --api-card: #FFFFFF;
  --api-graphite: #232946;
  --api-graphite-2: #2E3350;
  --api-sage: #5E8B6A;
  --api-copper: #D3925F;
  --api-border: #E5DDD5;
  --api-muted: #667085;
}

.api-list-header-actions { display: flex; align-items: center; gap: 10px; }
.api-list-next {
  display: inline-flex; align-items: center; justify-content: center;
  min-height: 42px; padding: 0 18px; border-radius: 10px;
  background: var(--api-sage); color: #fff; font-weight: 800; text-decoration: none;
}
.api-list-next:hover, .api-list-next:focus { color: #fff; background: #4d7758; }

.api-list-page {
  background: var(--api-bg);
  min-height: calc(100vh - 80px);
  padding: clamp(14px, 2vw, 28px);
  font-family: "Poppins", "Segoe UI", sans-serif;
}

.api-list-shell {
  width: min(100%, 1440px);
  margin: 0 auto;
}

.api-list-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  background: linear-gradient(135deg, var(--api-graphite), var(--api-graphite-2));
  color: #fff;
  border-radius: 18px 18px 0 0;
  padding: clamp(18px, 2.4vw, 28px);
  box-shadow: 0 18px 40px rgba(35, 41, 70, .18);
}

.api-list-kicker {
  margin: 0 0 6px;
  color: #E9D7C4;
  font-size: 12px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0;
}

.api-list-header h1 {
  margin: 0;
  color: #fff;
  font-size: clamp(24px, 3vw, 34px);
  font-weight: 800;
  line-height: 1.15;
}

.api-list-back,
.api-list-back:hover,
.api-list-back:focus {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 42px;
  padding: 0 18px;
  border-radius: 12px;
  background: rgba(255, 255, 255, .12);
  border: 1px solid rgba(255, 255, 255, .24);
  color: #fff;
  font-weight: 800;
  text-decoration: none;
}

.api-list-card {
  background: var(--api-card);
  border: 1px solid var(--api-border);
  border-top: 0;
  border-radius: 0 0 18px 18px;
  padding: clamp(16px, 2vw, 24px);
  box-shadow: 0 18px 44px rgba(35, 41, 70, .08);
}

.api-list-card-title {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 18px;
}

.api-list-card-title h2 {
  margin: 0 0 4px;
  color: var(--api-graphite);
  font-size: clamp(18px, 2vw, 24px);
  font-weight: 800;
}

.api-list-card-title p {
  margin: 0;
  color: var(--api-muted);
  font-size: 14px;
}

.api-list-table-wrap {
  width: 100%;
  overflow: auto;
  border: 1px solid var(--api-border);
  border-radius: 14px;
  background: #fff;
}

#item-table {
  width: 100% !important;
}

#item-table thead th {
  background: #F1ECE6;
  color: var(--api-graphite);
  font-weight: 800;
  border-bottom: 1px solid var(--api-border);
}

#item-table tbody td {
  vertical-align: middle;
}

#item-table .view-btn,
#item-table .api-action-btn,
#item-table .api-update-btn,
.api-preview-actions .api-update-btn {
  border: 0;
  color: #fff;
  border-radius: 10px;
  padding: 8px 14px;
  font-weight: 800;
  cursor: pointer;
  margin: 2px;
}

#item-table .view-btn,
#item-table .api-action-btn {
  background: var(--api-sage);
}

#item-table .view-btn:hover,
#item-table .api-action-btn:hover {
  background: #4E7859;
}

#item-table .api-edit-btn,
.api-preview-actions .api-preview-update {
  background: var(--api-graphite);
}

#item-table .api-delete-btn,
#item-table .api-delete-legacy-btn {
  background: #B45353;
}

#item-table .api-delete-btn:hover,
#item-table .api-delete-legacy-btn:hover {
  background: #963F3F;
}

#item-table .api-delete-btn:disabled,
#item-table .api-delete-legacy-btn:disabled,
#item-table .api-locked-btn,
#item-table .api-locked-btn:hover,
#item-table .api-locked-btn:disabled {
  background: #9CA3AF;
  opacity: 0.9;
  cursor: not-allowed;
}

.api-version-badge {
  display: inline-flex;
  align-items: center;
  margin-left: 8px;
  padding: 2px 8px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.02em;
  vertical-align: middle;
}

.api-version-v2 {
  background: rgba(94, 139, 106, 0.14);
  color: #3f6d4b;
}

.api-version-legacy {
  background: rgba(211, 146, 95, 0.18);
  color: #8a5a2d;
}

.api-preview-panel {
  margin: 0;
  padding: 16px;
  background: #FAF7F4;
  border: 1px solid var(--api-border);
  border-radius: 14px;
}

.api-preview-hero {
  display: grid;
  grid-template-columns: minmax(220px, 1fr) auto;
  gap: 16px;
  align-items: start;
  padding: 16px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--api-graphite), var(--api-graphite-2));
  color: #fff;
  margin-bottom: 14px;
}

.api-preview-hero h4 {
  margin: 0 0 6px;
  color: #fff;
  font-size: 18px;
  font-weight: 900;
}

.api-preview-hero p {
  margin: 0;
  color: #E9D7C4;
  font-weight: 700;
}

.api-preview-badges {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.api-preview-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  min-height: 30px;
  padding: 0 10px;
  border-radius: 999px;
  background: rgba(255,255,255,.12);
  border: 1px solid rgba(255,255,255,.2);
  color: #fff;
  font-size: 12px;
  font-weight: 900;
}

.api-preview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
  gap: 10px;
  margin-bottom: 14px;
}

.api-preview-kv {
  min-width: 0;
  padding: 11px 12px;
  border: 1px solid var(--api-border);
  border-radius: 10px;
  background: #fff;
}

.api-preview-label {
  display: block;
  margin-bottom: 4px;
  color: var(--api-copper);
  font-size: 11px;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0;
}

.api-preview-value {
  display: block;
  color: var(--api-graphite);
  font-size: 14px;
  font-weight: 800;
  overflow-wrap: anywhere;
}

.api-preview-section {
  margin-top: 14px;
  border: 1px solid var(--api-border);
  border-radius: 14px;
  background: #fff;
  overflow: hidden;
}

.api-preview-section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 13px 15px;
  background: #F1ECE6;
  border-bottom: 1px solid var(--api-border);
}

.api-preview-section-header h4 {
  margin: 0;
  color: var(--api-graphite);
  font-size: 16px;
  font-weight: 900;
}

.api-preview-section-count {
  color: var(--api-muted);
  font-size: 12px;
  font-weight: 900;
}

.api-preview-section-body {
  padding: 14px;
}

.api-preview-frame {
  border: 1px solid #E8DED4;
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 12px;
}

.api-preview-frame:last-child {
  margin-bottom: 0;
}

.api-preview-frame-title {
  padding: 10px 12px;
  background: #FBFAF8;
  color: var(--api-graphite);
  font-size: 13px;
  font-weight: 900;
  border-bottom: 1px solid #E8DED4;
}

.api-preview-table-wrap {
  overflow: auto;
}

.api-preview-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 640px;
}

.api-preview-table th,
.api-preview-table td {
  padding: 9px 10px;
  border-bottom: 1px solid #EFE7DF;
  text-align: left;
  vertical-align: top;
  font-size: 13px;
}

.api-preview-table th {
  width: 210px;
  color: var(--api-muted);
  background: #FFFCFA;
  font-weight: 900;
}

.api-preview-table td {
  color: #1B1E2B;
  font-weight: 700;
  overflow-wrap: anywhere;
}

.api-preview-row-chip {
  display: inline-flex;
  align-items: center;
  min-height: 24px;
  padding: 0 8px;
  border-radius: 999px;
  background: rgba(94,139,106,.14);
  color: #3F684A;
  font-size: 11px;
  font-weight: 900;
}

.api-preview-empty {
  padding: 18px;
  border: 1px dashed var(--api-border);
  border-radius: 12px;
  background: #fff;
  color: var(--api-muted);
  font-weight: 800;
}

.api-preview-raw {
  margin: 12px 0 0;
  padding: 14px;
  border: 1px solid var(--api-border);
  border-radius: 12px;
  background: #fff;
  color: #1B1E2B;
  font-family: Consolas, "Courier New", monospace;
  font-size: 12px;
  white-space: pre-wrap;
  word-break: break-word;
}

.api-preview-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  flex-wrap: wrap;
  margin-top: 16px;
}

.api-preview-actions .api-preview-close {
  background: rgba(35, 41, 70, .12);
  color: var(--api-graphite);
}

.dataTables_wrapper .dataTables_filter input,
.dataTables_wrapper .dataTables_length select {
  border: 1px solid var(--api-border);
  border-radius: 10px;
  min-height: 36px;
  padding: 6px 10px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
  background: var(--api-graphite) !important;
  color: #fff !important;
  border: 1px solid var(--api-graphite) !important;
  border-radius: 10px;
}

/* Modal overlay */
#rowModal {
  position: fixed;
  inset: 0;
  display: none;
  z-index: 9999;
  padding: 16px;
  overflow: hidden;
  overscroll-behavior: contain;
  align-items: center;
  justify-content: center;
  background: rgba(27,30,43,.72);
  backdrop-filter: blur(3px);
}

#rowModal.api-preview-open {
  display: flex !important;
}

#rowModal .api-preview-modal {
  position: relative;
  display: flex;
  flex-direction: column;
  width: min(1180px, calc(100vw - 32px));
  max-height: calc(100dvh - 32px);
  margin: 0 auto;
  background: #fff;
  border-radius: 18px;
  border: 1px solid var(--api-border);
  box-shadow: 0 24px 70px rgba(27,30,43,.28);
  overflow: hidden;
}

#rowModal .api-preview-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex: 0 0 auto;
  padding: clamp(16px, 2vw, 24px);
  padding-bottom: 12px;
  border-bottom: 1px solid var(--api-border);
}

#rowModal .api-preview-modal-header h3 {
  margin: 0;
  color: var(--api-graphite);
  font-size: clamp(18px, 2vw, 24px);
  font-weight: 900;
}

#rowModal .api-preview-modal-body {
  flex: 1 1 auto;
  min-height: 0;
  overflow-x: hidden;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
  overscroll-behavior: contain;
  padding: 0 clamp(16px, 2vw, 24px);
  outline: none;
}

#rowModal #rowDetailsTable {
  min-height: 0;
}

#rowModal .close-btn {
  position: static;
  flex: 0 0 auto;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  padding: 0;
  border: 0;
  background: transparent;
  cursor: pointer;
  font-size: 28px;
  line-height: 1;
  font-weight: bold;
  color: var(--api-graphite);
}

#rowModal .api-preview-actions {
  flex: 0 0 auto;
  margin-top: 0;
  padding: 16px clamp(16px, 2vw, 24px) clamp(16px, 2vw, 24px);
  border-top: 1px solid var(--api-border);
  background: #fff;
}

#rowDetailsTable td {
  padding: 9px 10px;
  border: 1px solid var(--api-border);
}
#rowDetailsTable tr:nth-child(odd) {
  background: #FAF7F4;
}

/* Prevent double confirmation */
.delete-confirm {
  pointer-events: none;
}

@media (max-width: 768px) {
  .api-list-page {
    padding: 10px;
  }

  .api-list-header,
  .api-list-card {
    border-radius: 14px;
  }

  .api-list-card {
    border-top: 1px solid var(--api-border);
    margin-top: 12px;
  }

  .api-list-back {
    width: 100%;
  }

  .api-list-header-actions { width: 100%; }
  .api-list-header-actions > a { flex: 1; }

  .api-list-card-title {
    align-items: flex-start;
    flex-direction: column;
  }

  #rowModal {
    padding: 10px;
  }

  #rowModal .api-preview-modal {
    width: calc(100vw - 20px);
    max-height: calc(100dvh - 20px);
    border-radius: 14px;
  }

  #rowModal .api-preview-modal-body {
    padding: 0 10px;
  }

  .api-preview-panel {
    padding: 10px;
  }

  .api-preview-hero {
    grid-template-columns: 1fr;
  }

  .api-preview-badges {
    justify-content: flex-start;
  }
}
</style>

<script>
var apiDeleteInProgress = false;

function escapePreviewHtml(value) {
  return String(value || '').replace(/[&<>"']/g, function(ch) {
    return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[ch];
  });
}

function normalizePreviewLabel(label) {
  return String(label || '')
    .replace(/\s+/g, ' ')
    .replace(/^NO\. OF /i, '')
    .trim();
}

function splitPreviewPair(line) {
  var index = line.indexOf(':');
  if (index === -1) return null;
  return {
    label: normalizePreviewLabel(line.slice(0, index)),
    value: line.slice(index + 1).trim() || 'N/A'
  };
}

function isPreviewNoise(line) {
  var clean = String(line || '').trim();
  return !clean || /^=+$/.test(clean) || /^-+$/.test(clean) || /^=+ END OF REPORT =+$/i.test(clean);
}

function parseApiPreview(content) {
  var lines = String(content || '').split(/\r?\n/);
  var parsed = {
    title: 'Full Product Configuration Summary',
    summary: [],
    counts: [],
    sections: [],
    raw: content || ''
  };
  var currentSection = null;
  var currentFrame = null;
  var currentRow = null;
  var beforeSection = true;

  function pushSection(title, count) {
    currentSection = {title: title, count: count || '', frames: []};
    parsed.sections.push(currentSection);
    currentFrame = null;
    currentRow = null;
    beforeSection = false;
  }

  function pushFrame(title) {
    if (!currentSection) pushSection('API Details', '');
    currentFrame = {title: title, details: [], rows: []};
    currentSection.frames.push(currentFrame);
    currentRow = null;
  }

  function pushRow(title) {
    if (!currentFrame) pushFrame('Frame Details');
    currentRow = {title: title, details: []};
    currentFrame.rows.push(currentRow);
  }

  lines.forEach(function(rawLine) {
    var line = String(rawLine || '').trim();
    if (isPreviewNoise(line)) return;

    if (/FULL PRODUCT CONFIGURATION SUMMARY|LEGACY PRODUCT API SUMMARY/i.test(line)) {
      parsed.title = line.replace(/\s+/g, ' ').trim();
      return;
    }

    var sectionMatch = line.match(/^\[SECTION\]\s+(.+?)(?:\s+\((.*?)\))?$/i);
    if (sectionMatch) {
      pushSection(sectionMatch[1], sectionMatch[2] || '');
      return;
    }

    if (/^(COMMAND|RESPONSE|ERROR)$/i.test(line)) {
      pushSection(line.toUpperCase(), '');
      pushFrame(line.toUpperCase() + ' Details');
      return;
    }

    if (/^---\s*Frame/i.test(line)) {
      pushFrame(line.replace(/^---\s*|\s*---$/g, ''));
      return;
    }

    if (/^(BYTEWISE|BITWISE)\s+ROW/i.test(line)) {
      pushRow(line);
      return;
    }

    var pair = splitPreviewPair(line);
    if (pair) {
      if (/COMMAND FRAMES|RESPONSE FRAMES|ERROR FRAMES/i.test(pair.label)) {
        parsed.counts.push(pair);
      } else if (currentRow) {
        currentRow.details.push(pair);
      } else if (currentFrame) {
        currentFrame.details.push(pair);
      } else if (beforeSection) {
        parsed.summary.push(pair);
      } else {
        if (!currentFrame) pushFrame('Frame Details');
        currentFrame.details.push(pair);
      }
    }
  });

  return parsed;
}

function renderPreviewPairs(pairs) {
  if (!pairs || !pairs.length) return '<div class="api-preview-empty">No values available.</div>';

  return '<div class="api-preview-grid">' + pairs.map(function(pair) {
    return '<div class="api-preview-kv">' +
      '<span class="api-preview-label">' + escapePreviewHtml(pair.label) + '</span>' +
      '<span class="api-preview-value">' + escapePreviewHtml(pair.value) + '</span>' +
    '</div>';
  }).join('') + '</div>';
}

function renderPreviewTable(pairs) {
  if (!pairs || !pairs.length) return '';

  return '<div class="api-preview-table-wrap"><table class="api-preview-table"><tbody>' +
    pairs.map(function(pair) {
      return '<tr><th>' + escapePreviewHtml(pair.label) + '</th><td>' + escapePreviewHtml(pair.value) + '</td></tr>';
    }).join('') +
  '</tbody></table></div>';
}

function renderApiPreviewHtml(content) {
  var parsed = parseApiPreview(content);
  var hasStructuredData = parsed.summary.length || parsed.counts.length || parsed.sections.length;

  if (!hasStructuredData) {
    return '<div class="api-preview-panel"><pre class="api-preview-raw">' + escapePreviewHtml(content || '') + '</pre></div>';
  }

  var html = '<div class="api-preview-panel">';
  html += '<div class="api-preview-hero"><div><h4>' + escapePreviewHtml(parsed.title) + '</h4><p>Organized by product, frame, and field values.</p></div>';
  html += '<div class="api-preview-badges">';
  if (parsed.counts.length) {
    html += parsed.counts.map(function(count) {
      return '<span class="api-preview-badge">' + escapePreviewHtml(count.label) + ': ' + escapePreviewHtml(count.value) + '</span>';
    }).join('');
  } else {
    html += '<span class="api-preview-badge">API Preview</span>';
  }
  html += '</div></div>';

  html += renderPreviewPairs(parsed.summary);

  parsed.sections.forEach(function(section) {
    html += '<section class="api-preview-section">';
    html += '<div class="api-preview-section-header"><h4>' + escapePreviewHtml(section.title) + '</h4>';
    html += '<span class="api-preview-section-count">' + escapePreviewHtml(section.count ? section.count + ' item(s)' : section.frames.length + ' block(s)') + '</span></div>';
    html += '<div class="api-preview-section-body">';

    if (!section.frames.length) {
      html += '<div class="api-preview-empty">No blocks found in this section.</div>';
    }

    section.frames.forEach(function(frame) {
      html += '<article class="api-preview-frame">';
      html += '<div class="api-preview-frame-title">' + escapePreviewHtml(frame.title) + '</div>';
      html += renderPreviewTable(frame.details);

      frame.rows.forEach(function(row) {
        html += '<div class="api-preview-frame-title"><span class="api-preview-row-chip">' + escapePreviewHtml(row.title) + '</span></div>';
        html += renderPreviewTable(row.details);
      });

      html += '</article>';
    });

    html += '</div></section>';
  });

  html += '<details><summary class="api-preview-section-count">Raw text</summary><pre class="api-preview-raw">' + escapePreviewHtml(content || '') + '</pre></details>';
  html += '</div>';
  return html;
}

function showApiPreviewModal(content, configId, itemId) {
  var $modal = $('#rowModal');
  var $scrollBody = $modal.find('.api-preview-modal-body');

  $('#rowDetailsTable').html(renderApiPreviewHtml(content || ''));
  $('#rowModal .api-preview-update').attr('data-config-id', configId || '');
  $('#rowModal .api-preview-update').attr('data-item-id', itemId || '');
  $('#rowModal .api-preview-update').toggle(!!configId);

  $scrollBody.scrollTop(0);
  $modal.addClass('api-preview-open').stop(true, true).fadeIn(200, function() {
    if ($scrollBody[0]) {
      $scrollBody[0].focus({ preventScroll: true });
    }
  });
}

function closeApiPreviewModal() {
  $('#rowModal').stop(true, true).fadeOut(200, function() {
    $(this).removeClass('api-preview-open');
  });
}

function scrollApiPreviewBody(deltaY) {
  var body = document.querySelector('#rowModal .api-preview-modal-body');
  if (!body || body.scrollHeight <= body.clientHeight) {
    return;
  }

  var maxScroll = body.scrollHeight - body.clientHeight;
  var nextScroll = body.scrollTop + deltaY;

  if (nextScroll < 0) {
    nextScroll = 0;
  } else if (nextScroll > maxScroll) {
    nextScroll = maxScroll;
  }

  body.scrollTop = nextScroll;
}

$(document).ready(function(){
  var $tbl = $('#item-table');

  $tbl.appTable({
    source: '<?php echo get_uri("items/list_data2/" . (int) ($itemid ?? 0)) . "?itemid=" . (int) ($itemid ?? 0) . "&modelno=" . rawurlencode((string) ($modelno ?? "")); ?>',
    order: [[0, 'desc']],
    columns: [
      {
        title: "Parameter Name",
        class: "text-left",
        render: function(data){
          if (!data) return 'N/A';
          return data;
        }
      },
      {title: "Action", class: "text-center", orderable: false},
      {title: "Update", class: "text-center", orderable: false}
    ]
  });

  var dt = $tbl.DataTable();

  $('#item-table tbody').on('click', '.view-api-btn', function(e){
    e.preventDefault();
    e.stopPropagation();

    var configId = $(this).data('config-id');
    var itemId = $(this).data('item-id');
    var $btn = $(this);
    $btn.prop('disabled', true).text('Loading...');

    $.getJSON('<?php echo get_uri("items/api_config_preview"); ?>/' + configId)
      .done(function(result){
        if (result && result.success) {
          showApiPreviewModal(result.preview, configId, itemId);
        } else {
          alert((result && result.message) ? result.message : 'Unable to load API preview.');
        }
      })
      .fail(function(){
        alert('Unable to load API preview.');
      })
      .always(function(){
        $btn.prop('disabled', false).text('View API');
      });
  });

  $('#item-table tbody').on('click', '.view-legacy-api-btn', function(e){
    e.preventDefault();
    e.stopPropagation();

    var preview = $(this).data('preview') || 'No API preview available.';
    var itemId = $(this).data('item-id') || '<?php echo (int) ($itemid ?? 0); ?>';
    showApiPreviewModal(preview, '', itemId);
  });

  $('#item-table tbody').on('click', '.api-edit-btn', function(e){
    if ($(this).is(':disabled') || $(this).data('locked') == 1 || $(this).hasClass('api-locked-btn')) {
      e.preventDefault();
      e.stopPropagation();
      alert('This default API is read-only.');
      return false;
    }

    if (!$(this).data('config-id')) {
      return true;
    }

    e.preventDefault();
    e.stopPropagation();

    if (!confirm('Do you want to edit this API configuration?')) return false;

    var configId = $(this).data('config-id');
    var rowItemId = $(this).data('item-id') || '<?php echo (int) ($itemid ?? 0); ?>';
    window.location.href = '<?php echo get_uri("items/modal_api"); ?>/' + encodeURIComponent(rowItemId) + '?edit_config_id=' + encodeURIComponent(configId);
  });

  $('#item-table tbody').on('click', '.api-delete-btn', function(e){
    e.preventDefault();
    e.stopPropagation();

    if ($(this).is(':disabled') || $(this).data('locked') == 1 || $(this).hasClass('api-locked-btn')) {
      alert('Default APIs cannot be deleted.');
      return false;
    }

    if (apiDeleteInProgress) return false;
    if (!confirm('Are you sure you want to delete this API configuration?')) return false;

    apiDeleteInProgress = true;
    var configId = $(this).data('config-id');
    var $btn = $(this);
    $btn.prop('disabled', true).text('Deleting...');

    $.post('<?php echo get_uri("items/delete_api_config"); ?>/' + configId)
      .done(function(result){
        if (typeof result === 'string') {
          try { result = JSON.parse(result); } catch (err) {}
        }

        if (result && result.success) {
          dt.ajax.reload(null, false);
        } else {
          alert((result && result.message) ? result.message : 'Unable to delete API config.');
        }
      })
      .fail(function(){
        alert('Unable to delete API config.');
      })
      .always(function(){
        apiDeleteInProgress = false;
        $btn.prop('disabled', false).text('Delete');
      });

    return false;
  });

  $('#item-table tbody').on('click', '.api-delete-legacy-btn', function(e){
    e.preventDefault();
    e.stopPropagation();

    if (apiDeleteInProgress) return false;
    if (!confirm('Are you sure you want to delete this API configuration?')) return false;

    apiDeleteInProgress = true;
    var deleteUrl = $(this).data('delete-url');
    var $btn = $(this);
    $btn.prop('disabled', true).text('Deleting...');

    $.post(deleteUrl)
      .done(function(result){
        if (typeof result === 'string') {
          try { result = JSON.parse(result); } catch (err) {}
        }

        if (result && result.success) {
          dt.ajax.reload(null, false);
        } else {
          alert((result && result.message) ? result.message : 'Unable to delete API config.');
        }
      })
      .fail(function(){
        alert('Unable to delete API config.');
      })
      .always(function(){
        apiDeleteInProgress = false;
        $btn.prop('disabled', false).text('Delete');
      });

    return false;
  });

  $('#rowModal .api-preview-modal').on('click', '.close-btn, .api-preview-close', function(e){
    e.preventDefault();
    e.stopPropagation();
    closeApiPreviewModal();
  });

  $('#rowModal .api-preview-modal').on('click', '.api-preview-update', function(e){
    e.preventDefault();
    e.stopPropagation();

    if (!confirm('Do you want to edit this API configuration?')) return false;

    var configId = $(this).data('config-id');
    var rowItemId = $(this).data('item-id') || '<?php echo (int) ($itemid ?? 0); ?>';
    window.location.href = '<?php echo get_uri("items/modal_api"); ?>/' + encodeURIComponent(rowItemId) + '?edit_config_id=' + encodeURIComponent(configId);
  });

  $(document).on('click', function(e) {
    if ($(e.target).is('#rowModal')) {
      closeApiPreviewModal();
    }
  });

  var rowModalEl = document.getElementById('rowModal');
  if (rowModalEl) {
    rowModalEl.addEventListener('wheel', function(e) {
      if (!rowModalEl.classList.contains('api-preview-open')) {
        return;
      }

      scrollApiPreviewBody(e.deltaY);
      e.preventDefault();
    }, { passive: false, capture: true });
  }
});

function handleBackButton() {
  window.history.back();
}
(function () {
  var back = document.getElementById('view-api-back');
  if (!back || back.getAttribute('data-back-bound') === '1') {
    return;
  }
  back.setAttribute('data-back-bound', '1');
  back.addEventListener('click', function (event) {
    event.preventDefault();
    handleBackButton();
  });
})();
</script>
