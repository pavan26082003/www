<?php 
//$node_ids = ['FFFFFFFFFFFFFFFF7EC2000000005200']; 
?>
<!DOCTYPE html>
<html>
<head>
<title>Label Designer</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


<style>
body {
  font-family: 'Segoe UI', sans-serif;
  background: linear-gradient(135deg, #e3f2fd, #ffffff);
  margin: 0;
}


/* Rotates content 90 degrees for Vertical labels */
.rotate-content-sideways {
    transform: rotate(-90deg); /* Use 90deg or -90deg based on your preference */
    transform-origin: center;
    white-space: nowrap; /* Prevents text from wrapping during rotation */
    width: auto !important;
}




.container {
  display: flex;
  width: 100%;
  height: calc(100vh - 50px); /* Full screen minus navbar */
}

/* --- PREMIUM FROSTED SIDEBAR --- */
.left-panel {
    width: 100px;
    /* Frosted glass effect */
    background: rgba(255, 255, 255, 0.8) !important; 
    backdrop-filter: blur(12px); 
    
    padding-top: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    border-right: 1px solid rgba(59, 130, 246, 0.2); /* Subtle blue tinted border */
    height: 100vh;
    position: fixed;
    left: 0;
    top: 60px;
    z-index: 1000;
}

.right-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
   background-color: #f1f5f9 !important; /* Very light cool slate gray */
    background-image: radial-gradient(#cbd5e1 0.5px, transparent 0.5px); /* Subtle high-tech dots */
    background-size: 20px 20px;
    position: relative;
    overflow: hidden;
}

h3 { color: #0d47a1; text-align: center; }

button, select {
  margin-bottom: 15px;
  display: inline;
  border-radius: 5px;
  border: 1px solid #ccc;
  font-size: 10px;
  cursor: pointer;
}

/* --- 1. SET SIZE FOR ALL DROPDOWNS --- */
#nodeSelect, #frameType, #inchSize { 
    width: 100px; 
    height: 30px; 
}

/* --- 2. POSITION THEM ON THE RIGHT SIDE WHEN CLICKED --- */
.icon-dropdown-wrapper select {
    position: fixed !important;
   left :105px;
    width: 350px !important; /* Increased width */
    height: 50px !important; /* Increased height */
    
    /* Make it visible and beautiful */
    opacity: 1 !important;   
    font-size: 18px !important; 
    font-weight: 800 !important;
    color: #1e293b !important;
    background: white !important;
    border: 2px solid #3b82f6 !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    
    z-index: 9999 !important;
    cursor: pointer;
}

/* --- 3. INCREASE FONT OF OPTIONS --- */
#nodeSelect option, #frameType option, #inchSize option {
    font-size: 16px !important;
    font-weight: 600;
    padding: 10px;
}
button {
  background-color: #2196F3;
  color: white;
  border: none;
  height: 30px;
}

button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

/* Modal */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0; top: 0;
  width: 100%; height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.5);
}

.modal-content {
  background-color: #f9f9f9;
  margin: 10% auto;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #ddd;
  width: 50%;
  position: relative;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.close {
  position: absolute;
  top: 10px; right: 15px;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}
.close:hover { color: red; }

.preview-container-outer {
    width: 500px !important;
    height: 500px !important;
    background-color: #ffffff;
    display: flex !important;
    align-items: center;
    justify-content: center;
    border-radius: 24px;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 20px 50px rgba(15, 23, 42, 0.1) !important;
    margin-bottom: 10px;
}

.preview-container {
    position: relative;   /* 🔥 REQUIRED */
    width: 250px;
    height: 250px;
    border-radius: 6px;
}

#imagePreview {
 max-width: 100%;
  max-height: 100%;
  display: none;
  transition: transform 0.4s ease;
  margin-top: 0 !important;

}


.button-group {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  gap: 15px;
}

/* Navbar */
/* --- PREMIUM FROSTED NAVBAR --- */
.navbar {
    width: 100%;
    height: 60px;
    /* Soft white with a glass-like transparency */
    background: rgba(255, 255, 255, 0.9) !important; 
    backdrop-filter: blur(10px); /* This makes it look like expensive frosted glass */
    
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 25px;
    box-sizing: border-box;
    position: sticky;
    top: 0;
    z-index: 1100;
    
   
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); 
}

.brand-text {
    font-size: 22px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 2px;
    
    /* Deep Indigo-Navy text for a premium look */
    color: #1e293b !important;
    
    /* Subtle blue gradient effect on the text itself */
    background: linear-gradient(to right, #1e293b, #3b82f6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}



/* --- MODERNIZED SIDEBAR (LARGER FONT & COMPACT SPACING) --- */
.icon-only-button, 
.icon-upload-label, 
.icon-dropdown-wrapper {
    position: relative;
    background: transparent;
    border: none;
   color: #334155;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 95px !important; /* Slightly wider to accommodate larger font */
    height: 85px !important; /* Slightly shorter to prevent sidebar scrolling */
    border-radius: 12px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    margin-bottom: 4px; /* Reduced margin for a tighter professional look */
}

/* Icons */
.icon-only-button i, 
.icon-upload-label i, 
.dropdown-icon {
    font-size: 24px !important; 
    margin-bottom: 6px; /* Tightened gap to icon */
    color: inherit;
}

/* INCREASED FONT SIZE & BOLDNESS */
.icon-only-button span, 
.icon-upload-label span, 
.dropdown-text {
    font-size: 12px !important; /* BOOSTED from 10px */
    font-weight: 800 !important; /* Maintains the 'Fabulous' thickness */
    text-transform: uppercase;
    letter-spacing: 0.6px;
    text-align: center;
    line-height: 1.1; /* Tighter line height for better multi-line stacking */
    width: 100%;
    padding: 0 6px;
    box-sizing: border-box;
    display: block;
    color: inherit;
}

/* Select Box Overlay */
.icon-dropdown-wrapper select {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    opacity: 0 !important;
    z-index: 9999 !important;
    cursor: pointer;
}




/* Hover Effect with Navy/Blue Theme */
.icon-only-button:hover, 
.icon-upload-label:hover, 
.icon-dropdown-wrapper:hover {
 background: rgba(59, 130, 246, 0.1) !important;
    color: #3b82f6 !important;
   
}


#nodeSelect option {
  background-color: white; /* Your desired background color */
  color: black;           /* Your desired text color */
  padding: 10px;            /* Note: padding often won't show in many browsers */
 marging:20px;
}

/* Optional: Style the first placeholder option differently */
#nodeSelect option[value=""] {
  color: blue;
 
}
/* Size info display */
.size-info {
  background-color: #f0f8ff;
  padding: 5px 10px;
  border-radius: 5px;
  margin: 5px 0;
  font-size: 10px;
  color: #0d47a1;
  border-left: 3px solid #2196F3;
  display: none;
}

.size-info.show {
  display: block;
}

/* Dimension display */
.dimension-display {
  font-size: 11px;
  color: white;
  background: rgba(0, 0, 0, 0.6);
  padding: 3px 8px;
  border-radius: 4px;
  margin-top: 5px;
}

/* Clear image button styling */
#clearImageBtn {
  background-color: #ff4444;
  margin-top: 5px;
}

#clearImageBtn:hover {
  background-color: #cc0000;
}

/* Clear image button in left panel */
.clear-image-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  margin-left: -20px;
  background: none;
  border: none;
  color: white;
  font-size: 10px;
  width: 100px;
  height: 60px;
  margin-bottom: 15px;
}

.clear-image-btn i {
  font-size: 24px;
  margin-bottom: 5px;
}
img, canvas {
    image-rendering: pixelated;
    image-rendering: crisp-edges;
}
canvas {
  width: 100%; /* This stretches the canvas */
  height: auto;
}
.img-container {
        width: 100%;
        max-height: 500px;
        margin-top: 20px;
    }
#rightSideOutput {
    word-break: break-all; /* Prevents long IDs from flowing off the screen */
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 5px;
    min-height: 50px;
   color:red;
margin:50px;
}
.selected-node-value {
  margin-left: 400px;
  font-weight: 600;
  color: red;
  max-width: 350px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
 position:absolute;
top:130px;
  
}
.selected-node-wrapper {
  margin-left: 200px;
  display: inline;
  flex-direction: column;
  position:absolute;
top:80px;
flex-direction: row; 
}

.selected-node-heading {
  font-weight: 200px;
  color:black;
flex-direction: row;
display:flex;
}

.status-online {
    color: green;
    font-weight: 600;
}

.status-offline {
    color: red;
    font-weight: 600;
}


.node-badge {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    padding: 8px 14px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 14px;
    background: #f1f1f1;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    display: flex;
    gap: 8px;
    align-items: center;
}

.node-badge .online {
    color: #0a9b1f;
}

.node-badge .offline {
    color: #d11a1a;
}

.selected-node-display {
    position: fixed;
    top: 80px; /* Slightly lower to breathe away from the navbar */
    right: 30px;
    /* Soft white with a glass blur effect */
    background: rgba(255, 255, 255, 0.9) !important;
    backdrop-filter: blur(8px); 
    
    padding: 20px;
    border-radius: 16px; /* Modern rounded corners */
    
    /* Professional Navy/Blue border accent */
    border: 1px solid #e2e8f0 !important;
    border-left: 6px solid #3b82f6 !important; 
    
    /* Subtle deep shadow for a "floating" look */
    box-shadow: 0 15px 35px rgba(15, 23, 42, 0.1) !important;
    
    min-width: 320px;
    max-width: 450px;
    z-index: 2000; /* Ensure it stays above everything */
    
    /* Typography */
    font-family: 'Inter', -apple-system, sans-serif;
    color: #1e293b;
    line-height: 1.5;
}

.selected-node-display .label {
    color: #0d47a1;
    margin-right: 10px;
    font-weight: 600;
    font-size: 14px;
    display: block;
    margin-bottom: 5px;
}

.selected-node-display .value {
    color: #d11a1a;
    font-weight: 700;
    font-size: 18px;
    display: block;
    background: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    border: 1px solid #e0e0e0;
    overflow-x: auto;
    white-space: nowrap;
    max-height: 100px;
    overflow-y: auto;
}

/* Status indicator */
.selected-node-display .status {
    font-size: 12px;
    margin-top: 5px;
    padding: 3px 8px;
    border-radius: 12px;
    display: inline-block;
    font-weight: 600;
}

.selected-node-display .status.online {
    background: rgba(76, 175, 80, 0.1);
    color: #0a9b1f;
    border: 1px solid rgba(76, 175, 80, 0.3);
}

.selected-node-display .status.offline {
    background: rgba(244, 67, 54, 0.1);
    color: #d11a1a;
    border: 1px solid rgba(244, 67, 54, 0.3);
}

/* Make it more prominent on hover */
.selected-node-display:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

/* Button container */
/* --- COMPACT ACTION AREA --- */
.buttons-container {
    display: grid;
    grid-template-columns: repeat(3, 160px); 
    gap: 8px; /* Tighter gap to save height */
    padding: 15px 28px; /* Reduced vertical padding */
    background: #ffffff;
    border-radius: 18px; 
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    margin-top: 10px; /* Pulls the box closer to the image */
}

.action-button {
    height: 34px !important; /* Slimmer buttons to save vertical space */
    font-size: 12px !important; 
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* --- THE COMPACT API ROW --- */
.api-row {
    grid-column: span 3;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px; /* Puts checkbox next to button */
    border-top: 1px solid #f1f5f9;
}

#generateApiBtn {
    background: #6366f1 !important; /* Professional Indigo */
    color: white !important;
    width: 140px; /* Slightly narrower */
    height: 32px !important;
}

.api-checkbox-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 10px;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    cursor: pointer;
}
/* --- PROFESSIONAL COLOR PALETTE --- */
#validateBtn { background: #0891b2; color: white; }
#resetBtn   { background: #64748b; color: white; }
#applyBtn   { background: #2563eb; color: white; }
#downloadBtn { background: #7c3aed; color: white; }
#previewBtn  { background: #0f172a; color: white; } /* Dark navy for high contrast */
#saveBtn     { background: #059669; color: white; }

/* Disabled state */
#saveBtn:disabled {
    background: #e2e8f0;
    color: #94a3b8;
    cursor: not-allowed;
    box-shadow: none;
}
#validateBtn {
    background-color: #17a2b8;
    color: white;
    border: none;
}

#resetBtn {
    background-color: #ffc107;
    color: #212529;
    border: none;
}

#applyBtn {
    background-color: #007bff;
    color: white;
    border: none;
}

#downloadBtn {
    background-color: #6f42c1;
    color: white;
    border: none;
}

#previewBtn {
    background-color: #20c997;
    color: white;
    border: none;
}

.color-buttons {
    display: flex;
    gap: 5px;
    margin-top: 10px;
}

.color-btn {
    padding: 5px 10px;
    font-size: 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    color: white;
    font-weight: 600;
}

#btnConvertWhite {
    background-color: #ffffff;
    color: #000;
    border: 1px solid #ccc;
}

#btnConvertRed {
    background-color: #dc3545;
}

#btnConvertYellow {
    background-color: #ffc107;
    color: #000;
}

#btnConvertBlack {
    background-color: #000000;
}

/* Status message */
.status-message {
    position: fixed;
    top: 140px;
    right: 20px;
    background: #fff3cd;
    padding: 10px 15px;
    border-radius: 6px;
    border: 1px solid #ffeaa7;
    font-size: 14px;
    color: #856404;
    z-index: 89;
    min-width: 300px;
    max-width: 500px;
    display: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.status-message.online {
    background: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.status-message.offline {
    background: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.status-message i {
    margin-right: 8px;
}

/* Refresh button in navbar */
.refresh-button {
    background: #3b82f6 !important; 
    color: white !important;
    border: none !important;
    border-radius: 10px !important; /* Slightly more rounded for the larger size */
    
    /* --- INCREASED SIZE --- */
    padding: 12px 28px !important;   /* Increased from 8px 16px */
    min-width: 180px;               /* Ensures a consistent, prominent width */
    height: 50px;                   /* Defined height for a bold look */
    
    font-weight: 900 !important;    /* Thicker text */
    text-transform: uppercase;
    font-size: 13px !important;     /* Increased from 11px */
    
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 5px 0 #2563eb;    /* Slightly deeper 3D effect */
}

.refresh-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 0 #2563eb;
}

.refresh-button:active {
    transform: translateY(2px);     /* Pushes down when clicked */
    box-shadow: 0 2px 0 #2563eb;
}

.refresh-button i {
    font-size: 20px;                /* Larger icon to match the bigger button */
}




/* --- RESPONSIVENESS FIX --- */
@media (max-width: 768px) {
    /* 1. Move sidebar to the bottom for mobile */
    .left-panel {
        width: 100% !important;
        height: 70px !important;
        bottom: 0 !important;
        top: auto !important;
        flex-direction: row !important;
        justify-content: space-around;
        padding: 0;
        border-right: none;
        border-top: 1px solid rgba(59, 130, 246, 0.2);
    }

    /* 2. Make the preview container fit the screen width */
    .preview-container-outer {
        width: 90vw !important;
        height: 90vw !important;
    }

    /* 3. Stack the action buttons vertically or in 2 columns */
    .buttons-container {
        grid-template-columns: repeat(2, 1fr);
        width: 90%;
        margin-bottom: 80px; /* Space for the bottom sidebar */
    }

    /* 4. Hide or reposition the fixed "Selected Node" box so it doesn't block the image */
    .selected-node-display {
        position: relative !important;
        top: 0 !important;
        right: 0 !important;
        width: 90% !important;
        margin-bottom: 20px;
    }
    
    /* 5. Move the pop-out menus to appear above the icons */
    .icon-dropdown-wrapper select {
        left: 50% !important;
        transform: translateX(-50%);
        bottom: 80px !important;
        top: auto !important;
        width: 80% !important;
    }
}



/* Hides scrollbar but allows scrolling functionality */
    .fabulous-sidebar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;     /* Firefox */
    }
    .fabulous-sidebar::-webkit-scrollbar {
        display: none;             /* Chrome, Safari, Opera */
    }


@keyframes pulse-blue {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.7); }
    70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(52, 152, 219, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(52, 152, 219, 0); }
}


@keyframes pulse-orange {
    0% { box-shadow: 0 0 0 0 rgba(230, 126, 34, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(230, 126, 34, 0); }
    100% { box-shadow: 0 0 0 0 rgba(230, 126, 34, 0); }
}
        
</style>
</head>

<body>


<!-- ✅ Selected Node Badge -->
    <div id="nodeBadge" class="node-badge" style="display:none;">
        <span id="nodeIdText"></span>
        <span id="nodeStatusText"></span>
    </div>

<!-- Navbar -->
<nav class="navbar" style="display: flex; justify-content: space-between; align-items: center; padding: 0 20px;">
    <div class="brand-container">
        <i class="bi bi-layers-half" style="font-size: 20px; color: #3b82f6;"></i>
        <span class="brand-text">Label Designer</span>
    </div>

    <img src="https://efactor.tech/files/brand_logo/karat_infinity.jpeg" 
         alt="Karat Infinity" 
         style="height: 65px; width: auto; image-rendering: high-quality; transform: translateZ(0); object-fit: contain;">
</nav>

<div id="selectedNodeDisplay" class="selected-node-display">
    <span class="label">SELECTED NODE:</span>
    <span id="selectedNodeValue" class="value">No node selected</span>
    <span id="selectedNodeStatus" class="status"></span>
</div>

<!-- Status Message Display -->
<div id="statusMessage" class="status-message">
    <i class="bi bi-info-circle"></i>
    <span id="statusMessageText">Select a node to proceed</span>
</div>

   <div class="container">
  <!-- LEFT PANEL -->
  <div class="left-panel">
    <button id="openProductModal" class="icon-only-button" 
        style="display:flex; flex-direction:column; align-items:center; justify-content:center;">
      <i class="bi bi-box"></i>
      <span style="font-size:12px;">View Product Info</span>
    </button>

<div id="productModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h4 style="margin-bottom:10px; font-weight:bold;">Product Information</h4>
    <p><strong>Model Name:</strong> <?= htmlspecialchars($modelname ?? '') ?></p>
    <p><strong>Model No:</strong> <?= htmlspecialchars($modelno ?? '') ?></p>
    <p><strong>Product Name:</strong> <?= htmlspecialchars($category ?? '') ?></p>
  </div>
</div>

<div class="icon-dropdown-wrapper">
    <i class="bi bi-menu-up dropdown-icon"></i>
    <div class="dropdown-text">Select Node</div>

    <select id="nodeSelect" required>
        <option value="">Select Node</option>

        <?php foreach ($displayNodes as $node): ?>
            <option 
                value="<?= htmlspecialchars($node['node_id']) ?>"
                data-status="<?= strtolower($node['status']) ?>"
            >
                <?= htmlspecialchars($node['name']) ?> (<?= strtoupper($node['status']) ?>)
         </option>
        <?php endforeach; ?>
    </select>
</div>




    
    <!-- Size Requirements Info -->
    <div id="sizeInfo" class="size-info">
      <div><strong>Required Size:</strong></div>
      <div id="sizeText">-</div>
    </div>








    <!-- Inch Size Selection -->
    <div class="icon-dropdown-wrapper">
      <i class="bi bi-rulers dropdown-icon"></i>
      <div class="dropdown-text">Select Size</div>
          <select id="inchSize">
        <option value="">Select Size</option>
        <option value="2.1">2.1 inch</option>
        <option value="2.9">2.9 inch</option>
        <option value="4.2">4.2 inch</option>
      </select>

    </div>


    <label for="imageUpload" class="icon-upload-label">
      <i class="bi bi-image"></i>
      <span style="font-size:12px"> Upload Image</span>
    </label>
    <input type="file" id="imageUpload" accept="image/*" style="display:none;">

    <!-- Clear Image Button -->
    <button type="button" id="clearImageBtn" class="clear-image-btn" style="display:none;">
      <i class="bi bi-trash"></i>
      <span style="font-size:10px">Clear Image</span>
    </button>

    <div class="icon-dropdown-wrapper">
      <i class="bi bi-menu-up dropdown-icon"></i>
      <div class="dropdown-text">Frame Type</div>
      <select id="frameType">
        <option value="">Frame Type</option>
        <option value="Vertical">Vertical</option>
        <option value="Horizontal">Horizontal</option>
        <option value="Rotate">Rotate</option>
      </select>
    </div>



<div id="customTemplateBtn" class="icon-only-button">
    <i class="bi bi-file-earmark-richtext"></i>
    <span>Template</span>
</div>
   
      </div>



  <!-- RIGHT PANEL -->
  <div class="right-panel">
  



    <div class="preview-container-outer">

    <div class="preview-container">
        <img id="imagePreview" style="border:2px solid black; margin-left:-250px;" src="#" alt="Preview">
    </div>

    <!-- dimension info -->
    <div style="margin-right:-570px; margin-bottom:350px;">
        <div style="font-size:10px; color:white;">
            Width: <span id="imageWidth">0</span> px
        </div>
        <div style="font-size:10px; color:white;">
            Height: <span id="imageHeight">0</span> px
        </div>
        <div id="requiredDimensions" class="dimension-display" style="display:none;">
            Required: <span id="requiredSizeText">-</span>
        </div>
    </div>

</div>

<!-- ADDED MISSING BUTTONS HERE -->
<div class="buttons-container">
    <!-- Color Conversion Buttons -->
    <!--div class="color-buttons">
        <button id="btnConvertWhite" class="color-btn">Convert to White</button>
        <button id="btnConvertRed" class="color-btn">Convert to Red</button>
        <button id="btnConvertYellow" class="color-btn">Convert to Yellow</button>
        <button id="btnConvertBlack" class="color-btn">Convert to Black</button>
    </div-->
    
    <!-- Main Action Buttons -->
    <button id="validateBtn" class="action-button">Validate Colors</button>
    <button id="resetBtn" class="action-button">Reset Image</button>
    <button id="applyBtn" class="action-button">Apply Changes</button>
    <button id="downloadBtn" class="action-button">Download Image</button>
    <button id="previewBtn" class="action-button">Preview Original</button>
    <button id="saveBtn" class="action-button" disabled>Send Image</button>
<div class="api-row">
       
    </div>
</div>

<div style="position: absolute; bottom: 70px; right: 70px; z-index: 100;">
        <button id="refreshPageBtn" class="refresh-button" title="Refresh page">
            <i class="bi bi-arrow-clockwise"></i>
            <span>Refresh Status</span>
        </button>
    </div>


</div>




<div id="colorMismatchModal" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    
    <div id="modalContent" 
         style="background:white; 
                margin: 5% auto; 
                padding:25px; 
                border-radius:8px; 
                width:400px; 
                max-height:80vh;
                overflow-y:auto;
                box-shadow:0 4px 12px rgba(0,0,0,0.15);">
        
        <h3>❌ Invalid Colors Found</h3>
        <p>Map each illegal color found to an allowed color:</p>
        
        <div id="mismatchedColorContainer" 
             style="margin-top:15px; padding-top:10px; border-top:1px solid #eee;">
            </div>
        
        <div style="margin-top:25px; display:flex; justify-content:space-between;">
            <button onclick="document.getElementById('colorMismatchModal').style.display='none'" 
                    style="padding:10px 15px; background:#ccc; color:#333; border:none; border-radius:4px; cursor:pointer;">
                Cancel
            </button>
            <button id="proceedColorMappingBtn" disabled
                    style="padding:10px 15px; background:#28a745; color:white; border:none; border-radius:4px; cursor:pointer;">
                Proceed with Mapping
            </button>
        </div>
    </div>
</div>
<div id="saveSummary" style="display:none; margin-top: 15px; padding: 10px; border: 1px solid #ccc; background: #f9f9f9; color: #333;">
    <h4 style="margin-top:0">Saved Data Details:</h4>
    <div id="summaryContent"></div>




</div>






<div id="templateModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:2000; justify-content:center; align-items:center; backdrop-filter: blur(15px);">
    <div style="background:#ffffff; border-radius:28px; width:98%; max-width:1550px; display:flex; flex-direction:column; height:92vh;overflow:hidden; box-shadow: 0 40px 100px rgba(0,0,0,0.5);">
        
<div style="background: #ffffff; border-bottom: 2px solid #f8fafc; display: flex; justify-content: space-between; align-items: center; padding: 15px 25px; width: 100%; box-sizing: border-box;">
    
    <div style="flex-shrink: 0; min-width: 250px;">
        <h2 style="color: #0f172a; margin: 0; font-size: 28px; font-weight: 900; letter-spacing: -1px; text-transform: uppercase; line-height: 1;">
            LABEL <span style="color: #3498db;">STUDIO</span>
        </h2>
        <p style="margin: 4px 0 0 0; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px;">
            PREMIUM EDITOR • <span style="color: #10b981;">LIVE</span>
        </p>
    </div>

    <div style="display: flex; align-items: center; justify-content: center; gap: 20px; flex-grow: 1;">
        
        <div style="display: flex; align-items: center; gap: 12px; border-right: 1px solid #f1f5f9; padding-right: 20px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 12px; font-weight: 900; color: #94a3b8;">SIZE</span>
                <input type="number" id="fontSizeInput" value="40" oninput="adjustFontSize(this.value)" 
                       style="height: 36px; width: 60px; text-align: center; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 15px; font-weight: 800; color: #1e293b; background: #ffffff;">
            </div>

            <div style="display: flex; align-items: center; gap: 4px; background: #f1f5f9; padding: 4px; border-radius: 8px;">
                <button onclick="applyFontStyle('bold')" style="width: 34px; height: 34px; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 900; cursor: pointer; color: #334155;">B</button>
                <button onclick="applyFontStyle('italic')" style="width: 34px; height: 34px; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; font-style: italic; cursor: pointer; color: #334155;">I</button>
                <button onclick="applyFontStyle('underline')" style="width: 34px; height: 34px; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: underline; cursor: pointer; color: #334155;">U</button>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 12px; border-right: 1px solid #f1f5f9; padding-right: 15px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 10px; font-weight: 900; color: #94a3b8;">BG</span>
                    <input type="color" id="bgColorPicker" oninput="applyCustomColor(this.value)" style="width: 30px; height: 30px; border: 1px solid #cbd5e1; border-radius: 4px; cursor: pointer; background: none;">
                </div>

<div style="display: flex; align-items: center; gap: 8px;">
    <span id="bgLabelText" style="font-size: 10px; font-weight: 900; color: #94a3b8; letter-spacing: 0.5px; white-space: nowrap;">
        BG IMG
    </span>
    
    <label for="bgUpload" style="width: 30px; height: 30px; border: 1.5px dashed #3498db; border-radius: 4px; display: flex; align-items: center; justify-content: center; cursor: pointer; background: #f0f9ff; color: #3498db; font-size: 14px; font-weight: 900; transition: all 0.3s ease;">
        +
    </label>
    <input type="file" id="bgUpload" accept="image/*" onchange="handleBackgroundUpload(event)" style="display: none;">
</div>

                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 10px; font-weight: 900; color: #94a3b8;">TEXT</span>
                    <input type="color" id="textColorPicker" oninput="applyTextColor(this.value)" style="width: 30px; height: 30px; border: 1px solid #cbd5e1; border-radius: 4px; cursor: pointer; background: none;">
                </div>
            </div>

           <div style="display: flex; align-items: center; gap: 10px;">
    <span id="qrLabelText" style="font-size: 11px; font-weight: 900; color: #3498db; letter-spacing: 0.5px; white-space: nowrap;">
        QR DATA
    </span>

    <input type="text" id="qrInput" oninput="updateQRCode(this.value)" placeholder="Enter Content..." 
           style="width: 240px; height: 38px; padding: 0 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 600; color: #1e293b; outline: none; background: #ffffff; transition: all 0.3s ease;">
                <button onclick="addLiveField()" style="height: 38px; padding: 0 15px; background: #10b981; color: white; border: none; border-radius: 8px; font-weight: 800; font-size: 11px; cursor: pointer;">+ ADD TEXT</button>
<div style="display: flex; align-items: center; gap: 4px; background: #f8fafc; padding: 2px; border-radius: 8px; border: 1px solid #e2e8f0; margin-left: 5px;">
                    <button onclick="undoAction()" title="Undo (Ctrl+Z)" style="width: 34px; height: 34px; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #64748b; transition: all 0.2s;">
                        ⟲
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px; min-width: 160px;">
        <img src="https://efactor.tech/files/brand_logo/karat_infinity.jpeg" alt="Logo" style="height: 35px; width: auto; mix-blend-mode: multiply;">
        <div id="dimensionBadge" style="border: 1.5px solid #3498db; color: #3498db; padding: 3px 12px; border-radius: 50px; font-weight: 900; font-family: monospace; font-size: 11px;">
            500 x 250 PX
        </div>
    </div>
</div>
<div id="apiModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; justify-content:center; align-items:center; backdrop-filter: blur(5px);">
    <div style="background:white; width:90%; max-width:700px; border-radius:24px; padding:25px; display:flex; flex-direction:column; max-height: 90vh; box-shadow: 0 25px 50px rgba(0,0,0,0.3);">
        
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <h3 style="margin:0; color:#2c3e50; font-size:20px;">API Payload Editor</h3>
            <span style="font-size:12px; color:#95a5a6; font-family:monospace;">JSON FORMAT</span>
        </div>

        <textarea id="apiTextarea" spellcheck="false" style="width:100%; flex:1; min-height:350px; font-family: 'Courier New', monospace; padding:15px; border:2px solid #f1f2f6; border-radius:12px; font-size:13px; outline:none; resize:none; box-sizing:border-box; background:#f8f9fa; color:#2f3542;"></textarea>
        
        <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px;">
            <button onclick="closeApiEditor()" style="padding:12px 25px; border-radius:10px; border:1px solid #dcdde1; background:#f1f2f6; color:#57606f; cursor:pointer; font-weight:700; transition:all 0.2s;">CANCEL</button>
            <button onclick="applyApiChanges()" style="padding:12px 35px; border-radius:10px; border:none; background:#27ae60; color:white; font-weight:800; cursor:pointer; box-shadow:0 4px 10px rgba(39, 174, 96, 0.2);">APPLY CHANGES</button>
        </div>
    </div>
</div>



<div style="display:flex; height: calc(100% - 110px); overflow:hidden;">
    <div style="width:320px; background:#f8f9fa; border-right:1px solid #eee; padding:10px; display:flex; flex-direction:column; gap:20px; overflow-y:auto;">
                
    <label style="font-size:15px; font-weight:bold; color: #666; text-transform:uppercase;">Select Layout Style:</label>
<div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:0px;">

    
 



<div style="padding: 10px; width: 100%; box-sizing: border-box; background: #fff;">

    <div style="font-size: 11px; font-weight: 900; color: #666; text-transform: uppercase; letter-spacing: 1px; margin: 10px 0 15px 5px; border-left: 3px solid #ff0000; padding-left: 8px; width:280px;">
        Horizontal Layouts
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 30px; width: 100%;">


 
 <button id="layout-style4" onclick="loadTemplate('style4', this)" style="padding:0; cursor:pointer; border:1px solid #ddd; border-radius:12px; background:#ffffff !important; height:120px; overflow:hidden; width:100%; display:block; outline:none; position:relative;">
    
    <div style="width: 100%; height: 100%; position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events:none; font-family: 'Arial Black', sans-serif; background: #ffffff;">
        
        <div style="position: relative; display: flex; align-items: flex-start;">
            <div style="color: #ff0000; font-size: 22px; font-weight: 900; letter-spacing: -1px; line-height: 1;">
                <br>DHRIO
            </div>
            <div style="color: #ff0000; font-size: 5px; font-weight: 900; margin-left: 1px; margin-top: 2px;">
               <br> TM
            </div>
        </div>

        <div style="display: flex; align-items: center; margin-top: 6px;">
            <div style="width: 8px; height: 2px; background: #ff0000; margin-right: 5px;"></div>
            
            <div style="color: #000000; font-size: 5.5px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">
              <br>  CONNECT - CONTROL - COMFORT
            </div>
        </div>

    </div>
</button>

        <button id="layout-style1" onclick="loadTemplate('style1', this)" style="padding:0; cursor:pointer; border:1px solid #ddd; border-radius:12px; background:#ff0000 !important; height:120px; overflow:hidden; width:100%; display:block; outline:none; position:relative;">
    
    <div style="width: 100%; height: 100%; position: relative; overflow: hidden; background: #ff0000; pointer-events:none; font-family: 'Arial Black', sans-serif;">
        
        <div style="position: absolute; top: 20px; left: 10px; text-align: left;">
            <div style="color: #000; font-size: 7px; font-weight: 900; text-transform: uppercase; line-height: 1;">
               <br> NATIONAL <span style="color: #ffff00;">BRANDS</span> WEEK
            </div>
            <div style="color: #ffffff; font-size: 4.5px; font-weight: 900;  text-transform: uppercase; letter-spacing: 0.2px;">
               <br> ANYTHING & EVERYTHING
            </div>
        </div>

        <div style="position: absolute; top: 8px; right: 8px; background: #ffffff; padding: 2px 4px; border: 0.5px solid #000; text-align: right;">
            <div style="color: #000; font-size: 3px; font-weight: 900; line-height: 1;"><br>FASHION FACTORY</div>
            <div style="color: #ff0000; font-size: 2.5px; font-weight: 700;"><br>BRANDS FOR SALE</div>
        </div>

        <div style="position: absolute; left: 10px; bottom: 32px; display: flex; align-items: baseline;">
            <div style="color: #ffffff; font-size: 32px; font-weight: 900; line-height: 0.8;"><br>50</div>
            <div style="color: #ffffff; font-size: 14px; font-weight: 900; margin-left: 1px;"><br>%</div>
            
            <div style="margin-left: 6px; border-left: 1.5px solid #ffffff; padding-left: 4px; display: flex; flex-direction: column; justify-content: center;">
                <div style="color: red; font-size: 5px; font-weight: 900; background: white; padding: 1px 2px; margin-bottom: 1px;"><br>@FLAT</div>
                <div style="color: #ffffff; font-size: 6px; font-weight: 900; line-height: 1;"><br>OFF</div>
            </div>
        </div>

        <div style="position: absolute; bottom: 10px; right: 10px; width: 22px; height: 22px; background: #000; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px; box-sizing: border-box;">
            <div style="width: 100%; height: 100%; border: 1px solid #fff; position: relative;">
                <div style="position: absolute; top: 2px; left: 2px; width: 4px; height: 4px; background: #fff;"></div>
                <div style="position: absolute; bottom: 2px; right: 2px; width: 4px; height: 4px; background: #fff;"></div>
            </div>
        </div>
    </div>
</button>

        <button id="layout-style5" onclick="loadTemplate('style5', this)" style="padding:0; cursor:pointer; border:1px solid #ddd; border-radius:12px; background:none !important; height:120px; overflow:hidden; width:100%; display:block; outline:none; position:relative;">
            <div style="width: 100%; height: 100%; position: absolute; top:0; left:0; background-image: url('https://plus.unsplash.com/premium_photo-1664302769311-95bbc931bee5?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8cmVkJTIwbmF0dXJlfGVufDB8fDB8fHww'); background-size: cover; background-position: center; filter: brightness(0.7);"></div>
            <div style="width: 100%; height: 100%; position: relative; display: flex; flex-direction: column; justify-content: center; align-items: center; pointer-events:none; font-family: 'Arial', sans-serif; z-index: 2;">
                <div style="border-top: 1px solid #fff; border-bottom: 1px solid #fff; padding: 4px 0; width: 80%; text-align: center;">
                    <div style="color:#ffffff; font-size: 9px; font-weight: 900; letter-spacing: 1px;">NATURE PRESERVE</div>
                </div>
                <div style="color:#ffffff; font-size: 4px; font-weight: 500; margin-top: 5px;">ESTABLISHED 2026</div>
            </div>
        </button>

      <button id="layout-style6" onclick="loadTemplate('style6', this)" style="padding:0; cursor:pointer; border:1px solid #333; border-radius:12px; background:#000000 !important; height:120px; overflow:hidden; width:100%; display:block; outline:none; position:relative;">
    
    <div style="width: 100%; height: 100%; position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events:none; font-family: 'Arial', sans-serif; background: #000000;">
        
        <div style="color:#ffffff; font-size: 10px; font-weight: 900; letter-spacing: 0.5px; margin-bottom: 5px; text-transform: uppercase;">
            <span style="color:#ffffff;"><br>IOT</span> <span style="color:#ffffff;">CORE</span> PRO
        </div>

        <div style="background:#ff0000; color:#ffffff; padding: 3px 10px; font-size: 14px; font-weight: 700; border-radius: 3px; line-height: 1;">
           <br> $129.99
        </div>

        <div style="display: flex; align-items: center; margin-top: 10px; width: 80%; justify-content: center;">
            <div style="width: 15px; height: 1px; background: #ffff00;"></div>
            <div style="color:#ffffff; font-size: 4px; font-weight: 600; text-transform: uppercase; margin: 0 6px; white-space: nowrap; letter-spacing: 0.3px;">
              <br>  SECURE • CONNECT • AUTOMATE
            </div>
            <div style="width: 15px; height: 1px; background: #ffff00;"></div>
        </div>

        <div style="position: absolute; top: 10px; right: 10px; width: 3px; height: 3px; background: #ff0000; border-radius: 50%;"></div>
    </div>
</button>

<button id="layout-style8" onclick="loadTemplate('style8', this)" style="padding:0; cursor:pointer; border:1px solid #ddd; border-radius:12px; background:#ffffff !important; height:120px; overflow:hidden; width:100%; display:block; outline:none; position:relative;">
    <div style="width: 100%; height: 100%; position: relative; display: flex; flex-direction: column; justify-content: center; align-items: center; pointer-events:none; font-family: 'Arial Black', sans-serif;">
        
        <div style="width: 120px; height: 60px; border: 1px solid #eee; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); background: #fff;">
             <div style="font-size: 10px; font-weight: 900; color: #000; text-transform: uppercase; text-align: center;">
                TAP TO EDIT
             </div>
        </div>

        <div style="position: absolute; bottom: 5px; width: 100%; text-align: center; opacity: 0.3;">
            <div style="font-size: 5px; font-weight: 900; color: #000;">STYLE 8 • HORIZONTAL</div>
        </div>
        
    </div>
</button>

 </div>

    <div style="font-size: 11px; font-weight: 900; color: #666; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 15px 5px; border-left: 3px solid #ff0000; padding-left: 8px; ">
        Vertical Layouts
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; width: 100%;">
     <button id="layout-style2" onclick="loadTemplate('style2', this)" style="padding:0; cursor:pointer; border:1px solid #ddd; border-radius:12px; background:#000000 !important; height:180px; overflow:hidden; width:100%; display:block; outline:none; position:relative;">
        
        <div style="width: 100%; height: 100%; position: relative; overflow: hidden; background: #000000; pointer-events:none; font-family: 'Arial Black', sans-serif; display: flex; flex-direction: column; align-items: center;">
            
            <div style="width: 100%; height: 22px; background: #ff0000; display: flex; align-items: center; justify-content: center; z-index: 10;">
                <div style="color: #ffffff; font-size: 5px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">
                    ★ ICE COLD REFRESHMENT ★
                </div>
            </div>

            <div style="text-align: center; margin-top: 10px; z-index: 5;">
                <div style="color: #ffffff; font-size: 11px; font-weight: 900; line-height: 1;">
                    CLASSIC
                </div>
                <div style="color: #ff0000; font-size: 7px; font-weight: 900; font-style: italic; line-height: 1.2;">
                    CANDY COKE
                </div>
            </div>

            <div style="margin-top: 10px;">
                <div style="width: 52px; height: 65px; background: #ffffff; border: 1.5px solid #3498db; border-radius: 4px; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
                    <div style="font-size: 8px; margin-bottom: 2px;">📁</div>
                    <div style="font-size: 4px; color: #ccc; font-weight: bold;">PHOTO</div>
                    <div style="position: absolute; bottom: 0; right: 0; width: 5px; height: 5px; background: #3498db;"></div>
                </div>
            </div>

            <div style="text-align: center; margin-top: 8px;">
                <div style="color: #ffff00; font-size: 6px; font-weight: 900; line-height: 1;">
                    ONLY
                </div>
                <div style="color: #ffffff; font-size: 20px; font-weight: 900; line-height: 1;">
                    $2.49
                </div>
            </div>

            <div style="position: absolute; bottom: 6px; width: 100%; text-align: center;">
                <div style="color: #ffffff; font-size: 3.5px; font-weight: 700; letter-spacing: 0.5px; opacity: 0.8;">
                    500ML | SERVE CHILLED | RECYCLE ME
                </div>
            </div>

        </div>
    </button>

        <button id="layout-style3" onclick="loadTemplate('style3', this)" style="padding:0; cursor:pointer; border:1px solid #ddd; border-radius:12px; background:#ff0000 !important; height:180px; overflow:hidden; width:100%; display:block; outline:none; position:relative;">
        <div style="width: 100%; height: 100%; position: relative; overflow: hidden; background: #ff0000; pointer-events:none; font-family: 'Arial Black', sans-serif; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            
            <div style="font-size: 10px; font-weight: 900; color: #ffff00; letter-spacing: 0.5px; margin-bottom: 5px;">HAPPY</div>
            
            <div style="background: #ffffff; color: #ff0000; font-size: 14px; font-weight: 900; padding: 4px 10px; margin: 8px 0; line-height: 1; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                POSITIVE
            </div>
            
            <div style="font-size: 11px; font-weight: 900; color: #ffff00; margin-bottom: 15px; letter-spacing: 1px;">THINKING</div>
            
            <div style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 80%;">
                <div style="width: 25px; height: 1.5px; background: #ffffff; opacity: 0.8;"></div>
                <div style="font-size: 9px; font-weight: 900; color: #ffffff;">DAY</div>
                <div style="width: 25px; height: 1.5px; background: #ffffff; opacity: 0.8;"></div>
            </div>

            <div style="position: absolute; bottom: 10px; width: 100%; text-align: center;">
                <div style="font-size: 4.5px; color: rgba(255,255,255,0.7); font-weight: 900; letter-spacing: 0.5px;">
                    POSITIVE VIBES ONLY • 2026
                </div>
            </div>

        </div>
    </button>
<button id="layout-style7" onclick="loadTemplate('style7', this)" style="padding:0; cursor:pointer; border:1px solid #ddd; border-radius:12px; background:#ffffff !important; height:180px; overflow:hidden; width:100%; display:block; outline:none; position:relative;">
    <div style="width: 100%; height: 100%; position: relative; display: flex; flex-direction: column; justify-content: center; align-items: center; pointer-events:none; font-family: 'Arial Black', sans-serif;">
        
        <div style="width: 60px; height: 120px; border: 1px solid #eee; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); background: #fff;">
             <div style="font-size: 10px; font-weight: 900; color: #000; text-transform: uppercase; text-align: center; line-height: 1.2;">
                TAP TO<br>EDIT
             </div>
        </div>

        <div style="position: absolute; bottom: 5px; width: 100%; text-align: center; opacity: 0.3;">
            <div style="font-size: 5px; font-weight: 900; color: #000;">STYLE 7</div>
        </div>
        
    </div>
</button>

<button id="layout-style9" onclick="loadTemplate('style9', this)" style="padding:0; cursor:pointer; border:1px solid #ddd; border-radius:12px; background:none !important; height:180px; overflow:hidden; width:100%; display:block; outline:none; position:relative; margin-bottom: 10px;">
    <div style="width: 100%; height: 100%; position: absolute; top:0; left:0; background-image: url('https://static.vecteezy.com/system/resources/thumbnails/015/567/763/small/abstract-yellow-background-with-wavy-line-vector.jpg'); background-size: cover; background-position: center;"></div>
    
    <div style="width: 100%; height: 100%; position: relative; display: flex; flex-direction: column; justify-content: center; align-items: center; pointer-events:none; font-family: 'Arial Black', sans-serif; z-index: 2;">
        
        <div style="width: 80%; text-align: center;">
            <div style="color:#0d0d0d; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
                ORGANIC
            </div>
        </div>
        
        <div style="color:#120d0d; font-size: 5px; font-weight: 800; margin-top: 8px; text-transform: uppercase; letter-spacing: 1px;">
            PREMIUM QUALITY
        </div>

        <div style="width: 25px; height: 1px; background: rgba(255,255,255,0.8); margin-top: 10px;"></div>
    </div>
</button>



    </div>
</div>
</div>
 </div>



            <div id="workspaceArea" style="flex:1; background:#f0f2f5; display:flex; justify-content:center; align-items:flex-start; overflow:auto; padding:10px; position: relative;">
    
    <div style="position: absolute; left: 1000px; top: 20px; display: flex; flex-direction: column; align-items: flex-start;">
        <button onclick="showApiEditor()" 
                style="background:#27ae60; color:white; padding:10px 15px; border-radius:10px; border:none; font-weight:bold; cursor:pointer; font-size:12px; white-space:nowrap; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.2s;">
            GENERATE API
        </button>

<button onclick="saveManualToJSON()" style="color: white; border-radius: 6px; border: none; font-weight: 800; cursor: pointer; font-size: 11px;  margin:300px;">
    save
</button>
    </div>


                <div id="canvas21" style="background:#ffffff; position:relative; border: 1px solid #bdc3c7; box-shadow: 0 30px 70px rgba(0,0,0,0.2); overflow: hidden; user-select:none;">
                    
                    <div id="field1" class="draggable text-element" contenteditable="true" style="position:absolute; top:40px; left:40px; font-size:28px; font-weight:800; color:#2c3e50; outline:none; white-space:nowrap; z-index:10;">BUSINESS NAME</div>
                    
                    <div id="field2" class="draggable text-element" contenteditable="true" style="position:absolute; top:80px; left:60px; font-size:18px; font-weight:500; color:#7f8c8d; outline:none; width:300px; line-height:1.4; z-index:10;"></div>

                    <div id="addressField" class="draggable text-element" contenteditable="true" style="position:absolute; bottom:40px; left:40px; font-size:14px; color:#34495e; font-weight:600; display:none; outline:none; background:rgba(255,255,255,0.5); padding:5px; border-radius:4px; z-index:10;">123 Industrial Ave, City</div>

<div id="priceTag" class="draggable" contenteditable="true" style="position:absolute; display:none; z-index:40; cursor:pointer; outline:none; text-align:center; display:flex; align-items:center; justify-content:center;">$0.00</div>


<div id="imageGroup" class="draggable" 
     style="position:absolute; top:40px; left:600px; width:150px; height:150px; border:2px dashed #dcdde1; background:#fcfcfc; display:none; border-radius:12px; overflow:hidden; cursor:move; z-index:5;" 
     onmousedown="startDragging(event)">
    
    <div id="imgPlaceholder" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#bdc3c7; font-size:10px; pointer-events:none;">PHOTO</div>
    
    <img id="userImage" style="width:100%; height:100%; object-fit:cover; display:none; pointer-events:none;" />
    
    <div id="resizer" style="width:14px; height:14px; background:#3498db; position:absolute; bottom:0; right:0; cursor:nwse-resize; z-index:20; border:1px solid white;" onmousedown="startResizing(event)"></div>

    <input type="file" id="galleryInput" style="display:none;" accept="image/*" onchange="handleGalleryUpload(event)">
    
    <div id="uploadBtn" style="position:absolute; top:10px; left:10px; background:white; border-radius:4px; padding:4px; cursor:pointer; font-size:14px; border:1px solid #ddd; z-index:30; line-height:1;" onclick="document.getElementById('galleryInput').click()">📁</div>
</div>

<div id="qrGroup" class="draggable" style="position:absolute; bottom:40px; right:40px; width:100px; height:100px; background:#000; padding:10px; border-radius:12px; display:block;">

    <div style="width:100%; height:100%; background:white; display:flex; align-items:center; justify-content:center;">

        <i class="bi bi-qr-code" style="color: black; font-size: 85px;"></i>

 </div>
</div>
                </div>
            </div>
        </div>

       <div style="padding: 7px 40px; background: #ffffff; border-top: 1px solid #f1f5f9; display: flex; justify-content: center; gap: 15px;">
    
    <button onclick="document.getElementById('templateModal').style.display='none'" 
            style="height: 32px; width: 100px; display: flex; align-items: center; justify-content: center; border-radius: 10px; border: 1.5px solid #e2e8f0; background: white; cursor: pointer; font-weight: 700; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.8px; transition: all 0.2s ease;"
            onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1';"
            onmouseout="this.style.background='white'; this.style.borderColor='#e2e8f0';">
        Discard
    </button>

    <button id="confirmTemplateBtn" 
            style="height: 32px; width: 160px; display: flex; align-items: center; justify-content: center; border-radius: 10px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; border: none; cursor: pointer; font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: 1.2px; transition: all 0.2s ease; box-shadow: 0 4px 10px rgba(15, 23, 42, 0.15);"
            onmouseover="this.style.transform='translateY(-1.5px)'; this.style.boxShadow='0 6px 12px rgba(15, 23, 42, 0.2)';"
            onmouseout="this.style.transform='translateY(0px)'; this.style.boxShadow='0 4px 10px rgba(15, 23, 42, 0.15);'"
            onclick="saveState()">
        Save Design
    </button>

</div>
    </div>
</div>


<input type="hidden" id="hiddenFrameType" name="hiddenFrameType" value="">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// =========================================================================
// 1. GLOBAL VARIABLE DECLARATIONS
// =========================================================================
let currentActiveStyle = 'style1'; // Add this at the very top of your script

let uploadedImage = null; 
let originalImageData = null;
let lastDetectedColorRGBs = {};
let isImageValid = false;
let selectedInchSize = null;
let lastValidatedSrc = "";
let originalImageSource = null;
let originalCanvas = null;
let uploadedImageObject = null; 
let currentRotation = 0;
let isVertical = false;
let isHorizontal = false;
let cropper = null;
let uploadedFileName = "cropped_image.png";
let finalFrameSelection = "";
let persistentFrameSelection = "";
let currentSelectedNodeStatus = ""; // Track current node status
let isSendingImage = false; // Track if image is being sent


let labelPayload = {
  
"style1_2.1":{
    "template_id": "style1_2.1",
    "config": {
        "selectedSize": "2.1",
        "orientation": "horizontal"
    },
    "canvas": {
        "background": "#ff0000"
    },
    "elements": {
        "title": {
            "x": 20,
            "y": 28,
            "fontSize": 14,
            "fontWeight": "700",
            "color": "#000000"
        },
        "subtitle": {
            "x": 20,
            "y": 60,
            "fontSize": 12,
            "fontWeight": "900",
            "color": "#ffd400"
        },
        "offer": {
            "x": 20,
            "y": 95,
            "fontSize": 55,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "offer_symbol": {
            "x" : 120,
            "y" : 100,
            "fontSize": 30,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "offer_type_box": {
            "x": 20,
            "y": 165,
            "width": 75,
            "height": 30,
            "backgroundColor": "#ffffff",
            "borderRadius": 4
        },
        "offer_type_red": {
            "x": 20,
            "y": 172,
            "fontSize": 15,
            "fontWeight": "900",
            "color": "#ff0000"
        },
        "offer_type_white": {
            "x": 105,
            "y": 172,
            "fontSize": 15,
            "fontWeight": "900",
            "color": "#ffffff",
            "backgroundColor": "#ff0000",
            "padding": "0px 4px"
        },
        "logo_box": {
            "x": 300,
            "y": 18,
            "width": 170,
            "height": 45,
            "backgroundColor": "#ffffff",
            "borderColor": "#000000",
            "borderWidth": 2
        },
        "logo_main": {
            "x": 310,
            "y": 30,
            "fontSize": 11,
            "fontWeight": "900",
            "color": "#000000"
        },
        "logo_sub": {
            "x": 320,
            "y": 48,
            "fontSize": 9,
            "fontWeight": "600",
            "color": "#ff0000"
        },
        "date_box": {
            "x": 15,
            "y": 200,
            "width": 150,
            "height": 30,
            "backgroundColor": "#000000"
        },
        "date": {
            "x": 20,
            "y": 205,
            "fontSize": 16,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "qr": {
            "x": 400,
            "y": 155,
            "size": 70
        }
    },
    "data": {
        "title": "NATIONAL BRANDS WEEK",
        "subtitle": "ANYTHING & EVERYTHING",
        "offer": "50",
        "offer_symbol": "%",
        "offer_type_red": "@FLAT",
        "offer_type_white": "OFF",
        "logo_main": "FASHION FACTORY",
        "logo_sub": "BRANDS FOR SALE",
        "date": "22 - 28 JAN",
        "qr_data": "https://fashionfactory.com/offer"
    }
},

"style1_4.2": {
    "template_id": "style1_4.2",
    "config": {
        "selectedSize": "4.2",
        "orientation": "horizontal"
    },
    "canvas": {
        "background": "#ff0000"
    },
    "elements": {
        "title": {
            "x": 40,
            "y": 40,
            "fontSize": 24,
            "fontWeight": "700",
            "color": "#000000"
        },
        "subtitle": {
            "x": 40,
            "y": 110,
            "fontSize": 22,
            "fontWeight": "900",
            "color": "#ffd400"
        },
        "offer": {
            "x": 40,
            "y": 160,
            "fontSize": 120,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "offer_symbol": {
            "x": 260,
            "y": 190,
            "fontSize": 70,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "offer_type_box": {
            "x": 40,
            "y": 320,
            "width": 170,
            "height": 60,
            "backgroundColor": "#ffffff",
            "borderRadius": 8
        },
        "offer_type_red": {
            "x": 40,
            "y": 330,
            "fontSize": 35,
            "fontWeight": "900",
            "color": "#ff0000"
        },
        "offer_type_white": {
            "x": 220,
            "y": 330,
            "fontSize": 35,
            "fontWeight": "900",
            "color": "#ffffff",
            "backgroundColor": "#ff0000",
            "padding": "0px 8px"
        },
        "logo_box": {
            "x": 510,
            "y": 40,
            "width": 240,
            "height": 80,
            "backgroundColor": "#ffffff",
            "borderColor": "#000000",
            "borderWidth": 3
        },
        "logo_main": {
            "x": 520,
            "y": 65,
            "fontSize": 16,
            "fontWeight": "900",
            "color": "#000000"
        },
        "logo_sub": {
            "x": 535,
            "y": 90,
            "fontSize": 14,
            "fontWeight": "600",
            "color": "#ff0000"
        },
        "date_box": {
            "x": 40,
            "y": 470,
            "width": 260,
            "height": 65,
            "backgroundColor": "#000000"
        },
        "date": {
            "x": 55,
            "y": 480,
            "fontSize": 30,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "qr": {
            "x": 650,
            "y": 450,
            "size": 100
        }
    },
    "data": {
        "title": "NATIONAL BRANDS WEEK",
        "subtitle": "ANYTHING & EVERYTHING",
        "offer": "50",
        "offer_symbol": "%",
        "offer_type_red": "@FLAT",
        "offer_type_white": "OFF",
        "logo_main": "FASHION FACTORY",
        "logo_sub": "BRANDS FOR SALE",
        "date": "22 - 28 JAN",
        "qr_data": "https://fashionfactory.com/offer"
    }
},

"style1" : {
    "template_id": "style1_2.9",
    "config": {
        "selectedSize": "2.9",
        "orientation": "horizontal"
    },
    "canvas": {
        "background": "#ff0000"
    },
    "elements": {
        "title": { "x": 30, "y": 20, "fontSize": 16, "fontWeight": "700", "color": "#000000" },
        "subtitle": { "x": 30, "y": 65, "fontSize": 14, "fontWeight": "900", "color": "#ffd400" },
        "offer": { "x": 30, "y": 90, "fontSize": 65, "fontWeight": "900", "color": "#ffffff" },
        "offer_symbol": { "x": 170, "y": 105, "fontSize": 40, "fontWeight": "900", "color": "#ffffff" },
        "offer_type_box": {
            "x": 30,
            "y": 165,
            "width": 110,
            "height": 35,
            "backgroundColor": "#ffffff",
            "borderRadius": 4
        },
        "offer_type_red": {
            "x": 35,
            "y": 172,
            "fontSize": 20,
            "fontWeight": "900",
            "color": "#ff0000"
        },
        "offer_type_white": {
            "x": 145,
            "y": 172,
            "fontSize": 20,
            "fontWeight": "900",
            "color": "#ffffff",
            "backgroundColor": "#ff0000",
            "padding": "0px 6px"
        },
        "logo_box": { "x": 380, "y": 20, "width": 190, "height": 50, "backgroundColor": "#ffffff", "borderColor": "#000000", "borderWidth": 2 },
        "logo_main": { "x": 395, "y": 38, "fontSize": 12, "fontWeight": "900", "color": "#000000" },
        "logo_sub": { "x": 400, "y": 52, "fontSize": 10, "fontWeight": "600", "color": "#ff0000" },
        "date_box": { "x": 25, "y": 210, "width": 170, "height": 35, "backgroundColor": "#000000" },
        "date": { "x": 35, "y": 215, "fontSize": 18, "fontWeight": "900", "color": "#ffffff" },
        "qr": { "x": 460, "y": 110, "size": 110 }
    },
    "data": {
        "title": "NATIONAL BRANDS WEEK",
        "subtitle": "ANYTHING & EVERYTHING",
        "offer": "50",
        "offer_symbol": "%",
        "offer_type_red": "@FLAT",
        "offer_type_white": "OFF",
        "logo_main": "FASHION FACTORY",
        "logo_sub": "BRANDS FOR SALE",
        "date": "22 - 28 JAN",
        "qr_data": "https://fashionfactory.com/offer"
    }
},





"style2_2.1": {
    "template_id": "style2_2.1",
    "config": {
        "selectedSize": "2.1",
        "orientation": "vertical"
    },
    "canvas": {
        "background": "#000000"
    },
    "elements": {
        "top_bar_box": {
            "x": 0,
            "y": 0,
            "width": 250,
            "height": 50,
            "backgroundColor": "#ff0000"
        },
        "top_tag": {
            "x": 20,
            "y": 20,
            "width": 250,
            "fontSize": 15,
            "fontWeight": "900",
            "color": "#ffffff",
            "textAlign": "center"
        },
        "classic_text": {
            "x": 20,
            "y": 75,
            "fontSize": 34,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "brand_name": {
            "x": 20,
            "y": 120,
            "fontSize": 22,
            "fontWeight": "900",
            "color": "#ff0000"
        },
        "image_box": {
            "x": 45,
            "y": 165,
            "width": 160,
            "height": 160,
            "backgroundColor": "#ffffff",
            "borderColor": "#ff0000",
            "borderWidth": 3
        },
        "only_tag": {
            "x": 80,
            "y": 345,
            "fontSize": 20,
            "fontWeight": "900",
            "color": "#ffff00"
        },
        "price_val": {
            "x": 10,
            "y": 365,
            "fontSize": 65,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "footer": {
            "x": 10,
            "y": 455,
            "fontSize": 11,
            "fontWeight": "700",
            "color": "white"
        }
    },
    "data": {
        "top_tag": "★ ICE COLD REFRESHMENT ★",
        "classic_text": "CLASSIC",
        "brand_name": "CANDY COKE",
        "only_tag": "ONLY",
        "price_val": "$2.49",
        "footer": "500ML | SERVE CHILLED | RECYCLE ME"
    }
},

"style2_2.9":{
    "template_id": "style2_2.9",
    "config": {
        "selectedSize": "2.9",
        "orientation": "vertical"
    },
    "canvas": {
        "background": "#000000"
    },
    "elements": {
        "top_bar_box": {
            "x": 0,
            "y": 0,
            "width": 250,
            "height": 65,
            "backgroundColor": "#ff0000"
        },
        "top_tag": {
            "x": 20,
            "y": 20,
            "fontSize": 15,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "classic_text": {
            "x": 20,
            "y": 90,
            "fontSize": 45,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "brand_name": {
            "x": 20,
            "y": 145,
            "fontSize": 28,
            "fontWeight": "900",
            "color": "#ff0000"
        },
        "image_box": {
            "x": 30,
            "y": 200,
            "width": 200,
            "height": 200,
            "backgroundColor": "#ffffff",
            "borderColor": "#ff0000",
            "borderWidth": 4
        },
        "only_tag": {
            "x": 90,
            "y": 420,
            "fontSize": 18,
            "fontWeight": "900",
            "color": "#ffff00"
        },
        "price_val": {
            "x": 20,
            "y": 450,
            "fontSize": 55,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "footer": {
            "x": 20,
            "y": 550,
            "fontSize": 10,
            "fontWeight": "700",
            "color": "#ffffff"
        }
    },
    "data": {
        "top_tag": "★ ICE COLD REFRESHMENT ★",
        "classic_text": "CLASSIC",
        "brand_name": "CANDY COKE",
        "only_tag": "ONLY",
        "price_val": "$2.49",
        "footer": "500ML | SERVE CHILLED | RECYCLE ME"
    }
},

"style2": {
    "template_id": "style2_4.2",
    "config": {
        "selectedSize": "4.2",
        "orientation": "vertical"
    },
    "canvas": {
        "background": "#000000"
    },
    "elements": {
        "top_bar_box": {
            "x": 0,
            "y": 0,
            "width": 600,
            "height": 90,
            "backgroundColor": "#ff0000"
        },
        "top_tag": {
            "x": 20,
            "y": 20,
            "fontSize": 35,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "classic_text": {
            "x": 100,
            "y": 130,
            "fontSize": 60,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "brand_name": {
            "x": 100,
            "y": 210,
            "fontSize": 40,
            "fontWeight": "900",
            "color": "#ff0000"
        },
        "image_box": {
            "x": 130,
            "y": 260,
            "width": 330,
            "height": 330,
            "backgroundColor": "#ffffff",
            "borderColor": "#ff0000",
            "borderWidth": 5
        },
        "only_tag": {
            "x": 200,
            "y": 610,
            "fontSize": 27,
            "fontWeight": "900",
            "color": "#ffff00"
        },
        "price_val": {
            "x": 100,
            "y": 630,
            "fontSize": 70,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "footer": {
            "x": 30,
            "y": 770,
            "fontSize": 18,
            "fontWeight": "700",
            "color": "#ffffff"
        }
    },
    "data": {
        "top_tag": "★ ICE COLD REFRESHMENT ★",
        "classic_text": "CLASSIC",
        "brand_name": "CANDY COKE",
        "only_tag": "ONLY",
        "price_val": "$2.49",
        "footer": "500ML | SERVE CHILLED | RECYCLE ME"
    }
},
 


"style3_2.1":{
    "template_id": "style3_2.1",
    "config": {
        "selectedSize": "2.1",
        "orientation": "vertical"
    },
    "canvas": {
        "background": "#ff0000"
    },
    "elements": {
        "happy": {
            "x": 40,
            "y": 120,
            "fontSize": 35,
            "fontWeight": "900",
            "color": "#ffff00"
        },
        "positive_box": {
            "x": 20,
            "y": 180,
            "width": 210,
            "height": 50,
            "backgroundColor": "#ffffff",
            "borderRadius": 4
        },
        "positive": {
            "x": 40,
            "y": 188,
            "fontSize": 32,
            "fontWeight": "900",
            "color": "#ff0000"
        },
        "thinking": {
            "x": 40,
            "y": 245,
            "fontSize": 28,
            "fontWeight": "900",
            "color": "#ffff00"
        },
        "day": {
            "x": 70,
            "y": 300,
            "fontSize": 35,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "line_left": {
            "x": 30,
            "y": 312,
            "width": 35,
            "height": 4,
            "color": "#ffffff"
        },
        "line_right": {
            "x": 180,
            "y": 312,
            "width": 35,
            "height": 4,
            "color": "#ffffff"
        }
    },
    "data": {
        "happy": "HAPPY",
        "positive": "POSITIVE",
        "thinking": "THINKING",
        "day": "DAY"
    }
},
    
"style3_2.9": {
        "template_id": "style3_2.9",
        "config": { "selectedSize": "2.9", "orientation": "vertical" },
        "canvas": { "background": "#ff0000" },
        "elements": {
            "happy": { "x": 40, "y": 150, "fontSize": 45, "fontWeight": "900", "color": "#ffff00" },
            "positive_box": { "x": 10, "y": 210, "width": 230, "height": 65, "backgroundColor": "#ffffff", "borderRadius": 6 },
            "positive": { 
                "x": 28, 
                "y": 222, 
                "fontSize": 40, 
                "fontWeight": "900", 
                "color": "#ff0000" 
            },
            "thinking": { "x": 42, "y": 290, "fontSize": 36, "fontWeight": "900", "color": "#ffff00" },
            "day": { "x": 70, "y": 350, "fontSize": 40, "fontWeight": "900", "color": "#ffffff" },
            "line_left": { "x": 10, "y": 370, "width": 50, "height": 5, "color": "#ffffff" },
            "line_right": { "x": 195, "y": 370, "width": 50, "height": 5, "color": "#ffffff" }
        },
        "data": { "happy": "HAPPY", "positive": "POSITIVE", "thinking": "THINKING", "day": "DAY" }
    }
,

    "style3": {
    "template_id": "style3_4.2",
    "config": {
        "selectedSize": "4.2",
        "orientation": "vertical"
    },
    "canvas": {
        "background": "#ff0000"
    },
    "elements": {
        "happy": {
            "x": 120,
            "y": 160,
            "fontSize": 70,
            "fontWeight": "900",
            "color": "#ffff00"
        },
        "positive_box": {
            "x": 80,
            "y": 260,
            "width": 420,
            "height": 100,
            "backgroundColor": "#ffffff",
            "borderRadius": 10
        },
        "positive": {
            "x": 115,
            "y": 280,
            "fontSize": 50,
            "fontWeight": "900",
            "color": "#ff0000"
        },
        "thinking": {
            "x": 90,
            "y": 400,
            "fontSize": 55,
            "fontWeight": "900",
            "color": "#ffff00"
        },
        "day": {
            "x": 180,
            "y": 520,
            "fontSize": 70,
            "fontWeight": "900",
            "color": "#ffffff"
        },
        "line_left": {
            "x": 110,
            "y": 555,
            "width": 60,
            "height": 6,
            "color": "#ffffff"
        },
        "line_right": {
            "x": 380,
            "y": 555,
            "width": 60,
            "height": 6,
            "color": "#ffffff"
        }
    },
    "data": {
        "happy": "HAPPY",
        "positive": "POSITIVE",
        "thinking": "THINKING",
        "day": "DAY"
    }
},






"style4_2.1": {
        "template_id": "style4_2.1",
        "config": {
            "selectedSize": "2.1",
            "orientation": "horizontal"
        },
        "canvas": {
            "background": "#ffffff"
        },
        "elements": {
            "brand": { "x": 70, "y": 90, "fontSize": 70, "fontWeight": "900", "color": "#ff0000" },
            "brand_suffix": { "x": 400, "y": 65, "fontSize": 16, "color": "#ff0000" },
            "tagline": { "x": 70, "y": 185, "fontSize": 14, "fontWeight": "700", "color": "#000000" },
            "line_left": { "x": 10, "y": 190, "width": 50, "height": 2, "color": "#000000" },
            "line_right": { "x": 425, "y": 190, "width": 50, "height": 2, "color": "#000000" }
        },
        "data": {
            "brand_name": "DHRIO",
            "brand_suffix": "TM",
            "tagline_text": "CONNECT - CONTROL - COMFORT"
        }
    },
    "style4_2.9": {
        "template_id": "style4_2.9",
        "config": {
            "selectedSize": "2.9",
            "orientation": "horizontal"
        },
        "canvas": {
            "background": "#ffffff"
        },
        "elements": {
            "brand": { "x": 60, "y": 70, "fontSize": 100, "fontWeight": "900", "color": "#ff0000" },
            "brand_suffix": { "x": 500, "y": 30, "fontSize": 20, "color": "#ff0000" },
            "tagline": { "x": 70, "y": 185, "fontSize": 19, "fontWeight": "700", "color": "#000000" },
            "line_left": { "x": 5, "y": 190, "width": 55, "height": 3, "color": "#000000" },
            "line_right": { "x": 535, "y":190, "width": 55, "height": 3, "color": "#000000" }
        },
        "data": {
            "brand_name": "DHRIO",
            "brand_suffix": "TM",
            "tagline_text": "CONNECT - CONTROL - COMFORT"
        }
    },
    "style4":{
    "template_id": "style4_4.2",
    "config": {
        "selectedSize": "4.2",
        "orientation": "horizontal"
    },
    "canvas": {
        "background": "#ffffff"
    },
    "elements": {
        "brand": {
            "x": 80,
            "y": 200,
            "fontSize": 180,
            "fontWeight": "900",
            "color": "#ff0000"
        },
        "brand_suffix": {
            "x": 675,
            "y": 135,
            "fontSize": 22,
            "color": "#ff0000"
        },
        "tagline": {
            "x": 125,
            "y": 380,
            "fontSize": 24,
            "fontWeight": "700",
            "color": "#000000"
        },
        "line_left": {
            "x": 20,
            "y": 392,
            "width": 70,
            "height": 4,
            "color": "#000000"
        },
        "line_right": {
            "x": 695,
            "y": 392,
            "width": 70,
            "height": 4,
            "color": "#000000"
        }
    },
    "data": {
        "brand_name": "DHRIO",
        "brand_suffix": "TM",
        "tagline_text": "CONNECT - CONTROL - COMFORT"
    }
}, 


"style5_2.1": {
    "template_id": "style5_2.1",
    "config": {
        "selectedSize": "2.1",
        "orientation": "horizontal"
    },
    "canvas": {
        "background": "transparent"
    },
    "elements": {
        "bg_image": {
            "type": "image",
            "x": 0,
            "y": 0,
            "width": 500,
            "height": 250
        },
        "main_title": {
            "x": 50,
            "y": 80,
            "width": 400,
            "fontSize": 45,
            "color": "#ffffff",
            "fontWeight": "900"
        },
        "tagline": {
            "x": 50,
            "y": 150,
            "width": 400,
            "fontSize": 16,
            "color": "#f8f9fa",
            "fontWeight": "600"
        },
        "est_date": {
            "x": 150,
            "y": 200,
            "width": 200,
            "fontSize": 12,
            "color": "#ffffff",
            "fontWeight": "700"
        }
    },
    "data": {
        "bg_image": "https://plus.unsplash.com/premium_photo-1664302769311-95bbc931bee5?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8cmVkJTIwbmF0dXJlfGVufDB8fDB8fHww",
        "main_title": "NATURE PRESERVE",
        "tagline": "Protecting our forests for future generations",
        "est_date": "ESTABLISHED 2026"
    }
},
"style5_2.9": {
    "template_id": "style5_2.9",
    "config": {
        "selectedSize": "2.9",
        "orientation": "horizontal"
    },
    "canvas": {
        "background": "transparent"
    },
    "elements": {
        "bg_image": {
            "type": "image",
            "x": 0,
            "y": 0,
            "width": 700,
            "height": 350
        },
        "main_title": {
            "x": 70,
            "y": 80,
            "width": 560,
            "fontSize": 53,
            "color": "#ffffff",
            "fontWeight": "900"
        },
        "tagline": {
            "x": 70,
            "y": 130,
            "width": 560,
            "fontSize": 22,
            "color": "#f8f9fa",
            "fontWeight": "600"
        },
        "est_date": {
            "x": 170,
            "y": 180,
            "width": 280,
            "fontSize": 17,
            "color": "#ffffff",
            "fontWeight": "700"
        }
    },
    "data": {
        "bg_image": "https://plus.unsplash.com/premium_photo-1664302769311-95bbc931bee5?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8cmVkJTIwbmF0dXJlfGVufDB8fDB8fHww",
        "main_title": "NATURE PRESERVE",
        "tagline": "Protecting our forests for future generations",
        "est_date": "ESTABLISHED 2026"
    }
},

"style5": {
    "template_id": "style5_4.2",
    "config": {
        "selectedSize": "4.2",
        "orientation": "horizontal"
    },
    "canvas": {
        "background": "transparent"
    },
    "elements": {
        "bg_image": {
            "type": "image",
            "x": 0,
            "y": 0,
            "width": 1000,
            "height": 500
        },
        "main_title": {
            "x": 100,
            "y": 160,
            "width": 800,
            "fontSize": 70,
            "color": "#ffffff",
            "fontWeight": "900"
        },
        "tagline": {
            "x": 100,
            "y": 250,
            "width": 800,
            "fontSize": 32,
            "color": "#f8f9fa",
            "fontWeight": "600"
        },
        "est_date": {
            "x": 250,
            "y": 350,
            "width": 400,
            "fontSize": 24,
            "color": "#ffffff",
            "fontWeight": "700"
        }
    },
    "data": {
        "bg_image": "https://plus.unsplash.com/premium_photo-1664302769311-95bbc931bee5?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8cmVkJTIwbmF0dXJlfGVufDB8fDB8fHww",
        "main_title": "NATURE PRESERVE",
        "tagline": "Protecting our forests for future generations",
        "est_date": "ESTABLISHED 2026"
    }
},


"style6_2.1": {
    "template_id": "style6_2.1",
    "config": {
        "selectedSize": "2.1",
        "orientation": "horizontal"
    },
    "canvas": { 
        "background": "#000000" 
    },
    "elements": {
        "title": { 
            "x": 90, 
            "y": 70, 
            "fontSize": 25, 
            "textColor": "#ffffff",
            "backgroundColor": "transparent"
        },
        "price_box": { 
            "x": 90, 
            "y": 115, 
            "width": 260, 
            "height": 60, 
            "backgroundColor": "#ff0000",
            "borderRadius": 4
        },
        "price_tag": { 
            "x": 90, 
            "y": 115, 
            "fontSize": 42, 
            "color": "#ffffff"
        },
        "footer": { 
            "x": 50, 
            "y": 205, 
            "fontSize": 14, 
            "textColor": "#ffffff",
            "backgroundColor": "transparent"
        }
    },
    "data": {
        "title": "IOT CORE PRO",
        "price": "$129.99",
        "footer": "SECURE • CONNECT • AUTOMATE"
    }
},

"style6_2.9":{
    "template_id": "style6_2.9",
    "config": { "selectedSize": "2.9", "orientation": "horizontal" },
    "canvas": { "background": "#000000" },
    "elements": {
        "title": { "x": 130, "y": 60, "fontSize": 35, "textColor": "#ffffff", "backgroundColor": "transparent" },
        "price_box": { "x": 130, "y": 110, "width": 340, "height": 75, "backgroundColor": "#ff0000", "borderRadius": 6 },
        "price_tag": { "x": 130, "y": 110, "fontSize": 52, "color": "#ffffff" },
        "footer": { "x": 65, "y": 200, "fontSize": 18, "textColor": "#ffffff", "backgroundColor": "transparent" }
    },
    "data": {
        "title": "IOT CORE PRO", "price": "$129.99", "footer": "SECURE • CONNECT • AUTOMATE"
    }
},

"style6":{
    "template_id": "style6_4.2",
    "config": { "selectedSize": "4.2", "orientation": "horizontal" },
    "canvas": { "background": "#000000" },
    "elements": {
        "title": { "x": 175, "y": 100, "fontSize": 50, "textColor": "#ffffff", "backgroundColor": "transparent" },
        "price_box": { "x": 175, "y": 180, "width": 470, "height": 110, "backgroundColor": "#ff0000", "borderRadius": 10 },
        "price_tag": { "x": 175, "y": 180, "fontSize": 78, "color": "#ffffff" },
        "footer": { "x": 90, "y": 340, "fontSize": 26, "textColor": "#ffffff", "backgroundColor": "transparent" }
    },
    "data": {
        "title": "IOT CORE PRO", "price": "$129.99", "footer": "SECURE • CONNECT • AUTOMATE"
    }
},

"style7_2.1": {
    "template_id": "style7_2.1",
    "config": {
        "selectedSize": "2.1",
        "orientation": "vertical"
    },
    "canvas": {
        "background": "#ffffff"
    },
    "elements": {
        "title": {
            "x": 40,
            "y": 230,
            "fontSize": 25,
            "fontWeight": "900",
            "color": "#000000"
        }
    },
    "data": {
        "title": "TAP TO EDIT"
    }
},

"style7_2.9": {
    "template_id": "style7_2.9",
    "config": {
        "selectedSize": "2.9",
        "orientation": "vertical"
    },
    "canvas": {
        "background": "#ffffff"
    },
    "elements": {
        "title": {
            "x": 40,
            "y": 270,
            "fontSize": 30,
            "fontWeight": "900",
            "color": "#000000"
        }
    },
    "data": {
        "title": "TAP TO EDIT"
    }
},

"style7": {
    "template_id": "style7_4.2",
    "config": {
        "selectedSize": "4.2",
        "orientation": "vertical"
    },
    "canvas": {
        "background": "#ffffff"
    },
    "elements": {
        "title": {
            "x": 70,
            "y": 380,
            "fontSize": 50,
            "fontWeight": "900",
            "color": "#000000"
        }
    },
    "data": {
        "title": "TAP TO EDIT"
    }
},

"style8_2.1":{
    "template_id": "style8_2.1",
    "config": {
        "selectedSize": "2.1",
        "orientation": "horizontal"
    },
    "canvas": {
        "background": "#ffffff"
    },
    "elements": {
        "title": {
            "fontSize": 35,
            "fontWeight": "900",
            "color": "#000000",
            "y": 105,
            "x" : 70
        }
    },
    "data": {
        "title": "TAP TO EDIT"
    }
},

"style8_2.9":{
    "template_id": "style8_2.9",
    "config": {
        "selectedSize": "2.9",
        "orientation": "horizontal"
    },
    "canvas": {
        "background": "#ffffff"
    },
    "elements": {
        "title": {
            "fontSize": 40,
            "fontWeight": "900",
            "color": "#000000",
            "y": 105,
            "x" : 90
        }
    },
    "data": {
        "title": "TAP TO EDIT"
    }
},

"style8": {
    "template_id": "style8_4.2",
    "config": {
        "selectedSize": "4.2",
        "orientation": "horizontal"
    },
    "canvas": {
        "background": "#ffffff"
    },
    "elements": {
        "title": {
            "fontSize": 60,
            "fontWeight": "900",
            "color": "#000000",
            "y": 270,
            "x" : 130
        }
    },
    "data": {
        "title": "TAP TO EDIT"
    }
},


"style9_2.1": {
    "template_id": "style9_2.1",
    "config": { "selectedSize": "2.1", "orientation": "vertical" },
    "canvas": { "background": "transparent" },
    "elements": {
        "main_title": { "fontSize": 20, "color": "#0d0d0d","fontWeight": "900", "x": 25, "y": 160 },
        "tagline": { "fontSize": 10, "color": "#120d0d", "fontWeight": "700", "x": 15, "y": 200 },
        "est_date": { "fontSize": 14,   "color": "#161313", "fontWeight": "800", "x": 25, "y": 240 }
    },
    "data": {
        "bg_image": "https://static.vecteezy.com/system/resources/thumbnails/015/567/763/small/abstract-yellow-background-with-wavy-line-vector.jpg",
        "main_title": "ORGANIC BLEND",
        "tagline": "PREMIUM QUALITY OVER QUANTITY",
        "est_date": "ESTABLISHED 2026"
    }
},
"style9_2.9": {
    "template_id": "style9_2.9",
    "config": {
        "selectedSize": "2.9",
        "orientation": "vertical"
    },
    "canvas": {
        "background": "transparent"
    },
    "elements": {
        "main_title": {
            "fontSize": 22,
             "color": "#0d0d0d",
            "fontWeight": "900",
            "x": 10,
            "y": 180
        },
        "tagline": {
            "fontSize": 12,
           "color": "#120d0d",
            "fontWeight": "700",
            "x": 10,
            "y": 230
        },
        "est_date": {
            "fontSize": 14,
            "color": "#161313",
            "fontWeight": "800",
            "x": 10,
            "y": 280
        }
    },
    "data": {
        "bg_image": "https://static.vecteezy.com/system/resources/thumbnails/015/567/763/small/abstract-yellow-background-with-wavy-line-vector.jpg",
        "main_title": "ORGANIC BLEND",
        "tagline": "PREMIUM QUALITY OVER QUANTITY",
        "est_date": "ESTABLISHED 2026"
    }
},
"style9": {
    "template_id": "style9_4.2",
    "config": {
        "selectedSize": "4.2",
        "orientation": "vertical"
    },
    "canvas": {
        "background": "transparent"
    },
    "elements": {
        "main_title": {
            "fontSize": 45,
            "color": "#0d0d0d",
            "fontWeight": "900",
            "x": 50,
            "y": 250
        },
        "tagline": {
            "fontSize": 18,
             "color": "#120d0d",
            "fontWeight": "700",
            "x": 50,
            "y": 350
        },
        "est_date": {
            "fontSize": 20,
             "color": "#161313",
            "fontWeight": "800",
            "x": 140,
            "y": 450
        }
    },
    "data": {
        "bg_image": "https://static.vecteezy.com/system/resources/thumbnails/015/567/763/small/abstract-yellow-background-with-wavy-line-vector.jpg",
        "main_title": "ORGANIC BLEND",
        "tagline": "PREMIUM QUALITY OVER QUANTITY",
        "est_date": "ESTABLISHED 2026"
    }
},

};




const COLOR_TOLERANCE = 100;

const TARGET_COLORS = {
    "White": [255, 255, 255],
    "Yellow": [255, 255, 0],
    "Black": [0, 0, 0],
    "Red": [255, 0, 0],
};

const LIGHT_INVALID_COLORS = {
    "Light Gray": [230, 240, 240], "Pale Pink": [255, 223, 230],
    "Light Cyan": [224, 255, 255], "Light Orange": [255, 220, 190],
    "Light Teal": [150, 200, 200], "blue":[135,206,235],
};

const DARK_INVALID_COLORS = {
    "Dark Gray": [105, 105, 105], "Dark Purple": [150, 0, 199],
    "Dark Teal": [48, 117, 117], "Dark Blue": [0, 0, 139],
    "Brown": [165, 42, 42],"orange":[255,165,0],
};

const ALL_NAMED_COLORS = {
    ...TARGET_COLORS, ...LIGHT_INVALID_COLORS, ...DARK_INVALID_COLORS,
    "Unknown Color": [0,0,0]
};

const sizeRequirements = {
    "2.1": { width: 250, height: 500, text: "250 × 500 px" },
    "2.9": { width: 250, height: 592, text: "250 × 592 px" },
    "4.2": { width: 800, height: 600, text: "800 × 600 px" }
};

//
// Modal
$("#openProductModal").click(() => $("#productModal").fadeIn());
$(".close").click(() => $("#productModal").fadeOut());
$(window).click(e => { if($(e.target).is("#productModal")) $("#productModal").fadeOut(); });
// =========================================================================
// 2. CORE HELPER FUNCTIONS (HSV ENGINE)
// =========================================================================

// NEW: RGB to HSV helper
function rgbToHsv(r, g, b) {
    r /= 255; g /= 255; b /= 255;
    const max = Math.max(r, g, b), min = Math.min(r, g, b);
    const delta = max - min;
    let h = 0;
    if (delta !== 0) {
        if (max === r) h = 60 * (((g - b) / delta) % 6);
        else if (max === g) h = 60 * (((b - r) / delta) + 2);
        else h = 60 * (((r - g) / delta) + 4);
    }
    if (h < 0) h += 360;
    const s = (max === 0) ? 0 : (delta / max);
    const v = max;
    return [h, s, v];
}

// Updated to use HSV logic for better accuracy
function getClosestColorInfo(r, g, b) {
    const [h, s, v] = rgbToHsv(r, g, b);
    let name = "Unknown Color";

    // Black/White Detection
    if (v < 0.15) name = "Black";
    else if (s < 0.15 && v > 0.85) name = "White";
    // Yellow Detection (Hue 45-65)
    else if (h >= 45 && h <= 65 && s > 0.4) name = "Yellow";
    // Red Detection (Hue 0-10 or 350-360)
    else if (h <= 10 || h >= 350) name = "Red";
    // Brown/Orange Detection (Hue 11-44)
    else if (h > 10 && h < 45) {
        name = (v < 0.7) ? "Brown" : "orange";
    }
    // Blue Detection
    else if (h >= 180 && h <= 240) name = "blue";
    // Fallback for others
    else if (s < 0.2) name = "Light Gray";

    return { [name]: [r, g, b] };
}

function colorDistance(r1, g1, b1, r2, g2, b2) {
    return Math.sqrt((r1 - r2)**2 + (g1 - g2)**2 + (b1 - b2)**2);
}

function isAllowedColor(r, g, b) {
    // Check if the HSV-identified color is in our target list
    const info = getClosestColorInfo(r, g, b);
    const name = Object.keys(info)[0];
    return TARGET_COLORS.hasOwnProperty(name);
}

function checkSizeAgainstRequirement() {
    const preview = document.getElementById('imagePreview');
    if (!selectedInchSize || !preview.src) return false;
    const req = sizeRequirements[selectedInchSize];
    if (preview.naturalWidth !== req.width || preview.naturalHeight !== req.height) {
        
        alert(`ℹ️ Size must be ${req.text}. Got ${preview.naturalWidth}x${preview.naturalHeight}`);

        return false;
    }
    return true;
}

// Function to update save button based on node status
function updateSaveButtonStatus(status) {
    const saveBtn = document.getElementById('saveBtn');
    const statusMessage = document.getElementById('statusMessage');
    const statusMessageText = document.getElementById('statusMessageText');
    
    if (saveBtn && statusMessage && statusMessageText) {
        if (status === 'online') {
            // Node is online - enable save button
            saveBtn.disabled = false;
            saveBtn.style.backgroundColor = '#28a745';
            saveBtn.title = "Send Image (Node is Online)";
            
            // Show success message
            statusMessage.className = 'status-message online';
            statusMessage.style.display = 'block';
            statusMessageText.innerHTML = '<i class="bi bi-check-circle"></i> Node is ONLINE. You can send the image.';
            
            // Hide message after 5 seconds
            setTimeout(() => {
                statusMessage.style.display = 'none';
            }, 5000);
            
        } else if (status === 'offline') {
            // Node is offline - disable save button
            saveBtn.disabled = true;
            saveBtn.style.backgroundColor = '#6c757d';
            saveBtn.title = "Cannot send: Node is offline";
            
            // Show error message
            //statusMessage.className = 'status-message offline';
           // statusMessage.style.display = 'block';
           // statusMessageText.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Node is OFFLINE. Cannot send image. Please select an online node.';
            
        } else {
            // No node selected or invalid status
            saveBtn.disabled = true;
            saveBtn.style.backgroundColor = '#6c757d';
            saveBtn.title = "Please select a node first";
            
            statusMessage.className = 'status-message';
            statusMessage.style.display = 'block';
            statusMessageText.innerHTML = '<i class="bi bi-info-circle"></i> Please select a node to proceed';
        }
    }
}

// =========================================================================
// 3. MANUAL REFRESH FUNCTIONS
// =========================================================================

function refreshPage() {
    // Don't refresh if we're in the middle of sending an image
    if (isSendingImage) {
        alert("⚠️ Cannot refresh while sending image. Please wait for the operation to complete.");
        return;
    }
    
    // Don't refresh if modal is open
    if (document.getElementById('colorMismatchModal').style.display === 'block' ||
        document.getElementById('productModal').style.display === 'block') {
        alert("⚠️ Cannot refresh while modal is open. Please close the modal first.");
        return;
    }
    
    // Don't refresh if image is being uploaded/processed
    if (document.getElementById('imageUpload').files.length > 0 && !isImageValid) {
        if (!confirm("⚠️ Image processing is in progress. Refreshing will lose unsaved changes. Continue?")) {
            return;
        }
    }
    
    // Save current selections to localStorage to restore after refresh
    const currentState = {
        nodeSelect: document.getElementById('nodeSelect').value,
        inchSize: selectedInchSize,
        frameType: document.getElementById('frameType').value
    };
    
    localStorage.setItem('labelDesignerState', JSON.stringify(currentState));
    
    // Show loading state on refresh button
    const refreshBtn = document.getElementById('refreshPageBtn');
    if (refreshBtn) {
        refreshBtn.disabled = true;
        refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Refreshing...';
    }
    
    // Refresh the page after a short delay to show the loading state
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

function restoreStateAfterRefresh() {
    const savedState = localStorage.getItem('labelDesignerState');
    if (savedState) {
        try {
            const state = JSON.parse(savedState);
            
            // Restore node selection
            if (state.nodeSelect) {
                const nodeSelect = document.getElementById('nodeSelect');
                nodeSelect.value = state.nodeSelect;
                if (state.nodeSelect) {
                    // Trigger change event to update UI
                    const event = new Event('change');
                    nodeSelect.dispatchEvent(event);
                }
            }
            
            // Restore inch size
            if (state.inchSize) {
                const inchSize = document.getElementById('inchSize');
                inchSize.value = state.inchSize;
                selectedInchSize = state.inchSize;
                if (state.inchSize) {
                    const event = new Event('change');
                    inchSize.dispatchEvent(event);
                }
            }
            
            // Restore frame type
            if (state.frameType) {
                document.getElementById('frameType').value = state.frameType;
                persistentFrameSelection = state.frameType;
            }
            
            // Clear the saved state
            localStorage.removeItem('labelDesignerState');
            
        } catch (e) {
            console.error("Error restoring state:", e);
        }
    }
}

// =========================================================================
// 4. IMAGE PROCESSING FUNCTIONS
// =========================================================================

function applyColorMapping(mappings, colorDefinitions) {
    const preview = document.getElementById('imagePreview');
    if (!preview || !preview.src) return;

    const canvas = document.createElement('canvas');
    canvas.width = preview.naturalWidth;
    canvas.height = preview.naturalHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(preview, 0, 0);

    let imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    let data = imageData.data;

    for (let i = 0; i < data.length; i += 4) {
        if (data[i + 3] < 50) continue; 

        const r = data[i];
        const g = data[i + 1];
        const b = data[i + 2];

        // Identify the pixel's color name using the new HSV engine
        const info = getClosestColorInfo(r, g, b);
        const detectedColorName = Object.keys(info)[0];

        if (detectedColorName && mappings[detectedColorName]) {
            const targetColorKey = mappings[detectedColorName];
            const targetRGB = TARGET_COLORS[targetColorKey];

            if (targetRGB) {
                data[i]     = targetRGB[0];
                data[i + 1] = targetRGB[1];
                data[i + 2] = targetRGB[2];
                data[i + 3] = 255;
            }
        }
    }

    ctx.putImageData(imageData, 0, 0);
    preview.src = canvas.toDataURL('image/png');
    isImageValid = true;
    lastValidatedSrc = preview.src;
    
    // Only enable save button if node is online
    updateSaveButtonStatus(currentSelectedNodeStatus);
}

function convertImageToSingleColor(colorName) {
    const preview = document.getElementById('imagePreview');
    if (!preview.src) return;
    const target = TARGET_COLORS[colorName];
    const canvas = document.createElement('canvas');
    canvas.width = preview.naturalWidth; canvas.height = preview.naturalHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(preview, 0, 0);
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const data = imageData.data;
    for (let i = 0; i < data.length; i += 4) {
        if (data[i+3] > 0) {
            data[i] = target[0]; data[i+1] = target[1]; data[i+2] = target[2]; data[i+3] = 255;
        }
    }
    ctx.putImageData(imageData, 0, 0);
    preview.src = canvas.toDataURL();
    isImageValid = true;
    
    // Only enable save button if node is online
    updateSaveButtonStatus(currentSelectedNodeStatus);
}



function showMappingModal(colorList, container, modal, proceedBtn) {
    container.innerHTML = '';
    let dropdowns = [];
    colorList.forEach(name => {
        const div = document.createElement('div');
        div.style.marginBottom = "10px";
        div.innerHTML = `Map <b>${name}</b>: <select data-name="${name}">
            <option value="">-- No Change --</option>
            ${Object.keys(TARGET_COLORS).map(c => `<option value="${c}">${c}</option>`).join('')}
        </select>`;
        container.appendChild(div);
        dropdowns.push(div.querySelector('select'));
    });

    const checkStatus = () => { 
        if(proceedBtn) proceedBtn.disabled = !dropdowns.some(s => s.value !== ""); 
    };
    dropdowns.forEach(s => s.addEventListener('change', checkStatus));
    checkStatus();

    if(proceedBtn) {
        proceedBtn.onclick = () => {
            const mappings = {};
            dropdowns.forEach(s => { if(s.value) mappings[s.dataset.name] = s.value; });
            applyColorMapping(mappings, lastDetectedColorRGBs);
            isImageValid = true; // Ensure this is true
            updateSaveButtonStatus(currentSelectedNodeStatus);
            modal.style.display = 'none';
        };
    }
    modal.style.display = 'block';
}

// =========================================================================
// 5. EVENT INITIALIZATION
// =========================================================================
document.addEventListener('DOMContentLoaded', function() {
    const imageUpload = document.getElementById('imageUpload');
    const imagePreview = document.getElementById('imagePreview');
    const resetBtn = document.getElementById('resetBtn');
    const applyBtn = document.getElementById('applyBtn');
    const inchSize = document.getElementById('inchSize');
    const validateBtn = document.getElementById('validateBtn');
    const saveBtn = document.getElementById('saveBtn');
    const downloadBtn = document.getElementById('downloadBtn');
    const previewBtn = document.getElementById('previewBtn');
    const requiredDimensions = document.getElementById('requiredDimensions');
    const requiredSizeText = document.getElementById('requiredSizeText');
    const clearImageBtn = document.getElementById('clearImageBtn');
    const frameType = document.getElementById('frameType');
    const nodeSelect = document.getElementById('nodeSelect');
    const dropdownText = document.querySelector('.dropdown-text');
    const uploadArea = document.getElementById('uploadArea');
    const selectedNodeDisplay = document.getElementById('selectedNodeDisplay');
    const selectedNodeValue = document.getElementById('selectedNodeValue');
    const selectedNodeStatus = document.getElementById('selectedNodeStatus');
    const refreshPageBtn = document.getElementById('refreshPageBtn');
    
    // Use the variable that "remembered" the choice
    const frameVal = finalFrameSelection;
    const frameText = document.getElementById('frameText');

    // Initialize selected node display
    if (selectedNodeDisplay) {
        selectedNodeDisplay.style.display = 'block';
    }

    // Restore state after page refresh
    restoreStateAfterRefresh();

    // Setup refresh button
    if (refreshPageBtn) {
        refreshPageBtn.addEventListener('click', refreshPage);
    }

    if (inchSize) {
    inchSize.addEventListener('change', (e) => {
        selectedInchSize = e.target.value;
        
        // 1. DIMENSION MAPPING (Internal resolution only)
        const sizeMap = {
            "2.1": { w: 250, h: 500 }, // Corrected for your 2.1 vertical requirements
            "2.9": { w: 250, h: 592 },
            "4.2": { width: 800, height: 600 },
            "4.8": { width: 800, height: 600 }
        };

        const req = sizeRequirements[selectedInchSize];
        const canvas = document.getElementById('canvas21'); 

        if (req && sizeMap[selectedInchSize]) {
            const dim = sizeMap[selectedInchSize];
            
            // --- THE FIX: We no longer touch 'outer.style' here ---
            // This keeps your grey box at the default 450x450 size
            
            if (canvas) {
                // Set the internal resolution for the hardware payload
                canvas.width = dim.w;
                canvas.height = dim.h; // Fixed typo (was dim.height)

                // Visual CSS to make sure the label scales to fit inside the 450px box
                canvas.style.maxWidth = "100%";
                canvas.style.maxHeight = "100%";
                canvas.style.width = "auto";
                canvas.style.height = "auto";
                canvas.style.display = "block";
            }
            
            if (requiredDimensions) requiredDimensions.style.display = 'block';
            if (requiredSizeText) requiredSizeText.textContent = req.text;
            if (imagePreview.src) checkSizeAgainstRequirement();
            
        } else {
            if (requiredDimensions) requiredDimensions.style.display = 'none';
        }
    });
}


   
    if (imageUpload) {
    imageUpload.addEventListener('change', (e) => {
        if (!selectedInchSize) {
            alert("Select size first!");
            e.target.value = "";
            return;
        }

        const file = e.target.files[0];
        if (!file) return;


window.myTemplateBlob = null;


        if (clearImageBtn) clearImageBtn.style.display = 'inline-block';
        uploadedFileName = file.name;

        const reader = new FileReader();
        reader.onload = (ev) => {
            const img = new Image();
            img.onload = () => {
                originalImageSource = img;
        
        originalImageData = ev.target.result;
                imagePreview.src = originalImageData;
                imagePreview.style.display = 'block';

                const wEl = document.getElementById('imageWidth');
                const hEl = document.getElementById('imageHeight');
                if (wEl) wEl.textContent = img.naturalWidth;
                if (hEl) hEl.textContent = img.naturalHeight;

                checkSizeAgainstRequirement();
                
                // Final UI Updates
                isImageValid = false; // Reset validity check
                updateSaveButtonStatus(currentSelectedNodeStatus);
            };
            img.src = ev.target.result;
        };
        reader.readAsDataURL(file);
    });
}


 // 6. Validation Logic (Handles "Save Without Changes")
    // 6. Validation Logic
if(validateBtn) {
        validateBtn.addEventListener('click', () => {
            if (!imagePreview.src) return;
            if (!checkSizeAgainstRequirement()) return;

            const canvas = document.createElement('canvas');
            canvas.width = imagePreview.naturalWidth; 
            canvas.height = imagePreview.naturalHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(imagePreview, 0, 0);
            const data = ctx.getImageData(0, 0, canvas.width, canvas.height).data;

            const invalidNames = new Set();
            const originalRGBs = {};
            
            for (let i = 0; i < data.length; i += 4) {
                if (data[i+3] < 128) continue;
                const info = getClosestColorInfo(data[i], data[i+1], data[i+2]);
                const name = Object.keys(info)[0];
                originalRGBs[name] = info[name];
                
                if (!isAllowedColor(data[i], data[i+1], data[i+2])) {
                    if (!TARGET_COLORS[name]) invalidNames.add(name);
                }
            }

            lastDetectedColorRGBs = originalRGBs;
            const modal = document.getElementById('colorMismatchModal');
            const container = document.getElementById('mismatchedColorContainer');
            const proceedBtn = document.getElementById('proceedColorMappingBtn');

            if (invalidNames.size === 0) {
                if (confirm("✅ All colors are valid! Do you still want to change/map any existing colors?")) {
                    showMappingModal(Object.keys(originalRGBs), container, modal, proceedBtn);
                } else {
                    isImageValid = true;
                    updateSaveButtonStatus(currentSelectedNodeStatus);
                }
            } else {
                showMappingModal(Array.from(invalidNames), container, modal, proceedBtn);
            }
        });
    }

    // Convert Buttons
    ['White', 'Red', 'Yellow', 'Black'].forEach(c => {
        const btn = document.getElementById(`btnConvert${c}`);
        if (btn) btn.onclick = () => convertImageToSingleColor(c);
    });


 
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (originalImageData) {
                imagePreview.src = originalImageData;
                isImageValid = false;
                updateSaveButtonStatus(currentSelectedNodeStatus);
            } else {
                alert("No original image found to reset to.");
            }
        });
    }

    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            const preview = document.getElementById('imagePreview');
            if (!preview.src || preview.src === "" || preview.src.includes(window.location.host)) {
                alert("Please upload and validate an image first!");
                return;
            }
            const link = document.createElement('a');
            link.download = 'processed_eink_image.png';
            link.href = preview.src;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }

    if (previewBtn) {
        previewBtn.addEventListener('mousedown', () => { if (originalImageData) imagePreview.src = originalImageData; });
        previewBtn.addEventListener('mouseup', () => { if (lastValidatedSrc) imagePreview.src = lastValidatedSrc; });
        previewBtn.addEventListener('touchstart', () => { if (originalImageData) imagePreview.src = originalImageData; });
        previewBtn.addEventListener('touchend', () => { if (lastValidatedSrc) imagePreview.src = lastValidatedSrc; });
    }

    if (applyBtn) {
        applyBtn.addEventListener('click', function() {
            alert("Changes Applied to Workspace!");
            const preview = document.getElementById('imagePreview');
            if (preview) {
                lastValidatedSrc = preview.src; 
            }
            applyBtn.disabled = true;
        });
    }



 // 7. Save Button Logic
if (saveBtn) {
    saveBtn.addEventListener('click', function () {
        if (currentSelectedNodeStatus !== 'online') {
            alert("❌ Cannot send: Selected node is offline. Please select an online node.");
            return;
        }
        
        const nodeEl = document.getElementById('nodeSelect');
        const nodeVal = nodeEl ? nodeEl.value : "";
        const sizeVal = (typeof selectedInchSize !== 'undefined') ? selectedInchSize : "";
        const frameVal = persistentFrameSelection; 
        const fileInput = document.getElementById('imageUpload');
        const imgPreview = document.getElementById('imagePreview');

        // --- CHANGE 1: Update Validation Logic ---
        // Now it's valid if there is a manual file OR a template payload
        const hasFile = (fileInput && fileInput.files.length > 0) || window.myTemplateBlob;

        let missing = [];
        if (!nodeVal) missing.push("Node Selection");
        if (!sizeVal) missing.push("Size Selection");
        if (!hasFile) missing.push("Image Upload (or Template)");
        if (!frameVal) missing.push("Frame Type");

        if (missing.length > 0) {
            alert("⚠️ Please complete the following:\n• " + missing.join("\n• "));
            return; 
        }

        isSendingImage = true;
        if (refreshPageBtn) refreshPageBtn.disabled = true;

        saveBtn.disabled = true;
        saveBtn.textContent = "Sending...";

        const overlay = document.createElement('div');
        overlay.id = "imageLoadingOverlay";
        overlay.style = `position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0, 0, 0, 0.6); padding: 20px; border-radius: 8px; display: flex; flex-direction: column; align-items: center; z-index: 100;`;
        overlay.innerHTML = `<span id="overlayText" style="color: white; margin-bottom: 10px; font-family: sans-serif;">Sending Image...</span><div style="width: 150px; height: 4px; background: rgba(255,255,255,0.2); position: relative; overflow: hidden; border-radius: 2px;"><div style="width: 60px; height: 100%; background: #ffffff; position: absolute; left: -60px; animation: imageLineMove 1.2s infinite linear;"></div></div><style>@keyframes imageLineMove { 0% { left: -60px; } 100% { left: 150px; } } #imagePreviewContainer { position: relative; } </style>`;
        imgPreview.parentElement.appendChild(overlay);

        // --- CHANGE 2: Updated AJAX Payload (The "Virtual File" Bridge) ---
        const formData = new FormData();
        
        if (window.myTemplateBlob) {
            // If template was used, send the binary data we captured
            formData.append('imageUpload', window.myTemplateBlob, 'template.png');
        } else {
            // Otherwise, send the manually uploaded file
            formData.append('imageUpload', fileInput.files[0]);
        }

        formData.append('node_id', nodeVal);
        formData.append('frame', frameVal);

        // --- CHANGE 3: Dimensions Bridge ---
        // Templates don't always have 'naturalWidth', so we check the UI text instead
        const finalW = imgPreview.naturalWidth || parseInt(document.getElementById('imageWidth').textContent);
        const finalH = imgPreview.naturalHeight || parseInt(document.getElementById('imageHeight').textContent);
        
        formData.append('width', finalW);
        formData.append('height', finalH);

        // --- REST OF CODE (Unchanged - Keeps your Timer and Logs) ---
        let maxTime = 80; 
        let elapsed = 0;
        console.log("⏳ Image Upload started. Max wait: 5 minutes");

        const consoleTimer = setInterval(() => {
            elapsed++;
            console.log(`⏱ Waiting... ${elapsed}s / ${maxTime}s`);
            if (elapsed >= maxTime) {
                console.warn("⚠️ Max wait reached. Still waiting for server...");
                clearInterval(consoleTimer);
            }
        }, 1000);

        $.ajax({
            url: "<?php echo get_uri('items/label_controller'); ?>",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (res) {
                clearInterval(consoleTimer);
                console.log(`📥 Response received in ${elapsed}s`);
                console.log("Full Response:", res);

                let otaResponse = null;
                try {
                    otaResponse = JSON.parse(res.ota_body);
                } catch (e) {
                    console.error("❌ Image Upload response parse failed", e);
                    alert("❌ Invalid Image Upload response");
                    isSendingImage = false;
                    if (refreshPageBtn) refreshPageBtn.disabled = false;
                    resetUI();
                    return;
                }

                if (otaResponse.success === true && otaResponse.status === "SUCCEEDED") {
                    console.log("✅ Image Upload SUCCESS");
                    alert("✅ Image Upload completed successfully!");
                    overlay.remove(); 
                    saveBtn.disabled = false; 
                    saveBtn.textContent = "Send Image";
                } else {
                    console.error("❌ Image Upload FAILED");
                    alert("❌ Image Upload Failed: " + (otaResponse.details || "Unknown error"));
                    overlay.remove();
                    saveBtn.disabled = false;
                    saveBtn.textContent = "Send Image";
                }
                isSendingImage = false;
                if (refreshPageBtn) refreshPageBtn.disabled = false;
                resetUI();
            },
            error: function (xhr, status, error) {
                clearInterval(consoleTimer);
                console.error(`❌ AJAX error after ${elapsed}s`);
                alert("❌ Server error occurred");
                isSendingImage = false;
                if (refreshPageBtn) refreshPageBtn.disabled = false;
                resetUI();
            }
        });
    });
}


// Node Select logic (Now safely outside the saveBtn block)

    // Clear Image Function
    function clearImage() {
        const fileInput = document.getElementById('imageUpload');
        const preview = document.getElementById('imagePreview');
        const clearBtn = document.getElementById('clearImageBtn');
        const saveBtn = document.getElementById('saveBtn');

        if (fileInput) fileInput.value = "";

// --- ADD THESE TWO LINES ---
    window.myTemplateBlob = null; 
    if (typeof styleBackgrounds !== 'undefined' && currentActiveStyle) {
        styleBackgrounds[currentActiveStyle] = null;
    }
    // ----------------------------


        if (preview) {
            preview.src = ""; 
            preview.style.display = 'none';
        }


        const wEl = document.getElementById('imageWidth');
        const hEl = document.getElementById('imageHeight');
        if (wEl) wEl.textContent = "0";
        if (hEl) hEl.textContent = "0";

        isImageValid = false;
        lastValidatedSrc = "";

        if (clearBtn) clearBtn.style.display = 'none';
        updateSaveButtonStatus(currentSelectedNodeStatus);
        alert("✨ Cleared successfully.");
    }

    const clearBtnEl = document.getElementById('clearImageBtn');
    if (clearBtnEl) {
        clearBtnEl.onclick = clearImage;
    }
 });
 

// NOTE: Ensure your DOMContentLoaded closing symbols (});) are only at the VERY bottom of the file.
// 2. The Transformation Listener
// --- 1. GLOBAL MEMORY ---
// This variable connects your dropdown to your Save button
 

const frameSelector = document.getElementById('frameType');
const preview = document.getElementById('imagePreview');

// --- 2. DROPDOWN LOGIC ---
frameSelector.addEventListener('change', function () {
    const selected = this.value;
    if (!selected || !preview.src) return;

    // Save selection to our global variable so the Save button can see it
    persistentFrameSelection = selected;

    if (selected === 'Vertical') {
        if (currentRotation % 360 !== 0) {
            currentRotation = 0;
            preview.style.transform = `rotate(${currentRotation}deg)`;
        }
    } else if (selected === 'Horizontal') {
        if (currentRotation % 360 !== 90) {
            currentRotation = 90;
            preview.style.transform = `rotate(${currentRotation}deg)`;
        }
    } else if (selected === 'Rotate') {
        currentRotation = (currentRotation + 90) % 360;
        preview.style.transform = `rotate(${currentRotation}deg)`;
        
        // Update memory based on new rotation
        persistentFrameSelection = (currentRotation % 180 === 0) ? "Vertical" : "Horizontal";
    }

    // --- POPUP FIX: DO NOT RESET this.value here ---
    // If you clear the dropdown, the user might get confused.
    // Keeping the value here ensures everything stays in sync.
});

// Node Select Event Handler
document.getElementById("nodeSelect").addEventListener("change", function () {
    const selectedOption = this.options[this.selectedIndex];
    const nodeId = selectedOption.value;
    const status = selectedOption.getAttribute("data-status");
    
    // Store current node status globally
    currentSelectedNodeStatus = status;

    const badge = document.getElementById("nodeBadge");
    const nodeIdText = document.getElementById("nodeIdText");
    const statusText = document.getElementById("nodeStatusText");

    // Update the selected node display in top right corner
    const selectedNodeValue = document.getElementById('selectedNodeValue');
    const selectedNodeStatus = document.getElementById('selectedNodeStatus');
    
    if (selectedNodeValue) {
        if (nodeId) {
            selectedNodeValue.textContent = nodeId;
            selectedNodeValue.style.color = status === 'online' ? '#0a9b1f' : '#d11a1a';
        } else {
            selectedNodeValue.textContent = 'No node selected';
            selectedNodeValue.style.color = '#666';
        }
    }

    if (selectedNodeStatus) {
        if (status) {
            selectedNodeStatus.textContent = status.toUpperCase();
            selectedNodeStatus.className = `status ${status}`;
            selectedNodeStatus.style.display = 'inline-block';
        } else {
            selectedNodeStatus.textContent = '';
            selectedNodeStatus.style.display = 'none';
        }
    }

    // Update save button status based on node status
    updateSaveButtonStatus(status);

    if (!nodeId) {
        if (badge) badge.style.display = "none";
        return;
    }

    if (nodeIdText) nodeIdText.textContent = nodeId;
    if (statusText) {
        statusText.textContent = `(${status.toUpperCase()})`;
        statusText.className = status === "online" ? "online" : "offline";
    }
    if (badge) badge.style.display = "flex";
});


    //
//
const applyBtn = document.getElementById('applyBtn');
if (applyBtn) {
    applyBtn.addEventListener('click', function() {
        const previewImg = document.getElementById('imagePreview');
        const fileInput = document.getElementById('imageUpload');

        if (cropper && previewImg) {
            const req = sizeRequirements[selectedInchSize];
            
            // 1. Get the cropped pixels at the required size
            const canvas = cropper.getCroppedCanvas({
                width: req.width,
                height: req.height
            });

            if (!canvas) {
                alert("Could not crop image. Please try again.");
                return;
            }

            // 2. Disable buttons during processing to prevent errors
            applyBtn.disabled = true;
            if (typeof saveBtn !== 'undefined') saveBtn.disabled = true;

            // 3. Convert to Blob (This is the critical part for your Save Button)
            canvas.toBlob((blob) => {
                const fileName = (typeof uploadedFileName !== 'undefined' && uploadedFileName) ? uploadedFileName : "image.png";
                const croppedFile = new File([blob], fileName, { type: "image/png" });

                // Update the file input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                fileInput.files = dataTransfer.files;

                // 4. Update the visual display ONLY after blob is ready
                const croppedDataUrl = canvas.toDataURL('image/png');
                
                // Destroy the cropper UI
                cropper.destroy();
                cropper = null;

                previewImg.src = croppedDataUrl;
                previewImg.style.display = 'block';
                
                // Re-enable buttons
                applyBtn.disabled = false;
                if (typeof saveBtn !== 'undefined') saveBtn.disabled = false;

                alert("✅ Applied! Image is now fixed at " + req.width + "x" + req.height);
            }, 'image/png');

        } else {
            alert("No active selection found. Please upload an image and select a size.");
        }
    });
}
//



function resetUI() {
    const saveBtn = document.getElementById('saveBtn');
    if (saveBtn) {
        saveBtn.disabled = false;
        saveBtn.textContent = "Send Image";
        // Re-apply the correct status-based styling
        updateSaveButtonStatus(currentSelectedNodeStatus);
    }

    const overlay = document.getElementById("imageLoadingOverlay");
    if (overlay) overlay.remove();
}




// --- PLACE AT THE TOP OF YOUR SCRIPT ---
let undoStack = [];
const MAX_UNDO_STEPS = 20;

let activeElement = null;
let lastSelectedTextElement = null;
let initialX, initialY;
const canvas = document.getElementById("canvas21");

// 1. OPEN MODAL & SET INITIAL DIMENSIONS
// 1. OPEN MODAL & SET INITIAL DIMENSIONS
// --- 1. THE BUTTON LISTENER (Click to Open Modal) ---
document.getElementById('customTemplateBtn').addEventListener('click', function() {
    const size = document.getElementById('inchSize').value;
    if (!size) { alert("Please select a size first!"); return; }
    
    const sizes = { 
        "2.1": {w: 500, h: 250},
        "2.9": {w: 592, h: 250},
        "4.2": {w: 800, h: 600}  
    };
    
    const selected = sizes[size];
    const canvas = document.getElementById("canvas21");

    // CRITICAL: Update the payload so loadTemplate knows which size to use
    Object.keys(labelPayload).forEach(styleKey => {
        labelPayload[styleKey].config.selectedSize = size;
    });

    // Apply physical size to canvas
    canvas.width = selected.w;
    canvas.height = selected.h;
    canvas.style.width = selected.w + "px";
    canvas.style.height = selected.h + "px";
    canvas.style.minWidth = selected.w + "px";
    canvas.style.minHeight = selected.h + "px";

    // Update Badge and Modal
    document.getElementById('dimensionBadge').textContent = selected.w + " x " + selected.h + " PX";
    document.getElementById('templateModal').style.display = 'flex';

    // Auto-load Style 4 and hide description
    const style4Btn = document.getElementById('layout-style4');
    loadTemplate('style4', style4Btn);
    const descBox = document.getElementById('descriptionContainer'); 
    if (descBox) descBox.style.display = 'none';

    // Sync Preview
    const preview = document.getElementById('imagePreview');
    if (preview) {
        if (canvas instanceof HTMLCanvasElement) {
            preview.src = canvas.toDataURL('image/png');
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
        const wDisplay = document.getElementById('imageWidth');
        const hDisplay = document.getElementById('imageHeight');
        if (wDisplay) wDisplay.textContent = canvas.width || canvas.offsetWidth;
        if (hDisplay) hDisplay.textContent = canvas.height || canvas.offsetHeight;
        isImageValid = true;
        updateSaveButtonStatus(currentSelectedNodeStatus);
    }
    saveState(); 
});

// --- 2. THE DYNAMIC ORIENTATION FUNCTION ---
function forceTemplateOrientation(styleId) {
    const p = labelPayload[styleId];
    if (!p) return; // Safety check if payload doesn't exist

    // Pull the size selection saved in the button click [cite: 2026-01-29]
    const sizeStr = p.config.selectedSize || "2.1";
    
    // --- UPDATED: ADDED 'style9' TO ENABLE VERTICAL DIMENSIONS --- [cite: 2026-01-21]
    const isVert = (styleId === 'style2' || styleId === 'style3' || styleId === 'style7' || styleId === 'style9');

    const sizes = { 
        "2.1": {w: 500, h: 250}, 
        "2.9": {w: 592, h: 250}, 
        "4.2": {w: 800, h: 600} 
    };

    const target = sizes[sizeStr] || sizes["2.1"];

    // Apply Dimensions based on Orientation [cite: 2026-01-21]
    if (isVert) {
        // Swaps dimensions: narrow width, tall height
        canvas.width = target.h; 
        canvas.height = target.w;
    } else {
        canvas.width = target.w;
        canvas.height = target.h;
    }
    
    // Physical Resize [cite: 2026-01-21]
    canvas.style.width = canvas.width + "px";
    canvas.style.height = canvas.height + "px";
    canvas.style.minWidth = canvas.width + "px";
    canvas.style.minHeight = canvas.height + "px";
    
    // Update UI Elements to reflect the change automatically [cite: 2026-01-21]
    const badge = document.getElementById('dimensionBadge');
    if (badge) badge.textContent = canvas.width + " x " + canvas.height + " PX";

    const orientSelect = document.getElementById('orientationSelect');
    if (orientSelect) orientSelect.value = isVert ? "vertical" : "horizontal";
}

// 1. CHANGE THE BACKGROUND (Of the label or the box)
// 2. THE BACKGROUND PICKER: Changes the box OR the main label
function applyCustomColor(color) {
saveState();
    if (lastSelectedTextElement) {
        const parentBox = lastSelectedTextElement.parentElement;
        // If the word is in a box (like POSITIVE), change the box color
        if (parentBox && parentBox !== canvas && parentBox.id !== 'field1') {
            parentBox.style.backgroundColor = color;
            return; 
        }
    }

    // Otherwise, change the main background (Style 3 red area)
    const mainContainer = document.querySelector('#field1 > div') || canvas;
    if (mainContainer) {
        mainContainer.style.backgroundColor = color;
        
        // Ensure UI updates when payload comes [cite: 2026-01-21]
        if (labelPayload[currentActiveStyle]) {
            if (labelPayload[currentActiveStyle].canvas) {
                labelPayload[currentActiveStyle].canvas.background = color;
            } else {
                labelPayload[currentActiveStyle].backgroundColor = color;
            }
        }
    }
}

// 3. THE TEXT PICKER: Changes only the letters
function applyTextColor(color) {
saveState();
    if (!lastSelectedTextElement) return;

    lastSelectedTextElement.style.setProperty('color', color, 'important');
    
    const fieldId = lastSelectedTextElement.getAttribute('data-field-id');
    if (fieldId && labelPayload[currentActiveStyle]?.elements[fieldId]) {
        labelPayload[currentActiveStyle].elements[fieldId].color = color;
    }
}

function adjustFontSize(size) {

  
    if (!lastSelectedTextElement) return;

    const newSize = parseInt(size);

    // 2. Update the Screen Instantly (Direct CSS)
    lastSelectedTextElement.style.fontSize = newSize + "px";

    // 3. Update the Payload Data (Style 1, 4, 6)
    if (labelPayload[currentActiveStyle]) {
        const p = labelPayload[currentActiveStyle];
        
        // Identify which field we clicked (e.g., "brand", "title", "subtitle") [cite: 2026-01-29]
        const fieldId = lastSelectedTextElement.getAttribute('data-field-id');

        // If the element exists in our data, update its size [cite: 2026-01-21]
        if (fieldId && p.elements[fieldId]) {
            p.elements[fieldId].fontSize = newSize;
            
            // Note: We don't call loadTemplate here so the blue box stays visible 
            // while you move the slider.
        }
    }
}
// 4. TEMPLATE SWITCHER (STRICT CLEANUP FOR STYLE 1 & 4)
// 4. TEMPLATE SWITCHER (FULL BEAUTIFIED VERSION)



   
function loadTemplate(style, element) {
    // --- 1. SET CURRENT STYLE (ADD THIS LINE) ---
    currentActiveStyle = style; 

document.querySelectorAll('.manual-field').forEach(field => {
        // Show only if the field matches the current style [cite: 2026-01-21]
        field.style.display = (field.getAttribute('data-style-id') === style) ? "block" : "none";
    });


 // --- 2. ELEMENT DECLARATIONS ---
    const priceTag = document.getElementById('priceTag');
    if (priceTag) priceTag.style.display = 'none'; // Hide by default

 const canvas = document.getElementById('canvas21');
    const qr = document.getElementById('qrGroup');
    const img = document.getElementById('imageGroup');
    const addr = document.getElementById('addressField');
    const h1 = document.getElementById('field1');
    const h2 = document.getElementById('field2');
    const userImg = document.getElementById('userImage');
    const placeholder = document.getElementById('imgPlaceholder');
    const uploadBtn = document.getElementById('uploadBtn');


 const currentW = parseInt(canvas.style.width) || 800;
const scale = currentW / 800;


    // --- 2. IMAGE CLEANUP LOGIC (The Fix) ---
    // This ensures the chocolate image ONLY appears in Style 4
    if (style === 'style4') {
        if (userImg) {
            userImg.src = "https://images.unsplash.com/photo-1548907040-4baa42d10919?w=600&auto=format&fit=crop";
            userImg.style.display = "block";
            userImg.style.width = "100%";
            userImg.style.height = "100%";
            userImg.style.objectFit = "cover";
        }
        if (placeholder) placeholder.style.display = "none";
    } else {
        // If switching AWAY from Style 4, remove the chocolate image
        // but keep the img group visible if the style requires it (Style 2/3)
        if (userImg && userImg.src.includes("unsplash.com")) {
            userImg.src = ""; 
            userImg.style.display = "none";
            if (placeholder) placeholder.style.display = "block";
        }
    }

   // --- 3. THE STRICT GLOBAL RESET ---
canvas.style.backgroundImage = 'none'; 
canvas.style.backgroundColor = '#ffffff';
canvas.style.borderTop = "none";

// FIX: Only reset the storage if we are MANUALLY switching styles (clicking a button)
// If 'element' exists, it means a button was clicked.
if (element && typeof styleBackgrounds !== 'undefined') {
    styleBackgrounds[style] = null; 
}

// Reset Buttons UI
document.querySelectorAll('[id^="layout-style"]').forEach(btn => {
    btn.style.borderColor = "#ddd"; 
    btn.style.backgroundColor = "white"; 
    btn.style.color = "#7f8c8d";
});

if(element) {
    element.style.borderColor = "#3498db"; 
    element.style.backgroundColor = "#ebf5fb"; 
    element.style.color = "#3498db";
}

[h1, h2].forEach(h => {
    h.style.textAlign = "left";
    h.style.color = "#2c3e50";
    h.style.textShadow = "none";
    h.style.width = "auto";
    h.style.letterSpacing = "normal";
    h.style.background = "none";
    h.style.padding = "0";
    h.style.borderRadius = "0";
    h.style.boxShadow = "none";
    h.style.border = "none";
    h.style.lineHeight = "1.2";
    h.style.fontWeight = "700";
});

[qr, img, addr].forEach(el => {
    if(el) {
        el.style.display = 'none';
        el.style.left = "auto"; el.style.right = "auto";
        el.style.top = "auto"; el.style.bottom = "auto";
        el.style.borderRadius = "0"; 
        el.style.border = "none";
        el.style.boxShadow = "none";
        el.style.backgroundColor = "transparent";
        el.style.padding = "0";
    }
});


// --- STEP 1: PASTE THE ALERT LOGIC HERE [cite: 2026-01-21] ---
    if (style === 'style5' || style === 'style9') {
        showBackgroundAlert(style); // Show the pop-up
        
        const label = document.querySelector('label[for="bgUpload"]');
        if (label) {
            label.style.animation = "pulse-blue 2s infinite";
            label.style.border = "2px solid #3498db";
        }
    } else {
        const label = document.querySelector('label[for="bgUpload"]');
        if (label) {
            label.style.animation = "none";
            label.style.border = "1.5px dashed #3498db";
        }
    }

// Inside loadTemplate(style)
const qrText = document.getElementById('qrLabelText');
const qrField = document.getElementById('qrInput');

if (style === 'style1') {
    if (qrText) qrText.innerText = "QR DATA (ACTIVE)"; // Changes the text
    if (qrField) qrField.style.animation = "pulse-orange 2s infinite"; // Starts the glow
} else {
    if (qrText) qrText.innerText = "QR DATA"; // Resets the text
    if (qrField) qrField.style.animation = "none"; // Stops the glow
}

    // --- 4. THE STYLES ---
if (style === 'style1') {
    forceTemplateOrientation('style1');
    
    const currentSize = document.getElementById('inchSize').value;
    const searchKey = style + "_" + currentSize;
    const p = labelPayload[searchKey] || labelPayload[style] || Object.values(labelPayload).find(item => item.template_id === 'style1');

    if (!p) return;
    const d = p.data;     
    const e = p.elements;
    currentActiveStyle = 'style1'; 

    const isVert = canvas.height > canvas.width;
    
    // Set up the H1 container
    h1.style.display = "block";
    h1.style.position = "absolute";
    h1.style.left = "0px";
    h1.style.top = "0px";
    h1.style.width = isVert ? canvas.height + "px" : canvas.width + "px";
    h1.style.height = isVert ? canvas.width + "px" : canvas.height + "px";
    h1.style.transformOrigin = "top left";
    h1.style.transform = isVert ? `rotate(90deg) translateY(-${canvas.width}px)` : "none";

    h1.innerHTML = `
    <div id="customCanvasContainer" style="width:100%; height:100%; position:relative; font-family: 'Arial Black', sans-serif; background:${p.canvas.background}; overflow:hidden; pointer-events: none;">
        
        <div style="position:absolute; left:${e.title.x}px; top:${e.title.y}px; z-index:20; pointer-events: auto;">
            <span class="text-element" data-field-id="title" 
                  style="font-size:${e.title.fontSize}px; font-weight:900; color:${e.title.color}; white-space:nowrap; cursor:pointer; display:inline-block;">
                ${d.title}
            </span>
        </div>
        
        <div style="position:absolute; left:${e.logo_box.x}px; top:${e.logo_box.y}px; background:${e.logo_box.backgroundColor}; width:${e.logo_box.width}px; height:${e.logo_box.height}px; border:${e.logo_box.borderWidth}px solid #000; display:flex; flex-direction:column; justify-content:center; align-items:center; z-index:20; pointer-events: auto;">
            <span class="text-element" data-field-id="logo_main" style="font-size:${e.logo_main.fontSize}px; font-weight:900; color:#000; line-height:1; cursor:pointer; display:block;">
                ${d.logo_main}
            </span>
            <span class="text-element" data-field-id="logo_sub" style="font-size:${e.logo_sub.fontSize}px; font-weight:900; color:#ff0000; line-height:1; cursor:pointer; display:block;">
                ${d.logo_sub}
            </span>
        </div>

        <div style="position:absolute; left:${e.subtitle.x}px; top:${e.subtitle.y}px; z-index:20; pointer-events: auto;">
            <span class="text-element" data-field-id="subtitle" 
                  style="font-size:${e.subtitle.fontSize}px; font-weight:900; color:${e.subtitle.color}; text-transform:uppercase; cursor:pointer; display:inline-block;">
                ${d.subtitle}
            </span>
        </div>
      
        <div style="position:absolute; left:${e.offer.x}px; top:${e.offer.y}px; display:flex; align-items:flex-end; z-index:20; pointer-events: auto;">
            <span class="text-element" data-field-id="offer" 
                  style="font-size:${e.offer.fontSize}px; font-weight:900; color:${e.offer.color}; line-height:0.8; cursor:pointer; display:inline-block;">
                ${d.offer}
            </span>
            <span style="font-size:${e.offer_symbol ? e.offer_symbol.fontSize : (e.offer.fontSize/2)}px; color:${e.offer.color}; font-weight:900; margin-left:5px; margin-bottom:5px;">
                ${d.offer_symbol || '%'}
            </span>
        </div>

        <div style="position:absolute; left:${e.offer_type_box.x}px; top:${e.offer_type_box.y}px; display:flex; align-items:center; z-index:20; pointer-events: auto;">
            <div style="background:#ffffff; padding:2px 8px; border-radius:4px; margin-right:10px;">
                <span class="text-element" data-field-id="offer_type_red" style="color:#ff0000; font-size:${e.offer_type_red.fontSize}px; font-weight:900; line-height:1; white-space:nowrap; cursor:pointer;">
                    ${d.offer_type_red}
                </span>
            </div>
            <span class="text-element" data-field-id="offer_type_white" style="font-size:${e.offer_type_white.fontSize}px; font-weight:900; color:#ffffff; line-height:1; white-space:nowrap; cursor:pointer;">
                ${d.offer_type_white}
            </span>
        </div>

        <div style="position:absolute; left:${e.date_box.x}px; top:${e.date_box.y}px; background:${e.date_box.backgroundColor}; width:${e.date_box.width}px; height:${e.date_box.height}px; display:flex; align-items:center; justify-content:center; z-index:20; pointer-events: auto;">
            <span class="text-element" data-field-id="date" 
                  style="color:${e.date.color}; font-size:${e.date.fontSize}px; font-weight:900; cursor:pointer;">
                ${d.date}
            </span>
        </div>

      <div id="qr-container-style1" style="position:absolute; left:${e.qr.x}px; top:${e.qr.y}px; background:#fff; padding:5px; width:${e.qr.size}px; height:${e.qr.size}px; border:1px solid #000; z-index:100; pointer-events: auto;">
     <img id="qr-image-live" 
          src="https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(d.qr_data || 'hello')}&size=150x150" 
          style="width:100%; height:100%;">
</div>
    </div>`;
}

else if (style === 'style2') {
    forceTemplateOrientation('style2');
    
    // 1. DYNAMIC DATA SELECTION
    const currentSize = document.getElementById('inchSize').value;
    const searchKey = style + "_" + currentSize;

    const p = labelPayload[searchKey] || 
              labelPayload[style] || 
              Object.values(labelPayload).find(item => item.template_id?.startsWith('style2'));

    if (!p) return; // Prevent "lag" or loading issues

    const d = p.data;     
    const e = p.elements; 
    currentActiveStyle = 'style2'; 

    const currentW = canvas.width;
    const currentH = canvas.height;
    
    h1.style.display = "block";
    h1.style.position = "absolute";
    h1.style.left = "0px";
    h1.style.top = "0px";
    h1.style.width = currentW + "px";
    h1.style.height = currentH + "px";

    const style2Img = (window.templateImages && window.templateImages['style2']) ? window.templateImages['style2'] : "";

    // 2. RENDER CONTENT [cite: 2026-01-21]
    h1.innerHTML = `
    <div id="customCanvasContainer" style="width:100%; height:100%; position:relative; font-family: 'Arial Black', sans-serif; background:${p.canvas.background}; overflow:hidden;">
        
        <div style="position:absolute; left:${e.top_bar_box.x}px; top:${e.top_bar_box.y}px; width:${e.top_bar_box.width}px; height:${e.top_bar_box.height}px; background:${e.top_bar_box.backgroundColor}; display:flex; align-items:center; justify-content:center; z-index:10;">
             <span class="text-element" data-field-id="top_tag" style="font-size:${e.top_tag.fontSize}px; font-weight:900; color:${e.top_tag.color}; text-transform:uppercase;">
                ${d.top_tag}
            </span>
        </div>

        <div style="position:absolute; left:0px; top:${e.classic_text.y}px; width:100%; text-align:center; z-index:10;">
            <span class="text-element" data-field-id="classic_text" style="font-size:${e.classic_text.fontSize}px; font-weight:900; color:${e.classic_text.color}; display:block;">
                ${d.classic_text}
            </span>
            <span class="text-element" data-field-id="brand_name" style="font-size:${e.brand_name.fontSize}px; font-weight:900; color:${e.brand_name.color}; display:block;">
                ${d.brand_name}
            </span>
        </div>

        <div class="draggable-wrapper" data-field-id="image_box" 
             style="position:absolute; left:${e.image_box.x}px; top:${e.image_box.y}px; width:${e.image_box.width}px; height:${e.image_box.height}px; z-index:100; cursor:move; border:2px solid #3498db; pointer-events: auto;">
            
            <div style="width:100%; height:100%; overflow:hidden;" onclick="document.getElementById('galleryInput').click();">
                ${style2Img === "" 
                    ? `<div style="color:#ff0000; font-size:14px; font-weight:900; display:flex; align-items:center; justify-content:center; height:100%; background:#eee;">+ UPLOAD</div>` 
                    : `<img src="${style2Img}" style="width:100%; height:100%; object-fit:fill; display:block; pointer-events: none;">`} 
            </div>

            <div class="resizer" style="width:14px; height:14px; background:#3498db; position:absolute; right:-7px; bottom:-7px; cursor:nwse-resize; border-radius:50%; z-index:110; border:2px solid white;"></div>
        </div>

        <div style="position:absolute; left:0px; top:${e.price_val.y}px; width:100%; text-align:center; z-index:10;">
            <span class="text-element" data-field-id="price_val" style="font-size:${e.price_val.fontSize}px; font-weight:900; color:${e.price_val.color}; display:block;">
                ${d.price_val}
            </span>
        </div>

        <div style="position:absolute; left:0px; bottom:15px; width:100%; text-align:center; z-index:10;">
            <span class="text-element" data-field-id="footer" style="font-size:${e.footer.fontSize}px; font-weight:700; color:${e.footer.color}; opacity:0.8;">
                ${d.footer}
            </span>
        </div>
    </div>`;

    // 3. START INTERACTIONS
    initInteractiveElements();
}

else if (style === 'style3') {
    forceTemplateOrientation('style3');
    
    h1.innerHTML = ''; 
    document.querySelectorAll('.description-placeholder, #descriptionBox, [id*="description"], .canvas-description')
            .forEach(el => el.remove());

    // --- FIX: IMPROVED DATA SELECTION FOR UPDATES ---
    const currentSize = document.getElementById('inchSize').value;
    
    // 1. Try style3_4.2 (if size is 4.2)
    // 2. Try style3 (your default 4.2 key)
    // 3. Fallback to any style3 available
    let p = labelPayload[`style3_${currentSize}`] || labelPayload['style3'];

    if (!p) {
        // Ultimate fallback to ensure it doesn't break if keys are slightly different
        p = Object.values(labelPayload).find(item => item.template_id && item.template_id.includes('style3'));
    }

    if (!p) return;
    const d = p.data;
    const e = p.elements;
    currentActiveStyle = 'style3';
    // -----------------------------------------------

    const isHorizontalCanvas = canvas.width > canvas.height;
    h1.style.display = "block";
    h1.style.position = "absolute";
    h1.style.left = "0px";
    h1.style.top = "0px";
    h1.style.width = isHorizontalCanvas ? canvas.height + "px" : canvas.width + "px";
    h1.style.height = isHorizontalCanvas ? canvas.width + "px" : canvas.height + "px";
    h1.style.transformOrigin = "top left";
    h1.style.transform = isHorizontalCanvas ? `rotate(90deg) translateY(-${canvas.height}px)` : "none";

    h1.innerHTML = `
    <div style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; background:${p.canvas.background}; font-family: 'Arial Black', sans-serif; overflow:hidden; pointer-events: auto;">
        
        <div style="margin-bottom:10px;">
             <span class="text-element" data-field-id="happy" 
                   style="font-size:${e.happy.fontSize}px; font-weight:900; color:${e.happy.color}; text-transform:uppercase; cursor:pointer;">
                ${d.happy}
            </span>
        </div>

        <div style="position:relative; width:${e.positive_box.width}px; height:${e.positive_box.height}px; background:${e.positive_box.backgroundColor}; border-radius:${e.positive_box.borderRadius}px; display:flex; align-items:center; justify-content:center; margin-bottom:15px;">
            <span class="text-element" data-field-id="positive" 
                  style="font-size:${e.positive.fontSize}px; font-weight:900; color:${e.positive.color}; text-transform:uppercase; cursor:pointer;">
                ${d.positive}
            </span>
        </div>

        <div style="margin-bottom:20px;">
            <span class="text-element" data-field-id="thinking" 
                  style="font-size:${e.thinking.fontSize}px; font-weight:900; color:${e.thinking.color}; text-transform:uppercase; cursor:pointer;">
                ${d.thinking}
            </span>
        </div>

        <div style="display:flex; align-items:center; justify-content:center; width:100%;">
            <div style="width:${e.line_left.width}px; height:${e.line_left.height}px; background:${e.line_left.color}; margin-right:10px;"></div>
            <span class="text-element" data-field-id="day" 
                  style="font-size:${e.day.fontSize}px; font-weight:900; color:${e.day.color}; text-transform:uppercase; cursor:pointer;">
                ${d.day}
            </span>
            <div style="width:${e.line_right.width}px; height:${e.line_right.height}px; background:${e.line_right.color}; margin-left:10px;"></div>
        </div>
        
    </div>`;
}


else if (style === 'style4') {
    forceTemplateOrientation('style4');
    
    // 1. CLEAN THE CANVAS [cite: 2026-01-21]
    h1.innerHTML = ''; 
    document.querySelectorAll('.description-placeholder, #descriptionBox, [id*="description"], .canvas-description')
            .forEach(el => el.remove());

    // 2. GET DATA [cite: 2026-01-29]
    const currentSize = document.getElementById('inchSize').value;
    const searchKey = style + "_" + currentSize;
    const p = labelPayload[searchKey] || labelPayload[style];
    if (!p) return;

    const d = p.data;
    const e = p.elements;
    
    // Set this for color and background color updates [cite: 2026-02-07]
    currentActiveStyle = 'style4'; 

    // 3. CANVAS SETUP [cite: 2026-01-21]
    const isVert = canvas.height > canvas.width;
    h1.style.display = "block";
    h1.style.position = "absolute";
    h1.style.left = "0px";
    h1.style.top = "0px";
    h1.style.width = isVert ? canvas.height + "px" : canvas.width + "px";
    h1.style.height = isVert ? canvas.width + "px" : canvas.height + "px";
    h1.style.transformOrigin = "top left";
    h1.style.transform = isVert ? `rotate(90deg) translateY(-${canvas.width}px)` : "none";

    // 4. RENDER CENTERED DESIGN WITH CLICKABLE ELEMENTS [cite: 2026-02-07]
    h1.innerHTML = `
    <div id="customCanvasContainer" style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; background:${p.canvas.background}; font-family: 'Arial Black', sans-serif; overflow:hidden; pointer-events: none;">
        
        <div style="position:relative; display:inline-block; pointer-events: auto; z-index:20;">
            
            <div style="line-height:0.8; white-space:nowrap; display:flex; align-items:flex-start;">
                <span class="text-element" data-field-id="brand" 
                      style="color:${e.brand.color}; font-size:${e.brand.fontSize}px; font-weight:${e.brand.fontWeight}; cursor:pointer;">
                    ${d.brand_name}
                </span>

                <div style="margin-left: 5px;">
                    <span class="text-element" data-field-id="brand_suffix" 
                          style="color:${e.brand_suffix.color}; font-size:${e.brand_suffix.fontSize}px; font-weight:900; cursor:pointer;">
                        ${d.brand_suffix}
                    </span>
                </div>
            </div>
        </div>

        <div style="display:flex; align-items:center; margin-top:15px; pointer-events: auto; z-index:20;">
            <div style="width:${e.line_left.width}px; height:${e.line_left.height}px; background:${e.line_left.color}; margin-right:10px;"></div>

            <span class="text-element" data-field-id="tagline" 
                  style="color:${e.tagline.color}; font-size:${e.tagline.fontSize}px; font-weight:${e.tagline.fontWeight}; text-transform:uppercase; white-space:nowrap; cursor:pointer;">
                ${d.tagline_text}
            </span>

            <div style="width:${e.line_right.width}px; height:${e.line_right.height}px; background:${e.line_right.color}; margin-left:10px;"></div>
        </div>
        
    </div>`;
}

else if (style === 'style5') {

    forceTemplateOrientation('style5');
    
    // 1. CLEAR OVERLAYS [cite: 2026-01-21]
    h1.innerHTML = ''; 
    document.querySelectorAll('.description-placeholder, #descriptionBox, [id*="description"], .canvas-description')
            .forEach(el => el.remove());

    // 2. DYNAMIC PAYLOAD SELECTION [cite: 2026-01-21, 2026-01-29]
    const currentSize = document.getElementById('inchSize').value;
    let p = labelPayload['style5_' + currentSize] || labelPayload['style5'];
    
    if (!p) return;
    const d = p.data;
    const e = p.elements;
    currentActiveStyle = 'style5';

    const isVert = canvas.height > canvas.width;
    const logicWidth = isVert ? canvas.height : canvas.width;
    const logicHeight = isVert ? canvas.width : canvas.height;
    
    // 3. SCALE LOGIC [cite: 2026-01-29]
    const rx = (currentSize === '4.2') ? 1 : (logicWidth / 500);
    const ry = (currentSize === '4.2') ? 1 : (logicHeight / 250);
    const scale = (currentSize === '4.2') ? 1 : Math.min(rx, ry);

    // 4. CANVAS STYLING
    h1.style.position = "absolute";
    h1.style.left = "0px";
    h1.style.top = "0px";
    h1.style.width = isVert ? canvas.height + "px" : "100%";
    h1.style.height = isVert ? canvas.width + "px" : "100%";
    h1.style.transform = isVert ? `rotate(90deg) translateY(-${canvas.width}px)` : "none";
    h1.style.transformOrigin = "top left";

    // --- NEW: BACKGROUND IMAGE SELECTION --- [cite: 2026-01-21]
    // If userUploadedBG has data, use it. Otherwise, fall back to the payload default.// Check ONLY style5's drawer for an uploaded image [cite: 2026-01-21]
const activeBackground = styleBackgrounds['style5'] ? styleBackgrounds['style5'] : d.bg_image;
    // 5. RENDER WITH UPDATED IMAGE SOURCE [cite: 2026-01-21, 2026-01-29]
    h1.innerHTML = `
        <div id="customCanvasContainer" style="width:100%; height:100%; position:relative; background: url('${activeBackground}') center/cover no-repeat; overflow:hidden; font-family: 'Arial Black', sans-serif; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; pointer-events: auto;">
            
            <div style="position:absolute; inset:0; background:rgba(0,0,0,0.35); pointer-events: none;"></div>

            <div style="position:relative; margin-bottom:${10 * ry}px;">
                <span class="text-element" data-field-id="main_title" 
                      style="color:${e.main_title.color}; font-size:${e.main_title.fontSize * scale}px; font-weight:900; text-transform:uppercase; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); cursor:pointer;">
                    ${d.main_title}
                </span>
            </div>

            <div style="position:relative; margin-bottom:${15 * ry}px; padding: 0 ${20 * rx}px;">
                <span class="text-element" data-field-id="tagline" 
                      style="color:${e.tagline.color}; font-size:${e.tagline.fontSize * scale}px; font-weight:600; cursor:pointer;">
                    ${d.tagline}
                </span>
            </div>

            <div style="position:relative; display:flex; flex-direction:column; align-items:center;">
                <div style="width:${(currentSize === '4.2' ? 120 : 80 * scale)}px; height:2px; background:#fff; margin-bottom:${5 * ry}px;"></div>
                <span class="text-element" data-field-id="est_date" 
                      style="color:${e.est_date.color}; font-size:${e.est_date.fontSize * scale}px; font-weight:700; text-transform:uppercase; letter-spacing:1px; cursor:pointer;">
                    ${d.est_date}
                </span>
            </div>
        </div>`;
}

else if (style === 'style6') {
    forceTemplateOrientation('style6');
    
    // 1. CLEAR OVERLAYS [cite: 2026-01-21]
    h1.innerHTML = ''; 
    document.querySelectorAll('.description-placeholder, #descriptionBox, [id*="description"], .canvas-description')
            .forEach(el => el.remove());

    // 2. DYNAMIC PAYLOAD SELECTION [cite: 2026-01-21, 2026-01-29]
    const currentSize = document.getElementById('inchSize').value;
    // Fix: Your payload uses "style6" for 4.2 specifically.
  let p = labelPayload['style6_' + currentSize] || labelPayload['style6'];
    if (!p) return;
    const d = p.data;
    const e = p.elements;
    currentActiveStyle = 'style6';

    const isVert = canvas.height > canvas.width;
    const logicWidth = isVert ? canvas.height : canvas.width;
    const logicHeight = isVert ? canvas.width : canvas.height;
    
    // 3. SCALE LOGIC: Only scale for small sizes; Use 1:1 for 4.2 [cite: 2026-01-29]
    const rx = (currentSize === '4.2') ? 1 : (logicWidth / 500);
    const ry = (currentSize === '4.2') ? 1 : (logicHeight / 250);
    const scale = (currentSize === '4.2') ? 1 : Math.min(rx, ry);

    // 4. CANVAS STYLING
    canvas.style.backgroundColor = p.canvas.background;
    h1.style.position = "absolute";
    h1.style.left = "0px";
    h1.style.top = "0px";
    h1.style.width = isVert ? canvas.height + "px" : "100%";
    h1.style.height = isVert ? canvas.width + "px" : "100%";
    h1.style.transform = isVert ? `rotate(90deg) translateY(-${canvas.width}px)` : "none";
    h1.style.transformOrigin = "top left";

    // 5. RENDER [cite: 2026-01-21, 2026-01-29]
    h1.innerHTML = `
        <div style="width:100%; height:100%; position:relative; background:${p.canvas.background}; overflow:hidden; font-family: 'Arial Black', sans-serif; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events: auto;">
            
            <div style="position:relative; margin-bottom:${15 * ry}px; background:${e.title.backgroundColor || 'transparent'}; padding:5px;">
                <span class="text-element" data-field-id="title" 
                      style="color:${e.title.textColor}; font-size:${e.title.fontSize * scale}px; font-weight:900; white-space:nowrap; cursor:pointer;">
                    ${d.title}
                </span>
            </div>

            <div style="position:relative; width:${e.price_box.width * rx}px; height:${e.price_box.height * ry}px; margin-bottom:${25 * ry}px; display:flex; align-items:center; justify-content:center;">
                <div style="position:absolute; width:100%; height:100%; background-color:${e.price_box.backgroundColor}; border-radius:${e.price_box.borderRadius * scale}px; cursor:pointer;"></div>
                <span class="text-element" data-field-id="price_tag" 
                      style="position:relative; color:${e.price_tag.color}; font-size:${e.price_tag.fontSize * scale}px; font-weight:900; cursor:pointer;">
                    ${d.price}
                </span>
            </div>

            <div style="position:relative; display:flex; align-items:center;">
                <div style="width:${20 * scale}px; height:2px; background:#fff; margin-right:10px;"></div>
                <span class="text-element" data-field-id="footer" 
                      style="color:${e.footer.textColor}; font-size:${e.footer.fontSize * scale}px; font-weight:600; text-transform:uppercase; cursor:pointer;">
                    ${d.footer}
                </span>
                <div style="width:${20 * scale}px; height:2px; background:#fff; margin-left:10px;"></div>
            </div>
        </div>`;
}


else if (style === 'style7') {
    forceTemplateOrientation('style7');
    
    h1.innerHTML = ''; 
    document.querySelectorAll('.description-placeholder, #descriptionBox, [id*="description"], .canvas-description')
            .forEach(el => el.remove());

    const currentSize = document.getElementById('inchSize').value;
    
    let p = labelPayload[`style7_${currentSize}`] || labelPayload['style7'];
    if (!p) {
        p = Object.values(labelPayload).find(item => item.template_id && item.template_id.includes('style7'));
    }

    if (!p) return;
    const d = p.data;
    const e = p.elements;
    
    // Set active style for color picker updates [cite: 2026-02-07]
    currentActiveStyle = 'style7';

    const isHorizontalCanvas = canvas.width > canvas.height;
    h1.style.display = "block";
    h1.style.position = "absolute";
    h1.style.left = "0px";
    h1.style.top = "0px";
    h1.style.width = isHorizontalCanvas ? canvas.height + "px" : canvas.width + "px";
    h1.style.height = isHorizontalCanvas ? canvas.width + "px" : canvas.height + "px";
    h1.style.transformOrigin = "top left";
    h1.style.transform = isHorizontalCanvas ? `rotate(90deg) translateY(-${canvas.height}px)` : "none";

    // --- RENDER WITH INTERACTIVE ELEMENTS ---
    h1.innerHTML = `
    <div id="customCanvasContainer" style="width:100%; height:100%; position:relative; background:${p.canvas.background}; font-family: 'Arial Black', sans-serif; overflow:hidden; pointer-events: none;">
        
        <div style="position:absolute; width:100%; top:${e.title.y}px; left:0; text-align:center; z-index:20; pointer-events: auto;">
             <span class="text-element" data-field-id="title" 
                   style="font-size:${e.title.fontSize}px; font-weight:900; color:${e.title.color}; text-transform:uppercase; cursor:pointer; display:inline-block; line-height:1.2;">
                ${d.title}
            </span>
        </div>

        <div style="position:absolute; width:100%; top:${e.title.y + (e.title.fontSize * 1.2)}px; display:flex; justify-content:center; pointer-events: none;">
             <div style="width:50px; height:4px; background:#ffff00; opacity: 0.8;"></div>
        </div>
        
    </div>`;
}

else if (style === 'style8') {
    forceTemplateOrientation('style8'); 
    
    h1.innerHTML = ''; 
    document.querySelectorAll('.description-placeholder, #descriptionBox, [id*="description"], .canvas-description')
            .forEach(el => el.remove());

    const currentSize = document.getElementById('inchSize').value;
    let p = labelPayload[`style8_${currentSize}`] || labelPayload['style8'];
    if (!p) p = Object.values(labelPayload).find(item => item.template_id && item.template_id.includes('style8'));

    if (!p) return;
    const d = p.data;
    const e = p.elements;
    
    // Set active style so the color picker knows which payload key to update
    currentActiveStyle = 'style8';

    // --- DIMENSION LOGIC ---
    h1.style.display = "block";
    h1.style.position = "absolute";
    h1.style.left = "0px";
    h1.style.top = "0px";
    h1.style.width = canvas.width + "px";
    h1.style.height = canvas.height + "px";
    h1.style.transform = "none";

    // --- RENDER WITH INTERACTIVE BOX LOGIC ---
    h1.innerHTML = `
    <div id="customCanvasContainer" style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; background:${p.canvas?.background || '#ffffff'}; font-family: 'Arial Black', sans-serif; overflow:hidden; pointer-events: none;">
        
        <div style="padding: 15px; display: flex; align-items: center; justify-content: center; pointer-events: auto; z-index: 20;">
             
             <span class="text-element" data-field-id="title" 
                   style="font-size:${e.title.fontSize}px; font-weight:900; color:${e.title.color || '#000000'}; text-transform:uppercase; cursor:pointer; display:inline-block; line-height:1.1;">
                ${d.title}
            </span>

        </div>
        
    </div>`;
}


else if (style === 'style9') {
    forceTemplateOrientation('style9');
    
    h1.innerHTML = ''; 
    document.querySelectorAll('.description-placeholder, #descriptionBox, [id*="description"], .canvas-description')
            .forEach(el => el.remove());

    const currentSize = document.getElementById('inchSize').value;
    let p = labelPayload['style9_' + currentSize] || labelPayload['style9'];
    if (!p) return;
    
    const d = p.data;
    const e = p.elements;
    currentActiveStyle = 'style9';

    // FIX: Standardize scaling for 2.1 and 2.9 to use the same 500px base [cite: 2026-01-29]
    // This prevents 2.9 from scaling up "manually" or incorrectly
    const baseHeight = (currentSize === '4.2') ? 800 : 500;
    const scale = canvas.height / baseHeight;

    h1.style.position = "absolute";
    h1.style.inset = "0px";
    h1.style.width = "100%";
    h1.style.height = "100%";

    // Check ONLY style9's drawer for an uploaded image [cite: 2026-01-21]
const activeBackground = styleBackgrounds['style9'] ? styleBackgrounds['style9'] : d.bg_image;

    h1.innerHTML = `
        <div id="customCanvasContainer" style="width:100%; height:100%; position:relative; background: url('${activeBackground}') center/cover no-repeat; overflow:hidden; font-family: 'Arial Black', sans-serif;">
            
            <div style="position:absolute; width:100%; height:100%; left:0; top:0; display:flex; flex-direction:column; align-items:center;">
                
                <div style="position:absolute; top:${e.main_title.y * scale}px; width:90%; text-align:center;">
                    <span class="text-element" data-field-id="main_title" 
                          style="color:${e.main_title.color}; font-size:${e.main_title.fontSize * scale}px; font-weight:${e.main_title.fontWeight}; text-transform:uppercase; cursor:pointer; display:block; line-height:1.1;">
                        ${d.main_title}
                    </span>
                </div>

                <div style="position:absolute; top:${e.tagline.y * scale}px; width:90%; text-align:center;">
                    <span class="text-element" data-field-id="tagline" 
                          style="color:${e.tagline.color}; font-size:${e.tagline.fontSize * scale}px; font-weight:${e.tagline.fontWeight}; text-transform: uppercase; cursor:pointer; display:block; line-height:1.2;">
                        ${d.tagline}
                    </span>
                </div>

                <div style="position:absolute; top:${e.est_date.y * scale}px; width:100%; display:flex; flex-direction:column; align-items:center;">
                    <div style="width:${40 * scale}px; height:1px; background:rgba(255,255,255,0.6); margin-bottom:${8 * scale}px;"></div>
                    <span class="text-element" data-field-id="est_date" 
                          style="color:${e.est_date.color}; font-size:${e.est_date.fontSize * scale}px; font-weight:${e.est_date.fontWeight}; text-transform:uppercase; cursor:pointer;">
                        ${d.est_date}
                    </span>
                </div>
            </div>
        </div>`;
}
// --- ADD THIS AT THE BOTTOM OF loadTemplate() ---
const currentSize = document.getElementById('inchSize').value;

// Look at every manual text box on the screen
document.querySelectorAll('.manual-field').forEach(field => {
    const fieldStyle = field.getAttribute('data-style-id');
    const fieldSize = field.getAttribute('data-size');

    // Only show the box if it matches the style AND the size we are looking at
    if (fieldStyle === style && fieldSize === currentSize) {
        field.style.display = "block"; // Show it
    } else {
        field.style.display = "none";  // Hide it
    }
});
}
// 5. DRAG & DROP + IMAGE UPLOAD
function handleGalleryUpload(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => { 
            // Save the image data to a unique global variable based on the style
            if (!window.templateImages) window.templateImages = {};
            window.templateImages[currentActiveStyle] = e.target.result;

            // Refresh ONLY the current active style
            loadTemplate(currentActiveStyle, null); 
        };
        reader.readAsDataURL(file);
    }
}

document.querySelectorAll('.draggable').forEach(el => {
    el.addEventListener('mousedown', (e) => {
        activeElement = el;
        initialX = e.clientX - activeElement.offsetLeft;
        initialY = e.clientY - activeElement.offsetTop;
        if (el.classList.contains('text-element')) lastSelectedTextElement = el;
        document.querySelectorAll('.draggable').forEach(d => d.style.outline = "none");
        activeElement.style.outline = "2px solid #3498db";
    });
});
document.addEventListener('mousemove', (e) => {
    if (activeElement) {
        activeElement.style.left = (e.clientX - initialX) + "px";
        activeElement.style.top = (e.clientY - initialY) + "px";
    }
});
document.addEventListener('mouseup', () => activeElement = null);



function addLiveField() {
saveState();
    const field = document.createElement('div');
    field.className = 'draggable text-element manual-field';
    field.setAttribute('data-style-id', currentActiveStyle); 
    field.contentEditable = "true";
    field.innerText = "Click to type...";
    
    field.style.cssText = `
        position: absolute;
        top: 30%; left: 50%;
        transform: translate(-50%, -50%);
        padding: 5px;
        min-width: 100px;
        border: 1px dashed transparent; /* THE FIX: Hide line by default */
        color: #000000 !important;
        font-weight: bold;
        font-size: 20px;
        cursor: move;
        z-index: 9999;
        outline: none; /* Removes the default browser blue box */
    `;

    // 1. SHOW LINE and CLEAR placeholder when you click to edit
    field.addEventListener('focus', () => {
        field.style.border = "1px dashed #3498db";
        
        // THE FIX: If the text is the default placeholder, empty it
        if (field.innerText === "Click to type...") {
            field.innerText = "";
        }
    });

    // 2. HIDE LINE when you click away (and restore placeholder if empty)
    field.addEventListener('blur', () => {
        field.style.border = "1px dashed transparent";
        
        // OPTIONAL: Restore placeholder if the user left it totally empty
        if (field.innerText.trim() === "") {
            field.innerText = "Click to type...";
        }
    });

    field.addEventListener('mousedown', (e) => {
        activeElement = field; 
        lastSelectedTextElement = field; 

        initialX = e.clientX - activeElement.offsetLeft;
        initialY = e.clientY - activeElement.offsetTop;

        // Ensure others hide their lines when this one is picked
        document.querySelectorAll('.manual-field').forEach(f => {
            if (f !== field) f.style.border = "1px dashed transparent";
        });
        
        field.style.border = "1px dashed #3498db";
        e.stopPropagation();
    });

    document.getElementById('canvas21').appendChild(field);
}



let qrGenerator = null;

function updateQRCode(text) {
    // 1. Update the image on the screen immediately [cite: 2026-01-21]
    const liveQR = document.getElementById('qr-image-live');
    if (liveQR) {
        liveQR.src = `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(text)}&size=150x150`;
    }

    // 2. Update the data payload for the API [cite: 2026-01-21]
    if (labelPayload[currentActiveStyle]) {
        labelPayload[currentActiveStyle].data.qr_data = text;
    }
    
    // 3. Sync the "Generate API" box live [cite: 2026-01-21]
    if (typeof generateAPI === "function") {
        generateAPI();
    }
}

// ==========================================
// STYLE 5 DYNAMIC VALIDATION & POPUP
// ==========================================

// This creates separate 'drawers' for each style's image
// Each style now has its own separate storage [cite: 2026-01-21]
let styleBackgrounds = {
    'style5': null,
    'style9': null
};
function handleBackgroundUpload(event) {

    const file = event.target.files[0];


    const canvas = document.getElementById("canvas21");


    
    // 1. Define the size mapping [cite: 2026-01-21]
    const sizes = { 
        "2.1": {w: 500, h: 250}, 
        "2.9": {w: 592, h: 250}, 
        "4.2": {w: 800, h: 600} 
    };

    // 2. Identify the active template based on canvas width [cite: 2026-01-21]
    const currentW = canvas.offsetWidth;
    const currentH = canvas.offsetHeight;
    let sizeLabel = "Unknown";
    let targetData = {w: currentW, h: currentH};

    for (let key in sizes) {
        if (Math.abs(sizes[key].w - currentW) < 10) {
            sizeLabel = key;
            targetData = sizes[key];
            break;
        }
    }

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.src = e.target.result;

            img.onload = function() {
                const imgW = img.width;
                const imgH = img.height;
                
                const isCanvasVertical = targetData.h > targetData.w;
                const isImageVertical = imgH > imgW;
                const frameType = isCanvasVertical ? "Vertical" : "Horizontal";
                const imgType = isImageVertical ? "Vertical" : "Horizontal";

                // --- VALIDATION: ORIENTATION & SIZE --- [cite: 2026-01-21]
                const targetRatio = targetData.w / targetData.h;
                const imageRatio = imgW / imgH;

                if (isCanvasVertical !== isImageVertical || Math.abs(targetRatio - imageRatio) > 0.1) {
                    const errorMessage = `
                        <div style="font-size:18px; margin-bottom:10px;">⚠️ <b>Invalid Image Dimensions</b></div>
                        <div style="background:rgba(0,0,0,0.2); padding:10px; border-radius:8px;">
                            ℹ️ Size must be <b>${targetData.w} × ${targetData.h} px</b> (${frameType})<br>
                            Got <b>${imgW} × ${imgH} px</b> (${imgType})
                        </div>
                        <div style="margin-top:10px; font-size:12px; opacity:0.9;">
                            Please crop your image to match the <b>${sizeLabel}"</b> template requirements.
                        </div>
                    `;
                    showTopWarning(errorMessage);
                    event.target.value = ""; 
                    return;
                }

               // --- SUCCESS: APPLY IMAGE --- [cite: 2026-01-21]
// Save the image ONLY to the currently active style [cite: 2026-01-21]
styleBackgrounds[currentActiveStyle] = e.target.result;

// Update the physical canvas background using that specific image [cite: 2026-01-21]
canvas.style.backgroundImage = `url('${styleBackgrounds[currentActiveStyle]}')`;
canvas.style.backgroundSize = "cover";
canvas.style.backgroundPosition = "center";





// Trigger the UI to refresh automatically [cite: 2026-01-21]
if (currentActiveStyle === 'style5' || currentActiveStyle === 'style9') {
    loadTemplate(currentActiveStyle);
}
                
                event.target.value = ""; 
            };
        };
        reader.readAsDataURL(file);
    }
}
/**
 * Renders the error popup at the top of the UI
 */
function showTopWarning(message) {
    let warningDiv = document.getElementById("style5-warning");
    
    if (!warningDiv) {
        warningDiv = document.createElement("div");
        warningDiv.id = "style5-warning";
        // Modern "Glassmorphism" Style: Dark blur, rounded, subtle border
        warningDiv.style = `
            position: fixed; 
            top: -100px; 
            left: 50%; 
            transform: translateX(-50%); 
            background: rgba(44, 62, 80, 0.95); 
            backdrop-filter: blur(10px);
            color: white; 
            padding: 16px 24px; 
            border-radius: 16px; 
            z-index: 10000; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.3); 
            transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55); 
            font-family: 'Segoe UI', Roboto, sans-serif; 
            border: 1px solid rgba(255,255,255,0.1);
            min-width: 380px;
        `;
        document.body.appendChild(warningDiv);
    }

    warningDiv.innerHTML = message;
    warningDiv.style.top = "30px"; // Slides down to a floating position
    warningDiv.style.opacity = "1";

    // Smooth exit
    setTimeout(() => {
        warningDiv.style.top = "-150px";
        warningDiv.style.opacity = "0";
    }, 6000);
}




document.getElementById('confirmTemplateBtn').addEventListener('click', function() {
    const canvasElement = document.getElementById("canvas21");
    const imagePreview = document.getElementById('imagePreview'); 
    const templateModal = document.getElementById('templateModal'); 
    
    // 1. DATASET CAPTURE - Get the exact locked dimensions we set at start
    const finalW = parseInt(canvasElement.width);
    const finalH = parseInt(canvasElement.height);

    // 2. HIDE UI HANDLES
    const uiElements = document.querySelectorAll('#resizer, #uploadBtn, .draggable');
    uiElements.forEach(el => {
        el.dataset.oldDisplay = el.style.display;
        el.style.outline = "none"; 
        if (el.id === 'resizer' || el.id === 'uploadBtn') el.style.display = 'none';
    });

    // 3. PRECISION CAPTURE (Fixes the 802x601 error)
    html2canvas(canvasElement, {
        useCORS: true,
        scale: 1, 
        width: finalW,   // Forces exact pixel width
        height: finalH,  // Forces exact pixel height
        logging: false,
        backgroundColor: "#ffffff" 
    }).then(canvasImage => {
        
        // 4. RESTORE UI FOR NEXT TIME
        uiElements.forEach(el => {
            if (el.id === 'resizer' || el.id === 'uploadBtn') {
                el.style.display = el.dataset.oldDisplay || 'block';
            }
        });

        // 5. THE FIX: Transfer to Main Container
        const finalDataURL = canvasImage.toDataURL("image/png");
        
        if (imagePreview) {
            imagePreview.src = finalDataURL;
            imagePreview.style.display = 'block'


        // This turns the template into a "Virtual File" (The Payload)
            canvasImage.toBlob((blob) => {
                window.myTemplateBlob = blob; 
                console.log("📦 Payload captured from Template Designer");
            }, 'image/png');
            // ---------------------------- 


   
            // Sync main UI text displays
            const wDisplay = document.getElementById('imageWidth');
            const hDisplay = document.getElementById('imageHeight');
            if (wDisplay) wDisplay.textContent = finalW;
            if (hDisplay) hDisplay.textContent = finalH;
        }

        // 6. AUTO-CLOSE MODAL
        if (templateModal) templateModal.style.display = 'none';


        showTopWarning("✅ Template applied! Ready for upload.");

        // 7. AUTO-UPDATE UI (Payload is ready)
        // This triggers your validation logic so the "Send" buttons light up
        if (typeof checkSizeAgainstRequirement === "function") {
            checkSizeAgainstRequirement();
        }
    });
});


// --- DRAG, RESIZE & STOP LOGIC ---
let dragging = false;
let resizing = false;
let startX, startY, startW, startH, offsetX, offsetY;

function startDragging(e) {
    // 1. Prevent "stickiness" by stopping default browser drag behavior
    if (e.target.id === 'resizer' || e.target.id === 'uploadBtn') return;
    
    const imgEl = document.getElementById('imageGroup');
    dragging = true;
    
    // Calculate offset so the image doesn't "jump" to the cursor corner
    offsetX = e.clientX - imgEl.offsetLeft;
    offsetY = e.clientY - imgEl.offsetTop;

    e.preventDefault(); 
}

function startResizing(e) {
    e.preventDefault();
    e.stopPropagation(); 
    resizing = true;
    
    startX = e.clientX;
    startY = e.clientY;
    
    const imgEl = document.getElementById('imageGroup');
    startW = imgEl.offsetWidth;
    startH = imgEl.offsetHeight;
}

// Global Mouse Move
window.onmousemove = (e) => {
    const imgEl = document.getElementById('imageGroup');
    if (!imgEl) return;

    if (dragging) {
        imgEl.style.right = 'auto'; 
        imgEl.style.bottom = 'auto';
        imgEl.style.left = (e.clientX - offsetX) + 'px';
        imgEl.style.top = (e.clientY - offsetY) + 'px';
    }

    if (resizing) {
        imgEl.style.right = 'auto';
        imgEl.style.bottom = 'auto';

        let newWidth = startW + (e.clientX - startX);
        let newHeight = startH + (e.clientY - startY);

        // Style 3 Circle Constraint
        if (imgEl.style.borderRadius === "50%") {
            let size = Math.max(newWidth, 50); 
            imgEl.style.width = size + 'px';
            imgEl.style.height = size + 'px';
        } else {
            if (newWidth > 50) imgEl.style.width = newWidth + 'px';
            if (newHeight > 50) imgEl.style.height = newHeight + 'px';
        }
    }
};

// --- THE CRITICAL FIX FOR THE STICKY CURSOR ---
window.onmouseup = () => {
    dragging = false;
    resizing = false;
};

// Also stop if mouse leaves the browser window
window.onmouseleave = () => {
    dragging = false;
    resizing = false;
};





// Function to show ONLY the active style in the editor
function showApiEditor() {
    const editor = document.getElementById('apiModal');
    const textarea = document.getElementById('apiTextarea');
    
    const currentSize = document.getElementById('inchSize').value;
    const uniqueKey = currentActiveStyle + "_" + currentSize;
    
    // THE FIX: If "style1_2.1" doesn't exist yet, find ANY version of style1
    const activeData = labelPayload[uniqueKey] || 
                       labelPayload[currentActiveStyle] || 
                       Object.values(labelPayload).find(item => item.template_id === currentActiveStyle);
    
    if (!activeData) {
        alert("No data found for this template!");
        return;
    }

    textarea.value = JSON.stringify(activeData, null, 4);
    editor.style.display = 'flex';
}

function closeApiEditor() {
    document.getElementById('apiModal').style.display = 'none';
}

// Function to take edited JSON and update ONLY the active style within the global object
function applyApiChanges() {
    const textarea = document.getElementById('apiTextarea');
    try {
        const newData = JSON.parse(textarea.value);
        
        // Use the size inside the JSON to create a unique name
        const sizeSuffix = newData.config.selectedSize;
        const uniqueKey = currentActiveStyle + "_" + sizeSuffix; 
        
        // Save it! (e.g., labelPayload["style1_4.2"] = newData)
        labelPayload[uniqueKey] = newData; 

        closeApiEditor();

        // RE-RENDER: This triggers the workspace change automatically [cite: 2026-01-21]
        loadTemplate(currentActiveStyle); 
        
        console.log("Updated " + uniqueKey + " successfully.");
    } catch (e) {
        alert("JSON Error: " + e.message);
    }
}




// This ensures only the CURRENT text has a box, and others are hidden [cite: 2026-01-29]
document.getElementById('canvas21').addEventListener('mousedown', function(e) {
    
    // 1. CLEAR: Remove the dashed box from everything else first [cite: 2026-01-29]
    document.querySelectorAll('.text-element').forEach(el => {
        el.style.outline = "none";
        el.style.border = "none";
    });

    // 2. FIND: Search for the nearest parent with the 'text-element' class [cite: 2026-01-29]
    const target = e.target.closest('.text-element');

    // 3. SELECT: If the user clicked a valid text part
    if (target && e.target.id !== 'canvas21') {
        lastSelectedTextElement = target; // This variable is used by applyTextColor() [cite: 2026-01-29]
        
        // 4. DRAW BOX: This creates the visual dashed blue selection [cite: 2026-01-21, 2026-01-29]
        target.style.outline = "2px dashed #3498db";
        target.style.outlineOffset = "4px";

        console.log("Selection active for:", target.getAttribute('data-field-id'));
    } else {
        // Clear selection if clicking empty background
        lastSelectedTextElement = null;
    }
});








function applyFontStyle(styleType) {
    if (!lastSelectedTextElement) return;
    
    // MUST call saveState here for Undo to work on styles [cite: 2026-01-29]
    saveState();

    if (styleType === "bold") {
        // Toggle Bold
        const isBold = lastSelectedTextElement.style.fontWeight === "bold" || lastSelectedTextElement.style.fontWeight === "900";
        lastSelectedTextElement.style.fontWeight = isBold ? "normal" : "bold";
    } else if (styleType === "italic") {
        // Toggle Italic
        const isItalic = lastSelectedTextElement.style.fontStyle === "italic";
        lastSelectedTextElement.style.fontStyle = isItalic ? "normal" : "italic";
    } else if (styleType === "underline") {
        // NEW: Toggle Underline [cite: 2026-01-29]
        const isUnderlined = lastSelectedTextElement.style.textDecoration === "underline";
        lastSelectedTextElement.style.textDecoration = isUnderlined ? "none" : "underline";
    } else if (styleType === "bold-italic") {
        lastSelectedTextElement.style.fontWeight = "bold";
        lastSelectedTextElement.style.fontStyle = "italic";
    }
}

// --- 4. KEYBOARD SHORTCUT ---
document.addEventListener('keydown', function(event) {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'z') {
        event.preventDefault(); 
        undoAction();
    }
});

// 1. THE CLICK DETECTOR: This picks the text or the background
document.getElementById('canvas21').addEventListener('mousedown', function(e) {
    const clickedText = e.target.closest('.text-element');
    
    // Clear all old blue dashed lines first
    document.querySelectorAll('.text-element, .manual-field').forEach(el => {
        el.style.outline = "none";
        if (el.parentElement) el.parentElement.style.border = "none";
    });

    if (clickedText) {
        lastSelectedTextElement = clickedText;
        activeElement = clickedText;

        // Show a dashed border on the box if it exists, otherwise on the text
        const parentBox = clickedText.parentElement;
        if (parentBox && parentBox !== canvas && parentBox.id !== 'field1') {
            parentBox.style.border = "2px dashed #3498db"; 
        } else {
            clickedText.style.outline = "2px dashed #3498db";
        }

        initialX = e.clientX - clickedText.offsetLeft;
        initialY = e.clientY - clickedText.offsetTop;
        e.stopPropagation();
    } else {
        // Clicked the empty space: Now we can change the main label background
        lastSelectedTextElement = null;
    }
});



// --- 1. SAVE STATE (Captures all styles including Underline) ---
// --- 1. SAVE STATE ---
function saveState() {
    // 1. Snapshot the data payload
    const snapshot = JSON.parse(JSON.stringify(labelPayload));
    
    // 2. Snapshot the visual styles of every text element
    const elementVisuals = {};
    document.querySelectorAll('.text-element').forEach(el => {
        const fieldId = el.getAttribute('data-field-id');
        if (fieldId) {
            const style = window.getComputedStyle(el);
            elementVisuals[fieldId] = {
                color: el.style.color || style.color,
                fontWeight: el.style.fontWeight || style.fontWeight,
                fontStyle: el.style.fontStyle || style.fontStyle,
                textDecoration: el.style.textDecoration || style.textDecoration,
                fontSize: el.style.fontSize || style.fontSize,
                // Capture background color of the box/container
                boxBackground: el.parentElement ? el.parentElement.style.backgroundColor : ''
            };
        }
    });

    // 3. Snapshot the main container color
    const mainContainer = document.querySelector('#field1 > div') || document.getElementById('canvas21');
    const containerColor = mainContainer ? mainContainer.style.backgroundColor : '';

    // 4. Snapshot manual fields
    const manualFields = Array.from(document.querySelectorAll('.manual-field')).map(field => ({
        text: field.innerText,
        top: field.style.top,
        left: field.style.left,
        color: field.style.color,
        display: field.style.display // Store if it was visible or hidden
    }));

    undoStack.push({ 
        payload: snapshot, 
        visuals: elementVisuals, 
        manualFields: manualFields,
        mainContainerColor: containerColor,
        styleId: currentActiveStyle
    });

    if (undoStack.length > 30) undoStack.shift();
}

// --- 2. UNDO ACTION (Step-by-Step Fix) ---
function undoAction() {
    if (undoStack.length <= 1) return;

    // Remove current state
    undoStack.pop(); 
    // Get the previous state
    const prevState = undoStack[undoStack.length - 1]; 
    
    // 1. Restore the data object
    labelPayload = JSON.parse(JSON.stringify(prevState.payload));

    // 2. Restore Main Container Color
    const mainContainer = document.querySelector('#field1 > div') || document.getElementById('canvas21');
    if (mainContainer) {
        mainContainer.style.backgroundColor = prevState.mainContainerColor;
    }

    // 3. Restore Element Visuals WITHOUT removing elements
    if (prevState.visuals) {
        Object.keys(prevState.visuals).forEach(fieldId => {
            const data = prevState.visuals[fieldId];
            const el = document.querySelector(`[data-field-id="${fieldId}"]`);
            if (el) {
                el.style.color = data.color;
                el.style.fontWeight = data.fontWeight;
                el.style.fontStyle = data.fontStyle;
                el.style.textDecoration = data.textDecoration;
                el.style.fontSize = data.fontSize;
                
                // Restore parent box background
                if (el.parentElement && el.parentElement.id !== 'canvas21') {
                    el.parentElement.style.backgroundColor = data.boxBackground;
                }
            }
        });
    }

    // 4. Fix Manual Fields (Restore text/position instead of deleting all)
    const currentManuals = document.querySelectorAll('.manual-field');
    prevState.manualFields.forEach((data, index) => {
        if (currentManuals[index]) {
            currentManuals[index].innerText = data.text;
            currentManuals[index].style.top = data.top;
            currentManuals[index].style.left = data.left;
            currentManuals[index].style.color = data.color;
            currentManuals[index].style.display = data.display;
        }
    });
}

// --- 3. INITIALIZATION (Saves first state) ---
window.onload = () => {
    saveState(); 
};

// --- 4. KEYBOARD SHORTCUT (Ctrl + Z) ---
document.addEventListener('keydown', function(event) {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'z') {
        event.preventDefault(); 
        undoAction();
    }
});




function saveManualToJSON() {
    const activeKey = currentActiveStyle; 
    const activeStyle = labelPayload[activeKey];
    const canvas = document.getElementById('canvas21');

    if (!activeStyle || !canvas) return;

    // --- CONFIG SYNC ---
    const viewSelect = document.getElementById('viewSelect') || document.querySelector('select');
    const sizeInput = document.getElementById('sizeInput') || document.querySelector('input[placeholder="40"]');

    if (viewSelect) activeStyle.config.orientation = viewSelect.value.toLowerCase();
    if (sizeInput) activeStyle.config.selectedSize = sizeInput.value;

    // Save Background
    activeStyle.canvas.background = canvas.style.backgroundColor;

    // --- QR DATA SYNC --- [cite: 2026-01-21]
    // Grabs current input value to ensure it matches the QR image
    const qrInputField = document.getElementById('qrInput');
    if (qrInputField && activeStyle.data) {
        activeStyle.data.qr_data = qrInputField.value;
    }

    // --- ELEMENT LOOP ---
    document.querySelectorAll('.text-element').forEach(el => {
        const fieldId = el.getAttribute('data-field-id');
        const content = el.innerText;

        if (fieldId && activeStyle.elements[fieldId]) {
            activeStyle.elements[fieldId].color = el.style.color;
            
            // Positioning math based on your 500x250 canvas
            const rx = (canvas.width > canvas.height ? canvas.width : canvas.height) / 500;
            const ry = (canvas.width > canvas.height ? canvas.height : canvas.width) / 250;
            
            const parent = el.closest('div[style*="position:absolute"]');
            if (parent && parent.style.left) {
                activeStyle.elements[fieldId].x = parseFloat(parent.style.left) / rx;
                activeStyle.elements[fieldId].y = parseFloat(parent.style.top) / ry;
            }

            // --- DATA MAPPING ---
            if (activeKey === 'style1') {
                if (fieldId === 'title') activeStyle.data.title = content;
                if (fieldId === 'subtitle') activeStyle.data.subtitle = content;
                if (fieldId === 'offer') activeStyle.data.offer = content;
                if (fieldId === 'date') activeStyle.data.date = content;
            } 
            else if (activeKey === 'style4') {
                if (fieldId === 'brand') activeStyle.data.brand_name = content;
                if (fieldId === 'tagline') activeStyle.data.tagline_text = content;
            }
            else if (activeKey === 'style6') {
                if (fieldId === 'title') activeStyle.data.title = content;
                if (fieldId === 'price_tag') activeStyle.data.price = content;
                if (fieldId === 'footer') activeStyle.data.footer = content;
            }
        }
    });

    // Update the Generate API box silently [cite: 2026-01-21]
    if (typeof generateAPI === "function") {
        generateAPI();
    }
}



function initInteractiveElements() {
    const wrappers = document.querySelectorAll('.draggable-wrapper');

    wrappers.forEach(wrapper => {
        wrapper.onmousedown = function(e) {
            // Cut off the background drag [cite: 2026-01-21]
            e.stopPropagation(); 
            e.preventDefault();

            let isResizing = e.target.classList.contains('resizer');
            let startX = e.clientX;
            let startY = e.clientY;
            let startW = wrapper.offsetWidth;
            let startH = wrapper.offsetHeight;
            let startL = wrapper.offsetLeft;
            let startT = wrapper.offsetTop;

            document.onmousemove = function(ev) {
                ev.stopPropagation(); 
                
                if (isResizing) {
                    wrapper.style.width = (startW + (ev.clientX - startX)) + 'px';
                    wrapper.style.height = (startH + (ev.clientY - startY)) + 'px';
                } else {
                    wrapper.style.left = (startL + (ev.clientX - startX)) + 'px';
                    wrapper.style.top = (startT + (ev.clientY - startY)) + 'px';
                }
                saveManualToJSON(); // Live update API payload [cite: 2026-01-21]
            };

            document.onmouseup = function() {
                document.onmousemove = null;
            };
        };
    });
}



function showBackgroundAlert() {
    let alertDiv = document.getElementById("bg-alert-popup");
    
    if (!alertDiv) {
        alertDiv = document.createElement("div");
        alertDiv.id = "bg-alert-popup";
        alertDiv.style = `
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            background: #2c3e50; color: white; padding: 12px 24px;
            border-radius: 50px; font-family: sans-serif; font-size: 13px;
            font-weight: 700; z-index: 10000; box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: all 0.4s ease; opacity: 0; pointer-events: none;
        `;
        document.body.appendChild(alertDiv);
    }

    // UPDATED: Generic message that works for any style [cite: 2026-01-21]
    alertDiv.innerHTML = `✨ <b>Custom Background Mode Enabled!</b>`;
    alertDiv.style.opacity = "1";
    alertDiv.style.top = "40px";

    setTimeout(() => {
        alertDiv.style.opacity = "0";
        alertDiv.style.top = "20px";
    }, 3000);
}



</script>



     </body>
</html> 
