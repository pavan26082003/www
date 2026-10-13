import React from 'react';

/**
 * TeamMobileBottomNav Component
 * 5-tab Slack-style fixed bottom navigation bar:
 * - Home (Active by default)
 * - DMs (Direct messages)
 * - Activity (Notifications)
 * - Search (Workspace search)
 * - More (Profile & Settings)
 *
 * Includes the floating purple '+' action button above the bar.
 */
export default function TeamMobileBottomNav({ activeTab = 'home', onTabChange, onFabClick, unreadDMsCount = 0 }) {
    return (
        <>
            {/* Floating Action Button (FAB) */}
            <button
                type="button"
                onClick={onFabClick}
                className="fixed right-4 bottom-20 z-40 w-12 h-12 rounded-2xl bg-[#1164A3] hover:bg-[#0F568C] text-white flex items-center justify-center shadow-xl shadow-blue-950/20 active:scale-95 transition-transform cursor-pointer"
                title="New message or channel"
            >
                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 4v16m8-8H4" />
                </svg>
            </button>

            {/* Bottom 5-Tab Navigation Bar */}
            <nav className="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-lg border-t border-[#E2E8F0] z-40 flex items-center justify-around py-1.5 px-2 select-none shadow-lg">
                {/* 1. Home */}
                <button
                    type="button"
                    onClick={() => onTabChange && onTabChange('home')}
                    className={`flex flex-col items-center justify-center flex-1 py-1 transition-colors cursor-pointer ${
                        activeTab === 'home' ? 'text-[#1164A3]' : 'text-slate-500 hover:text-slate-900'
                    }`}
                >
                    <div className={`px-4 py-1 rounded-full transition-colors ${
                        activeTab === 'home' ? 'bg-[#F1F5F9]' : ''
                    }`}>
                        <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                    </div>
                    <span className="text-[10px] font-bold tracking-tight mt-0.5">Home</span>
                </button>

                {/* 2. DMs */}
                <button
                    type="button"
                    onClick={() => onTabChange && onTabChange('dms')}
                    className={`flex flex-col items-center justify-center flex-1 py-1 transition-colors cursor-pointer relative ${
                        activeTab === 'dms' ? 'text-[#1164A3]' : 'text-slate-500 hover:text-slate-900'
                    }`}
                >
                    <div className="relative">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        {unreadDMsCount > 0 && (
                            <span className="absolute -top-1 -right-2 bg-[#E01E5A] text-white text-[9px] font-bold px-1 rounded-full">
                                {unreadDMsCount}
                            </span>
                        )}
                    </div>
                    <span className="text-[10px] font-bold tracking-tight mt-0.5">DMs</span>
                </button>

                {/* 3. Activity */}
                <button
                    type="button"
                    onClick={() => onTabChange && onTabChange('activity')}
                    className={`flex flex-col items-center justify-center flex-1 py-1 transition-colors cursor-pointer ${
                        activeTab === 'activity' ? 'text-[#1164A3]' : 'text-slate-500 hover:text-slate-900'
                    }`}
                >
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span className="text-[10px] font-bold tracking-tight mt-0.5">Activity</span>
                </button>

                {/* 4. Search */}
                <button
                    type="button"
                    onClick={() => onTabChange && onTabChange('search')}
                    className={`flex flex-col items-center justify-center flex-1 py-1 transition-colors cursor-pointer ${
                        activeTab === 'search' ? 'text-[#1164A3]' : 'text-slate-500 hover:text-slate-900'
                    }`}
                >
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span className="text-[10px] font-bold tracking-tight mt-0.5">Search</span>
                </button>

                {/* 5. More */}
                <button
                    type="button"
                    onClick={() => onTabChange && onTabChange('more')}
                    className={`flex flex-col items-center justify-center flex-1 py-1 transition-colors cursor-pointer ${
                        activeTab === 'more' ? 'text-[#1164A3]' : 'text-slate-500 hover:text-slate-900'
                    }`}
                >
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                    </svg>
                    <span className="text-[10px] font-bold tracking-tight mt-0.5">More</span>
                </button>
            </nav>
        </>
    );
}
