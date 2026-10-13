import React, { useState } from 'react';

/**
 * TeamDirectoryView Component (Matches Screen 5 Main Content Area)
 * - Tabs: People (active), Channels, User Groups, External, Invitations
 * - Search bar: "Search for people" + "Invite People" CTA button
 * - Filter bar: Title ▾, Location ▾, Filters, Most recommended ▾
 * - Member Cards Grid: Photo card + Name + Role + Location + Direct "Message" button
 */
export default function TeamDirectoryView({
    directoryMembers = [],
    channels = [],
    currentUser,
    onDirectMessageUser,
    onInviteTeammates,
    onToast
}) {
    const [activeTab, setActiveTab] = useState('people'); // 'people' | 'channels' | 'groups' | 'external' | 'invitations'
    const [searchQuery, setSearchQuery] = useState('');
    const [selectedTitle, setSelectedTitle] = useState('All');
    const [selectedLocation, setSelectedLocation] = useState('All');

    // Default directory members if not populated
    const members = directoryMembers.length > 0 ? directoryMembers : [
        {
            id: 'dir-pavan',
            userId: 'user-pavan',
            name: 'K Pavan Kumar',
            role: 'Lead AI Engineer & Workspace Owner',
            department: 'Core Architecture',
            location: 'Hyderabad, IN',
            email: 'umarpavan768@gmail.com',
            status: 'online',
            avatar: '',
            initials: 'PK',
            tags: ['AI', 'PyTorch', 'System Arch']
        },
        {
            id: 'dir-sai',
            userId: 'user-sai',
            name: 'Sai Tulasi Ram',
            role: 'Senior Machine Learning Systems Architect',
            department: 'Neural Pipelines',
            location: 'Bangalore, IN',
            email: 'sai.tulasiram@grid.ai',
            status: 'away',
            avatar: '',
            initials: 'ST',
            tags: ['Deep Learning', 'Attention Heads', 'Kubernetes']
        },
        {
            id: 'dir-elena',
            userId: 'user-elena',
            name: 'Elena Rostova',
            role: 'Lead Research Scientist & NLP Specialist',
            department: 'Research Lab',
            location: 'San Francisco, CA',
            email: 'elena.rostova@grid.ai',
            status: 'online',
            avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=300&auto=format&fit=crop&q=80',
            initials: 'ER',
            tags: ['Transformers', 'Alignment', 'CUDA']
        },
        {
            id: 'dir-marcus',
            userId: 'user-marcus',
            name: 'Marcus Vance',
            role: 'Infrastructure & High-Throughput Cluster Lead',
            department: 'DevOps & Infra',
            location: 'Austin, TX',
            email: 'marcus.vance@grid.ai',
            status: 'offline',
            avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&auto=format&fit=crop&q=80',
            initials: 'MV',
            tags: ['Clusters', 'Docker', 'Distributed']
        }
    ];

    const q = searchQuery.toLowerCase().trim();
    const filteredMembers = members.filter(m => {
        const matchesQuery = !q || m.name.toLowerCase().includes(q) || m.role.toLowerCase().includes(q) || m.department.toLowerCase().includes(q);
        const matchesTitle = selectedTitle === 'All' || m.role.toLowerCase().includes(selectedTitle.toLowerCase());
        const matchesLoc = selectedLocation === 'All' || m.location.toLowerCase().includes(selectedLocation.toLowerCase());
        return matchesQuery && matchesTitle && matchesLoc;
    });

    return (
        <div className="flex-1 h-full bg-[#FFFFFF] text-slate-900 flex flex-col overflow-y-auto">
            {/* Top Page Header */}
            <div className="px-8 pt-6 pb-2 shrink-0">
                <h1 className="text-2xl font-bold tracking-tight text-slate-900 mb-4">Directories</h1>

                {/* Subtabs Bar */}
                <div className="flex items-center gap-6 border-b border-slate-200 text-sm font-medium">
                    <button
                        type="button"
                        onClick={() => setActiveTab('people')}
                        className={`pb-3 flex items-center gap-2 border-b-2 transition-colors ${
                            activeTab === 'people'
                                ? 'border-[#3F0E40] text-slate-900 font-bold'
                                : 'border-transparent text-slate-500 hover:text-slate-800'
                        }`}
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>People</span>
                        <span className="ml-1 text-xs bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-full font-semibold">
                            {members.length}
                        </span>
                    </button>

                    <button
                        type="button"
                        onClick={() => setActiveTab('channels')}
                        className={`pb-3 flex items-center gap-2 border-b-2 transition-colors ${
                            activeTab === 'channels'
                                ? 'border-[#3F0E40] text-slate-900 font-bold'
                                : 'border-transparent text-slate-500 hover:text-slate-800'
                        }`}
                    >
                        <span className="font-semibold text-slate-400">#</span>
                        <span>Channels</span>
                        <span className="ml-1 text-xs bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-full font-semibold">
                            {channels.length + 1}
                        </span>
                    </button>

                    <button
                        type="button"
                        onClick={() => setActiveTab('groups')}
                        className={`pb-3 flex items-center gap-2 border-b-2 transition-colors ${
                            activeTab === 'groups'
                                ? 'border-[#3F0E40] text-slate-900 font-bold'
                                : 'border-transparent text-slate-500 hover:text-slate-800'
                        }`}
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>User Groups</span>
                    </button>

                    <button
                        type="button"
                        onClick={() => setActiveTab('external')}
                        className={`pb-3 flex items-center gap-2 border-b-2 transition-colors ${
                            activeTab === 'external'
                                ? 'border-[#3F0E40] text-slate-900 font-bold'
                                : 'border-transparent text-slate-500 hover:text-slate-800'
                        }`}
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>External</span>
                    </button>

                    <button
                        type="button"
                        onClick={() => setActiveTab('invitations')}
                        className={`pb-3 flex items-center gap-2 border-b-2 transition-colors ${
                            activeTab === 'invitations'
                                ? 'border-[#3F0E40] text-slate-900 font-bold'
                                : 'border-transparent text-slate-500 hover:text-slate-800'
                        }`}
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>Invitations</span>
                    </button>
                </div>
            </div>

            {/* Main Tab: People View */}
            {activeTab === 'people' && (
                <div className="px-8 py-5 flex-1 flex flex-col">
                    {/* Search bar & Invite People CTA */}
                    <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-4">
                        <div className="relative flex-1">
                            <input
                                type="text"
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                placeholder="Search for people"
                                className="w-full pl-10 pr-4 py-2 text-sm bg-white border border-slate-300 focus:border-[#1164A3] focus:ring-2 focus:ring-[#1164A3]/20 rounded-lg text-slate-900 placeholder-slate-400 outline-none transition-all shadow-sm"
                            />
                            <svg className="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            {searchQuery && (
                                <button
                                    type="button"
                                    onClick={() => setSearchQuery('')}
                                    className="absolute right-3 top-2.5 text-xs text-slate-400 hover:text-slate-600"
                                >
                                    ✕
                                </button>
                            )}
                        </div>

                        <button
                            type="button"
                            onClick={onInviteTeammates}
                            className="px-5 py-2 text-sm font-semibold border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 text-slate-800 rounded-lg transition-all shadow-sm shrink-0 flex items-center justify-center gap-2"
                        >
                            <span>Invite People</span>
                        </button>
                    </div>

                    {/* Filter Row: Title ▾, Location ▾, Filters, Most recommended ▾ */}
                    <div className="flex items-center justify-between gap-3 text-xs text-slate-700 mb-6 flex-wrap">
                        <div className="flex items-center gap-2">
                            {/* Title Dropdown */}
                            <select
                                value={selectedTitle}
                                onChange={(e) => setSelectedTitle(e.target.value)}
                                className="px-3 py-1.5 bg-white border border-slate-300 rounded-md hover:border-slate-400 focus:outline-none cursor-pointer font-medium"
                            >
                                <option value="All">Title</option>
                                <option value="AI">AI / Machine Learning</option>
                                <option value="Lead">Team Lead</option>
                                <option value="Architect">Architect</option>
                                <option value="Infra">Infrastructure</option>
                            </select>

                            {/* Location Dropdown */}
                            <select
                                value={selectedLocation}
                                onChange={(e) => setSelectedLocation(e.target.value)}
                                className="px-3 py-1.5 bg-white border border-slate-300 rounded-md hover:border-slate-400 focus:outline-none cursor-pointer font-medium"
                            >
                                <option value="All">Location</option>
                                <option value="Hyderabad">Hyderabad</option>
                                <option value="Bangalore">Bangalore</option>
                                <option value="San Francisco">San Francisco</option>
                                <option value="Austin">Austin</option>
                            </select>

                            {/* Filters button */}
                            <button
                                type="button"
                                onClick={() => onToast && onToast('Filters applied')}
                                className="px-3 py-1.5 bg-white border border-slate-300 rounded-md hover:border-slate-400 font-medium flex items-center gap-1.5 transition-colors"
                            >
                                <svg className="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <span>Filters</span>
                            </button>
                        </div>

                        {/* Right: Most recommended dropdown */}
                        <div className="flex items-center gap-1.5">
                            <span className="text-slate-500">Sort by:</span>
                            <select className="px-3 py-1.5 bg-white border border-slate-300 rounded-md hover:border-slate-400 focus:outline-none cursor-pointer font-medium">
                                <option>Most recommended</option>
                                <option>Name (A-Z)</option>
                                <option>Recently joined</option>
                            </select>
                        </div>
                    </div>

                    {/* Member Cards Grid: 4 columns matching Image 5 */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 pb-8">
                        {filteredMembers.map(member => {
                            const isMe = member.userId === currentUser?.id;
                            return (
                                <div
                                    key={member.id}
                                    className="bg-white border border-slate-200 hover:border-slate-300 hover:shadow-md rounded-xl p-4 flex flex-col justify-between transition-all group"
                                >
                                    <div>
                                        {/* Top Square Avatar / Photo Frame */}
                                        <div className="w-full aspect-[4/3] rounded-lg bg-slate-100 mb-3 overflow-hidden flex items-center justify-center relative border border-slate-100">
                                            {member.avatar ? (
                                                <img
                                                    src={member.avatar}
                                                    alt={member.name}
                                                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                />
                                            ) : (
                                                <div className="w-16 h-16 rounded-2xl bg-[#3F0E40] text-white text-xl font-bold flex items-center justify-center shadow-inner">
                                                    {member.initials}
                                                </div>
                                            )}

                                            {/* Status Dot */}
                                            <span
                                                className={`absolute bottom-2 right-2 w-3.5 h-3.5 rounded-full border-2 border-white shadow-sm ${
                                                    member.status === 'online' ? 'bg-emerald-500' : member.status === 'away' ? 'bg-amber-500' : 'bg-slate-400'
                                                }`}
                                            />
                                        </div>

                                        {/* Name & Role */}
                                        <div className="mb-2">
                                            <div className="flex items-center gap-1.5 font-bold text-sm text-slate-900 group-hover:text-[#1164A3] transition-colors">
                                                <span>{member.name}</span>
                                                {isMe && <span className="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.2 rounded font-semibold">you</span>}
                                            </div>
                                            <div className="text-xs text-slate-500 line-clamp-2 mt-0.5 leading-relaxed">
                                                {member.role}
                                            </div>
                                        </div>

                                        {/* Location */}
                                        <div className="flex items-center gap-1.5 text-[11px] text-slate-400 mb-3">
                                            <svg className="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span className="truncate">{member.location}</span>
                                        </div>
                                    </div>

                                    {/* Action button: Direct Message */}
                                    <div className="pt-2 border-t border-slate-100 mt-2">
                                        <button
                                            type="button"
                                            onClick={() => onDirectMessageUser(member.userId)}
                                            className="w-full py-1.5 px-3 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-[#3F0E40] hover:text-white text-slate-700 transition-colors flex items-center justify-center gap-1.5"
                                        >
                                            <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            <span>Message</span>
                                        </button>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            )}

            {/* Tab: Channels directory */}
            {activeTab === 'channels' && (
                <div className="px-8 py-5 flex-1">
                    <div className="text-sm font-bold text-slate-800 mb-3">Workspace Channels</div>
                    <div className="space-y-2 max-w-2xl">
                        <div className="p-3.5 border border-slate-200 rounded-xl flex items-center justify-between hover:bg-slate-50">
                            <div>
                                <div className="font-bold text-sm text-[#1164A3]"># GRID (My team)</div>
                                <div className="text-xs text-slate-500">GRID Platform Architecture & Core Sync</div>
                            </div>
                            <span className="text-xs font-semibold text-slate-500">2 members</span>
                        </div>
                        {channels.map(c => (
                            <div key={c.id} className="p-3.5 border border-slate-200 rounded-xl flex items-center justify-between hover:bg-slate-50">
                                <div>
                                    <div className="font-bold text-sm text-[#1164A3]"># {c.name}</div>
                                    <div className="text-xs text-slate-500">{c.topic}</div>
                                </div>
                                <span className="text-xs font-semibold text-slate-500">{c.memberCount} members</span>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Tab: Invitations / Other placeholder */}
            {['groups', 'external', 'invitations'].includes(activeTab) && (
                <div className="px-8 py-16 flex-1 flex flex-col items-center justify-center text-center">
                    <div className="w-12 h-12 rounded-full bg-purple-50 text-[#3F0E40] text-xl flex items-center justify-center mb-3">
                        📇
                    </div>
                    <h3 className="font-bold text-slate-800 text-base mb-1 capitalize">{activeTab} Directory</h3>
                    <p className="text-xs text-slate-500 max-w-sm mb-4">
                        All workspace members, invited collaborators, and external guest connections appear here.
                    </p>
                    <button
                        type="button"
                        onClick={onInviteTeammates}
                        className="px-4 py-2 text-xs font-bold text-white bg-[#3F0E40] hover:bg-[#522653] rounded-lg transition-colors"
                    >
                        Send an Invitation
                    </button>
                </div>
            )}
        </div>
    );
}
