import React, { useState, useRef, useEffect } from 'react';
import { createPortal } from 'react-dom';

/**
 * PostMediaCarousel Component
 * 
 * Recreates the LinkedIn multi-image carousel / document slider:
 * - Horizontal swipe and snap scrolling (left/right)
 * - Dynamic height adapting to image dimensions without awkward cropping
 * - Floating navigation buttons (< and >) with smooth animated transitions
 * - Slide counter badge (e.g. "1 / 4")
 * - Fullscreen expand viewer
 */
export default function PostMediaCarousel({ media = [], authorName = 'GRID Creator', singleUrl = null }) {
  const [currentIndex, setCurrentIndex] = useState(0);
  const [fullscreenIndex, setFullscreenIndex] = useState(null);
  const sliderRef = useRef(null);

  // Normalize media items into a clean array
  const items = Array.isArray(media) && media.length > 0
    ? media
    : singleUrl
      ? [{ id: 0, url: singleUrl, type: 'image', order: 0 }]
      : [];

  if (items.length === 0) return null;

  // Handle scroll tracking for active slide index
  const handleScroll = () => {
    if (!sliderRef.current) return;
    const { scrollLeft, clientWidth } = sliderRef.current;
    if (clientWidth <= 0) return;
    const index = Math.round(scrollLeft / clientWidth);
    if (index !== currentIndex && index >= 0 && index < items.length) {
      setCurrentIndex(index);
    }
  };

  const scrollToSlide = (index) => {
    if (!sliderRef.current) return;
    const target = Math.max(0, Math.min(index, items.length - 1));
    const width = sliderRef.current.clientWidth;
    sliderRef.current.scrollTo({
      left: target * width,
      behavior: 'smooth'
    });
    setCurrentIndex(target);
  };

  // Keyboard navigation for fullscreen lightbox
  useEffect(() => {
    if (fullscreenIndex === null) return;
    const handleKeyDown = (e) => {
      if (e.key === 'Escape') setFullscreenIndex(null);
      if (e.key === 'ArrowRight' && fullscreenIndex < items.length - 1) setFullscreenIndex(prev => prev + 1);
      if (e.key === 'ArrowLeft' && fullscreenIndex > 0) setFullscreenIndex(prev => prev - 1);
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [fullscreenIndex, items.length]);

function isVideoItem(item) {
  if (!item || !item.url) return false;
  return item.type === 'video' || /\.(mp4|webm|mov|mkv)(\?.*)?$/i.test(item.url);
}

  // SINGLE IMAGE / VIDEO MODE
  if (items.length === 1) {
    const item = items[0];
    const isVid = isVideoItem(item);

    return (
      <div className="relative rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-950/5 dark:bg-black/40 my-2.5 flex items-center justify-center group">
        {isVid ? (
          <video
            src={item.url}
            controls
            playsInline
            className="w-full h-auto max-h-[620px] object-contain rounded-2xl bg-black"
          />
        ) : (
          <img
            src={item.url}
            alt={authorName}
            className="w-full h-auto max-h-[580px] object-contain select-none cursor-pointer transition-transform duration-200"
            onClick={() => setFullscreenIndex(0)}
            loading="lazy"
          />
        )}

        {/* Fullscreen Button */}
        {!isVid && (
          <button
            type="button"
            onClick={() => setFullscreenIndex(0)}
            className="absolute bottom-3 right-3 p-2 rounded-xl bg-black/60 hover:bg-black/80 text-white backdrop-blur-md opacity-0 group-hover:opacity-100 transition-opacity shadow-md cursor-pointer"
            title="View Fullscreen"
          >
            <span className="material-symbols-outlined text-[18px] block">fullscreen</span>
          </button>
        )}

        {/* Fullscreen Modal Lightbox */}
        {fullscreenIndex !== null && (
          <FullscreenLightbox
            items={items}
            initialIndex={fullscreenIndex}
            onClose={() => setFullscreenIndex(null)}
          />
        )}
      </div>
    );
  }

  // MULTI-IMAGE / VIDEO CAROUSEL MODE (LinkedIn Style)
  return (
    <div className="relative rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-950/5 dark:bg-black/50 my-2.5 group select-none">
      {/* Top Left Slide Indicator Badge */}
      <div className="absolute top-3 left-3 z-10 flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/65 backdrop-blur-md text-white text-xs font-semibold shadow-md pointer-events-none tracking-wide">
        <span className="material-symbols-outlined text-[14px]">
          {isVideoItem(items[currentIndex]) ? 'videocam' : 'photo_library'}
        </span>
        <span>{currentIndex + 1} / {items.length}</span>
      </div>

      {/* Top Right Quick Fullscreen Expand */}
      <button
        type="button"
        onClick={() => setFullscreenIndex(currentIndex)}
        className="absolute top-3 right-3 z-10 p-1.5 rounded-full bg-black/60 hover:bg-black/85 text-white backdrop-blur-md transition-all shadow-md cursor-pointer opacity-80 hover:opacity-100"
        title="Expand full size"
      >
        <span className="material-symbols-outlined text-[16px] block">fullscreen</span>
      </button>

      {/* Horizontal Snap Scroll Container */}
      <div
        ref={sliderRef}
        onScroll={handleScroll}
        className="w-full flex overflow-x-auto snap-x snap-mandatory scroll-smooth no-scrollbar"
        style={{ scrollbarWidth: 'none', msOverflowStyle: 'none' }}
      >
        {items.map((item, idx) => {
          const isVid = isVideoItem(item);
          return (
            <div
              key={item.id || idx}
              className="w-full shrink-0 snap-start flex items-center justify-center min-h-[300px] max-h-[620px] bg-slate-900/5 dark:bg-black/30 relative"
            >
              {isVid ? (
                <video
                  src={item.url}
                  controls
                  playsInline
                  className="w-full h-auto max-h-[620px] object-contain bg-black"
                />
              ) : (
                <img
                  src={item.url}
                  alt={`${authorName} attachment ${idx + 1}`}
                  className="w-full h-auto max-h-[620px] object-contain transition-transform duration-300 cursor-pointer"
                  onClick={() => setFullscreenIndex(idx)}
                  loading="lazy"
                />
              )}
            </div>
          );
        })}
      </div>

      {/* Floating Left Arrow Navigation */}
      {currentIndex > 0 && (
        <button
          type="button"
          onClick={(e) => {
            e.stopPropagation();
            scrollToSlide(currentIndex - 1);
          }}
          className="absolute left-3 top-1/2 -translate-y-1/2 z-10 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-black/65 hover:bg-black/90 text-white backdrop-blur-md flex items-center justify-center transition-all shadow-lg cursor-pointer hover:scale-105 active:scale-95"
          title="Previous slide"
        >
          <span className="material-symbols-outlined text-[22px] leading-none">chevron_left</span>
        </button>
      )}

      {/* Floating Right Arrow Navigation */}
      {currentIndex < items.length - 1 && (
        <button
          type="button"
          onClick={(e) => {
            e.stopPropagation();
            scrollToSlide(currentIndex + 1);
          }}
          className="absolute right-3 top-1/2 -translate-y-1/2 z-10 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-black/65 hover:bg-black/90 text-white backdrop-blur-md flex items-center justify-center transition-all shadow-lg cursor-pointer hover:scale-105 active:scale-95"
          title="Next slide"
        >
          <span className="material-symbols-outlined text-[22px] leading-none">chevron_right</span>
        </button>
      )}

      {/* Bottom Center Dots Indicator */}
      {items.length > 1 && (
        <div className="absolute bottom-3 inset-x-0 z-10 flex items-center justify-center gap-1.5 pointer-events-none">
          {items.map((_, dotIdx) => (
            <span
              key={dotIdx}
              className={`h-1.5 rounded-full transition-all duration-300 ${
                dotIdx === currentIndex
                  ? 'w-5 bg-white shadow-xs'
                  : 'w-1.5 bg-white/50 backdrop-blur-xs'
              }`}
            />
          ))}
        </div>
      )}

      {/* Fullscreen Modal Lightbox */}
      {fullscreenIndex !== null && (
        <FullscreenLightbox
          items={items}
          initialIndex={fullscreenIndex}
          onClose={() => setFullscreenIndex(null)}
        />
      )}
    </div>
  );
}

/**
 * Portaled Fullscreen Lightbox for high-resolution inspection
 */
function FullscreenLightbox({ items, initialIndex = 0, onClose }) {
  const [active, setActive] = useState(initialIndex);

  useEffect(() => {
    document.body.style.overflow = 'hidden';
    return () => {
      document.body.style.overflow = '';
    };
  }, []);

  return createPortal(
    <div
      className="fixed inset-0 z-[99999] bg-black/95 backdrop-blur-md flex flex-col justify-between animate-fadeIn"
      onClick={onClose}
    >
      {/* Header bar */}
      <div
        className="flex items-center justify-between px-5 py-4 z-10 text-white"
        onClick={(e) => e.stopPropagation()}
      >
        <span className="text-sm font-semibold tracking-wide text-slate-300">
          {active + 1} of {items.length}
        </span>
        <button
          type="button"
          onClick={onClose}
          className="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors cursor-pointer"
          title="Close (Esc)"
        >
          <span className="material-symbols-outlined text-[22px] block">close</span>
        </button>
      </div>

      {/* Center Media Display */}
      <div
        className="flex-1 flex items-center justify-center p-4 min-h-0 relative"
        onClick={(e) => e.stopPropagation()}
      >
        {isVideoItem(items[active]) ? (
          <video
            src={items[active].url}
            controls
            autoPlay
            playsInline
            className="max-h-full max-w-full object-contain rounded-lg shadow-2xl bg-black"
          />
        ) : (
          <img
            src={items[active].url}
            alt={`Fullscreen item ${active + 1}`}
            className="max-h-full max-w-full object-contain select-none"
          />
        )}

        {/* Previous button */}
        {active > 0 && (
          <button
            type="button"
            onClick={() => setActive(prev => prev - 1)}
            className="absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/15 hover:bg-white/25 text-white backdrop-blur-md flex items-center justify-center cursor-pointer transition-all shadow-xl"
          >
            <span className="material-symbols-outlined text-[28px]">chevron_left</span>
          </button>
        )}

        {/* Next button */}
        {active < items.length - 1 && (
          <button
            type="button"
            onClick={() => setActive(prev => prev + 1)}
            className="absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/15 hover:bg-white/25 text-white backdrop-blur-md flex items-center justify-center cursor-pointer transition-all shadow-xl"
          >
            <span className="material-symbols-outlined text-[28px]">chevron_right</span>
          </button>
        )}
      </div>

      {/* Bottom Thumbnail Strip */}
      {items.length > 1 && (
        <div
          className="p-3 bg-black/60 backdrop-blur-md flex items-center justify-center gap-2 overflow-x-auto z-10"
          onClick={(e) => e.stopPropagation()}
        >
          {items.map((item, idx) => (
            <button
              key={item.id || idx}
              type="button"
              onClick={() => setActive(idx)}
              className={`w-14 h-14 rounded-lg overflow-hidden border-2 transition-all shrink-0 cursor-pointer ${
                idx === active ? 'border-blue-500 scale-105' : 'border-transparent opacity-60 hover:opacity-100'
              }`}
            >
              <img
                src={item.url}
                alt={`Thumb ${idx + 1}`}
                className="w-full h-full object-cover"
              />
            </button>
          ))}
        </div>
      )}
    </div>,
    document.body
  );
}
