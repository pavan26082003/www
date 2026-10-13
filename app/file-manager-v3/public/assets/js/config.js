// public/assets/js/config.js
// Central Configuration - SINGLE SOURCE OF TRUTH

// ✅ Set the correct API base path
if (typeof window._apiBase === 'undefined') {
    // Use the full path with the project folder
    window._apiBase = '/app/file-manager-v3/api/v1';
}

// Only declare API_BASE if not already declared
if (typeof window.API_BASE === 'undefined') {
    window.API_BASE = window._apiBase;
}

// Configuration object
window.CONFIG = {
    API_BASE: window.API_BASE,
    ASSETS_URL: '/app/file-manager-v3/assets',
    WS_URL: 'ws://' + window.location.host + '/app/file-manager-v3/ws'
};

console.log('✅ Config loaded - API_BASE:', window.API_BASE);