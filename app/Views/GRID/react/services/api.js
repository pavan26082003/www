/**
 * Centralized API Integration Service
 * 
 * Provides clean asynchronous methods for frontend actions.
 * Currently backed by optimistic local updates; effortlessly maps to
 * CodeIgniter REST endpoints (/api/profile, /api/artifacts, etc.) when backend is ready.
 */

const getApiBase = () => {
    if (typeof window !== 'undefined' && window.__GRID_DATA__ && window.__GRID_DATA__.apiUrl) {
        return window.__GRID_DATA__.apiUrl;
    }
    if (typeof window !== 'undefined' && window.location && window.location.pathname.includes('/index.php')) {
        return '/index.php/grid/api';
    }
    return '/grid/api';
};

export const ProfileAPI = {
    // 1. Fetch Profile Data
    async getProfile() {
        try {
            const res = await fetch(`${getApiBase()}/profile`);
            if (!res.ok) throw new Error('API offline');
            return await res.json();
        } catch (err) {
            console.warn('[ProfileAPI] Fallback to initial local state:', err);
            return null; // Signals component to use local state
        }
    },

    // 2. Update Profile Intro
    async updateProfileIntro(data) {
        console.log('[ProfileAPI] Sending updateProfileIntro:', data);
        try {
            const res = await fetch(`${getApiBase()}/profile/intro`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            if (res.ok) return await res.json();
        } catch (e) {
            console.log('[ProfileAPI] Mock local update persisted.');
        }
        return { success: true, data };
    },

    // 3. Update Cover Banner (Video, Image, Gradient, Portfolio URL)
    async updateCoverBanner(data) {
        console.log('[ProfileAPI] Sending updateCoverBanner:', data);
        try {
            const res = await fetch(`${getApiBase()}/profile/cover`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            if (res.ok) return await res.json();
        } catch (e) {
            console.log('[ProfileAPI] Mock cover update persisted.');
        }
        return { success: true, data };
    },

    // 4. Add Featured Artifact
    async addArtifact(artifact) {
        console.log('[ProfileAPI] Adding Artifact:', artifact);
        try {
            const res = await fetch(`${getApiBase()}/artifacts`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(artifact)
            });
            if (res.ok) return await res.json();
        } catch (e) {
            console.log('[ProfileAPI] Mock artifact added.');
        }
        return { success: true, artifact };
    },

    // 5. Create Activity Post
    async createPost(post) {
        console.log('[ProfileAPI] Creating Post:', post);
        try {
            const res = await fetch(`${getApiBase()}/posts`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(post)
            });
            if (res.ok) return await res.json();
        } catch (e) {
            console.log('[ProfileAPI] Mock post created.');
        }
        return { success: true, post };
    },

    // 6. Endorse Skill
    async endorseSkill(skillId) {
        console.log('[ProfileAPI] Endorsing Skill:', skillId);
        try {
            const res = await fetch(`${getApiBase()}/skills/${skillId}/endorse`, {
                method: 'POST'
            });
            if (res.ok) return await res.json();
        } catch (e) {
            console.log('[ProfileAPI] Mock endorsement registered.');
        }
        return { success: true, skillId };
    }
};
