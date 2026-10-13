import { useState, useRef } from "react";
import { getAvatarUrl } from "../../utils/avatar";
import "./Profile.css";

const aboutPresets = [
  "Creating clean, responsive design systems and real-time chat experiences.",
  "Available for design critiques and system architecture discussions.",
  "In a sprint review. Please ping for urgent matters.",
  "At work • Focus mode active 🎯",
  "Coffee enthusiast ☕ | Building the future of messaging",
];

const BIO_MAX_LENGTH = 180;

function Profile({
  currentUser,
  onUpdateCurrentUser,
  onUploadAvatar,
  onRemoveAvatar,
  isClosing,
  onBack,
  onToast,
  onOpenSettings,
}) {
  const [isEditing, setIsEditing] = useState(false);
  const [isSaving, setIsSaving] = useState(false);
  const [editName, setEditName] = useState(currentUser?.name || "");
  const [editRole, setEditRole] = useState(currentUser?.role || "");
  const [editPhone, setEditPhone] = useState(currentUser?.phone || "");
  const [editBio, setEditBio] = useState(currentUser?.bio || "");
  const [showAvatarPicker, setShowAvatarPicker] = useState(false);
  const [showQRModal, setShowQRModal] = useState(false);

  // Bio state in Hero Section
  const [isEditingBio, setIsEditingBio] = useState(false);
  const [heroBioText, setHeroBioText] = useState(currentUser?.bio || "");
  const deviceFileInputRef = useRef(null);

  const openBioEditor = () => {
    setHeroBioText(currentUser?.bio || "");
    setIsEditingBio(true);
  };

  // Open the edit form with the latest saved values
  const openEditForm = () => {
    setEditName(currentUser?.name || "");
    setEditRole(currentUser?.role || "");
    setEditPhone(currentUser?.phone || "");
    setEditBio(currentUser?.bio || "");
    setIsEditing(true);
  };

  // Persist through App (DB when logged in); resolves to true on success
  const saveUser = async (updated, successMessage) => {
    setIsSaving(true);
    const result = await onUpdateCurrentUser(updated);
    setIsSaving(false);
    if (result && !result.success) {
      onToast?.(result.message || "Could not save profile");
      return false;
    }
    onToast?.(successMessage);
    return true;
  };

  // Quick Hero Bio Save
  const handleSaveHeroBio = async () => {
    const updated = {
      ...currentUser,
      bio: heroBioText.trim(),
    };
    if (await saveUser(updated, "Bio updated! ✨")) {
      setIsEditingBio(false);
    }
  };

  const handleCancelHeroBio = () => {
    setHeroBioText(currentUser?.bio || "");
    setIsEditingBio(false);
  };

  // Device Photo Upload Handler
  const handleDevicePhotoUpload = async (e) => {
    const file = e.target.files?.[0];
    e.target.value = "";
    if (!file) return;

    if (!file.type.startsWith("image/")) {
      onToast?.("Please choose a valid image file");
      return;
    }

    if (file.size > 8 * 1024 * 1024) {
      onToast?.("Photo too large (max 8MB)");
      return;
    }

    setIsSaving(true);
    onToast?.("Uploading photo...");
    const result = await onUploadAvatar?.(file);
    setIsSaving(false);
    if (!result?.success) {
      onToast?.(result?.message || "Could not upload photo");
      return;
    }
    setShowAvatarPicker(false);
    onToast?.("Profile photo updated from device! 📸");
  };

  // Only a photo uploaded in the chat app can be removed (a CRM photo is managed in the CRM)
  const canRemovePhoto = currentUser?.avatarSource
    ? currentUser.avatarSource === "chat"
    : typeof currentUser?.avatar === "string" && currentUser.avatar.startsWith("data:");

  const handleRemovePhoto = async () => {
    if (isSaving) return;
    setIsSaving(true);
    const result = await onRemoveAvatar?.();
    setIsSaving(false);
    if (!result?.success) {
      onToast?.(result?.message || "Could not remove photo");
      return;
    }
    setShowAvatarPicker(false);
    onToast?.("Profile photo removed");
  };

  // Save Full Profile Edits
  const handleSaveProfile = async (e) => {
    e.preventDefault();
    if (!editName.trim()) {
      onToast?.("Display name cannot be empty");
      return;
    }

    const updated = {
      ...currentUser,
      name: editName.trim(),
      role: editRole.trim(),
      phone: editPhone.trim(),
      bio: editBio.trim(),
    };

    if (await saveUser(updated, "Profile changes saved successfully! ✨")) {
      setIsEditing(false);
    }
  };

  // Copy Profile Link
  const handleCopyProfileLink = () => {
    const handle = (currentUser?.name || "user").toLowerCase().replace(/[^a-z0-9]/g, "");
    const link = `https://gridchat.app/u/${handle}`;
    navigator.clipboard?.writeText?.(link);
    onToast?.("Profile link copied to clipboard! 🔗");
  };

  // Quick bio chip click
  const handleApplyBioPreset = (preset) => {
    setEditBio(preset);
  };

  return (
    <div className={`user-profile-workstation-page ${isClosing ? "closing" : ""}`}>
      {/* Top Header */}
      <header className="profile-master-header">
        <div className="profile-header-left">
          <button
            className="profile-nav-back-btn"
            onClick={onBack}
            title="Back to Chats"
            aria-label="Back to Chats"
          >
            <i className="fa-solid fa-arrow-left"></i>
          </button>
          <div className="profile-header-titles">
            <h1 className="profile-page-h1">My Profile</h1>
            <span className="profile-page-sub">Manage your account information and preferences</span>
          </div>
        </div>

        <div className="profile-header-right">
          <button
            className="profile-header-action-btn"
            onClick={() => setShowQRModal(true)}
            title="Show My QR Code"
            aria-label="Show QR Code"
          >
            <i className="fa-solid fa-qrcode"></i>
            <span>My QR Code</span>
          </button>

          <button
            className={`profile-header-action-btn primary ${isEditing ? "active-editing" : ""}`}
            onClick={() => (isEditing ? setIsEditing(false) : openEditForm())}
            title={isEditing ? "Cancel Editing" : "Edit Profile"}
          >
            <i className={`fa-solid ${isEditing ? "fa-xmark" : "fa-pen"}`}></i>
            <span>{isEditing ? "Cancel" : "Edit Profile"}</span>
          </button>
        </div>
      </header>

      {/* Main Content Scroller */}
      <main className="profile-content-scroller">
        <div className="profile-layout-wrapper">
          {/* Hero Profile Banner & Avatar Card */}
          <section className="profile-hero-card">
            <div className="hero-cover-backdrop">
              <div className="hero-gradient-overlay"></div>
            </div>

            <div className="hero-content-row">
              {/* Avatar with Camera Overlay */}
              <div className="hero-avatar-container">
                <img
                  src={getAvatarUrl(currentUser?.avatar, 240, currentUser?.name)}
                  alt={currentUser?.name || "User"}
                  className="hero-user-avatar-img"
                />
                <button
                  className="btn-change-avatar-camera"
                  onClick={() => setShowAvatarPicker(true)}
                  title="Change Profile Photo"
                  aria-label="Change Profile Photo"
                >
                  <i className="fa-solid fa-camera"></i>
                </button>
              </div>

              {/* Name, Role, and Bio Section */}
              <div className="hero-identity-block">
                <div className="hero-name-badge-row">
                  <h2 className="hero-display-name">{currentUser?.name || "User"}</h2>
                  <span className="hero-verified-tag" title="Verified Account">
                    <i className="fa-solid fa-circle-check"></i>
                  </span>
                  <span className="hero-pro-tag">PRO</span>
                </div>

                <p className="hero-role-headline">
                  {currentUser?.role || "Team Member"}
                </p>

                {/* Hero Bio Feature (Replaces Available • Online status) */}
                <div className="hero-bio-section">
                  {isEditingBio ? (
                    <div className="hero-bio-edit-container">
                      <div className="hero-bio-textarea-wrapper">
                        <textarea
                          className="hero-bio-textarea"
                          value={heroBioText}
                          onChange={(e) => setHeroBioText(e.target.value)}
                          placeholder="Write a bio about yourself..."
                          maxLength={BIO_MAX_LENGTH}
                          rows={2}
                          autoFocus
                        />
                        <span className="hero-bio-counter">{heroBioText.length}/{BIO_MAX_LENGTH}</span>
                      </div>
                      <div className="hero-bio-actions">
                        <button
                          type="button"
                          className="hero-bio-btn-cancel"
                          onClick={handleCancelHeroBio}
                          title="Cancel"
                        >
                          <i className="fa-solid fa-xmark"></i> Cancel
                        </button>
                        <button
                          type="button"
                          className="hero-bio-btn-save"
                          onClick={handleSaveHeroBio}
                          title="Save Bio"
                          disabled={isSaving}
                        >
                          <i className="fa-solid fa-check"></i> {isSaving ? "Saving..." : "Save Bio"}
                        </button>
                      </div>
                    </div>
                  ) : (
                    <div
                      className="hero-bio-display-card"
                      onClick={openBioEditor}
                      title="Click to edit your bio"
                      role="button"
                      tabIndex={0}
                    >
                      <i className="fa-solid fa-quote-left hero-bio-quote"></i>
                      <p className="hero-bio-text">
                        {currentUser?.bio || "Hey there! I am using GRID Chat. Tap to add your bio."}
                      </p>
                      <span className="hero-bio-edit-icon" title="Edit bio">
                        <i className="fa-solid fa-pen"></i>
                      </span>
                    </div>
                  )}
                </div>
              </div>
            </div>
          </section>

          {/* Quick Metrics & Account Activity Row */}
          <section className="profile-metrics-strip">
            <div className="metric-box">
              <div className="metric-icon-wrap icon-purple">
                <i className="fa-solid fa-comments"></i>
              </div>
              <div className="metric-meta">
                <span className="metric-number">17</span>
                <span className="metric-label">Conversations</span>
              </div>
            </div>

            <div className="metric-box">
              <div className="metric-icon-wrap icon-blue">
                <i className="fa-solid fa-compass"></i>
              </div>
              <div className="metric-meta">
                <span className="metric-number">3</span>
                <span className="metric-label">Moments Stories</span>
              </div>
            </div>

            <div className="metric-box">
              <div className="metric-icon-wrap icon-emerald">
                <i className="fa-solid fa-bullhorn"></i>
              </div>
              <div className="metric-meta">
                <span className="metric-number">2</span>
                <span className="metric-label">Channels Followed</span>
              </div>
            </div>

            <div className="metric-box">
              <div className="metric-icon-wrap icon-amber">
                <i className="fa-solid fa-hard-drive"></i>
              </div>
              <div className="metric-meta">
                <span className="metric-number">199 MB</span>
                <span className="metric-label">Cloud Media</span>
              </div>
            </div>
          </section>

          {/* 2-Column Details & Settings Workstation */}
          <div className="profile-details-grid">
            {/* Left Column: Personal Information & Bio */}
            <div className="details-col-left">
              <div className="profile-card">
                <div className="card-header-bar">
                  <div className="card-header-title">
                    <i className="fa-regular fa-user text-purple"></i>
                    <h3>Personal Information</h3>
                  </div>
                  {!isEditing && (
                    <button
                      className="card-quick-edit-btn"
                      onClick={openEditForm}
                    >
                      <i className="fa-solid fa-pen"></i> Edit
                    </button>
                  )}
                </div>

                {isEditing ? (
                  /* Edit Form */
                  <form className="profile-edit-form" onSubmit={handleSaveProfile}>
                    <div className="form-group">
                      <label className="form-label">Full Name</label>
                      <input
                        type="text"
                        className="form-input"
                        value={editName}
                        onChange={(e) => setEditName(e.target.value)}
                        required
                        maxLength={50}
                      />
                    </div>

                    <div className="form-group">
                      <label className="form-label">Role / Headline</label>
                      <input
                        type="text"
                        className="form-input"
                        value={editRole}
                        onChange={(e) => setEditRole(e.target.value)}
                        placeholder="e.g. Product Designer"
                        maxLength={60}
                      />
                    </div>

                    <div className="form-row-2col">
                      <div className="form-group">
                        <label className="form-label">Phone Number</label>
                        <input
                          type="text"
                          className="form-input"
                          value={editPhone}
                          onChange={(e) => setEditPhone(e.target.value)}
                          maxLength={20}
                        />
                      </div>

                      <div className="form-group">
                        <label className="form-label">Email Address</label>
                        <input
                          type="email"
                          className="form-input"
                          value={currentUser?.email || ""}
                          readOnly
                          title="Email is your login and can't be changed here"
                        />
                      </div>
                    </div>

                    <div className="form-group">
                      <div className="label-counter-row">
                        <label className="form-label">About / Bio</label>
                        <span className="char-counter">{editBio.length}/{BIO_MAX_LENGTH}</span>
                      </div>
                      <textarea
                        className="form-textarea"
                        rows={3}
                        value={editBio}
                        onChange={(e) => setEditBio(e.target.value)}
                        maxLength={BIO_MAX_LENGTH}
                      />

                      {/* Quick Bio Suggestion Chips */}
                      <div className="preset-chips-strip">
                        <span className="preset-hint">Quick suggestions:</span>
                        {aboutPresets.map((preset, idx) => (
                          <button
                            key={idx}
                            type="button"
                            className="preset-chip"
                            onClick={() => handleApplyBioPreset(preset)}
                          >
                            {preset.slice(0, 32)}...
                          </button>
                        ))}
                      </div>
                    </div>

                    <div className="form-action-buttons">
                      <button
                        type="button"
                        className="btn-cancel-edit"
                        onClick={() => setIsEditing(false)}
                      >
                        Cancel
                      </button>
                      <button type="submit" className="btn-save-edit" disabled={isSaving}>
                        <i className="fa-solid fa-check"></i> {isSaving ? "Saving..." : "Save Changes"}
                      </button>
                    </div>
                  </form>
                ) : (
                  /* Display View */
                  <div className="profile-info-list">
                    <div className="info-entry-row">
                      <div className="entry-icon">
                        <i className="fa-regular fa-id-badge"></i>
                      </div>
                      <div className="entry-content">
                        <span className="entry-label">Display Name</span>
                        <span className="entry-value">{currentUser?.name || "User"}</span>
                      </div>
                    </div>

                    <div className="info-entry-row">
                      <div className="entry-icon">
                        <i className="fa-solid fa-at"></i>
                      </div>
                      <div className="entry-content">
                        <span className="entry-label">Username Handle</span>
                        <span className="entry-value handle-text">
                          @{(currentUser?.name || "user").toLowerCase().replace(/[^a-z0-9]/g, "")}
                        </span>
                      </div>
                    </div>

                    <div className="info-entry-row">
                      <div className="entry-icon">
                        <i className="fa-solid fa-phone"></i>
                      </div>
                      <div className="entry-content">
                        <span className="entry-label">Phone Number</span>
                        <span className="entry-value">{currentUser?.phone || "Not set"}</span>
                      </div>
                    </div>

                    <div className="info-entry-row">
                      <div className="entry-icon">
                        <i className="fa-regular fa-envelope"></i>
                      </div>
                      <div className="entry-content">
                        <span className="entry-label">Email</span>
                        <span className="entry-value">
                          {currentUser?.email || "Not set"}
                        </span>
                      </div>
                    </div>

                    <div className="info-entry-row">
                      <div className="entry-icon">
                        <i className="fa-regular fa-comment-dots"></i>
                      </div>
                      <div className="entry-content">
                        <span className="entry-label">About / Bio</span>
                        <p className="entry-bio-text">
                          {currentUser?.bio || "No bio yet"}
                        </p>
                      </div>
                    </div>
                  </div>
                )}
              </div>

              {/* Linked Devices & Active Sessions */}
              <div className="profile-card">
                <div className="card-header-bar">
                  <div className="card-header-title">
                    <i className="fa-solid fa-laptop-code text-blue"></i>
                    <h3>Active Device Sessions</h3>
                  </div>
                </div>

                <div className="sessions-list">
                  <div className="session-item-row is-current-device">
                    <div className="device-icon">
                      <i className="fa-solid fa-desktop"></i>
                    </div>
                    <div className="device-meta">
                      <div className="device-name-line">
                        <span className="device-title">Windows Workstation • Chrome</span>
                        <span className="active-now-badge">Active Now</span>
                      </div>
                      <span className="device-subtext">Desktop Client • Hyderabad, India</span>
                    </div>
                  </div>

                  <div className="session-item-row">
                    <div className="device-icon">
                      <i className="fa-solid fa-mobile-screen"></i>
                    </div>
                    <div className="device-meta">
                      <div className="device-name-line">
                        <span className="device-title">Samsung Galaxy S8+</span>
                      </div>
                      <span className="device-subtext">Mobile App • Active 10 minutes ago</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* Right Column: QR Code Sharing, Privacy & Security */}
            <div className="details-col-right">
              {/* QR Code Sharing Card */}
              <div className="profile-card qr-card-widget">
                <div className="card-header-bar">
                  <div className="card-header-title">
                    <i className="fa-solid fa-qrcode text-purple"></i>
                    <h3>Quick Share Profile</h3>
                  </div>
                </div>

                <div className="qr-preview-box">
                  <div className="qr-visual-frame" onClick={() => setShowQRModal(true)}>
                    {/* Simulated SVG QR Code pattern */}
                    <div className="qr-matrix-simulation">
                      <i className="fa-solid fa-qrcode big-qr-icon"></i>
                      <img
                        src={getAvatarUrl(currentUser?.avatar, 80, currentUser?.name)}
                        alt="Profile"
                        className="qr-center-avatar"
                      />
                    </div>
                  </div>
                  <p className="qr-instruction-text">
                    Scan with any camera or phone to instantly start a conversation with {(currentUser?.name || "me").split(" ")[0]}.
                  </p>

                  <div className="qr-card-actions">
                    <button
                      className="btn-copy-link"
                      onClick={handleCopyProfileLink}
                    >
                      <i className="fa-solid fa-link"></i> Copy Link
                    </button>
                    <button
                      className="btn-enlarge-qr"
                      onClick={() => setShowQRModal(true)}
                    >
                      <i className="fa-solid fa-expand"></i> Enlarge QR
                    </button>
                  </div>
                </div>
              </div>

              {/* Privacy & Account Security Card */}
              <div className="profile-card">
                <div className="card-header-bar">
                  <div className="card-header-title">
                    <i className="fa-solid fa-shield-halved text-emerald"></i>
                    <h3>Privacy & Security Badges</h3>
                  </div>
                </div>

                <div className="security-badges-list">
                  <div className="security-badge-item">
                    <div className="badge-icon-wrap icon-green">
                      <i className="fa-solid fa-lock"></i>
                    </div>
                    <div className="badge-text-group">
                      <h4>End-to-End Encryption</h4>
                      <p>Your messages, voice calls, and media are cryptographically secured.</p>
                    </div>
                  </div>

                  <div className="security-badge-item">
                    <div className="badge-icon-wrap icon-purple">
                      <i className="fa-solid fa-key"></i>
                    </div>
                    <div className="badge-text-group">
                      <h4>Two-Step Verification</h4>
                      <p>Enabled. Extra PIN layer protects account authentication.</p>
                    </div>
                  </div>

                  <div className="security-badge-item">
                    <div className="badge-icon-wrap icon-blue">
                      <i className="fa-regular fa-eye"></i>
                    </div>
                    <div className="badge-text-group">
                      <h4>Profile Privacy</h4>
                      <p>Photo and status visible to My Contacts only.</p>
                    </div>
                  </div>
                </div>

                {onOpenSettings && (
                  <button
                    className="btn-open-settings-shortcut"
                    onClick={onOpenSettings}
                  >
                    <i className="fa-solid fa-gear"></i> Open Privacy & Security Settings
                  </button>
                )}
              </div>
            </div>
          </div>
        </div>
      </main>

      {/* Avatar Picker Modal with Device Photo Upload */}
      {showAvatarPicker && (
        <div className="profile-modal-overlay" onClick={() => setShowAvatarPicker(false)}>
          <div className="avatar-picker-dialog" onClick={(e) => e.stopPropagation()}>
            <header className="dialog-header">
              <div className="dialog-title-row">
                <i className="fa-solid fa-camera"></i>
                <h3>Choose Profile Photo</h3>
              </div>
              <button
                className="btn-dialog-close"
                onClick={() => setShowAvatarPicker(false)}
              >
                <i className="fa-solid fa-xmark"></i>
              </button>
            </header>

            {/* Hidden device photo input */}
            <input
              type="file"
              ref={deviceFileInputRef}
              accept="image/*"
              style={{ display: "none" }}
              onChange={handleDevicePhotoUpload}
            />

            {/* Primary Action: Choose from Device */}
            <div className="avatar-upload-prompt-card" onClick={() => deviceFileInputRef.current?.click()}>
              <div className="avatar-upload-icon-circle">
                <i className="fa-solid fa-cloud-arrow-up"></i>
              </div>
              <div className="avatar-upload-meta">
                <h4 className="avatar-upload-title">Choose from device</h4>
                <p className="avatar-upload-sub">Upload JPG, PNG, or WebP photo from your phone or computer</p>
              </div>
              <button
                type="button"
                className="btn-choose-device-action"
                onClick={(e) => {
                  e.stopPropagation();
                  deviceFileInputRef.current?.click();
                }}
              >
                <i className="fa-solid fa-folder-open"></i> Browse
              </button>
            </div>

            {canRemovePhoto && (
              <button
                type="button"
                className="avatar-remove-photo-btn"
                onClick={handleRemovePhoto}
                disabled={isSaving}
              >
                <i className={`fa-solid ${isSaving ? "fa-spinner fa-spin" : "fa-trash-can"}`}></i>
                Remove photo
              </button>
            )}

            <p className="avatar-initials-hint">
              <img src={getAvatarUrl("", 64, currentUser?.name)} alt="" />
              Without a photo, your initials are shown.
            </p>
          </div>
        </div>
      )}

      {/* Enlarge QR Code Modal */}
      {showQRModal && (
        <div className="profile-modal-overlay" onClick={() => setShowQRModal(false)}>
          <div className="qr-modal-dialog" onClick={(e) => e.stopPropagation()}>
            <header className="dialog-header">
              <div className="dialog-title-row">
                <i className="fa-solid fa-qrcode"></i>
                <h3>My Profile QR Code</h3>
              </div>
              <button
                className="btn-dialog-close"
                onClick={() => setShowQRModal(false)}
              >
                <i className="fa-solid fa-xmark"></i>
              </button>
            </header>

            <div className="qr-modal-body">
              <div className="qr-large-frame">
                <i className="fa-solid fa-qrcode giant-qr-icon"></i>
                <img
                  src={getAvatarUrl(currentUser?.avatar, 120, currentUser?.name)}
                  alt="Profile"
                  className="qr-large-center-avatar"
                />
              </div>

              <h4 className="qr-modal-user-name">{currentUser?.name || "User"}</h4>
              <span className="qr-modal-user-handle">
                @{(currentUser?.name || "user").toLowerCase().replace(/[^a-z0-9]/g, "")}
              </span>
              <p className="qr-modal-instruction">
                Your QR code is private. If you share it with someone, they can scan it with their camera to add you as a contact.
              </p>

              <div className="qr-modal-buttons-row">
                <button
                  className="btn-qr-action-copy"
                  onClick={handleCopyProfileLink}
                >
                  <i className="fa-solid fa-link"></i> Copy Link
                </button>
                <button
                  className="btn-qr-action-share"
                  onClick={() => {
                    handleCopyProfileLink();
                    setShowQRModal(false);
                  }}
                >
                  <i className="fa-solid fa-share-nodes"></i> Share Code
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default Profile;
