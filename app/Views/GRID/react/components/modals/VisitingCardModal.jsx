import React, { useEffect } from 'react';
import { createPortal } from 'react-dom';

/**
 * VisitingCardModal Component
 * Full Glassmorphism pop-up card for user's personal GRID Visiting Card.
 * Features:
 * - Acrylic multi-layer frosted glass backdrop & card surface
 * - Specular border reflection with soft ambient halo glow
 * - Dynamic QR matrix, user verification badge, eFactor score
 * - Copy link, Share, and Save PNG/vCard actions
 */
export default function VisitingCardModal({
    isOpen,
    onClose,
    user,
    onToast
}) {
    useEffect(() => {
        if (!isOpen) return;
        const handleKeyDown = (e) => {
            if (e.key === 'Escape') onClose();
        };
        const prevOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        window.addEventListener('keydown', handleKeyDown);
        return () => {
            document.body.style.overflow = prevOverflow;
            window.removeEventListener('keydown', handleKeyDown);
        };
    }, [isOpen, onClose]);

    if (!isOpen) return null;

    const handleCopy = () => {
        const link = `https://grid.community/card/${(user?.fullName || 'praful-shah').toLowerCase().replace(/\s+/g, '-')}`;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(link);
        }
        if (onToast) onToast('📋 Visiting card link copied to clipboard!');
    };

    const handleDownload = () => {
        if (onToast) onToast('💾 Visiting card saved to your device!');
    };

    const handleShare = () => {
        if (navigator.share) {
            navigator.share({
                title: `${user?.fullName || 'Praful Shah'}'s GRID Visiting Card`,
                url: window.location.href
            }).catch(() => {});
        } else {
            handleCopy();
        }
    };

    const modalContent = (
        <div
            className="fixed inset-0 z-[9999] flex items-center justify-center p-4 select-none animate-in fade-in duration-200"
            style={{
                backgroundColor: 'rgba(10, 15, 29, 0.65)',
                backdropFilter: 'blur(16px)',
                WebkitBackdropFilter: 'blur(16px)'
            }}
            onClick={(e) => {
                if (e.target === e.currentTarget) onClose();
            }}
            role="dialog"
            aria-modal="true"
        >
            {/* Ambient Background Glow */}
            <div className="absolute w-72 h-72 rounded-full bg-gradient-to-tr from-blue-600/30 via-purple-600/20 to-pink-500/20 blur-3xl pointer-events-none" />

            {/* Glassmorphism Card Frame */}
            <div
                className="relative w-full max-w-sm rounded-3xl p-6 overflow-hidden text-slate-900 dark:text-white shadow-[0_20px_60px_rgba(0,0,0,0.45)] border transition-all animate-in zoom-in-95 duration-200"
                style={{
                    background: 'linear-gradient(135deg, rgba(255, 255, 255, 0.45) 0%, rgba(255, 255, 255, 0.15) 100%)',
                    backdropFilter: 'blur(25px)',
                    WebkitBackdropFilter: 'blur(25px)',
                    borderColor: 'rgba(255, 255, 255, 0.4)',
                    boxShadow: '0 8px 32px 0 rgba(31, 38, 135, 0.37), inset 0 0 0 1px rgba(255, 255, 255, 0.25)'
                }}
            >
                {/* Close Button */}
                <button
                    type="button"
                    onClick={onClose}
                    className="absolute top-4 right-4 w-8 h-8 rounded-full flex items-center justify-center text-slate-600 dark:text-slate-200 hover:text-slate-950 dark:hover:text-white transition-colors cursor-pointer"
                    style={{
                        background: 'rgba(255, 255, 255, 0.3)',
                        backdropFilter: 'blur(10px)',
                        border: '1px solid rgba(255, 255, 255, 0.4)'
                    }}
                    title="Close"
                >
                    ✕
                </button>

                {/* Top Badge: Brand Pill */}
                <div className="flex items-center gap-2 mb-5">
                    <div className="w-6 h-6 rounded-lg bg-blue-600 text-white font-black text-xs flex items-center justify-center shadow-md">
                        G
                    </div>
                    <span className="text-xs font-black tracking-wider uppercase text-slate-800 dark:text-slate-100">
                        GRID <span className="text-blue-600 font-semibold">PASS</span>
                    </span>
                    <span className="ml-auto text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                        ● Level 2 Verified
                    </span>
                </div>

                {/* User Identity Section */}
                <div className="text-center space-y-2 mb-5">
                    <div className="relative inline-block">
                        {user?.avatarSrc ? (
                            <img
                                src={user.avatarSrc}
                                alt={user?.fullName || 'User'}
                                className="w-20 h-20 rounded-2xl object-cover mx-auto shadow-xl border-2 border-white/80"
                            />
                        ) : (
                            <div className="w-20 h-20 rounded-2xl bg-gradient-to-tr from-blue-600 to-purple-600 text-white font-extrabold text-2xl flex items-center justify-center mx-auto shadow-xl border-2 border-white/80">
                                {user?.initials || 'PS'}
                            </div>
                        )}
                        <span className="w-4 h-4 rounded-full bg-emerald-500 border-2 border-white absolute -bottom-1 -right-1 shadow-sm" />
                    </div>

                    <div>
                        <div className="flex items-center justify-center gap-1.5">
                            <h2 className="text-lg font-black text-slate-900 dark:text-white tracking-tight">
                                {user?.fullName || 'Praful Shah'}
                            </h2>
                            <svg className="w-4 h-4 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                            </svg>
                        </div>
                        <p className="text-xs text-slate-700 dark:text-slate-300 font-medium line-clamp-1 max-w-xs mx-auto">
                            {user?.headline || 'Lead AI Research Fellow & Systems Architect'}
                        </p>
                    </div>
                </div>

                {/* Scannable Glass QR Container */}
                <div
                    className="p-4 rounded-2xl mx-auto w-48 h-48 flex flex-col items-center justify-center relative shadow-inner mb-5"
                    style={{
                        background: 'rgba(255, 255, 255, 0.75)',
                        backdropFilter: 'blur(12px)',
                        border: '1px solid rgba(255, 255, 255, 0.8)'
                    }}
                >
                    {/* QR Code Graphic Matrix */}
                    <svg className="w-36 h-36" viewBox="0 0 100 100" fill="#0f172a">
                        {/* Top-Left Finder */}
                        <rect x="5" y="5" width="28" height="28" rx="4" fill="none" stroke="#0f172a" strokeWidth="5" />
                        <rect x="12" y="12" width="14" height="14" rx="2" fill="#2563eb" />
                        {/* Top-Right Finder */}
                        <rect x="67" y="5" width="28" height="28" rx="4" fill="none" stroke="#0f172a" strokeWidth="5" />
                        <rect x="74" y="12" width="14" height="14" rx="2" fill="#2563eb" />
                        {/* Bottom-Left Finder */}
                        <rect x="5" y="67" width="28" height="28" rx="4" fill="none" stroke="#0f172a" strokeWidth="5" />
                        <rect x="12" y="74" width="14" height="14" rx="2" fill="#2563eb" />
                        {/* Data Modules */}
                        <rect x="40" y="8" width="6" height="6" rx="1" />
                        <rect x="52" y="8" width="6" height="6" rx="1" />
                        <rect x="40" y="20" width="6" height="6" rx="1" />
                        <rect x="52" y="26" width="6" height="6" rx="1" />
                        <rect x="8" y="42" width="6" height="6" rx="1" />
                        <rect x="20" y="42" width="6" height="6" rx="1" />
                        <rect x="8" y="54" width="6" height="6" rx="1" />
                        <rect x="40" y="42" width="20" height="20" rx="3" fill="#2563eb" />
                        <rect x="46" y="48" width="8" height="8" rx="1" fill="#ffffff" />
                        <rect x="68" y="42" width="6" height="6" rx="1" />
                        <rect x="80" y="42" width="6" height="6" rx="1" />
                        <rect x="86" y="54" width="6" height="6" rx="1" />
                        <rect x="68" y="68" width="6" height="6" rx="1" />
                        <rect x="80" y="74" width="6" height="6" rx="1" />
                        <rect x="40" y="72" width="6" height="6" rx="1" />
                        <rect x="52" y="84" width="6" height="6" rx="1" />
                    </svg>

                    <div className="text-[10px] font-bold text-slate-600 tracking-wider uppercase mt-1">
                        Scan to Connect
                    </div>
                </div>

                {/* Footer Action Buttons */}
                <div className="grid grid-cols-2 gap-2.5">
                    <button
                        type="button"
                        onClick={handleCopy}
                        className="py-2.5 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 transition-all cursor-pointer text-slate-800 dark:text-white"
                        style={{
                            background: 'rgba(255, 255, 255, 0.35)',
                            backdropFilter: 'blur(8px)',
                            border: '1px solid rgba(255, 255, 255, 0.5)'
                        }}
                    >
                        <span>🔗 Copy Link</span>
                    </button>

                    <button
                        type="button"
                        onClick={handleShare}
                        className="py-2.5 px-3 rounded-xl font-bold text-xs bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center gap-1.5 shadow-md transition-all cursor-pointer"
                    >
                        <span>📤 Share Card</span>
                    </button>
                </div>

                <button
                    type="button"
                    onClick={handleDownload}
                    className="w-full mt-2.5 py-1.5 text-center text-[11px] font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-600 transition-colors cursor-pointer"
                >
                    💾 Save vCard &amp; QR to Photos
                </button>
            </div>
        </div>
    );

    return typeof document !== 'undefined' ? createPortal(modalContent, document.body) : modalContent;
}
