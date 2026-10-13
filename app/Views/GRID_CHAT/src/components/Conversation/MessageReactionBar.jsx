import { useState } from "react";
import "./Conversation.css";

const DEFAULT_REACTIONS = ["👍", "❤️", "😂", "😮", "😢", "🙏"];

const EXTENDED_REACTIONS = [
  "🎉", "🔥", "👏", "💯", "🤝", "🥳", 
  "😍", "🤩", "🤔", "👀", "🙌", "✨", 
  "🚀", "💡", "💪", "🫡", "💔", "😴",
  "😎", "🎯", "🌟", "🎈", "☕", "🍻"
];

function MessageReactionBar({
  onSelectReaction,
  positionStyle = {},
  currentReactions = [],
}) {
  const [showMore, setShowMore] = useState(false);

  return (
    <div className="msg-reaction-bar-container" style={positionStyle}>
      <div className="msg-reaction-bar" role="toolbar" aria-label="Message reactions">
        {DEFAULT_REACTIONS.map((emoji) => {
          const isUserReacted = currentReactions.some(
            (r) => r.emoji === emoji && r.userReacted
          );
          return (
            <button
              key={emoji}
              type="button"
              className={`msg-reaction-emoji-btn ${isUserReacted ? "is-reacted" : ""}`}
              onClick={(e) => {
                e.stopPropagation();
                onSelectReaction(emoji);
              }}
              title={`React with ${emoji}`}
              aria-label={`React with ${emoji}`}
            >
              <span className="reaction-emoji-char">{emoji}</span>
            </button>
          );
        })}

        <button
          type="button"
          className={`msg-reaction-more-btn ${showMore ? "active" : ""}`}
          onClick={(e) => {
            e.stopPropagation();
            setShowMore(!showMore);
          }}
          title="More reactions"
          aria-label="More reactions"
        >
          <i className="fa-solid fa-plus"></i>
        </button>
      </div>

      {showMore && (
        <div className="msg-reaction-extended-palette" onClick={(e) => e.stopPropagation()}>
          <div className="extended-palette-header">
            <span>Choose reaction</span>
            <button
              type="button"
              className="btn-palette-close"
              onClick={() => setShowMore(false)}
            >
              <i className="fa-solid fa-xmark"></i>
            </button>
          </div>
          <div className="extended-palette-grid">
            {EXTENDED_REACTIONS.map((emoji) => (
              <button
                key={emoji}
                type="button"
                className="palette-emoji-btn"
                onClick={() => {
                  onSelectReaction(emoji);
                  setShowMore(false);
                }}
              >
                <span>{emoji}</span>
              </button>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}

export default MessageReactionBar;
