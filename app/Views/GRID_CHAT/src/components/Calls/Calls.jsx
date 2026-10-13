import { useState } from "react";
import { INITIAL_CALLS } from "../../data/callsData";
import "./Calls.css";

// Toasts for a call now come from the shared CallOverlay, so this view no
// longer needs onToast.
function Calls({ isClosing, onBack, onStartCall }) {
  const [calls] = useState(INITIAL_CALLS);
  const [activeFilter, setActiveFilter] = useState("all"); // 'all' | 'missed' | 'audio' | 'video'
  const [searchQuery, setSearchQuery] = useState("");
  const [showNewCallModal, setShowNewCallModal] = useState(false);

  const filterTabs = [
    { id: "all", label: "All" },
    { id: "missed", label: "Missed" },
    { id: "audio", label: "Audio" },
    { id: "video", label: "Video" },
  ];

  const filteredCalls = calls.filter((call) => {
    if (activeFilter === "missed" && !call.isMissed) return false;
    if (activeFilter === "audio" && call.category !== "audio") return false;
    if (activeFilter === "video" && call.category !== "video") return false;

    if (searchQuery.trim()) {
      const q = searchQuery.toLowerCase().trim();
      return call.name.toLowerCase().includes(q) || call.subtitle.toLowerCase().includes(q);
    }
    return true;
  });

  // Opens the shared in-call screen (App owns it, so a call outlives this view).
  // The call log's avatars are pravatar indexes, so turn them into a real URL.
  const handleTriggerCall = (callItem, forceType) => {
    const type = forceType || callItem.callType || "audio";
    onStartCall?.(
      { ...callItem, avatar: `https://i.pravatar.cc/180?img=${callItem.avatar}` },
      type
    );
  };

  return (
    <div className={`calls-workstation ${isClosing ? "is-closing" : ""}`}>
      {/* Calls Top Header */}
      <header className="calls-header">
        <div className="calls-header-left">
          <button
            type="button"
            className="calls-back-btn"
            onClick={onBack}
            title="Back to Chats"
            aria-label="Back"
          >
            <i className="fa-solid fa-arrow-left"></i>
          </button>
          <div className="calls-title-col">
            <h1 className="calls-title">Calls</h1>
            <span className="calls-subtitle">{calls.length} logged communications</span>
          </div>
        </div>

        <div className="calls-header-actions">
          <button
            type="button"
            className="calls-new-btn"
            onClick={() => setShowNewCallModal(true)}
            title="Start New Call"
          >
            <i className="fa-solid fa-phone-plus"></i>
            <span>New Call</span>
          </button>
        </div>
      </header>

      {/* Search Input Bar */}
      <div className="calls-search-bar">
        <label className="calls-search-input-wrap">
          <i className="fa-solid fa-magnifying-glass search-icon"></i>
          <input
            type="search"
            placeholder="Search recent calls..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
          />
          {searchQuery && (
            <button
              type="button"
              className="search-clear-btn"
              onClick={() => setSearchQuery("")}
            >
              <i className="fa-solid fa-xmark"></i>
            </button>
          )}
        </label>
      </div>

      {/* Filter Tabs matching 2nd reference image (All, Missed, Audio, Video) */}
      <div className="calls-filter-bar">
        {filterTabs.map((tab) => {
          const isActive = activeFilter === tab.id;
          return (
            <button
              key={tab.id}
              type="button"
              className={`calls-filter-pill ${isActive ? "active" : ""}`}
              onClick={() => setActiveFilter(tab.id)}
            >
              <span>{tab.label}</span>
            </button>
          );
        })}
      </div>

      {/* Calls List matching 2nd Reference Image */}
      <div className="calls-scroll-area">
        <div className="calls-list">
          {filteredCalls.map((call) => {
            const isMissed = call.isMissed;
            const isVideo = call.callType === "video";

            return (
              <div key={call.id} className="call-card-item">
                {/* Contact Avatar with Online Dot */}
                <div className="call-avatar-wrap">
                  <img
                    src={`https://i.pravatar.cc/120?img=${call.avatar}`}
                    alt={call.name}
                    className="call-avatar-img"
                  />
                  {call.status === "online" && <i className="online-dot"></i>}
                </div>

                {/* Call Info: Name + Status Subtitle */}
                <div className="call-info-col">
                  <strong className="call-contact-name">{call.name}</strong>

                  <div className="call-status-row">
                    {/* Directional Icon */}
                    {isMissed ? (
                      <i className="fa-solid fa-arrow-down-left call-icon-missed"></i>
                    ) : call.direction === "outgoing" ? (
                      <i className="fa-solid fa-arrow-up-right call-icon-outgoing"></i>
                    ) : (
                      <i className="fa-solid fa-arrow-down-left call-icon-incoming"></i>
                    )}

                    <span className={`call-subtitle-text ${isMissed ? "is-missed" : ""}`}>
                      {call.subtitle}
                    </span>
                  </div>
                </div>

                {/* Timestamp */}
                <div className="call-time-col">
                  <span className="call-timestamp">{call.time}</span>
                </div>

                {/* Action Call Button (Phone or Video) */}
                <div className="call-action-col">
                  <button
                    type="button"
                    className={`call-quick-action-btn ${isVideo ? "is-video" : "is-audio"}`}
                    onClick={() => handleTriggerCall(call, isVideo ? "video" : "audio")}
                    title={isVideo ? `Start Video Call with ${call.name}` : `Call ${call.name}`}
                    aria-label={isVideo ? "Video Call" : "Audio Call"}
                  >
                    <i className={`fa-solid ${isVideo ? "fa-video" : "fa-phone"}`}></i>
                  </button>
                </div>
              </div>
            );
          })}

          {filteredCalls.length === 0 && (
            <div className="calls-empty-state">
              <div className="empty-icon-wrap">
                <i className="fa-solid fa-phone-slash"></i>
              </div>
              <h3>No calls found</h3>
              <p>No call history matches your current filter.</p>
            </div>
          )}

          {/* End-of-list clearance spacer for mobile bottom navigation */}
          <div className="calls-list-bottom-spacer" aria-hidden="true"></div>
        </div>
      </div>

      {/* New Call Contact Picker Modal */}
      {showNewCallModal && (
        <div className="new-call-modal-overlay" onClick={() => setShowNewCallModal(false)}>
          <div className="new-call-modal-card" onClick={(e) => e.stopPropagation()}>
            <div className="modal-header">
              <h3>Start New Call</h3>
              <button
                type="button"
                className="modal-close-btn"
                onClick={() => setShowNewCallModal(false)}
              >
                <i className="fa-solid fa-xmark"></i>
              </button>
            </div>

            <p className="modal-desc">Select a contact to initiate a voice or video call:</p>

            <div className="new-call-contacts-list">
              {calls.map((c) => (
                <div key={c.id} className="new-call-contact-row">
                  <img
                    src={`https://i.pravatar.cc/100?img=${c.avatar}`}
                    alt={c.name}
                    className="modal-contact-avatar"
                  />
                  <div className="modal-contact-info">
                    <strong>{c.name}</strong>
                    <span>{c.status === "online" ? "Online" : "Last seen recently"}</span>
                  </div>
                  <div className="modal-contact-actions">
                    <button
                      type="button"
                      className="modal-action-icon audio-btn"
                      onClick={() => {
                        setShowNewCallModal(false);
                        handleTriggerCall(c, "audio");
                      }}
                      title="Audio Call"
                    >
                      <i className="fa-solid fa-phone"></i>
                    </button>
                    <button
                      type="button"
                      className="modal-action-icon video-btn"
                      onClick={() => {
                        setShowNewCallModal(false);
                        handleTriggerCall(c, "video");
                      }}
                      title="Video Call"
                    >
                      <i className="fa-solid fa-video"></i>
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default Calls;
