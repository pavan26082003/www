import React from 'react';

/**
 * AboutCard Component
 * 
 * Bio narrative with paragraph bullets and interactive core technical arsenal chips.
 */
export default function AboutCard({
    about = {},
    onEditAbout,
    canEdit = true,
    visibilityInfo,
    onManageVisibility
}) {
    // Resolve paragraphs cleanly: if custom text or paragraphs array provided, use it
    const paragraphs = (() => {
        if (about.paragraphs && about.paragraphs.length > 0) return about.paragraphs;
        if (about.text && about.text.trim()) return about.text.split('\n\n').filter(Boolean);
        return [];
    })();

    const bullets = about.bullets || [];
    const closing = about.closing || '';
    const skills = about.skills || [];
    const hasContent = paragraphs.length > 0 || bullets.length > 0 || closing.trim() || skills.length > 0;

    const badgeText = typeof visibilityInfo === 'object' && visibilityInfo !== null
        ? (visibilityInfo.label || 'Public')
        : (visibilityInfo || 'Public');

    return (
        <section className="g-card" id="gAboutSection">
            <div className="g-card-body">
                <div className="g-section-header" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <h3 className="g-section-title" style={{ margin: 0 }}>About</h3>
                    </div>
                    {canEdit && onEditAbout && hasContent && (
                        <button 
                            type="button"
                            className="g-edit-btn" 
                            onClick={onEditAbout}
                            aria-label="Edit About" 
                            title="Edit About summary"
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path 
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" 
                                    strokeLinecap="round" 
                                    strokeLinejoin="round" 
                                    strokeWidth="2" 
                                />
                            </svg>
                        </button>
                    )}
                </div>

                {/* Bio Narrative or Empty State */}
                {hasContent ? (
                    <div className="g-about-text" id="gAboutNarrative">
                        {paragraphs.map((p, idx) => (
                            <p key={idx} dangerouslySetInnerHTML={{ __html: p }} />
                        ))}

                        {bullets && bullets.length > 0 && (
                            <ul>
                                {bullets.map((b, idx) => (
                                    <li key={idx} dangerouslySetInnerHTML={{ __html: b }} />
                                ))}
                            </ul>
                        )}

                        {closing && (
                            <p dangerouslySetInnerHTML={{ __html: closing }} />
                        )}
                    </div>
                ) : (
                    <div style={{
                        padding: '28px 20px',
                        textAlign: 'center',
                        borderRadius: '16px',
                        border: '1.5px dashed var(--slate-300, #cbd5e1)',
                        backgroundColor: 'var(--slate-50, #f8fafc)',
                        marginTop: '8px'
                    }}>
                        <div style={{ fontSize: '28px', marginBottom: '8px' }}>✍️</div>
                        <h4 style={{ fontSize: '14px', fontWeight: 700, color: 'var(--slate-800, #1e293b)', margin: '0 0 4px' }}>
                            No About summary added yet
                        </h4>
                        <p style={{ fontSize: '12px', color: 'var(--slate-500, #64748b)', margin: '0 auto 16px', maxWidth: '380px', lineHeight: 1.5 }}>
                            Introduce yourself to peers and recruiters by sharing your technical journey, passions, and expertise.
                        </p>
                        {canEdit && onEditAbout && (
                            <button
                                type="button"
                                onClick={onEditAbout}
                                style={{
                                    display: 'inline-flex',
                                    alignItems: 'center',
                                    gap: '6px',
                                    padding: '8px 18px',
                                    backgroundColor: '#2563eb',
                                    color: '#ffffff',
                                    fontSize: '12px',
                                    fontWeight: 700,
                                    borderRadius: '12px',
                                    border: 'none',
                                    cursor: 'pointer',
                                    boxShadow: '0 2px 4px rgba(37, 99, 235, 0.2)'
                                }}
                            >
                                <span>✎</span> Add About Summary
                            </button>
                        )}
                    </div>
                )}

                {/* Core Technical Arsenal Chips (only shown if provided) */}
                {skills && skills.length > 0 && (
                    <div className="g-skills-row">
                        <div className="g-skills-label">Core Technical Arsenal</div>
                        <div className="g-chips" id="gArsenalChipsList">
                            {skills.map((chip, idx) => (
                                <span key={idx} className="g-chip">
                                    {chip}
                                </span>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </section>
    );
}
