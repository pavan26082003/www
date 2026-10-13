import React from 'react';

/**
 * ExperienceList Component
 * 
 * Career history timeline with company icons, dates, bullet achievements,
 * current role badges, per-item edit buttons, and visibility controls.
 */
export default function ExperienceList({
    experiences = [],
    onAddExperience,
    onEditExperience,
    canEdit = true,
    visibilityInfo,
    onManageVisibility
}) {
    return (
        <section className="g-card" id="gExperienceSection">
            <div className="g-card-body">
                <div className="g-section-header">
                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <h3 className="g-section-title">Experience</h3>
                    </div>
                    {canEdit && onAddExperience && (
                        <button 
                            type="button"
                            className="g-add-btn" 
                            onClick={onAddExperience}
                            aria-label="Add experience" 
                            title="Add new experience"
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4v16m8-8H4" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
                            </svg>
                        </button>
                    )}
                </div>

                <div className="g-exp-list" id="gExpListContainer">
                    {experiences.map((exp, idx) => (
                        <React.Fragment key={exp.id}>
                            {idx > 0 && <hr className="g-divider" />}
                            <div className="g-exp-item group" id={exp.id} style={{ position: 'relative' }}>
                                <div className={`g-exp-icon ${exp.iconClass || 'g-exp-icon-brand'}`}>
                                    {exp.icon || 'EXP'}
                                </div>
                                <div style={{ flex: 1, minWidth: 0 }}>
                                    <div className="g-exp-header">
                                        <div>
                                            <div className="g-exp-title">{exp.title}</div>
                                            <div className="g-exp-company">{exp.company}</div>
                                            <div className="g-exp-dates">{exp.dates}</div>
                                        </div>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                                            {exp.isCurrent && (
                                                <span className="g-exp-current whitespace-nowrap shrink-0">Current Role</span>
                                            )}
                                            {canEdit && onEditExperience && (
                                                <button
                                                    type="button"
                                                    onClick={() => onEditExperience(exp)}
                                                    className="g-item-edit-btn"
                                                    title={`Edit ${exp.title}`}
                                                    aria-label={`Edit ${exp.title}`}
                                                    style={{
                                                        display: 'inline-flex',
                                                        alignItems: 'center',
                                                        justifyContent: 'center',
                                                        width: '28px',
                                                        height: '28px',
                                                        borderRadius: '6px',
                                                        border: '1px solid var(--slate-200)',
                                                        background: '#fff',
                                                        color: 'var(--slate-600)',
                                                        cursor: 'pointer',
                                                        transition: 'all 0.15s ease'
                                                    }}
                                                >
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style={{ width: '14px', height: '14px' }}>
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                            )}
                                        </div>
                                    </div>

                                    {exp.bullets && exp.bullets.length > 0 && (
                                        <div className="g-exp-bullets">
                                            {exp.bullets.map((bullet, bIdx) => (
                                                <p key={bIdx}>• {bullet}</p>
                                            ))}
                                        </div>
                                    )}

                                    {exp.tags && exp.tags.length > 0 && (
                                        <div className="g-exp-tags">
                                            {exp.tags.map((tag, tIdx) => (
                                                <span key={tIdx} className="g-exp-tag">
                                                    {tag}
                                                </span>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </React.Fragment>
                    ))}
                    {experiences.length === 0 && (
                        <div style={{ padding: '24px', textAlign: 'center', color: 'var(--slate-400)', fontSize: '13px' }}>
                            No work experience added yet.
                        </div>
                    )}
                </div>
            </div>
        </section>
    );
}
