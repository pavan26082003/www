import React from 'react';
import VerifiedBadge from '../ui/VerifiedBadge.jsx';

/**
 * JobCardItem Component
 * Renders a single LinkedIn-style job listing matching the user's screenshot.
 */
export default function JobCardItem({
    job,
    isSaved = false,
    isApplied = false,
    onSave,
    onDismiss,
    onSelect,
    onEasyApply
}) {
    if (!job) return null;

    return (
        <div className="group relative flex items-start justify-between p-3.5 sm:p-4 hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors border-b border-slate-200/60 dark:border-slate-800/80 last:border-b-0">
            <div
                className="flex items-start gap-3 sm:gap-4 flex-1 cursor-pointer pr-3"
                onClick={() => onSelect && onSelect(job)}
            >
                {/* Company Logo / Avatar */}
                <div
                    className="w-12 h-12 rounded-lg flex items-center justify-center font-bold text-white shadow-xs shrink-0 text-base"
                    style={{ backgroundColor: job.logoBg || '#2563eb' }}
                >
                    {job.logoIcon === 'apple' ? (
                        <svg className="w-6 h-6 fill-current" viewBox="0 0 170 170">
                            <path d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.19-2.12-9.97-3.17-14.34-3.17-4.58 0-9.49 1.05-14.75 3.17-5.26 2.13-9.5 3.24-12.74 3.35-4.35.13-9.16-1.9-14.42-6.08-3.7-3.04-7.58-7.77-11.64-14.19-5.99-9.46-10.77-20.15-14.34-32.08-3.57-11.93-5.35-23.08-5.35-33.45 0-14.36 3.69-26.11 11.08-35.25 7.39-9.14 16.64-13.82 27.76-14.05 4.57 0 9.79 1.25 15.66 3.75 5.87 2.5 9.79 3.81 11.76 3.93 1.52-.24 5.67-1.66 12.44-4.25 6.77-2.6 12.44-3.82 17.01-3.67 12.94.75 23.44 5.37 31.5 13.86-11.3 6.85-16.85 16.42-16.64 28.71.22 9.79 3.94 17.95 11.16 24.48 7.23 6.53 15.77 10.23 25.64 11.1-2.28 6.96-5.01 14.14-8.18 21.53zM119.22 33.74c0-7.39 2.62-14.41 7.86-21.05 5.24-6.64 11.64-11.23 19.2-13.77-.22 1.52-.38 2.83-.49 3.93-.43 4.24-1.74 8.7-3.93 13.38-2.19 4.68-5.27 8.7-9.24 12.06-4.24 3.7-8.7 6.1-13.38 7.21l-.02-1.76z" />
                        </svg>
                    ) : (
                        <span>{job.logoText || job.company.slice(0, 2).toUpperCase()}</span>
                    )}
                </div>

                {/* Main Job Meta Details */}
                <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-1.5 flex-wrap">
                        <h4 className="font-bold text-sm sm:text-base text-blue-600 dark:text-blue-400 hover:underline">
                            {job.title}
                        </h4>
                        {job.verifiedCompany && (
                            <VerifiedBadge size="xs" title="Verified Employer" />
                        )}
                    </div>

                    <p className="text-xs sm:text-sm text-slate-700 dark:text-slate-300 mt-0.5 font-medium">
                        {job.company} • {job.location}
                    </p>

                    {/* Alumni / Network Social Proof */}
                    {job.alumniInfo && (
                        <div className="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 mt-1">
                            <span className="text-sm">👥</span>
                            <span className="truncate">{job.alumniInfo}</span>
                        </div>
                    )}

                    {/* Salary (if applicable) */}
                    {job.salary && (
                        <p className="text-xs font-semibold text-emerald-600 dark:text-emerald-400 mt-1">
                            {job.salary}
                        </p>
                    )}

                    {/* Bottom Metadata Tags */}
                    <div className="flex items-center gap-2 mt-2 flex-wrap text-xs text-slate-500 dark:text-slate-400">
                        <span>{job.postedTime}</span>
                        {job.isPromoted && (
                            <>
                                <span>•</span>
                                <span className="font-medium text-slate-400">Promoted</span>
                            </>
                        )}
                        {job.isEasyApply && (
                            <>
                                <span>•</span>
                                <span className="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 font-bold">
                                    <svg className="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                    </svg>
                                    Easy Apply
                                </span>
                            </>
                        )}
                        {isApplied && (
                            <>
                                <span>•</span>
                                <span className="text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full">
                                    Applied ✓
                                </span>
                            </>
                        )}
                    </div>
                </div>
            </div>

            {/* Right Action Icons: Save & Dismiss */}
            <div className="flex items-center gap-1 shrink-0">
                {/* Save Bookmark Button */}
                <button
                    type="button"
                    onClick={(e) => {
                        e.stopPropagation();
                        onSave && onSave(job.id);
                    }}
                    className={`p-1.5 rounded-full hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors ${
                        isSaved ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200'
                    }`}
                    title={isSaved ? 'Saved to tracker' : 'Save job'}
                >
                    <svg className="w-5 h-5" fill={isSaved ? 'currentColor' : 'none'} stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                </button>

                {/* Dismiss Job 'X' Button */}
                <button
                    type="button"
                    onClick={(e) => {
                        e.stopPropagation();
                        onDismiss && onDismiss(job.id);
                    }}
                    className="p-1.5 rounded-full text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                    title="Dismiss recommendation"
                >
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    );
}
