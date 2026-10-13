import { useState } from "react";
import { getAvatarUrl } from "../../utils/avatar";
import "./Settings.css";

const ACCENT_OPTIONS = [
  { id: "emerald", name: "WhatsApp Emerald", color: "#00a884" },
  { id: "green", name: "Vibrant Green", color: "#10b981" },
  { id: "warm_canvas", name: "Warm Canvas", color: "#e07a5f" },
  { id: "tangerine", name: "GRID Tangerine", color: "#f97316" },
  { id: "amber", name: "Sunset Amber", color: "#f59e0b" },
  { id: "rose", name: "Rose Coral", color: "#f43f5e" },
];

const WALLPAPERS = [
  { id: "default", name: "Modern Clean", preview: "linear-gradient(135deg, #f8f9fe, #edf2f7)" },
  { id: "midnight", name: "Midnight Navy", preview: "linear-gradient(135deg, #0b0f19, #171e2e)" },
  { id: "geometric", name: "Geometric Grid", preview: "radial-gradient(circle, #e2e8f0 10%, transparent 11%)" },
  { id: "sunset", name: "Warm Canvas", preview: "linear-gradient(135deg, #fff1eb, #ace0f9)" },
];

const NAV_SECTIONS = [
  { id: "appearance", label: "Appearance", icon: "fa-solid fa-palette", desc: "Theme, accents & wallpapers" },
  { id: "profile", label: "Profile & Account", icon: "fa-solid fa-user", desc: "Name, bio & avatar" },
  { id: "notifications", label: "Notifications", icon: "fa-solid fa-bell", desc: "Sounds, alerts & badges" },
  { id: "privacy", label: "Privacy & Security", icon: "fa-solid fa-shield-halved", desc: "Last seen & read receipts" },
  { id: "chats", label: "Chats & Media", icon: "fa-solid fa-message", desc: "Enter to send & auto-download" },
  { id: "storage", label: "Storage & Data", icon: "fa-solid fa-database", desc: "Cache & network usage" },
  { id: "help", label: "Help & About", icon: "fa-solid fa-circle-info", desc: "Version & shortcuts" },
];

function Settings({
  isClosing,
  settings,
  onUpdateSettings,
  currentUser,
  onUpdateCurrentUser,
  onClose,
  onToast,
  onClearAllChats,
  onOpenProfile,
  initialSection = "appearance",
}) {
  const [activeSection, setActiveSection] = useState(initialSection || "appearance");

  // Profile local form state
  const [displayName, setDisplayName] = useState(currentUser?.name || "Henry Jabbawockiez");
  const [statusText, setStatusText] = useState(currentUser?.status || "Available • Online");
  const [phoneText, setPhoneText] = useState(currentUser?.phone || "+91 90590 44812");
  const [cacheCleared, setCacheCleared] = useState(false);

  const handleSaveProfile = async (e) => {
    e.preventDefault();
    if (!displayName.trim()) {
      onToast?.("Display name cannot be empty");
      return;
    }
    const result = await onUpdateCurrentUser?.({
      ...currentUser,
      name: displayName.trim(),
      status: statusText.trim(),
      phone: phoneText.trim(),
    });
    if (result && !result.success) {
      onToast?.(result.message || "Could not save profile");
      return;
    }
    onToast?.("Profile updated successfully! ✨");
  };

  const handleClearCache = () => {
    setCacheCleared(true);
    onToast?.("Storage cache cleared! 280 MB freed 🎉");
  };

  const handleResetSettings = () => {
    onUpdateSettings?.({
      theme: "light",
      accent: "amber",
      fontSize: "md",
      wallpaper: "default",
      soundEnabled: true,
      messagePreviews: true,
      liveAlerts: true,
      lastSeen: "everyone",
      readReceipts: true,
      twoStepAuth: false,
      enterToSend: true,
      autoDownloadMedia: true,
      lowDataMode: false,
    });
    onToast?.("All settings restored to defaults");
  };

  const currentNav = NAV_SECTIONS.find((sec) => sec.id === activeSection) || NAV_SECTIONS[0];

  return (
    <div className={`settings-container ${isClosing ? "closing" : ""}`}>
      {/* Top Header */}
      <header className="settings-top-header">
        <div className="settings-header-left">
          <button
            className="settings-back-btn"
            onClick={onClose}
            title="Back to Chats"
            aria-label="Back to Chats"
          >
            <i className="fa-solid fa-arrow-left"></i>
          </button>
          <div className="settings-title-info">
            <h2>{currentNav.label}</h2>
            <p>{currentNav.desc}</p>
          </div>
        </div>

        <button
          className="settings-close-icon-btn"
          onClick={onClose}
          title="Close Settings"
          aria-label="Close Settings"
        >
          <i className="fa-solid fa-xmark"></i>
        </button>
      </header>

      {/* Main Settings Body */}
      <div className="settings-body">
        {/* Left Navigation Menu */}
        <aside className="settings-nav-sidebar">
          {NAV_SECTIONS.map((sec) => {
            const isActive = activeSection === sec.id;
            return (
              <button
                key={sec.id}
                className={`settings-nav-item ${isActive ? "active" : ""}`}
                onClick={() => setActiveSection(sec.id)}
              >
                <div className="settings-nav-icon">
                  <i className={sec.icon}></i>
                </div>
                <div className="settings-nav-text">
                  <span className="settings-nav-label">{sec.label}</span>
                  <span className="settings-nav-desc">{sec.desc}</span>
                </div>
                <i className="fa-solid fa-chevron-right settings-nav-arrow"></i>
              </button>
            );
          })}
        </aside>

        {/* Right Content Panel */}
        <section className="settings-content-panel">
          {/* =========================================================
              SECTION 1: APPEARANCE (THEME, ACCENT & WALLPAPER)
             ========================================================= */}
          {activeSection === "appearance" && (
            <div className="settings-section-card">
              <div className="section-header">
                <h3>Appearance & Theme</h3>
                <p>Personalize your theme, accent color, and typography.</p>
              </div>

              {/* Theme Mode Selector (Light, Dark, System) */}
              <div className="setting-group">
                <label className="setting-group-title">Color Mode</label>
                <div className="theme-options-grid">
                  <button
                    type="button"
                    className={`theme-option-card ${settings.theme === "light" ? "active" : ""}`}
                    onClick={() => {
                      onUpdateSettings?.({ ...settings, theme: "light" });
                      onToast?.("Switched to Light Theme ☀️");
                    }}
                  >
                    <div className="theme-card-preview theme-preview-light">
                      <div className="preview-top-bar"></div>
                      <div className="preview-chat-row preview-incoming"></div>
                      <div className="preview-chat-row preview-outgoing"></div>
                    </div>
                    <div className="theme-card-info">
                      <i className="fa-solid fa-sun icon-sun"></i>
                      <span>Light Mode</span>
                    </div>
                  </button>

                  <button
                    type="button"
                    className={`theme-option-card ${settings.theme === "dark" ? "active" : ""}`}
                    onClick={() => {
                      onUpdateSettings?.({ ...settings, theme: "dark" });
                      onToast?.("Switched to Dark Theme 🌙");
                    }}
                  >
                    <div className="theme-card-preview theme-preview-dark">
                      <div className="preview-top-bar"></div>
                      <div className="preview-chat-row preview-incoming"></div>
                      <div className="preview-chat-row preview-outgoing"></div>
                    </div>
                    <div className="theme-card-info">
                      <i className="fa-solid fa-moon icon-moon"></i>
                      <span>Dark Mode</span>
                    </div>
                  </button>

                  <button
                    type="button"
                    className={`theme-option-card ${settings.theme === "system" ? "active" : ""}`}
                    onClick={() => {
                      onUpdateSettings?.({ ...settings, theme: "system" });
                      onToast?.("Synchronized with System Theme 💻");
                    }}
                  >
                    <div className="theme-card-preview theme-preview-system">
                      <div className="preview-split-half left"></div>
                      <div className="preview-split-half right"></div>
                    </div>
                    <div className="theme-card-info">
                      <i className="fa-solid fa-desktop icon-desktop"></i>
                      <span>System Default</span>
                    </div>
                  </button>
                </div>
              </div>

              {/* Accent Color Picker */}
              <div className="setting-group">
                <label className="setting-group-title">Accent Color</label>
                <p className="setting-group-subtitle">
                  Choose a vibrant primary accent color for active buttons, tabs, and outgoing bubbles.
                </p>
                <div className="accent-color-list">
                  {ACCENT_OPTIONS.map((acc) => {
                    const isSelected = (settings.accent || "amber") === acc.id;

                    return (
                      <button
                        key={acc.id}
                        type="button"
                        className={`accent-color-btn ${isSelected ? "selected" : ""}`}
                        style={{ "--accent-hex": acc.color }}
                        onClick={() => {
                          onUpdateSettings?.({ ...settings, accent: acc.id });
                          onToast?.(`Accent color changed to ${acc.name}`);
                        }}
                        title={acc.name}
                      >
                        <span className="accent-color-circle" style={{ backgroundColor: acc.color }}>
                          {isSelected && <i className="fa-solid fa-check"></i>}
                        </span>
                        <span className="accent-color-name">{acc.name}</span>
                      </button>
                    );
                  })}
                </div>
              </div>

              {/* Font Size Scaling */}
              <div className="setting-group">
                <label className="setting-group-title">Font Size & Text Scaling</label>
                <div className="segmented-control">
                  <button
                    type="button"
                    className={`segmented-btn ${settings.fontSize === "sm" ? "active" : ""}`}
                    onClick={() => onUpdateSettings?.({ ...settings, fontSize: "sm" })}
                  >
                    Small (13px)
                  </button>
                  <button
                    type="button"
                    className={`segmented-btn ${(settings.fontSize || "md") === "md" ? "active" : ""}`}
                    onClick={() => onUpdateSettings?.({ ...settings, fontSize: "md" })}
                  >
                    Default (14px)
                  </button>
                  <button
                    type="button"
                    className={`segmented-btn ${settings.fontSize === "lg" ? "active" : ""}`}
                    onClick={() => onUpdateSettings?.({ ...settings, fontSize: "lg" })}
                  >
                    Large (16px)
                  </button>
                </div>
              </div>

              {/* Chat Wallpaper Preview */}
              <div className="setting-group">
                <label className="setting-group-title">Chat Wallpaper</label>
                <div className="wallpaper-grid">
                  {WALLPAPERS.map((wp) => {
                    const isSelected = (settings.wallpaper || "default") === wp.id;
                    return (
                      <button
                        key={wp.id}
                        type="button"
                        className={`wallpaper-card ${isSelected ? "selected" : ""}`}
                        onClick={() => {
                          onUpdateSettings?.({ ...settings, wallpaper: wp.id });
                          onToast?.(`Chat wallpaper set to ${wp.name}`);
                        }}
                      >
                        <div className="wallpaper-swatch" style={{ background: wp.preview }}>
                          {isSelected && (
                            <div className="wallpaper-check">
                              <i className="fa-solid fa-check"></i>
                            </div>
                          )}
                        </div>
                        <span className="wallpaper-name">{wp.name}</span>
                      </button>
                    );
                  })}
                </div>
              </div>

              {/* Live Preview Box */}
              <div className="theme-live-preview-box">
                <div className="live-preview-label">
                  <i className="fa-solid fa-wand-magic-sparkles"></i> Live Theme & Accent Preview
                </div>
                <div className="live-preview-content">
                  <div className="preview-bubble incoming">
                    <span>Hey there! How does the new theme look? 👋</span>
                    <span className="preview-time">10:42 AM</span>
                  </div>
                  <div className="preview-bubble outgoing">
                    <span>Looks ultra sleek in both Light and Dark modes! ✨</span>
                    <span className="preview-time">10:43 AM <i className="fa-solid fa-check-double"></i></span>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* =========================================================
              SECTION 2: PROFILE & ACCOUNT
             ========================================================= */}
          {activeSection === "profile" && (
            <div className="settings-section-card">
              <div className="section-header">
                <h3>Profile & Account</h3>
                <p>Manage your public identity, status message, and profile photo.</p>
              </div>

              <form onSubmit={handleSaveProfile}>
                {/* Avatar Picker */}
                <div className="profile-avatar-editor">
                  <div className="profile-avatar-preview">
                    <img src={getAvatarUrl(currentUser?.avatar, 160, displayName || currentUser?.name)} alt="Profile" />
                    <span className="profile-badge-verified" title="Verified Account">
                      <i className="fa-solid fa-circle-check"></i>
                    </span>
                  </div>

                  <div className="profile-avatar-select">
                    <label className="setting-label">Profile Photo</label>
                    <p className="form-hint">
                      Upload or remove your photo on your profile page. Without a photo, your initials are shown.
                    </p>
                    {onOpenProfile && (
                      <button type="button" className="settings-open-profile-btn" onClick={onOpenProfile}>
                        <i className="fa-solid fa-camera"></i> Change photo
                      </button>
                    )}
                  </div>
                </div>

                <div className="form-group">
                  <label className="form-label" htmlFor="settings-name">Display Name</label>
                  <input
                    id="settings-name"
                    type="text"
                    className="form-input"
                    value={displayName}
                    onChange={(e) => setDisplayName(e.target.value)}
                    placeholder="Enter your name"
                  />
                  <small className="form-hint">This name will be visible to your contacts and in chat rooms.</small>
                </div>

                <div className="form-group">
                  <label className="form-label" htmlFor="settings-status">About / Status</label>
                  <input
                    id="settings-status"
                    type="text"
                    className="form-input"
                    value={statusText}
                    onChange={(e) => setStatusText(e.target.value)}
                    placeholder="Set your status"
                  />
                </div>

                <div className="form-group">
                  <label className="form-label" htmlFor="settings-phone">Phone Number</label>
                  <input
                    id="settings-phone"
                    type="text"
                    className="form-input"
                    value={phoneText}
                    onChange={(e) => setPhoneText(e.target.value)}
                    placeholder="+91 00000 00000"
                  />
                </div>

                <div className="form-actions">
                  <button type="submit" className="primary-btn">
                    <i className="fa-solid fa-floppy-disk"></i> Save Changes
                  </button>
                </div>
              </form>
            </div>
          )}

          {/* =========================================================
              SECTION 3: NOTIFICATIONS
             ========================================================= */}
          {activeSection === "notifications" && (
            <div className="settings-section-card">
              <div className="section-header">
                <h3>Notifications & Alerts</h3>
                <p>Configure how and when you receive message alerts.</p>
              </div>

              <div className="toggle-list">
                <div className="toggle-item">
                  <div className="toggle-info">
                    <strong>Message Sounds</strong>
                    <span>Play subtle chimes for incoming messages and alerts</span>
                  </div>
                  <label className="switch">
                    <input
                      type="checkbox"
                      checked={settings.soundEnabled ?? true}
                      onChange={(e) => {
                        onUpdateSettings?.({ ...settings, soundEnabled: e.target.checked });
                        onToast?.(`Message sounds ${e.target.checked ? "enabled" : "muted"}`);
                      }}
                    />
                    <span className="slider round"></span>
                  </label>
                </div>

                <div className="toggle-item">
                  <div className="toggle-info">
                    <strong>Message Previews</strong>
                    <span>Show message snippet and sender name in toast notifications</span>
                  </div>
                  <label className="switch">
                    <input
                      type="checkbox"
                      checked={settings.messagePreviews ?? true}
                      onChange={(e) => {
                        onUpdateSettings?.({ ...settings, messagePreviews: e.target.checked });
                        onToast?.(`Previews ${e.target.checked ? "enabled" : "hidden"}`);
                      }}
                    />
                    <span className="slider round"></span>
                  </label>
                </div>

                <div className="toggle-item">
                  <div className="toggle-info">
                    <strong>Live Streaming & Moments Notifications</strong>
                    <span>Get notified when followed streamers go live or post stories</span>
                  </div>
                  <label className="switch">
                    <input
                      type="checkbox"
                      checked={settings.liveAlerts ?? true}
                      onChange={(e) => {
                        onUpdateSettings?.({ ...settings, liveAlerts: e.target.checked });
                      }}
                    />
                    <span className="slider round"></span>
                  </label>
                </div>
              </div>
            </div>
          )}

          {/* =========================================================
              SECTION 4: PRIVACY & SECURITY
             ========================================================= */}
          {activeSection === "privacy" && (
            <div className="settings-section-card">
              <div className="section-header">
                <h3>Privacy & Security</h3>
                <p>Manage who can see your activity and protect your account.</p>
              </div>

              <div className="setting-group">
                <label className="setting-group-title">Last Seen & Online Status</label>
                <div className="segmented-control">
                  {["everyone", "contacts", "nobody"].map((opt) => (
                    <button
                      key={opt}
                      type="button"
                      className={`segmented-btn ${(settings.lastSeen || "everyone") === opt ? "active" : ""}`}
                      onClick={() => onUpdateSettings?.({ ...settings, lastSeen: opt })}
                    >
                      {opt.charAt(0).toUpperCase() + opt.slice(1)}
                    </button>
                  ))}
                </div>
              </div>

              <div className="toggle-list">
                <div className="toggle-item">
                  <div className="toggle-info">
                    <strong>Read Receipts (Blue Ticks)</strong>
                    <span>If turned off, you won&apos;t send or receive read receipts</span>
                  </div>
                  <label className="switch">
                    <input
                      type="checkbox"
                      checked={settings.readReceipts ?? true}
                      onChange={(e) => {
                        onUpdateSettings?.({ ...settings, readReceipts: e.target.checked });
                        onToast?.(`Read receipts ${e.target.checked ? "enabled" : "disabled"}`);
                      }}
                    />
                    <span className="slider round"></span>
                  </label>
                </div>

                <div className="toggle-item">
                  <div className="toggle-info">
                    <strong>Two-Step Verification</strong>
                    <span>Add an extra layer of security with a 6-digit PIN confirmation</span>
                  </div>
                  <label className="switch">
                    <input
                      type="checkbox"
                      checked={settings.twoStepAuth ?? false}
                      onChange={(e) => {
                        onUpdateSettings?.({ ...settings, twoStepAuth: e.target.checked });
                        onToast?.(`Two-step security ${e.target.checked ? "activated" : "deactivated"}`);
                      }}
                    />
                    <span className="slider round"></span>
                  </label>
                </div>
              </div>
            </div>
          )}

          {/* =========================================================
              SECTION 5: CHATS & MEDIA
             ========================================================= */}
          {activeSection === "chats" && (
            <div className="settings-section-card">
              <div className="section-header">
                <h3>Chats & Media Settings</h3>
                <p>Manage composer behaviors, media auto-downloads, and chat history.</p>
              </div>

              <div className="toggle-list">
                <div className="toggle-item">
                  <div className="toggle-info">
                    <strong>Enter is Send</strong>
                    <span>Pressing Enter key sends your message (Shift+Enter inserts a new line)</span>
                  </div>
                  <label className="switch">
                    <input
                      type="checkbox"
                      checked={settings.enterToSend ?? true}
                      onChange={(e) => {
                        onUpdateSettings?.({ ...settings, enterToSend: e.target.checked });
                        onToast?.(`Enter to send ${e.target.checked ? "enabled" : "disabled"}`);
                      }}
                    />
                    <span className="slider round"></span>
                  </label>
                </div>

                <div className="toggle-item">
                  <div className="toggle-info">
                    <strong>Auto-Download Media</strong>
                    <span>Automatically load shared photos and voice notes on Wi-Fi</span>
                  </div>
                  <label className="switch">
                    <input
                      type="checkbox"
                      checked={settings.autoDownloadMedia ?? true}
                      onChange={(e) => onUpdateSettings?.({ ...settings, autoDownloadMedia: e.target.checked })}
                    />
                    <span className="slider round"></span>
                  </label>
                </div>
              </div>

              <div className="danger-zone-card">
                <div className="danger-zone-header">
                  <i className="fa-solid fa-triangle-exclamation"></i>
                  <strong>Manage Chat History</strong>
                </div>
                <p>Permanently remove message history or reset all active conversations.</p>
                <div className="danger-actions">
                  <button
                    type="button"
                    className="danger-btn"
                    onClick={() => {
                      if (window.confirm("Are you sure you want to clear conversation messages?")) {
                        onClearAllChats?.();
                        onToast?.("All chat messages have been reset");
                      }
                    }}
                  >
                    <i className="fa-solid fa-trash-can"></i> Clear All Messages
                  </button>
                </div>
              </div>
            </div>
          )}

          {/* =========================================================
              SECTION 6: STORAGE & DATA
             ========================================================= */}
          {activeSection === "storage" && (
            <div className="settings-section-card">
              <div className="section-header">
                <h3>Storage & Network Usage</h3>
                <p>Inspect local storage consumption and free cached media.</p>
              </div>

              <div className="storage-overview-card">
                <div className="storage-overview-top">
                  <div>
                    <span className="storage-used-number">{cacheCleared ? "420 MB" : "700 MB"}</span>
                    <span className="storage-total"> / 5.0 GB used</span>
                  </div>
                  <span className="storage-free-badge">{cacheCleared ? "4.58 GB free" : "4.30 GB free"}</span>
                </div>

                <div className="storage-progress-bar">
                  <div className="storage-bar-segment segment-photos" style={{ width: "45%" }}></div>
                  <div className="storage-bar-segment segment-videos" style={{ width: "30%" }}></div>
                  <div
                    className="storage-bar-segment segment-cache"
                    style={{ width: cacheCleared ? "0%" : "25%" }}
                  ></div>
                </div>

                <div className="storage-legend">
                  <div className="legend-item">
                    <span className="legend-dot dot-photos"></span>
                    <span>Photos (315 MB)</span>
                  </div>
                  <div className="legend-item">
                    <span className="legend-dot dot-videos"></span>
                    <span>Videos (210 MB)</span>
                  </div>
                  <div className="legend-item">
                    <span className="legend-dot dot-cache"></span>
                    <span>Cache ({cacheCleared ? "0 MB" : "280 MB"})</span>
                  </div>
                </div>
              </div>

              <div className="storage-actions-card">
                <div className="storage-action-info">
                  <strong>Temporary Cache Files</strong>
                  <span>Free space without deleting your saved messages or starred media</span>
                </div>
                <button
                  type="button"
                  className="secondary-action-btn"
                  onClick={handleClearCache}
                  disabled={cacheCleared}
                >
                  <i className="fa-solid fa-broom"></i> {cacheCleared ? "Cache Cleared" : "Clear Cache (280 MB)"}
                </button>
              </div>

              <div className="toggle-list">
                <div className="toggle-item">
                  <div className="toggle-info">
                    <strong>Low Data Usage Mode</strong>
                    <span>Reduce data consumption for calls and media when on cellular networks</span>
                  </div>
                  <label className="switch">
                    <input
                      type="checkbox"
                      checked={settings.lowDataMode ?? false}
                      onChange={(e) => onUpdateSettings?.({ ...settings, lowDataMode: e.target.checked })}
                    />
                    <span className="slider round"></span>
                  </label>
                </div>
              </div>
            </div>
          )}

          {/* =========================================================
              SECTION 7: HELP & ABOUT
             ========================================================= */}
          {activeSection === "help" && (
            <div className="settings-section-card">
              <div className="section-header">
                <h3>Help & About</h3>
                <p>Application specifications, keyboard shortcuts, and troubleshooting.</p>
              </div>

              <div className="app-info-card">
                <div className="app-info-icon">
                  <i className="fa-solid fa-comments"></i>
                </div>
                <div className="app-info-details">
                  <h4>GRID MyChat Web</h4>
                  <p>Version 2.4.0 (Build 2026.09.10) • Production Ready</p>
                  <span className="app-info-status">
                    <i className="fa-solid fa-circle"></i> Connected to Cloud Gateway
                  </span>
                </div>
              </div>

              <div className="shortcuts-card">
                <h4 className="shortcuts-title">
                  <i className="fa-solid fa-keyboard"></i> Keyboard Shortcuts
                </h4>
                <div className="shortcuts-grid">
                  <div className="shortcut-row">
                    <span>Send Message</span>
                    <kbd>Enter</kbd>
                  </div>
                  <div className="shortcut-row">
                    <span>New Line in Composer</span>
                    <kbd>Shift + Enter</kbd>
                  </div>
                  <div className="shortcut-row">
                    <span>Search Chats</span>
                    <kbd>Ctrl + K</kbd>
                  </div>
                  <div className="shortcut-row">
                    <span>Close Panel / Modal</span>
                    <kbd>Esc</kbd>
                  </div>
                </div>
              </div>

              <div className="settings-reset-card">
                <div>
                  <strong>Reset All Preferences</strong>
                  <p>Revert appearance, theme, and chat configurations back to default.</p>
                </div>
                <button type="button" className="secondary-action-btn" onClick={handleResetSettings}>
                  <i className="fa-solid fa-rotate-left"></i> Reset Defaults
                </button>
              </div>
            </div>
          )}
        </section>
      </div>
    </div>
  );
}

export default Settings;
