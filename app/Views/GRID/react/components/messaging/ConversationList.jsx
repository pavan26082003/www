import React from 'react';
import { filterTabs } from '../../messagingState.js';
import VerifiedBadge from '../ui/VerifiedBadge.jsx';

export default function ConversationList({
    conversations = [],
    activeId,
    onSelectConversation,
    activeTab = 'focused',
    onSelectTab,
    searchQuery = '',
    onSearchChange,
    onComposeNew,
    className = '',
}) {
    // Filter conversations based on tab and search
    const filteredConversations = conversations.filter(conv => {
        const matchesSearch = !searchQuery || 
            conv.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
            conv.headline.toLowerCase().includes(searchQuery.toLowerCase()) ||
            conv.lastMessage.toLowerCase().includes(searchQuery.toLowerCase());

        if (!matchesSearch) return false;

        if (activeTab === 'focused') return true;
        if (activeTab === 'unread') return (conv.unreadCount || 0) > 0 || conv.category === 'unread';
        if (activeTab === 'jobs') return conv.category === 'jobs';
        if (activeTab === 'connections') return conv.category === 'connections' || conv.connectionDegree === '1st';
        if (activeTab === 'efactor') return conv.category === 'efactor' || conv.verified;
        if (activeTab === 'starred') return conv.isStarred;

        return true;
    });

    const pillScrollRef = React.useRef(null);

    const handlePillWheel = (e) => {
        if (pillScrollRef.current && e.deltaY !== 0) {
            pillScrollRef.current.scrollLeft += e.deltaY * 0.9;
        }
    };

    return (
        <section className={`g-msg-threads-col w-full md:w-[390px] lg:w-[415px] xl:w-[435px] border-r border-slate-200/80 dark:border-slate-800 shrink-0 min-h-0 h-full ${className}`}>
            {/* Header & Action Row */}
            <div className="p-4 border-b border-slate-200/80 dark:border-slate-800 g-msg-threads-header shrink-0">
                <div className="flex items-center justify-between mb-3">
                    <h1 className="text-xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">Messaging</h1>
                    <div className="flex items-center gap-1">
                        <button 
                            type="button" 
                            className="p-1.5 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" 
                            title="Filter Options"
                            aria-label="Filter Options"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                            </svg>
                        </button>
                        <button 
                            type="button" 
                            onClick={onComposeNew}
                            className="p-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors" 
                            title="Compose New Message"
                            aria-label="Compose New Message"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {/* Thread Search */}
                <div className="relative">
                    <svg className="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input 
                        type="text" 
                        value={searchQuery}
                        onChange={(e) => onSearchChange(e.target.value)}
                        placeholder="Search messages..." 
                        className="w-full bg-slate-100/70 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 rounded-lg pl-9 pr-8 py-1.5 text-xs text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-900"
                    />
                    {searchQuery && (
                        <button
                            type="button"
                            onClick={() => onSearchChange('')}
                            className="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                            title="Clear search"
                        >
                            <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    )}
                </div>
            </div>

            {/* Filter Tabs / Pills Tray (Horizontal Scroll with Wheel Support) */}
            <div className="relative border-b border-slate-200/80 dark:border-slate-800 g-msg-filter-pills shrink-0">
                <div 
                    ref={pillScrollRef}
                    onWheel={handlePillWheel}
                    className="px-3 py-2 flex items-center gap-1.5 overflow-x-auto text-xs font-medium no-scrollbar select-none"
                    style={{ scrollBehavior: 'smooth' }}
                >
                    {filterTabs.map((tab) => {
                        const isActive = activeTab === tab.id;
                        return (
                            <button
                                key={tab.id}
                                type="button"
                                onClick={() => onSelectTab(tab.id)}
                                className={`px-2.5 py-1 rounded-full flex items-center gap-1 shrink-0 whitespace-nowrap transition-all text-[11px] sm:text-xs ${
                                    isActive 
                                        ? 'bg-blue-600 text-white font-semibold shadow-xs' 
                                        : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300'
                                }`}
                            >
                                {tab.id === 'efactor' && (
                                    <svg className="w-3 h-3 text-emerald-500 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944z" clipRule="evenodd" />
                                    </svg>
                                )}
                                <span>{tab.label}</span>
                                {tab.hasArrow && (
                                    <svg className="w-3 h-3 ml-0.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                )}
                            </button>
                        );
                    })}
                </div>
            </div>

            {/* Scrollable Thread List */}
            <div className="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
                {filteredConversations.length === 0 ? (
                    <div className="p-8 text-center text-xs text-slate-400">
                        No conversations found.
                    </div>
                ) : (
                    filteredConversations.map((conv) => {
                        const isSelected = activeId === conv.id;
                        return (
                            <article
                                key={conv.id}
                                onClick={() => onSelectConversation(conv.id)}
                                className={`p-3.5 cursor-pointer flex gap-3 transition-colors relative ${
                                    isSelected
                                        ? 'bg-blue-50/60 dark:bg-blue-950/30 border-l-4 border-blue-600'
                                        : 'hover:bg-slate-50/80 dark:hover:bg-slate-800/30'
                                }`}
                            >
                                <div className="relative shrink-0">
                                    {conv.avatar ? (
                                        <img 
                                            src={conv.avatar} 
                                            alt={conv.name} 
                                            className="w-11 h-11 rounded-full object-cover border border-slate-200 dark:border-slate-700" 
                                        />
                                    ) : (
                                        <div className="w-11 h-11 rounded-xl bg-slate-900 text-white font-bold flex items-center justify-center text-xs border border-slate-700">
                                            {conv.groupBadge || conv.name.slice(0, 2).toUpperCase()}
                                        </div>
                                    )}

                                    {/* Online indicator dot */}
                                    {conv.isOnline && (
                                        <span 
                                            className="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-900" 
                                            title="Online now" 
                                        />
                                    )}
                                </div>

                                <div className="flex-1 min-w-0">
                                    <div className="flex items-center justify-between mb-0.5">
                                        <h3 className="text-xs font-bold text-slate-900 dark:text-slate-100 truncate flex items-center gap-1">
                                            <span>{conv.name}</span>
                                            {(conv.verified || conv.isPro) && (
                                                <VerifiedBadge size="xs" />
                                            )}
                                        </h3>
                                        <time className={`text-[11px] font-medium ${isSelected ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-400'}`}>
                                            {conv.lastTime}
                                        </time>
                                    </div>

                                    <p className="text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate mb-1">
                                        {conv.role || conv.headline}
                                    </p>

                                    <div className="flex items-center justify-between">
                                        <p className="text-xs text-slate-600 dark:text-slate-300 truncate">
                                            {conv.lastMessage}
                                        </p>
                                        {(conv.unreadCount || 0) > 0 && (
                                            <span className="w-4 h-4 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center shrink-0 ml-1">
                                                {conv.unreadCount}
                                            </span>
                                        )}
                                    </div>
                                </div>
                            </article>
                        );
                    })
                )}
            </div>
        </section>
    );
}
