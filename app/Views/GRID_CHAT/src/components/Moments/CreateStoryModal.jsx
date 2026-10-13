import { useState } from "react";
import "./CreateStoryModal.css";

const GRADIENTS = [
  "linear-gradient(135deg, #00a884, #059669)",
  "linear-gradient(135deg, #ec4899, #f43f5e)",
  "linear-gradient(135deg, #f97316, #ea580c)",
  "linear-gradient(135deg, #10b981, #059669)",
  "linear-gradient(135deg, #f59e0b, #ea580c)",
  "linear-gradient(135deg, #1e293b, #0f172a)",
];

// Matches the audience saved in the Status privacy panel
const privacyHint = (audience, count) => {
  if (audience === "only") {
    return count === 1 ? "Visible to 1 person" : `Visible to ${count} people`;
  }
  if (audience === "contacts_except") {
    return count === 0 ? "Visible to your contacts" : `Visible to your contacts except ${count}`;
  }
  return "Visible to your contacts";
};

function CreateStoryModal({
  privacyAudience = "contacts",
  privacyCount = 0,
  onClose,
  onPostStory,
  onToast,
}) {
  const [text, setText] = useState("");
  const [selectedBg, setSelectedBg] = useState(GRADIENTS[0]);

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!text.trim()) {
      onToast?.("Please type something for your status");
      return;
    }

    // Posting and the resulting toast are handled by the parent
    onPostStory({ type: "text", bg: selectedBg, text: text.trim(), caption: "" });
    onClose();
  };

  return (
    <div className="create-story-overlay">
      <div className="create-story-backdrop" onClick={onClose}></div>

      <div className="create-story-modal">
        <header className="create-story-header">
          <h3>Create Story</h3>
          <button className="close-btn" onClick={onClose} title="Cancel">
            <i className="fa-solid fa-xmark"></i>
          </button>
        </header>

        {/* Live Preview & Input Canvas */}
        <div className="create-story-canvas" style={{ background: selectedBg }}>
          <textarea
            placeholder="Type a status update..."
            value={text}
            onChange={(e) => setText(e.target.value)}
            maxLength={180}
            autoFocus
            rows={4}
          />
        </div>

        {/* Gradient Palette Picker */}
        <div className="palette-picker-section">
          <span>Choose Background</span>
          <div className="palette-circles">
            {GRADIENTS.map((bg, idx) => (
              <button
                key={idx}
                type="button"
                className={`palette-circle ${selectedBg === bg ? "active" : ""}`}
                style={{ background: bg }}
                onClick={() => setSelectedBg(bg)}
                title={`Gradient ${idx + 1}`}
              ></button>
            ))}
          </div>
        </div>

        {/* Footer Actions */}
        <div className="create-story-footer">
          <span className="privacy-hint">
            <i className="fa-solid fa-lock"></i> {privacyHint(privacyAudience, privacyCount)}
          </span>

          <button
            className="post-story-btn"
            onClick={handleSubmit}
            disabled={!text.trim()}
          >
            <i className="fa-solid fa-paper-plane"></i>
            Post to My Status
          </button>
        </div>
      </div>
    </div>
  );
}

export default CreateStoryModal;
