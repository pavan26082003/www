
// ============================================
// TOAST NOTIFICATIONS - SINGLE SOURCE
// ============================================

function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    if (!toast) {
        console.log('Toast:', type, message);
        return;
    }
    
    const icon = toast.querySelector('.toast-icon');
    const text = toast.querySelector('.toast-text');
    
    if (!icon || !text) {
        console.log('Toast:', type, message);
        return;
    }
    
    toast.className = 'toast';
    
    const iconMap = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-exclamation-circle',
        'warning': 'fas fa-exclamation-triangle',
        'info': 'fas fa-info-circle'
    };
    
    const colorMap = {
        'success': '#34a853',
        'error': '#ea4335',
        'warning': '#fbbc04',
        'info': '#1a73e8'
    };
    
    icon.className = 'toast-icon ' + (iconMap[type] || iconMap['info']);
    toast.style.borderLeft = '4px solid ' + (colorMap[type] || colorMap['info']);
    text.textContent = message;
    toast.classList.add('show');
    
    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(function() {
        toast.classList.remove('show');
    }, 3000);
}



// ============================================
// HTML HELPERS
// ============================================

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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

function formatFileSize(bytes) {
    if (bytes === 0 || bytes === undefined || bytes === null) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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

function getLanguageFromExtension(extension) {
    const langMap = {
        'php': 'php', 'js': 'javascript', 'css': 'css', 'html': 'html',
        'htm': 'html', 'json': 'json', 'xml': 'xml', 'txt': 'text',
        'py': 'python', 'java': 'java', 'cpp': 'cpp', 'c': 'c',
        'sql': 'sql', 'sh': 'bash', 'bash': 'bash', 'ruby': 'ruby',
        'go': 'go', 'rs': 'rust', 'ts': 'typescript', 'jsx': 'jsx',
        'tsx': 'tsx', 'vue': 'vue'
    };
    return langMap[extension] || 'text';
}



// Make functions globally available
window.showToast = showToast;
window.escapeHtml = escapeHtml;
window.formatDate = formatDate;
window.formatFileSize = formatFileSize;
window.getFileIcon = getFileIcon;
window.getFileColor = getFileColor;
window.getLanguageFromExtension = getLanguageFromExtension;

console.log('🔧 Utils module loaded');