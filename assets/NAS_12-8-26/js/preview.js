// assets/NAS/js/preview.js
// Preview Modal Functionality - Full File Support

if (!window.API_BASE) {
    console.warn('⚠️ NAS API base is not configured yet. Load config.js before using this module.');
}

console.log('📄 preview.js loading...');

// ✅ Use window.API_BASE directly (no const declaration)
// Just use window.API_BASE wherever needed

// Add spinning animation style
if (!document.getElementById('spinStyles')) {
    const style = document.createElement('style');
    style.id = 'spinStyles';
    style.textContent = `
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .preview-spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #e8eaed;
            border-top-color: #1a73e8;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }
    `;
    document.head.appendChild(style);
}


function showPreviewModal(file) {
    console.log('🖼️ Showing preview for:', file.name);
    console.log('📋 Full file data:', JSON.stringify(file, null, 2));
    
    const modal = document.getElementById('previewModal');
    if (!modal) {
        console.error('❌ Preview modal not found');
        return;
    }
    
    const content = document.getElementById('previewContent');
    const fileNameEl = document.getElementById('previewFileName');
    const fileSizeEl = document.getElementById('previewFileSize');
    const fileTypeEl = document.getElementById('previewFileType');
    const downloadBtn = document.getElementById('previewDownloadBtn');
    
    const fileName = file.name || file.original_name || 'Unknown';
    const fileSize = file.size || file.file_size || 0;
    const fileType = file.type || file.file_type || 'application/octet-stream';
    const fileId = file.id;
    
    // ✅ DEBUG: Log all permission-related properties
    console.log('🔍 PERMISSION DEBUG:');
    console.log('  - file.can_download:', file.can_download);
    console.log('  - file.permission:', file.permission);
    console.log('  - file.is_owner:', file.is_owner);
    console.log('  - file.is_shared:', file.is_shared);
    console.log('  - file.is_direct_share:', file.is_direct_share);
    
    // ✅ Determine permissions
    const canDownload = file.can_download !== undefined ? file.can_download : true;
    const permission = (file.permission || 'view').toLowerCase().trim();
    
    // ✅ Check if view-only (explicitly check permission === 'view' or 'view-only')
    const isViewOnly = (permission === 'view' || permission === 'view-only' || permission === 'none') && !canDownload;
    
    console.log('🔍 PDF Preview Debug:');
    console.log('  - permission:', permission);
    console.log('  - canDownload:', canDownload);
    console.log('  - isViewOnly:', isViewOnly);
    console.log('  - file name:', fileName);
    
    // Set file info
    if (fileNameEl) fileNameEl.textContent = fileName;
    if (fileSizeEl) fileSizeEl.textContent = 'Size: ' + formatFileSize(fileSize);
    if (fileTypeEl) fileTypeEl.textContent = 'Type: ' + (fileType || 'Unknown');
    
    // ✅ Setup download button based on permission
    if (downloadBtn) {
        if (!isViewOnly && canDownload) {
            console.log('✅ Download button: ENABLED');
            downloadBtn.style.display = 'inline-flex';
            downloadBtn.style.opacity = '1';
            downloadBtn.style.cursor = 'pointer';
            downloadBtn.className = 'preview-btn download';
            downloadBtn.innerHTML = '<i class="fas fa-download"></i> <span>Download</span>';
            downloadBtn.disabled = false;
            downloadBtn.title = 'Download file';
            downloadBtn.onclick = function(e) {
                e.stopPropagation();
                window.location.href = window.API_BASE + '/files/' + fileId + '/download';
            };
        } else {
            console.log('❌ Download button: DISABLED (view-only mode)');
            downloadBtn.style.display = 'inline-flex';
            downloadBtn.style.opacity = '0.5';
            downloadBtn.style.cursor = 'not-allowed';
            downloadBtn.className = 'preview-btn download disabled';
            downloadBtn.innerHTML = '<i class="fas fa-eye"></i> <span>View Only</span>';
            downloadBtn.disabled = true;
            downloadBtn.title = 'You have view-only permission. Download is disabled.';
            downloadBtn.onclick = null;
        }
    }
    
    // ✅ Show view-only indicator
    const permissionIndicator = document.getElementById('previewPermissionIndicator');
    if (permissionIndicator) {
        if (isViewOnly) {
            console.log('🔒 Showing View Only badge');
            permissionIndicator.style.display = 'inline-block';
            permissionIndicator.innerHTML = '🔒 View Only';
            permissionIndicator.className = 'preview-permission-badge view-only';
        } else {
            console.log('🔓 Showing Full Access badge');
            permissionIndicator.style.display = 'inline-block';
            permissionIndicator.innerHTML = '✅ Full Access';
            permissionIndicator.className = 'preview-permission-badge full-access';
        }
    }
    
    // Determine file type and render preview
    const extension = (fileName.split('.').pop() || '').toLowerCase();
    const mimeType = fileType.toLowerCase();
    
    const previewUrl = window.API_BASE + '/files/' + fileId + '/preview';
    const downloadUrl = window.API_BASE + '/files/' + fileId + '/download';
    
    console.log('📄 File extension:', extension);
    console.log('📄 MIME type:', mimeType);
    console.log('📄 Preview URL:', previewUrl);
    
    // ✅ Render based on file type with permission
    if (extension === 'pdf' || mimeType === 'application/pdf') {
        console.log('📄 Rendering PDF preview with isViewOnly:', isViewOnly);
        renderPdfPreview(content, previewUrl, fileName, isViewOnly);
    } else if (['pptx', 'ppt', 'docx', 'doc'].includes(extension)) {
        renderOfficePreview(content, fileId, fileName, isViewOnly);
    } else if (['xlsx', 'xls'].includes(extension)) {
        renderExcelPreview(content, fileId, fileName, fileSize, !isViewOnly, downloadUrl);
    } else if (mimeType.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'].includes(extension)) {
        renderImagePreview(content, previewUrl, fileName);
    } else if (mimeType.startsWith('video/') || ['mp4', 'webm', 'avi', 'mkv', 'mov', 'wmv', 'flv'].includes(extension)) {
        renderVideoPreview(content, previewUrl, fileName);
    } else if (mimeType.startsWith('audio/') || ['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma'].includes(extension)) {
        renderAudioPreview(content, previewUrl, fileName, fileSize);
    } else if (['txt', 'js', 'css', 'html', 'htm', 'json', 'xml', 'php', 'sql', 'py', 'java', 'cpp', 'c', 'h', 'sh', 'bash', 'rb', 'go', 'rs', 'ts', 'jsx', 'tsx', 'vue'].includes(extension)) {
        renderTextPreview(content, fileId, fileName, fileSize);
    } else if (['zip', 'rar', '7z', 'tar', 'gz', 'bz2'].includes(extension)) {
        renderArchivePreview(content, fileName, fileSize, !isViewOnly, downloadUrl);
    } else {
        renderGenericPreview(content, fileName, fileSize, fileType, !isViewOnly, downloadUrl);
    }
    
    // Activate modal
    console.log('📌 Activating modal...');
    modal.classList.add('active');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    console.log('✅ Modal activated');
}

function loadOfficePdfPreview(content, fileId, fileName) {
    const timestamp = Date.now();
    const previewUrl = window.API_BASE + '/preview/convert?file_id=' + fileId + '&_=' + timestamp;
    console.log('📄 Office PDF Preview URL:', previewUrl);
    
    content.style.cssText = `
        width: 100%;
        height: 100%;
        display: flex;
        align-items: stretch;
        justify-content: stretch;
        padding: 0;
        margin: 0;
        overflow: hidden;
        background: #525659;
    `;
    
    content.innerHTML = `
        <div style="width:100%;height:100%;background:#525659;position:relative;display:flex;flex-direction:column;">
            <div style="position:absolute;top:16px;right:16px;z-index:20;display:flex;gap:8px;flex-wrap:wrap;">
                <button onclick="window.open('${previewUrl}', '_blank')" 
                        style="padding:8px 16px;background:#1a73e8;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:6px;box-shadow:0 2px 8px rgba(0,0,0,0.3);">
                    <i class="fas fa-external-link-alt"></i> Full Screen
                </button>
                <button onclick="closePreviewModal()" 
                        style="padding:8px 16px;background:rgba(0,0,0,0.7);color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:6px;box-shadow:0 2px 8px rgba(0,0,0,0.3);">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
            
            <div style="width:100%;height:100%;background:white;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                <object data="${previewUrl}#toolbar=1&navpanes=1&scrollbar=1" 
                        type="application/pdf"
                        style="width:100%;height:100%;border:none;background:white;min-height:400px;">
                    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;padding:40px;color:#202124;text-align:center;">
                        <i class="fas fa-file-pdf" style="font-size:64px;color:#ea4335;margin-bottom:16px;display:block;"></i>
                        <h3 style="margin:0 0 8px;font-size:18px;font-weight:500;">Preview Unavailable</h3>
                        <p style="color:#5f6368;margin-bottom:16px;">Your browser doesn't support embedded PDF preview.</p>
                        <a href="${previewUrl}" target="_blank" style="color:#1a73e8;text-decoration:underline;font-size:16px;font-weight:500;">Click here to view</a>
                        <button onclick="closePreviewModal()" style="margin-top:16px;padding:8px 20px;background:#f1f3f4;color:#5f6368;border:none;border-radius:4px;cursor:pointer;">Close</button>
                    </div>
                </object>
            </div>
        </div>
    `;
}

function loadOfficePdfPreviewDirect(content, fileId, fileName) {
    const previewUrl = window.API_BASE + '/preview/convert?file_id=' + fileId;
    console.log('📄 Direct PDF Preview URL:', previewUrl);
    
    // Direct attempt to load the PDF
    content.innerHTML = `
        <div style="width:100%;height:100%;background:#525659;position:relative;">
            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);z-index:5;color:white;text-align:center;">
                <div class="preview-spinner" style="border-color:rgba(255,255,255,0.3);border-top-color:#fff;"></div>
                <p style="margin-top:16px;color:#e8eaed;">Loading PDF...</p>
            </div>
            <iframe src="${previewUrl}#toolbar=1&navpanes=1&scrollbar=1" 
                    style="width:100%;height:100%;border:none;background:white;"
                    allowfullscreen>
            </iframe>
        </div>
    `;
}

function renderPdfPreview(content, previewUrl, fileName, isViewOnly) {
    console.log('📄 renderPdfPreview called with:');
    console.log('  - previewUrl:', previewUrl);
    console.log('  - fileName:', fileName);
    console.log('  - isViewOnly:', isViewOnly);
    
    // ✅ Build the URL with toolbar parameters based on permission
    let finalUrl = previewUrl;
    
    if (isViewOnly) {
        // ✅ View-only mode: Disable toolbar completely
        finalUrl = previewUrl + '#toolbar=0&navpanes=0&scrollbar=0';
        console.log('🔒 View-only mode: toolbar disabled');
        console.log('📄 Final URL with toolbar=0:', finalUrl);
    } else {
        // ✅ Full access: Keep toolbar enabled
        finalUrl = previewUrl + '#toolbar=1&navpanes=1&scrollbar=1';
        console.log('🔓 Full access: toolbar enabled');
        console.log('📄 Final URL with toolbar=1:', finalUrl);
    }
    
    // Clear content and set styles
    content.style.cssText = `
        width: 100%;
        height: 100%;
        padding: 0;
        margin: 0;
        overflow: hidden;
        background: #525659;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    
    // ✅ Render the PDF with the appropriate URL parameters
    content.innerHTML = `
        <iframe src="${finalUrl}" 
                style="width:100%;height:100%;border:none;background:white;"
                allowfullscreen>
        </iframe>
    `;
    
    console.log('✅ PDF preview rendered with final URL:', finalUrl);
}


// ============================================
// OFFICE PREVIEW (PPT, DOCX) - Convert to PDF first
// ============================================

function renderOfficePreview(content, fileId, fileName, isViewOnly) {
    console.log('📄 Office preview for:', fileName);
    console.log('🔒 Is view-only:', isViewOnly);
    
    // ✅ Show loading
    content.innerHTML = `
        <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#f1f3f4;padding:20px;">
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;background:white;border-radius:12px;padding:40px;max-width:500px;width:100%;text-align:center;border:2px solid #e8eaed;">
                <div class="preview-spinner"></div>
                <h3 style="margin:0 0 8px;font-size:18px;font-weight:500;color:#202124;">Converting to PDF...</h3>
                <p style="margin:4px 0;color:#5f6368;font-size:14px;">${escapeHtml(fileName)}</p>
                <p style="margin:4px 0 16px;color:#9aa0a6;font-size:13px;">This may take a few moments</p>
                <button onclick="closePreviewModal()" style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:#f1f3f4;color:#5f6368;border:none;border-radius:6px;font-size:15px;font-weight:500;cursor:pointer;">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    `;
    
    // ✅ Call the conversion endpoint
    const convertUrl = window.API_BASE + '/preview/convert?file_id=' + fileId + '&_=' + Date.now();
    
    fetch(convertUrl, {
        credentials: 'include'
    })
    .then(response => {
        // Check if response is a PDF
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/pdf')) {
            return response.blob();
        }
        throw new Error('Conversion failed - not a PDF');
    })
    .then(blob => {
        // ✅ Create URL for the PDF blob
        const pdfUrl = URL.createObjectURL(blob);
        
        // ✅ Build final URL with toolbar control
        let finalUrl = pdfUrl;
        if (isViewOnly) {
            // ✅ View-only mode: Disable toolbar
            finalUrl = pdfUrl + '#toolbar=0&navpanes=0&scrollbar=0';
            console.log('🔒 View-only mode: toolbar disabled');
        } else {
            // ✅ Full access: Enable toolbar
            finalUrl = pdfUrl + '#toolbar=1&navpanes=1&scrollbar=1';
            console.log('🔓 Full access: toolbar enabled');
        }
        
        // ✅ Display the PDF
        content.style.cssText = `
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
            overflow: hidden;
            background: #525659;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        content.innerHTML = `
            <div style="width:100%;height:100%;position:relative;display:flex;flex-direction:column;">
                <div style="padding:6px 16px;background:#f8f9fa;border-bottom:1px solid #dadce0;display:flex;justify-content:space-between;align-items:center;flex-shrink:0;">
                    <span style="font-size:13px;color:#5f6368;">
                        <i class="fas fa-file-pdf" style="color:#ea4335;margin-right:8px;"></i>
                        ${escapeHtml(fileName)}
                    </span>
                    <div style="display:flex;gap:8px;align-items:center;">
                        ${isViewOnly ? `
                        <span style="color:#d93025;font-size:12px;padding:4px 8px;display:flex;align-items:center;gap:4px;">
                            <i class="fas fa-eye"></i> View Only
                        </span>` : ''}
                        <button onclick="closePreviewModal()" style="background:transparent;border:none;color:#5f6368;cursor:pointer;font-size:16px;padding:4px 8px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <iframe src="${finalUrl}" 
                        style="flex:1;width:100%;border:none;background:white;"
                        allowfullscreen>
                </iframe>
                <div style="padding:4px 16px;background:#f8f9fa;border-top:1px solid #dadce0;text-align:center;flex-shrink:0;">
                    <span style="font-size:11px;color:#9aa0a6;">
                        <i class="fas fa-info-circle"></i> 
                        ${isViewOnly ? '🔒 View Only Mode' : '✅ Full Access'}
                        • Converted from ${fileName.split('.').pop().toUpperCase()}
                    </span>
                </div>
            </div>
        `;
        
        console.log('✅ Office document converted and displayed as PDF');
    })
    .catch(error => {
        console.error('❌ Office conversion error:', error);
        
        // ✅ Fallback: Show error with download option
        const downloadUrl = window.API_BASE + '/files/' + fileId + '/download';
        
        content.style.cssText = `
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
            overflow: hidden;
            background: #f1f3f4;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        content.innerHTML = `
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px;background:white;border-radius:12px;border:2px solid #e8eaed;max-width:500px;width:100%;text-align:center;margin:20px;">
                <i class="fas fa-exclamation-triangle" style="font-size:48px;color:#ea4335;"></i>
                <h3 style="margin:16px 0 8px;font-size:18px;font-weight:500;color:#202124;">Preview Unavailable</h3>
                <p style="margin:4px 0 8px;color:#5f6368;font-size:14px;">${escapeHtml(fileName)}</p>
                <p style="margin:4px 0 16px;color:#9aa0a6;font-size:13px;">${error.message || 'Could not convert document to PDF'}</p>
                ${!isViewOnly ? `
                <a href="${downloadUrl}" target="_blank" 
                   style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:#1a73e8;color:white;border:none;border-radius:6px;font-size:15px;font-weight:500;cursor:pointer;text-decoration:none;">
                    <i class="fas fa-download"></i> Download to view
                </a>` : `
                <p style="color:#d93025;font-size:13px;display:flex;align-items:center;gap:6px;">
                    <i class="fas fa-lock"></i> Download disabled (View Only)
                </p>`}
                <button onclick="closePreviewModal()" style="margin-top:8px;padding:8px 20px;background:#f1f3f4;color:#5f6368;border:none;border-radius:6px;font-size:14px;cursor:pointer;">
                    Close
                </button>
            </div>
        `;
    });
}

function loadGoogleDocsPreview(content, fileId, fileName) {
    const viewerUrl = 'https://docs.google.com/viewer?url=' + 
                      encodeURIComponent(window.location.origin + window.API_BASE + '/files/' + fileId + '/download?preview=1');
    
    content.style.cssText = `
        width: 100%;
        height: 100%;
        padding: 0;
        margin: 0;
        overflow: hidden;
        background: #f1f3f4;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    
    content.innerHTML = `
        <div style="width:100%;height:100%;position:relative;">
            <iframe src="${viewerUrl}" 
                    style="width:100%;height:100%;border:none;background:white;"
                    allowfullscreen>
            </iframe>
        </div>
    `;
}



function renderExcelPreview(content, fileId, fileName, fileSize, canDownload, downloadUrl) {
    // Check if XLSX is loaded
    if (typeof XLSX === 'undefined') {
        content.innerHTML = `
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;padding:40px;background:#f1f3f4;">
                <i class="fas fa-exclamation-triangle" style="font-size:48px;color:#ea4335;"></i>
                <p style="margin-top:16px;font-size:16px;font-weight:500;color:#202124;">XLSX library not loaded</p>
                <p style="color:#5f6368;font-size:14px;text-align:center;max-width:400px;">Please refresh the page to load the spreadsheet library.</p>
                <button onclick="closePreviewModal()" style="margin-top:16px;padding:8px 20px;background:#f1f3f4;color:#5f6368;border:1px solid #dadce0;border-radius:4px;cursor:pointer;">Close</button>
            </div>
        `;
        return;
    }
    
    // Create the Excel preview instance
    const excelPreview = new ExcelPreview({
        container: content,
        canDownload: canDownload,
        downloadUrl: downloadUrl,
        fileName: fileName,
        fileSize: fileSize
    });
    
    // Load the Excel file
    excelPreview.loadFromUrl(window.API_BASE + '/files/' + fileId + '/download?preview=1')
        .catch(error => {
            console.error('❌ Excel load error:', error);
        });
    
    // Store reference for cleanup
    window._currentExcelPreview = excelPreview;
}

/**
 * Load Excel file using SheetJS
 */
function loadExcelFile(fileId, content, fileName, fileSize, canDownload, downloadUrl) {
    fetch(window.API_BASE + '/files/' + fileId + '/download?preview=1', {
        credentials: 'include'
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to load file');
        return response.arrayBuffer();
    })
    .then(arrayBuffer => {
        // ✅ Read workbook with formatting options
        const workbook = XLSX.read(arrayBuffer, { 
            type: 'array',
            cellDates: true,
            dateNF: 'yyyy-mm-dd hh:mm:ss',
            cellStyles: true,
            cellHTML: true,
            sheetStubs: true,
            dense: false
        });
        
        // ✅ Render with full styling
        renderExcelWorkbook(content, workbook, fileName, fileSize, canDownload, downloadUrl);
    })
    .catch(error => {
        console.error('❌ Excel load error:', error);
        showExcelError(content, error, canDownload, downloadUrl);
    });
}

/**
 * Render Excel workbook with professional styling
 */
function renderExcelWorkbook(content, workbook, fileName, fileSize, canDownload, downloadUrl) {
    const sheetNames = workbook.SheetNames;
    
    // ✅ Build the complete UI
    content.innerHTML = `
        <div style="width:100%;height:100%;display:flex;flex-direction:column;background:#f5f5f5;font-family:'Segoe UI',-apple-system,BlinkMacSystemFont,Roboto,Arial,sans-serif;">
            <!-- Toolbar -->
            <div style="background:white;border-bottom:1px solid #e0e0e0;padding:6px 16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:6px;flex-shrink:0;min-height:44px;">
                <div style="display:flex;align-items:center;gap:10px;overflow:hidden;">
                    <i class="fas fa-file-excel" style="color:#217346;font-size:18px;"></i>
                    <span style="font-weight:600;font-size:14px;color:#202124;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;">${escapeHtml(fileName)}</span>
                    <span style="color:#5f6368;font-size:11px;background:#f1f3f4;padding:2px 10px;border-radius:12px;">${formatFileSize(fileSize)}</span>
                </div>
                <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                    <!-- Sheet Navigation -->
                    <div style="display:flex;align-items:center;gap:4px;background:#f1f3f4;border-radius:4px;padding:2px;">
                        <button onclick="excelPrevSheet()" 
                                style="padding:3px 8px;background:transparent;border:none;cursor:pointer;border-radius:3px;font-size:12px;color:#5f6368;"
                                title="Previous sheet">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span id="excelSheetIndicator" style="font-size:12px;color:#202124;padding:0 8px;min-width:100px;text-align:center;font-weight:500;"></span>
                        <button onclick="excelNextSheet()" 
                                style="padding:3px 8px;background:transparent;border:none;cursor:pointer;border-radius:3px;font-size:12px;color:#5f6368;"
                                title="Next sheet">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    
                    <!-- Actions -->
                    ${canDownload ? `
                    <button onclick="window.location.href='${downloadUrl}'" 
                            style="padding:4px 14px;background:#217346;color:white;border:none;border-radius:4px;cursor:pointer;font-size:12px;display:flex;align-items:center;gap:4px;font-weight:500;">
                        <i class="fas fa-download"></i> Download
                    </button>` : `
                    <span style="padding:4px 12px;background:#fce8e6;color:#d93025;border-radius:4px;font-size:12px;display:flex;align-items:center;gap:4px;font-weight:500;">
                        <i class="fas fa-eye"></i> View Only
                    </span>`}
                    <button onclick="closePreviewModal()" 
                            style="padding:4px 12px;background:transparent;border:1px solid #dadce0;border-radius:4px;cursor:pointer;font-size:12px;color:#5f6368;"
                            title="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Sheet Content Container -->
            <div id="excelSheetContainer" style="flex:1;overflow:auto;padding:8px;background:#e6e9ed;">
                <div id="excelSheetContent" style="background:white;border-radius:2px;box-shadow:0 1px 3px rgba(0,0,0,0.08);padding:0;display:block;min-width:100%;height:100%;">
                </div>
            </div>
            
            <!-- Status Bar -->
            <div style="background:white;border-top:1px solid #e0e0e0;padding:2px 16px;display:flex;justify-content:space-between;font-size:11px;color:#5f6368;flex-shrink:0;">
                <span id="excelStatusLeft">📊 Ready</span>
                <span id="excelStatusCenter">Sheet 1 of ${sheetNames.length}</span>
                <span id="excelStatusRight">${sheetNames.length} sheet(s)</span>
            </div>
        </div>
    `;
    
    // ✅ Store workbook data
    window._excelData = {
        workbook: workbook,
        sheetNames: sheetNames,
        currentSheet: 0,
        canDownload: canDownload,
        downloadUrl: downloadUrl,
        fileName: fileName
    };
    
    // ✅ Render first sheet
    renderExcelSheet(workbook, sheetNames[0], 0);
}

/**
 * Render a single sheet with full styling and horizontal scroll
 */
function renderExcelSheet(workbook, sheetName, index) {
    const container = document.getElementById('excelSheetContent');
    if (!container) return;
    
    const worksheet = workbook.Sheets[sheetName];
    
    // ✅ Get data range
    const range = XLSX.utils.decode_range(worksheet['!ref'] || 'A1:A1');
    const data = [];
    
    // ✅ Build data array with proper cell values
    for (let r = range.s.r; r <= range.e.r; r++) {
        const row = [];
        for (let c = range.s.c; c <= range.e.c; c++) {
            const cellAddress = XLSX.utils.encode_cell({r: r, c: c});
            const cell = worksheet[cellAddress];
            if (cell) {
                let value = cell.v;
                // Handle dates
                if (cell.t === 'd') {
                    value = new Date(value).toLocaleDateString();
                }
                row.push({
                    value: value !== undefined && value !== null ? String(value) : '',
                    type: cell.t || 's',
                    style: cell.s || {}
                });
            } else {
                row.push({ value: '', type: 's', style: {} });
            }
        }
        data.push(row);
    }
    
    // ✅ Calculate total columns for min-width
    const totalCols = data.length > 0 ? data[0].length : 0;
    const minTableWidth = Math.max(totalCols * 120, 1000); // Minimum width for scrolling
    
    // ✅ Build HTML table with proper container for horizontal scroll
    let html = `
        <div style="overflow:auto;width:100%;height:100%;">
            <div style="min-width:${minTableWidth}px;display:inline-block;">
                <table style="border-collapse:collapse;font-family:Segoe UI,Arial,sans-serif;font-size:13px;width:100%;table-layout:fixed;border:1px solid #d0d3d8;">
    `;
    
    // Header row with column letters
    html += '<thead><tr>';
    html += '<th style="background:#f3f4f6;border:1px solid #d0d3d8;padding:4px 6px;font-size:10px;font-weight:600;color:#5f6368;text-align:center;min-width:30px;width:30px;position:sticky;top:0;z-index:2;user-select:none;">#</th>';
    for (let c = range.s.c; c <= range.e.c; c++) {
        const colLetter = XLSX.utils.encode_col(c);
        html += `<th style="background:#f3f4f6;border:1px solid #d0d3d8;padding:4px 8px;font-size:10px;font-weight:600;color:#5f6368;text-align:center;min-width:80px;width:80px;position:sticky;top:0;z-index:2;user-select:none;">${colLetter}</th>`;
    }
    html += '</tr></thead>';
    
    // Data rows
    html += '<tbody>';
    data.forEach((row, rowIndex) => {
        const rowNum = rowIndex + 1;
        const rowBg = rowIndex % 2 === 0 ? '#ffffff' : '#fafbfc';
        html += `<tr style="background:${rowBg};">`;
        html += `<td style="border:1px solid #d0d3d8;padding:4px 6px;font-size:10px;font-weight:500;color:#5f6368;text-align:center;background:#f8f9fa;position:sticky;left:0;z-index:1;user-select:none;min-width:30px;width:30px;">${rowNum}</td>`;
        
        row.forEach((cell) => {
            let value = cell.value || '';
            // Truncate long text
            if (value.length > 150) {
                value = value.substring(0, 150) + '...';
            }
            
            // Determine text color based on type
            let color = '#202124';
            if (cell.type === 'n' && !isNaN(parseFloat(value))) {
                color = '#0d652d'; // Numbers green
            } else if (cell.type === 'd') {
                color = '#1967d2'; // Dates blue
            } else if (cell.type === 'b') {
                color = '#d93025'; // Booleans red
            }
            
            // Alignment based on type
            let textAlign = 'left';
            if (cell.type === 'n') textAlign = 'right';
            
            html += `<td style="border:1px solid #d0d3d8;padding:4px 8px;font-size:13px;color:${color};text-align:${textAlign};min-width:80px;width:80px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;cursor:default;" title="${escapeHtml(String(cell.value || ''))}">${escapeHtml(value)}</td>`;
        });
        
        html += '</tr>';
    });
    html += '</tbody></table></div></div>';
    
    container.innerHTML = html;
    
    // ✅ Update status
    const totalCells = data.length > 0 ? data[0].length * data.length : 0;
    document.getElementById('excelStatusLeft').textContent = `📊 ${totalCells.toLocaleString()} cells`;
    document.getElementById('excelStatusCenter').textContent = `Sheet ${index + 1} of ${window._excelData.sheetNames.length}`;
    document.getElementById('excelStatusRight').textContent = `${escapeHtml(sheetName)} • ${window._excelData.sheetNames.length} sheets`;
    document.getElementById('excelSheetIndicator').textContent = `${index + 1}/${window._excelData.sheetNames.length} • ${escapeHtml(sheetName)}`;
}

/**
 * Go to previous sheet
 */
function excelPrevSheet() {
    const data = window._excelData;
    if (!data || data.currentSheet <= 0) return;
    const newIndex = data.currentSheet - 1;
    renderExcelSheet(data.workbook, data.sheetNames[newIndex], newIndex);
    data.currentSheet = newIndex;
}

/**
 * Go to next sheet
 */
function excelNextSheet() {
    const data = window._excelData;
    if (!data || data.currentSheet >= data.sheetNames.length - 1) return;
    const newIndex = data.currentSheet + 1;
    renderExcelSheet(data.workbook, data.sheetNames[newIndex], newIndex);
    data.currentSheet = newIndex;
}

/**
 * Show Excel error message
 */
function showExcelError(content, error, canDownload, downloadUrl) {
    content.innerHTML = `
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;padding:40px;background:#f1f3f4;">
            <i class="fas fa-exclamation-triangle" style="font-size:48px;color:#ea4335;"></i>
            <p style="margin-top:16px;font-size:16px;font-weight:500;color:#202124;">Failed to load spreadsheet</p>
            <p style="color:#5f6368;font-size:14px;text-align:center;max-width:400px;">${escapeHtml(error.message || 'Unknown error')}</p>
            ${canDownload ? `
            <button onclick="window.location.href='${downloadUrl}'" 
                    style="margin-top:16px;padding:10px 24px;background:#1a73e8;color:white;border:none;border-radius:6px;cursor:pointer;font-size:14px;font-weight:500;">
                <i class="fas fa-download"></i> Download to view
            </button>` : ''}
            <button onclick="closePreviewModal()" 
                    style="margin-top:8px;padding:8px 20px;background:transparent;border:1px solid #dadce0;border-radius:4px;cursor:pointer;color:#5f6368;font-size:13px;">
                Close
            </button>
        </div>
    `;
}

/**
 * Load SheetJS library dynamically
 */
function loadSheetJS() {
    return new Promise((resolve, reject) => {
        if (typeof XLSX !== 'undefined') {
            resolve();
            return;
        }
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}





/**
 * Render workbook with toolbar and sheets
 */
function renderWorkbook(content, workbook, fileName, fileSize, canDownload, downloadUrl) {
    const sheetNames = workbook.SheetNames;
    let currentSheetIndex = 0;
    
    // ✅ Create the preview UI
    content.innerHTML = `
        <div style="width:100%;height:100%;display:flex;flex-direction:column;background:#f1f3f4;">
            <!-- Toolbar -->
            <div style="background:white;border-bottom:1px solid #dadce0;padding:8px 16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;flex-shrink:0;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <i class="fas fa-file-excel" style="color:#217346;font-size:20px;"></i>
                    <span style="font-weight:500;font-size:14px;color:#202124;">${escapeHtml(fileName)}</span>
                    <span style="color:#5f6368;font-size:12px;">${formatFileSize(fileSize)}</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <!-- Sheet selector -->
                    <div style="display:flex;align-items:center;gap:4px;background:#f1f3f4;border-radius:4px;padding:2px;">
                        <button onclick="changeSheet('prev')" 
                                style="padding:4px 8px;background:transparent;border:none;cursor:pointer;border-radius:4px;"
                                title="Previous sheet">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span id="sheetIndicator" style="font-size:12px;color:#202124;padding:0 8px;min-width:80px;text-align:center;"></span>
                        <button onclick="changeSheet('next')" 
                                style="padding:4px 8px;background:transparent;border:none;cursor:pointer;border-radius:4px;"
                                title="Next sheet">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    
                    <!-- Actions -->
                    ${canDownload ? `
                    <button onclick="window.location.href='${downloadUrl}'" 
                            style="padding:6px 16px;background:#217346;color:white;border:none;border-radius:4px;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:6px;">
                        <i class="fas fa-download"></i> Download
                    </button>` : `
                    <span style="padding:6px 16px;background:#fce8e6;color:#d93025;border-radius:4px;font-size:13px;display:flex;align-items:center;gap:6px;">
                        <i class="fas fa-eye"></i> View Only
                    </span>`}
                    <button onclick="closePreviewModal()" 
                            style="padding:6px 16px;background:#f1f3f4;color:#5f6368;border:none;border-radius:4px;cursor:pointer;font-size:13px;">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
            
            <!-- Sheet Content -->
            <div id="sheetContainer" style="flex:1;overflow:auto;padding:16px;background:white;margin:8px;">
            </div>
            
            <!-- Footer -->
            <div style="padding:4px 16px;background:#f8f9fa;border-top:1px solid #dadce0;text-align:center;flex-shrink:0;">
                <span style="font-size:11px;color:#9aa0a6;">
                    <i class="fas fa-info-circle"></i> 
                    ${sheetNames.length} sheet(s) • Double-click cell to view content • Scroll to navigate
                </span>
            </div>
        </div>
    `;
    
    // ✅ Store workbook data for sheet switching
    window._excelData = {
        workbook: workbook,
        sheetNames: sheetNames,
        currentSheet: 0,
        canDownload: canDownload,
        downloadUrl: downloadUrl,
        fileName: fileName
    };
    
    // ✅ Render first sheet
    renderSheet(workbook, sheetNames[0], 0, content);
}

/**
 * Render a specific sheet
 */
function renderSheet(workbook, sheetName, index, content) {
    const container = document.getElementById('sheetContainer');
    if (!container) return;
    
    const worksheet = workbook.Sheets[sheetName];
    
    // ✅ Convert to HTML with formatting
    let html = XLSX.utils.sheet_to_html(worksheet, {
        id: 'excel-sheet',
        editable: false
    });
    
    // ✅ Update sheet indicator
    const indicator = document.getElementById('sheetIndicator');
    if (indicator) {
        indicator.textContent = `${index + 1}/${window._excelData.sheetNames.length} • ${escapeHtml(sheetName)}`;
    }
    
    // ✅ Wrap and style the table
    container.innerHTML = `
        <div style="overflow:auto;width:100%;height:100%;">
            <div style="min-width:100%;display:inline-block;">
                ${html}
            </div>
        </div>
    `;
    
    // ✅ Apply styles to the table
    const table = container.querySelector('table');
    if (table) {
        // Table styles
        table.style.width = '100%';
        table.style.borderCollapse = 'collapse';
        table.style.fontSize = '13px';
        table.style.fontFamily = '"Segoe UI", Arial, sans-serif';
        table.style.border = '1px solid #dadce0';
        
        // Cell styles
        const cells = table.querySelectorAll('th, td');
        cells.forEach(cell => {
            cell.style.border = '1px solid #dadce0';
            cell.style.padding = '6px 12px';
            cell.style.textAlign = 'left';
            cell.style.minWidth = '60px';
            cell.style.maxWidth = '300px';
            cell.style.overflow = 'hidden';
            cell.style.textOverflow = 'ellipsis';
            cell.style.whiteSpace = 'nowrap';
        });
        
        // Header styles
        const headers = table.querySelectorAll('th');
        headers.forEach(th => {
            th.style.backgroundColor = '#f1f3f4';
            th.style.fontWeight = '600';
            th.style.position = 'sticky';
            th.style.top = '0';
            th.style.zIndex = '2';
            th.style.borderBottom = '2px solid #dadce0';
        });
        
        // ✅ Add row numbers (Excel-like)
        const rows = table.querySelectorAll('tr');
        rows.forEach((row, idx) => {
            // Add row number as first cell
            const firstCell = row.querySelector('td');
            if (firstCell && idx > 0) {
                const rowNum = document.createElement('td');
                rowNum.textContent = idx;
                rowNum.style.cssText = `
                    background: #f8f9fa;
                    color: #5f6368;
                    font-size: 11px;
                    font-weight: 500;
                    text-align: center;
                    min-width: 30px;
                    border: 1px solid #dadce0;
                    padding: 6px 4px;
                    position: sticky;
                    left: 0;
                    z-index: 1;
                `;
                row.prepend(rowNum);
            }
            
            // Add column headers (A, B, C, ...)
            if (idx === 0) {
                const cells = row.querySelectorAll('th');
                cells.forEach((th, colIdx) => {
                    const colLetter = String.fromCharCode(65 + colIdx);
                    th.textContent = colLetter;
                    th.style.cssText = `
                        background: #f1f3f4;
                        color: #5f6368;
                        font-size: 11px;
                        font-weight: 600;
                        text-align: center;
                        min-width: 60px;
                        border: 1px solid #dadce0;
                        padding: 6px 8px;
                        position: sticky;
                        top: 0;
                        z-index: 2;
                    `;
                });
            }
        });
        
        // ✅ Zebra striping
        const allRows = table.querySelectorAll('tr');
        allRows.forEach((row, idx) => {
            if (idx > 0 && idx % 2 === 0) {
                row.style.backgroundColor = '#f8f9fa';
            }
        });
    }
    
    // ✅ Store current sheet
    if (window._excelData) {
        window._excelData.currentSheet = index;
    }
}

/**
 * Change sheet (previous/next)
 */
function changeSheet(direction) {
    if (!window._excelData) return;
    
    const data = window._excelData;
    const totalSheets = data.sheetNames.length;
    let newIndex = data.currentSheet;
    
    if (direction === 'next' && newIndex < totalSheets - 1) {
        newIndex++;
    } else if (direction === 'prev' && newIndex > 0) {
        newIndex--;
    } else {
        return;
    }
    
    const content = document.getElementById('previewContent');
    renderSheet(
        data.workbook, 
        data.sheetNames[newIndex], 
        newIndex, 
        content
    );
}

// ✅ Make functions global
window.changeSheet = changeSheet;


function loadExcelSpreadsheet(fileId) {
    const content = document.getElementById('previewContent');
    
    content.innerHTML = `
        <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#f1f3f4;padding:20px;">
            <div class="preview-spinner"></div>
            <p style="margin-top:16px;color:#5f6368;">Loading spreadsheet...</p>
        </div>
    `;
    
    fetch(window.API_BASE + '/files/' + fileId + '/download?preview=1', {
        credentials: 'include'
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to load file');
        return response.arrayBuffer();
    })
    .then(arrayBuffer => {
        if (typeof XLSX === 'undefined') {
            throw new Error('XLSX library not loaded. Please refresh the page.');
        }
        
        const workbook = XLSX.read(arrayBuffer, { type: 'array' });
        const firstSheetName = workbook.SheetNames[0];
        const firstSheet = workbook.Sheets[firstSheetName];
        const html = XLSX.utils.sheet_to_html(firstSheet);
        
        content.innerHTML = `
            <div style="width:100%;height:100%;overflow:auto;padding:20px;background:white;">
                <div style="max-width:100%;overflow:auto;">
                    ${html}
                </div>
            </div>
        `;
        
        const table = content.querySelector('table');
        if (table) {
            table.style.width = '100%';
            table.style.borderCollapse = 'collapse';
            table.style.fontSize = '14px';
            table.style.fontFamily = 'Arial, sans-serif';
            
            const cells = table.querySelectorAll('th, td');
            cells.forEach(cell => {
                cell.style.border = '1px solid #dadce0';
                cell.style.padding = '8px 12px';
                cell.style.textAlign = 'left';
            });
            
            const headers = table.querySelectorAll('th');
            headers.forEach(th => {
                th.style.backgroundColor = '#f1f3f4';
                th.style.fontWeight = '600';
            });
        }
    })
    .catch(error => {
        console.error('❌ Excel load error:', error);
        content.innerHTML = `
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px;color:#ea4335;">
                <i class="fas fa-exclamation-triangle" style="font-size:48px;"></i>
                <p style="margin-top:16px;">Failed to load spreadsheet: ${error.message}</p>
                <button onclick="closePreviewModal()" style="margin-top:16px;padding:8px 20px;background:#1a73e8;color:white;border:none;border-radius:4px;cursor:pointer;">Close</button>
            </div>
        `;
    });
}

// ============================================
// IMAGE PREVIEW
// ============================================

function renderImagePreview(content, previewUrl, fileName) {
    content.innerHTML = `
        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#1a1a1a;padding:20px;">
            <img src="${previewUrl}" alt="${escapeHtml(fileName)}" 
                 style="max-width:100%;max-height:100%;object-fit:contain;border-radius:4px;box-shadow:0 4px 30px rgba(0,0,0,0.5);"
                 onerror="this.parentElement.innerHTML='<div style=\\'text-align:center;color:#ea4335;padding:40px;\\'><i class=\\'fas fa-exclamation-triangle\\' style=\\'font-size:48px;\\'></i><p style=\\'margin-top:16px;\\'>Failed to load image</p></div>'">
        </div>
    `;
}

// ============================================
// VIDEO PREVIEW
// ============================================

function renderVideoPreview(content, previewUrl, fileName) {
    content.innerHTML = `
        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#000;padding:20px;">
            <video controls autoplay style="max-width:100%;max-height:100%;border-radius:4px;">
                <source src="${previewUrl}">
                <p>Your browser does not support the video tag.</p>
            </video>
        </div>
    `;
}

// ============================================
// AUDIO PREVIEW
// ============================================

function renderAudioPreview(content, previewUrl, fileName, fileSize) {
    content.innerHTML = `
        <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px;background:linear-gradient(135deg,#f8f9fa 0%,#e8eaed 100%);">
            <div style="width:120px;height:120px;border-radius:50%;background:linear-gradient(135deg,#1a73e8,#4fc3f7);display:flex;align-items:center;justify-content:center;margin-bottom:24px;box-shadow:0 8px 32px rgba(26,115,232,0.3);">
                <i class="fas fa-music" style="font-size:48px;color:white;"></i>
            </div>
            <div style="text-align:center;margin-bottom:24px;">
                <h3 style="margin:0;font-size:18px;font-weight:500;color:#202124;">${escapeHtml(fileName)}</h3>
                <p style="margin:4px 0 0;color:#5f6368;font-size:14px;">${formatFileSize(fileSize)}</p>
            </div>
            <audio controls autoplay style="width:100%;max-width:500px;">
                <source src="${previewUrl}">
                <p>Your browser does not support the audio tag.</p>
            </audio>
        </div>
    `;
}

// ============================================
// TEXT PREVIEW
// ============================================

function renderTextPreview(content, fileId, fileName, fileSize) {
    content.innerHTML = `
        <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#1e1e1e;padding:40px;">
            <div class="preview-spinner"></div>
            <p style="margin-top:16px;color:#e8eaed;">Loading file content...</p>
        </div>
    `;
    
    fetch(window.API_BASE + '/files/' + fileId + '/content', { credentials: 'include' })
        .then(response => {
            if (!response.ok) throw new Error('Failed to load content');
            return response.text();
        })
        .then(text => {
            const extension = fileName.split('.').pop().toLowerCase();
            const lang = getLanguageFromExtension(extension);
            const displayText = text.length > 500000 ? text.substring(0, 500000) + '\n\n... (File truncated, too large)' : text;
            
            content.innerHTML = `
                <div style="width:100%;height:100%;display:flex;flex-direction:column;background:#1e1e1e;overflow:hidden;">
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 20px;background:#2d2d2d;border-bottom:1px solid #3d3d3d;flex-shrink:0;">
                        <span style="color:#d4d4d4;font-size:13px;font-weight:500;">
                            <i class="fas fa-code" style="margin-right:8px;"></i> ${lang.toUpperCase()} - ${formatFileSize(text.length)}
                        </span>
                        <div style="display:flex;gap:8px;">
                            <button onclick="copyCode()" style="background:transparent;border:none;color:#d4d4d4;cursor:pointer;padding:4px 12px;border-radius:4px;font-size:13px;display:flex;align-items:center;gap:6px;">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                            <button onclick="closePreviewModal()" style="background:transparent;border:none;color:#d4d4d4;cursor:pointer;padding:4px 12px;border-radius:4px;font-size:13px;display:flex;align-items:center;gap:6px;">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                    <pre style="flex:1;margin:0;padding:20px;overflow:auto;background:#1e1e1e;color:#d4d4d4;font-family:'Consolas','Monaco','Courier New',monospace;font-size:14px;line-height:1.8;white-space:pre-wrap;word-wrap:break-word;">
                        <code class="language-${lang}">${escapeHtml(displayText)}</code>
                    </pre>
                </div>
            `;
            
            if (typeof Prism !== 'undefined') {
                try { Prism.highlightAll(); } catch (e) {}
            }
        })
        .catch(error => {
            content.innerHTML = `
                <div style="text-align:center;color:#ea4335;padding:40px;">
                    <i class="fas fa-exclamation-triangle" style="font-size:48px;"></i>
                    <p style="margin-top:16px;">Failed to load file content: ${error.message}</p>
                    <button onclick="closePreviewModal()" style="margin-top:16px;padding:8px 20px;background:#1a73e8;color:white;border:none;border-radius:4px;cursor:pointer;">Close</button>
                </div>
            `;
        });
}

// ============================================
// ARCHIVE/ZIP PREVIEW
// ============================================

function renderArchivePreview(content, fileName, fileSize, canDownload, downloadUrl) {
    content.innerHTML = `
        <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#f1f3f4;padding:20px;">
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;background:white;border-radius:12px;padding:40px;max-width:500px;width:100%;text-align:center;border:2px solid #e8eaed;">
                <i class="fas fa-file-archive" style="font-size:64px;color:#6c757d;margin-bottom:16px;"></i>
                <h3 style="margin:0 0 8px;font-size:18px;font-weight:500;color:#202124;word-break:break-all;">${escapeHtml(fileName)}</h3>
                <p style="margin:4px 0;color:#5f6368;font-size:14px;">Archive File</p>
                <p style="margin:4px 0 16px;color:#5f6368;font-size:14px;">Size: ${formatFileSize(fileSize)}</p>
                <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center;">
                    ${canDownload ? `
                    <button onclick="window.location.href='${downloadUrl}'" style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:#6c757d;color:white;border:none;border-radius:6px;font-size:15px;font-weight:500;cursor:pointer;">
                        <i class="fas fa-download"></i> Download
                    </button>` : ''}
                    <button onclick="closePreviewModal()" style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:#f1f3f4;color:#5f6368;border:none;border-radius:6px;font-size:15px;font-weight:500;cursor:pointer;">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
                <p style="margin-top:12px;font-size:12px;color:#9aa0a6;">
                    <i class="fas fa-info-circle"></i> Archive preview coming soon with file listing
                </p>
            </div>
        </div>
    `;
}

// ============================================
// GENERIC PREVIEW
// ============================================

function renderGenericPreview(content, fileName, fileSize, fileType, canDownload, downloadUrl) {
    content.innerHTML = `
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 40px;background:white;border-radius:12px;border:2px solid #e8eaed;max-width:500px;width:100%;text-align:center;margin:auto;">
            <i class="fas fa-file fa-5x" style="color:#dadce0;margin-bottom:16px;"></i>
            <h3 style="margin:0 0 8px;font-size:18px;font-weight:500;color:#202124;word-break:break-all;">${escapeHtml(fileName)}</h3>
            <p style="margin:4px 0;color:#5f6368;font-size:14px;">File type: ${fileType || 'Unknown'}</p>
            <p style="margin:4px 0 16px;color:#5f6368;font-size:14px;">Size: ${formatFileSize(fileSize)}</p>
            ${canDownload ? 
                `<button onclick="window.location.href='${downloadUrl}'" style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:#1a73e8;color:white;border:none;border-radius:6px;font-size:15px;font-weight:500;cursor:pointer;">
                    <i class="fas fa-download"></i> Download File
                </button>` :
                `<p style="color:#ea4335;"><i class="fas fa-lock"></i> Download disabled</p>`
            }
            <button onclick="closePreviewModal()" style="margin-top:8px;padding:8px 20px;background:#f1f3f4;color:#5f6368;border:none;border-radius:6px;font-size:14px;cursor:pointer;">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    `;
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function closePreviewModal() {
    const modal = document.getElementById('previewModal');
    if (modal) {
        modal.classList.remove('active');
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        const content = document.getElementById('previewContent');
        if (content) {
            content.innerHTML = '<div style="text-align:center;padding:40px;color:#5f6368;"><div class="preview-spinner"></div><p style="margin-top:16px;">Loading...</p></div>';
            content.style.cssText = '';
        }
        
        const modalHeader = document.querySelector('.preview-modal-header');
        if (modalHeader) {
            modalHeader.style.display = 'flex';
        }
    }
}

function copyCode() {
    const codeElement = document.querySelector('.preview-code code');
    if (codeElement) {
        navigator.clipboard.writeText(codeElement.textContent)
            .then(() => showToast('Code copied to clipboard!', 'success'))
            .catch(() => {
                const range = document.createRange();
                range.selectNode(codeElement);
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(range);
                document.execCommand('copy');
                showToast('Code copied to clipboard!', 'success');
            });
    }
}

// ============================================
// EXPOSE FUNCTIONS GLOBALLY
// ============================================

// Make sure all functions are available globally
window.showPreviewModal = showPreviewModal;
window.closePreviewModal = closePreviewModal;
window.copyCode = copyCode;
window.renderPdfPreview = renderPdfPreview;
window.renderOfficePreview = renderOfficePreview;
window.renderExcelPreview = renderExcelPreview;
window.renderImagePreview = renderImagePreview;
window.renderVideoPreview = renderVideoPreview;
window.renderAudioPreview = renderAudioPreview;
window.renderTextPreview = renderTextPreview;
window.renderArchivePreview = renderArchivePreview;
window.renderGenericPreview = renderGenericPreview;
window.excelPrevSheet = excelPrevSheet;
window.excelNextSheet = excelNextSheet;

console.log('📄 Preview module loaded');
console.log('✅ showPreviewModal available:', typeof window.showPreviewModal);