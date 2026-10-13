import React from 'react';

/**
 * ProjectsList Component
 * 
 * Key projects showcase with status badges and tech stack tags.
 */
export default function ProjectsList({
    projects = [],
    canEdit = true,
    onAddProject,
    onEditProject,
    onManageVisibility,
    visibilityInfo
}) {
    return (
        <section className="g-card" id="gProjectsSection">
            <div className="g-card-body">
                <div className="g-section-header" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <h3 className="g-section-title" style={{ margin: 0 }}>Key Projects</h3>
                    </div>

                    {canEdit && onAddProject && (
                        <button 
                            type="button"
                            className="g-edit-btn" 
                            onClick={onAddProject}
                            style={{ color: 'var(--brand-600, #4f46e5)', fontSize: '12px', fontWeight: 600, padding: '4px 8px' }}
                        >
                            + Add project
                        </button>
                    )}
                </div>

                <div className="g-proj-list" id="gProjectsListContainer">
                    {projects.map((proj) => (
                        <div key={proj.id} className="g-proj-item" id={proj.id} style={{ position: 'relative' }}>
                            <div className="g-proj-top" style={{ paddingRight: canEdit ? '34px' : '0' }}>
                                <div>
                                    <div className="g-proj-name">{proj.name}</div>
                                    <div className="g-proj-date">{proj.dates || proj.date}</div>
                                </div>
                                {proj.badge && (
                                    <span className={`g-proj-badge ${proj.badgeClass || 'g-proj-badge-indigo'}`}>
                                        {proj.badge}
                                    </span>
                                )}
                            </div>

                            <div className="g-proj-desc">{proj.desc}</div>

                            {proj.tags && proj.tags.length > 0 && (
                                <div className="g-proj-tags">
                                    {proj.tags.map((tag, idx) => (
                                        <span key={idx} className="g-proj-tag">
                                            {tag}
                                        </span>
                                    ))}
                                </div>
                            )}

                            {canEdit && onEditProject && (
                                <button
                                    type="button"
                                    onClick={() => onEditProject(proj)}
                                    className="g-item-edit-btn"
                                    title="Edit project"
                                    style={{
                                        position: 'absolute',
                                        top: '12px',
                                        right: '12px',
                                        background: 'transparent',
                                        border: 'none',
                                        color: 'var(--text-muted, #94a3b8)',
                                        cursor: 'pointer',
                                        fontSize: '1rem',
                                        padding: '4px 6px',
                                        borderRadius: '6px',
                                        transition: 'all 0.15s ease'
                                    }}
                                    onMouseEnter={(e) => { e.currentTarget.style.color = '#6366f1'; e.currentTarget.style.background = 'rgba(99, 102, 241, 0.1)'; }}
                                    onMouseLeave={(e) => { e.currentTarget.style.color = 'var(--text-muted, #94a3b8)'; e.currentTarget.style.background = 'transparent'; }}
                                >
                                    ✎
                                </button>
                            )}
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
