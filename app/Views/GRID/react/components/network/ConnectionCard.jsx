import React, { useState } from 'react';

/**
 * ConnectionCard Component
 * Displays a single verified connection card with presence telemetry,
 * mutual count, and quick interaction buttons (Message, Visiting Card, Connected dropdown).
 */
export default function ConnectionCard({
    connection,
    onMessage,
    onOpenVisitingCard,
    onRemove,
    onToast
}) {
    const [isMenuOpen, setIsMenuOpen] = useState(false);

    const handleVisitingCardClick = () => {
        if (onOpenVisitingCard) {
            onOpenVisitingCard(connection);
        } else {
            // Trigger global visiting card event
            const event = new CustomEvent('grid:open-visiting-card', {
                detail: {
                    name: connection.name,
                    headline: connection.headline,
                    avatar: connection.avatar,
                    cardId: connection.visitingCard?.cardId || '#GRID-VC-VERIFIED',
                    disciplineBadge: connection.visitingCard?.disciplineBadge || 'Verified Member'
                }
            });
            window.dispatchEvent(event);
        }
    };

    return (
        <div className="group relative bg-white/90 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm hover:shadow-md hover:border-blue-300 dark:hover:border-blue-700/60 transition-all flex flex-col justify-between">
            {/* Top Row: Avatar + Online Badge + Actions Menu */}
            <div>
                <div className="flex items-start justify-between mb-3.5">
                    <div className="relative">
                        <img
                            src={connection.avatar}
                            alt={connection.name}
                            className="w-14 h-14 rounded-2xl object-cover border-2 border-white dark:border-slate-800 shadow-sm group-hover:scale-105 transition-transform"
                            loading="lazy"
                        />
                        {/* Live Online Presence Ring */}
                        {connection.isOnline && (
                            <span
                                className="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full shadow-xs"
                                title="Active Now on GRID"
                            />
                        )}
                    </div>

                    {/* Overflow Context Menu Button */}
                    <div className="relative">
                        <button
                            type="button"
                            onClick={() => setIsMenuOpen(!isMenuOpen)}
                            className="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                            aria-label="More options"
                        >
                            <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                            </svg>
                        </button>

                        {isMenuOpen && (
                            <div className="absolute right-0 mt-1 w-44 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-100 dark:border-slate-700 py-1.5 z-20 animate-fadeIn">
                                <button
                                    type="button"
                                    onClick={() => {
                                        setIsMenuOpen(false);
                                        handleVisitingCardClick();
                                    }}
                                    className="w-full px-3.5 py-2 text-left text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2"
                                >
                                    <span>📇 View Visiting Card</span>
                                </button>
                                <button
                                    type="button"
                                    onClick={() => {
                                        setIsMenuOpen(false);
                                        onToast && onToast(`Endorsement recorded for ${connection.name}! 🌟`);
                                    }}
                                    className="w-full px-3.5 py-2 text-left text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2"
                                >
                                    <span>⭐ Endorse Skills</span>
                                </button>
                                <div className="border-t border-slate-100 dark:border-slate-700 my-1" />
                                <button
                                    type="button"
                                    onClick={() => {
                                        setIsMenuOpen(false);
                                        onRemove && onRemove(connection.id);
                                    }}
                                    className="w-full px-3.5 py-2 text-left text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 flex items-center gap-2"
                                >
                                    <span>Remove Connection</span>
                                </button>
                            </div>
                        )}
                    </div>
                </div>

                {/* Identity & Headline */}
                <div className="space-y-1">
                    <div className="flex items-center gap-1.5 flex-wrap">
                        <h3 className="font-extrabold text-base text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            {connection.name}
                        </h3>
                        {/* Level 2 Verified Pill Badge */}
                        <span className="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/80 dark:border-blue-800/80">
                            <svg className="w-2.5 h-2.5 text-blue-600 dark:text-blue-400 fill-current" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                            </svg>
                            <span>L2</span>
                        </span>
                    </div>

                    <p className="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed font-medium">
                        {connection.headline}
                    </p>

                    <div className="flex items-center gap-2 pt-1 text-[11px] text-slate-400 dark:text-slate-500">
                        <span>📍 {connection.location}</span>
                        <span>•</span>
                        <span>{connection.connectedDate}</span>
                    </div>
                </div>
            </div>

            {/* Bottom Row: Mutual Connections & Action CTAs */}
            <div className="pt-4 mt-3 border-t border-slate-100 dark:border-slate-800/80 space-y-3">
                {/* Mutual Connection Text */}
                <div className="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400">
                    <svg className="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span className="truncate">{connection.mutualCount} mutual connections</span>
                </div>

                {/* Primary Dual Actions: [Message] & [Visiting Card 📇] */}
                <div className="grid grid-cols-2 gap-2">
                    <button
                        type="button"
                        onClick={() => onMessage && onMessage(connection)}
                        className="w-full py-2 px-3 rounded-xl bg-slate-900 hover:bg-blue-600 text-white text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-2xs hover:shadow-xs active:scale-98 cursor-pointer"
                    >
                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>Message</span>
                    </button>

                    <button
                        type="button"
                        onClick={handleVisitingCardClick}
                        className="w-full py-2 px-3 rounded-xl bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-2xs active:scale-98 cursor-pointer"
                        title="View Full Glass Visiting Card"
                    >
                        <span>📇 Card</span>
                    </button>
                </div>
            </div>
        </div>
    );
}
