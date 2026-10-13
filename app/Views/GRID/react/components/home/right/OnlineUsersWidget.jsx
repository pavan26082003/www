import React from 'react';

/**
 * OnlineUsersWidget Component
 * Displays live network presence telemetry, active connections, and pending invitations.
 */
export default function OnlineUsersWidget({ onlineData, onViewConnections }) {
  const activeCount = onlineData?.activeInNetwork ?? 3;
  const pendingCount = onlineData?.pendingConnections ?? 0;
  const liveUsers = onlineData?.activeAvatars || [
    { initials: 'AK', bg: 'bg-slate-900', text: 'text-white' },
    { initials: 'SK', bg: 'bg-blue-600', text: 'text-white' },
    { initials: 'AT', bg: 'bg-emerald-700', text: 'text-white' }
  ];

  return (
    <div className="g-glass-card rounded-2xl border border-white/70 p-4 shadow-sm space-y-3">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-2">
          <div className="relative flex items-center justify-center">
            <span className="material-symbols-outlined text-[18px] text-slate-700">hub</span>
          </div>
          <h3 className="font-display text-xs font-bold uppercase tracking-wider text-slate-600">
            eFactor &amp; Grid Users Online
          </h3>
        </div>
        <span className="px-1.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-[10px] font-mono font-semibold">
          LIVE
        </span>
      </div>

      <div className="p-3 rounded-xl bg-slate-50/60 border border-slate-200/70 space-y-2.5">
        <div className="flex items-center justify-between text-xs">
          <span className="font-medium text-slate-700 flex items-center gap-1.5">
            <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
            {activeCount} active in your network
          </span>
          <div className="flex -space-x-1.5 overflow-hidden">
            {liveUsers.map((u, idx) => (
              <div
                key={idx}
                className={`inline-block h-6 w-6 rounded-full ring-2 ring-white ${u.bg} ${u.text} font-bold font-mono text-[9px] flex items-center justify-center`}
              >
                {u.initials}
              </div>
            ))}
          </div>
        </div>

        <div className="flex items-center justify-between text-[11px] text-slate-500 pt-2 border-t border-slate-200/60">
          <span>Total Pending Connections</span>
          <span className="font-mono font-semibold text-slate-700 bg-white px-2 py-0.5 rounded border border-slate-200/80">
            {pendingCount} Pending
          </span>
        </div>
      </div>

      <button
        onClick={() => onViewConnections ? onViewConnections() : (window.location.href = '/index.php/grid/network')}
        className="w-full py-1.5 px-3 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
        type="button"
      >
        <span>View All Network Connections</span>
        <span className="material-symbols-outlined text-[14px] text-slate-400">arrow_forward</span>
      </button>
    </div>
  );
}
