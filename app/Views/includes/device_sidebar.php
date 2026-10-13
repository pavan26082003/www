<style>

    .device-workspace{
        display:flex !important;
        align-items:flex-start !important;
        gap:20px;
        padding:0 !important;
        margin:0 !important;
    }

    .device-workspace-main{
        flex:1;
        margin:0 !important;
        padding-top:0 !important;
        align-self:flex-start !important;
    }

    .device-detail-sidebar{
        width:230px !important;
        min-width:230px !important;
        max-width:230px !important;
        flex: 0 0 230px !important;
        background:#020b2d;
        border-radius:0 0 18px 18px;
        padding:18px;
        margin-top:0 !important;
        top:0;
        position:sticky;
        height: 100vh;
        overflow-y: auto;
        align-self:flex-start;
    }
        

    .ef-sidebar-inner{
        display:flex;
        flex-direction:column;
        height:100%;
    }

    .ef-brand{
        display:flex;
        align-items:center;
        gap:12px;
        margin-bottom:30px;
    }

    .logo{
        width:42px;
        height:42px;
        border-radius:12px;
        background:linear-gradient(135deg,#7c4dff,#4f8cff);
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:700;
        color:#fff;
    }

    .ef-menu{
        list-style:none;
        padding:0;
        margin:0;
    }

    .ef-menu li{
        margin-bottom:10px;
    }

    .ef-menu a{
        display:flex;
        align-items:center;
        gap:12px;
        padding:14px 16px;
        border-radius:14px;
        text-decoration:none;
        color:#dbe4ff;
        transition:0.2s ease;
        font-weight:500;
    }

    .page-wrapper{
        padding-top:0 !important;
        margin-top:0 !important;
    }

    .ef-menu a:hover{
        background:rgba(255,255,255,0.08);
        color:white;
    }

    .ef-menu i{
        width:18px;
        text-align:center;
    }

    .sidebar-divider{
        height:1px;
        background:rgba(255,255,255,0.08);
        margin:24px 0;
    }

    .sidebar-footer{
        color:#94a3b8;
        font-size:13px;
        line-height:1.6;
    }

    @media(max-width:991px){

        .device-workspace{
            display:block;
        }

        .device-detail-sidebar{
            width:100%;
            min-height:auto;
            margin-bottom:20px;
            position:relative;
        }

    }

</style>
<?php

$current_node_id = '';

if (isset($node_id) && !empty($node_id)) {
    $current_node_id = $node_id;
} elseif (isset($_GET['node_id']) && !empty($_GET['node_id'])) {
    $current_node_id = $_GET['node_id'];
}

$current_model_number = '';

if (isset($model_number) && !empty($model_number)) {
    $current_model_number = $model_number;
} elseif (isset($_GET['model_number']) && !empty($_GET['model_number'])) {
    $current_model_number = $_GET['model_number'];
}

$current_user_id = '';

if (isset($user_id) && !empty($user_id)) {
    $current_user_id = $user_id;
} elseif (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $current_user_id = $_GET['user_id'];
} elseif (isset($_GET['userid']) && !empty($_GET['userid'])) {
    $current_user_id = $_GET['userid'];
}

?>
<aside class="ef-sidebar device-detail-sidebar device-fixed-sidebar" aria-label="Device workspace sidebar">
    <div class="ef-sidebar-inner">
        <div class="ef-brand">
            <div class="logo">DI</div>
            <div style="line-height:1"><div style="font-weight:800">Device</div><div style="font-size:0.75rem;color:#9ca3af">Workspace</div></div>
        </div>
        <nav>
            <ul class="ef-menu">
                <li><a href="<?= site_url('items/modal_productinfo/' . $current_node_id . '?model_number=' . urlencode($current_model_number) . '&user_id=' . $current_user_id) ?>">Product Info</a></li>                
                <li><a href="<?= site_url('items/modal_apisinfo/' . $current_node_id . '?model_number=' . urlencode($current_model_number) . '&user_id=' . $current_user_id) ?>">APIs Info</a></li>                
                <li>
                <a href="<?= site_url(
                    'items/controlDisplayFunction/' .
                    urlencode($current_model_number) .
                    '?node_id=' . urlencode($current_node_id) .
                    '&userid=' . urlencode($current_user_id ?? 0)
                ) ?>">
                Control
                </a></li>                
                <li><a href="<?= site_url('items/ChatDisplayFunction/DEVICE_MODEL?node_id=' . $current_node_id) ?>">Chat</span></a></li>
                <li><a href="<?= site_url('items/fw_update?node_id=' . $current_node_id) ?>">Firmware Update</a></li>            
            </ul>
        </nav>
        <div class="sidebar-divider"></div>
        <div class="sidebar-footer">Use this workspace for quick access to device actions and info.</div>
    </div>
</aside>
