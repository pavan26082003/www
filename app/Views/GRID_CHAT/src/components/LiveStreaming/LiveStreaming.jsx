import { useState } from "react";
import {
  initialLiveStreams,
  upcomingStreams as initialUpcomingStreams,
  streamCategories,
} from "../../data/liveStreamsData";
import LivePlayer from "./LivePlayer";
import LiveChat from "./LiveChat";
import GoLiveModal from "./GoLiveModal";
import "./LiveStreaming.css";

function LiveStreaming({ isClosing, onBack, onToast }) {
  const [streams, setStreams] = useState(initialLiveStreams);
  const [activeStreamId, setActiveStreamId] = useState(initialLiveStreams[0]?.id);
  const [activeCategory, setActiveCategory] = useState("all");
  const [searchQuery, setSearchQuery] = useState("");
  const [showGoLiveModal, setShowGoLiveModal] = useState(false);
  const [upcoming, setUpcoming] = useState(initialUpcomingStreams);
  const [mobileActiveTab, setMobileActiveTab] = useState("player"); // 'player' | 'chat'

  const activeStream = streams.find((s) => s.id === activeStreamId) || streams[0];

  // Filter streams based on category and search query
  const filteredStreams = streams.filter((stream) => {
    if (activeCategory !== "all" && stream.category !== activeCategory) {
      return false;
    }
    if (searchQuery.trim()) {
      const q = searchQuery.toLowerCase().trim();
      const matchTitle = stream.title.toLowerCase().includes(q);
      const matchStreamer = stream.streamer.name.toLowerCase().includes(q);
      const matchTags = stream.tags?.some((t) => t.toLowerCase().includes(q));
      return matchTitle || matchStreamer || matchTags;
    }
    return true;
  });

  const handleSelectStream = (stream) => {
    setActiveStreamId(stream.id);
    onToast?.(`Switched to ${stream.streamer.name}'s stream`);
    // Scroll smoothly to top player
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  const handleStartUserStream = (newStream) => {
    setStreams((prev) => [newStream, ...prev]);
    setActiveStreamId(newStream.id);
    setShowGoLiveModal(false);
  };

  const handleToggleReminder = (upcomingId) => {
    setUpcoming((prev) =>
      prev.map((item) => {
        if (item.id === upcomingId) {
          const nextState = !item.reminded;
          onToast?.(
            nextState
              ? `🔔 Reminder set for "${item.title}"`
              : `Reminder cancelled`
          );
          return { ...item, reminded: nextState };
        }
        return item;
      })
    );
  };

  return (
    <div className={`live-streaming-page ${isClosing ? "closing" : ""}`}>
      {/* Top Navigation Header */}
      <header className="live-streaming-header">
        <div className="header-left-col">
          {onBack && (
            <button
              className="live-back-nav-btn"
              onClick={onBack}
              title="Back to Chats"
            >
              <i className="fa-solid fa-arrow-left"></i>
            </button>
          )}

          <div className="live-brand-title">
            <div className="live-icon-pill">
              <i className="fa-solid fa-video"></i>
            </div>
            <div>
              <div className="title-row">
                <h1 className="live-header-h1">Live Streaming</h1>
                <span className="live-active-counter">
                  <span className="live-dot-pulse"></span> {streams.length} LIVE
                </span>
              </div>
              <p className="live-header-sub">Watch, interact, and stream with your community</p>
            </div>
          </div>
        </div>

        {/* Search Streams */}
        <div className="header-search-wrap">
          <label className="stream-search-bar">
            <i className="fa-solid fa-magnifying-glass"></i>
            <input
              type="search"
              placeholder="Search streams, creators, tags..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
            />
            {searchQuery && (
              <button
                type="button"
                className="clear-search-btn"
                onClick={() => setSearchQuery("")}
              >
                <i className="fa-solid fa-xmark"></i>
              </button>
            )}
          </label>
        </div>

        {/* Go Live Studio CTA */}
        <div className="header-right-actions">
          <button
            className="go-live-studio-btn"
            onClick={() => setShowGoLiveModal(true)}
          >
            <i className="fa-solid fa-tower-broadcast"></i>
            <span>Go Live</span>
          </button>
        </div>
      </header>

      {/* Category Pills Strip */}
      <div className="live-categories-bar">
        {streamCategories.map((cat) => (
          <button
            key={cat.id}
            className={`live-category-pill ${activeCategory === cat.id ? "active" : ""}`}
            onClick={() => setActiveCategory(cat.id)}
          >
            <i className={cat.icon}></i>
            <span>{cat.label}</span>
          </button>
        ))}
      </div>

      {/* Main Scroll Content Viewport */}
      <div className="live-content-scroller">
        {/* Mobile View Toggle Switcher */}
        <div className="mobile-stream-tabs mobile-only">
          <button
            className={`mobile-tab-toggle ${mobileActiveTab === "player" ? "active" : ""}`}
            onClick={() => setMobileActiveTab("player")}
          >
            <i className="fa-solid fa-play"></i>
            <span>Stream View</span>
          </button>
          <button
            className={`mobile-tab-toggle ${mobileActiveTab === "chat" ? "active" : ""}`}
            onClick={() => setMobileActiveTab("chat")}
          >
            <i className="fa-solid fa-comments"></i>
            <span>Live Chat</span>
          </button>
        </div>

        {/* Featured Broadcast Stage (Player + Chat) */}
        {activeStream && (
          <section className="live-broadcast-stage">
            <div className={`stage-player-col ${mobileActiveTab !== "player" ? "mobile-hidden" : ""}`}>
              <LivePlayer stream={activeStream} onToast={onToast} />
            </div>

            <div className={`stage-chat-col ${mobileActiveTab !== "chat" ? "mobile-hidden" : ""}`}>
              <LiveChat stream={activeStream} onToast={onToast} />
            </div>
          </section>
        )}

        {/* Explore & Discover Section */}
        <section className="live-discovery-section">
          <div className="section-headline-bar">
            <div className="headline-text-group">
              <h2 className="section-title">
                <i className="fa-solid fa-fire text-orange"></i> Live Broadcasts
              </h2>
              <span className="section-counter">
                {filteredStreams.length} stream{filteredStreams.length !== 1 ? "s" : ""} available
              </span>
            </div>
          </div>

          {filteredStreams.length === 0 ? (
            <div className="empty-streams-card">
              <i className="fa-solid fa-satellite-dish empty-icon"></i>
              <h3>No streams found</h3>
              <p>Try searching for another topic or change the category filter.</p>
              <button
                className="reset-filter-btn"
                onClick={() => {
                  setActiveCategory("all");
                  setSearchQuery("");
                }}
              >
                Reset Filters
              </button>
            </div>
          ) : (
            <div className="streams-cards-grid">
              {filteredStreams.map((item) => {
                const isCurrent = item.id === activeStreamId;

                return (
                  <article
                    key={item.id}
                    className={`stream-card ${isCurrent ? "is-selected-card" : ""}`}
                    onClick={() => handleSelectStream(item)}
                  >
                    <div className="card-thumbnail-box">
                      <img
                        src={item.thumbnail}
                        alt={item.title}
                        className="card-thumb-img"
                      />
                      <div className="card-thumb-overlay"></div>

                      <span className="card-live-badge">
                        <span className="card-live-dot"></span> LIVE
                      </span>

                      <span className="card-viewers-badge">
                        <i className="fa-solid fa-eye"></i> {item.viewers?.toLocaleString()}
                      </span>

                      {isCurrent && (
                        <span className="current-watching-tag">
                          <i className="fa-solid fa-circle-play"></i> Watching Now
                        </span>
                      )}
                    </div>

                    <div className="card-info-content">
                      <div className="card-creator-row">
                        <img
                          src={`https://i.pravatar.cc/90?img=${item.streamer.avatar}`}
                          alt={item.streamer.name}
                          className="card-creator-avatar"
                        />
                        <div className="card-creator-meta">
                          <div className="card-creator-name">
                            <span>{item.streamer.name}</span>
                            {item.streamer.verified && (
                              <i className="fa-solid fa-circle-check verified-icon"></i>
                            )}
                          </div>
                          <span className="card-category-label">{item.categoryLabel}</span>
                        </div>
                      </div>

                      <h3 className="card-stream-title">{item.title}</h3>

                      <div className="card-tags-row">
                        {item.tags?.slice(0, 3).map((t, idx) => (
                          <span key={idx} className="card-tag">
                            #{t}
                          </span>
                        ))}
                      </div>
                    </div>
                  </article>
                );
              })}
            </div>
          )}
        </section>

        {/* Upcoming Scheduled Streams */}
        <section className="upcoming-streams-section">
          <div className="section-headline-bar">
            <div className="headline-text-group">
              <h2 className="section-title">
                <i className="fa-solid fa-calendar-check text-purple"></i> Scheduled Broadcasts
              </h2>
              <span className="section-counter">Upcoming streams from contacts</span>
            </div>
          </div>

          <div className="upcoming-cards-grid">
            {upcoming.map((up) => (
              <div key={up.id} className="upcoming-stream-card">
                <div className="upcoming-top-row">
                  <div className="upcoming-creator-wrap">
                    <img
                      src={`https://i.pravatar.cc/80?img=${up.avatar}`}
                      alt={up.streamer}
                      className="upcoming-avatar"
                    />
                    <div>
                      <h4 className="upcoming-streamer-name">{up.streamer}</h4>
                      <span className="upcoming-category">{up.category}</span>
                    </div>
                  </div>

                  <button
                    className={`reminder-btn ${up.reminded ? "is-reminded" : ""}`}
                    onClick={() => handleToggleReminder(up.id)}
                    title={up.reminded ? "Reminder active" : "Set reminder"}
                  >
                    <i
                      className={`fa-solid ${
                        up.reminded ? "fa-bell text-purple" : "fa-bell"
                      }`}
                    ></i>
                    <span>{up.reminded ? "Reminded" : "Remind Me"}</span>
                  </button>
                </div>

                <h3 className="upcoming-title">{up.title}</h3>

                <div className="upcoming-footer-row">
                  <span className="upcoming-time-badge">
                    <i className="fa-regular fa-clock"></i> {up.date}
                  </span>
                </div>
              </div>
            ))}
          </div>
        </section>
      </div>

      {/* Go Live Studio Modal */}
      {showGoLiveModal && (
        <GoLiveModal
          onClose={() => setShowGoLiveModal(false)}
          onStartStream={handleStartUserStream}
          onToast={onToast}
        />
      )}
    </div>
  );
}

export default LiveStreaming;
