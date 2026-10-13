import React from 'react';

/**
 * EducationList Component
 * 
 * Composes:
 * - Academic degrees and university history
 * - Professional licenses and credentials
 */
export default function EducationList({
    educations = [],
    certifications = [],
    canEdit = true,
    showEducation = true,
    showCertifications = true,
    onAddEducation,
    onEditEducation,
    onAddCertification,
    onEditCertification,
    onManageEduVisibility,
    eduVisibilityInfo,
    onManageCertVisibility,
    certVisibilityInfo
}) {
    return (
        <>
            {/* ═══ EDUCATION SECTION ═══ */}
            {showEducation && (
                <section className="g-card" id="gEducationSection">
                    <div className="g-card-body">
                        <div className="g-section-header" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                                <h3 className="g-section-title" style={{ margin: 0 }}>Education</h3>
                            </div>
                            {canEdit && (
                                <button 
                                    type="button"
                                    className="g-add-btn" 
                                    onClick={onAddEducation}
                                    aria-label="Add education" 
                                    title="Add education"
                                >
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 4v16m8-8H4" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
                                    </svg>
                                </button>
                            )}
                        </div>

                        <div className="g-exp-list" id="gEducationListContainer">
                            {educations.map((edu, idx) => (
                                <React.Fragment key={edu.id}>
                                    {idx > 0 && <hr className="g-divider" />}
                                    <div className="g-edu-item" id={edu.id} style={{ position: 'relative', display: 'flex', gap: '14px', alignItems: 'flex-start' }}>
                                        <div className={`g-edu-icon ${edu.iconClass || 'g-edu-icon-brand'}`}>
                                            {edu.icon || 'EDU'}
                                        </div>
                                        <div style={{ flex: 1, paddingRight: canEdit ? '34px' : '0' }}>
                                            <div className="g-edu-name" style={{ fontWeight: 600 }}>{edu.name}</div>
                                            <div className="g-edu-degree">{edu.degree}</div>
                                            <div 
                                                className="g-edu-dates"
                                                dangerouslySetInnerHTML={{ __html: edu.dates }}
                                            />
                                            {edu.activities && (
                                                <div 
                                                    className="g-edu-activities"
                                                    dangerouslySetInnerHTML={{ __html: edu.activities }}
                                                />
                                            )}
                                        </div>
                                        {canEdit && onEditEducation && (
                                            <button
                                                type="button"
                                                onClick={() => onEditEducation(edu)}
                                                className="g-item-edit-btn"
                                                title="Edit education entry"
                                                style={{
                                                    position: 'absolute',
                                                    top: '0',
                                                    right: '0',
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
                                </React.Fragment>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {/* ═══ LICENSES & CERTIFICATIONS SECTION ═══ */}
            {showCertifications && (
                <section className="g-card" id="gCertificationsSection">
                    <div className="g-card-body">
                        <div className="g-section-header" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                                <h3 className="g-section-title" style={{ margin: 0 }}>Licenses &amp; Certifications</h3>
                            </div>
                            {canEdit && (
                                <button 
                                    type="button"
                                    className="g-add-btn" 
                                    onClick={onAddCertification}
                                    aria-label="Add certification" 
                                    title="Add certification"
                                >
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 4v16m8-8H4" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
                                    </svg>
                                </button>
                            )}
                        </div>

                        <div className="g-cert-list" id="gCertListContainer">
                            {certifications.map((cert) => (
                                <div key={cert.id} className="g-cert-item" id={cert.id} style={{ position: 'relative' }}>
                                    <div className="g-cert-inner" style={{ paddingRight: canEdit ? '34px' : '0' }}>
                                        <div className={`g-cert-icon ${cert.iconClass || 'g-cert-icon-amber'}`}>
                                            {cert.icon || 'CERT'}
                                        </div>
                                        <div style={{ flex: 1 }}>
                                            <div className="g-cert-name" style={{ fontWeight: 600 }}>{cert.name}</div>
                                            <div 
                                                className="g-cert-issuer"
                                                dangerouslySetInnerHTML={{ __html: cert.issuer }}
                                            />
                                            {cert.credId && (
                                                <div className="g-cert-id">{cert.credId}</div>
                                            )}
                                        </div>
                                    </div>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px', marginTop: '6px' }}>
                                        <a 
                                            href={cert.url || '#'} 
                                            target="_blank" 
                                            rel="noopener noreferrer" 
                                            className="g-cert-link"
                                        >
                                            Show credential ↗
                                        </a>
                                    </div>
                                    {canEdit && onEditCertification && (
                                        <button
                                            type="button"
                                            onClick={() => onEditCertification(cert)}
                                            className="g-item-edit-btn"
                                            title="Edit certification"
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
            )}
        </>
    );
}
