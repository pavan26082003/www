import { useState, useRef } from "react";
import "./LivePlayer.css";

function LivePlayer({ stream, onToast, onSendReaction }) {
  const [isPlaying, setIsPlaying] = useState(true);
  const [isMuted, setIsMuted] = useState(false);
  const [volume, setVolume] = useState(80);
  const [quality, setQuality] = useState("1080p60");
  const [isFollowed, setIsFollowed] = useState(false);
  const [floatingReactions, setFloatingReactions] = useState([]);
  const [isFullscreen, setIsFullscreen] = useState(false);
  const playerRef = useRef(null);
  const reactionCountRef = useRef(0);

  // Quick reactions toolbar
  const reactionEmojis = ["❤️", "🔥", "👏", "🚀", "💯", "😍"];

  const handleTriggerReaction = (emoji) => {
    reactionCountRef.current += 1;
    const id = crypto.randomUUID();
    const xPos = 20 + ((reactionCountRef.current * 17) % 60); // 20% to 80%
    setFloatingReactions((prev) => [...prev, { id, emoji, xPos }]);

    setTimeout(() => {
      setFloatingReactions((prev) => prev.filter((r) => r.id !== id));
    }, 2400);

    if (onSendReaction) {
      onSendReaction(emoji);
    }
  };

  const togglePlay = () => {
    setIsPlaying(!isPlaying);
    onToast?.(isPlaying ? "Stream paused" : "Stream resumed");
  };

  const toggleMute = () => {
    setIsMuted(!isMuted);
    onToast?.(isMuted ? "Audio unmuted" : "Audio muted");
  };

  const toggleFullscreen = () => {
    if (!playerRef.current) return;
    if (!document.fullscreenElement) {
      playerRef.current.requestFullscreen?.().catch(() => {});
      setIsFullscreen(true);
    } else {
      document.exitFullscreen?.().catch(() => {});
      setIsFullscreen(false);
    }
  };

  const handleFollowToggle = () => {
    setIsFollowed(!isFollowed);
    onToast?.(isFollowed ? `Unfollowed ${stream.streamer.name}` : `Followed ${stream.streamer.name} 🔔`);
  };

  const handleShare = () => {
    navigator.clipboard?.writeText?.(window.location.href);
    onToast?.("Stream link copied to clipboard! 🔗");
  };

  return (
    <div className="live-player-container" ref={playerRef}>
      {/* Viewport Area */}
      <div className="player-viewport">
        {/* Animated Background Simulation */}
        <div
          className={`viewport-backdrop ${isPlaying ? "is-live-active" : "is-paused"}`}
          style={{ background: stream.videoBgGradient || "#0f172a" }}
        >
          <img
            src={stream.thumbnail}
            alt={stream.title}
            className="stream-backdrop-img"
          />
          <div className="stream-glow-overlay"></div>

          {/* Ambient Live Animation Indicator */}
          <div className="live-pulse-radar">
            <span className="radar-ring"></span>
            <span className="radar-ring delay"></span>
          </div>
        </div>

        {/* Top Badges Overlay */}
        <div className="player-top-overlay">
          <div className="top-left-badges">
            <span className="live-pill-badge">
              <span className="live-dot"></span> LIVE
            </span>
            <span className="viewers-count-badge">
              <i className="fa-solid fa-eye"></i> {stream.viewers?.toLocaleString()} watching
            </span>
            <span className="duration-badge">
              <i className="fa-regular fa-clock"></i> {stream.duration || "00:45:18"}
            </span>
          </div>

          <div className="top-right-badges">
            <span className="quality-pill">{quality}</span>
            <button
              className="player-overlay-btn"
              onClick={handleShare}
              title="Share Stream"
            >
              <i className="fa-solid fa-share-nodes"></i>
            </button>
          </div>
        </div>

        {/* Floating Reactions Particles */}
        <div className="floating-reactions-layer">
          {floatingReactions.map((r) => (
            <span
              key={r.id}
              className="floating-reaction-item"
              style={{ left: `${r.xPos}%` }}
            >
              {r.emoji}
            </span>
          ))}
        </div>

        {/* Interactive Floating Quick Reactions bar on right side */}
        <div className="player-quick-reactions">
          {reactionEmojis.map((emoji, idx) => (
            <button
              key={idx}
              className="quick-reaction-bubble"
              onClick={() => handleTriggerReaction(emoji)}
              title={`React with ${emoji}`}
            >
              {emoji}
            </button>
          ))}
        </div>

        {/* Bottom Control Bar Overlay */}
        <div className="player-controls-bar">
          <div className="controls-left">
            <button
              className="control-icon-btn"
              onClick={togglePlay}
              title={isPlaying ? "Pause" : "Play"}
            >
              <i className={`fa-solid ${isPlaying ? "fa-pause" : "fa-play"}`}></i>
            </button>

            <div className="volume-control-group">
              <button
                className="control-icon-btn"
                onClick={toggleMute}
                title={isMuted ? "Unmute" : "Mute"}
              >
                <i
                  className={`fa-solid ${
                    isMuted || volume === 0
                      ? "fa-volume-xmark"
                      : volume < 50
                      ? "fa-volume-low"
                      : "fa-volume-high"
                  }`}
                ></i>
              </button>
              <input
                type="range"
                min="0"
                max="100"
                value={isMuted ? 0 : volume}
                onChange={(e) => {
                  setVolume(Number(e.target.value));
                  if (isMuted) setIsMuted(false);
                }}
                className="volume-slider"
              />
            </div>
          </div>

          <div className="controls-right">
            <select
              className="quality-select"
              value={quality}
              onChange={(e) => {
                setQuality(e.target.value);
                onToast?.(`Stream quality set to ${e.target.value}`);
              }}
              title="Stream Quality"
            >
              <option value="1080p60">1080p 60fps (HD)</option>
              <option value="720p60">720p 60fps</option>
              <option value="480p">480p (Standard)</option>
              <option value="Auto">Auto (Adaptive)</option>
            </select>

            <button
              className="control-icon-btn"
              onClick={toggleFullscreen}
              title={isFullscreen ? "Exit Fullscreen" : "Fullscreen"}
            >
              <i className={`fa-solid ${isFullscreen ? "fa-compress" : "fa-expand"}`}></i>
            </button>
          </div>
        </div>
      </div>

      {/* Stream Info & Streamer Header Bar */}
      <div className="streamer-info-card">
        <div className="streamer-profile-row">
          <div className="streamer-avatar-box">
            <img
              src={`https://i.pravatar.cc/150?img=${stream.streamer.avatar}`}
              alt={stream.streamer.name}
              className="streamer-avatar-img"
            />
            <span className="streamer-live-indicator"></span>
          </div>

          <div className="streamer-meta">
            <div className="streamer-name-line">
              <h3 className="streamer-name">{stream.streamer.name}</h3>
              {stream.streamer.verified && (
                <i className="fa-solid fa-circle-check verified-badge" title="Verified Streamer"></i>
              )}
              <span className="category-pill-tag">{stream.categoryLabel}</span>
            </div>
            <p className="streamer-subtext">
              {stream.streamer.followers} followers • {stream.startedAt}
            </p>
          </div>

          <div className="streamer-actions">
            <button
              className={`follow-streamer-btn ${isFollowed ? "is-following" : ""}`}
              onClick={handleFollowToggle}
            >
              <i className={`fa-solid ${isFollowed ? "fa-check" : "fa-plus"}`}></i>
              <span>{isFollowed ? "Following" : "Follow"}</span>
            </button>
          </div>
        </div>

        <h2 className="stream-headline">{stream.title}</h2>

        <div className="stream-tags-list">
          {stream.tags?.map((tag, i) => (
            <span key={i} className="stream-tag-item">
              #{tag}
            </span>
          ))}
        </div>
      </div>
    </div>
  );
}

export default LivePlayer;
