import { useState } from "react";
import { initialChannels, channelCategories } from "../../data/channelsData";
import ChannelPostCard from "./ChannelPostCard";
import CreateChannelModal from "./CreateChannelModal";
import "./Channels.css";

const CHANNEL_CACHE_KEYS = ["mychat_channels_data", "cached_channels"];

function readChannelCache() {
  for (const key of CHANNEL_CACHE_KEYS) {
    try {
      const saved = localStorage.getItem(key);
      if (!saved) continue;
      const parsed = JSON.parse(saved);
      if (!Array.isArray(parsed)) continue;
      const channels = parsed.filter(
        (channel) => channel && typeof channel === "object" && channel.id
      );
      if (channels.length) return channels;
    } catch {
      // This cache entry is unreadable. Try the other key.
    }
  }
  return null;
}

function writeChannelCache(channels) {
  const payload = JSON.stringify(channels);
  for (const key of CHANNEL_CACHE_KEYS) {
    try {
      localStorage.setItem(key, payload);
    } catch {
      // Storage unavailable or quota exceeded.
    }
  }
}

function loadChannelDirectory() {
  const offline = typeof navigator !== "undefined" && navigator.onLine === false;
  try {
    const cached = readChannelCache();
    if (offline) return cached || initialChannels;
    const directory = cached || initialChannels;
    writeChannelCache(directory);
    return directory;
  } catch {
    return readChannelCache() || initialChannels;
  }
}

function Channels({ isClosing, onBack, onToast }) {
  const [channels, setChannels] = useState(loadChannelDirectory);

  const [activeChannelId, setActiveChannelId] = useState(
    channels[0]?.id || "channel-1"
  );
  const [activeDirectoryTab, setActiveDirectoryTab] = useState("all"); // 'all' | 'following' | 'mine'
  const [selectedCategory, setSelectedCategory] = useState("all");
  const [searchQuery, setSearchQuery] = useState("");
  const [showCreateModal, setShowCreateModal] = useState(false);
  const [mobilePane, setMobilePane] = useState("list"); // 'list' | 'feed'
  const [showInfoDrawer, setShowInfoDrawer] = useState(false);

  // Active Channel
  const activeChannel = (Array.isArray(channels) ? channels : []).find((c) => c && c.id === activeChannelId) || (Array.isArray(channels) ? channels[0] : undefined);

  // Helper to persist channels state
  const updateChannelsState = (newChannels) => {
    setChannels(newChannels);
    writeChannelCache(newChannels);
  };

  const channelText = (value) => String(value || "").toLowerCase();

  // Filter the saved directory locally. Tab clicks never wait on the network.
  const filteredChannels = (Array.isArray(channels) ? channels : []).filter((channel) => {
    if (!channel || typeof channel !== "object") return false;
    if (activeDirectoryTab === "following" && !channel.isFollowing) {
      return false;
    }
    if (activeDirectoryTab === "mine" && !channel.isOwner) {
      return false;
    }
    if (selectedCategory !== "all" && channel.category !== selectedCategory) {
      return false;
    }
    if (searchQuery.trim()) {
      const q = searchQuery.toLowerCase().trim();
      return (
        channelText(channel.name).includes(q) ||
        channelText(channel.handle).includes(q) ||
        channelText(channel.bio).includes(q)
      );
    }
    return true;
  });

  // Follow / Unfollow Handler
  const handleToggleFollow = (channelId, e) => {
    e?.stopPropagation();
    const target = channels.find((c) => c.id === channelId);
    if (!target) return;

    const nextFollowState = !target.isFollowing;
    const nextFollowerCount = nextFollowState ? target.followers + 1 : Math.max(0, target.followers - 1);
    const formattedCount =
      nextFollowerCount >= 1000000
        ? `${(nextFollowerCount / 1000000).toFixed(1)}M`
        : nextFollowerCount >= 1000
        ? `${Math.round(nextFollowerCount / 1000)}K`
        : `${nextFollowerCount}`;

    const updated = channels.map((c) =>
      c.id === channelId
        ? {
            ...c,
            isFollowing: nextFollowState,
            followers: nextFollowerCount,
            followersFormatted: formattedCount,
          }
        : c
    );

    updateChannelsState(updated);
    onToast?.(
      nextFollowState
        ? `Followed "${target.name}" 📢`
        : `Unfollowed "${target.name}"`
    );
  };

  // Mute / Unmute Handler
  const handleToggleMute = (channelId, e) => {
    e?.stopPropagation();
    const updated = channels.map((c) =>
      c.id === channelId ? { ...c, isMuted: !c.isMuted } : c
    );
    updateChannelsState(updated);

    const target = channels.find((c) => c.id === channelId);
    onToast?.(
      target?.isMuted
        ? `Notifications unmuted for ${target.name} 🔔`
        : `Notifications muted for ${target.name} 🔕`
    );
  };

  // Emoji Reaction Handler
  const handleReaction = (channelId, postId, emoji) => {
    const updated = channels.map((channel) => {
      if (channel.id !== channelId) return channel;

      const updatedPosts = channel.posts.map((post) => {
        if (post.id !== postId) return post;

        const currentReactions = { ...(post.reactions || {}) };
        const prevUserReaction = post.userReaction;

        if (prevUserReaction === emoji) {
          // Deselect reaction
          currentReactions[emoji] = Math.max(0, (currentReactions[emoji] || 1) - 1);
          if (currentReactions[emoji] === 0) delete currentReactions[emoji];
          return {
            ...post,
            reactions: currentReactions,
            userReaction: null,
          };
        } else {
          // If already reacted with another emoji, decrement it
          if (prevUserReaction && currentReactions[prevUserReaction]) {
            currentReactions[prevUserReaction] = Math.max(0, currentReactions[prevUserReaction] - 1);
            if (currentReactions[prevUserReaction] === 0) delete currentReactions[prevUserReaction];
          }
          // Increment new emoji
          currentReactions[emoji] = (currentReactions[emoji] || 0) + 1;
          return {
            ...post,
            reactions: currentReactions,
            userReaction: emoji,
          };
        }
      });

      return { ...channel, posts: updatedPosts };
    });

    updateChannelsState(updated);
  };

  // Create Channel Callback
  const handleCreateChannel = (newChannel) => {
    const updated = [newChannel, ...channels];
    updateChannelsState(updated);
    setActiveChannelId(newChannel.id);
    setMobilePane("feed");
  };

  // Select Channel Click
  const handleSelectChannel = (channel) => {
    setActiveChannelId(channel.id);
    setMobilePane("feed");
  };

  // Copy Channel Link
  const handleShareChannel = () => {
    navigator.clipboard?.writeText?.(window.location.href);
    onToast?.(`Channel link @${activeChannel.handle} copied to clipboard! 🔗`);
  };

  const channelList = Array.isArray(channels) ? channels : [];
  const followingCount = channelList.filter((c) => c.isFollowing).length;
  const mineCount = channelList.filter((c) => c.isOwner).length;

  return (
    <div className={`channels-workstation-page ${isClosing ? "closing" : ""}`}>
      {/* Top Header Bar */}
      <header className="channels-master-header">
        <div className="channels-header-left">
          <button
            className="channels-nav-back-btn"
            onClick={onBack}
            title="Back to Chats"
            aria-label="Back to Chats"
          >
            <i className="fa-solid fa-arrow-left"></i>
          </button>

          <div className="channels-brand-block">
            <div className="channels-icon-pill">
              <i className="fa-solid fa-bullhorn"></i>
            </div>
            <div className="channels-title-col">
              <div className="channels-h1-row">
                <h1 className="channels-h1">Channels</h1>
                <span className="channels-badge-counter">
                  {channels.length} Directory
                </span>
              </div>
              <p className="channels-subtext">
                Stay updated on topics you care about from verified creators
              </p>
            </div>
          </div>
        </div>

        {/* Global Search Bar */}
        <div className="channels-search-container">
          <div className="channels-search-bar">
            <i className="fa-solid fa-magnifying-glass search-icon"></i>
            <input
              type="text"
              placeholder="Search channels, topics, handles..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
            />
            {searchQuery && (
              <button
                className="search-clear-btn"
                onClick={() => setSearchQuery("")}
                aria-label="Clear search"
              >
                <i className="fa-solid fa-xmark"></i>
              </button>
            )}
          </div>
        </div>

        {/* Right Header Actions */}
        <div className="channels-header-right">
          <button
            className="btn-create-channel-trigger"
            onClick={() => setShowCreateModal(true)}
          >
            <i className="fa-solid fa-plus"></i>
            <span>Create Channel</span>
          </button>
        </div>
      </header>

      {/* Main Channels Layout (2-Pane on Desktop, Single-Pane toggle on Mobile) */}
      <main className="channels-body-container">
        {/* Left Pane: Directory & Following Channels */}
        <section
          className={`channels-directory-pane ${
            mobilePane !== "list" ? "mobile-pane-hidden" : ""
          }`}
        >
          {/* Segmented Tab Controls */}
          <div className="directory-tabs-row">
            <button
              className={`directory-tab-btn ${
                activeDirectoryTab === "all" ? "is-tab-active" : ""
              }`}
              onClick={() => setActiveDirectoryTab("all")}
            >
              <i className="fa-solid fa-compass"></i>
              <span>Explore</span>
              <span className="tab-counter-pill">{channelList.length}</span>
            </button>
            <button
              className={`directory-tab-btn ${
                activeDirectoryTab === "following" ? "is-tab-active" : ""
              }`}
              onClick={() => setActiveDirectoryTab("following")}
            >
              <i className="fa-solid fa-circle-check"></i>
              <span>Following</span>
              <span className="tab-counter-pill">{followingCount}</span>
            </button>
            <button
              className={`directory-tab-btn ${
                activeDirectoryTab === "mine" ? "is-tab-active" : ""
              }`}
              onClick={() => setActiveDirectoryTab("mine")}
            >
              <i className="fa-solid fa-user"></i>
              <span>My channels</span>
              <span className="tab-counter-pill">{mineCount}</span>
            </button>
          </div>

          {/* Horizontal Category Filter Pills */}
          <div className="channels-categories-strip">
            {channelCategories.map((cat) => {
              const isSelected = selectedCategory === cat.id;

              return (
                <button
                  key={cat.id}
                  className={`channel-category-pill ${isSelected ? "is-category-active" : ""}`}
                  onClick={() => setSelectedCategory(cat.id)}
                >
                  <i className={cat.icon}></i>
                  <span>{cat.label}</span>
                </button>
              );
            })}
          </div>

          {/* Channels Cards List */}
          <div className="channels-list-scroller">
            {filteredChannels.length === 0 ? (
              <div className="empty-channels-state">
                <div className="empty-state-icon">
                  <i className="fa-solid fa-bullhorn"></i>
                </div>
                <h3>No channels found</h3>
                <p>
                  {activeDirectoryTab === "following"
                    ? "You haven't followed any channels yet. Explore the directory to find interesting topics!"
                    : activeDirectoryTab === "mine"
                    ? "Channels you create are kept on this device and stay available here offline."
                    : "Try adjusting your search keywords or category filters."}
                </p>
                {activeDirectoryTab === "following" && (
                  <button
                    className="btn-discover-channels"
                    onClick={() => setActiveDirectoryTab("all")}
                  >
                    Explore Directory
                  </button>
                )}
              </div>
            ) : (
              <div className="channels-cards-column">
                {filteredChannels.map((item) => {
                  const isSelected = item.id === activeChannelId;

                  return (
                    <article
                      key={item.id}
                      className={`channel-directory-card ${
                        isSelected ? "is-selected-channel" : ""
                      }`}
                      onClick={() => handleSelectChannel(item)}
                    >
                      <div className="card-avatar-box">
                        <img
                          src={item.avatar}
                          alt={item.name}
                          className="channel-card-avatar"
                        />
                        {item.verified && (
                          <span
                            className="channel-card-verified-dot"
                            title="Verified Channel"
                          >
                            <i className="fa-solid fa-check"></i>
                          </span>
                        )}
                      </div>

                      <div className="card-info-box">
                        <div className="card-headline-line">
                          <h4 className="card-channel-name">{item.name}</h4>
                          <span className="card-followers-badge">
                            {item.followersFormatted}
                          </span>
                        </div>

                        <span className="card-channel-handle">@{item.handle}</span>
                        <p className="card-channel-snippet">{item.bio}</p>

                        <div className="card-actions-row">
                          <button
                            className={`btn-card-follow ${
                              item.isFollowing ? "is-following-btn" : ""
                            }`}
                            onClick={(e) => handleToggleFollow(item.id, e)}
                          >
                            <i
                              className={`fa-solid ${
                                item.isFollowing ? "fa-check" : "fa-plus"
                              }`}
                            ></i>
                            <span>{item.isFollowing ? "Following" : "Follow"}</span>
                          </button>

                          {item.isFollowing && (
                            <button
                              className={`btn-card-mute ${
                                item.isMuted ? "is-muted" : ""
                              }`}
                              onClick={(e) => handleToggleMute(item.id, e)}
                              title={item.isMuted ? "Unmute notifications" : "Mute notifications"}
                              aria-label="Mute toggle"
                            >
                              <i
                                className={`fa-solid ${
                                  item.isMuted ? "fa-bell-slash" : "fa-bell"
                                }`}
                              ></i>
                            </button>
                          )}
                        </div>
                      </div>
                    </article>
                  );
                })}
              </div>
            )}
          </div>
        </section>

        {/* Right Pane: Channel Broadcast Feed View */}
        {activeChannel && (
          <section
            className={`channels-feed-pane ${
              mobilePane !== "feed" ? "mobile-pane-hidden" : ""
            }`}
          >
            {/* Feed Top Header & Banner */}
            <header className="channel-feed-banner-header">
              <div
                className="channel-cover-image"
                style={{ backgroundImage: `url(${activeChannel.banner})` }}
              >
                <div className="channel-cover-gradient"></div>

                {/* Mobile Back Button inside Feed Header */}
                <button
                  className="btn-mobile-feed-back"
                  onClick={() => setMobilePane("list")}
                  aria-label="Back to Channels List"
                >
                  <i className="fa-solid fa-arrow-left"></i>
                  <span>Channels</span>
                </button>
              </div>

              <div className="channel-hero-meta-bar">
                <div className="hero-avatar-wrap">
                  <img
                    src={activeChannel.avatar}
                    alt={activeChannel.name}
                    className="hero-channel-avatar"
                  />
                  {activeChannel.verified && (
                    <span className="hero-verified-badge" title="Verified Channel">
                      <i className="fa-solid fa-check"></i>
                    </span>
                  )}
                </div>

                <div className="hero-channel-details">
                  <div className="hero-title-row">
                    <h2 className="hero-channel-name">{activeChannel.name}</h2>
                    <span className="hero-category-tag">
                      {activeChannel.categoryLabel}
                    </span>
                  </div>

                  <div className="hero-stats-row">
                    <span className="hero-handle">@{activeChannel.handle}</span>
                    <span className="hero-dot-separator">•</span>
                    <span className="hero-followers">
                      <i className="fa-solid fa-users"></i> {activeChannel.followersFormatted} followers
                    </span>
                    {activeChannel.website && (
                      <>
                        <span className="hero-dot-separator">•</span>
                        <a
                          href={activeChannel.website}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="hero-website-link"
                        >
                          <i className="fa-solid fa-link"></i> Website
                        </a>
                      </>
                    )}
                  </div>

                  <p className="hero-bio-text">{activeChannel.bio}</p>
                </div>

                {/* Feed Action Controls */}
                <div className="hero-action-controls">
                  <button
                    className={`btn-hero-follow ${
                      activeChannel.isFollowing ? "is-following" : ""
                    }`}
                    onClick={(e) => handleToggleFollow(activeChannel.id, e)}
                  >
                    <i
                      className={`fa-solid ${
                        activeChannel.isFollowing ? "fa-check" : "fa-plus"
                      }`}
                    ></i>
                    <span>{activeChannel.isFollowing ? "Following" : "Follow"}</span>
                  </button>

                  {activeChannel.isFollowing && (
                    <button
                      className={`btn-hero-icon-action ${
                        activeChannel.isMuted ? "is-muted" : ""
                      }`}
                      onClick={(e) => handleToggleMute(activeChannel.id, e)}
                      title={activeChannel.isMuted ? "Unmute Notifications" : "Mute Notifications"}
                      aria-label="Mute notifications"
                    >
                      <i
                        className={`fa-solid ${
                          activeChannel.isMuted ? "fa-bell-slash" : "fa-bell"
                        }`}
                      ></i>
                    </button>
                  )}

                  <button
                    className="btn-hero-icon-action"
                    onClick={handleShareChannel}
                    title="Share Channel"
                    aria-label="Share Channel"
                  >
                    <i className="fa-solid fa-share-nodes"></i>
                  </button>

                  <button
                    className={`btn-hero-icon-action ${showInfoDrawer ? "active-info" : ""}`}
                    onClick={() => setShowInfoDrawer(!showInfoDrawer)}
                    title="Channel Information"
                    aria-label="Channel Information"
                  >
                    <i className="fa-solid fa-circle-info"></i>
                  </button>
                </div>
              </div>
            </header>

            {/* Broadcast Feed Content & Posts Stream */}
            <div className="channel-posts-scroll-container">
              <div className="broadcast-announcement-banner">
                <i className="fa-solid fa-bullhorn"></i>
                <div className="announcement-text">
                  <strong>Broadcast Channel:</strong> Only channel admins can post updates. You can react with emojis to show support!
                </div>
              </div>

              {/* Feed Posts */}
              <div className="channel-posts-feed-stream">
                {activeChannel.posts && activeChannel.posts.length > 0 ? (
                  activeChannel.posts.map((post) => (
                    <ChannelPostCard
                      key={post.id}
                      post={post}
                      channel={activeChannel}
                      onReaction={handleReaction}
                      onToast={onToast}
                    />
                  ))
                ) : (
                  <div className="empty-posts-state">
                    <i className="fa-regular fa-newspaper"></i>
                    <p>No broadcast updates published yet.</p>
                  </div>
                )}
              </div>
            </div>

            {/* Collapsible Channel Info Right Sidebar */}
            {showInfoDrawer && (
              <aside className="channel-info-drawer">
                <div className="info-drawer-header">
                  <h3>Channel Info</h3>
                  <button
                    className="btn-close-drawer"
                    onClick={() => setShowInfoDrawer(false)}
                    aria-label="Close Info Drawer"
                  >
                    <i className="fa-solid fa-xmark"></i>
                  </button>
                </div>

                <div className="info-drawer-body">
                  <div className="info-drawer-avatar-block">
                    <img
                      src={activeChannel.avatar}
                      alt={activeChannel.name}
                      className="drawer-avatar-img"
                    />
                    <h4>{activeChannel.name}</h4>
                    <span className="drawer-handle">@{activeChannel.handle}</span>
                  </div>

                  <div className="drawer-stats-grid">
                    <div className="drawer-stat-item">
                      <span className="stat-value">{activeChannel.followersFormatted}</span>
                      <span className="stat-label">Followers</span>
                    </div>
                    <div className="drawer-stat-item">
                      <span className="stat-value">{activeChannel.posts?.length || 0}</span>
                      <span className="stat-label">Broadcasts</span>
                    </div>
                    <div className="drawer-stat-item">
                      <span className="stat-value">{activeChannel.categoryLabel}</span>
                      <span className="stat-label">Category</span>
                    </div>
                  </div>

                  <div className="drawer-section-block">
                    <label>Description</label>
                    <p>{activeChannel.bio}</p>
                  </div>

                  <div className="drawer-section-block">
                    <label>Channel Details</label>
                    <ul className="drawer-detail-list">
                      <li>
                        <i className="fa-regular fa-calendar"></i>
                        <span>Created {activeChannel.createdAt}</span>
                      </li>
                      <li>
                        <i className="fa-solid fa-shield-halved"></i>
                        <span>Official & Verified Channel</span>
                      </li>
                      <li>
                        <i className="fa-solid fa-lock-open"></i>
                        <span>Public Channel</span>
                      </li>
                    </ul>
                  </div>

                  <div className="drawer-actions-block">
                    <button
                      className="btn-drawer-share"
                      onClick={handleShareChannel}
                    >
                      <i className="fa-solid fa-link"></i>
                      <span>Copy Channel Link</span>
                    </button>
                  </div>
                </div>
              </aside>
            )}
          </section>
        )}
      </main>

      {/* Create Channel Modal */}
      <CreateChannelModal
        isOpen={showCreateModal}
        onClose={() => setShowCreateModal(false)}
        onCreateChannel={handleCreateChannel}
        onToast={onToast}
      />
    </div>
  );
}

export default Channels;
