import { useMemo } from "react";

function MonthView({
  currentYear,
  currentMonth,
  events = [],
  selectedDate,
  onSelectDate,
  onOpenEventDetail,
  onQuickAddForDate,
}) {
  const daysOfWeek = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];

  // Generate calendar days for the currentMonth (0-indexed)
  const calendarCells = useMemo(() => {
    const firstDayIndex = new Date(currentYear, currentMonth, 1).getDay(); // 0 = Sun
    // Convert Sunday to 7 so Monday is 1, Sunday is 7
    const adjustedFirstDay = firstDayIndex === 0 ? 6 : firstDayIndex - 1;

    const daysInCurrentMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const daysInPrevMonth = new Date(currentYear, currentMonth, 0).getDate();

    const cells = [];

    // Prev month padding
    for (let i = adjustedFirstDay - 1; i >= 0; i--) {
      const day = daysInPrevMonth - i;
      const prevM = currentMonth === 0 ? 12 : currentMonth;
      const prevY = currentMonth === 0 ? currentYear - 1 : currentYear;
      const dateStr = `${prevY}-${String(prevM).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
      cells.push({
        day,
        dateStr,
        isCurrentMonth: false,
        isPrev: true,
      });
    }

    // Current month days
    for (let day = 1; day <= daysInCurrentMonth; day++) {
      // Format as YYYY-MM-DD
      const y = currentYear;
      const m = String(currentMonth + 1).padStart(2, "0");
      const d = String(day).padStart(2, "0");
      const dateStr = `${y}-${m}-${d}`;
      cells.push({
        day,
        dateStr,
        isCurrentMonth: true,
        isToday:
          new Date().getFullYear() === y &&
          new Date().getMonth() === currentMonth &&
          new Date().getDate() === day,
      });
    }

    // Next month padding to round up to complete 35 or 42 grid
    const totalCells = cells.length > 35 ? 42 : 35;
    const remaining = totalCells - cells.length;
    for (let day = 1; day <= remaining; day++) {
      const nextM = currentMonth === 11 ? 1 : currentMonth + 2;
      const nextY = currentMonth === 11 ? currentYear + 1 : currentYear;
      const dateStr = `${nextY}-${String(nextM).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
      cells.push({
        day,
        dateStr,
        isCurrentMonth: false,
        isNext: true,
      });
    }

    return cells;
  }, [currentYear, currentMonth]);

  // Group events by date string
  const eventsByDate = useMemo(() => {
    const map = {};
    events.forEach((evt) => {
      if (!map[evt.date]) map[evt.date] = [];
      map[evt.date].push(evt);
    });
    return map;
  }, [events]);

  const getCategoryBadgeClass = (category) => {
    switch (category) {
      case "chat_meeting":
        return "badge-meeting";
      case "live_stream":
        return "badge-live";
      case "channel_event":
        return "badge-channel";
      case "moment_memory":
        return "badge-memory";
      case "reel_schedule":
        return "badge-reel";
      case "personal_task":
        return "badge-task";
      default:
        return "badge-default";
    }
  };

  const getCategoryIcon = (category) => {
    switch (category) {
      case "chat_meeting":
        return "fa-video";
      case "live_stream":
        return "fa-tower-broadcast";
      case "channel_event":
        return "fa-bullhorn";
      case "moment_memory":
        return "fa-camera-retro";
      case "reel_schedule":
        return "fa-film";
      case "personal_task":
        return "fa-check";
      default:
        return "fa-calendar";
    }
  };

  return (
    <div className="social-month-view">
      {/* Day Headers Row */}
      <div className="month-weekday-header">
        {daysOfWeek.map((dow, idx) => (
          <div key={dow} className={`weekday-col-title ${idx >= 5 ? "is-weekend" : ""}`}>
            {dow}
          </div>
        ))}
      </div>

      {/* 7x5 or 7x6 Calendar Grid */}
      <div className="month-grid-cells">
        {calendarCells.map((cell) => {
          const dayEvents = eventsByDate[cell.dateStr] || [];
          const isSelected = selectedDate === cell.dateStr;

          return (
            <div
              key={`${cell.dateStr}_${cell.isPrev ? "prev" : cell.isNext ? "next" : "curr"}`}
              className={`month-cell ${!cell.isCurrentMonth ? "is-outside-month" : ""} ${
                cell.isToday ? "is-today" : ""
              } ${isSelected ? "is-selected-cell" : ""}`}
              onClick={() => onSelectDate(cell.dateStr)}
            >
              <div className="cell-top-bar">
                <span className="cell-day-num">{cell.day}</span>

                {dayEvents.length > 0 && (
                  <span className="cell-event-count" title={`${dayEvents.length} activities`}>
                    {dayEvents.length}
                  </span>
                )}

                <button
                  className="cell-quick-add-btn"
                  title={`Add activity on ${cell.dateStr}`}
                  onClick={(e) => {
                    e.stopPropagation();
                    onQuickAddForDate?.(cell.dateStr);
                  }}
                >
                  <i className="fa-solid fa-plus"></i>
                </button>
              </div>

              {/* Event Mini Pills */}
              <div className="cell-events-list">
                {dayEvents.slice(0, 3).map((evt) => (
                  <div
                    key={evt.id}
                    className={`cell-event-pill ${getCategoryBadgeClass(evt.category)}`}
                    onClick={(e) => {
                      e.stopPropagation();
                      onOpenEventDetail(evt);
                    }}
                    title={`${evt.startTime || ""} ${evt.title}`}
                  >
                    <i className={`fa-solid ${getCategoryIcon(evt.category)} pill-icon`}></i>
                    <span className="pill-text">{evt.title}</span>
                  </div>
                ))}

                {dayEvents.length > 3 && (
                  <div className="cell-more-badge">
                    +{dayEvents.length - 3} more
                  </div>
                )}
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}

export default MonthView;
