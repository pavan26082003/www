import React, { useState } from 'react';

/**
 * JobApplicationStatusView Component
 * Dedicated Application Tracker view matching the user's reference image:
 * - Header with total applies and application updates metrics
 * - Left rail: Filter chips, applied job cards with interview prep tools and status badges
 * - Right rail: Connected progress stepper (Contacted -> Application Viewed -> Resume Viewed -> Awaiting Action),
 *   activity on this job metrics, what worked for you match criteria, and recruiter profile.
 */
export default function JobApplicationStatusView({
    applications = [],
    onBack,
    onToast,
    onSelectJobDetail
}) {
    const [selectedAppId, setSelectedAppId] = useState(applications[0]?.id || 'app-1');
    const [filterCategory, setFilterCategory] = useState('all'); // 'all' | 'recruiter_actions' | 'quick_apply' | 'external'
    const [isMobileDetailOpen, setIsMobileDetailOpen] = useState(false);

    const selectedApp = applications.find(a => a.id === selectedAppId) || applications[0] || {};

    const filteredApplications = applications.filter(app => {
        if (filterCategory === 'recruiter_actions') {
            return app.statusType === 'resume_viewed' || app.statusType === 'interview';
        }
        if (filterCategory === 'quick_apply') {
            return app.isQuickApply;
        }
        if (filterCategory === 'external') {
            return !app.isQuickApply;
        }
        return true;
    });

    const totalApplies = applications.length >= 62 ? applications.length : 62;
    const updateCount = 13;

    return (
        <div className="space-y-4 pb-16 animate-in fade-in duration-200">
            {/* Top Navigation & Header - Hidden on mobile when viewing detail so only the lower action card is visible */}
            <div className={`g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-4 sm:p-5 shadow-sm flex-col sm:flex-row sm:items-center justify-between gap-4 ${isMobileDetailOpen ? 'hidden lg:flex' : 'flex'}`}>
                <div className="flex items-center gap-3">
                    <button
                        type="button"
                        onClick={onBack}
                        className="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:underline transition-colors cursor-pointer"
                    >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to all jobs
                    </button>
                    <span className="text-slate-300 dark:text-slate-700">|</span>
                    <h1 className="text-base sm:text-xl font-bold text-slate-900 dark:text-white">
                        Job application status
                    </h1>
                </div>

                {/* Right Counts Header */}
                <div className="flex items-center gap-6 text-slate-700 dark:text-slate-300">
                    <div className="flex items-baseline gap-2">
                        <span className="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                            {totalApplies}
                        </span>
                        <div className="text-[11px] leading-tight text-slate-500 dark:text-slate-400">
                            <div>Total</div>
                            <div>applies</div>
                        </div>
                    </div>

                    <div className="h-8 w-px bg-slate-200 dark:bg-slate-800" />

                    <div className="flex items-baseline gap-2">
                        <span className="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                            {updateCount}
                        </span>
                        <div className="text-[11px] leading-tight text-slate-500 dark:text-slate-400">
                            <div>Application</div>
                            <div>updates</div>
                        </div>
                    </div>
                </div>
            </div>

            {/* 2-Column Responsive Layout */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

                {/* LEFT COLUMN: Filter Chips & List of Applied Job Cards (Visible first on mobile, hidden when viewing detail on mobile) */}
                <div className={`space-y-3.5 ${isMobileDetailOpen ? 'hidden lg:block lg:col-span-5' : 'block lg:col-span-5'}`}>
                    {/* Filter Chips */}
                    <div className="flex items-center gap-2 overflow-x-auto scrollbar-none pb-1 text-xs">
                        <button
                            type="button"
                            onClick={() => setFilterCategory('recruiter_actions')}
                            className={`px-3 py-1.5 rounded-full font-semibold transition-all shrink-0 cursor-pointer border ${
                                filterCategory === 'recruiter_actions'
                                    ? 'bg-black text-white dark:bg-white dark:text-black border-black dark:border-white shadow-xs'
                                    : 'bg-white/70 dark:bg-slate-900/60 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-slate-100'
                            }`}
                        >
                            Recruiter Actions ({updateCount})
                        </button>

                        <button
                            type="button"
                            onClick={() => setFilterCategory('quick_apply')}
                            className={`px-3 py-1.5 rounded-full font-semibold transition-all shrink-0 cursor-pointer border ${
                                filterCategory === 'quick_apply'
                                    ? 'bg-black text-white dark:bg-white dark:text-black border-black dark:border-white shadow-xs'
                                    : 'bg-white/70 dark:bg-slate-900/60 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-slate-100'
                            }`}
                        >
                            Applies on GRID ({totalApplies - 5})
                        </button>

                        <button
                            type="button"
                            onClick={() => setFilterCategory('external')}
                            className={`px-3 py-1.5 rounded-full font-semibold transition-all shrink-0 cursor-pointer border ${
                                filterCategory === 'external'
                                    ? 'bg-black text-white dark:bg-white dark:text-black border-black dark:border-white shadow-xs'
                                    : 'bg-white/70 dark:bg-slate-900/60 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-slate-100'
                            }`}
                        >
                            Applies on External Site (5)
                        </button>
                    </div>

                    {/* Applied Jobs Card List */}
                    <div className="space-y-3">
                        {filteredApplications.map((app) => {
                            const isSelected = app.id === selectedApp?.id;

                            return (
                                <div
                                    key={app.id}
                                    onClick={() => {
                                        setSelectedAppId(app.id);
                                        setIsMobileDetailOpen(true);
                                        window.scrollTo({ top: 0, behavior: 'smooth' });
                                    }}
                                    className={`g-glass-card rounded-2xl border transition-all cursor-pointer p-4 space-y-3 ${
                                        isSelected
                                            ? 'border-blue-500 dark:border-blue-400 ring-2 ring-blue-500/20 shadow-md bg-blue-50/20 dark:bg-blue-950/20'
                                            : 'border-white/70 dark:border-slate-800/80 hover:border-slate-300 dark:hover:border-slate-700 shadow-sm'
                                    }`}
                                >
                                    <div className="flex items-start justify-between gap-3">
                                        <div className="flex-1 min-w-0">
                                            <h3 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white leading-snug line-clamp-2">
                                                {app.title}
                                            </h3>
                                            <p className="text-xs text-slate-600 dark:text-slate-400 mt-1 flex items-center gap-1.5 flex-wrap">
                                                <span className="font-semibold text-slate-800 dark:text-slate-200">{app.company}</span>
                                                {app.rating && (
                                                    <span className="inline-flex items-center gap-0.5 text-[11px] text-amber-500 font-medium">
                                                        ★ {app.rating}
                                                        {app.reviewCount && <span className="text-slate-400">({app.reviewCount})</span>}
                                                    </span>
                                                )}
                                            </p>
                                        </div>

                                        {app.logoText && (
                                            <div
                                                className="w-8 h-8 rounded-lg flex items-center justify-center font-bold shrink-0 text-xs shadow-2xs"
                                                style={{
                                                    backgroundColor: app.logoBg || '#fee2e2',
                                                    color: app.logoTextColor || '#ef4444'
                                                }}
                                            >
                                                {app.logoText}
                                            </div>
                                        )}
                                    </div>

                                    {/* Status Badge & Recruiter Activity */}
                                    <div className="flex items-center justify-between gap-2 text-xs flex-wrap pt-1">
                                        <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-medium bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20 text-[11px]">
                                            <svg className="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            {app.status}
                                        </span>

                                        <span className="text-[11px] text-slate-400 dark:text-slate-500">
                                            {app.recruiterActive}
                                        </span>
                                    </div>

                                    {/* Interview Prep Action Bar & View Progress link */}
                                    <div className="pt-2 border-t border-slate-200/60 dark:border-slate-800/80 flex items-center justify-between text-xs">
                                        <span className="text-blue-600 dark:text-blue-400 font-semibold text-[11px] flex items-center gap-1">
                                            <span>Track progress</span>
                                            <span>→</span>
                                        </span>
                                        <div className="flex items-center gap-2">
                                            <button
                                                type="button"
                                                onClick={(e) => {
                                                    e.stopPropagation();
                                                    onToast && onToast(`Starting AI Mock Interview for ${app.title} 🎙️`);
                                                }}
                                                className="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:underline px-2 py-0.5 rounded-md hover:bg-blue-50 dark:hover:bg-blue-950/40 transition-colors"
                                            >
                                                <span>🎙️</span> Mock
                                            </button>
                                            <button
                                                type="button"
                                                onClick={(e) => {
                                                    e.stopPropagation();
                                                    onToast && onToast(`Opening curated Q&A prep guide for ${app.company} 📖`);
                                                }}
                                                className="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:underline px-2 py-0.5 rounded-md hover:bg-blue-50 dark:hover:bg-blue-950/40 transition-colors"
                                            >
                                                <span>📖</span> Q&amp;A
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>

                {/* RIGHT COLUMN: Application Status Stepper & Detailed Tracker (7 cols on desktop, conditionally visible on mobile) */}
                <div className={`space-y-4 ${isMobileDetailOpen ? 'block lg:col-span-7' : 'hidden lg:block lg:col-span-7'}`}>
                    {/* Mobile Back Header Bar matching Reference Image 2 */}
                    <div className="lg:hidden flex items-center justify-between p-3.5 rounded-2xl g-glass-card border border-white/70 dark:border-slate-800/80 shadow-xs">
                        <button
                            type="button"
                            onClick={() => {
                                setIsMobileDetailOpen(false);
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            }}
                            className="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>Back to applied jobs</span>
                        </button>
                        <button
                            type="button"
                            onClick={() => {
                                if (onSelectJobDetail) {
                                    onSelectJobDetail(selectedApp);
                                } else {
                                    onToast && onToast(`Viewing similar jobs for ${selectedApp.title}`);
                                }
                            }}
                            className="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer"
                        >
                            <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>Similar jobs</span>
                        </button>
                    </div>
                    {selectedApp ? (
                        <>
                            {/* Selected Job Header Card */}
                            <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-5 sm:p-6 shadow-sm space-y-2">
                                <div className="flex items-start justify-between gap-3">
                                    <div className="flex-1 min-w-0">
                                        <h2 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                            {selectedApp.title}
                                        </h2>
                                        <p className="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1 flex items-center gap-2">
                                            <span className="font-semibold text-slate-800 dark:text-slate-200">{selectedApp.company}</span>
                                            {selectedApp.rating && (
                                                <span className="text-amber-500 font-medium text-xs">
                                                    ★ {selectedApp.rating} ({selectedApp.reviewCount} Reviews)
                                                </span>
                                            )}
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <button
                                        type="button"
                                        onClick={() => {
                                            if (onSelectJobDetail) {
                                                onSelectJobDetail(selectedApp);
                                            } else {
                                                onToast && onToast(`Viewing details for ${selectedApp.title}`);
                                            }
                                        }}
                                        className="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer"
                                    >
                                        View similar jobs / details ↗
                                    </button>
                                </div>
                            </div>

                            {/* Application Status Progress Stepper (Quick Apply Milestone Tracker) */}
                            <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-5 sm:p-6 shadow-sm space-y-5">
                                <div className="flex items-center justify-between">
                                    <h3 className="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                                        Application status
                                    </h3>
                                    {selectedApp.isQuickApply ? (
                                        <span className="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-800 flex items-center gap-1">
                                            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                                            Quick Apply Active
                                        </span>
                                    ) : (
                                        <span className="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-300/60 dark:border-amber-800">
                                            External Site Apply
                                        </span>
                                    )}
                                </div>

                                {selectedApp.isQuickApply ? (
                                    /* Connected Timeline Stepper matching reference design */
                                    <div className="relative pt-2 pb-4">
                                        <div className="flex items-start justify-between relative">
                                            {(selectedApp.steps || []).map((step, idx, arr) => {
                                                const isLast = idx === arr.length - 1;
                                                const isDone = step.completed;
                                                const isCurrent = step.isCurrent;

                                                return (
                                                    <div key={step.id || idx} className="flex-1 relative flex flex-col items-center text-center">
                                                        {/* Connecting Line to next step */}
                                                        {!isLast && (
                                                            <div
                                                                className={`absolute top-3 left-1/2 w-full h-1 -translate-y-1/2 z-0 ${
                                                                    isDone && (arr[idx + 1]?.completed || arr[idx + 1]?.isCurrent)
                                                                        ? 'bg-emerald-500'
                                                                        : 'bg-slate-200 dark:bg-slate-700'
                                                                }`}
                                                            />
                                                        )}

                                                        {/* Step Node Icon */}
                                                        <div
                                                            className={`relative z-10 w-6 h-6 rounded-full flex items-center justify-center transition-all ${
                                                                isDone
                                                                    ? 'bg-emerald-500 text-white shadow-xs'
                                                                    : isCurrent
                                                                    ? 'bg-white dark:bg-slate-800 border-2 border-emerald-500 text-emerald-600 ring-4 ring-emerald-500/20'
                                                                    : 'bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-600 text-slate-400'
                                                            }`}
                                                        >
                                                            {isDone ? (
                                                                <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            ) : (
                                                                <div className={`w-2.5 h-2.5 rounded-full ${isCurrent ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'}`} />
                                                            )}
                                                        </div>

                                                        {/* Step Label & Date */}
                                                        <div className="mt-2.5 px-1 max-w-[120px]">
                                                            <h4 className="text-[11px] sm:text-xs font-bold text-slate-900 dark:text-white leading-tight">
                                                                {step.label}
                                                            </h4>
                                                            {step.date && (
                                                                <span className="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 block">
                                                                    {step.date}
                                                                </span>
                                                            )}
                                                        </div>
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </div>
                                ) : (
                                    /* Non-Quick Apply / External Application notice */
                                    <div className="p-4 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/50 text-xs space-y-1.5">
                                        <p className="font-semibold text-slate-900 dark:text-white flex items-center gap-1.5">
                                            <span>🌐</span> Applied on External Company Portal
                                        </p>
                                        <p className="text-slate-600 dark:text-slate-400">
                                            This application was directed to {selectedApp.company}'s external careers system on {selectedApp.appliedDate || '3 weeks ago'}. Real-time progress milestones are exclusively available for <strong>Quick Apply</strong> roles.
                                        </p>
                                    </div>
                                )}
                            </div>

                            {/* Activity on this job Card */}
                            <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-5 sm:p-6 shadow-sm space-y-4">
                                <h3 className="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                                    Activity on this job
                                </h3>

                                <div className="grid grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/50">
                                    <div>
                                        <span className="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">
                                            {selectedApp.activity?.totalApplications?.toLocaleString() || '13,059'}
                                        </span>
                                        <p className="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                            Total applications
                                        </p>
                                    </div>

                                    <div className="border-l border-slate-200 dark:border-slate-700 pl-4">
                                        <span className="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">
                                            {selectedApp.activity?.viewedByRecruiter?.toLocaleString() || '13,049'}
                                        </span>
                                        <p className="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                            Applications viewed by recruiter
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* What may work for you? Card */}
                            <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-5 sm:p-6 shadow-sm space-y-3">
                                <div>
                                    <h3 className="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                                        What may work for you?
                                    </h3>
                                    <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        Following criteria suggests how well you match with the job.
                                    </p>
                                </div>

                                <div className="space-y-2 pt-1 text-xs sm:text-sm">
                                    {(selectedApp.matchCriteria || []).map((crit, idx) => (
                                        <div key={idx} className="flex items-center gap-2.5">
                                            {crit.match ? (
                                                <div className="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold">
                                                    ✓
                                                </div>
                                            ) : (
                                                <div className="w-4 h-4 rounded-full border border-slate-300 dark:border-slate-600 text-slate-400 flex items-center justify-center text-[10px] font-bold">
                                                    ✕
                                                </div>
                                            )}
                                            <span className={crit.match ? 'text-slate-900 dark:text-white font-medium' : 'text-slate-500 dark:text-slate-400'}>
                                                {crit.label}
                                            </span>
                                        </div>
                                    ))}
                                </div>

                                <div className="pt-2">
                                    <button
                                        type="button"
                                        onClick={() => onToast && onToast('Opening criteria breakdown...')}
                                        className="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer"
                                    >
                                        Detail View
                                    </button>
                                </div>
                            </div>

                            {/* Recruiter Profile Card */}
                            <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-4 sm:p-5 shadow-sm flex items-center gap-3.5">
                                <div className="w-11 h-11 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-300 shrink-0 text-base">
                                    👤
                                </div>
                                <div className="flex-1 min-w-0">
                                    <h4 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate">
                                        {selectedApp.recruiter?.name || 'HR Recruiter'}
                                    </h4>
                                    <p className="text-xs text-slate-600 dark:text-slate-400 truncate">
                                        {selectedApp.recruiter?.role || `HR at ${selectedApp.company}`}
                                    </p>
                                    <p className="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                                        {selectedApp.recruiter?.activity || selectedApp.recruiterActive}
                                    </p>
                                </div>
                            </div>
                        </>
                    ) : (
                        <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-8 text-center text-slate-500 dark:text-slate-400">
                            Select an applied job from the left to view its tracking milestones.
                        </div>
                    )}
                </div>

            </div>
        </div>
    );
}
