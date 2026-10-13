import { useState, useMemo } from "react";
import { RELATIONSHIP_MILESTONES } from "../../data/calendarData";

function RelationshipTimelineView({
  chats = [],
  onOpenChatWithContact,
  onScheduleWithContact,
}) {
  const [selectedContactId, setSelectedContactId] = useState(0); // Default to Raju

  const currentContact = useMemo(() => {
    return chats.find((c) => c.id === selectedContactId) || {
      id: selectedContactId,
      name: "Raju",
      avatar: 33,
      status: "online",
      phone: "+91 98450 11234",
    };
  }, [chats, selectedContactId]);

  const milestones = RELATIONSHIP_MILESTONES[selectedContactId] || [
    {
      id: "def-1",
      date: "Recent",
      type: "chat",
      icon: "fa-solid fa-comment-dots",
      color: "var(--color-primary, #f59e0b)",
      title: "Active Chat Conversation",
      description: `Active connection established with ${currentContact.name}.`,
    },
  ];

  return (
    <div className="social-relationship-timeline-view">
      {/* Contact Switcher Bar */}
      <div className="relationship-contact-picker">
        <div className="picker-title-group">
          <i className="fa-solid fa-timeline"></i>
          <div>
            <h3>Relationship Memory Timeline</h3>
            <p>Chronological communication memory and shared milestones</p>
          </div>
        </div>

        <div className="contact-pills-scroll">
          {chats.map((c) => (
            <button
              key={c.id}
              className={`contact-select-pill ${selectedContactId === c.id ? "active" : ""}`}
              onClick={() => setSelectedContactId(c.id)}
            >
              <img src={`https://i.pravatar.cc/80?img=${c.avatar}`} alt={c.name} />
              <span>{c.name}</span>
            </button>
          ))}
        </div>
      </div>

      {/* Selected Contact Hero Card */}
      <div className="relationship-hero-card">
        <div className="hero-left-info">
          <div className="hero-avatar-ring">
            <img src={`https://i.pravatar.cc/120?img=${currentContact.avatar || 12}`} alt={currentContact.name} />
            <span className="online-presence-dot"></span>
          </div>

          <div className="hero-meta-details">
            <div className="hero-name-row">
              <h2>{currentContact.name}</h2>
              <span className="contact-tag">Connected Contact</span>
            </div>
            <p className="hero-connection-since">
              Communication history tracked across GRID MyChat
            </p>
          </div>
        </div>

        {/* Milestone Stats */}
        <div className="relationship-stats-strip">
          <div className="rel-stat-box">
            <span className="stat-num">{milestones.length}</span>
            <span className="stat-lbl">Milestones</span>
          </div>
          <div className="rel-stat-box">
            <span className="stat-num">4</span>
            <span className="stat-lbl">Meetings</span>
          </div>
          <div className="rel-stat-box">
            <span className="stat-num">32m</span>
            <span className="stat-lbl">Calls Logged</span>
          </div>
        </div>

        {/* Quick Contact Actions */}
        <div className="hero-cta-actions">
          <button
            className="btn-rel-cta primary"
            onClick={() => onScheduleWithContact?.(currentContact)}
          >
            <i className="fa-solid fa-calendar-plus"></i>
            <span>Schedule Event</span>
          </button>
          <button
            className="btn-rel-cta secondary"
            onClick={() => onOpenChatWithContact?.(currentContact.id)}
          >
            <i className="fa-solid fa-comment-dots"></i>
            <span>Open Chat</span>
          </button>
        </div>
      </div>

      {/* Chronological Milestone Feed */}
      <div className="relationship-milestones-stream">
        <h4 className="milestones-heading">
          <i className="fa-solid fa-clock-rotate-left"></i> Shared Memories & Milestones
        </h4>

        <div className="timeline-node-track">
          {milestones.map((m) => (
            <div key={m.id} className="timeline-node-item">
              <div className="node-marker" style={{ background: m.color }}>
                <i className={m.icon}></i>
              </div>

              <div className="node-content-card">
                <div className="node-header-row">
                  <span className="node-date-badge">{m.date}</span>
                  <span className="node-type-label">{m.title}</span>
                </div>
                <p className="node-desc">{m.description}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

export default RelationshipTimelineView;
