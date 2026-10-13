import { getAvatarUrl } from "../../utils/avatar";
import "./MobileNav.css";

const mobileTabs = [
  {
    id: "chats",
    label: "Chats",
    icon: "fa-solid fa-comment-dots",
    color: "#f59e0b",
    bg: "rgba(245, 158, 11, 0.14)",
    darkColor: "#f59e0b",
    darkBg: "rgba(245, 158, 11, 0.22)",
  },
  {
    id: "moments",
    label: "Moments",
    icon: "fa-solid fa-compass",
    color: "#f43f5e",
    bg: "rgba(244, 63, 94, 0.12)",
    darkColor: "#fb7185",
    darkBg: "rgba(244, 63, 94, 0.24)",
  },
  {
    id: "livestreaming",
    label: "Live",
    icon: "fa-solid fa-video",
    color: "#ef4444",
    bg: "rgba(239, 68, 68, 0.12)",
    darkColor: "#f87171",
    darkBg: "rgba(239, 68, 68, 0.24)",
    isLive: true,
  },
  {
    id: "channels",
    label: "Channels",
    icon: "fa-solid fa-bullhorn",
    color: "#f97316",
    bg: "rgba(249, 115, 22, 0.12)",
    darkColor: "#fb923c",
    darkBg: "rgba(249, 115, 22, 0.24)",
  },
  {
    id: "calls",
    label: "Calls",
    icon: "fa-solid fa-phone",
    color: "#10b981",
    bg: "rgba(16, 185, 129, 0.12)",
    darkColor: "#34d399",
    darkBg: "rgba(16, 185, 129, 0.24)",
  },
];

function MobileNav({ currentUser, activeTab = "chats", onSelectTab, onToast }) {
  const handleTabClick = (tabId) => {
    if (onSelectTab) {
      onSelectTab(tabId);
    }
    if (
      tabId !== "chats" &&
      tabId !== "calls" &&
      tabId !== "moments" &&
      tabId !== "livestreaming" &&
      tabId !== "channels" &&
      onToast
    ) {
      const formatted = tabId.charAt(0).toUpperCase() + tabId.slice(1);
      onToast(`${formatted} tab selected`);
    }
  };

  return (
    <nav className="mobile-bottom-nav">
      {mobileTabs.map((tab) => {
        const isActive = activeTab === tab.id;

        return (
          <button
            key={tab.id}
            className={`mobile-tab-btn tab-${tab.id} ${isActive ? "active" : ""}`}
            onClick={() => handleTabClick(tab.id)}
            aria-label={tab.label}
            style={{
              "--tab-color": tab.color,
              "--tab-bg": tab.bg,
              "--tab-dark-color": tab.darkColor,
              "--tab-dark-bg": tab.darkBg,
            }}
          >
            <div className="tab-icon-wrap">
              {tab.id === "profile" || tab.isProfile ? (
                <div className="tab-avatar-wrap">
                  <img
                    src={getAvatarUrl(currentUser?.avatar, 100, currentUser?.name)}
                    alt="You"
                  />
                </div>
              ) : (
                <>
                  <i className={tab.icon}></i>
                  {tab.isLive && <span className="tab-live-pulse" title="Live streaming"></span>}
                </>
              )}

              {tab.badge && !isActive && (
                <span className="tab-badge">{tab.badge}</span>
              )}
            </div>

            <span className="tab-label">{tab.label}</span>
          </button>
        );
      })}
    </nav>
  );
}

export default MobileNav;
