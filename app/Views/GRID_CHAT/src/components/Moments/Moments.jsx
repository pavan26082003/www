import { useCallback, useEffect, useRef, useState } from "react";
import StoryViewer from "./StoryViewer";
import CreateStoryModal from "./CreateStoryModal";
import StatusMediaCounters from "./StatusMediaCounters";
import StatusPrivacyPanel from "./StatusPrivacyPanel";
import StatusDownloadPanel from "./StatusDownloadPanel";
import { buildStatusRingBackground, getStatusCounts } from "../../utils/statusRing";
import { getAvatarUrl } from "../../utils/avatar";
import { formatTimeAgo } from "../../utils/time";
import { searchChatContacts } from "../../services/chatService";
import {
  createMediaMoment,
  createTextMoment,
  deleteMoment,
  fetchMomentPrivacy,
  fetchMomentViewers,
  fetchMoments,
  markMomentViewed,
  saveMomentPrivacy,
  toggleMomentLike,
} from "../../services/momentService";
import "./Moments.css";

const DEFAULT_PRIVACY = {
  share: { audience: "contacts", targets: [] },
  download: { audience: "contacts", targets: [] },
};

// "3 status updates • Tap to view" / "Tap to add status update"
const myStatusSubtitle = (userStatus) => {
  const count = userStatus?.stories?.length || 0;
  if (count === 0) return "Tap to add status update";
  return `${count} status update${count > 1 ? "s" : ""} • Tap to view`;
};

function Moments({ isClosing, onBack, onToast, onOpenChatWithStoryReply }) {
  const [moments, setMoments] = useState([]);
  const [loading, setLoading] = useState(true);
  // The active status-viewer session: null, or { list: [moment,...], index } —
  // list is either every viewable contact (avatar click) or a single synthetic
  // contact entry whose `stories` has already been filtered to one type.
  const [viewerData, setViewerData] = useState(null);
  const [showCreateModal, setShowCreateModal] = useState(false);

  const [headerMenuOpen, setHeaderMenuOpen] = useState(false);
  const [statusActionMenuOpen, setStatusActionMenuOpen] = useState(false);
  const [myStatusMenuOpen, setMyStatusMenuOpen] = useState(false);
  const [myStatusDeleteConfirm, setMyStatusDeleteConfirm] = useState(false);
  // Which full-page settings panel is showing in place of the moments list.
  const [activePanel, setActivePanel] = useState(null); // null | "privacy" | "download"

  // Saved on the server (moment_privacy_settings), not in this browser.
  const [privacy, setPrivacy] = useState(DEFAULT_PRIVACY);

  const headerMenuRef = useRef(null);
  const statusActionMenuRef = useRef(null);
  const myStatusMenuRef = useRef(null);
  const mediaInputRef = useRef(null);

  useEffect(() => {
    const handleOutsideClick = (event) => {
      if (headerMenuRef.current && !headerMenuRef.current.contains(event.target)) {
        setHeaderMenuOpen(false);
      }
      if (statusActionMenuRef.current && !statusActionMenuRef.current.contains(event.target)) {
        setStatusActionMenuOpen(false);
      }
      if (myStatusMenuRef.current && !myStatusMenuRef.current.contains(event.target)) {
        setMyStatusMenuOpen(false);
        setMyStatusDeleteConfirm(false);
      }
    };

    document.addEventListener("click", handleOutsideClick);
    return () => document.removeEventListener("click", handleOutsideClick);
  }, []);

  const loadMoments = useCallback(async () => {
    const result = await fetchMoments();
    if (result.success) {
      setMoments(result.data.moments || []);
    } else {
      onToast?.(result.message || "Could not load statuses");
    }
    setLoading(false);
  }, [onToast]);

  useEffect(() => {
    loadMoments();
  }, [loadMoments]);

  useEffect(() => {
    let cancelled = false;
    fetchMomentPrivacy().then((result) => {
      if (!cancelled && result.success) setPrivacy(result.data.privacy);
    });
    return () => {
      cancelled = true;
    };
  }, []);

  // Separate user status vs friends
  const userStatus = moments.find((m) => m.isUser);
  const friendsMoments = moments.filter((m) => !m.isUser);

  // Your own ring comes from how many statuses you actually have: none means no
  // ring at all, one is an unbroken ring, several are split into arcs.
  const myStoryCount = userStatus?.stories?.length || 0;
  const myRingStyle =
    myStoryCount > 1
      ? { background: buildStatusRingBackground(myStoryCount, "var(--color-primary, #00a884)") }
      : undefined;

  // Divide into recent and viewed
  const recentUpdates = friendsMoments.filter((m) => !m.viewed);
  const viewedUpdates = friendsMoments.filter((m) => m.viewed);

  /**
   * Records one status as seen, as it comes up in the viewer — so the ring only
   * turns grey once every status of that contact has actually been watched.
   */
  const handleStoryViewed = useCallback(async (momentItem, story) => {
    if (!story?.realId || story.isMine || story.viewed) return;

    setMoments((prev) =>
      prev.map((item) => {
        if (item.contactId !== momentItem.contactId || item.isUser) return item;
        const stories = item.stories.map((s) =>
          s.realId === story.realId ? { ...s, viewed: true } : s
        );
        return { ...item, stories, viewed: stories.every((s) => s.viewed) };
      })
    );

    await markMomentViewed(story.realId);
  }, []);

  // Avatar/card click — opens every status belonging to this contact, in sequence,
  // and can still move on to the next/previous contact's stories.
  const openStatusViewer = (momentItem) => {
    if (!momentItem) return;

    if (momentItem.isUser && (!momentItem.stories || momentItem.stories.length === 0)) {
      setStatusActionMenuOpen(true);
      return;
    }

    const allViewable = moments.filter((m) => m.stories && m.stories.length > 0);
    const index = allViewable.findIndex((m) => m.id === momentItem.id);

    if (index === -1) {
      onToast?.("No statuses available right now");
      return;
    }

    setViewerData({ list: allViewable, index });
  };

  // Stable identities: the story viewer rebuilds its progress timer whenever
  // these change, and inline arrows would do that on every render of this page.
  const closeViewer = useCallback(() => setViewerData(null), []);

  const goToNextMoment = useCallback(() => {
    setViewerData((prev) =>
      prev ? { ...prev, index: Math.min(prev.index + 1, prev.list.length - 1) } : prev
    );
  }, []);

  const goToPrevMoment = useCallback(() => {
    setViewerData((prev) => (prev ? { ...prev, index: Math.max(prev.index - 1, 0) } : prev));
  }, []);

  // Media-counter click — opens ONLY that contact's statuses of the given type.
  const openFilteredStatusViewer = (momentItem, type) => {
    const filtered = (momentItem.stories || []).filter((status) => status.type === type);

    if (filtered.length === 0) {
      onToast?.(`No ${type} updates from ${momentItem.userName}`);
      return;
    }

    setViewerData({ list: [{ ...momentItem, stories: filtered }], index: 0 });
  };

  const handlePostStory = async (newStory) => {
    if (newStory.type !== "text") {
      onToast?.("That kind of status cannot be posted yet");
      return;
    }

    const result = await createTextMoment({
      text: newStory.text,
      bg: newStory.bg,
      caption: newStory.caption,
    });

    if (result.success) {
      onToast?.("Status posted");
      await loadMoments();
    } else {
      onToast?.(result.message || "Could not post your status");
    }
  };

  // The story itself is passed too, so the chat message can quote which status
  // was replied to.
  const handleSendStoryReply = (momentItem, story, textOrEmoji) => {
    if (!onOpenChatWithStoryReply || !momentItem?.contactId) {
      onToast?.("Replies are not available right now");
      return;
    }
    onOpenChatWithStoryReply(momentItem, story, textOrEmoji);
  };

  const handlePickPhotosVideos = () => {
    setStatusActionMenuOpen(false);
    mediaInputRef.current?.click();
  };

  const handlePickText = () => {
    setStatusActionMenuOpen(false);
    setShowCreateModal(true);
  };

  const handleMediaFileChange = async (event) => {
    const file = event.target.files?.[0];
    event.target.value = "";
    if (!file) return;

    onToast?.("Uploading status...");
    const result = await createMediaMoment({ file, caption: "" });

    if (result.success) {
      onToast?.("Status posted");
      await loadMoments();
    } else {
      onToast?.(result.message || "Could not upload your status");
    }
  };

  /**
   * Likes or unlikes a status. Returns the server's counts so the viewer can
   * settle on them, or null when it failed and the heart should snap back.
   */
  const handleToggleLike = async (story) => {
    const result = await toggleMomentLike(story.realId);
    if (!result.success) {
      onToast?.(result.message || "Could not update your like");
      return null;
    }

    // Keep the list in step for when the viewer closes
    setMoments((prev) =>
      prev.map((item) => ({
        ...item,
        stories: item.stories.map((s) =>
          s.realId === story.realId
            ? { ...s, likedByMe: result.data.liked, likeCount: result.data.likeCount }
            : s
        ),
      }))
    );

    return { liked: result.data.liked, likeCount: result.data.likeCount };
  };

  // Who has seen one of your own statuses (the server refuses anyone else's).
  const handleFetchViewers = async (story) => {
    const result = await fetchMomentViewers(story.realId);
    if (!result.success) {
      onToast?.(result.message || "Could not load viewers");
      return { viewers: [] };
    }
    return result.data;
  };

  // Deletes one status from inside the viewer (own statuses only).
  const handleDeleteStory = async (story) => {
    const result = await deleteMoment(story.realId);
    if (!result.success) {
      onToast?.(result.message || "Could not delete this status");
      return;
    }

    onToast?.("Status deleted");
    setViewerData(null);
    await loadMoments();
  };

  const saveAudience = async (setting, audience, userIds) => {
    const result = await saveMomentPrivacy(setting, audience, userIds);
    if (result.success) {
      setPrivacy(result.data.privacy);
      onToast?.(setting === "share" ? "Status privacy updated" : "Status download setting updated");
    } else {
      onToast?.(result.message || "Could not save the setting");
    }
  };

  const closeMyStatusMenu = () => {
    setMyStatusMenuOpen(false);
    setMyStatusDeleteConfirm(false);
  };

  // Frontend-only placeholder interactions — no backend/social API integration.
  const handleForward = () => {
    closeMyStatusMenu();
    onToast?.("Forward status");
  };

  const handleShare = () => {
    closeMyStatusMenu();
    onToast?.("Share status");
  };

  const handleShareFacebook = () => {
    closeMyStatusMenu();
    onToast?.("Shared to Facebook");
  };

  const handleShareInstagram = () => {
    closeMyStatusMenu();
    onToast?.("Shared to Instagram");
  };

  // The card menu clears everything; single statuses are deleted in the viewer.
  const handleConfirmDeleteMyStatus = async () => {
    const userStories = userStatus?.stories || [];
    const results = await Promise.all(userStories.map((story) => deleteMoment(story.realId)));

    onToast?.(results.every((r) => r.success) ? "Statuses deleted" : "Some statuses could not be deleted");
    await loadMoments();
    closeMyStatusMenu();
  };

  // Full-page swap into a settings panel — same push-navigation pattern used by
  // ContactProfile swapping into AllMediaGallery.
  if (activePanel === "privacy") {
    return (
      <div className={`moments-page ${isClosing ? "closing" : ""}`}>
        <StatusPrivacyPanel
          value={privacy.share}
          onChange={(audience, userIds) => saveAudience("share", audience, userIds)}
          onSearchContacts={searchChatContacts}
          onBack={() => setActivePanel(null)}
          onClose={() => setActivePanel(null)}
        />
      </div>
    );
  }

  if (activePanel === "download") {
    return (
      <div className={`moments-page ${isClosing ? "closing" : ""}`}>
        <StatusDownloadPanel
          value={privacy.download}
          onChange={(audience, userIds) => saveAudience("download", audience, userIds)}
          onSearchContacts={searchChatContacts}
          onBack={() => setActivePanel(null)}
          onClose={() => setActivePanel(null)}
        />
      </div>
    );
  }

  return (
    <div className={`moments-page ${isClosing ? "closing" : ""}`}>
      {/* Top Header */}
      <header className="moments-header">
        <div className="moments-header-left">
          {onBack && (
            <button className="moments-back-btn" onClick={onBack} title="Back to Chats">
              <i className="fa-solid fa-arrow-left"></i>
            </button>
          )}
          <div className="moments-title-wrap">
            <h1 className="moments-title">Moments</h1>
            <span className="moments-subtitle">Stories from your contacts</span>
          </div>
        </div>

        <div className="moments-header-actions">
          <div className="moments-more-wrap" ref={headerMenuRef}>
            <button
              className="moments-action-circle-btn"
              onClick={(event) => {
                event.stopPropagation();
                setHeaderMenuOpen((prev) => !prev);
              }}
              title="More options"
              aria-label="More options"
              aria-haspopup="menu"
              aria-expanded={headerMenuOpen}
            >
              <i className="fa-solid fa-ellipsis-vertical"></i>
            </button>

            {headerMenuOpen && (
              <div className="moments-dropdown-menu" role="menu">
                <button
                  type="button"
                  onClick={() => {
                    setHeaderMenuOpen(false);
                    setActivePanel("privacy");
                  }}
                >
                  <i className="fa-solid fa-lock"></i> Status privacy
                </button>
                <button
                  type="button"
                  onClick={() => {
                    setHeaderMenuOpen(false);
                    setActivePanel("download");
                  }}
                >
                  <i className="fa-solid fa-download"></i> Status download
                </button>
              </div>
            )}
          </div>
        </div>
      </header>

      {/* Main Scroll Content */}
      <div className="moments-scroll-area">
        {/* My Status Card */}
        <div className="my-status-card" onClick={() => openStatusViewer(userStatus)}>
          <div
            className={`my-status-avatar-wrap ${myStoryCount > 0 ? "has-status" : ""}`}
            style={myRingStyle}
            ref={statusActionMenuRef}
          >
            <img
              src={getAvatarUrl(userStatus?.userAvatar, 120, userStatus?.displayName)}
              alt="My Status"
            />
            <button
              type="button"
              className="my-status-add-badge"
              title="Add status update"
              aria-label="Add status update"
              aria-haspopup="menu"
              aria-expanded={statusActionMenuOpen}
              onClick={(event) => {
                event.stopPropagation();
                setStatusActionMenuOpen((prev) => !prev);
              }}
            >
              <i className="fa-solid fa-plus"></i>
            </button>

            {statusActionMenuOpen && (
              <div
                className="status-action-menu"
                role="menu"
                onClick={(event) => event.stopPropagation()}
              >
                <button type="button" onClick={handlePickPhotosVideos}>
                  <i className="fa-regular fa-image"></i> Photos &amp; videos
                </button>
                <button type="button" onClick={handlePickText}>
                  <i className="fa-solid fa-pen"></i> Text
                </button>
              </div>
            )}
          </div>

          <div className="my-status-info">
            <h3 className="my-status-name">My Status</h3>
            <p className="my-status-time">{myStatusSubtitle(userStatus)}</p>
          </div>

          <div className="my-status-actions" ref={myStatusMenuRef}>
            <button
              type="button"
              className="quick-icon-btn"
              title="More options"
              aria-label="More options"
              aria-haspopup="menu"
              aria-expanded={myStatusMenuOpen}
              onClick={(event) => {
                event.stopPropagation();
                setMyStatusMenuOpen((prev) => !prev);
                setMyStatusDeleteConfirm(false);
              }}
            >
              <i className="fa-solid fa-ellipsis-vertical"></i>
            </button>

            {myStatusMenuOpen && (
              <div
                className="my-status-menu"
                role="menu"
                onClick={(event) => event.stopPropagation()}
              >
                {myStatusDeleteConfirm ? (
                  <div className="my-status-menu-confirm">
                    <p>
                      Delete all {userStatus?.stories?.length || 0} of your statuses?
                    </p>
                    <div className="my-status-menu-confirm-actions">
                      <button type="button" onClick={() => setMyStatusDeleteConfirm(false)}>
                        Cancel
                      </button>
                      <button
                        type="button"
                        className="danger"
                        onClick={handleConfirmDeleteMyStatus}
                      >
                        Delete
                      </button>
                    </div>
                  </div>
                ) : (
                  <>
                    <button type="button" onClick={handleForward}>
                      <i className="fa-solid fa-share"></i> Forward
                    </button>
                    <button type="button" onClick={handleShare}>
                      <i className="fa-solid fa-share-nodes"></i> Share...
                    </button>
                    <button type="button" onClick={handleShareFacebook}>
                      <i className="fa-brands fa-facebook"></i> Share to Facebook
                    </button>
                    <button type="button" onClick={handleShareInstagram}>
                      <i className="fa-brands fa-instagram"></i> Share to Instagram
                    </button>
                    <button
                      type="button"
                      className="danger"
                      disabled={!userStatus?.stories?.length}
                      onClick={() => setMyStatusDeleteConfirm(true)}
                    >
                      <i className="fa-solid fa-trash-can"></i> Delete all
                    </button>
                  </>
                )}
              </div>
            )}
          </div>
        </div>

        <input
          type="file"
          ref={mediaInputRef}
          accept="image/*,video/*"
          onChange={handleMediaFileChange}
          style={{ display: "none" }}
        />

        {/* Stories Horizontal Reel */}
        {friendsMoments.length > 0 && (
          <div className="moments-reel-section">
            <div className="section-label">Quick Preview</div>
            <div className="moments-reel-scroll">
              {friendsMoments.map((item) => {
                const count = item.stories?.length || 0;
                const ringColor = item.viewed
                  ? "var(--status-ring-viewed)"
                  : "var(--color-primary, #f59e0b)";
                // A single status keeps the app's existing rich ring gradient (via CSS
                // class); 2+ statuses get a computed segmented ring, one arc per status.
                const ringStyle =
                  count > 1 ? { background: buildStatusRingBackground(count, ringColor) } : undefined;

                return (
                  <div
                    key={item.id}
                    className="reel-item"
                    onClick={() => openStatusViewer(item)}
                  >
                    <div
                      className={`reel-avatar-wrap ${item.viewed ? "viewed" : "active-ring"}`}
                      style={ringStyle}
                    >
                      <img src={getAvatarUrl(item.userAvatar, 120, item.userName)} alt={item.userName} />
                    </div>
                    <span className="reel-name">{item.userName.split(" ")[0]}</span>
                  </div>
                );
              })}
            </div>
          </div>
        )}

        {/* Recent Updates Section */}
        {recentUpdates.length > 0 && (
          <div className="moments-section">
            <h2 className="moments-section-heading">Recent Updates</h2>
            <div className="moments-list">
              {recentUpdates.map((item) => {
                const count = item.stories?.length || 0;
                const counts = getStatusCounts(item.stories);
                const ringStyle =
                  count > 1
                    ? { background: buildStatusRingBackground(count, "var(--color-primary, #f59e0b)") }
                    : undefined;

                return (
                  <div
                    key={item.id}
                    className="moment-card"
                    onClick={() => openStatusViewer(item)}
                  >
                    <div className="moment-card-avatar-wrap active-ring" style={ringStyle}>
                      <img src={getAvatarUrl(item.userAvatar, 120, item.userName)} alt={item.userName} />
                    </div>

                    <div className="moment-card-details">
                      <h4 className="moment-card-name">{item.userName}</h4>
                      <span className="moment-card-time">{formatTimeAgo(item.updatedAt)}</span>
                    </div>

                    <StatusMediaCounters
                      video={counts.video}
                      image={counts.image}
                      text={counts.text}
                      onSelectType={(type) => openFilteredStatusViewer(item, type)}
                    />
                  </div>
                );
              })}
            </div>
          </div>
        )}

        {/* Viewed Updates Section */}
        {viewedUpdates.length > 0 && (
          <div className="moments-section">
            <h2 className="moments-section-heading">Viewed Updates</h2>
            <div className="moments-list">
              {viewedUpdates.map((item) => {
                const count = item.stories?.length || 0;
                const counts = getStatusCounts(item.stories);
                const ringStyle =
                  count > 1
                    ? { background: buildStatusRingBackground(count, "var(--status-ring-viewed)") }
                    : undefined;

                return (
                  <div
                    key={item.id}
                    className="moment-card viewed"
                    onClick={() => openStatusViewer(item)}
                  >
                    <div className="moment-card-avatar-wrap viewed-ring" style={ringStyle}>
                      <img src={getAvatarUrl(item.userAvatar, 120, item.userName)} alt={item.userName} />
                    </div>

                    <div className="moment-card-details">
                      <h4 className="moment-card-name">{item.userName}</h4>
                      <span className="moment-card-time">{formatTimeAgo(item.updatedAt)}</span>
                    </div>

                    <StatusMediaCounters
                      video={counts.video}
                      image={counts.image}
                      text={counts.text}
                      onSelectType={(type) => openFilteredStatusViewer(item, type)}
                    />

                    <div className="moment-card-meta">
                      <i className="fa-solid fa-check-double viewed-icon"></i>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        )}

        {/* Nobody has posted yet */}
        {!loading && friendsMoments.length === 0 && (
          <div className="moments-empty">
            <i className="fa-regular fa-circle-play"></i>
            <h3>No status updates yet</h3>
            <p>When your contacts post a status, it will show up here for 24 hours.</p>
          </div>
        )}

        {/* Privacy Note */}
        <div className="moments-privacy-banner">
          <i className="fa-solid fa-lock"></i>
          <span>Status updates are private to the people you choose and disappear after 24 hours.</span>
        </div>
      </div>

      {/* Full-screen Story Viewer */}
      {viewerData && (
        <StoryViewer
          momentsList={viewerData.list}
          currentMomentIndex={viewerData.index}
          onClose={closeViewer}
          onNextMoment={goToNextMoment}
          onPrevMoment={goToPrevMoment}
          onStoryViewed={handleStoryViewed}
          onDeleteStory={handleDeleteStory}
          onToggleLike={handleToggleLike}
          onFetchViewers={handleFetchViewers}
          onSendReply={handleSendStoryReply}
          onToast={onToast}
        />
      )}

      {/* Create Story Modal (Text status) */}
      {showCreateModal && (
        <CreateStoryModal
          privacyAudience={privacy.share.audience}
          privacyCount={privacy.share.targets.length}
          onClose={() => setShowCreateModal(false)}
          onPostStory={handlePostStory}
          onToast={onToast}
        />
      )}
    </div>
  );
}

export default Moments;
