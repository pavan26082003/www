/**
 * Avatars: a real photo when there is one, otherwise the person's initials.
 *
 * Initials rule:
 *   "Anil"            → A
 *   "Anil Kumar"      → AK
 *   "Anil Kumar Teja" → AT  (first word + last word)
 */

const AVATAR_COLORS = [
  "#0ea5e9", "#8b5cf6", "#10b981", "#f59e0b", "#ef4444",
  "#ec4899", "#14b8a6", "#6366f1", "#f97316", "#0891b2",
];

const firstLetter = (word) => (Array.from(word)[0] || "").toUpperCase();

export function getInitials(name) {
  let text = String(name ?? "").trim();
  // Email used as a name → only the part before "@"
  if (text.includes("@")) {
    text = text.split("@")[0];
  }

  const words = text.split(/[\s._-]+/).filter(Boolean);
  if (words.length === 0) return "?";
  if (words.length === 1) return firstLetter(words[0]);
  return firstLetter(words[0]) + firstLetter(words[words.length - 1]);
}

// Same name → same colour everywhere
const colorForName = (name) => {
  let hash = 0;
  for (const char of String(name ?? "")) {
    hash = (hash * 31 + char.codePointAt(0)) >>> 0;
  }
  return AVATAR_COLORS[hash % AVATAR_COLORS.length];
};

const escapeXml = (text) =>
  text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");

/** Square SVG (displayed round by CSS) with white initials on a colour from the name. */
export function initialsAvatarDataUrl(name, size = 120) {
  const initials = getInitials(name);
  const fontSize = Math.round(size * (initials.length > 1 ? 0.4 : 0.46));
  const svg =
    `<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 ${size} ${size}">` +
    `<rect width="${size}" height="${size}" fill="${colorForName(name)}"/>` +
    `<text x="50%" y="50%" dy=".35em" text-anchor="middle" fill="#ffffff" ` +
    `font-family="Segoe UI, Roboto, Helvetica, Arial, sans-serif" font-size="${fontSize}" font-weight="600">` +
    `${escapeXml(initials)}</text></svg>`;
  return `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}`;
}

const isImageUrl = (value) =>
  value.startsWith("http://") ||
  value.startsWith("https://") ||
  value.startsWith("data:") ||
  value.startsWith("blob:") ||
  value.startsWith("/");

/**
 * Image source for an avatar.
 * @param avatar  photo URL / data URL / blob URL, or empty when the person has no photo
 * @param size    pixel size for the generated initials image
 * @param name    the person's (or group's) name, used for the initials
 */
export function getAvatarUrl(avatar, size = 120, name = "") {
  const value = typeof avatar === "string" ? avatar.trim() : "";
  return value && isImageUrl(value) ? value : initialsAvatarDataUrl(name, size);
}
