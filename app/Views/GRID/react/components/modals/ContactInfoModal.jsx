import React from 'react';
import ModalContainer from './ModalContainer.jsx';

/**
 * ContactInfoModal Component
 * 
 * Contact details card with 1-click clipboard copy.
 */
export default function ContactInfoModal({
    isOpen,
    onClose,
    user = {},
    onToast
}) {
    const fullName = user.fullName || user.name || 'Member';
    const email = user.email || '';
    const vanityUrl = user.bannerUrl ? `https://${user.bannerUrl}` : (user.gridId ? `https://grid.community/in/${user.gridId}` : '');

    function copyToClipboard(text, label) {
        if (!text) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => {
                if (onToast) onToast(`${label} copied to clipboard! 📋`);
            });
        }
    }

    return (
        <ModalContainer isOpen={isOpen} onClose={onClose} title={`${fullName}'s Contact Info`} maxWidth="500px">
            <div style={{ display: 'flex', flexDirection: 'column', gap: '18px' }}>
                {/* Profile Link */}
                {vanityUrl && (
                    <div>
                        <div style={{ fontSize: '11px', fontWeight: 700, textTransform: 'uppercase', color: 'var(--slate-400)', letterSpacing: '.06em', marginBottom: '4px' }}>
                            Your Profile
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '10px 12px', background: 'var(--slate-50)', border: '1px solid var(--slate-200)', borderRadius: '8px' }}>
                            <span style={{ fontSize: '13px', color: 'var(--brand-600)', fontFamily: 'monospace' }}>{vanityUrl}</span>
                            <button 
                                type="button" 
                                onClick={() => copyToClipboard(vanityUrl, 'Profile URL')}
                                style={{ background: 'none', border: 0, color: 'var(--brand-600)', fontWeight: 700, cursor: 'pointer', fontSize: '12px' }}
                            >
                                Copy
                            </button>
                        </div>
                    </div>
                )}

                {/* Email Address */}
                {email && (
                    <div>
                        <div style={{ fontSize: '11px', fontWeight: 700, textTransform: 'uppercase', color: 'var(--slate-400)', letterSpacing: '.06em', marginBottom: '4px' }}>
                            Email
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '10px 12px', background: 'var(--slate-50)', border: '1px solid var(--slate-200)', borderRadius: '8px' }}>
                            <span style={{ fontSize: '13px', color: 'var(--slate-800)' }}>{email}</span>
                            <button 
                                type="button" 
                                onClick={() => copyToClipboard(email, 'Email address')}
                                style={{ background: 'none', border: 0, color: 'var(--brand-600)', fontWeight: 700, cursor: 'pointer', fontSize: '12px' }}
                            >
                                Copy
                            </button>
                        </div>
                    </div>
                )}

                {/* GitHub & Social Profiles */}
                <div>
                    <div style={{ fontSize: '11px', fontWeight: 700, textTransform: 'uppercase', color: 'var(--slate-400)', letterSpacing: '.06em', marginBottom: '4px' }}>
                        Online Presence
                    </div>
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', fontSize: '13px' }}>
                        {user.githubUrl && (
                            <a href={user.githubUrl} target="_blank" rel="noopener noreferrer" style={{ color: 'var(--brand-600)', fontWeight: 600 }}>
                                GitHub Profile ↗
                            </a>
                        )}
                        {user.linkedinUrl && (
                            <a href={user.linkedinUrl} target="_blank" rel="noopener noreferrer" style={{ color: 'var(--brand-600)', fontWeight: 600 }}>
                                LinkedIn Profile ↗
                            </a>
                        )}
                        {user.website && (
                            <a href={user.website} target="_blank" rel="noopener noreferrer" style={{ color: 'var(--brand-600)', fontWeight: 600 }}>
                                Personal Website ↗
                            </a>
                        )}
                        {!user.githubUrl && !user.linkedinUrl && !user.website && (
                            <span style={{ color: 'var(--slate-400)', fontSize: '13px' }}>No external links added yet.</span>
                        )}
                    </div>
                </div>
            </div>

            <div style={{ display: 'flex', justifyContent: 'flex-end', marginTop: '24px', paddingTop: '16px', borderTop: '1px solid var(--slate-200)' }}>
                <button 
                    type="button" 
                    onClick={onClose}
                    className="g-btn-create"
                    style={{ padding: '8px 24px' }}
                >
                    Close
                </button>
            </div>
        </ModalContainer>
    );
}
