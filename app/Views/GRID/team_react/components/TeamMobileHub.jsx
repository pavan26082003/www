import React from 'react';
import TeamMobileTopBar from './TeamMobileTopBar.jsx';
import TeamMobileActionCards from './TeamMobileActionCards.jsx';
import TeamMobileSections from './TeamMobileSections.jsx';
import TeamMobileBottomNav from './TeamMobileBottomNav.jsx';

/**
 * TeamMobileHub Component
 * Main Slack-style Mobile Navigation Hub matching user's provided screenshot.
 * Houses the header, quick cards carousel, channel & DM accordion lists, FAB, and bottom navigation.
 */
export default function TeamMobileHub({
    workspace,
    workspaces,
    currentUser,
    myTeam,
    channels,
    directMessages,
    apps,
    activeItemId,
    activeTab,
    onTabChange,
    onSelectChannel,
    onSelectDM,
    onAddChannel,
    onSwitchWorkspace,
    onFabClick,
    onToast
}) {
    // Count total unread DMs for the bottom nav badge
    const unreadDMsCount = (directMessages || []).reduce((acc, dm) => acc + (dm.unread || 0), 0);

    return (
        <div className="w-full h-full bg-[#F8FAFC] text-slate-800 flex flex-col font-sans overflow-hidden relative">
            {/* Top Slack Mobile Header */}
            <TeamMobileTopBar
                workspace={workspace}
                currentUser={currentUser}
                workspaces={workspaces}
                onSwitchWorkspace={onSwitchWorkspace}
                onToast={onToast}
            />

            {/* Scrollable Content Workspace */}
            <div
                className="flex-1 min-h-0 overflow-y-auto overscroll-y-contain"
                style={{ WebkitOverflowScrolling: 'touch', touchAction: 'pan-y' }}
            >
                {/* Quick Action Cards Carousel */}
                <TeamMobileActionCards onToast={onToast} />

                {/* Collapsible Sections: My Team, Channels, Direct Messages, Recent Apps */}
                <TeamMobileSections
                    myTeam={myTeam}
                    channels={channels}
                    directMessages={directMessages}
                    apps={apps}
                    activeItemId={activeItemId}
                    onSelectChannel={onSelectChannel}
                    onSelectDM={onSelectDM}
                    onAddChannel={onAddChannel}
                    onToast={onToast}
                />
            </div>

            {/* Bottom 5-Tab Navigation + FAB */}
            <TeamMobileBottomNav
                activeTab={activeTab}
                onTabChange={onTabChange}
                onFabClick={onFabClick}
                unreadDMsCount={unreadDMsCount}
            />
        </div>
    );
}
