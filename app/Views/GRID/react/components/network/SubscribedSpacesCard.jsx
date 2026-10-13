import React from 'react';

/**
 * SubscribedSpacesCard Component
 * Right rail bottom module for displaying subscribed tech & design spaces.
 */
export default function SubscribedSpacesCard({ spaces = [], onNavigate, onToast }) {
    const handleSpaceClick = (space) => {
        onToast && onToast(`Opening space: ${space.name}`);
    };

    return (
        <section aria-label="Subscribed Spaces" className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm p-4 space-y-3">
            <div className="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <div className="flex items-center gap-2">
                    <span className="text-sm">🌐</span>
                    <h3 className="font-bold text-slate-900 dark:text-white text-xs tracking-tight">
                        Subscribed Spaces
                    </h3>
                </div>
                <button
                    type="button"
                    onClick={() => onToast && onToast('Explore all community spaces')}
                    className="text-xs text-blue-600 dark:text-blue-400 font-semibold hover:underline cursor-pointer"
                >
                    Explore
                </button>
            </div>

            <div className="space-y-2">
                {spaces.map((space, idx) => (
                    <div
                        key={idx}
                        onClick={() => handleSpaceClick(space)}
                        className="group flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 border border-transparent hover:border-slate-200/60 dark:hover:border-slate-700/60 transition-all cursor-pointer"
                    >
                        <div className="flex items-center gap-2.5 min-w-0">
                            <span className="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 font-mono font-bold text-[10px] flex items-center justify-center shrink-0 border border-blue-200/60 dark:border-blue-800/60 group-hover:scale-105 transition-transform">
                                {space.tag}
                            </span>
                            <div className="min-w-0">
                                <h4 className="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {space.name}
                                </h4>
                                <p className="text-[10px] text-slate-400 dark:text-slate-500">
                                    {space.members}
                                </p>
                            </div>
                        </div>

                        <svg className="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                ))}
            </div>
        </section>
    );
}
