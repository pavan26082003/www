import React, { useState, useEffect } from 'react';
import ModalContainer from './ModalContainer.jsx';
import ConfirmDeleteModal from './ConfirmDeleteModal.jsx';

/**
 * AddExperienceModal Component
 * 
 * Supports both adding new experience and editing/deleting existing entries.
 */
export default function AddExperienceModal({
    isOpen,
    onClose,
    onAdd,
    onUpdate,
    onDelete,
    itemToEdit = null
}) {
    const isEditing = !!itemToEdit;

    const [title, setTitle] = useState('');
    const [company, setCompany] = useState('');
    const [dates, setDates] = useState('');
    const [isCurrent, setIsCurrent] = useState(false);
    const [bulletsText, setBulletsText] = useState('');
    const [tagsText, setTagsText] = useState('');
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);

    useEffect(() => {
        if (isOpen && itemToEdit) {
            setTitle(itemToEdit.title || '');
            setCompany(itemToEdit.company || '');
            setDates(itemToEdit.dates || '');
            setIsCurrent(!!itemToEdit.isCurrent);
            setBulletsText((itemToEdit.bullets || []).join('\n'));
            setTagsText((itemToEdit.tags || []).join(', '));
        } else if (isOpen) {
            setTitle('');
            setCompany('');
            setDates('');
            setIsCurrent(false);
            setBulletsText('');
            setTagsText('');
        }
    }, [isOpen, itemToEdit]);

    function handleSubmit(e) {
        e.preventDefault();
        const bullets = bulletsText.split('\n').map(b => b.trim()).filter(Boolean);
        const tags = tagsText.split(',').map(t => t.trim()).filter(Boolean);
        const initials = company.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase() || 'EXP';

        const payload = {
            ...(itemToEdit || {}),
            id: isEditing ? itemToEdit.id : `exp-${Date.now()}`,
            icon: initials,
            iconClass: itemToEdit?.iconClass || 'g-exp-icon-brand',
            title,
            company,
            dates,
            isCurrent,
            bullets,
            tags
        };

        if (isEditing && onUpdate) {
            onUpdate(payload);
        } else if (onAdd) {
            onAdd(payload);
        }
        onClose();
    }

    function handleDelete() {
        if (!itemToEdit || !onDelete) return;
        setShowDeleteConfirm(true);
    }

    function handleConfirmDelete() {
        if (!itemToEdit || !onDelete) return;
        onDelete(itemToEdit.id);
        setShowDeleteConfirm(false);
        onClose();
    }

    return (
        <>
        <ModalContainer 
            isOpen={isOpen} 
            onClose={onClose} 
            title={isEditing ? "Edit Experience" : "Add Experience"} 
            maxWidth="540px"
        >
            <form onSubmit={handleSubmit}>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>
                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Job Title *
                        </label>
                        <input 
                            type="text" 
                            required
                            placeholder="e.g. LLM Post Training Intern"
                            value={title} 
                            onChange={(e) => setTitle(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        />
                    </div>

                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Company Name *
                        </label>
                        <input 
                            type="text" 
                            required
                            placeholder="e.g. Google · Full-time"
                            value={company} 
                            onChange={(e) => setCompany(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        />
                    </div>

                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Dates &amp; Location
                        </label>
                        <input 
                            type="text" 
                            placeholder="e.g. Jan 2026 – Present · 4 mos · Remote"
                            value={dates} 
                            onChange={(e) => setDates(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        />
                    </div>

                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <input 
                            type="checkbox" 
                            id="expIsCurrent"
                            checked={isCurrent}
                            onChange={(e) => setIsCurrent(e.target.checked)}
                            style={{ width: '16px', height: '16px' }}
                        />
                        <label htmlFor="expIsCurrent" style={{ fontSize: '13px', color: 'var(--slate-700)', cursor: 'pointer' }}>
                            I am currently working in this role
                        </label>
                    </div>

                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Key Achievements / Bullets (One per line)
                        </label>
                        <textarea 
                            rows="4"
                            placeholder="Designed automated LLM evaluation harnesses..."
                            value={bulletsText} 
                            onChange={(e) => setBulletsText(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '13px', resize: 'vertical' }}
                        />
                    </div>

                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Skills Used (Comma-separated)
                        </label>
                        <input 
                            type="text" 
                            placeholder="PyTorch, DPO, vLLM, RLHF"
                            value={tagsText} 
                            onChange={(e) => setTagsText(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        />
                    </div>
                </div>

                <div style={{ display: 'flex', justifyContent: isEditing ? 'space-between' : 'flex-end', alignItems: 'center', gap: '10px', marginTop: '24px', paddingTop: '16px', borderTop: '1px solid var(--slate-200)' }}>
                    {isEditing && (
                        <button
                            type="button"
                            onClick={handleDelete}
                            style={{
                                display: 'inline-flex',
                                alignItems: 'center',
                                gap: '6px',
                                padding: '8px 14px',
                                borderRadius: '8px',
                                border: '1px solid #fecaca',
                                background: '#fef2f2',
                                color: '#b91c1c',
                                fontSize: '13px',
                                fontWeight: 600,
                                cursor: 'pointer'
                            }}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style={{ width: '15px', height: '15px' }}>
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    )}
                    <div style={{ display: 'flex', gap: '10px' }}>
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
                            {isEditing ? 'Save Changes' : 'Save Experience'}
                        </button>
                    </div>
                </div>
            </form>
        </ModalContainer>

        <ConfirmDeleteModal
            isOpen={showDeleteConfirm}
            onClose={() => setShowDeleteConfirm(false)}
            onConfirm={handleConfirmDelete}
            title="Delete Experience?"
            message={`Are you sure you want to delete "${itemToEdit?.title}"?`}
            confirmLabel="Yes, Delete"
        />
        </>
    );
}
