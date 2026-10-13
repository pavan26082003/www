import React, { useRef } from 'react';

/**
 * CompanyInterestCarousel Component
 * Division 4: "Show your interest in these companies" matching user screenshot.
 */
export default function CompanyInterestCarousel({
    companies = [],
    onToggleInterest,
    onToast
}) {
    const scrollContainerRef = useRef(null);

    const scrollLeft = () => {
        if (scrollContainerRef.current) {
            scrollContainerRef.current.scrollBy({ left: -280, behavior: 'smooth' });
        }
    };

    const scrollRight = () => {
        if (scrollContainerRef.current) {
            scrollContainerRef.current.scrollBy({ left: 280, behavior: 'smooth' });
        }
    };

    if (!companies || companies.length === 0) return null;

    return (
        <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm overflow-hidden w-full max-w-full min-w-0">
            {/* Header with Title & Navigation Controls */}
            <div className="p-4 border-b border-slate-200/60 dark:border-slate-800/80 flex items-start justify-between gap-3">
                <div className="min-w-0 flex-1">
                    <h3 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate">
                        Show your interest in these companies
                    </h3>
                    <div className="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        <span className="truncate">Privately share your profile with recruiters</span>
                        <span>•</span>
                        <span className="font-semibold shrink-0">Promoted</span>
                        <span className="cursor-pointer shrink-0" title="Recruiters will see your interest">ℹ️</span>
                    </div>
                </div>

                {/* Left / Right Carousel Controls */}
                <div className="flex items-center gap-1 shrink-0">
                    <button
                        type="button"
                        onClick={scrollLeft}
                        className="w-7 h-7 rounded-full border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                        title="Previous companies"
                    >
                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        onClick={scrollRight}
                        className="w-7 h-7 rounded-full border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                        title="Next companies"
                    >
                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            {/* Horizontal Scrollable Carousel */}
            <div
                ref={scrollContainerRef}
                className="flex items-stretch gap-3 p-3.5 sm:p-4 overflow-x-auto scrollbar-none overscroll-x-contain w-full max-w-full"
                style={{ scrollSnapType: 'x mandatory' }}
            >
                {companies.map((comp) => {
                    const isInterested = comp.interested;

                    return (
                        <div
                            key={comp.id}
                            className="w-[190px] sm:w-[210px] bg-white/70 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/60 rounded-xl overflow-hidden flex flex-col justify-between shadow-2xs hover:shadow-sm transition-all group shrink-0"
                            style={{ scrollSnapAlign: 'start' }}
                        >
                            {/* Top Banner with Logo Badge */}
                            <div>
                                <div className="h-20 w-full relative bg-slate-200 dark:bg-slate-800 overflow-hidden">
                                    <img
                                        src={comp.banner}
                                        alt={comp.name}
                                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    />
                                    {/* Three-dot Action */}
                                    <button
                                        type="button"
                                        className="absolute top-2 right-2 w-6 h-6 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center text-xs backdrop-blur-xs transition-colors"
                                        title="More actions"
                                    >
                                        •••
                                    </button>
                                </div>

                                {/* Company Avatar / Logo (Floats over cover banner) */}
                                <div className="relative z-10 px-3.5 -mt-7 flex items-end">
                                    <img
                                        src={comp.logo}
                                        alt={comp.name}
                                        onError={(e) => {
                                            e.target.onerror = null;
                                            e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(comp.name)}&background=0284c7&color=fff&size=128&bold=true`;
                                        }}
                                        className="w-14 h-14 rounded-xl object-cover border-2 border-white dark:border-slate-900 shadow-md bg-white shrink-0"
                                    />
                                </div>

                                {/* Body Content */}
                                <div className="p-3.5 pt-2">
                                    <h4 className="font-extrabold text-sm text-slate-900 dark:text-white truncate">
                                        {comp.name}
                                    </h4>
                                    <p className="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">
                                        {comp.size}
                                    </p>

                                    {/* Actively Hiring Tag */}
                                    <div className="flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 mt-2">
                                        <svg className="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                        </svg>
                                        <span>{comp.hiringInfo}</span>
                                    </div>
                                </div>
                            </div>

                            {/* Bottom Action: "I'm interested" Pill Button */}
                            <div className="p-3.5 pt-0">
                                <button
                                    type="button"
                                    onClick={() => {
                                        onToggleInterest && onToggleInterest(comp.id);
                                        onToast && onToast(isInterested ? `Withdrew interest from ${comp.name}` : `Shared your interest with ${comp.name} recruiters! 🎉`);
                                    }}
                                    className={`w-full py-1.5 px-3 rounded-full text-xs font-bold transition-all border cursor-pointer ${
                                        isInterested
                                            ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500 text-emerald-600 dark:text-emerald-400'
                                            : 'border-blue-600 dark:border-blue-400 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30'
                                    }`}
                                >
                                    {isInterested ? 'Interest shared ✓' : "I'm interested"}
                                </button>
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
