import { useEffect, useRef, useState, useMemo } from "react";
import Message from "./Message";
import Composer from "./Composer";
import ConversationAttachmentLayer from "./ConversationAttachmentLayer";
import ConversationSearch from "./ConversationSearch";
import MessageReactionBar from "./MessageReactionBar";
import MessageContextMenu from "./MessageContextMenu";
import DeleteMessageModal from "./DeleteMessageModal";
import { getAvatarUrl } from "../../utils/avatar";
import { DELETED_MESSAGE_TEXT } from "../../utils/chatAdapters";
import "./Conversation.css";

function Conversation({
  selectedChat,
  messages = [],
  onSendMessage,
  onToast,
  onBack,
  onStartCall,
  onOpenContactInfo,
  onOpenMediaDocs,
  onOpenMute,
  onOpenClearChat,
  onOpenDeleteChat,
  onToggleFavourite,
  isFavourite = false,
  isMuted = false,
  onCreateEvent,
  onOpenEventDetail,
  contacts = [],
  onOpenContactChat,
  onDeleteMessages,
  onReactMessage,
  onStarMessages,
  onEditMessage,
  onVotePoll,
}) {
  const [moreOpen, setMoreOpen] = useState(false);
  // Owns the active conversation's attachment UI: null | "menu" | "contact" | "poll" | "camera"
  const [attachmentAction, setAttachmentAction] = useState(null);
  const [attachmentChatId, setAttachmentChatId] = useState(selectedChat?.id);
  // In-conversation message search — local/frontend only, no backend involved.
  const [isSearchOpen, setIsSearchOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState("");
  const [highlightedMessageId, setHighlightedMessageId] = useState(null);

  // Message Actions & Reactions State
  const [activeActionMessage, setActiveActionMessage] = useState(null); // { message, anchorRect }
  const [isSelectionMode, setIsSelectionMode] = useState(false);
  const [selectedMessageIds, setSelectedMessageIds] = useState([]);
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [replyingTo, setReplyingTo] = useState(null);
  const [editingMessage, setEditingMessage] = useState(null);
  const [localMessageOverrides, setLocalMessageOverrides] = useState({});

  const messagesRef = useRef(null);
  const highlightTimeoutRef = useRef(null);

  useEffect(() => {
    if (messagesRef.current) {
      messagesRef.current.scrollTop = messagesRef.current.scrollHeight;
    }
  }, [messages, selectedChat?.id]);

  useEffect(() => {
    return () => clearTimeout(highlightTimeoutRef.current);
  }, []);

  useEffect(() => {
    const handleKeyDown = (event) => {
      if (event.key === "Escape") {
        if (showDeleteModal) {
          setShowDeleteModal(false);
        } else if (activeActionMessage) {
          setActiveActionMessage(null);
        } else if (isSelectionMode) {
          setIsSelectionMode(false);
          setSelectedMessageIds([]);
        } else if (replyingTo) {
          setReplyingTo(null);
        } else if (editingMessage) {
          setEditingMessage(null);
        } else if (isSearchOpen) {
          setIsSearchOpen(false);
        }
      }
    };

    document.addEventListener("keydown", handleKeyDown);
    return () => document.removeEventListener("keydown", handleKeyDown);
  }, [showDeleteModal, activeActionMessage, isSelectionMode, replyingTo, editingMessage, isSearchOpen]);

  // Attachment UI, selection mode, and search belong to the active conversation — switching chats always
  // closes them.
  if (selectedChat?.id !== attachmentChatId) {
    setAttachmentChatId(selectedChat?.id);
    setAttachmentAction(null);
    setIsSearchOpen(false);
    setSearchQuery("");
    setActiveActionMessage(null);
    setIsSelectionMode(false);
    setSelectedMessageIds([]);
    setShowDeleteModal(false);
    setReplyingTo(null);
    setEditingMessage(null);
    setLocalMessageOverrides({});
  }

  const handleJumpToMessage = (messageId) => {
    setIsSearchOpen(false);

    requestAnimationFrame(() => {
      const el = document.getElementById(`message-${messageId}`);
      el?.scrollIntoView({ behavior: "smooth", block: "center" });
    });

    setHighlightedMessageId(messageId);
    clearTimeout(highlightTimeoutRef.current);
    highlightTimeoutRef.current = setTimeout(() => setHighlightedMessageId(null), 1800);
  };

  const addMessage = (textOrData) => {
    if (typeof textOrData === "object" && textOrData !== null) {
      if (onSendMessage) {
        onSendMessage(textOrData);
      }
      return;
    }

    const cleanText = (textOrData || "").trim();
    if (!cleanText) {
      return;
    }

    if (onSendMessage) {
      onSendMessage(cleanText);
    }
  };

  const handleMoreAction = (action) => {
    setMoreOpen(false);

    if (onToast) {
      onToast(action);
    }
  };

  const avatar = selectedChat?.avatar || "";
  const name = selectedChat?.name || "Unknown";
  const status = selectedChat?.conversationId ? selectedChat.status : selectedChat?.status || "online";

  const rawMessages =
    (messages && messages.length > 0) || selectedChat?.conversationId
      ? messages
      : selectedChat
      ? [
          {
            id: selectedChat.id || 1,
            type: "incoming",
            text:
              selectedChat.text ||
              `Start a new conversation with ${name}.`,
            time: selectedChat.time || "Just now",
            avatar: avatar,
            more: true,
          },
        ]
      : [];

  const displayMessages = rawMessages
    .filter((m) => !localMessageOverrides[m.id]?.isDeletedForMe)
    .map((m) => {
      const override = localMessageOverrides[m.id];
      return override ? { ...m, ...override } : m;
    });

  // Open Message Actions
  const handleOpenMessageActions = (msg, rect) => {
    if (isSelectionMode || msg.isDeleted) return;
    setActiveActionMessage({ message: msg, anchorRect: rect });
  };

  const handleCloseActions = () => {
    setActiveActionMessage(null);
  };

  // Toggle or add reaction to a message
  const handleToggleReaction = (messageId, emoji) => {
    setLocalMessageOverrides((prev) => {
      const existingMsg = displayMessages.find((m) => m.id === messageId);
      const currentReactions = existingMsg?.reactions ? [...existingMsg.reactions] : [];
      const reactionIdx = currentReactions.findIndex((r) => r.emoji === emoji);

      if (reactionIdx >= 0) {
        const item = currentReactions[reactionIdx];
        if (item.userReacted) {
          if (item.count <= 1) {
            currentReactions.splice(reactionIdx, 1);
          } else {
            currentReactions[reactionIdx] = { ...item, count: item.count - 1, userReacted: false };
          }
        } else {
          currentReactions[reactionIdx] = { ...item, count: item.count + 1, userReacted: true };
        }
      } else {
        currentReactions.push({ emoji, count: 1, userReacted: true });
      }

      return {
        ...prev,
        [messageId]: { ...(prev[messageId] || {}), reactions: currentReactions },
      };
    });

    onReactMessage?.(messageId, emoji);
  };

  const handleSelectReaction = (emoji) => {
    if (!activeActionMessage) return;
    handleToggleReaction(activeActionMessage.message.id, emoji);
    handleCloseActions();
  };

  // Menu action handlers
  const handleMenuReply = () => {
    if (!activeActionMessage) return;
    setReplyingTo(activeActionMessage.message);
    handleCloseActions();
  };

  const handleMenuCopy = () => {
    if (!activeActionMessage) return;
    const msg = activeActionMessage.message;
    const textToCopy = msg.text || msg.fileName || msg.pollData?.question || "Message";
    navigator.clipboard?.writeText?.(textToCopy);
    onToast?.("Message copied to clipboard! 📋");
    handleCloseActions();
  };

  const handleMenuReact = () => {
    // Keeps reaction bar open/focused
  };

  const handleMenuForward = () => {
    if (!activeActionMessage) return;
    onToast?.("Select a chat or contact to forward to ↗️");
    handleCloseActions();
  };

  const handleMenuToggleStar = () => {
    if (!activeActionMessage) return;
    const msgId = activeActionMessage.message.id;
    const currentStarred = activeActionMessage.message.isStarred;
    setLocalMessageOverrides((prev) => ({
      ...prev,
      [msgId]: { ...(prev[msgId] || {}), isStarred: !currentStarred },
    }));
    onStarMessages?.([msgId]);
    onToast?.(!currentStarred ? "Message starred ⭐" : "Message unstarred");
    handleCloseActions();
  };

  const handleMenuTogglePin = () => {
    if (!activeActionMessage) return;
    const msgId = activeActionMessage.message.id;
    const currentPinned = activeActionMessage.message.isPinned;
    setLocalMessageOverrides((prev) => ({
      ...prev,
      [msgId]: { ...(prev[msgId] || {}), isPinned: !currentPinned },
    }));
    onToast?.(!currentPinned ? "Message pinned 📌" : "Message unpinned");
    handleCloseActions();
  };

  const handleMenuEdit = () => {
    if (!activeActionMessage) return;
    setEditingMessage(activeActionMessage.message);
    handleCloseActions();
  };

  const handleMenuOpenInfo = () => {
    if (!activeActionMessage) return;
    const msg = activeActionMessage.message;
    const timeStr = msg.time || "Just now";
    const typeStr = msg.messageType || "Text message";
    onToast?.(`ℹ️ Sent: ${timeStr} • Type: ${typeStr}`);
    handleCloseActions();
  };

  // Enter selection mode on clicking Delete in menu
  const handleMenuDelete = () => {
    if (!activeActionMessage) return;
    const msgId = activeActionMessage.message.id;
    setIsSelectionMode(true);
    setSelectedMessageIds([msgId]);
    handleCloseActions();
  };

  // Selection mode handlers
  const handleToggleSelectMessage = (messageId) => {
    setSelectedMessageIds((prev) => {
      if (prev.includes(messageId)) {
        const next = prev.filter((id) => id !== messageId);
        if (next.length === 0) {
          setIsSelectionMode(false);
        }
        return next;
      } else {
        return [...prev, messageId];
      }
    });
  };

  const handleExitSelectionMode = () => {
    setIsSelectionMode(false);
    setSelectedMessageIds([]);
  };

  const handleStarSelected = () => {
    if (!selectedMessageIds.length) return;
    setLocalMessageOverrides((prev) => {
      const next = { ...prev };
      selectedMessageIds.forEach((id) => {
        next[id] = { ...(next[id] || {}), isStarred: true };
      });
      return next;
    });
    onStarMessages?.(selectedMessageIds);
    onToast?.(`${selectedMessageIds.length} message(s) starred ⭐`);
    handleExitSelectionMode();
  };

  const handleCopySelected = () => {
    if (!selectedMessageIds.length) return;
    const textPieces = selectedMessageIds
      .map((id) => displayMessages.find((m) => m.id === id))
      .filter(Boolean)
      .map((m) => m.text || m.fileName || "")
      .filter(Boolean);

    navigator.clipboard?.writeText?.(textPieces.join("\n"));
    onToast?.(`${selectedMessageIds.length} message(s) copied! 📋`);
    handleExitSelectionMode();
  };

  // Delete modal confirmations
  const handleConfirmDeleteForEveryone = () => {
    setLocalMessageOverrides((prev) => {
      const next = { ...prev };
      selectedMessageIds.forEach((id) => {
        next[id] = {
          ...(next[id] || {}),
          isDeleted: true,
          text: DELETED_MESSAGE_TEXT,
          reactions: [],
        };
      });
      return next;
    });

    onDeleteMessages?.(selectedMessageIds, "everyone");
    onToast?.("Message deleted for everyone 🗑️");
    setShowDeleteModal(false);
    handleExitSelectionMode();
  };

  const handleConfirmDeleteForMe = () => {
    setLocalMessageOverrides((prev) => {
      const next = { ...prev };
      selectedMessageIds.forEach((id) => {
        next[id] = { ...(next[id] || {}), isDeletedForMe: true };
      });
      return next;
    });

    onDeleteMessages?.(selectedMessageIds, "me");
    onToast?.("Message deleted for you 🗑️");
    setShowDeleteModal(false);
    handleExitSelectionMode();
  };

  const handleEditMessage = (messageId, newText) => {
    setLocalMessageOverrides((prev) => ({
      ...prev,
      [messageId]: { ...(prev[messageId] || {}), text: newText },
    }));
    onEditMessage?.(messageId, newText);
    onToast?.("Message updated ✨");
    setEditingMessage(null);
  };

  // Floating Position Calculator
  const calculatedPositions = useMemo(() => {
    if (!activeActionMessage?.anchorRect) {
      return {
        reactionBarStyle: { position: "fixed", top: "100px", left: "20px", zIndex: 1052 },
        contextMenuStyle: { position: "fixed", top: "160px", left: "20px", zIndex: 1051 },
      };
    }

    const rect = activeActionMessage.anchorRect;
    const isIncoming = activeActionMessage.message?.type === "incoming";
    const vw = typeof window !== "undefined" ? window.innerWidth : 1000;
    const vh = typeof window !== "undefined" ? window.innerHeight : 800;

    const barWidth = Math.min(320, vw - 24);
    const barHeight = 48;
    const menuWidth = Math.min(205, vw - 24);
    const menuHeight = 340;
    const gap = 8;

    // Viewport bounds: below header (~68px) and above composer (~vh - 75px)
    const minTop = 72;
    const maxBottom = vh - 75;

    let reactionBarTop;
    let contextMenuTop;

    // Condition 1: Can reaction bar fit above AND context menu fit below? (Middle message)
    const hasRoomAbove = (rect.top - barHeight - gap) >= minTop;
    const hasRoomBelow = (rect.bottom + gap + menuHeight) <= maxBottom;

    if (hasRoomAbove && hasRoomBelow) {
      // Middle message: Reaction bar above message, context menu below message
      reactionBarTop = rect.top - barHeight - gap;
      contextMenuTop = rect.bottom + gap;
    } else if (!hasRoomAbove) {
      // Top message: Cannot place reaction bar above. Both go below message, stacked together!
      reactionBarTop = Math.max(minTop, rect.bottom + gap);
      contextMenuTop = reactionBarTop + barHeight + gap;
    } else {
      // Bottom message: Cannot place context menu below. Both go above message, stacked together!
      contextMenuTop = Math.max(minTop + barHeight + gap, rect.top - menuHeight - gap);
      reactionBarTop = contextMenuTop - barHeight - gap;
      if (reactionBarTop < minTop) {
        reactionBarTop = minTop;
        contextMenuTop = reactionBarTop + barHeight + gap;
      }
    }

    // Horizontal placement:
    let reactionBarLeft;
    let contextMenuLeft;

    if (vw <= 600) {
      reactionBarLeft = Math.max(12, (vw - barWidth) / 2);
      contextMenuLeft = isIncoming
        ? Math.max(12, Math.min(rect.left, vw - menuWidth - 12))
        : Math.max(12, Math.min(rect.right - menuWidth, vw - menuWidth - 12));
    } else {
      if (isIncoming) {
        contextMenuLeft = Math.max(16, Math.min(rect.left, vw - menuWidth - 16));
        reactionBarLeft = Math.max(16, Math.min(rect.left, vw - barWidth - 16));
      } else {
        contextMenuLeft = Math.max(16, Math.min(rect.right - menuWidth, vw - menuWidth - 16));
        // Align right edge of reaction bar with right edge of context menu / message
        reactionBarLeft = Math.max(16, Math.min(contextMenuLeft + menuWidth - barWidth, vw - barWidth - 16));
      }
    }

    const availableMenuHeight = Math.max(160, maxBottom - contextMenuTop);

    return {
      reactionBarStyle: {
        position: "fixed",
        top: `${reactionBarTop}px`,
        left: `${reactionBarLeft}px`,
        zIndex: 1052,
      },
      contextMenuStyle: {
        position: "fixed",
        top: `${contextMenuTop}px`,
        left: `${contextMenuLeft}px`,
        maxHeight: `${availableMenuHeight}px`,
        overflowY: "auto",
        zIndex: 1051,
      },
    };
  }, [activeActionMessage]);

  const canDeleteForEveryone = useMemo(() => {
    if (!selectedMessageIds.length) return false;
    return selectedMessageIds.some((id) => {
      const msg = displayMessages.find((m) => m.id === id);
      return msg && msg.type === "outgoing" && !msg.isDeleted;
    });
  }, [selectedMessageIds, displayMessages]);

  if (!selectedChat) {
    return (
      <section className="conversation conversation--empty">
        <div className="conversation-empty-state">
          <i className="fa-regular fa-comments"></i>
          <h2>Select a chat</h2>
          <p>Choose a conversation from the list or tap + to start a new one.</p>
        </div>
      </section>
    );
  }

  return (
    <section className="conversation">

      {/* Conversation Header / Selection Toolbar */}
      {isSelectionMode ? (
        <header className="conversation-header conversation-selection-header">
          <div className="selection-header-left">
            <button
              type="button"
              className="selection-close-btn"
              onClick={handleExitSelectionMode}
              title="Close selection"
              aria-label="Close selection"
            >
              <i className="fa-solid fa-xmark"></i>
            </button>
            <span className="selection-count-text">
              {selectedMessageIds.length} selected
            </span>
          </div>

          <div className="selection-header-actions">
            <button
              type="button"
              className="selection-action-btn"
              onClick={handleStarSelected}
              title="Star messages"
              aria-label="Star messages"
            >
              <i className="fa-regular fa-star"></i>
            </button>

            <button
              type="button"
              className="selection-action-btn"
              onClick={handleCopySelected}
              title="Copy text"
              aria-label="Copy text"
            >
              <i className="fa-regular fa-copy"></i>
            </button>

            <button
              type="button"
              className="selection-action-btn is-delete"
              onClick={() => setShowDeleteModal(true)}
              title="Delete messages"
              aria-label="Delete messages"
            >
              <i className="fa-regular fa-trash-can"></i>
            </button>
          </div>
        </header>
      ) : (
        <header className="conversation-header">

        <div className="conversation-header-left">
          {onBack && (
            <button
              className="mobile-back-btn"
              onClick={onBack}
              title="Back to Chats"
              aria-label="Back to Chats"
            >
              <i className="fa-solid fa-arrow-left"></i>
            </button>
          )}

          <div
            className="conversation-person"
            onClick={onOpenContactInfo}
            title={selectedChat.isGroup ? "Group info" : "View contact info"}
          >
            <img
              src={getAvatarUrl(avatar, 120, name)}
              alt={name}
            />

            <div className="person-details">
              <h2>
                <span className="conversation-person-name-text">{name}</span>
                {isMuted && (
                  <i
                    className="fa-solid fa-bell-slash conversation-muted-icon"
                    title="Notifications muted"
                  ></i>
                )}
              </h2>
              <span className={status?.toLowerCase().includes("online") || status === "writes" ? "status-online" : "status-offline"}>{status}</span>
            </div>
          </div>
        </div>

        <div className="conversation-actions">
          <button
            className="header-action-btn"
            title={`Video call with ${name}`}
            aria-label={`Video call with ${name}`}
            onClick={() => onStartCall?.({ ...selectedChat, name, avatar }, "video")}
          >
            <i className="fa-solid fa-video"></i>
          </button>

          <button
            className="header-action-btn"
            title={`Audio call with ${name}`}
            aria-label={`Audio call with ${name}`}
            onClick={() => onStartCall?.({ ...selectedChat, name, avatar }, "audio")}
          >
            <i className="fa-solid fa-phone"></i>
          </button>

          <button
            className="header-action-btn"
            title="Search messages"
            aria-label="Search messages"
            onClick={() => {
              setMoreOpen(false);
              setAttachmentAction(null);
              setIsSearchOpen(true);
            }}
          >
            <i className="fa-solid fa-magnifying-glass"></i>
          </button>

          <button
            className="header-action-btn"
            title="More options"
            aria-label="More options"
            onClick={(event) => {
              event.stopPropagation();
              setMoreOpen((previous) => !previous);
            }}
          >
            <i className="fa-solid fa-ellipsis-vertical"></i>
          </button>
        </div>

        {/* More Menu */}
        {moreOpen && (
          <div
            className="more-menu open"
            onClick={(event) => event.stopPropagation()}
          >
            <button
              onClick={() => {
                setMoreOpen(false);
                if (onOpenContactInfo) onOpenContactInfo();
              }}
            >
              {selectedChat.isGroup ? "Group info" : "View contact"}
            </button>

            <button
              onClick={() => {
                setMoreOpen(false);
                if (onOpenMediaDocs) {
                  onOpenMediaDocs();
                } else {
                  handleMoreAction("Media, links, and docs");
                }
              }}
            >
              Media, links, and docs
            </button>

            <button
              onClick={() => {
                setMoreOpen(false);
                onOpenMute?.();
              }}
            >
              {isMuted ? "Unmute notifications" : "Mute notifications"}
            </button>

            {onToggleFavourite && (
              <button
                onClick={() => {
                  setMoreOpen(false);
                  onToggleFavourite();
                }}
              >
                {isFavourite ? "Remove from favourites" : "Add to favourites"}
              </button>
            )}

            <button
              onClick={() => {
                setMoreOpen(false);
                onOpenClearChat?.();
              }}
            >
              Clear chat
            </button>

            {onOpenDeleteChat && (
              <button
                onClick={() => {
                  setMoreOpen(false);
                  onOpenDeleteChat();
                }}
              >
                Delete chat
              </button>
            )}
          </div>
        )}

      </header>
      )}

      {/* Messages */}
      <div
        className="messages"
        ref={messagesRef}
        onClick={() => {
          setMoreOpen(false);
          handleCloseActions();
        }}
      >
        {displayMessages.map((message) => (
          <Message
            key={message.id}
            message={message}
            avatar={avatar}
            chatName={name}
            isGroup={Boolean(selectedChat.isGroup)}
            onOpenEventDetail={onOpenEventDetail}
            onOpenContactChat={onOpenContactChat}
            highlighted={message.id === highlightedMessageId}
            isSelectionMode={isSelectionMode}
            isSelected={selectedMessageIds.includes(message.id)}
            onToggleSelect={handleToggleSelectMessage}
            onOpenActions={handleOpenMessageActions}
            onToggleReaction={handleToggleReaction}
            onVotePoll={onVotePoll}
            // Tapping a call bubble calls the same person back
            onStartCall={(type) => onStartCall?.({ ...selectedChat, name, avatar }, type)}
          />
        ))}
      </div>

      {/* Conversation-scoped message search (never viewport-level) */}
      <ConversationSearch
        isOpen={isSearchOpen}
        query={searchQuery}
        onQueryChange={setSearchQuery}
        onClose={() => setIsSearchOpen(false)}
        messages={displayMessages}
        contactName={name}
        onJumpToMessage={handleJumpToMessage}
      />

      {/* Composer */}
      <Composer
        onSend={addMessage}
        onToast={onToast}
        selectedChat={selectedChat}
        onCreateEvent={onCreateEvent}
        attachmentAction={attachmentAction}
        onAttachmentActionChange={setAttachmentAction}
        replyingTo={replyingTo}
        onCancelReply={() => setReplyingTo(null)}
        editingMessage={editingMessage}
        onCancelEdit={() => setEditingMessage(null)}
        onEditMessage={handleEditMessage}
      />

      {/* Conversation-scoped Camera / Poll / Contact panels (never viewport-level) */}
      <ConversationAttachmentLayer
        action={attachmentAction}
        onClose={() => setAttachmentAction(null)}
        onSend={addMessage}
        onToast={onToast}
        contacts={contacts}
        selectedChat={selectedChat}
      />

      {/* Active Message Action Backdrop & Floating Reaction Bar / Context Menu */}
      {activeActionMessage && (
        <>
          <div className="msg-action-backdrop" onClick={handleCloseActions}></div>
          <MessageReactionBar
            positionStyle={calculatedPositions.reactionBarStyle}
            onSelectReaction={handleSelectReaction}
            currentReactions={activeActionMessage.message.reactions || []}
          />
          <MessageContextMenu
            message={activeActionMessage.message}
            positionStyle={calculatedPositions.contextMenuStyle}
            onReply={handleMenuReply}
            onCopy={handleMenuCopy}
            onReact={handleMenuReact}
            onForward={handleMenuForward}
            onTogglePin={handleMenuTogglePin}
            onToggleStar={handleMenuToggleStar}
            onEdit={handleMenuEdit}
            onDelete={handleMenuDelete}
            onOpenInfo={handleMenuOpenInfo}
          />
        </>
      )}

      {/* WhatsApp 3-Button Delete Confirmation Modal */}
      <DeleteMessageModal
        isOpen={showDeleteModal}
        count={selectedMessageIds.length}
        canDeleteForEveryone={canDeleteForEveryone}
        onDeleteForEveryone={handleConfirmDeleteForEveryone}
        onDeleteForMe={handleConfirmDeleteForMe}
        onCancel={() => setShowDeleteModal(false)}
      />

    </section>
  );
}

export default Conversation;