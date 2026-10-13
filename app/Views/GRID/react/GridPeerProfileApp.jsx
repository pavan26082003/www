import React, { useState, useEffect } from 'react';
import RightSidebar from './components/layout/RightSidebar.jsx';
import ProfileHero from './components/hero/ProfileHero.jsx';

// Section Components
import AboutCard from './components/sections/AboutCard.jsx';
import FeaturedArtifacts from './components/sections/FeaturedArtifacts.jsx';
import ActivityFeed from './components/sections/ActivityFeed.jsx';
import ExperienceList from './components/sections/ExperienceList.jsx';
import EducationList from './components/sections/EducationList.jsx';
import ProjectsList from './components/sections/ProjectsList.jsx';
import SkillsGrid from './components/sections/SkillsGrid.jsx';
import Recommendations from './components/sections/Recommendations.jsx';

// Modals
import ContactInfoModal from './components/modals/ContactInfoModal.jsx';

import { getInitialState } from './initialState.js';
import { gridApi } from './gridApi.js';

/**
 * GridPeerProfileApp — Dedicated Page View for Visiting Another User's Profile
 * 
 * Layout Architecture:
 * - Left Stream (#gMainProfileStream): 100% targeted to the visited person (Banner, Avatar, 
 *   Headline, About, Experience, Education, Projects, Skills with +1 endorsement, etc.).
 *   All owner editing controls, perspective switcher, and setup bars are hidden.
 * - Right Sidebar (<RightSidebar>): 100% MINE (Logged-in user's Vanity URL, Private Analytics, 
 *   Integrations, and Suggested Connections).
 */
export default function GridPeerProfileApp({ targetUser, currentUser, onNavigate, onToast }) {
    const targetUserId = targetUser?.id || null;

    // Build the logged-in user's own data for the Right Sidebar
    const myInitial = getInitialState();
    const myUser = {
        ...myInitial.user,
        ...(currentUser || {})
    };
    const myAnalytics = myInitial.analytics || {
        profileViews: 142,
        postViews: 1800,
        searchAppearances: 48
    };

    // Visited Peer State
    const [peerState, setPeerState] = useState(() => {
        const name = targetUser?.name || targetUser?.fullName || 'Community Member';
        const initials = targetUser?.initials || (name ? name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase() : 'U');
        return {
            user: {
                id: targetUserId,
                fullName: name,
                avatarSrc: targetUser?.avatar || targetUser?.avatarSrc || '',
                initials: initials,
                headline: targetUser?.headline || 'Tech Professional',
                pronoun: targetUser?.pronoun || '',
                location: targetUser?.location || 'Remote',
                bio: targetUser?.bio || '',
                connectionsCount: targetUser?.connectionsCount || 120,
                followersCount: targetUser?.followersCount || 240,
                bannerTag: targetUser?.bannerTag || '',
                bannerTitle: targetUser?.bannerTitle || '',
                bannerUrl: targetUser?.bannerUrl || (targetUser?.gridId ? `grid.community/in/${targetUser.gridId}` : ''),
                bannerVideoSrc: targetUser?.bannerVideoSrc || '',
                bannerImageSrc: targetUser?.bannerImageSrc || '',
                bannerGradient: targetUser?.bannerGradient || 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%)',
                openToWork: targetUser?.openToWork || { active: false }
            },
            about: {
                text: targetUser?.bio || '',
                paragraphs: targetUser?.bio ? targetUser.bio.split(/\r?\n\r?\n/).map(p => p.trim()).filter(Boolean) : [],
                bullets: [],
                closing: '',
                skills: []
            },
            artifacts: [],
            feed: [],
            experiences: [],
            education: [],
            certifications: [],
            projects: [],
            skills: [],
            honors: [],
            recommendations: [],
            languages: []
        };
    });

    const [isLoading, setIsLoading] = useState(true);
    const [isContactModalOpen, setIsContactModalOpen] = useState(false);

    // Fetch peer profile data from API
    useEffect(() => {
        if (!targetUserId) {
            setIsLoading(false);
            return;
        }

        setIsLoading(true);
        gridApi.getProfile(targetUserId).then(data => {
            if (!data) {
                setIsLoading(false);
                return;
            }

            const profile = data.profile || {};
            const user = data.user || {};

            let aboutData = {};
            if (profile.about_json) {
                try {
                    aboutData = typeof profile.about_json === 'string' 
                        ? JSON.parse(profile.about_json) 
                        : profile.about_json;
                } catch(e) { /* ignore */ }
            }
            const aboutText = aboutData.text || profile.bio || user.bio || '';
            const aboutSkills = Array.isArray(aboutData.skills) 
                ? aboutData.skills 
                : (profile.about_skills ? profile.about_skills.split(',').map(s => s.trim()).filter(Boolean) : []);

            setPeerState({
                user: {
                    id: user.id || targetUserId,
                    fullName: user.name || targetUser?.name || 'Community Member',
                    avatarSrc: user.avatar || targetUser?.avatar || '',
                    initials: user.initials || targetUser?.initials || 'U',
                    headline: profile.headline || targetUser?.headline || 'Tech Professional',
                    pronoun: profile.pronoun || targetUser?.pronoun || '',
                    location: profile.location || targetUser?.location || 'Remote',
                    bio: aboutText,
                    connectionsCount: profile.connections_count || 120,
                    followersCount: profile.followers_count || 240,
                    bannerType: profile.banner_type || 'gradient',
                    bannerVideoSrc: profile.banner_video_src || '',
                    bannerImageSrc: profile.banner_image_src || '',
                    bannerGradient: profile.banner_gradient || 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%)',
                    bannerTag: profile.banner_tag || '',
                    bannerTitle: profile.banner_title || '',
                    bannerUrl: profile.banner_url || '',
                    openToWork: {
                        active: profile.is_open_to_work ? true : false,
                        roles: profile.otw_roles || 'Open to opportunities',
                        note: profile.otw_note || 'Available'
                    }
                },
                about: {
                    text: aboutText,
                    paragraphs: Array.isArray(aboutData.paragraphs) && aboutData.paragraphs.length > 0
                        ? aboutData.paragraphs
                        : (aboutText ? aboutText.split(/\r?\n\r?\n/).map(p => p.trim()).filter(Boolean) : []),
                    bullets: Array.isArray(aboutData.bullets) ? aboutData.bullets : [],
                    closing: aboutData.closing || '',
                    skills: aboutSkills
                },
                artifacts: Array.isArray(data.artifacts) ? data.artifacts : [],
                feed: Array.isArray(data.posts) ? data.posts : [],
                experiences: Array.isArray(data.experiences) ? data.experiences : [],
                education: Array.isArray(data.education) ? data.education : [],
                certifications: Array.isArray(data.certifications) ? data.certifications : [],
                projects: Array.isArray(data.projects) ? data.projects : [],
                skills: Array.isArray(data.skills) ? data.skills : [],
                honors: Array.isArray(data.honors) ? data.honors : [],
                languages: Array.isArray(data.languages) ? data.languages : [],
                recommendations: Array.isArray(data.recommendations) ? data.recommendations : []
            });
            setIsLoading(false);
        }).catch(err => {
            console.warn('GridPeerProfileApp: Failed to load peer profile:', err);
            setIsLoading(false);
        });
    }, [targetUserId]);

    return (
        <div className="w-full">
            {/* Top Navigation Bar Helper */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-2">
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                    <button
                        type="button"
                        onClick={() => onNavigate ? onNavigate('home') : window.history.back()}
                        style={{
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: '6px',
                            background: 'none',
                            border: 'none',
                            color: 'var(--brand-600, #4f46e5)',
                            fontSize: '13px',
                            fontWeight: 600,
                            cursor: 'pointer',
                            padding: '4px 8px',
                            borderRadius: '6px'
                        }}
                    >
                        <span>←</span> Back to Feed
                    </button>

                    <button
                        type="button"
                        onClick={() => onNavigate ? onNavigate('profile') : null}
                        style={{
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: '6px',
                            background: 'rgba(99, 102, 241, 0.08)',
                            border: '1px solid rgba(99, 102, 241, 0.2)',
                            color: 'var(--brand-600, #4f46e5)',
                            fontSize: '12px',
                            fontWeight: 600,
                            cursor: 'pointer',
                            padding: '4px 12px',
                            borderRadius: '20px'
                        }}
                    >
                        <span>👤</span> View Your Profile
                    </button>
                </div>
            </div>

            {/* Main Stage Grid Container */}
            <div className="g-container">
                <div className="g-grid">
                    {/* ═════════════════════════════════════════════════════════ */}
                    {/* LEFT COLUMN: Data Related to the Person Being Viewed       */}
                    {/* ═════════════════════════════════════════════════════════ */}
                    <main id="gMainProfileStream">
                        {/* Peer Hero Card */}
                        <ProfileHero 
                            user={peerState.user}
                            canEdit={false}
                            onNavigate={onNavigate}
                            onContactInfo={() => setIsContactModalOpen(true)}
                            onToast={onToast}
                        />

                        {/* Peer About Card */}
                        {(peerState.about.text || peerState.about.skills?.length > 0) && (
                            <AboutCard 
                                about={peerState.about}
                                canEdit={false}
                            />
                        )}

                        {/* Peer Featured Artifacts */}
                        {peerState.artifacts?.length > 0 && (
                            <FeaturedArtifacts 
                                artifacts={peerState.artifacts}
                                canEdit={false}
                            />
                        )}

                        {/* Peer Activity Feed */}
                        {peerState.feed?.length > 0 && (
                            <ActivityFeed 
                                posts={peerState.feed}
                                followersCount={peerState.user.followersCount}
                                canEdit={false}
                                onToast={onToast}
                            />
                        )}

                        {/* Peer Work Experience Timeline */}
                        {peerState.experiences?.length > 0 && (
                            <ExperienceList 
                                experiences={peerState.experiences}
                                canEdit={false}
                            />
                        )}

                        {/* Peer Academic Education & Certifications */}
                        {(peerState.education?.length > 0 || peerState.certifications?.length > 0) && (
                            <EducationList 
                                educations={peerState.education}
                                certifications={peerState.certifications}
                                canEdit={false}
                                showEducation={peerState.education?.length > 0}
                                showCertifications={peerState.certifications?.length > 0}
                            />
                        )}

                        {/* Peer Key Projects */}
                        {peerState.projects?.length > 0 && (
                            <ProjectsList 
                                projects={peerState.projects}
                                canEdit={false}
                            />
                        )}

                        {/* Peer Skills & Endorsements (+1 action enabled for visitors!) */}
                        {peerState.skills?.length > 0 && (
                            <SkillsGrid 
                                skills={peerState.skills}
                                canEdit={false}
                                onToast={onToast}
                            />
                        )}

                        {/* Peer Recommendations, Honors & Languages */}
                        {(peerState.honors?.length > 0 || peerState.languages?.length > 0 || peerState.recommendations?.length > 0) && (
                            <Recommendations 
                                canEdit={false}
                                honors={peerState.honors}
                                recommendations={peerState.recommendations}
                                languages={peerState.languages}
                                onToast={onToast}
                            />
                        )}
                    </main>

                    {/* ═════════════════════════════════════════════════════════ */}
                    {/* RIGHT COLUMN: 100% MINE (Logged-in User's Right Sidebar)   */}
                    {/* ═════════════════════════════════════════════════════════ */}
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                        {/* Viewing Notice Card */}
                        <div 
                            style={{
                                background: 'rgba(255, 255, 255, 0.85)',
                                backdropFilter: 'blur(12px)',
                                borderRadius: '12px',
                                border: '1px solid rgba(99, 102, 241, 0.25)',
                                padding: '14px 16px',
                                boxShadow: '0 4px 16px rgba(0, 0, 0, 0.04)'
                            }}
                        >
                            <div style={{ fontSize: '11px', fontWeight: 700, color: 'var(--brand-600, #4f46e5)', textTransform: 'uppercase', letterSpacing: '0.05em' }}>
                                Visiting Peer Profile
                            </div>
                            <div style={{ fontSize: '13px', fontWeight: 600, color: '#1e293b', marginTop: '2px' }}>
                                You are viewing {peerState.user.fullName}
                            </div>
                            <div style={{ fontSize: '11px', color: '#64748b', marginTop: '2px' }}>
                                Connect or send a message to collaborate.
                            </div>
                        </div>

                        {/* Logged-in User's Right Sidebar (My URL, My Analytics, My Integrations, Suggested Peers) */}
                        <RightSidebar 
                            analytics={myAnalytics} 
                            user={myUser} 
                        />
                    </div>
                </div>
            </div>

            {/* Peer Contact Info Modal */}
            <ContactInfoModal 
                isOpen={isContactModalOpen}
                onClose={() => setIsContactModalOpen(false)}
                user={peerState.user}
                onToast={onToast}
            />
        </div>
    );
}
