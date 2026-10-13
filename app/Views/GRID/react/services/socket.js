/**
 * Lightweight React WebSocket Real-Time Client Hook
 * 
 * Automatically connects to local/cloud WebSocket endpoint,
 * maintains auto-reconnect, and dispatches real-time events.
 */
import { useEffect, useRef, useState } from 'react';

export function useWebSocket(url = null, onEvent, userId = null) {
    const [isConnected, setIsConnected] = useState(false);
    const socketRef = useRef(null);
    const reconnectTimerRef = useRef(null);
    const onEventRef = useRef(onEvent);
    onEventRef.current = onEvent;

    useEffect(() => {
        if (!url) return;
        let isMounted = true;

        function connect() {
            try {
                // Attach userId in query params if available
                let connectUrl = url;
                if (userId) {
                    const sep = connectUrl.includes('?') ? '&' : '?';
                    connectUrl = `${connectUrl}${sep}userId=${encodeURIComponent(userId)}`;
                }

                const ws = new WebSocket(connectUrl);
                socketRef.current = ws;

                ws.onopen = () => {
                    if (!isMounted) return;
                    setIsConnected(true);

                    // Handshake auth with gateway
                    if (userId && ws.readyState === WebSocket.OPEN) {
                        ws.send(JSON.stringify({ type: 'AUTH', userId: String(userId) }));
                    }
                };

                ws.onmessage = (event) => {
                    try {
                        const message = JSON.parse(event.data);
                        if (onEventRef.current) onEventRef.current(message);
                    } catch (e) {
                        console.warn('[WebSocket] Non-JSON payload received:', event.data);
                    }
                };

                ws.onclose = () => {
                    if (!isMounted) return;
                    setIsConnected(false);
                    // Silently attempt reconnect in 3s
                    reconnectTimerRef.current = setTimeout(connect, 3000);
                };

                ws.onerror = () => {
                    ws.close();
                };
            } catch (err) {
                // Silent fallback
            }
        }

        connect();

        return () => {
            isMounted = false;
            if (reconnectTimerRef.current) clearTimeout(reconnectTimerRef.current);
            if (socketRef.current) socketRef.current.close();
        };
    }, [url, userId]);

    function sendMessage(type, payload) {
        if (socketRef.current && socketRef.current.readyState === WebSocket.OPEN) {
            socketRef.current.send(JSON.stringify({ type, payload }));
        }
    }

    return { isConnected, sendMessage };
}
