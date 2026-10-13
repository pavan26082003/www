import React, { useState, useEffect } from 'react';
import ModalContainer from './ModalContainer.jsx';
import ConfirmDeleteModal from './ConfirmDeleteModal.jsx';

/**
 * AddProjectModal Component
 * 
 * Add / Edit / Delete key showcase project entry.
 */
export default function AddProjectModal({
    isOpen,
    onClose,
    onAdd,
    onUpdate,
    onDelete,
    itemToEdit = null
}) {
    const [name, setName] = useState('');
    const [dates, setDates] = useState('');
    const [badge, setBadge] = useState('Live Demo');
    const [desc, setDesc] = useState('');
    const [tagsText, setTagsText] = useState('');
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);

    useEffect(() => {
        if (isOpen && itemToEdit) {
            setName(itemToEdit.name || '');
            setDates(itemToEdit.dates || itemToEdit.date || '');
            setBadge(itemToEdit.badge || 'Live Demo');
            setDesc(itemToEdit.desc || '');
            setTagsText(Array.isArray(itemToEdit.tags) ? itemToEdit.tags.join(', ') : (itemToEdit.tags || ''));
        } else if (isOpen) {
            setName('');
            setDates('');
            setBadge('Live Demo');
            setDesc('');
            setTagsText('');
        }
    }, [isOpen, itemToEdit]);

    function handleSubmit(e) {
        e.preventDefault();
        const tags = tagsText.split(',').map(t => t.trim()).filter(Boolean);

        const projectPayload = {
            id: itemToEdit ? itemToEdit.id : `proj-${Date.now()}`,
            name,
            dates,
            badge,
            badgeClass: badge === 'Live Demo' ? 'g-proj-badge-green' : 'g-proj-badge-indigo',
            desc,
            tags
        };

        if (itemToEdit && onUpdate) {
            onUpdate(projectPayload);
        } else if (onAdd) {
            onAdd(projectPayload);
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
            title={itemToEdit ? "Edit Project" : "Add Project"} 
            maxWidth="540px"
        >
            <form onSubmit={handleSubmit}>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>
                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Project Name *
                        </label>
                        <input 
                            type="text" 
                            required
                            placeholder="e.g. HindiShala - Indic Educational Assistant"
                            value={name} 
                            onChange={(e) => setName(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        />
                    </div>

                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '10px' }}>
                        <div>
                            <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                                Dates
                            </label>
                            <input 
                                type="text" 
                                placeholder="Aug 2024 – Nov 2024"
                                value={dates} 
                                onChange={(e) => setDates(e.target.value)}
                                style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                            />
                        </div>
                        <div>
                            <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                                Badge Tag
                            </label>
                            <input 
                                type="text" 
                                placeholder="Live Demo, Open Source"
                                value={badge} 
                                onChange={(e) => setBadge(e.target.value)}
                                style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                            />
                        </div>
                    </div>

                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Description *
                        </label>
                        <textarea 
                            rows="3"
                            required
                            placeholder="Engineered an end-to-end interactive conversational system..."
                            value={desc} 
                            onChange={(e) => setDesc(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '13px', resize: 'vertical' }}
                        />
                    </div>

                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Technologies / Tags (Comma-separated)
                        </label>
                        <input 
                            type="text" 
                            placeholder="Llama-3, TRL, FastAPI, PyTorch"
                            value={tagsText} 
                            onChange={(e) => setTagsText(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        />
                    </div>
                </div>

                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: '24px', paddingTop: '16px', borderTop: '1px solid var(--slate-200)' }}>
                    {itemToEdit && onDelete ? (
                        <button
                            type="button"
                            onClick={handleDelete}
                            style={{
                                padding: '8px 14px',
                                borderRadius: '8px',
                                border: '1px solid #fecaca',
                                background: '#fef2f2',
                                color: '#dc2626',
                                fontSize: '13px',
                                fontWeight: 600,
                                cursor: 'pointer'
                            }}
                        >
                            Delete Project
                        </button>
                    ) : <div />}

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
                            {itemToEdit ? "Save Changes" : "Save Project"}
                        </button>
                    </div>
                </div>
            </form>
        </ModalContainer>

        <ConfirmDeleteModal
            isOpen={showDeleteConfirm}
            onClose={() => setShowDeleteConfirm(false)}
            onConfirm={handleConfirmDelete}
            title="Remove Project?"
            message={`Are you sure you want to remove project "${itemToEdit?.name}"?`}
            confirmLabel="Yes, Remove"
        />
        </>
    );
}
