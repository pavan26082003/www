import { useState } from "react";
import "./MuteNotificationsPanel.css";

const MUTE_OPTIONS = [
  { value: "8h", label: "8 hours" },
  { value: "1w", label: "1 week" },
  { value: "always", label: "Always" },
];

function MuteNotificationsPanel({ chat, muteInfo, onBack, onClose, onMute, onUnmute }) {
  const [selected, setSelected] = useState(muteInfo?.duration || null);

  const isMuted = Boolean(muteInfo?.muted);

  const handleMuteClick = () => {
    if (!selected) return;
    onMute?.(selected);
  };

  return (
    <div className="mute-panel-view">
      <header className="panel-secondary-header">
        <div className="panel-secondary-header-left">
          <button className="panel-secondary-back-btn" onClick={onBack} title="Back">
            <i className="fa-solid fa-arrow-left"></i>
          </button>
          <div className="panel-secondary-title-wrap">
            <h2>Mute notifications</h2>
          </div>
        </div>

        <div className="panel-secondary-header-actions">
          <button
            className="panel-secondary-icon-btn"
            onClick={onClose}
            title="Close"
            aria-label="Close"
          >
            <i className="fa-solid fa-xmark"></i>
          </button>
        </div>
      </header>

      <div className="panel-secondary-scroll">
        <div className="mute-panel-intro">
          <div className="mute-panel-icon">
            <i className={`fa-solid ${isMuted ? "fa-bell-slash" : "fa-bell"}`}></i>
          </div>
          <h3>Mute notifications</h3>
          <p>
            Choose how long notifications should be muted for
            {chat?.name ? ` ${chat.name}` : " this conversation"}.
          </p>
        </div>

        <div className="mute-options-list">
          {MUTE_OPTIONS.map((option) => (
            <label
              key={option.value}
              className={`mute-option-row ${selected === option.value ? "selected" : ""}`}
            >
              <input
                type="radio"
                name="mute-duration"
                value={option.value}
                checked={selected === option.value}
                onChange={() => setSelected(option.value)}
              />
              <span className="mute-option-radio-dot"></span>
              <span className="mute-option-label">{option.label}</span>
            </label>
          ))}
        </div>

        <div className="panel-secondary-footer">
          {isMuted && (
            <button type="button" className="panel-secondary-btn-secondary" onClick={onUnmute}>
              Unmute
            </button>
          )}
          <button
            type="button"
            className="panel-secondary-btn-primary"
            onClick={handleMuteClick}
            disabled={!selected}
          >
            Mute
          </button>
        </div>
      </div>
    </div>
  );
}

export default MuteNotificationsPanel;
