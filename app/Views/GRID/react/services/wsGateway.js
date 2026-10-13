/**
 * GRID Real-Time WebSocket Gateway Daemon
 * Port: 8085
 * 
 * Provides zero-reload bi-directional communication between GRID profiles:
 * 1. WebSocket endpoint for React frontend clients (ws://localhost:8085)
 * 2. HTTP POST endpoint for PHP backend (/broadcast)
 * 3. Connection registry mapping userIds (e.g. 356, 357) to active sockets
 */

const http = require('http');
const { WebSocketServer, WebSocket } = require('ws');

const PORT = process.env.GRID_WS_PORT || 8085;

// Create unified HTTP server
const server = http.createServer((req, res) => {
    // Set CORS headers
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    if (req.method === 'OPTIONS') {
        res.writeHead(200);
        res.end();
        return;
    }

    // Health check endpoint
    if (req.method === 'GET' && req.url === '/status') {
        let totalSockets = 0;
        for (const sockets of userSockets.values()) {
            totalSockets += sockets.size;
        }
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({
            status: 'online',
            connectedUsers: Array.from(userSockets.keys()),
            totalSockets,
            timestamp: new Date().toISOString()
        }));
        return;
    }

    // HTTP Broadcast endpoint for PHP GridApiController
    if (req.method === 'POST' && req.url === '/broadcast') {
        let body = '';
        req.on('data', chunk => { body += chunk; });
        req.on('end', () => {
            try {
                const payload = JSON.parse(body || '{}');
                const delivered = broadcastPayload(payload);
                res.writeHead(200, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ ok: true, delivered }));
            } catch (err) {
                res.writeHead(400, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ ok: false, error: err.message }));
            }
        });
        return;
    }

    res.writeHead(404);
    res.end();
});

// Create WebSocket server mounted on HTTP server
const wss = new WebSocketServer({ server });

// Map: userId (string) -> Set<WebSocket>
const userSockets = new Map();

function registerUserSocket(userId, ws) {
    if (!userId) return;
    const key = String(userId);
    if (!userSockets.has(key)) {
        userSockets.set(key, new Set());
    }
    userSockets.get(key).add(ws);
    ws.userId = key;
    console.log(`[WebSocket] User ${key} connected (total sockets for user: ${userSockets.get(key).size})`);
}

function unregisterUserSocket(ws) {
    if (!ws.userId) return;
    const key = String(ws.userId);
    if (userSockets.has(key)) {
        userSockets.get(key).delete(ws);
        if (userSockets.get(key).size === 0) {
            userSockets.delete(key);
        }
    }
    console.log(`[WebSocket] User ${key} disconnected`);
}

function broadcastPayload(payload) {
    const data = payload.data || {};
    const recipientId = data.recipient_id ? String(data.recipient_id) : null;
    const senderId = data.sender_id ? String(data.sender_id) : null;
    const jsonStr = JSON.stringify(payload);
    let delivered = 0;

    // Send to recipient's open sockets
    if (recipientId && userSockets.has(recipientId)) {
        for (const ws of userSockets.get(recipientId)) {
            if (ws.readyState === WebSocket.OPEN) {
                ws.send(jsonStr);
                delivered++;
            }
        }
    }

    // Send to sender's other open sockets (e.g. other tabs/windows)
    if (senderId && userSockets.has(senderId)) {
        for (const ws of userSockets.get(senderId)) {
            if (ws.readyState === WebSocket.OPEN) {
                ws.send(jsonStr);
                delivered++;
            }
        }
    }

    // Fallback broadcast to all connected sockets if recipient not targeted
    if (!recipientId && !senderId) {
        for (const client of wss.clients) {
            if (client.readyState === WebSocket.OPEN) {
                client.send(jsonStr);
                delivered++;
            }
        }
    }

    console.log(`[WebSocket Broadcast] Event ${payload.type || 'UNKNOWN'} delivered to ${delivered} socket(s)`);
    return delivered;
}

wss.on('connection', (ws, req) => {
    ws.isAlive = true;
    ws.on('pong', () => { ws.isAlive = true; });

    // Check query parameter for initial auth e.g. ws://localhost:8085?userId=356
    try {
        const url = new URL(req.url, `http://${req.headers.host || 'localhost'}`);
        const qUserId = url.searchParams.get('userId');
        if (qUserId) {
            registerUserSocket(qUserId, ws);
        }
    } catch (e) {}

    ws.on('message', (raw) => {
        try {
            const msg = JSON.parse(raw.toString());

            // 1. Handshake Auth
            if (msg.type === 'AUTH' && msg.userId) {
                registerUserSocket(msg.userId, ws);
                ws.send(JSON.stringify({
                    type: 'AUTH_OK',
                    userId: String(msg.userId)
                }));
                return;
            }

            // 2. Direct message forwarding
            if (msg.type === 'SEND_MESSAGE' && msg.data) {
                broadcastPayload({
                    type: 'NEW_MESSAGE',
                    data: msg.data
                });
                return;
            }

            // 3. Heartbeat
            if (msg.type === 'PING') {
                ws.send(JSON.stringify({ type: 'PONG' }));
                return;
            }
        } catch (err) {
            console.warn('[WebSocket] Invalid client message:', raw.toString());
        }
    });

    ws.on('close', () => {
        unregisterUserSocket(ws);
    });

    ws.on('error', (err) => {
        console.warn('[WebSocket] Socket error:', err.message);
        unregisterUserSocket(ws);
    });
});

// Periodic heartbeat sweep
const interval = setInterval(() => {
    for (const ws of wss.clients) {
        if (ws.isAlive === false) {
            unregisterUserSocket(ws);
            return ws.terminate();
        }
        ws.isAlive = false;
        ws.ping();
    }
}, 30000);

wss.on('close', () => {
    clearInterval(interval);
});

server.listen(PORT, '0.0.0.0', () => {
    console.log(`[GRID WebSocket Gateway] Running on ws://0.0.0.0:${PORT} & http://0.0.0.0:${PORT}/broadcast`);
});
