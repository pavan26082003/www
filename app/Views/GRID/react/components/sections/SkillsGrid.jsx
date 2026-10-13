import React, { useState } from 'react';

/**
 * SkillsGrid Component
 * 
 * 2-column responsive skill endorsement grid with live interactive
 * +1 endorsement actions and celebratory toast feedback.
 */
export default function SkillsGrid({
    skills = [],
    canEdit = true,
    onAddSkill,
    onEditSkill,
    onManageVisibility,
    visibilityInfo,
    onToast
}) {
    const [endorsedSkills, setEndorsedSkills] = useState({});
    const [endorsementCounts, setEndorsementCounts] = useState({});

    function handleEndorse(skill) {
        const skillId = skill.id;
        const isCurrentlyEndorsed = !!endorsedSkills[skillId];
        const baseCount = skill.endorsements || 0;
        const currentCount = endorsementCounts[skillId] !== undefined ? endorsementCounts[skillId] : baseCount;

        setEndorsedSkills(prev => ({
            ...prev,
            [skillId]: !isCurrentlyEndorsed
        }));

        setEndorsementCounts(prev => ({
            ...prev,
            [skillId]: isCurrentlyEndorsed ? Math.max(0, currentCount - 1) : currentCount + 1
        }));

        if (!isCurrentlyEndorsed && onToast) {
            onToast(`You endorsed ${skill.name}! 🌟`);
        }
    }

    return (
        <section className="g-card" id="gSkillsSection">
            <div className="g-card-body">
                <div className="g-section-header" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <h3 className="g-section-title" style={{ margin: 0 }}>Skills &amp; Endorsements</h3>
                    </div>

                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <button 
                            type="button"
                            className="g-btn-outline-brand" 
                            style={{ borderRadius: '9999px', padding: '4px 12px', fontSize: '12px' }}
                            onClick={() => onToast && onToast('Skill Assessment quiz coming soon!')}
                        >
                            Take skill quiz
                        </button>
                        {canEdit && onAddSkill && (
                            <button 
                                type="button"
                                className="g-add-btn" 
                                onClick={onAddSkill}
                                aria-label="Add skill" 
                                title="Add skill"
                            >
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 4v16m8-8H4" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
                                </svg>
                            </button>
                        )}
                    </div>
                </div>

                <div className="g-skills-grid" id="gSkillsGridContainer">
                    {skills.map((skill) => {
                        const isEndorsed = !!endorsedSkills[skill.id];
                        const count = endorsementCounts[skill.id] !== undefined 
                            ? endorsementCounts[skill.id] 
                            : (skill.endorsements || 0);

                        return (
                            <div key={skill.id} className="g-skill-item" id={skill.id} style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: '12px', position: 'relative' }}>
                                <div className="g-skill-info" style={{ flex: 1, minWidth: 0 }}>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                                        <div className="g-skill-name" style={{ wordBreak: 'break-word', lineHeight: 1.35 }}>{skill.name}</div>
                                        {canEdit && onEditSkill && (
                                            <button
                                                type="button"
                                                onClick={() => onEditSkill(skill)}
                                                className="g-item-edit-btn"
                                                title="Edit skill"
                                                style={{
                                                    background: 'transparent',
                                                    border: 'none',
                                                    color: 'var(--text-muted, #94a3b8)',
                                                    cursor: 'pointer',
                                                    fontSize: '0.85rem',
                                                    padding: '2px 4px',
                                                    borderRadius: '4px',
                                                    lineHeight: 1
                                                }}
                                                onMouseEnter={(e) => { e.currentTarget.style.color = '#6366f1'; }}
                                                onMouseLeave={(e) => { e.currentTarget.style.color = 'var(--text-muted, #94a3b8)'; }}
                                            >
                                                ✎
                                            </button>
                                        )}
                                    </div>
                                    <div className="g-skill-endorsement" style={{ marginTop: '2px' }}>
                                        <span className="g-endorse-count">{count}</span> endorsements
                                        {skill.note && ` · ${skill.note}`}
                                    </div>
                                </div>
                                <button 
                                    type="button"
                                    className={`g-skill-plus ${isEndorsed ? 'g-endorsed' : ''}`} 
                                    onClick={() => handleEndorse(skill)}
                                    title={isEndorsed ? 'Endorsed' : 'Endorse skill'} 
                                    aria-label={`Endorse ${skill.name}`}
                                    style={{
                                        width: '32px',
                                        height: '32px',
                                        minWidth: '32px',
                                        minHeight: '32px',
                                        maxWidth: '32px',
                                        maxHeight: '32px',
                                        flexShrink: 0,
                                        aspectRatio: '1 / 1',
                                        borderRadius: '50%',
                                        padding: 0,
                                        display: 'inline-flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        boxSizing: 'border-box'
                                    }}
                                >
                                    {isEndorsed ? (
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style={{ width: '16px', height: '16px' }}>
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    ) : (
                                        '+1'
                                    )}
                                </button>
                            </div>
                        );
                    })}
                </div>
            </div>
        </section>
    );
}
