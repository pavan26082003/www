import React, { useState, useEffect } from 'react';
import { getTeamInitialState, saveTeamState } from '../../teamState.js';
import WorkspaceWelcome from './WorkspaceWelcome.jsx';
import WorkspaceSetupWizard from './WorkspaceSetupWizard.jsx';
import TeamSidebar from './TeamSidebar.jsx';
import TeamWelcomeHub from './TeamWelcomeHub.jsx';
import TeamChatStream from './TeamChatStream.jsx';
import TeamDirectoryView from './TeamDirectoryView.jsx';
import TeamMobileApp from './TeamMobileApp.jsx';

/**
 * GridTeamApp
 * Master Orchestrator for the Slack-style GRID Team Workspace.
 * - Mobile Gate (< 768px): Shows "Mobile version coming soon. Please use desktop"
 * - Multi-pane Desktop Layout:
 *     1. Activity Rail (G logo, Home, Activity, Files, Agents)
 *     2. Aubergine Sidebar (Workspace header, My team, Channels, Direct messages)
 *     3. Main Content:
 *        - 'welcome-hub': Exact match of user screenshots (Welcome Pavan, Start a channel, Bring teammates, Sai Tulasi Ram)
 *        - 'chat': Real-time Slack conversation stream with composer & reactions
 *        - 'directories': People & Channels member directory
 */
export default function GridTeamApp({ onNavigate, onToast }) {
    const [teamData, setTeamData] = useState(() => getTeamInitialState());
    const [activeView, setActiveView] = useState(() => {
        if (typeof window !== 'undefined') {
            return localStorage.getItem('grid_team_screen') || 'welcome';
        }
        return 'welcome';
    });
    const [activeItemId, setActiveItemId] = useState(() => {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem('grid_team_item_id');
            if (saved === 'chan-nna') return 'chan-grid';
            return saved || 'chan-grid';
        }
        return 'chan-grid';
    });
    const [activeSubView, setActiveSubView] = useState(() => {
        if (typeof window !== 'undefined') {
            return localStorage.getItem('grid_team_subview') || 'welcome-hub';
        }
        return 'welcome-hub';
    });
    const [isMobileSidebarOpen, setIsMobileSidebarOpen] = useState(false);

    // Persist active state on reload
    useEffect(() => {
        if (typeof window !== 'undefined') {
            localStorage.setItem('grid_team_screen', activeView);
            localStorage.setItem('grid_team_subview', activeSubView);
            if (activeItemId) {
                localStorage.setItem('grid_team_item_id', activeItemId);
            } else {
                localStorage.removeItem('grid_team_item_id');
            }
        }
    }, [activeView, activeSubView, activeItemId]);

    // Mobile Detection (< 768px)
    const [isMobile, setIsMobile] = useState(() => {
        if (typeof window !== 'undefined') {
            return window.innerWidth < 768;
        }
        return false;
    });
    const [overrideMobileGate, setOverrideMobileGate] = useState(false);

    useEffect(() => {
        const handleResize = () => {
            setIsMobile(window.innerWidth < 768);
        };
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    // Auto-save any updates to local storage
    useEffect(() => {
        saveTeamState(teamData);
    }, [teamData]);

    const showToast = (msg) => {
        if (onToast) onToast(msg);
    };

    // Actions
    const handleLaunchWorkspace = (wsId) => {
        setTeamData(prev => ({
            ...prev,
            activeWorkspaceId: wsId
        }));
        setActiveView('workspace');
        setActiveSubView('welcome-hub'); // Launches into the exact Collaborating Overview Hub from Images 1-4!
        showToast(`Launching ${teamData.workspaces.find(w => w.id === wsId)?.name || 'GRID'} workspace... 🚀`);
    };

    const handleStartCreateWorkspace = () => {
        setActiveView('wizard');
    };

    const handleCancelWizard = () => {
        setActiveView('welcome');
    };

    const handleFinishWizard = ({ workspaceName, yourName, teammateEmails }) => {
        const newWsId = `ws-${Date.now()}`;
        const newWorkspace = {
            id: newWsId,
            name: workspaceName || 'New Workspace',
            slug: (workspaceName || 'new-workspace').toLowerCase().replace(/\s+/g, '-'),
            icon: (workspaceName || 'N').charAt(0).toUpperCase(),
            iconBg: '#3F0E40',
            memberCount: (teammateEmails ? teammateEmails.length : 0) + 1,
            lastActive: 'Just now',
            members: ['user-pavan'],
            isCurrent: true
        };

        setTeamData(prev => {
            const updatedWorkspaces = [newWorkspace, ...prev.workspaces.map(w => ({ ...w, isCurrent: false }))];
            let updatedDirectMessages = [...prev.directMessages];

            // Add invited teammates as direct message placeholders if provided
            if (teammateEmails && teammateEmails.length > 0) {
                teammateEmails.forEach((email, idx) => {
                    const cleanEmail = email.trim();
                    if (!cleanEmail) return;
                    const shortName = cleanEmail.split('@')[0];
                    updatedDirectMessages.push({
                        id: `dm-invited-${Date.now()}-${idx}`,
                        userId: `user-invited-${idx}`,
                        name: shortName,
                        shortName: shortName,
                        status: 'offline',
                        statusColor: '#94A3B8',
                        avatar: '',
                        initials: shortName.slice(0, 2).toUpperCase(),
                        unread: 0
                    });
                });
            }

            return {
                ...prev,
                currentUser: {
                    ...prev.currentUser,
                    name: yourName || prev.currentUser.name
                },
                workspaces: updatedWorkspaces,
                activeWorkspaceId: newWsId,
                directMessages: updatedDirectMessages
            };
        });

        setActiveView('workspace');
        setActiveSubView('welcome-hub');
        showToast(`🎉 Workspace "${workspaceName}" created successfully!`);
    };

    const handleExitToGrid = () => {
        try {
            localStorage.setItem('grid_team_screen', 'welcome');
            localStorage.removeItem('grid_team_subview');
            localStorage.removeItem('grid_team_item_id');
        } catch (e) {}
        setActiveView('welcome');
        if (onNavigate) {
            onNavigate('home');
        } else {
            window.location.href = '/index.php/grid';
        }
    };

    const handleSelectChannel = (channelId) => {
        setActiveItemId(channelId);
        setActiveSubView('chat');
        setIsMobileSidebarOpen(false);
    };

    const handleSelectDM = (dmId) => {
        setActiveItemId(dmId);
        setActiveSubView('chat');
        setIsMobileSidebarOpen(false);
    };

    const handleSelectDirectories = () => {
        setActiveSubView('directories');
        setIsMobileSidebarOpen(false);
    };

    const handleSelectHome = () => {
        setActiveSubView('welcome-hub');
        setIsMobileSidebarOpen(false);
    };

    const handleSwitchWorkspace = (wsId) => {
        setTeamData(prev => ({
            ...prev,
            activeWorkspaceId: wsId
        }));
        showToast(`Switched workspace to ${teamData.workspaces.find(w => w.id === wsId)?.name || 'workspace'}`);
    };

    const handleSendMessage = (content) => {
        const timeNow = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const newMsg = {
            id: `msg-${Date.now()}`,
            senderId: teamData.currentUser.id,
            senderName: teamData.currentUser.name,
            avatar: teamData.currentUser.avatar,
            initials: teamData.currentUser.initials,
            time: timeNow,
            date: 'Today',
            content,
            reactions: []
        };

        setTeamData(prev => {
            const currentMsgs = prev.messages[activeItemId] || [];
            return {
                ...prev,
                messages: {
                    ...prev.messages,
                    [activeItemId]: [...currentMsgs, newMsg]
                }
            };
        });
    };

    const handleAddReaction = (messageId, emoji) => {
        setTeamData(prev => {
            const currentMsgs = prev.messages[activeItemId] || [];
            const updated = currentMsgs.map(m => {
                if (m.id !== messageId) return m;
                const existingReaction = m.reactions?.find(r => r.emoji === emoji);
                const hasReacted = existingReaction?.users?.includes(prev.currentUser.id);

                let updatedReactions = m.reactions ? [...m.reactions] : [];
                if (hasReacted) {
                    updatedReactions = updatedReactions
                        .map(r => r.emoji === emoji ? { ...r, count: r.count - 1, users: r.users.filter(u => u !== prev.currentUser.id) } : r)
                        .filter(r => r.count > 0);
                } else if (existingReaction) {
                    updatedReactions = updatedReactions.map(r => r.emoji === emoji ? { ...r, count: r.count + 1, users: [...r.users, prev.currentUser.id] } : r);
                } else {
                    updatedReactions.push({ emoji, count: 1, users: [prev.currentUser.id] });
                }

                return { ...m, reactions: updatedReactions };
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

    const handleDirectMessageUser = (userId) => {
        const existingDM = teamData.directMessages.find(dm => dm.userId === userId);
        if (existingDM) {
            handleSelectDM(existingDM.id);
        } else {
            const person = teamData.directories?.find(d => d.userId === userId);
            const newDmId = `dm-${userId}`;
            const newDM = {
                id: newDmId,
                userId: userId,
                name: person?.name || 'Teammate',
                shortName: person?.name?.split(' ')[0] || 'Teammate',
                status: 'online',
                statusColor: '#10B981',
                avatar: person?.avatar || '',
                initials: person?.initials || 'TM',
                unread: 0
            };

            setTeamData(prev => ({
                ...prev,
                directMessages: [...prev.directMessages, newDM]
            }));
            handleSelectDM(newDmId);
        }
    };

    const handleAddChannel = () => {
        const chanName = window.prompt('Enter new channel name:');
        if (!chanName || !chanName.trim()) return;
        const cleanName = chanName.trim().toLowerCase().replace(/\s+/g, '-');
        const newChan = {
            id: `chan-${cleanName}-${Date.now()}`,
            name: cleanName,
            isPrivate: false,
            topic: `Discussion channel for #${cleanName}`,
            memberCount: 1,
            unread: 0
        };

        setTeamData(prev => ({
            ...prev,
            channels: [...prev.channels, newChan]
        }));
        handleSelectChannel(newChan.id);
        showToast(`Created channel #${cleanName} 🎉`);
    };

    const handleInviteTeammates = () => {
        const email = window.prompt('Enter teammate email to invite to GRID:');
        if (!email || !email.trim()) return;
        showToast(`Invitation sent to ${email.trim()}! 📨`);
    };

    const activeWorkspace = teamData.workspaces.find(w => w.id === teamData.activeWorkspaceId) || teamData.workspaces[0];

    // Determine current active item (channel or direct message)
    const isDirectMessage = activeItemId.startsWith('dm-');
    let activeItem = null;
    if (isDirectMessage) {
        activeItem = teamData.directMessages.find(dm => dm.id === activeItemId) || teamData.directMessages[0];
    } else {
        activeItem = teamData.myTeam?.channels?.find(c => c.id === activeItemId) ||
                     teamData.channels.find(c => c.id === activeItemId) ||
                     teamData.myTeam?.channels?.[0];
    }

    const currentMessages = teamData.messages[activeItemId] || [];

    return (
        <div className="w-full h-screen min-h-screen bg-[#F8FAFC] text-slate-800 flex flex-col font-sans overflow-hidden select-none">
            {/* SCREEN 1: Welcome & Launcher (Same on PC and Mobile) */}
            {activeView === 'welcome' && (
                <WorkspaceWelcome
                    workspaces={teamData.workspaces}
                    currentUser={teamData.currentUser}
                    onLaunchWorkspace={handleLaunchWorkspace}
                    onCreateWorkspace={handleStartCreateWorkspace}
                    onExitToGrid={handleExitToGrid}
                    onToast={showToast}
                />
            )}

            {/* SCREENS 2-4: 3-Step Setup Wizard (Same on PC and Mobile) */}
            {activeView === 'wizard' && (
                <WorkspaceSetupWizard
                    initialName={teamData.currentUser.name}
                    onFinish={handleFinishWizard}
                    onCancel={handleCancelWizard}
                    onToast={showToast}
                />
            )}

            {/* SCREEN 5: Main Slack-Style Team Workspace */}
            {activeView === 'workspace' && (
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
                        onAddChannel={handleAddChannel}
                        onSwitchWorkspace={handleSwitchWorkspace}
                        onSendMessage={handleSendMessage}
                        onAddReaction={handleAddReaction}
                        onExitToGrid={handleExitToGrid}
                        onToast={showToast}
                    />
                ) : (
                <div className="flex-1 flex overflow-hidden relative">
                    {/* Mobile Menu Backdrop */}
                    {isMobileSidebarOpen && (
                        <div
                            className="fixed inset-0 bg-black/60 z-30 md:hidden"
                            onClick={() => setIsMobileSidebarOpen(false)}
                        />
                    )}

                    {/* Left Sidebar (Desktop + Mobile Drawer) */}
                    <div className={`fixed inset-y-0 left-0 z-40 md:static md:z-auto transition-transform duration-200 ease-in-out ${
                        isMobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
                    }`}>
                        <TeamSidebar
                            workspace={activeWorkspace}
                            workspaces={teamData.workspaces}
                            currentUser={teamData.currentUser}
                            myTeam={teamData.myTeam}
                            channels={teamData.channels}
                            directMessages={teamData.directMessages}
                            apps={teamData.apps}
                            activeItemId={activeItemId}
                            activeSubView={activeSubView}
                            onSelectChannel={handleSelectChannel}
                            onSelectDM={handleSelectDM}
                            onSelectDirectories={handleSelectDirectories}
                            onSelectHome={handleSelectHome}
                            onSwitchWorkspace={handleSwitchWorkspace}
                            onCreateWorkspace={handleStartCreateWorkspace}
                            onReturnToWelcome={() => setActiveView('welcome')}
                            onExitToGrid={handleExitToGrid}
                            onAddChannel={handleAddChannel}
                            onInviteTeammates={handleInviteTeammates}
                            onToast={showToast}
                        />
                    </div>

                    {/* Main Content Pane */}
                    <div className="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-white">
                        {/* Desktop Persistent Slack Top Header Bar */}
                        <header className="hidden md:flex h-11 px-4 bg-white border-b border-[#E2E8F0] items-center justify-between shrink-0 text-[#64748B] select-none z-10 shadow-2xs">
                            <div className="flex items-center gap-1.5 text-[#64748B]">
                                <button
                                    type="button"
                                    onClick={() => showToast('Previous conversation')}
                                    className="p-1.5 hover:bg-[#F1F5F9] hover:text-[#0F172A] rounded transition-colors"
                                    title="Back"
                                >
                                    <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    onClick={() => showToast('Next conversation')}
                                    className="p-1.5 hover:bg-[#F1F5F9] rounded transition-colors text-[#CBD5E1] cursor-not-allowed"
                                    title="Forward"
                                >
                                    <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    onClick={() => showToast('Recent channel history')}
                                    className="p-1.5 hover:bg-[#F1F5F9] hover:text-[#0F172A] rounded transition-colors text-[#64748B]"
                                    title="History"
                                >
                                    <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                            </div>

                            {/* Center Search Pill */}
                            <div className="flex-1 max-w-xl mx-4">
                                <div className="relative">
                                    <input
                                        type="text"
                                        readOnly
                                        onClick={() => showToast('Search entire workspace (⌘K)')}
                                        placeholder="Describe what you are looking for"
                                        className="w-full h-7 bg-[#F1F5F9] hover:bg-[#E2E8F0]/60 text-xs text-[#0F172A] placeholder-[#64748B] rounded-md pl-8 pr-8 border border-[#CBD5E1] focus:border-[#0284C7] focus:outline-none cursor-pointer transition-colors shadow-2xs"
                                    />
                                    <svg className="w-3.5 h-3.5 text-[#64748B] absolute left-2.5 top-2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <div className="absolute right-2.5 top-1.5 flex items-center pointer-events-none">
                                        <span className="text-amber-500 text-[11px]">✨</span>
                                    </div>
                                </div>
                            </div>

                            {/* Right Actions */}
                            <div className="flex items-center gap-2 text-[#64748B]">
                                <button
                                    type="button"
                                    onClick={() => showToast('Help & documentation')}
                                    className="w-6 h-6 rounded-full hover:bg-[#F1F5F9] flex items-center justify-center text-xs text-[#64748B] hover:text-[#0F172A] transition-colors font-bold border border-transparent hover:border-[#CBD5E1]"
                                    title="Help"
                                >
                                    ?
                                </button>
                            </div>
                        </header>

                        {/* Mobile Header Bar */}
                        <div className="md:hidden h-12 px-4 bg-[#3F0E40] text-white flex items-center justify-between shrink-0 border-b border-[#522653]">
                            <button
                                type="button"
                                onClick={() => setIsMobileSidebarOpen(true)}
                                className="p-1.5 hover:bg-[#522653] rounded-md transition-colors"
                            >
                                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <span className="font-bold text-sm truncate">{activeWorkspace?.name || 'GRID'}</span>
                            <button
                                type="button"
                                onClick={handleExitToGrid}
                                className="text-xs text-[#B292B4] hover:text-white"
                            >
                                Exit
                            </button>
                        </div>

                        {/* View Switcher: Welcome Hub | Directories View | Chat Stream */}
                        {activeSubView === 'welcome-hub' ? (
                            <TeamWelcomeHub
                                currentUser={teamData.currentUser}
                                workspaces={teamData.workspaces}
                                onStartChannel={handleAddChannel}
                                onInviteTeammates={handleInviteTeammates}
                                onSelectDM={handleSelectDM}
                                onToast={showToast}
                            />
                        ) : activeSubView === 'directories' ? (
                            <TeamDirectoryView
                                directoryMembers={teamData.directories}
                                channels={teamData.channels}
                                currentUser={teamData.currentUser}
                                onDirectMessageUser={handleDirectMessageUser}
                                onInviteTeammates={handleInviteTeammates}
                                onToast={showToast}
                            />
                        ) : (
                            <TeamChatStream
                                activeItem={activeItem}
                                isDirectMessage={isDirectMessage}
                                messages={currentMessages}
                                currentUser={teamData.currentUser}
                                onSendMessage={handleSendMessage}
                                onAddReaction={handleAddReaction}
                                onToast={showToast}
                            />
                        )}
                    </div>
                </div>
                )
            )}
        </div>
    );
}
