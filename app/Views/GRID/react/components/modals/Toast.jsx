import React from 'react';

/**
 * Toast Notification Component
 * 
 * Displays floating feedback message at the bottom right.
 */
export default function Toast({ message, onClose }) {
    if (!message) return null;

    return (
        <div className="g-toast" role="status" aria-live="polite">
            <span className="g-toast-msg">{message}</span>
            <button 
                type="button" 
                className="g-toast-close" 
                onClick={onClose} 
                aria-label="Dismiss notification"
            >
                ✕
            </button>
        </div>
    );
}
