import { useState } from "react";
import { channelCategories } from "../../data/channelsData";

const avatarPresets = [
  "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=200&auto=format&fit=crop&q=80",
  "https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=200&auto=format&fit=crop&q=80",
  "https://images.unsplash.com/photo-1633356122544-f134324a6cee?w=200&auto=format&fit=crop&q=80",
  "https://images.unsplash.com/photo-1542751371-adc38448a05e?w=200&auto=format&fit=crop&q=80",
  "https://images.unsplash.com/photo-1622979135225-d2ba269bc1df?w=200&auto=format&fit=crop&q=80",
  "https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=200&auto=format&fit=crop&q=80",
];

function CreateChannelModal({ isOpen, onClose, onCreateChannel, onToast }) {
  const [name, setName] = useState("");
  const [handle, setHandle] = useState("");
  const [bio, setBio] = useState("");
  const [category, setCategory] = useState("tech");
  const [selectedAvatar, setSelectedAvatar] = useState(avatarPresets[0]);
  const [isPublic, setIsPublic] = useState(true);

  if (!isOpen) return null;

  const handleNameChange = (e) => {
    const val = e.target.value;
    setName(val);
    if (!handle || handle === name.toLowerCase().replace(/[^a-z0-9]/g, "")) {
      setHandle(val.toLowerCase().replace(/[^a-z0-9]/g, ""));
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!name.trim()) {
      onToast?.("Please enter a channel name");
      return;
    }

    const catObj = channelCategories.find((c) => c.id === category) || channelCategories[1];

    const newChannel = {
      id: `channel-${Date.now()}`,
      name: name.trim(),
      handle: handle.trim() || name.trim().toLowerCase().replace(/[^a-z0-9]/g, ""),
      avatar: selectedAvatar,
      banner: "https://images.unsplash.com/photo-1557683316-973673baf926?w=1200&auto=format&fit=crop&q=80",
      verified: true,
      category,
      categoryLabel: catObj.label,
      followers: 1,
      followersFormatted: "1",
      bio: bio.trim() || "Welcome to my official updates channel!",
      isFollowing: true,
      isMuted: false,
      unreadCount: 0,
      createdAt: "Just now",
      isOwner: true,
      posts: [
        {
          id: `post-${Date.now()}`,
          timestamp: "Just now",
          author: name.trim(),
          content: `🎉 Channel officially launched! Welcome everyone. Tap the reactions below and stay tuned for our upcoming broadcasts.`,
          views: 1,
          reactions: { "🎉": 1, "❤️": 1, "🔥": 1 },
          userReaction: "🎉",
        },
      ],
    };

    onCreateChannel(newChannel);
    onToast?.(`Channel "${newChannel.name}" created successfully! 📢`);
    onClose();
  };

  return (
    <div className="create-channel-modal-overlay" onClick={onClose}>
      <div
        className="create-channel-modal-card"
        onClick={(e) => e.stopPropagation()}
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-channel-title"
      >
        <header className="modal-header">
          <div className="modal-title-group">
            <div className="modal-icon-badge">
              <i className="fa-solid fa-bullhorn"></i>
            </div>
            <div>
              <h2 id="modal-channel-title" className="modal-title">Create a Channel</h2>
              <p className="modal-subtitle">Share updates and reach an unlimited audience</p>
            </div>
          </div>
          <button className="modal-close-btn" onClick={onClose} aria-label="Close modal">
            <i className="fa-solid fa-xmark"></i>
          </button>
        </header>

        <form className="modal-form" onSubmit={handleSubmit}>
          {/* Avatar Selector */}
          <div className="form-field-group">
            <label className="form-label">Channel Avatar</label>
            <div className="avatar-selection-row">
              <img src={selectedAvatar} alt="Channel preview" className="avatar-preview-big" />
              <div className="avatar-presets-grid">
                {avatarPresets.map((preset, idx) => (
                  <img
                    key={idx}
                    src={preset}
                    alt={`Preset ${idx + 1}`}
                    className={`avatar-preset-thumb ${selectedAvatar === preset ? "is-active-preset" : ""}`}
                    onClick={() => setSelectedAvatar(preset)}
                  />
                ))}
              </div>
            </div>
          </div>

          {/* Channel Name */}
          <div className="form-field-group">
            <label className="form-label">
              Channel Name <span className="req-star">*</span>
            </label>
            <input
              type="text"
              className="form-text-input"
              placeholder="e.g. AI Innovations Daily"
              value={name}
              onChange={handleNameChange}
              maxLength={60}
              required
            />
          </div>

          {/* Handle */}
          <div className="form-field-group">
            <label className="form-label">Channel Handle</label>
            <div className="handle-input-wrap">
              <span className="handle-prefix">@</span>
              <input
                type="text"
                className="form-text-input handle-field"
                placeholder="aiinnovations"
                value={handle}
                onChange={(e) => setHandle(e.target.value.toLowerCase().replace(/[^a-z0-9_]/g, ""))}
                maxLength={30}
              />
            </div>
          </div>

          {/* Category */}
          <div className="form-field-group">
            <label className="form-label">Category</label>
            <select
              className="form-select-input"
              value={category}
              onChange={(e) => setCategory(e.target.value)}
            >
              {channelCategories.filter((c) => c.id !== "all").map((cat) => (
                <option key={cat.id} value={cat.id}>
                  {cat.label}
                </option>
              ))}
            </select>
          </div>

          {/* Description */}
          <div className="form-field-group">
            <label className="form-label">Description / Bio</label>
            <textarea
              className="form-textarea-input"
              rows={3}
              placeholder="What is your channel about? Describe the content your followers will receive."
              value={bio}
              onChange={(e) => setBio(e.target.value)}
              maxLength={240}
            />
          </div>

          {/* Public / Private Visibility Switch */}
          <div className="form-visibility-row">
            <div className="visibility-icon-box">
              <i className={`fa-solid ${isPublic ? "fa-globe" : "fa-lock"}`}></i>
            </div>
            <div className="visibility-meta">
              <h4 className="visibility-title">{isPublic ? "Public Channel" : "Private Channel"}</h4>
              <p className="visibility-desc">
                {isPublic
                  ? "Anyone can find and follow your channel via search or direct link."
                  : "Only people with an invite link can join and view your posts."}
              </p>
            </div>
            <button
              type="button"
              className={`toggle-switch-btn ${isPublic ? "switch-on" : ""}`}
              onClick={() => setIsPublic(!isPublic)}
              aria-label="Toggle Public or Private"
            >
              <span className="toggle-switch-thumb"></span>
            </button>
          </div>

          {/* Footer Actions */}
          <footer className="modal-footer-actions">
            <button type="button" className="btn-cancel-modal" onClick={onClose}>
              Cancel
            </button>
            <button type="submit" className="btn-submit-channel">
              <i className="fa-solid fa-plus"></i> Create Channel
            </button>
          </footer>
        </form>
      </div>
    </div>
  );
}

export default CreateChannelModal;
