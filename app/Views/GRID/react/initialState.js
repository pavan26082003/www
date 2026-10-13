/**
 * Grid Profile Initial State Store
 * Centralized data contract for User, Hero, Artifacts, Feed, Experience, and Metrics.
 */

// Featured artifacts - clean array for authenticated user
export const initialArtifacts = [];

export const initialSkills = [];

export const initialExperience = [];

export const initialEducation = [];

export const initialCertifications = [];

export const initialProjects = [];

export const initialPosts = [];

export function getInitialState() {
    const serverData = (typeof window !== 'undefined' && window.__GRID_DATA__) || {};
    const user = serverData.user || {};
    const isCompleted = user.isWizardCompleted === true;

    return {
        user: {
            id: user.id || '',
            fullName: user.name || user.fullName || '',
            firstName: user.firstName || (user.name ? user.name.split(' ')[0] : ''),
            pronoun: user.pronoun || 'He/Him',
            isVerified: user.isVerified !== undefined ? user.isVerified : false,
            headline: user.headline || '',
            location: user.location || '',
            bio: user.bio || '',
            connectionsCount: user.connectionsCount || 0,
            followersCount: user.followersCount || 0,
            avatarSrc: user.avatar || user.avatarSrc || '',
            initials: user.initials || (user.name ? user.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase() : 'U'),
            bannerTag: user.bannerTag || '',
            bannerTitle: user.bannerTitle || '',
            bannerUrl: user.bannerUrl || (user.gridId ? `grid.community/in/${user.gridId}` : ''),
            bannerVideoSrc: user.bannerVideoSrc || '',
            bannerImageSrc: user.bannerImageSrc || '',
            bannerGradient: user.bannerGradient || 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%)',
            isWizardCompleted: isCompleted,
            openToWork: user.openToWork || {
                active: false,
                roles: 'Open to opportunities',
                note: 'Available'
            },
            profileStrength: user.profileStrength || {
                level: isCompleted ? 'Intermediate' : 'Beginner',
                percent: isCompleted ? 60 : 20,
                tip: isCompleted ? 'Add projects and artifacts to elevate your profile strength.' : 'Complete your setup wizard to introduce yourself to peers.'
            }
        },
        about: serverData.about ? {
            text: serverData.about.text || user.bio || '',
            paragraphs: Array.isArray(serverData.about.paragraphs) && serverData.about.paragraphs.length > 0
                ? serverData.about.paragraphs
                : (user.bio ? user.bio.split(/\r?\n\r?\n/).map(p => p.trim()).filter(Boolean) : []),
            bullets: Array.isArray(serverData.about.bullets) ? serverData.about.bullets : [],
            closing: serverData.about.closing || '',
            skills: Array.isArray(serverData.about.skills) ? serverData.about.skills : []
        } : {
            text: user.bio || '',
            paragraphs: user.bio ? user.bio.split(/\r?\n\r?\n/).map(p => p.trim()).filter(Boolean) : [],
            bullets: [],
            closing: '',
            skills: []
        },
        artifacts: (Array.isArray(serverData.artifacts) && serverData.artifacts.length > 0) ? serverData.artifacts : initialArtifacts,
        feed: initialPosts,
        experiences: (Array.isArray(serverData.experiences) && serverData.experiences.length > 0) ? serverData.experiences : initialExperience,
        education: (Array.isArray(serverData.education) && serverData.education.length > 0) ? serverData.education : initialEducation,
        certifications: (Array.isArray(serverData.certifications) && serverData.certifications.length > 0) ? serverData.certifications : initialCertifications,
        projects: (Array.isArray(serverData.projects) && serverData.projects.length > 0) ? serverData.projects : initialProjects,
        skills: (Array.isArray(serverData.skills) && serverData.skills.length > 0) ? serverData.skills : initialSkills,
        honors: Array.isArray(serverData.honors) ? serverData.honors : [],
        recommendations: [],
        languages: Array.isArray(serverData.languages) ? serverData.languages : [],
        analytics: {
            profileViews: user.profileViews || 0,
            postViews: user.postImpressions || 0,
            searchAppearances: user.searchAppearances || 0
        },
        sectionVisibility: {
            about: { connections: true, recruiters: true, nonConnections: true },
            artifacts: { connections: true, recruiters: true, nonConnections: true },
            feed: { connections: true, recruiters: true, nonConnections: true },
            experience: { connections: true, recruiters: true, nonConnections: true },
            education: { connections: true, recruiters: true, nonConnections: true },
            certifications: { connections: true, recruiters: true, nonConnections: true },
            projects: { connections: true, recruiters: true, nonConnections: true },
            skills: { connections: true, recruiters: true, nonConnections: true },
            recommendations: { connections: true, recruiters: true, nonConnections: true }
        },
        activeModal: null
    };
}
