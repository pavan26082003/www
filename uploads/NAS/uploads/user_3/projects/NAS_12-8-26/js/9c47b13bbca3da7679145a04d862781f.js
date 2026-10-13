/**
 * Excel Preview Module - Professional Excel-like Preview
 */

class ExcelPreview {
    constructor(options = {}) {
        this.container = options.container || null;
        this.workbook = null;
        this.sheetNames = [];
        this.currentSheetIndex = 0;
        this.zoom = 100;
        this.searchTerm = '';
        this.filteredRows = [];
        this.isLoading = false;
        this.canDownload = options.canDownload !== undefined ? options.canDownload : true;
        this.downloadUrl = options.downloadUrl || '';
        this.fileName = options.fileName || 'Spreadsheet';
        this.fileSize = options.fileSize || 0;
        
        // Cache for rendered sheets
        this.sheetCache = new Map();
        
        // Settings
        this.settings = {
            maxRows: 10000,
            maxCols: 100,
            rowHeight: 28,
            colWidth: 100,
            headerHeight: 30,
            rowNumberWidth: 40,
            searchHighlightColor: '#ffeb3b',
            freezeHeader: true
        };
        
        // ✅ Bind methods properly
        this.prevSheet = this.prevSheet.bind(this);
        this.nextSheet = this.nextSheet.bind(this);
        this.switchSheet = this.switchSheet.bind(this);
        this.search = this.search.bind(this);
        this.clearSearch = this.clearSearch.bind(this);
        this.zoomIn = this.zoomIn.bind(this);
        this.zoomOut = this.zoomOut.bind(this);
        this.resetZoom = this.resetZoom.bind(this);
        this.exportToCsv = this.exportToCsv.bind(this);
        this.destroy = this.destroy.bind(this);
        this.loadFromUrl = this.loadFromUrl.bind(this);
        this.loadFromBuffer = this.loadFromBuffer.bind(this);
        
        // Initialize if container provided
        if (this.container) {
            this.initContainer();
        }
    }

    /**
     * Initialize the container
     */
    initContainer() {
        if (!this.container) return;
        
        this.container.innerHTML = `
            <div class="excel-preview-wrapper" style="display:flex;flex-direction:column;height:100%;background:#f5f5f5;font-family:'Segoe UI',-apple-system,BlinkMacSystemFont,Roboto,Arial,sans-serif;">
                <!-- Toolbar -->
                <div class="excel-toolbar" style="background:#f3f4f6;border-bottom:1px solid #d0d3d8;padding:4px 12px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:4px;flex-shrink:0;min-height:40px;">
                    <div class="excel-toolbar-left" style="display:flex;align-items:center;gap:10px;overflow:hidden;">
                        <i class="fas fa-file-excel" style="color:#217346;font-size:18px;"></i>
                        <span class="excel-filename" style="font-weight:600;font-size:14px;color:#202124;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;">${this.escapeHtml(this.fileName)}</span>
                        <span class="excel-filesize" style="color:#5f6368;font-size:11px;background:#e8eaed;padding:2px 10px;border-radius:12px;">${this.formatFileSize(this.fileSize)}</span>
                    </div>
                    <div class="excel-toolbar-right" style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                        <!-- Sheet Navigation -->
                        <div class="excel-sheet-nav" style="display:flex;align-items:center;gap:3px;background:#e8eaed;border-radius:4px;padding:2px;">
                            <button class="excel-btn-prev" onclick="if(window._excelPreview) window._excelPreview.prevSheet()" style="padding:2px 8px;background:transparent;border:none;cursor:pointer;border-radius:3px;font-size:12px;color:#5f6368;" title="Previous sheet">‹</button>
                            <span class="excel-sheet-indicator" style="font-size:12px;color:#202124;padding:0 8px;min-width:80px;text-align:center;font-weight:500;">Loading...</span>
                            <button class="excel-btn-next" onclick="if(window._excelPreview) window._excelPreview.nextSheet()" style="padding:2px 8px;background:transparent;border:none;cursor:pointer;border-radius:3px;font-size:12px;color:#5f6368;" title="Next sheet">›</button>
                        </div>
                        
                        <!-- Search -->
                        <div class="excel-search" style="display:flex;align-items:center;gap:3px;background:white;border:1px solid #dadce0;border-radius:4px;padding:0 6px;">
                            <i class="fas fa-search" style="color:#9aa0a6;font-size:12px;"></i>
                            <input type="text" class="excel-search-input" placeholder="Search..." onkeyup="if(window._excelPreview) window._excelPreview.search(this.value)" style="border:none;outline:none;padding:4px 4px;font-size:12px;width:120px;background:transparent;">
                            <button class="excel-search-clear" onclick="if(window._excelPreview) window._excelPreview.clearSearch()" style="background:transparent;border:none;cursor:pointer;color:#9aa0a6;font-size:12px;display:none;">✕</button>
                        </div>
                        
                        <!-- Zoom -->
                        <div class="excel-zoom" style="display:flex;align-items:center;gap:2px;">
                            <button onclick="if(window._excelPreview) window._excelPreview.zoomOut()" style="padding:2px 8px;background:transparent;border:1px solid #dadce0;border-radius:3px;cursor:pointer;font-size:14px;line-height:1;">−</button>
                            <span class="excel-zoom-level" style="font-size:12px;min-width:40px;text-align:center;">100%</span>
                            <button onclick="if(window._excelPreview) window._excelPreview.zoomIn()" style="padding:2px 8px;background:transparent;border:1px solid #dadce0;border-radius:3px;cursor:pointer;font-size:14px;line-height:1;">+</button>
                        </div>
                        
                        <!-- Actions -->
                        ${this.canDownload ? `
                        <button onclick="if(window._excelPreview) window._excelPreview.exportToCsv()" style="padding:4px 12px;background:#217346;color:white;border:none;border-radius:4px;cursor:pointer;font-size:12px;display:flex;align-items:center;gap:4px;font-weight:500;">
                            <i class="fas fa-file-csv"></i> CSV
                        </button>
                        <button onclick="window.location.href='${this.downloadUrl}'" style="padding:4px 12px;background:#1a73e8;color:white;border:none;border-radius:4px;cursor:pointer;font-size:12px;display:flex;align-items:center;gap:4px;font-weight:500;">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        ` : `
                        <span style="padding:4px 12px;background:#fce8e6;color:#d93025;border-radius:4px;font-size:12px;display:flex;align-items:center;gap:4px;font-weight:500;">
                            <i class="fas fa-eye"></i> View Only
                        </span>
                        `}
                        <button onclick="if(window._excelPreview) window._excelPreview.destroy()" style="padding:4px 12px;background:transparent;border:1px solid #dadce0;border-radius:4px;cursor:pointer;font-size:12px;color:#5f6368;">
                            ✕ Close
                        </button>
                    </div>
                </div>
                
                <!-- Sheet Container -->
                <div class="excel-sheet-container" style="flex:1;overflow:hidden;position:relative;background:#e6e9ed;padding:2px;">
                    <div class="excel-sheet-scroll" style="width:100%;height:100%;overflow:auto;position:relative;">
                        <div class="excel-sheet-content" style="background:white;border-radius:2px;box-shadow:0 1px 3px rgba(0,0,0,0.08);display:inline-block;min-width:100%;min-height:100%;position:relative;">
                            <div class="excel-loading" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 20px;color:#5f6368;font-family:'Segoe UI',Arial,sans-serif;">
                                <div class="excel-spinner" style="width:36px;height:36px;border:3px solid #e8eaed;border-top-color:#1a73e8;border-radius:50%;animation:spin 0.8s linear infinite;margin-bottom:16px;"></div>
                                <p style="margin:0;font-size:14px;">Loading spreadsheet...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Status Bar -->
                <div class="excel-statusbar" style="background:#f3f4f6;border-top:1px solid #d0d3d8;padding:2px 16px;display:flex;justify-content:space-between;font-size:11px;color:#5f6368;flex-shrink:0;">
                    <span class="excel-status-left">📊 Ready</span>
                    <span class="excel-status-center">Sheet 1 of 1</span>
                    <span class="excel-status-right">Cells: 0</span>
                </div>
            </div>
        `;
        
        // Add styles
        this.addStyles();
        
        // Store reference
        window._excelPreview = this;
    }

    /**
     * Add required CSS styles
     */
    addStyles() {
        if (document.getElementById('excel-preview-styles')) return;
        
        const style = document.createElement('style');
        style.id = 'excel-preview-styles';
        style.textContent = `
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            
            .excel-sheet-scroll::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }
            .excel-sheet-scroll::-webkit-scrollbar-track {
                background: #e6e9ed;
            }
            .excel-sheet-scroll::-webkit-scrollbar-thumb {
                background: #c1c7cd;
                border-radius: 4px;
            }
            .excel-sheet-scroll::-webkit-scrollbar-thumb:hover {
                background: #a8adb4;
            }
            
            .excel-sheet-content table {
                border-collapse: collapse;
                font-family: 'Segoe UI', Arial, sans-serif;
                font-size: 13px;
                width: 100%;
                table-layout: fixed;
                border: 1px solid #d0d3d8;
            }
            
            .excel-sheet-content table th,
            .excel-sheet-content table td {
                border: 1px solid #d0d3d8;
                padding: 4px 8px;
                min-width: 60px;
                max-width: 300px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                cursor: default;
                user-select: none;
            }
            
            .excel-sheet-content table .excel-row-header {
                background: #f8f9fa;
                color: #5f6368;
                font-size: 10px;
                font-weight: 500;
                text-align: center;
                min-width: 30px;
                width: 30px;
                position: sticky;
                left: 0;
                z-index: 2;
                user-select: none;
            }
            
            .excel-sheet-content table .excel-col-header {
                background: #f3f4f6;
                color: #5f6368;
                font-size: 10px;
                font-weight: 600;
                text-align: center;
                min-width: 60px;
                width: 60px;
                position: sticky;
                top: 0;
                z-index: 3;
                user-select: none;
            }
            
            .excel-sheet-content table .excel-corner {
                background: #f3f4f6;
                position: sticky;
                top: 0;
                left: 0;
                z-index: 4;
                width: 30px;
                min-width: 30px;
            }
            
            .excel-sheet-content table tr:nth-child(even) td:not(.excel-row-header) {
                background: #fafbfc;
            }
            
            .excel-sheet-content table .excel-search-highlight {
                background: #ffeb3b !important;
            }
            
            .excel-sheet-content table .excel-number {
                color: #0d652d;
                text-align: right;
            }
            
            .excel-sheet-content table .excel-date {
                color: #1967d2;
            }
            
            .excel-sheet-content table .excel-boolean {
                color: #d93025;
                text-align: center;
            }
            
            .excel-sheet-content table .excel-text {
                color: #202124;
                text-align: left;
            }
            
            .excel-sheet-content table .excel-currency {
                color: #0d652d;
                text-align: right;
                font-weight: 500;
            }
            
            .excel-sheet-content table .excel-percentage {
                color: #0d652d;
                text-align: right;
            }
            
            .excel-toolbar button:hover {
                background: #e8eaed !important;
            }
            
            .excel-toolbar button:active {
                background: #dadce0 !important;
            }
            
            .excel-search-input:focus {
                border-color: #1a73e8;
            }
            
            @media (max-width: 768px) {
                .excel-toolbar {
                    padding: 4px 8px;
                }
                .excel-toolbar-left .excel-filename {
                    max-width: 100px;
                    font-size: 12px;
                }
                .excel-search-input {
                    width: 80px !important;
                    font-size: 11px !important;
                }
                .excel-toolbar-right {
                    gap: 2px;
                }
                .excel-sheet-content table {
                    font-size: 11px;
                }
                .excel-sheet-content table th,
                .excel-sheet-content table td {
                    padding: 2px 4px;
                    min-width: 40px;
                    font-size: 11px;
                }
            }
            
            @media (max-width: 480px) {
                .excel-toolbar {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 4px;
                }
                .excel-toolbar-left {
                    justify-content: center;
                }
                .excel-toolbar-right {
                    justify-content: center;
                    flex-wrap: wrap;
                }
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Load Excel from URL
     */
    loadFromUrl(url, options = {}) {
        // Check if SheetJS is loaded
        if (typeof XLSX === 'undefined') {
            this.showError('XLSX library not loaded. Please include SheetJS.');
            return Promise.reject('XLSX not loaded');
        }
        
        return fetch(url, {
            credentials: 'include',
            ...options
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            return response.arrayBuffer();
        })
        .then(buffer => this.loadFromBuffer(buffer))
        .catch(error => {
            this.showError('Failed to load file: ' + error.message);
            throw error;
        });
    }

    /**
     * Load Excel from ArrayBuffer
     */
    loadFromBuffer(buffer) {
        this.isLoading = true;
        this.showLoading();
        
        try {
            if (typeof XLSX === 'undefined') {
                throw new Error('XLSX library not loaded');
            }
            
            this.workbook = XLSX.read(buffer, {
                type: 'array',
                cellDates: true,
                dateNF: 'yyyy-mm-dd hh:mm:ss',
                cellStyles: true,
                cellHTML: true,
                sheetStubs: true,
                dense: false,
                cellFormula: true
            });
            
            this.sheetNames = this.workbook.SheetNames;
            
            if (this.sheetNames.length === 0) {
                throw new Error('No sheets found in the workbook');
            }
            
            this.currentSheetIndex = 0;
            this.sheetCache.clear();
            
            // Render first sheet
            this.renderSheet(this.sheetNames[0], 0);
            
            // Update sheet indicator
            this.updateSheetIndicator();
            
            this.isLoading = false;
            return this;
            
        } catch (error) {
            this.isLoading = false;
            this.showError('Failed to parse Excel file: ' + error.message);
            throw error;
        }
    }

    /**
     * Render a specific sheet
     */
    renderSheet(sheetName, index) {
        const container = this.container.querySelector('.excel-sheet-content');
        if (!container) return;
        
        const worksheet = this.workbook.Sheets[sheetName];
        if (!worksheet) {
            this.showError('Sheet not found: ' + sheetName);
            return;
        }
        
        // Check cache
        const cacheKey = sheetName + '_' + this.zoom + '_' + this.searchTerm;
        if (this.sheetCache.has(cacheKey)) {
            container.innerHTML = this.sheetCache.get(cacheKey);
            this.updateStatusBar(index);
            return;
        }
        
        try {
            // Get data range
            const range = XLSX.utils.decode_range(worksheet['!ref'] || 'A1:A1');
            
            // Limit rows and columns for performance
            const maxRows = Math.min(range.e.r - range.s.r + 1, this.settings.maxRows);
            const maxCols = Math.min(range.e.c - range.s.c + 1, this.settings.maxCols);
            
            // Build data array
            const data = [];
            for (let r = 0; r < maxRows; r++) {
                const row = [];
                for (let c = 0; c < maxCols; c++) {
                    const cellAddress = XLSX.utils.encode_cell({
                        r: range.s.r + r,
                        c: range.s.c + c
                    });
                    const cell = worksheet[cellAddress];
                    if (cell) {
                        let value = cell.v;
                        let displayValue = value !== undefined && value !== null ? String(value) : '';
                        
                        // Handle dates
                        if (cell.t === 'd') {
                            try {
                                displayValue = new Date(value).toLocaleString();
                            } catch (e) {
                                displayValue = String(value);
                            }
                        }
                        
                        // Handle formulas
                        if (cell.f) {
                            displayValue = displayValue || '=' + cell.f;
                        }
                        
                        row.push({
                            value: displayValue,
                            rawValue: value,
                            type: cell.t || 's',
                            style: cell.s || {},
                            formula: cell.f || null,
                            cell: cell
                        });
                    } else {
                        row.push({
                            value: '',
                            rawValue: null,
                            type: 's',
                            style: {},
                            formula: null,
                            cell: null
                        });
                    }
                }
                data.push(row);
            }
            
            // Build HTML table
            const html = this.buildTableHtml(data, range, maxRows, maxCols);
            
            // Cache the result
            this.sheetCache.set(cacheKey, html);
            
            // Update container
            container.innerHTML = html;
            
            // Update status bar
            this.updateStatusBar(index);
            
            this.currentSheetIndex = index;
            
        } catch (error) {
            this.showError('Failed to render sheet: ' + error.message);
        }
    }

    /**
     * Build HTML table
     */
    buildTableHtml(data, range, maxRows, maxCols) {
        let html = '<table>';
        
        // Header row
        html += '<thead><tr>';
        // Corner cell
        html += '<th class="excel-corner"></th>';
        // Column letters
        for (let c = 0; c < maxCols; c++) {
            const colLetter = XLSX.utils.encode_col(range.s.c + c);
            html += `<th class="excel-col-header" style="min-width:${this.settings.colWidth}px;width:${this.settings.colWidth}px;">${colLetter}</th>`;
        }
        html += '</tr></thead>';
        
        // Data rows
        html += '<tbody>';
        for (let r = 0; r < maxRows; r++) {
            const rowNum = range.s.r + r + 1;
            const rowData = data[r];
            
            html += `<tr>`;
            // Row number
            html += `<td class="excel-row-header" style="min-width:${this.settings.rowNumberWidth}px;width:${this.settings.rowNumberWidth}px;">${rowNum}</td>`;
            
            // Cells
            for (let c = 0; c < maxCols; c++) {
                const cell = rowData[c];
                if (!cell) {
                    html += `<td class="excel-empty"></td>`;
                    continue;
                }
                
                let value = cell.value || '';
                const type = cell.type;
                const rawValue = cell.rawValue;
                
                // Determine styling
                let classes = '';
                let style = '';
                let displayValue = value;
                
                // Number formatting
                if (type === 'n' && rawValue !== null && !isNaN(rawValue)) {
                    classes = 'excel-number';
                    
                    if (typeof rawValue === 'number') {
                        if (value.includes('$') || value.includes('€') || value.includes('£')) {
                            classes = 'excel-currency';
                        } else if (value.includes('%')) {
                            classes = 'excel-percentage';
                        }
                    }
                } else if (type === 'd') {
                    classes = 'excel-date';
                } else if (type === 'b') {
                    classes = 'excel-boolean';
                }
                
                // Apply search highlight
                if (this.searchTerm && value.toLowerCase().includes(this.searchTerm.toLowerCase())) {
                    classes += ' excel-search-highlight';
                }
                
                // Apply cell styling
                if (cell.style) {
                    const s = cell.style;
                    if (s.bold) style += 'font-weight:bold;';
                    if (s.italic) style += 'font-style:italic;';
                    if (s.underline) style += 'text-decoration:underline;';
                    if (s.fgColor) style += `color:${this.rgbToHex(s.fgColor)};`;
                    if (s.bgColor) style += `background:${this.rgbToHex(s.bgColor)};`;
                }
                
                // Truncate long text
                if (displayValue.length > 200) {
                    displayValue = displayValue.substring(0, 200) + '...';
                }
                
                html += `<td class="${classes}" style="${style}" title="${this.escapeHtml(String(value || ''))}">${this.escapeHtml(displayValue)}</td>`;
            }
            
            html += '</tr>';
        }
        html += '</tbody></table>';
        
        return html;
    }

    /**
     * Switch to a specific sheet
     */
    switchSheet(index) {
        if (index < 0 || index >= this.sheetNames.length) return;
        if (index === this.currentSheetIndex) return;
        
        this.currentSheetIndex = index;
        this.renderSheet(this.sheetNames[index], index);
        this.updateSheetIndicator();
        
        // Clear search when switching sheets
        this.clearSearch();
    }

    /**
     * Go to previous sheet
     */
    prevSheet() {
        if (this.currentSheetIndex > 0) {
            this.switchSheet(this.currentSheetIndex - 1);
        }
    }

    /**
     * Go to next sheet
     */
    nextSheet() {
        if (this.currentSheetIndex < this.sheetNames.length - 1) {
            this.switchSheet(this.currentSheetIndex + 1);
        }
    }

    /**
     * Search within the current sheet
     */
    search(term) {
        this.searchTerm = term.trim();
        const searchInput = this.container.querySelector('.excel-search-input');
        const clearBtn = this.container.querySelector('.excel-search-clear');
        
        if (clearBtn) {
            clearBtn.style.display = this.searchTerm ? 'block' : 'none';
        }
        
        if (this.sheetNames.length > 0) {
            this.renderSheet(this.sheetNames[this.currentSheetIndex], this.currentSheetIndex);
        }
    }

    /**
     * Clear search
     */
    clearSearch() {
        const searchInput = this.container.querySelector('.excel-search-input');
        if (searchInput) {
            searchInput.value = '';
        }
        this.search('');
    }

    /**
     * Zoom in
     */
    zoomIn() {
        if (this.zoom < 200) {
            this.zoom += 10;
            this.applyZoom();
        }
    }

    /**
     * Zoom out
     */
    zoomOut() {
        if (this.zoom > 50) {
            this.zoom -= 10;
            this.applyZoom();
        }
    }

    /**
     * Reset zoom
     */
    resetZoom() {
        this.zoom = 100;
        this.applyZoom();
    }

    /**
     * Apply zoom level
     */
    applyZoom() {
        const container = this.container.querySelector('.excel-sheet-scroll');
        if (container) {
            container.style.transform = `scale(${this.zoom / 100})`;
            container.style.transformOrigin = 'top left';
            
            const zoomLabel = this.container.querySelector('.excel-zoom-level');
            if (zoomLabel) {
                zoomLabel.textContent = this.zoom + '%';
            }
        }
    }

    /**
     * Export to CSV
     */
    exportToCsv() {
        if (!this.workbook) return;
        
        const sheetName = this.sheetNames[this.currentSheetIndex];
        const worksheet = this.workbook.Sheets[sheetName];
        const csv = XLSX.utils.sheet_to_csv(worksheet);
        
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.href = url;
        link.download = this.fileName.replace(/\.[^.]+$/, '') + '.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    /**
     * Update sheet indicator
     */
    updateSheetIndicator() {
        const indicator = this.container.querySelector('.excel-sheet-indicator');
        if (indicator) {
            const total = this.sheetNames.length;
            const current = this.currentSheetIndex + 1;
            const name = this.sheetNames[this.currentSheetIndex] || 'Sheet';
            indicator.textContent = `${current}/${total} • ${this.escapeHtml(name)}`;
        }
    }

    /**
     * Update status bar
     */
    updateStatusBar(index) {
        const statusCenter = this.container.querySelector('.excel-status-center');
        const statusRight = this.container.querySelector('.excel-status-right');
        
        if (statusCenter) {
            const total = this.sheetNames.length;
            const current = index + 1;
            const name = this.sheetNames[index] || 'Sheet';
            statusCenter.textContent = `Sheet ${current} of ${total} • ${this.escapeHtml(name)}`;
        }
        
        if (statusRight) {
            const worksheet = this.workbook.Sheets[this.sheetNames[index]];
            if (worksheet) {
                const range = XLSX.utils.decode_range(worksheet['!ref'] || 'A1:A1');
                const rows = range.e.r - range.s.r + 1;
                const cols = range.e.c - range.s.c + 1;
                statusRight.textContent = `📊 ${(rows * cols).toLocaleString()} cells • ${rows} rows × ${cols} cols`;
            }
        }
        
        const statusLeft = this.container.querySelector('.excel-status-left');
        if (statusLeft) {
            statusLeft.textContent = '📊 Ready';
        }
    }

    /**
     * Show loading indicator
     */
    showLoading() {
        const container = this.container.querySelector('.excel-sheet-content');
        if (container) {
            container.innerHTML = `
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 20px;color:#5f6368;font-family:'Segoe UI',Arial,sans-serif;">
                    <div style="width:36px;height:36px;border:3px solid #e8eaed;border-top-color:#1a73e8;border-radius:50%;animation:spin 0.8s linear infinite;margin-bottom:16px;"></div>
                    <p style="margin:0;font-size:14px;">Loading spreadsheet...</p>
                </div>
            `;
        }
    }

    /**
     * Show error message
     */
    showError(message) {
        const container = this.container.querySelector('.excel-sheet-content');
        if (container) {
            container.innerHTML = `
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 20px;color:#ea4335;font-family:'Segoe UI',Arial,sans-serif;text-align:center;">
                    <i class="fas fa-exclamation-triangle" style="font-size:48px;margin-bottom:16px;"></i>
                    <p style="margin:0 0 8px;font-size:16px;font-weight:500;">Failed to load spreadsheet</p>
                    <p style="margin:0;font-size:14px;color:#5f6368;">${this.escapeHtml(message)}</p>
                </div>
            `;
        }
    }

    /**
     * Destroy the preview
     */
    destroy() {
        if (this.container) {
            this.container.innerHTML = '';
        }
        if (window._excelPreview === this) {
            delete window._excelPreview;
        }
        if (typeof closePreviewModal === 'function') {
            closePreviewModal();
        }
    }

    /**
     * Helper: Convert RGB to Hex
     */
    rgbToHex(rgb) {
        if (!rgb) return '';
        if (typeof rgb === 'string') return rgb;
        if (typeof rgb === 'object' && rgb.r !== undefined) {
            const r = Math.round(rgb.r * 255);
            const g = Math.round(rgb.g * 255);
            const b = Math.round(rgb.b * 255);
            return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
        }
        return '';
    }

    /**
     * Helper: Escape HTML
     */
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Helper: Format file size
     */
    formatFileSize(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ExcelPreview;
}