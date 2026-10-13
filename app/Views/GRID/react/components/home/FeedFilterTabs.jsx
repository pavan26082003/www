import React from 'react';

/**
 * FeedFilterTabs Component
 * Switches between feed perspectives:
 * - All Updates
 * - Artifacts & Research
 * - Network Opportunities
 * - Marketplace
 */
export default function FeedFilterTabs({ activeTab = 'all', onSelectTab }) {
  const tabs = [
    { id: 'all', label: 'All Updates' },
    { id: 'artifacts', label: 'Artifacts & Research' },
    { id: 'opportunities', label: 'Network Opportunities' },
    { id: 'marketplace', label: 'Marketplace' }
  ];

  return (
    <div className="flex items-center justify-between px-3 sm:px-1 my-2 sm:my-3">
      <div className="flex items-center gap-2.5 sm:gap-3 overflow-x-auto py-1 scrollbar-none">
        {tabs.map((tab) => {
          const isActive = activeTab === tab.id;
          return (
            <button
              key={tab.id}
              onClick={() => onSelectTab && onSelectTab(tab.id)}
              className={`px-4 py-2 rounded-full font-medium text-xs whitespace-nowrap transition-all cursor-pointer ${
                isActive
                  ? 'bg-slate-900 text-white shadow-sm'
                  : 'bg-white/80 backdrop-blur-md hover:bg-white text-slate-600 hover:text-slate-900 border border-slate-200/80 hover:border-slate-300 shadow-2xs'
              }`}
              type="button"
            >
              {tab.label}
            </button>
          );
        })}
      </div>

      <div className="hidden sm:flex items-center gap-1 text-[11px] text-slate-500">
        <span>Sort by:</span>
        <span className="font-semibold text-slate-700 cursor-pointer hover:text-blue-600 flex items-center">
          Recent
          <span className="material-symbols-outlined text-[14px]">arrow_drop_down</span>
        </span>
      </div>
    </div>
  );
}
