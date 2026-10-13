import React from 'react';

/**
 * NetworkStatsRail Component
 * Left structural sidebar for the Network module:
 * - Telemetry & standing (eFactor score, total connections, active presence)
 * - My visiting card quick launcher
 * - Subscribed community spaces list
 */
export default function NetworkStatsRail({
    stats = {},
    user = {},
    onFilterSelect,
    activeFilter = 'all',
    onOpenVisitingCard,
    onOpenScanQr
}) {
    const handleMyCardClick = (e) => {
        e.preventDefault();
        if (onOpenVisitingCard) {
            onOpenVisitingCard();
        } else {
            window.dispatchEvent(new CustomEvent('grid:open-visiting-card'));
        }
    };

    const handleScanQrClick = (e) => {
        e.preventDefault();
        if (onOpenScanQr) {
            onOpenScanQr();
        } else {
            window.dispatchEvent(new CustomEvent('grid:open-scan-qr'));
        }
    };

    return (
        <aside className="space-y-4">
            {/* 1. Network Telemetry Card */}
            <div className="bg-white/90 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-5 shadow-xs">
                <div className="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h2 className="font-extrabold text-sm text-slate-900 dark:text-white tracking-tight">
                        Network Overview
                    </h2>
                    <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60">
                        ● Live Graph
                    </span>
                </div>

                <div className="pt-3 space-y-3">
                    {/* Stat Item: Connections */}
                    <div className="flex items-center justify-between text-xs">
                        <span className="text-slate-600 dark:text-slate-400 font-medium">Total Connections</span>
                        <span className="font-black text-slate-900 dark:text-white text-sm">{stats.totalConnections || 842}</span>
                    </div>

                    {/* Stat Item: Active Now */}
                    <div className="flex items-center justify-between text-xs">
                        <span className="text-slate-600 dark:text-slate-400 font-medium flex items-center gap-1.5">
                            <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                            Active in Network
                        </span>
                        <span className="font-bold text-emerald-600 dark:text-emerald-400">{stats.activeInNetwork || 3} online</span>
                    </div>

                    {/* Stat Item: eFactor Standing */}
                    <div className="flex items-center justify-between text-xs">
                        <span className="text-slate-600 dark:text-slate-400 font-medium">eFactor Trust Index</span>
                        <span className="font-extrabold text-blue-600 dark:text-blue-400">{stats.eFactorScore || 94}/100</span>
                    </div>

                    {/* Stat Item: Verified Level */}
                    <div className="flex items-center justify-between text-xs">
                        <span className="text-slate-600 dark:text-slate-400 font-medium">Peer Standing</span>
                        <span className="font-bold text-slate-800 dark:text-slate-200">{stats.verificationLevel || 'Level 2'}</span>
                    </div>
                </div>

                {/* Growth indicator */}
                <div className="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <svg className="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span>{stats.graphGrowthPercent || '+12% peer expansion this month'}</span>
                </div>
            </div>

            {/* 2. My Visiting Card & QR Quick Actions */}
            <div className="bg-gradient-to-br from-slate-900 to-blue-950 text-white rounded-2xl p-5 shadow-sm space-y-3">
                <div className="flex items-center gap-2.5">
                    <span className="text-2xl">📇</span>
                    <div>
                        <div className="font-extrabold text-sm leading-tight">My Visiting Card</div>
                        <div className="text-[11px] text-slate-300">Level 2 Verified QR Matrix</div>
                    </div>
                </div>

                <p className="text-xs text-slate-300 leading-relaxed">
                    Share your verified contact matrix or scan a peer's card directly into your network graph.
                </p>

                <div className="grid grid-cols-2 gap-2 pt-1">
                    <button
                        type="button"
                        onClick={handleMyCardClick}
                        className="w-full py-2 px-3 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all border border-white/15 text-center cursor-pointer active:scale-95"
                    >
                        View Card
                    </button>
                    <button
                        type="button"
                        onClick={handleScanQrClick}
                        className="w-full py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all text-center cursor-pointer shadow-sm active:scale-95 flex items-center justify-center gap-1"
                    >
                        <span>📷</span> Scan
                    </button>
                </div>
            </div>

            {/* 3. Subscribed Community Spaces */}
            <div className="bg-white/90 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 shadow-xs">
                <h3 className="font-extrabold text-xs text-slate-900 dark:text-white uppercase tracking-wider mb-3">
                    Subscribed Spaces ({stats.spacesCount || 14})
                </h3>

                <div className="space-y-2 text-xs">
                    {[
                        { name: 'AI Post-Training & Alignment', members: '1,420 peers', tag: 'ML' },
                        { name: 'Design Systems Guild', members: '890 peers', tag: 'UI' },
                        { name: 'Next.js & React Core', members: '2,100 peers', tag: 'JS' },
                        { name: 'Silicon Valley Founders', members: '340 peers', tag: 'VC' }
                    ].map((space, idx) => (
                        <div
                            key={idx}
                            className="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors cursor-pointer group"
                        >
                            <div className="min-w-0 flex-1 pr-2">
                                <div className="font-bold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 truncate">
                                    {space.name}
                                </div>
                                <div className="text-[11px] text-slate-400">{space.members}</div>
                            </div>
                            <span className="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                {space.tag}
                            </span>
                        </div>
                    ))}
                </div>
            </div>
        </aside>
    );
}
