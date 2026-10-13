import { useState, useMemo } from "react";

function GroupPlannerView({
  events = [],
  chats = [],
  onOpenCreateEvent,
  onOpenEventDetail,
  onVotePoll,
  onToast,
}) {
  const groupChats = useMemo(() => {
    const groups = chats.filter((c) => c.isGroup);
    if (groups.length > 0) return groups;
    return [
      { id: 10, name: "Tech Innovators Hub", isGroup: true, avatar: 44 },
      { id: 11, name: "Family Group", isGroup: true, avatar: 41 },
      { id: 12, name: "Product Launch Squad", isGroup: true, avatar: 38 },
    ];
  }, [chats]);

  const [selectedGroupId, setSelectedGroupId] = useState(groupChats[0]?.id || 10);

  const selectedGroup = useMemo(() => {
    return groupChats.find((g) => g.id === selectedGroupId) || groupChats[0];
  }, [groupChats, selectedGroupId]);

  // Find events for this group
  const groupEvents = useMemo(() => {
    return events.filter(
      (e) =>
        e.isGroup ||
        e.contactId === selectedGroupId ||
        (e.contactName && e.contactName.toLowerCase().includes("group")) ||
        (e.contactName && e.contactName.toLowerCase().includes("hub"))
    );
  }, [events, selectedGroupId]);

  return (
    <div className="social-group-planner-view">
      {/* Group Selector Header */}
      <div className="group-planner-header">
        <div className="group-title-cluster">
          <i className="fa-solid fa-users-rectangle text-purple"></i>
          <div>
            <h3>Group Chat Calendars</h3>
            <p>Collaborative events, destination polls, and shared coordination</p>
          </div>
        </div>

        <div className="group-selection-tabs">
          {groupChats.map((g) => (
            <button
              key={g.id}
              className={`group-tab-btn ${selectedGroupId === g.id ? "active" : ""}`}
              onClick={() => setSelectedGroupId(g.id)}
            >
              <img src={`https://i.pravatar.cc/80?img=${g.avatar}`} alt={g.name} />
              <span>{g.name}</span>
            </button>
          ))}
        </div>
      </div>

      {/* Group Hero Banner */}
      <div className="group-planner-hero">
        <div className="hero-group-left">
          <img
            src={`https://i.pravatar.cc/100?img=${selectedGroup?.avatar || 44}`}
            alt={selectedGroup?.name}
            className="group-hero-avatar"
          />
          <div>
            <h2>{selectedGroup?.name} Calendar</h2>
            <p>All members can suggest dates, vote on polls, and RSVP.</p>
          </div>
        </div>

        <button
          className="btn-create-group-event"
          onClick={() => onOpenCreateEvent?.({ isGroup: true, contact: selectedGroup })}
        >
          <i className="fa-solid fa-plus"></i>
          <span>Add Group Event</span>
        </button>
      </div>

      {/* Group Events Feed */}
      <div className="group-events-grid">
        {groupEvents.length === 0 ? (
          <div className="group-empty-events">
            <i className="fa-solid fa-calendar-xmark"></i>
            <h4>No events scheduled for {selectedGroup?.name}</h4>
            <p>Plan a group meetup, birthday celebration, or trip together.</p>
          </div>
        ) : (
          groupEvents.map((evt) => (
            <div
              key={evt.id}
              className="group-event-card"
              onClick={() => onOpenEventDetail(evt)}
            >
              <div className="group-card-top">
                <span className="event-date-badge">
                  <i className="fa-regular fa-calendar"></i> {evt.date} • {evt.startTime}
                </span>
                <span className="event-cat-tag">{evt.category.replace("_", " ")}</span>
              </div>

              <h4 className="group-card-title">{evt.title}</h4>
              {evt.description && <p className="group-card-desc">{evt.description}</p>}

              {/* Group Voting Poll */}
              {evt.poll && (
                <div className="group-poll-container" onClick={(e) => e.stopPropagation()}>
                  <span className="poll-question">
                    <i className="fa-solid fa-check-to-slot text-purple"></i> {evt.poll.question}
                  </span>
                  <div className="poll-options-grid">
                    {evt.poll.options.map((opt) => (
                      <button
                        key={opt.id}
                        className={`btn-poll-option ${evt.poll.userVoted === opt.id ? "is-voted" : ""}`}
                        onClick={() => {
                          onVotePoll?.(evt.id, opt.id);
                          onToast?.("🗳️ Vote recorded in group poll!");
                        }}
                      >
                        <span>{opt.text}</span>
                        <span className="opt-vote-count">{opt.votes}</span>
                      </button>
                    ))}
                  </div>
                </div>
              )}

              <div className="group-card-footer">
                <div className="attendee-count">
                  <i className="fa-solid fa-user-group"></i>
                  <span>{evt.attendees?.length || 4} Attending</span>
                </div>
                <button className="btn-view-group-agenda">
                  <span>View Details</span>
                  <i className="fa-solid fa-chevron-right"></i>
                </button>
              </div>
            </div>
          ))
        )}
      </div>
    </div>
  );
}

export default GroupPlannerView;
