import React, { useState } from 'react';

/**
 * FollowingFollowersView Component
 * Renders the Following & Followers management view with tabs and search.
 */
export default function FollowingFollowersView({ onBack, onToast }) {
    const [activeTab, setActiveTab] = useState('following'); // 'following' | 'followers'
    const [searchQuery, setSearchQuery] = useState('');

    const initialFollowing = [
        {
            id: 'f-1',
            name: 'Andrej Karpathy',
            headline: 'Building Eureka Labs • Former Tesla AI & OpenAI',
            avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80',
            followers: '1.2M followers'
        },
        {
            id: 'f-2',
            name: 'Guillermo Rauch',
            headline: 'CEO @ Vercel • Creator of Next.js',
            avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=256&q=80',
            followers: '450K followers'
        },
        {
            id: 'f-3',
            name: 'Sarah Drasner',
            headline: 'Director of Engineering @ Google • Author & Speaker',
            avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=256&q=80',
            followers: '280K followers'
        }
    ];

    const initialFollowers = [
        {
            id: 'fl-1',
            name: 'Deepak Verma',
            headline: 'Senior Full Stack Engineer at TechWave Systems',
            avatar: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=256&q=80',
            mutual: '18 mutual connections'
        },
        {
            id: 'fl-2',
            name: 'Ananya Roy',
            headline: 'Technical Recruiter at Novatech Global Solutions',
            avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=256&q=80',
            mutual: '12 mutual connections'
        }
    ];

    const [followingList, setFollowingList] = useState(initialFollowing);
    const [followingStateMap, setFollowingStateMap] = useState({ 'f-1': true, 'f-2': true, 'f-3': true });

    const handleToggleFollow = (person) => {
        const next = !followingStateMap[person.id];
        setFollowingStateMap(prev => ({ ...prev, [person.id]: next }));
        onToast && onToast(next ? `Following ${person.name}` : `Unfollowed ${person.name}`);
    };

    const currentList = activeTab === 'following' ? followingList : initialFollowers;
    const filteredList = currentList.filter(p => p.name.toLowerCase().includes(searchQuery.toLowerCase().trim()));

    return (
        <section aria-label="Following and Followers" className="w-full space-y-3">
            {/* Back button */}
            <div className="flex items-center justify-between px-3.5 sm:px-0 pb-1">
                <button
                    type="button"
                    onClick={onBack}
                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer"
                >
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Back to Network</span>
                </button>
            </div>

            {/* Container */}
            <div className="g-glass-card rounded-none sm:rounded-2xl border-x-0 sm:border-x border-y sm:border border-white/70 dark:border-slate-800/80 shadow-sm overflow-hidden">
                <div className="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div className="flex items-center gap-4 border-b sm:border-b-0 border-slate-100 dark:border-slate-800 pb-2 sm:pb-0">
                        <button
                            type="button"
                            onClick={() => setActiveTab('following')}
                            className={`text-sm font-bold pb-1 cursor-pointer transition-colors ${
                                activeTab === 'following'
                                    ? 'text-blue-600 border-b-2 border-blue-600'
                                    : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'
                            }`}
                        >
                            Following ({followingList.length})
                        </button>
                        <button
                            type="button"
                            onClick={() => setActiveTab('followers')}
                            className={`text-sm font-bold pb-1 cursor-pointer transition-colors ${
                                activeTab === 'followers'
                                    ? 'text-blue-600 border-b-2 border-blue-600'
                                    : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'
                            }`}
                        >
                            Followers (3,890)
                        </button>
                    </div>

                    <div className="relative sm:w-60">
                        <input
                            type="text"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            placeholder="Filter by name"
                            className="w-full px-3 py-1.5 bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                        />
                    </div>
                </div>

                <div className="divide-y divide-slate-100 dark:divide-slate-800/80">
                    {filteredList.map((person) => {
                        const isFollowing = !!followingStateMap[person.id];

                        return (
                            <div
                                key={person.id}
                                className="p-4 sm:p-5 flex items-center justify-between gap-4 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors"
                            >
                                <div className="flex items-center gap-3.5 min-w-0">
                                    <img
                                        src={person.avatar}
                                        alt={person.name}
                                        className="w-12 h-12 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-700 shrink-0"
                                    />
                                    <div className="min-w-0 space-y-0.5">
                                        <h2 className="text-sm font-bold text-slate-900 dark:text-white truncate">
                                            {person.name}
                                        </h2>
                                        <p className="text-xs text-slate-600 dark:text-slate-300 line-clamp-1">
                                            {person.headline}
                                        </p>
                                        <p className="text-[11px] text-slate-400 dark:text-slate-500">
                                            {person.followers || person.mutual}
                                        </p>
                                    </div>
                                </div>

                                <div className="shrink-0">
                                    <button
                                        type="button"
                                        onClick={() => handleToggleFollow(person)}
                                        className={`px-4 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer ${
                                            isFollowing
                                                ? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700'
                                                : 'bg-blue-600 hover:bg-blue-700 text-white shadow-2xs'
                                        }`}
                                    >
                                        {isFollowing ? 'Following' : '+ Follow'}
                                    </button>
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>
        </section>
    );
}
