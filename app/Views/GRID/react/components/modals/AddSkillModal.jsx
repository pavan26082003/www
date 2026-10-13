import React, { useState, useEffect, useRef } from 'react';
import ModalContainer from './ModalContainer.jsx';
import ConfirmDeleteModal from './ConfirmDeleteModal.jsx';
import gridApi from '../../gridApi.js';

/**
 * AddSkillModal Component
 * 
 * Add / Edit / Delete technical skill with live database taxonomy autocomplete.
 */
export default function AddSkillModal({
    isOpen,
    onClose,
    onAdd,
    onUpdate,
    onDelete,
    itemToEdit = null
}) {
    const [skillName, setSkillName] = useState('');
    const [note, setNote] = useState('');
    const [suggestions, setSuggestions] = useState([]);
    const [showSuggestions, setShowSuggestions] = useState(false);
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);
    const searchTimeoutRef = useRef(null);

    useEffect(() => {
        if (isOpen && itemToEdit) {
            setSkillName(itemToEdit.name || '');
            setNote(itemToEdit.note || '');
        } else if (isOpen) {
            setSkillName('');
            setNote('');
        }
        setSuggestions([]);
        setShowSuggestions(false);
    }, [isOpen, itemToEdit]);

    // Live Database Autocomplete Query
    const handleSkillInputChange = (value) => {
        setSkillName(value);
        if (searchTimeoutRef.current) clearTimeout(searchTimeoutRef.current);

        if (value.trim().length >= 1) {
            searchTimeoutRef.current = setTimeout(async () => {
                const results = await gridApi.suggestSkills(value.trim());
                if (Array.isArray(results) && results.length > 0) {
                    setSuggestions(results);
                    setShowSuggestions(true);
                } else {
                    setSuggestions([]);
                    setShowSuggestions(false);
                }
            }, 180);
        } else {
            setSuggestions([]);
            setShowSuggestions(false);
        }
    };

    function handleSubmit(e) {
        e.preventDefault();
        if (!skillName.trim()) return;

        const skillPayload = {
            id: itemToEdit ? itemToEdit.id : `skill-${Date.now()}`,
            name: skillName.trim(),
            endorsements: itemToEdit ? (itemToEdit.endorsements || 0) : 1,
            note: note.trim() || (itemToEdit ? '' : 'Recently added')
        };

        if (itemToEdit && onUpdate) {
            onUpdate(skillPayload);
        } else if (onAdd) {
            onAdd(skillPayload);
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
            title={itemToEdit ? "Edit Skill" : "Add Skill"} 
            maxWidth="480px"
        >
            <form onSubmit={handleSubmit}>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>
                    <div style={{ position: 'relative' }}>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Skill Name *
                        </label>
                        <input 
                            type="text" 
                            required
                            placeholder="e.g. Python, React, PyTorch, Docker..."
                            value={skillName} 
                            onChange={(e) => handleSkillInputChange(e.target.value)}
                            onFocus={() => { if (suggestions.length > 0) setShowSuggestions(true); }}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                            autoComplete="off"
                        />

                        {/* Database Live Suggestions Dropdown */}
                        {showSuggestions && suggestions.length > 0 && (
                            <div 
                                style={{
                                    position: 'absolute',
                                    top: '100%',
                                    left: 0,
                                    right: 0,
                                    zIndex: 50,
                                    background: '#ffffff',
                                    borderRadius: '8px',
                                    marginTop: '4px',
                                    boxShadow: '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)',
                                    border: '1px solid #e2e8f0',
                                    maxHeight: '210px',
                                    overflowY: 'auto'
                                }}
                            >
                                <div style={{ padding: '6px 12px', fontSize: '10px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: '#94a3b8', borderBottom: '1px solid #f1f5f9' }}>
                                    Standard Taxonomy Matches
                                </div>
                                {suggestions.map((s) => (
                                    <button
                                        key={s.id}
                                        type="button"
                                        onClick={() => {
                                            setSkillName(s.name);
                                            setShowSuggestions(false);
                                        }}
                                        style={{
                                            width: '100%',
                                            display: 'flex',
                                            justifyContent: 'space-between',
                                            alignItems: 'center',
                                            padding: '8px 12px',
                                            textAlign: 'left',
                                            fontSize: '13px',
                                            cursor: 'pointer',
                                            background: 'transparent',
                                            border: 'none',
                                            borderBottom: '1px solid #f8fafc',
                                            transition: 'background 0.15s ease'
                                        }}
                                        onMouseEnter={(e) => e.currentTarget.style.background = '#f1f5f9'}
                                        onMouseLeave={(e) => e.currentTarget.style.background = 'transparent'}
                                    >
                                        <span style={{ fontWeight: 600, color: '#0f172a' }}>{s.name}</span>
                                        <span style={{ fontSize: '11px', color: '#64748b', background: '#f8fafc', padding: '2px 6px', borderRadius: '4px' }}>
                                            {s.category}
                                        </span>
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>

                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Context / Note (Optional)
                        </label>
                        <input 
                            type="text" 
                            placeholder="e.g. Used across 4 core repositories"
                            value={note} 
                            onChange={(e) => setNote(e.target.value)}
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
                            Delete Skill
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
                            {itemToEdit ? "Save Changes" : "Add Skill"}
                        </button>
                    </div>
                </div>
            </form>
        </ModalContainer>

        <ConfirmDeleteModal
            isOpen={showDeleteConfirm}
            onClose={() => setShowDeleteConfirm(false)}
            onConfirm={handleConfirmDelete}
            title="Remove Skill?"
            message={`Are you sure you want to remove skill "${itemToEdit?.name}"?`}
            confirmLabel="Yes, Remove"
        />
        </>
    );
}
