import { useState } from "react";
import { getAvatarUrl } from "../../utils/avatar";
import "./Sidebar.css";

const navItems = [
  {
    id: "chat",
    label: "Chats",
    icon: "fa-comment-dots",
    color: "#00a884",
    bg: "#e6f7f4",
    darkColor: "#2dd4bf",
    darkBg: "rgba(0, 168, 132, 0.24)",
  },
  {
    id: "moments",
    label: "Moments",
    icon: "fa-compass",
    color: "#f43f5e",
    bg: "#ffe4e6",
    darkColor: "#fb7185",
    darkBg: "rgba(244, 63, 94, 0.24)",
  },
  {
    id: "livestreaming",
    label: "Live Streaming",
    icon: "fa-video",
    color: "#ef4444",
    bg: "#fee2e2",
    darkColor: "#f87171",
    darkBg: "rgba(239, 68, 68, 0.24)",
    isLive: true,
  },
  {
    id: "channels",
    label: "Channels",
    icon: "fa-bullhorn",
    color: "#f97316",
    bg: "#ffedd5",
    darkColor: "#fb923c",
    darkBg: "rgba(249, 115, 22, 0.24)",
  },
  {
    id: "calls",
    label: "Calls",
    icon: "fa-phone",
    color: "#10b981",
    bg: "#dcfce7",
    darkColor: "#34d399",
    darkBg: "rgba(16, 185, 129, 0.24)",
  },
  {
    id: "calendar",
    label: "Calendar",
    icon: "fa-calendar-days",
    color: "#d97706",
    bg: "#fef3c7",
    darkColor: "#fbbf24",
    darkBg: "rgba(217, 119, 6, 0.24)",
  },
];

// Shown in the sidebar footer, below the profile
const settingsItem = {
  id: "settings",
  label: "Settings",
  icon: "fa-gear",
  color: "#64748b",
  bg: "#f1f5f9",
  darkColor: "#94a3b8",
  darkBg: "rgba(100, 116, 139, 0.24)",
};

function Sidebar({
  currentUser,
  onToast,
  onOpenProfile,
  activeView: propActiveView,
  onSelectView,
}) {
  const [internalActiveView, setInternalActiveView] = useState("chat");
  const activeView = propActiveView !== undefined ? propActiveView : internalActiveView;

  const handleNavClick = (view) => {
    setInternalActiveView(view);
    if (onSelectView) {
      onSelectView(view);
    }

    if (
      view !== "chat" &&
      view !== "moments" &&
      view !== "livestreaming" &&
      view !== "settings" &&
      view !== "channels" &&
      view !== "calendar" &&
      view !== "calls"
    ) {
      const item = navItems.find((n) => n.id === view);
      const label = item ? item.label : view;
      onToast?.(`${label} section selected`);
    }
  };

  const renderNavItem = (item) => (
    <button
      key={item.id}
      className={`nav-item ${activeView === item.id ? "active" : ""}`}
      onClick={() => handleNavClick(item.id)}
      data-tooltip={item.label}
      title={item.label}
    >
      <span
        className="nav-icon-tile"
        style={{
          "--item-color": item.color,
          "--item-bg": item.bg,
          "--item-dark-color": item.darkColor,
          "--item-dark-bg": item.darkBg,
        }}
      >
        <i className={`fa-solid ${item.icon}`}></i>
        {item.isLive && (
          <span className="nav-live-dot" title="Live streaming"></span>
        )}
      </span>

      <span className="nav-item-label">{item.label}</span>
    </button>
  );

  return (
    <aside className="sidebar sidebar-collapsed">

      {/* Main Navigation starting at top */}
      <nav className="main-nav">
        {navItems.map(renderNavItem)}
      </nav>

      {/* Sidebar Footer: Profile placed directly above Settings */}
      <div className="sidebar-footer">
        <div
          className={`profile-block ${activeView === "profile" ? "active" : ""}`}
          onClick={onOpenProfile}
          data-tooltip={currentUser?.name || "Profile"}
          title={currentUser?.name || "Profile"}
        >
          <div className="profile-avatar">
            <img
              src={getAvatarUrl(currentUser?.avatar, 120, currentUser?.name)}
              alt={currentUser?.name || "Profile"}
            />
          </div>

          <div className="profile-name">
            <span>{currentUser?.name || "Henry Jabbawockiez"}</span>
            <i className="fa-solid fa-chevron-down chevron"></i>
          </div>
        </div>

        {renderNavItem(settingsItem)}
      </div>
    </aside>
  );
}

export default Sidebar;