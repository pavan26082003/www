import React, { useState, useEffect } from 'react';
import ModalContainer from './ModalContainer.jsx';
import ConfirmDeleteModal from './ConfirmDeleteModal.jsx';

/**
 * AddEducationModal Component
 * 
 * Supports adding and editing/deleting academic education or professional certifications.
 */
export default function AddEducationModal({
    isOpen,
    onClose,
    onAdd,
    onUpdate,
    onDelete,
    onAddEducation,
    onUpdateEducation,
    onDeleteEducation,
    onAddCertification,
    onUpdateCertification,
    onDeleteCertification,
    itemToEdit = null,
    isCertification = false
}) {
    const isCert = isCertification || Boolean(itemToEdit && (itemToEdit.itemType === 'certification' || itemToEdit.issuer !== undefined || itemToEdit.credId !== undefined));
    const isEditing = Boolean(itemToEdit && itemToEdit.id && !itemToEdit._isNew);

    const actualOnAdd = onAdd || (isCert ? onAddCertification : onAddEducation);
    const actualOnUpdate = onUpdate || (isCert ? onUpdateCertification : onUpdateEducation);
    const actualOnDelete = onDelete || (isCert ? onDeleteCertification : onDeleteEducation);

    // Education fields
    const [name, setName] = useState('');
    const [degree, setDegree] = useState('');
    const [dates, setDates] = useState('');
    const [activities, setActivities] = useState('');

    // Certification fields
    const [issuer, setIssuer] = useState('');
    const [credId, setCredId] = useState('');
    const [url, setUrl] = useState('');
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);

    useEffect(() => {
        if (isOpen && isEditing && itemToEdit) {
            setName(itemToEdit.name || '');
            setDegree(itemToEdit.degree || '');
            setDates(itemToEdit.dates || '');
            setActivities(itemToEdit.activities ? itemToEdit.activities.replace(/<[^>]*>/g, '').replace(/^Activities:\s*/i, '') : '');
            setIssuer(itemToEdit.issuer ? itemToEdit.issuer.replace(/<[^>]*>/g, '') : '');
            setCredId(itemToEdit.credId || '');
            setUrl(itemToEdit.url || '');
        } else if (isOpen) {
            setName('');
            setDegree('');
            setDates('');
            setActivities('');
            setIssuer('');
            setCredId('');
            setUrl('');
        }
    }, [isOpen, itemToEdit, isEditing]);

    function handleSubmit(e) {
        e.preventDefault();

        if (isCert) {
            const initials = name.split(' ').map(w => w[0]).join('').substring(0, 3).toUpperCase() || 'CERT';
            const payload = {
                ...(itemToEdit || {}),
                id: isEditing ? itemToEdit.id : `cert-${Date.now()}`,
                name,
                issuer,
                credId,
                url,
                icon: initials,
                iconClass: itemToEdit?.iconClass || 'g-cert-icon-amber'
            };
            if (isEditing && actualOnUpdate) actualOnUpdate(payload);
            else if (actualOnAdd) actualOnAdd(payload);
        } else {
            const initials = name.split(' ').map(w => w[0]).join('').substring(0, 3).toUpperCase() || 'EDU';
            const payload = {
                ...(itemToEdit || {}),
                id: isEditing ? itemToEdit.id : `edu-${Date.now()}`,
                icon: initials,
                iconClass: itemToEdit?.iconClass || 'g-edu-icon-brand',
                name,
                degree,
                dates,
                activities: activities ? `<strong>Activities:</strong> ${activities}` : ''
            };
            if (isEditing && actualOnUpdate) actualOnUpdate(payload);
            else if (actualOnAdd) actualOnAdd(payload);
        }
        onClose();
    }

    function handleDelete() {
        if (!itemToEdit || !actualOnDelete) return;
        setShowDeleteConfirm(true);
    }

    function handleConfirmDelete() {
        if (!itemToEdit || !actualOnDelete) return;
        actualOnDelete(itemToEdit.id);
        setShowDeleteConfirm(false);
        onClose();
    }

    const title = isCert 
        ? (isEditing ? 'Edit Certification' : 'Add License or Certification')
        : (isEditing ? 'Edit Education' : 'Add Education');

    return (
        <>
        <ModalContainer isOpen={isOpen} onClose={onClose} title={title} maxWidth="540px">
            <form onSubmit={handleSubmit}>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>
                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            {isCert ? 'Certification / License Name *' : 'School / University *'}
                        </label>
                        <input 
                            type="text" 
                            required
                            placeholder={isCert ? "e.g. AWS Certified Cloud Practitioner" : "e.g. Malla Reddy University"}
                            value={name} 
                            onChange={(e) => setName(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        />
                    </div>

                    {!isCert ? (
                        <>
                            <div>
                                <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                                    Degree &amp; Specialization *
                                </label>
                                <input 
                                    type="text" 
                                    required
                                    placeholder="e.g. Bachelor of Technology - BTech, CSE (AI & ML)"
                                    value={degree} 
                                    onChange={(e) => setDegree(e.target.value)}
                                    style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                                />
                            </div>

                            <div>
                                <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                                    Dates (Years attended)
                                </label>
                                <input 
                                    type="text" 
                                    placeholder="e.g. 2022 - 2026"
                                    value={dates} 
                                    onChange={(e) => setDates(e.target.value)}
                                    style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                                />
                            </div>

                            <div>
                                <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                                    Societies &amp; Activities
                                </label>
                                <textarea 
                                    rows="3"
                                    placeholder="Lead Organizer at AI Systems Guild • Vice Chair ACM"
                                    value={activities} 
                                    onChange={(e) => setActivities(e.target.value)}
                                    style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '13px', resize: 'vertical' }}
                                />
                            </div>
                        </>
                    ) : (
                        <>
                            <div>
                                <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                                    Issuing Organization *
                                </label>
                                <input 
                                    type="text" 
                                    required
                                    placeholder="e.g. Amazon Web Services (AWS)"
                                    value={issuer} 
                                    onChange={(e) => setIssuer(e.target.value)}
                                    style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                                />
                            </div>

                            <div>
                                <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                                    Credential ID
                                </label>
                                <input 
                                    type="text" 
                                    placeholder="e.g. Credential ID: AWS-CCP-7749210"
                                    value={credId} 
                                    onChange={(e) => setCredId(e.target.value)}
                                    style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                                />
                            </div>

                            <div>
                                <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                                    Credential Verification URL
                                </label>
                                <input 
                                    type="url" 
                                    placeholder="https://aws.amazon.com/verification"
                                    value={url} 
                                    onChange={(e) => setUrl(e.target.value)}
                                    style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                                />
                            </div>
                        </>
                    )}
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
                            {isEditing ? 'Save Changes' : (isCert ? 'Save Credential' : 'Save Education')}
                        </button>
                    </div>
                </div>
            </form>
        </ModalContainer>

        <ConfirmDeleteModal
            isOpen={showDeleteConfirm}
            onClose={() => setShowDeleteConfirm(false)}
            onConfirm={handleConfirmDelete}
            title={isCert ? "Delete Certification?" : "Delete Education?"}
            message={`Are you sure you want to delete "${itemToEdit?.name}"?`}
            confirmLabel="Yes, Delete"
        />
        </>
    );
}
