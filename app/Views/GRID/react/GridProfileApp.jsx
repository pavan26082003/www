import React, { useState, useEffect } from 'react';
import LeftSidebar from './components/layout/LeftSidebar.jsx';
import RightSidebar from './components/layout/RightSidebar.jsx';
import ProfileHero from './components/hero/ProfileHero.jsx';
import ProfilePerspectiveBar from './components/hero/ProfilePerspectiveBar.jsx';

// Section Components
import AboutCard from './components/sections/AboutCard.jsx';
import FeaturedArtifacts from './components/sections/FeaturedArtifacts.jsx';
import ActivityFeed from './components/sections/ActivityFeed.jsx';
import ExperienceList from './components/sections/ExperienceList.jsx';
import EducationList from './components/sections/EducationList.jsx';
import ProjectsList from './components/sections/ProjectsList.jsx';
import SkillsGrid from './components/sections/SkillsGrid.jsx';
import Recommendations from './components/sections/Recommendations.jsx';

// Modal Components
import EditCoverModal from './components/modals/EditCoverModal.jsx';
import EditProfileModal from './components/modals/EditProfileModal.jsx';
import OpenToWorkModal from './components/modals/OpenToWorkModal.jsx';
import EditAboutModal from './components/modals/EditAboutModal.jsx';
import AddArtifactModal from './components/modals/AddArtifactModal.jsx';
import AddExperienceModal from './components/modals/AddExperienceModal.jsx';
import AddEducationModal from './components/modals/AddEducationModal.jsx';
import AddProjectModal from './components/modals/AddProjectModal.jsx';
import AddSkillModal from './components/modals/AddSkillModal.jsx';
import CreatePostModal from './components/modals/CreatePostModal.jsx';
import ContactInfoModal from './components/modals/ContactInfoModal.jsx';
import SectionVisibilityModal from './components/modals/SectionVisibilityModal.jsx';
import AddHonorModal from './components/modals/AddHonorModal.jsx';
import AddLanguageModal from './components/modals/AddLanguageModal.jsx';
import ProfilePhotoModal from './components/modals/ProfilePhotoModal.jsx';

import { getInitialState } from './initialState.js';
import { ProfileAPI } from './services/api.js';
import { useWebSocket } from './services/socket.js';
import { gridApi } from './gridApi.js';

export default function GridProfileApp({ onNavigate, onToast, targetUser = null, currentUser = null }) {
    const isViewingOther = Boolean(targetUser && targetUser.id);
    const [state, setState] = useState(() => {
        const initial = getInitialState();
        if (targetUser && targetUser.name) {
            return {
                ...initial,
                user: {
                    ...initial.user,
                    id: targetUser.id,
                    fullName: targetUser.name,
                    avatarSrc: targetUser.avatar || '',
                    initials: targetUser.initials || 'U',
                    headline: targetUser.headline || initial.user.headline
                }
            };
        }
        return initial;
    });

    const [activeModal, setActiveModal] = useState(null);
    const [activePerspective, setActivePerspective] = useState(() => isViewingOther ? 'connection' : 'owner');
    const [itemToEdit, setItemToEdit] = useState(null);
    const [visibilityModalInitialSection, setVisibilityModalInitialSection] = useState('about');

    useEffect(() => {
        setActivePerspective(isViewingOther ? 'connection' : 'owner');
    }, [isViewingOther, targetUser?.id]);

    // ─── SPA Navigation Data Loader ───────────────────────────────────
    // When navigating to /grid/profile via client-side routing (e.g. from /grid home),
    // window.__GRID_DATA__ was set by index.php which does NOT include profile sections.
    // Detect this condition and fetch the full profile data from the API.
    useEffect(() => {
        const serverData = (typeof window !== 'undefined' && window.__GRID_DATA__) || {};
        const hasProfileSections = Array.isArray(serverData.experiences) ||
                                   Array.isArray(serverData.education) ||
                                   Array.isArray(serverData.skills) ||
                                   serverData.about;
        
        // If we already have section data from server hydration (profile.php), skip fetch
        if (hasProfileSections && !isViewingOther) return;

        const userIdToFetch = isViewingOther ? targetUser.id : null;

        gridApi.getProfile(userIdToFetch).then(data => {
            if (!data) return;
            
            const profile = data.profile || {};
            const user = data.user || {};

            // Parse about data from profile
            let aboutData = {};
            if (profile.about_json) {
                try {
                    aboutData = typeof profile.about_json === 'string' 
                        ? JSON.parse(profile.about_json) 
                        : profile.about_json;
                } catch(e) { /* ignore */ }
            }
            const aboutText = aboutData.text || profile.bio || '';
            const aboutSkills = Array.isArray(aboutData.skills) 
                ? aboutData.skills 
                : (profile.about_skills ? profile.about_skills.split(',').map(s => s.trim()).filter(Boolean) : []);

            setState(prev => ({
                ...prev,
                user: {
                    ...prev.user,
                    id: user.id || prev.user.id,
                    fullName: user.name || prev.user.fullName,
                    avatarSrc: user.avatar || prev.user.avatarSrc,
                    initials: user.initials || prev.user.initials,
                    headline: profile.headline || prev.user.headline,
                    pronoun: profile.pronoun || prev.user.pronoun,
                    location: profile.location || prev.user.location,
                    bio: aboutText || prev.user.bio,
                    bannerType: profile.banner_type || prev.user.bannerType || 'gradient',
                    bannerVideoSrc: profile.banner_video_src || prev.user.bannerVideoSrc || '',
                    bannerImageSrc: profile.banner_image_src || prev.user.bannerImageSrc || '',
                    bannerGradient: profile.banner_gradient || prev.user.bannerGradient || '',
                    bannerTag: profile.banner_tag || prev.user.bannerTag || '',
                    bannerTitle: profile.banner_title || prev.user.bannerTitle || '',
                    bannerUrl: profile.banner_url || prev.user.bannerUrl || '',
                    isWizardCompleted: profile.is_wizard_completed ? true : prev.user.isWizardCompleted,
                    openToWork: {
                        active: profile.is_open_to_work ? true : false,
                        roles: profile.otw_roles || prev.user.openToWork?.roles || 'Open to opportunities',
                        note: profile.otw_note || prev.user.openToWork?.note || 'Available'
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
                experiences: Array.isArray(data.experiences) ? data.experiences : (isViewingOther ? [] : prev.experiences),
                education: Array.isArray(data.education) ? data.education : (isViewingOther ? [] : prev.education),
                certifications: Array.isArray(data.certifications) ? data.certifications : (isViewingOther ? [] : prev.certifications),
                projects: Array.isArray(data.projects) ? data.projects : (isViewingOther ? [] : prev.projects),
                skills: Array.isArray(data.skills) ? data.skills : (isViewingOther ? [] : prev.skills),
                honors: Array.isArray(data.honors) ? data.honors : (isViewingOther ? [] : prev.honors),
                languages: Array.isArray(data.languages) ? data.languages : (isViewingOther ? [] : prev.languages),
                artifacts: Array.isArray(data.artifacts) ? data.artifacts : (isViewingOther ? [] : prev.artifacts)
            }));
        }).catch(err => {
            console.warn('GridProfileApp: Failed to fetch profile data via API:', err);
        });
    }, [isViewingOther, targetUser?.id]);

    // ─── Activity Feed: real posts + reposts for this profile ────────────
    useEffect(() => {
        const userIdToFetch = isViewingOther ? targetUser.id : null;

        gridApi.getUserPosts(userIdToFetch).then(posts => {
            if (!Array.isArray(posts)) return;
            setState(prev => ({
                ...prev,
                feed: posts
            }));
        }).catch(err => {
            console.warn('GridProfileApp: Failed to fetch activity feed posts:', err);
        });
    }, [isViewingOther, targetUser?.id]);

    useEffect(() => {
        const handleWizardCompleted = (e) => {
            const wizardData = e.detail;
            if (!wizardData) return;
            setState(prev => ({
                ...prev,
                user: {
                    ...prev.user,
                    fullName: wizardData.full_name || prev.user.fullName,
                    headline: wizardData.headline || prev.user.headline,
                    location: wizardData.location || prev.user.location,
                    openToWork: {
                        ...prev.user.openToWork,
                        active: wizardData.role === 'candidate'
                    }
                },
                about: {
                    ...prev.about,
                    text: wizardData.bio || prev.about.text
                },
                skills: Array.isArray(wizardData.skills) && wizardData.skills.length > 0
                    ? wizardData.skills.map((s, idx) => ({
                        id: `wizard-skill-${idx}`,
                        name: s,
                        endorsements: 1,
                        note: 'Added during onboarding'
                    }))
                    : prev.skills
            }));
        };
        window.addEventListener('grid:profile-wizard-completed', handleWizardCompleted);

        const handleAvatarUpdated = (e) => {
            const avatarUrl = e.detail;
            if (avatarUrl) {
                setState(prev => ({
                    ...prev,
                    user: {
                        ...prev.user,
                        avatarSrc: avatarUrl
                    }
                }));
            }
        };
        window.addEventListener('grid:avatar-updated', handleAvatarUpdated);

        return () => {
            window.removeEventListener('grid:profile-wizard-completed', handleWizardCompleted);
            window.removeEventListener('grid:avatar-updated', handleAvatarUpdated);
        };
    }, []);

    function showToast(msg) {
        if (onToast) onToast(msg);
    }

    // Perspective & Visibility Calculations: owner can edit, viewing other cannot edit
    const canEdit = !isViewingOther && activePerspective === 'owner';

    function isSectionVisible(sectionKey) {
        if (activePerspective === 'owner') return true;
        const rules = (state.sectionVisibility && state.sectionVisibility[sectionKey]) || { connections: true, recruiters: true, nonConnections: true };
        if (activePerspective === 'connection') return rules.connections !== false;
        if (activePerspective === 'recruiter') return rules.recruiters !== false;
        if (activePerspective === 'nonConnection') return rules.nonConnections !== false;
        return true;
    }

    function getVisibilityLabel(sectionKey) {
        const rules = (state.sectionVisibility && state.sectionVisibility[sectionKey]) || { connections: true, recruiters: true, nonConnections: true };
        if (!rules.connections && !rules.recruiters && !rules.nonConnections) return 'Hidden';
        if (rules.connections && rules.recruiters && rules.nonConnections) return 'Public';
        if (rules.connections && rules.recruiters && !rules.nonConnections) return 'Network Only';
        if (rules.connections && !rules.recruiters && !rules.nonConnections) return 'Connections Only';
        return 'Custom';
    }

    function getHiddenSectionsCount() {
        if (activePerspective === 'owner') return 0;
        const allKeys = ['about', 'artifacts', 'feed', 'experience', 'education', 'certifications', 'projects', 'skills', 'recommendations'];
        return allKeys.filter(k => !isSectionVisible(k)).length;
    }

    function handleOpenVisibility(sectionKey = 'about') {
        setVisibilityModalInitialSection(sectionKey);
        setActiveModal('sectionVisibility');
    }

    function handleSaveVisibility(newRules) {
        setState(prev => ({
            ...prev,
            sectionVisibility: newRules
        }));
        showToast('Profile privacy & visibility settings saved! 🔒');
    }

    // WebSocket real-time event listener
    const wsUrl = (typeof window !== 'undefined' && window.__GRID_DATA__ && window.__GRID_DATA__.wsUrl) 
        ? window.__GRID_DATA__.wsUrl 
        : null;

    useWebSocket(wsUrl, (event) => {
        if (!event || !event.type) return;
        if (event.type === 'SKILL_ENDORSED' && event.skillId) {
            setState(prev => ({
                ...prev,
                skills: prev.skills.map(s => s.id === event.skillId ? { ...s, endorsements: (s.endorsements || 0) + 1 } : s)
            }));
            showToast(`Real-time update: Skill endorsed! 🌟`);
        } else if (event.type === 'NEW_POST' && event.post) {
            setState(prev => ({
                ...prev,
                feed: [event.post, ...prev.feed]
            }));
            showToast(`New network post from ${event.post.authorName || 'colleague'}! 📢`);
        } else if (event.type === 'NOTIFICATION' && event.message) {
            showToast(event.message);
        }
    });

    // Handlers for Updating Profile Intro & Cover
    function handleUpdateCover(coverData) {
        setState(prev => ({
            ...prev,
            user: { ...prev.user, ...coverData }
        }));
        gridApi.saveProfile({
            banner_type: coverData.bannerType || (coverData.bannerVideoSrc ? 'video' : coverData.bannerImageSrc ? 'image' : 'gradient'),
            banner_video_src: coverData.bannerVideoSrc || '',
            banner_image_src: coverData.bannerImageSrc || '',
            banner_gradient: coverData.bannerGradient || '',
            banner_tag: coverData.bannerTag || '',
            banner_title: coverData.bannerTitle || '',
            banner_url: coverData.bannerUrl || ''
        });
        showToast('Cover banner updated successfully! 🎨');
    }

    function handleUpdateProfile(profileData) {
        const fullName = profileData.fullName || profileData.name || '';
        const initials = fullName ? fullName.trim().split(/\s+/).map(n => n[0]).join('').slice(0, 2).toUpperCase() : 'U';
        setState(prev => ({
            ...prev,
            user: { ...prev.user, ...profileData, initials }
        }));
        gridApi.saveProfile({
            full_name: fullName,
            pronoun: profileData.pronoun || '',
            headline: profileData.headline || '',
            location: profileData.location || ''
        });
        showToast('Profile intro saved! ✏️');
    }

    function handleUpdateOpenToWork(otwData) {
        setState(prev => ({
            ...prev,
            user: { ...prev.user, openToWork: otwData }
        }));
        gridApi.saveProfile({
            is_open_to_work: (otwData && otwData.active !== undefined) ? (otwData.active ? 1 : 0) : 1,
            roles: otwData?.roles || '',
            note: otwData?.note || '',
            otw_roles: otwData?.roles || '',
            otw_note: otwData?.note || ''
        });
        showToast('Open to work preferences updated! 💼');
    }

    // About handlers
    function handleUpdateAbout(aboutData) {
        const text = aboutData.text || (aboutData.paragraphs ? aboutData.paragraphs.join('\n\n') : '');
        const skills = Array.isArray(aboutData.skills) ? aboutData.skills : [];
        const paragraphs = aboutData.paragraphs || (text ? text.split('\n\n').map(p => p.trim()).filter(Boolean) : []);
        setState(prev => ({
            ...prev,
            about: { ...prev.about, ...aboutData, text, paragraphs, skills },
            user: { ...prev.user, bio: text }
        }));
        gridApi.saveProfile({
            bio: text,
            about_skills: skills.join(', '),
            about_json: {
                text,
                paragraphs,
                bullets: aboutData.bullets || [],
                closing: aboutData.closing || '',
                skills
            }
        });
        showToast('About section updated! 📝');
    }

    // Honors & Awards CRUD
    // Honors & Awards CRUD (1:N)
    function handleSaveHonor(honor) {
        setState(prev => {
            const existing = prev.honors || [];
            const idx = existing.findIndex(h => h.id === honor.id);
            const updated = idx >= 0
                ? existing.map(h => h.id === honor.id ? honor : h)
                : [...existing, honor];
            return { ...prev, honors: updated };
        });
        gridApi.saveSectionItem('honor', honor, honor.id).then(res => {
            if (res && res.id && (!honor.id || typeof honor.id === 'string')) {
                setState(prev => ({
                    ...prev,
                    honors: (prev.honors || []).map(h => h.id === honor.id ? { ...h, id: res.id } : h)
                }));
            }
        });
        showToast('Honor & Award saved successfully! 🏆');
    }

    function handleDeleteHonor(honorId) {
        setState(prev => ({
            ...prev,
            honors: (prev.honors || []).filter(h => h.id !== honorId)
        }));
        gridApi.deleteSectionItem('honor', honorId);
        showToast('Honor & Award removed.');
    }

    // Languages CRUD (1:N)
    function handleSaveLanguage(lang) {
        setState(prev => {
            const existing = prev.languages || [];
            const idx = existing.findIndex(l => l.id === lang.id);
            const updated = idx >= 0
                ? existing.map(l => l.id === lang.id ? lang : l)
                : [...existing, lang];
            return { ...prev, languages: updated };
        });
        gridApi.saveSectionItem('language', lang, lang.id).then(res => {
            if (res && res.id && (!lang.id || typeof lang.id === 'string')) {
                setState(prev => ({
                    ...prev,
                    languages: (prev.languages || []).map(l => l.id === lang.id ? { ...l, id: res.id } : l)
                }));
            }
        });
        showToast('Language saved! 🌐');
    }

    function handleDeleteLanguage(langId) {
        setState(prev => ({
            ...prev,
            languages: (prev.languages || []).filter(l => l.id !== langId)
        }));
        gridApi.deleteSectionItem('language', langId);
        showToast('Language removed.');
    }

    // Artifacts CRUD (1:N)
    function handleAddArtifact(newArtifact) {
        setState(prev => ({
            ...prev,
            artifacts: [newArtifact, ...prev.artifacts]
        }));
        gridApi.saveSectionItem('artifact', newArtifact).then(res => {
            if (res && res.id) {
                setState(prev => ({
                    ...prev,
                    artifacts: prev.artifacts.map(a => a.id === newArtifact.id ? { ...a, id: res.id } : a)
                }));
            }
        });
        showToast(`Artifact "${newArtifact.name}" added to showcase! 🚀`);
    }

    function handleUpdateArtifact(updatedArtifact) {
        setState(prev => ({
            ...prev,
            artifacts: prev.artifacts.map(a => a.id === updatedArtifact.id ? updatedArtifact : a)
        }));
        gridApi.saveSectionItem('artifact', updatedArtifact, updatedArtifact.id);
        showToast(`Artifact "${updatedArtifact.name}" updated! ✏️`);
    }

    function handleDeleteArtifact(artifactId) {
        setState(prev => ({
            ...prev,
            artifacts: prev.artifacts.filter(a => a.id !== artifactId)
        }));
        gridApi.deleteSectionItem('artifact', artifactId);
        showToast('Artifact removed from showcase.');
    }

    // Experience CRUD (1:N)
    function handleAddExperience(newExp) {
        setState(prev => ({
            ...prev,
            experiences: [newExp, ...prev.experiences]
        }));
        gridApi.saveSectionItem('experience', newExp).then(res => {
            if (res && res.id) {
                setState(prev => ({
                    ...prev,
                    experiences: prev.experiences.map(e => e.id === newExp.id ? { ...e, id: res.id } : e)
                }));
            }
        });
        showToast(`Experience at ${newExp.company} added! 💼`);
    }

    function handleUpdateExperience(updatedExp) {
        setState(prev => ({
            ...prev,
            experiences: prev.experiences.map(e => e.id === updatedExp.id ? updatedExp : e)
        }));
        gridApi.saveSectionItem('experience', updatedExp, updatedExp.id);
        showToast(`Experience at ${updatedExp.company} updated! ✏️`);
    }

    function handleDeleteExperience(expId) {
        setState(prev => ({
            ...prev,
            experiences: prev.experiences.filter(e => e.id !== expId)
        }));
        gridApi.deleteSectionItem('experience', expId);
        showToast('Experience entry removed.');
    }

    // Education & Certifications CRUD (1:N)
    function handleAddEducation(newEdu) {
        setState(prev => ({
            ...prev,
            education: [newEdu, ...prev.education]
        }));
        gridApi.saveSectionItem('education', newEdu).then(res => {
            if (res && res.id) {
                setState(prev => ({
                    ...prev,
                    education: prev.education.map(e => e.id === newEdu.id ? { ...e, id: res.id } : e)
                }));
            }
        });
        showToast(`Education at ${newEdu.name} added! 🎓`);
    }

    function handleUpdateEducation(updatedEdu) {
        setState(prev => ({
            ...prev,
            education: prev.education.map(e => e.id === updatedEdu.id ? updatedEdu : e)
        }));
        gridApi.saveSectionItem('education', updatedEdu, updatedEdu.id);
        showToast(`Education at ${updatedEdu.name} updated! ✏️`);
    }

    function handleDeleteEducation(eduId) {
        setState(prev => ({
            ...prev,
            education: prev.education.filter(e => e.id !== eduId)
        }));
        gridApi.deleteSectionItem('education', eduId);
        showToast('Education entry removed.');
    }

    function handleAddCertification(newCert) {
        setState(prev => ({
            ...prev,
            certifications: [newCert, ...prev.certifications]
        }));
        gridApi.saveSectionItem('certification', newCert).then(res => {
            if (res && res.id) {
                setState(prev => ({
                    ...prev,
                    certifications: prev.certifications.map(c => c.id === newCert.id ? { ...c, id: res.id } : c)
                }));
            }
        });
        showToast(`Certification "${newCert.name}" added! 📜`);
    }

    function handleUpdateCertification(updatedCert) {
        setState(prev => ({
            ...prev,
            certifications: prev.certifications.map(c => c.id === updatedCert.id ? updatedCert : c)
        }));
        gridApi.saveSectionItem('certification', updatedCert, updatedCert.id);
        showToast(`Certification "${updatedCert.name}" updated! ✏️`);
    }

    function handleDeleteCertification(certId) {
        setState(prev => ({
            ...prev,
            certifications: prev.certifications.filter(c => c.id !== certId)
        }));
        gridApi.deleteSectionItem('certification', certId);
        showToast('Certification entry removed.');
    }

    // Project CRUD (1:N)
    function handleAddProject(newProj) {
        setState(prev => ({
            ...prev,
            projects: [newProj, ...prev.projects]
        }));
        gridApi.saveSectionItem('project', newProj).then(res => {
            if (res && res.id) {
                setState(prev => ({
                    ...prev,
                    projects: prev.projects.map(p => p.id === newProj.id ? { ...p, id: res.id } : p)
                }));
            }
        });
        showToast(`Project "${newProj.name}" added! 💻`);
    }

    function handleUpdateProject(updatedProj) {
        setState(prev => ({
            ...prev,
            projects: prev.projects.map(p => p.id === updatedProj.id ? updatedProj : p)
        }));
        gridApi.saveSectionItem('project', updatedProj, updatedProj.id);
        showToast(`Project "${updatedProj.name}" updated! ✏️`);
    }

    function handleDeleteProject(projId) {
        setState(prev => ({
            ...prev,
            projects: prev.projects.filter(p => p.id !== projId)
        }));
        gridApi.deleteSectionItem('project', projId);
        showToast('Project removed.');
    }

    // Skill CRUD
    function handleAddSkill(newSkill) {
        setState(prev => ({
            ...prev,
            skills: [...prev.skills, newSkill]
        }));
        gridApi.saveSectionItem('skill', newSkill).then(res => {
            if (res && res.id) {
                setState(prev => ({
                    ...prev,
                    skills: prev.skills.map(s => s.id === newSkill.id ? { ...s, id: res.id } : s)
                }));
            }
        });
        showToast(`Skill "${newSkill.name}" added! 🌟`);
    }

    function handleUpdateSkill(updatedSkill) {
        setState(prev => ({
            ...prev,
            skills: prev.skills.map(s => s.id === updatedSkill.id ? updatedSkill : s)
        }));
        gridApi.saveSectionItem('skill', updatedSkill, updatedSkill.id);
        showToast(`Skill "${updatedSkill.name}" updated! ✏️`);
    }

    function handleDeleteSkill(skillId) {
        setState(prev => ({
            ...prev,
            skills: prev.skills.filter(s => s.id !== skillId)
        }));
        gridApi.deleteSectionItem('skill', skillId);
        showToast('Skill removed.');
    }

    // Feed Post Handler
    function handleCreatePost(newPost) {
        setState(prev => ({
            ...prev,
            feed: [newPost, ...prev.feed]
        }));
        gridApi.createPost(newPost);
        showToast('Post published to your network! 📢');
    }

    // Modal Routing dispatcher for "Add Section" dropdown
    function handleAddSection(type) {
        setItemToEdit(null);
        if (type === 'experience') setActiveModal('addExperience');
        else if (type === 'education') setActiveModal('addEducation');
        else if (type === 'project') setActiveModal('addProject');
        else if (type === 'certification') {
            setItemToEdit({ itemType: 'certification' });
            setActiveModal('addEducation');
        }
        else if (type === 'skill') setActiveModal('addSkill');
        else if (type === 'honor' || type === 'honors') setActiveModal('addHonor');
        else if (type === 'language' || type === 'languages') setActiveModal('addLanguage');
        else showToast(`Opening ${type} modal`);
    }

    // Individual Edit Launchers
    function startEditExperience(exp) {
        setItemToEdit(exp);
        setActiveModal('addExperience');
    }

    function startEditEducation(edu) {
        setItemToEdit({ ...edu, itemType: 'education' });
        setActiveModal('addEducation');
    }

    function startEditCertification(cert) {
        setItemToEdit({ ...cert, itemType: 'certification' });
        setActiveModal('addEducation');
    }

    function startEditProject(proj) {
        setItemToEdit(proj);
        setActiveModal('addProject');
    }

    function startEditSkill(skill) {
        setItemToEdit(skill);
        setActiveModal('addSkill');
    }

    function startEditArtifact(art) {
        setItemToEdit(art);
        setActiveModal('addArtifact');
    }

    const showEducation = isSectionVisible('education');
    const showCertifications = isSectionVisible('certifications');

    return (
        <div className="w-full">
            {/* Main Profile Container */}
            <div className="g-container">
                <div className="g-grid">
                    {/* Center Profile Stream */}
                    <main id="gMainProfileStream">
                        {/* ═══ 1. Profile Hero (Banner, Avatar, Identity, Progress) ═══ */}
                        <ProfileHero 
                            user={state.user}
                            canEdit={canEdit}
                            onNavigate={onNavigate}
                            onEditCover={() => { setItemToEdit(null); setActiveModal('editCover'); }}
                            onEditAvatar={() => { setItemToEdit(null); setActiveModal('profilePhoto'); }}
                            onEditProfile={() => { setItemToEdit(null); setActiveModal('editProfile'); }}
                            onOpenToWork={() => { setItemToEdit(null); setActiveModal('openToWork'); }}
                            onAddSection={handleAddSection}
                            onContactInfo={() => setActiveModal('contactInfo')}
                            onEditViews={() => handleOpenVisibility('about')}
                            onRequestRecommendation={() => showToast('Recommendation request sent! 🤝')}
                            onToast={showToast}
                        />

                        {/* ═══ Perspective Preview Bar (Owner | Connection | Recruiter | Non-Connection) ═══ */}
                        {!isViewingOther && (
                            <ProfilePerspectiveBar 
                                activePerspective={activePerspective}
                                onPerspectiveChange={setActivePerspective}
                                onOpenVisibilityModal={() => handleOpenVisibility('about')}
                                hiddenSectionsCount={getHiddenSectionsCount()}
                            />
                        )}

                        {/* ═══ 2. About Card ═══ */}
                        {isSectionVisible('about') && (
                            <AboutCard 
                                about={state.about}
                                canEdit={canEdit}
                                onEditAbout={() => { setItemToEdit(null); setActiveModal('editAbout'); }}
                                onManageVisibility={() => handleOpenVisibility('about')}
                                visibilityInfo={getVisibilityLabel('about')}
                            />
                        )}

                        {/* ═══ 3. Featured Artifacts ═══ */}
                        {isSectionVisible('artifacts') && (
                            <FeaturedArtifacts 
                                artifacts={state.artifacts}
                                canEdit={canEdit}
                                onAddArtifact={() => { setItemToEdit(null); setActiveModal('addArtifact'); }}
                                onEditArtifact={startEditArtifact}
                                onManageVisibility={() => handleOpenVisibility('artifacts')}
                                visibilityInfo={getVisibilityLabel('artifacts')}
                            />
                        )}

                        {/* ═══ 4. Activity Feed ═══ */}
                        {isSectionVisible('feed') && (
                            <ActivityFeed
                                posts={state.feed}
                                followersCount={state.user.followersCount || 540}
                                canEdit={canEdit}
                                onCreatePost={() => { setItemToEdit(null); setActiveModal('createPost'); }}
                                onManageVisibility={() => handleOpenVisibility('feed')}
                                visibilityInfo={getVisibilityLabel('feed')}
                                onNavigate={onNavigate}
                                onToast={showToast}
                            />
                        )}

                        {/* ═══ 5. Work Experience Timeline ═══ */}
                        {isSectionVisible('experience') && (
                            <ExperienceList 
                                experiences={state.experiences}
                                canEdit={canEdit}
                                onAddExperience={() => { setItemToEdit(null); setActiveModal('addExperience'); }}
                                onEditExperience={startEditExperience}
                                onManageVisibility={() => handleOpenVisibility('experience')}
                                visibilityInfo={getVisibilityLabel('experience')}
                            />
                        )}

                        {/* ═══ 6. Academic Education & Certifications ═══ */}
                        {(showEducation || showCertifications) && (
                            <EducationList 
                                educations={state.education}
                                certifications={state.certifications}
                                canEdit={canEdit}
                                showEducation={showEducation}
                                showCertifications={showCertifications}
                                onAddEducation={() => { setItemToEdit({ itemType: 'education' }); setActiveModal('addEducation'); }}
                                onEditEducation={startEditEducation}
                                onAddCertification={() => { setItemToEdit({ itemType: 'certification' }); setActiveModal('addEducation'); }}
                                onEditCertification={startEditCertification}
                                onManageEduVisibility={() => handleOpenVisibility('education')}
                                eduVisibilityInfo={getVisibilityLabel('education')}
                                onManageCertVisibility={() => handleOpenVisibility('certifications')}
                                certVisibilityInfo={getVisibilityLabel('certifications')}
                            />
                        )}

                        {/* ═══ 7. Key Projects ═══ */}
                        {isSectionVisible('projects') && (
                            <ProjectsList 
                                projects={state.projects}
                                canEdit={canEdit}
                                onAddProject={() => { setItemToEdit(null); setActiveModal('addProject'); }}
                                onEditProject={startEditProject}
                                onManageVisibility={() => handleOpenVisibility('projects')}
                                visibilityInfo={getVisibilityLabel('projects')}
                            />
                        )}

                        {/* ═══ 8. Skills & Endorsements (+1 action) ═══ */}
                        {isSectionVisible('skills') && (
                            <SkillsGrid 
                                skills={state.skills}
                                canEdit={canEdit}
                                onAddSkill={() => { setItemToEdit(null); setActiveModal('addSkill'); }}
                                onEditSkill={startEditSkill}
                                onManageVisibility={() => handleOpenVisibility('skills')}
                                visibilityInfo={getVisibilityLabel('skills')}
                                onToast={showToast}
                            />
                        )}

                        {/* ═══ 9. Recommendations, Honors & Languages ═══ */}
                        {isSectionVisible('recommendations') && (
                            <Recommendations 
                                canEdit={canEdit}
                                honors={state.honors}
                                recommendations={state.recommendations}
                                languages={state.languages}
                                onRequestRecommendation={() => showToast('Recommendation request sent! 🤝')}
                                onAddHonor={() => { setItemToEdit(null); setActiveModal('addHonor'); }}
                                onEditHonor={(honor) => { setItemToEdit(honor); setActiveModal('addHonor'); }}
                                onAddLanguage={() => { setItemToEdit(null); setActiveModal('addLanguage'); }}
                                onEditLanguage={(lang) => { setItemToEdit(lang); setActiveModal('addLanguage'); }}
                                onManageVisibility={() => handleOpenVisibility('recommendations')}
                                visibilityInfo={getVisibilityLabel('recommendations')}
                            />
                        )}
                    </main>

                    {/* Right Analytics & Connections Sidebar */}
                    <RightSidebar 
                        analytics={state.analytics} 
                        user={state.user} 
                    />
                </div>
            </div>

            {/* ═══ React Modal Dialogs ═══ */}
            <EditCoverModal 
                isOpen={activeModal === 'editCover'}
                onClose={() => { setActiveModal(null); setItemToEdit(null); }}
                user={state.user}
                onSave={handleUpdateCover}
            />

            <EditProfileModal 
                isOpen={activeModal === 'editProfile'}
                onClose={() => { setActiveModal(null); setItemToEdit(null); }}
                user={state.user}
                onSave={handleUpdateProfile}
            />

            <OpenToWorkModal 
                isOpen={activeModal === 'openToWork'}
                onClose={() => { setActiveModal(null); setItemToEdit(null); }}
                openToWork={state.user.openToWork}
                onSave={handleUpdateOpenToWork}
            />

            <EditAboutModal 
                isOpen={activeModal === 'editAbout'}
                onClose={() => { setActiveModal(null); setItemToEdit(null); }}
                about={state.about}
                onSave={handleUpdateAbout}
            />

            <AddArtifactModal 
                isOpen={activeModal === 'addArtifact'}
                onClose={() => { setActiveModal(null); setItemToEdit(null); }}
                itemToEdit={itemToEdit}
                onAdd={handleAddArtifact}
                onUpdate={handleUpdateArtifact}
                onDelete={handleDeleteArtifact}
            />

            <AddExperienceModal 
                isOpen={activeModal === 'addExperience'}
                onClose={() => { setActiveModal(null); setItemToEdit(null); }}
                itemToEdit={itemToEdit}
                onAdd={handleAddExperience}
                onUpdate={handleUpdateExperience}
                onDelete={handleDeleteExperience}
            />

            <AddEducationModal 
                isOpen={activeModal === 'addEducation'}
                onClose={() => { setActiveModal(null); setItemToEdit(null); }}
                itemToEdit={itemToEdit}
                isCertification={itemToEdit?.itemType === 'certification'}
                onAdd={(item) => {
                    if (itemToEdit?.itemType === 'certification' || item?.issuer !== undefined) {
                        handleAddCertification(item);
                    } else {
                        handleAddEducation(item);
                    }
                }}
                onUpdate={(item) => {
                    if (itemToEdit?.itemType === 'certification' || item?.issuer !== undefined) {
                        handleUpdateCertification(item);
                    } else {
                        handleUpdateEducation(item);
                    }
                }}
                onDelete={(id) => {
                    if (itemToEdit?.itemType === 'certification' || itemToEdit?.issuer !== undefined) {
                        handleDeleteCertification(id);
                    } else {
                        handleDeleteEducation(id);
                    }
                }}
                onAddEducation={handleAddEducation}
                onUpdateEducation={handleUpdateEducation}
                onDeleteEducation={handleDeleteEducation}
                onAddCertification={handleAddCertification}
                onUpdateCertification={handleUpdateCertification}
                onDeleteCertification={handleDeleteCertification}
            />

            <AddProjectModal 
                isOpen={activeModal === 'addProject'}
                onClose={() => { setActiveModal(null); setItemToEdit(null); }}
                itemToEdit={itemToEdit}
                onAdd={handleAddProject}
                onUpdate={handleUpdateProject}
                onDelete={handleDeleteProject}
            />

            <AddSkillModal 
                isOpen={activeModal === 'addSkill'}
                onClose={() => { setActiveModal(null); setItemToEdit(null); }}
                itemToEdit={itemToEdit}
                onAdd={handleAddSkill}
                onUpdate={handleUpdateSkill}
                onDelete={handleDeleteSkill}
            />

            <CreatePostModal 
                isOpen={activeModal === 'createPost'}
                onClose={() => { setActiveModal(null); setItemToEdit(null); }}
                authorName={state.user.fullName}
                onPublish={handleCreatePost}
            />

            <ContactInfoModal 
                isOpen={activeModal === 'contactInfo'}
                onClose={() => { setActiveModal(null); setItemToEdit(null); }}
                user={state.user}
                onToast={showToast}
            />

            <AddHonorModal
                isOpen={activeModal === 'addHonor'}
                onClose={() => { setActiveModal(null); setItemToEdit(null); }}
                itemToEdit={itemToEdit}
                onSave={handleSaveHonor}
                onDelete={handleDeleteHonor}
            />

            <AddLanguageModal
                isOpen={activeModal === 'addLanguage'}
                onClose={() => { setActiveModal(null); setItemToEdit(null); }}
                itemToEdit={itemToEdit}
                onSave={handleSaveLanguage}
                onDelete={handleDeleteLanguage}
            />

            <ProfilePhotoModal
                isOpen={activeModal === 'profilePhoto'}
                onClose={() => { setActiveModal(null); }}
                user={state.user}
                canEdit={canEdit}
                onAvatarUpdated={(url) => {
                    setState(prev => ({
                        ...prev,
                        user: { ...prev.user, avatarSrc: url }
                    }));
                }}
                onToast={showToast}
            />

            <SectionVisibilityModal 
                isOpen={activeModal === 'sectionVisibility'}
                onClose={() => { setActiveModal(null); setItemToEdit(null); }}
                sectionVisibility={state.sectionVisibility}
                initialSection={visibilityModalInitialSection}
                onSaveVisibility={handleSaveVisibility}
            />
        </div>
    );
}

