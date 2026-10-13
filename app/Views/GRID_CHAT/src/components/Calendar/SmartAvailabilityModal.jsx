import { useState } from "react";
import { INITIAL_AVAILABILITY_SLOTS } from "../../data/calendarData";

function SmartAvailabilityModal({
  onClose,
  onBookSlotAsEvent,
  onToast,
}) {
  const [slots, setSlots] = useState(INITIAL_AVAILABILITY_SLOTS);
  const [selectedSlotId, setSelectedSlotId] = useState(null);
  const [bookerName, setBookerName] = useState("Raju");
  const [meetingTopic, setMeetingTopic] = useState("Product Strategy & Design Review");

  const handleToggleSlot = (id) => {
    setSlots((prev) =>
      prev.map((s) => (s.id === id ? { ...s, isAvailable: !s.isAvailable } : s))
    );
  };

  const handleConfirmBooking = (e) => {
    e.preventDefault();
    const slot = slots.find((s) => s.id === selectedSlotId);
    if (!slot) return;

    // Create an event from this booked slot
    const newEvent = {
      id: `evt-booked-${Date.now()}`,
      title: `${meetingTopic} (${bookerName})`,
      category: "chat_meeting",
      date: "2026-09-15", // Next Tuesday
      startTime: slot.time.split("-")[0]?.trim() || "15:00",
      endTime: slot.time.split("-")[1]?.trim() || "16:00",
      contactName: bookerName,
      contactAvatar: 33,
      description: `Booked via Smart Availability Sharing: ${slot.day} ${slot.time}`,
      location: "GRID Video Sync",
      visibility: "selected_people",
      status: "going",
      attendees: [
        { name: "Henry Jabbawockiez", avatar: 12, status: "host" },
        { name: bookerName, avatar: 33, status: "going" },
      ],
      reminder: "15_min_before",
    };

    onBookSlotAsEvent(newEvent);
    onToast?.(`🎉 Successfully booked slot with ${bookerName}! Synced to your calendar.`);
    onClose();
  };

  return (
    <div className="create-event-overlay" onClick={onClose}>
      <div className="create-event-modal availability-modal" onClick={(e) => e.stopPropagation()}>
        {/* Header */}
        <div className="create-event-header">
          <div className="modal-title-row">
            <div className="modal-icon-badge availability-badge">
              <i className="fa-solid fa-calendar-check"></i>
            </div>
            <div>
              <h3>Smart Availability Sharing</h3>
              <p>Publish bookable slots and schedule meetings with zero friction</p>
            </div>
          </div>
          <button className="modal-close-btn" onClick={onClose}>
            <i className="fa-solid fa-xmark"></i>
          </button>
        </div>

        {/* Modal Content */}
        <div className="availability-modal-body">
          <div className="availability-info-banner">
            <i className="fa-solid fa-bolt text-amber"></i>
            <span>
              Share your open windows with contacts. When someone books a slot, it automatically appears in both calendars!
            </span>
          </div>

          <h4 className="slots-section-title">Active Booking Windows</h4>
          <div className="slots-list">
            {slots.map((slot) => (
              <div
                key={slot.id}
                className={`availability-slot-card ${slot.isAvailable ? "available" : "paused"} ${
                  selectedSlotId === slot.id ? "selected-to-book" : ""
                }`}
                onClick={() => {
                  if (slot.isAvailable) setSelectedSlotId(slot.id);
                }}
              >
                <div className="slot-left-info">
                  <span className="slot-day-tag">{slot.day}</span>
                  <strong className="slot-time-range">{slot.time}</strong>
                  <span className="slot-duration-lbl">{slot.duration}</span>
                </div>

                <div className="slot-right-controls" onClick={(e) => e.stopPropagation()}>
                  <button
                    type="button"
                    className={`btn-slot-toggle ${slot.isAvailable ? "is-active" : ""}`}
                    onClick={() => handleToggleSlot(slot.id)}
                  >
                    {slot.isAvailable ? "Open for Booking" : "Paused"}
                  </button>
                </div>
              </div>
            ))}
          </div>

          {/* Booking Simulator Form */}
          {selectedSlotId && (
            <form className="slot-booking-form" onSubmit={handleConfirmBooking}>
              <div className="booking-form-header">
                <i className="fa-solid fa-calendar-plus text-purple"></i>
                <span>Confirm Slot Booking</span>
              </div>

              <div className="form-row-2col">
                <div className="form-group">
                  <label className="form-label">Attendee / Contact</label>
                  <input
                    type="text"
                    className="form-input"
                    value={bookerName}
                    onChange={(e) => setBookerName(e.target.value)}
                    required
                  />
                </div>
                <div className="form-group">
                  <label className="form-label">Meeting Topic</label>
                  <input
                    type="text"
                    className="form-input"
                    value={meetingTopic}
                    onChange={(e) => setMeetingTopic(e.target.value)}
                    required
                  />
                </div>
              </div>

              <button type="submit" className="btn-confirm-booking">
                <i className="fa-solid fa-check"></i>
                <span>Book Selected Slot & Sync Calendar</span>
              </button>
            </form>
          )}

          {/* Share Action */}
          <div className="availability-share-bar">
            <button
              type="button"
              className="btn-share-slots"
              onClick={() => onToast?.("📋 Availability link copied to clipboard! Share it in any chat.")}
            >
              <i className="fa-solid fa-share-nodes"></i>
              <span>Copy Availability Link</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}

export default SmartAvailabilityModal;
