import React from 'react';
import { gridApi } from '../../gridApi';

export default function Header({
    user,
    onOpenMobileNav,
    onSearch,
    activeNav = 'profile',
    onNavigate,
    theme = 'dark',
    onToggleTheme,
}) {
    const [searchQuery, setSearchQuery] = React.useState('');
    const [searchResults, setSearchResults] = React.useState([]);
    const [isSearching, setIsSearching] = React.useState(false);
    const [showResults, setShowResults] = React.useState(false);
    const [isSearchActive, setIsSearchActive] = React.useState(false);
    const [activeDropdown, setActiveDropdown] = React.useState(null);
    const searchTimeoutRef = React.useRef(null);
    const searchWrapRef = React.useRef(null);

    function toggleDropdown(name) {
        setActiveDropdown(prev => prev === name ? null : name);
    }

    // Debounced people search for the header search bar
    const handleSearchChange = (value) => {
        setSearchQuery(value);
        if (onSearch) onSearch(value);

        if (searchTimeoutRef.current) clearTimeout(searchTimeoutRef.current);

        const trimmed = value.trim();
        if (trimmed.length < 2) {
            setSearchResults([]);
            setShowResults(false);
            setIsSearching(false);
            return;
        }

        setIsSearching(true);
        setShowResults(true);
        searchTimeoutRef.current = setTimeout(async () => {
            try {
                const results = await gridApi.searchUsers(trimmed);
                setSearchResults(Array.isArray(results) ? results : []);
            } catch (err) {
                console.warn('Search failed:', err);
                setSearchResults([]);
            } finally {
                setIsSearching(false);
            }
        }, 250);
    };

    const clearSearch = () => {
        setSearchQuery('');
        setSearchResults([]);
        setShowResults(false);
        if (onSearch) onSearch('');
    };

    const handleSelectPerson = (person) => {
        clearSearch();
        setIsSearchActive(false);
        if (onNavigate) {
            onNavigate('profile', {
                targetUser: {
                    id: person.id,
                    name: person.name,
                    avatar: person.avatar,
                    headline: person.headline,
                    gridId: person.grid_id
                }
            });
        }
    };

    // Close the desktop results dropdown on outside click. The mobile overlay
    // manages its own visibility (back button / result selection), so this is
    // skipped while it's open — avoids a mousedown/click race when tapping a
    // result there.
    React.useEffect(() => {
        if (isSearchActive) return;
        const handleClickOutside = (e) => {
            if (searchWrapRef.current && !searchWrapRef.current.contains(e.target)) {
                setShowResults(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, [isSearchActive]);

    function SearchResultsDropdown() {
        if (!showResults) return null;
        return (
            <div className="g-popover-menu" style={{ width: '320px', top: 'calc(100% + 6px)', left: '0', maxHeight: '360px', overflowY: 'auto' }}>
                {isSearching ? (
                    <div style={{ padding: '14px', fontSize: '13px', color: 'var(--slate-500)' }}>Searching...</div>
                ) : searchResults.length === 0 ? (
                    <div style={{ padding: '14px', fontSize: '13px', color: 'var(--slate-500)' }}>
                        No people found for "{searchQuery}"
                    </div>
                ) : (
                    searchResults.map((person) => (
                        <div
                            key={person.id}
                            onClick={() => handleSelectPerson(person)}
                            style={{
                                display: 'flex',
                                alignItems: 'center',
                                gap: '10px',
                                padding: '8px 14px',
                                cursor: 'pointer'
                            }}
                            onMouseEnter={(e) => { e.currentTarget.style.background = 'var(--slate-100)'; }}
                            onMouseLeave={(e) => { e.currentTarget.style.background = 'transparent'; }}
                        >
                            {person.avatar ? (
                                <img
                                    src={person.avatar}
                                    alt={person.name}
                                    style={{ width: '32px', height: '32px', borderRadius: '50%', objectFit: 'cover', flexShrink: 0 }}
                                    onError={(e) => { e.currentTarget.style.display = 'none'; }}
                                />
                            ) : (
                                <div style={{ width: '32px', height: '32px', borderRadius: '50%', background: 'var(--brand-600)', color: '#fff', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '12px', fontWeight: 700, flexShrink: 0 }}>
                                    {(person.name || 'U').slice(0, 2).toUpperCase()}
                                </div>
                            )}
                            <div style={{ minWidth: 0 }}>
                                <div style={{ fontSize: '13px', fontWeight: 600, color: 'var(--slate-900)', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                    {person.name || 'GRID Member'}
                                </div>
                                <div style={{ fontSize: '11px', color: 'var(--slate-500)', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                    {person.headline || 'GRID Member'}
                                </div>
                            </div>
                        </div>
                    ))
                )}
            </div>
        );
    }

    const handleHomeClick = (e) => {
        if (onNavigate) {
            e.preventDefault();
            onNavigate('home');
        }
    };

    const handleProfileClick = (e) => {
        if (onNavigate) {
            e.preventDefault();
            setActiveDropdown(null);
            onNavigate('profile');
        }
    };

    const handleMessagingClick = (e) => {
        if (onNavigate) {
            e.preventDefault();
            setActiveDropdown(null);
            onNavigate('messaging');
        }
    };

    const handleNetworkClick = (e) => {
        if (onNavigate) {
            e.preventDefault();
            setActiveDropdown(null);
            onNavigate('network');
        }
    };

    const handleTeamClick = (e) => {
        e.preventDefault();
        setActiveDropdown(null);
        window.open('/index.php/grid/team', '_blank');
    };

    const handleJobsClick = (e) => {
        if (onNavigate) {
            e.preventDefault();
            setActiveDropdown(null);
            onNavigate('jobs');
        }
    };

    return (
        <header className="g-header">
            {/* Mobile Full-Width Search Overlay */}
            {isSearchActive && (
                <div className="g-mobile-search-overlay">
                    <div style={{ position: 'relative', display: 'flex', alignItems: 'center', width: '100%' }}>
                        <button
                            type="button"
                            onClick={() => setIsSearchActive(false)}
                            className="p-2 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200"
                            aria-label="Back"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </button>
                        <input
                            type="text"
                            autoFocus
                            placeholder="Search people on GRID..."
                            value={searchQuery}
                            onChange={(e) => handleSearchChange(e.target.value)}
                            className="flex-1 bg-transparent text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none"
                        />
                        {searchQuery && (
                            <button
                                type="button"
                                onClick={clearSearch}
                                className="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        )}
                        <SearchResultsDropdown />
                    </div>
                </div>
            )}

            <div className="g-header-inner">
                <div className="g-header-left">
                    {/* Logo (Dynamically adapts between Light & Dark modes) */}
                    <a href="/index.php/grid" className="g-logo" onClick={handleHomeClick} title="GRID Workspace">
                        <img
                            src={
                                theme === 'dark'
                                    ? ((typeof window !== 'undefined' && window.__GRID_DATA__?.images?.logoDark) || '/Views/GRID/images/grid_logo_dark.png')
                                    : ((typeof window !== 'undefined' && window.__GRID_DATA__?.images?.logoLight) || '/Views/GRID/images/grid_logo_light.png')
                            }
                            alt="GRID"
                            className="g-logo-img"
                            style={{
                                height: '44px',
                                width: 'auto',
                                maxHeight: '48px',
                                objectFit: 'contain',
                                display: 'block',
                                filter: theme === 'dark' ? 'none' : 'drop-shadow(0 2px 4px rgba(15, 23, 42, 0.24)) drop-shadow(0 1px 2px rgba(15, 23, 42, 0.28))',
                                transition: 'filter 0.2s ease'
                            }}
                        />
                    </a>
                </div>

                {/* Middle Area between Brand Icon and Home: Centered Search Bar */}
                <div className="g-header-center">
                    <div ref={searchWrapRef} className={`g-search-wrap ${isSearchActive ? 'g-mobile-search-active' : ''}`}>
                        <div className="g-search-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            placeholder="Search people on GRID..."
                            value={searchQuery}
                            onChange={(e) => handleSearchChange(e.target.value)}
                            onFocus={() => { if (searchResults.length > 0 || searchQuery.trim().length >= 2) setShowResults(true); }}
                            autoComplete="off"
                        />
                        <span className="g-search-shortcut">⌘K</span>
                        {/* Mobile search close button */}
                        <button
                            className="g-search-close-btn"
                            style={{ display: isSearchActive ? 'flex' : 'none' }}
                            onClick={() => setIsSearchActive(false)}
                            aria-label="Close search"
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <SearchResultsDropdown />
                    </div>
                </div>

                {/* Right Action / Navigation Links */}
                <div className="g-actions">
                    {/* Dedicated Mobile Action Cluster (< 1024px) */}
                    <div className="g-mobile-actions">
                        {/* 1. Mobile Search Trigger */}
                        <button
                            type="button"
                            className="g-mobile-btn"
                            onClick={() => setIsSearchActive(true)}
                            aria-label="Open search"
                            title="Search"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
                            </svg>
                        </button>

                        {/* 2. Mobile Theme Toggle (Sun/Moon) */}
                        <button
                            type="button"
                            className="g-mobile-btn"
                            onClick={onToggleTheme}
                            aria-label={theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode'}
                            title={theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode'}
                        >
                            {theme === 'dark' ? (
                                <svg className="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            ) : (
                                <svg className="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            )}
                        </button>

                        {/* 3. Mobile Avatar Thumbnail */}
                        <a
                            href="/index.php/grid/profile"
                            onClick={handleProfileClick}
                            className="g-mobile-avatar-btn"
                            title="View Profile"
                        >
                            {user?.avatarSrc ? (
                                <img src={user.avatarSrc} alt={user?.fullName || 'User'} className="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-slate-700" />
                            ) : (
                                <div className="w-8 h-8 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center">
                                    {user?.initials || 'U'}
                                </div>
                            )}
                        </a>

                        {/* 4. Hamburger Menu Button (Next to Profile) */}
                        <button
                            type="button"
                            className="g-mobile-btn g-hamburger-btn"
                            onClick={onOpenMobileNav}
                            aria-label="Open navigation menu"
                            title="Menu"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>

                    <nav className="g-top-nav">
                        {/* 1. Home */}
                        <a
                            href="/index.php/grid"
                            className={`g-top-nav-item ${activeNav === 'home' ? 'active' : ''}`}
                            onClick={handleHomeClick}
                        >
                            <div className="g-top-nav-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <span className="g-top-nav-text">Home</span>
                        </a>

                        {/* 2. Network */}
                        <a
                            href="/index.php/grid/network"
                            className={`g-top-nav-item ${activeNav === 'network' ? 'active' : ''}`}
                            onClick={handleNetworkClick}
                        >
                            <div className="g-top-nav-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span className="g-top-nav-text">Network</span>
                        </a>

                        {/* 3. Team */}
                        <a
                            href="/index.php/grid/team"
                            target="_blank"
                            rel="noopener noreferrer"
                            className={`g-top-nav-item ${activeNav === 'team' ? 'active' : ''}`}
                            onClick={handleTeamClick}
                            title="Team"
                        >
                            <div className="g-top-nav-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <span className="g-top-nav-text">Team</span>
                        </a>

                        {/* 4. Jobs */}
                        <a
                            href="/index.php/grid/jobs"
                            className={`g-top-nav-item ${activeNav === 'jobs' ? 'active' : ''}`}
                            onClick={handleJobsClick}
                        >
                            <div className="g-top-nav-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span className="g-top-nav-text">Jobs</span>
                        </a>

                        {/* 4. Messaging */}
                        <a
                            href="/index.php/grid/messaging"
                            className={`g-top-nav-item ${activeNav === 'messaging' ? 'active' : ''}`}
                            onClick={handleMessagingClick}
                            aria-label="Messaging"
                            title="Messaging Workspace"
                        >
                            <div className="g-top-nav-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span className="g-top-nav-badge" style={{ background: 'var(--brand-600)' }} />
                            </div>
                            <span className="g-top-nav-text">Messaging</span>
                        </a>

                        {/* 5. Notifications */}
                        <div className="g-dropdown-anchor">
                            <button
                                className={`g-top-nav-item ${activeDropdown === 'notifications' ? 'active' : ''}`}
                                onClick={() => toggleDropdown('notifications')}
                                aria-label="Notifications"
                            >
                                <div className="g-top-nav-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <span className="g-top-nav-badge" style={{ background: 'var(--emerald-500)' }} />
                                </div>
                                <span className="g-top-nav-text">Notifications</span>
                            </button>
                            {activeDropdown === 'notifications' && (
                                <div className="g-popover-menu" style={{ width: '300px', top: '100%', right: '0' }}>
                                    <div className="g-popover-header">
                                        <span>Notifications</span>
                                        <span className="g-badge-count">1 new</span>
                                    </div>
                                    <div style={{ fontSize: '12px', color: 'var(--slate-600)', padding: '8px 0' }}>
                                        🔔 <strong>Sarah J.</strong> endorsed your <strong>DPO</strong> skill.
                                    </div>
                                </div>
                            )}
                        </div>

                        <div className="g-top-nav-divider" />

                        {/* 6. Me (Profile Menu) */}
                        <div className="g-dropdown-anchor">
                            <button
                                className={`g-top-nav-item ${activeNav === 'profile' ? 'active' : ''}`}
                                onClick={() => toggleDropdown('profile')}
                                aria-label="Account Menu"
                                title={user?.fullName || 'User Profile'}
                            >
                                <div className="g-top-nav-avatar">
                                    {user?.avatarSrc ? (
                                        <img src={user.avatarSrc} alt={user?.fullName || 'User'} />
                                    ) : (
                                        user?.initials || 'U'
                                    )}
                                </div>
                                <span className="g-top-nav-text" style={{ display: 'flex', alignItems: 'center', gap: '2px' }}>
                                    Me
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style={{ width: '9px', height: '9px' }}>
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </button>
                            {activeDropdown === 'profile' && (
                                <div className="g-popover-menu" style={{ width: '220px', top: '100%', right: '0' }}>
                                    <div className="g-popover-header">
                                        <span>{user?.fullName || 'User Profile'}</span>
                                    </div>
                                    <div style={{ padding: '6px 0', fontSize: '13px' }}>
                                        <a
                                            href="/index.php/grid/profile"
                                            onClick={handleProfileClick}
                                            style={{ display: 'block', padding: '8px 14px', color: 'var(--brand-600)', fontWeight: 600 }}
                                        >
                                            View Profile
                                        </a>
                                        <a
                                            href="#setup"
                                            onClick={(e) => {
                                                e.preventDefault();
                                                setActiveDropdown(null);
                                                window.dispatchEvent(new CustomEvent('grid:open-profile-wizard'));
                                            }}
                                            style={{ display: 'block', padding: '8px 14px', color: 'var(--slate-700)' }}
                                        >
                                            🚀 Profile Setup Wizard
                                        </a>
                                        <a href="/index.php/grid/settings" style={{ display: 'block', padding: '8px 14px', color: 'var(--slate-700)' }}>Settings & Privacy</a>
                                        <a href="/index.php/auth/logout" style={{ display: 'block', padding: '8px 14px', color: 'var(--slate-700)', borderTop: '1px solid var(--slate-100)' }}>Sign Out</a>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* 7. Theme Toggle (Sun / Moon) */}
                        <button
                            type="button"
                            className="g-theme-toggle-btn"
                            onClick={onToggleTheme}
                            aria-label={theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode'}
                            title={theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode'}
                        >
                            {theme === 'dark' ? (
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" className="g-theme-icon">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            ) : (
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" className="g-theme-icon">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            )}
                        </button>
                    </nav>
                </div>
            </div>
        </header>
    );
}
