const COUNTER_TYPES = [
  { type: "video", icon: "fa-solid fa-video", label: "video" },
  { type: "image", icon: "fa-solid fa-image", label: "photo" },
  { type: "text", icon: "fa-solid fa-note-sticky", label: "text" },
];

// Each pill filters that contact's status viewer down to just its own type —
// clicking a pill must never also trigger the card's "open all statuses" click.
function StatusMediaCounters({ video = 0, image = 0, text = 0, onSelectType }) {
  const counts = { video, image, text };

  return (
    <div className="status-media-counters" onClick={(event) => event.stopPropagation()}>
      {COUNTER_TYPES.map(({ type, icon, label }) => {
        const count = counts[type];
        return (
          <button
            key={type}
            type="button"
            className="status-counter-pill"
            disabled={count === 0}
            onClick={(event) => {
              event.stopPropagation();
              onSelectType?.(type);
            }}
            title={`${count} ${label} update${count === 1 ? "" : "s"}`}
          >
            <i className={icon}></i>
            {count}
          </button>
        );
      })}
    </div>
  );
}

export default StatusMediaCounters;
