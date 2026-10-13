import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom/client';
import AppShell from './components/layout/AppShell.jsx';
import GridProfileApp from './GridProfileApp.jsx';
import GridPeerProfileApp from './GridPeerProfileApp.jsx';
import GridHomeApp from './GridHomeApp.jsx';
import GridMessagingApp from './components/messaging/GridMessagingApp.jsx';
import GridTeamApp from './components/team/GridTeamApp.jsx';
import GridNetworkApp from './GridNetworkApp.jsx';
import GridJobsApp from './GridJobsApp.jsx';
import ProfileSetupWizard from './components/wizard/ProfileSetupWizard.jsx';

/**
 * AppRouter Component
 * Light-weight, URL-synchronized router for the unified GRID application:
 * - Direct URL / deep links:
 *     /grid           -> GridHomeApp (Workspace & Feed)
 *     /grid/profile   -> GridProfileApp (Professional Portfolio & Identity)
 *     /grid/messaging -> GridMessagingApp (Secure Professional Messaging)
 *     /grid/team      -> GridTeamApp (Slack-style Team Workspace & Wizard)
 *     /grid/network   -> GridNetworkApp (Verified Peer Graph & Opportunities)
 *     /grid/jobs      -> GridJobsApp (LinkedIn-style Jobs Hub & Career Matches)
 * - Seamless SPA client-side transitions via onNavigate callback & popstate listener.
 * - Unified AppShell provides shared background, header, mobile drawer, and toast feedback.
 */
function AppRouter() {
    const getInitialRoute = () => {
        const path = window.location.pathname.toLowerCase();
        if (path.includes('/profile')) return 'profile';
        if (path.includes('/messaging') || path.includes('/messages')) return 'messaging';
        if (path.includes('/team')) return 'team';
        if (path.includes('/network')) return 'network';
        if (path.includes('/jobs') || path.includes('/job')) return 'jobs';
        return 'home';
    };

    const [route, setRoute] = useState(getInitialRoute);
    const [viewingProfileUser, setViewingProfileUser] = useState(() => {
        try {
            const params = new URLSearchParams(window.location.search);
            const viewUserId = params.get('userId') || params.get('u');
            return viewUserId ? { id: viewUserId } : null;
        } catch (e) {
            return null;
        }
    });
    const [messagingTargetUser, setMessagingTargetUser] = useState(null);
    const [toastMessage, setToastMessage] = useState(null);

    const [isSetupWizardOpen, setIsSetupWizardOpen] = useState(() => {
        if (typeof window !== 'undefined') {
            const params = new URLSearchParams(window.location.search);
            if (params.get('setup') === 'true') return true;
            if (window.__GRID_DATA__ && window.__GRID_DATA__.user && window.__GRID_DATA__.user.isWizardCompleted === false) {
                return true;
            }
        }
        return false;
    });

    const [userState, setUserState] = useState(() => {
        const u = (typeof window !== 'undefined' && window.__GRID_DATA__ && window.__GRID_DATA__.user) || {};
        return {
            fullName: u.name || 'User',
            avatarSrc: u.avatar || '',
            initials: u.initials || 'U'
        };
    });

    const showToast = (msg) => {
        setToastMessage(msg);
        setTimeout(() => {
            setToastMessage((prev) => (prev === msg ? null : prev));
        }, 3500);
    };

    useEffect(() => {
        const handleOpenWizard = () => setIsSetupWizardOpen(true);
        window.addEventListener('grid:open-profile-wizard', handleOpenWizard);
        return () => window.removeEventListener('grid:open-profile-wizard', handleOpenWizard);
    }, []);

    useEffect(() => {
        const handleCompleted = (e) => {
            const data = e.detail;
            if (!data) return;
            const nameParts = (data.full_name || '').trim().split(/\s+/);
            const initials = ((nameParts[0]?.[0] || '') + (nameParts[1]?.[0] || 'U')).toUpperCase();
            setUserState(prev => ({
                ...prev,
                fullName: data.full_name || prev.fullName,
                initials: initials
            }));
        };
        window.addEventListener('grid:profile-wizard-completed', handleCompleted);

        const handleAvatarUpdated = (e) => {
            const avatarUrl = e.detail;
            if (avatarUrl) {
                setUserState(prev => ({
                    ...prev,
                    avatarSrc: avatarUrl
                }));
            }
        };
        window.addEventListener('grid:avatar-updated', handleAvatarUpdated);

        return () => {
            window.removeEventListener('grid:profile-wizard-completed', handleCompleted);
            window.removeEventListener('grid:avatar-updated', handleAvatarUpdated);
        };
    }, []);

    const handleWizardFinish = (wizardData) => {
        setIsSetupWizardOpen(false);
        if (window.__GRID_DATA__ && window.__GRID_DATA__.user) {
            window.__GRID_DATA__.user.isWizardCompleted = true;
            if (wizardData.full_name) window.__GRID_DATA__.user.name = wizardData.full_name;
            if (wizardData.headline) window.__GRID_DATA__.user.headline = wizardData.headline;
            if (wizardData.location) window.__GRID_DATA__.user.location = wizardData.location;
            if (wizardData.bio) window.__GRID_DATA__.user.bio = wizardData.bio;
            if (wizardData.grid_id) window.__GRID_DATA__.user.gridId = wizardData.grid_id;
        }
        showToast('Profile onboarding completed successfully! 🚀');
        window.dispatchEvent(new CustomEvent('grid:profile-wizard-completed', { detail: wizardData }));
    };

    useEffect(() => {
        const handlePopState = (event) => {
            const path = window.location.pathname.toLowerCase();
            const urlParams = new URLSearchParams(window.location.search);
            const viewUserId = urlParams.get('userId') || urlParams.get('u');
            const stateTargetUser = event.state?.targetUser;

            if (path.includes('/profile')) {
                setRoute('profile');
                setViewingProfileUser(stateTargetUser || (viewUserId ? { id: viewUserId } : null));
                setMessagingTargetUser(null);
            } else if (path.includes('/messaging') || path.includes('/messages')) {
                setRoute('messaging');
                setViewingProfileUser(null);
                setMessagingTargetUser(event.state?.targetUser || null);
            } else if (path.includes('/team')) {
                setRoute('team');
                setViewingProfileUser(null);
                setMessagingTargetUser(null);
            } else if (path.includes('/network')) {
                setRoute('network');
                setViewingProfileUser(null);
                setMessagingTargetUser(null);
            } else if (path.includes('/jobs') || path.includes('/job')) {
                setRoute('jobs');
                setViewingProfileUser(null);
                setMessagingTargetUser(null);
            } else {
                setRoute('home');
                setViewingProfileUser(null);
                setMessagingTargetUser(null);
            }
        };

        window.addEventListener('popstate', handlePopState);
        return () => window.removeEventListener('popstate', handlePopState);
    }, []);

    const handleNavigate = (targetRoute, params = {}) => {
        setRoute(targetRoute);
        if (targetRoute === 'profile') {
            setViewingProfileUser(params.targetUser || null);
            setMessagingTargetUser(null);
        } else if (targetRoute === 'messaging') {
            setViewingProfileUser(null);
            setMessagingTargetUser(params.targetUser || null);
        } else {
            setViewingProfileUser(null);
            setMessagingTargetUser(null);
        }

        const baseUrl = window.location.pathname.includes('/index.php') ? '/index.php/grid' : '/grid';
        let targetUrl = baseUrl;
        if (targetRoute === 'profile') {
            const query = params.targetUser?.id ? `?userId=${params.targetUser.id}` : '';
            targetUrl = `${baseUrl}/profile${query}`;
        } else if (targetRoute === 'messaging') {
            const query = params.targetUser?.id ? `?user=${encodeURIComponent(params.targetUser.id)}` : '';
            targetUrl = `${baseUrl}/messaging${query}`;
        } else if (targetRoute === 'team') {
            targetUrl = `${baseUrl}/team`;
        } else if (targetRoute === 'network') {
            targetUrl = `${baseUrl}/network`;
        } else if (targetRoute === 'jobs') {
            targetUrl = `${baseUrl}/jobs`;
        }
        window.history.pushState({ route: targetRoute, ...params }, '', targetUrl);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // If onboarding wizard is active, render it as its own dedicated standalone page
    if (isSetupWizardOpen) {
        return (
            <ProfileSetupWizard
                initialName={userState.fullName}
                onCancel={() => setIsSetupWizardOpen(false)}
                onToast={showToast}
                onFinish={handleWizardFinish}
            />
        );
    }

    return (
        <AppShell
            activeNav={route}
            onNavigate={handleNavigate}
            currentUser={userState}
            toastMessage={toastMessage}
            onCloseToast={() => setToastMessage(null)}
        >
            {route === 'profile' ? (
                viewingProfileUser && viewingProfileUser.id && String(viewingProfileUser.id) !== String(userState?.id) ? (
                    <GridPeerProfileApp 
                        onNavigate={handleNavigate} 
                        onToast={showToast} 
                        targetUser={viewingProfileUser} 
                        currentUser={userState} 
                    />
                ) : (
                    <GridProfileApp 
                        onNavigate={handleNavigate} 
                        onToast={showToast} 
                        currentUser={userState} 
                    />
                )
            ) : route === 'messaging' ? (
                <GridMessagingApp 
                    onNavigate={handleNavigate} 
                    onToast={showToast} 
                    initialTargetUser={messagingTargetUser}
                />
            ) : route === 'team' ? (
                <GridTeamApp onNavigate={handleNavigate} onToast={showToast} />
            ) : route === 'network' ? (
                <GridNetworkApp onNavigate={handleNavigate} onToast={showToast} currentUser={userState} />
            ) : route === 'jobs' ? (
                <GridJobsApp onNavigate={handleNavigate} onToast={showToast} currentUser={userState} />
            ) : (
                <GridHomeApp onNavigate={handleNavigate} onToast={showToast} />
            )}
        </AppShell>
    );
}

const rootElement = document.getElementById('grid-profile-root');

if (rootElement) {
    const root = ReactDOM.createRoot(rootElement);
    root.render(
        <React.StrictMode>
            <AppRouter />
        </React.StrictMode>
    );
} else {
    console.error('GRID React: Root element #grid-profile-root was not found.');
}
