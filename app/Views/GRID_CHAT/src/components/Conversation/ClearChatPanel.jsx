import "./ClearChatPanel.css";

const COPY = {
  clear: {
    title: "Clear chat",
    icon: "fa-trash-can",
    question: (name) => `Are you sure you want to clear all messages from ${name ? `your conversation with ${name}` : "this conversation"}?`,
    subtext: "Messages are removed only for you — the other person keeps their copy. This can't be undone.",
    confirm: "Clear chat",
  },
  delete: {
    title: "Delete chat",
    icon: "fa-trash",
    question: (name) => `Delete ${name ? `your chat with ${name}` : "this chat"}?`,
    subtext:
      "The chat and its messages are removed from your list only — the other person keeps their copy. If they message you again, the chat comes back with only the new messages.",
    confirm: "Delete chat",
  },
};

// Confirmation panel for "Clear chat" (mode="clear") and "Delete chat" (mode="delete")
function ClearChatPanel({ chat, mode = "clear", onBack, onClose, onConfirmClear }) {
  const copy = COPY[mode] || COPY.clear;

  return (
    <div className="clear-chat-panel-view">
      <header className="panel-secondary-header">
        <div className="panel-secondary-header-left">
          <button className="panel-secondary-back-btn" onClick={onBack} title="Back">
            <i className="fa-solid fa-arrow-left"></i>
          </button>
          <div className="panel-secondary-title-wrap">
            <h2>{copy.title}</h2>
          </div>
        </div>

        <div className="panel-secondary-header-actions">
          <button
            className="panel-secondary-icon-btn"
            onClick={onClose}
            title="Close"
            aria-label="Close"
          >
            <i className="fa-solid fa-xmark"></i>
          </button>
        </div>
      </header>

      <div className="panel-secondary-scroll">
        <div className="clear-chat-intro">
          <div className="clear-chat-icon-warn">
            <i className={`fa-solid ${copy.icon}`}></i>
          </div>
          <h3>{copy.title}</h3>
          <p>{copy.question(chat?.name)}</p>
          <p className="clear-chat-subtext">{copy.subtext}</p>
        </div>

        <div className="panel-secondary-footer two-buttons">
          <button type="button" className="panel-secondary-btn-secondary" onClick={onClose}>
            Cancel
          </button>
          <button type="button" className="panel-secondary-btn-danger" onClick={onConfirmClear}>
            {copy.confirm}
          </button>
        </div>
      </div>
    </div>
  );
}

export default ClearChatPanel;
