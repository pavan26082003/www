import React, { useState } from 'react';
import VerifiedBadge from '../ui/VerifiedBadge.jsx';

/**
 * NetworkInvitations Component
 * Displays incoming connection invitations with instant Accept / Ignore actions.
 */
export default function NetworkInvitations({
    invitations = [],
    onAccept,
    onIgnore,
    onOpenVisitingCard
}) {
    const [isExpanded, setIsExpanded] = useState(true);

    if (!invitations || invitations.length === 0) {
        return null;
    }

    return (
        <div className="mb-6 bg-white/95 dark:bg-slate-900/90 backdrop-blur-xl border border-blue-200/80 dark:border-blue-900/50 rounded-2xl p-4 sm:p-5 shadow-sm">
            {/* Header / Toggle */}
            <div className="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div className="flex items-center gap-2.5">
                    <span className="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse" />
                    <h2 className="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white tracking-tight">
                        Invitations & Requests ({invitations.length})
                    </h2>
                </div>

                <button
                    type="button"
                    onClick={() => setIsExpanded(!isExpanded)}
                    className="text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 cursor-pointer"
                >
                    {isExpanded ? 'Collapse ▴' : 'Show All ▾'}
                </button>
            </div>

            {/* List of Pending Invitations */}
            {isExpanded && (
                <div className="divide-y divide-slate-100 dark:divide-slate-800/80 pt-2 space-y-3 sm:space-y-0">
                    {invitations.map((inv) => (
                        <div key={inv.id} className="py-3.5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            {/* Left: Avatar + Info */}
                            <div className="flex items-start gap-3.5 flex-1 min-w-0">
                                <img
                                    src={inv.avatar}
                                    alt={inv.name}
                                    className="w-12 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shadow-2xs shrink-0"
                                    loading="lazy"
                                />
                                <div className="min-w-0 flex-1">
                                    <div className="flex items-center gap-1.5 flex-wrap">
                                        <h3 className="font-extrabold text-sm text-slate-900 dark:text-white hover:text-blue-600 transition-colors">
                                            {inv.name}
                                        </h3>
                                        <span className="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60">
                                            <VerifiedBadge size="xs" /> Verified Peer
                                        </span>
                                    </div>

                                    <p className="text-xs text-slate-600 dark:text-slate-300 truncate mt-0.5">
                                        {inv.headline}
                                    </p>

                                    {/* Personal Note Quote */}
                                    {inv.note && (
                                        <p className="text-[11px] text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-l-2 border-blue-500 pl-2 py-1 mt-1.5 italic rounded-r">
                                            "{inv.note}"
                                        </p>
                                    )}

                                    <div className="flex items-center gap-2 text-[11px] text-slate-400 mt-1">
                                        <span>👥 {inv.mutualNames}</span>
                                        <span>•</span>
                                        <span>{inv.timeAgo}</span>
                                    </div>
                                </div>
                            </div>

                            {/* Right: Actions */}
                            <div className="flex items-center gap-2 w-full sm:w-auto justify-end shrink-0">
                                <button
                                    type="button"
                                    onClick={() => onIgnore && onIgnore(inv.id)}
                                    className="flex-1 sm:flex-none px-3.5 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors border border-slate-200/80 dark:border-slate-700 cursor-pointer"
                                >
                                    Ignore
                                </button>
                                <button
                                    type="button"
                                    onClick={() => onAccept && onAccept(inv)}
                                    className="flex-1 sm:flex-none px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-xs hover:shadow active:scale-95 cursor-pointer"
                                >
                                    Accept Request
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
