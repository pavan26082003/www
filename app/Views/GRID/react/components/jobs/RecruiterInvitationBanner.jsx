import React, { useState } from 'react';

/**
 * RecruiterInvitationBanner Component
 * Pending Recruiter & Team Invitations card from Stitch specification.
 */
export default function RecruiterInvitationBanner({ onToast }) {
    const [invitationState, setInvitationState] = useState('visible'); // 'visible' | 'accepted' | 'ignored'

    if (invitationState !== 'visible') return null;

    const handleAccept = () => {
        setInvitationState('accepted');
        onToast && onToast('Accepted invitation from Kavya Sundaram! 🎉');
    };

    const handleIgnore = () => {
        setInvitationState('ignored');
        onToast && onToast('Invitation dismissed.');
    };

    return (
        <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm p-4 sm:p-5">
            <div className="flex items-center justify-between pb-2 mb-3 border-b border-slate-200/60 dark:border-slate-800/80">
                <div className="flex items-center gap-2">
                    <h2 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                        Pending Recruiter &amp; Team Invitations
                    </h2>
                    <span className="w-4 h-4 rounded-full bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 font-bold text-[10px] flex items-center justify-center">
                        1
                    </span>
                </div>
                <button
                    type="button"
                    onClick={handleIgnore}
                    className="text-xs text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition-colors cursor-pointer"
                >
                    Clear all
                </button>
            </div>

            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3.5">
                <div className="flex items-start gap-3">
                    <div className="w-10 h-10 rounded-full bg-amber-700 text-white flex items-center justify-center font-extrabold text-xs shrink-0 ring-2 ring-amber-300 dark:ring-amber-800">
                        KS
                    </div>
                    <div>
                        <div className="flex items-center gap-1.5 flex-wrap">
                            <h3 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                                Kavya Sundaram
                            </h3>
                            <span className="text-[10px] text-slate-500 dark:text-slate-400 font-medium">
                                • 1st Mutual
                            </span>
                        </div>
                        <p className="text-xs text-slate-600 dark:text-slate-300 mt-0.5 leading-tight">
                            Staff AI Research Engineer at Applied Labs • Former CMU
                        </p>
                        <p className="text-[11px] text-slate-400 dark:text-slate-500 mt-1 flex items-center gap-1">
                            <span>👥 Elena Rostova and 14 other mutual connections</span>
                        </p>
                    </div>
                </div>

                {/* Accept / Ignore Actions */}
                <div className="flex items-center gap-2 w-full sm:w-auto shrink-0 justify-end">
                    <button
                        type="button"
                        onClick={handleIgnore}
                        className="px-4 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors cursor-pointer"
                    >
                        Ignore
                    </button>
                    <button
                        type="button"
                        onClick={handleAccept}
                        className="px-4 py-1.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-full transition-colors shadow-xs cursor-pointer flex items-center gap-1"
                    >
                        <span>✓ Accept</span>
                    </button>
                </div>
            </div>
        </div>
    );
}
