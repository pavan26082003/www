import React, { useState } from 'react';

/**
 * JobsRightRail Component
 * Right rail sidebar from Stitch specification:
 * 1. Manage my job search (Connections, Following & Recruiters, Saved jobs, Interview events, Company pages)
 * 2. Key Team Leads & Recruiters (Arjun Nambiar, Priya Sharma + Connect buttons)
 * 3. Subscribed Spaces (AI Post-Training & Alignment, Design Systems Guild, Next.js & React Core)
 */
export default function JobsRightRail({
    trackerCounts,
    savedJobCount = 0,
    appliedCount = 62,
    onOpenTracker,
    onOpenPostJob,
    onOpenRecruiterATS,
    onNavigateTab,
    onToast
}) {
    const [connectedLeads, setConnectedLeads] = useState([]);

    const handleConnectLead = (id, name) => {
        setConnectedLeads((prev) =>
            prev.includes(id) ? prev.filter((item) => item !== id) : [...prev, id]
        );
        const isPending = !connectedLeads.includes(id);
        onToast && onToast(isPending ? `Invitation sent to ${name}! 🤝` : `Invitation withdrawn for ${name}.`);
    };

    return (
        <aside className="space-y-4 lg:col-span-4 min-w-0">
            {/* 1. Job Tracker Card */}
            <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm p-4 sm:p-5">
                <div className="flex items-center justify-between pb-2 mb-3 border-b border-slate-200/60 dark:border-slate-800/80">
                    <div className="flex items-center gap-2">
                        <div className="w-6 h-6 rounded-lg bg-blue-600/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                            <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h2 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                            Job Tracker
                        </h2>
                    </div>
                    <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-800">
                        {trackerCounts?.updates || 13} updates
                    </span>
                </div>

                <ul className="text-xs space-y-2.5 text-slate-600 dark:text-slate-300">
                    {/* Applied Jobs Item - Primary entry point to Application Status */}
                    <li
                        onClick={() => {
                            if (onOpenTracker) {
                                onOpenTracker();
                            } else if (onToast) {
                                onToast('Opening Job Application Tracker');
                            }
                        }}
                        className="group flex items-center justify-between p-3 rounded-xl bg-blue-50/60 dark:bg-blue-950/30 border border-blue-200/60 dark:border-blue-900/50 hover:bg-blue-100/70 dark:hover:bg-blue-900/50 cursor-pointer transition-all shadow-2xs"
                    >
                        <span className="flex items-center gap-2.5">
                            <div className="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <span className="font-bold text-slate-900 dark:text-white block group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors text-xs">
                                    Applied jobs
                                </span>
                                <span className="text-[11px] text-slate-500 dark:text-slate-400 block">
                                    Quick Apply progress &amp; status
                                </span>
                            </div>
                        </span>
                        <div className="text-right shrink-0">
                            <span className="font-extrabold text-sm sm:text-base text-blue-600 dark:text-blue-400 block">
                                {appliedCount}
                            </span>
                            <span className="text-[10px] text-blue-600 dark:text-blue-400 font-semibold group-hover:underline">
                                Track →
                            </span>
                        </div>
                    </li>

                    {/* Saved Jobs Item */}
                    <li
                        onClick={() => {
                            onNavigateTab && onNavigateTab('saved');
                            onToast && onToast(`Showing ${savedJobCount} saved jobs`);
                        }}
                        className="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-100/70 dark:hover:bg-slate-800/50 cursor-pointer transition-colors"
                    >
                        <span className="flex items-center gap-2.5">
                            <svg className="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            <span className="font-medium text-slate-800 dark:text-slate-200">Saved jobs</span>
                        </span>
                        <span className="font-bold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full text-xs">
                            {savedJobCount}
                        </span>
                    </li>
                </ul>

                {/* Open Application Tracker Button */}
                <div className="mt-3 pt-3 border-t border-slate-200/60 dark:border-slate-800/80">
                    <button
                        type="button"
                        onClick={() => {
                            if (onOpenTracker) {
                                onOpenTracker();
                            } else if (onToast) {
                                onToast('Opening Job Application Tracker');
                            }
                        }}
                        className="w-full py-2 px-3 rounded-xl bg-black hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-black font-bold text-xs transition-all shadow-xs flex items-center justify-center gap-1.5 cursor-pointer"
                    >
                        <span>Open Application Status ({appliedCount})</span>
                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </div>

            {/* Post a free job banner */}
            <div className="g-glass-card rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 shadow-sm space-y-2.5">
                <div className="flex items-center justify-between">
                    <span className="text-xs font-bold text-slate-900 dark:text-white">
                        Hiring for your team?
                    </span>
                    <span className="text-lg">💼</span>
                </div>
                <p className="text-xs text-slate-500 dark:text-slate-400">
                    Get your open roles in front of quality candidates across the network.
                </p>
                <button
                    type="button"
                    onClick={onOpenPostJob}
                    className="w-full py-2 px-3 rounded-xl border border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-950/40 font-bold text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                >
                    <span>Post a free job</span>
                    <span>→</span>
                </button>
            </div>

            {/* 2. Key Team Leads & Recruiters Card */}
            <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm p-4 sm:p-5">
                <div className="flex items-center justify-between mb-3 pb-2 border-b border-slate-200/60 dark:border-slate-800/80">
                    <h2 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                        Key Team Leads &amp; Recruiters
                    </h2>
                    <button
                        type="button"
                        onClick={() => onToast && onToast('Showing all matching hiring managers')}
                        className="text-xs text-blue-600 dark:text-blue-400 hover:underline font-semibold cursor-pointer"
                    >
                        See All
                    </button>
                </div>

                <div className="space-y-3.5">
                    {/* Lead 1: Arjun Nambiar */}
                    <div className="flex items-start gap-3">
                        <div className="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-extrabold text-xs shrink-0 ring-2 ring-slate-200 dark:ring-slate-700">
                            AN
                        </div>
                        <div className="flex-1 min-w-0">
                            <h3 className="text-xs font-bold text-slate-900 dark:text-white truncate">
                                Arjun Nambiar
                            </h3>
                            <p className="text-[11px] text-slate-500 dark:text-slate-400 truncate leading-tight">
                                Lead AI Engineer at HyperScale Systems
                            </p>
                            <p className="text-[10px] text-blue-600 dark:text-blue-400 font-semibold mt-0.5">
                                ✦ Worked on PyTorch Transformers
                            </p>
                            <button
                                type="button"
                                onClick={() => handleConnectLead('lead-1', 'Arjun Nambiar')}
                                className={`mt-2 px-3 py-1 text-xs font-bold rounded-full w-full transition-all cursor-pointer ${
                                    connectedLeads.includes('lead-1')
                                        ? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700'
                                        : 'bg-black dark:bg-white text-white dark:text-black hover:bg-slate-800 dark:hover:bg-slate-200 shadow-xs'
                                }`}
                            >
                                {connectedLeads.includes('lead-1') ? 'Pending ✓' : '+ Connect'}
                            </button>
                        </div>
                    </div>

                    {/* Lead 2: Priya Sharma */}
                    <div className="flex items-start gap-3 pt-3 border-t border-slate-200/60 dark:border-slate-800/80">
                        <div className="w-10 h-10 rounded-full bg-purple-900 text-white flex items-center justify-center font-extrabold text-xs shrink-0 ring-2 ring-purple-300 dark:ring-purple-700">
                            PS
                        </div>
                        <div className="flex-1 min-w-0">
                            <h3 className="text-xs font-bold text-slate-900 dark:text-white truncate">
                                Priya Sharma
                            </h3>
                            <p className="text-[11px] text-slate-500 dark:text-slate-400 truncate leading-tight">
                                Design Systems Architect at Lattice Labs
                            </p>
                            <p className="text-[10px] text-blue-600 dark:text-blue-400 font-semibold mt-0.5">
                                ✦ Shared 14 mutual engineering peers
                            </p>
                            <button
                                type="button"
                                onClick={() => handleConnectLead('lead-2', 'Priya Sharma')}
                                className={`mt-2 px-3 py-1 text-xs font-bold rounded-full w-full transition-all cursor-pointer ${
                                    connectedLeads.includes('lead-2')
                                        ? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700'
                                        : 'bg-black dark:bg-white text-white dark:text-black hover:bg-slate-800 dark:hover:bg-slate-200 shadow-xs'
                                }`}
                            >
                                {connectedLeads.includes('lead-2') ? 'Pending ✓' : '+ Connect'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {/* 3. Subscribed Spaces / Tech Hubs Card */}
            <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm p-4 sm:p-5">
                <div className="flex items-center justify-between mb-2">
                    <div className="flex items-center gap-2">
                        <svg className="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path clipRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v.183a2 2 0 001.94 1.996 5.992 5.992 0 01-4.857 2.821V18a6 6 0 01-5.67-9.973z" fillRule="evenodd" />
                        </svg>
                        <h2 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                            Subscribed Spaces
                        </h2>
                    </div>
                    <button
                        type="button"
                        onClick={() => onToast && onToast('Exploring all verified GRID engineering spaces')}
                        className="text-xs text-blue-600 dark:text-blue-400 hover:underline font-semibold cursor-pointer"
                    >
                        Explore
                    </button>
                </div>

                <div className="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    {/* Space 1: AI Post-Training */}
                    <div
                        onClick={() => onToast && onToast('Opening AI Post-Training & Alignment Space')}
                        className="py-2.5 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/50 px-1 rounded-lg transition-colors cursor-pointer"
                    >
                        <div className="flex items-center gap-2.5">
                            <span className="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-bold text-[10px] flex items-center justify-center shrink-0 border border-blue-200 dark:border-blue-900">
                                ML
                            </span>
                            <div>
                                <p className="font-bold text-slate-800 dark:text-slate-200">AI Post-Training &amp; Alignment</p>
                                <p className="text-[10px] text-slate-400">1,420 peers</p>
                            </div>
                        </div>
                        <svg className="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    {/* Space 2: Design Systems Guild */}
                    <div
                        onClick={() => onToast && onToast('Opening Design Systems Guild Space')}
                        className="py-2.5 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/50 px-1 rounded-lg transition-colors cursor-pointer"
                    >
                        <div className="flex items-center gap-2.5">
                            <span className="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold text-[10px] flex items-center justify-center shrink-0 border border-indigo-200 dark:border-indigo-900">
                                UI
                            </span>
                            <div>
                                <p className="font-bold text-slate-800 dark:text-slate-200">Design Systems Guild</p>
                                <p className="text-[10px] text-slate-400">890 peers</p>
                            </div>
                        </div>
                        <svg className="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    {/* Space 3: Next.js & React Core */}
                    <div
                        onClick={() => onToast && onToast('Opening Next.js & React Core Space')}
                        className="py-2.5 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/50 px-1 rounded-lg transition-colors cursor-pointer"
                    >
                        <div className="flex items-center gap-2.5">
                            <span className="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-300 font-bold text-[10px] flex items-center justify-center shrink-0 border border-sky-200 dark:border-sky-900">
                                JS
                            </span>
                            <div>
                                <p className="font-bold text-slate-800 dark:text-slate-200">Next.js &amp; React Core</p>
                                <p className="text-[10px] text-slate-400">2,100 peers</p>
                            </div>
                        </div>
                        <svg className="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </div>
        </aside>
    );
}
