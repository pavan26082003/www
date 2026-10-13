<?php 
// Assuming $node_ids is defined somewhere above this code
?>
<!DOCTYPE html>
<html>
<head>
<title>Label Designer</title>
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
  font-family: 'Segoe UI', sans-serif;
  background: linear-gradient(135deg, #e3f2fd, #ffffff);
  margin: 0;
}

.container {
  display: flex;
  width: 100%;
  height: calc(100vh - 50px);
}

.left-panel {
  width: 90px;
  background: black;
  padding-top: 25px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 25px;
}

.right-panel {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
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

#nodeSelect, #frameType, #inchSize { width: 100px; height: 30px; }

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
  width: 60%;
  max-width: 450px;
  aspect-ratio: 1 / 1;
  background-color: rgba(0,0,0,0.5);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.preview-container {
  width: 300px;
  height: 300px;
  border-radius: 6px;
  position: relative;
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
.navbar {
  width: 100%;
  height: 50px;
  background-color: black;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding-left: 20px;
  box-sizing: border-box;
  color: white;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 4px 20px rgba(0, 123, 255, 0.8);
}

.icon-only-button {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  margin-left: -5px;
  font-size: 10px;
}

.icon-only-button i {
  font-size: 24px;
  margin-bottom: 5px;
}

.icon-upload-label {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  margin-left: -35px;
  background: none;
  border: none;
  color: white;
  font-size: 10px;
  width: 100px;
  height: 60px;
  margin-bottom: 15px;
}

.icon-upload-label i {
  font-size: 24px;
  margin-bottom: 5px;
}

.icon-dropdown-wrapper {
  position: relative;
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  width: 60px;
  margin-bottom: 15px;
  margin-left: -15px;
}

.icon-dropdown-wrapper select {
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}

.icon-dropdown-wrapper .dropdown-icon {
  font-size: 24px;
  color: white;
}

.icon-dropdown-wrapper .dropdown-text {
  margin-top: 5px;
  font-size: 9px;
  text-align: center;
  color: white;
  pointer-events: none;
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
  width: 100%;
  height: auto;
}

/* Progress bar */
.progress-container {
  display: none;
  width: 300px;
  margin-top: 20px;
}

.progress-bar {
  background-color: #ddd;
  height: 20px;
  border-radius: 10px;
  overflow: hidden;
}

.progress-fill {
  background-color: #4CAF50;
  height: 100%;
  width: 0%;
  transition: width 0.3s ease;
}

.progress-text {
  text-align: center;
  font-size: 12px;
  margin-top: 5px;
  color: #333;
}
</style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar"><span style="font-weight:bold; margin-right:-20px;">Label Designer</span></nav>

<div class="container">
  <!-- LEFT PANEL -->
  <div class="left-panel">
    <button id="openProductModal" class="icon-only-button" 
        style="display:flex; flex-direction:column; align-items:center; justify-content:center;" type="button">
      <i class="bi bi-box"></i>
      <span style="font-size:8px;">View Product Info</span>
    </button>
    
    <div class="icon-dropdown-wrapper">
      <i class="bi bi-menu-up dropdown-icon"></i>
      <div class="dropdown-text">Select Node</div>
      <select id="nodeSelect" required>
        <option value="">Select Node</option>
        <?php foreach ($node_ids as $id): ?>
          <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($id) ?></option>
        <?php endforeach; ?>
        <option value="FFFFFFFFFFFFFFFF7EC200000005E800">Default Node</option>
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
      <span style="font-size:8px">Upload Image</span>
    </label>
    <input type="file" id="imageUpload" accept="image/*" style="display:none;">

    <!-- Clear Image Button -->
    <button type="button" id="clearImageBtn" class="clear-image-btn" style="display:none;">
      <i class="bi bi-trash"></i>
      <span style="font-size:8px">Clear Image</span>
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
  </div>

  <!-- RIGHT PANEL -->
  <div class="right-panel">
    <div class="preview-container-outer">
      <div class="preview-container">
        <img id="imagePreview" style="border: 2px solid black; margin-left:-250px;" src="#" alt="Preview">
      </div>
      
      <div style="margin-right:-570px; margin-bottom:350px;">
        <div style="font-size:10px; color:white;">Width: <span id="imageWidth">0</span> px</div>
        <div style="font-size:10px; color:white;">Height: <span id="imageHeight">0</span> px</div>
        <div id="requiredDimensions" class="dimension-display" style="display:none;">
          Required: <span id="requiredSizeText">-</span>
        </div>
      </div>
    </div>
 
    <div class="button-group">  
      <button type="button" id="previewBtn" class="btn-info">
        <i class="bi bi-eye"></i> Preview Original
      </button>
      <button type="button" id="resetBtn" class="btn btn-warning">
        Reset Image
      </button>
      <button type="button" id="applyBtn" class="btn-primary">
        <i class="bi bi-magic"></i> Apply Changes
      </button>
      <button type="button" id="validateBtn"><i class="bi bi-check-circle"></i> Validate</button>
      <!--button type="button" id="saveBtn" disabled><i class="bi bi-save"></i> Save</button-->
      <button type="button" id="saveBtn" disabled>Save</button>
      <button type="button" id="downloadBtn"><i class="bi bi-download"></i> Download</button>
    </div>
    
    <!-- Progress Bar -->
    <div class="progress-container" id="progressContainer">
      <div class="progress-bar">
        <div class="progress-fill" id="progressFill"></div>
      </div>
      <div class="progress-text" id="progressText">0%</div>
    </div>
  </div>
</div>

<!-- Product Modal -->
<div id="productModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h4 style="margin-bottom:10px; font-weight:bold;">Product Information</h4>
    <p><strong>Model Name:</strong> <?= htmlspecialchars($modelname ?? '') ?></p>
    <p><strong>Model No:</strong> <?= htmlspecialchars($modelno ?? '') ?></p>
    <p><strong>Product Name:</strong> <?= htmlspecialchars($category ?? '') ?></p>
  </div>
</div>

<!-- Color Mismatch Modal -->
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
            <!-- Dynamic content will be inserted here -->
        </div>
        
        <div style="margin-top:25px; display:flex; justify-content:space-between;">
            <button type="button" onclick="closeColorModal()" 
                    style="padding:10px 15px; background:#ccc; color:#333; border:none; border-radius:4px; cursor:pointer;">
                Cancel
            </button>
            <button type="button" id="proceedColorMappingBtn" disabled
                    style="padding:10px 15px; background:#28a745; color:white; border:none; border-radius:4px; cursor:pointer;">
                Proceed with Mapping
            </button>
        </div>
    </div>
</div>

<!-- Save Summary -->
<div id="saveSummary" style="display:none; margin-top: 15px; padding: 10px; border: 1px solid #ccc; background: #f9f9f9; color: #333;">
    <h4 style="margin-top:0">Saved Data Details:</h4>
    <div id="summaryContent"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// =========================================================================
// 1. GLOBAL VARIABLE DECLARATIONS
// =========================================================================
let uploadedImage = null; 
let originalImageData = null;
let lastDetectedColorRGBs = {};
let isImageValid = false;
let selectedInchSize = null;
let lastValidatedSrc = "";
let originalImageSource = null;
let uploadedFileName = "";
let imageBase64Data = ""; // Store base64 image for sending

const TARGET_COLORS = {
    "White": [255, 255, 255],
    "Yellow": [255, 255, 0],
    "Black": [0, 0, 0],
    "Red": [255, 0, 0],
};

const sizeRequirements = {
    "2.1": { width: 250, height: 500, text: "250 × 500 px" },
    "2.9": { width: 250, height: 592, text: "250 × 592 px" },
    "4.2": { width: 800, height: 600, text: "800 × 600 px" }
};

// =========================================================================
// 2. INITIALIZATION
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
    const progressContainer = document.getElementById('progressContainer');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    
    // =========================================================================
    // 3. EVENT HANDLERS
    // =========================================================================
    
    // Size selection
    if (inchSize) {
        inchSize.addEventListener('change', (e) => {
            selectedInchSize = e.target.value;
            const req = sizeRequirements[selectedInchSize];
            if (req) {
                if (requiredDimensions) requiredDimensions.style.display = 'block';
                if (requiredSizeText) requiredSizeText.textContent = req.text;
                if (imagePreview.src) checkSizeAgainstRequirement();
            } else {
                if (requiredDimensions) requiredDimensions.style.display = 'none';
                selectedInchSize = null;
            }
        });
    }
    
    // Image upload
    if(imageUpload) {
        imageUpload.addEventListener('change', (e) => {
            if (!selectedInchSize) { 
                alert("Select size first!"); 
                e.target.value = ""; 
                return; 
            }
            const file = e.target.files[0];
            if (!file) return;
            if (clearImageBtn) clearImageBtn.style.display = 'inline-block';
            uploadedFileName = file.name;
            
            const reader = new FileReader();
            reader.onload = (ev) => {
                const img = new Image();
                img.onload = () => {
                    originalImageSource = img; 
                    originalImageData = ev.target.result;
                    imageBase64Data = originalImageData; // Store for sending
                    imagePreview.src = originalImageData;
                    imagePreview.style.display = 'block';
                    
                    const wEl = document.getElementById('imageWidth');
                    const hEl = document.getElementById('imageHeight');
                    if(wEl) wEl.textContent = img.naturalWidth;
                    if(hEl) hEl.textContent = img.naturalHeight;
                    
                    checkSizeAgainstRequirement();
                    isImageValid = false;
                    if(saveBtn) saveBtn.disabled = false;
                };
                img.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
    }
    
    // Validation
    if(validateBtn) {
        validateBtn.addEventListener('click', () => {
            if (!imagePreview.src) return;
            if (!checkSizeAgainstRequirement()) return;
            
            // For now, just mark as valid
            isImageValid = true;
            lastValidatedSrc = imagePreview.src;
            
            alert("✅ Image validated successfully!");
            if(saveBtn) saveBtn.disabled = false;
        });
    }
    
    // Save button (AJAX call to your controller)
    if(saveBtn) {
        saveBtn.addEventListener('click', function () {

    if (!imageUpload.files.length) {
        alert("Please upload an image first!");
        return;
    }

    const formData = new FormData();

    // MUST MATCH $_FILES['imageUpload']
    formData.append('imageUpload', imageUpload.files[0]);

    formData.append('node_id', nodeSelect.value);
    formData.append('width', imagePreview.naturalWidth);
    formData.append('height', imagePreview.naturalHeight);
    formData.append('frame', frameType.value);

    saveBtn.disabled = true;
    saveBtn.textContent = "Saving...";

    $.ajax({
        url: "<?php echo get_uri('items/label_controller'); ?>",
        type: "POST",
        data: formData,
        processData: false, // 🚨 REQUIRED
        contentType: false, // 🚨 REQUIRED
        success: function (res) {
            console.log(res);
            alert("✅ Saved successfully");
            saveBtn.textContent = "Save to Database";
            saveBtn.disabled = false;
        },
        error: function () {
            alert("❌ Save failed");
            saveBtn.textContent = "Save to Database";
            saveBtn.disabled = false;
        }
    });
});
    }



    
    // Download button
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            const preview = document.getElementById('imagePreview');
            if (!preview.src || preview.src === "" || preview.src.includes(window.location.host)) {
                alert("Please upload and validate an image first!");
                return;
            }
            const link = document.createElement('a');
            link.download = 'label_design_' + Date.now() + '.png';
            link.href = preview.src;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }
    
    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (originalImageData) {
                imagePreview.src = originalImageData;
                imageBase64Data = originalImageData;
                isImageValid = false;
                if (saveBtn) saveBtn.disabled = true;
            } else {
                alert("No original image found to reset to.");
            }
        });
    }
    
    // Apply button
    if (applyBtn) {
        applyBtn.addEventListener('click', function() {
            // Update the base64 data with current preview
            if (imagePreview.src && imagePreview.src !== window.location.href) {
                lastValidatedSrc = imagePreview.src;
                alert("✅ Changes applied!");
                applyBtn.disabled = true;
                setTimeout(() => { applyBtn.disabled = false; }, 1000);
            }
        });
    }
    
    // Preview button
    if (previewBtn) {
        previewBtn.addEventListener('mousedown', () => { 
            if (originalImageData) {
                imagePreview.src = originalImageData;
                imageBase64Data = originalImageData;
            }
        });
        previewBtn.addEventListener('mouseup', () => { 
            if (lastValidatedSrc) {
                imagePreview.src = lastValidatedSrc;
                imageBase64Data = lastValidatedSrc;
            }
        });
    }
    
    // Node select display
    if (nodeSelect && dropdownText) {
        nodeSelect.addEventListener('change', function() {
            const selectedText = this.options[this.selectedIndex].text;
            if (this.value !== "") {
                dropdownText.textContent = selectedText;
            } else {
                dropdownText.textContent = "Select Node";
            }
        });
    }
    
    // Clear image
    function clearImage() {
        const fileInput = document.getElementById('imageUpload');
        const preview = document.getElementById('imagePreview');
        const clearBtn = document.getElementById('clearImageBtn');
        const saveBtn = document.getElementById('saveBtn');

        if (fileInput) fileInput.value = "";
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
        imageBase64Data = "";

        if (clearBtn) clearBtn.style.display = 'none';
        if (saveBtn) {
            saveBtn.disabled = true;
        }
        alert("✨ Image cleared successfully.");
    }

    const clearBtnEl = document.getElementById('clearImageBtn');
    if (clearBtnEl) {
        clearBtnEl.onclick = clearImage;
    }
    
    // Modal handlers
    const productModal = document.getElementById('productModal');
    const openProductModal = document.getElementById('openProductModal');
    const closeModal = document.querySelector('.close');
    
    if (openProductModal) {
        openProductModal.onclick = function() {
            productModal.style.display = 'block';
        }
    }
    
    if (closeModal) {
        closeModal.onclick = function() {
            productModal.style.display = 'none';
        }
    }
    
    window.onclick = function(event) {
        if (event.target == productModal) {
            productModal.style.display = 'none';
        }
        if (event.target == document.getElementById('colorMismatchModal')) {
            closeColorModal();
        }
    }
});

// =========================================================================
// 4. HELPER FUNCTIONS
// =========================================================================
function checkSizeAgainstRequirement() {
    const preview = document.getElementById('imagePreview');
    if (!selectedInchSize || !preview.src) return false;
    const req = sizeRequirements[selectedInchSize];
    if (preview.naturalWidth !== req.width || preview.naturalHeight !== req.height) {
        alert(`❌ Size must be ${req.text}. Got ${preview.naturalWidth}x${preview.naturalHeight}`);
        return false;
    }
    return true;
}

function validateBeforeSave() {
    const nodeSelect = document.getElementById('nodeSelect');
    const inchSize = document.getElementById('inchSize');
    const frameType = document.getElementById('frameType');
    const imagePreview = document.getElementById('imagePreview');
    
    if (!nodeSelect || !nodeSelect.value) {
        alert("Please select a Node ID!");
        return false;
    }
    
    if (!inchSize || !inchSize.value) {
        alert("Please select a size!");
        return false;
    }
    
    if (!frameType || !frameType.value) {
        alert("Please select a frame type!");
        return false;
    }
    
    if (!imagePreview || !imagePreview.src || imagePreview.src === window.location.href) {
        alert("Please upload an image!");
        return false;
    }
    
    if (!checkSizeAgainstRequirement()) {
        return false;
    }
    
    return true;
}

function showProgress(show) {
    const progressContainer = document.getElementById('progressContainer');
    if (progressContainer) {
        progressContainer.style.display = show ? 'block' : 'none';
    }
    if (!show) {
        updateProgress(0);
    }
}

function updateProgress(percent) {
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    
    if (progressFill) {
        progressFill.style.width = percent + '%';
    }
    if (progressText) {
        progressText.textContent = Math.round(percent) + '%';
    }
}

function closeColorModal() {
    const modal = document.getElementById('colorMismatchModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function showSaveSummary(response) {
    const summaryDiv = document.getElementById('saveSummary');
    const summaryContent = document.getElementById('summaryContent');
    
    if (summaryDiv && summaryContent) {
        summaryContent.innerHTML = `
            <div style="margin-bottom: 10px;"><strong>Save Successful:</strong></div>
            <table style="width:100%; border-collapse: collapse; margin-top: 10px; text-align: left; font-size: 12px;">
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 5px; font-weight:bold;">Status:</td>
                    <td style="padding: 5px; color:green;">✅ ${response.status}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 5px; font-weight:bold;">Message:</td>
                    <td style="padding: 5px;">${response.msg || 'Success'}</td>
                </tr>
                ${response.path ? `
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 5px; font-weight:bold;">File Path:</td>
                    <td style="padding: 5px;">${response.path}</td>
                </tr>` : ''}
                ${response.file_name ? `
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 5px; font-weight:bold;">File Name:</td>
                    <td style="padding: 5px;">${response.file_name}</td>
                </tr>` : ''}
                ${response.dimensions ? `
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 5px; font-weight:bold;">Dimensions:</td>
                    <td style="padding: 5px;">${response.dimensions}</td>
                </tr>` : ''}
                ${response.node_id ? `
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 5px; font-weight:bold;">Node ID:</td>
                    <td style="padding: 5px;">${response.node_id}</td>
                </tr>` : ''}
                ${response.ota_response ? `
                <tr>
                    <td style="padding: 5px; font-weight:bold;">OTA Response:</td>
                    <td style="padding: 5px;">${response.ota_response}</td>
                </tr>` : ''}
            </table>
            <div style="margin-top: 15px; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px;">
                <strong>Save completed at:</strong> ${response.timestamp || new Date().toLocaleString()}
            </div>
        `;
        summaryDiv.style.display = 'block';
        
        // Auto-hide after 10 seconds
        setTimeout(() => {
            summaryDiv.style.display = 'none';
        }, 10000);
    }
}

// Helper function to get URL parameters
function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name) || '';
}
</script>

</body>
</html>