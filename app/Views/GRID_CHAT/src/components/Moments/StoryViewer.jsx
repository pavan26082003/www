import { useEffect, useState, useRef, useCallback } from "react";
import { formatTimeAgo } from "../../utils/time";
import { getAvatarUrl } from "../../utils/avatar";
import "./StoryViewer.css";

const QUICK_REACTIONS = ["❤️", "🔥", "😂", "👏", "😮", "🎉"];

// A vertical drag longer than this opens or closes the reply sheet
const SWIPE_THRESHOLD = 50;

function StoryViewer({
  momentsList,
  currentMomentIndex,
  onClose,
  onNextMoment,
  onPrevMoment,
  onStoryViewed,
  onDeleteStory,
  onToggleLike,
  onFetchViewers,
  onSendReply,
  onToast,
}) {
  const currentMoment = momentsList[currentMomentIndex];
  const [slideIndex, setSlideIndex] = useState(0);
  const [progress, setProgress] = useState(0);
  const [prevMomentIndex, setPrevMomentIndex] = useState(currentMomentIndex);
  const [isPaused, setIsPaused] = useState(false);
  const [isHolding, setIsHolding] = useState(false);
  const [replyText, setReplyText] = useState("");
  const [replyOpen, setReplyOpen] = useState(false);
  const [confirmDelete, setConfirmDelete] = useState(false);
  const [viewersOpen, setViewersOpen] = useState(false);
  const [viewers, setViewers] = useState(null); // null while loading
  // Likes changed in this session, keyed by status id — the list this viewer was
  // opened with is a snapshot and does not update underneath us.
  const [likeOverrides, setLikeOverrides] = useState({});
  const timerRef = useRef(null);
  const videoRef = useRef(null);
  const DURATION = 5000; // 5s per slide for images and text

  const stories = currentMoment?.stories || [];
  const currentSlide = stories[slideIndex] || {};
  const isOwnStatus = Boolean(currentMoment?.isUser);
  const isVideoSlide = currentSlide.type === "video";

  const override = likeOverrides[currentSlide.realId];
  const liked = override ? override.liked : Boolean(currentSlide.likedByMe);
  const likeCount = override ? override.likeCount : currentSlide.likeCount || 0;

  // Anything that takes over the screen also holds the story still
  const timerPaused = isPaused || isHolding || replyOpen || viewersOpen || confirmDelete;

  const closeReply = useCallback(() => setReplyOpen(false), []);
  const openReply = useCallback(() => {
    if (!isOwnStatus) setReplyOpen(true);
  }, [isOwnStatus]);

  // Reset slide index when switching moments (React recommended pattern: adjust state during render)
  if (currentMomentIndex !== prevMomentIndex) {
    setPrevMomentIndex(currentMomentIndex);
    setSlideIndex(0);
    setProgress(0);
    setConfirmDelete(false);
    setReplyOpen(false);
    setViewersOpen(false);
    // Without these the next contact's story opens permanently frozen
    setIsPaused(false);
    setIsHolding(false);
  }

  // Each status counts as seen the moment it comes up on screen, so a contact's
  // ring only turns grey once every one of their statuses has been watched.
  // The set keeps paging back and forth from reporting the same status twice.
  const reportedRef = useRef(new Set());
  const viewedStoryId = currentSlide.realId;
  useEffect(() => {
    if (viewedStoryId && !reportedRef.current.has(viewedStoryId)) {
      reportedRef.current.add(viewedStoryId);
      onStoryViewed?.(currentMoment, currentSlide);
    }
    // currentSlide is derived from these two, and re-running per object identity
    // would report the same status repeatedly
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [viewedStoryId]);

  // Moving to another status closes the viewers list and drops its stale rows
  useEffect(() => {
    setViewersOpen(false);
    setViewers(null);
  }, [viewedStoryId]);

  const handleNextSlide = useCallback(() => {
    if (slideIndex < stories.length - 1) {
      setSlideIndex((prev) => prev + 1);
      setProgress(0);
    } else {
      // Advance to next user's story or close
      if (currentMomentIndex < momentsList.length - 1) {
        onNextMoment();
      } else {
        onClose();
      }
    }
  }, [slideIndex, stories.length, currentMomentIndex, momentsList.length, onNextMoment, onClose]);

  const handlePrevSlide = useCallback(() => {
    if (slideIndex > 0) {
      setSlideIndex((prev) => prev - 1);
      setProgress(0);
    } else {
      if (currentMomentIndex > 0) {
        onPrevMoment();
      }
    }
  }, [slideIndex, currentMomentIndex, onPrevMoment]);

  // Progress for images and text. A video drives its own progress from the
  // element's playhead below, so the two clocks can never disagree.
  useEffect(() => {
    if (!stories.length || timerPaused || isVideoSlide) return undefined;

    const interval = 50; // update every 50ms
    const step = (interval / DURATION) * 100;

    // The updater stays pure — advancing happens in the effect below, because
    // StrictMode double-invokes updaters and would otherwise skip a slide.
    timerRef.current = setInterval(() => {
      setProgress((prev) => Math.min(100, prev + step));
    }, interval);

    return () => clearInterval(timerRef.current);
  }, [timerPaused, stories.length, isVideoSlide]);

  // Advance once the bar is full, wherever the progress came from
  useEffect(() => {
    if (progress >= 100) handleNextSlide();
  }, [progress, handleNextSlide]);

  // Pause really pauses: without this the video kept playing while the progress
  // bar froze, which is what made the button look dead.
  useEffect(() => {
    const video = videoRef.current;
    if (!video) return;

    if (timerPaused) {
      video.pause();
    } else {
      video.play().catch(() => {
        // Autoplay refused; the user can still use the controls
      });
    }
  }, [timerPaused, viewedStoryId]);

  useEffect(() => {
    const handleKeyDown = (event) => {
      if (event.key === "Escape") {
        if (replyOpen) closeReply();
        else if (viewersOpen) setViewersOpen(false);
        else onClose();
      }
      if (event.key === "ArrowRight") handleNextSlide();
      if (event.key === "ArrowLeft") handlePrevSlide();
    };

    document.addEventListener("keydown", handleKeyDown);
    return () => document.removeEventListener("keydown", handleKeyDown);
  }, [replyOpen, viewersOpen, closeReply, onClose, handleNextSlide, handlePrevSlide]);

  // ---------------------------------------------------------------------
  // Drag up to reply, drag down to dismiss it. Pointer events cover both
  // touch and mouse; a drag must not also register as a tap on the
  // previous/next zones underneath.
  // ---------------------------------------------------------------------
  const dragRef = useRef({ startY: 0, dragging: false });
  const swipedRef = useRef(false);

  const handlePointerDown = (event) => {
    dragRef.current = { startY: event.clientY, dragging: true };
    swipedRef.current = false;
    setIsHolding(true);
    // Capture so the matching pointerup always comes back to this element. A
    // release over the outer prev/next buttons (which sit outside the
    // container) used to strand isHolding at true, permanently freezing the
    // story and making Pause/Resume appear dead.
    try {
      event.currentTarget.setPointerCapture?.(event.pointerId);
    } catch {
      // Capture is best-effort; the window listener below is the safety net
    }
  };

  const handlePointerUp = (event) => {
    setIsHolding(false);
    if (!dragRef.current.dragging) return;
    dragRef.current.dragging = false;

    const deltaY = event.clientY - dragRef.current.startY;
    if (Math.abs(deltaY) < 10) return; // a tap, let the nav zones have it

    swipedRef.current = true;
    if (deltaY <= -SWIPE_THRESHOLD) openReply();
    else if (deltaY >= SWIPE_THRESHOLD) {
      if (replyOpen) closeReply();
      else onClose();
    }
  };

  // Safety net for a pointerup lost outside the window entirely
  useEffect(() => {
    const release = () => {
      dragRef.current.dragging = false;
      setIsHolding(false);
    };
    window.addEventListener("pointerup", release);
    window.addEventListener("pointercancel", release);
    return () => {
      window.removeEventListener("pointerup", release);
      window.removeEventListener("pointercancel", release);
    };
  }, []);

  // A drag that ended on the nav zones must not also page the story
  const guardTap = (action) => () => {
    if (swipedRef.current) {
      swipedRef.current = false;
      return;
    }
    action();
  };

  const handleSendReply = (e) => {
    e?.preventDefault();
    if (!replyText.trim()) return;

    // The parent reports success or failure — this no longer claims delivery
    onSendReply?.(currentMoment, currentSlide, replyText.trim());
    setReplyText("");
    closeReply();
  };

  const handleQuickReaction = (emoji) => {
    onSendReply?.(currentMoment, currentSlide, emoji);
    closeReply();
  };

  const handleToggleLike = async () => {
    if (!currentSlide.realId || !onToggleLike) return;

    // Optimistic: the heart should answer instantly
    const next = { liked: !liked, likeCount: Math.max(0, likeCount + (liked ? -1 : 1)) };
    setLikeOverrides((prev) => ({ ...prev, [currentSlide.realId]: next }));

    const result = await onToggleLike(currentSlide);
    if (result) {
      setLikeOverrides((prev) => ({ ...prev, [currentSlide.realId]: result }));
    } else {
      // Put it back the way it was
      setLikeOverrides((prev) => ({
        ...prev,
        [currentSlide.realId]: { liked, likeCount },
      }));
    }
  };

  const handleOpenViewers = async () => {
    setViewersOpen(true);
    setViewers(null);
    const result = await onFetchViewers?.(currentSlide);
    setViewers(result?.viewers || []);
  };

  if (!currentMoment || !stories.length) return null;

  const viewCount = currentSlide.viewCount || 0;

  return (
    <div className="story-viewer-overlay">
      {/* Background backdrop blur */}
      <div className="story-backdrop" onClick={onClose}></div>

      {/* Main Story Container */}
      <div
        className="story-container"
        onPointerDown={handlePointerDown}
        onPointerUp={handlePointerUp}
        // Releasing or leaving outside the container must not leave the story frozen
        onPointerCancel={() => {
          setIsHolding(false);
          dragRef.current.dragging = false;
        }}
        onPointerLeave={() => {
          setIsHolding(false);
          dragRef.current.dragging = false;
        }}
      >
        {/* Top Progress Bars */}
        <div className="story-progress-row">
          {stories.map((s, idx) => {
            let width = "0%";
            if (idx < slideIndex) width = "100%";
            else if (idx === slideIndex) width = `${progress}%`;

            return (
              <div key={s.id || idx} className="progress-bar-bg">
                <div
                  className="progress-bar-fill"
                  style={{ width }}
                ></div>
              </div>
            );
          })}
        </div>

        {/* Story Header — kept out of the drag gesture, otherwise pressing
            Pause and moving would close the viewer instead of pausing */}
        <div className="story-header" onPointerDown={(event) => event.stopPropagation()}>
          <div className="story-author-info">
            <div className="story-avatar-wrap">
              <img
                src={getAvatarUrl(currentMoment.userAvatar, 80, currentMoment.displayName || currentMoment.userName)}
                alt={currentMoment.userName}
              />
            </div>
            <div className="story-author-details">
              <h4 className="story-author-name">{currentMoment.userName}</h4>
              <span className="story-timestamp">
                {formatTimeAgo(currentSlide.createdAt || currentMoment.updatedAt)}
              </span>
            </div>
          </div>

          <div className="story-header-actions">
            {currentSlide.media && currentMoment.canDownload && (
              <a
                className="story-action-icon-btn"
                href={`${currentSlide.media}?download=1`}
                download
                title="Download"
                onClick={(event) => event.stopPropagation()}
              >
                <i className="fa-solid fa-download"></i>
              </a>
            )}

            {currentSlide.isMine && onDeleteStory && (
              <button
                className="story-action-icon-btn danger"
                onClick={() => setConfirmDelete(true)}
                title="Delete this status"
              >
                <i className="fa-solid fa-trash-can"></i>
              </button>
            )}

            <button
              className="story-action-icon-btn"
              onClick={() => setIsPaused((p) => !p)}
              title={isPaused ? "Resume" : "Pause"}
            >
              <i
                className={`fa-solid ${isPaused ? "fa-play" : "fa-pause"}`}
              ></i>
            </button>

            <button
              className="story-action-icon-btn"
              onClick={onClose}
              title="Close"
            >
              <i className="fa-solid fa-xmark"></i>
            </button>
          </div>
        </div>

        {confirmDelete && (
          <div className="story-delete-confirm" onPointerDown={(e) => e.stopPropagation()}>
            <p>Delete this status?</p>
            <div className="story-delete-confirm-actions">
              <button type="button" onClick={() => setConfirmDelete(false)}>
                Cancel
              </button>
              <button
                type="button"
                className="danger"
                onClick={() => {
                  setConfirmDelete(false);
                  onDeleteStory(currentSlide);
                }}
              >
                Delete
              </button>
            </div>
          </div>
        )}

        {/* Paused is otherwise almost invisible on a text or image status */}
        {isPaused && (
          <div className="story-paused-badge" aria-live="polite">
            <i className="fa-solid fa-pause"></i> Paused
          </div>
        )}

        {/* Story Content Viewport */}
        <div className={`story-content-viewport ${isPaused ? "is-paused" : ""}`}>
          {/* Navigation Tap Zones */}
          <div
            className="story-nav-zone left-zone"
            onClick={guardTap(handlePrevSlide)}
            title="Previous"
          ></div>
          <div
            className="story-nav-zone right-zone"
            onClick={guardTap(handleNextSlide)}
            title="Next"
          ></div>

          {currentSlide.type === "image" ? (
            <div className="story-image-wrap">
              <img
                src={currentSlide.media}
                alt={currentSlide.caption || "Story"}
                className="story-media-image"
              />
              {currentSlide.caption && (
                <div className="story-caption-overlay">
                  <p>{currentSlide.caption}</p>
                </div>
              )}
            </div>
          ) : currentSlide.type === "video" ? (
            <div className="story-image-wrap">
              <video
                ref={videoRef}
                src={currentSlide.media}
                className="story-media-video"
                autoPlay
                muted
                playsInline
                // The bar follows the real playhead, so a long video plays in
                // full instead of being cut off at a flat five seconds
                onTimeUpdate={(event) => {
                  const { currentTime, duration } = event.currentTarget;
                  if (duration > 0) setProgress(Math.min(100, (currentTime / duration) * 100));
                }}
                onEnded={() => {
                  if (!timerPaused) handleNextSlide();
                }}
              />
              {currentSlide.caption && (
                <div className="story-caption-overlay">
                  <p>{currentSlide.caption}</p>
                </div>
              )}
            </div>
          ) : (
            <div
              className="story-gradient-wrap"
              style={{ background: currentSlide.bg || "linear-gradient(135deg, #00a884, #059669)" }}
            >
              <div className="story-text-content">
                <p className="story-quote-text">{currentSlide.text}</p>
                {currentSlide.caption && (
                  <span className="story-sub-caption">
                    {currentSlide.caption}
                  </span>
                )}
              </div>
            </div>
          )}
        </div>

        {/* Your own status: who has seen it */}
        {isOwnStatus && (
          <div className="story-footer story-owner-footer" onPointerDown={(e) => e.stopPropagation()}>
            <button type="button" className="story-views-btn" onClick={handleOpenViewers}>
              <i className="fa-solid fa-eye"></i>
              <span>{viewCount === 0 ? "No views yet" : `Viewed by ${viewCount}`}</span>
              {likeCount > 0 && (
                <span className="story-views-likes">
                  <i className="fa-solid fa-heart"></i> {likeCount}
                </span>
              )}
            </button>
          </div>
        )}

        {/* Someone else's status: like it, or drag up to reply */}
        {!isOwnStatus && (
          <div className={`story-footer ${replyOpen ? "is-expanded" : ""}`}>
            {!replyOpen ? (
              <div className="story-footer-collapsed">
                <button
                  type="button"
                  className="story-reply-handle"
                  onClick={openReply}
                  title="Reply"
                >
                  <i className="fa-solid fa-chevron-up"></i>
                  <span>Swipe up to reply</span>
                </button>

                <button
                  type="button"
                  className={`story-like-btn ${liked ? "is-liked" : ""}`}
                  onClick={handleToggleLike}
                  onPointerDown={(e) => e.stopPropagation()}
                  title={liked ? "Remove like" : "Like this status"}
                  aria-pressed={liked}
                >
                  <i className={`${liked ? "fa-solid" : "fa-regular"} fa-heart`}></i>
                  {likeCount > 0 && <span>{likeCount}</span>}
                </button>
              </div>
            ) : (
              <div className="story-reply-sheet" onPointerDown={(e) => e.stopPropagation()}>
                <button
                  type="button"
                  className="story-sheet-grab"
                  onClick={closeReply}
                  aria-label="Close reply"
                >
                  <span></span>
                </button>

                <div className="story-quick-reactions">
                  {QUICK_REACTIONS.map((emoji) => (
                    <button
                      key={emoji}
                      type="button"
                      className="quick-reaction-btn"
                      onClick={() => handleQuickReaction(emoji)}
                      title={`Send ${emoji}`}
                    >
                      {emoji}
                    </button>
                  ))}
                </div>

                <form className="story-reply-form" onSubmit={handleSendReply}>
                  <input
                    type="text"
                    placeholder={`Reply to ${currentMoment.userName}...`}
                    value={replyText}
                    onChange={(e) => setReplyText(e.target.value)}
                    className="story-reply-input"
                    autoFocus
                  />
                  {replyText.trim() && (
                    <button type="submit" className="story-send-btn" title="Send reply">
                      <i className="fa-solid fa-paper-plane"></i>
                    </button>
                  )}
                </form>
              </div>
            )}
          </div>
        )}

        {/* Viewers list for your own status */}
        {viewersOpen && (
          <div className="story-viewers-sheet" onPointerDown={(e) => e.stopPropagation()}>
            <button
              type="button"
              className="story-sheet-grab"
              onClick={() => setViewersOpen(false)}
              aria-label="Close viewers"
            >
              <span></span>
            </button>

            <h4 className="story-viewers-title">
              <i className="fa-solid fa-eye"></i>
              {viewers === null
                ? "Loading..."
                : viewers.length === 0
                ? "No one has seen this yet"
                : `Viewed by ${viewers.length}`}
            </h4>

            <ul className="story-viewers-list">
              {(viewers || []).map((viewer) => (
                <li key={viewer.id}>
                  <img src={getAvatarUrl(viewer.avatar, 64, viewer.name)} alt="" />
                  <span className="story-viewer-name">{viewer.name}</span>
                  {viewer.liked && <i className="fa-solid fa-heart story-viewer-liked"></i>}
                  <span className="story-viewer-time">{formatTimeAgo(viewer.viewedAt)}</span>
                </li>
              ))}
            </ul>
          </div>
        )}
      </div>

      {/* External Prev/Next Navigation Controls for Desktop */}
      {currentMomentIndex > 0 && (
        <button
          className="story-outer-nav-btn prev-btn"
          onClick={onPrevMoment}
          title="Previous Moment"
        >
          <i className="fa-solid fa-chevron-left"></i>
        </button>
      )}

      {currentMomentIndex < momentsList.length - 1 && (
        <button
          className="story-outer-nav-btn next-btn"
          onClick={onNextMoment}
          title="Next Moment"
        >
          <i className="fa-solid fa-chevron-right"></i>
        </button>
      )}
    </div>
  );
}

export default StoryViewer;
