import "./Conversation.css";

function DeleteMessageModal({
  isOpen,
  count = 1,
  canDeleteForEveryone = true,
  onDeleteForEveryone,
  onDeleteForMe,
  onCancel,
}) {
  if (!isOpen) return null;

  const titleText = "Deleting Messages?";

  return (
    <div className="msg-delete-modal-backdrop" onClick={onCancel}>
      <div
        className="msg-delete-modal-card"
        onClick={(e) => e.stopPropagation()}
        role="dialog"
        aria-modal="true"
        aria-labelledby="delete-dialog-title"
      >
        <div className="msg-delete-modal-header">
          <div className="msg-delete-icon-badge">
            <i className="fa-regular fa-trash-can"></i>
          </div>
          <h3 id="delete-dialog-title" className="msg-delete-title">
            {titleText}
          </h3>
          <p className="msg-delete-desc">
            {count > 1 ? `${count} messages selected.` : "1 message selected."} You can delete for everyone or just for yourself.
          </p>
        </div>

        {/* 3 Column Buttons Arranged One Below Another (WhatsApp style) */}
        <div className="msg-delete-actions-column">
          <button
            type="button"
            className="msg-delete-btn msg-delete-btn-everyone"
            onClick={onDeleteForEveryone}
            autoFocus
          >
            <i className="fa-solid fa-users"></i>
            <span>Delete For Every One</span>
          </button>

          <button
            type="button"
            className="msg-delete-btn msg-delete-btn-me"
            onClick={onDeleteForMe}
          >
            <i className="fa-regular fa-user"></i>
            <span>Delete for me</span>
          </button>

          <button
            type="button"
            className="msg-delete-btn msg-delete-btn-cancel"
            onClick={onCancel}
          >
            <span>Cancel</span>
          </button>
        </div>
      </div>
    </div>
  );
}

export default DeleteMessageModal;
