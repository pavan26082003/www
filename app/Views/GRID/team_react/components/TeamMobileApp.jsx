import React, { useState } from 'react';
import TeamMobileHub from './TeamMobileHub.jsx';
import TeamChatStream from './TeamChatStream.jsx';

/**
 * TeamMobileApp Component
 * Master controller for the mobile Slack-style GRID Team Workspace (< 768px).
 * Replaces the TeamMobileGate with a native, responsive experience.
 * Manages transitions between the Mobile Navigation Hub and Full-screen Chat Streams.
 */
export default function TeamMobileApp({
    workspace,
    workspaces = [],
    currentUser,
    myTeam,
    channels = [],
    directMessages = [],
    apps = [],
    activeItemId,
    messages = {},
    onSelectChannel,
    onSelectDM,
    onAddChannel,
    onSwitchWorkspace,
    onSendMessage,
    onAddReaction,
    onExitToGrid,
    onToast
}) {
    const [mobileView, setMobileView] = useState('hub'); // 'hub' | 'chat'
    const [mobileTab, setMobileTab] = useState('home'); // 'home' | 'dms' | 'activity' | 'search' | 'more'
    const [isFabMenuOpen, setIsFabMenuOpen] = useState(false);

    const mobileViewRef = React.useRef(mobileView);
    mobileViewRef.current = mobileView;

    const isFabMenuOpenRef = React.useRef(isFabMenuOpen);
    isFabMenuOpenRef.current = isFabMenuOpen;

    const hasPushedHubRef = React.useRef(false);
    const lastBackPressTimeRef = React.useRef(0);

    // Sync mobile navigation with browser history to handle swipe-back gestures gracefully
    React.useEffect(() => {
        if (!hasPushedHubRef.current) {
            try {
                window.history.pushState({ gridTeamMobile: 'hub' }, '', window.location.href);
                hasPushedHubRef.current = true;
            } catch (err) {}
        }

        const handlePopState = (e) => {
            // 1. If currently inside Chat view, back gesture/button returns to Hub
            if (mobileViewRef.current === 'chat') {
                setMobileView('hub');
                return;
            }

            // 2. If FAB sheet/modal is open, back gesture closes it
            if (isFabMenuOpenRef.current) {
                setIsFabMenuOpen(false);
                return;
            }

            // 3. If on Hub: Prevent accidental exit from back gestures. Double-back within 2s required to exit.
            const now = Date.now();
            if (now - lastBackPressTimeRef.current > 2000) {
                lastBackPressTimeRef.current = now;
                try {
                    window.history.pushState({ gridTeamMobile: 'hub' }, '', window.location.href);
                } catch (err) {}
                if (onToast) onToast('Swipe back again to exit Team');
            } else {
                if (onExitToGrid) {
                    onExitToGrid();
                } else {
                    window.location.href = '/index.php/grid';
                }
            }
        };

        window.addEventListener('popstate', handlePopState);
        return () => window.removeEventListener('popstate', handlePopState);
    }, [onToast, onExitToGrid]);

    // Touch swipe-to-back gesture detection for in-app swipe back
    const touchStartRef = React.useRef({ x: 0, y: 0, time: 0 });

    const handleTouchStart = (e) => {
        if (e.touches && e.touches.length === 1) {
            touchStartRef.current = {
                x: e.touches[0].clientX,
                y: e.touches[0].clientY,
                time: Date.now()
            };
        }
    };

    const handleTouchEnd = (e) => {
        if (e.changedTouches && e.changedTouches.length === 1) {
            const deltaX = e.changedTouches[0].clientX - touchStartRef.current.x;
            const deltaY = e.changedTouches[0].clientY - touchStartRef.current.y;
            const deltaTime = Date.now() - touchStartRef.current.time;

            // Swipe from left edge (start X < 65px) moving right (deltaX > 60px)
            if (touchStartRef.current.x < 65 && deltaX > 60 && Math.abs(deltaY) < 60 && deltaTime < 500) {
                if (mobileViewRef.current === 'chat') {
                    handleBackToHub();
                }
            }
        }
    };

    // Determine current active conversation item
    const isDirectMessage = activeItemId?.startsWith('dm-');
    let activeItem = null;
    if (isDirectMessage) {
        activeItem = directMessages.find(dm => dm.id === activeItemId) || directMessages[0];
    } else {
        activeItem = myTeam?.channels?.find(c => c.id === activeItemId) ||
                     channels.find(c => c.id === activeItemId) ||
                     myTeam?.channels?.[0] ||
                     channels[0];
    }

    const currentMessages = messages[activeItemId] || [];

    // Handlers for channel / DM selection in mobile view
    const handleChannelClick = (channelId) => {
        if (onSelectChannel) onSelectChannel(channelId);
        try {
            window.history.pushState({ gridTeamMobile: 'chat', channelId }, '', window.location.href);
        } catch (err) {}
        setMobileView('chat');
    };

    const handleDMClick = (dmId) => {
        if (onSelectDM) onSelectDM(dmId);
        try {
            window.history.pushState({ gridTeamMobile: 'chat', dmId }, '', window.location.href);
        } catch (err) {}
        setMobileView('chat');
    };

    const handleBackToHub = () => {
        if (window.history.state && window.history.state.gridTeamMobile === 'chat') {
            window.history.back();
        } else {
            setMobileView('hub');
        }
    };

    const handleTabChange = (tabId) => {
        setMobileTab(tabId);
        if (tabId === 'home') {
            setMobileView('hub');
        } else if (tabId === 'dms') {
            setMobileView('hub');
            onToast && onToast('Showing Direct Messages');
        } else if (tabId === 'activity') {
            onToast && onToast('Activity & Mentions stream');
        } else if (tabId === 'search') {
            const query = window.prompt('Search GRID Team Workspace:');
            if (query) onToast && onToast(`Searching for "${query}"...`);
        } else if (tabId === 'more') {
            setIsFabMenuOpen(true);
        }
    };

    const handleFabClick = () => {
        setIsFabMenuOpen(true);
    };

    return (
        <div
            onTouchStart={handleTouchStart}
            onTouchEnd={handleTouchEnd}
            className="w-full h-full h-[100dvh] bg-[#F8FAFC] text-slate-900 flex flex-col font-sans overflow-hidden relative overscroll-none"
            style={{ overscrollBehaviorX: 'none', overscrollBehaviorY: 'none', overscrollBehavior: 'none' }}
        >
            {/* VIEW 1: Mobile Navigation Hub */}
            {mobileView === 'hub' && (
                <TeamMobileHub
                    workspace={workspace}
                    workspaces={workspaces}
                    currentUser={currentUser}
                    myTeam={myTeam}
                    channels={channels}
                    directMessages={directMessages}
                    apps={apps}
                    activeItemId={activeItemId}
                    activeTab={mobileTab}
                    onTabChange={handleTabChange}
                    onSelectChannel={handleChannelClick}
                    onSelectDM={handleDMClick}
                    onAddChannel={onAddChannel}
                    onSwitchWorkspace={onSwitchWorkspace}
                    onFabClick={handleFabClick}
                    onToast={onToast}
                />
            )}

            {/* VIEW 2: Full-screen Mobile Chat Stream */}
            {mobileView === 'chat' && (
                <div className="w-full h-full flex flex-col bg-white text-slate-900">
                    <TeamChatStream
                        activeItem={activeItem}
                        isDirectMessage={isDirectMessage}
                        messages={currentMessages}
                        currentUser={currentUser}
                        onSendMessage={onSendMessage}
                        onAddReaction={onAddReaction}
                        onBack={handleBackToHub}
                        onToast={onToast}
                    />
                </div>
            )}

            {/* Quick Action FAB Bottom Sheet Modal */}
            {isFabMenuOpen && (
                <div className="fixed inset-0 z-50 flex items-end justify-center">
                    <div
                        className="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity"
                        onClick={() => setIsFabMenuOpen(false)}
                    />
                    <div className="relative w-full max-w-md bg-white border-t border-[#CBD5E1] rounded-t-3xl p-5 shadow-2xl space-y-3 z-10 animate-in slide-in-from-bottom duration-200">
                        <div className="w-10 h-1 bg-slate-300 rounded-full mx-auto mb-2" />
                        <h3 className="text-sm font-bold text-slate-900 tracking-tight">Create or Collaborate</h3>

                        <div className="space-y-1 pt-1">
                            <button
                                type="button"
                                onClick={() => {
                                    setIsFabMenuOpen(false);
                                    if (directMessages.length > 0) handleDMClick(directMessages[0].id);
                                }}
                                className="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-[#F1F5F9] text-slate-700 hover:text-slate-900 transition-colors cursor-pointer text-left"
                            >
                                <span className="w-8 h-8 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600">
                                    💬
                                </span>
                                <div>
                                    <div className="text-xs font-bold text-slate-900">New Direct Message</div>
                                    <div className="text-[11px] text-slate-500">Start a chat with a teammate</div>
                                </div>
                            </button>

                            <button
                                type="button"
                                onClick={() => {
                                    setIsFabMenuOpen(false);
                                    onAddChannel && onAddChannel();
                                }}
                                className="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-[#F1F5F9] text-slate-700 hover:text-slate-900 transition-colors cursor-pointer text-left"
                            >
                                <span className="w-8 h-8 rounded-lg bg-sky-50 border border-sky-200 flex items-center justify-center text-[#1164A3] font-bold">
                                    #
                                </span>
                                <div>
                                    <div className="text-xs font-bold text-slate-900">Create a Channel</div>
                                    <div className="text-[11px] text-slate-500">Organize discussions around a topic</div>
                                </div>
                            </button>

                            <button
                                type="button"
                                onClick={() => {
                                    setIsFabMenuOpen(false);
                                    onToast && onToast('Starting a live team audio huddle... 🎧');
                                }}
                                className="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-[#F1F5F9] text-slate-700 hover:text-slate-900 transition-colors cursor-pointer text-left"
                            >
                                <span className="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600">
                                    🎧
                                </span>
                                <div>
                                    <div className="text-xs font-bold text-slate-900">Start a Huddle</div>
                                    <div className="text-[11px] text-slate-500">Lightweight, informal audio conversation</div>
                                </div>
                            </button>

                            <button
                                type="button"
                                onClick={() => {
                                    setIsFabMenuOpen(false);
                                    onExitToGrid && onExitToGrid();
                                }}
                                className="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-[#F1F5F9] text-slate-700 hover:text-slate-900 transition-colors cursor-pointer text-left border-t border-[#E2E8F0] pt-3 mt-2"
                            >
                                <span className="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700">
                                    🏠
                                </span>
                                <div>
                                    <div className="text-xs font-bold text-slate-900">Return to Main GRID Feed</div>
                                    <div className="text-[11px] text-slate-500">Exit workspace to home feed</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
