import React, { useState, useEffect } from 'react';
import Header from './Header.jsx';
import MobileNav from './MobileNav.jsx';
import Toast from '../modals/Toast.jsx';
import VisitingCardModal from '../modals/VisitingCardModal.jsx';
import ScanQrModal from '../modals/ScanQrModal.jsx';
import AuroraBackground from '../ui/AuroraBackground.jsx';

/**
 * AppShell Component
 * Unified layout shell wrapping both GRID Home Workspace and GRID Profile.
 * Provides:
 * - Single source of truth for global background (wrapped in AuroraBackground)
 * - Centralized Theme Management (Obsidian Dark & Executive Light) with persistence
 * - Single unified Header (brand logo, search, synchronized top-nav, theme toggle)
 * - Single accessible MobileNav drawer
 * - Shared global Toast feedback notification
 * - Glassmorphism Modals for Visiting Card and QR Scanner
 */
export default function AppShell({ activeNav, onNavigate, user, currentUser, toastMessage, onCloseToast, children }) {
    const activeUser = user || currentUser || { fullName: 'User', initials: 'U' };
    const [isMobileNavOpen, setIsMobileNavOpen] = useState(false);
    const [isVisitingCardOpen, setIsVisitingCardOpen] = useState(false);
    const [isScanQrOpen, setIsScanQrOpen] = useState(false);

    // Theme state: defaults to 'light' (Executive White), with localStorage memory
    const [theme, setTheme] = useState(() => {
        try {
            // Check if user explicitly manually toggled previously
            const manual = localStorage.getItem('grid_theme_manual');
            const saved = localStorage.getItem('grid_theme');
            if (manual === 'true' && (saved === 'dark' || saved === 'light')) {
                return saved;
            }
        } catch (e) {}
        return 'light'; // White theme by default
    });

    useEffect(() => {
        try {
            localStorage.setItem('grid_theme', theme);
        } catch (e) {}

        const root = document.documentElement;
        const body = document.body;

        if (theme === 'dark') {
            root.classList.add('dark');
            body.classList.add('dark');
            root.setAttribute('data-theme', 'dark');
            body.setAttribute('data-theme', 'dark');
        } else {
            root.classList.remove('dark');
            body.classList.remove('dark');
            root.setAttribute('data-theme', 'light');
            body.setAttribute('data-theme', 'light');
        }
    }, [theme]);

    // Viewport Scroll Lock for Fixed Workspaces (Messaging or Team)
    const isMessaging = activeNav === 'messaging';
    const isTeam = activeNav === 'team';
    const isFixedViewport = isMessaging || isTeam;

    useEffect(() => {
        const root = document.documentElement;
        const body = document.body;

        if (isFixedViewport) {
            root.classList.add('g-lock-scroll');
            body.classList.add('g-lock-scroll');
        } else {
            root.classList.remove('g-lock-scroll');
            body.classList.remove('g-lock-scroll');
        }

        return () => {
            root.classList.remove('g-lock-scroll');
            body.classList.remove('g-lock-scroll');
        };
    }, [isFixedViewport]);

    const handleToggleTheme = () => {
        setTheme(prev => {
            const next = prev === 'dark' ? 'light' : 'dark';
            try {
                localStorage.setItem('grid_theme_manual', 'true');
            } catch (e) {}
            return next;
        });
    };

    useEffect(() => {
        const handleOpenCard = () => setIsVisitingCardOpen(true);
        const handleOpenQr = () => setIsScanQrOpen(true);
        window.addEventListener('grid:open-visiting-card', handleOpenCard);
        window.addEventListener('grid:open-scan-qr', handleOpenQr);
        return () => {
            window.removeEventListener('grid:open-visiting-card', handleOpenCard);
            window.removeEventListener('grid:open-scan-qr', handleOpenQr);
        };
    }, []);

    return (
        <AuroraBackground theme={theme} isFixedViewport={isFixedViewport}>
            <div className={`g-app-shell ${isFixedViewport ? 'g-shell-fixed' : ''} ${isTeam ? 'h-full' : ''}`}>
                {/* 1. Universal Top Header with Theme Toggle (hidden in standalone Team workspace) */}
                {!isTeam && (
                    <Header
                        user={activeUser}
                        activeNav={activeNav}
                        onNavigate={onNavigate}
                        theme={theme}
                        onToggleTheme={handleToggleTheme}
                        onOpenMobileNav={() => setIsMobileNavOpen(true)}
                    />
                )}

                {/* 2. Universal Mobile Drawer (< 1024px) (hidden in standalone Team workspace) */}
                {!isTeam && (
                    <MobileNav
                        isOpen={isMobileNavOpen}
                        onClose={() => setIsMobileNavOpen(false)}
                        user={activeUser}
                        theme={theme}
                        onToggleTheme={handleToggleTheme}
                        onNavigate={onNavigate}
                        activeNav={activeNav}
                        onOpenVisitingCard={() => setIsVisitingCardOpen(true)}
                        onOpenScanQr={() => setIsScanQrOpen(true)}
                    />
                )}

                {/* 3. Page Stage Content (Home Workspace, Profile Showcase, Messaging, or Team) */}
                <div className={`g-page-content flex-1 w-full max-w-full overflow-x-hidden ${isMessaging ? 'g-page-content-fixed' : ''} ${isTeam ? 'h-full flex flex-col' : ''}`}>
                    {children}
                </div>

                {/* 4. Glassmorphism Popups for Visiting Card & QR Scanner */}
                <VisitingCardModal
                    isOpen={isVisitingCardOpen}
                    onClose={() => setIsVisitingCardOpen(false)}
                    user={user}
                    onToast={onCloseToast}
                />

                <ScanQrModal
                    isOpen={isScanQrOpen}
                    onClose={() => setIsScanQrOpen(false)}
                    user={user}
                    onToast={onCloseToast}
                />

                {/* 5. Universal Toast Notification */}
                <Toast message={toastMessage} onClose={onCloseToast} />
            </div>
        </AuroraBackground>
    );
}
