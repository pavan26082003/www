import React, { useState, useEffect } from 'react';
import { createPortal } from 'react-dom';
import AnimatedThumbsUp from '../../ui/AnimatedThumbsUp.jsx';
import CommentsDrawer from './CommentsDrawer.jsx';
import ExpandablePostText from '../../ui/ExpandablePostText.jsx';
import VerifiedBadge from '../../ui/VerifiedBadge.jsx';
import PostMediaCarousel from './PostMediaCarousel.jsx';
import { gridApi } from '../../../gridApi';

/**
 * TechPostCard Component
 * Renders technical and engineering experience posts with:
 * - Author header with verified badge, degree, and Follow button
 * - Expandable narrative description with LinkedIn-style "... more" toggle
 * - Featured Technical Artifact card or Rejection Notice media card
 * - Social engagement counters and interactive Like/Comment/Repost/Send bar.
 */
export default function TechPostCard({ post, onDeletePost, onNavigate }) {
  const currentUserId = (typeof window !== 'undefined' && window.__GRID_DATA__?.user?.id) || null;
  const isOwnPost = Boolean(currentUserId && post.author?.id && String(post.author.id) === String(currentUserId));
  const [liked, setLiked] = useState(Boolean(post.has_liked));
  const [likesCount, setLikesCount] = useState(post.metrics?.likes || 0);
  const [reposted, setReposted] = useState(Boolean(post.has_reposted));
  const [repostsCount, setRepostsCount] = useState(post.metrics?.reposts || 0);
  const [commentsCount, setCommentsCount] = useState(post.metrics?.comments || 0);
  const [isFollowing, setIsFollowing] = useState(false);
  const [isCommentsOpen, setIsCommentsOpen] = useState(false);
  const [showMenu, setShowMenu] = useState(false);
  const [showConfirmModal, setShowConfirmModal] = useState(false);
  const [isDeleting, setIsDeleting] = useState(false);

  useEffect(() => {
    if (showConfirmModal) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  }, [showConfirmModal]);

  const toggleLike = async () => {
    const nextLiked = !liked;
    setLiked(nextLiked);
    setLikesCount(prev => nextLiked ? prev + 1 : Math.max(0, prev - 1));
    try {
      const postId = post.db_id || post.id;
      const res = await gridApi.toggleLike(postId);
      if (res && res.success && res.data) {
        setLiked(res.data.liked);
        setLikesCount(res.data.likes_count);
      }
    } catch (err) {
      console.warn('Failed to toggle like:', err);
    }
  };

  const toggleRepost = async () => {
    if (isOwnPost) return;
    const nextReposted = !reposted;
    setReposted(nextReposted);
    setRepostsCount(prev => nextReposted ? prev + 1 : Math.max(0, prev - 1));
    try {
      const postId = post.db_id || post.id;
      const res = await gridApi.toggleRepost(postId);
      if (res && res.success && res.data) {
        setReposted(res.data.reposted);
        setRepostsCount(res.data.reposts_count);
      }
    } catch (err) {
      console.warn('Failed to toggle repost:', err);
    }
  };

  const handleDeleteClick = () => {
    setShowMenu(false);
    setShowConfirmModal(true);
  };

  const handleConfirmDelete = async () => {
    setIsDeleting(true);
    try {
      const postId = post.db_id || post.id;
      const res = await gridApi.deletePost(postId);
      if (res && res.success) {
        if (onDeletePost) {
          onDeletePost(post.id);
        }
      }
    } catch (err) {
      console.warn('Error deleting post:', err);
      if (onDeletePost) {
        onDeletePost(post.id);
      }
    } finally {
      setIsDeleting(false);
      setShowConfirmModal(false);
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

      {/* Author Header */}
      <div className="flex items-start justify-between gap-3">
        <div 
          className="flex items-center gap-3 min-w-0 flex-1 cursor-pointer group"
          onClick={handleAuthorClick}
        >
          <img
            alt={post.author.name}
            className="w-11 h-11 min-w-[44px] min-h-[44px] rounded-full object-cover shrink-0 aspect-square ring-2 ring-slate-100 dark:ring-slate-700 transition-all duration-150 group-hover:ring-blue-500"
            src={post.author.avatar || '/assets/images/avatar.jpg'}
            onError={(e) => {
              e.currentTarget.onerror = null;
              e.currentTarget.src = '/assets/images/avatar.jpg';
            }}
          />
          <div className="min-w-0 flex-1">
            <div className="flex items-center gap-1.5 flex-nowrap min-w-0">
              <span className="font-display font-bold text-sm text-slate-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 group-hover:underline">
                {post.author.name}
              </span>
              {post.author.verified && (
                <VerifiedBadge size="xs" title="Verified Creator" />
              )}
              <span className="text-xs text-slate-400 shrink-0">• {post.author.degree || '3rd+'}</span>
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
              <span className="material-symbols-outlined text-[13px]">public</span>
            </div>
          </div>
        </div>

        <div className="flex items-center gap-1 shrink-0">
          <button
            onClick={() => setIsFollowing(!isFollowing)}
            className={`text-xs font-semibold px-3 py-1 rounded-full border transition-colors flex items-center gap-1 cursor-pointer ${
              isFollowing
                ? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-300 dark:border-slate-700'
                : 'text-blue-600 hover:bg-blue-50 border-blue-200'
            }`}
            type="button"
          >
            <span className="material-symbols-outlined text-[15px]">
              {isFollowing ? 'check' : 'add'}
            </span>
            {isFollowing ? 'Following' : 'Follow'}
          </button>

          {/* Three-dot options menu with Delete post */}
          <div className="relative">
            <button
              className={`p-1.5 rounded-full hover:bg-slate-100 cursor-pointer transition-colors ${
                showMenu ? 'text-slate-800 bg-slate-100' : 'text-slate-400 hover:text-slate-600'
              }`}
              type="button"
              onClick={() => setShowMenu(!showMenu)}
              title="More options"
            >
              <span className="material-symbols-outlined text-[18px]">more_horiz</span>
            </button>

            {showMenu && (
              <>
                <div
                  className="fixed inset-0 z-20"
                  onClick={() => setShowMenu(false)}
                />
                <div className="absolute right-0 top-full mt-1 w-36 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-lg py-1 z-30 animate-in fade-in zoom-in-95 duration-100">
                  <button
                    type="button"
                    onClick={handleDeleteClick}
                    className="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 font-medium transition-colors cursor-pointer text-left"
                  >
                    <span className="material-symbols-outlined text-[16px]">delete</span>
                    <span>Delete post</span>
                  </button>
                </div>
              </>
            )}
          </div>
        </div>
      </div>

      {/* Post Content with LinkedIn-style ...more clamp */}
      <ExpandablePostText
        text={post.content}
        truncateAfter={post.truncateAfter}
      />

      {/* Attached Media Rejection Notice Card (matching user screenshot) */}
      {post.mediaCard && (
        <div className="rounded-xl border-2 border-[#5b8a53] bg-[#0c140e] p-6 font-sans text-white shadow-md space-y-4 my-3">
          <div className="text-sm font-semibold tracking-wide text-slate-100">
            Dear <strong className="font-bold text-white text-base">{post.mediaCard.recipient || 'Nikhil Pratap Singh,'}</strong>
          </div>
          <p className="text-xs sm:text-[13px] text-slate-200 leading-relaxed font-normal">
            {post.mediaCard.message}
          </p>
          <p className="text-xs sm:text-[13px] text-slate-200 leading-relaxed font-normal">
            {post.mediaCard.closing}
          </p>
          {post.mediaCard.quotedText && (
            <div className="pt-2">
              <span 
                className="text-[#68d391] text-xs font-medium"
              >
                {post.mediaCard.quotedText}
              </span>
            </div>
          )}
        </div>
      )}

      {/* Attached Media Carousel (Dynamic Height & Left/Right Horizontal Slider) */}
      {!post.artifact && (
        <PostMediaCarousel
          media={post.media}
          singleUrl={post.media_url || post.image}
          authorName={post.author?.name || 'Creator'}
        />
      )}

      {/* Featured Technical Artifact Card */}
      {post.artifact && (
        <div className="rounded-xl border border-slate-200/90 overflow-hidden bg-slate-900 text-white shadow-xs">
          <div className="grid grid-cols-1 md:grid-cols-12">
            <div className="md:col-span-5 relative h-44 md:h-auto min-h-[160px] bg-slate-950 overflow-hidden">
              <img
                alt={post.artifact.title}
                className="w-full h-full object-cover opacity-85 hover:scale-105 transition-transform duration-500"
                src={post.artifact.image}
              />
              <span className="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-md bg-blue-600/90 backdrop-blur-xs font-mono text-[10px] font-semibold tracking-wide text-white uppercase">
                {post.artifact.badge || 'OPEN SOURCE REPO'}
              </span>
              <span className="absolute bottom-2.5 left-2.5 px-2 py-0.5 rounded bg-black/70 font-mono text-[10px] text-emerald-400 flex items-center gap-1">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" /> 60 FPS
              </span>
            </div>

            <div className="md:col-span-7 p-4 flex flex-col justify-between bg-slate-900">
              <div>
                <div className="flex items-center gap-2">
                  <h3 className="font-display font-bold text-sm text-white">{post.artifact.title}</h3>
                  <span className="px-1.5 py-0.5 rounded bg-slate-800 text-[10px] font-mono text-slate-300">
                    {post.artifact.version || 'v3.0.2'}
                  </span>
                </div>
                <p className="text-xs text-slate-300 mt-1 line-clamp-2 leading-relaxed">
                  {post.artifact.description}
                </p>

                {/* Tag Chips */}
                <div className="flex flex-wrap gap-1.5 mt-2.5">
                  {post.artifact.tags?.map((t, idx) => (
                    <span
                      key={idx}
                      className="px-2 py-0.5 rounded-md bg-slate-800 border border-slate-700/60 text-[11px] font-mono text-slate-300"
                    >
                      {t}
                    </span>
                  ))}
                </div>
              </div>

              <div className="flex items-center justify-between pt-3 mt-3 border-t border-slate-800/80">
                <a
                  className="text-[11px] font-mono text-blue-400 hover:underline flex items-center gap-1 truncate max-w-[200px]"
                  href="#"
                  onClick={(e) => e.preventDefault()}
                >
                  {post.artifact.repoUrl || 'github.com/linearlabs/canvas'}
                </a>
                <div className="flex items-center gap-1.5 text-xs text-amber-300">
                  <span className="material-symbols-outlined text-[15px]">star</span>
                  <span className="font-mono text-[11px] font-semibold">{post.artifact.stars || 142}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Social Engagement Stats */}
      <div className="flex items-center justify-between text-xs text-slate-400 pt-1 px-1">
        <div className="flex items-center gap-1">
          <span className="w-4 h-4 rounded-full bg-blue-600 flex items-center justify-center text-white text-[9px]">
            👍
          </span>
          <span className="w-4 h-4 rounded-full bg-rose-500 flex items-center justify-center text-white text-[9px]">
            ❤️
          </span>
          <span className="ml-1 text-slate-500 font-medium">{likesCount}</span>
        </div>
        <div className="flex items-center gap-3 text-slate-500">
          <button
            type="button"
            onClick={() => setIsCommentsOpen(true)}
            className="hover:underline hover:text-blue-600 transition-colors cursor-pointer"
          >
            {commentsCount} comments
          </button>
          <span>•</span>
          <span>{repostsCount} reposts</span>
        </div>
      </div>

      {/* Social Action Buttons Bar */}
      <div className="flex items-center justify-between border-t border-slate-100 pt-2 text-slate-600">
        <button
          onClick={toggleLike}
          className={`flex items-center justify-center gap-1.5 flex-1 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 cursor-pointer ${
            liked ? 'text-blue-600 font-semibold bg-blue-50/40' : 'text-slate-600 hover:bg-slate-50'
          }`}
          type="button"
          aria-label={liked ? 'Unlike post' : 'Like post'}
        >
          <AnimatedThumbsUp isLiked={liked} size={18} />
          <span className={`transition-colors duration-200 ${liked ? 'text-blue-600 font-semibold' : 'text-slate-600'}`}>Like</span>
        </button>
        <button
          className="flex items-center justify-center gap-1.5 flex-1 py-1.5 hover:bg-slate-50 rounded-lg text-xs font-medium transition-colors cursor-pointer"
          type="button"
          onClick={() => setIsCommentsOpen(true)}
          aria-label="Open comments"
        >
          <span className="material-symbols-outlined text-[18px]">chat_bubble_outline</span>
          <span>Comment</span>
        </button>
        <button
          onClick={toggleRepost}
          disabled={isOwnPost}
          className={`flex items-center justify-center gap-1.5 flex-1 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 ${
            isOwnPost
              ? 'text-slate-300 dark:text-slate-700 cursor-not-allowed'
              : reposted
                ? 'text-emerald-600 font-semibold bg-emerald-50/40 cursor-pointer'
                : 'text-slate-600 hover:bg-slate-50 cursor-pointer'
          }`}
          type="button"
          title={isOwnPost ? "You can't repost your own post" : (reposted ? 'Undo repost' : 'Repost')}
          aria-label={isOwnPost ? 'Repost unavailable on your own post' : (reposted ? 'Undo repost' : 'Repost')}
        >
          <span className="material-symbols-outlined text-[18px]">repeat</span>
          <span>{reposted ? 'Reposted' : 'Repost'}</span>
        </button>
        <button
          className="flex items-center justify-center gap-1.5 flex-1 py-1.5 hover:bg-slate-50 rounded-lg text-xs font-medium transition-colors cursor-pointer"
          type="button"
          onClick={() => {
            if (navigator.clipboard && window.location) {
              navigator.clipboard.writeText(window.location.href);
            }
          }}
        >
          <span className="material-symbols-outlined text-[18px]">send</span>
          <span>Send</span>
        </button>
      </div>

      {/* Comments Bottom Sheet Drawer */}
      <CommentsDrawer
        isOpen={isCommentsOpen}
        onClose={() => setIsCommentsOpen(false)}
        post={post}
        onAddCommentCount={() => setCommentsCount(prev => prev + 1)}
        onNavigate={onNavigate}
      />

      {/* Custom In-App Delete Confirmation Card Portaled to document.body */}
      {showConfirmModal && typeof document !== 'undefined' && createPortal(
        <div 
          className="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs animate-in fade-in duration-200"
          onClick={(e) => {
            if (e.target === e.currentTarget && !isDeleting) {
              setShowConfirmModal(false);
            }
          }}
        >
          <div 
            className="w-full max-w-xs sm:max-w-sm bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 text-center space-y-4 animate-in zoom-in-95 duration-200"
            role="dialog"
            aria-modal="true"
          >
            <div className="w-14 h-14 rounded-full bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 flex items-center justify-center mx-auto ring-8 ring-red-50/50 dark:ring-red-950/30">
              <span className="material-symbols-outlined text-[28px]">delete</span>
            </div>
            
            <div className="space-y-1.5">
              <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                Delete post?
              </h3>
              <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed px-1">
                Are you sure you want to delete this post? This action cannot be undone and will permanently remove it from your feed.
              </p>
            </div>

            <div className="flex items-center gap-3 pt-2">
              <button
                type="button"
                onClick={() => setShowConfirmModal(false)}
                disabled={isDeleting}
                className="flex-1 px-4 py-2.5 rounded-full border border-slate-200 dark:border-slate-700 text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer disabled:opacity-50"
              >
                Cancel
              </button>
              <button
                type="button"
                onClick={handleConfirmDelete}
                disabled={isDeleting}
                className="flex-1 px-4 py-2.5 rounded-full bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-semibold shadow-xs transition-colors cursor-pointer disabled:opacity-50 flex items-center justify-center gap-1.5"
              >
                {isDeleting ? (
                  <>
                    <span className="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                    <span>Deleting...</span>
                  </>
                ) : (
                  <span>Yes, Delete</span>
                )}
              </button>
            </div>
          </div>
        </div>,
        document.body
      )}
    </article>
  );
}
