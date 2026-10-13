import React, { useState, useMemo } from 'react';
import JobCardItem from './JobCardItem.jsx';

/**
 * MoreJobsFeed Component
 * Division 6: "More jobs for you" comprehensive feed with interactive filter tags.
 */
export default function MoreJobsFeed({
    jobs = [],
    savedJobIds = [],
    appliedJobIds = [],
    onSave,
    onDismiss,
    onSelect,
    onToast
}) {
    const [selectedFilter, setSelectedFilter] = useState('all');
    const [visibleCount, setVisibleCount] = useState(8);

    const filterOptions = [
        { id: 'all', label: 'All Jobs' },
        { id: 'easy-apply', label: 'Easy Apply' },
        { id: 'hybrid-remote', label: 'Remote / Hybrid' },
        { id: 'aiml', label: 'AI & Machine Learning' },
        { id: 'saved', label: `Saved (${savedJobIds.length})` }
    ];

    const filteredJobs = useMemo(() => {
        return jobs.filter((job) => {
            if (selectedFilter === 'easy-apply') {
                return job.isEasyApply;
            }
            if (selectedFilter === 'hybrid-remote') {
                return job.workplaceType === 'Hybrid' || job.workplaceType === 'Remote';
            }
            if (selectedFilter === 'aiml') {
                const text = `${job.title} ${job.description} ${(job.skills || []).join(' ')}`.toLowerCase();
                return text.includes('ai') || text.includes('ml') || text.includes('data') || text.includes('learning');
            }
            if (selectedFilter === 'saved') {
                return savedJobIds.includes(job.id);
            }
            return true;
        });
    }, [jobs, selectedFilter, savedJobIds]);

    const displayedJobs = filteredJobs.slice(0, visibleCount);

    return (
        <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm overflow-hidden">
            {/* Header */}
            <div className="p-4 sm:p-5 border-b border-slate-200/60 dark:border-slate-800/80">
                <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                    More jobs for you
                </h3>
                <p className="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Based on your profile, preferences, and activity like applies, searches, and saves
                </p>

                {/* Filter Chips */}
                <div className="flex items-center gap-2 mt-3.5 overflow-x-auto scrollbar-none pb-1">
                    {filterOptions.map((f) => (
                        <button
                            key={f.id}
                            type="button"
                            onClick={() => setSelectedFilter(f.id)}
                            className={`px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors shrink-0 cursor-pointer border ${
                                selectedFilter === f.id
                                    ? 'bg-emerald-600 border-emerald-600 text-white shadow-xs'
                                    : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'
                            }`}
                        >
                            {f.label}
                        </button>
                    ))}
                </div>
            </div>

            {/* Job List */}
            {displayedJobs.length > 0 ? (
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
            ) : (
                <div className="p-10 text-center text-slate-500 dark:text-slate-400">
                    <p className="text-sm font-medium">No jobs matching this filter.</p>
                    <button
                        type="button"
                        onClick={() => setSelectedFilter('all')}
                        className="mt-2 text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline"
                    >
                        View all jobs
                    </button>
                </div>
            )}

            {/* Load More Button */}
            {filteredJobs.length > visibleCount && (
                <div className="p-3.5 text-center border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                    <button
                        type="button"
                        onClick={() => setVisibleCount((prev) => prev + 6)}
                        className="text-xs sm:text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline transition-all cursor-pointer"
                    >
                        Load more jobs ({filteredJobs.length - visibleCount} remaining)
                    </button>
                </div>
            )}
        </div>
    );
}
