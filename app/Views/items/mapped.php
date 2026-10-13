<?php
$viewapisBase = get_uri('items/viewapis/');
$user_id = $login_user->id ?? null;
if ($user_id) {
    $_SESSION['user_id'] = $user_id;
}
$mappedFilesBase = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/files/';
$mappedCategoryImages = $category_images ?? [];
$mappedDefaultCategoryImage = $default_category_image ?? ($mappedFilesBase . 'home/blank.jpeg');
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

<div id="mapped-app" class="mapped-page page-wrapper clearfix">
    <style>
        #mapped-app {
            font-family:'Inter',sans-serif;
            background:#f0f4f8;
            color:#1a2332;
            padding:20px;
            transition: background 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            min-height: calc(100vh - 60px);
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }
        body.mapped-dark-page .page-container,
        body.mapped-dark-page .scrollable-page {
            background: #0d1117 !important;
        }

        /* ----- HEADER ----- */
        .page-header {
            background: linear-gradient(135deg,#1a2332 0%,#2c3e50 100%);
            padding:22px 30px; border-radius:20px; margin-bottom:30px;
            box-shadow:0 10px 40px rgba(26,35,50,0.15); position:relative; overflow:hidden;
            transition: background 0.3s ease, border-color 0.3s ease;
        }
        .page-header::before { content:''; position:absolute; top:-50%; right:-10%; width:300px; height:300px; background:rgba(52,152,219,0.08); border-radius:50%; }
        .page-header::after { content:''; position:absolute; bottom:-50%; left:-5%; width:200px; height:200px; background:rgba(52,152,219,0.06); border-radius:50%; }
        .header-content { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; position:relative; z-index:1; }
        .header-left { display:flex; align-items:center; gap:15px; flex-wrap:wrap; }
        .btn-back {
            background:rgba(255,255,255,0.1); color:#fff; border:1px solid rgba(255,255,255,0.2);
            padding:8px 18px; border-radius:10px; font-size:14px; font-weight:500;
            transition:all .3s ease; cursor:pointer; display:flex; justify-content: center; align-items: center;
        }
        .btn-back:hover { background:rgba(255,255,255,0.2); transform:translateX(-4px); }
        .btn-back i { margin-right:8px; }
        .page-header h1 { color:#fff; font-size:24px; font-weight:700; margin:0; letter-spacing:-0.3px; }
        .page-header h1 i { color:#3498db; margin-right:12px; }
        .header-right { display:flex; align-items:center; gap:18px; flex-wrap:wrap; }
        .header-info { color:rgba(255,255,255,0.75); font-size:13px; }
        .header-info i { margin-right:6px; color:#3498db; }
        .page-header p { color:rgba(255,255,255,0.65); margin:4px 0 0; font-size:13px; }

        /* ----- TOOLBAR (shared) ----- */
        .toolbar {
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            padding: 18px 24px;
            border-radius: 16px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #eef2f6;
            transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
        .toolbar .left { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
        .toolbar .left .section-title {
            font-size:18px; font-weight:700; color:#1a2332; margin:0;
            display:flex; align-items:center; gap:10px;
            transition: color 0.3s ease;
        }
        .toolbar .left .section-title i { color:#3498db; }
        .toolbar .right { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .search-box { position:relative; }
        .search-box input {
            padding:8px 16px 8px 38px; border:2px solid #dce1e8; border-radius:10px;
            font-size:14px; outline:none; transition:.3s ease; background:#fff; min-width:180px;
        }
        .search-box input:focus { border-color:#3498db; box-shadow:0 0 0 3px rgba(52,152,219,0.1); }
        .search-box i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#7f8c8d; }
        .entries-select {
            padding:8px 14px; border:2px solid #dce1e8; border-radius:10px;
            font-size:14px; outline:none; background:#fff; cursor:pointer; transition:.3s ease;
        }
        .entries-select:focus { border-color:#3498db; }
        .entries-label { color:#7f8c8d; font-size:14px; font-weight:500; }

        .status-filter {
            padding:8px 14px; border:2px solid #dce1e8; border-radius:10px;
            font-size:14px; outline:none; background:#fff; cursor:pointer; transition:.3s ease;
            min-width:130px;
        }
        .status-filter:focus { border-color:#3498db; }

        .theme-btn {
            background:#fff; border:2px solid #dce1e8; border-radius:10px;
            padding:8px 14px; font-size:14px; font-weight:600; color:#1a2332;
            transition:.3s ease; cursor:pointer; display:inline-flex; align-items:center; gap:8px;
        }
        .theme-btn:hover { border-color:#3498db; background:#f7f9fc; }
        .theme-btn i { font-size:16px; }

        .toggle-view-btn {
            background:#fff; border:2px solid #dce1e8; border-radius:10px;
            padding:8px 16px; font-size:14px; font-weight:600; color:#1a2332;
            transition:.3s ease; cursor:pointer; display:inline-flex; align-items:center; gap:8px;
        }
        .toggle-view-btn:hover { border-color:#3498db; background:#f7f9fc; }
        .toggle-view-btn i { font-size:16px; }

        /* ===== CATEGORIES SCROLL WRAPPER ===== */
        #categoriesSection {
            display: block;
            overflow: visible;
        }

        .categories-scroll-wrapper {
            max-height: min(560px, calc(100vh - 280px));
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 4px 10px 40px 4px;
            margin-bottom: 8px;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
            position: relative;
            box-sizing: border-box;
            scrollbar-width: thin;
            scrollbar-color: #b0b8c4 #e8ecf0;
        }
        #categoriesContainer {
            padding-bottom: 16px;
            margin-bottom: 0;
        }
        .category-col {
            padding-bottom: 10px;
        }
        .categories-scroll-wrapper::-webkit-scrollbar {
            width: 8px !important;
            height: 8px !important;
        }
        .categories-scroll-wrapper::-webkit-scrollbar-track {
            background: #e8ecf0 !important;
            border-radius: 10px !important;
        }
        .categories-scroll-wrapper::-webkit-scrollbar-thumb {
            background: #b0b8c4 !important;
            border-radius: 10px !important;
            transition: background 0.3s ease;
            min-height: 40px;
        }
        .categories-scroll-wrapper::-webkit-scrollbar-thumb:hover {
            background: #8a94a4 !important;
        }

        /* DataTables wrapper */
        #tableViewContainer,
        #item-table_wrapper,
        .dataTables_wrapper {
            background: transparent !important;
            width: 100% !important;
        }
        #tableViewContainer {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            margin-top: 4px;
        }
        #item-table {
            width: 100% !important;
            table-layout: fixed !important;
            margin-top: 0 !important;
            background: #ffffff;
            border: 1px solid #e8ecf0;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            display: none !important;
        }
        /* Hide duplicate scroll header strip (causes dark bar above table) */
        .dataTables_scrollHead,
        .dataTables_scrollFoot {
            display: none !important;
            height: 0 !important;
            min-height: 0 !important;
            overflow: hidden !important;
        }

        /* Ensure pagination controls are always visible */
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 15px !important;
            padding-top: 10px !important;
            border-top: 1px solid #eef2f6;
            display: flex !important;
            justify-content: flex-end !important;
            flex-wrap: wrap !important;
            gap: 5px !important;
        }
        .dataTables_wrapper .dataTables_info {
            padding-top: 20px !important;
            margin-bottom: 10px !important;
        }
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 15px !important;
        }
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 15px !important;
        }

        /* Model page - ensure full scrollable content */
        #modelsSection {
            display: none;
            min-height: auto;
            padding-bottom: 40px;
            width: 100%;
        }
        #modelsSection.active {
            display: block;
        }

        /* ===== CATEGORY CARDS (neutral — no online/offline highlight) ===== */
        .category-card {
            background: #ffffff;
            border-radius:16px;
            overflow:hidden;
            box-shadow:0 4px 20px rgba(0,0,0,0.05);
            cursor:pointer;
            transition:all .3s cubic-bezier(0.165,0.84,0.44,1);
            border:2px solid #e8ecf0;
            height:100%;
            position:relative;
        }
        .category-card:hover {
            transform:translateY(-4px);
            box-shadow:0 12px 40px rgba(52,152,219,0.15);
            border-color:#3498db;
        }
        .category-card .card-body h5 { color: #1a2332; }
        .category-card .card-body .model-count { color: #5c6b7a; }
        .category-card .category-badge {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: #ffffff;
        }
        .category-card .card-image-wrap {
            background: linear-gradient(135deg, #f8fafc, #eef2f6);
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 170px;
        }
        .category-card .card-image {
            width: 100%;
            height: 170px;
            object-fit: contain;
            display: block;
            background: transparent;
        }
        .category-card .card-body { padding:18px 16px; text-align:center; }
        .category-card .card-body h5 { font-size:17px; font-weight:700; margin:0 0 4px; text-transform:capitalize; }
        .category-card .card-body .model-count { font-size:13px; margin:0; }
        .category-card .card-body .model-count i { margin-right:6px; }
        .category-badge {
            position:absolute; top:10px; right:10px;
            padding:3px 14px; border-radius:20px; font-size:12px; font-weight:600;
            box-shadow:0 4px 12px rgba(0,0,0,0.2);
        }
        
        .no-results { text-align:center; padding:40px 20px; color:#7f8c8d; display:none; }
        .no-results i { font-size:48px; color:#dce1e8; margin-bottom:15px; }

        #item-table tbody td img {
            display: inline-block;
            vertical-align: middle;
            border-radius: 8px;
            padding: 4px;
            background: #f8fafc;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.06);
        }

        /* ----- TABLE VIEW ----- */
        #item-table {
            border-radius: 12px;
            border-collapse: collapse;
            width: 100% !important;
            table-layout: fixed !important;
        }
        
        #item-table thead th {
            background: #e8eef4 !important;
            color: #1a2332 !important;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 18px;
            border: none;
            border-bottom: 2px solid #dce1e8 !important;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        #item-table tbody td {
            padding: 12px 18px;
            vertical-align: middle;
            font-size: 14px;
            border-bottom: 1px solid #eef2f6;
            color: #1a2332;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        #item-table tbody tr {
            transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
            cursor: default;
        }
        
        /* ===== TABLE ROW - ONLINE STATUS ONLY ===== */
        #item-table tbody tr.row-online {
            background: #ecfdf3 !important;
            box-shadow: inset 4px 0 0 #22c55e;
        }
        #item-table tbody tr.row-online td {
            background: #ecfdf3 !important;
            border-top: 1px solid #bbf7d0 !important;
            border-bottom: 1px solid #bbf7d0 !important;
        }
        #item-table tbody tr.row-online td:first-child {
            padding-left: 14px;
        }
        #item-table tbody tr.row-online:hover,
        #item-table tbody tr.row-online:hover td {
            background: #d1fae5 !important;
            box-shadow: inset 4px 0 0 #16a34a;
        }

        /* Offline rows — normal appearance */
        #item-table tbody tr.row-offline {
            background: #ffffff !important;
            border-left: none;
        }
        #item-table tbody tr.row-offline:hover {
            background: #f8f9fa !important;
            box-shadow: none;
            transform: none;
        }
        
        #item-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* ===== BUTTON STYLES ===== */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 80px;
            height: 38px;
            text-decoration: none;
            white-space: nowrap;
        }
        
        .btn-action i {
            font-size: 14px;
        }
        
        .btn-action:disabled,
        .btn-action[disabled] {
            opacity: 0.55;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .btn-api {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: #ffffff;
        }
        .btn-api:hover {
            background: linear-gradient(135deg, #138496, #0f7a8a);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
            color: #ffffff;
        }
        
        .btn-details-table {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: #ffffff;
        }
        .btn-details-table:hover {
            background: linear-gradient(135deg, #2980b9, #1a252f);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
            color: #ffffff;
        }

        /* ===== STATUS BADGE ===== */
        .status-badge {
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status-badge i {
            font-size: 8px;
        }
        .status-online {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        .status-offline {
            background: #eef2f6;
            color: #5c6b7a;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 10px;
            border: 2px solid #dce1e8;
            padding: 8px 16px;
            font-size: 14px;
            outline: none;
            transition: 0.3s ease;
            background: #ffffff;
        }
        
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
        }
        
        .dataTables_wrapper .dataTables_length select {
            border-radius: 10px;
            border: 2px solid #dce1e8;
            padding: 6px 12px;
            font-size: 14px;
            outline: none;
            background: #ffffff;
        }

        .dataTables_wrapper .dataTables_info {
            color: #6b7280;
            font-size: 14px;
            padding-top: 10px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 6px 14px;
            margin: 0 3px;
            border-radius: 8px;
            border: 1px solid #dce1e8;
            color: #1a2332;
            background: #ffffff;
            transition: 0.3s ease;
            cursor: pointer;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f0f4f8;
            border-color: #3498db;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #3498db;
            color: #ffffff;
            border-color: #3498db;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* ----- GRID VIEW ----- */
        .grid-view { display:none; gap:20px; margin-top:10px; }
        .grid-view.active { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); align-items: stretch; }
        .grid-item {
            border-radius:14px; padding:20px;
            box-shadow:0 2px 12px rgba(0,0,0,0.05);
            transition:.3s ease;
            border-left:4px solid #3498db;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .grid-item:hover { transform:translateY(-4px); box-shadow:0 8px 30px rgba(0,0,0,0.08); }
        
        /* Grid Item - Online highlight only */
        .grid-item.grid-online {
            background: #f0fff4;
            border-left-color: #28a745;
            box-shadow: 0 4px 18px rgba(40, 167, 69, 0.12);
        }
        .grid-item.grid-online:hover {
            background: #d4edda;
            box-shadow: 0 8px 30px rgba(40, 167, 69, 0.2);
        }

        /* Offline grid cards — normal */
        .grid-item.grid-offline {
            background: #ffffff;
            border-left-color: #dce1e8;
        }
        .grid-item.grid-offline:hover {
            background: #f8f9fa;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
        }
        
        .grid-item .grid-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid #ecf0f1; }
        .grid-item .grid-header h5 { margin:0; font-size:15px; font-weight:600; color:#1a2332; }
        .grid-item .grid-header h5 i { color:#3498db; margin-right:8px; }
        .grid-item .grid-image-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 96px;
            margin-bottom: 14px;
            padding: 14px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 1px solid #ecf0f1;
        }
        .grid-item .grid-device-image {
            width: 72px;
            height: 72px;
            object-fit: contain;
            background: #fff;
            border-radius: 10px;
            padding: 8px;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.06);
        }
        .grid-item .grid-detail { display:flex; justify-content:space-between; padding:5px 0; font-size:13px; }
        .grid-item .grid-detail label { color:#7f8c8d; font-weight:500; }
        .grid-item .grid-detail span { color:#1a2332; font-weight:500; }

        .grid-actions {
            display: flex;
            align-items: stretch;
            gap: 10px;
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid #ecf0f1;
        }
        .grid-actions .btn-action {
            flex: 1 1 0;
            min-width: 0;
            width: 100%;
            margin: 0;
            box-sizing: border-box;
        }

        .btn-details {
            background:linear-gradient(135deg,#3498db,#2c3e50); color:#fff; border:none;
            padding:8px 20px; border-radius:8px; font-size:13px; font-weight:600;
            transition:.3s ease; cursor:pointer; width:100%; margin-top:12px;
        }
        .btn-details:hover { transform:scale(1.02); box-shadow:0 4px 15px rgba(52,152,219,0.3); color:#fff; }
        .btn-details i { margin-right:6px; }

        .modal-content { border-radius:16px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.2); }
        .modal-header { background:linear-gradient(135deg,#1a2332,#2c3e50); color:#fff; border-radius:16px 16px 0 0; padding:20px 28px; border:none; }
        .modal-header .btn-close { filter:brightness(0) invert(1); opacity:0.8; }
        .modal-title { font-weight:700; font-size:20px; }
        .modal-title i { margin-right:12px; color:#3498db; }
        .modal-body { padding:28px; }
        .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .detail-item { background:#f8f9fa; padding:12px 16px; border-radius:10px; border-left:4px solid #3498db; transition:.3s ease; }
        .detail-item:hover { background:#e9ecef; transform:translateX(4px); }
        .detail-item label { font-size:11px; font-weight:700; color:#7f8c8d; text-transform:uppercase; letter-spacing:0.4px; display:block; margin-bottom:3px; }
        .detail-item span { font-size:14px; font-weight:500; color:#1a2332; }
        .detail-section-title { font-size:16px; font-weight:700; color:#1a2332; margin:18px 0 12px; padding-bottom:8px; border-bottom:2px solid #ecf0f1; }
        .detail-section-title i { color:#3498db; margin-right:10px; }
        
        /* ===== MAP CONTAINER WITH HIGHLIGHT ===== */
        .map-container {
            margin-top: 12px;
            border-radius: 12px;
            overflow: hidden;
            height: 250px;
            background: #e9ecef;
            position: relative;
            border: 2px solid #3498db;
        }
        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
            filter: brightness(0.95);
        }
        .map-container .map-overlay {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(26, 35, 50, 0.85);
            color: #ffffff;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            pointer-events: none;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            backdrop-filter: blur(4px);
        }
        .map-container .map-overlay i {
            color: #ff4d4d;
            font-size: 14px;
        }
        .map-container .map-overlay .pulse {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ff4d4d;
            animation: pulse-animation 1.5s ease-in-out infinite;
        }
        @keyframes pulse-animation {
            0% { transform: scale(0.8); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.7; }
            100% { transform: scale(0.8); opacity: 1; }
        }
        .map-container .map-marker-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 40px;
            color: #ff4d4d;
            text-shadow: 0 0 20px rgba(255, 77, 77, 0.5);
            pointer-events: none;
            z-index: 10;
            animation: bounce-marker 2s ease-in-out infinite;
        }
        @keyframes bounce-marker {
            0%, 100% { transform: translate(-50%, -50%) translateY(0); }
            50% { transform: translate(-50%, -50%) translateY(-10px); }
        }

        /* DataTables scroll cleanup */
        #mapped-app .dataTables_wrapper .dataTables_scroll {
            clear: both;
        }
        #mapped-app .dataTables_wrapper .dataTables_scrollBody {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }

        /* ===== DARK THEME (FULL OVERRIDES) ===== */
        #mapped-app.dark-theme {
            background: #0d1117 !important;
            color: #e6edf3 !important;
        }
        #mapped-app.dark-theme .page-header {
            background: linear-gradient(135deg, #161b22 0%, #0d1117 100%) !important;
            border: 1px solid #30363d !important;
        }
        #mapped-app.dark-theme .page-header h1 { color: #e6edf3 !important; }
        #mapped-app.dark-theme .page-header p { color: #8b949e !important; }
        #mapped-app.dark-theme .header-info { color: #8b949e !important; }
        #mapped-app.dark-theme .header-info i { color: #1f6feb !important; }
        #mapped-app.dark-theme .btn-back { 
            background: rgba(255,255,255,0.05) !important; 
            color: #e6edf3 !important; 
            border-color: #30363d !important;
        }
        #mapped-app.dark-theme .btn-back:hover { background: rgba(255,255,255,0.1) !important; }
        
        #mapped-app.dark-theme .toolbar {
            background: #161b22 !important;
            color: #e6edf3 !important;
            border-color: #30363d !important;
        }
        #mapped-app.dark-theme .toolbar .left .section-title { color: #e6edf3 !important; }
        #mapped-app.dark-theme .entries-label { color: #8b949e !important; }
        
        /* Dark Theme - Category Cards (neutral) */
        #mapped-app.dark-theme .category-card {
            background: #161b22 !important;
            border-color: #30363d !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
        }
        #mapped-app.dark-theme .category-card:hover {
            border-color: #1f6feb !important;
            box-shadow: 0 12px 40px rgba(31, 111, 235, 0.2) !important;
        }
        #mapped-app.dark-theme .category-card .card-body h5 { color: #e6edf3 !important; }
        #mapped-app.dark-theme .category-card .card-body .model-count { color: #8b949e !important; }
        #mapped-app.dark-theme .category-card .card-image-wrap {
            background: linear-gradient(180deg,rgb(58, 58, 58) 0%, #5f5f5f 100%) !important;
            border-bottom: 1px solid #30363d;
        }
        #mapped-app.dark-theme .category-card .card-image {
            background: transparent !important;
        }
        #mapped-app.dark-theme .category-card .category-badge {
            background: linear-gradient(135deg, #1f6feb, #0d419d) !important;
            color: #e6edf3 !important;
        }
        
        #mapped-app.dark-theme #item-table tbody td img {
            background: #5f5f5f !important;
            padding: 6px 8px !important;
            border-radius: 8px;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.1);
        }

        #mapped-app.dark-theme .search-box input { 
            background: #0d1117 !important; 
            color: #e6edf3 !important; 
            border-color: #30363d !important; 
        }
        #mapped-app.dark-theme .search-box input:focus { border-color: #1f6feb !important; }
        #mapped-app.dark-theme .search-box i { color: #8b949e !important; }
        
        #mapped-app.dark-theme .entries-select { 
            background: #0d1117 !important; 
            color: #e6edf3 !important; 
            border-color: #30363d !important; 
        }
        #mapped-app.dark-theme .entries-select option { background: #0d1117 !important; }
        
        #mapped-app.dark-theme .status-filter { 
            background: #0d1117 !important; 
            color: #e6edf3 !important; 
            border-color: #30363d !important; 
        }
        #mapped-app.dark-theme .status-filter option { background: #0d1117 !important; }
        
        #mapped-app.dark-theme .toggle-view-btn { 
            background: #0d1117 !important; 
            color: #e6edf3 !important; 
            border-color: #30363d !important; 
        }
        #mapped-app.dark-theme .toggle-view-btn:hover { border-color: #1f6feb !important; background: #1c2333 !important; }
        
        #mapped-app.dark-theme .theme-btn { 
            background: #0d1117 !important; 
            color: #e6edf3 !important; 
            border-color: #30363d !important; 
        }
        #mapped-app.dark-theme .theme-btn:hover { border-color: #1f6feb !important; background: #1c2333 !important; }
        
        /* Dark Theme - Table Rows */
        #mapped-app.dark-theme #item-table tbody tr.row-online {
            background: #0f2418 !important;
            box-shadow: inset 4px 0 0 #2ea043;
        }
        #mapped-app.dark-theme #item-table tbody tr.row-online td {
            color: #e6edf3 !important;
            background: #0f2418 !important;
            border-top: 1px solid #1f4a2d !important;
            border-bottom: 1px solid #1f4a2d !important;
        }
        #mapped-app.dark-theme #item-table tbody tr.row-online:hover,
        #mapped-app.dark-theme #item-table tbody tr.row-online:hover td {
            background: #1a3a24 !important;
            box-shadow: inset 4px 0 0 #3fb950;
        }
        
        #mapped-app.dark-theme #item-table tbody tr.row-offline {
            background: #161b22 !important;
            border-left: none;
        }
        #mapped-app.dark-theme #item-table tbody tr.row-offline td {
            color: #e6edf3 !important;
            background: #161b22 !important;
        }
        #mapped-app.dark-theme #item-table tbody tr.row-offline:hover {
            background: #1c2333 !important;
            box-shadow: none;
        }
        
        #mapped-app.dark-theme #item-table {
            background: #161b22 !important;
            border-color: #30363d !important;
        }
        #mapped-app.dark-theme #item-table thead th {
            background: #0d1117 !important;
            color: #e6edf3 !important;
            border-bottom: 2px solid #30363d !important;
        }
        
        #mapped-app.dark-theme #item-table tbody td {
            border-bottom: 1px solid #30363d !important;
            color: #e6edf3 !important;
        }
        #mapped-app.dark-theme #item-table tbody tr:not(.row-online) td {
            background: #161b22 !important;
        }
        #mapped-app.dark-theme #item-table tbody tr:not(.row-online):hover td {
            background: #1c2333 !important;
        }
        
        /* Dark Theme - Grid Items */
        #mapped-app.dark-theme .grid-item .grid-header {
            border-bottom-color: #30363d !important;
        }
        #mapped-app.dark-theme .grid-item .grid-image-wrap {
            background: #5f5f5f !important;
            border-color: #30363d !important;
        }
        #mapped-app.dark-theme .grid-item .grid-device-image {
            background: #f0f4f8 !important;
        }
        #mapped-app.dark-theme .grid-actions {
            border-top-color: #30363d !important;
        }
        #mapped-app.dark-theme .grid-item.grid-online {
            background: #0d1a12 !important;
            border-left-color: #2ea043 !important;
            color: #e6edf3 !important;
        }
        #mapped-app.dark-theme .grid-item.grid-online .grid-header h5 { color: #e6edf3 !important; }
        #mapped-app.dark-theme .grid-item.grid-online .grid-detail span { color: #e6edf3 !important; }
        #mapped-app.dark-theme .grid-item.grid-online .grid-detail label { color: #8b949e !important; }
        #mapped-app.dark-theme .grid-item.grid-online:hover { background: #1a2a1a !important; }
        
        #mapped-app.dark-theme .grid-item.grid-offline {
            background: #161b22 !important;
            border-left-color: #30363d !important;
            color: #e6edf3 !important;
        }
        #mapped-app.dark-theme .grid-item.grid-offline .grid-header h5 { color: #e6edf3 !important; }
        #mapped-app.dark-theme .grid-item.grid-offline .grid-detail span { color: #e6edf3 !important; }
        #mapped-app.dark-theme .grid-item.grid-offline .grid-detail label { color: #8b949e !important; }
        #mapped-app.dark-theme .grid-item.grid-offline:hover { background: #1c2333 !important; }
        
        /* Dark Theme - Buttons */
        #mapped-app.dark-theme .btn-api {
            background: linear-gradient(135deg, #1f6feb, #0d1117) !important;
        }
        #mapped-app.dark-theme .btn-api:hover {
            background: linear-gradient(135deg, #388bfd, #1f6feb) !important;
            box-shadow: 0 4px 15px rgba(31, 111, 235, 0.3) !important;
        }
        
        #mapped-app.dark-theme .btn-details-table {
            background: linear-gradient(135deg, #1f6feb, #0d1117) !important;
        }
        #mapped-app.dark-theme .btn-details-table:hover {
            background: linear-gradient(135deg, #388bfd, #1f6feb) !important;
            box-shadow: 0 4px 15px rgba(31, 111, 235, 0.3) !important;
        }
        
        #mapped-app.dark-theme .dataTables_wrapper .dataTables_filter input {
            background: #0d1117 !important;
            color: #e6edf3 !important;
            border-color: #30363d !important;
        }
        #mapped-app.dark-theme .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #1f6feb !important;
        }
        #mapped-app.dark-theme .dataTables_wrapper .dataTables_info {
            color: #8b949e !important;
        }
        #mapped-app.dark-theme .dataTables_wrapper .dataTables_length select {
            background: #0d1117 !important;
            color: #e6edf3 !important;
            border-color: #30363d !important;
        }
        #mapped-app.dark-theme .dataTables_wrapper .dataTables_length select option {
            background: #0d1117 !important;
        }
        #mapped-app.dark-theme .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #8b949e !important;
            background: #0d1117 !important;
            border-color: #30363d !important;
        }
        #mapped-app.dark-theme .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #1f6feb !important;
            color: #fff !important;
            border-color: #1f6feb !important;
        }
        #mapped-app.dark-theme .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #1c2333 !important;
            color: #e6edf3 !important;
            border-color: #1f6feb !important;
        }
        #mapped-app.dark-theme .dataTables_wrapper .dataTables_length label {
            color: #8b949e !important;
        }
        #mapped-app.dark-theme .dataTables_wrapper .dataTables_filter label {
            color: #8b949e !important;
        }
        
        #mapped-app.dark-theme .detail-item { 
            background: #1c2333 !important; 
            color: #e6edf3 !important; 
            border-left-color: #1f6feb !important;
        }
        #mapped-app.dark-theme .detail-item span { color: #e6edf3 !important; }
        #mapped-app.dark-theme .detail-item label { color: #8b949e !important; }
        #mapped-app.dark-theme .detail-item:hover { background: #2d3748 !important; }
        
        #mapped-app.dark-theme .modal-content { 
            background: #161b22 !important; 
            color: #e6edf3 !important; 
            border-color: #30363d !important;
        }
        #mapped-app.dark-theme .modal-header { 
            background: linear-gradient(135deg, #0d1117, #161b22) !important; 
            border-bottom-color: #30363d !important;
        }
        #mapped-app.dark-theme .modal-header .btn-close { filter: invert(1) !important; }
        #mapped-app.dark-theme .modal-body .detail-section-title { 
            color: #e6edf3 !important; 
            border-bottom-color: #30363d !important; 
        }
        
        #mapped-app.dark-theme .status-badge.status-online {
            background: #1a4a2a !important;
            color: #6fcf97 !important;
        }
        #mapped-app.dark-theme .badge.bg-primary {
            background-color: #1f6feb !important;
            color: #e6edf3 !important;
        }
        #mapped-app.dark-theme .status-badge.status-offline {
            background: #21262d !important;
            color: #8b949e !important;
        }
        
        #mapped-app.dark-theme .no-results { color: #8b949e !important; }
        #mapped-app.dark-theme .no-results i { color: #30363d !important; }
        
        #mapped-app.dark-theme .categories-scroll-wrapper::-webkit-scrollbar-track { background: #1c2333 !important; }
        #mapped-app.dark-theme .categories-scroll-wrapper::-webkit-scrollbar-thumb { background: #30363d !important; }
        #mapped-app.dark-theme .categories-scroll-wrapper::-webkit-scrollbar-thumb:hover { background: #484f58 !important; }
        
        #mapped-app.dark-theme .btn-details {
            background: linear-gradient(135deg, #1f6feb, #0d1117) !important;
        }
        #mapped-app.dark-theme .btn-details:hover {
            background: linear-gradient(135deg, #388bfd, #1f6feb) !important;
            box-shadow: 0 4px 15px rgba(31, 111, 235, 0.3) !important;
        }
        #mapped-app.dark-theme .map-container {
            background: #0d1117 !important;
            border-color: #1f6feb !important;
        }
        #mapped-app.dark-theme .status-badge { color: #e6edf3 !important; }
        
        /* Dark Theme - Perfect Scrollbar */
        #mapped-app.dark-theme .ps__rail-y {
            background-color: #1c2333 !important;
            border-color: #30363d !important;
        }
        #mapped-app.dark-theme .ps__rail-x {
            background-color: #1c2333 !important;
            border-color: #30363d !important;
        }
        #mapped-app.dark-theme .ps__thumb-y {
            background-color: #30363d !important;
        }
        #mapped-app.dark-theme .ps__thumb-x {
            background-color: #30363d !important;
        }
        #mapped-app.dark-theme .ps__thumb-y:hover {
            background-color: #484f58 !important;
        }
        #mapped-app.dark-theme .ps__thumb-x:hover {
            background-color: #484f58 !important;
        }

        /* ===== MODAL BODY OVERFLOW FIX ===== */
        .modal-dialog-scrollable .modal-body {
            overflow-y: auto;
            max-height: 70vh;
        }

        /* ===== PREVENT BODY SCROLL WHEN MODAL OPEN ===== */
        .modal-open {
            overflow: hidden !important;
        }
        .modal-open .modal {
            overflow-y: auto !important;
        }
        .modal-backdrop {
            z-index: 1040 !important;
        }
        .modal {
            z-index: 1050 !important;
        }

        .modal {
            z-index: 20050 !important;
        }
        .modal-backdrop {
            z-index: 20040 !important;
        }
        #detailsModal .modal-body {
            max-height: calc(100vh - 180px);
            overflow-y: auto;
        }
        #detailsModal .map-container {
            min-height: 280px;
        }
        #detailsModal .map-container iframe {
            display: block;
            min-height: 280px;
        }

        @media (max-width:768px) {
            #mapped-app { padding:12px; }
            .page-header { padding:18px; }
            .page-header h1 { font-size:20px; }
            .toolbar .left, .toolbar .right { width:100%; justify-content:flex-start; }
            .toolbar .right { flex-wrap:wrap; }
            .search-box input { min-width:140px; width:100%; }
            .grid-view.active { grid-template-columns:1fr; }
            .detail-grid { grid-template-columns:1fr; }
            .categories-scroll-wrapper {
                max-height: min(420px, calc(100vh - 240px));
                padding-bottom: 48px;
            }
            .header-right { width:100%; justify-content:flex-start; }
            .header-content { flex-direction:column; align-items:stretch; }
            .header-left { flex-wrap:wrap; }
            #item-table thead th,
            #item-table tbody td {
                padding: 10px 12px;
                font-size: 12px;
            }
            .dataTables_wrapper .dataTables_paginate {
                justify-content: center !important;
            }
            .btn-action {
                padding: 6px 12px;
                font-size: 12px;
                min-width: 60px;
                height: 32px;
            }
            .map-container {
                height: 180px;
            }
        }
        @media (max-width:576px) {
            .toolbar { padding:12px 16px; }
            .toolbar .right { gap:8px; }
            .search-box input { min-width:120px; font-size:13px; }
            .entries-select, .status-filter, .theme-btn, .toggle-view-btn { font-size:12px; padding:6px 10px; }
            .category-card .card-image-wrap { min-height: 130px; }
            .category-card .card-image { height:130px; }
            .page-header h1 { font-size:18px; }
            #item-table thead th,
            #item-table tbody td {
                padding: 8px 10px;
                font-size: 11px;
            }
            .btn-action {
                padding: 4px 10px;
                font-size: 11px;
                min-width: 50px;
                height: 28px;
            }
            .map-container {
                height: 150px;
            }
            .map-container .map-overlay {
                font-size: 10px;
                padding: 4px 12px;
                bottom: 8px;
            }
        }
    </style>

<!-- ===== HEADER ===== -->
<div class="page-header">
    <div class="header-content">
        <div class="header-left">
            <button class="btn-back" id="btnBack" onclick="handleBack()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <div>
                <h1><i class="fas fa-cube"></i> Brand Products</h1>
                <p id="headerSubtitle">Manage and monitor all your device categories and models</p>
            </div>
        </div>
        <div class="header-right">
            <span class="header-info"><i class="fas fa-database"></i> <span id="totalProducts">0</span> Products</span>
        </div>
    </div>
</div>

<!-- ===== PAGE 1: CATEGORIES ===== -->
<div id="categoriesSection">
    <div class="toolbar">
        <div class="left">
            <div class="section-title"><i class="fas fa-th-large"></i> Device Categories</div>
        </div>
        <div class="right">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="categorySearch" placeholder="Search categories..." onkeyup="filterCategories()" />
            </div>
            <span class="entries-label">Show:</span>
            <select class="entries-select" id="entriesSelect" onchange="changeEntries()">
                <option value="4">4</option>
                <option value="8" selected>8</option>
                <option value="12">12</option>
                <option value="16">16</option>
                <option value="all">All</option>
            </select>
            <button class="theme-btn" id="themeToggleCategories" onclick="toggleTheme()">
                <i class="fas fa-moon"></i> <span id="themeLabelCategories">Theme</span>
            </button>
        </div>
    </div>
    <div class="categories-scroll-wrapper" id="categoriesScrollWrapper">
        <div class="row g-4" id="categoriesContainer"></div>
    </div>
    <div class="no-results" id="noResults"><i class="fas fa-search"></i><h5>No categories found</h5><p>Try adjusting your search</p></div>
</div>

<!-- ===== PAGE 2: MODELS ===== -->
<div id="modelsSection">
    <div class="toolbar">
        <div class="left">
            <div class="section-title">
                <i class="fas fa-list" id="categoryTitleIcon"></i>
                <span id="categoryTitle">Models</span>
                <span class="badge bg-primary ms-2" id="modelCount">0</span>
            </div>
        </div>
        <div class="right">
            <select class="status-filter" id="statusFilterModels" onchange="filterModels()">
                <option value="all">All Status</option>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
            </select>

            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="modelSearch" placeholder="Search models..." onkeyup="filterModels()" />
            </div>

            <span class="entries-label">Show:</span>
            <select class="entries-select" id="modelEntriesSelect" onchange="changeModelEntries()">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="all">All</option>
            </select>

            <button class="toggle-view-btn" id="toggleViewBtn" onclick="toggleView()">
                <i class="fas fa-th-large"></i> <span id="toggleViewLabel">Grid</span>
            </button>

            <button class="theme-btn" id="themeToggleModels" onclick="toggleTheme()">
                <i class="fas fa-moon"></i> <span id="themeLabelModels">Theme</span>
            </button>
        </div>
    </div>

    <!-- Single table container - no duplicate wrappers -->
    <div id="tableViewContainer">
        <table id="item-table" class="display" width="100%"></table>
    </div>
    <div class="grid-view" id="gridViewContainer"></div>
</div>
</div>

<!-- ===== MODAL (outside mapped-app for correct stacking/map render) ===== -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" data-bs-backdrop="static" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel"><i class="fas fa-info-circle"></i> Device Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
        </div>
    </div>
</div>

<!-- ===== SCRIPTS ===== -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ========== GLOBALS ==========
    let allProducts = [];
    let categoryData = {};
    let currentCategory = '';
    let dataTable = null;
    let modalInstance = null;
    let currentEntries = 8;
    let currentModelEntries = 10;
    let currentView = 'table';
    let filteredModelData = [];
    let isDarkTheme = false;
    const VIEWAPIS_BASE = <?php echo json_encode($viewapisBase); ?>;

    function applyThemeState() {
        const mappedApp = document.getElementById('mapped-app');
        if (mappedApp) {
            mappedApp.classList.toggle('dark-theme', isDarkTheme);
        }
        document.body.classList.toggle('mapped-dark-page', isDarkTheme);

        const themeIcons = document.querySelectorAll('.theme-btn i');
        const themeLabels = document.querySelectorAll('.theme-btn span');
        themeIcons.forEach(icon => {
            icon.className = isDarkTheme ? 'fas fa-sun' : 'fas fa-moon';
        });
        themeLabels.forEach(label => {
            label.textContent = isDarkTheme ? 'Light' : 'Theme';
        });
    }

    try {
        const stored = localStorage.getItem('darkTheme');
        if (stored !== null) isDarkTheme = stored === 'true';
        else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) isDarkTheme = true;
    } catch (e) {}

    const FILES_BASE = <?php echo json_encode($mappedFilesBase, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    let categoryImages = <?php echo json_encode($mappedCategoryImages, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    let defaultCategoryImage = <?php echo json_encode($mappedDefaultCategoryImage, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    const categoryIcons = {
        'fan':'fa-fan','cooker':'fa-utensils','exhaust_hood':'fa-wind','default':'fa-cube'
    };

    const DEVICE_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp'];

    function normalizeDeviceImageUrl(url) {
        if (!url) return '';
        const value = String(url).trim();
        if (!value) return '';

        try {
            const parsed = new URL(value, window.location.origin);
            const pathMatch = parsed.pathname.match(/\/files\/(.+)/i);
            if (pathMatch && pathMatch[1]) {
                return FILES_BASE + pathMatch[1].replace(/^\/+/, '');
            }
            if (parsed.origin === window.location.origin) {
                return parsed.href;
            }
        } catch (e) {}

        if (value.indexOf('/files/') !== -1) {
            const parts = value.split('/files/');
            if (parts[1]) {
                return FILES_BASE + parts[1].replace(/^\/+/, '');
            }
        }

        return value;
    }

    function extractImageUrl(html) {
        if (!html) return '';
        const match = String(html).match(/src\s*=\s*["']([^"']+)["']/i);
        return match ? normalizeDeviceImageUrl(match[1]) : '';
    }

    function getDeviceImageCandidates(deviceType) {
        const key = (deviceType || '').toLowerCase().trim();
        if (!key) return [defaultCategoryImage];

        const candidates = [];
        if (categoryImages[key]) {
            candidates.push(categoryImages[key]);
        }

        ['brand_logo', 'home'].forEach(folder => {
            DEVICE_IMAGE_EXTENSIONS.forEach(ext => {
                candidates.push(FILES_BASE + folder + '/' + key + '.' + ext);
            });
        });

        candidates.push(defaultCategoryImage);
        return [...new Set(candidates.filter(Boolean))];
    }

    function getDeviceImageUrl(deviceType, deviceHtml) {
        const fromHtml = extractImageUrl(deviceHtml);
        if (fromHtml) return fromHtml;

        const candidates = getDeviceImageCandidates(deviceType);
        return candidates[0] || defaultCategoryImage;
    }

    function buildDeviceImageHtml(deviceType, deviceHtml, size, extraClass) {
        const dimension = size || 30;
        const url = getDeviceImageUrl(deviceType, deviceHtml);
        const alt = escapeAttr(deviceType || 'Device');
        const candidates = getDeviceImageCandidates(deviceType);
        const fallback = candidates.length > 1 ? candidates[1] : defaultCategoryImage;
        const className = 'device-type-img' + (extraClass ? ' ' + extraClass : '');

        return `<img src="${escapeAttr(url)}" data-fallback="${escapeAttr(fallback)}" style="width:${dimension}px;height:${dimension}px;object-fit:contain;" alt="${alt}" class="${className}" onerror="if(this.dataset.fallback && this.src!==this.dataset.fallback){this.src=this.dataset.fallback;}else{this.onerror=null;this.src=defaultCategoryImage;}" />`;
    }

    function refreshDeviceImageMap(products) {
        (products || []).forEach(p => {
            const key = (p.deviceType || '').toLowerCase().trim();
            if (!key || categoryImages[key]) return;

            const url = p.deviceImageUrl || extractImageUrl(p.device);
            if (url) categoryImages[key] = url;
        });
    }

    function getCategoryImage(cat) {
        const key = (cat || '').toLowerCase().trim();
        return categoryImages[key] || getDeviceImageUrl(key, '');
    }

    function mergeCategoryImages(apiImages) {
        if (!apiImages || typeof apiImages !== 'object') return;
        categoryImages = Object.assign({}, categoryImages, apiImages);
    }

    // ========== THEME TOGGLE ==========
    function toggleTheme() {
        isDarkTheme = !isDarkTheme;
        applyThemeState();
        if (dataTable) {
            dataTable.draw();
        }
        try { localStorage.setItem('darkTheme', isDarkTheme); } catch (e) {}
    }

    // ========== HANDLE BACK ==========
    function handleBack() {
        const modelsSection = document.getElementById('modelsSection');
        const categoriesSection = document.getElementById('categoriesSection');
        if (modelsSection.style.display !== 'none' && modelsSection.style.display !== '') {
            showCategories();
        } else {
            window.history.back();
        }
    }

    // ========== ONLINE STATUS HELPER ==========
    function isProductOnline(source) {
        if (!source) return false;
        const internet = String(source.internet ?? source[5] ?? '').trim().toLowerCase();
        if (internet === 'true' || internet === '1' || internet === 'online') return true;

        const statusHtml = String(source.status ?? source[0] ?? '').toLowerCase();
        return statusHtml.includes('green') || statusHtml.includes('online');
    }

    // ========== PROCESS DATA ==========
    function processTableData(data) {
        if (!data || !data.length) { showEmptyState(); return; }
        allProducts = []; categoryData = {};
        data.forEach(row => {
            const deviceType = (row[3] || 'unknown').toLowerCase().trim();
            if (!deviceType || deviceType==='null' || deviceType==='') return;
            const p = {
                status:row[0], device:row[1], nodeId:row[2]||'N/A',
                deviceType, model:row[4]||'Unknown', internet:row[5]||'N/A',
                lastSeen:row[6]||'N/A', latitude:row[7]||'N/A', longitude:row[8]||'N/A',
                userId:row[9]||'N/A', phone:row[10]||'N/A', email:row[11]||'N/A',
                apiInfo:row[12]||'N/A',
                itemId: row[13] || '',
                isOnline: false
            };
            p.isOnline = isProductOnline(p);
            p.deviceImageUrl = getDeviceImageUrl(deviceType, p.device);
            allProducts.push(p);
            if(!categoryData[deviceType]) categoryData[deviceType]=[];
            categoryData[deviceType].push(p);
        });
        refreshDeviceImageMap(allProducts);
        document.getElementById('totalProducts').textContent = allProducts.length;
        renderCategories();
    }

    // ========== PAGE 1: CATEGORIES ==========
    function renderCategories() {
        const container = document.getElementById('categoriesContainer');
        container.innerHTML = '';
        const sorted = Object.keys(categoryData).sort();
        const display = currentEntries==='all' ? sorted : sorted.slice(0,currentEntries);
        display.forEach(cat => {
            const products = categoryData[cat] || [];
            const img = getCategoryImage(cat);
            const icon = categoryIcons[cat] || categoryIcons['default'];

            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3 category-col';
            col.dataset.category = cat;
            col.innerHTML = `
                <div class="category-card" onclick="showCategoryModels('${cat}')">
                    <div class="card-image-wrap">
                        <img src="${img}" alt="${cat}" class="card-image" onerror="this.onerror=null;this.src=defaultCategoryImage;" />
                    </div>
                    <div class="card-body">
                        <h5><i class="fas ${icon}" style="color:#3498db;"></i> ${cat.replace('_',' ')}</h5>
                        <p class="model-count"><i class="fas fa-cubes"></i> ${products.length} Models</p>
                    </div>
                    <span class="category-badge">${products.length}</span>
                </div>
            `;
            container.appendChild(col);
        });
        document.getElementById('noResults').style.display = display.length===0 ? 'block' : 'none';
        document.getElementById('categoriesSection').style.display = 'block';
        document.getElementById('modelsSection').style.display = 'none';
        document.getElementById('headerSubtitle').textContent = 'Manage and monitor all your device categories and models';
        refreshCategoriesScroll();
    }

    function refreshCategoriesScroll() {
        const wrapper = document.getElementById('categoriesScrollWrapper');
        if (!wrapper) return;
        wrapper.style.overflowY = 'auto';
        void wrapper.offsetHeight;
    }

    function filterCategories() {
        const term = document.getElementById('categorySearch').value.toLowerCase().trim();
        const cols = document.querySelectorAll('.category-col');
        let visible=0;
        cols.forEach(col => {
            const cat = col.dataset.category;
            const show = term==='' || cat.replace('_',' ').toLowerCase().includes(term);
            col.style.display = show ? 'block' : 'none';
            if(show) visible++;
        });
        document.getElementById('noResults').style.display = visible===0 ? 'block' : 'none';
        refreshCategoriesScroll();
    }

    function changeEntries() {
        const sel = document.getElementById('entriesSelect');
        currentEntries = sel.value==='all' ? 'all' : parseInt(sel.value);
        renderCategories();
        filterCategories();
    }

    // ========== PAGE 2: MODELS ==========
    function showCategoryModels(category) {
        currentCategory = category;
        const products = categoryData[category] || [];
        if(!products.length) return;
        const icon = categoryIcons[category] || categoryIcons['default'];
        document.getElementById('categoryTitleIcon').className = `fas ${icon}`;
        document.getElementById('categoryTitle').textContent = category.replace('_',' ') + ' Models';
        document.getElementById('modelCount').textContent = products.length;

        document.getElementById('categoriesSection').style.display = 'none';
        document.getElementById('modelsSection').style.display = 'block';
        document.getElementById('headerSubtitle').textContent = `Viewing ${category.replace('_',' ')} models`;

        document.getElementById('statusFilterModels').value = 'all';
        document.getElementById('modelSearch').value = '';

        filteredModelData = [...products];
        applyModelEntries();
        renderGridView(filteredModelData);
        initializeDataTable(filteredModelData);
        if(currentView==='table') showTableView();
        else showGridView();
        updateToggleBtn();
        
        // Scroll to top of models section
        setTimeout(function() {
            document.getElementById('modelsSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }

    function applyModelEntries() {
        const sel = document.getElementById('modelEntriesSelect');
        const val = sel.value;
        currentModelEntries = val==='all' ? 'all' : parseInt(val);
    }

    function changeModelEntries() {
        applyModelEntries();
        filterModels();
    }

    function filterModels() {
        const term = document.getElementById('modelSearch').value.toLowerCase().trim();
        const statusFilter = document.getElementById('statusFilterModels').value;
        const products = categoryData[currentCategory] || [];

        let filtered = products;

        if (term !== '') {
            filtered = filtered.filter(p =>
                p.model.toLowerCase().includes(term) ||
                p.nodeId.toLowerCase().includes(term) ||
                p.deviceType.toLowerCase().includes(term)
            );
        }

        if (statusFilter !== 'all') {
            const wantOnline = statusFilter === 'online';
            filtered = filtered.filter(p => p.isOnline === wantOnline);
        }

        filteredModelData = filtered;
        renderGridView(filteredModelData);
        initializeDataTable(filteredModelData);
        if(currentView==='table') showTableView();
        else showGridView();
    }

    // ========== TOGGLE VIEW ==========
    function toggleView() {
        if(currentView==='table') {
            currentView='grid';
            showGridView();
        } else {
            currentView='table';
            showTableView();
        }
        updateToggleBtn();
    }

    function showTableView() {
        document.getElementById('tableViewContainer').style.display = 'block';
        document.getElementById('gridViewContainer').classList.remove('active');
        if(dataTable) {
            dataTable.columns.adjust().draw();
        }
    }

    function showGridView() {
        document.getElementById('tableViewContainer').style.display = 'none';
        document.getElementById('gridViewContainer').classList.add('active');
    }

    function updateToggleBtn() {
        const label = document.getElementById('toggleViewLabel');
        const icon = document.querySelector('#toggleViewBtn i');
        if(currentView==='table') {
            label.textContent = 'Grid';
            icon.className = 'fas fa-th-large';
        } else {
            label.textContent = 'Table';
            icon.className = 'fas fa-table';
        }
    }

    // ========== GRID VIEW RENDER ==========
    function renderGridView(products) {
        const container = document.getElementById('gridViewContainer');
        container.innerHTML = '';
        let display = products;
        if(currentModelEntries!=='all') display = products.slice(0,currentModelEntries);
        display.forEach(p => {
            const isOnline = p.isOnline;
            const statusClass = isOnline ? 'status-online' : 'status-offline';
            const gridClass = isOnline ? 'grid-online' : 'grid-offline';
            const statusText = isOnline ? 'Online' : 'Offline';
            const deviceImg = buildDeviceImageHtml(p.deviceType, p.device, 72, 'grid-device-image');
            const div = document.createElement('div');
            div.className = `grid-item ${gridClass}`;
            div.innerHTML = `
                <div class="grid-image-wrap">${deviceImg}</div>
                <div class="grid-header">
                    <h5>${escapeHtml(p.model)}</h5>
                    <span class="status-badge ${statusClass}"><i class="fas fa-circle" style="font-size:8px;"></i> ${statusText}</span>
                </div>
                <div class="grid-detail"><label>Node ID:</label><span>${p.nodeId}</span></div>
                <div class="grid-detail"><label>Device Type:</label><span>${p.deviceType}</span></div>
                <div class="grid-detail"><label>Internet:</label><span>${p.internet}</span></div>
                <div class="grid-detail"><label>Last Seen:</label><span>${p.lastSeen}</span></div>
                <div class="grid-actions">
                    ${p.itemId
                        ? `<a href="${VIEWAPIS_BASE + encodeURIComponent(p.itemId)}" class="btn-action btn-api"><i class="fas fa-code"></i> API</a>`
                        : `<button type="button" class="btn-action btn-api" disabled title="API unavailable"><i class="fas fa-code"></i> API</button>`
                    }
                    <button type="button" class="btn-action btn-details-table js-open-details" data-node-id="${escapeAttr(p.nodeId)}"><i class="fas fa-info-circle"></i> Details</button>
                </div>
            `;
            container.appendChild(div);
        });
    }

    // ========== DATATABLE - SINGLE TABLE ==========
    function initializeDataTable(products) {
        // Destroy existing DataTable if it exists
        if (dataTable) {
            dataTable.destroy();
            dataTable = null;
        }
        
        // Clear the table container - remove all content
        var tableElement = document.getElementById('item-table');
        if (tableElement) {
            // Remove all rows and rebuild
            while (tableElement.firstChild) {
                tableElement.removeChild(tableElement.firstChild);
            }
        }
        
        if (!products || products.length === 0) {
            // Show empty state in table
            $('#item-table').html('<tbody><tr><td colspan="12" class="text-center py-4">No models found</td></tr></tbody>');
            return;
        }
        
        const data = products.map(p => {
            const isOnline = p.isOnline;
            const statusText = isOnline ? 'Online' : 'Offline';
            const statusClass = isOnline ? 'status-online' : 'status-offline';
            const apiHref = p.itemId ? (VIEWAPIS_BASE + encodeURIComponent(p.itemId)) : '#';
            const apiButton = p.itemId
                ? `<a href="${apiHref}" class="btn-action btn-api"><i class="fas fa-code"></i> API</a>`
                : `<button class="btn-action btn-api" type="button" disabled title="API unavailable"><i class="fas fa-code"></i> API</button>`;
            return [
                `<span class="status-badge ${statusClass}"><i class="fas fa-circle" style="font-size:8px;margin-right:4px;"></i> ${statusText}</span>`,
                buildDeviceImageHtml(p.deviceType, p.device, 30),
                p.nodeId||'N/A', 
                p.deviceType||'N/A',
                p.model||'N/A', 
                p.internet||'N/A', 
                p.lastSeen||'N/A',
                p.userId||'N/A', 
                p.phone||'N/A', 
                p.email||'N/A',
                apiButton,
                `<button type="button" class="btn-action btn-details-table js-open-details" data-node-id="${escapeAttr(p.nodeId)}"><i class="fas fa-info-circle"></i> Details</button>`
            ];
        });
        
        dataTable = $('#item-table').DataTable({
            data: data,
            columns: [
                { title: "STATUS", data: 0 },
                { title: "DEVICE", data: 1 },
                { title: "NODE ID", data: 2 },
                { title: "DEVICE TYPE", data: 3 },
                { title: "MODEL NAME", data: 4 },
                { title: "INTERNET", data: 5 },
                { title: "LAST SEEN", data: 6 },
                { title: "USER ID", data: 7 },
                { title: "PHONE", data: 8 },
                { title: "EMAIL", data: 9 },
                { title: "API", data: 10 },
                { title: "ACTION", data: 11 }
            ],
            pageLength: currentModelEntries==='all' ? 25 : currentModelEntries,
            lengthMenu: [[5,10,25,50,-1],[5,10,25,50,"All"]],
            language: { 
                info: "Showing _START_ to _END_ of _TOTAL_ models", 
                infoEmpty: "No models found", 
                infoFiltered: "(filtered from _MAX_ total models)",
                paginate: { previous: "Previous", next: "Next" }
            },
            order: [[4,'asc']],
            destroy: true,
            paging: true,
            searching: false,
            lengthChange: false,
            ordering: true,
            info: true,
            autoWidth: false,
            scrollX: false,
            responsive: false,
            dom: 'rt<"row mt-2"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            columnDefs: [
                { targets: 0, width: '110px' },
                { targets: 4, width: '160px' },
                { targets: 10, width: '90px', orderable: false },
                { targets: 11, width: '100px', orderable: false }
            ],
            createdRow: function(row, data, dataIndex) {
                const p = products[dataIndex];
                $(row).removeClass('row-online row-offline');
                if (p && p.isOnline) {
                    $(row).addClass('row-online');
                }
            },
            drawCallback: function() {
                const table = this.api();
                table.rows({ page: 'current' }).every(function() {
                    const p = products[this.index()];
                    const row = this.node();
                    $(row).removeClass('row-online row-offline');
                    if (p && p.isOnline) {
                        $(row).addClass('row-online');
                    }
                });
            },
            rowCallback: function(row, data, dataIndex) {
                const p = products[dataIndex];
                $(row).removeClass('row-online row-offline');
                if (p && p.isOnline) {
                    $(row).addClass('row-online');
                }
            }
        });
    }

    // ========== GET AREA NAME FROM COORDINATES ==========
    function getAreaName(lat, lng, callback) {
        if (!lat || !lng || lat === 'N/A' || lng === 'N/A') {
            callback(null);
            return;
        }
        
        const latNum = parseFloat(lat);
        const lngNum = parseFloat(lng);
        if (isNaN(latNum) || isNaN(lngNum)) {
            callback(null);
            return;
        }
        
        // Use OpenStreetMap Nominatim API for reverse geocoding
        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latNum}&lon=${lngNum}&zoom=16&addressdetails=1`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    // Extract area name (city, town, or village)
                    const address = data.address;
                    let areaName = '';
                    if (address.city) areaName = address.city;
                    else if (address.town) areaName = address.town;
                    else if (address.village) areaName = address.village;
                    else if (address.suburb) areaName = address.suburb;
                    else if (address.county) areaName = address.county;
                    else if (address.state) areaName = address.state;
                    else areaName = data.display_name.split(',').slice(0, 2).join(', ');
                    callback(areaName);
                } else {
                    callback(null);
                }
            })
            .catch(() => callback(null));
    }

    // ========== DETAILS MODAL HELPERS ==========
    function escapeAttr(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;');
    }

    function escapeHtml(value) {
        return escapeAttr(value).replace(/>/g, '&gt;');
    }

    function parseCoordinate(value) {
        if (value === null || value === undefined) return null;
        const text = String(value).trim();
        if (!text || text.toLowerCase() === 'n/a' || text.toLowerCase() === 'null') return null;
        const num = parseFloat(text);
        return Number.isFinite(num) ? num : null;
    }

    function findProductByNodeId(nodeId) {
        const products = categoryData[currentCategory] || [];
        return products.find(x => String(x.nodeId) === String(nodeId));
    }

    function ensureDetailsModalInBody() {
        const modalEl = document.getElementById('detailsModal');
        if (modalEl && modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }
    }

    function buildMapHtml(lat, lng) {
        const latFixed = lat.toFixed(6);
        const lngFixed = lng.toFixed(6);
        const delta = 0.02;
        const bbox = `${lng - delta},${lat - delta},${lng + delta},${lat + delta}`;
        const osmSrc = `https://www.openstreetmap.org/export/embed.html?bbox=${encodeURIComponent(bbox)}&layer=mapnik&marker=${latFixed}%2C${lngFixed}`;
        return `
            <div class="map-container" id="detailsMapContainer">
                <iframe id="detailsMapFrame" title="Device location map" src="about:blank" data-map-src="${escapeAttr(osmSrc)}" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
                <div class="map-overlay">
                    <span class="pulse"></span>
                    <i class="fas fa-location-dot"></i>
                    <span id="areaNameDisplay">📍 ${latFixed}, ${lngFixed}</span>
                </div>
            </div>
        `;
    }

    function loadDetailsMap(lat, lng) {
        const frame = document.getElementById('detailsMapFrame');
        if (!frame) return;

        const mapSrc = frame.getAttribute('data-map-src');
        frame.src = mapSrc || 'about:blank';

        getAreaName(lat, lng, function(areaName) {
            const areaDisplay = document.getElementById('areaNameDisplay');
            if (areaDisplay && areaName) {
                areaDisplay.textContent = `📍 ${areaName} (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
            }
        });
    }

    function openDetailsModal(nodeId) {
        const p = findProductByNodeId(nodeId);
        if (!p) {
            alert('Product not found');
            return;
        }

        const isOnline = p.isOnline;
        const statusText = isOnline ? 'Online' : 'Offline';
        const color = isOnline ? '#27ae60' : '#e74c3c';
        const lat = parseCoordinate(p.latitude);
        const lng = parseCoordinate(p.longitude);
        const latDisplay = lat !== null ? lat : (p.latitude || 'N/A');
        const lngDisplay = lng !== null ? lng : (p.longitude || 'N/A');
        const mapHtml = (lat !== null && lng !== null)
            ? buildMapHtml(lat, lng)
            : '<p class="text-muted mt-2"><i class="fas fa-map-marker-alt"></i> No coordinates available</p>';

        const modalBody = document.getElementById('modalBody');
        modalBody.innerHTML = `
            <h6 class="detail-section-title"><i class="fas fa-microchip"></i> Device Information</h6>
            <div class="detail-grid">
                <div class="detail-item"><label>Model</label><span><strong>${escapeAttr(p.model)}</strong></span></div>
                <div class="detail-item"><label>Status</label><span style="color:${color};font-weight:700;">${statusText}</span></div>
                <div class="detail-item"><label>Node ID</label><span>${escapeAttr(p.nodeId)}</span></div>
                <div class="detail-item"><label>Device Type</label><span>${escapeAttr(p.deviceType)}</span></div>
                <div class="detail-item"><label>Internet</label><span>${escapeAttr(p.internet)}</span></div>
                <div class="detail-item"><label>Last Seen</label><span>${escapeAttr(p.lastSeen)}</span></div>
            </div>
            <h6 class="detail-section-title"><i class="fas fa-map-marker-alt"></i> Location</h6>
            <div class="detail-grid">
                <div class="detail-item"><label>Latitude</label><span>${latDisplay}</span></div>
                <div class="detail-item"><label>Longitude</label><span>${lngDisplay}</span></div>
            </div>
            ${mapHtml}
            <h6 class="detail-section-title"><i class="fas fa-user"></i> User</h6>
            <div class="detail-grid">
                <div class="detail-item"><label>User ID</label><span>${escapeAttr(p.userId)}</span></div>
                <div class="detail-item"><label>Phone</label><span>${escapeAttr(p.phone)}</span></div>
                <div class="detail-item"><label>Email</label><span>${escapeAttr(p.email)}</span></div>
            </div>
            <h6 class="detail-section-title"><i class="fas fa-code"></i> API</h6>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                ${p.itemId
                    ? `<a href="${VIEWAPIS_BASE + encodeURIComponent(p.itemId)}" class="btn-action btn-api" style="flex:1; min-width:150px; text-decoration:none;"><i class="fas fa-code"></i> View APIs</a>`
                    : `<button type="button" class="btn-action btn-api" disabled style="flex:1; min-width:150px;"><i class="fas fa-code"></i> View APIs</button>`
                }
            </div>
        `;

        const modalEl = document.getElementById('detailsModal');
        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(modalEl);
            modalEl.addEventListener('hidden.bs.modal', function() {
                const frame = document.getElementById('detailsMapFrame');
                if (frame) frame.src = 'about:blank';
            });
            modalEl.addEventListener('shown.bs.modal', function() {
                const mapLat = parseFloat(modalEl.dataset.mapLat || '');
                const mapLng = parseFloat(modalEl.dataset.mapLng || '');
                if (Number.isFinite(mapLat) && Number.isFinite(mapLng)) {
                    loadDetailsMap(mapLat, mapLng);
                }
            });
        }

        modalEl.dataset.mapLat = lat !== null ? String(lat) : '';
        modalEl.dataset.mapLng = lng !== null ? String(lng) : '';
        modalInstance.show();
    }

    function showDetails(modelName) {
        const products = categoryData[currentCategory] || [];
        const p = products.find(x => x.model === modelName);
        if (p) {
            openDetailsModal(p.nodeId);
            return;
        }
        alert('Product not found');
    }

    // ========== SHOW API INFO IN MODAL ==========
    function showApiInfoModal(modelName) {
        const products = categoryData[currentCategory] || [];
        const p = products.find(x => x.model === modelName);
        if(!p) { alert('Product not found'); return; }
        
        const displayDiv = document.getElementById('apiInfoDisplay');
        const contentDiv = document.getElementById('apiInfoContent');
        if (displayDiv && contentDiv) {
            contentDiv.textContent = p.apiInfo || 'No API information available';
            displayDiv.style.display = 'block';
            // Scroll to the API info section
            displayDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            alert(`API Info for ${p.model}:\n${p.apiInfo}`);
        }
    }

    // ========== SHOW API INFO (from table) ==========
    function showApiInfo(modelName) {
        const products = categoryData[currentCategory] || [];
        const p = products.find(x => x.model === modelName);
        if (!p) { alert('Product not found'); return; }
        if (p.itemId) {
            window.location.href = VIEWAPIS_BASE + encodeURIComponent(p.itemId);
            return;
        }
        alert('API page is not available for this device.');
    }

    // ========== BACK TO CATEGORIES ==========
    function showCategories() {
        if(dataTable) { 
            dataTable.destroy(); 
            dataTable = null; 
        }
        // Clear table
        var tableElement = document.getElementById('item-table');
        if (tableElement) {
            while (tableElement.firstChild) {
                tableElement.removeChild(tableElement.firstChild);
            }
        }
        document.getElementById('modelsSection').style.display = 'none';
        document.getElementById('categoriesSection').style.display = 'block';
        document.getElementById('headerSubtitle').textContent = 'Manage and monitor all your device categories and models';
        renderCategories();
        filterCategories();
        currentView = 'table';
        updateToggleBtn();
        document.getElementById('modelSearch').value = '';
        document.getElementById('statusFilterModels').value = 'all';
    }

    // ========== EMPTY STATE ==========
    function showEmptyState() {
        document.getElementById('categoriesContainer').innerHTML = `
            <div class="col-12"><div class="empty-state"><i class="fas fa-box-open"></i><h4>No Products Found</h4><p>Inventory is empty.</p></div></div>
        `;
    }

    applyThemeState();

    // ========== INIT ==========
    $(document).ready(function() {
        ensureDetailsModalInBody();

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.js-open-details');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();
            openDetailsModal(btn.getAttribute('data-node-id'));
        });

        // Initially hide models section
        document.getElementById('modelsSection').style.display = 'none';
        
        $.ajax({
            url: "<?php echo site_url('items/list_data_brand'); ?>",
            type:'GET', dataType:'json',
            success: function(res) {
                if (res && res.category_images) mergeCategoryImages(res.category_images);
                if (res && res.default_category_image) defaultCategoryImage = res.default_category_image;
                if(res && res.data) processTableData(res.data);
                else showEmptyState();
            },
            error: function() { showEmptyState(); console.error('load failed'); }
        });
    });
</script>