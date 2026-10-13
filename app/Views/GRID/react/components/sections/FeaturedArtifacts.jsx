import React from 'react';

/**
 * FeaturedArtifacts Component
 * 
 * Interactive showcase grid / mobile touch-scroll carousel for
 * models, preprint papers, and certifications.
 */
export default function FeaturedArtifacts({
    artifacts = [],
    canEdit = true,
    onAddArtifact,
    onEditArtifact,
    onManageVisibility,
    visibilityInfo
}) {
    return (
        <section className="g-card" id="gFeaturedSection">
            <div className="g-card-body">
                <div className="g-section-header" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <h3 className="g-section-title" style={{ margin: 0 }}>Featured Artifacts</h3>
                    </div>

                    {canEdit && onAddArtifact && (
                        <button 
                            type="button"
                            className="g-edit-btn" 
                            onClick={onAddArtifact}
                            style={{ color: 'var(--brand-600, #4f46e5)', fontSize: '12px', fontWeight: 600, padding: '4px 8px' }}
                        >
                            + Add artifact
                        </button>
                    )}
                </div>

                {/* Artifacts Grid / Mobile Carousel */}
                {artifacts.length === 0 ? (
                    <div style={{ textAlign: 'center', padding: '32px 16px', background: 'var(--slate-50, #f8fafc)', borderRadius: '12px', border: '1px dashed var(--slate-300, #cbd5e1)' }}>
                        <div style={{ fontSize: '26px', marginBottom: '6px' }}>📁</div>
                        <div style={{ fontWeight: 600, fontSize: '13px', color: 'var(--slate-700, #334155)' }}>No featured artifacts yet</div>
                        <p style={{ fontSize: '12px', color: 'var(--slate-400, #94a3b8)', margin: '4px 0 0' }}>
                            Showcase open-source repositories, preprint research papers, or certifications.
                        </p>
                    </div>
                ) : (
                    <div className="g-featured-grid" id="gFeaturedGrid">
                        {artifacts.map((art) => (
                            <div key={art.id} className="g-feat-card" id={art.id} style={{ position: 'relative' }}>
                                <div className={`g-feat-cover ${art.coverClass || 'g-feat-cover-1'}`}>
                                    {art.imgSrc && (
                                        <>
                                            <img 
                                                src={art.imgSrc} 
                                                alt={art.name} 
                                                className="g-feat-cover-img" 
                                            />
                                            <div className="g-feat-cover-overlay" />
                                        </>
                                    )}
                                    <span className={`g-feat-tag ${art.tagClass || 'g-feat-tag-1'}`}>
                                        {art.type}
                                    </span>
                                    <span className="g-feat-name">
                                        {art.name}
                                    </span>
                                    {canEdit && onEditArtifact && (
                                        <button
                                            type="button"
                                            onClick={(e) => {
                                                e.preventDefault();
                                                e.stopPropagation();
                                                onEditArtifact(art);
                                            }}
                                            className="g-item-edit-btn"
                                            title="Edit artifact"
                                            style={{
                                                position: 'absolute',
                                                top: '8px',
                                                right: '8px',
                                                background: 'rgba(15, 23, 42, 0.65)',
                                                backdropFilter: 'blur(4px)',
                                                border: '1px solid rgba(255, 255, 255, 0.2)',
                                                color: '#fff',
                                                cursor: 'pointer',
                                                fontSize: '0.9rem',
                                                padding: '3px 7px',
                                                borderRadius: '6px',
                                                zIndex: 5,
                                                transition: 'all 0.15s ease'
                                            }}
                                        >
                                            ✎
                                        </button>
                                    )}
                                </div>

                                <div className="g-feat-body">
                                    <div>
                                        <div className="g-feat-title">{art.title}</div>
                                        <div className="g-feat-desc">{art.desc}</div>
                                    </div>
                                    <div className="g-feat-footer">
                                        <a 
                                            href={art.linkUrl || '#'} 
                                            target="_blank" 
                                            rel="noopener noreferrer"
                                            style={{ color: 'inherit', textDecoration: 'none' }}
                                        >
                                            <span>{art.linkText}</span>
                                        </a>
                                        <span>{art.badge}</span>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </section>
    );
}
