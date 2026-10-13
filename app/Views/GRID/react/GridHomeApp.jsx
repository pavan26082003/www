import React, { useState, useEffect } from 'react';
import DisciplineFilterBar from './components/home/DisciplineFilterBar';
import LeftHomeRail from './components/home/LeftHomeRail';
import PostComposer from './components/home/PostComposer';
import SuggestedTopics from './components/home/SuggestedTopics';
import TopicHubBanner from './components/home/TopicHubBanner';
import TechPostCard from './components/home/posts/TechPostCard';
import JobPostCard from './components/home/posts/JobPostCard';
import MarketPostCard from './components/home/posts/MarketPostCard';
import VisitingCardWidget from './components/home/right/VisitingCardWidget';
import OnlineUsersWidget from './components/home/right/OnlineUsersWidget';
import PermissionsWidget from './components/home/right/PermissionsWidget';
import { getHomeInitialState } from './homeState';
import { gridApi } from './gridApi';

const DEFAULT_AVATAR = '/assets/images/avatar.jpg';

export function formatAvatarUrl(avatar, fallback = DEFAULT_AVATAR) {
  if (!avatar || typeof avatar !== 'string') return fallback;
  if (avatar.startsWith('a:') && avatar.includes('file_name')) {
    const match = avatar.match(/"file_name";s:\d+:"([^"]+)"/);
    if (match && match[1]) {
      return `/files/profile_images/${match[1]}`;
    }
    return fallback;
  }
  if (avatar.startsWith('_file') && (avatar.endsWith('.png') || avatar.endsWith('.jpg') || avatar.endsWith('.jpeg'))) {
    return `/files/profile_images/${avatar}`;
  }
  return avatar;
}

/**
 * GridHomeApp Component
 * Master Orchestrator for the GRID Home Workspace (Feed & Suggested Topics).
 * Implements the 3-column unified layout:
 * - Left Rail (3 cols): Mini Profile, + Add/Edit Experience, GRIDLINKS, Nav Menu
 * - Center Stream (6 cols): Discipline Filter Bar, Quick Composer, Suggested Topics Carousel, Feed Tabs & 3 Post Archetypes
 * - Right Rail (3 cols): Visiting Card QR Matrix, eFactor Online Telemetry, Permission Levels Matrix, Footer
 */
export default function GridHomeApp({ onNavigate, onToast }) {
  const [homeData, setHomeData] = useState(() => getHomeInitialState());
  const [selectedDiscipline, setSelectedDiscipline] = useState(null);
  const [activeFeedTab, setActiveFeedTab] = useState('all');
  const [selectedTopic, setSelectedTopic] = useState(null);
  const [activeComposerTag, setActiveComposerTag] = useState(null);
  const [showTopicComposer, setShowTopicComposer] = useState(false);

  // Dynamically initialize authenticated user identity (name, grid_id, avatar, headline)
  useEffect(() => {
    async function loadIdentity() {
      try {
        const profileData = await gridApi.getProfile();
        if (profileData && profileData.user) {
          const user = profileData.user;
          const profile = profileData.profile || {};
          const nameParts = (user.name || '').trim().split(/\s+/);
          const firstName = nameParts[0] || user.name || 'User';
          const initials = user.initials || (nameParts[0]?.[0] || '') + (nameParts[1]?.[0] || 'U');
          const resolvedGridId = profile.grid_id || `ID-${user.id}`;
          const resolvedAvatar = formatAvatarUrl(user.avatar);

          setHomeData((prev) => ({
            ...prev,
            currentUser: {
              ...prev.currentUser,
              id: String(user.id),
              name: user.name,
              firstName: firstName,
              role: profile.headline || prev.currentUser?.role || 'Update your profile',
              avatar: resolvedAvatar,
              gridId: resolvedGridId,
              initials: initials
            },
            visitingCard: {
              ...prev.visitingCard,
              owner: user.name,
              cardId: `#GRID-${resolvedGridId}`
            }
          }));
        }
      } catch (err) {
        console.warn('Could not load user profile on home page:', err);
      }
    }

    async function loadFeedPosts() {
      try {
        const dbPosts = await gridApi.getPosts(30, 0);
        if (Array.isArray(dbPosts) && dbPosts.length > 0) {
          const normalized = dbPosts.map((p) => ({
            ...p,
            author: {
              ...p.author,
              avatar: formatAvatarUrl(p.author?.avatar),
              gridId: p.author?.grid_id || p.author?.gridId || ''
            }
          }));
          setHomeData((prev) => {
            const existingIds = new Set(normalized.map(dp => dp.id));
            const remainingLocal = prev.posts.filter(lp => !existingIds.has(lp.id));
            return {
              ...prev,
              posts: [...normalized, ...remainingLocal]
            };
          });
        }
      } catch (err) {
        console.warn('Could not load database posts:', err);
      }
    }

    async function loadTopChallenges() {
      try {
        const challenges = await gridApi.getTopChallenges(6);
        if (Array.isArray(challenges) && challenges.length > 0) {
          setHomeData((prev) => {
            const currentTopics = prev.suggestedTopics || [];
            const normalizedChallenges = challenges.map(ch => ({
              ...ch,
              id: `challenge-${ch.id}`,
              db_challenge_id: ch.id,
              isChallenge: true,
              title: ch.title,
              tag: ch.tag,
              description: ch.description,
              stat1Val: ch.prize_amount,
              stat1Label: 'Prize',
              stat2Val: ch.time_left_str || 'Active',
              stat2Label: 'Left',
              bgImage: ch.bg_image || 'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=600&auto=format&fit=crop&q=80',
              iconType: ch.icon_type || 'trophy',
              company: ch.company || { name: ch.company_name, logo: ch.company_logo }
            }));

            const existingSlugs = new Set(normalizedChallenges.map(nc => nc.slug));
            const remaining = currentTopics.filter(t => !existingSlugs.has(t.slug) && !existingSlugs.has(t.id));
            return {
              ...prev,
              suggestedTopics: [...normalizedChallenges, ...remaining]
            };
          });
        }
      } catch (err) {
        console.warn('Could not load company challenges:', err);
      }
    }

    loadIdentity();
    loadFeedPosts();
    loadTopChallenges();

    const handleWizardCompleted = (e) => {
      const wizardData = e.detail;
      if (wizardData) {
        const nameParts = (wizardData.full_name || '').trim().split(/\s+/);
        const firstName = nameParts[0] || 'User';
        const initials = ((nameParts[0]?.[0] || '') + (nameParts[1]?.[0] || 'U')).toUpperCase();
        setHomeData((prev) => ({
          ...prev,
          currentUser: {
            ...prev.currentUser,
            name: wizardData.full_name || prev.currentUser?.name,
            firstName: firstName,
            role: wizardData.headline || prev.currentUser?.role,
            gridId: wizardData.grid_id || prev.currentUser?.gridId,
            initials: initials
          },
          visitingCard: {
            ...prev.visitingCard,
            owner: wizardData.full_name || prev.visitingCard?.owner,
            cardId: wizardData.grid_id ? `#GRID-${wizardData.grid_id}` : prev.visitingCard?.cardId
          }
        }));
      }
      loadIdentity();
    };

    window.addEventListener('grid:profile-wizard-completed', handleWizardCompleted);
    return () => window.removeEventListener('grid:profile-wizard-completed', handleWizardCompleted);
  }, []);

  const showToast = (msg) => {
    if (onToast) onToast(msg);
  };

  const handlePostCreated = (newPost) => {
    setHomeData((prev) => ({
      ...prev,
      posts: [newPost, ...prev.posts]
    }));
    showToast('Post published successfully to your GRID feed! 🚀');
  };

  const handleApplyJob = (jobPost) => {
    showToast(`Application submitted to ${jobPost.job?.company || 'Kinetic AI'} with Grid Identity!`);
  };

  const handleRequestScope = (marketPost) => {
    showToast(`Scope request sent to ${marketPost.author?.name || 'Sarah Chen'}!`);
  };

  const handleShareVisitingCard = () => {
    showToast('Visiting card link copied to clipboard!');
  };

  const handleSavePng = () => {
    showToast('Visiting card SVG downloaded!');
  };

  const handleDeletePost = (postId) => {
    setHomeData((prev) => ({
      ...prev,
      posts: prev.posts.filter((p) => p.id !== postId && p.db_id !== postId)
    }));
    showToast('Post deleted from your feed.');
  };

  const handleSelectTopic = (topic) => {
    if (selectedTopic?.id === topic.id) {
      setSelectedTopic(null);
      setShowTopicComposer(false);
      showToast('Showing all feed updates');
    } else {
      setSelectedTopic(topic);
      setShowTopicComposer(false);
      showToast(`Viewing curated #${topic.title} stream`);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  };

  const handleClearTopic = () => {
    setSelectedTopic(null);
    setShowTopicComposer(false);
    showToast('Showing all feed updates');
  };

  const handlePostToTopic = (cleanTag) => {
    setActiveComposerTag(cleanTag);
    setShowTopicComposer(true);
    showToast(`Added ${cleanTag} to composer tags`);
  };

  const handleAddTopic = (newTopic) => {
    setHomeData((prev) => ({
      ...prev,
      suggestedTopics: [newTopic, ...(prev.suggestedTopics || [])]
    }));
    showToast(`Created new community topic #${newTopic.title}! 🎉`);
  };

  // Filter posts based on activeFeedTab, selectedDiscipline, and selectedTopic
  const filteredPosts = homeData.posts.filter((post) => {
    // 1. Tab filter
    if (activeFeedTab === 'artifacts' && post.type !== 'tech_artifact') return false;
    if (activeFeedTab === 'opportunities' && post.type !== 'opportunity') return false;
    if (activeFeedTab === 'marketplace' && post.type !== 'marketplace') return false;

    // 2. Discipline filter
    if (selectedDiscipline && post.author?.discipline && post.author.discipline !== selectedDiscipline) {
      // Allow general or exact match
      if (post.author.discipline !== 'General' && post.author.discipline !== selectedDiscipline) {
        return false;
      }
    }

    // 3. Topic filter (Contra.com style: only activates when a topic card is explicitly clicked)
    if (selectedTopic) {
      const topicLower = selectedTopic.title.toLowerCase();
      const topicClean = topicLower.replace(/[^a-z0-9]/g, '');
      // Extract distinctive keywords (exclude generic stopwords like 'design', 'challenge')
      const stopWords = new Set(['design', 'challenge', 'the', 'and', 'for', 'with', 'hub']);
      const distinctWords = topicLower.split(/\s+/).filter(w => w.length > 2 && !stopWords.has(w));

      // 1. Check post tags array (e.g. #BrandDesign, #BrandDesigner, #Branding)
      const postTags = Array.isArray(post.tags) ? post.tags : [];
      const tagMatch = postTags.some((tag) => {
        const cleanTag = String(tag).toLowerCase().replace(/[^a-z0-9]/g, '');
        return cleanTag.includes(topicClean) || 
               topicClean.includes(cleanTag) ||
               distinctWords.some(dw => cleanTag.includes(dw));
      });

      // 2. Check full title or compound phrase in text
      const contentText = ((post.content || '') + ' ' + (post.title || '')).toLowerCase();
      const authorRole = (post.author?.role || '').toLowerCase();
      const authorDiscipline = (post.author?.discipline || '').toLowerCase();

      const phraseMatch = contentText.includes(topicLower) || 
                          authorRole.includes(topicLower) ||
                          authorDiscipline.includes(topicLower);

      // 3. Check distinct keywords (e.g. 'brand' or 'midjourney') without false positives from generic words
      const distinctMatch = distinctWords.length > 0 && distinctWords.some(dw => 
        contentText.includes(dw) || authorRole.includes(dw)
      );

      if (!tagMatch && !phraseMatch && !distinctMatch) {
        return false;
      }
    }

    return true;
  });

  const currentUser = {
    fullName: homeData.currentUser?.name || (typeof window !== 'undefined' && window.__GRID_DATA__?.user?.name) || 'User',
    avatarSrc: homeData.currentUser?.avatar || (typeof window !== 'undefined' && window.__GRID_DATA__?.user?.avatar) || DEFAULT_AVATAR,
    initials: homeData.currentUser?.initials || (typeof window !== 'undefined' && window.__GRID_DATA__?.user?.initials) || 'U'
  };

  return (
    <div className="w-full font-sans">
      {/* Main 3-Column Home Workspace Container */}
      <main className="max-w-7xl mx-auto px-0 sm:px-6 lg:px-8 py-2 sm:py-6 w-full">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
          {/* ================= LEFT COLUMN: Structural Sidebar (hidden on mobile, sticky on desktop) ================= */}
          <aside className="hidden lg:block lg:col-span-3 space-y-4 sticky top-6 self-start">
            <LeftHomeRail
              profileData={homeData.currentUser}
              onNavigate={onNavigate}
              onAddExperience={() => {
                if (onNavigate) {
                  onNavigate('profile', { openModal: 'experience' });
                } else {
                  window.location.href = '/index.php/grid/profile';
                }
              }}
              onProfileSettings={() => {
                if (onNavigate) {
                  onNavigate('profile');
                } else {
                  window.location.href = '/index.php/grid/profile';
                }
              }}
            />
          </aside>

          {/* ================= CENTER COLUMN: Feed Command Center (full width on mobile, 6 cols on desktop) ================= */}
          <section className="w-full lg:col-span-6 space-y-2 sm:space-y-4 min-w-0">
            {/* Top Workspace Section: Visible by default, completely HIDDEN when a topic is active */}
            {!selectedTopic ? (
              <>
                {/* Top Horizontal Role / Discipline Filter Rail */}
                <DisciplineFilterBar
                  disciplines={homeData.disciplines}
                  selectedDiscipline={selectedDiscipline}
                  onSelectDiscipline={setSelectedDiscipline}
                />

                {/* Quick Post / Artifact Composer Box */}
                <PostComposer
                  currentUser={homeData.currentUser}
                  onPostCreated={handlePostCreated}
                  externalTag={activeComposerTag}
                />

                {/* Suggested Topics Multi-Card Carousel */}
                <SuggestedTopics
                  topics={homeData.suggestedTopics}
                  selectedTopic={selectedTopic}
                  onSelectTopic={handleSelectTopic}
                  onAddTopic={handleAddTopic}
                />
              </>
            ) : (
              <>
                {/* Topic Hero Hub Banner: Replaces top section when a topic is active */}
                <TopicHubBanner
                  topic={selectedTopic}
                  onClear={handleClearTopic}
                  onPostToTopic={handlePostToTopic}
                />

                {/* Inline Composer for Challenge Entries / Topic Posts */}
                {showTopicComposer && (
                  <div className="relative rounded-2xl border border-blue-500/30 bg-blue-500/5 p-3 sm:p-4 animate-fadeIn space-y-2">
                    <div className="flex items-center justify-between px-1">
                      <span className="text-xs font-semibold text-blue-600 dark:text-blue-400 flex items-center gap-1.5 font-mono">
                        <span className="material-symbols-outlined text-[16px]">edit_note</span>
                        <span>Submit post to #{selectedTopic.title}</span>
                      </span>
                      <button
                        type="button"
                        onClick={() => setShowTopicComposer(false)}
                        className="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs flex items-center gap-1 cursor-pointer"
                      >
                        <span className="material-symbols-outlined text-[16px]">close</span>
                        <span>Cancel</span>
                      </button>
                    </div>
                    <PostComposer
                      currentUser={homeData.currentUser}
                      onPostCreated={(post) => {
                        handlePostCreated(post);
                        setShowTopicComposer(false);
                      }}
                      externalTag={activeComposerTag}
                    />
                  </div>
                )}
              </>
            )}

            {/* Feed Stream */}
            <div className="space-y-4">
              {filteredPosts.map((post) => {
                if (post.type === 'opportunity') {
                  return (
                    <JobPostCard
                      key={post.id}
                      post={post}
                      onApply={handleApplyJob}
                      onNavigate={onNavigate}
                    />
                  );
                }
                if (post.type === 'marketplace') {
                  return (
                    <MarketPostCard
                      key={post.id}
                      post={post}
                      onRequestScope={handleRequestScope}
                      onNavigate={onNavigate}
                    />
                  );
                }
                return (
                  <TechPostCard
                    key={post.id}
                    post={post}
                    onDeletePost={handleDeletePost}
                    onNavigate={onNavigate}
                  />
                );
              })}

              {filteredPosts.length === 0 && (
                selectedTopic ? (
                  <div className="p-8 text-center bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-slate-300 dark:border-slate-800 space-y-3 shadow-xs">
                    <div className="w-12 h-12 mx-auto rounded-full bg-blue-50 dark:bg-blue-950/40 text-blue-500 flex items-center justify-center">
                      <span className="material-symbols-outlined text-[24px]">tag</span>
                    </div>
                    <div>
                      <h4 className="font-semibold text-slate-800 dark:text-slate-200">
                        No updates in #{selectedTopic.title} yet
                      </h4>
                      <p className="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                        Be the first creator to share work, artifacts, or challenge entries in #{selectedTopic.title}!
                      </p>
                    </div>
                    <div className="flex items-center justify-center gap-2 pt-1">
                      <button
                        type="button"
                        onClick={() => handlePostToTopic('#' + selectedTopic.title.replace(/\s+/g, ''))}
                        className="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors cursor-pointer"
                      >
                        Publish First Post
                      </button>
                      <button
                        type="button"
                        onClick={handleClearTopic}
                        className="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition-colors cursor-pointer"
                      >
                        Show All Updates
                      </button>
                    </div>
                  </div>
                ) : (
                  <div className="p-8 text-center bg-white rounded-2xl border border-slate-200 text-slate-500 text-sm">
                    No posts found matching the selected filter.
                  </div>
                )
              )}
            </div>
          </section>

          {/* ================= RIGHT COLUMN: Identity & Telemetry Widgets (hidden on mobile, sticky on desktop) ================= */}
          <aside className="hidden lg:block lg:col-span-3 space-y-4 sticky top-6 self-start">
            {/* Widget 1: Praful Shah's Visiting Card / QR Code */}
            <VisitingCardWidget
              cardData={homeData.visitingCard}
              onShare={handleShareVisitingCard}
              onSavePng={handleSavePng}
            />

            {/* Widget 2: eFactor & Grid Users Online Telemetry */}
            <OnlineUsersWidget
              onlineData={homeData.onlineUsers}
              onViewConnections={() => {
                if (onNavigate) {
                  onNavigate('network');
                } else {
                  showToast('Opening verified network graph...');
                }
              }}
            />

            {/* Widget 3: Permission Levels Used & Platform Footer */}
            <PermissionsWidget />
          </aside>
        </div>
      </main>
    </div>
  );
}
