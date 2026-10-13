import { useCallback, useEffect, useMemo, useRef, useState } from "react";

import Sidebar from "./components/Sidebar/Sidebar";
import ChatList from "./components/ChatList/ChatList";
import Conversation from "./components/Conversation/Conversation";
import ContactProfile from "./components/Conversation/ContactProfile";
import MuteNotificationsPanel from "./components/Conversation/MuteNotificationsPanel";
import ClearChatPanel from "./components/Conversation/ClearChatPanel";
import Moments from "./components/Moments/Moments";
import LiveStreaming from "./components/LiveStreaming/LiveStreaming";
import Channels from "./components/Channels/Channels";
import Profile from "./components/Profile/Profile";
import MobileNav from "./components/Mobile/MobileNav";
import Settings from "./components/Settings/Settings";
import Toast from "./components/Toast/Toast";
import Calendar from "./components/Calendar/Calendar";
import EventDetailModal from "./components/Calendar/EventDetailModal";
import Calls from "./components/Calls/Calls";
import CallOverlay from "./components/Calls/CallOverlay";
import { initialCalendarEvents } from "./data/calendarData";
import { initialChats, initialMessagesByChatId } from "./data/chats";
import {
  getSessionUser,
  getInitialRoute,
  updateProfile,
  uploadAvatar,
  removeAvatar,
  createClientId,
  fetchConversationMessages,
  sendConversationMessage,
  uploadConversationMedia,
  markConversationRead,
  setConversationFavourite,
  muteConversation,
  clearConversation,
  deleteConversation,
  deleteMessages,
  openDirectChat,
  searchChatContacts,
  votePoll,
} from "./services/chatService";
import useChatSync from "./hooks/useChatSync";
import {
  toChat,
  toUiMessage,
  toSendPayload,
  toMediaUpload,
  mediaPreviewLabel,
  MEDIA_LIMITS,
  mergeMessages,
  applyReadReceipt,
  withDayDividers,
  DELETED_MESSAGE_TEXT,
} from "./utils/chatAdapters";
import { formatMessageTime } from "./utils/time";

import "./App.css";

// Logged in through PHP → chats come from grid_chat_db; otherwise (Vite dev) demo data
const HAS_BACKEND_SESSION = Boolean(getSessionUser()?.name);

const DEFAULT_CURRENT_USER = {
  id: "current_user",
  name: "Henry Jabbawockiez",
  role: "Product Designer • UI Systems Lead",
  status: "Available • Online",
  phone: "+91 90590 44812",
  email: "henry.jabbawockiez@gridchat.com",
  bio: "Creating clean, responsive design systems and real-time chat experiences. Coffee enthusiast ☕",
  avatar: "",
  isCurrentUser: true,
};

function App() {
  const [chats, setChats] = useState(HAS_BACKEND_SESSION ? [] : initialChats);
  const [messagesByChatId, setMessagesByChatId] = useState(HAS_BACKEND_SESSION ? {} : initialMessagesByChatId);
  // Demo mode opens the first chat; with the backend nothing is selected until the user picks a chat
  const [selectedChat, setSelectedChat] = useState(HAS_BACKEND_SESSION ? null : initialChats[0]);
  // Conversations whose messages have been loaded from the API
  const loadedConversationIdsRef = useRef(new Set());
  const selectedChatRef = useRef(selectedChat);
  const [currentUser, setCurrentUser] = useState(() => {
    const sessionUser = getSessionUser();
    if (sessionUser && sessionUser.name) {
      return {
        id: sessionUser.id || "current_user",
        name: sessionUser.name,
        role: sessionUser.role || "Team Member",
        status: "Available • Online",
        phone: sessionUser.phone || "",
        email: sessionUser.email || "",
        bio: sessionUser.bio || "",
        avatar: sessionUser.avatar || "",
        avatarSource: sessionUser.avatarSource || "none",
        isCurrentUser: true,
      };
    }
    try {
      const saved = localStorage.getItem("mychat_current_user");
      if (saved) return JSON.parse(saved);
    } catch {
      // ignore
    }
    return DEFAULT_CURRENT_USER;
  });

  // Settings State with LocalStorage Persistence
  const [settings, setSettings] = useState(() => {
    try {
      const saved = localStorage.getItem("mychat_settings");
      if (saved) {
        const parsed = JSON.parse(saved);
        return {
          ...parsed,
          accent: parsed.accent === "purple" || parsed.accent === "blue" || parsed.accent === "emerald" ? "amber" : (parsed.accent || "amber"),
        };
      }
    } catch {
      // ignore
    }
    return {
      theme: "dark",
      accent: "amber",
      fontSize: "md",
      wallpaper: "default",
      soundEnabled: true,
      messagePreviews: true,
      liveAlerts: true,
      lastSeen: "everyone",
      readReceipts: true,
      twoStepAuth: false,
      enterToSend: true,
      autoDownloadMedia: true,
      lowDataMode: false,
    };
  });

  const [toastMessage, setToastMessage] = useState("");
  const [mobileView, setMobileView] = useState("chats"); // 'chats' | 'conversation' | 'contact_info' | 'moments' | 'livestreaming' | 'settings'
  const [activeMobileTab, setActiveMobileTab] = useState("chats");
  const [sidebarActiveView, setSidebarActiveView] = useState("chat");
  const [chatViewMode, setChatViewMode] = useState(() => {
    try {
      const saved = localStorage.getItem("chatViewMode_v2");
      return saved || "grid";
    } catch {
      return "grid";
    }
  });

  const handleToggleChatViewMode = (nextMode) => {
    const target = nextMode || (chatViewMode === "list" ? "grid" : "list");
    setChatViewMode(target);
    try {
      localStorage.setItem("chatViewMode_v2", target);
      localStorage.setItem("chatViewMode", target);
    } catch {
      // ignore
    }
  };
  const [activeProfileContact, setActiveProfileContact] = useState(DEFAULT_CURRENT_USER);
  const [profileSource, setProfileSource] = useState("bottom_nav"); // 'bottom_nav' | 'conversation'
  const [isSidePanelOpen, setIsSidePanelOpen] = useState(false);
  const [showMediaGalleryDirectly, setShowMediaGalleryDirectly] = useState(false);
  // Which content the shared secondary side panel shows: 'contact' | 'mute' | 'clear'
  const [secondaryPanelKind, setSecondaryPanelKind] = useState("contact");
  // Per-chat mute state, keyed by chat id: { muted: boolean, duration: '8h' | '1w' | 'always' }
  const [mutedChats, setMutedChats] = useState({});
  const [isMomentsClosing, setIsMomentsClosing] = useState(false);
  const [isLiveStreamingClosing, setIsLiveStreamingClosing] = useState(false);
  const [isChannelsClosing, setIsChannelsClosing] = useState(false);
  const [isProfileClosing, setIsProfileClosing] = useState(false);
  const [isSettingsClosing, setIsSettingsClosing] = useState(false);
  const [isCalendarClosing, setIsCalendarClosing] = useState(false);
  const [isCallsClosing, setIsCallsClosing] = useState(false);
  // The call currently on screen: { id, name, avatar, type }, or null
  const [activeCall, setActiveCall] = useState(null);
  const [calendarEvents, setCalendarEvents] = useState(initialCalendarEvents);
  const [activeEventDetail, setActiveEventDetail] = useState(null);
  const [settingsInitialSection, setSettingsInitialSection] = useState("appearance");
  const [settingsOpenKey, setSettingsOpenKey] = useState(0);
  const [isSidebarCollapsed, setIsSidebarCollapsed] = useState(() => {
    const saved = localStorage.getItem("sidebarCollapsed");
    // DEFAULT CLOSED (only icons)
    if (saved === null) return true;
    return saved === "true";
  });

  // Synchronize Theme & Settings to DOM and LocalStorage
  useEffect(() => {
    try {
      localStorage.setItem("mychat_settings", JSON.stringify(settings));
    } catch {
      // ignore
    }

    let effectiveTheme = settings.theme;
    if (effectiveTheme === "system") {
      const prefersDark = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;
      effectiveTheme = prefersDark ? "dark" : "light";
    }

    document.documentElement.setAttribute("data-theme", effectiveTheme);
    document.documentElement.setAttribute("data-accent", settings.accent || "amber");
    document.documentElement.setAttribute("data-wallpaper", settings.wallpaper || "default");
  }, [settings]);

  // Handle System Theme changes dynamically when system theme is active
  useEffect(() => {
    if (settings.theme !== "system") return;
    const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
    const handler = (e) => {
      document.documentElement.setAttribute("data-theme", e.matches ? "dark" : "light");
    };
    mediaQuery.addEventListener("change", handler);
    return () => mediaQuery.removeEventListener("change", handler);
  }, [settings.theme]);

  const showToast = (message) => {
    setToastMessage(message);
  };

  useEffect(() => {
    if (!toastMessage) return;

    const timer = setTimeout(() => {
      setToastMessage("");
    }, 2200);

    return () => clearTimeout(timer);
  }, [toastMessage]);

  useEffect(() => {
    selectedChatRef.current = selectedChat;
  }, [selectedChat]);

  // Apply a sync result: chat list, new/changed messages, read receipts
  const handleChatSync = useCallback((data, { isInitial }) => {
    const openChatId = selectedChatRef.current?.conversationId;

    if (isInitial || data.conversations.length > 0) {
      // The open chat is being read, so it never shows an unread badge
      const nextChats = data.conversations
        .map(toChat)
        .map((chat) => (chat.id === openChatId ? { ...chat, unread: undefined } : chat));
      setChats(nextChats);
      setSelectedChat((prev) => {
        const updated = prev && nextChats.find((chat) => chat.id === prev.id);
        return updated ? { ...prev, ...updated } : prev;
      });
    }

    const changed = [...data.messages, ...data.updatedMessages];
    if (changed.length > 0 || data.reads.length > 0) {
      setMessagesByChatId((prev) => {
        const next = { ...prev };
        const byConversation = {};
        changed.forEach((message) => {
          (byConversation[message.conversationId] ||= []).push(toUiMessage(message));
        });
        Object.entries(byConversation).forEach(([conversationId, messages]) => {
          // Chats never opened load their messages when opened
          if (loadedConversationIdsRef.current.has(Number(conversationId))) {
            next[conversationId] = mergeMessages(next[conversationId], messages);
          }
        });
        data.reads.forEach(({ conversationId, readUpToMessageId }) => {
          if (next[conversationId]) {
            next[conversationId] = applyReadReceipt(next[conversationId], readUpToMessageId);
          }
        });
        return next;
      });
    }

    if (openChatId && data.messages.some((m) => m.conversationId === openChatId && m.type === "incoming")) {
      markConversationRead(openChatId);
    }
  }, []);

  useChatSync({ enabled: HAS_BACKEND_SESSION, onSync: handleChatSync });

  const loadConversationMessages = (conversationId) => {
    if (loadedConversationIdsRef.current.has(conversationId)) return;
    loadedConversationIdsRef.current.add(conversationId);

    fetchConversationMessages(conversationId).then((result) => {
      if (!result.success) {
        loadedConversationIdsRef.current.delete(conversationId);
        showToast(result.message || "Could not load messages");
        return;
      }
      setMessagesByChatId((prev) => ({
        ...prev,
        [conversationId]: mergeMessages(prev[conversationId], result.data.messages.map(toUiMessage)),
      }));
    });
  };

  const updateMediaUrlRoute = (isOpen, targetChat) => {
    if (typeof window === "undefined" || !window.history) return;
    try {
      const pathname = window.location.pathname;
      const chat = targetChat || selectedChat;
      if (isOpen) {
        if (!pathname.includes("/media")) {
          const basePath = pathname.replace(/\/media(\/.*)?$/, "").replace(/\/+$/, "");
          const targetUrl = `${basePath || "/grid-chat"}/media${chat?.id ? `/${chat.id}` : ""}`;
          window.history.pushState({ view: "media", chatId: chat?.id }, "", targetUrl);
        }
      } else {
        if (pathname.includes("/media")) {
          let basePath = pathname.replace(/\/media(\/.*)?$/, "").replace(/\/+$/, "");
          if (!basePath) basePath = "/grid-chat";
          window.history.pushState({ view: "chat" }, "", basePath);
        }
      }
    } catch {
      // ignore
    }
  };

  // Synchronize URL routing for /media and browser back/forward history navigation
  useEffect(() => {
    const checkRoute = () => {
      if (typeof window === "undefined") return;
      const pathname = window.location.pathname;
      const search = window.location.search;
      const hash = window.location.hash;
      const initialRoute = getInitialRoute();

      const isMedia =
        initialRoute === "media" ||
        pathname.includes("/media") ||
        search.includes("view=media") ||
        hash.includes("media");

      if (isMedia) {
        const mediaMatch = pathname.match(/\/media\/([a-zA-Z0-9_-]+)/);
        const searchParams = new URLSearchParams(search);
        const targetChatId = (mediaMatch && mediaMatch[1]) || searchParams.get("chat");

        if (targetChatId && chats.length > 0) {
          const matched = chats.find((c) => String(c.id) === String(targetChatId));
          if (matched) {
            setSelectedChat(matched);
            setActiveProfileContact(matched);
          }
        }

        setShowMediaGalleryDirectly(true);
        setSecondaryPanelKind("contact");
        setIsSidePanelOpen(true);
        if (window.innerWidth <= 768) {
          setMobileView("contact_info");
        }
      } else {
        setShowMediaGalleryDirectly(false);
      }
    };

    checkRoute();
    window.addEventListener("popstate", checkRoute);
    return () => window.removeEventListener("popstate", checkRoute);
  }, [chats]);

  const handleSelectChat = (chat) => {
    setSelectedChat(chat);
    if (isSidePanelOpen) {
      setActiveProfileContact(chat);
    }
    setMobileView("conversation");
    setSidebarActiveView("chat");

    if (HAS_BACKEND_SESSION && chat?.conversationId) {
      loadConversationMessages(chat.conversationId);
      if (chat.unread) {
        markConversationRead(chat.conversationId);
        setChats((prev) => prev.map((c) => (c.id === chat.id ? { ...c, unread: undefined } : c)));
      }
    }
  };

  /**
   * The direct chat with a CRM user, creating it if this is the first contact.
   * Used by the contacts list, status replies and call records.
   *
   * @returns the chat, or null when it could not be opened
   */
  const resolveDirectChat = async (userId) => {
    const id = Number(userId);
    if (!id) return null;

    const known = chats.find((c) => !c.isGroup && Number(c.userId) === id);
    if (known?.conversationId) return known;

    const result = await openDirectChat(id);
    if (!result.success || !result.data?.conversation) {
      showToast(result.message || "Could not open the chat");
      return null;
    }

    const chat = toChat(result.data.conversation);
    setChats((prev) => [chat, ...prev.filter((c) => c.id !== chat.id)]);
    return chat;
  };

  // New chat picked from the contacts list → find or create the direct conversation
  const handleStartDirectChat = async (user) => {
    const chat = await resolveDirectChat(user.id);
    if (!chat) return false;
    handleSelectChat(chat);
    return true;
  };

  const handleNewChat = (newChat) => {
    setChats((prevChats) => [newChat, ...prevChats]);
    setMessagesByChatId((prev) => ({
      ...prev,
      [newChat.id]: [
        {
          id: Date.now(),
          type: "divider",
          text: "Today",
        },
        {
          id: Date.now() + 1,
          type: "incoming",
          text: newChat.text || `Start a new conversation with ${newChat.name}.`,
          time: "Just now",
          avatar: newChat.avatar,
          more: true,
        },
      ],
    }));
    setSelectedChat(newChat);
    if (isSidePanelOpen) {
      setActiveProfileContact(newChat);
    }
    setMobileView("conversation");
    setSidebarActiveView("chat");
  };

  /**
   * Backend chat: show the message at once (clock tick), then replace it with
   * the saved one.
   *
   * @param targetChat send to this chat instead of the selected one. Needed by
   *   status replies and call records, which resolve their own conversation —
   *   `selectedChat` comes from the render closure, so it is still the old
   *   value in the same tick as a handleSelectChat() call.
   */
  const sendToConversation = (textOrMsg, targetChat) => {
    const chat = targetChat || selectedChat;
    const conversationId = chat.conversationId;
    const media = toMediaUpload(textOrMsg);
    const payload = media ? null : toSendPayload(textOrMsg);
    if (!media && !payload) {
      if (typeof textOrMsg === "object" && textOrMsg !== null) {
        showToast("This item can't be sent");
      }
      return;
    }

    const messageType = media ? media.messageType : payload.messageType;
    const body = media ? media.body : payload.body;

    if (media && media.file.size > MEDIA_LIMITS[messageType]) {
      showToast(`File is too large (max ${MEDIA_LIMITS[messageType] / (1024 * 1024)} MB)`);
      return;
    }

    const clientId = createClientId();
    const createdAt = new Date().toISOString();
    const optimistic = {
      // Local preview (data/object URL) until the saved message arrives; the File itself isn't kept
      ...(typeof textOrMsg === "object" ? textOrMsg : {}),
      file: undefined,
      id: `local-${clientId}`,
      clientId,
      conversationId,
      type: "outgoing",
      messageType,
      text: body,
      createdAt,
      time: formatMessageTime(createdAt),
      status: "pending",
      progress: media ? 0 : undefined,
      check: false,
    };

    const updateOptimistic = (changes) =>
      setMessagesByChatId((prev) => ({
        ...prev,
        [conversationId]: (prev[conversationId] || []).map((m) =>
          m.clientId === clientId && m.status === "pending" ? { ...m, ...changes } : m
        ),
      }));

    setMessagesByChatId((prev) => ({
      ...prev,
      [conversationId]: mergeMessages(prev[conversationId], [optimistic]),
    }));

    const preview = `You: ${body || mediaPreviewLabel(messageType) || "Shared an item"}`;
    setChats((prev) => {
      const existing = prev.find((c) => c.id === conversationId) || chat;
      return [
        { ...existing, text: preview, time: formatMessageTime(createdAt), lastMessageAt: createdAt },
        ...prev.filter((c) => c.id !== conversationId),
      ];
    });

    const request = media
      ? uploadConversationMedia({ conversationId, clientId, ...media }, (progress) => updateOptimistic({ progress }))
      : sendConversationMessage({ conversationId, clientId, ...payload });

    request.then((result) => {
      if (result.success) {
        setMessagesByChatId((prev) => ({
          ...prev,
          [conversationId]: mergeMessages(prev[conversationId], [toUiMessage(result.data.message)]),
        }));
      } else {
        updateOptimistic({ status: "failed", progress: undefined });
        showToast(result.message || "Message not sent");
      }
    });
  };

  const handleSendMessage = (textOrMsg) => {
    if (!selectedChat) return;

    if (HAS_BACKEND_SESSION && selectedChat.conversationId) {
      sendToConversation(textOrMsg);
      return;
    }

    let newMsg;
    let displayText = "";

    if (typeof textOrMsg === "object" && textOrMsg !== null) {
      newMsg = {
        id: Date.now(),
        type: "outgoing",
        time: "Just now",
        check: true,
        ...textOrMsg,
      };
      displayText =
        textOrMsg.text ||
        (textOrMsg.messageType === "event"
          ? `📅 Event: ${textOrMsg.eventData?.title}`
          : textOrMsg.messageType === "video"
          ? "🎥 Video"
          : textOrMsg.messageType === "image"
          ? "📷 Photo"
          : textOrMsg.messageType === "poll"
          ? `📊 Poll: ${textOrMsg.pollData?.question || ""}`
          : textOrMsg.messageType === "contact"
          ? `👤 Contact: ${textOrMsg.contactData?.name || ""}`
          : "Shared an item");
    } else {
      displayText = String(textOrMsg || "");
      newMsg = {
        id: Date.now(),
        type: "outgoing",
        text: displayText,
        time: "Just now",
        check: true,
      };
    }

    // Update conversation messages
    setMessagesByChatId((prev) => ({
      ...prev,
      [selectedChat.id]: [...(prev[selectedChat.id] || []), newMsg],
    }));

    // Update chat last message & time, clear missed call, and bump to top as the most recent chat
    setChats((prevChats) => {
      const existing = prevChats.find((c) => c.id === selectedChat.id);
      const updatedChat = {
        ...(existing || selectedChat),
        text: displayText,
        hasMissedCall: false,
        hasMissedVideoCall: false,
        hasMissedAudioCall: false,
        isMissedCall: false,
        isMissed: false,
        time: "Just now",
      };
      const filtered = prevChats.filter((c) => c.id !== selectedChat.id);
      return [updatedChat, ...filtered];
    });

    setSelectedChat((prev) => ({
      ...prev,
      text: displayText,
      hasMissedCall: false,
      hasMissedVideoCall: false,
      hasMissedAudioCall: false,
      isMissedCall: false,
      isMissed: false,
      time: "Just now",
    }));
  };

  const handleDeleteMessages = async (messageIds, deleteType) => {
    if (!selectedChat?.id) return;
    const chatId = selectedChat.id;
    const idSet = new Set(messageIds);

    // Remember what the messages looked like, so a failed request can be undone
    const before = messagesByChatId[chatId] || [];

    const applyLocal = (list) =>
      deleteType === "everyone"
        ? list.map((m) =>
            idSet.has(m.id)
              ? { ...m, isDeleted: true, text: DELETED_MESSAGE_TEXT, reactions: [] }
              : m
          )
        : list.filter((m) => !idSet.has(m.id));

    setMessagesByChatId((prev) => ({ ...prev, [chatId]: applyLocal(prev[chatId] || []) }));

    if (!HAS_BACKEND_SESSION || !selectedChat.conversationId) return;

    // Only saved messages exist server-side; an unsent one is already gone locally
    const serverIds = before
      .filter((m) => idSet.has(m.id) && m.serverId)
      .map((m) => m.serverId);
    if (!serverIds.length) return;

    const result = await deleteMessages(serverIds, deleteType);
    if (!result.success) {
      // Put them back rather than leaving the screen lying about what happened
      setMessagesByChatId((prev) => ({ ...prev, [chatId]: before }));
      showToast(result.message || "Messages were not deleted");
    }
  };

  const handleReactMessage = (messageId, emoji) => {
    if (!selectedChat?.id) return;
    setMessagesByChatId((prev) => {
      const current = prev[selectedChat.id] || [];
      const updated = current.map((m) => {
        if (m.id === messageId) {
          const currentReactions = m.reactions ? [...m.reactions] : [];
          const idx = currentReactions.findIndex((r) => r.emoji === emoji);
          if (idx >= 0) {
            const item = currentReactions[idx];
            if (item.userReacted) {
              if (item.count <= 1) {
                currentReactions.splice(idx, 1);
              } else {
                currentReactions[idx] = { ...item, count: item.count - 1, userReacted: false };
              }
            } else {
              currentReactions[idx] = { ...item, count: item.count + 1, userReacted: true };
            }
          } else {
            currentReactions.push({ emoji, count: 1, userReacted: true });
          }
          return { ...m, reactions: currentReactions };
        }
        return m;
      });
      return {
        ...prev,
        [selectedChat.id]: updated,
      };
    });
  };

  const handleStarMessages = (messageIds) => {
    if (!selectedChat?.id) return;
    const idSet = new Set(messageIds);
    setMessagesByChatId((prev) => {
      const current = prev[selectedChat.id] || [];
      const updated = current.map((m) => {
        if (idSet.has(m.id)) {
          return { ...m, isStarred: !m.isStarred };
        }
        return m;
      });
      return {
        ...prev,
        [selectedChat.id]: updated,
      };
    });
  };

  const handleEditMessage = (messageId, newText) => {
    if (!selectedChat?.id) return;
    setMessagesByChatId((prev) => {
      const current = prev[selectedChat.id] || [];
      const updated = current.map((m) => {
        if (m.id === messageId) {
          return { ...m, text: newText };
        }
        return m;
      });
      return {
        ...prev,
        [selectedChat.id]: updated,
      };
    });
  };

  const handleCreateChatEvent = (eventData) => {
    const fullEvent = {
      id: eventData.id || `evt-${Date.now()}`,
      title: eventData.title || "Scheduled Event",
      category: eventData.category || "meeting",
      date: eventData.date || "2026-09-12",
      time: eventData.time || "17:00",
      duration: eventData.duration || "1 hour",
      withPerson: eventData.withPerson || selectedChat?.name || "Friend",
      status: "confirmed",
      location: eventData.location || "In-App Room",
      description: eventData.description || `Scheduled event with ${eventData.withPerson || selectedChat?.name || "Friend"}`,
      rsvps: { going: 2, maybe: 0, declined: 0, userRsvp: "going" },
      ...eventData,
    };

    // Add to calendar events list
    setCalendarEvents((prev) => [fullEvent, ...prev]);

    // Send as chat event message
    handleSendMessage({
      messageType: "event",
      text: `📅 Scheduled: ${fullEvent.title}`,
      eventData: fullEvent,
    });

    showToast(`Event "${fullEvent.title}" added to Calendar!`);
  };

  const handleOpenContactInfo = () => {
    setActiveProfileContact(selectedChat);
    setProfileSource("conversation");
    setShowMediaGalleryDirectly(false);
    setSecondaryPanelKind("contact");
    setIsSidePanelOpen(true);
    setMobileView("contact_info");
  };

  const handleOpenMediaDocs = () => {
    setActiveProfileContact(selectedChat);
    setProfileSource("conversation");
    setShowMediaGalleryDirectly(true);
    setSecondaryPanelKind("contact");
    setIsSidePanelOpen(true);
    setMobileView("contact_info");
    updateMediaUrlRoute(true, selectedChat);
  };

  const handleOpenMute = () => {
    setActiveProfileContact(selectedChat);
    setProfileSource("conversation");
    setShowMediaGalleryDirectly(false);
    setSecondaryPanelKind("mute");
    setIsSidePanelOpen(true);
    setMobileView("contact_info");
  };

  const handleOpenClearChat = () => {
    setActiveProfileContact(selectedChat);
    setProfileSource("conversation");
    setShowMediaGalleryDirectly(false);
    setSecondaryPanelKind("clear");
    setIsSidePanelOpen(true);
    setMobileView("contact_info");
  };

  const handleCloseContactInfo = () => {
    setIsSidePanelOpen(false);
    setShowMediaGalleryDirectly(false);
    setSecondaryPanelKind("contact");
    updateMediaUrlRoute(false);
    if (mobileView === "contact_info") {
      setMobileView("conversation");
    }
  };

  const handleOpenDeleteChat = () => {
    setActiveProfileContact(selectedChat);
    setProfileSource("conversation");
    setShowMediaGalleryDirectly(false);
    setSecondaryPanelKind("delete");
    setIsSidePanelOpen(true);
    setMobileView("contact_info");
  };

  // Chat saved in grid_chat_db (null for demo chats)
  const findBackendChat = (chatId) =>
    HAS_BACKEND_SESSION ? chats.find((c) => c.id === chatId && c.conversationId) || null : null;

  // Replace one chat with the server's saved state
  const applyConversationUpdate = (conversation) => {
    if (!conversation) return;
    const updated = toChat(conversation);
    const chat = selectedChatRef.current?.id === updated.id ? { ...updated, unread: undefined } : updated;
    setChats((prev) => prev.map((c) => (c.id === chat.id ? chat : c)));
    setSelectedChat((prev) => (prev?.id === chat.id ? { ...prev, ...chat } : prev));
    setActiveProfileContact((prev) => (prev?.id === chat.id ? { ...prev, ...chat } : prev));
  };

  // Mute state for the panel/header: from the DB for backend chats, local for demo chats
  const getMuteInfo = (chat) => {
    const backendChat = chat && findBackendChat(chat.id);
    if (backendChat) {
      return {
        muted: Boolean(backendChat.isMuted),
        duration: backendChat.isMuted && backendChat.mutedUntil?.startsWith("9999") ? "always" : null,
      };
    }
    return mutedChats[chat?.id];
  };

  const handleSetChatMute = async (chatId, duration) => {
    if (!chatId) return;
    const backendChat = findBackendChat(chatId);
    if (backendChat) {
      const result = await muteConversation(backendChat.conversationId, duration);
      if (!result.success) {
        showToast(result.message || "Could not mute notifications");
        return;
      }
      applyConversationUpdate(result.data.conversation);
    } else {
      setMutedChats((prev) => ({ ...prev, [chatId]: { muted: true, duration } }));
    }
    handleCloseContactInfo();
    const label = duration === "8h" ? "8 hours" : duration === "1w" ? "1 week" : "always";
    showToast(`Notifications muted for ${label}`);
  };

  const handleUnmuteChat = async (chatId) => {
    if (!chatId) return;
    const backendChat = findBackendChat(chatId);
    if (backendChat) {
      const result = await muteConversation(backendChat.conversationId, "off");
      if (!result.success) {
        showToast(result.message || "Could not unmute notifications");
        return;
      }
      applyConversationUpdate(result.data.conversation);
    } else {
      setMutedChats((prev) => {
        const next = { ...prev };
        delete next[chatId];
        return next;
      });
    }
    handleCloseContactInfo();
    showToast("Notifications unmuted");
  };

  const handleClearChatMessages = async (chatId) => {
    if (!chatId) return;
    const backendChat = findBackendChat(chatId);
    if (backendChat) {
      const result = await clearConversation(backendChat.conversationId);
      if (!result.success) {
        showToast(result.message || "Could not clear chat");
        return;
      }
      applyConversationUpdate(result.data.conversation);
    }
    setMessagesByChatId((prev) => ({ ...prev, [chatId]: [] }));
    handleCloseContactInfo();
    showToast("Chat cleared");
  };

  const handleToggleFavourite = async (chatId) => {
    const chat = chats.find((c) => c.id === chatId);
    if (!chat) return;
    const isFavourite = !chat.isFavourite;
    const setLocalFavourite = (value) => {
      setChats((prev) => prev.map((c) => (c.id === chatId ? { ...c, isFavourite: value } : c)));
      setSelectedChat((prev) => (prev?.id === chatId ? { ...prev, isFavourite: value } : prev));
    };

    setLocalFavourite(isFavourite);
    if (HAS_BACKEND_SESSION && chat.conversationId) {
      const result = await setConversationFavourite(chat.conversationId, isFavourite);
      if (!result.success) {
        setLocalFavourite(!isFavourite);
        showToast(result.message || "Could not update favourites");
        return;
      }
      applyConversationUpdate(result.data.conversation);
    }
    showToast(isFavourite ? "Added to favourites" : "Removed from favourites");
  };

  // Delete removes the chat for the current user only; the other person keeps it
  const handleDeleteChat = async (chatId) => {
    if (!chatId) return;
    const backendChat = findBackendChat(chatId);
    if (backendChat) {
      const result = await deleteConversation(backendChat.conversationId);
      if (!result.success) {
        showToast(result.message || "Could not delete chat");
        return;
      }
      loadedConversationIdsRef.current.delete(backendChat.conversationId);
    }

    setChats((prev) => prev.filter((c) => c.id !== chatId));
    setMessagesByChatId((prev) => {
      const next = { ...prev };
      delete next[chatId];
      return next;
    });
    handleCloseContactInfo();
    if (selectedChat?.id === chatId) {
      setSelectedChat(null);
      setMobileView("chats");
    }
    showToast("Chat deleted");
  };

  const handleCloseProfile = () => {
    setIsProfileClosing(true);
    setTimeout(() => {
      setMobileView("chats");
      setActiveMobileTab("chats");
      setSidebarActiveView("chat");
      setIsProfileClosing(false);
    }, 180);
  };

  const handleOpenUserProfile = () => {
    setIsMomentsClosing(false);
    setIsLiveStreamingClosing(false);
    setIsChannelsClosing(false);
    setIsProfileClosing(false);
    setIsSettingsClosing(false);
    setActiveMobileTab("profile");
    setSidebarActiveView("profile");
    setMobileView("profile");
  };

  // Logged in through PHP → profile is saved in grid_chat_db; otherwise (Vite dev) localStorage only
  const hasBackendSession = HAS_BACKEND_SESSION;

  const saveLocalUser = (userData) => {
    try {
      localStorage.setItem("mychat_current_user", JSON.stringify(userData));
    } catch {
      // ignore
    }
  };

  // Apply the server's saved user, keeping client-only fields (e.g. status)
  const applyServerUser = (serverUser) => {
    if (!serverUser) return;
    setCurrentUser((prev) => ({ ...prev, ...serverUser }));
  };

  // Optimistically update, persist, and roll back on failure.
  // Resolves to { success, message }.
  const handleUpdateCurrentUser = async (newUserData) => {
    const previousUser = currentUser;
    setCurrentUser(newUserData);

    if (!hasBackendSession) {
      saveLocalUser(newUserData);
      return { success: true };
    }

    const profileChanged = ["name", "role", "phone", "bio"].some(
      (key) => (newUserData[key] ?? "") !== (previousUser[key] ?? "")
    );

    let result = { success: true };
    if (profileChanged) {
      result = await updateProfile(newUserData);
    }

    if (!result.success) {
      setCurrentUser(previousUser);
      return { success: false, message: result.message || "Could not save profile" };
    }
    applyServerUser(result.data);
    return { success: true, message: result.message };
  };

  const handleUploadAvatar = async (file) => {
    if (!hasBackendSession) {
      // Dev fallback: keep the photo locally as a data URL
      const dataUrl = await new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(file);
      }).catch(() => null);
      if (!dataUrl) return { success: false, message: "Failed to read photo file" };
      const updated = { ...currentUser, avatar: dataUrl };
      setCurrentUser(updated);
      saveLocalUser(updated);
      return { success: true };
    }

    const result = await uploadAvatar(file);
    if (!result.success) {
      return { success: false, message: result.message || "Could not upload photo" };
    }
    applyServerUser(result.data);
    return { success: true, message: result.message };
  };

  // Back to the CRM photo, or initials when there is none
  const handleRemoveAvatar = async () => {
    if (!hasBackendSession) {
      const updated = { ...currentUser, avatar: "" };
      setCurrentUser(updated);
      saveLocalUser(updated);
      return { success: true };
    }

    const result = await removeAvatar();
    if (!result.success) {
      return { success: false, message: result.message || "Could not remove photo" };
    }
    applyServerUser(result.data);
    return { success: true, message: result.message };
  };

  const handleCloseMoments = () => {
    setIsMomentsClosing(true);
    setTimeout(() => {
      setMobileView("chats");
      setActiveMobileTab("chats");
      setSidebarActiveView("chat");
      setIsMomentsClosing(false);
    }, 180);
  };

  const handleCloseLiveStreaming = () => {
    setIsLiveStreamingClosing(true);
    setTimeout(() => {
      setMobileView("chats");
      setActiveMobileTab("chats");
      setSidebarActiveView("chat");
      setIsLiveStreamingClosing(false);
    }, 180);
  };

  const handleCloseChannels = () => {
    setIsChannelsClosing(true);
    setTimeout(() => {
      setMobileView("chats");
      setActiveMobileTab("chats");
      setSidebarActiveView("chat");
      setIsChannelsClosing(false);
    }, 180);
  };

  const handleCloseSettings = () => {
    setIsSettingsClosing(true);
    setTimeout(() => {
      setMobileView("chats");
      setActiveMobileTab("chats");
      setSidebarActiveView("chat");
      setIsSettingsClosing(false);
    }, 180);
  };

  const handleOpenSettingsSection = (sectionId = "appearance") => {
    setSettingsInitialSection(sectionId);
    setSettingsOpenKey((prev) => prev + 1);
    setIsMomentsClosing(false);
    setIsLiveStreamingClosing(false);
    setIsChannelsClosing(false);
    setIsProfileClosing(false);
    setIsSettingsClosing(false);
    setIsCalendarClosing(false);
    setSidebarActiveView("settings");
    setMobileView("settings");
    setActiveMobileTab("settings");
  };

  const handleCloseCalendar = () => {
    setIsCalendarClosing(true);
    setTimeout(() => {
      setMobileView("chats");
      setActiveMobileTab("chats");
      setSidebarActiveView("chat");
      setIsCalendarClosing(false);
    }, 180);
  };

  const handleCloseCalls = () => {
    setIsCallsClosing(true);
    setTimeout(() => {
      setMobileView("chats");
      setActiveMobileTab("chats");
      setSidebarActiveView("chat");
      setIsCallsClosing(false);
    }, 180);
  };

  /**
   * Opens the shared in-call screen. Called from the Calls list, the
   * conversation header and the contact profile, so they all behave the same.
   *
   * @param contact anything carrying a name and an avatar
   * @param type    "audio" | "video"
   */
  const handleStartCall = (contact, type = "audio") => {
    if (!contact) return;

    setActiveCall({
      id: contact.id ?? contact.conversationId ?? contact.name,
      // Kept separately so the finished call knows where to post its record.
      // A call from the Calls list has neither (that list is still mock data),
      // so it simply isn't logged.
      conversationId: contact.conversationId ?? null,
      userId: contact.userId ?? null,
      name: contact.name || "Unknown",
      avatar: contact.avatar || "",
      type: type === "video" ? "video" : "audio",
      startedAt: new Date().toISOString(),
    });
  };

  /**
   * A finished call becomes an ordinary message in the conversation, so it sits
   * in the stream in chronological order. The structured record goes in
   * metadata; the body is only the chat-list preview.
   */
  const handleEndCall = async (endedCall) => {
    setActiveCall(null);
    if (!endedCall) return;

    const isVideo = endedCall.type === "video";
    const status = endedCall.status || "cancelled";
    const duration = status === "completed" ? Math.max(0, Math.round(endedCall.durationSeconds || 0)) : 0;

    if (!HAS_BACKEND_SESSION) {
      showToast(`Call with ${endedCall.name} ended`);
      return;
    }

    const chat = endedCall.conversationId
      ? chats.find((c) => c.conversationId === endedCall.conversationId)
      : endedCall.userId
      ? await resolveDirectChat(endedCall.userId)
      : null;

    if (!chat?.conversationId) {
      // Nothing to attach it to (e.g. started from the mock Calls list)
      showToast(`Call with ${endedCall.name} ended`);
      return;
    }

    sendToConversation(
      {
        messageType: "call",
        text: isVideo ? "Video call" : "Voice call",
        callData: {
          call_type: isVideo ? "video" : "audio",
          direction: "outgoing",
          status,
          duration,
          started_at: endedCall.startedAt || null,
          ended_at: new Date().toISOString(),
        },
      },
      chat
    );
  };

  /**
   * A reply typed on someone's status is delivered as a normal chat message
   * that quotes the status, then the chat opens so the sender sees it land.
   */
  const handleOpenChatWithStoryReply = async (moment, story, textOrEmoji) => {
    const text = String(textOrEmoji || "").trim();
    if (!text || !moment?.contactId) return;

    if (!HAS_BACKEND_SESSION) {
      showToast("Sign in to reply to a status");
      return;
    }

    const chat = await resolveDirectChat(moment.contactId);
    if (!chat?.conversationId) return;

    const statusReply = {
      momentId: story?.realId ?? null,
      type: story?.type || "text",
      text: story?.type === "text" ? (story.text || "").slice(0, 120) : null,
      ownerName: moment.displayName || moment.userName || "",
      createdAt: story?.createdAt || null,
    };

    sendToConversation(
      {
        messageType: "text",
        text,
        statusReply,
        // Shown straight away; the saved message rebuilds the same card from
        // its metadata, so the quote never flickers
        replyTo: {
          sender: `${statusReply.ownerName}'s status`,
          text: statusReply.text || (story?.type === "video" ? "🎥 Video" : "📷 Photo"),
          isStatus: true,
        },
      },
      chat
    );

    handleSelectChat(chat);
    setActiveMobileTab("chats");
  };

  const handleSelectMobileTab = (tab) => {
    setActiveMobileTab(tab);

    if (tab === "chats") {
      setChatViewMode("grid");
      if (mobileView === "moments") {
        handleCloseMoments();
      } else if (mobileView === "livestreaming") {
        handleCloseLiveStreaming();
      } else if (mobileView === "channels") {
        handleCloseChannels();
      } else if (mobileView === "calendar") {
        handleCloseCalendar();
      } else if (mobileView === "calls") {
        handleCloseCalls();
      } else if (mobileView === "profile") {
        handleCloseProfile();
      } else if (mobileView === "settings") {
        handleCloseSettings();
      } else {
        setMobileView("chats");
        setSidebarActiveView("chat");
      }
    } else if (tab === "calls") {
      setIsMomentsClosing(false);
      setIsLiveStreamingClosing(false);
      setIsChannelsClosing(false);
      setIsProfileClosing(false);
      setIsSettingsClosing(false);
      setIsCalendarClosing(false);
      setIsCallsClosing(false);
      setMobileView("calls");
      setSidebarActiveView("calls");
    } else if (tab === "profile") {
      handleOpenUserProfile();
    } else if (tab === "moments") {
      setIsMomentsClosing(false);
      setMobileView("moments");
      setSidebarActiveView("moments");
    } else if (tab === "livestreaming") {
      setIsLiveStreamingClosing(false);
      setMobileView("livestreaming");
      setSidebarActiveView("livestreaming");
    } else if (tab === "channels") {
      setIsMomentsClosing(false);
      setIsLiveStreamingClosing(false);
      setIsChannelsClosing(false);
      setMobileView("channels");
      setSidebarActiveView("channels");
    } else if (tab === "calendar") {
      setIsMomentsClosing(false);
      setIsLiveStreamingClosing(false);
      setIsChannelsClosing(false);
      setIsProfileClosing(false);
      setIsSettingsClosing(false);
      setIsCalendarClosing(false);
      setIsCallsClosing(false);
      setMobileView("calendar");
      setSidebarActiveView("calendar");
    } else if (tab === "settings") {
      setIsMomentsClosing(false);
      setIsLiveStreamingClosing(false);
      setIsChannelsClosing(false);
      setIsProfileClosing(false);
      setIsSettingsClosing(false);
      setIsCalendarClosing(false);
      setIsCallsClosing(false);
      setMobileView("settings");
      setSidebarActiveView("settings");
    }
  };

  const handleSidebarSelectView = (view) => {
    if (
      view === sidebarActiveView &&
      !isMomentsClosing &&
      !isLiveStreamingClosing &&
      !isChannelsClosing &&
      !isProfileClosing &&
      !isSettingsClosing &&
      !isCalendarClosing &&
      !isCallsClosing
    )
      return;

    if (view === "chat") {
      setChatViewMode("grid");
      if (sidebarActiveView === "moments" || mobileView === "moments") {
        handleCloseMoments();
      } else if (sidebarActiveView === "livestreaming" || mobileView === "livestreaming") {
        handleCloseLiveStreaming();
      } else if (sidebarActiveView === "channels" || mobileView === "channels") {
        handleCloseChannels();
      } else if (sidebarActiveView === "calendar" || mobileView === "calendar") {
        handleCloseCalendar();
      } else if (sidebarActiveView === "calls" || mobileView === "calls") {
        handleCloseCalls();
      } else if (sidebarActiveView === "profile" || mobileView === "profile") {
        handleCloseProfile();
      } else if (sidebarActiveView === "settings" || mobileView === "settings") {
        handleCloseSettings();
      } else {
        setSidebarActiveView("chat");
        setMobileView("chats");
        setActiveMobileTab("chats");
      }
    } else if (view === "calls") {
      setIsMomentsClosing(false);
      setIsLiveStreamingClosing(false);
      setIsChannelsClosing(false);
      setIsProfileClosing(false);
      setIsSettingsClosing(false);
      setIsCalendarClosing(false);
      setIsCallsClosing(false);
      setSidebarActiveView("calls");
      setMobileView("calls");
      setActiveMobileTab("calls");
    } else if (view === "moments") {
      setIsMomentsClosing(false);
      setIsLiveStreamingClosing(false);
      setIsChannelsClosing(false);
      setIsProfileClosing(false);
      setIsSettingsClosing(false);
      setIsCalendarClosing(false);
      setIsCallsClosing(false);
      setSidebarActiveView("moments");
      setMobileView("moments");
      setActiveMobileTab("moments");
    } else if (view === "livestreaming") {
      setIsMomentsClosing(false);
      setIsLiveStreamingClosing(false);
      setIsChannelsClosing(false);
      setIsProfileClosing(false);
      setIsSettingsClosing(false);
      setIsCalendarClosing(false);
      setIsCallsClosing(false);
      setSidebarActiveView("livestreaming");
      setMobileView("livestreaming");
      setActiveMobileTab("livestreaming");
    } else if (view === "channels") {
      setIsMomentsClosing(false);
      setIsLiveStreamingClosing(false);
      setIsChannelsClosing(false);
      setIsProfileClosing(false);
      setIsSettingsClosing(false);
      setIsCalendarClosing(false);
      setIsCallsClosing(false);
      setSidebarActiveView("channels");
      setMobileView("channels");
      setActiveMobileTab("channels");
    } else if (view === "calendar") {
      setIsMomentsClosing(false);
      setIsLiveStreamingClosing(false);
      setIsChannelsClosing(false);
      setIsProfileClosing(false);
      setIsSettingsClosing(false);
      setIsCalendarClosing(false);
      setIsCallsClosing(false);
      setSidebarActiveView("calendar");
      setMobileView("calendar");
      setActiveMobileTab("calendar");
    } else if (view === "profile") {
      handleOpenUserProfile();
    } else if (view === "settings") {
      setIsMomentsClosing(false);
      setIsLiveStreamingClosing(false);
      setIsChannelsClosing(false);
      setIsProfileClosing(false);
      setIsSettingsClosing(false);
      setIsCalendarClosing(false);
      setIsCallsClosing(false);
      setSidebarActiveView("settings");
      setMobileView("settings");
      setActiveMobileTab("settings");
    }
  };

  const handleUpdateContact = (updatedContact) => {
    if (!updatedContact || !updatedContact.id) return;
    setChats((prev) =>
      prev.map((c) => (c.id === updatedContact.id ? { ...c, ...updatedContact } : c))
    );
    if (selectedChat?.id === updatedContact.id) {
      setSelectedChat((prev) => ({ ...prev, ...updatedContact }));
    }
    if (activeProfileContact?.id === updatedContact.id) {
      setActiveProfileContact((prev) => ({ ...prev, ...updatedContact }));
    }
  };

  const handleShareContact = (targetMember, sharedContact) => {
    if (!targetMember || !sharedContact) return;
    const shareMessage = {
      id: "share_" + Date.now(),
      sender: currentUser.name,
      time: formatMessageTime(new Date()),
      text: `Contact: ${sharedContact.name} (${sharedContact.phone || "No phone"})`,
      messageType: "contact",
      contact: {
        id: sharedContact.id,
        name: sharedContact.name,
        phone: sharedContact.phone,
        avatar: sharedContact.avatar,
      },
      status: "sent",
    };
    setMessagesByChatId((prev) => ({
      ...prev,
      [targetMember.id]: [...(prev[targetMember.id] || []), shareMessage],
    }));
    showToast(`Contact shared with ${targetMember.name}`);
  };

  const handleVotePoll = async (messageId, optionIndex) => {
    if (messageId === undefined || messageId === null || optionIndex === undefined) return;

    // Optimistically update poll counts and user selection in state
    setMessagesByChatId((prev) => {
      const next = { ...prev };
      let changed = false;
      for (const chatId in next) {
        const msgs = next[chatId];
        const idx = msgs.findIndex((m) => m.id === messageId || m.serverId === messageId);
        if (idx !== -1) {
          const msg = msgs[idx];
          if (msg.messageType === "poll" && msg.pollData) {
            const prevVoted = msg.pollData.userVotedIndex ?? null;
            if (prevVoted === optionIndex) return prev; // already voted for this option

            const updatedOptions = (msg.pollData.options || []).map((opt, i) => {
              let count = opt.votes || 0;
              if (prevVoted !== null && i === prevVoted && count > 0) count -= 1;
              if (i === optionIndex) count += 1;
              return { ...opt, votes: count };
            });

            const updatedPoll = {
              ...msg.pollData,
              options: updatedOptions,
              userVotedIndex: optionIndex,
              totalVotes: updatedOptions.reduce((acc, o) => acc + (o.votes || 0), 0),
            };

            const newMsgs = [...msgs];
            newMsgs[idx] = { ...msg, pollData: updatedPoll };
            next[chatId] = newMsgs;
            changed = true;
          }
          break;
        }
      }
      return changed ? next : prev;
    });

    if (HAS_BACKEND_SESSION) {
      const result = await votePoll(messageId, optionIndex);
      if (result.success && result.data?.pollData) {
        setMessagesByChatId((prev) => {
          const next = { ...prev };
          for (const chatId in next) {
            const msgs = next[chatId];
            const idx = msgs.findIndex((m) => m.id === messageId || m.serverId === messageId);
            if (idx !== -1) {
              const newMsgs = [...msgs];
              newMsgs[idx] = { ...newMsgs[idx], pollData: result.data.pollData };
              next[chatId] = newMsgs;
              break;
            }
          }
          return next;
        });
      } else if (!result.success) {
        showToast(result.message || "Failed to record vote");
      }
    }
  };

  const handleProfileBack = () => {
    setIsSidePanelOpen(false);
    setShowMediaGalleryDirectly(false);
    updateMediaUrlRoute(false);
    if (profileSource === "conversation") {
      setMobileView("conversation");
    } else {
      setActiveMobileTab("chats");
      setMobileView("chats");
    }
  };

  const selectedChatId = selectedChat?.id;
  const selectedChatMessages = useMemo(
    () => withDayDividers(messagesByChatId[selectedChatId] || []),
    [messagesByChatId, selectedChatId]
  );

  const resolvedTheme =
    settings.theme === "system"
      ? (typeof window !== "undefined" && window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light")
      : settings.theme;

  return (
    <div
      className={`app mobile-view-${mobileView} font-size-${settings.fontSize || "md"}`}
      data-theme={resolvedTheme}
      data-accent={settings.accent || "amber"}
      data-wallpaper={settings.wallpaper || "default"}
    >
      <main className={`app-content ${isSidePanelOpen ? "has-side-panel" : ""} sidebar-is-collapsed`}>
        <Sidebar
          currentUser={currentUser}
          onToast={showToast}
          onOpenProfile={handleOpenUserProfile}
          activeView={sidebarActiveView}
          onSelectView={handleSidebarSelectView}
        />

        <div className={`chat-list-wrapper ${mobileView === "chats" ? "mobile-active" : ""} ${sidebarActiveView === "chat" ? "chat-view-active" : ""}`}>
          <ChatList
            chats={chats}
            selectedChat={selectedChat}
            onSelectChat={handleSelectChat}
            onNewChat={handleNewChat}
            onStartChat={HAS_BACKEND_SESSION ? handleStartDirectChat : undefined}
            onSearchContacts={HAS_BACKEND_SESSION ? searchChatContacts : undefined}
            onToast={showToast}
            onOpenSettingsSection={handleOpenSettingsSection}
            onMarkAllRead={() => {
              if (HAS_BACKEND_SESSION) {
                chats.filter((c) => c.unread && c.conversationId).forEach((c) => markConversationRead(c.conversationId));
              }
              setChats((prev) => prev.map((c) => ({ ...c, unread: undefined })));
            }}
            currentUser={currentUser}
            onOpenProfile={handleOpenUserProfile}
            viewMode={chatViewMode}
            onToggleViewMode={handleToggleChatViewMode}
          />
        </div>

        <div className={`conversation-wrapper ${mobileView === "conversation" ? "mobile-active" : ""} ${sidebarActiveView === "chat" ? "chat-view-active" : ""}`}>
          <Conversation
            selectedChat={selectedChat}
            messages={selectedChatMessages}
            onSendMessage={handleSendMessage}
            onToast={showToast}
            onBack={() => setMobileView("chats")}
            onStartCall={handleStartCall}
            onOpenContactInfo={handleOpenContactInfo}
            onOpenMediaDocs={handleOpenMediaDocs}
            onOpenMute={handleOpenMute}
            onOpenClearChat={handleOpenClearChat}
            onOpenDeleteChat={handleOpenDeleteChat}
            onToggleFavourite={() => handleToggleFavourite(selectedChat?.id)}
            isFavourite={Boolean(selectedChat?.isFavourite)}
            isMuted={Boolean(getMuteInfo(selectedChat)?.muted)}
            onCreateEvent={handleCreateChatEvent}
            onOpenEventDetail={(evt) => setActiveEventDetail(evt)}
            contacts={chats}
            onOpenContactChat={(chatId) => {
              const target = chats.find((c) => c.id === chatId);
              if (target) handleSelectChat(target);
            }}
            onDeleteMessages={handleDeleteMessages}
            onReactMessage={handleReactMessage}
            onStarMessages={handleStarMessages}
            onEditMessage={handleEditMessage}
            onVotePoll={handleVotePoll}
          />
        </div>

        {/* Contact Info / Media / Mute / Clear Chat — shared secondary-panel slot:
            Right Panel on Desktop, Fullscreen on Mobile */}
        {(isSidePanelOpen || mobileView === "contact_info") && (
          <aside className={`contact-side-panel ${mobileView === "contact_info" ? "mobile-active" : ""}`}>
            {secondaryPanelKind === "mute" ? (
              <MuteNotificationsPanel
                chat={activeProfileContact || selectedChat}
                key={`mute-${(activeProfileContact || selectedChat)?.id}`}
                muteInfo={getMuteInfo(activeProfileContact || selectedChat)}
                onBack={handleCloseContactInfo}
                onClose={handleCloseContactInfo}
                onMute={(duration) =>
                  handleSetChatMute((activeProfileContact || selectedChat)?.id, duration)
                }
                onUnmute={() => handleUnmuteChat((activeProfileContact || selectedChat)?.id)}
              />
            ) : secondaryPanelKind === "clear" || secondaryPanelKind === "delete" ? (
              <ClearChatPanel
                mode={secondaryPanelKind}
                chat={activeProfileContact || selectedChat}
                onBack={handleCloseContactInfo}
                onClose={handleCloseContactInfo}
                onConfirmClear={() =>
                  secondaryPanelKind === "delete"
                    ? handleDeleteChat((activeProfileContact || selectedChat)?.id)
                    : handleClearChatMessages((activeProfileContact || selectedChat)?.id)
                }
              />
            ) : (
              <ContactProfile
                contact={activeProfileContact || selectedChat}
                messages={messagesByChatId[(activeProfileContact || selectedChat)?.id] || []}
                contacts={chats}
                onClose={handleCloseContactInfo}
                onBack={handleProfileBack}
                onToast={showToast}
                onUpdateContact={handleUpdateContact}
                onShareContact={handleShareContact}
                onStartCall={handleStartCall}
                initialShowMedia={showMediaGalleryDirectly}
                onMediaOpen={() => {
                  setShowMediaGalleryDirectly(true);
                  updateMediaUrlRoute(true, activeProfileContact || selectedChat);
                }}
                onMediaClose={() => {
                  setShowMediaGalleryDirectly(false);
                  updateMediaUrlRoute(false);
                }}
              />
            )}
          </aside>
        )}

        {(mobileView === "moments" || isMomentsClosing) && (
          <Moments
            isClosing={isMomentsClosing}
            onBack={handleCloseMoments}
            onToast={showToast}
            onOpenChatWithStoryReply={handleOpenChatWithStoryReply}
          />
        )}

        {(sidebarActiveView === "livestreaming" || mobileView === "livestreaming" || isLiveStreamingClosing) && (
          <LiveStreaming
            isClosing={isLiveStreamingClosing}
            onBack={handleCloseLiveStreaming}
            onToast={showToast}
          />
        )}

        {(sidebarActiveView === "channels" || mobileView === "channels" || isChannelsClosing) && (
          <Channels
            isClosing={isChannelsClosing}
            onBack={handleCloseChannels}
            onToast={showToast}
          />
        )}

        {(sidebarActiveView === "calls" || mobileView === "calls" || isCallsClosing) && (
          <Calls
            isClosing={isCallsClosing}
            onBack={handleCloseCalls}
            onStartCall={handleStartCall}
          />
        )}

        {(sidebarActiveView === "profile" || mobileView === "profile" || isProfileClosing) && (
          <Profile
            currentUser={currentUser}
            onUpdateCurrentUser={handleUpdateCurrentUser}
            onUploadAvatar={handleUploadAvatar}
            onRemoveAvatar={handleRemoveAvatar}
            isClosing={isProfileClosing}
            onBack={handleCloseProfile}
            onToast={showToast}
            onOpenSettings={() => {
              setIsMomentsClosing(false);
              setIsLiveStreamingClosing(false);
              setIsChannelsClosing(false);
              setIsProfileClosing(false);
              setIsSettingsClosing(false);
              setSidebarActiveView("settings");
              setMobileView("settings");
              setActiveMobileTab("settings");
            }}
          />
        )}

        {(sidebarActiveView === "settings" || mobileView === "settings" || isSettingsClosing) && (
          <Settings
            key={`settings-${settingsOpenKey}`}
            isClosing={isSettingsClosing}
            settings={settings}
            onUpdateSettings={setSettings}
            currentUser={currentUser}
            onUpdateCurrentUser={handleUpdateCurrentUser}
            onClose={handleCloseSettings}
            onToast={showToast}
            initialSection={settingsInitialSection}
            onOpenProfile={handleOpenUserProfile}
            onClearAllChats={() => {
              setMessagesByChatId({});
            }}
          />
        )}

        {(sidebarActiveView === "calendar" || mobileView === "calendar" || isCalendarClosing) && (
          <Calendar
            isClosing={isCalendarClosing}
            onBack={handleCloseCalendar}
            onToast={showToast}
            events={calendarEvents}
            onUpdateEvents={setCalendarEvents}
            onNavigateToChat={(chatId) => {
              const targetChat = chats.find((c) => c.id === chatId) || chats[0];
              if (targetChat) {
                handleSelectChat(targetChat);
              }
            }}
          />
        )}
      </main>

      {/* Mobile Bottom Navigation */}
      {(mobileView === "chats" ||
        mobileView === "calls" ||
        mobileView === "moments" ||
        mobileView === "livestreaming" ||
        mobileView === "channels" ||
        mobileView === "calendar" ||
        mobileView === "settings" ||
        mobileView === "profile" ||
        (mobileView === "contact_info" && profileSource === "bottom_nav")) && (
          <MobileNav
            currentUser={currentUser}
            activeTab={activeMobileTab}
            onSelectTab={handleSelectMobileTab}
            onToast={showToast}
          />
        )}

      {activeEventDetail && (
        <EventDetailModal
          event={activeEventDetail}
          onClose={() => setActiveEventDetail(null)}
          onToast={showToast}
        />
      )}

      {/* Above every view, so a call survives switching between chats and tabs */}
      {activeCall && (
        <CallOverlay call={activeCall} onEnd={handleEndCall} onToast={showToast} />
      )}

      <Toast message={toastMessage} />
    </div>
  );
}

export default App;