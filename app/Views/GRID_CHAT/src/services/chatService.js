// Service to communicate with PHP CodeIgniter backend (GridChatController)

const getAppData = () => {
  if (typeof window !== 'undefined' && window.__APP_DATA__) {
    return window.__APP_DATA__;
  }
  return {
    currentUser: null,
    apiBase: '/grid-chat/api',
    csrfTokenName: '',
    csrfHash: '',
  };
};

export const getSessionUser = () => {
  const appData = getAppData();
  return appData.currentUser;
};

export const getApiBase = () => {
  const appData = getAppData();
  return appData.apiBase || '/grid-chat/api';
};

export const getInitialRoute = () => {
  const appData = getAppData();
  return appData.initialRoute || '';
};

export const fetchCurrentUser = async () => {
  try {
    const res = await fetch(`${getApiBase()}/current_user`);
    if (!res.ok) return null;
    const json = await res.json();
    return json.success ? json.data : null;
  } catch (err) {
    console.warn('Could not fetch current user from API, using fallback', err);
    return null;
  }
};

// Read JSON from an API response and keep the CSRF token fresh
const readJson = async (res, action) => {
  let json = null;
  try {
    json = await res.json();
  } catch {
    // non-JSON response
  }
  if (json?.csrf_hash && window.__APP_DATA__) {
    window.__APP_DATA__.csrfHash = json.csrf_hash;
  }
  if (!json) throw new Error(`HTTP error ${res.status} on ${action}`);
  return json;
};

// GET an API action; empty params are left out. Resolves to { success, data, message }
export const getJson = async (action, params = {}) => {
  const query = new URLSearchParams(
    Object.entries(params).filter(([, value]) => value !== undefined && value !== null && value !== '')
  ).toString();

  try {
    const res = await fetch(`${getApiBase()}/${action}${query ? `?${query}` : ''}`, {
      credentials: 'same-origin',
    });
    return await readJson(res, action);
  } catch (err) {
    console.error(`API ${action} failed`, err);
    return { success: false, message: 'Network error', error: err.message };
  }
};

// POST a FormData to an API action with the CSRF token
export const postForm = async (action, formData) => {
  const appData = getAppData();
  if (appData.csrfTokenName && appData.csrfHash) {
    formData.append(appData.csrfTokenName, appData.csrfHash);
  }

  try {
    const res = await fetch(`${getApiBase()}/${action}`, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin',
    });
    return await readJson(res, action);
  } catch (err) {
    console.error(`API ${action} failed`, err);
    return { success: false, message: 'Network error', error: err.message };
  }
};

export const toFormData = (fields) => {
  const formData = new FormData();
  Object.entries(fields).forEach(([key, value]) => {
    if (value !== undefined && value !== null) {
      formData.append(key, value);
    }
  });
  return formData;
};

// ---------------------------------------------------------------------------
// Chat (grid_chat_db) — ChatController
// ---------------------------------------------------------------------------

// Unique id per outgoing message so a retry never creates a duplicate
export const createClientId = () => {
  if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
    return crypto.randomUUID();
  }
  // crypto.randomUUID is missing on plain-http LAN addresses
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (char) => {
    const random = Math.floor(Math.random() * 16);
    return (char === 'x' ? random : (random % 4) + 8).toString(16);
  });
};

export const fetchConversations = () => getJson('chat/conversations');

export const fetchConversationMessages = (conversationId, beforeId) =>
  getJson('chat/messages', { conversation_id: conversationId, before_id: beforeId });

export const inviteCall = ({ callId, calleeUserId, conversationId, callType }) =>
  postForm('chat/call_invite', toFormData({
    call_id: callId,
    callee_user_id: calleeUserId || '',
    conversation_id: conversationId || '',
    call_type: callType,
  }));

export const sendConversationMessage = ({ conversationId, clientId, messageType, body, metadata, replyToMessageId }) =>
  postForm('chat/send', toFormData({
    conversation_id: conversationId,
    client_id: clientId,
    message_type: messageType,
    body: body ?? '',
    metadata: metadata ? JSON.stringify(metadata) : undefined,
    reply_to_message_id: replyToMessageId,
  }));

/**
 * Upload a photo, video, audio file, voice note or document as a message.
 * Uses XHR for upload progress; onProgress receives 0-100.
 * Resolves to { success, data: { message }, message } like the other API calls.
 */
export const uploadConversationMedia = (
  { conversationId, clientId, messageType, file, fileName, body, duration },
  onProgress
) =>
  new Promise((resolve) => {
    const appData = getAppData();
    const formData = toFormData({
      conversation_id: conversationId,
      client_id: clientId,
      message_type: messageType,
      body: body ?? '',
      duration,
    });
    formData.append('file', file, fileName || file.name || 'upload');
    if (appData.csrfTokenName && appData.csrfHash) {
      formData.append(appData.csrfTokenName, appData.csrfHash);
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', `${getApiBase()}/media/upload`);
    xhr.withCredentials = true;

    xhr.upload.onprogress = (event) => {
      if (event.lengthComputable) {
        onProgress?.(Math.round((event.loaded / event.total) * 100));
      }
    };

    xhr.onload = () => {
      let json = null;
      try {
        json = JSON.parse(xhr.responseText);
      } catch {
        // non-JSON response (e.g. request larger than the server allows)
      }
      if (json?.csrf_hash && window.__APP_DATA__) {
        window.__APP_DATA__.csrfHash = json.csrf_hash;
      }
      resolve(
        json || {
          success: false,
          message: xhr.status === 413 ? 'File is too large' : `Upload failed (${xhr.status})`,
        }
      );
    };

    xhr.onerror = () => resolve({ success: false, message: 'Network error' });
    xhr.send(formData);
  });

export const markConversationRead = (conversationId, messageId) =>
  postForm('chat/read', toFormData({ conversation_id: conversationId, message_id: messageId }));

// Per-user chat settings (only affect the current user). Each resolves to { success, data: { conversation } }.
export const setConversationFavourite = (conversationId, isFavourite) =>
  postForm('chat/favourite', toFormData({ conversation_id: conversationId, is_favourite: isFavourite ? 1 : 0 }));

// duration: "8h" | "1w" | "always" | "off"
export const muteConversation = (conversationId, duration) =>
  postForm('chat/mute', toFormData({ conversation_id: conversationId, duration }));

export const clearConversation = (conversationId) =>
  postForm('chat/clear', toFormData({ conversation_id: conversationId }));

// Resolves to { success, data: { conversationId, deleted: true } }
export const deleteConversation = (conversationId) =>
  postForm('chat/delete_chat', toFormData({ conversation_id: conversationId }));

/**
 * Delete messages. mode "everyone" soft-deletes your own messages for both
 * sides; mode "me" hides them for you only.
 * Resolves to { success, data: { mode, messageIds } }.
 */
export const deleteMessages = (messageIds, mode) =>
  postForm('chat/delete_message', toFormData({ message_ids: messageIds.join(','), mode }));

// Resolves to { success, data: { messageId, conversationId, pollData } }
export const votePoll = (messageId, optionIndex) =>
  postForm('chat/vote_poll', toFormData({ message_id: messageId, option_index: optionIndex }));

// ---------------------------------------------------------------------------
// Groups (GroupController). Member calls resolve to
// { success, data: { conversationId, members, memberCount, isAdmin } }.
// ---------------------------------------------------------------------------

// Resolves to { success, data: { conversation } }
export const createGroupChat = (title, userIds) =>
  postForm('group/create', toFormData({ title, user_ids: userIds.join(',') }));

export const fetchGroupMembers = (conversationId) =>
  getJson('group/members', { conversation_id: conversationId });

export const addGroupMembers = (conversationId, userIds) =>
  postForm('group/add_members', toFormData({ conversation_id: conversationId, user_ids: userIds.join(',') }));

export const removeGroupMember = (conversationId, userId) =>
  postForm('group/remove_member', toFormData({ conversation_id: conversationId, user_id: userId }));

export const setGroupAdmin = (conversationId, userId, isAdmin) =>
  postForm('group/set_admin', toFormData({ conversation_id: conversationId, user_id: userId, is_admin: isAdmin ? 1 : 0 }));

// Resolves to { success, data: { conversationId, left: true } }
export const leaveGroup = (conversationId) =>
  postForm('group/leave', toFormData({ conversation_id: conversationId }));

// Resolves to { success, data: { conversation } }
export const renameGroup = (conversationId, title) =>
  postForm('group/rename', toFormData({ conversation_id: conversationId, title }));

// Resolves to { success, data: { conversation } }
export const uploadGroupPhoto = (conversationId, file) => {
  const formData = toFormData({ conversation_id: conversationId });
  formData.append('photo', file, file.name || 'group-photo');
  return postForm('group/photo', formData);
};

export const openDirectChat = (userId) =>
  postForm('chat/open_direct', toFormData({ user_id: userId }));

export const searchChatContacts = (query, offset) =>
  getJson('chat/contacts', { q: query, offset });

// First call: omit afterMessageId. Later calls: send the cursor returned by the server.
export const syncChat = ({ afterMessageId, readSince } = {}) =>
  getJson('chat/sync', { after_message_id: afterMessageId, read_since: readSince });

// Profile (stored in grid_chat_db.user_profiles). Each resolves to
// { success, data: currentUser, message } — data is the saved user from the server.
export const updateProfile = async ({ name, role, phone, bio }) => {
  const formData = new FormData();
  formData.append('name', name ?? '');
  formData.append('role', role ?? '');
  formData.append('phone', phone ?? '');
  formData.append('bio', bio ?? '');
  return postForm('update_profile', formData);
};

export const uploadAvatar = async (file) => {
  const formData = new FormData();
  formData.append('avatar', file);
  return postForm('upload_avatar', formData);
};

// Remove the uploaded chat photo; the user then shows their CRM photo or initials
export const removeAvatar = async () => postForm('remove_avatar', new FormData());
