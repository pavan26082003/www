import React from 'react';

/**
 * JobsTopChipsBar Component
 * Horizontal recommendation chips bar matching Stitch Jobs Portal design.
 */
export default function JobsTopChipsBar({
    selectedChip = 'all',
    onSelectChip,
    onOpenPostJob,
    onToggleRecruiterView
}) {
    const chips = [
        { id: 'all', label: 'All Recommendations' },
        { id: 'ai-llm', label: '🤖 AI & LLM Specialists' },
        { id: 'product-design', label: 'Product Designers' },
        { id: 'founders-execs', label: 'Founders & Executives' },
        { id: 'hyd-tech', label: 'Bengaluru / Hyderabad Tech' }
    ];

    return (
        <div className="g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm p-3">
            <div className="flex items-center justify-between gap-2 overflow-x-auto scrollbar-none pb-0.5 text-xs whitespace-nowrap">
                <div className="flex items-center gap-2">
                    {chips.map((chip) => {
                        const isActive = selectedChip === chip.id;
                        return (
                            <button
                                key={chip.id}
                                type="button"
                                onClick={() => onSelectChip && onSelectChip(chip.id)}
                                className={`px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 cursor-pointer ${
                                    isActive
                                        ? 'bg-black text-white dark:bg-white dark:text-black shadow-xs'
                                        : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200/60 dark:border-slate-700/60'
                                }`}
                            >
                                {chip.label}
                            </button>
                        );
                    })}
                </div>

                {onOpenPostJob && (
                    <div className="flex items-center shrink-0 pl-2">
                        <button
                            type="button"
                            onClick={onOpenPostJob}
                            className="px-3 py-1.5 rounded-full font-bold text-xs bg-slate-900 hover:bg-black dark:bg-white dark:hover:bg-slate-100 text-white dark:text-black shadow-xs transition-all cursor-pointer flex items-center gap-1"
                        >
                            <span>+</span>
                            <span>Post a free job</span>
                        </button>
                    </div>
                )}
            </div>
        </div>
    );
}
