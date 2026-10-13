// assets/NAS/js/websocket-client.js
// WebSocket Client for Real-time Updates


class WebSocketClient {
    constructor(userId) {
        this.userId = userId;
        this.ws = null;
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 3;
        this.reconnectDelay = 3000;
        this.listeners = {};
        this.pingInterval = null;
        this.enabled = Boolean(
            window.CONFIG &&
            window.CONFIG.WS_ENABLED === true &&
            window.CONFIG.WS_URL
        );

        // Check if WebSocket is supported
        if (!this.enabled) {
            console.log('ℹ️ WebSocket disabled: no production endpoint configured');
            return;
        }

        if (typeof WebSocket === 'undefined') {
            console.warn('⚠️ WebSocket not supported, disabling');
            this.enabled = false;
            return;
        }

        this.connect();
    }

    getWebSocketUrl() {

        if (
            typeof window.NAS_WS_URL === 'string' &&
            window.NAS_WS_URL.trim() !== ''
        ) {
            return window.NAS_WS_URL;
        }

        if (
            window.CONFIG &&
            window.CONFIG.WS_URL
        ) {
            return window.CONFIG.WS_URL;
        }

        const protocol =
            window.location.protocol === 'https:'
                ? 'wss:'
                : 'ws:';

        const host =
            window.location.host;

        let nasPath = '';

        if (window._basePath) {
            try {
                nasPath = new URL(
                    window._basePath,
                    window.location.origin
                ).pathname;
            } catch (error) {
                nasPath = window._basePath;
            }
        }

        nasPath =
            nasPath.replace(/\/+$/, '');

        return (
            protocol
            + '//'
            + host
            + nasPath
            + '/ws'
        );
    }

    connect() {
        if (!this.enabled) {
            console.log('ℹ️ WebSocket disabled');
            return;
        }

        if (!this.userId) {
            console.warn('⚠️ WebSocket: No user ID provided');
            return;
        }

        const wsUrl = this.getWebSocketUrl();
        console.log('🔌 Connecting to WebSocket:', wsUrl);

        try {
            this.ws = new WebSocket(wsUrl);

            this.ws.onopen = () => {
                this.isConnected = true;
                this.reconnectAttempts = 0;
                console.log('✅ WebSocket connected');
                this.emit('connected', { userId: this.userId });

                this.send({
                    type: 'auth',
                    user_id: this.userId
                });

                this.startPing();
                updateSyncStatus('connected');
            };

            this.ws.onmessage = (event) => {
                try {
                    const data = JSON.parse(event.data);
                    this.handleMessage(data);
                } catch (e) {
                    console.error('❌ WebSocket parse error:', e);
                }
            };

            this.ws.onclose = () => {
                this.isConnected = false;
                this.stopPing();
                console.log('🔌 WebSocket disconnected');
                this.emit('disconnected');
                updateSyncStatus('disconnected');
                this.reconnect();
            };

            this.ws.onerror = (error) => {
                console.warn('⚠️ WebSocket error:', error);
                this.emit('error', error);
                // Don't reconnect on error, onclose will handle it
            };

        } catch (e) {
            console.warn('⚠️ WebSocket connection error:', e.message);
            this.reconnect();
        }
    }

    send(data) {
        if (this.isConnected && this.ws && this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(JSON.stringify(data));
            return true;
        } else {
            console.warn('⚠️ WebSocket not connected, message not sent');
            return false;
        }
    }

    handleMessage(data) {
        console.log('📨 WebSocket message:', data);

        switch(data.type) {
            case 'auth_success':
                console.log('✅ WebSocket authenticated');
                this.emit('authenticated', data);
                break;

            case 'file_uploaded':
            case 'file_added':
                this.emit('file_added', data);
                if (typeof loadItems === 'function') {
                    loadItems(window._currentViewType || 'my-drive');
                }
                break;

            case 'file_deleted':
                this.emit('file_deleted', data);
                if (typeof loadItems === 'function') {
                    loadItems(window._currentViewType || 'my-drive');
                }
                break;

            case 'folder_created':
                this.emit('folder_created', data);
                if (typeof loadItems === 'function') {
                    loadItems(window._currentViewType || 'my-drive');
                }
                break;

            case 'file_updated':
                this.emit('file_updated', data);
                if (typeof loadItems === 'function') {
                    loadItems(window._currentViewType || 'my-drive');
                }
                break;

            case 'sync_progress':
                this.emit('sync_progress', data);
                break;

            case 'notification':
                this.emit('notification', data);
                if (typeof showToast === 'function') {
                    showToast(data.message || 'New notification', 'info');
                }
                break;

            case 'pong':
                // Heartbeat response
                break;

            default:
                console.log('📨 Unknown WebSocket message type:', data.type);
        }
    }

    on(event, callback) {
        if (!this.listeners[event]) {
            this.listeners[event] = [];
        }
        this.listeners[event].push(callback);
    }

    emit(event, data) {
        if (this.listeners[event]) {
            this.listeners[event].forEach(callback => {
                try {
                    callback(data);
                } catch (e) {
                    console.error('❌ WebSocket listener error:', e);
                }
            });
        }
    }

    reconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.log('❌ Max reconnect attempts reached');
            return;
        }

        this.reconnectAttempts++;
        const delay = this.reconnectDelay * this.reconnectAttempts;

        console.log(`🔄 Reconnecting in ${delay/1000}s... (Attempt ${this.reconnectAttempts})`);

        setTimeout(() => {
            if (!this.isConnected) {
                console.log(`🔄 Reconnecting... (Attempt ${this.reconnectAttempts})`);
                this.connect();
            }
        }, delay);
    }

    startPing() {
        this.stopPing();
        this.pingInterval = setInterval(() => {
            this.send({ type: 'ping', timestamp: Date.now() });
        }, 30000);
    }

    stopPing() {
        if (this.pingInterval) {
            clearInterval(this.pingInterval);
            this.pingInterval = null;
        }
    }

    disconnect() {
        this.stopPing();
        if (this.ws) {
            try {
                this.ws.close();
            } catch (e) {
                // Ignore
            }
            this.ws = null;
        }
        this.isConnected = false;
    }

    isAuthenticated() {
        return this.isConnected && this.userId;
    }
}

// Global WebSocket instance
let syncWs = null;

// Initialize WebSocket when user is logged in
function initWebSocket(userId) {
    if (!userId) {
        console.warn('⚠️ Cannot initialize WebSocket: No user ID');
        return;
    }

    if (
        !window.CONFIG ||
        window.CONFIG.WS_ENABLED !== true ||
        !window.CONFIG.WS_URL
    ) {
        console.log('ℹ️ WebSocket not configured; HTTP APIs remain active');
        updateSyncStatus('disconnected');
        return;
    }

    // Clean up existing connection
    if (syncWs) {
        try {
            syncWs.disconnect();
        } catch (e) {
            // Ignore
        }
        syncWs = null;
    }

    // Only create WebSocket if the browser supports it
    if (typeof WebSocket === 'undefined') {
        console.log('ℹ️ WebSocket not supported by browser');
        updateSyncStatus('disconnected');
        return;
    }

    try {
        syncWs = new WebSocketClient(userId);

        syncWs.on('authenticated', function(data) {
            console.log('✅ WebSocket authenticated for user:', data.user_id);
            updateSyncStatus('connected');
        });

        syncWs.on('disconnected', function() {
            console.log('🔌 WebSocket disconnected');
            updateSyncStatus('disconnected');
        });

        syncWs.on('notification', function(data) {
            console.log('🔔 Notification:', data);
            if (typeof showToast === 'function') {
                showToast(data.message || data.data?.message || 'New notification', 'info');
            }
        });
    } catch (e) {
        console.warn('⚠️ Failed to initialize WebSocket:', e.message);
        updateSyncStatus('disconnected');
    }
}

// Update sync status indicator
function updateSyncStatus(status) {
    const indicator = document.getElementById('syncStatusIndicator');
    if (!indicator) return;

    const statusMap = {
        'connected': { icon: 'fa-circle', color: '#34a853', text: 'Live' },
        'disconnected': { icon: 'fa-circle', color: '#ea4335', text: 'Offline' },
        'connecting': { icon: 'fa-spinner fa-spin', color: '#fbbc04', text: 'Connecting...' }
    };

    const s = statusMap[status] || statusMap['disconnected'];
    indicator.className = `sync-status ${status}`;
    indicator.innerHTML = `<i class="fas ${s.icon}" style="color:${s.color};"></i> ${s.text}`;
}

// WebSocket initialization is triggered by app.js after the dashboard is ready.

console.log('🔌 WebSocket client loaded');
