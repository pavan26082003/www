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
#brand-page-wrapper .brand-table-card{overflow:hidden;background:var(--panel);border:1px solid var(--line);border-radius:22px;box-shadow:var(--shadow)}
/* DataTable controls remain visible in both views */
#brand-page-wrapper .dataTables_wrapper{padding:0;background:#fff}
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced{display:flex;flex-wrap:wrap;align-items:center;gap:12px;padding:18px 20px;border-bottom:1px solid var(--line);background:linear-gradient(180deg,#fff,#fafcff)}
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_length,#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter{float:none!important;margin:0!important}
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter{margin-left:auto!important}
#brand-page-wrapper .dataTables_wrapper label{font-size:13px;font-weight:650;color:#5c6e89}
#brand-page-wrapper .dataTables_wrapper select,#brand-page-wrapper .dataTables_wrapper .dataTables_filter input{height:38px;margin-left:8px!important;padding:0 12px;border:1px solid #dce5f2;border-radius:10px;background:#fff;color:#243a5a;font:600 13px Inter,Arial,sans-serif;outline:0;transition:.2s}
#brand-page-wrapper .dataTables_wrapper .dataTables_filter input{width:220px;padding-left:36px;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%2364758b' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='7'/%3E%3Cpath d='m20 20-4-4'/%3E%3C/svg%3E") no-repeat 12px center}
#brand-page-wrapper .dataTables_wrapper select:focus,#brand-page-wrapper .dataTables_wrapper .dataTables_filter input:focus{border-color:#a5b4fc;box-shadow:0 0 0 4px rgba(79,70,229,.10)}
#brand-page-wrapper .brand-toolbar-toggle{min-height:38px;padding:0 13px;color:#4f46e5;background:#f5f3ff;border:1px solid #ddd6fe;box-shadow:none}
#brand-page-wrapper .brand-toolbar-toggle:hover{background:#ede9fe;transform:translateY(-1px)}
#brand-page-wrapper .brand-toolbar-toggle .table-label{display:none}
#brand-page-wrapper .brand-toolbar-toggle.is-grid .grid-label{display:none}
#brand-page-wrapper .brand-toolbar-toggle.is-grid .table-label{display:inline}
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
#brand-page-wrapper .dataTables_paginate{padding:16px 20px 20px!important;float:right!important}
#brand-page-wrapper .dataTables_info{padding:21px 20px!important;color:#657790!important;font-size:13px!important}
#brand-page-wrapper .dataTables_paginate .paginate_button{min-width:34px!important;height:34px;margin:0 2px!important;padding:8px 10px!important;border:1px solid #e0e7f1!important;border-radius:9px!important;background:#fff!important;color:#52647e!important;font-size:12px!important;font-weight:700!important}
#brand-page-wrapper .dataTables_paginate .paginate_button.current,#brand-page-wrapper .dataTables_paginate .paginate_button:hover:not(.disabled){border-color:var(--primary)!important;background:var(--primary)!important;color:#fff!important}
#brand-page-wrapper .dataTables_paginate .paginate_button.disabled{opacity:.45!important}
/* Existing generated action anchors/buttons */
#brand-page-wrapper .option a,#brand-page-wrapper .option button,#brand-page-wrapper .brand-grid-actions a,#brand-page-wrapper .brand-grid-actions button{display:inline-flex!important;align-items:center;justify-content:center;gap:7px;min-height:34px;margin:2px;padding:7px 10px!important;border-radius:10px!important;font:700 12px Inter,Arial,sans-serif!important;text-decoration:none!important;transition:.2s ease}
#brand-page-wrapper .option a:hover,#brand-page-wrapper .option button:hover,#brand-page-wrapper .brand-grid-actions a:hover,#brand-page-wrapper .brand-grid-actions button:hover{transform:translateY(-2px);filter:brightness(.98);box-shadow:0 7px 14px rgba(43,65,104,.13)}
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
@media(max-width:760px){#brand-page-wrapper{padding:18px 14px}.brand-header{padding:19px!important;border-radius:18px!important}.brand-header-left{gap:11px!important}.brand-header-icon{display:none!important}#brand-page-wrapper h1{font-size:23px}.brand-header-text p{font-size:12px!important}.brand-add-btn{width:100%;margin-top:2px}.brand-header{align-items:stretch!important;flex-direction:column}.brand-header-left{width:100%}.dataTables_wrapper .dt-toolbar-enhanced{padding:14px!important}.dataTables_wrapper .dt-toolbar-enhanced .dataTables_filter{order:3;width:100%;margin-left:0!important}.dataTables_wrapper .dataTables_filter input{width:calc(100% - 56px)!important}.dt-table-area{padding:0 12px 8px!important}#brand-page-wrapper #brand-grid-view{grid-template-columns:repeat(auto-fill,minmax(155px,1fr));gap:12px}.brand-grid-card{min-height:210px!important;padding:15px!important}.brand-grid-logo{width:90px!important;height:90px!important}.dataTables_info{padding:16px 12px!important}.dataTables_paginate{padding:12px!important;float:none!important;display:flex!important;justify-content:center!important;flex-wrap:wrap}.brand-logo-cell,.brand-logo-placeholder{width:58px!important;height:58px!important}}

/* === Production DataTable layout: controls remain visible in both table and card presentation === */
#brand-page-wrapper{--panel:#ffffff;--panel-soft:#f6fafc;--canvas:#eef5f7;--ink:#172b4d;--muted:#61718b;--primary:#167a8b;--primary-dark:#105c69;--line:#d9e6eb;--accent:#e1f3f5;--danger:#c0394b}
#brand-page-wrapper .brand-table-card{background:var(--panel);border-color:var(--line);box-shadow:0 16px 38px rgba(23,43,77,.10)}
#brand-page-wrapper .dataTables_wrapper{position:relative;background:var(--panel)}
#brand-page-wrapper .dt-toolbar-enhanced{display:flex!important;align-items:center;gap:12px;flex-wrap:wrap;padding:16px 20px!important;background:linear-gradient(180deg,#f9fcfd 0%,#f1f8fa 100%)!important;border-bottom:1px solid var(--line)!important;box-shadow:inset 0 -1px 0 rgba(255,255,255,.7)}
#brand-page-wrapper .dt-toolbar-heading{display:flex;align-items:center;gap:10px;min-width:170px;margin-right:auto;font-weight:800;color:var(--ink);font-size:14px}
#brand-page-wrapper .dt-toolbar-heading i{display:grid;place-items:center;width:34px;height:34px;border-radius:10px;background:#d7f0f3;color:var(--primary)}
#brand-page-wrapper .dt-toolbar-heading small{display:block;margin-top:2px;font-size:11px;font-weight:600;color:var(--muted)}
#brand-page-wrapper .dt-toolbar-enhanced .dataTables_length,#brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter,#brand-page-wrapper .dt-toolbar-enhanced .dt-buttons,#brand-page-wrapper .dt-toolbar-enhanced .buttons-collection,#brand-page-wrapper .dt-toolbar-enhanced [class*="filter"]{float:none!important;margin:0!important}
#brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter{margin-left:0!important}
#brand-page-wrapper .dt-toolbar-enhanced .dt-buttons{display:flex;align-items:center;gap:7px;flex-wrap:wrap}
#brand-page-wrapper .dt-toolbar-enhanced .dt-button,#brand-page-wrapper .dt-toolbar-enhanced button:not(.brand-toolbar-toggle){display:inline-flex!important;align-items:center;justify-content:center;gap:7px;min-height:38px!important;padding:8px 12px!important;border:1px solid #bcd5da!important;border-radius:10px!important;background:#fff!important;color:#1f5360!important;font:700 12px Inter,Arial,sans-serif!important;box-shadow:0 2px 6px rgba(23,43,77,.05)!important;transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease,background .18s ease!important}
#brand-page-wrapper .dt-toolbar-enhanced .dt-button:hover,#brand-page-wrapper .dt-toolbar-enhanced button:not(.brand-toolbar-toggle):hover{transform:translateY(-1px);border-color:#7fbdc7!important;background:#f4fcfd!important;box-shadow:0 8px 16px rgba(22,122,139,.14)!important}
#brand-page-wrapper .brand-toolbar-toggle{border-color:var(--primary)!important;background:var(--primary)!important;color:#fff!important;box-shadow:0 7px 14px rgba(22,122,139,.22)!important}
#brand-page-wrapper .brand-toolbar-toggle:hover{background:var(--primary-dark)!important}
#brand-page-wrapper .dataTables_wrapper .dataTables_filter input{border-color:#bcd5da!important;background:#fff!important;color:var(--ink)!important}
#brand-page-wrapper .dataTables_wrapper .dataTables_filter input:focus{border-color:var(--primary)!important;box-shadow:0 0 0 3px rgba(22,122,139,.15)!important}
#brand-page-wrapper .dataTables_wrapper select{border-color:#bcd5da!important;background:#fff!important;color:var(--ink)!important}
#brand-page-wrapper .dataTables_info{color:var(--muted)!important;font-weight:600!important}
#brand-page-wrapper .dataTables_paginate .paginate_button.current,#brand-page-wrapper .dataTables_paginate .paginate_button:hover:not(.disabled){background:var(--primary)!important;border-color:var(--primary)!important;color:#fff!important}
#brand-page-wrapper .grid-mode-table{display:none!important}
#brand-page-wrapper #brand-grid-view{margin:0!important;padding:20px!important;background:#fbfdfe;border-top:0!important}
#brand-page-wrapper .brand-grid-card{border-color:#d8e6ea!important;background:#fff!important;box-shadow:0 7px 17px rgba(23,43,77,.07)!important}
#brand-page-wrapper .brand-grid-card:hover{border-color:#8cc5ce!important;box-shadow:0 18px 32px rgba(22,122,139,.15)!important}
#brand-page-wrapper .brand-grid-logo{background:#f1f8fa!important;border-color:#cde0e5!important}
#brand-page-wrapper .option a[title*="View"],#brand-page-wrapper .brand-grid-actions a[title*="View"]{background:#e4f5f7!important;color:#126473!important}
#brand-page-wrapper .option a[title*="Edit"],#brand-page-wrapper .brand-grid-actions a[title*="Edit"]{background:#eef5ff!important;color:#2863a5!important}
#brand-page-wrapper .option a[title*="Delete"],#brand-page-wrapper .brand-grid-actions a[title*="Delete"]{background:#fff0f1!important;color:#b63346!important}
@media(max-width:860px){#brand-page-wrapper .dt-toolbar-heading{width:100%;margin-right:0}#brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter{flex:1;min-width:220px}#brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter input{width:calc(100% - 58px)!important}}
@media(max-width:560px){#brand-page-wrapper .dt-toolbar-enhanced{padding:13px!important;gap:9px}#brand-page-wrapper .dt-toolbar-enhanced .dataTables_length,#brand-page-wrapper .dt-toolbar-enhanced .brand-toolbar-toggle{width:100%}#brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter{width:100%;min-width:0}#brand-page-wrapper .dt-toolbar-enhanced .dt-buttons{width:100%;overflow-x:auto;flex-wrap:nowrap;padding-bottom:2px}#brand-page-wrapper .dt-toolbar-enhanced .dt-button{white-space:nowrap}#brand-page-wrapper #brand-grid-view{grid-template-columns:repeat(auto-fill,minmax(150px,1fr));padding:13px!important}}


/* Final alignment pass: toolbar and DataTables footer */
#brand-page-wrapper .dataTables_wrapper .dt-toolbar-enhanced{
  display:flex!important;
  align-items:center!important;
  justify-content:flex-start!important;
  gap:12px!important;
  min-height:94px;
}
#brand-page-wrapper .dt-toolbar-heading{
  flex:1 1 320px!important;
  margin-right:10px!important;
  min-width:260px!important;
}
#brand-page-wrapper .dt-toolbar-enhanced .dataTables_length,
#brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter,
#brand-page-wrapper .dt-toolbar-enhanced .dt-buttons,
#brand-page-wrapper .dt-toolbar-enhanced [class*="filter"]{
  flex:0 0 auto!important;
  display:flex!important;
  align-items:center!important;
  min-height:40px;
}
#brand-page-wrapper .dt-toolbar-enhanced .dataTables_length label,
#brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter label{margin:0!important;white-space:nowrap!important}
#brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter{margin-left:0!important}
#brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter input{width:240px!important}
#brand-page-wrapper .dt-toolbar-enhanced .brand-toolbar-toggle{flex:0 0 auto!important;order:20!important;margin-left:0!important}
#brand-page-wrapper .dt-footer-enhanced{
  display:flex!important;
  align-items:center!important;
  justify-content:space-between!important;
  gap:14px!important;
  min-height:70px;
  padding:8px 20px 14px;
  border-top:1px solid #e3edf0;
  background:#ffffff;
}
#brand-page-wrapper .dt-footer-enhanced .dataTables_info,
#brand-page-wrapper .dt-footer-enhanced .dataTables_paginate{
  float:none!important;
  margin:0!important;
  padding:8px 0!important;
}
#brand-page-wrapper .dt-footer-enhanced .dataTables_paginate{display:flex!important;align-items:center!important;justify-content:flex-end!important;flex-wrap:wrap!important;gap:4px!important}
#brand-page-wrapper .dt-footer-enhanced .paginate_button{display:inline-flex!important;align-items:center!important;justify-content:center!important}
@media (max-width:1180px){
  #brand-page-wrapper .dt-toolbar-heading{flex-basis:100%!important;margin-right:0!important}
  #brand-page-wrapper .dt-toolbar-enhanced{min-height:auto}
}
@media (max-width:720px){
  #brand-page-wrapper .dt-toolbar-enhanced{align-items:stretch!important}
  #brand-page-wrapper .dt-toolbar-heading{width:100%!important}
  #brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter{flex:1 1 100%!important;width:100%!important}
  #brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter label{width:100%!important}
  #brand-page-wrapper .dt-toolbar-enhanced .dataTables_filter input{width:calc(100% - 58px)!important}
  #brand-page-wrapper .dt-toolbar-enhanced .dataTables_length,
  #brand-page-wrapper .dt-toolbar-enhanced .brand-toolbar-toggle{flex:1 1 auto!important}
  #brand-page-wrapper .dt-footer-enhanced{flex-direction:column!important;align-items:stretch!important;padding:10px 14px 14px}
  #brand-page-wrapper .dt-footer-enhanced .dataTables_paginate{justify-content:center!important}
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

$(function(){
  $('#brand-back-btn').on('click',function(){ if(window.history.length>1){window.history.back();}else{window.location.href='<?php echo get_uri("items"); ?>';} });
  $('#item-table').appTable({
    source:'<?php echo_uri("items/list_data30") ?>',
    order:[[0,'desc']],
    filterDropdown:[{name:'category_id',class:'w200'}],
    columns:[
      {title:"<?php echo 'Brand Name' ?>",class:'w20p',data:'brand_name',render:function(data){var name=escapeHtml(data||'—');return '<span class="brand-name-badge"><span class="brand-dot"></span>'+name+'</span>'; }},
      {title:"<?php echo 'Brand Logo' ?>",class:'w20p',data:'brandlogo_url',render:function(data,type,row){return imageMarkup(data,row&&row.brand_name,false);}},
      {title:"<i class='fa-solid fa-ellipsis' aria-hidden='true'></i><span class='sr-only'>Actions</span>",class:'text-center option w100',data:'options'}
    ],
    printColumns:[0,1,2,3], xlsColumns:[0,1,2,3]
  });

  var gridMode=false;
  function buildGrid(){
    var api=tableApi(), grid=$('#brand-grid-view'); if(!api) return;
    var rows=api.rows({search:'applied',page:'current'}).data(); grid.empty();
    if(!rows || !rows.length){ grid.html('<div class="brand-empty-state"><i class="fa-regular fa-folder-open" aria-hidden="true"></i><strong>No matching brands</strong><div>Try another search or create a new brand.</div></div>'); return; }
    rows.each(function(row){ var name=row&&row.brand_name?String(row.brand_name):'—';
      grid.append('<article class="brand-grid-card" tabindex="0" aria-label="'+escapeHtml(name)+' brand"><div class="brand-grid-logo">'+imageMarkup(row&&row.brandlogo_url,name,true)+'</div><h3 class="brand-grid-name">'+escapeHtml(name)+'</h3><div class="brand-grid-actions">'+(row&&row.options?row.options:'')+'</div></article>');
    }); attachImageFallback(grid);
  }
  function gridHost(){ var table=$('#item-table'); var grid=$('#brand-grid-view'); if(table.length && grid.parent()[0]!==table.parent()[0]) grid.insertBefore(table); return grid; }
  function setView(enable){
    gridMode=enable; var api=tableApi(),table=$('#item-table'),grid=gridHost();
    table.toggleClass('grid-mode-table',enable); grid.toggleClass('active',enable);
    $('#item-table_wrapper').find('.brand-toolbar-toggle').toggleClass('is-grid',enable).attr('aria-pressed',String(enable)).find('.grid-label').text(enable?'Table view':'Card view');
    if(enable) buildGrid(); if(api) api.columns.adjust();
  }
  function isBottomControl(node){return node.is('.dataTables_info,.dataTables_paginate,.dataTables_processing');}
  function setupFooter(){
    var wrapper=$('#item-table_wrapper'); if(!wrapper.length) return;
    var footer=wrapper.children('.dt-footer-enhanced');
    if(!footer.length){ footer=$('<div class="dt-footer-enhanced" role="navigation" aria-label="Table pagination"></div>'); wrapper.append(footer); }
    var info=wrapper.children('.dataTables_info'), pages=wrapper.children('.dataTables_paginate');
    if(info.length) footer.append(info); if(pages.length) footer.append(pages);
  }
  function setupToolbar(){
    var wrapper=$('#item-table_wrapper'); if(!wrapper.length || wrapper.data('brandToolbarReady')) return; wrapper.data('brandToolbarReady',true);
    var table=$('#item-table');
    var toolbar=$('<div class="dt-toolbar-enhanced" role="toolbar" aria-label="Brand table tools"><div class="dt-toolbar-heading"><i class="fa-solid fa-table-list" aria-hidden="true"></i><div>Brand directory<small>Search, filter, export and manage brands</small></div></div></div>');
    var toggle=$('<button type="button" class="brand-toolbar-toggle" aria-pressed="false"><i class="fa-solid fa-table-cells-large" aria-hidden="true"></i><span class="grid-label">Card view</span></button>');
    /* Move every top DataTable/appTable control, including Buttons, export, filters, length and search. Bottom record count/pagination stay untouched. */
    var candidates=wrapper.children().filter(function(){ var n=$(this); return !n.is(table) && !isBottomControl(n) && !n.hasClass('dt-toolbar-enhanced') && !n.find(table).length; });
    candidates.each(function(){ toolbar.append(this); }); toolbar.append(toggle); wrapper.prepend(toolbar);
    toggle.on('click',function(){setView(!gridMode);}); gridHost(); setupFooter();
  }
  $('#item-table').on('init.dt draw.dt',function(){ setupToolbar(); setupFooter(); attachImageFallback('#item-table'); if(gridMode) buildGrid(); });
  $(document).on('click','.brand-grid-actions a,.brand-grid-actions button',function(e){ e.stopPropagation(); });
  $(document).on('keydown','.brand-grid-card',function(e){ if((e.key==='Enter'||e.key===' ')&&!$(e.target).is('a,button,input')){e.preventDefault();$(this).find('a,button').first().trigger('click');} });
});
})(jQuery);
</script>