import React, { useState } from 'react';

/**
 * WorkspaceSetupWizard Component (Screens 2, 3 & 4)
 * Matches user images 2, 3 & 4:
 * - Floating clean white setup card on dark aubergine void
 * - 3-step purple dashed progress bar
 * - Step 1: "Name your Slack workspace" (50 char counter + Next)
 * - Step 2: "What is your name?" (Your identity in the workspace)
 * - Step 3: "Invite your teammates" (email textarea, Copy Invite Link, Skip this step)
 * - Live Interactive Sidebar Preview: Updates workspace name, user name, and teammates in real-time
 */
export default function WorkspaceSetupWizard({
    initialName = '',
    onFinish,
    onCancel,
    onToast
}) {
    const [step, setStep] = useState(1); // 1 | 2 | 3
    const [workspaceName, setWorkspaceName] = useState('');
    const [yourName, setYourName] = useState(initialName || 'K Pavan Kumar');
    const [teammateEmailsText, setTeammateEmailsText] = useState('');

    const maxChars = 50;

    const handleCopyInviteLink = () => {
        const fakeInvite = `https://grid.community/invite/${(workspaceName || 'workspace').toLowerCase().replace(/\s+/g, '-')}-${Math.floor(1000 + Math.random() * 9000)}`;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(fakeInvite);
        }
        if (onToast) onToast('📋 Invite link copied to clipboard!');
    };

    const handleNext = () => {
        if (step === 1) {
            if (!workspaceName.trim()) return;
            setStep(2);
        } else if (step === 2) {
            if (!yourName.trim()) return;
            setStep(3);
        } else if (step === 3) {
            completeWizard();
        }
    };

    const completeWizard = () => {
        const emails = teammateEmailsText
            .split(/[,\n]/)
            .map(e => e.trim())
            .filter(Boolean);

        onFinish({
            workspaceName: workspaceName.trim() || 'My Workspace',
            yourName: yourName.trim() || 'K Pavan Kumar',
            teammateEmails: emails
        });
    };

    // Extract first teammate name for preview
    const firstTeammate = teammateEmailsText
        .split(/[,\n]/)
        .map(e => e.trim())
        .filter(Boolean)[0] || 'Your teammate';
    const firstTeammateName = firstTeammate.includes('@') ? firstTeammate.split('@')[0] : firstTeammate;

    return (
        <div className="min-h-screen w-full bg-[#1A1D21] text-slate-100 flex items-center justify-center p-4 sm:p-6 font-sans select-none overflow-y-auto">
            {/* Centered Floating Modal Card */}
            <div className="w-full max-w-4xl bg-white text-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-slate-200/80 flex flex-col md:flex-row min-h-[540px]">
                
                {/* Left Side: Multi-Step Interactive Form */}
                <div className="flex-1 p-8 sm:p-12 flex flex-col justify-between">
                    <div>
                        {/* Top 3-Step Progress Indicator Bar */}
                        <div className="flex items-center gap-2 mb-8">
                            <div className={`h-1.5 w-10 rounded-full transition-all ${step >= 1 ? 'bg-[#611f69]' : 'bg-slate-200'}`} />
                            <div className={`h-1.5 w-10 rounded-full transition-all ${step >= 2 ? 'bg-[#611f69]' : 'bg-slate-200'}`} />
                            <div className={`h-1.5 w-10 rounded-full transition-all ${step >= 3 ? 'bg-[#611f69]' : 'bg-slate-200'}`} />
                        </div>

                        {/* STEP 1: Name your Slack workspace */}
                        {step === 1 && (
                            <div className="space-y-4 animate-fadeIn">
                                <h1 className="text-3xl sm:text-[34px] font-extrabold text-slate-900 tracking-tight leading-tight">
                                    Name your GRID Team workspace
                                </h1>
                                <p className="text-sm sm:text-base text-slate-600 leading-relaxed max-w-md">
                                    Choose something your team will recognize like the name of your company or team. You can always update it later.
                                </p>

                                <div className="pt-4">
                                    <div className="relative max-w-md">
                                        <input
                                            type="text"
                                            maxLength={maxChars}
                                            value={workspaceName}
                                            onChange={(e) => setWorkspaceName(e.target.value)}
                                            onKeyDown={(e) => { if (e.key === 'Enter' && workspaceName.trim()) handleNext(); }}
                                            placeholder="ex. Acme Inc."
                                            autoFocus
                                            className="w-full px-4 py-3.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 text-slate-900 text-base placeholder-slate-400 font-medium outline-none transition-all pr-12"
                                        />
                                        <span className="absolute right-3.5 top-4 text-xs font-semibold text-slate-400">
                                            {maxChars - workspaceName.length}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* STEP 2: What is your name? */}
                        {step === 2 && (
                            <div className="space-y-4 animate-fadeIn">
                                <h1 className="text-3xl sm:text-[34px] font-extrabold text-slate-900 tracking-tight leading-tight">
                                    What is your name?
                                </h1>
                                <p className="text-sm sm:text-base text-slate-600 leading-relaxed max-w-md">
                                    Adding your name helps your teammates recognize and connect with you on the workspace.
                                </p>

                                <div className="pt-4">
                                    <div className="max-w-md">
                                        <input
                                            type="text"
                                            value={yourName}
                                            onChange={(e) => setYourName(e.target.value)}
                                            onKeyDown={(e) => { if (e.key === 'Enter' && yourName.trim()) handleNext(); }}
                                            placeholder="ex. K Pavan Kumar"
                                            autoFocus
                                            className="w-full px-4 py-3.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 text-slate-900 text-base placeholder-slate-400 font-medium outline-none transition-all"
                                        />
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* STEP 3: Invite your teammates */}
                        {step === 3 && (
                            <div className="space-y-4 animate-fadeIn">
                                <h1 className="text-3xl sm:text-[34px] font-extrabold text-slate-900 tracking-tight leading-tight">
                                    Invite your teammates
                                </h1>
                                <p className="text-sm sm:text-base text-slate-600 leading-relaxed max-w-md">
                                    GRID Team works better with more people. Add your core collaborators.
                                </p>

                                <div className="pt-3 max-w-md">
                                    <div className="flex items-center justify-between text-xs mb-1.5 font-bold">
                                        <span className="text-slate-700">Add teammate by email</span>
                                        <button
                                            type="button"
                                            onClick={() => onToast && onToast('Google Contacts sync initiated')}
                                            className="inline-flex items-center gap-1 text-blue-600 hover:underline font-semibold bg-transparent border-0 cursor-pointer"
                                        >
                                            <svg className="w-3.5 h-3.5" viewBox="0 0 24 24">
                                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                            </svg>
                                            <span>Add from Google Contacts</span>
                                        </button>
                                    </div>

                                    <textarea
                                        rows={3}
                                        value={teammateEmailsText}
                                        onChange={(e) => setTeammateEmailsText(e.target.value)}
                                        placeholder="Ex. ellis@gmail.com, maria@gmail.com"
                                        autoFocus
                                        className="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-4 focus:ring-blue-500/15 text-slate-900 text-sm placeholder-slate-400 font-medium outline-none resize-none transition-all"
                                    />
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Bottom Step Actions */}
                    <div className="pt-8 flex items-center gap-3 flex-wrap">
                        {step === 1 && (
                            <button
                                type="button"
                                disabled={!workspaceName.trim()}
                                onClick={handleNext}
                                className="py-3 px-8 rounded-xl font-bold text-sm bg-slate-900 text-white hover:bg-slate-800 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                            >
                                Next
                            </button>
                        )}

                        {step === 2 && (
                            <>
                                <button
                                    type="button"
                                    onClick={() => setStep(1)}
                                    className="py-3 px-5 rounded-xl font-bold text-sm bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all"
                                >
                                    Back
                                </button>
                                <button
                                    type="button"
                                    disabled={!yourName.trim()}
                                    onClick={handleNext}
                                    className="py-3 px-8 rounded-xl font-bold text-sm bg-slate-900 text-white hover:bg-slate-800 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                                >
                                    Next
                                </button>
                            </>
                        )}

                        {step === 3 && (
                            <>
                                <button
                                    type="button"
                                    onClick={completeWizard}
                                    className="py-3 px-8 rounded-xl font-bold text-sm bg-slate-900 text-white hover:bg-slate-800 transition-all"
                                >
                                    Next
                                </button>
                                <button
                                    type="button"
                                    onClick={handleCopyInviteLink}
                                    className="py-3 px-4 rounded-xl font-bold text-sm bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 inline-flex items-center gap-2 transition-all"
                                >
                                    <svg className="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    <span>Copy Invite Link</span>
                                </button>
                                <button
                                    type="button"
                                    onClick={completeWizard}
                                    className="text-xs text-slate-500 hover:text-slate-800 font-bold px-2 py-3 bg-transparent border-0 cursor-pointer transition-colors ml-auto"
                                >
                                    Skip this step
                                </button>
                            </>
                        )}

                        {/* Exit / Cancel to Welcome screen */}
                        {onCancel && (
                            <button
                                type="button"
                                onClick={onCancel}
                                className="text-xs text-slate-400 hover:text-slate-600 font-medium ml-auto"
                            >
                                Cancel setup
                            </button>
                        )}
                    </div>
                </div>

                {/* Right Side: Interactive Slack Purple Sidebar Live Preview */}
                <div className="w-full md:w-[320px] lg:w-[360px] bg-slate-100/70 border-t md:border-t-0 md:border-l border-slate-200/80 p-6 flex flex-col justify-center items-center overflow-hidden">
                    <div className="w-full max-w-[280px] bg-[#3F0E40] text-white rounded-xl shadow-xl overflow-hidden border border-purple-950 font-sans text-xs select-none">
                        {/* Preview Top Header */}
                        <div className="p-3 border-b border-purple-900/50 flex items-center justify-between">
                            <div className="font-bold text-sm truncate max-w-[170px]">
                                {workspaceName.trim() || 'Your workspace'}
                            </div>
                            <span className="text-[10px] bg-purple-900/80 px-1.5 py-0.5 rounded text-purple-200">
                                # project
                            </span>
                        </div>

                        {/* Preview Navigation Items */}
                        <div className="p-3 space-y-3">
                            <div className="space-y-1 text-purple-300 text-[11px]">
                                <div className="flex items-center gap-1.5 px-2 py-1 rounded hover:bg-purple-900/40">
                                    <span>💬</span> Unreads
                                </div>
                                <div className="flex items-center gap-1.5 px-2 py-1 rounded hover:bg-purple-900/40">
                                    <span>🧵</span> Threads
                                </div>
                            </div>

                            {/* Preview Channels */}
                            <div className="pt-1">
                                <div className="text-[10px] uppercase font-bold text-purple-300/80 px-2 mb-1 tracking-wider">
                                    Channels
                                </div>
                                <div className="space-y-0.5">
                                    <div className="flex items-center gap-1.5 px-2 py-1 rounded bg-purple-900/60 font-semibold text-white">
                                        <span>#</span> project
                                    </div>
                                    <div className="flex items-center gap-1.5 px-2 py-1 rounded text-purple-300 text-[11px]">
                                        <span>+</span> Add channels
                                    </div>
                                </div>
                            </div>

                            {/* Preview Direct Messages */}
                            <div className="pt-1">
                                <div className="text-[10px] uppercase font-bold text-purple-300/80 px-2 mb-1 tracking-wider">
                                    Direct messages
                                </div>
                                <div className="space-y-1">
                                    {/* Current User */}
                                    <div className="flex items-center gap-2 px-2 py-1 rounded text-purple-200">
                                        <span className="w-2 h-2 rounded-full bg-emerald-400 shrink-0" />
                                        <span className="truncate">{yourName.trim() || 'Your name'}</span>
                                        <span className="text-[9px] text-purple-300/60 font-medium ml-auto">you</span>
                                    </div>

                                    {/* Invited Teammates Preview */}
                                    {step === 3 && teammateEmailsText.trim() ? (
                                        <div className="flex items-center gap-2 px-2 py-1 rounded text-purple-200">
                                            <span className="w-2 h-2 rounded-full bg-slate-400 shrink-0" />
                                            <span className="truncate">{firstTeammateName}</span>
                                        </div>
                                    ) : (
                                        <div className="flex items-center gap-2 px-2 py-1 rounded text-purple-300/60">
                                            <span className="w-2 h-2 rounded-full bg-slate-500 shrink-0" />
                                            <span>Your teammate</span>
                                        </div>
                                    )}

                                    <div className="flex items-center gap-1.5 px-2 py-1 rounded text-purple-300 text-[11px]">
                                        <span>+</span> Add teammates
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Preview Footer */}
                        <div className="p-2.5 bg-purple-950/40 text-[10px] text-purple-300/60 text-center border-t border-purple-900/40">
                            Live Workspace Preview
                        </div>
                    </div>
                </div>

            </div>
        </div>
    );
}
