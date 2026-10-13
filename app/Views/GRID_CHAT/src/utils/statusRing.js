// Builds a conic-gradient background representing N equal status-ring segments
// (one per status item), separated by small gaps — e.g. Instagram/WhatsApp style.
// A single status renders as one unbroken ring for visual continuity.
export const buildStatusRingBackground = (count, color) => {
  const total = Math.max(1, count || 1);

  if (total === 1) {
    return color;
  }

  const gapDeg = total > 6 ? 4 : 8;
  const segmentDeg = 360 / total;
  const stops = [];

  for (let i = 0; i < total; i++) {
    const segStart = i * segmentDeg;
    const segEnd = (i + 1) * segmentDeg;
    const colorStart = segStart + gapDeg / 2;
    const colorEnd = segEnd - gapDeg / 2;

    stops.push(`transparent ${segStart}deg ${colorStart}deg`);
    stops.push(`${color} ${colorStart}deg ${colorEnd}deg`);
    stops.push(`transparent ${colorEnd}deg ${segEnd}deg`);
  }

  return `conic-gradient(${stops.join(", ")})`;
};

export const getStatusCounts = (statuses = []) => {
  const counts = { video: 0, image: 0, text: 0 };
  statuses.forEach((status) => {
    if (status.type === "video") counts.video += 1;
    else if (status.type === "image") counts.image += 1;
    else counts.text += 1;
  });
  return counts;
};
