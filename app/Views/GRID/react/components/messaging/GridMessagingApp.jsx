import React, { useState, useEffect, useRef, useCallback } from 'react';
import { initialConversations } from '../../messagingState.js';
import gridApi from '../../gridApi.js';
import { useWebSocket } from '../../services/socket.js';
import ConversationList from './ConversationList.jsx';
import ActiveChatPane from './ActiveChatPane.jsx';
import VisitingCardPanel from './VisitingCardPanel.jsx';

/**
 * GridMessagingApp
 * Master orchestrator for the GRID Messaging Workspace (Circle Connect).
 * - 100% Real-Time Zero-Reload Communication using WebSockets
 *   (the gateway re-delivers to every one of the sender's own open sockets,
 *   so a single WebSocket connection already covers same-tab and multi-tab sync)
 * - MySQL database persistence via gridApi (/grid/api/messages/...)
 * - Zero hardcoded mock contacts
 */
export default function GridMessagingApp({ onNavigate, onToast, initialTargetUser = null }) {
    const [conversations, setConversations] = useState(initialConversations);
    const [activeId, setActiveId] = useState(null);
    const [activeTab, setActiveTab] = useState('focused');
    const [searchQuery, setSearchQuery] = useState('');
    const [isVisitingCardOpen, setIsVisitingCardOpen] = useState(false);
    const [mobileView, setMobileView] = useState('list'); // 'list' | 'chat'
    const [isLoadingLiveThreads, setIsLoadingLiveThreads] = useState(true);

    const currentUserId = (typeof window !== 'undefined' && window.__GRID_DATA__?.user?.id) || null;
    const activeIdRef = useRef(activeId);
    activeIdRef.current = activeId;
    const conversationsRef = useRef(conversations);
    conversationsRef.current = conversations;

    // ─── Real-Time Incoming Message Handler ──────────────────────────────────
    const handleIncomingRealtimeMessage = useCallback((msgData) => {
        if (!msgData || !msgData.conversation_id) return;
        const convId = `db_${msgData.conversation_id}`;
        const isMine = String(msgData.sender_id) === String(currentUserId);
        const currentActive = activeIdRef.current;

        const formattedTime = new Date(msgData.created_at || Date.now()).toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        });

        const newMsg = {
            id: `msg_${msgData.id || Date.now()}`,
            sender: isMine ? 'me' : 'them',
            senderName: isMine ? 'You' : (msgData.sender_name || 'Member'),
            avatar: isMine ? '' : (msgData.sender_avatar || ''),
            time: formattedTime,
            content: msgData.message || msgData.message_body || '',
            readStatus: isMine ? 'Sent' : 'Read',
            created_at: msgData.created_at || new Date().toISOString()
        };

        setConversations(prev => {
            const exists = prev.some(c => c.id === convId);
            if (!exists) {
                // Conversation not in current list yet; trigger full sync
                loadDatabaseConversations();
                return prev;
            }

            return prev.map(conv => {
                if (conv.id === convId) {
                    const currentMessages = conv.messages || [];

                    // Our own message, echoed back (WebSocket re-delivers to the sender's
                    // own sockets for multi-tab sync). Reconcile it against the unconfirmed
                    // optimistic entry this tab already added, matched deterministically by
                    // the msg_temp_ id prefix + identical content — NOT by timestamp, since
                    // client/server clocks and date-string parsing can't be relied on to
                    // agree within any fixed window.
                    if (isMine) {
                        const pendingIndex = currentMessages.findIndex(
                            m => m.id.startsWith('msg_temp_') && m.content === newMsg.content
                        );
                        if (pendingIndex !== -1) {
                            const messages = [...currentMessages];
                            messages[pendingIndex] = { ...messages[pendingIndex], id: newMsg.id };
                            return {
                                ...conv,
                                lastMessage: `You: ${newMsg.content}`,
                                lastTime: formattedTime,
                                messages
                            };
                        }
                    }

                    // Otherwise (a genuinely new message, or an already-reconciled echo),
                    // dedupe by exact message ID or identical recent content.
                    const isDuplicate = currentMessages.some(m =>
                        m.id === newMsg.id ||
                        (m.content === newMsg.content && Math.abs(new Date(m.created_at || 0) - new Date(msgData.created_at || 0)) < 2000)
                    );

                    if (isDuplicate) return conv;

                    const shouldIncrementUnread = !isMine && currentActive !== convId;

                    return {
                        ...conv,
                        lastMessage: isMine ? `You: ${newMsg.content}` : newMsg.content,
                        lastTime: formattedTime,
                        unreadCount: shouldIncrementUnread ? (conv.unreadCount || 0) + 1 : conv.unreadCount,
                        messages: [...currentMessages, newMsg]
                    };
                }
                return conv;
            });
        });

        // Toast notification if incoming message from another user while viewing a different thread
        if (!isMine && currentActive !== convId && onToast) {
            onToast(`New message from ${msgData.sender_name || 'peer'}: "${(msgData.message || '').slice(0, 32)}..."`);
        }
    }, [currentUserId, onToast]);

    // ─── WebSocket Hook (Port 8085) ──────────────────────────────────────────
    const handleWebSocketEvent = useCallback((event) => {
        if (event && event.type === 'NEW_MESSAGE' && event.data) {
            handleIncomingRealtimeMessage(event.data);
        }
    }, [handleIncomingRealtimeMessage]);

    const wsUrl = (typeof window !== 'undefined' && window.location?.hostname)
        ? `${window.location.protocol === 'https:' ? 'wss:' : 'ws:'}//${window.location.hostname}:8085`
        : 'ws://localhost:8085';

    useWebSocket(wsUrl, handleWebSocketEvent, currentUserId);

    // ─── Database Sync ──────────────────────────────────────────────────────
    const loadDatabaseConversations = async () => {
        setIsLoadingLiveThreads(true);
        try {
            const dbConvs = await gridApi.getConversations();
            if (Array.isArray(dbConvs)) {
                const formatted = dbConvs.map((c) => ({
                    id: `db_${c.conversation_id || c.id}`,
                    dbId: c.conversation_id || c.id,
                    name: c.opponent?.name || c.opponent_name || 'Member',
                    avatar: c.opponent?.avatar || c.opponent_avatar || '',
                    headline: c.opponent?.headline || c.opponent_headline || 'GRID Professional',
                    grid_id: c.opponent?.grid_id || c.opponent_grid_id || '',
                    opponent_user_id: c.opponent?.user_id || c.opponent_user_id,
                    verified: true,
                    connectionDegree: '1st',
                    lastMessage: c.last_message || 'Started a conversation',
                    lastTime: c.last_message_at
                        ? new Date(c.last_message_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                        : 'Recently',
                    unreadCount: c.unread_count || 0,
                    category: 'focused',
                    messages: []
                }));

                setConversations((prev) => {
                    return formatted.map((fc) => {
                        const existing = prev.find((p) => p.id === fc.id);
                        return existing && existing.messages && existing.messages.length > 0
                            ? { ...fc, messages: existing.messages }
                            : fc;
                    });
                });

                // Auto-select first conversation if none active, or reload active thread
                const currentActive = activeIdRef.current;
                if (!currentActive && formatted.length > 0) {
                    setActiveId(formatted[0].id);
                    loadThreadMessages(formatted[0].dbId, formatted[0].id);
                } else if (currentActive) {
                    const activeConv = formatted.find((c) => c.id === currentActive);
                    if (activeConv) {
                        loadThreadMessages(activeConv.dbId, activeConv.id);
                    }
                }
            }

            // Check URL query parameters for target user
            if (typeof window !== 'undefined') {
                const params = new URLSearchParams(window.location.search);
                const targetHandle = params.get('user') || params.get('startChat') || params.get('u');
                if (targetHandle) {
                    initiateDirectConversation(targetHandle);
                }
            }
        } catch (err) {
            console.warn('Could not sync live conversations:', err);
        } finally {
            setIsLoadingLiveThreads(false);
        }
    };

    useEffect(() => {
        loadDatabaseConversations();
    }, []);

    // Watch for target user transitions from profile pages
    useEffect(() => {
        if (initialTargetUser && initialTargetUser.id) {
            initiateDirectConversation(String(initialTargetUser.id));
        }
    }, [initialTargetUser]);

    const initiateDirectConversation = async (handleOrId) => {
        try {
            const cleanHandle = String(handleOrId).replace(/^@/, '');
            const res = await gridApi.startConversation(cleanHandle);
            if (res && res.conversation_id) {
                const convId = `db_${res.conversation_id}`;
                const opponentName = res.opponent?.name || 'Member';
                const opponentAvatar = res.opponent?.avatar || '';
                const opponentHeadline = res.opponent?.headline || 'GRID Professional';
                const opponentGridId = res.opponent?.grid_id || cleanHandle;
                const opponentUserId = res.opponent?.user_id || cleanHandle;

                const mappedMessages = (res.messages || []).map((m) => ({
                    id: `msg_${m.id}`,
                    sender: m.is_mine ? 'me' : 'them',
                    senderName: m.is_mine ? 'You' : opponentName,
                    avatar: m.is_mine ? '' : opponentAvatar,
                    time: new Date(m.created_at || Date.now()).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                    content: m.message || m.message_body || '',
                    readStatus: m.is_read ? 'Read' : 'Sent'
                }));

                const newConv = {
                    id: convId,
                    dbId: res.conversation_id,
                    name: opponentName,
                    avatar: opponentAvatar,
                    headline: opponentHeadline,
                    grid_id: opponentGridId,
                    opponent_user_id: opponentUserId,
                    verified: true,
                    connectionDegree: '1st',
                    lastMessage: mappedMessages.length > 0 ? mappedMessages[mappedMessages.length - 1].content : 'Conversation started',
                    lastTime: 'Just now',
                    unreadCount: 0,
                    category: 'focused',
                    messages: mappedMessages
                };

                setConversations((prev) => [newConv, ...prev.filter((c) => c.id !== convId)]);
                setActiveId(convId);
                setMobileView('chat');
                if (onToast) onToast(`Chat connected with ${opponentName} 💬`);
            }
        } catch (e) {
            console.warn('Failed to start conversation:', e);
        }
    };

    const loadThreadMessages = async (dbConvId, stateConvId) => {
        try {
            const threadData = await gridApi.getThread(dbConvId);
            if (threadData && Array.isArray(threadData.messages)) {
                const opponentName = threadData.opponent?.name || 'Member';
                const opponentAvatar = threadData.opponent?.avatar || '';

                const mappedMessages = threadData.messages.map((m) => ({
                    id: `msg_${m.id}`,
                    sender: m.is_mine ? 'me' : 'them',
                    senderName: m.is_mine ? 'You' : opponentName,
                    avatar: m.is_mine ? '' : opponentAvatar,
                    time: new Date(m.created_at || Date.now()).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                    content: m.message || m.message_body || '',
                    readStatus: m.is_read ? 'Read' : 'Sent'
                }));

                setConversations((prev) =>
                    prev.map((c) => (c.id === stateConvId ? { ...c, messages: mappedMessages } : c))
                );
            }
        } catch (e) {
            console.warn('Failed to load thread messages:', e);
        }
    };

    const activeConversation = conversations.find((c) => c.id === activeId) || null;

    const handleSelectConversation = (id) => {
        setActiveId(id);
        setMobileView('chat');

        const selected = conversations.find((c) => c.id === id);
        if (selected?.dbId) {
            loadThreadMessages(selected.dbId, id);
        }

        // Clear unread count on open
        setConversations((prev) =>
            prev.map((conv) => (conv.id === id && conv.unreadCount > 0 ? { ...conv, unreadCount: 0 } : conv))
        );
    };

    const handleSendMessage = async (convId, text) => {
        const now = new Date();
        const formattedTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        const targetConv = conversations.find((c) => c.id === convId);
        const recipientId = targetConv?.opponent_user_id;

        const optimisticId = `msg_temp_${Date.now()}`;
        const newMessage = {
            id: optimisticId,
            sender: 'me',
            senderName: 'You',
            time: formattedTime,
            content: text,
            readStatus: 'Sent',
            created_at: now.toISOString()
        };

        // 1. Optimistic local state update (Zero-Reload)
        setConversations((prev) =>
            prev.map((conv) => {
                if (conv.id === convId) {
                    return {
                        ...conv,
                        lastMessage: `You: ${text}`,
                        lastTime: formattedTime,
                        messages: [...(conv.messages || []), newMessage]
                    };
                }
                return conv;
            })
        );

        // 2. Persist to MySQL database & trigger WebSocket broadcast via PHP API
        if (recipientId) {
            try {
                const res = await gridApi.sendMessage(
                    recipientId,
                    text,
                    targetConv.grid_id || null
                );
                if (res && res.success && res.data) {
                    // Update optimistic message with real database ID
                    setConversations((prev) =>
                        prev.map((conv) => {
                            if (conv.id === convId) {
                                return {
                                    ...conv,
                                    messages: (conv.messages || []).map((m) =>
                                        m.id === optimisticId ? { ...m, id: `msg_${res.data.id}` } : m
                                    )
                                };
                            }
                            return conv;
                        })
                    );
                }
            } catch (err) {
                console.warn('Failed to persist message to database:', err);
            }
        }
    };

    const handleComposeNew = () => {
        const target = window.prompt('Enter universal @grid_id or user ID to message:');
        if (target && target.trim()) {
            initiateDirectConversation(target.trim());
        }
    };

    return (
        <main className="g-messaging-stage flex-1 max-w-[1920px] w-full mx-auto px-0 sm:px-4 lg:px-6 py-0 sm:py-3 flex min-h-0 h-full overflow-hidden">
            {/* Main Messaging Container: 2 or 3 Panels */}
            <div className="g-messaging-workspace flex-1 flex rounded-none sm:rounded-2xl border-0 sm:border border-slate-200/80 dark:border-slate-800 shadow-subtle overflow-hidden min-h-0 h-full">
                {/* 1. Left: Conversation List Column */}
                <ConversationList
                    conversations={conversations}
                    activeId={activeId}
                    onSelectConversation={handleSelectConversation}
                    activeTab={activeTab}
                    onSelectTab={setActiveTab}
                    searchQuery={searchQuery}
                    onSearchChange={setSearchQuery}
                    onComposeNew={handleComposeNew}
                    className={mobileView === 'list' ? 'flex flex-col' : 'hidden md:flex md:flex-col'}
                />

                {/* 2. Center: Active Chat Stream & Rich Composer */}
                <ActiveChatPane
                    conversation={activeConversation}
                    currentUser={{ fullName: 'You' }}
                    onSendMessage={handleSendMessage}
                    isVisitingCardOpen={isVisitingCardOpen}
                    onToggleVisitingCard={() => setIsVisitingCardOpen(!isVisitingCardOpen)}
                    onBackToList={() => setMobileView('list')}
                    className={mobileView === 'chat' ? 'flex flex-col' : 'hidden md:flex md:flex-col'}
                />

                {/* 3. Right: Visiting Card & Permission Inspector */}
                {activeConversation && (
                    <VisitingCardPanel
                        conversation={activeConversation}
                        isOpen={isVisitingCardOpen}
                        onClose={() => setIsVisitingCardOpen(false)}
                    />
                )}
            </div>
        </main>
    );
}
