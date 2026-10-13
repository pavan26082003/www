import React, { useState } from 'react';

export default function RightSidebar({ analytics, user }) {
    const [copied, setCopied] = useState(false);
    const [connectedList, setConnectedList] = useState({});

    const profileUrl = user.bannerUrl ? (user.bannerUrl.startsWith('http') ? user.bannerUrl : `https://${user.bannerUrl}`) : (user.gridId ? `https://grid.community/in/${user.gridId}` : (typeof window !== 'undefined' ? window.location.href : 'https://grid.community'));

    function handleCopy() {
        navigator.clipboard.writeText(profileUrl);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    }

    function toggleConnect(id) {
        setConnectedList(prev => ({ ...prev, [id]: !prev[id] }));
    }

    return (
        <aside className="g-right-sidebar">
            {/* Public Profile & URL widget */}
            <div className="g-widget">
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '12px' }}>
                    <div className="g-widget-label" style={{ margin: 0 }}>Public Profile &amp; URL</div>
                    <button className="g-edit-btn" title="Edit vanity link" style={{ padding: '2px' }}>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style={{ width: '14px', height: '14px' }}>
                            <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
                        </svg>
                    </button>
                </div>
                <div className="g-url-box">
                    <span style={{ overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                        {profileUrl.replace(/^https?:\/\//, '')}
                    </span>
                    <button className="g-url-copy" onClick={handleCopy}>
                        {copied ? 'Copied!' : 'Copy'}
                    </button>
                </div>
            </div>

            {/* Analytics Widget */}
            <div className="g-widget">
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '12px' }}>
                    <h4 className="g-widget-title" style={{ margin: 0 }}>Analytics</h4>
                    <span style={{ fontSize: '10px', color: 'var(--slate-400)' }}>Private to you</span>
                </div>
                <div className="g-analytics-grid">
                    <div className="g-analytics-item">
                        <div className="g-analytics-num">{analytics.profileViews}</div>
                        <div className="g-analytics-label">Profile views</div>
                        <div className="g-analytics-change g-analytics-up">↑ 18%</div>
                    </div>
                    <div className="g-analytics-item">
                        <div className="g-analytics-num">1.8k</div>
                        <div className="g-analytics-label">Post views</div>
                        <div className="g-analytics-change g-analytics-up">↑ 32%</div>
                    </div>
                    <div className="g-analytics-item">
                        <div className="g-analytics-num">{analytics.searchAppearances}</div>
                        <div className="g-analytics-label">Search appearances</div>
                        <div className="g-analytics-change g-analytics-flat">stable</div>
                    </div>
                </div>
            </div>

            {/* Verified Integrations */}
            <div className="g-widget">
                <div className="g-widget-label">Verified Integrations</div>
                <div className="g-integrations-grid">
                    <div className="g-integration"><div className="g-integration-icon">GH</div><div className="g-integration-label">GitHub</div></div>
                    <div className="g-integration"><div className="g-integration-icon">🤗</div><div className="g-integration-label">HuggingFace</div></div>
                    <div className="g-integration"><div className="g-integration-icon">⚡</div><div className="g-integration-label">Replit</div></div>
                    <div className="g-integration"><div className="g-integration-icon">N</div><div className="g-integration-label">Notion</div></div>
                    <div className="g-integration"><div className="g-integration-icon">HS</div><div className="g-integration-label">HubSpot</div></div>
                    <div className="g-integration"><div className="g-integration-icon">V</div><div className="g-integration-label">Gamma</div></div>
                </div>
            </div>

            {/* Suggested Connections */}
            <div className="g-widget">
                <div className="g-widget-label">People You May Know</div>
                <div className="g-people-list">
                    <div className="g-person">
                        <div className="g-person-info">
                            <div className="g-person-avatar">AK</div>
                            <div>
                                <div className="g-person-name">Dr. Ananya K.</div>
                                <div className="g-person-role">Research Scientist @ T-Hub</div>
                                <div className="g-person-mutual">14 mutual connections</div>
                            </div>
                        </div>
                        <button 
                            className="g-btn-connect" 
                            style={{ background: connectedList['p1'] ? 'var(--slate-200)' : 'var(--brand-600)', color: connectedList['p1'] ? 'var(--slate-700)' : '#fff' }}
                            onClick={() => toggleConnect('p1')}
                        >
                            {connectedList['p1'] ? 'Pending' : 'Connect'}
                        </button>
                    </div>

                    <div className="g-person">
                        <div className="g-person-info">
                            <div className="g-person-avatar">SK</div>
                            <div>
                                <div className="g-person-name">Sai Kiran</div>
                                <div className="g-person-role">ML Infra Engineer @ Cohesity</div>
                                <div className="g-person-mutual">8 mutual connections</div>
                            </div>
                        </div>
                        <button 
                            className="g-btn-connect" 
                            style={{ background: connectedList['p2'] ? 'var(--slate-200)' : 'var(--brand-600)', color: connectedList['p2'] ? 'var(--slate-700)' : '#fff' }}
                            onClick={() => toggleConnect('p2')}
                        >
                            {connectedList['p2'] ? 'Pending' : 'Connect'}
                        </button>
                    </div>
                </div>
            </div>
        </aside>
    );
}
