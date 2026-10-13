import React, { useState, useRef, useEffect } from 'react';
import { createPortal } from 'react-dom';
import AnimatedThumbsUp from '../../ui/AnimatedThumbsUp.jsx';
import { gridApi } from '../../../gridApi';

/**
 * CommentsDrawer Component
 * 
 * Recreates the LinkedIn / Grid bottom-sheet comments drawer:
 * - Drag handle and "Most relevant ▾" filter
 * - Threaded comments with author replies and connecting guide line
 * - Interactive like reactions with AnimatedThumbsUp
 * - Sticky bottom input bar with user avatar, mention/image/GIF buttons, and "Comment" submit
 */
export default function CommentsDrawer({
  isOpen = false,
  onClose,
  post,
  currentUser = {
    name: 'Praful Shah',
    avatar: '/assets/images/avatar.jpg',
    headline: 'AI Systems Engineer & Full-Stack Architect'
  },
  onAddCommentCount,
  onNavigate
}) {
  const [comments, setComments] = useState(() => getInitialComments(post));
  const [newCommentText, setNewCommentText] = useState('');
  const [replyingTo, setReplyingTo] = useState(null); // { commentId, authorName }
  const [sortOption, setSortOption] = useState('Most relevant');
  const [showSortMenu, setShowSortMenu] = useState(false);
  const inputRef = useRef(null);
  const listEndRef = useRef(null);

  const handleUserClick = (author) => {
    if (onNavigate && author) {
      const authorId = author.id || author.user_id;
      if (authorId) {
        if (onClose) onClose();
        onNavigate('profile', {
          targetUser: {
            id: authorId,
            name: author.name,
            avatar: author.avatar,
            headline: author.headline || author.role
          }
        });
      }
    }
  };

  // Sync / reset comments when post changes or drawer opens
  useEffect(() => {
    if (post) {
      const postId = post.db_id || (typeof post.id === 'string' ? post.id.replace('db-', '') : post.id);
      const numId = parseInt(postId, 10);
      if (numId > 0) {
        gridApi.getComments(numId).then(serverComments => {
          if (Array.isArray(serverComments) && serverComments.length > 0) {
            setComments(serverComments);
          } else {
            setComments(getInitialComments(post));
          }
        }).catch(() => {
          setComments(getInitialComments(post));
        });
      } else {
        setComments(getInitialComments(post));
      }
    }
  }, [post?.id, isOpen]);

  // Lock body scroll and focus input when opened
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
      setTimeout(() => {
        if (inputRef.current) inputRef.current.focus();
      }, 200);
      return () => {
        document.body.style.overflow = '';
      };
    }
  }, [isOpen]);

  if (!isOpen) return null;

  function handleAddComment(e) {
    e?.preventDefault();
    const text = newCommentText.trim();
    if (!text) return;

    if (replyingTo) {
      // Add nested reply to a specific comment
      setComments(prev => prev.map(c => {
        if (c.id === replyingTo.commentId) {
          const newReply = {
            id: `reply-${Date.now()}`,
            author: {
              name: currentUser.name || 'You',
              isAuthor: (currentUser.name === post?.author?.name),
              avatar: currentUser.avatar,
              headline: currentUser.headline || 'Member @ Grid',
              timeAgo: 'Just now'
            },
            replyTo: replyingTo.authorName,
            text: text,
            likes: 0,
            userLiked: false
          };
          return {
            ...c,
            replies: [...(c.replies || []), newReply]
          };
        }
        return c;
      }));
      setReplyingTo(null);
    } else {
      // Add top-level comment
      const tempId = `comment-${Date.now()}`;
      const newComment = {
        id: tempId,
        author: {
          name: currentUser.name || 'You',
          verified: true,
          degree: '1st',
          avatar: currentUser.avatar,
          headline: currentUser.headline || 'Member @ Grid',
          timeAgo: 'Just now'
        },
        text: text,
        likes: 0,
        userLiked: false,
        reactions: ['👍'],
        replies: []
      };
      setComments(prev => [newComment, ...prev]);

      const postId = post?.db_id || (typeof post?.id === 'string' ? post.id.replace('db-', '') : post?.id);
      const numId = parseInt(postId, 10);
      if (numId > 0) {
        gridApi.addComment(numId, text).then(res => {
          if (res && res.success && res.data) {
            setComments(prev => prev.map(c => c.id === tempId ? { ...c, id: res.data.id } : c));
          }
        }).catch(err => console.warn('Failed to persist comment:', err));
      }
    }

    setNewCommentText('');
    if (onAddCommentCount) onAddCommentCount();
  }

  function handleToggleCommentLike(commentId, isReply = false, replyId = null) {
    setComments(prev => prev.map(c => {
      if (!isReply && c.id === commentId) {
        const nextLiked = !c.userLiked;
        return {
          ...c,
          userLiked: nextLiked,
          likes: nextLiked ? c.likes + 1 : Math.max(0, c.likes - 1)
        };
      }
      if (isReply && c.id === commentId) {
        return {
          ...c,
          replies: (c.replies || []).map(r => {
            if (r.id === replyId) {
              const nextLiked = !r.userLiked;
              return {
                ...r,
                userLiked: nextLiked,
                likes: nextLiked ? r.likes + 1 : Math.max(0, r.likes - 1)
              };
            }
            return r;
          })
        };
      }
      return c;
    }));
  }

  function startReply(comment, authorName) {
    setReplyingTo({ commentId: comment.id, authorName });
    setNewCommentText(`@${authorName} `);
    if (inputRef.current) {
      inputRef.current.focus();
    }
  }

  return createPortal(
    <div 
      className="fixed inset-0 z-[100] flex flex-col justify-end items-center bg-black/60 backdrop-blur-[2px] transition-opacity duration-200"
      onClick={(e) => {
        if (e.target === e.currentTarget) onClose();
      }}
    >
      <div 
        className="w-full max-w-lg bg-white dark:bg-slate-900 rounded-t-3xl shadow-2xl flex flex-col max-h-[85vh] overflow-hidden animate-in slide-in-from-bottom duration-300 border-t border-slate-200/80 dark:border-slate-800"
        role="dialog"
        aria-modal="true"
      >
        {/* Top Drag Handle (matching reference screenshot) */}
        <div className="pt-3 pb-1 flex justify-center shrink-0 cursor-grab">
          <div className="w-12 h-1 bg-slate-900 dark:bg-slate-300 rounded-full" />
        </div>

        {/* Header Bar */}
        <div className="px-4 py-2 flex items-center justify-between shrink-0">
          <div className="relative">
            <button
              type="button"
              onClick={() => setShowSortMenu(prev => !prev)}
              className="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200 hover:text-blue-600 transition-colors cursor-pointer"
            >
              <span>{sortOption}</span>
              <svg className="w-3 h-3 fill-current text-slate-700 dark:text-slate-300" viewBox="0 0 20 20">
                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
              </svg>
            </button>

            {showSortMenu && (
              <div className="absolute left-0 top-6 z-20 w-36 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 py-1 text-xs">
                {['Most relevant', 'Most recent'].map(opt => (
                  <button
                    key={opt}
                    type="button"
                    className={`w-full text-left px-3 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-700 ${
                      sortOption === opt ? 'font-semibold text-blue-600' : 'text-slate-700 dark:text-slate-200'
                    }`}
                    onClick={() => {
                      setSortOption(opt);
                      setShowSortMenu(false);
                    }}
                  >
                    {opt}
                  </button>
                ))}
              </div>
            )}
          </div>

          <button
            type="button"
            onClick={onClose}
            className="w-7 h-7 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
            aria-label="Close comments"
          >
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        {/* Scrollable Comments List */}
        <div className="flex-1 overflow-y-auto px-4 py-2 space-y-4">
          {comments.map((comment) => (
            <div key={comment.id} className="relative">
              {/* Parent Comment Item */}
              <div className="flex items-start gap-3">
                <img
                  src={comment.author.avatar || '/assets/images/avatar.jpg'}
                  alt={comment.author.name}
                  onClick={() => handleUserClick(comment.author)}
                  className={`w-9 h-9 rounded-full object-cover shrink-0 ring-1 ring-slate-200 dark:ring-slate-700 mt-0.5 ${(comment.author?.id || comment.author?.user_id) ? 'cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all' : ''}`}
                  onError={(e) => {
                    e.currentTarget.onerror = null;
                    e.currentTarget.src = '/assets/images/avatar.jpg';
                  }}
                />
                <div className="flex-1 min-w-0">
                  {/* Author Meta Row */}
                  <div className="flex items-start justify-between">
                    <div>
                      <div className="flex items-center gap-1 flex-wrap text-xs">
                        <span 
                          onClick={() => handleUserClick(comment.author)}
                          className={`font-bold text-slate-900 dark:text-white ${(comment.author?.id || comment.author?.user_id) ? 'cursor-pointer hover:text-blue-600 hover:underline' : ''}`}
                        >
                          {comment.author.name}
                        </span>
                        {comment.author.verified && (
                          <svg className="w-3.5 h-3.5 text-slate-700 dark:text-slate-300 inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M9 12l2 2 4-4" />
                          </svg>
                        )}
                        {comment.author.degree && (
                          <span className="text-[11px] text-slate-500">
                            • {comment.author.degree}
                          </span>
                        )}
                        <span className="text-[11px] text-slate-400 ml-1">
                          {comment.author.timeAgo}
                        </span>
                        <button
                          type="button"
                          className="ml-1 text-[12px] font-bold text-[#0a66c2] hover:underline cursor-pointer"
                          onClick={(e) => {
                            e.currentTarget.textContent = 'Following';
                            e.currentTarget.classList.remove('text-[#0a66c2]');
                            e.currentTarget.classList.add('text-slate-400');
                          }}
                        >
                          Follow
                        </button>
                      </div>
                      <p className="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-xs">
                        {comment.author.headline}
                      </p>
                    </div>

                    <button 
                      type="button"
                      className="text-slate-400 hover:text-slate-600 p-1"
                      aria-label="Comment options"
                    >
                      <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="6" r="1.5" />
                        <circle cx="12" cy="12" r="1.5" />
                        <circle cx="12" cy="18" r="1.5" />
                      </svg>
                    </button>
                  </div>

                  {/* Comment Body */}
                  <p className="text-xs text-slate-800 dark:text-slate-200 mt-1 leading-relaxed break-words">
                    {comment.text}
                  </p>

                  {/* Actions & Reactions Row */}
                  <div className="flex items-center justify-between mt-1.5 pt-0.5">
                    <div className="flex items-center gap-4 text-xs text-slate-600 dark:text-slate-400">
                      <button
                        type="button"
                        onClick={() => handleToggleCommentLike(comment.id)}
                        className={`flex items-center gap-1 hover:text-blue-600 transition-colors cursor-pointer ${
                          comment.userLiked ? 'text-blue-600 font-semibold' : ''
                        }`}
                      >
                        <AnimatedThumbsUp isLiked={comment.userLiked} size={15} />
                        {comment.likes > 0 && <span className="font-semibold text-[11px]">{comment.likes}</span>}
                      </button>

                      <button
                        type="button"
                        onClick={() => startReply(comment, comment.author.name)}
                        className="flex items-center gap-1 hover:text-blue-600 transition-colors cursor-pointer"
                      >
                        <svg className="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        {comment.replies && comment.replies.length > 0 && (
                          <span className="font-semibold text-[11px]">{comment.replies.length}</span>
                        )}
                      </button>
                    </div>

                    {comment.reactions && comment.reactions.length > 0 && (
                      <div className="flex items-center">
                        <span className="w-4 h-4 rounded-full bg-rose-500 flex items-center justify-center text-[9px] text-white shadow-xs">
                          ❤️
                        </span>
                      </div>
                    )}
                  </div>
                </div>
              </div>

              {/* Nested Threaded Replies with Vertical Connector Line */}
              {comment.replies && comment.replies.length > 0 && (
                <div className="relative pl-7 mt-2 space-y-3">
                  {/* Curved / vertical connector line */}
                  <div 
                    className="absolute left-4 top-[-8px] bottom-4 w-5 border-l-2 border-b-2 border-slate-300 dark:border-slate-700 rounded-bl-xl pointer-events-none"
                  />

                  {comment.replies.map((reply) => (
                    <div key={reply.id} className="flex items-start gap-2.5">
                      <div className="relative shrink-0 mt-0.5">
                        <img
                          src={reply.author.avatar || '/assets/images/avatar.jpg'}
                          alt={reply.author.name}
                          onClick={() => handleUserClick(reply.author)}
                          className={`w-7 h-7 rounded-full object-cover ring-2 ring-emerald-500/80 p-0.5 bg-white ${(reply.author?.id || reply.author?.user_id) ? 'cursor-pointer hover:ring-blue-500 transition-all' : ''}`}
                          onError={(e) => {
                            e.currentTarget.onerror = null;
                            e.currentTarget.src = '/assets/images/avatar.jpg';
                          }}
                        />
                      </div>

                      <div className="flex-1 min-w-0">
                        <div className="flex items-start justify-between">
                          <div>
                            <div className="flex items-center gap-1.5 flex-wrap text-xs">
                              <span 
                                onClick={() => handleUserClick(reply.author)}
                                className={`font-bold text-slate-900 dark:text-white ${(reply.author?.id || reply.author?.user_id) ? 'cursor-pointer hover:text-blue-600 hover:underline' : ''}`}
                              >
                                {reply.author.name}
                              </span>
                              {reply.author.isAuthor && (
                                <span className="px-1.5 py-0.2 rounded-md bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium text-[10px]">
                                  Author
                                </span>
                              )}
                              <span className="text-[11px] text-slate-400">
                                • {reply.author.timeAgo}
                              </span>
                            </div>
                            <p className="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-xs">
                              {reply.author.headline}
                            </p>
                          </div>

                          <button 
                            type="button"
                            className="text-slate-400 hover:text-slate-600 p-0.5"
                            aria-label="Reply options"
                          >
                            <svg className="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                              <circle cx="12" cy="6" r="1.5" />
                              <circle cx="12" cy="12" r="1.5" />
                              <circle cx="12" cy="18" r="1.5" />
                            </svg>
                          </button>
                        </div>

                        {/* Reply Body */}
                        <p className="text-xs text-slate-800 dark:text-slate-200 mt-1 leading-relaxed break-words">
                          {reply.replyTo && (
                            <span className="text-[#0a66c2] font-bold mr-1 hover:underline cursor-pointer">
                              {reply.replyTo}
                            </span>
                          )}
                          {reply.text.replace(new RegExp(`^(Thank you\\s+)?@?${reply.replyTo}\\s*`), (m) => m.startsWith('Thank you') ? 'Thank you ' : '')}
                        </p>

                        {/* Actions */}
                        <div className="flex items-center gap-4 text-xs text-slate-600 dark:text-slate-400 mt-1">
                          <button
                            type="button"
                            onClick={() => handleToggleCommentLike(comment.id, true, reply.id)}
                            className={`flex items-center gap-1 hover:text-blue-600 transition-colors cursor-pointer ${
                              reply.userLiked ? 'text-blue-600 font-semibold' : ''
                            }`}
                          >
                            <AnimatedThumbsUp isLiked={reply.userLiked} size={13} />
                            {reply.likes > 0 && <span className="font-semibold text-[10px]">{reply.likes}</span>}
                          </button>

                          <button
                            type="button"
                            onClick={() => startReply(comment, reply.author.name)}
                            className="hover:text-blue-600 transition-colors cursor-pointer"
                          >
                            <svg className="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                          </button>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>
          ))}
          <div ref={listEndRef} />
        </div>

        {/* Replying banner indicator */}
        {replyingTo && (
          <div className="px-4 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-xs flex items-center justify-between border-t border-blue-100 dark:border-blue-900 shrink-0">
            <span>Replying to <strong>@{replyingTo.authorName}</strong></span>
            <button
              type="button"
              onClick={() => {
                setReplyingTo(null);
                setNewCommentText('');
              }}
              className="text-blue-500 hover:text-blue-700 font-semibold cursor-pointer"
            >
              Cancel
            </button>
          </div>
        )}

        {/* Sticky Bottom Input Bar (matching reference screenshot) */}
        <form onSubmit={handleAddComment} className="p-3 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shrink-0 space-y-2">
          {/* Avatar + Pill Input Field */}
          <div className="flex items-center gap-2.5">
            <img
              src={currentUser.avatar || '/assets/images/avatar.jpg'}
              alt={currentUser.name}
              className="w-8 h-8 rounded-full object-cover shrink-0 ring-1 ring-slate-300 dark:ring-slate-700"
              onError={(e) => {
                e.currentTarget.onerror = null;
                e.currentTarget.src = '/assets/images/avatar.jpg';
              }}
            />
            <div className="flex-1 relative">
              <input
                ref={inputRef}
                type="text"
                value={newCommentText}
                onChange={(e) => setNewCommentText(e.target.value)}
                placeholder={replyingTo ? `Reply to ${replyingTo.authorName}...` : "Add a comment..."}
                className="w-full rounded-full border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-blue-500 transition-all shadow-2xs"
              />
            </div>
          </div>

          {/* Bottom Icons & Blue "Comment" Submit Button Row */}
          <div className="flex items-center justify-between pl-10.5">
            <div className="flex items-center gap-3 text-slate-600 dark:text-slate-400">
              {/* Image upload icon */}
              <button
                type="button"
                className="hover:text-blue-600 transition-colors p-1 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                title="Add photo"
                onClick={() => {}}
              >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </button>

              {/* GIF icon */}
              <button
                type="button"
                className="px-1.5 py-0.5 rounded border border-slate-400 dark:border-slate-600 text-[10px] font-bold tracking-wider hover:border-blue-500 hover:text-blue-600 transition-colors cursor-pointer"
                title="Add GIF"
                onClick={() => setNewCommentText(prev => prev ? `${prev} 🖼️` : '🖼️')}
              >
                GIF
              </button>

              {/* Mention icon */}
              <button
                type="button"
                className="hover:text-blue-600 transition-colors p-1 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 font-bold text-sm cursor-pointer"
                title="Mention someone"
                onClick={() => setNewCommentText(prev => prev + '@')}
              >
                @
              </button>
            </div>

            <button
              type="submit"
              disabled={!newCommentText.trim()}
              className="px-5 py-1.5 rounded-full bg-[#0a66c2] hover:bg-[#004182] disabled:opacity-40 disabled:hover:bg-[#0a66c2] text-white font-semibold text-xs transition-all shadow-xs cursor-pointer"
            >
              Comment
            </button>
          </div>
        </form>
      </div>
    </div>,
    document.body
  );
}

/**
 * Generates post-tailored realistic comments matching the user's reference screenshot
 */
function getInitialComments(post) {
  const authorName = post?.author?.name || 'Author';

  if (post?.type === 'tech_artifact' || post?.artifact) {
    return [
      {
        id: 'c1',
        author: {
          name: 'Anushka A.',
          verified: true,
          degree: '3rd+',
          avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
          headline: 'Computer science student at Stella Maris College | Graphics & AI',
          timeAgo: '23h'
        },
        text: `Great insights, ${authorName.split(' ')[0].toLowerCase()} ❤️`,
        likes: 1,
        userLiked: false,
        reactions: ['❤️'],
        replies: [
          {
            id: 'c1-r1',
            author: {
              name: authorName,
              isAuthor: true,
              avatar: post?.author?.avatar || '/assets/images/avatar.jpg',
              headline: post?.author?.role || 'Author @ Grid',
              timeAgo: '23h',
              openToWork: true
            },
            replyTo: 'Anushka A.',
            text: 'Thank you Anushka A. ❤️😇 We benchmarked draw-call batching across WebGL nodes with zero frame drops.',
            likes: 1,
            userLiked: false
          }
        ]
      },
      {
        id: 'c2',
        author: {
          name: 'Devin Thorne',
          verified: true,
          degree: '2nd',
          avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80',
          headline: 'Senior WebGL Architect @ VoxelEngine',
          timeAgo: '18h'
        },
        text: 'The memory isolation benchmark between Three.js primitives and raw WebGL is top tier. Dropping this in our engineering Slack.',
        likes: 4,
        userLiked: false,
        reactions: ['👍'],
        replies: []
      }
    ];
  }

  if (post?.type === 'opportunity' || post?.job) {
    return [
      {
        id: 'c-job-1',
        author: {
          name: 'Priya Sundaram',
          verified: true,
          degree: '2nd',
          avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&auto=format&fit=crop&q=80',
          headline: 'Product Designer | Design Systems Lead',
          timeAgo: '2h'
        },
        text: `Just applied via Grid Identity! Excited about Kinetic AI's mission in autonomous agent orchestration. ✨`,
        likes: 2,
        userLiked: false,
        reactions: ['👏'],
        replies: [
          {
            id: 'c-job-r1',
            author: {
              name: authorName,
              isAuthor: true,
              avatar: post?.author?.avatar,
              headline: post?.author?.role || 'Founder & CEO',
              timeAgo: '1h',
              openToWork: false
            },
            replyTo: 'Priya Sundaram',
            text: 'Thanks Priya! Reviewing candidate portfolios today — your generative canvas work looks great.',
            likes: 1,
            userLiked: false
          }
        ]
      }
    ];
  }

  return [];
}
