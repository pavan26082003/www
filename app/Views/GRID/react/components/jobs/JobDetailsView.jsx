import React, { useState } from 'react';
import VerifiedBadge from '../ui/VerifiedBadge.jsx';
import MoreJobsFeed from './MoreJobsFeed.jsx';

/**
 * JobDetailsView Component
 * Dedicated full job details view matching the GRID Home & Network theme & aesthetics:
 * - Uses g-glass-card with frosted glass translucency and ambient glow
 * - High-contrast obsidian dark and executive titanium slate color harmony
 * - Clean responsive 2-column grid layout (8 cols main + 4 cols sidebar)
 * - Includes "More jobs for you" component at the bottom of the main column
 */
export default function JobDetailsView({
    jobDetail,
    isSaved = false,
    isApplied = false,
    onBack,
    onSave,
    onApply,
    moreJobs = [],
    savedJobIds = [],
    appliedJobIds = [],
    onDismiss,
    onSelect,
    onToast
}) {
    const [sendJobAlertActive, setSendJobAlertActive] = useState(false);
    const [isDescriptionExpanded, setIsDescriptionExpanded] = useState(false);

    const {
        title = 'Senior Python Full Stack Developer',
        company = 'Novatech Global Solutions',
        rating = 4.4,
        reviewCount = 148,
        experience = '3 - 6 years',
        salary = '₹14,00,000 - 22,00,000 P.A.',
        location = 'Hyderabad (HITEC City / Madhapur) [Hybrid]',
        postedTime = '3 days ago',
        openings = 4,
        applicants = '92+',
        logoBg = '#3b82f6',
        logoText = 'NT',
        verifiedCompany = true,
        highlights = [],
        jobMatchScore = [],
        description = {},
        aboutCompany = {},
        imposterWarning = '',
        similarJobs = [],
        sidebar = {}
    } = jobDetail || {};

    const handleSendAlertToggle = () => {
        setSendJobAlertActive(!sendJobAlertActive);
        onToast && onToast(!sendJobAlertActive ? 'Job alert activated! You will receive matching Python jobs via email.' : 'Job alert paused.');
    };

    return (
        <div className="space-y-5 pb-16 animate-in fade-in duration-200">
            {/* Top Navigation & Breadcrumb */}
            <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-3.5 sm:p-4 shadow-sm flex items-center justify-between gap-3">
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
                <div className="flex items-center gap-2">
                    <span className="hidden sm:inline-block text-xs text-slate-500 dark:text-slate-400">
                        Viewing: <strong className="text-slate-700 dark:text-slate-200">{title}</strong>
                    </span>
                    {isApplied && (
                        <span className="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-800/80 rounded-full">
                            ✓ Applied
                        </span>
                    )}
                </div>
            </div>

            {/* 2-Column Responsive Grid matching Reference layout */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                
                {/* LEFT MAIN COLUMN: 8 Cols */}
                <div className="lg:col-span-8 space-y-4">
                    
                    {/* 1. TOP HEADER CARD */}
                    <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-5 sm:p-6 shadow-sm relative overflow-hidden">
                        <div className="flex items-start justify-between gap-4">
                            <div className="flex-1 min-w-0">
                                <h1 className="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white leading-tight">
                                    {title}
                                </h1>
                                <div className="flex items-center gap-2 mt-2 flex-wrap">
                                    <span className="font-semibold text-slate-700 dark:text-slate-200 text-sm sm:text-base">
                                        {company}
                                    </span>
                                    {verifiedCompany && <VerifiedBadge size="xs" title="Verified Employer" />}
                                    <div className="flex items-center gap-1 text-xs font-medium text-amber-500 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-md border border-amber-200/60 dark:border-amber-900/60">
                                        <span>★</span>
                                        <span className="font-bold text-slate-800 dark:text-slate-200">{rating}</span>
                                        <span className="text-slate-500 dark:text-slate-400">({reviewCount} Reviews)</span>
                                    </div>
                                </div>
                            </div>

                            {/* Top Right Company Logo Badge */}
                            <div
                                className="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center font-bold text-white shadow-sm shrink-0 text-xl"
                                style={{ backgroundColor: logoBg }}
                            >
                                {logoText}
                            </div>
                        </div>

                        {/* Key Specs Row (Experience, Salary, Location) */}
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 my-4 py-3.5 border-y border-slate-200/60 dark:border-slate-800/80 text-xs sm:text-sm text-slate-700 dark:text-slate-300">
                            <div className="flex items-center gap-2">
                                <span className="text-slate-400 text-base">💼</span>
                                <div>
                                    <span className="text-slate-400 block text-[11px]">Experience</span>
                                    <span className="font-semibold">{experience}</span>
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                <span className="text-slate-400 text-base">💰</span>
                                <div>
                                    <span className="text-slate-400 block text-[11px]">Salary</span>
                                    <span className="font-semibold text-slate-900 dark:text-white">{salary}</span>
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                <span className="text-slate-400 text-base">📍</span>
                                <div className="truncate">
                                    <span className="text-slate-400 block text-[11px]">Location</span>
                                    <span className="font-semibold truncate block" title={location}>{location}</span>
                                </div>
                            </div>
                        </div>

                        {/* Send me jobs like this alert */}
                        <div className="flex items-center justify-between py-1 text-xs">
                            <button
                                type="button"
                                onClick={handleSendAlertToggle}
                                className={`inline-flex items-center gap-1.5 font-medium transition-colors cursor-pointer ${
                                    sendJobAlertActive
                                        ? 'text-emerald-600 dark:text-emerald-400'
                                        : 'text-blue-600 dark:text-blue-400 hover:underline'
                                }`}
                            >
                                <span>{sendJobAlertActive ? '🔔' : '🔕'}</span>
                                {sendJobAlertActive ? 'Job alert active for this role' : 'Send me jobs like this'}
                            </button>
                        </div>

                        {/* Bottom Actions Row */}
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-4 pt-3.5 border-t border-slate-200/60 dark:border-slate-800/80">
                            <div className="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                                <span>Posted: <strong className="text-slate-700 dark:text-slate-300">{postedTime}</strong></span>
                                <span>•</span>
                                <span>Openings: <strong className="text-slate-700 dark:text-slate-300">{openings}</strong></span>
                                <span>•</span>
                                <span>Applicants: <strong className="text-slate-700 dark:text-slate-300">{applicants}</strong></span>
                            </div>

                            <div className="flex items-center gap-2.5">
                                <button
                                    type="button"
                                    onClick={() => onSave && onSave(jobDetail.id)}
                                    className={`px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl border transition-all cursor-pointer inline-flex items-center gap-1.5 ${
                                        isSaved
                                            ? 'bg-blue-50/80 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800'
                                            : 'bg-white/80 dark:bg-slate-800/80 text-slate-700 dark:text-slate-200 border-slate-300/80 dark:border-slate-700/80 hover:bg-slate-100 dark:hover:bg-slate-700'
                                    }`}
                                >
                                    <svg className="w-4 h-4" fill={isSaved ? 'currentColor' : 'none'} stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                    </svg>
                                    {isSaved ? 'Saved' : 'Save'}
                                </button>

                                <button
                                    type="button"
                                    onClick={() => onApply && onApply(jobDetail)}
                                    disabled={isApplied}
                                    className={`px-6 py-2 text-xs sm:text-sm font-bold rounded-xl shadow-xs transition-all cursor-pointer inline-flex items-center gap-2 ${
                                        isApplied
                                            ? 'bg-emerald-600 text-white cursor-not-allowed opacity-90'
                                            : 'bg-blue-600 hover:bg-blue-700 text-white hover:shadow-md'
                                    }`}
                                >
                                    {isApplied ? (
                                        <>
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Applied
                                        </>
                                    ) : (
                                        <>
                                            <span>Apply</span>
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </>
                                    )}
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* 2. JOB HIGHLIGHTS & JOB MATCH SCORE */}
                    <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-5 sm:p-6 shadow-sm space-y-4">
                        <h2 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                            Job highlights
                        </h2>
                        
                        <ul className="space-y-2.5 text-xs sm:text-sm text-slate-700 dark:text-slate-300">
                            {highlights.map((item, idx) => (
                                <li key={idx} className="flex items-start gap-2.5">
                                    <span className="text-blue-500 font-bold shrink-0 mt-0.5">•</span>
                                    <span className="leading-relaxed">{item}</span>
                                </li>
                            ))}
                        </ul>

                        {/* Job match score chips matching ref */}
                        <div className="pt-4 border-t border-slate-200/60 dark:border-slate-800/80">
                            <h3 className="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2.5">
                                Job match score
                            </h3>
                            <div className="flex items-center gap-2 flex-wrap">
                                {jobMatchScore.map((score, idx) => (
                                    <span
                                        key={idx}
                                        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-emerald-500/10 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/25 dark:border-emerald-500/30"
                                        title={score.desc}
                                    >
                                        <svg className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {score.label}
                                    </span>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* 3. ABOUT THE JOB / JOB DESCRIPTION */}
                    <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-5 sm:p-6 shadow-sm space-y-4">
                        <div>
                            <h2 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                About the job
                            </h2>
                            <h3 className="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 mt-1 mb-2">
                                About {company}
                            </h3>
                        </div>

                        {!isDescriptionExpanded ? (
                            <div>
                                <p className="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed line-clamp-3">
                                    {description.intro}
                                </p>
                                <button
                                    type="button"
                                    onClick={() => setIsDescriptionExpanded(true)}
                                    className="text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 hover:underline cursor-pointer inline-flex items-center gap-1 mt-1.5"
                                >
                                    ... more
                                </button>
                            </div>
                        ) : (
                            <div className="space-y-5 animate-in fade-in duration-200">
                                <div>
                                    <p className="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                        {description.intro}
                                    </p>
                                    <button
                                        type="button"
                                        onClick={() => setIsDescriptionExpanded(false)}
                                        className="text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 hover:underline cursor-pointer inline-flex items-center gap-1 mt-1.5"
                                    >
                                        ... less
                                    </button>
                                </div>

                                <div>
                                    <h3 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white mb-2">
                                        Responsibilities:
                                    </h3>
                                    <ul className="space-y-2 text-xs sm:text-sm text-slate-700 dark:text-slate-300 mb-5">
                                        {(description.responsibilities || []).map((resp, idx) => (
                                            <li key={idx} className="flex items-start gap-2.5">
                                                <span className="text-slate-400 font-bold shrink-0 mt-0.5">•</span>
                                                <span className="leading-relaxed">{resp}</span>
                                            </li>
                                        ))}
                                    </ul>
                                </div>

                                {/* Structured Specification Table */}
                                <div className="border border-slate-200/70 dark:border-slate-800/80 rounded-xl overflow-hidden divide-y divide-slate-200/60 dark:divide-slate-800/80 text-xs sm:text-sm">
                                    <div className="grid grid-cols-1 sm:grid-cols-3 p-3 bg-slate-50/50 dark:bg-slate-800/40">
                                        <span className="font-semibold text-slate-500 dark:text-slate-400">Role:</span>
                                        <span className="sm:col-span-2 font-medium text-slate-900 dark:text-white">{description.role}</span>
                                    </div>
                                    <div className="grid grid-cols-1 sm:grid-cols-3 p-3">
                                        <span className="font-semibold text-slate-500 dark:text-slate-400">Industry Type:</span>
                                        <span className="sm:col-span-2 font-medium text-slate-900 dark:text-white">{description.industryType}</span>
                                    </div>
                                    <div className="grid grid-cols-1 sm:grid-cols-3 p-3 bg-slate-50/50 dark:bg-slate-800/40">
                                        <span className="font-semibold text-slate-500 dark:text-slate-400">Department:</span>
                                        <span className="sm:col-span-2 font-medium text-slate-900 dark:text-white">{description.department}</span>
                                    </div>
                                    <div className="grid grid-cols-1 sm:grid-cols-3 p-3">
                                        <span className="font-semibold text-slate-500 dark:text-slate-400">Employment Type:</span>
                                        <span className="sm:col-span-2 font-medium text-slate-900 dark:text-white">{description.employmentType}</span>
                                    </div>
                                    <div className="grid grid-cols-1 sm:grid-cols-3 p-3 bg-slate-50/50 dark:bg-slate-800/40">
                                        <span className="font-semibold text-slate-500 dark:text-slate-400">Role Category:</span>
                                        <span className="sm:col-span-2 font-medium text-slate-900 dark:text-white">{description.roleCategory}</span>
                                    </div>
                                </div>

                                {/* Education */}
                                <div className="pt-2">
                                    <h3 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white mb-2">
                                        Education
                                    </h3>
                                    <div className="space-y-1 text-xs sm:text-sm text-slate-700 dark:text-slate-300">
                                        <p><strong className="text-slate-500 dark:text-slate-400">UG:</strong> {description.education?.ug}</p>
                                        {description.education?.pg && (
                                            <p><strong className="text-slate-500 dark:text-slate-400">PG:</strong> {description.education?.pg}</p>
                                        )}
                                    </div>
                                </div>

                                {/* Key Skills Chips */}
                                <div className="pt-2">
                                    <h3 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white mb-1">
                                        Key Skills
                                    </h3>
                                    <p className="text-[11px] text-slate-500 dark:text-slate-400 mb-3">
                                        Skills highlighted with '<span className="text-amber-500 font-bold">★</span>' are preferred keyskills
                                    </p>
                                    <div className="flex items-center gap-2 flex-wrap">
                                        {(description.keySkills || []).map((skill, idx) => (
                                            <span
                                                key={idx}
                                                className={`px-3 py-1.5 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 ${
                                                    skill.isKey
                                                        ? 'bg-blue-500/10 dark:bg-blue-500/15 text-blue-700 dark:text-blue-300 border border-blue-500/25 dark:border-blue-500/30'
                                                        : 'bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60'
                                                }`}
                                            >
                                                {skill.isKey && <span className="text-amber-500">★</span>}
                                                {skill.name}
                                            </span>
                                        ))}
                                    </div>
                                </div>

                                {/* Share & Report Section */}
                                <div className="pt-4 border-t border-slate-200/60 dark:border-slate-800/80 flex items-center justify-between gap-4 flex-wrap text-xs">
                                    <div className="flex items-center gap-2">
                                        <span className="text-slate-500 dark:text-slate-400">Share this job:</span>
                                        <button
                                            type="button"
                                            onClick={() => onToast && onToast('Shared to LinkedIn!')}
                                            className="w-7 h-7 rounded-full bg-slate-100/80 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-blue-950/50 text-blue-600 flex items-center justify-center transition-colors cursor-pointer"
                                            title="Share on LinkedIn"
                                        >
                                            in
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => onToast && onToast('Shared to X!')}
                                            className="w-7 h-7 rounded-full bg-slate-100/80 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center transition-colors cursor-pointer"
                                            title="Share on X"
                                        >
                                            𝕏
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => onToast && onToast('Job link copied to clipboard!')}
                                            className="w-7 h-7 rounded-full bg-slate-100/80 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center transition-colors cursor-pointer"
                                            title="Copy Link"
                                        >
                                            🔗
                                        </button>
                                    </div>

                                    <button
                                        type="button"
                                        onClick={() => onToast && onToast('Job flagged for team review. Thank you!')}
                                        className="text-slate-400 hover:text-rose-500 dark:hover:text-rose-400 transition-colors cursor-pointer"
                                    >
                                        Report this job
                                    </button>
                                </div>
                            </div>
                        )}
                    </div>

                    {/* 4. ABOUT COMPANY */}
                    <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-5 sm:p-6 shadow-sm space-y-3">
                        <h2 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                            About company
                        </h2>
                        <p className="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                            {aboutCompany.overview}
                        </p>
                        <div className="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400 pt-1">
                            <span>Team Size: <strong className="text-slate-700 dark:text-slate-300">{aboutCompany.companySize}</strong></span>
                            <span>•</span>
                            <a
                                href={aboutCompany.website || '#'}
                                target="_blank"
                                rel="noreferrer"
                                className="text-blue-600 dark:text-blue-400 hover:underline"
                            >
                                Company Website ↗
                            </a>
                        </div>
                    </div>

                    {/* 5. BEWARE OF IMPOSTERS */}
                    <div className="bg-amber-500/10 dark:bg-amber-500/10 border border-amber-500/20 dark:border-amber-500/20 rounded-2xl p-4 sm:p-5 text-xs text-amber-900 dark:text-amber-200 flex items-start gap-3 backdrop-blur-xs">
                        <span className="text-xl shrink-0">🛡️</span>
                        <div>
                            <h4 className="font-bold text-amber-900 dark:text-amber-100 text-xs sm:text-sm mb-1">
                                Beware of imposters!
                            </h4>
                            <p className="leading-relaxed text-amber-800/90 dark:text-amber-300/90">
                                {imposterWarning}
                            </p>
                        </div>
                    </div>

                    {/* 6. MORE JOBS FOR YOU (Replaces Similar jobs as requested) */}
                    <MoreJobsFeed
                        jobs={moreJobs}
                        savedJobIds={savedJobIds}
                        appliedJobIds={appliedJobIds}
                        onSave={onSave}
                        onDismiss={onDismiss}
                        onSelect={onSelect}
                        onToast={onToast}
                    />

                </div>

                {/* RIGHT SIDEBAR: 4 Cols */}
                <div className="lg:col-span-4 space-y-4">

                    {/* 1. JOBS YOU MIGHT BE INTERESTED IN */}
                    <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-4 sm:p-5 shadow-sm space-y-3.5">
                        <div className="flex items-center justify-between">
                            <h3 className="font-bold text-sm text-slate-900 dark:text-white">
                                Jobs you might be interested in
                            </h3>
                        </div>

                        <div className="space-y-3 divide-y divide-slate-200/60 dark:divide-slate-800/80">
                            {(sidebar.interestedJobs || []).map((intJob) => (
                                <div
                                    key={intJob.id}
                                    className="pt-3 first:pt-0 flex items-start gap-3 group cursor-pointer"
                                    onClick={() => onToast && onToast(`Selected ${intJob.title}`)}
                                >
                                    <div
                                        className="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-white shrink-0 text-xs shadow-2xs"
                                        style={{ backgroundColor: intJob.logoBg }}
                                    >
                                        {intJob.logoText}
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <h4 className="font-semibold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 truncate">
                                            {intJob.title}
                                        </h4>
                                        <p className="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                            {intJob.company} • ⭐ {intJob.rating} ({intJob.reviews})
                                        </p>
                                        <p className="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 truncate">
                                            📍 {intJob.location} • {intJob.posted}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* 2. REVIEWS (AmbitionBox-style) */}
                    <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-4 sm:p-5 shadow-sm space-y-3">
                        <div className="flex items-center justify-between">
                            <h3 className="font-bold text-sm text-slate-900 dark:text-white">
                                Reviews
                            </h3>
                            <button
                                type="button"
                                onClick={() => onToast && onToast('Opening employee reviews...')}
                                className="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer"
                            >
                                View all
                            </button>
                        </div>

                        <div className="flex items-center gap-2">
                            <div className="flex items-center gap-1 text-sm font-bold text-amber-500 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-md border border-amber-200/60 dark:border-amber-900/60">
                                <span>★</span>
                                <span>{sidebar.reviews?.overallRating}</span>
                            </div>
                            <span className="text-xs text-slate-500 dark:text-slate-400">
                                {sidebar.reviews?.reviewCount} employee reviews
                            </span>
                        </div>

                        <div className="bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/50 p-3 rounded-xl space-y-2 text-xs">
                            <div>
                                <span className="font-bold text-emerald-600 dark:text-emerald-400 block text-[11px]">
                                    👍 Likes
                                </span>
                                <p className="text-slate-600 dark:text-slate-300 mt-0.5 leading-relaxed">
                                    {sidebar.reviews?.featuredReview?.likes}
                                </p>
                            </div>
                            <div>
                                <span className="font-bold text-rose-500 dark:text-rose-400 block text-[11px]">
                                    👎 Dislikes
                                </span>
                                <p className="text-slate-600 dark:text-slate-300 mt-0.5 leading-relaxed">
                                    {sidebar.reviews?.featuredReview?.dislikes}
                                </p>
                            </div>
                        </div>

                        <div className="flex items-center justify-between pt-1 text-[11px] text-slate-400">
                            <button
                                type="button"
                                onClick={() => onToast && onToast('Opening full review details...')}
                                className="text-blue-600 dark:text-blue-400 font-medium hover:underline cursor-pointer"
                            >
                                Read full review →
                            </button>
                            <span className="text-[10px] text-slate-400">Powered by AmbitionBox</span>
                        </div>
                    </div>

                    {/* 3. SALARY INSIGHTS */}
                    <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-4 sm:p-5 shadow-sm space-y-3">
                        <div className="flex items-center justify-between">
                            <h3 className="font-bold text-sm text-slate-900 dark:text-white">
                                Salary insights
                            </h3>
                        </div>

                        <div className="bg-blue-500/10 dark:bg-blue-950/30 border border-blue-500/20 dark:border-blue-900/40 p-3 rounded-xl">
                            <p className="text-xs text-slate-600 dark:text-slate-300">
                                {sidebar.salaryInsights?.role} at {company} typically earns between:
                            </p>
                            <p className="text-lg font-bold text-blue-700 dark:text-blue-300 mt-1">
                                {sidebar.salaryInsights?.range}
                            </p>
                            <p className="text-[11px] text-emerald-600 dark:text-emerald-400 mt-0.5 font-medium">
                                ↑ {sidebar.salaryInsights?.avgMarketComparison}
                            </p>
                        </div>

                        <div className="flex items-center justify-between text-[11px] text-slate-400">
                            <button
                                type="button"
                                onClick={() => onToast && onToast('Opening salary benchmark analytics...')}
                                className="text-blue-600 dark:text-blue-400 font-medium hover:underline cursor-pointer"
                            >
                                See detailed salary breakup ↗
                            </button>
                            <span className="text-[10px]">Powered by AmbitionBox</span>
                        </div>
                    </div>

                    {/* 4. BENEFITS & PERKS (4-Grid matching reference) */}
                    <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-4 sm:p-5 shadow-sm space-y-3">
                        <div className="flex items-center justify-between">
                            <h3 className="font-bold text-sm text-slate-900 dark:text-white">
                                Benefits & Perks
                            </h3>
                            <button
                                type="button"
                                onClick={() => onToast && onToast('Showing all company perks')}
                                className="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer"
                            >
                                View all
                            </button>
                        </div>

                        <div className="grid grid-cols-2 gap-2.5">
                            {(sidebar.benefits || []).map((ben) => (
                                <div
                                    key={ben.id}
                                    className="p-2.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/50 flex flex-col items-center text-center space-y-1 hover:bg-slate-100/80 dark:hover:bg-slate-800 transition-colors"
                                >
                                    <span className="text-xl">{ben.icon}</span>
                                    <span className="text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight">
                                        {ben.title}
                                    </span>
                                    <span className="text-[10px] text-slate-500 dark:text-slate-400 leading-tight">
                                        {ben.desc}
                                    </span>
                                </div>
                            ))}
                        </div>

                        <div className="text-right text-[10px] text-slate-400 pt-0.5">
                            Powered by AmbitionBox
                        </div>
                    </div>

                    {/* 5. CAREER SERVICES PROMO (FastForward-style) */}
                    <div className="bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-blue-500/10 dark:from-indigo-950/40 dark:to-purple-950/40 border border-indigo-200/60 dark:border-indigo-800/60 rounded-2xl p-4 sm:p-5 shadow-sm space-y-2.5 backdrop-blur-xs">
                        <div className="flex items-center justify-between">
                            <span className="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                                {sidebar.servicesPromo?.badge}
                            </span>
                            <button
                                type="button"
                                onClick={() => onToast && onToast('Opening career acceleration options...')}
                                className="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline cursor-pointer"
                            >
                                Know more
                            </button>
                        </div>

                        <h4 className="font-bold text-xs sm:text-sm text-slate-900 dark:text-white leading-snug">
                            {sidebar.servicesPromo?.title}
                        </h4>
                        <p className="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            {sidebar.servicesPromo?.subtitle}
                        </p>

                        <button
                            type="button"
                            onClick={() => onToast && onToast('Profile highlighted for top recruiters! 🚀')}
                            className="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-xs transition-colors cursor-pointer mt-1"
                        >
                            Get Featured Candidate Profile
                        </button>
                    </div>

                </div>

            </div>
        </div>
    );
}
