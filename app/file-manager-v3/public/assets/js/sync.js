// public/assets/js/sync.js
// Sync Functionality - Complete UI Integration

console.log('🔄 sync.js loading...');

// ============================================
// LOAD SYNC DATA
// ============================================

function loadSyncData() {
    getSyncStatus();
    loadSyncFolders();
    loadUnsyncedFiles();
}

// ============================================
// GET SYNC STATUS
// ============================================

function getSyncStatus() {
    console.log('📊 Getting sync status...');
    
    fetch(window.API_BASE + '/sync/status', {
        credentials: 'include',
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        console.log('📊 Sync status:', data);
        
        if (data.success && data.status) {
            const status = data.status;
            
            // Update stats
            document.getElementById('syncTotal').textContent = status.total_files || 0;
            document.getElementById('syncSynced').textContent = status.files_synced || 0;
            document.getElementById('syncPending').textContent = status.queue_stats?.pending || 0;
            document.getElementById('syncErrors').textContent = status.queue_stats?.failed || 0;
            
            // Update sync folder path
            const folderDisplay = document.getElementById('syncFolderDisplay');
            if (folderDisplay && status.sync_folder) {
                folderDisplay.textContent = status.sync_folder;
            }

            if (status.last_sync) {
                const lastSyncEl = document.getElementById('lastSyncTime');
                if (lastSyncEl) {
                    lastSyncEl.textContent = 'Last sync: ' + formatDate(status.last_sync);
                }
            }
            
            // Update status indicator
            updateSyncStatusIndicator(status.status);
        }
    })
    .catch(error => {
        console.warn('⚠️ Could not load sync status:', error);
    });
}

// ============================================
// UPDATE SYNC STATUS INDICATOR
// ============================================

function updateSyncStatusIndicator(status) {
    const indicator = document.querySelector('.sync-status');
    if (!indicator) return;
    
    const statusMap = {
        'idle': { text: 'Ready', color: '#9aa0a6', icon: 'fa-circle' },
        'running': { text: 'Syncing...', color: '#fbbc04', icon: 'fa-spinner fa-spin' },
        'completed': { text: 'Sync Complete', color: '#34a853', icon: 'fa-check-circle' },
        'failed': { text: 'Sync Failed', color: '#ea4335', icon: 'fa-exclamation-circle' }
    };
    
    const s = statusMap[status] || statusMap['idle'];
    
    // Update status indicator if it exists
    const indicatorEl = document.querySelector('.sync-status-indicator');
    if (indicatorEl) {
        indicatorEl.innerHTML = `<i class="fas ${s.icon}" style="color:${s.color};"></i> ${s.text}`;
    }
}

// ============================================
// LOAD SYNC FOLDERS
// ============================================

function loadSyncFolders() {
    const container = document.getElementById('syncFoldersList');
    if (!container) return;
    
    fetch(window.API_BASE + '/sync/folders', {
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.folders && data.folders.length > 0) {
            let html = '';
            data.folders.forEach(folder => {
                html += `
                    <div class="sync-folder-item">
                        <div class="sync-folder-info">
                            <i class="fas fa-folder"></i>
                            <div>
                                <div class="sync-folder-name">${escapeHtml(folder.folder_name)}</div>
                                <div class="sync-folder-path">${escapeHtml(folder.local_path)}</div>
                                <div class="sync-folder-meta">
                                    Last sync: ${folder.last_sync ? formatDate(folder.last_sync) : 'Never'}
                                    <span class="sync-status-badge ${folder.sync_enabled ? 'enabled' : 'disabled'}">
                                        ${folder.sync_enabled ? 'Active' : 'Paused'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        } else {
            container.innerHTML = `
                <div class="sync-empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>Sync is ready</p>
                    <p class="hint">Click "Init Sync" to set up your sync folder</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.warn('⚠️ Could not load sync folders:', error);
        container.innerHTML = `
            <div class="sync-empty-state">
                <i class="fas fa-folder-open"></i>
                <p>Sync is ready</p>
                <p class="hint">Click "Init Sync" to set up your sync folder</p>
            </div>
        `;
    });
}

// ============================================
// LOAD UNSYNCED FILES
// ============================================

function loadUnsyncedFiles() {
    const container = document.getElementById('syncUnsyncedList');
    if (!container) return;
    
    fetch(window.API_BASE + '/sync/unsynced', {
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.files && data.files.length > 0) {
            document.getElementById('unsyncedCount').textContent = data.files.length;
            
            let html = '';
            data.files.forEach(file => {
                html += `
                    <div class="sync-unsynced-item">
                        <div class="file-icon">
                            <i class="fas fa-file"></i>
                        </div>
                        <div class="file-info">
                            <div class="file-name">${escapeHtml(file.name)}</div>
                            <div class="file-path">${escapeHtml(file.path)}</div>
                        </div>
                        <span class="file-size">${formatFileSize(file.size)}</span>
                        <span class="file-status ${file.status}">${file.status}</span>
                    </div>
                `;
            });
            container.innerHTML = html;
            container.classList.add('open');
        } else {
            document.getElementById('unsyncedCount').textContent = '0';
            container.innerHTML = `
                <div class="sync-empty-state">
                    <i class="fas fa-check-circle" style="color:#34a853;"></i>
                    <p>All files are synced! 🎉</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.warn('⚠️ Could not load unsynced files:', error);
        document.getElementById('unsyncedCount').textContent = '0';
        container.innerHTML = `
            <div class="sync-empty-state">
                <i class="fas fa-check-circle" style="color:#34a853;"></i>
                <p>All files are synced! 🎉</p>
            </div>
        `;
    });
}

// ============================================
// INITIALIZE SYNC
// ============================================

function initSync() {
    console.log('📁 Initializing sync...');
    
    const btn = document.querySelector('.sync-add-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Initializing...';
    }
    
    // Use the global showToast
    if (typeof window.showToast === 'function') {
        window.showToast('Initializing sync folder...', 'info');
    } else {
        console.log('🔔 Toast: info - Initializing sync folder...');
    }
    
    fetch(window.API_BASE + '/sync/init', {
        method: 'POST',
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        console.log('📁 Init response:', data);
        
        if (data.success) {
            if (typeof window.showToast === 'function') {
                window.showToast('Sync initialized successfully!', 'success');
            }
            getSyncStatus();
            loadSyncFolders();
            
            const folderDisplay = document.getElementById('syncFolderDisplay');
            if (folderDisplay && data.folder_path) {
                folderDisplay.textContent = data.folder_path;
            }
        } else {
            if (typeof window.showToast === 'function') {
                window.showToast('Failed to initialize sync: ' + data.message, 'error');
            }
        }
    })
    .catch(error => {
        console.error('❌ Init error:', error);
        if (typeof window.showToast === 'function') {
            window.showToast('Error initializing sync', 'error');
        }
    })
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-folder-plus"></i> Init Sync';
        }
    });
}

// ============================================
// RUN SYNC
// ============================================

function runSync() {
    console.log('🔄 Running sync...');
    
    const btn = document.getElementById('syncRunBtn');
    if (!btn) return;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';
    
    if (typeof window.showToast === 'function') {
        window.showToast('Starting synchronization...', 'info');
    }
    
    fetch(window.API_BASE + '/sync/start', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        console.log('🔄 Sync response:', data);
        
        if (data.success) {
            const result = data;
            const message = result.message || 'Sync completed successfully!';
            const details = `Uploaded: ${result.uploaded || 0}, Updated: ${result.updated || 0}, Deleted: ${result.deleted || 0}, Skipped: ${result.skipped || 0}`;
            
            // Show success toast with details
            showToast(`${message} (${details})`, result.errors && result.errors.length > 0 ? 'warning' : 'success');
            
            // Show error details if any
            if (result.errors && result.errors.length > 0) {
                console.warn('⚠️ Sync errors:', result.errors);
                if (result.errors.length <= 3) {
                    result.errors.forEach(err => showToast('Error: ' + err, 'error'));
                } else {
                    showToast(`Errors: ${result.errors.length} files failed to sync`, 'error');
                }
            }
            
            // Reload sync status and unsynced files
            getSyncStatus();
            loadUnsyncedFiles();
        } else {
            showToast('Sync failed: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('❌ Sync error:', error);
        if (typeof window.showToast === 'function') {
            window.showToast('Error during sync: ' + error.message, 'error');
        }
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-play"></i> Sync Now';
    });
}

// ============================================
// DOWNLOAD FROM CLOUD TO LOCAL
// ============================================

function downloadFiles() {
    console.log('📥 Downloading files from cloud...');
    
    showToast('Downloading files from cloud...', 'info');
    
    fetch(window.API_BASE + '/sync/download', {
        method: 'POST',
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`Downloaded ${data.downloaded || 0} files to local folder`, 'success');
            getSyncStatus();
        } else {
            showToast('Download failed: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('❌ Download error:', error);
        showToast('Error downloading files', 'error');
    });
}

// ============================================
// TOGGLE UNSYNCED FILES
// ============================================

function toggleUnsyncedFiles() {
    const list = document.getElementById('syncUnsyncedList');
    const icon = document.getElementById('unsyncedToggleIcon');
    
    if (list) {
        list.classList.toggle('open');
    }
    if (icon) {
        icon.classList.toggle('open');
    }
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function formatDate(dateString) {
    if (!dateString) return 'Never';
    const date = new Date(dateString);
    return date.toLocaleString();
}

function formatFileSize(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}



// ============================================
// AUTO-LOAD ON PAGE LOAD
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Load sync data after a small delay
    setTimeout(function() {
        loadSyncData();
    }, 1000);
});

// Make functions globally accessible
window.initSync = initSync;
window.runSync = runSync;
window.loadSyncData = loadSyncData;
window.getSyncStatus = getSyncStatus;
window.loadSyncFolders = loadSyncFolders;
window.loadUnsyncedFiles = loadUnsyncedFiles;
window.downloadFiles = downloadFiles;
window.toggleUnsyncedFiles = toggleUnsyncedFiles;

console.log('🔄 Sync module loaded');
console.log('✅ initSync available:', typeof window.initSync);
console.log('✅ runSync available:', typeof window.runSync);