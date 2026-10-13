import React, { useState } from 'react';
import JobCardItem from './JobCardItem.jsx';

/**
 * JobsProfileMatchSection Component
 * Division 2: "Jobs that match your profile" with integrated Top Applicant Spotlight.
 */
export default function JobsProfileMatchSection({
    jobs = [],
    userProfile,
    savedJobIds = [],
    appliedJobIds = [],
    onSave,
    onDismiss,
    onSelect,
    onToast
}) {
    const [isExpanded, setIsExpanded] = useState(false);

    const firstBatch = jobs.slice(0, 2);
    const secondBatch = jobs.slice(2);
    const displayedSecondBatch = isExpanded ? secondBatch : secondBatch.slice(0, 2);

    return (
        <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm overflow-hidden">
            {/* Header */}
            <div className="p-4 sm:p-5 border-b border-slate-200/60 dark:border-slate-800/80">
                <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                    Jobs that match your profile
                </h3>
                <p className="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Based on your profile and search history
                </p>
            </div>

            {/* First Batch of Jobs */}
            <div className="divide-y divide-slate-100 dark:divide-slate-800">
                {firstBatch.map((job) => (
                    <JobCardItem
                        key={job.id}
                        job={job}
                        isSaved={savedJobIds.includes(job.id)}
                        isApplied={appliedJobIds.includes(job.id)}
                        onSave={onSave}
                        onDismiss={onDismiss}
                        onSelect={onSelect}
                    />
                ))}
            </div>

            {/* INLINE TOP APPLICANT SPOTLIGHT CARD (Exact match to user screenshot) */}
            <div className="p-4 bg-gradient-to-r from-amber-500/10 via-orange-500/5 to-transparent border-y border-amber-500/20 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3.5 overflow-hidden">
                <div className="flex items-center gap-3 min-w-0 flex-1">
                    <img
                        src={userProfile?.avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=128&q=80'}
                        alt={userProfile?.name || 'User'}
                        className="w-11 h-11 rounded-full object-cover ring-2 ring-amber-500/60 shadow-xs shrink-0"
                    />
                    <div className="min-w-0 flex-1">
                        <div className="flex items-center gap-1.5 text-[10px] font-extrabold text-amber-700 dark:text-amber-400 uppercase tracking-wider">
                            <span>Top Applicant</span>
                            <span>★</span>
                        </div>
                        <h4 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white mt-0.5 truncate sm:whitespace-normal">
                            See the full list of jobs where you'd be a top applicant
                        </h4>
                        <p className="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            Cancel anytime. First 14 days free.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    onClick={() => onToast && onToast('GRID Career Premium: 14-day free trial activated! 🌟')}
                    className="w-full sm:w-auto px-4 py-2 rounded-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs shadow-xs transition-all active:scale-98 cursor-pointer shrink-0 text-center whitespace-nowrap"
                >
                    Subscribe to Premium
                </button>
            </div>

            {/* Second Batch of Jobs */}
            <div className="divide-y divide-slate-100 dark:divide-slate-800">
                {displayedSecondBatch.map((job) => (
                    <JobCardItem
                        key={job.id}
                        job={job}
                        isSaved={savedJobIds.includes(job.id)}
                        isApplied={appliedJobIds.includes(job.id)}
                        onSave={onSave}
                        onDismiss={onDismiss}
                        onSelect={onSelect}
                    />
                ))}
            </div>

            {/* Footer "Show all ->" */}
            {secondBatch.length > 2 && (
                <div className="p-3 text-center border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                    <button
                        type="button"
                        onClick={() => setIsExpanded(!isExpanded)}
                        className="text-xs sm:text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 inline-flex items-center gap-1.5 transition-colors cursor-pointer"
                    >
                        <span>{isExpanded ? 'Show less' : 'Show all'}</span>
                        <svg className={`w-4 h-4 transition-transform ${isExpanded ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                </div>
            )}
        </div>
    );
}
