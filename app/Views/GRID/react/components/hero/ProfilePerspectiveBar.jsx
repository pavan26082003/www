import React from 'react';

/**
 * ProfilePerspectiveBar Component
 * 
 * Allows the profile owner to test and preview the profile through
 * the eyes of 3 distinct viewer audiences:
 * - Myself (Owner) - can edit, see all controls & visibility badges
 * - Connection - viewing as a 1st-degree connection
 * - Recruiter - viewing as a verified recruiter
 * - Non-Connection - viewing as a public non-connected visitor
 */
export default function ProfilePerspectiveBar({
    activePerspective = 'owner',
    onPerspectiveChange,
    onOpenVisibilityModal,
    hiddenSectionsCount = 0
}) {
    const PERSPECTIVES = [
        { key: 'owner', label: 'Myself (Owner / Edit)', icon: '👑', color: '#6366f1' },
        { key: 'connection', label: 'Connection', icon: '🤝', color: '#0ea5e9' },
        { key: 'recruiter', label: 'Recruiter', icon: '💼', color: '#10b981' },
        { key: 'nonConnection', label: 'Non-Connection', icon: '🌐', color: '#f59e0b' }
    ];

    const isOwner = activePerspective === 'owner';
    const activeObj = PERSPECTIVES.find(p => p.key === activePerspective) || PERSPECTIVES[0];

    return (
        <div 
            className="g-card" 
            style={{
                marginBottom: '16px',
                border: isOwner ? '1px solid rgba(99, 102, 241, 0.2)' : '1px solid rgba(245, 158, 11, 0.35)',
                background: isOwner 
                    ? 'linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95))'
                    : 'linear-gradient(135deg, rgba(254, 243, 199, 0.25), rgba(255, 255, 255, 0.95))',
                backdropFilter: 'blur(8px)',
                boxShadow: '0 2px 8px rgba(0, 0, 0, 0.04)',
                borderRadius: '16px'
            }}
        >
            <div className="g-card-body" style={{ padding: '12px 18px' }}>
                <div style={{
                    display: 'flex',
                    flexWrap: 'wrap',
                    alignItems: 'center',
                    justifyContent: 'space-between',
                    gap: '12px'
                }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                        <div style={{
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: '6px',
                            fontSize: '12px',
                            fontWeight: 700,
                            color: 'var(--slate-700, #334155)',
                            textTransform: 'uppercase',
                            letterSpacing: '0.5px'
                        }}>
                            <span>👁️</span>
                            <span>View Profile As:</span>
                        </div>

                        {/* Perspective Switcher Pills */}
                        <div style={{
                            display: 'inline-flex',
                            background: '#f1f5f9',
                            padding: '3px',
                            borderRadius: '10px',
                            border: '1px solid #e2e8f0',
                            gap: '2px'
                        }}>
                            {PERSPECTIVES.map(p => {
                                const isSelected = p.key === activePerspective;
                                return (
                                    <button
                                        key={p.key}
                                        type="button"
                                        onClick={() => onPerspectiveChange(p.key)}
                                        style={{
                                            padding: '4px 12px',
                                            borderRadius: '8px',
                                            border: 'none',
                                            fontSize: '12px',
                                            fontWeight: isSelected ? 700 : 500,
                                            cursor: 'pointer',
                                            background: isSelected ? '#fff' : 'transparent',
                                            color: isSelected ? p.color : 'var(--slate-600, #475569)',
                                            boxShadow: isSelected ? '0 1px 3px rgba(0,0,0,0.1)' : 'none',
                                            display: 'inline-flex',
                                            alignItems: 'center',
                                            gap: '5px',
                                            transition: 'all 0.15s ease'
                                        }}
                                    >
                                        <span>{p.icon}</span>
                                        <span>{p.label}</span>
                                    </button>
                                );
                            })}
                        </div>
                    </div>

                    {/* Manage Visibility Rules Button */}
                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <button
                            type="button"
                            onClick={onOpenVisibilityModal}
                            style={{
                                display: 'inline-flex',
                                alignItems: 'center',
                                gap: '6px',
                                padding: '6px 14px',
                                borderRadius: '9999px',
                                border: '1px solid #6366f1',
                                background: 'rgba(99, 102, 241, 0.08)',
                                color: '#4f46e5',
                                fontSize: '12px',
                                fontWeight: 600,
                                cursor: 'pointer',
                                transition: 'all 0.15s ease'
                            }}
                        >
                            <span>🔒</span>
                            <span>Privacy &amp; Visibility Settings</span>
                        </button>
                    </div>
                </div>

                {/* Info Notice when viewing as someone else */}
                {!isOwner && (
                    <div style={{
                        marginTop: '10px',
                        padding: '8px 12px',
                        borderRadius: '8px',
                        background: 'rgba(245, 158, 11, 0.12)',
                        border: '1px solid rgba(245, 158, 11, 0.3)',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        gap: '10px',
                        fontSize: '12px',
                        color: '#92400e'
                    }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <span>ℹ️</span>
                            <span>
                                Previewing as <strong>{activeObj.label}</strong>. Edit controls are hidden. 
                                {hiddenSectionsCount > 0 
                                    ? ` ${hiddenSectionsCount} section(s) marked 'Hide' for this audience are omitted.`
                                    : ` All profile sections are visible to this audience.`
                                }
                            </span>
                        </div>
                        <button
                            type="button"
                            onClick={() => onPerspectiveChange('owner')}
                            style={{
                                background: 'transparent',
                                border: 'none',
                                color: '#b45309',
                                fontWeight: 700,
                                textDecoration: 'underline',
                                cursor: 'pointer',
                                fontSize: '12px',
                                whiteSpace: 'nowrap'
                            }}
                        >
                            Return to Edit Mode
                        </button>
                    </div>
                )}
            </div>
        </div>
    );
}
