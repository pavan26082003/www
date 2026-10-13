import React, { useState, useEffect } from 'react';
import ModalContainer from './ModalContainer.jsx';

const SECTION_METADATA = [
    { key: 'about', label: 'About & Bio', icon: '📝', desc: 'Executive summary, expertise highlights, and philosophy' },
    { key: 'artifacts', label: 'Featured Artifacts', icon: '⭐', desc: 'Code repositories, research papers, demos, and models' },
    { key: 'experience', label: 'Work Experience', icon: '💼', desc: 'Career timeline, roles, achievements, and impact metrics' },
    { key: 'education', label: 'Education', icon: '🎓', desc: 'University degrees, academic milestones, and coursework' },
    { key: 'certifications', label: 'Licenses & Certifications', icon: '📜', desc: 'Industry certifications, credential IDs, and verification links' },
    { key: 'projects', label: 'Key Projects', icon: '🚀', desc: 'Side projects, open-source repos, and technical showcases' },
    { key: 'skills', label: 'Skills & Endorsements', icon: '⚡', desc: 'Technical stack, proficiency areas, and peer endorsements' },
    { key: 'feed', label: 'Activity & Feed', icon: '📰', desc: 'Recent posts, shared insights, and published updates' },
    { key: 'recommendations', label: 'Recommendations', icon: '💬', desc: 'Endorsements and testimonials from colleagues & leaders' }
];

const AUDIENCES = [
    { key: 'connections', label: 'Connections', icon: '🤝', desc: 'People in your 1st-degree professional network' },
    { key: 'recruiters', label: 'Recruiters', icon: '💼', desc: 'Verified talent partners, hiring managers, and headhunters' },
    { key: 'nonConnections', label: 'Non-Connections', icon: '🌐', desc: 'General public & visitors browsing without connecting' }
];

/**
 * SectionVisibilityModal Component
 * 
 * Granular section-by-section audience privacy controls:
 * 1. Connections: View or Hide
 * 2. Recruiters: View or Hide
 * 3. Non-connections: View or Hide
 */
export default function SectionVisibilityModal({
    isOpen,
    onClose,
    sectionVisibility = {},
    initialSection = null,
    onSaveVisibility
}) {
    const [rules, setRules] = useState({});
    const [activeSectionKey, setActiveSectionKey] = useState(initialSection || 'about');

    useEffect(() => {
        if (isOpen) {
            // Clone or init defaults
            const initialRules = {};
            SECTION_METADATA.forEach(sec => {
                const existing = sectionVisibility[sec.key];
                initialRules[sec.key] = {
                    connections: existing ? existing.connections !== false : true,
                    recruiters: existing ? existing.recruiters !== false : true,
                    nonConnections: existing ? existing.nonConnections !== false : true
                };
            });
            setRules(initialRules);
            if (initialSection) {
                setActiveSectionKey(initialSection);
            }
        }
    }, [isOpen, sectionVisibility, initialSection]);

    function handleToggle(secKey, audienceKey, value) {
        setRules(prev => ({
            ...prev,
            [secKey]: {
                ...(prev[secKey] || { connections: true, recruiters: true, nonConnections: true }),
                [audienceKey]: value
            }
        }));
    }

    function applyPresetToSection(secKey, preset) {
        let updatedAudience = { connections: true, recruiters: true, nonConnections: true };
        if (preset === 'public') {
            updatedAudience = { connections: true, recruiters: true, nonConnections: true };
        } else if (preset === 'network') {
            updatedAudience = { connections: true, recruiters: true, nonConnections: false };
        } else if (preset === 'connectionsOnly') {
            updatedAudience = { connections: true, recruiters: false, nonConnections: false };
        } else if (preset === 'hidden') {
            updatedAudience = { connections: false, recruiters: false, nonConnections: false };
        }

        setRules(prev => ({
            ...prev,
            [secKey]: updatedAudience
        }));
    }

    function handleSave() {
        if (onSaveVisibility) {
            onSaveVisibility(rules);
        }
        onClose();
    }

    const currentSectionRule = rules[activeSectionKey] || { connections: true, recruiters: true, nonConnections: true };
    const currentMeta = SECTION_METADATA.find(s => s.key === activeSectionKey) || SECTION_METADATA[0];

    return (
        <ModalContainer
            isOpen={isOpen}
            onClose={onClose}
            title="Profile Section Visibility & Privacy"
            maxWidth="680px"
        >
            <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                <p style={{ margin: 0, fontSize: '13px', color: 'var(--slate-600, #475569)', lineHeight: 1.5 }}>
                    Specify exactly who can view each section of your profile. Choose whether 
                    <strong> Connections</strong>, <strong>Recruiters</strong>, and <strong>Non-connections</strong> see or hide each part.
                </p>

                {/* Section Selector Pills */}
                <div style={{
                    display: 'flex',
                    gap: '6px',
                    overflowX: 'auto',
                    paddingBottom: '4px',
                    borderBottom: '1px solid var(--slate-200, #e2e8f0)'
                }}>
                    {SECTION_METADATA.map(sec => {
                        const isSelected = sec.key === activeSectionKey;
                        const secRule = rules[sec.key];
                        const isFullyHidden = secRule && !secRule.connections && !secRule.recruiters && !secRule.nonConnections;
                        const isPartial = secRule && (!secRule.nonConnections || !secRule.recruiters);

                        return (
                            <button
                                key={sec.key}
                                type="button"
                                onClick={() => setActiveSectionKey(sec.key)}
                                style={{
                                    padding: '6px 12px',
                                    borderRadius: '8px',
                                    border: isSelected ? '1px solid #6366f1' : '1px solid var(--slate-200, #e2e8f0)',
                                    background: isSelected ? 'rgba(99, 102, 241, 0.1)' : '#fff',
                                    color: isSelected ? '#4f46e5' : 'var(--slate-700, #334155)',
                                    fontSize: '12px',
                                    fontWeight: isSelected ? 600 : 500,
                                    cursor: 'pointer',
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: '6px',
                                    whiteSpace: 'nowrap',
                                    flexShrink: 0,
                                    transition: 'all 0.15s ease'
                                }}
                            >
                                <span>{sec.icon}</span>
                                <span>{sec.label}</span>
                                {isFullyHidden && (
                                    <span style={{ fontSize: '10px', background: '#fee2e2', color: '#dc2626', padding: '1px 5px', borderRadius: '4px' }}>Hidden</span>
                                )}
                                {!isFullyHidden && isPartial && (
                                    <span style={{ fontSize: '10px', background: '#fef3c7', color: '#d97706', padding: '1px 5px', borderRadius: '4px' }}>Custom</span>
                                )}
                            </button>
                        );
                    })}
                </div>

                {/* Active Section Settings Card */}
                <div style={{
                    background: '#f8fafc',
                    borderRadius: '12px',
                    border: '1px solid var(--slate-200, #e2e8f0)',
                    padding: '16px'
                }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '14px' }}>
                        <div>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                                <span style={{ fontSize: '1.25rem' }}>{currentMeta.icon}</span>
                                <h4 style={{ margin: 0, fontSize: '15px', fontWeight: 700, color: 'var(--slate-900, #0f172a)' }}>
                                    {currentMeta.label}
                                </h4>
                            </div>
                            <p style={{ margin: '4px 0 0', fontSize: '12px', color: 'var(--slate-500, #64748b)' }}>
                                {currentMeta.desc}
                            </p>
                        </div>

                        {/* Quick Presets for current section */}
                        <div style={{ display: 'flex', gap: '6px' }}>
                            <button
                                type="button"
                                onClick={() => applyPresetToSection(activeSectionKey, 'public')}
                                style={{
                                    fontSize: '11px',
                                    padding: '3px 8px',
                                    borderRadius: '6px',
                                    border: '1px solid #cbd5e1',
                                    background: '#fff',
                                    color: '#475569',
                                    cursor: 'pointer'
                                }}
                                title="Visible to everyone"
                            >
                                Public
                            </button>
                            <button
                                type="button"
                                onClick={() => applyPresetToSection(activeSectionKey, 'network')}
                                style={{
                                    fontSize: '11px',
                                    padding: '3px 8px',
                                    borderRadius: '6px',
                                    border: '1px solid #cbd5e1',
                                    background: '#fff',
                                    color: '#475569',
                                    cursor: 'pointer'
                                }}
                                title="Connections & Recruiters only"
                            >
                                Network Only
                            </button>
                            <button
                                type="button"
                                onClick={() => applyPresetToSection(activeSectionKey, 'hidden')}
                                style={{
                                    fontSize: '11px',
                                    padding: '3px 8px',
                                    borderRadius: '6px',
                                    border: '1px solid #fecaca',
                                    background: '#fef2f2',
                                    color: '#dc2626',
                                    cursor: 'pointer'
                                }}
                                title="Hide from all audiences"
                            >
                                Hide All
                            </button>
                        </div>
                    </div>

                    {/* 3 Audience Rows */}
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
                        {AUDIENCES.map(aud => {
                            const isVisible = currentSectionRule[aud.key] !== false;

                            return (
                                <div
                                    key={aud.key}
                                    style={{
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'space-between',
                                        background: '#fff',
                                        padding: '12px 14px',
                                        borderRadius: '10px',
                                        border: '1px solid var(--slate-200, #e2e8f0)',
                                        gap: '12px'
                                    }}
                                >
                                    <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                                        <span style={{ fontSize: '1.2rem' }}>{aud.icon}</span>
                                        <div>
                                            <div style={{ fontSize: '13px', fontWeight: 600, color: 'var(--slate-800, #1e293b)' }}>
                                                {aud.label}
                                            </div>
                                            <div style={{ fontSize: '11px', color: 'var(--slate-500, #64748b)' }}>
                                                {aud.desc}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Segmented Toggle Control: View / Hide */}
                                    <div style={{
                                        display: 'inline-flex',
                                        background: '#f1f5f9',
                                        padding: '3px',
                                        borderRadius: '8px',
                                        border: '1px solid #e2e8f0'
                                    }}>
                                        <button
                                            type="button"
                                            onClick={() => handleToggle(activeSectionKey, aud.key, true)}
                                            style={{
                                                padding: '4px 14px',
                                                borderRadius: '6px',
                                                border: 'none',
                                                fontSize: '12px',
                                                fontWeight: 600,
                                                cursor: 'pointer',
                                                background: isVisible ? '#10b981' : 'transparent',
                                                color: isVisible ? '#fff' : '#64748b',
                                                boxShadow: isVisible ? '0 1px 2px rgba(0,0,0,0.1)' : 'none',
                                                transition: 'all 0.15s ease'
                                            }}
                                        >
                                            👁️ View
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => handleToggle(activeSectionKey, aud.key, false)}
                                            style={{
                                                padding: '4px 14px',
                                                borderRadius: '6px',
                                                border: 'none',
                                                fontSize: '12px',
                                                fontWeight: 600,
                                                cursor: 'pointer',
                                                background: !isVisible ? '#ef4444' : 'transparent',
                                                color: !isVisible ? '#fff' : '#64748b',
                                                boxShadow: !isVisible ? '0 1px 2px rgba(0,0,0,0.1)' : 'none',
                                                transition: 'all 0.15s ease'
                                            }}
                                        >
                                            🚫 Hide
                                        </button>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>

                {/* Footer Controls */}
                <div style={{
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'center',
                    marginTop: '8px',
                    paddingTop: '14px',
                    borderTop: '1px solid var(--slate-200, #e2e8f0)'
                }}>
                    <button
                        type="button"
                        onClick={() => {
                            // Apply public to all sections
                            const publicAll = {};
                            SECTION_METADATA.forEach(sec => {
                                publicAll[sec.key] = { connections: true, recruiters: true, nonConnections: true };
                            });
                            setRules(publicAll);
                        }}
                        style={{
                            fontSize: '12px',
                            color: '#6366f1',
                            background: 'none',
                            border: 'none',
                            cursor: 'pointer',
                            textDecoration: 'underline'
                        }}
                    >
                        Reset all to Public
                    </button>

                    <div style={{ display: 'flex', gap: '10px' }}>
                        <button
                            type="button"
                            onClick={onClose}
                            style={{
                                padding: '8px 18px',
                                borderRadius: '9999px',
                                border: '1px solid var(--slate-300, #cbd5e1)',
                                background: '#fff',
                                fontSize: '13px',
                                fontWeight: 600,
                                cursor: 'pointer'
                            }}
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            onClick={handleSave}
                            className="g-btn-create"
                            style={{ padding: '8px 24px' }}
                        >
                            Save Visibility Settings
                        </button>
                    </div>
                </div>
            </div>
        </ModalContainer>
    );
}
