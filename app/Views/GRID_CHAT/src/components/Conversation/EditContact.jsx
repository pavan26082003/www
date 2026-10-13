import { useState } from "react";
import "./EditContact.css";

function EditContact({ contact, onBack, onSave, onToast }) {
  // Split contact name into first and last name
  const nameParts = (contact?.name || "").trim().split(" ");
  const initialFirst = nameParts[0] || "";
  const initialLast = nameParts.slice(1).join(" ") || "";

  // Extract phone digits without +91 country prefix
  const rawPhone = (contact?.phone || "98853 36987")
    .replace(/^\+91\s*/, "")
    .trim();

  const [firstName, setFirstName] = useState(initialFirst || "Apeshyam");
  const [lastName, setLastName] = useState(initialLast);
  const [username, setUsername] = useState(
    contact?.username || (initialFirst ? initialFirst.toLowerCase() : "apeshyam")
  );
  const [countryCode] = useState("IN +91");
  const [phone, setPhone] = useState(rawPhone || "98853 36987");
  const [syncToPhone, setSyncToPhone] = useState(true);
  const [syncEmail, setSyncEmail] = useState(
    contact?.email || "bandarusaitulasiram@gmail.com"
  );
  const [showEmailOptions, setShowEmailOptions] = useState(false);

  const availableEmails = [
    "bandarusaitulasiram@gmail.com",
    "personal.backup@gmail.com",
    "work.gridchat@company.org",
  ];

  const handleSave = (e) => {
    e.preventDefault();
    const trimmedFirst = firstName.trim();
    if (!trimmedFirst) {
      onToast?.("First name cannot be empty");
      return;
    }

    const fullName = lastName.trim()
      ? `${trimmedFirst} ${lastName.trim()}`
      : trimmedFirst;

    const fullPhone = phone.trim().startsWith("+")
      ? phone.trim()
      : `+91 ${phone.trim()}`;

    const updated = {
      ...contact,
      name: fullName,
      phone: fullPhone,
      username: username.trim(),
      email: syncEmail,
      syncToPhone,
    };

    onSave?.(updated);
    onToast?.("Contact updated successfully ✨");
  };

  return (
    <div className="edit-contact-page">
      {/* Header matching Image 2 */}
      <header className="edit-contact-header">
        <button
          type="button"
          className="edit-back-btn"
          onClick={onBack}
          title="Back"
          aria-label="Back"
        >
          <i className="fa-solid fa-arrow-left"></i>
        </button>

        <h2 className="edit-header-title">Edit contact</h2>

        <button
          type="button"
          className="edit-menu-btn"
          title="More options"
          aria-label="More options"
        >
          <i className="fa-solid fa-ellipsis-vertical"></i>
        </button>
      </header>

      {/* Scrollable Form Body */}
      <form className="edit-contact-form" onSubmit={handleSave}>
        {/* Row 1: First Name & Last Name */}
        <div className="edit-form-group">
          <div className="edit-group-icon">
            <i className="fa-regular fa-user"></i>
          </div>
          <div className="edit-group-fields">
            <div className="edit-input-container">
              <label className="edit-floating-label">First name</label>
              <input
                type="text"
                className="edit-text-input"
                value={firstName}
                onChange={(e) => setFirstName(e.target.value)}
                autoFocus
              />
            </div>

            <div className="edit-input-container">
              <label className="edit-floating-label">Last name</label>
              <input
                type="text"
                className="edit-text-input"
                value={lastName}
                placeholder="Last name"
                onChange={(e) => setLastName(e.target.value)}
              />
            </div>
          </div>
        </div>

        {/* Row 2: Username */}
        <div className="edit-form-group">
          <div className="edit-group-icon">
            <i className="fa-solid fa-at"></i>
          </div>
          <div className="edit-group-fields">
            <div className="edit-input-container">
              <label className="edit-floating-label">Username</label>
              <input
                type="text"
                className="edit-text-input"
                value={username}
                placeholder="Username"
                onChange={(e) => setUsername(e.target.value)}
              />
            </div>
          </div>
        </div>

        {/* Row 3: Country & Phone */}
        <div className="edit-form-group">
          <div className="edit-group-icon">
            <i className="fa-solid fa-phone"></i>
          </div>
          <div className="edit-group-fields edit-phone-split-row">
            {/* Country */}
            <div className="edit-input-container edit-country-container">
              <label className="edit-floating-label">Country</label>
              <div className="edit-country-select-box">
                <span>{countryCode}</span>
                <i className="fa-solid fa-caret-down"></i>
              </div>
            </div>

            {/* Phone */}
            <div className="edit-input-container edit-phone-container">
              <label className="edit-floating-label">Phone</label>
              <input
                type="tel"
                className="edit-text-input"
                value={phone}
                onChange={(e) => setPhone(e.target.value)}
              />
            </div>
          </div>
        </div>

        {/* Row 4: Sync contact to phone */}
        <div className="edit-form-group edit-sync-group">
          <div className="edit-group-icon">
            <i className="fa-solid fa-arrows-rotate"></i>
          </div>
          <div className="edit-group-fields edit-sync-fields">
            <div className="sync-toggle-row">
              <div className="sync-text-info">
                <h4 className="sync-title">Sync contact to phone</h4>
                <p className="sync-subtitle">
                  Only contacts with a phone number can be synced
                </p>
              </div>

              {/* iOS/WhatsApp style Green Switch */}
              <label className="sync-switch-label">
                <input
                  type="checkbox"
                  checked={syncToPhone}
                  onChange={(e) => setSyncToPhone(e.target.checked)}
                />
                <span className={`sync-switch-slider ${syncToPhone ? "is-checked" : ""}`}>
                  {syncToPhone && <i className="fa-solid fa-check sync-check-icon"></i>}
                </span>
              </label>
            </div>

            {/* Sync to email selector */}
            <div className="sync-email-dropdown-wrap">
              <label className="sync-to-label">Sync to</label>
              <div
                className="sync-email-bar"
                onClick={() => setShowEmailOptions((prev) => !prev)}
                role="button"
                tabIndex={0}
              >
                <span className="sync-email-text">{syncEmail}</span>
                <i className={`fa-solid ${showEmailOptions ? "fa-caret-up" : "fa-caret-down"}`}></i>
              </div>

              {showEmailOptions && (
                <div className="sync-email-menu">
                  {availableEmails.map((email) => (
                    <button
                      type="button"
                      key={email}
                      className={`sync-email-menu-item ${email === syncEmail ? "active" : ""}`}
                      onClick={() => {
                        setSyncEmail(email);
                        setShowEmailOptions(false);
                      }}
                    >
                      {email}
                    </button>
                  ))}
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Bottom Save Action Button */}
        <div className="edit-contact-bottom-bar">
          <button type="submit" className="edit-save-btn">
            Save
          </button>
        </div>
      </form>
    </div>
  );
}

export default EditContact;
