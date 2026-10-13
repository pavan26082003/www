import React, { useState } from 'react';
import VerifiedBadge from '../ui/VerifiedBadge.jsx';

/**
 * StitchPeerCard Component
 * LinkedIn-Style 2-Column Mobile & Multi-Column Desktop Peer Recommendation Card.
 * Matches LinkedIn Mobile reference:
 * - Compact 2-column card on mobile (grid-cols-2)
 * - Proportional banner (h-14 sm:h-16) with top-right dismiss button
 * - Centered avatar (w-14 h-14 sm:w-16 sm:h-16) with OPEN badge / online dot
 * - Name with verified badge / LinkedIn icon
 * - 2-line clamped headline & single-line mutuals stack
 * - Full-width '+ Connect' / '+ Follow' rounded button
 */
export default function StitchPeerCard({ peer, onConnect, onDismiss, onToast, onOpenProfile }) {
    const [isConnected, setIsConnected] = useState(false);
    const [isDismissed, setIsDismissed] = useState(false);

    if (isDismissed) return null;

    const handleOpenProfileClick = (e) => {
        e.stopPropagation();
        if (onOpenProfile) {
            onOpenProfile(peer);
        }
    };

    const handleConnectToggle = () => {
        if (!isConnected) {
            setIsConnected(true);
            if (onConnect) {
                onConnect(peer);
            } else {
                onToast && onToast(`Connection invitation sent to ${peer.name}! 🚀`);
            }
        } else {
            setIsConnected(false);
            onToast && onToast(`Withdrew invitation for ${peer.name}`);
        }
    };

    const handleDismissClick = (e) => {
        e.stopPropagation();
        setIsDismissed(true);
        if (onDismiss) onDismiss(peer.id);
        onToast && onToast(`Suggestion dismissed`);
    };

    const bannerGradientClass = peer.bannerGradient || 'from-blue-600 via-indigo-600 to-cyan-500';

    return (
        <article className="g-glass-card border border-white/70 dark:border-slate-800/80 rounded-xl sm:rounded-2xl overflow-hidden flex flex-col justify-between hover:shadow-md hover:border-blue-400/50 dark:hover:border-blue-500/50 transition-all duration-200 relative group shadow-xs">
            {/* Dismiss suggestion button */}
            <button
                type="button"
                onClick={handleDismissClick}
                className="absolute top-1.5 right-1.5 sm:top-2 sm:right-2 z-10 w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-black/50 hover:bg-black/70 text-white flex items-center justify-center text-[11px] sm:text-xs backdrop-blur-xs transition-colors cursor-pointer"
                title="Dismiss suggestion"
            >
                ✕
            </button>

            {/* Cover Banner & Overlapping Avatar */}
            <div>
                <div className={`h-14 sm:h-16 w-full bg-gradient-to-r ${bannerGradientClass} transition-opacity`} />
                <div className="px-2 sm:px-3 pt-0 pb-1 text-center -mt-7 sm:-mt-8">
                    <div className="relative inline-block cursor-pointer" onClick={handleOpenProfileClick} title={`View ${peer.name}'s profile`}>
                        <img
                            alt={peer.name}
                            src={peer.avatar}
                            className={`w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover mx-auto shadow-xs transition-transform group-hover:scale-105 ${
                                peer.isOpenToWork
                                    ? 'border-2 border-emerald-500 ring-2 ring-white dark:ring-slate-900'
                                    : 'border-2 border-white dark:border-slate-900'
                            }`}
                            loading="lazy"
                        />
                        {/* Open to Work pill badge */}
                        {peer.isOpenToWork && (
                            <span className="absolute -bottom-1 -right-1 bg-emerald-600 text-white text-[7px] sm:text-[8px] font-black px-1 py-0.2 sm:px-1.5 sm:py-0.5 rounded-full uppercase tracking-tighter shadow-2xs">
                                OPEN
                            </span>
                        )}
                        {/* Online status indicator */}
                        {!peer.isOpenToWork && peer.isOnline && (
                            <span className="absolute bottom-0 right-0.5 w-3 h-3 sm:w-3.5 sm:h-3.5 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full" />
                        )}
                    </div>

                    <div className="mt-1 sm:mt-1.5">
                        <div className="flex items-center justify-center gap-1">
                            <h4 
                                onClick={handleOpenProfileClick}
                                className="font-bold text-xs sm:text-sm text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer transition-colors truncate max-w-[130px] sm:max-w-[170px]"
                            >
                                {peer.name}
                            </h4>
                            {peer.verified && (
                                <VerifiedBadge size="xs" />
                            )}
                            {!peer.verified && (
                                <span className="text-[9px] sm:text-[10px] text-amber-600 font-bold shrink-0">in</span>
                            )}
                        </div>
                        <p className="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5 min-h-[26px] sm:min-h-[32px] leading-tight sm:leading-snug px-0.5">
                            {peer.headline}
                        </p>
                    </div>
                </div>
            </div>

            {/* Mutual Connections & Action Button */}
            <div className="px-2 sm:px-3 pb-2.5 sm:pb-3 pt-0 flex flex-col justify-between flex-grow">
                {/* Centered mutuals stack and count */}
                <div className="flex items-center justify-center gap-1 sm:gap-1.5 my-1.5 sm:my-2 min-h-[18px] sm:min-h-[22px]">
                    {peer.mutualAvatars && peer.mutualAvatars.length > 0 && (
                        <div className="flex -space-x-1 overflow-hidden shrink-0">
                            {peer.mutualAvatars.map((av, idx) => (
                                <img
                                    key={idx}
                                    alt=""
                                    src={av}
                                    className="inline-block h-3.5 w-3.5 sm:h-4 sm:w-4 rounded-full ring-1 ring-white dark:ring-slate-900 object-cover"
                                />
                            ))}
                        </div>
                    )}
                    <span className="text-[9px] sm:text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-[120px] sm:max-w-[160px]">
                        {peer.mutualText || `${peer.mutualCount || 0} mutuals`}
                    </span>
                </div>

                {/* Full-width Connect / Follow Button */}
                {peer.isFollowable ? (
                    <button
                        type="button"
                        onClick={handleConnectToggle}
                        className={`w-full py-1.5 px-2 rounded-full text-[11px] sm:text-xs font-bold transition-all flex items-center justify-center gap-1 cursor-pointer active:scale-95 ${
                            isConnected
                                ? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700'
                                : 'border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'
                        }`}
                    >
                        {isConnected ? (
                            <>
                                <svg className="w-3 h-3 sm:w-3.5 sm:h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Following</span>
                            </>
                        ) : (
                            <>
                                <svg className="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Follow</span>
                            </>
                        )}
                    </button>
                ) : (
                    <button
                        type="button"
                        onClick={handleConnectToggle}
                        className={`w-full py-1.5 px-2 rounded-full text-[11px] sm:text-xs font-bold transition-all flex items-center justify-center gap-1 cursor-pointer active:scale-95 ${
                            isConnected
                                ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800'
                                : 'border border-blue-600 dark:border-blue-500 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/40'
                        }`}
                    >
                        {isConnected ? (
                            <>
                                <svg className="w-3 h-3 sm:w-3.5 sm:h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Connected</span>
                            </>
                        ) : (
                            <>
                                <svg className="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Connect</span>
                            </>
                        )}
                    </button>
                )}
            </div>
        </article>
    );
}
