import React, { useState, useRef, useEffect } from 'react';
import { gridApi } from '../../gridApi';

/**
 * PostComposer Component
 * Renders the clean prompt box "What are you working on, Pavan?"
 * with attachment controls (Image, Checklist, Emoji, Add tags) and a Post action button.
 */
export default function PostComposer({ 
  onPostCreated, 
  currentUser = { name: 'Pavan', avatar: '/assets/images/avatar.jpg' },
  externalTag = null
}) {
  const [content, setContent] = useState('');
  const [tags, setTags] = useState([]);
  const [showTagInput, setShowTagInput] = useState(false);
  const [tagInput, setTagInput] = useState('');
  const [isPosting, setIsPosting] = useState(false);
  const [isUploading, setIsUploading] = useState(false);
  const [uploadError, setUploadError] = useState('');
  const [selectedFiles, setSelectedFiles] = useState([]);
  const [previewUrls, setPreviewUrls] = useState([]);
  const fileInputRef = useRef(null);
  const textareaRef = useRef(null);

  // Auto-attach external tag when a user clicks 'Post in #Topic' from TopicHubBanner
  useEffect(() => {
    if (externalTag && !tags.includes(externalTag)) {
      setTags(prev => prev.includes(externalTag) ? prev : [...prev, externalTag]);
      if (textareaRef.current) {
        textareaRef.current.focus();
      }
    }
  }, [externalTag]);

  // Auto-expand textarea height dynamically based on screen size and text length
  useEffect(() => {
    if (textareaRef.current) {
      textareaRef.current.style.height = 'auto';
      textareaRef.current.style.height = `${Math.min(textareaRef.current.scrollHeight, 280)}px`;
    }
  }, [content]);

  const handleFileChange = (e) => {
    const files = Array.from(e.target.files || []);
    if (files.length === 0) return;

    setUploadError('');
    const validFiles = [];
    const newPreviews = [];

    for (const file of files) {
      const isVid = (file.type && file.type.startsWith('video/')) || /\.(mp4|webm|mov|mkv|avi)$/i.test(file.name);
      const maxSize = isVid ? 100 * 1024 * 1024 : 15 * 1024 * 1024;
      if (file.size > maxSize) {
        setUploadError(`File ${file.name} exceeds maximum ${isVid ? '100MB' : '15MB'} limit.`);
        continue;
      }
      validFiles.push(file);
      newPreviews.push({
        url: URL.createObjectURL(file),
        isVideo: isVid,
        name: file.name
      });
    }

    setSelectedFiles(prev => [...prev, ...validFiles]);
    setPreviewUrls(prev => [...prev, ...newPreviews]);
  };

  const clearAllSelectedFiles = () => {
    previewUrls.forEach(item => {
      const url = typeof item === 'string' ? item : item?.url;
      if (url && url.startsWith('blob:')) {
        URL.revokeObjectURL(url);
      }
    });
    setSelectedFiles([]);
    setPreviewUrls([]);
    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  const removeSelectedFile = (indexToRemove) => {
    if (indexToRemove === undefined) {
      clearAllSelectedFiles();
      return;
    }
    setPreviewUrls(prev => {
      const item = prev[indexToRemove];
      const url = typeof item === 'string' ? item : item?.url;
      if (url && url.startsWith('blob:')) {
        URL.revokeObjectURL(url);
      }
      return prev.filter((_, idx) => idx !== indexToRemove);
    });
    setSelectedFiles(prev => prev.filter((_, idx) => idx !== indexToRemove));
    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  const handlePost = async () => {
    if ((!content.trim() && selectedFiles.length === 0) || isPosting) return;
    setIsPosting(true);
    setUploadError('');
    const postTags = tags.length > 0 ? tags : ['#CreatorWork', '#GRID'];

    try {
      let mediaUrls = [];
      if (selectedFiles.length > 0) {
        setIsUploading(true);
        const uploadPromises = selectedFiles.map(f => gridApi.uploadFile(f, 'post'));
        const uploadResults = await Promise.all(uploadPromises);
        let uploadFailed = false;
        for (const res of uploadResults) {
          if (res && res.success && res.file_url) {
            mediaUrls.push(res.file_url);
          } else {
            uploadFailed = true;
            console.error('File upload failed:', res);
            setUploadError(res?.message || 'Failed to upload video/image. Please try again.');
          }
        }
        setIsUploading(false);

        // If files were selected but all uploads failed, abort posting
        if (uploadFailed && mediaUrls.length === 0) {
          setIsPosting(false);
          return;
        }
      }

      const res = await gridApi.createPost({
        content: content.trim() || 'Shared an update',
        tags: postTags,
        media_urls: mediaUrls,
        media_url: mediaUrls[0] || null,
        type: 'general'
      });

      if (res && res.success && res.data) {
        const savedPost = res.data;
        if (savedPost.author) {
          savedPost.author.gridId = savedPost.author.grid_id || savedPost.author.gridId || currentUser.gridId || '';
          if (!savedPost.author.avatar || savedPost.author.avatar.startsWith('a:')) {
            savedPost.author.avatar = currentUser.avatar;
          }
        }
        if (onPostCreated) {
          onPostCreated(savedPost);
        }
      } else {
        if (onPostCreated) {
          onPostCreated({
            id: 'post-' + Date.now(),
            author: {
              name: currentUser.name || 'Pavan',
              role: currentUser.role || 'Full-Stack Creator • GRID Pro',
              timeAgo: 'Just now',
              avatar: currentUser.avatar,
              discipline: 'General',
              gridId: currentUser.gridId || ''
            },
            title: '',
            content: content.trim() || 'Shared an update',
            tags: postTags,
            media_url: mediaUrls[0] || previewUrls[0]?.url || null,
            media: (mediaUrls.length > 0 ? mediaUrls : previewUrls).map((item, i) => {
              const isVid = previewUrls[i]?.isVideo || /\.(mp4|webm|mov|mkv)$/i.test(typeof item === 'string' ? item : item.url);
              return {
                id: i,
                url: typeof item === 'string' ? item : item.url,
                type: isVid ? 'video' : 'image',
                order: i
              };
            }),
            metrics: { likes: 0, comments: 0, reposts: 0 }
          });
        }
      }
    } catch (err) {
      console.warn('Backend post creation failed, using client dispatch:', err);
      if (onPostCreated) {
        onPostCreated({
          id: 'post-' + Date.now(),
          author: {
            name: currentUser.name || 'Pavan',
            role: currentUser.role || 'Full-Stack Creator • GRID Pro',
            timeAgo: 'Just now',
            avatar: currentUser.avatar,
            discipline: 'General',
            gridId: currentUser.gridId || ''
          },
          title: '',
          content: content.trim() || 'Shared an update',
          tags: postTags,
          media_url: previewUrls[0]?.url || null,
          media: previewUrls.map((p, i) => ({
            id: i,
            url: p?.url || p,
            type: p?.isVideo ? 'video' : 'image',
            order: i
          })),
          metrics: { likes: 0, comments: 0, reposts: 0 }
        });
      }
    } finally {
      setIsPosting(false);
      setIsUploading(false);
      setContent('');
      setTags([]);
      setShowTagInput(false);
      clearAllSelectedFiles();
    }
  };

  const handleAddTag = (e) => {
    if (e.key === 'Enter' && tagInput.trim()) {
      e.preventDefault();
      const cleanTag = tagInput.trim().startsWith('#') ? tagInput.trim() : '#' + tagInput.trim();
      if (!tags.includes(cleanTag)) {
        setTags([...tags, cleanTag]);
      }
      setTagInput('');
    }
  };

  const removeTag = (tagToRemove) => {
    setTags(tags.filter(t => t !== tagToRemove));
  };

  return (
    <div className="g-glass-card rounded-none sm:rounded-2xl border-x-0 sm:border-x border-y sm:border border-slate-200/80 dark:border-slate-800 p-4 shadow-sm space-y-3">
      <div className="flex items-start gap-3">
        <img
          alt={currentUser.name}
          className="w-10 h-10 rounded-full object-cover ring-2 ring-slate-100 shrink-0 mt-1"
          src={currentUser.avatar || '/assets/images/avatar.jpg'}
          onError={(e) => {
            e.currentTarget.onerror = null;
            e.currentTarget.src = '/assets/images/avatar.jpg';
          }}
        />
        <textarea
          ref={textareaRef}
          rows={1}
          className="w-full bg-transparent border-none text-slate-700 dark:text-slate-200 placeholder:text-slate-400 text-sm focus:outline-none focus:ring-0 px-1 py-1.5 font-normal resize-none overflow-y-auto leading-relaxed break-words min-h-[38px] max-h-[280px]"
          placeholder={`What are you working on, ${currentUser.firstName || currentUser.name || 'there'}?`}
          value={content}
          onChange={(e) => setContent(e.target.value)}
          onKeyDown={(e) => {
            if (e.key === 'Enter' && (e.metaKey || e.ctrlKey)) {
              e.preventDefault();
              handlePost();
            }
          }}
        />
      </div>

      {/* Multi-Image Preview Thumbnails with remove buttons */}
      {previewUrls.length > 0 && (
        <div className={`my-2 grid gap-2 ${
          previewUrls.length === 1 ? 'grid-cols-1' :
          previewUrls.length === 2 ? 'grid-cols-2' :
          'grid-cols-2 sm:grid-cols-3'
        }`}>
          {previewUrls.map((item, idx) => {
            const url = typeof item === 'string' ? item : item.url;
            const isVid = typeof item === 'object' ? item.isVideo : /\.(mp4|webm|mov|mkv)$/i.test(url);
            return (
              <div key={idx} className="relative rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-900 aspect-video flex items-center justify-center group">
                {isVid ? (
                  <video
                    src={url}
                    className="w-full h-full object-cover"
                    muted
                    autoPlay
                    loop
                    playsInline
                  />
                ) : (
                  <img
                    src={url}
                    alt={`Upload preview ${idx + 1}`}
                    className="w-full h-full object-cover"
                  />
                )}
                {isVid && (
                  <span className="absolute bottom-1.5 left-1.5 px-2 py-0.5 rounded-md bg-black/70 backdrop-blur-xs text-[10px] font-mono font-semibold text-white flex items-center gap-1 pointer-events-none">
                    <span className="material-symbols-outlined text-[12px] text-red-500">videocam</span> VIDEO
                  </span>
                )}
                <button
                  type="button"
                  onClick={() => removeSelectedFile(idx)}
                  className="absolute top-1.5 right-1.5 p-1 rounded-full bg-black/70 hover:bg-black/90 text-white transition-colors cursor-pointer shadow-sm z-10"
                  title="Remove"
                >
                  <span className="material-symbols-outlined text-[14px] block leading-none">close</span>
                </button>
              </div>
            );
          })}
          {previewUrls.length < 10 && (
            <button
              type="button"
              onClick={() => fileInputRef.current && fileInputRef.current.click()}
              className="rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700 hover:border-blue-400 dark:hover:border-blue-500 bg-slate-50/50 dark:bg-slate-900/50 aspect-video flex flex-col items-center justify-center text-slate-400 hover:text-blue-600 transition-colors cursor-pointer"
            >
              <span className="material-symbols-outlined text-[24px]">add_photo_alternate</span>
              <span className="text-[11px] font-medium mt-1">Add media</span>
            </button>
          )}
        </div>
      )}

      {tags.length > 0 && (
        <div className="flex flex-wrap gap-1.5 px-1">
          {tags.map((tag) => (
            <span
              key={tag}
              className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[11px] font-medium"
            >
              {tag}
              <button
                type="button"
                onClick={() => removeTag(tag)}
                className="text-blue-500 hover:text-blue-800"
              >
                ×
              </button>
            </span>
          ))}
        </div>
      )}

      {showTagInput && (
        <div className="flex items-center gap-2 px-1">
          <input
            type="text"
            placeholder="Type tag & press Enter (e.g. #ThreeJS)"
            className="text-xs px-2.5 py-1 rounded-md border border-slate-200 focus:outline-none focus:border-blue-500 w-52"
            value={tagInput}
            onChange={(e) => setTagInput(e.target.value)}
            onKeyDown={handleAddTag}
            autoFocus
          />
          <button
            type="button"
            onClick={() => setShowTagInput(false)}
            className="text-xs text-slate-400 hover:text-slate-600"
          >
            Cancel
          </button>
        </div>
      )}

      {uploadError && (
        <div className="text-xs text-red-600 bg-red-50 border border-red-200 px-3 py-1.5 rounded-lg flex items-center justify-between">
          <span>{uploadError}</span>
          <button type="button" onClick={() => setUploadError('')} className="text-red-400 hover:text-red-700 font-bold ml-2">×</button>
        </div>
      )}

      <div className="flex items-center justify-between pt-2 border-t border-slate-100">
        <div className="flex items-center gap-1 sm:gap-2 text-slate-500">
          {/* Universal Media input: Opens native gallery with BOTH photos and videos on Phone & PC */}
          <input
            ref={fileInputRef}
            type="file"
            multiple
            accept="image/*,video/*,.jpg,.jpeg,.png,.webp,.gif,.mp4,.webm,.mov,.mkv,.avi"
            className="hidden"
            onChange={handleFileChange}
          />
          <button
            className={`p-1.5 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer ${
              selectedFiles.length > 0 ? 'text-blue-600 bg-blue-50' : 'text-slate-500 hover:text-slate-800'
            }`}
            title="Add photos or video (up to 100MB)"
            type="button"
            onClick={() => fileInputRef.current && fileInputRef.current.click()}
          >
            <span className="material-symbols-outlined text-[20px]">image</span>
          </button>
          <button
            className="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-slate-800 transition-colors cursor-pointer"
            title="Add task / bullet point"
            type="button"
            onClick={() => setContent(prev => prev ? `${prev}\n• ` : '• ')}
          >
            <span className="material-symbols-outlined text-[20px]">check_box</span>
          </button>
          <button
            className="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-slate-800 transition-colors cursor-pointer"
            title="Add emoji"
            type="button"
            onClick={() => setContent(prev => prev + ' 🚀')}
          >
            <span className="material-symbols-outlined text-[20px]">sentiment_satisfied</span>
          </button>
          <div className="h-4 w-px bg-slate-200 mx-1" />
          <button
            className="flex items-center gap-1 px-2 py-1 text-xs text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-md font-medium transition-colors"
            type="button"
            onClick={() => setShowTagInput(true)}
          >
            <span className="material-symbols-outlined text-[16px] text-slate-400">tag</span>
            <span>Add tags</span>
          </button>
        </div>

        <button
          className={`px-5 py-1.5 rounded-full text-xs font-semibold shadow-xs transition-all ${
            (content.trim() || selectedFiles.length > 0) && !isPosting
              ? 'bg-slate-900 hover:bg-slate-800 text-white cursor-pointer'
              : 'bg-slate-200 text-slate-400 cursor-not-allowed'
          }`}
          type="button"
          disabled={(!content.trim() && selectedFiles.length === 0) || isPosting}
          onClick={handlePost}
        >
          {isUploading ? 'Uploading...' : isPosting ? 'Posting...' : 'Post'}
        </button>
      </div>
    </div>
  );
}
