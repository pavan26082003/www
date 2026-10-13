import React from 'react';

/**
 * TeamActivityRail Component
 * The thin leftmost vertical navigation rail seen in Slack desktop (Images 1, 2, 3, 4):
 * - Workspace Icon [G]
 * - Home (🏠)
 * - Activity (🔔)
 * - Files (📁)
 * - Agents & tools (🎛️)
 * - Add (+)
 * - Theme toggle (🌙/☀️)
 * - User profile avatar at the bottom
 */
export default function TeamActivityRail({
    workspace,
    currentUser,
    activeRailItem = 'home',
    onSelectRailItem,
    onToast
}) {
    return (
        <aside className="w-14 min-w-[56px] h-full bg-[#1A1D21] border-r border-[#2C3136] flex flex-col items-center justify-between py-3 select-none z-30 shrink-0">
            {/* Top Workspace Logo & Navigation Icons */}
            <div className="flex flex-col items-center gap-4 w-full">
                {/* [G] Workspace Icon Square */}
                <div
                    onClick={() => onSelectRailItem('home')}
                    className="w-9 h-9 rounded-xl bg-[#3F0E40] text-white font-black text-base flex items-center justify-center shadow-md cursor-pointer hover:opacity-90 transition-opacity border border-white/10"
                    title={workspace?.name || 'GRID Workspace'}
                >
                    {workspace?.icon || 'G'}
                </div>

                {/* Rail Navigation Buttons */}
                <div className="flex flex-col items-center gap-2 w-full px-1.5">
                    {/* Home */}
                    <button
                        type="button"
                        onClick={() => onSelectRailItem('home')}
                        className={`w-10 h-10 rounded-xl flex flex-col items-center justify-center text-xs transition-colors group ${
                            activeRailItem === 'home'
                                ? 'bg-white/15 text-white font-bold'
                                : 'text-slate-400 hover:text-white hover:bg-white/10'
                        }`}
                        title="Home"
                    >
                        <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        <span className="text-[9px] mt-0.5 leading-none">Home</span>
                    </button>

                    {/* Activity */}
                    <button
                        type="button"
                        onClick={() => {
                            onSelectRailItem('activity');
                            if (onToast) onToast('No new notifications');
                        }}
                        className={`w-10 h-10 rounded-xl flex flex-col items-center justify-center text-xs transition-colors group ${
                            activeRailItem === 'activity'
                                ? 'bg-white/15 text-white font-bold'
                                : 'text-slate-400 hover:text-white hover:bg-white/10'
                        }`}
                        title="Activity"
                    >
                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span className="text-[9px] mt-0.5 leading-none">Activity</span>
                    </button>

                    {/* Files */}
                    <button
                        type="button"
                        onClick={() => {
                            onSelectRailItem('files');
                            if (onToast) onToast('All workspace files & canvases');
                        }}
                        className={`w-10 h-10 rounded-xl flex flex-col items-center justify-center text-xs transition-colors group ${
                            activeRailItem === 'files'
                                ? 'bg-white/15 text-white font-bold'
                                : 'text-slate-400 hover:text-white hover:bg-white/10'
                        }`}
                        title="Files"
                    >
                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span className="text-[9px] mt-0.5 leading-none">Files</span>
                    </button>

                    {/* Agents & tools */}
                    <button
                        type="button"
                        onClick={() => {
                            onSelectRailItem('agents');
                            if (onToast) onToast('Connected tools & AI Agents');
                        }}
                        className={`w-10 h-10 rounded-xl flex flex-col items-center justify-center text-xs transition-colors group ${
                            activeRailItem === 'agents'
                                ? 'bg-white/15 text-white font-bold'
                                : 'text-slate-400 hover:text-white hover:bg-white/10'
                        }`}
                        title="Agents & tools"
                    >
                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span className="text-[9px] mt-0.5 leading-none">Agents</span>
                    </button>

                    {/* Add More (+) */}
                    <button
                        type="button"
                        onClick={() => onToast && onToast('Add canvas, list, or integration')}
                        className="w-8 h-8 rounded-full border border-slate-600 hover:border-white text-slate-400 hover:text-white flex items-center justify-center text-base mt-2 transition-colors"
                        title="Add more"
                    >
                        +
                    </button>
                </div>
            </div>

            {/* Bottom: Theme toggle & Avatar */}
            <div className="flex flex-col items-center gap-3 w-full">
                {/* Theme icon pill */}
                <button
                    type="button"
                    onClick={() => onToast && onToast('Theme preferences')}
                    className="w-7 h-7 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center text-xs transition-colors"
                    title="Toggle appearance"
                >
                    🌙
                </button>

                {/* User Avatar */}
                <div
                    className="w-8 h-8 rounded-md bg-purple-800 text-white font-bold text-xs flex items-center justify-center border border-purple-400/40 relative cursor-pointer"
                    title={currentUser?.name || 'Your Profile'}
                >
                    {currentUser?.initials || 'PK'}
                    <span className="w-2 h-2 rounded-full bg-emerald-500 border border-[#1A1D21] absolute -bottom-0.5 -right-0.5" />
                </div>
            </div>
        </aside>
    );
}
