import React, { useEffect } from 'react';
import { createPortal } from 'react-dom';

/**
 * ModalContainer Component
 * 
 * Reusable accessible modal dialog shell:
 * - Semi-transparent backdrop with blur
 * - Keyboard Escape listener
 * - Header with title and close '✕' button
 * - Scrollable body and customizable footer
 * - Portaled to document.body to escape CSS containing blocks
 */
export default function ModalContainer({
    isOpen,
    onClose,
    title,
    maxWidth = '540px',
    children
}) {
    useEffect(() => {
        if (!isOpen) return;

        function handleKeyDown(e) {
            if (e.key === 'Escape') onClose();
        }

        const prevOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        window.addEventListener('keydown', handleKeyDown);

        return () => {
            document.body.style.overflow = prevOverflow;
            window.removeEventListener('keydown', handleKeyDown);
        };
    }, [isOpen, onClose]);

    if (!isOpen) return null;

    const modalMarkup = (
        <div 
            className="g-modal-backdrop" 
            style={{ display: 'flex', zIndex: 9999 }}
            onClick={(e) => {
                if (e.target === e.currentTarget) onClose();
            }}
            role="dialog"
            aria-modal="true"
            aria-labelledby="gModalTitle"
        >
            <div 
                className="g-modal-dialog" 
                style={{ maxWidth, width: '100%', margin: 'auto' }}
            >
                {/* Modal Header */}
                <div className="g-modal-header">
                    <h3 className="g-modal-title" id="gModalTitle">{title}</h3>
                    <button 
                        type="button" 
                        className="g-modal-close" 
                        onClick={onClose}
                        aria-label="Close dialog"
                    >
                        ✕
                    </button>
                </div>

                {/* Modal Body / Content */}
                <div className="g-modal-body">
                    {children}
                </div>
            </div>
        </div>
    );

    return typeof document !== 'undefined' ? createPortal(modalMarkup, document.body) : modalMarkup;
}
