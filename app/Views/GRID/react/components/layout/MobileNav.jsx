import React, { useState } from 'react';

/**
 * MobileNav Component
 * Off-canvas slide-out navigation drawer with integrated Theme Switcher
 * and single-page navigation links.
 */
export default function MobileNav({
    isOpen,
    onClose,
    user,
    theme = 'light',
    onToggleTheme,
    onNavigate,
    activeNav = 'home',
    onOpenVisitingCard,
    onOpenScanQr
}) {
    const [isJobsExpanded, setIsJobsExpanded] = useState(activeNav === 'jobs');

    if (!isOpen) return null;

    const handleNav = (targetRoute) => {
        if (targetRoute === 'team') {
            onClose();
            window.open('/index.php/grid/team', '_blank');
            return;
        }
        if (onNavigate) {
            onNavigate(targetRoute);
        }
        onClose();
    };

    return (
        <div className="fixed inset-0 z-[100] flex justify-end overflow-hidden" role="dialog" aria-modal="true" aria-label="Mobile Navigation">
            {/* Backdrop */}
            <div 
                className="fixed inset-0 bg-slate-900/60 dark:bg-black/75 backdrop-blur-xs transition-opacity"
                onClick={onClose}
                aria-hidden="true"
            />

            {/* Off-canvas Drawer Panel */}
            <aside className="relative w-80 max-w-[85vw] h-full bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 shadow-2xl flex flex-col z-10 overflow-y-auto">
                {/* 1. Header with User Identity and Close Button */}
                <div className="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-slate-50/70 dark:bg-slate-800/40">
                    <div className="flex items-center gap-3 min-w-0">
                        {user?.avatarSrc ? (
                            <img 
                                src={user.avatarSrc} 
                                alt={user.fullName || 'User'} 
                                className="w-10 h-10 rounded-full object-cover border border-slate-200 dark:border-slate-700 shrink-0" 
                            />
                        ) : (
                            <div className="w-10 h-10 rounded-full bg-blue-600 text-white font-bold text-sm flex items-center justify-center shrink-0">
                                {user?.initials || 'U'}
                            </div>
                        )}
                        <div className="min-w-0">
                            <div className="font-bold text-sm text-slate-900 dark:text-slate-100 truncate">
                                {user?.fullName || 'Praful Shah'}
                            </div>
                            <div className="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                {user?.headline ? `${user.headline.slice(0, 30)}...` : 'Executive Member'}
                            </div>
                        </div>
                    </div>

                    <button 
                        type="button" 
                        onClick={onClose} 
                        className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-700 transition-colors cursor-pointer"
                        aria-label="Close menu"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {/* 2. Theme Switcher Card inside Hamburger Drawer */}
                <div className="p-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-900/50">
                    <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Appearance / Theme
                    </div>
                    <div className="grid grid-cols-2 gap-2 bg-slate-200/70 dark:bg-slate-800 p-1 rounded-xl">
                        {/* Light Mode Button */}
                        <button
                            type="button"
                            onClick={() => {
                                if (theme !== 'light' && onToggleTheme) onToggleTheme();
                            }}
                            className={`flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg text-xs font-semibold transition-all cursor-pointer ${
                                theme === 'light'
                                    ? 'bg-white text-slate-900 shadow-xs'
                                    : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100'
                            }`}
                        >
                            <span>☀️ Light</span>
                        </button>

                        {/* Dark Mode Button */}
                        <button
                            type="button"
                            onClick={() => {
                                if (theme !== 'dark' && onToggleTheme) onToggleTheme();
                            }}
                            className={`flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg text-xs font-semibold transition-all cursor-pointer ${
                                theme === 'dark'
                                    ? 'bg-slate-950 text-white shadow-xs'
                                    : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100'
                            }`}
                        >
                            <span>🌙 Dark</span>
                        </button>
                    </div>
                </div>

                {/* 3. Primary Navigation Menu */}
                <div className="p-3 flex-1 overflow-y-auto space-y-1">
                    <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 py-1.5">
                        Navigation
                    </div>

                    {/* Home */}
                    <button
                        type="button"
                        onClick={() => handleNav('home')}
                        className={`w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors cursor-pointer ${
                            activeNav === 'home'
                                ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                        }`}
                    >
                        <div className="flex items-center gap-3">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Home Workspace</span>
                        </div>
                        {activeNav === 'home' && <span className="w-1.5 h-1.5 rounded-full bg-blue-600" />}
                    </button>

                    {/* My Visiting Card (Glassmorphism Modal Trigger) */}
                    <button
                        type="button"
                        onClick={() => {
                            onClose();
                            if (onOpenVisitingCard) onOpenVisitingCard();
                        }}
                        className="w-full flex items-center px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                    >
                        <div className="flex items-center gap-3">
                            <svg className="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                            <span>My Visiting Card</span>
                        </div>
                    </button>

                    {/* Scan QR Code (Glassmorphism Modal Trigger) */}
                    <button
                        type="button"
                        onClick={() => {
                            onClose();
                            if (onOpenScanQr) onOpenScanQr();
                        }}
                        className="w-full flex items-center px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                    >
                        <div className="flex items-center gap-3">
                            <svg className="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span>Scan QR Code</span>
                        </div>
                    </button>

                    {/* Profile */}
                    <button
                        type="button"
                        onClick={() => handleNav('profile')}
                        className={`w-full flex items-center px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors cursor-pointer ${
                            activeNav === 'profile'
                                ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                        }`}
                    >
                        <div className="flex items-center gap-3">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Professional Profile</span>
                        </div>
                    </button>

                    {/* Messaging */}
                    <button
                        type="button"
                        onClick={() => handleNav('messaging')}
                        className={`w-full flex items-center px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors cursor-pointer ${
                            activeNav === 'messaging'
                                ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                        }`}
                    >
                        <div className="flex items-center gap-3">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Messages &amp; Channels</span>
                        </div>
                    </button>

                    {/* Team Workspace */}
                    <button
                        type="button"
                        onClick={() => handleNav('team')}
                        className="w-full flex items-center px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                    >
                        <div className="flex items-center gap-3">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Team Workspace</span>
                        </div>
                    </button>

                    {/* Network */}
                    <a
                        href="/index.php/grid/network"
                        onClick={(e) => {
                            e.preventDefault();
                            handleNav('network');
                        }}
                        className={`w-full flex items-center px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors cursor-pointer ${
                            activeNav === 'network'
                                ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                        }`}
                    >
                        <div className="flex items-center gap-3">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Network Opportunities</span>
                        </div>
                    </a>

                    {/* Jobs Hub (Collapsible Accordion with Down Arrow) */}
                    <div>
                        <div
                            className={`w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors cursor-pointer ${
                                activeNav === 'jobs'
                                    ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                            }`}
                        >
                            <div
                                onClick={() => {
                                    sessionStorage.removeItem('grid_open_tracker');
                                    handleNav('jobs');
                                    window.dispatchEvent(new CustomEvent('grid:open-jobs-search'));
                                }}
                                className="flex items-center gap-3 flex-1"
                            >
                                <svg className="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>Jobs Hub</span>
                            </div>

                            {/* Down / Up Arrow Button */}
                            <button
                                type="button"
                                onClick={(e) => {
                                    e.stopPropagation();
                                    setIsJobsExpanded(prev => !prev);
                                }}
                                className="p-1 rounded-md text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/50 dark:hover:bg-slate-700/50 transition-all cursor-pointer shrink-0 ml-2"
                                aria-label="Toggle Jobs Hub sub-menu"
                            >
                                <svg
                                    className={`w-4 h-4 transition-transform duration-200 ${isJobsExpanded ? 'rotate-180' : 'rotate-0'}`}
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>

                        {/* Sub-menu options matching reference design */}
                        {isJobsExpanded && (
                            <div className="ml-7 pl-3 border-l-2 border-slate-200 dark:border-slate-800 space-y-1 my-1 animate-in fade-in slide-in-from-top-1 duration-150">
                                {/* 1. Job Search */}
                                <button
                                    type="button"
                                    onClick={() => {
                                        sessionStorage.removeItem('grid_open_tracker');
                                        handleNav('jobs');
                                        window.dispatchEvent(new CustomEvent('grid:open-jobs-search'));
                                    }}
                                    className="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors text-left cursor-pointer"
                                >
                                    <svg className="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <span>Job Search</span>
                                </button>

                                {/* 2. Job Tracking */}
                                <button
                                    type="button"
                                    onClick={() => {
                                        sessionStorage.setItem('grid_open_tracker', 'true');
                                        handleNav('jobs');
                                        window.dispatchEvent(new CustomEvent('grid:open-jobs-tracker'));
                                    }}
                                    className="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors text-left cursor-pointer"
                                >
                                    <svg className="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    <span>Job Tracking</span>
                                </button>
                            </div>
                        )}
                    </div>

                    <div className="pt-4 pb-1">
                        <div className="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 py-1">
                            Verified Spaces
                        </div>
                        <div className="space-y-1">
                            <a href="#announcements" onClick={onClose} className="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <span>📢</span> Announcements
                            </a>
                            <a href="#designtalk" onClick={onClose} className="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <span>🎨</span> Design Talk &amp; Critique
                            </a>
                            <a href="#productideas" onClick={onClose} className="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <span>💡</span> Product Engineering
                            </a>
                            <a href="#airesearch" onClick={onClose} className="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <span>⚡</span> AI &amp; ML Research Lab
                            </a>
                        </div>
                    </div>
                </div>

                {/* 4. Footer Actions */}
                <div className="p-4 border-t border-slate-100 dark:border-slate-800 shrink-0 space-y-2 bg-slate-50/50 dark:bg-slate-900">
                    <button
                        type="button"
                        onClick={() => handleNav('profile')}
                        className="w-full py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-all shadow-sm flex items-center justify-center gap-1.5"
                    >
                        <span>View Full Profile</span>
                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>

                    <div className="flex items-center justify-between text-[11px] text-slate-500 pt-1">
                        <a href="/index.php/grid/settings" onClick={onClose} className="hover:underline">Settings &amp; Privacy</a>
                        <a href="/index.php/auth/logout" className="text-red-500 hover:underline">Sign Out</a>
                    </div>
                </div>
            </aside>
        </div>
    );
}
