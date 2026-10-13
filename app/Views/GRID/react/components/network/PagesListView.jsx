import React, { useState } from 'react';

/**
 * PagesListView Component
 * Renders the Pages management section (Company & Organization pages).
 */
export default function PagesListView({ pages = [], onBack, onToast }) {
    const [followingMap, setFollowingMap] = useState({
        'page-1': true,
        'page-2': true,
        'page-3': true,
        'page-4': true
    });

    const handleToggleFollow = (page) => {
        const next = !followingMap[page.id];
        setFollowingMap((prev) => ({ ...prev, [page.id]: next }));
        onToast && onToast(next ? `Following ${page.name}` : `Unfollowed ${page.name}`);
    };

    return (
        <section aria-label="Pages Hub" className="w-full space-y-3">
            {/* Back button */}
            <div className="flex items-center justify-between px-3.5 sm:px-0 pb-1">
                <button
                    type="button"
                    onClick={onBack}
                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer"
                >
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Back to Network</span>
                </button>
            </div>

            {/* Main Pages Container */}
            <div className="g-glass-card rounded-none sm:rounded-2xl border-x-0 sm:border-x border-y sm:border border-white/70 dark:border-slate-800/80 shadow-sm overflow-hidden">
                <div className="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h1 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                            Pages You Follow ({pages.length})
                        </h1>
                        <p className="text-xs text-slate-500 dark:text-slate-400">
                            Organizations, labs, and universities in your ecosystem
                        </p>
                    </div>
                </div>

                <div className="divide-y divide-slate-100 dark:divide-slate-800/80">
                    {pages.map((page) => {
                        const isFollowing = !!followingMap[page.id];

                        return (
                            <div
                                key={page.id}
                                className="p-4 sm:p-5 flex items-center justify-between gap-4 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors"
                            >
                                <div className="flex items-center gap-3.5 min-w-0">
                                    <div
                                        style={{ backgroundColor: page.logoBg }}
                                        className="w-12 h-12 rounded-xl text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-xs"
                                    >
                                        {page.logoText}
                                    </div>
                                    <div className="min-w-0 space-y-0.5">
                                        <h2 className="text-sm sm:text-base font-bold text-slate-900 dark:text-white truncate">
                                            {page.name}
                                        </h2>
                                        <p className="text-xs text-slate-600 dark:text-slate-300 line-clamp-1">
                                            {page.tagline}
                                        </p>
                                        <p className="text-[11px] text-slate-400 dark:text-slate-500">
                                            {page.industry} • {page.followers}
                                        </p>
                                    </div>
                                </div>

                                <div className="shrink-0">
                                    <button
                                        type="button"
                                        onClick={() => handleToggleFollow(page)}
                                        className={`px-4 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer ${
                                            isFollowing
                                                ? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700'
                                                : 'bg-blue-600 hover:bg-blue-700 text-white shadow-2xs'
                                        }`}
                                    >
                                        {isFollowing ? 'Following' : '+ Follow'}
                                    </button>
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>
        </section>
    );
}
