import React, { useState } from 'react';

/**
 * TeamMobileTopBar Component
 * Top header bar for the mobile Team workspace matching Slack-style reference.
 * Displays:
 * - Square 'G' badge
 * - 'GRID ⌄' workspace selector
 * - History button
 * - User Profile Avatar with status ring
 */
export default function TeamMobileTopBar({ workspace, currentUser, onSwitchWorkspace, workspaces = [], onToast }) {
    const [showWorkspaceMenu, setShowWorkspaceMenu] = useState(false);

    const wsName = workspace?.name || 'GRID';
    const wsInitial = workspace?.icon || wsName.charAt(0).toUpperCase() || 'G';

    return (
        <header className="bg-white text-slate-900 px-3.5 py-2.5 flex items-center justify-between border-b border-[#E2E8F0] shrink-0 select-none z-30 shadow-2xs">
            {/* Left & Center: Workspace Badge + Workspace Name Dropdown */}
            <div className="flex items-center gap-2.5 relative">
                {/* Square Workspace Badge */}
                <div className="w-8 h-8 rounded-lg bg-[#0F172A] border border-[#CBD5E1] flex items-center justify-center text-white font-extrabold text-sm shadow-xs shrink-0">
                    {wsInitial}
                </div>

                {/* Workspace Name with Chevron */}
                <button
                    type="button"
                    onClick={() => setShowWorkspaceMenu(!showWorkspaceMenu)}
                    className="flex items-center gap-1.5 text-slate-900 hover:text-slate-700 transition-colors cursor-pointer group"
                >
                    <span className="font-extrabold text-base tracking-tight uppercase">
                        {wsName}
                    </span>
                    <svg className="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-800 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {/* Workspace Dropdown Menu */}
                {showWorkspaceMenu && (
                    <div className="absolute left-0 top-11 w-56 bg-white border border-[#E2E8F0] rounded-xl shadow-2xl p-1.5 z-50 text-xs">
                        <div className="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-[#F1F5F9]">
                            Workspaces
                        </div>
                        {workspaces.map((ws) => (
                            <button
                                key={ws.id}
                                type="button"
                                onClick={() => {
                                    onSwitchWorkspace && onSwitchWorkspace(ws.id);
                                    setShowWorkspaceMenu(false);
                                }}
                                className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-left transition-colors cursor-pointer ${
                                    ws.id === workspace?.id ? 'bg-[#F1F5F9] text-[#1164A3] font-bold' : 'text-slate-700 hover:bg-[#F8FAFC]'
                                }`}
                            >
                                <div className="flex items-center gap-2 truncate">
                                    <span className="w-5 h-5 rounded bg-[#0F172A] flex items-center justify-center font-bold text-[10px] text-white">
                                        {ws.icon || 'G'}
                                    </span>
                                    <span className="truncate">{ws.name}</span>
                                </div>
                                {ws.id === workspace?.id && (
                                    <span className="text-[#1164A3] text-xs font-bold">✓</span>
                                )}
                            </button>
                        ))}
                    </div>
                )}
            </div>

            {/* Right: History Icon & User Profile Avatar */}
            <div className="flex items-center gap-2">
                {/* History / Clock Button */}
                <button
                    type="button"
                    onClick={() => onToast && onToast('Recent history')}
                    className="w-8 h-8 rounded-lg bg-[#F1F5F9] hover:bg-[#E2E8F0] flex items-center justify-center text-slate-600 hover:text-slate-900 transition-colors cursor-pointer"
                    title="History"
                >
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>

                {/* Profile Avatar with Online Ring */}
                <div
                    onClick={() => onToast && onToast(`Signed in as ${currentUser?.name || 'User'}`)}
                    className="relative cursor-pointer"
                >
                    <img
                        alt={currentUser?.name || 'User'}
                        src={currentUser?.avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=128&q=80'}
                        className="w-8 h-8 rounded-lg object-cover ring-2 ring-emerald-500/80 shadow-2xs"
                    />
                    <span className="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 rounded-full ring-2 ring-white" />
                </div>
            </div>
        </header>
    );
}
