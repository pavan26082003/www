// Message action helpers (frontend only): which actions a message supports,
// copy text, reactions, reply previews, delete and forward state changes.

import { DELETED_MESSAGE_TEXT } from "./chatAdapters";

export const QUICK_REACTIONS = ["👍", "❤️", "😂", "😮", "😢", "🙏"];

const TYPE_LABELS = {
  text: "Text",
  image: "Photo",
  video: "Video",
  voice: "Voice message",
  audio: "Audio",
  file: "Document",
  location: "Location",
  contact: "Contact",
  poll: "Poll",
  event: "Event",
  finished: "Document",
};

// Messages that are not real chat messages can't be acted on
export const isActionableMessage = (message) =>
  Boolean(message) && message.type !== "divider" && message.type !== "system";

export const isOutgoing = (message) => message?.type === "outgoing";

export const messageTypeLabel = (message) => {
  if (message?.isDeleted) return "Deleted message";
  if (message?.image && !message.messageType) return TYPE_LABELS.image;
  return TYPE_LABELS[message?.messageType] || TYPE_LABELS.text;
};

const isTextMessage = (message) => !message.messageType || message.messageType === "text";

/** Short one-line description used for reply quotes, pinned bar and forward toasts. */
export const getPreviewText = (message) => {
  if (!message) return "";
  if (message.isDeleted) return DELETED_MESSAGE_TEXT;
  const caption = message.text ? `: ${message.text}` : "";
  switch (message.messageType) {
    case "image":
      return `📷 Photo${caption}`;
    case "video":
      return `🎥 Video${caption}`;
    case "voice":
      return "🎤 Voice message";
    case "audio":
      return `🎵 ${message.fileName || "Audio"}`;
    case "file":
    case "finished":
      return `📄 ${message.fileName || "Document"}`;
    case "location":
      return "📍 Location";
    case "contact":
      return `👤 ${message.contactData?.name || "Contact"}`;
    case "poll":
      return `📊 ${message.pollData?.question || "Poll"}`;
    case "event":
      return `📅 ${message.eventData?.title || message.text || "Event"}`;
    default:
      if (message.image) return `📷 Photo${caption}`;
      return message.text || "";
  }
};

/** Text placed on the clipboard for "Copy", or "" when there is nothing sensible to copy. */
export const getCopyText = (message) => {
  if (!message || message.isDeleted) return "";
  switch (message.messageType) {
    case "voice":
      return "";
    case "file":
    case "finished":
    case "audio":
      return message.fileName || message.text || "";
    case "location":
      return message.location
        ? `${message.location.lat}, ${message.location.lng}`
        : "";
    case "contact":
      return message.contactData?.name || "";
    case "poll": {
      const options = (message.pollData?.options || []).map((option) => `- ${option.text}`);
      return [message.pollData?.question, ...options].filter(Boolean).join("\n");
    }
    case "event": {
      const event = message.eventData;
      return event ? [event.title, [event.date, event.time].filter(Boolean).join(" ")].filter(Boolean).join("\n") : message.text || "";
    }
    default:
      return message.text || "";
  }
};

/**
 * Actions shown in the context menu, in WhatsApp order.
 * Edit only for the current user's own text messages; nothing but info/delete for deleted ones.
 */
export const getMessageActions = (message) => {
  if (!isActionableMessage(message)) return [];

  if (message.isDeleted) {
    return ["info", "delete"];
  }

  const actions = ["info", "reply"];
  if (getCopyText(message)) actions.push("copy");
  actions.push("react", "forward", "pin");
  if (getCopyText(message)) actions.push("askAi");
  actions.push("star");

  const isSending = message.status === "pending" || message.status === "failed";
  if (isOutgoing(message) && isTextMessage(message) && !message.image && message.text && !isSending) {
    actions.push("edit");
  }

  actions.push("delete");
  return actions;
};

/**
 * One reaction per person (like WhatsApp): picking the same emoji again removes it,
 * picking another replaces it. reactions = { "👍": { count, mine } }.
 */
export const applyReaction = (message, emoji) => {
  const reactions = { ...(message.reactions || {}) };
  const previous = Object.keys(reactions).find((key) => reactions[key]?.mine);

  if (previous) {
    const count = reactions[previous].count - 1;
    if (count > 0) {
      reactions[previous] = { count, mine: false };
    } else {
      delete reactions[previous];
    }
  }

  if (previous !== emoji) {
    reactions[emoji] = { count: (reactions[emoji]?.count || 0) + 1, mine: true };
  }

  return { ...message, reactions };
};

export const reactionSummary = (message) =>
  Object.entries(message?.reactions || {})
    .filter(([, value]) => value?.count > 0)
    .sort((a, b) => b[1].count - a[1].count)
    .map(([emoji, value]) => ({ emoji, count: value.count, mine: Boolean(value.mine) }));

export const toReplyPreview = (message, chatName = "") => ({
  id: message.id,
  serverId: message.serverId,
  senderName: isOutgoing(message) ? "You" : message.senderName || chatName || "Contact",
  text: getPreviewText(message),
  isOutgoing: isOutgoing(message),
});

/** "Delete for everyone" is only possible when every selected message is the user's own, sent message. */
export const canDeleteForEveryone = (messages) =>
  messages.length > 0 &&
  messages.every(
    (message) =>
      isOutgoing(message) && !message.isDeleted && message.status !== "pending" && message.status !== "failed"
  );

const MEDIA_FIELDS = [
  "image", "video", "audio", "audioUrl", "fileUrl", "fileName", "fileSize", "duration",
  "location", "contactData", "pollData", "eventData", "attachments", "metadata",
];

export const markDeletedForEveryone = (message) => {
  const deleted = {
    ...message,
    isDeleted: true,
    messageType: "text",
    text: "You deleted this message",
    reactions: undefined,
    isPinned: false,
    isStarred: false,
    isEdited: false,
    replyTo: undefined,
    replyToId: undefined,
  };
  MEDIA_FIELDS.forEach((field) => {
    delete deleted[field];
  });
  return deleted;
};

/** Local copy of a message for "Forward". */
export const buildForwardCopy = (message, id, createdAt, time) => ({
  ...message,
  id,
  serverId: undefined,
  clientId: undefined,
  conversationId: undefined,
  senderId: undefined,
  senderName: undefined,
  senderAvatar: undefined,
  type: "outgoing",
  forwarded: true,
  createdAt,
  time,
  status: undefined,
  check: false,
  progress: undefined,
  reactions: undefined,
  isPinned: false,
  pinnedAt: undefined,
  isStarred: false,
  isEdited: false,
  editedAt: undefined,
  replyTo: undefined,
  replyToId: undefined,
});

/** Copy text to the clipboard; resolves to true on success. */
export const copyToClipboard = async (text) => {
  if (!text) return false;
  try {
    if (navigator.clipboard?.writeText) {
      await navigator.clipboard.writeText(text);
      return true;
    }
  } catch {
    // fall back below (e.g. plain-http LAN address)
  }
  try {
    const textarea = document.createElement("textarea");
    textarea.value = text;
    textarea.setAttribute("readonly", "");
    textarea.style.position = "absolute";
    textarea.style.left = "-9999px";
    document.body.appendChild(textarea);
    textarea.select();
    const ok = document.execCommand("copy");
    document.body.removeChild(textarea);
    return ok;
  } catch {
    return false;
  }
};
