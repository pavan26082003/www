import React, { useState, useMemo, useEffect } from 'react';
import { getInitialNetworkState } from './networkState.js';
import gridApi from './gridApi.js';
import StitchPeerCard from './components/network/StitchPeerCard.jsx';
import NewslettersSection from './components/network/NewslettersSection.jsx';
import ManageNetworkCard from './components/network/ManageNetworkCard.jsx';
import PeopleYouMayKnow from './components/network/PeopleYouMayKnow.jsx';
import SubscribedSpacesCard from './components/network/SubscribedSpacesCard.jsx';
import ConnectionsListView from './components/network/ConnectionsListView.jsx';
import EventsListView from './components/network/EventsListView.jsx';
import PagesListView from './components/network/PagesListView.jsx';
import FollowingFollowersView from './components/network/FollowingFollowersView.jsx';

/**
 * GridNetworkApp Component
 * Modern LinkedIn-style Network Hub synchronized with Stitch design specification.
 * - Clean 2-column layout (No left sidebar; mid-portion + right rail)
 * - Right Rail: Manage my network -> People you may know -> Subscribed Spaces
 * - Mid Portion: Connections/Events/Pages View OR Default Feed
 */
export default function GridNetworkApp({ onNavigate, onToast, currentUser }) {
    const [networkData, setNetworkData] = useState(getInitialNetworkState);
    const [activeTab, setActiveTab] = useState('grow');
    const [selectedChip, setSelectedChip] = useState('all');
    const [clearedInvitations, setClearedInvitations] = useState(false);
    const [selectedManageView, setSelectedManageView] = useState(null); // 'connections' | 'following' | 'events' | 'pages' | null

    // Load live connections from backend API
    useEffect(() => {
        let isMounted = true;
        gridApi.getConnections().then((liveConnections) => {
            if (isMounted && Array.isArray(liveConnections) && liveConnections.length > 0) {
                setNetworkData((prev) => ({
                    ...prev,
                    connectionsList: liveConnections,
                    manageNetworkItems: (prev.manageNetworkItems || []).map((item) =>
                        item.id === 'connections'
                            ? { ...item, count: liveConnections.length.toLocaleString() }
                            : item
                    )
                }));
            }
        });
        return () => { isMounted = false; };
    }, []);

    const {
        manageNetworkItems = [],
        topicChips = [],
        pendingInvitations = [],
        recentActivityPeers = [],
        trendingHubPeers = [],
        newsletters = [],
        subscribedSpaces = [],
        recommendations = []
    } = networkData;

    // Handle Accepting an invitation
    const handleAcceptInvitation = (invitation) => {
        setNetworkData((prev) => ({
            ...prev,
            pendingInvitations: prev.pendingInvitations.filter((i) => i.id !== invitation.id),
            manageNetworkItems: prev.manageNetworkItems.map((item) =>
                item.id === 'connections'
                    ? { ...item, count: '1,429' }
                    : item
            )
        }));
        onToast && onToast(`Connected with ${invitation.name}! 🎉 Welcome them to your network.`);
    };

    // Handle Ignoring an invitation
    const handleIgnoreInvitation = (id) => {
        setNetworkData((prev) => ({
            ...prev,
            pendingInvitations: prev.pendingInvitations.filter((i) => i.id !== id)
        }));
        onToast && onToast('Invitation dismissed.');
    };

    // Handle Clear All Invitations
    const handleClearAllInvitations = () => {
        setClearedInvitations(true);
        setNetworkData((prev) => ({
            ...prev,
            pendingInvitations: []
        }));
        onToast && onToast('All pending invitations cleared.');
    };

    // Filter peers based on selected topic chip
    const filteredRecentPeers = useMemo(() => {
        if (selectedChip === 'all') return recentActivityPeers;
        if (selectedChip === 'ai') return recentActivityPeers.filter((p) => p.discipline === 'ai');
        if (selectedChip === 'design') return recentActivityPeers.filter((p) => p.discipline === 'design' || p.headline.toLowerCase().includes('design'));
        if (selectedChip === 'founders') return recentActivityPeers.filter((p) => p.discipline === 'founders' || p.headline.toLowerCase().includes('lead') || p.headline.toLowerCase().includes('architect'));
        if (selectedChip === 'hub') return recentActivityPeers;
        return recentActivityPeers;
    }, [recentActivityPeers, selectedChip]);

    const filteredTrendingPeers = useMemo(() => {
        if (selectedChip === 'all') return trendingHubPeers;
        if (selectedChip === 'ai') return trendingHubPeers.filter((p) => p.discipline === 'ai');
        if (selectedChip === 'design') return trendingHubPeers.filter((p) => p.discipline === 'design');
        if (selectedChip === 'founders') return trendingHubPeers.filter((p) => p.discipline === 'founders' || p.headline.toLowerCase().includes('lead') || p.headline.toLowerCase().includes('architect'));
        if (selectedChip === 'hub') return trendingHubPeers;
        return trendingHubPeers;
    }, [trendingHubPeers, selectedChip]);

    return (
        <div className="w-full font-sans">
            {/* Standard 2-Column Responsive Container (edge-to-edge on mobile, padded on desktop) */}
            <div className="max-w-7xl mx-auto px-0 sm:px-6 lg:px-8 py-2 sm:py-6 w-full">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-5 w-full items-start">

                    {/* ========================================================= */}
                    {/* MID PORTION: Selected Manage View (Connections/Events/Pages/Following) OR Default Feed */}
                    {/* ========================================================= */}
                    <main className="space-y-4 lg:col-span-8 xl:col-span-8 min-w-0 w-full" data-purpose="network-feed-area">

                        {selectedManageView === 'connections' ? (
                            <ConnectionsListView
                                connections={networkData.connectionsList || []}
                                onBack={() => {
                                    setSelectedManageView(null);
                                    window.scrollTo({ top: 0, behavior: 'smooth' });
                                }}
                                onMessage={(conn) => onToast && onToast(`Opening conversation with ${conn.name} 💬`)}
                                onOpenProfile={(p) => onNavigate && onNavigate('profile', { targetUser: p })}
                                onRemoveConnection={(connId) => {
                                    setNetworkData((prev) => {
                                        const nextList = (prev.connectionsList || []).filter((c) => c.id !== connId);
                                        return {
                                            ...prev,
                                            connectionsList: nextList,
                                            manageNetworkItems: (prev.manageNetworkItems || []).map((item) =>
                                                item.id === 'connections'
                                                    ? { ...item, count: nextList.length.toLocaleString() }
                                                    : item
                                            )
                                        };
                                    });
                                    onToast && onToast('Connection removed.');
                                }}
                                onToast={onToast}
                            />
                        ) : selectedManageView === 'events' ? (
                            <EventsListView
                                events={networkData.eventsList || []}
                                onBack={() => {
                                    setSelectedManageView(null);
                                    window.scrollTo({ top: 0, behavior: 'smooth' });
                                }}
                                onToast={onToast}
                            />
                        ) : selectedManageView === 'pages' ? (
                            <PagesListView
                                pages={networkData.pagesList || []}
                                onBack={() => {
                                    setSelectedManageView(null);
                                    window.scrollTo({ top: 0, behavior: 'smooth' });
                                }}
                                onToast={onToast}
                            />
                        ) : selectedManageView === 'following' ? (
                            <FollowingFollowersView
                                onBack={() => {
                                    setSelectedManageView(null);
                                    window.scrollTo({ top: 0, behavior: 'smooth' });
                                }}
                                onToast={onToast}
                            />
                        ) : (
                            <>
                                {/* Top Tab Bar & Pending Invitations Container */}
                                <div className="g-glass-card rounded-none sm:rounded-2xl border-x-0 sm:border-x border-y sm:border border-white/70 dark:border-slate-800/80 shadow-sm overflow-hidden" data-purpose="invitations-container">
                                    
                                    {/* Filter Tabs matching Stitch */}
                                    <div className="flex items-center justify-between px-4 sm:px-6 pt-3 sm:pt-4 border-b border-slate-100 dark:border-slate-800">
                                        <div className="flex items-center gap-3 sm:gap-6 overflow-x-auto scrollbar-none">
                                            <button
                                                type="button"
                                                onClick={() => setActiveTab('grow')}
                                                className={`pb-3 text-sm font-bold flex items-center gap-2 cursor-pointer transition-colors ${
                                                    activeTab === 'grow'
                                                        ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400'
                                                        : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'
                                                }`}
                                            >
                                                <span>Grow</span>
                                                <span className="bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 text-xs px-2 py-0.5 rounded-full font-bold">
                                                    New
                                                </span>
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => setActiveTab('catchup')}
                                                className={`pb-3 text-sm font-semibold transition-colors cursor-pointer ${
                                                    activeTab === 'catchup'
                                                        ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400'
                                                        : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'
                                                }`}
                                            >
                                                Catch up
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => setActiveTab('celebrations')}
                                                className={`pb-3 text-sm font-semibold transition-colors cursor-pointer ${
                                                    activeTab === 'celebrations'
                                                        ? 'text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400'
                                                        : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'
                                                }`}
                                            >
                                                Celebrations
                                            </button>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => onToast && onToast('Manage pending invitations')}
                                            className="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline pb-3 cursor-pointer"
                                        >
                                            Manage ({pendingInvitations.length})
                                        </button>
                                    </div>

                                    {/* Pending Invitations Box */}
                                    {pendingInvitations.length > 0 && !clearedInvitations ? (
                                        <div className="p-4 sm:p-5 bg-gradient-to-r from-slate-50/50 via-white/40 to-blue-50/20 dark:from-slate-900/40 dark:via-slate-900/60 dark:to-blue-950/10">
                                            <div className="flex items-center justify-between mb-4">
                                                <h3 className="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                                    <span>Pending Invitations</span>
                                                    <span className="bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold px-2 py-0.5 rounded-full">
                                                        {pendingInvitations.length}
                                                    </span>
                                                </h3>
                                                <button
                                                    type="button"
                                                    onClick={handleClearAllInvitations}
                                                    className="text-xs text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white font-medium cursor-pointer"
                                                >
                                                    Clear all
                                                </button>
                                            </div>

                                            {/* Featured Invitation Row */}
                                            {pendingInvitations.map((inv) => (
                                                <div
                                                    key={inv.id}
                                                    className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-3.5 g-glass-card rounded-xl border border-white/70 dark:border-slate-800/80 shadow-xs"
                                                >
                                                    <div className="flex items-start sm:items-center gap-3">
                                                        <div className="relative shrink-0">
                                                            <img
                                                                alt={inv.name}
                                                                src={inv.avatar}
                                                                className="w-12 h-12 rounded-full object-cover ring-2 ring-blue-100 dark:ring-blue-900"
                                                            />
                                                            {inv.isOnline && (
                                                                <span className="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 rounded-full ring-2 ring-white dark:ring-slate-900" />
                                                            )}
                                                        </div>
                                                        <div>
                                                            <div className="flex items-center gap-1.5 flex-wrap">
                                                                <h4 className="font-bold text-sm text-slate-900 dark:text-white">
                                                                    {inv.name}
                                                                </h4>
                                                                <span className="text-[10px] bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 px-1.5 py-0.5 font-semibold rounded">
                                                                    {inv.tag || '1st Mutual'}
                                                                </span>
                                                            </div>
                                                            <p className="text-xs text-slate-600 dark:text-slate-400 line-clamp-1 mt-0.5">
                                                                {inv.headline}
                                                            </p>
                                                            <p className="text-[11px] text-slate-400 dark:text-slate-500 flex items-center gap-1 mt-0.5">
                                                                <svg className="w-3 h-3 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                                                                </svg>
                                                                <span>{inv.mutualText}</span>
                                                            </p>
                                                        </div>
                                                    </div>

                                                    {/* Action Buttons */}
                                                    <div className="flex items-center gap-2 self-end sm:self-center shrink-0">
                                                        <button
                                                            type="button"
                                                            onClick={() => handleIgnoreInvitation(inv.id)}
                                                            className="px-3.5 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full border border-slate-300 dark:border-slate-700 transition-colors cursor-pointer"
                                                        >
                                                            Ignore
                                                        </button>
                                                        <button
                                                            type="button"
                                                            onClick={() => handleAcceptInvitation(inv)}
                                                            className="px-4 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-full shadow-xs transition-colors flex items-center gap-1 cursor-pointer"
                                                        >
                                                            <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Accept
                                                        </button>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <div className="p-4 text-center text-xs text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-900/40">
                                            ✨ You're all caught up! No pending connection requests.
                                        </div>
                                    )}
                                </div>

                                {/* Topic Pills / Skill Clusters (Chips) */}
                                <div className="flex items-center gap-2 overflow-x-auto px-3.5 sm:px-0 pb-1 scrollbar-none" data-purpose="industry-filter-chips">
                                    {topicChips.map((chip) => {
                                        const isSelected = selectedChip === chip.id;
                                        return (
                                            <button
                                                key={chip.id}
                                                type="button"
                                                onClick={() => setSelectedChip(chip.id)}
                                                className={`px-3.5 py-1.5 text-xs font-semibold rounded-full transition-all whitespace-nowrap cursor-pointer ${
                                                    isSelected
                                                        ? 'bg-slate-900 dark:bg-blue-600 text-white shadow-2xs'
                                                        : 'g-glass-card hover:bg-slate-100 dark:hover:bg-slate-800/80 border border-white/70 dark:border-slate-800/80 text-slate-700 dark:text-slate-300 shadow-xs'
                                                }`}
                                            >
                                                {chip.label}
                                            </button>
                                        );
                                    })}
                                </div>

                                {/* ========================================================= */}
                                {/* RECOMMENDATION SECTION 1: Recent Activity Peers */}
                                {/* ========================================================= */}
                                <section className="g-glass-card rounded-none sm:rounded-2xl border-x-0 sm:border-x border-y sm:border border-white/70 dark:border-slate-800/80 p-4 sm:p-5 shadow-sm space-y-4" data-purpose="people-you-may-know-section">
                                    <div className="flex items-center justify-between">
                                        <h3 className="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">
                                            People you may know based on your recent activity
                                        </h3>
                                        <button
                                            type="button"
                                            onClick={() => setSelectedChip('all')}
                                            className="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer shrink-0"
                                        >
                                            Show all
                                        </button>
                                    </div>

                                    {/* 2-Columns on Mobile (LinkedIn Style), 3-Cols on Tablet, 4-Cols on Desktop */}
                                    <div className="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-4">
                                        {filteredRecentPeers.map((peer) => (
                                            <StitchPeerCard
                                                key={peer.id}
                                                peer={peer}
                                                onToast={onToast}
                                                onOpenProfile={(p) => onNavigate && onNavigate('profile', { targetUser: p })}
                                            />
                                        ))}
                                    </div>

                                    {/* LinkedIn-style Load More Button */}
                                    <button
                                        type="button"
                                        onClick={() => onToast && onToast('Loading more peer recommendations...')}
                                        className="w-full py-2 bg-white/60 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 rounded-full text-xs font-bold shadow-xs mt-2 transition-all cursor-pointer"
                                    >
                                        Load more
                                    </button>
                                </section>

                                {/* ========================================================= */}
                                {/* RECOMMENDATION SECTION 2: Trending in Tech Hub (Open To Work) */}
                                {/* ========================================================= */}
                                <section className="g-glass-card rounded-none sm:rounded-2xl border-x-0 sm:border-x border-y sm:border border-white/70 dark:border-slate-800/80 p-4 sm:p-5 shadow-sm space-y-4" data-purpose="regional-hub-recommendations">
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center gap-2">
                                            <h3 className="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">
                                                Trending in Greater AI &amp; Systems Engineering Hub
                                            </h3>
                                            <span className="text-[9px] sm:text-[10px] bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold px-1.5 sm:px-2 py-0.5 rounded-md border border-emerald-200 dark:border-emerald-800">
                                                Actively Hiring
                                            </span>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => onToast && onToast('Viewing all 140 trending tech peers')}
                                            className="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer shrink-0"
                                        >
                                            View all (140)
                                        </button>
                                    </div>

                                    {/* 2-Columns on Mobile (LinkedIn Style) with Open to Work badges */}
                                    <div className="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-4">
                                        {filteredTrendingPeers.map((peer) => (
                                            <StitchPeerCard
                                                key={peer.id}
                                                peer={peer}
                                                onToast={onToast}
                                                onOpenProfile={(p) => onNavigate && onNavigate('profile', { targetUser: p })}
                                            />
                                        ))}
                                    </div>
                                </section>

                                {/* ========================================================= */}
                                {/* SPOTLIGHT SECTION: Engineering Newsletters & Design Systems */}
                                {/* ========================================================= */}
                                <NewslettersSection
                                    newsletters={newsletters}
                                    onToast={onToast}
                                />
                            </>
                        )}

                    </main>

                    {/* ========================================================= */}
                    {/* RIGHT SIDEBAR: Manage my network -> People you may know -> Subscribed Spaces */}
                    {/* ========================================================= */}
                    <aside className={`space-y-4 lg:col-span-4 xl:col-span-4 min-w-0 ${selectedManageView ? 'hidden lg:block' : ''}`} data-purpose="network-sidebar">
                        
                        {/* 1. Manage my network (Top of right column) */}
                        <ManageNetworkCard
                            items={manageNetworkItems}
                            activeFilter={selectedManageView}
                            onFilterSelect={(itemId) => {
                                setSelectedManageView(itemId === selectedManageView ? null : itemId);
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            }}
                        />

                        {/* 2. People you may know & 3. Subscribed Spaces (Hidden when an option is selected) */}
                        {!selectedManageView && (
                            <>
                                <PeopleYouMayKnow
                                    recommendations={recommendations}
                                    onToast={onToast}
                                />

                                <SubscribedSpacesCard
                                    spaces={subscribedSpaces}
                                    onNavigate={onNavigate}
                                    onToast={onToast}
                                />
                            </>
                        )}

                    </aside>

                </div>
            </div>
        </div>
    );
}
