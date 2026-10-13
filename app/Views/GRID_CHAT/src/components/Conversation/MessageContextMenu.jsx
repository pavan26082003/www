import "./Conversation.css";

function MessageContextMenu({
  message,
  onReply,
  onCopy,
  onReact,
  onForward,
  onTogglePin,
  onToggleStar,
  onEdit,
  onDelete,
  onOpenInfo,
  positionStyle = {},
}) {
  if (!message) return null;

  const isOutgoing = message.type === "outgoing";
  const isDeleted = Boolean(message.isDeleted);
  const isStarred = Boolean(message.isStarred);
  const isPinned = Boolean(message.isPinned);
  const canEdit = isOutgoing && !isDeleted && (!message.messageType || message.messageType === "text");

  return (
    <div
      className="msg-context-menu"
      style={positionStyle}
      onClick={(e) => e.stopPropagation()}
      role="menu"
      aria-label="Message options"
    >
      <button
        type="button"
        className="msg-context-item"
        onClick={onReply}
        role="menuitem"
      >
        <i className="fa-solid fa-reply"></i>
        <span>Reply</span>
      </button>

      <button
        type="button"
        className="msg-context-item"
        onClick={onCopy}
        role="menuitem"
      >
        <i className="fa-regular fa-copy"></i>
        <span>Copy</span>
      </button>

      <button
        type="button"
        className="msg-context-item"
        onClick={onReact}
        role="menuitem"
      >
        <i className="fa-regular fa-face-smile"></i>
        <span>React</span>
      </button>

      <button
        type="button"
        className="msg-context-item"
        onClick={onForward}
        role="menuitem"
      >
        <i className="fa-solid fa-share"></i>
        <span>Forward</span>
      </button>

      <button
        type="button"
        className="msg-context-item"
        onClick={onTogglePin}
        role="menuitem"
      >
        <i className="fa-solid fa-thumbtack"></i>
        <span>{isPinned ? "Unpin" : "Pin"}</span>
      </button>

      <button
        type="button"
        className="msg-context-item"
        onClick={onToggleStar}
        role="menuitem"
      >
        <i className={`${isStarred ? "fa-solid is-starred-icon" : "fa-regular"} fa-star`}></i>
        <span>{isStarred ? "Unstar" : "Star"}</span>
      </button>

      {canEdit && (
        <button
          type="button"
          className="msg-context-item"
          onClick={onEdit}
          role="menuitem"
        >
          <i className="fa-solid fa-pen"></i>
          <span>Edit</span>
        </button>
      )}

      <button
        type="button"
        className="msg-context-item"
        onClick={onOpenInfo}
        role="menuitem"
      >
        <i className="fa-solid fa-circle-info"></i>
        <span>Message info</span>
      </button>

      <div className="msg-context-divider"></div>

      <button
        type="button"
        className="msg-context-item is-delete"
        onClick={onDelete}
        role="menuitem"
      >
        <i className="fa-regular fa-trash-can"></i>
        <span>Delete</span>
      </button>
    </div>
  );
}

export default MessageContextMenu;
