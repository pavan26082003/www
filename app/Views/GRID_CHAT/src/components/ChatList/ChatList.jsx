import { useState, useRef, useEffect } from "react";
import { createPortal } from "react-dom";
import ChatCard from "./ChatCard";
import NewChatPicker from "./NewChatPicker";
import gridLogoLight from "../../assets/grid_logo_light.png";
import gridLogoDark from "../../assets/grid_logo_dark.png";
import { getAvatarUrl } from "../../utils/avatar";
import "./ChatList.css";

function ChatList({
  currentUser,
  chats = [],
  selectedChat,
  onSelectChat,
  onNewChat,
  onStartChat,
  onSearchContacts,
  onOpenProfile,
  onToast,
  onOpenSettingsSection,
  onMarkAllRead,
  viewMode: propViewMode,
  onToggleViewMode,
}) {
  const [search, setSearch] = useState("");
  const [filterTab, setFilterTab] = useState("all");
  const [menuOpen, setMenuOpen] = useState(false);

  // Custom new-contact modal state
  const [newContactModalOpen, setNewContactModalOpen] = useState(false);
  const [newContactName, setNewContactName] = useState("");
  const [newContactPhone, setNewContactPhone] = useState("");
  const [newContactError, setNewContactError] = useState("");
  // Backend mode: pick a real person instead of creating a local contact
  const [newChatPickerOpen, setNewChatPickerOpen] = useState(false);

  const menuRef = useRef(null);
  const [internalViewMode, setInternalViewMode] = useState(() => {
    return localStorage.getItem("chatViewMode_v2") || "grid";
  });

  const viewMode = propViewMode !== undefined ? propViewMode : internalViewMode;

  useEffect(() => {
    const handleOutsideClick = (e) => {
      if (menuRef.current && !menuRef.current.contains(e.target)) {
        setMenuOpen(false);
      }
    };
    const handleKeyDown = (e) => {
      if (e.key === "Escape") {
        setMenuOpen(false);
      }
    };

    if (menuOpen) {
      document.addEventListener("mousedown", handleOutsideClick);
      document.addEventListener("touchstart", handleOutsideClick);
      document.addEventListener("keydown", handleKeyDown);
    }
    return () => {
      document.removeEventListener("mousedown", handleOutsideClick);
      document.removeEventListener("touchstart", handleOutsideClick);
      document.removeEventListener("keydown", handleKeyDown);
    };
  }, [menuOpen]);

  useEffect(() => {
    if (!newContactModalOpen) return;

    const handleModalKeyDown = (e) => {
      if (e.key === "Escape") {
        e.preventDefault();
        closeNewContactModal();
        return;
      }

      if (e.key === "Enter" && !e.shiftKey) {
        const activeElement = document.activeElement;

        if (
          activeElement &&
          activeElement.tagName !== "BUTTON" &&
          activeElement.tagName !== "TEXTAREA"
        ) {
          e.preventDefault();
          handleCreateContact();
        }
      }
    };

    document.addEventListener("keydown", handleModalKeyDown);

    return () => {
      document.removeEventListener("keydown", handleModalKeyDown);
    };
  }, [newContactModalOpen, newContactName, newContactPhone]);

  const handleToggleView = () => {
    const nextMode = viewMode === "list" ? "grid" : "list";
    if (onToggleViewMode) {
      onToggleViewMode(nextMode);
    } else {
      setInternalViewMode(nextMode);
    }
    try {
      localStorage.setItem("chatViewMode_v2", nextMode);
      localStorage.setItem("chatViewMode", nextMode);
    } catch {
      // ignore
    }

    if (onToast) {
      onToast(`Switched to ${nextMode === "grid" ? "Grid" : "List"} view`);
    }
  };

  const filteredChats = chats.filter((chat) => {
    // Filter tab
    if (filterTab === "unread" && !chat.unread) return false;
    if (filterTab === "favourites" && !chat.isFavourite) return false;
    if (filterTab === "groups" && !chat.isGroup) return false;

    // Search query
    const query = search.toLowerCase().trim();
    if (!query) return true;

    const haystack = `
      ${chat.name}
      ${chat.text || ""}
    `.toLowerCase();

    return haystack.includes(query);
  });

  const handleSelectChat = (chat) => {
    onSelectChat(chat);
  };

  const handleNewChat = () => {
    setMenuOpen(false);
    if (onStartChat && onSearchContacts) {
      setNewChatPickerOpen(true);
      return;
    }
    setNewContactName("");
    setNewContactPhone("");
    setNewContactError("");
    setNewContactModalOpen(true);
  };

  const closeNewContactModal = () => {
    setNewContactModalOpen(false);
    setNewContactName("");
    setNewContactPhone("");
    setNewContactError("");
  };

  const handleCreateContact = () => {
    const cleanName = newContactName.trim();

    if (!cleanName) {
      setNewContactError("Please enter a contact name.");
      return;
    }

    const newChat = {
      id: `contact-${Date.now()}`,
      name: cleanName,
      phone: newContactPhone.trim(),
      time: "Just now",
      status: "online",
      avatar: Math.floor(Math.random() * 70) + 1,
      text: `Start a new conversation with ${cleanName}.`,
      type: "text",
      isNew: true,
    };

    if (onNewChat) {
      onNewChat(newChat);
    } else if (onSelectChat) {
      onSelectChat(newChat);
    }

    closeNewContactModal();
  };

  return (
    <section className="chat-list-panel">

      {/* Unified Top Header (Web & Mobile) */}
      <header className="chat-panel-header">
        <div className="header-left">
          <img
            src={gridLogoLight}
            alt="GRID"
            className="chat-header-logo logo-light"
          />
          <img
            src={gridLogoDark}
            alt="GRID"
            className="chat-header-logo logo-dark"
          />
        </div>

        <div className="header-actions">
          {/* 1. View as List / View as Grid (Symbol only) */}
          <button
            className={`header-view-toggle-btn ${viewMode === "grid" ? "is-grid" : "is-list"}`}
            onClick={handleToggleView}
            title={viewMode === "list" ? "View as Grid" : "View as List"}
            aria-label={viewMode === "list" ? "View as Grid" : "View as List"}
          >
            <i className={`fa-solid ${viewMode === "list" ? "fa-table-cells-large" : "fa-list-ul"}`}></i>
          </button>

          {/* 2. Profile Button (in between List/Grid and 3 dots) */}
          <button
            type="button"
            className="header-profile-btn"
            onClick={onOpenProfile}
            title="My Profile"
            aria-label="My Profile"
          >
            <img
              src={getAvatarUrl(currentUser?.avatar, 100, currentUser?.name)}
              alt={currentUser?.name || "Profile"}
              className="header-profile-avatar-img"
            />
          </button>

          {/* 3. Three Dots Options with WhatsApp Style Dropdown */}
          <div className="header-menu-container" ref={menuRef}>
            <button
              className={`header-menu-btn ${menuOpen ? "active" : ""}`}
              title="More options"
              aria-label="More options"
              onClick={(e) => {
                e.stopPropagation();
                setMenuOpen((prev) => !prev);
              }}
            >
              <i className="fa-solid fa-ellipsis-vertical"></i>
            </button>

            {menuOpen && (
              <div className="whatsapp-dropdown-menu">
                {/* Chat Quick Actions (Reference Image 4 & 5) */}
                <button
                  type="button"
                  className="dropdown-menu-item"
                  onClick={() => {
                    setMenuOpen(false);
                    onToast?.("New group creation coming soon");
                  }}
                >
                  <span className="item-icon-tile icon-emerald">
                    <i className="fa-solid fa-user-group"></i>
                  </span>
                  <span className="item-label">New group</span>
                </button>

                <button
                  type="button"
                  className="dropdown-menu-item"
                  onClick={() => {
                    setMenuOpen(false);
                    setFilterTab("favourites");
                    onToast?.("Showing Starred conversations");
                  }}
                >
                  <span className="item-icon-tile icon-amber">
                    <i className="fa-solid fa-star"></i>
                  </span>
                  <span className="item-label">Starred</span>
                </button>

                <button
                  type="button"
                  className="dropdown-menu-item"
                  onClick={() => {
                    setMenuOpen(false);
                    onMarkAllRead?.();
                    onToast?.("All chats marked as read ✓✓");
                  }}
                >
                  <span className="item-icon-tile icon-teal">
                    <i className="fa-solid fa-check-double"></i>
                  </span>
                  <span className="item-label">Read all</span>
                </button>

                <div className="dropdown-menu-divider"></div>

                {/* Settings Section Options (As Selected in Reference Image 3) */}
                <button
                  type="button"
                  className="dropdown-menu-item"
                  onClick={() => {
                    setMenuOpen(false);
                    onOpenSettingsSection?.("appearance");
                  }}
                >
                  <span className="item-icon-tile icon-purple">
                    <i className="fa-solid fa-palette"></i>
                  </span>
                  <span className="item-label">Appearance</span>
                </button>

                <button
                  type="button"
                  className="dropdown-menu-item"
                  onClick={() => {
                    setMenuOpen(false);
                    onOpenSettingsSection?.("profile");
                  }}
                >
                  <span className="item-icon-tile icon-blue">
                    <i className="fa-solid fa-user"></i>
                  </span>
                  <span className="item-label">Profile & Account</span>
                </button>

                <button
                  type="button"
                  className="dropdown-menu-item"
                  onClick={() => {
                    setMenuOpen(false);
                    onOpenSettingsSection?.("notifications");
                  }}
                >
                  <span className="item-icon-tile icon-orange">
                    <i className="fa-solid fa-bell"></i>
                  </span>
                  <span className="item-label">Notifications</span>
                </button>

                <button
                  type="button"
                  className="dropdown-menu-item"
                  onClick={() => {
                    setMenuOpen(false);
                    onOpenSettingsSection?.("privacy");
                  }}
                >
                  <span className="item-icon-tile icon-green">
                    <i className="fa-solid fa-shield-halved"></i>
                  </span>
                  <span className="item-label">Privacy & Security</span>
                </button>

                <button
                  type="button"
                  className="dropdown-menu-item"
                  onClick={() => {
                    setMenuOpen(false);
                    onOpenSettingsSection?.("chats");
                  }}
                >
                  <span className="item-icon-tile icon-indigo">
                    <i className="fa-solid fa-message"></i>
                  </span>
                  <span className="item-label">Chats & Media</span>
                </button>

                <button
                  type="button"
                  className="dropdown-menu-item"
                  onClick={() => {
                    setMenuOpen(false);
                    onOpenSettingsSection?.("storage");
                  }}
                >
                  <span className="item-icon-tile icon-sky">
                    <i className="fa-solid fa-database"></i>
                  </span>
                  <span className="item-label">Storage & Data</span>
                </button>

                <button
                  type="button"
                  className="dropdown-menu-item"
                  onClick={() => {
                    setMenuOpen(false);
                    onOpenSettingsSection?.("help");
                  }}
                >
                  <span className="item-icon-tile icon-slate">
                    <i className="fa-solid fa-circle-info"></i>
                  </span>
                  <span className="item-label">Help & About</span>
                </button>
              </div>
            )}
          </div>
        </div>
      </header>


      {/* Search Row directly following (Chats, People, Groups) - stories reel removed */}
      {/* Search Row matching Reference Image */}
      <div className="search-row">
        <label className="search-box">
          <i className="fa-solid fa-magnifying-glass search-icon"></i>

          <input
            type="search"
            placeholder="Search conversations..."
            autoComplete="off"
            value={search}
            onChange={(e) => setSearch(e.target.value)}
          />
        </label>
      </div>

      {/* Filter Pills Row with Icons (Matching Reference Image) */}
      <div className="filter-pills-row">
        {[
          { id: "all", label: "All", icon: "fa-solid fa-table-cells-large" },
          { id: "unread", label: "Unread", icon: "fa-regular fa-comment-dots" },
          { id: "favourites", label: "Favourites", icon: "fa-solid fa-star" },
          { id: "groups", label: "Groups", icon: "fa-solid fa-users" },
        ].map((tab) => (
          <button
            key={tab.id}
            className={`filter-pill ${filterTab === tab.id ? "active" : ""}`}
            onClick={() => setFilterTab(tab.id)}
          >
            <i className={tab.icon}></i>
            <span>{tab.label}</span>
          </button>
        ))}
      </div>

      {/* Section Title with Dynamic Counter */}
      <div className="chat-section-title">
        <span className="section-title-text">Recent Conversations</span>
        <span className="section-count-badge">
          {filteredChats.length} conversations
        </span>
      </div>

      {/* Chat List / Grid View */}
      <div className={`chat-list ${viewMode === "grid" ? "grid-view" : "list-view"}`}>
        {filteredChats.map((chat) => (
          <ChatCard
            key={chat.id}
            chat={chat}
            selected={selectedChat?.id === chat.id}
            onClick={() => handleSelectChat(chat)}
            viewMode={viewMode}
          />
        ))}

        {filteredChats.length === 0 && (
          <div className="no-chats">
            No chats found
          </div>
        )}
      </div>

      {/* WhatsApp-Style Circular Floating Action Button (+) at Bottom Right */}
      <button
        type="button"
        className="chat-fab-new-btn"
        onClick={handleNewChat}
        title="Start New Chat"
        aria-label="Start New Chat"
      >
        <i className="fa-solid fa-plus"></i>
      </button>

      {newChatPickerOpen && (
        <NewChatPicker
          onSearchContacts={onSearchContacts}
          onStartChat={onStartChat}
          onClose={() => setNewChatPickerOpen(false)}
        />
      )}

      {newContactModalOpen && createPortal(
        <div
          className="new-contact-modal-backdrop"
          onMouseDown={(e) => {
            if (e.target === e.currentTarget) {
              closeNewContactModal();
            }
          }}
        >
          <div
            className="new-contact-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="new-contact-title"
          >
            <div className="new-contact-modal-header">
              <div className="new-contact-title-group">
                <div className="new-contact-icon">
                  <i className="fa-solid fa-user-plus"></i>
                </div>

                <div>
                  <h2 id="new-contact-title">Create New Contact</h2>
                  <p>Add a contact to start a new conversation</p>
                </div>
              </div>

              <button
                type="button"
                className="new-contact-close"
                onClick={closeNewContactModal}
                aria-label="Close"
                title="Close"
              >
                <i className="fa-solid fa-xmark"></i>
              </button>
            </div>

            <div className="new-contact-form">
              <div className="new-contact-field">
                <label htmlFor="new-contact-name">Contact name</label>

                <div
                  className={`new-contact-input-wrap ${
                    newContactError ? "has-error" : ""
                  }`}
                >
                  <i className="fa-solid fa-user"></i>

                  <input
                    id="new-contact-name"
                    type="text"
                    value={newContactName}
                    onChange={(e) => {
                      setNewContactName(e.target.value);
                      if (newContactError) {
                        setNewContactError("");
                      }
                    }}
                    placeholder="Enter contact name"
                    autoFocus
                    autoComplete="off"
                    maxLength={100}
                  />
                </div>

                {newContactError && (
                  <span className="new-contact-error">
                    <i className="fa-solid fa-circle-exclamation"></i>
                    {newContactError}
                  </span>
                )}
              </div>

              <div className="new-contact-field">
                <label htmlFor="new-contact-phone">
                  Phone number
                  <span className="optional-label">Optional</span>
                </label>

                <div className="new-contact-input-wrap">
                  <i className="fa-solid fa-phone"></i>

                  <input
                    id="new-contact-phone"
                    type="tel"
                    value={newContactPhone}
                    onChange={(e) => setNewContactPhone(e.target.value)}
                    placeholder="Enter phone number"
                    autoComplete="tel"
                    inputMode="tel"
                    maxLength={30}
                  />
                </div>
              </div>
            </div>

            <div className="new-contact-modal-footer">
              <button
                type="button"
                className="new-contact-cancel-btn"
                onClick={closeNewContactModal}
              >
                Cancel
              </button>

              <button
                type="button"
                className="new-contact-create-btn"
                onClick={handleCreateContact}
              >
                <i className="fa-solid fa-user-plus"></i>
                Create Contact
              </button>
            </div>
          </div>
        </div>,
        document.body
      )}

    </section>
  );
}

export default ChatList;