import React, { useState, useMemo } from 'react';

/**
 * ConnectionsListView Component
 * Matches the reference image pixel-perfect:
 * - Header: "{count} connections", "Sort by: Recently added ▾", Search input, "Search with filters"
 * - Connection rows: Avatar with #OPENTOWORK badge, Full Name, Headline, Connected date, [Message] button, ••• menu
 * - Fully responsive on mobile, tablet, and desktop
 */
export default function ConnectionsListView({
    connections = [],
    onBack,
    onMessage,
    onRemoveConnection,
    onToast,
    onOpenProfile
}) {
    const [searchQuery, setSearchQuery] = useState('');
    const [sortBy, setSortBy] = useState('recently_added'); // 'recently_added' | 'first_name' | 'last_name'
    const [filterOpenToWorkOnly, setFilterOpenToWorkOnly] = useState(false);
    const [activeMenuId, setActiveMenuId] = useState(null);

    // Filter and sort connections
    const filteredConnections = useMemo(() => {          
        let result = [...connections];

        // 1. Search Query
        if (searchQuery.trim()) {
            const q = searchQuery.toLowerCase().trim();
            result = result.filter(
                (c) =>
                    c.name.toLowerCase().includes(q) ||
                    (c.headline && c.headline.toLowerCase().includes(q))
            );
        }

        // 2. Open to work filter
        if (filterOpenToWorkOnly) {
            result = result.filter((c) => c.isOpenToWork);
        }

        // 3. Sorting
        if (sortBy === 'first_name') {
            result.sort((a, b) => a.name.localeCompare(b.name));
        } else if (sortBy === 'last_name') {
            result.sort((a, b) => {
                const lastA = a.name.split(' ').slice(-1)[0] || '';
                const lastB = b.name.split(' ').slice(-1)[0] || '';
                return lastA.localeCompare(lastB);
            });
        }
        // default recently_added preserves chronological array order

        return result;
    }, [connections, searchQuery, sortBy, filterOpenToWorkOnly]);

    const handleMessageClick = (conn) => {
        if (onMessage) {
            onMessage(conn);
        } else {
            onToast && onToast(`Opening conversation with ${conn.name} 💬`);
        }
    };

    const handleRemoveClick = (connId, connName) => {
        setActiveMenuId(null);
        if (onRemoveConnection) {
            onRemoveConnection(connId);
        } else {
            onToast && onToast(`Removed ${connName} from connections.`);
        }
    };

    return (
        <section aria-label="Connections List" className="w-full space-y-3">
            {/* Mobile / Header Back Navigation */}
            <div className="flex items-center justify-between px-3.5 sm:px-0 pb-1">
                <button
                    type="button"
                    onClick={onBack}
                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer"
                >
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Back to Network</span>
                </button>
            </div>

            {/* Main Connections Container */}
            <div className="g-glass-card rounded-none sm:rounded-2xl border-x-0 sm:border-x border-y sm:border border-white/70 dark:border-slate-800/80 shadow-sm overflow-hidden">
                
                {/* Header Row matching Reference Image */}
                <div className="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 space-y-3">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        
                        {/* Title & Sort Filter */}
                        <div className="flex items-center gap-3 flex-wrap">
                            <h1 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                {connections.length} connections
                            </h1>
                            <div className="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                                <span>Sort by:</span>
                                <select
                                    value={sortBy}
                                    onChange={(e) => setSortBy(e.target.value)}
                                    className="bg-transparent font-semibold text-slate-800 dark:text-slate-200 border-none focus:ring-0 focus:outline-none cursor-pointer text-xs"
                                >
                                    <option value="recently_added" className="dark:bg-slate-900">Recently added</option>
                                    <option value="first_name" className="dark:bg-slate-900">First name</option>
                                    <option value="last_name" className="dark:bg-slate-900">Last name</option>
                                </select>
                            </div>
                        </div>

                        {/* Search & Search with Filters */}
                        <div className="flex items-center gap-2.5 w-full sm:w-auto">
                            {/* Search by name input */}
                            <div className="relative flex-1 sm:w-64">
                                <svg
                                    className="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>
                                <input
                                    type="text"
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    placeholder="Search by name"
                                    className="w-full pl-9 pr-7 py-1.5 bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-slate-200 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all"
                                />
                                {searchQuery && (
                                    <button
                                        type="button"
                                        onClick={() => setSearchQuery('')}
                                        className="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs"
                                    >
                                        ✕
                                    </button>
                                )}
                            </div>

                            {/* Search with filters button */}
                            <button
                                type="button"
                                onClick={() => setFilterOpenToWorkOnly(!filterOpenToWorkOnly)}
                                className={`text-xs font-bold whitespace-nowrap px-3 py-1.5 rounded-lg border transition-all cursor-pointer ${
                                    filterOpenToWorkOnly
                                        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-300 dark:border-emerald-700 shadow-2xs'
                                        : 'text-blue-600 dark:text-blue-400 border-transparent hover:border-slate-200 dark:hover:border-slate-700 hover:underline'
                                }`}
                            >
                                {filterOpenToWorkOnly ? '✓ Open to Work' : 'Search with filters'}
                            </button>
                        </div>
                    </div>
                </div>

                {/* Connection Rows matching Reference Image */}
                <div className="divide-y divide-slate-100 dark:divide-slate-800/80">
                    {filteredConnections.length > 0 ? (
                        filteredConnections.map((conn) => {
                            const isMenuOpen = activeMenuId === conn.id;

                            return (
                                <div
                                    key={conn.id}
                                    className="p-4 sm:p-5 flex items-start sm:items-center justify-between gap-3.5 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors relative"
                                >
                                    {/* Left: Avatar + Details */}
                                    <div className="flex items-start sm:items-center gap-3.5 min-w-0 flex-1">
                                        {/* Avatar with optional #OPENTOWORK badge frame */}
                                        <div 
                                            className="relative shrink-0 cursor-pointer"
                                            onClick={() => onOpenProfile && onOpenProfile(conn)}
                                            title={`View ${conn.name}'s profile`}
                                        >
                                            {conn.avatar ? (
                                                <div className="relative">
                                                    <img
                                                        src={conn.avatar}
                                                        alt={conn.name}
                                                        className={`w-12 h-12 sm:w-14 sm:h-14 rounded-full object-cover ${
                                                            conn.isOpenToWork
                                                                ? 'p-0.5 ring-2 ring-emerald-500 bg-white dark:bg-slate-900'
                                                                : 'ring-1 ring-slate-200 dark:ring-slate-700'
                                                        }`}
                                                    />
                                                    {conn.isOpenToWork && (
                                                        <span className="absolute -bottom-1 -right-1 text-[8px] font-extrabold uppercase px-1 py-0.2 bg-emerald-600 text-white rounded tracking-tighter leading-tight shadow-xs">
                                                            Open
                                                        </span>
                                                    )}
                                                </div>
                                            ) : (
                                                <div className="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-sm">
                                                    {conn.initials || conn.name.slice(0, 2).toUpperCase()}
                                                </div>
                                            )}
                                        </div>

                                        {/* Name, Headline & Connected Date */}
                                        <div className="min-w-0 flex-1 space-y-1">
                                            <div className="flex items-center gap-2 flex-wrap">
                                                <h2 
                                                    onClick={() => onOpenProfile && onOpenProfile(conn)}
                                                    className="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-tight hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer transition-colors"
                                                >
                                                    {conn.name}
                                                </h2>
                                                {conn.isOpenToWork && (
                                                    <span className="hidden sm:inline-flex items-center text-[10px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 px-1.5 py-0.5 rounded">
                                                        #OpenToWork
                                                    </span>
                                                )}
                                            </div>

                                            <p className="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed font-normal">
                                                {conn.headline}
                                            </p>

                                            <p className="text-[11px] text-slate-400 dark:text-slate-500">
                                                Connected on {conn.connectedDate}
                                            </p>
                                        </div>
                                    </div>

                                    {/* Right: Message & More Options Button */}
                                    <div className="flex items-center gap-2 shrink-0 self-center sm:self-center">
                                        <button
                                            type="button"
                                            onClick={() => handleMessageClick(conn)}
                                            className="px-4 py-1.5 rounded-full border border-blue-600 dark:border-blue-500 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/40 text-xs font-bold transition-all cursor-pointer active:scale-95 shadow-2xs"
                                        >
                                            Message
                                        </button>

                                        {/* Dropdown Menu for More Options */}
                                        <div className="relative">
                                            <button
                                                type="button"
                                                onClick={() => setActiveMenuId(isMenuOpen ? null : conn.id)}
                                                className="w-8 h-8 rounded-full flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer text-base"
                                                title="More actions"
                                            >
                                                •••
                                            </button>

                                            {isMenuOpen && (
                                                <div className="absolute right-0 top-9 w-44 bg-white dark:bg-slate-900 rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 py-1.5 z-20 text-xs">
                                                    <button
                                                        type="button"
                                                        onClick={() => {
                                                            setActiveMenuId(null);
                                                            onToast && onToast(`Visiting card shared with ${conn.name}`);
                                                        }}
                                                        className="w-full text-left px-3.5 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                                    >
                                                        Share Visiting Card
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() => {
                                                            setActiveMenuId(null);
                                                            navigator?.clipboard?.writeText(window.location.href);
                                                            onToast && onToast('Profile link copied to clipboard!');
                                                        }}
                                                        className="w-full text-left px-3.5 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                                    >
                                                        Copy Profile Link
                                                    </button>
                                                    <div className="h-px bg-slate-100 dark:bg-slate-800 my-1" />
                                                    <button
                                                        type="button"
                                                        onClick={() => handleRemoveClick(conn.id, conn.name)}
                                                        className="w-full text-left px-3.5 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors cursor-pointer font-medium"
                                                    >
                                                        Remove connection
                                                    </button>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            );
                        })
                    ) : (
                        <div className="p-8 text-center space-y-2">
                            <p className="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                No connections found matching "{searchQuery}"
                            </p>
                            <p className="text-xs text-slate-400">
                                Try searching for another name or clearing the filter.
                            </p>
                            <button
                                type="button"
                                onClick={() => {
                                    setSearchQuery('');
                                    setFilterOpenToWorkOnly(false);
                                }}
                                className="px-3.5 py-1.5 text-xs font-bold text-blue-600 hover:underline cursor-pointer"
                            >
                                Clear search &amp; filters
                            </button>
                        </div>
                    )}
                </div>

            </div>
        </section>
    );
}
