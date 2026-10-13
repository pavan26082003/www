import React, { useState } from 'react';

/**
 * TeamWelcomeHub Component
 * Exact match of the Main Workspace Page in the user's reference screenshots (Images 1, 2, 3, 4):
 * - Hero with waving hand emoji 👋: "Welcome K Pavan Kumar! Let's get you collaborating!"
 * - "Your team is on a Pro trial • 29 days left"
 * - Promo banner in top right: "🎁 Get 55% off for 12 months!"
 * - 3 Action Cards:
 *     1. "Start a channel" (+ icon) -> creates channel
 *     2. "Bring in teammates" (user icon) -> invites teammates
 *     3. "Sai Tulasi Ram" (avatar icon) -> "👋 Welcome them >" -> jumps straight to DM chat
 * - Progress checklist: "Set up Slack for your team" (75% circle gauge)
 * - Bottom banner: "Huddles notes, Thread and channel summaries, and 1 more AI feature are turned on for GRID. Learn more" with ✕ dismiss
 */
export default function TeamWelcomeHub({
    currentUser,
    workspaces = [],
    onStartChannel,
    onInviteTeammates,
    onSelectDM,
    onToast
}) {
    const [isAiBannerVisible, setIsAiBannerVisible] = useState(true);
    const [isPromoVisible, setIsPromoVisible] = useState(true);

    const userName = currentUser?.name || 'K Pavan Kumar';

    return (
        <div className="flex-1 h-full bg-[#FFFFFF] text-slate-900 flex flex-col overflow-y-auto relative select-none">
            {/* Main Stage Content */}
            <div className="flex-1 px-8 lg:px-14 py-8 max-w-6xl w-full mx-auto flex flex-col justify-between">
                <div>
                    {/* Top Row: Hero Text + Right Promo Card */}
                    <div className="flex flex-col lg:flex-row items-start justify-between gap-6 mb-10">
                        <div>
                            {/* Waving Hand Emoji */}
                            <div className="text-3xl mb-3 animate-bounce">👋</div>

                            <h1 className="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 leading-tight">
                                Welcome {userName}!
                                <br />
                                Let's get you collaborating!
                            </h1>

                            <p className="text-sm font-medium text-slate-500 mt-2 flex items-center gap-2">
                                <span>Your team is on a Pro trial</span>
                                <span>•</span>
                                <span>29 days left</span>
                            </p>
                        </div>

                        {/* Top-Right Promo Card (Get 55% off for 12 months!) */}
                        {isPromoVisible && (
                            <div className="bg-white border border-[#E2E8F0] rounded-2xl p-4 shadow-xs hover:shadow-md transition-shadow flex items-center gap-3 shrink-0 relative group max-w-xs">
                                <div className="w-10 h-10 rounded-xl bg-blue-50 text-[#1164A3] flex items-center justify-center text-xl shrink-0">
                                    🎁
                                </div>
                                <div>
                                    <div className="font-bold text-xs text-[#1D1C1D]">Get 55% off for 12 months!</div>
                                    <button
                                        type="button"
                                        onClick={() => onToast && onToast('Discount applied to your subscription')}
                                        className="text-[11px] font-bold text-[#007A5A] hover:text-[#006C4E] hover:underline"
                                    >
                                        Upgrade now →
                                    </button>
                                </div>
                                <button
                                    type="button"
                                    onClick={() => setIsPromoVisible(false)}
                                    className="absolute top-2 right-2 text-slate-400 hover:text-slate-600 text-xs p-1 cursor-pointer"
                                >
                                    ✕
                                </button>
                            </div>
                        )}
                    </div>

                    {/* 3 Quick Action Cards in a Horizontal Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
                        {/* Card 1: Start a channel */}
                        <div
                            onClick={onStartChannel}
                            className="bg-white border border-[#E2E8F0] hover:border-[#1164A3]/40 hover:shadow-md rounded-2xl p-6 flex flex-col justify-between transition-all cursor-pointer group"
                        >
                            <div>
                                <div className="w-10 h-10 rounded-full bg-[#F8F8F8] group-hover:bg-blue-50 text-[#1D1C1D] group-hover:text-[#1164A3] flex items-center justify-center text-lg font-bold mb-4 transition-colors">
                                    +
                                </div>
                                <h3 className="font-bold text-base text-[#1D1C1D] group-hover:text-[#1164A3] transition-colors mb-1.5">
                                    Start a channel
                                </h3>
                                <p className="text-xs text-[#616061] leading-relaxed">
                                    Create a chat room for specific topics or projects.
                                </p>
                            </div>
                        </div>

                        {/* Card 2: Bring in teammates */}
                        <div
                            onClick={onInviteTeammates}
                            className="bg-white border border-[#E2E8F0] hover:border-[#1164A3]/40 hover:shadow-md rounded-2xl p-6 flex flex-col justify-between transition-all cursor-pointer group"
                        >
                            <div>
                                <div className="w-10 h-10 rounded-full bg-[#F8F8F8] group-hover:bg-blue-50 text-[#1D1C1D] group-hover:text-[#1164A3] flex items-center justify-center text-lg mb-4 transition-colors">
                                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                <h3 className="font-bold text-base text-[#1D1C1D] group-hover:text-[#1164A3] transition-colors mb-1.5">
                                    Bring in teammates
                                </h3>
                                <p className="text-xs text-[#616061] leading-relaxed">
                                    Invite and add them to channels to collaborate.
                                </p>
                            </div>
                        </div>

                        {/* Card 3: Sai Tulasi Ram */}
                        <div
                            onClick={() => onSelectDM('dm-sai')}
                            className="bg-white border border-[#E2E8F0] hover:border-[#1164A3]/40 hover:shadow-md rounded-2xl p-6 flex flex-col justify-between transition-all cursor-pointer group"
                        >
                            <div>
                                <div className="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-lg mb-4">
                                    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                    </svg>
                                </div>
                                <h3 className="font-bold text-base text-[#1D1C1D] group-hover:text-[#1164A3] transition-colors mb-1">
                                    Sai Tulasi Ram
                                </h3>
                                <p className="text-xs text-[#616061] mb-4">
                                    Invited by you
                                </p>
                            </div>
                            <button
                                type="button"
                                className="text-xs font-bold text-[#007A5A] group-hover:text-[#006C4E] flex items-center gap-1 mt-2 transition-colors"
                            >
                                <span>👋 Welcome them &gt;</span>
                            </button>
                        </div>
                    </div>

                    {/* Progress Checklist Tracker: Set up Slack for your team */}
                    <div className="pt-2 pb-6">
                        <div className="flex items-center gap-3">
                            {/* Circular gauge ~75% */}
                            <div className="w-6 h-6 rounded-full border-2 border-slate-200 border-t-emerald-500 border-r-emerald-500 border-b-emerald-500 rotate-45 shrink-0" />
                            <h2 className="text-lg font-bold text-[#1D1C1D]">
                                Set up GRID for your team
                            </h2>
                        </div>
                    </div>
                </div>

                {/* Bottom Floating AI Feature Notification Banner */}
                {isAiBannerVisible && (
                    <div className="w-full bg-[#F8F8F8] border border-[#E2E8F0] text-[#1D1C1D] px-5 py-3 rounded-xl flex items-center justify-between text-xs mt-8 shadow-xs">
                        <div className="flex items-center gap-2 truncate pr-4">
                            <span className="text-amber-500 font-bold text-sm">✨</span>
                            <span className="truncate">
                                Huddles notes, Thread and channel summaries, and 1 more AI feature are turned on for GRID.
                            </span>
                            <button
                                type="button"
                                onClick={() => onToast && onToast('AI feature details')}
                                className="text-[#1264A3] hover:underline font-semibold shrink-0 ml-1 cursor-pointer"
                            >
                                Learn more
                            </button>
                        </div>

                        <button
                            type="button"
                            onClick={() => setIsAiBannerVisible(false)}
                            className="text-[#616061] hover:text-[#1D1C1D] p-1 shrink-0 cursor-pointer"
                            title="Dismiss"
                        >
                            ✕
                        </button>
                    </div>
                )}
            </div>
        </div>
    );
}
