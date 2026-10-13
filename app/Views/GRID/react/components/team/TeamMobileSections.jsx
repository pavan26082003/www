import React, { useState } from 'react';

/**
 * TeamMobileSections Component
 * Collapsible accordion trees for My Team, Channels, Direct Messages, and Recent Apps.
 * Matches Slack mobile workspace navigation tree shown in user's screenshot.
 */
export default function TeamMobileSections({
    myTeam,
    channels = [],
    directMessages = [],
    apps = [],
    activeItemId,
    onSelectChannel,
    onSelectDM,
    onAddChannel,
    onToast
}) {
    const [openSections, setOpenSections] = useState({
        myTeam: true,
        channels: true,
        dms: true,
        apps: false
    });

    const toggleSection = (sectionKey) => {
        setOpenSections(prev => ({
            ...prev,
            [sectionKey]: !prev[sectionKey]
        }));
    };

    return (
        <div className="space-y-1 pb-24 text-slate-700">
            {/* ========================================================= */}
            {/* 1. MY TEAM SECTION */}
            {/* ========================================================= */}
            <div className="border-b border-[#E2E8F0] pb-1">
                <button
                    type="button"
                    onClick={() => toggleSection('myTeam')}
                    className="w-full flex items-center justify-between px-4 py-2.5 hover:bg-[#F1F5F9] transition-colors cursor-pointer group"
                >
                    <div className="flex items-center gap-2">
                        <span className="text-base">🤗</span>
                        <span className="font-bold text-sm text-[#0F172A] group-hover:text-[#1164A3] transition-colors">
                            {myTeam?.label || 'My team'} &gt;
                        </span>
                    </div>
                    <svg
                        className={`w-4 h-4 text-slate-400 transition-transform duration-200 ${
                            openSections.myTeam ? 'rotate-180' : ''
                        }`}
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {openSections.myTeam && (
                    <div className="space-y-0.5 px-2">
                        {(myTeam?.channels || []).map((chan) => {
                            const isSelected = activeItemId === chan.id;
                            return (
                                <button
                                    key={chan.id}
                                    type="button"
                                    onClick={() => onSelectChannel && onSelectChannel(chan.id)}
                                    className={`w-full flex items-center justify-between px-6 py-2 rounded-lg text-sm transition-colors cursor-pointer ${
                                        isSelected
                                            ? 'bg-[#1164A3] text-white font-bold shadow-xs'
                                            : 'text-slate-700 hover:bg-[#F1F5F9] hover:text-slate-900'
                                    }`}
                                >
                                    <div className="flex items-center gap-2.5">
                                        <span className={isSelected ? 'text-blue-200 font-bold' : 'text-slate-400 font-bold'}>#</span>
                                        <span>{chan.name}</span>
                                    </div>
                                    {chan.unread > 0 && (
                                        <span className="bg-[#E01E5A] text-white text-[10px] font-extrabold px-2 py-0.2 rounded-full">
                                            {chan.unread}
                                        </span>
                                    )}
                                </button>
                            );
                        })}
                    </div>
                )}
            </div>

            {/* ========================================================= */}
            {/* 2. CHANNELS SECTION */}
            {/* ========================================================= */}
            <div className="border-b border-[#E2E8F0] pb-1">
                <button
                    type="button"
                    onClick={() => toggleSection('channels')}
                    className="w-full flex items-center justify-between px-4 py-2.5 hover:bg-[#F1F5F9] transition-colors cursor-pointer group"
                >
                    <div className="flex items-center gap-2">
                        <span className="text-slate-400 font-bold text-base">#</span>
                        <span className="font-bold text-sm text-[#0F172A] group-hover:text-[#1164A3] transition-colors">
                            Channels &gt;
                        </span>
                    </div>
                    <svg
                        className={`w-4 h-4 text-slate-400 transition-transform duration-200 ${
                            openSections.channels ? 'rotate-180' : ''
                        }`}
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {openSections.channels && (
                    <div className="space-y-0.5 px-2">
                        {channels.map((chan) => {
                            const isSelected = activeItemId === chan.id;
                            return (
                                <button
                                    key={chan.id}
                                    type="button"
                                    onClick={() => onSelectChannel && onSelectChannel(chan.id)}
                                    className={`w-full flex items-center justify-between px-6 py-2 rounded-lg text-sm transition-colors cursor-pointer ${
                                        isSelected
                                            ? 'bg-[#1164A3] text-white font-bold shadow-xs'
                                            : 'text-slate-700 hover:bg-[#F1F5F9] hover:text-slate-900'
                                    }`}
                                >
                                    <div className="flex items-center gap-2.5">
                                        <span className={isSelected ? 'text-blue-200 font-bold' : 'text-slate-400 font-bold'}>#</span>
                                        <span>{chan.name}</span>
                                    </div>
                                    {chan.unread > 0 && (
                                        <span className="bg-[#E01E5A] text-white text-[10px] font-extrabold px-2 py-0.2 rounded-full">
                                            {chan.unread}
                                        </span>
                                    )}
                                </button>
                            );
                        })}

                        {/* Add Channel Action */}
                        <button
                            type="button"
                            onClick={() => onAddChannel ? onAddChannel() : onToast && onToast('Create channel modal')}
                            className="w-full flex items-center gap-2.5 px-6 py-2 rounded-lg text-sm text-slate-500 hover:text-slate-900 hover:bg-[#F1F5F9] transition-colors cursor-pointer"
                        >
                            <span className="text-base font-bold leading-none">+</span>
                            <span>Add channel</span>
                        </button>
                    </div>
                )}
            </div>

            {/* ========================================================= */}
            {/* 3. DIRECT MESSAGES SECTION */}
            {/* ========================================================= */}
            <div className="border-b border-[#E2E8F0] pb-1">
                <button
                    type="button"
                    onClick={() => toggleSection('dms')}
                    className="w-full flex items-center justify-between px-4 py-2.5 hover:bg-[#F1F5F9] transition-colors cursor-pointer group"
                >
                    <div className="flex items-center gap-2">
                        <svg className="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span className="font-bold text-sm text-[#0F172A] group-hover:text-[#1164A3] transition-colors">
                            Direct messages &gt;
                        </span>
                    </div>
                    <svg
                        className={`w-4 h-4 text-slate-400 transition-transform duration-200 ${
                            openSections.dms ? 'rotate-180' : ''
                        }`}
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {openSections.dms && (
                    <div className="space-y-0.5 px-2">
                        {directMessages.map((dm) => {
                            const isSelected = activeItemId === dm.id;
                            const isOnline = dm.status === 'online' || dm.statusColor === '#10B981';

                            return (
                                <button
                                    key={dm.id}
                                    type="button"
                                    onClick={() => onSelectDM && onSelectDM(dm.id)}
                                    className={`w-full flex items-center justify-between px-4 py-2 rounded-lg text-sm transition-colors cursor-pointer ${
                                        isSelected
                                            ? 'bg-[#1164A3] text-white font-bold shadow-xs'
                                            : 'text-slate-700 hover:bg-[#F1F5F9] hover:text-slate-900'
                                    }`}
                                >
                                    <div className="flex items-center gap-2.5 truncate">
                                        {/* Avatar with Status Dot */}
                                        <div className="relative shrink-0">
                                            {dm.avatar ? (
                                                <img
                                                    alt={dm.name}
                                                    src={dm.avatar}
                                                    className="w-6 h-6 rounded-md object-cover"
                                                />
                                            ) : (
                                                <div className="w-6 h-6 rounded-md bg-slate-200 border border-slate-300 flex items-center justify-center text-[10px] font-bold text-slate-700">
                                                    {dm.initials || dm.name.charAt(0)}
                                                </div>
                                            )}
                                            <span
                                                className={`absolute -bottom-0.5 -right-0.5 w-2 h-2 rounded-full ring-1 ring-white ${
                                                    isOnline ? 'bg-emerald-500' : 'bg-slate-400'
                                                }`}
                                            />
                                        </div>

                                        <span className="truncate">{dm.name}</span>
                                    </div>

                                    {dm.unread > 0 && (
                                        <span className="bg-[#E01E5A] text-white text-[10px] font-extrabold px-2 py-0.2 rounded-full">
                                            {dm.unread}
                                        </span>
                                    )}
                                </button>
                            );
                        })}
                    </div>
                )}
            </div>

            {/* ========================================================= */}
            {/* 4. RECENT APPS SECTION */}
            {/* ========================================================= */}
            <div>
                <button
                    type="button"
                    onClick={() => toggleSection('apps')}
                    className="w-full flex items-center justify-between px-4 py-2.5 hover:bg-[#F1F5F9] transition-colors cursor-pointer group"
                >
                    <div className="flex items-center gap-2">
                        <svg className="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span className="font-bold text-sm text-[#0F172A] group-hover:text-[#1164A3] transition-colors">
                            Recent apps &gt;
                        </span>
                    </div>
                    <svg
                        className={`w-4 h-4 text-slate-400 transition-transform duration-200 ${
                            openSections.apps ? 'rotate-180' : ''
                        }`}
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {openSections.apps && (
                    <div className="space-y-0.5 px-4 pb-2">
                        {apps.length > 0 ? (
                            apps.map((app) => (
                                <button
                                    key={app.id}
                                    type="button"
                                    onClick={() => onToast && onToast(`Opening app: ${app.name}`)}
                                    className="w-full flex items-center gap-2.5 px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-[#F1F5F9] hover:text-slate-900 transition-colors cursor-pointer"
                                >
                                    <span className="text-base">{app.icon}</span>
                                    <span>{app.name}</span>
                                </button>
                            ))
                        ) : (
                            <div className="px-4 py-2 text-xs text-slate-500">
                                Google Drive, GitHub, and Canvas integrations ready
                            </div>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}
