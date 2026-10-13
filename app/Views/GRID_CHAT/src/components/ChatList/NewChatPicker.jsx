import { useEffect, useState } from "react";
import { createPortal } from "react-dom";
import { getAvatarUrl } from "../../utils/avatar";

const SEARCH_DEBOUNCE_MS = 300;

// Pick a person (active CRM user) to start a direct chat with.
function NewChatPicker({ onSearchContacts, onStartChat, onClose }) {
  const [query, setQuery] = useState("");
  const [contacts, setContacts] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState("");
  const [startingId, setStartingId] = useState(null);

  useEffect(() => {
    let cancelled = false;
    const timer = setTimeout(async () => {
      const result = await onSearchContacts(query.trim());
      if (cancelled) return;
      setIsLoading(false);
      if (result.success) {
        setContacts(result.data.contacts);
        setError("");
      } else {
        setError(result.message || "Could not load people");
      }
    }, query ? SEARCH_DEBOUNCE_MS : 0);

    return () => {
      cancelled = true;
      clearTimeout(timer);
    };
  }, [query, onSearchContacts]);

  useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.key === "Escape") onClose();
    };
    document.addEventListener("keydown", handleKeyDown);
    return () => document.removeEventListener("keydown", handleKeyDown);
  }, [onClose]);

  const handlePick = async (contact) => {
    setStartingId(contact.id);
    const started = await onStartChat(contact);
    setStartingId(null);
    if (started) onClose();
  };

  let listContent;
  if (isLoading) {
    listContent = <li className="new-chat-contact-empty">Loading people…</li>;
  } else if (error) {
    listContent = <li className="new-chat-contact-empty is-error">{error}</li>;
  } else if (contacts.length === 0) {
    listContent = <li className="new-chat-contact-empty">No people found</li>;
  } else {
    listContent = contacts.map((contact) => (
      <li key={contact.id}>
        <button
          type="button"
          className="new-chat-contact-item"
          onClick={() => handlePick(contact)}
          disabled={startingId !== null}
        >
          <img src={getAvatarUrl(contact.avatar, 80, contact.name)} alt="" />
          <span className="new-chat-contact-text">
            <strong>{contact.name}</strong>
            <small>{contact.role || contact.email}</small>
          </span>
          {startingId === contact.id && <i className="fa-solid fa-spinner fa-spin"></i>}
        </button>
      </li>
    ));
  }

  return createPortal(
    <div
      className="new-contact-modal-backdrop"
      onMouseDown={(e) => {
        if (e.target === e.currentTarget) onClose();
      }}
    >
      <div className="new-contact-modal" role="dialog" aria-modal="true" aria-labelledby="new-chat-title">
        <div className="new-contact-modal-header">
          <div className="new-contact-title-group">
            <div className="new-contact-icon">
              <i className="fa-solid fa-comment-medical"></i>
            </div>
            <div>
              <h2 id="new-chat-title">New Chat</h2>
              <p>Choose a person to start a conversation</p>
            </div>
          </div>

          <button type="button" className="new-contact-close" onClick={onClose} aria-label="Close" title="Close">
            <i className="fa-solid fa-xmark"></i>
          </button>
        </div>

        <div className="new-contact-form">
          <div className="new-contact-field">
            <label htmlFor="new-chat-search">Search people</label>
            <div className="new-contact-input-wrap">
              <i className="fa-solid fa-magnifying-glass"></i>
              <input
                id="new-chat-search"
                type="text"
                value={query}
                onChange={(e) => {
                  setQuery(e.target.value);
                  setIsLoading(true);
                }}
                placeholder="Name or email"
                autoFocus
                autoComplete="off"
                maxLength={100}
              />
            </div>
          </div>

          <ul className="new-chat-contact-list">{listContent}</ul>
        </div>

        <div className="new-contact-modal-footer">
          <button type="button" className="new-contact-cancel-btn" onClick={onClose}>
            Cancel
          </button>
        </div>
      </div>
    </div>,
    document.body
  );
}

export default NewChatPicker;
