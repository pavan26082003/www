import React from 'react';

/**
 * WorkspaceWelcome Component (Screen 1)
 * Matches Image 1:
 * - Slack/GRID top brand header & "Sign in to another account"
 * - "Welcome back! You can create a new workspace or choose from an existing one."
 * - Large "+ Create a new workspace" beige card CTA
 * - Terms & disclosures disclaimer
 * - "OR continue to existing workspaces" divider
 * - "Ready to launch" user email and existing workspace card with arrow launch button
 * - Privacy & Terms, Contact Us, Change region footer
 */
export default function WorkspaceWelcome({
    workspaces = [],
    currentUser = {},
    onLaunchWorkspace,
    onCreateWorkspace,
    onExitToGrid,
    onToast
}) {
    const userEmail = currentUser.email || 'umarpavan768@gmail.com';

    return (
        <div className="min-h-screen w-full bg-white text-slate-900 flex flex-col justify-between font-sans select-none overflow-y-auto">
            {/* 1. Top Header Bar */}
            <header className="w-full px-6 py-5 flex items-center justify-between max-w-7xl mx-auto flex-none">
                {/* Brand Header: GRID Team */}
                <div className="flex items-center">
                    <span className="font-extrabold text-2xl tracking-tight text-slate-900 font-sans">
                        GRID <span className="text-blue-600 font-bold">Team</span>
                    </span>
                </div>

                {/* Top Right Help / Account Link */}
                <div className="text-xs text-slate-600">
                    <span>Missing something? </span>
                    <button 
                        type="button"
                        onClick={() => onToast && onToast('Switch account requested')}
                        className="font-bold text-blue-700 hover:underline bg-transparent border-0 cursor-pointer"
                    >
                        Sign in to another account
                    </button>
                </div>
            </header>

            {/* 2. Main Center Content Container */}
            <main className="flex-1 flex flex-col items-center justify-center px-4 py-8 max-w-2xl mx-auto w-full">
                {/* Title & Subtitle */}
                <h1 className="text-4xl sm:text-[42px] font-extrabold text-slate-950 tracking-tight text-center mb-3">
                    Welcome back!
                </h1>
                <p className="text-base sm:text-lg text-slate-600 text-center mb-8 max-w-lg leading-relaxed">
                    You can create a new workspace or choose from an existing one.
                </p>

                {/* Primary CTA Card Button: "+ Create a new workspace" */}
                <button
                    type="button"
                    onClick={onCreateWorkspace}
                    className="w-full py-5 px-6 rounded-2xl bg-[#F7F4EE] hover:bg-[#F0ECE4] active:scale-[0.99] border border-[#E9E3D8] text-slate-900 font-bold text-lg flex items-center justify-center gap-3.5 transition-all shadow-2xs hover:shadow-xs group"
                >
                    <div className="w-8 h-8 rounded-lg bg-white border border-[#DDD6CA] flex items-center justify-center shadow-2xs text-slate-700 group-hover:scale-110 transition-transform">
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span>Create a new workspace</span>
                </button>

                {/* Legal & Terms Disclaimer */}
                <p className="text-[11px] sm:text-xs text-slate-500 leading-relaxed text-left sm:text-center mt-4 px-2">
                    By continuing, you agree to our{' '}
                    <a href="#terms" onClick={(e) => e.preventDefault()} className="underline hover:text-slate-800">Main Services Agreement</a>,{' '}
                    <a href="#user-terms" onClick={(e) => e.preventDefault()} className="underline hover:text-slate-800">User Terms of Service</a>, and{' '}
                    <a href="#supplemental" onClick={(e) => e.preventDefault()} className="underline hover:text-slate-800">GRID Team Supplemental Terms</a>. Additional disclosures are available in our{' '}
                    <a href="#privacy" onClick={(e) => e.preventDefault()} className="underline hover:text-slate-800">Privacy Policy</a> and{' '}
                    <a href="#cookies" onClick={(e) => e.preventDefault()} className="underline hover:text-slate-800">Cookie Policy</a>.
                </p>

                {/* "OR continue to existing workspaces" Divider */}
                <div className="w-full flex items-center my-8">
                    <div className="flex-1 border-t border-slate-200" />
                    <span className="px-4 text-xs font-medium text-slate-600 uppercase tracking-wider">
                        OR continue to existing workspaces
                    </span>
                    <div className="flex-1 border-t border-slate-200" />
                </div>

                {/* Ready to Launch Section */}
                <div className="w-full space-y-3">
                    <div className="px-1 text-left">
                        <div className="text-sm font-bold text-slate-900">Ready to launch</div>
                        <div className="text-xs text-slate-500">{userEmail}</div>
                    </div>

                    {/* Existing Workspaces List Card */}
                    {workspaces.map((ws) => (
                        <div
                            key={ws.id}
                            onClick={() => onLaunchWorkspace(ws.id)}
                            className="w-full p-4 rounded-xl bg-white border border-slate-200/90 hover:border-slate-300 shadow-2xs hover:shadow-md flex items-center justify-between cursor-pointer transition-all group"
                        >
                            <div className="flex items-center gap-3.5 min-w-0">
                                {/* Workspace Letter Tile (Dark square with letter G) */}
                                <div 
                                    className="w-12 h-12 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-xs shrink-0"
                                    style={{ backgroundColor: ws.iconBg || '#4A154B' }}
                                >
                                    {ws.icon || 'G'}
                                </div>

                                <div className="text-left min-w-0">
                                    <h2 className="text-base font-bold text-slate-900 group-hover:text-purple-900 transition-colors">
                                        {ws.name}
                                    </h2>
                                    <div className="flex items-center gap-1.5 text-xs text-slate-500">
                                        {/* Two member avatars mockup */}
                                        <div className="flex -space-x-1 shrink-0">
                                            <div className="w-4 h-4 rounded-full bg-emerald-600 text-[8px] text-white flex items-center justify-center font-bold ring-1 ring-white">
                                                P
                                            </div>
                                            <div className="w-4 h-4 rounded-full bg-amber-600 text-[8px] text-white flex items-center justify-center font-bold ring-1 ring-white">
                                                S
                                            </div>
                                        </div>
                                        <span>{ws.memberCount || 2} members</span>
                                        <span>•</span>
                                        <span>{ws.lastActive || 'Last sign-in 4 hours ago'}</span>
                                    </div>
                                </div>
                            </div>

                            {/* Black Right Arrow Button */}
                            <button
                                type="button"
                                className="w-9 h-9 rounded-full bg-transparent group-hover:bg-slate-100 flex items-center justify-center text-slate-800 transition-all shrink-0"
                                title={`Open ${ws.name}`}
                                aria-label={`Open ${ws.name}`}
                            >
                                <svg className="w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                    ))}
                </div>

                {/* Back to GRID Feed Link */}
                <div className="mt-8">
                    <button
                        type="button"
                        onClick={onExitToGrid}
                        className="text-xs text-slate-500 hover:text-slate-800 font-medium inline-flex items-center gap-1.5 transition-colors"
                    >
                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Return to GRID Main Workspace</span>
                    </button>
                </div>
            </main>

            {/* 3. Footer Links */}
            <footer className="w-full py-6 px-4 text-center text-xs text-slate-500 flex-none border-t border-slate-100">
                <div className="flex items-center justify-center gap-6 flex-wrap">
                    <a href="#privacy" onClick={(e) => e.preventDefault()} className="hover:text-slate-800 transition-colors">
                        Privacy & Terms
                    </a>
                    <a href="#contact" onClick={(e) => e.preventDefault()} className="hover:text-slate-800 transition-colors">
                        Contact Us
                    </a>
                    <div className="inline-flex items-center gap-1 cursor-pointer hover:text-slate-800 transition-colors">
                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Change region</span>
                        <svg className="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </footer>
        </div>
    );
}
