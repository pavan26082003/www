import React, { useState, useEffect } from 'react';
import { getTeamInitialState, saveTeamState } from './teamState';
import WorkspaceWelcome from './components/WorkspaceWelcome';
import WorkspaceSetupWizard from './components/WorkspaceSetupWizard';
import TeamSidebar from './components/TeamSidebar';
import TeamWelcomeHub from './components/TeamWelcomeHub';
import TeamChatStream from './components/TeamChatStream';
import TeamMobileApp from './components/TeamMobileApp';

/**
 * TeamApp Root Component
 * Standalone React App for GRID Team Workspaces
 * Flow:
 * - 'welcome': Screen 1 Launcher
 * - 'wizard': 3-Step Setup Wizard (Screens 2, 3, 4)
 * - 'workspace': Main Workspace Hub & Chat
 */
export default function TeamApp() {
    const [teamData, setTeamData] = useState(() => getTeamInitialState());
    const [screen, setScreen] = useState(() => {
        if (typeof window !== 'undefined') {
            return localStorage.getItem('grid_team_screen') || 'welcome';
        }
        return 'welcome';
    });
    const [activeSubView, setActiveSubView] = useState(() => {
        if (typeof window !== 'undefined') {
            return localStorage.getItem('grid_team_subview') || 'home';
        }
        return 'home';
    });
    const [activeItemId, setActiveItemId] = useState(() => {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem('grid_team_item_id');
            if (saved === 'chan-nna') return 'chan-grid';
            return saved || null;
        }
        return null;
    });
    const [toastMessage, setToastMessage] = useState(null);

    // Persist active view on page refresh / reload
    useEffect(() => {
        if (typeof window !== 'undefined') {
            localStorage.setItem('grid_team_screen', screen);
            localStorage.setItem('grid_team_subview', activeSubView);
            if (activeItemId) {
                localStorage.setItem('grid_team_item_id', activeItemId);
            } else {
                localStorage.removeItem('grid_team_item_id');
            }
        }
    }, [screen, activeSubView, activeItemId]);

    // Modals
    const [isAddChannelOpen, setIsAddChannelOpen] = useState(false);
    const [newChannelName, setNewChannelName] = useState('');
    const [newChannelTopic, setNewChannelTopic] = useState('');
    const [newChannelIsPrivate, setNewChannelIsPrivate] = useState(false);

    const [isInviteModalOpen, setIsInviteModalOpen] = useState(false);
    const [inviteEmailsText, setInviteEmailsText] = useState('');

    // Responsive Desktop Detection
    const [isMobile, setIsMobile] = useState(typeof window !== 'undefined' ? window.innerWidth < 768 : false);

    useEffect(() => {
        const handleResize = () => setIsMobile(window.innerWidth < 768);
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    // Persist state changes
    useEffect(() => {
        saveTeamState(teamData);
    }, [teamData]);

    const showToast = (msg) => {
        setToastMessage(msg);
        setTimeout(() => {
            setToastMessage(prev => (prev === msg ? null : prev));
        }, 3200);
    };

    const activeWorkspace = teamData.workspaces.find(w => w.id === teamData.activeWorkspaceId) || teamData.workspaces[0];

    // ==========================================
    // SCREEN 1: LAUNCHER ACTIONS
    // ==========================================
    const handleLaunchWorkspace = (workspaceId) => {
        setTeamData(prev => ({
            ...prev,
            activeWorkspaceId: workspaceId
        }));
        setScreen('workspace');
        setActiveSubView('home');
        setActiveItemId(null);
    };

    const handleStartCreateWorkspace = () => {
        setScreen('wizard');
    };

    const handleExitToGrid = () => {
        try {
            // Reset saved screen so opening Team next time always starts on Welcome page
            localStorage.setItem('grid_team_screen', 'welcome');
            localStorage.removeItem('grid_team_subview');
            localStorage.removeItem('grid_team_item_id');
        } catch (e) {}
        window.location.href = '/index.php/grid';
    };

    // ==========================================
    // SCREEN 2, 3, 4: WIZARD FINISH
    // ==========================================
    const handleWizardFinish = ({ workspaceName, yourName, teammateEmails }) => {
        const newWsId = `ws-${Date.now()}`;
        const newWs = {
            id: newWsId,
            name: workspaceName,
            slug: workspaceName.toLowerCase().replace(/\s+/g, '-'),
            icon: workspaceName.charAt(0).toUpperCase() || 'W',
            iconBg: '#3F0E40',
            memberCount: 1 + teammateEmails.length,
            lastActive: 'Just now',
            members: ['user-pavan'],
            isCurrent: true
        };

        const updatedUser = {
            ...teamData.currentUser,
            name: yourName,
            initials: yourName.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()
        };

        // Add invited teammates to direct messages
        const newDMs = teammateEmails.map((email, idx) => {
            const rawName = email.split('@')[0];
            const cleanName = rawName.charAt(0).toUpperCase() + rawName.slice(1);
            return {
                id: `dm-${Date.now()}-${idx}`,
                userId: `user-inv-${Date.now()}-${idx}`,
                name: cleanName,
                shortName: rawName,
                status: 'online',
                statusColor: '#10B981',
                avatar: '',
                initials: cleanName.slice(0, 2).toUpperCase(),
                unread: 0
            };
        });

        setTeamData(prev => ({
            ...prev,
            currentUser: updatedUser,
            workspaces: [newWs, ...prev.workspaces.map(w => ({ ...w, isCurrent: false }))],
            activeWorkspaceId: newWsId,
            directMessages: [...newDMs, ...prev.directMessages]
        }));

        setScreen('workspace');
        setActiveSubView('home');
        setActiveItemId(null);
        showToast(`🎉 Workspace "${workspaceName}" created successfully!`);
    };

    // ==========================================
    // MAIN WORKSPACE NAVIGATION & CHAT
    // ==========================================
    const handleSelectChannel = (channelId) => {
        setActiveSubView('channel');
        setActiveItemId(channelId);
    };

    const handleSelectDM = (dmId) => {
        setActiveSubView('dm');
        setActiveItemId(dmId);
    };

    const handleSelectHome = () => {
        setActiveSubView('home');
        setActiveItemId(null);
    };

    const handleSwitchWorkspace = (wsId) => {
        setTeamData(prev => ({
            ...prev,
            activeWorkspaceId: wsId,
            workspaces: prev.workspaces.map(w => ({ ...w, isCurrent: w.id === wsId }))
        }));
        setActiveSubView('home');
        setActiveItemId(null);
        showToast('Switched workspace');
    };

    const handleSendMessage = (content) => {
        if (!activeItemId) return;
        const newMsg = {
            id: `msg-${Date.now()}`,
            senderId: teamData.currentUser.id,
            senderName: teamData.currentUser.name,
            avatar: teamData.currentUser.avatar,
            initials: teamData.currentUser.initials,
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            date: 'Today',
            content,
            reactions: []
        };

        setTeamData(prev => {
            const list = prev.messages[activeItemId] || [];
            return {
                ...prev,
                messages: {
                    ...prev.messages,
                    [activeItemId]: [...list, newMsg]
                }
            };
        });
    };

    const handleAddReaction = (messageId, emoji) => {
        if (!activeItemId) return;
        setTeamData(prev => {
            const list = prev.messages[activeItemId] || [];
            const updated = list.map(m => {
                if (m.id !== messageId) return m;
                const existing = m.reactions?.find(r => r.emoji === emoji);
                if (existing) {
                    return {
                        ...m,
                        reactions: m.reactions.map(r => r.emoji === emoji ? { ...r, count: r.count + 1 } : r)
                    };
                }
                return {
                    ...m,
                    reactions: [...(m.reactions || []), { emoji, count: 1, users: [prev.currentUser.id] }]
                };
            });
            return {
                ...prev,
                messages: {
                    ...prev.messages,
                    [activeItemId]: updated
                }
            };
        });
    };

    // Add Channel Action
    const handleCreateChannelSubmit = (e) => {
        e.preventDefault();
        const cleanName = newChannelName.trim().toLowerCase().replace(/\s+/g, '-');
        if (!cleanName) return;

        const newChan = {
            id: `chan-${Date.now()}`,
            name: cleanName,
            isPrivate: newChannelIsPrivate,
            topic: newChannelTopic.trim() || 'General discussion',
            memberCount: 1,
            unread: 0
        };

        setTeamData(prev => ({
            ...prev,
            channels: [...prev.channels, newChan],
            messages: {
                ...prev.messages,
                [newChan.id]: [
                    {
                        id: `msg-welcome-${Date.now()}`,
                        senderId: prev.currentUser.id,
                        senderName: prev.currentUser.name,
                        initials: prev.currentUser.initials,
                        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                        date: 'Today',
                        content: `🎉 Created channel #${cleanName}!`,
                        reactions: [{ emoji: '🚀', count: 1, users: [prev.currentUser.id] }]
                    }
                ]
            }
        }));

        setIsAddChannelOpen(false);
        setNewChannelName('');
        setNewChannelTopic('');
        setNewChannelIsPrivate(false);
        handleSelectChannel(newChan.id);
        showToast(`Channel #${cleanName} created!`);
    };

    // Invite Teammates Submit
    const handleInviteSubmit = (e) => {
        e.preventDefault();
        const emails = inviteEmailsText.split(/[,\n]/).map(s => s.trim()).filter(Boolean);
        if (emails.length === 0) return;

        const newDMs = emails.map((email, idx) => {
            const rawName = email.split('@')[0];
            const cleanName = rawName.charAt(0).toUpperCase() + rawName.slice(1);
            return {
                id: `dm-inv-${Date.now()}-${idx}`,
                userId: `user-inv-${Date.now()}-${idx}`,
                name: cleanName,
                shortName: rawName,
                status: 'online',
                statusColor: '#10B981',
                avatar: '',
                initials: cleanName.slice(0, 2).toUpperCase(),
                unread: 0
            };
        });

        setTeamData(prev => ({
            ...prev,
            directMessages: [...newDMs, ...prev.directMessages]
        }));

        setIsInviteModalOpen(false);
        setInviteEmailsText('');
        showToast(`Sent ${emails.length} teammate invite${emails.length > 1 ? 's' : ''}!`);
    };

    // Determine current active item for chat stream
    let currentActiveItem = null;
    if (activeSubView === 'channel') {
        currentActiveItem = teamData.myTeam?.channels?.find(c => c.id === activeItemId) ||
            teamData.channels?.find(c => c.id === activeItemId);
    } else if (activeSubView === 'dm') {
        currentActiveItem = teamData.directMessages?.find(d => d.id === activeItemId);
    }

    return (
        <div className="w-screen h-screen overflow-hidden flex flex-col bg-[#F8FAFC] font-sans antialiased text-slate-800">
            {/* SCREEN 1: Workspace Launcher (Same on PC and Mobile) */}
            {screen === 'welcome' && (
                <WorkspaceWelcome
                    currentUser={teamData.currentUser}
                    workspaces={teamData.workspaces}
                    onLaunchWorkspace={handleLaunchWorkspace}
                    onCreateWorkspace={handleStartCreateWorkspace}
                    onExitToGrid={handleExitToGrid}
                    onToast={showToast}
                />
            )}

            {/* SCREEN 2, 3, 4: Setup Wizard (Same on PC and Mobile) */}
            {screen === 'wizard' && (
                <WorkspaceSetupWizard
                    initialName={teamData.currentUser?.name}
                    onFinish={handleWizardFinish}
                    onCancel={() => setScreen('welcome')}
                    onToast={showToast}
                />
            )}

            {/* MAIN WORKSPACE VIEW */}
            {screen === 'workspace' && (
                isMobile ? (
                    <TeamMobileApp
                        workspace={activeWorkspace}
                        workspaces={teamData.workspaces}
                        currentUser={teamData.currentUser}
                        myTeam={teamData.myTeam}
                        channels={teamData.channels}
                        directMessages={teamData.directMessages}
                        apps={teamData.apps}
                        activeItemId={activeItemId}
                        messages={teamData.messages}
                        onSelectChannel={handleSelectChannel}
                        onSelectDM={handleSelectDM}
                        onAddChannel={() => setIsAddChannelOpen(true)}
                        onSwitchWorkspace={handleSwitchWorkspace}
                        onSendMessage={handleSendMessage}
                        onAddReaction={handleAddReaction}
                        onExitToGrid={handleExitToGrid}
                        onToast={showToast}
                    />
                ) : (
                <div className="w-full h-full flex flex-col overflow-hidden">
                    {/* Top Slack Clean Light Universal Search Bar */}
                    <header className="h-11 bg-white border-b border-[#E2E8F0] flex items-center justify-between px-3 text-xs shrink-0 select-none z-30 shadow-2xs">
                        {/* History Navigation Buttons */}
                        <div className="flex items-center gap-1.5 text-[#64748B]">
                            <button
                                type="button"
                                onClick={handleSelectHome}
                                className="p-1.5 hover:bg-[#F1F5F9] hover:text-[#0F172A] rounded transition-colors"
                                title="Back / Home"
                            >
                                <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button
                                type="button"
                                className="p-1.5 hover:bg-[#F1F5F9] rounded transition-colors text-[#CBD5E1] cursor-not-allowed"
                                title="Forward"
                            >
                                <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <button
                                type="button"
                                onClick={() => showToast('Recent activity log')}
                                className="p-1.5 hover:bg-[#F1F5F9] hover:text-[#0F172A] rounded transition-colors text-[#64748B]"
                                title="Recent conversations"
                            >
                                <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        </div>

                        {/* Centered Search Pill */}
                        <div className="flex-1 max-w-xl mx-4">
                            <div className="relative flex items-center">
                                <input
                                    type="text"
                                    placeholder="Describe what you are looking for"
                                    className="w-full h-7 bg-[#F1F5F9] hover:bg-[#E2E8F0]/60 focus:bg-white text-[#0F172A] placeholder-[#64748B] rounded-md pl-7 pr-7 text-xs border border-[#CBD5E1] focus:border-[#0284C7] outline-none transition-all shadow-2xs"
                                />
                                <svg className="w-3.5 h-3.5 text-[#64748B] absolute left-2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                {/* Slack Sparkles Icon on Right */}
                                <div className="absolute right-2 flex items-center pointer-events-none">
                                    <span className="text-amber-500 text-[11px]">✨</span>
                                </div>
                            </div>
                        </div>

                        {/* Right Help Button */}
                        <div className="flex items-center gap-2">
                            <button
                                type="button"
                                onClick={() => showToast('Help & keyboard shortcuts')}
                                className="w-6 h-6 rounded-full hover:bg-[#F1F5F9] text-[#64748B] hover:text-[#0F172A] flex items-center justify-center font-bold text-xs border border-transparent hover:border-[#CBD5E1]"
                                title="Help"
                            >
                                ?
                            </button>
                        </div>
                    </header>

                    {/* Main Body: Sidebar + Main Stage */}
                    <div className="flex-1 flex overflow-hidden">
                        {/* Slack Sidebar (No activity rail, No directories) */}
                        <TeamSidebar
                            workspace={activeWorkspace}
                            workspaces={teamData.workspaces}
                            currentUser={teamData.currentUser}
                            myTeam={teamData.myTeam}
                            channels={teamData.channels}
                            directMessages={teamData.directMessages}
                            activeItemId={activeItemId}
                            activeSubView={activeSubView}
                            onSelectChannel={handleSelectChannel}
                            onSelectDM={handleSelectDM}
                            onSelectHome={handleSelectHome}
                            onSwitchWorkspace={handleSwitchWorkspace}
                            onCreateWorkspace={handleStartCreateWorkspace}
                            onReturnToWelcome={() => setScreen('welcome')}
                            onExitToGrid={handleExitToGrid}
                            onAddChannel={() => setIsAddChannelOpen(true)}
                            onInviteTeammates={() => setIsInviteModalOpen(true)}
                            onToast={showToast}
                        />

                        {/* Right Main Stage: Welcome Hub OR Chat Stream */}
                        <main className="flex-1 h-full bg-white text-slate-900 overflow-hidden flex flex-col">
                            {activeSubView === 'home' || !currentActiveItem ? (
                                <TeamWelcomeHub
                                    currentUser={teamData.currentUser}
                                    workspaces={teamData.workspaces}
                                    onStartChannel={() => setIsAddChannelOpen(true)}
                                    onInviteTeammates={() => setIsInviteModalOpen(true)}
                                    onSelectDM={(dmId) => handleSelectDM(dmId)}
                                    onToast={showToast}
                                />
                            ) : (
                                <TeamChatStream
                                    activeItem={currentActiveItem}
                                    isDirectMessage={activeSubView === 'dm'}
                                    messages={teamData.messages[activeItemId] || []}
                                    currentUser={teamData.currentUser}
                                    onSendMessage={handleSendMessage}
                                    onAddReaction={handleAddReaction}
                                    onToast={showToast}
                                />
                            )}
                        </main>
                    </div>
                </div>
                )
            )}

            {/* ========================================================================= */}
            {/* MODAL 1: ADD CHANNEL                                                      */}
            {/* ========================================================================= */}
            {isAddChannelOpen && (
                <div className="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
                    <div className="w-full max-w-md bg-white text-slate-900 rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                        <div className="p-6">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                                <h3 className="text-xl font-extrabold text-slate-900">Create a channel</h3>
                                <button
                                    type="button"
                                    onClick={() => setIsAddChannelOpen(false)}
                                    className="text-slate-400 hover:text-slate-700 font-bold p-1 cursor-pointer"
                                >
                                    ✕
                                </button>
                            </div>

                            <form onSubmit={handleCreateChannelSubmit} className="space-y-4">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">
                                        Name
                                    </label>
                                    <div className="relative">
                                        <span className="absolute left-3.5 top-3 text-slate-400 font-bold">#</span>
                                        <input
                                            type="text"
                                            value={newChannelName}
                                            onChange={(e) => setNewChannelName(e.target.value)}
                                            placeholder="e.g. marketing-plan"
                                            autoFocus
                                            className="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 text-sm font-medium outline-none"
                                        />
                                    </div>
                                    <p className="text-[11px] text-slate-400 mt-1">
                                        Channels are where conversations happen around a topic.
                                    </p>
                                </div>

                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">
                                        Topic / Description (optional)
                                    </label>
                                    <input
                                        type="text"
                                        value={newChannelTopic}
                                        onChange={(e) => setNewChannelTopic(e.target.value)}
                                        placeholder="What is this channel about?"
                                        className="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 text-sm font-medium outline-none"
                                    />
                                </div>

                                <div className="flex items-center justify-between pt-2">
                                    <div>
                                        <div className="text-xs font-bold text-slate-800">Make private</div>
                                        <div className="text-[11px] text-slate-400">Only invited members can view</div>
                                    </div>
                                    <input
                                        type="checkbox"
                                        checked={newChannelIsPrivate}
                                        onChange={(e) => setNewChannelIsPrivate(e.target.checked)}
                                        className="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 cursor-pointer"
                                    />
                                </div>

                                <div className="pt-4 flex items-center justify-end gap-2 border-t border-slate-100">
                                    <button
                                        type="button"
                                        onClick={() => setIsAddChannelOpen(false)}
                                        className="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={!newChannelName.trim()}
                                        className="px-5 py-2 rounded-xl text-xs font-bold bg-[#007A5A] text-white hover:bg-[#006C4E] disabled:opacity-40 transition-colors cursor-pointer"
                                    >
                                        Create Channel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            {/* ========================================================================= */}
            {/* MODAL 2: INVITE TEAMMATES                                                 */}
            {/* ========================================================================= */}
            {isInviteModalOpen && (
                <div className="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
                    <div className="w-full max-w-md bg-white text-slate-900 rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                        <div className="p-6">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                                <div>
                                    <h3 className="text-xl font-extrabold text-slate-900">Invite teammates</h3>
                                    <p className="text-xs text-slate-500">to {activeWorkspace?.name || 'GRID'}</p>
                                </div>
                                <button
                                    type="button"
                                    onClick={() => setIsInviteModalOpen(false)}
                                    className="text-slate-400 hover:text-slate-700 font-bold p-1 cursor-pointer"
                                >
                                    ✕
                                </button>
                            </div>

                            <form onSubmit={handleInviteSubmit} className="space-y-4">
                                <div>
                                    <label className="block text-xs font-bold text-slate-700 mb-1">
                                        Email addresses
                                    </label>
                                    <textarea
                                        rows={3}
                                        value={inviteEmailsText}
                                        onChange={(e) => setInviteEmailsText(e.target.value)}
                                        placeholder="name@company.com, coworker@company.com"
                                        autoFocus
                                        className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 text-xs font-medium outline-none resize-none"
                                    />
                                </div>

                                <div className="pt-2 flex items-center justify-between border-t border-slate-100">
                                    <button
                                        type="button"
                                        onClick={() => {
                                            if (navigator.clipboard) {
                                                navigator.clipboard.writeText(`https://grid.community/invite/${(activeWorkspace?.name || 'grid').toLowerCase()}-99`);
                                            }
                                            showToast('📋 Invite link copied to clipboard!');
                                        }}
                                        className="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1 cursor-pointer"
                                    >
                                        <span>🔗 Copy invite link</span>
                                    </button>

                                    <div className="flex items-center gap-2">
                                        <button
                                            type="button"
                                            onClick={() => setIsInviteModalOpen(false)}
                                            className="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 cursor-pointer"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            disabled={!inviteEmailsText.trim()}
                                            className="px-5 py-2 rounded-xl text-xs font-bold bg-[#007A5A] text-white hover:bg-[#006C4E] disabled:opacity-40 transition-colors cursor-pointer"
                                        >
                                            Send Invites
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            {/* ========================================================================= */}
            {/* FLOATING TOAST NOTIFICATION                                               */}
            {/* ========================================================================= */}
            {toastMessage && (
                <div className="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-4 py-2.5 rounded-xl shadow-2xl border border-slate-700 text-xs font-medium flex items-center gap-2 animate-in slide-in-from-bottom duration-200">
                    <span>{toastMessage}</span>
                </div>
            )}
        </div>
    );
}
