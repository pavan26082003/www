import { useState, useEffect, useRef, useMemo, useCallback } from "react";
import {
  EMOJI_CATEGORIES,
  EMOJI_DATA,
  getRecentEmojis,
  addRecentEmoji,
} from "./emojiData.js";
import "./EmojiPicker.css";

// ---------------------------------------------------------------------------
// EmojiPicker
// ---------------------------------------------------------------------------
// Props:
//   isOpen        – boolean: whether the picker is visible
//   onClose       – fn(): called when user closes the picker
//   onEmojiSelect – fn(emoji: string): called after inserting emoji
//   textareaRef   – React ref pointing at the composer textarea
// ---------------------------------------------------------------------------

function EmojiPicker({ isOpen, onClose, onEmojiSelect, textareaRef }) {
  const [searchQuery, setSearchQuery] = useState("");
  const [activeCategory, setActiveCategory] = useState("smileys");
  const [recentEmojis, setRecentEmojis] = useState([]);

  const pickerRef = useRef(null);
  const searchRef = useRef(null);
  const gridRef = useRef(null);

  // --------------------------------------------------
  // Load recent emojis on open
  // --------------------------------------------------
  useEffect(() => {
    if (isOpen) {
      setRecentEmojis(getRecentEmojis());
      setSearchQuery("");
      // Focus search after animation settles — mouse/keyboard devices only, because on
      // phones focusing opens the on-screen keyboard and it covers the emojis
      const hasFinePointer = window.matchMedia?.("(hover: hover) and (pointer: fine)").matches;
      if (hasFinePointer) {
        setTimeout(() => {
          searchRef.current?.focus();
        }, 80);
      }
    }
  }, [isOpen]);

  // --------------------------------------------------
  // Keyboard: Escape closes picker
  // --------------------------------------------------
  useEffect(() => {
    if (!isOpen) return;

    const handleKey = (e) => {
      if (e.key === "Escape") {
        onClose();
        textareaRef?.current?.focus();
      }
    };

    document.addEventListener("keydown", handleKey);
    return () => document.removeEventListener("keydown", handleKey);
  }, [isOpen, onClose, textareaRef]);

  // --------------------------------------------------
  // Search results (memoized, debounce-free for small datasets)
  // --------------------------------------------------
  const searchResults = useMemo(() => {
    if (!searchQuery.trim()) return null;
    const q = searchQuery.toLowerCase().trim();

    const results = [];
    Object.values(EMOJI_DATA).forEach((categoryEmojis) => {
      categoryEmojis.forEach((item) => {
        if (
          item.name.toLowerCase().includes(q) ||
          item.keywords.some((kw) => kw.toLowerCase().includes(q))
        ) {
          results.push(item);
        }
      });
    });

    // Deduplicate (some emojis appear in multiple categories)
    const seen = new Set();
    return results.filter((item) => {
      if (seen.has(item.emoji)) return false;
      seen.add(item.emoji);
      return true;
    });
  }, [searchQuery]);

  // --------------------------------------------------
  // Insert emoji at cursor position in textarea
  // --------------------------------------------------
  const insertEmoji = useCallback(
    (emoji) => {
      const textarea = textareaRef?.current;

      if (textarea) {
        const start = textarea.selectionStart ?? textarea.value.length;
        const end = textarea.selectionEnd ?? textarea.value.length;
        const before = textarea.value.substring(0, start);
        const after = textarea.value.substring(end);
        const newValue = before + emoji + after;

        // Trigger React's onChange by using a native input event
        const nativeInputValueSetter = Object.getOwnPropertyDescriptor(
          window.HTMLTextAreaElement.prototype,
          "value"
        )?.set;
        nativeInputValueSetter?.call(textarea, newValue);
        textarea.dispatchEvent(new Event("input", { bubbles: true }));

        // Restore cursor after emoji
        const newCursor = start + emoji.length;
        requestAnimationFrame(() => {
          textarea.focus();
          textarea.setSelectionRange(newCursor, newCursor);
          // Auto-resize
          textarea.style.height = "auto";
          textarea.style.height = `${Math.min(textarea.scrollHeight, 120)}px`;
        });
      }

      // Update recent emojis
      const updated = addRecentEmoji(emoji);
      setRecentEmojis(updated);

      // Notify parent
      onEmojiSelect?.(emoji);
    },
    [textareaRef, onEmojiSelect]
  );

  // --------------------------------------------------
  // Category scroll: jump to section header
  // --------------------------------------------------
  const scrollToCategory = useCallback((catId) => {
    const el = gridRef.current?.querySelector(`[data-category="${catId}"]`);
    if (el) {
      el.scrollIntoView({ behavior: "smooth", block: "start" });
    }
    setActiveCategory(catId);
  }, []);

  // --------------------------------------------------
  // Track active category on scroll
  // --------------------------------------------------
  useEffect(() => {
    if (!isOpen || searchQuery) return;
    const grid = gridRef.current;
    if (!grid) return;

    const sections = grid.querySelectorAll("[data-category]");
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            setActiveCategory(entry.target.dataset.category);
          }
        });
      },
      { root: grid, threshold: 0.2, rootMargin: "0px 0px -70% 0px" }
    );

    sections.forEach((s) => observer.observe(s));
    return () => observer.disconnect();
  }, [isOpen, searchQuery]);

  // --------------------------------------------------
  // Render
  // --------------------------------------------------
  if (!isOpen) return null;

  const showRecent = recentEmojis.length > 0 && !searchQuery;
  const visibleCategories = showRecent
    ? EMOJI_CATEGORIES
    : EMOJI_CATEGORIES.filter((c) => c.id !== "recent");

  return (
    <div
      className="emoji-picker"
      ref={pickerRef}
      role="dialog"
      aria-modal="true"
      aria-label="Emoji picker"
      onClick={(e) => e.stopPropagation()}
    >
      {/* ---- Search ---- */}
      <div className="ep-search-wrap">
        <span className="ep-search-icon" aria-hidden="true">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
        </span>
        <input
          ref={searchRef}
          type="text"
          className="ep-search-input"
          placeholder="Search emojis…"
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
          aria-label="Search emojis"
          autoComplete="off"
          spellCheck="false"
        />
        {searchQuery && (
          <button
            className="ep-search-clear"
            onClick={() => setSearchQuery("")}
            aria-label="Clear search"
            type="button"
          >
            ×
          </button>
        )}
      </div>

      {/* ---- Category Tabs ---- */}
      {!searchQuery && (
        <div className="ep-category-tabs" role="tablist" aria-label="Emoji categories">
          {visibleCategories.map((cat) => (
            <button
              key={cat.id}
              className={`ep-cat-btn${activeCategory === cat.id ? " active" : ""}`}
              onClick={() => scrollToCategory(cat.id)}
              title={cat.label}
              aria-label={cat.label}
              aria-selected={activeCategory === cat.id}
              role="tab"
              type="button"
            >
              <span className="ep-cat-icon" aria-hidden="true">
                {cat.icon}
              </span>
            </button>
          ))}
        </div>
      )}

      {/* ---- Emoji Grid ---- */}
      <div className="ep-scroll" ref={gridRef}>
        {/* Search results */}
        {searchQuery && (
          <div className="ep-section">
            {searchResults && searchResults.length > 0 ? (
              <div className="ep-grid">
                {searchResults.map((item) => (
                  <EmojiBtn
                    key={item.emoji}
                    item={item}
                    onClick={insertEmoji}
                  />
                ))}
              </div>
            ) : (
              <div className="ep-no-results">
                <span>😶</span>
                <p>No emojis found for "<strong>{searchQuery}</strong>"</p>
              </div>
            )}
          </div>
        )}

        {/* Category sections (when not searching) */}
        {!searchQuery && (
          <>
            {/* Recently used */}
            {showRecent && (
              <div className="ep-section" data-category="recent">
                <div className="ep-section-label">Recently Used</div>
                <div className="ep-grid">
                  {recentEmojis.map((emoji) => (
                    <EmojiBtn
                      key={emoji}
                      item={{ emoji, name: emoji, keywords: [] }}
                      onClick={insertEmoji}
                    />
                  ))}
                </div>
              </div>
            )}

            {/* All categories */}
            {EMOJI_CATEGORIES.filter((c) => c.id !== "recent").map((cat) => {
              const emojis = EMOJI_DATA[cat.id] || [];
              return (
                <div
                  key={cat.id}
                  className="ep-section"
                  data-category={cat.id}
                >
                  <div className="ep-section-label">{cat.label}</div>
                  <div className="ep-grid">
                    {emojis.map((item) => (
                      <EmojiBtn
                        key={item.emoji + item.name}
                        item={item}
                        onClick={insertEmoji}
                      />
                    ))}
                  </div>
                </div>
              );
            })}
          </>
        )}
      </div>
    </div>
  );
}

// ---------------------------------------------------------------------------
// EmojiBtn — single emoji button (memoized for performance)
// ---------------------------------------------------------------------------
const EmojiBtn = ({ item, onClick }) => (
  <button
    className="ep-emoji-btn"
    onClick={() => onClick(item.emoji)}
    aria-label={item.name}
    title={item.name}
    type="button"
  >
    {item.emoji}
  </button>
);

export default EmojiPicker;
