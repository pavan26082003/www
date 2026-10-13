import React, { useState } from 'react';
import ReactDOM from 'react-dom';

/**
 * TeamSidebar Component
 * Exact match of Slack's sidebar from reference screenshots:
 * - Top header with Workspace Switcher dropdown (GRID ▾), Settings (⚙️), Compose (✏️)
 * - "Set up your space" progress badge with 75% circular ring
 * - "Find a conversation..." quick search
 * - "Huddles" toggle row
 * - NO DIRECTORIES (removed as requested)
 * - Section "My team":
 *     - Caret ▾, + icon, and three dots ⋮ on hover
 *     - Clicking ⋮ opens floating pop card OUTSIDE the sidebar via ReactDOM.createPortal at left: 260px
 * - Section "Channels":
 *     - Caret ▾, + icon, and three dots ⋮ on hover
 *     - Floating pop card via ReactDOM.createPortal
 * - Section "Direct messages":
 *     - Caret ▾, + icon, and three dots ⋮ on hover
 *     - Floating pop card via ReactDOM.createPortal matching reference screenshot
 * - Bottom User Profile pill with active status and Exit link
 */
export default function TeamSidebar({
    workspace,
    workspaces = [],
    currentUser,
    myTeam,
    channels = [],
    directMessages = [],
    activeItemId,
    activeSubView,
    onSelectChannel,
    onSelectDM,
    onSelectHome,
    onSwitchWorkspace,
    onCreateWorkspace,
    onReturnToWelcome,
    onExitToGrid,
    onAddChannel,
    onInviteTeammates,
    onToast
}) {
    const [isWsMenuOpen, setIsWsMenuOpen] = useState(false);
    const [isMyTeamExpanded, setIsMyTeamExpanded] = useState(true);
    const [isChannelsExpanded, setIsChannelsExpanded] = useState(true);
    const [isDmsExpanded, setIsDmsExpanded] = useState(true);
    const [searchQuery, setSearchQuery] = useState('');
    const [isHuddleActive, setIsHuddleActive] = useState(false);

    // Floating Context Menu State
    // Positioned via React Portal directly into document.body at left: 260px
    const [activeMenu, setActiveMenu] = useState(null); // { type: 'my-team' | 'channels' | 'dms' | 'item', top: number }

    const toggleHuddle = () => {
        setIsHuddleActive(prev => {
            const next = !prev;
            if (onToast) onToast(next ? '🎧 Huddle started in #GRID' : 'Huddle ended');
            return next;
        });
    };

    // Filter items if searching
    const q = searchQuery.toLowerCase().trim();
    const filteredMyTeamChannels = myTeam?.channels?.filter(c => !q || c.name.toLowerCase().includes(q)) || [];
    const filteredChannels = channels.filter(c => !q || c.name.toLowerCase().includes(q));
    const filteredDMs = directMessages.filter(dm => !q || dm.name.toLowerCase().includes(q));

    const closeAllMenus = () => {
        setActiveMenu(null);
        setIsWsMenuOpen(false);
    };

    // Open floating menu outside sidebar via portal
    const handleOpenMenu = (e, type, id = null) => {
        e.stopPropagation();
        e.preventDefault();
        const rect = e.currentTarget.getBoundingClientRect();
        const menuHeight = type === 'my-team' ? 360 : type === 'dms' ? 240 : 260;
        // Keep inside viewport bounds
        const topPos = Math.max(12, Math.min(rect.top - 8, window.innerHeight - menuHeight - 16));

        setActiveMenu({
            type,
            id,
            top: topPos,
            left: 260 // Exactly right of sidebar
        });
    };

    const workspaceDisplayName = workspace?.name || 'GRID';

    return (
        <>
            <aside className="w-[260px] min-w-[260px] max-w-[260px] h-full bg-[#F8FAFC] text-[#334155] flex flex-col select-none border-r border-[#E2E8F0] relative z-20 shrink-0">
                {/* Top Workspace Header */}
                <div className="h-13 px-4 flex items-center justify-between border-b border-[#E2E8F0] shrink-0 bg-white">
                    <div className="relative">
                        <button
                            type="button"
                            onClick={() => {
                                setActiveMenu(null);
                                setIsWsMenuOpen(prev => !prev);
                            }}
                            className="flex items-center gap-1.5 text-[#0F172A] font-bold text-base hover:bg-[#F1F5F9] py-1 px-2 -ml-2 rounded-md transition-colors"
                        >
                            <span className="truncate max-w-[130px]">{workspaceDisplayName}</span>
                            <svg className={`w-3.5 h-3.5 text-[#64748B] transition-transform ${isWsMenuOpen ? 'rotate-180' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {/* Workspace Switcher Menu Dropdown */}
                        {isWsMenuOpen && (
                            <>
                                <div className="fixed inset-0 z-40" onClick={() => setIsWsMenuOpen(false)} />
                                <div className="absolute left-0 top-full mt-2 w-72 bg-white text-slate-800 rounded-xl shadow-2xl border border-slate-200 py-2 z-50 animate-in fade-in duration-150">
                                    <div className="px-4 py-2 border-b border-slate-100">
                                        <div className="flex items-center gap-3">
                                            <div className="w-9 h-9 rounded-lg bg-[#1164A3] text-white font-black flex items-center justify-center text-lg shadow-xs">
                                                {workspace?.icon || 'G'}
                                            </div>
                                            <div className="overflow-hidden">
                                                <div className="font-bold text-sm text-[#0F172A] truncate">{workspaceDisplayName}</div>
                                                <div className="text-xs text-slate-500 truncate">{workspace?.memberCount || 2} members</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="py-1">
                                        <div className="text-[11px] font-semibold text-slate-400 px-4 py-1.5 uppercase tracking-wider">Switch Workspace</div>
                                        {workspaces.map(ws => (
                                             <button
                                                key={ws.id}
                                                type="button"
                                                onClick={() => {
                                                    closeAllMenus();
                                                    onSwitchWorkspace(ws.id);
                                                }}
                                                className={`w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors ${ws.id === workspace?.id ? 'text-[#1164A3] font-bold bg-blue-50/70' : 'text-slate-700 hover:bg-slate-100'}`}
                                            >
                                                <div className="flex items-center gap-2 truncate">
                                                    <span className="w-5 h-5 rounded bg-slate-200 text-slate-700 text-[11px] font-bold flex items-center justify-center shrink-0">
                                                        {ws.icon || ws.name.charAt(0)}
                                                    </span>
                                                    <span className="truncate">{ws.name}</span>
                                                </div>
                                                {ws.id === workspace?.id && (
                                                    <span className="text-[#1164A3] text-xs font-bold">Active</span>
                                                )}
                                            </button>
                                        ))}
                                    </div>

                                    <div className="border-t border-slate-100 py-1">
                                        <button
                                            type="button"
                                            onClick={() => {
                                                closeAllMenus();
                                                onCreateWorkspace();
                                            }}
                                            className="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 hover:text-[#0F172A] flex items-center gap-2 cursor-pointer"
                                        >
                                            <span className="text-sm font-bold text-[#1164A3]">+</span> Create a new workspace
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => {
                                                closeAllMenus();
                                                onReturnToWelcome();
                                            }}
                                            className="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 hover:text-[#0F172A] flex items-center gap-2 cursor-pointer"
                                        >
                                            <span>🏢</span> Workspace launcher
                                        </button>
                                    </div>

                                    <div className="border-t border-slate-100 pt-1">
                                        <button
                                            type="button"
                                            onClick={() => {
                                                closeAllMenus();
                                                onExitToGrid();
                                            }}
                                            className="w-full px-4 py-2 text-left text-xs text-rose-600 hover:bg-rose-50 flex items-center gap-2 cursor-pointer"
                                        >
                                            <span>←</span> Exit to GRID Feed
                                        </button>
                                    </div>
                                </div>
                            </>
                        )}
                    </div>

                    {/* Right Action Icons: Settings & Slack Compose Button */}
                    <div className="flex items-center gap-1 text-[#64748B]">
                        <button
                            type="button"
                            onClick={() => onToast && onToast('Workspace Preferences')}
                            className="p-1.5 hover:bg-[#F1F5F9] hover:text-[#0F172A] rounded-md transition-colors cursor-pointer"
                            title="Preferences"
                        >
                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            onClick={() => {
                                if (directMessages.length > 0) onSelectDM(directMessages[1]?.id || directMessages[0]?.id);
                                if (onToast) onToast('Composing new direct message');
                            }}
                            className="w-7 h-7 rounded-lg bg-white hover:bg-slate-100 text-[#0F172A] border border-[#CBD5E1] shadow-2xs flex items-center justify-center transition-all cursor-pointer font-bold"
                            title="New message"
                        >
                            <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {/* Scrollable Sidebar Body */}
                <div className="flex-1 overflow-y-auto overflow-x-hidden py-3 px-2 space-y-3.5 scrollbar-thin scrollbar-thumb-slate-300">
                    {/* "Set up your space" Pill with Circular Ring */}
                    <div className="px-2">
                        <button
                            type="button"
                            onClick={onSelectHome}
                            className="w-full bg-white hover:bg-slate-50 text-left text-xs font-semibold text-[#0F172A] px-3 py-2 rounded-lg flex items-center justify-between transition-colors border border-[#CBD5E1] shadow-2xs cursor-pointer"
                        >
                            <div className="flex items-center gap-2">
                                <span className="text-sm">🧭</span>
                                <span>Set up your space</span>
                            </div>
                            {/* Circular progress badge ~75% */}
                            <div className="w-4 h-4 rounded-full border-2 border-slate-200 border-t-emerald-500 border-r-emerald-500 border-b-emerald-500 rotate-45 shrink-0" />
                        </button>
                    </div>

                    {/* Quick Search Pill */}
                    <div className="px-2">
                        <div className="relative">
                            <input
                                type="text"
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                placeholder="Find a conversation..."
                                className="w-full bg-white focus:bg-white text-xs text-[#0F172A] placeholder-[#94A3B8] rounded-md pl-7 pr-2.5 py-1.5 border border-[#CBD5E1] focus:border-[#0284C7] focus:outline-none transition-all shadow-2xs"
                            />
                            <svg className="w-3.5 h-3.5 text-[#94A3B8] absolute left-2 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    {/* Core Navigation Items */}
                    <div className="space-y-0.5 text-xs font-medium px-1">
                        {/* Huddles Item */}
                        <button
                            type="button"
                            onClick={toggleHuddle}
                            className={`w-full flex items-center justify-between px-2.5 py-1.5 rounded-md transition-colors cursor-pointer ${isHuddleActive ? 'bg-[#1164A3] text-white font-bold' : 'text-[#334155] hover:bg-[#E2E8F0]/70 hover:text-[#0F172A]'}`}
                        >
                            <div className="flex items-center gap-2">
                                <svg className={`w-4 h-4 ${isHuddleActive ? 'text-white' : 'text-[#64748B]'}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" />
                                </svg>
                                <span>Huddles</span>
                            </div>
                            {isHuddleActive && (
                                <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
                            )}
                        </button>
                    </div>

                    {/* ========================================================================= */}
                    {/* SECTION 1: MY TEAM (With Hover Caret, +, and ⋮) */}
                    {/* ========================================================================= */}
                    <div className="pt-1">
                        <div className="group flex items-center justify-between px-2 py-1 text-xs font-bold text-[#64748B] hover:text-[#0F172A] rounded hover:bg-[#E2E8F0]/60 transition-colors">
                            <button
                                type="button"
                                onClick={() => setIsMyTeamExpanded(!isMyTeamExpanded)}
                                className="flex items-center gap-1.5 flex-1 text-left truncate cursor-pointer"
                            >
                                <svg className={`w-3 h-3 text-[#64748B] transition-transform ${isMyTeamExpanded ? 'rotate-90' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M9 5l7 7-7 7" />
                                </svg>
                                <span className="text-sm">😆</span>
                                <span className="uppercase tracking-wider text-[11px] font-bold truncate">
                                    {myTeam?.label || 'My team'}
                                </span>
                            </button>

                            {/* Hover Actions: + and Three Dots ⋮ */}
                            <div className="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button
                                    type="button"
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        onAddChannel();
                                    }}
                                    className="p-1 hover:bg-[#CBD5E1] text-[#64748B] hover:text-[#0F172A] rounded cursor-pointer"
                                    title="Create a channel"
                                >
                                    <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    onClick={(e) => handleOpenMenu(e, 'my-team')}
                                    className="p-1 hover:bg-[#CBD5E1] text-[#64748B] hover:text-[#0F172A] rounded cursor-pointer"
                                    title="Section options"
                                >
                                    <svg className="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="5" r="2" />
                                        <circle cx="12" cy="12" r="2" />
                                        <circle cx="12" cy="19" r="2" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {/* My Team Channel Items */}
                        {isMyTeamExpanded && (
                            <div className="mt-0.5 space-y-0.5 pl-3 pr-1">
                                {filteredMyTeamChannels.map(channel => {
                                    const isSelected = activeItemId === channel.id && activeSubView === 'channel';
                                    const hasUnread = channel.unread > 0;
                                    return (
                                        <button
                                            key={channel.id}
                                            type="button"
                                            onClick={() => onSelectChannel(channel.id)}
                                            className={`w-full flex items-center justify-between px-2 py-1.5 rounded-md text-xs transition-colors text-left cursor-pointer ${
                                                isSelected
                                                    ? 'bg-[#1164A3] text-white font-bold shadow-xs'
                                                    : hasUnread
                                                    ? 'text-[#0F172A] font-bold hover:bg-[#E2E8F0]/70'
                                                    : 'text-[#334155] hover:bg-[#E2E8F0]/70 hover:text-[#0F172A]'
                                            }`}
                                        >
                                            <div className="flex items-center gap-2 truncate">
                                                <span className={`text-sm ${isSelected ? 'text-white font-bold' : 'text-[#64748B]'}`}>#</span>
                                                <span className="truncate">{channel.name}</span>
                                            </div>
                                            {hasUnread && (
                                                <span className="px-1.5 py-0.5 bg-[#E01E5A] text-white rounded-full text-[10px] font-extrabold shadow-xs">
                                                    {channel.unread}
                                                </span>
                                            )}
                                        </button>
                                    );
                                })}
                            </div>
                        )}
                    </div>

                    {/* ========================================================================= */}
                    {/* SECTION 2: CHANNELS (With Hover Caret, +, and ⋮) */}
                    {/* ========================================================================= */}
                    <div className="pt-1">
                        <div className="group flex items-center justify-between px-2 py-1 text-xs font-bold text-[#64748B] hover:text-[#0F172A] rounded hover:bg-[#E2E8F0]/60 transition-colors">
                            <button
                                type="button"
                                onClick={() => setIsChannelsExpanded(!isChannelsExpanded)}
                                className="flex items-center gap-1.5 flex-1 text-left truncate cursor-pointer"
                            >
                                <svg className={`w-3 h-3 text-[#64748B] transition-transform ${isChannelsExpanded ? 'rotate-90' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M9 5l7 7-7 7" />
                                </svg>
                                <span className="text-sm">#</span>
                                <span className="uppercase tracking-wider text-[11px] font-bold truncate">Channels</span>
                            </button>

                            {/* Hover Actions: + and Three Dots ⋮ */}
                            <div className="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button
                                    type="button"
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        onAddChannel();
                                    }}
                                    className="p-1 hover:bg-[#CBD5E1] text-[#64748B] hover:text-[#0F172A] rounded cursor-pointer"
                                    title="Add channel"
                                >
                                    <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    onClick={(e) => handleOpenMenu(e, 'channels')}
                                    className="p-1 hover:bg-[#CBD5E1] text-[#64748B] hover:text-[#0F172A] rounded cursor-pointer"
                                    title="Section options"
                                >
                                    <svg className="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="5" r="2" />
                                        <circle cx="12" cy="12" r="2" />
                                        <circle cx="12" cy="19" r="2" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {/* Channels List */}
                        {isChannelsExpanded && (
                            <div className="mt-0.5 space-y-0.5 pl-3 pr-1">
                                {filteredChannels.map(channel => {
                                    const isSelected = activeItemId === channel.id && activeSubView === 'channel';
                                    const hasUnread = channel.unread > 0;
                                    return (
                                        <button
                                            key={channel.id}
                                            type="button"
                                            onClick={() => onSelectChannel(channel.id)}
                                            className={`w-full flex items-center justify-between px-2 py-1.5 rounded-md text-xs transition-colors text-left cursor-pointer ${
                                                isSelected
                                                    ? 'bg-[#1164A3] text-white font-bold shadow-xs'
                                                    : hasUnread
                                                    ? 'text-[#0F172A] font-bold hover:bg-[#E2E8F0]/70'
                                                    : 'text-[#334155] hover:bg-[#E2E8F0]/70 hover:text-[#0F172A]'
                                            }`}
                                        >
                                            <div className="flex items-center gap-2 truncate">
                                                <span className={`text-sm ${isSelected ? 'text-white font-bold' : 'text-[#64748B]'}`}>#</span>
                                                <span className="truncate">{channel.name}</span>
                                            </div>
                                            {hasUnread && (
                                                <span className="px-1.5 py-0.5 bg-[#E01E5A] text-white rounded-full text-[10px] font-extrabold shadow-xs">
                                                    {channel.unread}
                                                </span>
                                            )}
                                        </button>
                                    );
                                })}

                                {/* Add channels button */}
                                <button
                                    type="button"
                                    onClick={onAddChannel}
                                    className="w-full flex items-center gap-2 px-2 py-1.5 rounded-md text-xs text-[#64748B] hover:text-[#0F172A] hover:bg-[#E2E8F0]/70 transition-colors cursor-pointer"
                                >
                                    <span className="text-sm font-bold">+</span>
                                    <span>Add channels</span>
                                </button>
                            </div>
                        )}
                    </div>

                    {/* ========================================================================= */}
                    {/* SECTION 3: DIRECT MESSAGES (With Hover Caret, +, and ⋮) */}
                    {/* ========================================================================= */}
                    <div className="pt-1">
                        <div className="group flex items-center justify-between px-2 py-1 text-xs font-bold text-[#64748B] hover:text-[#0F172A] rounded hover:bg-[#E2E8F0]/60 transition-colors">
                            <button
                                type="button"
                                onClick={() => setIsDmsExpanded(!isDmsExpanded)}
                                className="flex items-center gap-1.5 flex-1 text-left truncate cursor-pointer"
                            >
                                <svg className={`w-3 h-3 text-[#64748B] transition-transform ${isDmsExpanded ? 'rotate-90' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M9 5l7 7-7 7" />
                                </svg>
                                <span className="text-sm">💬</span>
                                <span className="uppercase tracking-wider text-[11px] font-bold truncate">Direct messages</span>
                            </button>

                            {/* Hover Actions: + and Three Dots ⋮ */}
                            <div className="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button
                                    type="button"
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        if (directMessages.length > 0) onSelectDM(directMessages[0].id);
                                    }}
                                    className="p-1 hover:bg-[#CBD5E1] text-[#64748B] hover:text-[#0F172A] rounded cursor-pointer"
                                    title="Start direct message"
                                >
                                    <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    onClick={(e) => handleOpenMenu(e, 'dms')}
                                    className="p-1 hover:bg-[#CBD5E1] text-[#64748B] hover:text-[#0F172A] rounded cursor-pointer"
                                    title="Section options"
                                >
                                    <svg className="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="5" r="2" />
                                        <circle cx="12" cy="12" r="2" />
                                        <circle cx="12" cy="19" r="2" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {/* Direct Messages List */}
                        {isDmsExpanded && (
                            <div className="mt-0.5 space-y-0.5 pl-3 pr-1">
                                {filteredDMs.map(dm => {
                                    const isSelected = activeItemId === dm.id && activeSubView === 'dm';
                                    const isSelf = dm.userId === currentUser?.id || dm.name.includes('(you)');
                                    const hasUnread = dm.unread > 0;

                                    return (
                                        <button
                                            key={dm.id}
                                            type="button"
                                            onClick={() => onSelectDM(dm.id)}
                                            className={`w-full flex items-center justify-between px-2 py-1.5 rounded-md text-xs transition-colors text-left cursor-pointer ${
                                                isSelected
                                                    ? 'bg-[#1164A3] text-white font-bold shadow-xs'
                                                    : hasUnread
                                                    ? 'text-[#0F172A] font-bold hover:bg-[#E2E8F0]/70'
                                                    : 'text-[#334155] hover:bg-[#E2E8F0]/70 hover:text-[#0F172A]'
                                            }`}
                                        >
                                            <div className="flex items-center gap-2 truncate">
                                                {/* Status Indicator */}
                                                <span
                                                    className="w-2 h-2 rounded-full shrink-0"
                                                    style={{ backgroundColor: dm.statusColor || '#2BAC76' }}
                                                />
                                                <span className="truncate">{dm.name}</span>
                                                {isSelf && (
                                                    <span className="text-[10px] text-[#64748B] font-normal shrink-0">🚀</span>
                                                )}
                                            </div>
                                            {hasUnread && (
                                                <span className="px-1.5 py-0.5 bg-[#E01E5A] text-white rounded-full text-[10px] font-extrabold shadow-xs">
                                                    {dm.unread}
                                                </span>
                                            )}
                                        </button>
                                    );
                                })}

                                {/* + Invite people button */}
                                <button
                                    type="button"
                                    onClick={onInviteTeammates}
                                    className="w-full flex items-center gap-2 px-2 py-1.5 rounded-md text-xs text-[#64748B] hover:text-[#0F172A] hover:bg-[#E2E8F0]/70 transition-colors cursor-pointer"
                                >
                                    <span className="text-sm font-bold">+</span>
                                    <span>Invite people</span>
                                </button>
                            </div>
                        )}
                    </div>
                </div>

                {/* Bottom Profile Bar */}
                <div className="p-2.5 border-t border-[#E2E8F0] bg-white shrink-0 flex items-center justify-between">
                    <div className="flex items-center gap-2.5 overflow-hidden">
                        <div className="relative">
                            <div className="w-8 h-8 rounded-md bg-[#1164A3] text-white font-black text-xs flex items-center justify-center border border-slate-200 shadow-2xs">
                                {currentUser?.initials || 'PK'}
                            </div>
                            <span className="w-2.5 h-2.5 rounded-full bg-[#2BAC76] border-2 border-white absolute -bottom-0.5 -right-0.5" />
                        </div>
                        <div className="overflow-hidden">
                            <div className="font-bold text-xs text-[#0F172A] truncate max-w-[110px]">
                                {currentUser?.name || 'K Pavan Kumar'}
                            </div>
                            <div className="text-[10px] text-[#2BAC76] font-medium flex items-center gap-1">
                                <span className="w-1.5 h-1.5 rounded-full bg-[#2BAC76]" />
                                <span>Active</span>
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        onClick={onExitToGrid}
                        className="text-[11px] font-semibold text-[#64748B] hover:text-[#0F172A] px-2 py-1 rounded hover:bg-[#F1F5F9] transition-colors flex items-center gap-1 cursor-pointer"
                        title="Return to GRID feed"
                    >
                        <span>Exit</span>
                        <span>↗</span>
                    </button>
                </div>
            </aside>

            {/* ========================================================================= */}
            {/* FLOATING CONTEXT MENUS RENDERED VIA PORTAL TO DOCUMENT.BODY                */}
            {/* Exactly positioned at left: 260px, top: activeMenu.top                      */}
            {/* ========================================================================= */}
            {activeMenu && ReactDOM.createPortal(
                <>
                    {/* Backdrop to close menu on click outside */}
                    <div
                        className="fixed inset-0 z-40 bg-transparent cursor-default"
                        onClick={closeAllMenus}
                    />

                    {/* 1. Menu for "My team" (Exact match to Reference Screenshot) */}
                    {activeMenu.type === 'my-team' && (
                        <div
                            className="fixed z-50 bg-white text-slate-800 rounded-xl shadow-2xl border border-slate-200/90 py-1.5 text-xs select-none w-[240px] animate-in fade-in zoom-in-95 duration-100"
                            style={{ top: `${activeMenu.top}px`, left: `${activeMenu.left}px` }}
                        >
                            <div className="px-3.5 py-1 text-[12px] font-bold text-slate-900 border-b border-slate-100">
                                {myTeam?.label || 'My team'}
                            </div>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onAddChannel();
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white flex items-center gap-2 cursor-pointer transition-colors"
                            >
                                <span className="font-bold text-sm">#</span>
                                <span>Create a channel</span>
                            </button>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Section shared');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white flex items-center justify-between cursor-pointer transition-colors"
                            >
                                <span>Share</span>
                                <span className="text-slate-400 text-[11px]">&gt;</span>
                            </button>

                            <div className="my-1 border-t border-slate-100" />
                            <div className="px-3.5 pt-1 pb-0.5 text-[11px] font-semibold text-slate-400">
                                Manage section
                            </div>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Rename section');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white cursor-pointer transition-colors"
                            >
                                Rename
                            </button>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Create new section');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white cursor-pointer transition-colors"
                            >
                                Create a new section
                            </button>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Manage conversations');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white cursor-pointer transition-colors"
                            >
                                Manage conversations
                            </button>

                            <div className="my-1 border-t border-slate-100" />
                            <div className="px-3.5 pt-1 pb-0.5 text-[11px] font-semibold text-slate-400">
                                Section settings
                            </div>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Filtered to active conversations');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white flex items-center justify-between cursor-pointer transition-colors"
                            >
                                <span>Filter</span>
                                <span className="text-slate-400 text-[11px]">Active only &gt;</span>
                            </button>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Sorted A-Z');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white flex items-center justify-between cursor-pointer transition-colors"
                            >
                                <span>Sort</span>
                                <span className="text-slate-400 text-[11px]">A-Z &gt;</span>
                            </button>

                            <div className="my-1 border-t border-slate-100" />
                            <div className="px-3.5 pt-1 pb-0.5 text-[11px] font-semibold text-slate-400">
                                Notifications
                            </div>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('All conversations in My team muted');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white cursor-pointer transition-colors"
                            >
                                Mute all conversations
                            </button>

                            <div className="my-1 border-t border-slate-100" />

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Default system section cannot be deleted');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-rose-600 hover:bg-rose-50 cursor-pointer transition-colors"
                            >
                                Delete section
                            </button>
                        </div>
                    )}

                    {/* 2. Menu for "Channels" */}
                    {activeMenu.type === 'channels' && (
                        <div
                            className="fixed z-50 bg-white text-slate-800 rounded-xl shadow-2xl border border-slate-200/90 py-1.5 text-xs select-none w-[240px] animate-in fade-in zoom-in-95 duration-100"
                            style={{ top: `${activeMenu.top}px`, left: `${activeMenu.left}px` }}
                        >
                            <div className="px-3.5 py-1 text-[12px] font-bold text-slate-900 border-b border-slate-100">
                                Channels
                            </div>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onAddChannel();
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white flex items-center gap-2 cursor-pointer transition-colors"
                            >
                                <span className="font-bold text-sm">#</span>
                                <span>Create a channel</span>
                            </button>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Browsing all channels');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white cursor-pointer transition-colors"
                            >
                                Browse channels
                            </button>

                            <div className="my-1 border-t border-slate-100" />
                            <div className="px-3.5 pt-1 pb-0.5 text-[11px] font-semibold text-slate-400">
                                Manage section
                            </div>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Create new section');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white cursor-pointer transition-colors"
                            >
                                Create a new section
                            </button>

                            <div className="my-1 border-t border-slate-100" />
                            <div className="px-3.5 pt-1 pb-0.5 text-[11px] font-semibold text-slate-400">
                                Section settings
                            </div>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Filtered to active channels');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white flex items-center justify-between cursor-pointer transition-colors"
                            >
                                <span>Filter</span>
                                <span className="text-slate-400 text-[11px]">Active only &gt;</span>
                            </button>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Sorted A-Z');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white flex items-center justify-between cursor-pointer transition-colors"
                            >
                                <span>Sort</span>
                                <span className="text-slate-400 text-[11px]">A-Z &gt;</span>
                            </button>

                            <div className="my-1 border-t border-slate-100" />

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('All channels muted');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white cursor-pointer transition-colors"
                            >
                                Mute all channels
                            </button>
                        </div>
                    )}

                    {/* 3. Menu for "Direct messages" (Exact match to Reference Screenshot Image) */}
                    {activeMenu.type === 'dms' && (
                        <div
                            className="fixed z-50 bg-white text-slate-800 rounded-xl shadow-2xl border border-slate-200/90 py-1.5 text-xs select-none w-[240px] animate-in fade-in zoom-in-95 duration-100"
                            style={{ top: `${activeMenu.top}px`, left: `${activeMenu.left}px` }}
                        >
                            <div className="px-3.5 pt-1 pb-0.5 text-[11px] font-semibold text-slate-400">
                                Direct messages
                            </div>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    if (directMessages.length > 0) onSelectDM(directMessages[0].id);
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white flex items-center gap-2 cursor-pointer transition-colors"
                            >
                                <svg className="w-4 h-4 text-slate-600 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span>Start direct message</span>
                            </button>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Recent direct messages');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white cursor-pointer transition-colors"
                            >
                                Recent direct messages
                            </button>

                            <div className="my-1 border-t border-slate-100" />
                            <div className="px-3.5 pt-1 pb-0.5 text-[11px] font-semibold text-slate-400">
                                Manage section
                            </div>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Create new section');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white cursor-pointer transition-colors"
                            >
                                Create a new section
                            </button>

                            <div className="my-1 border-t border-slate-100" />
                            <div className="px-3.5 pt-1 pb-0.5 text-[11px] font-semibold text-slate-400">
                                Section settings
                            </div>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Filtered to active DMs');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white flex items-center justify-between cursor-pointer transition-colors"
                            >
                                <span>Filter</span>
                                <span className="text-slate-400 text-[11px]">Active only &gt;</span>
                            </button>

                            <button
                                type="button"
                                onClick={() => {
                                    closeAllMenus();
                                    onToast && onToast('Sorted by recency');
                                }}
                                className="w-full px-3.5 py-1.5 text-left text-slate-800 hover:bg-[#1264A3] hover:text-white flex items-center justify-between cursor-pointer transition-colors"
                            >
                                <span>Sort</span>
                                <span className="text-slate-400 text-[11px]">Recency &gt;</span>
                            </button>
                        </div>
                    )}
                </>,
                document.body
            )}
        </>
    );
}
