import React, { useState } from 'react';
import JobCardItem from './JobCardItem.jsx';

/**
 * JobsPreferencesSection Component
 * Division 1: "Jobs based on your preferences" matching user screenshot.
 */
export default function JobsPreferencesSection({
    preferences,
    jobs = [],
    savedJobIds = [],
    appliedJobIds = [],
    onSave,
    onDismiss,
    onSelect,
    onEditPreferences,
    onToast
}) {
    const [isExpanded, setIsExpanded] = useState(false);

    const displayedJobs = isExpanded ? jobs : jobs.slice(0, 3);
    const subtitleText = `${(preferences?.roles || []).join(' • ')} • ${preferences?.locationType || 'Hybrid'} • ${preferences?.location || 'Secunderabad'}`;

    return (
        <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm overflow-hidden">
            {/* Header with Title & Preferences Chips */}
            <div className="p-4 sm:p-5 border-b border-slate-200/60 dark:border-slate-800/80 flex items-start justify-between gap-3">
                <div>
                    <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                        Jobs based on your preferences
                    </h3>
                    <p className="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 sm:line-clamp-1 leading-relaxed">
                        {subtitleText}
                    </p>
                </div>

                {/* Edit Preferences Pencil Button */}
                <button
                    type="button"
                    onClick={() => onEditPreferences && onEditPreferences()}
                    className="p-2 rounded-full text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shrink-0 cursor-pointer"
                    title="Edit job preferences"
                >
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </button>
            </div>

            {/* List of Jobs */}
            <div className="divide-y divide-slate-100 dark:divide-slate-800">
                {displayedJobs.map((job) => (
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

            {/* Footer "Show all ->" Action */}
            {jobs.length > 3 && (
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
