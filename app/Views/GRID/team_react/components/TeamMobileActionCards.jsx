import React from 'react';

/**
 * TeamMobileActionCards Component
 * Horizontal quick access card carousel at the top of the mobile Team hub.
 * Matches user's Slack reference screenshot:
 * - Later (0 items)
 * - Drafts & Sent (0 drafts)
 * - Huddles (0 live)
 */
export default function TeamMobileActionCards({ onToast }) {
    const cards = [
        {
            id: 'later',
            title: 'Later',
            count: '0 items',
            icon: (
                <svg className="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
            )
        },
        {
            id: 'drafts',
            title: 'Drafts & Sent',
            count: '0 drafts',
            icon: (
                <svg className="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            )
        },
        {
            id: 'huddles',
            title: 'Huddles',
            count: '0 live',
            icon: (
                <svg className="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" />
                </svg>
            )
        }
    ];

    return (
        <div className="px-3.5 pt-3 pb-2">
            <div className="flex items-center gap-2.5 overflow-x-auto scrollbar-none pb-1">
                {cards.map((card) => (
                    <button
                        key={card.id}
                        type="button"
                        onClick={() => onToast && onToast(`Opening ${card.title}`)}
                        className="flex-1 min-w-[105px] bg-white hover:bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-3 text-left transition-colors shrink-0 cursor-pointer shadow-2xs group"
                    >
                        <div className="mb-2 text-[#475569] group-hover:text-[#0F172A] group-hover:scale-105 transition-transform">
                            {card.icon}
                        </div>
                        <h4 className="text-xs font-bold text-slate-900 tracking-tight truncate">
                            {card.title}
                        </h4>
                        <p className="text-[11px] text-slate-500 mt-0.5">
                            {card.count}
                        </p>
                    </button>
                ))}

                {/* Settings / More quick shortcut */}
                <button
                    type="button"
                    onClick={() => onToast && onToast('Workspace Preferences')}
                    className="w-12 bg-white hover:bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-3 flex items-center justify-center text-slate-500 hover:text-slate-900 transition-colors shrink-0 cursor-pointer shadow-2xs"
                    title="Workspace Settings"
                >
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
        </div>
    );
}
