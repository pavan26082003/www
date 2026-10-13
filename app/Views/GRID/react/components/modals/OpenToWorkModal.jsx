import React, { useState } from 'react';
import ModalContainer from './ModalContainer.jsx';

/**
 * OpenToWorkModal Component
 * 
 * Edit job titles, location types, and availability preferences.
 */
export default function OpenToWorkModal({
    isOpen,
    onClose,
    openToWork = {},
    onSave
}) {
    const [active, setActive] = useState(openToWork.active !== undefined ? openToWork.active : true);
    const [roles, setRoles] = useState(openToWork.roles || 'ML Engineer, LLM Alignment Researcher, and AI Systems Intern roles.');
    const [note, setNote] = useState(openToWork.note || 'Hybrid / Remote • Immediate availability');

    function handleSubmit(e) {
        e.preventDefault();
        onSave({
            active,
            roles,
            note
        });
        onClose();
    }

    return (
        <ModalContainer isOpen={isOpen} onClose={onClose} title="Edit Open To Work Preferences" maxWidth="520px">
            <form onSubmit={handleSubmit}>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                    {/* Active Toggle */}
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '12px', background: 'var(--slate-50)', borderRadius: '10px' }}>
                        <div>
                            <strong style={{ fontSize: '13px', color: 'var(--slate-900)' }}>Show #OpenToWork Badge</strong>
                            <p style={{ fontSize: '12px', color: 'var(--slate-500)', marginTop: '2px' }}>Visible to all Grid members and recruiters</p>
                        </div>
                        <input 
                            type="checkbox"
                            checked={active}
                            onChange={(e) => setActive(e.target.checked)}
                            style={{ width: '18px', height: '18px', cursor: 'pointer' }}
                        />
                    </div>

                    {/* Target Roles */}
                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Target Job Titles / Roles
                        </label>
                        <textarea 
                            rows="2"
                            value={roles}
                            onChange={(e) => setRoles(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '13px', resize: 'vertical' }}
                        />
                    </div>

                    {/* Work Types / Notes */}
                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Workplace Types &amp; Notice
                        </label>
                        <input 
                            type="text"
                            value={note}
                            onChange={(e) => setNote(e.target.value)}
                            placeholder="e.g. Hybrid / Remote • Immediate availability"
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
                        Save Preferences
                    </button>
                </div>
            </form>
        </ModalContainer>
    );
}
