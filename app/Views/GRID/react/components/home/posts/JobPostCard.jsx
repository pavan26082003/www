import React, { useState } from 'react';
import ExpandablePostText from '../../ui/ExpandablePostText.jsx';
import { gridApi } from '../../../gridApi';

/**
 * JobPostCard Component
 * Renders an executive / founding opportunity post with:
 * - Direct hiring badge & bookmark toggle
 * - Opportunity Inset Box with role tags, location, immediate start badge, and Series A funding
 * - "Apply with Grid Identity" 1-click modal trigger
 * - Mutual connections discovery link
 */
export default function JobPostCard({ post, onApply, onNavigate }) {
  const currentUserId = (typeof window !== 'undefined' && window.__GRID_DATA__?.user?.id) || null;
  const isOwnPost = Boolean(currentUserId && post.author?.id && String(post.author.id) === String(currentUserId));
  const [bookmarked, setBookmarked] = useState(false);
  const [applied, setApplied] = useState(false);
  const [reposted, setReposted] = useState(Boolean(post.has_reposted));

  const handleApply = () => {
    setApplied(true);
    if (onApply) onApply(post);
  };

  const toggleRepost = async () => {
    if (isOwnPost) return;
    const nextReposted = !reposted;
    setReposted(nextReposted);
    try {
      const postId = post.db_id || post.id;
      const res = await gridApi.toggleRepost(postId);
      if (res && res.success && res.data) {
        setReposted(res.data.reposted);
      }
    } catch (err) {
      console.warn('Failed to toggle repost:', err);
    }
  };

  const handleAuthorClick = (e) => {
    e && e.stopPropagation && e.stopPropagation();
    if (onNavigate && post.author) {
      const authorId = post.author.id || post.author.user_id || post.user_id;
      if (authorId) {
        onNavigate('profile', {
          targetUser: {
            id: authorId,
            name: post.author.name,
            avatar: post.author.avatar,
            headline: post.author.role,
            gridId: post.author.gridId || post.author.grid_id
          }
        });
      }
    }
  };

  return (
    <article className="g-glass-card rounded-none sm:rounded-2xl border-x-0 sm:border-x border-y sm:border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 shadow-sm space-y-3.5">
      {/* Repost Attribution Banner */}
      {post.is_repost && post.reposted_by && (
        <div className="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 -mb-1">
          <span className="material-symbols-outlined text-[16px]">repeat</span>
          <span className="truncate">{post.reposted_by.name || 'A member'} reposted this</span>
        </div>
      )}

      {/* Header */}
      <div className="flex items-start justify-between gap-3">
        <div 
          className="flex items-center gap-3 min-w-0 flex-1 cursor-pointer group"
          onClick={handleAuthorClick}
        >
          <img
            alt={post.author.name}
            className="w-11 h-11 min-w-[44px] min-h-[44px] rounded-full object-cover shrink-0 aspect-square ring-2 ring-slate-100 dark:ring-slate-700 transition-all duration-150 group-hover:ring-blue-500"
            src={post.author.avatar}
          />
          <div className="min-w-0 flex-1">
            <div className="flex items-center gap-1.5 flex-nowrap min-w-0">
              <span className="font-display font-bold text-sm text-slate-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 group-hover:underline">{post.author.name}</span>
              {post.author.verified && (
                <span className="material-symbols-outlined text-[15px] text-blue-600 fill-current-solid shrink-0">
                  verified
                </span>
              )}
              <span className="text-xs text-slate-400 shrink-0">• 2nd</span>
            </div>
            <div 
              className="text-xs text-slate-500 dark:text-slate-400 leading-normal truncate max-w-full block"
              title={post.author.role}
            >
              {post.author.role}
            </div>
            <div className="flex items-center gap-1 text-[11px] text-slate-400 mt-0.5 shrink-0">
              <span>{post.author.timeAgo}</span>
              <span>•</span>
              <span className="text-blue-600 font-medium">Direct Opportunity</span>
            </div>
          </div>
        </div>

        <div className="flex items-center gap-1 shrink-0">
          {!isOwnPost && (
            <button
              onClick={toggleRepost}
              className={`p-1.5 rounded-full hover:bg-slate-100 transition-colors cursor-pointer ${
                reposted ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600'
              }`}
              type="button"
              aria-label={reposted ? 'Undo repost' : 'Repost'}
              title={reposted ? 'Reposted' : 'Repost'}
            >
              <span className="material-symbols-outlined text-[18px]">repeat</span>
            </button>
          )}
          <button
            onClick={() => setBookmarked(!bookmarked)}
            className="p-1.5 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-100 transition-colors cursor-pointer"
            type="button"
            aria-label="Bookmark job"
          >
            <span className="material-symbols-outlined text-[18px]">
              {bookmarked ? 'bookmark' : 'bookmark_border'}
            </span>
          </button>
        </div>
      </div>

      {/* Opportunity Inset Card */}
      {post.job && (
        <div className="rounded-xl border border-slate-200/90 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-900/60 p-4 space-y-3">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-xl bg-blue-600 text-white font-bold font-mono text-sm flex items-center justify-center shadow-xs shrink-0">
                {post.job.badge || 'EXP'}
              </div>
              <div>
                <div className="flex items-center gap-2">
                  <h3 className="font-display font-bold text-sm text-slate-900 dark:text-white">{post.job.title}</h3>
                  <span className="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-semibold tracking-wide">
                    {post.job.type || 'Full-time'}
                  </span>
                </div>
                <div className="text-xs text-slate-500 dark:text-slate-400">
                  {post.job.company} • {post.job.track}
                </div>
              </div>
            </div>
          </div>

          <ExpandablePostText
            text={post.job.description}
            maxChars={120}
            className="text-xs text-slate-600 dark:text-slate-300 leading-relaxed"
          />

          <div className="flex flex-wrap items-center gap-3 pt-2 text-[11px] text-slate-500 font-medium">
            <span className="flex items-center gap-1 text-slate-700">
              <span className="material-symbols-outlined text-[14px] text-emerald-600">public</span>
              {post.job.location || 'Remote (US / EU)'}
            </span>
            <span>•</span>
            <span className="flex items-center gap-1 text-slate-700">
              <span className="material-symbols-outlined text-[14px] text-blue-600">bolt</span>
              {post.job.compensation || 'Immediate Start'}
            </span>
            <span>•</span>
            <span className="flex items-center gap-1 text-slate-700">
              <span className="material-symbols-outlined text-[14px] text-slate-400">group</span>
              {post.job.funding || 'Series A ($14M Lead)'}
            </span>
          </div>
        </div>
      )}

      {/* Action Buttons */}
      <div className="flex flex-wrap items-center justify-between gap-2 pt-1">
        <div className="flex items-center gap-2">
          <button
            onClick={handleApply}
            className={`h-8 px-4 rounded-full font-semibold text-xs transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer ${
              applied
                ? 'bg-emerald-600 text-white hover:bg-emerald-700'
                : 'bg-blue-600 hover:bg-blue-700 text-white'
            }`}
            type="button"
          >
            <span className="material-symbols-outlined text-[15px]">
              {applied ? 'check_circle' : 'verified_user'}
            </span>
            <span>{applied ? 'Application Sent!' : 'Apply with Grid Identity'}</span>
          </button>
          <button
            className="h-8 px-3.5 rounded-full bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 font-medium text-xs transition-colors flex items-center gap-1 cursor-pointer"
            type="button"
            onClick={() => {
              window.location.href = '/index.php/grid/messaging';
            }}
          >
            <span className="material-symbols-outlined text-[15px]">mail</span>
            <span>Message {post.author.name?.split(' ')?.[0] || 'Member'}</span>
          </button>
        </div>
        <span
          className="text-[11px] font-medium text-slate-400 hover:text-slate-600 cursor-pointer"
          onClick={() => {
            window.location.href = '/index.php/grid/network';
          }}
        >
          View 4 Mutual Connections →
        </span>
      </div>
    </article>
  );
}
