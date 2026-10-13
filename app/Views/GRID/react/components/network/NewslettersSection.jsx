import React, { useState } from 'react';

/**
 * NewslettersSection Component
 * Engineering Newsletters & Design Systems digest cards matching Stitch screen.
 */
export default function NewslettersSection({ newsletters = [], onToast }) {
    const [subscribedMap, setSubscribedMap] = useState({});

    const handleSubscribeToggle = (item) => {
        const nextState = !subscribedMap[item.id];
        setSubscribedMap((prev) => ({ ...prev, [item.id]: nextState }));
        if (nextState) {
            onToast && onToast(`Subscribed to ${item.title}! 📬`);
        } else {
            onToast && onToast(`Unsubscribed from ${item.title}`);
        }
    };

    return (
        <section aria-label="Engineering Newsletters" className="g-glass-card rounded-none sm:rounded-2xl border-x-0 sm:border-x border-y sm:border border-white/70 dark:border-slate-800/80 p-4 sm:p-5 shadow-sm space-y-4">
            <div className="flex items-center justify-between">
                <h3 className="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">
                    Engineering Newsletters &amp; Design Systems
                </h3>
                <button
                    type="button"
                    onClick={() => onToast && onToast('Explore all engineering newsletters')}
                    className="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer"
                >
                    Explore all
                </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {newsletters.map((item) => {
                    const isSubscribed = !!subscribedMap[item.id];
                    const isAi = item.id === 'news-1';

                    return (
                        <div
                            key={item.id}
                            className="bg-slate-50/60 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/50 rounded-xl p-4 flex flex-col justify-between hover:border-blue-400/60 dark:hover:border-blue-500/60 transition-colors"
                        >
                            <div className="flex items-start gap-3">
                                {/* Icon Badge */}
                                <div
                                    className={`w-12 h-12 rounded-xl text-white flex items-center justify-center font-mono font-bold text-lg shrink-0 shadow-xs ${
                                        isAi
                                            ? 'bg-slate-900 dark:bg-slate-800'
                                            : 'bg-gradient-to-tr from-blue-600 to-indigo-600'
                                    }`}
                                >
                                    {item.iconText}
                                </div>

                                <div className="min-w-0 flex-1">
                                    <div className="flex items-center gap-2 flex-wrap">
                                        <h4 className="font-bold text-sm text-slate-900 dark:text-white truncate">
                                            {item.title}
                                        </h4>
                                        <span
                                            className={`text-[10px] font-semibold px-2 py-0.5 rounded ${
                                                isAi
                                                    ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300'
                                                    : 'bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300'
                                            }`}
                                        >
                                            {item.cadence}
                                        </span>
                                    </div>

                                    <p className="text-xs text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                        By <strong className="text-slate-800 dark:text-slate-200">{item.author}</strong> ({item.authorRole}). {item.description}
                                    </p>

                                    <p className="text-[11px] text-slate-400 dark:text-slate-500 mt-2">
                                        {item.stats}
                                    </p>
                                </div>
                            </div>

                            <div className="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span className="text-xs text-slate-500 dark:text-slate-400">
                                    {item.schedule}
                                </span>
                                <button
                                    type="button"
                                    onClick={() => handleSubscribeToggle(item)}
                                    className={`px-4 py-1 text-xs font-bold rounded-full transition-colors cursor-pointer ${
                                        isSubscribed
                                            ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800'
                                            : 'text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-950/40'
                                    }`}
                                >
                                    {isSubscribed ? 'Subscribed ✓' : 'Subscribe'}
                                </button>
                            </div>
                        </div>
                    );
                })}
            </div>
        </section>
    );
}
