import { useState, useRef, useEffect } from "react";
import VoiceMessageBubble from "./VoiceMessageBubble.jsx";
import { getAvatarUrl } from "../../utils/avatar";
import { DELETED_MESSAGE_TEXT } from "../../utils/chatAdapters";
import { formatDuration } from "../../utils/time";

// Stable colour per group member for their name above messages
const SENDER_COLORS = ["#0ea5e9", "#8b5cf6", "#10b981", "#f97316", "#ec4899", "#14b8a6", "#6366f1", "#e11d48"];
const senderColor = (senderId) => SENDER_COLORS[Math.abs(Number(senderId) || 0) % SENDER_COLORS.length];

// A call that never connected has no duration to show
const CALL_STATUS_LABELS = {
  missed: "Missed call",
  cancelled: "Cancelled",
  declined: "Declined",
};

function Message({
  message,
  avatar,
  chatName = "",
  onOpenEventDetail,
  onOpenContactChat,
  highlighted = false,
  isGroup = false,
  onOpenActions,
  isSelectionMode = false,
  isSelected = false,
  onToggleSelect,
  onToggleReaction,
  onVotePoll,
  onStartCall,
}) {
  const [rsvpState, setRsvpState] = useState(
    message.eventData?.rsvps?.userRsvp || "going"
  );
  const [rsvpCounts, setRsvpCounts] = useState({
    going: message.eventData?.rsvps?.going || 1,
    maybe: message.eventData?.rsvps?.maybe || 0,
    declined: message.eventData?.rsvps?.declined || 0,
  });
  const [pollVotedIndex, setPollVotedIndex] = useState(
    message.pollData?.userVotedIndex ?? null
  );
  const [pollCounts, setPollCounts] = useState(
    (message.pollData?.options || []).map((opt) => opt.votes || 0)
  );

  useEffect(() => {
    if (message.pollData) {
      if (message.pollData.userVotedIndex !== undefined) {
        setPollVotedIndex(message.pollData.userVotedIndex);
      }
      if (Array.isArray(message.pollData.options)) {
        setPollCounts(message.pollData.options.map((opt) => opt.votes || 0));
      }
    }
  }, [message.pollData]);

  const handlePollVote = (index) => {
    if (pollVotedIndex === index) return;
    setPollCounts((prev) => {
      const next = [...prev];
      if (pollVotedIndex !== null && next[pollVotedIndex] > 0) {
        next[pollVotedIndex] -= 1;
      }
      next[index] = (next[index] || 0) + 1;
      return next;
    });
    setPollVotedIndex(index);
    onVotePoll?.(message.id || message.serverId, index);
  };

  if (message.type === "divider") {
    return (
      <div className="date-divider" id={`message-${message.id}`}>
        <span>{message.text}</span>
      </div>
    );
  }

  // Group notice ("Admin added Ravi")
  if (message.type === "system") {
    return (
      <div className={`system-notice ${highlighted ? "search-highlight" : ""}`} id={`message-${message.id}`}>
        <span title={message.time}>{message.text}</span>
      </div>
    );
  }

  const isIncoming = message.type === "incoming";
  // In groups each incoming message shows who sent it
  const showSender = isGroup && isIncoming && Boolean(message.senderName);
  const isEventMessage =
    message.messageType === "event" ||
    message.messageType === "live_invite" ||
    message.messageType === "channel_event" ||
    message.messageType === "reel_schedule" ||
    Boolean(message.eventData);

  const eventData = message.eventData || (isEventMessage ? {
    id: message.id || "evt-chat-custom",
    title: message.text || "Scheduled Event",
    category: message.messageType === "live_invite" ? "live" : message.messageType === "channel_event" ? "channel" : "meeting",
    date: "2026-09-12",
    time: "17:00",
    duration: "1 hour",
    withPerson: isIncoming ? "You" : "Contact",
    location: "In-App Room",
    description: "Scheduled via chat conversation.",
  } : null);

  const handleRsvp = (status) => {
    if (status === rsvpState) return;
    setRsvpCounts((prev) => {
      const next = { ...prev };
      if (rsvpState && next[rsvpState] > 0) next[rsvpState]--;
      next[status] = (next[status] || 0) + 1;
      return next;
    });
    setRsvpState(status);
  };

  const bubbleWrapperRef = useRef(null);
  const longPressTimerRef = useRef(null);
  const touchStartPosRef = useRef({ x: 0, y: 0 });

  const handleTouchStart = (e) => {
    if (isSelectionMode || message.isDeleted) return;
    const touch = e.touches[0];
    touchStartPosRef.current = { x: touch.clientX, y: touch.clientY };
    longPressTimerRef.current = setTimeout(() => {
      const rect = bubbleWrapperRef.current?.getBoundingClientRect();
      onOpenActions?.(message, rect);
    }, 450);
  };

  const handleTouchMove = (e) => {
    if (!longPressTimerRef.current) return;
    const touch = e.touches[0];
    const dx = Math.abs(touch.clientX - touchStartPosRef.current.x);
    const dy = Math.abs(touch.clientY - touchStartPosRef.current.y);
    if (dx > 10 || dy > 10) {
      clearTimeout(longPressTimerRef.current);
      longPressTimerRef.current = null;
    }
  };

  const handleTouchEnd = () => {
    if (longPressTimerRef.current) {
      clearTimeout(longPressTimerRef.current);
      longPressTimerRef.current = null;
    }
  };

  const handleContextMenu = (e) => {
    if (isSelectionMode || message.isDeleted) return;
    e.preventDefault();
    const rect = bubbleWrapperRef.current?.getBoundingClientRect();
    onOpenActions?.(message, rect);
  };

  const handleTriggerClick = (e) => {
    e.stopPropagation();
    const rect = bubbleWrapperRef.current?.getBoundingClientRect();
    onOpenActions?.(message, rect);
  };

  return (
    <div
      id={`message-${message.id}`}
      className={`message-row ${
        isIncoming ? "incoming" : "outgoing"
      } ${message.messageType === "file" ? "file-row" : ""} ${isEventMessage ? "event-row" : ""} ${
        highlighted ? "search-highlight" : ""
      } ${isSelectionMode ? "in-selection-mode" : ""} ${isSelected ? "is-selected-row" : ""}`}
      onClick={isSelectionMode ? () => onToggleSelect?.(message.id) : undefined}
      onContextMenu={handleContextMenu}
      onTouchStart={handleTouchStart}
      onTouchMove={handleTouchMove}
      onTouchEnd={handleTouchEnd}
    >
      {/* Selection Checkbox */}
      {isSelectionMode && (
        <div className="msg-selection-checkbox-wrap" onClick={() => onToggleSelect?.(message.id)}>
          <div className={`msg-selection-checkbox ${isSelected ? "checked" : ""}`}>
            {isSelected && <i className="fa-solid fa-check"></i>}
          </div>
        </div>
      )}

      {/* Incoming avatar (group chats only) */}
      {isGroup && isIncoming && (
        <img
          className="mini-avatar"
          src={
            showSender
              ? getAvatarUrl(message.senderAvatar, 80, message.senderName)
              : getAvatarUrl(avatar, 80, chatName)
          }
          alt={showSender ? message.senderName : ""}
          title={showSender ? message.senderName : undefined}
        />
      )}

      <div className="message-bubble-wrapper" ref={bubbleWrapperRef}>
        {/* Quoted / Replied-To Message Preview */}
        {message.replyTo && (
          <div className="msg-quoted-card">
            <div className="msg-quoted-bar"></div>
            <div className="msg-quoted-content">
              <span className="msg-quoted-author">{message.replyTo.sender || "Message"}</span>
              <p className="msg-quoted-snippet">{message.replyTo.text || "Quoted message"}</p>
            </div>
          </div>
        )}

        {/* Action Trigger Chevron Button (Desktop Hover) */}
        {!isSelectionMode && !message.isDeleted && (
          <button
            type="button"
            className="msg-action-trigger-btn"
            onClick={handleTriggerClick}
            title="Message options"
            aria-label="Message options"
          >
            <i className="fa-solid fa-chevron-down"></i>
          </button>
        )}

        {showSender && (
          <span
            className="group-sender-name"
            style={{ "--sender-color": senderColor(message.senderId) }}
          >
            {message.senderName}
          </span>
        )}

        {/* Deleted Message State */}
        {message.isDeleted ? (
          <div className={`bubble ${isIncoming ? "blue-bubble" : "white-bubble"} is-deleted-bubble`}>
            <i className="fa-solid fa-ban deleted-icon"></i>
            <span className="deleted-text">{message.text || DELETED_MESSAGE_TEXT}</span>
          </div>
        ) : message.messageType === "call" ? (
          /* Finished voice/video call. The stored `direction` is the caller's
             record; what each side SEES is derived from whose message it is,
             so the caller reads "Outgoing" and the callee "Incoming". */
          (() => {
            const call = message.callData || {};
            const isVideo = call.call_type === "video";
            const status = call.status || "completed";
            const unanswered = status !== "completed";
            const label = CALL_STATUS_LABELS[status] || (isIncoming ? "Incoming" : "Outgoing");

            return (
              <div
                className={`bubble call-bubble ${isIncoming ? "blue-bubble" : "white-bubble"} ${
                  unanswered ? "is-unanswered" : ""
                }`}
                role="button"
                tabIndex={0}
                title={`Call back ${chatName}`}
                onClick={() => onStartCall?.(isVideo ? "video" : "audio")}
                onKeyDown={(e) => {
                  if (e.key === "Enter" || e.key === " ") {
                    e.preventDefault();
                    onStartCall?.(isVideo ? "video" : "audio");
                  }
                }}
              >
                <span className="call-bubble-icon">
                  <i className={`fa-solid ${isVideo ? "fa-video" : "fa-phone"}`}></i>
                </span>

                <span className="call-bubble-body">
                  <strong className="call-bubble-title">{isVideo ? "Video call" : "Audio call"}</strong>
                  <span className="call-bubble-status">
                    <i
                      className={`fa-solid ${
                        unanswered
                          ? "fa-arrow-down-left"
                          : isIncoming
                          ? "fa-arrow-down-left"
                          : "fa-arrow-up-right"
                      } call-bubble-arrow`}
                    ></i>
                    {label}
                  </span>
                  {status === "completed" && call.duration > 0 && (
                    <span className="call-bubble-duration">{formatDuration(call.duration)}</span>
                  )}
                </span>
              </div>
            );
          })()
        ) : isEventMessage && eventData ? (
          <div className="bubble chat-event-bubble">
            <div className="chat-event-header">
              <div className="chat-event-icon-badge">
                <i
                  className={`fa-solid ${
                    eventData.category === "live"
                      ? "fa-video"
                      : eventData.category === "channel"
                      ? "fa-bullhorn"
                      : eventData.category === "reels"
                      ? "fa-film"
                      : "fa-calendar-check"
                  }`}
                ></i>
              </div>
              <div className="chat-event-header-info">
                <span className="chat-event-category-pill">
                  {eventData.category === "live"
                    ? "🔴 LIVE INVITATION"
                    : eventData.category === "channel"
                    ? "📢 CHANNEL EVENT"
                    : eventData.category === "reels"
                    ? "🎬 REEL PREMIERE"
                    : "📅 MEETING INVITATION"}
                </span>
                <h4 className="chat-event-title">{eventData.title}</h4>
              </div>
            </div>

            <div className="chat-event-details">
              <div className="chat-event-detail-row">
                <i className="fa-regular fa-clock"></i>
                <span>
                  {eventData.date} • {eventData.time} ({eventData.duration || "1 hr"})
                </span>
              </div>
              {eventData.withPerson && (
                <div className="chat-event-detail-row">
                  <i className="fa-regular fa-user"></i>
                  <span>With {eventData.withPerson}</span>
                </div>
              )}
              {eventData.location && (
                <div className="chat-event-detail-row">
                  <i className="fa-solid fa-location-dot"></i>
                  <span>{eventData.location}</span>
                </div>
              )}
              {eventData.description && (
                <p className="chat-event-desc">{eventData.description}</p>
              )}
            </div>

            {/* Attendance / RSVP options */}
            <div className="chat-event-rsvp-section">
              <div className="chat-event-rsvp-label">
                <span>Will you attend?</span>
                <span className="rsvp-tally">
                  {rsvpCounts.going} going
                </span>
              </div>
              <div className="chat-event-rsvp-buttons">
                <button
                  type="button"
                  className={`rsvp-btn ${rsvpState === "going" ? "active-going" : ""}`}
                  onClick={() => handleRsvp("going")}
                >
                  <i className="fa-solid fa-check"></i> Going
                </button>
                <button
                  type="button"
                  className={`rsvp-btn ${rsvpState === "maybe" ? "active-maybe" : ""}`}
                  onClick={() => handleRsvp("maybe")}
                >
                  <i className="fa-solid fa-question"></i> Maybe
                </button>
                <button
                  type="button"
                  className={`rsvp-btn ${rsvpState === "declined" ? "active-declined" : ""}`}
                  onClick={() => handleRsvp("declined")}
                >
                  <i className="fa-solid fa-xmark"></i> Decline
                </button>
              </div>
            </div>

            {/* Synced with Social Calendar Badge & Action */}
            <div className="chat-event-footer">
              <span className="chat-event-synced-tag">
                <i className="fa-solid fa-calendar-check"></i> Synced to Calendar
              </span>
              <button
                type="button"
                className="chat-event-action-btn"
                onClick={() => onOpenEventDetail && onOpenEventDetail(eventData)}
              >
                View Details
              </button>
            </div>
          </div>
        ) : message.messageType === "image" || message.image ? (
          /* Image message */
          <div className="bubble image-bubble">
            <img
              src={message.image}
              alt={message.text || "Media"}
              className="chat-image-preview"
            />
            {message.text && <p className="image-caption">{message.text}</p>}
          </div>
        ) : message.messageType === "file" ? (
          message.fileUrl ? (
            <a
              className="bubble blue-bubble file-bubble file-bubble-link"
              href={message.fileUrl}
              download={message.fileName}
              target="_blank"
              rel="noreferrer"
              title={`Open ${message.fileName}`}
            >
              <div className="file-icon">
                <i className="fa-solid fa-file-zipper"></i>
              </div>
              <div>
                <strong>{message.fileName}</strong>
                <small>{message.fileSize}</small>
              </div>
            </a>
          ) : (
            <div className="bubble blue-bubble file-bubble">
              <div className="file-icon">
                <i className="fa-solid fa-file-zipper"></i>
              </div>
              <div>
                <strong>{message.fileName}</strong>
                <small>{message.fileSize}</small>
              </div>
            </div>
          )
        ) : message.messageType === "video" ? (
          <div className="bubble video-bubble">
            <video src={message.video} className="chat-video-preview" controls preload="metadata" />
            {message.fileName && <span className="video-file-name">{message.fileName}</span>}
          </div>
        ) : message.messageType === "voice" ? (
          <VoiceMessageBubble
            audioUrl={message.audioUrl}
            duration={message.duration}
            outgoing={!isIncoming}
          />
        ) : message.messageType === "audio" ? (
          <div className="bubble audio-bubble">
            <div className="audio-file-icon">
              <i className="fa-solid fa-music"></i>
            </div>
            <div className="audio-bubble-body">
              <strong className="audio-file-name">{message.fileName || "Audio message"}</strong>
              <audio src={message.audio} className="chat-audio-player" controls preload="metadata" />
            </div>
          </div>
        ) : message.messageType === "location" ? (
          <div className="bubble location-bubble">
            <div className="location-map-preview">
              <i className="fa-solid fa-location-dot location-pin-icon"></i>
            </div>
            <div className="location-bubble-body">
              <strong>Current Location</strong>
              <span className="location-coords">
                {message.location?.lat?.toFixed(5)}, {message.location?.lng?.toFixed(5)}
              </span>
              <a
                className="location-open-btn"
                href={`https://www.openstreetmap.org/?mlat=${message.location?.lat}&mlon=${message.location?.lng}#map=16/${message.location?.lat}/${message.location?.lng}`}
                target="_blank"
                rel="noreferrer"
              >
                <i className="fa-solid fa-map"></i> Open in Maps
              </a>
            </div>
          </div>
        ) : message.messageType === "contact" ? (
          <div className="bubble contact-bubble">
            <img
              className="contact-bubble-avatar"
              src={getAvatarUrl(message.contactData?.avatar, 100, message.contactData?.name)}
              alt={message.contactData?.name}
            />
            <div className="contact-bubble-body">
              <strong className="contact-bubble-name">{message.contactData?.name}</strong>
              <span className="contact-bubble-label">
                <i className="fa-solid fa-address-card"></i> GRID Contact
              </span>
            </div>
            {onOpenContactChat && (
              <button
                type="button"
                className="contact-bubble-message-btn"
                onClick={() => onOpenContactChat(message.contactData?.id)}
                title={`Message ${message.contactData?.name}`}
              >
                <i className="fa-solid fa-message"></i>
              </button>
            )}
          </div>
        ) : message.messageType === "poll" ? (
          <div className="bubble chat-poll-bubble">
            <div className="chat-poll-question-row">
              <i className="fa-solid fa-square-poll-vertical"></i>
              <span className="chat-poll-question">{message.pollData?.question}</span>
            </div>
            <div className="chat-poll-options-list">
              {(message.pollData?.options || []).map((opt, index) => {
                const totalVotes = pollCounts.reduce((a, b) => a + b, 0);
                const count = pollCounts[index] || 0;
                const percentage = totalVotes > 0 ? Math.round((count / totalVotes) * 100) : 0;
                const isSelected = pollVotedIndex === index;

                return (
                  <button
                    type="button"
                    key={index}
                    className={`chat-poll-option-btn ${isSelected ? "is-selected-vote" : ""}`}
                    onClick={() => handlePollVote(index)}
                  >
                    <div
                      className="chat-poll-fill-bar"
                      style={{ width: pollVotedIndex !== null ? `${percentage}%` : "0%" }}
                    ></div>
                    <div className="chat-poll-label-row">
                      <span>
                        {isSelected && <i className="fa-solid fa-check"></i>} {opt.text}
                      </span>
                      {pollVotedIndex !== null && <span>{percentage}%</span>}
                    </div>
                  </button>
                );
              })}
            </div>
            <div className="chat-poll-footer">
              <span>{pollCounts.reduce((a, b) => a + b, 0)} votes</span>
              <span>{pollVotedIndex !== null ? "Vote submitted" : "Tap to vote"}</span>
            </div>
          </div>
        ) : message.messageType === "finished" ? (
          /* Finished outgoing file */
          <div className="bubble white-bubble finished">
            {message.text}
            <br />
            <b>{message.fileName}</b>
            <span className="file-mini">
              <i className="fa-solid fa-file-arrow-down"></i>
            </span>
          </div>
        ) : (
          /* Normal message */
          <div
            className={`bubble ${
              isIncoming ? "blue-bubble" : "white-bubble"
            } ${message.compact ? "compact" : ""}`}
          >
            {message.text}
          </div>
        )}

        {/* Time */}
        <div className={`message-time ${!isIncoming ? "right" : ""}`}>
          {message.isStarred && (
            <span className="msg-starred-indicator" title="Starred">
              <i className="fa-solid fa-star"></i>
            </span>
          )}
          {message.isPinned && (
            <span className="msg-pinned-indicator" title="Pinned">
              <i className="fa-solid fa-thumbtack"></i>
            </span>
          )}
          {message.time}
          {!isIncoming && message.status === "pending" && (
            <span className="wa-checks is-pending" title="Sending">
              {typeof message.progress === "number" && (
                <span className="upload-progress">{message.progress}% </span>
              )}
              <i className="fa-regular fa-clock"></i>
            </span>
          )}
          {!isIncoming && message.status === "failed" && (
            <span className="wa-checks is-failed" title="Not sent">
              <i className="fa-solid fa-circle-exclamation"></i>
            </span>
          )}
          {!isIncoming && message.status === "sent" && (
            <span className="wa-checks is-sent" title="Sent">
              <i className="fa-solid fa-check"></i>
            </span>
          )}
          {!isIncoming && (message.status === undefined || message.status === "read") && message.check && (
            <span className="wa-checks" title="Read">
              <i className="fa-solid fa-check-double"></i>
            </span>
          )}
        </div>

        {/* Reactions Display Pills */}
        {message.reactions && message.reactions.length > 0 && (
          <div className={`msg-reactions-badge-row ${!isIncoming ? "right" : ""}`}>
            {message.reactions.map((r, idx) => (
              <button
                key={idx}
                type="button"
                className={`msg-reaction-pill ${r.userReacted ? "is-user-reacted" : ""}`}
                onClick={(e) => {
                  e.stopPropagation();
                  onToggleReaction?.(message.id, r.emoji);
                }}
                title={`${r.count} reaction${r.count > 1 ? "s" : ""}`}
              >
                <span className="reaction-pill-emoji">{r.emoji}</span>
                {r.count > 1 && <span className="reaction-pill-count">{r.count}</span>}
              </button>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}

export default Message;