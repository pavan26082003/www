import React, { useState } from 'react';

/**
 * PeopleYouMayKnow Component
 * Right rail recommendations for expanding the verified network graph.
 */
export default function PeopleYouMayKnow({
    recommendations = [],
    onConnect,
    onDismiss,
    onToast
}) {
    const [connectedList, setConnectedList] = useState([]);
    const [dismissedList, setDismissedList] = useState([]);

    const handleConnect = (rec) => {
        setConnectedList((prev) => [...prev, rec.id]);
        if (onConnect) {
            onConnect(rec);
        } else {
            onToast && onToast(`Connection invitation sent to ${rec.name}! 🚀`);
        }
    };

    const handleDismiss = (id) => {
        setDismissedList((prev) => [...prev, id]);
        if (onDismiss) onDismiss(id);
    };

    const visibleRecs = recommendations.filter(
        (rec) => !dismissedList.includes(rec.id)
    );

    if (visibleRecs.length === 0) {
        return null;
    }

    return (
        <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 p-4 sm:p-5 shadow-sm space-y-4">
            <div className="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 className="font-extrabold text-sm text-slate-900 dark:text-white tracking-tight">
                    People You May Know
                </h3>
                <span className="text-xs text-blue-600 font-bold hover:underline cursor-pointer">
                    See All
                </span>
            </div>

            <div className="space-y-4">
                {visibleRecs.map((rec) => {
                    const isConnected = connectedList.includes(rec.id);

                    return (
                        <div key={rec.id} className="flex items-start gap-3 relative group">
                            {/* Avatar */}
                            <img
                                src={rec.avatar}
                                alt={rec.name}
                                className="w-11 h-11 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shadow-2xs shrink-0"
                                loading="lazy"
                            />

                            {/* Info */}
                            <div className="min-w-0 flex-1">
                                <div className="flex items-center justify-between">
                                    <h4 className="font-extrabold text-xs text-slate-900 dark:text-white truncate">
                                        {rec.name}
                                    </h4>
                                    <button
                                        type="button"
                                        onClick={() => handleDismiss(rec.id)}
                                        className="text-slate-400 hover:text-slate-600 p-0.5 text-xs opacity-0 group-hover:opacity-100 transition-opacity"
                                        title="Dismiss recommendation"
                                    >
                                        ✕
                                    </button>
                                </div>

                                <p className="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5 leading-snug">
                                    {rec.headline}
                                </p>

                                <div className="text-[10px] text-blue-600 dark:text-blue-400 mt-1 font-medium truncate">
                                    ✦ {rec.reason}
                                </div>

                                <div className="mt-2.5">
                                    {isConnected ? (
                                        <button
                                            type="button"
                                            disabled
                                            className="w-full py-1.5 px-3 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200 flex items-center justify-center gap-1 cursor-default"
                                        >
                                            <span>✓</span> Invited
                                        </button>
                                    ) : (
                                        <button
                                            type="button"
                                            onClick={() => handleConnect(rec)}
                                            className="w-full py-1.5 px-3 rounded-lg bg-slate-900 hover:bg-blue-600 text-white text-xs font-bold transition-colors shadow-2xs flex items-center justify-center gap-1 cursor-pointer active:scale-95"
                                        >
                                            <span>+</span> Connect
                                        </button>
                                    )}
                                </div>
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
