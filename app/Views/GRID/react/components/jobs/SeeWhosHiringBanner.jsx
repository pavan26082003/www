import React from 'react';

/**
 * SeeWhosHiringBanner Component
 * Division 5: "See who's hiring" quick callout banner matching user screenshot.
 */
export default function SeeWhosHiringBanner({ userProfile, onSearchJobs, onToast }) {
    const firstName = userProfile?.name?.split(' ')[1] || userProfile?.name?.split(' ')[0] || 'Pavan';

    return (
        <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-4 sm:p-5 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3.5 overflow-hidden">
            <div className="flex items-center gap-3 min-w-0 flex-1">
                <img
                    src={userProfile?.avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=128&q=80'}
                    alt={userProfile?.name || 'User'}
                    className="w-11 h-11 rounded-full object-cover ring-2 ring-blue-500/50 shadow-xs shrink-0"
                />
                <div className="min-w-0 flex-1">
                    <h4 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                        See who's hiring
                    </h4>
                    <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        {firstName}, find a company that needs your skills
                    </p>
                </div>
            </div>

            <button
                type="button"
                onClick={() => {
                    onSearchJobs && onSearchJobs();
                    onToast && onToast('Displaying companies actively hiring for your skillset 🎯');
                }}
                className="w-full sm:w-auto px-4 py-1.5 rounded-full border border-blue-600 dark:border-blue-400 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/40 font-bold text-xs transition-colors cursor-pointer text-center shrink-0 whitespace-nowrap"
            >
                Search jobs
            </button>
        </div>
    );
}
