import React, { useState, useEffect } from 'react';
import ModalContainer from './ModalContainer.jsx';

/**
 * AddHonorModal Component
 * 
 * Add or edit honors, awards, scholarships, hackathon wins, and recognitions.
 */
export default function AddHonorModal({
    isOpen,
    onClose,
    onSave,
    onDelete,
    itemToEdit = null
}) {
    const isEditing = !!itemToEdit;
    const [title, setTitle] = useState('');
    const [issuer, setIssuer] = useState('');
    const [year, setYear] = useState('');
    const [description, setDescription] = useState('');
    const [icon, setIcon] = useState('🏆');

    const availableIcons = ['🏆', '🏅', '🥇', '🥈', '🥉', '🎖️', '⭐', '🎓', '🚀'];

    useEffect(() => {
        if (isOpen && itemToEdit) {
            setTitle(itemToEdit.title || '');
            setIssuer(itemToEdit.issuer || '');
            setYear(itemToEdit.year || '');
            setDescription(itemToEdit.description || '');
            setIcon(itemToEdit.icon || '🏆');
        } else if (isOpen) {
            setTitle('');
            setIssuer('');
            setYear(new Date().getFullYear().toString());
            setDescription('');
            setIcon('🏆');
        }
    }, [isOpen, itemToEdit]);

    function handleSubmit(e) {
        e.preventDefault();
        if (!title.trim()) return;

        const payload = {
            id: itemToEdit?.id || `honor-${Date.now()}`,
            icon,
            title: title.trim(),
            issuer: issuer.trim() + (year.trim() ? ` (${year.trim()})` : ''),
            year: year.trim(),
            description: description.trim()
        };

        onSave(payload);
        onClose();
    }

    return (
        <ModalContainer 
            isOpen={isOpen} 
            onClose={onClose} 
            title={isEditing ? "Edit Honor or Award" : "Add Honor or Award"} 
            maxWidth="520px"
        >
            <form onSubmit={handleSubmit} className="space-y-4 pt-1">
                {/* Icon Picker */}
                <div>
                    <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Badge Icon
                    </label>
                    <div className="flex gap-2">
                        {availableIcons.map((ic) => (
                            <button
                                key={ic}
                                type="button"
                                onClick={() => setIcon(ic)}
                                className={`w-10 h-10 rounded-xl text-xl flex items-center justify-center border transition-all ${
                                    icon === ic
                                        ? 'border-blue-600 bg-blue-50 dark:bg-blue-950/40 shadow-xs scale-105'
                                        : 'border-slate-200 dark:border-slate-700 hover:border-slate-400 bg-white dark:bg-slate-800'
                                }`}
                            >
                                {ic}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Title */}
                <div>
                    <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Title / Award Name *
                    </label>
                    <input
                        type="text"
                        required
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        placeholder="e.g. 1st Place — National Hackathon, Dean's Merit Award"
                        className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                {/* Issuer & Year */}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Issuer / Organization *
                        </label>
                        <input
                            type="text"
                            required
                            value={issuer}
                            onChange={(e) => setIssuer(e.target.value)}
                            placeholder="e.g. ACM, IEEE, University"
                            className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Year
                        </label>
                        <input
                            type="text"
                            value={year}
                            onChange={(e) => setYear(e.target.value)}
                            placeholder="e.g. 2025"
                            className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </div>

                {/* Description */}
                <div>
                    <label className="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Description / Impact (Optional)
                    </label>
                    <textarea
                        rows="3"
                        value={description}
                        onChange={(e) => setDescription(e.target.value)}
                        placeholder="Briefly describe what this honor was recognized for and its significance..."
                        className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                    />
                </div>

                {/* Actions */}
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
                            {isEditing ? "Update Honor" : "Add Honor"}
                        </button>
                    </div>
                </div>
            </form>
        </ModalContainer>
    );
}
