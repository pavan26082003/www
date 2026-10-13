import React, { useEffect } from 'react';
import { createPortal } from 'react-dom';

/**
 * ConfirmDeleteModal Component
 * Full-screen portaled confirmation dialog replacing native window.confirm().
 * Escapes parent CSS containing blocks (backdrop-filter, transform) with z-[9999].
 */
export default function ConfirmDeleteModal({
    isOpen,
    onClose,
    onConfirm,
    title = 'Delete Item?',
    message = 'Are you sure you want to delete this item? This action cannot be undone.',
    confirmLabel = 'Yes, Delete',
    isDeleting = false
}) {
    useEffect(() => {
        if (!isOpen) return;

        const handleKeyDown = (e) => {
            if (e.key === 'Escape' && !isDeleting) onClose();
        };

        const originalOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        window.addEventListener('keydown', handleKeyDown);

        return () => {
            document.body.style.overflow = originalOverflow;
            window.removeEventListener('keydown', handleKeyDown);
        };
    }, [isOpen, isDeleting, onClose]);

    if (!isOpen || typeof document === 'undefined') return null;

    return createPortal(
        <div 
            className="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs animate-in fade-in duration-200 select-none"
            onClick={(e) => {
                if (e.target === e.currentTarget && !isDeleting) onClose();
            }}
            role="dialog"
            aria-modal="true"
        >
            <div 
                className="w-full max-w-xs sm:max-w-sm bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 text-center space-y-4 animate-in zoom-in-95 duration-200"
                onClick={(e) => e.stopPropagation()}
            >
                {/* Warning Icon Badge */}
                <div className="w-14 h-14 rounded-full bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 flex items-center justify-center mx-auto ring-8 ring-red-50/50 dark:ring-red-950/30">
                    <span className="material-symbols-outlined text-[28px]">delete</span>
                </div>

                {/* Title & Message */}
                <div className="space-y-1.5">
                    <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                        {title}
                    </h3>
                    <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed px-1">
                        {message}
                    </p>
                </div>

                {/* Actions */}
                <div className="flex items-center gap-3 pt-2">
                    <button
                        type="button"
                        onClick={onClose}
                        disabled={isDeleting}
                        className="flex-1 px-4 py-2.5 rounded-full border border-slate-200 dark:border-slate-700 text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer disabled:opacity-50"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        onClick={onConfirm}
                        disabled={isDeleting}
                        className="flex-1 px-4 py-2.5 rounded-full bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-semibold shadow-xs transition-colors cursor-pointer disabled:opacity-50 flex items-center justify-center gap-1.5"
                    >
                        {isDeleting ? (
                            <>
                                <span className="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                                <span>Deleting...</span>
                            </>
                        ) : (
                            <span>{confirmLabel}</span>
                        )}
                    </button>
                </div>
            </div>
        </div>,
        document.body
    );
}
