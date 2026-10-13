import { useEffect, useRef, useState } from "react";
import AllMediaGallery from "./AllMediaGallery";
import EditContact from "./EditContact";
import ShareContactModal from "./ShareContactModal";
import { getAvatarUrl } from "../../utils/avatar";
import "./ContactProfile.css";

function ContactProfile({
  contact,
  messages = [],
  contacts = [],
  onClose,
  onBack,
  onToast,
  onUpdateContact,
  onShareContact,
  onStartCall,
  initialShowMedia = false,
  onMediaOpen,
  onMediaClose,
}) {
  const [showAllMedia, setShowAllMedia] = useState(initialShowMedia);
  const [menuOpen, setMenuOpen] = useState(false);
  const [isEditing, setIsEditing] = useState(false);
  const [isSharing, setIsSharing] = useState(false);
  const [currentContact, setCurrentContact] = useState(contact);
  const menuRef = useRef(null);

  useEffect(() => {
    setCurrentContact(contact);
  }, [contact]);

  useEffect(() => {
    setShowAllMedia(initialShowMedia);
  }, [initialShowMedia]);

  useEffect(() => {
    if (!menuOpen) return;

    const handleClickOutside = (e) => {
      if (menuRef.current && !menuRef.current.contains(e.target)) {
        setMenuOpen(false);
      }
    };

    const handleKeyDown = (e) => {
      if (e.key === "Escape") {
        setMenuOpen(false);
      }
    };

    document.addEventListener("mousedown", handleClickOutside);
    document.addEventListener("keydown", handleKeyDown);
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
      document.removeEventListener("keydown", handleKeyDown);
    };
  }, [menuOpen]);

  const handleOpenMedia = () => {
    setShowAllMedia(true);
    if (onMediaOpen) onMediaOpen();
  };

  const handleCloseMedia = () => {
    setShowAllMedia(false);
    if (onMediaClose) onMediaClose();
  };

  if (!currentContact) return null;

  if (isEditing) {
    return (
      <EditContact
        contact={currentContact}
        onBack={() => setIsEditing(false)}
        onSave={(updated) => {
          setCurrentContact(updated);
          if (onUpdateContact) onUpdateContact(updated);
          setIsEditing(false);
        }}
        onToast={onToast}
      />
    );
  }

  if (showAllMedia) {
    return (
      <AllMediaGallery
        contact={currentContact}
        messages={messages}
        onBack={handleCloseMedia}
        onClose={() => {
          handleCloseMedia();
          if (onClose) onClose();
        }}
        onToast={onToast}
      />
    );
  }

  const handleAction = (actionName) => {
    if (onToast) {
      onToast(`${actionName} triggered`);
    }
  };

  // Extract recent media files, images, and documents from this chat's messages
  const chatMedia = (messages || [])
    .filter(
      (m) =>
        m.messageType === "file" ||
        m.messageType === "finished" ||
        m.messageType === "image" ||
        m.image ||
        m.fileName
    )
    .map((m, idx) => ({
      id: m.id || idx,
      type: m.image || m.messageType === "image" ? "image" : "file",
      title: m.fileName || m.text || "Media file",
      size: m.fileSize || "",
      image: m.image,
      time: m.time || "",
    }));

  return (
    <div className="contact-profile-page">
      {/* Top Header matching WhatsApp Web Reference 1 */}
      <header className="profile-header">
        <button
          className="back-btn"
          onClick={onBack || onClose}
          title="Back"
          aria-label="Back"
        >
          <i className="fa-solid fa-arrow-left"></i>
        </button>

        <h2 className="profile-header-title">
          {currentContact.isCurrentUser ? "My Profile" : "Contact info"}
        </h2>

        <div className="profile-header-right-actions">
          {/* 3-dots dropdown menu */}
          <div className="profile-menu-wrapper" ref={menuRef}>
            <button
              type="button"
              className={`profile-3dots-btn ${menuOpen ? "active" : ""}`}
              onClick={() => setMenuOpen((prev) => !prev)}
              title="More options"
              aria-label="More options"
              aria-expanded={menuOpen}
            >
              <i className="fa-solid fa-ellipsis-vertical"></i>
            </button>

            {menuOpen && (
              <div className="profile-dropdown-menu" role="menu">
                <button
                  type="button"
                  className="profile-dropdown-item"
                  role="menuitem"
                  onClick={() => {
                    setMenuOpen(false);
                    setIsSharing(true);
                  }}
                >
                  Share
                </button>
                <button
                  type="button"
                  className="profile-dropdown-item"
                  role="menuitem"
                  onClick={() => {
                    setMenuOpen(false);
                    setIsEditing(true);
                  }}
                >
                  Edit
                </button>
              </div>
            )}
          </div>

          {onClose && (
            <button
              type="button"
              className="profile-header-close-btn"
              onClick={onClose}
              title="Close"
              aria-label="Close"
            >
              <i className="fa-solid fa-xmark"></i>
            </button>
          )}
        </div>
      </header>

      <div className="profile-scroll-content">
        {/* Profile Hero */}
        <div className="profile-hero">
          <div className="contact-hero-avatar-wrap hero-avatar-wrap">
            {/* Photo, or the name's initials when there is none */}
            <img src={getAvatarUrl(currentContact.avatar, 240, currentContact.name)} alt={currentContact.name} />
          </div>

          <h1 className="hero-name">{currentContact.name}</h1>
          <p className="hero-phone">{currentContact.phone || "+91 90590 44812"}</p>
          <span className="hero-status">
            {currentContact.status || "Last seen today at 2:30 pm"}
          </span>
        </div>

        {/* Action Buttons Row */}
        <div className="profile-actions-row">
          {currentContact.isCurrentUser ? (
            <>
              <button
                className="profile-action-btn"
                onClick={() => handleAction("Edit Profile")}
              >
                <div className="action-circle">
                  <i className="fa-solid fa-pen"></i>
                </div>
                <span>Edit</span>
              </button>

              <button
                className="profile-action-btn"
                onClick={() => handleAction("QR Code")}
              >
                <div className="action-circle">
                  <i className="fa-solid fa-qrcode"></i>
                </div>
                <span>QR Code</span>
              </button>

              <button
                className="profile-action-btn"
                onClick={() => handleAction("Privacy settings")}
              >
                <div className="action-circle">
                  <i className="fa-solid fa-shield-halved"></i>
                </div>
                <span>Privacy</span>
              </button>

              <button
                className="profile-action-btn"
                onClick={() => handleAction("Account settings")}
              >
                <div className="action-circle">
                  <i className="fa-solid fa-gear"></i>
                </div>
                <span>Settings</span>
              </button>
            </>
          ) : (
            <>
              <button
                className="profile-action-btn"
                onClick={() => onStartCall?.(currentContact, "audio")}
              >
                <div className="action-circle">
                  <i className="fa-solid fa-phone"></i>
                </div>
                <span>Voice</span>
              </button>

              <button
                className="profile-action-btn"
                onClick={() => onStartCall?.(currentContact, "video")}
              >
                <div className="action-circle">
                  <i className="fa-solid fa-video"></i>
                </div>
                <span>Video</span>
              </button>

              <button
                className="profile-action-btn"
                onClick={() => handleAction("Payment")}
              >
                <div className="action-circle">
                  <i className="fa-solid fa-indian-rupee-sign"></i>
                </div>
                <span>Pay</span>
              </button>

              <button
                className="profile-action-btn"
                onClick={() => handleAction("Search messages")}
              >
                <div className="action-circle">
                  <i className="fa-solid fa-magnifying-glass"></i>
                </div>
                <span>Search</span>
              </button>
            </>
          )}
        </div>

        {/* Media, links, and docs */}
        <div className="profile-section media-section">
          <div
            className="section-header"
            onClick={handleOpenMedia}
            title="Open all media, links, and docs"
          >
            <span>Media, links, and docs</span>
            <span className="count">
              {chatMedia.length} <i className="fa-solid fa-chevron-right"></i>
            </span>
          </div>

          {chatMedia.length > 0 ? (
            <div className="media-preview-carousel">
              {chatMedia.map((m) => (
                <div
                  key={m.id}
                  className="media-thumb-item"
                  onClick={handleOpenMedia}
                  title={m.title}
                >
                  {m.type === "image" && m.image ? (
                    <img
                      src={m.image}
                      alt={m.title}
                      className="media-thumb-img"
                    />
                  ) : (
                    <div className="media-thumb-file">
                      <i className="fa-solid fa-file-zipper"></i>
                      <span>{m.title}</span>
                      {m.size && <small>{m.size}</small>}
                    </div>
                  )}
                </div>
              ))}
            </div>
          ) : (
            <div className="no-media-box">
              <i className="fa-regular fa-images"></i>
              <span>No media, links, or docs shared yet</span>
            </div>
          )}
        </div>

        {/* Visit Channel Card (Placed directly below media) */}
        {!currentContact.isCurrentUser && (
          <div className="profile-section channel-card-section">
            <div
              className="visit-channel-card"
              onClick={() => handleAction(`Visited ${currentContact.name}'s Official Channel`)}
              title={`Visit ${currentContact.name}'s Channel`}
            >
              <div className="channel-card-left">
                <div className="channel-badge-avatar">
                  <img src={getAvatarUrl(currentContact.avatar, 120, currentContact.name)} alt={currentContact.name} />
                  <span className="channel-verified-badge" title="Verified Channel">
                    <i className="fa-solid fa-check"></i>
                  </span>
                </div>

                <div className="channel-card-info">
                  <div className="channel-name-row">
                    <h3>{contact.name} Official</h3>
                    <span className="channel-tag">Channel</span>
                  </div>
                  <p className="channel-subtext">
                    <i className="fa-solid fa-bullhorn channel-icon"></i>
                    <span>Official updates, community posts & broadcasts</span>
                  </p>
                  <div className="channel-metrics">
                    <span className="follower-count">
                      <i className="fa-solid fa-users"></i> 14.8K followers
                    </span>
                  </div>
                </div>
              </div>

              <button
                className="visit-channel-btn"
                type="button"
                onClick={(e) => {
                  e.stopPropagation();
                  handleAction(`Visited ${contact.name}'s Official Channel`);
                }}
              >
                <span>Visit</span>
                <i className="fa-solid fa-arrow-right"></i>
              </button>
            </div>
          </div>
        )}

        {/* Settings Options List */}
        <div className="profile-section options-section">
          <div
            className="profile-list-item"
            onClick={() => handleAction("Manage storage")}
          >
            <div className="item-icon">
              <i className="fa-regular fa-images"></i>
            </div>
            <div className="item-text">
              <h3>Manage storage</h3>
              <p>199.2 MB</p>
            </div>
          </div>

          <div
            className="profile-list-item"
            onClick={() => handleAction("Notifications")}
          >
            <div className="item-icon">
              <i className="fa-regular fa-bell"></i>
            </div>
            <div className="item-text">
              <h3>Notifications</h3>
            </div>
          </div>

          <div
            className="profile-list-item"
            onClick={() => handleAction("Media visibility")}
          >
            <div className="item-icon">
              <i className="fa-regular fa-image"></i>
            </div>
            <div className="item-text">
              <h3>Media visibility</h3>
              <p>On</p>
            </div>
          </div>

          <div
            className="profile-list-item"
            onClick={() => handleAction("Encryption info")}
          >
            <div className="item-icon">
              <i className="fa-solid fa-lock"></i>
            </div>
            <div className="item-text">
              <h3>Encryption</h3>
              <p>Messages and calls are end-to-end encrypted. Tap to verify.</p>
            </div>
          </div>

          <div
            className="profile-list-item"
            onClick={() => handleAction("Disappearing messages")}
          >
            <div className="item-icon">
              <i className="fa-regular fa-clock"></i>
            </div>
            <div className="item-text">
              <h3>Disappearing messages</h3>
              <p>Off</p>
            </div>
          </div>
        </div>
      </div>

      {isSharing && (
        <ShareContactModal
          contacts={(contacts || []).filter((c) => c.id !== currentContact.id)}
          onClose={() => setIsSharing(false)}
          onSelect={(targetMember) => {
            if (onShareContact) {
              onShareContact(targetMember, currentContact);
            } else if (onToast) {
              onToast(`Contact shared to ${targetMember.name}`);
            }
            setIsSharing(false);
          }}
        />
      )}
    </div>
  );
}

export default ContactProfile;
