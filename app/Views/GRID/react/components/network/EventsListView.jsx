import React, { useState } from 'react';

/**
 * EventsListView Component
 * Renders the Events management page with Upcoming and Past tabs.
 */
export default function EventsListView({ events = [], onBack, onToast }) {
    const [activeTab, setActiveTab] = useState('upcoming');
    const [attendingMap, setAttendingMap] = useState({});

    const handleToggleAttend = (event) => {
        const nextState = !attendingMap[event.id];
        setAttendingMap(prev => ({ ...prev, [event.id]: nextState }));
        onToast && onToast(nextState ? `RSVP confirmed for ${event.title}! 🎟️` : `RSVP cancelled for ${event.title}`);
    };

    return (
        <section aria-label="Events Hub" className="w-full space-y-3">
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

            {/* Main Events Container */}
            <div className="g-glass-card rounded-none sm:rounded-2xl border-x-0 sm:border-x border-y sm:border border-white/70 dark:border-slate-800/80 shadow-sm overflow-hidden">
                <div className="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h1 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                            Your Events ({events.length})
                        </h1>
                        <p className="text-xs text-slate-500 dark:text-slate-400">
                            Webinars, tech mixers, and summits in your engineering circle
                        </p>
                    </div>
                    <div className="flex items-center gap-2 text-xs">
                        <button
                            type="button"
                            onClick={() => setActiveTab('upcoming')}
                            className={`px-3 py-1.5 rounded-lg font-semibold transition-colors cursor-pointer ${
                                activeTab === 'upcoming'
                                    ? 'bg-blue-600 text-white'
                                    : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                            }`}
                        >
                            Upcoming
                        </button>
                        <button
                            type="button"
                            onClick={() => setActiveTab('past')}
                            className={`px-3 py-1.5 rounded-lg font-semibold transition-colors cursor-pointer ${
                                activeTab === 'past'
                                    ? 'bg-blue-600 text-white'
                                    : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                            }`}
                        >
                            Past
                        </button>
                    </div>
                </div>

                <div className="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    {events.map((ev) => {
                        const isAttending = attendingMap[ev.id];

                        return (
                            <div
                                key={ev.id}
                                className="bg-slate-50/60 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/50 rounded-xl overflow-hidden flex flex-col justify-between hover:border-blue-400/50 dark:hover:border-blue-600/50 transition-all shadow-2xs"
                            >
                                <div className={`h-16 bg-gradient-to-r ${ev.coverGradient} p-3 flex items-start justify-between text-white`}>
                                    <span className="text-[10px] font-bold uppercase tracking-wider bg-black/30 backdrop-blur-xs px-2 py-0.5 rounded">
                                        {ev.type}
                                    </span>
                                </div>

                                <div className="p-4 space-y-2 flex-1">
                                    <p className="text-xs font-bold text-blue-600 dark:text-blue-400">
                                        {ev.date}
                                    </p>
                                    <h2 className="text-sm font-bold text-slate-900 dark:text-white leading-snug">
                                        {ev.title}
                                    </h2>
                                    <p className="text-xs text-slate-500 dark:text-slate-400">
                                        Organized by <strong className="text-slate-700 dark:text-slate-300">{ev.organizer}</strong> • {ev.attendees}
                                    </p>
                                </div>

                                <div className="p-4 pt-0 flex items-center justify-between gap-3">
                                    <button
                                        type="button"
                                        onClick={() => handleToggleAttend(ev)}
                                        className={`w-full py-1.5 px-3 rounded-full text-xs font-bold transition-all cursor-pointer ${
                                            isAttending
                                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700'
                                                : 'bg-blue-600 hover:bg-blue-700 text-white shadow-2xs'
                                        }`}
                                    >
                                        {isAttending ? '✓ Attending' : 'Attend Event'}
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
