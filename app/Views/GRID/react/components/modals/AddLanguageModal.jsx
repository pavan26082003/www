import React, { useState, useEffect } from 'react';
import ModalContainer from './ModalContainer.jsx';

/**
 * AddLanguageModal Component
 * 
 * Add or edit languages and proficiency levels.
 */
export default function AddLanguageModal({
    isOpen,
    onClose,
    onSave,
    onDelete,
    itemToEdit = null
}) {
    const isEditing = !!itemToEdit;
    const [name, setName] = useState('');
    const [level, setLevel] = useState('Full Professional');

    const proficiencyLevels = [
        'Native / Bilingual',
        'Full Professional',
        'Professional Working',
        'Limited Working',
        'Elementary'
    ];

    useEffect(() => {
        if (isOpen && itemToEdit) {
            setName(itemToEdit.name || '');
            setLevel(itemToEdit.level || 'Full Professional');
        } else if (isOpen) {
            setName('');
            setLevel('Full Professional');
        }
    }, [isOpen, itemToEdit]);

    function handleSubmit(e) {
        e.preventDefault();
        if (!name.trim()) return;

        onSave({
            id: itemToEdit?.id || `lang-${Date.now()}`,
            name: name.trim(),
            level
        });
        onClose();
    }

    return (
        <ModalContainer 
            isOpen={isOpen} 
            onClose={onClose} 
            title={isEditing ? "Edit Language" : "Add Language"} 
            maxWidth="420px"
        >
            <form onSubmit={handleSubmit} className="space-y-4 pt-1">
                <div>
                    <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Language Name *
                    </label>
                    <input
                        type="text"
                        required
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        placeholder="e.g. English, Telugu, Hindi, Spanish"
                        className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div>
                    <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Proficiency Level
                    </label>
                    <select
                        value={level}
                        onChange={(e) => setLevel(e.target.value)}
                        className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        {proficiencyLevels.map((lvl) => (
                            <option key={lvl} value={lvl}>{lvl}</option>
                        ))}
                    </select>
                </div>

                <div className="flex items-center justify-between pt-4 mt-2 border-t border-slate-200 dark:border-slate-800">
                    {isEditing && onDelete ? (
                        <button
                            type="button"
                            onClick={() => { onDelete(itemToEdit.id); onClose(); }}
                            className="px-4 py-2 text-xs font-bold text-red-600 hover:text-red-700 dark:text-red-400 cursor-pointer"
                        >
                            Delete
                        </button>
                    ) : <div />}

                    <div className="flex items-center gap-2">
                        <button
                            type="button"
                            onClick={onClose}
                            className="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-xl border border-slate-200 dark:border-slate-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            className="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition-all"
                        >
                            {isEditing ? "Update Language" : "Add Language"}
                        </button>
                    </div>
                </div>
            </form>
        </ModalContainer>
    );
}
