
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Auto Test Studio</title>
    <style>
         :root {
   --primary-color: #4f46e5;      /* professional blue */
    --secondary-color: #1d4ed8;    /* darker blue hover */
    --danger-color: #dc2626;       /* red */
    --success-color: #16a34a;      /* green */
    --warning-color: #f59e0b;      /* amber */

    --bg-color: #f4f6fb;           /* main background */

   --sidebar-bg: #0f172a;      /* deep navy sidebar */
    --sidebar-text: #e5e7eb;
     --card-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --card-bg: #ffffff;

   --border-color: #f1f5f9;
  }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: var(--bg-color);
            color: #334155;
            height: 100vh;
            overflow: hidden;
        }

        /* =========================
           MAIN LAYOUT
        ========================= */
        .studio-container {
            display: flex;
            height: 100vh;
        }

        /* =========================
           SIDEBAR
        ========================= */
       .sidebar {
    width: 320px;
     background: linear-gradient(180deg,#111827,#1f2937);

          color: var(--sidebar-text);          
    padding: 20px;
    display: flex;
    flex-direction: column;
    box-shadow: 2px 0 10px rgba(0,0,0,0.08);
    z-index: 10;
    overflow-y: auto;
}
        .sidebar-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h1 {
            font-size: 24px;
            color: white;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-header h1 i {
            color: #60a5fa;
        }

        .sidebar-header p {
            font-size: 14px;
            color: #94a3b8;
        }

        .sidebar-section {
            margin-bottom: 25px;
        }

        .sidebar-section h3 {
            font-size: 16px;
            margin-bottom: 15px;
           
            display: flex;
            align-items: center;
            gap: 8px;
            color: #dbeafe;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }

        .sidebar-section h3 i {
            font-size: 18px;
            color: #60a5fa;
        }

          .btn {
            padding: 10px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex: 1;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-secondary {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }


        /* Model Display */
        .model-display {
            background-color: rgba(255, 255, 255, 0.08);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-color);
        }

        .model-display h4 {
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .model-display p {
            font-size: 18px;
            font-weight: 600;
            color: white;
            padding: 8px 0;
        }

        /* Parameters List */
        .parameters-list {
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 5px;
            max-height: 250px;
            overflow-y: auto;
            overflowX = "auto";
            scroll-behavior: smooth;
        }
        /* Parameter Search */
        .parameter-search {
            position: relative;
            margin-bottom: 15px;
        }

        .parameter-search i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
            pointer-events: none;
            z-index: 5;
        }

        .parameter-search input {
            width: 100%;
            height: 42px !important;
            padding-left: 42px !important;
            padding-right: 14px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.08);
            color: white;
            font-size: 14px;
            line-height: 42px !important;
            outline: none;
            transition: all 0.2s ease;
        }


        .parameter-search input::placeholder {
            color: #94a3b8;
        }

        .parameter-search input:focus {
            border-color: #60a5fa;
            background: rgba(255,255,255,0.12);
            box-shadow: 0 0 0 3px rgba(96,165,250,0.15);
        }
        .parameter-item {
            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 16px 18px;
            border-radius: 14px;
            margin-bottom: 12px;

            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.04);

            transition: all 0.25s ease;
            cursor: pointer;

            backdrop-filter: blur(8px);
        }
        
        .parameter-item:hover {
            background: rgba(255,255,255,0.08);
            transform: scale(1.015);
            border-color: rgba(96,165,250,0.25);
            box-shadow: 0 6px 18px rgba(0,0,0,0.18);
        }

        .parameter-item.has-instances {
            background-color: rgba(22, 163, 74, 0.1);
            border-left: 3px solid var(--success-color);
        }

        .parameter-label {
            flex: 1;
            font-size: 15px;
            font-weight: 500;
            color: #f8fafc;
            line-height: 1.4;
            letter-spacing: 0.2px;
        }

        .parameter-count {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-right: 8px;
        }

        .instance-count {
            background-color: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-right: 8px;
        }

               /* Added Parameters List */
        .added-parameters-list {
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 5px;
            max-height: 200px;
            overflow-y: auto;
        }

        .added-parameter-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 5px;
            background-color: rgba(22, 163, 74, 0.1);
            border-left: 3px solid var(--success-color);
        }

        .added-parameter-label {
            flex: 1;
            font-size: 14px;
            color: #e2e8f0;
        }

        .instance-badge {
            background-color: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            font-size: 11px;
            padding: 1px 6px;
            border-radius: 8px;
            margin-left: 8px;
        }

        .remove-added-btn {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            background-color: rgba(220, 38, 38, 0.2);
            border: none;
            color: #fca5a5;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            font-size: 12px;
        }

        .remove-added-btn:hover {
            background-color: rgba(220, 38, 38, 0.4);
            color: white;
        }

        /* Quick Add Controls */
        .quick-add-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0;
            background: transparent;
        }

        .quick-add-btn {
            min-width: 74px;
            height: 40px;
            border-radius: 10px;
            background: rgba(37,99,235,0.12);
            border: 1px solid rgba(96,165,250,0.5);
            color: #60a5fa;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .quick-add-btn:hover {
            background: #2563eb;
            color: white;
            transform: scale(1.04);
            box-shadow: 0 0 16px rgba(37,99,235,0.45);
        }

        .quick-add-input {
            width: 55px;
            padding: 4px 8px;
            border-radius: 4px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            text-align: center;
            font-size: 12px;
        }

        /* Product Info */
        .product-info {
            background-color: rgba(255, 255, 255, 0.08);
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.5;
        }

        .product-info h4 {
            color: #cbd5e1;
            margin-bottom: 10px;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .product-info p {
            color: #94a3b8;
            margin-bottom: 10px;
        }

        /* Sidebar Buttons */
        .sidebar-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        
        /* =========================
           MAIN CONTENT AREA
        ========================= */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .content-header {
           
            background: linear-gradient(to right,#ffffff,#f8fafc);
            padding: 20px 30px;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-header h2 {
            font-size: 22px;
            color: #1e293b;
        }

        .selected-count {
            font-size: 16px;
            color: var(--success-color);
            font-weight: 600;
            background-color: #f0fdf4;
            padding: 8px 16px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

       


.content-window {
    flex: 1;
    padding: 30px;
    overflow-y: scroll;
    background-color: #f1f5f9;
}

/* ✅ RIGHT SIDE SCROLLBAR CONTROL */
.content-window::-webkit-scrollbar {
    width: 16px;   /* 🔥 increase here */
}

.content-window::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 10px;
}

.content-window::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.28);
    border-radius: 10px;
    transition: background 0.2s ease;
}

.content-window::-webkit-scrollbar-thumb:hover {
    background: rgba(96, 165, 250, 0.55);
}

/* Sidebar Scrollbar */
.sidebar::-webkit-scrollbar,
.parameters-list::-webkit-scrollbar,
.added-parameters-list::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-thumb,
.parameters-list::-webkit-scrollbar-thumb,
.added-parameters-list::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.16);
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-thumb:hover,
.parameters-list::-webkit-scrollbar-thumb:hover,
.added-parameters-list::-webkit-scrollbar-thumb:hover {
    background: rgba(96,165,250,0.55);
}    

        /* =========================
           PARAMETER DISPLAY CARDS
        ========================= */
        .parameter-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
                        border:1px solid #e5e7eb;
    box-shadow:0 8px 20px rgba(0,0,0,0.06);            

            animation: fadeIn 0.4s ease;
            position: relative;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .parameter-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap:wrap;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .parameter-card-header h3 {
            font-size: 24px;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .parameter-card-header h3 i {
            color: var(--primary-color);
        }

        .instance-indicator {
            background-color: var(--warning-color);
            color: white;
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 10px;
        }

        .card-actions {
            display: flex;
            gap: 10px;
            flex-wrap:wrap;
justify-content:flex-end;
        }

        .card-btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background-color: white;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .card-btn:hover {
            background-color: #f8fafc;
        }

        .card-btn.disable-btn {
    border-color: #d1d5db;
    color: #6b7280;
    background-color: #f9fafb;
}

.card-btn.disable-btn:hover {
    background-color: #f3f4f6;
}


.card-btn.enable-btn {
    border-color: #2563eb;
    color: #2563eb;
    background-color: #eff6ff;
}

.card-btn.enable-btn:hover {
    background-color: #dbeafe;
}


.card-btn.remove-btn {
    border-color: #e5e7eb;
    color: #374151;
    background-color: #f8fafc;
}

.card-btn.remove-btn:hover {
    background-color: #f1f5f9;
}


.card-btn.duplicate-btn {
    border-color: #cbd5f5;
       color: #374151;
    
    background-color: #eef2ff;
}

.card-btn.duplicate-btn:hover {
    background-color: #e0e7ff;
}       



        /* Sections Layout */
        .sections-flex-wrapper {
            display: flex;
            flex-wrap: nowrap;
            gap: 20px;
            align-items: stretch;
            margin-top: 15px;
            width: 100%;
        }

        .cmd-container, .resp-container, .error-container {
                       
             flex:1 1 0;
    min-width:0;
             background: #f3f4f6;
    border: 1px solid #e5e7eb;           

            border-radius: 10px;
            padding: 15px;
            display: flex;
            flex-direction: column;
        }

        .box-header {
           
          border-color: #e5e7eb;
    color: #374151;
    background-color: #f8fafc;

                         
    
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 16px;
            margin-bottom: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .cmd-section, .resp-section, .error-section {
            width: 100%;
            margin-bottom: 20px;
            background: #ffffff;
    border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .section-header h4 {
            font-size: 18px;
            color: #1e293b;
        }

        /* =========================
           INPUT STYLES
        ========================= */
        .input-row {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 15px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .input-group label {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }

        .byte-position {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 5px;
            background-color: #f8fafc;
            padding: 8px 12px;
            border-radius: 6px;
            border-left: 3px solid #94a3b8;
        }

        .min-max-display {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            padding: 10px 15px;
            background-color: #f1f5f9;
            border-radius: 6px;
            font-size: 14px;
        }

        .min-max-display span {
            font-weight: 600;
            color: #475569;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border-radius: 6px;
           
            border:1.5px solid #d1d5db;
    background:#ffffff;
            font-size: 14px;
            transition: 0.2s ease;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        /* =========================
           DISABLED STATE
        ========================= */
        .disabled {
            opacity: 0.6;
            background-color: #f8fafc;
        }

        .disabled input,
        .disabled textarea,
        .disabled select {
            background-color: #f1f5f9;
            cursor: not-allowed;
        }

        /* =========================
           EMPTY STATE
        ========================= */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #94a3b8;
            text-align: center;
            padding: 60px 40px;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 25px;
            opacity: 0.4;
        }

        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #64748b;
        }

        .empty-state p {
            max-width: 400px;
            line-height: 1.6;
            font-size: 16px;
        }

        /* =========================
           SUBMIT BUTTON
        ========================= */
        .submit-section {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e2e8f0;
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--success-color), #15803d);
            color: #ffffff;
            font-size: 18px;
            padding: 16px 40px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3);
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #15803d, #166534);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.4);
        }

        /* =========================
           RESPONSIVE
        ========================= */
                      
        @media (max-width:1024px){

.sections-flex-wrapper{
flex-wrap:wrap;
}

.cmd-container,
.resp-container,
.error-container{
flex:1 1 calc(50% - 10px);
min-width:calc(50% - 10px);
}

}
@media (max-width:768px){

.sections-flex-wrapper{
flex-direction:column;
}

.cmd-container,
.resp-container,
.error-container{
width:100%;
min-width:100%;
}

}


        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }

.api-modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.6);
    z-index:9999;
    overflow:auto;
}

.api-modal-content{
    background:white;
    width:80%;
    margin:40px auto;
    padding:20px;
    border-radius:6px;
}

.api-modal-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

      
.close-api-btn{
    background:red;
    color:white;
    border:none;
    padding:5px 10px;
    cursor:pointer;
}

//









    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>





        <div class="studio-container">
        <!-- LEFT SIDEBAR -->
        <div class="sidebar">
            <div class="sidebar-header">
              
                 <h1><i class="fas fa-flask"></i> Auto Test Studio</h1>
                <p>Automated tests for your devices</p>

            </div>
            
            <div class="sidebar-section">
                <h3><i class="fas fa-microchip"></i> Model Information</h3>
                <div class="model-display">
                    <h4>Current Model</h4>
                    <p id="current-model"><?php echo htmlspecialchars($modelno ?? 'Not Selected'); ?></p>
                </div>
            </div>
            
            <div class="sidebar-section">
                <h3><i class="fas fa-sliders-h"></i> Available Parameters</h3>
                <!-- Search Box -->
                <div class="parameter-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="parameter-search-input" placeholder="Search parameters...">
                </div>
                <div class="parameters-list" id="parameters-list">
                    <!-- Parameters will be populated by JavaScript -->
                </div>
            </div>
            
            <div class="sidebar-section">
                <h3><i class="fas fa-check-circle"></i> Added Instances</h3>
                <div class="added-parameters-list" id="added-parameters-list">
                    <!-- Added parameter instances will appear here -->
                </div>
            </div>
            
            <div class="sidebar-buttons">
                <button class="btn btn-secondary" id="refresh-btn">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <button class="btn btn-primary" id="clear-all-btn">
                    <i class="fas fa-trash"></i> Clear All
                </button>
            </div>
        </div>
        
        <!-- MAIN CONTENT AREA -->
        <div class="main-content">
            <div class="content-header">
                <h2>Test Configuration</h2>
                <div class="selected-count" id="selected-count">
                    <i class="fas fa-check-circle"></i>
                    <span>0 Instances Added</span>
                </div>
            </div>
            
            <div class="content-window" id="content-window">
                <!-- Empty state shown when no parameter is selected -->
                <div class="empty-state" id="empty-state">
                    <i class="fas fa-cogs"></i>
                    <h3>No Instances Added</h3>
                    <p>Click the <i class="fas fa-plus" style="color: #60a5fa;"></i> button next to parameters in the sidebar to add instances to the test configuration.</p>
                    <p>You can add the same parameter multiple times!</p>
                </div>
                
                <!-- Parameter configurations will be displayed here -->
                <div id="parameter-configurations"></div>
            </div>
        </div>
    </div>
   <div id="apiTestModal" class="api-modal">
    <div class="api-modal-content">

        <div class="api-modal-header">
            <h2>API Test Configuration</h2>
            <button onclick="closeApiModal()" class="close-api-btn">✖</button>
        </div>

        <div id="apiTestContent"></div>

    </div>
</div>
<div id="backendDataView"></div>
<div id="parameterDebugView"></div>


    <!-- Hidden form for submission -->
    <form id="file-form2" style="display: none;">
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="modelno" id="modelno" value="<?php echo $modelno; ?>" />
    </form>

    <script>
        // Simulated PHP data (in real implementation, this would come from PHP)
        const serverParameters = <?php echo json_encode($parameters, JSON_PRETTY_PRINT); ?> || {};
           
        // DOM Elements
        const parametersList = document.getElementById('parameters-list');
        const addedParametersList = document.getElementById('added-parameters-list');
        const selectedCount = document.getElementById('selected-count');
        const emptyState = document.getElementById('empty-state');
        const parameterConfigurations = document.getElementById('parameter-configurations');
        const contentWindow = document.getElementById('content-window');
        const refreshBtn = document.getElementById('refresh-btn');
        const clearAllBtn = document.getElementById('clear-all-btn');
        const currentModelDisplay = document.getElementById('current-model');

        // Store parameter instances
        let parameterInstances = {}; // { parameterName: [instance1, instance2, ...] }
        let allInstances = []; // Flat array of all instances

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
                        
            // Initialize parameterInstances object
            Object.keys(serverParameters).forEach(sectionName => {
                parameterInstances[sectionName] = [];
            });
            
            // Populate parameters list
            populateParametersList();
            updateAddedParametersList();
            
            // Setup buttons
            refreshBtn.addEventListener('click', refreshParameters);
            clearAllBtn.addEventListener('click', clearAllConfigurations);
            
            // Update selected count display
            updateSelectedCount();
            // Parameter search
            setupParameterSearch();
            function setupParameterSearch() {

                const searchInput = document.getElementById('parameter-search-input');

                if (!searchInput) return;

                searchInput.addEventListener('input', function () {

                    const searchValue = this.value.toLowerCase();

                    const parameterItems = document.querySelectorAll('.parameter-item');

                    parameterItems.forEach(item => {

                        const label = item.querySelector('.parameter-label');

                        if (!label) return;

                        const parameterName = label.textContent.toLowerCase();

                        if (parameterName.includes(searchValue)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }

                    });

                });

            }
        });

        // Populate the parameters list with ADD buttons
      

function populateParametersList() {
    parametersList.innerHTML = '';
    
    Object.keys(serverParameters).forEach(sectionName => {
        const sectionData = serverParameters[sectionName];

        const paramItem = document.createElement('div');
        paramItem.className = 'parameter-item';
        paramItem.style.cursor = 'pointer';

        const label = document.createElement('span');
        label.className = 'parameter-label';
        label.textContent = sectionName;
        paramItem.appendChild(label);

              

paramItem.onclick = function(event) {
    event.stopPropagation();

    const paramName = sectionName;

    // ✅ ONLY ADD — no manual box handling
    addParameterInstance(paramName);
};

        // --- All your Quick Add Logic must stay INSIDE this loop ---
     
        const quickAddControls = document.createElement('div');
        quickAddControls.className = 'quick-add-controls';
        quickAddControls.style.display = "flex";
        quickAddControls.style.alignItems = "center";
        quickAddControls.style.gap = "6px";

        quickAddControls.innerHTML = `
            <button class="quick-add-btn" data-parameter="${sectionName}">Add</button>
            <div class="instance-box-container" style="display:flex;gap:4px;padding:2px;"></div>
        `;

        const addBtn = quickAddControls.querySelector('.quick-add-btn');
        const boxContainer = quickAddControls.querySelector('.instance-box-container');

        
if (parameterInstances[sectionName]) {
       
    parameterInstances[sectionName].forEach(inst => {

        // 🔥 find GLOBAL position
        const globalIndex = allInstances.findIndex(i => i.id === inst.id);

        const box = document.createElement("div");

        // ✅ GLOBAL NUMBER
        box.textContent = globalIndex + 1;

        box.style.width = "26px";
        box.style.height = "28px";
        box.style.display = "flex";
        box.style.alignItems = "center";
        box.style.justifyContent = "center";
        box.style.border = "1px solid #cfd8ff";
        box.style.borderRadius = "3px";
        box.style.background = "rgba(37,99,235,0.2)";
        box.style.color = "white";
        box.style.fontSize = "14px";
        box.style.fontWeight = "bold";

        boxContainer.appendChild(box);
    });
}
             
        addBtn.addEventListener("click", function (event) {
    event.stopPropagation();

    const paramName = sectionName;

    // ✅ ONLY THIS (no box logic here)
    addParameterInstance(paramName);
});       
 
        paramItem.appendChild(quickAddControls);
        parametersList.appendChild(paramItem);

    }); // <-- This closes the .forEach loop
} // <-- This closes the function

/* APPEND */
 
        // Add a parameter instance
       
         


// Add a parameter instance (only one per parameter type)
/*
function addParameterInstance(sectionName) {
    const savedValues = storeAllInputValues();
    
    // ✅ CHECK: If this parameter already exists, prevent adding another
    if (parameterInstances[sectionName] && parameterInstances[sectionName].length > 0) {
        showNotification(`"${sectionName}" already added. Each parameter can only be added once.`, 'warning');
        
        // Scroll to the existing instance
        const existingInstanceId = parameterInstances[sectionName][0].id;
        const existingCard = document.getElementById(`parameter-card-${existingInstanceId}`);
        if (existingCard) {
            existingCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            existingCard.style.boxShadow = '0 0 0 3px rgba(245, 158, 11, 0.5)';
            setTimeout(() => {
                existingCard.style.boxShadow = '';
            }, 2000);
        }
        return;
    }
    
    // Initialize if needed
    if (!parameterInstances[sectionName]) {
        parameterInstances[sectionName] = [];
    }
    
    const instanceId = `${sectionName}-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    
    const instance = {
        id: instanceId,
        name: sectionName,
        instanceNumber: 1, // Always instance #1 since only one per parameter
        timestamp: Date.now()
    };
    
    parameterInstances[sectionName].push(instance);
    allInstances.push(instance);
    
    populateParametersList();
    updateAddedParametersList();
    updateSelectedCount();
    renderAllInstances();
    restoreAllInputValues(savedValues);
    
    // Auto-fill from saved config if available
    setTimeout(() => {
        if (!savedConfig) return;
        
        let testData = null;
        
        Object.keys(savedConfig).forEach(testKey => {
            if (['user_id','id','modelno'].includes(testKey)) return;
            const testBlock = savedConfig[testKey];
            if (testBlock && testBlock[instance.name]) {
                testData = testBlock[instance.name];
            }
        });
        
        if (testData) {
            const card = document.getElementById(`parameter-card-${instance.id}`);
            if (!card) return;
            
            const timeFields = card.querySelectorAll('input[name="cmd_time[]"]');
            
            testData.forEach((item, index) => {
                const valueField = card.querySelector(`input[name="cmd_value[${instance.id}][${index}]"]`);
                if (valueField) valueField.value = item.value || '';
                
                if (timeFields[index]) {
                    timeFields[index].value = item.time || '';
                }
                
                const respTimeFields = card.querySelectorAll('input[name="resp_time[]"]');
                const respValueField = card.querySelector(`input[name="resp_value[${instance.id}][${index}]"]`);
                if (respValueField) respValueField.value = item.value || '';
                if (respTimeFields[index]) {
                    respTimeFields[index].value = item.time || '';
                }
                
                const errTimeFields = card.querySelectorAll('input[name="error_time[]"]');
                const errValueField = card.querySelector(`input[name="error_value[${instance.id}][${index}]"]`);
                if (errValueField) errValueField.value = item.value || '';
                if (errTimeFields[index]) {
                    errTimeFields[index].value = item.time || '';
                }
            });
        }
    }, 500);
    
    showNotification(`Added "${sectionName}"`, 'success');
}
*/

function addParameterInstance(sectionName) {

    const savedValues = storeAllInputValues();

    if (!parameterInstances[sectionName]) {
        parameterInstances[sectionName] = [];
    }

    const instanceId = `${sectionName}-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

    const instance = {
        id: instanceId,
        name: sectionName,
        instanceNumber: parameterInstances[sectionName].length + 1,
        timestamp: Date.now()
    };

         parameterInstances[sectionName].push(instance);
    allInstances.push(instance);

if (!window.isInitialLoad) {
  
    populateParametersList();
    updateAddedParametersList();
    updateSelectedCount();
    renderAllInstances();
    restoreAllInputValues(savedValues);
}

     
    // 🔥 ADD THIS BLOCK (AUTO FILL FROM DB)
   


setTimeout(() => {

    if (!savedConfig) return;

    let testData = null;

    // 🔥 find correct data
    Object.keys(savedConfig).forEach(testKey => {

        if (['user_id','id','modelno'].includes(testKey)) return;

        const testBlock = savedConfig[testKey];

        if (testBlock && testBlock[instance.name]) {
            testData = testBlock[instance.name];
        }

    });

    if (!testData) {
        console.log("❌ No DB data for:", instance.name);
        return;
    }

    console.log("✅ AUTO FILL:", testData);

   
const card = document.getElementById(`parameter-card-${instance.id}`);
if (!card) return;

// ✅ GET ALL TIME INPUTS
const timeFields = card.querySelectorAll('input[name="cmd_time[]"]');

testData.forEach((item, index) => {

    // VALUE FIELD
    const valueField = card.querySelector(
        `input[name="cmd_value[${instance.id}][${index}]"]`
    );
    if (valueField) valueField.value = item.value || '';

    // TIME FIELD
    if (timeFields[index]) {
        timeFields[index].value = item.time || '';
    }



const respTimeFields = card.querySelectorAll('input[name="resp_time[]"]');

testData.forEach((item, index) => {

    const valueField = card.querySelector(
        `input[name="resp_value[${instance.id}][${index}]"]`
    );
    if (valueField) valueField.value = item.value || '';

    if (respTimeFields[index]) {
        respTimeFields[index].value = item.time || '';
    }

});
const errTimeFields = card.querySelectorAll('input[name="error_time[]"]');

testData.forEach((item, index) => {

    const valueField = card.querySelector(
        `input[name="error_value[${instance.id}][${index}]"]`
    );
    if (valueField) valueField.value = item.value || '';

    if (errTimeFields[index]) {
        errTimeFields[index].value = item.time || '';
    }

});
    });

}, 500);


showNotification(`Added "${sectionName}" (Instance #${instance.instanceNumber})`, 'success');
}

function storeAllInputValues() {
    const values = {};

    document.querySelectorAll("input").forEach(input => {
        if (!input.name) return;

        if (!values[input.name]) {
            values[input.name] = [];
        }

        values[input.name].push(input.value);
    });

    return values;
}

function restoreAllInputValues(values) {

    Object.keys(values).forEach(name => {

        const inputs = document.querySelectorAll(`[name="${name}"]`);

        inputs.forEach((input, index) => {
            if (values[name][index] !== undefined) {
                input.value = values[name][index];
            }
        });

    });

}      
     

        // Remove a parameter instance
        function removeParameterInstance(instanceId) {
               const savedValues = storeAllInputValues();
            // Find which parameter this instance belongs to
            let found = false;
            Object.keys(parameterInstances).forEach(paramName => {
                const index = parameterInstances[paramName].findIndex(inst => inst.id === instanceId);
                if (index !== -1) {
                    const instance = parameterInstances[paramName][index];
                    parameterInstances[paramName].splice(index, 1);
                    
                    // Remove from allInstances array
                    const allIndex = allInstances.findIndex(inst => inst.id === instanceId);
                    if (allIndex !== -1) {
                        allInstances.splice(allIndex, 1);
                    }
                    
                    // Re-number remaining instances
                    parameterInstances[paramName].forEach((inst, idx) => {
                        inst.instanceNumber = idx + 1;
                    });
                    
                    showNotification(`Removed "${paramName}" (Instance #${instance.instanceNumber})`, 'info');
                    found = true;
                }
            });
            
            if (found) {
                // Update UI
                populateParametersList();
                updateAddedParametersList();
                updateSelectedCount();
                renderAllInstances();
                 restoreAllInputValues(savedValues);
            }
        }

        // Duplicate a parameter instance
        function duplicateParameterInstance(instanceId) {
            const savedValues = storeAllInputValues();
            // Find the instance to duplicate
            let sourceInstance = null;
            let paramName = null;
            
            Object.keys(parameterInstances).forEach(param => {
                const instance = parameterInstances[param].find(inst => inst.id === instanceId);
                if (instance) {
                    sourceInstance = instance;
                    paramName = param;
                }
            });
            
            if (sourceInstance && paramName) {
                // Create new instance with same name but new ID
                const newInstanceId = `${paramName}-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
                
                const newInstance = {
                    id: newInstanceId,
                    name: paramName,
                    instanceNumber: parameterInstances[paramName].length + 1,
                    timestamp: Date.now(),
                    isDuplicate: true
                };
                
                // Add to arrays
                parameterInstances[paramName].push(newInstance);
                allInstances.push(newInstance);
                
                // Update UI
                populateParametersList();
                updateAddedParametersList();
                updateSelectedCount();
                renderAllInstances();
                 restoreAllInputValues(savedValues);
                
                showNotification(`Duplicated "${paramName}"`, 'success');
            }
        }

        // Update the added parameters list
        function updateAddedParametersList() {
            addedParametersList.innerHTML = '';
            
            if (allInstances.length === 0) {
                const emptyMessage = document.createElement('div');
                emptyMessage.className = 'parameter-item';
                emptyMessage.style.justifyContent = 'center';
                emptyMessage.style.cursor = 'default';
                emptyMessage.style.opacity = '0.7';
                emptyMessage.textContent = "No instances added yet";
                addedParametersList.appendChild(emptyMessage);
                return;
            }
            
            // Group instances by parameter name
            const groupedInstances = {};
            allInstances.forEach(instance => {
                if (!groupedInstances[instance.name]) {
                    groupedInstances[instance.name] = [];
                }
                groupedInstances[instance.name].push(instance);
            });
            
            // Display grouped instances
            Object.keys(groupedInstances).forEach(paramName => {
                const instances = groupedInstances[paramName];
                
                const groupHeader = document.createElement('div');
                groupHeader.className = 'parameter-item';
                groupHeader.style.background = 'rgba(255,255,255,0.05)';
                groupHeader.style.fontWeight = 'bold';
                groupHeader.style.marginBottom = '5px';
                groupHeader.innerHTML = `<span class="parameter-label">${paramName}</span>`;
                addedParametersList.appendChild(groupHeader);
                
                instances.forEach(instance => {
                    const addedItem = document.createElement('div');
                    addedItem.className = 'added-parameter-item';
                    addedItem.dataset.instanceId = instance.id;
                    
                    addedItem.innerHTML = `
                        <span class="added-parameter-label">
                            Instance #${instance.instanceNumber}
                            ${instance.isDuplicate ? '<span class="instance-badge">Duplicate</span>' : ''}
                        </span>
                        <button class="remove-added-btn" data-instance-id="${instance.id}">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    
                    // Add remove event
                    const removeBtn = addedItem.querySelector('.remove-added-btn');
                    removeBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        removeParameterInstance(instance.id);
                    });
                    
                    // Also make the whole item clickable to scroll to it
                    addedItem.addEventListener('click', function(e) {
                        if (e.target !== removeBtn && !e.target.classList.contains('fa-times')) {
                            const card = document.getElementById(`parameter-card-${instance.id}`);
                            if (card) {
                                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                // Add highlight effect
                                card.style.boxShadow = '0 0 0 3px rgba(37, 99, 235, 0.3)';
                                setTimeout(() => {
                                    card.style.boxShadow = '';
                                }, 2000);
                            }
                        }
                    });
                    
                    addedParametersList.appendChild(addedItem);
                });
            });
        }

        // Update the selected count display
        function updateSelectedCount() {
            const count = allInstances.length;
             selectedCount.innerHTML = `<i class="fas fa-check-circle"></i><span>${count} Instance${count !== 1 ? 's' : ''} Added</span>`;

                   }

        // Render all instances in the content window
        

function renderAllInstances() {

    if (allInstances.length === 0) {
        emptyState.style.display = 'flex';
        parameterConfigurations.innerHTML = '';
        return;
    }

    emptyState.style.display = 'none';
    parameterConfigurations.innerHTML = '';

    // ❌ REMOVE SORTING
    // const sortedInstances = [...allInstances].sort(...)

    // ✅ USE ORIGINAL CLICK ORDER
    allInstances.forEach((instance, index) => {

        const sectionData = serverParameters[instance.name];
        const configurationHTML = generateParameterConfiguration(instance, sectionData, index);

        parameterConfigurations.insertAdjacentHTML("beforeend", configurationHTML);
    });

    parameterConfigurations.innerHTML += `
        <div class="submit-section">
            <button type="button" class="submit-btn" onclick="submitTestConfiguration()">
                <i class="fas fa-paper-plane"></i> Submit Test Configuration
            </button>
        </div>
    `;

    setTimeout(() => {
        setupInputValidation();
        setupSectionActions();
    



       },100);
}
   
        // Generate the configuration form for a parameter instance
        function generateParameterConfiguration(instance, sectionData, index) {
            const cmdDefaults = sectionData.cmd || [];
            const respDefaults = sectionData.resp || [];
            const errorDefaults = sectionData.error || [];
            
            return `
                <div class="parameter-card" id="parameter-card-${instance.id}">
                    <div class="parameter-card-header">
                                            
                        
                       <h3>
    <i class="fas fa-cog"></i> ${instance.name}
    <span class="instance-indicator">Instance #${instance.instanceNumber}</span>
    ${instance.isDuplicate ? '<span style="margin-left: 8px; color: #f59e0b;"><i class="fas fa-clone"></i> Duplicate</span>' : ''}
</h3>

<h4 style="margin-left:28px; margin-top:4px;">
    API Test ${index + 1}
</h4>
                        <div class="card-actions">
                            <button type="button" class="card-btn duplicate-btn" onclick="duplicateInstance('${instance.id}')">
                                <i class="fas fa-clone"></i> Duplicate
                            </button>
                            <button type="button" class="card-btn disable-btn" onclick="disableInstance('${instance.id}')">
                                <i class="fas fa-toggle-off"></i> Disable
                            </button>
                            <button type="button" class="card-btn remove-btn" onclick="removeInstance('${instance.id}')">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                    </div>
                    
                    <input type="hidden" name="parameters[${instance.id}]" value="${instance.name}">
                    <input type="hidden" name="instance_number[${instance.id}]" value="${instance.instanceNumber}">

                    <div class="sections-flex-wrapper">
                        <!-- Command Section -->
                        <div class="cmd-container" id="cmd-container-${instance.id}">
                            <div class="box-header">
                                <i class="fas fa-terminal"></i> Command
                            </div>
                            ${generateCommandSections(instance, cmdDefaults)}
                        </div>
                        
                        <!-- Response Section -->
                        <div class="resp-container" id="resp-container-${instance.id}">
                            <div class="box-header">
                                <i class="fas fa-reply"></i> Response
                            </div>
                            ${generateResponseSections(instance, respDefaults)}
                        </div>
                        
                        <!-- Error Section -->
                        <!-- Error Section -->
<div class="error-container" id="error-container-${instance.id}">
    <div class="box-header">
        <i class="fas fa-exclamation-triangle"></i> Error
    </div>
    ${generateErrorSections(instance, errorDefaults || [])}
</div>
                     </div>    
                    </div>
                </div>
            `;
        }

        // Generate command sections HTML
        
      function generateCommandSections(instance, cmdDefaults) {
            let html = '';
            let timeInputDisplayed = false;
            
            cmdDefaults.forEach((cmd, index) => {
              
                               
                const min = cmd.cmd_min || '';
                const max = cmd.cmd_max || '';
               const hasMinMax = (min !== "" && max !== "");
                
                html += `
                    <div class="cmd-section" id="cmd-section-${instance.id}-${index}">
                        <div class="section-header">
                            <h4>${cmd.cmd_default}</h4>
                            <button type="button" class="card-btn" onclick="toggleSectionDisable('cmd-section-${instance.id}-${index}')">
                                <i class="fas fa-toggle-off"></i> Disable
                            </button>
                        </div>
                        
                        <div class="byte-position">
                            Byte Position: ${cmd.cmd_startbyteposition || ''}
                        </div>
                        
                        ${min || max ? `
                            <div class="min-max-display">
                                ${min ? `<div>Min: <span>${min}</span></div>` : ''}
                                ${max ? `<div>Max: <span>${max}</span></div>` : ''}
                            </div>
                        ` : ''}
                        
                             <div class="input-row">
                                                

                       <div class="input-group">
                     <label>Value</label>

                   ${
                hasMinMax
              ? `
        <input type="text" 
               id="cmd-value-${instance.id}-${index}"
               name="cmd_value[${instance.id}][${index}]" 
               placeholder="Enter value" 
               class="cmd-value-input"
               data-min="${min}" 
               data-max="${max}">
        `
        : `
        <div style="color:red;font-size:12px;">
            ⚠️ Set Min & Max values in the form
        </div>
        `
    }

</div>
                           
                           
                          
${!timeInputDisplayed ? `
    <div class="input-group">
        <label>Time (Sec)</label>
        <input type="text" 
               class="cmd-time-input"
               name="cmd_time[]" 
               placeholder="Enter time in seconds"
               value="">
    </div>
` : ''}







                        </div>
                        
                        <input type="hidden" 
                               name="cmd_default[${instance.id}][${index}]" 
                               value="${cmd.cmd_default}">
                    </div>
                `;
                                   
                            });
            
            return html || '<div class="cmd-section"><p>No command configuration available.</p></div>';
        }
           

           
//
        // Generate response sections HTML
           function generateResponseSections(instance, respDefaults) {
    let html = '';
    let timeInputDisplayed = false;

    respDefaults.forEach((resp, index) => {

        const min = resp.resp_min || '';
        const max = resp.resp_max || '';
        const hasMinMax = (min !== "" && max !== "");


        html += `
            <div class="resp-section" id="resp-section-${instance.id}-${index}">
                <div class="section-header">
                    <h4>${resp.resp_default}</h4>
                    <button type="button" class="card-btn" onclick="toggleSectionDisable('resp-section-${instance.id}-${index}')">
                        <i class="fas fa-toggle-off"></i> Disable
                    </button>
                </div>

                <div class="byte-position">
                    Byte Position: ${resp.resp_startbyteposition || ''}
                </div>

                ${(min !== "" || max !== "") ? `
                    <div class="min-max-display">
                        ${min !== "" ? `<div>Min: <span>${min}</span></div>` : ''}
                        ${max !== "" ? `<div>Max: <span>${max}</span></div>` : ''}
                    </div>
                ` : ''}

                
                   <div class="input-row">
                                                

  <div class="input-group">
    <label>Value</label>

    ${
        hasMinMax
        ? `
        <input type="text" 
               id="resp-value-${instance.id}-${index}"
               name="resp_value[${instance.id}][${index}]" 
               placeholder="Enter value" 
               class="resp-value-input"
               data-min="${min}" 
               data-max="${max}">
        `
        : `
        <div style="color:red;font-size:12px;">
            ⚠️ Set Min & Max values in the form
        </div>
        `
    }

</div>



        

  
 
${!timeInputDisplayed ? `
    <div class="input-group">
        <label>Time (Sec)</label>
        <input type="text" 
               class="resp-time-input"
               name="resp_time[]" 
               placeholder="Enter time in seconds"
               value="">
    </div>
` : ''}






                </div>

                <input type="hidden"
                       name="resp_default[${instance.id}][${index}]"
                       value="${resp.resp_default}">
            </div>
        `;
                        });

    return html || '<div class="resp-section"><p>No response configuration available.</p></div>';
}
        

//
function generateErrorSections(instance, errorDefaults) {

    let html = '';

    errorDefaults.forEach((error, index) => {

        const byte = error.error_startbyteposition || '';
        const min = error.error_min || '';
        const max = error.error_max || '';

        html += `
        <div class="error-section" id="error-section-${instance.id}-${index}">
            
            <div class="section-header">
                <h4>${error.error_default}</h4>
                <button type="button" class="card-btn"
                        onclick="toggleSectionDisable('error-section-${instance.id}-${index}')">
                    <i class="fas fa-toggle-off"></i> Disable
                </button>
            </div>

            <div class="byte-position">
                Byte Position: ${byte}
            </div>

            ${min || max ? `
            <div class="min-max-display">
                ${min !== '' ? `<div>Min: <span>${min}</span></div>` : ''}
                ${max !== '' ? `<div>Max: <span>${max}</span></div>` : ''}
            </div>
            ` : ''}

            <div class="input-row">

                <div class="input-group">
                    <label for="error-value-${instance.id}-${index}">Value</label>
                    <input type="text"
                           id="error-value-${instance.id}-${index}"
                           name="error_value[${instance.id}][${index}]"
                           placeholder="Enter value"
                           class="error-value-input"
                           data-min="${min}"
                           data-max="${max}">
                </div>

                <div class="input-group">
                    <label for="error-time-${instance.id}-${index}">Time (Sec)</label>
                    <input type="text"
                           id="error-time-${instance.id}-${index}"
                           name="error_time[${instance.id}][${index}]"
                           placeholder="Enter time in seconds">
                </div>

            </div>

        </div>
        `;
    });

    return html || `<div class="error-section"><p>No error configuration available.</p></div>`;
}
        // Setup input validation
        document.addEventListener("input", function(e){

    if(
        e.target.classList.contains("cmd-value-input") ||
        e.target.classList.contains("resp-value-input") ||
        e.target.classList.contains("error-value-input")
    ){

        const input = e.target;
        const value = parseFloat(input.value);

        const min = parseFloat(input.dataset.min);
        const max = parseFloat(input.dataset.max);

        const row = input.closest(".input-row"); // highlight only value row

        if(!isNaN(min) && !isNaN(max)){

            if(value < min || value > max){

                row.style.background = "#ffe6e6";
                row.style.border = "2px solid red";
                row.style.borderRadius = "5px";

                showValidationMessage("Enter value between Min–Max");

            }else{

                row.style.background = "";
                row.style.border = "";

                hideValidationMessage();
            }

        }

    }

});

function showValidationMessage(msg){

    let box = document.getElementById("validationMessage");

    if(!box){
        box = document.createElement("div");
        box.id = "validationMessage";

        box.style.position = "fixed";
        box.style.top = "50%";
        box.style.left = "50%";
        box.style.transform = "translate(-50%, -50%)";

        box.style.background = "#ff4d4d";
        box.style.color = "#fff";
        box.style.padding = "15px 25px";
        box.style.borderRadius = "6px";
        box.style.fontSize = "15px";
        box.style.zIndex = "9999";
        box.style.boxShadow = "0 0 10px rgba(0,0,0,0.3)";

        document.body.appendChild(box);
    }

    box.innerText = msg;
}

function hideValidationMessage(){
    const box = document.getElementById("validationMessage");
    if(box) box.remove();
}

        // Setup section action buttons
        function setupSectionActions() {
            // This will be called after rendering to attach event handlers
        }

        // Toggle disable/enable for a section
        function toggleSectionDisable(sectionId) {
            const section = document.getElementById(sectionId);
            if (!section) return;
            
            const button = section.querySelector('.card-btn');
            const inputs = section.querySelectorAll('input');
            
            if (section.classList.contains('disabled')) {
                section.classList.remove('disabled');
                if (button) {
                    button.innerHTML = '<i class="fas fa-toggle-off"></i> Disable';
                    button.classList.remove('enable-btn');
                }
                
                inputs.forEach(input => {
                    input.disabled = false;
                });
            } else {
                section.classList.add('disabled');
                if (button) {
                    button.innerHTML = '<i class="fas fa-toggle-on"></i> Enable';
                    button.classList.add('enable-btn');
                }
                
                inputs.forEach(input => {
                    input.value = '';
                    input.disabled = true;
                });
            }
        }

        // Disable an entire instance
        function disableInstance(instanceId) {
            const card = document.getElementById(`parameter-card-${instanceId}`);
            if (!card) return;
            
            const disableBtn = card.querySelector('.disable-btn');
            const cmdContainer = card.querySelector('.cmd-container');
            const respContainer = card.querySelector('.resp-container');
            const errorContainer = card.querySelector('.error-container');
            
            if (card.classList.contains('disabled')) {
                // Enable the instance
                card.classList.remove('disabled');
                disableBtn.innerHTML = '<i class="fas fa-toggle-off"></i> Disable';
                disableBtn.classList.remove('enable-btn');
                
                // Enable all inputs
                [cmdContainer, respContainer, errorContainer].forEach(container => {
                    if (container) {
                        container.classList.remove('disabled');
                        const inputs = container.querySelectorAll('input');
                        inputs.forEach(input => input.disabled = false);
                    }
                });
                
                // Find instance name
                const instance = allInstances.find(inst => inst.id === instanceId);
                if (instance) {
                    showNotification(`"${instance.name}" (Instance #${instance.instanceNumber}) enabled`, 'success');
                }
            } else {
                // Disable the instance
                card.classList.add('disabled');
                disableBtn.innerHTML = '<i class="fas fa-toggle-on"></i> Enable';
                disableBtn.classList.add('enable-btn');
                
                // Disable all inputs
                [cmdContainer, respContainer, errorContainer].forEach(container => {
                    if (container) {
                        container.classList.add('disabled');
                        const inputs = container.querySelectorAll('input');
                        inputs.forEach(input => {
                            input.value = '';
                            input.disabled = true;
                        });
                    }
                });
                
                // Find instance name
                const instance = allInstances.find(inst => inst.id === instanceId);
                if (instance) {
                    showNotification(`"${instance.name}" (Instance #${instance.instanceNumber}) disabled`, 'info');
                }
            }
        }

        // Remove an instance
        function removeInstance(instanceId) {
            removeParameterInstance(instanceId);
        }

        // Duplicate an instance
        function duplicateInstance(instanceId) {
            duplicateParameterInstance(instanceId);
        }

        // Clear all configurations
        function clearAllConfigurations() {
            if (allInstances.length === 0) {
                showNotification('No instances to clear', 'info');
                return;
            }
            
            if (confirm('Are you sure you want to clear all instances?')) {
                // Clear all instances
                Object.keys(parameterInstances).forEach(param => {
                    parameterInstances[param] = [];
                });
                allInstances = [];
                
                // Update UI
                populateParametersList();
                updateAddedParametersList();
                updateSelectedCount();
                renderAllInstances();
                
                showNotification('All instances cleared', 'success');
            }
        }

        // Refresh parameters (simulated)
        function refreshParameters() {
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
            refreshBtn.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                console.log("Parameters refreshed");
                populateParametersList();
                
                refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                refreshBtn.disabled = false;
                
                // Show notification
                showNotification('Parameters refreshed successfully', 'success');
            }, 1000);
        }

        // Submit test configuration
        function submitTestConfiguration() {
            const form = document.getElementById('file-form2');
            const formData = new FormData(form);
            
            // Collect all visible form data
            const visibleInputs = document.querySelectorAll('#parameter-configurations input:not([type="hidden"])');
            visibleInputs.forEach(input => {
                if (input.name && !input.disabled && input.value.trim() !== '') {
                    // Add to the hidden form
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = input.name;
                    hiddenInput.value = input.value;
                    form.appendChild(hiddenInput);
                }
            });
            
                      
            // Show success message
            showNotification('Test configuration submitted successfully!', 'success');
            
            // In a real implementation, you would submit the form here
            // form.submit();
        }

        // Show notification
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
                <span>${message}</span>
            `;
            
            // Add styles for notification
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background-color: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#3b82f6'};
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                display: flex;
                align-items: center;
                gap: 10px;
                z-index: 1000;
                animation: slideIn 0.3s ease;
            `;
            
            // Add keyframe animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
            
            document.body.appendChild(notification);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Expose functions to global scope for inline onclick handlers
        window.toggleSectionDisable = toggleSectionDisable;
        window.disableInstance = disableInstance;
        window.removeInstance = removeInstance;
        window.duplicateInstance = duplicateInstance;
        window.submitTestConfiguration = submitTestConfiguration;

//



function submitTestConfiguration() {

    const modal = document.getElementById("apiTestModal");
    const content = document.getElementById("apiTestContent");

    if (!modal || !content) {
        console.error("Modal missing");
        return;
    }

    let apiArray = [];
    let hasError = false;
    let hasEmptyField = false;
let hasCompletelyEmptyCard = false;

    const setSafeBorder = (el, isError) => {
        if (el && el.style) {
            el.style.border = isError ? "2px solid red" : "";
        }
    };

    
   const cards = document.querySelectorAll(".parameter-card");

    cards.forEach((card, index) => {

        const instanceId = card.id.replace("parameter-card-", "");
        const name = card.querySelector("h3")?.innerText.split("\n")[0].trim() || "";
        const instance = card.querySelector("h3")?.innerText.split("#")[1]?.trim() || "";

        let cmdArray = [];
        let respArray = [];
        let errArray = [];

        /* COMMAND */
       
   card.querySelectorAll(".cmd-section").forEach(cmd => {

    const valueInput = cmd.querySelector(".cmd-value-input");
        const timeInput = cmd.querySelector(".cmd-time-input");
    
    // ✅ SKIP if no input exists (means "No command configuration available")
    if (!valueInput && !timeInput) return;

    const byte = cmd.querySelector(".byte-position")?.innerText || "";
    const value = valueInput?.value || "";
    const time = timeInput?.value || "";

    if (valueInput && value === "") {
        setSafeBorder(valueInput, true);
        hasEmptyField = true;
    }

    if (timeInput && time === "") {
        setSafeBorder(timeInput, true);
        hasEmptyField = true;
    }

    const min = cmd.querySelector(".min-max-display div:first-child span")?.innerText || "";
            const max = cmd.querySelector(".min-max-display div:last-child span")?.innerText || "";
            const valNum = parseFloat(value);
            const minNum = parseFloat(min);
            const maxNum = parseFloat(max);

            if (value !== "" && !isNaN(minNum) && !isNaN(maxNum)) {
                if (valNum < minNum || valNum > maxNum) hasError = true;
            }

        
const paramName = cmd.querySelector("h4")?.innerText.trim() || name;

const item = {
    mainparameter: name,
    parametername: paramName,
    instance: instance,
    bytePosition: byte,
    time: time,
 type: "command" 
};

// ✅ add min/max + value ONLY if available
if (min !== "" && max !== "") {
    item.min = min;
    item.max = max;
    item.value = value;
}

cmdArray.push(item);
});






        /* RESPONSE */
                
card.querySelectorAll(".resp-section").forEach(resp => {

    const valueInput = resp.querySelector(".resp-value-input");
       const timeInput = resp.querySelector(".resp-time-input");
     const time = timeInput?.value || "";

    // ✅ SKIP empty sections
    if (!valueInput && !timeInput) return;
   
    const byte = resp.querySelector(".byte-position")?.innerText || "";
    const value = valueInput?.value || "";
      if (valueInput && value === "") {
        setSafeBorder(valueInput, true);
        hasEmptyField = true;
    }

    if (timeInput && time === "") {
        setSafeBorder(timeInput, true);
        hasEmptyField = true;
    }
    const min = resp.querySelector(".min-max-display div:first-child span")?.innerText || "";
            const max = resp.querySelector(".min-max-display div:last-child span")?.innerText || "";

const paramName = resp.querySelector("h4")?.innerText.trim() || name;
    const item = {
    mainparameter: name,
    parametername: paramName,
    instance: instance,
    bytePosition: byte,
    time: time,
        type: "response"   // ✅ ADD THIS
};


// ✅ add min/max + value ONLY if available
if (min !== "" && max !== "") {
    item.min = min;
    item.max = max;
    item.value = value;
}

respArray.push(item);
});

       



        /* ERROR */
        card.querySelectorAll(".error-section").forEach(err => {

    const valueInput = err.querySelector(".error-value-input");
    const timeInput = err.querySelector("input[name^='error_time']") ||
                      document.getElementById(`error-time-${instanceId}`);

    // ✅ SKIP empty sections
    if (!valueInput && !timeInput) return;

    const byte = err.querySelector(".byte-position")?.innerText || "";
    const value = valueInput?.value || "";
    const time = timeInput?.value || "";

    if (valueInput && value === "") {
        setSafeBorder(valueInput, true);
        hasEmptyField = true;
    }

    if (timeInput && time === "") {
        setSafeBorder(timeInput, true);
        hasEmptyField = true;
    }
const min = err.querySelector(".min-max-display div:first-child span")?.innerText || "";
            const max = err.querySelector(".min-max-display div:last-child span")?.innerText || "";
            const valNum = parseFloat(value);
            const minNum = parseFloat(min);
            const maxNum = parseFloat(max);

            if (value !== "" && !isNaN(minNum) && !isNaN(maxNum)) {
                if (valNum < minNum || valNum > maxNum) hasError = true;
            }

const paramName = err.querySelector("h4")?.innerText.trim() || name;

   const item = {
    mainparameter: name,
    parametername: paramName,
    instance: instance,
    bytePosition: byte,
    time: time,
};

// ✅ add min/max + value ONLY if available
if (min !== "" && max !== "") {
    item.min = min;
    item.max = max;
    item.value = value;
}

errArray.push(item);
});

let hasMissingMinMax = false;

// 🔍 check all arrays
[...cmdArray, ...respArray, ...errArray].forEach(item => {
    if (item.min === undefined || item.max === undefined) {
        hasMissingMinMax = true;
    }
});

if (hasMissingMinMax) {

    alert(`Min & Max values missing for "${name}". Please remove this instance.`);

    // 🔥 highlight
    card.style.border = "2px solid red";
    card.style.background = "rgba(255, 0, 0, 0.05)";

    hasCompletelyEmptyCard = true; // reuse same blocker

    return; // ✅ stop this card
}

if (cmdArray.length === 0 && respArray.length === 0 && errArray.length === 0) {

    alert(`No data entered for "${name}". Please remove this instance.`);

    // highlight
    card.style.border = "2px solid red";
    card.style.background = "rgba(255, 0, 0, 0.05)";

    hasCompletelyEmptyCard = true;

    return; // skip this card ONLY
}



        apiArray.push({
            apiTest: `API Test ${index + 1}`,
            command: cmdArray,
            response: respArray,
            error: errArray
        });
    });

    if (hasEmptyField) {
        alert("Fill all fields");
        return;
    }

    if (hasError) {
        alert("Invalid values");
        return;
    }

    // ✅ USE FORMDATA (LIKE saveConfiguration)
    
    // 🔥 LOADING BUTTON (optional)
    
     




const finalOutput = {
    user_id: <?= json_encode($user_id) ?>,
    id: <?= json_encode($id) ?>,
    modelno: <?= json_encode($modelno) ?>
};


 

apiArray.forEach((test, index) => {

    const apiKey = `API Test ${index + 1}`; // 🔥 API position

    if (!finalOutput[apiKey]) {
        finalOutput[apiKey] = {};
    }

    const allItems = [
        ...(test.command || []),
        ...(test.response || []),
        ...(test.error || [])
    ];

    allItems.forEach(item => {

        const paramKey = item.mainparameter || "Unknown";

        if (!finalOutput[apiKey][paramKey]) {
            finalOutput[apiKey][paramKey] = [];
        }

       
const { mainparameter, parametername, min, max, value, ...rest } = item;


const cleanItem = {
    defaultname: parametername,
    instance: rest.instance,
    bytePosition: rest.bytePosition,
    time: rest.time
};

// ✅ include min/max + value ONLY if min & max exist
if (min !== "" && max !== "") {
    cleanItem.min = min;
    cleanItem.max = max;
    cleanItem.value = value;
}

        finalOutput[apiKey][paramKey].push(cleanItem);
    });

});

/*console.log(JSON.stringify(finalOutput, null, 2));*/

// ❌ STOP if any completely empty card exists
if (hasCompletelyEmptyCard) {
    console.log("Blocked submission due to empty instance");
    return; // 🚫 this prevents API call + print window
}

fetch('<?php echo get_uri("items/submitTestConfiguration"); ?>', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify(finalOutput)
})
   
.then(async response => {
    const text = await response.text();

   
    // ✅ SAFE CHECK BEFORE JSON PARSE
    if (!text || text.trim() === "") {
        throw new Error("Empty response from server");
    }

    if (!text.trim().startsWith("{")) {
        throw new Error("Server returned non-JSON:\n" + text);
    }

    return JSON.parse(text);
})

    .then(data => {

        if (data && data.success) {
         
           
            // ✅ OPEN PRINT WINDOW (same like saveConfiguration)
            const printWindow = window.open('', '_blank');

            printWindow.document.write(`
                <html>
                <head>
                    <title>API Test Configuration</title>
                    <style>
                        body {
                            font-family: monospace;
                            padding: 20px;
                            background: white;
                            white-space: pre-wrap;
                        }
                        pre {
                            background: #111;
                            color: white;
                            padding: 15px;
                            border-radius: 5px;
                        }
                        button {
                            padding:10px;
                            margin:5px;
                        }
                    </style>
                </head>
                <body>
                    <h2>API Test Output</h2>
                   
                     <pre>${JSON.stringify(finalOutput, null, 2)}</pre>
                    <button onclick="window.print()">Print</button>
                    <button onclick="window.close()">Close</button>
                </body>
                </html>
            `);

            printWindow.document.close();

        } else {
            alert(data.message || "Backend error");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Error: " + error.message);
    });
}





   
//
function closeApiModal(){
    document.getElementById("apiTestModal").style.display="none";
}
//

    </script>


<script>
    // Separate script to ensure ALL parameter names are displayed
    (function() {
        const targetList = document.getElementById('parameters-list');
        
        // Clear existing items if any
        targetList.innerHTML = '';

        // Loop through all keys found in the backend parameters
        Object.keys(backendData.parameters).forEach(paramName => {
            const item = document.createElement('div');
            item.className = 'parameter-item';
            item.style.cursor = 'pointer';
            
            const label = document.createElement('span');
            label.className = 'parameter-label';
            label.textContent = paramName;
            
            item.appendChild(label);

            // Link to your existing add function
            item.onclick = function(event) {
                event.stopPropagation();
                if (typeof addParameterInstance === 'function') {
                    addParameterInstance(paramName);
                }
            };

            targetList.appendChild(item);
        });
    })();
</script>
<script>
window.addEventListener('scroll', function(event) {
    // 1. Target the most common classes used by libraries for popups
    const selectors = [
        '.suggestion-box', 
        '.autocomplete-items', 
        '.select2-container--open', 
        '.ui-autocomplete', 
        '[class*="dropdown-menu"]',
        '[id*="suggestion"]',
        '.popup-container'
    ];

    selectors.forEach(selector => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(el => {
            // Force hide the box
            el.style.display = 'none';
        });
    });

    // 2. Force the active input to "Blur" (Lose focus)
    // This is the most effective way to kill library-generated menus
    if (document.activeElement && 
       (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'SELECT')) {
        document.activeElement.blur();
    }
}, true); // 'true' makes it work even if the scroll happens inside a <div>
// Listen for scroll events on the window
</script>
<script>
const savedConfig = <?= json_encode($savedConfig ?? null) ?>;
console.log("SAVED CONFIG GLOBAL:", savedConfig);

</script>
<script>
/*
document.addEventListener("DOMContentLoaded", function () {
    // 1. Safety check: If no config exists, stop.
    if (typeof savedConfig === 'undefined' || !savedConfig) {
        console.warn("⚠️ No saved config found to auto-load.");
        return;
    }

    console.log("🚀 Starting Auto-Load Sync...");

    Object.keys(savedConfig).forEach(testKey => {
        // Skip metadata keys that aren't parameter sections
        const skipKeys = ['user_id', 'id', 'modelno'];
        if (skipKeys.includes(testKey)) return;

        const testBlock = savedConfig[testKey];

        Object.keys(testBlock).forEach(sectionName => {
            const items = testBlock[sectionName];

            // Ensure we are dealing with an array of data
            if (Array.isArray(items)) {
                
                // 2. Identify the goal: How many rows are in the DB?
                const targetCount = items.length;

                // 3. Identify current state: How many rows are already in the UI?
                // (Assumes parameterInstances is your global tracker)
                const existingCount = (parameterInstances[sectionName]) 
                    ? parameterInstances[sectionName].length 
                    : 0;

                console.log(`Section [${sectionName}]: DB has ${targetCount}, UI has ${existingCount}`);

                // 4. Add exactly enough instances to match the DB count
                const needed = targetCount - existingCount;
                
                for (let i = 0; i < needed; i++) {
                    addParameterInstance(sectionName);
                }
            }
        });
    });

    console.log("✅ Auto-Load Complete.");
});
*/
/*
document.addEventListener("DOMContentLoaded", function () {
    if (typeof savedConfig === 'undefined' || !savedConfig) return;

    Object.keys(savedConfig).forEach(testKey => {
        if (['user_id', 'id', 'modelno'].includes(testKey)) return;

        const testBlock = savedConfig[testKey];

        Object.keys(testBlock).forEach(sectionName => {
            const items = testBlock[sectionName];

            if (Array.isArray(items) && items.length > 0) {
                // CHECK: Does this section already exist in the UI?
                const existingCount = parameterInstances[sectionName]?.length || 0;

                // FIX: Instead of looping through items.length (which is 7),
                // we only want to add the SECTION itself if it's missing.
                if (existingCount === 0) {
                    console.log(`Adding section: ${sectionName} (contains ${items.length} sub-parameters)`);
                    
                    // This creates the "Get Status" box ONCE
                    addParameterInstance(sectionName); 
                    
                    // If you need to fill the 7 inputs inside that box, 
                    // that logic should happen INSIDE addParameterInstance 
                    // or in a separate fill function.
                }
            }
        });
    });
});
*/
/*
document.addEventListener("DOMContentLoaded", function () {

    if (!savedConfig) {
        console.log("❌ No savedConfig");
        return;
    }

    console.log("✅ FULL savedConfig:", savedConfig);

    window.isInitialLoad = true;

    const restrictedSections = ['tuning', 'getstatus', 'commandgetstatus'];
    const sectionCount = {}; // track how many times added

    Object.keys(savedConfig).forEach(testKey => {

        if (['user_id', 'id', 'modelno'].includes(testKey)) return;

        const testBlock = savedConfig[testKey];
        if (!testBlock || typeof testBlock !== 'object') return;

        Object.keys(testBlock).forEach(sectionName => {

            const items = testBlock[sectionName];

            if (!Array.isArray(items) || items.length === 0) return;

            // init count
            if (!sectionCount[sectionName]) {
                sectionCount[sectionName] = 0;
            }

            // 🔥 RESTRICT ONLY SOME SECTIONS
            if (restrictedSections.includes(sectionName)) {

                // already added → skip
                if (sectionCount[sectionName] > 0) {
                    console.log(`⛔ Skipping extra: ${sectionName}`);
                    return;
                }

                // add ONLY DB count once
                for (let i = 0; i < items.length; i++) {
                    addParameterInstance(sectionName);
                }

                sectionCount[sectionName] = items.length;

            } else {
                // ✅ allow multiple (power, speed etc.)
                for (let i = 0; i < items.length; i++) {
                    addParameterInstance(sectionName);
                }

                sectionCount[sectionName] += items.length;
            }

        });

    });

    // ✅ render once
    renderAllInstances();

    // ✅ fill values
    setTimeout(() => {

        Object.keys(savedConfig).forEach(testKey => {

            if (['user_id', 'id', 'modelno'].includes(testKey)) return;

            const testBlock = savedConfig[testKey];

            Object.keys(testBlock).forEach(sectionName => {

                const items = testBlock[sectionName];

                items.forEach((item, index) => {
                    fillInputs(sectionName, index, item);
                });

            });

        });

    }, 300);
    
    window.isInitialLoad = false;
   
});
*/

document.addEventListener("DOMContentLoaded", function () {

    if (typeof savedConfig === 'undefined' || !savedConfig) return;

    Object.keys(savedConfig).forEach(testKey => {

        if (['user_id', 'id', 'modelno'].includes(testKey)) return;

        const testBlock = savedConfig[testKey];

        Object.keys(testBlock).forEach(sectionName => {

            const items = testBlock[sectionName];

            if (!Array.isArray(items) || items.length === 0) return;

            console.log(`Processing section: ${sectionName}`);

            items.forEach((item) => {

                const instanceNumber = item.instance;

                // ✅ Ensure array exists
                if (!parameterInstances[sectionName]) {
                    parameterInstances[sectionName] = [];
                }

                // ✅ Check if this instance already exists
                const alreadyExists = parameterInstances[sectionName].some(
                    inst => inst.instanceNumber == instanceNumber
                );

                if (!alreadyExists) {

                    console.log(`Adding ${sectionName} instance ${instanceNumber}`);

                    // ✅ Create UI card
                    addParameterInstance(sectionName);

                    // ✅ Get last added instance
                    const lastInstance = parameterInstances[sectionName].slice(-1)[0];

                    if (lastInstance) {
                        lastInstance.instanceNumber = instanceNumber;

                        // 🔥 IMPORTANT: also update UI label if needed
                        const card = document.getElementById(`parameter-card-${lastInstance.id}`);
                        if (card) {
                            const title = card.querySelector("h3");
                            if (title) {
                                title.innerText = `${sectionName} #${instanceNumber}`;
                            }
                        }
                    }
                }

            });

        });

    });

});
</script>

</body>
</html>
