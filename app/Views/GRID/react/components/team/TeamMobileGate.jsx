import React from 'react';

/**
 * TeamMobileGate Component
 * Shown when accessing GRID Team Workspace on mobile devices (< 768px).
 * Displays a sleek, executive message informing the user that the mobile version
 * is coming soon and prompting them to use desktop for the full workspace.
 */
export default function TeamMobileGate({ onOverrideDesktop, onExitToGrid }) {
    return (
        <div className="fixed inset-0 z-50 bg-[#0F172A] text-white flex flex-col items-center justify-center p-6 select-none font-sans overflow-y-auto">
            {/* Ambient Spotlight Background */}
            <div className="absolute inset-0 bg-[radial-gradient(circle_at_top,#3F0E40_0%,transparent_65%)] opacity-70 pointer-events-none" />

            <div className="relative z-10 max-w-md w-full bg-[#1A1D21]/90 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-2xl text-center flex flex-col items-center">
                {/* Icon: Phone transitioning to Desktop */}
                <div className="w-16 h-16 rounded-2xl bg-[#3F0E40] border border-purple-500/30 flex items-center justify-center mb-6 shadow-lg shadow-purple-950/50">
                    <span className="text-3xl">💻</span>
                </div>

                {/* Coming Soon Pill */}
                <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-900/60 border border-purple-400/40 text-[11px] font-bold text-purple-200 uppercase tracking-wider mb-4">
                    <span className="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse" />
                    <span>Mobile Version Coming Soon</span>
                </div>

                <h2 className="text-2xl font-bold tracking-tight text-white mb-2">
                    Use Desktop for Team Workspace
                </h2>

                <p className="text-sm text-slate-400 leading-relaxed mb-8">
                    The GRID Team Workspace is built for multi-pane collaboration with channels, live huddles, and teammate direct messages. For the best experience, please open on your desktop or laptop.
                </p>

                {/* Primary Action: Return to Mobile Feed */}
                <div className="w-full space-y-3">
                    <button
                        type="button"
                        onClick={onExitToGrid}
                        className="w-full py-3 px-4 bg-white hover:bg-slate-100 text-slate-900 font-bold text-sm rounded-xl transition-all shadow-md active:scale-95 flex items-center justify-center gap-2"
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Return to GRID Mobile Feed</span>
                    </button>

                    {/* Secondary Action: Continue on Mobile anyway (Desktop Preview) */}
                    <button
                        type="button"
                        onClick={onOverrideDesktop}
                        className="w-full py-2.5 px-4 bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white font-medium text-xs rounded-xl transition-colors border border-white/10"
                    >
                        Preview Desktop Layout Anyway
                    </button>
                </div>

                {/* Micro Footer */}
                <div className="mt-6 pt-4 border-t border-white/5 text-[11px] text-slate-500">
                    GRID Community • Next-Gen AI Collaboration
                </div>
            </div>
        </div>
    );
}
