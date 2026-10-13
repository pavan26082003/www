export const formatDuration = (totalSeconds) => {
  const safeSeconds = Number.isFinite(totalSeconds) && totalSeconds > 0 ? totalSeconds : 0;
  const minutes = Math.floor(safeSeconds / 60);
  const seconds = Math.floor(safeSeconds % 60);
  return `${minutes}:${String(seconds).padStart(2, "0")}`;
};

// Server times are ISO-8601 UTC; everything below formats in the viewer's local time.

const toDate = (iso) => {
  if (!iso) return null;
  const date = new Date(iso);
  return Number.isNaN(date.getTime()) ? null : date;
};

const startOfDay = (date) => new Date(date.getFullYear(), date.getMonth(), date.getDate());

const daysAgo = (date, now = new Date()) =>
  Math.round((startOfDay(now) - startOfDay(date)) / 86400000);

// "10:42 am"
export const formatMessageTime = (iso) => {
  const date = toDate(iso);
  return date ? date.toLocaleTimeString([], { hour: "numeric", minute: "2-digit" }) : "";
};

// Chat list: time today, "Yesterday", weekday this week, otherwise a short date
export const formatChatListTime = (iso) => {
  const date = toDate(iso);
  if (!date) return "";
  const days = daysAgo(date);
  if (days <= 0) return formatMessageTime(iso);
  if (days === 1) return "Yesterday";
  if (days < 7) return date.toLocaleDateString([], { weekday: "short" });
  return date.toLocaleDateString([], { day: "numeric", month: "short" });
};

// Date divider label inside a conversation
export const formatDayLabel = (iso) => {
  const date = toDate(iso);
  if (!date) return "";
  const days = daysAgo(date);
  if (days <= 0) return "Today";
  if (days === 1) return "Yesterday";
  return date.toLocaleDateString([], { weekday: "short", day: "numeric", month: "short", year: "numeric" });
};

// Status/Moments ages: "Just now", "15m ago", "3h ago", "2d ago"
export const formatTimeAgo = (iso) => {
  const date = toDate(iso);
  if (!date) return "";

  const seconds = Math.max(0, Math.floor((Date.now() - date.getTime()) / 1000));
  if (seconds < 60) return "Just now";
  if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`;
  if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`;
  return `${Math.floor(seconds / 86400)}d ago`;
};

// Local calendar day key, e.g. "2026-09-17"
export const localDayKey = (iso) => {
  const date = toDate(iso);
  if (!date) return "";
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}-${String(date.getDate()).padStart(2, "0")}`;
};
