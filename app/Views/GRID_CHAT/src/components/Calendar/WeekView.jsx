import { useMemo } from "react";

const HOURS = [
  "08:00", "09:00", "10:00", "11:00", "12:00",
  "13:00", "14:00", "15:00", "16:00", "17:00",
  "18:00", "19:00", "20:00", "21:00", "22:00"
];

function WeekView({
  selectedDate,
  events = [],
  onSelectDate,
  onOpenEventDetail,
  onQuickAddForDateAndTime,
}) {
  // Compute 7 days around selectedDate (Monday to Sunday)
  const weekDays = useMemo(() => {
    const baseDate = new Date(selectedDate || "2026-09-12");
    const dayOfWeek = baseDate.getDay(); // 0 = Sun
    const diffToMonday = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;

    const monday = new Date(baseDate);
    monday.setDate(baseDate.getDate() + diffToMonday);

    const days = [];
    for (let i = 0; i < 7; i++) {
      const d = new Date(monday);
      d.setDate(monday.getDate() + i);
      const dateStr = d.toISOString().slice(0, 10);
      days.push({
        name: d.toLocaleDateString("en-US", { weekday: "short" }),
        dayNum: d.getDate(),
        dateStr,
        isToday: new Date().toISOString().slice(0, 10) === dateStr,
        isSelected: selectedDate === dateStr,
      });
    }
    return days;
  }, [selectedDate]);

  const eventsByDateAndHour = useMemo(() => {
    const map = {};
    events.forEach((evt) => {
      const dateKey = evt.date;
      const hourKey = evt.startTime ? evt.startTime.slice(0, 2) + ":00" : "09:00";
      const key = `${dateKey}_${hourKey}`;
      if (!map[key]) map[key] = [];
      map[key].push(evt);
    });
    return map;
  }, [events]);

  const getEventCategoryColor = (cat) => {
    switch (cat) {
      case "chat_meeting":
        return "var(--color-primary, #f59e0b)";
      case "live_stream":
        return "#ef4444";
      case "channel_event":
        return "#ea580c";
      case "moment_memory":
        return "#f59e0b";
      case "reel_schedule":
        return "#ec4899";
      default:
        return "#10b981";
    }
  };

  return (
    <div className="social-week-view">
      {/* Week Header Row */}
      <div className="week-header-row">
        <div className="time-gutter-header">Time</div>
        {weekDays.map((d) => (
          <div
            key={d.dateStr}
            className={`week-day-header ${d.isSelected ? "is-selected-day" : ""} ${d.isToday ? "is-today" : ""}`}
            onClick={() => onSelectDate(d.dateStr)}
          >
            <span className="week-day-name">{d.name}</span>
            <span className="week-day-num">{d.dayNum}</span>
          </div>
        ))}
      </div>

      {/* Hourly Grid Rows */}
      <div className="week-grid-body">
        {HOURS.map((hour) => (
          <div key={hour} className="week-hour-row">
            <div className="hour-label">{hour}</div>

            {weekDays.map((d) => {
              const key = `${d.dateStr}_${hour}`;
              const slotEvents = eventsByDateAndHour[key] || [];

              return (
                <div
                  key={d.dateStr}
                  className="week-hour-cell"
                  onClick={() => onQuickAddForDateAndTime?.(d.dateStr, hour)}
                  title={`Click to schedule activity at ${hour} on ${d.dateStr}`}
                >
                  {slotEvents.map((evt) => (
                    <div
                      key={evt.id}
                      className="week-event-card"
                      style={{ borderLeftColor: getEventCategoryColor(evt.category) }}
                      onClick={(e) => {
                        e.stopPropagation();
                        onOpenEventDetail(evt);
                      }}
                    >
                      <div className="week-event-time">
                        {evt.startTime} - {evt.endTime}
                      </div>
                      <div className="week-event-title">{evt.title}</div>
                      {evt.contactName && (
                        <div className="week-event-person">
                          <i className="fa-solid fa-user"></i> {evt.contactName}
                        </div>
                      )}
                    </div>
                  ))}
                </div>
              );
            })}
          </div>
        ))}
      </div>
    </div>
  );
}

export default WeekView;
