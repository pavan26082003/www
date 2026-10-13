import React, { useRef, useState, useEffect } from 'react';

/**
 * SuggestedTopics Component
 * Carousel / horizontal snap slider with:
 * - Left & Right edge blur masks that appear dynamically based on scroll position
 * - Dynamic arrow visibility: left arrow is hidden when at start; right arrow is hidden when at end
 * - Smooth track-based sliding with side-by-side cards
 * - Includes #Brand Designer, #Midjourney, #Flora Museum Challenge, #Descript Challenge, #CapCut Design
 */
export default function SuggestedTopics({ 
  topics = [], 
  selectedTopic = null, 
  onSelectTopic,
  onAddTopic
}) {
  const scrollRef = useRef(null);
  const [canScrollLeft, setCanScrollLeft] = useState(false);
  const [canScrollRight, setCanScrollRight] = useState(true);
  const [isCollapsed, setIsCollapsed] = useState(false);
  const [isCreateOpen, setIsCreateOpen] = useState(false);
  const [newTitle, setNewTitle] = useState('');
  const [newDesc, setNewDesc] = useState('');
  const [newIsChallenge, setNewIsChallenge] = useState(false);
  const [newPrize, setNewPrize] = useState('$5,000');
  const [newIcon, setNewIcon] = useState('hub');

  const checkScrollLimits = () => {
    const el = scrollRef.current;
    if (!el) return;
    const atStart = el.scrollLeft <= 8;
    const atEnd = Math.ceil(el.scrollLeft + el.clientWidth) >= el.scrollWidth - 8;
    setCanScrollLeft(!atStart);
    setCanScrollRight(!atEnd);
  };

  useEffect(() => {
    checkScrollLimits();
    const el = scrollRef.current;
    if (!el) return;

    el.addEventListener('scroll', checkScrollLimits, { passive: true });
    window.addEventListener('resize', checkScrollLimits);

    // Re-check after images/fonts settle
    const timer = setTimeout(checkScrollLimits, 300);

    return () => {
      el.removeEventListener('scroll', checkScrollLimits);
      window.removeEventListener('resize', checkScrollLimits);
      clearTimeout(timer);
    };
  }, [topics, isCollapsed]);

  const handleSlideLeft = () => {
    if (!scrollRef.current) return;
    const cardWidth = scrollRef.current.clientWidth / 2;
    scrollRef.current.scrollBy({ left: -(cardWidth + 7), behavior: 'smooth' });
  };

  const handleSlideRight = () => {
    if (!scrollRef.current) return;
    const cardWidth = scrollRef.current.clientWidth / 2;
    scrollRef.current.scrollBy({ left: cardWidth + 7, behavior: 'smooth' });
  };

  const handleCreateSubmit = (e) => {
    e.preventDefault();
    if (!newTitle.trim()) return;

    const topicSlug = newTitle.trim().replace(/^#/, '');
    const cleanId = 'topic-' + Date.now();
    const createdTopic = {
      id: cleanId,
      title: topicSlug,
      icon: newIcon || 'hub',
      description: newDesc.trim() || `Community hub for creators and builders in #${topicSlug}.`,
      stat1Val: newIsChallenge ? newPrize : '1',
      stat1Label: newIsChallenge ? 'Prize' : 'Contributors',
      stat2Val: newIsChallenge ? '30d Left' : '1',
      stat2Label: newIsChallenge ? 'Left' : 'Posts',
      bgImage: 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?w=600&auto=format&fit=crop&q=80',
      avatars: [
        'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80'
      ]
    };

    if (onAddTopic) {
      onAddTopic(createdTopic);
    }
    if (onSelectTopic) {
      onSelectTopic(createdTopic);
    }

    setIsCreateOpen(false);
    setNewTitle('');
    setNewDesc('');
    setNewIsChallenge(false);
  };

  const renderIcon = (topic) => {
    if (topic.iconType === 'flora') {
      return (
        <svg viewBox="0 0 24 24" fill="currentColor" className="w-4 h-4 text-white shrink-0">
          <circle cx="8" cy="8" r="3.5" />
          <circle cx="16" cy="8" r="3.5" />
          <circle cx="8" cy="16" r="3.5" />
          <circle cx="16" cy="16" r="3.5" />
        </svg>
      );
    }
    if (topic.iconType === 'descript') {
      return (
        <svg viewBox="0 0 24 24" fill="none" className="w-4 h-4 shrink-0">
          <rect x="2" y="5" width="10" height="2.5" rx="1.25" fill="#EF4444" />
          <rect x="2" y="9.5" width="14" height="2.5" rx="1.25" fill="#EF4444" />
          <rect x="2" y="14" width="12" height="2.5" rx="1.25" fill="#EF4444" />
          <rect x="2" y="18.5" width="8" height="2.5" rx="1.25" fill="#EF4444" />
        </svg>
      );
    }
    if (topic.iconType === 'capcut') {
      return (
        <svg viewBox="0 0 24 24" fill="currentColor" className="w-4 h-4 text-slate-950 shrink-0">
          <path d="M4 6L11 12L4 18H8L13 13.5L18 18H20L13 12L20 6H18L13 10.5L8 6H4Z" />
        </svg>
      );
    }
    if (topic.icon) {
      return (
        <span className="material-symbols-outlined text-[17px] text-blue-400">
          {topic.icon}
        </span>
      );
    }
    return null;
  };

  return (
    <div className="space-y-2.5 px-3 sm:px-0">
      {/* Header */}
      <div className="flex items-center justify-between px-1">
        <div className="flex items-center gap-2">
          <span className="font-mono uppercase text-[11px] font-bold tracking-wider text-slate-500">
            SUGGESTED TOPICS
          </span>
          {selectedTopic && (
            <span className="px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300 text-[10px] font-medium font-mono">
              Active: #{selectedTopic.title}
            </span>
          )}
        </div>
        <div className="flex items-center gap-1.5">
          <button
            type="button"
            onClick={() => setIsCreateOpen(true)}
            className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-[11px] font-semibold border border-blue-200 dark:border-blue-800 transition-colors cursor-pointer"
            title="Create a new community topic or challenge"
          >
            <span className="material-symbols-outlined text-[14px]">add</span>
            <span>Community</span>
          </button>

          <button
            onClick={() => setIsCollapsed(prev => !prev)}
            className="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-1 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
            type="button"
            title={isCollapsed ? 'Expand topics carousel' : 'Collapse topics carousel'}
            aria-label="Toggle carousel"
          >
            <span className="material-symbols-outlined text-[18px]">
              {isCollapsed ? 'expand_more' : 'expand_less'}
            </span>
          </button>
        </div>
      </div>

      {/* Collapsed view: compact pill strip */}
      {isCollapsed ? (
        <div className="flex items-center gap-1.5 overflow-x-auto scrollbar-none py-1">
          {topics.map((topic) => {
            const isSelected = selectedTopic?.id === topic.id;
            return (
              <button
                key={topic.id}
                type="button"
                onClick={() => onSelectTopic && onSelectTopic(topic)}
                className={`shrink-0 px-3 py-1 rounded-full text-xs font-medium transition-all flex items-center gap-1.5 cursor-pointer border ${
                  isSelected
                    ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                    : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-300'
                }`}
              >
                <span>#{topic.title}</span>
                {topic.stat1Label === 'Prize' && (
                  <span className="text-[10px] px-1 py-0.2 rounded bg-amber-400/20 text-amber-500 font-bold">
                    {topic.stat1Val}
                  </span>
                )}
              </button>
            );
          })}
        </div>
      ) : (
        <div className="relative group">
          {/* Left Arrow Button */}
          {canScrollLeft && (
            <button
              onClick={handleSlideLeft}
              className="absolute -left-3 top-1/2 -translate-y-1/2 z-20 w-8 h-8 bg-white/95 backdrop-blur-xs rounded-full shadow-md border border-slate-200/90 flex items-center justify-center text-slate-700 hover:text-slate-950 hover:bg-white transition-all cursor-pointer hover:scale-105 active:scale-95"
              type="button"
              aria-label="Slide left"
            >
              <span className="material-symbols-outlined text-[18px]">chevron_left</span>
            </button>
          )}

          {/* Right Arrow Button */}
          {canScrollRight && (
            <button
              onClick={handleSlideRight}
              className="absolute -right-3 top-1/2 -translate-y-1/2 z-20 w-8 h-8 bg-white/95 backdrop-blur-xs rounded-full shadow-md border border-slate-200/90 flex items-center justify-center text-slate-700 hover:text-slate-950 hover:bg-white transition-all cursor-pointer hover:scale-105 active:scale-95"
              type="button"
              aria-label="Slide right"
            >
              <span className="material-symbols-outlined text-[18px]">chevron_right</span>
            </button>
          )}

          {/* Scrollable Cards Track */}
          <div
            ref={scrollRef}
            className="flex gap-3.5 overflow-x-auto scroll-smooth scrollbar-none py-1 px-0.5 rounded-2xl"
            style={{
              scrollSnapType: 'x mandatory',
              scrollbarWidth: 'none',
              msOverflowStyle: 'none'
            }}
          >
            {topics.map((topic) => {
              const isLight = topic.isLight || false;
              const isSelected = selectedTopic?.id === topic.id;

              return (
                <div
                  key={topic.id}
                  onClick={() => onSelectTopic && onSelectTopic(topic)}
                  className={`relative h-44 rounded-2xl overflow-hidden p-4 flex flex-col justify-between shadow-xs group cursor-pointer shrink-0 transition-all duration-200 hover:-translate-y-0.5 ${
                    isSelected
                      ? 'ring-2 ring-blue-500 ring-offset-2 ring-offset-slate-900 shadow-md shadow-blue-500/20 scale-[1.01]'
                      : ''
                  } ${
                    isLight
                      ? topic.bgGradient || 'bg-gradient-to-tr from-sky-400 via-cyan-200 to-white text-slate-900'
                      : 'bg-slate-900 text-white'
                  }`}
                  style={{
                    width: 'calc(50% - 7px)',
                    minWidth: '270px',
                    scrollSnapAlign: 'start'
                  }}
                >
                {/* Background image if present */}
                {topic.bgImage ? (
                  <img
                    alt={topic.title}
                    className="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    src={topic.bgImage}
                  />
                ) : null}

                {/* Overlays for dark cards */}
                {!isLight && (
                  <>
                    <div className="absolute inset-0 bg-[radial-gradient(#60a5fa_1px,transparent_1px)] [background-size:10px_10px] opacity-20 pointer-events-none" />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/90 via-black/55 to-black/35 pointer-events-none" />
                  </>
                )}

                {/* Top Row: Icon + #Title + Right Arrow */}
                <div className="relative z-1 flex items-center justify-between gap-2">
                  <div className="flex items-center gap-1.5 min-w-0">
                    <div
                      className={`flex items-center gap-1.5 font-display font-bold text-sm truncate ${
                        isLight ? 'text-slate-900' : 'text-white'
                      }`}
                    >
                      {renderIcon(topic)}
                      <span className={isLight ? 'text-slate-500 font-mono' : 'text-slate-300 font-mono'}>
                        #
                      </span>
                      <span className="truncate">{topic.title}</span>
                    </div>
                    {topic.company && (
                      <span className="shrink-0 px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-300 border border-blue-400/30 text-[9px] font-mono font-medium hidden sm:inline">
                        {topic.company.name}
                      </span>
                    )}
                  </div>
                  <span
                    className={`material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform shrink-0 ${
                      isLight ? 'text-slate-700' : 'text-white/80'
                    }`}
                  >
                    arrow_forward
                  </span>
                </div>

                {/* Bottom Content: Desc + Stats + Avatars */}
                <div className="relative z-1">
                  <p
                    className={`text-xs line-clamp-2 leading-relaxed mb-3 ${
                      isLight ? 'text-slate-700' : 'text-slate-200'
                    }`}
                  >
                    {topic.description}
                  </p>

                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                      <div>
                        <div
                          className={`font-display font-bold text-xs leading-tight ${
                            isLight ? 'text-slate-900' : 'text-white'
                          }`}
                        >
                          {topic.stat1Val || topic.contributors || '$10K'}
                        </div>
                        <div className={`text-[10px] ${isLight ? 'text-slate-500' : 'text-slate-300'}`}>
                          {topic.stat1Label || 'Contributors'}
                        </div>
                      </div>
                      <div>
                        <div
                          className={`font-display font-bold text-xs leading-tight ${
                            isLight ? 'text-slate-900' : 'text-white'
                          }`}
                        >
                          {topic.stat2Val || topic.posts || 'Today'}
                        </div>
                        <div className={`text-[10px] ${isLight ? 'text-slate-500' : 'text-slate-300'}`}>
                          {topic.stat2Label || 'Posts'}
                        </div>
                      </div>
                    </div>

                    {/* Contributor Avatar Stack */}
                    <div className="flex -space-x-1.5">
                      {topic.avatars &&
                        topic.avatars.map((av, idx) => (
                          <img
                            key={idx}
                            alt="Contributor"
                            className={`w-6 h-6 rounded-full object-cover ring-2 ${
                              isLight ? 'ring-white shadow-xs' : 'ring-white/70'
                            }`}
                            src={av}
                          />
                        ))}
                      {topic.extraBadge && (
                        <div className="w-6 h-6 rounded-full bg-blue-600 text-white font-mono text-[9px] font-bold flex items-center justify-center ring-2 ring-slate-800">
                          {topic.extraBadge}
                        </div>
                      )}
                    </div>
                  </div>
                </div>
              </div>
            );
          })}
          </div>
        </div>
      )}

      {/* Create Community / Topic Hub Modal */}
      {isCreateOpen && (
        <div className="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs animate-fadeIn">
          <div className="relative w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden p-6 space-y-4">
            <div className="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
              <div className="flex items-center gap-2">
                <span className="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 material-symbols-outlined text-[20px]">
                  hub
                </span>
                <h3 className="text-base font-bold text-slate-900 dark:text-white font-display">
                  Create Community Topic
                </h3>
              </div>
              <button
                type="button"
                onClick={() => setIsCreateOpen(false)}
                className="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-md cursor-pointer"
              >
                <span className="material-symbols-outlined text-[20px]">close</span>
              </button>
            </div>

            <form onSubmit={handleCreateSubmit} className="space-y-3.5">
              <div>
                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Topic / Skill Name
                </label>
                <div className="relative">
                  <span className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-mono text-sm">#</span>
                  <input
                    type="text"
                    required
                    value={newTitle}
                    onChange={(e) => setNewTitle(e.target.value)}
                    placeholder="e.g. ThreeJS, BrandDesign, WebGL"
                    className="w-full pl-7 pr-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden"
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Description
                </label>
                <textarea
                  rows="2"
                  value={newDesc}
                  onChange={(e) => setNewDesc(e.target.value)}
                  placeholder="What is this community or discipline about?"
                  className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden resize-none"
                />
              </div>

              <div className="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                <div>
                  <div className="text-xs font-semibold text-slate-800 dark:text-slate-200">
                    Host as a Bounty Challenge?
                  </div>
                  <div className="text-[11px] text-slate-500">
                    Add a prize pool for creator submissions
                  </div>
                </div>
                <input
                  type="checkbox"
                  checked={newIsChallenge}
                  onChange={(e) => setNewIsChallenge(e.target.checked)}
                  className="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 cursor-pointer"
                />
              </div>

              {newIsChallenge && (
                <div>
                  <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Prize Pool Amount
                  </label>
                  <input
                    type="text"
                    value={newPrize}
                    onChange={(e) => setNewPrize(e.target.value)}
                    placeholder="e.g. $2,500"
                    className="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden"
                  />
                </div>
              )}

              <div className="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button
                  type="button"
                  onClick={() => setIsCreateOpen(false)}
                  className="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="px-4 py-2 rounded-xl text-xs font-semibold bg-blue-600 hover:bg-blue-500 text-white shadow-xs transition-colors cursor-pointer"
                >
                  Create Community Hub
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
