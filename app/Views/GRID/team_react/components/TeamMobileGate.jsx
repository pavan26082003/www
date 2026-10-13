import React from 'react';

/**
 * TeamMobileGate Component
 * Mobile responsive gate: Informs mobile users that mobile version is coming soon
 * and instructs them to open GRID Team on desktop for the complete experience.
 */
export default function TeamMobileGate({ onReturnHome }) {
    return (
        <div className="md:hidden fixed inset-0 z-50 bg-[#1A1D21] text-white flex flex-col justify-between p-6 font-sans select-none">
            {/* Top Brand */}
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-2.5">
                    <div className="w-8 h-8 rounded-lg bg-[#3F0E40] border border-purple-500/40 text-white font-black flex items-center justify-center text-sm shadow-md">
                        G
                    </div>
                    <span className="font-extrabold tracking-tight text-base text-slate-100">
                        GRID <span className="text-purple-400 font-medium">Team</span>
                    </span>
                </div>
                <span className="text-[11px] font-bold px-2 py-0.5 rounded-full bg-purple-900/60 text-purple-300 border border-purple-500/30">
                    Desktop First
                </span>
            </div>

            {/* Center Announcement Hero */}
            <div className="text-center py-8 space-y-4 max-w-sm mx-auto">
                <div className="w-20 h-20 mx-auto rounded-3xl bg-gradient-to-tr from-purple-900/60 to-purple-600/30 border border-purple-500/30 flex items-center justify-center text-4xl shadow-xl">
                    💻
                </div>

                <div className="space-y-2">
                    <h2 className="text-2xl font-extrabold text-white tracking-tight leading-snug">
                        GRID Team for Mobile is coming soon
                    </h2>
                    <p className="text-sm text-slate-300 leading-relaxed">
                        We are currently crafting a tailored mobile experience for GRID workspaces.
                        For the full collaborative feature set with live channels, huddles, and direct messaging, please open on your desktop browser.
                    </p>
                </div>

                <div className="bg-white/5 border border-white/10 rounded-xl p-3.5 text-left text-xs text-slate-300 space-y-2">
                    <div className="font-semibold text-white flex items-center gap-1.5">
                        <span className="text-emerald-400">●</span> Desktop Experience Includes:
                    </div>
                    <ul className="list-disc pl-4 space-y-1 text-slate-400">
                        <li>Multi-channel team messaging & rich composer</li>
                        <li>Live direct messages with instant replies</li>
                        <li>Dynamic floating section controls & permissions</li>
                        <li>Workspace setup wizard & teammate invites</li>
                    </ul>
                </div>
            </div>

            {/* Bottom Actions */}
            <div className="space-y-3 pt-4">
                <a
                    href="/index.php/grid"
                    className="w-full py-3 px-4 rounded-xl font-bold text-sm bg-[#3F0E40] hover:bg-[#522653] text-white flex items-center justify-center gap-2 border border-purple-500/40 transition-all text-center no-underline"
                >
                    <span>←</span> Return to GRID Home Feed
                </a>
                <p className="text-[11px] text-center text-slate-500">
                    Resize browser window &ge; 768px to preview desktop layout.
                </p>
            </div>
        </div>
    );
}
