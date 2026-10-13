// assets/NAS/js/share.js
// Share Functionality

if (!window.API_BASE) {
    console.warn('⚠️ NAS API base is not configured yet. Load config.js before using this module.');
}

console.log('🔗 share.js loading...');

let shareState = {
    itemId: null,
    itemType: null,
    selectedUser: null,
    selectedUsername: '',
    selectedUserEmail: ''
};


// Use the shared NAS request wrapper when available. If a stale/local
// config.js is loaded, fall back to the CSRF data injected by the dashboard.
function shareRequest(url, options = {}) {
    if (typeof window.nasFetch === 'function') {
        return window.nasFetch(url, options);
    }

    const request = { ...options };
    request.credentials = request.credentials || 'include';

    const method = String(request.method || 'GET').toUpperCase();
    const headers = new Headers(request.headers || {});
    headers.set('Accept', headers.get('Accept') || 'application/json');
    headers.set('X-Requested-With', 'XMLHttpRequest');

    if (!['GET', 'HEAD', 'OPTIONS'].includes(method)) {
        const csrf = window.NAS_CSRF || {};
        const tokenName = csrf.tokenName || 'rise_csrf_token';
        const headerName = csrf.headerName || 'X-CSRF-TOKEN';
        const hash = csrf.hash || '';

        if (hash) {
            headers.set(headerName, hash);

            if (request.body instanceof FormData && !request.body.has(tokenName)) {
                request.body.append(tokenName, hash);
            }
        }
    }

    request.headers = headers;
    return window.fetch(url, request);
}

function ensureShareUserList() {
    const resultsDiv = document.getElementById('shareSearchResults');
    if (!resultsDiv) return null;

    let userList = document.getElementById('shareUserList');
    if (!userList) {
        resultsDiv.innerHTML = '<div id="shareUserList"></div>';
        userList = document.getElementById('shareUserList');
    }

    return userList;
}

function bindShareModalEvents(modal) {
    if (!modal || modal.dataset.shareBound === '1') return;

    modal.dataset.shareBound = '1';

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeShareModal();
        }
    });

    const searchInput = modal.querySelector('#shareSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchUsers();
            }
        });
    }
}

// ============================================
// OPEN SHARE MODAL
// ============================================

function openShareModal(itemId, itemType, itemName) {
    console.log('🔗 openShareModal called with:', itemId, itemType, itemName);
    
    // Store state
    shareState.itemId = itemId;
    shareState.itemType = itemType;
    shareState.selectedUser = null;
    shareState.selectedUsername = '';
    shareState.selectedUserEmail = '';
    
    // Get modal. The local view may not have the pre-rendered modal, so
    // keep the reference returned by createShareModal().
    let modal = document.getElementById('shareModal');
    if (!modal) {
        console.warn('⚠️ Share modal not found in DOM; creating fallback modal');
        modal = createShareModal();
        if (!modal) {
            console.error('❌ Failed to create share modal');
            return;
        }
    }

    bindShareModalEvents(modal);
    
    // Update modal content
    const nameElement = document.getElementById('shareItemName');
    const typeElement = document.getElementById('shareItemType');
    const iconBox = document.getElementById('shareItemIcon');
    const iconElement = iconBox ? iconBox.querySelector('i') : null;
    
    if (nameElement) nameElement.textContent = itemName;
    if (typeElement) typeElement.textContent = itemType.charAt(0).toUpperCase() + itemType.slice(1);
    if (iconElement) {
        iconElement.className = itemType === 'folder' ? 'fas fa-folder' : 'fas fa-file';
    }
    if (iconBox) {
        iconBox.style.background = itemType === 'folder' ? '#fef7e0' : '#e8f0fe';
        iconBox.style.color = itemType === 'folder' ? '#fbbc04' : '#1a73e8';
    }
    
    // Reset form
    const searchInput = document.getElementById('shareSearchInput');
    const userList = ensureShareUserList();
    const permissionSelect = document.getElementById('sharePermission');
    const expiresInput = document.getElementById('shareExpires');
    const resultsDiv = document.getElementById('shareSearchResults');
    
    if (searchInput) {
        searchInput.value = '';
        setTimeout(() => searchInput.focus(), 100);
    }
    if (userList) userList.innerHTML = '';
    if (permissionSelect) permissionSelect.value = 'view';
    if (expiresInput) expiresInput.value = '';
    if (resultsDiv) resultsDiv.style.display = 'none';
    
    // Load existing shares
    loadExistingShares(itemId, itemType);
    
    // Show modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// ============================================
// CLOSE SHARE MODAL
// ============================================

function closeShareModal() {
    const modal = document.getElementById('shareModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// ============================================
// CREATE SHARE MODAL (fallback)
// ============================================

function createShareModal() {
    // Check if already exists
    if (document.getElementById('shareModal')) {
        return document.getElementById('shareModal');
    }
    
    const modal = document.createElement('div');
    modal.id = 'shareModal';
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="share-modal-content">
            <div class="share-modal-header">
                <div class="share-modal-title">
                    <div class="share-item-icon" id="shareItemIcon">
                        <i class="fas fa-file"></i>
                    </div>
                    <div class="share-title-text">
                        <h3>Share</h3>
                        <p class="share-item-name" id="shareItemName">Item name</p>
                        <p class="share-item-type" id="shareItemType" style="font-size:12px;color:#5f6368;margin:2px 0 0;"></p>
                    </div>
                </div>
                <button class="modal-close" onclick="closeShareModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="share-modal-body">
                <div class="share-search-section">
                    <label class="share-label"><i class="fas fa-user-plus"></i> Add people</label>
                    <div class="share-search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="shareSearchInput" placeholder="Enter email or name..." class="share-search-input">
                        <button onclick="searchUsers()" class="share-search-btn">
                            <i class="fas fa-arrow-right"></i> <span>Search</span>
                        </button>
                    </div>
                    <div id="shareSearchResults" class="share-search-results" style="display:none;">
                        <div id="shareUserList"></div>
                    </div>
                </div>
                <div class="share-permission-section">
                    <label class="share-label"><i class="fas fa-lock"></i> Permission</label>
                    <select id="sharePermission" class="share-select">
                        <option value="view">👁️ View only</option>
                        <option value="download">⬇️ View & download</option>
                        <option value="edit">✏️ Edit</option>
                        <option value="share">🔗 Share</option>
                        <option value="full">⭐ Full access</option>
                    </select>
                </div>
                <div class="share-expiration-section">
                    <label class="share-label"><i class="fas fa-clock"></i> Expiration (optional)</label>
                    <input type="datetime-local" id="shareExpires" class="share-date-input">
                </div>
                <button onclick="shareItem()" class="share-btn-primary" id="shareConfirmBtn">
                    <i class="fas fa-share-alt"></i> Share
                </button>
                <div class="share-existing-section">
                    <div class="share-existing-header">
                        <h4>People with access</h4>
                        <span class="share-count" id="shareCount">0</span>
                    </div>
                    <div id="existingShares" class="share-existing-list">
                        <div class="share-empty-state">
                            <i class="fas fa-users"></i>
                            <p>No one has access yet</p>
                            <span>Share this item with others</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    bindShareModalEvents(modal);
    return modal;
}

// ============================================
// SEARCH USERS
// ============================================

function searchUsers() {
    const searchInput = document.getElementById('shareSearchInput');
    if (!searchInput) return;
    
    const query = searchInput.value.trim();
    if (query.length < 2) {
        showToast('Please enter at least 2 characters', 'error');
        return;
    }
    
    const resultsDiv = document.getElementById('shareSearchResults');
    const userList = ensureShareUserList();
    
    if (!resultsDiv || !userList) return;
    
    resultsDiv.style.display = 'block';
    userList.innerHTML = `<div class="share-loading"><i class="fas fa-spinner fa-spin"></i> Searching...</div>`;
    
    shareRequest(window.API_BASE + '/users/search?q=' + encodeURIComponent(query), {
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.users && data.users.length > 0) {
            let html = '';
            data.users.forEach(user => {
                html += `
                    <div class="share-user-item" onclick="selectUser(${user.id}, '${escapeHtml(user.username)}', '${escapeHtml(user.email)}')">
                        <div class="share-user-avatar">${escapeHtml(user.username.charAt(0).toUpperCase())}</div>
                        <div class="share-user-info">
                            <div class="share-user-name">${escapeHtml(user.username)}</div>
                            <div class="share-user-email">${escapeHtml(user.email)}</div>
                        </div>
                        <button class="share-user-add"><i class="fas fa-plus"></i></button>
                    </div>
                `;
            });
            userList.innerHTML = html;
        } else {
            userList.innerHTML = `<div class="share-empty-search"><i class="fas fa-user-slash"></i><p>No users found</p></div>`;
        }
    })
    .catch(error => {
        console.error('Search error:', error);
        userList.innerHTML = `<div class="share-error"><i class="fas fa-exclamation-triangle"></i><p>Error searching users</p></div>`;
    });
}

// ============================================
// SELECT USER
// ============================================

function selectUser(id, username, email) {
    shareState.selectedUser = id;
    shareState.selectedUsername = username;
    shareState.selectedUserEmail = email;
    
    const resultsDiv = document.getElementById('shareSearchResults');
    const userList = ensureShareUserList();
    if (!resultsDiv || !userList) return;
    
    userList.innerHTML = `
        <div class="share-selected-user">
            <div class="share-user-avatar" style="background:#1a73e8;">${escapeHtml(username.charAt(0).toUpperCase())}</div>
            <div class="share-user-info">
                <div class="share-user-name">${escapeHtml(username)}</div>
                <div class="share-user-email">${escapeHtml(email)}</div>
            </div>
            <button type="button" onclick="clearSelectedUser()" class="share-user-remove"><i class="fas fa-times"></i></button>
        </div>
    `;
    resultsDiv.style.display = 'block';
}

// ============================================
// CLEAR SELECTED USER
// ============================================

function clearSelectedUser() {
    shareState.selectedUser = null;
    shareState.selectedUsername = '';
    shareState.selectedUserEmail = '';
    
    const resultsDiv = document.getElementById('shareSearchResults');
    const userList = ensureShareUserList();
    if (userList) {
        userList.innerHTML = '';
    }
    if (resultsDiv) {
        resultsDiv.style.display = 'none';
    }
}

// ============================================
// SHARE ITEM
// ============================================

function shareItem() {
    if (!shareState.selectedUser) {
        showToast('Please select a user to share with', 'error');
        return;
    }
    
    const permissionSelect = document.getElementById('sharePermission');
    const expiresInput = document.getElementById('shareExpires');
    const shareBtn = document.getElementById('shareConfirmBtn');
    
    const permission = permissionSelect ? permissionSelect.value : 'view';
    const expiresAt = expiresInput ? expiresInput.value : null;
    
    const payload = {
        item_type: shareState.itemType,
        item_id: shareState.itemId,
        share_with_user_id: shareState.selectedUser,
        permission: permission,
        expires_at: expiresAt
    };
    console.log('📤 Share payload:', payload);
    
    if (shareBtn) {
        shareBtn.disabled = true;
        shareBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sharing...';
    }
    
    shareRequest(window.API_BASE + '/share', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload),
        credentials: 'include'
    })
    .then(async response => {
        const text = await response.text();
        console.log('📥 Response status:', response.status);
        console.log('📥 Response text:', text);
        
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('❌ Failed to parse JSON:', text);
            throw new Error('Server returned: ' + text.substring(0, 100));
        }
    })
    .then(data => {
        if (data.success) {
            showToast('Item shared successfully!', 'success');
            loadExistingShares(shareState.itemId, shareState.itemType);
            clearSelectedUser();
            const searchInput = document.getElementById('shareSearchInput');
            if (searchInput) searchInput.value = '';
            const resultsDiv = document.getElementById('shareSearchResults');
            if (resultsDiv) resultsDiv.style.display = 'none';
        } else {
            showToast('Failed to share: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('❌ Share error:', error);
        showToast('Error sharing item: ' + error.message, 'error');
    })
    .finally(() => {
        if (shareBtn) {
            shareBtn.disabled = false;
            shareBtn.innerHTML = '<i class="fas fa-share-alt"></i> Share';
        }
    });
}

// ============================================
// LOAD EXISTING SHARES
// ============================================

function loadExistingShares(itemId, itemType) {
    const container = document.getElementById('existingShares');
    const countElement = document.getElementById('shareCount');
    
    if (!container) {
        console.error('❌ existingShares element not found');
        return;
    }
    
    container.innerHTML = `<div class="share-loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>`;
    
    shareRequest(window.API_BASE + '/share?item_type=' + encodeURIComponent(itemType) + '&item_id=' + encodeURIComponent(itemId), {
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        console.log('📥 Shares data:', data);
        
        if (data.success && data.shares && data.shares.length > 0) {
            if (countElement) countElement.textContent = data.shares.length;
            
            let html = '';
            data.shares.forEach(share => {
                const username = share.shared_with?.username || 'Unknown';
                const email = share.shared_with?.email || '';
                const permission = share.permissions || 'view';
                const initial = username.charAt(0).toUpperCase();
                
                html += `
                    <div class="share-item-row">
                        <div class="share-item-user">
                            <div class="share-user-avatar small" style="background:#1a73e8;color:white;display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;font-weight:600;font-size:14px;flex-shrink:0;">
                                ${escapeHtml(initial)}
                            </div>
                            <div class="share-user-info">
                                <div class="share-item-username">${escapeHtml(username)}</div>
                                <div class="share-item-email">${escapeHtml(email)}</div>
                            </div>
                        </div>
                        <div class="share-item-actions">
                            <select onchange="updatePermission(${share.share_id}, this.value)" class="share-permission-select">
                                <option value="view" ${permission === 'view' ? 'selected' : ''}>👁️ View</option>
                                <option value="download" ${permission === 'download' ? 'selected' : ''}>⬇️ Download</option>
                                <option value="edit" ${permission === 'edit' ? 'selected' : ''}>✏️ Edit</option>
                                <option value="share" ${permission === 'share' ? 'selected' : ''}>🔗 Share</option>
                                <option value="full" ${permission === 'full' ? 'selected' : ''}>⭐ Full</option>
                            </select>
                            ${share.expires_at ? `<span class="share-expires-badge">Expires ${formatDate(share.expires_at)}</span>` : ''}
                            <button onclick="revokeShare(${share.share_id})" class="share-revoke-btn" title="Revoke access">
                                <i class="fas fa-user-times"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        } else {
            if (countElement) countElement.textContent = '0';
            container.innerHTML = `
                <div class="share-empty-state">
                    <i class="fas fa-users"></i>
                    <p>No one has access yet</p>
                    <span>Share this item with others</span>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Load shares error:', error);
        container.innerHTML = `<div class="share-error"><i class="fas fa-exclamation-triangle"></i><p>Error loading shares</p></div>`;
    });
}

// ============================================
// REVOKE SHARE
// ============================================

function revokeShare(shareId) {
    if (!confirm('Revoke access for this user?')) return;
    
    shareRequest(window.API_BASE + '/share/' + shareId, { 
        method: 'DELETE',
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Access revoked successfully', 'success');
            loadExistingShares(shareState.itemId, shareState.itemType);
        } else {
            showToast('Failed to revoke: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Revoke error:', error);
        showToast('Error revoking access', 'error');
    });
}

// ============================================
// UPDATE PERMISSION
// ============================================

function updatePermission(shareId, newPermission) {
    console.log('📝 Updating permission for share:', shareId, 'to:', newPermission);
    
    const select = document.querySelector(`select[onchange*="updatePermission(${shareId}"]`);
    if (select) {
        select.disabled = true;
        select.style.opacity = '0.6';
    }
    
    shareRequest(window.API_BASE + '/share/' + shareId, {
        method: 'PUT',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            permission: newPermission,
            expires_at: null 
        }),
        credentials: 'include'
    })
    .then(async response => {
        const text = await response.text();
        console.log('📥 Update response:', response.status, text);
        
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('❌ Failed to parse JSON:', text);
            throw new Error('Server returned: ' + text.substring(0, 100));
        }
    })
    .then(data => {
        if (data.success) {
            showToast('Permission updated successfully!', 'success');
            loadExistingShares(shareState.itemId, shareState.itemType);
        } else {
            showToast('Failed to update permission: ' + (data.message || 'Unknown error'), 'error');
            if (select) {
                select.value = select.dataset.oldValue || 'view';
            }
        }
    })
    .catch(error => {
        console.error('Update permission error:', error);
        showToast('Error updating permission', 'error');
    })
    .finally(() => {
        if (select) {
            select.disabled = false;
            select.style.opacity = '1';
        }
    });
}

// ============================================
// MAKE FUNCTIONS GLOBALLY AVAILABLE
// ============================================

// Expose all functions to window
window.openShareModal = openShareModal;
window.closeShareModal = closeShareModal;
window.searchUsers = searchUsers;
window.selectUser = selectUser;
window.clearSelectedUser = clearSelectedUser;
window.shareItem = shareItem;
window.revokeShare = revokeShare;
window.updatePermission = updatePermission;
window.loadExistingShares = loadExistingShares;
window.createShareModal = createShareModal;

const initialShareModal = document.getElementById('shareModal');
if (initialShareModal) {
    bindShareModalEvents(initialShareModal);
}

console.log('🔗 Share module loaded');
console.log('🔗 openShareModal type:', typeof window.openShareModal);