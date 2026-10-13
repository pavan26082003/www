import { useEffect, useState } from "react";
import { createPortal } from "react-dom";
import { getAvatarUrl } from "../../utils/avatar";
import "./Groups.css";

const SEARCH_DEBOUNCE_MS = 300;
const GROUP_NAME_MAX = 100;

/**
 * Pick several people (active CRM users). Used for "New group" (askGroupName) and
 * "Add members". onConfirm(selectedUsers, groupName) resolves to true when done.
 */
function PeoplePickerModal({
  title,
  subtitle,
  confirmLabel,
  icon = "fa-user-group",
  askGroupName = false,
  excludeIds = [],
  // People already chosen, e.g. when editing a status privacy list
  initialSelected = [],
  // Lets the user confirm with nobody selected (clearing an existing list)
  allowEmpty = false,
  onSearchContacts,
  onConfirm,
  onClose,
}) {
  const [groupName, setGroupName] = useState("");
  const [query, setQuery] = useState("");
  const [contacts, setContacts] = useState([]);
  const [selected, setSelected] = useState(initialSelected);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState("");
  const [isSaving, setIsSaving] = useState(false);

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
      if (e.key === "Escape" && !isSaving) onClose();
    };
    document.addEventListener("keydown", handleKeyDown);
    return () => document.removeEventListener("keydown", handleKeyDown);
  }, [onClose, isSaving]);

  const excluded = new Set(excludeIds.map(Number));
  const visibleContacts = contacts.filter((contact) => !excluded.has(Number(contact.id)));
  const isSelected = (id) => selected.some((person) => person.id === id);

  const toggle = (contact) => {
    setSelected((prev) =>
      prev.some((person) => person.id === contact.id)
        ? prev.filter((person) => person.id !== contact.id)
        : [...prev, contact]
    );
  };

  const canConfirm =
    (allowEmpty || selected.length > 0) && (!askGroupName || groupName.trim() !== "") && !isSaving;

  const handleConfirm = async () => {
    if (!canConfirm) return;
    setIsSaving(true);
    const done = await onConfirm(selected, groupName.trim());
    setIsSaving(false);
    if (done) onClose();
  };

  let listContent;
  if (isLoading) {
    listContent = <li className="people-picker-empty">Loading people…</li>;
  } else if (error) {
    listContent = <li className="people-picker-empty is-error">{error}</li>;
  } else if (visibleContacts.length === 0) {
    listContent = <li className="people-picker-empty">No people found</li>;
  } else {
    listContent = visibleContacts.map((contact) => {
      const checked = isSelected(contact.id);
      return (
        <li key={contact.id}>
          <button
            type="button"
            className={`people-picker-item ${checked ? "is-selected" : ""}`}
            onClick={() => toggle(contact)}
            aria-pressed={checked}
          >
            <img src={getAvatarUrl(contact.avatar, 80, contact.name)} alt="" />
            <span className="people-picker-text">
              <strong>{contact.name}</strong>
              <small>{contact.role || contact.email}</small>
            </span>
            <span className="people-picker-check" aria-hidden="true">
              {checked && <i className="fa-solid fa-check"></i>}
            </span>
          </button>
        </li>
      );
    });
  }

  return createPortal(
    <div
      className="new-contact-modal-backdrop"
      onMouseDown={(e) => {
        if (e.target === e.currentTarget && !isSaving) onClose();
      }}
    >
      <div className="new-contact-modal people-picker-modal" role="dialog" aria-modal="true" aria-labelledby="people-picker-title">
        <div className="new-contact-modal-header">
          <div className="new-contact-title-group">
            <div className="new-contact-icon">
              <i className={`fa-solid ${icon}`}></i>
            </div>
            <div>
              <h2 id="people-picker-title">{title}</h2>
              {subtitle && <p>{subtitle}</p>}
            </div>
          </div>
          <button type="button" className="new-contact-close" onClick={onClose} aria-label="Close" title="Close" disabled={isSaving}>
            <i className="fa-solid fa-xmark"></i>
          </button>
        </div>

        <div className="new-contact-form">
          {askGroupName && (
            <div className="new-contact-field">
              <label htmlFor="people-picker-group-name">Group name</label>
              <div className="new-contact-input-wrap">
                <i className="fa-solid fa-users"></i>
                <input
                  id="people-picker-group-name"
                  type="text"
                  value={groupName}
                  onChange={(e) => setGroupName(e.target.value)}
                  placeholder="e.g. Project Team"
                  maxLength={GROUP_NAME_MAX}
                  autoComplete="off"
                  autoFocus
                />
              </div>
            </div>
          )}

          {selected.length > 0 && (
            <div className="people-picker-chips" aria-label="Selected people">
              {selected.map((person) => (
                <button key={person.id} type="button" className="people-picker-chip" onClick={() => toggle(person)} title={`Remove ${person.name}`}>
                  <img src={getAvatarUrl(person.avatar, 48, person.name)} alt="" />
                  <span>{person.name}</span>
                  <i className="fa-solid fa-xmark"></i>
                </button>
              ))}
            </div>
          )}

          <div className="new-contact-field">
            <label htmlFor="people-picker-search">
              Add people
              <span className="optional-label">{selected.length} selected</span>
            </label>
            <div className="new-contact-input-wrap">
              <i className="fa-solid fa-magnifying-glass"></i>
              <input
                id="people-picker-search"
                type="text"
                value={query}
                onChange={(e) => {
                  setQuery(e.target.value);
                  setIsLoading(true);
                }}
                placeholder="Search by name or email"
                autoComplete="off"
                autoFocus={!askGroupName}
                maxLength={100}
              />
            </div>
          </div>

          <ul className="people-picker-list">{listContent}</ul>
        </div>

        <div className="new-contact-modal-footer">
          <button type="button" className="new-contact-cancel-btn" onClick={onClose} disabled={isSaving}>
            Cancel
          </button>
          <button type="button" className="new-contact-create-btn" onClick={handleConfirm} disabled={!canConfirm}>
            <i className={`fa-solid ${isSaving ? "fa-spinner fa-spin" : "fa-check"}`}></i>
            {confirmLabel}
          </button>
        </div>
      </div>
    </div>,
    document.body
  );
}

export default PeoplePickerModal;
