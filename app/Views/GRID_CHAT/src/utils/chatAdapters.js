// Convert chat API data (grid_chat_db) into the shapes the existing UI components use.

import { formatChatListTime, formatDayLabel, formatMessageTime, localDayKey } from "./time";

const PREVIEW_LABELS = {
  image: "📷 Photo",
  video: "🎥 Video",
  file: "📄 Document",
  audio: "🎵 Audio",
  voice: "🎤 Voice message",
  location: "📍 Location",
  contact: "👤 Contact",
  poll: "📊 Poll",
  event: "📅 Event",
  call: "📞 Call",
};

/**
 * Shown in place of a message deleted for everyone. No icon in the text: the
 * bubble already draws one (`fa-ban` in Message.jsx), and the chat-list preview
 * and quote card read better as plain text.
 */
export const DELETED_MESSAGE_TEXT = "This message was deleted";

const MB = 1024 * 1024;

// Same limits as MediaController on the server
export const MEDIA_LIMITS = {
  image: 10 * MB,
  video: 100 * MB,
  audio: 25 * MB,
  voice: 25 * MB,
  file: 100 * MB,
};

export const mediaPreviewLabel = (messageType) => PREVIEW_LABELS[messageType] || "";

export const formatFileSize = (bytes) => {
  if (!Number.isFinite(bytes) || bytes <= 0) return "";
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < MB) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / MB).toFixed(1)} MB`;
};


// "data:image/jpeg;base64,..." (camera capture) → Blob
const dataUrlToBlob = (dataUrl) => {
  const [header, base64] = dataUrl.split(",");
  const mime = header.match(/data:([^;]+)/)?.[1] || "application/octet-stream";
  const bytes = atob(base64);
  const buffer = new Uint8Array(bytes.length);
  for (let i = 0; i < bytes.length; i += 1) buffer[i] = bytes.charCodeAt(i);
  return new Blob([buffer], { type: mime });
};

const EXTENSION_BY_MIME = {
  "image/jpeg": "jpg",
  "image/png": "png",
  "image/webp": "webp",
  "audio/webm": "webm",
  "audio/ogg": "ogg",
  "audio/mp4": "m4a",
};

const defaultFileName = (messageType, mime) => {
  const extension = EXTENSION_BY_MIME[String(mime).split(";")[0]] || "bin";
  return `${messageType === "voice" ? "voice-note" : messageType}-${Date.now()}.${extension}`;
};

const previewText = (lastMessage, isGroup) => {
  if (!lastMessage) return "";
  // Group notices already read as a sentence ("Admin added Ravi")
  if (lastMessage.messageType === "system") return lastMessage.text || "";
  if (lastMessage.isDeleted) return DELETED_MESSAGE_TEXT;
  // A call's body already reads as a sentence ("Voice call"), so don't prefix
  // it with the generic label as well
  if (lastMessage.messageType === "call") {
    const summary = `📞 ${lastMessage.text || "Call"}`;
    return lastMessage.isOutgoing ? `You: ${summary}` : summary;
  }

  const label = PREVIEW_LABELS[lastMessage.messageType];
  const body = lastMessage.text || "";
  const text = label ? (body ? `${label}: ${body}` : label) : body;

  if (lastMessage.isOutgoing) return `You: ${text}`;
  return isGroup && lastMessage.senderName ? `${lastMessage.senderName}: ${text}` : text;
};

// API conversation → chat list item
export const toChat = (conversation) => ({
  id: conversation.id,
  conversationId: conversation.id,
  userId: conversation.userId,
  isGroup: conversation.isGroup,
  name: conversation.name,
  // "" = no photo → components show the name's initials (utils/avatar.js)
  avatar: conversation.avatar || "",
  memberCount: conversation.memberCount,
  myRole: conversation.myRole,
  text: previewText(conversation.lastMessage, conversation.isGroup),
  type: conversation.lastMessage?.messageType === "voice" ? "voice" : "text",
  voice: conversation.lastMessage?.messageType === "voice" ? PREVIEW_LABELS.voice : undefined,
  time: formatChatListTime(conversation.lastMessageAt),
  lastMessageAt: conversation.lastMessageAt,
  // ChatCard renders the badge only for a truthy value
  unread: conversation.unread > 0 ? conversation.unread : undefined,
  isFavourite: conversation.isFavourite,
  isArchived: conversation.isArchived,
  isMuted: conversation.isMuted,
  mutedUntil: conversation.mutedUntil,
  isDeleted: conversation.isDeleted,
  status: conversation.isDeleted ? "Account deleted" : conversation.isGroup ? `${conversation.memberCount} members` : "",
});

// Attachment → the fields Message.jsx renders for each media type
const mediaFields = (type, attachment) => {
  if (!attachment) return {};
  const common = { fileName: attachment.fileName, fileSize: formatFileSize(attachment.size) };
  switch (type) {
    case "image":
      return { ...common, image: attachment.url };
    case "video":
      return { ...common, video: attachment.url };
    case "audio":
      return { ...common, audio: attachment.url };
    case "voice":
      return { ...common, audioUrl: attachment.url, duration: attachment.duration ?? 0 };
    case "file":
      return { ...common, fileUrl: `${attachment.url}?download=1` };
    default:
      return {};
  }
};

/**
 * The quoted card above a reply. Two things produce one:
 *  - a reply to a message — the server sends `replyTo` with the original's
 *    author and text (media and deleted originals have no text, so fall back
 *    to the type label);
 *  - a reply to a status — an ordinary text message carrying
 *    `metadata.statusReply`, since a status is not a message and cannot be
 *    pointed at by reply_to_message_id.
 */
const toQuotedCard = (message) => {
  const replyTo = message.replyTo;
  if (replyTo) {
    return {
      id: replyTo.id,
      sender: replyTo.sender || "Message",
      text: replyTo.isDeleted
        ? DELETED_MESSAGE_TEXT
        : replyTo.text || PREVIEW_LABELS[replyTo.messageType] || "Message",
      messageType: replyTo.messageType,
    };
  }

  const status = message.metadata?.statusReply;
  if (!status) return undefined;

  // Incoming means the viewer is the one whose status was replied to
  const owner = message.type === "incoming" ? "Your status" : `${status.ownerName || "Their"}'s status`;
  const label = status.type === "text" ? status.text : PREVIEW_LABELS[status.type];

  return {
    id: undefined, // a status has no message to scroll to
    sender: owner,
    text: label || "Status",
    isStatus: true,
  };
};

// API message → conversation message
export const toUiMessage = (message) => {
  const metadata = message.isDeleted ? null : message.metadata;
  const type = message.messageType;

  // Group notice: rendered as a centred line, never "outgoing"/"incoming"
  if (type === "system") {
    return {
      id: message.id,
      serverId: message.id,
      conversationId: message.conversationId,
      type: "system",
      messageType: "system",
      text: message.text || "",
      createdAt: message.createdAt,
      time: formatMessageTime(message.createdAt),
    };
  }

  return {
    ...(message.isDeleted ? {} : mediaFields(type, message.attachments?.[0])),
    id: message.id,
    serverId: message.id,
    clientId: message.clientId || undefined,
    conversationId: message.conversationId,
    senderId: message.senderId,
    senderName: message.sender?.name,
    senderAvatar: message.sender?.avatar || "",
    type: message.type,
    messageType: type,
    text: message.isDeleted ? DELETED_MESSAGE_TEXT : message.text || "",
    location: type === "location" ? metadata : undefined,
    contactData: type === "contact" ? metadata : undefined,
    pollData: type === "poll" ? metadata : undefined,
    eventData: type === "event" ? metadata : undefined,
    callData: type === "call" ? metadata : undefined,
    // Without this the quote renders on the optimistic copy and then vanishes
    // the moment the server row replaces it
    replyTo: toQuotedCard(message),
    replyToId: message.replyToId ?? undefined,
    attachments: message.attachments || [],
    isDeleted: message.isDeleted,
    isEdited: message.isEdited,
    createdAt: message.createdAt,
    time: formatMessageTime(message.createdAt),
    status: message.isRead ? "read" : "sent",
    check: Boolean(message.isRead),
  };
};

/**
 * What the composer produced → send payload, or null for media the server
 * can't receive yet (photos, video, files, audio, voice).
 */
export const toSendPayload = (textOrMessage) => {
  if (typeof textOrMessage !== "object" || textOrMessage === null) {
    const body = String(textOrMessage || "").trim();
    return body ? { messageType: "text", body } : null;
  }

  const message = textOrMessage;
  // Carried on every branch — dropping it here is what stopped replies being
  // stored at all, even though the server has always accepted the field.
  const replyToMessageId = message.replyTo?.id;

  switch (message.messageType) {
    case undefined:
    case "text":
      if (!message.text?.trim()) return null;
      return {
        messageType: "text",
        body: message.text.trim(),
        replyToMessageId,
        // A status reply travels as a normal text message; the status it
        // answers rides along in metadata
        metadata: message.statusReply ? { statusReply: message.statusReply } : undefined,
      };
    case "location":
      return { messageType: "location", body: message.text || "", metadata: message.location, replyToMessageId };
    case "contact":
      return { messageType: "contact", body: message.text || "", metadata: message.contactData, replyToMessageId };
    case "poll":
      return { messageType: "poll", body: message.text || "", metadata: message.pollData, replyToMessageId };
    case "event":
      return { messageType: "event", body: message.text || "", metadata: message.eventData, replyToMessageId };
    case "call":
      // body is only the chat-list preview; callData is the real record
      return { messageType: "call", body: message.text || "", metadata: message.callData };
    default:
      return null;
  }
};

const MEDIA_TYPES = ["image", "video", "audio", "voice", "file"];

/**
 * What the composer produced → media upload payload, or null if it isn't media.
 * The composer passes the original File/Blob as `file`; camera photos arrive as a data URL.
 */
export const toMediaUpload = (message) => {
  if (typeof message !== "object" || message === null || !MEDIA_TYPES.includes(message.messageType)) {
    return null;
  }

  let file = message.file;
  if (!file && typeof message.image === "string" && message.image.startsWith("data:")) {
    file = dataUrlToBlob(message.image);
  }
  if (!file) return null;

  return {
    messageType: message.messageType,
    file,
    fileName: message.fileName || file.name || defaultFileName(message.messageType, file.type),
    body: message.text || "",
    duration: message.messageType === "voice" ? Math.round(message.duration || 0) : undefined,
  };
};

const messageKey = (message) =>
  message.clientId ? `client:${message.clientId}` : `server:${message.serverId ?? message.id}`;

const compareMessages = (a, b) => {
  if (a.serverId && b.serverId) return a.serverId - b.serverId;
  if (a.serverId) return -1; // unsent messages stay at the bottom
  if (b.serverId) return 1;
  return String(a.createdAt || "").localeCompare(String(b.createdAt || ""));
};

// Merge server messages into a conversation; replaces optimistic copies by clientId
export const mergeMessages = (existing = [], incoming = []) => {
  const byKey = new Map();
  existing.forEach((message) => byKey.set(messageKey(message), message));
  incoming.forEach((message) => byKey.set(messageKey(message), message));
  return [...byKey.values()].sort(compareMessages);
};

// Mark outgoing messages up to readUpToMessageId as read
export const applyReadReceipt = (messages = [], readUpToMessageId) =>
  messages.map((message) =>
    message.type === "outgoing" && message.serverId && message.serverId <= readUpToMessageId && message.status !== "read"
      ? { ...message, status: "read", check: true }
      : message
  );

// Insert "Today" / "Yesterday" / date dividers (only for API messages that have createdAt)
export const withDayDividers = (messages = []) => {
  if (!messages.some((message) => message.createdAt)) return messages;

  const result = [];
  let currentDay = "";
  messages.forEach((message) => {
    const day = localDayKey(message.createdAt);
    if (day && day !== currentDay) {
      currentDay = day;
      result.push({ id: `divider-${day}`, type: "divider", text: formatDayLabel(message.createdAt) });
    }
    result.push(message);
  });
  return result;
};
