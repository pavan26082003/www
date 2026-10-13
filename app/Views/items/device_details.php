<style>

#page-content{
    padding-top:0 !important;
    margin-top:0 !important;
}

.page-wrapper{
    padding-top:0 !important;
    margin-top:0 !important;
}

</style>
<div id="page-content" class="clearfix">

    <style>
        /* Device detail compact card - compressed, dashboard-style */
        /* tighten top spacing to reduce empty gap under navbar */
        .device-workspace{
            display:flex;
            gap:18px;
            align-items:flex-start;
            margin-top:-66px !important;
            padding-top:0 !important;
            
        }
        .device-workspace-main{
            margin-top:0 !important;
            padding-top:0 !important;
        }
        .device-detail-sidebar{
            width:260px;
            min-width:260px;
            margin-top:0;
            border-radius:0;
        }
        .device-detail-sidebar .ef-sidebar-inner{padding:0;gap:12px}
        .device-detail-sidebar .ef-brand{padding:0;margin-bottom:14px}
        .device-detail-sidebar .ef-brand .logo{width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#7c3aed,#3b82f6);font-size:1rem}
        .device-detail-sidebar .ef-menu{margin:0;padding:0}
        .device-detail-sidebar .ef-menu li{margin-bottom:8px}
        .device-detail-sidebar .ef-menu a{padding:10px 12px;color:#e6eefb;border-radius:10px;background:transparent;border:1px solid transparent}
        .device-detail-sidebar .ef-menu a:hover{background:rgba(255,255,255,0.02);color:#fff}
        .device-detail-sidebar .ef-menu a.active{background:rgba(255,255,255,0.03);color:#fff;border-color:rgba(255,255,255,0.02)}
        .device-detail-sidebar .ef-menu i{width:18px;color:#c7d2fe}
        .device-detail-sidebar .ef-menu a span.ef-label{display:inline!important}
        .device-detail-sidebar .sidebar-divider{height:1px;background:rgba(255,255,255,0.04);margin:12px 0}
        .device-detail-sidebar .sidebar-footer{font-size:0.82rem;color:#94a3b8;line-height:1.4;margin-top:10px}

        .device-detail-card{padding:12px;border-radius:12px;border:1px solid rgba(124,58,237,0.12);box-shadow:0 12px 30px rgba(11,17,32,0.06);max-width:920px;margin:0 auto;background:linear-gradient(180deg,#ffffff 0%,#fbfdff 100%);position:relative;overflow:hidden}
        .device-detail-card::before{content:"";position:absolute;left:0;top:0;height:4px;width:100%;background:linear-gradient(90deg,#7c3aed 0%,#3b82f6 100%);opacity:0.95}
        .device-detail-card::after{content:"";position:absolute;right:-40px;bottom:-40px;width:120px;height:120px;border-radius:50%;background:radial-gradient(circle at 30% 30%,rgba(124,58,237,0.05),transparent 40%),radial-gradient(circle at 70% 70%,rgba(59,130,246,0.03),transparent 40%);pointer-events:none}
        .device-detail-card img{max-height:96px;width:96px;height:96px;object-fit:cover;border-radius:10px;box-shadow:0 8px 20px rgba(59,130,246,0.06)}
        .device-detail-card h2{font-size:1.1rem;margin:0;font-weight:800;color:#0f172a}
        .device-detail-card p{color:#475569;margin:4px 0}
        .device-detail-card .badge-status{font-size:0.85rem;padding:6px 10px;border:1px solid rgba(15,23,42,0.04);box-shadow:0 6px 18px rgba(15,23,42,0.03);background:#fff}
        .device-detail-card .ef-node-id{display:inline-block;max-width:100%;overflow-wrap:anywhere;word-break:break-all;hyphens:auto}
        .device-detail-card .row > [class*='col-']{padding-bottom:6px}

        .header-row{display:flex;gap:14px;align-items:center}
        .header-info{flex:1;min-width:0}
        .meta-row{display:flex;gap:10px;align-items:center;flex-wrap:wrap;color:#475569;font-size:0.92rem}
        .summary-stats{display:flex;gap:8px;margin-top:8px;flex-wrap:wrap}
        .summary-stats .stat{padding:6px 8px;border-radius:10px;background:linear-gradient(90deg,#fbfdff,#f8fbff);border:1px solid rgba(15,23,42,0.04);font-size:0.86rem}

        hr{margin:10px 0;border-color:rgba(15,23,42,0.04)}
        h4.mb-3{margin-bottom:8px}

        .info-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px}
        .info-card{
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px 18px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }

        .info-card:hover{
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            border-color: #c7d2fe;
        }
        @media (max-width:991px){.device-workspace{display:block}.device-detail-sidebar{position:relative!important;top:auto!important;height:auto!important;width:100%!important;margin-bottom:12px}.device-detail-card{margin:0}}
        @media (max-width:767px){.device-detail-card{padding:10px}.info-grid{grid-template-columns:1fr}.device-detail-card img{width:80px;height:80px}}
    </style>

    <div class="device-workspace">
        <?= view('includes/device_sidebar', [
            'node_id' => $item->node_id ?? '',
            'model_number' => $item->model_number ?? '',
            'user_id' => $item->user_id ?? ''
        ]); ?>

        <div class="device-workspace-main">
            <div class="card p-4 device-detail-card">

                <div class="header-row mb-2">
                    <?php
                    $deviceImage = strtolower($item->device_type ?? 'scanner');
                    ?>

                    <img src="<?= base_url('assets/device_images/' . $deviceImage . '.png') ?>" 
                        alt="Device">                    
                    <div class="header-info">
                        <h2><?= $item->device_type ?? '--' ?></h2>
                        <div class="meta-row">
                            <div><strong>Node ID:</strong> <span class="ef-node-id"><?= $item->node_id ?? '--' ?></span></div>
                            <div style="margin-left:auto"><span class="badge-status <?= ($item->internet == 'true') ? 'online' : 'offline' ?>"><?= ($item->internet == 'true') ? 'Online' : 'Offline' ?></span></div>
                        </div>
                        <div class="summary-stats">
                            <div class="stat">⚙ FW: <strong><?= $item->fw_version ?? '--' ?></strong></div>
                            <div class="stat">📶 RSSI: <strong><?= $item->RSSI ?? '--' ?></strong></div>
                            <div class="stat">🔌 Power: <strong><?= ($item->power == 'true') ? 'ON' : 'OFF' ?></strong></div>
                            <div class="stat">🔁 Baud: <strong><?= $item->Baud_Rate ?? '--' ?></strong></div>
                        </div>
                    </div>
                </div>

                <hr>

                <h4 class="mb-3">Device Information</h4>

                <div class="info-grid">
                    <div class="info-card"><strong>Product Name</strong><div><?= $item->model_name ?? '--' ?></div></div>
                    <div class="info-card"><strong>Device Type</strong><div><?= $item->device_type ?? '--' ?></div></div>
                    <div class="info-card"><strong>Model Number</strong><div><?= $item->model_number ?? '--' ?></div></div>
                    <div class="info-card"><strong>Brand</strong><div><?= $item->vendor_name ?? '--' ?></div></div>
                    <div class="info-card"><strong>Internet</strong><div><span class="badge-status <?= ($item->internet == 'true') ? 'online' : 'offline' ?>"><?= ($item->internet == 'true') ? 'Online' : 'Offline' ?></span></div></div>
                    <div class="info-card"><strong>RSSI</strong><div><?= $item->RSSI ?? '--' ?></div></div>
                    <div class="info-card"><strong>FW Version</strong><div><?= $item->fw_version ?? '--' ?></div></div>
                    <div class="info-card"><strong>BaudRate</strong><div><?= $item->Baud_Rate ?? '--' ?></div></div>
                    <div class="info-card"><strong>Power</strong><div><?= ($item->power == 'true') ? 'ON' : 'OFF' ?></div></div>
                    <div class="info-card"><strong>Latitude</strong><div><?= $item->latitude ?? '--' ?></div></div>
                    <div class="info-card"><strong>Longitude</strong><div><?= $item->longitude ?? '--' ?></div></div>
                    <div class="info-card"><strong>User ID</strong><div><?= $item->user_id ?? '--' ?></div></div>
                    <div class="info-card"><strong>Phone</strong><div><?= $item->phone ?? '--' ?></div></div>
                </div>

            </div>
        </div>
    </div>

</div>