import React, { useState, useEffect, useRef } from 'react';

/**
 * ProfileActions Component
 * 
 * Interactive action buttons:
 * - "Open to" dropdown popover
 * - "Add profile section" dropdown popover
 * - "..." More options dropdown popover
 */
export default function ProfileActions({
    canEdit = true,
    user = {},
    onNavigate,
    onOpenToWork,
    onAddSection,
    onContactInfo,
    onEditViews,
    onToast
}) {
    const [activeDropdown, setActiveDropdown] = useState(null);
    const [isConnected, setIsConnected] = useState(false);
    const containerRef = useRef(null);

    // Close dropdowns on outside click or Escape key
    useEffect(() => {
        function handleClickOutside(e) {
            if (containerRef.current && !containerRef.current.contains(e.target)) {
                setActiveDropdown(null);
            }
        }
        function handleKeyDown(e) {
            if (e.key === 'Escape') setActiveDropdown(null);
        }
        document.addEventListener('mousedown', handleClickOutside);
        document.addEventListener('keydown', handleKeyDown);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
            document.removeEventListener('keydown', handleKeyDown);
        };
    }, []);

    function toggleDropdown(name) {
        setActiveDropdown(prev => prev === name ? null : name);
    }

    function handleConnect() {
        setIsConnected(prev => {
            const next = !prev;
            if (next && onToast) onToast('Connection request sent! 🤝');
            else if (onToast) onToast('Connection removed.');
            return next;
        });
    }

    function handleShareProfile(e) {
        e.preventDefault();
        setActiveDropdown(null);
        const url = window.location.href;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(() => {
                if (onToast) onToast('Profile URL copied to clipboard! 📋');
            });
        }
    }

    if (!canEdit) {
        return (
            <div className="g-hero-actions" ref={containerRef}>
                <button
                    type="button"
                    className="g-btn-primary"
                    onClick={handleConnect}
                    style={{
                        background: isConnected ? '#10b981' : 'var(--brand-primary, #6366f1)',
                        display: 'inline-flex',
                        alignItems: 'center',
                        gap: '6px'
                    }}
                >
                    <span>{isConnected ? '✓ Connected' : '+ Connect'}</span>
                </button>
                <button
                    type="button"
                    className="g-btn-outline"
                    onClick={() => {
                        if (onNavigate) {
                            onNavigate('messaging', { targetUser: user });
                        } else if (onToast) {
                            onToast('Direct messaging opened! 💬');
                        }
                    }}
                    style={{ display: 'inline-flex', alignItems: 'center', gap: '6px' }}
                >
                    <span>Message</span>
                </button>
                <div className="g-dropdown-anchor">
                    <button 
                        type="button"
                        className="g-btn-dots" 
                        onClick={() => toggleDropdown('more')}
                        aria-label="More options" 
                        aria-expanded={activeDropdown === 'more'}
                    >
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6 10a2 2 0 110 4 2 2 0 010-4zm6 0a2 2 0 110 4 2 2 0 010-4zm6 0a2 2 0 110 4 2 2 0 010-4z" />
                        </svg>
                    </button>
                    {activeDropdown === 'more' && (
                        <div className="g-popover-menu" style={{ display: 'block', width: '210px' }}>
                            <a href="#share" className="g-menu-link" onClick={handleShareProfile}>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                Share profile link
                            </a>
                            <a href="#contact" className="g-menu-link" onClick={(e) => { e.preventDefault(); setActiveDropdown(null); if (onContactInfo) onContactInfo(); }}>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Contact Info
                            </a>
                        </div>
                    )}
                </div>
            </div>
        );
    }

    return (
        <div className="g-hero-actions" ref={containerRef}>
            {/* 1. "Open to" Dropdown */}
            <div className="g-dropdown-anchor">
                <button 
                    type="button"
                    className="g-btn-primary" 
                    id="gOpenToBtn" 
                    onClick={() => toggleDropdown('openTo')}
                    aria-expanded={activeDropdown === 'openTo'} 
                    aria-label="Open to work or hiring options"
                >
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
                    </svg>
                    <span>Open to</span>
                </button>
                {activeDropdown === 'openTo' && (
                    <div className="g-popover-menu" id="gOpenToDropdown" style={{ display: 'block', width: '240px' }}>
                        <div className="g-popover-header"><strong>I am open to...</strong></div>
                        <a 
                            href="#open-to-work" 
                            className="g-menu-link" 
                            onClick={(e) => {
                                e.preventDefault();
                                setActiveDropdown(null);
                                if (onOpenToWork) onOpenToWork();
                            }}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Finding a new job
                        </a>
                        <a 
                            href="#hiring" 
                            className="g-menu-link"
                            onClick={(e) => {
                                e.preventDefault();
                                setActiveDropdown(null);
                                if (onToast) onToast('Hiring manager settings enabled.');
                            }}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Hiring talent
                        </a>
                        <a 
                            href="#services" 
                            className="g-menu-link"
                            onClick={(e) => {
                                e.preventDefault();
                                setActiveDropdown(null);
                                if (onToast) onToast('Consulting services activated.');
                            }}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Providing services
                        </a>
                    </div>
                )}
            </div>

            {/* 2. "Add profile section" Dropdown */}
            <div className="g-dropdown-anchor">
                <button 
                    type="button"
                    className="g-btn-outline" 
                    id="gAddSectionBtn" 
                    onClick={() => toggleDropdown('addSection')}
                    aria-expanded={activeDropdown === 'addSection'}
                >
                    <span>Add profile section</span>
                </button>
                {activeDropdown === 'addSection' && (
                    <div className="g-popover-menu" id="gAddSectionDropdown" style={{ display: 'block', width: '230px' }}>
                        <div className="g-popover-header"><strong>Add Section</strong></div>
                        <a 
                            href="#add-experience" 
                            className="g-menu-link"
                            onClick={(e) => {
                                e.preventDefault();
                                setActiveDropdown(null);
                                if (onAddSection) onAddSection('experience');
                            }}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Experience
                        </a>
                        <a 
                            href="#add-education" 
                            className="g-menu-link"
                            onClick={(e) => {
                                e.preventDefault();
                                setActiveDropdown(null);
                                if (onAddSection) onAddSection('education');
                            }}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                            </svg>
                            Education
                        </a>
                        <a 
                            href="#add-project" 
                            className="g-menu-link"
                            onClick={(e) => {
                                e.preventDefault();
                                setActiveDropdown(null);
                                if (onAddSection) onAddSection('project');
                            }}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Project
                        </a>
                        <a 
                            href="#add-cert" 
                            className="g-menu-link"
                            onClick={(e) => {
                                e.preventDefault();
                                setActiveDropdown(null);
                                if (onAddSection) onAddSection('certification');
                            }}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            Licenses &amp; Certifications
                        </a>
                        <a 
                            href="#add-skill" 
                            className="g-menu-link"
                            onClick={(e) => {
                                e.preventDefault();
                                setActiveDropdown(null);
                                if (onAddSection) onAddSection('skill');
                            }}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Skills
                        </a>
                        <a 
                            href="#add-honor" 
                            className="g-menu-link"
                            onClick={(e) => {
                                e.preventDefault();
                                setActiveDropdown(null);
                                if (onAddSection) onAddSection('honor');
                            }}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                            Honors &amp; Awards
                        </a>
                        <a 
                            href="#add-language" 
                            className="g-menu-link"
                            onClick={(e) => {
                                e.preventDefault();
                                setActiveDropdown(null);
                                if (onAddSection) onAddSection('language');
                            }}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                            </svg>
                            Languages
                        </a>
                    </div>
                )}
            </div>

            {/* 3. "..." More Options Dropdown */}
            <div className="g-dropdown-anchor">
                <button 
                    type="button"
                    className="g-btn-dots" 
                    id="gMoreOptionsBtn" 
                    onClick={() => toggleDropdown('more')}
                    aria-label="More options" 
                    aria-expanded={activeDropdown === 'more'}
                >
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 10a2 2 0 110 4 2 2 0 010-4zm6 0a2 2 0 110 4 2 2 0 010-4zm6 0a2 2 0 110 4 2 2 0 010-4z" />
                    </svg>
                </button>
                {activeDropdown === 'more' && (
                    <div className="g-popover-menu" id="gMoreOptionsDropdown" style={{ display: 'block', width: '210px' }}>
                        {canEdit && (
                            <a 
                                href="#wizard" 
                                className="g-menu-link" 
                                onClick={(e) => {
                                    e.preventDefault();
                                    setActiveDropdown(null);
                                    if (typeof window !== 'undefined') {
                                        window.dispatchEvent(new CustomEvent('grid:open-profile-wizard'));
                                    }
                                }}
                            >
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Profile Setup Wizard 🚀
                            </a>
                        )}
                        {canEdit && (
                            <a 
                                href="#edit-views" 
                                className="g-menu-link" 
                                id="gEditViewsBtn"
                                onClick={(e) => {
                                    e.preventDefault();
                                    setActiveDropdown(null);
                                    if (onEditViews) onEditViews();
                                }}
                            >
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Edit Views
                            </a>
                        )}
                        <a 
                            href="#share" 
                            className="g-menu-link" 
                            id="gShareProfileBtn"
                            onClick={handleShareProfile}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                            Share profile link
                        </a>
                        <a 
                            href="#print" 
                            className="g-menu-link"
                            onClick={(e) => {
                                e.preventDefault();
                                setActiveDropdown(null);
                                window.print();
                            }}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Save / Print to PDF
                        </a>
                        <hr className="g-divider" style={{ margin: '6px 0' }} />
                        <a 
                            href="#contact" 
                            className="g-menu-link" 
                            onClick={(e) => {
                                e.preventDefault();
                                setActiveDropdown(null);
                                if (onContactInfo) onContactInfo();
                            }}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Contact Info
                        </a>
                    </div>
                )}
            </div>
        </div>
    );
}
