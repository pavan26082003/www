import React, { useState } from 'react';

/**
 * ManageNetworkCard Component
 * Right rail top navigation card matching Stitch "Manage my network" module.
 * Displays counts for Connections, Following & followers, Groups, Events, Pages, Newsletters, Hashtags.
 */
export default function ManageNetworkCard({ items = [], onFilterSelect, activeFilter }) {
    const [isExpanded, setIsExpanded] = useState(false);

    const renderIcon = (type) => {
        switch (type) {
            case 'connections':
                return (
                    <svg className="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                );
            case 'following':
                return (
                    <svg className="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                );
            case 'events':
                return (
                    <svg className="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                );
            case 'pages':
                return (
                    <svg className="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                );
            case 'newsletters':
                return (
                    <svg className="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                );
            case 'hashtags':
                return (
                    <svg className="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                    </svg>
                );
            default:
                return (
                    <svg className="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                );
        }
    };

    const displayItems = isExpanded ? items : items.slice(0, 4);

    return (
        <section aria-label="Network Management Menu" className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm overflow-hidden">
            <div className="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h2 className="font-bold text-slate-900 dark:text-white text-sm tracking-tight">Manage my network</h2>
                <button
                    type="button"
                    onClick={() => setIsExpanded(!isExpanded)}
                    className="text-xs text-blue-600 dark:text-blue-400 font-semibold hover:underline cursor-pointer"
                >
                    {isExpanded ? 'Less' : 'More'}
                </button>
            </div>

            {/* Navigation Links with Item Counts */}
            <nav className="divide-y divide-slate-100/70 dark:divide-slate-800/60 text-sm">
                {displayItems.map((item) => {
                    const isSelected = activeFilter === item.id;
                    return (
                        <button
                            key={item.id}
                            type="button"
                            onClick={() => onFilterSelect && onFilterSelect(item.id)}
                            className={`w-full flex items-center justify-between px-4 py-3 font-medium transition-all group cursor-pointer text-left ${
                                isSelected
                                    ? 'bg-blue-50/80 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 font-bold border-l-4 border-blue-600 dark:border-blue-400 pl-3'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50/80 dark:hover:bg-slate-800/60'
                            }`}
                        >
                            <div className="flex items-center gap-3">
                                {renderIcon(item.icon)}
                                <span className={`text-xs transition-colors ${isSelected ? 'text-blue-700 dark:text-blue-300 font-bold' : 'group-hover:text-slate-900 dark:group-hover:text-white'}`}>
                                    {item.label}
                                </span>
                            </div>
                            <span className={`text-[11px] font-semibold px-2 py-0.5 rounded-full transition-colors ${
                                isSelected
                                    ? 'bg-blue-600 text-white dark:bg-blue-500'
                                    : 'text-slate-500 dark:text-slate-400 bg-slate-100/90 dark:bg-slate-800'
                            }`}>
                                {item.count}
                            </span>
                        </button>
                    );
                })}
            </nav>
        </section>
    );
}
