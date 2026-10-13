/**
 * teamState.js
 * Comprehensive initial state and state helpers for Standalone GRID Team Workspace
 */

export const initialTeamData = {
    currentUser: {
        id: 'user-pavan',
        name: 'K Pavan Kumar',
        displayName: 'pavan',
        email: 'umarpavan768@gmail.com',
        avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuB0JMtxhQ02NsJAGucDr5mPhqMXM7S2dSh9ApxaOrjBpGFXL18mJrlzrzzEJRw737t1vu9Tcqri1rbH-CnDZtieK4L4BtmJzCP4_5FdnzbFXQUGfbfoh84rU333cFrjS78zpHLXqld0Zcyz5u-NytgOwsRE840swIFnrRHXrC1bG4E5sLOF6s5MgZLkhvd92Cpw1iZkw4GT5I-yyv57M7ve69ixFXJ2DvKQb3_TcB0iHyTUV4MCJ7Zs',
        initials: 'PK',
        status: 'active',
        role: 'Workspace Owner'
    },

    // Workspaces List (Screen 1 launcher)
    workspaces: [
        {
            id: 'ws-grid',
            name: 'GRID',
            slug: 'grid-community',
            icon: 'G',
            iconBg: '#3F0E40',
            memberCount: 2,
            lastActive: 'Last sign-in 4 hours ago',
            members: ['user-pavan', 'user-sai'],
            isCurrent: true
        }
    ],

    // Active workspace configuration
    activeWorkspaceId: 'ws-grid',

    // Slack Sidebar Sections
    myTeam: {
        id: 'section-my-team',
        label: 'My team',
        icon: '😄',
        channels: [
            {
                id: 'chan-grid',
                name: 'GRID',
                isPrivate: false,
                topic: 'GRID Platform Architecture & Core Sync',
                description: 'Collaborative development for the GRID ecosystem',
                memberCount: 2,
                unread: 0
            }
        ]
    },

    channels: [
        {
            id: 'chan-all-grid',
            name: 'all-grid',
            isPrivate: false,
            topic: 'Company-wide announcements and updates',
            memberCount: 4,
            unread: 0
        },
        {
            id: 'chan-new-channel',
            name: 'new-channel',
            isPrivate: false,
            topic: 'Brainstorming and sandbox ideas',
            memberCount: 2,
            unread: 1
        },
        {
            id: 'chan-social',
            name: 'social',
            isPrivate: false,
            topic: 'Coffee chats, weekend plans, and casual vibes',
            memberCount: 3,
            unread: 0
        }
    ],

    // Direct Messages
    directMessages: [
        {
            id: 'dm-sai',
            userId: 'user-sai',
            name: 'Sai Tulasi Ram',
            shortName: 'sai',
            status: 'away',
            statusColor: '#F59E0B', // orange
            avatar: '',
            initials: 'ST',
            unread: 0
        },
        {
            id: 'dm-pavan',
            userId: 'user-pavan',
            name: 'K Pavan Kumar (you)',
            shortName: 'pavan',
            status: 'online',
            statusColor: '#10B981', // green
            avatar: '',
            initials: 'KP',
            unread: 0
        },
        {
            id: 'dm-elena',
            userId: 'user-elena',
            name: 'Elena Rostova',
            shortName: 'elena',
            status: 'online',
            statusColor: '#10B981',
            avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&auto=format&fit=crop&q=80',
            initials: 'ER',
            unread: 0
        },
        {
            id: 'dm-marcus',
            userId: 'user-marcus',
            name: 'Marcus Vance',
            shortName: 'marcus',
            status: 'offline',
            statusColor: '#94A3B8',
            avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
            initials: 'MV',
            unread: 0
        }
    ],

    // Apps & Connected Integrations
    apps: [
        { id: 'app-drive', name: 'Google Drive', icon: 'drive' },
        { id: 'app-slack', name: 'Slack', icon: 'slack' },
        { id: 'app-calendar', name: 'Google Calendar', icon: 'calendar', external: true }
    ],

    // Messages repository keyed by conversation ID
    messages: {
        'chan-grid': [
            {
                id: 'm1',
                senderId: 'user-sai',
                senderName: 'Sai Tulasi Ram',
                avatar: '',
                initials: 'ST',
                time: '11:15 AM',
                date: 'Today',
                content: 'Hey @Pavan, have you reviewed the new attention heads benchmark for the neural engine?',
                reactions: [{ emoji: '🚀', count: 2, users: ['user-pavan', 'user-sai'] }]
            },
            {
                id: 'm2',
                senderId: 'user-pavan',
                senderName: 'K Pavan Kumar',
                avatar: '',
                initials: 'KP',
                time: '11:22 AM',
                date: 'Today',
                content: 'Yes! The batch latency dropped by 34%. Pushing the evaluation checkpoints to the cluster now.',
                reactions: [{ emoji: '🔥', count: 1, users: ['user-sai'] }]
            },
            {
                id: 'm3',
                senderId: 'user-sai',
                senderName: 'Sai Tulasi Ram',
                avatar: '',
                initials: 'ST',
                time: '11:28 AM',
                date: 'Today',
                content: 'Awesome. Let’s do a quick huddle at 2 PM to sync on deployment.',
                reactions: [{ emoji: '👍', count: 2, users: ['user-pavan', 'user-sai'] }]
            }
        ],

        'chan-all-grid': [
            {
                id: 'm_all_1',
                senderId: 'user-pavan',
                senderName: 'K Pavan Kumar',
                avatar: '',
                initials: 'KP',
                time: '9:00 AM',
                date: 'Yesterday',
                content: 'Welcome to the GRID workspace team! All project assets, repositories, and direct chat channels are live.',
                reactions: [{ emoji: '🎉', count: 4, users: ['user-pavan', 'user-sai', 'user-elena', 'user-marcus'] }]
            }
        ],

        'dm-sai': [
            {
                id: 'm_dm_1',
                senderId: 'user-sai',
                senderName: 'Sai Tulasi Ram',
                avatar: '',
                initials: 'ST',
                time: 'Yesterday',
                date: 'Yesterday',
                content: 'Hey Pavan, ping me whenever you have 5 minutes for the direct model inference review.',
                reactions: []
            }
        ],

        'dm-elena': [
            {
                id: 'm_dm_2',
                senderId: 'user-elena',
                senderName: 'Elena Rostova',
                avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&auto=format&fit=crop&q=80',
                initials: 'ER',
                time: '10:45 AM',
                date: 'Today',
                content: 'Elena here! The memory isolation benchmarks look stellar.',
                reactions: [{ emoji: '✨', count: 1, users: ['user-pavan'] }]
            }
        ]
    }
};

export function getTeamInitialState() {
    try {
        if (typeof window !== 'undefined') {
            if (localStorage.getItem('grid_team_item_id') === 'chan-nna') {
                localStorage.setItem('grid_team_item_id', 'chan-grid');
            }
            localStorage.removeItem('grid_team_state_standalone_v1');
            localStorage.removeItem('grid_team_state_v1');
            localStorage.removeItem('grid_team_state');

            const saved = localStorage.getItem('grid_team_state_standalone_v2');
            if (saved) {
                const parsed = JSON.parse(saved);
                // Migrate any old 'nna' channel reference
                if (parsed?.myTeam?.channels) {
                    parsed.myTeam.channels = parsed.myTeam.channels.map(c => 
                        (c.name === 'nna' || c.id === 'chan-nna') ? { ...c, id: 'chan-grid', name: 'GRID' } : c
                    );
                }
                if (parsed?.channels) {
                    parsed.channels = parsed.channels.map(c => 
                        (c.name === 'nna' || c.id === 'chan-nna') ? { ...c, id: 'chan-grid', name: 'GRID' } : c
                    );
                }
                if (parsed?.messages) {
                    if (parsed.messages['chan-nna'] && !parsed.messages['chan-grid']) {
                        parsed.messages['chan-grid'] = parsed.messages['chan-nna'];
                    }
                    delete parsed.messages['chan-nna'];
                }
                return parsed;
            }
        }
    } catch (e) {}
    return initialTeamData;
}

export function saveTeamState(state) {
    try {
        localStorage.setItem('grid_team_state_standalone_v2', JSON.stringify(state));
    } catch (e) {}
}
