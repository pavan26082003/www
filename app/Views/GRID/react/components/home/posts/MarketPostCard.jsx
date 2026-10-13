import React, { useState } from 'react';
import ExpandablePostText from '../../ui/ExpandablePostText.jsx';
import VerifiedBadge from '../../ui/VerifiedBadge.jsx';
import { gridApi } from '../../../gridApi';

/**
 * MarketPostCard Component
 * Renders a verified pro service / marketplace advisory listing with:
 * - GRID PRO verified tag and 4.9★ rating
 * - Service Scope breakdown (price, turnaround, stack)
 * - "Case Studies" & "Request Scope" contract actions
 */
export default function MarketPostCard({ post, onRequestScope, onNavigate }) {
  const currentUserId = (typeof window !== 'undefined' && window.__GRID_DATA__?.user?.id) || null;
  const isOwnPost = Boolean(currentUserId && post.author?.id && String(post.author.id) === String(currentUserId));
  const [requested, setRequested] = useState(false);
  const [reposted, setReposted] = useState(Boolean(post.has_reposted));

  const handleRequest = () => {
    setRequested(true);
    if (onRequestScope) onRequestScope(post);
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
              <VerifiedBadge size="xs" title="Verified Pro" />
              <span className="text-xs text-amber-500 font-semibold font-mono shrink-0">4.9 ★ (127)</span>
            </div>
            <div 
              className="text-xs text-slate-500 dark:text-slate-400 leading-normal truncate max-w-full block"
              title={post.author.role}
            >
              {post.author.role}
            </div>
            <div className="text-[11px] text-slate-400 mt-0.5 shrink-0">
              {post.author.timeAgo} • Offering Studio Advisory
            </div>
          </div>
        </div>

        <div className="flex items-center gap-2 shrink-0">
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
          <span className="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-[10px] font-mono uppercase font-semibold text-slate-500 dark:text-slate-400">
            MARKETPLACE
          </span>
        </div>
      </div>

      <ExpandablePostText
        text={post.content ||
          'Opening 2 advisory slots for late Q2. If your engineering and product teams are struggling with design token drift between Figma and production React/Tailwind codebases, this package delivers deterministic parity.'}
      />

      {/* Service Scope Box */}
      {post.service && (
        <div className="rounded-xl border border-slate-200/90 bg-slate-50/50 p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
          <div>
            <div className="font-display font-bold text-sm text-slate-900">
              {post.service.title}
            </div>
            <div className="flex flex-wrap items-center gap-2 mt-1 text-xs text-slate-500">
              <span className="text-emerald-600 font-semibold font-mono">
                {post.service.price || 'Starting at $2,400 USD'}
              </span>
              <span>•</span>
              <span>{post.service.turnaround || '5 Business Days'}</span>
              <span>•</span>
              <span className="text-slate-600">{post.service.deliverables || 'Style Dictionary & CI setup'}</span>
            </div>
          </div>

          <div className="flex flex-wrap items-center gap-2 justify-end sm:w-auto shrink-0">
            <button
              className="h-8 px-3 rounded-full border border-slate-200 hover:bg-white text-slate-700 font-medium text-xs transition-colors shrink-0 cursor-pointer"
              type="button"
              onClick={handleRequest}
            >
              Case Studies
            </button>
            <button
              onClick={handleRequest}
              className={`h-8 px-3 rounded-full font-semibold text-xs transition-colors shrink-0 shadow-xs cursor-pointer ${
                requested
                  ? 'bg-emerald-600 text-white'
                  : 'bg-blue-600 hover:bg-blue-700 text-white'
              }`}
              type="button"
            >
              {requested ? 'Scope Requested' : 'Request Scope'}
            </button>
          </div>
        </div>
      )}
    </article>
  );
}
