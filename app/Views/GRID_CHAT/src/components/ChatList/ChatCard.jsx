import { getAvatarUrl } from "../../utils/avatar";

function formatCompactTime(timeStr) {
  if (!timeStr) return "";
  const str = String(timeStr).trim();
  if (str.toLowerCase() === "offline" || str.toLowerCase() === "online") {
    return "";
  }
  // If it's already a short relative time like "2m ago", "1h ago", "11:55 am", "Yesterday"
  if (str.length <= 10 && !str.includes("/")) {
    return str;
  }
  const dateObj = new Date(str);
  if (!isNaN(dateObj.getTime())) {
    const now = new Date();
    const diffMs = now - dateObj;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return "now";
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24 && dateObj.getDate() === now.getDate()) {
      return dateObj.toLocaleTimeString([], { hour: "numeric", minute: "2-digit" });
    }
    if (diffDays === 1 || (diffHours < 48 && dateObj.getDate() === now.getDate() - 1)) {
      return "Yesterday";
    }
    if (diffDays < 7) {
      return `${diffDays}d ago`;
    }
    return dateObj.toLocaleDateString([], { month: "short", day: "numeric" });
  }
  return str.split(" ")[0]; // fallback to first token if too long
}

function ChatCard({ chat, selected, onClick, viewMode = "list" }) {
  const isMissedVideo = Boolean(
    chat.hasMissedVideoCall ||
    chat.missedCallType === "video" ||
    (chat.hasMissedCall && (chat.callType === "video" || (typeof chat.text === "string" && chat.text.toLowerCase().includes("video"))))
  );

  const isMissedAudio = Boolean(
    chat.hasMissedAudioCall ||
    chat.missedCallType === "audio" ||
    (!isMissedVideo && (
      chat.hasMissedCall ||
      chat.isMissedCall ||
      chat.isMissed ||
      chat.type === "missed_call" ||
      (typeof chat.text === "string" && chat.text.toLowerCase().trim() === "missed call") ||
      (typeof chat.subtitle === "string" && chat.subtitle.toLowerCase().includes("missed"))
    ))
  );

  const isMissedCall = isMissedVideo || isMissedAudio;
  const missedCallLabel = isMissedVideo ? "Missed video call" : "Missed call";
  const missedCallIcon = isMissedVideo ? "fa-solid fa-video" : "fa-solid fa-phone";

  if (viewMode === "grid") {
    // Extract last message text for the top note bubble
    let lastMsg =
      chat.type === "voice"
        ? "Voice message"
        : chat.text || "Start a conversation";

    if (lastMsg === "Click to start chatting") {
      lastMsg = "Hey there!";
    }

    const formattedTime = formatCompactTime(chat.time);

    // Clean name if email
    let displayName = chat.name || "";
    if (displayName.includes("@")) {
      displayName = displayName.split("@")[0];
    }

    const avatarUrl = getAvatarUrl(chat.avatar, 140, chat.name);

    return (
      <article
        className={`chat-card grid-card
          ${selected ? "selected" : ""}
          ${chat.faded ? "faded" : ""}
          ${isMissedCall ? "has-missed-call" : ""}
        `}
        onClick={onClick}
        title={`${displayName}: ${isMissedCall ? missedCallLabel : lastMsg}`}
      >
        {/* Top Note Bubble (Matching Reference Image) */}
        <div className={`grid-note-bubble ${isMissedCall ? "is-missed-call" : ""}`}>
          <div className="grid-note-icon">
            {isMissedCall ? (
              <i className={`${missedCallIcon} call-icon-missed`}></i>
            ) : chat.type === "voice" ? (
              <i className="fa-solid fa-bars-staggered voice-wave-icon"></i>
            ) : chat.image || chat.text?.toLowerCase().includes("photo") || chat.attachments?.some((a) => a.text?.includes("Photo")) ? (
              <i className="fa-regular fa-image photo-icon"></i>
            ) : (
              <i className="fa-solid fa-file-lines doc-icon"></i>
            )}
          </div>

          <div className="grid-note-content">
            {isMissedCall ? (
              <>
                <span className="grid-note-text grid-missed-title">{missedCallLabel}</span>
                {lastMsg && lastMsg !== "Missed call" && lastMsg !== "Missed video call" && lastMsg !== "Hey there!" && (
                  <span className="grid-missed-sub" title={lastMsg}>{lastMsg}</span>
                )}
              </>
            ) : (
              <span className="grid-note-text" title={lastMsg}>{lastMsg}</span>
            )}
            {formattedTime ? (
              <span className="grid-note-time">{formattedTime}</span>
            ) : null}
          </div>
        </div>

        {/* Profile Avatar Row */}
        <div className="grid-avatar-row">
          <div className="grid-avatar-wrap">
            <img
              src={avatarUrl}
              alt={displayName}
            />
          </div>
        </div>

        {/* Contact Name below the profile */}
        <div className="grid-contact-name" title={chat.name}>
          {displayName}
        </div>
      </article>
    );
  }

  return (
    <article
      className={`chat-card
        ${selected ? "selected" : ""}
        ${chat.faded ? "faded" : ""}
      `}
      onClick={onClick}
    >

      {/* Avatar */}
      <div className="avatar-wrap">
        <img
          src={getAvatarUrl(chat.avatar, 120, chat.name)}
          alt=""
        />

        {(chat.status === "writes" ||
          chat.status === "records voice message" ||
          chat.status === "online") && (
          <i className="online-dot"></i>
        )}
      </div>

      {/* Content */}
      <div className="chat-card-content">

        {/* Name + Time */}
        <div className="chat-topline">
          <strong className="chat-card-name">
            {chat.isGroup && (
              <i className="fa-solid fa-user-group group-icon-indicator" style={{ marginRight: "6px", fontSize: "11px", color: "var(--color-primary, #00a884)" }} title="Group"></i>
            )}
            {chat.name}
            {chat.isFavourite && (
              <i className="fa-solid fa-star" style={{ marginLeft: "6px", fontSize: "10.5px", color: "#f59e0b" }} title="Favourite"></i>
            )}
          </strong>
          <time>{chat.time}</time>
        </div>

        {/* Message Preview + Unread Badge on the same line */}
        <div className="chat-bottomline">
          <p className="chat-preview-text">
            {isMissedCall ? (
              <span style={{ color: "#ef4444", fontWeight: 600, display: "inline-flex", alignItems: "center", gap: "6px" }}>
                <i className={missedCallIcon} style={{ fontSize: "11px", color: "#ef4444" }}></i>
                {missedCallLabel}
              </span>
            ) : chat.type === "voice" ? (
              <>
                <i className="fa-solid fa-microphone" style={{ marginRight: "5px", color: "#10b981", fontSize: "11px" }}></i>
                {chat.voice?.replace("♟", "")?.trim() || "Voice message (01:15)"}
              </>
            ) : (
              chat.text || "Start a new conversation."
            )}
          </p>

          {chat.unread && (
            <span
              className={`unread ${
                chat.type === "voice" ? "red" : ""
              }`}
            >
              {chat.unread}
            </span>
          )}
        </div>

      </div>

    </article>
  );
}

export default ChatCard;