import { useMemo } from "react";

function DayTimelineView({
  selectedDate,
  events = [],
  onOpenEventDetail,
  onOpenChatWithContact,
  onJoinCall,
  onWatchLive,
  onToggleTaskComplete,
  onToast,
}) {
  // Filter events for selectedDate (or upcoming if none on selectedDate)
  const dayEvents = useMemo(() => {
    const matched = events.filter((e) => e.date === selectedDate);
    if (matched.length > 0) return matched;
    // If no events exactly on selectedDate, sort all events chronologically
    return [...events].sort((a, b) => (a.date + a.startTime).localeCompare(b.date + b.startTime));
  }, [events, selectedDate]);

  // Group events into time periods
  const groupedEvents = useMemo(() => {
    const groups = {
      morning: [],
      afternoon: [],
      evening: [],
      night: [],
    };

    dayEvents.forEach((evt) => {
      const hour = parseInt(evt.startTime ? evt.startTime.slice(0, 2) : "12", 10);
      if (hour < 12) groups.morning.push(evt);
      else if (hour < 17) groups.afternoon.push(evt);
      else if (hour < 21) groups.evening.push(evt);
      else groups.night.push(evt);
    });

    return groups;
  }, [dayEvents]);

  const renderEventItem = (evt) => {
    return (
      <article
        key={evt.id}
        className={`timeline-card category-${evt.category} ${evt.isCompleted ? "is-task-done" : ""}`}
        onClick={() => onOpenEventDetail(evt)}
      >
        <div className="timeline-card-glow-strip"></div>

        {/* Left Time Column */}
        <div className="timeline-time-col">
          <span className="timeline-start-time">{evt.startTime || "All Day"}</span>
          {evt.endTime && <span className="timeline-end-time">{evt.endTime}</span>}
          <span className="timeline-date-chip">{evt.date}</span>
        </div>

        {/* Main Content Card */}
        <div className="timeline-card-main">
          <div className="timeline-card-header">
            <span className={`timeline-cat-badge ${evt.category}`}>
              {evt.category === "chat_meeting" && <><i className="fa-solid fa-video"></i> Chat Sync</>}
              {evt.category === "live_stream" && <><i className="fa-solid fa-tower-broadcast"></i> Live Stream</>}
              {evt.category === "channel_event" && <><i className="fa-solid fa-bullhorn"></i> Channel Event</>}
              {evt.category === "moment_memory" && <><i className="fa-solid fa-camera-retro"></i> Moment Memory</>}
              {evt.category === "reel_schedule" && <><i className="fa-solid fa-film"></i> Scheduled Reel</>}
              {evt.category === "personal_task" && <><i className="fa-solid fa-check"></i> Personal Task</>}
            </span>

            {evt.visibility && (
              <span className="timeline-vis-badge">
                <i className="fa-solid fa-lock"></i> {evt.visibility.replace("_", " ")}
              </span>
            )}
          </div>

          <h3 className="timeline-card-title">{evt.title}</h3>
          {evt.description && <p className="timeline-card-desc">{evt.description}</p>}

          {/* Contextual Card Details */}
          {evt.category === "chat_meeting" && (
            <div className="timeline-context-block">
              <div className="timeline-participant-row">
                <div className="participant-avatars">
                  {evt.attendees?.map((att, idx) => (
                    <img
                      key={idx}
                      src={`https://i.pravatar.cc/80?img=${att.avatar || 12}`}
                      alt={att.name}
                      title={att.name}
                      className="stacked-att-avatar"
                    />
                  ))}
                </div>
                <span className="participant-text">
                  With <strong>{evt.contactName}</strong>
                </span>
              </div>

              <div className="timeline-action-buttons" onClick={(e) => e.stopPropagation()}>
                <button
                  className="btn-timeline-action primary"
                  onClick={() => onJoinCall?.(evt)}
                >
                  <i className="fa-solid fa-phone-volume"></i>
                  <span>Join Call</span>
                </button>
                <button
                  className="btn-timeline-action secondary"
                  onClick={() => onOpenChatWithContact?.(evt.contactId)}
                >
                  <i className="fa-solid fa-comment-dots"></i>
                  <span>Open Chat</span>
                </button>
              </div>
            </div>
          )}

          {evt.category === "live_stream" && (
            <div className="timeline-context-block">
              <div className="timeline-live-meta">
                <span className="live-creator-name">
                  Streamer: <strong>{evt.streamerName}</strong>
                </span>
                <span className="live-forecast-badge">
                  <i className="fa-solid fa-eye"></i> {evt.viewersForecast} expected
                </span>
              </div>
              <div className="timeline-action-buttons" onClick={(e) => e.stopPropagation()}>
                <button
                  className="btn-timeline-action live-primary"
                  onClick={() => onWatchLive?.(evt)}
                >
                  <i className="fa-solid fa-play"></i>
                  <span>Watch Stream</span>
                </button>
                <button
                  className="btn-timeline-action secondary"
                  onClick={() => onToast?.(`🔔 Reminder set for "${evt.title}"`)}
                >
                  <i className="fa-solid fa-bell"></i>
                  <span>Remind Me</span>
                </button>
              </div>
            </div>
          )}

          {evt.category === "channel_event" && (
            <div className="timeline-context-block">
              {evt.bannerUrl && (
                <div className="channel-event-banner-wrap">
                  <img src={evt.bannerUrl} alt={evt.title} className="channel-event-banner-img" />
                </div>
              )}
              <div className="timeline-action-buttons" onClick={(e) => e.stopPropagation()}>
                <button
                  className="btn-timeline-action channel-primary"
                  onClick={() => onToast?.(`Joined ${evt.channelName} Event`)}
                >
                  <i className="fa-solid fa-ticket"></i>
                  <span>Register & Follow</span>
                </button>
              </div>
            </div>
          )}

          {evt.category === "moment_memory" && (
            <div className="timeline-context-block">
              <div className="memory-preview-box">
                {evt.thumbnail && (
                  <img src={evt.thumbnail} alt={evt.title} className="memory-thumb-img" />
                )}
                <div className="memory-info-meta">
                  <span className="memory-years-ago">Captured {evt.memoryYear}</span>
                  <span className="memory-media-badge">{evt.mediaCount} Stories in Vault</span>
                </div>
              </div>
              <div className="timeline-action-buttons" onClick={(e) => e.stopPropagation()}>
                <button
                  className="btn-timeline-action memory-primary"
                  onClick={() => onToast?.("Opening Moments Memory Player...")}
                >
                  <i className="fa-solid fa-play"></i>
                  <span>Revisit Moment</span>
                </button>
              </div>
            </div>
          )}

          {evt.category === "personal_task" && (
            <div className="timeline-context-block">
              <label className="task-checkbox-label" onClick={(e) => e.stopPropagation()}>
                <input
                  type="checkbox"
                  checked={evt.isCompleted || false}
                  onChange={() => onToggleTaskComplete?.(evt.id)}
                />
                <span>{evt.isCompleted ? "Completed" : "Mark task done"}</span>
              </label>
            </div>
          )}
        </div>
      </article>
    );
  };

  const hasAnyEvents = dayEvents.length > 0;

  return (
    <div className="social-day-timeline-view">
      <div className="timeline-view-header">
        <div className="timeline-heading-block">
          <h2>Daily Activity Timeline</h2>
          <span className="timeline-date-indicator">
            {new Date(selectedDate || "2026-09-12").toLocaleDateString("en-US", {
              weekday: "long",
              year: "numeric",
              month: "long",
              day: "numeric",
            })}
          </span>
        </div>
      </div>

      {!hasAnyEvents ? (
        <div className="timeline-empty-state">
          <i className="fa-solid fa-calendar-plus empty-icon"></i>
          <h3>No events scheduled for this day</h3>
          <p>Tap below to schedule a meeting, live event, or personal reminder.</p>
        </div>
      ) : (
        <div className="timeline-sections-stream">
          {groupedEvents.morning.length > 0 && (
            <section className="timeline-period-group">
              <h4 className="period-label"><i className="fa-solid fa-sun text-amber"></i> Morning (08:00 - 12:00)</h4>
              <div className="period-cards-list">{groupedEvents.morning.map(renderEventItem)}</div>
            </section>
          )}

          {groupedEvents.afternoon.length > 0 && (
            <section className="timeline-period-group">
              <h4 className="period-label"><i className="fa-solid fa-cloud-sun text-blue"></i> Afternoon (12:00 - 17:00)</h4>
              <div className="period-cards-list">{groupedEvents.afternoon.map(renderEventItem)}</div>
            </section>
          )}

          {groupedEvents.evening.length > 0 && (
            <section className="timeline-period-group">
              <h4 className="period-label"><i className="fa-solid fa-moon text-purple"></i> Evening (17:00 - 21:00)</h4>
              <div className="period-cards-list">{groupedEvents.evening.map(renderEventItem)}</div>
            </section>
          )}

          {groupedEvents.night.length > 0 && (
            <section className="timeline-period-group">
              <h4 className="period-label"><i className="fa-solid fa-stars text-indigo"></i> Night (21:00 onwards)</h4>
              <div className="period-cards-list">{groupedEvents.night.map(renderEventItem)}</div>
            </section>
          )}
        </div>
      )}
    </div>
  );
}

export default DayTimelineView;
