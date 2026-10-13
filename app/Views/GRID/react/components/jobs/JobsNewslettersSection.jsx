import React, { useState } from 'react';

/**
 * JobsNewslettersSection Component
 * Engineering Newsletters & Design Systems from Stitch specification.
 */
export default function JobsNewslettersSection({ onToast }) {
    const [subscribed, setSubscribed] = useState([]);

    const toggleSubscribe = (id, name) => {
        setSubscribed((prev) =>
            prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id]
        );
        const isSub = !subscribed.includes(id);
        onToast && onToast(isSub ? `Subscribed to ${name}! 📬` : `Unsubscribed from ${name}.`);
    };

    return (
        <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm p-4 sm:p-5">
            <div className="flex items-center justify-between mb-3.5">
                <div>
                    <h2 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                        Engineering Newsletters &amp; Design Systems
                    </h2>
                    <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Curated digests authored by leaders in your extended network
                    </p>
                </div>
                <button
                    type="button"
                    onClick={() => onToast && onToast('Exploring all engineering publications')}
                    className="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline cursor-pointer shrink-0"
                >
                    Explore all
                </button>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                {/* Digest Card 1 */}
                <div className="border border-slate-200/70 dark:border-slate-800 rounded-xl p-3.5 bg-slate-50/50 dark:bg-slate-800/30 flex flex-col justify-between">
                    <div>
                        <div className="flex items-start gap-3">
                            <div className="w-10 h-10 rounded-lg bg-black text-white flex items-center justify-center font-extrabold text-xs shrink-0 shadow-xs">
                                #AI
                            </div>
                            <div>
                                <div className="flex items-center gap-1.5 flex-wrap">
                                    <h3 className="text-xs font-bold text-slate-900 dark:text-white">
                                        Canvas Nodes &amp; Shaders
                                    </h3>
                                    <span className="text-[9px] bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 font-bold px-1.5 py-0.5 rounded">
                                        Weekly
                                    </span>
                                </div>
                                <p className="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    By Elena Rostova (Staff Designer at Linear)
                                </p>
                            </div>
                        </div>
                        <p className="text-xs text-slate-600 dark:text-slate-300 mt-2 line-clamp-2 leading-relaxed">
                            Deep dive into 60fps WebGL shaders and GPU-accelerated canvas components.
                        </p>
                    </div>

                    <div className="flex items-center justify-between mt-3 pt-2.5 border-t border-slate-200/60 dark:border-slate-800">
                        <span className="text-[10px] text-slate-400">14,200 subscribers</span>
                        <button
                            type="button"
                            onClick={() => toggleSubscribe('nl-1', 'Canvas Nodes & Shaders')}
                            className={`text-xs font-bold cursor-pointer transition-colors ${
                                subscribed.includes('nl-1')
                                    ? 'text-emerald-600 dark:text-emerald-400'
                                    : 'text-blue-600 dark:text-blue-400 hover:text-blue-700'
                            }`}
                        >
                            {subscribed.includes('nl-1') ? 'Subscribed ✓' : 'Subscribe'}
                        </button>
                    </div>
                </div>

                {/* Digest Card 2 */}
                <div className="border border-slate-200/70 dark:border-slate-800 rounded-xl p-3.5 bg-slate-50/50 dark:bg-slate-800/30 flex flex-col justify-between">
                    <div>
                        <div className="flex items-start gap-3">
                            <div className="w-10 h-10 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-extrabold text-sm shrink-0 shadow-xs">
                                ⚡
                            </div>
                            <div>
                                <div className="flex items-center gap-1.5 flex-wrap">
                                    <h3 className="text-xs font-bold text-slate-900 dark:text-white">
                                        Design Tokens &amp; Systems
                                    </h3>
                                    <span className="text-[9px] bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 font-bold px-1.5 py-0.5 rounded">
                                        Bi-weekly
                                    </span>
                                </div>
                                <p className="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    By Sarah Chen (Principal Brand Architect)
                                </p>
                            </div>
                        </div>
                        <p className="text-xs text-slate-600 dark:text-slate-300 mt-2 line-clamp-2 leading-relaxed">
                            Bridging Figma tokens with deterministic Tailwind &amp; React production systems.
                        </p>
                    </div>

                    <div className="flex items-center justify-between mt-3 pt-2.5 border-t border-slate-200/60 dark:border-slate-800">
                        <span className="text-[10px] text-slate-400">8,830 subscribers</span>
                        <button
                            type="button"
                            onClick={() => toggleSubscribe('nl-2', 'Design Tokens & Systems')}
                            className={`text-xs font-bold cursor-pointer transition-colors ${
                                subscribed.includes('nl-2')
                                    ? 'text-emerald-600 dark:text-emerald-400'
                                    : 'text-blue-600 dark:text-blue-400 hover:text-blue-700'
                            }`}
                        >
                            {subscribed.includes('nl-2') ? 'Subscribed ✓' : 'Subscribe'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
