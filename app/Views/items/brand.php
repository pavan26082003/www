<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
  /* ============================================ */
/* BRAND DIRECTORY - CLEANED & DEDUPLICATED     */
/* ============================================ */

#brand-page-wrapper {
  --ink: #13233d;
  --muted: #64748b;
  --line: #e6edf7;
  --panel: #fff;
  --soft: #f6f8fc;
  --primary: #0f766e;
  --primary-2: #0ea5a4;
  --primary-light: #14b8a6;
  --primary-lighter: #99f6e4;
  --primary-bg: #f0fdfa;
  --primary-dark: #0d5e56;
  --success: #059669;
  --danger: #dc2626;
  --shadow: 0 14px 36px rgba(34, 55, 92, .10);
  font-family: Inter, Arial, sans-serif;
  max-width: 1440px;
  margin: 0 auto;
  padding: 28px 24px;
  color: var(--ink);
  --panel-soft: #f6fafc;
  --canvas: #eef5f7;
  --accent: #e1f3f5;
}

#brand-page-wrapper *,
#brand-page-wrapper *:before,
#brand-page-wrapper *:after {
  box-sizing: border-box;
}

/* ============================================ */
/* HEADER */
/* ============================================ */

#brand-page-wrapper .brand-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  padding: 24px 28px;
  margin-bottom: 22px;
  position: relative;
  overflow: hidden;
  border: 1px solid #99f6e4;
  border-radius: 24px;
  background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 48%, #e6f7f6 100%);
  box-shadow: var(--shadow);
}

#brand-page-wrapper .brand-header:after {
  content: "";
  position: absolute;
  inset: auto -70px -90px auto;
  width: 220px;
  height: 220px;
  border-radius: 50%;
  background: radial-gradient(circle, #99f6e4 0, rgba(153, 246, 228, 0) 67%);
  pointer-events: none;
}

#brand-page-wrapper .brand-header-left {
  display: flex;
  align-items: center;
  gap: 16px;
  min-width: 0;
  position: relative;
  z-index: 1;
}

#brand-page-wrapper .brand-back-btn {
  width: 44px;
  height: 44px;
  display: grid;
  place-items: center;
  flex: 0 0 44px;
  border: 1px solid #dbe4f5;
  border-radius: 14px;
  background: #fff;
  color: #38506e;
  box-shadow: 0 6px 16px rgba(59, 82, 120, .08);
  cursor: pointer;
  transition: .2s ease;
}

#brand-page-wrapper .brand-back-btn:hover {
  color: #fff;
  background: var(--primary);
  border-color: var(--primary);
  transform: translateY(-2px);
}

#brand-page-wrapper .brand-header-icon {
  width: 52px;
  height: 52px;
  display: grid;
  place-items: center;
  flex: 0 0 52px;
  border-radius: 16px;
  color: #fff;
  font-size: 21px;
  background: linear-gradient(135deg, var(--primary), var(--primary-2));
  box-shadow: 0 10px 20px rgba(15, 118, 110, .26);
}

#brand-page-wrapper .brand-header-text {
  min-width: 0;
}

#brand-page-wrapper .brand-breadcrumb {
  display: flex;
  gap: 7px;
  align-items: center;
  margin: 0 0 5px;
  font-size: 12px;
  font-weight: 700;
  color: #7182a0;
}

#brand-page-wrapper .brand-breadcrumb i {
  font-size: 10px;
  color: #a7b4c9;
}

#brand-page-wrapper h1 {
  margin: 0;
  font-size: 28px;
  line-height: 1.15;
  letter-spacing: -.7px;
  font-weight: 800;
  color: #172b4d;
}

#brand-page-wrapper .brand-header-text p {
  margin: 6px 0 0;
  color: #6c7c95;
  font-size: 14px;
}

/* ============================================ */
/* ADD BRAND BUTTON */
/* ============================================ */

#brand-page-wrapper .brand-add-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 9px;
  min-height: 44px;
  padding: 0 20px;
  border: 1.5px solid #99f6e4;
  border-radius: 13px;
  font-family: inherit;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  white-space: nowrap;
  cursor: pointer;
  transition: transform .2s ease, box-shadow .2s ease, background .2s ease, color .2s ease;
  position: relative;
  z-index: 1;
  color: #0f766e;
  background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
  box-shadow: 0 4px 12px rgba(15, 118, 110, .10);
}

#brand-page-wrapper .brand-add-btn:hover {
  color: #fff;
  background: linear-gradient(135deg, #0d5e56, #0f766e);
  border-color: #0d5e56;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(13, 94, 86, .30);
}

#brand-page-wrapper .brand-add-btn i {
  color: #0f766e;
  font-size: 16px;
  transition: color .2s ease;
}

#brand-page-wrapper .brand-add-btn:hover i {
  color: #fff;
}

/* ============================================ */
/* ALERT */
/* ============================================ */

#brand-page-wrapper .brand-alert {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
  padding: 14px 16px;
  border: 1px solid #fecaca;
  border-radius: 14px;
  background: #fff5f5;
  color: #b42318;
  font-weight: 650;
}

/* ============================================ */
/* TABLE CARD */
/* ============================================ */

#brand-page-wrapper .brand-table-card {
  overflow: hidden;
  background: var(--panel);
  border: 1px solid var(--line);
  border-radius: 22px;
  box-shadow: var(--shadow);
  position: relative;
}

/* ============================================ */
/* DATATABLES WRAPPER */
/* ============================================ */

#brand-page-wrapper .dataTables_wrapper {
  padding: 0;
  background: #fff;
  position: relative;
}

/* ============================================ */
/* ENHANCED TOOLBAR */
/* ============================================ */

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced {
  display: flex !important;
  flex-wrap: nowrap !important;
  align-items: center !important;
  gap: 12px !important;
  padding: 10px 20px !important;
  border-bottom: 1px solid var(--line);
  background: linear-gradient(180deg, #f9fcfd 0%, #f1f8fa 100%) !important;
  overflow-x: auto;
  min-height: 56px;
  flex-shrink: 0;
  border-radius: 22px 22px 0 0;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .row,
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced [class*="col-"],
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .datatable-tools {
  display: contents !important;
}

/* Length control */
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_length {
  display: flex !important;
  align-items: center !important;
  flex: 0 0 auto !important;
  margin: 0 !important;
  padding: 0 !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_length label {
  display: flex !important;
  align-items: center !important;
  margin: 0 !important;
  gap: 6px !important;
  white-space: nowrap !important;
  font-size: 13px !important;
  font-weight: 600 !important;
  color: #4a5a7a !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_length select {
  height: 36px !important;
  padding: 0 28px 0 12px !important;
  border: 1px solid #dce5f2 !important;
  border-radius: 8px !important;
  background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364758b' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right 10px center !important;
  background-size: 12px !important;
  color: #243a5a !important;
  font: 600 13px Inter, Arial, sans-serif !important;
  outline: 0 !important;
  cursor: pointer !important;
  appearance: none !important;
  -webkit-appearance: none !important;
  min-width: 60px !important;
}

/* Column visibility button */
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .column-show-hide-popover {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  height: 36px !important;
  width: 36px !important;
  padding: 0 !important;
  border: 1px solid #dce5f2 !important;
  border-radius: 8px !important;
  background: #fff !important;
  color: #5a6f8f !important;
  cursor: pointer !important;
  transition: all .2s ease !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .column-show-hide-popover:hover {
  background: #f0f4fe !important;
  border-color: #b8c8e0 !important;
}

/* Search Box */
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter {
  display: flex !important;
  align-items: center !important;
  flex: 0 0 280px !important;
  flex-shrink: 0 !important;
  min-width: 240px !important;
  margin-left: auto !important;
  position: relative !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter label {
  width: 100% !important;
  margin: 0 !important;
  position: relative !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter input {
  width: 100% !important;
  height: 38px !important;
  max-width: none !important;
  margin-left: 0 !important;
  padding: 0 14px 0 40px !important;
  border: 1.5px solid #99f6e4 !important;
  border-radius: 11px !important;
  background: #fff !important;
  background-image: none !important;
  color: #134e4a !important;
  font: 500 13px/1 Inter, system-ui, sans-serif !important;
  letter-spacing: .01em !important;
  outline: 0 !important;
  box-shadow: 0 2px 8px rgba(15, 118, 110, .06) !important;
  transition: border-color .2s ease, box-shadow .2s ease !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter input::placeholder {
  color: #94a3b8 !important;
  font-weight: 500 !important;
  opacity: 1 !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter input:focus {
  border-color: #0f766e !important;
  box-shadow: 0 0 0 4px rgba(15, 118, 110, .12), 0 4px 14px rgba(15, 118, 110, .08) !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter::before {
  content: "\f002" !important;
  font-family: "Font Awesome 6 Free" !important;
  font-weight: 900 !important;
  position: absolute !important;
  left: 13px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  color: #0f766e !important;
  font-size: 14px !important;
  opacity: .7 !important;
  pointer-events: none !important;
  z-index: 2 !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter:focus-within::before {
  opacity: 1 !important;
}

/* Buttons Container */
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dt-buttons {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  flex: 0 0 auto !important;
  margin: 0 !important;
  padding: 0 !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .datatable-export {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  flex: 0 0 auto !important;
}

/* Button Base Styles */
#brand-page-wrapper .dt-toolbar-enhanced .dt-button {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 7px !important;
  min-height: 38px !important;
  height: 38px !important;
  padding: 0 15px !important;
  border: 1.5px solid #e2e8f0 !important;
  border-radius: 10px !important;
  background: #ffffff !important;
  color: #2d3f5e !important;
  font: 700 12px/1 Inter, system-ui, sans-serif !important;
  letter-spacing: .02em !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
  transition: all .25s ease !important;
  cursor: pointer !important;
  white-space: nowrap !important;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button i {
  font-size: 15px !important;
  transition: transform .3s ease !important;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button:hover {
  transform: translateY(-2px) !important;
  border-color: #b8c8e0 !important;
  box-shadow: 0 8px 20px rgba(15, 118, 110, .12) !important;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button:hover i {
  transform: scale(1.1) rotate(-5deg);
}

/* Excel Button */
#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-excel {
  background: linear-gradient(135deg, #ecfdf5, #d1fae5) !important;
  border: 1.5px solid #6ee7b7 !important;
  color: #047857 !important;
  box-shadow: 0 2px 8px rgba(5, 150, 105, .1) !important;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-excel i {
  color: #059669 !important;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-excel:hover {
  background: linear-gradient(135deg, #d1fae5, #a7f3d0) !important;
  border-color: #34d399 !important;
  box-shadow: 0 8px 20px rgba(5, 150, 105, .18) !important;
}

/* Print Button */
#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-print {
  background: linear-gradient(135deg, #fff, #f0fdfa) !important;
  border: 1.5px solid #99f6e4 !important;
  color: #0f766e !important;
  box-shadow: 0 2px 8px rgba(15, 118, 110, .08) !important;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-print i {
  color: #0f766e !important;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-print:hover {
  background: linear-gradient(135deg, #ecfdf5, #ccfbf1) !important;
  border-color: #2dd4bf !important;
  box-shadow: 0 8px 20px rgba(15, 118, 110, .16) !important;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button .brand-export-label {
  line-height: 1 !important;
}

/* Toggle View Button */
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .brand-toolbar-toggle-wrapper {
  display: flex !important;
  align-items: center !important;
  flex: 0 0 auto !important;
  margin-left: auto !important;
}

#brand-page-wrapper .brand-toolbar-toggle {
  border-color: #99f6e4 !important;
  background: linear-gradient(135deg, #f0fdfa, #ccfbf1) !important;
  color: #0f766e !important;
  box-shadow: 0 4px 12px rgba(15, 118, 110, .10) !important;
  min-height: 36px !important;
  height: 36px !important;
  padding: 0 16px !important;
  border-radius: 10px !important;
  font-size: 12px !important;
  font-weight: 600 !important;
  letter-spacing: 0.3px !important;
  position: relative !important;
  overflow: hidden !important;
  border: 1.5px solid #99f6e4 !important;
  transition: transform .2s ease, box-shadow .2s ease, background .2s ease, color .2s ease !important;
}

#brand-page-wrapper .brand-toolbar-toggle:before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0));
  pointer-events: none;
  border-radius: 10px;
}

#brand-page-wrapper .brand-toolbar-toggle:hover {
  color: #fff !important;
  transform: translateY(-2px) !important;
  box-shadow: 0 8px 20px rgba(13, 94, 86, .30) !important;
  background: linear-gradient(135deg, #0d5e56, #0f766e) !important;
  border-color: #0d5e56 !important;
}

#brand-page-wrapper .brand-toolbar-toggle:active {
  transform: translateY(0px) scale(0.97) !important;
}

#brand-page-wrapper .brand-toolbar-toggle i {
  font-size: 14px !important;
  margin-right: 5px !important;
  transition: color .2s ease, transform .3s ease !important;
  color: #0f766e !important;
}

#brand-page-wrapper .brand-toolbar-toggle:hover i {
  color: #fff !important;
  transform: rotate(15deg);
}

/* Default: Show "Grid" text, hide "Table" text */
#brand-page-wrapper .brand-toolbar-toggle .table-label {
  display: none !important;
}
#brand-page-wrapper .brand-toolbar-toggle .grid-label {
  display: inline !important;
}

/* When in grid mode (is-grid class): Show "Table" text, hide "Grid" text */
#brand-page-wrapper .brand-toolbar-toggle.is-grid .grid-label {
  display: none !important;
}
#brand-page-wrapper .brand-toolbar-toggle.is-grid .table-label {
  display: inline !important;
}

#brand-page-wrapper .brand-toolbar-toggle .grid-label,
#brand-page-wrapper .brand-toolbar-toggle .table-label {
  font-size: 12px !important;
  font-weight: 600 !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced #item-table_processing {
  display: none !important;
}

/* Select2 / Category dropdown */
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .select2-container {
  min-width: 120px !important;
  width: 140px !important;
  flex: 0 0 auto !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .DTTT_container {
  display: flex !important;
  align-items: center !important;
  flex: 0 0 auto !important;
  margin: 0 !important;
  padding: 0 !important;
}

/* ============================================ */
/* TABLE STYLES */
/* ============================================ */

#brand-page-wrapper .dt-table-area {
  padding: 8px 12px 12px !important;
}

#brand-page-wrapper .table-responsive {
  overflow-x: auto;
  overflow-y: hidden;
}

#brand-page-wrapper.brand-view-grid #item-table_wrapper table.dataTable,
#brand-page-wrapper.brand-view-grid #item-table_wrapper .dataTables_scroll,
#brand-page-wrapper.brand-view-grid #item-table_wrapper .dataTables_scrollHead,
#brand-page-wrapper.brand-view-grid #item-table_wrapper .dataTables_scrollBody,
#brand-page-wrapper .grid-mode-table {
  display: none !important;
}

#brand-page-wrapper.brand-view-table #brand-grid-view {
  display: none !important;
}

#brand-page-wrapper.brand-view-grid #brand-grid-view.active {
  display: grid !important;
}

#brand-page-wrapper table.dataTable {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate !important;
  border-spacing: 0 10px !important;
  table-layout: fixed !important;
  min-width: 100% !important;
}

#brand-page-wrapper table.dataTable thead th {
  padding: 12px 16px !important;
  border-bottom: 0 !important;
  background: #f6f8fc !important;
  color: #61728d !important;
  font-size: 11px !important;
  letter-spacing: .06em;
  text-transform: uppercase;
  font-weight: 800 !important;
  box-sizing: border-box !important;
}

#brand-page-wrapper table.dataTable tbody td {
  padding: 14px 16px !important;
  background: transparent !important;
  border: none !important;
  color: #31425d;
  font-size: 14px;
  vertical-align: middle !important;
  box-sizing: border-box !important;
}

#brand-page-wrapper table.dataTable tbody td:first-child {
  border-radius: 12px 0 0 12px !important;
}

#brand-page-wrapper table.dataTable tbody td:last-child {
  border-radius: 0 12px 12px 0 !important;
}

/* Column widths */
#brand-page-wrapper table.dataTable thead th.brand-col-name,
#brand-page-wrapper table.dataTable tbody td.brand-col-name {
  width: 38% !important;
  text-align: left !important;
}

#brand-page-wrapper table.dataTable thead th.brand-col-logo,
#brand-page-wrapper table.dataTable tbody td.brand-col-logo {
  width: 32% !important;
  text-align: center !important;
}

#brand-page-wrapper table.dataTable thead th.brand-col-actions,
#brand-page-wrapper table.dataTable tbody td.brand-col-actions {
  width: 30% !important;
  text-align: center !important;
}

/* Table rows */
#brand-page-wrapper table.dataTable tbody tr {
  background: #fff !important;
  border-radius: 12px !important;
  box-shadow: inset 0 0 0 1px #e2e8f0, 0 1px 4px rgba(15, 118, 110, .04) !important;
  transition: background .22s ease, box-shadow .22s ease !important;
}

#brand-page-wrapper table.dataTable tbody td {
  background: transparent !important;
  border: none !important;
  border-radius: 0 !important;
  color: #31425d !important;
  box-shadow: none !important;
  transition: color .22s ease !important;
}

#brand-page-wrapper table.dataTable tbody td:first-child {
  border-radius: 12px 0 0 12px !important;
}

#brand-page-wrapper table.dataTable tbody td:last-child {
  border-radius: 0 12px 12px 0 !important;
}

#brand-page-wrapper table.dataTable tbody tr:hover {
  background: linear-gradient(180deg, #f0fdfa 0%, #ecfdf5 100%) !important;
  box-shadow: inset 0 0 0 1px #5eead4, 0 6px 18px rgba(15, 118, 110, .14) !important;
}

#brand-page-wrapper table.dataTable tbody tr:hover td {
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
}

#brand-page-wrapper table.dataTable tbody tr:hover .brand-name-badge {
  color: #0f766e !important;
}

#brand-page-wrapper table.dataTable tbody tr:hover .brand-dot {
  box-shadow: 0 0 0 4px #ccfbf1 !important;
}

/* Brand name badge */
#brand-page-wrapper .brand-name-badge {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  font-weight: 750;
  color: #20324d;
}

#brand-page-wrapper .brand-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary), var(--primary-2));
  box-shadow: 0 0 0 4px #eef2ff;
}

/* Logo cells */
#brand-page-wrapper .brand-logo-cell,
#brand-page-wrapper .brand-logo-placeholder {
  width: 72px;
  height: 72px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  border: 1px solid #dfe7f4;
  border-radius: 16px;
  background: #f8faff;
  margin: 0 auto !important;
}

#brand-page-wrapper .brand-logo-cell img {
  display: block;
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  padding: 8px;
  transition: transform .25s ease;
}

#brand-page-wrapper tr:hover .brand-logo-cell img {
  transform: scale(1.08);
}

#brand-page-wrapper .brand-logo-placeholder {
  border-style: dashed;
  color: #8695ac;
  font-size: 23px;
  background: linear-gradient(135deg, #f7f9fd, #eef2ff);
}

#brand-page-wrapper .dataTables_wrapper .dataTables_scroll,
#brand-page-wrapper .dataTables_wrapper .dataTables_scrollHead,
#brand-page-wrapper .dataTables_wrapper .dataTables_scrollBody,
#brand-page-wrapper #item-table_wrapper {
  width: 100% !important;
  max-width: 100% !important;
}

/* ============================================ */
/* FOOTER / PAGINATION */
/* ============================================ */

#brand-page-wrapper .dataTables_wrapper>.dataTables_paginate,
#brand-page-wrapper .dataTables_wrapper>.dataTables_info {
  display: none !important;
}

#brand-page-wrapper .dt-footer-enhanced {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 14px !important;
  min-height: 70px;
  padding: 12px 20px 16px;
  border-top: 1px solid #e3edf0;
  background: #ffffff;
  border-radius: 0 0 22px 22px;
  margin-top: 0;
}

#brand-page-wrapper .dt-footer-enhanced .dataTables_info,
#brand-page-wrapper .dt-footer-enhanced .dataTables_paginate {
  float: none !important;
  margin: 0 !important;
  padding: 8px 0 !important;
  display: flex !important;
  align-items: center !important;
}

#brand-page-wrapper .dt-footer-enhanced .dataTables_info {
  color: #657790 !important;
  font-size: 15px !important;
  font-weight: 600 !important;
  line-height: 1.45 !important;
}

#brand-page-wrapper .dt-footer-enhanced .dataTables_paginate {
  justify-content: flex-end !important;
  flex-wrap: wrap !important;
  gap: 4px !important;
}

#brand-page-wrapper .dt-footer-enhanced .paginate_button {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  min-width: 38px !important;
  height: 38px !important;
  margin: 0 2px !important;
  padding: 8px 12px !important;
  border: 1px solid #e0e7f1 !important;
  border-radius: 9px !important;
  background: #fff !important;
  color: #52647e !important;
  font-size: 14px !important;
  font-weight: 700 !important;
  cursor: pointer;
  transition: all 0.2s ease;
}

#brand-page-wrapper .dt-footer-enhanced .paginate_button.current,
#brand-page-wrapper .dt-footer-enhanced .paginate_button:hover:not(.disabled) {
  border-color: var(--primary) !important;
  background: var(--primary) !important;
  color: #fff !important;
}

#brand-page-wrapper .dt-footer-enhanced .paginate_button.disabled {
  opacity: 0.45 !important;
  cursor: default !important;
}

/* ============================================ */
/* ACTION BUTTONS */
/* ============================================ */

#brand-page-wrapper .option a::before,
#brand-page-wrapper .option button::before,
#brand-page-wrapper .brand-grid-actions a::before,
#brand-page-wrapper .brand-grid-actions button::before {
  display: none !important;
  content: none !important;
}

#brand-page-wrapper .option a,
#brand-page-wrapper .option button,
#brand-page-wrapper .brand-grid-actions a,
#brand-page-wrapper .brand-grid-actions button {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 8px !important;
  min-height: 34px !important;
  margin: 2px !important;
  padding: 6px 14px !important;
  border-radius: 10px !important;
  font: 700 13px Inter, Arial, sans-serif !important;
  text-decoration: none !important;
  transition: all .3s ease !important;
  border: 1px solid transparent !important;
  position: relative !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06) !important;
  letter-spacing: 0.3px !important;
}

#brand-page-wrapper .option a i,
#brand-page-wrapper .option button i,
#brand-page-wrapper .brand-grid-actions a i,
#brand-page-wrapper .brand-grid-actions button i {
  display: inline-flex !important;
  font-size: 15px !important;
  transition: transform .3s ease !important;
}

#brand-page-wrapper .option a:hover i,
#brand-page-wrapper .option button:hover i,
#brand-page-wrapper .brand-grid-actions a:hover i,
#brand-page-wrapper .brand-grid-actions button:hover i {
  transform: scale(1.15) !important;
}

#brand-page-wrapper .option a:hover,
#brand-page-wrapper .option button:hover,
#brand-page-wrapper .brand-grid-actions a:hover,
#brand-page-wrapper .brand-grid-actions button:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 8px 20px rgba(43, 65, 104, .15) !important;
}

/* View Button */
#brand-page-wrapper .option a[title*="View"],
#brand-page-wrapper .brand-grid-actions a[title*="View"] {
  background: linear-gradient(135deg, #e8f0fe, #dce6f9) !important;
  color: #2563a5 !important;
  border-color: #b8cfe8 !important;
}

#brand-page-wrapper .option a[title*="View"]:hover,
#brand-page-wrapper .brand-grid-actions a[title*="View"]:hover {
  background: linear-gradient(135deg, #dce6f9, #c8d8f0) !important;
  border-color: #8aacd0 !important;
  box-shadow: 0 8px 24px rgba(37, 99, 165, .20) !important;
}

/* Edit Button */
#brand-page-wrapper .option a[title*="Edit"],
#brand-page-wrapper .brand-grid-actions a[title*="Edit"] {
  background: linear-gradient(135deg, #e8f5ec, #d8f0e0) !important;
  color: #1a7a3f !important;
  border-color: #b8e0c4 !important;
}

#brand-page-wrapper .option a[title*="Edit"]:hover,
#brand-page-wrapper .brand-grid-actions a[title*="Edit"]:hover {
  background: linear-gradient(135deg, #1a7a3f, #2d8f4e) !important;
  color: #ffffff !important;
  border-color: #1a7a3f !important;
  box-shadow: 0 8px 24px rgba(26, 122, 63, .30) !important;
}

#brand-page-wrapper .option a[title*="Edit"]:hover i,
#brand-page-wrapper .brand-grid-actions a[title*="Edit"]:hover i {
  color: #ffffff !important;
}

/* Delete Button */
#brand-page-wrapper .option a[title*="Delete"],
#brand-page-wrapper .brand-grid-actions a[title*="Delete"] {
  background: linear-gradient(135deg, #fee8e8, #fcd4d4) !important;
  color: #b91c1c !important;
  border-color: #f5b8b8 !important;
}

#brand-page-wrapper .option a[title*="Delete"]:hover,
#brand-page-wrapper .brand-grid-actions a[title*="Delete"]:hover {
  background: linear-gradient(135deg, #b91c1c, #d32f2f) !important;
  color: #ffffff !important;
  border-color: #b91c1c !important;
  box-shadow: 0 8px 24px rgba(185, 28, 28, .30) !important;
}

#brand-page-wrapper .option a[title*="Delete"]:hover i,
#brand-page-wrapper .brand-grid-actions a[title*="Delete"]:hover i {
  color: #ffffff !important;
}

/* Action column container */
#brand-page-wrapper td.option,
#brand-page-wrapper td.brand-col-actions {
  display: table-cell !important;
  text-align: center !important;
  vertical-align: middle !important;
}

#brand-page-wrapper .brand-grid-actions,
#brand-page-wrapper .brand-action-row {
  display: inline-flex !important;
  flex-direction: row !important;
  flex-wrap: nowrap !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 6px !important;
  width: auto !important;
  max-width: 100% !important;
  white-space: nowrap !important;
}

#brand-page-wrapper .brand-action-row {
  margin: 0 auto !important;
}

#brand-page-wrapper td.option a,
#brand-page-wrapper td.option button,
#brand-page-wrapper .brand-grid-actions a,
#brand-page-wrapper .brand-grid-actions button,
#brand-page-wrapper .brand-action-row a,
#brand-page-wrapper .brand-action-row button {
  display: inline-flex !important;
  flex: 0 0 auto !important;
  width: auto !important;
  max-width: none !important;
  white-space: nowrap !important;
  margin: 0 !important;
  float: none !important;
}

#brand-page-wrapper td.option a.delete,
#brand-page-wrapper .brand-grid-actions a.delete {
  cursor: pointer;
}

/* ============================================ */
/* GRID VIEW */
/* ============================================ */

#brand-page-wrapper #brand-grid-view {
  display: none;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 18px;
  padding: 12px 0 16px;
  margin: 0 !important;
  background: #fbfdfe;
  border-top: 0 !important;
}

#brand-page-wrapper #brand-grid-view.active {
  display: grid;
}

#brand-page-wrapper .brand-grid-card {
  display: flex;
  flex-direction: column;
  min-height: 238px;
  padding: 20px;
  border: 1px solid #e6ebf4;
  border-radius: 18px;
  background: linear-gradient(180deg, #fff, #fbfcff);
  box-shadow: 0 7px 18px rgba(29, 48, 82, .06);
  transition: transform .24s ease, box-shadow .24s ease, border-color .24s ease, background .24s ease;
  cursor: default !important;
}

#brand-page-wrapper .brand-grid-card:hover {
  border-color: #5eead4 !important;
  box-shadow: 0 14px 34px rgba(15, 118, 110, .2) !important;
  transform: translateY(-4px) !important;
  background: linear-gradient(180deg, #f0fdfa 0%, #ecfdf5 100%) !important;
}

#brand-page-wrapper .brand-grid-card:hover .brand-grid-name {
  color: #0f766e !important;
}

#brand-page-wrapper .brand-grid-card:hover .brand-grid-logo {
  border-color: #99f6e4 !important;
  background: #f0fdfa !important;
}

#brand-page-wrapper .brand-grid-card:focus-within {
  outline: 3px solid rgba(15, 118, 110, .22) !important;
  outline-offset: 2px !important;
}

#brand-page-wrapper .brand-grid-logo {
  width: 110px;
  height: 110px;
  margin: 0 auto 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  border: 1px solid #dfe7f4;
  border-radius: 20px;
  background: #f7f9fd;
  padding: 12px;
}

#brand-page-wrapper .brand-grid-logo img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform .3s ease;
}

#brand-page-wrapper .brand-grid-card:hover .brand-grid-logo img {
  transform: scale(1.07);
}

#brand-page-wrapper .no-logo-icon {
  font-size: 34px;
  color: #94a3b8;
}

#brand-page-wrapper .brand-grid-name {
  margin: 0 0 14px;
  text-align: center;
  color: #20324d;
  font-size: 15px;
  font-weight: 800;
  line-height: 1.35;
  overflow-wrap: anywhere;
}

#brand-page-wrapper .brand-grid-actions {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: nowrap;
  gap: 6px;
  margin-top: auto;
}

#brand-page-wrapper .brand-empty-state {
  grid-column: 1/-1;
  padding: 64px 20px;
  text-align: center;
  color: #70809a;
}

#brand-page-wrapper .brand-empty-state i {
  display: block;
  margin-bottom: 12px;
  font-size: 42px;
  color: #a5b4fc;
}

#brand-page-wrapper :focus-visible {
  outline: 3px solid rgba(79, 70, 229, .42);
  outline-offset: 3px;
}

/* ============================================ */
/* TOAST / SAVE POPUP */
/* ============================================ */

#brand-page-wrapper .brand-toast-stack {
  position: fixed;
  top: 18px;
  right: 18px;
  z-index: 10050;
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-width: min(420px, calc(100vw - 24px));
  pointer-events: none;
}

#brand-page-wrapper .brand-toast {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 14px 16px;
  border-radius: 14px;
  font-size: 14px;
  font-weight: 600;
  line-height: 1.45;
  box-shadow: 0 16px 40px rgba(15, 118, 110, .18);
  animation: brandToastIn .35s ease;
  pointer-events: auto;
}

#brand-page-wrapper .brand-toast-success {
  background: linear-gradient(135deg, #ecfdf5, #d1fae5);
  border: 1px solid #6ee7b7;
  color: #065f46;
}

#brand-page-wrapper .brand-toast-error {
  background: linear-gradient(135deg, #fff5f5, #fee2e2);
  border: 1px solid #fca5a5;
  color: #991b1b;
}

#brand-page-wrapper .brand-toast i {
  font-size: 18px;
  margin-top: 1px;
  flex: 0 0 auto;
}

#brand-page-wrapper .brand-saved-badge {
  display: none;
  align-items: center;
  gap: 7px;
  padding: 8px 14px;
  border-radius: 999px;
  background: linear-gradient(135deg, #ecfdf5, #d1fae5);
  border: 1px solid #6ee7b7;
  color: #065f46;
  font-size: 13px;
  font-weight: 700;
  box-shadow: 0 8px 20px rgba(15, 118, 110, .14);
  animation: brandToastIn .35s ease;
  white-space: nowrap;
}

#brand-page-wrapper .brand-saved-badge.show {
  display: inline-flex;
}

#brand-page-wrapper .brand-saved-badge i {
  font-size: 14px;
}

@keyframes brandToastIn {
  from {
    opacity: 0;
    transform: translateY(-12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ============================================ */
/* SAVE RESULT POPUP */
/* ============================================ */

.brand-save-overlay {
  position: fixed;
  inset: 0;
  z-index: 10060;
  display: none;
  align-items: center;
  justify-content: center;
  padding: 16px;
  background: rgba(15, 35, 61, .52);
  backdrop-filter: blur(4px);
}

.brand-save-overlay.active {
  display: flex;
}

body.brand-save-open {
  overflow: hidden;
}

.brand-save-popup {
  width: min(560px, 100%);
  max-height: min(88vh, 760px);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  border-radius: 22px;
  background: #fff;
  box-shadow: 0 28px 64px rgba(15, 35, 61, .28);
  animation: brandPopupIn .32s ease;
  font-family: Inter, Arial, sans-serif;
  font-size: 15px !important;
}

@keyframes brandPopupIn {
  from {
    opacity: 0;
    transform: translateY(18px) scale(.97);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.brand-save-popup-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 20px 22px;
  background: linear-gradient(135deg, #0f766e, #0ea5a4);
  color: #fff;
}

.brand-save-popup-head h3 {
  margin: 0;
  font-size: 20px !important;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 10px;
}

.brand-save-popup-head h3 i {
  font-size: 20px !important;
}

.brand-save-popup-close {
  width: 36px;
  height: 36px;
  display: grid;
  place-items: center;
  border: 0;
  border-radius: 10px;
  background: rgba(255, 255, 255, .18);
  color: #fff;
  font-size: 18px;
  cursor: pointer;
  transition: .2s;
}

.brand-save-popup-close:hover {
  background: rgba(255, 255, 255, .28);
}

.brand-save-popup-body {
  padding: 20px 22px;
  overflow-y: auto;
  flex: 1;
}

.brand-save-popup-body .brand-save-msg {
  margin: 0 0 16px;
  padding: 14px 16px !important;
  border-radius: 12px;
  background: #ecfdf5;
  border: 1px solid #a7f3d0;
  color: #065f46;
  font-weight: 700;
  font-size: 15px !important;
}

.brand-save-details {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  overflow: hidden;
  font-size: 14px !important;
}

.brand-save-details th,
.brand-save-details td {
  padding: 12px 16px !important;
  text-align: left;
  border-bottom: 1px solid #edf2f7;
  vertical-align: top;
  word-break: break-word;
  font-size: 14px !important;
}

.brand-save-details th {
  width: 38%;
  background: #f8fafc;
  color: #475569;
  font-weight: 700;
}

.brand-save-details tr:last-child th,
.brand-save-details tr:last-child td {
  border-bottom: 0;
}

.brand-save-logo {
  margin-top: 16px;
  text-align: center;
  padding: 14px;
  border: 1px dashed #cbd5e1;
  border-radius: 14px;
  background: #f8fafc;
}

.brand-save-logo img {
  max-width: 100%;
  max-height: 120px;
  object-fit: contain;
  border-radius: 10px;
}

.brand-save-popup-foot {
  padding: 14px 22px 18px;
  border-top: 1px solid #e2e8f0;
  background: #f8fafc;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  flex-wrap: wrap;
}

.brand-save-popup-foot .brand-save-done {
  min-height: 44px !important;
  padding: 0 24px !important;
  border: 0;
  border-radius: 12px;
  background: linear-gradient(135deg, #0f766e, #0ea5a4);
  color: #fff;
  font: 700 15px Inter, Arial, sans-serif !important;
  cursor: pointer;
  box-shadow: 0 8px 20px rgba(15, 118, 110, .25);
  transition: .2s;
}

.brand-save-popup-foot .brand-save-done:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 26px rgba(15, 118, 110, .32);
}

.brand-save-popup-foot .brand-save-cancel {
  min-height: 44px !important;
  padding: 0 24px !important;
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  background: #fff;
  color: #475569;
  font: 700 15px Inter, Arial, sans-serif !important;
  cursor: pointer;
  transition: .2s;
}

.brand-save-popup-foot .brand-save-cancel:hover {
  background: #f1f5f9;
  border-color: #cbd5e1;
}

/* ============================================ */
/* AJAX MODAL */
/* ============================================ */

#ajaxModal .modal-dialog {
  max-width: min(720px, calc(100vw - 24px)) !important;
  margin: 12px auto !important;
}

#ajaxModal .modal-content {
  border-radius: 20px !important;
  overflow: hidden !important;
  border: 0 !important;
  box-shadow: 0 28px 64px rgba(15, 35, 61, .24) !important;
  font-family: Inter, system-ui, -apple-system, "Segoe UI", sans-serif !important;
}

#ajaxModal .modal-content:has(.brand-modal-container)>.modal-header,
#ajaxModal .modal-content:has(.edit-brand-modal)>.modal-header,
#ajaxModal .modal-content.brand-modal-loaded>.modal-header {
  display: none !important;
}

#ajaxModal .modal-content:has(.brand-modal-container)>.modal-body,
#ajaxModal .modal-content:has(.edit-brand-modal)>.modal-body,
#ajaxModal .modal-content.brand-modal-loaded>.modal-body {
  padding: 0 !important;
}

#ajaxModal .modal-header {
  background: linear-gradient(135deg, #0d5e56, #0f766e 55%, #0ea5a4) !important;
  border: 0 !important;
  padding: 16px 22px !important;
}

#ajaxModal .modal-header .modal-title {
  font: 800 19px/1.2 Inter, system-ui, sans-serif !important;
  color: #fff !important;
  letter-spacing: -.02em !important;
}

#ajaxModal .modal-header .close,
#ajaxModal .modal-header .btn-close {
  color: #fff !important;
  opacity: .88 !important;
  text-shadow: none !important;
  font-size: 22px !important;
  transition: opacity .2s ease !important;
}

#ajaxModal .modal-header .close:hover,
#ajaxModal .modal-header .btn-close:hover {
  opacity: 1 !important;
}

#ajaxModal .brand-modal-container,
#ajaxModal .edit-brand-modal {
  max-width: 100%;
  font-size: 15px !important;
}

#ajaxModal .brand-modal-container .brand-modal-header {
  border-radius: 0;
}

#ajaxModal .brand-modal-body {
  padding: 28px 28px 8px !important;
}

#ajaxModal .brand-modal-header h2 {
  font-size: 1.65rem !important;
}

#ajaxModal .brand-modal-header p,
#ajaxModal .edit-brand-modal .modal-header-band p {
  font-size: .95rem !important;
}

#ajaxModal .choice-btn {
  margin: 4px !important;
}

#ajaxModal .modal-footer {
  flex-wrap: wrap !important;
  gap: 10px !important;
  background: #f8fafc !important;
  border-top: 1.5px solid #e2e8f0 !important;
}

#ajaxModal .modal-footer .btn {
  flex: 1 1 auto !important;
  min-width: 120px !important;
  border-radius: 11px !important;
  font-weight: 700 !important;
  font-family: inherit !important;
  font-size: .95rem !important;
}

#ajaxModal .modal-backdrop.show {
  opacity: .52 !important;
}

#ajaxModal .edit-brand-modal .modal-body {
  padding: 0 15px !important;
}

#ajaxModal .edit-brand-modal .modal-header-band {
  margin: -15px -15px 22px !important;
}

#ajaxModal .edit-brand-modal .modal-footer {
  margin: 22px -15px -15px !important;
  border-radius: 0 !important;
}

#ajaxModal .form-control,
#ajaxModal .edit-brand-modal .form-control {
  font-size: .95rem !important;
}

#ajaxModal .form-group label,
#ajaxModal .edit-brand-modal .form-group label {
  font-size: .92rem !important;
}

#ajaxModal .form-control:focus {
  border-color: #0f766e !important;
  box-shadow: 0 0 0 4px rgba(15, 118, 110, .12) !important;
}

#ajaxModal .btn-primary,
#ajaxModal .btn-save {
  background: linear-gradient(135deg, #0d5e56, #0f766e) !important;
  border-color: #0d5e56 !important;
  color: #fff !important;
  font-size: .95rem !important;
}

#ajaxModal .btn-primary:hover,
#ajaxModal .btn-save:hover {
  background: linear-gradient(135deg, #0f766e, #14b8a6) !important;
  border-color: #0f766e !important;
}

#ajaxModal .btn-cancel,
#ajaxModal .brand-modal-cancel {
  display: inline-flex !important;
  align-items: center !important;
  gap: 8px !important;
  padding: 10px 20px !important;
  background: #f1f5f9 !important;
  color: #475569 !important;
  border: 1.5px solid #e2e8f0 !important;
  border-radius: 11px !important;
  font: 700 .95rem Inter, system-ui, sans-serif !important;
  cursor: pointer !important;
  transition: background .2s, border-color .2s !important;
}

#ajaxModal .btn-cancel:hover,
#ajaxModal .brand-modal-cancel:hover {
  background: #e2e8f0 !important;
  border-color: #cbd5e1 !important;
}

#ajaxModal .modal-footer {
  justify-content: space-between !important;
}

#ajaxModal .brand-modal-foot-actions {
  display: flex !important;
  gap: 10px !important;
  margin-left: auto !important;
  flex-wrap: wrap !important;
}

/* ============================================ */
/* RESPONSIVE */
/* ============================================ */

@media(max-width:1024px) {
  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced {
    flex-wrap: nowrap !important;
    gap: 8px !important;
    overflow-x: auto !important;
  }

  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .brand-toolbar-toggle-wrapper {
    margin-left: auto !important;
  }
}

@media(max-width:768px) {
  #brand-page-wrapper {
    padding: 18px 14px;
  }

  #brand-page-wrapper .brand-header {
    padding: 19px !important;
    border-radius: 18px !important;
    align-items: stretch !important;
    flex-direction: column;
  }

  #brand-page-wrapper .brand-header-left {
    gap: 11px !important;
    width: 100%;
  }

  #brand-page-wrapper .brand-header-icon {
    display: none !important;
  }

  #brand-page-wrapper h1 {
    font-size: 23px;
  }

  #brand-page-wrapper .brand-header-text p {
    font-size: 12px !important;
  }

  #brand-page-wrapper .brand-add-btn {
    width: 100%;
    margin-top: 2px;
  }

  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced {
    flex-wrap: nowrap !important;
    padding: 10px 16px !important;
    overflow-x: auto !important;
  }

  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter {
    flex: 0 0 220px !important;
    min-width: 200px !important;
  }

  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .brand-toolbar-toggle-wrapper {
    margin-left: auto !important;
  }

  #brand-page-wrapper .dt-toolbar-enhanced .dt-button {
    font-size: 11px !important;
    padding: 0 10px !important;
  }

  #brand-page-wrapper .dt-table-area {
    padding: 0 12px 8px !important;
  }

  #brand-page-wrapper #brand-grid-view {
    grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
    gap: 12px;
  }

  #brand-page-wrapper .brand-grid-card {
    min-height: 210px !important;
    padding: 15px !important;
  }

  #brand-page-wrapper .brand-grid-logo {
    width: 90px !important;
    height: 90px !important;
  }

  #brand-page-wrapper .dt-footer-enhanced {
    flex-direction: column !important;
    align-items: stretch !important;
    padding: 10px 14px 14px !important;
  }

  #brand-page-wrapper .dt-footer-enhanced .dataTables_paginate {
    justify-content: center !important;
  }

  #brand-page-wrapper .brand-logo-cell,
  #brand-page-wrapper .brand-logo-placeholder {
    width: 58px !important;
    height: 58px !important;
  }

  #brand-page-wrapper table.dataTable thead th:nth-child(2),
  #brand-page-wrapper table.dataTable tbody td:nth-child(2) {
    display: none;
  }

  #brand-page-wrapper .brand-toast-stack {
    top: 12px;
    right: 12px;
    left: 12px;
    max-width: none;
  }

  .brand-save-popup-head h3 {
    font-size: 18px !important;
  }

  .brand-save-popup-body,
  .brand-save-popup-foot {
    padding-left: 16px;
    padding-right: 16px;
  }

  #brand-page-wrapper .dt-footer-enhanced .dataTables_info {
    font-size: 14px !important;
  }

  #brand-page-wrapper .dt-footer-enhanced .paginate_button {
    font-size: 13px !important;
    min-width: 36px !important;
    height: 36px !important;
  }

  #ajaxModal .brand-modal-header h2 {
    font-size: 1.35rem !important;
  }
}

@media(max-width:640px) {
  #brand-page-wrapper .option a,
  #brand-page-wrapper .option button,
  #brand-page-wrapper .brand-grid-actions a,
  #brand-page-wrapper .brand-grid-actions button {
    min-height: 36px !important;
    padding: 6px 10px !important;
    font-size: 12px !important;
  }

  #brand-page-wrapper td.option,
  #brand-page-wrapper .brand-grid-actions,
  #brand-page-wrapper .brand-action-row {
    flex-wrap: nowrap !important;
    gap: 4px !important;
  }
}

@media(max-width:560px) {
  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_length {
    width: 100%;
  }

  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter {
    flex: 0 0 180px !important;
    min-width: 160px !important;
  }

  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dt-buttons {
    width: auto;
    overflow-x: auto;
    flex-wrap: nowrap;
    padding-bottom: 2px;
  }

  #brand-page-wrapper .dt-toolbar-enhanced .dt-button {
    white-space: nowrap;
    font-size: 11px !important;
    padding: 0 8px !important;
    min-height: 32px !important;
    height: 32px !important;
  }

  #brand-page-wrapper .dt-toolbar-enhanced .dt-button .brand-export-label {
    display: none !important;
  }

  #brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-excel,
  #brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-print {
    min-width: 40px !important;
    padding: 0 11px !important;
  }

  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .select2-container {
    min-width: 90px !important;
    width: 100px !important;
  }

  #brand-page-wrapper #brand-grid-view {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    padding: 13px !important;
  }

  #brand-page-wrapper .brand-toolbar-toggle {
    font-size: 11px !important;
    padding: 0 10px !important;
    min-height: 32px !important;
    height: 32px !important;
  }
}

@media(max-width:480px) {
  #brand-page-wrapper .brand-add-btn span {
    display: inline;
  }

  #brand-page-wrapper .option a span.brand-action-label,
  #brand-page-wrapper .brand-grid-actions a span.brand-action-label {
    display: none;
  }

  #brand-page-wrapper .option a,
  #brand-page-wrapper .brand-grid-actions a {
    min-width: 38px;
    padding: 8px !important;
  }
}
</style>

<div id="brand-page-wrapper" class="brand-view-table">
<?php if(session()->has('error')): ?>
  <div class="brand-alert" role="alert"><i class="fa-solid fa-circle-exclamation"></i><?php echo session('error'); ?></div>
<?php endif; ?>
<?php if(session()->has('success')): ?>
  <div class="brand-alert" role="alert" style="border-color:#a7f3d0;background:#ecfdf5;color:#065f46;"><i class="fa-solid fa-circle-check"></i><?php echo session('success'); ?></div>
<?php endif; ?>

<div class="brand-toast-stack" id="brand-toast-stack" aria-live="polite" aria-atomic="true"></div>

<header class="brand-header" role="banner">
  <div class="brand-header-left">
    <button type="button" class="brand-back-btn" id="brand-back-btn" aria-label="Go back" title="Back"><i class="fa-solid fa-arrow-left"></i></button>
    <div class="brand-header-icon" aria-hidden="true"><i class="fa-solid fa-tags"></i></div>
    <div class="brand-header-text">
      <nav class="brand-breadcrumb" aria-label="Breadcrumb"><span>Products</span><i class="fa-solid fa-chevron-right"></i><span>Brands</span></nav>
      <h1>Brands <span class="brand-saved-badge" id="brand-saved-badge" role="status" aria-live="polite"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Saved</span></h1>
      <p>Manage product brands, logos, and actions from one directory.</p>
    </div>
  </div>
  <?php echo modal_anchor(get_uri("items/modal_formbrand"), '<i class="fa-solid fa-plus" aria-hidden="true"></i><span>Add Brand</span>', array("class" => "brand-add-btn", "title" => 'Add Brand', "aria-label" => "Add a new brand")); ?>
</header>

<section class="brand-table-card" aria-label="Brand directory">
  <div class="dt-table-area">
    <div id="brand-grid-view" role="region" aria-label="Brand cards" aria-live="polite"></div>
    <div class="table-responsive" id="brand-table-view"><table id="item-table" class="display" cellspacing="0" width="100%" aria-label="Brands list"></table></div>
  </div>
</section>
</div>

<div class="brand-save-overlay" id="brand-save-overlay" role="dialog" aria-modal="true" aria-labelledby="brand-save-title" aria-hidden="true">
  <div class="brand-save-popup">
    <div class="brand-save-popup-head">
      <h3 id="brand-save-title"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Brand Saved</h3>
      <button type="button" class="brand-save-popup-close" id="brand-save-close" aria-label="Close"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
    </div>
    <div class="brand-save-popup-body" id="brand-save-body"></div>
    <div class="brand-save-popup-foot">
      <button type="button" class="brand-save-cancel" id="brand-save-cancel">Cancel</button>
      <button type="button" class="brand-save-done" id="brand-save-done">Done</button>
    </div>
  </div>
</div>

<script>
(function($){
'use strict';
function escapeHtml(value){ return $('<div>').text(value == null ? '' : String(value)).html(); }
function safeUrl(value){ try { var url = new URL(String(value || ''), window.location.origin); return /^(https?:|data:image\/)/i.test(url.protocol) ? url.href : ''; } catch(e) { return ''; } }
function noImage(){ return '<div class="brand-logo-placeholder" role="img" aria-label="No logo available"><i class="fa-regular fa-image" aria-hidden="true"></i></div>'; }
function imageMarkup(url,name,large){ var safe=safeUrl(url); if(!safe){ return large?'<span class="no-logo-icon"><i class="fa-regular fa-image" aria-hidden="true"></i></span>':noImage(); } var alt=escapeHtml((name||'Brand')+' logo'); return large?'<img src="'+escapeHtml(safe)+'" alt="'+alt+'" loading="lazy">':'<div class="brand-logo-cell"><img src="'+escapeHtml(safe)+'" alt="'+alt+'" loading="lazy"></div>'; }
function attachImageFallback(scope){ $(scope).find('img').off('error.brandImage').on('error.brandImage',function(){ var card=$(this).closest('.brand-grid-logo'); if(card.length){card.html('<span class="no-logo-icon"><i class="fa-regular fa-image" aria-hidden="true"></i></span>');}else{$(this).closest('.brand-logo-cell').replaceWith(noImage());} }); }
function tableApi(){ return $.fn.DataTable.isDataTable('#item-table') ? $('#item-table').DataTable() : null; }
var brandEditBase = '<?php echo get_uri("items/modal_formbrand_edit/"); ?>';
var brandAddModalUrl = '<?php echo get_uri("items/modal_formbrand"); ?>';
var brandDeleteUrl = '<?php echo get_uri("items/deletebrand"); ?>';
var brandModalLoadId = 0;
var brandModalXhr = null;

function showBrandToast(message, type) {
  var kind = type === 'error' ? 'brand-toast-error' : 'brand-toast-success';
  var icon = type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check';
  var $toast = $('<div class="brand-toast ' + kind + '" role="status"><i class="fa-solid ' + icon + '" aria-hidden="true"></i><span></span></div>');
  $toast.find('span').text(message || 'Done');
  $('#brand-toast-stack').append($toast);
  setTimeout(function(){ $toast.fadeOut(260, function(){ $(this).remove(); }); }, 4200);
}

function showBrandSavedNotice() {
  showBrandToast('Saved');
  var $badge = $('#brand-saved-badge');
  $badge.addClass('show');
  setTimeout(function(){ $badge.removeClass('show'); }, 3500);
}

function closeBrandSavePopup() {
  $('#brand-save-overlay').removeClass('active').attr('aria-hidden', 'true');
  $('body').removeClass('brand-save-open');
}

function closeBrandAjaxModal() {
  brandModalLoadId += 1;
  if (brandModalXhr && brandModalXhr.readyState !== 4) {
    brandModalXhr.abort();
    brandModalXhr = null;
  }
  if ($('#ajaxModal').length) {
    $('#ajaxModal').modal('hide');
  }
}

function showBrandModalLoading(title) {
  var safeTitle = title || 'Add Brand';
  $('#ajaxModalTitle').html(safeTitle);
  $('#ajaxModal .modal-header').removeClass('hide');
  $('#ajaxModal .modal-footer').removeClass('hide');
  $('#ajaxModalContent').html($('#ajaxModalOriginalContent').html());
  $('#ajaxModalContent').find('.original-modal-body').removeClass('original-modal-body').addClass('modal-body');
  $('#ajaxModal .modal-content').removeClass('brand-modal-loaded');
}

function applyBrandModalContent(response) {
  $('#ajaxModalContent').html(response);
  var $content = $('#ajaxModal .modal-content');
  $content.toggleClass('brand-modal-loaded', $content.find('.brand-modal-container, .edit-brand-modal').length > 0);
  if ($content.find('.brand-modal-container').length) {
    resetBrandAddModal();
  }
  if (typeof setModalScrollbar === 'function') {
    setModalScrollbar();
  }
  if (typeof feather !== 'undefined' && feather.replace) {
    feather.replace();
  }
}

function loadBrandModalContent(url, title, postData) {
  var loadId = ++brandModalLoadId;
  if (brandModalXhr && brandModalXhr.readyState !== 4) {
    brandModalXhr.abort();
  }

  showBrandModalLoading(title);
  $('#ajaxModal').modal('show');

  brandModalXhr = $.ajax({
    url: url,
    data: $.extend({ ajaxModal: 1 }, postData || {}),
    cache: false,
    type: 'POST',
    success: function(response) {
      if (loadId !== brandModalLoadId) return;
      applyBrandModalContent(response);
      if (!$('#ajaxModal').hasClass('show')) {
        $('#ajaxModal').modal('show');
      }
    },
    error: function(_xhr, status) {
      if (loadId !== brandModalLoadId || status === 'abort') return;
      showBrandModalLoading(title);
      showBrandToast('Could not load brand form. Please try again.', 'error');
    },
    complete: function() {
      if (loadId === brandModalLoadId) {
        brandModalXhr = null;
      }
    }
  });
}

function whenBrandAjaxModalReady(callback) {
  var $modal = $('#ajaxModal');
  if (!$modal.length) {
    callback();
    return;
  }
  if ($modal.hasClass('show')) {
    callback();
    return;
  }
  if ($('.modal-backdrop').length) {
    $modal.one('hidden.bs.modal.brandQueue', function() {
      setTimeout(callback, 0);
    });
    return;
  }
  callback();
}

function openBrandAddModal() {
  whenBrandAjaxModalReady(function() {
    loadBrandModalContent(brandAddModalUrl, 'Add Brand');
  });
}

function resetBrandAddModal() {
  var $modal = $('#ajaxModal');
  if (!$modal.find('.brand-modal-container').length) return;
  $modal.find('#choiceSection').show();
  $modal.find('#brandYesForm, #brandNoForm').addClass('hidden');
  $modal.find('#step-email-ip').show();
  $modal.find('#otp-message, #otp-box, #step-brand-details').hide();
  $modal.find('#input_email, #input_ip, #input_otp').val('');
}

function showBrandAddForm(choice) {
  var $modal = $('#ajaxModal');
  $modal.find('#choiceSection').hide();
  $modal.find('#brandYesForm, #brandNoForm').addClass('hidden');
  if (choice === 'yes') {
    $modal.find('#brandYesForm').removeClass('hidden');
  } else {
    $modal.find('#brandNoForm').removeClass('hidden');
  }
}

function goBackBrandAddForm() {
  var $modal = $('#ajaxModal');
  $modal.find('#brandYesForm, #brandNoForm').addClass('hidden');
  $modal.find('#choiceSection').show();
}

function brandImagePreview(input) {
  var previewId = input.getAttribute('data-preview');
  var wrapId = input.getAttribute('data-preview-wrap');
  if (!previewId) return;
  var preview = document.getElementById(previewId);
  var wrap = wrapId ? document.getElementById(wrapId) : null;
  var file = input.files && input.files[0];
  if (!preview) return;
  if (file) {
    var reader = new FileReader();
    reader.onloadend = function () {
      preview.src = reader.result;
      if (wrap) wrap.style.display = 'inline-block';
      else preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    preview.src = '#';
    if (wrap) wrap.style.display = 'none';
    else preview.style.display = 'none';
  }
}

function sendBrandOtp() {
  var email = $.trim($('#ajaxModal #input_email').val() || '');
  var ip = $.trim($('#ajaxModal #input_ip').val() || '');
  if (!email || !ip) {
    showBrandToast('Please enter email and IP address.', 'error');
    return;
  }
  fetch('https://efactor.tech/call.php?action=send_otp', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: email, ip_address: ip })
  })
  .then(function(res){ return res.json(); })
  .then(function(data){
    if (data.status === 'success') {
      $('#ajaxModal #step-email-ip').hide();
      $('#ajaxModal #otp-message, #ajaxModal #otp-box').show();
      showBrandToast('OTP sent successfully!');
    } else {
      showBrandToast(data.description || 'OTP sending failed!', 'error');
    }
  })
  .catch(function(){
    showBrandToast('Something went wrong while sending OTP.', 'error');
  });
}

function verifyBrandOtp() {
  var otp = $.trim($('#ajaxModal #input_otp').val() || '');
  var email = $.trim($('#ajaxModal #input_email').val() || '');
  if (!otp || !email) {
    showBrandToast('Please enter OTP and email.', 'error');
    return;
  }
  fetch('https://efactor.tech/call.php?action=verify_otp', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: email, verification_code: otp })
  })
  .then(function(res){ return res.json(); })
  .then(function(data){
    if (data.status === 'success') {
      $('#ajaxModal #otp-box, #ajaxModal #otp-message').hide();
      $('#ajaxModal #step-brand-details').show();
      showBrandToast('OTP verified successfully!');
    } else {
      showBrandToast(data.description || 'Invalid OTP!', 'error');
    }
  })
  .catch(function(){
    showBrandToast('Verification failed!', 'error');
  });
}

function openBrandSavePopup(html) {
  var $wrap = $('<div>').html(html || '');
  var message = '';
  $wrap.find('h3').each(function(){ if (!message && /saved|success/i.test($(this).text())) message = $(this).text().trim(); });
  if (!message) message = 'Brand saved successfully!';

  var rows = '';
  $wrap.find('table tr').each(function(){
    var cells = $(this).find('td');
    if (cells.length >= 2) {
      rows += '<tr><th>' + escapeHtml($(cells[0]).text().trim()) + '</th><td>' + escapeHtml($(cells[1]).text().trim()) + '</td></tr>';
    }
  });

  var logoHtml = '';
  $wrap.find('img').each(function(){
    var src = safeUrl($(this).attr('src'));
    if (src) logoHtml = '<div class="brand-save-logo"><img src="' + escapeHtml(src) + '" alt="Brand logo"></div>';
  });

  var body = '<p class="brand-save-msg"><i class="fa-solid fa-check" aria-hidden="true"></i> ' + escapeHtml(message) + '</p>';
  if (rows) body += '<table class="brand-save-details" aria-label="Saved brand details"><tbody>' + rows + '</tbody></table>';
  body += logoHtml;

  $('#brand-save-body').html(body);
  $('#brand-save-overlay').addClass('active').attr('aria-hidden', 'false');
  $('body').addClass('brand-save-open');
  showBrandToast('Brand saved successfully!');
}

function reloadBrandTable() {
  var api = tableApi();
  if (api && api.ajax) {
    api.ajax.reload(null, false);
    return;
  }
  if ($.fn.DataTable.isDataTable('#item-table')) {
    $('#item-table').appTable({ reload: true });
  }
}

function extractBrandId($a) {
  var id = $a.attr('data-post-id') || $a.attr('data-id');
  if (id) return String(id);
  var url = String($a.attr('data-action-url') || '');
  var match = url.match(/modal_formbrand(?:_edit)?\/(\d+)/i);
  return match ? match[1] : '';
}

function openBrandEditModal(id) {
  if (!id) return;
  var url = brandEditBase.replace(/\/?$/, '/') + id;
  whenBrandAjaxModalReady(function() {
    loadBrandModalContent(url, 'Edit Brand', { id: id });
  });
}

function fixActionLinks(scope) {
  var $scope = scope ? $(scope) : $('#brand-page-wrapper');

  $scope.find('a.edit').each(function(){
    var $a = $(this);
    var id = extractBrandId($a);
    if (!id) return;
    var url = brandEditBase.replace(/\/?$/, '/') + id;
    $a.attr('data-action-url', url);
    $a.attr('data-post-id', id);
    $a.attr('data-id', id);
    $a.removeAttr('data-act');
    $a.attr('data-title', 'Edit Brand');
    $a.attr('title', 'Edit');
  });

  $scope.find('a.delete[data-action-url*="delete_brand"], a.delete[data-action-url*="deletebrand"]').each(function(){
    var $a = $(this);
    if ($a.data('brandDeleteFixed')) return;
    $a.attr('data-action-url', brandDeleteUrl);
    $a.attr('data-undo', '0');
    $a.removeAttr('data-action');
    $a.removeAttr('data-act');
    $a.data('brandDeleteFixed', true);
  });
}

// Build each action button once so it has exactly one icon and one label.
function wrapActionButtons(scope) {
  var $scope = scope ? $(scope) : $('#brand-page-wrapper');
  $scope.find('td.option, .brand-grid-actions').each(function(){
    var $cell = $(this);
    if ($cell.find('> .brand-action-row').length) return;
    var $actions = $cell.children('a, button');
    if ($actions.length > 1) {
      $actions.wrapAll('<div class="brand-action-row"></div>');
    } else if ($actions.length === 1 && !$cell.hasClass('brand-action-row')) {
      $actions.wrap('<div class="brand-action-row"></div>');
    }
  });
}

function addModernIcons() {
  $('#item-table .option a[title*="Edit"], #item-table .option a[title*="Delete"], #item-table .option a[title*="View"], #brand-grid-view .brand-grid-actions a[title*="Edit"], #brand-grid-view .brand-grid-actions a[title*="Delete"], #brand-grid-view .brand-grid-actions a[title*="View"]').each(function() {
    var $this = $(this);
    if ($this.data('brandIconsReady')) return;
    var title = ($this.attr('title') || '').toLowerCase();
    var iconClass = 'fa-regular fa-pen-to-square';
    var label = 'Edit';

    if (title.indexOf('delete') !== -1) {
      iconClass = 'fa-regular fa-trash-can';
      label = 'Delete';
    } else if (title.indexOf('view') !== -1) {
      iconClass = 'fa-regular fa-eye';
      label = 'View';
    }

    $this.empty();
    $this.append('<i class="' + iconClass + '" aria-hidden="true"></i>');
    $this.append('<span class="brand-action-label">' + label + '</span>');
    $this.data('brandIconsReady', true);
  });
  wrapActionButtons('#item-table, #brand-grid-view');
}

// Excel, Print icons + search placeholder
function addRichIcons() {
  var $toolbar = $('#item-table_wrapper .dt-toolbar-enhanced');
  if (!$toolbar.length) return;

  $toolbar.find('.dataTables_filter input').attr({
    placeholder: 'Search brands…',
    'aria-label': 'Search brands'
  });

  function enhanceExportBtn(selector, iconClass, defaultLabel, title) {
    $toolbar.find(selector).each(function(){
      var $btn = $(this);
      if ($btn.data('brandExportReady')) return;
      var label = $.trim($btn.text().replace(/\s+/g, ' ')) || defaultLabel;
      $btn.empty().append(
        '<i class="' + iconClass + '" aria-hidden="true"></i>',
        '<span class="brand-export-label">' + label + '</span>'
      );
      $btn.attr('title', title);
      $btn.data('brandExportReady', true);
    });
  }

  enhanceExportBtn('.dt-button.buttons-excel', 'fa-solid fa-file-excel', 'Excel', 'Export to Excel');
  enhanceExportBtn('.dt-button.buttons-print', 'fa-solid fa-print', 'Print', 'Print brands');
}

$(function(){
  var brandPage = document.getElementById('brand-page-wrapper');
  if (brandPage) {
    brandPage.addEventListener('click', function(e) {
      var btn = e.target.closest && e.target.closest('.brand-add-btn');
      if (!btn || !brandPage.contains(btn)) return;
      e.preventDefault();
      e.stopImmediatePropagation();
      openBrandAddModal();
    }, true);
  }

  $(document).on('shown.bs.modal', '#ajaxModal', function(){
    var $content = $('#ajaxModal .modal-content');
    $content.toggleClass('brand-modal-loaded', $content.find('.brand-modal-container, .edit-brand-modal').length > 0);
    if ($content.find('.brand-modal-container').length) {
      resetBrandAddModal();
    }
  });
  $(document).on('hidden.bs.modal', '#ajaxModal', function(){
    $('#ajaxModal .modal-content').removeClass('brand-modal-loaded');
    resetBrandAddModal();
    if (brandModalXhr && brandModalXhr.readyState !== 4) {
      showBrandModalLoading($('#ajaxModalTitle').text() || 'Add Brand');
    }
  });

  $(document).on('click', '#ajaxModal .brand-modal-cancel', function(e){
    e.preventDefault();
    closeBrandAjaxModal();
  });
  $(document).on('click', '#ajaxModal .brand-modal-back', function(e){
    e.preventDefault();
    goBackBrandAddForm();
  });
  $(document).on('click', '#ajaxModal .brand-choice-yes', function(e){
    e.preventDefault();
    showBrandAddForm('yes');
  });
  $(document).on('click', '#ajaxModal .brand-choice-no', function(e){
    e.preventDefault();
    showBrandAddForm('no');
  });
  $(document).on('click', '#ajaxModal .brand-send-otp', function(e){
    e.preventDefault();
    sendBrandOtp();
  });
  $(document).on('click', '#ajaxModal .brand-verify-otp', function(e){
    e.preventDefault();
    verifyBrandOtp();
  });
  $(document).on('change', '#ajaxModal .brand-image-input', function(){
    brandImagePreview(this);
  });

  $('#brand-back-btn').on('click',function(){ if(window.history.length>1){window.history.back();}else{window.location.href='<?php echo get_uri("items"); ?>';} });

  $('#brand-save-close, #brand-save-done, #brand-save-cancel').on('click', closeBrandSavePopup);
  $('#brand-save-overlay').on('click', function(e){ if (e.target === this) closeBrandSavePopup(); });
  $(document).on('keydown.brandSavePopup', function(e){ if (e.key === 'Escape') closeBrandSavePopup(); });

  if ($('.brand-alert').text().match(/success|saved|updated/i)) {
    showBrandToast($('.brand-alert').last().text().trim() || 'Success');
  }

  function initBrandDataTable() {
    if ($.fn.DataTable.isDataTable('#item-table')) {
      return;
    }
    $('#item-table').appTable({
      source:'<?php echo_uri("items/list_data30") ?>',
      order:[[0,'desc']],
      filterDropdown:[{name:'category_id',class:'w200'}],
      columns:[
        {title:"<?php echo 'Brand Name' ?>",class:'brand-col-name',data:'brand_name',render:function(data){var name=escapeHtml(data||'—');return '<span class="brand-name-badge"><span class="brand-dot"></span>'+name+'</span>'; }},
        {title:"<?php echo 'Brand Logo' ?>",class:'brand-col-logo',data:'brandlogo_url',render:function(data,type,row){return imageMarkup(data,row&&row.brand_name,false);}},
        {title:"Actions<span class='sr-only'>Actions</span>",class:'text-center option brand-col-actions',data:'options'}
      ],
      printColumns:[0,1,2], xlsColumns:[0,1,2]
    });
  }

  $(window).on('pageshow.brandTable', function(e){
    if (e.originalEvent && e.originalEvent.persisted && $.fn.DataTable.isDataTable('#item-table')) {
      var api = tableApi();
      if (api) api.ajax.reload(null, false);
    }
  });

  var gridMode=false;
  
  function buildGrid(){
    var api=tableApi(), grid=$('#brand-grid-view'); 
    if(!api) return;
    var rows=api.rows({search:'applied',page:'current'}).data(); 
    grid.empty();
    
    if(!rows || !rows.length){ 
      grid.html('<div class="brand-empty-state"><i class="fa-regular fa-folder-open" aria-hidden="true"></i><strong>No matching brands</strong><div>Try another search or create a new brand.</div></div>'); 
      return; 
    }
    
    rows.each(function(row){ 
      var name=row&&row.brand_name?String(row.brand_name):'—';
      var optionsHtml = row&&row.options?row.options:'';
      
      grid.append('<article class="brand-grid-card" tabindex="0" aria-label="'+escapeHtml(name)+' brand"><div class="brand-grid-logo">'+imageMarkup(row&&row.brandlogo_url,name,true)+'</div><h3 class="brand-grid-name">'+escapeHtml(name)+'</h3><div class="brand-grid-actions">'+optionsHtml+'</div></article>');
    }); 
    
    attachImageFallback(grid);
    fixActionLinks(grid);
    
    // Add modern icons to the newly created grid items
    setTimeout(function() {
      addModernIcons();
    }, 10);
  }
  
  function gridHost(){ 
    var table=$('#item-table'); 
    var grid=$('#brand-grid-view'); 
    if(table.length && grid.parent()[0]!==table.parent()[0]) {
      grid.insertBefore(table); 
    }
    return grid; 
  }
  
  function setView(enable){
    gridMode=enable;
    var api=tableApi(), table=$('#item-table'), grid=gridHost();
    var $page = $('#brand-page-wrapper');

    $page.toggleClass('brand-view-grid', enable);
    $page.toggleClass('brand-view-table', !enable);
    table.toggleClass('grid-mode-table', enable);
    grid.toggleClass('active', enable);

    var $toggle = $('#item-table_wrapper').find('.brand-toolbar-toggle');
    $toggle.toggleClass('is-grid', enable).attr('aria-pressed', String(enable));

    if(enable) {
      $toggle.find('.grid-label').hide();
      $toggle.find('.table-label').show();
      buildGrid();
    } else {
      $toggle.find('.grid-label').show();
      $toggle.find('.table-label').hide();
    }

    if(api && !enable) {
      setTimeout(function(){ api.columns.adjust(); }, 50);
    }

    setTimeout(function() {
      addModernIcons();
    }, 10);
  }
  
  function isBottomControl(node){return node.is('.dataTables_info,.dataTables_paginate,.dataTables_processing');}
  
  function setupFooter(){
    var wrapper=$('#item-table_wrapper'); 
    if(!wrapper.length) return;
    
    wrapper.find('.dt-footer-enhanced').remove();
    var footer=$('<div class="dt-footer-enhanced" role="navigation" aria-label="Table pagination"></div>');
    var info = wrapper.children('.dataTables_info');
    var paginate = wrapper.children('.dataTables_paginate');
    
    if(info.length) {
      footer.append(info);
    }
    if(paginate.length) {
      footer.append(paginate);
    }
    
    if(footer.children().length > 0) {
      wrapper.append(footer);
    }
  }
  
  function setupToolbar(){
    var wrapper=$('#item-table_wrapper'); 
    if(!wrapper.length || wrapper.data('brandToolbarReady')) return; 
    wrapper.data('brandToolbarReady', true);
    
    var table=$('#item-table');
    
    var toolbar=$('<div class="dt-toolbar-enhanced" role="toolbar" aria-label="Brand table tools"></div>');
    
    var candidates=wrapper.children().filter(function(){ 
      var n=$(this); 
      return !n.is(table) && 
             !isBottomControl(n) && 
             !n.hasClass('dt-toolbar-enhanced') && 
             !n.hasClass('dt-footer-enhanced') &&
             !n.find(table).length; 
    });
    
    candidates.each(function(){ 
      toolbar.append(this); 
    });
    
    var toggle=$('<button type="button" class="brand-toolbar-toggle" aria-pressed="false" aria-label="Toggle card view" title="Toggle card view"><i class="fa-solid fa-table-cells-large" aria-hidden="true"></i><span class="grid-label">Grid</span><span class="table-label">Table</span></button>');

    toolbar.append(toggle);
    wrapper.prepend(toolbar);

    toggle.on('click', function(){ setView(!gridMode); });
    setupFooter();
    gridHost();
    bindPrintHandler();
    
    setTimeout(function() {
      addRichIcons();
      addModernIcons();
    }, 50);
  }
  
  function getFilteredBrandRows(){
    var api=tableApi();
    if(!api) return [];
    return api.rows({search:'applied'}).data().toArray();
  }

  function openBrandPrintWindow(title, bodyHtml, mode){
    var gridCss='*{box-sizing:border-box}body{font-family:Inter,Arial,sans-serif;margin:24px;color:#13233d}h1{margin:0 0 18px;font-size:22px}.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px}.card{border:1px solid #dbe4f5;border-radius:14px;padding:16px;text-align:center;break-inside:avoid;page-break-inside:avoid}.card img{display:block;width:100px;height:100px;object-fit:contain;margin:0 auto 12px;border:1px solid #e2e8f0;border-radius:12px;padding:8px;background:#f8fafc}.card h3{margin:0;font-size:15px;font-weight:700;color:#20324d;word-break:break-word}.no-logo{width:100px;height:100px;margin:0 auto 12px;display:grid;place-items:center;border:1px dashed #cbd5e1;border-radius:12px;color:#94a3b8;font-size:13px;background:#f8fafc}';
    var tableCss='*{box-sizing:border-box}body{font-family:Inter,Arial,sans-serif;margin:24px;color:#13233d}h1{margin:0 0 18px;font-size:22px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #dbe4f5;padding:10px 12px;text-align:left;vertical-align:middle}th{background:#f6f8fc;font-size:12px;text-transform:uppercase;letter-spacing:.04em}td img{width:72px;height:72px;object-fit:contain;border:1px solid #e2e8f0;border-radius:10px;padding:6px;background:#f8fafc}';
    var html='<!DOCTYPE html><html><head><meta charset="utf-8"><title>'+escapeHtml(title)+'</title><style>'+(mode==='grid'?gridCss:tableCss)+'</style></head><body><h1>'+escapeHtml(title)+'</h1>'+bodyHtml+'</body></html>';
    var printWin=window.open('', '_blank');
    if(!printWin){
      showBrandToast('Please allow pop-ups to print.', 'error');
      return;
    }
    printWin.document.open();
    printWin.document.write(html);
    printWin.document.close();
    setTimeout(function(){
      printWin.focus();
      printWin.print();
      setTimeout(function(){ printWin.close(); }, 600);
    }, 300);
  }

  function printBrandGridCards(rows){
    var cards='';
    rows.forEach(function(row){
      var name=escapeHtml(row&&row.brand_name?String(row.brand_name):'—');
      var logo=safeUrl(row&&row.brandlogo_url);
      var logoHtml=logo
        ? '<img src="'+escapeHtml(logo)+'" alt="'+name+' logo">'
        : '<div class="no-logo">No logo</div>';
      cards+='<article class="card">'+logoHtml+'<h3>'+name+'</h3></article>';
    });
    openBrandPrintWindow('Brands', '<div class="grid">'+cards+'</div>', 'grid');
  }

  function printBrandTableRows(rows){
    var body='<table><thead><tr><th>Brand Name</th><th>Brand Logo</th></tr></thead><tbody>';
    rows.forEach(function(row){
      var name=escapeHtml(row&&row.brand_name?String(row.brand_name):'—');
      var logo=safeUrl(row&&row.brandlogo_url);
      var logoCell=logo
        ? '<img src="'+escapeHtml(logo)+'" alt="'+name+' logo">'
        : '—';
      body+='<tr><td>'+name+'</td><td>'+logoCell+'</td></tr>';
    });
    body+='</tbody></table>';
    openBrandPrintWindow('Brands', body, 'table');
  }

  function handleBrandPrint(e){
    if(e){
      e.preventDefault();
      e.stopImmediatePropagation();
    }
    var rows=getFilteredBrandRows();
    if(!rows.length){
      showBrandToast('Nothing to print.', 'error');
      return false;
    }
    if(gridMode){
      printBrandGridCards(rows);
    }else{
      printBrandTableRows(rows);
    }
    return false;
  }

  function fixTableColumns(){
    var $table=$('#item-table');
    if(!$table.length) return;
    $table.css('width','100%');
    $('#item-table_wrapper').find('table').css('width','100%');
    wrapActionButtons('#item-table');
    var api=tableApi();
    if(api) api.columns.adjust();
  }

  function bindPrintHandler(){
    $(document)
      .off('click.brandPrint', '#item-table_wrapper .buttons-print')
      .on('click.brandPrint', '#item-table_wrapper .buttons-print', handleBrandPrint);
  }
  
  var gridRebuildTimer = null;

  function scheduleGridRebuild() {
    if (!gridMode) return;
    clearTimeout(gridRebuildTimer);
    gridRebuildTimer = setTimeout(buildGrid, 120);
  }

  function onBrandTableInit() {
    setupToolbar();
    attachImageFallback('#item-table');
    fixActionLinks('#item-table');
    bindPrintHandler();
    setTimeout(function() {
      addRichIcons();
      addModernIcons();
      fixTableColumns();
    }, 100);
  }

  // Event handlers for DataTable
  $('#item-table').on('init.dt', onBrandTableInit);
  $('#item-table').on('draw.dt', function(){
    attachImageFallback('#item-table');
    fixActionLinks('#item-table tbody');
    scheduleGridRebuild();
  });

  initBrandDataTable();

  // Edit brand — open correct edit modal (table + grid)
  $(document).on('click', '#brand-page-wrapper a.edit', function(e){
    e.preventDefault();
    e.stopImmediatePropagation();
    var id = extractBrandId($(this));
    if (!id) {
      showBrandToast('Could not open edit form. Missing brand ID.', 'error');
      return false;
    }
    openBrandEditModal(id);
    return false;
  });

  // Delete brand (table + grid) — capture phase so appTable delete/undo handlers never fire
  if (brandPage) {
    brandPage.addEventListener('click', function(e){
      var link = e.target.closest && e.target.closest('a.delete');
      if (!link || !brandPage.contains(link)) return;
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();

      var id = link.getAttribute('data-id');
      if (!id) return;
      if (!window.confirm('Are you sure you want to delete this brand?')) return;

      $.ajax({
        url: brandDeleteUrl,
        type: 'POST',
        dataType: 'json',
        data: { id: id },
        success: function(result){
          if (result && result.success) {
            showBrandToast(result.message || 'Brand deleted successfully!');
            reloadBrandTable();
            if (gridMode) setTimeout(buildGrid, 300);
          } else {
            showBrandToast((result && result.message) ? result.message : 'Could not delete brand.', 'error');
          }
        },
        error: function(){
          showBrandToast('Could not delete brand. Please try again.', 'error');
        }
      });
    }, true);
  }

  // If an old undo link is still visible, reload safely instead of reinitialising DataTable
  $(document).on('click', '.undo-delete', function(e){
    e.preventDefault();
    e.stopImmediatePropagation();
    $(this).closest('.app-alert, .alert').remove();
    reloadBrandTable();
    if (gridMode) setTimeout(buildGrid, 300);
    return false;
  });

  // All brand modal forms — AJAX only (modal scripts do not run after ajax load)
  $(document).on('submit', '#ajaxModal form.general-form', function(e){
    e.preventDefault();
    e.stopImmediatePropagation();

    var form = this;
    var action = String(form.action || '');
    var $form = $(form);
    var $submit = $form.find('[type="submit"]').prop('disabled', true);

    // Edit brand — stay on page, show Saved badge
    if (action.indexOf('savebrand_edit') !== -1) {
      var nameVal = $.trim($form.find('[name="name"]').val() || '');
      if (!nameVal) {
        showBrandToast('Brand name is required.', 'error');
        $submit.prop('disabled', false);
        return false;
      }

      $.ajax({
        url: form.action,
        type: 'POST',
        data: new FormData(form),
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res){
          if (res && res.success) {
            closeBrandAjaxModal();
            showBrandSavedNotice();
            reloadBrandTable();
            if (gridMode) setTimeout(buildGrid, 300);
          } else {
            showBrandToast((res && res.message) ? res.message : 'Could not save brand.', 'error');
          }
        },
        error: function(xhr){
          var msg = 'Could not save brand. Please try again.';
          try {
            var res = xhr.responseJSON || JSON.parse(xhr.responseText || '{}');
            if (res && res.message) msg = res.message;
          } catch (err) {}
          showBrandToast(msg, 'error');
        },
        complete: function(){
          $submit.prop('disabled', false);
        }
      });
      return false;
    }

    // Add brand (savebrand returns HTML)
    if (action.indexOf('savebrand') === -1) {
      $submit.prop('disabled', false);
      return false;
    }

    $.ajax({
      url: form.action,
      type: 'POST',
      data: new FormData(form),
      processData: false,
      contentType: false,
      success: function(html){
        closeBrandAjaxModal();
        openBrandSavePopup(html);
        reloadBrandTable();
        if (gridMode) setTimeout(buildGrid, 400);
      },
      error: function(){
        showBrandToast('Could not save brand. Please try again.', 'error');
      },
      complete: function(){
        $submit.prop('disabled', false);
      }
    });
    return false;
  });

  // MutationObserver for dynamic row changes (tbody only — avoids search/footer text jitter)
  var iconObserverTimer = null;
  var iconObserver = new MutationObserver(function(mutations) {
    var shouldUpdate = false;
    mutations.forEach(function(mutation) {
      if (mutation.type === 'childList' && mutation.addedNodes.length) {
        shouldUpdate = true;
      }
    });
    if (shouldUpdate) {
      clearTimeout(iconObserverTimer);
      iconObserverTimer = setTimeout(function() {
        fixActionLinks('#item-table tbody');
        addModernIcons();
      }, 150);
    }
  });

  setTimeout(function() {
    var tbody = document.querySelector('#item-table tbody');
    if (tbody) {
      iconObserver.observe(tbody, {
        childList: true,
        subtree: true
      });
    }
  }, 500);
  
  // Grid card clicks should not hijack action button clicks
  $(document).on('click','.brand-grid-actions a, .brand-grid-actions button',function(e){
    e.stopPropagation();
  });
  
  // Keyboard navigation for grid cards
  $(document).on('keydown','.brand-grid-card',function(e){ 
    if((e.key==='Enter'||e.key===' ')&&!$(e.target).is('a,button,input')){
      e.preventDefault();
      $(this).find('a,button').first().trigger('click');
    } 
  });
  
  // Add icons after any AJAX completion
  $(document).ajaxComplete(function() {
    setTimeout(function() {
      fixActionLinks('#brand-page-wrapper');
      addModernIcons();
      addRichIcons();
    }, 50);
  });
  
  // Force initial icon addition after page load
  setTimeout(function() {
    fixActionLinks('#brand-page-wrapper');
    addModernIcons();
    addRichIcons();
  }, 200);
});
})(jQuery);
</script>