/**
 * gridApi.js
 * High-performance, lightweight API client for the GRID Professional Platform.
 * Connects React views directly to CodeIgniter 4 GridApiController endpoints.
 * 
 * Features:
 * - Automatic base URL resolution (supports /grid/api and /index.php/grid/api)
 * - Safe JSON parsing with graceful fallbacks
 * - Standardized error handling
 */

const getApiBase = () => {
    if (typeof window !== 'undefined' && window.location) {
        const isIndexPhp = window.location.pathname.includes('/index.php');
        return isIndexPhp ? '/index.php/grid/api' : '/grid/api';
    }
    return '/grid/api';
};

const request = async (endpoint, options = {}) => {
    const url = `${getApiBase()}${endpoint}`;
    const defaultHeaders = {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    };

    if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
        defaultHeaders['Content-Type'] = 'application/json';
        options.body = JSON.stringify(options.body);
    }

    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                ...defaultHeaders,
                ...options.headers
            }
        });

        const data = await response.json();
        return data;
    } catch (err) {
        console.warn(`Grid API request failed [${endpoint}]:`, err);
        return {
            success: false,
            message: err.message || 'Network request failed'
        };
    }
};

export const gridApi = {
    // 1. Skills Autocomplete
    suggestSkills: async (query) => {
        const res = await request(`/skills/suggest?q=${encodeURIComponent(query || '')}`);
        return res.success ? res.data : [];
    },

    searchUsers: async (query) => {
        const res = await request(`/search/users?q=${encodeURIComponent(query || '')}`);
        return res.success ? res.data : [];
    },

    // 2. Profile & Identity
    getProfile: async (userId = null) => {
        const endpoint = userId ? `/profile/${userId}` : '/profile';
        const res = await request(endpoint);
        return res.success ? res.data : null;
    },

    saveProfile: async (profileData) => {
        return await request('/profile/save', {
            method: 'POST',
            body: profileData
        });
    },

    uploadAvatar: async (file) => {
        const formData = new FormData();
        formData.append('avatar', file);
        return await request('/profile/upload-avatar', {
            method: 'POST',
            body: formData
        });
    },

    uploadBanner: async (file) => {
        const formData = new FormData();
        formData.append('banner', file);
        return await request('/profile/upload-banner', {
            method: 'POST',
            body: formData
        });
    },

    completeWizardSetup: async (wizardData) => {
        return await request('/profile/wizard-setup', {
            method: 'POST',
            body: wizardData
        });
    },

    saveSectionItem: async (section, item, id = null) => {
        return await request('/profile/section/save', {
            method: 'POST',
            body: { section, item, id: id || (item && item.id) }
        });
    },

    deleteSectionItem: async (section, id) => {
        return await request('/profile/section/delete', {
            method: 'POST',
            body: { section, id }
        });
    },

    // 3. Network & Connections
    getConnections: async () => {
        const res = await request('/connections');
        return res.success ? res.data : [];
    },

    requestConnection: async (targetUserId) => {
        const formData = new FormData();
        formData.append('target_user_id', targetUserId);
        return await request('/connections/request', {
            method: 'POST',
            body: formData
        });
    },

    respondConnection: async (targetUserId, action) => {
        const formData = new FormData();
        formData.append('target_user_id', targetUserId);
        formData.append('action', action); // 'accept' | 'decline'
        return await request('/connections/respond', {
            method: 'POST',
            body: formData
        });
    },

    // 4. Jobs & Application Tracker
    getJobs: async (filters = {}) => {
        const query = new URLSearchParams();
        if (filters.discipline) query.append('discipline', filters.discipline);
        if (filters.workplace) query.append('workplace', filters.workplace);

        const res = await request(`/jobs?${query.toString()}`);
        return res.success ? res.data : [];
    },

    applyForJob: async (jobId, coverNote = '') => {
        const formData = new FormData();
        formData.append('job_id', jobId);
        formData.append('cover_note', coverNote);
        return await request('/jobs/apply', {
            method: 'POST',
            body: formData
        });
    },

    getApplicationTracker: async () => {
        const res = await request('/jobs/tracker');
        return res.success ? res.data : [];
    },

    // 5. Recruiter Mode & ATS Pipeline
    createJob: async (jobData) => {
        return await request('/jobs/create', {
            method: 'POST',
            body: jobData
        });
    },

    getRecruiterJobs: async () => {
        const res = await request('/recruiter/jobs');
        return res.success ? res.data : [];
    },

    getJobApplicants: async (jobId) => {
        const res = await request(`/recruiter/jobs/${jobId}/applicants`);
        return res.success ? res.data : [];
    },

    updateApplicationStatus: async (applicationId, status) => {
        return await request('/recruiter/applications/status', {
            method: 'POST',
            body: { application_id: applicationId, status }
        });
    },

    // 6. Circle Connect & Direct Messaging (Universal Handle & grid_id)
    getConversations: async () => {
        const res = await request('/messages/conversations');
        return res.success ? res.data : [];
    },

    getThread: async (conversationId) => {
        const res = await request(`/messages/thread/${conversationId}`);
        return res.success ? res.data : null;
    },

    sendMessage: async (recipientId, messageText, recipientGridId = null) => {
        return await request('/messages/send', {
            method: 'POST',
            body: {
                recipient_id: recipientId,
                recipient_grid_id: recipientGridId,
                message: messageText
            }
        });
    },

    startConversation: async (targetIdentifier) => {
        const res = await request(`/messages/start/${encodeURIComponent(targetIdentifier)}`);
        return res.success ? res.data : null;
    },

    // 7. Posts & Feed Stream
    getPosts: async (limit = 20, offset = 0) => {
        const res = await request(`/posts?limit=${limit}&offset=${offset}`);
        return res.success ? res.data : [];
    },

    getUserPosts: async (userId = null, limit = 20, offset = 0) => {
        const endpoint = userId ? `/posts/user/${userId}` : '/posts/user';
        const res = await request(`${endpoint}?limit=${limit}&offset=${offset}`);
        return res.success ? res.data : [];
    },

    createPost: async (postData) => {
        return await request('/posts/create', {
            method: 'POST',
            body: postData
        });
    },

    deletePost: async (postId) => {
        return await request('/posts/delete', {
            method: 'POST',
            body: { id: postId }
        });
    },

    toggleLike: async (postId) => {
        return await request('/posts/like', {
            method: 'POST',
            body: { post_id: postId }
        });
    },

    toggleRepost: async (postId) => {
        return await request('/posts/repost', {
            method: 'POST',
            body: { post_id: postId }
        });
    },

    getComments: async (postId) => {
        const res = await request(`/posts/${postId}/comments`);
        return res.success ? res.data : [];
    },

    addComment: async (postId, text) => {
        return await request('/posts/comment', {
            method: 'POST',
            body: { post_id: postId, comment: text }
        });
    },

    deleteComment: async (commentId) => {
        return await request(`/posts/comment/${commentId}`, {
            method: 'DELETE'
        });
    },

    // 8. Universal Media & File Upload
    uploadFile: async (file, type = 'post') => {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('type', type);
        return await request('/upload', {
            method: 'POST',
            body: formData
        });
    },

    // 9. Company-Sponsored Community Challenges & Events
    getTopChallenges: async (limit = 6) => {
        const res = await request(`/challenges/top?limit=${limit}`);
        return (res && res.success && Array.isArray(res.data)) ? res.data : [];
    },

    createChallenge: async (challengeData) => {
        return await request('/challenges/create', {
            method: 'POST',
            body: challengeData
        });
    }
};

export default gridApi;
