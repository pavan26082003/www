import React, { useState, useEffect } from 'react';
import ModalContainer from './ModalContainer.jsx';
import ConfirmDeleteModal from './ConfirmDeleteModal.jsx';

/**
 * AddArtifactModal Component
 * 
 * Add / Edit / Delete featured artifact (Repository, Paper, Certification, Demo).
 */
export default function AddArtifactModal({
    isOpen,
    onClose,
    onAdd,
    onUpdate,
    onDelete,
    itemToEdit = null
}) {
    const [name, setName] = useState('');
    const [type, setType] = useState('Open Source Repo');
    const [title, setTitle] = useState('');
    const [desc, setDesc] = useState('');
    const [linkText, setLinkText] = useState('');
    const [linkUrl, setLinkUrl] = useState('');
    const [badge, setBadge] = useState('⭐ New');
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);

    useEffect(() => {
        if (isOpen && itemToEdit) {
            setName(itemToEdit.name || '');
            setType(itemToEdit.type || 'Open Source Repo');
            setTitle(itemToEdit.title || '');
            setDesc(itemToEdit.desc || '');
            setLinkText(itemToEdit.linkText || '');
            setLinkUrl(itemToEdit.linkUrl || '');
            setBadge(itemToEdit.badge || '⭐ New');
        } else if (isOpen) {
            setName('');
            setType('Open Source Repo');
            setTitle('');
            setDesc('');
            setLinkText('');
            setLinkUrl('');
            setBadge('⭐ New');
        }
    }, [isOpen, itemToEdit]);

    function handleSubmit(e) {
        e.preventDefault();
        const artifactPayload = {
            id: itemToEdit ? itemToEdit.id : `feat-${Date.now()}`,
            name,
            type,
            title,
            desc,
            linkText: linkText || name,
            linkUrl: linkUrl || '#',
            badge: badge || 'New',
            coverClass: itemToEdit ? (itemToEdit.coverClass || 'g-feat-cover-1') : 'g-feat-cover-1',
            tagClass: itemToEdit ? (itemToEdit.tagClass || 'g-feat-tag-1') : 'g-feat-tag-1',
            imgSrc: itemToEdit ? itemToEdit.imgSrc : undefined
        };

        if (itemToEdit && onUpdate) {
            onUpdate(artifactPayload);
        } else if (onAdd) {
            onAdd(artifactPayload);
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
            title={itemToEdit ? "Edit Featured Artifact" : "Add Featured Artifact"} 
            maxWidth="540px"
        >
            <form onSubmit={handleSubmit}>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>
                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Artifact Name *
                        </label>
                        <input 
                            type="text" 
                            required
                            placeholder="e.g. HindiShala - Indic LLM"
                            value={name} 
                            onChange={(e) => setName(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        />
                    </div>

                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Artifact Type *
                        </label>
                        <select 
                            value={type} 
                            onChange={(e) => setType(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        >
                            <option value="Open Source Repo">Open Source Repo</option>
                            <option value="Preprint Paper">Preprint Paper</option>
                            <option value="Official Certification">Official Certification</option>
                            <option value="Interactive Demo">Interactive Demo</option>
                        </select>
                    </div>

                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Subtitle / Headline
                        </label>
                        <input 
                            type="text" 
                            placeholder="e.g. Indic Multilingual Assistant"
                            value={title} 
                            onChange={(e) => setTitle(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        />
                    </div>

                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Description
                        </label>
                        <textarea 
                            rows="2"
                            placeholder="Brief technical summary of the achievement..."
                            value={desc} 
                            onChange={(e) => setDesc(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '13px', resize: 'vertical' }}
                        />
                    </div>

                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '10px' }}>
                        <div>
                            <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                                Link Text
                            </label>
                            <input 
                                type="text" 
                                placeholder="github.com/..."
                                value={linkText} 
                                onChange={(e) => setLinkText(e.target.value)}
                                style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                            />
                        </div>
                        <div>
                            <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                                Link URL
                            </label>
                            <input 
                                type="url" 
                                placeholder="https://..."
                                value={linkUrl} 
                                onChange={(e) => setLinkUrl(e.target.value)}
                                style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                            />
                        </div>
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
                            Delete Artifact
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
                            {itemToEdit ? "Save Changes" : "Add Artifact"}
                        </button>
                    </div>
                </div>
            </form>
        </ModalContainer>

        <ConfirmDeleteModal
            isOpen={showDeleteConfirm}
            onClose={() => setShowDeleteConfirm(false)}
            onConfirm={handleConfirmDelete}
            title="Remove Artifact?"
            message={`Are you sure you want to remove artifact "${itemToEdit?.name}"?`}
            confirmLabel="Yes, Remove"
        />
        </>
    );
}
