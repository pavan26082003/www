// public/assets/js/preview.js
// Preview Modal Functionality - Full File Support

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
    console.log('📋 File data:', file);
    
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
    const canDownload = file.can_download !== undefined ? file.can_download : true;
    
    // Set file info
    if (fileNameEl) fileNameEl.textContent = fileName;
    if (fileSizeEl) fileSizeEl.textContent = 'Size: ' + formatFileSize(fileSize);
    if (fileTypeEl) fileTypeEl.textContent = 'Type: ' + (fileType || 'Unknown');
    
    // Setup download button
    if (downloadBtn) {
        if (canDownload) {
            downloadBtn.style.display = 'inline-flex';
            downloadBtn.style.opacity = '1';
            downloadBtn.style.cursor = 'pointer';
            downloadBtn.className = 'preview-btn download';
            downloadBtn.innerHTML = '<i class="fas fa-download"></i> <span>Download</span>';
            downloadBtn.disabled = false;
            downloadBtn.onclick = function(e) {
                e.stopPropagation();
                window.location.href = window.API_BASE + '/files/' + fileId + '/download';
            };
        } else {
            downloadBtn.style.display = 'inline-flex';
            downloadBtn.style.opacity = '0.5';
            downloadBtn.style.cursor = 'not-allowed';
            downloadBtn.className = 'preview-btn download disabled';
            downloadBtn.innerHTML = '<i class="fas fa-lock"></i> <span>View Only</span>';
            downloadBtn.disabled = true;
        }
    }
    
    // Determine file type and render preview
    const extension = (fileName.split('.').pop() || '').toLowerCase();
    const mimeType = fileType.toLowerCase();
    
    const previewUrl = window.API_BASE + '/files/' + fileId + '/preview';
    const downloadUrl = window.API_BASE + '/files/' + fileId + '/download';
    
    // Render based on file type
    if (extension === 'pdf' || mimeType === 'application/pdf') {
        console.log('📄 Rendering PDF preview for:', fileName);
        renderPdfPreview(content, previewUrl, fileName);
    } else if (['pptx', 'ppt', 'docx', 'doc'].includes(extension)) {
        renderOfficePreview(content, fileId, fileName);
    } else if (['xlsx', 'xls'].includes(extension)) {
        renderExcelPreview(content, fileId, fileName, fileSize, canDownload, downloadUrl);
    } else if (mimeType.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'].includes(extension)) {
        renderImagePreview(content, previewUrl, fileName);
    } else if (mimeType.startsWith('video/') || ['mp4', 'webm', 'avi', 'mkv', 'mov', 'wmv', 'flv'].includes(extension)) {
        renderVideoPreview(content, previewUrl, fileName);
    } else if (mimeType.startsWith('audio/') || ['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma'].includes(extension)) {
        renderAudioPreview(content, previewUrl, fileName, fileSize);
    } else if (['txt', 'js', 'css', 'html', 'htm', 'json', 'xml', 'php', 'sql', 'py', 'java', 'cpp', 'c', 'h', 'sh', 'bash', 'rb', 'go', 'rs', 'ts', 'jsx', 'tsx', 'vue'].includes(extension)) {
        renderTextPreview(content, fileId, fileName, fileSize);
    } else if (['zip', 'rar', '7z', 'tar', 'gz', 'bz2'].includes(extension)) {
        renderArchivePreview(content, fileName, fileSize, canDownload, downloadUrl);
    } else {
        renderGenericPreview(content, fileName, fileSize, fileType, canDownload, downloadUrl);
    }
    
    // ✅ ACTIVATE THE MODAL
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

// ============================================
// PDF PREVIEW - CLEAN SINGLE APPROACH
// ============================================

function renderPdfPreview(content, previewUrl, fileName) {
    console.log('📄 PDF Preview URL:', previewUrl);
    
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
    
    // Single iframe - clean and simple
    content.innerHTML = `
        <iframe src="${previewUrl}" 
                style="width:100%;height:100%;border:none;background:white;"
                allowfullscreen>
        </iframe>
    `;
    
    console.log('✅ PDF preview rendered');
}

// ============================================
// OFFICE PREVIEW (PPT, DOCX) - Convert to PDF
// ============================================

function renderOfficePreview(content, fileId, fileName) {
    console.log('📄 Office preview for:', fileName);
    
    // Show loading
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
    
    // Get preview info
    fetch(window.API_BASE + '/preview/info?file_id=' + fileId, {
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        console.log('📥 Preview info:', data);
        
        if (data.success && (data.has_cached_pdf || data.has_conversion)) {
            // Load the converted PDF
            const previewUrl = window.API_BASE + '/preview/convert?file_id=' + fileId + '&_=' + Date.now();
            
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
                <iframe src="${previewUrl}" 
                        style="width:100%;height:100%;border:none;background:white;"
                        allowfullscreen>
                </iframe>
            `;
        } else {
            // Use Google Docs viewer
            loadGoogleDocsPreview(content, fileId, fileName);
        }
    })
    .catch(error => {
        console.error('❌ Preview error:', error);
        loadGoogleDocsPreview(content, fileId, fileName);
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




// ============================================
// EXCEL PREVIEW (Using SheetJS)
// ============================================

function renderExcelPreview(content, fileId, fileName, fileSize, canDownload, downloadUrl) {
    content.innerHTML = `
        <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#f1f3f4;padding:20px;">
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;background:white;border-radius:12px;padding:40px;max-width:600px;width:100%;text-align:center;border:2px solid #e8eaed;">
                <i class="fas fa-file-excel" style="font-size:64px;color:#217346;margin-bottom:16px;"></i>
                <h3 style="margin:0 0 8px;font-size:18px;font-weight:500;color:#202124;word-break:break-all;">${escapeHtml(fileName)}</h3>
                <p style="margin:4px 0;color:#5f6368;font-size:14px;">Excel Spreadsheet</p>
                <p style="margin:4px 0 16px;color:#5f6368;font-size:14px;">Size: ${formatFileSize(fileSize)}</p>
                <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center;">
                    <button onclick="loadExcelSpreadsheet(${fileId})" style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:#217346;color:white;border:none;border-radius:6px;font-size:15px;font-weight:500;cursor:pointer;">
                        <i class="fas fa-table"></i> View Spreadsheet
                    </button>
                    ${canDownload ? `
                    <button onclick="window.location.href='${downloadUrl}'" style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:#6c757d;color:white;border:none;border-radius:6px;font-size:15px;font-weight:500;cursor:pointer;">
                        <i class="fas fa-download"></i> Download
                    </button>` : ''}
                    <button onclick="closePreviewModal()" style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:#f1f3f4;color:#5f6368;border:none;border-radius:6px;font-size:15px;font-weight:500;cursor:pointer;">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    `;
}

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

console.log('📄 Preview module loaded');
console.log('✅ showPreviewModal available:', typeof window.showPreviewModal);