import React, { useState, useEffect } from 'react';
import gridApi from '../../gridApi.js';

/**
 * RecruiterDashboardView Component
 * Complete ATS (Applicant Tracking System) Pipeline & Job Management view for Recruiters.
 */
export default function RecruiterDashboardView({
    onNavigate,
    onToast,
    onOpenPostJobModal,
    onBackToCandidateFeed
}) {
    const [jobs, setJobs] = useState([]);
    const [selectedJobId, setSelectedJobId] = useState(null);
    const [applicants, setApplicants] = useState([]);
    const [statusFilter, setStatusFilter] = useState('all'); // 'all' | 'applied' | 'reviewing' | 'interview' | 'offered' | 'rejected'
    const [isLoadingJobs, setIsLoadingJobs] = useState(true);
    const [isLoadingApplicants, setIsLoadingApplicants] = useState(false);
    const [updatingAppId, setUpdatingAppId] = useState(null);

    // Initial load: Fetch recruiter's jobs
    useEffect(() => {
        loadRecruiterJobs();
    }, []);

    const loadRecruiterJobs = async () => {
        setIsLoadingJobs(true);
        try {
            const res = await gridApi.getRecruiterJobs();
            if (Array.isArray(res)) {
                setJobs(res);
                if (res.length > 0 && !selectedJobId) {
                    setSelectedJobId(res[0].id);
                }
            } else {
                setJobs([]);
            }
        } catch (e) {
            console.warn('Failed to load recruiter jobs:', e);
            setJobs([]);
        } finally {
            setIsLoadingJobs(false);
        }
    };

    // Load applicants whenever selected job changes
    useEffect(() => {
        if (!selectedJobId) return;
        loadApplicants(selectedJobId);
    }, [selectedJobId]);

    const loadApplicants = async (jobId) => {
        setIsLoadingApplicants(true);
        try {
            const res = await gridApi.getJobApplicants(jobId);
            if (Array.isArray(res)) {
                setApplicants(res);
            } else {
                setApplicants([]);
            }
        } catch (e) {
            console.warn('Failed to load applicants:', e);
            setApplicants([]);
        } finally {
            setIsLoadingApplicants(false);
        }
    };

    const handleUpdateStatus = async (appId, newStatus) => {
        setUpdatingAppId(appId);
        try {
            const res = await gridApi.updateApplicationStatus(appId, newStatus);
            if (res && res.success) {
                onToast && onToast(`Candidate moved to ${newStatus.toUpperCase()} stage! 🎯`);
                // Update local list
                setApplicants((prev) =>
                    prev.map((app) => (app.id === appId ? { ...app, status: newStatus } : app))
                );
            } else {
                onToast && onToast(res?.message || 'Failed to update candidate status.');
            }
        } catch (e) {
            onToast && onToast(e.message || 'Status update error.');
        } finally {
            setUpdatingAppId(null);
        }
    };

    const selectedJob = jobs.find((j) => j.id === selectedJobId) || jobs[0];

    const stages = [
        { id: 'all', label: 'All Candidates', count: applicants.length },
        { id: 'applied', label: 'Applied (New)', count: applicants.filter(a => a.status === 'applied').length },
        { id: 'reviewing', label: 'Under Review', count: applicants.filter(a => a.status === 'reviewing').length },
        { id: 'interview', label: 'Interviewing', count: applicants.filter(a => a.status === 'interview').length },
        { id: 'offered', label: 'Offer Extended', count: applicants.filter(a => a.status === 'offered').length },
        { id: 'rejected', label: 'Archived', count: applicants.filter(a => a.status === 'rejected').length }
    ];

    const filteredApplicants = statusFilter === 'all'
        ? applicants
        : applicants.filter(a => a.status === statusFilter);

    const getStageBadgeStyle = (status) => {
        switch (status) {
            case 'applied':
                return 'bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 border-blue-200 dark:border-blue-800';
            case 'reviewing':
                return 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border-amber-200 dark:border-amber-800';
            case 'interview':
                return 'bg-purple-100 text-purple-800 dark:bg-purple-950/80 dark:text-purple-300 border-purple-200 dark:border-purple-800';
            case 'offered':
                return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800';
            case 'rejected':
                return 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700';
            default:
                return 'bg-slate-100 text-slate-700';
        }
    };

    return (
        <div className="space-y-6 animate-fadeIn">
            {/* Header Banner */}
            <div className="g-glass-card rounded-3xl border border-purple-200/60 dark:border-purple-900/40 p-6 sm:p-8 bg-gradient-to-r from-purple-900/20 via-indigo-900/10 to-transparent flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <div className="flex items-center gap-2 mb-1.5">
                        <span className="text-xs font-extrabold uppercase tracking-widest text-purple-600 dark:text-purple-400">
                            Recruiter ATS Hub
                        </span>
                        <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300">
                            Hiring Workspace
                        </span>
                    </div>
                    <h1 className="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Candidate Pipeline &amp; Role Management
                    </h1>
                    <p className="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-1 max-w-2xl">
                        Review verified candidates, schedule technical deep dives, and manage candidate workflows in real time.
                    </p>
                </div>

                <div className="flex items-center gap-3 shrink-0">
                    <button
                        type="button"
                        onClick={onBackToCandidateFeed}
                        className="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:border-slate-400 text-slate-700 dark:text-slate-200 font-bold text-xs transition-colors cursor-pointer"
                    >
                        ← Candidate Hub
                    </button>
                    <button
                        type="button"
                        onClick={onOpenPostJobModal}
                        className="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold rounded-xl text-xs shadow-lg shadow-purple-500/25 transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <span>+</span>
                        <span>Post a Job</span>
                    </button>
                </div>
            </div>

            {/* Layout: Left Sidebar of Posted Jobs, Right Area for ATS Kanban Pipeline */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                {/* Posted Jobs Column (4 Cols) */}
                <div className="lg:col-span-4 space-y-3">
                    <div className="flex items-center justify-between px-1">
                        <h2 className="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Your Active Openings ({jobs.length})
                        </h2>
                    </div>

                    {isLoadingJobs ? (
                        <div className="p-8 text-center text-xs text-slate-400 g-glass-card rounded-2xl border border-slate-200 dark:border-slate-800">
                            Loading your posted jobs...
                        </div>
                    ) : jobs.length === 0 ? (
                        <div className="p-8 text-center g-glass-card rounded-2xl border border-dashed border-slate-300 dark:border-slate-800 space-y-3">
                            <div className="text-3xl">📋</div>
                            <h3 className="font-bold text-sm text-slate-800 dark:text-slate-200">
                                No job postings yet
                            </h3>
                            <p className="text-xs text-slate-500 dark:text-slate-400">
                                Publish your first open role to start receiving matched engineering applicants.
                            </p>
                            <button
                                type="button"
                                onClick={onOpenPostJobModal}
                                className="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs transition-colors"
                            >
                                + Post Free Job
                            </button>
                        </div>
                    ) : (
                        <div className="space-y-2">
                            {jobs.map((job) => {
                                const isSelected = job.id === selectedJobId;
                                return (
                                    <div
                                        key={job.id}
                                        onClick={() => setSelectedJobId(job.id)}
                                        className={`p-4 rounded-2xl border cursor-pointer transition-all ${
                                            isSelected
                                                ? 'bg-purple-50/70 dark:bg-purple-950/40 border-purple-500 shadow-sm'
                                                : 'g-glass-card border-slate-200/80 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'
                                        }`}
                                    >
                                        <div className="flex items-start justify-between gap-2">
                                            <h3 className="font-bold text-sm text-slate-900 dark:text-white line-clamp-1">
                                                {job.title}
                                            </h3>
                                            <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 shrink-0">
                                                {job.status || 'Active'}
                                            </span>
                                        </div>
                                        <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            {job.company_name} • {job.location || 'Remote'}
                                        </p>
                                        <div className="flex items-center justify-between text-[11px] text-slate-400 mt-3 pt-2 border-t border-slate-200/50 dark:border-slate-800/50">
                                            <span>👥 {job.applicant_count || 0} applicants</span>
                                            <span className="capitalize">{job.workplace_type || 'remote'}</span>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </div>

                {/* ATS Pipeline Candidates Column (8 Cols) */}
                <div className="lg:col-span-8 space-y-4">
                    {selectedJob ? (
                        <>
                            {/* Selected Job Banner & Stage Tabs */}
                            <div className="g-glass-card rounded-2xl border border-slate-200 dark:border-slate-800 p-5 space-y-4">
                                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div>
                                        <h2 className="text-lg font-extrabold text-slate-900 dark:text-white">
                                            {selectedJob.title}
                                        </h2>
                                        <p className="text-xs text-slate-500 dark:text-slate-400">
                                            {selectedJob.discipline} • {selectedJob.location} • {selectedJob.salary_range || 'Competitive'}
                                        </p>
                                    </div>
                                    <span className="text-xs font-semibold text-purple-600 dark:text-purple-400">
                                        Total Pipeline: {applicants.length} Candidates
                                    </span>
                                </div>

                                {/* Stages Filter Pills */}
                                <div className="flex items-center gap-2 overflow-x-auto scrollbar-none pb-1 text-xs">
                                    {stages.map((st) => (
                                        <button
                                            key={st.id}
                                            type="button"
                                            onClick={() => setStatusFilter(st.id)}
                                            className={`px-3 py-1.5 rounded-xl font-bold transition-all shrink-0 cursor-pointer flex items-center gap-1.5 ${
                                                statusFilter === st.id
                                                    ? 'bg-purple-600 text-white shadow-xs'
                                                    : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'
                                            }`}
                                        >
                                            <span>{st.label}</span>
                                            <span className={`text-[10px] px-1.5 py-0.2 rounded-full font-extrabold ${
                                                statusFilter === st.id ? 'bg-white/30 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'
                                            }`}>
                                                {st.count}
                                            </span>
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Applicants Feed */}
                            {isLoadingApplicants ? (
                                <div className="p-12 text-center text-xs text-slate-400 g-glass-card rounded-2xl border border-slate-200 dark:border-slate-800">
                                    Fetching candidate pipeline...
                                </div>
                            ) : filteredApplicants.length === 0 ? (
                                <div className="p-12 text-center g-glass-card rounded-2xl border border-dashed border-slate-300 dark:border-slate-800 space-y-2">
                                    <div className="text-2xl">🔍</div>
                                    <h4 className="font-bold text-sm text-slate-800 dark:text-slate-200">
                                        No candidates in this stage
                                    </h4>
                                    <p className="text-xs text-slate-400">
                                        Candidates who apply will automatically appear here with their verified GRID profiles.
                                    </p>
                                </div>
                            ) : (
                                <div className="space-y-3">
                                    {filteredApplicants.map((cand) => (
                                        <div
                                            key={cand.id}
                                            className="g-glass-card rounded-2xl border border-slate-200 dark:border-slate-800 p-5 hover:border-purple-300 dark:hover:border-purple-800 transition-all space-y-3"
                                        >
                                            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                                <div className="flex items-start gap-3">
                                                    <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-slate-900 to-slate-700 text-white flex items-center justify-center font-bold text-base shrink-0 shadow-sm">
                                                        {cand.user?.initials || cand.name?.slice(0, 2).toUpperCase() || 'CA'}
                                                    </div>
                                                    <div>
                                                        <div className="flex items-center gap-2 flex-wrap">
                                                            <h3 className="font-bold text-sm text-slate-900 dark:text-white">
                                                                {cand.user?.name || cand.name || 'Anonymous Candidate'}
                                                            </h3>
                                                            {cand.profile?.grid_id && (
                                                                <span className="text-[10px] font-mono font-semibold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/60 px-2 py-0.5 rounded border border-purple-200 dark:border-purple-900/40">
                                                                    @{cand.profile.grid_id}
                                                                </span>
                                                            )}
                                                        </div>
                                                        <p className="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                                                            {cand.profile?.headline || 'Engineer & Builder'}
                                                        </p>
                                                        <p className="text-[11px] text-slate-400 mt-0.5">
                                                            📍 {cand.profile?.location || 'India'} • Applied on {new Date(cand.created_at || Date.now()).toLocaleDateString()}
                                                        </p>
                                                    </div>
                                                </div>

                                                {/* Current Stage Badge */}
                                                <div className="shrink-0">
                                                    <span className={`text-[11px] font-bold px-3 py-1 rounded-full border uppercase tracking-wider ${getStageBadgeStyle(cand.status)}`}>
                                                        {cand.status}
                                                    </span>
                                                </div>
                                            </div>

                                            {/* Cover Note */}
                                            {cand.cover_note && (
                                                <div className="p-3 rounded-xl bg-slate-50 dark:bg-slate-850 border border-slate-200/60 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 italic">
                                                    "{cand.cover_note}"
                                                </div>
                                            )}

                                            {/* Actions & Status Dropdown */}
                                            <div className="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-3 border-t border-slate-200/60 dark:border-slate-800">
                                                <div className="flex items-center gap-3">
                                                    <button
                                                        type="button"
                                                        onClick={() => {
                                                            const targetHandle = cand.profile?.grid_id || cand.user_id;
                                                            onNavigate && onNavigate('profile', { targetUser: { id: targetHandle } });
                                                        }}
                                                        className="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline text-left cursor-pointer"
                                                    >
                                                        View Profile ↗
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() => {
                                                            const targetHandle = cand.profile?.grid_id || cand.user_id;
                                                            onNavigate && onNavigate('messaging', { targetUser: { id: targetHandle } });
                                                        }}
                                                        className="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline text-left cursor-pointer flex items-center gap-1"
                                                    >
                                                        <span>💬</span>
                                                        <span>Message</span>
                                                    </button>
                                                </div>

                                                {/* Stage Progressor Controls */}
                                                <div className="flex items-center gap-2 flex-wrap">
                                                    <span className="text-[10px] font-bold text-slate-400 uppercase">
                                                        Move to:
                                                    </span>

                                                    {cand.status !== 'reviewing' && (
                                                        <button
                                                            type="button"
                                                            disabled={updatingAppId === cand.id}
                                                            onClick={() => handleUpdateStatus(cand.id, 'reviewing')}
                                                            className="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 hover:bg-amber-100 transition-colors cursor-pointer"
                                                        >
                                                            Review
                                                        </button>
                                                    )}

                                                    {cand.status !== 'interview' && (
                                                        <button
                                                            type="button"
                                                            disabled={updatingAppId === cand.id}
                                                            onClick={() => handleUpdateStatus(cand.id, 'interview')}
                                                            className="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 hover:bg-purple-100 transition-colors cursor-pointer"
                                                        >
                                                            Interview
                                                        </button>
                                                    )}

                                                    {cand.status !== 'offered' && (
                                                        <button
                                                            type="button"
                                                            disabled={updatingAppId === cand.id}
                                                            onClick={() => handleUpdateStatus(cand.id, 'offered')}
                                                            className="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 hover:bg-emerald-100 transition-colors cursor-pointer"
                                                        >
                                                            Offer
                                                        </button>
                                                    )}

                                                    {cand.status !== 'rejected' && (
                                                        <button
                                                            type="button"
                                                            disabled={updatingAppId === cand.id}
                                                            onClick={() => handleUpdateStatus(cand.id, 'rejected')}
                                                            className="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 transition-colors cursor-pointer"
                                                        >
                                                            Archive
                                                        </button>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </>
                    ) : (
                        <div className="p-12 text-center g-glass-card rounded-2xl border border-slate-200 dark:border-slate-800">
                            Select a job posting on the left to review applicants.
                        </div>
                    )}
                </div>

            </div>
        </div>
    );
}
