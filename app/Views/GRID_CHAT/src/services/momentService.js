// Moments (Status/Stories) — MomentController in grid_chat_db.
// Shares chatService's transport: same session cookie, CSRF token pair and
// { success, data, message } contract, so nothing here throws on failure.

import { getApiBase, getJson, postForm, toFormData } from './chatService';

// Resolves to { success, data: { moments: [...] } }
export const fetchMoments = () => getJson('moments');

export const createTextMoment = ({ text, bg, caption }) =>
  postForm('moments/text', toFormData({ text, bg, caption: caption ?? '' }));

/**
 * Post a photo or video status. Uses XHR for upload progress; onProgress
 * receives 0-100. Resolves like the other calls.
 */
export const createMediaMoment = ({ file, caption }, onProgress) =>
  new Promise((resolve) => {
    const appData = typeof window !== 'undefined' ? window.__APP_DATA__ : null;
    const formData = toFormData({ caption: caption ?? '' });
    formData.append('file', file, file.name || 'status');
    if (appData?.csrfTokenName && appData?.csrfHash) {
      formData.append(appData.csrfTokenName, appData.csrfHash);
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', `${getApiBase()}/moments/media`);
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

export const markMomentViewed = (momentId) =>
  postForm('moments/view', toFormData({ moment_id: momentId }));

export const deleteMoment = (momentId) =>
  postForm('moments/delete', toFormData({ moment_id: momentId }));

// Adds or removes your heart. Resolves to { success, data: { liked, likeCount } }
export const toggleMomentLike = (momentId) =>
  postForm('moments/like', toFormData({ moment_id: momentId }));

// Who watched one of YOUR statuses (the server refuses anyone else's).
// Resolves to { success, data: { viewers: [...], likeCount } }
export const fetchMomentViewers = (momentId) =>
  getJson('moments/viewers', { moment_id: momentId });

// ---------------------------------------------------------------------------
// Status privacy. Both resolve to
// { success, data: { privacy: { share: {...}, download: {...} } } } where each
// setting is { audience: 'contacts'|'contacts_except'|'only', targets: [user] }.
// ---------------------------------------------------------------------------

export const fetchMomentPrivacy = () => getJson('moments/privacy');

// setting: 'share' | 'download'
export const saveMomentPrivacy = (setting, audience, userIds = []) =>
  postForm('moments/privacy', toFormData({
    setting,
    audience,
    user_ids: userIds.join(','),
  }));
