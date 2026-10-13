import React from 'react';
import VerifiedBadge from '../ui/VerifiedBadge.jsx';

export default function LeftSidebar({ user }) {
    return (
        <aside className="g-sidebar">
            <div className="g-sidebar-card">
                <nav className="g-nav">
                    <a href="/index.php/grid" className="g-nav-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        <span>Home</span>
                    </a>
                    <a href="/index.php/grid/profile" className="g-nav-link active">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        <span>Profile</span>
                        <span style={{ marginLeft: 'auto', display: 'flex', alignItems: 'center' }}>
                            <VerifiedBadge size="xs" title="GRID Verified Pro" />
                        </span>
                    </a>
                    <a href="#network" className="g-nav-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span>Network</span>
                        <span className="g-nav-badge g-nav-badge-slate">{user.connectionsCount}</span>
                    </a>
                    <a href="#activity" className="g-nav-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        <span>Activity</span>
                    </a>
                    <a href="#projects" className="g-nav-link">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        <span>Projects</span>
                    </a>
                </nav>
            </div>

            <div className="g-sidebar-card">
                <div className="g-sidebar-title">
                    <span>Spaces</span>
                    <button title="Add space">+</button>
                </div>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '2px' }}>
                    <a href="#announcements" className="g-space-link"><span className="g-space-icon">📢</span> Announcements</a>
                    <a href="#designtalk" className="g-space-link"><span className="g-space-icon">🎨</span> Design talk</a>
                    <a href="#productideas" className="g-space-link"><span className="g-space-icon">💡</span> Product ideas</a>
                    <a href="#airesearch" className="g-space-link"><span className="g-space-icon">⚡</span> AI &amp; ML Research</a>
                </div>
            </div>

            <div className="g-sidebar-card">
                <div className="g-sidebar-title">
                    <span>Recent Topics</span>
                </div>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '4px' }}>
                    <div className="g-topic"><span className="hash">#</span> llm-alignment-dpo</div>
                    <div className="g-topic"><span className="hash">#</span> hyderabad-tech-hubs</div>
                    <div className="g-topic"><span className="hash">#</span> campus-placement-ai</div>
                    <div className="g-topic"><span className="hash">#</span> pytorch-distributed</div>
                </div>
            </div>
        </aside>
    );
}
