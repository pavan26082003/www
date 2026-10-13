import { useState } from "react";
import { getAvatarUrl } from "../../utils/avatar";
import "./ShareContactModal.css";

function ShareContactModal({ contacts = [], onClose, onSelect }) {
  const [search, setSearch] = useState("");

  const filtered = contacts.filter((c) =>
    c.name.toLowerCase().includes(search.trim().toLowerCase())
  );

  return (
    <div className="contact-modal-backdrop" onClick={onClose}>
      <div className="contact-modal" onClick={(e) => e.stopPropagation()}>
        <div className="contact-modal-header">
          <h3>
            <i className="fa-solid fa-user"></i> Share Contact
          </h3>
          <button className="contact-modal-close" onClick={onClose} title="Close" type="button">
            <i className="fa-solid fa-xmark"></i>
          </button>
        </div>

        <div className="contact-modal-search">
          <i className="fa-solid fa-magnifying-glass"></i>
          <input
            type="text"
            placeholder="Search contacts..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            autoFocus
          />
        </div>

        <div className="contact-modal-list">
          {filtered.length === 0 ? (
            <div className="contact-modal-empty">No contacts found</div>
          ) : (
            filtered.map((contact) => (
              <button
                type="button"
                key={contact.id}
                className="contact-modal-item"
                onClick={() => onSelect(contact)}
              >
                <img
                  src={getAvatarUrl(contact.avatar, 80, contact.name)}
                  alt={contact.name}
                  className="contact-modal-avatar"
                />
                <div className="contact-modal-info">
                  <span className="contact-modal-name">{contact.name}</span>
                  <span className="contact-modal-status">{contact.status || "GRID Contact"}</span>
                </div>
                <i className="fa-solid fa-chevron-right"></i>
              </button>
            ))
          )}
        </div>
      </div>
    </div>
  );
}

export default ShareContactModal;
