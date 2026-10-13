import { useState } from "react";

function EventDetailModal({
  event,
  onClose,
  onRsvp,
  onVotePoll,
  onDeleteEvent,
  onOpenChatWithContact,
  onJoinCall,
  onWatchLive,
  onToast,
}) {
  const [rsvpStatus, setRsvpStatus] = useState(event?.status || "going");
  const [poll, setPoll] = useState(event?.poll || null);

  if (!event) return null;

  const handleRsvpChange = (status) => {
    setRsvpStatus(status);
    onRsvp?.(event.id, status);
    onToast?.(`RSVP updated to "${status.toUpperCase()}" for ${event.title}`);
  };

  const handleVote = (optionId) => {
    if (!poll) return;
    const updatedOptions = poll.options.map((opt) =>
      opt.id === optionId ? { ...opt, votes: opt.votes + 1 } : opt
    );
    const updatedPoll = { ...poll, options: updatedOptions, userVoted: optionId };
    setPoll(updatedPoll);
    onVotePoll?.(event.id, updatedPoll);
    onToast?.("🗳️ Vote recorded!");
  };

  return (
    <div className="create-event-overlay" onClick={onClose}>
      <div className="create-event-modal event-detail-modal" onClick={(e) => e.stopPropagation()}>
        {/* Header */}
        <div className="create-event-header">
          <div className="modal-title-row">
            <div className="modal-icon-badge detail-badge">
              <i className="fa-solid fa-calendar-day"></i>
            </div>
            <div>
              <h3>Event Overview</h3>
              <p>{event.date} • {event.startTime} - {event.endTime || "End"}</p>
            </div>
          </div>
          <button className="modal-close-btn" onClick={onClose}>
            <i className="fa-solid fa-xmark"></i>
          </button>
        </div>

        {/* Modal Body */}
        <div className="event-detail-body">
          <div className="detail-hero-title">
            <span className={`timeline-cat-badge ${event.category}`}>
              {event.category.replace("_", " ")}
            </span>
            <h2>{event.title}</h2>
          </div>

          {event.description && (
            <p className="detail-description">{event.description}</p>
          )}

          {/* Location / Destination */}
          {event.location && (
            <div className="detail-meta-row">
              <i className="fa-solid fa-location-dot meta-icon text-purple"></i>
              <div>
                <strong>Location / Platform</strong>
                <span>{event.location}</span>
              </div>
            </div>
          )}

          {/* Contact / Participants */}
          {event.contactName && (
            <div className="detail-meta-row">
              <img
                src={`https://i.pravatar.cc/80?img=${event.contactAvatar || 12}`}
                alt={event.contactName}
                className="detail-avatar"
              />
              <div>
                <strong>{event.isGroup ? "Group Chat" : "Connected Contact"}</strong>
                <span>{event.contactName}</span>
              </div>
            </div>
          )}

          {/* Interactive RSVP Status Buttons */}
          <div className="rsvp-control-box">
            <span className="rsvp-label">Your Attendance Status:</span>
            <div className="rsvp-btn-group">
              <button
                type="button"
                className={`btn-rsvp going ${rsvpStatus === "going" ? "active" : ""}`}
                onClick={() => handleRsvpChange("going")}
              >
                <i className="fa-solid fa-check"></i> Going
              </button>
              <button
                type="button"
                className={`btn-rsvp maybe ${rsvpStatus === "maybe" ? "active" : ""}`}
                onClick={() => handleRsvpChange("maybe")}
              >
                <i className="fa-solid fa-question"></i> Maybe
              </button>
              <button
                type="button"
                className={`btn-rsvp decline ${rsvpStatus === "declined" ? "active" : ""}`}
                onClick={() => handleRsvpChange("declined")}
              >
                <i className="fa-solid fa-xmark"></i> Decline
              </button>
            </div>
          </div>

          {/* Group Event Poll (if any) */}
          {poll && (
            <div className="event-poll-box">
              <div className="poll-title-row">
                <i className="fa-solid fa-square-poll-vertical text-purple"></i>
                <h4>{poll.question}</h4>
              </div>
              <div className="poll-options-list">
                {poll.options.map((opt) => (
                  <button
                    key={opt.id}
                    type="button"
                    className={`poll-vote-btn ${poll.userVoted === opt.id ? "voted" : ""}`}
                    onClick={() => handleVote(opt.id)}
                  >
                    <span>{opt.text}</span>
                    <span className="vote-counter">{opt.votes} votes</span>
                  </button>
                ))}
              </div>
            </div>
          )}

          {/* Action Row */}
          <div className="detail-actions-row">
            {event.category === "chat_meeting" && (
              <button
                type="button"
                className="btn-detail-action primary"
                onClick={() => {
                  onJoinCall?.(event);
                  onClose();
                }}
              >
                <i className="fa-solid fa-video"></i>
                <span>Join Video Meeting</span>
              </button>
            )}

            {event.category === "live_stream" && (
              <button
                type="button"
                className="btn-detail-action live"
                onClick={() => {
                  onWatchLive?.(event);
                  onClose();
                }}
              >
                <i className="fa-solid fa-tower-broadcast"></i>
                <span>Watch Stream</span>
              </button>
            )}

            {event.contactId !== undefined && (
              <button
                type="button"
                className="btn-detail-action secondary"
                onClick={() => {
                  onOpenChatWithContact?.(event.contactId);
                  onClose();
                }}
              >
                <i className="fa-solid fa-comment-dots"></i>
                <span>Open Conversation</span>
              </button>
            )}

            <button
              type="button"
              className="btn-delete-event"
              onClick={() => {
                onDeleteEvent?.(event.id);
                onClose();
              }}
              title="Delete Event"
            >
              <i className="fa-solid fa-trash-can"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}

export default EventDetailModal;
