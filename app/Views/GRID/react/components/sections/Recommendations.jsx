import React, { useState } from 'react';

/**
 * Recommendations Component
 * 
 * Composes:
 * - Recommendations with Received / Given tabs
 * - Honors & Awards with Add/Edit capability
 * - Languages multi-column proficiency grid with Add/Edit capability
 */
export default function Recommendations({
    canEdit = true,
    recommendations = [],
    honors = [],
    languages = [],
    onRequestRecommendation,
    onAddHonor,
    onEditHonor,
    onAddLanguage,
    onEditLanguage,
    onManageVisibility,
    visibilityInfo
}) {
    const [recTab, setRecTab] = useState('received');

    const receivedRecs = (recommendations || []).filter(r => r.type === 'received' || !r.type);
    const givenRecs = (recommendations || []).filter(r => r.type === 'given');

    return (
        <>
            {/* ═══ RECOMMENDATIONS SECTION ═══ */}
            <section className="g-card" id="gRecommendationsSection">
                <div className="g-card-body">
                    <div className="g-section-header" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                            <h3 className="g-section-title" style={{ margin: 0 }}>Recommendations</h3>
                        </div>
                        {canEdit && onRequestRecommendation && (
                            <button 
                                type="button"
                                onClick={onRequestRecommendation}
                                style={{ background: 'none', border: 0, fontSize: '12px', color: 'var(--brand-600, #2563eb)', fontWeight: 600, cursor: 'pointer' }}
                            >
                                Ask for recommendation
                            </button>
                        )}
                    </div>

                    <div className="g-rec-tabs" id="gRecTabs">
                        <button 
                            type="button"
                            className={`g-rec-tab ${recTab === 'received' ? 'active' : ''}`}
                            onClick={() => setRecTab('received')}
                        >
                            Received ({receivedRecs.length})
                        </button>
                        <button 
                            type="button"
                            className={`g-rec-tab ${recTab === 'given' ? 'active' : ''}`}
                            onClick={() => setRecTab('given')}
                        >
                            Given ({givenRecs.length})
                        </button>
                    </div>

                    {recTab === 'received' ? (
                        <div id="gRecContentReceived">
                            {receivedRecs.length > 0 ? (
                                receivedRecs.map((rec) => (
                                    <div key={rec.id} className="g-rec-item" id={rec.id}>
                                        <div className="g-rec-avatar">{rec.initials || 'U'}</div>
                                        <div>
                                            <div className="g-rec-name">{rec.name}</div>
                                            <div className="g-rec-role">{rec.role}</div>
                                            <blockquote className="g-rec-quote">"{rec.quote}"</blockquote>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <div style={{
                                    padding: '24px 16px',
                                    textAlign: 'center',
                                    color: 'var(--slate-500, #64748b)',
                                    fontSize: '13px',
                                    borderRadius: '12px',
                                    border: '1.5px dashed var(--slate-200, #e2e8f0)',
                                    marginTop: '12px'
                                }}>
                                    <div style={{ fontSize: '24px', marginBottom: '4px' }}>🤝</div>
                                    <p style={{ margin: 0, fontWeight: 600 }}>No recommendations received yet</p>
                                    <p style={{ margin: '4px 0 0', fontSize: '12px', color: 'var(--slate-400, #94a3b8)' }}>
                                        Reach out to teammates, project partners, or mentors to build social proof on your profile.
                                    </p>
                                </div>
                            )}
                        </div>
                    ) : (
                        <div id="gRecContentGiven">
                            {givenRecs.length > 0 ? (
                                givenRecs.map((rec) => (
                                    <div key={rec.id} className="g-rec-item" id={rec.id}>
                                        <div className="g-rec-avatar" style={{ background: '#475569' }}>{rec.initials || 'U'}</div>
                                        <div>
                                            <div className="g-rec-name">{rec.name}</div>
                                            <div className="g-rec-role">{rec.role}</div>
                                            <blockquote className="g-rec-quote">"{rec.quote}"</blockquote>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <div style={{
                                    padding: '24px 16px',
                                    textAlign: 'center',
                                    color: 'var(--slate-500, #64748b)',
                                    fontSize: '13px',
                                    borderRadius: '12px',
                                    border: '1.5px dashed var(--slate-200, #e2e8f0)',
                                    marginTop: '12px'
                                }}>
                                    <p style={{ margin: 0, fontWeight: 600 }}>You haven't given any recommendations yet</p>
                                    <p style={{ margin: '4px 0 0', fontSize: '12px', color: 'var(--slate-400, #94a3b8)' }}>
                                        Recommend peers you've worked with on open-source, client work, or team projects.
                                    </p>
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </section>

            {/* ═══ HONORS & LANGUAGES SECTION ═══ */}
            <section className="g-card" id="gHonorsSection">
                <div className="g-card-body" style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                    {/* Honors & Awards */}
                    <div>
                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '12px' }}>
                            <div className="g-honors-title" style={{ margin: 0 }}>Honors &amp; Awards</div>
                            {canEdit && onAddHonor && (
                                <button
                                    type="button"
                                    onClick={onAddHonor}
                                    style={{
                                        background: 'none',
                                        border: 'none',
                                        color: '#2563eb',
                                        fontSize: '12px',
                                        fontWeight: 700,
                                        cursor: 'pointer',
                                        display: 'flex',
                                        alignItems: 'center',
                                        gap: '4px'
                                    }}
                                >
                                    + Add Honor / Award
                                </button>
                            )}
                        </div>

                        {honors && honors.length > 0 ? (
                            <div>
                                {honors.map((honor, idx) => (
                                    <div key={honor.id || idx} className="g-honor" style={{ position: 'relative' }}>
                                        <span className="g-honor-icon">{honor.icon || '🏆'}</span>
                                        <div style={{ flex: 1 }}>
                                            <strong>{honor.title}</strong>
                                            <p>{honor.issuer}</p>
                                            {honor.description && (
                                                <p style={{ fontSize: '12px', color: 'var(--slate-500, #64748b)', marginTop: '2px' }}>
                                                    {honor.description}
                                                </p>
                                            )}
                                        </div>
                                        {canEdit && onEditHonor && (
                                            <button
                                                type="button"
                                                onClick={() => onEditHonor(honor)}
                                                className="g-edit-btn"
                                                style={{ marginLeft: '8px' }}
                                                title="Edit Honor"
                                            >
                                                ✎
                                            </button>
                                        )}
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div style={{
                                padding: '20px 16px',
                                textAlign: 'center',
                                borderRadius: '12px',
                                border: '1.5px dashed var(--slate-200, #e2e8f0)',
                                backgroundColor: 'var(--slate-50, #f8fafc)'
                            }}>
                                <div style={{ fontSize: '22px', marginBottom: '4px' }}>🏆</div>
                                <p style={{ fontSize: '13px', fontWeight: 600, color: 'var(--slate-700, #334155)', margin: 0 }}>
                                    No honors or awards added yet
                                </p>
                                <p style={{ fontSize: '11px', color: 'var(--slate-400, #94a3b8)', margin: '2px 0 10px' }}>
                                    Add your hackathon wins, scholarships, certifications, and academic recognitions.
                                </p>
                                {canEdit && onAddHonor && (
                                    <button
                                        type="button"
                                        onClick={onAddHonor}
                                        style={{
                                            padding: '6px 14px',
                                            backgroundColor: '#2563eb',
                                            color: '#fff',
                                            borderRadius: '8px',
                                            border: 'none',
                                            fontSize: '11px',
                                            fontWeight: 700,
                                            cursor: 'pointer'
                                        }}
                                    >
                                        + Add Honor
                                    </button>
                                )}
                            </div>
                        )}
                    </div>

                    <hr className="g-divider" />

                    {/* Languages */}
                    <div>
                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '12px' }}>
                            <div className="g-honors-title" style={{ margin: 0 }}>Languages</div>
                            {canEdit && onAddLanguage && (
                                <button
                                    type="button"
                                    onClick={onAddLanguage}
                                    style={{
                                        background: 'none',
                                        border: 'none',
                                        color: '#2563eb',
                                        fontSize: '12px',
                                        fontWeight: 700,
                                        cursor: 'pointer',
                                        display: 'flex',
                                        alignItems: 'center',
                                        gap: '4px'
                                    }}
                                >
                                    + Add Language
                                </button>
                            )}
                        </div>

                        {languages && languages.length > 0 ? (
                            <div className="g-langs-grid">
                                {languages.map((lang, idx) => (
                                    <div 
                                        key={lang.id || idx} 
                                        className="g-lang"
                                        onClick={() => canEdit && onEditLanguage && onEditLanguage(lang)}
                                        style={{ cursor: canEdit && onEditLanguage ? 'pointer' : 'default' }}
                                    >
                                        <div className="g-lang-name">{lang.name}</div>
                                        <div className="g-lang-level">{lang.level}</div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div style={{
                                padding: '16px',
                                textAlign: 'center',
                                borderRadius: '12px',
                                border: '1.5px dashed var(--slate-200, #e2e8f0)',
                                backgroundColor: 'var(--slate-50, #f8fafc)'
                            }}>
                                <p style={{ fontSize: '12px', fontWeight: 600, color: 'var(--slate-600, #475569)', margin: 0 }}>
                                    No languages added yet
                                </p>
                                {canEdit && onAddLanguage && (
                                    <button
                                        type="button"
                                        onClick={onAddLanguage}
                                        style={{
                                            marginTop: '6px',
                                            padding: '4px 12px',
                                            backgroundColor: '#f1f5f9',
                                            color: '#1e293b',
                                            borderRadius: '6px',
                                            border: '1px solid #cbd5e1',
                                            fontSize: '11px',
                                            fontWeight: 600,
                                            cursor: 'pointer'
                                        }}
                                    >
                                        + Add Language
                                    </button>
                                )}
                            </div>
                        )}
                    </div>
                </div>
            </section>
        </>
    );
}
