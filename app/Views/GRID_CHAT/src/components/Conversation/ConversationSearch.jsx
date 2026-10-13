import { useMemo } from "react";

// Resolves what text a message should be matched against — most message types carry
// the searchable content in a type-specific field rather than `.text`.
const getSearchableText = (message) => {
  if (message.text) return message.text;
  if (message.messageType === "poll") return message.pollData?.question || "";
  if (message.messageType === "contact") return message.contactData?.name || "";
  if (message.messageType === "file") return message.fileName || "";
  if (message.messageType === "event") return message.eventData?.title || "";
  return "";
};

const getResultPreview = (message) => {
  switch (message.messageType) {
    case "poll":
      return { icon: "fa-solid fa-square-poll-vertical", label: message.pollData?.question || "Poll" };
    case "contact":
      return { icon: "fa-solid fa-user", label: message.contactData?.name || "Contact" };
    case "file":
      return { icon: "fa-solid fa-file-lines", label: message.fileName || "Document" };
    case "image":
      return { icon: "fa-solid fa-image", label: message.text || "Photo" };
    case "video":
      return { icon: "fa-solid fa-video", label: message.text || message.fileName || "Video" };
    case "audio":
      return { icon: "fa-solid fa-headphones", label: message.fileName || "Audio message" };
    case "location":
      return { icon: "fa-solid fa-location-dot", label: "Location shared" };
    case "event":
      return { icon: "fa-solid fa-calendar-check", label: message.eventData?.title || message.text || "Event" };
    default:
      return { icon: "fa-solid fa-message", label: message.text || "" };
  }
};

const highlightMatch = (text, query) => {
  if (!query) return text;
  const idx = text.toLowerCase().indexOf(query.toLowerCase());
  if (idx === -1) return text;
  return (
    <>
      {text.slice(0, idx)}
      <mark>{text.slice(idx, idx + query.length)}</mark>
      {text.slice(idx + query.length)}
    </>
  );
};

function ConversationSearch({
  isOpen,
  query,
  onQueryChange,
  onClose,
  messages = [],
  contactName,
  onJumpToMessage,
}) {
  // Groups matches under the nearest preceding date divider already present in the
  // message list — purely derived from local/mock conversation data, no backend involved.
  const groupedResults = useMemo(() => {
    const trimmed = query.trim().toLowerCase();
    if (!trimmed) return [];

    let currentGroup = "Earlier";
    const groups = [];

    messages.forEach((message) => {
      if (message.type === "divider") {
        currentGroup = message.text;
        return;
      }
      if (message.type !== "incoming" && message.type !== "outgoing") return;

      const searchable = getSearchableText(message);
      if (!searchable.toLowerCase().includes(trimmed)) return;

      let bucket = groups.find((g) => g.label === currentGroup);
      if (!bucket) {
        bucket = { label: currentGroup, items: [] };
        groups.push(bucket);
      }
      bucket.items.push(message);
    });

    return groups;
  }, [messages, query]);

  if (!isOpen) return null;

  const trimmedQuery = query.trim();
  const hasResults = groupedResults.some((group) => group.items.length > 0);

  return (
    <div className="conversation-search-layer">
      <div className="conversation-search-header">
        <button
          type="button"
          className="header-action-btn"
          onClick={onClose}
          title="Close search"
          aria-label="Close search"
        >
          <i className="fa-solid fa-xmark"></i>
        </button>
        <h2>Search messages</h2>
      </div>

      <div className="conversation-search-input-row">
        <i className="fa-solid fa-magnifying-glass"></i>
        <input
          type="text"
          autoFocus
          placeholder="Search messages..."
          value={query}
          onChange={(event) => onQueryChange(event.target.value)}
        />
        {query && (
          <button
            type="button"
            className="conversation-search-clear"
            onClick={() => onQueryChange("")}
            title="Clear"
            aria-label="Clear search"
          >
            <i className="fa-solid fa-xmark"></i>
          </button>
        )}
      </div>

      <div className="conversation-search-results">
        {!trimmedQuery ? (
          <div className="conversation-search-empty">
            <i className="fa-solid fa-magnifying-glass"></i>
            <span>Search for messages in this conversation</span>
          </div>
        ) : !hasResults ? (
          <div className="conversation-search-empty">
            <i className="fa-solid fa-comment-slash"></i>
            <span>No messages found for &ldquo;{trimmedQuery}&rdquo;</span>
          </div>
        ) : (
          groupedResults.map((group) => (
            <div className="conversation-search-group" key={group.label}>
              <div className="conversation-search-group-label">{group.label}</div>
              {group.items.map((message) => {
                const preview = getResultPreview(message);
                return (
                  <button
                    type="button"
                    key={message.id}
                    className="conversation-search-result"
                    onClick={() => onJumpToMessage(message.id)}
                  >
                    <span className="conversation-search-result-icon">
                      <i className={preview.icon}></i>
                    </span>
                    <span className="conversation-search-result-body">
                      <span className="conversation-search-result-sender">
                        {message.type === "incoming" ? contactName : "You"}
                      </span>
                      <span className="conversation-search-result-text">
                        {highlightMatch(preview.label, trimmedQuery)}
                      </span>
                    </span>
                    <span className="conversation-search-result-time">{message.time}</span>
                  </button>
                );
              })}
            </div>
          ))
        )}
      </div>
    </div>
  );
}

export default ConversationSearch;
