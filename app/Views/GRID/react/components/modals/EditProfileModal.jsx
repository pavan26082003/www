import React, { useState } from 'react';
import ModalContainer from './ModalContainer.jsx';

/**
 * EditProfileModal Component
 * 
 * Edit basic personal and professional identity info:
 * Name, pronoun, headline, and location.
 */
export default function EditProfileModal({
    isOpen,
    onClose,
    user = {},
    onSave
}) {
    const [fullName, setFullName] = React.useState(user.fullName || user.name || '');
    const [pronoun, setPronoun] = React.useState(user.pronoun || 'He/Him');
    const [headline, setHeadline] = React.useState(user.headline || '');
    const [location, setLocation] = React.useState(user.location || '');

    React.useEffect(() => {
        if (isOpen) {
            setFullName(user.fullName || user.name || '');
            setPronoun(user.pronoun || 'He/Him');
            setHeadline(user.headline || '');
            setLocation(user.location || '');
        }
    }, [isOpen, user]);

    function handleSubmit(e) {
        e.preventDefault();
        onSave({
            fullName,
            pronoun,
            headline,
            location
        });
        onClose();
    }

    return (
        <ModalContainer isOpen={isOpen} onClose={onClose} title="Edit Intro & Basic Info" maxWidth="560px">
            <form onSubmit={handleSubmit}>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                    {/* Full Name */}
                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Full Name *
                        </label>
                        <input 
                            type="text"
                            required
                            value={fullName}
                            onChange={(e) => setFullName(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        />
                    </div>

                    {/* Pronouns */}
                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Pronouns
                        </label>
                        <input 
                            type="text"
                            value={pronoun}
                            onChange={(e) => setPronoun(e.target.value)}
                            placeholder="He/Him, She/Her, They/Them"
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        />
                    </div>

                    {/* Headline */}
                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Headline *
                        </label>
                        <textarea 
                            rows="3"
                            required
                            value={headline}
                            onChange={(e) => setHeadline(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '13px', resize: 'vertical' }}
                        />
                        <span style={{ fontSize: '11px', color: 'var(--slate-500)' }}>
                            Tip: HTML tags like &lt;strong&gt; are supported.
                        </span>
                    </div>

                    {/* Location */}
                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Location
                        </label>
                        <input 
                            type="text"
                            value={location}
                            onChange={(e) => setLocation(e.target.value)}
                            placeholder="e.g. Greater Hyderabad Area, Telangana, India"
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        />
                    </div>
                </div>

                {/* Footer Buttons */}
                <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '10px', marginTop: '24px', paddingTop: '16px', borderTop: '1px solid var(--slate-200)' }}>
                    <button 
                        type="button" 
                        onClick={onClose}
                        style={{ padding: '8px 18px', borderRadius: '9999px', border: '1px solid var(--slate-300)', background: '#fff', fontSize: '13px', fontWeight: 600, cursor: 'pointer' }}
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        className="g-btn-create"
                        style={{ padding: '8px 22px' }}
                    >
                        Save Intro
                    </button>
                </div>
            </form>
        </ModalContainer>
    );
}
