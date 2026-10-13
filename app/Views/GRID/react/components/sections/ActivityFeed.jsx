import React, { useState } from 'react';
import AnimatedThumbsUp from '../ui/AnimatedThumbsUp.jsx';
import ExpandablePostText from '../ui/ExpandablePostText.jsx';
import CommentsDrawer from '../home/posts/CommentsDrawer.jsx';
import PostMediaCarousel from '../home/posts/PostMediaCarousel.jsx';
import { gridApi } from '../../gridApi';

/**
 * ActivityFeed Component
 *
 * Shows a profile's real posts (and what they've reposted) via
 * gridApi.getUserPosts(), with dynamic tab filtering, working Like/Repost/
 * Comment actions wired to the same backend as the Home feed, and a
 * "X reposted this" attribution banner matching the post-card convention.
 */
export default function ActivityFeed({
    posts = [],
    followersCount = 540,
    canEdit = true,
    onCreatePost,
    onManageVisibility,
    visibilityInfo,
    onNavigate,
    onToast
}) {
    const currentUserId = (typeof window !== 'undefined' && window.__GRID_DATA__?.user?.id) || null;
    const [activeTab, setActiveTab] = useState('posts');
    const [likedMap, setLikedMap] = useState({});
    const [likeCountMap, setLikeCountMap] = useState({});
    const [repostedMap, setRepostedMap] = useState({});
    const [isFollowingAuthor, setIsFollowingAuthor] = useState(false);
    const [activeCommentsPost, setActiveCommentsPost] = useState(null);
    const [commentCountMap, setCommentCountMap] = useState({});

    // Filter posts according to active tab. Only 'posts' is backed by real
    // data today — grid_posts has no comments/images/documents post type of
    // its own (comments live in a separate table), so those tabs stay empty
    // until/unless they get their own dedicated queries.
    const filteredPosts = posts.filter(post => {
        if (activeTab === 'posts') return true;
        const types = (post.type || '').split(' ');
        return types.includes(activeTab);
    });

    function isLiked(post) {
        return likedMap[post.id] !== undefined ? likedMap[post.id] : Boolean(post.has_liked);
    }

    function likeCount(post) {
        return likeCountMap[post.id] !== undefined ? likeCountMap[post.id] : (post.metrics?.likes || 0);
    }

    function isReposted(post) {
        return repostedMap[post.id] !== undefined ? repostedMap[post.id] : Boolean(post.has_reposted);
    }

    function commentCount(post) {
        return commentCountMap[post.id] !== undefined ? commentCountMap[post.id] : (post.metrics?.comments || 0);
    }

    async function toggleLike(post) {
        const nextLiked = !isLiked(post);
        setLikedMap(prev => ({ ...prev, [post.id]: nextLiked }));
        setLikeCountMap(prev => ({
            ...prev,
            [post.id]: nextLiked ? likeCount(post) + 1 : Math.max(0, likeCount(post) - 1)
        }));
        try {
            const postId = post.db_id || post.id;
            const res = await gridApi.toggleLike(postId);
            if (res && res.success && res.data) {
                setLikedMap(prev => ({ ...prev, [post.id]: res.data.liked }));
                setLikeCountMap(prev => ({ ...prev, [post.id]: res.data.likes_count }));
                if (res.data.liked && onToast) onToast('Post liked! ❤️');
            }
        } catch (err) {
            console.warn('Failed to toggle like:', err);
        }
    }

    function isOwnPost(post) {
        return Boolean(currentUserId && post.author?.id && String(post.author.id) === String(currentUserId));
    }

    async function toggleRepost(post) {
        if (isOwnPost(post)) return;
        const nextReposted = !isReposted(post);
        setRepostedMap(prev => ({ ...prev, [post.id]: nextReposted }));
        try {
            const postId = post.db_id || post.id;
            const res = await gridApi.toggleRepost(postId);
            if (res && res.success && res.data) {
                setRepostedMap(prev => ({ ...prev, [post.id]: res.data.reposted }));
                if (res.data.reposted && onToast) onToast('Reposted to your feed! 🔁');
            }
        } catch (err) {
            console.warn('Failed to toggle repost:', err);
        }
    }

    function toggleFollow() {
        setIsFollowingAuthor(prev => {
            const next = !prev;
            if (next && onToast) onToast('Following author! ✓');
            return next;
        });
    }

    function sharePost(url) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(() => {
                if (onToast) onToast('Post link copied to clipboard! 📋');
            });
        }
    }

    function handleAuthorClick(post) {
        if (onNavigate && post.author) {
            const authorId = post.author.id || post.author.user_id;
            if (authorId) {
                onNavigate('profile', {
                    targetUser: {
                        id: authorId,
                        name: post.author.name,
                        avatar: post.author.avatar,
                        headline: post.author.role,
                        gridId: post.author.grid_id
                    }
                });
            }
        }
    }

    return (
        <section className="g-card" id="gActivitySection">
            <div className="g-card-body">
                <div className="g-activity-header" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <div>
                            <h3 className="g-section-title" style={{ margin: 0 }}>Activity</h3>
                            <p id="gActivityFollowers" style={{ margin: '2px 0 0' }}>{followersCount} followers</p>
                        </div>
                    </div>
                    {canEdit && onCreatePost && (
                        <button
                            type="button"
                            className="g-btn-outline-brand"
                            onClick={onCreatePost}
                        >
                            Create a post
                        </button>
                    )}
                </div>

                {/* Filter Tabs */}
                <div className="g-tabs" id="gActivityTabs" role="tablist">
                    {['posts', 'comments', 'images', 'documents'].map(tab => (
                        <button
                            key={tab}
                            type="button"
                            className={`g-tab ${activeTab === tab ? 'active' : ''}`}
                            onClick={() => setActiveTab(tab)}
                            role="tab"
                            aria-selected={activeTab === tab}
                        >
                            {tab.charAt(0).toUpperCase() + tab.slice(1)}
                        </button>
                    ))}
                </div>

                {/* Activity Feed List */}
                <div id="gActivityFeedList">
                    {filteredPosts.length === 0 ? (
                        <div id="gActivityEmptyState" style={{ textAlign: 'center', padding: '32px 16px', color: 'var(--slate-500)' }}>
                            <div style={{ fontSize: '28px', marginBottom: '8px' }}>📭</div>
                            <div style={{ fontWeight: 600, fontSize: '14px', color: 'var(--slate-800)' }}>No items found in this section</div>
                            <div style={{ fontSize: '12px', marginTop: '4px' }}>When you post or interact, items will appear here.</div>
                        </div>
                    ) : (
                        filteredPosts.map(post => {
                            const liked = isLiked(post);
                            const reposted = isReposted(post);
                            const count = likeCount(post);
                            const postUrl = `https://grid.community/post/${post.db_id || post.id}`;
                            const authorInitials = post.author?.name
                                ? post.author.name.slice(0, 2).toUpperCase()
                                : 'U';

                            return (
                                <article key={post.id} className="g-post" id={post.id}>
                                    {/* Repost Attribution Banner — matches the Home feed post-card convention */}
                                    {post.is_repost && post.reposted_by && (
                                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px', marginBottom: '6px' }} className="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                            <span className="material-symbols-outlined" style={{ fontSize: '16px' }}>repeat</span>
                                            <span>{post.reposted_by.name || 'A member'} reposted this</span>
                                        </div>
                                    )}

                                    <div className="g-post-author">
                                        <div
                                            className="g-post-author-info"
                                            style={{ cursor: onNavigate ? 'pointer' : 'default' }}
                                            onClick={() => handleAuthorClick(post)}
                                        >
                                            <div className="g-post-avatar">
                                                {post.author?.avatar ? (
                                                    <img
                                                        src={post.author.avatar}
                                                        alt={post.author.name}
                                                        style={{ width: '100%', height: '100%', borderRadius: '50%', objectFit: 'cover' }}
                                                        onError={(e) => { e.currentTarget.style.display = 'none'; }}
                                                    />
                                                ) : authorInitials}
                                            </div>
                                            <div>
                                                <div className="g-post-name">
                                                    {post.author?.name || 'Community Member'} <span>• {post.author?.timeAgo || 'Recently'}</span>
                                                </div>
                                                <div className="g-post-role">
                                                    {post.author?.role || 'Member'}
                                                </div>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            className={`g-btn-follow ${isFollowingAuthor ? 'g-following' : ''}`}
                                            onClick={toggleFollow}
                                            style={isFollowingAuthor ? { background: 'var(--slate-100)', color: 'var(--slate-700)' } : {}}
                                        >
                                            {isFollowingAuthor ? 'Following ✓' : 'Follow +'}
                                        </button>
                                    </div>

                                    <div className="g-post-body">
                                        <ExpandablePostText text={post.content} maxChars={150} />
                                    </div>

                                    <PostMediaCarousel
                                        media={post.media}
                                        singleUrl={post.media_url || post.image}
                                        authorName={post.author?.name || 'Creator'}
                                    />

                                    <div className="g-post-actions">
                                        <div className="g-post-actions-left">
                                            <button
                                                type="button"
                                                className={`g-post-action g-like-btn ${liked ? 'g-liked' : ''}`}
                                                onClick={() => toggleLike(post)}
                                                style={{ display: 'inline-flex', alignItems: 'center', gap: '6px' }}
                                                aria-label={liked ? 'Unlike post' : 'Like post'}
                                            >
                                                <AnimatedThumbsUp isLiked={liked} size={17} />
                                                <span className="g-like-count">{count}</span>
                                            </button>
                                            <button
                                                type="button"
                                                className="g-post-action"
                                                onClick={() => setActiveCommentsPost(post)}
                                            >
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
                                                </svg>
                                                <span>{commentCount(post)}</span>
                                            </button>
                                            {!isOwnPost(post) && (
                                                <button
                                                    type="button"
                                                    className="g-post-action"
                                                    onClick={() => toggleRepost(post)}
                                                    style={{ display: 'inline-flex', alignItems: 'center', gap: '6px', color: reposted ? '#059669' : undefined }}
                                                    aria-label={reposted ? 'Undo repost' : 'Repost'}
                                                >
                                                    <span className="material-symbols-outlined" style={{ fontSize: '17px' }}>repeat</span>
                                                    <span>{reposted ? 'Reposted' : 'Repost'}</span>
                                                </button>
                                            )}
                                            <button
                                                type="button"
                                                className="g-post-action"
                                                onClick={() => sharePost(postUrl)}
                                            >
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
                                                </svg>
                                                <span>Share</span>
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            );
                        })
                    )}
                </div>
            </div>

            <CommentsDrawer
                isOpen={Boolean(activeCommentsPost)}
                onClose={() => setActiveCommentsPost(null)}
                post={activeCommentsPost || {}}
                onAddCommentCount={() => {
                    if (!activeCommentsPost) return;
                    setCommentCountMap(prev => ({
                        ...prev,
                        [activeCommentsPost.id]: commentCount(activeCommentsPost) + 1
                    }));
                }}
                onNavigate={onNavigate}
            />
        </section>
    );
}
