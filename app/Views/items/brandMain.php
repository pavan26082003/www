<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* Brand directory: scoped so existing layout/business logic remains untouched */
#brand-page-wrapper{--ink:#13233d;--muted:#64748b;--line:#e6edf7;--panel:#fff;--soft:#f6f8fc;--primary:#4f46e5;--primary-2:#7c3aed;--success:#059669;--danger:#dc2626;--shadow:0 14px 36px rgba(34,55,92,.10);font-family:Inter,Arial,sans-serif;max-width:1440px;margin:0 auto;padding:28px 24px;color:var(--ink)}
#brand-page-wrapper *,#brand-page-wrapper *:before,#brand-page-wrapper *:after{box-sizing:border-box}
#brand-page-wrapper .brand-header{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:24px 28px;margin-bottom:22px;position:relative;overflow:hidden;border:1px solid #dce5ff;border-radius:24px;background:linear-gradient(135deg,#fbfcff 0%,#eef2ff 48%,#f7f4ff 100%);box-shadow:var(--shadow)}
#brand-page-wrapper .brand-header:after{content:"";position:absolute;inset:auto -70px -90px auto;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,#c7d2fe 0,rgba(199,210,254,0) 67%);pointer-events:none}
#brand-page-wrapper .brand-header-left{display:flex;align-items:center;gap:16px;min-width:0;position:relative;z-index:1}
#brand-page-wrapper .brand-back-btn{width:44px;height:44px;display:grid;place-items:center;flex:0 0 44px;border:1px solid #dbe4f5;border-radius:14px;background:#fff;color:#38506e;box-shadow:0 6px 16px rgba(59,82,120,.08);cursor:pointer;transition:.2s ease}
#brand-page-wrapper .brand-back-btn:hover{color:#fff;background:var(--primary);border-color:var(--primary);transform:translateY(-2px)}
#brand-page-wrapper .brand-header-icon{width:52px;height:52px;display:grid;place-items:center;flex:0 0 52px;border-radius:16px;color:#fff;font-size:21px;background:linear-gradient(135deg,var(--primary),var(--primary-2));box-shadow:0 10px 20px rgba(79,70,229,.26)}
#brand-page-wrapper .brand-header-text{min-width:0}
#brand-page-wrapper .brand-breadcrumb{display:flex;gap:7px;align-items:center;margin:0 0 5px;font-size:12px;font-weight:700;color:#7182a0}
#brand-page-wrapper .brand-breadcrumb i{font-size:10px;color:#a7b4c9}
#brand-page-wrapper h1{margin:0;font-size:28px;line-height:1.15;letter-spacing:-.7px;font-weight:800;color:#172b4d}
#brand-page-wrapper .brand-header-text p{margin:6px 0 0;color:#6c7c95;font-size:14px}
#brand-page-wrapper .brand-add-btn,#brand-page-wrapper .brand-toolbar-toggle{display:inline-flex;align-items:center;justify-content:center;gap:9px;min-height:44px;padding:0 17px;border:0;border-radius:13px;font-family:inherit;font-size:14px;font-weight:750;text-decoration:none;white-space:nowrap;cursor:pointer;transition:transform .2s ease,box-shadow .2s ease,background .2s ease}
#brand-page-wrapper .brand-add-btn{position:relative;z-index:1;color:#fff;background:linear-gradient(135deg,var(--primary),var(--primary-2));box-shadow:0 10px 18px rgba(79,70,229,.25)}
#brand-page-wrapper .brand-add-btn:hover{color:#fff;transform:translateY(-2px);box-shadow:0 15px 25px rgba(79,70,229,.32)}
#brand-page-wrapper .brand-alert{display:flex;align-items:center;gap:10px;margin-bottom:20px;padding:14px 16px;border:1px solid #fecaca;border-radius:14px;background:#fff5f5;color:#b42318;font-weight:650}
#brand-page-wrapper .brand-table-card{overflow:hidden;background:var(--panel);border:1px solid var(--line);border-radius:22px;box-shadow:var(--shadow);position:relative}
/* DataTable controls */
#brand-page-wrapper .dataTables_wrapper{padding:0;background:#fff}
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced{
  display:flex!important;
  flex-wrap:nowrap!important;
  align-items:center;
  gap:12px;
  padding:12px 20px;
  border-bottom:1px solid var(--line);
  background:linear-gradient(180deg,#fff,#fafcff);
  overflow-x:auto;
  min-height:50px;
}
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_length{
  display:flex!important;
  align-items:center;
  flex:0 0 auto;
  min-width:0;
}
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter{
  display:flex!important;
  align-items:center;
  margin-left:auto!important;
  flex:0 1 300px;
  min-width:0;
}
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dt-buttons{
  display:flex!important;
  align-items:center;
  gap:6px;
  flex:0 0 auto;
  flex-wrap:nowrap!important;
}
#brand-page-wrapper .dataTables_wrapper label{font-size:13px;font-weight:650;color:#5c6e89;display:flex;align-items:center;margin:0}
#brand-page-wrapper .dataTables_wrapper select,#brand-page-wrapper .dataTables_wrapper .dataTables_filter input{height:38px;margin-left:8px!important;padding:0 12px;border:1px solid #dce5f2;border-radius:10px;background:#fff;color:#243a5a;font:600 13px Inter,Arial,sans-serif;outline:0;transition:.2s;white-space:nowrap;flex:0 1 auto;min-width:0}
#brand-page-wrapper .dataTables_wrapper .dataTables_filter input{width:100%;max-width:220px;padding-left:36px;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%2364758b' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='7'/%3E%3Cpath d='m20 20-4-4'/%3E%3C/svg%3E") no-repeat 12px center;flex:1 1 auto;min-width:120px}
#brand-page-wrapper .dataTables_wrapper select:focus,#brand-page-wrapper .dataTables_wrapper .dataTables_filter input:focus{border-color:#a5b4fc;box-shadow:0 0 0 4px rgba(79,70,229,.10)}
#brand-page-wrapper .brand-toolbar-toggle{
  min-height:38px;
  padding:0 13px;
  color:#4f46e5;
  background:#f5f3ff;
  border:1px solid #ddd6fe;
  box-shadow:none;
  border-radius:10px!important;
  width:auto!important;
  height:38px!important;
  min-width:auto!important;
  flex:0 0 auto!important;
}
#brand-page-wrapper .brand-toolbar-toggle:hover{background:#ede9fe;transform:translateY(-1px)}
/* Default: Show "Grid" text, hide "Table" text */
#brand-page-wrapper .brand-toolbar-toggle .table-label{display:none!important}
#brand-page-wrapper .brand-toolbar-toggle .grid-label{display:inline!important}
/* When in grid mode (is-grid class): Show "Table" text, hide "Grid" text */
#brand-page-wrapper .brand-toolbar-toggle.is-grid .grid-label{display:none!important}
#brand-page-wrapper .brand-toolbar-toggle.is-grid .table-label{display:inline!important}
/* Show text labels on toggle button */
#brand-page-wrapper .brand-toolbar-toggle .grid-label,
#brand-page-wrapper .brand-toolbar-toggle .table-label {
    font-size: 12px;
    font-weight: 600;
}
#brand-page-wrapper .brand-toolbar-toggle i {
    font-size: 14px;
    margin-right: 5px;
}
#brand-page-wrapper .dt-table-area{padding:0 20px 8px}
#brand-page-wrapper .table-responsive{overflow-x:auto;overflow-y:hidden}
#brand-page-wrapper table.dataTable{width:100%!important;margin:0!important;border-collapse:separate;border-spacing:0 8px!important}
#brand-page-wrapper table.dataTable thead th{padding:12px 14px!important;border-bottom:0!important;background:#f6f8fc!important;color:#61728d!important;font-size:11px!important;letter-spacing:.06em;text-transform:uppercase;font-weight:800!important}
#brand-page-wrapper table.dataTable tbody td{padding:14px!important;border-top:1px solid #edf1f7!important;border-bottom:1px solid #edf1f7!important;background:#fff;color:#31425d;font-size:14px;vertical-align:middle!important;transition:.2s}
#brand-page-wrapper table.dataTable tbody td:first-child{border-left:1px solid #edf1f7;border-radius:14px 0 0 14px}
#brand-page-wrapper table.dataTable tbody td:last-child{border-right:1px solid #edf1f7;border-radius:0 14px 14px 0}
#brand-page-wrapper table.dataTable tbody tr{transition:transform .2s ease,filter .2s ease}
#brand-page-wrapper table.dataTable tbody tr:hover{transform:translateY(-2px);filter:drop-shadow(0 8px 12px rgba(44,69,108,.08))}
#brand-page-wrapper table.dataTable tbody tr:hover td{background:#fbfcff}
#brand-page-wrapper .brand-name-badge{display:inline-flex;align-items:center;gap:10px;font-size:14px;font-weight:750;color:#20324d}
#brand-page-wrapper .brand-dot{width:8px;height:8px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#8b5cf6);box-shadow:0 0 0 4px #eef2ff}
#brand-page-wrapper .brand-logo-cell,#brand-page-wrapper .brand-logo-placeholder{width:72px;height:72px;display:flex;align-items:center;justify-content:center;overflow:hidden;border:1px solid #dfe7f4;border-radius:16px;background:#f8faff}
#brand-page-wrapper .brand-logo-cell img{display:block;max-width:100%;max-height:100%;object-fit:contain;padding:8px;transition:transform .25s ease}
#brand-page-wrapper tr:hover .brand-logo-cell img{transform:scale(1.08)}
#brand-page-wrapper .brand-logo-placeholder{border-style:dashed;color:#8695ac;font-size:23px;background:linear-gradient(135deg,#f7f9fd,#eef2ff)}
/* Hide original pagination and info elements */
#brand-page-wrapper .dataTables_wrapper > .dataTables_paginate,
#brand-page-wrapper .dataTables_wrapper > .dataTables_info {
    display: none !important;
}
/* Footer styles - pagination will be moved here */
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
    font-size: 13px !important;
    font-weight: 600 !important;
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
    min-width: 34px !important;
    height: 34px;
    margin: 0 2px !important;
    padding: 8px 10px !important;
    border: 1px solid #e0e7f1 !important;
    border-radius: 9px !important;
    background: #fff !important;
    color: #52647e !important;
    font-size: 12px !important;
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
/* Grid sits only where table body sits; all DataTables controls/pagination stay on screen */
#brand-page-wrapper #brand-grid-view{display:none;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:18px;padding:12px 0 16px}
#brand-page-wrapper #brand-grid-view.active{display:grid}
#brand-page-wrapper .brand-grid-card{display:flex;flex-direction:column;min-height:238px;padding:20px;border:1px solid #e6ebf4;border-radius:18px;background:linear-gradient(180deg,#fff,#fbfcff);box-shadow:0 7px 18px rgba(29,48,82,.06);transition:transform .24s ease,box-shadow .24s ease,border-color .24s ease}
#brand-page-wrapper .brand-grid-card:hover{border-color:#c7d2fe;box-shadow:0 17px 31px rgba(50,71,114,.14);transform:translateY(-5px)}
#brand-page-wrapper .brand-grid-card:focus-within{outline:3px solid rgba(79,70,229,.22);outline-offset:2px}
#brand-page-wrapper .brand-grid-logo{width:110px;height:110px;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;overflow:hidden;border:1px solid #dfe7f4;border-radius:20px;background:#f7f9fd;padding:12px}
#brand-page-wrapper .brand-grid-logo img{display:block;width:100%;height:100%;object-fit:contain;transition:transform .3s ease}
#brand-page-wrapper .brand-grid-card:hover .brand-grid-logo img{transform:scale(1.07)}
#brand-page-wrapper .no-logo-icon{font-size:34px;color:#94a3b8}
#brand-page-wrapper .brand-grid-name{margin:0 0 14px;text-align:center;color:#20324d;font-size:15px;font-weight:800;line-height:1.35;overflow-wrap:anywhere}
#brand-page-wrapper .brand-grid-actions{display:flex;justify-content:center;align-items:center;flex-wrap:wrap;gap:4px;margin-top:auto}
#brand-page-wrapper .brand-empty-state{grid-column:1/-1;padding:64px 20px;text-align:center;color:#70809a}
#brand-page-wrapper .brand-empty-state i{display:block;margin-bottom:12px;font-size:42px;color:#a5b4fc}
#brand-page-wrapper :focus-visible{outline:3px solid rgba(79,70,229,.42);outline-offset:3px}
@media(max-width:760px){#brand-page-wrapper{padding:18px 14px}.brand-header{padding:19px!important;border-radius:18px!important}.brand-header-left{gap:11px!important}.brand-header-icon{display:none!important}#brand-page-wrapper h1{font-size:23px}.brand-header-text p{font-size:12px!important}.brand-add-btn{width:100%;margin-top:2px}.brand-header{align-items:stretch!important;flex-direction:column}.brand-header-left{width:100%}.dataTables_wrapper .dt-toolbar-enhanced{flex-wrap:wrap!important}.dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter{order:3;width:100%;margin-left:0!important}.dataTables_wrapper .dataTables_filter input{width:calc(100% - 56px)!important}.dt-table-area{padding:0 12px 8px!important}#brand-page-wrapper #brand-grid-view{grid-template-columns:repeat(auto-fill,minmax(155px,1fr));gap:12px}.brand-grid-card{min-height:210px!important;padding:15px!important}.brand-grid-logo{width:90px!important;height:90px!important}.dt-footer-enhanced{flex-direction:column!important;align-items:stretch!important;padding:10px 14px 14px!important}.dt-footer-enhanced .dataTables_paginate{justify-content:center!important}.brand-logo-cell,.brand-logo-placeholder{width:58px!important;height:58px!important}}
@media(max-width:560px){#brand-page-wrapper .dt-toolbar-enhanced .dataTables_length{width:100%}#brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter{width:100%;min-width:0}#brand-page-wrapper .dt-toolbar-enhanced .dt-buttons{width:100%;overflow-x:auto;flex-wrap:nowrap;padding-bottom:2px}#brand-page-wrapper .dt-toolbar-enhanced .dt-button{white-space:nowrap}#brand-page-wrapper #brand-grid-view{grid-template-columns:repeat(auto-fill,minmax(150px,1fr));padding:13px!important}}

/* === Production DataTable layout === */
#brand-page-wrapper{--panel:#ffffff;--panel-soft:#f6fafc;--canvas:#eef5f7;--ink:#172b4d;--muted:#61718b;--primary:#167a8b;--primary-dark:#105c69;--line:#d9e6eb;--accent:#e1f3f5;--danger:#c0394b}
#brand-page-wrapper .brand-table-card{background:var(--panel);border-color:var(--line);box-shadow:0 16px 38px rgba(23,43,77,.10)}
#brand-page-wrapper .dataTables_wrapper{position:relative;background:var(--panel)}
#brand-page-wrapper .dt-toolbar-enhanced{
  display:flex!important;
  align-items:center;
  gap:12px;
  flex-wrap:nowrap!important;
  overflow-x:auto;
  padding:12px 20px!important;
  background:linear-gradient(180deg,#f9fcfd 0%,#f1f8fa 100%)!important;
  border-bottom:1px solid var(--line)!important;
  min-height:50px;
}
#brand-page-wrapper .dt-toolbar-enhanced .dataTables_length{
  display:flex!important;
  align-items:center;
  flex:0 0 auto;
  min-width:0;
}
#brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter{
  display:flex!important;
  align-items:center;
  margin-left:auto!important;
  flex:0 1 300px;
  min-width:0;
}
#brand-page-wrapper .dt-toolbar-enhanced .dt-buttons{
  display:flex!important;
  align-items:center;
  gap:6px;
  flex:0 0 auto;
  flex-wrap:nowrap!important;
}
#brand-page-wrapper .dt-toolbar-enhanced .dt-button{
  display:inline-flex!important;
  align-items:center;
  justify-content:center;
  gap:7px;
  min-height:38px!important;
  padding:8px 12px!important;
  border:1px solid #bcd5da!important;
  border-radius:10px!important;
  background:#fff!important;
  color:#1f5360!important;
  font:700 12px Inter,Arial,sans-serif!important;
  box-shadow:0 2px 6px rgba(23,43,77,.05)!important;
  transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease,background .18s ease!important;
  cursor:pointer;
}
#brand-page-wrapper .dt-toolbar-enhanced .dt-button:hover{
  transform:translateY(-1px);
  border-color:#7fbdc7!important;
  background:#f4fcfd!important;
  box-shadow:0 8px 16px rgba(22,122,139,.14)!important;
}
#brand-page-wrapper .brand-toolbar-toggle{
  border-color:var(--primary)!important;
  background:var(--primary)!important;
  color:#fff!important;
  box-shadow:0 7px 14px rgba(22,122,139,.22)!important;
}
#brand-page-wrapper .brand-toolbar-toggle:hover{
  background:var(--primary-dark)!important;
  transform:translateY(-1px);
  border-color:var(--primary-dark)!important;
}
#brand-page-wrapper .brand-toolbar-toggle i{font-size:14px;margin-right:5px}
/* Default: Show "Grid" text, hide "Table" text */
#brand-page-wrapper .brand-toolbar-toggle .table-label{display:none!important}
#brand-page-wrapper .brand-toolbar-toggle .grid-label{display:inline!important}
/* When in grid mode (is-grid class): Show "Table" text, hide "Grid" text */
#brand-page-wrapper .brand-toolbar-toggle.is-grid .grid-label{display:none!important}
#brand-page-wrapper .brand-toolbar-toggle.is-grid .table-label{display:inline!important}
#brand-page-wrapper .brand-toolbar-toggle .grid-label,
#brand-page-wrapper .brand-toolbar-toggle .table-label{
  font-size:12px;
  font-weight:600;
}
#brand-page-wrapper .dataTables_wrapper .dataTables_filter input{
  border-color:#bcd5da!important;
  background:#fff!important;
  color:var(--ink)!important;
  width:100%!important;
  max-width:220px!important;
  flex:1 1 auto!important;
  min-width:120px!important;
}
#brand-page-wrapper .dataTables_wrapper .dataTables_filter input:focus{
  border-color:var(--primary)!important;
  box-shadow:0 0 0 3px rgba(22,122,139,.15)!important;
}
#brand-page-wrapper .dataTables_wrapper select{
  border-color:#bcd5da!important;
  background:#fff!important;
  color:var(--ink)!important;
}
#brand-page-wrapper .grid-mode-table{display:none!important}
#brand-page-wrapper #brand-grid-view{margin:0!important;padding:20px!important;background:#fbfdfe;border-top:0!important}
#brand-page-wrapper .brand-grid-card{border-color:#d8e6ea!important;background:#fff!important;box-shadow:0 7px 17px rgba(23,43,77,.07)!important}
#brand-page-wrapper .brand-grid-card:hover{border-color:#8cc5ce!important;box-shadow:0 18px 32px rgba(22,122,139,.15)!important}
#brand-page-wrapper .brand-grid-logo{background:#f1f8fa!important;border-color:#cde0e5!important}
#brand-page-wrapper .option a[title*="View"],#brand-page-wrapper .brand-grid-actions a[title*="View"]{background:#e4f5f7!important;color:#126473!important}

#brand-page-wrapper .option a[title*="Edit"],#brand-page-wrapper .brand-grid-actions a[title*="Edit"]{background:#eef5ff!important;color:#2863a5!important}

#brand-page-wrapper .option a[title*="Delete"],#brand-page-wrapper .brand-grid-actions a[title*="Delete"]{background:#fff0f1!important;color:#b63346!important}
@media(max-width:860px){#brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter{flex:1;min-width:220px}#brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter input{width:calc(100% - 58px)!important}}









/* duplicate style block merged */
/* Brand directory: scoped so existing layout/business logic remains untouched */
#brand-page-wrapper {
  --ink: #13233d;
  --muted: #64748b;
  --line: #e6edf7;
  --panel: #fff;
  --soft: #f6f8fc;
  --primary: #0f766e;
  --primary-2: #0ea5a4;
  --success: #059669;
  --danger: #dc2626;
  --shadow: 0 14px 36px rgba(34, 55, 92, .10);
  font-family: Inter, Arial, sans-serif;
  max-width: 1440px;
  margin: 0 auto;
  padding: 28px 24px;
  color: var(--ink)
}

#brand-page-wrapper *,
#brand-page-wrapper *:before,
#brand-page-wrapper *:after {
  box-sizing: border-box
}

#brand-page-wrapper .brand-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  padding: 24px 28px;
  margin-bottom: 22px;
  position: relative;
  overflow: hidden;
  border: 1px solid #dce5ff;
  border-radius: 24px;
  background: linear-gradient(135deg, #fbfcff 0%, #eef2ff 48%, #f7f4ff 100%);
  box-shadow: var(--shadow)
}

#brand-page-wrapper .brand-header:after {
  content: "";
  position: absolute;
  inset: auto -70px -90px auto;
  width: 220px;
  height: 220px;
  border-radius: 50%;
  background: radial-gradient(circle, #c7d2fe 0, rgba(199, 210, 254, 0) 67%);
  pointer-events: none
}

#brand-page-wrapper .brand-header-left {
  display: flex;
  align-items: center;
  gap: 16px;
  min-width: 0;
  position: relative;
  z-index: 1
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
  transition: .2s ease
}

#brand-page-wrapper .brand-back-btn:hover {
  color: #fff;
  background: var(--primary);
  border-color: var(--primary);
  transform: translateY(-2px)
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
  box-shadow: 0 10px 20px rgba(79, 70, 229, .26)
}

#brand-page-wrapper .brand-header-text {
  min-width: 0
}

#brand-page-wrapper .brand-breadcrumb {
  display: flex;
  gap: 7px;
  align-items: center;
  margin: 0 0 5px;
  font-size: 12px;
  font-weight: 700;
  color: #7182a0
}

#brand-page-wrapper .brand-breadcrumb i {
  font-size: 10px;
  color: #a7b4c9
}

#brand-page-wrapper h1 {
  margin: 0;
  font-size: 28px;
  line-height: 1.15;
  letter-spacing: -.7px;
  font-weight: 800;
  color: #172b4d
}

#brand-page-wrapper .brand-header-text p {
  margin: 6px 0 0;
  color: #6c7c95;
  font-size: 14px
}

#brand-page-wrapper .brand-add-btn,
#brand-page-wrapper .brand-toolbar-toggle {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 9px;
  min-height: 44px;
  padding: 0 17px;
  border: 0;
  border-radius: 13px;
  font-family: inherit;
  font-size: 14px;
  font-weight: 750;
  text-decoration: none;
  white-space: nowrap;
  cursor: pointer;
  transition: transform .2s ease, box-shadow .2s ease, background .2s ease
}

#brand-page-wrapper .brand-add-btn {
  position: relative;
  z-index: 1;
  color: #fff;
  background: linear-gradient(135deg, var(--primary), var(--primary-2));
  box-shadow: 0 10px 18px rgba(79, 70, 229, .25)
}

#brand-page-wrapper .brand-add-btn:hover {
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 15px 25px rgba(79, 70, 229, .32)
}

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
  font-weight: 650
}

#brand-page-wrapper .brand-table-card {
  overflow: hidden;
  background: var(--panel);
  border: 1px solid var(--line);
  border-radius: 22px;
  box-shadow: var(--shadow);
  position: relative
}

/* ============================================ */
/* ENHANCED TOOLBAR - FIXED ALIGNMENT */
/* ============================================ */

#brand-page-wrapper .dataTables_wrapper {
  padding: 0;
  background: #fff
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced {
  display: flex !important;
  flex-wrap: nowrap !important;
  align-items: center !important;
  gap: 12px !important;
  padding: 10px 20px !important;
  border-bottom: 1px solid var(--line);
  background: linear-gradient(180deg, #f8faff 0%, #f0f4fe 100%);
  overflow-x: auto;
  min-height: 56px;
  border-radius: 22px 22px 0 0;
}

/* Hide the original row and col-md wrappers that break flex */
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .row,
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced [class*="col-"],
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .datatable-tools {
  display: contents !important;
}

/* Show Entries / Length control */
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
  flex: 0 0 260px !important;
  margin: 0 0 0 auto !important;
  padding: 0 !important;
  min-width: 220px !important;
  position: relative !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter label {
  width: 100% !important;
  margin: 0 !important;
  position: relative !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter input {
  width: 100% !important;
  height: 36px !important;
  padding: 0 14px 0 38px !important;
  border: 1.5px solid #e2e8f0 !important;
  border-radius: 10px !important;
  background: #ffffff !important;
  color: #1a2a4a !important;
  font: 500 13px Inter, Arial, sans-serif !important;
  outline: 0 !important;
  transition: all .3s ease !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter input::placeholder {
  color: #9aabca !important;
  font-weight: 400 !important;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter input:focus {
  border-color: var(--primary) !important;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, .12), 0 4px 12px rgba(79, 70, 229, .08) !important;
}

/* Search icon */
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter::before {
  content: "\f002";
  font-family: "Font Awesome 6 Free";
  font-weight: 900;
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #8a9bb8;
  font-size: 14px;
  pointer-events: none;
  z-index: 2;
  transition: color .3s ease;
}

#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter:focus-within::before {
  color: var(--primary);
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
  gap: 6px !important;
  min-height: 36px !important;
  height: 36px !important;
  padding: 0 14px !important;
  border: 1.5px solid #e2e8f0 !important;
  border-radius: 10px !important;
  background: #ffffff !important;
  color: #2d3f5e !important;
  font: 600 12px Inter, Arial, sans-serif !important;
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
  box-shadow: 0 8px 20px rgba(79, 70, 229, .15) !important;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button:hover i {
  transform: scale(1.1) rotate(-5deg);
}

/* Print Button */
#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-print {
  background: linear-gradient(135deg, #f8fafc, #eef2f7) !important;
  border-color: #d0d9e8 !important;
  color: #2c3e5a !important;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-print i {
  color: #5a6f8f !important;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-print:hover {
  background: linear-gradient(135deg, #eef2f7, #e4eaf3) !important;
  border-color: #b0c0d8 !important;
  box-shadow: 0 8px 20px rgba(44, 62, 90, .15) !important;
}

/* Excel Button */
#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-excel {
  background: linear-gradient(135deg, #f0faf5, #e2f3ea) !important;
  border-color: #a8d5c0 !important;
  color: #1a6e4a !important;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-excel i {
  color: #1e7e54 !important;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-excel:hover {
  background: linear-gradient(135deg, #e2f3ea, #d0ecde) !important;
  border-color: #7ac0a0 !important;
  box-shadow: 0 8px 20px rgba(26, 110, 74, .15) !important;
}

/* Category dropdown */
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

/* Toggle View Button */
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .brand-toolbar-toggle-wrapper {
  display: flex !important;
  align-items: center !important;
  flex: 0 0 auto !important;
  margin-left: auto !important;
}

#brand-page-wrapper .brand-toolbar-toggle {
  border-color: var(--primary) !important;
  background: linear-gradient(135deg, var(--primary), var(--primary-2)) !important;
  color: #fff !important;
  box-shadow: 0 4px 14px rgba(79, 70, 229, .25) !important;
  min-height: 36px !important;
  height: 36px !important;
  padding: 0 16px !important;
  border-radius: 10px !important;
  font-size: 12px !important;
  font-weight: 600 !important;
  letter-spacing: 0.3px !important;
  position: relative !important;
  overflow: hidden !important;
  border: 1px solid var(--primary) !important;
}

#brand-page-wrapper .brand-toolbar-toggle:before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
  pointer-events: none;
  border-radius: 10px;
}

#brand-page-wrapper .brand-toolbar-toggle:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 8px 24px rgba(79, 70, 229, .35) !important;
}

#brand-page-wrapper .brand-toolbar-toggle:active {
  transform: translateY(0px) scale(0.97) !important;
}

#brand-page-wrapper .brand-toolbar-toggle i {
  font-size: 14px !important;
  margin-right: 5px !important;
  transition: transform .3s ease !important;
}

#brand-page-wrapper .brand-toolbar-toggle:hover i {
  transform: rotate(15deg);
}

/* Default: Show "Grid" text, hide "Table" text */
#brand-page-wrapper .brand-toolbar-toggle .table-label {
  display: none !important
}

#brand-page-wrapper .brand-toolbar-toggle .grid-label {
  display: inline !important
}

/* When in grid mode (is-grid class): Show "Table" text, hide "Grid" text */
#brand-page-wrapper .brand-toolbar-toggle.is-grid .grid-label {
  display: none !important
}

#brand-page-wrapper .brand-toolbar-toggle.is-grid .table-label {
  display: inline !important
}

#brand-page-wrapper .brand-toolbar-toggle .grid-label,
#brand-page-wrapper .brand-toolbar-toggle .table-label {
  font-size: 12px !important;
  font-weight: 600 !important;
}

/* Hide processing indicator in toolbar */
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced #item-table_processing {
  display: none !important;
}

/* ============================================ */
/* TABLE STYLES */
/* ============================================ */

#brand-page-wrapper .dt-table-area {
  padding: 0 20px 8px
}

#brand-page-wrapper .table-responsive {
  overflow-x: auto;
  overflow-y: hidden
}

#brand-page-wrapper table.dataTable {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0 8px !important
}

#brand-page-wrapper table.dataTable thead th {
  padding: 12px 14px !important;
  border-bottom: 0 !important;
  background: #f6f8fc !important;
  color: #61728d !important;
  font-size: 11px !important;
  letter-spacing: .06em;
  text-transform: uppercase;
  font-weight: 800 !important
}

#brand-page-wrapper table.dataTable tbody td {
  padding: 14px !important;
  border-top: 1px solid #edf1f7 !important;
  border-bottom: 1px solid #edf1f7 !important;
  background: #fff;
  color: #31425d;
  font-size: 14px;
  vertical-align: middle !important;
  transition: .2s
}

#brand-page-wrapper table.dataTable tbody td:first-child {
  border-left: 1px solid #edf1f7;
  border-radius: 14px 0 0 14px
}

#brand-page-wrapper table.dataTable tbody td:last-child {
  border-right: 1px solid #edf1f7;
  border-radius: 0 14px 14px 0
}

#brand-page-wrapper table.dataTable tbody tr {
  transition: transform .2s ease, filter .2s ease
}

#brand-page-wrapper table.dataTable tbody tr:hover {
  transform: translateY(-2px);
  filter: drop-shadow(0 8px 12px rgba(44, 69, 108, .08))
}

#brand-page-wrapper table.dataTable tbody tr:hover td {
  background: #fbfcff
}

#brand-page-wrapper .brand-name-badge {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  font-weight: 750;
  color: #20324d
}

#brand-page-wrapper .brand-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4f46e5, #8b5cf6);
  box-shadow: 0 0 0 4px #eef2ff
}

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
  background: #f8faff
}

#brand-page-wrapper .brand-logo-cell img {
  display: block;
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  padding: 8px;
  transition: transform .25s ease
}

#brand-page-wrapper tr:hover .brand-logo-cell img {
  transform: scale(1.08)
}

#brand-page-wrapper .brand-logo-placeholder {
  border-style: dashed;
  color: #8695ac;
  font-size: 23px;
  background: linear-gradient(135deg, #f7f9fd, #eef2ff)
}

/* ============================================ */
/* FOOTER / PAGINATION */
/* ============================================ */

/* Hide original pagination and info elements */
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
  font-size: 13px !important;
  font-weight: 600 !important;
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
  min-width: 34px !important;
  height: 34px;
  margin: 0 2px !important;
  padding: 8px 10px !important;
  border: 1px solid #e0e7f1 !important;
  border-radius: 9px !important;
  background: #fff !important;
  color: #52647e !important;
  font-size: 12px !important;
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
/* ACTION BUTTONS - View, Edit, Delete */
/* ============================================ */

/* Remove the CSS pseudo-element icons */
#brand-page-wrapper .option a::before,
#brand-page-wrapper .option button::before,
#brand-page-wrapper .brand-grid-actions a::before,
#brand-page-wrapper .brand-grid-actions button::before {
  display: none !important;
  content: none !important;
}

/* Style action buttons with modern Font Awesome icons */
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

/* Show Font Awesome icons in action buttons */
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

/* Edit Button - Modern rich style */
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
  transform: translateY(-2px) !important;
}

#brand-page-wrapper .option a[title*="Edit"]:hover i,
#brand-page-wrapper .brand-grid-actions a[title*="Edit"]:hover i {
  color: #ffffff !important;
}

/* Delete Button - Modern rich style */
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
  transform: translateY(-2px) !important;
}

#brand-page-wrapper .option a[title*="Delete"]:hover i,
#brand-page-wrapper .brand-grid-actions a[title*="Delete"]:hover i {
  color: #ffffff !important;
}

/* ============================================ */
/* GRID VIEW */
/* ============================================ */

#brand-page-wrapper #brand-grid-view {
  display: none;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 18px;
  padding: 12px 0 16px
}

#brand-page-wrapper #brand-grid-view.active {
  display: grid
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
  transition: transform .24s ease, box-shadow .24s ease, border-color .24s ease
}

#brand-page-wrapper .brand-grid-card:hover {
  border-color: #c7d2fe;
  box-shadow: 0 17px 31px rgba(50, 71, 114, .14);
  transform: translateY(-5px)
}

#brand-page-wrapper .brand-grid-card:focus-within {
  outline: 3px solid rgba(79, 70, 229, .22);
  outline-offset: 2px
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
  padding: 12px
}

#brand-page-wrapper .brand-grid-logo img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform .3s ease
}

#brand-page-wrapper .brand-grid-card:hover .brand-grid-logo img {
  transform: scale(1.07)
}

#brand-page-wrapper .no-logo-icon {
  font-size: 34px;
  color: #94a3b8
}

#brand-page-wrapper .brand-grid-name {
  margin: 0 0 14px;
  text-align: center;
  color: #20324d;
  font-size: 15px;
  font-weight: 800;
  line-height: 1.35;
  overflow-wrap: anywhere
}

#brand-page-wrapper .brand-grid-actions {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 4px;
  margin-top: auto
}

#brand-page-wrapper .brand-empty-state {
  grid-column: 1/-1;
  padding: 64px 20px;
  text-align: center;
  color: #70809a
}

#brand-page-wrapper .brand-empty-state i {
  display: block;
  margin-bottom: 12px;
  font-size: 42px;
  color: #a5b4fc
}

#brand-page-wrapper :focus-visible {
  outline: 3px solid rgba(79, 70, 229, .42);
  outline-offset: 3px
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
    padding: 18px 14px
  }
  
  .brand-header {
    padding: 19px !important;
    border-radius: 18px !important
  }
  
  .brand-header-left {
    gap: 11px !important
  }
  
  .brand-header-icon {
    display: none !important
  }
  
  #brand-page-wrapper h1 {
    font-size: 23px
  }
  
  .brand-header-text p {
    font-size: 12px !important
  }
  
  .brand-add-btn {
    width: 100%;
    margin-top: 2px
  }
  
  .brand-header {
    align-items: stretch !important;
    flex-direction: column
  }
  
  .brand-header-left {
    width: 100%
  }
  
  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced {
    flex-wrap: nowrap !important;
    padding: 10px 16px !important;
    overflow-x: auto !important;
  }
  
  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter {
    flex: 0 0 220px !important;
    order: 0 !important;
    min-width: 220px !important;
  }
  
  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .brand-toolbar-toggle-wrapper {
    margin-left: auto !important;
  }
  
  #brand-page-wrapper .dt-toolbar-enhanced .dt-button {
    font-size: 11px !important;
    padding: 0 10px !important;
  }
  
  .dt-table-area {
    padding: 0 12px 8px !important
  }
  
  #brand-page-wrapper #brand-grid-view {
    grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
    gap: 12px
  }
  
  .brand-grid-card {
    min-height: 210px !important;
    padding: 15px !important
  }
  
  .brand-grid-logo {
    width: 90px !important;
    height: 90px !important
  }
  
  .dt-footer-enhanced {
    flex-direction: column !important;
    align-items: stretch !important;
    padding: 10px 14px 14px !important
  }
  
  .dt-footer-enhanced .dataTables_paginate {
    justify-content: center !important
  }
  
  .brand-logo-cell,
  .brand-logo-placeholder {
    width: 58px !important;
    height: 58px !important
  }
}

@media(max-width:560px) {
  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_length {
    width: 100%
  }
  
  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter {
    width: auto;
    min-width: 0
  }
  
  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dt-buttons {
    width: auto;
    overflow-x: auto;
    flex-wrap: nowrap;
    padding-bottom: 2px
  }
  
  #brand-page-wrapper .dt-toolbar-enhanced .dt-button {
    white-space: nowrap;
    font-size: 11px !important;
    padding: 0 8px !important;
    min-height: 32px !important;
    height: 32px !important;
  }
  
  #brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .select2-container {
    min-width: 90px !important;
    width: 100px !important;
  }
  
  #brand-page-wrapper #brand-grid-view {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    padding: 13px !important
  }
  
  #brand-page-wrapper .brand-toolbar-toggle {
    font-size: 11px !important;
    padding: 0 10px !important;
    min-height: 32px !important;
    height: 32px !important;
  }
}

/* Rich Icons for Excel and Print buttons */
#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-excel i.fa-file-excel {
  font-size: 16px !important;
  color: #1a6e4a !important;
  filter: drop-shadow(0 2px 4px rgba(26, 110, 74, 0.25));
  transition: all 0.3s ease;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-excel:hover i.fa-file-excel {
  color: #0d4a2e !important;
  transform: scale(1.15) rotate(-5deg);
  filter: drop-shadow(0 4px 8px rgba(26, 110, 74, 0.35));
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-print i.fa-print {
  font-size: 16px !important;
  color: #2c3e5a !important;
  filter: drop-shadow(0 2px 4px rgba(44, 62, 90, 0.25));
  transition: all 0.3s ease;
}

#brand-page-wrapper .dt-toolbar-enhanced .dt-button.buttons-print:hover i.fa-print {
  color: #1a2a4a !important;
  transform: scale(1.15) rotate(-5deg);
  filter: drop-shadow(0 4px 8px rgba(44, 62, 90, 0.35));
}
</style>

<div id="brand-page-wrapper">
<?php if(session()->has('error')): ?>
  <div class="brand-alert" role="alert"><i class="fa-solid fa-circle-exclamation"></i><?php echo session('error'); ?></div>
<?php endif; ?>

<header class="brand-header" role="banner">
  <div class="brand-header-left">
    <button type="button" class="brand-back-btn" id="brand-back-btn" aria-label="Go back" title="Back"><i class="fa-solid fa-arrow-left"></i></button>
    <div class="brand-header-icon" aria-hidden="true"><i class="fa-solid fa-tags"></i></div>
    <div class="brand-header-text">
      <nav class="brand-breadcrumb" aria-label="Breadcrumb"><span>Products</span><i class="fa-solid fa-chevron-right"></i><span>Brands</span></nav>
      <h1>Brands</h1>
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

<script>
(function($){
'use strict';
function escapeHtml(value){ return $('<div>').text(value == null ? '' : String(value)).html(); }
function safeUrl(value){ try { var url = new URL(String(value || ''), window.location.origin); return /^(https?:|data:image\/)/i.test(url.protocol) ? url.href : ''; } catch(e) { return ''; } }
function noImage(){ return '<div class="brand-logo-placeholder" role="img" aria-label="No logo available"><i class="fa-regular fa-image" aria-hidden="true"></i></div>'; }
function imageMarkup(url,name,large){ var safe=safeUrl(url); if(!safe){ return large?'<span class="no-logo-icon"><i class="fa-regular fa-image" aria-hidden="true"></i></span>':noImage(); } var alt=escapeHtml((name||'Brand')+' logo'); return large?'<img src="'+escapeHtml(safe)+'" alt="'+alt+'" loading="lazy">':'<div class="brand-logo-cell"><img src="'+escapeHtml(safe)+'" alt="'+alt+'" loading="lazy"></div>'; }
function attachImageFallback(scope){ $(scope).find('img').off('error.brandImage').on('error.brandImage',function(){ var card=$(this).closest('.brand-grid-logo'); if(card.length){card.html('<span class="no-logo-icon"><i class="fa-regular fa-image" aria-hidden="true"></i></span>');}else{$(this).closest('.brand-logo-cell').replaceWith(noImage());} }); }
function tableApi(){ return $.fn.DataTable.isDataTable('#item-table') ? $('#item-table').DataTable() : null; }

// Build each action button once so it has exactly one icon and one label.
function addModernIcons() {
  $('#item-table .option a[title*="Edit"], #item-table .option a[title*="Delete"], #item-table .option a[title*="View"], #brand-grid-view .brand-grid-actions a[title*="Edit"], #brand-grid-view .brand-grid-actions a[title*="Delete"], #brand-grid-view .brand-grid-actions a[title*="View"]').each(function() {
    var $this = $(this);
    var title = ($this.attr('title') || '').toLowerCase();
    var iconClass = 'fa-regular fa-pen-to-square';

    if (title.indexOf('delete') !== -1) {
      iconClass = 'fa-regular fa-trash-can';
    } else if (title.indexOf('view') !== -1) {
      iconClass = 'fa-regular fa-eye';
    }

    var text = $this.text().trim();
    $this.empty();
    $this.append('<i class="' + iconClass + '" aria-hidden="true"></i>');

    if (text) {
      $this.append(document.createTextNode(' ' + text));
    }
  });
}

// Function to add rich icons to Excel and Print buttons
function addRichIcons() {
  var excelBtn = $('.dt-buttons .buttons-excel');
  if (excelBtn.length) {
    var html = excelBtn.html();
    if (!html.includes('fa-file-excel') && !html.includes('fa-')) {
      excelBtn.html('<i class="fa-solid fa-file-excel" aria-hidden="true"></i> ' + html.trim());
    }
  }
  
  var printBtn = $('.dt-buttons .buttons-print');
  if (printBtn.length) {
    var html = printBtn.html();
    if (!html.includes('fa-print') && !html.includes('fa-')) {
      printBtn.html('<i class="fa-solid fa-print" aria-hidden="true"></i> ' + html.trim());
    }
  }
}

$(function(){
  $('#brand-back-btn').on('click',function(){ if(window.history.length>1){window.history.back();}else{window.location.href='<?php echo get_uri("items"); ?>';} });
  
  $('#item-table').appTable({
    source:'<?php echo_uri("items/list_data30") ?>',
    order:[[0,'desc']],
    filterDropdown:[{name:'category_id',class:'w200'}],
    columns:[
      {title:"<?php echo 'Brand Name' ?>",class:'w20p',data:'brand_name',render:function(data){var name=escapeHtml(data||'—');return '<span class="brand-name-badge"><span class="brand-dot"></span>'+name+'</span>'; }},
      {title:"<?php echo 'Brand Logo' ?>",class:'w20p',data:'brandlogo_url',render:function(data,type,row){return imageMarkup(data,row&&row.brand_name,false);}},
      {title:"Actions<span class='sr-only'>Actions</span>",class:'text-center option w100',data:'options'}
    ],
    printColumns:[0,1,2], xlsColumns:[0,1,2]
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
    table.toggleClass('grid-mode-table',enable); 
    grid.toggleClass('active',enable);
    
    var $toggle = $('#item-table_wrapper').find('.brand-toolbar-toggle');
    $toggle.toggleClass('is-grid',enable).attr('aria-pressed',String(enable));
    
    if(enable) {
      $toggle.find('.grid-label').hide();
      $toggle.find('.table-label').show();
      buildGrid();
    } else {
      $toggle.find('.grid-label').show();
      $toggle.find('.table-label').hide();
    }
    
    if(api) api.columns.adjust();
    
    // Add icons after view change
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
    bindPrintSafety();
    
    setTimeout(function() {
      addRichIcons();
      addModernIcons();
    }, 50);
  }
  
  function bindPrintSafety(){
    $(document)
      .off('click.brandPrintFix', '#item-table_wrapper .buttons-print')
      .on('click.brandPrintFix', '#item-table_wrapper .buttons-print', function(){
        var wasGrid = gridMode;
        if (wasGrid) {
          setView(false);
        }
        var restored = false;
        var restore = function(){
          if (restored) return;
          restored = true;
          window.removeEventListener('afterprint', restore);
          if (wasGrid) {
            setView(true);
          }
        };
        window.addEventListener('afterprint', restore);
        setTimeout(restore, 1500);
      });
  }
  
  // Event handlers for DataTable
  $('#item-table').on('init.dt draw.dt', function(){
    setupToolbar(); 
    attachImageFallback('#item-table'); 
    bindPrintSafety();
    
    if(gridMode) {
      buildGrid();
    }
    
    setTimeout(function() {
      addRichIcons();
      addModernIcons();
    }, 100);
  });
  
  // MutationObserver for dynamic changes
  var iconObserver = new MutationObserver(function(mutations) {
    var shouldUpdate = false;
    mutations.forEach(function(mutation) {
      if (mutation.type === 'childList' && mutation.addedNodes.length) {
        shouldUpdate = true;
      }
    });
    if (shouldUpdate) {
      setTimeout(function() {
        addRichIcons();
        addModernIcons();
      }, 0);
    }
  });
  
  setTimeout(function() {
    var wrapper = document.getElementById('item-table_wrapper');
    if (wrapper) {
      iconObserver.observe(wrapper, {
        childList: true,
        subtree: true,
        attributes: false,
        characterData: true
      });
    }
  }, 500);
  
  // Click events for grid actions
  $(document).on('click','.brand-grid-actions a,.brand-grid-actions button',function(e){ 
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
      addModernIcons();
      addRichIcons();
    }, 50);
  });
  
  // Force initial icon addition after page load
  setTimeout(function() {
    addModernIcons();
    addRichIcons();
  }, 200);
});
})(jQuery);
</script>