import React from 'react';

/**
 * TopicHubBanner Component (Contra-Inspired)
 * Renders a focused, high-contrast Hero Hub banner when a user clicks into a Suggested Topic or Challenge.
 * ONLY displayed when activeTopic is selected.
 */
export default function TopicHubBanner({ topic, onClear, onPostToTopic }) {
  if (!topic) return null;

  const isChallenge = topic.stat1Label === 'Prize' || ['flora', 'descript', 'capcut'].includes(topic.iconType);
  const cleanTag = '#' + topic.title.replace(/\s+/g, '');

  const renderIcon = () => {
    if (topic.iconType === 'flora') {
      return (
        <svg viewBox="0 0 24 24" fill="currentColor" className="w-5 h-5 text-emerald-400 shrink-0">
          <circle cx="8" cy="8" r="3.5" />
          <circle cx="16" cy="8" r="3.5" />
          <circle cx="8" cy="16" r="3.5" />
          <circle cx="16" cy="16" r="3.5" />
        </svg>
      );
    }
    if (topic.iconType === 'descript') {
      return (
        <svg viewBox="0 0 24 24" fill="none" className="w-5 h-5 shrink-0">
          <rect x="2" y="5" width="10" height="2.5" rx="1.25" fill="#EF4444" />
          <rect x="2" y="9.5" width="14" height="2.5" rx="1.25" fill="#EF4444" />
          <rect x="2" y="14" width="12" height="2.5" rx="1.25" fill="#EF4444" />
          <rect x="2" y="18.5" width="8" height="2.5" rx="1.25" fill="#EF4444" />
        </svg>
      );
    }
    if (topic.iconType === 'capcut') {
      return (
        <svg viewBox="0 0 24 24" fill="currentColor" className="w-5 h-5 text-sky-400 shrink-0">
          <path d="M4 6L11 12L4 18H8L13 13.5L18 18H20L13 12L20 6H18L13 10.5L8 6H4Z" />
        </svg>
      );
    }
    if (topic.icon) {
      return (
        <span className="material-symbols-outlined text-[22px] text-blue-400">
          {topic.icon}
        </span>
      );
    }
    return (
      <span className="material-symbols-outlined text-[20px] text-blue-400">
        tag
      </span>
    );
  };

  return (
    <div className="relative rounded-2xl overflow-hidden border border-slate-200/90 dark:border-slate-800 bg-slate-900 text-white shadow-lg p-5 transition-all animate-fadeIn space-y-4">
      {/* Background ambient lighting and mesh overlay */}
      <div className="absolute inset-0 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:12px_12px] opacity-15 pointer-events-none" />
      <div className="absolute top-0 right-0 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none" />

      {/* Top Action Nav: Back Button + Active Filter Tag */}
      <div className="relative z-10 flex items-center justify-between border-b border-white/10 pb-3">
        <button
          type="button"
          onClick={onClear}
          className="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-300 hover:text-white transition-colors cursor-pointer group"
        >
          <span className="material-symbols-outlined text-[18px] group-hover:-translate-x-0.5 transition-transform">
            arrow_back
          </span>
          <span>All Updates</span>
        </button>

        <div className="flex items-center gap-2">
          <span className="text-[11px] font-mono text-slate-400 hidden sm:inline">
            Filtering feed by:
          </span>
          <button
            type="button"
            onClick={onClear}
            className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-400/30 text-xs font-medium hover:bg-blue-500/30 transition-colors cursor-pointer"
            title="Remove topic filter"
          >
            <span>{cleanTag}</span>
            <span className="material-symbols-outlined text-[14px]">close</span>
          </button>
        </div>
      </div>

      {/* Main Topic Header */}
      <div className="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div className="space-y-1.5 max-w-xl">
          <div className="flex flex-wrap items-center gap-2">
            <div className="p-1.5 rounded-lg bg-white/10 backdrop-blur-xs flex items-center justify-center">
              {renderIcon()}
            </div>
            <h2 className="text-xl sm:text-2xl font-display font-bold tracking-tight text-white flex items-center gap-1.5">
              <span>{topic.title}</span>
            </h2>
            {isChallenge && (
              <span className="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[10px] font-mono font-bold uppercase tracking-wider">
                Challenge
              </span>
            )}
            {topic.company && (
              <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-400/30 text-[11px] font-medium font-mono">
                {topic.company.logo && (
                  <img src={topic.company.logo} alt="" className="w-3.5 h-3.5 rounded-full object-cover" />
                )}
                <span>{topic.company.name}</span>
                <span className="material-symbols-outlined text-[13px] text-blue-400">verified</span>
              </span>
            )}
          </div>
          <p className="text-sm text-slate-300 leading-relaxed">
            {topic.description}
          </p>
          {topic.prize_details && (
            <div className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-400/10 border border-amber-400/20 text-amber-300 text-xs font-mono">
              <span className="material-symbols-outlined text-[15px] text-amber-400">workspace_premium</span>
              <span>{topic.prize_details}</span>
            </div>
          )}
        </div>

        {/* CTA Button */}
        <div className="shrink-0 flex items-center gap-2">
          <button
            type="button"
            onClick={() => onPostToTopic && onPostToTopic(cleanTag)}
            className="w-full sm:w-auto px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-md hover:shadow-blue-500/25 transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-95"
          >
            <span className="material-symbols-outlined text-[16px]">
              {isChallenge ? 'military_tech' : 'edit_square'}
            </span>
            <span>{isChallenge ? 'Submit Challenge Entry' : `Post in ${cleanTag}`}</span>
          </button>
        </div>
      </div>

      {/* Bottom Info Bar: Metrics & Contributor Avatars */}
      <div className="relative z-10 pt-2 flex flex-wrap items-center justify-between gap-3 border-t border-white/5 text-xs text-slate-300">
        <div className="flex items-center gap-4">
          <div className="flex items-center gap-1.5">
            <span className="font-display font-bold text-sm text-white">
              {topic.stat1Val || '36K'}
            </span>
            <span className="text-[11px] text-slate-400">
              {topic.stat1Label || 'Contributors'}
            </span>
          </div>
          <div className="h-3 w-px bg-white/15" />
          <div className="flex items-center gap-1.5">
            <span className="font-display font-bold text-sm text-white">
              {topic.stat2Val || '24K'}
            </span>
            <span className="text-[11px] text-slate-400">
              {topic.stat2Label || 'Posts'}
            </span>
          </div>
        </div>

        {/* Overlapping Contributor Avatars */}
        {topic.avatars && topic.avatars.length > 0 && (
          <div className="flex items-center gap-2">
            <span className="text-[11px] text-slate-400 hidden sm:inline">Active creators:</span>
            <div className="flex -space-x-2">
              {topic.avatars.map((av, idx) => (
                <img
                  key={idx}
                  src={av}
                  alt="Creator"
                  className="w-6 h-6 rounded-full object-cover ring-2 ring-slate-900 shadow-sm"
                />
              ))}
              {topic.extraBadge && (
                <div className="w-6 h-6 rounded-full bg-blue-600 text-white font-mono text-[9px] font-bold flex items-center justify-center ring-2 ring-slate-900">
                  {topic.extraBadge}
                </div>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
