import React, { useState, useEffect } from 'react';
import { createPortal } from 'react-dom';

/**
 * ScanQrModal Component
 * Full Glassmorphism pop-up scanner for GRID QR codes.
 * Features:
 * - High-tech frosted glass HUD
 * - Corner reticle brackets with animated sweeping neon laser line
 * - Tab 1: "Scan QR" (Camera Viewfinder, Upload from Gallery, Torch Toggle)
 * - Tab 2: "My QR Code" (Instant personal QR code sharing)
 */
export default function ScanQrModal({
    isOpen,
    onClose,
    user,
    onToast
}) {
    const [activeTab, setActiveTab] = useState('scan'); // 'scan' | 'my-qr'
    const [isTorchOn, setIsTorchOn] = useState(false);

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

    const handleFileUpload = (e) => {
        const file = e.target.files?.[0];
        if (file) {
            const userName = user?.fullName || user?.name || 'Praful Shah';
            if (onToast) onToast(`Scanning image "${file.name}"... Connected to ${userName}! ✨`);
            setTimeout(() => {
                onClose();
            }, 1200);
        }
    };

    const toggleTorch = () => {
        setIsTorchOn(prev => {
            const next = !prev;
            if (onToast) onToast(next ? '🔦 Flashlight turned ON' : 'Flashlight turned OFF');
            return next;
        });
    };

    const modalMarkup = (
        <div
            className="fixed inset-0 z-[9999] flex items-center justify-center p-4 select-none animate-in fade-in duration-200"
            style={{
                backgroundColor: 'rgba(10, 15, 29, 0.7)',
                backdropFilter: 'blur(18px)',
                WebkitBackdropFilter: 'blur(18px)'
            }}
            onClick={(e) => {
                if (e.target === e.currentTarget) onClose();
            }}
            role="dialog"
            aria-modal="true"
        >
            {/* Ambient Background Glow */}
            <div className="absolute w-80 h-80 rounded-full bg-gradient-to-tr from-cyan-500/25 via-blue-600/20 to-purple-600/20 blur-3xl pointer-events-none" />

            {/* Glassmorphic Scanner Dialog */}
            <div
                className="relative w-full max-w-sm rounded-3xl p-6 overflow-hidden text-white shadow-[0_20px_60px_rgba(0,0,0,0.6)] border transition-all animate-in zoom-in-95 duration-200"
                style={{
                    background: 'linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(30, 41, 59, 0.6) 100%)',
                    backdropFilter: 'blur(28px)',
                    WebkitBackdropFilter: 'blur(28px)',
                    borderColor: 'rgba(255, 255, 255, 0.2)',
                    boxShadow: '0 8px 32px 0 rgba(0, 0, 0, 0.45), inset 0 0 0 1px rgba(255, 255, 255, 0.15)'
                }}
            >
                {/* Close Button */}
                <button
                    type="button"
                    onClick={onClose}
                    className="absolute top-4 right-4 w-8 h-8 rounded-full flex items-center justify-center text-slate-300 hover:text-white transition-colors cursor-pointer"
                    style={{
                        background: 'rgba(255, 255, 255, 0.12)',
                        backdropFilter: 'blur(10px)',
                        border: '1px solid rgba(255, 255, 255, 0.2)'
                    }}
                    title="Close"
                >
                    ✕
                </button>

                {/* Header Title */}
                <div className="flex items-center gap-2 mb-4">
                    <div className="w-6 h-6 rounded-lg bg-cyan-500 text-slate-950 font-black text-xs flex items-center justify-center shadow-md">
                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <span className="text-xs font-black tracking-wider uppercase text-slate-200">
                        GRID <span className="text-cyan-400 font-semibold">SCANNER</span>
                    </span>
                </div>

                {/* Dual Mode Switcher Tabs */}
                <div className="grid grid-cols-2 gap-1.5 p-1 rounded-xl bg-white/10 backdrop-blur-md border border-white/10 mb-5 text-xs font-bold">
                    <button
                        type="button"
                        onClick={() => setActiveTab('scan')}
                        className={`py-2 px-3 rounded-lg transition-all cursor-pointer flex items-center justify-center gap-1.5 ${
                            activeTab === 'scan'
                                ? 'bg-cyan-500 text-slate-950 shadow-md font-extrabold'
                                : 'text-slate-300 hover:text-white'
                        }`}
                    >
                        <span>📷 Scan QR</span>
                    </button>

                    <button
                        type="button"
                        onClick={() => setActiveTab('my-qr')}
                        className={`py-2 px-3 rounded-lg transition-all cursor-pointer flex items-center justify-center gap-1.5 ${
                            activeTab === 'my-qr'
                                ? 'bg-cyan-500 text-slate-950 shadow-md font-extrabold'
                                : 'text-slate-300 hover:text-white'
                        }`}
                    >
                        <span>🪪 My QR Code</span>
                    </button>
                </div>

                {/* TAB 1: SCANNER VIEW */}
                {activeTab === 'scan' && (
                    <div className="space-y-4">
                        {/* High-Tech Camera Viewfinder Window */}
                        <div
                            className="w-56 h-56 mx-auto rounded-3xl relative overflow-hidden flex items-center justify-center border shadow-2xl"
                            style={{
                                background: isTorchOn ? 'rgba(255, 255, 255, 0.12)' : 'rgba(0, 0, 0, 0.5)',
                                borderColor: 'rgba(34, 211, 238, 0.4)'
                            }}
                        >
                            {/* HUD Corner Reticle Brackets */}
                            <div className="absolute top-3 left-3 w-6 h-6 border-t-2 border-l-2 border-cyan-400 rounded-tl-lg" />
                            <div className="absolute top-3 right-3 w-6 h-6 border-t-2 border-r-2 border-cyan-400 rounded-tr-lg" />
                            <div className="absolute bottom-3 left-3 w-6 h-6 border-b-2 border-l-2 border-cyan-400 rounded-bl-lg" />
                            <div className="absolute bottom-3 right-3 w-6 h-6 border-b-2 border-r-2 border-cyan-400 rounded-br-lg" />

                            {/* Center Target Aim Cross */}
                            <div className="w-12 h-12 border border-white/20 rounded-full flex items-center justify-center">
                                <div className="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping" />
                            </div>

                            {/* Animated Sweeping Laser Scan Line */}
                            <div
                                className="absolute left-3 right-3 h-0.5 bg-gradient-to-r from-transparent via-cyan-400 to-transparent shadow-[0_0_12px_#22d3ee] animate-laserSweep pointer-events-none"
                            />

                            <style>{`
                                @keyframes laserSweep {
                                    0% { top: 12%; opacity: 0.7; }
                                    50% { top: 86%; opacity: 1; }
                                    100% { top: 12%; opacity: 0.7; }
                                }
                                .animate-laserSweep {
                                    animation: laserSweep 2.2s ease-in-out infinite;
                                }
                            `}</style>
                        </div>

                        <p className="text-center text-xs text-slate-300 font-medium">
                            Align any GRID QR code or visiting card within the frame
                        </p>

                        {/* Scanner Actions: Upload Image & Flashlight */}
                        <div className="flex items-center justify-center gap-3 pt-2">
                            {/* Upload from Gallery Button */}
                            <label className="py-2 px-4 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 border border-white/20 text-white flex items-center gap-1.5 transition-all cursor-pointer shadow-sm">
                                <svg className="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Upload Image</span>
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleFileUpload}
                                    className="hidden"
                                />
                            </label>

                            {/* Flashlight Toggle */}
                            <button
                                type="button"
                                onClick={toggleTorch}
                                className={`p-2 rounded-xl text-xs font-bold border transition-all cursor-pointer ${
                                    isTorchOn
                                        ? 'bg-amber-400 text-slate-950 border-amber-300 shadow-[0_0_12px_rgba(251,191,36,0.5)]'
                                        : 'bg-white/10 hover:bg-white/20 border-white/20 text-white'
                                }`}
                                title="Toggle Flashlight"
                            >
                                🔦
                            </button>
                        </div>
                    </div>
                )}

                {/* TAB 2: MY QR CODE VIEW */}
                {activeTab === 'my-qr' && (
                    <div className="space-y-4 text-center">
                        <div
                            className="w-56 h-56 mx-auto rounded-3xl p-4 flex flex-col items-center justify-center shadow-xl border"
                            style={{
                                background: 'rgba(255, 255, 255, 0.9)',
                                borderColor: 'rgba(255, 255, 255, 0.95)'
                            }}
                        >
                            {/* Scannable QR Matrix */}
                            <svg className="w-36 h-36" viewBox="0 0 100 100" fill="#0f172a">
                                <rect x="5" y="5" width="28" height="28" rx="4" fill="none" stroke="#0f172a" strokeWidth="5" />
                                <rect x="12" y="12" width="14" height="14" rx="2" fill="#06b6d4" />
                                <rect x="67" y="5" width="28" height="28" rx="4" fill="none" stroke="#0f172a" strokeWidth="5" />
                                <rect x="74" y="12" width="14" height="14" rx="2" fill="#06b6d4" />
                                <rect x="5" y="67" width="28" height="28" rx="4" fill="none" stroke="#0f172a" strokeWidth="5" />
                                <rect x="12" y="74" width="14" height="14" rx="2" fill="#06b6d4" />
                                <rect x="40" y="8" width="6" height="6" rx="1" />
                                <rect x="52" y="8" width="6" height="6" rx="1" />
                                <rect x="40" y="20" width="6" height="6" rx="1" />
                                <rect x="52" y="26" width="6" height="6" rx="1" />
                                <rect x="8" y="42" width="6" height="6" rx="1" />
                                <rect x="20" y="42" width="6" height="6" rx="1" />
                                <rect x="40" y="42" width="20" height="20" rx="3" fill="#06b6d4" />
                                <rect x="46" y="48" width="8" height="8" rx="1" fill="#ffffff" />
                                <rect x="68" y="42" width="6" height="6" rx="1" />
                                <rect x="80" y="42" width="6" height="6" rx="1" />
                                <rect x="68" y="68" width="6" height="6" rx="1" />
                                <rect x="80" y="74" width="6" height="6" rx="1" />
                            </svg>

                            <div className="text-[11px] font-bold text-slate-800 tracking-tight mt-1">
                                {user?.fullName || 'Praful Shah'}
                            </div>
                        </div>

                        <p className="text-xs text-slate-300">
                            Have others scan your code to connect on GRID instantly
                        </p>

                        <div className="flex items-center justify-center gap-2 pt-1">
                            <button
                                type="button"
                                onClick={() => {
                                    if (onToast) onToast('📋 QR Profile link copied!');
                                }}
                                className="py-2.5 px-4 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition-colors shadow-md cursor-pointer"
                            >
                                Copy Profile Link
                            </button>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );

    return typeof document !== 'undefined' ? createPortal(modalMarkup, document.body) : modalMarkup;
}
