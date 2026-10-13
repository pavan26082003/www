import { useState, useEffect, useMemo } from "react";
import MonthView from "./MonthView";
import WeekView from "./WeekView";
import DayTimelineView from "./DayTimelineView";
import RelationshipTimelineView from "./RelationshipTimelineView";
import GroupPlannerView from "./GroupPlannerView";
import CreateEventModal from "./CreateEventModal";
import SmartAvailabilityModal from "./SmartAvailabilityModal";
import EventDetailModal from "./EventDetailModal";
import {
  CALENDAR_CATEGORIES,
  INITIAL_CALENDAR_EVENTS,
} from "../../data/calendarData";
import "./Calendar.css";

function Calendar({
  isClosing,
  onBack,
  onToast,
  chats = [],
  onOpenChatWithContact,
  onJoinCall,
  onWatchLive,
  onAddChatEventMessage,
  events: propEvents,
  onUpdateEvents,
}) {
  // Calendar Events with LocalStorage persistence or prop sync
  const [internalEvents, setInternalEvents] = useState(() => {
    try {
      const saved = localStorage.getItem("mychat_calendar_events");
      if (saved) return JSON.parse(saved);
    } catch {
      // ignore
    }
    return INITIAL_CALENDAR_EVENTS;
  });

  const events = propEvents || internalEvents;

  // Save to localStorage on changes
  useEffect(() => {
    try {
      localStorage.setItem("mychat_calendar_events", JSON.stringify(events));
    } catch {
      // ignore
    }
  }, [events]);

  const [viewMode, setViewMode] = useState("month"); // 'month' | 'week' | 'day' | 'relationship' | 'availability' | 'groups'
  const [currentYear, setCurrentYear] = useState(2026);
  const [currentMonth, setCurrentMonth] = useState(8); // 8 = September (0-indexed)
  const [selectedDate, setSelectedDate] = useState("2026-09-12");
  const [activeCategory, setActiveCategory] = useState("all");
  const [searchQuery, setSearchQuery] = useState("");
  const [showCreateModal, setShowCreateModal] = useState(false);
  const [showAvailabilityModal, setShowAvailabilityModal] = useState(false);
  const [selectedEventDetail, setSelectedEventDetail] = useState(null);
  const [createDefaults, setCreateDefaults] = useState({});

  // Month navigation
  const handlePrevMonth = () => {
    if (currentMonth === 0) {
      setCurrentMonth(11);
      setCurrentYear((prev) => prev - 1);
    } else {
      setCurrentMonth((prev) => prev - 1);
    }
  };

  const handleNextMonth = () => {
    if (currentMonth === 11) {
      setCurrentMonth(0);
      setCurrentYear((prev) => prev + 1);
    } else {
      setCurrentMonth((prev) => prev + 1);
    }
  };

  const handleToday = () => {
    setCurrentYear(2026);
    setCurrentMonth(8);
    setSelectedDate("2026-09-12");
  };

  // Filter events based on category and search
  const filteredEvents = useMemo(() => {
    return events.filter((evt) => {
      // Category filter
      if (activeCategory !== "all" && evt.category !== activeCategory) {
        return false;
      }
      // Search query filter
      if (searchQuery.trim()) {
        const q = searchQuery.toLowerCase().trim();
        const matchTitle = evt.title.toLowerCase().includes(q);
        const matchDesc = evt.description?.toLowerCase().includes(q);
        const matchPerson = evt.contactName?.toLowerCase().includes(q);
        const matchStreamer = evt.streamerName?.toLowerCase().includes(q);
        return matchTitle || matchDesc || matchPerson || matchStreamer;
      }
      return true;
    });
  }, [events, activeCategory, searchQuery]);

  const updateEventsState = (updater) => {
    if (onUpdateEvents) {
      const next = typeof updater === "function" ? updater(events) : updater;
      onUpdateEvents(next);
    } else {
      setInternalEvents((prev) => {
        const next = typeof updater === "function" ? updater(prev) : updater;
        return next;
      });
    }
  };

  // Create new event handler
  const handleCreateEvent = (newEvent, autoSendToChat) => {
    updateEventsState((prev) => [newEvent, ...prev]);
    onToast?.(`🎉 Event "${newEvent.title}" added to your Social Calendar!`);

    // Auto-post interactive Event Card into conversation
    if (autoSendToChat && newEvent.contactId !== undefined && onAddChatEventMessage) {
      onAddChatEventMessage(newEvent.contactId, {
        id: Date.now(),
        type: "outgoing",
        messageType: "event",
        text: `📅 New Event Scheduled: "${newEvent.title}" on ${newEvent.date} at ${newEvent.startTime}`,
        eventData: newEvent,
        time: "Just now",
        check: true,
      });
      onToast?.(`Sent Event Card to ${newEvent.contactName}'s conversation`);
    }
  };

  // Book availability slot handler
  const handleBookSlotAsEvent = (bookedEvent) => {
    updateEventsState((prev) => [bookedEvent, ...prev]);
  };

  // RSVP change handler
  const handleRsvp = (eventId, status) => {
    updateEventsState((prev) =>
      prev.map((e) => (e.id === eventId ? { ...e, status } : e))
    );
  };

  // Vote poll handler
  const handleVotePoll = (eventId, updatedPollOrOptionId) => {
    updateEventsState((prev) =>
      prev.map((e) => {
        if (e.id === eventId && e.poll) {
          if (typeof updatedPollOrOptionId === "object") {
            return { ...e, poll: updatedPollOrOptionId };
          }
          const optionId = updatedPollOrOptionId;
          const options = e.poll.options.map((opt) =>
            opt.id === optionId ? { ...opt, votes: opt.votes + 1 } : opt
          );
          return { ...e, poll: { ...e.poll, options, userVoted: optionId } };
        }
        return e;
      })
    );
  };

  // Delete event handler
  const handleDeleteEvent = (eventId) => {
    updateEventsState((prev) => prev.filter((e) => e.id !== eventId));
    onToast?.("Event removed from calendar");
  };

  // Toggle task complete
  const handleToggleTaskComplete = (taskId) => {
    updateEventsState((prev) =>
      prev.map((e) => (e.id === taskId ? { ...e, isCompleted: !e.isCompleted } : e))
    );
    onToast?.("Task status updated");
  };

  const monthName = new Date(currentYear, currentMonth).toLocaleString("en-US", {
    month: "long",
  });

  return (
    <div className={`social-calendar-workstation-page ${isClosing ? "closing" : ""}`}>
      {/* Top Navigation Header */}
      <header className="calendar-master-header">
        <div className="calendar-header-left">
          {onBack && (
            <button
              className="calendar-nav-back-btn"
              onClick={onBack}
              title="Back to Chats"
              aria-label="Back to Chats"
            >
              <i className="fa-solid fa-arrow-left"></i>
            </button>
          )}

          <div className="calendar-brand-block">
            <div className="calendar-icon-pill">
              <i className="fa-solid fa-calendar-days"></i>
            </div>
            <div>
              <div className="title-row">
                <h1 className="calendar-h1">Social Calendar</h1>
                <span className="calendar-events-counter">
                  {events.length} Activities
                </span>
              </div>
              <p className="calendar-subtext">
                Connect meetings, live broadcasts, channels, reels & memories
              </p>
            </div>
          </div>
        </div>

        {/* View Switcher Tabs */}
        <div className="calendar-view-switcher">
          <button
            className={`btn-view-toggle ${viewMode === "month" ? "active" : ""}`}
            onClick={() => setViewMode("month")}
            title="Month View"
          >
            <i className="fa-solid fa-table-cells"></i>
            <span>Month</span>
          </button>
          <button
            className={`btn-view-toggle ${viewMode === "week" ? "active" : ""}`}
            onClick={() => setViewMode("week")}
            title="Week View"
          >
            <i className="fa-solid fa-table-columns"></i>
            <span>Week</span>
          </button>
          <button
            className={`btn-view-toggle ${viewMode === "day" ? "active" : ""}`}
            onClick={() => setViewMode("day")}
            title="Day Timeline"
          >
            <i className="fa-solid fa-bars-staggered"></i>
            <span>Timeline</span>
          </button>
          <button
            className={`btn-view-toggle ${viewMode === "relationship" ? "active" : ""}`}
            onClick={() => setViewMode("relationship")}
            title="Relationship Memory Timeline"
          >
            <i className="fa-solid fa-timeline"></i>
            <span>Memory</span>
          </button>
          <button
            className={`btn-view-toggle ${viewMode === "groups" ? "active" : ""}`}
            onClick={() => setViewMode("groups")}
            title="Group Chat Calendars & Polls"
          >
            <i className="fa-solid fa-users"></i>
            <span>Groups</span>
          </button>
        </div>

        {/* Header Right Action CTA Buttons */}
        <div className="calendar-header-actions">
          <button
            className="btn-open-availability"
            onClick={() => setShowAvailabilityModal(true)}
            title="Smart Availability Slot Sharing"
          >
            <i className="fa-solid fa-clock"></i>
            <span>Availability</span>
          </button>

          <button
            className="btn-create-event-trigger"
            onClick={() => {
              setCreateDefaults({});
              setShowCreateModal(true);
            }}
            title="Create Social Event"
          >
            <i className="fa-solid fa-plus"></i>
            <span>New Activity</span>
          </button>
        </div>
      </header>

      {/* Sub-Header Navigation & Filter Controls Bar */}
      <div className="calendar-controls-bar">
        {/* Month / Date Navigator (when Month or Week is active) */}
        {(viewMode === "month" || viewMode === "week" || viewMode === "day") && (
          <div className="calendar-date-stepper">
            <button className="btn-step-nav" onClick={handlePrevMonth} title="Previous Month">
              <i className="fa-solid fa-chevron-left"></i>
            </button>
            <span className="current-month-label">
              {monthName} {currentYear}
            </span>
            <button className="btn-step-nav" onClick={handleNextMonth} title="Next Month">
              <i className="fa-solid fa-chevron-right"></i>
            </button>
            <button className="btn-today-shortcut" onClick={handleToday}>
              Today
            </button>
          </div>
        )}

        {/* Search Input Bar */}
        <div className="calendar-search-wrap">
          <label className="calendar-search-bar">
            <i className="fa-solid fa-magnifying-glass"></i>
            <input
              type="search"
              placeholder="Search activities, people, channels..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
            />
            {searchQuery && (
              <button
                type="button"
                className="clear-search-btn"
                onClick={() => setSearchQuery("")}
              >
                <i className="fa-solid fa-xmark"></i>
              </button>
            )}
          </label>
        </div>

        {/* Category Pills Strip */}
        <div className="calendar-category-pills">
          {CALENDAR_CATEGORIES.map((cat) => (
            <button
              key={cat.id}
              className={`calendar-cat-pill ${activeCategory === cat.id ? "active" : ""}`}
              onClick={() => setActiveCategory(cat.id)}
            >
              <i className={cat.icon}></i>
              <span>{cat.label}</span>
            </button>
          ))}
        </div>
      </div>

      {/* Main Workstation View Area */}
      <div className="calendar-workstation-viewport">
        {viewMode === "month" && (
          <MonthView
            currentYear={currentYear}
            currentMonth={currentMonth}
            events={filteredEvents}
            selectedDate={selectedDate}
            onSelectDate={(dateStr) => setSelectedDate(dateStr)}
            onOpenEventDetail={(evt) => setSelectedEventDetail(evt)}
            onQuickAddForDate={(dateStr) => {
              setCreateDefaults({ date: dateStr });
              setShowCreateModal(true);
            }}
          />
        )}

        {viewMode === "week" && (
          <WeekView
            selectedDate={selectedDate}
            events={filteredEvents}
            onSelectDate={(dateStr) => setSelectedDate(dateStr)}
            onOpenEventDetail={(evt) => setSelectedEventDetail(evt)}
            onQuickAddForDateAndTime={(dateStr, timeStr) => {
              setCreateDefaults({ date: dateStr, startTime: timeStr });
              setShowCreateModal(true);
            }}
          />
        )}

        {viewMode === "day" && (
          <DayTimelineView
            selectedDate={selectedDate}
            events={filteredEvents}
            onOpenEventDetail={(evt) => setSelectedEventDetail(evt)}
            onOpenChatWithContact={onOpenChatWithContact}
            onJoinCall={onJoinCall}
            onWatchLive={onWatchLive}
            onToggleTaskComplete={handleToggleTaskComplete}
            onToast={onToast}
          />
        )}

        {viewMode === "relationship" && (
          <RelationshipTimelineView
            chats={chats}
            onOpenChatWithContact={onOpenChatWithContact}
            onScheduleWithContact={(contact) => {
              setCreateDefaults({ contact, category: "chat_meeting" });
              setShowCreateModal(true);
            }}
            onToast={onToast}
          />
        )}

        {viewMode === "groups" && (
          <GroupPlannerView
            events={events}
            chats={chats}
            onOpenCreateEvent={(defaults) => {
              setCreateDefaults(defaults || {});
              setShowCreateModal(true);
            }}
            onOpenEventDetail={(evt) => setSelectedEventDetail(evt)}
            onVotePoll={handleVotePoll}
            onToast={onToast}
          />
        )}
      </div>

      {/* Create Event Dialog Modal */}
      {showCreateModal && (
        <CreateEventModal
          initialDate={createDefaults.date || selectedDate}
          initialTime={createDefaults.startTime}
          initialContact={createDefaults.contact}
          initialCategory={createDefaults.category || "chat_meeting"}
          chats={chats}
          onClose={() => setShowCreateModal(false)}
          onCreateEvent={handleCreateEvent}
          onToast={onToast}
        />
      )}

      {/* Smart Availability Dialog Modal */}
      {showAvailabilityModal && (
        <SmartAvailabilityModal
          onClose={() => setShowAvailabilityModal(false)}
          onBookSlotAsEvent={handleBookSlotAsEvent}
          onToast={onToast}
        />
      )}

      {/* Event Details & RSVP Modal */}
      {selectedEventDetail && (
        <EventDetailModal
          event={selectedEventDetail}
          onClose={() => setSelectedEventDetail(null)}
          onRsvp={handleRsvp}
          onVotePoll={handleVotePoll}
          onDeleteEvent={handleDeleteEvent}
          onOpenChatWithContact={onOpenChatWithContact}
          onJoinCall={onJoinCall}
          onWatchLive={onWatchLive}
          onToast={onToast}
        />
      )}
    </div>
  );
}

export default Calendar;
