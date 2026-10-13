import React, { useState } from 'react';

/**
 * RecentSearchesSection Component
 * Division 3: "Recent job searches" matching user screenshot.
 */
export default function RecentSearchesSection({
    searches = [],
    onSelectSearch,
    onClearSearches,
    onToast
}) {
    const [isExpanded, setIsExpanded] = useState(false);

    if (!searches || searches.length === 0) return null;

    const displayedSearches = isExpanded ? searches : searches.slice(0, 3);

    return (
        <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm overflow-hidden">
            {/* Header with Clear Button */}
            <div className="p-4 sm:p-5 border-b border-slate-200/60 dark:border-slate-800/80 flex items-center justify-between">
                <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                    Recent job searches
                </h3>
                <button
                    type="button"
                    onClick={() => {
                        onClearSearches && onClearSearches();
                        onToast && onToast('Cleared recent searches');
                    }}
                    className="text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition-colors cursor-pointer"
                >
                    Clear
                </button>
            </div>

            {/* List of Recent Search Rows */}
            <div className="divide-y divide-slate-100 dark:divide-slate-800">
                {displayedSearches.map((item) => (
                    <div
                        key={item.id}
                        onClick={() => onSelectSearch && onSelectSearch(item.query)}
                        className="p-3.5 sm:p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors flex items-center justify-between cursor-pointer group"
                    >
                        <div className="flex items-start gap-3">
                            <div className="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 group-hover:bg-blue-50 dark:group-hover:bg-blue-950/40 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors shrink-0 mt-0.5">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 className="text-sm font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {item.query}
                                </h4>
                                <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    {item.location} • <span className="font-bold text-emerald-600 dark:text-emerald-400">{item.alertsCount} new alerts</span>
                                </p>
                            </div>
                        </div>

                        <svg className="w-4 h-4 text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                ))}
            </div>

            {/* Footer "Show more ⌄" */}
            {searches.length > 3 && (
                <div className="p-3 text-center border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                    <button
                        type="button"
                        onClick={() => setIsExpanded(!isExpanded)}
                        className="text-xs sm:text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 inline-flex items-center gap-1.5 transition-colors cursor-pointer"
                    >
                        <span>{isExpanded ? 'Show less' : 'Show more'}</span>
                        <svg className={`w-4 h-4 transition-transform ${isExpanded ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            )}
        </div>
    );
}
