import React from 'react';
import VerifiedBadge from '../ui/VerifiedBadge.jsx';

/**
 * JobsLeftRail Component
 * Left rail navigation and user identity card matching user screenshot.
 */
export default function JobsLeftRail({
    userProfile,
    trackerCounts,
    onNavigateTab,
    onPostJob,
    onOpenPreferences,
    onNavigate,
    onToast
}) {
    return (
        <aside className="w-full lg:w-[260px] xl:w-[280px] shrink-0 space-y-3.5">
            {/* 1. Mini Profile Card */}
            <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs">
                {/* Banner */}
                <div className="h-16 w-full relative bg-slate-200 dark:bg-slate-800">
                    <img
                        src={userProfile?.banner}
                        alt="Profile Banner"
                        className="w-full h-full object-cover"
                    />
                </div>

                {/* Avatar & Identity Info */}
                <div className="relative z-10 px-4 pb-4 -mt-8 text-center sm:text-left flex flex-col items-center sm:items-start">
                    <div className="relative inline-block">
                        <img
                            src={userProfile?.avatar}
                            alt={userProfile?.name}
                            className="w-16 h-16 rounded-full object-cover border-3 border-white dark:border-slate-900 shadow-md bg-white"
                        />
                        {userProfile?.verified && (
                            <span className="absolute bottom-0 right-0">
                                <VerifiedBadge size="sm" className="bg-white dark:bg-slate-900 rounded-full p-0.5 shadow-xs" />
                            </span>
                        )}
                    </div>

                    <h3 className="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white mt-2 flex items-center gap-1.5 hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer">
                        {userProfile?.name}
                        {userProfile?.verified && <VerifiedBadge size="xs" />}
                    </h3>

                    <p className="text-xs text-slate-600 dark:text-slate-400 mt-0.5 leading-snug line-clamp-2">
                        {userProfile?.headline}
                    </p>

                    <div className="flex items-center gap-1.5 text-[11px] text-slate-400 dark:text-slate-500 mt-1.5">
                        <span>📍 {userProfile?.location}</span>
                    </div>
                </div>
            </div>

            {/* 2. Job Tracker Navigation Menu Card (Exact Match to Screenshot) */}
            <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden divide-y divide-slate-100 dark:divide-slate-800">
                <div className="py-1">
                    {/* Preferences */}
                    <button
                        type="button"
                        onClick={() => {
                            onOpenPreferences && onOpenPreferences();
                            onToast && onToast('Opening Career & Job Preferences');
                        }}
                        className="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200 text-xs sm:text-sm font-semibold transition-colors cursor-pointer text-left group"
                    >
                        <svg className="w-4 h-4 text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        <span>Preferences</span>
                    </button>

                    {/* Job Tracker */}
                    <button
                        type="button"
                        onClick={() => {
                            onNavigateTab && onNavigateTab('tracker');
                            onToast && onToast(`Job Tracker: ${trackerCounts?.saved || 0} saved, ${trackerCounts?.applied || 0} applied`);
                        }}
                        className="w-full flex items-center justify-between px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200 text-xs sm:text-sm font-semibold transition-colors cursor-pointer text-left group"
                    >
                        <div className="flex items-center gap-3">
                            <svg className="w-4 h-4 text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            <span>Job tracker</span>
                        </div>
                        <span className="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 rounded-full">
                            {trackerCounts?.saved || 0}
                        </span>
                    </button>

                    {/* My Career Insights */}
                    <button
                        type="button"
                        onClick={() => {
                            onToast && onToast('Career Insights: Top skills in demand for your profile');
                        }}
                        className="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200 text-xs sm:text-sm font-semibold transition-colors cursor-pointer text-left group"
                    >
                        <svg className="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>My Career Insights</span>
                    </button>
                </div>

                {/* Post a Free Job Action */}
                <div className="p-3">
                    <button
                        type="button"
                        onClick={() => {
                            onPostJob && onPostJob();
                            onToast && onToast('Opening Free Job Posting Portal 🚀');
                        }}
                        className="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl border border-blue-600/40 hover:border-blue-600 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 text-xs sm:text-sm font-bold transition-all cursor-pointer shadow-2xs"
                    >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Post a free job</span>
                    </button>
                </div>
            </div>

            {/* 3. Footer Links Card */}
            <div className="p-4 text-center text-[11px] text-slate-400 dark:text-slate-500 leading-relaxed space-y-2">
                <div className="flex flex-wrap items-center justify-center gap-x-2.5 gap-y-1">
                    <a href="#" className="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">About</a>
                    <a href="#" className="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Accessibility</a>
                    <a href="#" className="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Help Center</a>
                    <a href="#" className="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Privacy & Terms</a>
                    <a href="#" className="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Ad Choices</a>
                    <a href="#" className="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Advertising</a>
                    <a href="#" className="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Business Services</a>
                    <a href="#" className="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Get the app</a>
                </div>
                <div className="flex items-center justify-center gap-1 font-bold text-slate-600 dark:text-slate-300 pt-1">
                    <span className="text-blue-600 dark:text-blue-400">GRID</span>
                    <span>Corporation © 2026</span>
                </div>
            </div>
        </aside>
    );
}
