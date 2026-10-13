import React, { useState, useEffect } from 'react';
import { getInitialJobsState, saveJobsState, samplePythonJobDetail } from './jobsState.js';
import gridApi from './gridApi.js';
import JobsRightRail from './components/jobs/JobsRightRail.jsx';
import JobsTopChipsBar from './components/jobs/JobsTopChipsBar.jsx';
import RecruiterInvitationBanner from './components/jobs/RecruiterInvitationBanner.jsx';
import JobsPreferencesSection from './components/jobs/JobsPreferencesSection.jsx';
import JobsProfileMatchSection from './components/jobs/JobsProfileMatchSection.jsx';
import RecentSearchesSection from './components/jobs/RecentSearchesSection.jsx';
import CompanyInterestCarousel from './components/jobs/CompanyInterestCarousel.jsx';
import SeeWhosHiringBanner from './components/jobs/SeeWhosHiringBanner.jsx';
import MoreJobsFeed from './components/jobs/MoreJobsFeed.jsx';
import JobsNewslettersSection from './components/jobs/JobsNewslettersSection.jsx';
import EasyApplyModal from './components/jobs/EasyApplyModal.jsx';
import JobDetailsView from './components/jobs/JobDetailsView.jsx';
import JobApplicationStatusView from './components/jobs/JobApplicationStatusView.jsx';
import PostJobModal from './components/jobs/PostJobModal.jsx';

/**
 * GridJobsApp Component
 * Master Orchestrator for the LinkedIn-Style GRID Jobs Hub matching Stitch design:
 * - Clean 2-column layout (Main Feed 8 cols + Right Rail 4 cols, no left sidebar)
 * - Main Feed: Top recommendation chips -> Recruiter invitation -> All 6 core job divisions -> Newsletters
 * - Right Rail: Job Tracker -> Key Team Leads & Recruiters -> Subscribed Spaces
 */
export default function GridJobsApp({ onNavigate, onToast, currentUser }) {
    const [jobsData, setJobsData] = useState(getInitialJobsState);
    const [selectedChip, setSelectedChip] = useState('all');
    const [selectedJobDetail, setSelectedJobDetail] = useState(null);
    const [activeJobForModal, setActiveJobForModal] = useState(null);
    const [isApplyModalOpen, setIsApplyModalOpen] = useState(false);
    const [isPostJobModalOpen, setIsPostJobModalOpen] = useState(false);
    const [isTrackerOpen, setIsTrackerOpen] = useState(() => {
        try {
            const shouldOpen = sessionStorage.getItem('grid_open_tracker') === 'true';
            if (shouldOpen) {
                sessionStorage.removeItem('grid_open_tracker');
                return true;
            }
        } catch (e) {}
        return false;
    });
    const [searchFilterText, setSearchFilterText] = useState('');

    // Listen for mobile drawer navigation events
    useEffect(() => {
        const handleOpenTracker = () => {
            setSelectedJobDetail(null);
            setIsTrackerOpen(true);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };
        const handleOpenSearch = () => {
            setSelectedJobDetail(null);
            setIsTrackerOpen(false);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };

        window.addEventListener('grid:open-jobs-tracker', handleOpenTracker);
        window.addEventListener('grid:open-jobs-search', handleOpenSearch);

        return () => {
            window.removeEventListener('grid:open-jobs-tracker', handleOpenTracker);
            window.removeEventListener('grid:open-jobs-search', handleOpenSearch);
        };
    }, []);

    // Persist updates to localStorage
    useEffect(() => {
        saveJobsState(jobsData);
    }, [jobsData]);

    const {
        userProfile,
        preferences,
        trackerCounts,
        savedJobIds = [],
        appliedJobIds = [],
        dismissedJobIds = [],
        preferenceJobs = [],
        profileMatchJobs = [],
        recentSearches = [],
        interestedCompanies = [],
        moreJobs = []
    } = jobsData;

    // Filter out dismissed jobs across all lists
    const activePreferenceJobs = preferenceJobs.filter(j => !dismissedJobIds.includes(j.id));
    const activeProfileMatchJobs = profileMatchJobs.filter(j => !dismissedJobIds.includes(j.id));
    const activeMoreJobs = moreJobs.filter(j => !dismissedJobIds.includes(j.id));

    // Handle Job Bookmarking / Saving
    const handleSaveJob = (jobId) => {
        setJobsData((prev) => {
            const isAlreadySaved = prev.savedJobIds.includes(jobId);
            const newSavedIds = isAlreadySaved
                ? prev.savedJobIds.filter(id => id !== jobId)
                : [...prev.savedJobIds, jobId];

            return {
                ...prev,
                savedJobIds: newSavedIds,
                trackerCounts: {
                    ...prev.trackerCounts,
                    saved: newSavedIds.length
                }
            };
        });

        const isSavedNow = !savedJobIds.includes(jobId);
        onToast && onToast(isSavedNow ? 'Job saved to your tracker! 🔖' : 'Job removed from saved items.');
    };

    // Handle Job Dismissal ('X')
    const handleDismissJob = (jobId) => {
        setJobsData((prev) => ({
            ...prev,
            dismissedJobIds: [...prev.dismissedJobIds, jobId]
        }));
        onToast && onToast('Job recommendation removed.');
    };

    // Handle Company Interest Toggle
    const handleToggleInterest = (companyId) => {
        setJobsData((prev) => ({
            ...prev,
            interestedCompanies: prev.interestedCompanies.map((c) =>
                c.id === companyId ? { ...c, interested: !c.interested } : c
            )
        }));
    };

    // Handle Clear Recent Searches
    const handleClearSearches = () => {
        setJobsData((prev) => ({
            ...prev,
            recentSearches: []
        }));
    };

    // Handle Selecting a Recent Search
    const handleSelectSearch = (query) => {
        setSearchFilterText(query);
        onToast && onToast(`Filtering jobs for "${query}" 🔍`);
    };

    // Handle Opening Job Details View
    const handleSelectJob = (job) => {
        // As requested: use the comprehensive Python Developer detail dummy structure across all job post clicks for testing
        setSelectedJobDetail(samplePythonJobDetail);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // Handle Easy Apply Submission
    const handleSubmitApplication = async (jobId) => {
        const appliedJob = activeJobForModal || samplePythonJobDetail;
        const newAppId = `app-${Date.now()}`;

        // Asynchronously submit application to CodeIgniter database
        if (jobId) {
            gridApi.applyForJob(jobId, 'Applied via GRID Quick Apply').catch((err) => {
                console.warn('API application submission error:', err);
            });
        }

        const newApplication = {
            id: newAppId,
            jobId: jobId || appliedJob.id,
            title: appliedJob.title || 'Python Developer Opportunity',
            company: appliedJob.company || 'Novatech Global Solutions',
            rating: appliedJob.rating || 4.4,
            reviewCount: appliedJob.reviewCount || 148,
            logoBg: appliedJob.logoBg || '#3b82f6',
            logoText: appliedJob.logoText || 'NT',
            logoTextColor: '#ffffff',
            location: appliedJob.location || 'Hyderabad (Hybrid)',
            appliedDate: 'Just now',
            status: 'Application sent just now',
            statusType: 'application_submitted',
            recruiterActive: 'Recruiter active today',
            isQuickApply: true,
            source: 'grid_quick_apply',
            steps: [
                { id: 'step-1', label: 'Contacted By Email', date: 'Today', completed: true },
                { id: 'step-2', label: 'Application Viewed', date: null, completed: false, isCurrent: true },
                { id: 'step-3', label: 'Resume Viewed', date: null, completed: false, isCurrent: false },
                { id: 'step-4', label: 'Awaiting Recruiter Action', date: null, completed: false, isCurrent: false }
            ],
            activity: {
                totalApplications: 93,
                viewedByRecruiter: 42
            },
            matchCriteria: [
                { label: 'Early Applicant', match: true },
                { label: 'Keyskills', match: true },
                { label: 'Location', match: true },
                { label: 'Work Experience', match: true },
                { label: 'Industry', match: true },
                { label: 'Department', match: true }
            ],
            recruiter: {
                name: 'Ananya Roy',
                role: `Hiring Lead at ${appliedJob.company || 'Novatech Global Solutions'}`,
                activity: 'Recruiter active today'
            }
        };

        setJobsData((prev) => {
            const newAppliedIds = prev.appliedJobIds.includes(jobId)
                ? prev.appliedJobIds
                : [jobId, ...prev.appliedJobIds];

            const existingApps = prev.appliedApplications || [];
            const newAppliedApplications = [newApplication, ...existingApps];

            return {
                ...prev,
                appliedJobIds: newAppliedIds,
                appliedApplications: newAppliedApplications,
                trackerCounts: {
                    ...prev.trackerCounts,
                    applied: newAppliedApplications.length
                }
            };
        });
        setIsApplyModalOpen(false);
        onToast && onToast('Application submitted via Quick Apply! Real-time progress is active in Job Tracker. 🎉');
    };

    // Handle Edit Preferences
    const handleEditPreferences = () => {
        const newRoles = window.prompt(
            'Edit your preferred job roles (comma separated):',
            preferences?.roles?.join(', ') || 'Full Stack Engineer, Python Developer'
        );
        if (newRoles && newRoles.trim()) {
            const rolesArray = newRoles.split(',').map(s => s.trim()).filter(Boolean);
            setJobsData(prev => ({
                ...prev,
                preferences: {
                    ...prev.preferences,
                    roles: rolesArray
                }
            }));
            onToast && onToast('Updated your job preferences! 🎯');
        }
    };

    // Handle Post a Free Job
    const handlePostJob = () => {
        const title = window.prompt('Enter job title to post for free:');
        if (title && title.trim()) {
            const newJob = {
                id: `user-posted-${Date.now()}`,
                title: title.trim(),
                company: 'My Company',
                logoBg: '#4f46e5',
                logoText: 'MC',
                location: 'Hyderabad (Hybrid)',
                workplaceType: 'Hybrid',
                postedTime: 'Just now',
                isEasyApply: true,
                isPromoted: false,
                skills: ['Communication', 'Teamwork', 'Core Skills'],
                description: 'Open position posted directly from GRID Jobs Employer portal.'
            };
            setJobsData(prev => ({
                ...prev,
                moreJobs: [newJob, ...prev.moreJobs]
            }));
            onToast && onToast(`Free job "${title.trim()}" posted successfully! 🚀`);
        }
    };

    return (
        <div className="w-full font-sans">
            {/* Main Content Container */}
            <div className="max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8 py-4 sm:py-6 w-full">
                {selectedJobDetail ? (
                    /* Detailed Job View matching the reference design */
                    <JobDetailsView
                        jobDetail={selectedJobDetail}
                        isSaved={savedJobIds.includes(selectedJobDetail.id)}
                        isApplied={appliedJobIds.includes(selectedJobDetail.id)}
                        onBack={() => {
                            setSelectedJobDetail(null);
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }}
                        onSave={handleSaveJob}
                        onApply={(job) => {
                            setActiveJobForModal(job);
                            setIsApplyModalOpen(true);
                        }}
                        moreJobs={activeMoreJobs}
                        savedJobIds={savedJobIds}
                        appliedJobIds={appliedJobIds}
                        onDismiss={handleDismissJob}
                        onSelect={handleSelectJob}
                        onToast={onToast}
                    />
                ) : isTrackerOpen ? (
                    /* Dedicated Job Tracker & Application Status View matching Reference Image */
                    <JobApplicationStatusView
                        applications={jobsData.appliedApplications || []}
                        onBack={() => {
                            setIsTrackerOpen(false);
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }}
                        onSelectJobDetail={(job) => {
                            setIsTrackerOpen(false);
                            setSelectedJobDetail(samplePythonJobDetail);
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }}
                        onToast={onToast}
                    />
                ) : (
                    /* Default Feed View with Main Feed (8 cols) & Right Rail (4 cols) */
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-5 w-full">
                        {/* 1. MAIN FEED: 8 cols on desktop, full width on mobile */}
                        <main className="space-y-4 lg:col-span-8 min-w-0 w-full overflow-hidden">
                            {/* TOP: Recommendations Filter Chips */}
                            <JobsTopChipsBar
                                selectedChip={selectedChip}
                                onSelectChip={(chipId) => {
                                    setSelectedChip(chipId);
                                    onToast && onToast(`Showing recommendations for ${chipId}`);
                                }}
                                onOpenPostJob={() => setIsPostJobModalOpen(true)}
                            />

                            {/* PENDING INVITATIONS: Kavya Sundaram */}
                            <RecruiterInvitationBanner onToast={onToast} />

                            {/* DIVISION 1: Jobs based on your preferences */}
                            <JobsPreferencesSection
                                preferences={preferences}
                                jobs={activePreferenceJobs}
                                savedJobIds={savedJobIds}
                                appliedJobIds={appliedJobIds}
                                onSave={handleSaveJob}
                                onDismiss={handleDismissJob}
                                onSelect={handleSelectJob}
                                onEditPreferences={handleEditPreferences}
                                onToast={onToast}
                            />

                            {/* DIVISION 2: Jobs that match your profile (with Top Applicant Spotlight) */}
                            <JobsProfileMatchSection
                                jobs={activeProfileMatchJobs}
                                userProfile={userProfile}
                                savedJobIds={savedJobIds}
                                appliedJobIds={appliedJobIds}
                                onSave={handleSaveJob}
                                onDismiss={handleDismissJob}
                                onSelect={handleSelectJob}
                                onToast={onToast}
                            />

                            {/* DIVISION 3: Recent job searches */}
                            <RecentSearchesSection
                                searches={recentSearches}
                                onSelectSearch={handleSelectSearch}
                                onClearSearches={handleClearSearches}
                                onToast={onToast}
                            />

                            {/* DIVISION 4: Show your interest in these companies */}
                            <CompanyInterestCarousel
                                companies={interestedCompanies}
                                onToggleInterest={handleToggleInterest}
                                onToast={onToast}
                            />

                            {/* DIVISION 5: See who's hiring Quick Callout Banner */}
                            <SeeWhosHiringBanner
                                userProfile={userProfile}
                                onSearchJobs={() => onToast && onToast('Showing companies actively hiring for your profile')}
                                onToast={onToast}
                            />

                            {/* DIVISION 6: More jobs for you (Comprehensive List with Filter Chips) */}
                            <MoreJobsFeed
                                jobs={activeMoreJobs}
                                savedJobIds={savedJobIds}
                                appliedJobIds={appliedJobIds}
                                onSave={handleSaveJob}
                                onDismiss={handleDismissJob}
                                onSelect={handleSelectJob}
                                onToast={onToast}
                            />

                            {/* ENGINEERING NEWSLETTERS & DESIGN SYSTEMS (From Stitch) */}
                            <JobsNewslettersSection onToast={onToast} />
                        </main>

                        {/* 2. RIGHT RAIL: 4 cols on desktop */}
                        <JobsRightRail
                            trackerCounts={trackerCounts}
                            savedJobCount={savedJobIds.length}
                            appliedCount={jobsData.appliedApplications?.length || 62}
                            onOpenTracker={() => {
                                setSelectedJobDetail(null);
                                setIsTrackerOpen(true);
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            }}
                            onOpenPostJob={() => setIsPostJobModalOpen(true)}
                            onNavigateTab={onNavigate}
                            onToast={onToast}
                        />
                    </div>
                )}
            </div>

            {/* Interactive Easy Apply Modal */}
            <EasyApplyModal
                job={activeJobForModal}
                userProfile={userProfile}
                isOpen={isApplyModalOpen}
                onClose={() => setIsApplyModalOpen(false)}
                onSubmitApplication={handleSubmitApplication}
            />

            {/* Post a Free Job Modal */}
            <PostJobModal
                isOpen={isPostJobModalOpen}
                onClose={() => setIsPostJobModalOpen(false)}
                onJobCreated={(newJob) => {
                    onToast && onToast('Job published successfully! 🚀');
                }}
                onToast={onToast}
            />
        </div>
    );
}
