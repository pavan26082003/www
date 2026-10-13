import { useState } from "react";
import { CALENDAR_CATEGORIES } from "../../data/calendarData";

function CreateEventModal({
  initialDate,
  initialTime,
  initialContact,
  initialCategory = "chat_meeting",
  chats = [],
  onClose,
  onCreateEvent,
  onToast,
}) {
  /* =========================================================
     FORM STATE
  ========================================================= */

  const [aiPrompt, setAiPrompt] = useState("");
  const [title, setTitle] = useState("");
  const [category, setCategory] = useState(initialCategory);

  const [date, setDate] = useState(
    initialDate || "2026-09-12"
  );

  const [startTime, setStartTime] = useState(
    initialTime || "17:00"
  );

  const [endTime, setEndTime] = useState("18:00");

  /*
   * Keep IDs as strings inside the select.
   * This prevents problems when IDs are numeric or strings.
   */
  const [selectedContactId, setSelectedContactId] = useState(
    initialContact?.id != null
      ? String(initialContact.id)
      : ""
  );

  const [description, setDescription] = useState("");
  const [visibility, setVisibility] = useState("friends");
  const [reminder, setReminder] = useState("15_min_before");
  const [recurrence, setRecurrence] = useState("none");
  const [autoSendToChat, setAutoSendToChat] = useState(true);

  /* =========================================================
     NORMALIZE CONTACT / GROUP DATA
  ========================================================= */

  /*
   * The select gets its options from "chats".
   *
   * Protect against:
   * - chats being undefined
   * - chats not being an array
   * - invalid records
   * - missing IDs
   * - missing names
   */
  const availableChats = Array.isArray(chats)
    ? chats.filter(
        (chat) =>
          chat &&
          chat.id != null &&
          String(chat.name || "").trim() !== ""
      )
    : [];

  /*
   * If the modal was opened from a specific contact but the
   * parent did not provide chats, keep that contact available.
   */
  const contactOptions =
    availableChats.length > 0
      ? availableChats
      : initialContact?.id != null &&
        String(initialContact?.name || "").trim() !== ""
      ? [initialContact]
      : [];

  /* =========================================================
     CATEGORY CHANGE
  ========================================================= */

  const handleCategoryChange = (catId) => {
    setCategory(catId);

    if (
      !title ||
      title.includes("Discussion") ||
      title.includes("Broadcast") ||
      title.includes("Demo") ||
      title.includes("Premiere") ||
      title.includes("Deliverables")
    ) {
      if (catId === "chat_meeting") {
        setTitle("Project Architecture Discussion");
      } else if (catId === "live_stream") {
        setTitle("Special Live Stream Broadcast");
      } else if (catId === "channel_event") {
        setTitle("Product Demo & AMA");
      } else if (catId === "reel_schedule") {
        setTitle("New Reel Premiere");
      } else if (catId === "personal_task") {
        setTitle("Review Team Deliverables");
      }
    }
  };

  /* =========================================================
     AI NATURAL LANGUAGE PARSER
  ========================================================= */

  const handleAiAutoFill = () => {
    if (!aiPrompt.trim()) {
      onToast?.(
        "Please enter a sentence for the AI Assistant (e.g., 'Meeting with Raju tomorrow at 5pm')"
      );
      return;
    }

    const text = aiPrompt.toLowerCase();

    let detectedCategory = category;
    let detectedTitle = aiPrompt.trim();
    let detectedTime = startTime;
    let detectedContactId = selectedContactId;
    let detectedRecurrence = recurrence;

    /* -------------------------------------------------------
       Category Detection
    ------------------------------------------------------- */

    if (
      text.includes("call") ||
      text.includes("meet") ||
      text.includes("discuss") ||
      text.includes("sync")
    ) {
      detectedCategory = "chat_meeting";
    } else if (
      text.includes("live") ||
      text.includes("stream") ||
      text.includes("broadcast")
    ) {
      detectedCategory = "live_stream";
    } else if (
      text.includes("channel") ||
      text.includes("demo")
    ) {
      detectedCategory = "channel_event";
    } else if (
      text.includes("reel") ||
      text.includes("publish")
    ) {
      detectedCategory = "reel_schedule";
    } else if (
      text.includes("remind") ||
      text.includes("task")
    ) {
      detectedCategory = "personal_task";
    }

    /* -------------------------------------------------------
       Time Detection
    ------------------------------------------------------- */

    const timeMatch = text.match(
      /(\d{1,2})\s*(am|pm)/i
    );

    if (timeMatch) {
      let hour = parseInt(timeMatch[1], 10);

      const ampm = timeMatch[2].toLowerCase();

      if (ampm === "pm" && hour < 12) {
        hour += 12;
      }

      if (ampm === "am" && hour === 12) {
        hour = 0;
      }

      detectedTime = `${String(hour).padStart(
        2,
        "0"
      )}:00`;
    }

    /* -------------------------------------------------------
       Recurrence Detection
    ------------------------------------------------------- */

    if (
      text.includes("every monday") ||
      text.includes("weekly") ||
      text.includes("every day") ||
      text.includes("daily")
    ) {
      detectedRecurrence = text.includes("daily")
        ? "daily"
        : "weekly";
    }

    /* -------------------------------------------------------
       Contact / Group Detection
    ------------------------------------------------------- */

    const matchedContact = contactOptions.find(
      (chat) =>
        text.includes(
          String(chat.name).toLowerCase()
        )
    );

    if (matchedContact) {
      detectedContactId = String(
        matchedContact.id
      );
    }

    /* -------------------------------------------------------
       Apply Detected Values
    ------------------------------------------------------- */

    setTitle(detectedTitle);
    setCategory(detectedCategory);
    setStartTime(detectedTime);
    setSelectedContactId(
      detectedContactId != null
        ? String(detectedContactId)
        : ""
    );
    setRecurrence(detectedRecurrence);

    onToast?.(
      "✨ AI parsed your event details successfully!"
    );
  };

  /* =========================================================
     FORM SUBMIT
  ========================================================= */

  const handleSubmit = (e) => {
    e.preventDefault();

    if (!title.trim()) {
      onToast?.("Please provide an event title");
      return;
    }

    /*
     * Compare IDs as strings.
     * This works for:
     *
     * 1
     * "1"
     * "contact-1"
     * "group-1"
     */
    const contact =
      contactOptions.find(
        (chat) =>
          String(chat.id) ===
          String(selectedContactId)
      ) || null;

    const newEvent = {
      id: `evt-${Date.now()}`,

      title: title.trim(),

      category,

      date,

      startTime,

      endTime,

      description: description.trim(),

      visibility,

      reminder,

      recurrence,

      contactId: contact?.id,

      contactName: contact?.name,

      contactAvatar: contact?.avatar,

      autoSendToChat,

      attendees: [
        {
          name: "Henry Jabbawockiez",
          avatar: 12,
          status: "host",
        },

        ...(contact
          ? [
              {
                name: contact.name,
                avatar: contact.avatar,
                status: "going",
              },
            ]
          : []),
      ],
    };

    onCreateEvent(
      newEvent,
      autoSendToChat
    );

    onClose();
  };

  /* =========================================================
     RENDER
  ========================================================= */

  return (
    <div
      className="create-event-overlay"
      onClick={onClose}
    >
      <div
        className="create-event-modal"
        onClick={(e) => e.stopPropagation()}
      >
        {/* =================================================
            MODAL HEADER
        ================================================= */}

        <div className="create-event-header">
          <div className="modal-title-row">
            <div className="modal-icon-badge">
              <i className="fa-solid fa-calendar-plus"></i>
            </div>

            <div>
              <h3>Create Social Activity</h3>

              <p>
                Connect events with conversations,
                people & channels
              </p>
            </div>
          </div>

          <button
            type="button"
            className="modal-close-btn"
            onClick={onClose}
            aria-label="Close"
          >
            <i className="fa-solid fa-xmark"></i>
          </button>
        </div>

        {/* =================================================
            AI QUICK ASSISTANT
        ================================================= */}

        <div className="ai-assistant-quick-box">
          <div className="ai-input-pill">
            <i className="fa-solid fa-wand-magic-sparkles text-purple"></i>

            <input
              type="text"
              placeholder="AI Assistant: e.g. 'Project review with Raju tomorrow at 5pm'..."
              value={aiPrompt}
              onChange={(e) =>
                setAiPrompt(e.target.value)
              }
              onKeyDown={(e) => {
                if (e.key === "Enter") {
                  e.preventDefault();
                  handleAiAutoFill();
                }
              }}
            />

            <button
              type="button"
              className="btn-ai-autofill"
              onClick={handleAiAutoFill}
            >
              Parse
            </button>
          </div>
        </div>

        {/* =================================================
            FORM BODY
        ================================================= */}

        <form
          className="create-event-form"
          onSubmit={handleSubmit}
        >
          {/* =================================================
              ACTIVITY CATEGORY
          ================================================= */}

          <div className="form-group">
            <label className="form-label">
              Activity Category
            </label>

            <div className="category-chips-grid">
              {CALENDAR_CATEGORIES.filter(
                (c) => c.id !== "all"
              ).map((cat) => (
                <button
                  key={cat.id}
                  type="button"
                  className={`category-chip ${
                    category === cat.id
                      ? "active"
                      : ""
                  }`}
                  onClick={() =>
                    handleCategoryChange(cat.id)
                  }
                >
                  <i className={cat.icon}></i>

                  <span>{cat.label}</span>
                </button>
              ))}
            </div>
          </div>

          {/* =================================================
              EVENT TITLE
          ================================================= */}

          <div className="form-group">
            <label className="form-label">
              Event Title *
            </label>

            <input
              type="text"
              className="form-input"
              placeholder="e.g. System Architecture Discussion"
              value={title}
              onChange={(e) =>
                setTitle(e.target.value)
              }
              required
            />
          </div>

          {/* =================================================
              DATE + TIME
          ================================================= */}

          <div className="form-row-2col">
            <div className="form-group">
              <label className="form-label">
                Date
              </label>

              <input
                type="date"
                className="form-input"
                value={date}
                onChange={(e) =>
                  setDate(e.target.value)
                }
                required
              />
            </div>

            <div className="form-group time-range-group">
              <label className="form-label">
                Time Window
              </label>

              <div className="time-inputs-pair">
                <input
                  type="time"
                  className="form-input"
                  value={startTime}
                  onChange={(e) =>
                    setStartTime(e.target.value)
                  }
                />

                <span>to</span>

                <input
                  type="time"
                  className="form-input"
                  value={endTime}
                  onChange={(e) =>
                    setEndTime(e.target.value)
                  }
                />
              </div>
            </div>
          </div>

          {/* =================================================
              CONTACT / GROUP BINDING
          ================================================= */}

          <div className="form-group">
            <label
              className="form-label"
              htmlFor="event-contact"
            >
              Tag Contact or Group
            </label>

            <select
              id="event-contact"
              className="form-select"
              value={selectedContactId}
              onChange={(e) =>
                setSelectedContactId(
                  e.target.value
                )
              }
            >
              <option value="">
                Select a contact or group
              </option>

              {contactOptions.length > 0 ? (
                contactOptions.map((chat) => (
                  <option
                    key={String(chat.id)}
                    value={String(chat.id)}
                  >
                    {chat.isGroup
                      ? "👥 Group: "
                      : "👤 "}
                    {chat.name}
                  </option>
                ))
              ) : (
                <option value="" disabled>
                  No contacts or groups available
                </option>
              )}
            </select>

            {/* Helpful frontend-only status */}
            {contactOptions.length === 0 && (
              <small
                style={{
                  display: "block",
                  marginTop: "6px",
                  color: "var(--color-text-muted, #8792a6)",
                  fontSize: "12px",
                }}
              >
                No contacts or groups were provided to
                this event form.
              </small>
            )}
          </div>

          {/* =================================================
              DESCRIPTION
          ================================================= */}

          <div className="form-group">
            <label className="form-label">
              Description / Agenda
            </label>

            <textarea
              className="form-textarea"
              rows={2}
              placeholder="Add agenda notes, meeting link, or discussion points..."
              value={description}
              onChange={(e) =>
                setDescription(e.target.value)
              }
            />
          </div>

          {/* =================================================
              PRIVACY + REMINDER
          ================================================= */}

          <div className="form-row-2col">
            <div className="form-group">
              <label className="form-label">
                Privacy Visibility
              </label>

              <select
                className="form-select"
                value={visibility}
                onChange={(e) =>
                  setVisibility(e.target.value)
                }
              >
                <option value="everyone">
                  🌍 Everyone (Public)
                </option>

                <option value="followers">
                  👥 Followers
                </option>

                <option value="friends">
                  🤝 Friends
                </option>

                <option value="selected_people">
                  🔒 Selected People
                </option>

                <option value="only_me">
                  👤 Only Me (Private)
                </option>
              </select>
            </div>

            <div className="form-group">
              <label className="form-label">
                Notification Reminder
              </label>

              <select
                className="form-select"
                value={reminder}
                onChange={(e) =>
                  setReminder(e.target.value)
                }
              >
                <option value="at_time">
                  At time of event
                </option>

                <option value="10_min_before">
                  10 minutes before
                </option>

                <option value="15_min_before">
                  15 minutes before
                </option>

                <option value="1_hour_before">
                  1 hour before
                </option>

                <option value="1_day_before">
                  1 day before
                </option>
              </select>
            </div>
          </div>

          {/* =================================================
              AUTO SEND EVENT CARD
          ================================================= */}

          <div className="form-checkbox-row">
            <label className="checkbox-container">
              <input
                type="checkbox"
                checked={autoSendToChat}
                onChange={(e) =>
                  setAutoSendToChat(
                    e.target.checked
                  )
                }
              />

              <span className="checkmark"></span>

              <span className="checkbox-text">
                Post interactive{" "}
                <strong>Event Card</strong>{" "}
                directly into conversation with
                RSVP buttons
              </span>
            </label>
          </div>

          {/* =================================================
              FOOTER ACTIONS
          ================================================= */}

          <div className="modal-footer-actions">
            <button
              type="button"
              className="btn-cancel"
              onClick={onClose}
            >
              Cancel
            </button>

            <button
              type="submit"
              className="btn-submit-event"
            >
              <i className="fa-solid fa-check"></i>

              <span>
                Create Social Event
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default CreateEventModal;