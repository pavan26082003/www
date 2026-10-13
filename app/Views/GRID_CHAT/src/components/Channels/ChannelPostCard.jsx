import { useState } from "react";

function ChannelPostCard({ post, channel, onReaction, onToast }) {
  const [selectedPollOption, setSelectedPollOption] = useState(null);

  const handleEmojiClick = (emoji) => {
    onReaction(channel.id, post.id, emoji);
  };

  const handleShare = () => {
    navigator.clipboard?.writeText?.(window.location.href);
    onToast?.("Channel post link copied to clipboard! 🔗");
  };

  const handleVote = (index) => {
    if (selectedPollOption !== null) return;
    setSelectedPollOption(index);
    onToast?.("Vote recorded in channel poll! 🗳️");
  };

  return (
    <article className="channel-post-card">
      {/* Post Author / Meta Header */}
      <header className="post-card-header">
        <div className="post-author-group">
          <img
            src={channel.avatar}
            alt={channel.name}
            className="post-channel-avatar"
          />
          <div className="post-author-meta">
            <div className="post-author-name-row">
              <span className="post-channel-name">{channel.name}</span>
              {channel.verified && (
                <i className="fa-solid fa-circle-check post-verified-badge" title="Verified Channel"></i>
              )}
            </div>
            <span className="post-timestamp">{post.timestamp}</span>
          </div>
        </div>

        <button
          className="post-share-icon-btn"
          onClick={handleShare}
          title="Share Post"
          aria-label="Share Post"
        >
          <i className="fa-solid fa-share-nodes"></i>
        </button>
      </header>

      {/* Post Body Content */}
      <div className="post-card-body">
        <p className="post-text-content">{post.content}</p>

        {/* Media: Image / Infographic */}
        {post.media?.type === "image" && (
          <div className="post-media-box">
            <img
              src={post.media.url}
              alt={post.media.caption || "Channel broadcast media"}
              className="post-media-img"
              loading="lazy"
            />
            {post.media.caption && (
              <span className="post-media-caption">{post.media.caption}</span>
            )}
          </div>
        )}

        {/* Media: Interactive Poll */}
        {post.media?.type === "poll" && (
          <div className="post-poll-box">
            <div className="poll-question-row">
              <i className="fa-solid fa-square-poll-vertical poll-icon"></i>
              <span className="poll-question">{post.media.question}</span>
            </div>

            <div className="poll-options-list">
              {post.media.options.map((opt, idx) => {
                const isSelected = selectedPollOption === idx;
                const percentage = opt.percentage || 0;

                return (
                  <button
                    key={idx}
                    className={`poll-option-btn ${isSelected ? "is-selected-vote" : ""}`}
                    onClick={() => handleVote(idx)}
                  >
                    <div
                      className="poll-fill-bar"
                      style={{ width: selectedPollOption !== null ? `${percentage}%` : "0%" }}
                    ></div>
                    <div className="poll-label-row">
                      <span className="poll-opt-text">{opt.text}</span>
                      {selectedPollOption !== null && (
                        <span className="poll-opt-percent">{percentage}%</span>
                      )}
                    </div>
                  </button>
                );
              })}
            </div>

            <div className="poll-footer-info">
              <span>{post.media.totalVotes?.toLocaleString()} total votes</span>
              <span>{selectedPollOption !== null ? "• Vote submitted" : "• Tap option to vote"}</span>
            </div>
          </div>
        )}
      </div>

      {/* Post Footer: Metrics & Interactive Reaction Bubbles */}
      <footer className="post-card-footer">
        <div className="post-reactions-strip">
          {Object.entries(post.reactions || {}).map(([emoji, count]) => {
            const isSelfReacted = post.userReaction === emoji;

            return (
              <button
                key={emoji}
                className={`post-reaction-chip ${isSelfReacted ? "active-reaction" : ""}`}
                onClick={() => handleEmojiClick(emoji)}
                title={`React with ${emoji}`}
              >
                <span className="reaction-emoji">{emoji}</span>
                <span className="reaction-count">{count}</span>
              </button>
            );
          })}

          {/* Quick Add Reaction Button */}
          <button
            className="add-reaction-trigger"
            onClick={() => handleEmojiClick("🔥")}
            title="React with 🔥"
          >
            <i className="fa-regular fa-face-smile"></i>
          </button>
        </div>

        <div className="post-stats-group">
          <span className="post-views-count">
            <i className="fa-solid fa-eye"></i> {post.views?.toLocaleString()}
          </span>
        </div>
      </footer>
    </article>
  );
}

export default ChannelPostCard;
