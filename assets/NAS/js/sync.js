// assets/NAS/js/sync.js
// Browser-driven manual sync for Efactor Drive.
//
// Local I/O happens in the user's browser through the File System Access API.
// PHP never attempts to create C:\Users\... on the web server.

console.log('🔄 Browser sync module loading...');

const EFACTOR_SYNC_DB = 'efactor_nas_sync';
const EFACTOR_SYNC_STORE = 'handles';

function getSyncUsername() {
    const raw = String(window._username || '').trim();
    const safe = raw.replace(/[^a-zA-Z0-9._-]/g, '');

    if (!safe) {
        throw new Error('Logged-in username is unavailable. Please sign in again before using sync.');
    }

    return safe;
}


function getDirectoryPickerId() {
    const username = getSyncUsername().toLowerCase();

    // showDirectoryPicker() limits id to 32 characters.
    // Keep the visible prefix short and add a deterministic hash so long
    // usernames/emails still get their own stable picker identity.
    let hash = 2166136261;
    for (let i = 0; i < username.length; i++) {
        hash ^= username.charCodeAt(i);
        hash = Math.imul(hash, 16777619);
    }

    const hashHex = (hash >>> 0).toString(16).padStart(8, '0');
    const shortName = username.replace(/[^a-z0-9_-]/g, '').slice(0, 12) || 'user';
    return 'efs-' + shortName + '-' + hashHex;
}

// Browser permission/handle is isolated per logged-in username.
const EFACTOR_SYNC_HANDLE_KEY = 'mydrive_username_' + getSyncUsername().toLowerCase();
const EFACTOR_SYNC_MAX_FILE_SIZE = 100 * 1024 * 1024;

function syncFetch(url, options = {}) {
    if (typeof window.nasFetch === 'function') {
        return window.nasFetch(url, options);
    }
    return fetch(url, {
        credentials: 'include',
        ...options
    });
}

async function syncJson(url, options = {}) {
    const response = await syncFetch(url, options);
    const text = await response.text();
    let data;
    try {
        data = text ? JSON.parse(text) : {};
    } catch (e) {
        throw new Error('Invalid server response (HTTP ' + response.status + ')');
    }

    if (!response.ok || data.success === false) {
        throw new Error(data.message || 'Sync request failed (HTTP ' + response.status + ')');
    }
    return data;
}

function syncToast(message, type = 'info') {
    if (typeof window.showToast === 'function') {
        window.showToast(message, type);
    } else {
        console.log('🔔', type, message);
    }
}

function supportsLocalSync() {
    return typeof window.showDirectoryPicker === 'function'
        && typeof FileSystemDirectoryHandle !== 'undefined';
}

function openSyncDb() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(EFACTOR_SYNC_DB, 1);
        request.onupgradeneeded = () => {
            const db = request.result;
            if (!db.objectStoreNames.contains(EFACTOR_SYNC_STORE)) {
                db.createObjectStore(EFACTOR_SYNC_STORE);
            }
        };
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error || new Error('Unable to open sync database'));
    });
}

async function saveSyncHandle(handle) {
    const db = await openSyncDb();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(EFACTOR_SYNC_STORE, 'readwrite');
        tx.objectStore(EFACTOR_SYNC_STORE).put(handle, EFACTOR_SYNC_HANDLE_KEY);
        tx.oncomplete = () => {
            db.close();
            resolve();
        };
        tx.onerror = () => {
            db.close();
            reject(tx.error || new Error('Unable to save sync folder permission'));
        };
    });
}

async function loadSyncHandle() {
    const db = await openSyncDb();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(EFACTOR_SYNC_STORE, 'readonly');
        const request = tx.objectStore(EFACTOR_SYNC_STORE).get(EFACTOR_SYNC_HANDLE_KEY);
        request.onsuccess = () => resolve(request.result || null);
        request.onerror = () => reject(request.error || new Error('Unable to load sync folder permission'));
        tx.oncomplete = () => db.close();
    });
}

async function ensureHandlePermission(handle, ask = true) {
    if (!handle) return false;
    const options = { mode: 'readwrite' };

    if (typeof handle.queryPermission === 'function') {
        const current = await handle.queryPermission(options);
        if (current === 'granted') return true;
    }

    if (ask && typeof handle.requestPermission === 'function') {
        return (await handle.requestPermission(options)) === 'granted';
    }

    return false;
}

/**
 * First Init Sync:
 * user selects a destination folder.
 * The browser then creates Efactor\<username>\MyDrive below it.
 * This prevents two application users who select the same destination
 * from sharing the same physical MyDrive folder.
 */
async function chooseMyDriveRoot() {
    if (!supportsLocalSync()) {
        throw new Error('Local sync requires a browser with directory read/write support, such as current Chrome or Edge.');
    }

    syncToast('Choose the destination folder. Efactor\\' + getSyncUsername() + '\\MyDrive will be created inside it.', 'info');

    const baseHandle = await window.showDirectoryPicker({
        id: getDirectoryPickerId(),
        mode: 'readwrite'
    });

    if (!(await ensureHandlePermission(baseHandle, true))) {
        throw new Error('Write permission for the selected folder was not granted');
    }

    const username = getSyncUsername();
    const efactorHandle = await baseHandle.getDirectoryHandle('Efactor', { create: true });
    const userHandle = await efactorHandle.getDirectoryHandle(username, { create: true });
    const myDriveHandle = await userHandle.getDirectoryHandle('MyDrive', { create: true });
    await saveSyncHandle(myDriveHandle);
    return myDriveHandle;
}

async function getMyDriveRoot(allowPicker = false) {
    if (!supportsLocalSync()) {
        throw new Error('This browser does not support Efactor local folder synchronization');
    }

    let handle = null;
    try {
        handle = await loadSyncHandle();
    } catch (e) {
        console.warn('Could not load saved sync folder:', e);
    }

    if (handle && await ensureHandlePermission(handle, true)) {
        return handle;
    }

    if (allowPicker) {
        return chooseMyDriveRoot();
    }

    throw new Error('Local sync folder is not connected. Click Init Sync first.');
}

function normalizeLocalPath(path) {
    return String(path || '')
        .replace(/\\/g, '/')
        .replace(/\/+/g, '/')
        .replace(/^\/+|\/+$/g, '');
}

async function getDirectoryForPath(rootHandle, relativeDirectory, create = true) {
    const normalized = normalizeLocalPath(relativeDirectory);
    let current = rootHandle;
    if (!normalized) return current;

    for (const segment of normalized.split('/')) {
        if (!segment || segment === '.' || segment === '..') {
            throw new Error('Invalid local sync folder path');
        }
        current = await current.getDirectoryHandle(segment, { create });
    }
    return current;
}

async function localFileExists(rootHandle, relativePath) {
    const normalized = normalizeLocalPath(relativePath);
    const parts = normalized.split('/');
    const fileName = parts.pop();
    if (!fileName) return false;

    try {
        const dir = await getDirectoryForPath(rootHandle, parts.join('/'), false);
        const handle = await dir.getFileHandle(fileName, { create: false });
        const file = await handle.getFile();
        return { exists: true, file };
    } catch (e) {
        if (e && e.name === 'NotFoundError') return { exists: false, file: null };
        throw e;
    }
}

async function writeBlobToLocal(rootHandle, relativePath, blob, skipExisting = true) {
    const normalized = normalizeLocalPath(relativePath);
    const parts = normalized.split('/');
    const fileName = parts.pop();
    if (!fileName) throw new Error('Invalid file path: ' + relativePath);

    if (skipExisting) {
        const existing = await localFileExists(rootHandle, normalized);
        if (existing.exists) {
            return { written: false, skipped: true };
        }
    }

    const dir = await getDirectoryForPath(rootHandle, parts.join('/'), true);
    const fileHandle = await dir.getFileHandle(fileName, { create: true });
    const writable = await fileHandle.createWritable();
    try {
        await writable.write(blob);
    } finally {
        await writable.close();
    }
    return { written: true, skipped: false };
}

async function scanLocalTree(rootHandle, prefix = '') {
    const folders = [];
    const files = [];

    for await (const [name, handle] of rootHandle.entries()) {
        if (name === '.DS_Store' || name === '.efactor') continue;
        const relativePath = normalizeLocalPath(prefix ? prefix + '/' + name : name);

        if (handle.kind === 'directory') {
            folders.push(relativePath);
            const child = await scanLocalTree(handle, relativePath);
            folders.push(...child.folders);
            files.push(...child.files);
        } else if (handle.kind === 'file') {
            const file = await handle.getFile();
            files.push({
                relative_path: relativePath,
                file,
                size: file.size,
                modified: file.lastModified
            });
        }
    }

    return { folders, files };
}

function isComputersPath(path) {
    const normalized = normalizeLocalPath(path);
    return normalized === 'Computers' || normalized.startsWith('Computers/');
}

async function fetchManifest() {
    const data = await syncJson(window.API_BASE + '/sync/manifest', {
        method: 'GET',
        credentials: 'include',
        headers: { 'Accept': 'application/json' }
    });
    return data.manifest || { cluster_roots: [], folders: [], files: [] };
}

async function pullManifestToLocal(rootHandle, manifest) {
    const result = {
        folders_created: 0,
        downloaded: 0,
        skipped: 0,
        errors: [],
        synced_files: []
    };

    const roots = Array.isArray(manifest.cluster_roots) ? manifest.cluster_roots : [];
    for (const root of roots) {
        try {
            await getDirectoryForPath(rootHandle, root, true);
            result.folders_created++;
        } catch (e) {
            result.errors.push('Folder ' + root + ': ' + e.message);
        }
    }

    const folders = Array.isArray(manifest.folders) ? manifest.folders : [];
    for (const folder of folders) {
        try {
            await getDirectoryForPath(rootHandle, folder.relative_path, true);
            result.folders_created++;
        } catch (e) {
            result.errors.push('Folder ' + folder.relative_path + ': ' + e.message);
        }
    }

    const files = Array.isArray(manifest.files) ? manifest.files : [];
    for (let i = 0; i < files.length; i++) {
        const item = files[i];
        try {
            syncToast(`Downloading ${i + 1}/${files.length}: ${item.name}`, 'info');

            const existing = await localFileExists(rootHandle, item.relative_path);
            if (existing.exists) {
                // Conservative behavior: never destroy a pre-existing local file.
                // If size matches, treat it as already present; otherwise leave it
                // untouched and report it as a conflict for the user to inspect.
                if (Number(existing.file.size) === Number(item.size || 0)) {
                    result.skipped++;
                    result.synced_files.push({
                        file_id: item.id,
                        relative_path: item.relative_path
                    });
                } else {
                    result.errors.push('Local file already exists with different size: ' + item.relative_path);
                }
                continue;
            }

            const url = window.API_BASE + (item.download_url || ('/files/' + item.id + '/download'));
            const response = await syncFetch(url, {
                method: 'GET',
                credentials: 'include'
            });
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            const blob = await response.blob();
            await writeBlobToLocal(rootHandle, item.relative_path, blob, false);
            result.downloaded++;
            result.synced_files.push({
                file_id: item.id,
                relative_path: item.relative_path
            });
        } catch (e) {
            result.errors.push(item.relative_path + ': ' + e.message);
        }
    }

    return result;
}

async function uploadLocalFile(localFile) {
    if (localFile.file.size > EFACTOR_SYNC_MAX_FILE_SIZE) {
        return {
            success: false,
            error: localFile.relative_path + ' exceeds the current 100 MB sync limit'
        };
    }

    const formData = new FormData();
    formData.append('file', localFile.file, localFile.file.name);
    formData.append('relative_path', localFile.relative_path);

    try {
        const response = await syncFetch(window.API_BASE + '/sync/upload', {
            method: 'POST',
            credentials: 'include',
            headers: { 'Accept': 'application/json' },
            body: formData
        });

        const text = await response.text();
        let data;
        try {
            data = text ? JSON.parse(text) : {};
        } catch (e) {
            throw new Error('Invalid upload response');
        }

        if (!response.ok || data.success === false) {
            throw new Error(data.message || 'Upload failed (HTTP ' + response.status + ')');
        }
        return data;
    } catch (e) {
        return { success: false, error: e.message };
    }
}

async function reportSyncComplete(summary) {
    return syncJson(window.API_BASE + '/sync/complete', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(summary)
    });
}

// ============================================
// LOAD SYNC DATA
// ============================================

function loadSyncData() {
    getSyncStatus();
    loadSyncFolders();
    loadUnsyncedFiles();
}

async function getSyncStatus() {
    try {
        const data = await syncJson(window.API_BASE + '/sync/status', {
            method: 'GET',
            credentials: 'include',
            headers: { 'Accept': 'application/json' }
        });

        if (!data.status) return;
        const status = data.status;

        const total = document.getElementById('syncTotal');
        const synced = document.getElementById('syncSynced');
        const pending = document.getElementById('syncPending');
        const errors = document.getElementById('syncErrors');
        if (total) total.textContent = status.total_files || 0;
        if (synced) synced.textContent = status.files_synced || 0;
        if (pending) pending.textContent = status.queue_stats?.pending || 0;
        if (errors) errors.textContent = status.queue_stats?.failed || 0;

        const folderDisplay = document.getElementById('syncFolderDisplay');
        if (folderDisplay) folderDisplay.textContent = 'Efactor/MyDrive (this computer)';

        const lastSyncEl = document.getElementById('lastSyncTime');
        if (lastSyncEl && status.last_sync) {
            lastSyncEl.textContent = 'Last sync: ' + formatDate(status.last_sync);
        }

        updateSyncStatusIndicator(status.status);
    } catch (error) {
        console.warn('⚠️ Could not load sync status:', error);
    }
}

function updateSyncStatusIndicator(status) {
    const statusMap = {
        idle: { text: 'Ready', color: '#9aa0a6', icon: 'fa-circle' },
        running: { text: 'Syncing...', color: '#fbbc04', icon: 'fa-spinner fa-spin' },
        completed: { text: 'Sync Complete', color: '#34a853', icon: 'fa-check-circle' },
        failed: { text: 'Sync Failed', color: '#ea4335', icon: 'fa-exclamation-circle' }
    };
    const s = statusMap[status] || statusMap.idle;
    const indicatorEl = document.querySelector('.sync-status-indicator');
    if (indicatorEl) {
        indicatorEl.innerHTML = `<i class="fas ${s.icon}" style="color:${s.color};"></i> ${s.text}`;
    }
}

async function loadSyncFolders() {
    const container = document.getElementById('syncFoldersList');
    if (!container) return;

    try {
        const data = await syncJson(window.API_BASE + '/sync/folders', {
            method: 'GET', credentials: 'include'
        });

        if (data.folders && data.folders.length > 0) {
            container.innerHTML = data.folders.map(folder => `
                <div class="sync-folder-item">
                    <div class="sync-folder-info">
                        <i class="fas fa-folder"></i>
                        <div>
                            <div class="sync-folder-name">MyDrive</div>
                            <div class="sync-folder-path">Efactor/MyDrive (this computer)</div>
                            <div class="sync-folder-meta">
                                Last sync: ${folder.last_sync ? formatDate(folder.last_sync) : 'Never'}
                                <span class="sync-status-badge ${folder.sync_enabled ? 'enabled' : 'disabled'}">
                                    ${folder.sync_enabled ? 'Active' : 'Paused'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = `
                <div class="sync-empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>Sync is ready</p>
                    <p class="hint">Click "Init Sync" to connect Efactor/MyDrive on this computer</p>
                </div>`;
        }
    } catch (error) {
        console.warn('⚠️ Could not load sync folders:', error);
    }
}

async function loadUnsyncedFiles() {
    const container = document.getElementById('syncUnsyncedList');
    if (!container) return;

    try {
        const data = await syncJson(window.API_BASE + '/sync/unsynced', {
            method: 'GET', credentials: 'include'
        });
        const files = Array.isArray(data.files) ? data.files : [];
        const countEl = document.getElementById('unsyncedCount');
        if (countEl) countEl.textContent = String(files.length);

        if (files.length === 0) {
            container.innerHTML = `
                <div class="sync-empty-state">
                    <i class="fas fa-check-circle" style="color:#34a853;"></i>
                    <p>All tracked files are synced</p>
                </div>`;
            return;
        }

        container.innerHTML = files.map(file => `
            <div class="sync-unsynced-item">
                <div class="file-icon"><i class="fas fa-file"></i></div>
                <div class="file-info">
                    <div class="file-name">${escapeHtml(file.name || '')}</div>
                    <div class="file-path">${escapeHtml(file.path || '')}</div>
                </div>
                <span class="file-size">${formatFileSize(file.size || 0)}</span>
                <span class="file-status ${escapeHtml(file.status || '')}">${escapeHtml(file.status || '')}</span>
            </div>`).join('');
    } catch (error) {
        console.warn('⚠️ Could not load unsynced files:', error);
    }
}

// ============================================
// INIT SYNC: CLOUD -> USER COMPUTER
// ============================================

async function initSync() {
    const btn = document.querySelector('.sync-add-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Initializing...';
    }

    try {
        const rootHandle = await getMyDriveRoot(true);
        const initData = await syncJson(window.API_BASE + '/sync/init', {
            method: 'POST',
            credentials: 'include',
            headers: { 'Accept': 'application/json' }
        });

        const manifest = initData.manifest || await fetchManifest();
        syncToast('Downloading your Efactor Drive to this computer...', 'info');
        const result = await pullManifestToLocal(rootHandle, manifest);

        await reportSyncComplete({
            folders_created: result.folders_created,
            downloaded: result.downloaded,
            uploaded: 0,
            skipped: result.skipped,
            errors: result.errors,
            synced_files: result.synced_files
        });

        const msg = `Sync initialized: ${result.downloaded} downloaded, ${result.skipped} already present`;
        syncToast(msg, result.errors.length ? 'warning' : 'success');
        if (result.errors.length) console.warn('Init sync errors:', result.errors);

        const folderDisplay = document.getElementById('syncFolderDisplay');
        if (folderDisplay) folderDisplay.textContent = 'Efactor/MyDrive (this computer)';

        loadSyncData();
    } catch (error) {
        console.error('❌ Init sync error:', error);
        syncToast('Init Sync failed: ' + error.message, 'error');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-folder-plus"></i> Init Sync';
        }
    }
}

// ============================================
// SYNC NOW: NEW LOCAL FILES/FOLDERS -> CLOUD
// ============================================

async function runSync() {
    const btn = document.getElementById('syncRunBtn');
    if (!btn) return;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';

    const summary = {
        folders_created: 0,
        uploaded: 0,
        downloaded: 0,
        skipped: 0,
        errors: [],
        synced_files: []
    };

    try {
        const rootHandle = await getMyDriveRoot(false);
        await syncJson(window.API_BASE + '/sync/start', {
            method: 'POST',
            credentials: 'include',
            headers: { 'Accept': 'application/json' }
        });

        syncToast('Scanning Efactor/MyDrive on this computer...', 'info');
        const [manifest, localTree] = await Promise.all([
            fetchManifest(),
            scanLocalTree(rootHandle)
        ]);

        const cloudFolders = new Set([
            ...(manifest.cluster_roots || []).map(normalizeLocalPath),
            ...(manifest.folders || []).map(item => normalizeLocalPath(item.relative_path))
        ]);

        const newFolders = localTree.folders
            .map(normalizeLocalPath)
            .filter(path => path && !cloudFolders.has(path) && !isComputersPath(path));

        if (newFolders.length > 0) {
            const folderResult = await syncJson(window.API_BASE + '/sync/folders/ensure', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ paths: newFolders })
            });
            summary.folders_created = Number(folderResult.created || 0);
            if (Array.isArray(folderResult.errors)) {
                summary.errors.push(...folderResult.errors);
            }
        }

        const cloudFiles = new Set(
            (manifest.files || []).map(item => normalizeLocalPath(item.relative_path))
        );

        const newFiles = localTree.files.filter(item => {
            const path = normalizeLocalPath(item.relative_path);
            return path && !cloudFiles.has(path) && !isComputersPath(path);
        });

        for (let i = 0; i < newFiles.length; i++) {
            const item = newFiles[i];
            syncToast(`Uploading ${i + 1}/${newFiles.length}: ${item.file.name}`, 'info');
            const uploadResult = await uploadLocalFile(item);
            if (uploadResult.success) {
                if (uploadResult.skipped) summary.skipped++;
                else summary.uploaded++;
            } else {
                summary.errors.push(item.relative_path + ': ' + (uploadResult.error || 'Upload failed'));
            }
        }

        // Existing cloud paths are unchanged in phase 1.
        summary.skipped += localTree.files.length - newFiles.length;

        await reportSyncComplete(summary);

        const message = `Sync complete: ${summary.folders_created} folders, ${summary.uploaded} new files uploaded, ${summary.skipped} unchanged`;
        syncToast(message, summary.errors.length ? 'warning' : 'success');
        if (summary.errors.length) console.warn('Sync errors:', summary.errors);

        loadSyncData();
        if (typeof window.loadItems === 'function') {
            window.loadItems();
        } else if (typeof loadItems === 'function') {
            loadItems();
        }
    } catch (error) {
        console.error('❌ Sync error:', error);
        summary.errors.push(error.message);
        try { await reportSyncComplete(summary); } catch (_) {}
        syncToast('Sync failed: ' + error.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-play"></i> Sync Now';
    }
}

// ============================================
// DOWNLOAD NEW CLOUD ITEMS TO THE CONNECTED LOCAL FOLDER
// ============================================

async function downloadFiles() {
    try {
        const rootHandle = await getMyDriveRoot(false);
        const manifest = await fetchManifest();
        const result = await pullManifestToLocal(rootHandle, manifest);
        await reportSyncComplete({
            folders_created: result.folders_created,
            downloaded: result.downloaded,
            uploaded: 0,
            skipped: result.skipped,
            errors: result.errors,
            synced_files: result.synced_files
        });
        syncToast(`Downloaded ${result.downloaded} new cloud file(s)`, result.errors.length ? 'warning' : 'success');
        loadSyncData();
    } catch (error) {
        console.error('❌ Download error:', error);
        syncToast('Download failed: ' + error.message, 'error');
    }
}

function toggleUnsyncedFiles() {
    const list = document.getElementById('syncUnsyncedList');
    const icon = document.getElementById('unsyncedToggleIcon');
    if (list) list.classList.toggle('open');
    if (icon) icon.classList.toggle('open');
}

function formatDate(dateString) {
    if (!dateString) return 'Never';
    return new Date(dateString).toLocaleString();
}

function formatFileSize(bytes) {
    bytes = Number(bytes || 0);
    if (bytes <= 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.min(Math.floor(Math.log(bytes) / Math.log(k)), sizes.length - 1);
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = String(text || '');
    return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(loadSyncData, 1000);
});

window.initSync = initSync;
window.runSync = runSync;
window.downloadFiles = downloadFiles;
window.loadSyncData = loadSyncData;
window.getSyncStatus = getSyncStatus;
window.loadSyncFolders = loadSyncFolders;
window.loadUnsyncedFiles = loadUnsyncedFiles;
window.toggleUnsyncedFiles = toggleUnsyncedFiles;

console.log('✅ Browser sync module loaded');
