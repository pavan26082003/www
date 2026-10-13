// assets/NAS/js/app.js
// Complete File Manager Frontend - CodeIgniter NAS Integration

// config.js or the PHP dashboard view should define window.API_BASE.
// If it was not loaded, derive a safe NAS API path from the current URL.
if (!window.API_BASE) {
    const currentPath = window.location.pathname || '';
    const marker = '/NAS';
    const markerIndex = currentPath.indexOf(marker);
    const nasBase = markerIndex >= 0
        ? currentPath.substring(0, markerIndex) + marker
        : '/NAS';

    window._basePath = window._basePath || nasBase;
    window.NAS_BASE = window.NAS_BASE || window._basePath;
    window._apiBase = window._apiBase || window._basePath.replace(/\/+$/, '') + '/api/v1';
    window.API_BASE = window._apiBase;
}

console.log('📌 app.js - window.API_BASE:', window.API_BASE);

// ============================================
// CONSTANTS & STATE
// ============================================


const API = {
    FILES: window.API_BASE + '/files',
    FOLDERS: window.API_BASE + '/folders',
    SHARE: window.API_BASE + '/share',
    CLUSTERS: window.API_BASE + '/clusters',
    DASHBOARD: window.API_BASE + '/dashboard',
    SYNC: window.API_BASE + '/sync',
    AUTH: window.API_BASE + '/auth',
    USERS: window.API_BASE + '/users',
    PREVIEW: window.API_BASE + '/preview'
};

let state = {
    currentFolderId: null,
    currentViewType: 'my-drive',
    currentItems: [],
    currentView: 'grid',
    currentFilter: 'all',
    searchTerm: '',
    folderPath: [],
    selectedItem: null,
    shareModal: {
        itemId: null,
        itemType: null,
        selectedUser: null
    }
};

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const dashboardRoot =
        document.getElementById('fileGrid') ||
        document.getElementById('mainContent') ||
        document.getElementById('clusterNav');

    if (!dashboardRoot) {
        console.log('ℹ️ NAS app.js: dashboard markup not present; initialization skipped.');
        return;
    }

    console.log('🚀 App initialized');
    console.log('📌 User ID:', window._userId);
    console.log('📌 API Base:', window.API_BASE);
    console.log('📌 Initial View:', window._initialView);

    // Load initial data
    loadClusters();
    loadItems(window._initialView || 'my-drive');
    loadStorageInfo();
    loadSyncData();

    // Setup UI
    setupEventListeners();
    setupNewButton();
    setupSidebarToggle();
    setupSearch();
    setupProfileDropdown();
    updateEmptyTrashButton();

    // Prepare user folder
    const userId = window._userId || null;
    if (userId) {
        prepareUserFolder();
        if (window.CONFIG && window.CONFIG.WS_ENABLED === true) {
            initWebSocket(userId);
        }
    }
});

// ============================================
// API HELPERS
// ============================================
async function apiRequest(url, options = {}) {
    const defaultOptions = {
        credentials: 'include',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    };

    const config = { ...defaultOptions, ...options };

    try {
        const response = await fetch(url, config);
        const text = await response.text();

        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('❌ Failed to parse JSON:', text);
            throw new Error('Invalid server response');
        }
    } catch (error) {
        console.error('❌ API Request failed:', error);
        throw error;
    }
}

// ============================================
// FILE OPERATIONS
// ============================================

/**
 * Load items for current view
 */
function loadItems(view, search) {
    if (view === undefined) view = state.currentViewType;
    if (search === undefined) search = state.searchTerm;

    state.currentViewType = view;
    state.searchTerm = search;

    let url = API.FILES + '?view=' + view;

    // Handle folder navigation
    if (state.currentFolderId) {
        url += '&folder_id=' + state.currentFolderId;
    }

    if (search && search.trim() !== '') {
        url += '&search=' + encodeURIComponent(search.trim());
    }

    console.log('📤 Loading items from:', url);

    const container = document.getElementById('fileGrid');
    if (container) {
        container.innerHTML = `
            <div style="text-align:center;padding:40px;color:#5f6368;">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p>Loading...</p>
            </div>
        `;
    }

    apiRequest(url)
        .then(data => {
            if (data.success) {
                state.currentItems = data.items || [];
                renderItems();
                updateStorage(data.storage);
                updateBreadcrumb();
                updateEmptyTrashButton();
            } else {
                showToast('Failed to load items: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('❌ Error loading items:', error);
            showToast('Error loading items: ' + error.message, 'error');
        });
}

/**
 * Render items in grid/list view
 */
function renderItems() {
    const container = document.getElementById('fileGrid');
    if (!container) return;

    let items = state.currentItems;

    // Apply filter
    if (state.currentFilter === 'folders') {
        items = items.filter(item => item.is_folder);
    } else if (state.currentFilter === 'files') {
        items = items.filter(item => !item.is_folder);
    }

    if (items.length === 0) {
        const emptyMessages = {
            'my-drive': 'This folder is empty',
            'projects': 'No projects found',
            'starred': 'No starred items',
            'trash': 'Trash is empty',
            'shared': 'No items shared with you',
            'images': 'No images found',
            'videos': 'No videos found',
            'documents': 'No documents found',
            'archives': 'No archives found'
        };

        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-${state.currentViewType === 'trash' ? 'trash' : 'folder-open'}"></i>
                <p>${emptyMessages[state.currentViewType] || 'No items found'}</p>
                <p class="hint">${state.currentViewType === 'trash' ? 'Deleted items will appear here' : 'Upload files or create folders to get started'}</p>
            </div>
        `;
        return;
    }

    const gridClass = state.currentView === 'list' ? 'file-grid list-view' : 'file-grid';
    container.className = gridClass;

    let html = '';
    items.forEach(item => {
        if (item.is_folder) {
            html += renderFolderItem(item);
        } else {
            html += renderFileItem(item);
        }
    });

    container.innerHTML = html;
}

function renderFolderItem(folder) {
    const isTrash = state.currentViewType === 'trash' || folder.in_trash;
    const isStarred = folder.is_starred || false;
    const isShared = folder.is_shared || false;
    const canDownload = folder.can_download !== undefined ? folder.can_download : true;

    const canShare = folder.is_owner || (folder.permission && ['share', 'full'].includes(folder.permission));

    let clickHandler;
    if (isTrash) {
        clickHandler = `event.stopPropagation(); showToast('Cannot open items in trash', 'warning');`;
    } else if (isShared) {
        clickHandler = `openSharedFolder(${folder.id})`;
    } else {
        clickHandler = `openFolder(${folder.id})`;
    }

    const cursorStyle = isTrash ? 'cursor:default;opacity:0.8;' : 'cursor:pointer;';

    const depth = folder.depth || 0;
    const indentStyle = depth > 0 ? `padding-left: ${depth * 24 + 16}px;` : '';
    const sharedClass = isShared ? ' shared-item' : '';
    const sharedBadge = isShared ? `<span class="shared-badge"><i class="fas fa-share-alt"></i> Shared</span>` : '';

    // ✅ Only show permission badge for shared items
    const permissionBadge = (isShared && !folder.is_owner && folder.permission) ?
        `<span class="permission-badge ${folder.permission}">${folder.permission}</span>` : '';

    // ✅ Use window.openShareModal to ensure it's available
    const shareButton = (!isTrash && canShare) ? `
        <button onclick="event.stopPropagation();window.openShareModal(${folder.id}, 'folder', '${escapeHtml(folder.name)}')"
                class="action-btn"
                title="Share">
            <i class="fas fa-share-alt"></i>
        </button>` : '';

    const downloadAction = (isTrash || !canDownload) ? '' : `
        <button onclick="event.stopPropagation();downloadFolder(${folder.id})"
                class="action-btn"
                title="Download Folder as ZIP">
            <i class="fas fa-download"></i>
        </button>
    `;

    return `
        <div class="file-item${isTrash ? ' in-trash' : ''}${sharedClass}"
             onclick="${clickHandler}"
             data-id="${folder.id}"
             style="${cursorStyle} ${indentStyle}">
            <div class="actions">
                <button onclick="event.stopPropagation();toggleStar(${folder.id}, 'folder')"
                        class="action-btn"
                        title="${isStarred ? 'Unstar' : 'Star'}"
                        style="color:${isStarred ? '#fbbc04' : '#dadce0'};">
                    <i class="fas fa-star"></i>
                </button>
                ${!isTrash && canDownload ? `
                <button onclick="event.stopPropagation();downloadFolder(${folder.id})"
                        class="action-btn"
                        title="Download ZIP">
                    <i class="fas fa-download"></i>
                </button>` : ''}
                ${shareButton}
                ${isTrash ? `
                <button onclick="event.stopPropagation();restoreItem(${folder.id}, 'folder')"
                        class="action-btn"
                        title="Restore">
                    <i class="fas fa-undo"></i>
                </button>
                <button onclick="event.stopPropagation();permanentDelete(${folder.id}, 'folder')"
                        class="action-btn danger"
                        title="Delete Permanently">
                    <i class="fas fa-trash"></i>
                </button>` : `
                ${!isShared || folder.is_owner ? `
                <button onclick="event.stopPropagation();renameFolder(${folder.id})"
                        class="action-btn"
                        title="Rename">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="event.stopPropagation();deleteFolder(${folder.id})"
                        class="action-btn danger"
                        title="Delete">
                    <i class="fas fa-trash"></i>
                </button>` : ''}
                `}
                <button onclick="event.stopPropagation();openActionMenu(${folder.id}, 'folder')"
                        class="menu-btn"
                        title="More options">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="icon folder">
                <i class="fas fa-folder${isShared ? ' shared' : ''}"></i>
            </div>
            <div class="info">
                <div class="name">
                    ${escapeHtml(folder.name)}
                    ${sharedBadge}
                    ${permissionBadge}
                </div>
                ${isShared && folder.shared_by ? `<span style="font-size:11px;color:#1a73e8;">Shared by ${escapeHtml(folder.shared_by)}</span>` : ''}
                <div class="meta">${isTrash ? 'Deleted ' + formatDate(folder.deleted_at) : formatDate(folder.created_at)}</div>
            </div>
        </div>
    `;
}

function renderFileItem(file) {
    const icon = getFileIcon(file.file_type);
    const color = getFileColor(file.file_type);
    const isTrash = state.currentViewType === 'trash' || file.in_trash;
    const isStarred = file.is_starred || false;
    const isShared = file.is_shared || false;
    const canDownload = file.can_download !== undefined ? file.can_download : true;
    const isComputersView = file.is_computers_view || state.currentViewType === 'computers';

    // ✅ Check if user can share this item
    const canShare = file.is_owner || (file.permission && ['share', 'full'].includes(file.permission));

    let clickHandler = isTrash
        ? `event.stopPropagation(); showToast('Cannot open items in trash', 'warning');`
        : `openFile(${file.id})`;

    const cursorStyle = isTrash ? 'cursor:default;opacity:0.8;' : 'cursor:pointer;';

    const depth = file.depth || 0;
    const indentStyle = depth > 0 ? `padding-left: ${depth * 24 + 16}px;` : '';
    const sharedClass = isShared ? ' shared-item' : '';
    const sharedBadge = isShared ? `<span class="shared-badge"><i class="fas fa-share-alt"></i> Shared</span>` : '';

    // ✅ Only show permission badge for shared items
    const permissionBadge = (isShared && !file.is_owner && file.permission) ?
        `<span class="permission-badge ${file.permission}">${file.permission}</span>` : '';

    // ✅ Cluster badge for Computers view
    const clusterBadge = (isComputersView && file.cluster_name) ?
        `<span class="cluster-badge"><i class="fas fa-folder"></i> ${escapeHtml(file.cluster_name)}</span>` : '';

    // ✅ Use window.openShareModal to ensure it's available
    const shareButton = (!isTrash && canShare) ? `
        <button onclick="event.stopPropagation();window.openShareModal(${file.id}, 'file', '${escapeHtml(file.name)}')"
                class="action-btn"
                title="Share">
            <i class="fas fa-share-alt"></i>
        </button>` : '';

    return `
        <div class="file-item${isTrash ? ' in-trash' : ''}${sharedClass}${isComputersView ? ' computers-view' : ''}"
             onclick="${clickHandler}"
             data-id="${file.id}"
             style="${cursorStyle} ${indentStyle}">
            <div class="actions">
                <button onclick="event.stopPropagation();toggleStar(${file.id}, 'file')"
                        class="action-btn"
                        title="${isStarred ? 'Unstar' : 'Star'}"
                        style="color:${isStarred ? '#fbbc04' : '#dadce0'};">
                    <i class="fas fa-star"></i>
                </button>
                ${!isTrash && canDownload ? `
                <button onclick="event.stopPropagation();downloadFile(${file.id})"
                        class="action-btn"
                        title="Download">
                    <i class="fas fa-download"></i>
                </button>` : ''}
                ${shareButton}
                ${isTrash ? `
                <button onclick="event.stopPropagation();restoreItem(${file.id}, 'file')"
                        class="action-btn"
                        title="Restore">
                    <i class="fas fa-undo"></i>
                </button>
                <button onclick="event.stopPropagation();permanentDelete(${file.id}, 'file')"
                        class="action-btn danger"
                        title="Delete Permanently">
                    <i class="fas fa-trash"></i>
                </button>` : `
                ${!isShared || file.is_owner ? `
                <button onclick="event.stopPropagation();renameFile(${file.id})"
                        class="action-btn"
                        title="Rename">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="event.stopPropagation();deleteFile(${file.id})"
                        class="action-btn danger"
                        title="Delete">
                    <i class="fas fa-trash"></i>
                </button>` : ''}
                `}
                <button onclick="event.stopPropagation();openActionMenu(${file.id}, 'file')"
                        class="menu-btn"
                        title="More options">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
            <div class="icon" style="color:${color}">
                <i class="${icon}"></i>
            </div>
            <div class="info">
                <div class="name">
                    ${escapeHtml(file.name)}
                    ${sharedBadge}
                    ${permissionBadge}
                    ${clusterBadge}
                </div>
                ${isShared && file.shared_by ? `<span style="font-size:11px;color:#1a73e8;">Shared by ${escapeHtml(file.shared_by)}</span>` : ''}
                <div class="meta">
                    ${file.file_size_formatted || ''}
                    ${isTrash ? '• Deleted ' + formatDate(file.deleted_at) : '• ' + formatDate(file.created_at)}
                    ${isComputersView && file.cluster_name ? `• 📁 ${escapeHtml(file.cluster_name)}` : ''}
                </div>
            </div>
        </div>
    `;
}

function renameFile(fileId) {
    const item = state.currentItems.find(i => !i.is_folder && i.id === fileId);
    if (!item) return;

    const newName = prompt('Enter new file name:', item.name);
    if (!newName || newName === item.name) return;

    // ✅ FIX: Use correct endpoint with ID in URL
    fetch(window.API_BASE + '/files/' + fileId, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            file_id: fileId,
            action: 'rename',
            new_name: newName
        }),
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'File renamed successfully!', 'success');
            loadItems(state.currentViewType);
        } else {
            showToast('Failed to rename file: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('❌ Rename file error:', error);
        showToast('Error renaming file: ' + error.message, 'error');
    });
}



// ============================================
// FOLDER OPERATIONS
// ============================================

function renameFolder(folderId) {
    const item = state.currentItems.find(i => i.is_folder && i.id === folderId);
    if (!item) return;

    const newName = prompt('Enter new folder name:', item.name);
    if (!newName || newName === item.name) return;

    // ✅ FIX: Use correct endpoint with ID in URL
    fetch(window.API_BASE + '/folders/' + folderId, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            folder_id: folderId,
            action: 'rename',
            new_name: newName
        }),
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Folder renamed successfully!', 'success');
            loadItems(state.currentViewType);
        } else {
            showToast('Failed to rename folder: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('❌ Rename folder error:', error);
        showToast('Error renaming folder: ' + error.message, 'error');
    });
}

function openFolder(folderId) {
    console.log('📂 Opening folder:', folderId);
    console.log('📌 Current view type:', state.currentViewType);

    // Preserve the tab/view from which the folder was opened.
    const currentView = state.currentViewType || 'my-drive';

    state.currentFolderId = folderId;
    state.currentViewType = currentView;
    state.currentCluster = currentView;
    state.searchTerm = '';

    const searchInput = document.getElementById('searchInput');

    if (searchInput) {
        searchInput.value = '';
    }

    // Example:
    // Projects -> ?view=projects&folder_id=123
    // Starred  -> ?view=starred&folder_id=123
    loadItems(currentView);

    // Remove previous active selection.
    document
        .querySelectorAll('.sidebar-nav li, #clusterNav li')
        .forEach(function(item) {
            item.classList.remove('active');
        });

    // Keep current tab active.
    const activeItem = document.querySelector(
        '[data-view="' + currentView + '"]'
    );

    if (activeItem) {
        activeItem.classList.add('active');
    }

    const titleMap = {
        'my-drive': 'My Drive',
        'shared': 'Shared with Me',
        'projects': 'Projects',
        'images': 'Images',
        'videos': 'Videos',
        'documents': 'Documents',
        'archives': 'Archives',
        'starred': 'Starred',
        'trash': 'Trash',
        'computers': 'Computers'
    };

    const pageTitle = document.getElementById('pageTitle');

    if (pageTitle) {
        pageTitle.textContent =
            titleMap[currentView] || currentView;
    }

    const breadcrumb = document.getElementById('breadcrumb');

    if (breadcrumb) {
        if (
            currentView === 'starred' ||
            currentView === 'trash'
        ) {
            breadcrumb.style.display = 'none';
        } else {
            breadcrumb.style.display = 'flex';
        }
    }

    updateBreadcrumb();
}


function navigateToRoot() {
    console.log('🏠 Navigating to root of current view');

    const currentView =
        state.currentViewType || 'my-drive';

    state.currentFolderId = null;
    state.currentPath = [];
    state.folderPath = [];
    state.searchTerm = '';

    state.currentViewType = currentView;
    state.currentCluster = currentView;

    const searchInput =
        document.getElementById('searchInput');

    if (searchInput) {
        searchInput.value = '';
    }

    // Return to root of SAME tab.
    loadItems(currentView);

    document
        .querySelectorAll('.sidebar-nav li, #clusterNav li')
        .forEach(function(item) {
            item.classList.remove('active');
        });

    const activeItem = document.querySelector(
        '[data-view="' + currentView + '"]'
    );

    if (activeItem) {
        activeItem.classList.add('active');
    }

    const titleMap = {
        'my-drive': 'My Drive',
        'shared': 'Shared with Me',
        'projects': 'Projects',
        'images': 'Images',
        'videos': 'Videos',
        'documents': 'Documents',
        'archives': 'Archives',
        'starred': 'Starred',
        'trash': 'Trash',
        'computers': 'Computers'
    };

    const pageTitle =
        document.getElementById('pageTitle');

    if (pageTitle) {
        pageTitle.textContent =
            titleMap[currentView] || currentView;
    }

    updateBreadcrumb();
}

/**
 * Navigate to shared root
 */
function navigateToSharedRoot() {
    console.log('📂 Navigating to shared root');
    state.currentFolderId = null;
    state.currentViewType = 'shared';
    state.searchTerm = '';

    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.value = '';
    loadItems('shared');
    updateBreadcrumb();

    // Update UI
    document.querySelectorAll('.sidebar-nav li').forEach(i => i.classList.remove('active'));
    const sharedLi = document.querySelector('[data-view="shared"]');
    if (sharedLi) sharedLi.classList.add('active');
    document.getElementById('pageTitle').textContent = 'Shared with Me';
    document.getElementById('breadcrumb').style.display = 'flex';
}

function showCreateFolderModal() {
    if (state.currentViewType === 'trash') {
        showToast('Cannot create folder in trash', 'error');
        return;
    }

    const modal = document.getElementById('createFolderModal');
    if (!modal) return;

    const input = document.getElementById('folderNameInput');
    input.value = '';
    input.focus();
    modal.classList.add('active');

    const confirmBtn = modal.querySelector('.confirm');
    confirmBtn.onclick = function() {
        const name = input.value.trim();
        if (!name) {
            showToast('Please enter a folder name', 'error');
            return;
        }
        createFolder(name);
        modal.classList.remove('active');
    };

    input.onkeydown = function(e) {
        if (e.key === 'Enter') {
            confirmBtn.click();
        }
    };
}

function createFolder(name) {
    const activeCluster = document.querySelector('#clusterNav li.active');
    let clusterSlug = activeCluster?.dataset?.view || 'my-drive';

    apiRequest(API.FOLDERS, {
        method: 'POST',
        body: JSON.stringify({
            name: name,
            parent_id: state.currentFolderId,
            cluster: clusterSlug
        })
    })
    .then(data => {
        if (data.success) {
            showToast('Folder created successfully!', 'success');
            loadItems(state.currentViewType);
        } else {
            showToast('Failed to create folder: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('❌ Create folder error:', error);
        showToast('Error creating folder: ' + error.message, 'error');
    });
}




// ============================================
// FILE OPERATIONS
// ============================================

function openFile(fileId) {
    showToast('Loading file...', 'info');

    apiRequest(API.FILES + '/' + fileId + '/info')
        .then(data => {
            if (data.success && data.file) {
                showPreviewModal(data.file);
            } else {
                showToast('Failed to load file: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('❌ Error opening file:', error);
            showToast('Error opening file: ' + error.message, 'error');
        });
}



function deleteFile(fileId) {
    // ✅ Check if this is a shared item
    const item = state.currentItems.find(i => i.id === fileId && !i.is_folder);
    if (item && item.is_shared && !item.is_owner) {
        // If it's a shared item and not owned, remove share instead
        removeShare(fileId, 'file');
        return;
    }

    if (!confirm('Move this file to trash?')) return;

    fetch(window.API_BASE + '/files/' + fileId, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ file_id: fileId }),
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'File moved to trash', 'success');
            loadItems(state.currentViewType);
        } else {
            showToast('Failed to delete file: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('❌ Delete file error:', error);
        showToast('Error deleting file: ' + error.message, 'error');
    });
}

function deleteFolder(folderId) {
    // ✅ Check if this is a shared item
    const item = state.currentItems.find(i => i.id === folderId && i.is_folder);
    if (item && item.is_shared && !item.is_owner) {
        // If it's a shared folder and not owned, remove share instead
        removeShare(folderId, 'folder');
        return;
    }

    if (!confirm('Move this folder to trash?')) return;

    fetch(window.API_BASE + '/folders/' + folderId, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ folder_id: folderId }),
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Folder moved to trash', 'success');
            loadItems(state.currentViewType);
        } else {
            showToast('Failed to delete folder: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('❌ Delete folder error:', error);
        showToast('Error deleting folder: ' + error.message, 'error');
    });
}

function restoreItem(itemId, type) {
    console.log('🔄 Restoring item:', type, 'ID:', itemId);

    // ✅ Use the correct endpoint based on type
    const endpoint = type === 'folder'
        ? window.API_BASE + '/folders/' + itemId + '/restore'
        : window.API_BASE + '/files/' + itemId + '/restore';

    console.log('📤 Sending restore request to:', endpoint);

    showToast('Restoring item...', 'info');

    fetch(endpoint, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        credentials: 'include'
    })
    .then(async response => {
        const text = await response.text();
        console.log('📥 Raw response:', text);

        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('❌ Failed to parse JSON:', text);
            throw new Error('Server returned invalid response: ' + text.substring(0, 100));
        }
    })
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Item restored successfully!', 'success');
            loadItems(state.currentViewType);
        } else {
            showToast('Failed to restore: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('❌ Restore error:', error);
        showToast('Error restoring item: ' + error.message, 'error');
    });
}

function permanentDelete(itemId, type) {
    if (!confirm('⚠️ Permanently delete this item? This action cannot be undone!')) {
        return;
    }

    const endpoint = type === 'folder'
        ? window.API_BASE + '/folders/' + itemId + '?permanent=true'
        : window.API_BASE + '/files/' + itemId + '?permanent=true';

    fetch(endpoint, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json'
        },
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Item permanently deleted', 'success');
            loadItems(state.currentViewType);
        } else {
            showToast('Failed to delete permanently: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('❌ Permanent delete error:', error);
        showToast('Error deleting item: ' + error.message, 'error');
    });
}

function downloadFile(fileId) {
    console.log('📥 Downloading file:', fileId);

    // Find the item in currentItems
    const item = state.currentItems.find(i => i.id === fileId);
    if (!item) {
        showToast('File not found', 'error');
        return;
    }

    // Check if it's a folder or file
    if (item.is_folder) {
        // Download folder as ZIP
        window.location.href = window.API_BASE + '/files/download-folder?folder_id=' + fileId;
    } else {
        // Download single file
        window.location.href = window.API_BASE + '/files/' + fileId + '/download';
    }
}

function downloadFolder(folderId) {
    console.log('📁 Downloading folder as ZIP:', folderId);
    window.location.href = window.API_BASE + '/files/download-folder?folder_id=' + folderId;
}

function toggleStar(itemId, type) {
    console.log('⭐ Toggling star for:', type, 'ID:', itemId);

    const item = state.currentItems.find(i => i.id === itemId && i.is_folder === (type === 'folder'));
    if (!item) {
        showToast('Item not found', 'error');
        return;
    }

    // Optimistically update UI
    const newStarState = !item.is_starred;
    item.is_starred = newStarState;
    renderItems();

    // ✅ FIX: Use the correct endpoint with ID in URL
    const endpoint = type === 'folder'
        ? window.API_BASE + '/folders/' + itemId
        : window.API_BASE + '/files/' + itemId;

    const payload = type === 'folder'
        ? { folder_id: itemId, action: 'star' }
        : { file_id: itemId, action: 'star' };

    console.log('📤 Sending star request to:', endpoint);
    console.log('📦 Payload:', payload);

    fetch(endpoint, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload),
        credentials: 'include'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('📦 Response data:', data);
        if (data.success) {
            // Update the item in currentItems with server response
            const updatedItem = state.currentItems.find(i => i.id === itemId && i.is_folder === (type === 'folder'));
            if (updatedItem) {
                updatedItem.is_starred = data.is_starred;
            }
            showToast(data.message || (newStarState ? 'Starred!' : 'Unstarred!'), 'success');
            // Reload current view to reflect changes
            loadItems(state.currentViewType);
        } else {
            // Revert on error
            if (item) {
                item.is_starred = !newStarState;
                renderItems();
            }
            showToast('Failed to update star: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('❌ Star error:', error);
        // Revert on error
        if (item) {
            item.is_starred = !newStarState;
            renderItems();
        }
        showToast('Error updating star: ' + error.message, 'error');
    });
}

// ============================================
// UPLOAD OPERATIONS
// ============================================

function uploadFiles(files) {

    if (!files || files.length === 0) {

        showToast(
            'No files selected',
            'error'
        );

        return;
    }


    const formData =
        new FormData();

    let validFiles = 0;
    let isFolderUpload = false;
    let rootFolder = '';


    /*
    |--------------------------------------------------------------------------
    | Check folder upload
    |--------------------------------------------------------------------------
    */

    if (files[0].webkitRelativePath) {

        isFolderUpload = true;

        rootFolder =
            files[0]
                .webkitRelativePath
                .split('/')[0];
    }


    /*
    |--------------------------------------------------------------------------
    | Process files
    |--------------------------------------------------------------------------
    */

    for (
        let i = 0;
        i < files.length;
        i++
    ) {

        const file =
            files[i];

        const fileName =
            file.name.trim();


        /*
        |--------------------------------------------------------------------------
        | 100 MB frontend limit
        |--------------------------------------------------------------------------
        */

        if (
            file.size >
            100 * 1024 * 1024
        ) {

            showToast(
                'File "' +
                fileName +
                '" exceeds 100MB limit',
                'error'
            );

            continue;
        }


        let relativePath = '';


        if (file.webkitRelativePath) {

            const pathParts =
                file.webkitRelativePath
                    .split('/');

            pathParts.shift();

            relativePath =
                pathParts
                    .slice(0, -1)
                    .join('/');
        }


        formData.append(
            'files[]',
            file
        );

        formData.append(
            'paths[]',
            relativePath
        );

        validFiles++;
    }


    if (validFiles === 0) {

        showToast(
            'No valid files to upload',
            'error'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Folder upload
    |--------------------------------------------------------------------------
    */

    if (
        isFolderUpload &&
        rootFolder
    ) {

        formData.append(
            'root_folder',
            rootFolder
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Current folder
    |--------------------------------------------------------------------------
    */

    formData.append(
        'folder_id',
        state.currentFolderId || ''
    );


    /*
    |--------------------------------------------------------------------------
    | Current cluster
    |--------------------------------------------------------------------------
    */

    const activeCluster =
        document.querySelector(
            '#clusterNav li.active'
        );

    if (activeCluster) {

        const view =
            activeCluster.dataset.view;

        if (
            view &&
            view !== 'home' &&
            view !== 'my-drive'
        ) {

            formData.append(
                'cluster',
                view
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CodeIgniter CSRF
    |--------------------------------------------------------------------------
    */

    if (
        window.NAS_CSRF &&
        window.NAS_CSRF.tokenName &&
        window.NAS_CSRF.hash
    ) {

        formData.append(
            window.NAS_CSRF.tokenName,
            window.NAS_CSRF.hash
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    */

    showToast(
        '📤 Uploading ' +
        validFiles +
        ' file(s)...',
        'info'
    );


    fetch(
        API.FILES,
        {
            method: 'POST',

            body: formData,

            credentials: 'include',

            headers: {
                'Accept':
                    'application/json'
            }
        }
    )
    .then(
        async response => {

            const rawResponse =
                await response.text();


            console.log(
                '📤 Upload HTTP status:',
                response.status
            );

            console.log(
                '📤 Upload final URL:',
                response.url
            );

            console.log(
                '📤 Upload redirected:',
                response.redirected
            );

            console.log(
                '📤 Upload raw response:',
                rawResponse
            );


            if (response.redirected) {

                throw new Error(
                    'Upload request was redirected to: '
                    + response.url
                );
            }


            let data;


            try {

                data =
                    JSON.parse(
                        rawResponse
                    );

            } catch (parseError) {

                console.error(
                    '❌ Upload returned non-JSON:',
                    rawResponse
                );

                throw new Error(
                    'Upload API returned an invalid response.'
                );
            }


            return data;
        }
    )
    .then(
        data => {

            if (data.success) {

                let msg =
                    data.message
                    || 'Upload completed!';


                if (
                    data.errors &&
                    data.errors.length > 0
                ) {

                    msg +=
                        '\n\n⚠️ '
                        + data.errors.join('\n');
                }


                showToast(
                    msg,
                    'success'
                );


                loadItems(
                    state.currentViewType
                );

            } else {

                showToast(
                    '❌ Upload failed: '
                    + (
                        data.message
                        || 'Unknown error'
                    ),
                    'error'
                );
            }
        }
    )
    .catch(
        error => {

            console.error(
                '❌ Upload error:',
                error
            );


            showToast(
                '❌ Error uploading files: '
                + error.message,
                'error'
            );
        }
    );
}

// ============================================
// STORAGE OPERATIONS
// ============================================

function loadStorageInfo() {
    apiRequest(API.FILES + '?view=my-drive')
        .then(data => {
            if (data.success && data.storage) {
                updateStorage(data.storage);
            }
        })
        .catch(error => console.error('Error loading storage:', error));
}

function updateStorage(storage) {
    const fill = document.getElementById('storageFill');
    const usedText = document.getElementById('storageUsed');
    const limitText = document.getElementById('storageLimit');

    if (fill) {
        const percentage = Math.min(storage.percentage, 100);
        fill.style.width = percentage + '%';
        if (percentage > 90) {
            fill.style.background = '#ea4335';
        } else if (percentage > 70) {
            fill.style.background = '#fbbc04';
        } else {
            fill.style.background = '#34a853';
        }
    }
    if (usedText) usedText.textContent = storage.used_formatted;
    if (limitText) limitText.textContent = storage.limit_formatted;
}

// ============================================
// CLUSTER OPERATIONS
// ============================================

function loadClusters() {
    const nav = document.getElementById('clusterNav');
    if (!nav) return;

    nav.innerHTML = `
        <li class="loading-clusters">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Loading clusters...</span>
        </li>
    `;

    apiRequest(API.CLUSTERS)
        .then(data => {
            if (data.success && data.clusters) {
                renderClusters(data.clusters);
            } else {
                renderDefaultClusters();
            }
        })
        .catch(error => {
            console.error('Error loading clusters:', error);
            renderDefaultClusters();
        });
}

function renderClusters(clusters) {
    const nav = document.getElementById('clusterNav');
    if (!nav) return;

    const iconMap = {
        'my-drive': 'fa-cloud',
        'shared': 'fa-share-alt',
        'projects': 'fa-folder-open',
        'images': 'fa-image',
        'videos': 'fa-video',
        'documents': 'fa-file-alt',
        'archives': 'fa-file-archive',
        'starred': 'fa-star',
        'trash': 'fa-trash'
    };

    let html = '';
    let firstActive = true;
    const seenSlugs = new Set();

    clusters.forEach(cluster => {
        if (seenSlugs.has(cluster.slug)) return;
        seenSlugs.add(cluster.slug);

        const icon = iconMap[cluster.slug] || 'fa-folder';
        const isActive = (cluster.slug === 'my-drive' && firstActive) ? 'active' : '';
        if (cluster.slug === 'my-drive') firstActive = false;

        html += `
            <li class="${isActive}" data-view="${cluster.slug}" data-cluster-id="${cluster.id}">
                <i class="fas ${icon}"></i>
                <span>${escapeHtml(cluster.name)}</span>
                ${cluster.file_count > 0 ? `<span class="badge">${cluster.file_count}</span>` : ''}
            </li>
        `;
    });

    nav.innerHTML = html;

    // Add click handlers
    // In renderClusters() or the click handler
document.querySelectorAll('#clusterNav li').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('#clusterNav li').forEach(i => i.classList.remove('active'));
        this.classList.add('active');

        const view = this.dataset.view || 'my-drive';

        // ✅ Always preserve the view when clicking a cluster
        state.currentViewType = view;
        state.currentFolderId = null;
        state.currentCluster = view;
        state.searchTerm = '';

        document.getElementById('searchInput').value = '';

        // Update page title
        const titleMap = {
            'my-drive': 'My Drive',
            'shared': 'Shared with Me',
            'projects': 'Projects',
            'images': 'Images',
            'videos': 'Videos',
            'documents': 'Documents',
            'archives': 'Archives',
            'starred': 'Starred',
            'trash': 'Trash',
            'computers': 'Computers'
        };
        document.getElementById('pageTitle').textContent = titleMap[view] || view;

        // Show/hide breadcrumb
        const breadcrumb = document.getElementById('breadcrumb');
        const specialViews = ['starred', 'trash'];
        if (breadcrumb) {
            breadcrumb.style.display = specialViews.indexOf(view) !== -1 ? 'none' : 'flex';
        }

        // Show/hide buttons
        const newFolderBtn = document.getElementById('newFolderBtn');
        const uploadBtn = document.getElementById('uploadBtn');
        if (view === 'trash') {
            if (newFolderBtn) newFolderBtn.style.display = 'none';
            if (uploadBtn) uploadBtn.style.display = 'none';
        } else {
            if (newFolderBtn) newFolderBtn.style.display = 'inline-flex';
            if (uploadBtn) uploadBtn.style.display = 'inline-flex';
        }

        loadItems(view);
        setTimeout(updateEmptyTrashButton, 50);
    });
});
}

function renderDefaultClusters() {
    const nav = document.getElementById('clusterNav');
    if (!nav) return;

    const defaultClusters = [
        { slug: 'my-drive', name: 'My Drive', icon: 'fa-cloud' },
        { slug: 'shared', name: 'Shared with Me', icon: 'fa-share-alt' },
        { slug: 'projects', name: 'Projects', icon: 'fa-folder-open' },
        { slug: 'images', name: 'Images', icon: 'fa-image' },
        { slug: 'videos', name: 'Videos', icon: 'fa-video' },
        { slug: 'documents', name: 'Documents', icon: 'fa-file-alt' },
        { slug: 'archives', name: 'Archives', icon: 'fa-file-archive' },
        { slug: 'starred', name: 'Starred', icon: 'fa-star' },
        { slug: 'trash', name: 'Trash', icon: 'fa-trash' }
    ];

    let html = '';
    defaultClusters.forEach((cluster, index) => {
        const isActive = index === 0 ? 'active' : '';
        html += `
            <li class="${isActive}" data-view="${cluster.slug}">
                <i class="fas ${cluster.icon}"></i>
                <span>${cluster.name}</span>
            </li>
        `;
    });

    nav.innerHTML = html;
}


// ============================================
// SYNC OPERATIONS
// ============================================

function loadSyncData() {
    getSyncStatus();
    loadSyncFolders();
    loadUnsyncedFiles();
}

function getSyncStatus() {
    apiRequest(API.SYNC + '/status')
        .then(data => {
            if (data.success && data.status) {
                const status = data.status;
                document.getElementById('syncTotal').textContent = status.total_files || 0;
                document.getElementById('syncSynced').textContent = status.files_synced || 0;
                document.getElementById('syncPending').textContent = status.queue_stats?.pending || 0;
                document.getElementById('syncErrors').textContent = status.queue_stats?.failed || 0;
            }
        })
        .catch(error => console.warn('Could not load sync status:', error));
}

function loadSyncFolders() {
    // This will be populated from the API
    const container = document.getElementById('syncFoldersList');
    if (!container) return;

    container.innerHTML = `
        <div class="sync-empty-state">
            <i class="fas fa-folder-open"></i>
            <p>Sync is ready</p>
            <p class="hint">Click "Sync Now" to synchronize your files</p>
        </div>
    `;
}

function loadUnsyncedFiles() {
    const container = document.getElementById('syncUnsyncedList');
    if (!container) return;

    container.innerHTML = `
        <div class="sync-empty-state">
            <i class="fas fa-check-circle" style="color:#34a853;"></i>
            <p>All files are synced! 🎉</p>
        </div>
    `;
    document.getElementById('unsyncedCount').textContent = '0';
}

function runSync() {
    const btn = document.getElementById('syncRunBtn');
    if (!btn) return;

    showToast('Starting synchronization...', 'info');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';

    apiRequest(API.SYNC + '/start', { method: 'POST' })
        .then(data => {
            if (data.success) {
                showToast('Sync completed successfully!', 'success');
                getSyncStatus();
            } else {
                showToast('Sync failed: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Error during sync: ' + error.message, 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-play"></i> Sync Now';
        });
}

function syncAllFiles() {
    showToast('📁 Downloading all files to local folder...', 'info');

    apiRequest(API.SYNC + '/start', { method: 'POST' })
        .then(data => {
            if (data.success) {
                showToast('✅ Files synced successfully!', 'success');
                getSyncStatus();
            } else {
                showToast('❌ Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showToast('❌ Error downloading files: ' + error.message, 'error');
        });
}

function prepareUserFolder() {
    // This is handled by the sync system
    console.log('📁 Preparing user folder...');
}

function toggleUnsyncedFiles() {
    const list = document.getElementById('syncUnsyncedList');
    const icon = document.getElementById('unsyncedToggleIcon');

    if (list) list.classList.toggle('open');
    if (icon) icon.classList.toggle('open');
}

// ============================================
// UI HELPERS
// ============================================

function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    if (!toast) return;

    const icon = toast.querySelector('.toast-icon');
    const text = toast.querySelector('.toast-text');

    toast.className = 'toast';
    toast.style.borderLeft = type === 'success' ? '4px solid #34a853' :
                           type === 'error' ? '4px solid #ea4335' :
                           '4px solid #1a73e8';

    icon.className = type === 'success' ? 'fas fa-check-circle toast-icon' :
                    type === 'error' ? 'fas fa-exclamation-circle toast-icon' :
                    'fas fa-info-circle toast-icon';

    text.textContent = message;
    toast.classList.add('show');

    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(() => toast.classList.remove('show'), 3000);
}

function formatDate(dateString) {
    if (!dateString) return 'Unknown';
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    if (diff < 60000) return 'Just now';
    if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
    if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
    if (diff < 604800000) return Math.floor(diff / 86400000) + 'd ago';
    if (diff < 2592000000) return Math.floor(diff / 604800000) + 'w ago';
    return date.toLocaleDateString();
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getFileIcon(fileType) {
    if (!fileType) return 'fas fa-file';
    const icons = {
        'image': 'fas fa-image',
        'pdf': 'fas fa-file-pdf',
        'word': 'fas fa-file-word',
        'document': 'fas fa-file-word',
        'excel': 'fas fa-file-excel',
        'spreadsheet': 'fas fa-file-excel',
        'powerpoint': 'fas fa-file-powerpoint',
        'presentation': 'fas fa-file-powerpoint',
        'zip': 'fas fa-file-archive',
        'rar': 'fas fa-file-archive',
        'video': 'fas fa-file-video',
        'audio': 'fas fa-file-audio',
        'text': 'fas fa-file-alt',
        'code': 'fas fa-code'
    };
    for (const key in icons) {
        if (fileType.indexOf(key) !== -1) return icons[key];
    }
    return 'fas fa-file';
}

function getFileColor(fileType) {
    if (!fileType) return '#5f6368';
    const colors = {
        'image': '#e83e8c',
        'pdf': '#dc3545',
        'word': '#007bff',
        'excel': '#28a745',
        'powerpoint': '#fd7e14',
        'zip': '#6c757d',
        'video': '#20c997',
        'audio': '#17a2b8',
        'text': '#5f6368',
        'code': '#6610f2'
    };
    for (const key in colors) {
        if (fileType.indexOf(key) !== -1) return colors[key];
    }
    return '#5f6368';
}

/**
 * Open a shared folder
 */
function openSharedFolder(folderId) {
    console.log('📂 Opening shared folder:', folderId);

    // Set current folder and keep shared view
    state.currentFolderId = folderId;
    state.currentViewType = 'shared';
    state.searchTerm = '';

    document.getElementById('searchInput').value = '';
    loadItems('shared');
    updateBreadcrumb();

    // Update UI
    document.querySelectorAll('.sidebar-nav li').forEach(i => i.classList.remove('active'));
    const sharedLi = document.querySelector('[data-view="shared"]');
    if (sharedLi) sharedLi.classList.add('active');
    document.getElementById('pageTitle').textContent = 'Shared with Me';
    document.getElementById('breadcrumb').style.display = 'flex';
}

/**
 * Get folder path recursively (for breadcrumb)
 */
function getFolderPath(folderId, callback) {
    console.log('🔍 Getting folder path for:', folderId);

    fetch(window.API_BASE + '/folders/' + folderId + '/info', {
        credentials: 'include'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('📄 Folder info:', data);

        if (data.success && data.folder) {
            const folder = data.folder;

            // If folder has a parent, recursively get its path
            if (folder.parent_id) {
                getFolderPath(folder.parent_id, function(parentPath) {
                    callback([...parentPath, { id: folder.id, name: folder.name }]);
                });
            } else {
                callback([{ id: folder.id, name: folder.name }]);
            }
        } else {
            callback([]);
        }
    })
    .catch(error => {
        console.error('❌ Error getting folder path:', error);
        callback([]);
    });
}

function updateBreadcrumb() {
    const container =
        document.getElementById('breadcrumb');

    if (!container) {
        return;
    }

    const currentView =
        state.currentViewType || 'my-drive';

    console.log(
        '🔄 Updating breadcrumb, currentFolderId:',
        state.currentFolderId,
        'view:',
        currentView
    );

    // Starred and Trash don't show breadcrumbs.
    if (
        currentView === 'starred' ||
        currentView === 'trash'
    ) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'flex';

    const titleMap = {
        'my-drive': 'My Drive',
        'shared': 'Shared with Me',
        'projects': 'Projects',
        'images': 'Images',
        'videos': 'Videos',
        'documents': 'Documents',
        'archives': 'Archives',
        'computers': 'Computers'
    };

    const rootTitle =
        titleMap[currentView] || currentView;

    // Shared root.
    if (
        currentView === 'shared' &&
        state.currentFolderId === null
    ) {
        container.innerHTML =
            '<span onclick="navigateToSharedRoot()" ' +
            'style="cursor:pointer;color:#1a73e8;font-weight:500;">' +
            'Shared with Me</span>';

        return;
    }

    // Root of My Drive / Projects / Images / etc.
    if (state.currentFolderId === null) {
        container.innerHTML =
            '<span onclick="navigateToRoot()" ' +
            'style="cursor:pointer;color:#1a73e8;font-weight:500;">' +
            escapeHtml(rootTitle) +
            '</span>';

        return;
    }

    // Shared folder breadcrumb.
    if (currentView === 'shared') {

        fetch(
            window.API_BASE +
            '/folders/' +
            state.currentFolderId +
            '/info',
            {
                credentials: 'include'
            }
        )
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {

            if (!data.success || !data.folder) {
                container.innerHTML =
                    '<span onclick="navigateToSharedRoot()" ' +
                    'style="cursor:pointer;color:#1a73e8;">' +
                    'Shared with Me</span>';

                return;
            }

            const folder = data.folder;

            let html =
                '<span onclick="navigateToSharedRoot()" ' +
                'style="cursor:pointer;color:#1a73e8;">' +
                'Shared with Me</span>';

            if (folder.parent_id) {

                getFolderPath(
                    folder.parent_id,
                    function(path) {

                        path.forEach(function(parentFolder) {

                            html +=
                                '<span class="separator">/</span>';

                            html +=
                                '<span onclick="openSharedFolder(' +
                                parentFolder.id +
                                ')" ' +
                                'style="cursor:pointer;color:#1a73e8;">' +
                                escapeHtml(parentFolder.name) +
                                '</span>';
                        });

                        html +=
                            '<span class="separator">/</span>';

                        html +=
                            '<span style="font-weight:500;color:#202124;">' +
                            escapeHtml(folder.name) +
                            '</span>';

                        container.innerHTML = html;
                    }
                );

            } else {

                html +=
                    '<span class="separator">/</span>';

                html +=
                    '<span style="font-weight:500;color:#202124;">' +
                    escapeHtml(folder.name) +
                    '</span>';

                container.innerHTML = html;
            }
        })
        .catch(function(error) {

            console.error(
                '❌ Shared breadcrumb error:',
                error
            );

            container.innerHTML =
                '<span onclick="navigateToSharedRoot()" ' +
                'style="cursor:pointer;color:#1a73e8;">' +
                'Shared with Me</span>';
        });

        return;
    }

    // Normal folders:
    // Projects / Folder
    // Images / Folder
    // Documents / Folder
    // etc.
    fetch(
        window.API_BASE +
        '/folders/' +
        state.currentFolderId +
        '/info',
        {
            credentials: 'include'
        }
    )
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {

        let html =
            '<span onclick="navigateToRoot()" ' +
            'style="cursor:pointer;color:#1a73e8;font-weight:500;">' +
            escapeHtml(rootTitle) +
            '</span>';

        if (!data.success || !data.folder) {
            container.innerHTML = html;
            return;
        }

        const folder = data.folder;

        if (folder.parent_id) {

            getFolderPath(
                folder.parent_id,
                function(path) {

                    path.forEach(function(parentFolder) {

                        html +=
                            '<span class="separator">/</span>';

                        html +=
                            '<span onclick="openFolder(' +
                            parentFolder.id +
                            ')" ' +
                            'style="cursor:pointer;color:#1a73e8;">' +
                            escapeHtml(parentFolder.name) +
                            '</span>';
                    });

                    html +=
                        '<span class="separator">/</span>';

                    html +=
                        '<span style="font-weight:500;color:#202124;">' +
                        escapeHtml(folder.name) +
                        '</span>';

                    container.innerHTML = html;
                }
            );

        } else {

            html +=
                '<span class="separator">/</span>';

            html +=
                '<span style="font-weight:500;color:#202124;">' +
                escapeHtml(folder.name) +
                '</span>';

            container.innerHTML = html;
        }
    })
    .catch(function(error) {

        console.error(
            '❌ Error fetching folder:',
            error
        );

        container.innerHTML =
            '<span onclick="navigateToRoot()" ' +
            'style="cursor:pointer;color:#1a73e8;">' +
            escapeHtml(rootTitle) +
            '</span>';
    });
}

function updateEmptyTrashButton() {
    const emptyTrashBtn = document.getElementById('emptyTrashBtn');
    const newFolderBtn = document.getElementById('newFolderBtn');
    const uploadBtn = document.getElementById('uploadBtn');

    if (!emptyTrashBtn) return;

    if (state.currentViewType === 'trash') {
        emptyTrashBtn.style.display = 'inline-flex';
        if (newFolderBtn) newFolderBtn.style.display = 'none';
        if (uploadBtn) uploadBtn.style.display = 'none';
    } else {
        emptyTrashBtn.style.display = 'none';
        if (newFolderBtn) newFolderBtn.style.display = 'inline-flex';
        if (uploadBtn) uploadBtn.style.display = 'inline-flex';
    }
}

function emptyTrash() {
    showConfirmDialog(
        '⚠️ This will permanently delete all items in your trash. This action cannot be undone!',
        function() {
            showToast('Emptying trash...', 'info');

            // Get all items currently in trash view
            const trashItems = state.currentItems;
            const totalItems = trashItems.length;

            if (totalItems === 0) {
                showToast('Trash is already empty', 'info');
                return;
            }

            console.log('🗑️ Emptying trash: ' + totalItems + ' items');

            let deletedCount = 0;
            let errorCount = 0;
            let processedCount = 0;
            let errorMessages = [];

            // Process each item in trash
            trashItems.forEach(function(item) {
                // ✅ Determine the correct endpoint
                let endpoint;
                if (item.is_folder) {
                    endpoint = window.API_BASE + '/folders/' + item.id + '?permanent=true';
                } else {
                    endpoint = window.API_BASE + '/files/' + item.id + '?permanent=true';
                }

                console.log('🗑️ Deleting:', item.name, 'ID:', item.id, 'Endpoint:', endpoint);

                fetch(endpoint, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'include'
                })
                .then(async response => {
                    const text = await response.text();
                    console.log('📥 Response for ' + item.name + ':', response.status, text);

                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        if (response.ok) {
                            return { success: true, message: 'Deleted successfully' };
                        }
                        throw new Error('Server error: ' + response.status);
                    }
                })
                .then(data => {
                    processedCount++;

                    if (data.success) {
                        deletedCount++;
                        console.log('✅ Deleted:', item.name);
                    } else {
                        errorCount++;
                        errorMessages.push(item.name + ': ' + (data.message || 'Unknown error'));
                        console.error('❌ Error deleting:', item.name, data.message);
                    }

                    // Check if all items have been processed
                    if (processedCount === totalItems) {
                        const message = 'Trash emptied! ' + deletedCount + ' item(s) deleted.';
                        if (errorCount > 0) {
                            showToast(message + ' (' + errorCount + ' failed: ' + errorMessages.join('; ') + ')', 'error');
                        } else {
                            showToast(message, 'success');
                        }
                        // Reload trash view
                        loadItems('trash');
                    }
                })
                .catch(error => {
                    processedCount++;
                    errorCount++;
                    errorMessages.push(item.name + ': ' + error.message);
                    console.error('❌ Error deleting item:', item.name, error);

                    if (processedCount === totalItems) {
                        showToast('Error emptying trash. ' + errorCount + ' items failed.', 'error');
                        loadItems('trash');
                    }
                });
            });
        }
    );
}



function showConfirmDialog(message, onConfirm) {
    const overlay = document.getElementById('confirmDialog');
    const messageEl = document.getElementById('confirmMessage');
    const confirmBtn = document.getElementById('confirmActionBtn');

    if (!overlay) return;

    if (message) messageEl.textContent = message;

    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    confirmBtn.onclick = function() {
        closeConfirmDialog();
        if (typeof onConfirm === 'function') onConfirm();
    };
}

function closeConfirmDialog() {
    const overlay = document.getElementById('confirmDialog');
    if (overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function openActionMenu(itemId, type) {
    console.log('📂 Opening menu for:', itemId, 'Type:', type);

    const item = state.currentItems.find(i => i.id === itemId && i.is_folder === (type === 'folder'));

    if (!item) {
        console.error('❌ Item not found!', itemId, type);
        showToast('Item not found', 'error');
        return;
    }

    console.log('📂 Item found:', item);

    const menu = document.getElementById('actionMenu');
    const overlay = document.getElementById('actionMenuOverlay');
    const menuContent = document.getElementById('menuContent');
    const menuHeader = document.getElementById('menuHeader');

    if (!menu || !overlay || !menuContent || !menuHeader) {
        console.error('❌ Menu elements not found');
        return;
    }

    const isTrash = state.currentViewType === 'trash' || item.in_trash;
    const isShared = item.is_shared || false;
    const canDownload = item.can_download !== undefined ? item.can_download : true;
    const itemName = item.name || 'Unknown';

    // Check permissions
    const canShare = item.is_owner || (item.permission && ['share', 'full'].includes(item.permission));
    const canRename = item.is_owner || (item.permission && ['edit', 'full'].includes(item.permission));

    menuHeader.innerHTML = `
        <span class="item-name">${escapeHtml(itemName)}</span>
        ${isShared && !item.is_owner && item.permission ? `<span class="permission-badge ${item.permission}" style="font-size:11px;">${item.permission}</span>` : ''}
        <button class="close-menu" onclick="closeActionMenu()">
            <i class="fas fa-times"></i>
        </button>
    `;

    let items = '';

    if (isTrash) {
        items += `
            <button class="menu-item" onclick="restoreItem(${item.id}, '${type}'); closeActionMenu();">
                <i class="fas fa-undo"></i> Restore
            </button>
            <button class="menu-item danger" onclick="permanentDelete(${item.id}, '${type}'); closeActionMenu();">
                <i class="fas fa-trash"></i> Delete Permanently
            </button>
        `;
    } else {
        // View button for files only
        if (!item.is_folder) {
            items += `
                <button class="menu-item" onclick="openFile(${item.id}); closeActionMenu();">
                    <i class="fas fa-eye"></i> View
                </button>
            `;
        }

        // Download button
        if (canDownload) {
            const downloadLabel = item.is_folder ? 'Download ZIP' : 'Download';
            const downloadAction = item.is_folder ? 'downloadFolder' : 'downloadFile';
            items += `
                <button class="menu-item" onclick="${downloadAction}(${item.id}); closeActionMenu();">
                    <i class="fas fa-download" style="color:#34a853;"></i> ${downloadLabel}
                </button>
            `;
        }

        // Star button
        items += `
            <button class="menu-item" onclick="toggleStar(${item.id}, '${type}'); closeActionMenu();">
                <i class="fas fa-star" style="color: ${item.is_starred ? '#fbbc04' : '#5f6368'};"></i> ${item.is_starred ? 'Unstar' : 'Star'}
            </button>
        `;

        // Share button - only if can share
        if (canShare) {
            items += `
                <button class="menu-item" onclick="window.openShareModal(${item.id}, '${type}', '${escapeHtml(itemName)}'); closeActionMenu();">
                    <i class="fas fa-share-alt" style="color:#1a73e8;"></i> Share
                </button>
            `;
        }

        // Rename button - only if can rename
        if (canRename) {
            const renameAction = item.is_folder ? 'renameFolder' : 'renameFile';
            items += `
                <button class="menu-item" onclick="${renameAction}(${item.id}); closeActionMenu();">
                    <i class="fas fa-edit"></i> Rename
                </button>
            `;
        }

        // ✅ Delete/Remove from Shared - Use correct function based on type
        if (isShared && !item.is_owner) {
            // Shared item (not owner) - Remove from Shared
            // ✅ Use removeShare directly with the correct type
            items += `
                <button class="menu-item danger" onclick="removeShare(${item.id}, '${type}'); closeActionMenu();">
                    <i class="fas fa-user-minus"></i> Remove from Shared
                </button>
            `;
        } else if (item.is_owner) {
            // Owner - Move to Trash
            const deleteAction = item.is_folder ? 'deleteFolder' : 'deleteFile';
            items += `
                <button class="menu-item danger" onclick="${deleteAction}(${item.id}); closeActionMenu();">
                    <i class="fas fa-trash"></i> Move to Trash
                </button>
            `;
        }
    }

    menuContent.innerHTML = items;
    menu.classList.add('active');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

/**
 * Remove shared item from user's view (for shared users)
 * This only removes the share for the current user, not delete the actual file
 */

function removeShare(itemId, type) {
    if (!confirm('Remove this item from your shared items? The item will still be available to the owner and other users.')) {
        return;
    }

    // ✅ Use the correct endpoint based on type
    const endpoint = type === 'folder'
        ? window.API_BASE + '/folders/' + itemId
        : window.API_BASE + '/files/' + itemId;

    console.log('🗑️ Removing share - Type:', type, 'ID:', itemId, 'Endpoint:', endpoint);

    showToast('Removing from your shared items...', 'info');

    fetch(endpoint, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            remove_share: true  // Flag to indicate this is a share removal
        }),
        credentials: 'include'
    })
    .then(async response => {
        const text = await response.text();
        console.log('📥 Response:', text);
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('❌ Failed to parse JSON:', text);
            throw new Error('Server returned invalid response');
        }
    })
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Item removed from your shared items', 'success');
            loadItems(state.currentViewType);
        } else {
            showToast('Failed to remove: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('❌ Remove share error:', error);
        showToast('Error removing item: ' + error.message, 'error');
    });
}

function closeActionMenu() {
    const menu = document.getElementById('actionMenu');
    const overlay = document.getElementById('actionMenuOverlay');
    if (menu) menu.classList.remove('active');
    if (overlay) overlay.classList.remove('active');
    document.body.style.overflow = '';
}

// ============================================
// UI SETUP
// ============================================

function setupEventListeners() {
    // Upload button
    document.getElementById('uploadBtn')?.addEventListener('click', function() {
        document.getElementById('fileInput').click();
    });

    // File input change
    document.getElementById('fileInput')?.addEventListener('change', function(e) {
        if (this.files.length > 0) {
            uploadFiles(this.files);
            this.value = '';
        }
    });

    // New Folder button
    document.getElementById('newFolderBtn')?.addEventListener('click', function() {
        showCreateFolderModal();
    });

    // View toggle
    document.querySelectorAll('.view-toggle button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.view-toggle button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            state.currentView = this.dataset.view;
            renderItems();
        });
    });

    // Empty trash button
    document.getElementById('emptyTrashBtn')?.addEventListener('click', function() {
        emptyTrash();
    });
}

// ============================================
// NEW BUTTON DROPDOWN - SIMPLIFIED WORKING VERSION
// ============================================

function setupNewButton() {
    console.log('🔧 Setting up New button...');

    const newBtn = document.getElementById('newBtn');
    if (!newBtn) {
        console.warn('❌ New button not found!');
        return;
    }

    // Remove any inline onclick
    newBtn.removeAttribute('onclick');
    newBtn.onclick = null;

    // Remove all existing listeners by cloning
    const parent = newBtn.parentNode;
    const newButton = document.createElement('button');
    newButton.className = 'new-btn';
    newButton.id = 'newBtn';
    newButton.innerHTML = '<i class="fas fa-plus"></i><span>New</span>';
    parent.replaceChild(newButton, newBtn);

    // Get fresh reference
    const freshButton = document.getElementById('newBtn');

    // Add click event
    freshButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('🔄 New button clicked!');

        if (state.currentViewType === 'trash') {
            showToast('Cannot perform actions in trash', 'error');
            return;
        }

        // Toggle dropdown
        if (document.getElementById('newDropdown')) {
            closeNewDropdown();
        } else {
            createNewDropdown();
        }
    });

    console.log('✅ New button setup complete');
}

// ============================================
// DROPDOWN STATE
// ============================================

let newDropdownOpen = false;

// ============================================
// CREATE DROPDOWN
// ============================================

function createNewDropdown() {
    console.log('📋 Creating dropdown');

    // Remove existing dropdown if any
    const existing = document.getElementById('newDropdown');
    if (existing) {
        existing.remove();
    }

    newDropdownOpen = true;
    const dropdown = document.createElement('div');
    dropdown.id = 'newDropdown';
    dropdown.className = 'new-dropdown';

    // Position below the New button
    const newBtn = document.getElementById('newBtn');
    const rect = newBtn ? newBtn.getBoundingClientRect() : null;

    let top = rect ? rect.bottom + 5 : 80;
    let left = rect ? rect.left : 20;

    // Adjust for sidebar collapse
    const sidebar = document.getElementById('sidebar');
    if (sidebar && sidebar.classList.contains('collapsed')) {
        left = 12;
    }

    // Style the dropdown
    Object.assign(dropdown.style, {
        position: 'fixed',
        top: top + 'px',
        left: left + 'px',
        background: '#ffffff',
        borderRadius: '8px',
        boxShadow: '0 4px 20px rgba(0,0,0,0.2)',
        zIndex: '999999',
        minWidth: '220px',
        padding: '6px 0',
        border: '1px solid #e8eaed',
        fontFamily: "'Google Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif",
        animation: 'dropdownSlide 0.15s ease'
    });

    // Dropdown items
    dropdown.innerHTML = `
        <div class="dropdown-item" data-action="folder" style="padding:12px 20px;cursor:pointer;display:flex;align-items:center;gap:14px;border:none;background:none;width:100%;text-align:left;font-size:14px;color:#202124;border-radius:4px 4px 0 0;font-family:inherit;">
            <i class="fas fa-folder" style="color:#fbbc04;font-size:18px;width:24px;"></i>
            <span>New Folder</span>
        </div>
        <div class="dropdown-item" data-action="file" style="padding:12px 20px;cursor:pointer;display:flex;align-items:center;gap:14px;border:none;background:none;width:100%;text-align:left;font-size:14px;color:#202124;font-family:inherit;">
            <i class="fas fa-file-upload" style="color:#1a73e8;font-size:18px;width:24px;"></i>
            <span>File Upload</span>
        </div>
        <div class="dropdown-item" data-action="folder-upload" style="padding:12px 20px;cursor:pointer;display:flex;align-items:center;gap:14px;border:none;background:none;width:100%;text-align:left;font-size:14px;color:#202124;border-top:1px solid #e8eaed;border-radius:0 0 4px 4px;font-family:inherit;">
            <i class="fas fa-folder-open" style="color:#34a853;font-size:18px;width:24px;"></i>
            <span>Folder Upload</span>
        </div>
    `;

    // Add click handlers to items
    const items = dropdown.querySelectorAll('.dropdown-item');
    items.forEach(function(item) {
        // Hover effects
        item.addEventListener('mouseenter', function() {
            this.style.background = '#f1f3f4';
        });
        item.addEventListener('mouseleave', function() {
            this.style.background = 'transparent';
        });

        // Click handler
        item.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const action = this.dataset.action;
            console.log('📋 Dropdown item clicked:', action);

            // Close dropdown
            closeNewDropdown();

            // Execute action after dropdown closes
            setTimeout(function() {
                switch(action) {
                    case 'folder':
                        console.log('📁 Creating new folder...');
                        showCreateFolderModal();
                        break;
                    case 'file':
                        console.log('📤 Uploading file...');
                        const uploadBtn = document.getElementById('uploadBtn');
                        if (uploadBtn) {
                            uploadBtn.click();
                        }
                        break;
                    case 'folder-upload':
                        console.log('📂 Uploading folder...');
                        const input = document.createElement('input');
                        input.type = 'file';
                        input.webkitdirectory = true;
                        input.multiple = true;
                        input.style.display = 'none';
                        document.body.appendChild(input);
                        input.click();
                        input.addEventListener('change', function() {
                            if (this.files.length > 0) {
                                uploadFiles(this.files);
                            }
                            this.remove();
                        });
                        break;
                    default:
                        console.warn('Unknown action:', action);
                }
            }, 100);
        });
    });

    document.body.appendChild(dropdown);

    // Close on click outside
    setTimeout(function() {
        document.addEventListener('click', closeDropdownOutside);
    }, 10);

    console.log('✅ Dropdown created');
}

// ============================================
// CLOSE DROPDOWN
// ============================================

function closeDropdownOutside(e) {
    const newBtn = document.getElementById('newBtn');
    const dropdown = document.getElementById('newDropdown');

    if (dropdown && !dropdown.contains(e.target) && e.target !== newBtn) {
        closeNewDropdown();
    }
}

function closeNewDropdown() {
    console.log('🔒 Closing dropdown');
    newDropdownOpen = false;
    const dropdown = document.getElementById('newDropdown');
    if (dropdown) {
        dropdown.remove();
    }
    document.removeEventListener('click', closeDropdownOutside);
}

// ============================================
// ADD CSS FOR DROPDOWN ANIMATION
// ============================================

(function addDropdownStyles() {
    if (document.getElementById('dropdownStyles')) return;

    const style = document.createElement('style');
    style.id = 'dropdownStyles';
    style.textContent = `
        @keyframes dropdownSlide {
            from { opacity: 0; transform: translateY(-8px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .dropdown-item {
            transition: background 0.15s;
            cursor: pointer;
        }
        .dropdown-item:active {
            background: #e8eaed !important;
        }
    `;
    document.head.appendChild(style);
})();

// Make functions globally accessible
window.createNewDropdown = createNewDropdown;
window.closeNewDropdown = closeNewDropdown;



function setupSidebarToggle() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebarToggle = document.getElementById('sidebarToggle');

    if (menuToggle) {
        menuToggle.addEventListener('click', toggleSidebar);
    }
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (!sidebar) return;

    sidebar.classList.toggle('collapsed');
    if (mainContent) mainContent.classList.toggle('expanded');

    if (toggleBtn) {
        if (sidebar.classList.contains('collapsed')) {
            toggleBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        } else {
            toggleBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
        }
    }

    const newDropdown =
    document.getElementById('newDropdown');

	if (newDropdown) {
		const left =
			sidebar.classList.contains('collapsed')
				? '70px'
				: '250px';

		newDropdown.style.left = left;
	}
}

function setupSearch() {
    const searchInput =
        document.getElementById('searchInput');

    if (!searchInput) {
        return;
    }

    let searchTimeout = null;
    let lastSearchValue = '';

    function performSearch(value) {
        value = (value || '').trim();

        if (searchTimeout) {
            clearTimeout(searchTimeout);
            searchTimeout = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Search cleared
        |--------------------------------------------------------------------------
        */

        if (value === '') {
            lastSearchValue = '';
            state.searchTerm = '';

            loadItems(
                state.currentViewType,
                ''
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Avoid duplicate request
        |--------------------------------------------------------------------------
        */

        if (value === lastSearchValue) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Debounced search
        |--------------------------------------------------------------------------
        */

        searchTimeout = setTimeout(function() {

            lastSearchValue = value;
            state.searchTerm = value;

            console.log(
                '🔍 Searching:',
                value,
                'view:',
                state.currentViewType,
                'folder:',
                state.currentFolderId
            );

            loadItems(
                state.currentViewType,
                value
            );

        }, 500);
    }

    searchInput.addEventListener(
        'input',
        function() {
            performSearch(this.value);
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Escape clears search
    |--------------------------------------------------------------------------
    */

    searchInput.addEventListener(
        'keydown',
        function(event) {

            if (event.key === 'Escape') {
                this.value = '';

                lastSearchValue = '';
                state.searchTerm = '';

                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                    searchTimeout = null;
                }

                loadItems(
                    state.currentViewType,
                    ''
                );
            }
        }
    );
}

function setupProfileDropdown() {
    const userAvatar = document.getElementById('userAvatar');
    const profileDropdown = document.getElementById('profileDropdown');

    if (userAvatar && profileDropdown) {
        userAvatar.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-profile')) {
                profileDropdown.classList.remove('active');
            }
        });
    }
}

/**
 * Remove shared folder from user's view
 */
function removeShareFolder(folderId) {
    removeShare(folderId, 'folder');
}

/**
 * Remove shared file from user's view
 */
function removeShareFile(fileId) {
    removeShare(fileId, 'file');
}

function logout() {
    if (!confirm('Are you sure you want to logout?')) {
        return;
    }

    console.log('🚪 Logging out from Efactor NAS...');

    const nasBase = (
        window._basePath ||
        window.NAS_BASE ||
        '/NAS'
    ).replace(/\/+$/, '');

    // Go through the NAS web logout route.
    // The controller will destroy Efactor + NAS session
    // and redirect to the Efactor signin page.
    window.location.href = nasBase + '/logout';
}

// ============================================
// INITIALIZATION
// ============================================
console.log('🚀 App.js loaded successfully');
console.log('📌 API Base:', window.API_BASE);
console.log('📌 User ID:', window._userId);
